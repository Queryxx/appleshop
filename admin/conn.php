<?php 
$servername = "sql201.infinityfree.com";
$username = "if0_38337050";
$password = "BZMOpf1BBgXuR";
$db = "if0_38337050_jmy";

$conn = new mysqli($servername, $username, $password, $db);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
?>