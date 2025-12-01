<?php
session_start();
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

$topic = new Topic($conn);

//Implementation of the voting, Upvote and Downvote.


?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Topic List</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                font-family: Arial, sans-serif;
            }

            table {
                width: 100%;
                text-align: center;
                margin: 0 auto;
            }

            .container5 {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            thead {
                background-color: #545b62;
            }

            th, td {
                text-align: left;
                padding: 12px 15px;
                border: 1px solid black;
            }

            a {
                text-decoration: none;
            }

            a.theme-link {
                padding: 6px 10px;
                text-decoration: none;
                border-radius: 4px;
            }

        </style>
    </head>
    <body>
    <h1>Topic List</h1>

    <div class="container5">
        <table>
            <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Vote</th>
            </tr>
            </thead>
            <tbody>
            <?php

            foreach ($topic->getTopics() as $t) {
                $votes = getVoteResults($t->id);
                echo "
                            <tr>
                                <th>{$t->title}</th>
                                <th>{$t->description}</th>
                                <th> 
                                <a href='vote.php?vote=up&topic={$topic->id}'>{$votes['up']} Upvotes</a>
                                <a href='vote.php?vote=down&topic={$topic->id}'>{$votes['down']} Downvotes</a>
                                </th>
                              </tr>
                            ";
            }
            ?>
            </tbody>
        </table>
    </div>
    </body>
    </html>

<?php show_source(__FILE__); ?>