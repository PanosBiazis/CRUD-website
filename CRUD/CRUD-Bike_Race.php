<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bike Race</title> 
    <meta name="author" content="Panagiotis Biazis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="11/4/2024">
    <meta time="17:52:00">
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
        .username {
            position: relative;
            left: 1450px;
            font-size: 20px;
            bottom: 30px;
            color: red; /* Ensure text color is white */
            width: 300px;
        }
        .logout{
            position: relative;
            font-size: 30px;
            bottom: 60px;
            left: 1750px;
            color: purple; /* Ensure text color is white */
            width: 100px;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
            position: relative;
            bottom: 4px;
        }
        .pagination a {
            color: black;
            padding: 8px 16px;
            text-decoration: none;
        }
        .pagination a.active {
            background-color: red;
            color: white;
        }
        .search-bar{
            position: relative;
            text-align:center;
            bottom: 10px;
        }
        input {
            width: 300px; 
            height: 20px;
        }
        button {
            width: 100px;
            height: 25px;
        }
    </style>
</head>
<body>
    <header>
        <div class="home">
            <!--Navbar-->
            <div class="nav">
                <div style="position:relative; top: 10px; left: 10px; color:orange;">Bike Race 2024</div>
                <div class="search-bar">
                    <form action="search_results.php" method="GET">
                        <input type="text" name="search_query" placeholder="Search...">
                        <button type="submit">Search</button>
                    </form>
                </div>
                <div class="username">
                    <?php
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $database = "crud";
                    $conn = mysqli_connect($servername, $username, $password, $database);
                    if(!$conn){
                        die("Connection Failed: ".mysqli_connect_error());
                    }
                    // Start session to access user data
                    session_start();
                    if(isset($_SESSION['username'])) {
                        echo "Welcome, ".$_SESSION['username']."!";
                    }
                    ?>
                </div>
                <div class="logout"> 
                    <a href="Start.php">Logout</a>
                </div>
            </div>
        </div>
    </header>
    <section>
        <fieldset style="border: 20px solid black; background-color:white; position:relative; left:400px;">
            <h1 style="color:green; position:relative; left:40px;  width:150px; height: 40px;"><u>Bike Race Table</u></h1>
            <div id="add">
                <form action="Add.php" method="post">
                    <input style="background-color: green; position:relative; left: 610px; bottom :60px; width:100px; height: 40px;" type="submit" value="Add">
                </form>
            </div>
            <div id="delete_all">
                <form action="DeleteAll.php" method="post">
                    <input style="background-color: red; position:relative; left: 750px;  bottom :100px;width:100px; height: 40px;" type="submit" value="Delete All">
                </form>
            </div>
            <div id="select_delete">
                <form action="select_delete.php" method="post">
                    <input style="background-color: orange; position:relative; left: 880px;  bottom :140px; width:110px; height: 40px;" type="submit" value="Select to Delete">
                </form>
            </div>
            <div id="edit">
                <form action="edit.php" method="post">
                    <input style="background-color: yellow; position:relative; left: 880px;  bottom :120px;width:110px; height: 40px;" type="submit" value="Edit">
                </form>
            </div>
            <div id="sort_buttons">
                <!-- Button to sort records by ID in ascending order -->
                <form action="" method="post">
                    <input style="background-color: lightgreen; position:relative; left: 610px; bottom :160px; width:100px; height: 40px;" type="submit" name="sort_asc" value="Sort Asc">
                </form>
                <!-- Button to sort records by ID in descending order -->
                <form action="" method="post">
                    <input style="background-color:darkred; position:relative; left: 750px; bottom :200px;width:100px; height: 40px;" type="submit" name="sort_desc" value="Sort Desc">
                </form>
            </div>
            <fieldset style="height:70px; width:auto;  position:relative; bottom:170px; background-color: blue; border: 5px solid blue;"></fieldset>
            <tr>
                <?php 
                // Perform database query
                $query = "SELECT * FROM contestants";
                $result = mysqli_query($conn, $query);

                // Check if sort button is clicked
                if(isset($_POST['sort_asc']) || isset($_POST['sort_desc'])) {
                    // Sort records by ID in ascending order
                    if(isset($_POST['sort_asc'])) {
                        $query .= " ORDER BY id ASC";
                    }
                    // Sort records by ID in descending order
                    elseif(isset($_POST['sort_desc'])) {
                        $query .= " ORDER BY id DESC";
                    }
                }

                // Check if query was successful
                if (!$result) {
                    die("Error executing the query: " . mysqli_error($conn));
                }

                // Check if there are rows returned
                $total_records = mysqli_num_rows($result);
                $records_per_page = 5; // Number of records per page
                $total_pages = ceil($total_records / $records_per_page); // Calculate total pages

                // Calculate current page number
                $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

                // Calculate offset
                $offset = ($current_page - 1) * $records_per_page;

                // Fetch records with LIMIT and OFFSET
                $query .= " LIMIT $records_per_page OFFSET $offset";
                $result = mysqli_query($conn, $query);

                // Display table header
                if ($result->num_rows > 0) {
                    echo "<table style='position:relative; bottom:170px; border: 15px solid red;'><tr>";
                    $columns = $result->fetch_fields();
                    foreach ($columns as $column) {
                        echo "<th>" . $column->name . "</th>";
                    }
                    echo "</tr>";
                    // Display each row
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        foreach ($row as $cell) {
                            echo "<td>" . $cell . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='position:relative; bottom:170px; text-align:center;'><b>No results found.</b></p><br/>";
                }

                // Display pagination links
                echo "<div class='pagination'>";
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<a href='?page=" . $i . "' class='" . ($i == $current_page ? "active" : "") . "'>" . $i . "</a>"; // Display pagination links
                }
                echo "</div>";
                ?>
            </tr>
        </fieldset>
        <table style="background-color: green; border: 5px solid green; height: 170px; position:relative; left: 430px; bottom: 230px; width:990px;">
    </table>
    </section>
</body>
</html>