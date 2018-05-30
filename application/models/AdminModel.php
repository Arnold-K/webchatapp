<?php
class AdminModel {
    private $db;
    public function __construct() {
        //require APP . 'config/db_connect.php';
        if(class_exists('Db_Connect')){
            $this->db = new Db_Connect();
        } else {
            require APP . 'config/db_connect.php';
            $this->db = new Db_Connect();
        }
    }
    
    public function insertConfiguration($municipality, $budget_year, $status, $user_id){
        $this->db->checkConnection();
        $query = "INSERT INTO profiles (municipality, budget_year, status, user_id) "
                . "VALUES (?, ?, ?)";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('ss',$username);
        $stmt->execute();
        $this->db->closeConnection();
    }
    
    public function getMunicipalities($user_id){
        $this->db->checkConnection();
        $query;
        if($user_id==99){
            $query = "SELECT municipalities.id, municipalities.name
            from municipalities";
        }else {
            $query = "SELECT municipalities.id, municipalities.name
            from municipalities 
            inner join users 
            on municipalities.id = users.id_municipality
            where users.id=?";
        }
        
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('s',$user_id);
        $stmt->execute();
        $results = $stmt->get_result();        
        //$results = $results->fetch_assoc();
        $this->db->closeConnection();
        return $results;
    }
    function getBudget($municipality){
        $this->db->checkConnection();
        $query = "select budget_line.id, budget_line.description, budget_line.parent, budget_line.bltype 
            from ((budget_line 
            INNER JOIN config on config.id = budget_line.id_config_buget)
            INNER JOIN municipalities on config.municipality = municipalities.id)
            WHERE config.status=1 and municipalities.name=?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('s',$municipality);
        $stmt->execute();
        $results = $stmt->get_result();   
        //$results = $results->fetch_assoc();
        $this->db->closeConnection();
        return $results;
    }
    public function insertNewBudget($genders, $ages, $educations, $budget_year, $municipality, $parent_childs){
        $this->db->checkConnection();
        $user_id = ($_SESSION['user_id']);
        $municipality_index=-1;
        $id_config_budget = -1;
        
//        $val1 = "James";
//        $val2 = 123;
//        
//        //insert into config table;
//        $id_config = 0;
//        $this->db->checkConnection();
//        $query = "INSERT INTO test (test_string, test_int,test_date) VALUES (?, ?, current_timestamp)";
//        $stmt = $this->db->getConnection()->prepare($query);
//        $stmt->bind_param('si', $val1, $val2);
//        $stmt->execute();      
        
        //get municipality index;
        $stmt = $this->db->getConnection()->prepare("SELECT id FROM `municipalities` WHERE name=?");
        $stmt->bind_param('s',$municipality);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_assoc();
        $municipality_index = $result['id'];
        //insert into config table;
        $id_config = 0;
        $date = new DateTime();
        $date->setDate((int)$budget_year, 1, 1);
        $date = $date->format('Y-m-d');
       //cho $date; exit();
       //
       //
        //updating config
        
        $query = "UPDATE config SET status=0 where municipality=? and status=1";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('i',$municipality_index);
        $stmt->execute();
        $yearthis = $budget_year."-01-01";
        $query = "delete from config where municipality=? and budget_year=?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('is',$municipality_index, $yearthis);
        $stmt->execute();
        
        //inserting config 
        $query = "INSERT INTO config (municipality, budget_year, status, id_user) "
                . "VALUES (?, ?, 1, ?)";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('isi',$municipality_index,$date,  $user_id);
        $stmt->execute();
        $id_config_budget = $stmt->insert_id;
        echo $stmt->insert_id;
        //echo '<pre>';
        
        $father_id;
        $father_element = "";
        for($i=0;$i<count($parent_childs);$i++){
            for($j=0;$j<count($parent_childs[$i]);$j++){
                if($j==0){
                    $query = "INSERT INTO budget_line (description, parent, bltype,notes,id_config_buget) values('',0,?,'',?)";
                    $stmt = $this->db->getConnection()->prepare($query);
                    $father_element = utf8_decode($parent_childs[$i][$j]);
                    $stmt->bind_param('si', $father_element, $id_config_budget);
                    $stmt->execute();
                    $father_id = $stmt->insert_id;
                    continue;
                };  
                    $query = "INSERT INTO budget_line (description, parent, bltype,notes,id_config_buget)"
                                            . "values(  ?,          ?,      ?,      '',      ?           )";
                    $stmt = $this->db->getConnection()->prepare($query);
                    $stmt->bind_param('sisi', utf8_decode($parent_childs[$i][$j]), $father_id, $father_element,$id_config_budget);
                    $stmt->execute();
                    //$father_id = $stmt->insert_id;
                    //echo 'Child: '.$parent_childs[$i][$j];
            }
        }
            
        
        
        $this->db->closeConnection();
        return true;
    }
    public function getSelectedParentID($id_config_budget, $parent){
        $query = "select id from budget_line where id_config_buget=? and bltype=?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('is', $id_config_budget, $parent);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_assoc();
        return (int)$result['id'];
    }
    public function getSelectedID($id_config_budget, $parent, $child){
        $parent_id = $this->getSelectedParentID($id_config_budget, $parent);
        $query = "select id from budget_line where id_config_buget=? and parent=? and description=?";
        $stmt = $this->db->getConnection()->prepare($query);
//        echo $parent_id;
//        exit();
        $stmt->bind_param('iis', $id_config_budget, $parent_id, $child);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_assoc();
        return (int)$result['id'];
    }

    public function insertNewProfile($gender, $age, $education, $elements, $municipality, $notes){
        $this->db->checkConnection();
        $query = "select config.municipality, config.id, municipalities.name "
                . "from config INNER JOIN municipalities on "
                . "config.municipality = municipalities.id where "
                . "status=1 and municipalities.name=?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('s',$municipality);
        $stmt->execute();
        $result = $stmt->get_result();
        $result = $result->fetch_assoc();
        $id_config_budget = $result['id'];
        
        if($age=="18-25"){$age=1;}  if($age=="36-45"){$age=3;}
        if($age=="26-35"){$age=2;}  if($age=="46-55"){$age=4;}
        if($age=="56+"){$age=5;}
        if(strtolower($gender)=="mashkull"){$gender=1;} else {$gender=2;}
        $query = "insert into profiles(age, education ,gender, id_config_buget, notes) VALUES(?,?,?,?,?)";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('isiis', $age, $education, $gender, $id_config_budget, $notes);
        $stmt->execute();
        $profile_id = $stmt->insert_id;
        $myData = json_decode(json_encode($elements), true);
        $parents = array_keys($myData);
        
        
        $selected_child_id = -1;
        
        for($i=0;$i<count($parents);$i++){
            for($j=0;$j<count($myData[$parents[$i]]);$j++){
                // PARENT ELEMENT: $parents[$i];
                //CHILD ELEMENT $myData[$parents[$i]][$j];
                $selected_child_id = $this->getSelectedID($id_config_budget, utf8_decode($parents[$i]), utf8_decode($myData[$parents[$i]][$j]));
//                echo $selected_child_id;
//                exit();
                $query = "insert into results(id_selection, id_profile ,id_config_budget) VALUES(?,?,?)";
                $stmt = $this->db->getConnection()->prepare($query);
                $stmt->bind_param('iii', $selected_child_id, $profile_id, $id_config_budget);
                $stmt->execute();
            }
        }
        return true;
//        foreach ( $elements['streets'] as $coords => $street )
//        {   
//          echo $coords;
//        }
        
    }
    public function getResults($municipality) {
        $this->db->checkConnection();
        $query = "select id from municipalities where name=?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('s',$municipality);
        $stmt->execute();
        $results = $stmt->get_result();
        $results = $results->fetch_assoc();
        $results = $results['id'];
        
        $query = "select budget_line.id, budget_line.description, budget_line.parent, budget_line.bltype 
            from ((budget_line 
            INNER JOIN config on config.id = budget_line.id_config_buget)
            INNER JOIN municipalities on config.municipality = municipalities.id)
            WHERE config.status=1 and municipalities.name=? and config.budget_year=?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('s',$municipality);
        $stmt->execute();
        $results = $stmt->get_result();
        $this->db->closeConnection();
        return $results;
    }
    public function getoneResults($user_id) {
        $this->db->checkConnection();
        $query = "SELECT municipalities.name
             from municipalities 
             inner join users 
             on municipalities.id = users.id_municipality
             where users.id=?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('i',$user_id);
        $stmt->execute();
        $results = $stmt->get_result();
        $results = $results->fetch_assoc();
        $municipality_name = $results['name'];

        $this->db->checkConnection();
        $query = "select id from municipalities where name=?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('s',$municipality_name);
        $stmt->execute();
        $results = $stmt->get_result();
        $results = $results->fetch_assoc();
        $results = $results['id'];
        
        $query = "select budget_line.id, budget_line.description, budget_line.parent, budget_line.bltype 
            from ((budget_line 
            INNER JOIN config on config.id = budget_line.id_config_buget)
            INNER JOIN municipalities on config.municipality = municipalities.id)
            WHERE config.status=1 and municipalities.name=? and config.budget_year=?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('s',$municipality);
        $stmt->execute();
        $results = $stmt->get_result();
        $this->db->closeConnection();
        return $results;
    }
    
    public function getAllResults($municipality, $year) {
        $this->db->checkConnection();
        $query = "select config.id from municipalities INNER JOIN config on municipalities.id = config.municipality WHERE municipalities.name=? and config.status=1";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('s',$municipality);
        $stmt->execute();
        $results = $stmt->get_result();
        $results = $results->fetch_assoc();
        $id_config_results = $results['id'];
        $date = $year . "-01-01";
        $query1 = "SELECT bl.bltype, bl.description, p.age, p.education, p.gender, 
        r.id_profile, m.name, p.notes
        FROM budget_line AS bl 
         JOIN results AS r ON r.id_selection = bl.id
       LEFT JOIN budget_line AS br ON br.parent = bl.id AND br.id=r.id_selection
        JOIN profiles AS p ON p.id = r.id_profile 
        JOIN config AS c ON c.id = bl.id_config_buget
        JOIN municipalities AS m ON m.id = c.municipality 
        WHERE bl.id_config_buget=? 
        AND c.budget_year=?";
        $stmt1 = $this->db->getConnection()->prepare($query1);
        $stmt1->bind_param('is', $id_config_results, $date);
        $stmt1->execute();
        $results1 = $stmt1->get_result();
        $this->db->closeConnection();
        return $results1;
    }
    public function getoneMunipAllResults($user_id, $year) {

        $this->db->checkConnection();
        $query = "SELECT municipalities.name
             from municipalities 
             inner join users 
             on municipalities.id = users.id_municipality
             where users.id=?";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('i',$user_id);
        $stmt->execute();
        $results = $stmt->get_result();
        $results = $results->fetch_assoc();
        $municipality_name = $results['name'];

        $this->db->checkConnection();
        $query = "select config.id from municipalities 
        INNER JOIN config on municipalities.id = config.municipality 
        WHERE municipalities.name=? and config.status=1";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bind_param('s',$municipality_name);
        $stmt->execute();
        $results = $stmt->get_result();
        $results = $results->fetch_assoc();
        $id_config_results = $results['id'];



        // $this->db->checkConnection();
        // $query= "set @nm =(SELECT municipalities.name
        //     from municipalities 
        //     inner join users 
        //     on municipalities.id = users.id_municipality
        //     where users.id=?);
        //     select config.id from municipalities 
        //     INNER JOIN config on municipalities.id = config.municipality 
        //     WHERE municipalities.name=@nm and config.status=1";
        // $stmt = $this->db->getConnection()->prepare($query);
        // $stmt->bind_param('s',$user_id);
        // $stmt->execute();
        // $results = $stmt->get_result();
        // $results = $results->fetch_assoc();
        // $id_config_results = $results['id'];
        $date = $year . "-01-01";
        $query1 = "SELECT bl.bltype, bl.description, p.age, p.education, p.gender, 
        r.id_profile, m.name, p.notes
        FROM budget_line AS bl 
         JOIN results AS r ON r.id_selection = bl.id
       LEFT JOIN budget_line AS br ON br.parent = bl.id AND br.id=r.id_selection
        JOIN profiles AS p ON p.id = r.id_profile 
        JOIN config AS c ON c.id = bl.id_config_buget
        JOIN municipalities AS m ON m.id = c.municipality 
        WHERE bl.id_config_buget=? 
        AND c.budget_year=?";
        $stmt1 = $this->db->getConnection()->prepare($query1);
        $stmt1->bind_param('is', $id_config_results, $date);
        $stmt1->execute();
        $results1 = $stmt1->get_result();
        $this->db->closeConnection();
        return $results1;
    }
    
    
    
    
}
