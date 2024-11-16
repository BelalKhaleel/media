<?php
session_start();

if(isset($_SESSION['login']) || isset($_SESSION['registration'])) {
  unset($_SESSION['credentials']);
  unset($_SESSION['admin']);
  unset($_SESSION['login']);
  unset($_SESSION['registration']);
  header("location:./index.php");
  exit();
}