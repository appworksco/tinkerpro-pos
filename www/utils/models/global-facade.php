<?php

class GlobalFacade extends DBConnection {

  // Redirect user to login page if not logged in
  public function notLoggedIn($userId) {
    if ($userId == 0 || $userId == NULL) {
      header("Location: login.php");
    }
  }
  // Redirect user to index page if logged in
  public function isLoggedIn($userId) {
    if ($userId != 0 || $userId != NULL) {
      header("Location: index.php");
    }
  }

  // Check the status of the database
  public function dbStatus() {
    $connected = $this->connect();
    if ($connected) {
      echo '<img src="./assets/icons/db-online.jpg" style="width: 20px; margin-bottom: 3px"> Online';
    }
  }

}

?>