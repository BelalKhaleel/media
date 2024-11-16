<?php

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
  header("location:../../../index.php");
  exit;
}