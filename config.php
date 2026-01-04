<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$mandant = -1;
if (isset($_POST["manId"])) $mandant = (int)$_POST["manId"];
elseif (isset($_GET["manId"])) $mandant = (int)$_GET["manId"];
else {
    echo "<!DOCTYPE html><html lang='de'><head><meta charset='utf-8'><title>CashView - Konfiguration</title></head><body>Mandanten-ID nicht übergeben</body></html>";
    exit;
}

// DB (wie bisher)
$db = mysqli_connect("192.168.5.103", "cashview", "cash123", "cashview")
    or die("ERROR connecting to database.");

$success_message = "";
$error_message = "";

// -------------------- ACTIONS --------------------
$action = $_POST["action"] ?? "";

/* Helper: Mandanten-Sicherheit prüfen */
function konto_gehoert_mandant(mysqli $db, int $ktoId, int $manId): bool {
    $q = "SELECT COUNT(*) AS cnt FROM Konten WHERE id = $ktoId AND manId = $manId";
    $r = mysqli_query($db, $q);
    $row = $r ? mysqli_fetch_assoc($r) : ["cnt" => 0];
    return ((int)$row["cnt"] > 0);
}
function kategorie_gehoert_mandant_oder_global(mysqli $db, int $katId, int $manId): bool {
    $q = "SELECT COUNT(*) AS cnt FROM kategorien WHERE ID = $katId AND (manId = $manId OR manId = 0)";
    $r = mysqli_query($db, $q);
    $row = $r ? mysqli_fetch_assoc($r) : ["cnt" => 0];
    return ((int)$row["cnt"] > 0);
}

// ========== KATEGORIEN ==========
if ($action === "add_kategorie") {
    $bez = mysqli_real_escape_string($db, trim($_POST["bez"] ?? ""));
    $sortorder = (int)($_POST["sortorder"] ?? 0);
    $r = (int)($_POST["color_r"] ?? 100);
    $g = (int)($_POST["color_g"] ?? 100);
    $b = (int)($_POST["color_b"] ?? 255);
    $statscolor = "$r,$g,$b";

    if ($bez === "") {
        $error_message = "❌ Bitte eine Bezeichnung angeben.";
    } else {
        $res = mysqli_query($db, "SELECT MAX(ID) AS maxid FROM kategorien");
        $row = $res ? mysqli_fetch_assoc($res) : ["maxid" => 0];
        $new_id = ((int)($row["maxid"] ?? 0)) + 1;

        $sql = "INSERT INTO kategorien (ID, bez, sortorder, statscolor, manId) VALUES ($new_id, '$bez', $sortorder, '$statscolor', $mandant)";
        if (!mysqli_query($db, $sql)) $error_message = "❌ Fehler beim Speichern der Kategorie: ".mysqli_error($db);
        else $success_message = "✅ Kategorie erfolgreich hinzugefügt!";
    }
}

if ($action === "edit_kategorie") {
    $id = (int)($_POST["id"] ?? 0);
    $bez = mysqli_real_escape_string($db, trim($_POST["bez"] ?? ""));
    $sortorder = (int)($_POST["sortorder"] ?? 0);
    $r = (int)($_POST["color_r"] ?? 100);
    $g = (int)($_POST["color_g"] ?? 100);
    $b = (int)($_POST["color_b"] ?? 255);
    $statscolor = "$r,$g,$b";

    if ($id <= 0 || $bez === "") {
        $error_message = "❌ Ungültige Eingaben.";
    } else {
        $sql = "UPDATE kategorien SET bez='$bez', sortorder=$sortorder, statscolor='$statscolor' WHERE ID=$id AND manId=$mandant";
        if (!mysqli_query($db, $sql)) $error_message = "❌ Fehler beim Aktualisieren: ".mysqli_error($db);
        else $success_message = "✅ Kategorie erfolgreich aktualisiert!";
    }
}

if ($action === "delete_kategorie") {
    $id = (int)($_POST["id"] ?? 0);
    if ($id <= 0) {
        $error_message = "❌ Ungültige Kategorie-ID.";
    } else {
        $check = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM transaktionen WHERE katID = $id AND manId = $mandant");
        $row = $check ? mysqli_fetch_assoc($check) : ["cnt" => 0];
        if ((int)$row["cnt"] > 0) {
            $error_message = "❌ Kategorie kann nicht gelöscht werden, da Transaktionen existieren!";
        } else {
            if (!mysqli_query($db, "DELETE FROM kategorien WHERE ID=$id AND manId=$mandant")) {
                $error_message = "❌ Fehler beim Löschen: ".mysqli_error($db);
            } else {
                $success_message = "✅ Kategorie erfolgreich gelöscht!";
            }
        }
    }
}

// ========== KONTEN ==========
if ($action === "add_konto") {
    $bez = mysqli_real_escape_string($db, trim($_POST["konto_bez"] ?? ""));
    $grenze = (float)($_POST["konto_grenze"] ?? 0);
    $initialbetrag = (float)($_POST["konto_initial"] ?? 0);

    if ($bez === "") {
        $error_message = "❌ Bitte eine Kontobezeichnung angeben.";
    } else {
        $res = mysqli_query($db, "SELECT MAX(id) AS maxid FROM Konten");
        $row = $res ? mysqli_fetch_assoc($res) : ["maxid" => 0];
        $new_id = ((int)($row["maxid"] ?? 0)) + 1;

        $sql = "INSERT INTO Konten (id, Bez, Grenze, manId) VALUES ($new_id, '$bez', '$grenze', $mandant)";
        if (!mysqli_query($db, $sql)) {
            $error_message = "❌ Fehler beim Speichern des Kontos: ".mysqli_error($db);
        } else {
            $res2 = mysqli_query($db, "SELECT MAX(initId) AS maxid FROM Initialwerte");
            $row2 = $res2 ? mysqli_fetch_assoc($res2) : ["maxid" => 0];
            $new_init_id = ((int)($row2["maxid"] ?? 0)) + 1;

            $sql2 = "INSERT INTO Initialwerte (initId, Betrag, KtoId) VALUES ($new_init_id, $initialbetrag, $new_id)";
            if (!mysqli_query($db, $sql2)) $error_message = "❌ Konto angelegt, aber Initialwert nicht gespeichert: ".mysqli_error($db);
            else $success_message = "✅ Konto erfolgreich hinzugefügt!";
        }
    }
}

if ($action === "edit_konto") {
    $id = (int)($_POST["konto_id"] ?? 0);
    $bez = mysqli_real_escape_string($db, trim($_POST["konto_bez"] ?? ""));
    $grenze = (float)($_POST["konto_grenze"] ?? 0);

    if ($id <= 0 || $bez === "") {
        $error_message = "❌ Ungültige Eingaben.";
    } else {
        $sql = "UPDATE Konten SET Bez='$bez', Grenze='$grenze' WHERE id=$id AND manId=$mandant";
        if (!mysqli_query($db, $sql)) $error_message = "❌ Fehler beim Aktualisieren: ".mysqli_error($db);
        else $success_message = "✅ Konto erfolgreich aktualisiert!";
    }
}

if ($action === "delete_konto") {
    $id = (int)($_POST["konto_id"] ?? 0);
    if ($id <= 0) {
        $error_message = "❌ Ungültige Konto-ID.";
    } else {
        $check = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM transaktionen WHERE KtoID = $id AND manId = $mandant");
        $row = $check ? mysqli_fetch_assoc($check) : ["cnt" => 0];

        if ((int)$row["cnt"] > 0) {
            $error_message = "❌ Konto kann nicht gelöscht werden, da Transaktionen existieren!";
        } else {
            mysqli_query($db, "DELETE FROM Initialwerte WHERE KtoId = $id");
            if (!mysqli_query($db, "DELETE FROM Konten WHERE id=$id AND manId=$mandant")) {
                $error_message = "❌ Fehler beim Löschen: ".mysqli_error($db);
            } else {
                $success_message = "✅ Konto erfolgreich gelöscht!";
            }
        }
    }
}

// ========== LAUFENDE KOSTEN ==========
if ($action === "add_laufend") {
    $beschreibung = mysqli_real_escape_string($db, trim($_POST["laufend_beschreibung"] ?? ""));
    $wert = (float)($_POST["laufend_wert"] ?? 0);
    $modulo = (int)($_POST["laufend_modulo"] ?? 1);
    $ktoID = (int)($_POST["laufend_konto"] ?? 0);
    $katID = (int)($_POST["laufend_kategorie"] ?? 0);

    if ($beschreibung === "" || $wert == 0.0 || $ktoID <= 0 || $katID <= 0) {
        $error_message = "❌ Bitte alle Felder korrekt ausfüllen.";
    } elseif (!konto_gehoert_mandant($db, $ktoID, $mandant)) {
        $error_message = "❌ Ungültiges Konto (gehört nicht zu diesem Mandanten).";
    } elseif (!kategorie_gehoert_mandant_oder_global($db, $katID, $mandant)) {
        $error_message = "❌ Ungültige Kategorie (gehört nicht zu diesem Mandanten / global).";
    } else {
        $res = mysqli_query($db, "SELECT MAX(id) AS maxid FROM laufendes");
        $row = $res ? mysqli_fetch_assoc($res) : ["maxid" => 0];
        $new_id = ((int)($row["maxid"] ?? 0)) + 1;

        $sql = "INSERT INTO laufendes (id, Wert, ktoID, katID, modulo, Beschreibung, manId)
                VALUES ($new_id, $wert, $ktoID, $katID, $modulo, '$beschreibung', $mandant)";
        if (!mysqli_query($db, $sql)) $error_message = "❌ Fehler beim Speichern: ".mysqli_error($db);
        else $success_message = "✅ Laufende Kosten erfolgreich hinzugefügt!";
    }
}

if ($action === "edit_laufend") {
    $id = (int)($_POST["laufend_id"] ?? 0);
    $beschreibung = mysqli_real_escape_string($db, trim($_POST["laufend_beschreibung"] ?? ""));
    $wert = (float)($_POST["laufend_wert"] ?? 0);
    $modulo = (int)($_POST["laufend_modulo"] ?? 1);
    $ktoID = (int)($_POST["laufend_konto"] ?? 0);
    $katID = (int)($_POST["laufend_kategorie"] ?? 0);

    if ($id <= 0 || $beschreibung === "" || $wert == 0.0 || $ktoID <= 0 || $katID <= 0) {
        $error_message = "❌ Bitte alle Felder korrekt ausfüllen.";
    } elseif (!konto_gehoert_mandant($db, $ktoID, $mandant)) {
        $error_message = "❌ Ungültiges Konto (gehört nicht zu diesem Mandanten).";
    } elseif (!kategorie_gehoert_mandant_oder_global($db, $katID, $mandant)) {
        $error_message = "❌ Ungültige Kategorie (gehört nicht zu diesem Mandanten / global).";
    } else {
        // Sicherstellen: Datensatz gehört zum Mandanten
        $check = mysqli_query($db, "SELECT COUNT(*) AS cnt FROM laufendes WHERE id=$id AND manId=$mandant");
        $row = $check ? mysqli_fetch_assoc($check) : ["cnt" => 0];
        if ((int)$row["cnt"] <= 0) {
            $error_message = "❌ Ungültige laufende Kosten-ID (nicht dein Mandant).";
        } else {
            $sql = "UPDATE laufendes
                    SET Wert = $wert, ktoID = $ktoID, katID = $katID, modulo = $modulo, Beschreibung = '$beschreibung'
                    WHERE id = $id AND manId = $mandant";
            if (!mysqli_query($db, $sql)) $error_message = "❌ Fehler beim Aktualisieren: ".mysqli_error($db);
            else $success_message = "✅ Laufende Kosten erfolgreich aktualisiert!";
        }
    }
}

if ($action === "delete_laufend") {
    $id = (int)($_POST["laufend_id"] ?? 0);
    if ($id <= 0) $error_message = "❌ Ungültige ID.";
    else {
        if (!mysqli_query($db, "DELETE FROM laufendes WHERE id=$id AND manId=$mandant")) {
            $error_message = "❌ Fehler beim Löschen: ".mysqli_error($db);
        } else {
            $success_message = "✅ Laufende Kosten erfolgreich gelöscht!";
        }
    }
}

// -------------------- DATA --------------------
$kategorien = [];
$q = "SELECT ID, bez, sortorder, statscolor, manId
      FROM kategorien
      WHERE (manId = $mandant OR manId = 0) AND sortorder <> 999
      ORDER BY sortorder";
$r = mysqli_query($db, $q);
while ($r && ($row = mysqli_fetch_assoc($r))) $kategorien[] = $row;

$konten = [];
$q = "SELECT k.id, k.Bez, k.Grenze, i.Betrag
      FROM Konten k
      LEFT JOIN Initialwerte i ON k.id = i.KtoId
      WHERE k.manId = $mandant
      ORDER BY k.Bez";
$r = mysqli_query($db, $q);
while ($r && ($row = mysqli_fetch_assoc($r))) $konten[] = $row;

$laufendes = [];
// Join abgesichert: Konto MUSS Mandant, Kategorie darf Mandant oder global
$q = "SELECT l.id, l.Wert, l.modulo, l.Beschreibung, l.ktoID, l.katID,
             k.Bez AS KontoBez, kat.bez AS KatBez
      FROM laufendes l
      LEFT JOIN Konten k ON l.ktoID = k.id AND k.manId = l.manId
      LEFT JOIN kategorien kat ON l.katID = kat.ID AND (kat.manId = l.manId OR kat.manId = 0)
      WHERE l.manId = $mandant
      ORDER BY l.Beschreibung";
$r = mysqli_query($db, $q);
while ($r && ($row = mysqli_fetch_assoc($r))) $laufendes[] = $row;

$intervalle = [
    1  => "Monatlich",
    2  => "Alle 2 Monate",
    3  => "Quartalsweise (alle 3 Monate)",
    4  => "Alle 4 Monate",
    6  => "Halbjährlich (alle 6 Monate)",
    12 => "Jährlich (alle 12 Monate)"
];

// Summe laufende Kosten: nur positive Beträge (Ausgaben)
$summe_laufend_pos = 0.0;
foreach ($laufendes as $l) {
    $w = (float)($l["Wert"] ?? 0);
    if ($w > 0) $summe_laufend_pos += $w;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CashView - Konfiguration</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link href="http://192.168.5.103/cashview/favicon.ico" rel="shortcut icon">
    <link rel="icon" href="http://192.168.5.103/cashview/favicon.ico" type="image/ico">

    <style>
        body { font-size: 14px; padding: 0; margin: 0; }
        .container { padding-left: 10px; padding-right: 10px; }

        .navbar { padding: 0.5rem 1rem; flex-wrap: wrap; }
        .navbar-brand { font-size: 1.1rem; margin-right: auto; }
        .btn-back { font-size: 0.85rem; padding: 0.4rem 0.8rem; }

        .nav-tabs { margin-bottom: 1.5rem; border-bottom: 2px solid #dee2e6; }
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

        .card { margin-bottom: 1rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card-header { padding: 0.75rem 1rem; background-color: #f8f9fa; }
        .card-title { font-size: 1.1rem; margin-bottom: 0.25rem; }
        .card-subtitle { font-size: 0.85rem; }
        .card-body { padding: 1rem; }

        .color-preview {
            width: 30px; height: 30px; border: 2px solid #ccc; display: inline-block;
            vertical-align: middle; margin-right: 10px; border-radius: 4px;
        }

        .table-responsive { font-size: 0.85rem; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table { margin-bottom: 0; }
        .table td, .table th { padding: 0.75rem; vertical-align: middle; }

        .action-buttons { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .action-buttons .btn { font-size: 0.8rem; padding: 0.4rem 0.8rem; }

        .form-group { margin-bottom: 1rem; }
        .form-group label { font-weight: 600; margin-bottom: 0.5rem; }
        .form-control { font-size: 1rem; }
        input[type="color"] { height: 50px; cursor: pointer; }

        .alert { border-radius: 8px; border: none; padding: 1rem 1.5rem; margin-bottom: 1.5rem; }

        .btn-primary, .btn-success { width: 100%; padding: 0.75rem; font-size: 1rem; font-weight: 600; }
        .btn-secondary { width: 100%; padding: 0.75rem; margin-top: 0.5rem; }

        .edit-card { display: none; }

        /* Einnahmen in grün */
        .income { color: #28a745 !important; font-weight: 600; }

        /* Summe-Box am Ende */
        .sum-box {
            margin: 2rem 0 1rem 0;
            padding: 1rem 1.25rem;
            border-radius: 10px;
            background: #f8f9fa;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .sum-title { font-weight: 700; margin-bottom: 0.25rem; }
        .sum-value { font-size: 1.4rem; font-weight: 800; }

        @media (min-width: 768px) {
            body { font-size: 16px; }
            .container { max-width: 760px; padding-left: 20px; padding-right: 20px; }
            .card-title { font-size: 1.3rem; }
            .navbar-brand { font-size: 1.3rem; }
            .btn-back { font-size: 0.95rem; padding: 0.5rem 1rem; }
            .table-responsive { font-size: 0.95rem; }
            .btn-primary, .btn-success { width: auto; min-width: 250px; }
            .btn-secondary { width: auto; min-width: 150px; margin-top: 0; margin-left: 0.5rem; }
            .color-preview { width: 40px; height: 40px; }
        }
        @media (min-width: 1025px) {
            .container { max-width: 1140px; padding-left: 15px; padding-right: 15px; }
            .navbar-brand { font-size: 1.5rem; }
            .btn-back { font-size: 1rem; padding: 0.5rem 1.5rem; }
            .card-title { font-size: 1.5rem; }
            .table-responsive { font-size: 1rem; }
            .action-buttons .btn { font-size: 0.9rem; padding: 0.5rem 1rem; }
        }

        @media (hover: none) and (pointer: coarse) {
            .btn { min-height: 44px; min-width: 44px; }
            .form-control { min-height: 44px; }
            .card { margin-bottom: 1.2rem; }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body { background-color: #121212; color: #ffffff; }
            .card { background-color: #1e1e1e; border-color: #333; }
            .card-header { background-color: #2a2a2a; border-bottom-color: #333; }
            .card-title { color: #ffffff; }
            .card-subtitle { color: #aaaaaa !important; }
            .table { color: #ffffff; }
            .table thead th { color: #ffffff; background-color: #2a2a2a; border-color: #444; }
            .table td { color: #e0e0e0; border-color: #444; }
            .table-striped tbody tr:nth-of-type(odd) { background-color: rgba(255,255,255,0.05); }
            .form-control { background-color: #2a2a2a; color: #ffffff; border-color: #444; }
            .form-control:focus { background-color: #2a2a2a; color: #ffffff; border-color: #667eea; }
            input[type="color"] { background-color: #2a2a2a; border-color: #444; }
            .form-group label { color: #ffffff; }
            .alert-success { background-color: #1a4d2e; color: #51cf66; border-color: #2d7a4a; }
            .alert-danger { background-color: #4a2020; color: #ff6b6b; border-color: #7a2d2d; }
            .text-muted { color: #aaaaaa !important; }
            .color-preview { border-color: #555; }
            .nav-tabs { border-bottom-color: #444; }
            .nav-tabs .nav-link { color: #aaaaaa; }
            .nav-tabs .nav-link.active { color: #667eea; }

            .sum-box { background: #1e1e1e; box-shadow: 0 1px 3px rgba(0,0,0,0.35); }
        }
    </style>
</head>
<body>

<div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <span class="navbar-brand">CashView - Konfiguration</span>
        <a class="btn btn-secondary btn-back" href="index.php?manId=<?=$mandant?>" role="button">Zurück</a>
    </nav>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?=$success_message?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?=$error_message?></div>
    <?php endif; ?>

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
        <div class="tab-pane fade show active" id="kategorien" role="tabpanel" aria-labelledby="kategorien-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Meine Kategorien</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Kategorien für Mandant <?=$mandant?></h6>
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
                            <?php foreach ($kategorien as $row): ?>
                                <?php
                                $color = explode(",", (string)$row["statscolor"]);
                                $rr = (int)($color[0] ?? 100);
                                $gg = (int)($color[1] ?? 100);
                                $bb = (int)($color[2] ?? 255);
                                $rgb = "rgb($rr,$gg,$bb)";
                                $is_own = ((int)$row["manId"] === $mandant);
                                ?>
                                <tr>
                                    <td><div class="color-preview" style="background-color: <?=$rgb?>;"></div></td>
                                    <td><?=h($row["bez"])?><?=$is_own ? "" : " <span class='text-muted'>(global)</span>"?></td>
                                    <td><?= (int)$row["sortorder"] ?></td>
                                    <td>
                                        <?php if ($is_own): ?>
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-primary"
                                                        onclick="editKategorie(<?= (int)$row['ID']?>,'<?= h($row['bez'])?>',<?= (int)$row['sortorder']?>,<?= $rr?>,<?= $gg?>,<?= $bb?>)">
                                                    Bearbeiten
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteKategorie(<?= (int)$row['ID']?>)">Löschen</button>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Nicht bearbeitbar</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($kategorien) === 0): ?>
                                <tr><td colspan="4" class="text-muted">Keine Kategorien vorhanden.</td></tr>
                            <?php endif; ?>
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
                        <input type="hidden" name="manId" value="<?=$mandant?>">
                        <input type="hidden" name="action" value="add_kategorie">

                        <div class="form-group">
                            <label>Bezeichnung</label>
                            <input type="text" class="form-control" name="bez" required>
                        </div>

                        <div class="form-group">
                            <label>Sortierung</label>
                            <input type="number" class="form-control" name="sortorder" value="10" required>
                        </div>

                        <div class="form-group">
                            <label>Farbe</label>
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
                        <input type="hidden" name="manId" value="<?=$mandant?>">
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
                                    <td><?=h($row["Bez"])?></td>
                                    <td><?=number_format((float)($row["Betrag"] ?? 0), 2, ',', '.')?> €</td>
                                    <td><?=number_format((float)($row["Grenze"] ?? 0), 2, ',', '.')?> €</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-primary"
                                                    onclick="editKonto(<?= (int)$row['id']?>,'<?= h($row['Bez'])?>',<?= (float)$row['Grenze']?>)">
                                                Bearbeiten
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteKonto(<?= (int)$row['id']?>)">Löschen</button>
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

            <!-- Neues Konto -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Neues Konto hinzufügen</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php">
                        <input type="hidden" name="manId" value="<?=$mandant?>">
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
                        <input type="hidden" name="manId" value="<?=$mandant?>">
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
        <div class="tab-pane fade" id="laufend" role="tabpanel" aria-labelledby="laufend-tab">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Meine laufenden Kosten</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Wiederkehrende Ausgaben / Einnahmen</h6>
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
                            <?php foreach ($laufendes as $row): ?>
                                <?php
                                $mod = (int)($row["modulo"] ?? 1);
                                $wert = (float)($row["Wert"] ?? 0);
                                $isIncome = ($wert < 0);
                                ?>
                                <tr>
                                    <td><?=h($row["Beschreibung"])?></td>
                                    <td class="<?= $isIncome ? 'income' : '' ?>">
                                        <?=number_format($wert, 2, ',', '.')?> €
                                    </td>
                                    <td><?=h($intervalle[$mod] ?? ($mod." Monate"))?></td>
                                    <td><?=h($row["KontoBez"] ?? "")?></td>
                                    <td><?=h($row["KatBez"] ?? "")?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-primary"
                                                onclick="editLaufend(
                                                    <?= (int)$row['id']?>,
                                                    '<?= h($row['Beschreibung'])?>',
                                                    <?= (float)$wert ?>,
                                                    <?= (int)$mod ?>,
                                                    <?= (int)($row['ktoID'] ?? 0) ?>,
                                                    <?= (int)($row['katID'] ?? 0) ?>
                                                )">Bearbeiten</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteLaufend(<?= (int)$row['id']?>)">Löschen</button>
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

            <!-- Edit laufende Kosten -->
            <div class="card edit-card" id="editLaufendCard">
                <div class="card-header">
                    <h5 class="card-title">Laufende Kosten bearbeiten</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php" id="editLaufendForm">
                        <input type="hidden" name="manId" value="<?=$mandant?>">
                        <input type="hidden" name="action" value="edit_laufend">
                        <input type="hidden" name="laufend_id" id="edit_laufend_id">

                        <div class="form-group">
                            <label>Beschreibung</label>
                            <input type="text" class="form-control" name="laufend_beschreibung" id="edit_laufend_beschreibung" required>
                        </div>

                        <div class="form-group">
                            <label>Betrag</label>
                            <input type="number" step="0.01" class="form-control" name="laufend_wert" id="edit_laufend_wert" required>
                            <small class="form-text text-muted">Negative Beträge = Einnahmen (werden grün angezeigt)</small>
                        </div>

                        <div class="form-group">
                            <label>Intervall (in Monaten)</label>
                            <select class="form-control" name="laufend_modulo" id="edit_laufend_modulo" required>
                                <?php foreach ($intervalle as $val => $label): ?>
                                    <option value="<?=$val?>"><?=h($label)?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Konto</label>
                            <select class="form-control" name="laufend_konto" id="edit_laufend_konto" required>
                                <option value="">Bitte wählen…</option>
                                <?php foreach ($konten as $k): ?>
                                    <option value="<?= (int)$k["id"] ?>"><?= h($k["Bez"]) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kategorie</label>
                            <select class="form-control" name="laufend_kategorie" id="edit_laufend_kategorie" required>
                                <option value="">Bitte wählen…</option>
                                <?php foreach ($kategorien as $ka): ?>
                                    <option value="<?= (int)$ka["ID"] ?>"><?= h($ka["bez"]) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                            <button type="button" class="btn btn-secondary" onclick="cancelEditLaufend()">Abbrechen</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Neue laufende Kosten -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Neue laufende Kosten hinzufügen</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="config.php">
                        <input type="hidden" name="manId" value="<?=$mandant?>">
                        <input type="hidden" name="action" value="add_laufend">

                        <div class="form-group">
                            <label>Beschreibung</label>
                            <input type="text" class="form-control" name="laufend_beschreibung" placeholder="z.B. Netflix Abo" required>
                        </div>

                        <div class="form-group">
                            <label>Betrag</label>
                            <input type="number" step="0.01" class="form-control" name="laufend_wert" placeholder="0.00" required>
                            <small class="form-text text-muted">Negative Beträge = Einnahmen</small>
                        </div>

                        <div class="form-group">
                            <label>Intervall (in Monaten)</label>
                            <select class="form-control" name="laufend_modulo" required>
                                <?php foreach ($intervalle as $val => $label): ?>
                                    <option value="<?=$val?>" <?=$val===1?'selected':''?>><?=h($label)?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Das Cronjob-Script läuft am Monatsersten</small>
                        </div>

                        <div class="form-group">
                            <label>Konto</label>
                            <select class="form-control" name="laufend_konto" required>
                                <option value="">Bitte wählen…</option>
                                <?php foreach ($konten as $k): ?>
                                    <option value="<?= (int)$k["id"] ?>"><?= h($k["Bez"]) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kategorie</label>
                            <select class="form-control" name="laufend_kategorie" required>
                                <option value="">Bitte wählen…</option>
                                <?php foreach ($kategorien as $ka): ?>
                                    <option value="<?= (int)$ka["ID"] ?>"><?= h($ka["bez"]) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="alert alert-info">
                            ℹ️ <strong>Hinweis:</strong> Laufende Kosten werden automatisch am Monatsersten gebucht.
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Laufende Kosten hinzufügen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div><!-- /.tab-content -->

    <!-- Ganz am Ende der Seite: Summe laufender Kosten (nur Ausgaben) -->
    <div class="sum-box">
        <div class="sum-title">Summe laufende Kosten (nur Ausgaben, ohne Einnahmen)</div>
        <div class="sum-value"><?= number_format($summe_laufend_pos, 2, ',', '.') ?> €</div>
        <div class="text-muted">Negative Beträge (Einnahmen) werden nicht mitgerechnet.</div>
    </div>

</div><!-- /.container -->

<!-- Bootstrap JS (optional). Wenn geblockt, greift der Fallback darunter. -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

<script>
/* ====== Farbpicker -> RGB Hidden Inputs ====== */
(function () {
    const cp = document.getElementById('colorpicker');
    const r = document.getElementById('color_r');
    const g = document.getElementById('color_g');
    const b = document.getElementById('color_b');
    if (cp && r && g && b) {
        cp.addEventListener('input', function(e) {
            const hex = e.target.value;
            r.value = parseInt(hex.substr(1,2), 16);
            g.value = parseInt(hex.substr(3,2), 16);
            b.value = parseInt(hex.substr(5,2), 16);
        });
        cp.dispatchEvent(new Event('input'));
    }

    const ecp = document.getElementById('edit_kat_colorpicker');
    if (ecp) {
        ecp.addEventListener('input', function(e) {
            const hex = e.target.value;
            const rr = document.getElementById('edit_kat_color_r');
            const gg = document.getElementById('edit_kat_color_g');
            const bb = document.getElementById('edit_kat_color_b');
            if (!rr || !gg || !bb) return;
            rr.value = parseInt(hex.substr(1,2), 16);
            gg.value = parseInt(hex.substr(3,2), 16);
            bb.value = parseInt(hex.substr(5,2), 16);
        });
    }
})();

function rgbToHex(r,g,b){
    const toHex = n => ('0' + Number(n).toString(16)).slice(-2);
    return '#' + toHex(r) + toHex(g) + toHex(b);
}

function postForm(fields) {
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = 'config.php';
    Object.keys(fields).forEach(k => {
        const i = document.createElement('input');
        i.type = 'hidden';
        i.name = k;
        i.value = fields[k];
        f.appendChild(i);
    });
    document.body.appendChild(f);
    f.submit();
}

/* Kategorien */
function editKategorie(id, bez, sortorder, r, g, b) {
    document.getElementById('edit_kat_id').value = id;
    document.getElementById('edit_kat_bez').value = bez;
    document.getElementById('edit_kat_sortorder').value = sortorder;

    document.getElementById('edit_kat_color_r').value = r;
    document.getElementById('edit_kat_color_g').value = g;
    document.getElementById('edit_kat_color_b').value = b;

    const cp = document.getElementById('edit_kat_colorpicker');
    if (cp) cp.value = rgbToHex(r,g,b);

    const card = document.getElementById('editKategorieCard');
    if (card) { card.style.display = 'block'; card.scrollIntoView({behavior:'smooth'}); }
}
function cancelEditKategorie() {
    const card = document.getElementById('editKategorieCard');
    if (card) card.style.display = 'none';
}
function deleteKategorie(id) {
    if (!confirm('Kategorie wirklich löschen?')) return;
    postForm({ manId: <?= (int)$mandant ?>, action: 'delete_kategorie', id: id });
}

/* Konten */
function editKonto(id, bez, grenze) {
    document.getElementById('edit_konto_id').value = id;
    document.getElementById('edit_konto_bez').value = bez;
    document.getElementById('edit_konto_grenze').value = grenze;

    const card = document.getElementById('editKontoCard');
    if (card) { card.style.display = 'block'; card.scrollIntoView({behavior:'smooth'}); }
}
function cancelEditKonto() {
    const card = document.getElementById('editKontoCard');
    if (card) card.style.display = 'none';
}
function deleteKonto(id) {
    if (!confirm('Konto wirklich löschen? Alle zugehörigen Initialwerte werden ebenfalls gelöscht!')) return;
    postForm({ manId: <?= (int)$mandant ?>, action: 'delete_konto', konto_id: id });
}

/* Laufende Kosten */
function editLaufend(id, beschreibung, wert, modulo, ktoID, katID) {
    document.getElementById('edit_laufend_id').value = id;
    document.getElementById('edit_laufend_beschreibung').value = beschreibung;
    document.getElementById('edit_laufend_wert').value = wert;
    document.getElementById('edit_laufend_modulo').value = String(modulo);

    // Selects setzen
    document.getElementById('edit_laufend_konto').value = String(ktoID);
    document.getElementById('edit_laufend_kategorie').value = String(katID);

    const card = document.getElementById('editLaufendCard');
    if (card) { card.style.display = 'block'; card.scrollIntoView({behavior:'smooth'}); }
}
function cancelEditLaufend() {
    const card = document.getElementById('editLaufendCard');
    if (card) card.style.display = 'none';
}
function deleteLaufend(id) {
    if (!confirm('Laufende Kosten wirklich löschen?')) return;
    postForm({ manId: <?= (int)$mandant ?>, action: 'delete_laufend', laufend_id: id });
}

/* ====== Tabs: Bootstrap wenn verfügbar, sonst Fallback ====== */
(function () {
    const hasBootstrapTabs = (window.jQuery && typeof jQuery.fn.tab === 'function');

    function setActiveTab(hash) {
        if (!hash) hash = '#kategorien';
        const links = document.querySelectorAll('#configTabs a.nav-link');
        const panes = document.querySelectorAll('#configTabsContent .tab-pane');

        links.forEach(a => {
            const isActive = (a.getAttribute('href') === hash);
            a.classList.toggle('active', isActive);
            a.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        panes.forEach(p => {
            const isActive = ('#' + p.id === hash);
            p.classList.toggle('show', isActive);
            p.classList.toggle('active', isActive);
        });
    }

    const initialHash = window.location.hash || '#kategorien';

    if (hasBootstrapTabs) {
        jQuery('#configTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = jQuery(e.target).attr('href');
            if (target) history.replaceState(null, '', target);
        });
        const $link = jQuery('#configTabs a[data-toggle="tab"][href="' + initialHash + '"]');
        if ($link.length) $link.tab('show');
    } else {
        setActiveTab(initialHash);
        document.querySelectorAll('#configTabs a.nav-link').forEach(a => {
            a.addEventListener('click', function (e) {
                const href = a.getAttribute('href');
                if (!href || !href.startsWith('#')) return;
                e.preventDefault();
                history.replaceState(null, '', href);
                setActiveTab(href);
            });
        });
        window.addEventListener('hashchange', function() {
            setActiveTab(window.location.hash);
        });
    }
})();
</script>

</body>
</html>
