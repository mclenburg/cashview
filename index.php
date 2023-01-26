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

           $mandant = -1;
           if(isset($_POST["manId"]))
           {
             $mandant = $_POST["manId"];
           }
           else {
             $mandant = $_GET["manId"];
           }
           if($mandant == -1) { echo("Fehlende Mandanten-ID."); return; }

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
	         	 	  <span style="font-size:30px;color:#ff0000;">Kein gültiger Betrag!</span>
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
	         	 	  <span style="font-size:30px;color:#ff0000;">Es fehlt der Betrag!</span>
	    <?php
	         	 }
	         }
	    ?>

	    <div class="container">
	      <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	        <?php echo("<span class=\"navbar-brand\">CashView</span><a class=\"btn btn-secondary d-inline-block float-right\" href=\"stats.php?manId=$mandant\" role=\"button\">Statistik</a>");
	        ?>
	      </nav>

	      <div class="card">
            <div class="card-header"><h5 class="d-inline-block card-title">aktueller Finanzstand</h5>
                                     <a class="d-inline-block float-right margin-top"  data-toggle="modal" data-target="#tankpreise" href="#">wo tanken?</a>
                                     <h6 class="card-subtitle mb-2 text-muted">verf&uuml;gbare Betr&auml;ge pro Konto</h6>

            </div>
            <div class="card-body">
              <p class="card-text">
                <table class="table-responsive">
                  <thead>
                    <tr>
                       <th scope="col">Konto</th>
                       <th scope="col">verf. Betrag</th>
                       <th scope="col">Dispo</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php
                      		     $query = "select Betrag, KtoID, Bez, Grenze from Initialwerte, Konten where Konten.id = KtoID and Konten.manId = $mandant";
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
                                 	 	   echo("<tr><td> " .$init_wert["Bez"] ."</td><td>". $plus ."</td><td>". $rest ."</td></tr>");

                                 }
                                 echo("<tr style=\"border-top:1px dotted gray;\"><td>Gesamt</td><td>". $plus_kum ."</td><td>". $rest_kum ."</td></tr>");
                             ?>
                  </tbody>
                </table>
              </p>
            </div>  <!-- card-body -->
          </div>  <!-- card -->
          <br>
          <div class="card">
            <div class="card-header"><h5 class="card-title">Buchungseintrag</h5>
            </div>
            <div class="card-body">
              <p class="card-text">
                <form method="POST" action="index.php">
                  <?php echo("<input type=\"hidden\" value=\"" .$mandant. "\" name=\"manId\" />");"
                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="betrag">Betrag</span>
                    </div>
                    <?php
                      $restTage = date("t")-date("d")+1;
                      $proTag = number_format(round($plus_kum/$restTage, 2), 2, ",", "");

                      echo("<input name=\"betrag\" type=\"text\" class=\"form-control\" placeholder=\"".$proTag."\" aria-label=\"Betrag\" aria-describedby=\"betrag\" onKeyUp=\"if((event.keyCode < 48 of event.keyCode > 57) and event.keyCode != 188 and event.keyCode != 190) this.value = this.value.substr(0, this.value.length-2);\">");
                  ?>
                  </div>
                  <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="zweck">Zweck</span>
                    </div>
                    <select name="zweck" size=1 aria-label="Zweck" aria-describedby="zweck">
                      <?php
                        $query = "select ID, Bez from kategorien where sortorder <> 999 order by sortorder";
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
                    <select name="konto" size=1  aria-label="Konto" aria-describedby="konto">
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
                  <div class="col-sm-12"><input type="submit" value="Speichern" class="btn btn-primary"></div>


                  </form>
              </p>
            </div>  <!-- card-body -->
          </div>  <!-- card -->

          <div class="modal" id="tankpreise">
            <div class="modal-dialog">
              <div class="modal-content">

                <div class="modal-header">
                  <h4 class="modal-title">aktuelle Benzinpreise</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                  <script type="text/javascript" src="https://maps.benzinpreis.de/bpimg/show_bpimg.php?code=g8RNi7pdOPv3VIuToBqiOzLGsnUCXQoS50623U5Fo1jWKcK7xyGyzlz3LSjV8nu6BWQ4PsjVeQOEo3RyhdnK%2FhpVO4HULpvZ0B9VPHCavnIVjEmuQ52%2Br1of9az4Vtd3HthJdOzXA0Cu8fWaZNnEQJuXrfjBcbW75oJPPludkukN9hqcJNKLAe7JWczNWgQlFSQuSDBUeGp7MQBsXXRayQG6igUCrdWQCUTUSzbnY2tU6yZH0CilzNlzdFDE1radH2VsoTZfT4W6zCE%2FEwVUvw%3D%3D"></script>
                </div>

              </div>
            </div>
          </div>
	    </div>  <!-- container-fluid -->

        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	</body>
</html>
