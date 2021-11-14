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
     $config = array('user'=>$_POST['user'], 'passwd'=>$_POST['passwd']);
     file_put_contents('config.php', '<?php return ' . var_export($config, true) . ';');
   ?>
</body>
</html>