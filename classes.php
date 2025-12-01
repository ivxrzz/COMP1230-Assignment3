<?php

class User
{
    private $pdo;

    public $id;
    public $username;
    public $email;
    public $passwordHash;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function registerUser($username, $email, $password): bool
    {
        //If the username is empty
        if (trim($username) === '') {
            return false;
        }
        //Is the password is less than 9
        if (strlen($password) < 9) {
            return false;
        }

        // Email must be valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }


        // Check duplicate username
        $sql = "SELECT id FROM Users WHERE username = :username LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }

        // Check duplicate email
        $sql = "SELECT id FROM Users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }

        // Hash password and insert
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $passwordHash
        ]);

        return $ok;

    }

    public function authenticateUser($username, $password): bool
    {

        $sql = "SELECT id, username, email, password FROM Users WHERE username = :username LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Store some info in the object (optional)
        $this->id = $user['id'];
        $this->username = $user['username'];
        $this->email = $user['email'];
        $this->passwordHash = $user['password'];

        return true;

    }
}

class Topic
{
    private $pdo;
    public $id;
    public $userId;
    public $title;
    public $description;
    public $createdAt;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // createTopic($userId, $title, $description): bool
    public function createTopic($userId, $title, $description)
    {

        $sql = "INSERT INTO Topics (user_id, title, description, created_at) VALUES (:user_id, :title, :description, NOW())";
        $stmt = $this->pdo->prepare($sql);

        $ok = $stmt->execute([
            ':user_id' => $userId,
            ':title' => $title,
            ':description' => $description
        ]);

        return $ok;
    }

    public function getTopics()
    {
        $topics = [];

        $sql = "SELECT id, user_id, title, description, created_at FROM Topics ORDER BY created_at ASC, id ASC";
        $stmt = $this->pdo->query($sql);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $topic = new Topic($this->pdo);
            $topic->id = $row['id'];
            $topic->userId = $row['user_id'];
            $topic->title = $row['title'];
            $topic->description = $row['description'];
            $topic->createdAt = $row['created_at'];
            $topics[] = $topic;
        }
        return $topics;
    }

    public function getCreatedTopics($userId)
    {


        $sql = "SELECT id, title, description, created_at FROM Topics WHERE user_id = :user_id ORDER BY created_at ASC, id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getVoteResults($topicID): array
    {

        return [];
    }
}

class Vote
{
    private $pdo;

    public $id;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // vote($userId, $topicId, $voteType): bool
    public function vote($userId, $topicId, $voteType)
    {

        if ($this->hasVoted($topicId, $userId)) {
            return false;
        }

        $sql = "INSERT INTO Votes (user_id, topic_id, vote_type, voted_at) VALUES (:user_id, :topic_id, :vote_type, NOW())";
        $stmt = $this->pdo->prepare($sql);

        $ok = $stmt->execute([
            ':user_id' => $userId,
            ':topic_id' => $topicId,
            ':vote_type' => $voteType
        ]);
        return $ok;
    }


    // hasVoted($topicId, $userId): bool
    public function hasVoted($topicId, $userId)
    {

        $sql = "SELECT id FROM Votes WHERE topic_id = :topic_id AND user_id = :user_id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':topic_id' => $topicId,
            ':user_id' => $userId
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? true : false;

    }

    // Not in the tests, but okay to keep if you want
    public function getUserVoteHistory($userId)
    {

        $sql = "SELECT topic_id, vote_type, voted_at FROM Votes WHERE user_id = :user_id ORDER BY voted_at DESC, id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}


class Comment
{
    //We have made the PDO Private so no users cannot access
    private $pdo;

    private $id;

    private $topicId;

    private $userId;

    private $comment;

    private $comment_At;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    //Adding the comment
    public function addComment($userId, $topicId, $commentText)
    {

        $sql = "INSERT INTO Comments(user_id, topic_id, comment, commented_at) VALUES (:user_id, :topic_id, :comment, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $output = $stmt->execute([
            ':user_id' => $userId,
            ':topic_id' => $topicId,
            ':comment' => $commentText
        ]);
        return $output;


    }


    //Get the Comment //Using an array
    public function getComments($topicId)
    {


        $sql = "SELECT user_id, topic_id, comment, commented_at FROM Comments WHERE topic_id = :topic_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":topic_id" => $topicId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);


    }

}

const SECONDS = 1;  //1 second
const MINUTES = 60 * SECONDS; //60 seconds -> minutes
const HOURS = 60 * MINUTES; //60 minutes -> 1 hour
const Days = 24 * HOURS; //1 Days 24 hours
const Weeks = 7 * Days; //1 week

const Months = 30 * Days; //1 month
const Years = 365 * Days; // 1 Year

class TimeFormatter
{


    public static function formatTimestamp($timestamp)
    {
        $currenttime = time();
        $timediff = $currenttime - $timestamp; //this difference is in seconds
        if ($timediff < MINUTES) {
            return $timediff . " seconds ago";
        }
        if ($timediff < HOURS) {
            $minutes = floor($timediff / MINUTES); //if it is less than an hour it will divide the amount of minutes.
            return $minutes . " minutes ago";
        }
        if ($timediff < Days) {
            $hours = floor($timediff / HOURS);
            return $hours . " hours ago";
        }
        if ($timediff < Weeks) {
            $days = floor($timediff / Days);
            return $days . " days ago";
        }
        if ($timediff < Months) {
            $weeks = floor($timediff / Weeks);
            return $weeks . " weeks ago";
        }
        if ($timediff < Years) {
            $years = floor($timediff / Months);
            return $years . " months ago";
        } elseif ($timediff > Years) {
            return date("M d, Y", $timestamp);
        }
    }
}
