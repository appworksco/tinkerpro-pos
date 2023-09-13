<?php 

session_start();

require( __DIR__ . '../../vendor/autoload.php');
include( __DIR__ . '/layout/header.php');
include( __DIR__ . '/utils/db/connector.php');
include( __DIR__ . '/utils/models/shop-facade.php');
include( __DIR__ . '/utils/models/series-facade.php');
include( __DIR__ . '/utils/models/product-facade.php');
include( __DIR__ . '/utils/models/transaction-facade.php');

use Mike42\Escpos;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintBuffers\ImagePrintBuffer;
use Mike42\Escpos\CapabilityProfiles\DefaultCapabilityProfile;
use Mike42\Escpos\CapabilityProfiles\SimpleCapabilityProfile;

$connector = new WindowsPrintConnector("smb://TRANSAK-POS1/xprinter");
$printer = new Printer($connector);
$shopFacade = new ShopFacade;
$seriesFacade = new SeriesFacade;
$productFacade = new ProductFacade;
$transactionFacade = new TransactionFacade;

$userId = 0;

if (isset($_SESSION["user_id"])) {
  $userId = $_SESSION["user_id"];
}
if (isset($_GET["first_name"])) {
  $firstName = $_GET["first_name"];
}
if (isset($_GET["last_name"])) {
  $lastName = $_GET["last_name"];
}
if (isset($_GET["payer"])) {
  $payer = $_GET["payer"];
}
if (isset($_GET["transaction_num"])) {
  $transactionNum = $_GET["transaction_num"];
}

// Get the shop info
$fetchShop = $shopFacade->fetchShop();
while ($row = $fetchShop->fetch(PDO::FETCH_ASSOC)) {
  $shopName = $row['shop_name'];
  $shopAddress = $row['shop_address'];
  $contactNumber = $row['contact_number'];
}

// Get the time and date
date_default_timezone_set("Asia/Manila");
$date = date("m/d/Y");
$time = date("h:i:sa");

// Add spaces for layout
function addSpaces($string = '', $valid_string_length = 0) {
  if (strlen($string) < $valid_string_length) {
    $spaces = $valid_string_length - strlen($string);
    for ($index1 = 1; $index1 <= $spaces; $index1++) {
      $string = $string . ' ';
    }
  }
  return $string;
}

$transactions = $transactionFacade->getTotal()->fetchAll();
foreach($transactions as $transaction) {
  $total = $transaction['total'];
}

// Print receipt
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> setEmphasis(true);
$printer -> setLineSpacing(10);
$printer -> text("$shopName\n");
$printer -> setEmphasis(false);
$printer -> text("$shopAddress\n");
$printer -> text("CN: $contactNumber\n");
$printer -> feed(1);
$printer -> setEmphasis(true);
$printer -> text("PAY LATER\n");
$printer -> setEmphasis(false);
$printer -> feed(1);
$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> text("Terminal : " . getenv('COMPUTERNAME') . "\n"); // Computer name should be TRANSAK-POS1
$printer -> text("Trans #  : $transactionNum\n");
$printer -> text("Cashier  : $firstName $lastName\n");
$printer -> text("Date     : $date\n");
$printer -> text("Time     : $time\n");
$printer -> feed(1);
$printer -> text("Payer    : $payer\n");
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> feed(1);
$printer -> setEmphasis(true);
$printer -> text(addSpaces('Item(s)', 20) . addSpaces('Subtotal', 10) . "\n");
$printer -> setEmphasis(false);
$printer -> text("------------------------------\n");
$printer -> feed(1);

$items = $transactionFacade->getTransactions()->fetchAll();
foreach ($items as $item) {
  //Current item ROW 1
  $name_lines = str_split($item['prod_qty'] . 'x' . number_format($item['prod_price'], 2) . ' - ' . $item['prod_desc'], 20);
  foreach ($name_lines as $k => $l) {
    $l = trim($l);
    $name_lines[$k] = addSpaces($l . ' ' , 20);
  }

  $subtotal = str_split($item['subtotal'], 10);
  foreach ($subtotal as $k => $l) {
    $l = trim($l);
    $subtotal[$k] = addSpaces(number_format($l, 2), 10);
  }
  $counter = 0;
  $temp = [];
  $temp[] = count($name_lines);
  $temp[] = count($subtotal);
  $counter = max($temp);

  for ($i = 0; $i < $counter; $i++) {
    $line = '';
    if (isset($name_lines[$i])) {
        $line .= ($name_lines[$i]);
    }
    if (isset($subtotal[$i])) {
        $line .= ($subtotal[$i]);
    }
    $printer->text($line . "\n");
  }
}
$printer -> feed(1);
$printer -> text("------------------------------ \n");
$printer -> text(addSpaces('TOTAL', 20) . addSpaces(number_format($total,2), 10) . "\n");
$printer -> feed(2);
$printer -> setJustification(Printer::JUSTIFY_LEFT);
$printer -> text("POS Supplier\n");
$printer -> text("Appworks Co.\n");
$printer -> text("P3 - Lunao, Gingoog City, 9014\n");
$printer -> text("Website  : www.appworksco.com\n");
$printer -> text("Mobile # : 09262130305\n");
$printer -> text("TIN #    : 611376341-000000\n");
$printer -> feed(2);
$printer -> setJustification(Printer::JUSTIFY_CENTER);
$printer -> text("KINDLY PAY YOUR BILLS!\n");
$printer -> cut();
$printer -> pulse();
$printer -> close();

$productFacade->decreaseStock();
$clearTransaction = $transactionFacade->clearTransactionPayLater();
$seriesFacade->updateSeries();

if ($clearTransaction) {
  header("Location: home.php?first_name=" . $firstName . "&last_name=" . $lastName);
}

?>