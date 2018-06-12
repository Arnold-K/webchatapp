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
        if($this->getIdByUsername($_POST['username'])['code']==404){
            http_response_code(404);
            exit();
        }
        $receiver = $this->getIdByUsername($_POST['username']);
        if($receiver['code']==404){
            http_response_code(404);
            $this->result_array['code'] = 404;
            $this->result_array['message'] = "user not found";
        }
        $model_call = $this->model->sendMessage($receiver['data']['id'], $content);
        if($model_call){
            http_response_code(200);
            $this->result_array['code'] = 200;
            $this->result_array['message'] = "message has been delivered";
        } else {
            http_response_code(400);
            $this->result_array['code'] = 400;
            $this->result_array['message'] = "message could not be sent";
        }
        
        echo json_encode($this->result_array);
    }
    
    public function sendMessageById(){
        if(!isset($_POST['id']) || !isset($_POST['messagecontent'])){
            http_response_code(400);
            exit();
        }
        $content = $_POST['messagecontent'];
        $receiver = $_POST['id'];
        $model_call = $this->model->sendMessage($receiver, $content);
        if($model_call){
            http_response_code(200);
            $this->result_array['code'] = 200;
            $this->result_array['message'] = "message has been delivered";
        } else {
            http_response_code(400);
            $this->result_array['code'] = 400;
            $this->result_array['message'] = "message could not be sent";
        }
        
        echo json_encode($this->result_array);
    }

    public function getMessagesNames(){
        if ($_SERVER['REQUEST_METHOD'] != "GET"){
            http_response_code(400);
            exit();
        }

        $model_call = $this->model->GetMessagesNames();
        $this->result_array = $model_call;
        echo json_encode($this->result_array);
    }
    
    private function getIdByUsername($username){
        return $this->model->getIdByUsername($username);
    }
    
    public function getMessagesUser($id){
        if ($_SERVER['REQUEST_METHOD'] != "GET"){
            http_response_code(400);
            exit();
        }
        $result_array = [];
        $model;
        if(!class_exists("MessagesModel")){
            require APP . 'models/messagesmodel.php';
            $model = new MessagesModel();
        }
        $model_call = $model->getMessagesUser($id);
        $result_array = $model_call;
        header('Content-Type: application/json');
        echo json_encode($result_array);
    }

    public function deleteMessages(){
        $this->model->deleteMessages($_POST['id']);
    }
}

    