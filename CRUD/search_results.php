<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Search Results</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Panagiotis Biazis">
    <meta name="date" content="11/4/2024">
    <meta time="17:52:00">
    <style>
        body {
            background-color: lightskyblue;/*Background color*/
            color:black; /*Text color*/
            font-family: Arial, sans-serif; /*Font family*/
            text-align: center;
        }
        table{
            position: relative;
            left: 860px;
            top: 50px; 
            background-color: white;
            border: 5px solid black;
        }
    </style>
  </head>
  <body>
  <div class='goback'>
    <form action="http://localhost/CRUD/CRUD-Bike_Race.php">
        <!-- http://localhost:8080/CRUD/CRUD-Bike_Race.php -->
    <input style="position:relative; left: 850px; height: 70px; width:140px; background-color: lightgreen;" type="submit" value="Go to Main Page">
    </form>
</div>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "crud";
$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Retrieve search query from URL parameters
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

// Prepare the SQL query with four placeholders for the search query
$query = "SELECT * FROM contestants WHERE Id = ? OR Name = ? OR Country = ? OR Time = ?";

// Initialize a prepared statement
$stmt = mysqli_stmt_init($conn);

// Check if the prepared statement can be initialized
if (mysqli_stmt_prepare($stmt, $query)) {
    // Bind the search query parameters to the prepared statement
    mysqli_stmt_bind_param($stmt, "ssss", $search_query, $search_query, $search_query, $search_query);

    // Execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Get the result set from the prepared statement
        $result = mysqli_stmt_get_result($stmt);

        // Display search result
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                echo "<h2>Search Result</h2>";
                echo "<table>";
                echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>Name</th>";
                echo "<th>Country</th>";
                echo "<th>Time</th>";
                echo "</tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['time']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No results found.";
            }
        } else {
            echo "Error retrieving results.";
        }
    } else {
        echo "Error executing the prepared statement.";
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing the statement.";
}

// Close the database connection
mysqli_close($conn);
?>
</body>
</html>