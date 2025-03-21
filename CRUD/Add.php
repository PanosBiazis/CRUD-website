<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Input Form</title>
    <meta name="author" content="Panagiotis Biazis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="11/4/2024">
    <meta time="17:52:00">
</head>
<body style="background-color: lightgreen;">
<fieldset style="width:26%; height:fit-content; position:relative; left: 40%; text-align: center; border: 10px solid black; background-color: white;">
<form action="" method="post">
    <label>ID:</label>
    <input type="number" name="id" required><br>
    <label>Name:</label>
    <input type="text" name="name" required><br>
    <label>Country:</label>
    <input type="text" name="country" required><br>
    <label>Time:</label>
    <input type="" name="time" required><br><br>
    <input type="submit" value="Submit">
</form>
<div class='goback'>
    <form action="http://localhost/CRUD/CRUD-Bike_Race.php">
        <!-- "http://localhost:8080/CRUD/CRUD-Bike_Race.php" -->
    <input style="position:relative; bottom: 120px; right: 200px; " type="submit" value="Go to Main Page">
    </form>
</div>
<div style='position:relative;'>ID cannot be empty or not a character or string of characters.</div><br/>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "crud";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $result=$conn->query("SELECT * FROM contestants");//query anything you want

    if ($result->num_rows > 0) {
        // Display table header
        echo "<table style='position:relative; border: 15px solid red; left: 60px;'><tr>";
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
        echo "<p style='position:relative; top:20px;'><b>No results found.</b></p>";
    }
    $conn->close();
    ?>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crud";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
        echo "";
        echo "";
        exit();
    }

    $id = intval($_POST['id']);
    $time =strval($_POST['time']);
    // The rest of the code remains unchanged
    // ...

    // Prepare the INSERT statement
    $stmt = $conn->prepare("INSERT INTO contestants (id, name, country, time) VALUES (?, ?, ?, ?)");

    // Bind the parameters to the prepared statement
    // ...

    // Set the parameter values and Execute the prepared statement
    // ...

    // Close the prepared statement
    // ...
    $stmt->bind_param("isss", $id, $name, $country, $time);

// Get the current time
//$now = date('H:i:s', time()) . str_pad(microtime(false), 6, '0', STR_PAD_LEFT);

// Set the parameter values
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
// Check if the 'time' input is set and not empty
if (!isset($_POST['time']) || $_POST['time'] === '') {
    echo "Time input is missing or empty.";
    exit();
}

// Check if the 'time' input is in the correct format (HH:MM:SS)
//$time = DateTime::createFromFormat('H:i:s', $_POST['time']);



// Execute the prepared statement
$stmt->execute();

// Close the prepared statement
$stmt->close();

    // Close the database connection
    $conn->close();

}



?>
</fieldset>
<script>
var goback=confirm("Do you want to go to the main page?");
if(goback==true){
    window.location="http://localhost/CRUD/CRUD-Bike_Race.php"; 
    // "http://localhost:8080/CRUD/CRUD-Bike_Race.php"
}else{
    window.location="http://localhost/CRUD/AddAn.php";
    // "http://localhost:8080/CRUD/AddAn.php"
}
</script>
</body>
</html>