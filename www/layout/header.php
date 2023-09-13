<?php 

session_start();
ob_start();

$invalid = array();
$success = array();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Transak POS">
  <meta name="author" content="Appworks Co.">
  <link rel="icon" type="image/png" href="img/fav.png">
  <!-- CSS -->
  <link rel="stylesheet" href="assets/vendor/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="assets/vendor/datatables/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <title>Transak POS</title>
</head>
<body>
  