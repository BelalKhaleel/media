<?php

session_start();
require_once("../connection.php");

if(
  $_SERVER['REQUEST_METHOD'] === 'POST'
  && isset($_POST['firstName']) 
  && !empty(trim($_POST['firstName']))
  && isset($_POST['lastName']) 
  && !empty(trim($_POST['lastName']))
  && isset($_POST['gender']) 
  && !empty(trim($_POST['gender']))
  && is_numeric(trim($_POST['gender']))
  && isset($_POST['phone']) 
  && !empty(trim($_POST['phone']))
  && isset($_POST['address']) 
  && !empty(trim($_POST['address']))
  && isset($_POST['country']) 
  && !empty(trim($_POST['country']))
  && is_numeric(trim($_POST['country']))
  && isset($_POST['username']) 
  && !empty(trim($_POST['username']))
  && isset($_POST['password']) 
  && !empty(trim($_POST['password']))
  ) {
  $first_name = trim($_POST['firstName']);
  $last_name = trim($_POST['lastName']);
  $gender = trim($_POST['gender']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);
  $country = trim($_POST['country']);
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $hash_password = password_hash($password, PASSWORD_BCRYPT);

  $sql = "INSERT INTO `clients` (`FName`, `LName`, `GenderID`, `Phone`, `Address`, `CountryID`, `Username`, `Password`) 
          VALUES (:firstName, :lastName, :genderId, :phone, :address, :countryId, :username, :password);";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':firstName' => $first_name, 
    ':lastName' => $last_name, 
    ':genderId' => $gender, 
    ':phone' => $phone, 
    ':address' => $address,
    ':countryId' => $country,
    ':username' => $username,
    ':password' => $hash_password,
  ]);
  $_SESSION['registration'] = true;
  header("location:../index.php");
  exit();
} else {
  $_SESSION['registration'] = false;
  header("location:../registration.php");
  exit();
}