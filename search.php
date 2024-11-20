<?php

header("Content-Type: application/json");
require_once('./connection.php');

if(isset($_POST['search']) && !empty(trim($_POST['search']))) {
  $search_term = "%" . trim($_POST['search']) . "%";
  $query = "SELECT movies.MovieID, movies.Title
            FROM movies 
            WHERE LOWER(movies.Title) LIKE LOWER(:searchTerm)";
  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':searchTerm', $search_term);
  $stmt->execute();
  $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if (!$movies) {
    echo json_encode(['error' => 'No movies containing search terms']);
  } else {
    echo json_encode(['success' => TRUE, 'movies' => $movies]);
  }
} else {
  echo json_encode(['error' => 'error']);
}