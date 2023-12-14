<?php


class DbHandler {

    private $conn;

    function __construct() {
        require_once 'dbConnect.php';
        // opening db connection
        $db = new dbConnect();

        $this->conn = $db->connect();
        
        mysqli_set_charset( $this->conn, 'utf8');
        mysqli_query($this->conn, "SET SESSION sql_mode = 'ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
        
    }
    
    /**
     * Fetching single record
     */
    public function getOneRecord($query) {
        $r = $this->conn->query($query.' LIMIT 1') or die($this->conn->error.__LINE__);
        return $result = $r->fetch_assoc();    
    }
    
    /**
     * Fetching multiple records
     */
    
    public function getAllRecords($query) {
        $data =  array();
        // error_log($query, 3, "logfile5.log");
        $stmt = $this->conn->query($query) ;
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $data[] = $row;
            }
            $stmt->close();
        }
        return $data;
    }
    
    public function getRows($query)
    {
        // error_log($query, 3, "logfile1.log");
        return $this->conn->query($query);
    }
    
    /**
     * Creating new record
     */
    public function insertIntoTable($obj, $column_names, $table_name, $multiple) {
        $c = (array) $obj;
        $keys = array_keys($c);
        $columns = '';
        $values = '';
        foreach($column_names as $desired_key){ 
            
           if(!in_array($desired_key, $keys)) {
                $$desired_key = '';
            }else{
                 if (in_array($desired_key, $multiple))
                 {
                    $$desired_key = implode(",",$c[$desired_key]);
                 }
                 else
                 {
                    $$desired_key = $c[$desired_key];
                 }
            }
            // $columns = $columns.$desired_key.',';
            $columns = $columns."`".$desired_key.'`,';
            $values = $values."'".$$desired_key."',";
        }
        $query = "INSERT INTO ".$table_name."(".trim($columns,',').") VALUES(".trim($values,',').")";
        // error_log("pks869", 3, "logfile1.log");
        // error_log($query, 3, "logfile1.log");
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        // error_log("pks", 3, "logfile1.log");

        if ($r) {
            $new_row_id = $this->conn->insert_id;
            return $new_row_id;
            } else {
            return NULL;
        }
    }
    
    /**
     * Creating new record with QUERY
    */
    public function insertByValues($values,  $columns, $table_name) {
        
        $query = "INSERT INTO ".$table_name."(".trim( $columns,',').") VALUES(".trim($values,',').")";
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        if ($r) {
            $new_row_id = $this->conn->insert_id;
            return $new_row_id;
            } else {
            return NULL;
        }
    }
 
    /**
     * Creating new record with BYQUERY
    */
    public function insertByQuery($query) {
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        if ($r) {
            $new_row_id = $this->conn->insert_id;
            return $new_row_id;
            } else {
            return NULL;
        }
    }
    
    /**
     * Creating new record with BYQUERY
    */
    public function updateByQuery($query) {
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        if ($r) {
            return "Done";
            } else {
            return NULL;
        }
    }
    
    
    /**
     * Update record
     */
    public function updateIntoTable($obj, $column_names, $table_name, $condition) {
        
        $c = (array) $obj;
        $keys = array_keys($c);
        $columns = '';
        foreach($column_names as $desired_key){ // Check the obj received. If blank insert blank into the array.
           if(!in_array($desired_key, $keys)) {
                $$desired_key = '';
            }else{
                $$desired_key = $c[$desired_key];
            }
            $columns = $columns."`".$desired_key."`='".$$desired_key."',";
        }
        $query = "UPDATE ".$table_name." SET ".trim($columns,',')." where ".$condition;
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        if ($r) {
            return "yes";
        } else {
            return NULL;
        }
    }
    
    /**
     * Update record
     */
    public function NupdateIntoTable($obj, $column_names, $table_name, $condition, $multiple) {
        
        $c = (array) $obj;
        $keys = array_keys($c);
        $columns = '';
        foreach($column_names as $desired_key){ 
            
           if(!in_array($desired_key, $keys)) {
                $$desired_key = '';
            }else{
                if (in_array($desired_key, $multiple))
                 {
                    if ($c[$desired_key])
                    {
                        if (is_array($c[$desired_key]))
                        {
                            $count = count($c[$desired_key]);
                            if ($count > 1)
                            {
                                $$desired_key = implode(",",$c[$desired_key]);
                            }
                            else
                            {
                                $$desired_key = implode("",$c[$desired_key]);
                            }
                        }
                        else
                        {
                            $$desired_key = $c[$desired_key];
                        }
                        
                    }
                    else
                    {
                        $$desired_key = $c[$desired_key];
                    }
                 }
                 else
                 {
                    $$desired_key = $c[$desired_key];
                 }
            }
            $columns = $columns."`".$desired_key."`='".$$desired_key."',";
        }
        $query = "UPDATE ".$table_name." SET ".trim($columns,',')." where ".$condition;
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        if ($r) {
            return "yes";
        } else {
            return NULL;
        }
    }


    
    /**
     * DELETE record
     */
    public function deleteIntoTable($obj, $column_names, $table_name, $condition) {
        
        $query = "DELETE FROM ".$table_name."  where ".$condition;
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        return $query;
    }
    public function historydata($obj, $column_names, $tabble_name, $condition, $module_id,$multiple,$modified_by, $modified_date)
    {
        //$olddata = $this->conn->query("SELECT * FROM $tabble_name where $condition") or die($this->conn->error.__LINE__); 
        $olddata =  array();
        $stmt = $this->conn->query("SELECT * FROM $tabble_name where $condition") or die($this->conn->error.__LINE__);
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $olddata[] = $row;
            }
            $stmt->close();
        }
        //var_dump($data);
        //exit(0);
        $c = (array) $obj;
        $keys = array_keys($c);
        $columns = '';
        $values = '';
        foreach($column_names as $desired_key)
        { 
            $old_value = $olddata[0][$desired_key];
            if (in_array($desired_key, $multiple))
            {
               if ($c[$desired_key])
               {
                   if (is_array($c[$desired_key]))
                   {
                       $count = count($c[$desired_key]);
                       if ($count > 1)
                       {
                            $new_value = implode(",",$c[$desired_key]);
                       }
                       else
                       {
                            $new_value = implode("",$c[$desired_key]);
                       }
                   }
                   else
                   {
                        $new_value = $c[$desired_key];
                   }
               }
            }
            else{
                $new_value = $c[$desired_key];
            }
            
            if ($old_value==$new_value)
            {

            }
            else{
                if ($desired_key=='modified_by' || $desired_key=='modified_date')
                {

                }
                else{
                    $query = "INSERT INTO history (module_name, module_id, field_name, old_value, new_value, modified_by, modified_date) VALUES('$tabble_name', '$module_id', '$desired_key', '$old_value', '$new_value', '$modified_by', '$modified_date' )";
                    $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
                }
            }
        }
        return 'DONE';
    }

    
    public function getSession(){
        if (!isset($_SESSION)) {
            session_start();
        }
        $sess = array();
        if(isset($_SESSION['user_id']))
        {
            $sess["user_id"] = $_SESSION['user_id'];
            $sess["username"] = $_SESSION['username'];
            $sess["emp_name"] = $_SESSION['emp_name'];
            $sess['emp_image'] = $_SESSION['emp_image'];
            $sess["email_id"] = $_SESSION['email_id'];
            $sess["bo_id"] = $_SESSION['bo_id'];
            $sess["bo_name"] = $_SESSION['bo_name'];
            if (!$_SESSION['teams'])
            {
                $_SESSION['teams'] = "";
            }
            $sess["teams"] = $_SESSION['teams'];
            if (!$_SESSION['sub_teams'])
            {
                $_SESSION['sub_teams'] = "";
            }
            $sess["sub_teams"] = $_SESSION['sub_teams'];
            $sess["role"] = $_SESSION['role'];
            $sess["permissions"] = $_SESSION['permissions'];
        }
        else
        {
            $sess["user_id"] = '';
            $sess["username"] = 'Guest';
            $sess["emp_name"] = 'Guest';
            $sess['emp_image'] = null;
            $sess["email_id"] = 'Guest';
            $sess["bo_id"] = '';
            $sess["bo_name"] = 'Guest';
            
            $sess["teams"] = 'Guest';
            $sess["sub_teams"] = 'Guest';
            $sess["role"] = 'Guest';
            $sess["permissions"] = 'Guest';
            $db = new DbHandler();
            //$scehduled_task = $db->DoScheduledTask();
            //$invscehduled_task = $db->DoInvScheduledTask();
        }
        return $sess;
    }
    public function destroySession(){
        if (!isset($_SESSION)) {
        session_start();
        }
        if(isSet($_SESSION['user_id']))
        {
            unset($_SESSION['user_id']);
            unset($_SESSION['username']);
            unset($_SESSION['emp_name']);
            unset($_SESSION['emp_image']);

            unset($_SESSION['email_id']);
            unset($_SESSION['bo_id']);
            unset($_SESSION['bo_name']);
            if (!$_SESSION['teams'])
            {
                $_SESSION['teams'] = "";
            }
            unset($_SESSION['teams']);
            if (!$_SESSION['sub_teams'])
            {
                $_SESSION['sub_teams'] = "";
            }
            unset($_SESSION['sub_teams']);
            unset($_SESSION['role']);
            unset($_SESSION['permissions']);
            $info='info';
            if(isSet($_COOKIE[$info]))
            {
                setcookie ($info, '', time() - $cookie_time);
            }
            $msg="Logged Out Successfully...";
        }
        else
        {
            $msg = "Not logged in...";
        }
        return $msg;
    }

    public function ConvertAmount($amount,$para) {
        if ($para=='Abs')
        {
            return $amount;
        }
    
        if ($para=='Th')
        {
            return $amount*1000;
        }
    
        if ($para=='Lac')
        {
            return $amount*100000;
        }
        if ($para=='Cr')
        {
            return $amount*10000000;
        }
    }

    public function ShowAmount($amount,$para) {
        if ($para=='Abs')
        {
            return $amount;
        }
    
        if ($para=='Th')
        {
            return $amount/1000;
        }
    
        if ($para=='Lac')
        {
            return $amount/100000;
        }
        if ($para=='Cr')
        {
            return $amount/10000000;
        }
    }

}


?>
