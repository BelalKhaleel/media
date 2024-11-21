<?php
session_start();
if (isset($_SESSION['login']) || isset($_SESSION['registration'])) {
  if ($_SESSION['admin']) {
    header("location:./admin/admin.php");
  } else {
    header("location:./index.php");
  }
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <title>Login</title>
</head>
<body style="padding: 2em; display: flex; flex-direction: column; align-items: center; ">
  <h1>Login</h1>
  <form action="./Auth/login.php" method="post" style="width: 30%; ">
    <div class="mb-3">
      <label for="username" class="form-label">Email address:</label>
      <input type="text" class="form-control" id="username" name="username" >
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password:</label>
      <input type="password" class="form-control" id="password" name="password" >
    </div>
    <div class="mb-3">
      <input type="checkbox" id="keep-me" name="keep-me" >
      <label for="keep-me" class="form-label">Keep me logged in</label>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
  <?php
  if (isset($_SESSION['credentials']) && !$_SESSION['credentials']) {
    ?>
    <p style="color: red;">Please fill in the required fields</p>
    <?php
  }
  if (isset($_SESSION['login']) && !$_SESSION['login']) {
    ?>
    <p style="color: red;">Incorrect username or password</p>
    <?php
  }
  unset($_SESSION['credentials']);
  unset($_SESSION['login']);
  ?>
</body>
</html>