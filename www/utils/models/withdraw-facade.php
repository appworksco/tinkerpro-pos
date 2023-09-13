<?php

class WithdrawFacade extends DBConnection {

  public function withdrawAmount($amount, $withdrawBy, $date) {
    $sql = $this->connect()->prepare("INSERT INTO withdraw(amount, withdraw_by, date) VALUES (?, ?, ?)");
    $sql->execute([$amount, $withdrawBy, $date]);
    return $sql;
  }

}

?>