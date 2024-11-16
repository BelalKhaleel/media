<?php
session_start();
require_once('./check-admin.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>actors</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body style="padding:1em;">
  <h1>Actors</h1>
  <form action="actors.php" method="get">
    <input type="text" name="search" placeholder="Search actor">
    <button type="submit">Search</button>
  </form>
  <form action="./Controllers/ActorController/addActor.php" method="post">
    <label for="actor-name">Actor Name:</label>
    <input type="text" name="actor-name" id="actor-name" placeholder="Enter actor name">
    <label for="gender">Gender:</label>
    <select name="gender" id="gender">
    <?php
      require_once('../connection.php');
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
    <label for="nationality">Nationality:</label>
    <select name="nationality" id="nationality">
    <?php
      $sql = "SELECT * FROM `nationalities`;";
      $stmt = $pdo->query($sql);
      $nationalities = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach($nationalities as $nationality) {
        ?>
        <option value="<?= $nationality['NationalityID'] ?>"><?= $nationality['NationalityName'] ?></option>
        <?php
      }
      ?>
    </select>
    <button type="submit">Add</button>
    <?php
    if(isset($_SESSION['add-actor'])) {
      if($_SESSION['add-actor']) {
      ?>
      <p style="color:green">Actor added successfully</p>
      <?php
      } else {
      ?>
      <p style="color:red">Invalid input</p>
      <?php
      }
    }
    unset($_SESSION['add-actor']);
    ?>
  </form>
  <?php
  $actors_per_page = 10;
  $sql = "SELECT COUNT(actors.ActorID) AS 'total_actors' FROM actors;";
  $stmt = $pdo->query($sql);
  $total_number_of_actors = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_number_of_actors = $total_number_of_actors['total_actors'];
  $number_of_pages = ceil($total_number_of_actors / $actors_per_page);
  if (isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page'])) {
    $limit = ($_GET['page'] - 1) * $actors_per_page;
  } else {
    $limit = 0;
  }
  if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim(strtolower($_GET['search']));
    $search_term = "%$search_term%";
    $sql = "SELECT actors.ActorID, actors.ActorName, genders.GenderName, nationalities.NationalityName
            FROM actors
            JOIN genders ON actors.GenderID = genders.GenderID
            JOIN nationalities ON actors.NationalityID = nationalities.NationalityID
            WHERE LOWER(actors.ActorName) LIKE :search_term
            ORDER BY ActorID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':search_term', $search_term);
    $stmt->execute();
    $actors = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $sql = "SELECT actors.ActorID, actors.ActorName, genders.GenderName, nationalities.NationalityName
            FROM actors
            JOIN genders ON actors.GenderID = genders.GenderID
            JOIN nationalities ON actors.NationalityID = nationalities.NationalityID
            ORDER BY ActorID
            LIMIT :limit, $actors_per_page;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $actors = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  if (!$actors) {
    echo "No actors found";
  } else {
  ?>
  <table class="table table-striped">
    <thead>
      <th>Actor ID</th>
      <th>Actor Name</th>
      <th>Gender</th>
      <th>Nationality</th>
      <th>Actions</th>
    </thead>
    <tbody>
      <?php
      foreach($actors as $actor) {
        ?>
        <tr>
          <td><?= $actor['ActorID'] ?></td>
          <td><?= $actor['ActorName'] ?></td>
          <td><?= $actor['GenderName'] ?></td>
          <td><?= $actor['NationalityName'] ?></td>
          <td style="display:flex; gap: 10px;">
            <form action="./Controllers/ActorController/deleteActor.php" method="post">
              <input type="hidden" name="id" value="<?= $actor['ActorID'] ?>">
              <input type="hidden" name="page" value="<?= $_GET['page'] ?? 1 ?>">
              <button type="submit" class="btn btn-danger">DELETE</button>
            </form>
            <form action="./edit-actor.php" method="post">
              <input type="hidden" name="id" value="<?= $actor['ActorID'] ?>">
              <input type="hidden" name="name" value="<?= $actor['ActorName'] ?>">
              <input type="hidden" name="gender" value="<?= $actor['GenderName'] ?>">
              <input type="hidden" name="nationality" value="<?= $actor['NationalityName'] ?>">
              <input type="hidden" name="page" value="<?= $_GET['page'] ?? 1 ?>">
              <button type="submit" class="btn btn-primary">UPDATE</button>
            </form>
          </td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
  <?php
  $webpage = 'actors';
  require_once('../pagination.php');
  }
  ?>
</body>
</html>