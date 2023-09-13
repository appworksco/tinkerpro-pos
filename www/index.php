<?php

include( __DIR__ . '/layout/header.php');
include( __DIR__ . '/utils/db/connector.php');
include( __DIR__ . '/utils/models/user-facade.php');
include( __DIR__ . '/utils/models/shop-facade.php');

$userFacade = new UserFacade;
$shopFacade = new ShopFacade;

// If the login form is submitted
if (isset($_POST["login"])) {
  $username = $_POST["username"];
  $password = $_POST["password"];

  if (empty($username)) {
    array_push($invalid, 'Username should not be empty!');
  } if (empty($password)) {
    array_push($invalid, 'Password should not be empty!');
  } else {

    $verifyUsernameAndPassword = $userFacade->verifyUsernameAndPassword($username, $password);
    $login = $userFacade->login($username, $password);

    if ($verifyUsernameAndPassword > 0) {
      while ($row = $login->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $firstName = $row['first_name'];
        $lastName = $row['last_name'];
        header("Location: home.php?first_name=" . $firstName . "&last_name=" . $lastName);
      }
    } else {
      array_push($invalid, "Incorrect username or password!");
    }
  }
}

?>

<main class="form-wrapper">
  <div class="card form-card p-0">
    <div class="form px-5">
      <form action="index.php" method="post">
        <img src="./assets/img/transak-pos-logo.png" class="img-fluid py-4" alt="">
        <span class="display"></span>
        <div class="badge bg-danger w-100 mb-3">
          <p class="small m-0">Press the 'Esc' key to close the application.</p>
        </div>
        <?php include('errors.php') ?>
        <div class="form-floating">
          <input type="text" class="form-control" id="username" placeholder="Username" name="username" autofocus>
          <label for="username">Username</label>
        </div>
        <div class="form-floating">
          <input type="password" class="form-control" id="password" placeholder="Password" name="password" autofocus>
          <label for="password">Password</label>
        </div>
        <div class="date text-center py-5">
          <p class="m-0">Business Date</p>
          <h1><span id="dateDisplay"></span></h1>
        </div>
        <button type="submit" class="d-none" name="login"></button>
        <p class="small mb-3 text-center text-muted">www.appworksco.com | Transak POS <br> Retail Build</p>
      </form>
    </div>
  </div>
</main>

<?php include('./layout/footer.php') ?>

<script>
  function renderDate() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = mm + '/' + dd + '/' + yyyy;
    var myDate = document.getElementById('dateDisplay');
    myDate.textContent = today;
  }
  renderDate();

  document.addEventListener("keyup", function(e) {
    // Exit app
    if (e.key == 'Escape') {
      close();
    }
  })
</script>