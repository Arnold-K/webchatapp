<?php
class MessagesModel {
    private $database = null;
    private $message = [];

    public function __construct(){
        $this->database = new DB_Connect();
    }

    public function getIdByUsername($username){
        $query = 'SELECT id FROM users WHERE username=?';
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->database->closeConnection();
        return $result->fetch_assoc();
        
    }

    public function sendMessage($id, $content){
        $query = "INSERT INTO messages (message, sender, receiver, status, seen) 
        VALUES (?,?,?,?,?)";
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->bind_param('sii', $content, $_SESSION['user_id'], $id, 1, 0);
        $stmt->execute();
        $this->database->closeConnection();
        return true;
    }

}