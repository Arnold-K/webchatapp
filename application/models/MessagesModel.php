<?php
class MessagesModel {
    private $db;
    public function __construct() {
        if(class_exists('Db_Connect')){
            $this->db = new Db_Connect();
        } else {
            require APP . 'config/db_connect.php';
            $this->db = new Db_Connect();
        }
    }

    