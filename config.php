<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CashView - Konfiguration</title>
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
        }

        .navbar-brand {
            font-size: 1.1rem;
            margin-right: auto;
        }

        .btn-back {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }

        /* Tabs */
        .nav-tabs {
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #dee2e6;
        }

        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.75rem 1rem;
            font-weight: 600;
        }

        .nav-tabs .nav-link.active {
            color: #667eea;
            border-bottom-color: #667eea;
            background: transparent;
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

        /* Color Preview */
        .color-preview {
            width: 30px;
            height: 30px;
            border: 2px solid #ccc;
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
            border-radius: 4px;
        }

        /* Tabelle responsive */
        .table-responsive {
            font-size: 0.85rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            margin-bottom: 0;
        }

        .table td,
        .table th {
            padding: 0.75rem;
            vertical-align: middle;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
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

        input[type="color"] {
            height: 50px;
            cursor: pointer;
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Buttons */
        .btn-primary,
        .btn-success {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .btn-secondary {
            width: 100%;
            padding: 0.75rem;
            margin-top: 0.5rem;
        }

        /* Edit Cards versteckt */
        .edit-card {
            display: none;
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

            .navbar-brand {
                font-size: 1.3rem;
            }

            .btn-back {
                font-size: 0.95rem;
                padding: 0.5rem 1rem;
            }

            .table-responsive {
                font-size: 0.95rem;
            }

            .btn-primary,
            .btn-success {
                width: auto;
                min-width: 250px;
            }

            .btn-secondary {
                width: auto;
                min-width: 150px;
                margin-top: 0;
                margin-left: 0.5rem;
            }

            .color-preview {
                width: 40px;
                height: 40px;
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

            .table-responsive {
                font-size: 1rem;
            }

            .btn-primary,
            .btn-success {
                width: auto;
                min-width: 250px;
            }

            .btn-secondary {
                width: auto;
                min-width: 150px;
                margin-top: 0;
                margin-left: 0.5rem;
            }

            .action-buttons .btn {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }

            .color-preview {
                width: 40px;
                height: 40px;
            }
        }

        /* Touch-Optimierungen */
        @media (hover: none) and (pointer: coarse) {
            .btn {
                min-height: 44px;
                min-width: 44px;
            }

            .form-control {
                min-height: 44px;
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

            .form-control {
                background-color: #2a2a2a;
                color: #ffffff;
                border-color: #444;
            }

            .form-control:focus {
                background-color: #2a2a2a;
                color: #ffffff;
                border-color: #667eea;
            }

            input[type="color"] {
                background-color: #2a2a2a;
                border-color: #444;
            }

            .form-group label {
                color: #ffffff;
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

            .text-muted {
                color: #aaaaaa !important;
            }

            .color-preview {
                border-color: #555;
            }

            .nav-tabs {
                border-bottom-color: #444;
            }

            .nav-tabs .nav-link {
                color: #aaaaaa;
            }

            .nav-tabs .nav-link.active {
                color: #667eea;
            }
        }
    </style>
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mandant = -1;
if(isset($_POST["manId"])) {
    $mandant = $_POST["manId"];
} elseif(isset($_GET["manId"])) {
    $mandant = $_GET["manId"];
} else {
    echo("Mandanten-ID nicht übergeben");
    echo("</body></html>");
    return;
}

($GLOBALS["___mysqli_ston"] = mysqli_connect("192.168.5.103", "cashview", "cash123", "cashview"))
    or die("ERROR connecting to database.");

$success_message = "";
$error_message = "";

// ========== KATEGORIEN ==========
if(isset($_POST["action"]) && $_POST["action"] == "add_kategorie") {
    $bez = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST["bez"]);
    $sortorder = intval($_POST["sortorder"]);
    $color_r = intval($_POST["color_r"]);
    $color_g = intval($_POST["color_g"]);
    $color_b = intval($_POST["color_b"]);
    $statscolor = "$color_r,$color_g,$color_b";

    $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT MAX(ID) as maxid FROM kategorien");
    $row = mysqli_fetch_assoc($result);
    $new_id = $row["maxid"] + 1;

    $insert = "INSERT INTO kategorien (ID, bez, sortorder, statscolor, manId)
               VALUES ($new_id, '$bez', $sortorder, '$statscolor', $mandant)";
    mysqli_query($GLOBALS["___mysqli_ston"], $insert)
        or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));

    $success_message = "✅ Kategorie erfolgreich hinzugefügt!";
}

if(isset($_POST["action"]) && $_POST["action"] == "edit_kategorie") {
    $id = intval($_POST["id"]);
    $bez = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST["bez"]);
    $sortorder = intval($_POST["sortorder"]);
    $color_r = intval($_POST["color_r"]);
    $color_g = intval($_POST["color_g"]);
    $color_b = intval($_POST["color_b"]);
    $statscolor = "$color_r,$color_g,$color_b";

    $update = "UPDATE kategorien
               SET bez = '$bez', sortorder = $sortorder, statscolor = '$statscolor'
               WHERE ID = $id AND manId = $mandant";
    mysqli_query($GLOBALS["___mysqli_ston"], $update)
        or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));

    $success_message = "✅ Kategorie erfolgreich aktualisiert!";
}

if(isset($_POST["action"]) && $_POST["action"] == "delete_kategorie") {
    $id = intval($_POST["id"]);

    $check = "SELECT COUNT(*) as cnt FROM transaktionen WHERE katID = $id AND manId = $mandant";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $check);
    $row = mysqli_fetch_assoc($result);

    if($row["cnt"] > 0) {
        $error_message = "❌ Kategorie kann nicht gelöscht werden, da Transaktionen existieren!";
    } else {
        $delete = "DELETE FROM kategorien WHERE ID = $id AND manId = $mandant";
        mysqli_query($GLOBALS["___mysqli_ston"], $delete)
            or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));
        $success_message = "✅ Kategorie erfolgreich gelöscht!";
    }
}

// ========== KONTEN ==========
if(isset($_POST["action"]) && $_POST["action"] == "add_konto") {
    $bez = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST["konto_bez"]);
    $grenze = floatval($_POST["konto_grenze"]);
    $initialbetrag = floatval($_POST["konto_initial"]);

    $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT MAX(id) as maxid FROM Konten");
    $row = mysqli_fetch_assoc($result);
    $new_id = $row["maxid"] + 1;

    $insert = "INSERT INTO Konten (id, Bez, Grenze, manId)
               VALUES ($new_id, '$bez', '$grenze', $mandant)";
    mysqli_query($GLOBALS["___mysqli_ston"], $insert)
        or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));

    $result_init = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT MAX(initId) as maxid FROM Initialwerte");
    $row_init = mysqli_fetch_assoc($result_init);
    $new_init_id = $row_init["maxid"] + 1;

    $insert_init = "INSERT INTO Initialwerte (initId, Betrag, KtoId)
                   VALUES ($new_init_id, $initialbetrag, $new_id)";
    mysqli_query($GLOBALS["___mysqli_ston"], $insert_init)
        or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));

    $success_message = "✅ Konto erfolgreich hinzugefügt!";
}

if(isset($_POST["action"]) && $_POST["action"] == "edit_konto") {
    $id = intval($_POST["konto_id"]);
    $bez = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST["konto_bez"]);
    $grenze = floatval($_POST["konto_grenze"]);

    $update = "UPDATE Konten
               SET Bez = '$bez', Grenze = '$grenze'
               WHERE id = $id AND manId = $mandant";
    mysqli_query($GLOBALS["___mysqli_ston"], $update)
        or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));

    $success_message = "✅ Konto erfolgreich aktualisiert!";
}

if(isset($_POST["action"]) && $_POST["action"] == "delete_konto") {
    $id = intval($_POST["konto_id"]);

    $check = "SELECT COUNT(*) as cnt FROM transaktionen WHERE KtoID = $id AND manId = $mandant";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $check);
    $row = mysqli_fetch_assoc($result);

    if($row["cnt"] > 0) {
        $error_message = "❌ Konto kann nicht gelöscht werden, da Transaktionen existieren!";
    } else {
        mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM Initialwerte WHERE KtoId = $id");
        mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM Konten WHERE id = $id AND manId = $mandant");
        $success_message = "✅ Konto erfolgreich gelöscht!";
    }
}

// ========== LAUFENDE KOSTEN ==========
if(isset($_POST["action"]) && $_POST["action"] == "add_laufend") {
    $wert = floatval($_POST["laufend_wert"]);
    $ktoID = intval($_POST["laufend_konto"]);
    $katID = intval($_POST["laufend_kategorie"]);
    $modulo = intval($_POST["laufend_modulo"]);
    $beschreibung = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST["laufend_beschreibung"]);

    $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT MAX(id) as maxid FROM laufendes");
    $row = mysqli_fetch_assoc($result);
    $new_id = ($row["maxid"] ? $row["maxid"] : 0) + 1;

    $insert = "INSERT INTO laufendes (id, Wert, ktoID, katID, modulo, Beschreibung, manId)
               VALUES ($new_id, $wert, $ktoID, $katID, $modulo, '$beschreibung', $mandant)";
    mysqli_query($GLOBALS["___mysqli_ston"], $insert)
        or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));

    $success_message = "✅ Laufende Kosten erfolgreich hinzugefügt!";
}

if(isset($_POST["action"]) && $_POST["action"] == "delete_laufend") {
    $id = intval($_POST["laufend_id"]);

    $delete = "DELETE FROM laufendes WHERE id = $id AND manId = $mandant";
    mysqli_query($GLOBALS["___mysqli_ston"], $delete)
        or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));

    $success_message = "✅ Laufende Kosten erfolgreich gelöscht!";
}
?>

<div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <?php echo("<span class=\"navbar-brand\">CashView - Konfiguration</span>
                    <a class=\"btn btn-secondary btn-back\" href=\"index.php?manId=$mandant\" role=\"button\">Zurück</a>"); ?>
    </nav>

    <?php if($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="configTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="kategorien-tab" data-toggle="tab" href="#kategorien" role="tab">📁 Kategorien</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="konten-tab" data-toggle="tab" href="#konten" role="tab">💳 Konten</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="laufend-tab" data-toggle="tab" href="#laufend" role="tab">🔄 Laufende Kosten</a>
        </li>
    </ul>

    <div class="tab-content" id="configTabsContent">
        <!-- ========== TAB: KATEGORIEN ========== -->
        <div class="tab-pane fade show active" id="kategorien" role="tabpanel">
            <!-- Bestehende Kategorien -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Meine Kategorien</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Kategorien für Mandant <?php echo $mandant; ?></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Farbe</th>
                                    <th>Bezeichnung</th>
                                    <th>Sortierung</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT ID, bez, sortorder, statscolor, manId
                                          FROM kategorien
                                          WHERE (manId = $mandant OR manId = 0) AND sortorder <> 999
                                          ORDER BY sortorder";
                                $result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
                                    or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));

                                while($row = mysqli_fetch_assoc($result)) {
                                    $color = explode(",", $row["statscolor"]);
                                    $rgb = "rgb(".$color[0].",".$color[1].",".$color[2].")";
                                    $is_own = ($row["manId"] == $mandant);

                                    echo("<tr>");
                                    echo("<td><div class=\"color-preview\" style=\"background-color: $rgb;\"></div></td>");
                                    echo("<td>".$row["bez"].($is_own ? "" : " <span class=\"text-muted\">(global)</span>")."</td>");
                                    echo("<td>".$row["sortorder"]."</td>");
                                    echo("<td>");

                                    if($is_own) {
                                        echo("<div class=\"action-buttons\">");
                                        echo("<button class=\"btn btn-sm btn-primary\" onclick=\"editKategorie(".$row["ID"].", '".$row["bez"]."', ".$row["sortorder"].", ".$color[0].", ".$color[1].", ".$color[2].")\">Bearbeiten</button>");
                                        echo("<button class=\"btn btn-sm btn-danger\" onclick=\"deleteKategorie(".$row["ID"].")\">Löschen</button>");
                                        echo("</div>");
                                    } else {
                                        echo("<span class=\"text-muted\">Nicht bearbeitbar</span>");
                                    }

                                    echo("</td>");
                                    echo("</tr>");
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Neues Konto -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Neues Konto hinzufügen</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php">
                        <input type="hidden" name="manId" value="<?php echo $mandant; ?>">
                        <input type="hidden" name="action" value="add_konto">

                        <div class="form-group">
                            <label>Kontobezeichnung</label>
                            <input type="text" class="form-control" name="konto_bez" placeholder="z.B. Girokonto" required>
                        </div>

                        <div class="form-group">
                            <label>Anfangsbestand</label>
                            <input type="number" step="0.01" class="form-control" name="konto_initial" placeholder="0.00" required>
                            <small class="form-text text-muted">Aktueller Kontostand zum Zeitpunkt der Einrichtung</small>
                        </div>

                        <div class="form-group">
                            <label>Dispogrenze</label>
                            <input type="number" step="0.01" class="form-control" name="konto_grenze" value="0" required>
                            <small class="form-text text-muted">Betrag, um den das Konto überzogen werden kann</small>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Konto hinzufügen</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Konto -->
            <div class="card edit-card" id="editKontoCard">
                <div class="card-header">
                    <h5 class="card-title">Konto bearbeiten</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php">
                        <input type="hidden" name="manId" value="<?php echo $mandant; ?>">
                        <input type="hidden" name="action" value="edit_konto">
                        <input type="hidden" name="konto_id" id="edit_konto_id">

                        <div class="form-group">
                            <label>Kontobezeichnung</label>
                            <input type="text" class="form-control" name="konto_bez" id="edit_konto_bez" required>
                        </div>

                        <div class="form-group">
                            <label>Dispogrenze</label>
                            <input type="number" step="0.01" class="form-control" name="konto_grenze" id="edit_konto_grenze" required>
                        </div>

                        <div class="alert alert-info">
                            ℹ️ Der Anfangsbestand kann nicht geändert werden, da dies die Finanzhistorie verfälschen würde.
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                            <button type="button" class="btn btn-secondary" onclick="cancelEditKonto()">Abbrechen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========== TAB: LAUFENDE KOSTEN ========== -->
        <div class="tab-pane fade" id="laufend" role="tabpanel">
            <!-- Bestehende laufende Kosten -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Meine laufenden Kosten</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Wiederkehrende Ausgaben</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Beschreibung</th>
                                    <th>Betrag</th>
                                    <th>Intervall</th>
                                    <th>Konto</th>
                                    <th>Kategorie</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT l.id, l.Wert, l.modulo, l.Beschreibung, k.Bez as KontoBez, kat.bez as KatBez
                                          FROM laufendes l
                                          LEFT JOIN Konten k ON l.ktoID = k.id
                                          LEFT JOIN kategorien kat ON l.katID = kat.ID
                                          WHERE l.manId = $mandant";
                                $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);

                                $intervalle = array(
                                    1 => "Täglich",
                                    7 => "Wöchentlich",
                                    14 => "14-tägig",
                                    30 => "Monatlich",
                                    90 => "Quartalsweise",
                                    365 => "Jährlich"
                                );

                                while($row = mysqli_fetch_assoc($result)) {
                                    $intervall = isset($intervalle[$row["modulo"]]) ? $intervalle[$row["modulo"]] : $row["modulo"]." Tage";

                                    echo("<tr>");
                                    echo("<td>".$row["Beschreibung"]."</td>");
                                    echo("<td>".number_format($row["Wert"], 2, ',', '.')." €</td>");
                                    echo("<td>".$intervall."</td>");
                                    echo("<td>".$row["KontoBez"]."</td>");
                                    echo("<td>".$row["KatBez"]."</td>");
                                    echo("<td>");
                                    echo("<button class=\"btn btn-sm btn-danger\" onclick=\"deleteLaufend(".$row["id"].")\">Löschen</button>");
                                    echo("</td>");
                                    echo("</tr>");
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Neue laufende Kosten -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Neue laufende Kosten hinzufügen</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php">
                        <input type="hidden" name="manId" value="<?php echo $mandant; ?>">
                        <input type="hidden" name="action" value="add_laufend">

                        <div class="form-group">
                            <label>Beschreibung</label>
                            <input type="text" class="form-control" name="laufend_beschreibung" placeholder="z.B. Netflix Abo" required>
                        </div>

                        <div class="form-group">
                            <label>Betrag</label>
                            <input type="number" step="0.01" class="form-control" name="laufend_wert" placeholder="0.00" required>
                        </div>

                        <div class="form-group">
                            <label>Intervall</label>
                            <select class="form-control" name="laufend_modulo" required>
                                <option value="1">Täglich</option>
                                <option value="7">Wöchentlich</option>
                                <option value="14">14-tägig</option>
                                <option value="30" selected>Monatlich</option>
                                <option value="90">Quartalsweise</option>
                                <option value="365">Jährlich</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Konto</label>
                            <select class="form-control" name="laufend_konto" required>
                                <?php
                                $query = "SELECT id, Bez FROM Konten WHERE manId = $mandant";
                                $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo("<option value=\"".$row["id"]."\">".$row["Bez"]."</option>");
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kategorie</label>
                            <select class="form-control" name="laufend_kategorie" required>
                                <?php
                                $query = "SELECT ID, bez FROM kategorien WHERE (manId = 0 OR manId = $mandant) AND sortorder <> 999 ORDER BY sortorder";
                                $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo("<option value=\"".$row["ID"]."\">".$row["bez"]."</option>");
                                }
                                ?>
                            </select>
                        </div>

                        <div class="alert alert-info">
                            ℹ️ <strong>Hinweis:</strong> Laufende Kosten werden automatisch am Monatsersten gebucht, wenn das Intervall (Modulo) zum aktuellen Monat passt. Monatliche Kosten (30 Tage) werden jeden Monat gebucht, quartalsweise (90 Tage) alle 3 Monate, etc.
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Laufende Kosten hinzufügen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
// Colorpicker für Kategorien
document.getElementById('colorpicker').addEventListener('input', function(e) {
    const hex = e.target.value;
    const r = parseInt(hex.substr(1,2), 16);
    const g = parseInt(hex.substr(3,2), 16);
    const b = parseInt(hex.substr(5,2), 16);

    document.getElementById('color_r').value = r;
    document.getElementById('color_g').value = g;
    document.getElementById('color_b').value = b;
});

document.getElementById('edit_kat_colorpicker').addEventListener('input', function(e) {
    const hex = e.target.value;
    const r = parseInt(hex.substr(1,2), 16);
    const g = parseInt(hex.substr(3,2), 16);
    const b = parseInt(hex.substr(5,2), 16);

    document.getElementById('edit_kat_color_r').value = r;
    document.getElementById('edit_kat_color_g').value = g;
    document.getElementById('edit_kat_color_b').value = b;
});

// Kategorien-Funktionen
function editKategorie(id, bez, sortorder, r, g, b) {
    document.getElementById('edit_kat_id').value = id;
    document.getElementById('edit_kat_bez').value = bez;
    document.getElementById('edit_kat_sortorder').value = sortorder;
    document.getElementById('edit_kat_color_r').value = r;
    document.getElementById('edit_kat_color_g').value = g;
    document.getElementById('edit_kat_color_b').value = b;

    const hex = '#' +
        ('0' + r.toString(16)).slice(-2) +
        ('0' + g.toString(16)).slice(-2) +
        ('0' + b.toString(16)).slice(-2);
    document.getElementById('edit_kat_colorpicker').value = hex;

    document.getElementById('editKategorieCard').style.display = 'block';
    document.getElementById('editKategorieCard').scrollIntoView({ behavior: 'smooth' });
}

function cancelEditKategorie() {
    document.getElementById('editKategorieCard').style.display = 'none';
    document.getElementById('editKategorieForm').reset();
}

function deleteKategorie(id) {
    if(confirm('Kategorie wirklich löschen?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'config.php';

        const inputManId = document.createElement('input');
        inputManId.type = 'hidden';
        inputManId.name = 'manId';
        inputManId.value = <?php echo $mandant; ?>;
        form.appendChild(inputManId);

        const inputAction = document.createElement('input');
        inputAction.type = 'hidden';
        inputAction.name = 'action';
        inputAction.value = 'delete_kategorie';
        form.appendChild(inputAction);

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'id';
        inputId.value = id;
        form.appendChild(inputId);

        document.body.appendChild(form);
        form.submit();
    }
}

// Konten-Funktionen
function editKonto(id, bez, grenze) {
    document.getElementById('edit_konto_id').value = id;
    document.getElementById('edit_konto_bez').value = bez;
    document.getElementById('edit_konto_grenze').value = grenze;

    document.getElementById('editKontoCard').style.display = 'block';
    document.getElementById('editKontoCard').scrollIntoView({ behavior: 'smooth' });
}

function cancelEditKonto() {
    document.getElementById('editKontoCard').style.display = 'none';
}

function deleteKonto(id) {
    if(confirm('Konto wirklich löschen? Alle zugehörigen Initialwerte werden ebenfalls gelöscht!')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'config.php';

        const inputManId = document.createElement('input');
        inputManId.type = 'hidden';
        inputManId.name = 'manId';
        inputManId.value = <?php echo $mandant; ?>;
        form.appendChild(inputManId);

        const inputAction = document.createElement('input');
        inputAction.type = 'hidden';
        inputAction.name = 'action';
        inputAction.value = 'delete_konto';
        form.appendChild(inputAction);

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'konto_id';
        inputId.value = id;
        form.appendChild(inputId);

        document.body.appendChild(form);
        form.submit();
    }
}

// Laufende Kosten-Funktionen
function deleteLaufend(id) {
    if(confirm('Laufende Kosten wirklich löschen?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'config.php';

        const inputManId = document.createElement('input');
        inputManId.type = 'hidden';
        inputManId.name = 'manId';
        inputManId.value = <?php echo $mandant; ?>;
        form.appendChild(inputManId);

        const inputAction = document.createElement('input');
        inputAction.type = 'hidden';
        inputAction.name = 'action';
        inputAction.value = 'delete_laufend';
        form.appendChild(inputAction);

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'laufend_id';
        inputId.value = id;
        form.appendChild(inputId);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</body>
</html>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Neue Kategorie -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Neue Kategorie hinzufügen</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php">
                        <input type="hidden" name="manId" value="<?php echo $mandant; ?>">
                        <input type="hidden" name="action" value="add_kategorie">

                        <div class="form-group">
                            <label for="bez">Bezeichnung</label>
                            <input type="text" class="form-control" name="bez" required>
                        </div>

                        <div class="form-group">
                            <label for="sortorder">Sortierung</label>
                            <input type="number" class="form-control" name="sortorder" value="10" required>
                        </div>

                        <div class="form-group">
                            <label for="colorpicker">Farbe</label>
                            <input type="color" class="form-control" id="colorpicker" value="#6464ff">
                            <input type="hidden" name="color_r" id="color_r" value="100">
                            <input type="hidden" name="color_g" id="color_g" value="100">
                            <input type="hidden" name="color_b" id="color_b" value="255">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Kategorie hinzufügen</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Kategorie -->
            <div class="card edit-card" id="editKategorieCard">
                <div class="card-header">
                    <h5 class="card-title">Kategorie bearbeiten</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php" id="editKategorieForm">
                        <input type="hidden" name="manId" value="<?php echo $mandant; ?>">
                        <input type="hidden" name="action" value="edit_kategorie">
                        <input type="hidden" name="id" id="edit_kat_id">

                        <div class="form-group">
                            <label>Bezeichnung</label>
                            <input type="text" class="form-control" name="bez" id="edit_kat_bez" required>
                        </div>

                        <div class="form-group">
                            <label>Sortierung</label>
                            <input type="number" class="form-control" name="sortorder" id="edit_kat_sortorder" required>
                        </div>

                        <div class="form-group">
                            <label>Farbe</label>
                            <input type="color" class="form-control" id="edit_kat_colorpicker">
                            <input type="hidden" name="color_r" id="edit_kat_color_r">
                            <input type="hidden" name="color_g" id="edit_kat_color_g">
                            <input type="hidden" name="color_b" id="edit_kat_color_b">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                            <button type="button" class="btn btn-secondary" onclick="cancelEditKategorie()">Abbrechen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========== TAB: KONTEN ========== -->
        <div class="tab-pane fade" id="konten" role="tabpanel">
            <!-- Bestehende Konten -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Meine Konten</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Konto</th>
                                    <th>Anfangsbestand</th>
                                    <th>Dispogrenze</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT k.id, k.Bez, k.Grenze, i.Betrag
                                          FROM Konten k
                                          LEFT JOIN Initialwerte i ON k.id = i.KtoId
                                          WHERE k.manId = $mandant";
                                $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);

                                while($row = mysqli_fetch_assoc($result)) {
                                    echo("<tr>");
                                    echo("<td>".$row["Bez"]."</td>");
                                    echo("<td>".number_format($row["Betrag"] ? $row["Betrag"] : 0, 2, ',', '.')." €</td>");
                                    echo("<td>".number_format($row["Grenze"], 2, ',', '.')." €</td>");
                                    echo("<td>");
                                    echo("<div class=\"action-buttons\">");
                                    echo("<button class=\"btn btn-sm btn-primary\" onclick=\"editKonto(".$row["id"].", '".$row["Bez"]."', ".$row["Grenze"].")\">Bearbeiten</button>");
                                    echo("<button class=\"btn btn-sm btn-danger\" onclick=\"deleteKonto(".$row["id"].")\">Löschen</button>");
                                    echo("</div>");
                                    echo("</td>");
                                    echo("</tr>");
                                }
                                ?>