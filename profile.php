<?php
session_start();
date_default_timezone_set('EST'); //represent the current Timezone which is EST
include 'navbar.php';
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

$topicObject = new Topic($conn);
$voteObject = new Vote($conn);


//If the user is not logged in it will redirect it to the Login.php USERS MUST BE LOGGINED MAKE SURE YOUR LOGGED IN
if (!isset($_SESSION['username'])) {
    header('location: login.php');
    exit;
}


?>

    <html>
    <head>
        <title>Profile Page</title>
        <style>
            body {
                font-family: Arial, sans-serif;
            }

        </style>
    </head>

    <body>
    <h1>Your Profile</h1>


    <?php
    function getUserVotingHistory($conn, $user_id)
    {

        $topicObject = new Topic($conn);
        $vote = new Vote($conn);

        $history = [];

        $topics = $topicObject->getTopics();
        foreach ($topics as $topic) {
            if ($vote->hasVoted($topic->id, $user_id)) {
                $history[] = $topic;
            }
        }

        return $history;
    }


    $uname = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT id FROM Users WHERE username = :username");
    $stmt->execute(['username' => $uname]);
    $user_id = $stmt->fetchColumn();
    $history = $voteObject->getUserVoteHistory($uname);
    $topics = $topicObject->getTopics();

    $topicsVotedOn = 0;
    $topicsCreated = 0;
    foreach ($topics as $topic) {
        if ($voteObject->hasVoted($topic->id, $user_id)) {
            $topicsVotedOn++;
        }
        if ($topic->userId == $user_id) {
            $topicsCreated++;
        }
    }
    //The total votes of how many votes they have casted
    $totalVotes = count($history);


    echo "
    <p>Total Topics Created: {$topicsCreated}</p> 
    <p>Total Vote Casted: {$topicsVotedOn}</p> 
";
    ?>

    <ul>
        <?php
        foreach (getUserVotingHistory($conn, $user_id) as $topic) {
            $formatted = TimeFormatter::formatTimestamp($topic->createdAt);
            echo "
                <li>Title: {$topic->title}
                <br>
                    Description: {$topic->description}
                    <br>
                    Topic Created: {$formatted}
                </li>
                <br>
                ";
        }
        ?>
    </ul>

    </body>

    </html>

<?php show_source(__FILE__); ?>