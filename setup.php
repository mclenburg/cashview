<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CashView - Setup</title>
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

        /* Navigation */
        .navbar {
            padding: 0.5rem 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .navbar-brand {
            font-size: 1.1rem;
            margin-right: auto;
        }

        /* Cards */
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

        /* Formular */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-control {
            font-size: 1rem;
        }

        .form-text {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        /* Buttons */
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .btn-success,
        .btn-danger {
            font-size: 0.9rem;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Mandanten-Liste */
        .mandant-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid #e9ecef;
            transition: all 0.2s;
        }

        .mandant-item:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
        }

        .mandant-info {
            flex: 1;
        }

        .mandant-id {
            font-weight: bold;
            color: #007bff;
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }

        .mandant-details {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .mandant-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* Konten-Liste */
        .konto-list {
            margin-top: 0.5rem;
            padding-left: 1rem;
        }

        .konto-item {
            font-size: 0.85rem;
            color: #495057;
            padding: 0.25rem 0;
        }

        /* Collapse Section */
        .collapse-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        /* iPad 10 */
        @media only screen and (min-width: 768px) and (max-width: 1024px) {
            body {
                font-size: 16px;
            }

            .container {
                max-width: 760px;
                padding-left: 20px;
                padding-right: 20px;
            }

            .navbar-brand {
                font-size: 1.3rem;
            }

            .card-title {
                font-size: 1.3rem;
            }

            .btn-primary {
                width: auto;
                min-width: 250px;
            }
        }

        /* Desktop */
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

            .card-title {
                font-size: 1.5rem;
            }

            .btn-primary {
                width: auto;
                min-width: 350px;
            }

            .mandant-actions {
                flex-wrap: nowrap;
            }
        }

        /* Touch-Optimierungen */
        @media (hover: none) and (pointer: coarse) {
            .btn {
                min-height: 44px;
            }

            .form-control {
                min-height: 44px;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #121212;
                color: #ffffff;
            }

            .card {
                background-color: #1e1e1e;
                border-color: #333;
            }

            .card-header {
                background-color: #2a2a2a;
                border-bottom-color: #333;
            }

            .card-title {
                color: #ffffff;
            }

            .card-subtitle {
                color: #aaaaaa !important;
            }

            .form-group label {
                color: #e0e0e0;
            }

            .form-control {
                background-color: #2a2a2a;
                color: #ffffff;
                border-color: #444;
            }

            .form-control:focus {
                background-color: #2a2a2a;
                border-color: #007bff;
            }

            .form-text {
                color: #aaaaaa;
            }

            .mandant-item {
                background-color: #2a2a2a;
                border-color: #444;
            }

            .mandant-item:hover {
                border-color: #007bff;
            }

            .mandant-id {
                color: #4d9eff;
            }

            .mandant-details {
                color: #aaaaaa;
            }

            .konto-item {
                color: #e0e0e0;
            }

            .collapse-section {
                border-top-color: #444;
            }

            .alert-success {
                background-color: #1a4d2e;
                color: #51cf66;
                border-color: #2d7a4a;
            }

            .alert-danger {
                background-color: #4a2020;
                color: #ff6b6b;
                border-color: #7a2d2d;
            }

            .alert-info {
                background-color: #1a3a52;
                color: #4dabf7;
                border-color: #2d5a7a;
            }
        }
    </style>
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

($GLOBALS["___mysqli_ston"] = mysqli_connect("192.168.5.103", "cashview", "cash123", "cashview"))
    or die("ERROR connecting to database.");

$success_message = "";
$error_message = "";

// Mandant hinzufügen
if(isset($_POST["action"]) && $_POST["action"] == "create_mandant") {
    $mandant_name = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST["mandant_name"]);
    $konten = $_POST["konten"];
    $initialbetraege = $_POST["initialbetraege"];
    $dispogrenzen = $_POST["dispogrenzen"];

    // Höchste Mandanten-ID ermitteln
    $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT MAX(manId) as maxid FROM Konten");
    $row = mysqli_fetch_assoc($result);
    $new_mandant_id = ($row["maxid"] ? $row["maxid"] : 0) + 1;

    // Konten anlegen
    $konto_count = 0;
    foreach($konten as $index => $konto_name) {
        if(!empty($konto_name)) {
            $konto_name_escaped = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $konto_name);
            $dispo = isset($dispogrenzen[$index]) ? floatval($dispogrenzen[$index]) : 0;

            // Höchste Konto-ID ermitteln
            $result_kto = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT MAX(id) as maxid FROM Konten");
            $row_kto = mysqli_fetch_assoc($result_kto);
            $new_konto_id = ($row_kto["maxid"] ? $row_kto["maxid"] : 0) + 1;

            // Konto anlegen
            $insert_konto = "INSERT INTO Konten (id, Bez, Grenze, manId)
                            VALUES ($new_konto_id, '$konto_name_escaped', '$dispo', $new_mandant_id)";
            mysqli_query($GLOBALS["___mysqli_ston"], $insert_konto)
                or die("ERROR Konto: ".mysqli_error($GLOBALS["___mysqli_ston"]));

            // Initialwert setzen
            $initialbetrag = isset($initialbetraege[$index]) ? floatval($initialbetraege[$index]) : 0;

            $result_init = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT MAX(initId) as maxid FROM Initialwerte");
            $row_init = mysqli_fetch_assoc($result_init);
            $new_init_id = ($row_init["maxid"] ? $row_init["maxid"] : 0) + 1;

            $insert_init = "INSERT INTO Initialwerte (initId, Betrag, KtoId)
                           VALUES ($new_init_id, $initialbetrag, $new_konto_id)";
            mysqli_query($GLOBALS["___mysqli_ston"], $insert_init)
                or die("ERROR Init: ".mysqli_error($GLOBALS["___mysqli_ston"]));

            $konto_count++;
        }
    }

    $success_message = "✅ Mandant #$new_mandant_id erfolgreich angelegt mit $konto_count Konto(en)!";
}

// Mandant löschen
if(isset($_POST["action"]) && $_POST["action"] == "delete_mandant") {
    $mandant_id = intval($_POST["mandant_id"]);

    // Prüfen ob Transaktionen existieren
    $check = "SELECT COUNT(*) as cnt FROM transaktionen WHERE manId = $mandant_id";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $check);
    $row = mysqli_fetch_assoc($result);

    if($row["cnt"] > 0) {
        $error_message = "❌ Mandant kann nicht gelöscht werden, da " . $row["cnt"] . " Transaktion(en) existieren!";
    } else {
        // Konten des Mandanten finden
        $konten_query = "SELECT id FROM Konten WHERE manId = $mandant_id";
        $konten_result = mysqli_query($GLOBALS["___mysqli_ston"], $konten_query);

        while($konto = mysqli_fetch_assoc($konten_result)) {
            // Initialwerte löschen
            mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM Initialwerte WHERE KtoId = " . $konto["id"]);
        }

        // Konten löschen
        mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM Konten WHERE manId = $mandant_id");

        // Mandantenspezifische Kategorien löschen
        mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM kategorien WHERE manId = $mandant_id");

        $success_message = "✅ Mandant #$mandant_id erfolgreich gelöscht!";
    }
}

// Alle Mandanten laden
$mandanten = array();
$query = "SELECT DISTINCT manId FROM Konten ORDER BY manId";
$result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
while($row = mysqli_fetch_assoc($result)) {
    $manId = $row["manId"];

    // Konten des Mandanten laden
    $konten_query = "SELECT k.id, k.Bez, k.Grenze, i.Betrag
                     FROM Konten k
                     LEFT JOIN Initialwerte i ON k.id = i.KtoId
                     WHERE k.manId = $manId";
    $konten_result = mysqli_query($GLOBALS["___mysqli_ston"], $konten_query);

    $konten_list = array();
    while($konto = mysqli_fetch_assoc($konten_result)) {
        $konten_list[] = $konto;
    }

    // Anzahl Transaktionen
    $trans_query = "SELECT COUNT(*) as cnt FROM transaktionen WHERE manId = $manId";
    $trans_result = mysqli_query($GLOBALS["___mysqli_ston"], $trans_query);
    $trans_count = mysqli_fetch_assoc($trans_result)["cnt"];

    $mandanten[] = array(
        'id' => $manId,
        'konten' => $konten_list,
        'transaktionen' => $trans_count
    );
}
?>

<div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <span class="navbar-brand">🚀 CashView Setup</span>
    </nav>

    <?php if($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Bestehende Mandanten -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">📋 Bestehende Mandanten</h5>
            <h6 class="card-subtitle">Übersicht aller angelegten Mandanten</h6>
        </div>
        <div class="card-body">
            <?php if(empty($mandanten)): ?>
                <div class="alert alert-info">
                    ℹ️ Noch keine Mandanten angelegt. Erstelle deinen ersten Mandanten unten!
                </div>
            <?php else: ?>
                <?php foreach($mandanten as $mandant): ?>
                    <div class="mandant-item">
                        <div class="mandant-info">
                            <div class="mandant-id">Mandant #<?php echo $mandant['id']; ?></div>
                            <div class="mandant-details">
                                <?php echo count($mandant['konten']); ?> Konto(en) •
                                <?php echo $mandant['transaktionen']; ?> Transaktion(en)
                            </div>
                            <div class="konto-list">
                                <?php foreach($mandant['konten'] as $konto): ?>
                                    <div class="konto-item">
                                        💳 <?php echo $konto['Bez']; ?>:
                                        <?php echo number_format($konto['Betrag'] ? $konto['Betrag'] : 0, 2, ',', '.'); ?> €
                                        (Dispo: <?php echo number_format($konto['Grenze'], 2, ',', '.'); ?> €)
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mandant-actions">
                            <a href="index.php?manId=<?php echo $mandant['id']; ?>" class="btn btn-success">Öffnen</a>
                            <?php if($mandant['transaktionen'] == 0): ?>
                                <button class="btn btn-danger" onclick="deleteMandant(<?php echo $mandant['id']; ?>)">Löschen</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Neuer Mandant -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">➕ Neuen Mandanten anlegen</h5>
            <h6 class="card-subtitle">Erstelle einen neuen Mandanten mit Konten</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="setup.php" id="createForm">
                <input type="hidden" name="action" value="create_mandant">

                <div class="form-group">
                    <label for="mandant_name">Mandantenbezeichnung (optional)</label>
                    <input type="text" class="form-control" name="mandant_name" id="mandant_name" placeholder="z.B. Max Mustermann">
                    <small class="form-text">Optional: Interne Bezeichnung für den Mandanten</small>
                </div>

                <div id="konten-container">
                    <div class="form-group">
                        <label>Konto 1</label>
                        <input type="text" class="form-control mb-2" name="konten[]" placeholder="Kontobezeichnung (z.B. Girokonto)" required>
                        <input type="number" step="0.01" class="form-control mb-2" name="initialbetraege[]" placeholder="Anfangsbestand in €" required>
                        <input type="number" step="0.01" class="form-control" name="dispogrenzen[]" placeholder="Dispogrenze in € (optional)" value="0">
                    </div>
                </div>

                <button type="button" class="btn btn-secondary mb-3" onclick="addKonto()">+ Weiteres Konto hinzufügen</button>

                <div class="collapse-section">
                    <div class="alert alert-info">
                        💡 <strong>Tipp:</strong> Nach dem Anlegen kannst du in der Konfiguration eigene Kategorien für diesen Mandanten erstellen!
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">🚀 Mandanten anlegen</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
let kontoCount = 1;

function addKonto() {
    kontoCount++;
    const container = document.getElementById('konten-container');
    const newKonto = document.createElement('div');
    newKonto.className = 'form-group';
    newKonto.innerHTML = `
        <label>Konto ${kontoCount}</label>
        <input type="text" class="form-control mb-2" name="konten[]" placeholder="Kontobezeichnung (z.B. Sparkonto)" required>
        <input type="number" step="0.01" class="form-control mb-2" name="initialbetraege[]" placeholder="Anfangsbestand in €" required>
        <input type="number" step="0.01" class="form-control" name="dispogrenzen[]" placeholder="Dispogrenze in € (optional)" value="0">
    `;
    container.appendChild(newKonto);
}

function deleteMandant(id) {
    if(confirm('Mandant #' + id + ' wirklich löschen?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'setup.php';

        const inputAction = document.createElement('input');
        inputAction.type = 'hidden';
        inputAction.name = 'action';
        inputAction.value = 'delete_mandant';
        form.appendChild(inputAction);

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'mandant_id';
        inputId.value = id;
        form.appendChild(inputId);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</body>
</html>