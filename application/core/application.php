<?php

class Application{
	private $url_controller = null;
	private $url_action = null;
    private $url_params = array();
    private $api = false;
	
	public function __construct()	{
            $this->splitUrl();
            if (!$this->url_controller && $this->api == false) {
                require APP . 'controllers/indexController.php';
                $this->checkURL();

            } else if (file_exists(APP . 'controllers/' . $this->url_controller . 'Controller.php') && $this->api == false) {
                require APP . 'controllers/' . $this->url_controller . 'Controller.php';
                $this->checkURL();

            } else  if (file_exists(APP . 'controllers/' . preg_replace('/\.php/', "Controller.php", $this->url_controller)) && $this->api == false){
                $this->url_controller = preg_replace('/\.php/', "", $this->url_controller);
                require APP . 'controllers/' . $this->url_controller . 'Controller.php';
                $this->checkURL();

            } else if(!$this->url_controller) {
                require APP . 'controllers/api/indexController.php';
                $this->checkURL();
                
            }  else if (file_exists(APP . 'controllers/api/' . $this->url_controller . 'Controller.php')) {
                require APP . 'controllers/api/' . $this->url_controller . 'Controller.php';
                $this->checkURL();

            }   else if (file_exists(APP . 'controllers/api/' . preg_replace('/\.php/', "Controller.php", $this->url_controller))){
                $this->url_controller = preg_replace('/\.php/', "", $this->url_controller);
                require APP . 'controllers/api/' . $this->url_controller . 'Controller.php';
                $this->checkURL();
            }
            
            else {
                $this->error();
            
            }
        }	
	
	private function checkURL(){
            $this->url_controller.= "Controller";            
            if($this->url_controller == 404){
                
                    $this->error();
            } else if (method_exists($this->url_controller, $this->url_action)) {
                    if (!empty($this->url_params)) {
                        @call_user_func_array(array($this->url_controller, $this->url_action), $this->url_params);
                    } else {
                        $this->url_controller = new $this->url_controller();
                        $this->url_controller->{$this->url_action}();
                    }
                    
                } else {
                    if (strlen($this->url_action) == 0) {
                        if(!class_exists($this->url_controller) && $this->url_controller=='Controller') $this->url_controller = 'indexController';
                        $this->url_controller = new $this->url_controller();
                        if(method_exists($this->url_controller, 'index'))
                            $this->url_controller->index();
                    }
                    
                    else {
                        $this->error();
                    }
                }
            
	}
	
	private function splitUrl()	{
		if (isset($_GET['url'])) {
			$url = trim($_GET['url'], '/');
			$url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            if(isset($url[0])){
                if ($url[0]!='api') {
                    $this->url_controller = $url[0];
                    $this->url_action = isset($url[1]) ? $url[1] : null;
                    unset($url[0], $url[1]);
                } else {
                    $this->api = true;
                    $this->url_controller = isset($url[1]) ? $url[1] : null;
                    $this->url_action = isset($url[2]) ? $url[2] : null;
                    unset($url[0], $url[1], $url[2]);
                }
            }
			$this->url_params = array_values($url);

		}
    }
    
    private function error(){
        if(file_exists(APP . 'controllers/problemController.php')){
            require APP . 'controllers/problemController.php';
            $page = new ProblemController();
            if(method_exists($page, 'index'))
                $page->index();
        }else {
            die('Requested operation failed!');
        }
        exit();
        
    }
}
