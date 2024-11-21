<?php
session_start();
require_once('./connection.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
    .container {
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
    main {
      padding: 1em;
    }
    section {
      padding: 1em;
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
    if(
      (isset($_SESSION['login']) && $_SESSION['login'])
      || (isset($_SESSION['registration']) && $_SESSION['registration'])
      || isset($_COOKIE['user'])
      ) {
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
    <a href="./registration.php">Sign up</a>
    <p>Already a user?</p>
    <a href="./login.php">Login</a>
    <?php
    }
    ?>
  </nav>
  <div class="container">
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
    <main>
      <section class="search-section">
        <input type="text" id="search-input" placeholder="Enter movie name">
        <button type="button" id="search-bar">Search Movie</button>
        <div id="searched-movies"></div>
      </section>
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
  </div>
  <script>
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-bar');
    function getMovieData(result) {
      let searchedMovies = document.getElementById('searched-movies');
      searchedMovies.innerHTML = "";
      if(result.success) {
        const movieInfo = result.movies;
        movieInfo.forEach(movie => {
          const a = document.createElement('a');
          a.href = `./movie-details.php?movie_id=${movie.MovieID}`;
          a.textContent = movie.Title;
          a.style.display = 'block';
          searchedMovies.appendChild(a);
        });
        
      } else {
        console.log('No movie with such title');
      }
    }
    searchInput.addEventListener('keyup', () => {
      let search = searchInput.value;

      const urlencoded = new URLSearchParams();
      urlencoded.append("search", search);

      const requestOptions = {
        method: "POST", 
        body: urlencoded,
      }

      fetch('http://localhost/media/search.php', requestOptions)
        .then(response => response.json())
        .then(result => getMovieData(result))
        .catch(error => console.error(error));
    });
  </script>
</body>
</html>