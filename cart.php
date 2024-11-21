<?php
session_start();
require_once('./connection.php');

$sql = "SELECT saledetail.ID, saledetail.SaleID, movies.MovieID, movies.Quantity, movies.Title, saledetail.Qty 
        FROM saledetail 
        JOIN sales ON sales.SaleID = saledetail.SaleID 
        JOIN clients ON clients.ClientID = sales.ClientID 
        JOIN movies ON saledetail.MovieID = movies.MovieID 
        WHERE clients.ClientID = :clientId AND sales.Opened = 1;";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':clientId', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(!$orders) {
  $sql = "DELETE FROM sales WHERE sales.SaleID = :saleId;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':saleId', $_SESSION['sale-id'], PDO::PARAM_INT);
  $stmt->execute();
  unset($_SESSION['sale-id']);
  ?>
  <p>No orders to show</p>
  <a href="./index.php">Go back to movies</a>
  <?php
  die();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && isset($_POST['method']) 
    && $_POST['method'] === 'patch'
    && isset($_POST['sale-id'])
    && !empty(trim($_POST['sale-id'])) 
    && is_numeric($_POST['sale-id'])
    && $_POST['sale-id'] > 0
    && isset($_POST['quantity']) 
    && !empty(trim($_POST['quantity'])) 
    && is_numeric($_POST['quantity'])
    && $_POST['quantity'] > 0
    ) {
  $sale_id = filter_var(trim($_POST['sale-id']), FILTER_VALIDATE_INT);
  $quantity = filter_var(trim($_POST['quantity']), FILTER_VALIDATE_INT);
  $sql = "SELECT saledetail.ID, movies.Quantity FROM saledetail 
          JOIN sales ON sales.SaleID = saledetail.SaleID 
          JOIN movies ON saledetail.MovieID = movies.MovieID 
          WHERE saledetail.ID = :saleId;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':saleId', $sale_id, PDO::PARAM_INT);
  $stmt->execute();
  $order = $stmt->fetch(PDO::FETCH_ASSOC);
  $available_quantity = $order['Quantity'];
  if ($quantity > $available_quantity) {
    die('Quantity requested is greater than that available');
  }
  $sql = "UPDATE saledetail SET saledetail.Qty = :quantity WHERE saledetail.ID = :saleId;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':saleId', $sale_id, PDO::PARAM_INT);
  $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
  $stmt->execute();
  header("location: " . $_SERVER['PHP_SELF']);
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && isset($_POST['method']) 
    && $_POST['method'] === 'delete'
    && isset($_POST['sale-id']) 
    && !empty(trim($_POST['sale-id'])) 
    && is_numeric($_POST['sale-id'])
    ) {
  $sale_id = filter_var(trim($_POST['sale-id']), FILTER_VALIDATE_INT);
  $_SESSION['sale-id'] = $sale_id;
  $sql = "DELETE FROM saledetail WHERE saledetail.ID = :saleId;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':saleId', $sale_id, PDO::PARAM_INT);
  $stmt->execute();
  header("location: " . $_SERVER['PHP_SELF']);
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && isset($_POST['method']) 
    && $_POST['method'] === 'delete-all'
    && isset($_POST['sale-id']) 
    && !empty(trim($_POST['sale-id'])) 
    && is_numeric($_POST['sale-id'])
    ) {
  $sale_id = filter_var(trim($_POST['sale-id']), FILTER_VALIDATE_INT);
  $sql = "DELETE FROM sales WHERE sales.SaleID = :saleId;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':saleId', $sale_id, PDO::PARAM_INT);
  $stmt->execute();
  header("location: " . $_SERVER['PHP_SELF']);
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <title>Cart</title>
  <style>
    body {
      padding: 2em;
    }
  </style>
</head>
<body>
  <h1>Order:</h1>
  <table class="table table-striped">
    <thead>
      <th>Movie</th>
      <th>Quantity</th>
      <th>Actions</th>
    </thead>
    <tbody>
      <?php
      foreach($orders as $order) {
        ?>
        <tr>
          <td><?= $order['Title'] ?></td>
          <td><?= $order['Qty'] ?></td>
          <td style="display:flex; gap: 10px;">
            <form action="" method="post">
              <input type="hidden" name="method" value="patch">
              <input type="hidden" name="sale-id" value="<?= $order['ID'] ?>">
              <input type="number" name="quantity" min="1" max="<?= $order['Quantity'] ?>" value="<?= $order['Qty'] ?>">
              <button type="submit" class="btn btn-primary">UPDATE</button>
            </form>
            <form action="" method="post">
              <input type="hidden" name="method" value="delete">
              <input type="hidden" name="sale-id" value="<?= $order['ID'] ?>">
              <button type="submit" class="btn btn-danger">DELETE</button>
            </form>
          </td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
  <form action="" method="post">
    <input type="hidden" name="method" value="delete-all">
    <input type="hidden" name="sale-id" value="<?= $order['SaleID'] ?>">
    <button type="submit" class="btn btn-danger">DELETE ALL</button>
  </form>
  <a href="./index.php">Go back to movies</a>
</body>
</html>