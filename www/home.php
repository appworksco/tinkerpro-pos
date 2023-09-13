<?php

include( __DIR__ . '/layout/header.php');
include( __DIR__ . '/utils/db/connector.php');
include( __DIR__ . '/utils/models/global-facade.php');
include( __DIR__ . '/utils/models/series-facade.php');
include( __DIR__ . '/utils/models/transaction-facade.php');
include( __DIR__ . '/utils/models/withdraw-facade.php');
include( __DIR__ . '/utils/models/product-facade.php');
include( __DIR__ . '/utils/models/shop-facade.php');

$globalFacade = new GlobalFacade;
$seriesFacade = new SeriesFacade;
$transactionFacade = new TransactionFacade;
$withdrawFacade = new WithdrawFacade;
$productFacade = new ProductFacade;
$shopFacade = new ShopFacade;

$userId = 0;
$isTransact = 0;
if (isset($_SESSION["user_id"])) {
  $userId = $_SESSION["user_id"];
}
if (isset($_GET["is_transact"])) {
  $isTransact = $_GET["is_transact"];
}
if (isset($_GET["first_name"])) {
  $firstName = $_GET["first_name"];
}
if (isset($_GET["last_name"])) {
  $lastName = $_GET["last_name"];
}

// Get the shop info
$fetchShop = $shopFacade->fetchShop();
while ($row = $fetchShop->fetch(PDO::FETCH_ASSOC)) {
  $shopName = $row['shop_name'];
}

// Transact payment for cash
if (isset($_POST["transact"])) {
  $amount = $_POST["amount"];
  $total = $_POST["total"];

  if ($amount >= $total ) {
    $change = $amount - $total;
  ?>

  <!-- Transaction info modal -->
  <div class="modal" id="changeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-border">
        <div class="modal-header bg-gradient-to-right">
          <h5 class="modal-title text-white">Transaction Info</h5>
        </div>
        <div class="modal-body">
          <form method="post">
            <div class="d-flex justify-content-between">
              <div class="w-100">
                <h4 class="float-start">Amount:</h4>
                <h4 class="money-font text-end pt-1">&#8369; <?= number_format($amount, 2) ?></h4>
              </div>
            </div>
            <div class="d-flex justify-content-between">
              <div class="w-100">
                <h4 class="float-start">Total:</h4>
                <h4 class="money-font text-end pt-1">&#8369; <?= number_format($total, 2) ?></h4>
              </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
              <div class="w-100">
                <h1 class="text-warning money-font float-start">Change:</h1>
                <h1 class="text-warning money-font float-end">&#8369; <?= number_format($change, 2) ?></h1>
              </div>
            </div>
            <!-- Hidden values -->
            <input type="hidden" name="amount" value="<?= $amount ?>">
            <input type="hidden" name="total" value="<?= $total ?>">
            <input type="hidden" name="change" value="<?= $change ?>">
            <?php
              $getLatestSeries = $seriesFacade->getLatestSeries(); 
              foreach($getLatestSeries as $series) { ?>
              <input type="hidden" name="transaction_num" value="<?= date('mdy') . $series['series'] ?>">
            <?php } ?>
            <button type="submit" class="d-none" id="saveTransactionPayCash" name="save_transaction_pay_cash"></button>
          </form>
          <div class="modal-footer">
            <p class="lead text-end">[ENTER] - PRINT RECEIPT</p>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php } }

// Process pay later transactions
if (isset($_POST["process"])) {
  $total = $_POST["total"];
  $payer = $_POST["payer"];

  if (!empty($payer)) {
  ?>

  <!-- Transaction info modal -->
  <div class="modal" id="payLaterInfoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-border">
        <div class="modal-header bg-gradient-to-right">
          <h5 class="modal-title text-white">Transaction Info</h5>
        </div>
        <div class="modal-body">
          <form method="post">
            <div class="d-flex justify-content-between">
              <div class="w-100">
                <h4 class="float-start">Total:</h4>
                <h4 class="money-font text-end pt-1">&#8369; <?= number_format($total, 2) ?></h4>
              </div>
            </div>
            <div class="d-flex justify-content-between">
              <div class="w-100">
                <h4 class="float-start">Payer:</h4>
                <h4 class="money-font text-end pt-1"><?= $payer ?></h4>
              </div>
            </div>
            <!-- Hidden values -->
            <input type="hidden" name="total" value="<?= $total ?>">
            <input type="hidden" name="payer" value="<?= $payer ?>">
            <?php
              $getLatestSeries = $seriesFacade->getLatestSeries(); 
              foreach($getLatestSeries as $series) { ?>
              <input type="hidden" name="transaction_num" value="<?= date('mdy') . $series['series'] ?>">
            <?php } ?>
            <button type="submit" class="d-none" id="saveTransactionPayLater" name="save_transaction_pay_later"></button>
          </form>
          <div class="modal-footer">
            <p class="lead text-end">[ENTER] - PRINT RECEIPT</p>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php } }

// Void product
if (isset($_POST["void_product"])) {
  $prodId = $_POST["product_id"];
  $voidProduct = $transactionFacade->voidProduct($prodId);
  if ($voidProduct) {
    $productFacade->increaseStockById($prodId);
  }
}

// Void transaction
if (isset($_POST["void_transaction"])) {
  $clearTransaction = $transactionFacade->clearTransaction();
  if ($clearTransaction) {
    $productFacade->increaseStock();
  }
}

// Save transaction pay cash
if (isset($_POST["save_transaction_pay_cash"])) {
  $amount = $_POST["amount"];
  $change = $_POST["change"];
  $transactionNum = $_POST["transaction_num"];
  // print receipt
  header("Location: http://localhost/transak-pos/www/pay-cash-receipt.php?first_name=" . $firstName . "&last_name=" . $lastName . "&amount=" . $amount . "&change=" . $change . "&transaction_num=" . $transactionNum);
}

// Save transaction pay later
if (isset($_POST["save_transaction_pay_later"])) {
  $payer = $_POST["payer"];
  $transactionNum = $_POST["transaction_num"];
  $transactionFacade->updatePayer($transactionNum, $payer);
  // print receipt
  header("Location: http://localhost/transak-pos/www/pay-later-receipt.php?first_name=" . $firstName . "&last_name=" . $lastName . "&payer=" . $payer . "&transaction_num=" . $transactionNum);
}

// Withdraw
if (isset($_POST["withdraw"])) {
  $amount = $_POST["withdraw_amount"];
  $withdrawBy = $firstName . ' ' . $lastName;
  $date = date('Y-m-d');
  header("Location: http://localhost/transak-pos/www/withdraw-amount.php?first_name=" . $firstName . "&last_name=" . $lastName . "&amount=" . $amount . "&withdraw_by=" . $withdrawBy . "&date=" . $date);
}

// Reprint
if (isset($_POST["reprint"])) {
  $transactionNum = $_POST["transaction_num"];
  header("Location: http://localhost/transak-pos/www/reprint-receipt.php?first_name=" . $firstName . "&last_name=" . $lastName . "&transaction_num=" . $transactionNum);
}

?>

<!-- POS Header Start -->
<div class="pos-main-wrapper">
  <div class="container-fluid"> 
    <div class="row">
      <!-- Col 8 -->
      <div class="col-lg-8 col-md-8 col-sm-8 pe-0">
        <!-- POS Barcode Start -->
        <div class="pos-barcode pt-4">
          <div class="container-fluid">
            <form method="post">
              <div class="row">
                <div class="col-lg-1 col-md-1 col-sm-1 pe-0">
                  <input type="number" id="qty" class="form-control w-100" placeholder="Qty" min="1" value="1">
                </div>
                <div class="col-lg-11 col-md-11 col-sm-11">
                  <input type="number" id="barcode" class="form-control w-100" placeholder="Barcode" autofocus>
                  <input type="hidden" id="firstName" value="<?= $firstName ?>">
                  <input type="hidden" id="lastName" value="<?= $lastName ?>">

                  <?php
                    $getLatestSeries = $seriesFacade->getLatestSeries(); 
                    foreach($getLatestSeries as $series) { ?>
                  <input type="hidden" id="transactionNum" value="<?= date('mdy') . $series['series'] ?>">
                  <?php } ?>
                </div>
              </div>
              <div id="counter" class="d-none"></div>
            </form>
          </div>
        </div>
        <!-- POS Barcode End -->
        <!-- POS Transaction Body Start -->
        <div class="pos-transaction-body mt-3">
          <div class="pos-transaction">
            <!-- POS Transaction Start -->
            <div class="container-fluid p-0">
              <table class="table table-borderless">
                <thead>
                  <tr style="border: 2px solid #ff7d00">
                    <th class="text-custom-warning">Code</th>
                    <th class="text-custom-warning">Item / Description</th>
                    <th class="text-custom-warning">Qty</th>
                    <th class="text-custom-warning">Price</th>
                    <th class="text-custom-warning">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $transactions = $transactionFacade->getTransactions()->fetchAll();
                    foreach($transactions as $transaction) { ?>
                  <tr class="text-white">
                    <td class="py-0"><?= $transaction["prod_id"] ?></td>
                    <td class="py-0"><?= $transaction["prod_desc"] ?></td>
                    <td class="py-0"><?= $transaction["prod_qty"] ?></td>
                    <td class="py-0"><?= number_format($transaction["prod_price"], 2) ?></td>
                    <td class="py-0"><?= number_format($transaction["subtotal"], 2) ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
          <!-- POS Transaction End -->
        </div>
        <!-- POS Transaction Body End -->
        <table class="table table-borderless">
          <tbody class="text-custom-warning" style="border: 2px solid #ff7d00">
            <tr>
              <td class="py-0">Amount</td>
              <td class="py-0 float-end">
              <?php
              $transactions = $transactionFacade->getTotal()->fetchAll();
              foreach($transactions as $transaction) {
                if ($transaction["total"] == NULL) {
                  echo '&#8369; <span id="total">0.00</span>';
                } else {
                  echo '<input type="hidden" value="' . number_format($transaction["total"], 2) .'"' . '>';
                  echo '&#8369; <span id="total">' . number_format($transaction["total"], 2) . '</span>';
                  $total = $transaction["total"];
                } ?>
              <?php } ?>
              </td>
            </tr>
            <tr>
              <td class="py-0">Discount</td>
              <td class="py-0 float-end">Discount</td>
            </tr>
            <tr>
              <td class="py-0">VAT</td>
              <td class="py-0 float-end">Discount</td>
            </tr>
            <tr>
              <td class="py-0">Total</td>
              <td class="py-0 float-end">Discount</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Col 4 -->
      <div class="col-lg-4 col-md-4 col-sm-4">
        <img src="./assets/img/tp-light-logo.png" class="img-fluid" alt="Tinker Pro Logo">
        <div class="row">
          <div class="col-lg-4 pb-2 pe-0">
            <div class="card bg-custom-dark">
              <p>asdadasd</p>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="card bg-custom-dark">
              <p>asdadasd</p>
            </div>
          </div>
        </div>
        <div class="card bg-custom-dark">
          <div class="card-body small">
            <p class="m-0"><span class="text-custom-lighter">Store:</span> <span class="fst-italic text-custom-light"><?= $shopName ?></span></p>
            <p class="m-0"><span class="text-custom-lighter">Cashier:</span> <span class="fst-italic text-custom-light"><?= $firstName . ' ' . $lastName ?></span></p>
            <p class="m-0"><span class="text-custom-lighter">ID No:</span> <span class="fst-italic text-custom-light"></span></p>
            <p class="m-0"><span class="text-custom-lighter">Up Time:</span> <span class="fst-italic text-custom-light"><?= shell_exec('uptime -p') ?></span></p>
            <br>
            <p class="m-0"><span class="text-custom-lighter">Date &amp; Time:</span> <span id="dateDisplay" class="fst-italic text-custom-light" style="margin-right: 2px"></span> <span class="fst-italic text-custom-light"> | </span> <span id="clockDisplay" class="fst-italic text-custom-light"></span></p>
            <p class="m-0"><span class="text-custom-lighter">Machine Name:</span> <span class="fst-italic text-custom-light"><?= getenv('COMPUTERNAME') . '(Local)' ?></span></p>
            <p class="m-0"><span class="text-custom-lighter">Connection Status:</span> <span class="fst-italic text-custom-light">Offline</span></p>
            <p class="m-0"><span class="text-custom-lighter">Device ID:</span> <span class="fst-italic text-custom-light"></span></p>
            <p class="m-0"><span class="text-custom-lighter">Software Version:</span> <span class="fst-italic text-custom-light"></span></p>
          </div>
        </div>
  
      </div>
    </div>
  </div>
</div>
<!-- POS Header End -->

<!-- Help modal -->
<div class="modal" id="helpModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content custom-border">
      <div class="modal-header bg-gradient-to-right">
        <h5 class="modal-title text-white">About Transak POS</h5>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-6">
            <p class="small"><span class="fw-bold">Transak POS</span> <br> Version 1.0 (RGP) <br> Appworks Co.</p></div>
          <div class="col-6">
            <p class="small">www.appworksco.com<br> info.transakpos@gmail.com <br> 09262130305</p>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <p class="fw-bold small">Help / Information</p>
            <div class="row">
              <div class="col-4">
                <p class="small"><span class="fw-bold">F1</span> - Help <br> <span class="fw-bold">F2</span> - Search <br> <span class="fw-bold">F3</span> - Void Product <br> <span class="fw-bold">F4</span> - Void Transaction</p>
              </div>
              <div class="col-4">
                <p class="small"><span class="fw-bold">F5</span> - Pay Cash <br> <span class="fw-bold">F6</span> - Pay Later <br> <span class="fw-bold">F7</span> - Withdraw <br> <span class="fw-bold">F8</span> - Reprint</p>
              </div>
              <div class="col-4">
                <p class="small"><span class="fw-bold">F11</span> - Load Data <br> <span class="fw-bold">F12</span> - Logout</p>
              </div>
            </div>
            <p class="small text-center m-0 mt-2">Copyright © 2023 Transak POS. All Rights Reserved.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Search product modal -->
<div class="modal" id="searchProductModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content custom-border">
      <div class="modal-header bg-gradient-to-right">
        <h5 class="modal-title text-white">Search Product</h5>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table id="searchProductTable" class="table table-striped" style="width:100%">
            <thead>
              <tr>
                <th>Barcode</th>
                <th>Product Description</th>
                <th>Product Price</th>
              </tr>
            </thead>
            <tbody>
              <?php   
                $fetchProducts = $productFacade->fetchProducts();
                while ($row = $fetchProducts->fetch(PDO::FETCH_ASSOC)) { ?>
                  <tr>
                    <td><?= $row["barcode"] ?></td>
                    <td><?= $row["prod_desc"] ?></td>
                    <td><?= $row["prod_price"] ?></td>
                  </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="modal-footer px-0">
          <div class="row w-100">
            <div class="col-6">
              <p class="lead text-start mb-0">[ESC] - Cancel</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Void transaction modal -->
<div class="modal" id="voidTransactionModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content custom-border">
      <div class="modal-header bg-gradient-to-right">
        <h5 class="modal-title text-white">Void Transaction</h5>
      </div>
      <div class="modal-body">
        <form method="post">
          <p class="text-center">Are you sure you want to void the transaction?</p>
          <button type="submit" class="d-none" id="voidTransaction" name="void_transaction"></button>
        </form>
        <div class="modal-footer px-0">
          <div class="row w-100">
            <div class="col-6">
              <p class="lead text-start mb-0">[ESC] - Cancel</p>
            </div>
            <div class="col-6">
              <p class="lead text-end mb-0">[ENTER] - Void</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Void product modal -->
<div class="modal" id="voidProductModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content custom-border">
      <div class="modal-header bg-gradient-to-right">
        <h5 class="modal-title text-white">Void Product</h5>
      </div>
      <div class="modal-body">
        <form method="post">
          <p class="text-center">Enter the ID that you would like to void</p>
          <input type="number" class="form-control" id="productId" name="product_id" required autofocus>
          <button type="submit" class="d-none" id="voidProduct" name="void_product"></button>
        </form>
        <div class="modal-footer px-0">
          <div class="row w-100">
            <div class="col-6">
              <p class="lead text-start mb-0">[ESC] - Cancel</p>
            </div>
            <div class="col-6">
              <p class="lead text-end mb-0">[ENTER] - Void</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Withdraw modal -->
<div class="modal" id="withdrawModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content custom-border">
      <div class="modal-header bg-gradient-to-right">
        <h5 class="modal-title text-white">Withdraw</h5>
      </div>
      <div class="modal-body">
        <form method="post">
          <p class="text-center">Enter the amount that you would like to withdraw</p>
          <input type="number" class="form-control" id="withdrawAmount" name="withdraw_amount" required autofocus>
          <input type="hidden" name="withdraw_by" value="<?= $firstName . ' ' . $lastName ?>">
          <button type="submit" class="d-none" id="withdraw" name="withdraw"></button>
        </form>
        <div class="modal-footer px-0">
          <div class="row w-100">
            <div class="col-6">
              <p class="lead text-start mb-0">[ESC] - Cancel</p>
            </div>
            <div class="col-6">
              <p class="lead text-end mb-0">[ENTER] - Withdraw</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Reprint modal -->
<div class="modal" id="reprintModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content custom-border">
      <div class="modal-header bg-gradient-to-right">
        <h5 class="modal-title text-white">Reprint</h5>
      </div>
      <div class="modal-body">
        <form method="post">
          <p class="text-center">Enter the transaction number that you would like to reprint</p>
          <?php   
            $getLatestTransactionNum = $transactionFacade->getLatestTransactionNum(); 
            foreach($getLatestTransactionNum as $transactionNum) { ?>
          <input type="number" class="form-control" id="transactionNumReprint" name="transaction_num" value="<?= $transactionNum['transaction_num']; ?>" required autofocus>
          <?php } ?>
          <button type="submit" class="d-none" id="reprint" name="reprint"></button>
        </form>
        <div class="modal-footer px-0">
          <div class="row w-100">
            <div class="col-6">
              <p class="lead text-start mb-0">[ESC] - Cancel</p>
            </div>
            <div class="col-6">
              <p class="lead text-end mb-0">[ENTER] - Reprint</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Load data modal -->
<div class="modal" id="loadDataModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content custom-border">
      <div class="modal-header bg-gradient-to-right">
        <h5 class="modal-title text-white">Load Data</h5>
      </div>
      <div class="modal-body">
        <p class="text-center">Loading data from server...</p>
        <div id="myProgress">
          <div id="myBar"></div>
        </div>
        <button type="submit" class="d-none" id="loadData" onclick="move()"></button>
      </div>
    </div>
  </div>
</div>

<!--Logout modal -->
<div class="modal" id="logoutModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content custom-border">
      <div class="modal-header bg-gradient-to-right">
        <h5 class="modal-title text-white">Logout</h5>
      </div>
      <div class="modal-body">
        <form method="post">
          <p class="text-center">Are you sure you want to logout?</p>
        </form>
        <div class="modal-footer px-0">
          <div class="row w-100">
            <div class="col-6">
              <p class="lead text-start mb-0">[ESC] - Cancel</p>
            </div>
            <div class="col-6">
              <p class="lead text-end mb-0">[ENTER] - Logout</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('./layout/footer.php') ?>