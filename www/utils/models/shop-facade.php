<?php

class ShopFacade extends DBConnection {

  public function fetchShop() {
    $sql = $this->connect()->prepare("SELECT * FROM shop");
    $sql->execute();
    return $sql;
  }

}

?>