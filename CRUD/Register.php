<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Panagiotis Biazis">
    <meta name="date" content="11/4/2024">
    <meta time="17:52:00">
    <style>
        body {
            background-color: lightgreen;/*Background color*/
        }
        .Register_field {/*Register Field*/
          background-color: white; 
           position: absolute;
          width: 500px;
          left: 750px;
          border: 10px solid darkblue;
        }
        .Register { 
            text-align: center;
        }
        input{/*Size of Buttons*/
            width: 300px;
            height: 25px;
        }
    </style>    
</head>
<body>
<fieldset class="Register_field"><!--Register Field-->
<form action="" method="post">
    <div class="Register"><!--Register-->
    <label style="position:relative; left: 30px;">Username:</label>
    <input style="position:relative; left: 30px;" type="text" name="username" required><br><br>
    <label style="position:relative; left: 40px;">Email:</label>
    <input style="position:relative; left: 40px;" type="email" name="email" required><br><br>
    <label style="position:relative; left: 30px;">Password:</label>
    <input style="position:relative; left: 30px;" type="password" name="password" required><br><br>
    <label>Confirm Password:</label>
    <input type="password" name="confirm_password" required><br><br><br>
    <input type="submit" value="Submit">
</form>
<?php
// Define the database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crud";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form fields are set and not empty
    if(isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"]) &&
       !empty($_POST["username"]) && !empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["confirm_password"])) {
        
        // Get form data
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];

        // Check if passwords match
        if ($password !== $confirm_password) {
            echo "Passwords do not match.";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare and bind the insert statement
            $stmt = $conn->prepare("INSERT INTO accounts (email, password, username) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $hashed_password, $username);

            // Execute the statement
            if ($stmt->execute()) {
                echo "<br>Registration successful!";
                echo "<br>";
                echo "<h1>NOW TRY TO LOGIN</h1>";
                echo "<script>
                setTimeout(function(){
                    window.location.href = 'http://localhost/CRUD/Login.php';
                }, 3000); // Redirect after 3 seconds
                </script>";// Redirect to the desired page after 3 seconds
                exit;
            } else {
                echo "Error: " . $stmt->error;
            }
            // http://localhost:8080/CRUD/Login.php

            // Close statement
            $stmt->close();
        }
    } else {
        echo "<br><br>Please fill out all the fields.";
        echo "<br>";
    }
    // Close connection
    $conn->close();
}
?>
<br><br><a href='http://localhost/CRUD/Login.php'>Login</a><br><br>
<!-- http://localhost:8080/CRUD/Login.php -->
<a href='http://localhost/CRUD/Start.php'>First page</a><br><br>
<!-- http://localhost:8080/CRUD/Start.php -->
<a href="http://localhost/CRUD/CRUD-BR_User.php">login without account</a><br><br>
<!-- http://localhost:8080/CRUD/CRUD-BR_User.php -->
</div><!--Register-->
</fieldset><!--Fieldset-->
</body>
</html>