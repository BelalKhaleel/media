<?php
session_start();
require_once('./check-admin.php');
require_once("../connection.php");
require_once("../trim.php");

if (
  $_SERVER['REQUEST_METHOD'] === 'POST'
 && isset($_POST['id'])
 && isset($_POST['movie-title'])
 && isset($_POST['production-year'])
 && isset($_POST['unit-price'])
 && isset($_POST['quantity'])
 && isset($_POST['director-name'])
 && isset($_POST['categories'])
 && isset($_POST['actors'])
 && isset($_POST['image'])
 && isset($_POST['page'])
 && !empty(trim($_POST['movie-title']))
 && !empty(trim($_POST['production-year']))
 && !empty(trim($_POST['director-name']))
 && !empty($_POST['categories'])
 && !empty($_POST['actors'])
 && is_numeric($_POST['id'])
 && is_numeric($_POST['page'])
 && is_numeric($_POST['production-year'])
 && is_numeric($_POST['quantity'])
 && is_numeric($_POST['unit-price'])
 && $_POST['production-year'] >= 1888
 && $_POST['production-year'] <= date("Y")
 && $_POST['id'] > 0
 && $_POST['page'] > 0
 && $_POST['unit-price'] >= 0
 && $_POST['quantity'] >= 0
) {
  trim_array($_POST);
  $movie_id = $_POST['id'];
  $page = $_POST['page'];
  $movie_title = $_POST['movie-title'];
  $production_year = filter_var($_POST['production-year'], FILTER_VALIDATE_INT);
  $unit_price = filter_var($_POST['unit-price'], FILTER_VALIDATE_FLOAT);
  $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
  $director_name = $_POST['director-name'];
  $movie_categories = explode(", ", $_POST['categories']);
  $movie_actors = explode(", ", $_POST['actors']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Movie</title>
  <style>
    body {
      padding: 0 25em;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
  </style>
</head>
<body>
  <form action="./Controllers/MovieController/editMovie.php" method="post" id="add-movie" enctype="multipart/form-data" >
      <h2>Edit Movie</h2>
      <input type="hidden" name="id" value="<?= $movie_id ?>">
      <input type="hidden" name="page" value="<?= $page ?>">
      <label for="movie-title">Movie Title:</label>
      <input type="text" id="movie-title" name="movie-title" value="<?= $movie_title ?>">
      <label for="production-year">Production Year:</label>
      <input type="number" id="production-year" name="production-year" min="1888" max="<?= date("Y") ?>" value="<?= $production_year ?>">
      <label for="unit-price">Price:</label>
      <input type="number" id="unit-price" name="unit-price" min="0" step="0.01" value="<?= $unit_price ?>">
      <label for="quantity">Quantity:</label>
      <input type="number" id="quantity" name="quantity" min="0" value="<?= $quantity ?>">
      <label for="director-name">Director:</label>
      <select name="director" id="director" >
        <?php
        $sql = "SELECT DirectorID, DirectorName FROM `directors`";
        $stmt = $pdo->query($sql);
        $directors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!$directors) {
          ?>
          <option value="">none</option>
          <?php
        } else {
          foreach($directors as $director) {
            if($director['DirectorName'] === $director_name) {
              ?>
              <option value="<?= $director['DirectorID'] ?>" selected ><?= $director['DirectorName'] ?></option>
              <?php
            } else {
              ?>
              <option value="<?= $director['DirectorID'] ?>"><?= $director['DirectorName'] ?></option>
              <?php
            }
          }
        } 
        ?>
      </select>
      <label for="categories">Categories:</label>
      <select name="categories[]" id="categories" multiple >
        <?php
        $sql = "SELECT * FROM `categories`";
        $stmt = $pdo->query($sql);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!$categories) {
          ?>
          <option value="">none</option>
          <?php
        } else {
          foreach($categories as $category) {
            if(in_array($category['CategoryName'], $movie_categories)) {
              ?>
              <option value="<?= $category['CategoryID'] ?>" selected><?= $category['CategoryName'] ?></option>
              <?php
            } else {
              ?>
              <option value="<?= $category['CategoryID'] ?>" ><?= $category['CategoryName'] ?></option>
              <?php
            }
          }
        } 
        ?>
      </select>
      <label for="actors">Actors:</label>
      <select name="actors[]" id="actors" multiple >
      <?php
      $sql = "SELECT ActorID, ActorName FROM `actors`";
      $stmt = $pdo->query($sql);
      $actors = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if(!$actors) {
        ?>
        <option value="">none</option>
        <?php
      } else {
        foreach($actors as $actor) {
          if(in_array($actor['ActorName'], $movie_actors)) {
            ?>
            <option value="<?= $actor['ActorID'] ?>" selected><?= $actor['ActorName'] ?></option>
            <?php
          } else {
            ?>
            <option value="<?= $actor['ActorID'] ?>"><?= $actor['ActorName'] ?></option>
            <?php
          }
        }
      } 
      ?>
      </select>
      <img src="../<?= $_POST['image'] ?>" alt="default image" style="width:50px; height:60px;">
      <input type="file" name="file">
      <button type="submit">Submit</button>
    </form>
</body>
</html>