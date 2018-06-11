<?php
class MessagesController {
    private $model = null;
    private $result_array = [];

    public function __construct(){
        require APP . 'models/messagesmodel.php';
        $this->model = new MessagesModel();
        session_start();
        header('Content-Type: application/json');
    }

    public function sendMessage(){
        if ($_SERVER['REQUEST_METHOD'] != "POST"){
            http_response_code(400);
            exit();
        }
        if(!isset($_POST['username']) || !isset($_POST['messagecontent'])){
            http_response_code(400);
            exit();
        }

        $content = $_POST['messagecontent'];
        $receiver = $this->getIdByUsername($_SESSION['username']);
        if($this->result_array = $this->model->sendMessage($receiver, $content)){
            $this->result_array['code'] = 200;
        }
        echo json_encode($this->result_array);
    }

    private function getIdByUsername($username){
        return $this->model->getIdByUsername($username)['id'];
    }
}

    