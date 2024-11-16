<?php
session_start();
require_once('./check-admin.php');

if(isset($_POST['category']) && !empty(trim($_POST['category']))) {
  $category = trim($_POST['category']);
  $sql = "INSERT INTO categories (CategoryName) VALUES (:category)";
  require_once("../../connection.php");
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':category', $category);
  $stmt->execute();
  $_SESSION["add-category"] = true;
  header("location:../categories.php");
} else {
  $_SESSION["add-category"] = false;
  header("location:../categories.php");
}