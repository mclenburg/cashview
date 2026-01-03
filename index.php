<!DOCTYPE html> 
<html lang="de">
	<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>CashView - Die Finanz&uuml;bersicht</title>
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

            /* Error/Success Messages */
            .error-message {
                font-size: 1.2rem;
                color: #ff0000;
                padding: 1rem;
                text-align: center;
                margin-bottom: 1rem;
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

                /* Zwei-Spalten Layout für Desktop */
                .desktop-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 1.5rem;
                }
            }

            /* Touch-Optimierungen */
            @media (hover: none) and (pointer: coarse) {
                .btn {
                    min-height: 44px;
                    min-width: 44px;
                }

                .form-control,
                select {
                    min-height: 44px;
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

                .card-title {
                    color: #ffffff;
                }

                .card-subtitle {
                    color: #aaaaaa !important;
                }

                .table-responsive {
                    color: #ffffff;
                }

                .table-responsive th {
                    color: #ffffff;
                    border-color: #444;
                }

                .table-responsive td {
                    color: #e0e0e0;
                    border-color: #444;
                }

                .form-control,
                select.form-control {
                    background-color: #2a2a2a;
                    color: #ffffff;
                    border-color: #444;
                }

                .form-control::placeholder {
                    color: #888;
                }

                .input-group-text {
                    background-color: #2a2a2a;
                    color: #ffffff;
                    border-color: #444;
                }

                .modal-content {
                    background-color: #1e1e1e;
                    color: #ffffff;
                }

                .modal-header {
                    background-color: #2a2a2a;
                    border-bottom-color: #444;
                }

                .modal-title {
                    color: #ffffff;
                }

                .close {
                    color: #ffffff;
                    text-shadow: none;
                }

                .tankpreise-link {
                    color: #6ea8fe;
                }

                .error-message {
                    color: #ff6b6b;
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

	       $name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		   ($GLOBALS["___mysqli_ston"] = mysqli_connect("192.168.5.103",  "cashview",  "cash123", "cashview"))  or die("ERROR connecting to database.");

	         if(isset($_POST["betrag"]))
	         {
	         	 $Betrag = str_replace(",", ".", $_POST["betrag"]);
	         	 $Konto  = $_POST["konto"];
	         	 $Zweck  = $_POST["zweck"];

	         	 if($Betrag != null)
	         	 {
	         	 	 $kommas = strlen($Betrag) - strlen(str_replace(".", "", $Betrag));
	         	 	 if($kommas > 1)
	         	 	 {
	    ?>
	         	 	  <div class="error-message">Kein gültiger Betrag!</div>
	    <?php
	         	 	 }
	         	 	 else
	         	 	 {
	         	     $insert = "INSERT INTO transaktionen (Wert, Datum, KtoID, katID, manId) VALUES ($Betrag, now(), $Konto, $Zweck, $mandant)";
	         	     mysqli_query($GLOBALS["___mysqli_ston"], $insert) or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));
	         	   }
	         	 }
	         	 else
	         	 {
	    ?>
	         	 	  <div class="error-message">Es fehlt der Betrag!</div>
	    <?php
	         	 }
	         }
	    ?>

	    <div class="container">
	      <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	        <?php echo("<span class=\"navbar-brand\">CashView</span>
                       <a class=\"btn btn-secondary d-inline-block\" href=\"stats.php?manId=$mandant\" role=\"button\">Statistik</a>
                       <a class=\"btn btn-secondary d-inline-block\" href=\"config.php?manId=$mandant\" role=\"button\">Konfiguration</a>");
	        ?>
	      </nav>

	      <div class="card">
            <div class="card-header">
                <h5 class="d-inline-block card-title">Aktueller Finanzstand</h5>
                <a class="d-inline-block float-right tankpreise-link" data-toggle="modal" data-target="#tankpreise" href="#">wo tanken?</a>
                <h6 class="card-subtitle mb-2 text-muted">Verfügbare Beträge pro Konto</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                       <th scope="col">Konto</th>
                       <th scope="col">Verf. Betrag</th>
                       <th scope="col">Dispo</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php
                      		     $query = "select Betrag, KtoID, Bez, Grenze from Initialwerte inner join Konten on Konten.id = KtoID where Konten.manId = $mandant";
                                 $result = mysqli_query($GLOBALS["___mysqli_ston"], $query)or die("$query " .mysqli_error($GLOBALS["___mysqli_ston"]));
                                 $plus_kum = 0;
                                 $rest_kum = 0;
                                 while($init_wert = mysqli_fetch_assoc($result))
                                 {
                                 	 $stand = $init_wert["Betrag"];
                                 	 $query = "select Wert from transaktionen where KtoID = " .$init_wert["KtoID"] . " and manId = $mandant";
                                 	 $result_inner = mysqli_query($GLOBALS["___mysqli_ston"], $query) OR die("Error: $query " .mysqli_error($GLOBALS["___mysqli_ston"]));
                                 	 $stand = $init_wert["Betrag"];
                                 	 while($trans_row = mysqli_fetch_assoc($result_inner))
                                 	 {
                                 	 	 $stand = ($stand - $trans_row["Wert"]);

                                 	 }

                                 	 	   if($stand <= 0)
                                 	 	   {
                                 	 	   	 $plus = 0;
                                 	 	   }
                                 	 	   else
                                 	 	   {
                                 	 	   	 $plus = $stand;

                                 	 	   }

                                 	 	   if($stand > 0) {
                                 	 	     $rest = 0 - $init_wert["Grenze"];
                                 	 	   }
                                 	 	   else {
                                 	 	     $rest = $stand - $init_wert["Grenze"];
                                 	 	   }

                                 	 	   $rest_kum = ($rest_kum + $rest);
                                 	 	   $plus_kum = ($plus_kum + $plus);
                                 	 	   echo("<tr><td>" .$init_wert["Bez"] ."</td><td>". number_format($plus, 2, ',', '.') ." €</td><td>". number_format($rest, 2, ',', '.') ." €</td></tr>");

                                 }
                                 echo("<tr style=\"border-top:2px solid #dee2e6; font-weight: bold;\"><td>Gesamt</td><td>". number_format($plus_kum, 2, ',', '.') ." €</td><td>". number_format($rest_kum, 2, ',', '.') ." €</td></tr>");
                             ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
                <h5 class="card-title">Buchungseintrag</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php">
                  <?php echo("<input type=\"hidden\" value=\"" .$mandant. "\" name=\"manId\" />"); ?>

                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="betrag">Betrag</span>
                    </div>
                    <?php
                      $restTage = date("t")-date("d")+1;
                      $proTag = number_format(round($plus_kum/$restTage, 2), 2, ",", "");
                      $proTagDispo = number_format(round($rest_kum/$restTage, 2), 2, ",", "");

                      if($proTagDispo != $proTag) {
                        echo("<input name=\"betrag\" type=\"text\" class=\"form-control\" placeholder=\"".$proTag." (".$proTagDispo.")\" aria-label=\"Betrag\" aria-describedby=\"betrag\">");
                      }
                      else {
                        echo("<input name=\"betrag\" type=\"text\" class=\"form-control\" placeholder=\"".$proTag."\" aria-label=\"Betrag\" aria-describedby=\"betrag\">");
                      }
                  ?>
                  </div>

                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="zweck">Zweck</span>
                    </div>
                    <select name="zweck" class="form-control" aria-label="Zweck" aria-describedby="zweck">
                      <?php
                        $query = "select ID, Bez from kategorien where (manId = 0 OR manId = $mandant) AND sortorder <> 999 order by sortorder";
                        $result = mysqli_query($GLOBALS["___mysqli_ston"], $query) or die("ERROR: " .mysqli_error($GLOBALS["___mysqli_ston"]));
                        while($kat_row = mysqli_fetch_assoc($result))
                        {
                          echo("<option value=\"".$kat_row["ID"]."\">".$kat_row["Bez"]."</option>\n");
                        }
                      ?>
                    </select>
                  </div>

                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="konto">Konto</span>
                    </div>
                    <select name="konto" class="form-control" aria-label="Konto" aria-describedby="konto">
                     <?php
                       $query = "select ID, Bez from Konten where manId = $mandant";
                       $result = mysqli_query($GLOBALS["___mysqli_ston"], $query) or die("ERROR: " .mysqli_error($GLOBALS["___mysqli_ston"]));
                       while($kat_row = mysqli_fetch_assoc($result))
                       {
                         echo("<option value=\"".$kat_row["ID"]."\">".$kat_row["Bez"]."</option>");
                       }
                     ?>
                    </select>
                  </div>

                  <div class="text-center">
                      <input type="submit" value="Speichern" class="btn btn-primary">
                  </div>
                </form>
            </div>
          </div>

          <div class="modal fade" id="tankpreise" tabindex="-1" role="dialog" aria-labelledby="tankpreiseLabel" aria-hidden="true">
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

        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	</body>
</html>