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
        if($result->num_rows > 0){
            $this->message['code'] = 200;
            $this->message['data'] = $result->fetch_assoc();
            return $this->message;
        }
        $this->message['code'] = 404;
        return $this->message;
    }

    public function sendMessage($id, $content){
        $query = "INSERT INTO messages (message, sender, receiver, status, seen) 
        VALUES (?,?,?,1,0)";
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->bind_param('sii', $content, $_SESSION['user_id'], $id);
        $stmt->execute();
        $this->database->closeConnection();
        return true;
    }
    public function getMessagesNames(){
        // SELECT column_name(s)
        // FROM table1
        // INNER JOIN table2 ON table1.column_name = table2.column_name;
        $query = 'SELECT distinct users.id,users.name FROM users inner join messages 
                    on users.id=messages.receiver or users.id=messages.sender where messages.status=1';
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->database->closeConnection();
        while($data = $result->fetch_assoc()){
            array_push($this->message, $data);
        }
        return $this->message;
    }

    public function getMessagesUser($id){
        $query = "select message from messages where (sender=? or receiver=?) and status=1";
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->bind_param('ii', $id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->database->closeConnection();
        while($data = $result->fetch_assoc()){
            array_push($this->message, $data);
        }
        $query = "select message from messages where (sender=? or receiver=?) and status=1";
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->bind_param('ii', $_SESSION['user_id'], $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->database->closeConnection();
        while($data = $result->fetch_assoc()){
            array_push($this->message, $data);
        }
        //print_r($this->message);
        return $this->message;
    }

    public function deleteMessages($id){
        $query = "UPDATE messages SET status=0 WHERE status=1 and receiver=? and sender=?";
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->bind_param('ii', $id, $_SESSION['user_id']);
        $stmt->execute();

        $query = "UPDATE messages SET status=0 WHERE status=1 and receiver=? and sender=?";
        $stmt = $this->database->getConnection()->prepare($query);
        $stmt->bind_param('ii', $_SESSION['user_id'], $id);
        $stmt->execute();
    }

}