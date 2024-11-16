<?php
session_start();
require_once('./check-admin.php');

if(
  isset($_POST['id']) 
  && !empty(trim($_POST['id'])) 
  && is_numeric($_POST['id'])
  && isset($_POST['page']) 
  && !empty(trim($_POST['page'])) 
  && is_numeric($_POST['page'])
  ) {
  $id = trim($_POST['id']);
  require_once('../../../connection.php');
  $sql = "SELECT COUNT(*) FROM `actors` WHERE `ActorID` = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $actor_exists = $stmt->fetchColumn();
  if ($actor_exists) {
    $sql = "DELETE FROM actors WHERE actors.ActorID=:id;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $page = trim($_POST['page']) ?? 1;
    header("location:../../actors.php?page=$page");
    exit();
  } else {
    die("Actor not found");
  }
} else {
  header("location:../../actors.php?page=$page");
  exit();
}