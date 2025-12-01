<?php
include 'navbar.php';
session_start();

include 'classes.php';

define("CONFIG", include __DIR__ . '/db.config.php');

$hostwithPort = CONFIG['app']['host'];
$username = CONFIG['app']['username'];
$password = CONFIG['app']['password'];

//Build DSN for the PDO
//I had to use AI to help me with the PDO as the dsn was not working due to my db.config being weirdly configured
$dsn = "mysql:host=$hostwithPort;dbname=s5468069_project";

//Creating a new instance of the PDO Class and assign the variable $conn
$conn = new PDO($dsn, $username, $password, [
    //sets up on how the PHP Handles Errors
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$topic = new Topic($conn);

$rows = $conn->prepare('SELECT id FROM Users where username = :username');
$rows->execute([ ':username' => $_SESSION['username']]);
$user_id = $rows->fetchColumn();

if (isset($_POST["title"]) && isset($_POST["description"])) {
    if ($topic->createTopic($user_id, $_POST["title"], $_POST["description"])) {
        header("Location: topic_list.php");
    }
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile Page</title>
    <link rel="stylesheet" href="style.css">
    <style>

        body {
            font-family: Arial, sans-serif;

            /* This is for the flex that helps with the dashboard*/
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 150vh;
        }

        header {
            text-align: center;
            padding: 20px;
            margin-bottom: 30px;
            width: 100%

        }

        .card4 {
            border-radius: 15px;
            width: 60%;

        }

        nav {
            width: 100%; /* Fixes the nav bar as it shrinks if it does NOT have this.*/
        }

        /*Good afternoon, the source code has an issue since it is a lot of source code it has HTML and CSS block issues hence I have to use css to place it properly.*/
        #source-code {
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
<header>
    <h1>Dashboard</h1>
</header>

<div class="card4">
    <p>Create a Topic:</p>
    <form action="dashboard.php" method="POST">
        <label for="title">Title</label>
        <br>
        <br>
        <input type="text" name="title" id="title">
        <br>
        <br>
        <label for="description">Description</label>
        <br>
        <br>
        <input type="text" name="description" id="description">
        <br>
        <br>
        <input type="submit" name="submit" value="Create Topic" id="CreateTopic">
    </form>
</div>

</body>
<!--There have been issues with the source code being formatted incorrectly due to HTML and CSS  formatting issue hence I had to implement this.-->
<div id="source-code">
    <?php show_source(__FILE__); ?>
</div>
</html>