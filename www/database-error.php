<?php
include( __DIR__ . '/layout/header.php');
?>

<main class="form-wrapper">
  <div class="d-flex align-items-center justify-content-center w-100">
    <div class="container">
      <div class="col-12">
        <h1 class="display-4 text-white">Database Error!</h1>
        <p class="lead text-white">This either means that the username and password information in connector.php file is incorrect or we can't contact the database server at localhost. this could mean that your database server is down.</p>
      </div>
    </div>
  </div>
</main>

<?php
include('./layout/footer.php');
?>