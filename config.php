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
        .color-preview {
            width: 30px;
            height: 30px;
            border: 1px solid #ccc;
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
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

// Kategorie hinzufügen
if(isset($_POST["action"]) && $_POST["action"] == "add") {
    $bez = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST["bez"]);
    $sortorder = intval($_POST["sortorder"]);
    $color_r = intval($_POST["color_r"]);
    $color_g = intval($_POST["color_g"]);
    $color_b = intval($_POST["color_b"]);
    $statscolor = "$color_r,$color_g,$color_b";

    // Höchste ID ermitteln
    $result = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT MAX(ID) as maxid FROM kategorien");
    $row = mysqli_fetch_assoc($result);
    $new_id = $row["maxid"] + 1;

    $insert = "INSERT INTO kategorien (ID, bez, sortorder, statscolor, manId)
               VALUES ($new_id, '$bez', $sortorder, '$statscolor', $mandant)";
    mysqli_query($GLOBALS["___mysqli_ston"], $insert)
        or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));

    echo('<div class="alert alert-success">Kategorie erfolgreich hinzugefügt!</div>');
}

// Kategorie bearbeiten
if(isset($_POST["action"]) && $_POST["action"] == "edit") {
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

    echo('<div class="alert alert-success">Kategorie erfolgreich aktualisiert!</div>');
}

// Kategorie löschen
if(isset($_POST["action"]) && $_POST["action"] == "delete") {
    $id = intval($_POST["id"]);

    // Prüfen ob Transaktionen existieren
    $check = "SELECT COUNT(*) as cnt FROM transaktionen WHERE katID = $id AND manId = $mandant";
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $check);
    $row = mysqli_fetch_assoc($result);

    if($row["cnt"] > 0) {
        echo('<div class="alert alert-danger">Kategorie kann nicht gelöscht werden, da Transaktionen existieren!</div>');
    } else {
        $delete = "DELETE FROM kategorien WHERE ID = $id AND manId = $mandant";
        mysqli_query($GLOBALS["___mysqli_ston"], $delete)
            or die("ERROR: ".mysqli_error($GLOBALS["___mysqli_ston"]));
        echo('<div class="alert alert-success">Kategorie erfolgreich gelöscht!</div>');
    }
}
?>

<div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <?php echo("<span class=\"navbar-brand\">CashView - Konfiguration</span>
                    <a class=\"btn btn-secondary d-inline-block float-right\" href=\"index.php?manId=$mandant\" role=\"button\">Zurück</a>"); ?>
    </nav>

    <!-- Bestehende Kategorien -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title">Meine Kategorien</h5>
            <h6 class="card-subtitle mb-2 text-muted">Kategorien für Mandant <?php echo $mandant; ?></h6>
        </div>
        <div class="card-body">
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
                        echo("<td>".$row["bez"].($is_own ? "" : " (global)")."</td>");
                        echo("<td>".$row["sortorder"]."</td>");
                        echo("<td>");

                        if($is_own) {
                            echo("<button class=\"btn btn-sm btn-primary\" onclick=\"editCategory(".$row["ID"].", '".$row["bez"]."', ".$row["sortorder"].", ".$color[0].", ".$color[1].", ".$color[2].")\">Bearbeiten</button> ");
                            echo("<button class=\"btn btn-sm btn-danger\" onclick=\"deleteCategory(".$row["ID"].")\">Löschen</button>");
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

    <!-- Neue Kategorie hinzufügen -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title">Neue Kategorie hinzufügen</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="config.php" id="addForm">
                <input type="hidden" name="manId" value="<?php echo $mandant; ?>">
                <input type="hidden" name="action" value="add">

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
                    <div class="input-group">
                        <input type="color" class="form-control" id="colorpicker" value="#6464ff" style="height: 50px;">
                        <input type="hidden" name="color_r" id="color_r" value="100">
                        <input type="hidden" name="color_g" id="color_g" value="100">
                        <input type="hidden" name="color_b" id="color_b" value="255">
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Kategorie hinzufügen</button>
            </form>
        </div>
    </div>

    <!-- Bearbeiten-Form (versteckt) -->
    <div class="card mt-3" id="editCard" style="display: none;">
        <div class="card-header">
            <h5 class="card-title">Kategorie bearbeiten</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="config.php" id="editForm">
                <input type="hidden" name="manId" value="<?php echo $mandant; ?>">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">

                <div class="form-group">
                    <label for="edit_bez">Bezeichnung</label>
                    <input type="text" class="form-control" name="bez" id="edit_bez" required>
                </div>

                <div class="form-group">
                    <label for="edit_sortorder">Sortierung</label>
                    <input type="number" class="form-control" name="sortorder" id="edit_sortorder" required>
                </div>

                <div class="form-group">
                    <label for="edit_colorpicker">Farbe</label>
                    <div class="input-group">
                        <input type="color" class="form-control" id="edit_colorpicker" style="height: 50px;">
                        <input type="hidden" name="color_r" id="edit_color_r">
                        <input type="hidden" name="color_g" id="edit_color_g">
                        <input type="hidden" name="color_b" id="edit_color_b">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Speichern</button>
                <button type="button" class="btn btn-secondary" onclick="cancelEdit()">Abbrechen</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
// Colorpicker für "Hinzufügen"-Formular
document.getElementById('colorpicker').addEventListener('input', function(e) {
    const hex = e.target.value;
    const r = parseInt(hex.substr(1,2), 16);
    const g = parseInt(hex.substr(3,2), 16);
    const b = parseInt(hex.substr(5,2), 16);

    document.getElementById('color_r').value = r;
    document.getElementById('color_g').value = g;
    document.getElementById('color_b').value = b;
});

// Colorpicker für "Bearbeiten"-Formular
document.getElementById('edit_colorpicker').addEventListener('input', function(e) {
    const hex = e.target.value;
    const r = parseInt(hex.substr(1,2), 16);
    const g = parseInt(hex.substr(3,2), 16);
    const b = parseInt(hex.substr(5,2), 16);

    document.getElementById('edit_color_r').value = r;
    document.getElementById('edit_color_g').value = g;
    document.getElementById('edit_color_b').value = b;
});

function editCategory(id, bez, sortorder, r, g, b) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_bez').value = bez;
    document.getElementById('edit_sortorder').value = sortorder;
    document.getElementById('edit_color_r').value = r;
    document.getElementById('edit_color_g').value = g;
    document.getElementById('edit_color_b').value = b;

    // Hex-Wert für Colorpicker berechnen
    const hex = '#' +
        ('0' + r.toString(16)).slice(-2) +
        ('0' + g.toString(16)).slice(-2) +
        ('0' + b.toString(16)).slice(-2);
    document.getElementById('edit_colorpicker').value = hex;

    document.getElementById('editCard').style.display = 'block';
    document.getElementById('editCard').scrollIntoView({ behavior: 'smooth' });
}

function cancelEdit() {
    document.getElementById('editCard').style.display = 'none';
    document.getElementById('editForm').reset();
}

function deleteCategory(id) {
    if(confirm('Wirklich löschen?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'config.php';

        var inputManId = document.createElement('input');
        inputManId.type = 'hidden';
        inputManId.name = 'manId';
        inputManId.value = <?php echo $mandant; ?>;
        form.appendChild(inputManId);

        var inputAction = document.createElement('input');
        inputAction.type = 'hidden';
        inputAction.name = 'action';
        inputAction.value = 'delete';
        form.appendChild(inputAction);

        var inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'id';
        inputId.value = id;
        form.appendChild(inputId);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
</body>
</html>