<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete all records table</title>
    <meta name="author" content="Panagiotis Biazis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="11/4/2024">
    <meta time="17:52:00">
</head>
<body style="background-color: lightgreen; font-family: Arial; text-align: center; color: black;">

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crud";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$table_name = "contestants";

$result = $conn->query("TRUNCATE TABLE " . $table_name);

if ($result) {
    echo "<b>All records deleted successfully from the " . $table_name . " table.</b>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>

<script>
    setTimeout(function(){
        window.location.href = "http://localhost/CRUD/CRUD-Bike_Race.php";
    }, 3000); // Redirect after 3 seconds , http://localhost:8080/CRUD/CRUD-Bike_Race.php
</script>


</body>
</html>