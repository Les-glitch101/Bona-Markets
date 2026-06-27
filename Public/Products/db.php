<?php
$conn = new mysqli("localhost", "root", "", "bona_markets", 3306);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>

