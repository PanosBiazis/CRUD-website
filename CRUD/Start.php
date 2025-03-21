<!DOCTYPE html>
<head>
    <title>Bike Race 2024</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Panagiotis Biazis">
    <meta name="date" content="11/4/2024">
    <meta time="17:52:00">
</head>
<body>
    <style>
        body {
            background-image: url("bikerace.jpg");/*Background Image*/
            background-size: cover;
        }
    </style>
    <div id="login"><!--Login Button-->
        <form action="Login.php" method="post">
    <input style="background-color: lightskyblue; position:relative; left: 1600px; top:250px; width:200px; height: 70px;" type="submit" value="Login">
        </form>
    </div>
    <div id="register"><!--Register Button-->
    <form action="Register.php" method="post">
    <input style="background-color: green; position:relative; left: 1600px; top:300px; width:200px; height: 70px;" type="submit" value="Register">
    </form>
    </div>
    <div id="Login without account"><!--Loginwithoutaccount Button -->
    <form action="Loginwithoutaccount.php" method="post">
    <input style="background-color: yellow; position:relative; left: 1600px; top:350px; width:200px; height: 70px;" type="submit" value="Login without account">
    </form>
    </div>
</body>
</html>