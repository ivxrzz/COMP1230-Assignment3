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

$vote = new Vote($conn);



if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

//Grabbing the session of the loggin username
$username = $_SESSION['username'];
$topicID = $_GET['topic'];
$voteType = $_GET['vote'];
$stmt = $conn->prepare('select id from Users where username = :username');
$stmt->execute(['username' => $username]);
$user_id = $stmt->fetchColumn();

$vote->vote($user_id, $topicID, $voteType);

header('Location: topic_list.php');
exit;

?>

<?php show_source(__FILE__); ?>
