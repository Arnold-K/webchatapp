<?php
class Db_Connect {
    private $connection = null;

    //GET CONNECTION
    public function getConnection(){
        if(!isset($this->connection)){
            $this->setConnection();
            if(isset($this->connection)){
                return $this->connection;
            } else {
                $config = new config();
                $config->prepareDatabase();
                $this->setConnection();
                return $this->connection;
            }
        }
    }

    //SET CONNECTION
    public function setConnection(){
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }

    //CLOSE CONNECTION
    public function closeConnection(){
        $this->connection->close();
    }
}

