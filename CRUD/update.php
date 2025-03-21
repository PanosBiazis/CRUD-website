<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update</title>
    <meta name="author" content="Panagiotis Biazis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="11/4/2024">
    <meta time="17:52:00">
    <style>
        body {
            text-align: center;/*Center text*/
            background-color: lightgreen;/*Background color*/
            font-family: Arial, sans-serif;/*Font family*/
            color:black;/*text color */
        } 
    </style>
</head>
<body>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "crud";
$tablename = "contestants";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: ". mysqli_connect_error());
}

// Get data from the form
$id = $_POST['id'];
$name = $_POST['name'];
$country = $_POST['country'];
$time = $_POST['time'];

// Create SQL query
$sql = "UPDATE $tablename SET name='$name', country='$country', time='$time' WHERE id=$id";

// Execute SQL query
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    echo "<h1><b>Record updated successfully</b></h1>";
} else {
    echo "<h1><b>Error updating record: ". mysqli_error($conn)."</b></h1>";
}

// Close connection
mysqli_close($conn);
?>

</body>
</html>