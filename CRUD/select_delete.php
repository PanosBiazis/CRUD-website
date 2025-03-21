<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Select to delete</title>
    <meta name="author" content="Panagiotis Biazis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="11/4/2024">
    <meta time="17:52:00">
  </head>
  <body style="text-align: center; background-color:dimgray;">
<fieldset style="border: 20px solid black; background-color:white; width:20%; position:relative; left:40%;">
    <h1 style="color:red; text-align: center;"><u>Delete Record</u></h1>
    <form action="delete.php" method="post">
        <label for="id">Enter ID:</label><br><br>
        <input type="text" id="id" name="id" ><br><br>
        <label for="name">Enter Name:</label><br><br>
        <input type="text" id="name" name="name" ><br><br>
        <input type="submit" value="Submit"><br><br>
    </form>
    <div class='goback'>
    <form action="http://localhost/CRUD/CRUD-Bike_Race.php">
        <!-- http://localhost:8080/CRUD/CRUD-Bike_Race.php -->
    <input style="position:relative; height: 30px; width:140px; background-color: lightgreen;" type="submit" value="Go to Main Page"><br><br>
    </form>
</div>
<?php 
$servername = "localhost";
$username = "root";
$password = "";
$database = "crud";
$conn = mysqli_connect($servername, $username, $password, $database);
if(!$conn){
    die("Connection Failed: ".mysqli_connect_error());
}
$result=$conn->query("SELECT * FROM contestants");//query anything you want

if ($result->num_rows > 0) {
    // Display table header
    echo "<table style='position:relative; border: 5px solid black; left: 60px;'><tr>";
    $columns = $result->fetch_fields();
    foreach ($columns as $column) {
        echo "<th>" . $column->name . "</th>";
    }
    echo "</tr>";

    // Display each row
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . $cell . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No results found.<br><br>";
}
?>
</fieldset>
</body>
</html>