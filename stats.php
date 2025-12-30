
<?php
require_once 'db.php';
$mandant = 1;

$sql = "SELECT kat.Bez, kat.col_r, kat.col_g, kat.col_b, SUM(trans.amount) summe
        FROM transaktionen trans
        LEFT JOIN kategorien kat 
            ON trans.katID = kat.ID AND kat.manId=$mandant
        GROUP BY kat.ID";
$res = $db->query($sql);
?>
<h1>Statistik</h1>
<a href="index.php">Übersicht</a> | <a href="config.php">Konfiguration</a>
<ul>
<?php while($r = $res->fetch_assoc()): ?>
    <li style="color: rgb(<?= $r['col_r'] ?>,<?= $r['col_g'] ?>,<?= $r['col_b'] ?>)">
        <?= htmlspecialchars($r['Bez']) ?>: <?= $r['summe'] ?>
    </li>
<?php endwhile; ?>
</ul>
