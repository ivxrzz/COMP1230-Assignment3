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
        if (trim($username) === '') {
            return false;
        }

        if (strlen($password) < 9) {
            return false;
        }

        // Email must be valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        try {
            // Check duplicate username
            $sql = "SELECT id FROM users WHERE username = :username LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':username' => $username]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                return false;
            }

            // Check duplicate email
            $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                return false;
            }

            // Hash password and insert
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $passwordHash
            ]);

            return $ok;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function authenticateUser($username, $password): bool
    {
        try {
            $sql = "SELECT id, username, email, password FROM users WHERE username = :username LIMIT 1";
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
        } catch (PDOException $e) {
            return false;
        }
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
        try {
            $sql = "INSERT INTO topics (user_id, title, description, created_at) VALUES (:user_id, :title, :description, NOW())";
            $stmt = $this->pdo->prepare($sql);

            $ok = $stmt->execute([
                ':user_id' => $userId,
                ':title' => $title,
                ':description' => $description
            ]);

            return $ok;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getTopics()
    {
        $topics = [];

        try {
            $sql = "SELECT id, user_id, title, description, created_at FROM topics ORDER BY created_at ASC, id ASC";
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
        } catch (PDOException $e) {

        }

        return $topics;
    }

    public function getCreatedTopics($userId)
    {
        $result = [];

        try {
            $sql = "SELECT id, title, description, created_at FROM Topics WHERE user_id = :user_id ORDER BY created_at ASC, id ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

        }

        return $result;
    }
}

class Vote
{
    private $pdo;

    public $id;
    public $userId;
    public $topicId;
    public $voteType;
    public $votedAt;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // vote($userId, $topicId, $voteType): bool
    public function vote($userId, $topicId, $voteType)
    {
        // Only allow 'up' or 'down'
        if ($voteType !== 'up' && $voteType !== 'down') {
            return false;
        }

        // Prevent duplicate vote
        if ($this->hasVoted($topicId, $userId)) {
            return false;
        }

        try {
            $sql = "INSERT INTO Votes (user_id, topic_id, vote_type, voted_at) VALUES (:user_id, :topic_id, :vote_type, NOW())";
            $stmt = $this->pdo->prepare($sql);

            $ok = $stmt->execute([
                ':user_id' => $userId,
                ':topic_id' => $topicId,
                ':vote_type' => $voteType
            ]);

            return $ok;
        } catch (PDOException $e) {
            return false;
        }
    }

    // hasVoted($topicId, $userId): bool
    public function hasVoted($topicId, $userId)
    {
        try {
            $sql = "SELECT id FROM Votes WHERE topic_id = :topic_id AND user_id = :user_id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':topic_id' => $topicId,
                ':user_id' => $userId
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? true : false;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Not in the tests, but okay to keep if you want
    public function getUserVoteHistory($userId)
    {
        $history = [];

        try {
            $sql = "SELECT topic_id, vote_type, voted_at FROM Votes WHERE user_id = :user_id ORDER BY voted_at DESC, id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

        }

        return $history;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
    public function createTopic($username, $title, $description)
    {
        try
        {
            $sql = "INSERT INTO topics (user_id, title, description, created_at) VALUES (:user_id, :title, :description, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':user_id' => $username,
                ':title' => $title,
                ':description' => $description
            ]);
            return $ok;
        } catch (PDOException $e)
        {
            return false;
        }
    }
    public function getTopics()
    {
        $topics = [];
        try {
            $sql = "SELECT id, user_id, title, description, created_at FROM topics ORDER BY created_at DESC";
            $stmt = $this->pdo->query($sql);

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $topic = new Topic($this->pdo);
                $topic->setId($row['id']);
                $topic->setUserId($row['user_id']);
                $topic->setTitle($row['title']);
                $topic->setDescription($row['description']);
                $topic->setCreatedAt($row['created_at']);
                $topics[] = $topic;
            }
        } catch (PDOException $e) {

        }
        return $topics;
    }
    public function getCreatedTopics($userId)
    {
        $result = [];
        try {
            $sql = "SELECT id, user_id, title, description, created_at FROM topics WHERE user_id = :user_id ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

        }
        return $result;
    }
}



class Comment{
    //We have made the PDO Private so no users cannot access
    private $pdo;

    private $id;

    private $topicId;

    private $userId;

    private $comment;

    private $comment_At;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    //Adding the comment
    public function addComment($userId, $topicId, $commentText){
        try{
            $sql = "INSERT INTO Comments(user_id, topic_id, comment, comment_at) VALUES (:user_id, :topic_id, :comment, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $output = $stmt->execute([
                ':user_id' => $userId,
                ':topic_id' => $topicId,
                ':comment' => $commentText
            ]);
            return $output;
        }catch(PDOException $e){
            //If something goes wrong it will return false
            return false;
        }


    }


    //Get the Comment //Using an array
    public function getComments($topicId){

        $result = [];

        try{
            $sql = "SELECT user_id, topic_id, comment, comment_at FROM Comments WHERE topic_id = :topic_id ORDER BY comment_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':topic_id' => $topicId,
                 ':user_id' => $this->userId
            ]);
        }catch (PDOException $e){

        }

        return $result;

        }

}
