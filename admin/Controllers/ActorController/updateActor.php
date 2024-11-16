<?php
session_start();
require_once('./check-admin.php');

if (
   isset($_POST['id']) 
&& !empty(trim($_POST['id'])) 
&& is_numeric($_POST['id'])
&& isset($_POST['page']) 
&& !empty(trim($_POST['page'])) 
&& is_numeric($_POST['page'])
&& isset($_POST['actor-name'])
&& !empty(trim($_POST['actor-name']))
&& isset($_POST['gender'])
&& !empty(trim($_POST['gender']))
&& isset($_POST['nationality'])
&& !empty(trim($_POST['nationality']))
) {
  $id = trim($_POST['id']);
  $page = trim($_POST['page']) ?? 1;
  $name = trim($_POST['actor-name']);
  $genderId = trim($_POST['gender']);
  $nationalityId = trim($_POST['nationality']);
  $sql = "UPDATE `actors` SET `ActorName` = :actorName, `GenderID` = :gender, `NationalityID` = :nationality WHERE `actors`.`ActorID` = :id;";
  require_once("../../../connection.php");
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':actorName' => $name, ':gender' => $genderId, ':nationality' => $nationalityId, ':id' => $id]);
  header("location:../../actors.php?page=$page");
  exit();
}