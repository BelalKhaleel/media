<?php
session_start();
require_once('./check-admin.php');

if (
      isset($_POST['id']) 
  && !empty($_POST['id']) 
  && is_numeric($_POST['id'])
  && isset($_POST['name'])
  && !empty($_POST['name'])
  && isset($_POST['gender'])
  && !empty($_POST['gender'])
  && isset($_POST['nationality'])
  && !empty($_POST['nationality'])
  ) {
  $id = $_POST['id'];
  $actor_name = $_POST['name'];
  $actor_gender = $_POST['gender'];
  $actor_nationality = $_POST['nationality'];
  $page = $_POST['page'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Actor</title>
</head>
    <body>
      <h1>Edit Actor</h1>
      <form action="./Controllers/ActorController/updateActor.php" method="post">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="page" value="<?= $page ?>">
        <label for="actor-name">Actor Name:</label>
        <input type="text" name="actor-name" id="actor-name" placeholder="Enter actor name" value="<?= $actor_name ?>">
        <label for="gender">Gender:</label>
        <select name="gender" id="gender">
        <?php
          require_once('../connection.php');
          $sql = "SELECT * FROM `genders`;";
          $stmt = $pdo->query($sql);
          $genders = $stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach($genders as $gender) {
            if ($gender['GenderName'] === $actor_gender) {
            ?>
            <option value="<?= $gender['GenderID'] ?>" selected ><?= $gender['GenderName'] ?></option>
            <?php
            } else {
            ?>
            <option value="<?= $gender['GenderID'] ?>"><?= $gender['GenderName'] ?></option>
            <?php
            }
          }
          ?>
        </select>
        <label for="nationality">Nationality:</label>
        <select name="nationality" id="nationality">
        <?php
          $sql = "SELECT * FROM `nationalities`;";
          $stmt = $pdo->query($sql);
          $nationalities = $stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach($nationalities as $nationality) {
            if ($nationality['NationalityName'] === $actor_nationality) {
            ?>
            <option value="<?= $nationality['NationalityID'] ?>" selected ><?= $nationality['NationalityName'] ?></option>
            <?php
            } else {
            ?>
            <option value="<?= $nationality['NationalityID'] ?>"><?= $nationality['NationalityName'] ?></option>
            <?php
            }
          }
          ?>
        </select>
        <button type="submit">Edit</button>
      </form>
    </body>
  <?php
  } else {
    die("Actor not found");
  }
  ?>
</html>