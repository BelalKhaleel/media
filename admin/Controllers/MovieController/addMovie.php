<?php
session_start();
require_once('../../check-admin.php');
require_once("../../../connection.php");
require_once("../../../trim.php");

if (
    isset($_POST['movie-title'])
 && isset($_POST['production-year'])
 && isset($_POST['unit-price'])
 && isset($_POST['quantity'])
 && isset($_POST['director'])
 && isset($_POST['categories'])
 && isset($_POST['actors'])
 && !empty(trim($_POST['movie-title']))
 && !empty(trim($_POST['production-year']))
 && !empty(trim($_POST['director']))
 && !empty($_POST['categories'])
 && !empty($_POST['actors'])
 && !empty($_FILES['file']['name'])
 && is_array($_POST['categories'])
 && is_array($_POST['actors'])
 && is_array($_FILES['file'])
 && is_numeric($_POST['production-year'])
 && is_numeric($_POST['quantity'])
 && is_numeric($_POST['unit-price'])
 && $_POST['production-year'] >= 1888
 && $_POST['production-year'] <= date("Y")
 && $_POST['unit-price'] >= 0
 && $_POST['quantity'] >= 0
 && count($_POST['categories']) > 0
 && count($_POST['actors']) > 0
) {

  trim_array($_POST);

  $movie_title = $_POST['movie-title'];
  $sql = "SELECT movies.Title FROM movies WHERE REPLACE(LOWER(movies.Title), ' ', '') = :movie_title;";
  $stmt = $pdo->prepare($sql);
  $movie_title_to_lower = strtolower($movie_title);
  $movie_title_no_spaces = preg_replace('/\s+/', '', $movie_title_to_lower);
  $stmt->bindParam(':movie_title', $movie_title_no_spaces);
  $stmt->execute();

  if ($stmt->fetchColumn()) {
    die('Movie already exists!');
  }

  $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
  if (
       $ext !== 'jpg'
    && $ext !== 'jpeg'
    && $ext !== 'webp'
    && $ext !== 'svg'
    && $ext !== 'png'
    && !getimagesize($_FILES['file']['tmp_name'])) {
      die('this is not an image');
  }
  $target_file = "uploads/IMG_" . bin2hex(random_bytes(10)) . ".$ext";
  if(file_exists($target_file)) {
    die('file already exists');
  }
  if($_FILES['file']['size'] > 100000) {
    die('file too large!');
  }
  move_uploaded_file($_FILES['file']['tmp_name'], '../../../' . $target_file);

  $production_year = filter_var($_POST['production-year'], FILTER_VALIDATE_INT);
  $unit_price = filter_var($_POST['unit-price'], FILTER_VALIDATE_FLOAT);
  $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
  $director_id = filter_var($_POST['director'], FILTER_VALIDATE_INT);
  $categories = $_POST['categories'];
  foreach($categories as $category_id) {
    filter_var($category_id, FILTER_VALIDATE_INT);
  }
  $actors = $_POST['actors'];
  foreach($actors as $actor_id) {
    filter_var($actor_id, FILTER_VALIDATE_INT);
  }

  $sql = "INSERT INTO `movies` (`Title`, `ProduceYear`, `UnitPrice`, `Quantity`, `DirectorID`, `image`) VALUES (:title, :year, :price, :quantity, :directorId, :image);";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':title' => $movie_title,
    ':year' => $production_year,
    ':price' => $unit_price,
    ':quantity' => $quantity,
    ':directorId' => $director_id,
    ':image' => $target_file,
  ]);

  $movie_id = $pdo->lastInsertId();

  foreach($categories as $category_id) {
    $sql = "INSERT INTO `moviecategories` (`MovieID`, `CategoryID`) VALUES (:movieId, :categoryId);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':movieId' => $movie_id,
      ':categoryId' => $category_id,
    ]);
  }
  
  foreach($actors as $actor_id) {
    $sql = "INSERT INTO `movieactors` (`MovieID`, `ActorID`) VALUES (:movieId, :actorId);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':movieId' => $movie_id,
      ':actorId' => $actor_id,
    ]);
  }

  header("Location:../../movies.php");
  exit;
} else {
  die("Error: movie not added. Please check the input values and try again." . PHP_EOL);
}
