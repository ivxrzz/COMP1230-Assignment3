<?php
session_start();
include 'classes.php';

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$topicID = $_GET['topic'];
$voteType = $_GET['vote'];

vote($username, $topicID, $voteType);

header('Location: topic_list.php');
exit;

?>

<?php show_source(__FILE__); ?>