<?php
session_start();
require_once('./connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <style>
    *, 
    *::before, 
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    nav, aside {
      padding: 1em;
    }
    nav {
      background-color: gold;
      display: flex;
    }
    nav a.logout {
      margin: 0 auto;
    }
    main {
      display: flex;
    }
    aside {
      display: flex;
      flex-direction: column;
      gap: 4px;
      width: fit-content;
    }
    aside a {
      text-decoration: none;
      color: black;
    }
    aside a:hover {
      color: gold;
    }
    section {
      padding-left: 3em;
    }
    section img {
      width: 13em;
      height: 13em;
    }
  </style>
</head>
<body>
  <nav>
    <?php
    if(isset($_SESSION['login'])) {
      ?>
      <a href="./logout.php" class="logout">Logout</a>
      <?php
      $sql = "SELECT sales.SaleID, sales.ClientID, sales.Opened FROM sales WHERE ClientID = :clientId AND Opened = 1;";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':clientId', $_SESSION['user_id'], PDO::PARAM_INT);
      $stmt->execute();
      $sale = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($sale) {
        ?>
        <a href="./cart.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
          </svg>
        </a>
        <?php
      }
    } else {
    ?>
    <a href="./registeration.php">Sign up</a>
    <p>Already a user?</p>
    <a href="./login.php">Login</a>
    <?php
    }
    ?>
  </nav>
  <main>
    <aside>
      <?php
      $sql = "SELECT * FROM `categories`";
      $stmt = $pdo->query($sql);
      $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if (!$categories) {
        echo "Not available";
      } else {
        foreach($categories as $category) {
          ?>
          <a href="index.php?category_id=<?= $category['CategoryID'] ?>"><?= $category['CategoryName'] ?></a>
          <?php
        }
      }
      ?>
    </aside>
    <section>
      <?php
      if (isset($_GET['category_id']) && !empty(trim($_GET['category_id'])) && is_numeric($_GET['category_id'])) {
        $sql = "SELECT movies.MovieID, movies.Title, movies.image 
                FROM `movies` 
                JOIN moviecategories 
                ON movies.MovieID = moviecategories.MovieID 
                WHERE moviecategories.CategoryID = :categoryId;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':categoryId', $_GET['category_id']);
        $stmt->execute();
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($movies as $movie){
          ?>
          <a href="movie-details.php?movie_id=<?= $movie['MovieID'] ?>">
            <img src="<?= $movie['image'] ?>" alt="<?= $movie['Title'] ?>">
          </a>
          <?php
        } 
      } else {
        $sql = "SELECT MovieID, Title, image FROM `movies`";
        $stmt = $pdo->query($sql);
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($movies as $movie){
          ?>
          <a href="movie-details.php?movie_id=<?= $movie['MovieID'] ?>">
            <img src="<?= $movie['image'] ?>" alt="<?= $movie['Title'] ?>">
          </a>
          <?php
        }
      }
      ?>
    </section>
  </main>
</body>
</html>