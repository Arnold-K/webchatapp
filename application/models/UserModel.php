<?php
class UserModel {
    private $database = null;

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
}