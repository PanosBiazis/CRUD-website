<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bike Race</title> 
    <meta name="author" content="Panagiotis Biazis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="11/4/2024">
    <meta time="17:52:00">
</head>
<body>
    <style>
        body {
            background-color: lightseagreen;
            background-size: cover;
        }
        fieldset{
            margin: 5px;
            padding: 5px;
            border: 5px solid black;
            border-radius: 10px;
            width: 1000px;
            max-height: fit-content;
        }

        table{
            margin: 5px;
            padding: 5px;
            border: 5px solid black;
            border-radius: 10px;
            width: 1000px;
            max-height: fit-content;
            position: relative;
            bottom:100px;
        }

        th{
            margin: 5px;
            padding: 5px;
            width: 500px;
            height: 30px;
        }

        td{
            margin: 5px;
            padding: 5px;
            border: 5px solid black;
            border-radius: 10px;
            width: 500px;
            height: 50px;
        }
        .home{
            position: relative;
            bottom: 5px;
        }
        .nav{
            height:50px;
            background: black;
            color:white;
            padding: 10px;
            font-size: 20px;
        }
        .home .nav .buttons{
            position: relative;
            text-align: right;
        }
        .home .nav .Login{
            position: relative;
            bottom: 30px;
            right: 50px;
        }
        .home .nav .Register{
            position: relative;
            bottom: 30px;
            right: 20px;
        }
        .nav ul{
            position:absolute;
            right: 5px;
        }
        .nav ul li{
            display: inline-block;
        }
        .nav ul li a{
            color: white;
            padding: 5px;
        }
        .home .nav .First_page{
            position: relative;
            bottom: 30px;
        }
    </style>
    <header>
    <div class="home">
            <!--Navbar-->
        <div class="nav">
            <div style="position:relative; top: 10px; left: 10px; color:orange;">Bike Race 2024</div>
            <div class="buttons">
                <ul>
                    <li>
            <div class="Login">
                <a href="Login.php"><input style="background-color: blue;  width:70px; height: 20px;" type="submit" value="Login"></a>
            </div>
                    </li>
            <li>
            <div class="Register">
              <a href="Register.php"><input style="background-color: green;  width:70px; height: 20px;" type="submit" value="Register"></a>
            </div>
                    </li>
                    <li>
            <div class="First_page">
                <a href="Start.php"><input style="background-color: red;  width:80px; height: 20px;" type="submit" value="First page"></a>
            </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    </header>
    <section>
        <fieldset style="border: 20px solid black; background-color:white; position:relative; left:400px; top:50px;">
            <h1 style="color:green; position:relative; left:40px; bottom:10px;"><u>Bike Race</u></h1>
            <fieldset style="height:40px; width:auto;  position:relative; bottom:30px;  background-color: blue; border: 5px solid blue;"></fieldset>
                <tr>
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
    echo "<table style='position:relative; bottom:20px;  border: 15px solid red;'><tr>";
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
    echo "<p style='position:relative; bottom:20px; text-align:center;'><b>No results found.</b></p>";
}

?>
            </tr>
            <table style="background-color: green; border: 5px solid green; height: 170px; position:relative; bottom:1px; "></table>
        </fieldset>
    </section>
</body>
</html>