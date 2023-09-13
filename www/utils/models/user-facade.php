<?php

class UserFacade extends DBConnection {

  public function login($username, $password) {
    $sql = $this->connect()->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $sql->execute([$username, $password]);
    return $sql;
  }

  public function verifyUsernameAndPassword($username, $password) {
    $sql = $this->connect()->prepare("SELECT username, password FROM users WHERE username = ? AND password = ?");
    $sql->execute([$username, $password]);
    $count = $sql->rowCount();
    return $count;
  }

  public function fetchShop() {
    $sql = $this->connect()->prepare("SELECT * FROM users");
    $sql->execute();
    return $sql;
  }

}

?>