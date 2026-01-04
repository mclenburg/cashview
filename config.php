<?php
/**
 * CashView - Konfiguration (Kategorien / Konten / Laufende Kosten)
 * Neu aufgebaut im gleichen Bootstrap-Design, mit gleicher Funktionalität.
 *
 * Fix für "Konten-Tab öffnet nicht":
 *  - Tab-Panes sind sauber als Geschwister in EINER .tab-content-Struktur aufgebaut
 *  - korrekte aria-Attribute / IDs
 *  - optional: Hash-Sync (öffnet Tab passend zur URL #konten / #laufend)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* =========================
   Konfiguration DB
   ========================= */
$dbHost = "192.168.5.103";
$dbUser = "cashview";
$dbPass = "cash123";
$dbName = "cashview";

/* =========================
   Helpers
   ========================= */
function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function post($k, $d=null) { return $_POST[$k] ?? $d; }
function get($k, $d=null) { return $_GET[$k] ?? $d; }

$mandant = -1;
if (isset($_POST["manId"])) {
    $mandant = (int)$_POST["manId"];
} elseif (isset($_GET["manId"])) {
    $mandant = (int)$_GET["manId"];
} else {
    echo "<!DOCTYPE html><html lang='de'><head><meta charset='utf-8'><title>CashView - Konfiguration</title></head><body>";
    echo "Mandanten-ID nicht übergeben";
    echo "</body></html>";
    exit;
}

$mysqli = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if (!$mysqli) {
    die("ERROR connecting to database.");
}

$success_message = "";
$error_message   = "";

/* =========================
   Actions
   ========================= */
$action = (string)post("action", "");

/** ---------- KATEGORIEN ---------- */
if ($action === "add_kategorie") {
    $bez       = trim((string)post("bez", ""));
    $sortorder = (int)post("sortorder", 0);
    $r         = (int)post("color_r", 0);
    $g         = (int)post("color_g", 0);
    $b         = (int)post("color_b", 0);

    if ($bez === "") {
        $error_message = "❌ Bitte eine Bezeichnung angeben.";
    } else {
        $statscolor = "{$r},{$g},{$b}";

        $res = mysqli_query($mysqli, "SELECT MAX(ID) AS maxid FROM kategorien");
        $row = $res ? mysqli_fetch_assoc($res) : ["maxid" => 0];
        $new_id = ((int)($row["maxid"] ?? 0)) + 1;

        $stmt = mysqli_prepare($mysqli, "INSERT INTO kategorien (ID, bez, sortorder, statscolor, manId) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isisi", $new_id, $bez, $sortorder, $statscolor, $mandant);
        if (!mysqli_stmt_execute($stmt)) {
            $error_message = "❌ Fehler beim Speichern der Kategorie.";
        } else {
            $success_message = "✅ Kategorie erfolgreich hinzugefügt!";
        }
        mysqli_stmt_close($stmt);
    }
}

if ($action === "edit_kategorie") {
    $id        = (int)post("id", 0);
    $bez       = trim((string)post("bez", ""));
    $sortorder = (int)post("sortorder", 0);
    $r         = (int)post("color_r", 0);
    $g         = (int)post("color_g", 0);
    $b         = (int)post("color_b", 0);

    if ($id <= 0 || $bez === "") {
        $error_message = "❌ Ungültige Eingaben.";
    } else {
        $statscolor = "{$r},{$g},{$b}";
        $stmt = mysqli_prepare($mysqli, "UPDATE kategorien SET bez = ?, sortorder = ?, statscolor = ? WHERE ID = ? AND manId = ?");
        mysqli_stmt_bind_param($stmt, "sisii", $bez, $sortorder, $statscolor, $id, $mandant);
        if (!mysqli_stmt_execute($stmt)) {
            $error_message = "❌ Fehler beim Aktualisieren der Kategorie.";
        } else {
            $success_message = "✅ Kategorie erfolgreich aktualisiert!";
        }
        mysqli_stmt_close($stmt);
    }
}

if ($action === "delete_kategorie") {
    $id = (int)post("id", 0);

    // Nur eigene Kategorien dürfen gelöscht werden (manId = Mandant)
    if ($id <= 0) {
        $error_message = "❌ Ungültige Kategorie-ID.";
    } else {
        // Check, ob Kategorie in Transaktionen genutzt wird
        $stmt = mysqli_prepare($mysqli, "SELECT COUNT(*) AS cnt FROM transaktionen WHERE KatID = ? AND manId = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $mandant);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = $res ? mysqli_fetch_assoc($res) : ["cnt" => 0];
        mysqli_stmt_close($stmt);

        if ((int)$row["cnt"] > 0) {
            $error_message = "❌ Kategorie kann nicht gelöscht werden, da Transaktionen existieren!";
        } else {
            $stmt = mysqli_prepare($mysqli, "DELETE FROM kategorien WHERE ID = ? AND manId = ?");
            mysqli_stmt_bind_param($stmt, "ii", $id, $mandant);
            if (!mysqli_stmt_execute($stmt)) {
                $error_message = "❌ Fehler beim Löschen der Kategorie.";
            } else {
                $success_message = "✅ Kategorie erfolgreich gelöscht!";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

/** ---------- KONTEN ---------- */
if ($action === "add_konto") {
    $bez            = trim((string)post("konto_bez", ""));
    $grenze         = (float)post("konto_grenze", 0);
    $initialbetrag  = (float)post("konto_initial", 0);

    if ($bez === "") {
        $error_message = "❌ Bitte eine Kontobezeichnung angeben.";
    } else {
        $res = mysqli_query($mysqli, "SELECT MAX(id) AS maxid FROM Konten");
        $row = $res ? mysqli_fetch_assoc($res) : ["maxid" => 0];
        $new_id = ((int)($row["maxid"] ?? 0)) + 1;

        $stmt = mysqli_prepare($mysqli, "INSERT INTO Konten (id, Bez, Grenze, manId) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isdi", $new_id, $bez, $grenze, $mandant);
        if (!mysqli_stmt_execute($stmt)) {
            $error_message = "❌ Fehler beim Speichern des Kontos.";
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);

            $res2 = mysqli_query($mysqli, "SELECT MAX(initId) AS maxid FROM Initialwerte");
            $row2 = $res2 ? mysqli_fetch_assoc($res2) : ["maxid" => 0];
            $new_init_id = ((int)($row2["maxid"] ?? 0)) + 1;

            $stmt2 = mysqli_prepare($mysqli, "INSERT INTO Initialwerte (initId, Betrag, KtoId) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "idi", $new_init_id, $initialbetrag, $new_id);
            if (!mysqli_stmt_execute($stmt2)) {
                $error_message = "❌ Konto angelegt, aber Initialwert konnte nicht gespeichert werden.";
            } else {
                $success_message = "✅ Konto erfolgreich hinzugefügt!";
            }
            mysqli_stmt_close($stmt2);
        }
    }
}

if ($action === "edit_konto") {
    $id     = (int)post("konto_id", 0);
    $bez    = trim((string)post("konto_bez", ""));
    $grenze = (float)post("konto_grenze", 0);

    if ($id <= 0 || $bez === "") {
        $error_message = "❌ Ungültige Eingaben.";
    } else {
        $stmt = mysqli_prepare($mysqli, "UPDATE Konten SET Bez = ?, Grenze = ? WHERE id = ? AND manId = ?");
        mysqli_stmt_bind_param($stmt, "sdii", $bez, $grenze, $id, $mandant);
        if (!mysqli_stmt_execute($stmt)) {
            $error_message = "❌ Fehler beim Aktualisieren des Kontos.";
        } else {
            $success_message = "✅ Konto erfolgreich aktualisiert!";
        }
        mysqli_stmt_close($stmt);
    }
}

if ($action === "delete_konto") {
    $id = (int)post("konto_id", 0);
    if ($id <= 0) {
        $error_message = "❌ Ungültige Konto-ID.";
    } else {
        $stmt = mysqli_prepare($mysqli, "SELECT COUNT(*) AS cnt FROM transaktionen WHERE KtoID = ? AND manId = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $mandant);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = $res ? mysqli_fetch_assoc($res) : ["cnt" => 0];
        mysqli_stmt_close($stmt);

        if ((int)$row["cnt"] > 0) {
            $error_message = "❌ Konto kann nicht gelöscht werden, da Transaktionen existieren!";
        } else {
            mysqli_query($mysqli, "DELETE FROM Initialwerte WHERE KtoId = {$id}");
            $stmt2 = mysqli_prepare($mysqli, "DELETE FROM Konten WHERE id = ? AND manId = ?");
            mysqli_stmt_bind_param($stmt2, "ii", $id, $mandant);
            if (!mysqli_stmt_execute($stmt2)) {
                $error_message = "❌ Fehler beim Löschen des Kontos.";
            } else {
                $success_message = "✅ Konto erfolgreich gelöscht!";
            }
            mysqli_stmt_close($stmt2);
        }
    }
}

/** ---------- LAUFENDE KOSTEN ---------- */
if ($action === "add_laufend") {
    $wert         = (float)post("laufend_wert", 0);
    $ktoID        = (int)post("laufend_konto", 0);
    $katID        = (int)post("laufend_kategorie", 0);
    $modulo       = (int)post("laufend_modulo", 1);
    $beschreibung = trim((string)post("laufend_beschreibung", ""));

    if ($beschreibung === "" || $ktoID <= 0 || $katID <= 0 || $wert == 0.0) {
        $error_message = "❌ Bitte alle Felder korrekt ausfüllen (Beschreibung, Betrag, Konto, Kategorie).";
    } else {
        $res = mysqli_query($mysqli, "SELECT MAX(id) AS maxid FROM laufendes");
        $row = $res ? mysqli_fetch_assoc($res) : ["maxid" => 0];
        $new_id = ((int)($row["maxid"] ?? 0)) + 1;

        $stmt = mysqli_prepare($mysqli, "INSERT INTO laufendes (id, Wert, ktoID, katID, modulo, Beschreibung, manId) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "idiiisi", $new_id, $wert, $ktoID, $katID, $modulo, $beschreibung, $mandant);
        if (!mysqli_stmt_execute($stmt)) {
            $error_message = "❌ Fehler beim Speichern der laufenden Kosten.";
        } else {
            $success_message = "✅ Laufende Kosten erfolgreich hinzugefügt!";
        }
        mysqli_stmt_close($stmt);
    }
}

if ($action === "delete_laufend") {
    $id = (int)post("laufend_id", 0);
    if ($id <= 0) {
        $error_message = "❌ Ungültige ID.";
    } else {
        $stmt = mysqli_prepare($mysqli, "DELETE FROM laufendes WHERE id = ? AND manId = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $mandant);
        if (!mysqli_stmt_execute($stmt)) {
            $error_message = "❌ Fehler beim Löschen der laufenden Kosten.";
        } else {
            $success_message = "✅ Laufende Kosten erfolgreich gelöscht!";
        }
        mysqli_stmt_close($stmt);
    }
}

/* =========================
   Data for display
   ========================= */

// Kategorien (eigene + globale)
$kategorien = [];
$q = "SELECT ID, bez, sortorder, statscolor, manId
      FROM kategorien
      WHERE (manId = ? OR manId = 0) AND sortorder <> 999
      ORDER BY sortorder";
$stmt = mysqli_prepare($mysqli, $q);
mysqli_stmt_bind_param($stmt, "i", $mandant);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($res && ($row = mysqli_fetch_assoc($res))) $kategorien[] = $row;
mysqli_stmt_close($stmt);

// Konten (eigene)
$konten = [];
$q = "SELECT k.id, k.Bez, k.Grenze, i.Betrag
      FROM Konten k
      LEFT JOIN Initialwerte i ON i.KtoId = k.id
      WHERE k.manId = ?
      ORDER BY k.Bez";
$stmt = mysqli_prepare($mysqli, $q);
mysqli_stmt_bind_param($stmt, "i", $mandant);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($res && ($row = mysqli_fetch_assoc($res))) $konten[] = $row;
mysqli_stmt_close($stmt);

// Laufendes (eigene)
$laufendes = [];
$q = "SELECT l.id, l.Wert, l.ktoID, l.katID, l.modulo, l.Beschreibung,
             k.Bez AS KontoBez, ka.bez AS KatBez
      FROM laufendes l
      LEFT JOIN Konten k ON k.id = l.ktoID
      LEFT JOIN kategorien ka ON ka.ID = l.katID
      WHERE l.manId = ?
      ORDER BY l.Beschreibung";
$stmt = mysqli_prepare($mysqli, $q);
mysqli_stmt_bind_param($stmt, "i", $mandant);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($res && ($row = mysqli_fetch_assoc($res))) $laufendes[] = $row;
mysqli_stmt_close($stmt);

// Intervalle für Anzeige
$intervalle = [
    1  => "Monatlich",
    2  => "Alle 2 Monate",
    3  => "Quartalsweise (alle 3 Monate)",
    4  => "Alle 4 Monate",
    6  => "Halbjährlich (alle 6 Monate)",
    12 => "Jährlich (alle 12 Monate)"
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CashView - Konfiguration</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="http://192.168.5.103/cashview/favicon.ico" rel="shortcut icon">
    <link rel="icon" href="http://192.168.5.103/cashview/favicon.ico" type="image/ico">

    <style>
        /* Mobile First Styles */
        body { font-size: 14px; padding: 0; margin: 0; }
        .container { padding: 1rem; }
        .nav-tabs { margin-bottom: 1rem; border-bottom: 2px solid #dee2e6; }
        .nav-tabs .nav-link { border: none; border-bottom: 3px solid transparent; padding: 0.75rem 0.5rem; font-size: 0.95rem; }
        .nav-tabs .nav-link.active { border-bottom-color: #007bff; font-weight: 600; }
        .card { margin-bottom: 1rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card-header { padding: 0.75rem 1rem; background-color: #f8f9fa; }
        .card-title { font-size: 1.1rem; margin-bottom: 0.25rem; }
        .card-body { padding: 1rem; }
        .table { font-size: 0.85rem; }
        .table th { border-top: none; font-weight: 600; }
        .btn { padding: 0.5rem 1rem; font-size: 0.9rem; border-radius: 6px; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.8rem; }
        .form-group { margin-bottom: 1rem; }
        .form-control { border-radius: 6px; font-size: 0.9rem; }
        .color-preview { width: 24px; height: 24px; border-radius: 4px; border: 1px solid #ddd; display: inline-block; vertical-align: middle; }
        .action-buttons { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .alert { border-radius: 8px; margin-bottom: 1rem; }
        @media (min-width: 768px) {
            body { font-size: 16px; }
            .container { max-width: 1200px; }
            .nav-tabs .nav-link { padding: 1rem 1.5rem; font-size: 1rem; }
            .table { font-size: 0.9rem; }
        }
    </style>
</head>
<body>
<div class="container">

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?= h($success_message) ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?= h($error_message) ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs" id="configTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="kategorien-tab" data-toggle="tab" href="#kategorien" role="tab"
               aria-controls="kategorien" aria-selected="true">📁 Kategorien</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="konten-tab" data-toggle="tab" href="#konten" role="tab"
               aria-controls="konten" aria-selected="false">💳 Konten</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="laufend-tab" data-toggle="tab" href="#laufend" role="tab"
               aria-controls="laufend" aria-selected="false">🔄 Laufende Kosten</a>
        </li>
    </ul>

    <!-- WICHTIG: Alle 3 Tab-Panes als direkte Kinder von .tab-content (Fix für "Konten öffnet nicht") -->
    <div class="tab-content" id="configTabsContent">

        <!-- ========== TAB: KATEGORIEN ========== -->
        <div class="tab-pane fade show active" id="kategorien" role="tabpanel" aria-labelledby="kategorien-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Meine Kategorien</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Kategorien für Mandant <?= (int)$mandant; ?></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Farbe</th>
                                <th>Bezeichnung</th>
                                <th>Sortierung</th>
                                <th>Typ</th>
                                <th>Aktionen</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($kategorien as $row): ?>
                                <?php
                                $color = explode(",", (string)$row["statscolor"]);
                                $r = (int)($color[0] ?? 0);
                                $g = (int)($color[1] ?? 0);
                                $b = (int)($color[2] ?? 0);
                                $rgb = "rgb({$r},{$g},{$b})";
                                $is_own = ((int)$row["manId"] === (int)$mandant);
                                ?>
                                <tr>
                                    <td><div class="color-preview" style="background: <?= h($rgb) ?>"></div></td>
                                    <td><?= h($row["bez"]) ?></td>
                                    <td><?= (int)$row["sortorder"] ?></td>
                                    <td><?= $is_own ? "Eigene" : "Global" ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($is_own): ?>
                                                <button class="btn btn-sm btn-outline-primary"
                                                        onclick="startEditKategorie(<?= (int)$row['ID'] ?>,'<?= h($row['bez']) ?>',<?= (int)$row['sortorder'] ?>,<?= $r ?>,<?= $g ?>,<?= $b ?>)">
                                                    Bearbeiten
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteKategorie(<?= (int)$row['ID'] ?>)">
                                                    Löschen
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($kategorien) === 0): ?>
                                <tr><td colspan="5" class="text-muted">Keine Kategorien vorhanden.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kategorie hinzufügen -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Neue Kategorie hinzufügen</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php">
                        <input type="hidden" name="manId" value="<?= (int)$mandant ?>">
                        <input type="hidden" name="action" value="add_kategorie">

                        <div class="form-group">
                            <label>Bezeichnung</label>
                            <input type="text" name="bez" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Sortierreihenfolge</label>
                            <input type="number" name="sortorder" class="form-control" value="0" required>
                        </div>

                        <div class="form-group">
                            <label>Farbe</label>
                            <input type="color" id="colorpicker" class="form-control" value="#007bff" style="height: 44px;">
                            <input type="hidden" name="color_r" id="color_r" value="0">
                            <input type="hidden" name="color_g" id="color_g" value="123">
                            <input type="hidden" name="color_b" id="color_b" value="255">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Hinzufügen</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kategorie bearbeiten (hidden) -->
            <div class="card" id="editKategorieCard" style="display:none;">
                <div class="card-header">
                    <h5 class="card-title">Kategorie bearbeiten</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php" id="editKategorieForm">
                        <input type="hidden" name="manId" value="<?= (int)$mandant ?>">
                        <input type="hidden" name="action" value="edit_kategorie">
                        <input type="hidden" name="id" id="edit_kat_id">

                        <div class="form-group">
                            <label>Bezeichnung</label>
                            <input type="text" name="bez" id="edit_kat_bez" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Sortierreihenfolge</label>
                            <input type="number" name="sortorder" id="edit_kat_sortorder" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Farbe</label>
                            <input type="color" id="edit_kat_colorpicker" class="form-control" style="height: 44px;">
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
        <div class="tab-pane fade" id="konten" role="tabpanel" aria-labelledby="konten-tab">
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
                            <?php foreach ($konten as $row): ?>
                                <tr>
                                    <td><?= h($row["Bez"]) ?></td>
                                    <td><?= number_format((float)($row["Betrag"] ?? 0), 2, ',', '.') ?> €</td>
                                    <td><?= number_format((float)($row["Grenze"] ?? 0), 2, ',', '.') ?> €</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    onclick="startEditKonto(<?= (int)$row['id'] ?>,'<?= h($row['Bez']) ?>',<?= (float)$row['Grenze'] ?>)">
                                                Bearbeiten
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteKonto(<?= (int)$row['id'] ?>)">
                                                Löschen
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($konten) === 0): ?>
                                <tr><td colspan="4" class="text-muted">Keine Konten vorhanden.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Konto hinzufügen -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Neues Konto hinzufügen</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php">
                        <input type="hidden" name="manId" value="<?= (int)$mandant ?>">
                        <input type="hidden" name="action" value="add_konto">

                        <div class="form-group">
                            <label>Konto-Bezeichnung</label>
                            <input type="text" name="konto_bez" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Anfangsbestand</label>
                            <input type="number" step="0.01" name="konto_initial" class="form-control" value="0.00" required>
                        </div>

                        <div class="form-group">
                            <label>Dispogrenze</label>
                            <input type="number" step="0.01" name="konto_grenze" class="form-control" value="0.00" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Hinzufügen</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Konto bearbeiten (hidden) -->
            <div class="card" id="editKontoCard" style="display:none;">
                <div class="card-header">
                    <h5 class="card-title">Konto bearbeiten</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php" id="editKontoForm">
                        <input type="hidden" name="manId" value="<?= (int)$mandant ?>">
                        <input type="hidden" name="action" value="edit_konto">
                        <input type="hidden" name="konto_id" id="edit_konto_id">

                        <div class="form-group">
                            <label>Konto-Bezeichnung</label>
                            <input type="text" name="konto_bez" id="edit_konto_bez" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Dispogrenze</label>
                            <input type="number" step="0.01" name="konto_grenze" id="edit_konto_grenze" class="form-control" required>
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
        <div class="tab-pane fade" id="laufend" role="tabpanel" aria-labelledby="laufend-tab">
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
                                <th>Konto</th>
                                <th>Kategorie</th>
                                <th>Intervall</th>
                                <th>Aktionen</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($laufendes as $row): ?>
                                <?php
                                $mod = (int)($row["modulo"] ?? 1);
                                $intervall = $intervalle[$mod] ?? ($mod . " Tage");
                                ?>
                                <tr>
                                    <td><?= h($row["Beschreibung"]) ?></td>
                                    <td><?= number_format((float)$row["Wert"], 2, ',', '.') ?> €</td>
                                    <td><?= h($row["KontoBez"] ?? ("#" . (int)$row["ktoID"])) ?></td>
                                    <td><?= h($row["KatBez"] ?? ("#" . (int)$row["katID"])) ?></td>
                                    <td><?= h($intervall) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteLaufend(<?= (int)$row['id'] ?>)">
                                                Löschen
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($laufendes) === 0): ?>
                                <tr><td colspan="6" class="text-muted">Keine laufenden Kosten vorhanden.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Laufende Kosten hinzufügen -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Laufende Kosten hinzufügen</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php">
                        <input type="hidden" name="manId" value="<?= (int)$mandant ?>">
                        <input type="hidden" name="action" value="add_laufend">

                        <div class="form-group">
                            <label>Beschreibung</label>
                            <input type="text" name="laufend_beschreibung" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Betrag</label>
                            <input type="number" step="0.01" name="laufend_wert" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Konto</label>
                            <select name="laufend_konto" class="form-control" required>
                                <option value="">Bitte wählen...</option>
                                <?php foreach ($konten as $k): ?>
                                    <option value="<?= (int)$k["id"] ?>"><?= h($k["Bez"]) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kategorie</label>
                            <select name="laufend_kategorie" class="form-control" required>
                                <option value="">Bitte wählen...</option>
                                <?php foreach ($kategorien as $ka): ?>
                                    <option value="<?= (int)$ka["ID"] ?>"><?= h($ka["bez"]) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Intervall</label>
                            <select name="laufend_modulo" class="form-control" required>
                                <?php foreach ($intervalle as $val => $label): ?>
                                    <option value="<?= (int)$val ?>"><?= h($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Hinzufügen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div><!-- /.tab-content -->
</div><!-- /.container -->

<script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha384-vk5WoKIiMzZ6I0X58F3RDeo63eFUsVTNff7kwh28ykVZCEN0N7LxyzKk5X7xL7sA"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>

<script>
    // --- Farbe -> RGB Hidden Inputs (Add) ---
    (function initColorpickerAdd() {
        const cp = document.getElementById('colorpicker');
        if (!cp) return;
        cp.addEventListener('input', function(e) {
            const hex = e.target.value;
            const r = parseInt(hex.substr(1,2), 16);
            const g = parseInt(hex.substr(3,2), 16);
            const b = parseInt(hex.substr(5,2), 16);
            document.getElementById('color_r').value = r;
            document.getElementById('color_g').value = g;
            document.getElementById('color_b').value = b;
        });
        // initial set
        cp.dispatchEvent(new Event('input'));
    })();

    // --- Farbe -> RGB Hidden Inputs (Edit Kategorie) ---
    (function initColorpickerEditKat() {
        const cp = document.getElementById('edit_kat_colorpicker');
        if (!cp) return;
        cp.addEventListener('input', function(e) {
            const hex = e.target.value;
            const r = parseInt(hex.substr(1,2), 16);
            const g = parseInt(hex.substr(3,2), 16);
            const b = parseInt(hex.substr(5,2), 16);
            document.getElementById('edit_kat_color_r').value = r;
            document.getElementById('edit_kat_color_g').value = g;
            document.getElementById('edit_kat_color_b').value = b;
        });
    })();

    function rgbToHex(r, g, b) {
        const toHex = (n) => ('0' + Number(n).toString(16)).slice(-2);
        return '#' + toHex(r) + toHex(g) + toHex(b);
    }

    // --- Kategorien Edit ---
    function startEditKategorie(id, bez, sortorder, r, g, b) {
        document.getElementById('edit_kat_id').value = id;
        document.getElementById('edit_kat_bez').value = bez;
        document.getElementById('edit_kat_sortorder').value = sortorder;

        document.getElementById('edit_kat_color_r').value = r;
        document.getElementById('edit_kat_color_g').value = g;
        document.getElementById('edit_kat_color_b').value = b;

        const hex = rgbToHex(r, g, b);
        document.getElementById('edit_kat_colorpicker').value = hex;
        document.getElementById('editKategorieCard').style.display = 'block';
        document.getElementById('editKategorieCard').scrollIntoView({ behavior: 'smooth' });
    }

    function cancelEditKategorie() {
        document.getElementById('editKategorieCard').style.display = 'none';
        document.getElementById('editKategorieForm').reset();
    }

    function deleteKategorie(id) {
        if (!confirm('Kategorie wirklich löschen?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'config.php';

        form.appendChild(hidden('manId', <?= (int)$mandant ?>));
        form.appendChild(hidden('action', 'delete_kategorie'));
        form.appendChild(hidden('id', id));

        document.body.appendChild(form);
        form.submit();
    }

    // --- Konten Edit ---
    function startEditKonto(id, bez, grenze) {
        document.getElementById('edit_konto_id').value = id;
        document.getElementById('edit_konto_bez').value = bez;
        document.getElementById('edit_konto_grenze').value = grenze;

        document.getElementById('editKontoCard').style.display = 'block';
        document.getElementById('editKontoCard').scrollIntoView({ behavior: 'smooth' });
    }

    function cancelEditKonto() {
        document.getElementById('editKontoCard').style.display = 'none';
        document.getElementById('editKontoForm').reset();
    }

    function deleteKonto(id) {
        if (!confirm('Konto wirklich löschen?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'config.php';

        form.appendChild(hidden('manId', <?= (int)$mandant ?>));
        form.appendChild(hidden('action', 'delete_konto'));
        form.appendChild(hidden('konto_id', id));

        document.body.appendChild(form);
        form.submit();
    }

    // --- Laufendes ---
    function deleteLaufend(id) {
        if (!confirm('Laufende Kosten wirklich löschen?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'config.php';

        form.appendChild(hidden('manId', <?= (int)$mandant ?>));
        form.appendChild(hidden('action', 'delete_laufend'));
        form.appendChild(hidden('laufend_id', id));

        document.body.appendChild(form);
        form.submit();
    }

    function hidden(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }

    // --- Tab per URL-Hash öffnen (optional, macht UX besser) ---
    (function syncTabsWithHash() {
        function activateFromHash() {
            const hash = window.location.hash;
            if (!hash) return;
            const $link = $('a[data-toggle="tab"][href="' + hash + '"]');
            if ($link.length) $link.tab('show');
        }
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('href');
            if (target) history.replaceState(null, '', target);
        });
        activateFromHash();
    })();
</script>
</body>
</html>
