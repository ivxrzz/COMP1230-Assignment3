<?php

?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Voting Application - O'Neal Jean & Ivor</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body{
            /* This is for the flex that helps with the index.php*/
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 150vh;
        }
        #source-code-index{
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
<div class="container1">
    <div class ="card1">
        <h1>Welcome to Voting App - O'Neal Jean & Ivor</h1>
        <p>Please <a href="login.php" class="btn">login</a> to continue.</p>
        <p>Don't have an account? Please <a href="register.php" class="btn">Register</a>.</p>
    </div>
</div>
</body>
<!--There have been issues with the source code being formatted incorrectly due to HTML and CSS  formatting issue hence I had to implement this.-->
<div id="source-code-index">
    <?php show_source(__FILE__); ?>
</div>
</html>


