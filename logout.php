<?php
session_start();

if($_SESSION['login']) {
  $_SESSION['login'] = false;
  unset($_SESSION['credentials']);
  unset($_SESSION['admin']);
  unset($_SESSION['login']);
  header("location:./index.php");
  exit();
}