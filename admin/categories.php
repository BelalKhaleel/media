<?php
session_start();
require_once('./check-admin.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>categories</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body style="padding:1em;">
  <h1>Categories</h1>
  <form action="categories.php" method="get">
    <input type="text" name="search" placeholder="Search category">
    <button type="submit">Search</button>
  </form>
  <?php
  require_once('../connection.php');
  $categories_per_page = 5;
  $sql = "SELECT COUNT(categories.CategoryID) AS 'total_categories' FROM categories;";
  $stmt = $pdo->query($sql);
  $total_number_of_categories = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_number_of_categories = $total_number_of_categories['total_categories'];
  $number_of_pages = ceil($total_number_of_categories / $categories_per_page);
  if (isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page'])) {
    $limit = ($_GET['page'] - 1) * $categories_per_page;
  } else {
    $limit = 0;
  }
  if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = strtolower($_GET['search']);
    $search_term = "%$search_term%";
    $sql = "SELECT categories.CategoryID, categories.CategoryName 
            FROM `categories` 
            WHERE LOWER(categories.CategoryName) LIKE :search_term";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':search_term', $search_term);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } else {
  $sql = "SELECT categories.CategoryID, categories.CategoryName
          FROM `categories`
          LIMIT :limit, $categories_per_page;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
  $stmt->execute();
  $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  if (!$categories) {
    echo "No categories found";
  } else {
  ?>
  <form action="./Controllers/CategoryController/addCategory.php" method="post" >
    <div class="mb-3">
      <label for="add-category" class="form-label">Add Category</label>
      <input type="text" class="form-control" id="add-category" name="category" style="width:20%;">
    </div>
    <button type="submit" class="btn btn-primary">Add</button>
    <?php
    if (isset($_SESSION["add-category"])) {
      if($_SESSION["add-category"]) {
      ?>
      <p style="color:green;">Category added successfully!</p>
      <?php
      } else {
      ?>
      <p style="color:red;">Invalid Input</p>
      <?php
      }
    }
    unset($_SESSION["add-category"]);
    ?>
  </form>
  <table class="table table-striped">
    <thead>
      <th>Category ID</th>
      <th>Category Name</th>
    </thead>
    <tbody>
      <?php
      foreach($categories as $category) {
        ?>
        <tr>
          <td><?= $category['CategoryID'] ?></td>
          <td><?= $category['CategoryName'] ?></td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
  <?php
  $webpage = 'categories';
  require_once('../pagination.php');
  }
  ?>
</body>
</html>