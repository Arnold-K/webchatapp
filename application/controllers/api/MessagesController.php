<?php
class UserController {
    private $model = null;
    private $result_array = [];

    public function __construct(){
        require APP . 'models/messagesmodel.php';
        $this->model = new MessagesModel();
        session_start();
        header('Content-Type: application/json');
    }

    