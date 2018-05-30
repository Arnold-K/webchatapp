<?php
class UserController {
    private $model = null;

    public function __construct(){
        require APP . 'models/usermodel.php';
        $this->model = new UserModel();
        header('Content-Type: application/json');
    }

    public function getAllUsers(){
        http_response_code(200);
        print json_encode($this->model->getAllUsers());
    }


}