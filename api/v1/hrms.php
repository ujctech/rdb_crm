<?php

// ELIGIBILITY

$app->get('/eligibility_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from eligibility as a LEFT JOIN designation as b ON a.designation_id = b.designation_id ORDER BY a.team,b.designation,a.eligibility_title";
    $eligibilities = $db->getAllRecords($sql);
    echo json_encode($eligibilities);
});

$app->post('/eligibility_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('eligibility_title'),$r->eligibility);
    $db = new DbHandler();

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->eligibility->created_by = $created_by;
    $r->eligibility->created_date = $created_date;
    
    $eligibility_title = $r->eligibility->eligibility_title;
    $iseligibilityExists = $db->getOneRecord("select 1 from eligibility where eligibility_title='$eligibility_title' ");
    if(!$iseligibilityExists){
        $tabble_name = "eligibility";
        $column_names = array('eligibility_title', 'designation_id', 'team',  'incentives', 'other_benefits', 'para_hike','created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->eligibility, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Eligibility added successfully";
            $response["eligibility_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Upload Eligibility. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Eligibility of this Eligibility already exists!";
        echoResponse(201, $response);
    }
});

$app->get('/eligibility_edit_ctrl/:eligibility_id', function($eligibility_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from eligibility where eligibility_id = $eligibility_id  ORDER BY created_date DESC";
    $eligibilities = $db->getAllRecords($sql);
    echo json_encode($eligibilities);
    
});

$app->post('/eligibility_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('eligibility_id'),$r->eligibility);
    $db = new DbHandler();
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->eligibility->modified_by = $modified_by;
    $r->eligibility->modified_date = $modified_date;

    $eligibility_id  = $r->eligibility->eligibility_id;
    $iseligibilityExists = $db->getOneRecord("select 1 from eligibility where eligibility_id=$eligibility_id");
    if($iseligibilityExists){
        $tabble_name = "eligibility";
        $column_names = array('eligibility_title', 'designation_id', 'team', 'incentives', 'other_benefits', 'para_hike','modified_by','modified_date');
        $condition = "eligibility_id='$eligibility_id'";
        $result = $db->updateIntoTable($r->eligibility, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Eligibility Updated successfully";
            $_SESSION['tmpeligibility_id'] = $eligibility_id;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Eligibility. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Eligibility with the provided Eligibility Id does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/eligibility_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('eligibility_id'),$r->eligibility);
    $db = new DbHandler();
    $eligibility_id  = $r->eligibility->eligibility_id;
    $iseligibilityExists = $db->getOneRecord("select 1 from eligibility where eligibility_id=$eligibility_id");
    if($iseligibilityExists){
        $tabble_name = "eligibility";
        $column_names = array('eligibility_title', 'designation_id','team',  'incentives', 'other_benefits', 'para_hike');
        $condition = "eligibility_id='$eligibility_id'";
        $result = $db->deleteIntoTable($r->eligibility, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Eligibility Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Eligibility. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Eligibility with the provided Eligibility id does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selecteligibility', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from eligibility ORDER BY eligibility_title";
    $eligibilities = $db->getAllRecords($sql);
    echo json_encode($eligibilities);
});



?>
