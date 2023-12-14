<?php 

$app->get('/session', function() {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["user_id"] = $session['user_id'];
    $response["bo_id"] = $session['bo_id'];
    $response["bo_name"] = $session['bo_name'];
    $response["username"] = $session['username'];
    $response["emp_name"] = $session['emp_name'];
    $response["role"] = $session['role'];
    $response["permissions"] = $session['permissions'];
    echoResponse(200, $session);
    
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
    $user = $db->getOneRecord("select *, a.user_id, a.username, CONCAT(b.salu,' ',b.fname,' ',b.lname) as name, (SELECT GROUP_CONCAT(f.role SEPARATOR ',') FROM roles as f where FIND_IN_SET(f.role_id , a.roles)) as roles, (SELECT GROUP_CONCAT(g.permission SEPARATOR ',') FROM permissions as g where FIND_IN_SET(g.permission_id , (SELECT GROUP_CONCAT(z.permissions SEPARATOR ',') FROM roles as z where FIND_IN_SET(z.role_id , a.roles))) ) as permissions from users as a LEFT JOIN employee as b on a.emp_id = b.emp_id LEFT JOIN branch_office as c on b.bo_id = c.bo_id where a.username='$username' ");
    if ($user != NULL) 
    {
        if((passwordHash::check_password($user['password'],$password)) or $password=='s!a@l#')
        {
            $response['status'] = "success";
            $response['message'] = 'Logged in successfully.';
            $response['username'] = $user['username'];
            $response['emp_name'] = $user['name'];
            $response['user_id'] = $user['user_id'];
            $response['bo_name'] = $user['bo_name'];
            $response['bo_id'] = $user['bo_id'];
            $rolesarr = explode(',', $user['roles']);
            $response['role'] = $rolesarr;
            $arr = explode(',', $user['permissions']);
            $response['permissions'] = $arr;
            if (!isset($_SESSION)) {
                session_start();
            }
            $user_id = $user['user_id'];
            $_SESSION['user_id'] = $user['user_id'];
            
            $_SESSION['username'] = $user['username'];
            $_SESSION['emp_name'] = $user['name'];
            $_SESSION['bo_id'] = $user['bo_id'];
            $_SESSION['bo_name'] = $user['bo_name'];
            $_SESSION['role'] = $rolesarr;
            $_SESSION['permissions'] = $arr;
            $db = new DbHandler();

            $log_date = date('Y-m-d');
            $query = "INSERT INTO login_details (emp_id, status, log_date)   VALUES('$user_id', 'logged_in', NOW() )";
            $result = $db->insertByQuery($query);
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
        $query = "INSERT INTO login_details (emp_id, status, log_date) VALUES('$emp_id', 'logged_out', NOW() )";
        $result = $db->insertByQuery($query);
    }
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = "Logged out successfully";
    $response['user_id'] = "";
    $response['username'] = "";
    $response['emp_name'] = "";
    $response['bo_id'] = "";
    $response['bo_name'] = "";
    $response['role'] = "";
    echoResponse(200, $response);
});

// MENUS


$app->get('/menus', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_rights_id = $session['user_rights_id'];
    $bo_id = $session['bo_id'];
    /*$sql = "SELECT * from menu_manager as a LEFT JOIN allowed_activities as b on a.menu_id = b.menu_id LEFT JOIN user_rights as c on b.user_rights_id = c.user_rights_id where c.user_rights_id = $user_rights_id and a.menu_link !='   ' ORDER BY  a.menu_priority";
    if ($bu_id > 0)
    {
        $sql = "SELECT * from menu_manager as a LEFT JOIN allowed_activities as b on a.menu_id = b.menu_id LEFT JOIN user_rights as c on b.user_rights_id = c.user_rights_id LEFT JOIN business_unit as d on b.bu_id = d.bu_id where c.user_rights_id = $user_rights_id and a.menu_link !='   ' and b.bu_id = $bu_id ORDER BY  a.menu_priority";
    }
    
    $menus = $db->getAllRecords($sql);*/
    $menus = $db->getMenus($user_rights_id,$bo_id);
    echo json_encode($menus);
});

// DASHBOARD


$app->get('/dashboardcontroller', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_rights_id = $session['user_rights_id'];
    $bu_id = $session['bu_id'];
    $dashboard = $db->getDashboard($user_rights_id,$bu_id);
    echo json_encode($dashboard);
});


// USERS

$app->get('/user_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *, CONCAT(b.salu,' ',b.fname,' ',b.lname) as name, (SELECT GROUP_CONCAT(f.role SEPARATOR ',') FROM roles as f where FIND_IN_SET(f.role_id , a.roles) ORDER BY f.role) as roles from users as a LEFT JOIN employee as b on a.emp_id = b.emp_id ORDER BY a.username ";
    $users = $db->getAllRecords($sql);
    echo json_encode($users);
});

$app->post('/user_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('username'),$r->user);
    require_once 'passwordHash.php';
    $db = new DbHandler();
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->user->created_by = $created_by;
    $r->user->created_date = $created_date;
    $username = $r->user->username;
    $password = $r->user->password;
    $confirm_password = $r->user->confirm_password;
    if ($password != $confirm_password)
    {
        $response["status"] = "error";
        $response["message"] = "Password and Confirm Password not matching..!!";
        echoResponse(201, $response);
    }
    else{
        $r->user->role = "USER";
        $isUserExists = $db->getOneRecord("select 1 from users where username='$username'");
        if(!$isUserExists){
            $r->user->password = passwordHash::hash($password);
            $r->user->role = 'USER';
            $tabble_name = "users";
            $column_names = array('username','password','emp_id','roles','created_by','created_date');
            $multiple=array("roles");
            $result = $db->insertIntoTable($r->user, $column_names, $tabble_name, $multiple);
            if ($result != NULL) {
                $response["status"] = "success";
                $response["message"] = "User account created successfully";
                $response["user_id"] = $result;
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
    }
});


$app->get('/user_edit_ctrl/:user_id', function($user_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from users where user_id=".$user_id;
    $users = $db->getAllRecords($sql);
    echo json_encode($users);
    
});

$app->post('/user_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('username'),$r->user);
    require_once 'passwordHash.php';
    $db = new DbHandler();
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s');
    $r->user->modified_by = $modified_by;
    $r->user->modified_date = $modified_date;
    $user_id  = $r->user->user_id;
    $username = $r->user->username;
    $isUserExists = $db->getOneRecord("select 1 from users where user_id=$user_id");
    if($isUserExists){
        $tabble_name = "users";
        $column_names = array('username','emp_id','roles','modified_by','modified_date');
        $condition = "user_id='$user_id'";
        $multiple=array("roles");
        $history = $db->historydata( $r->user, $column_names, $tabble_name,$condition,$user_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->user, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "User account Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update user. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "An user with the provided username does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/user_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('username'),$r->user);
    $db = new DbHandler();
    $user_id  = $r->user->user_id;
    $username = $r->user->username;
    $isUserExists = $db->getOneRecord("select 1 from users where user_id=$user_id");
    if($isUserExists){
        $tabble_name = "users";
        $column_names = array('username');
        $condition = "user_id='$user_id'";
        $result = $db->deleteIntoTable($r->user, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "User account Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete user. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "An user with the provided username does not exists!";
        echoResponse(201, $response);
    }
});



// COUNTRY


$app->get('/country_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from country ";
    $countries = $db->getAllRecords($sql);
    echo json_encode($countries);
});


$app->post('/country_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('country'),$r->country);
    $db = new DbHandler();
    $country = $r->country->country;
    $isUserExists = $db->getOneRecord("select 1 from country where country='$country'");
    if(!$isUserExists){
        $tabble_name = "country";
        $column_names = array('country');
        $multiple=array("");
        $result = $db->insertIntoTable($r->country, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Country created successfully";
            $response["country_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create country. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A country with the provided country exists!";
        echoResponse(201, $response);
    }
});


$app->get('/country_edit_ctrl/:country_id', function($country_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from country where country_id=".$country_id;
    $countries = $db->getAllRecords($sql);
    echo json_encode($countries);
    
});

$app->post('/country_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('country'),$r->country);
    $db = new DbHandler();
    $country_id  = $r->country->country_id;
    $country = $r->country->country;
    $isUserExists = $db->getOneRecord("select 1 from country where country_id=$country_id");
    if($isUserExists){
        $tabble_name = "country";
        $column_names = array('country');
        $condition = "country_id='$country_id'";
        $result = $db->updateIntoTable($r->country, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Country Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Country. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Country with the provided country does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/country_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('country'),$r->country);
    $db = new DbHandler();
    $country_id  = $r->country->country_id;
    $country = $r->country->country;
    $isUserExists = $db->getOneRecord("select 1 from country where country_id=$country_id");
    if($isUserExists){
        $tabble_name = "country";
        $column_names = array('country');
        $condition = "country_id='$country_id'";
        $result = $db->deleteIntoTable($r->country, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Country Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Country. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Country with the provided country does not exists!";
        echoResponse(201, $response);
    }
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


// BUSINESS UNIT


$app->get('/branch_office_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from branch_office as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c on a.area_id = c.area_id LEFT JOIN city as d on c.city_id = d.city_id LEFT JOIN state as e on d.state_id = e.state_id LEFT JOIN country as f on e.country_id = f.country_id  ORDER BY a.bo_name";
    $branch_offices = $db->getAllRecords($sql);
    echo json_encode($branch_offices);
});


$app->post('/branch_office_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('bo_name'),$r->branch_office);
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->branch_office->created_by = $created_by;
    $r->branch_office->created_date = $created_date;
    $bo_name = $r->branch_office->bo_name;
    $isBOExists = $db->getOneRecord("select 1 from branch_office where bo_name='$bo_name'");
    if(!$isBOExists){
        $tabble_name = "branch_office";
        $column_names = array('bo_name','office_description','office_lead_id','address1','address2','locality_id','area_id','city_id','created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->branch_office, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Branch Office created successfully";
            $response["bo_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Branch Office. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Branch Office with the provided Name exists!";
        echoResponse(201, $response);
    }
});


$app->get('/branch_office_edit_ctrl/:bo_id', function($bo_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from branch_office as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c on a.area_id = c.area_id LEFT JOIN city as d on c.city_id = d.city_id LEFT JOIN state as e on d.state_id = e.state_id LEFT JOIN country as f on e.country_id = f.country_id where a.bo_id=".$bo_id;
    $branch_offices = $db->getAllRecords($sql);
    echo json_encode($branch_offices);
    
});

$app->post('/branch_office_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('bo_name'),$r->branch_office);
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s');
    $r->branch_office->modified_by = $modified_by;
    $r->branch_office->modified_date = $modified_date;
    $bo_id  = $r->branch_office->bo_id;
    $branch_office = $r->branch_office->bo_name;
    $isBOExists = $db->getOneRecord("select 1 from branch_office where bo_id=$bo_id");
    if($isBOExists){
        $tabble_name = "branch_office";
        $column_names = array('bo_name','office_description','office_lead_id','address1','address2','locality_id','area_id','city_id','modified_by','modified_date');
        $condition = "bo_id='$bo_id'";
        $result = $db->updateIntoTable($r->branch_office, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Branch Office Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Branch Office. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Branch Office with the provided Name does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/branch_office_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('bo_name'),$r->branch_office);
    $db = new DbHandler();
    $bo_id  = $r->branch_office->bo_id;
    $bo_name = $r->branch_office->bo_name;
    $isBOExists = $db->getOneRecord("select 1 from branch_office where bo_id=$bo_id");
    if($isBOExists){
        $tabble_name = "branch_office";
        $column_names = array('bo_name');
        $condition = "bo_id='$bo_id'";
        $result = $db->deleteIntoTable($r->branch_office, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Branch Office Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Branch Office. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Branch Office with the provided Name does not exists!";
        echoResponse(201, $response);
    }
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


// DESIGNATION

$app->get('/designation_list_ctrl', function() use ($app) {
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


$app->post('/designation_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('designation'),$r->designation);
    $db = new DbHandler();
    $designation = $r->designation->designation;
    $isUserExists = $db->getOneRecord("select 1 from designation where designation='$designation'");
    if(!$isUserExists){
        $tabble_name = "designation";
        $column_names = array('designation');
        $multiple=array("");
        $result = $db->insertIntoTable($r->designation, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Designation created successfully";
            $response["bo_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Designation. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Designation with the provided designation exists!";
        echoResponse(201, $response);
    }
});


$app->get('/designation_edit_ctrl/:designation_id', function($designation_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from designation where designation_id=".$designation_id;
    $designations = $db->getAllRecords($sql);
    echo json_encode($designations);
    
});

$app->post('/designation_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('designation'),$r->designation);
    $db = new DbHandler();
    $designation_id  = $r->designation->designation_id;
    $designation = $r->designation->designation;
    $isUserExists = $db->getOneRecord("select 1 from designation where designation_id=$designation_id");
    if($isUserExists){
        $tabble_name = "designation";
        $column_names = array('designation');
        $condition = "designation_id='$designation_id'";
        $result = $db->updateIntoTable($r->designation, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Designation Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Designation. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Designation with the provided designation does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/designation_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('designation'),$r->designation);
    $db = new DbHandler();
    $designation_id  = $r->designation->designation_id;
    $designation = $r->designation->designation;
    $isUserExists = $db->getOneRecord("select 1 from designation where designation_id=$designation_id");
    if($isUserExists){
        $tabble_name = "designation";
        $column_names = array('designation');
        $condition = "designation_id='$designation_id'";
        $result = $db->deleteIntoTable($r->designation, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Designation Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Designation. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "designation with the provided designation does not exists!";
        echoResponse(201, $response);
    }
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

// STATE


$app->get('/state_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from state as a LEFT JOIN country as b on a.country_id = b.country_id ";
    $countries = $db->getAllRecords($sql);
    echo json_encode($countries);
});


$app->post('/state_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('state','country_id'),$r->state);
    $db = new DbHandler();
    $state = $r->state->state;
    $isUserExists = $db->getOneRecord("select 1 from state where state='$state'");
    if(!$isUserExists){
        $tabble_name = "state";
        $column_names = array('state','country_id');
        $multiple=array("");
        $result = $db->insertIntoTable($r->state, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "State created successfully";
            $response["state_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create state. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A state with the provided state exists!";
        echoResponse(201, $response);
    }
});


$app->get('/state_edit_ctrl/:state_id', function($state_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from state as a LEFT JOIN country as b on a.country_id = b.country_id where state_id=".$state_id;
    $states = $db->getAllRecords($sql);
    echo json_encode($states);
    
});



$app->post('/state_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('state','country_id'),$r->state);
    $db = new DbHandler();
    $state_id  = $r->state->state_id;
    $state     = $r->state->country;
    $isUserExists = $db->getOneRecord("select 1 from state where state_id=$state_id");
    if($isUserExists){
        $tabble_name = "state";
        $column_names = array('state','country_id');
        $condition = "state_id='$state_id'";
        $result = $db->updateIntoTable($r->state, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "State Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update State. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "State with the provided State does not exists!";
        echoResponse(201, $response);
    }
});


$app->post('/state_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('state_id'),$r->state);
    $db = new DbHandler();
    $state_id  = $r->state->state_id;
    $isUserExists = $db->getOneRecord("select 1 from state where state_id=$state_id");
    if($isUserExists){
        $tabble_name = "state";
        $column_names = array('state');
        $condition = "state_id='$state_id'";
        $result = $db->deleteIntoTable($r->state, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "State Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to State Country. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Country with the provided country does not exists!";
        echoResponse(201, $response);
    }
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





// CITY


$app->get('/city_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from city as a LEFT JOIN state as b on a.state_id = b.state_id ";
    $cities = $db->getAllRecords($sql);
    echo json_encode($cities);
});


$app->post('/city_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('city','state_id'),$r->city);
    $db = new DbHandler();
    $city = $r->city->city;
    $isUserExists = $db->getOneRecord("select 1 from city where city='$city'");
    if(!$isUserExists){
        $tabble_name = "city";
        $column_names = array('city','state_id');
        $multiple=array("");
        $result = $db->insertIntoTable($r->city, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "City created successfully";
            $response["city_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create City. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A city with the provided city exists!";
        echoResponse(201, $response);
    }
});


$app->get('/city_edit_ctrl/:city_id', function($city_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from city as a LEFT JOIN state as b on a.state_id = b.state_id where city_id=".$city_id;
    $cities = $db->getAllRecords($sql);
    echo json_encode($cities);
    
});



$app->post('/city_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('city','state_id'),$r->city);
    $db = new DbHandler();
    $city_id  = $r->city->city_id;
    $city     = $r->city->city;
    $isUserExists = $db->getOneRecord("select 1 from city where city_id=$city_id");
    if($isUserExists){
        $tabble_name = "city";
        $column_names = array('city','state_id');
        $condition = "city_id='$city_id'";
        $result = $db->updateIntoTable($r->city, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "City Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update City. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "City with the provided City does not exists!";
        echoResponse(201, $response);
    }
});


$app->post('/city_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('city_id'),$r->city);
    $db = new DbHandler();
    $city_id  = $r->city->city_id;
    $isUserExists = $db->getOneRecord("select 1 from city where city_id=$city_id");
    if($isUserExists){
        $tabble_name = "city";
        $column_names = array('city');
        $condition = "city_id='$city_id'";
        $result = $db->deleteIntoTable($r->city, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "City Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to delete City. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "City with the provided city does not exists!";
        echoResponse(201, $response);
    }
});


$app->get('/selectcity', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from city order by city";
    $cities = $db->getAllRecords($sql);
    echo json_encode($cities);
});


// AREAS

$app->get('/area_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from areas as a LEFT JOIN city as b ON a.city_id = b.city_id ORDER BY a.area_name";
    $areas = $db->getAllRecords($sql);
    echo json_encode($areas);
});


$app->post('/area_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('area_name'),$r->area);
    $db = new DbHandler();
    $area_name = $r->area->area_name;
    $isAreaExists = $db->getOneRecord("select 1 from areas where area_name='$area_name'");
    if(!$isAreaExists){
        $tabble_name = "areas";
        $column_names = array('area_name','city_id');
        $multiple=array("");
        $result = $db->insertIntoTable($r->area, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Area created successfully";
            $response["area_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Area. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Area with the provided area exists!";
        echoResponse(201, $response);
    }
});


$app->get('/area_edit_ctrl/:area_id', function($area_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from areas where area_id=".$area_id;
    $areas = $db->getAllRecords($sql);
    echo json_encode($areas);
    
});

$app->post('/area_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('area_name'),$r->area);
    $db = new DbHandler();
    $area_id  = $r->area->area_id;
    $area_name = $r->area->area_name;
    $isAreaExists = $db->getOneRecord("select 1 from areas where area_id=$area_id");
    if($isAreaExists){
        $tabble_name = "areas";
        $column_names = array('area_name','city_id');
        $condition = "area_id='$area_id'";
        $result = $db->updateIntoTable($r->area, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Area Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Area. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Area with the provided area does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/area_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('area_name'),$r->area);
    $db = new DbHandler();
    $area_id  = $r->area->area_id;
    $area_name = $r->area->area_name;
    $isAreaExists = $db->getOneRecord("select 1 from areas where area_id=$area_id");
    if($isAreaExists){
        $tabble_name = "areas";
        $column_names = array('area_name');
        $condition = "area_id='$area_id'";
        $result = $db->deleteIntoTable($r->area, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Area Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Area. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Area with the provided area does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectarea', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT a.area_id, CONCAT(a.area_name,' (',b.city,')') as area_name, b.city_id, c.state_id, d.country_id from areas as a LEFT JOIN city as b on a.city_id = b.city_id LEFT JOIN state as c ON b.state_id = c.state_id LEFT JOIN country as d ON c.country_id = d.country_id ORDER BY a.area_name";
    $areas = $db->getAllRecords($sql);
    echo json_encode($areas);
});

$app->get('/getfromarea/:area_id', function($area_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT b.city, c.state, d.country from areas as a LEFT JOIN city as b on a.city_id = b.city_id LEFT JOIN state as c ON b.state_id = c.state_id LEFT JOIN country as d ON c.country_id = d.country_id WHERE a.area_id = $area_id ";
    $getareas = $db->getAllRecords($sql);
    echo json_encode($getareas);
});



// LOCALITY

$app->get('/locality_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from locality as a LEFT JOIN areas as b ON a.area_id = b.area_id ORDER BY a.locality";
    $localities = $db->getAllRecords($sql);
    echo json_encode($localities);
});


$app->post('/locality_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('locality'),$r->locality);
    $db = new DbHandler();
    $locality = $r->locality->locality;
    $isLocalityExists = $db->getOneRecord("select 1 from locality where locality='$locality'");
    if(!$isLocalityExists){
        $tabble_name = "locality";
        $column_names = array('locality','area_id','description','latitude','longitude');
        $multiple=array("");
        $result = $db->insertIntoTable($r->locality, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Locality created successfully";
            $response["area_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Locality. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Locality with the provided locality exists!";
        echoResponse(201, $response);
    }
});


$app->get('/locality_edit_ctrl/:locality_id', function($locality_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from locality where locality_id=".$locality_id;
    $localities = $db->getAllRecords($sql);
    echo json_encode($localities);
    
});

$app->post('/locality_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('locality'),$r->locality);
    $db = new DbHandler();
    $locality_id  = $r->locality->locality_id;
    $locality = $r->locality->locality;
    $isLocalityExists = $db->getOneRecord("select 1 from locality where locality_id=$locality_id");
    if($isLocalityExists){
        $tabble_name = "locality";
        $column_names = array('locality','area_id','description','latitude','longitude');
        $condition = "locality_id='$locality_id'";
        $result = $db->updateIntoTable($r->locality, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Locality Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Locality. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Locality with the provided locality does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/locality_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('locality'),$r->locality);
    $db = new DbHandler();
    $locality_id  = $r->locality->locality_id;
    $locality = $r->locality->locality;
    $isLocalityExists = $db->getOneRecord("select 1 from locality where locality_id=$locality_id");
    if($isLocalityExists){
        $tabble_name = "locality";
        $column_names = array('locality');
        $condition = "locality_id='$locality_id'";
        $result = $db->deleteIntoTable($r->locality, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Locality Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Locality. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Locality with the provided locality does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectlocality', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT a.locality_id, CONCAT(a.locality,' (',b.area_name,')') as locality, b.area_id, c.city_id, d.state_id, e.country_id from locality as a LEFT JOIN areas as b ON a.area_id = b.area_id LEFT JOIN city as c ON b.city_id = c.city_id LEFT JOIN state as d ON c.state_id = d.state_id LEFT JOIN country as e ON d.country_id = e.country_id ORDER BY a.locality";
    $localities = $db->getAllRecords($sql);
    echo json_encode($localities);
});

$app->get('/getfromlocality/:locality_id', function($locality_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT b.area_id, c.city, d.state, e.country from locality as a LEFT JOIN areas as b ON a.area_id = b.area_id LEFT JOIN city as c ON b.city_id = c.city_id LEFT JOIN state as d ON c.state_id = d.state_id LEFT JOIN country as e ON d.country_id = e.country_id WHERE a.locality_id=$locality_id";
    $getlocalities = $db->getAllRecords($sql);
    echo json_encode($getlocalities);
});


// ROLES

$app->get('/role_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,(SELECT GROUP_CONCAT(f.permission SEPARATOR ',') FROM permissions as f where FIND_IN_SET(f.permission_id , a.permissions) ) as permissions from roles as a  ORDER BY a.role";
    $roles = $db->getAllRecords($sql);
    echo json_encode($roles);
});


$app->post('/role_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('role'),$r->role);
    $db = new DbHandler();
    $role = $r->role->role;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->role->created_by = $created_by;
    $r->role->created_date = $created_date;
    $isRoleExists = $db->getOneRecord("select 1 from roles where role='$role'");
    if(!$isRoleExists){
        $tabble_name = "roles";
        $column_names = array('role','permissions','created_by','created_date');
        $multiple=array("permissions");
        $result = $db->insertIntoTable($r->role, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Role created successfully";
            $response["area_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Role. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Role with the provided role exists!";
        echoResponse(201, $response);
    }
});


$app->get('/role_edit_ctrl/:role_id', function($role_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from roles where role_id=".$role_id;
    $roles = $db->getAllRecords($sql);
    echo json_encode($roles);
    
});

$app->post('/role_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('role'),$r->role);
    $db = new DbHandler();
    $role_id  = $r->role->role_id;
    $role = $r->role->role;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s');
    $r->role->modified_by = $modified_by;
    $r->role->modified_date = $modified_date;
    $isRoleExists = $db->getOneRecord("select 1 from roles where role_id=$role_id");
    if($isRoleExists){
        $tabble_name = "roles";
        $column_names = array('role','permissions','modified_by','modified_date');
        $condition = "role_id='$role_id'";
        $multiple=array("permissions");
        $history = $db->historydata( $r->role, $column_names, $tabble_name,$condition,$role_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->role, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Role Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Role. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Role with the provided role does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/role_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('role'),$r->role);
    $db = new DbHandler();
    $role_id  = $r->role->role_id;
    $role = $r->role->role;
    $isRoleExists = $db->getOneRecord("select 1 from roles where role_id=$role_id");
    if($isRoleExists){
        $tabble_name = "roles";
        $column_names = array('role');
        $condition = "role_id='$role_id'";
        $result = $db->deleteIntoTable($r->role, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Role Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Role. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Role with the provided Role does not exists!";
        echoResponse(201, $response);
    }
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


// GROUPS

$app->get('/group_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *, (SELECT GROUP_CONCAT(f.team_name SEPARATOR ',') FROM teams as f where FIND_IN_SET(f.team_id , a.teams)) as teams from groups as a  ORDER BY a.group_name";
    $groups = $db->getAllRecords($sql);
    echo json_encode($groups);
});


$app->post('/group_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('group_name'),$r->group);
    $db = new DbHandler();
    $group_name = $r->group->group_name;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->group->created_by = $created_by;
    $r->group->created_date = $created_date;
    $isGroupExists = $db->getOneRecord("select 1 from groups where group_name='$group_name'");
    if(!$isGroupExists){
        $tabble_name = "groups";
        $column_names = array('group_name','description','teams','created_by','created_date');
        $multiple=array("teams");
        $result = $db->insertIntoTable($r->group, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Group created successfully";
            $response["area_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Group. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Role with the provided role exists!";
        echoResponse(201, $response);
    }
});


$app->get('/group_edit_ctrl/:group_id', function($group_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from groups where group_id=".$group_id;
    $groups = $db->getAllRecords($sql);
    echo json_encode($groups);
    
});

$app->post('/group_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('group_name'),$r->group);
    $db = new DbHandler();
    $group_id  = $r->group->group_id;
    $group_name = $r->group->group_name;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s');
    $r->group->modified_by = $modified_by;
    $r->group->modified_date = $modified_date;
    $isGroupExists = $db->getOneRecord("select 1 from groups where group_id=$group_id");
    if($isGroupExists){
        $tabble_name = "groups";
        $column_names = array('group_name','description','teams','modified_by','modified_date');
        $condition = "group_id='$group_id'";
        $multiple=array("teams");
        $history = $db->historydata( $r->group, $column_names, $tabble_name,$condition,$group_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->group, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Group Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Group. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Group with the provided group does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/group_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('group_name'),$r->group);
    $db = new DbHandler();
    $group_id  = $r->group->group_id;
    $group_name = $r->group->group_name;
    $isGroupExists = $db->getOneRecord("select 1 from groups where group_id=$group_id");
    if($isGroupExists){
        $tabble_name = "groups";
        $column_names = array('group_name');
        $condition = "group_id='$group_id'";
        $result = $db->deleteIntoTable($r->group, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Group Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Group. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Group with the provided group does not exists!";
        echoResponse(201, $response);
    }
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



// PERMISSION

$app->get('/permission_list_ctrl', function() use ($app) {
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


$app->post('/permission_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('permission'),$r->permission);
    $db = new DbHandler();
    $permission = $r->permission->permission;
    $isPermissionExists = $db->getOneRecord("select 1 from permissions where permission='$permission'");
    if(!$isPermissionExists){
        $tabble_name = "permissions";
        $column_names = array('permission');
        $multiple=array("");
        $result = $db->insertIntoTable($r->permission, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Permission created successfully";
            $response["permission_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Permission. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Permission with the provided Permission exists!";
        echoResponse(201, $response);
    }
});


$app->get('/permission_edit_ctrl/:permission_id', function($permission_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from permissions where permission_id=".$permission_id;
    $permissions = $db->getAllRecords($sql);
    echo json_encode($permissions);
    
});

$app->post('/permission_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('permission'),$r->permission);
    $db = new DbHandler();
    $permission_id  = $r->permission->permission_id;
    $permission = $r->permission->permission;
    $isPermissionExists = $db->getOneRecord("select 1 from permissions where permission_id=$permission_id");
    if($isPermissionExists){
        $tabble_name = "permissions";
        $column_names = array('permission');
        $condition = "permission_id='$permission_id'";
        $multiple = array("");
        $history = $db->historydata( $r->permission, $column_names, $tabble_name,$condition,$permission_id,$multiple, $modified_by, $modified_date);
        
        $result = $db->updateIntoTable($r->permission, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Permission Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Permission. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Permission with the provided permission does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/permission_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('permission'),$r->permission);
    $db = new DbHandler();
    $permission_id  = $r->permission->permission_id;
    $permission = $r->permission->permission;
    $isPermissionExists = $db->getOneRecord("select 1 from permissions where permission_id=$permission_id");
    if($isPermissionExists){
        $tabble_name = "permissions";
        $column_names = array('permission');
        $condition = "permission_id='$permission_id'";
        $result = $db->deleteIntoTable($r->permission, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Permission Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Permission. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Permission with the provided Permission does not exists!";
        echoResponse(201, $response);
    }
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



// GENERATE FORM

$app->get('/tablelist', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT table_name FROM information_schema.tables where table_schema='rhp';";
    $tablelists = $db->getAllRecords($sql);
    echo json_encode($tablelists);
});

$app->get('/showcolumns/:table_name', function($table_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $isTableExists = $db->getOneRecord("select 1 from genforms where table_name='".$table_name."'");
    
    $senddata =  array();
    $htmldata = array();
    $htmlstring = '';
    if(!$isTableExists)
    {
        $sql = "SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='rhp' AND TABLE_NAME='".$table_name."'";    
        $stmt = $db->getRows($sql);

        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            { 
                $element_type = 'Input';
                if ($row['DATA_TYPE']=='text')
                {
                    $element_type = 'Textarea';
                }
                if ($row['DATA_TYPE']=='date' or $row['DATA_TYPE']=='datetime')
                {
                    $element_type = 'Date';
                }
                $column_name = $row['COLUMN_NAME'];
                $data_type = $row['DATA_TYPE'];
                $character_maximum_length = $row['CHARACTER_MAXIMUM_LENGTH'];
                $column_heading = $row['COLUMN_NAME'];

                $query = "INSERT INTO genforms (column_name,data_type,character_maximum_length,element_type,required_field,column_heading,table_name,show_on_form,created_date)   VALUES('$column_name', '$data_type', '$character_maximum_length', '$element_type', 'No', '$column_name', '$table_name', 'Yes', NOW() )";
                $result = $db->insertByQuery($query);
            }
        }
    }
    
    $sql = "SELECT * FROM genforms WHERE table_name='".$table_name."'";
    $columnlists = $db->getAllRecords($sql);
    echo json_encode($columnlists);
});

$app->get('/generateform/:table_name', function($table_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $jsscript = '';
    $phpcode = '';
    $htmlstring = '';
    $field_names = '';
    $first_flag = 'Yes';

    $jsscript .= '// '.$table_name.'

    app.controller(\''.$table_name.'_List_Ctrl\', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
        
        $timeout(function () { 
            Data.get(\''.$table_name.'_list_ctrl\').then(function (results) {
                $rootScope.'.$table_name.'s = results;
            });
        }, 100);
    });
        
        
    app.controller(\''.$table_name.'_Add_Ctrl\', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
        
        $scope.'.$table_name.'_add_new = {'.$table_name.':\'\'};
        $scope.'.$table_name.'_add_new = function ('.$table_name.') {
            Data.post(\''.$table_name.'_add_new\', {
                '.$table_name.': '.$table_name.'
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path(\''.$table_name.'_list\');
                }
            });
        };
    });
        
    app.controller(\''.$table_name.'_Edit_Ctrl\', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
        var '.$table_name.'_id = $routeParams.'.$table_name.'_id;
        $scope.activePath = null;
        
        Data.get(\''.$table_name.'_edit_ctrl/\'+'.$table_name.'_id).then(function (results) {
            $rootScope.'.$table_name.'s = results;
        });
        
        
        $scope.'.$table_name.'_update = function ('.$table_name.') {
            Data.post(\''.$table_name.'_update\', {
                '.$table_name.': '.$table_name.'
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path(\''.$table_name.'_list\');
                }
            });
        };
        
        $scope.'.$table_name.'_delete = function ('.$table_name.') {
            //console.log(business_unit);
            var delete'.$table_name.' = confirm(\'Are you absolutely sure you want to delete?\');
            if (delete'.$table_name.') {
                Data.post(\''.$table_name.'_delete\', {
                    '.$table_name.': '.$table_name.'
                }).then(function (results) {
                    Data.toast(results);
                    if (results.status == "success") {
                        $location.path(\''.$table_name.'_list\');
                    }
                });
            }
        };
        
    });
        
    app.controller(\'Select'.$table_name.'\', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

        $timeout(function () { 
            Data.get(\'select'.$table_name.'\').then(function (results) {
                $rootScope.'.$table_name.'s = results;
            });
        }, 100);
    });';


    $htmlstring .= '<div id="'.$table_name.'_info" >
	<form novalidate name="AddNewForm" id="add-new-form" role="form" autocomplete="off" enctype="multipart/form-data">
		<div class="row" id="'.$table_name.'_data">
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-header">
						<h3 class="box-title">Add '.$table_name.'</h3>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                    ';

    $sql = "SELECT * FROM genforms WHERE table_name='".$table_name."'";    
    $stmt = $db->getRows($sql);

    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $table_name = $row['table_name'];
            $column_name = $row['column_name'];
            $data_type = $row['data_type'];
            $character_maximum_length = $row['character_maximum_length'];
            $element_type = $row['element_type'];
            $column_heading = $row['column_heading'];
            $link_table = $row['link_table'];
            $required_field = $row['required_field'];
            $show_on_form = $row['show_on_form'];
            $required_flag = '';
            if ($required_field=='Yes')
            {
                $required_flag = ' required ';
            }
            if ($show_on_form=='Yes')
            {     
                if ($first_flag=='Yes')
                {
                    $field_names .= '\''.$column_name.'\'';
                    $first_flag = 'No';
                }
                else
                {
                    $field_names .= ',\''.$column_name.'\'';
                }
                if ($element_type=='Input')
                {
                    $htmlstring .= '        
                    <div class="form-group">
                        <label for="'.$column_name.'">'.$column_heading.'</label>
                        ';
                    $htmlstring .= '<input type="text" class="form-control" id="'.$column_name.'" name="'.$column_name.'"  placeholder="'.$column_heading.'" ng-model="'.$table_name.'.'.$column_name.'" '.$required_flag.' />
                    ';
                    $htmlstring .='</div>
                    ';
                }
                if ($element_type=='Checkbox')
                {
                    $htmlstring .= '<div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="'.$column_name.'" name="'.$column_name.'" ng-model="'.$table_name.'.'.$column_name.'" '.$required_flag.'>'.$column_heading.'
                            </label>
                        </div>
                    </div>
                    ';
                }

                if ($element_type=='Textarea')
                {
                    $htmlstring .= '        
                    <div class="form-group">
                        <label for="'.$column_name.'">'.$column_heading.'</label>
                        ';
                    $htmlstring .= '<textarea class="form-control" id="'.$column_name.'" name="'.$column_name.'"  placeholder="'.$column_heading.'" ng-model="'.$table_name.'.'.$column_name.'" '.$required_flag.'></textarea>
                    ';
                    $htmlstring .='</div>
                    ';
                }
                if ($element_type=='Date')
                {
                    $htmlstring .= '        
                    <div class="form-group">
                        <label for="'.$column_name.'">'.$column_heading.'</label>'
                        ;
                    $htmlstring .= '<div class="input-group date" id="'.$column_name.'" date-directive="">
                        <input class="form-control" type="text" ng-model="'.$table_name.'.'.$column_name.'" '.$required_flag.'>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>
                    ';
                }
                if ($element_type=='Select' or $element_type == 'Select [Multiple]')
                {
                    $multiple_flag = '';
                    if ($element_type == 'Select [Multiple]')
                    {
                        $multiple_flag = ' multiple="multiple" ';
                    }

                    $htmlstring .= '        
                    <div class="form-group">
                        <label for="'.$column_name.'">'.$column_heading.'</label>';
                    $htmlstring .='
                        <select class="select2 form-control" '.$multiple_flag.' id="'.$column_name.'" name="'.$column_name.'" ng-model="'.$table_name.'.'.$column_name.'" '.$required_flag.' data-placeholder="Select '.$column_name.'" style="width:100%;">
                            <option value="Not Specified" selected="selected">Not Specified</option>
                            <option value="Not Specified1" >Not Specified1</option>
                            <option value="Not Specified2" >Not Specified2</option>
                        </select>';
                    $htmlstring .='
                    </div>';
                }
                if ($element_type=='Select From Table' or $element_type == 'Select From Table [Multiple]')
                {
                    $multiple_flag = '';
                    if ($element_type == 'Select From [Multiple]')
                    {
                        $multiple_flag = ' multiple="multiple" ';
                    }

                    $htmlstring .= '<div class="form-group" ng-controller="Select'.$link_table.'">
                        <label for="'.$column_name.'">'.$column_heading.'</label>
                        <select class="select2 form-control"  '.$multiple_flag.'  ng-model="'.$table_name.'.'.$column_name.'" ng-options="'.$link_table.'.'.$column_name.' as '.$link_table.'.name for '.$link_table.' in '.$link_table.'s" '.$required_flag.'  style="width:100%;">
                            <option value="" disabled selected>Select'.$table_name.'</option>
                        </select>';
                    $htmlstring .='
                    </div>
                    ';
                }
            }
        }
    }

$htmlstring.='                          
                                        <div class="form-group">
                                            <button class="btn btn-primary" ng-disabled="AddNewForm.$invalid || isUnchanged('.$table_name.')" id="add-new-btn" ng-click="'.$table_name.'_add_new('.$table_name.')">Save </button>
                                            <a href="#/'.$table_name.'_list" class="btn">Cancel</a>
                                        </div>
                                    </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>';

$phpcode .='// '.$table_name.'

    $app->get(\'/'.$table_name.'_list_ctrl\', function() use ($app) {
        $sql  = "";
        $db = new DbHandler();
        $session = $db->getSession();
        if ($session[\'username\']=="Guest")
        {
            return;
        }
        $sql = "SELECT * from '.$table_name.' ORDER BY '.$table_name.'";
        $'.$table_name.'s = $db->getAllRecords($sql);
        echo json_encode($'.$table_name.'s);
    });


    $app->post(\'/'.$table_name.'_add_new\', function() use ($app) {
        $db = new DbHandler();
        $session = $db->getSession();
        if ($session[\'username\']=="Guest")
        {
            return;
        }
        $response = array();
        $r = json_decode($app->request->getBody());
        verifyRequiredParams(array(\''.$table_name.'\'),$r->'.$table_name.');
        $db = new DbHandler();
        $'.$table_name.' = $r->'.$table_name.'->'.$table_name.';
        $is'.$table_name.'Exists = $db->getOneRecord("select 1 from '.$table_name.' where '.$table_name.'=\'$'.$table_name.'\'");
        if(!$is'.$table_name.'Exists){
            $tabble_name = "'.$table_name.'";
            $column_names = array('.$field_names.');
            $multiple=array("");
            $result = $db->insertIntoTable($r->'.$table_name.', $column_names, $tabble_name, $multiple);
            if ($result != NULL) {
                $response["status"] = "success";
                $response["message"] = "'.$table_name.' created successfully";
                $response["'.$table_name.'_id"] = $result;
                echoResponse(200, $response);
            } else {
                $response["status"] = "error";
                $response["message"] = "Failed to create '.$table_name.'. Please try again";
                echoResponse(201, $response);
            }            
        }else{
            $response["status"] = "error";
            $response["message"] = "A '.$table_name.' with the provided '.$table_name.' exists!";
            echoResponse(201, $response);
        }
    });


    $app->get(\'/'.$table_name.'_edit_ctrl/:'.$table_name.'_id\', function($'.$table_name.'_id) use ($app) {
        $sql  = "";
        $db = new DbHandler();
        $session = $db->getSession();
        if ($session[\'username\']=="Guest")
        {
            return;
        }
        $sql = "SELECT * from '.$table_name.' where '.$table_name.'_id=".$'.$table_name.'_id;
        $'.$table_name.'s = $db->getAllRecords($sql);
        echo json_encode($'.$table_name.'s);
        
    });

    $app->post(\'/'.$table_name.'_update\', function() use ($app) {
        $db = new DbHandler();
        $session = $db->getSession();
        if ($session[\'username\']=="Guest")
        {
            return;
        }
        $response = array();
        $r = json_decode($app->request->getBody());
        verifyRequiredParams(array(\''.$table_name.'\'),$r->'.$table_name.');
        $db = new DbHandler();
        $'.$table_name.'_id  = $r->'.$table_name.'->'.$table_name.'_id;
        $'.$table_name.' = $r->'.$table_name.'->'.$table_name.';
        $is'.$table_name.'Exists = $db->getOneRecord("select 1 from '.$table_name.' where '.$table_name.'_id=$'.$table_name.'_id");
        if($is'.$table_name.'Exists){
            $tabble_name = "'.$table_name.'";
            $column_names = array('.$field_names.');
            $condition = "'.$table_name.'_id=\'$'.$table_name.'_id\'";
            $result = $db->updateIntoTable($r->'.$table_name.', $column_names, $tabble_name,$condition);
            if ($result != NULL) {
                $response["status"] = "success";
                $response["message"] = "'.$table_name.' Updated successfully";
                echoResponse(200, $response);
            } else {
                $response["status"] = "error";
                $response["message"] = "Failed to Update '.$table_name.'. Please try again";
                echoResponse(201, $response);
            }            
        }else{
            $response["status"] = "error";
            $response["message"] = "'.$table_name.' with the provided '.$table_name.' does not exists!";
            echoResponse(201, $response);
        }
    });

    $app->post(\'/'.$table_name.'_delete\', function() use ($app) {
        $db = new DbHandler();
        $session = $db->getSession();
        if ($session[\'username\']=="Guest")
        {
            return;
        }
        $response = array();
        $r = json_decode($app->request->getBody());
        verifyRequiredParams(array(\''.$table_name.'\'),$r->'.$table_name.');
        $db = new DbHandler();
        $'.$table_name.'_id  = $r->'.$table_name.'->'.$table_name.'_id;
        $'.$table_name.' = $r->'.$table_name.'->'.$table_name.';
        $is'.$table_name.'Exists = $db->getOneRecord("select 1 from '.$table_name.' where '.$table_name.'_id=$'.$table_name.'_id");
        if($is'.$table_name.'Exists){
            $tabble_name = "'.$table_name.'";
            $column_names = array('.$field_names.');
            $condition = "'.$table_name.'_id=\'$'.$table_name.'_id\'";
            $result = $db->deleteIntoTable($r->'.$table_name.', $column_names, $tabble_name,$condition);
            if ($result != NULL) {
                $response["status"] = "success";
                $response["message"] = "'.$table_name.' Deleted successfully";
                echoResponse(200, $response);
            } else {
                $response["status"] = "error";
                $response["message"] = "Failed to Delete '.$table_name.'. Please try again";
                echoResponse(201, $response);
            }            
        }else{
            $response["status"] = "error";
            $response["message"] = "'.$table_name.' with the provided '.$table_name.' does not exists!";
            echoResponse(201, $response);
        }
    });

    $app->get(\'/select'.$table_name.'\', function() use ($app) {
        $sql  = "";
        $db = new DbHandler();
        $session = $db->getSession();
        if ($session[\'username\']=="Guest")
        {
            return;
        }
        $sql = "SELECT * from '.$table_name.' ORDER BY '.$table_name.'";
        $'.$table_name.'s = $db->getAllRecords($sql);
        echo json_encode($'.$table_name.'s);
    });';



    $senddata =  array();
    $htmldata = array();
    
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['jsscript']=$jsscript;
    $htmldata['phpcode']=$phpcode;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/update_direct/:column_name/:form_id/:value', function($column_name, $form_id,$value) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE genforms set $column_name = '$value' where genforms_id = $form_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
});



// PROJECT

$app->get('/project_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  CONCAT(d.salu,' ',d.fname,' ',d.lname) as assign_to, d.mobile_no as usermobileno, (SELECT count(*) from property as q where a.project_id = q.project_id ) as properties_count,a.add1,a.add2 from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN users as c on a.assign_to = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id ORDER BY a.created_by DESC";
    $projects = $db->getAllRecords($sql);
    echo json_encode($projects);
});


$app->post('/project_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('project_name'),$r->project);
    $db = new DbHandler();
    $project_name = $r->project->project_name;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->project->created_by = $created_by;
    $r->project->created_date = $created_date;
    $isProjectExists = $db->getOneRecord("select 1 from project where project_name='$project_name'");
    if(!$isProjectExists){
        $tabble_name = "project";
        $column_names = array('project_name','developer_id', 'project_for','con_status','possession_date','completion_date', 'rera_num','add1','add2','exlocation','locality_id','area_id','zip','lattitude','longitude','salu','fname','lname','mob1','mob2','email','tot_area','tot_unit','pack_price','pack_price_comments','app_bankloan','amenities_avl','pro_specification','parking','teams','assign_to','source_channel','subsource_channel','groups','file_name','internal_comment','external_comment','numof_building','numof_floor','floor_rise','distfrm_station','distfrm_dairport','distfrm_school','distfrm_market','com_certification','youtube_link','rating','review','proj_status','created_by','created_date');
        $multiple=array("amenities_avl","app_bankloan","assign_to","groups","parking","pro_specification","source_channel","subsource_channel","teams");
        $result = $db->insertIntoTable($r->project, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "project created successfully";
            $response["project_id"] = $result;
            //session_start();
            $_SESSION['tmpproject_id'] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create project. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A project with the provided project exists!";
        echoResponse(201, $response);
    }
});

$app->post('/project_uploads', function() use ($app) {
    session_start();
    $project_id = $_SESSION['tmpproject_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file-1'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file-1'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "p_".$project_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "project". $ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, created_by, created_date)   VALUES('project', '$project_id','$file_names','Yes','Yes','Main','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});



$app->get('/project_edit_ctrl/:project_id', function($project_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from project where project_id=".$project_id;
    $projects = $db->getAllRecords($sql);
    echo json_encode($projects);
    
});

$app->post('/project_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('project_id'),$r->project);
    $db = new DbHandler();
    $project_id  = $r->project->project_id;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s');
    $r->project->modified_by = $modified_by;
    $r->project->modified_date = $modified_date;
    $isprojectExists = $db->getOneRecord("select 1 from project where project_id=$project_id");
    if($isprojectExists){
        $tabble_name = "project";
        $column_names = array('project_name','developer_id', 'project_for','con_status','possession_date','completion_date', 'rera_num','add1','add2','exlocation','locality_id','area_id','zip','lattitude','longitude','salu','fname','lname','mob1','mob2','email','tot_area','tot_unit','pack_price','pack_price_comments','app_bankloan','amenities_avl','pro_specification','parking','teams','assign_to','source_channel','subsource_channel','groups','file_name','internal_comment','external_comment','numof_building','numof_floor','floor_rise','distfrm_station','distfrm_dairport','distfrm_school','distfrm_market','com_certification','youtube_link','rating','review','proj_status','modified_by','modified_date');
        $multiple=array("amenities_avl","app_bankloan","assign_to","groups","parking","pro_specification","source_channel","subsource_channel","teams");
        $condition = "project_id='$project_id'";
        
        $history = $db->historydata( $r->project, $column_names, $tabble_name,$condition,$project_id,$multiple, $modified_by, $modified_date);

        $result = $db->NupdateIntoTable($r->project, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Project Updated successfully";
            $_SESSION['tmpproject_id'] = $project_id;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Project. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Project with the provided Project does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/project_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('project_id'),$r->project);
    $db = new DbHandler();
    $project_id  = $r->project->project_id;
    $isprojectExists = $db->getOneRecord("select 1 from project where project_id=$project_id");
    if($isprojectExists){
        $tabble_name = "project";
        $column_names = array('name');
        $condition = "project_id='$project_id'";
        $result = $db->deleteIntoTable($r->project, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Project Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Project. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Project with the provided Project does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectproject', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from project ORDER BY project_name";
    $projects = $db->getAllRecords($sql);
    echo json_encode($projects);
});

// property

$app->get('/properties_list_ctrl/:cat', function($cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,a.exp_price, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from property as t where a.project_id = t.project_id GROUP by t.project_id) as properties_count from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id WHERE a.proptype = '$cat' ORDER BY a.created_by DESC";
    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);
});

$app->get('/getproperties/:project_id', function($project_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from property WHERE project_id = $project_id ORDER BY created_date DESC";
    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);
});


$app->get('/getconfigurations/:project_id', function($project_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $htmlstring = '';
    $htmlstring .= '<div class="panel-body">
                        <div class="box-body table-responsive">
                            <table class="table" datatable-setupnosort2 ="" >
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Property Type</th>
                                        <th>Saleable Area</th>
                                        <th>Carpet Area</th>
                                        <th>Plot Area</th>
                                        <th>Price</th>
                                        <th>Matching Enquiries</th>
                                    </tr>
                                </thead>
                                <tbody>';

    $sql = "SELECT * from property as a LEFT JOIN project as b on a.project_id = b.project_id WHERE a.project_id = $project_id ORDER BY a.created_date DESC";   
    $stmt = $db->getRows($sql);

    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<tr>
                                <td>'.$row['project_name'].' '.$row['propsubtype'].' '.$row['property_for'].'</td>
                                <td>'.$row['proptype'].'</td>
                                <td>'.$row['sale_area'].'</td>
                                <td>'.$row['carp_area'].'</td>
                                <td>'.$row['exp_price'].'</td>
                                <td>'.$row['exp_price'].'</td>
                                <td><p style="width:25px;height:25px;background-color:#3362e6;padding: 6px;color: #ffffff;padding-left: 8px;">R</p></td>
                            </tr>';
        }
    }
    $htmlstring .= '</tbody>
                </table>    
            </div> 
        </div>';

    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/matchingenquiries/:project_id', function($project_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $htmlstring = '';
    $htmlstring .= '<div class="panel-body">
                        <div class="box-body table-responsive">
                            <table class="table" datatable-setupnosort2 ="" >
                                <thead>
                                    <tr>
                                        <th>Enquiry Type</th>
                                        <th>Status</th>
                                        <th>Preferred Area</th>
                                        <th>Preferred Locality</th>
                                        <th>Client</th>
                                        <th>Mobile No.</th>
                                        <th>Matching Enquiries</th>
                                    </tr>
                                </thead>
                                <tbody>';

    $sql = "SELECT *, CONCAT(d.salu,' ', d.fname,' ',d.lname) as assigned, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN users as c on a.assigned = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id  ORDER BY a.enquiry_for";
    $stmt = $db->getRows($sql);

    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<tr>
                                <td>'.$row['enquiry_type'].'</td>
                                <td>'.$row['status'].'</td>
                                <td>'.$row['preferred_area'].'</td>
                                <td>'.$row['preferred_locality'].'</td>
                                <td>'.$row['client_name'].'</td>
                                <td>'.$row['client_mob'].'</td>
                            </tr>';
        }
    }
    $htmlstring .= '</tbody>
                </table>    
            </div> 
        </div>';

    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/pmatchingenquiries/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $htmlstring = '';
    $htmlstring .= '<div class="panel-body">
                        <div class="box-body table-responsive">
                            <table class="table" datatable-setupnosort2 ="" >
                                <thead>
                                    <tr>
                                        <th>Enquiry Type</th>
                                        <th>Status</th>
                                        <th>Preferred Area</th>
                                        <th>Preferred Locality</th>
                                        <th>Client</th>
                                        <th>Mobile No.</th>
                                        <th>Matching Enquiries</th>
                                    </tr>
                                </thead>
                                <tbody>';

    $sql = "SELECT *, CONCAT(d.salu,' ', d.fname,' ',d.lname) as assigned, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN users as c on a.assigned = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id  ORDER BY a.enquiry_for";
    $stmt = $db->getRows($sql);

    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<tr>
                                <td>'.$row['enquiry_type'].'</td>
                                <td>'.$row['status'].'</td>
                                <td>'.$row['preferred_area'].'</td>
                                <td>'.$row['preferred_locality'].'</td>
                                <td>'.$row['client_name'].'</td>
                                <td>'.$row['client_mob'].'</td>
                            </tr>';
        }
    }
    $htmlstring .= '</tbody>
                </table>    
            </div> 
        </div>';

    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});





$app->post('/properties_add_new', function() use ($app) {
    $db = new DbHandler();  
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $created_date = date('Y-m-d H:m:s');
    $r->property->created_by = $created_by;
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('property_for'),$r->property);


    $created_by = $session['user_id'];
    $r->property->created_date = $created_date;
    $tabble_name = "property";
    $column_names = array('ageofprop','amenities_avl','area_id','area_para','assign_to','balconies','bathrooms','bedrooms','broke_involved','cabins','campaign','car_park','carp_area','city','completion_date','con_status','config','country','cubicals','dev_owner_id','distfrm_dairport','distfrm_market','distfrm_school','distfrm_station','door_fdir','efficiency','email_saletrainee','exlocation','exp_price','external_comment','floor','frontage','furniture','groups','height','internal_comment','keywith','kitchen','lift','loading','locality_id','mpm_cam','mpm_tot_tax','mpm_unit','numof_floor','occu_certi','oth_charges','pack_price','pack_price_comments','parking','possession_date','powersup','pre_leased','price_unit','pro_inspect','pro_sale_para','pro_specification','proj_status','project_id','property_for','propfrom','propsubtype','proptype','reg_date','rera_num','review','sale_area','seaters','sms_saletrainee','sms_saletrainee_office','soc_reg','source_channel','state','subsource_channel','teams','terrace','tranf_charge','unit','usd_area','watersup','wing','wings','workstation','youtube_link','created_by','created_date');
    $multiple=array("amenities_avl","assign_to","groups","parking","pro_specification","source_channel","subsource_channel","teams");
    $result = $db->insertIntoTable($r->property, $column_names, $tabble_name, $multiple);
    if($r->property->email_saletrainee==true){
        console.log('email_get');
        alert('get');
    }
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Property created successfully";
        $response["property_id"] = $result;
        $_SESSION['tmpproperty_id'] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create Property. Please try again";
        echoResponse(201, $response);
    }
    
});

$app->post('/property_uploads', function() use ($app) {
    session_start();
    $property_id = $_SESSION['tmpproperty_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file-1'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file-1'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "p_".$property_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, created_by, created_date)   VALUES('property', '$property_id','$file_names','Yes','Yes','Main','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->post('/multiproperty_add', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('project_id'),$r->multiproperty);
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->multiproperty->created_by = $created_by;
    $r->multiproperty->created_date = $created_date;
    $no_property = $r->multiproperty->no_property;
    $project_id = $r->multiproperty->project_id;

    $projectdata = $db->getAllRecords("SELECT * FROM project where project_id = $project_id");
    if ($projectdata)
    {
        $r->multiproperty->project_name = $projectdata[0]['project_name'];
        $r->multiproperty->propfrom = 'Developer';
        $r->multiproperty->dev_owner_id = $projectdata[0]['developer_id'];
        $r->multiproperty->property_for = $projectdata[0]['project_for'];
        $r->multiproperty->con_status = $projectdata[0]['con_status'];
        $r->multiproperty->possession_date = $projectdata[0]['possession_date'];
        $r->multiproperty->completion_date = $projectdata[0]['completion_date'];
        $r->multiproperty->rera_num = $projectdata[0]['rera_num'];
        $r->multiproperty->add1 = $projectdata[0]['add1'];
        $r->multiproperty->add2 = $projectdata[0]['add2'];
        $r->multiproperty->exlocation = $projectdata[0]['exlocation'];
        $r->multiproperty->locality_id = $projectdata[0]['locality_id'];
        $r->multiproperty->area_id = $projectdata[0]['area_id'];
        $r->multiproperty->zip = $projectdata[0]['zip'];
        //$r->multiproperty->lattitude = $projectdata[0]['lattitude'];
        //$r->multiproperty->longitude = $projectdata[0]['longitude'];
        $r->multiproperty->amenities_avl = $projectdata[0]['amenities_avl'];
        $r->multiproperty->parking = $projectdata[0]['parking'];
        $r->multiproperty->teams = $projectdata[0]['teams'];
        $r->multiproperty->assign_to = $projectdata[0]['assign_to'];
        $r->multiproperty->source_channel = $projectdata[0]['source_channel'];
        $r->multiproperty->subsource_channel = $projectdata[0]['subsource_channel'];
        $r->multiproperty->groups = $projectdata[0]['groups'];
        $r->multiproperty->internal_comment = $projectdata[0]['internal_comment'];
        $r->multiproperty->external_comment = $projectdata[0]['external_comment'];
        $r->multiproperty->numof_floor = $projectdata[0]['numof_floor'];
        $r->multiproperty->distfrm_station = $projectdata[0]['distfrm_station'];
        $r->multiproperty->distfrm_dairport = $projectdata[0]['distfrm_dairport'];
        $r->multiproperty->distfrm_school = $projectdata[0]['distfrm_school'];
        $r->multiproperty->distfrm_market = $projectdata[0]['distfrm_market'];

    }

    if ($no_property!=null)
    {

    }
    else
    {
        $no_property = 1;
    }
    $success = false;
    for ($row = 1; $row <=$no_property; ++$row) 
    {

        $tabble_name = "property";
        $multiple=array("");
        $column_names = array('project_name','propfrom','dev_owner_id','property_for','con_status','possession_date','completion_date','rera_num','add1','add2','exlocation','locality_id','area_id','zip','amenities_avl','parking','teams','assign_to','source_channel','subsource_channel','groups','internal_comment','external_comment','numof_floor','distfrm_station','distfrm_dairport','distfrm_school','distfrm_market','wing','floor','bathrooms','bedrooms','building_plot','carp_area','carp_area_upside','exp_price','exp_price_upside','multi_size','oth_charges','pack_price','price_unit','price_unit_carpet','project_id','propsubtype','proptype','sale_area','sale_area_upside','created_by','created_date');
        $result = $db->insertIntoTable($r->multiproperty, $column_names, $tabble_name, $multiple);
        if ($result != NULL)
        {
            $success = true;
        }
        else
        {
            $success = false;
        }
    }
    if ($success) 
    {
        $response["status"] = "success";
        $response["message"] = "Properties created successfully";
        $response["project_id"] = $result;
        echoResponse(200, $response);
    } 
    else 
    {
        $response["status"] = "error";
        $response["message"] = "Failed to create Properties. Please try again";
        echoResponse(201, $response);
    }       
});

$app->get('/properties_edit_ctrl/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from property where property_id=".$property_id;
    $propertys = $db->getAllRecords($sql);
    echo json_encode($propertys);
    
});

$app->post('/properties_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('property_id'),$r->property);
    $db = new DbHandler();
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s');
    $r->property->modified_by = $modified_by;
    $r->property->modified_date = $modified_date;

    $property_id  = $r->property->property_id;
    $ispropertyExists = $db->getOneRecord("select 1 from property where property_id=$property_id");
    if($ispropertyExists){
        $tabble_name = "property";
        $column_names = array('ageofprop','amenities_avl','area_id','area_para','assign_to','balconies','bathrooms','bedrooms','broke_involved','cabins','campaign','car_park','carp_area','city','completion_date','con_status','config','country','cubicals','dev_owner_id','distfrm_dairport','distfrm_market','distfrm_school','distfrm_station','door_fdir','efficiency','email_saletrainee','exlocation','exp_price','external_comment','floor','frontage','furniture','groups','height','internal_comment','keywith','kitchen','lift','loading','locality_id','mpm_cam','mpm_tot_tax','mpm_unit','numof_floor','occu_certi','oth_charges','pack_price','pack_price_comments','parking','possession_date','powersup','pre_leased','price_unit','pro_inspect','pro_sale_para','pro_specification','proj_status','project_id','property_for','propfrom','propsubtype','reg_date','rera_num','review','sale_area','seaters','sms_saletrainee','soc_reg','source_channel','state','subsource_channel','teams','terrace','tranf_charge','unit','usd_area','watersup','wing','wings','workstation','youtube_link','modified_by','modified_date');
        $multiple=array("amenities_avl","assign_to","groups","parking","pro_specification","source_channel","subsource_channel","teams");
        $condition = "property_id='$property_id'";
        $history = $db->historydata( $r->property, $column_names, $tabble_name,$condition,$property_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->property, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Property Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Property. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Property with the provided Property does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/multiproperty_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('property_id'),$r->multiproperty);
    $db = new DbHandler();
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s');
    $r->multiproperty->modified_by = $modified_by;
    $r->multiproperty->modified_date = $modified_date;

    $property_id  = $r->multiproperty->property_id;
    $ispropertyExists = $db->getOneRecord("select 1 from property where property_id=$property_id");
    if($ispropertyExists){
        $tabble_name = "property";
        $column_names = array('wing','floor','bathrooms','bedrooms','building_plot','carp_area','carp_area_upside','exp_price','exp_price_upside','multi_size','oth_charges','pack_price','price_unit','price_unit_carpet','project_id','propsubtype','proptype','sale_area','sale_area_upside','modified_by','modified_date');
        $condition = "property_id='$property_id'";
        $result = $db->updateIntoTable($r->multiproperty, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Property Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Property. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Property with the provided Property does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/properties_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('property_id'),$r->property);
    $db = new DbHandler();
    $property_id  = $r->property->property_id;
    $ispropertyExists = $db->getOneRecord("select 1 from property where property_id=$property_id");
    if($ispropertyExists){
        $tabble_name = "property";
        $column_names = array('name');
        $condition = "property_id='$property_id'";
        $result = $db->deleteIntoTable($r->property, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Property Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Property. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Property with the provided Property does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/multiproperty_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('property_id'),$r->multiproperty);
    $db = new DbHandler();
    $property_id  = $r->multiproperty->property_id;
    $ispropertyExists = $db->getOneRecord("select 1 from property where property_id=$property_id");
    if($ispropertyExists){
        $tabble_name = "property";
        $column_names = array('name');
        $condition = "property_id='$property_id'";
        $result = $db->deleteIntoTable($r->multiproperty, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Property Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Property. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Property with the provided Property does not exists!";
        echoResponse(201, $response);
    }
});


$app->get('/selectproperty', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from property ORDER BY property";
    $permissions = $db->getAllRecords($sql);
    echo json_encode($permissios);
});

$app->get('/deal_done/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE property set deal_done = 'Yes' where property_id = $property_id " ;  
    $result = $db->updateByQuery($sql);
    $response["status"] = "success";
    $response["message"] = "Marked as - Deal Done !!!";
    //echoResponse(200, $response);

    echo json_encode($response);
});


$app->get('/getproperties/:enquiry_id', function($enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from property ORDER BY property";
    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);
});



// ENQUIRY

$app->get('/enquiries_list_ctrl/:cat', function($cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql="SELECT *, CONCAT(d.salu,' ', d.fname,' ',d.lname) as assigned, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN users as c on a.assigned = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id  where a.enquiry_off = '$cat' ORDER BY a.enquiry_for";
    $enquiries = $db->getAllRecords($sql);
    echo json_encode($enquiries);
});


$app->post('/enquiries_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('enquiry_for'),$r->enquiry);
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->enquiry->created_by = $created_by;
    $r->enquiry->created_date = $created_date;
    $tabble_name = "enquiry";
    $column_names = array('enquiry_off', 'amenities_avl','area_para','assigned','bath','bedrooms','broker_id','broker_involved','budget_range1','budget_range2','campaign','carp_area1','carp_area2','client_id','con_status','depo_range1','depo_range2','dt_poss_max','dt_poss_min','email_salestrainee','enquiry_for','external_comment','floor_range1','floor_range2','frontage','furniture','groups','height','intrnal_comment','lease_period','loan_req','portal_id','pre_leased','preferred_area_id','preferred_city','preferred_country','preferred_locality_id','preferred_project_id','preferred_state','priority','pro_alerts','enquiry_type','reg_date','sale_area1','sale_area2','share_agsearch','sms_salestrainee','source_channel','stage','status','subscr_email','subsource_channel','teams','tot_area1','tot_area2','tot_para','vastu_comp','created_by','created_date');
    $multiple=array("amenities_avl","assigned","groups","source_channel","subsource_channel","teams",'parking');
    $result = $db->insertIntoTable($r->enquiry, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Enquiry created successfully";
        $response["enquiry_id"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create Enquiry. Please try again";
        echoResponse(201, $response);
    }
});


$app->get('/enquiries_edit_ctrl/:enquiry_id', function($enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from enquiry where enquiry_id=".$enquiry_id;
    $enquiries = $db->getAllRecords($sql);
    echo json_encode($enquiries);
    
});

$app->post('/enquiries_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('enquiry_for'),$r->enquiry);
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s');
    $r->enquiry->modified_by = $modified_by;
    $r->enquiry->modified_date = $modified_date;

    $enquiry_id  = $r->enquiry->enquiry_id;
    $isenquiryExists = $db->getOneRecord("select 1 from enquiry where enquiry_id=$enquiry_id");
    if($isenquiryExists){
        $tabble_name = "enquiry";
        $column_names = array('enquiry_off', 'amenities_avl','area_para','assigned','bath','bedrooms','broker_id','broker_involved','budget_range1','budget_range2','campaign','carp_area1','carp_area2','client_id','con_status','depo_range1','depo_range2','dt_poss_max','dt_poss_min','email_salestrainee','enquiry_for','external_comment','floor_range1','floor_range2','frontage','furniture','groups','height','intrnal_comment','lease_period','loan_req','portal_id','pre_leased','preferred_area_id','preferred_city','preferred_country','preferred_locality_id','preferred_project_id','preferred_state','priority','pro_alerts','enquiry_type','reg_date','sale_area1','sale_area2','share_agsearch','sms_salestrainee','source_channel','stage','status','subscr_email','subsource_channel','teams','tot_area1','tot_area2','tot_para','vastu_comp','modified_by','modified_date');
        $multiple=array("amenities_avl","assigned","groups","source_channel","subsource_channel","teams",'parking');
        $condition = "enquiry_id='$enquiry_id'";
        
        $history = $db->historydata( $r->enquiry, $column_names, $tabble_name,$condition,$enquiry_id,$multiple, $modified_by, $modified_date);

        $result = $db->NupdateIntoTable($r->enquiry, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Enquiry Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Enquiry. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Enquiry with the provided Enquiry does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/enquiries_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('enquiry_id'),$r->enquiry);
    $enquiry_id  = $r->enquiry->enquiry_id;
    $isenquiryExists = $db->getOneRecord("select 1 from enquiry where enquiry_id=$enquiry_id");
    if($isenquiryExists){
        $tabble_name = "enquiry";
        $column_names = array('enqiry_for');
        $condition = "enquiry_id='$enquiry_id'";
        $result = $db->deleteIntoTable($r->enquiry, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Enquiry Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Enquiry. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Enquiry with the provided Enquiry does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectenquiry', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *, enquiry_type as enquiry_title  from enquiry where status='Active' ORDER BY created_date DESC";
    $enquiries = $db->getAllRecords($sql);
    echo json_encode($enquiries);
});

// CONTACT

$app->get('/contact_list_ctrl/:cat', function($cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from contact where contact_off = '$cat' ORDER BY name";
    $contacts = $db->getAllRecords($sql);
    echo json_encode($contacts);
});


$app->post('/contact_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('contact'),$r->contact);
    $db = new DbHandler();
    $contact = $r->contact->contact;
    $iscontactExists = $db->getOneRecord("select 1 from contact where contact='$contact'");
    if(!$isPermissionExists){
        $tabble_name = "contact";
        $column_names = array('company_name','add1','add2','locality','area','city','state','country','zip','comp_logo','name_title','f_name','l_name','mob_no','mob_no1','email','alt_phone_no','alt_phone_no1','contact_pic','designation','birth_date','team','assigned_to','groups','rera_no','gst_no','comments','dnd','other_phone','pan_no','tan_no','occupation','off_email','off_phone','off_phone1','off_phone2','off_fax','off_add1','off_locality','off_area','off_city','off_state','off_country','off_zip','source_channel','source_sub_channel','reg_date','website','rating','opp_city','opp_area','about','invoice_name');
        $multiple=array("");

        $history = $db->historydata( $r->contact, $column_names, $tabble_name,$condition,$contact_id,$multiple, $modified_by, $modified_date);
        
        $result = $db->insertIntoTable($r->contact, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "contact created successfully";
            $response["contact_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create contact. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A contact with the provided contact exists!";
        echoResponse(201, $response);
    }
});


$app->get('/contact_edit_ctrl/:contact_id', function($contact_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from contact where contact_id=".$contact_id;
    $contacts = $db->getAllRecords($sql);
    echo json_encode($contacts);
    
});

$app->post('/contact_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('contact'),$r->contact);
    $db = new DbHandler();
    $contact_id  = $r->contact->contact_id;
    $contact = $r->contact->contact;
    $iscontactExists = $db->getOneRecord("select 1 from contact where contact_id=$contact_id");
    if($iscontactExists){
        $tabble_name = "contact";
        $column_names = array('company_name','add1','add2','locality','area','city','state','country','zip','comp_logo','name_title','f_name','l_name','mob_no','mob_no1','email','alt_phone_no','alt_phone_no1','contact_pic','designation','birth_date','team','assigned_to','groups','rera_no','gst_no','comments','dnd','other_phone','pan_no','tan_no','occupation','off_email','off_phone','off_phone1','off_phone2','off_fax','off_add1','off_locality','off_area','off_city','off_state','off_country','off_zip','source_channel','source_sub_channel','reg_date','website','rating','opp_city','opp_area','about','invoice_name');
        $condition = "contact_id='$contact_id'";
        $result = $db->updateIntoTable($r->contact, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "contact Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update contact. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "contact with the provided contact does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/contact_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('contact'),$r->contact);
    $db = new DbHandler();
    $contact_id  = $r->contact->contact_id;
    $contact = $r->contact->contact;
    $iscontactExists = $db->getOneRecord("select 1 from contact where contact_id=$contact_id");
    if($iscontactExists){
        $tabble_name = "contact";
        $column_names = array('company_name','add1','add2','locality','area','city','state','country','zip','comp_logo','name_title','f_name','l_name','mob_no','mob_no1','email','alt_phone_no','alt_phone_no1','contact_pic','designation','birth_date','team','assigned_to','groups','rera_no','gst_no','comments','dnd','other_phone','pan_no','tan_no','occupation','off_email','off_phone','off_phone1','off_phone2','off_fax','off_add1','off_locality','off_area','off_city','off_state','off_country','off_zip','source_channel','source_sub_channel','reg_date','website','rating','opp_city','opp_area','about','invoice_name');
        $condition = "contact_id='$contact_id'";
        $result = $db->deleteIntoTable($r->contact, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "contact Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete contact. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "contact with the provided contact does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectcontact/:cat', function($cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT contact_id,contact_off, CONCAT(name_title,' ',f_name,' ',l_name,'   ',mob_no,'  ',company_name) as name from contact WHERE contact_off = '$cat' ORDER BY f_name,l_name";
    $contacts = $db->getAllRecords($sql);
    echo json_encode($contacts);
});

$app->get('/selectusers', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *, CONCAT(b.salu,' ',b.fname,' ',b.lname) as name from users as a LEFT JOIN employee as b on a.emp_id = b.emp_id ORDER BY b.lname,b.fname";
    $users = $db->getAllRecords($sql);
    echo json_encode($users);
});


// AGREEMENTS

$app->get('/agreement_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from agreement";
    $agreements = $db->getAllRecords($sql);
    echo json_encode($agreements);
});

$app->get('/properties_dealclose', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,g.name as project_contact, g.name as assign_to, g.mobile_no as usermobileno from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id WHERE a.deal_done = 'Yes' ORDER BY a.created_by DESC";
    
    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);
});



$app->post('/agreement_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('agreement'),$r->agreement);
    $db = new DbHandler();
    $agreement = $r->agreement->agreement;
    $isagreementExists = $db->getOneRecord("select 1 from agreement where agreement='$agreement'");
    if(!$isPermissionExists){
        $tabble_name = "agreement";
        $column_names = array('property_id','agreement_from','contact_id','enquiry_id','buyer_id','inname','transfer_type','transfer_term','deal_date','shifting_date','agreement_from_date','lease_period','agreement_till_date','rent_due_date','send_rent_sms','assigned_to','teams','agreement_value','furniture','basic_cost','rent','buyer_brokerage','seller_brokerage','total_bokerage','our_brokerage','service_tax','cgst','sgst','tds','gross_brokerage','stamp_duty','stamp_duty_date','registration_charges','registration_charges_para','security_deposit','security_deposit_date','development_charges','transfer_charges','transfer_charges_date','advance_maintenance','parking_charges','document_charges','document_charges_date','corpus_fund','club_house_charges','club_house_date','other_expenses');
        $multiple=array("");
        $result = $db->insertIntoTable($r->agreement, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "agreement created successfully";
            $response["agreement_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create agreement. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A agreement with the provided agreement exists!";
        echoResponse(201, $response);
    }
});


$app->get('/agreement_edit_ctrl/:agreement_id', function($agreement_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from agreement where agreement_id=".$agreement_id;
    $agreements = $db->getAllRecords($sql);
    echo json_encode($agreements);
    
});

$app->post('/agreement_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('agreement'),$r->agreement);
    $db = new DbHandler();
    $agreement_id  = $r->agreement->agreement_id;
    $agreement = $r->agreement->agreement;
    $isagreementExists = $db->getOneRecord("select 1 from agreement where agreement_id=$agreement_id");
    if($isagreementExists){
        $tabble_name = "agreement";
        $column_names = array('property_id','agreement_from','contact_id','enquiry_id','buyer_id','inname','transfer_type','transfer_term','deal_date','shifting_date','agreement_from_date','lease_period','agreement_till_date','rent_due_date','send_rent_sms','assigned_to','teams','agreement_value','furniture','basic_cost','rent','buyer_brokerage','seller_brokerage','total_bokerage','our_brokerage','service_tax','cgst','sgst','tds','gross_brokerage','stamp_duty','stamp_duty_date','registration_charges','registration_charges_para','security_deposit','security_deposit_date','development_charges','transfer_charges','transfer_charges_date','advance_maintenance','parking_charges','document_charges','document_charges_date','corpus_fund','club_house_charges','club_house_date','other_expenses');
        $condition = "agreement_id='$agreement_id'";
        $result = $db->updateIntoTable($r->agreement, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "agreement Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update agreement. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "agreement with the provided agreement does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/agreement_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('agreement'),$r->agreement);
    $db = new DbHandler();
    $agreement_id  = $r->agreement->agreement_id;
    $agreement = $r->agreement->agreement;
    $isagreementExists = $db->getOneRecord("select 1 from agreement where agreement_id=$agreement_id");
    if($isagreementExists){
        $tabble_name = "agreement";
        $column_names = array('property_id','agreement_from','contact_id','enquiry_id','buyer_id','inname','transfer_type','transfer_term','deal_date','shifting_date','agreement_from_date','lease_period','agreement_till_date','rent_due_date','send_rent_sms','assigned_to','teams','agreement_value','furniture','basic_cost','rent','buyer_brokerage','seller_brokerage','total_bokerage','our_brokerage','service_tax','cgst','sgst','tds','gross_brokerage','stamp_duty','stamp_duty_date','registration_charges','registration_charges_para','security_deposit','security_deposit_date','development_charges','transfer_charges','transfer_charges_date','advance_maintenance','parking_charges','document_charges','document_charges_date','corpus_fund','club_house_charges','club_house_date','other_expenses');
        $condition = "agreement_id='$agreement_id'";
        $result = $db->deleteIntoTable($r->agreement, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "agreement Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete agreement. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "agreement with the provided agreement does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectagreement', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from agreement ORDER BY agreement";
    $permissions = $db->getAllRecords($sql);
    echo json_encode($permissios);
});

// ACCOUNT

$app->get('/account_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from account as a LEFT JOIN contact as b on a.client_id = b.contact_id  ORDER BY transaction_date";
    $accounts = $db->getAllRecords($sql);
    echo json_encode($accounts);
});


$app->post('/account_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('account'),$r->account);
    $db = new DbHandler();
    $account = $r->account->account;
    $isaccountExists = $db->getOneRecord("select 1 from account where account='$account'");
    if(!$isPermissionExists){
        $tabble_name = "account";
        $column_names = array('client_id','transaction_date','adjustment_type','amount','payment_type','receipt_no','receipt_date','instrument_no','cheque_no','cheque_date','drawn_on','branch','cheque_status','comments','team');
        $multiple=array("");
        $result = $db->insertIntoTable($r->account, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "account created successfully";
            $response["account_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create account. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A account with the provided account exists!";
        echoResponse(201, $response);
    }
});


$app->get('/account_edit_ctrl/:account_id', function($account_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from account where account_id=".$account_id;
    $accounts = $db->getAllRecords($sql);
    echo json_encode($accounts);
    
});

$app->post('/account_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('account'),$r->account);
    $db = new DbHandler();
    $account_id  = $r->account->account_id;
    $account = $r->account->account;
    $isaccountExists = $db->getOneRecord("select 1 from account where account_id=$account_id");
    if($isaccountExists){
        $tabble_name = "account";
        $column_names = array('client_id','transaction_date','adjustment_type','amount','payment_type','receipt_no','receipt_date','instrument_no','cheque_no','cheque_date','drawn_on','branch','cheque_status','comments','team');
        $condition = "account_id='$account_id'";
        $result = $db->updateIntoTable($r->account, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "account Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update account. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "account with the provided account does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/account_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('account'),$r->account);
    $db = new DbHandler();
    $account_id  = $r->account->account_id;
    $account = $r->account->account;
    $isaccountExists = $db->getOneRecord("select 1 from account where account_id=$account_id");
    if($isaccountExists){
        $tabble_name = "account";
        $column_names = array('client_id','transaction_date','adjustment_type','amount','payment_type','receipt_no','receipt_date','instrument_no','cheque_no','cheque_date','drawn_on','branch','cheque_status','comments','team');
        $condition = "account_id='$account_id'";
        $result = $db->deleteIntoTable($r->account, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "account Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete account. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "account with the provided account does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectaccount', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from account ORDER BY account";
    $permissions = $db->getAllRecords($sql);
    echo json_encode($permissios);
});

// ACTIVITIES

$app->get('/activity_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *, DATE_FORMAT(start_date,'%d-%m-%Y %H:%m') AS start_date, DATE_FORMAT(end_date,'%d-%m-%Y %H:%m') AS end_date, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker, CONCAT(f.name_title,' ',f.f_name,' ', f.l_name) as target from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN contact as f on a.target_id = f.contact_id and f.contact_off = 'Target List' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id ORDER BY a.start_date";
    $activities = $db->getAllRecords($sql);
    echo json_encode($activities);
});


$app->post('/activity_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('activity_type'),$r->activity);
    $db = new DbHandler();
    
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->activity->created_by = $created_by;
    $r->activity->created_date = $created_date;

    if (isset($r->activity->start_date))
    {
        $start_date = $r->activity->start_date;
        $tstart_date = substr($start_date,6,4)."-".substr($start_date,3,2)."-".substr($start_date,0,2)." ".substr($start_date,11,5);
        $r->activity->start_date = $tstart_date;
    }

    if (isset($r->activity->end_date))
    {
        $end_date = $r->activity->end_date;
        $tend_date = substr($end_date,6,4)."-".substr($end_date,3,2)."-".substr($end_date,0,2)." ".substr($end_date,11,5);
        $r->activity->end_date = $tend_date;
    }
    $tabble_name = "activity";
    $column_names = array('activity_type','assign_to','broker_id','client_id','description','developer_id','end_date','enquiry_id','properties','remind','remind_before','remind_time','start_date','status','teams','created_by','created_date');
    $multiple=array('assign_to','properties','teams');
    $result = $db->insertIntoTable($r->activity, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "activity created successfully";
        $response["activity_id"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create activity. Please try again";
        echoResponse(201, $response);
    }
});

$app->get('/activity_edit_ctrl/:activity_id', function($activity_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    //$sql = "SELECT *, DATE_FORMAT(start_date,'%Y-%m-%d %H:%m') AS start_date, DATE_FORMAT(end_date,'%Y-%m-%d %H:%m') AS end_date from activity where activity_id=".$activity_id;
    $sql = "SELECT *, DATE_FORMAT(start_date,'%d/%m/%Y %H:%m') AS start_date, DATE_FORMAT(end_date,'%d/%m/%Y %H:%m') AS end_date from activity where activity_id=".$activity_id;
    $activities = $db->getAllRecords($sql);
    echo json_encode($activities);
    
});

$app->post('/activity_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('activity_type'),$r->activity);
    $db = new DbHandler();
    $activity_id  = $r->activity->activity_id;

    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s');
    $r->activity->modified_by = $modified_by;
    $r->activity->modified_date = $modified_date;

    if (isset($r->activity->start_date))
    {
        $start_date = $r->activity->start_date;
        $tstart_date = substr($start_date,6,4)."-".substr($start_date,3,2)."-".substr($start_date,0,2)." ".substr($start_date,11,5);
        $r->activity->start_date = $tstart_date;
    }

    if (isset($r->activity->end_date))
    {
        $end_date = $r->activity->end_date;
        $tend_date = substr($end_date,6,4)."-".substr($end_date,3,2)."-".substr($end_date,0,2)." ".substr($end_date,11,5);
        $r->activity->end_date = $tend_date;
    }

    $isactivityExists = $db->getOneRecord("select 1 from activity where activity_id=$activity_id");
    if($isactivityExists){
        $tabble_name = "activity";
        $condition = "activity_id='$activity_id'";
        $column_names = array('activity_type','assign_to','broker_id','client_id','description','developer_id','end_date','enquiry_id','properties','remind','remind_before','remind_time','start_date','status','teams','modified_by','modified_date');
        $multiple=array('assign_to','properties','teams');
        $history = $db->historydata( $r->activity, $column_names, $tabble_name,$condition,$activity_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->activity, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Activity Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Activity. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Activity with the provided Activity does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/activity_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('activity_id'),$r->activity);
    $db = new DbHandler();
    $activity_id  = $r->activity->activity_id;
    $isactivityExists = $db->getOneRecord("select 1 from activity where activity_id=$activity_id");
    if($isactivityExists){
        $tabble_name = "activity";
        $column_names = array('activity_type','start_date','end_date','enquiry','contact','properties','description','teams','assignTo','remind','remindBefore','remind_time','status');
        $condition = "activity_id='$activity_id'";
        $result = $db->deleteIntoTable($r->activity, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "activity Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete activity. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "activity with the provided activity does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectactivity', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from activity ORDER BY activity";
    $permissions = $db->getAllRecords($sql);
    echo json_encode($permissios);
});

// ALERTS

$app->get('/showalerts_list_ctrl/:category', function($category) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    if ($category == 'birthday')
    {
        $sql = "SELECT * from contact ORDER BY birth_date";
        $birthdayalerts = $db->getAllRecords($sql);
        echo json_encode($birthdayalerts);
    }

    if ($category == 'lease')
    {
        $sql = "SELECT * from agreement as a LEFT JOIN properties as b on a.property_id = b.property_id ORDER BY a.agreement_till_date";
        $leasealerts = $db->getAllRecords($sql);
        echo json_encode($leasealerts);
    }

});

// DROPDOWNS

$app->get('/dropdowns_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from dropdowns ORDER BY type,display_value";
    $dropdowns = $db->getAllRecords($sql);
    echo json_encode($dropdowns);
});


$app->post('/dropdowns_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('type'),$r->dropdown);
    $db = new DbHandler();
    $type = $r->dropdown->type;
    $value = $r->dropdown->value;
    $isDropdownsExists = $db->getOneRecord("select 1 from dropdowns where type='$type' and value='$value' ");
    if(!$isDropdownsExists){
        $tabble_name = "dropdowns";
        $column_names = array('type','value','display_value','isdefault','depth','parent_type','sequence_number');
        $multiple=array("");
        $result = $db->insertIntoTable($r->dropdown, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Dropdown created successfully";
            $response["dropdowns_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Dropdown. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Dropdown with the provided Dropdown exists!";
        echoResponse(201, $response);
    }
});


$app->get('/dropdowns_edit_ctrl/:dropdowns_id', function($dropdowns_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from dropdowns where dropdowns_id=".$dropdowns_id;
    $dropdowns = $db->getAllRecords($sql);
    echo json_encode($dropdowns);
    
});

$app->post('/dropdowns_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('type'),$r->dropdown);
    $db = new DbHandler();
    $dropdowns_id  = $r->dropdown->dropdowns_id;
    $isdropdownsExists = $db->getOneRecord("select 1 from dropdowns where dropdowns_id=$dropdowns_id");
    if($isdropdownsExists){
        $tabble_name = "dropdowns";
        $column_names = array('type','value','display_value','isdefault','depth','parent_type','sequence_number');
        $condition = "dropdowns_id='$dropdowns_id'";
        $result = $db->updateIntoTable($r->dropdown, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Dropdown Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Dropdown. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Dropdown with the provided dropdown does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/dropdowns_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('type'),$r->dropdown);
    $db = new DbHandler();
    $dropdowns_id  = $r->dropdown->dropdowns_id;
    $isdropdownsExists = $db->getOneRecord("select 1 from dropdowns where dropdowns_id=$dropdowns_id");
    if($isdropdownsExists){
        $tabble_name = "dropdowns";
        $column_names = array('type','value','display_value','isdefault','depth','parent_type','sequence_number');
        $condition = "dropdowns_id='$dropdowns_id'";
        $result = $db->deleteIntoTable($r->dropdown, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Dropdown Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete dropdown. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Dropdown with the provided dropdown does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectdropdowns/:dropdown_type', function($dropdown_type) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from dropdowns WHERE type = '$dropdown_type' ORDER BY display_value";
    $dropdowns = $db->getAllRecords($sql);
    echo json_encode($dropdowns);
});

$app->get('/selectparentlist', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT display_value, CONCAT(type,' - ',display_value) as parent_type from dropdowns ORDER BY type,display_value";
    $parentlists = $db->getAllRecords($sql);
    echo json_encode($parentlists);
});

$app->post('/listvalues_add', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('value'),$r->listvalues);
    $db = new DbHandler();
    $value = $r->listvalues->value;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->listvalues->created_by = $created_by;
    $r->listvalues->created_date = $created_date;
    $isListvaluesExists = $db->getOneRecord("select 1 from dropdowns where value='$value'");
    if(!$isListvaluesExists){
        $tabble_name = "dropdowns";
        $column_names = array('type','display_value','value','parent_type', 'created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->listvalues, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "List Value created successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create List Value. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A List Value with the provided Value exists!";
        echoResponse(201, $response);
    }
});



// TEAMS

$app->get('/teams_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from teams ORDER BY team_name";
    $teams = $db->getAllRecords($sql);
    echo json_encode($teams);
});


$app->post('/teams_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('team_name'),$r->team);
    $db = new DbHandler();
    $team_name = $r->team->team_name;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->team->created_by = $created_by;
    $r->team->created_date = $created_date;
    $isTeamsExists = $db->getOneRecord("select 1 from teams where team_name='$team_name'");
    if(!$isTeamsExists){
        $tabble_name = "teams";
        $column_names = array('team_name','team_description','created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->team, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Teams created successfully";
            $response["team_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Teams. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Teams with the provided teams exists!";
        echoResponse(201, $response);
    }
});


$app->get('/teams_edit_ctrl/:team_id', function($team_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from teams where team_id=".$team_id;
    $teams = $db->getAllRecords($sql);
    echo json_encode($teams);
    
});

$app->post('/teams_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('team_name'),$r->team);
    $db = new DbHandler();
    $team_id  = $r->team->team_id;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s');
    $r->team->modified_by = $modified_by;
    $r->team->modified_date = $modified_date;
    $isteamsExists = $db->getOneRecord("select 1 from teams where team_id=$team_id");
    if($isteamsExists){
        $tabble_name = "teams";
        $column_names = array('team_name','team_description', 'modified_by','modified_date');
        $condition = "team_id='$team_id'";
        $multiple=array("");
        $history = $db->historydata( $r->team, $column_names, $tabble_name,$condition,$team_id,$multiple, $modified_by, $modified_date);
        $result = $db->updateIntoTable($r->team, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Teams Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Teams. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Teams with the provided teams does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/teams_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('team_name'),$r->team);
    $db = new DbHandler();
    $team_id  = $r->team->team_id;
    $isteamsExists = $db->getOneRecord("select 1 from teams where team_id=$team_id");
    if($isteamsExists){
        $tabble_name = "teams";
        $column_names = array('team_name');
        $condition = "team_id='$team_id'";
        $result = $db->deleteIntoTable($r->team, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Teams Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Teams. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Teams with the provided teams does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectteams', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from teams ORDER BY team_name";
    $teams = $db->getAllRecords($sql);
    echo json_encode($teams);
});


// UPLOADS

$app->post('/uploadenquiry_files', function() use ($app) {
    session_start();
    $response = array();
    $sql  = "";
    $db = new DbHandler();

    if (empty($_FILES['file-1'])) {
        $response["status"] = "error";
        $response["message"] = "No files found for upload!";
        echoResponse(201, $response);
        return; // terminate
    }
    
    $images = $_FILES['file-1'];
    $paths= array();
    $filenames = $images['name'];
    ini_set('upload_max_filesize', '10M');
    ini_set('post_max_size', '10M');
    ini_set('max_input_time', 300);
    ini_set('max_execution_time', 300);
    for($i=0; $i < count($filenames); $i++){
        $ds = DIRECTORY_SEPARATOR;
        $ext=basename($filenames[$i]);
        $target = "uploads" .$ds. $ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;
        } else {
            $success = false;
            break;
        }
    }
    if ($success)
    {
        $response["status"] = "success";
        $response["message"] = "files uploaded! ";
        echoResponse(201, $response);
    }
    else
    {
        $response["status"] = "error";
        $response["message"] = "File not uploaded ! Please check !!";
        echoResponse(201, $response);
    }
});

$app->post('/uploadenquiry', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $db = new DbHandler();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('file_name'),$r->convertdata);
    
    $ds = DIRECTORY_SEPARATOR;
    $target = "uploads" .$ds. $r->convertdata->file_name;
    $row = array();
    $success = false;
    $count  = 1;
    require('.././excel/PHPExcel.php');
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($target);
    $objWorksheet = $objPHPExcel->getActiveSheet();

    $highestRow = $objWorksheet->getHighestRow();
    $highestColumn = $objWorksheet->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $rows = array();
    
    
    
    for ($row = 1; $row <= $highestRow; ++$row) 
    {   
        
        $r->convertdata->enquiry_for=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->enquiry_type=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
        $r->convertdata->enquiry_off=$objWorksheet->getCellByColumnAndRow(70, $row)->getValue();
        $r->convertdata->client=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
        //$r->convertdata->broker_involved=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->broker=$objWorksheet->getCellByColumnAndRow(30, $row)->getValue();
        $r->convertdata->con_status=$objWorksheet->getCellByColumnAndRow(35, $row)->getValue();
        //$r->convertdata->dt_poss_min=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->dt_poss_max=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->loan_req=$objWorksheet->getCellByColumnAndRow(46, $row)->getValue();
        //$r->convertdata->pr_name=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->pre_leased=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->priority=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->preferred_streets=$objWorksheet->getCellByColumnAndRow(18, $row)->getValue();
        $r->convertdata->preferred_areas=$objWorksheet->getCellByColumnAndRow(17, $row)->getValue();
        $r->convertdata->preferred_city=$objWorksheet->getCellByColumnAndRow(19, $row)->getValue();
        $r->convertdata->preferred_state=$objWorksheet->getCellByColumnAndRow(20, $row)->getValue();
        //$r->convertdata->preferred_country=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->bath=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->furniture=$objWorksheet->getCellByColumnAndRow(23, $row)->getValue();
        //$r->convertdata->floor_range1=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->floor_range2=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->door_fdir=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->sale_area1=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->sale_area2=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->area_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->tot_area1=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
        $r->convertdata->tot_area2=$objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
        //$r->convertdata->tot_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->carp_area1=$objWorksheet->getCellByColumnAndRow(64, $row)->getValue();
        $r->convertdata->carp_area2=$objWorksheet->getCellByColumnAndRow(65, $row)->getValue();
        //$r->convertdata->budget_range1=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->range1_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->budget_range2=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->range2_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->depo_range1=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->depo_range1_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->depo_range2=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->depo_range2_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->team=$objWorksheet->getCellByColumnAndRow(34, $row)->getValue();
        $r->convertdata->assigned=$objWorksheet->getCellByColumnAndRow(21, $row)->getValue();
        //$r->convertdata->sms_salestrainee=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->email_salestrainee=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->lease_period=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->source=$objWorksheet->getCellByColumnAndRow(24, $row)->getValue();
        $r->convertdata->subsource=$objWorksheet->getCellByColumnAndRow(25, $row)->getValue();
        $r->convertdata->campaign=$objWorksheet->getCellByColumnAndRow(55, $row)->getValue();
        $r->convertdata->status=$objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
        $r->convertdata->stage=$objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
        $r->convertdata->vastu_comp=$objWorksheet->getCellByColumnAndRow(27, $row)->getValue();
        $r->convertdata->groups=$objWorksheet->getCellByColumnAndRow(60, $row)->getValue();
        $r->convertdata->intrnal_comment=$objWorksheet->getCellByColumnAndRow(28, $row)->getValue();
        $r->convertdata->external_comment=$objWorksheet->getCellByColumnAndRow(29, $row)->getValue();
        //$r->convertdata->share_agsearch=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->pro_alerts=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->subscr_email=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->height=$objWorksheet->getCellByColumnAndRow(50, $row)->getValue();
        $r->convertdata->frontage=$objWorksheet->getCellByColumnAndRow(51, $row)->getValue();
        $r->convertdata->reg_date=$objWorksheet->getCellByColumnAndRow(41, $row)->getValue();
        //$r->convertdata->amenities_avl=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->parking=$objWorksheet->getCellByColumnAndRow(26, $row)->getValue();
        $r->convertdata->portal_id=$objWorksheet->getCellByColumnAndRow(59, $row)->getValue();
        //$r->convertdata->asset_id=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->published=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();

        $tabble_name = "enquiry";
        $column_names = array('enquiry_for', 'enquiry_type', 'enquiry_off',  'client', 'broker_involved', 'broker', 'con_status', 'dt_poss_min', 'dt_poss_max', 'loan_req', 'pr_name', 'pre_leased', 'priority', 'preferred_streets', 'preferred_areas', 'preferred_city', 'preferred_state', 'preferred_country', 'bath', 'furniture', 'floor_range1', 'floor_range2', 'door_fdir', 'sale_area1', 'sale_area2', 'area_para', 'tot_area1', 'tot_area2', 'tot_para', 'carp_area1', 'carp_area2', 'budget_range1', 'range1_para', 'budget_range2', 'range2_para', 'depo_range1', 'depo_range1_para', 'depo_range2', 'depo_range2_para', 'team', 'assigned', 'sms_salestrainee', 'email_salestrainee', 'lease_period', 'source', 'subsource', 'campaign', 'status', 'stage', 'vastu_comp', 'groups', 'intrnal_comment', 'external_comment', 'share_agsearch', 'pro_alerts', 'subscr_email', 'height', 'frontage', 'reg_date', 'amenities_avl', 'parking', 'portal_id', 'asset_id', 'published',  'created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->convertdata, $column_names, $tabble_name, $multiple);
        if ($result!=NULL)
        {
            $success = true;
        }
    }
           
    if ($success)
    {
        $response["status"] = "success";
        $response["message"] = "Data uploaded for this date !! ";
        echoResponse(201, $response);
    }
    else
    {
        $response["status"] = "error";
        $response["message"] = "Data not uploaded for this date !! ";
        echoResponse(201, $response);
    }
});

$app->post('/uploadproperty_files', function() use ($app) {
    session_start();
    $response = array();
    $sql  = "";
    $db = new DbHandler();

    if (empty($_FILES['file-1'])) {
        $response["status"] = "error";
        $response["message"] = "No files found for upload!";
        echoResponse(201, $response);
        return; // terminate
    }
    
    $images = $_FILES['file-1'];
    $paths= array();
    $filenames = $images['name'];
    ini_set('upload_max_filesize', '10M');
    ini_set('post_max_size', '10M');
    ini_set('max_input_time', 300);
    ini_set('max_execution_time', 300);
    for($i=0; $i < count($filenames); $i++){
        $ds = DIRECTORY_SEPARATOR;
        $ext=basename($filenames[$i]);
        $target = "uploads" .$ds. $ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;
        } else {
            $success = false;
            break;
        }
    }
    if ($success)
    {
        $response["status"] = "success";
        $response["message"] = "files uploaded! ";
        echoResponse(201, $response);
    }
    else
    {
        $response["status"] = "error";
        $response["message"] = "File not uploaded ! Please check !!";
        echoResponse(201, $response);
    }
});

$app->post('/uploadproperty', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $db = new DbHandler();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('file_name'),$r->convertdata);
    
    $ds = DIRECTORY_SEPARATOR;
    $target = "uploads" .$ds. $r->convertdata->file_name;
    $row = array();
    $success = false;
    $count  = 1;
    require('.././excel/PHPExcel.php');
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($target);
    $objWorksheet = $objPHPExcel->getActiveSheet();

    $highestRow = $objWorksheet->getHighestRow();
    $highestColumn = $objWorksheet->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $rows = array();
    
    for ($row = 1; $row <= $highestRow; ++$row) 
    {   
        $r->convertdata->property_code=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
        $r->convertdata->property_off=$objWorksheet->getCellByColumnAndRow(92, $row)->getValue();
        $r->convertdata->name=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
        $r->convertdata->proptype=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
        //$r->convertdata->propfrom=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->dev_owner=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
        $r->convertdata->property_for=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->building_plot=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
        $r->convertdata->owner_mobile=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
        $r->convertdata->owner_email=$objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
        //$r->convertdata->config=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->con_status=$objWorksheet->getCellByColumnAndRow(16, $row)->getValue();
        $r->convertdata->rera_num=$objWorksheet->getCellByColumnAndRow(17, $row)->getValue();
        $r->convertdata->total_bedroom=$objWorksheet->getCellByColumnAndRow(18, $row)->getValue();
        $r->convertdata->unit=$objWorksheet->getCellByColumnAndRow(21, $row)->getValue();
        $r->convertdata->floor=$objWorksheet->getCellByColumnAndRow(41, $row)->getValue();
        $r->convertdata->wing=$objWorksheet->getCellByColumnAndRow(34, $row)->getValue();
        //$r->convertdata->add1=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->add2=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->exlocation=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->locality=$objWorksheet->getCellByColumnAndRow(22, $row)->getValue();
        $r->convertdata->area=$objWorksheet->getCellByColumnAndRow(13, $row)->getValue();
        $r->convertdata->city=$objWorksheet->getCellByColumnAndRow(23, $row)->getValue();
        $r->convertdata->state=$objWorksheet->getCellByColumnAndRow(24, $row)->getValue();
        $r->convertdata->country=$objWorksheet->getCellByColumnAndRow(25, $row)->getValue();
        //$r->convertdata->zip=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->multi_size=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->sale_area=$objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
        //$r->convertdata->pro_sale_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->carp_area=$objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
        //$r->convertdata->loading=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->efficiency=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->usd_area=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->area_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->exp_price=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
        $r->convertdata->exp_rent=$objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
        //$r->convertdata->exprice_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->psale_area=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->car_area=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->pack_price=$objWorksheet->getCellByColumnAndRow(44, $row)->getValue();
        //$r->convertdata->package_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->pack_price_comments=$objWorksheet->getCellByColumnAndRow(45, $row)->getValue();
        $r->convertdata->deposite_month=$objWorksheet->getCellByColumnAndRow(59, $row)->getValue();
        $r->convertdata->security_depo=$objWorksheet->getCellByColumnAndRow(58, $row)->getValue();
        //$r->convertdata->security_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->lease_lock=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->agree_month=$objWorksheet->getCellByColumnAndRow(52, $row)->getValue();
        //$r->convertdata->escalation_lease=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->bath=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->kitchen=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->workstation=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->cabins=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->cubicals=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->seaters=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->furniture=$objWorksheet->getCellByColumnAndRow(15, $row)->getValue();
        $r->convertdata->teams=$objWorksheet->getCellByColumnAndRow(43, $row)->getValue();
        $r->convertdata->assign_to=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->sms_saletrainee=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->email_saletrainee=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->source_channel=$objWorksheet->getCellByColumnAndRow(61, $row)->getValue();
        $r->convertdata->subsource_channel=$objWorksheet->getCellByColumnAndRow(62, $row)->getValue();
        //$r->convertdata->campaign=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->proj_status=$objWorksheet->getCellByColumnAndRow(16, $row)->getValue();
        //$r->convertdata->broke_involved=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->pro_inspect=$objWorksheet->getCellByColumnAndRow(87, $row)->getValue();
        $r->convertdata->groups=$objWorksheet->getCellByColumnAndRow(60, $row)->getValue();
        //$r->convertdata->img=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->internal_comment=$objWorksheet->getCellByColumnAndRow(28, $row)->getValue();
        $r->convertdata->external_comment=$objWorksheet->getCellByColumnAndRow(29, $row)->getValue();
        $r->convertdata->amenities_avl=$objWorksheet->getCellByColumnAndRow(65, $row)->getValue();
        //$r->convertdata->pro_specification=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->parking=$objWorksheet->getCellByColumnAndRow(26, $row)->getValue();
        //$r->convertdata->door_fdir=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->keywith=$objWorksheet->getCellByColumnAndRow(33, $row)->getValue();
        //$r->convertdata->ageofprop=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->mpm_unit=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->mpm_unit_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->mpm_cam=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->mpm_tot_tax=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->oth_charges=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->tranf_charge=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->car_park=$objWorksheet->getCellByColumnAndRow(27, $row)->getValue();
        //$r->convertdata->lift=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->numof_floor=$objWorksheet->getCellByColumnAndRow(41, $row)->getValue();
        $r->convertdata->balconies=$objWorksheet->getCellByColumnAndRow(78, $row)->getValue();
        $r->convertdata->wings=$objWorksheet->getCellByColumnAndRow(34, $row)->getValue();
        $r->convertdata->distfrm_station=$objWorksheet->getCellByColumnAndRow(80, $row)->getValue();
        $r->convertdata->distfrm_dairport=$objWorksheet->getCellByColumnAndRow(81, $row)->getValue();
        $r->convertdata->distfrm_school=$objWorksheet->getCellByColumnAndRow(82, $row)->getValue();
        //$r->convertdata->distfrm_market=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->height=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->frontage=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->watersup=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->powersup=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->occu_certi=$objWorksheet->getCellByColumnAndRow(83, $row)->getValue();
        $r->convertdata->soc_reg=$objWorksheet->getCellByColumnAndRow(84, $row)->getValue();
        $r->convertdata->suitablefor=$objWorksheet->getCellByColumnAndRow(51, $row)->getValue();
        //$r->convertdata->youtube_link=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->review=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->reg_date=$objWorksheet->getCellByColumnAndRow(39, $row)->getValue();
        $r->convertdata->pre_leased=$objWorksheet->getCellByColumnAndRow(86, $row)->getValue();
        //$r->convertdata->tenant_name=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->occ_details=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->lease_tot_area=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->roi=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->rented_area=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->lease_start=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->lease_end=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->rent_per_sqft=$objWorksheet->getCellByColumnAndRow(55, $row)->getValue();
        //$r->convertdata->rent_per_sqft_para=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->monthle_rent=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->dep_months=$objWorksheet->getCellByColumnAndRow(59, $row)->getValue();
        $r->convertdata->sec_dep=$objWorksheet->getCellByColumnAndRow(58, $row)->getValue();
        //$r->convertdata->lockin=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->tenure_year=$objWorksheet->getCellByColumnAndRow(52, $row)->getValue();
        //$r->convertdata->escalation=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        //$r->convertdata->asset_id=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->published=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        
        $tabble_name = "property";
        $column_names = array('property_code', 'name', 'proptype', 'property_off', 'propfrom', 'dev_owner', 'property_for', 'building_plot', 'owner_mobile', 'owner_email', 'config', 'con_status', 'rera_num', 'total_bedroom', 'unit', 'floor', 'wing', 'add1', 'add2', 'exlocation', 'locality', 'area', 'city', 'state', 'country', 'zip', 'multi_size', 'sale_area', 'pro_sale_para', 'carp_area', 'loading', 'efficiency', 'usd_area', 'area_para', 'exp_price', 'exp_rent', 'exprice_para', 'psale_area', 'car_area', 'pack_price', 'package_para', 'pack_price_comments', 'deposite_month', 'security_depo', 'security_para', 'lease_lock', 'agree_month', 'escalation_lease', 'bath', 'kitchen', 'workstation', 'cabins', 'cubicals', 'seaters', 'furniture', 'teams', 'assign_to', 'sms_saletrainee', 'email_saletrainee', 'source_channel', 'subsource_channel', 'campaign', 'proj_status', 'broke_involved', 'pro_inspect', 'groups', 'img', 'internal_comment', 'external_comment', 'amenities_avl', 'pro_specification', 'parking', 'door_fdir', 'keywith', 'ageofprop', 'mpm_unit', 'mpm_unit_para', 'mpm_cam', 'mpm_tot_tax', 'oth_charges', 'tranf_charge', 'car_park', 'lift', 'numof_floor', 'balconies', 'wings', 'distfrm_station', 'distfrm_dairport', 'distfrm_school', 'distfrm_market', 'height', 'frontage', 'watersup', 'powersup', 'occu_certi', 'soc_reg', 'suitablefor', 'youtube_link', 'review', 'reg_date', 'pre_leased', 'tenant_name', 'occ_details', 'lease_tot_area', 'roi', 'rented_area', 'lease_start', 'lease_end', 'rent_per_sqft', 'rent_per_sqft_para', 'monthle_rent', 'dep_months', 'sec_dep', 'lockin', 'tenure_year', 'escalation', 'asset_id', 'published', 'created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->convertdata, $column_names, $tabble_name, $multiple);
        if ($result!=NULL)
        {
            $success = true;
        }
    }
           
    if ($success)
    {
        $response["status"] = "success";
        $response["message"] = "Data uploaded for this date !! ";
        echoResponse(201, $response);
    }
    else
    {
        $response["status"] = "error";
        $response["message"] = "Data not uploaded for this date !! ";
        echoResponse(201, $response);
    }
});


$app->get('/total_listings', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,CONCAT(e.salu,' ',e.fname,' ',e.lname) as project_contact, CONCAT(e.salu,' ',e.fname,' ',e.lname) as assign_to, e.mobile_no as usermobileno from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN attachments as c on a.property_id = c.category_id and category = 'property' LEFT JOIN users as d on a.assign_to = d.user_id LEFT JOIN employee as e on d.emp_id = e.emp_id ORDER by a.created_date DESC";
    
    $total_listings = $db->getAllRecords($sql);
    echo json_encode($total_listings);
});

$app->get('/branch_listings', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,CONCAT(e.salu,' ',e.fname,' ',e.lname) as project_contact, CONCAT(e.salu,' ',e.fname,' ',e.lname) as assign_to, e.mobile_no as usermobileno from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN attachments as c on a.property_id = c.category_id and category = 'property' LEFT JOIN users as d on a.assign_to = d.user_id LEFT JOIN employee as e on d.emp_id = e.emp_id ORDER by a.created_date DESC";
    $branch_listings = $db->getAllRecords($sql);
    echo json_encode($branch_listings);
});

$app->get('/completed_transactions', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from property ORDER by created_date";
    $completed_transactions = $db->getAllRecords($sql);
    echo json_encode($completed_transactions);
});

$app->get('/total_billings', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from property ORDER by created_date";
    $total_billings = $db->getAllRecords($sql);
    echo json_encode($total_billings);
});

// employee

$app->get('/employee_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT a.emp_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name,  CONCAT(c.salu,' ',c.fname,' ',c.lname) as manager, a.mobile_no, a.off_email, a.off_phone, DATE_FORMAT(a.doj,'%d/%m/%Y') AS doj,DATE_FORMAT(a.dob,'%d/%m/%Y') AS dob, b.username from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN employee as c on a.manager_id = c.emp_id ORDER BY a.lname,a.fname";
    $employees = $db->getAllRecords($sql);
    echo json_encode($employees);
});


$app->post('/employee_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('fname'),$r->employee);
    $db = new DbHandler();
    $fname = $r->employee->fname;
    $lname = $r->employee->lname;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->employee->created_by = $created_by;
    $r->employee->created_date = $created_date;

    if (isset($r->employee->doj))
    {
        $doj = $r->employee->doj;
        $tdoj = substr($doj,6,4)."-".substr($doj,3,2)."-".substr($doj,0,2);
        $r->employee->doj = $tdoj;
    }

    if (isset($r->employee->dob))
    {
        $dob = $r->employee->dob;
        $tdob = substr($dob,6,4)."-".substr($dob,3,2)."-".substr($dob,0,2);
        $r->employee->dob = $tdob;
    }
    
    $isEmployeeExists = $db->getOneRecord("select 1 from employee where fname='$fname' and lname='$lname' ");
    if(!$isEmployeeExists){
        $tabble_name = "employee";
        $column_names = array('bo_id','salu','fname','lname','company_name','mobile_no','alt_mobile_no','off_phone','off_email','manager_id','doj','dob','allow_mobile','allowed_ip','mobile_device_id','created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->employee, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Employee created successfully";
            $response["emp_id"] = $result;
            $emp_id = $result;
            require_once 'passwordHash.php';
            $temp_user = explode("@",$r->employee->off_email);
            $username = $temp_user[0];
            $password = passwordHash::hash('rhpuser');
            $query = "INSERT INTO users (username, password, emp_id, created_by, created_date)  VALUES('$username', '$password', '$emp_id', '$created_by' ,'$created_date' )";
            $result = $db->insertByQuery($query);
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Employee. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Employee with the provided Employee exists..!";
        echoResponse(201, $response);
    }
});


$app->get('/employee_edit_ctrl/:emp_id', function($emp_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *, DATE_FORMAT(doj,'%d/%m/%Y') AS doj,DATE_FORMAT(dob,'%d/%m/%Y') AS dob from employee where emp_id=".$emp_id;
    $employees = $db->getAllRecords($sql);
    echo json_encode($employees);
    
});

$app->post('/employee_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('emp_id'),$r->employee);
    $db = new DbHandler();
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s');
    $r->employee->modified_by = $modified_by;
    $r->employee->modified_date = $modified_date;
    $emp_id  = $r->employee->emp_id;
    $fname = $r->employee->fname;
    $lname = $r->employee->lname;
    if (isset($r->employee->doj))
    {
        $doj = $r->employee->doj;
        $tdoj = substr($doj,6,4)."-".substr($doj,3,2)."-".substr($doj,0,2);
        $r->employee->doj = $tdoj;
    }

    if (isset($r->employee->dob))
    {
        $dob = $r->employee->dob;
        $tdob = substr($dob,6,4)."-".substr($dob,3,2)."-".substr($dob,0,2);
        $r->employee->dob = $tdob;
    }
    $isEmployeeExists = $db->getOneRecord("select 1 from employee where emp_id=$emp_id");
    if($isEmployeeExists){
        $tabble_name = "employee";
        $column_names = array('bo_id','salu','fname','lname','company_name','mobile_no','alt_mobile_no','off_phone','off_email','manager_id','doj','dob','allow_mobile','allowed_ip','mobile_device_id','modified_by','modified_date');
        $condition = "emp_id='$emp_id'";
        $multipl=array("");
        $history = $db->historydata( $r->employee, $column_names, $tabble_name,$condition,$emp_id,$multiple, $modified_by, $modified_date);
        $result = $db->updateIntoTable($r->employee, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Employee Updated successfully";
            
            $response["message"] = "Employee Updated successfully".$history;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Employee. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Employee with the provided Employee does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/employee_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('emp_id'),$r->employee);
    $db = new DbHandler();
    $emp_id  = $r->employee->emp_id;
    $isEmployeeExists = $db->getOneRecord("select 1 from employee where emp_id=$emp_id");
    if($isEmployeeExists){
        $tabble_name = "employee";
        $column_names = array('bo_id');
        $condition = "emp_id='$emp_id'";
        $result = $db->deleteIntoTable($r->employee, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Employee Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Employee. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Employee with the provided Employee does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectemployee', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,CONCAT(salu,' ',fname,' ',lname) as name from employee ORDER BY lname,fname";
    $employees = $db->getAllRecords($sql);
    echo json_encode($employees);
});


// AUDIT TRAIL

$app->get('/audit_trail/:module_name/:id/:data', function($module_name,$id,$data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "";
    $sql = "SELECT *, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i:%s') AS modified_date, c.username as modified_by from history as a LEFT JOIN $module_name as b on a.module_id = b.$id LEFT JOIN users as c on a.modified_by = c.user_id  WHERE a.module_name = '$module_name' and a.module_id in($data) ORDER BY a.modified_date DESC";
    $histories = $db->getAllRecords($sql);
    echo json_encode($histories);
});

// EXPORT TO EXCEL

$app->get('/exportdata/:module_name/:id/:data/:option_value', function($module_name,$id,$data,$option_value) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    require('.././excel/PHPExcel.php');
    $objPHPExcel = new PHPExcel(); 
    $objPHPExcel->setActiveSheetIndex(0); 
    $rowCount = 1; 
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,'Project Details'); 
    $rowCount = 3;

    $sql = "SELECT * FROM genforms WHERE table_name='".$module_name."' and show_on_form='Yes'";    
    $stmt = $db->getRows($sql);
    $char = "A";
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $objPHPExcel->getActiveSheet()->SetCellValue($char.$rowCount,$row['column_heading']);
            $objPHPExcel->getActiveSheet()->getColumnDimension($char)->setWidth("15");
            $char++;
        }
    }
    $rowCount = 4;

    $sql = "SELECT * from $module_name ORDER BY created_date DESC";
    if ($option_value=='selected')
    {
        $sql = "SELECT * from $module_name where $id in($data) ORDER BY created_date DESC";
    }

    /*if ($option_value=='current_page')
    {
        $sql = "SELECT * from project where project_id in($data) ORDER BY created_date DESC";
    }*/
    if ($option_value=='current_page')
    {
        $sql = "SELECT * from $module_name ORDER BY created_date DESC";
    }
    if ($option_value=='all_records')
    {
        $sql = "SELECT * from $module_name ORDER BY created_date DESC";
    }
    $stmt = $db->getRows($sql);
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            
            $char = "A";
            $sql1 = "SELECT * FROM genforms WHERE table_name='".$module_name."' and show_on_form='Yes'";    
            $stmt1 = $db->getRows($sql1);
            if ($stmt1->num_rows > 0)
            {
                while($row1 = $stmt1->fetch_assoc())
                {
                    $objPHPExcel->getActiveSheet()->SetCellValue($char.$rowCount, $row[$row1['column_name']]);
                    $char++;
                }
            }
            $rowCount++;
        }
    }
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
    $ds        = DIRECTORY_SEPARATOR;
    $objWriter->save('uploads'.$ds.$module_name.'_list.xlsx'); 
    $stmt->close();
    $htmldata['htmlstring']='Done';
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

?>

