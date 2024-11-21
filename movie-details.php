<?php
session_start();
require_once('./connection.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Details</title>
    <style>
      form {
        width: fit-content;
      }
      .success-message {
        color: green;
      }
      .error-message {
        color: red;
      }
    </style>
  </head>
  <body>
    <?php
    if (!isset($_GET['movie_id']) && empty(trim($_GET['movie_id'])) && !is_numeric($_GET['movie_id'])) {
      die('Invalid parameter');
    }
    $sql = "SELECT movies.MovieID, 
                   movies.Title, 
                   movies.ProduceYear, 
                   movies.UnitPrice, 
                   movies.Quantity,
                   movies.image,
                   directors.DirectorName, 
                   GROUP_CONCAT(DISTINCT actors.ActorName SEPARATOR ', ') AS Actors, 
                   GROUP_CONCAT(DISTINCT categories.CategoryName SEPARATOR ', ') AS Categories 
                   FROM movies 
                   JOIN directors ON movies.DirectorID = directors.DirectorID 
                   JOIN movieactors ON movieactors.MovieID = movies.MovieID 
                   JOIN actors ON movieactors.ActorID = actors.ActorID 
                   JOIN moviecategories ON moviecategories.MovieID = movies.MovieID 
                   JOIN categories ON moviecategories.CategoryID = categories.CategoryID 
                   WHERE movies.MovieID = :movieId
                   GROUP BY movies.MovieID;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':movieId', $_GET['movie_id']);
    $stmt->execute();
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$movie) {
      die('Movie not found');
    }
    ?>
    <div>
      <img src="<?= $movie['image'] ?>" alt="movie image" style="width: 15em; height: 15em;">
      <p>Title: <span><?= $movie['Title'] ?></span></p>
      <p>Produced: <span><?= $movie['ProduceYear'] ?></span></p>
      <p>Price: <span><?= $movie['UnitPrice'] ?></span></p>
      <p>Quantity: <span><?= $movie['Quantity'] ?></span></p>
      <p>Director: <span><?= $movie['DirectorName'] ?></span></p>
      <p>Actors: <span><?= $movie['Actors'] ?></span></p>
      <p>Categories: <span><?= $movie['Categories'] ?></span></p>
    </div>
    <form action="add-to-cart.php" method="post">
      <fieldset>
        <legend>Buy Now!</legend>
        <?php
        if(!isset($_SESSION['user_id'])) {
          die('You need to login first!');
        }
        ?>
        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
        <input type="hidden" name="movie_id" value="<?= $movie['MovieID'] ?>">
        <?php
        $sql = "SELECT movies.Quantity FROM `movies` WHERE movies.MovieID = :movieId;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':movieId', $movie['MovieID'], PDO::PARAM_INT);
        $stmt->execute();
        $max_quantity = $stmt->fetch(PDO::FETCH_ASSOC);
        $max_quantity = $max_quantity['Quantity'];
        ?>
        <label for="quantity">Please enter the number of items</label>
        <input type="number" id="quantity" name="quantity" min="1" max="<?= $max_quantity ?>" value="1">
        <button type="submit">Add to cart</button>
      </fieldset>
    </form>
    <?php
    if(isset($_SESSION['add-to-cart'])) {
      if($_SESSION['add-to-cart']) {
        ?>
        <p class="success-message">Added successfully!</p>
        <?php
      } else {
        ?>
        <p class="error-message">Movie not added</p>
        <?php
      }
    }
    unset($_SESSION['add-to-cart']);
  ?>
  <a href="./cart.php">View cart</a>
  <a href="./index.php">Go back to movies</a>
</body>
</html>