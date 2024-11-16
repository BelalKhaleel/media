<?php
session_start();
require_once('./check-admin.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin</title>
</head>
<body style="padding:1em;">
  <h1>Admin Dashboard</h1>
  <a href="clients.php">clients</a>
  <a href="categories.php">categories</a>
  <a href="actors.php">actors</a>
  <a href="directors.php">directors</a>
  <a href="movies.php">movies</a>
</body>
</html>