<?php
session_start();
require_once('./check-admin.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body style="padding:1em;">
  <h1>Movies</h1>
  <?php
  if(!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Order details not found");
  }
  $id = $_GET["id"];
  require_once('../connection.php');
  $sql = "SELECT movies.MovieID, movies.Title
          FROM movies
          JOIN saledetail ON saledetail.MovieID = movies.MovieID
          WHERE saledetail.SaleID=:id;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if(!$movies) {
    echo "Movies not found";
  } else {
    ?>
    <table class="table table-striped">
      <thead>
        <th>Movie ID</th>
        <th>Movie Title</th>
      </thead>
      <tbody>
        <?php
        foreach($movies as $movie) {
        ?>
        <tr>
          <td><?= $movie['MovieID'] ?></td>
          <td><?= $movie['Title'] ?></td>
        </tr>
        <?php
        }
        ?>
      </tbody>
    </table>
    <?php
  }
  ?>
</body>
</html>