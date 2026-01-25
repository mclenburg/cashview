<?php
// ============================================================================
// CashView - Statistik Dashboard
// ============================================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============================================================================
// KONFIGURATION & INITIALISIERUNG
// ============================================================================

class DatabaseConfig {
    const HOST = '192.168.5.103';
    const USER = 'cashview';
    const PASSWORD = 'cash123';
    const DATABASE = 'cashview';
}

class DateHelper {
    public static function getDaysInMonth() {
        return date("t");
    }

    public static function getCurrentDay() {
        return date("d");
    }

    public static function getRemainingDays() {
        return self::getDaysInMonth() - self::getCurrentDay() + 1;
    }
}

// ============================================================================
// DATENBANKVERBINDUNG
// ============================================================================

function connectToDatabase() {
    $connection = mysqli_connect(
        DatabaseConfig::HOST,
        DatabaseConfig::USER,
        DatabaseConfig::PASSWORD,
        DatabaseConfig::DATABASE
    );

    if (!$connection) {
        die("ERROR: Datenbankverbindung fehlgeschlagen.");
    }

    return $connection;
}

// ============================================================================
// MANDANTEN-VALIDIERUNG
// ============================================================================

function getMandantId() {
    if (isset($_POST["manId"])) {
        return (int)$_POST["manId"];
    }

    if (isset($_GET["manId"])) {
        return (int)$_GET["manId"];
    }

    die("ERROR: Mandanten-ID nicht übergeben</body></html>");
}

// ============================================================================
// DATENBANKABFRAGEN
// ============================================================================

function calculateAvailableBalance($connection, $mandantId) {
    $query = "SELECT Betrag, KtoID
              FROM Initialwerte
              INNER JOIN Konten ON Konten.id = KtoID
              WHERE Konten.manId = ?";

    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $mandantId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $totalBalance = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $accountBalance = $row["Betrag"];
        $accountId = $row["KtoID"];

        // Transaktionen für dieses Konto abziehen
        $transQuery = "SELECT Wert FROM transaktionen WHERE KtoID = ? AND manId = ?";
        $transStmt = mysqli_prepare($connection, $transQuery);
        mysqli_stmt_bind_param($transStmt, "ii", $accountId, $mandantId);
        mysqli_stmt_execute($transStmt);
        $transResult = mysqli_stmt_get_result($transStmt);

        while ($transRow = mysqli_fetch_assoc($transResult)) {
            $accountBalance -= $transRow["Wert"];
        }

        if ($accountBalance > 0) {
            $totalBalance += $accountBalance;
        }
    }

    return $totalBalance;
}

function getInitialBalance($connection, $mandantId) {
    $query = "SELECT SUM(Betrag) as wert
              FROM Initialwerte
              INNER JOIN Konten ON Initialwerte.KtoId = Konten.id
              WHERE Konten.manId = ?";

    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $mandantId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result)["wert"];
}

function getTransactionsUntilDate($connection, $mandantId, $daysBack = 30) {
    $query = "SELECT SUM(wert) as wert
              FROM transaktionen
              WHERE DATE(Datum) <= DATE(DATE_SUB(CURRENT_DATE(), INTERVAL ? DAY))
              AND manId = ?";

    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ii", $daysBack, $mandantId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result)["wert"];
}

function getCategoryExpensesAll($connection, $mandantId) {
    $query = "SELECT SUM(trans.wert) as summe, kat.bez, kat.ID, kat.statscolor
              FROM transaktionen trans
              LEFT OUTER JOIN kategorien kat ON trans.katID = kat.ID
              WHERE wert > 0
              AND trans.manId = ?
              AND (kat.manId = 0 OR kat.manId = ?)
              GROUP BY katID
              ORDER BY sortorder";

    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ii", $mandantId, $mandantId);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function getCategoryExpensesLast30Days($connection, $mandantId) {
    $query = "SELECT SUM(trans.wert) as summe, kat.bez, kat.statscolor
              FROM transaktionen trans
              LEFT OUTER JOIN kategorien kat ON trans.katID = kat.ID
              WHERE wert > 0
              AND trans.manId = ?
              AND trans.Datum > DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
              AND (kat.manId = 0 OR kat.manId = ?)
              GROUP BY katID
              ORDER BY sortorder";

    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ii", $mandantId, $mandantId);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function getCategoryBreakdown30Days($connection, $mandantId) {
    $query = "SELECT SUM(t.wert) as wert, k.bez as kategorie
              FROM transaktionen t
              INNER JOIN kategorien k ON t.katID = k.ID
              WHERE DATE(t.Datum) >= DATE(DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY))
              AND k.bez != 'Gehalt'
              AND t.manId = ?
              AND (k.manId = 0 OR k.manId = ?)
              GROUP BY k.bez
              ORDER BY k.sortorder";

    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ii", $mandantId, $mandantId);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function getMonthlyComparison($connection, $mandantId) {
    $monthlyData = array();

    $currentYear = date('Y');
    $currentMonth = date('n');

    $monthNames = array(
        1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April',
        5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember'
    );

    for ($i = 0; $i < 3; $i++) {
        $targetMonth = $currentMonth - $i;
        $targetYear = $currentYear;

        // Jahreswechsel berücksichtigen
        while ($targetMonth < 1) {
            $targetMonth += 12;
            $targetYear--;
        }

        $firstDay = date('Y-m-01', mktime(0, 0, 0, $targetMonth, 1, $targetYear));
        $lastDay = date('Y-m-t', mktime(0, 0, 0, $targetMonth, 1, $targetYear));

        $periodName = $monthNames[$targetMonth] . " " . $targetYear;
        if ($i == 0) {
            $periodName .= " (aktuell)";
        }

        $query = "SELECT SUM(wert) as total
                  FROM transaktionen
                  WHERE manId = ?
                  AND wert > 0
                  AND DATE(Datum) >= ?
                  AND DATE(Datum) <= ?";

        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "iss", $mandantId, $firstDay, $lastDay);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        $monthlyData[] = array(
            'period' => $periodName,
            'total' => $row['total'] ? floatval($row['total']) : 0,
            'startDate' => $firstDay,
            'endDate' => $lastDay,
            'isCurrentMonth' => ($i == 0)
        );
    }

    return $monthlyData;
}

function getDailyExpenses($connection, $mandantId) {
    $query = "SELECT SUM(trans.wert) as summe, DATE(trans.Datum) as datum
              FROM transaktionen trans
              WHERE DATE(trans.Datum) > DATE(DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY))
              AND manId = ?
              GROUP BY DATE(Datum)
              ORDER BY Datum";

    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $mandantId);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

function getLastTransactions($connection, $mandantId, $limit = 3) {
    $query = "SELECT t.Wert, t.Datum, k.bez as Kategorie, ko.Bez as Konto
              FROM transaktionen t
              LEFT JOIN kategorien k ON t.katID = k.ID
              LEFT JOIN Konten ko ON t.KtoID = ko.id
              WHERE t.manId = ?
              ORDER BY t.Datum DESC
              LIMIT ?";

    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ii", $mandantId, $limit);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

// ============================================================================
// DATENVERARBEITUNG
// ============================================================================

function calculateBalanceHistory($dailyExpenses, $initialBalance) {
    $expensesByDate = array();

    while ($row = mysqli_fetch_assoc($dailyExpenses)) {
        $expensesByDate[$row["datum"]] = $row["summe"];
    }

    $balanceHistory = array();
    $dates = array();
    $currentBalance = $initialBalance;

    for ($day = 30; $day >= 0; $day--) {
        $date = new DateTime("-" . $day . " days");
        $dateStr = $date->format("Y-m-d");
        $dateLabel = $date->format("d.m.");

        if (isset($expensesByDate[$dateStr])) {
            $currentBalance -= $expensesByDate[$dateStr];
        }

        $dates[] = $dateLabel;
        $balanceHistory[] = round($currentBalance, 2);
    }

    return array(
        'dates' => $dates,
        'balances' => $balanceHistory
    );
}

function prepareChartData($result) {
    $values = array();
    $labels = array();
    $colors = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $values[] = floatval($row["summe"]);
        $labels[] = $row["bez"];

        $colorParts = explode(",", $row["statscolor"]);
        $colors[] = "rgba(" . $colorParts[0] . "," . $colorParts[1] . "," . $colorParts[2] . ", 0.8)";
    }

    return array(
        'values' => $values,
        'labels' => $labels,
        'colors' => $colors
    );
}

function calculateTrendAnalysis($monthlyData, $connection, $mandantId) {
    // Aktuellen Monat hochrechnen basierend auf bisherigen Ausgaben
    $currentMonthData = $monthlyData[0];
    $currentDay = (int)date('d'); // Aktueller Tag im Monat
    $daysInMonth = (int)date('t'); // Gesamtzahl Tage im Monat
    $remainingDays = $daysInMonth - $currentDay;

    // Durchschnittliche Tagesausgaben der letzten beiden Monate berechnen
    $lastMonth = $monthlyData[1];
    $monthBeforeLast = $monthlyData[2];

    // Tage im letzten und vorletzten Monat
    $daysLastMonth = (int)date('t', strtotime($lastMonth['startDate']));
    $daysMonthBeforeLast = (int)date('t', strtotime($monthBeforeLast['startDate']));

    // Durchschnittliche Tagesausgaben der letzten beiden Monate
    $avgDailyLastMonth = $daysLastMonth > 0 ? $lastMonth['total'] / $daysLastMonth : 0;
    $avgDailyMonthBeforeLast = $daysMonthBeforeLast > 0 ? $monthBeforeLast['total'] / $daysMonthBeforeLast : 0;
    $avgDailyExpenses = ($avgDailyLastMonth + $avgDailyMonthBeforeLast) / 2;

    // Hochrechnung für verbleibende Tage
    $projectedRemainingExpenses = $avgDailyExpenses * $remainingDays;
    $projectedCurrentMonth = $currentMonthData['total'] + $projectedRemainingExpenses;

    // Durchschnitt der letzten beiden Monate (ohne aktuellen)
    $average = ($lastMonth['total'] + $monthBeforeLast['total']) / 2;

    // Trend berechnen
    $trendPercent = $average > 0 ? (($projectedCurrentMonth - $average) / $average * 100) : 0;

    return array(
        'average' => $average,
        'current' => $currentMonthData['total'],
        'projected' => $projectedCurrentMonth,
        'percent' => $trendPercent,
        'remainingDays' => $remainingDays,
        'avgDailyExpenses' => $avgDailyExpenses
    );
}

// ============================================================================
// HTML-AUSGABE FUNKTIONEN
// ============================================================================

function renderTrendBar($period, $maxValue, $trendAnalysis) {
    $actualValue = $period['total'];
    $barClass = $period['isCurrentMonth'] ? 'current-month' : '';

    echo '<div class="trend-bar-wrapper">';
    echo '<div class="trend-month">';
    echo '<span>' . htmlspecialchars($period['period']) . '</span>';
    echo '<span class="trend-amount">' . number_format($actualValue, 2, ',', '.') . ' €</span>';
    echo '</div>';
    echo '<div class="trend-bar-container">';

    // Für den aktuellen Monat: Zwei Balken (transparent + solid)
    if ($period['isCurrentMonth'] && $trendAnalysis['remainingDays'] > 0) {
        $projectedValue = $trendAnalysis['projected'];
        $actualWidth = $maxValue > 0 ? ($actualValue / $maxValue * 100) : 0;
        $projectedWidth = $maxValue > 0 ? ($projectedValue / $maxValue * 100) : 0;

        // Transparenter Balken für Prognose (im Hintergrund)
        echo '<div class="trend-bar trend-bar-projected" style="width: ' . $projectedWidth . '%"></div>';
        // Solider Balken für tatsächliche Ausgaben (im Vordergrund)
        echo '<div class="trend-bar trend-bar-actual" style="width: ' . $actualWidth . '%"></div>';
    } else {
        // Normale Monate: Ein solider Balken
        $barWidth = $maxValue > 0 ? ($actualValue / $maxValue * 100) : 0;
        echo '<div class="trend-bar ' . $barClass . '" style="width: ' . $barWidth . '%"></div>';
    }

    echo '</div>';
    echo '</div>';
}

function renderTrendSummary($trendAnalysis) {
    $percent = $trendAnalysis['percent'];
    $average = $trendAnalysis['average'];
    $projected = $trendAnalysis['projected'];
    $current = $trendAnalysis['current'];
    $remainingDays = $trendAnalysis['remainingDays'];

    echo '<div class="trend-summary">';
    echo '<h6>Trend-Analyse</h6>';
    echo '<div>';

    // Hochrechnungs-Info anzeigen
    if ($remainingDays > 0) {
        echo '<div style="margin-bottom: 1rem; font-size: 0.9rem; color: #666;">';
        echo 'Bisher: <strong>' . number_format($current, 2, ',', '.') . ' €</strong> ';
        echo '| Hochrechnung Monatsende: <strong>' . number_format($projected, 2, ',', '.') . ' €</strong>';
        echo '<br><small>(noch ' . $remainingDays . ' Tage, basierend auf Ø-Tagesausgaben der letzten 2 Monate)</small>';
        echo '</div>';
    }

    echo '<div>Durchschnitt (letzte 2 Monate): <strong>' . number_format($average, 2, ',', '.') . ' €</strong></div>';

    if (abs($percent) < 5) {
        echo '<div class="trend-indicator trend-neutral">';
        echo '≈ ' . number_format(abs($percent), 1) . '% Stabil';
        echo '</div>';
        echo '<p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">Deine hochgerechneten Ausgaben sind stabil.</p>';
    } elseif ($percent > 0) {
        echo '<div class="trend-indicator trend-up">';
        echo '↑ +' . number_format($percent, 1) . '% Höher';
        echo '</div>';
        echo '<p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">Du wirst voraussichtlich mehr ausgeben als im Durchschnitt der letzten 2 Monate.</p>';
    } else {
        echo '<div class="trend-indicator trend-down">';
        echo '↓ ' . number_format($percent, 1) . '% Niedriger';
        echo '</div>';
        echo '<p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">Gut gemacht! Du wirst voraussichtlich weniger ausgeben als im Durchschnitt.</p>';
    }

    echo '</div>';
    echo '</div>';
}

function renderCategoryTable($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row["kategorie"]) . '</td>';
        echo '<td>' . number_format($row["wert"], 2, ',', '.') . ' €</td>';
        echo '</tr>';
    }
}

function renderTransactionTable($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $datum = date('d.m.Y H:i', strtotime($row["Datum"]));
        $betrag = number_format($row["Wert"], 2, ',', '.');
        $kategorie = $row["Kategorie"] ? htmlspecialchars($row["Kategorie"]) : '-';
        $konto = $row["Konto"] ? htmlspecialchars($row["Konto"]) : '-';

        echo '<tr>';
        echo '<td>' . $datum . '</td>';
        echo '<td>' . $betrag . ' €</td>';
        echo '<td>' . $kategorie . '</td>';
        echo '<td>' . $konto . '</td>';
        echo '</tr>';
    }
}

// ============================================================================
// HAUPTPROGRAMM
// ============================================================================

$mandantId = getMandantId();
$connection = connectToDatabase();

// Berechnungen durchführen
$remainingDays = DateHelper::getRemainingDays();
$availableBalance = calculateAvailableBalance($connection, $mandantId);
$dailyAverage = $remainingDays > 0 ? $availableBalance / $remainingDays : 0;

$initialBalance = getInitialBalance($connection, $mandantId);
$transactionsUntil30Days = getTransactionsUntilDate($connection, $mandantId, 30);
$balance30DaysAgo = $initialBalance - $transactionsUntil30Days;

// Daten für Charts abrufen
$categoryExpensesAll = getCategoryExpensesAll($connection, $mandantId);
$categoryExpenses30Days = getCategoryExpensesLast30Days($connection, $mandantId);
$categoryBreakdown = getCategoryBreakdown30Days($connection, $mandantId);
$monthlyComparison = getMonthlyComparison($connection, $mandantId);
$dailyExpenses = getDailyExpenses($connection, $mandantId);
$lastTransactions = getLastTransactions($connection, $mandantId, 3);

// Chart-Daten vorbereiten
$chartDataAll = prepareChartData($categoryExpensesAll);
$chartData30Days = prepareChartData($categoryExpenses30Days);
$balanceHistory = calculateBalanceHistory($dailyExpenses, $balance30DaysAgo);
$trendAnalysis = calculateTrendAnalysis($monthlyComparison, $connection, $mandantId);

// Maximalen Wert für Trend-Balken berechnen (inkl. Prognose für Skalierung)
$maxMonthlyValue = max(
    $monthlyComparison[0]['total'],
    $monthlyComparison[1]['total'],
    $monthlyComparison[2]['total'],
    $trendAnalysis['projected']
);
if ($maxMonthlyValue == 0) $maxMonthlyValue = 1;

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CashView - Die Finanzübersicht</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <link href="favicon.ico" rel="shortcut icon">
    <link rel="icon" href="favicon.ico" type="image/ico">
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
        }

        .navbar-brand {
            font-size: 1.1rem;
            margin-right: auto;
        }

        .btn-back {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
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
            margin-bottom: 0;
        }

        .card-body {
            padding: 1rem;
        }

        /* Verfügbar pro Tag */
        .daily-amount {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            padding: 1.5rem 0;
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            width: 100%;
            height: 300px;
            margin: 0 auto;
        }

        .chart-container-large {
            height: 350px;
        }

        /* Tabelle responsive */
        .table-responsive {
            font-size: 0.9rem;
        }

        #KatTable {
            font-size: 0.85rem;
        }

        #KatTable thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 10;
        }

        /* DataTables Mobile */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.85rem;
        }

        /* iPhone 13 */
        @media only screen and (min-width: 390px) and (max-width: 428px) {
            .container {
                max-width: 100%;
                padding-left: 12px;
                padding-right: 12px;
            }

            .daily-amount {
                font-size: 2.2rem;
            }

            .chart-container {
                height: 280px;
            }
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

            .card-title {
                font-size: 1.3rem;
            }

            .daily-amount {
                font-size: 2.5rem;
                padding: 2rem 0;
            }

            .chart-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
            }

            .chart-grid .card {
                margin-bottom: 0;
            }

            .chart-container {
                height: 320px;
            }

            #KatTable {
                font-size: 1rem;
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

            .btn-back {
                font-size: 1rem;
                padding: 0.5rem 1.5rem;
            }

            .card-title {
                font-size: 1.5rem;
            }

            .daily-amount {
                font-size: 3rem;
                padding: 2.5rem 0;
            }

            .chart-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .chart-grid .card {
                margin-bottom: 0;
            }

            .chart-full {
                grid-column: 1 / -1;
            }

            .chart-container {
                height: 350px;
            }

            .chart-container-large {
                height: 400px;
            }

            #KatTable {
                font-size: 1rem;
            }

            .table-responsive {
                font-size: 1rem;
            }
        }

        /* Landscape Mobile */
        @media only screen and (max-width: 926px) and (orientation: landscape) {
            .daily-amount {
                font-size: 1.8rem;
                padding: 1rem 0;
            }

            .chart-container {
                height: 250px;
            }
        }

        /* Touch-Optimierungen */
        @media (hover: none) and (pointer: coarse) {
            .btn {
                min-height: 44px;
                min-width: 44px;
            }

            .card {
                margin-bottom: 1.2rem;
            }
        }

        /* Dark Mode */
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

            .table {
                color: #ffffff;
            }

            .table thead th {
                color: #ffffff;
                background-color: #2a2a2a;
                border-color: #444;
            }

            .table td {
                color: #e0e0e0;
                border-color: #444;
            }

            .table-striped tbody tr:nth-of-type(odd) {
                background-color: rgba(255, 255, 255, 0.05);
            }

            .dataTables_wrapper {
                color: #ffffff;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                color: #ffffff;
            }
        }

        /* Trend-Analyse Styles */
        .trend-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .trend-bar-wrapper {
            margin-bottom: 1rem;
        }

        .trend-month {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .trend-amount {
            color: #666;
            font-size: 0.9rem;
        }

        .trend-bar-container {
            background: #e9ecef;
            border-radius: 8px;
            height: 30px;
            position: relative;
            overflow: hidden;
        }

        .trend-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 10px;
            color: white;
            font-size: 0.85rem;
            font-weight: bold;
            position: absolute;
            left: 0;
            top: 0;
        }

        .trend-bar.current-month {
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        }

        .trend-bar-projected {
            background: linear-gradient(90deg, rgba(40, 167, 69, 0.3) 0%, rgba(32, 201, 151, 0.3) 100%);
            opacity: 0.6;
            z-index: 1;
        }

        .trend-bar-actual {
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
            z-index: 2;
        }

        .trend-summary {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
            text-align: center;
        }

        .trend-summary h6 {
            margin-bottom: 1rem;
            color: #495057;
        }

        .trend-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .trend-up {
            color: #dc3545;
            background: #ffe6e6;
        }

        .trend-down {
            color: #28a745;
            background: #e6ffe6;
        }

        .trend-neutral {
            color: #ffc107;
            background: #fff8e1;
        }

        @media only screen and (min-width: 768px) {
            .trend-bar-container {
                height: 40px;
            }

            .trend-amount {
                font-size: 1rem;
            }
        }

        @media (prefers-color-scheme: dark) {
            .trend-amount {
                color: #aaaaaa;
            }

            .trend-summary {
                background-color: #2a2a2a;
                color: #ffffff;
            }

            .trend-summary h6 {
                color: #ffffff;
            }

            .trend-bar-container {
                background: #2a2a2a;
            }

            .trend-bar-projected {
                background: linear-gradient(90deg, rgba(81, 207, 102, 0.3) 0%, rgba(32, 201, 151, 0.3) 100%);
            }

            .trend-bar-actual {
                background: linear-gradient(90deg, #51cf66 0%, #20c997 100%);
            }

            .trend-up {
                color: #ff6b6b;
                background: #4a2020;
            }

            .trend-down {
                color: #51cf66;
                background: #1a4d2e;
            }

            .trend-neutral {
                color: #ffd43b;
                background: #4a3d00;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <span class="navbar-brand">CashView - Statistik</span>
            <a class="btn btn-secondary btn-back" href="index.php?manId=<?php echo $mandantId; ?>" role="button">Zurück</a>
        </nav>

        <!-- Verfügbar pro Tag -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Verfügbar pro Tag</h5>
            </div>
            <div class="card-body">
                <div class="daily-amount">
                    <?php echo number_format($dailyAverage, 2, ',', '.') . " €"; ?>
                </div>
            </div>
        </div>

        <!-- Trendanalyse -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">📈 Ausgaben-Trend (Monatsvergleich)</h5>
            </div>
            <div class="card-body">
                <div class="trend-container">
                    <?php
                    foreach ($monthlyComparison as $period) {
                        renderTrendBar($period, $maxMonthlyValue, $trendAnalysis);
                    }

                    renderTrendSummary($trendAnalysis);
                    ?>
                </div>
            </div>
        </div>

        <!-- Chart Grid -->
        <div class="chart-grid">
            <!-- Letzte 30 Tage -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Letzte 30 Tage</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chart30Days"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gesamt -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Gesamt</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartAll"></canvas>
                    </div>
                </div>
            </div>

            <!-- Verlauf -->
            <div class="card chart-full">
                <div class="card-header">
                    <h5 class="card-title">Guthaben-Verlauf (30 Tage)</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container chart-container-large">
                        <canvas id="chartLine"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kategorien Tabelle -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Aufteilung Kategorien (30 Tage)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="KatTable">
                        <thead>
                            <tr>
                                <th>Kategorie</th>
                                <th>Betrag</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php renderCategoryTable($categoryBreakdown); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Letzte 3 Transaktionen -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Letzte Transaktionen</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Datum</th>
                                <th>Betrag</th>
                                <th>Kategorie</th>
                                <th>Konto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php renderTransactionTable($lastTransactions); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <script>
    // DataTable
    $(document).ready(function () {
        $('#KatTable').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "order": [[ 1, "desc" ]],
            "responsive": true,
            "language": {
                "emptyTable": "Keine Daten verfügbar"
            }
        });
    });

    // Chart.js Konfiguration
    Chart.defaults.font.family = "'Segoe UI', 'Helvetica Neue', Arial, sans-serif";
    Chart.defaults.plugins.legend.display = true;
    Chart.defaults.plugins.legend.position = 'bottom';

    // Dark Mode Detection
    const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const textColor = isDarkMode ? '#ffffff' : '#666';
    const gridColor = isDarkMode ? '#444' : '#e0e0e0';

    // PHP Daten für JavaScript
    const data30 = <?php echo json_encode($chartData30Days['values']); ?>;
    const labels30 = <?php echo json_encode($chartData30Days['labels']); ?>;
    const colors30 = <?php echo json_encode($chartData30Days['colors']); ?>;

    const dataAll = <?php echo json_encode($chartDataAll['values']); ?>;
    const labelsAll = <?php echo json_encode($chartDataAll['labels']); ?>;
    const colorsAll = <?php echo json_encode($chartDataAll['colors']); ?>;

    const lineLabels = <?php echo json_encode($balanceHistory['dates']); ?>;
    const lineData = <?php echo json_encode($balanceHistory['balances']); ?>;

    // Chart 1: Letzte 30 Tage (Doughnut)
    const ctx30 = document.getElementById('chart30Days').getContext('2d');
    const chart30 = new Chart(ctx30, {
        type: 'doughnut',
        data: {
            labels: labels30,
            datasets: [{
                data: data30,
                backgroundColor: colors30,
                borderWidth: 2,
                borderColor: isDarkMode ? '#1e1e1e' : '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: textColor,
                        padding: 15,
                        font: {
                            size: 11
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (!data.datasets[0] || !data.datasets[0].data) return [];

                            const dataset = data.datasets[0].data;
                            const total = dataset.reduce((sum, value) => sum + parseFloat(value), 0);

                            if (total === 0) return [];

                            return data.labels.map((label, index) => {
                                const value = parseFloat(dataset[index]);
                                const percentage = ((value / total) * 100).toFixed(1);

                                return {
                                    text: label + ' (' + percentage + '%)',
                                    fillStyle: data.datasets[0].backgroundColor[index],
                                    hidden: false,
                                    index: index
                                };
                            });
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = parseFloat(context.parsed) || 0;
                            let total = context.dataset.data.reduce((sum, val) => sum + parseFloat(val), 0);
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value.toFixed(2) + ' € (' + percentage + '%)';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    });

    // Chart 2: Gesamt (Doughnut)
    const ctxAll = document.getElementById('chartAll').getContext('2d');
    const chartAll = new Chart(ctxAll, {
        type: 'doughnut',
        data: {
            labels: labelsAll,
            datasets: [{
                data: dataAll,
                backgroundColor: colorsAll,
                borderWidth: 2,
                borderColor: isDarkMode ? '#1e1e1e' : '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: textColor,
                        padding: 15,
                        font: {
                            size: 11
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (!data.datasets[0] || !data.datasets[0].data) return [];

                            const dataset = data.datasets[0].data;
                            const total = dataset.reduce((sum, value) => sum + parseFloat(value), 0);

                            if (total === 0) return [];

                            return data.labels.map((label, index) => {
                                const value = parseFloat(dataset[index]);
                                const percentage = ((value / total) * 100).toFixed(1);

                                return {
                                    text: label + ' (' + percentage + '%)',
                                    fillStyle: data.datasets[0].backgroundColor[index],
                                    hidden: false,
                                    index: index
                                };
                            });
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = parseFloat(context.parsed) || 0;
                            let total = context.dataset.data.reduce((sum, val) => sum + parseFloat(val), 0);
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ': ' + value.toFixed(2) + ' € (' + percentage + '%)';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    });

    // Chart 3: Guthaben-Verlauf (Line)
    const ctxLine = document.getElementById('chartLine').getContext('2d');

    // Gradient für Line Chart
    const gradient = ctxLine.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(40, 167, 69, 0.5)');
    gradient.addColorStop(1, 'rgba(40, 167, 69, 0.05)');

    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: lineLabels,
            datasets: [{
                label: 'Guthaben in €',
                data: lineData,
                borderColor: 'rgb(40, 167, 69)',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(40, 167, 69)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(40, 167, 69)',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: textColor,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgb(40, 167, 69)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Guthaben: ' + context.parsed.y.toFixed(2) + ' €';
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: true,
                        color: gridColor,
                        drawBorder: false
                    },
                    ticks: {
                        color: textColor,
                        maxRotation: 45,
                        minRotation: 45,
                        font: {
                            size: 10
                        }
                    }
                },
                y: {
                    display: true,
                    grid: {
                        display: true,
                        color: gridColor,
                        drawBorder: false
                    },
                    ticks: {
                        color: textColor,
                        callback: function(value) {
                            return value.toFixed(0) + ' €';
                        }
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });

    // Dark Mode Watcher für dynamische Anpassung
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            location.reload();
        });
    }
    </script>
</body>
</html>