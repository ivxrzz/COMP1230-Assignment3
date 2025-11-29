<?php
// User class (ivor) testing new changes in class.php
class User {
    private $pdo;
//Testing something hello
    private $id;
    private $username;
    private $email;
    private $passwordHash;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int) $id;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($username)
    {
        $this->username = $username;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }
    public function setPasswordHash($hash)
    {
        $this->passwordHash = $hash;
    }
    public function registerUser($username, $email, $password)
    {
        if(trim($username) == "")
        {
            return false;
        }
        if(strlen($password) < 9)
        {
            return false;
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return false;
        }
        try
        {
            $sql = "SELECT id FROM users WHERE username = :username LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':username'=> $username]);
            if($stmt->fetch())
            {
                return false;
            }
            $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email'=> $email]);
            if($stmt->fetch())
            {
                return false;
            }
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $passwordHash
            ]);

            return $ok;
        } catch (PDOException $e)
        {
            return false;
        }
    }
    public function authenticateUser($username, $password)
    {
        try {
            $sql = "SELECT id, username, email, password FROM users WHERE username = :username LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$user)
            {
                return false;
            }
            if(password_verify($password, $user['password']))
            {
                return false;
            }
            $this->setId($user['id']);
            $this->setUsername($user['username']);
            $this->setEmail($user['email']);
            $this->setPasswordHash($user['password']);

            return true;
        } catch (PDOException $e)
        {
            return false;
        }
    }
}

class Topic
{
    private $pdo;
    private $id;
    private $userId;
    private $title;
    private $description;
    private $createdAt;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = (int)$userId;
    }

    public function getTitle()
    {
        return $this->title;
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

class Vote
{

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
