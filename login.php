<?php
session_start();
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

$user = new User($conn);

//assign the form values in the $_SESSION
if(isset($_POST["submit"])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    //Checks if both fields are filled
    if(empty($username) || empty($password)){
        $error = "Please enter username and password.";
    }
    else {
        if ($user->authenticateUser($username, $password)) {
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit;
        }
        else{
            $error= "Invalid username or password";
        }
    }
}
?>

<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body{
            flex-direction: column;
        }
        .error{
            color:red;
            text-align: center;
            margin-bottom: 30px;

        }
        #source-code-login{
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
<div class="container3">
    <div class="card3">
        <h1>Login</h1>
        <!--Error Message -->
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username">
            <br>
            <br>
            <label for="pass">Password:</label>
            <input type="password" name="password" id="password">
            <br>
            <br>
            <input type="submit" name="submit" value="Login">
        </form>
    </div>
</div>
</body>

<!--There have been issues with the source code being formatted incorrectly due to HTML and CSS  formatting issue hence I had to implement this.-->
<div id="source-code-login">
    <?php show_source(__FILE__); ?>
</div>
</html>

