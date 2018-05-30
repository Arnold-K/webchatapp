<?php
class AdminController{
    private $loginModel;    
    public function __construct() {
        session_start();
        ob_start();
        //called immediately
        require APP . 'models/LoginModel.php';
        $this->loginModel = new LoginModel();
    }
    public function index(){
        //this will be called by application.php
        require APP . 'views/admin.php';
        if($this->loginModel->isLoggedIn()){
            header("Location: ".PUB."admin/home");
        }
    }
    public function login(){
        $username = "";
        $password = "";
        $remember = "";
        if(isset($_POST['username']) && isset($_POST['password'])){
            $username = $_POST['username'];
            $password = $_POST['password'];
            @$remember = $_POST['remember'];
            if( $this->loginModel->logIn($username, $password)){//login success
                header("Location: ". PUB . "admin/home");
                ob_end_flush();
            } 
            header("Location: " . PUB . "admin");
        } else {
            header("Location: " . PUB . "admin");
        }
    }
    public function home(){
        if(!$this->loginModel->isLoggedIn()){header ("Location: " . PUB . "admin/login");}
        require APP . 'views/admin.home.php';
    }
    public function logout(){
        if (isset($_SESSION['user_id'])) {
            session_destroy();
            header("Location: " . PUB . "home");
        } else { header("Location: " . PUB . "admin"); }
    }
    
    public function newbudget(){
        if(!$this->loginModel->isLoggedIn()){header ("Location: " . PUB . "admin/login");}
        require APP . 'models/AdminModel.php';
        $adminModel = new AdminModel();
        if(!$this->loginModel->isLoggedIn()){
            header ("Location: " . PUB . "admin/login");
        }
        $json_object = [];
        
        //inserting genders
        $genders = [];
        $ages = [];
        $educations = [];
        $budget_year = $_GET['year_i_0'];
        
        $municipality = $_GET['municipality_i_0'];
        for($i=0;$i<25;$i++){
            $indexing = (string)('gender_i_'.$i);
            if(isset($_GET[$indexing])){
                $genders[$i] = $_GET[$indexing];
            } else {
                break;
            }
        }
        
        //geting ages
        for($i=0;$i<25;$i++){
            $indexing = (string)('age_i_'.$i);
            if(isset($_GET[$indexing])){
                $ages[$i] = $_GET[$indexing];
            } else {
                break;
            }
        }
        
        //getting education
        for($i=0;$i<25;$i++){
            $indexing = (string)('edu_i_'.$i);
            if(isset($_GET[$indexing])){
                $educations[$i] = $_GET[$indexing];
            } else {
                break;
            }
        }
        
        //get municipality
        
        
        //getparents and childs
        $elements = [];
        $temp = [];
        for($i=0;$i<25;$i++){
            if(isset($_GET['parent_i_'.$i])){
                
                
                $parent = $_GET['parent_i_'.$i];//set the parent
                array_push($temp, $parent);
                
                
                for($j=0;$j<25;$j++){
                    if(isset($_GET['child_'.$i.'_i_'.$j])){
                        $temp[$j+1] = $_GET['child_'.$i.'_i_'.$j];
                    } else break;
                }
                $elements[$_GET['parent_i_'.$i]] = $temp;
                $temp = [];
                
            } else break;
        }
        $data = array_values($elements);
        if($adminModel->insertNewBudget($genders, $ages, $educations, $budget_year, $municipality, $data)){
            $message = "Data written successfully";
            header("Location: ".PUB . "admin/home");
        }
    }
    public function displayData($datatype = [], $municipality = []){
        session_start();
        if(count($datatype)==0){
            require APP . 'controllers/problemController.php';
            $page = new ProblemController();
            $page->index();
        }
        require APP . 'models/AdminModel.php';
        $adminModel = new AdminModel();
        $array = array();
        if($datatype=="municipalities"){
            // print_r($_SESSION);
            $data = $adminModel->getMunicipalities($_SESSION['user_id']);
            $i=0;
            while($row = $data->fetch_assoc()){
                $array[$i] = utf8_encode($row['name']);
                $i++;
            }
            echo json_encode($array);
           // header('Content-Type: application/json; charset=utf-8');
            return;
        } else if($datatype=="budget"){
            $data = $adminModel->getBudget($municipality);
            $data_array = array();
            $i=0;
            while ($row = $data->fetch_assoc()){
                $row['description'] = utf8_encode($row['description']);
                $row['bltype'] = utf8_encode($row['bltype']);
                $data_array[$i]=$row;
                $i++;
            }
            header('Content-Type: application/json');
            echo json_encode($data_array);
            
            return;
        }
        require APP . 'controllers/problemController.php';
        $page = new ProblemController();
        $page->index();     
        return;
    }
    
    
    public function results($muncipality=[], $viti=[]) {
        if(strlen($muncipality)!=0 && strlen($viti)!=0){
            
            if(!class_exists("AdminModel")){
                require APP . 'models/AdminModel.php';
            }
            
            //get budget line
            $adminModel = new AdminModel();
            $data = $adminModel->getBudget($muncipality);
            $data_array = array();
            $i=0;
            while ($row = $data->fetch_assoc()){
                $row['description'] = utf8_encode($row['description']);
                $row['bltype'] = utf8_encode($row['bltype']);
                $data_array[$i]= $row;
                $i++;
            }
            
            //get vote data
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            $adminModel = new AdminModel();
            session_start();
            // print_r($_SESSION);
            // exit();

            $data;
            if($_SESSION['user_role']!=99){
                $data = $adminModel->getoneMunipAllResults($_SESSION['user_id'], $viti);
            } else {
                $data = $adminModel->getAllResults($muncipality, $viti);
            }
            

            $other_array = array();
            $i=0;
            
            while ($row = $data->fetch_assoc()) {
                $row['bltype']= utf8_encode($row['bltype']);
                $row['description']= utf8_encode($row['description']);
                $row['education']= utf8_encode($row['education']);
                $row['notes']= utf8_encode($row['notes']);
                $other_array[$i] = $row;
                $i++;
            }
            //budeget line only (no results)
            $budget_line = $data_array;
            
            //generates JSON files for the view section
            if(isset($_GET['type'])){
                if($_GET['type']=='budget_line'){
                    echo json_encode($data_array); // budget line only
                    exit();
                } else
                if($_GET['type']=='results'){
                    echo json_encode($other_array); // results data
                    exit();
                } 
            } else die('Ju nuk keni akses ne kete faqe.');
            
            //vote data
            $selected_budget_line = $other_array; // vote data
            $html_data;
            $html_data = '<table id="example" class="display nowrap" cellspacing="0" width="100%">';
            $parent_html = '';
            $child_html = '';
            
            
            
            $count = 0;
            for($i=0;$i<count($budget_line);$i++){ // budget line only
                if(strlen($budget_line[$i]['description']) ==0) continue;
                $parent_html.='<th>'.'p'.$count. '' . $budget_line[$i]['bltype'] .'</th>';
                $child_html.='<th>'.'p'.$count. '' . $budget_line[$i]['description'] .'</th>';
                
                $count++;
            }
            $parent_html.='<th>Gjini</th><th>Mosha</th><th>Arsimimi</th><th>Sugjerime</th>';
            $child_html.='<th>Gjini</th><th>Mosha</th><th>Arsimimi</th><th>Sugjerime</th>';
            
            
//            $html_data .= '<thead><tr>';
//            $html_data.=$parent_html;
//            $html_data .='</tr></thead>';
            
            $html_data.= '<thead><tr>';
            $html_data.=$child_html;
            $html_data .='</tr></thead>';
            
            $html_data.= '<tfoot><tr>';
            $html_data.=$parent_html;
            $html_data .='</tr></tfoot>';
            
            $html_data .='<tbody>';
            
//            $count = 0;
//            for($i=0;$i<count($budget_line);$i++){
//                if(strlen($budget_line[$i]['description']) ==0) continue;
//                $html_data.="<tr>";
//                for($j=0;$j<count($selected_budget_line);$j++){
//                    if( $budget_line[$i]['bltype'] != $selected_budget_line[$j]['bltype'] ) continue;
//                    if( $budget_line[$i]['description'] == $selected_budget_line[$j]['description'] ){
//                        $html_data.='<td>1</td>';
//                    } else {
//                        $html_data.='<td>0</td>';
//                    }
//                    $html_data.='<td>'. $selected_budget_line[$j]['gender'] .'</td>';
//                    $html_data.='<td>'. $selected_budget_line[$j]['age'] .'</td>';
//                    $html_data.='<td>'. $selected_budget_line[$j]['education'] .'</td>';
//                }
//                $html_data.="</tr>";
//                $count++;
//            }
            // note start the loop from the other side
//            
//            echo '<pre>';
//            print_r($selected_budget_line[1]['id_profile']);
            
            //getting all the budget line;
            function getResultArray($child, $id_profile, $array){
                for($i=0;$i<count($array);$i++){
                    if($array[$i]['description']==$child && $id_profile==$array[$i]['id_profile']){
                        return true;
                    }
                }
                return false;
            }


            $all_elements = array();
            $all_elements_parents = array();
                    
            
            for($j=0;$j<count($budget_line);$j++){
                if(strlen($budget_line[$j]['description']) ==0) continue;
                array_push($all_elements, utf8_decode($budget_line[$j]['description']));
                array_push($all_elements_parents, utf8_decode($budget_line[$j]['bltype']));
            }
            $id_profile=0;
            for($i=0;$i<count($selected_budget_line);$i++){
                if($id_profile!=0 && $id_profile!=$selected_budget_line[$i]['id_profile']){
                    $html_data.='</tr>';
                }
                if($id_profile!=$selected_budget_line[$i]['id_profile']){
                    $id_profile=$selected_budget_line[$i]['id_profile'];
                    $html_data.='<tr>';
                    for($j=0;$j<count($all_elements);$j++){
                    if(getResultArray(utf8_decode($all_elements[$j]), $id_profile, $selected_budget_line)){
                        $html_data.='<td>1</td>';
                    } else {
                        $html_data.='<td>0</td>';
                    }
                }
                    $html_data.='<td>'. $selected_budget_line[$i]['gender'].'</td>';
                    $html_data.='<td>'. $selected_budget_line[$i]['age'].'</td>';
                    $html_data.='<td>'. $selected_budget_line[$i]['education'].'</td>';
                    $html_data.='<td>'. utf8_decode($selected_budget_line[$i]['notes']).'</td>';
                }
                
            }
            
            
            
            
            $html_data .='</tbody>';
            $html_data .='</table>';
            require APP . 'views/admin.results.php';
        } else {
            echo 'failed';
//            require APP . 'controllers/problemController.php';
//            $page = new ProblemController();
//            $page->index();
        }
        
        
    }
    
    public function showresults(){
        if(!class_exists("AdminModel")){
            require APP . 'models/AdminModel.php';
        }
        
        function getResultNumber($user_id, $viti){
            $adminModel = new AdminModel();
            // $data = $adminModel->getAllResults($municipality, $viti);
            $data = $adminModel->getoneMunipAllResults($user_id, $viti);
            return $data->num_rows;
        }
        function getMunipResultNumber($municipality, $viti){
            $adminModel = new AdminModel();
            // $data = $adminModel->getAllResults($municipality, $viti);
            $data = $adminModel->getAllResults($municipality, $viti);
            return $data->num_rows;
        }
        $mun_durres;
        $mun_elbasan;
        $mun_korce;
        if($_SESSION['user_role'] != 99){
            $mun_durres = getResultNumber($_SESSION['user_role'], 2018); 
        }else {
            $mun_durres = getMunipResultNumber('durres', 2018); 
            $mun_elbasan = getMunipResultNumber('elbasan', 2018); 
            $mun_korce = getMunipResultNumber('korce', 2018); 
        }
        require APP . 'views/admin.results.php';
    }
    
    
    


}

