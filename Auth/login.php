<?php
session_start();
require_once("../connection.php");

if (
  isset($_POST['username']) 
  && !empty(trim($_POST['username']))
  && isset($_POST['password']) 
  && !empty(trim($_POST['password']))
) {
  $username1 = trim($_POST['username']);
  $password1 = trim($_POST['password']);
  $sql = "SELECT ClientID, Username, Password, is_admin FROM `clients` WHERE Username = :username AND Password = :password;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':username', $username1);
  $stmt->bindParam(':password', $password1);
  $stmt->execute();
  $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$user_info) {
    $_SESSION['login'] = false;
    header("location:../login.php");
    exit();
  } else {
    $_SESSION['login'] = true;
    $_SESSION['user_id'] = $user_info['ClientID'];
    $is_admin = $user_info['is_admin'];
    if ($is_admin) {
      $_SESSION['admin'] = true;
      header("location:../admin/admin.php");
      exit();
    } else {
      $_SESSION['admin'] = false;
      header("location:../index.php");
      exit();
    }
  }
} else {
  $_SESSION['credentials'] = false;
  header("location:../login.php");
  exit();
}