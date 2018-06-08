<?php
class UserModel {
    private $database = null;
    private $message = [];

    public function __construct(){
        $this->database = new DB_Connect();
    }

    public function getAllUsers(){
        $query = "select * from users";
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->database->closeConnection();
        return $result->fetch_assoc();
    }

    public function login($username, $email, $password){
        $query = "SELECT id,name,username,email,role,updated_at,last_login_at FROM users WHERE status=1 AND password=? AND (username=? or email=?) LIMIT 1";
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->bind_param('sss', $password, $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        //  Credential missmatch
        if($result->num_rows == 0){
            return $this->message;
        }

        //updating last login at
        $query = "INSERT INTO users (last_login_at) VALUES (current_timestamp)";
        $this->database->getConnection()->query($query);

        //closing connection
        $this->database->closeConnection();

        //  Credentials correct = Login successfull
        $this->message['data'] = $result->fetch_assoc();
        return $this->message;
    }

    public function signup($name, $username, $email, $password, $role){
        if($this->emailExists($email)){
            $this->message['status'] = 0;
            $this->message['error'] = "email already exists";
            $this->database->closeConnection();
            return $this->message;
        }
        if($this->usernameExists($username)){
            $this->message['status'] = 0;
            $this->message['error'] = "username already exists";
            $this->database->closeConnection();
            return $this->message;
        }

        $query = "INSERT INTO users (name, username, email, password, role, updated_at, last_login_at) VALUES (?, ?,?, ? , ? , current_timestamp, current_timestamp)";
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->bind_param('ssssi', $name, $username, $email, $password, $role);
        $stmt->execute();
        $this->message['status'] = 1;
        $this->database->closeConnection();
        return $this->message;
    }

    private function emailExists($email){
        $query = "SELECT email FROM users WHERE email=? LIMIT 1";
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0)
            return true;
        return false;
    }
    private function usernameExists($username){
        $query = "SELECT username FROM users WHERE username=? LIMIT 1";
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0)
            return true;
        
        return false;
    }
}