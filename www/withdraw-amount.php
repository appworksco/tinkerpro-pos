<?php

require( __DIR__ . '../../vendor/autoload.php');
include( __DIR__ . '/utils/db/connector.php');
include( __DIR__ . '/utils/models/withdraw-facade.php');

use Mike42\Escpos;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintBuffers\ImagePrintBuffer;
use Mike42\Escpos\CapabilityProfiles\DefaultCapabilityProfile;
use Mike42\Escpos\CapabilityProfiles\SimpleCapabilityProfile;

$connector = new WindowsPrintConnector("smb://DESKTOP-6HNGOTO/xprinter");
$printer = new Printer($connector);

$withdrawFacade = new WithdrawFacade;

if (isset($_GET["first_name"])) {
  $firstName = $_GET["first_name"];
}
if (isset($_GET["last_name"])) {
  $lastName = $_GET["last_name"];
}
if (isset($_GET["amount"])) {
  $amount = $_GET["amount"];
}
if (isset($_GET["withdraw_by"])) {
  $withdrawBy = $_GET["withdraw_by"];
}
if (isset($_GET["date"])) {
  $date = $_GET["date"];
}

$withdrawAmount = $withdrawFacade->withdrawAmount($amount, $withdrawBy, $date);
if ($withdrawAmount) {
  $printer -> pulse();
  $printer -> close();
  header("Location: home.php?first_name=" . $firstName . "&last_name=" . $lastName);
}

?>