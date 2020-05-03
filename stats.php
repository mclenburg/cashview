<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CashView - Die Finanz&uuml;bersicht</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="http://192.168.5.103/cashview/favicon.ico" rel="shortcut icon">
    <link rel="icon" href="http://192.168.5.103/cashview/favicon.ico" type="image/ico">
</head>
<body>
  <?php
             error_reporting(E_ALL);
             ini_set('display_errors', 1);

  	       $name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  		   ($GLOBALS["___mysqli_ston"] = mysqli_connect("192.168.5.103",  "cashview",  "cash123", "cashview"))  or die("ERROR connecting to database.");

  		   $queryAll = "select sum(trans.wert) summe, kat.bez from transaktionen trans left outer join kategorien kat on trans.katID = kat.ID where wert > 0 group by katID";
  		   $query30 = "select sum(trans.wert) summe, kat.bez from transaktionen trans left outer join kategorien kat on trans.katID = kat.ID where wert > 0 and trans.Datum > DATE_SUB(CURRENT_DATE(),INTERVAL 30 DAY) group by katID";

           $resultAll = mysqli_query($GLOBALS["___mysqli_ston"], $queryAll)or die("$queryAll " .mysqli_error($GLOBALS["___mysqli_ston"]));
           $result30 = mysqli_query($GLOBALS["___mysqli_ston"], $query30)or die("$query30 " .mysqli_error($GLOBALS["___mysqli_ston"]));

           while( $row = mysqli_fetch_assoc( $resultAll)){
               $arrayAll[$row["bez"]] = $row["summe"];
           }
           while( $row = mysqli_fetch_assoc( $result30)){
               $array30[$row["bez"]] = $row["summe"];
           }

           $breite = 400;
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

           $schwarz = imagecolorallocate($diagrammAll, 0, 0, 0);
           $weiss = imagecolorallocate($diagrammAll, 255, 255, 255);
           $schwarz30 = imagecolorallocate($diagramm30, 0, 0, 0);
           $weiss30 = imagecolorallocate($diagramm30, 255, 255, 255);

           $color1 = imagecolorallocate($diagrammAll, 2, 117, 216);
           $color2 = imagecolorallocate($diagrammAll, 92, 184, 92);
           $color3 = imagecolorallocate($diagrammAll, 91, 192, 222);
           $color4 = imagecolorallocate($diagrammAll, 240, 173, 78);
           $color5 = imagecolorallocate($diagrammAll, 217, 83, 79);

           $color6 = imagecolorallocate($diagrammAll, 253, 138, 39);
           $color7 = imagecolorallocate($diagrammAll, 142, 184, 40);
           $color8 = imagecolorallocate($diagrammAll, 154, 40, 184);
           $color9 = imagecolorallocate($diagrammAll, 0, 255, 0);
           $color10 = imagecolorallocate($diagrammAll, 0, 0, 255);

           imagefill($diagrammAll, 0, 0, $weiss);
           imagefill($diagramm30, 0, 0, $weiss);

           //arsort($arrayAll);
           $gesamtAll = array_sum($arrayAll);
           //arsort($array30);
           $gesamt30 = array_sum($array30);

           $i = 0;
           $winkel = 0;
           foreach($arrayAll as $key => $value)
           {
             $i++;
             $start = $winkel;
             $winkel = $start + $value*360/$gesamtAll;

             $color = "color".$i;
             imagesetthickness ( $diagrammAll , 3 );
             for($rad = 0; $rad <= 100; $rad++) {
               imagearc($diagrammAll, $start_x, $start_y, ($radius-$rad), ($radius-$rad), $start, $winkel, $$color);  //because gap
             }
             $unterkante = $rand_oben+$punktbreite+($i-1)*($punktbreite+$abstand);
             imagefilledrectangle($diagrammAll, $rand_links, $rand_oben+($i-1)*($punktbreite+$abstand), $rand_links+$punktbreite, $unterkante, $$color);
             imagettftext($diagrammAll, $schriftgroesse, 0, $rand_links+$punktbreite+5, $unterkante-$punktbreite/2+$schriftgroesse/2, $schwarz, "media/arial.ttf", $key." ".round($value*100/$gesamtAll, 1)." %");
           }

           $i = 0;
           $winkel = 0;
           foreach($array30 as $key => $value)
           {
             $i++;
             $start = $winkel;
             $winkel = $start + $value*360/$gesamt30;

             $color = "color".$i;
             imagesetthickness ( $diagramm30 , 3 );
             for($rad = 0; $rad <= 100; $rad++) {
               imagearc($diagramm30, $start_x, $start_y, ($radius-$rad), ($radius-$rad), $start, $winkel, $$color);  //because gap
             }
             $unterkante = $rand_oben+$punktbreite+($i-1)*($punktbreite+$abstand);
             imagefilledrectangle($diagramm30, $rand_links, $rand_oben+($i-1)*($punktbreite+$abstand), $rand_links+$punktbreite, $unterkante, $$color);
             imagettftext($diagramm30, $schriftgroesse, 0, $rand_links+$punktbreite+5, $unterkante-$punktbreite/2+$schriftgroesse/2, $schwarz, "media/arial.ttf", $key." ".round($value*100/$gesamt30, 1)." %");
           }

  ?>
  <div class="container">
  	      <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  	        <span class="navbar-brand">CashView - Statistik</span>
  	      </nav>

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
  </div>

</body>
</html>