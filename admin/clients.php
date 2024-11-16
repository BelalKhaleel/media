<?php
session_start();
require_once('./check-admin.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clients</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
  <h1>Clients</h1>
  <form action="clients.php" method="get">
    <input type="text" name="search" placeholder="Search client">
    <button type="submit">Search</button>
  </form>
  <?php
  require_once('../connection.php');
  $clients_per_page = 3;
  $sql = "SELECT COUNT(clients.ClientID) AS 'total_clients' FROM clients;";
  $stmt = $pdo->query($sql);
  $total_number_of_clients = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_number_of_clients = $total_number_of_clients['total_clients'];
  $number_of_pages = ceil($total_number_of_clients / $clients_per_page);
  if (isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page'])) {
    $limit = ($_GET['page'] - 1) * $clients_per_page;
  } else {
    $limit = 0;
  }
  if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = strtolower($_GET['search']);
    $search_term = "%$search_term%";
    $sql = "SELECT clients.clientID, clients.FName, clients.LName, clients.Phone 
            FROM `clients` 
            WHERE LOWER(clients.FName) LIKE :search_term 
               OR LOWER(clients.LName) LIKE :search_term";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':search_term', $search_term);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } else {
  $sql = "SELECT clients.clientID, clients.FName, clients.LName, clients.Phone
          FROM `clients`
          LIMIT :limit, $clients_per_page;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
  $stmt->execute();
  $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  if (!$clients) {
    echo "No clients found";
  } else {
  ?>
  <table class="table table-striped">
    <thead>
      <th>Client ID</th>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Phone</th>
    </thead>
    <tbody>
      <?php
      foreach($clients as $client) {
        ?>
        <tr>
          <td><a href="client-info.php?id=<?= $client['clientID']?>"><?= $client['clientID'] ?></a></td>
          <td><?= $client['FName'] ?></td>
          <td><?= $client['LName'] ?></td>
          <td><?= $client['Phone'] ?></td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
  <?php
  $webpage = 'clients';
  require_once('../pagination.php');
  }
  ?>
</body>
</html>