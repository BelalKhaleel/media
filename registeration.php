<?php
session_start();
if (isset($_SESSION['login']) || isset($_SESSION['registeration'])) {
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
  <title>Sign Up</title>
</head>
<body style="padding: 2em; display: flex; flex-direction: column; align-items: center; ">
  <form action="./Auth/registeration.php" method="post" style="width: 30%; ">
    <div class="mb-3">
      <label for="firstName" class="form-label">First Name:</label>
      <input type="text" class="form-control" id="firstName" name="firstName">
    </div>
    <div class="mb-3">
      <label for="lastName" class="form-label">Last Name:</label>
      <input type="text" class="form-control" id="lastName" name="lastName">
    </div>
    <div class="mb-3">
      <label for="gender">Gender:</label>
      <select name="gender" id="gender">
      <?php
        require_once('./connection.php');
        $sql = "SELECT * FROM `genders`;";
        $stmt = $pdo->query($sql);
        $genders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($genders as $gender) {
          ?>
          <option value="<?= $gender['GenderID'] ?>"><?= $gender['GenderName'] ?></option>
          <?php
        }
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label for="phone" class="form-label">Phone:</label>
      <input type="tel" class="form-control" id="phone" name="phone" >
    </div>
    <div class="mb-3">
      <label for="address" class="form-label">Address:</label>
      <input type="tel" class="form-control" id="address" name="address" >
    </div>
    <div class="mb-3">
      <label for="country">Country:</label>
      <select name="country" id="country">
      <?php
        $sql = "SELECT * FROM `countries`;";
        $stmt = $pdo->query($sql);
        $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($countries as $country) {
          ?>
          <option value="<?= $country['CountryID'] ?>"><?= $country['CountryName'] ?></option>
          <?php
        }
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label for="username" class="form-label">Username:</label>
      <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password:</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
  <?php
  if (isset($_SESSION['registeration']) && !$_SESSION['registeration']) {
    ?>
    <p style="color: red;">Please fill out the required fields</p>
    <?php
  }
  ?>
</body>
</html>