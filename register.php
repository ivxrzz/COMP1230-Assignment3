<?php

include 'classes.php';

define("CONFIG", include __DIR__ . '/db.config.php');

$hostwithPort = CONFIG['app']['host'];
$username = CONFIG['app']['username'];
$password = CONFIG['app']['password'];

//Build DSN for the PDO
//I had to use AI to help me with the PDO as the dsn was not working due to my db.config being weirdly configured
$dsn = "mysql:host=$hostwithPort;dbname=php_realassignment";

//Creating a new instance of the PDO Class and assign the variable $conn
$conn = new PDO($dsn, $username, $password, [
    //sets up on how the PHP Handles Errors
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);


$user = new User($conn); // Creating thE USER Object

//We created the usr object which is then we use the form _POST request to get the username, email and password.
if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["pass"])) {
    if ($user->registerUser($_POST["username"], $_POST["email"], $_POST["pass"])) {
        header("Location: login.php");
        exit;
    }
}
?>

<!--
oneal
caiocotts@gmail.com
yomamapass
-->

<html>
<head>
    <title>Registration Page</title>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            /* This is for the flex that helps with the register.php*/
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 150vh;
        }

        #source-code-register {
            max-height: 400px;
            overflow-y: scroll;
            background: #f5f5f5;
            color: black;
            padding: 10px;
            margin-top: 20px;
            width: 80%;
            word-wrap: break-word;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
<div class="container2">
    <div class="card2">
        <h1>Registration</h1>
        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username">
            <br>
            <br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email">
            <br>
            <br>
            <label for="pass">Password:</label>
            <input type="password" name="pass" id="pass">
            <br>
            <br>
            <input type="submit">
        </form>
    </div>
</div>
</body>
<!--There have been issues with the source code being formatted incorrectly due to HTML and CSS  formatting issue hence I had to implement this.-->
<div id="source-code-register">
    <?php show_source(__FILE__); ?>
</div>
</html>


