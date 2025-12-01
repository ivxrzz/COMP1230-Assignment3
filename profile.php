<?php

session_start();
include 'classes.php';
include 'navbar.php';

//If the user is not logged in it will redirect it to the Login.php USERS MUST BE LOGGINED MAKE SURE YOUR LOGGED IN
if(!isset($_SESSION['username'])){
    header('location: login.php');
    exit;
}

$theme = getTheme();

?>


    <html>
    <head>
        <title>Profile Page</title>
        <style>
            body {
                background-color: <?= $theme === 'dark' ? '#222' : '#fff' ?>;
                color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
                font-family: Arial, sans-serif;
            }
            a.theme-link {
                padding: 6px 10px;
                background: <?= $theme === 'dark' ? '#444' : '#ddd' ?>;
                color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
                text-decoration: none;
                border-radius: 4px;
            }

        </style>
    </head>

    <body>
    <h1>Your Profile</h1>

    <a class ="theme-link" href="theme.php?theme=light&redirect=profile.php">Light Theme</a>
    <a class ="theme-link" href="theme.php?theme=dark&redirect=profile.php">Dark Theme</a>

    <?php
    function getUserVotingHistory($username){

        $history = [];

        $topics = getTopics();
        foreach($topics as $topic){
            if(hasVoted($username, $topic["topicID"])){
                $history[] = $topic;
            }
        }

        return $history;
    }

    $username = $_SESSION['username'];
    $history = getUserVotingHistory($username);
    $topics = getTopics(); //This is from the functions.php

    $votesCreated = 0;
    $topicsCreated = 0;
    foreach($topics as $topic){
        if(hasVoted($username, $topic['topicID'])){
            $votesCreated++;

        }
        if($topic['creator'] == $username){
            $topicsCreated++;
        }
    }
    //The total votes of how many votes they have casted
    $totalVotes = count($history);


    echo "
    <p>Total Topics Created: {$topicsCreated}</p> 
    <p>Total Vote Cast: {$votesCreated}</p> 
";

    ?>
    <ul>
        <?php
        foreach(getUserVotingHistory($username) as $topic){
            echo"
                <li>Title: {$topic['title']}
                <br>
                    Description: {$topic['description']}
                </li>
                <br>
                ";
        }
        ?>
    </ul>

    </body>

    </html>

<?php show_source(__FILE__); ?>