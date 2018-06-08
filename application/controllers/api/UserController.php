<?php
class UserController {
    private $model = null;
    private $result_array = [];

    public function __construct(){
        require APP . 'models/usermodel.php';
        $this->model = new UserModel();
        session_start();
        header('Content-Type: application/json');
    }

    public function getAllUsers(){
        print json_encode($this->model->getAllUsers());
    }

    public function signin(){
        if ($_SERVER['REQUEST_METHOD'] != "POST"){
            http_response_code(400);
            exit();
        }
        $this->result_array = $this->model->login($_POST['username'], $_POST['username'],$_POST['password']);
        $this->result_array['remember'] = (isset($_POST['remember']))?true:false;
        echo json_encode($this->result_array);
    }

    public function signup(){
        if ($_SERVER['REQUEST_METHOD'] != "POST"){
            http_response_code(400);
            exit();
        }

        //check if the values are set;
        if(!isset($_POST['name']) || !isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['confirm'])){
            http_response_code(400);
            exit();
        }
        

        //validating before inserting in the database
        if (preg_match("/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/i", $_POST['name'])) {
            http_response_code(400);
            exit();
        }
        if ( !preg_match("%^[A-Za-z][A-Za-z0-9]{5,31}$%", $_POST['username']) ){
            http_response_code(400);
            exit();
        }
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            exit();
        }
        if($_POST['password'] != $_POST['confirm']){
            http_response_code(400);
            exit();
        }

        $role = 3;
        $this->result_array = $this->model->signup($_POST['name'], $_POST['username'], $_POST['email'], $_POST['password'], $role);
        if($this->result_array['status'] == 0){
            echo json_encode($this->result_array);
            exit();
        }
        //perform sign in and tell javascript to relocate

    }

    public function signout(){

    }

    public function deleteUser(){

    }


}