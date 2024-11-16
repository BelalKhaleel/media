<?php

$server = "localhost";
$db = "media";
$dsn = "mysql:host=$server;dbname=$db";
$db_username = "root";
$db_password = "";

try {
  $pdo = new PDO($dsn, $db_username, $db_password);
} catch (PDOException $exception) {
  echo "Connection to database failed! Error: " . $exception->getMessage();
}