<?php
session_start();
require_once('./check-admin.php');

if (
     isset($_POST['actor-name']) 
  && isset($_POST['gender'])
  && isset($_POST['nationality'])
  && !empty(trim($_POST['actor-name']))
  && !empty(trim($_POST['gender']))
  && !empty(trim($_POST['nationality']))
  && !is_numeric($_POST['actor-name'])
  && is_numeric($_POST['gender'])
  && is_numeric($_POST['nationality'])
  && $_POST['gender'] > 0
  && $_POST['nationality'] > 0
  ) {

    $actor = filter_var(trim($_POST['actor-name']), FILTER_SANITIZE_STRING);
    $gender_id = filter_var(trim($_POST['gender']), FILTER_VALIDATE_INT);
    $nationality_id = filter_var(trim($_POST['nationality']), FILTER_VALIDATE_INT);

    require_once("../../../connection.php");

    $sql = "SELECT GenderID FROM `genders` WHERE GenderID = :genderId;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':genderId', $gender_id);
    $stmt->execute();

    if (!$stmt->fetchColumn()) {
      die("Gender ID not found");
    }

    $sql = "SELECT NationalityID FROM `nationalities` WHERE NationalityID = :nationalityId;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nationalityId', $nationality_id);
    $stmt->execute();

    if (!$stmt->fetchColumn()) {
      die("Nationality ID not found");
    }

    $sql = "INSERT INTO `actors` (`ActorID`, `ActorName`, `GenderID`, `NationalityID`) VALUES (NULL, :actor, :gender, :nationality);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':actor'=> $actor, ':gender'=> $gender, ':nationality'=> $nationality]);
    
    $_SESSION['add-actor'] = true;
    header("location:../../actors.php");
    exit;
  } else {
    $_SESSION['add-actor'] = false;
    header("location:../../actors.php");
    exit;
  }