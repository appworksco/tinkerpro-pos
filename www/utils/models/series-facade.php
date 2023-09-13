<?php

class SeriesFacade extends DBConnection {

  public function getLatestSeries() {
    $sql = $this->connect()->prepare("SELECT series FROM series ORDER BY series DESC LIMIT 1 ");
    $sql->execute();
    return $sql;
  }

  public function updateSeries() {
    $sql = $this->connect()->prepare("UPDATE series SET series = series + 1");
    $sql->execute();
    return $sql;
  }

}

?>