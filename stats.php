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

        /* Navigation optimiert für Mobile */
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
            margin-bottom: 0;
        }

        .card-body {
            padding: 1rem;
        }

        /* Verfügbar pro Tag - Extra prominent */
        .daily-amount {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            padding: 1.5rem 0;
        }

        /* Diagramme responsive */
        .chart-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .chart-container img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
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

        /* DataTables Mobile Optimierung */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.85rem;
        }

        /* iPhone 13 (390x844) */
        @media only screen and (min-width: 390px) and (max-width: 428px) {
            .container {
                max-width: 100%;
                padding-left: 12px;
                padding-right: 12px;
            }

            .chart-container img {
                width: 100%;
                max-width: 370px;
            }

            .daily-amount {
                font-size: 2.2rem;
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

            .daily-amount {
                font-size: 2.5rem;
                padding: 2rem 0;
            }

            .chart-container {
                display: flex;
                justify-content: center;
            }

            .chart-container img {
                max-width: 500px;
            }

            /* Zwei Spalten Layout für Diagramme */
            .chart-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
            }

            .chart-grid .card {
                margin-bottom: 0;
            }

            #KatTable {
                font-size: 1rem;
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

            .chart-container img {
                max-width: 600px;
            }

            /* Drei Spalten Layout für kleinere Charts */
            .chart-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .chart-grid .card {
                margin-bottom: 0;
            }

            /* Volle Breite für Liniendiagramm */
            .chart-full {
                grid-column: 1 / -1;
            }

            #KatTable {
                font-size: 1rem;
            }

            .table-responsive {
                font-size: 1rem;
            }
        }

        /* Landscape Modus für Mobilgeräte */
        @media only screen and (max-width: 926px) and (orientation: landscape) {
            .chart-container img {
                max-width: 450px;
            }

            .daily-amount {
                font-size: 1.8rem;
                padding: 1rem 0;
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

            .table {
                color: #ffffff;
            }

            .table-striped tbody tr:nth-of-type(odd) {
                background-color: rgba(255, 255, 255, 0.05);
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

  		   $resultRest = mysqli_query($GLOBALS["___mysqli_ston"], "select sum(wert) wert from transaktionen where date(Datum) <= date(DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY)) and manId = $mandant")or die("queryRest " .mysqli_error($GLOBALS["___mysqli_ston"]));
  		   $rest = mysqli_fetch_assoc($resultRest)["wert"];
  		   $resultInit = mysqli_query($GLOBALS["___mysqli_ston"], "select sum(Betrag) wert from Initialwerte inner join Konten on Initialwerte.KtoId = Konten.id where Konten.manId = $mandant")or die("queryIni " .mysqli_error($GLOBALS["___mysqli_ston"]));
           $init = mysqli_fetch_assoc($resultInit)["wert"];
           $rest = $init - $rest;

           $queryAll = "select sum(trans.wert) summe, kat.bez, kat.ID, kat.statscolor from transaktionen trans left outer join kategorien kat on trans.katID = kat.ID where wert > 0 and trans.manId = $mandant and (kat.manId = 0 OR kat.manId = $mandant) group by katID order by sortorder";
  		   $query30 = "select sum(trans.wert) summe, kat.bez from transaktionen trans left outer join kategorien kat on trans.katID = kat.ID where wert > 0 and trans.manId = $mandant and trans.Datum > DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY) and (kat.manId = 0 OR kat.manId = $mandant) group by katID order by sortorder";

           $resultAll = mysqli_query($GLOBALS["___mysqli_ston"], $queryAll)or die("$queryAll " .mysqli_error($GLOBALS["___mysqli_ston"]));
           $result30 = mysqli_query($GLOBALS["___mysqli_ston"], $query30)or die("$query30 " .mysqli_error($GLOBALS["___mysqli_ston"]));

           $querySumPerKat30 = "select sum(t.wert) wert, k.bez kategorie from transaktionen t inner join kategorien k on t.katID = k.ID where date(t.Datum) >= date(DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY)) and k.bez != 'Gehalt' and t.manId = $mandant and (k.manId = 0 OR k.manId = $mandant) group by k.bez order by k.sortorder";
           $sumPerKat30 = mysqli_query($GLOBALS["___mysqli_ston"], $querySumPerKat30)or die("$querySumPerKat30 " .mysqli_error($GLOBALS["___mysqli_ston"]));

           // 30-Tage-Perioden Vergleich (rollierend)
           $monthlyComparison = array();
           for($i = 0; $i < 3; $i++) {
               $endDays = $i * 30;
               $startDays = $endDays + 30;

               $endDate = date('Y-m-d', strtotime("-$endDays days"));
               $startDate = date('Y-m-d', strtotime("-$startDays days"));

               // Label für die Periode
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

           // Durchschnitt berechnen
           $avgMonthly = array_sum(array_column($monthlyComparison, 'total')) / 3;

           // Trend berechnen (aktuelle 30 Tage vs. Durchschnitt)
           $currentPeriod = $monthlyComparison[0]['total'];
           $trendPercent = $avgMonthly > 0 ? (($currentPeriod - $avgMonthly) / $avgMonthly * 100) : 0;

           $breite = 350;
           $hoehe = 250;
           $radius = 200;
           $start_x = ($breite/3)*2;
           $start_y = $hoehe/2;

           $rand_oben = 20;
           $rand_links = 20;
           $punktbreite = 10;
           $abstand = 10;
           $schriftgroesse = 10;

           $diagrammAll = imagecreatetruecolor($breite, $hoehe);
           $diagramm30 = imagecreatetruecolor($breite, $hoehe);
           $diagrammLine = imagecreatetruecolor($breite, $hoehe+30);

           $schwarz = imagecolorallocate($diagrammAll, 0, 0, 0);
           $weiss = imagecolorallocate($diagrammAll, 255, 255, 255);
           $schwarz30 = imagecolorallocate($diagramm30, 0, 0, 0);
           $weiss30 = imagecolorallocate($diagramm30, 255, 255, 255);
           $yellow = imagecolorallocate($diagrammLine, 255, 250, 140);
           $lightyellow = imagecolorallocate($diagrammLine, 255, 246, 143);

           $arrayAll = array();
           $colorMap = array();
           while( $row = mysqli_fetch_assoc( $resultAll)){
               $arrayAll[$row["bez"]] = $row["summe"];
               $color=explode(",", $row["statscolor"]);
               $colorMap[$row["bez"]] = imagecolorallocate($diagrammAll, $color[0], $color[1], $color[2]);
           }

           $array30 = array();
           while( $row = mysqli_fetch_assoc( $result30)){
               $array30[$row["bez"]] = $row["summe"];
           }

           $queryLine = "select sum(trans.wert) summe, DATE(trans.Datum) datum from transaktionen trans WHERE date(trans.Datum) > date(DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY)) and manId = $mandant group by DATE(Datum) ORDER BY Datum";
           $resultLine = mysqli_query($GLOBALS["___mysqli_ston"], $queryLine)or die("$queryLine " .mysqli_error($GLOBALS["___mysqli_ston"]));
           $arrayLine = array();
           while( $row = mysqli_fetch_assoc( $resultLine)){
             $arrayLine[$row["datum"]] = $row["summe"];
           }
           $jetzt = $datum = date("d.m.Y");

           imagefill($diagrammAll, 0, 0, $weiss);
           imagefill($diagramm30, 0, 0, $weiss);
           imagefill($diagrammLine, 0, 0, $weiss);

           $gesamtAll = array_sum($arrayAll);
           $gesamt30 = array_sum($array30);

           $i = 0;
           $winkel = 0;
           foreach($arrayAll as $key => $value)
           {
             $i++;
             $start = round($winkel);
             $winkel = $start + round($value*360/$gesamtAll);

             $color = $colorMap[$key];
             imagesetthickness ( $diagrammAll , 3 );
             for($rad = 0; $rad <= 50; $rad++) {
               imagearc($diagrammAll, round($start_x), round($start_y), round($radius-$rad), round($radius-$rad), $start, $winkel, $color);
             }
             $unterkante = $rand_oben+$punktbreite+($i-1)*($punktbreite+$abstand);
             imagefilledrectangle($diagrammAll, $rand_links, $rand_oben+($i-1)*($punktbreite+$abstand), $rand_links+$punktbreite, $unterkante, $color);
             imagettftext($diagrammAll, $schriftgroesse, 0, $rand_links+$punktbreite+5, $unterkante-$punktbreite/2+$schriftgroesse/2, $schwarz, "media/NotoSans-Regular.ttf", $key." ".round($value*100/$gesamtAll, 1)." %");
           }

           $i = 0;
           $winkel = 0;
           foreach($array30 as $key => $value)
           {
             $i++;
             $start = $winkel;
             $winkel = $start + round($value*360/$gesamt30);

             $color = $colorMap[$key];
             imagesetthickness ( $diagramm30 , 3 );
             for($rad = 0; $rad <= 50; $rad++) {
               imagearc($diagramm30, round($start_x), round($start_y), round($radius-$rad), round($radius-$rad), $start, $winkel, $color);
             }
             $unterkante = $rand_oben+$punktbreite+($i-1)*($punktbreite+$abstand);
             imagefilledrectangle($diagramm30, $rand_links, $rand_oben+($i-1)*($punktbreite+$abstand), $rand_links+$punktbreite, $unterkante, $color);
             imagettftext($diagramm30, $schriftgroesse, 0, $rand_links+$punktbreite+5, $unterkante-$punktbreite/2+$schriftgroesse/2, $schwarz, "media/NotoSans-Regular.ttf", $key." ".round($value*100/$gesamt30, 1)." %");
           }

           $maxGuthaben = $rest;
           $minGuthaben = $rest;
           $tempGuthaben = $rest;
           foreach($arrayLine as $key => $value) {
               $tempGuthaben += (0-$value);
               if($tempGuthaben > $maxGuthaben) $maxGuthaben = $tempGuthaben;
               if($tempGuthaben < $minGuthaben) $minGuthaben = $tempGuthaben;
           }

           $xperday = round($breite-($rand_links+40))/30;
           $ypereuro = round($hoehe-$rand_oben)/($maxGuthaben-$minGuthaben);
           $posxachse = round($hoehe-$rand_oben-$ypereuro*(0-$minGuthaben));
           if($posxachse > $hoehe-$rand_oben) $posxachse= ($hoehe-$rand_oben);

           imageline($diagrammLine, ($rand_links+40), 0, ($rand_links+40), ($hoehe-$rand_oben+3), $schwarz);
           imageline($diagrammLine, round($rand_links+37), round($posxachse), $breite, round($posxachse), $schwarz);
           if($minGuthaben > 0) {
             imagettftext($diagrammLine, $schriftgroesse, 0, $rand_links+5, round($posxachse) , $schwarz, "media/NotoSans-Regular.ttf", round($minGuthaben,-1));
           }
           else {
             imagettftext($diagrammLine, $schriftgroesse, 0, $rand_links+5, round($posxachse) , $schwarz, "media/NotoSans-Regular.ttf", 0);
           }

           imagettftext($diagrammLine, $schriftgroesse, 90, $rand_links, $hoehe/2+$schriftgroesse/2, $schwarz, "media/NotoSans-Regular.ttf", "Guthaben");
           $i = 0;
           $lichtgrau = imagecolorallocate($diagrammLine, 200, 200, 200);
           $stepsize = 50;
           for($wert = $minGuthaben; $wert <= $maxGuthaben; $wert+=$stepsize) {
             if($wert <-10 || $wert > 10) {
               imagettftext($diagrammLine, $schriftgroesse, 0, $rand_links+5, round($hoehe-$rand_oben - ($ypereuro*$i*$stepsize)) , $schwarz, "media/NotoSans-Regular.ttf", round($wert,-1));
               if($i>0) {
                 imageline($diagrammLine, round($rand_links+37), round($hoehe-$rand_oben-($ypereuro*$i*$stepsize)), $breite, round($hoehe-$rand_oben-($ypereuro*$i*$stepsize)), $lichtgrau);
               }
             }
             $i++;
           }

           for($dat=30; $dat>=0; $dat--) {
             $date = new DateTime("-".$dat." days");
             if($dat%5==0) {
               imagettftext($diagrammLine, 8, 70, round($rand_links+40+$xperday*(30-$dat)-8), ($hoehe+10) , $schwarz, "media/NotoSans-Regular.ttf", str_pad($date->format("d.m."), strlen($maxGuthaben), " ", STR_PAD_LEFT));
               imageline($diagrammLine, round($rand_links+40+$xperday*(30-$dat)), $posxachse, round($rand_links+40+$xperday*(30-$dat)), $posxachse+2, $schwarz);
             }
             if($date->format("D") == "Sat") {
               imagesetthickness ( $diagrammLine , round($xperday) );
               imageline($diagrammLine, round($rand_links+40+$xperday*(30-$dat)), $posxachse-1, round($rand_links+40+$xperday*(30-$dat)), 0, $lightyellow);
             }
             if($date->format("D") == "Sun") {
               imagesetthickness ( $diagrammLine , round($xperday) );
               imageline($diagrammLine, round($rand_links+40+$xperday*(30-$dat)), $posxachse-1, round($rand_links+40+$xperday*(30-$dat)), 0, $yellow);
             }
             imagesetthickness ($diagrammLine , 1 );
           }

           if($minGuthaben<0) {
             $minGuthaben=0;
           }
           $posy_alt = round($posxachse-($ypereuro*$rest)+($ypereuro*$minGuthaben));
           $dat_alt = 30;
           for($dat=29; $dat>=0; $dat--) {
               $found = false;
               foreach($arrayLine as $key => $value) {
                 $date = new DateTime("-".$dat." days");
                 if(strtotime($key) == strtotime($date->format("Y-m-d"))) {
                     $rest -= $value;
                     imageline($diagrammLine, round(($rand_links+40)+$xperday*(30-$dat_alt)), round($posy_alt), round(($rand_links+40)+$xperday*(30-$dat)), round($posxachse-($ypereuro*$rest)+($ypereuro*$minGuthaben)), $schwarz);
                     $posy_alt = $posxachse-($ypereuro*$rest) + ($ypereuro*$minGuthaben);
                     $dat_alt = $dat;
                     $found = true;
                 }
               }
               if(!$found) {
                 imageline($diagrammLine, round(($rand_links+40)+$xperday*(30-$dat_alt)), round($posy_alt), round(($rand_links+40)+$xperday*(30-$dat)), round($posy_alt), $schwarz);
                 $dat_alt = $dat;
               }
           }
  ?>
  <div class="container">
  	      <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  	        <span class="navbar-brand">CashView - Statistik</span>
            <a class="btn btn-secondary btn-back" href="index.php?manId=<?php echo $mandant; ?>" role="button">Zurück</a>
  	      </nav>

          <!-- Verfügbar pro Tag - Prominent platziert -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Verfügbar pro Tag</h5>
            </div>
            <div class="card-body">
              <div class="daily-amount">
                <?php echo(number_format(round($rest/$resttage,2), 2, ',', '.') ." €"); ?>
              </div>
            </div>
          </div>

          <!-- 30-Tage-Perioden Vergleich / Trendanalyse -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">📈 Ausgaben-Trend (3x 30-Tage-Perioden)</h5>
            </div>
            <div class="card-body">
              <?php
                // Debug-Ausgabe im HTML-Kommentar
                echo('<!-- Debug Info:');
                echo(' Mandant: ' . $mandant);
                foreach($monthlyComparison as $m) {
                  echo(' | ' . $m['period'] . ': ' . $m['total'] . '€ (' . $m['startDate'] . ' bis ' . $m['endDate'] . ')');
                }
                echo(' -->');
              ?>
              <div class="trend-container">
                <?php
                  // Maximalen Wert für Balkenbreite finden
                  $maxValue = max(array_column($monthlyComparison, 'total'));
                  if($maxValue == 0) $maxValue = 1; // Verhindere Division durch 0

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

                      // Textliche Interpretation
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

          <!-- Chart Grid für Tablet und Desktop -->
          <div class="chart-grid">
            <!-- Letzte 30 Tage -->
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Letzte 30 Tage</h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <?php
                     ob_start();
                     imagepng($diagramm30);
                     $imagedata = ob_get_clean();
                     echo("<img src=\"data:image/png;base64,".base64_encode($imagedata)."\" alt=\"Diagramm 30 Tage\">");
                  ?>
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
                  <?php
                     ob_start();
                     imagepng($diagrammAll);
                     $imagedata = ob_get_clean();
                     echo("<img src=\"data:image/png;base64,".base64_encode($imagedata)."\" alt=\"Diagramm Gesamt\">");
                  ?>
                </div>
              </div>
            </div>

            <!-- Verlauf - Volle Breite auf Desktop -->
            <div class="card chart-full">
              <div class="card-header">
                <h5 class="card-title">Verlauf (30 Tage)</h5>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <?php
                     ob_start();
                     imagepng($diagrammLine);
                     $imagedata = ob_get_clean();
                     echo("<img src=\"data:image/png;base64,".base64_encode($imagedata)."\" alt=\"Verlaufsdiagramm\">");
                  ?>
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
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script>
    $(document).ready(function () {
      // Responsive DataTable Konfiguration
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
    </script>
</body>
</html>