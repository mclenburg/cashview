<?php
/**
 * CashView - Finanzübersicht
 * Refactored für Clean Code und Stabilität
 */

// ============================================================================
// CONFIGURATION
// ============================================================================

define('DB_HOST', '192.168.5.103');
define('DB_USER', 'cashview');
define('DB_PASS', 'cash123');
define('DB_NAME', 'cashview');

date_default_timezone_set('Europe/Berlin');
error_reporting(E_ALL);
ini_set('display_errors', 0); // In Produktion auf 0 setzen

// ============================================================================
// DATABASE CONNECTION
// ============================================================================

function getDatabaseConnection() {
    static $connection = null;

    if ($connection === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $connection->set_charset('utf8mb4');
        } catch (Exception $e) {
            die('Datenbankverbindung fehlgeschlagen');
        }
    }

    return $connection;
}

// ============================================================================
// INPUT VALIDATION & SANITIZATION
// ============================================================================

function getMandantId() {
    $manId = $_POST['manId'] ?? $_GET['manId'] ?? null;

    if ($manId === null) {
        return null;
    }

    $manId = filter_var($manId, FILTER_VALIDATE_INT);
    return ($manId !== false && $manId > 0) ? $manId : null;
}

function validateAmount($amount) {
    if (empty($amount)) {
        return ['valid' => false, 'error' => 'Es fehlt der Betrag!'];
    }

    // Erlaube Komma als Dezimaltrennzeichen
    $amount = str_replace(',', '.', $amount);

    // Prüfe auf mehrere Dezimalpunkte
    if (substr_count($amount, '.') > 1) {
        return ['valid' => false, 'error' => 'Kein gültiger Betrag!'];
    }

    // Prüfe ob numerisch
    if (!is_numeric($amount)) {
        return ['valid' => false, 'error' => 'Kein gültiger Betrag!'];
    }

    $value = floatval($amount);

    if ($value <= 0) {
        return ['valid' => false, 'error' => 'Betrag muss größer als 0 sein!'];
    }

    return ['valid' => true, 'value' => $value];
}

function sanitizeOutput($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatCurrency($amount) {
    return number_format($amount, 2, ',', '.') . ' €';
}

// ============================================================================
// BUSINESS LOGIC - ACCOUNTS
// ============================================================================

function getAccountsWithBalances($conn, $mandantId) {
    // Hole alle Konten mit Initialwerten
    $sql = "SELECT i.Betrag, i.KtoID, k.Bez, k.Grenze
            FROM Initialwerte i
            INNER JOIN Konten k ON k.id = i.KtoID
            WHERE k.manId = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $mandantId);
    $stmt->execute();
    $result = $stmt->get_result();

    $accounts = [];

    while ($account = $result->fetch_assoc()) {
        $balance = calculateAccountBalance($conn, $account['KtoID'], $mandantId);

        $available = max(0, $balance);
        $withCredit = $balance - floatval($account['Grenze']);

        $accounts[] = [
            'id' => $account['KtoID'],
            'name' => $account['Bez'],
            'available' => $available,
            'with_credit' => $withCredit
        ];
    }

    return $accounts;
}

function calculateAccountBalance($conn, $accountId, $mandantId) {
    // Hole Initialwert
    $sql = "SELECT Betrag FROM Initialwerte WHERE KtoID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $initial = $result->fetch_assoc();

    $balance = $initial ? floatval($initial['Betrag']) : 0;

    // Subtrahiere Transaktionssumme
    $sql = "SELECT COALESCE(SUM(Wert), 0) as total
            FROM transaktionen
            WHERE KtoID = ? AND manId = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $accountId, $mandantId);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = $result->fetch_assoc();

    $balance -= floatval($transactions['total']);

    return $balance;
}

function calculateTotals($accounts) {
    $totalAvailable = 0;
    $totalWithCredit = 0;

    foreach ($accounts as $account) {
        $totalAvailable += $account['available'];
        $totalWithCredit += $account['with_credit'];
    }

    return [
        'available' => $totalAvailable,
        'with_credit' => $totalWithCredit
    ];
}

function calculateDailyBudget($totalAvailable, $totalWithCredit) {
    $remainingDays = date('t') - date('d') + 1;

    $daily = $remainingDays > 0 ? $totalAvailable / $remainingDays : 0;
    $dailyWithCredit = $remainingDays > 0 ? $totalWithCredit / $remainingDays : 0;

    return [
        'daily' => $daily,
        'daily_with_credit' => $dailyWithCredit,
        'show_credit' => abs($daily - $dailyWithCredit) > 0.01
    ];
}

// ============================================================================
// BUSINESS LOGIC - TRANSACTIONS
// ============================================================================

function createTransaction($conn, $amount, $accountId, $categoryId, $mandantId) {
    // Validiere IDs
    if (!is_numeric($accountId) || $accountId <= 0) {
        throw new Exception('Ungültige Konto-ID');
    }

    if (!is_numeric($categoryId) || $categoryId <= 0) {
        throw new Exception('Ungültige Kategorie-ID');
    }

    // Prüfe ob Konto zum Mandanten gehört
    $sql = "SELECT id FROM Konten WHERE id = ? AND manId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $accountId, $mandantId);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        throw new Exception('Konto nicht gefunden');
    }

    // Prüfe ob Kategorie zugänglich ist (global oder eigene)
    $sql = "SELECT ID FROM kategorien WHERE ID = ? AND (manId = 0 OR manId = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $categoryId, $mandantId);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        throw new Exception('Kategorie nicht gefunden');
    }

    // Erstelle Transaktion
    $sql = "INSERT INTO transaktionen (Wert, Datum, KtoID, katID, manId)
            VALUES (?, NOW(), ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('diii', $amount, $accountId, $categoryId, $mandantId);

    return $stmt->execute();
}

function getCategories($conn, $mandantId) {
    $sql = "SELECT ID, Bez
            FROM kategorien
            WHERE (manId = 0 OR manId = ?) AND sortorder <> 999
            ORDER BY sortorder, Bez";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $mandantId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAccountsList($conn, $mandantId) {
    $sql = "SELECT id, Bez FROM Konten WHERE manId = ? ORDER BY Bez";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $mandantId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ============================================================================
// MAIN LOGIC
// ============================================================================

$errorMessage = '';
$successMessage = '';
$mandantId = getMandantId();

if ($mandantId === null) {
    $errorMessage = 'Mandanten-ID nicht übergeben oder ungültig';
} else {
    try {
        $conn = getDatabaseConnection();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['betrag'])) {
            $validation = validateAmount($_POST['betrag']);

            if (!$validation['valid']) {
                $errorMessage = $validation['error'];
            } else {
                $amount = $validation['value'];
                $accountId = $_POST['konto'] ?? null;
                $categoryId = $_POST['zweck'] ?? null;

                createTransaction($conn, $amount, $accountId, $categoryId, $mandantId);

                // Redirect to prevent form resubmission
                header("Location: index.php?manId={$mandantId}&success=1");
                exit;
            }
        }

        // Check for success message from redirect
        if (isset($_GET['success']) && $_GET['success'] == '1') {
            $successMessage = 'Transaktion erfolgreich gespeichert';
        }

        // Load data for display
        $accounts = getAccountsWithBalances($conn, $mandantId);
        $totals = calculateTotals($accounts);
        $dailyBudget = calculateDailyBudget($totals['available'], $totals['with_credit']);
        $categories = getCategories($conn, $mandantId);
        $accountsList = getAccountsList($conn, $mandantId);

    } catch (Exception $e) {
        $errorMessage = 'Ein Fehler ist aufgetreten: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CashView - Die Finanzübersicht</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="http://192.168.5.103/cashview/favicon.ico" rel="shortcut icon">
    <link rel="icon" href="http://192.168.5.103/cashview/favicon.ico" type="image/ico">
    <style>
        /* Mobile First Styles */
        body {
            font-size: 14px;
            padding: 0;
            margin: 0;
        }

        .container {
            padding-left: 10px;
            padding-right: 10px;
        }

        /* Navigation optimiert für Mobile */
        .navbar {
            padding: 0.5rem 1rem;
            flex-wrap: wrap;
        }

        .navbar-brand {
            font-size: 1.1rem;
            margin-right: auto;
        }

        .navbar .btn {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
            margin-left: 0.5rem;
        }

        /* Cards für Mobile */
        .card {
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card-header {
            padding: 0.75rem 1rem;
            background-color: #f8f9fa;
        }

        .card-title {
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }

        .card-subtitle {
            font-size: 0.85rem;
        }

        .card-body {
            padding: 1rem;
        }

        /* Tabelle responsive */
        .table-responsive {
            font-size: 0.9rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive table {
            width: 100%;
            min-width: 300px;
        }

        .table-responsive th,
        .table-responsive td {
            padding: 0.5rem;
            white-space: nowrap;
        }

        /* Formular Optimierungen */
        .form-control,
        .form-control-sm {
            font-size: 1rem;
        }

        select.form-control {
            font-size: 1rem;
        }

        .input-group-text {
            font-size: 0.9rem;
        }

        /* Submit Button */
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
        }

        /* Modal Optimierung */
        .modal-header {
            padding: 1rem;
        }

        .modal-title {
            font-size: 1.1rem;
        }

        /* Tankpreise Link */
        .tankpreise-link {
            font-size: 0.85rem;
        }

        /* iPhone 13 (390x844) */
        @media only screen and (min-width: 390px) and (max-width: 428px) {
            .container {
                max-width: 100%;
                padding-left: 12px;
                padding-right: 12px;
            }

            .navbar-brand {
                font-size: 1.15rem;
            }
        }

        /* iPad 10 (820x1180) */
        @media only screen and (min-width: 768px) and (max-width: 1024px) {
            body {
                font-size: 16px;
            }

            .container {
                max-width: 760px;
                padding-left: 20px;
                padding-right: 20px;
            }

            .card-title {
                font-size: 1.3rem;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .navbar .btn {
                font-size: 0.95rem;
                padding: 0.5rem 1rem;
            }

            .table-responsive {
                font-size: 1rem;
            }

            .btn-primary {
                width: auto;
                min-width: 200px;
            }
        }

        /* Desktop (1920x1080 und größer) */
        @media only screen and (min-width: 1025px) {
            body {
                font-size: 16px;
            }

            .container {
                max-width: 1140px;
                padding-left: 15px;
                padding-right: 15px;
            }

            .navbar-brand {
                font-size: 1.5rem;
            }

            .navbar .btn {
                font-size: 1rem;
                padding: 0.5rem 1.5rem;
            }

            .card-title {
                font-size: 1.5rem;
            }

            .table-responsive {
                font-size: 1rem;
            }

            .btn-primary {
                width: auto;
                min-width: 250px;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #121212;
                color: #e0e0e0;
            }

            .card {
                background-color: #1e1e1e;
                border-color: #2d2d2d;
            }

            .card-header {
                background-color: #2d2d2d;
                border-bottom-color: #3d3d3d;
            }

            .table {
                color: #e0e0e0;
            }

            .table-striped tbody tr:nth-of-type(odd) {
                background-color: rgba(255, 255, 255, 0.05);
            }

            .form-control, select.form-control {
                background-color: #2d2d2d;
                color: #e0e0e0;
                border-color: #3d3d3d;
            }

            .input-group-text {
                background-color: #2d2d2d;
                color: #e0e0e0;
                border-color: #3d3d3d;
            }
        }
    </style>
</head>
<body>
    <?php if ($mandantId === null): ?>
        <div class="container mt-5">
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Fehler</h4>
                <p><?php echo sanitizeOutput($errorMessage); ?></p>
            </div>
        </div>
    <?php else: ?>

        <!-- Error/Success Messages -->
        <?php if (!empty($errorMessage)): ?>
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo sanitizeOutput($errorMessage); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo sanitizeOutput($successMessage); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <div class="container">
            <!-- Navigation -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <span class="navbar-brand">CashView</span>
                <a class="btn btn-secondary d-inline-block"
                   href="stats.php?manId=<?php echo $mandantId; ?>"
                   role="button">Statistik</a>
                <a class="btn btn-secondary d-inline-block"
                   href="config.php?manId=<?php echo $mandantId; ?>"
                   role="button">Konfiguration</a>
            </nav>

            <!-- Account Overview -->
            <div class="card">
                <div class="card-header">
                    <h5 class="d-inline-block card-title">Aktueller Finanzstand</h5>
                    <a class="d-inline-block float-right tankpreise-link"
                       data-toggle="modal"
                       data-target="#tankpreise"
                       href="#">wo tanken?</a>
                    <h6 class="card-subtitle mb-2 text-muted">Verfügbare Beträge pro Konto</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Konto</th>
                                    <th scope="col">Verf. Betrag</th>
                                    <th scope="col">Dispo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($accounts as $account): ?>
                                    <tr>
                                        <td><?php echo sanitizeOutput($account['name']); ?></td>
                                        <td><?php echo formatCurrency($account['available']); ?></td>
                                        <td><?php echo formatCurrency($account['with_credit']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr style="border-top: 2px solid #dee2e6; font-weight: bold;">
                                    <td>Gesamt</td>
                                    <td><?php echo formatCurrency($totals['available']); ?></td>
                                    <td><?php echo formatCurrency($totals['with_credit']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Transaction Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Buchungseintrag</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php">
                        <input type="hidden" name="manId" value="<?php echo $mandantId; ?>">

                        <!-- Amount -->
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="betrag">Betrag</span>
                            </div>
                            <?php
                                $placeholder = number_format($dailyBudget['daily'], 2, ',', '');
                                if ($dailyBudget['show_credit']) {
                                    $placeholder .= ' (' . number_format($dailyBudget['daily_with_credit'], 2, ',', '') . ')';
                                }
                            ?>
                            <input name="betrag"
                                   type="text"
                                   class="form-control"
                                   placeholder="<?php echo $placeholder; ?>"
                                   aria-label="Betrag"
                                   aria-describedby="betrag"
                                   required>
                        </div>

                        <!-- Category -->
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="zweck">Zweck</span>
                            </div>
                            <select name="zweck"
                                    class="form-control"
                                    aria-label="Zweck"
                                    aria-describedby="zweck"
                                    required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['ID']; ?>">
                                        <?php echo sanitizeOutput($category['Bez']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Account -->
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="konto">Konto</span>
                            </div>
                            <select name="konto"
                                    class="form-control"
                                    aria-label="Konto"
                                    aria-describedby="konto"
                                    required>
                                <?php foreach ($accountsList as $account): ?>
                                    <option value="<?php echo $account['id']; ?>">
                                        <?php echo sanitizeOutput($account['Bez']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Submit -->
                        <div class="text-center">
                            <input type="submit" value="Speichern" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Fuel Prices Modal -->
            <div class="modal fade" id="tankpreise" tabindex="-1" role="dialog"
                 aria-labelledby="tankpreiseLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="tankpreiseLabel">Aktuelle Benzinpreise</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <script type="text/javascript" src="https://maps.benzinpreis.de/bpimg/show_bpimg.php?code=g8RNi7pdOPv3VIuToBqiOzLGsnUCXQoS50623U5Fo1jWKcK7xyGyzlz3LSjV8nu6BWQ4PsjVeQOEo3RyhdnK%2FhpVO4HULpvZ0B9VPHCavnIVjEmuQ52%2Br1of9az4Vtd3HthJdOzXA0Cu8fWaZNnEQJuXrfjBcbW75oJPPludkukN9hqcJNKLAe7JWczNWgQlFSQuSDBUeGp7MQBsXXRayQG6igUCrdWQCUTUSzbnY2tU6yZH0CilzNlzdFDE1radH2VsoTZfT4W6zCE%2FEwVUvw%3D%3D"></script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>