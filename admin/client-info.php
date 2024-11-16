<?php
session_start();
require_once('./check-admin.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <title>Client info</title>
</head>
<body style="padding:1em;">
  <h1>Client Info</h1>
  <?php
  if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error no such client");
  }
  $id = $_GET['id'];
  require_once('../connection.php');
  $sql = "SELECT clients.ClientID, clients.FName, clients.LName, clients.Phone, clients.Address, clients.Username, genders.GenderName, countries.CountryName
          FROM clients
          JOIN genders ON genders.GenderID = clients.GenderID
          JOIN countries ON countries.CountryID = clients.CountryID
          WHERE clients.ClientID = :id;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $client_info = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$client_info) {
    die("Error");
  }
  ?>
  <table class="table table-striped">
    <thead>
      <th>Client ID</th>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Phone</th>
      <th>Address</th>
      <th>Username</th>
      <th>Gender</th>
      <th>Country</th>
    </thead>
    <tbody>
      <tr>
        <td><?= $client_info['ClientID'] ?></td>
        <td><?= $client_info['FName'] ?></td>
        <td><?= $client_info['LName'] ?></td>
        <td><?= $client_info['Phone'] ?></td>
        <td><?= $client_info['Address'] ?></td>
        <td><?= $client_info['Username'] ?></td>
        <td><?= $client_info['GenderName'] ?></td>
        <td><?= $client_info['CountryName'] ?></td>
      </tr>
    </tbody>
  </table>
  <h2>Orders</h2>
  <?php
  $sql = "SELECT sales.SaleID, sales.saleDate, sales.ClientID, SUM(movies.UnitPrice*saledetail.Qty) AS 'total'
          FROM sales
          JOIN saledetail ON saledetail.SaleID=sales.SaleID
          JOIN movies ON movies.MovieID=saledetail.MovieID
          WHERE sales.ClientID=:id
          GROUP BY 1, 2;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if (!$orders) {
    echo "No orders yet";
  } else {
    ?>
    <table class="table table-striped">
      <thead>
        <th>Sale ID</th>
        <th>Sale Date</th>
        <th>Total</th>
      </thead>
      <tbody>
        <?php
        foreach($orders as $order) {
        ?>
        <tr>
          <td><a href="order-details.php?id=<?= $order['SaleID'] ?>"><?= $order['SaleID'] ?></a></td>
          <td><?= $order['saleDate'] ?></td>
          <td><?= $order['total'] ?></td>
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