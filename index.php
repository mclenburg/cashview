
<?php
require_once 'db.php';
$mandant = 1;
$kats = $db->query("SELECT ID, Bez FROM kategorien WHERE manId=$mandant AND sortorder<>999 ORDER BY sortorder");
?>
<h1>Übersicht</h1>
<a href="config.php">Konfiguration</a>
<select>
    <?php while($k = $kats->fetch_assoc()): ?>
        <option value="<?= $k['ID'] ?>"><?= htmlspecialchars($k['Bez']) ?></option>
    <?php endwhile; ?>
</select>
