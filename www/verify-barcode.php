<?php

include( __DIR__ . '/utils/db/connector.php');
include( __DIR__ . '/utils/models/product-facade.php');
include( __DIR__ . '/utils/models/transaction-facade.php');

$productFacade = new ProductFacade;
$transactionFacade = new TransactionFacade;

$transactionNum = $_POST["transactionNum"];
$prodQty = $_POST["qty"];
$barcode = $_POST["barcode"];

$verifyBarcode = $productFacade->verifyBarcode($barcode);
$fetchProduct = $productFacade->fetchProduct($barcode);
if ($verifyBarcode == 1) {
  while ($row = $fetchProduct->fetch(PDO::FETCH_ASSOC)) {
    $prodId = $row['id'];
    $prodDesc = $row['prod_desc'];
    $prodPrice = $row['prod_price'];
    $subTotal = $prodQty * $prodPrice;
    $sales = $prodQty * $row['markup'];
    $date = date("Y-m-d");
    $addTransaction = $transactionFacade->addTransaction($transactionNum, $prodId, $prodQty, $prodDesc, $prodPrice, $subTotal, $sales, $date);
    // Update product quantity for inventory
    if ($addTransaction) {
      $subtractQuantity = $productFacade->subtractQuantity($prodQty, $barcode);
    }
  }
}

?>