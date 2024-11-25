<?php
session_start();
require_once('./check-admin.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>movies</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body style="padding:1em;">
  <h1>Movies</h1>
  <form action="movies.php" method="get">
    <input type="text" name="search" placeholder="Search movie">
    <button type="submit">Search</button>
  </form>
  <?php
  require_once('../connection.php');
  $movies_per_page = 5;
  $sql = "SELECT COUNT(movies.MovieID) AS 'total_movies' FROM movies;";
  $stmt = $pdo->query($sql);
  $total_number_of_movies = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_number_of_movies = $total_number_of_movies['total_movies'];
  $number_of_pages = ceil($total_number_of_movies / $movies_per_page);
  if (isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page'])) {
    $limit = ($_GET['page'] - 1) * $movies_per_page;
  } else {
    $limit = 0;
  }
  if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = strtolower($_GET['search']);
    $search_term = "%$search_term%";
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
            GROUP BY movies.MovieID
            HAVING LOWER(movies.Title) LIKE :search_term";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':search_term', $search_term);
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } else {
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
            GROUP BY movies.MovieID
            LIMIT :limit, $movies_per_page;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  if (!$movies) {
    echo "No movies found";
  } else {
  ?>
  <table class="table table-striped">
    <thead>
      <th>Movie ID</th>
      <th>Movie Title</th>
      <th>Production Year</th>
      <th>Price</th>
      <th>Quantity</th>
      <th>Director</th>
      <th>Actors</th>
      <th>Categories</th>
      <th>Image</th>
      <th>Update Movie</th>
    </thead>
    <tbody>
      <?php
      foreach($movies as $movie) {
        ?>
        <tr>
          <td><?= $movie['MovieID'] ?></td>
          <td><?= $movie['Title'] ?></td>
          <td><?= $movie['ProduceYear'] ?></td>
          <td><?= $movie['UnitPrice'] ?></td>
          <td><?= $movie['Quantity'] ?></td>
          <td><?= $movie['DirectorName'] ?></td>
          <td><?= $movie['Actors'] ?></td>
          <td><?= $movie['Categories'] ?></td>
          <td><img src="../<?= $movie['image'] ?>" alt="movie image" style="width:50px; height:60px;"></td>
          <td>
            <form action="./edit-movie.php" method="post">
                <input type="hidden" name="id" value="<?= $movie['MovieID'] ?>">
                <input type="hidden" name="movie-title" value="<?= $movie['Title'] ?>">
                <input type="hidden" name="production-year" value="<?= $movie['ProduceYear'] ?>">
                <input type="hidden" name="unit-price" value="<?= $movie['UnitPrice'] ?>">
                <input type="hidden" name="quantity" value="<?= $movie['Quantity'] ?>">
                <input type="hidden" name="director-name" value="<?= $movie['DirectorName'] ?>">
                <input type="hidden" name="actors" value="<?= $movie['Actors'] ?>">
                <input type="hidden" name="categories" value="<?= $movie['Categories'] ?>">
                <input type="hidden" name="image" value="<?= $movie['image'] ?>">
                <input type="hidden" name="page" value="<?= $_GET['page'] ?? 1 ?>">
                <button type="submit" class="btn btn-primary">EDIT</button>
              </form>
          </td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
  <form action="./Controllers/MovieController/addMovie.php" method="post" id="add-movie" enctype="multipart/form-data" >
    <h2>Add movie</h2>
    <label for="movie-title">Movie Title:</label>
    <input type="text" id="movie-title" name="movie-title" >
    <label for="production-year">Production Year:</label>
    <input type="number" id="production-year" name="production-year" min="1888" max="<?= date("Y") ?>" >
    <label for="unit-price">Price:</label>
    <input type="number" id="unit-price" name="unit-price" min="0" step="0.01" >
    <label for="quantity">Quantity:</label>
    <input type="number" id="quantity" name="quantity" min="0" >
    <label for="director">Director:</label>
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
        ?>
        <option value="" disabled selected >Please choose the director</option>
        <?php
        foreach($directors as $director) {
          ?>
          <option value="<?= $director['DirectorID'] ?>"><?= $director['DirectorName'] ?></option>
          <?php
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
          ?>
          <option value="<?= $category['CategoryID'] ?>"><?= $category['CategoryName'] ?></option>
          <?php
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
        ?>
        <option value="<?= $actor['ActorID'] ?>"><?= $actor['ActorName'] ?></option>
        <?php
      }
    } 
    ?>
    </select>
    <input type="file" name="file" >
    <button type="submit">Submit</button>
  </form>
  <?php
  $webpage = 'movies';
  require_once('../pagination.php');
  }
  ?>
</body>
</html>