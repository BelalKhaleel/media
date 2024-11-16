<?php
session_start();
require_once("../connection.php");

if (
  isset($_POST['username']) 
  && !empty(trim($_POST['username']))
  && isset($_POST['password']) 
  && !empty(trim($_POST['password']))
) {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $sql = "SELECT ClientID, Username, Password, is_admin FROM `clients` WHERE Username = :username;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':username', $username);
  $stmt->execute();
  $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$user_info) {
    $_SESSION['login'] = FALSE;
    header("location:../login.php");
    exit();
  }
  if(password_verify($password, $user_info['Password'])) {
    $_SESSION['credentials'] = TRUE;
    $_SESSION['login'] = TRUE;
    $_SESSION['user_id'] = $user_info['ClientID'];
    $is_admin = $user_info['is_admin'];
    if ($is_admin) {
      $_SESSION['admin'] = TRUE;
      header("location:../admin/admin.php");
      exit();
    } else {
      $_SESSION['admin'] = FALSE;
      header("location:../index.php");
      exit();
    }
  } else {
    $_SESSION['login'] = FALSE;
    header("location:../login.php");
    exit();
  }
} else {
  $_SESSION['credentials'] = FALSE;
  header("location:../login.php");
  exit();
}