<?php
?>

<html>
<head>
    <style>
        #NavBar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            background-color: #333333;
            display: flex;
            justify-content: center;


        }


        #NavBar ul li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;

        }

        #NavBar ul li a:hover {
            background-color: #111111;
        }
    </style
</head>
<body>
<nav id="navBar">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="topic_list.php">Topic</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
</body>
</html>