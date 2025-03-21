<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete</title>
    <meta name="author" content="Panagiotis Biazis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="11/4/2024">
    <meta time="17:52:00">
</head>
<body style="background-color:darkcyan; text-align:center;">
<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "crud";
    $conn = mysqli_connect($servername, $username, $password, $database);

    if(!$conn) {
        die("Connection Failed: ".mysqli_connect_error());
    }

    $id = $_POST['id'];
    $name = $_POST['name'];

    $sql = "DELETE FROM contestants WHERE id = '$id' AND name = '$name'";
    if (mysqli_query($conn, $sql)) {
        echo "<h1><b>Record deleted successfully</b></h1>";
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }

    mysqli_close($conn);
    // Redirect the user to the main page after a delay
header('Refresh: 3; URL=http://localhost/CRUD/CRUD-Bike_Race.php');
// http://localhost:8080/CRUD/CRUD-Bike_Race.php
?>
</body>
</html>