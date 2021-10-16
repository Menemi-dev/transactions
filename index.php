<?php
require_once __DIR__.'/vendor/autoload.php';
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script type="text/javascript" src="public/assets/scripts.js"></script>
    </head>
    <body>
        <?php include_once "Templates/input-section.php"; ?>
        <?php include_once "Templates/comparison-section.php"; ?>
        <?php include_once "Templates/report-section.php"; ?>
    </body>
</html>