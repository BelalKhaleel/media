<?php
session_start();
require_once('./check-admin.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>directors</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body style="padding:1em;">
  <h1>Directors</h1>
  <form action="directors.php" method="get">
    <input type="text" name="search" placeholder="Search Director">
    <button type="submit">Search</button>
  </form>
  <?php
  require_once('../connection.php');
  $directors_per_page = 5;
  $sql = "SELECT COUNT(directors.DirectorID) AS 'total_directors' FROM directors;";
  $stmt = $pdo->query($sql);
  $total_number_of_directors = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_number_of_directors = $total_number_of_directors['total_directors'];
  $number_of_pages = ceil($total_number_of_directors / $directors_per_page);
  if (isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page'])) {
    $limit = ($_GET['page'] - 1) * $directors_per_page;
  } else {
    $limit = 0;
  }
  if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = strtolower($_GET['search']);
    $search_term = "%$search_term%";
    $sql = "SELECT directors.DirectorID, directors.DirectorName 
            FROM `directors` 
            WHERE LOWER(directors.DirectorName) LIKE :search_term";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':search_term', $search_term);
    $stmt->execute();
    $directors = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } else {
  $sql = "SELECT directors.DirectorID, directors.DirectorName
          FROM `directors`
          LIMIT :limit, $directors_per_page;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
  $stmt->execute();
  $directors = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  if (!$directors) {
    echo "No directors found";
  } else {
  ?>
  <table class="table table-striped">
    <thead>
      <th>Director ID</th>
      <th>Director Name</th>
    </thead>
    <tbody>
      <?php
      foreach($directors as $Director) {
        ?>
        <tr>
          <td><?= $Director['DirectorID'] ?></td>
          <td><?= $Director['DirectorName'] ?></td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
  <?php
  $webpage = 'directors';
  require_once('../pagination.php');
  }
  ?>
</body>
</html>