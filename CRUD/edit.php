<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit</title>
    <meta name="author" content="Panagiotis Biazis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="11/4/2024">
    <meta time="17:52:00">
    <script>
window.onload = function() {
    var form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        var id = document.getElementById('id').value;
        var name = document.getElementById('name').value;
        var country = document.getElementById('country').value;
        var time = document.getElementById('time').value;

        // Validate the input fields
        if (!id ||!name ||!country ||!time) {
            alert('Please fill in all the fields');
            return;
        }

        // Send the AJAX request
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Redirect the user to the specified URL after 3 seconds
                setTimeout(function() {
                    window.location.href = 'http://localhost/CRUD/CRUD-Bike_Race.php?id=' + id + '&name=' + encodeURIComponent(name) + '&country=' + encodeURIComponent(country) + '&time=' + encodeURIComponent(time); 
                }, 3000);
                // http://localhost:8080/CRUD/CRUD-Bike_Race.php?id=
            } else {
                alert('Error updating record: ' + xhr.statusText);
            }
        };
        xhr.send(encodeURI('id=' + id + '&name=' + name + '&country=' + country + '&time=' + time));
    });
};
</script>
<style>
body{
    background-color: lightgreen;
    font-family: Arial;
    text-align: center;
    color: black;
}
button{
    background-color: lightgreen;
    height: 30px;
    width: 80px;
}
</style>    
</head>
<body>
    <fieldset style="width:26%; height:fit-content; position:relative; left: 37%; text-align: center; border: 10px solid black; background-color: white;">
    <h1>Update Record:</h1>
    <form method="post">
        <label for="id" style="position:relative; left: 10px;">ID:</label>
        <input type="text" style="position:relative; left: 10px; height: 25px;" name="id" id="id">
        <br><br>
        <label for="name">Name:</label>
        <input style="height: 25px;" type="text" name="name" id="name">
        <br><br>
        <label for="country" style="position:relative; right: 10px;">Country:</label>
        <input style="position:relative; right: 10px; height: 25px;" type="text" name="country" id="country">
        <br><br>
        <label for="time">Time:</label>
        <input style="height: 25px;" type="text" name="time" id="time">
        <br><br>
        <button type="submit" name="update">Update</button><br><br><br>
        </form>
        <div class='goback'>
            <form action="http://localhost/CRUD/CRUD-Bike_Race.php">
                <!-- http://localhost:8080/CRUD/CRUD-Bike_Race.php -->
            <input style="position:relative; " type="submit" value="Go to Main Page"><br><br><br>
            </form>
        </div>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "crud";
    $conn = mysqli_connect($servername, $username, $password, $database);

    if (!$conn) {
        die("Connection Failed: ". mysqli_connect_error());
    }

    if (isset($_POST['update'])) {
        // Get the user's input
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $country = mysqli_real_escape_string($conn, $_POST['country']);
        $time = mysqli_real_escape_string($conn, $_POST['time']);

        // Create the SQL query to select the record
        $querySelect = "SELECT * FROM contestants WHERE id=$id";

        // Execute the query to select the record
        $resultSelect = mysqli_query($conn, $querySelect);

        // Check if the record exists
        if (mysqli_num_rows($resultSelect) > 0) {
            // Create the SQL query to update the record
            $queryUpdate = "UPDATE contestants SET name='$name', country='$country', time='$time' WHERE id=$id";

            // Execute the query to update the record
            $resultUpdate = mysqli_query($conn, $queryUpdate);

            if ($resultUpdate) {
                echo "Record updated successfully";
            } else {
                echo "Error updating record: ". mysqli_error($conn);
            }
        } else {
            echo "Record not found.";
        }
    }

    $result = $conn->query("SELECT * FROM contestants");

    if ($result->num_rows > 0) {
        // Display table header
        echo "<table><tr>";
        $columns = $result->fetch_fields();
        foreach ($columns as $column) {
            echo "<th>". $column->name. "</th>";
        }
        echo "</tr>";

        // Display each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>". $cell. "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No results found.";
    }

    // Close the database connection
    mysqli_close($conn);

   
  ?>
    </fieldset>
</body>
</html>