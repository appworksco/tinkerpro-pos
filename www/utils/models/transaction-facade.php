<?php

class TransactionFacade extends DBConnection {

  public function getTransactions() {
    $sql = $this->connect()->prepare("SELECT * FROM transactions WHERE is_transact = '0'");
    $sql->execute();
    return $sql;
  }

  public function getTransactionsByNum($transactionNum) {
    $sql = $this->connect()->prepare("SELECT * FROM transactions WHERE transaction_num = '$transactionNum' AND is_transact = '1'");
    $sql->execute();
    return $sql;
  }

  public function getLatestTransactionNum() {
    $sql = $this->connect()->prepare("SELECT transaction_num FROM transactions WHERE is_transact = '1' ORDER BY transaction_num DESC LIMIT 1 ");
    $sql->execute();
    return $sql;
  }

  public function addTransaction($transactionNum, $prodId, $prodQty, $prodDesc, $prodPrice, $subTotal, $sales, $date) {
    $sql = $this->connect()->prepare("INSERT INTO transactions(transaction_num, prod_id, prod_qty, prod_desc, prod_price, subtotal, sales, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->execute([$transactionNum, $prodId, $prodQty, $prodDesc, $prodPrice, $subTotal, $sales, $date]);
    return $sql;
  }

  public function getTotal() {
    $sql = $this->connect()->prepare("SELECT SUM(subtotal) AS total FROM transactions WHERE is_transact = '0'");
    $sql->execute();
    return $sql;
  }

  public function getTotalByNum($transactionNum) {
    $sql = $this->connect()->prepare("SELECT SUM(subtotal) AS total FROM transactions WHERE transaction_num = '$transactionNum' AND is_transact = '1'");
    $sql->execute();
    return $sql;
  }

  public function clearTransaction() {
    $sql = $this->connect()->prepare("UPDATE transactions SET is_transact = '1', is_paid = '1'");
    $sql->execute();
    return $sql;
  }

  public function clearTransactionPayLater() {
    $sql = $this->connect()->prepare("UPDATE transactions SET is_transact = '1'");
    $sql->execute();
    return $sql;
  }

  public function voidProduct($prodId) {
    $sql = $this->connect()->prepare("DELETE FROM transactions WHERE prod_id = '$prodId'");
    $sql->execute();
    return $sql;
  }

  public function updatePayer($transactionNum, $payer) {
    $sql = $this->connect()->prepare("UPDATE transactions SET transact_type = '1', payer = '$payer' WHERE transaction_num = '$transactionNum'");
    $sql->execute();
    return $sql;
  }

}

?>