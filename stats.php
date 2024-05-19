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
</head>
<body>
  <?php
           $mandant = -1;
           if(isset($_POST["manId"]))
           {
             $mandant = $_POST["manId"];
           }
           else {
             $mandant = $_GET["manId"];
           }
           error_reporting(E_ALL);
           ini_set('display_errors', 1);

           $anzahl_tage = date("t");
           $heute = date("d");
           $resttage = $anzahl_tage - $heute + 1; //plus 1, da heute ja auch noch zur Verfügung steht

  	       $name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  		   ($GLOBALS["___mysqli_ston"] = mysqli_connect("192.168.5.103",  "cashview",  "cash123", "cashview"))  or die("ERROR connecting to database.");

  		   $resultRest = mysqli_query($GLOBALS["___mysqli_ston"], "select sum(wert) wert from transaktionen where date(Datum) <= date(DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY)) and manId = $mandant")or die("queryRest " .mysqli_error($GLOBALS["___mysqli_ston"]));
  		   $rest = mysqli_fetch_assoc($resultRest)["wert"];
  		   $resultInit = mysqli_query($GLOBALS["___mysqli_ston"], "select sum(Betrag) wert from Initialwerte inner join Konten on Initialwerte.KtoId = Konten.id where Konten.manId = $mandant")or die("queryIni " .mysqli_error($GLOBALS["___mysqli_ston"]));
           $init = mysqli_fetch_assoc($resultInit)["wert"];
           $rest = $init - $rest;

  		   $queryAll = "select sum(trans.wert) summe, kat.bez, kat.ID, kat.statscolor from transaktionen trans left outer join kategorien kat on trans.katID = kat.ID where wert > 0 and manId = $mandant group by katID order by sortorder";
  		   $query30 = "select sum(trans.wert) summe, kat.bez from transaktionen trans left outer join kategorien kat on trans.katID = kat.ID where wert > 0 and manId = $mandant and trans.Datum > DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY) group by katID order by sortorder";

           $resultAll = mysqli_query($GLOBALS["___mysqli_ston"], $queryAll)or die("$queryAll " .mysqli_error($GLOBALS["___mysqli_ston"]));
           $result30 = mysqli_query($GLOBALS["___mysqli_ston"], $query30)or die("$query30 " .mysqli_error($GLOBALS["___mysqli_ston"]));

           $querySumPerKat30 = "select sum(t.wert) wert, k.bez kategorie from transaktionen t inner join kategorien k on t.katID = k.ID where date(t.Datum) >= date(DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY)) and k.bez != 'Gehalt' and manId = $mandant group by k.bez order by k.sortorder";
           $sumPerKat30 = mysqli_query($GLOBALS["___mysqli_ston"], $querySumPerKat30)or die("$querySumPerKat30 " .mysqli_error($GLOBALS["___mysqli_ston"]));

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

           while( $row = mysqli_fetch_assoc( $resultAll)){
               $arrayAll[$row["bez"]] = $row["summe"];
               $color=explode(",", $row["statscolor"]);
               $colorMap[$row["bez"]] = imagecolorallocate($diagrammAll, $color[0], $color[1], $color[2]);
           }
           while( $row = mysqli_fetch_assoc( $result30)){
               $array30[$row["bez"]] = $row["summe"];
           }

           $queryLine = "select sum(trans.wert) summe, DATE(trans.Datum) datum from transaktionen trans WHERE date(trans.Datum) > date(DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY)) and manId = $mandant group by DATE(Datum) ORDER BY Datum";
           $resultLine = mysqli_query($GLOBALS["___mysqli_ston"], $queryLine)or die("$queryLine " .mysqli_error($GLOBALS["___mysqli_ston"]));
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
             $start = $winkel;
             $winkel = $start + $value*360/$gesamtAll;

             $color = $colorMap[$key];
             imagesetthickness ( $diagrammAll , 3 );
             for($rad = 0; $rad <= 50; $rad++) {
               imagearc($diagrammAll, $start_x, $start_y, round($radius-$rad), round($radius-$rad), $start, $winkel, $color);  //because gap
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
             $winkel = $start + $value*360/$gesamt30;

             $color = $colorMap[$key];
             imagesetthickness ( $diagramm30 , 3 );
             for($rad = 0; $rad <= 50; $rad++) {
               imagearc($diagramm30, $start_x, $start_y, round($radius-$rad), round($radius-$rad), $start, $winkel, $color);  //because gap
             }
             $unterkante = $rand_oben+$punktbreite+($i-1)*($punktbreite+$abstand);
             imagefilledrectangle($diagramm30, $rand_links, $rand_oben+($i-1)*($punktbreite+$abstand), $rand_links+$punktbreite, $unterkante, $color);
             imagettftext($diagramm30, $schriftgroesse, 0, $rand_links+$punktbreite+5, $unterkante-$punktbreite/2+$schriftgroesse/2, $schwarz, "media/NotoSans-Regular.ttf", $key." ".round($value*100/$gesamt30, 1)." %");
           }

           //maxGuthaben ermitteln, ausgehend von $rest
           $maxGuthaben = $rest;
           $minGuthaben = $rest;
           $tempGuthaben = $rest;
           foreach($arrayLine as $key => $value) {
               $tempGuthaben += (0-$value);
               if($tempGuthaben > $maxGuthaben) $maxGuthaben = $tempGuthaben;
               if($tempGuthaben < $minGuthaben) $minGuthaben = $tempGuthaben;
           }

           $xperday = ($breite-($rand_links+40))/30;
           $ypereuro = ($hoehe-$rand_oben)/($maxGuthaben-$minGuthaben);
           $posxachse = $hoehe-$rand_oben-$ypereuro*(0-$minGuthaben);
           if($posxachse > $hoehe-$rand_oben) $posxachse= ($hoehe-$rand_oben);

           //Liniendiagramm bauen
           imageline($diagrammLine, ($rand_links+40), 0, ($rand_links+40), ($hoehe-$rand_oben+3), $schwarz); //Y-Achse
           imageline($diagrammLine, ($rand_links+37), $posxachse, $breite, $posxachse, $schwarz); //X-Achse
           if($minGuthaben > 0) {
             imagettftext($diagrammLine, $schriftgroesse, 0, $rand_links+5, $posxachse , $schwarz, "media/NotoSans-Regular.ttf", round($minGuthaben,-1));
           }
           else {
             imagettftext($diagrammLine, $schriftgroesse, 0, $rand_links+5, $posxachse , $schwarz, "media/NotoSans-Regular.ttf", 0);
           }

           //Y-Achse beschriften
           imagettftext($diagrammLine, $schriftgroesse, 90, $rand_links, $hoehe/2+$schriftgroesse/2, $schwarz, "media/NotoSans-Regular.ttf", "Guthaben");
           $i = 0;
           $lichtgrau = imagecolorallocate($diagrammLine, 200, 200, 200);
           $stepsize = 50;
           for($wert = $minGuthaben; $wert <= $maxGuthaben; $wert+=$stepsize) {
             if($wert <-10 || $wert > 10) {
               imagettftext($diagrammLine, $schriftgroesse, 0, $rand_links+5, ($hoehe-$rand_oben - ($ypereuro*$i*$stepsize)) , $schwarz, "media/NotoSans-Regular.ttf", round($wert,-1));
               if($i>0) {
                 imageline($diagrammLine, ($rand_links+37), ($hoehe-$rand_oben-($ypereuro*$i*$stepsize)), $breite, ($hoehe-$rand_oben-($ypereuro*$i*$stepsize)), $lichtgrau);
               }
             }
             $i++;
           }

           //X-Achse beschriften
           for($dat=30; $dat>=0; $dat--) {
             $date = new DateTime("-".$dat." days");
             if($dat%5==0) {
               imagettftext($diagrammLine, 8, 70, ($rand_links+40+$xperday*(30-$dat)-8), ($hoehe+10) , $schwarz, "media/NotoSans-Regular.ttf", str_pad($date->format("d.m."), strlen($maxGuthaben), " ", STR_PAD_LEFT));
               imageline($diagrammLine, ($rand_links+40+$xperday*(30-$dat)), $posxachse, ($rand_links+40+$xperday*(30-$dat)), $posxachse+2, $schwarz);
             }
             if($date->format("D") == "Sat") {
               imagesetthickness ( $diagrammLine , $xperday );
               imageline($diagrammLine, ($rand_links+40+$xperday*(30-$dat)), $posxachse-1, ($rand_links+40+$xperday*(30-$dat)), 0, $lightyellow);
             }
             if($date->format("D") == "Sun") {
               imagesetthickness ( $diagrammLine , $xperday );
               imageline($diagrammLine, ($rand_links+40+$xperday*(30-$dat)), $posxachse-1, ($rand_links+40+$xperday*(30-$dat)), 0, $yellow);
             }
             imagesetthickness ($diagrammLine , 1 );
           }

           //Werte eintragen
           if($minGuthaben<0) {
             $minGuthaben=0;
           }
           $posy_alt = $posxachse-($ypereuro*$rest)+($ypereuro*$minGuthaben);
           $dat_alt = 30;
           for($dat=29; $dat>=0; $dat--) {
               $found = false;
               foreach($arrayLine as $key => $value) {
                 $date = new DateTime("-".$dat." days");
                 if(strtotime($key) == strtotime($date->format("Y-m-d"))) {
                     $rest -= $value;
                     imageline($diagrammLine, ($rand_links+40)+$xperday*(30-$dat_alt), $posy_alt, ($rand_links+40)+$xperday*(30-$dat), $posxachse-($ypereuro*$rest)+($ypereuro*$minGuthaben), $schwarz);
                     $posy_alt = $posxachse-($ypereuro*$rest) + ($ypereuro*$minGuthaben);
                     $dat_alt = $dat;
                     $found = true;
                 }
               }
               if(!$found) {
                 imageline($diagrammLine, ($rand_links+40)+$xperday*(30-$dat_alt), $posy_alt, ($rand_links+40)+$xperday*(30-$dat), $posy_alt, $schwarz);
                 $dat_alt = $dat;
               }
           }
  ?>
  <div class="container">
  	      <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  	        <span class="navbar-brand">CashView - Statistik</span>
  	      </nav>

          <div class="card">
                        <div class="card-header"><h5 class="d-inline-block card-title">verf&uuml;gbar pro Tag</h5>
                        </div>
                        <div class="card-body">
                          <p class="card-text">
            	            <?php
                             echo("<b>".round($rest/$resttage,2)." €</b>");
                          ?>
            	            </p>
            	          </div>
            	      </div>
          <div class="card">
              <div class="card-header"><h5 class="d-inline-block card-title">letzte 30 Tage</h5>
              </div>
              <div class="card-body">
                <p class="card-text">
  	            <?php
  	               ob_start();
                   imagepng($diagramm30);
                   $imagedata = ob_get_clean();
                   echo("<img src=\"data:image/png;base64,".base64_encode($imagedata)."\">");
                ?>
  	            </p>
  	          </div>
  	      </div>
  	      <div class="card">
                        <div class="card-header"><h5 class="d-inline-block card-title">Gesamt</h5>
                        </div>
                        <div class="card-body">
                          <p class="card-text">
            	            <?php
                               ob_start();
                               imagepng($diagrammAll);
                               $imagedata = ob_get_clean();
                               echo("<img src=\"data:image/png;base64,".base64_encode($imagedata)."\">");
                            ?>
            	            </p>
            	          </div>
            	      </div>

                      <div class="card">
                          <div class="card-header"><h5 class="d-inline-block card-title">Verlauf (30 Tage)</h5>
                          </div>
                          <div class="card-body">
                            <p class="card-text">
              	            <?php
                                 ob_start();
                                 imagepng($diagrammLine);
                                 $imagedata = ob_get_clean();
                                 echo("<img src=\"data:image/png;base64,".base64_encode($imagedata)."\">");
                              ?>
              	            </p>
              	          </div>
              	      </div>

              	      <div class="card">
                        <div class="card-header"><h5 class="d-inline-block card-title">Aufteilung Kategorien (30 Tage)</h5>
                        </div>
                        <div class="card-body">
                          <p class="card-text">
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
                                  echo("<tr><td>".$row["kategorie"]."</td><td>".$row["wert"]."</td></tr>");
                               }
                            ?>
                            </tbody>
                            </table>
                            </p>
                          </div>
                      </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script>
    $(document).ready(function () {
      $('#KatTable').DataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "order": [[ 1, "asc" ]]
      });
    });
    </script>
</body>
</html>