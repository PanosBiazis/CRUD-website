<?php
 // Define the database connection details
 $servername = "localhost";
 $username = "root";
 $password = "";
 $dbname = "crud";
 
 // Create a new MySQLi connection
 $conn = new mysqli($servername, $username, $password, $dbname);
 
 // Check if the connection was successful
 if ($conn->connect_error) {
     // If the connection failed, display an error message
     die("Connection failed: " . $conn->connect_error);
 }
 ?>