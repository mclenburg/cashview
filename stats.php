<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CashView - Die Finanz&uuml;bersicht</title>
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
        }

        .trend-bar.current-month {
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
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
  <?php
           error_reporting(E_ALL);
           ini_set('display_errors', 1);

           $mandant = -1;
           if(isset($_POST["manId"]))
           {
             $mandant = $_POST["manId"];
           }
           elseif(isset($_GET["manId"])){
             $mandant = $_GET["manId"];
           }
           else {
             echo("Mandanten-ID nicht übergeben");
             echo("</body></html>");
             return;
           }

           $anzahl_tage = date("t");
           $heute = date("d");
           $resttage = $anzahl_tage - $heute + 1;

  	       $name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  		   ($GLOBALS["___mysqli_ston"] = mysqli_connect("192.168.5.103",  "cashview",  "cash123", "cashview"))  or die("ERROR connecting to database.");

           // Verfügbares Guthaben berechnen (identisch zu index.php)
           $query = "select Betrag, KtoID from Initialwerte inner join Konten on Konten.id = KtoID where Konten.manId = $mandant";
           $result = mysqli_query($GLOBALS["___mysqli_ston"], $query) or die("$query " .mysqli_error($GLOBALS["___mysqli_ston"]));
           $plus_kum = 0;

           while($init_wert = mysqli_fetch_assoc($result)) {
               $stand = $init_wert["Betrag"];
               $query_trans = "select Wert from transaktionen where KtoID = " .$init_wert["KtoID"] . " and manId = $mandant";
               $result_inner = mysqli_query($GLOBALS["___mysqli_ston"], $query_trans) OR die("Error: $query_trans " .mysqli_error($GLOBALS["___mysqli_ston"]));

               while($trans_row = mysqli_fetch_assoc($result_inner)) {
                   $stand = ($stand - $trans_row["Wert"]);
               }

               if($stand > 0) {
                   $plus_kum += $stand;
               }
           }

  		   $resultRest = mysqli_query($GLOBALS["___mysqli_ston"], "select sum(wert) wert from transaktionen where date(Datum) <= date(DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY)) and manId = $mandant")or die("queryRest " .mysqli_error($GLOBALS["___mysqli_ston"]));
  		   $rest = mysqli_fetch_assoc($resultRest)["wert"];
  		   $resultInit = mysqli_query($GLOBALS["___mysqli_ston"], "select sum(Betrag) wert from Initialwerte inner join Konten on Initialwerte.KtoId = Konten.id where Konten.manId = $mandant")or die("queryIni " .mysqli_error($GLOBALS["___mysqli_ston"]));
           $init = mysqli_fetch_assoc($resultInit)["wert"];
           $rest = $init - $rest;

           $queryAll = "select sum(trans.wert) summe, kat.bez, kat.ID, kat.statscolor from transaktionen trans left outer join kategorien kat on trans.katID = kat.ID where wert > 0 and trans.manId = $mandant and (kat.manId = 0 OR kat.manId = $mandant) group by katID order by sortorder";
  		   $query30 = "select sum(trans.wert) summe, kat.bez, kat.statscolor from transaktionen trans left outer join kategorien kat on trans.katID = kat.ID where wert > 0 and trans.manId = $mandant and trans.Datum > DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY) and (kat.manId = 0 OR kat.manId = $mandant) group by katID order by sortorder";

           $resultAll = mysqli_query($GLOBALS["___mysqli_ston"], $queryAll)or die("$queryAll " .mysqli_error($GLOBALS["___mysqli_ston"]));
           $result30 = mysqli_query($GLOBALS["___mysqli_ston"], $query30)or die("$query30 " .mysqli_error($GLOBALS["___mysqli_ston"]));

           $querySumPerKat30 = "select sum(t.wert) wert, k.bez kategorie from transaktionen t inner join kategorien k on t.katID = k.ID where date(t.Datum) >= date(DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY)) and k.bez != 'Gehalt' and t.manId = $mandant and (k.manId = 0 OR k.manId = $mandant) group by k.bez order by k.sortorder";
           $sumPerKat30 = mysqli_query($GLOBALS["___mysqli_ston"], $querySumPerKat30)or die("$querySumPerKat30 " .mysqli_error($GLOBALS["___mysqli_ston"]));

           // 30-Tage-Perioden Vergleich
           $monthlyComparison = array();
           for($i = 0; $i < 3; $i++) {
               $endDays = $i * 30;
               $startDays = $endDays + 30;

               $endDate = date('Y-m-d', strtotime("-$endDays days"));
               $startDate = date('Y-m-d', strtotime("-$startDays days"));

               if($i == 0) {
                   $periodName = "Letzte 30 Tage";
               } else {
                   $periodName = "Vor " . ($i * 30) . "-" . (($i + 1) * 30) . " Tagen";
               }

               $queryMonth = "SELECT SUM(wert) as total FROM transaktionen
                             WHERE manId = $mandant
                             AND wert > 0
                             AND DATE(Datum) > '$startDate'
                             AND DATE(Datum) <= '$endDate'";
               $resultMonth = mysqli_query($GLOBALS["___mysqli_ston"], $queryMonth);
               $row = mysqli_fetch_assoc($resultMonth);

               $monthlyComparison[] = array(
                   'period' => $periodName,
                   'total' => $row['total'] ? floatval($row['total']) : 0,
                   'startDate' => $startDate,
                   'endDate' => $endDate
               );
           }

           $avgMonthly = array_sum(array_column($monthlyComparison, 'total')) / 3;
           $currentPeriod = $monthlyComparison[0]['total'];
           $trendPercent = $avgMonthly > 0 ? (($currentPeriod - $avgMonthly) / $avgMonthly * 100) : 0;

           // Daten für Chart.js sammeln
           $arrayAll = array();
           $arrayAll_labels = array();
           $arrayAll_colors = array();
           while( $row = mysqli_fetch_assoc( $resultAll)){
               $arrayAll[] = $row["summe"];
               $arrayAll_labels[] = $row["bez"];
               $color = explode(",", $row["statscolor"]);
               $arrayAll_colors[] = "rgba(".$color[0].",".$color[1].",".$color[2].", 0.8)";
           }

           $array30 = array();
           $array30_labels = array();
           $array30_colors = array();
           while( $row = mysqli_fetch_assoc( $result30)){
               $array30[] = $row["summe"];
               $array30_labels[] = $row["bez"];
               $color = explode(",", $row["statscolor"]);
               $array30_colors[] = "rgba(".$color[0].",".$color[1].",".$color[2].", 0.8)";
           }

           // Verlaufsdiagramm Daten
           $queryLine = "select sum(trans.wert) summe, DATE(trans.Datum) datum from transaktionen trans WHERE date(trans.Datum) > date(DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY)) and manId = $mandant group by DATE(Datum) ORDER BY Datum";
           $resultLine = mysqli_query($GLOBALS["___mysqli_ston"], $queryLine)or die("$queryLine " .mysqli_error($GLOBALS["___mysqli_ston"]));

           $arrayLine_dates = array();
           $arrayLine_values = array();
           $arrayLine = array();
           while( $row = mysqli_fetch_assoc( $resultLine)){
             $arrayLine[$row["datum"]] = $row["summe"];
           }

           // Guthaben-Verlauf berechnen
           $guthabenVerlauf = array();
           $tempGuthaben = $rest;

           for($dat=30; $dat>=0; $dat--) {
               $date = new DateTime("-".$dat." days");
               $dateStr = $date->format("Y-m-d");
               $dateLabel = $date->format("d.m.");

               if(isset($arrayLine[$dateStr])) {
                   $tempGuthaben -= $arrayLine[$dateStr];
               }

               $arrayLine_dates[] = $dateLabel;
               $guthabenVerlauf[] = round($tempGuthaben, 2);
           }
  ?>
  <div class="container">
  	      <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  	        <span class="navbar-brand">CashView - Statistik</span>
            <a class="btn btn-secondary btn-back" href="index.php?manId=<?php echo $mandant; ?>" role="button">Zurück</a>
  	      </nav>

          <!-- Verfügbar pro Tag -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Verfügbar pro Tag</h5>
            </div>
            <div class="card-body">
              <div class="daily-amount">
                <?php echo(number_format(round($plus_kum/$resttage,2), 2, ',', '.') ." €"); ?>
              </div>
            </div>
          </div>

          <!-- Trendanalyse -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">📈 Ausgaben-Trend (3x 30-Tage-Perioden)</h5>
            </div>
            <div class="card-body">
              <div class="trend-container">
                <?php
                  $maxValue = max(array_column($monthlyComparison, 'total'));
                  if($maxValue == 0) $maxValue = 1;

                  foreach($monthlyComparison as $index => $period) {
                    $barWidth = ($period['total'] / $maxValue * 100);
                    $isCurrent = $index === 0;
                    $barClass = $isCurrent ? 'current-month' : '';

                    echo('<div class="trend-bar-wrapper">');
                    echo('<div class="trend-month">');
                    echo('<span>' . $period['period'] . '</span>');
                    echo('<span class="trend-amount">' . number_format($period['total'], 2, ',', '.') . ' €</span>');
                    echo('</div>');
                    echo('<div class="trend-bar-container">');
                    echo('<div class="trend-bar ' . $barClass . '" style="width: ' . $barWidth . '%"></div>');
                    echo('</div>');
                    echo('</div>');
                  }
                ?>

                <div class="trend-summary">
                  <h6>Trend-Analyse</h6>
                  <div>
                    <div>Durchschnitt (90 Tage): <strong><?php echo number_format($avgMonthly, 2, ',', '.'); ?> €</strong></div>
                    <?php
                      if(abs($trendPercent) < 5) {
                        echo('<div class="trend-indicator trend-neutral">');
                        echo('≈ ' . number_format(abs($trendPercent), 1) . '% Stabil');
                      } elseif($trendPercent > 0) {
                        echo('<div class="trend-indicator trend-up">');
                        echo('↑ +' . number_format($trendPercent, 1) . '% Höher');
                      } else {
                        echo('<div class="trend-indicator trend-down">');
                        echo('↓ ' . number_format($trendPercent, 1) . '% Niedriger');
                      }
                      echo('</div>');

                      if(abs($trendPercent) < 5) {
                        echo('<p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">Deine Ausgaben sind stabil.</p>');
                      } elseif($trendPercent > 0) {
                        echo('<p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">Du gibst mehr aus als im Durchschnitt der letzten 90 Tage.</p>');
                      } else {
                        echo('<p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">Gut gemacht! Du gibst weniger aus als im Durchschnitt.</p>');
                      }
                    ?>
                  </div>
                </div>
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
                    <?php
                       while( $row = mysqli_fetch_assoc( $sumPerKat30)){
                          echo("<tr><td>".$row["kategorie"]."</td><td>".number_format($row["wert"], 2, ',', '.')." €</td></tr>");
                       }
                    ?>
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
                    <?php
                       $queryLastTrans = "SELECT t.Wert, t.Datum, k.bez as Kategorie, ko.Bez as Konto
                                          FROM transaktionen t
                                          LEFT JOIN kategorien k ON t.katID = k.ID
                                          LEFT JOIN Konten ko ON t.KtoID = ko.id
                                          WHERE t.manId = $mandant
                                          ORDER BY t.Datum DESC
                                          LIMIT 3";
                       $resultLastTrans = mysqli_query($GLOBALS["___mysqli_ston"], $queryLastTrans);

                       while($row = mysqli_fetch_assoc($resultLastTrans)) {
                           $datum = date('d.m.Y H:i', strtotime($row["Datum"]));
                           $betrag = number_format($row["Wert"], 2, ',', '.');

                           echo("<tr>");
                           echo("<td>".$datum."</td>");
                           echo("<td>".$betrag." €</td>");
                           echo("<td>".($row["Kategorie"] ? $row["Kategorie"] : '-')."</td>");
                           echo("<td>".($row["Konto"] ? $row["Konto"] : '-')."</td>");
                           echo("</tr>");
                       }
                    ?>
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
    const data30 = <?php echo json_encode($array30); ?>;
    const labels30 = <?php echo json_encode($array30_labels); ?>;
    const colors30 = <?php echo json_encode($array30_colors); ?>;

    const dataAll = <?php echo json_encode($arrayAll); ?>;
    const labelsAll = <?php echo json_encode($arrayAll_labels); ?>;
    const colorsAll = <?php echo json_encode($arrayAll_colors); ?>;

    const lineLabels = <?php echo json_encode($arrayLine_dates); ?>;
    const lineData = <?php echo json_encode($guthabenVerlauf); ?>;

    // Funktion zum Berechnen der Prozentangaben für Legende
    function generateLegendLabels(chart) {
        const data = chart.data;
        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);

        return data.labels.map((label, index) => {
            const value = data.datasets[0].data[index];
            const percentage = ((value / total) * 100).toFixed(1);
            return label + ' (' + percentage + '%)';
        });
    }

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
                            const total = data.datasets[0].data.reduce((a, b) => a + b, 0);

                            return data.labels.map((label, index) => {
                                const value = data.datasets[0].data[index];
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
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
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
                            const total = data.datasets[0].data.reduce((a, b) => a + b, 0);

                            return data.labels.map((label, index) => {
                                const value = data.datasets[0].data[index];
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
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
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
            location.reload(); // Reload bei Theme-Wechsel
        });
    }
    </script>
</body>
</html>