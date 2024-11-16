<?php
session_start();
require_once('./connection.php');
print_r($_POST);
if(
  isset($_POST['user_id'])
  && !empty(trim($_POST['user_id']))
  && is_numeric($_POST['user_id'])
  && isset($_POST['movie_id'])
  && !empty(trim($_POST['movie_id']))
  && is_numeric($_POST['movie_id'])
  && is_numeric($_POST['user_id'])
  && isset($_POST['quantity'])
  && !empty(trim($_POST['quantity']))
  && is_numeric($_POST['quantity'])
  ) {
    $user_id = filter_var(trim($_POST['user_id']), FILTER_VALIDATE_INT);
    $movie_id = filter_var(trim($_POST['movie_id']), FILTER_VALIDATE_INT);
    $quantity = filter_var(trim($_POST['quantity']), FILTER_VALIDATE_INT);
    $sql = "SELECT * FROM `sales` WHERE ClientID = :clientId AND Opened = 1;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':clientId', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $sale = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($sale) {
      $sale_id = $sale['SaleID'];
      $sql = "SELECT * FROM `saledetail` WHERE saledetail.SaleID = :saleId AND MovieID = :movieId;";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':saleId', $sale_id, PDO::PARAM_INT);
      $stmt->bindParam(':movieId', $movie_id, PDO::PARAM_INT);
      $stmt->execute();
      $sale_detail = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($sale_detail) {
        $sql = "UPDATE saledetail SET Qty = Qty + :quantity WHERE SaleID = :saleId AND MovieID = :movieId;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          ':quantity' => $quantity,
          ':saleId' => $sale_id,
          ':movieId' => $movie_id,
        ]);
      } else {
        $sql = "INSERT INTO `saledetail` (`SaleID`, `MovieID`, `Qty`) 
                VALUES (:saleId, :movieId, :quantity);";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          ':saleId' => $sale_id,
          ':movieId' => $movie_id,
          ':quantity' => $quantity,
        ]);
      }
    } else {
      $sql = "INSERT INTO `sales` (`ClientID`, `saleDate`, `Opened`) 
              VALUES (:clientId, current_timestamp(), 1);";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':clientId', $user_id, PDO::PARAM_INT);
      $stmt->execute();
      $sale_id = $pdo->lastInsertId();
      $sql = "INSERT INTO `saledetail` (`SaleID`, `MovieID`, `Qty`) 
              VALUES (:saleId, :movieId, :quantity);";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ':saleId' => $sale_id,
        ':movieId' => $movie_id,
        ':quantity' => $quantity,
      ]);
    }
    $_SESSION['add-to-cart'] = TRUE;
    header("location:./movie-details.php?movie_id=$movie_id");
    exit;
  } else {
    $_SESSION['add-to-cart'] = FALSE;
    header("location:./movie-details.php?movie_id=$movie_id");
    exit;
}