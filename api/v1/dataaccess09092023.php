<?php  

$app->get('/getfromstate/:state_id', function($state_id) use ($app) { 
    $sql  = "";
    $db = new DbHandler();  
    $session = $db->getSession(); 
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT (SELECT GROUP_CONCAT(CONCAT(f.country,'-',f.country_code), ',') FROM country as f where FIND_IN_SET(a.state_id , f.country_id)) as country from state as a LEFT JOIN country as b ON b.country_id = a.country_id WHERE a.state_id = $state_id ";
    $getstates = $db->getAllRecords($sql);
    echo json_encode($getstates);
});

$app->get('/getfromcity/:city_id', function($city_id) use ($app) {  
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT (SELECT GROUP_CONCAT(CONCAT(f.country,'-',f.country_code), ',') FROM country as f where FIND_IN_SET(d.country_id , f.country_id)) as country, (SELECT GROUP_CONCAT(g.state, ',') FROM state as g where FIND_IN_SET(b.state_id , g.state_id)) as state, (SELECT GROUP_CONCAT(h.city, ',') FROM city as h where FIND_IN_SET(b.city_id , h.city_id)) as city from city as b LEFT JOIN state as c ON b.state_id = c.state_id LEFT JOIN country as d ON c.country_id = d.country_id WHERE b.city_id in ($city_id) ";
    $getcities = $db->getAllRecords($sql);
    echo json_encode($getcities);
});

$app->get('/getfromarea/:area_id', function($area_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT GROUP_CONCAT(city,',') as city , GROUP_CONCAT(c.state,',') as state , GROUP_CONCAT(d.country,',') as country from areas as a LEFT JOIN city as b on a.city_id = b.city_id LEFT JOIN state as c ON b.state_id = c.state_id LEFT JOIN country as d ON c.country_id = d.country_id WHERE a.area_id in($area_id) ";
    $getareas = $db->getAllRecords($sql);
    echo json_encode($getareas);
});


$app->get('/getfromlocality/:locality_id', function($locality_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT b.area_id, c.city, d.state, CONCAT(e.country,'-',e.country_code)  as country  from locality as a LEFT JOIN areas as b ON a.area_id = b.area_id LEFT JOIN city as c ON b.city_id = c.city_id LEFT JOIN state as d ON c.state_id = d.state_id LEFT JOIN country as e ON d.country_id = e.country_id WHERE a.locality_id in($locality_id)";
    $getlocalities = $db->getAllRecords($sql);
    echo json_encode($getlocalities);
});


// MAIL TEMPLATE

$app->get('/mail_template_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from mail_template ORDER BY template_title";
    $mail_templates = $db->getAllRecords($sql);
    echo json_encode($mail_templates);
});


$app->post('/mail_template_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('template_title'),$r->mail_template);
    $db = new DbHandler();

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->mail_template->created_by = $created_by;
    $r->mail_template->created_date = $created_date;

    $template_title = $r->mail_template->template_title;
    $ismail_templateExists = $db->getOneRecord("select 1 from mail_template where template_title='$template_title' ");
    if(!$ismail_templateExists){
        $tabble_name = "mail_template";
        $column_names = array('module_name', 'template_title', 'subject', 'text_message', 'footer_note','sequence_number','created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->mail_template, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Mail Template created successfully";
            $response["mail_template_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Mail Template. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Mail Template with the provided Mail Template exists!";
        echoResponse(201, $response);
    }
});


$app->get('/mail_template_edit_ctrl/:mail_template_id', function($mail_template_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from mail_template where mail_template_id=".$mail_template_id;
    $mail_templates = $db->getAllRecords($sql);
    echo json_encode($mail_templates);
    
});

$app->post('/mail_template_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('mail_template_id'),$r->mail_template);
    $db = new DbHandler();
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->mail_template->modified_by = $modified_by;
    $r->mail_template->modified_date = $modified_date;

    $mail_template_id  = $r->mail_template->mail_template_id;
    $ismail_templateExists = $db->getOneRecord("select 1 from mail_template where mail_template_id=$mail_template_id");
    if($ismail_templateExists){
        $tabble_name = "mail_template";
        $column_names = array('module_name','template_title', 'subject', 'text_message', 'footer_note','sequence_number','modified_by','modified_date');
        $condition = "mail_template_id='$mail_template_id'";
        $result = $db->updateIntoTable($r->mail_template, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Mail Template Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Mail Template. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Mail Template with the provided Mail Template does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/mail_template_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('mail_template_id'),$r->mail_template);
    $db = new DbHandler();
    $mail_template_id  = $r->mail_template->mail_template_id;
    $ismail_templateExists = $db->getOneRecord("select 1 from mail_template where mail_template_id=$mail_template_id");
    if($ismail_templateExists){
        $tabble_name = "mail_template";
        $column_names = array('template_title', 'subject', 'text_message', 'footer_note');
        $condition = "mail_template_id='$mail_template_id'";
        $result = $db->deleteIntoTable($r->mail_template, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Mail Template Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Mail Template. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Mail Template with the provided Mail Template does not exists!";
        echoResponse(201, $response);
    }
});




// RESUME

$app->get('/resume_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from resume as a LEFT JOIN designation as b ON a.designation_id = b.designation_id ORDER BY a.employee_name";
    $resumes = $db->getAllRecords($sql);
    echo json_encode($resumes);
});


$app->post('/resume_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('employee_name'),$r->resume);
    $db = new DbHandler();

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->resume->created_by = $created_by;
    $r->resume->created_date = $created_date;

    $employee_name = $r->resume->employee_name;
    $isresumeExists = $db->getOneRecord("select 1 from resume where employee_name='$employee_name' ");
    if(!$isresumeExists){
        $tabble_name = "resume";
        $column_names = array('employee_name', 'designation_id', 'mobile_no', 'email', 'remarks', 'filename','status','created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->resume, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Resume uploaded successfully";
            $response["resume_id"] = $result;
            $_SESSION['tmpresume_id'] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Upload Resume. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Resume of this employee already exists!";
        echoResponse(201, $response);
    }
});

$app->post('/resume_uploads', function() use ($app) {
    session_start();
    $resume_id = $_SESSION['tmpresume_id'];
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
        $file_names = strtolower(pathinfo($filenames[$i], PATHINFO_BASENAME));
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "resumes". $ds. $file_names;

        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $sql = "UPDATE resume set filename = '$file_names' where resume_id = $resume_id " ;  
            $result = $db->updateByQuery($sql);
            $count ++;
        } 
        else 
        {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
});


$app->get('/resume_edit_ctrl/:resume_id', function($resume_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from resume where resume_id = $resume_id  ORDER BY created_date DESC";
    $resumes = $db->getAllRecords($sql);
    echo json_encode($resumes);
    
});

$app->post('/resume_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('resume_id'),$r->resume);
    $db = new DbHandler();
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->resume->modified_by = $modified_by;
    $r->resume->modified_date = $modified_date;

    $resume_id  = $r->resume->resume_id;
    $isresumeExists = $db->getOneRecord("select 1 from resume where resume_id=$resume_id");
    if($isresumeExists){
        $tabble_name = "resume";
        $column_names = array('employee_name', 'designation_id', 'mobile_no', 'email', 'remarks', 'filename','status','modified_by','modified_date');
        $condition = "resume_id='$resume_id'";
        $result = $db->updateIntoTable($r->resume, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Resume Updated successfully";
            $_SESSION['tmpresume_id'] = $resume_id;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Resume. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Resume with the provided Resume Id does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/resume_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('resume_id'),$r->resume);
    $db = new DbHandler();
    $resume_id  = $r->resume->resume_id;
    $isresumeExists = $db->getOneRecord("select 1 from resume where resume_id=$resume_id");
    if($isresumeExists){
        $tabble_name = "resume";
        $column_names = array('employee_name', 'mobile_no', 'email', 'remarks', 'filename','status');
        $condition = "resume_id='$resume_id'";
        $result = $db->deleteIntoTable($r->resume, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Resume Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Resume. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Resume with the provided Resume id does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectresume', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from resume ORDER BY employee_name";
    $resumes = $db->getAllRecords($sql);
    echo json_encode($resumes);
});


$app->get('/getdatavalues_resumes/:field_name', function($field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM resume ORDER BY $field_name";
    if ($field_name == 'designation_id')
    {
        $sql = "SELECT designation_id, designation FROM designation ORDER BY designation";
    }
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});

$app->post('/search_resumes', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());

    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];

    $role = $session['role'];

    $searchsql = "select * from resume limit 0";
    
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    { 
        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM resume as a LEFT JOIN designation as b ON a.designation_id = b.designation_id WHERE a.resume_id > 0 ";

    }
    else if (in_array("Branch Head", $role))
    {
        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM resume as a LEFT JOIN designation as b ON a.designation_id = b.designation_id WHERE a.resume_id > 0 ";

    }
    else
    { 

        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM resume as a LEFT JOIN designation as b ON a.designation_id = b.designation_id WHERE a.resume_id > 0 ";


    }     

    if (isset($r->searchdata->designation_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->designation_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.designation_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->mobile_no))
    {
        $first = "Yes";
        $new_data = $r->searchdata->mobile_no;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.mobile_no LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->email))
    {
        $first = "Yes";
        $new_data = $r->searchdata->email;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.email LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->employee_name))
    {
        $first = "Yes";
        $new_data = $r->searchdata->employee_name;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.employee_name LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    
    $searchsql .=  $sql .  " ORDER BY a.employee_name ";

    $resumes = $db->getAllRecords($searchsql);

    //$resumes[0]['$searchsql']=$searchsql;

    echo json_encode($resumes);
});


// SMS TEMPLATE

$app->get('/sms_template_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from sms_template ORDER BY template_title";
    $sms_templates = $db->getAllRecords($sql);
    echo json_encode($sms_templates);
});


$app->post('/sms_template_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('template_title'),$r->sms_template);
    $db = new DbHandler();

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->sms_template->created_by = $created_by;
    $r->sms_template->created_date = $created_date;

    $template_title = $r->sms_template->template_title;
    $issms_templateExists = $db->getOneRecord("select 1 from sms_template where template_title='$template_title' ");
    if(!$issms_templateExists){
        $tabble_name = "sms_template";
        $column_names = array('module_name', 'template_title',  'text_message','sequence_number', 'created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->sms_template, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "SMS Template created successfully";
            $response["sms_template_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create sms Template. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A sms Template with the provided sms Template exists!";
        echoResponse(201, $response);
    }
});


$app->get('/sms_template_edit_ctrl/:sms_template_id', function($sms_template_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from sms_template where sms_template_id=".$sms_template_id;
    $sms_templates = $db->getAllRecords($sql);
    echo json_encode($sms_templates);
    
});

$app->post('/sms_template_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('sms_template_id'),$r->sms_template);
    $db = new DbHandler();
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->sms_template->modified_by = $modified_by;
    $r->sms_template->modified_date = $modified_date;

    $sms_template_id  = $r->sms_template->sms_template_id;
    $issms_templateExists = $db->getOneRecord("select 1 from sms_template where sms_template_id=$sms_template_id");
    if($issms_templateExists){
        $tabble_name = "sms_template";
        $column_names = array('module_name','template_title',  'text_message','sequence_number', 'modified_by','modified_date');
        $condition = "sms_template_id='$sms_template_id'";
        $result = $db->updateIntoTable($r->sms_template, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "SMS Template Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update sms Template. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "sms Template with the provided sms Template does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/sms_template_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('sms_template_id'),$r->sms_template);
    $db = new DbHandler();
    $sms_template_id  = $r->sms_template->sms_template_id;
    $issms_templateExists = $db->getOneRecord("select 1 from sms_template where sms_template_id=$sms_template_id");
    if($issms_templateExists){
        $tabble_name = "sms_template";
        $column_names = array('template_title', 'text_message', 'footer_note');
        $condition = "sms_template_id='$sms_template_id'";
        $result = $db->deleteIntoTable($r->sms_template, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "SMS Template Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete sms Template. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "sms Template with the provided sms Template does not exists!";
        echoResponse(201, $response);
    }
});



$app->get('/get_sms_template/:cat/:id/:sms_template_id', function($cat,$id,$sms_template_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    if ($cat=="property")
    {
        $sql = "SELECT * from property as a LEFT JOIN contact as b on a.dev_owner_id = b.contact_id WHERE a.property_id = $id LIMIT 1";
    }
    if ($cat=="enquiry")
    {
        $sql = "SELECT * from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id WHERE a.enquiry_id = $id LIMIT 1 ";
    }

    $data = $db->getAllRecords($sql);
    $id = 0;
    $owner_contact_details = "";
    $sms_sender = "Mr. Dhaval Thakkar:9819902226";
    $sms_sender_name = "Mr. Dhaval Thakkar:9819902226";
    $sender_mail_id = "dhaval@rdbrothers.com";
    $property_type = "";
    $no_matching_enquiry = "";
    $project_details = "";
    $rera_number = "";
    $no_matching_property = "";
    $text_message = "";

    if ($data)
    {
        if ($cat=="property")
        {
            $id = $data[0]['property_id'];
            $property_type = $data[0]['proptype'];
            
            $t = "";
            if ($data[0]['project_name'])
            {
                $t = $data[0]['project_name'];
            }
            else{
                $t = $data[0]['building_name'];
            }
            if ($data[0]['bedrooms']>0)
            {
                $t .= " ".$data[0]['bedrooms'];
            }
            $t .= " ".$data[0]['propsubtype']." for ".$data[0]['property_for'];
            $project_details = $t;
        }
        if ($cat=="enquiry")
        {
            $id = $data[0]['enquiry_id'];
            $property_type = $data[0]['enquiry_off'];
        }
        $owner_contact_details = $data[0]['name_title']." ".$data[0]['f_name']." ".$data[0]['l_name']." ".$data[0]['mob_no']." ".$data[0]['email'];
        $rera_number = $data[0]['rera_umber'];
    }

    $sql = "SELECT * from sms_template WHERE sms_template_id = $sms_template_id LIMIT 1 ";
    $data = $db->getAllRecords($sql);
    if ($data)
    {
        $text_message = $data[0]['text_message'];
    }
    $senddata =  array();
    $htmldata = array();
    $text_message = str_replace("{{property_id}}",$id,$text_message);
    $text_message = str_replace("{{owner_contact_details}}",$owner_contact_details,$text_message);
    $text_message = str_replace("{{contact_details}}",$owner_contact_details,$text_message);
    $text_message = str_replace("{{sms_sender}}","Mr. Dhaval Thakkar:9819902226",$text_message);
    $text_message = str_replace("{{sms_sender_name}}","Mr. Dhaval Thakkar:9819902226",$text_message);
    $text_message = str_replace("{{sender_mail_id}}","dhaval@rdbrothers.com",$text_message);
    $text_message = str_replace("{{property_type}}",$property_type,$text_message);
    $text_message = str_replace("{{no_matching_enquiry}}",$no_matching_enquiry,$text_message);
    $text_message = str_replace("{{project_details}}",$project_details,$text_message);
    $text_message = str_replace("{{rera_number}}",$rera_number,$text_message);
    $text_message = str_replace("{{no_matching_property}}",$no_matching_property,$text_message);
    
    $htmldata['text_message']=$text_message;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/sms_sent_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS tcreated_date from client_sms ORDER BY created_date DESC";
    $sms_sents = $db->getAllRecords($sql);
    echo json_encode($sms_sents);
});


$app->get('/email_sent_list_ctrl/:next_page_id', function($next_page_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $created_by = $session['user_id'];
    $role = $session['role'];
    
    $countsql = "";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    { 
        $sql = "SELECT *, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') as created_date, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') as mail_date,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = client_mails.created_by) as created_by from client_mails ORDER BY client_mails.created_date DESC LIMIT $next_page_id,30";
        $countsql = "SELECT count(*) as mail_count from client_mails";        
    }
    else{
        $sql = "SELECT *, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') as created_date, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') as mail_date,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by from client_mails as a  WHERE a.created_by = $created_by ORDER BY a.created_date DESC LIMIT $next_page_id,30";
        $countsql = "SELECT  count(*) as mail_count  from client_mails as a WHERE a.created_by = $created_by"; 
    }

    $email_sents = $db->getAllRecords($sql);
    $mail_count = 0;
    $mailcountdata = $db->getAllRecords($countsql);
    if ($mailcountdata)
    {
         $mail_count = $mailcountdata[0]['mail_count'];
    }
    if ($mail_count>0)
    {
        $email_sents[0]['mail_count']=$mail_count;
    }   
    
    echo json_encode($email_sents);
    
});



$app->get('/getsms_data/:cat/:id', function($cat,$id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "(SELECT GROUP_CONCAT(mobile_no SEPARATOR ',') as mobile_nos FROM referrals where FIND_IN_SET(referrals_id , '$id'))";
   
    $mobile_nos = $db->getAllRecords($sql);
    echo json_encode($mobile_nos);
});

$app->get('/getwa_data/:cat/:id', function($cat,$id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "(SELECT GROUP_CONCAT(mobile_no SEPARATOR ',') as mobile_nos FROM referrals where FIND_IN_SET(referrals_id , '$id'))";
    if ($cat=='contacts')
    {
        $sql = "(SELECT GROUP_CONCAT(mob_no SEPARATOR ',') as mobile_nos FROM contact where FIND_IN_SET(contact_id , '$id'))";
    }
   
    $mobile_nos = $db->getAllRecords($sql);
    echo json_encode($mobile_nos);
});


// SENDSMS

//$res = file_get_contents("http://smsp.myoperator.co/api/sendhttp.php?authkey=150242ABJivcxsN58ff6943&mobiles=91".$customer_mobile."&message=Hey there, Its ".$delivery_boy." I just received your order from ".$resturant_name." , I shall visit resturant shortly. Feel free to call me on (".$delivery_mobile.") for any query related to your order no: ".$order_no."&sender=FBUNNY&route=4");


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
    $created_date = date('Y-m-d H:i:s');
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
    $sql = "SELECT * , CONCAT(b.salu,' ',b.fname,' ',b.lname) as employee_name from users as a LEFT JOIN employee as b on a.emp_id = b.emp_id where user_id=".$user_id;
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
    $modified_date = date('Y-m-d H:i:s');
    $r->user->modified_by = $modified_by;
    $r->user->modified_date = $modified_date;
    $user_id  = $r->user->user_id;
    $username = $r->user->username;
    $isUserExists = $db->getOneRecord("select 1 from users where user_id=$user_id");
    if($isUserExists){
        $tabble_name = "users";
        $column_names = array('username','roles','modified_by','modified_date');
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
    
    verifyRequiredParams(array('country','country_code'),$r->country);
    $db = new DbHandler();
    $country = $r->country->country;
    $user_id=$session['user_id'];
    $isUserExists = $db->getOneRecord("select 1 from country where country='$country'");
    if(!$isUserExists){
        $tabble_name = "country";
        $column_names = array('country','country_code','created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->country, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Country created successfully";
            $response["country_id"] = $result;
            $response["user_id"]=$session['user_id'];
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
    verifyRequiredParams(array('country','country_code'),$r->country);
    $db = new DbHandler();
    $country_id  = $r->country->country_id;
    $country = $r->country->country;
    $isUserExists = $db->getOneRecord("select 1 from country where country_id=$country_id");
    if($isUserExists){
        $tabble_name = "country";
        $column_names = array('country','country_code');
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
        $column_names = array('country','country_code');
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




// BRANCH OFFICE


$app->get('/branch_office_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT f.*,CONCAT(g.salu,'',g.fname,' ',g.lname) as office_leading,a.locality,b.area_name, c.city, d.state, e.country from branch_office as f LEFT JOIN employee as g on f.office_lead_id=g.emp_id LEFT JOIN  locality as a on f.locality_id=a.locality_id LEFT JOIN areas as b ON a.area_id = b.area_id LEFT JOIN city as c ON b.city_id = c.city_id LEFT JOIN state as d ON c.state_id = d.state_id LEFT JOIN country as e ON d.country_id = e.country_id  ORDER BY bo_id DESC";
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
    $created_date = date('Y-m-d H:i:s');
    $r->branch_office->created_by = $created_by;
    $r->branch_office->created_date = $created_date;
    $bo_name = $r->branch_office->bo_name;
    $isBOExists = $db->getOneRecord("select 1 from branch_office where bo_name='$bo_name'");
    if(!$isBOExists){
        $tabble_name = "branch_office";
        $column_names = array('bo_name','office_description','office_lead_id','address1','address2','locality_id','area_id','created_by','created_date');
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
    $sql = "SELECT a.*,b.locality,c.area_name,d.city,e.state,f.country from branch_office as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c on b.area_id = c.area_id LEFT JOIN city as d on c.city_id = d.city_id LEFT JOIN state as e on d.state_id = e.state_id LEFT JOIN country as f on e.country_id = f.country_id where a.bo_id=".$bo_id;
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
    $modified_date = date('Y-m-d H:i:s');
    $r->branch_office->modified_by = $modified_by;
    $r->branch_office->modified_date = $modified_date;
    $bo_id  = $r->branch_office->bo_id;
    $branch_office = $r->branch_office->bo_name;
    $isBOExists = $db->getOneRecord("select 1 from branch_office where bo_id=$bo_id");
    if($isBOExists){
        $tabble_name = "branch_office";
        $column_names = array('bo_name','office_description','office_lead_id','address1','address2','locality_id','area_id','modified_by','modified_date');
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







// CITY


$app->get('/city_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT b.*, c.state, d.country from city as b LEFT JOIN state as c ON b.state_id = c.state_id LEFT JOIN country as d ON c.country_id = d.country_id";
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
    $sql = "SELECT b.*, c.state, d.country from city as b LEFT JOIN state as c ON b.state_id = c.state_id LEFT JOIN country as d ON c.country_id = d.country_id where city_id=".$city_id;
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





// AREAS

$app->get('/area_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT a.*,b.city, c.state, CONCAT(d.country,'-',d.country_code)  as country  from areas as a LEFT JOIN city as b on a.city_id = b.city_id LEFT JOIN state as c ON b.state_id = c.state_id LEFT JOIN country as d ON c.country_id = d.country_id";
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
    $sql = "SELECT a.*,b.city, c.state, d.country from areas as a LEFT JOIN city as b on a.city_id = b.city_id LEFT JOIN state as c ON b.state_id = c.state_id LEFT JOIN country as d ON c.country_id = d.country_id where area_id=".$area_id;
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





// LOCALITY

$app->get('/locality_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT a.*,b.area_name, c.city, d.state, CONCAT(e.country,'-',e.country_code) as country from locality as a LEFT JOIN areas as b ON a.area_id = b.area_id LEFT JOIN city as c ON b.city_id = c.city_id LEFT JOIN state as d ON c.state_id = d.state_id LEFT JOIN country as e ON d.country_id = e.country_id ORDER BY a.locality";
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
    $sql = "SELECT a.*,b.area_name, c.city, d.state, e.country from locality as a LEFT JOIN areas as b ON a.area_id = b.area_id LEFT JOIN city as c ON b.city_id = c.city_id LEFT JOIN state as d ON c.state_id = d.state_id LEFT JOIN country as e ON d.country_id = e.country_id where locality_id=".$locality_id;
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
    verifyRequiredParams(array('role'),$r->zrole);
    $db = new DbHandler();
    $role = $r->zrole->role;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->zrole->created_by = $created_by;
    $r->zrole->created_date = $created_date;
    $isRoleExists = $db->getOneRecord("select 1 from roles where role='$role'");
    if(!$isRoleExists){
        $tabble_name = "roles";
        $column_names = array('role','permissions','created_by','created_date');
        $multiple=array("permissions");
        $result = $db->insertIntoTable($r->zrole, $column_names, $tabble_name, $multiple);
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
    verifyRequiredParams(array('role'),$r->zrole);
    $db = new DbHandler();
    $role_id  = $r->zrole->role_id;
    $role = $r->zrole->role;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->zrole->modified_by = $modified_by;
    $r->zrole->modified_date = $modified_date;
    $isRoleExists = $db->getOneRecord("select 1 from roles where role_id=$role_id");
    if($isRoleExists){
        $tabble_name = "roles";
        $column_names = array('role','permissions','modified_by','modified_date');
        $condition = "role_id='$role_id'";
        $multiple=array("permissions");
        $history = $db->historydata( $r->zrole, $column_names, $tabble_name,$condition,$role_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->zrole, $column_names, $tabble_name,$condition,$multiple);
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
    verifyRequiredParams(array('role'),$r->zrole);
    $db = new DbHandler();
    $role_id  = $r->zrole->role_id;
    $role = $r->zrole->role;
    $isRoleExists = $db->getOneRecord("select 1 from roles where role_id=$role_id");
    if($isRoleExists){
        $tabble_name = "roles";
        $column_names = array('role');
        $condition = "role_id='$role_id'";
        $result = $db->deleteIntoTable($r->zrole, $column_names, $tabble_name,$condition);
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





$app->get('/getroles/:role_id', function($role_id) use ($app) 
{
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
    $isRoleExists = $db->getOneRecord("select 1 from role_details where role_id=$role_id");
    if(!$isRoleExists)
    {
    
        $sql = "SELECT * FROM permissions ORDER by permission" ;
        $stmt = $db->getRows($sql);

        if ($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $permission_id = $row['permission_id'];
                
                $query = "INSERT INTO role_details (role_id, permission_id,action_create,action_update,action_delete,action_view,action_export)  VALUES('$role_id','$permission_id', 'true', 'false','false','true', 'false')";
                $result = $db->insertByQuery($query);
            }
        }
        $stmt->close();
    }
    else
    {

        $sql1 = "SELECT * FROM permissions ORDER by permission" ;
        $stmt1 = $db->getRows($sql1);

        if ($stmt1->num_rows > 0)
        {
            while($row1 = $stmt1->fetch_assoc())
            {
                $permission_id = $row1['permission_id'];
                $isPermissionExists = $db->getOneRecord("select 1 from role_details where permission_id=$permission_id and role_id = $role_id ");

                if(!$isPermissionExists)
                {
                
                    $query = "INSERT INTO role_details (role_id, permission_id,action_create,action_update,action_delete,action_view,action_export)  VALUES('$role_id','$permission_id', 'true', 'false','false','true', 'false')";
                    $result = $db->insertByQuery($query);
                }
            }
        }
        $stmt1->close();

    }
    
    $sql = "SELECT * from role_details LEFT JOIN permissions ON permissions.permission_id = role_details.permission_id WHERE role_details.role_id = $role_id ORDER BY permissions.permission_group, permissions.permission";

    $htmlstring .= '<table class="table table-bordered table-striped" >
                                <thead>
                                    <tr>
                                        <th>Group</th>
                                        <th>Permissions</th>
                                        <th>Create</th>
                                        <th>Update</th>
                                        <th>Delete</th>
                                        <th>View</th>
                                        <th>Export</th>
                                        <th>Comments</th>
                                    </tr>
                                </thead>
                                <tbody>';

    $stmt = $db->getRows($sql);

    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $permission_id = $row['permission_id'];
            $htmlstring .= '<tr>
                                <td>'.$row['permission_group'].'</td>
                                <td>'.$row['permission'].'</td>
                                <td><label><input type="checkbox" class="check_element" name="action_create_'.$permission_id.'" id="action_create_'.$permission_id.'" ng-model="permission.action_create_'.$permission_id.'" ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_role_details(\'action_create\','.$row['permission_id'].',permission.action_create_'.$permission_id.')" value="'.$row['action_create'].'"';
                                if ($row['action_create']=='true')
                                {
                                   

                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';
                                
                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="action_update_'.$permission_id.'" id="action_update_'.$permission_id.'" ng-model="permission.action_update_'.$permission_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_role_details(\'action_update\','.$row['permission_id'].',permission.action_update_'.$permission_id.')" value="'.$row['action_update'].'"';
                                if ($row['action_update']=='true')
                                {
                                    
                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';

                                
                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="action_delete_'.$permission_id.'" id="action_delete_'.$permission_id.'" ng-model="permission.action_delete_'.$permission_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_role_details(\'action_delete\','.$row['permission_id'].',permission.action_delete_'.$permission_id.')" value="'.$row['action_delete'].'"';
                                if ($row['action_delete']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';

                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="action_view_'.$permission_id.'" id="action_view_'.$permission_id.'" ng-model="permission.action_view_'.$permission_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_role_details(\'action_view\','.$row['permission_id'].',permission.action_view_'.$permission_id.')" value="'.$row['action_view'].'"';
                                if ($row['action_view']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';

                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="action_export_'.$permission_id.'" id="action_export_'.$permission_id.'" ng-model="permission.action_export_'.$permission_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_role_details(\'action_export\','.$row['permission_id'].',permission.action_export_'.$permission_id.')" value="'.$row['action_export'].'"';
                                if ($row['action_export']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';

                                $htmlstring .= '<td><input type="text" class="form-control" id="comments_'.$permission_id.'" name="comments_'.$permission_id.'" placeholder="Comments" ng-model="permission.comments_'.$permission_id.'"/>
                                </td>
                            </tr>';
        }
        $htmlstring .='</tbody>
                            <tfoot>
                                <tr>
                                    <th>Group</th>
                                    <th>Permissions</th>
                                    <th>Create</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                    <th>View</th>
                                    <th>Export</th>
                                    <th>Comments</th>
                                </tr>
                            </tfoot>
                        </table>'; 
    }   
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/update_role_details/:action/:permission_id/:action_value', function($action, $permission_id,$action_value) use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE role_details set $action = '$action_value' where permission_id = $permission_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
});


$app->get('/getuserroles/:user_id/:role_id', function($user_id,$role_id) use ($app) 
{
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
    $isRoleExists = $db->getOneRecord("select 1 from user_role_details where user_id = $user_id and role_id=$role_id");

    if(!$isRoleExists)
    {
        $sqldelete = "DELETE FROM user_role_details WHERE user_id = $user_id";
        $result = $db->updateByQuery($sqldelete);

        $sql = "SELECT * FROM role_details WHERE role_id = $role_id" ;
        $stmt = $db->getRows($sql);

        if ($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $permission_id = $row['permission_id'];
                $action_create = $row['action_create'];
                $action_update = $row['action_update'];
                $action_delete = $row['action_delete'];
                $action_view = $row['action_view'];
                $action_export = $row['action_export'];
                $query = "INSERT INTO user_role_details (user_id, role_id, permission_id,action_create,action_update,action_delete,action_view,action_export)  VALUES('$user_id','$role_id','$permission_id', '$action_create', '$action_update','$action_delete','$action_view', '$action_export')";
                $result = $db->insertByQuery($query);
            }
        }
        $stmt->close();
    }
    else
    {
        
        $sql1 = "SELECT * FROM permissions" ;
        $stmt1 = $db->getRows($sql1);

        if ($stmt1->num_rows > 0)
        {
            while($row1 = $stmt1->fetch_assoc())
            {
                $permission_id = $row1['permission_id'];
                $isPermissionExists = $db->getOneRecord("select 1 from user_role_details where user_id = $user_id and permission_id=$permission_id and role_id = $role_id ");

                if(!$isPermissionExists)
                {
                    $query = "INSERT INTO user_role_details (user_id, role_id, permission_id)  VALUES('$user_id','$role_id','$permission_id')";
                    $result = $db->insertByQuery($query);
                }
            }
        }
        $stmt1->close();
    }
    
    
    $sql = "SELECT * from user_role_details LEFT JOIN permissions ON permissions.permission_id = user_role_details.permission_id WHERE user_role_details.role_id = $role_id and user_role_details.user_id = $user_id ORDER BY permissions.permission_group,permissions.permission";

    $htmlstring .= '<table class="table table-bordered table-striped" >
                                <thead>
                                    <tr>
                                        <th>Group</th>
                                        <th>Permissions</th>
                                        <th>Create</th>
                                        <th>Update</th>
                                        <th>Delete</th>
                                        <th>View</th>
                                        <th>Export</th>
                                        <th>Comments</th>
                                    </tr>
                                </thead>
                                <tbody>';

    $stmt = $db->getRows($sql);

    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $user_role_details_id = $row['user_role_details_id'];
            $htmlstring .= '<tr>
                                <td>'.$row['permission_group'].'</td>
                                <td>'.$row['permission'].'</td>
                                <td><label><input type="checkbox" class="check_element" name="action_create_'.$user_role_details_id.'" id="action_create_'.$user_role_details_id.'" ng-model="action_create_'.$user_role_details_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_user_role_details(\'action_create\','.$row['user_role_details_id'].',action_create_'.$user_role_details_id.')" value="'.$row['action_create'].'"';
                                if ($row['action_create']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';
                                
                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="action_update_'.$user_role_details_id.'" id="action_update_'.$user_role_details_id.'" ng-model="action_update_'.$user_role_details_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_user_role_details(\'action_update\','.$row['user_role_details_id'].',action_update_'.$user_role_details_id.')" value="'.$row['action_update'].'"';
                                if ($row['action_update']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true"  checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';

                                
                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="action_delete_'.$user_role_details_id.'" id="action_delete_'.$user_role_details_id.'" ng-model="action_delete_'.$user_role_details_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_user_role_details(\'action_delete\','.$row['user_role_details_id'].',action_delete_'.$user_role_details_id.')" value="'.$row['action_delete'].'"';
                                if ($row['action_delete']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true"  checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';

                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="action_view_'.$user_role_details_id.'" id="action_view_'.$user_role_details_id.'" ng-model="action_view_'.$user_role_details_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_user_role_details(\'action_view\','.$row['user_role_details_id'].',action_view_'.$user_role_details_id.')" value="'.$row['action_view'].'"';
                                if ($row['action_view']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true"  checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';

                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="action_export_'.$user_role_details_id.'" id="action_export_'.$user_role_details_id.'" ng-model="action_export_'.$user_role_details_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_user_role_details(\'action_export\','.$row['user_role_details_id'].',action_export_'.$user_role_details_id.')" value="'.$row['action_export'].'"';
                                if ($row['action_export']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true"  checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';

                                $htmlstring .= '<td><input type="text" class="form-control" id="comments_'.$user_role_details_id.'" name="comments_'.$user_role_details_id.'" placeholder="Comments" ng-model="comments_'.$user_role_details_id.'"/>
                                </td>
                            </tr>';
        }
        $htmlstring .='</tbody>
                            <tfoot>
                                <tr>
                                    <th>Group</th>
                                    <th>Permissions</th>
                                    <th>Create</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                    <th>View</th>
                                    <th>Export</th>
                                    <th>Comments</th>
                                </tr>
                            </tfoot>
                        </table>'; 
    }  
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/update_user_role_details/:action/:user_role_details_id/:action_value', function($action, $user_role_details_id,$action_value) use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE user_role_details set $action = '$action_value' where user_role_details_id = $user_role_details_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
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
    verifyRequiredParams(array('group_name'),$r->groupdata);
    $db = new DbHandler();
    $group_name = $r->groupdata->group_name;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->groupdata->created_by = $created_by;
    $r->groupdata->created_date = $created_date;
    $isGroupExists = $db->getOneRecord("select 1 from groups where group_name='$group_name'");
    if(!$isGroupExists){
        $tabble_name = "groups";
        $column_names = array('group_name','description','created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->groupdata, $column_names, $tabble_name, $multiple);
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
    verifyRequiredParams(array('group_name'),$r->groupdata);
    $db = new DbHandler();
    $group_id  = $r->groupdata->group_id;
    $group_name = $r->groupdata->group_name;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->groupdata->modified_by = $modified_by;
    $r->groupdata->modified_date = $modified_date;
    $isGroupExists = $db->getOneRecord("select 1 from groups where group_id=$group_id");
    if($isGroupExists){
        $tabble_name = "groups";
        $column_names = array('group_name','description','modified_by','modified_date');
        $condition = "group_id='$group_id'";
        $multiple=array("");
        $history = $db->historydata( $r->groupdata, $column_names, $tabble_name,$condition,$group_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->groupdata, $column_names, $tabble_name,$condition,$multiple);
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
    verifyRequiredParams(array('group_name'),$r->groupdata);
    $db = new DbHandler();
    $group_id  = $r->groupdata->group_id;
    $group_name = $r->groupdata->group_name;
    $isGroupExists = $db->getOneRecord("select 1 from groups where group_id=$group_id");
    if($isGroupExists){
        $tabble_name = "groups";
        $column_names = array('group_name');
        $condition = "group_id='$group_id'";
        $result = $db->deleteIntoTable($r->groupdata, $column_names, $tabble_name,$condition);
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

$app->get('/selectgroups', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT * FROM groups ORDER by group_name";
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
    
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->permission->created_by = $created_by;
    $r->permission->created_date = $created_date;

    $isPermissionExists = $db->getOneRecord("select 1 from permissions where permission='$permission'");
    if(!$isPermissionExists){
        $tabble_name = "permissions";
        $column_names = array('permission','created_by','created_date');
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
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->permission->modified_by = $modified_by;
    $r->permission->modified_date = $modified_date;

    $isPermissionExists = $db->getOneRecord("select 1 from permissions where permission_id=$permission_id");
    if($isPermissionExists){
        $tabble_name = "permissions";
        $column_names = array('permission','modified_by','modified_date');
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




// Expense Head

$app->get('/expense_head_list_ctrl', function() use ($app) {
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


$app->post('/expense_head_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('expense_title'),$r->expense_headdata);
    $db = new DbHandler();
    $expense_title = $r->expense_headdata->expense_title;
    
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->expense_headdata->created_by = $created_by;
    $r->expense_headdata->created_date = $created_date;

    $isexpense_headExists = $db->getOneRecord("select 1 from expense_head where expense_title='$expense_title'");
    if(!$isexpense_headExists){
        $tabble_name = "expense_head";
        $column_names = array('expense_title','created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->expense_headdata, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Expense Head created successfully";
            $response["expense_head_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Expense Head. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Expense Head with the provided Expense Head exists!";
        echoResponse(201, $response);
    }
});


$app->get('/expense_head_edit_ctrl/:expense_head_id', function($expense_head_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from expense_head where expense_head_id=".$expense_head_id;
    $expense_heads = $db->getAllRecords($sql);
    echo json_encode($expense_heads);
    
});

$app->post('/expense_head_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('expense_head_id'),$r->expense_headdata);
    $db = new DbHandler();
    $expense_head_id  = $r->expense_headdata->expense_head_id;
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->expense_headdata->modified_by = $modified_by;
    $r->expense_headdata->modified_date = $modified_date;

    $isexpense_headExists = $db->getOneRecord("select 1 from expense_head where expense_head_id=$expense_head_id");
    if($isexpense_headExists){
        $tabble_name = "expense_head";
        $column_names = array('expense_title','modified_by','modified_date');
        $condition = "expense_head_id='$expense_head_id'";
        $multiple = array("");
        $history = $db->historydata( $r->expense_headdata, $column_names, $tabble_name,$condition,$expense_head_id,$multiple, $modified_by, $modified_date);
        
        $result = $db->updateIntoTable($r->expense_headdata, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Expense Head Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Expense Head. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Expense Head with the provided Expense Head does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/expense_head_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('expense_head_id'),$r->expense_headdata);
    $db = new DbHandler();
    $expense_head_id  = $r->expense_headdata->expense_head_id;
    $isexpense_headExists = $db->getOneRecord("select 1 from expense_head where expense_head_id=$expense_head_id");
    if($isexpense_headExists){
        $tabble_name = "expense_head";
        $column_names = array('expense_title');
        $condition = "expense_head_id='$expense_head_id'";
        $result = $db->deleteIntoTable($r->expense_headdata, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Expense Head Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Expense Head. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Expense Head with the provided Expense Head does not exists!";
        echoResponse(201, $response);
    }
});


// EMPLOYEE ASSET

$app->get('/employee_asset_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,CONCAT(b.salu,' ',b.fname,' ',b.lname) as employee_name, DATE_FORMAT(a.issued_date,'%d/%m/%Y') AS issued_date from employee_asset as a LEFT JOIN employee as b ON  a.emp_id = b.emp_id ORDER BY a.asset_title";
    $employee_assets = $db->getAllRecords($sql);
    echo json_encode($employee_assets);
});


$app->post('/employee_asset_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('emp_id'),$r->employee_assetdata);
    $db = new DbHandler();
    $emp_id = $r->employee_assetdata->emp_id;
    
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->employee_assetdata->created_by = $created_by;
    $r->employee_assetdata->created_date = $created_date;

    if (isset($r->employee_assetdata->issued_date))
    {
        $tissued_date = substr($r->employee_assetdata->issued_date,6,4)."-".substr($r->employee_assetdata->issued_date,3,2)."-".substr($r->employee_assetdata->issued_date,0,2)." ".substr($r->employee_assetdata->issued_date,11,5);
        $r->employee_assetdata->issued_date = $tissued_date;
    }

    $tabble_name = "employee_asset";
    $column_names = array('emp_id', 'issued_date', 'asset_title', 'asset_spec', 'created_by','created_date');
    $multiple=array("");
    $result = $db->insertIntoTable($r->employee_assetdata, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Employee Asset created successfully";
        $response["employee_asset_id"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create Employee Asset. Please try again";
        echoResponse(201, $response);
    }
});


$app->get('/employee_asset_edit_ctrl/:employee_asset_id', function($employee_asset_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,DATE_FORMAT(issued_date,'%d/%m/%Y') AS issued_date from employee_asset where employee_asset_id=".$employee_asset_id;
    $employee_assets = $db->getAllRecords($sql);
    echo json_encode($employee_assets);
    
});

$app->post('/employee_asset_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('employee_asset_id'),$r->employee_assetdata);
    $db = new DbHandler();
    $employee_asset_id  = $r->employee_assetdata->employee_asset_id;
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->employee_assetdata->modified_by = $modified_by;
    $r->employee_assetdata->modified_date = $modified_date;

    if (isset($r->employee_assetdata->issued_date))
    {
        $tissued_date = substr($r->employee_assetdata->issued_date,6,4)."-".substr($r->employee_assetdata->issued_date,3,2)."-".substr($r->employee_assetdata->issued_date,0,2)." ".substr($r->employee_assetdata->issued_date,11,5);
        $r->employee_assetdata->issued_date = $tissued_date;
    }

    $isemployee_assetExists = $db->getOneRecord("select 1 from employee_asset where employee_asset_id=$employee_asset_id");
    if($isemployee_assetExists){
        $tabble_name = "employee_asset";
        $column_names = array('emp_id', 'issued_date', 'asset_title', 'asset_spec', 'modified_by','modified_date');
        $condition = "employee_asset_id='$employee_asset_id'";
        $multiple = array("");
        $history = $db->historydata( $r->employee_assetdata, $column_names, $tabble_name,$condition,$employee_asset_id,$multiple, $modified_by, $modified_date);
        
        $result = $db->updateIntoTable($r->employee_assetdata, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Employee Asset Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Employee Asset. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Employee Asset with the provided Employee Asset ID does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/employee_asset_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('employee_asset_id'),$r->employee_assetdata);
    $db = new DbHandler();
    $employee_asset_id  = $r->employee_assetdata->employee_asset_id;
    $isemployee_assetExists = $db->getOneRecord("select 1 from employee_asset where employee_asset_id=$employee_asset_id");
    if($isemployee_assetExists){
        $tabble_name = "employee_asset";
        $column_names = array('emp_id', 'issued_date', 'asset_title', 'asset_spec', );
        $condition = "employee_asset_id='$employee_asset_id'";
        $result = $db->deleteIntoTable($r->employee_assetdata, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Employee Asset Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Employee Asset. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Employee Asset with the provided Employee Asset does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectemployee_asset', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from employee_asset ORDER BY asset_title";
    $employee_assets = $db->getAllRecords($sql);
    echo json_encode($employee_assets);
});

// EMPLOYEE LEAVE

$app->get('/employee_leave_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];

    $role = $session['role'];

    if (in_array("Admin", $role) || in_array("Human Resource new",$role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT *,CONCAT(b.salu,' ',b.fname,' ',b.lname) as employee_name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to FROM employee_leave as a LEFT JOIN employee as b ON a.emp_id = b.emp_id LEFT JOIN users as c ON a.emp_id = c.emp_id ORDER BY a.leave_date_from DESC ";

    }
    else
    {
        $sql = "SELECT *,CONCAT(b.salu,' ',b.fname,' ',b.lname) as employee_name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to FROM employee_leave as a LEFT JOIN employee as b ON a.emp_id = b.emp_id LEFT JOIN users as c ON a.emp_id = c.emp_id WHERE c.user_id = $user_id ORDER BY a.leave_date_from DESC ";
    }
    $employee_leaves = $db->getAllRecords($sql);
    echo json_encode($employee_leaves);
});


$app->post('/employee_leave_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('emp_id'),$r->employee_leavedata);
    $db = new DbHandler();

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->employee_leavedata->created_by = $created_by;
    $r->employee_leavedata->created_date = $created_date;
    $r->employee_leavedata->leave_status = "Requested";

    if (isset($r->employee_leavedata->leave_date_from))
    {
        $tleave_date_from = substr($r->employee_leavedata->leave_date_from,6,4)."-".substr($r->employee_leavedata->leave_date_from,3,2)."-".substr($r->employee_leavedata->leave_date_from,0,2)." ".substr($r->employee_leavedata->leave_date_from,11,5);
        $r->employee_leavedata->leave_date_from = $tleave_date_from;
    }

    if (isset($r->employee_leavedata->leave_date_to))
    {
        $tleave_date_to = substr($r->employee_leavedata->leave_date_to,6,4)."-".substr($r->employee_leavedata->leave_date_to,3,2)."-".substr($r->employee_leavedata->leave_date_to,0,2)." ".substr($r->employee_leavedata->leave_date_to,11,5);
        $r->employee_leavedata->leave_date_to = $tleave_date_to;
    }

    $tabble_name = "employee_leave";
    $column_names = array('emp_id', 'leave_date_from', 'leave_date_to', 'days', 'leave_reason', 'leave_status', 'created_by','created_date');
    $multiple=array("");
    $result = $db->insertIntoTable($r->employee_leavedata, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Leave Record created successfully";
        $response["employee_leave_id"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create Leave Record. Please try again";
        echoResponse(201, $response);
    }
});


$app->get('/employee_leave_edit_ctrl/:employee_leave_id', function($employee_leave_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,DATE_FORMAT(leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(leave_date_to,'%d/%m/%Y') AS leave_date_to  from employee_leave where employee_leave_id=".$employee_leave_id;
    $employee_leaves = $db->getAllRecords($sql);
    echo json_encode($employee_leaves);
    
});

$app->post('/employee_leave_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('employee_leave_id'),$r->employee_leavedata);
    $db = new DbHandler();
    $employee_leave_id  = $r->employee_leavedata->employee_leave_id;
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->employee_leavedata->modified_by = $modified_by;
    $r->employee_leavedata->modified_date = $modified_date;
    if (isset($r->employee_leavedata->leave_date_from))
    {
        $tleave_date_from = substr($r->employee_leavedata->leave_date_from,6,4)."-".substr($r->employee_leavedata->leave_date_from,3,2)."-".substr($r->employee_leavedata->leave_date_from,0,2)." ".substr($r->employee_leavedata->leave_date_from,11,5);
        $r->employee_leavedata->leave_date_from = $tleave_date_from;
    }

    if (isset($r->employee_leavedata->leave_date_to))
    {
        $tleave_date_to = substr($r->employee_leavedata->leave_date_to,6,4)."-".substr($r->employee_leavedata->leave_date_to,3,2)."-".substr($r->employee_leavedata->leave_date_to,0,2)." ".substr($r->employee_leavedata->leave_date_to,11,5);
        $r->employee_leavedata->leave_date_to = $tleave_date_to;
    }

    $isemployee_leaveExists = $db->getOneRecord("select 1 from employee_leave where employee_leave_id=$employee_leave_id");
    if($isemployee_leaveExists){
        $tabble_name = "employee_leave";
        $column_names = array('emp_id', 'leave_date_from', 'leave_date_to', 'days', 'leave_reason', 'leave_status', 'modified_by','modified_date');
        $condition = "employee_leave_id='$employee_leave_id'";
        $multiple = array("");
        $history = $db->historydata( $r->employee_leavedata, $column_names, $tabble_name,$condition,$employee_leave_id,$multiple, $modified_by, $modified_date);
        
        $result = $db->updateIntoTable($r->employee_leavedata, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Leave Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Leave. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Leave Record with the provided Leave ID does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/employee_leave_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('employee_leave_id'),$r->employee_leavedata);
    $db = new DbHandler();
    $employee_leave_id  = $r->employee_leavedata->employee_leave_id;
    $isemployee_leaveExists = $db->getOneRecord("select 1 from employee_leave where employee_leave_id=$employee_leave_id");
    if($isemployee_leaveExists){
        $tabble_name = "employee_leave";
        $column_names = array('expense_title');
        $condition = "employee_leave_id='$employee_leave_id'";
        $result = $db->deleteIntoTable($r->employee_leavedata, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Leave Record Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Leave Record, Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Leave Record with the provided Leave ID does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectemployee_leave', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from employee_leave ORDER BY emp_id";
    $employee_leaves = $db->getAllRecords($sql);
    echo json_encode($employee_leaves);
});


$app->get('/getdatavalues_leaves/:field_name', function($field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM employee_leave ORDER BY $field_name";
    
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});
// pks search add 05-05-2023 afternoon

$app->post('/search_leaves', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    // error_log(print_r($r, true), 3, "logfile.log");

    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];

    $role = $session['role'];

    $searchsql = "select * from employee_leave limit 0";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    { 
        // $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM employee_leave as a LEFT JOIN designation as b ON a.designation_id = b.designation_id WHERE a.employee_leave_id > 0 ";

        $searchsql = "SELECT *,CONCAT(b.salu,' ',b.fname,' ',b.lname) as employee_name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y')
        AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to FROM employee_leave as a LEFT JOIN 
       employee as b ON a.emp_id = b.emp_id LEFT JOIN users as c ON a.emp_id = c.emp_id ";
    }
    else if (in_array("Branch Head", $role))
    {
        // $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM employee_leave as a LEFT JOIN designation as b ON a.designation_id = b.designation_id WHERE a.employee_leave_id > 0 ";
        $searchsql = "SELECT *,CONCAT(b.salu,' ',b.fname,' ',b.lname) as employee_name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y')
        AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to FROM employee_leave as a LEFT JOIN 
       employee as b ON a.emp_id = b.emp_id LEFT JOIN users as c ON a.emp_id = c.emp_id ";
    }
    else
    { 
        // $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM employee_leave as a LEFT JOIN designation as b ON a.designation_id = b.designation_id WHERE a.employee_leave_id > 0 ";
        $searchsql = "SELECT *,CONCAT(b.salu,' ',b.fname,' ',b.lname) as employee_name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to FROM employee_leave as a LEFT JOIN employee as b ON a.emp_id = b.emp_id LEFT JOIN users as c ON a.emp_id = c.emp_id ";
    }     
    // WHERE c.user_id = $user_id

    // if (isset($r->searchdata->designation_id))
    // {
    //     $first = "Yes";
    //     $new_data = $r->searchdata->designation_id;
    //     if ($new_data)
    //     {
    //         foreach($new_data as $value)
    //         {
    //             if ($first=='Yes')
    //             {
    //                 $sql .= " AND (";
    //                 $first = "No";
    //             }
    //             else{
    //                 $sql .= " OR ";
    //             }                  
    //             $sql .= " a.designation_id LIKE '".$value."' ";
    //         }
    //         if ($first=='No')
    //         {
    //             $sql .= ") ";
                
    //         }
    //     }
    // }

    // if (isset($r->searchdata->mobile_no))
    // {
    //     $first = "Yes";
    //     $new_data = $r->searchdata->mobile_no;
    //     if ($new_data)
    //     {
    //         foreach($new_data as $value)
    //         {
    //             if ($first=='Yes')
    //             {
    //                 $sql .= " AND (";
    //                 $first = "No";
    //             }
    //             else{
    //                 $sql .= " OR ";
    //             }                  
    //             $sql .= " a.mobile_no LIKE '".$value."' ";
    //         }
    //         if ($first=='No')
    //         {
    //             $sql .= ") ";
                
    //         }
    //     }
    // }

    // if (isset($r->searchdata->email))
    // {
    //     $first = "Yes";
    //     $new_data = $r->searchdata->email;
    //     if ($new_data)
    //     {
    //         foreach($new_data as $value)
    //         {
    //             if ($first=='Yes')
    //             {
    //                 $sql .= " AND (";
    //                 $first = "No";
    //             }
    //             else{
    //                 $sql .= " OR ";
    //             }                  
    //             $sql .= " a.email LIKE '".$value."' ";
    //         }
    //         if ($first=='No')
    //         {
    //             $sql .= ") ";
                
    //         }
    //     }
    // }

    if (isset($r->searchdata->employee_name) && (!isset($r->searchdata->employee_leave_start_date)) && (!isset($r->searchdata->employee_leave_end_date)) )
    {
        $first = "Yes";
        $new_data = $r->searchdata->employee_name;
        // error_log(print_r($new_data, true), 3, "logfile.log");
        if ($new_data)
        {
            // foreach($new_data as $value)
            // {
            //     if ($first=='Yes')
            //     {
            //         $sql .= " AND (";
            //         $first = "No";
            //     }
            //     else{
            //         $sql .= " OR ";
            //     }                  
                // $sql .= " a.employee_name LIKE '".$new_data."' ";
                $sql .= " WHERE CONCAT(b.salu, ' ', b.fname, ' ', b.lname) LIKE '%".$new_data."%' ";
            // }
            // if ($first=='No')
            // {
            //     $sql .= ") ";
                
            // }
        }
    }
    if ((isset($r->searchdata->employee_leave_start_date)) && (!isset($r->searchdata->employee_leave_end_date)) && (!isset($r->searchdata->employee_name)) )
    {
        $first = "Yes";
        $date_str = $r->searchdata->employee_leave_start_date;
        $date = DateTime::createFromFormat('d/m/Y', $date_str);
        if ($date) {
            $new_data = $date->format('Y-m-d');
            // error_log($new_data, 3, "logfile.log");
        } else {
            // error_log("Invalid date format: " . $date_str, 3, "logfile.log");
        }

        // $date_str =  $r->searchdata->employee_leave_start_date;
        // error_log($date_str, 3, "logfile.log");
        // $new_data =  date('Y-m-d', strtotime($date_str));
        // $new_data2 = $r->searchdata->employee_leave_end_date;
        if ($new_data)
        {
            // foreach($new_data as $value)
            // {
            //     if ($first=='Yes')
            //     {
            //         $sql .= " AND (";
            //         $first = "No";
            //     }
            //     else{
            //         $sql .= " OR ";
            //     }                  
                $sql .= " WHERE a.leave_date_from LIKE '".$new_data."' ";
            // }
            // if ($first=='No')
            // {
            //     $sql .= ") ";
                
            // }
        }
    }
    if ((isset($r->searchdata->employee_leave_start_date)) && (!isset($r->searchdata->employee_leave_end_date)) && (isset($r->searchdata->employee_name)) )
    {
        $first = "Yes";
        $date_str = $r->searchdata->employee_leave_start_date;
        $new_data1 = $r->searchdata->employee_name;
        $date = DateTime::createFromFormat('d/m/Y', $date_str);
        if ($date) {
            $new_data = $date->format('Y-m-d');
            // error_log($new_data, 3, "logfile.log");
        } else {
            // error_log("Invalid date format: " . $date_str, 3, "logfile.log");
        }

        // $date_str =  $r->searchdata->employee_leave_start_date;
        // error_log($date_str, 3, "logfile.log");
        // $new_data =  date('Y-m-d', strtotime($date_str));
        // $new_data2 = $r->searchdata->employee_leave_end_date;
        if ($new_data)
        {
            // foreach($new_data as $value)
            // {
            //     if ($first=='Yes')
            //     {
            //         $sql .= " AND (";
            //         $first = "No";
            //     }
            //     else{
            //         $sql .= " OR ";
            //     }                  
                // $sql .= " WHERE CONCAT(b.salu, ' ', b.fname, ' ', b.lname) LIKE '%' '".$new_data1."' '%' a.leave_date_from LIKE '".$new_data."' ";
                $sql .= " WHERE CONCAT(b.salu, ' ', b.fname, ' ', b.lname) LIKE '%".$new_data1."%' AND a.leave_date_from LIKE '".$new_data."' ";
            // }
            // if ($first=='No')
            // {
            //     $sql .= ") ";
                
            // }
        }
    }
    
    if ((isset($r->searchdata->employee_leave_start_date)) && (isset($r->searchdata->employee_leave_end_date)) && (!isset($r->searchdata->employee_name)) )
    {
        // error_log("plk", 3, "logfile.log");
        $first = "Yes";
        $date_str1 = $r->searchdata->employee_leave_start_date;
        $date_str2 = $r->searchdata->employee_leave_end_date;
        $date1 = DateTime::createFromFormat('d/m/Y', $date_str1);
        $date2 = DateTime::createFromFormat('d/m/Y', $date_str2);
        if ($date1) {
            $new_data1 = $date1->format('Y-m-d');
            // error_log($new_data1, 3, "logfile.log");
        } else {
            // error_log("Invalid date format: " . $date_str1, 3, "logfile.log");
        }
        if ($date2) {
            $new_data2 = $date2->format('Y-m-d');
            // error_log($new_data2, 3, "logfile.log");
        } else {
            // error_log("Invalid date format: " . $date_str2, 3, "logfile.log");
        }
        // error_log($new_data1, 3, "logfile.log");
        // error_log($new_data2, 3, "logfile.log");
        if ($new_data1 && $new_data2)
        {
            // foreach($new_data as $value)
            // {
            //     if ($first=='Yes')
            //     {
            //         $sql .= " AND (";
            //         $first = "No";
            //     }
            //     else{
            //         $sql .= " OR ";
            //     }
                $sql .= " WHERE a.leave_date_from >= '".$new_data1."' AND a.leave_date_from <= '".$new_data2."' ";
                // $sql .= " a.leave_date_from BETWEEN '".$new_data1."' AND '".$new_data2."' ";
            // }
            // if ($first=='No')
            // {
            //     $sql .= ") ";
                
            // }
        }
    }
    if ((isset($r->searchdata->employee_leave_start_date)) && (isset($r->searchdata->employee_leave_end_date)) && (isset($r->searchdata->employee_name)) )
    {
        $first = "Yes";
        $date_str1 = $r->searchdata->employee_leave_start_date;
        $date_str2 = $r->searchdata->employee_leave_end_date;
        $date1 = DateTime::createFromFormat('d/m/Y', $date_str1);
        $date2 = DateTime::createFromFormat('d/m/Y', $date_str2);
        if ($date1) {
            $new_data1 = $date1->format('Y-m-d');
            // error_log($new_data1, 3, "logfile.log");
        } else {
            // error_log("Invalid date format: " . $date_str1, 3, "logfile.log");
        }
        if ($date2) {
            $new_data2 = $date2->format('Y-m-d');
            // error_log($new_data2, 3, "logfile.log");
        } else {
            // error_log("Invalid date format: " . $date_str2, 3, "logfile.log");
        }
        $new_data3 = $r->searchdata->employee_name;
        if ($new_data1 && $new_data2 && $new_data3)
        {
            // foreach($new_data as $value)
            // {
            //     if ($first=='Yes')
            //     {
            //         $sql .= " AND (";
            //         $first = "No";
            //     }
            //     else{
            //         $sql .= " OR ";
            //     } 
                // $sql .= " a.employee_name LIKE '".$new_data."' a.leave_date_from BETWEEN '".$new_data1."' AND '".$new_data2."' ";
                $sql .= "WHERE CONCAT(b.salu, ' ', b.fname, ' ', b.lname) LIKE '%' '".$new_data3."' '%' AND a.leave_date_from BETWEEN '".$new_data1."' AND '".$new_data2."'";
                // $sql .= "where a.employee_name LIKE '%".$new_data3."%' AND a.leave_date_from BETWEEN '".$new_data1."' AND '".$new_data2."'";
            // }
            // if ($first=='No')
            // {
            //     $sql .= ") ";g
                
            // }
        }
    }
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
       
    }else{
        $sql .= "AND c.user_id = '".$user_id."' ";
    }
    
    $searchsql .=  $sql .  " ORDER BY b.fname ";

    // error_log(print_r($searchsql, true), 3, "logfile.log");
    $resumes = $db->getAllRecords($searchsql);

    //$resumes[0]['$searchsql']=$searchsql;

    // error_log(print_r($resumes, true), 3, "logfile.log");

    echo json_encode($resumes);
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








$app->get('/activityenquiries/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $property_for = "";
    $property_type = "";
    $project_id = 0;
    $area_id = 0;
    $exp_price = 0;
    $bedrooms = 0;
    $bathrooms = 0;

    $propertydata = $db->getAllRecords("SELECT * FROM property where property_id = $property_id");
    
    if ($propertydata)
    {
        $property_for = $propertydata[0]['property_for'];
        if ($property_for == 'Sale')
        {
            $property_for = 'Buy';
        }
        if ($property_for == 'Rent/Lease')
        {
            $property_for = 'Lease';
        }
        $property_type = $propertydata[0]['propsubtype'];
        $project_id = $propertydata[0]['project_id'];
        $area_id = $propertydata[0]['area_id'];
        $exp_price = $propertydata[0]['exp_price'];
        $bedrooms = $propertydata[0]['bedrooms'];
        $bathrooms = $propertydata[0]['bathrooms'];
    }

    $bo_id = $session['bo_id'];
    $sql = "SELECT *, CONCAT(a.enquiry_off,'_',a.enquiry_id,' ',a.enquiry_type,'-',b.name_title,' ',b.f_name,' ',b.l_name) as enquiry_title , CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob, e.locality as preferred_locality, f.area_name as preferred_area  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN users as c on a.assigned = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id  WHERE ";
    
    $insert_and = "No";

    if ($property_for !="")
    {
        $sql .= " a.enquiry_for = '$property_for'  ";
        $insert_and = "Yes";
    }

    if ($property_type !="")
    {
        if ($insert_and == "Yes")
        {
            $sql .= " and ";
        }
        $sql .= " a.enquiry_type = '$property_type' ";
        $insert_and = "Yes";
    }

    if ($project_id !=0)
    {
        if ($insert_and == "Yes")
        {
            $sql .= " and ";
        }
        $sql .= " a.preferred_project_id  = $project_id ";
        $insert_and = "Yes";
    }

    if ($area_id !=0)
    {
        if ($insert_and == "Yes")
        {
            $sql .= " and ";
        }
        $sql .= " a.preferred_area_id  = $area_id  ";
        $insert_and = "Yes";
    }

    if ($exp_price >0)
    {
        if ($insert_and == "Yes")
        {
            $sql .= " and ";
        }

        $sql .= " ( $exp_price>=a.budget_range1 and $exp_price<=budget_range2) ";
        $insert_and = "Yes";
    }
    
    /*if ($bedrooms >0)
    {
        $sql .= " ( a.bedrooms = $bedrooms) and ";
    }

    if ($bathrooms >0)
    {
        $sql .= " ( a.bath = $bathrooms)  and ";
    }
    $bo_id = $session['bo_id'];
    
    $sql .= " a.status = 'Active' ";*/
    
    //$sql .= " z.bo_id = $bo_id ";

    $sql .= " ORDER BY a.enquiry_for";     

//echo $sql;
//exit(0);                       
    
    $selectenquiries = $db->getAllRecords($sql);
    echo json_encode($selectenquiries);
    
});
   











$app->get('/activityproperties/:enquiry_id', function($enquiry_id) use ($app) {
        $sql  = "";
        $db = new DbHandler();
        $session = $db->getSession();
        if ($session['username']=="Guest")
        {
            return;
        }
        
        $enquiry_for = "";
        $enquiry_type = "";
        $preferred_project_id = 0;
        $preferrred_area_id = 0;
        $budget_range1 = 0;
        $budget_range2 = 0;
        $bedrooms = 0;
        $bath = 0;
    
        $enquirydata = $db->getAllRecords("SELECT * FROM enquiry where enquiry_id = $enquiry_id");
        
        if ($enquirydata)
        {
            $enquiry_for = $enquirydata[0]['enquiry_for'];
            if ($enquiry_for == 'Buy')
            {
                $enquiry_for = 'Sale';
            }

            if ($enquiry_for == 'Lease')
            {
                $enquiry_for = 'Rent/Lease';
            }

            $enquiry_type = $enquirydata[0]['enquiry_type'];
            $preferred_project_id = $enquirydata[0]['preferred_project_id'];
            $preferred_area_id = $enquirydata[0]['preferred_area_id'];
            $budget_range1 = $enquirydata[0]['budget_range1'];
            $budget_range2 = $enquirydata[0]['budget_range2'];
            $bedrooms = $enquirydata[0]['bedrooms'];
            $bath = $enquirydata[0]['bath'];
        }
        $bo_id = $session['bo_id'];
        $role = $session['role'];

        $ifAdmin = false;
        if (in_array("Admin", $role) || in_array("Sub Admin", $role))
        {
            $ifAdmin = true; 
        }
    
        $sql = "SELECT * from property as a LEFT JOIN project as b on a.project_id = b.project_id ";
    
    if (!$ifAdmin)
    {
            $sql .= " LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id ";
        }

        $sql .= " WHERE ";
        
        if ($enquiry_for !="")
        {
            $sql .= " a.property_for = '$enquiry_for' and ";
        }
    
        if ($enquiry_type !="")
        {
            $sql .= " a.propsubtype = '$enquiry_type' and ";
        }
    
        if ($preferred_project_id !=0)
        {
            $sql .= " a.project_id  = $preferred_project_id and ";
        }
    
        if ($preferred_area_id !=0)
        {
            $sql .= " a.area_id  = $preferred_area_id and ";
        }
    
        if ($budget_range1 >0 or $budget_range2 >0)
        {
            $sql .= " ( a.exp_price >=  $budget_range1 and a.exp_price <= $budget_range2) ";
        }
        
        /*if ($bedrooms >0)
        {
            $sql .= " ( a.bedrooms = $bedrooms) and ";
        }
    
        if ($bath >0)
        {
            $sql .= " ( a.bathrooms = $bath) and ";
        }

    $sql .= " a.deal_done != 'Yes' ";*/

    if (!$ifAdmin)
    {
            $sql .= " and z.bo_id = $bo_id "; 
    }   
    $sql .= " ORDER BY a.property_for"; 

    $enquiries = $db->getAllRecords($sql);
    echo json_encode($enquiries);


});

$app->get('/activityproject/:enquiry_id', function($enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $enquiry_for = "";
    $enquiry_type = "";
    $preferred_project_id = 0;
    $preferrred_area_id = 0;
    $budget_range1 = 0;
    $budget_range2 = 0;
    $bedrooms = 0;
    $bath = 0;

    $enquirydata = $db->getAllRecords("SELECT * FROM enquiry where enquiry_id = $enquiry_id");
    
    if ($enquirydata)
    {
        $enquiry_for = $enquirydata[0]['enquiry_for'];
        if ($enquiry_for == 'Buy')
        {
            $enquiry_for = 'Sale';
        }

        if ($enquiry_for == 'Lease')
        {
            $enquiry_for = 'Rent/Lease';
        }

        $enquiry_type = $enquirydata[0]['enquiry_type'];
        $preferred_project_id = $enquirydata[0]['preferred_project_id'];
        $preferred_area_id = $enquirydata[0]['preferred_area_id'];
        $budget_range1 = $enquirydata[0]['budget_range1'];
        $budget_range2 = $enquirydata[0]['budget_range2'];
        $bedrooms = $enquirydata[0]['bedrooms'];
        $bath = $enquirydata[0]['bath'];
    }
    $bo_id = $session['bo_id'];
    $role = $session['role'];

    $ifAdmin = false;
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $ifAdmin = true; 
    }

    $sql = "SELECT * from property as a LEFT JOIN project as b on a.project_id = b.project_id ";

    if (!$ifAdmin)
    {
        $sql .= " LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id ";
    }

    $sql .= " WHERE ";
    
    if ($enquiry_for !="")
    {
        $sql .= " a.property_for = '$enquiry_for' and ";
    }

    if ($enquiry_type !="")
    {
        $sql .= " a.propsubtype = '$enquiry_type' and ";
    }

    if ($preferred_project_id !=0)
    {
        $sql .= " a.project_id  = $preferred_project_id and ";
    }

    if ($preferred_area_id !=0)
    {
        $sql .= " a.area_id  = $preferred_area_id and ";
    }

    if ($budget_range1 >0 or $budget_range2 >0)
    {
        $sql .= " ( a.exp_price >=  $budget_range1 and a.exp_price <= $budget_range2) ";
    }
    
    /*if ($bedrooms >0)
    {
        $sql .= " ( a.bedrooms = $bedrooms) and ";
    }

    if ($bath >0)
    {
        $sql .= " ( a.bathrooms = $bath) and ";
    }

    $sql .= " a.deal_done != 'Yes' ";*/

    if (!$ifAdmin)
    {
            $sql .= " and z.bo_id = $bo_id "; 
    }   
    $sql .= " ORDER BY a.property_for"; 

    $projects = $db->getAllRecords($sql);
    echo json_encode($projects);


});

$app->get('/selectusers', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT a.user_id, CONCAT(b.salu,' ',b.fname,' ',b.lname) as name,CONCAT(IFNULL(d.team_name,''),' - ', IFNULL(c.sub_team_name,'')) as sub_team_name from users as a LEFT JOIN employee as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON b.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON b.teams  = d.team_id WHERE a.status !='InActive' and a.username!='admin' ORDER BY b.lname,b.fname ";

    //$sql = "SELECT a.user_id, CONCAT(b.salu,' ',b.fname,' ',b.lname) as name,  IFNULL(c.sub_team_name,'') as sub_team_name from users as a LEFT JOIN employee as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON b.sub_teams  = c.sub_team_id WHERE a.status !='InActive' and a.username!='admin' ORDER BY b.lname,b.fname";


    $users = $db->getAllRecords($sql);
    echo json_encode($users);
});

$app->get('/select_assign_to/:teams/:sub_teams', function($teams,$sub_teams) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $team_query = " ( ";  
    $tteams = explode(',', $teams); 

    $first_record = "Yes";

    foreach($tteams as $value)
    {
        if ($first_record == 'Yes')
        {
            $team_query .= " ";
            $first_record = "No";
            $team_query .= " FIND_IN_SET(".$value." , b.teams) ";
            
        }
        else
        {
            $team_query .= " OR FIND_IN_SET(".$value." , b.teams) ";
        }
    }
    $team_query .= " ) "; 
    $first_record = "Yes";
    if ($sub_teams==0 || $sub_teams=='undefined')
    {

    }
    else
    {
        $tsub_teams = explode(',', $sub_teams); 
        foreach($tsub_teams as $value)
        {
            if ($first_record == 'Yes')
            {
                $team_query .= " and (";
                $first_record = "No";
                $team_query .= " FIND_IN_SET(".$value." , b.sub_teams) ";
                
            }
            else
            {
                $team_query .= " OR FIND_IN_SET(".$value." , b.sub_teams) ";
            }
        }
    }
    if ($first_record == 'No')
    {
        $team_query .= " ) "; 
    }




    $sql = "SELECT a.user_id, CONCAT(b.salu,' ',b.fname,' ',b.lname) as name, CONCAT(IFNULL(d.team_name,''),' - ',  IFNULL(c.sub_team_name,'')) as sub_team_name from users as a LEFT JOIN employee as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON b.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON b.teams  = d.team_id WHERE a.status !='InActive' and a.username!='admin' and ".$team_query." ORDER BY b.lname,b.fname ";
    
    $users = $db->getAllRecords($sql);
    echo json_encode($users);
});


$app->get('/selectusers_employee_type/:employee_type', function($employee_type) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    

    $sql = "SELECT a.user_id, CONCAT(b.salu,' ',b.fname,' ',b.lname) as name,CONCAT(IFNULL(d.team_name,''),' - ', IFNULL(c.sub_team_name,'')) as sub_team_name from users as a LEFT JOIN employee as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON b.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON b.teams  = d.team_id WHERE a.status !='InActive' and a.username!='admin' and b.employee_type = '$employee_type' ORDER BY b.lname,b.fname";
    $users = $db->getAllRecords($sql);
    echo json_encode($users);
});



$app->get('/selectusers_subordinates', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];
    $role = $session['role'];
    
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR FIND_IN_SET('$value', a.teams) ";
    }

    $user = $db->getOneRecord("select emp_id from users where user_id= $user_id ");
    if ($user != NULL) 
    {
        $emp_id = $user['emp_id'];
    }


    $sql = "SELECT b.user_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name,CONCAT(IFNULL(d.team_name,''),' - ', IFNULL(c.sub_team_name,'')) as sub_team_name from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON a.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON a.teams  = d.team_id WHERE (b.user_id = $user_id or a.manager_id = $emp_id) and b.status !='InActive' and b.username!='admin' ORDER BY a.lname,a.fname";

    if ($user_id==58  )
    {
        $sql = "SELECT b.user_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name,CONCAT(IFNULL(d.team_name,''),' - ',  IFNULL(c.sub_team_name,'')) as sub_team_name from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON a.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON a.teams  = d.team_id WHERE (b.user_id = $user_id or a.manager_id = $emp_id ".$team_query." ) and b.status !='InActive' and b.username!='admin' ORDER BY a.lname,a.fname";
    }
 
    if (in_array("Admin", $role) || in_array("Human Resource new",$role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT b.user_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name, CONCAT(IFNULL(d.team_name,''),' - ', IFNULL(c.sub_team_name,'')) as sub_team_name from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON a.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON a.teams  = d.team_id WHERE b.status !='InActive' and b.username!='admin' ORDER BY a.lname,a.fname";
    }
    //echo $sql;
    $users = $db->getAllRecords($sql);
    echo json_encode($users);
});


$app->get('/getsubodrniate/:teams/:sub_teams', function($teams,$sub_teams) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];
    $role = $session['role'];
    
    $team_query = " (";   
    $tteams = explode(",",$teams);
    $first = "Yes";
    foreach($tteams as $value)
    {
        if ($first == "Yes")
        {
            $team_query .= " FIND_IN_SET('$value', a.teams) ";
            $first = "No";
        }
        else
        {
            $team_query .= " OR FIND_IN_SET('$value', a.teams) ";
        }
    }
    $first = "Yes";
    $team_query .= " ) ";
    if ($sub_teams=="undefined")
    {
    }
    else
    {
        $tsub_teams = explode(",",$sub_teams);
        foreach($tsub_teams as $value)
        {
            if ($first == "Yes")
            {
                $team_query .= " and ( FIND_IN_SET('$value', a.sub_teams) ";
                $first = "No";
            }
            else
            {
                $team_query .= " OR FIND_IN_SET('$value', a.sub_teams) ";
            }
        }
    }
    if ($first=='No')
    {
        $team_query .= " ) ";
    }
    $user = $db->getOneRecord("select emp_id from users where user_id= $user_id ");
    if ($user != NULL) 
    {
        $emp_id = $user['emp_id'];
    }



    //$sql = "SELECT b.user_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name,CONCAT(IFNULL(d.team_name,''),' - ', IFNULL(c.sub_team_name,'')) as sub_team_name from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON a.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON a.teams  = d.team_id WHERE (b.user_id = $user_id or a.manager_id = $emp_id) and b.status !='InActive' and b.username!='admin' and ".$team_query." ORDER BY a.lname,a.fname";

    $sql = "SELECT b.user_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name,CONCAT(IFNULL(e.designation,'')) as sub_team_name from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON a.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON a.teams  = d.team_id LEFT join designation as e on a.designation_id = e.designation_id WHERE (b.user_id = $user_id or a.manager_id = $emp_id) and b.status !='InActive' and b.username!='admin' and ".$team_query." ORDER BY a.lname,a.fname";

    if ($user_id==58  )
    {
        $sql = "SELECT b.user_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name,CONCAT(IFNULL(e.designation,'')) as sub_team_name  from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON a.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON a.teams  = d.team_id LEFT join designation as e on a.designation_id = e.designation_id WHERE (b.user_id = $user_id or a.manager_id = $emp_id ) and ".$team_query." and b.status !='InActive' and b.username!='admin' ORDER BY a.lname,a.fname";
    }
 
    if (in_array("Admin", $role) || in_array("Human Resource new",$role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT b.user_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name,CONCAT(IFNULL(e.designation,'')) as sub_team_name from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON a.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON a.teams  = d.team_id LEFT join designation as e on a.designation_id = e.designation_id WHERE b.status !='InActive' and b.username!='admin' and ".$team_query." ORDER BY a.lname,a.fname";

        //$sql = "SELECT b.user_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name, CONCAT(IFNULL(e.designation,'')) as sub_team_name from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON a.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON a.teams  = d.team_id LEFT join designation as e on a.designation_id = e.designation_id WHERE b.status !='InActive' and b.username!='admin' ORDER BY a.lname,a.fname";
    }
    //echo $sql;
    $users = $db->getAllRecords($sql);
    $users[0]['sql']=$sql;
    echo json_encode($users);
});

$app->get('/getassign_to_list/:teams/:sub_teams', function($teams,$sub_teams) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];
    $role = $session['role'];
    
    $team_query = " (";   
    $tteams = explode(",",$teams);
    $first = "Yes";
    foreach($tteams as $value)
    {
        if ($first == "Yes")
        {
            $team_query .= " FIND_IN_SET('$value', a.teams) ";
            $first = "No";
        }
        else
        {
            $team_query .= " OR FIND_IN_SET('$value', a.teams) ";
        }
    }
    $first = "Yes";
    $team_query .= " ) ";
    if ($sub_teams=="undefined")
    {
    }
    else
    {
        $tsub_teams = explode(",",$sub_teams);
        foreach($tsub_teams as $value)
        {
            if ($first == "Yes")
            {
                $team_query .= " and ( FIND_IN_SET('$value', a.sub_teams) ";
                $first = "No";
            }
            else
            {
                $team_query .= " OR FIND_IN_SET('$value', a.sub_teams) ";
            }
        }
    }
    if ($first=='No')
    {
        $team_query .= " ) ";
    }
    


    $sql = "SELECT b.user_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name,CONCAT(IFNULL(d.team_name,''),' - ', IFNULL(c.sub_team_name,'')) as sub_team_name from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON a.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON a.teams  = d.team_id WHERE and b.status !='InActive' and b.username!='admin' and ".$team_query." ORDER BY a.lname,a.fname";

    if (in_array("Admin", $role) || in_array("Human Resource new",$role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT b.user_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name, ,CONCAT(IFNULL(d.team_name,''),' - ', IFNULL(c.sub_team_name,'')) as sub_team_name from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN sub_teams as c ON a.sub_teams  = c.sub_team_id LEFT JOIN teams as d ON a.teams  = d.team_id WHERE b.status !='InActive' and b.username!='admin' ORDER BY a.lname,a.fname";
    }
    //echo $sql;
    $users = $db->getAllRecords($sql);
    $users[0]['sql']=$sql;
    echo json_encode($users);
});



// DOCUMENT

$app->get('/document_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,a.category from tdocument as a LEFT JOIN attachments as b ON a.tdocument_id = b.category_id and b.category = 'document'  GROUP BY a.tdocument_id ORDER BY a.category, a.group_title, a.doc_title";
    $tdocuments = $db->getAllRecords($sql);
    echo json_encode($tdocuments);
});


$app->post('/document_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('doc_title'),$r->tdocument);
    $db = new DbHandler();

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->tdocument->created_by = $created_by;
    $r->tdocument->created_date = $created_date;

    $doc_title = $r->tdocument->doc_title;
    $doc_type = $r->tdocument->doc_type;
    /*$istdocumentExists = $db->getOneRecord("select 1 from tdocument where doc_title='$doc_title' and doc_type = '$doc_type' ");
    if(!$istdocumentExists){*/
        $tabble_name = "tdocument";
        $column_names = array('category','group_title','description','doc_type','doc_title','file_name','priority','status','created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->tdocument, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Document created successfully";
            $response["tdocument_id"] = $result;
            $_SESSION['tmptdocument_id'] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Document. Please try again";
            echoResponse(201, $response);
        }            
    /*}else{
        $response["status"] = "error";
        $response["message"] = "A Document with the provided Document exists!";
        echoResponse(201, $response);
    }*/
});

$app->post('/tdocument_uploads', function() use ($app) {
    session_start();
    $tdocument_id = $_SESSION['tmptdocument_id'];
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
        $file_names = "d_".$tdocument_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "document". $ds. $file_names;

        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, created_by, created_date)   VALUES('document', '$tdocument_id','$file_names','Yes','Yes','Main','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
});


$app->post('/uploads_files_task', function() use ($app) {
    session_start();
    $task_id = $_SESSION['tmptask_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_task'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_task'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "t_".$task_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "task". $ds. $file_names;

        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, created_by, created_date)   VALUES('task', '$task_id','$file_names','Yes','Yes','Main','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
});

$app->get('/document_edit_ctrl/:tdocument_id', function($tdocument_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT  from tdocument where tdocument_id=".$tdocument_id;
    $tdocuments = $db->getAllRecords($sql);
    echo json_encode($tdocuments);
    
});


$app->post('/document_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('tdocument_id'),$r->tdocument);
    $db = new DbHandler();
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:m:s
');
    $r->tdocument->modified_by = $modified_by;
    $r->tdocument->modified_date = $modified_date;

    $tdocument_id  = $r->tdocument->tdocument_id;
    $istdocumentExists = $db->getOneRecord("select 1 from tdocument where tdocument_id=$tdocument_id");
    if($istdocumentExists){
        $tabble_name = "tdocument";
        $column_names = array('category','group_title','description','doc_type','doc_title','file_name','priority','status','modified_by','modified_date');
        $condition = "tdocument_id='$tdocument_id'";
        $result = $db->updateIntoTable($r->tdocument, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Document Updated successfully";
            $_SESSION['tmptdocument_id'] = $tdocument_id;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Document. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Document with the provided Document does not exists!";
        echoResponse(201, $response);
    }
});

/*$app->post('/document_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('tdocument_id'),$r->tdocument);
    $db = new DbHandler();
    $tdocument_id  = $r->tdocument->tdocument_id;
    $istdocumentExists = $db->getOneRecord("select 1 from tdocument where tdocument_id=$tdocument_id");
    if($istdocumentExists){
        $tabble_name = "tdocument";
        $column_names = array('group_title','description','doc_type','doc_title','file_name','priority','status');
        $condition = "tdocument_id='$tdocument_id'";
        $result = $db->deleteIntoTable($r->tdocument, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Document Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Document. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Document with the provided Document does not exists!";
        echoResponse(201, $response);
    }
});*/


$app->get('/document_delete/:tdocument_id', function($tdocument_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "DELETE from tdocument where tdocument_id = $tdocument_id ";
    $result = $db->insertByQuery($sql);
});


$app->get('/selectdocument', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from tdocument ORDER BY group_title,doc_title";
    $tdocuments = $db->getAllRecords($sql);
    echo json_encode($tdocuments);
});

$app->get('/showdocuments', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $role = $session['role'];
    $user_id = $session['user_id'];
    $sub_team_name = "";
    $user = $db->getOneRecord("select a.emp_id,b.sub_team_id,b.sub_team_name from employee as a LEFT JOIN sub_teams as b ON b.sub_team_id =  a.sub_teams LEFT JOIN users as c ON a.emp_id = c.emp_id where c.user_id= $user_id ");
    if ($user != NULL) 
    {
        $sub_team_name = $user['sub_team_name'];
    }

    if (in_array("Admin", $role) || in_array("HR", $role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT *,a.category from tdocument as a LEFT JOIN attachments as b ON a.tdocument_id = b.category_id and b.category = 'document'  GROUP BY a.tdocument_id ORDER BY a.category, a.group_title, a.doc_title";
    }
    else
    {
        $sql = "SELECT *,a.category from tdocument as a LEFT JOIN attachments as b ON a.tdocument_id = b.category_id and b.category = 'document' WHERE a.category = '$sub_team_name' GROUP BY a.tdocument_id ORDER BY a.category, a.group_title, a.doc_title";
    }
    

    $showdocuments = $db->getAllRecords($sql);
    $showdocuments[0]['sql'] = $sql;
    echo json_encode($showdocuments);
});





$app->get('/getfromproperty/:id', function($id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT *,a.assign_to as assign_to, a.teams as teams,a.created_by,a.modified_by from property as a LEFT JOIN project as b on a.project_id = b.project_id where a.property_id=".$id;
    $getproperties = $db->getAllRecords($sql);
    echo json_encode($getproperties);
    
});

$app->get('/getfromproject/:id', function($id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];

    $sql = "SELECT *,a.assign_to as assign_to, a.teams as teams,a.created_by,a.modified_by from project as a where a.project_id=".$id;//." and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";
    $getprojects = $db->getAllRecords($sql);
    echo json_encode($getprojects);
    
});

$app->get('/getowner_developer/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT propfrom,dev_owner_id FROM property where property_id=".$property_id;
    $getproperties = $db->getAllRecords($sql);
    echo json_encode($getproperties);
    
});

$app->get('/getenquiry_buyer/:enquiry_id', function($enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT client_id FROM enquiry where enquiry_id=".$enquiry_id;
    $getenquiries = $db->getAllRecords($sql);
    echo json_encode($getenquiries);
    
});

$app->get('/getfromenquiry/:id', function($id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from enquiry where enquiry_id=".$id;
    $getenquiries = $db->getAllRecords($sql);
    echo json_encode($getenquiries);
});

$app->get('/getfromactivity/:id', function($id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,c.dev_owner_id as contact_id, DATE_FORMAT(b.start_date,'%d/%m/%Y %H:%i') AS start_date, DATE_FORMAT(b.end_date,'%d/%m/%Y %H:%i') AS end_date from activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id LEFT JOIN property as c on c.property_id = b.property_id where a.activity_id=".$id;
    $getproperties = $db->getAllRecords($sql);
    echo json_encode($getproperties);
});



// EXPENSES

$app->get('/expense_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];   
    $role = $session['role'];

    $sql= "";
    if (in_array("Admin", $role) || in_array("Accountant", $role) || in_array("Branch Head", $role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.expense_date,'%d-%m-%Y') AS expense_date, d.expense_title,e.team_name,f.sub_team_name FROM expense as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id LEFT JOIN expense_head as d ON a.expense_head_id = d.expense_head_id LEFT JOIN teams as e ON a.teams = e.team_id LEFT JOIN sub_teams as f ON a.sub_teams = f.sub_team_id ORDER BY a.expense_date DESC";
    }
    else
    {
        $sql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.expense_date,'%d-%m-%Y') AS expense_date, d.expense_title,e.team_name,f.sub_team_name FROM expense as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id LEFT JOIN expense_head as d ON a.expense_head_id = d.expense_head_id LEFT JOIN teams as e ON a.teams = e.team_id LEFT JOIN sub_teams as f ON a.sub_teams = f.sub_team_id WHERE (a.created_by = $user_id or a.modified_by = $user_id) ORDER BY a.expense_date DESC";
    }
    $expenselist = $db->getAllRecords($sql);
    echo json_encode($expenselist);
});


$app->post('/expense_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('expense_head_id'),$r->expensedata);
    $db = new DbHandler();

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->expensedata->created_by = $created_by;
    $r->expensedata->created_date = $created_date;

    if (isset($r->expensedata->expense_date))
    {
        $texpense_date = substr($r->expensedata->expense_date,6,4)."-".substr($r->expensedata->expense_date,3,2)."-".substr($r->expensedata->expense_date,0,2)." ".substr($r->expensedata->expense_date,11,5);
        $r->expensedata->expense_date = $texpense_date;
    }
    $tabble_name = "expense";
    
    $column_names = array('expense_type', 'expense_head_id', 'user_id', 'amount',  'expense_no', 'expense_date', 'comments', 'teams', 'sub_teams', 'payment_type', 'created_by', 'created_date');
    
    $multiple=array("");
    
    $result = $db->insertIntoTable($r->expensedata, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Expense Entry successfully";
        $response["expense_id"] = $result;
        $_SESSION['tmpexpense_id'] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to Add Expense Entry. Please try again";
        echoResponse(201, $response);
    }
});

$app->post('/expense_uploads', function() use ($app) {
    session_start();
    $expense_id = $_SESSION['tmpexpense_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_documents'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_documents'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "exp_".$expense_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "expense_docs". $ds. $file_names;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;
            
            $query = "INSERT INTO attachments (category, category_id, filenames, created_by, created_date)   VALUES('expense_docs', '$expense_id','$file_names','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->get('/expense_documents/:expense_id', function($expense_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'expense_docs' and category_id = $expense_id";
    $expense_documents = $db->getAllRecords($sql);
    echo json_encode($expense_documents);
});

$app->get('/expense_edit_ctrl/:expense_id', function($expense_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,DATE_FORMAT(expense_date,'%d-%m-%Y') AS expense_date from expense where expense_id=".$expense_id;
    $expenses = $db->getAllRecords($sql);
    echo json_encode($expenses);
    
});

$app->post('/expense_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('expense_id'),$r->expensedata);
    $db = new DbHandler();
    $expense_id  = $r->expensedata->expense_id;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->expensedata->modified_by = $modified_by;
    $r->expensedata->modified_date = $modified_date;
    
    if (isset($r->expensedata->expense_date))
    {
        $texpense_date = substr($r->expensedata->expense_date,6,4)."-".substr($r->expensedata->expense_date,3,2)."-".substr($r->expensedata->expense_date,0,2)." ".substr($r->expensedata->expense_date,11,5);
        $r->expensedata->expense_date = $texpense_date;
    }

    $isexpenseExists = $db->getOneRecord("select 1 from expense where expense_id=$expense_id");
    if($isexpenseExists){
        $tabble_name = "expense";
        $column_names = array('expense_type', 'expense_head_id', 'user_id', 'amount',  'expense_no', 'expense_date', 'comments', 'teams', 'sub_teams', 'payment_status', 'modified_by','modified_date');
        $multiple=array("");
        $condition = "expense_id='$expense_id'";
        $history = $db->historydata( $r->expensedata, $column_names, $tabble_name,$condition,$activity_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->expensedata, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Expense Entry Updated successfully";
            $_SESSION['tmpexpense_id'] = $expense_id;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Updated Expense Entry. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Expense Id with the provided Expense Id does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/expense_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('expense_id'),$r->expensedata);
    $db = new DbHandler();
    $expense_id  = $r->expensedata->expense_id;
    $isexpenseExists = $db->getOneRecord("select 1 from expense where expense_id=$expense_id");
    if($isexpenseExists){
        $tabble_name = "expense";
        $column_names = array('expense_type', 'expense_head_id', 'user_id', 'amount',  'expense_no', 'expense_date', 'comments', 'teams', 'modified_by','modified_date');
        $condition = "expense_id='$expense_id'";
        $result = $db->deleteIntoTable($r->expensedata, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Expense Entry Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Deleted Expense Entry. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Expense ID with the provided Expense ID does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectexpense', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from expense ORDER BY expense_date DESC";
    $expenses = $db->getAllRecords($sql);
    echo json_encode($expenses);
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

$app->post('/search_expenses', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());

    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];

    $role = $session['role'];


    $searchsql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.expense_date,'%d-%m-%Y') AS expense_date, d.expense_title,e.team_name  FROM expense as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id LEFT JOIN expense_head as d ON a.expense_head_id = d.expense_head_id LEFT JOIN teams as e ON a.teams = e.team_id ORDER BY a.expense_date DESC";

    if (in_array("Admin", $role) || in_array("Accountant", $role) || in_array("Branch Head", $role) || in_array("Sub Admin", $role))
    {
        $searchsql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.expense_date,'%d-%m-%Y') AS expense_date, d.expense_title,e.team_name  FROM expense as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id LEFT JOIN expense_head as d ON a.expense_head_id = d.expense_head_id LEFT JOIN teams as e ON a.teams = e.team_id WHERE a.created_by > 0 ";
    }
    else
    {
        $searchsql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.expense_date,'%d-%m-%Y') AS expense_date, d.expense_title,e.team_name  FROM expense as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id LEFT JOIN expense_head as d ON a.expense_head_id = d.expense_head_id LEFT JOIN teams as e ON a.teams = e.team_id WHERE a.created_by > 0 and (a.created_by = $user_id or a.modified_by = $user_id) ";
        
    }

    $sql = " ";
    
    if (isset($r->searchdata->user_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->user_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                } 
                $sql .= " a.user_id = ".$value." ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }

    if (isset($r->searchdata->expense_head_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->expense_head_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                } 
                $sql .= " a.expense_head_id = ".$value." ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }


    if (isset($r->searchdata->teams))
    {
        $first = "Yes";
        $new_data = $r->searchdata->teams;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                } 
                $sql .= " a.teams LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->expense_date_from))
    {
        $expense_date_from = $r->searchdata->expense_date_from;
        $texpense_date_from = substr($expense_date_from,6,4)."-".substr($expense_date_from,3,2)."-".substr($expense_date_from,0,2);
        if (isset($r->searchdata->expense_date_to))
        {
            $expense_date_to = $r->searchdata->expense_date_to;
            $texpense_date_to = substr($expense_date_to,6,4)."-".substr($expense_date_to,3,2)."-".substr($expense_date_to,0,2);
        }
        $sql .= " and a.expense_date BETWEEN '".$texpense_date_from."' AND '".$texpense_date_to."' ";
    }
    $searchsql .=  $sql .  " ORDER BY a.expense_date DESC";
    
    
    $expenselist = $db->getAllRecords($searchsql);
    echo json_encode($expenselist);
});


$app->get('/expense_report/:start_date/:end_date/:expense_head_id/:user_id/:teams', function($start_date,$end_date,$expense_head_id,$user_id,$teams) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $senddata =  array();
    $htmldata = array(); 

    $team_query = " ";  
      
    if ($teams)
    {
        if ($teams!=undefined)
        {
            $team_query = " and ( ";
            $tteams = explode(' ', $teams); 

            $first_record = "Yes";

            foreach($tteams as $value)
            {
                if ($first_record == 'Yes')
                {
                    $team_query .= " ";
                    $first_record = "No";
                    $team_query .= " FIND_IN_SET(".$value." , a.teams) ";                    
                }
                else
                {
                    $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";                
                }
            }
            $team_query .= " ) "; 
        }
    }

    $expense_sql = " ";

    if ($expense_head_id>0)
    {
        $expense_sql = " and a.expense_head_id = ".$expense_head_id." ";
    }

    $user_sql = " ";
    if ($user_id>0)
    {
        $user_sql = " and a.user_id = ".$user_id." ";
    }
    
    $sql = "";

    $expense_data = '<div style="margin-top:25px;">
                        <table class="table table-bordered table-striped" >
                            <thead>
                                <tr>
                                    <th>Expense ID</th>
                                    <th>Expene Type</th>
                                    <th>Account</th>
                                    <th>Voucher No</th>
                                    <th>Voucher Date</th>
                                    <th>Emp Name</th>
                                    <th>Team</th>
                                    <th class="numbers">Amount</th>
                                </tr>
                            </thead>
                        <tbody>';


    $sql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.expense_date,'%d-%m-%Y') AS expense_date, d.expense_title,e.team_name  FROM expense as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id LEFT JOIN expense_head as d ON a.expense_head_id = d.expense_head_id LEFT JOIN teams as e ON a.teams = e.team_id WHERE a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' ".$expense_sql." ".$team_query." ".$user_sql." ORDER BY a.expense_date ";

    $stmt = $db->getRows($sql);
    $total_amount = 0;
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $expense_data .='<tr><td><a href="#/expense_edit/'.$row['expense_id'].'  title="Edit">ExpenseID_'.$row['expense_id'].'</a></td>
                            <td>'.$row['expense_type'].'</td>
                            <td>'.$row['expense_title'].'</td>
                            <td>'.$row['expense_no'].'</td>
                            <td>'.$row['expense_date'].'</td>
                            <td>'.$row['emp_name'].'</td>
                            <td>'.$row['team_name'].'</td>
                            <td class="numbers">'.$row['amount'].'</td></tr>';   

            $total_amount = $total_amount + $row['amount'];       
        }
    }
    $expense_data .='<tr><td></td><td></td><td></td><td></td><td></td><td></td>
                            <td style="text-align:right;font-weight:700;">TOTAL</td>
                            <td class="numbers">'.$total_amount.'</td></tr>';

    $expense_data .= '</table></div>';
    $htmldata['htmlstring'] = $expense_data;
    $htmldata['sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


// FRANCHISEE REPORTS

$app->get('/employeedata_report/:start_date/:end_date/:user_id/:teams/:sub_teams', function($start_date,$end_date,$user_id,$teams,$sub_teams) use ($app) {
    
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    //$start_date .= " 00:00:00";
    //$end_date   .= " 23:59:59";

    $month_dis = array();
    $month_dis[1] = "jan";
    $month_dis[2] = "feb";
    $month_dis[3] = "mar";
    $month_dis[4] = "apr";
    $month_dis[5] = "may"; 
    $month_dis[6] = "jun";
    $month_dis[7] = "jul";
    $month_dis[8] = "aug";
    $month_dis[9] = "sep";
    $month_dis[10] = "oct";
    $month_dis[11] = "nov";
    $month_dis[12] = "dec";
    
    $senddata =  array();
    $htmldata = array(); 

    // $team_query = " ";  
    // $uteam_query = " ";  
        
    // if ($user_id>0)
    // {
    //     $team_query = " ( FIND_IN_SET(".$user_id." , a.assign_to)) ";
    //     $uteam_query = " ( FIND_IN_SET(".$user_id." , a.user_id)) ";
    // }
    // else
    // {
    //     $team_query = " ( ";
    //     $uteam_query = " ( ";  
    //     $tteams = explode(' ', $teams); 

    //     $first_record = "Yes";

    //     foreach($tteams as $value)
    //     {
    //         if ($first_record == 'Yes')
    //         {
    //             $team_query .= " ";
    //             $uteam_query .= " ";
    //             $first_record = "No";
    //             $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
    //             $uteam_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
    //         }
    //         else
    //         {
    //             $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
    //             $uteam_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
    //         }
    //     }
    //     $team_query .= " ) "; 
    //     $uteam_query .= " ) ";
        
    //     $sub_team_query = " ( ";
    //     $sub_uteam_query = " ( ";  
    //     $tsub_teams = explode(' ', $sub_teams); 

    //     $first_record = "Yes";

    //     foreach($tsub_teams as $value)
    //     {
    //         if ($first_record == 'Yes')
    //         {
    //             $sub_team_query .= " ";
    //             $sub_uteam_query .= " ";
    //             $first_record = "No";
    //             $sub_team_query .= " FIND_IN_SET(".$value." , a.sub_teams) ";
    //             $sub_uteam_query .= " FIND_IN_SET(".$value." , a.sub_teams) ";
                
    //         }
    //         else
    //         {
    //             $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
    //             $sub_uteam_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
    //         }
    //     }
    //     $sub_team_query .= " ) "; 
    //     $sub_uteam_query .= " ) ";
    // }
    
    // $transactions = 0;    
    // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." ";
    // $tsql = $sql;
    // $transactionsdata = $db->getAllRecords($sql);
    // $tsql = $sql;
    // if ($transactionsdata)
    // {
    //      $transactions = $transactionsdata[0]['transactions'];
    // }
    
    // $transactions_commercial=0;
    
    if(($teams != 0) && ($sub_teams != 0)){
        
   
        // ----------------------pks 2023 start--------------------------
        $team_query = " ( ";  
        // $sub_team_query = " ( ";
        // $sub_team_query = "";
        $sub_team_q = [];
        $tteams = explode(' ', $teams); 
        if($sub_teams === 'undefined'){
            $sub_t_teams = '';
            $sub_teams_pks = array();
        }else{
            $sub_t_teams = explode(' ', $sub_teams);
            $sub_teams_pks = explode(',', $sub_teams);
        }
    
        $first_record = "Yes";
        $second_record = "Yes";
    
        foreach($tteams as $value)
        {
            if ($first_record == 'Yes')
            {
                $team_query .= " ";
                $first_record = "No";
                $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
            }
            else
            {
                $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
            }
        }
        $team_query .= " ) ";  
     
        if(sizeof($sub_teams_pks) != 0){
            foreach($sub_teams_pks as $value)
            {
                if ($second_record == 'Yes')
                {
                    // $sub_team_query .= " ";
                    $second_record = "No";
                    $sub_team_q[] = "and FIND_IN_SET(".$value." , a.sub_teams) ";
                }
                else
                {
                    $sub_team_q[] = "OR FIND_IN_SET(".$value." , a.sub_teams) ";
                }
            }
        }
        $sub_team_query = implode(" ", $sub_team_q);
        

        // if($sub_t_teams != ""){
        //     foreach($sub_t_teams as $value)
        //     {
        //         if ($second_record == 'Yes')
        //         {
        //             // $sub_team_query .= " ";
        //             $second_record = "No";
        //             $sub_team_query = " and FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //         else
        //         {
        //             $sub_team_query = " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //     }
        // }
        
        
        
        // ----------------------pks 2023 end--------------------------
        // $sub_team_query .= " ) "; 
        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and ".$team_query."".$sub_team_query." ";
        // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $tsql = $sql;     
        
        // error_log("pks is here", 3, "logfile1.log");
        // error_log($sql, 3, "logfile1.log");
        
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
             $transactions = $transactionsdata[0]['transactions'];
        }
        
        $transactions_commercial=0;
    
        // error_log(print_r($sub_team_query, true), 3, "logfile1.log");
        
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query."".$sub_team_query." " ;

        // error_log($sql, 3, "logfile1.log");
        
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_commercial = $transactionsdata[0]['transactions_commercial'];
        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query."".$sub_team_query."  ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_retail = $transactionsdata[0]['transactions_retail'];

        }

        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query."".$sub_team_query."  " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_residential = $transactionsdata[0]['transactions_residential'];

        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query."".$sub_team_query."  " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $tsql .= $sql;

        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query."".$sub_team_query."  " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query."".$sub_team_query."  " ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
            $brokerage_received = $transactionsdata[0]['brokerage_received'];
        }

        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' and  ".$uteam_query."".$sub_team_query." ";
        $expense_madedata = $db->getAllRecords($sql );
    
        if ($expense_madedata)
        {
            $expense_made = $expense_madedata[0]['expense_made'];
        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' and  ".$uteam_query."".$sub_team_query."  GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);

        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
            }
        }
        $expense_data .= '</table></div>';

        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' and  ".$uteam_query."".$sub_team_query."  GROUP BY a.user_id";
        $stmt = $db->getRows($sql);

        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';


        $tsql .= $sql;
        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' and  ".$uteam_query."".$sub_team_query."  ";
        $advance_madedata = $db->getAllRecords($sql );
    
        if ($advance_madedata)
        {
            $advance_made = $advance_madedata[0]['advance_made'];

        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                <thead>
                                    <tr>
                                        <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                        <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                    </tr>
                                </thead>
                                <tbody>';
        $temp_contribution = round($emp_contribution,2);
        // $htmlstring .= '    <tr><td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td><td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr>
        //                 <tr><td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td><td></td><td class="numbers"></td><td></td><td></td></tr>
        //                 <tr><td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td><td></td><td></td><td></td><td></td></tr>
        //                 <tr><td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td><td></td><td></td><td></td><td></td></tr>
        //                 <tr><td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td><td></td><td></td><td></td><td></td></tr>
        //                 <tr><td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td><td ></td><td></td><td></td><td></td></tr> <tr><td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td><td></td><td></td><td></td></tr>
        //                 <tr><td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td><td></td><td></td><td></td><td></td></tr>
        //                 <tr><td colspan="6" style="text-align:center;">';
        //                 //$total_income = $brokerage_received-$expense_made-$advance_made;

        //                 $total_income = $transactions-$expense_made-$advance_made-$temp_contribution;

        //                 if ($total_income>0)
        //                 {
        //                     $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
        //                 }
        //                 else{
        //                     $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
        //                 }
        //                 $htmlstring .= '</td></tr>';
                        
        // $htmlstring .= '</tbody></table></div>';
        $htmlstring .= ' <tr>';
        if (in_array(2, $sub_teams_pks)) {
            $htmlstring .= '<td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td>';
        } else {
            $htmlstring .= '<td style="width:16%"></td><td style="width:16%"></td>';
        }
        $htmlstring .= '    <td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr><tr>';
        if (in_array(3, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .= '    <td></td><td class="numbers"></td><td></td><td></td></tr><tr>';
        if (in_array(12, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        if (in_array(4, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        if (in_array(34, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td>';
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td>';
        $htmlstring .=     '<td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td>';
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr>
                            <tr><td colspan="6" style="text-align:center;">';
                            //$total_income = $brokerage_received-$expense_made-$advance_made;
                            $total_income = $transactions-$expense_made-$advance_made;
                            if ($total_income>0)
                            {
                                $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                            }
                            else{
                                $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                            }
                            $htmlstring .= '</td></tr>';
                            
        $htmlstring .= '</tbody></table></div>';
    }elseif(($teams != 0) && ($sub_teams == 0)){
        $team_query = " ( ";  
        // $sub_team_query = " ( ";
        // $sub_team_query = "";
        $tteams = explode(' ', $teams); 
        // if($sub_teams === 'undefined'){
        //     $sub_t_teams = '';
        //     $sub_teams_pks = array();
        // }else{
        //     $sub_t_teams = explode(' ', $sub_teams);
        //     $sub_teams_pks = explode(',', $sub_teams);
        // }
    
        $first_record = "Yes";
        $second_record = "Yes";
    
        foreach($tteams as $value)
        {
            if ($first_record == 'Yes')
            {
                $team_query .= " ";
                $first_record = "No";
                $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
            }
            else
            {
                $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
            }
        }
        $team_query .= " ) ";  
    
     
        // if($sub_t_teams != ""){
        //     foreach($sub_t_teams as $value)
        //     {
        //         if ($second_record == 'Yes')
        //         {
        //             $sub_team_query .= " ";
        //             $second_record = "No";
        //             $sub_team_query .= " and FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //         else
        //         {
        //             $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //     }
        // }
        // $sub_team_query .= " ) ";  
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sub_teams, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
    
        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and ".$team_query." ";
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sql, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
        // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $tsql = $sql;
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
             $transactions = $transactionsdata[0]['transactions'];
        }
        
        $transactions_commercial=0;
    
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;

        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_commercial = $transactionsdata[0]['transactions_commercial'];
        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_retail = $transactionsdata[0]['transactions_retail'];

        }

        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_residential = $transactionsdata[0]['transactions_residential'];

        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $tsql .= $sql;

        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
            $brokerage_received = $transactionsdata[0]['brokerage_received'];
        }

        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' and  ".$uteam_query." ";
        $expense_madedata = $db->getAllRecords($sql );
    
        if ($expense_madedata)
        {
            $expense_made = $expense_madedata[0]['expense_made'];
        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' and  ".$uteam_query."  GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);

        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
            }
        }
        $expense_data .= '</table></div>';

        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' and  ".$uteam_query."  GROUP BY a.user_id";
        $stmt = $db->getRows($sql);

        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';


        $tsql .= $sql;
        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' and  ".$uteam_query." ";
        $advance_madedata = $db->getAllRecords($sql );
    
        if ($advance_madedata)
        {
            $advance_made = $advance_madedata[0]['advance_made'];

        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                <thead>
                                    <tr>
                                        <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                        <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                    </tr>
                                </thead>
                                <tbody>';
        $temp_contribution = round($emp_contribution,2);
        $htmlstring .= '    <tr><td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td><td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr>
                        <tr><td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td><td></td><td class="numbers"></td><td></td><td></td></tr>
                        <tr><td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td><td></td><td></td><td></td><td></td></tr>
                        <tr><td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td><td></td><td></td><td></td><td></td></tr>
                        <tr><td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td><td></td><td></td><td></td><td></td></tr>
                        <tr><td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td><td ></td><td></td><td></td><td></td></tr> <tr><td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td><td></td><td></td><td></td></tr>
                        <tr><td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td><td></td><td></td><td></td><td></td></tr>
                        <tr><td colspan="6" style="text-align:center;">';
                        //$total_income = $brokerage_received-$expense_made-$advance_made;

                        $total_income = $transactions-$expense_made-$advance_made-$temp_contribution;

                        if ($total_income>0)
                        {
                            $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                        }
                        else{
                            $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                        }
                        $htmlstring .= '</td></tr>';
                        
        $htmlstring .= '</tbody></table></div>';
    }else{
        // $team_query = " ( ";  
        // $sub_team_query = " ( ";
        // $sub_team_query = "";
        // $tteams = explode(' ', $teams); 
        // if($sub_teams === 'undefined'){
        //     $sub_t_teams = '';
        //     $sub_teams_pks = array();
        // }else{
        //     $sub_t_teams = explode(' ', $sub_teams);
        //     $sub_teams_pks = explode(',', $sub_teams);
        // }
    
        $first_record = "Yes";
        $second_record = "Yes";
    
        // foreach($tteams as $value)
        // {
        //     if ($first_record == 'Yes')
        //     {
        //         $team_query .= " ";
        //         $first_record = "No";
        //         $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
        //     }
        //     else
        //     {
        //         $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
        //     }
        // }
        // $team_query .= " ) ";  
    
     
        // if($sub_t_teams != ""){
        //     foreach($sub_t_teams as $value)
        //     {
        //         if ($second_record == 'Yes')
        //         {
        //             $sub_team_query .= " ";
        //             $second_record = "No";
        //             $sub_team_query .= " and FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //         else
        //         {
        //             $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //     }
        // }
        // $sub_team_query .= " ) ";  
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sub_teams, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
    
        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' ";
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sql, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
        // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $tsql = $sql;
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
             $transactions = $transactionsdata[0]['transactions'];
        }
        
        $transactions_commercial=0;
    
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;

        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_commercial = $transactionsdata[0]['transactions_commercial'];
        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_retail = $transactionsdata[0]['transactions_retail'];

        }

        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_residential = $transactionsdata[0]['transactions_residential'];

        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $tsql .= $sql;

        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
            $brokerage_received = $transactionsdata[0]['brokerage_received'];
        }

        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' ";
        $expense_madedata = $db->getAllRecords($sql );
    
        if ($expense_madedata)
        {
            $expense_made = $expense_madedata[0]['expense_made'];
        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);

        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
            }
        }
        $expense_data .= '</table></div>';

        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date'  GROUP BY a.user_id";
        $stmt = $db->getRows($sql);

        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';


        $tsql .= $sql;
        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' ";
        $advance_madedata = $db->getAllRecords($sql );
    
        if ($advance_madedata)
        {
            $advance_made = $advance_madedata[0]['advance_made'];

        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                <thead>
                                    <tr>
                                        <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                        <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                    </tr>
                                </thead>
                                <tbody>';
        $temp_contribution = round($emp_contribution,2);
        $htmlstring .= '    <tr><td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td><td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr>
                        <tr><td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td><td></td><td class="numbers"></td><td></td><td></td></tr>
                        <tr><td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td><td></td><td></td><td></td><td></td></tr>
                        <tr><td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td><td></td><td></td><td></td><td></td></tr>
                        <tr><td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td><td></td><td></td><td></td><td></td></tr>
                        <tr><td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td><td ></td><td></td><td></td><td></td></tr> <tr><td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td><td></td><td></td><td></td></tr>
                        <tr><td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td><td></td><td></td><td></td><td></td></tr>
                        <tr><td colspan="6" style="text-align:center;">';
                        //$total_income = $brokerage_received-$expense_made-$advance_made;

                        $total_income = $transactions-$expense_made-$advance_made-$temp_contribution;

                        if ($total_income>0)
                        {
                            $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                        }
                        else{
                            $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                        }
                        $htmlstring .= '</td></tr>';
                        
        $htmlstring .= '</tbody></table></div>';
    }
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$tsql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/agentdata_report/:start_date/:end_date/:user_id/:teams/:sub_teams', function($start_date,$end_date,$user_id,$teams,$sub_teams) use ($app) {
    
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    //$start_date .= " 00:00:00";
    //$end_date   .= " 23:59:59";

    $month_dis = array();
    $month_dis[1] = "jan";
    $month_dis[2] = "feb";
    $month_dis[3] = "mar";
    $month_dis[4] = "apr";
    $month_dis[5] = "may"; 
    $month_dis[6] = "jun";
    $month_dis[7] = "jul";
    $month_dis[8] = "aug";
    $month_dis[9] = "sep";
    $month_dis[10] = "oct";
    $month_dis[11] = "nov";
    $month_dis[12] = "dec";
    
    $senddata =  array();
    $htmldata = array(); 
    // $team_query = " ";  
     
    // if ($user_id>0)
    // {
    //     $team_query = " ( FIND_IN_SET(".$user_id." , a.assign_to)) ";
    //     $uteam_query = " ( FIND_IN_SET(".$user_id." , a.user_id)) ";
    // }
    // else
    // {
    //     $team_query = " ( ";
    //     $uteam_query = " ( ";  
    //     $tteams = explode(' ', $teams); 

    //     $first_record = "Yes";

    //     foreach($tteams as $value)
    //     {
    //         if ($first_record == 'Yes')
    //         {
    //             $team_query .= " ";
    //             $uteam_query .= " ";
    //             $first_record = "No";
    //             $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
    //             $uteam_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
    //         }
    //         else
    //         {
    //             $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
    //             $uteam_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
    //         }
    //     }
    //     $team_query .= " ) "; 
    //     $uteam_query .= " ) "; 
    // }
    
    // $transactions = 0;    
    // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
    // $tsql = $sql;
    // $transactionsdata = $db->getAllRecords($sql);
    // $tsql = $sql;
    // if ($transactionsdata)
    // {
    //      $transactions = $transactionsdata[0]['transactions'];
    // }
     if(($teams != 0) && ($sub_teams != 0)){
        $team_query = " ( ";  
        // $sub_team_query = " ( ";
        // $sub_team_query = "";
        $sub_team_q = [];
        $tteams = explode(' ', $teams); 
        if($sub_teams === 'undefined'){
            $sub_t_teams = '';
            $sub_teams_pks = array();
        }else{
            $sub_t_teams = explode(' ', $sub_teams);
            $sub_teams_pks = explode(',', $sub_teams);
        }
    
        $first_record = "Yes";
        $second_record = "Yes";
    
        foreach($tteams as $value)
        {
            if ($first_record == 'Yes')
            {
                $team_query .= " ";
                $first_record = "No";
                $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
            }
            else
            {
                $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
            }
        }
        $team_query .= " ) ";  
    
    
        if(sizeof($sub_teams_pks) != 0){
            foreach($sub_teams_pks as $value)
            {
                if ($second_record == 'Yes')
                {
                    // $sub_team_query .= " ";
                    $second_record = "No";
                    $sub_team_q[] = "and FIND_IN_SET(".$value." , a.sub_teams) ";
                }
                else
                {
                    $sub_team_q[] = "OR FIND_IN_SET(".$value." , a.sub_teams) ";
                }
            }
        }
        $sub_team_query = implode(" ", $sub_team_q);
     
        // if($sub_t_teams != ""){
        //     foreach($sub_t_teams as $value)
        //     {
        //         if ($second_record == 'Yes')
        //         {
        //             $sub_team_query .= " ";
        //             $second_record = "No";
        //             $sub_team_query .= " and FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //         else
        //         {
        //             $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //     }
        // }
        // $sub_team_query .= " ) ";  
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sub_teams, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
    
        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and ".$team_query."".$sub_team_query." ";
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sql, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
        // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $tsql = $sql;
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
             $transactions = $transactionsdata[0]['transactions'];
        }
        
        $transactions_commercial=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query."".$sub_team_query." " ;

        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_commercial = $transactionsdata[0]['transactions_commercial'];
    
        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query."".$sub_team_query." ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_retail = $transactionsdata[0]['transactions_retail'];
    
        }
    
        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query."".$sub_team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_residential = $transactionsdata[0]['transactions_residential'];
    
        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query."".$sub_team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query."".$sub_team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query."".$sub_team_query." " ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
             $brokerage_received = $transactionsdata[0]['brokerage_received'];
    
        }
    
        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses'  ".$uteam_query."".$sub_team_query." ";
        $expense_madedata = $db->getAllRecords($sql );
        
        if ($expense_madedata)
        {
             $expense_made = $expense_madedata[0]['expense_made'];
    
        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' and  ".$uteam_query."".$sub_team_query." GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $expense_data .= '</table></div>';
    
    
        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' and  ".$uteam_query."".$sub_team_query." GROUP BY a.user_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';
    
    
        $tsql = $sql;
        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' and  ".$uteam_query."".$sub_team_query." ";
        $advance_madedata = $db->getAllRecords($sql );
        
        if ($advance_madedata)
        {
             $advance_made = $advance_madedata[0]['advance_made'];
    
        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                            <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        $temp_contribution = round($emp_contribution,2);
    
        // $htmlstring .= '    <tr><td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td><td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr>
        //                     <tr><td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td><td></td><td class="numbers"></td><td></td><td></td></tr>
        //                     <tr><td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td><td></td><td></td><td></td><td></td></tr>
        //                     <tr><td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td><td></td><td></td><td></td><td></td></tr>
        //                     <tr><td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td><td></td><td></td><td></td><td></td></tr>
        //                     <tr><td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td><td ></td><td></td><td></td><td></td></tr> <tr><td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td><td></td><td></td><td></td></tr>
        //                     <tr><td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td><td></td><td></td><td></td><td></td></tr>
        //                     <tr><td colspan="6" style="text-align:center;">';
        //                     //$total_income = $brokerage_received-$expense_made-$advance_made;
        //                     $total_income = $transactions-$expense_made-$advance_made-$temp_contribution;
        //                     if ($total_income>0)
        //                     {
        //                         $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
        //                     }
        //                     else{
        //                         $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
        //                     }
        //                     $htmlstring .= '</td></tr>';
                            
        // $htmlstring .= '</tbody></table></div>';
        $htmlstring .= ' <tr>';
        if (in_array(2, $sub_teams_pks)) {
            $htmlstring .= '<td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td>';
        } else {
            $htmlstring .= '<td style="width:16%"></td><td style="width:16%"></td>';
        }
        $htmlstring .= '    <td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr><tr>';
        if (in_array(3, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .= '    <td></td><td class="numbers"></td><td></td><td></td></tr><tr>';
        if (in_array(12, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        if (in_array(4, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        if (in_array(34, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td>';
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td>';
        $htmlstring .=     '<td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td>';
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr>
                            <tr><td colspan="6" style="text-align:center;">';
                            //$total_income = $brokerage_received-$expense_made-$advance_made;
                            $total_income = $transactions-$expense_made-$advance_made;
                            if ($total_income>0)
                            {
                                $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                            }
                            else{
                                $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                            }
                            $htmlstring .= '</td></tr>';
                            
        $htmlstring .= '</tbody></table></div>';
    }elseif(($teams != 0) && ($sub_teams == 0)){
        $team_query = " ( ";  
        // $sub_team_query = " ( ";
        // $sub_team_query = "";
        $tteams = explode(' ', $teams); 
        // if($sub_teams === 'undefined'){
        //     $sub_t_teams = '';
        //     $sub_teams_pks = array();
        // }else{
        //     $sub_t_teams = explode(' ', $sub_teams);
        //     $sub_teams_pks = explode(',', $sub_teams);
        // }
    
        $first_record = "Yes";
        $second_record = "Yes";
    
        foreach($tteams as $value)
        {
            if ($first_record == 'Yes')
            {
                $team_query .= " ";
                $first_record = "No";
                $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
            }
            else
            {
                $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
            }
        }
        $team_query .= " ) ";  
    
     
        // if($sub_t_teams != ""){
        //     foreach($sub_t_teams as $value)
        //     {
        //         if ($second_record == 'Yes')
        //         {
        //             $sub_team_query .= " ";
        //             $second_record = "No";
        //             $sub_team_query .= " and FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //         else
        //         {
        //             $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //     }
        // }
        // $sub_team_query .= " ) ";  
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sub_teams, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
    
        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and ".$team_query."";
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sql, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
        // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $tsql = $sql;
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
             $transactions = $transactionsdata[0]['transactions'];
        }
        
        $transactions_commercial=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;

        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_commercial = $transactionsdata[0]['transactions_commercial'];
    
        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_retail = $transactionsdata[0]['transactions_retail'];
    
        }
    
        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_residential = $transactionsdata[0]['transactions_residential'];
    
        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
             $brokerage_received = $transactionsdata[0]['brokerage_received'];
    
        }
    
        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' and  ".$uteam_query." ";
        $expense_madedata = $db->getAllRecords($sql );
        
        if ($expense_madedata)
        {
             $expense_made = $expense_madedata[0]['expense_made'];
    
        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' and  ".$uteam_query." GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $expense_data .= '</table></div>';
    
    
        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' and  ".$uteam_query." GROUP BY a.user_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';
    
    
        $tsql = $sql;
        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' and  ".$uteam_query."".$sub_team_query." ";
        $advance_madedata = $db->getAllRecords($sql );
        
        if ($advance_madedata)
        {
             $advance_made = $advance_madedata[0]['advance_made'];
    
        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                            <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        $temp_contribution = round($emp_contribution,2);
    
        $htmlstring .= '    <tr><td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td><td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr>
                            <tr><td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td><td></td><td class="numbers"></td><td></td><td></td></tr>
                            <tr><td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td><td ></td><td></td><td></td><td></td></tr> <tr><td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td><td></td><td></td><td></td></tr>
                            <tr><td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td colspan="6" style="text-align:center;">';
                            //$total_income = $brokerage_received-$expense_made-$advance_made;
                            $total_income = $transactions-$expense_made-$advance_made-$temp_contribution;
                            if ($total_income>0)
                            {
                                $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                            }
                            else{
                                $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                            }
                            $htmlstring .= '</td></tr>';
                            
        $htmlstring .= '</tbody></table></div>';
    }else{
            // $team_query = " ( ";  
        // $sub_team_query = " ( ";
        // $sub_team_query = "";
        // $tteams = explode(' ', $teams); 
        // if($sub_teams === 'undefined'){
        //     $sub_t_teams = '';
        //     $sub_teams_pks = array();
        // }else{
        //     $sub_t_teams = explode(' ', $sub_teams);
        //     $sub_teams_pks = explode(',', $sub_teams);
        // }
    
        // $first_record = "Yes";
        // $second_record = "Yes";
    
        // foreach($tteams as $value)
        // {
        //     if ($first_record == 'Yes')
        //     {
        //         $team_query .= " ";
        //         $first_record = "No";
        //         $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
        //     }
        //     else
        //     {
        //         $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
        //     }
        // }
        // $team_query .= " ) ";  
    
     
        // if($sub_t_teams != ""){
        //     foreach($sub_t_teams as $value)
        //     {
        //         if ($second_record == 'Yes')
        //         {
        //             $sub_team_query .= " ";
        //             $second_record = "No";
        //             $sub_team_query .= " and FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //         else
        //         {
        //             $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //     }
        // }
        // $sub_team_query .= " ) ";  
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sub_teams, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
    
        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' ";
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sql, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
        // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $tsql = $sql;
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
             $transactions = $transactionsdata[0]['transactions'];
        }
        
        $transactions_commercial=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;

        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_commercial = $transactionsdata[0]['transactions_commercial'];
    
        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_retail = $transactionsdata[0]['transactions_retail'];
    
        }
    
        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_residential = $transactionsdata[0]['transactions_residential'];
    
        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
             $brokerage_received = $transactionsdata[0]['brokerage_received'];
    
        }
    
        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' ";
        $expense_madedata = $db->getAllRecords($sql );
        
        if ($expense_madedata)
        {
             $expense_made = $expense_madedata[0]['expense_made'];
    
        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $expense_data .= '</table></div>';
    
    
        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' GROUP BY a.user_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';
    
    
        $tsql = $sql;
        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' ";
        $advance_madedata = $db->getAllRecords($sql );
        
        if ($advance_madedata)
        {
             $advance_made = $advance_madedata[0]['advance_made'];
    
        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                            <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        $temp_contribution = round($emp_contribution,2);
    
        $htmlstring .= '    <tr><td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td><td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr>
                            <tr><td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td><td></td><td class="numbers"></td><td></td><td></td></tr>
                            <tr><td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td><td ></td><td></td><td></td><td></td></tr> <tr><td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td><td></td><td></td><td></td></tr>
                            <tr><td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td colspan="6" style="text-align:center;">';
                            //$total_income = $brokerage_received-$expense_made-$advance_made;
                            $total_income = $transactions-$expense_made-$advance_made-$temp_contribution;
                            if ($total_income>0)
                            {
                                $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                            }
                            else{
                                $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                            }
                            $htmlstring .= '</td></tr>';
                            
        $htmlstring .= '</tbody></table></div>';
    }

    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$tsql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/franchiseedata_report/:start_date/:end_date/:teams/:sub_teams', function($start_date,$end_date,$teams,$sub_teams) use ($app) {
  
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    //$start_date .= " 00:00:00";
    //$end_date   .= " 23:59:59";

    $month_dis = array();
    $month_dis[1] = "jan";
    $month_dis[2] = "feb";
    $month_dis[3] = "mar";
    $month_dis[4] = "apr";
    $month_dis[5] = "may"; 
    $month_dis[6] = "jun";
    $month_dis[7] = "jul";
    $month_dis[8] = "aug";
    $month_dis[9] = "sep";
    $month_dis[10] = "oct";
    $month_dis[11] = "nov";
    $month_dis[12] = "dec";
    
     $senddata =  array();
    $htmldata = array(); 
    if(($teams != 'undefined') && ($sub_teams != 'undefined')){
        $team_query = " ( ";  
        // $sub_team_query = " ( ";
        // $sub_team_query = "";
        $sub_team_q = [];
        $tteams = explode(' ', $teams); 
        if($sub_teams === 'undefined'){
            $sub_t_teams = '';
            $sub_teams_pks = array();
        }else{
            $sub_t_teams = explode(' ', $sub_teams);
            $sub_teams_pks = explode(',', $sub_teams);
        }
    
        $first_record = "Yes";
        $second_record = "Yes";
    
        foreach($tteams as $value)
        {
            if ($first_record == 'Yes')
            {
                $team_query .= " ";
                $first_record = "No";
                $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
            }
            else
            {
                $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
            }
        }
        $team_query .= " ) ";  
    
    
        if(sizeof($sub_teams_pks) != 0){
            foreach($sub_teams_pks as $value)
            {
                if ($second_record == 'Yes')
                {
                    // $sub_team_query .= " ";
                    $second_record = "No";
                    $sub_team_q[] = "and FIND_IN_SET(".$value." , a.sub_teams) ";
                }
                else
                {
                    $sub_team_q[] = "OR FIND_IN_SET(".$value." , a.sub_teams) ";
                }
            }
        }
        $sub_team_query = implode(" ", $sub_team_q);
     
        // if($sub_t_teams != ""){
        //     foreach($sub_t_teams as $value)
        //     {
        //         if ($second_record == 'Yes')
        //         {
        //             $sub_team_query .= " ";
        //             $second_record = "No";
        //             $sub_team_query .= " and FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //         else
        //         {
        //             $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //     }
        // }
        // $sub_team_query .= " ) ";  
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sub_teams, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
    
        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and ".$team_query."".$sub_team_query." ";
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sql, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
        // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $tsql = $sql;
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
             $transactions = $transactionsdata[0]['transactions'];
        }
        
        $transactions_commercial=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' and ".$team_query."".$sub_team_query." ";
        // $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
    
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_commercial = $transactionsdata[0]['transactions_commercial'];
    
        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' and ".$team_query."".$sub_team_query." ";
        // $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_retail = $transactionsdata[0]['transactions_retail'];
    
        }
    
        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' and ".$team_query."".$sub_team_query." ";
        // $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_residential = $transactionsdata[0]['transactions_residential'];
    
        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date'  and ".$team_query."".$sub_team_query." ";
        // $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date'  and ".$team_query."".$sub_team_query." ";
        // $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as b LEFT JOIN agreement as a ON b.agreement_id = a.agreement_id where a.deal_date BETWEEN '$start_date' and '$end_date'  and ".$team_query."".$sub_team_query." ";
        // $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
             $brokerage_received = $transactionsdata[0]['brokerage_received'];
    
        }
    
        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' and ".$team_query."".$sub_team_query." ";
        // $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' and  ".$team_query." ";
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sql, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
        $expense_madedata = $db->getAllRecords($sql );
        
        if ($expense_madedata)
        {
             $expense_made = $expense_madedata[0]['expense_made'];
    
        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' ".$team_query."".$sub_team_query." GROUP BY a.expense_head_id ";
        // $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' and  ".$team_query." GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $expense_data .= '</table></div>';
        $tsql = $sql;
    
    
        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        // $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' and   ".$team_query."".$sub_team_query." GROUP BY a.user_id";
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." GROUP BY a.user_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';
    
        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' and ".$team_query."".$sub_team_query." ";
        // $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' and  ".$team_query." ";
        $advance_madedata = $db->getAllRecords($sql );
    
        if ($advance_madedata)
        {
            $advance_made = $advance_madedata[0]['advance_made'];

        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                            <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        $temp_contribution = round($emp_contribution,2);
        $htmlstring .= ' <tr>';
        if (in_array(2, $sub_teams_pks)) {
            $htmlstring .= '<td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td>';
        } else {
            $htmlstring .= '<td style="width:16%"></td><td style="width:16%"></td>';
        }
        $htmlstring .= '    <td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr><tr>';
        if (in_array(3, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .= '    <td></td><td class="numbers"></td><td></td><td></td></tr><tr>';
        if (in_array(12, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        if (in_array(4, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        if (in_array(34, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td>';
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td>';
        $htmlstring .=     '<td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td>';
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr>
                            <tr><td colspan="6" style="text-align:center;">';
                            //$total_income = $brokerage_received-$expense_made-$advance_made;
                            $total_income = $transactions-$expense_made-$advance_made;
                            if ($total_income>0)
                            {
                                $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                            }
                            else{
                                $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                            }
                            $htmlstring .= '</td></tr>';
                            
        $htmlstring .= '</tbody></table></div>';
        
    }elseif(($teams != 'undefined') && ($sub_teams == 'undefined')){
        $team_query = " ( ";  
        // $sub_team_query = " ( ";  
        $tteams = explode(' ', $teams); 
        // $sub_t_teams = explode(' ', $sub_teams);
        // error_log($sub_t_teams, 3, "logfile.log");

        $first_record = "Yes";
        $second_record = "Yes";

        foreach($tteams as $value)
        {
            if ($first_record == 'Yes')
            {
                $team_query .= " ";
                $first_record = "No";
                $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
            }
            else
            {
                $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
            }
        }
        $team_query .= " ) ";

        // foreach($sub_t_teams as $value)
        // {
        //     if ($second_record == 'Yes')
        //     {
        //         $sub_team_query .= " ";
        //         $second_record = "No";
        //         $sub_team_query .= " FIND_IN_SET(".$value." , a.sub_teams) ";
        //     }
        //     else
        //     {
        //         $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //     }
        // }
        // $sub_team_query .= " ) ";  
        // error_log(print_r($team_query, true), 3, "logfile.log");
        // error_log($sub_team, 3, "logfile.log");

        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and '$team_query' ";
        // error_log($sql, 3, "logfile.log");
        $tsql = $sql;
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
            $transactions = $transactionsdata[0]['transactions'];
        }
        
        $transactions_commercial=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;

        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_commercial = $transactionsdata[0]['transactions_commercial'];

        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_retail = $transactionsdata[0]['transactions_retail'];

        }

        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_residential = $transactionsdata[0]['transactions_residential'];

        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
            $brokerage_received = $transactionsdata[0]['brokerage_received'];

        }

        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' and  ".$team_query." ";
        $expense_madedata = $db->getAllRecords($sql );
        
        if ($expense_madedata)
        {
            $expense_made = $expense_madedata[0]['expense_made'];

        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' and  ".$team_query." GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);

        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $expense_data .= '</table></div>';
        $tsql = $sql;


        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." GROUP BY a.user_id";
        $stmt = $db->getRows($sql);

        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';

        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' and  ".$team_query." ";
    
        $advance_madedata = $db->getAllRecords($sql );
    
        if ($advance_madedata)
        {
             $advance_made = $advance_madedata[0]['advance_made'];
    
        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                            <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        $temp_contribution = round($emp_contribution,2);
        $htmlstring .= '    <tr><td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td><td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr>
                            <tr><td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td><td></td><td class="numbers"></td><td></td><td></td></tr>
                            <tr><td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td><td ></td><td></td><td></td><td></td></tr> <tr><td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td><td></td><td></td><td></td></tr>
                            <tr><td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td colspan="6" style="text-align:center;">';
                            //$total_income = $brokerage_received-$expense_made-$advance_made;
                            $total_income = $transactions-$expense_made-$advance_made;
                            if ($total_income>0)
                            {
                                $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                            }
                            else{
                                $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                            }
                            $htmlstring .= '</td></tr>';
                            
        $htmlstring .= '</tbody></table></div>';
    }else{
        // $team_query = " ( ";  
        // $sub_team_query = " ( ";  
        // $tteams = explode(' ', $teams); 
        // $sub_t_teams = explode(' ', $sub_teams);
        // error_log($sub_t_teams, 3, "logfile.log");

        $first_record = "Yes";
        $second_record = "Yes";

        // foreach($tteams as $value)
        // {
        //     if ($first_record == 'Yes')
        //     {
        //         $team_query .= " ";
        //         $first_record = "No";
        //         $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
        //     }
        //     else
        //     {
        //         $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
        //     }
        // }
        // $team_query .= " ) ";

        // foreach($sub_t_teams as $value)
        // {
        //     if ($second_record == 'Yes')
        //     {
        //         $sub_team_query .= " ";
        //         $second_record = "No";
        //         $sub_team_query .= " FIND_IN_SET(".$value." , a.sub_teams) ";
        //     }
        //     else
        //     {
        //         $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //     }
        // }
        // $sub_team_query .= " ) ";  
        // error_log(print_r($team_query, true), 3, "logfile.log");
        // error_log($sub_team, 3, "logfile.log");

        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' ";
        // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' AND a.teams in (2, 5) ";
        // error_log($sql, 3, "logfile.log");
        $tsql = $sql;
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
            $transactions = $transactionsdata[0]['transactions'];
        }
        
        $transactions_commercial=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date'  " ;

        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_commercial = $transactionsdata[0]['transactions_commercial'];

        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_retail = $transactionsdata[0]['transactions_retail'];

        }

        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_residential = $transactionsdata[0]['transactions_residential'];

        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
            $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
            $brokerage_received = $transactionsdata[0]['brokerage_received'];

        }

        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' ";
        $expense_madedata = $db->getAllRecords($sql );
        
        if ($expense_madedata)
        {
            $expense_made = $expense_madedata[0]['expense_made'];

        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);

        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $expense_data .= '</table></div>';
        $tsql = $sql;


        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' GROUP BY a.user_id";
        $stmt = $db->getRows($sql);

        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';

        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' ";
        
        $advance_madedata = $db->getAllRecords($sql );
    
        if ($advance_madedata)
        {
             $advance_made = $advance_madedata[0]['advance_made'];
    
        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                            <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        $temp_contribution = round($emp_contribution,2);
        $htmlstring .= '    <tr><td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td><td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr>
                            <tr><td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td><td></td><td class="numbers"></td><td></td><td></td></tr>
                            <tr><td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td><td ></td><td></td><td></td><td></td></tr> <tr><td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td><td></td><td></td><td></td></tr>
                            <tr><td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td colspan="6" style="text-align:center;">';
                            //$total_income = $brokerage_received-$expense_made-$advance_made;
                            $total_income = $transactions-$expense_made-$advance_made;
                            if ($total_income>0)
                            {
                                $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                            }
                            else{
                                $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                            }
                            $htmlstring .= '</td></tr>';
                            
        $htmlstring .= '</tbody></table></div>';
    }
    

    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$tsql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/poweredbydata_report/:start_date/:end_date/:teams/:sub_teams', function($start_date,$end_date,$teams,$sub_teams) use ($app) {
      
    // error_log($teams, 3, "logfile1.log");
    // error_log("---------", 3, "logfile1.log");
    // error_log($sub_teams, 3, "logfile1.log");
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    //$start_date .= " 00:00:00";
    //$end_date   .= " 23:59:59";

    $month_dis = array();
    $month_dis[1] = "jan";
    $month_dis[2] = "feb";
    $month_dis[3] = "mar";
    $month_dis[4] = "apr";
    $month_dis[5] = "may"; 
    $month_dis[6] = "jun";
    $month_dis[7] = "jul";
    $month_dis[8] = "aug";
    $month_dis[9] = "sep";
    $month_dis[10] = "oct";
    $month_dis[11] = "nov";
    $month_dis[12] = "dec";
    
    $senddata =  array();
    $htmldata = array(); 

    // $team_query = " ( ";  
    // $tteams = explode(' ', $teams); 

    // $first_record = "Yes";

    // foreach($tteams as $value)
    // {
    //     if ($first_record == 'Yes')
    //     {
    //         $team_query .= " ";
    //         $first_record = "No";
    //         $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
            
    //     }
    //     else
    //     {
    //         $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
    //     }
    // }
    // $team_query .= " ) ";  

    // $transactions = 0;    
    // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
    // $tsql = $sql;
    // $transactionsdata = $db->getAllRecords($sql);
    // $tsql = $sql;
    // if ($transactionsdata)
    // {
    //      $transactions = $transactionsdata[0]['transactions'];
    // }
    if(($teams != 0) && ($sub_teams != 0)){
        $team_query = " ( ";  
        // $sub_team_query = " ( ";
        // $sub_team_query = "";
        $sub_team_q = [];
        $tteams = explode(' ', $teams); 
        if($sub_teams === 'undefined'){
            $sub_t_teams = '';
            $sub_teams_pks = array();
        }else{
            $sub_t_teams = explode(' ', $sub_teams);
            $sub_teams_pks = explode(',', $sub_teams);
        }
    
        $first_record = "Yes";
        $second_record = "Yes";
    
        foreach($tteams as $value)
        {
            if ($first_record == 'Yes')
            {
                $team_query .= " ";
                $first_record = "No";
                $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
            }
            else
            {
                $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
            }
        }
        $team_query .= " ) ";  
        
        if(sizeof($sub_teams_pks) != 0){
            foreach($sub_teams_pks as $value)
            {
                if ($second_record == 'Yes')
                {
                    // $sub_team_query .= " ";
                    $second_record = "No";
                    $sub_team_q[] = "and FIND_IN_SET(".$value." , a.sub_teams) ";
                }
                else
                {
                    $sub_team_q[] = "OR FIND_IN_SET(".$value." , a.sub_teams) ";
                }
            }
        }
        $sub_team_query = implode(" ", $sub_team_q);
    
     
        // if($sub_t_teams != ""){
        //     foreach($sub_t_teams as $value)
        //     {
        //         if ($second_record == 'Yes')
        //         {
        //             $sub_team_query .= " ";
        //             $second_record = "No";
        //             $sub_team_query .= " and FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //         else
        //         {
        //             $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //     }
        // }
        // $sub_team_query .= " ) ";  
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sub_teams, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
    
        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and ".$team_query."".$sub_team_query." ";
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sql, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
        // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $tsql = $sql;
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
             $transactions = $transactionsdata[0]['transactions'];
        }
    
        $transactions_commercial=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query."".$sub_team_query." " ;

        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_commercial = $transactionsdata[0]['transactions_commercial'];
    
        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query."".$sub_team_query." ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_retail = $transactionsdata[0]['transactions_retail'];
    
        }
    
        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query."".$sub_team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_residential = $transactionsdata[0]['transactions_residential'];
    
        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query."".$sub_team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query."".$sub_team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query."".$sub_team_query." " ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
             $brokerage_received = $transactionsdata[0]['brokerage_received'];
    
        }
    
        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' ".$team_query."".$sub_team_query." ";
        $expense_madedata = $db->getAllRecords($sql );
        
        if ($expense_madedata)
        {
             $expense_made = $expense_madedata[0]['expense_made'];
    
        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' and  ".$team_query."".$sub_team_query." GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $expense_data .= '</table></div>';
    
        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' and  ".$team_query."".$sub_team_query." GROUP BY a.user_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';
    
        $tsql = $sql;
        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' and  ".$team_query."".$sub_team_query." ";
        $advance_madedata = $db->getAllRecords($sql );
        
        if ($advance_madedata)
        {
             $advance_made = $advance_madedata[0]['advance_made'];
    
        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                            <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        $temp_contribution = round($emp_contribution,2);
        // $htmlstring .= '    <tr><td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td><td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr>
        //                     <tr><td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td><td></td><td class="numbers"></td><td></td><td></td></tr>
        //                     <tr><td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td><td></td><td></td><td></td><td></td></tr>
        //                     <tr><td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td><td></td><td></td><td></td><td></td></tr>
        //                     <tr><td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td><td></td><td></td><td></td><td></td></tr>
        //                     <tr><td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td><td ></td><td></td><td></td><td></td></tr> <tr><td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td><td></td><td></td><td></td></tr>
        //                     <tr><td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td><td></td><td></td><td></td><td></td></tr>
        //                     <tr><td colspan="6" style="text-align:center;">';
        //                     //$total_income = $brokerage_received-$expense_made-$advance_made;
        //                     $total_income = $transactions-$expense_made-$advance_made;
        //                     if ($total_income>0)
        //                     {
        //                         $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
        //                     }
        //                     else{
        //                         $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
        //                     }
        //                     $htmlstring .= '</td></tr>';
                        
        // $htmlstring .= '</tbody></table></div>';
        $htmlstring .= ' <tr>';
        if (in_array(2, $sub_teams_pks)) {
            $htmlstring .= '<td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td>';
        } else {
            $htmlstring .= '<td style="width:16%"></td><td style="width:16%"></td>';
        }
        $htmlstring .= '    <td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr><tr>';
        if (in_array(3, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .= '    <td></td><td class="numbers"></td><td></td><td></td></tr><tr>';
        if (in_array(12, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        if (in_array(4, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        if (in_array(34, $sub_teams_pks)) {
            $htmlstring .= '<td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td>';
        } else {
            $htmlstring .= '<td></td><td class="numbers"></td>';
        }
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td>';
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td>';
        $htmlstring .=     '<td></td><td></td><td></td></tr><tr>';
        $htmlstring .=     '<td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td>';
        $htmlstring .=     '<td></td><td></td><td></td><td></td></tr>
                            <tr><td colspan="6" style="text-align:center;">';
                            //$total_income = $brokerage_received-$expense_made-$advance_made;
                            $total_income = $transactions-$expense_made-$advance_made;
                            if ($total_income>0)
                            {
                                $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                            }
                            else{
                                $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                            }
                            $htmlstring .= '</td></tr>';
                            
        $htmlstring .= '</tbody></table></div>';
    }elseif(($teams != 0) && ($sub_teams == 0)){
        $team_query = " ( ";  
        // $sub_team_query = " ( ";
        // $sub_team_query = "";
        $tteams = explode(' ', $teams); 
        // if($sub_teams === 'undefined'){
        //     $sub_t_teams = '';
        //     $sub_teams_pks = array();
        // }else{
        //     $sub_t_teams = explode(' ', $sub_teams);
        //     $sub_teams_pks = explode(',', $sub_teams);
        // }
    
        $first_record = "Yes";
        $second_record = "Yes";
    
        foreach($tteams as $value)
        {
            if ($first_record == 'Yes')
            {
                $team_query .= " ";
                $first_record = "No";
                $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
            }
            else
            {
                $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
            }
        }
        $team_query .= " ) ";  
    
     
        // if($sub_t_teams != ""){
        //     foreach($sub_t_teams as $value)
        //     {
        //         if ($second_record == 'Yes')
        //         {
        //             $sub_team_query .= " ";
        //             $second_record = "No";
        //             $sub_team_query .= " and FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //         else
        //         {
        //             $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //     }
        // }
        // $sub_team_query .= " ) ";  
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sub_teams, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
    
        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and ".$team_query." ";
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sql, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
        // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $tsql = $sql;
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
             $transactions = $transactionsdata[0]['transactions'];
        }
    
        $transactions_commercial=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;

        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_commercial = $transactionsdata[0]['transactions_commercial'];
    
        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_retail = $transactionsdata[0]['transactions_retail'];
    
        }
    
        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_residential = $transactionsdata[0]['transactions_residential'];
    
        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date'  and  ".$team_query." " ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
             $brokerage_received = $transactionsdata[0]['brokerage_received'];
    
        }
    
        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' and  ".$team_query." ";
        $expense_madedata = $db->getAllRecords($sql );
        
        if ($expense_madedata)
        {
             $expense_made = $expense_madedata[0]['expense_made'];
    
        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' and  ".$team_query." GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $expense_data .= '</table></div>';
    
        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." GROUP BY a.user_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';
    
        $tsql = $sql;
        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' and  ".$team_query." ";
        $advance_madedata = $db->getAllRecords($sql );
        
        if ($advance_madedata)
        {
             $advance_made = $advance_madedata[0]['advance_made'];
    
        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                            <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        $temp_contribution = round($emp_contribution,2);
        $htmlstring .= '    <tr><td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td><td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr>
                            <tr><td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td><td></td><td class="numbers"></td><td></td><td></td></tr>
                            <tr><td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td><td ></td><td></td><td></td><td></td></tr> <tr><td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td><td></td><td></td><td></td></tr>
                            <tr><td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td colspan="6" style="text-align:center;">';
                            //$total_income = $brokerage_received-$expense_made-$advance_made;
                            $total_income = $transactions-$expense_made-$advance_made;
                            if ($total_income>0)
                            {
                                $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                            }
                            else{
                                $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                            }
                            $htmlstring .= '</td></tr>';
                        
        $htmlstring .= '</tbody></table></div>';
    }else{
        // $team_query = " ( ";  
        // $sub_team_query = " ( ";
        // $sub_team_query = "";
        // $tteams = explode(' ', $teams); 
        // if($sub_teams === 'undefined'){
        //     $sub_t_teams = '';
        //     $sub_teams_pks = array();
        // }else{
        //     $sub_t_teams = explode(' ', $sub_teams);
        //     $sub_teams_pks = explode(',', $sub_teams);
        // }
    
        $first_record = "Yes";
        $second_record = "Yes";
    
        // foreach($tteams as $value)
        // {
        //     if ($first_record == 'Yes')
        //     {
        //         $team_query .= " ";
        //         $first_record = "No";
        //         $team_query .= " FIND_IN_SET(".$value." , a.teams) ";
                
        //     }
        //     else
        //     {
        //         $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
        //     }
        // }
        // $team_query .= " ) ";  
    
     
        // if($sub_t_teams != ""){
        //     foreach($sub_t_teams as $value)
        //     {
        //         if ($second_record == 'Yes')
        //         {
        //             $sub_team_query .= " ";
        //             $second_record = "No";
        //             $sub_team_query .= " and FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //         else
        //         {
        //             $sub_team_query .= " OR FIND_IN_SET(".$value." , a.sub_teams) ";
        //         }
        //     }
        // }
        // $sub_team_query .= " ) ";  
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sub_teams, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
    
        $transactions = 0;    
        $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' ";
        
        // error_log("------------------------------------------", 3, "logfile1.log");
        // error_log($sql, 3, "logfile1.log");
        // error_log("+++++++++++++++++++++++++++++++++++", 3, "logfile1.log");
        // $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where a.deal_date BETWEEN '$start_date' and '$end_date' and  ".$team_query." " ;
        $tsql = $sql;
        $transactionsdata = $db->getAllRecords($sql);
        $tsql = $sql;
        if ($transactionsdata)
        {
             $transactions = $transactionsdata[0]['transactions'];
        }
    
        $transactions_commercial=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;

        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_commercial = $transactionsdata[0]['transactions_commercial'];
    
        }
        $transactions_retail=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and a.deal_date BETWEEN '$start_date' and '$end_date' ";
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_retail = $transactionsdata[0]['transactions_retail'];
    
        }
    
        $transactions_residential=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_residential = $transactionsdata[0]['transactions_residential'];
    
        }
        $transactions_preleased=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_preleased = $transactionsdata[0]['transactions_preleased'];
        }
        $transactions_others=0;
        $sql = "SELECT sum(a.our_brokerage) as transactions_others FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='others' and a.deal_date BETWEEN '$start_date' and '$end_date' " ;
        $transactionsdata = $db->getAllRecords($sql);
        if ($transactionsdata)
        {
             $transactions_others = $transactionsdata[0]['transactions_others'];
        }
        $tsql .= $sql;
        
        $brokerage_received=0;
        $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where b.deal_date BETWEEN '$start_date' and '$end_date'" ;
        $transactionsdata = $db->getAllRecords($sql );
        if ($transactionsdata)
        {
             $brokerage_received = $transactionsdata[0]['brokerage_received'];
    
        }
    
        $expense_made=0;
        $sql = "SELECT sum(amount) as expense_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Expenses' and ";
        $expense_madedata = $db->getAllRecords($sql );
        
        if ($expense_madedata)
        {
             $expense_made = $expense_madedata[0]['expense_made'];
    
        }
        $expense_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.expense_head_id, b.expense_title, sum(a.amount) as amount FROM expense as a LEFT JOIN expense_head as b ON a.expense_head_id = b.expense_head_id where a.expense_date BETWEEN '$start_date' and '$end_date' and a.expense_type = 'Expenses' GROUP BY a.expense_head_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $expense_data .='<tr><td>'.$row['expense_title'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $expense_data .= '</table></div>';
    
        $emp_contribution = 0;
        $voucher_data = '<div style="margin-top:25px;"><table class="table table-bordered table-striped" >';
        $sql = "SELECT a.user_id, CONCAT(c.salu,' ',c.fname,' ', c.lname) as emp_name, sum(a.amount) as amount FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.transaction_date BETWEEN '$start_date' and '$end_date' GROUP BY a.user_id";
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $emp_contribution = $emp_contribution + $row['amount'];
                $voucher_data .='<tr><td>'.$row['emp_name'].'</td><td style="text-align:right;">'.$row['amount'].'</td></tr>';
                
            }
        }
        $voucher_data .= '</table></div>';
    
        $tsql = $sql;
        $advance_made=0;
        $sql = "SELECT sum(amount) as advance_made FROM expense as a where expense_date BETWEEN '$start_date' and '$end_date' and expense_type = 'Advance' ";
        $advance_madedata = $db->getAllRecords($sql );
        
        if ($advance_madedata)
        {
             $advance_made = $advance_madedata[0]['advance_made'];
    
        }
        $tsql .= $sql;
        $brokerage_invoiced = 0;
        $htmlstring .= '<table class="table table-bordered table-striped" >
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="width:33%;"><h1 style="text-align:center;">INCOME</h1></th><th colspan="2"  style="width:33%;"><h1 style="text-align:center;">EXPENSES</h1></th>
                                            <th colspan="2"  style="width:33%;"><h1 style="text-align:center;">ADVANCES</h1></th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        $temp_contribution = round($emp_contribution,2);
        $htmlstring .= '    <tr><td style="width:16%">Transactions Commercial:</td><td class="numbers"  style="width:16%">'.$transactions_commercial.'</td><td  style="width:16%" rowspan="7" colspan="2"><span style="float:left;font-weight:bold;">Expenses Made:</span><span style="float:right;">'.$expense_made.'</span>'.$expense_data.'<span style="float:left;font-weight:bold;font-weight:bold;">Emp.Contribution:</span><span style="float:right;">'.$temp_contribution.'</span>'.$voucher_data.'</td><td  style="width:16%;font-weight:bold;">Advance Given:</td><td class="numbers"  style="width:16%">'.$advance_made.'</td></tr>
                            <tr><td>Transactions Retail:</td><td class="numbers">'.$transactions_retail.'</td><td></td><td class="numbers"></td><td></td><td></td></tr>
                            <tr><td>Transactions Residential:</td><td class="numbers">'.$transactions_residential.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Preleased:</td><td class="numbers">'.$transactions_preleased.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td>Transactions Others:</td><td class="numbers">'.$transactions_others.'</td><td></td><td></td><td></td><td></td></tr>
                            <tr><td ><strong>Total Transactions:</strong></td><td  class="numbers"><strong>'.$transactions.'</strong></td><td ></td><td></td><td></td><td></td></tr> <tr><td>Brokerage Received:</td><td class="numbers">'.$brokerage_received.'</td><td></td><td></td><td></td></tr>
                            <tr><td><strong>Brokerage Expected:</strong></td><td class="numbers"><strong>'.($transactions-$brokerage_received).'</strong></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td colspan="6" style="text-align:center;">';
                            //$total_income = $brokerage_received-$expense_made-$advance_made;
                            $total_income = $transactions-$expense_made-$advance_made;
                            if ($total_income>0)
                            {
                                $htmlstring .= '<span style="color:green;"><strong>Net Income:'.$total_income.'</strong></span>';
                            }
                            else{
                                $htmlstring .= '<span style="color:red;"><strong>Net Loss:'.$total_income.'</strong></span>';
                            }
                            $htmlstring .= '</td></tr>';
                        
        $htmlstring .= '</tbody></table></div>';
    }

    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$tsql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

// ACTIVITIES

$app->get('/activity_list_ctrl/:cat/:id/:next_page_id', function($cat,$id,$next_page_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    // error_log(print_r($session, true), 3, "logfile.log");
    if ($session['username']=="Guest")
    {
        return;
    }

    $bo_id = $session['bo_id'];
    $user_id = $session['user_id']; 
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $permissions = $session['permissions'];
    $role = $session['role'];
    $sql = "select * from activity limit 0";
    $countsql = "SELECT count(*) as activity_count FROM activity";
    if ($id == 0)
    {
        if (in_array("Admin", $role) || in_array("Sub Admin", $role))
        {
            //  $sql = "SELECT a.description,a.property_id,a.project_id,a.enquiry_id,a.client_id,a.developer_id,a.broker_id,a.assign_to,a.activity_id,a.activity_type,b.enquiry_for,b.enquiry_type, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id ";
           
            $sql = "SELECT a.description,a.property_id,a.project_id,a.enquiry_id,a.client_id,a.developer_id,a.broker_id,a.assign_to,a.activity_id,a.activity_type,b.enquiry_for,b.enquiry_type, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id ";
            
            $sql .=  " ORDER BY a.created_date DESC LIMIT $next_page_id, 30 "; // GROUP BY a.activity_id 

            $countsql = "SELECT count(*) as activity_count FROM activity as a ";


        }
        else if (in_array("Branch Head", $role))
        {
            $sql = "SELECT *,a.description,a.property_id, a.project_id, a.enquiry_id, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id where (a.created_by = $user_id ";

            
            $sql .= " ) ".$team_query." ORDER BY a.created_date DESC LIMIT $next_page_id, 30"; // GROUP BY a.activity_id 
            
            $countsql = "SELECT count(*) as activity_count FROM activity as a where (a.created_by = $user_id ) ".$team_query." ";

        }
        else
        { 

            $sql = "SELECT *,a.description,a.property_id, a.project_id, a.enquiry_id, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id where (a.created_by = $user_id ";

            $sql .= " ) ORDER BY a.created_date DESC LIMIT $next_page_id, 30"; // GROUP BY a.activity_id 

            $countsql = "SELECT count(*) as activity_count FROM activity as a where a.created_by = $user_id " ;

        }    
    }
    else
    {
        if (in_array("Admin", $role) || in_array("Sub Admin", $role))
        { 

            $sql = "SELECT *,a.description,a.property_id,a.project_id,a.enquiry_id, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id  ";
            $tsql = " ";
            if ($cat == 'project' and $id > 0)
            {
                $tsql = " WHERE a.project_id = $id ";
            }

            if ($cat == 'property' and $id > 0)
            {
                $tsql = " WHERE a.property_id = $id ";
            }

            if ($cat == 'enquiry' and $id > 0)
            {
                $tsql = " WHERE a.enquiry_id = $id ";
            }
            
            $sql .= $tsql . " ORDER BY a.created_date DESC LIMIT $next_page_id, 30"; // GROUP BY a.activity_id 

            $countsql = "SELECT count(*) as activity_count FROM activity as a  " .$tsql;

        }
        else if (in_array("Branch Head", $role))
        {
            $sql = "SELECT *,a.description,a.property_id, a.project_id, a.enquiry_id, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id ";

            $tsql = " ";
            if ($cat == 'project' and $id > 0)
            {
                $tsql = " WHERE a.project_id = $id ";
            }

            if ($cat == 'property' and $id > 0)
            {
                $tsql = " WHERE a.property_id = $id ";
            }

            if ($cat == 'enquiry' and $id > 0)
            {
                $tsql = " WHERE a.enquiry_id = $id ";
            }
            
            $sql .= $tsql."  ORDER BY a.created_date DESC LIMIT $next_page_id, 30"; // GROUP BY a.activity_id 
            
            $countsql = "SELECT count(*) as activity_count FROM activity as a ".$tsql;

        }
        else
        { 
            
            $sql = "SELECT *,a.description,a.property_id, a.project_id, a.enquiry_id, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id ";

            $tsql = " ";
            if ($cat == 'project' and $id > 0)
            {
                $tsql = " WHERE a.project_id = $id ";
            }

            if ($cat == 'property' and $id > 0)
            {
                $tsql = " WHERE a.property_id = $id ";
            }

            if ($cat == 'enquiry' and $id > 0)
            {
                $tsql = " WHERE a.enquiry_id = $id ";
            }
            
            $sql .= $tsql."  ORDER BY a.created_date DESC LIMIT $next_page_id, 30"; // GROUP BY a.activity_id 

            $countsql = "SELECT count(*) as activity_count FROM activity as a " .$tsql;

        }  
    }
    
    // error_log($sql, 3, "logfile.log");
    $activities = $db->getAllRecords($sql);
    

    $activity_count = 0;
    $activity_countdata = $db->getAllRecords($countsql);
    if ($activity_countdata)
    {
         $activity_count = $activity_countdata[0]['activity_count'];

    }
    if ($activity_count>0)
    {
        $activities[0]['activity_count']=$activity_count;
    }
    $activities[0]['sql']=$sql;
    echo json_encode($activities);
});

$app->post('/search_activities', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());

    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];

    $next_page_id = $r->searchdata->next_page_id;
    
    $role = $session['role'];
    $countsql = "SELECT count(*) as activity_count FROM activity ";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $searchsql = "SELECT *,a.description,a.property_id, a.enquiry_id, a.project_id, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS start_date,DATE_FORMAT(a.activity_end,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id where a.created_by > 0 ";

        $countsql = "SELECT count(*) as activity_count FROM activity as a WHERE created_by > 0 ";
    }
    else if (in_array("Branch Head", $role))
    {
        $searchsql = "SELECT *, a.description,a.property_id, a.enquiry_id, a.project_id,CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS start_date,DATE_FORMAT(a.activity_end,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.created_by = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id where (a.created_by = $user_id ".$team_query." ) "; 

        $countsql = "SELECT count(*) as activity_count FROM activity as a LEFT JOIN users as g on a.created_by = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id  WHERE a.created_by > 0 and (a.created_by = $user_id ".$team_query." ) ";
    }

    else
    {
        $searchsql = "SELECT *, a.description,a.property_id, a.enquiry_id, a.project_id,CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS start_date,DATE_FORMAT(a.activity_end,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id where (a.created_by = $user_id) "; 

        $countsql = "SELECT count(*) as activity_count FROM activity as a WHERE created_by > 0 and (created_by = $user_id ) ";

    }

    $sql = " ";
    if (isset($r->searchdata->status))
    {
        $first = "Yes";
        $new_data = $r->searchdata->status;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.status LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->activity_type))
    {
        $first = "Yes";
        $new_data = $r->searchdata->activity_type;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.activity_type LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->assign_to))
    {
        $first = "Yes";
        $new_data = $r->searchdata->assign_to;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                } 
                $sql .= " a.assign_to LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }
    if (isset($r->searchdata->teams))
    {
        $first = "Yes";
        $new_data = $r->searchdata->teams;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                } 
                $sql .= " a.teams LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }
    if (isset($r->searchdata->client_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->client_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.client_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->broker_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->broker_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.broker_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->property_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->property_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.property_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->project_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->project_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.project_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->enquiry_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->enquiry_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.enquiry_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    if (isset($r->searchdata->activity_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->activity_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.activity_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->created_date_from))
    {
        $created_date_from = $r->searchdata->created_date_from;
        $tcreated_date_from = substr($created_date_from,6,4)."-".substr($created_date_from,3,2)."-".substr($created_date_from,0,2);
        if (isset($r->searchdata->created_date_to))
        {
            $created_date_to = $r->searchdata->created_date_to;
            $tcreated_date_to = substr($created_date_to,6,4)."-".substr($created_date_to,3,2)."-".substr($created_date_to,0,2);
        }
        $sql .= " and a.created_date BETWEEN '".$tcreated_date_from."' AND '".$tcreated_date_to."' ";
    }

    if (isset($r->searchdata->activity_start))
    {
        $activity_start = $r->searchdata->activity_start;
        $tactivity_start = substr($activity_start,6,4)."-".substr($activity_start,3,2)."-".substr($activity_start,0,2);
        if (isset($r->searchdata->activity_end))
        {
            $activity_end = $r->searchdata->activity_end;
            $tactivity_end = substr($activity_end,6,4)."-".substr($activity_end,3,2)."-".substr($activity_end,0,2);
        }
        $sql .= " and a.activity_start>= '".$tactivity_start."' AND a.activity_end <='".$tactivity_end."' ";
    }
    
    $searchsql = $searchsql .  $sql .  "  ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";// GROUP BY a.activity_id CAST(a.property_id as UNSIGNED) DESC";

    $listactivities = $db->getAllRecords($searchsql);

    $countsql = $countsql . $sql;
    $activity_count = 0;
    $activitycountdata = $db->getAllRecords($countsql);
    if ($activitycountdata)
    {
         $activity_count = $activitycountdata[0]['activity_count'];

    }
    if ($activity_count>0)
    {
        $listactivities[0]['activity_count']=$activity_count;
    }
    //$properties[0]['countsql']=$countsql;
    $listactivities[0]['searchsql']=$searchsql;
//$listactivities[0]['sql']=$sql;
    //echo $sql;
    echo json_encode($listactivities);

});


$app->get('/getdatavalues_activity/:field_name', function($field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM activity  ORDER BY $field_name";
    //echo $sql;
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});

$app->get('/activity_open', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *, DATE_FORMAT(start_date,'%d-%m-%Y %H:%i') AS start_date, DATE_FORMAT(end_date,'%d-%m-%Y %H:%i') AS end_date, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker, CONCAT(f.name_title,' ',f.f_name,' ', f.l_name) as target from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN contact as f on a.target_id = f.contact_id and f.contact_off = 'Target List' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id where i.status='Deal Done' ORDER BY i.start_date";
    $activities = $db->getAllRecords($sql);
    echo json_encode($activities);
});

$app->get('/change_activity_sub_type/:activity_type', function($activity_type) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from dropdowns WHERE type = 'ACTIVITY_SUB_TYPE' and parent_type in ('$activity_type') ORDER BY CAST(sequence_number as UNSIGNED)";
    $dropdowns = $db->getAllRecords($sql);
    echo json_encode($dropdowns);
});

// $app->post('/activity_add_new', function() use ($app) {
//     $db = new DbHandler();
//     $session = $db->getSession();
//     if ($session['username']=="Guest")
//     {
//         return;
//     }
//     $response = array();
//     $r = json_decode($app->request->getBody());
//     verifyRequiredParams(array('activity_type'),$r->activitydata);
//     $db = new DbHandler();
    
//     $created_by = $session['user_id'];
//     $created_date = date('Y-m-d H:i:s');
//     $r->activitydata->created_by = $created_by;
//     $r->activitydata->created_date = $created_date;

//     if (isset($r->activitydata->activity_start))
//     {
//         $tstart_date = substr($r->activitydata->activity_start,6,4)."-".substr($r->activitydata->activity_start,3,2)."-".substr($r->activitydata->activity_start,0,2)." ".substr($r->activitydata->activity_start,11,5);
//         $r->activitydata->activity_start = $tstart_date;
//     }

//     if (isset($r->activitydata->activity_end))
//     {
//         $tstart_end = substr($r->activitydata->activity_end,6,4)."-".substr($r->activitydata->activity_end,3,2)."-".substr($r->activitydata->activity_end,0,2)." ".substr($r->activitydata->activity_end,11,5);
//         $r->activitydata->activity_end = $tstart_end;
//     }
//     $properties = $r->activitydata->property_id;
//     $projects = $r->activitydata->project_id;
//     $brokers = $r->activitydata->broker_id;
//     $tstring = '';
//     $tresult = NULL;
//     if ($properties)
//     {
//         foreach ($properties as $value) {
//             $property_id = $value;
//             $r->activitydata->property_id = $property_id;
//             $tabble_name = "activity";
//             $column_names = array('activity_type','activity_sub_type','activity_start','activity_end','assign_to','broker_id','client_id','developer_id','enquiry_id','property_id','project_id','description', 'remind','remind_before','remind_time','status','closure_comment','teams','sub_teams','created_by','created_date');
//             $multiple=array('assign_to','teams','sub_teams');
//             $result = $db->insertIntoTable($r->activitydata, $column_names, $tabble_name, $multiple);
//         }
//     }

//     else if ($projects)
//     {
//         foreach ($projects as $value) {
//             $project_id = $value;
//             $r->activitydata->project_id = $project_id;
//             $tabble_name = "activity";
//             $column_names = array('activity_type','activity_sub_type','activity_start','activity_end','assign_to','broker_id','client_id','developer_id','enquiry_id','property_id','project_id','description', 'remind','remind_before','remind_time','status','closure_comment','teams','sub_teams','created_by','created_date');
//             $multiple=array('assign_to','teams','sub_teams');
//             $result = $db->insertIntoTable($r->activitydata, $column_names, $tabble_name, $multiple);
//         }
//     }

//     else if ($brokers)
//     {
//         foreach ($brokers as $value) {
//             $broker_id = $value;
//             $r->activitydata->broker_id = $broker_id;
//             $tabble_name = "activity";
//             $column_names = array('activity_type','activity_sub_type','activity_start','activity_end','assign_to','broker_id','client_id','developer_id','enquiry_id','property_id','project_id','description', 'remind','remind_before','remind_time','status','closure_comment','teams','sub_teams','created_by','created_date');
//             $multiple=array('assign_to','teams','sub_teams');
//             $result = $db->insertIntoTable($r->activitydata, $column_names, $tabble_name, $multiple);
//         }
//     }
//     else
//     {
//         $tabble_name = "activity";
//         $column_names = array('activity_type','activity_sub_type','activity_start','activity_end','assign_to','broker_id','client_id','developer_id','enquiry_id','property_id','project_id','description','calling_count', 'remind','remind_before','remind_time','status','closure_comment','teams','sub_teams','created_by','created_date');
//         $multiple=array('assign_to','teams','sub_teams','broker_id');
//         $result = $db->insertIntoTable($r->activitydata, $column_names, $tabble_name, $multiple);
//     }
//     if ($result != NULL) {
//         $response["status"] = "success";
//         $response["message"] = "Activity created successfully [".$tstring."]";
//         $response["activity_id"] = $result;        
//         $_SESSION['tmpactivity_id'] = $result;
//         echoResponse(200, $response);
//     } else {
//         $response["status"] = "error";
//         $response["message"] = "Failed to create activity. Please try again";
//         echoResponse(201, $response);
//     }
// });

$app->post('/activity_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('activity_type'),$r->activitydata);
    $db = new DbHandler();
    
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->activitydata->created_by = $created_by;
    $r->activitydata->created_date = $created_date;

    if (isset($r->activitydata->activity_start))
    {
        $tstart_date = substr($r->activitydata->activity_start,6,4)."-".substr($r->activitydata->activity_start,3,2)."-".substr($r->activitydata->activity_start,0,2)." ".substr($r->activitydata->activity_start,11,5);
        $r->activitydata->activity_start = $tstart_date;
    }

    if (isset($r->activitydata->activity_end))
    {
        $tstart_end = substr($r->activitydata->activity_end,6,4)."-".substr($r->activitydata->activity_end,3,2)."-".substr($r->activitydata->activity_end,0,2)." ".substr($r->activitydata->activity_end,11,5);
        $r->activitydata->activity_end = $tstart_end;
    }
    $properties = $r->activitydata->property_id;
    $projects = $r->activitydata->project_id;
    $brokers = $r->activitydata->broker_id;
    $tstring = '';
    $tresult = NULL;
    if ($properties)
    {
        foreach ($properties as $value) {
            $property_id = $value;
            $r->activitydata->property_id = $property_id;
            $tabble_name = "activity";
            $column_names = array('activity_type','activity_sub_type','activity_start','activity_end','assign_to','broker_id','client_id','developer_id','enquiry_id','property_id','project_id','description', 'remind','remind_before','remind_time','status','closure_comment','teams','sub_teams','created_by','created_date');
            $multiple=array('assign_to','teams','sub_teams');
            $result = $db->insertIntoTable($r->activitydata, $column_names, $tabble_name, $multiple);
        }
    }

    else if ($projects)
    {
        foreach ($projects as $value) {
            $project_id = $value;
            $r->activitydata->project_id = $project_id;
            $tabble_name = "activity";
            $column_names = array('activity_type','activity_sub_type','activity_start','activity_end','assign_to','broker_id','client_id','developer_id','enquiry_id','property_id','project_id','description', 'remind','remind_before','remind_time','status','closure_comment','teams','sub_teams','created_by','created_date');
            $multiple=array('assign_to','teams','sub_teams');
            $result = $db->insertIntoTable($r->activitydata, $column_names, $tabble_name, $multiple);
        }
    }

    else if ($brokers)
    {
        foreach ($brokers as $value) {
            $broker_id = $value;
            $r->activitydata->broker_id = $broker_id;
            $tabble_name = "activity";
            $column_names = array('activity_type','activity_sub_type','activity_start','activity_end','assign_to','broker_id','client_id','developer_id','enquiry_id','property_id','project_id','description', 'remind','remind_before','remind_time','status','closure_comment','teams','sub_teams','created_by','created_date');
            $multiple=array('assign_to','teams','sub_teams');
            $result = $db->insertIntoTable($r->activitydata, $column_names, $tabble_name, $multiple);
        }
    }
    else
    {
        $tabble_name = "activity";
        $column_names = array('activity_type','activity_sub_type','activity_start','activity_end','assign_to','broker_id','client_id','developer_id','enquiry_id','property_id','project_id','description','calling_count', 'remind','remind_before','remind_time','status','closure_comment','teams','sub_teams','created_by','created_date');
        $multiple=array('assign_to','teams','sub_teams','broker_id');
        $result = $db->insertIntoTable($r->activitydata, $column_names, $tabble_name, $multiple);
    }
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Activity created successfully [".$tstring."]";
        $response["activity_id"] = $result;        
        $_SESSION['tmpactivity_id'] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create activity. Please try again";
        echoResponse(201, $response);
    }
});

$app->post('/activity_uploads', function() use ($app) {
    session_start();
    $activity_id = $_SESSION['tmpactivity_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_activity'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_activity'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "a_".$activity_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "activities". $ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;            

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, created_by, created_date)   VALUES('activity', '$activity_id','$file_names','Yes','Yes','Main','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});


$app->get('/activity_edit_ctrl/:activity_id', function($activity_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    //$sql = "SELECT *, DATE_FORMAT(start_date,'%Y-%m-%d %H:%i') AS start_date, DATE_FORMAT(end_date,'%Y-%m-%d %H:%i') AS end_date from activity where activity_id=".$activity_id;
    $sql = "SELECT *, DATE_FORMAT(activity_start,'%d/%m/%Y %H:%i') AS activity_start, DATE_FORMAT(activity_end,'%d/%m/%Y %H:%i') AS activity_end from activity where activity_id=".$activity_id;
    $activities = $db->getAllRecords($sql);
    echo json_encode($activities);
    
});


$app->get('/activity_images/:activity_id', function($activity_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'activity' and category_id = $activity_id";
    $activity_images = $db->getAllRecords($sql);
    echo json_encode($activity_images);
});


$app->get('/activity_schedules/:activity_id', function($activity_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *, DATE_FORMAT(a.start_date,'%d-%m-%Y %H:%i') AS start_date, DATE_FORMAT(a.end_date,'%d-%m-%Y %H:%i') AS end_date from activity_details as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN project as c on b.project_id = c.project_id where a.activity_id=".$activity_id;
    //$sql = "SELECT * from activity where activity_id=".$activity_id;
    $activity_schedules = $db->getAllRecords($sql);
    echo json_encode($activity_schedules);
    
});


$app->get('/add_activity_schedules/:activity_id/:data', function($activity_id, $data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * FROM property where property_id IN($data) " ;
    $stmt = $db->getRows($sql);
    
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $property_id = $row['property_id'];
            $query = "INSERT INTO activity_details (start_date, end_date, activity_id, property_id, status, created_by, created_date)  VALUES('$created_date', '$created_date', '$activity_id','$property_id','Visit Pending', '$created_by' ,'$created_date' )";
            $result = $db->insertByQuery($query);
        }
    }
    $sql = "SELECT *, DATE_FORMAT(a.start_date,'%d-%m-%Y %H:%i') AS start_date, DATE_FORMAT(a.end_date,'%d-%m-%Y %H:%i') AS end_date, DATE_FORMAT(a.visited_on,'%d-%m-%Y %H:%i') AS visited_on, a.status as status from activity_details as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN project as c on b.project_id = c.project_id where a.activity_id=".$activity_id;
    $activity_schedules = $db->getAllRecords($sql);
    echo json_encode($activity_schedules);
});

$app->get('/get_activity_schedules/:activity_details_id', function($activity_details_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $sql = "SELECT *, DATE_FORMAT(start_date,'%d-%m-%Y %H:%i') AS start_date, DATE_FORMAT(end_date,'%d-%m-%Y %H:%i') AS end_date, DATE_FORMAT(visited_on,'%d-%m-%Y %H:%i') AS visited_on from activity_details where activity_details_id=".$activity_details_id;
    $activity_schedules = $db->getAllRecords($sql);
    echo json_encode($activity_schedules);
});

$app->get('/update_activity_schedules/:activity_details_id/:start_date/:end_date/:visited_on/:description/:status/:activity_id', function($activity_details_id,$start_date,$end_date,$visited_on,$description,$status,$activity_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    if (isset($start_date))
    {
        $tstart_date = substr($start_date,6,4)."-".substr($start_date,3,2)."-".substr($start_date,0,2)." ".substr($start_date,11,5);
        $start_date = $tstart_date;
    }

    if (isset($end_date))
    {
        $tend_date = substr($end_date,6,4)."-".substr($end_date,3,2)."-".substr($end_date,0,2)." ".substr($end_date,11,5);
        $end_date = $tend_date;
    }

    if (isset($visited_on))
    {
        $tvisited_on = substr($visited_on,6,4)."-".substr($visited_on,3,2)."-".substr($visited_on,0,2)." ".substr($visited_on,11,5);
        $visited_on = $tvisited_on;
    }
    
    $sql = "UPDATE activity_details set start_date = '$start_date', end_date = '$end_date', visited_on = '$visited_on', description = '$description', status = '$status' where activity_details_id = $activity_details_id " ;  
    $result = $db->updateByQuery($sql);

    $sql = "SELECT *, DATE_FORMAT(a.start_date,'%d-%m-%Y %H:%i') AS start_date, DATE_FORMAT(a.end_date,'%d-%m-%Y %H:%i') AS end_date, DATE_FORMAT(a.visited_on,'%d-%m-%Y %H:%i') AS visited_on, a.status as status from activity_details as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN project as c on b.project_id = c.project_id where a.activity_id=".$activity_id;
    $activity_schedules = $db->getAllRecords($sql);
    echo json_encode($activity_schedules);
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
    $modified_date = date('Y-m-d H:i:s');
    $r->activity->modified_by = $modified_by;
    $r->activity->modified_date = $modified_date;

    if (isset($r->activity->activity_start))
    {
        $tstart_date = substr($r->activity->activity_start,6,4)."-".substr($r->activity->activity_start,3,2)."-".substr($r->activity->activity_start,0,2)." ".substr($r->activity->activity_start,11,5);
        $r->activity->activity_start = $tstart_date;
    }

    if (isset($r->activity->activity_end))
    {
        $tstart_end = substr($r->activity->activity_end,6,4)."-".substr($r->activity->activity_end,3,2)."-".substr($r->activity->activity_end,0,2)." ".substr($r->activity->activity_end,11,5);
        $r->activity->activity_end = $tstart_end;
    }

    $isactivityExists = $db->getOneRecord("select 1 from activity where activity_id=$activity_id");
    if($isactivityExists){
        $tabble_name = "activity";
        $condition = "activity_id='$activity_id'";
        $column_names = array('activity_type','activity_sub_type','activity_start','activity_end','assign_to','broker_id','client_id','developer_id','enquiry_id','property_id','project_id','description','calling_count','remind','remind_before','remind_time','status','closure_comment','teams','sub_teams','modified_by','modified_date');
        $multiple=array('assign_to','properties','teams','sub_teams','project_id');
        $history = $db->historydata( $r->activity, $column_names, $tabble_name,$condition,$activity_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->activity, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Activity Updated successfully";
            $_SESSION['tmpactivity_id'] = $activity_id;
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
        $column_names = array('activity_type','enquiry','contact','description','teams','assignTo','remind','remindBefore','remind_time','status');
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

$app->get('/manualproperties', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT property_id, CONCAT(property.proptype,'_',property.property_id,' ',property.project_name,' ',property.building_name,' ',property.propsubtype,' ',property.property_for) property_name from property LEFT JOIN project ON property.project_id = project.project_id ORDER BY property.created_date DESC";
    $manual_properties = $db->getAllRecords($sql);
    echo json_encode($manual_properties);
});

$app->get('/addschdulemanually/:activity_id/:manual_property_id', function($activity_id, $manual_property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    
    $query = "INSERT INTO activity_details (start_date, end_date, activity_id, property_id, status, created_by, created_date)  VALUES('$created_date', '$created_date', '$activity_id','$manual_property_id','Visit Pending', '$created_by' ,'$created_date' )";
    $result = $db->insertByQuery($query);
    
    $sql = "SELECT *, DATE_FORMAT(a.start_date,'%d-%m-%Y %H:%i') AS start_date, DATE_FORMAT(a.end_date,'%d-%m-%Y %H:%i') AS end_date, DATE_FORMAT(a.visited_on,'%d-%m-%Y %H:%i') AS visited_on, a.status as status from activity_details as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN project as c on b.project_id = c.project_id where a.activity_id=".$activity_id;
    $activity_schedules = $db->getAllRecords($sql);
    echo json_encode($activity_schedules);
});



// task

$app->get('/task_list_ctrl/:next_page_id', function($next_page_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $permissions = $session['permissions'];
    $role = $session['role'];
    $sql = "select * from task limit 0";
    $countsql = "SELECT count(*) as task_count FROM task";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    { 
        $sql = "SELECT a.task_title,a.task_status, a.description,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, a.description, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, a.status as status,a.task_id as task_id, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by FROM task as a LEFT JOIN contact as c ON a.client_id = c.contact_id   LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id ORDER BY a.created_date DESC LIMIT $next_page_id, 30";
        $countsql = "SELECT count(*) as task_count FROM task as a  ";
    }
    else if (in_array("Branch Head", $role))
    {
        $sql = "SELECT a.task_title,a.task_status a.description,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, a.description, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, a.status as status,a.task_id as task_id, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by FROM task as a LEFT JOIN contact as c ON a.client_id = c.contact_id   LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id ORDER BY a.created_date DESC LIMIT $next_page_id, 30";

        $countsql = "SELECT count(*) as task_count FROM task as a  ";

    }
    else
    { 

        $sql = "SELECT a.task_title, a.task_status, a.description,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, a.description, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, a.status as status,a.task_id as task_id, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by FROM task as a LEFT JOIN contact as c ON a.client_id = c.contact_id   LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id WHERE a.created_by = $user_id ORDER BY a.created_date DESC LIMIT $next_page_id, 30";

        $countsql = "SELECT count(*) as task_count FROM task as a where a.created_by = $user_id ";

    }    
    
    $task = $db->getAllRecords($sql);

    $task_count = 0;
    $task_countdata = $db->getAllRecords($countsql);
    if ($task_countdata)
    {
         $task_count = $task_countdata[0]['task_count'];

    }
    if ($task_count>0)
    {
        $task[0]['task_count']=$task_count;
    }
    //$task[0]['sql']=$countsql;
    echo json_encode($task);
});

$app->get('/task_excel_download/:from_date/:to_date/:user_id', function($from_date,$to_date,$user_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $bo_id = $session['bo_id'];
    //$user_id = $session['user_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $permissions = $session['permissions'];
    $role = $session['role'];

    $from_date = $from_date.' 00:00:00';
    $to_date = $to_date.' 23:59:59'; 


    require('.././excel/PHPExcel.php');
    $objPHPExcel = new PHPExcel(); 
    $objPHPExcel->setActiveSheetIndex(0); 
    $rowCount = 1; 
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, 'Task List'); 
    $rowCount = 3;
    
    $objPHPExcel->getActiveSheet()->SetCellValue('A3','Task ID');
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("15");    
    $objPHPExcel->getActiveSheet()->SetCellValue('B3','Task Title');
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("50");
    $objPHPExcel->getActiveSheet()->SetCellValue('C3','Task');
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth("100");
    $objPHPExcel->getActiveSheet()->SetCellValue('D3','Client');
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("50");
    $objPHPExcel->getActiveSheet()->SetCellValue('E3','Teams');
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth("50");
    $objPHPExcel->getActiveSheet()->SetCellValue('F3','Assign To');
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth("50");
    $objPHPExcel->getActiveSheet()->SetCellValue('G3','Status');
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth("15");
    $objPHPExcel->getActiveSheet()->SetCellValue('H3','Task Status');
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth("15");
    $objPHPExcel->getActiveSheet()->SetCellValue('I3','Created by');
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth("50");
    $objPHPExcel->getActiveSheet()->SetCellValue('J3','Created Date');
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth("15");
    $objPHPExcel->getActiveSheet()->SetCellValue('K3','Modified Date');
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("15");
    $objPHPExcel->getActiveSheet()->SetCellValue('L3','comments');
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth("15");

    $rowCount = 4;

    $sql = "SELECT a.task_title,a.task_status, a.description,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, a.description, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, a.status as status,a.task_id as task_id, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by FROM task as a LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client'  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id WHERE (a.created_date BETWEEN '$from_date' AND '$to_date' ) ";
    
    if ($user_id == 0)
    {
        $sql .= " ORDER BY a.created_date";
    }
    else
    {
        $sql .= "and (a.created_by = $user_id or a.modified_by = $user_id or $user_id in (a.assign_to)) ORDER BY a.created_date";
    }
    //and a.created_by = $user_id
    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $comments = "";
            $id = $row['task_id'];
            $csql = "SELECT *,a.user_comment,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date FROM commenting as a WHERE a.category_id = $id and a.category = 'Task' ";
            $cstmt = $db->getRows($csql);
            if($cstmt->num_rows > 0)
            {
                $count = 1;
                while($crow = $cstmt->fetch_assoc())
                {
                    $comments .= $count.'. '. $crow['created_by'].' : '.$crow['created_date'].'  '.$crow['user_comment'].'     ';
                    $count++;
                }
            }
            
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,$row['task_id']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,$row['task_title']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,$row['description']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,$row['client']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount,$row['teams']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,$row['assign_to']);
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount,$row['status']);
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount,$row['task_status']);
            $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount,$row['created_by']);
            $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount,$row['created_date']);
            $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount,$row['modified_date']);
            $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount,$comments);
            $rowCount++;
        }
    }
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
    $ds        = DIRECTORY_SEPARATOR;
    $objWriter->save('uploads'.$ds.'task_list.xlsx'); 
    $stmt->close();
    $htmldata['htmlstring']='Done';
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->post('/search_task', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());

    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];

    $next_page_id = $r->searchdata->next_page_id;
    
    $role = $session['role'];
    $countsql = "SELECT count(*) as task_count FROM task ";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $searchsql = "SELECT *,a.description,a.property_id, a.enquiry_id, a.project_id, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(a.task_start,'%d-%m-%Y %H:%i') AS start_date,DATE_FORMAT(a.task_end,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.task_id as task_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from task as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN task_details as i on a.task_id = i.task_id where a.created_by > 0 ";

        $countsql = "SELECT count(*) as task_count FROM task as a WHERE created_by > 0 ";
    }
    else if (in_array("Branch Head", $role))
    {
        $searchsql = "SELECT *, a.description,a.property_id, a.enquiry_id, a.project_id,CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(a.task_start,'%d-%m-%Y %H:%i') AS start_date,DATE_FORMAT(a.task_end,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.task_id as task_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from task as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.created_by = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN task_details as i on a.task_id = i.task_id where (a.created_by = $user_id ".$team_query." ) "; 

        $countsql = "SELECT count(*) as task_count FROM task as a LEFT JOIN users as g on a.created_by = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id  WHERE a.created_by > 0 and (a.created_by = $user_id ".$team_query." ) ";
    }

    else
    {
        $searchsql = "SELECT *, a.description,a.property_id, a.enquiry_id, a.project_id,CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(a.task_start,'%d-%m-%Y %H:%i') AS start_date,DATE_FORMAT(a.task_end,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.task_id as task_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from task as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN task_details as i on a.task_id = i.task_id where (a.created_by = $user_id) "; 

        $countsql = "SELECT count(*) as task_count FROM task as a WHERE created_by > 0 and (created_by = $user_id ) ";

    }

    $sql = " ";
    if (isset($r->searchdata->status))
    {
        $first = "Yes";
        $new_data = $r->searchdata->status;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.status LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->task_type))
    {
        $first = "Yes";
        $new_data = $r->searchdata->task_type;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.task_type LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->assign_to))
    {
        $first = "Yes";
        $new_data = $r->searchdata->assign_to;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                } 
                $sql .= " a.assign_to LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }
    if (isset($r->searchdata->teams))
    {
        $first = "Yes";
        $new_data = $r->searchdata->teams;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                } 
                $sql .= " a.teams LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }
    if (isset($r->searchdata->client_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->client_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.client_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->broker_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->broker_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.broker_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->property_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->property_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.property_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->project_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->project_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.project_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->enquiry_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->enquiry_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.enquiry_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    if (isset($r->searchdata->task_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->task_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.task_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->created_date_from))
    {
        $created_date_from = $r->searchdata->created_date_from;
        $tcreated_date_from = substr($created_date_from,6,4)."-".substr($created_date_from,3,2)."-".substr($created_date_from,0,2);
        if (isset($r->searchdata->created_date_to))
        {
            $created_date_to = $r->searchdata->created_date_to;
            $tcreated_date_to = substr($created_date_to,6,4)."-".substr($created_date_to,3,2)."-".substr($created_date_to,0,2);
        }
        $sql .= " and a.created_date BETWEEN '".$tcreated_date_from."' AND '".$tcreated_date_to."' ";
    }

    if (isset($r->searchdata->task_start))
    {
        $task_start = $r->searchdata->task_start;
        $ttask_start = substr($task_start,6,4)."-".substr($task_start,3,2)."-".substr($task_start,0,2);
        if (isset($r->searchdata->task_end))
        {
            $task_end = $r->searchdata->task_end;
            $ttask_end = substr($task_end,6,4)."-".substr($task_end,3,2)."-".substr($task_end,0,2);
        }
        $sql .= " and a.task_start>= '".$ttask_start."' AND a.task_end <='".$ttask_end."' ";
    }
    
    $searchsql = $searchsql .  $sql .  " GROUP BY a.task_id ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";// CAST(a.property_id as UNSIGNED) DESC";

    $listtask = $db->getAllRecords($searchsql);

    $countsql = $countsql . $sql;
    $task_count = 0;
    $taskcountdata = $db->getAllRecords($countsql);
    if ($taskcountdata)
    {
         $task_count = $taskcountdata[0]['task_count'];

    }
    if ($task_count>0)
    {
        $listtask[0]['task_count']=$task_count;
    }
    //$properties[0]['countsql']=$countsql;
    $listtask[0]['searchsql']=$searchsql;
//$listtask[0]['sql']=$sql;
    //echo $sql;
    echo json_encode($listtask);

});

$app->post('/task_save_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('task_title'),$r->task);
    $db = new DbHandler();
    
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->task->created_by = $created_by;
    $r->task->created_date = $created_date;
    $r->task->task_status = "Active";

    
    $tabble_name = "task";
    $column_names = array('task_title','description', 'task_status', 'client_id', 'teams', 'sub_teams', 'assign_to', 'status', 'closure_comment','created_by','created_date');

    $multiple=array('assign_to','teams','sub_teams');
    $result = $db->insertIntoTable($r->task, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Task created successfully ";
        $response["task_id"] = $result;
        $_SESSION['tmptask_id'] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create task. Please try again";
        echoResponse(201, $response);
    }
});


$app->get('/task_edit_ctrl/:task_id', function($task_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    //$sql = "SELECT *, DATE_FORMAT(start_date,'%Y-%m-%d %H:%i') AS start_date, DATE_FORMAT(end_date,'%Y-%m-%d %H:%i') AS end_date from task where task_id=".$task_id;
    $sql = "SELECT *, DATE_FORMAT(created_date,'%d/%m/%Y %H:%i') AS created_Date FROM task where task_id=".$task_id;
    $task = $db->getAllRecords($sql);
    echo json_encode($task);
    
});

$app->post('/task_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('task_title'),$r->taskdata);
    $db = new DbHandler();
    $task_id  = $r->taskdata->task_id;

    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->taskdata->modified_by = $modified_by;
    $r->taskdata->modified_date = $modified_date;

    $istaskExists = $db->getOneRecord("select 1 from task where task_id=$task_id");
    if($istaskExists){
        $tabble_name = "task";
        $condition = "task_id='$task_id'";
        $column_names = array('task_title','description', 'task_status', 'client_id', 'teams', 'sub_teams', 'assign_to' ,'status', 'closure_comment','modified_by','modified_date');
        $multiple=array('assign_to','teams');
        $history = $db->historydata( $r->taskdata, $column_names, $tabble_name,$condition,$task_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->taskdata, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Task Updated successfully";
            $_SESSION['tmptask_id'] = $task_id;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Task. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Task with the provided task does not exists!";
        echoResponse(201, $response);
    }
});


$app->post('/task_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('task_id'),$r->taskdata);
    $db = new DbHandler();
    $task_id  = $r->taskdata->task_id;
    $istaskExists = $db->getOneRecord("select 1 from task where task_id=$task_id");
    if($istaskExists){
        $tabble_name = "task";
        $column_names = array('task_title','description','teams','assignto');
        $condition = "task_id='$task_id'";
        $result = $db->deleteIntoTable($r->taskdata, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Task Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Task. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Task with the provided Task does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selecttask', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];
    $sql = "SELECT task_id, CONCAT(task_id,'-',task_title) as task FROM task where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET($user_id , assign_to))  ORDER BY created_date DESC ";
    $task_list= $db->getAllRecords($sql);
    echo json_encode($task_list);
});


// REFERRALS

$app->get('/referrals_list_ctrl/:cat/:next_page_id', function($cat,$next_page_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $emp_name = $session['emp_name'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $permissions = $session['permissions'];
    $role = $session['role'];
    $sql = "select * from referrals limit 0";
    $countsql = "SELECT count(*) as referrals_count FROM referrals";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    { 
        $sql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals as a WHERE a.contact_off = '$cat' ORDER BY a.broker_id LIMIT $next_page_id, 30";
        // $sql = "SELECT a.*, DATE_FORMAT(a.created_date, '%d-%m-%Y %H:%i') AS created_on FROM referrals AS a JOIN (SELECT referrals_id, MIN(created_date) AS min_created_date FROM referrals_comment GROUP BY referrals_id ) AS c ON c.referrals_id = a.referrals_id WHERE a.contact_off = '$cat' ORDER BY a.broker_id LIMIT $next_page_id, 30;";
        //pks 17-07-2023
        // $sql = "SELECT a.*,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals as a LEFT JOIN referrals_comment AS c ON c.referrals_id = a.referrals_id WHERE a.contact_off = '$cat' ORDER BY c.created_date ASC LIMIT $next_page_id, 30";
        
        $countsql = "SELECT count(*) as referrals_count FROM referrals as a WHERE a.contact_off = '$cat'  ";
    }
    else if (in_array("Branch Head", $role))
    {
        $sql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals as a WHERE contact_off = '$cat'  ORDER BY a.broker_id LIMIT $next_page_id, 30";
        //pks 17-07-2023
        // $sql = "SELECT a.*,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals as a LEFT JOIN referrals_comment AS c ON c.referrals_id = a.referrals_id WHERE a.contact_off = '$cat' ORDER BY c.created_date ASC LIMIT $next_page_id, 30";
        
        $countsql = "SELECT count(*) as referrals_count FROM referrals as a WHERE a.contact_off = '$cat'  ";

    }
    else
    { 
        // error_log($next_page_id, 3, "logfile1.log");
        // $sql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals as a WHERE a.contact_off = '$cat' and a.assigned_to LIKE '%$emp_name%' ORDER BY a.broker_id LIMIT $next_page_id, 30";
        //pks 17-07-2023
        $sql = "SELECT a.*, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals AS a LEFT JOIN referrals_comment AS c ON a.referrals_id = c.referrals_id WHERE a.contact_off = '$cat' AND a.assigned_to LIKE '%$emp_name%' ORDER BY c.created_date ASC LIMIT $next_page_id, 30";
        
        $countsql = "SELECT count(*) as referrals_count FROM referrals as a where  a.contact_off = '$cat' and  a.assigned_to LIKE '%$emp_name%'   ";

    }   
    // console.log($sql); 
    
    $referrals = $db->getAllRecords($sql);
    $referrals[0]['sql']=$sql;
    $referrals_count = 0;
    $referrals_countdata = $db->getAllRecords($countsql);
    if ($referrals_countdata)
    {
         $referrals_count = $referrals_countdata[0]['referrals_count'];

    }
    if ($referrals_count>0)
    {
        $referrals[0]['referrals_count']=$referrals_count;
    }
    echo json_encode($referrals);
});


$app->post('/search_referrals', function() use ($app) {

    $db = new DbHandler();
    $session = $db->getSession();
    $emp_name = $session['emp_name'];
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());

    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];

    $role = $session['role'];

    $next_page_id = $r->searchdata->next_page_id;
    $cat = $r->searchdata->cat;
    
    $role = $session['role'];
    $searchsql = "select * from referrals limit 0";
    $countsql = "SELECT count(*) as referrals_count FROM referrals";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    { 
        // $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals as a WHERE contact_off = '$cat'  ";
        $searchsql = "SELECT a.*,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals as a LEFT JOIN referrals_comment AS c ON c.referrals_id = a.referrals_id WHERE contact_off = '$cat' ";
        // $countsql = "SELECT count(*) as referrals_count FROM referrals as a WHERE a.contact_off = '$cat'   ";
        $countsql = "SELECT count(*) as referrals_count FROM referrals as a WHERE a.contact_off = '$cat' ";
    }
    else if (in_array("Branch Head", $role))
    {
        // $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals as a WHERE contact_off = '$cat' ";
        $searchsql = "SELECT a.*,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals as a LEFT JOIN referrals_comment AS c ON c.referrals_id = a.referrals_id WHERE contact_off = '$cat' ";
        // $countsql = "SELECT count(*) as referrals_count FROM referrals as a WHERE a.contact_off = '$cat'   ";
        $countsql = "SELECT count(*) as referrals_count FROM referrals as a WHERE a.contact_off = '$cat' ";

    }
    else
    { 
        // $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals as a WHERE a.contact_off = '$cat' and a.assigned_to LIKE '%$emp_name%'  ";
        $searchsql = "SELECT a.*,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals as a LEFT JOIN referrals_comment AS c ON a.referrals_id = c.referrals_id WHERE a.contact_off = '$cat' AND a.assigned_to LIKE '%$emp_name%' ";
        // $countsql = "SELECT count(*) as referrals_count FROM referrals as a where  a.contact_off = '$cat' and  a.assigned_to LIKE '%$emp_name%'";
        $countsql = "SELECT count(*) as referrals_count FROM referrals as a where  a.contact_off = '$cat' and  a.assigned_to LIKE '%$emp_name%' ";

    }     

    if (isset($r->searchdata->broker_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->broker_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.broker_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->mobile_no))
    {
        $first = "Yes";
        $new_data = $r->searchdata->mobile_no;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.mobile_no LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->email))
    {
        $first = "Yes";
        $new_data = $r->searchdata->email;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.email LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->assigned_to))
    {
        $first = "Yes";
        $new_data = $r->searchdata->assigned_to;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.assigned_to LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->teams))
    {
        $first = "Yes";
        $new_data = $r->searchdata->teams;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.teams LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->sub_teams))
    {
        $first = "Yes";
        $new_data = $r->searchdata->sub_teams;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.sub_teams LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->groups_names))
    {
        $first = "Yes";
        $new_data = $r->searchdata->groups_names;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.groups_names LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    
    // ----------------------------------------------pks 17-07-2023-----------------------------------
    if (isset($r->searchdata->created_date_from))
    {
        // error_log($r->searchdata->created_date_from, 3, "logfile.log");
        $new_data1 = $r->searchdata->created_date_from;
        $date1 = DateTime::createFromFormat('d/m/Y', $new_data1);
        $date1_time = date_format($date1, 'Y-m-d 00:00:00');
        // $date1_time = date_format($date1, 'Y-m-d H:i:s');
        if ($new_data1)
        {
            $sql .= "AND c.created_date >= '$date1_time'";
            // $sql .= "AND created_date >= '$date1_time'";
        }
    }

    if (isset($r->searchdata->created_date_to))
    {
        // error_log($r->searchdata->created_date_to, 3, "logfile.log");
        $new_data2 = $r->searchdata->created_date_to;
        $date2 = DateTime::createFromFormat('d/m/Y', $new_data2);
        $date2_time = date_format($date2, 'Y-m-d 00:00:00');
        // $date2_time = date_format($date2, 'Y-m-d H:i:s');
        if ($new_data2)
        {
            $sql .= "AND c.created_date <= '$date2_time'";
            // $sql .= "AND created_date <= '$date2_time'";
        }
    }
//-------------------------------------------------pks end ------------------------------------------------

    if (isset($r->searchdata->sub_group))
    {
        $first = "Yes";
        $new_data = $r->searchdata->sub_group;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.sub_group LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    if (isset($r->searchdata->status))
    {
        $first = "Yes";
        $value = $r->searchdata->status;
        if ($value)
        {
            $sql .= " AND ( a.status LIKE '".$value."' )";
        }
    }

    
    // $searchsql .=  $sql .  " ORDER BY a.broker_id LIMIT $next_page_id, 30";// CAST(a.property_id as UNSIGNED) DESC";
    $searchsql .=  $sql .  " ORDER BY c.created_date ASC LIMIT $next_page_id, 30";

    $referrals = $db->getAllRecords($searchsql);

    $countsql = $countsql . $sql;
    $referrals_count = 0;
    $referralscountdata = $db->getAllRecords($countsql);
    if ($referralscountdata)
    {
         $referrals_count = $referralscountdata[0]['referrals_count'];

    }
    if ($referrals_count>0)
    {
        $referrals[0]['referrals_count']=$referrals_count;
    }
    $referrals[0]['$searchsql']=$searchsql;

    echo json_encode($referrals);
});

$app->get('/getdatavalues_referrals/:cat/:field_name', function($cat, $field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM referrals WHERE contact_off = '$cat'  ORDER BY $field_name";

    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});


// REFERRALS PROPERTY

$app->get('/referrals_property_ctrl/:next_page_id/:otp_ref', function($next_page_id,$otp_ref) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    if (!$otp_ref)
    {
        return;
    }

    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $role = $session['role'];
    
    $sql = "SELECT referral_propertydata_otp, referral_propertydata_otp_created FROM users WHERE user_id = $user_id"; 
    $otpdata = $db->getAllRecords($sql);
    if ($otpdata)
    {
        $org_otp = $otpdata[0]['referral_propertydata_otp'];
        $org_referral_otp_created = $otpdata[0]['referral_propertydata_otp_created'];
    }
    if ($org_otp == $otp_ref  || in_array("Admin", $role) || in_array("Sub Admin", $role))
    {

    }
    else{
        $response["status"] = "error";
        $response["message"] = "Otp Not Matching, try again ...";
        echoResponse(201, $response);
        return;
    }
    

    $emp_name = $session['emp_name'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $permissions = $session['permissions'];
    $role = $session['role'];
    $sql = "select * from referrals limit 0";
    $countsql = "SELECT count(*) as referrals_count FROM referrals_proprty";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    { 
        $sql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals_property as a  ORDER BY a.building_name LIMIT $next_page_id, 30";

        $countsql = "SELECT count(*) as referrals_count FROM referrals_property as a  ";
    }
    else if (in_array("Branch Head", $role))
    {
        $sql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals_property as a  ORDER BY a.building_name LIMIT $next_page_id, 30";

        $countsql = "SELECT count(*) as referrals_count FROM referrals_property as a  ";

    }
    else
    { 

        $sql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals_property as a ORDER BY a.building_name LIMIT $next_page_id, 30";
        //WHERE  a.assigned_to = '$emp_name' 
        $countsql = "SELECT count(*) as referrals_count FROM referrals_property as a ";
        //where    a.assigned_to = '$emp_name
    }   
    
    
    $referrals = $db->getAllRecords($sql);
    //$referrals[0]['sql']=$sql;
    $referrals_count = 0;
    $referrals_countdata = $db->getAllRecords($countsql);
    if ($referrals_countdata)
    {
         $referrals_count = $referrals_countdata[0]['referrals_count'];

    }
    if ($referrals_count>0)
    {
        $referrals[0]['referrals_count']=$referrals_count;
    }
    echo json_encode($referrals);
});


$app->post('/search_referrals_property', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());

    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $otp_ref = $r->searchdata->otp;
    $permissions = $session['permissions'];

    $role = $session['role'];

    $next_page_id = $r->searchdata->next_page_id;
    
    
    $role = $session['role'];
    $tsql = "SELECT referral_propertydata_otp, referral_propertydata_otp_created FROM users WHERE user_id = $user_id"; 
    $otpdata = $db->getAllRecords($tsql);
    if ($otpdata)
    {
        $org_otp = $otpdata[0]['referral_propertydata_otp'];
        $org_referral_otp_created = $otpdata[0]['referral_propertydata_otp_created'];
    }
    if ($org_otp == $otp_ref  || in_array("Admin", $role) || in_array("Sub Admin", $role))
    {

    }
    else{
        $response["status"] = "error";
        $response["message"] = "Otp Not Matching, try again ...";
        echoResponse(201, $response);
        return;
    }


    
    $searchsql = "select * from referrals_property limit 0";
    $countsql = "SELECT count(*) as referrals_count FROM referrals_property";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    { 
        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals_property as a where a.referral_property_id > 0  ";

        $countsql = "SELECT count(*) as referrals_count FROM referrals_property as a  where a.referral_property_id > 0 ";
    }
    else if (in_array("Branch Head", $role))
    {
        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals_property as a  where a.referral_property_id > 0 ";

        $countsql = "SELECT count(*) as referrals_count FROM referrals_property as a where a.referral_property_id > 0 ";

    }
    else
    { 

        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals_property as a  where a.referral_property_id > 0 ";

        $countsql = "SELECT count(*) as referrals_count FROM referrals as a   where a.referral_property_id > 0  ";

    }     


   

    
    if (isset($r->searchdata->assign_to))
    {
        $first = "Yes";
        $new_data = $r->searchdata->assign_to;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.assign_to LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }


    if (isset($r->searchdata->contact_person))
    {
        $first = "Yes";
        $new_data = $r->searchdata->contact_person;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.contact_person LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    if (isset($r->searchdata->mobile_no))
    {
        $first = "Yes";
        $new_data = $r->searchdata->mobile_no;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.mobile_no LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    if (isset($r->searchdata->email_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->email_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.email_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    



    if (isset($r->searchdata->building_name))
    {
        $first = "Yes";
        $new_data = $r->searchdata->building_name;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.building_name LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->group_name))
    {
        $first = "Yes";
        $new_data = $r->searchdata->group_name;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.group_name LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->sub_group))
    {
        $first = "Yes";
        $new_data = $r->searchdata->sub_group;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.sub_group LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->sub_team))
    {
        $first = "Yes";
        $new_data = $r->searchdata->sub_team;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.sub_team LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->team))
    {
        $first = "Yes";
        $new_data = $r->searchdata->team;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.team LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->status))
    {
        $first = "Yes";
        $value = $r->searchdata->status;
        if ($value)
        {
            $sql .= " AND ( a.status LIKE '".$value."' )";
        }
    }
    
    $searchsql .=  $sql .  " ORDER BY a.building_name LIMIT $next_page_id, 30";// CAST(a.property_id as UNSIGNED) DESC";

    $referrals = $db->getAllRecords($searchsql);

    $countsql = $countsql . $sql;
    $referrals_count = 0;
    $referralscountdata = $db->getAllRecords($countsql);
    if ($referralscountdata)
    {
         $referrals_count = $referralscountdata[0]['referrals_count'];

    }
    if ($referrals_count>0)
    {
        $referrals[0]['referrals_count']=$referrals_count;
    }
    $referrals[0]['$searchsql']=$searchsql;
    

    echo json_encode($referrals);
});


$app->post('/search_referrals_propertyall', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());

    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $otp_ref = $r->searchdata->otp;
    $permissions = $session['permissions'];

    $role = $session['role'];

    $next_page_id = $r->searchdata->next_page_id;
    
    
    $role = $session['role'];
    $tsql = "SELECT referral_propertydata_otp, referral_propertydata_otp_created FROM users WHERE user_id = $user_id"; 
    $otpdata = $db->getAllRecords($tsql);
    if ($otpdata)
    {
        $org_otp = $otpdata[0]['referral_propertydata_otp'];
        $org_referral_otp_created = $otpdata[0]['referral_propertydata_otp_created'];
    }
    if ($org_otp == $otp_ref  || in_array("Admin", $role) || in_array("Sub Admin", $role))
    {

    }
    else{
        $response["status"] = "error";
        $response["message"] = "Otp Not Matching, try again ...";
        echoResponse(201, $response);
        return;
    }

    $find_what = $r->searchdata->find_what;
    
    $searchsql = "select * from referrals_property limit 0";
    $countsql = "SELECT count(*) as referrals_count FROM referrals_property";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    { 
        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals_property as a where a.referral_property_id > 0  ";

        $countsql = "SELECT count(*) as referrals_count FROM referrals_property as a  where a.referral_property_id > 0 ";
    }
    else if (in_array("Branch Head", $role))
    {
        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals_property as a  where a.referral_property_id > 0 ";

        $countsql = "SELECT count(*) as referrals_count FROM referrals_property as a where a.referral_property_id > 0 ";

    }
    else
    { 

        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM referrals_property as a  where a.referral_property_id > 0 ";

        $countsql = "SELECT count(*) as referrals_count FROM referrals as a   where a.referral_property_id > 0  ";

    }     
    
    //'contact_person','mobile_no','email_id','building_name', 'property_type', 'locality', 'developer_name', 'registration_date', 'lease_start_date', 'lease_end_date', 'transaction_type', 'area', 'rate', 'floor', 'buyer_company', 'buyer_name', 'lease', 'representative_role', 'tenure', 'rate_1', 'security_deposit', 'condition_space', 'car_park_slots', 'rent_free_period', 'cam_charge', 'carpet', 'chargeable_area', 'starting_rate', 'landlord_company', 'landlord_name', 'landlord_designation', 'escalation_per', 'escalation_monthly', 'lock_in_expiry', 'assign_to',  'team', 'sub_team', 'group_name', 'sub_group', 


    $sql = " AND  (a.building_name LIKE '%".$find_what."%' OR a.contact_person LIKE '%".$find_what."%' OR a.group_name LIKE '%".$find_what."%' OR a.sub_group LIKE '%".$find_what."%' OR a.locality LIKE '%".$find_what."%' OR a.developer_name LIKE '%".$find_what."%'  OR a.buyer_company LIKE '%".$find_what."%'  OR a.buyer_name LIKE '%".$find_what."%'  OR a.landlord_company LIKE '%".$find_what."%'  OR a.landlord_name LIKE '%".$find_what."%'  OR a.team LIKE '%".$find_what."%' OR a.sub_team LIKE '%".$find_what."%' OR a.mobile_no LIKE '%".$find_what."%'  OR a.email_id LIKE '%".$find_what."%') ";


    $searchsql .=  $sql .  " ORDER BY a.building_name LIMIT $next_page_id, 30";// CAST(a.property_id as UNSIGNED) DESC";

    $referrals = $db->getAllRecords($searchsql);

    $countsql = $countsql . $sql;
    $referrals_count = 0;
    $referralscountdata = $db->getAllRecords($countsql);
    if ($referralscountdata)
    {
         $referrals_count = $referralscountdata[0]['referrals_count'];

    }
    if ($referrals_count>0)
    {
        $referrals[0]['referrals_count']=$referrals_count;
    }
    $referrals[0]['$searchsql']=$searchsql;
    

    echo json_encode($referrals);
});


$app->get('/save_referrals_property_comment/:referral_property_id/:closure_comment/:next_date', function($referral_property_id, $closure_comment,$next_date) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest") 
    {
        return;
    }
    $response = array();
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');

    $sql = "INSERT INTO referrals_property_comment (referral_property_id, closure_comment,next_date,created_by,created_date) VALUES ('$referral_property_id','$closure_comment','$next_date','$created_by','$created_date')";    
    $result = $db->insertByQuery($sql);
    $response["comment_id"] = $result;
    $response["status"] = "success";
    $response["message"] = "Comment Added successfully";
    echoResponse(200, $response);
});

$app->get('/delete_selected_propertydata/:data', function($data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "DELETE FROM referrals_property WHERE referral_property_id IN($data)" ;  
    $result = $db->updateByQuery($sql);
    $response["status"] = "success";
    $response["message"] = "Deleted Selected Data !!!";
    echo json_encode($response);
});


$app->get('/delete_selected_referrals/:data', function($data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "DELETE FROM referrals WHERE referrals_id IN($data)" ;  
    $result = $db->updateByQuery($sql);
    $response["status"] = "success";
    $response["message"] = "Deleted Selected Data !!!";
    echo json_encode($response);
});

$app->get('/change_assign_to/:data/:reassign_to', function($data,$reassign_to) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE referrals_property set assign_to = '$reassign_to' WHERE referral_property_id IN($data)" ;  
    $result = $db->updateByQuery($sql);
    $response["status"] = "success";
    $response["message"] = "Reassigned Selected Data !!!";
    echo json_encode($response);
});

$app->get('/change_assign_to_referral/:data/:reassign_to', function($data,$reassign_to) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE referrals set assigned_to = '$reassign_to' WHERE referrals_id IN($data)" ;  
    $result = $db->updateByQuery($sql);
    $response["status"] = "success";
    $response["message"] = "Reassigned Selected Data !!!";
    echo json_encode($response);
});


$app->get('/show_history_referral_property/:referral_property_id', function($referral_property_id) use ($app) 
{
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];    
    $senddata =  array();
    $htmldata = array();
    $htmlstring = '';
    $htmlstring .= '<div class="myTicker">
                        <table class="table table-bordered table-striped" >
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Comment</th>
                                    <th>Next Date</th>
                                </tr>
                            </thead> 
                            <tbody>';

    $sql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on,DATE_FORMAT(a.next_date,'%d-%m-%Y %H:%i') AS next_date FROM referrals_property_comment as a where a.referral_property_id  = $referral_property_id ORDER BY a.created_date ";
    
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)    
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<tr><td>'.$row['created_on'].'</td><td>'.$row['closure_comment'].'</td><td>'.$row['next_date'].'</td></tr>';
        }
    }
    $stmt->close();
    $htmlstring .='</table></div>';        
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/getdatavalues_referrals_property/:field_name', function( $field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM referrals_property   ORDER BY $field_name";
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});

$app->get('/change_sub_group/:group_name', function( $group_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT sub_group FROM referrals_property WHERE group_name in ('$group_name') ORDER BY sub_group";
    $sub_groups = $db->getAllRecords($sql);
    echo json_encode($sub_groups);
});


$app->post('/uploadreferrals_property_files', function() use ($app) {
    session_start();
    $response = array();
    $sql  = "";
    $db = new DbHandler();

    if (empty($_FILES['file_imports'])) {
        $response["status"] = "error";
        $response["message"] = "No files found for upload!";
        echoResponse(201, $response);
        return; // terminate
    }
    
    $images = $_FILES['file_imports'];
    $paths= array();
    $filenames = $images['name'];
    
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

$app->post('/uploadreferrals_property_data', function() use ($app) {
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

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->convertdata->created_by = $created_by;
    $r->convertdata->created_date = $created_date;
    
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
    $count = 0;
    
    
    for ($row = 1; $row <= $highestRow; ++$row) 
    {
        
        if ($objWorksheet->getCellByColumnAndRow(0, $row)->getValue()=='Building Name')
        {

        }
        else
        {
            $count ++;

            $contact_person = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
            $contact_person = str_replace("'","\'",$contact_person);
            $r->convertdata->contact_person=$contact_person;

            $r->convertdata->mobile_no=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue(); 
            $r->convertdata->email_id=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue(); 

            $building_name = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
            $building_name = str_replace("'","\'",$building_name);
            $r->convertdata->building_name=$building_name;

            $r->convertdata->property_type=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue(); 

            $locality = $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
            $locality = str_replace("'","\'",$locality);
            $r->convertdata->locality=$locality;
            
            $developer_name = $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
            $developer_name = str_replace("'","\'",$developer_name);
            $r->convertdata->developer_name=$developer_name;

            $r->convertdata->registration_date=$objWorksheet->getCellByColumnAndRow(7, $row)->getValue(); 
            $r->convertdata->lease_start_date=$objWorksheet->getCellByColumnAndRow(8, $row)->getValue(); 
            $r->convertdata->lease_end_date=$objWorksheet->getCellByColumnAndRow(9, $row)->getValue(); 
            $r->convertdata->transaction_type=$objWorksheet->getCellByColumnAndRow(10, $row)->getValue(); 
            $r->convertdata->area=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue(); 
            $r->convertdata->rate=$objWorksheet->getCellByColumnAndRow(12, $row)->getValue(); 
            $r->convertdata->floor=$objWorksheet->getCellByColumnAndRow(13, $row)->getValue();    

            $buyer_company = $objWorksheet->getCellByColumnAndRow(14, $row)->getValue();
            $buyer_company = str_replace("'","\'",$buyer_company);
            $r->convertdata->buyer_company=$buyer_company; 
            
            $buyer_name = $objWorksheet->getCellByColumnAndRow(15, $row)->getValue();
            $buyer_name = str_replace("'","\'",$buyer_name);
            $r->convertdata->buyer_name=$buyer_name;
            //two column added
           $r->convertdata->buyer_mobile=$objWorksheet->getCellByColumnAndRow(16, $row)->getValue(); 
            $r->convertdata->buyer_email=$objWorksheet->getCellByColumnAndRow(17, $row)->getValue(); 


            $r->convertdata->lease=$objWorksheet->getCellByColumnAndRow(18, $row)->getValue(); 
            $r->convertdata->representative_role=$objWorksheet->getCellByColumnAndRow(19, $row)->getValue(); 
            $r->convertdata->tenure=$objWorksheet->getCellByColumnAndRow(20, $row)->getValue(); 
            $r->convertdata->rate_1=$objWorksheet->getCellByColumnAndRow(21, $row)->getValue(); 
            $r->convertdata->security_deposit=$objWorksheet->getCellByColumnAndRow(22, $row)->getValue(); 
            $r->convertdata->condition_space=$objWorksheet->getCellByColumnAndRow(23, $row)->getValue(); 
            $r->convertdata->car_park_slots=$objWorksheet->getCellByColumnAndRow(24, $row)->getValue(); 
            $r->convertdata->rent_free_period=$objWorksheet->getCellByColumnAndRow(25, $row)->getValue(); 
            $r->convertdata->cam_charge=$objWorksheet->getCellByColumnAndRow(26, $row)->getValue(); 
            $r->convertdata->carpet=$objWorksheet->getCellByColumnAndRow(27, $row)->getValue(); 
            $r->convertdata->chargeable_area=$objWorksheet->getCellByColumnAndRow(28, $row)->getValue(); 
            $r->convertdata->starting_rate=$objWorksheet->getCellByColumnAndRow(29, $row)->getValue(); 

            
            $landlord_company = $objWorksheet->getCellByColumnAndRow(30, $row)->getValue();
            $landlord_company = str_replace("'","\'",$landlord_company);
            $r->convertdata->landlord_company=$landlord_company;

            $landlord_name = $objWorksheet->getCellByColumnAndRow(31, $row)->getValue();
            $landlord_name = str_replace("'","\'",$landlord_name);
            $r->convertdata->landlord_name=$landlord_name;  

            $r->convertdata->landlord_designation=$objWorksheet->getCellByColumnAndRow(32, $row)->getValue(); 
            $r->convertdata->escalation_per=$objWorksheet->getCellByColumnAndRow(33, $row)->getValue(); 
            $r->convertdata->escalation_monthly=$objWorksheet->getCellByColumnAndRow(34, $row)->getValue(); 
            $r->convertdata->lock_in_expiry=$objWorksheet->getCellByColumnAndRow(35, $row)->getValue(); 

            
            $r->convertdata->team=$objWorksheet->getCellByColumnAndRow(36, $row)->getValue(); 
            $r->convertdata->sub_team=$objWorksheet->getCellByColumnAndRow(37, $row)->getValue(); 
            $r->convertdata->assign_to=$objWorksheet->getCellByColumnAndRow(38, $row)->getValue(); 
            
            $r->convertdata->group_name=$objWorksheet->getCellByColumnAndRow(39, $row)->getValue(); 
            $r->convertdata->sub_group=$objWorksheet->getCellByColumnAndRow(40, $row)->getValue(); 
            

            $tabble_name = "referrals_property";

            $column_names = array('contact_person','mobile_no','email_id','building_name', 'property_type', 'locality', 'developer_name', 'registration_date', 'lease_start_date', 'lease_end_date', 'transaction_type', 'area', 'rate', 'floor', 'buyer_company', 'buyer_name','buyer_mobile', 'buyer_email', 'lease', 'representative_role', 'tenure', 'rate_1', 'security_deposit', 'condition_space', 'car_park_slots', 'rent_free_period', 'cam_charge', 'carpet', 'chargeable_area', 'starting_rate', 'landlord_company', 'landlord_name', 'landlord_designation', 'escalation_per', 'escalation_monthly', 'lock_in_expiry', 'assign_to',  'team', 'sub_team', 'group_name', 'sub_group', 'created_by', 'created_date');

            $multiple=array("");
            $result = $db->insertIntoTable($r->convertdata, $column_names, $tabble_name, $multiple);
            if ($result!=NULL)
            {
                $success = true;
            }
           
        }
    }
           
    if ($success)
    {
        $response["status"] = "success";
        $response["message"] = "Referrals Data uploaded [Total Records - ".$count."] !! ";
        echoResponse(201, $response);
    }
    else
    {
        $response["status"] = "error";
        $response["message"] = "Referrals Data not uploaded, Please Check Excel file !! ";
        echoResponse(201, $response);
    }
});

$app->get('/delete_referral_property/:referrals_id', function($referrals_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "DELETE from referrals_property where referral_property_id = $referrals_id ";
    $result = $db->updateByQuery($sql);
});

$app->get('/change_referral_property_status/:status/:referral_property_id', function($status,$referral_property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE referrals_property set status = '$status' where referral_property_id = $referral_property_id ";
    $result = $db->updateByQuery($sql);
});


$app->get('/change_referral_status/:status/:referrals_id', function($status,$referrals_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE referrals set status = '$status' where referrals_id = $referrals_id ";
    $result = $db->updateByQuery($sql);
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

$app->get('/selecttypelist', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT type from dropdowns GROUP By type ORDER BY type ";
    $typelists = $db->getAllRecords($sql);
    echo json_encode($typelists);
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
    $parent_type = $r->dropdown->parent_type;
    $isDropdownsExists = $db->getOneRecord("select 1 from dropdowns where type='$type' and value='$value' and parent_type='$parent_type' ");
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
    $sql = "SELECT * from dropdowns WHERE type = '$dropdown_type' ORDER BY CAST(sequence_number  as UNSIGNED) ";
    $dropdowns = $db->getAllRecords($sql);
    echo json_encode($dropdowns);
});

$app->get('/selectdropdownsNew/:dropdown_type/:cat', function($dropdown_type,$cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from dropdowns WHERE type = '$dropdown_type' and parent_type = '$cat' ORDER BY CAST(sequence_number  as UNSIGNED)";
    $dropdowns = $db->getAllRecords($sql);
    echo json_encode($dropdowns);
});

$app->get('/change_enquirysubtype/:enquiry_type', function($enquiry_type) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from dropdowns WHERE type = 'PROP_SUB_TYPE' and parent_type = '$enquiry_type' ORDER BY display_value";
    $dropdowns = $db->getAllRecords($sql);
    echo json_encode($dropdowns);
});

$app->get('/change_suitablefor/:propsubtype', function($propsubtype) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from dropdowns WHERE type = 'PROP_SUB_TYPE' and parent_type = '$propsubtype' ORDER BY display_value";
    $dropdowns = $db->getAllRecords($sql);
    echo json_encode($dropdowns);
});


$app->get('/change_sub_source/:source_channel', function($source_channel) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from dropdowns WHERE type = 'SUB_SOURCE' and parent_type in ('$source_channel') ORDER BY display_value";
    $dropdowns = $db->getAllRecords($sql);
    $dropdowns[0]['sql']=$sql;
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
    $created_date = date('Y-m-d H:i:s');
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
    $created_date = date('Y-m-d H:i:s');
    $r->team->created_by = $created_by;
    $r->team->created_date = $created_date;
    $isTeamsExists = $db->getOneRecord("select 1 from teams where team_name='$team_name'");
    if(!$isTeamsExists){
        $tabble_name = "teams";
        $column_names = array('team_name','team_description','sharing_per','team_type','created_by','created_date');
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
    $modified_date = date('Y-m-d H:i:s');
    $r->team->modified_by = $modified_by;
    $r->team->modified_date = $modified_date;
    $isteamsExists = $db->getOneRecord("select 1 from teams where team_id=$team_id");
    if($isteamsExists){
        $tabble_name = "teams";
        $column_names = array('team_name','team_description','sharing_per','team_type', 'modified_by','modified_date');
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
    $sql = "SELECT team_id,team_name from teams ORDER BY team_name";
    $teams = $db->getAllRecords($sql);
    echo json_encode($teams);
});


$app->get('/selectteams_team_type/:team_type', function($team_type) use ($app) {
    // error_log($team_type, 3, "logfile1.log");
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT team_id,team_name from teams WHERE team_type = '$team_type' ORDER BY team_name";
    // if ($temp_type=="All")
    if ($temp_type="All")
    {
        $sql = "SELECT team_id,team_name from teams ORDER BY team_name";
    }
    $teams = $db->getAllRecords($sql);
    echo json_encode($teams);
});

$app->get('/selectteamfromid/:user_id', function($user_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT a.teams FROM employee as a LEFT JOIN users as b ON a.emp_id = b.emp_id WHERE b.user_id = $user_id ";
    $team_id = $db->getAllRecords($sql);
    echo json_encode($team_id);
});


// SUB TEAMS

$app->get('/sub_teams_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from sub_teams ORDER BY sub_team_name";
    $sub_teams = $db->getAllRecords($sql);
    echo json_encode($sub_teams);
});


$app->post('/sub_teams_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('sub_team_name'),$r->sub_team);
    $db = new DbHandler();
    $sub_team_name = $r->sub_team->sub_team_name;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->sub_team->created_by = $created_by;
    $r->sub_team->created_date = $created_date;
    $issub_teamsExists = $db->getOneRecord("select 1 from sub_teams where sub_team_name='$sub_team_name'");
    if(!$issub_teamsExists){
        $tabble_name = "sub_teams";
        $column_names = array('sub_team_name','sub_team_description','created_by','created_date');
        $multiple=array("");
        $result = $db->insertIntoTable($r->sub_team, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "sub_teams created successfully";
            $response["sub_team_id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create sub_teams. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A sub_teams with the provided sub_teams exists!";
        echoResponse(201, $response);
    }
});


$app->get('/sub_teams_edit_ctrl/:sub_team_id', function($sub_team_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from sub_teams where sub_team_id=".$sub_team_id;
    $sub_teams = $db->getAllRecords($sql);
    echo json_encode($sub_teams);
    
});

$app->post('/sub_teams_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('sub_team_name'),$r->sub_team_data);
    $db = new DbHandler();
    $sub_team_id  = $r->sub_team_data->sub_team_id;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->sub_team_data->modified_by = $modified_by;
    $r->sub_team_data->modified_date = $modified_date;
    $issub_teamsExists = $db->getOneRecord("select 1 from sub_teams where sub_team_id=$sub_team_id");
    if($issub_teamsExists){
        $tabble_name = "sub_teams";
        $column_names = array('sub_team_name','sub_team_description', 'modified_by','modified_date');
        $condition = "sub_team_id='$sub_team_id'";
        $multiple=array("");
        $history = $db->historydata( $r->sub_team_data, $column_names, $tabble_name,$condition,$sub_team_id,$multiple, $modified_by, $modified_date);
        $result = $db->updateIntoTable($r->sub_team_data, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "sub_teams Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update sub_teams. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "sub_teams with the provided sub_teams does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/sub_teams_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('sub_team_name'),$r->sub_team_data);
    $db = new DbHandler();
    $sub_team_id  = $r->sub_team_data->sub_team_id;
    $issub_teamsExists = $db->getOneRecord("select 1 from sub_teams where sub_team_id=$sub_team_id");
    if($issub_teamsExists){
        $tabble_name = "sub_teams";
        $column_names = array('sub_team_name');
        $condition = "sub_team_id='$sub_team_id'";
        $result = $db->deleteIntoTable($r->sub_team_data, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "sub_teams Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete sub_teams. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "sub_teams with the provided sub_teams does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectsubteams', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT sub_team_id,sub_team_name from sub_teams ORDER BY sub_team_name";
    $sub_teams = $db->getAllRecords($sql);
    echo json_encode($sub_teams);
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
    // pks 26082023
    
    for ($row = 1; $row <= $highestRow; ++$row) 
    {   
        $r->convertdata->enquiry_for=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
        $r->convertdata->enquiry_type=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
        $r->convertdata->enquiry_off=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
        $r->convertdata->client=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
        $r->convertdata->client_id=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
        $r->convertdata->broker_involved=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
        $r->convertdata->broker=$objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
        $r->convertdata->con_status=$objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
        $r->convertdata->loan_req=$objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
        $r->convertdata->pre_leased=$objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
        $r->convertdata->priority=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
        $r->convertdata->preferred_streets=$objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
        $r->convertdata->preferred_areas=$objWorksheet->getCellByColumnAndRow(13, $row)->getValue();
        $r->convertdata->preferred_city=$objWorksheet->getCellByColumnAndRow(14, $row)->getValue();
        $r->convertdata->preferred_state=$objWorksheet->getCellByColumnAndRow(15, $row)->getValue();
        $r->convertdata->preferred_country=$objWorksheet->getCellByColumnAndRow(16, $row)->getValue();
        $r->convertdata->bath=$objWorksheet->getCellByColumnAndRow(17, $row)->getValue();
        $r->convertdata->furniture=$objWorksheet->getCellByColumnAndRow(18, $row)->getValue();
        $r->convertdata->tot_area1=$objWorksheet->getCellByColumnAndRow(19, $row)->getValue();
        $r->convertdata->tot_area2=$objWorksheet->getCellByColumnAndRow(20, $row)->getValue();
        $r->convertdata->sale_area1=$objWorksheet->getCellByColumnAndRow(21, $row)->getValue();
        $r->convertdata->sale_area2=$objWorksheet->getCellByColumnAndRow(22, $row)->getValue();
        $r->convertdata->area_para=$objWorksheet->getCellByColumnAndRow(23, $row)->getValue();
        $r->convertdata->carp_area1=$objWorksheet->getCellByColumnAndRow(24, $row)->getValue();
        $r->convertdata->carp_area2=$objWorksheet->getCellByColumnAndRow(25, $row)->getValue();
        $r->convertdata->carp_area_para=$objWorksheet->getCellByColumnAndRow(26, $row)->getValue();
        $r->convertdata->budget_range1=$objWorksheet->getCellByColumnAndRow(27, $row)->getValue();
        $r->convertdata->budget_range2=$objWorksheet->getCellByColumnAndRow(28, $row)->getValue();
        $r->convertdata->budget_range2_para=$objWorksheet->getCellByColumnAndRow(29, $row)->getValue();
        $r->convertdata->teams=$objWorksheet->getCellByColumnAndRow(30, $row)->getValue();
        $r->convertdata->sub_teams=$objWorksheet->getCellByColumnAndRow(31, $row)->getValue();
        $r->convertdata->assigned=$objWorksheet->getCellByColumnAndRow(32, $row)->getValue();
        $r->convertdata->source_channel=$objWorksheet->getCellByColumnAndRow(33, $row)->getValue();
        $r->convertdata->subsource_channel=$objWorksheet->getCellByColumnAndRow(34, $row)->getValue();
        $r->convertdata->campaign=$objWorksheet->getCellByColumnAndRow(35, $row)->getValue();
        $r->convertdata->status=$objWorksheet->getCellByColumnAndRow(36, $row)->getValue();
        $r->convertdata->stage=$objWorksheet->getCellByColumnAndRow(37, $row)->getValue();
        $r->convertdata->vastu_comp=$objWorksheet->getCellByColumnAndRow(38, $row)->getValue();
        $r->convertdata->groups=$objWorksheet->getCellByColumnAndRow(39, $row)->getValue();
        $r->convertdata->intrnal_comment=$objWorksheet->getCellByColumnAndRow(40, $row)->getValue();
        $r->convertdata->external_comment=$objWorksheet->getCellByColumnAndRow(41, $row)->getValue();
        $r->convertdata->height=$objWorksheet->getCellByColumnAndRow(42, $row)->getValue();
        $r->convertdata->frontage=$objWorksheet->getCellByColumnAndRow(43, $row)->getValue();
        $r->convertdata->reg_date=$objWorksheet->getCellByColumnAndRow(44, $row)->getValue();
        $r->convertdata->parking=$objWorksheet->getCellByColumnAndRow(45, $row)->getValue();
        $r->convertdata->portal_id=$objWorksheet->getCellByColumnAndRow(46, $row)->getValue();
        $r->convertdata->published=$objWorksheet->getCellByColumnAndRow(47, $row)->getValue();
        $r->convertdata->created_by=$objWorksheet->getCellByColumnAndRow(48, $row)->getValue();
        $r->convertdata->broker_id=$objWorksheet->getCellByColumnAndRow(49, $row)->getValue();
        $r->convertdata->preferred_area_id=$objWorksheet->getCellByColumnAndRow(50, $row)->getValue();
        $r->convertdata->budget_range1_para=$objWorksheet->getCellByColumnAndRow(51, $row)->getValue();
        $r->convertdata->sms_salestrainee=$objWorksheet->getCellByColumnAndRow(52, $row)->getValue();
        $r->convertdata->email_salestrainee=$objWorksheet->getCellByColumnAndRow(53, $row)->getValue();
        
        $createdDate = $objWorksheet->getCellByColumnAndRow(54, $row)->getValue();

        $r->convertdata->created_date=date("Y-m-d H:i:s", strtotime($createdDate));

        $tabble_name = "enquiry";
        $column_names = array('enquiry_for','enquiry_type','enquiry_off','client','client_id','broker_involved','broker','con_status','loan_req','pre_leased','priority','preferred_streets','preferred_areas','preferred_city','preferred_state','preferred_country','bath','furniture','tot_area1','tot_area2','sale_area1','sale_area2','area_para','carp_area1','carp_area2','carp_area_para','budget_range1','budget_range2','budget_range2_para','teams','sub_teams','assigned','source_channel','subsource_channel','campaign','status','stage','vastu_comp','groups','intrnal_comment','external_comment','height','frontage','reg_date','parking','portal_id','published','created_by','broker_id', 'preferred_area_id','budget_range1_para','sms_salestrainee','email_salestrainee','created_date');
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

$app->post('/uploadcontact_files', function() use ($app) {
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

$app->post('/uploadcontact', function() use ($app) {
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
    $a = array();
    $i=0;
    $j=0;
    for ($row = 1; $row <= $highestRow; ++$row)
    {   
        if($row == 1){
            
        }else{
            $checkPhoneExist = $objWorksheet->getCellByColumnAndRow(16, $row)->getValue();
            $check_sql = "SELECT * FROM `contact` WHERE `mob_no` = '$checkPhoneExist'";
            $rowspresent = $db->getRows($check_sql);
            if($rowspresent->num_rows > 0){
                $j++;
                $a = $checkPhoneExist;
                $response["contact_no"] = $a;
                // error_log($result, 3, "logfile2.log");
            }else{
                $i++;
                $r->convertdata->prefix=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
                $r->convertdata->contact_off=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
                $r->convertdata->name=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
                $r->convertdata->company_name=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
                $r->convertdata->add1=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
                $r->convertdata->add2=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
                $r->convertdata->locality_id=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
                $r->convertdata->area_id=$objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
                $r->convertdata->city=$objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
                $r->convertdata->state=$objWorksheet->getCellByColumnAndRow(9, $row)->getValue();
                $r->convertdata->country=$objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
                $r->convertdata->zip=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
                $r->convertdata->comp_logo=$objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
                $r->convertdata->name_title=$objWorksheet->getCellByColumnAndRow(13, $row)->getValue();
                $r->convertdata->f_name=$objWorksheet->getCellByColumnAndRow(14, $row)->getValue();
                $r->convertdata->l_name=$objWorksheet->getCellByColumnAndRow(15, $row)->getValue();
                $r->convertdata->mob_no=$objWorksheet->getCellByColumnAndRow(16, $row)->getValue();
                $r->convertdata->mob_no1=$objWorksheet->getCellByColumnAndRow(17, $row)->getValue();
                $r->convertdata->email=$objWorksheet->getCellByColumnAndRow(18, $row)->getValue();
                $r->convertdata->alt_phone_no=$objWorksheet->getCellByColumnAndRow(19, $row)->getValue();
                $r->convertdata->alt_phone_no1=$objWorksheet->getCellByColumnAndRow(20, $row)->getValue();
                $r->convertdata->contact_pic=$objWorksheet->getCellByColumnAndRow(21, $row)->getValue();
                $r->convertdata->designation=$objWorksheet->getCellByColumnAndRow(22, $row)->getValue();
                $r->convertdata->birth_date=$objWorksheet->getCellByColumnAndRow(23, $row)->getValue();
                
                $team_get = $objWorksheet->getCellByColumnAndRow(24, $row)->getValue();
                $teamcheck_sql = "SELECT * FROM `teams` WHERE `team_name` = '$team_get'";
                $teamrowspresent = $db->getAllRecords($teamcheck_sql);
                if(count($teamrowspresent) > 0){
                    $team_id = $teamrowspresent[0]['team_id'];
                    $r->convertdata->teams=$team_id;
                }
                else{
                    $r->convertdata->teams= 0;
                }
                
                $sub_team_get = $objWorksheet->getCellByColumnAndRow(25, $row)->getValue();
                $sub_teamcheck_sql = "SELECT * FROM `sub_teams` WHERE `sub_team_name` = '$sub_team_get'";
                $sub_teamrowspresent = $db->getAllRecords($sub_teamcheck_sql);
                if(count($sub_teamrowspresent) > 0){
                    $subteam_id = $sub_teamrowspresent[0]['sub_team_id'];
                    $r->convertdata->sub_teams=$subteam_id;
                }
                else{
                    $r->convertdata->sub_teams= 0;
                }
                
                $assign_to_get = $objWorksheet->getCellByColumnAndRow(26, $row)->getValue();
                $assign_to_get_array = explode(",", $assign_to_get);
                $a = array();
                foreach($assign_to_get_array as $b){
                    $name_data = explode(' ', $b);
                    $salu ="";
                    $fname ="";
                    $lname ="";
                    if (isset($name_data[0])) {
                        $salu = $name_data[0];
                    }
                    if (isset($name_data[1])) {
                        $fname = $name_data[1];
                    }
                    if (isset($name_data[2])) {
                        $lname = $name_data[2];
                    }
                    $assign_to_getcheck_sql = "SELECT * FROM `employee` WHERE `salu` = '$salu' AND `fname`= '$fname' AND `lname` = '$lname'";
                    // error_log($assign_to_getcheck_sql, 3, "logfile2.log");
                    $assign_to_getrowspresent = $db->getAllRecords($assign_to_getcheck_sql);
                    if(count($assign_to_getrowspresent) > 0){
                        $emp_data = $assign_to_getrowspresent[0]['emp_id'];
                        if($emp_data > 0){
                            $assign_to_user_getcheck_sql = "SELECT * FROM `users` WHERE `emp_id` = '$emp_data'";
                            $assign_to_user_getrowspresent = $db->getAllRecords($assign_to_user_getcheck_sql);
                            if(count($assign_to_user_getrowspresent) > 0){
                                $a[] = $assign_to_user_getrowspresent['0']['user_id'];
                            }
                        }
                    }
                }
                if(sizeof($a) > 0){
                    $f = implode(',', $a);
                    $r->convertdata->assign_to=$f;
                }
                else{
                    $r->convertdata->assign_to= 0;
                }
                
                // $r->convertdata->assign_to=$objWorksheet->getCellByColumnAndRow(26, $row)->getValue();
                
                $r->convertdata->groups=$objWorksheet->getCellByColumnAndRow(27, $row)->getValue();
                $r->convertdata->rera_no=$objWorksheet->getCellByColumnAndRow(28, $row)->getValue();
                $r->convertdata->gst_no=$objWorksheet->getCellByColumnAndRow(29, $row)->getValue();
                $r->convertdata->comments=$objWorksheet->getCellByColumnAndRow(30, $row)->getValue();
                $r->convertdata->testimonial=$objWorksheet->getCellByColumnAndRow(31, $row)->getValue();
                $r->convertdata->dnd=$objWorksheet->getCellByColumnAndRow(32, $row)->getValue();
                $r->convertdata->other_phone=$objWorksheet->getCellByColumnAndRow(33, $row)->getValue();
                $r->convertdata->pan_no=$objWorksheet->getCellByColumnAndRow(34, $row)->getValue();
                $r->convertdata->tan_no=$objWorksheet->getCellByColumnAndRow(35, $row)->getValue();
                $r->convertdata->aadhar_no=$objWorksheet->getCellByColumnAndRow(36, $row)->getValue();
                $r->convertdata->occupation=$objWorksheet->getCellByColumnAndRow(37, $row)->getValue();
                $r->convertdata->off_email=$objWorksheet->getCellByColumnAndRow(38, $row)->getValue();
                $r->convertdata->off_phone=$objWorksheet->getCellByColumnAndRow(39, $row)->getValue();
                $r->convertdata->off_phone1=$objWorksheet->getCellByColumnAndRow(40, $row)->getValue();
                $r->convertdata->off_phone2=$objWorksheet->getCellByColumnAndRow(41, $row)->getValue();
                $r->convertdata->off_fax=$objWorksheet->getCellByColumnAndRow(42, $row)->getValue();
                $r->convertdata->off_add1=$objWorksheet->getCellByColumnAndRow(43, $row)->getValue();
                $r->convertdata->off_locality=$objWorksheet->getCellByColumnAndRow(44, $row)->getValue();
                $r->convertdata->off_area=$objWorksheet->getCellByColumnAndRow(45, $row)->getValue();
                $r->convertdata->off_city=$objWorksheet->getCellByColumnAndRow(46, $row)->getValue();
                $r->convertdata->off_state=$objWorksheet->getCellByColumnAndRow(47, $row)->getValue();
                $r->convertdata->off_country=$objWorksheet->getCellByColumnAndRow(48, $row)->getValue();
                $r->convertdata->off_zip=$objWorksheet->getCellByColumnAndRow(49, $row)->getValue();
                $r->convertdata->source_channel=$objWorksheet->getCellByColumnAndRow(50, $row)->getValue();
                $r->convertdata->source_sub_channel=$objWorksheet->getCellByColumnAndRow(51, $row)->getValue();
                
                $cellValuereg_date = $objWorksheet->getCellByColumnAndRow(52, $row)->getValue();
                $excelDatereg_date = ($cellValuereg_date - 25569) * 86400;
                $formattedDatereg_date = date("Y-m-d", $excelDatereg_date);
                $r->convertdata->reg_date=$formattedDatereg_date;
                
                $r->convertdata->website=$objWorksheet->getCellByColumnAndRow(53, $row)->getValue();
                $r->convertdata->rating=$objWorksheet->getCellByColumnAndRow(54, $row)->getValue();
                $r->convertdata->opp_city=$objWorksheet->getCellByColumnAndRow(55, $row)->getValue();
                $r->convertdata->opp_area=$objWorksheet->getCellByColumnAndRow(56, $row)->getValue();
                $r->convertdata->about=$objWorksheet->getCellByColumnAndRow(57, $row)->getValue();
                $r->convertdata->invoice_name=$objWorksheet->getCellByColumnAndRow(58, $row)->getValue();
                $r->convertdata->visitor_group=$objWorksheet->getCellByColumnAndRow(59, $row)->getValue();
                $r->convertdata->visited=$objWorksheet->getCellByColumnAndRow(60, $row)->getValue();
                $r->convertdata->created_by=$objWorksheet->getCellByColumnAndRow(61, $row)->getValue();
                
                $cellValuecreated_date = $objWorksheet->getCellByColumnAndRow(62, $row)->getValue();
                $excelDatecreated_date = ($cellValuecreated_date - 25569) * 86400;
                $formattedDatecreated_date = date("Y-m-d", $excelDatecreated_date); 
                // error_log($formattedDatecreated_date, 3, "logfile2.log");
                $r->convertdata->created_date=$formattedDatecreated_date;
                
                $r->convertdata->modified_by=$objWorksheet->getCellByColumnAndRow(63, $row)->getValue();
                $r->convertdata->modified_date=$objWorksheet->getCellByColumnAndRow(64, $row)->getValue();
                // $r->convertdata->locality=$objWorksheet->getCellByColumnAndRow(66, $row)->getValue();
                // $r->convertdata->description=$objWorksheet->getCellByColumnAndRow(67, $row)->getValue();
                // $r->convertdata->latitude=$objWorksheet->getCellByColumnAndRow(68, $row)->getValue();
                // $r->convertdata->longitude=$objWorksheet->getCellByColumnAndRow(69, $row)->getValue();
                // $r->convertdata->area_name=$objWorksheet->getCellByColumnAndRow(70, $row)->getValue();
                // $r->convertdata->city_id=$objWorksheet->getCellByColumnAndRow(71, $row)->getValue();
                // $r->convertdata->state_id=$objWorksheet->getCellByColumnAndRow(72, $row)->getValue();
                // $r->convertdata->f_char=$objWorksheet->getCellByColumnAndRow(73, $row)->getValue();
                // $r->convertdata->location_name=$objWorksheet->getCellByColumnAndRow(74, $row)->getValue();
                // $r->convertdata->residential_properties_count=$objWorksheet->getCellByColumnAndRow(75, $row)->getValue();
                // $r->convertdata->retail_properties_count=$objWorksheet->getCellByColumnAndRow(76, $row)->getValue();
                // $r->convertdata->pre_leased_properties_count=$objWorksheet->getCellByColumnAndRow(77, $row)->getValue();
                // $r->convertdata->commercial_properties_count=$objWorksheet->getCellByColumnAndRow(78, $row)->getValue();
                // $r->convertdata->project_count=$objWorksheet->getCellByColumnAndRow(79, $row)->getValue();
                // $r->convertdata->team=$objWorksheet->getCellByColumnAndRow(80, $row)->getValue();
                // $r->convertdata->filenames=$objWorksheet->getCellByColumnAndRow(81, $row)->getValue();
                
                $tabble_name = "contact";
                $column_names = array('prefix', 'contact_off', 'name',  'company_name', 'add1', 'add2', 'locality_id', 'area_id', 'city', 'state', 'country', 'zip', 'comp_logo', 
                    'name_title', 'f_name', 'l_name', 'mob_no', 'mob_no1', 'email', 'alt_phone_no', 'alt_phone_no1', 'contact_pic', 'designation', 
                    'birth_date', 'teams', 'sub_teams', 'assign_to', 'groups', 'rera_no', 'gst_no', 'comments', 'testimonial', 'dnd', 'other_phone', 'pan_no', 'tan_no', 
                    'aadhar_no', 'occupation', 'off_email', 'off_phone', 'off_phone1', 'off_phone2', 'off_fax', 'off_add1', 'off_locality', 'off_area', 'off_city', 'off_state', 'off_country',
                    'off_zip', 'source_channel', 'source_sub_channel', 'reg_date', 'website', 'rating', 'opp_city', 'opp_area', 'about', 
                    'invoice_name', 'visitor_group', 'visited', 'created_by', 'created_date', 'modified_by', 'modified_date');
                    
                $multiple=array("");
                
                $result = $db->insertIntoTable($r->convertdata, $column_names, $tabble_name, $multiple);
                if ($result!=NULL)
                {
                    $success = true;
                }
            }
        }
    }
           
    if ($success)
    {
        $response["status"] = "success";
        $response["message"] = "$i contacts inserted successfully.";
        echoResponse(201, $response);
    }
    else
    {
        $response["status"] = "error";
        $response["message"] = "$j contacts are already exist !! ";
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
    $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms, i.contact_off,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id GROUP BY a.property_id ORDER BY a.modified_date DESC LIMIT 30";
    
    $total_listings = $db->getAllRecords($sql);
    echo json_encode($total_listings);
});



$app->get('/count_total_listings', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();

    $residentialdata = $db->getAllRecords("SELECT count(*) as total_list_residential FROM property where proptype='residential' and created_date >= now() - INTERVAL 3 DAY  ");
    if ($residentialdata)
    {
         $total_list_residential = $residentialdata[0]['total_list_residential'];

    }

    $commercialdata = $db->getAllRecords("SELECT count(*) as total_list_commercial FROM property where proptype='commercial' and created_date >= now() - INTERVAL 3 DAY   ");
    if ($commercialdata)
    {
         $total_list_commercial = $commercialdata[0]['total_list_commercial'];

    }
    $htmldata['total_list_residential']=$total_list_residential;

    $htmldata['total_list_commercial']=$total_list_commercial;
    $senddata[]=$htmldata;
    echo json_encode($senddata);

});


    


$app->get('/branch_listings', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,CONCAT(e.salu,' ',e.fname,' ',e.lname) as project_contact, CONCAT(e.salu,' ',e.fname,' ',e.lname) as assign_to, e.mobile_no as usermobileno from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN attachments as c on a.property_id = c.category_id and category = 'property' LEFT JOIN users as d on a.assign_to = d.user_id LEFT JOIN employee as e on d.emp_id = e.emp_id GROUP BY a.property_id ORDER by a.created_date DESC LIMIT 15";
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
    $sql = "SELECT * from property ORDER by created_date LIMIT 15";
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
    $sql = "SELECT * from property ORDER by created_date LIMIT 15";
    $total_billings = $db->getAllRecords($sql);
    echo json_encode($total_billings);
});

// employee

// $app->get('/employee_list_ctrl', function() use ($app) {
//     $sql  = "";
//     $db = new DbHandler();
//     $session = $db->getSession();
//     if ($session['username']=="Guest")
//     {
//         return;
//     }
//     $sql = "SELECT a.emp_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name, a.employee_type, CONCAT(c.salu,' ',c.fname,' ',c.lname) as manager, a.mobile_no, a.off_email, a.off_phone, DATE_FORMAT(a.doj,'%d/%m/%Y') AS doj,DATE_FORMAT(a.dob,'%d/%m/%Y') AS dob, b.username,a.status,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,(SELECT GROUP_CONCAT(h.sub_team_name SEPARATOR ',') FROM sub_teams as h where FIND_IN_SET(h.sub_team_id , a.sub_teams)) as sub_teams, DATE_FORMAT(d.referral_otp_created,'%d/%m/%Y %H:%i') AS referral_otp_created,d.referral_otp,DATE_FORMAT(d.referral_propertydata_otp_created,'%d/%m/%Y %H:%i') AS referral_propertydata_otp_created,d.referral_propertydata_otp from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN employee as c on a.manager_id = c.emp_id LEFT JOIN users as d ON a.emp_id = d.emp_id ORDER BY a.lname,a.fname";


//     $employees = $db->getAllRecords($sql);
//     echo json_encode($employees);
// });
$app->get('/selectemployees', function() use ($app) {
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

$app->get('/employee_List_Ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
   $sql="SELECT a.emp_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name, a.employee_type, CONCAT(c.salu,' ',c.fname,' ',c.lname) as manager, a.mobile_no, a.off_email, a.off_phone, DATE_FORMAT(a.doj,'%d/%m/%Y') AS doj,DATE_FORMAT(a.dob,'%d/%m/%Y') AS dob, b.username,a.status,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,(SELECT GROUP_CONCAT(h.sub_team_name SEPARATOR ',') FROM sub_teams as h where FIND_IN_SET(h.sub_team_id , a.sub_teams)) as sub_teams, DATE_FORMAT(d.referral_otp_created,'%d/%m/%Y %H:%i') AS referral_otp_created,d.referral_otp,DATE_FORMAT(d.referral_propertydata_otp_created,'%d/%m/%Y %H:%i') AS referral_propertydata_otp_created,d.referral_propertydata_otp from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN employee as c on a.manager_id = c.emp_id LEFT JOIN users as d ON a.emp_id = d.emp_id ORDER BY a.lname,a.fname ASC ";

    $resumes = $db->getAllRecords($sql);
    echo json_encode($resumes);
});

$app->get('/getdatavalues_employee/:field_name', function($field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM employee ORDER BY $field_name";
    if ($field_name == 'manager_id')
    {
        $sql = "SELECT manager_id,  CONCAT(salu,' ',fname,' ',lname) as manager FROM employee ";
        // $sql = "SELECT *,  CONCAT(c.salu,' ',c.fname,' ',c.lname) as manager FROM employee as a LEFT JOIN employee as c on a.manager_id = c.emp_id";

    }
    
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});

$app->post('/search_employees', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());

    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];

    $role = $session['role'];

    $searchsql = "select * from employee limit 0";
    
    $searchsql = "SELECT a.emp_id, CONCAT(a.salu,' ',a.fname,' ',a.lname) as name, a.employee_type, CONCAT(c.salu,' ',c.fname,' ',c.lname) as manager, a.mobile_no, a.off_email, a.off_phone, DATE_FORMAT(a.doj,'%d/%m/%Y') AS doj,DATE_FORMAT(a.dob,'%d/%m/%Y') AS dob, b.username,a.status,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,(SELECT GROUP_CONCAT(h.sub_team_name SEPARATOR ',') FROM sub_teams as h where FIND_IN_SET(h.sub_team_id , a.sub_teams)) as sub_teams, DATE_FORMAT(d.referral_otp_created,'%d/%m/%Y %H:%i') AS referral_otp_created,d.referral_otp,DATE_FORMAT(d.referral_propertydata_otp_created,'%d/%m/%Y %H:%i') AS referral_propertydata_otp_created,d.referral_propertydata_otp from employee as a LEFT JOIN users as b on a.emp_id = b.emp_id LEFT JOIN employee as c on a.manager_id = c.emp_id LEFT JOIN users as d ON a.emp_id = d.emp_id WHERE a.emp_id > 0 ";
      
    
    if (isset($r->searchdata->emp_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->emp_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.emp_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->mobile_no))
    {
        $first = "Yes";
        $new_data = $r->searchdata->mobile_no;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.mobile_no LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->off_email))
    {
        $first = "Yes";
        $new_data = $r->searchdata->off_email;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.off_email LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->teams))
    {
        $first = "Yes";
        $new_data = $r->searchdata->teams;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.teams LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->sub_teams))
    {
        $first = "Yes";
        $new_data = $r->searchdata->sub_teams;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.sub_teams LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
 
     if (isset($r->searchdata->dob))
    {
        $first = "Yes";
        $new_data = $r->searchdata->dob;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.dob LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    if (isset($r->searchdata->manager_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->manager_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.manager_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    if (isset($r->searchdata->status))
    {
        $first = "Yes";
        $new_data = $r->searchdata->status;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                if ($first=='Yes')
                {
                    $sql .= " AND (";
                    $first = "No";
                }
                else{
                    $sql .= " OR ";
                }                  
                $sql .= " a.status LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    

    $searchsql .=  $sql .  " ORDER BY a.lname,a.fname ";

    $resumes = $db->getAllRecords($searchsql);

    echo json_encode($resumes);
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
    $r->employee->year1 = $r->year;
    $fname = $r->employee->fname;
    $lname = $r->employee->lname;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->employee->created_by = $created_by;
    $r->employee->created_date = $created_date;
    $r->employee->status = 'Active';

    if (isset($r->employee->increment_date))
    {
        $increment_date = $r->employee->increment_date;
        $tincrement_date = substr($increment_date,6,4)."-".substr($increment_date,3,2)."-".substr($increment_date,0,2);
        $r->employee->increment_date = $tincrement_date;
    }

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
    $emp_user = $r->employee->emp_user;

    $iscontactExists = $db->getOneRecord("select 1 from users where username='$emp_user'");
    if($iscontactExists){
        $response["status"] = "error";
        $response["message"] = "User already Registered .. !!!";
        echoResponse(201, $response);
        return;
    }

    $isEmployeeExists = $db->getOneRecord("select 1 from employee where fname='$fname' and lname='$lname' ");
    if(!$isEmployeeExists){
        $tabble_name = "employee";
        

        $column_names = array('bo_id','teams','sub_teams','salu','fname','lname','company_name','address','emp_user','mobile_no','alt_mobile_no','off_phone','off_email','designation_id','manager_id','doj','dob','basic_salary','incentive_per','increment_per','increment_date','weekly_off','rera_no','sharing_per','leave_allowed','leave_opening','travel_allowance','year1','allow_mobile','allowed_ip','mobile_device_id','employee_type','status','created_by','created_date');
        $multiple=array("teams","sub_teams");
        $result = $db->insertIntoTable($r->employee, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Employee created successfully";
            $response["emp_id"] = $result;
            $emp_id = $result;
            $_SESSION['tmpemp_id'] = $result;

            require_once 'passwordHash.php';
            //$temp_user = explode("@",$r->employee->off_email);
            //$username = $temp_user[0];
            
            $password = passwordHash::hash('user123$');
            $query = "INSERT INTO users (username, password, emp_id, status, created_by, created_date)  VALUES('$emp_user', '$password', '$emp_id', 'Active','$created_by' ,'$created_date' )";
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

$app->post('/employee_uploads', function() use ($app) {
    session_start();
    $emp_id = $_SESSION['tmpemp_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_profile_pic'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_profile_pic'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "c_".$emp_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "employee". $ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;            

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, created_by, created_date)   VALUES('employee', '$emp_id','$file_names','Yes','Yes','Main','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->post('/employee_documents_uploads', function() use ($app) {
    session_start();
    $emp_id = $_SESSION['tmpemp_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_documents'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_documents'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "d_".$emp_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "employee_docs". $ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            if (isset($_POST['file_title_'.$count]))
            {
                $description = $_POST['file_title_'.$count];
            }
            else
            {
                $description = "";
            }

            if (isset($_POST['cert_category_'.$count]))
            {
                $file_category = $_POST['cert_category_'.$count];
            }
            else
            {
                $file_category = "";
            }
            if (isset($_POST['main_image_'.$count]))
            {
                $isdefault = $_POST['main_image_'.$count];
            }
            else
            {
                $isdefault = "false";
            }
            if (isset($_POST['share_on_web_'.$count]))
            {
                $share_on_web = $_POST['share_on_web_'.$count];
            }
            else
            {
                $share_on_web = "false";
            }

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('employee_docs', '$emp_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->get('/employee_image_update/:attachment_id/:field_name/:value', function($attachment_id,$field_name,$value) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE attachments set $field_name = '$value' where attachment_id = $attachment_id " ;  
    $result = $db->updateByQuery($sql);
    $response["status"] = "success";
    $response["message"] = "Value Changed !!!";
    echo json_encode($response);
});

$app->get('/employee_edit_ctrl/:emp_id', function($emp_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *, DATE_FORMAT(doj,'%d/%m/%Y') AS doj,DATE_FORMAT(dob,'%d/%m/%Y') AS dob,DATE_FORMAT(increment_date,'%d/%m/%Y') AS increment_date from employee where emp_id=".$emp_id;
    $employees = $db->getAllRecords($sql);
    echo json_encode($employees);
    
});

$app->get('/employee_images/:emp_id', function($emp_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'employee' and category_id = $emp_id";
    $employee_images = $db->getAllRecords($sql);
    echo json_encode($employee_images);
});

$app->get('/employee_documents/:emp_id', function($emp_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'employee_docs' and category_id = $emp_id";
    $employee_documents = $db->getAllRecords($sql);
    echo json_encode($employee_documents);
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
    $modified_date = date('Y-m-d H:i:s');
    $r->employee->modified_by = $modified_by;
    $r->employee->modified_date = $modified_date;
    $emp_id  = $r->employee->emp_id;
    $status  = $r->employee->status;
    $year  = $r->employee->year1;
    $fname = $r->employee->fname;
    $lname = $r->employee->lname;
    if (isset($r->employee->increment_date))
    {
        $increment_date = $r->employee->increment_date;
        $tincrement_date = substr($increment_date,6,4)."-".substr($increment_date,3,2)."-".substr($increment_date,0,2);
        $r->employee->increment_date = $tincrement_date;
    }
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
        $column_names = array('bo_id','teams','sub_teams','salu','fname','lname','address','company_name','mobile_no','alt_mobile_no','off_phone','off_email','manager_id','designation_id','doj','dob','basic_salary','incentive_per','increment_per','increment_date','weekly_off','rera_no','sharing_per','leave_allowed','leave_opening','travel_allowance','allow_mobile','year1','allowed_ip','mobile_device_id','status','employee_type', 'modified_by','modified_date');
        $condition = "emp_id='$emp_id'";
        $multiple=array("teams","sub_teams");
        $history = $db->historydata( $r->employee, $column_names, $tabble_name,$condition,$emp_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->employee, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Employee Updated successfully";
            $_SESSION['tmpemp_id'] = $emp_id;
            //if ($status=="InActive")
            //{
                $sql = "UPDATE users set status = '$status' where emp_id = $emp_id " ;
                $result = $db->updateByQuery($sql);
            //}
            //$response["message"] = "Employee Updated successfully".$history;
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

$app->get('/myprofile', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $emp_id = 0;
    $user_id = $session['user_id'];
    $user = $db->getOneRecord("select emp_id from users where user_id= $user_id ");
    if ($user != NULL) 
    {
        $emp_id = $user['emp_id'];
    }
    if ($emp_id>0)
    {
        $sql = "SELECT *, DATE_FORMAT(doj,'%d/%m/%Y') AS doj,DATE_FORMAT(dob,'%d/%m/%Y') AS dob from employee where emp_id = $emp_id";
        $datamyprofile = $db->getAllRecords($sql);
        echo json_encode($datamyprofile);
    }
    else{
        exit(0);
    }
});

$app->post('/myprofile_update', function() use ($app) {
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
    $modified_date = date('Y-m-d H:i:s');
    $r->employee->modified_by = $modified_by;
    $r->employee->modified_date = $modified_date;
    $emp_id  = $r->employee->emp_id;
    $fname = $r->employee->fname;
    $lname = $r->employee->lname;
    
    if (isset($r->employee->dob))
    {
        $dob = $r->employee->dob;
        $tdob = substr($dob,6,4)."-".substr($dob,3,2)."-".substr($dob,0,2);
        $r->employee->dob = $tdob;
    }
    $isEmployeeExists = $db->getOneRecord("select 1 from employee where emp_id=$emp_id");
    if($isEmployeeExists){
        $tabble_name = "employee";
        $column_names = array('salu','fname','lname','mobile_no','alt_mobile_no','off_phone','off_email','doj','dob','modified_by','modified_date');
        $condition = "emp_id='$emp_id'";
        $multiple=array("");
        $history = $db->historydata( $r->employee, $column_names, $tabble_name,$condition,$emp_id,$multiple, $modified_by, $modified_date);
        $result = $db->updateIntoTable($r->employee, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Profile Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Profile. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Profile does not exists!";
        echoResponse(201, $response);
    }
});



// $app->get('/selectemployee', function() use ($app) {
//     $sql  = "";
//     $db = new DbHandler();
//     $session = $db->getSession();
//     if ($session['username']=="Guest")
//     {
//         return;
//     }
//     $sql = "SELECT *,CONCAT(salu,' ',fname,' ',lname) as name from employee WHERE status !='InActive' and fname!='admin' ORDER BY lname,fname";
//     $employees = $db->getAllRecords($sql);
//     echo json_encode($employees);
// });
$app->get('/selectemployee', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    $u_name = $session['username'];
    if ($session['username']=="Guest")
    {
        return;
    }
    if(($session['username'] == "admin") || ($session['role']['0'] == "Human Resource new")){
         $sql = "SELECT *,CONCAT(salu,' ',fname,' ',lname) as name from employee WHERE status !='InActive' and fname!='Admin' ORDER BY lname,fname";
    }else{
         $sql = "SELECT *,CONCAT(salu,' ',fname,' ',lname) as name from employee WHERE status !='InActive' and fname!='Admin' AND emp_user LIKE '%$u_name%' ORDER BY lname,fname";
    }
    $employees = $db->getAllRecords($sql);
    echo json_encode($employees);
});


$app->get('/getemployee_details/:user_id', function($user_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    if ($user_id==0)
    {
        $user_id = $session['user_id'];
    }
    $sql = "SELECT CONCAT(a.salu,' ',a.fname,' ',a.lname) as name,a.increment_per,month(a.increment_date) as increment_month,month(a.doj) as joining_month  from employee as a LEFT JOIN users as b ON a.emp_id = b.emp_id WHERE b.user_id = $user_id";
    $employees = $db->getAllRecords($sql);
    echo json_encode($employees);
});

$app->post('/change_password_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $user_id = $session['user_id'];
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('old_password'),$r->user);
    require_once 'passwordHash.php';
    $db = new DbHandler();
    $old_password  = $r->user->old_password;
    $new_password = $r->user->new_password;
    $confirm_password = $r->user->confirm_password;
    if ($new_password != $confirm_password)
    {
        $response["status"] = "error";
        $response["message"] = "New Password and Confirm Password not matching !!!";
        echoResponse(201, $response);  
    }
    else
    {
        $user = $db->getOneRecord("select password from users where user_id='$user_id' ");
        if ($user != NULL) {
            if(passwordHash::check_password($user['password'],$old_password)){
                $r->user->password = passwordHash::hash($new_password);
                $r->user->show_password = $new_password;
                $tabble_name = "users";
                $column_names = array('password','show_password');
                $multiple = array('');
                $condition = "user_id='$user_id'";
                $result = $db->updateIntoTable($r->user, $column_names, $tabble_name, $condition, $multiple);
                if ($result != NULL) {
                    $response["status"] = "success";
                    $response["message"] = "Password Updated successfully";
                    echoResponse(200, $response);
                } else {
                    $response["status"] = "error";
                    $response["message"] = "Failed to Update Password... Please try again";
                    echoResponse(201, $response);
                }            
            }else{
                $response["status"] = "error";
                $response["message"] = "Old Password not matching ...!!";
                echoResponse(201, $response);
            }
        }else
        {
            $response["status"] = "error";
            $response["message"] = "Password not updated ...!!";
            echoResponse(201, $response);
        }
    }
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

$app->get('/getreport_fields/:module_name/:category', function($module_name,$category) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "";
    if ($module_name == "project")
    {
        $isTableExists = $db->getOneRecord("select 1 from report_fields where table_name='".$module_name."'");
        if(!$isTableExists)
        {
            $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  CONCAT(d.salu,' ',d.fname,' ',d.lname) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , d.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN users as c on a.assign_to = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN contact as e on a.developer_id = e.contact_id LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  GROUP BY a.project_id ORDER BY a.created_by DESC";
            
            $stmt = $db->getRows($sql);
            if($stmt->num_rows > 0)
            {
                while($fieldinfo=mysqli_fetch_field($stmt))
                {
                    $field_exists = $db->getOneRecord("select 1 FROM report_fields WHERE column_name = '$fieldinfo->name' and table_name = '$module_name' ");
                    if (!$field_exists) 
                    {
                        $query = "INSERT INTO report_fields (table_name , report_name, column_name, data_type, character_maximum_length, column_heading, required_field, show_on_form)   VALUES('$module_name' , 'Project List', '$fieldinfo->name', '$fieldinfo->type', '$fieldinfo->max_length', '$fieldinfo->name','Yes' ,'Yes')";
                        $result = $db->insertByQuery($query);
                    }
                }
            }
        }
    }

    if ($module_name == "property")
    {
        $isTableExists = $db->getOneRecord("select 1 from report_fields where table_name='".$module_name."'");
        if(!$isTableExists)
        {
            $sql = "SELECT *,a.exp_price, SUBSTRING(i.f_name,1,1) as f_char,CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id GROUP BY a.property_id ORDER BY a.property_id DESC";
            
            $stmt = $db->getRows($sql);
            if($stmt->num_rows > 0)
            {
                while($fieldinfo=mysqli_fetch_field($stmt))
                {
                    $field_exists = $db->getOneRecord("select 1 FROM report_fields WHERE column_name = '$fieldinfo->name' and table_name = '$module_name' ");
                    if (!$field_exists) 
                    {
                        $query = "INSERT INTO report_fields (table_name , report_name, column_name, data_type, character_maximum_length, column_heading, required_field, show_on_form)   VALUES('$module_name' , 'Property List', '$fieldinfo->name', '$fieldinfo->type', '$fieldinfo->max_length', '$fieldinfo->name','Yes' ,'Yes')";
                        $result = $db->insertByQuery($query);
                    }
                }
            }
        }
    }

    if ($module_name == "enquiry")
    {
        $isTableExists = $db->getOneRecord("select 1 from report_fields where table_name='".$module_name."'");
        if(!$isTableExists)
        {
            $sql = "SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assign_to from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id ORDER BY a.enquiry_id DESC";
            
            $stmt = $db->getRows($sql);
            if($stmt->num_rows > 0)
            {
                while($fieldinfo=mysqli_fetch_field($stmt))
                {
                    $field_exists = $db->getOneRecord("select 1 FROM report_fields WHERE column_name = '$fieldinfo->name' and table_name = '$module_name' ");
                    if (!$field_exists) 
                    {
                        $query = "INSERT INTO report_fields (table_name , report_name, column_name, data_type, character_maximum_length, column_heading, required_field, show_on_form)   VALUES('$module_name' , 'Enquiry List', '$fieldinfo->name', '$fieldinfo->type', '$fieldinfo->max_length', '$fieldinfo->name','Yes' ,'Yes')";
                        $result = $db->insertByQuery($query);
                    }
                }
            }
        }
    }



    if ($module_name == "agreement")
    {
        $isTableExists = $db->getOneRecord("select 1 from report_fields where table_name='".$module_name."'");
        if(!$isTableExists)
        {
            $sql = "SELECT *,DATE_FORMAT(a.agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(a.agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date,DATE_FORMAT(a.club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(a.deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(a.document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(a.security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(a.stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(a.transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' ";
            
            $stmt = $db->getRows($sql);
            if($stmt->num_rows > 0)
            {
                while($fieldinfo=mysqli_fetch_field($stmt))
                {
                    $field_exists = $db->getOneRecord("select 1 FROM report_fields WHERE column_name = '$fieldinfo->name' and table_name = '$module_name' ");
                    if (!$field_exists) 
                    {
                        $query = "INSERT INTO report_fields (table_name , report_name, column_name, data_type, character_maximum_length, column_heading, required_field, show_on_form)   VALUES('$module_name' , 'Property List', '$fieldinfo->name', '$fieldinfo->type', '$fieldinfo->max_length', '$fieldinfo->name','Yes' ,'Yes')";
                        $result = $db->insertByQuery($query);
                    }
                }
            }
        }
    }

    if ($module_name == "contacts")
    {
        $isTableExists = $db->getOneRecord("select 1 from report_fields where table_name='".$module_name."'");
        if(!$isTableExists)
        {
            $sql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id   ORDER BY a.created_date  ";
            
            $stmt = $db->getRows($sql);
            if($stmt->num_rows > 0)
            {
                while($fieldinfo=mysqli_fetch_field($stmt))
                {
                    $field_exists = $db->getOneRecord("select 1 FROM report_fields WHERE column_name = '$fieldinfo->name' and table_name = '$module_name' ");
                    if (!$field_exists) 
                    {
                        $query = "INSERT INTO report_fields (table_name , report_name, column_name, data_type, character_maximum_length, column_heading, required_field, show_on_form)   VALUES('$module_name' , 'Property List', '$fieldinfo->name', '$fieldinfo->type', '$fieldinfo->max_length', '$fieldinfo->name','Yes' ,'Yes')";
                        $result = $db->insertByQuery($query);
                    }
                }
            }
        }
    }

    if ($module_name == "collections")
    {
        $isTableExists = $db->getOneRecord("select 1 from report_fields where table_name='".$module_name."'");
        if(!$isTableExists)
        {
            $sql = "SELECT *, DATE_FORMAT(due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id  ";
            
            $stmt = $db->getRows($sql);
            if($stmt->num_rows > 0)
            {
                while($fieldinfo=mysqli_fetch_field($stmt))
                {
                    $field_exists = $db->getOneRecord("select 1 FROM report_fields WHERE column_name = '$fieldinfo->name' and table_name = '$module_name' ");
                    if (!$field_exists) 
                    {
                        $query = "INSERT INTO report_fields (table_name , report_name, column_name, data_type, character_maximum_length, column_heading, required_field, show_on_form)   VALUES('$module_name' , 'Property List', '$fieldinfo->name', '$fieldinfo->type', '$fieldinfo->max_length', '$fieldinfo->name','Yes' ,'Yes')";
                        $result = $db->insertByQuery($query);
                    }
                }
            }
        }
    }


    $sql = "SELECT * FROM report_fields WHERE table_name = '$module_name' and show_on_form = 'Yes' and required_field = 'Yes'  ORDER BY column_heading";
    if ($category == 'unselected')
    {
        $sql = "SELECT * FROM report_fields WHERE table_name = '$module_name' and show_on_form = 'No' and required_field = 'Yes' ORDER BY column_heading";  
    }
    $selected_fields = $db->getAllRecords($sql);
    echo json_encode($selected_fields);
});


$app->get('/update_exportdirect/:column_name/:form_id/:value', function($column_name, $form_id,$value) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE report_fields set $column_name = '$value' where report_fields_id = $form_id " ;
    
    if ($column_name=='required_field' and $value=='No')
    {
        $sql = "UPDATE report_fields set $column_name = '$value', show_on_form = 'No' where report_fields_id = $form_id " ;

    }
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
});


$app->get('/reportfieldsselect/:report_fields_id/:selected_action', function($report_fields_id, $selected_action) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    if ($selected_action=='select')
    {
        $sql = "UPDATE report_fields set show_on_form = 'Yes' where report_fields_id = $report_fields_id " ;  
        $result = $db->updateByQuery($sql);
        $response["status"] = "success";
        $response["message"] = "Done !!!";
    }
    if ($selected_action=='unselect')
    {
        $sql = "UPDATE report_fields set show_on_form = 'No' where report_fields_id = $report_fields_id " ;  
        $result = $db->updateByQuery($sql);
        $response["status"] = "success";
        $response["message"] = "Done !!!";
    }
    
});


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
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $module_name.' Details'); 
    $rowCount = 3;

    $sql = "SELECT * FROM report_fields WHERE table_name='".$module_name."' and show_on_form='Yes' ";    
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
    if ($option_value=='selected' || $option_value=='current_page' )
    {
        
        $sql = "SELECT * from $module_name where $id in($data) ORDER BY created_date DESC";
        if ($module_name =="project")
        {
            $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  CONCAT(d.salu,' ',d.fname,' ',d.lname) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , d.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN users as c on a.assign_to = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN contact as e on a.developer_id = e.contact_id LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id where $id in($data) GROUP BY a.project_id ORDER BY a.created_by DESC";
        }
        if ($module_name =="property")
        {
            $sql = "SELECT *,a.exp_price, SUBSTRING(i.f_name,1,1) as f_char,CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.property_id in($data) GROUP BY a.property_id ORDER BY a.property_id DESC";
        }
        if ($module_name =="enquiry")
        {
            $sql = "SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name,b.company_name, b.email as client_email, b.mob_no as client_mob, SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name, c.email as broker_email, c.mob_no as broker_mob,e.locality as preferred_locality,(SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id where a.enquiry_id in ($data)  ORDER BY a.enquiry_id DESC";

            //$sql="SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assign_to from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id where a.enquiry_id in($data)  ORDER BY a.enquiry_id DESC";
        }


        if ($module_name =="agreement")
        {
            $sql = "SELECT *,DATE_FORMAT(a.agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(a.agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date,DATE_FORMAT(a.club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(a.deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(a.document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(a.security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(a.stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(a.transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' WHERE a.agreement_id in($data) ORDER by a.created_date DESC "; 
        }

        if ($module_name =="contacts")
        {
            $sql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id WHERE a.contact_id in($data) ORDER by a.created_date DESC "; 
        }

        if ($module_name =="collections")
        {
            $sql = "SELECT *, DATE_FORMAT(due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id  WHERE a.payments_id in($data) ORDER by a.created_date DESC "; 
        }
        if ($module_name =="activity")
        {
            // $sql = "SELECT *, DATE_FORMAT(due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id  WHERE a.payments_id in($data) ORDER by a.created_date DESC "; 

            $sql = "SELECT a.description,a.property_id,a.project_id,a.enquiry_id,a.client_id,a.developer_id,a.broker_id,a.assign_to,a.created_by,a.activity_id,a.activity_type,a.activity_sub_type,a.activity_end, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id from activity as a LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id where a.activity_id in ($data)";
            // $sql = "SELECT a.description,a.property_id,a.project_id,a.enquiry_id,a.client_id,a.developer_id,a.broker_id,a.assign_to,a.created_by,a.activity_id,a.activity_type,a.activity_sub_type,a.activity_end,b.enquiry_for,b.enquiry_type, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id where a.activity_id in ($data)";
        }

    }

    /*if ($option_value=='current_page')
    {
        $sql = "SELECT * from project where project_id in($data) ORDER BY created_date DESC";
    }*/
    //if ($option_value=='current_page')
    //{
    //    $sql = "SELECT * from $module_name ORDER BY created_date DESC";
        
    //}
    if ($option_value=='all_records')
    {
        $sql = "SELECT * from $module_name ORDER BY created_date DESC";
        if ($module_name =="project")
        {
            $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  CONCAT(d.salu,' ',d.fname,' ',d.lname) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , d.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN users as c on a.assign_to = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN contact as e on a.developer_id = e.contact_id LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  GROUP BY a.project_id ORDER BY a.created_by DESC";
        }
        if ($module_name =="property")
        {
            $sql = "SELECT *,a.exp_price, SUBSTRING(i.f_name,1,1) as f_char,CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id  GROUP BY a.property_id ORDER BY a.property_id DESC";
        }
        if ($module_name =="enquiry")
        {
            //$sql="SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assign_to from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id  ORDER BY a.enquiry_id DESC";
            $sql = "SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name,b.company_name, b.email as client_email, b.mob_no as client_mob, SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name, c.email as broker_email, c.mob_no as broker_mob,e.locality as preferred_locality,(SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id where a.enquiry_id in ($data)  ORDER BY a.enquiry_id DESC";
        }

        if ($module_name =="agreement")
        {
            $sql = "SELECT *,DATE_FORMAT(a.agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(a.agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date,DATE_FORMAT(a.club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(a.deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(a.document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(a.security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(a.stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(a.transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' ORDER by a.created_date DESC "; 
        }
        if ($module_name =="contacts")
        {
            $sql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id ORDER by a.created_date DESC "; 
        }
        if ($module_name =="collections")
        {
            $sql = "SELECT *, DATE_FORMAT(due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id  ORDER by a.created_date DESC "; 
        }
        if ($module_name =="activity")
        {
            // $sql = "SELECT *, DATE_FORMAT(due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id  ORDER by a.created_date DESC ";
            $sql = "SELECT a.description,a.property_id,a.project_id,a.enquiry_id,a.client_id,a.developer_id,a.broker_id,a.assign_to,a.created_by,a.activity_id,a.activity_type,a.activity_sub_type,a.activity_end, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id from activity as a LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id ";
            
            // $sql = "SELECT a.description,a.property_id,a.project_id,a.enquiry_id,a.client_id,a.developer_id,a.broker_id,a.assign_to,a.created_by,a.activity_id,a.activity_type,a.activity_sub_type,a.activity_end,b.enquiry_for,b.enquiry_type, DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date,a.status as status,a.activity_id as activity_id,(SELECT filenames FROM attachments WHERE g.emp_id = category_id and category='employee' LIMIT 1) as filenames from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id";
        }
    }
    $stmt = $db->getRows($sql);
    /*echo $sql;
    echo $module_name;
    echo "done";
    return;*/
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            
            $char = "A";
            $sql1 = "SELECT * FROM report_fields WHERE table_name='".$module_name."' and show_on_form='Yes'";    
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

$app->get('/preleased/:module_name/:id/:data', function($module_name,$id,$data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();

    $sql = "SELECT *,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end, DATE_FORMAT(a.lease_start,'%Y') as lease_start_year ,DATE_FORMAT(a.lease_start,'%d') as lease_start_day, DATE_FORMAT(a.lease_start,'%m') as lease_start_month,DATE_FORMAT(a.lease_end,'%Y') as lease_end_year ,DATE_FORMAT(a.lease_end,'%d') as lease_end_day, DATE_FORMAT(a.lease_end,'%m') as lease_end_month, MONTHNAME(a.lease_start) as lease_start_monthname,MONTHNAME(a.lease_end) as lease_end_monthname,b.locality as locality, c.area_name as area_name,d.city from $module_name as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c on a.area_id = c.area_id LEFT JOIN city as d on c.city_id = d.city_id where a.$id in($data) ";  

    $propertydata = $db->getAllRecords($sql);
    $property_id = 0;
    $rent_esc = 1;
    $lease_start = "0000-00-00";
    $lease_end = "0000-00-00";
    
    $htmlstring = '';
    if ($propertydata)
    {
        $property_id = $propertydata[0]['property_id'];
        //$rent_esc = $propertydata[0]['rent_est'];
        $lease_start = $propertydata[0]['lease_start'];
        $lease_start_day = $propertydata[0]['lease_start_day'];
        $lease_start_month = $propertydata[0]['lease_start_month'];
        $lease_start_monthname = $propertydata[0]['lease_start_monthname'];
        $lease_start_year = (int)$propertydata[0]['lease_start_year'];
        $lease_end = $propertydata[0]['lease_end'];
        $lease_end_day = $propertydata[0]['lease_end_day'];
        $lease_end_month = $propertydata[0]['lease_end_month'];
        $lease_end_monthname = $propertydata[0]['lease_end_monthname'];
        $lease_end_year = (int)$propertydata[0]['lease_end_year'];
        $ag_tenure = $propertydata[0]['ag_tenure'];
        
        $address = $propertydata[0]['wing'].', ';
        //.', '.$propertydata[0]['unit'].', '.$propertydata[0]['floor'].', '.$propertydata[0]['road_no'].', '.$propertydata[0]['building_name'].', '.$propertydata[0]['landmark'].', '.$propertydata[0]['locality'].', '.$propertydata[0]['area_name'].', '.$propertydata[0]['city'];
        if ($propertydata[0]['unit'])
        {
            $address .= $propertydata[0]['unit'].', ';
        }
        if ($propertydata[0]['floor'])
        {
            $address .= $propertydata[0]['floor'].', ';
        }
        if ($propertydata[0]['road_no'])
        {
            $address .= $propertydata[0]['road_no'].', ';
        }
        if ($propertydata[0]['building_name'])
        {
            $address .= $propertydata[0]['building_name'].', ';
        }
        if ($propertydata[0]['landmark'])
        {
            $address .= $propertydata[0]['landmark'].', ';
        }
        if ($propertydata[0]['locality'])
        {
            $address .= $propertydata[0]['locality'].', ';
        }
        if ($propertydata[0]['area_name'])
        {
            $address .= $propertydata[0]['area_name'].', ';
        }
        if ($propertydata[0]['city'])
        {
            $address .= $propertydata[0]['city'];
        }

        $carp_area = $propertydata[0]['carp_area'];
        $tenant_name = $propertydata[0]['tenant_name'];
        $occ_details = $propertydata[0]['occ_details'];
        $lock_per = $propertydata[0]['lock_per'];
        $rent_esc = (int) $propertydata[0]['rent_esc'];
        $pre_leased_rent = (int)$propertydata[0]['pre_leased_rent'];
        $security_depo = $propertydata[0]['security_depo'];
        $monthle_rent = (int)$propertydata[0]['monthle_rent'];
        $rented_area = $propertydata[0]['rented_area'];
        $prop_tax = (int)$propertydata[0]['prop_tax'];
        $cam_charges = (int)$propertydata[0]['cam_charges'];
        $notice_period = $propertydata[0]['notice_period'];
        $stamp_duty = (int)$propertydata[0]['stamp_duty'];
        $exp_price = $db->ConvertAmount($propertydata[0]['exp_price'],$propertydata[0]['exp_price_para']);
        $exp_price_per_year = $exp_price / 12;
        $total_years = $ag_tenure / 12;
        if ($rent_esc>0)
        {
            $total_years = $total_years / $rent_esc;
            $ttotal_years = (int)$total_years;
            if ($total_years > $ttotal_years)
            {
                $total_years = $total_years + 1;
            }
        }
                
                /*add1:results[0].add1,
                add2:results[0].add2,
                ag_tenure:results[0].ag_tenure,
                ageofprop:results[0].ageofprop,
                agree_month:results[0].agree_month,
                amenities_avl:results[0].amenities_avl,
                area:results[0].area,
                area_id:results[0].area_id,
                area_name:results[0].area_name,
                area_para:results[0].area_para,
                asset_id:results[0].asset_id,
                assign_to:results[0].assign_to,
                availablefor:results[0].availablefor,
                balconies:results[0].balconies,
                bathrooms:results[0].bathrooms,
                bedrooms:results[0].bedrooms,
                broke_involved:results[0].broke_involved,
                building_name:results[0].building_name,
                building_plot:results[0].building_plot,
                cabins:results[0].cabins,
                cam_charges:results[0].cam_charges,
                campaign:results[0].campaign,
                car_area:results[0].car_area,
                car_park:results[0].car_park,
                carp_area:results[0].carp_area,
                carp_area2:results[0].carp_area2,
                carp_area_para:results[0].carp_area_para,
                carp_area_upside:results[0].carp_area_upside,
                cc:results[0].cc,
                city:results[0].city,
                city_id:results[0].city_id,
                completion_date:results[0].completion_date,
                con_status:results[0].con_status,
                conferences:results[0].conferences,
                config:results[0].config,
                country:results[0].country,
                created_by:results[0].created_by,
                created_date:results[0].created_date,
                cubicals:results[0].cubicals,
                deal_done:results[0].add1,
                dep_months:results[0].dep_months,
                deposite_month:results[0].deposite_month,
                description:results[0].description,
                dev_owner:results[0].dev_owner,
                dev_owner_name:results[0].dev_owner_name,
                dev_owner_id:results[0].dev_owner_id,
                distfrm_dairport:results[0].distfrm_dairport,
                distfrm_highway:results[0].distfrm_highway,
                distfrm_market:results[0].distfrm_market,
                distfrm_school:results[0].distfrm_school,
                distfrm_station:results[0].distfrm_station,
                door_fdir:results[0].door_fdir,
                dry_room:results[0].dry_room,
                efficiency:results[0].efficiency,
                email_saletrainee:results[0].email_saletrainee,
                escalation:results[0].escalation,
                escalation_lease:results[0].escalation_lease,
                exlocation:results[0].exlocation,
                exp_price:(results[0].exp_price),
                exp_price2:(results[0].exp_price2),
                exp_price2_para:results[0].exp_price2_para,
                exp_price_para:results[0].exp_price_para,
                exp_price_upside:results[0].exp_price_upside,
                exp_rent:results[0].exp_rent,
                exp_rent2:results[0].exp_rent2,
                exp_rent2_para:results[0].exp_rent2_para,
                exp_rent_para:results[0].exp_rent_para,
                exprice_para:results[0].exprice_para,
                external_comment:results[0].external_comment,
                floor:results[0].floor,
                frontage:results[0].frontage,
                fur_charges:results[0].fur_charges,
                furniture:results[0].furniture,
                groups:results[0].groups,
                garden_facing:results[0].garden_facing,
                height:results[0].height,
                internal_comment:results[0].internal_comment,
                internalroad:results[0].internalroad,
                keywith:results[0].keywith,
                kitchen:results[0].kitchen,
                landmark:results[0].landmark,
                latitude:results[0].latitude,
                lease_end:results[0].lease_end,
                lease_lock:results[0].lease_lock,
                lease_start:results[0].lease_start,
                lease_tot_area:results[0].lease_tot_area,
                lift:results[0].lift,
                loading:results[0].loading,
                locality:results[0].locality,
                locality_id:results[0].locality_id,
                lock_per:results[0].lock_per,
                longitude:results[0].longitude,
                main_charges:(results[0].main_charges),
                main_charges_para:results[0].main_charges_para,
                mainroad:results[0].mainroad,
                modified_by:results[0].modified_by,
                modified_date:results[0].modified_date,
                monthle_rent:results[0].monthle_rent,
                mpm_cam:results[0].mpm_cam,
                mpm_tot_tax:results[0].mpm_tot_tax,
                mpm_unit:results[0].mpm_unit,
                mpm_unit_para:results[0].mpm_unit_para,
                multi_size:results[0].multi_size,
                numof_floor:results[0].numof_floor,
                occ_details:results[0].occ_details,
                occu_certi:results[0].occu_certi,
                washrooms:results[0].washrooms,
                oth_charges:results[0].oth_charges,
                other_tenant:results[0].other_tenant,
                owner_email:results[0].owner_email,
                owner_mobile:results[0].owner_mobile,
                pack_price:(results[0].pack_price),
                pack_price_comments:results[0].pack_price_comments,
                pack_price_para:results[0].pack_price_para,
                package_para:results[0].package_para,
                /*park_charge:results[0].park_charge,*/
                /*park_charge:(results[0].park_charge),
                park_charge_para:results[0].park_charge_para,
                parking:results[0].parking,
                pooja_room:results[0].pooja_room,
                possession_date:results[0].possession_date,
                powersup:results[0].powersup,
                pre_leased:results[0].pre_leased,
                pre_leased_rent:results[0].pre_leased_rent,
                price_carpet:results[0].price_carpet,
                price_unit:(results[0].price_unit),
                price_unit_carpet:(results[0].price_unit_carpet),
                price_unit_carpet_para:results[0].price_unit_carpet_para,
                price_unit_para:results[0].price_unit_para,
                priority:results[0].priority,
                pro_inspect:results[0].pro_inspect,
                pro_sale_para:results[0].pro_sale_para,
                pro_specification:results[0].pro_specification,
                proj_status:results[0].proj_status,
                project_id:results[0].project_id,
                project_name:results[0].project_name,
                prop_tax:results[0].prop_tax,
                property_code:results[0].property_code,
                property_for:results[0].property_for,
                property_id:results[0].property_id,
                propfrom:results[0].propfrom,
                propsubtype:results[0].propsubtype,
                proptype:results[0].proptype,
                psale_area:results[0].psale_area,
                published:results[0].published,
                reg_date:results[0].reg_date,
                rent_esc:results[0].rent_esc,
                rent_per_sqft:results[0].rent_per_sqft,
                rent_per_sqft_para:results[0].rent_per_sqft_para,
                rented_area:results[0].rented_area,
                rera_num:results[0].rera_num,
                review:results[0].review,
                road_no:results[0].road_no,
                roi:results[0].roi,
                rece:results[0].rece,
                sale_area:results[0].sale_area,
                sale_area2:results[0].sale_area2,
                sale_area_upside:results[0].sale_area_upside,
                seaters:results[0].seaters,
                sec_dep:results[0].sec_dep,
                security_depo:(results[0].security_depo),
                security_depo_para:results[0].security_depo_para,
                security_para:results[0].security_para,
                servent_room:results[0].servent_room,
                sms_saletrainee:results[0].sms_saletrainee,
                soc_reg:results[0].soc_reg,
                source_channel:results[0].source_channel,
                state:results[0].state,
                state_id:results[0].state_id,
                store_room:results[0].store_room,
                study_room:results[0].study_room,
                subsource_channel:results[0].subsource_channel,
                suitable_for:results[0].suitable_for,
                suitablefor:results[0].suitablefor,
                teams:results[0].teams,
                tenant:results[0].tenant,
                tenant1:results[0].tenant1,
                tenant_name:results[0].tenant_name,
                tenure_year:results[0].tenure_year,
                terrace:results[0].terrace,
                terrace_para:results[0].terrace_para,
                tranf_charge:results[0].tranf_charge,
                unit:results[0].unit,
                usd_area:results[0].usd_area,
                vastu_comp:results[0].vastu_comp,
                watersup:results[0].watersup,
                wing:results[0].wing,
                wings:results[0].wings,
                workstation:results[0].workstation,
                meeting_room:results[0].meeting_room,
                server_room:results[0].server_room,
                youtube_link:results[0].youtube_link,
                zip:results[0].zip*/


    }
    $dummy = "";
    $htmlstring .= '<div style="margin-left:10%;margin-right:10%;width:80%">
                        <table style="border:1px solid #000000;width:100%;table-layout:fixed;" id = "preleased" class="preleased">
                                <tr>
                                   <td colspan="2" style="background-color:#a6ccfd;border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:center;font-size:18px;">Property Details</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Address</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$address.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Total Offered Area (Sqft) Saleable</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$rented_area .'</td>
                                </tr>
                                <tr>
                                   <td colspan="2" style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;background-color:#a6ccfd;">Lease Details</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Tenant Name</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$tenant_name.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Tenant Details</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$occ_details.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Leased Total Area (Sqft)</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$rented_area .'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Leave and License Start Date</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$lease_start.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Leave and License End Date</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$lease_end.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Rent Start Date</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$lease_start.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Lock in Period</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$lock_per.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Gross Rent (INR / Per Month)</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$dummy.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Escalation after every '.$rent_esc.' year</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$pre_leased_rent.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Interest Free Refundable Security Deposit (In INR)</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$security_depo.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Bank Rate of Interest</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$dummy.'</td>
                                </tr>
                                <tr>
                                <td colspan="2">
                                    <table style="width:100%;border:1px solid #000000;">
                                        <tr style="background-color:#a6ccfd;">
                                        <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:14px;">Terms '.substr($lease_start_monthname,0,3).' '.$lease_start_year.'</td>';
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:14px;">'.substr($lease_start_monthname,0,3).' '.$lease_start_year.' to '.substr($lease_end_monthname,0,3).' '.($lease_start_year+$rent_esc);

                                            $lease_start_year  = ($lease_start_year+$rent_esc); 
                                            $htmlstring .='</td>';
                                        }
                                        $htmlstring .='</tr>';
                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Carpet Area (Sq. Ft.)</td>';
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">';

                                            if ($n==1)
                                            {
                                                $htmlstring .=$rented_area;
                                            }
                                            else{
                                                $htmlstring .=$dummy;
                                            }
                                            $htmlstring .='</td>';
                                        }

                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Tenure (Months)</td>';
                                        
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">';

                                            if ($n==1)
                                            {
                                                $htmlstring .=$ag_tenure;
                                            }
                                            else{
                                                $htmlstring .=$dummy;
                                            }
                                            $htmlstring .='</td>';
                                        }
                                        $htmlstring .='</tr>';  
                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Notice Period</td>';

                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">';
                                            if ($n==1)
                                            {
                                                $htmlstring .=$notice_period;
                                            }
                                            else{
                                                $htmlstring .=$dummy;
                                            }
                                            $htmlstring .='</td>';
                                        }
                                        $htmlstring .='</tr>';
                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Per Month Rent</td>';
                                        $total_rent_per_year = $monthle_rent;
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">'.$total_rent_per_year.'</td>';

                                            $total_rent_per_year = $total_rent_per_year +round((($total_rent_per_year)*($pre_leased_rent/100)),0);
                                        }
                                        $htmlstring .='</tr>';   

                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Security Deposit</td>';
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">';
                                            if ($n==1)
                                            {
                                                $htmlstring .=$security_depo;
                                            }
                                            else{
                                                $htmlstring .=$dummy;
                                            }
                                            $htmlstring .='</td>';
                                        }
                                        $htmlstring .='</tr>';

                                        $htmlstring .='<tr style="background-color:#a6ccfd;">
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;background-color:#a6ccfd;" colspan="'.($total_years+1).'">Inflow</td>
                                        </tr>';
                                       
                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Rent: Per Annum (Rs)</td>';
                                        $total_rent_per_year = $monthle_rent;
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">'.($total_rent_per_year*12).'</td>';
                                            $total_rent_per_year = $total_rent_per_year + round((($total_rent_per_year)*($pre_leased_rent/100)),0);
                                        }
                                        $htmlstring .='</tr>';
                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Less:Per Annum CAM</td>';
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">'.round(((int)$cam_charges*12),0).'</td>';
                                        }
                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Less:Property Tax</td>';
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">'.round(((int)$prop_tax*12),0).'</td>';
                                        }
                                        $htmlstring .='</tr>';
                                        $total_rent_per_year = $monthle_rent;
                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Total Inflow Per Annum (Rental Income) (Rs)</td>';
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $total_inflow = round(((int)$total_rent_per_year*12),0)-round(((int)$cam_charges*12),0)-round(((int)$prop_tax*12),0);
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">'.$total_inflow.'</td>';
                                            $total_rent_per_year = $total_rent_per_year + round((($total_rent_per_year)*($pre_leased_rent/100)),0);
                                        }
                                        $htmlstring .='</tr>';
                                        $htmlstring .='<tr style="background-color:#a6ccfd;;">
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;background-color:#a6ccfd;" colspan="'.($total_years+1).'">Acquisition Cost</td></tr>';
                                        
                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Total Acquisition Cost</td>';
                                        
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">';
                                            if ($n==1)
                                            {
                                                $htmlstring .=$exp_price;
                                            }
                                            else{
                                                $htmlstring .=$dummy;
                                            }
                                            $htmlstring .='</td>';
                                        }
                                        $htmlstring .='</tr>';

                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Add: Stamp Duty and Registration</td>';
                                        
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">';
                                            if ($n==1)
                                            {
                                                $htmlstring .=$stamp_duty;
                                            }
                                            else{
                                                $htmlstring .=$dummy;
                                            }
                                            $htmlstring .='</td>';

                                        }
                                        $htmlstring .='</tr>';
                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Less: Security Deposit</td>';
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">';
                                            if ($n==1)
                                            {
                                                $htmlstring .=$security_depo;
                                            }
                                            else{
                                                $htmlstring .=$dummy;
                                            }
                                            $htmlstring .='</td>';
                                        }
                                        $htmlstring .='</tr>';
                                        $htmlstring .='<tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Total OutFlow (Rs.)</td>';
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:right;">';
                                            if ($n==1)
                                            {
                                                $htmlstring .=($exp_price+$stamp_duty - $security_depo);
                                            }
                                            else{
                                                $htmlstring .=$dummy;
                                            }
                                            $htmlstring .='</td>';
                                        }
                                        $htmlstring .='</tr>';
                                        $htmlstring .='<tr style="background-color:#a6ccfd;;">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">ROI (%)</td>';
                                        $total_rent_per_year = $monthle_rent;
                                        $total_roi = 0;
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            //$roi = round((((($total_rent_per_year*12)-$cam_charges-$prop_tax))/($exp_price+$stamp_duty - $security_depo)*100),2);

                                            $roi = round((((($total_rent_per_year*12)-($cam_charges*12)-($prop_tax*12)))/($exp_price+$stamp_duty - $security_depo)*100),2);

                                            $total_roi = $total_roi + $roi;
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;text-align:right;">'.$roi.'</td>';
                                            $total_rent_per_year = $total_rent_per_year + (($total_rent_per_year)*($pre_leased_rent/100));

                                        }
                                        $htmlstring .='</tr>';
                                        $weighted_avg = round(($total_roi / $total_years),2);
                                        $htmlstring .='<tr style="background-color:#a6ccfd;;">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">Weighted Average Yield Over Tenure</td>';
                                        for ($n=1; $n<=$total_years;++$n)
                                        {
                                            $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;text-align:right;">';
                                            if ($n==1)
                                            {
                                                $htmlstring .=$weighted_avg;
                                            }
                                            else{
                                                $htmlstring .=$dummy;
                                            }
                                            $htmlstring .='</td>';
                                        }
                                        $htmlstring .='</tr>
                                    </table>
                                </td>
                                </tr>
                            </table>
                    </div>';

    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

//SAMPLE
$app->get('/preleased_sample/:module_name/:id/:data', function($module_name,$id,$data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();

    $sql = "SELECT *,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end, DATE_FORMAT(a.lease_start,'%Y') as lease_start_year ,DATE_FORMAT(a.lease_start,'%d') as lease_start_day, DATE_FORMAT(a.lease_start,'%m') as lease_start_month,DATE_FORMAT(a.lease_end,'%Y') as lease_end_year ,DATE_FORMAT(a.lease_end,'%d') as lease_end_day, DATE_FORMAT(a.lease_end,'%m') as lease_end_month, MONTHNAME(a.lease_start) as lease_start_monthname,MONTHNAME(a.lease_end) as lease_end_monthname,b.locality as locality, c.area_name as area_name,d.city from $module_name as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c on a.area_id = c.area_id LEFT JOIN city as d on c.city_id = d.city_id where a.$id in($data) ";  

    $propertydata = $db->getAllRecords($sql);
    $property_id = 0;
    $rent_esc = 1;
    $lease_start = "0000-00-00";
    $lease_end = "0000-00-00";
    
    $htmlstring = '';
    if ($propertydata)
    {
        $property_id = $propertydata[0]['property_id'];
        $rent_esc = $propertydata[0]['rent_est'];
        $lease_start = $propertydata[0]['lease_start'];
        $lease_start_day = $propertydata[0]['lease_start_day'];
        $lease_start_month = $propertydata[0]['lease_start_month'];
        $lease_start_monthname = $propertydata[0]['lease_start_monthname'];
        $lease_start_year = $propertydata[0]['lease_start_year'];
        $lease_end = $propertydata[0]['lease_end'];
        $lease_end_day = $propertydata[0]['lease_end_day'];
        $lease_end_month = $propertydata[0]['lease_end_month'];
        $lease_end_monthname = $propertydata[0]['lease_end_monthname'];
        $lease_end_year = $propertydata[0]['lease_end_year'];
        $ag_tenure = $propertydata[0]['ag_tenure'];
        $address = $propertydata[0]['wing'].', ';
        //.', '.$propertydata[0]['unit'].', '.$propertydata[0]['floor'].', '.$propertydata[0]['road_no'].', '.$propertydata[0]['building_name'].', '.$propertydata[0]['landmark'].', '.$propertydata[0]['locality'].', '.$propertydata[0]['area_name'].', '.$propertydata[0]['city'];
        if ($propertydata[0]['unit'])
        {
            $address .= $propertydata[0]['unit'].', ';
        }
        if ($propertydata[0]['floor'])
        {
            $address .= $propertydata[0]['floor'].', ';
        }
        if ($propertydata[0]['road_no'])
        {
            $address .= $propertydata[0]['road_no'].', ';
        }
        if ($propertydata[0]['building_name'])
        {
            $address .= $propertydata[0]['building_name'].', ';
        }
        if ($propertydata[0]['landmark'])
        {
            $address .= $propertydata[0]['landmark'].', ';
        }
        if ($propertydata[0]['locality'])
        {
            $address .= $propertydata[0]['locality'].', ';
        }
        if ($propertydata[0]['area_name'])
        {
            $address .= $propertydata[0]['area_name'].', ';
        }
        if ($propertydata[0]['city'])
        {
            $address .= $propertydata[0]['city'];
        }

        $carp_area = $propertydata[0]['carp_area'];
        $tenant_name = $propertydata[0]['tenant_name'];
        $occ_details = $propertydata[0]['occ_details'];
        $lock_per = $propertydata[0]['lock_per'];
        $rent_esc = $propertydata[0]['rent_esc'];
        $pre_leased_rent = $propertydata[0]['pre_leased_rent'];
        $security_depo = $propertydata[0]['security_depo'];
        $monthle_rent = $propertydata[0]['monthle_rent'];
        $rented_area = $propertydata[0]['rented_area'];
        $cam_charges = $propertydata[0]['cam_charges'];
        $notice_period = $propertydata[0]['notice_period'];
        $stamp_duty = $propertydata[0]['stamp_duty'];
                
                /*add1:results[0].add1,
                add2:results[0].add2,
                ag_tenure:results[0].ag_tenure,
                ageofprop:results[0].ageofprop,
                agree_month:results[0].agree_month,
                amenities_avl:results[0].amenities_avl,
                area:results[0].area,
                area_id:results[0].area_id,
                area_name:results[0].area_name,
                area_para:results[0].area_para,
                asset_id:results[0].asset_id,
                assign_to:results[0].assign_to,
                availablefor:results[0].availablefor,
                balconies:results[0].balconies,
                bathrooms:results[0].bathrooms,
                bedrooms:results[0].bedrooms,
                broke_involved:results[0].broke_involved,
                building_name:results[0].building_name,
                building_plot:results[0].building_plot,
                cabins:results[0].cabins,
                cam_charges:results[0].cam_charges,
                campaign:results[0].campaign,
                car_area:results[0].car_area,
                car_park:results[0].car_park,
                carp_area:results[0].carp_area,
                carp_area2:results[0].carp_area2,
                carp_area_para:results[0].carp_area_para,
                carp_area_upside:results[0].carp_area_upside,
                cc:results[0].cc,
                city:results[0].city,
                city_id:results[0].city_id,
                completion_date:results[0].completion_date,
                con_status:results[0].con_status,
                conferences:results[0].conferences,
                config:results[0].config,
                country:results[0].country,
                created_by:results[0].created_by,
                created_date:results[0].created_date,
                cubicals:results[0].cubicals,
                deal_done:results[0].add1,
                dep_months:results[0].dep_months,
                deposite_month:results[0].deposite_month,
                description:results[0].description,
                dev_owner:results[0].dev_owner,
                dev_owner_name:results[0].dev_owner_name,
                dev_owner_id:results[0].dev_owner_id,
                distfrm_dairport:results[0].distfrm_dairport,
                distfrm_highway:results[0].distfrm_highway,
                distfrm_market:results[0].distfrm_market,
                distfrm_school:results[0].distfrm_school,
                distfrm_station:results[0].distfrm_station,
                door_fdir:results[0].door_fdir,
                dry_room:results[0].dry_room,
                efficiency:results[0].efficiency,
                email_saletrainee:results[0].email_saletrainee,
                escalation:results[0].escalation,
                escalation_lease:results[0].escalation_lease,
                exlocation:results[0].exlocation,
                exp_price:(results[0].exp_price),
                exp_price2:(results[0].exp_price2),
                exp_price2_para:results[0].exp_price2_para,
                exp_price_para:results[0].exp_price_para,
                exp_price_upside:results[0].exp_price_upside,
                exp_rent:results[0].exp_rent,
                exp_rent2:results[0].exp_rent2,
                exp_rent2_para:results[0].exp_rent2_para,
                exp_rent_para:results[0].exp_rent_para,
                exprice_para:results[0].exprice_para,
                external_comment:results[0].external_comment,
                floor:results[0].floor,
                frontage:results[0].frontage,
                fur_charges:results[0].fur_charges,
                furniture:results[0].furniture,
                groups:results[0].groups,
                garden_facing:results[0].garden_facing,
                height:results[0].height,
                internal_comment:results[0].internal_comment,
                internalroad:results[0].internalroad,
                keywith:results[0].keywith,
                kitchen:results[0].kitchen,
                landmark:results[0].landmark,
                latitude:results[0].latitude,
                lease_end:results[0].lease_end,
                lease_lock:results[0].lease_lock,
                lease_start:results[0].lease_start,
                lease_tot_area:results[0].lease_tot_area,
                lift:results[0].lift,
                loading:results[0].loading,
                locality:results[0].locality,
                locality_id:results[0].locality_id,
                lock_per:results[0].lock_per,
                longitude:results[0].longitude,
                main_charges:(results[0].main_charges),
                main_charges_para:results[0].main_charges_para,
                mainroad:results[0].mainroad,
                modified_by:results[0].modified_by,
                modified_date:results[0].modified_date,
                monthle_rent:results[0].monthle_rent,
                mpm_cam:results[0].mpm_cam,
                mpm_tot_tax:results[0].mpm_tot_tax,
                mpm_unit:results[0].mpm_unit,
                mpm_unit_para:results[0].mpm_unit_para,
                multi_size:results[0].multi_size,
                numof_floor:results[0].numof_floor,
                occ_details:results[0].occ_details,
                occu_certi:results[0].occu_certi,
                washrooms:results[0].washrooms,
                oth_charges:results[0].oth_charges,
                other_tenant:results[0].other_tenant,
                owner_email:results[0].owner_email,
                owner_mobile:results[0].owner_mobile,
                pack_price:(results[0].pack_price),
                pack_price_comments:results[0].pack_price_comments,
                pack_price_para:results[0].pack_price_para,
                package_para:results[0].package_para,
                /*park_charge:results[0].park_charge,*/
                /*park_charge:(results[0].park_charge),
                park_charge_para:results[0].park_charge_para,
                parking:results[0].parking,
                pooja_room:results[0].pooja_room,
                possession_date:results[0].possession_date,
                powersup:results[0].powersup,
                pre_leased:results[0].pre_leased,
                pre_leased_rent:results[0].pre_leased_rent,
                price_carpet:results[0].price_carpet,
                price_unit:(results[0].price_unit),
                price_unit_carpet:(results[0].price_unit_carpet),
                price_unit_carpet_para:results[0].price_unit_carpet_para,
                price_unit_para:results[0].price_unit_para,
                priority:results[0].priority,
                pro_inspect:results[0].pro_inspect,
                pro_sale_para:results[0].pro_sale_para,
                pro_specification:results[0].pro_specification,
                proj_status:results[0].proj_status,
                project_id:results[0].project_id,
                project_name:results[0].project_name,
                prop_tax:results[0].prop_tax,
                property_code:results[0].property_code,
                property_for:results[0].property_for,
                property_id:results[0].property_id,
                propfrom:results[0].propfrom,
                propsubtype:results[0].propsubtype,
                proptype:results[0].proptype,
                psale_area:results[0].psale_area,
                published:results[0].published,
                reg_date:results[0].reg_date,
                rent_esc:results[0].rent_esc,
                rent_per_sqft:results[0].rent_per_sqft,
                rent_per_sqft_para:results[0].rent_per_sqft_para,
                rented_area:results[0].rented_area,
                rera_num:results[0].rera_num,
                review:results[0].review,
                road_no:results[0].road_no,
                roi:results[0].roi,
                rece:results[0].rece,
                sale_area:results[0].sale_area,
                sale_area2:results[0].sale_area2,
                sale_area_upside:results[0].sale_area_upside,
                seaters:results[0].seaters,
                sec_dep:results[0].sec_dep,
                security_depo:(results[0].security_depo),
                security_depo_para:results[0].security_depo_para,
                security_para:results[0].security_para,
                servent_room:results[0].servent_room,
                sms_saletrainee:results[0].sms_saletrainee,
                soc_reg:results[0].soc_reg,
                source_channel:results[0].source_channel,
                state:results[0].state,
                state_id:results[0].state_id,
                store_room:results[0].store_room,
                study_room:results[0].study_room,
                subsource_channel:results[0].subsource_channel,
                suitable_for:results[0].suitable_for,
                suitablefor:results[0].suitablefor,
                teams:results[0].teams,
                tenant:results[0].tenant,
                tenant1:results[0].tenant1,
                tenant_name:results[0].tenant_name,
                tenure_year:results[0].tenure_year,
                terrace:results[0].terrace,
                terrace_para:results[0].terrace_para,
                tranf_charge:results[0].tranf_charge,
                unit:results[0].unit,
                usd_area:results[0].usd_area,
                vastu_comp:results[0].vastu_comp,
                watersup:results[0].watersup,
                wing:results[0].wing,
                wings:results[0].wings,
                workstation:results[0].workstation,
                meeting_room:results[0].meeting_room,
                server_room:results[0].server_room,
                youtube_link:results[0].youtube_link,
                zip:results[0].zip*/


    }
    $dummy = 0;
    $htmlstring .= '<div style="margin-left:10%;margin-right:10%;width:80%">
                        <table style="border:1px solid #000000;width:100%;table-layout:fixed;" id = "preleased" class="preleased">
                                <tr>
                                   <td colspan="2" style="background-color:#a6ccfd;border-right:1px solid #000000;border-bottom:1px solid #000000;text-align:center;font-size:18px;">Property Details</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Address</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$address.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Total Offered Area (Sqft) Saleable</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$rented_area .'</td>
                                </tr>
                                <tr>
                                   <td colspan="2" style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;background-color:#a6ccfd;">Lease Details</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Tenant Name</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$tenant_name.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Tenant Details</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$occ_details.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Leased Total Area (Sqft)</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$rented_area .'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Leave and License Start Date</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$lease_start.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Leave and License End Date</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$lease_end.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Rent Start Date</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$lease_start.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Lock in Period</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$lock_per.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Gross Rent (INR / Per Month)</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$dummy.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Escalation after every '.$rent_esc.' year</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$pre_leased_rent.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Interest Free Refundable Security Deposit (In INR)</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$security_depo.'</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">Bank Rate of Interest</td>
                                    <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;max-width:500px; min-width:500px;">'.$dummy.'</td>
                                </tr>
                                <tr>
                                <td colspan="2">
                                    <table style="width:100%;border:1px solid #000000;">
                                        <tr style="background-color:#a6ccfd;">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:14px;">Terms Jan 2021</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:14px;">Jan 2021 to Dec 2023</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:14px;">Jan 2021 to Dec 2023</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:14px;">Jan 2021 to Dec 2023</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:14px;">Jan 2021 to Dec 2023</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:14px;">Jan 2021 to Dec 2023</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:14px;">Jan 2021 to Dec 2023</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:14px;">Jan 2021 to Dec 2023</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Carpet Area (Sq. Ft.)</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$rented_area .'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Tenure (Months)</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$ag_tenure.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Notice Period</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$notice_period.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Per Month Rent</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$monthle_rent.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$monthle_rent.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$monthle_rent.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$monthle_rent.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$monthle_rent.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$monthle_rent.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$monthle_rent.'</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Security Deposit</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$security_depo.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="background-color:#a6ccfd;">
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;background-color:#a6ccfd;" colspan="8">Inflow</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Rent: Per Annum (Rs)</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$monthle_rent.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Less:Per Annum CAM</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$cam_charges.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Total Inflow Per Annum (Rental Income) (Rs)</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="background-color:#a6ccfd;;">
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;background-color:#a6ccfd;" colspan="8">Acquisition Cost</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Total Acquisition Cost</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Add: Stamp Duty and Registration</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$stamp_duty.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Less: Security Deposit</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$security_depo.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;">Total OutFlow (Rs.)</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="background-color:#a6ccfd;;">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">Yield (%)</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                        </tr>
                                        <tr style="background-color:#a6ccfd;;">
                                            <td style="width:40%;border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">Weighted Average Yield Over Tenure</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#a6ccfd;">'.$dummy.'</td>
                                        </tr>
                                    </table>

                                </td>
                                </tr>
                            </table>
                    </div>';


                    /**/


                    /*require('.././excel/PHPExcel.php');
                    $filename = "export.xlsx";
                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->setActiveSheetIndex(0);
                    $asheet = $objPHPExcel->getActiveSheet();
                
                    $table = $htmlstring; //put your table
                
                    $tmpfile = tempnam(sys_get_temp_dir(), 'html');
                    file_put_contents($tmpfile, $table);
                    $excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
                    $excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
                    unlink($tmpfile);

                    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
                    $ds        = DIRECTORY_SEPARATOR;
                    $objWriter->save('uploads'.$ds.'reports'.$ds.$filename); */


    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});
// END SAMPLE
$app->get('/select_mail_template/:mail_template_id', function($mail_template_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from mail_template WHERE mail_template_id = $mail_template_id ";
    $getmail_templates = $db->getAllRecords($sql);
    echo json_encode($getmail_templates);
});

$app->post('/mail_sent', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');

    $ds          = DIRECTORY_SEPARATOR;
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('to_mail_id'),$r->mail_sent);
    $db = new DbHandler();
    $mail_date = $r->mail_sent->mail_date;
    $created_date = $r->mail_sent->created_date;
    //$client_id = $r->mail_sent->client_id;
    $to_mail_id = $r->mail_sent->to_mail_id;
    $cc_mail_id = $r->mail_sent->cc_mail_id;
    //$bcc_mail_id = $r->mail_sent->bcc_mail_id;
    $subject = $r->mail_sent->subject;
    $attachments = $r->mail_sent->file_name;
    $text_message = $r->mail_sent->text_message;
    
    /*if (isset($r->mail_sent->selected_properties))
    {
        $selected_properties = $r->mail_sent->selected_properties;
        $sql = "SELECT *,a.exp_price,a.rera_num,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, a.share_on_website, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames ,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.property_id IN ($selected_properties))";

        $stmt = $db->getRows($sql);
        $htmlstring = '';
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $htmlstring .= '<div>
                                        <p ng-if="selectproperties.length>0" style="font-size:16px;font-weight: bold;">Matching Properties</p>
                                        <div ng-repeat="selectproperty in selectproperties">
                                            
                                            <p style="font-size:16px;text-align:center;font-weight: bold;border:1px solid #a09a9a;">PROPERTYID_{{selectproperty.property_id}}</p></a>   
                                            
                                            <p style="font-size:20px;color:#2b57a0; margin-top:5px;">
                                                <span ng-if="selectproperty.project_name">{{selectproperty.project_name}}</span>
                                                <span ng-if="!selectproperty.project_name">{{selectproperty.building_name}}</span>
                                                <span ng-if="selectproperty.project_name || selectproperty.building_name">:</span><span ng-if="selectproperty.bedrooms>0">{{selectproperty.bedrooms}}BHK &nbsp;</span>{{selectproperty.propsubtype}} for {{selectproperty.property_for}}
                                            </p>
                                            <div class="checkbox" style="position:absolute;margin:0px;"><label><input type="checkbox" class="check_element" name="assign_all" id="assign_all" ng-model="assign_all" style="width:30px;height:30px;" value="{{selectproperty.property_id}}" ng-click="selectproperty_for_mail(selectproperty.property_id)"/></label>
                                            </div>
                                            <p><img class="img_size" src="dist/img/no_image.jpg" ng-if="selectproperty.filenames==null " style="height:185px;" /> 
                                            <img class="img_size" src="api/v1/uploads/property/thumb/{{selectproperty.filenames}}" ng-if="selectproperty.filenames!=null " style="height:185px;" /> </p>
                                            <p style="font-size:14px;line-height:normal;";>
                                            <span ng-if="selectproperty.wing">{{selectproperty.wing}},&nbsp;</span><span ng-if="selectproperty.unit">{{selectproperty.unit}},&nbsp;</span><span ng-if="selectproperty.floor">{{selectproperty.floor}},&nbsp;</span><span ng-if="selectproperty.road_no">{{selectproperty.road_no}},&nbsp;</span><span ng-if="!selectproperty.project_name">{{selectproperty.building_name}},&nbsp;</span><span ng-if="selectproperty.landmark">{{selectproperty.landmark}},&nbsp;</span><span ng-if="selectproperty.locality">{{selectproperty.locality}},&nbsp;</span><span ng-if="selectproperty.area_name">{{selectproperty.area_name}},&nbsp;</span><span ng-if="selectproperty.city">{{selectproperty.city}}</span></p>
                                        </div>
                                    </div>';

            }
        }

    }*/




    //$attachments = $r->mail_sent->attachments;
    
    /*require '../phpmailer/PHPMailerAutoload.php';
    require '../phpmailer/class.smtp.php';
    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Mailer = 'smtp';
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true; 
    $mail->SMTPDebug = 2; 
    $mail->Timeout       =   1300; 
    $mail->SMTPKeepAlive = true;
    $mail->Username = 'test@realtyhubpro.com'; 
    $mail->Password = 'Cloud123#';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;    
    //$mail->SMTPAuth = false;
    //$mail->SMTPSecure = false;

    $mail->setFrom('crm@rdbrother.com');
    //$mail->AddReplyTo("sqft@realtyhubpro.com", "sqft");
    
    $new_data = explode(';',$to_mail_id);
    $tstring = '';
    if ($new_data)
    {
        foreach($new_data as $email)
        {
            $mail->AddAddress(trim($email)); 
        }
    }
    $new_data = explode(';',$cc_mail_id);
    if ($new_data)
    {
        foreach($new_data as $email)
        { 
            $mail->AddCC(trim($email)); 
        } 
    }
    /*$new_data = explode(';',$bcc_mail_id);
    if ($new_data)
    {
        foreach($new_data as $email)
        { 
            $mail->AddBCC(trim($email)); 
        } 
    }
    $dir = dirname(__FILE__);
    $new_data = explode(';',$attachments);
    if ($new_data)
    {
        foreach($new_data as $fn)
        { 
            $filename = $dir.$ds.'uploads'.$ds.$fn;
            $mail->addAttachment($filename);   
        } 
    }
    
    $mail->isHTML(true);  
    $mail->Subject = $subject;
    $mail->Body    = '<pre>'.$text_message.'</pre>';
    
    if(!$mail->send()) {
        $response["status"] = "error";
        $response["message"] = "Mail not Sent ...!".$mail->ErrorInfo;
        echoResponse(200, $response); 
        $mail->SmtpClose();
    } else {
        $query = "INSERT INTO client_mails (mail_date , category,category_id, subject, text_message, to_mail_id, cc_mail_id,attachments,created_by,  created_date)   VALUES('$mail_date' , '$category_id', '$category', '$subject', '$attchaments','$text_message', '$to_mail_id', '$cc_mail_id', '', '$created_date')";
        $result = $db->insertByQuery($query);
        
        $mail->SmtpClose();
        $response["status"] = "success";
        $response["message"] = "Message has been sent ...!";
        echoResponse(200, $response); 
    }

    $myemail = "crm@rdbrothers.com";
    $toemail = $r->mail_sent->to_mail_id;

    $myemaildata = $db->getAllRecords("SELECT off_email FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = $created_by LIMIT 1");
    if ($myemaildata)
    {
        $myemail = $myemaildata[0]['off_email'];

    }

    $cc_mail_id = $r->mail_sent->cc_mail_id;
    $email_subject = $r->mail_sent->subject;
    $email_body = "<html><body>".$r->mail_sent->text_message."</html></body>";
    $category_id = $r->mail_sent->category_id;
    $category = $r->mail_sent->category;
    $selected_files = $r->mail_sent->selected_files;
    $headers = "From: ".$myemail."\r\n";
    $headers .= "Reply-To: ".$myemail." \r\n";
    $headers .= "CC: ".$cc_mail_id."\r\n";
    $uid = md5(uniqid(time()));
   

    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";

    $nmessage .= "--".$uid."\r\n";
    $nmessage .= "Content-type:text/html; charset=iso-8859-1\r\n";
    $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $nmessage .= $email_body."\r\n\r\n";
    $new_data = explode(',',$attachments);
    $tstring = '';
    if ($new_data)
    {
        if (count($new_data)<=1)
        {
            $name_of_the_file = $attachments;
            $file = dirname( __FILE__ ). $ds."uploads" . $ds . "attachments". $ds. $name_of_the_file;
            $content = file_get_contents( $file);
            $content = chunk_split(base64_encode($content));
            $nmessage .= "--".$uid."\r\n";
            $nmessage .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name_of_the_file\"\n" . 
            "Content-Disposition: attachment;\n" . " filename=\"$name_of_the_file\"\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $content . "\n\n";
        }
        else
        {
            foreach($new_data as $name_of_the_file)
            {
                $file = dirname( __FILE__ ). $ds."uploads" . $ds . "attachments". $ds. $name_of_the_file;
                $content = file_get_contents( $file);
                $content = chunk_split(base64_encode($content));
                $nmessage .= "--".$uid."\r\n";
                $nmessage .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name_of_the_file\"\n" . 
                "Content-Disposition: attachment;\n" . " filename=\"$name_of_the_file\"\n" . 
                "Content-Transfer-Encoding: base64\n\n" . $content . "\n\n";
                
            }
        }
    }

    $sql = "SELECT * FROM attachments WHERE attachment_id IN ($selected_files)";
    $stmt = $db->getRows($sql);

    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $name_of_the_file = $row['filenames'];
            $file = dirname( __FILE__ ). $ds."uploads" . $ds . "reports". $ds. $name_of_the_file;
            if ($row['category'] == 'property_docs')
            {
                $file = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. "docs" .$ds. $name_of_the_file;
            }
            $content = file_get_contents( $file);
            $content = chunk_split(base64_encode($content));
            $nmessage .= "--".$uid."\r\n";
            $nmessage .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name_of_the_file\"\n" . 
            "Content-Disposition: attachment;\n" . " filename=\"$name_of_the_file\"\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $content . "\n\n";
        }
    }


    
    if(!mail($toemail,$email_subject,$nmessage,$headers))
    {
        $response["status"] = "error";
        $response["message"] = "Mail not Sent ...!";
    }
    else
    {
        $response["status"] = "success";
        $response["message"] = "Mail has been sent ...!";
        $mail_date = date('Y-m-d');
        $created_date = date('Y-m-d H:i:s');
        $query = "INSERT INTO client_mails (mail_date , category,category_id, subject, text_message, from_mail_id, to_mail_id, cc_mail_id, attachments, created_by,  created_date)  VALUES('$mail_date' , '$category', '$category_id', '$email_subject', '$email_body','$myemail', '$toemail', '$cc_mail_id', '$attachments' ,'$created_by', '$created_date')";
        $result = $db->insertByQuery($query);
    }
    echoResponse(200, $response);*/

    //* ORIGINAL FORMAT
    $myemail = "crm@rdbrothers.com";
    $toemail = $r->mail_sent->to_mail_id;
    $parts = explode("@",$toemail); 
    $domain_name = $parts[1]; 
    
    if ($domain_name=='rdbrothers.com')
    {
        //$toemail .=  '.test-google-a.com';
    }

    $myemaildata = $db->getAllRecords("SELECT off_email FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = $created_by LIMIT 1");
    if ($myemaildata)
    {
        $myemail = $myemaildata[0]['off_email'];

    }

    $cc_mail_id = $r->mail_sent->cc_mail_id;
    $email_subject = $r->mail_sent->subject;
    $email_body = "<html><body>".$r->mail_sent->text_message."</html></body>";
    $category_id = $r->mail_sent->category_id;
    $category = $r->mail_sent->category;
    $selected_files = $r->mail_sent->selected_files;
    $headers = "From: ".$myemail."\r\n";
    $headers .= "Reply-To: ".$myemail." \r\n";
    $headers .= "CC: ".$cc_mail_id."\r\n";
    $uid = md5(uniqid(time()));
    
    
    
    // message & attachment
    //$nmessage = "--".$uid."\r\n";

    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";

    $nmessage .= "--".$uid."\r\n";
    $nmessage .= "Content-type:text/html; charset=iso-8859-1\r\n";
    $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $nmessage .= $email_body."\r\n\r\n";
    $new_data = explode(',',$attachments);
    $tstring = '';
    if ($new_data)
    {
        if (count($new_data)<=1)
        {
            $name_of_the_file = $attachments;
            $file = dirname( __FILE__ ). $ds."uploads" . $ds . "attachments". $ds. $name_of_the_file;
            $content = file_get_contents( $file);
            $content = chunk_split(base64_encode($content));
            $nmessage .= "--".$uid."\r\n";
            $nmessage .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name_of_the_file\"\n" . 
            "Content-Disposition: attachment;\n" . " filename=\"$name_of_the_file\"\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $content . "\n\n";
        }
        else
        {
            foreach($new_data as $name_of_the_file)
            {
                $file = dirname( __FILE__ ). $ds."uploads" . $ds . "attachments". $ds. $name_of_the_file;
                $content = file_get_contents( $file);
                $content = chunk_split(base64_encode($content));
                $nmessage .= "--".$uid."\r\n";
                $nmessage .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name_of_the_file\"\n" . 
                "Content-Disposition: attachment;\n" . " filename=\"$name_of_the_file\"\n" . 
                "Content-Transfer-Encoding: base64\n\n" . $content . "\n\n";
                
            }
        }
    }

    $sql = "SELECT * FROM attachments WHERE attachment_id IN ($selected_files)";
    $stmt = $db->getRows($sql);

    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $name_of_the_file = $row['filenames'];
            $file = dirname( __FILE__ ). $ds."uploads" . $ds . "reports". $ds. $name_of_the_file;
            if ($row['category'] == 'property_docs')
            {
                $file = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. "docs" .$ds. $name_of_the_file;
            }
            $content = file_get_contents( $file);
            $content = chunk_split(base64_encode($content));
            $nmessage .= "--".$uid."\r\n";
            $nmessage .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$name_of_the_file\"\n" . 
            "Content-Disposition: attachment;\n" . " filename=\"$name_of_the_file\"\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $content . "\n\n";
        }
    }


    //$nmessage .= "Content-Type: application/octet-stream; name=\"".$attachments."\"\r\n";
    //$nmessage .= "Content-Transfer-Encoding: base64\r\n";
    //$nmessage .= "Content-Disposition: attachment; filename=\"".$attachments."\"\r\n\r\n";
    //$nmessage .= $content."\r\n\r\n";
    $nmessage .= "--".$uid."--";
    define(MAIL_HUB, 'rdbrothers.com');
    define(LOCAL_RELAY, 'rdbrothers.com');
    if(!mail($toemail,$email_subject,$nmessage,$headers))
    {
        $response["status"] = "error";
        $response["message"] = "Mail not Sent ...!";
    }
    else
    {
        $response["status"] = "success";
        $response["message"] = "Mail has been sent ...!";
        $mail_date = date('Y-m-d');
        $created_date = date('Y-m-d H:i:s');
        $query = "INSERT INTO client_mails (mail_date , category,category_id, subject, text_message, from_mail_id, to_mail_id, cc_mail_id, attachments, created_by,  created_date)  VALUES('$mail_date' , '$category', '$category_id', '$email_subject', '$email_body','$myemail', '$toemail', '$cc_mail_id', '$attachments' ,'$created_by', '$created_date')";
        $result = $db->insertByQuery($query);
    }
    echoResponse(200, $response);
});


$app->get('/removedocument/:attachment_id', function($attachment_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();

    $sql = "DELETE FROM attachments WHERE attachment_id = $attachment_id " ;  
    $result = $db->updateByQuery($sql);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "document Deleted successfully";
        echoResponse(200, $response);
    }
});

$app->get('/getonereferrals/:mailcategory_id', function($mailcategory_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from referrals as a WHERE a.referrals_id = $mailcategory_id ";
    $getonereferrals = $db->getAllRecords($sql);
    echo json_encode($getonereferrals);
});

$app->get('/getonecontacts/:mailcategory_id', function($mailcategory_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT * FROM contact AS a WHERE a.contact_id IN ($mailcategory_id) ";
    $getonecontacts = $db->getAllRecords($sql);
    echo json_encode($getonecontacts);
});

function create_pdf($htmlstring,$filename)
{
    include("mpdf60/mpdf.php");
        
    /*$stylesheet = file_get_contents('stylesheet.css');
    $mpdf->WriteHTML($stylesheet,1);
    $mpdf = new mPDF('',    // mode - default ''
                        '',    // format - A4, for example, default ''
                        0,     // font size - default 0
                        '',    // default font family
                        15,    // margin_left
                        15,    // margin right
                        16,    // margin top
                        16,    // margin bottom
                        9,     // margin header
                        9,     // margin footer
                        'L');  // L - landscape, P - portrait*/
    
    
    $mpdf = new mPDF('', 'Letter-L', 0, '', 5, 5, 5, 1, 0, 0,'L');
    $mpdf->SetDisplayMode('fullwidth');
    //$mpdf->SetDisplayMode('fullpage');

    //$mpdf->SetFont("Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif");
    $htmlheader="";
    $htmlfooter="";

    $mpdf->SetHTMLHeader($htmlheader, 'O', true);
    $mpdf->SetFooter($htmlfooter);
    
    $mpdf->WriteHTML($htmlstring);
    $ds          = DIRECTORY_SEPARATOR;
    //$dir = $_SERVER['DOCUMENT_ROOT'];
    $dir = $_SERVER['SERVER_NAME'] ;//. dirname(__FILE__);
    
    $mpdf->Output('uploads'.$ds.'reports'.$ds.$filename);

}


$app->post('/attachment_uploads_old', function() use ($app) {
    session_start();
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
        //$file_names = "c_".$emp_id."_".time()."_".$count.".".$ext;
        $file_names = strtolowerpathinfo($filenames[$i]);
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "attachments". $ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->post('/attachment_uploads', function() use ($app) {
    session_start();
    //$emp_id = $_SESSION['tmpemp_id'];
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
        //$ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        //$file_names = "a_".$user_id."_".time()."_".$count.".".$ext;
        $file_names = $filenames[$i];
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "attachments". $ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;            

            /*$query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, created_by, created_date)   VALUES('employee', '$emp_id','$file_names','Yes','Yes','Main','$user_id', now() )";
            $result = $db->insertByQuery($query);*/
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});


$app->get('/getsentitemsorg', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $sql = "SELECT *, DATE_FORMAT(created_date,'%d/%m/%Y %H:%S') as created_date from client_mails ORDER BY client_mails.created_date DESC";
    $htmlstring = '<div style="overflow-y:auto;min-height:400px;"><ul style="display:block;margin-left:-41px;">';
    $stmt = $db->getRows($sql);

    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
        
    $htmlstring .= '<a href="javascript:void(0)" ng-click="showclientmail('.$row['mail_id'].')"><li style="border:1px solid #DACECE;padding:10px;margin-bottom:3px;"><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;"><span>'.$row['to_mail_id'].'</span><span style="float:right;">'.$row['created_date'].'</span></p><p  style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;">'.$row['subject'].'</p></li></a>';
            
        }
    }
    $htmlstring .='</ul></div>';        
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/getsentitems/:mailcategory/:mailcategory_id/:next_page_id', function($mailcategory,$mailcategory_id,$next_page_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $created_by = $session['user_id'];
    $role = $session['role'];
    
    $countsql = "";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    { 
        $sql = "SELECT *, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') as created_date, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') as mail_date,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = client_mails.created_by) as created_by from client_mails WHERE client_mails.category = '$mailcategory' and client_mails.category_id = '$mailcategory_id' ORDER BY client_mails.created_date DESC LIMIT $next_page_id,30";
        $countsql = "SELECT count(*) as mail_count from client_mails WHERE category = '$mailcategory' and category_id = '$mailcategory_id'";        
    }
    else
    {
        $sql = "SELECT *, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') as created_date, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') as mail_date,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by from client_mails as a  WHERE a.created_by = $created_by and a.category = '$mailcategory' and a.category_id = '$mailcategory_id' ORDER BY a.created_date DESC LIMIT $next_page_id,30";
        $countsql = "SELECT count(*) as mail_count from client_mails as a WHERE a.created_by = $created_by  and a.category = '$mailcategory' and a.category_id = '$mailcategory_id'"; 
    }

    $getsentitems = $db->getAllRecords($sql);
    $mail_count = 0;
    $mailcountdata = $db->getAllRecords($countsql);
    if ($mailcountdata)
    {
         $mail_count = $mailcountdata[0]['mail_count'];
    }
    if ($mail_count>0)
    {
        $getsentitems[0]['mail_count']=$mail_count;
    }   
    
    echo json_encode($getsentitems);
});

$app->post('/smssend', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');

    $response = array();
    $r = json_decode($app->request->getBody());
    $db = new DbHandler();
    $receipient = $r->sms_data->receipient;
    $message  = $r->sms_data->message;
    $category = $r->sms_data->category;
    $category_id  = $r->sms_data->category_id;

    $curl = curl_init();
    $post_fields = array();
    $post_fields["method"] = "sendMessage";
    $post_fields["send_to"] = $receipient;
    $post_fields["msg"] = $message;
    // $post_fields["msg"] = "May your birthday be the start of a year filled with good luck, good health & much happiness. Here’s wishing you a very happy birthday from team R.D.Brothers Properties";
    
    //$post_fields["msg"] =  "Warm greetings from R.D Brothers Properties! Please note that Registration Office address has been sent on your registered email Id or contact  9823041486 for further details";

  
    
    $post_fields["msg_type"] = "TEXT";
    $post_fields["userid"] = "2000196081";
    $post_fields["password"] = "Rdbrothers@123";
    $post_fields["v"] = "1.1";
    $post_fields["auth_scheme"] = "PLAIN";
    $post_fields["format"] = "JSON";

    //$res = file_get_contents("http://smsp.myoperator.co/api/sendhttp.php?authkey=150242ABJivcxsN58ff6943&mobiles=91".$customer_mobile."&message=Hey there, Its ".$delivery_boy." I just received your order from ".$resturant_name." , I shall visit resturant shortly. Feel free to call me on (".$delivery_mobile.") for any query related to your order no: ".$order_no."&sender=FBUNNY&route=4");

    // IMPORTANT
    //$res = file_get_contents("https://enterprise.smsgupshup.com/GatewayAPI/rest?method=SendMessage&send_to=91".$receipient."&msg=".$message."&msg_type=TEXT&userid=2000195702&auth_scheme=plain&password=Rdbrothers@123&v=1.1"); 

    //https://enterprise.smsgupshup.com/GatewayAPI/rest?msg=Hi%20Test%20Message &v=1.1&userid=XXXXXXXXX&password=XXXXX&send_to=9XXXXXXXXX&msg_type=text&method=sendMessage

   //https://enterprise.smsgupshup.com/GatewayAPI/rest?msg=Hi&v=1.1&userid=2000196081&password=Rdbrothers@123&send_to=7021149409&msg_type=text&method=sendMessage
    
    curl_setopt_array($curl, array(CURLOPT_URL => "https://enterprise.smsgupshup.com/GatewayAPI/rest",CURLOPT_RETURNTRANSFER => true,CURLOPT_ENCODING => "",CURLOPT_MAXREDIRS => 10,CURLOPT_TIMEOUT =>30,CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,CURLOPT_CUSTOMREQUEST => "POST",CURLOPT_POSTFIELDS => $post_fields));
    $tresponse = curl_exec($curl);
    // console.log($tresponse);
    $t = json_decode($tresponse);
    $data = $t->response->id;
    $res = curl_error($curl);
    curl_close($curl);
    if ($res) {
        $response["status"] = "error";
        $response["message"] = "SMS not Sent ...! ".$res;
    } else 
    {
        $response["status"] = "success";
        $response["message"] = "SMS has been sent ...!";
        $response["response_received"] = $data;
        //$sresponse['res'] = $res;
        
        $mail_date = date('Y-m-d');
        $created_date = date('Y-m-d H:i:s');
        
        $query = "INSERT INTO client_sms (category,category_id, receipient, message, sms_response,status,created_by,  created_date)  VALUES('$category', '$category_id', '$receipient', '$message','$data','success','$created_by', '$created_date')";
        
        $result = $db->insertByQuery($query);

    }
    echoResponse(200, $response);
});

$app->post('/wasend', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');

    $response = array();
    $r = json_decode($app->request->getBody());
    $db = new DbHandler();
    $receipient = $r->wa_data->receipient;
    $message  = $r->wa_data->message;
    $category = $r->wa_data->category;
    $category_id  = $r->wa_data->category_id;
    $mail_date = date('Y-m-d');
    $created_date = date('Y-m-d H:i:s');
    
    $query = "INSERT INTO client_wa (category,category_id, receipient, message, wa_response,status,created_by,  created_date)  VALUES('$category', '$category_id', '$receipient', '$message','$data','success','$created_by', '$created_date')";
    
    $result = $db->insertByQuery($query);
    $response["status"] = "success";
    $response["message"] = "SMS has been sent ...!";
    $response["response_received"] = $data;
    echoResponse(200, $response);
});

$app->get('/select_broucher/:module_name/:id/:data', function($module_name,$id,$data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $htmlstring = '<html><head></head><body>';

    if($module_name == 'property')
    {
        $sql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in($data) GROUP BY a.property_id ORDER BY CAST(a.property_id as UNSIGNED) DESC";

        

        $stmt = $db->getRows($sql);
        $count = 1;
        
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            { 
                $htmlstring .= '<table width="600" style="font-size:12px;font-family:arial;border:1px solid #cccccc;background-color:#bad6ef;padding:10px;" cellspacing="0" cellpadding="0" align="center">
                <tr>
                    <td>
                        <table width="580" style="background-color:#8496a5;color:#ffffff;padding:10px;margin:10px;margin-bottom:0px;" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td style="padding:15px;"><p style="text-align:center;">We have shortlisted following properties for you.</p></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="580" style="background-color:#5f89ab;color:#ffffff;margin:10px;" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td>
                                    <table width="560" style="background-color:#ffffff;color:#000000;padding:10px;margin:10px;" cellspacing="0" cellpadding="0" align="center">
                                        <tr>
                                            <td style="width:245px;height:185px;border-right:1px solid #5d5d5d;"><img src="api/v1/uploads/property/thumb/'.$row['filenames'].'" style="width:245px; height:185px;" /></td>
                                            <td colspan="3" style="text-align:left;padding:5px;border-right:1px solid #5d5d5d;" valign="top" >
                                                <p style="font-size:20px;color:#ff0000;margin-top:5px;">
                                                <span>'.$row['project_name'].' '.$row['building_name'].'</span>
                                                '.$row['propsubtype'].' for '.$row['property_for'].'
                                                </p>
                                                <p style="font-size:16px";>
                                                    '.$row['locality'].',
                                                    <span>'.$row['area_name'].', </span>
                                                    <span>'.$row['city'].'</span>
                                                </p>
                                                <p style="font-size:16px;"><span>Property ID:'.$row['property_id'].'</span> <span style="margin-right:15px;">'.$db->ShowAmount($row['exp_price'],$row['exp_price_para']).' '.$row['exp_price_para'].'</p>
                                                <p style="font-size:16px;"><span>'.$row['furniture'].'</span> </p>
                                            </td>
                                        </tr>
                                        <tr style="border-top:1px solid #5d5d5d;">
                                            <td colspan="3" style="text-align:left;padding:5px;">For More Information Contact: <span style="font-weight:bold;margin-right:5px;">'.$row['project_contact'].'</span></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table width="560" style="background-color:#87b5ef;color:#ffffff;padding:10px;margin:10px;" cellspacing="0" cellpadding="0" align="left">
                                        <tr style="border-bottom:1px solid #5d5d5d;font-size:14px;">
                                            <td colspan="3" style="text-align:left;padding:5px;">'.$row['assign_to'].'</td>
                                        </tr>
                                        <tr>
                                            <td>';
                                            if ($row['mob_no'])
                                            {
                                                $htmlstring .='<p style="text-align:left;padding:5px;">'.$row['mob_no'].'</p>';
                                            }
                                            if ($row['email'])
                                            {
                                                $htmlstring .='<p style="text-align:left;padding:5px;">'.$row['email'].'</p>';
                                            }
                                            if ($row['off_phone'])
                                            {
                                                $htmlstring .='<p style="text-align:left;padding:5px;">Office Phone:'.$row['off_phone'].'</p>';
                                            }
                                            if ($row['team'])
                                            {
                                                $htmlstring .='<p style="text-align:left;padding:5px;">Company Address:'.$row['team'].'</p>';
                                            }

                                            $htmlstring .='</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="580" style="background-color:#dbe7f1;color:#ffffff;margin:10px;" cellspacing="0" cellpadding="0" align="center">
                            <tr><td><p style="text-align:center;"><img src="dist/img/sqftlogo1.jpg" style="width:71px; height:71px;" /></p></td></tr>
                        </table>
                    </td>
                </tr>
                </table>
                <p style="margin-bottom:10px;"></p>';
            
            
            }
        }

        $htmlstring .= '</body></html>';


        /*$propertydata = $db->getAllRecords($sql);
        $property_id = 0;
        if ($propertydata)
        {
            $property_id = $propertydata[0]['property_id'];
            $location_map = "location.jpg";
            $page_1 = '<div id="page_1" style="width:978px;height:728px;margin:20px;">
                            <img src="dist/img/sqft.jpg" style="width:958px; height:708px;" />
                    </div>';

            $page_2 =   '<div id="page_2" style="width:978px;height:728px;margin:20px;">
                            <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                <div style="text-align:center;">
                                    <div style="width: 90%;float: left;font-size: 45px;">'.$propertydata[0]['project_name'].':'.$propertydata[0]['propsubtype'].' for '.$propertydata[0]['property_for'].'</div>
                                    <div style="float:right;">
                                        <img src="dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                    </div>
                                </div>
                                <div>
                                    <img src="api/v1/uploads/property/'.$propertydata[0]['filenames'].'"  style="width:752px; height:505px;margin:50px;margin-left:75px;" />
                                </div>
                            </div>
                        </div>';
            $page_3 =   '<div id="page_3" style="width:978px;height:728px;margin:20px;">
                            <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                <div style="text-align:center;">
                                    <div style="width:90%;float: left;font-size: 45px;color:#000000;background-color:#ffffff;">Commercial Terms</div>
                                    <div style="float:right;">
                                        <img src="dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                    </div>
                                </div>
                                <div  style="margin:50px;">
                                    <table style="background-color:#747272;color:#000000;font-size:30px;width:100%;border:1px solid #ffffff;">
                                    <tr>
                                    <td style="width:50%;border:1px solid #ffffff;">Carpet Area (sqft)</td>
                                    <td style="width:50%;border:1px solid #ffffff;">'.$propertydata[0]['carp_area'].'</td>
                                    </tr>
                                    <tr>
                                    <td style="width:50%;border:1px solid #ffffff;">Saleable Area (sqft)</td>
                                    <td style="width:50%;border:1px solid #ffffff;">'.$propertydata[0]['sale_area'].'</td>
                                    </tr>
                                    <tr>
                                    <td style="width:50%;border:1px solid #ffffff;">Proposed Floors</td>
                                    <td style="width:50%;border:1px solid #ffffff;">'.$propertydata[0]['floor'].'</td>
                                    </tr>
                                    <tr>
                                    <td style="width:50%;border:1px solid #ffffff;">Quoted Rent on Saleable Area</td>
                                    <td style="width:50%;border:1px solid #ffffff;">'.$propertydata[0]['exp_rent'].'</td>
                                    </tr>
                                    <tr>
                                    <td style="width:50%;border:1px solid #ffffff;">Security Deposit</td>
                                    <td style="width:50%;border:1px solid #ffffff;">'.$propertydata[0]['security_depo'].'</td>
                                    </tr>
                                    <tr>
                                    <td style="width:50%;border:1px solid #ffffff;">Lock in period</td>
                                    <td style="width:50%;border:1px solid #ffffff;">'.$propertydata[0]['lock_per'].'</td>
                                    </tr>
                                    <tr>
                                    <td style="width:50%;border:1px solid #ffffff;">Lease Tenure</td>
                                    <td style="width:50%;border:1px solid #ffffff;">'.$propertydata[0]['tenure_year'].'</td>
                                    </tr>
                                    <tr>
                                    <td style="width:50%;border:1px solid #ffffff;">Rent Escalation</td>
                                    <td style="width:50%;border:1px solid #ffffff;">'.$propertydata[0]['rent_esc'].'</td>
                                    </tr>
                                    <tr>
                                    <td style="width:50%;border:1px solid #ffffff;">Location</td>
                                    <td style="width:50%;border:1px solid #ffffff;">'.$propertydata[0]['locality'].','.$propertydata[0]['city'].'</td>
                                    </tr>
                                    <tr>
                                    <td style="width:50%;border:1px solid #ffffff;">Furniture Detail</td>
                                    <td style="width:50%;border:1px solid #ffffff;">'.$propertydata[0]['furniture'].'</td>
                                    </tr>
                                    </table>
                                </div>
                            </div>
                        </div>';
            $page_4 =   '<div id="page_4" style="width:978px;height:728px;margin:20px;">
                            <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                <div style="text-align:center;">
                                    <div style="width:90%;float: left;font-size: 45px;color:#000000;background-color:#ffffff;">PHOTOS</div>
                                    <div style="float:right;">
                                        <img src="dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                    </div>
                                </div>
                                <div style="margin:100px;">
                                    <table style="font-size:18px;">
                                        <tr>';

            $sql = "SELECT * from attachments WHERE category = 'property' and category_id = $property_id LIMIT 4";
            $stmt = $db->getRows($sql);
            $count = 1;
            if($stmt->num_rows > 0)
            {
                while($row = $stmt->fetch_assoc())
                {                          
            
                    $page_4 .=                      '<td style="width:50%;padding:10px;text-align:center;">
                                                        <img src="api/v1/uploads/property/'.$row['filenames'].'" style="width:340px; height:228px;border:1px solid #000000;"/>
                                                        <p style="text-align:center;">'.$row['description'].'</p>
                                                    </td>';
                                                    $count ++;
                                                    if($count==3)
                                                    {
                                                        $page_4 .='</tr><tr>';
                                                    }
                }
            }

            $page_4 .=                    '</tr>
                                    </table>
                                </div>
                            </div>
                        </div>';
            $page_5 =   '<div id="page_5" style="width:978px;height:728px;margin:20px;">
                            <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                <div style="text-align:center;">
                                    <div style="width:90%;float: left;font-size: 45px;color:#000000;background-color:#ffffff;">Location Map</div>
                                    <div style="float:right;">
                                        <img src="dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                        </div>
                                    </div>
                                <div style="margin:100px;">
                                    <img src="api/v1/uploads/property/'.$location_map.'"  style="width:752px; height:505px;" />
                                </div>
                            </div>
                        </div>';
            $page_6 =   '<div id="page_6" style="width:978px;height:728px;margin:20px;">
                            <div style="border:5px solid #ee0a0a;width:968px; height:718px;text-align:center;">
                                <div style="margin-top:200px;margin-left:350px;text-align:center;">
                                    <img src="dist/img/sqftlogo1.jpg" style="width:251px; height:271px;" />
                                </div>
                                <div style="text-align:center;width:100%;margin-top:50px;">
                                <div style="text-align:center;font-size:20px;font-weight:bold;width:80%;margin:0 auto;">
                                    <p>402/403, Metro Avenue, Opp Guru Nanak Petrol Pump, Near Western Exp Highway, Andheri East, Mumbai-400 099.</p>
                                    <p>Mob : 8879634179</p>
                                    <p>Email Id  : <span style="color:#a898f0;">andheri@sqft.co.in</span></p>
                                    <p>Website : <span style="color:#ee0a0a;">www.sqft.co.in</span></p>
                                </div>
                                </div>
                            </div>
                        </div>';


            $htmldata['page_1']=$page_1;
            $htmldata['page_2']=$page_2;
            $htmldata['page_3']=$page_3;
            $htmldata['page_4']=$page_4;
            $htmldata['page_5']=$page_5;
            $htmldata['page_6']=$page_6;
        }*/
    }

    if($module_name == 'project')
    {
        $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id WHERE a.project_id in($data) GROUP BY a.project_id ORDER BY CAST(a.project_id as UNSIGNED) DESC";
        
        $stmt = $db->getRows($sql);
        $count = 1;
        
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            { 
                $htmlstring .= '<table width="600" style="font-size:12px;font-family:arial;border:1px solid #cccccc;background-color:#bad6ef;padding:10px;" cellspacing="0" cellpadding="0" align="center">
                <tr>
                    <td>
                        <table width="580" style="background-color:#8496a5;color:#ffffff;padding:10px;margin:10px;margin-bottom:0px;" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td style="padding:15px;"><p style="text-align:center;">We have shortlisted following projects for you.</p></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="580" style="background-color:#5f89ab;color:#ffffff;margin:10px;" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td>
                                    <table width="560" style="background-color:#ffffff;color:#000000;padding:10px;margin:10px;" cellspacing="0" cellpadding="0" align="center">
                                        <tr>
                                            <td style="width:245px;height:185px;border-right:1px solid #5d5d5d;"><img src="api/v1/uploads/property/thumb/'.$row['filenames'].'" style="width:245px; height:185px;" /></td>
                                            <td colspan="3" style="text-align:left;padding:5px;border-right:1px solid #5d5d5d;" valign="top" >
                                                <p style="font-size:20px;color:#ff0000;margin-top:5px;">
                                                <span>'.$row['project_name'].'
                                                </p>
                                                <p style="font-size:16px";>
                                                    '.$row['locality'].',
                                                    <span>'.$row['area_name'].', </span>
                                                    <span>'.$row['city'].'</span>
                                                </p>
                                                <p style="font-size:16px;"><span>Project ID:'.$row['project_id'].'</span> <span style="margin-right:15px;">'.$row['pack_price'].'</p>
                                            </td>
                                        </tr>
                                        <tr style="border-top:1px solid #5d5d5d;">
                                            <td colspan="3" style="text-align:left;padding:5px;">For More Information Contact: <span style="font-weight:bold;margin-right:5px;">'.$row['project_contact'].'</span></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table width="560" style="background-color:#87b5ef;color:#ffffff;padding:10px;margin:10px;" cellspacing="0" cellpadding="0" align="left">
                                        <tr style="border-bottom:1px solid #5d5d5d;font-size:14px;">
                                            <td colspan="3" style="text-align:left;padding:5px;">'.$row['assign_to'].'</td>
                                        </tr>
                                        <tr>
                                            <td>';
                                            if ($row['mob_no'])
                                            {
                                                $htmlstring .='<p style="text-align:left;padding:5px;">'.$row['mob_no'].'</p>';
                                            }
                                            if ($row['email'])
                                            {
                                                $htmlstring .='<p style="text-align:left;padding:5px;">'.$row['email'].'</p>';
                                            }
                                            if ($row['off_phone'])
                                            {
                                                $htmlstring .='<p style="text-align:left;padding:5px;">Office Phone:'.$row['off_phone'].'</p>';
                                            }
                                            if ($row['team'])
                                            {
                                                $htmlstring .='<p style="text-align:left;padding:5px;">Company Address:'.$row['team'].'</p>';
                                            }

                                            $htmlstring .='</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="580" style="background-color:#dbe7f1;color:#ffffff;margin:10px;" cellspacing="0" cellpadding="0" align="center">
                            <tr><td><p style="text-align:center;"><img src="dist/img/sqftlogo1.jpg" style="width:71px; height:71px;" /></p></td></tr>
                        </table>
                    </td>
                </tr>
                </table>
                <p style="margin-bottom:10px;"></p>';
            
            
            }
        }

        $htmlstring .= '</body></html>';

    }

    $htmldata['text_message']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->post('/send_broucher', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');

    $ds          = DIRECTORY_SEPARATOR;
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('to_mail_id'),$r->mail_sent);
    $db = new DbHandler();
    $mail_date = $r->mail_sent->mail_date;
    $created_date = $r->mail_sent->created_date;
    //$client_id = $r->mail_sent->client_id;
    $to_mail_id = $r->mail_sent->to_mail_id;
    $cc_mail_id = $r->mail_sent->cc_mail_id;
    //$bcc_mail_id = $r->mail_sent->bcc_mail_id;
    $subject = $r->mail_sent->subject;
    $text_message = $r->mail_sent->text_message;
    //$attachments = $r->mail_sent->attachments;
    
    /*require '../phpmailer/PHPMailerAutoload.php';
    require '../phpmailer/class.smtp.php';
    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Mailer = 'smtp';
    $mail->Host = 'server126.web-hosting.com'; 
    $mail->SMTPAuth = true; 
    $mail->SMTPDebug = 2; 
    $mail->Timeout       =   1300; 
    $mail->SMTPKeepAlive = true;
    $mail->Username = 'test@realtyhubpro.com'; 
    $mail->Password = 'Yahoo@123';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;    
    //$mail->SMTPAuth = false;
    //$mail->SMTPSecure = false;

    $mail->setFrom('test@realtyhubpro.com');
    //$mail->AddReplyTo("sqft@realtyhubpro.com", "sqft");
    
    $new_data = explode(';',$to_mail_id);
    $tstring = '';
    if ($new_data)
    {
        foreach($new_data as $email)
        {
            $mail->AddAddress(trim($email)); 
        }
    }
    $new_data = explode(';',$cc_mail_id);
    if ($new_data)
    {
        foreach($new_data as $email)
        { 
            $mail->AddCC(trim($email)); 
        } 
    }
    $new_data = explode(';',$bcc_mail_id);
    if ($new_data)
    {
        foreach($new_data as $email)
        { 
            $mail->AddBCC(trim($email)); 
        } 
    }
    $dir = dirname(__FILE__);
    $new_data = explode(';',$attachments);
    if ($new_data)
    {
        foreach($new_data as $fn)
        { 
            $filename = $dir.$ds.'uploads'.$ds.$fn;
            $mail->addAttachment($filename);   
        } 
    }
    
    $mail->isHTML(true);  
    $mail->Subject = $subject;
    $mail->Body    = '<pre>'.$text_message.'</pre>';
    
    if(!$mail->send()) {
        $response["status"] = "error";
        $response["message"] = "Mail not Sent ...!".$mail->ErrorInfo;
        echoResponse(200, $response); 
        $mail->SmtpClose();
    } else {
        $query = "INSERT INTO client_mails (mail_date , category,category_id, subject, text_message, to_mail_id, cc_mail_id,created_by,  created_date)   VALUES('$mail_date' , '$category_id', '$category', '$subject', '$text_message', '$to_mail_id', '$cc_mail_id', '', '$created_date')";
        $result = $db->insertByQuery($query);
        
        $mail->SmtpClose();
        $response["status"] = "success";
        $response["message"] = "Message has been sent ...!";
        echoResponse(200, $response); 
    }*/

    $myemail = "crm@rdbrothers.com";
    $parts = explode("@",$toemail); 
    $domain_name = $parts[1]; 
    $toemail = $r->mail_sent->to_mail_id;
    if ($domain_name=='rdbrothers.com')
    {
        //$toemail .=  '.test-google-a.com';
    }
    

    $myemaildata = $db->getAllRecords("SELECT off_email FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = $created_by LIMIT 1");
    if ($myemaildata)
    {
        $myemail = $myemaildata[0]['off_email'];

    }


    /*$first_id="Yes";
    if ($assign_to)
    {
        foreach($assign_to as $value)
        {
            $toemaildata = $db->getAllRecords("SELECT off_email FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = $value and off_email != '' ");
            if ($toemaildata)
            {
                if ($first_id=="Yes")
                {
                    $toemail = $toemaildata[0]['off_email'];
                    $first_id ="No";
                }
                else{
                    $toemail .= ';'.$toemaildata[0]['off_email'];
                } 
            }
        }
    }*/
    $cc_mail_id = $r->mail_sent->cc_mail_id;
    $email_subject = $r->mail_sent->subject;
    $email_body = $r->mail_sent->text_message;
    $category_id = $r->mail_sent->category_id;
    $headers = "From: ".$myemail."\r\n";
    $headers .= "Reply-To: ".$myemail." \r\n";
    $headers .= 'CC: test@test.com\r\n';
    define(MAIL_HUB, 'rdbrothers.com');
    define(LOCAL_RELAY, 'rdbrothers.com');
    if(!mail($toemail,$email_subject,$email_body,$headers))
    {
        $response["message1"] = "Mail not Sent ...!";
    }
    else
    {
        $response["message1"] = "Mail has been sent ...!";
        $mail_date = date('Y-m-d H:i:s');
        $created_date = date('Y-m-d H:i:s');
        $query = "INSERT INTO client_mails (mail_date , category,category_id, subject, text_message, from_mail_id, to_mail_id, cc_mail_id,created_by,  created_date)   VALUES('$mail_date' , 'property', '$category_id', '$email_subject', '$email_body', '$myemail', '$toemail', '$cc_mail_id', '$created_by', '$created_date')";
        $result = $db->insertByQuery($query);
    }
    echoResponse(200, $response);
});

$app->get('/reports/:module_name/:id/:data', function($module_name,$id,$data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    

    $htmlstring = '<ul class="slides">';
    if($module_name == 'project')
    {
        $sql = "SELECT *, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date  from project as a  LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.project_id = f.category_id and f.category='project' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.developer_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.project_id in($data) GROUP BY a.project_id LIMIT 1 ";

        $projectdata = $db->getAllRecords($sql);
        $project_id = 0;
        if ($projectdata)
        {
            $project_id = $projectdata[0]['project_id'];
            $location_map = "location.jpg";
            
            // PAGE 1
            $htmlstring .= '<li>
                                <div class="slider">
                                    <div style="width:978px;height:728px;margin:20px;">
                                        <img src="dist/img/sqft.jpg" style="width:958px; height:708px;" />
                                    </div>
                                </div>
                            </li>';

            // PAGE 2
            $htmlstring .= '<li>
                                <div class="slider">
                                    <div style="width:978px;height:728px;margin:20px;">
                                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                            <div style="text-align:center;">
                                                <div style="width: 90%;float: left;font-size: 45px;">'.$projectdata[0]['project_name'].':'.$projectdata[0]['project_type'].' for '.$projectdata[0]['project_for'].'
                                                </div>
                                                <div style="float:right;">
                                                    <img src="dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                                </div>
                                            </div>
                                        <div>
                                        <img src="api/v1/uploads/project/'.$projectdata[0]['filenames'].'"  style="width:752px; height:505px;margin:50px;margin-left:75px;" />
                                    </div>
                                </div>
                            </li>';

            // PAGE 3
            $htmlstring .= '<li>
                                <div class="slider">
                                    <div style="width:978px;height:728px;margin:20px;">
                                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                            <div style="text-align:center;">
                                                <div style="width:90%;float: left;font-size: 45px;color:#000000;background-color:#ffffff;">Commercial Terms
                                                </div>
                                                <div style="float:right;">
                                                    <img src="dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                                </div>
                                            </div>
                                            <div  style="margin:50px;">
                                                <table style="background-color:#dad5d5;color:#000000;font-size:30px;width:100%;border:1px solid #ffffff;">
                                                    <tr>
                                                        <td style="width:50%;border:1px solid #ffffff;">Total Area (sqft)</td>
                                                        <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['tot_area'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:50%;border:1px solid #ffffff;">Total Unit (sqft)</td>
                                                        <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['tot_unit'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:50%;border:1px solid #ffffff;">No of Floors</td>
                                                        <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['numof_floor'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:50%;border:1px solid #ffffff;">Package Price</td>
                                                        <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['pack_price'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:50%;border:1px solid #ffffff;">Maintenance Charges</td>
                                                        <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['maintenance_charges'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:50%;border:1px solid #ffffff;">Project Tax</td>
                                                        <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['prop_tax'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:50%;border:1px solid #ffffff;">Transfer Charges</td>
                                                        <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['transfer_charge'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:50%;border:1px solid #ffffff;">Parking Charges</td>
                                                        <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['park_charge'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:50%;border:1px solid #ffffff;">Location</td>
                                                        <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['locality'].','.$projectdata[0]['city'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:50%;border:1px solid #ffffff;">Project Status</td>
                                                        <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['proj_status'].'</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>';

            // PAGE 3 (EXTENDED)
            $sql = "SELECT * from property as a LEFT JOIN project as b on a.project_id = b.project_id WHERE a.project_id = $project_id ORDER BY a.created_date DESC LIMIT 10";   
            $stmt = $db->getRows($sql);
                
            if($stmt->num_rows > 0)
            {

                $htmlstring .= '<li>
                                    <div class="slider">
                                        <div style="width:978px;height:728px;margin:20px;">
                                            <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                            <div style="text-align:center;">
                                                <div style="width:90%;float: left;font-size: 45px;color:#000000;background-color:#ffffff;">PROPERTIES
                                                </div>
                                                <div style="float:right;">
                                                    <img src="dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                                </div>
                                            </div>
                                            <div style="margin:100px;">
                                                <table style="font-size:16px;border:1px solid #000000;">
                                                    <thead>
                                                        <tr>
                                                            <th style="border:1px solid #000000;padding:10px;">Property ID</th>
                                                            <th style="border:1px solid #000000;padding:10px;">Title</th>
                                                            <th style="border:1px solid #000000;padding:10px;text-align:right;">Saleable Area</th>
                                                            <th style="border:1px solid #000000;padding:10px;text-align:right;">Carpet Area</th>
                                                            <th style="border:1px solid #000000;padding:10px;text-align:right;">Price</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>';
                while($row = $stmt->fetch_assoc())
                {
                    $htmlstring .= '                    <tr>
                                                            <td style="width:30px;border:1px solid #000000;padding:10px;">'.$row['proptype'].'_'.$row['property_id'].'</td>
                                                            <td style="width:250px;border:1px solid #000000;padding:10px;">'.$row['project_name'].' '.$row['propsubtype'].' '.$row['property_for'].'</td>
                                                            <td  style="width:30px;text-align:right;border:1px solid #000000;padding:10px;">'.$row['sale_area'].'</td>
                                                            <td  style="width:30px;text-align:right;border:1px solid #000000;padding:10px;">'.$row['carp_area'].'</td>
                                                            <td style="width:30px;text-align:right;border:1px solid #000000;padding:10px;">'.$row['exp_price'].'</td>
                                                        </tr>';
                }
                $htmlstring .= '                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </li>';
            }
            

            // PAGE 4
            $htmlstring .= '<li>
                                <div class="slider">
                                    <div style="width:978px;height:728px;margin:20px;">
                                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                        <div style="text-align:center;">
                                            <div style="width:90%;float: left;font-size: 45px;color:#000000;background-color:#ffffff;">PHOTOS
                                            </div>
                                            <div style="float:right;">
                                                <img src="dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                            </div>
                                        </div>
                                        <div style="margin:100px;">
                                            <table style="font-size:18px;">
                                                <tr>';

            $sql = "SELECT * from attachments WHERE category = 'project' and category_id = $project_id LIMIT 4";
            $stmt = $db->getRows($sql);
            $count = 1;
            if($stmt->num_rows > 0)
            {
                while($row = $stmt->fetch_assoc())
                {                          
            
                    $htmlstring .= '                <td style="width:50%;padding:10px;text-align:center;" valign="top">
                                                        <img src="api/v1/uploads/project/thumb/'.$row['filenames'].'" style="width:340px; height:228px;border:1px solid #000000;"/>
                                                        <p style="text-align:center;">'.$row['description'].'</p>
                                                    </td>';
                                                    $count ++;
                                                    if($count==3)
                                                    {
                                                        $htmlstring .= '
                                                </tr>
                                                <tr>';
                                                    }
                }
            }

            $htmlstring .= '                    </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </li>';

            // PAGE 5
            $htmlstring .= '<li>
                                <div class="slider">
                                    <div style="width:978px;height:728px;margin:20px;">
                                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                            <div style="text-align:center;">
                                                <div style="width:90%;float: left;font-size: 45px;color:#000000;background-color:#ffffff;">Location Map
                                                </div>
                                                <div style="float:right;">
                                                    <img src="dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                                    </div>
                                                </div>
                                                <div style="margin:100px;">
                                                    <img src="api/v1/uploads/project/'.$location_map.'"  style="width:752px; height:505px;" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>';

            // PAGE 6
            $htmlstring .= '<li>
                                <div class="slider">
                                    <div style="width:978px;height:728px;margin:20px;">
                                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;text-align:center;">
                                            <div style="margin-top:200px;margin-left:350px;text-align:center;">
                                                <img src="dist/img/sqftlogo1.jpg" style="width:251px; height:271px;" />
                                            </div>
                                            <div style="text-align:center;width:100%;margin-top:50px;">
                                                <div style="text-align:center;font-size:20px;font-weight:bold;width:80%;margin:0 auto;">
                                                    <p>402/403, Metro Avenue, Opp Guru Nanak Petrol Pump, Near Western Exp Highway, Andheri East, Mumbai-400 099.</p>
                                                    <p>Mob : 8879634179</p>
                                                    <p>Email Id  : <span style="color:#a898f0;">andheri@sqft.co.in</span></p>
                                                    <p>Website : <span style="color:#ee0a0a;">www.sqft.co.in</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>';
        }
    }

    if($module_name == 'property')
    {
        $sql = "SELECT *,a.exp_price, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in($data) GROUP BY a.property_id LIMIT 1 ";

        $propertydata = $db->getAllRecords($sql);
        $property_id = 0;
        if ($propertydata)
        {
            $property_id = $propertydata[0]['property_id'];
            $location_map = "location.jpg";
            
            // PAGE 1
            $htmlstring .= '<li>
                                <div class="slider">
                                    <div style="width:978px;height:728px;margin:20px;">
                                        <img src="dist/img/ppt_main.jpg" style="width:958px; height:708px;" />
                                    </div>
                                </div>
                            </li>';
            
            // PAGE 2
            $htmlstring .= '<li>
                                <div class="slider">
                                    <div style="width:978px;height:728px;margin:20px;">
                                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                            <div style="text-align:center;">
                                                <div style="width: 90%;float: left;font-size: 45px;">'.$propertydata[0]['building_plot'].'
                                                </div>
                                                <div style="float:right;">
                                                    <img src="dist/img/logo.png" style="width:79px; height:82px;"/>
                                                </div>
                                            </div>
                                        <div>
                                        <img src="api/v1/uploads/property/'.$propertydata[0]['filenames'].'"  style="width:752px; height:505px;margin:50px;margin-left:75px;" />
                                    </div>
                                </div>
                            </li>';
            
            // PAGE 4
            $sql = "SELECT * from attachments WHERE category = 'property' and category_id = $property_id LIMIT 6";
            $stmt = $db->getRows($sql);
            $count = 1;
            if($stmt->num_rows > 0)
            {
                while($row = $stmt->fetch_assoc())
                {  

                    $htmlstring .= '<li>
                                <div class="slider">
                                    <div style="width:978px;height:728px;margin:20px;">
                                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                            <div style="text-align:center;">
                                                <div style="width:90%;float: left;font-size: 45px;color:#000000;background-color:#ffffff;">PHOTOS</div>
                                                <div style="float:right;">
                                                    <img src="dist/img/logo.png" style="width:79px; height:82px;"/>
                                                </div>
                                            </div>
                                            <div style="margin:20px;">
                                                <table style="font-size:18px;">
                                                    <tr>
                                                    <td style="width:33%;padding:10px;text-align:center;" valign="top">
                                                            <img src="api/v1/uploads/property/thumb/'.$row['filenames'].'" style="width:290px; height:228px;border:1px solid #000000;"/>
                                                            <p style="text-align:center;">'.$row['description'].'</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>';
                }
            }
    

            // PAGE 5
            $htmlstring .= '<li>
                                <div class="slider">
                                    <div style="width:978px;height:728px;margin:20px;">
                                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                                            <div style="text-align:center;">
                                                <div style="width:90%;float: left;font-size: 45px;color:#000000;background-color:#ffffff;">Location Map</div>
                                                    <div style="float:right;">
                                                    <img src="dist/img/logo.png" style="width:79px; height:82px;"/>
                                                    </div>
                                                </div>
                                                <div style="margin:100px;">
                                                    <img src="api/v1/uploads/property/'.$location_map.'"  style="width:752px; height:505px;" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>';

            // PAGE 6
            $htmlstring .= '<li>
                                <div class="slider">
                                    <div style="width:978px;height:728px;margin:20px;">
                                        <img src="dist/img/thanks.jpg" style="width:958px; height:708px;" />
                                        <div style="margin-top:100px;font-size:18px;font-weight:bold;text-align:center;">
                                                <p>Assigned to :'.$propertydata[0]['assign_to'].'</p>
                                                <p>Email :'.$propertydata[0]['email'].'</p> 
                                                <p>Mob No :'.$propertydata[0]['usermobileno'].'</p> 
                                        </div>
                                    </div>
                                </div>
                            </li>';

        }
    }
    $htmlstring .= '</ul>';
    $htmldata['pages']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/export_report/:module_name/:id/:data/:report_type', function($module_name,$id,$data,$report_type) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $htmlstring = '';
    $filename = $module_name."_list.".$report_type;

    if ($report_type=="pptx")
    {
        require_once 'ppt.php';

        $ppt = new ppt();
        $a = $ppt->create_ppt($module_name,$id,$data,$filename);
    }
    $htmldata['htmlstring']='Done';
    $htmldata['htmlstring1']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/export_reportorg/:module_name/:id/:data/:report_type', function($module_name,$id,$data,$report_type) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $htmlstring = '';
    $filename = $module_name."_list.".$report_type;


if($module_name == 'project')
{
    $sql = "SELECT *, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date  from project as a  LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.project_id = f.category_id and f.category='project' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.developer_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.project_id in($data) GROUP BY a.project_id LIMIT 1 ";
    
    $projectdata = $db->getAllRecords($sql);
    $project_id = 0;
    if ($projectdata)
    {
        $project_id = $projectdata[0]['project_id'];
        $location_map = "location.jpg";

        $page_1 = '<div id="page_1" style="width:978px;height:728px;">
                        <img src="http://realtyhubpro.com/sqft/dist/img/sqft.jpg" style="width:958px; height:708px;" />
                   </div>';

        $page_2 =   '<div id="page_2" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                
                                <div style="width: 778px;float: left;font-size: 35px;">'.$projectdata[0]['project_name'].':'.$projectdata[0]['project_type'].' for '.$projectdata[0]['project_for'].'
                                </div>
                                <div style="float:right;">
                                    <img src="http://realtyhubpro.com//sqft//dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div>
                                <!--img src="http://realtyhubpro.com//sqft//api//v1//uploads//project//'.$projectdata[0]['filenames'].'" width="752" height="505" style="margin:50px;margin-left:75px;" /-->
                                <img src="http://realtyhubpro.com//sqft//api//v1//uploads//project//'.$projectdata[0]['filenames'].'" style="max-width: 752px; max-height: 505px;margin:50px;margin-left:75px;" />

                                

                            </div>
                        </div>
                    </div>';

        $page_3 =   '<div id="page_3" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                <div style="width:778px;float: left;font-size: 35px;color:#000000;background-color:#ffffff;">Commercial Terms
                                </div>
                                <div style="float:right;">
                                    <img src="http://realtyhubpro.com//sqft//dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div  style="margin:50px;">
                                <table style="background-color:#747272;color:#000000;font-size:30px;width:100%;border:1px solid #ffffff;">
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Total Area (sqft)</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['tot_area'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Total Unit (sqft)</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['tot_unit'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">No of Floors</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['numof_floor'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Package Price</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['pack_price'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Maintenance Charges</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['maintenance_charges'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Project Tax</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['prop_tax'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Transfer Charges</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['transfer_charge'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Parking Charges</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['park_charge'].'</td>

                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Location</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['locality'].','.$projectdata[0]['city'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Project Status</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['proj_status'].'</td>
                                </tr>
                                </table>
                            </div>
                        </div>
                    </div>';

        $page_4 =   '<div id="page_4" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                <div style="width:778px;float: left;font-size: 35px;color:#000000;background-color:#ffffff;">PHOTOS
                                </div>
                                <div style="float:right;">
                                    <img src="http://realtyhubpro.com//sqft//dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div style="margin:100px;">
                                <table style="font-size:18px;">
                                    <tr>';

        $sql = "SELECT * from attachments WHERE category = 'project' and category_id = $project_id LIMIT 4";
        $stmt = $db->getRows($sql);
        $count = 1;
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {                          
        
                $page_4 .=                      '<td style="width:50%;padding:10px;text-align:center;">
                                                    <img src="http://realtyhubpro.com//sqft//api/v1/uploads/project/'.$row['filenames'].'" style="width:340px; height:228px;border:1px solid #000000;"/>
                                                    <p style="text-align:center;">'.$row['description'].'</p>
                                                </td>';
                                                $count ++;
                                                if($count==3)
                                                {
                                                    $page_4 .='</tr><tr>';
                                                }
            }
        }

        $page_4 .=                    '</tr>
                                </table>
                            </div>
                        </div>
                    </div>';

        $page_5 =   '<div id="page_5" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                <div style="width:778px;float: left;font-size: 35px;color:#000000;background-color:#ffffff;">Location Map
                                </div>
                                <div style="float:right;">
                                    <img src="http://realtyhubpro.com//sqft//dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div style="margin:100px;">
                                <img src="http://realtyhubpro.com//sqft//api/v1/uploads/project/'.$location_map.'"  style="width:752px; height:505px;" />
                            </div>
                        </div>
                    </div>';

        $page_6 =   '<div id="page_6" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;text-align:center;">
                            <div style="margin-top:200px;text-align:center;">
                                <img src="http://realtyhubpro.com//sqft//dist/img/sqftlogo1.jpg" style="width:251px; height:271px;" />
                            </div>
                            <div style="text-align:center;width:978px;margin-top:50px;">
                                <div style="text-align:center;font-size:20px;font-weight:bold;width:80%;margin:0 auto;">
                                <p>Assigned to :'.$propertydata[0]['assign_to'].'</p>
                                <p>Email :'.$propertydata[0]['email'].'</p> 
                                <p>Mob No :'.$propertydata[0]['usermobileno'].'</p> 
                                </div>
                            </div>
                        </div>
                    </div>';
    }
}//if($module_name == 'project') ends


if($module_name == 'property')
{
    $sql = "SELECT *,a.exp_price, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in($data) GROUP BY a.property_id LIMIT 1 ";
    
    $propertydata = $db->getAllRecords($sql);
    $property_id = 0;
    $t_page = 1;
    if ($propertydata)
    {
        $property_id = $propertydata[0]['property_id'];
        $location_map = "location.jpg";

        $page_1 = '<div id="page_1" style="width:978px;height:728px;">
                        <img src="http://crm.rdbothers.com//dist/img/ppt_main.jpg" style="width:958px; height:708px;" />
                   </div>';

        $page_2 =   '<div id="page_2" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                
                                <div style="width: 778px;float: left;font-size: 25px;">'.$propertydata[0]['building_plot'].'                               </div>
                                <div style="float:right;">
                                    <img src="http://crm.rdbothers.com//dist/img/minilogo.png" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div>
                               <img src="http://crm.rdbothers.com//api//v1//uploads//property//'.$propertydata[0]['filenames'].'" style="max-width: 752px; max-height: 505px;margin:50px;margin-left:75px;" />
                            </div>
                        </div>
                    </div>';

        $sql = "SELECT * from attachments WHERE category = 'property' and category_id = $property_id LIMIT 4";
        $stmt = $db->getRows($sql);
        $count = 3;
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {   

                $page_4 =   '<div id="page_"'.$count. ' style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                <div style="width:778px;float: left;font-size: 35px;color:#000000;background-color:#ffffff;">PHOTOS
                                </div>
                                <div style="float:right;">
                                    <img src="http://crm.rdbothers.com//dist/img/minilogo.png" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div style="margin:100px;">
                                <table style="font-size:18px;">
                                    <tr>
                                        <td style="width:100%;padding:10px;text-align:center;">
                                            <img src="http://crm.rdbothers.com//api/v1/uploads/property/'.$row['filenames'].'" style="width:340px; height:228px;border:1px solid #000000;"/>
                                            <p style="text-align:center;">'.$row['description'].'</p>
                                        </td>
                                        </tr>
                                </table>
                            </div>
                        </div>
                    </div>';
                $count ++;
                if($count==3)
                {
                    $page_4 .='</tr><tr>';
                }
            }
        }

        $page_5 =   '<div id="page_5" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                <div style="width:778px;float: left;font-size: 35px;color:#000000;background-color:#ffffff;">Location Map
                                </div>
                                <div style="float:right;">
                                    <img src="http://crm.rdbothers.com//dist/img/logo.png" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div style="margin:100px;">
                                <img src="http://crm.rdbothers.com//api/v1/uploads/property/'.$location_map.'"  style="width:752px; height:505px;" />
                            </div>
                        </div>
                    </div>';

        /*$page_6 =   '<div id="page_6" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;text-align:center;">
                            <div style="margin-top:200px;text-align:center;">
                                <img src="http://crm.rdbothers.com//dist/img/logo.png" style="width:251px; height:271px;" />
                            </div>
                            <div style="text-align:center;width:978px;margin-top:50px;">
                                <div style="text-align:center;font-size:20px;font-weight:bold;width:80%;margin:0 auto;">
                                    
                                </div>
                            </div>
                        </div>
                    </div>';*/
        $page_6 = '<div id="page_6" style="width:978px;height:728px;">
                        <img src="http://crm.rdbothers.com//dist/img/thanks.jpg" style="width:958px; height:708px;" />
                   </div>';
    }
}//if($module_name == 'property') ends


    if ($report_type=="pdf")
    {

        $htmlstring = '<table><tr><td style="width:100%;">'.$page_1.'</td></tr></table>';
        $htmlstring .= '<table><tr><td style="width:100%;">'.$page_2.'</td></tr></table>';
        $htmlstring .= '<table><tr><td style="width:100%;">'.$page_3.'</td></tr></table>';
        $htmlstring .= '<table><tr><td style="width:100%;">'.$page_4.'</td></tr></table>';
        $htmlstring .= '<table><tr><td style="width:100%;">'.$page_5.'</td></tr></table>';
        $htmlstring .= '<table><tr><td style="width:100%;">'.$page_6.'</td></tr></table>';
        
        $htmlstring = $page_1.$page_2.$page_3.$page_4.$page_5.$page_6;

        
        include("mpdf60/mpdf.php");
        
        /*$stylesheet = file_get_contents('stylesheet.css');
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf = new mPDF('',    // mode - default ''
                         '',    // format - A4, for example, default ''
                         0,     // font size - default 0
                         '',    // default font family
                         15,    // margin_left
                         15,    // margin right
                         16,    // margin top
                         16,    // margin bottom
                         9,     // margin header
                         9,     // margin footer
                         'L');  // L - landscape, P - portrait*/
        
        
        $mpdf = new mPDF('', 'Letter-L', 0, '', 5, 5, 5, 1, 0, 0,'L');
        $mpdf->SetDisplayMode('fullwidth');
        //$mpdf->SetDisplayMode('fullpage');

        //$mpdf->SetFont("Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif");
        $htmlheader="";
        $htmlfooter="";

        $mpdf->SetHTMLHeader($htmlheader, 'O', true);
        $mpdf->SetFooter($htmlfooter);
        
        $mpdf->WriteHTML($htmlstring);
        $ds          = DIRECTORY_SEPARATOR;
        //$dir = $_SERVER['DOCUMENT_ROOT'];
        $dir = $_SERVER['SERVER_NAME'] ;//. dirname(__FILE__);
        
        $mpdf->Output('uploads'.$ds.'reports'.$ds.$filename);

    }
    
    if ($report_type=="xlsx")
    {
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
        $objWriter->save('uploads'.$ds.'reports'.$ds.$filename); 
        $stmt->close();
    }


    if ($report_type=="pptx")
    {
        require_once 'ppt.php';

        $ppt = new ppt();
        $a = $ppt->create_ppt($module_name,$id,$data,$filename);
    }

    $htmldata['htmlstring']='Done';
    $htmldata['htmlstring1']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/createreports1/:module_name/:id/:data', function($module_name,$id,$data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];

    $senddata =  array();
    $htmldata = array();
    $htmlstring = '';
    if($module_name == 'property')
    {
        $sql = "SELECT *,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor, SUBSTRING(i.f_name,1,1) as f_char,CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, , (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.property_id in ($data) and a.created_by = $user_id GROUP BY a.property_id ORDER BY a.modified_date DESC"; //CAST(a.property_id as UNSIGNED) DESC";

        if (in_array("Admin Level", $permissions))
        {

            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in ($data)  GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
        }

        if (in_array("Branch Head Level", $permissions))
        {
            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE  a.property_id in ($data)  and z.bo_id = $bo_id GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
        }

        if (in_array("Branch User Level", $permissions))
        {
            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE a.property_id in ($data)  and z.bo_id = $bo_id GROUP BY a.property_id ORDER BY a.modified_date DESC ";//CAST(a.property_id as UNSIGNED) DESC";
        }

        if (in_array("User Level", $permissions))
        {
            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE  a.property_id in ($data) and a.created_by = $user_id GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
        }
        /*$propertydata = $db->getAllRecords($sql);
        $property_id = 0;
        $slides = 1;
        if ($propertydata)
        {
            $property_id = $propertydata[0]['property_id'];
            $location_map = "location.jpg";

            // 1st and 2nd Slides
            $htmlstring .= '<div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3" style="height:500px;overflow-x:scroll;">
                                        <div class="row">
                                            <a href="javascript:void(0)" ng-click="show_slide_view(\'top_slide_view_\','.$slides.')">

                                                <div class="col-md-12 page_links" id="top_slide" >
                                                    <p>Top Slide</p>
                                                    <img class="page_links_img" src="dist/img/ppt_main.jpg"/>
                                                </div>
                                            </a>';
                                            $slides ++;

            $htmlstring .= '                <a href="javascript:void(0)" ng-click="show_slide_view(\'main_slide_view_\','.$slides.') ">
                                                <div class="col-md-12 page_links" id="main_slide" >
                                                    <p>'.$propertydata[0]['description'].'</p>
                                                    <img class="page_links_img" src="api/v1/uploads/property/'.$propertydata[0]['filenames'].'"/>
                                                </div>
                                            </a>';
                                            $slides ++;

            $htmlstring .= '                <a href="javascript:void(0)" ng-click="show_slide_view(\'details_slide_view_\','.$slides.') ">
                                            <div class="col-md-12 page_links" id="details_slide" >
                                                <p>Details Slide</p>
                                                <img class="page_links_img" src="dist/img/details.jpg"/>
                                            </div>
                                            </a>';
                                            $slides ++;
            
            
            // IMAGE SLIDES
            $innersql = "SELECT * from attachments WHERE category = 'property' and  category_id = $property_id and isdefault != 'true'";
            $innerstmt = $db->getRows($innersql);
            $count = 1;
            if($innerstmt->num_rows > 0)
            { 
                $image_count = 1;
                while($innerrow = $innerstmt->fetch_assoc())
                {           
                    if ($image_count==1)
                    {               
                        $htmlstring .= '    <div class="col-md-12 page_links" >
                                                <p><div class="checkbox hidden-xs hidden-sm"><label><input type="checkbox" class="check_element" name="assign_all" id="assign_all" ng-model="assign_all" ng-checked="true" style="width:20px;height:20px;float: left;" value="'.$innerrow['attachment_id'].'"/></label></div>
                                                    <lable>Slide No.</lable>
                                                    <input type="text"  id="slide_no_'.$slides.'" name="slide_no_'.$slides.'"  placeholder="Slide No " ng-model="slide_no_'.$slides.'" ng-init="slide_no_'.$slides.'='.$slides.'" style="margin-bottom:10px;"/>
                                                    
                                                    <input type="text" id="description_'.$slides.'" name="description_'.$slides.'" placeholder="File Title" ng-model="description_'.$slides.'" style="margin-bottom:10px;" ng-init="description_'.$slides.'='.$innerrow['description'].'"/>

                                                </p>';
                        $htmlstring .= '<a href="javascript:void(0)">ng-click="show_slide_view(\'image_slide_view_\','.$slides.')"';
                                            if ($innerrow['filenames'])
                                            {
                                                $htmlstring .= '<img class="page_links_img" src="api/v1/uploads/property/'.$innerrow['filenames'].'"/> ';
                                            }
                                            else{
                                                $htmlstring .= '<img  class="page_links_img" src="dist/img/no_image.jpg" />';
                                            }
                    }
                    if ($image_count==2)
                    {               
                    
                        if ($innerrow['filenames'])
                        {
                            $htmlstring .= '<img class="page_links_img" src="api/v1/uploads/property/'.$innerrow['filenames'].'"/> ';
                        }
                        else{
                            $htmlstring .= '<img  class="page_links_img" src="dist/img/no_image.jpg" />';
                        }
                                            
                        $htmlstring .= '</a></div>';
                    }
                    if ($image_count==2)
                    {
                        $image_count==1;
                        $slides ++;
                    }
                    else{
                        $image_count==2;
                    }
                    
                }
            }

            $htmlstring .= '            <a href="javascript:void(0)" ng-click="show_slide_view(\'location_slide_view_\','.$slides.') ">
                        <div class="col-md-12 page_links" style="margin-bottom:10px;" id="location_slide">
                            <p>Location</p>
                            <img class="page_links_img" src="dist/img/location.jpg"/>
                        </div>
                        </a>';
            $slides ++;
            $htmlstring .= '            <a href="javascript:void(0)" ng-click="show_slide_view(\'last_slide_view_\','.$slides.') ">
                        <div class="col-md-12 page_links" style="margin-bottom:10px;" id="last_slide"  >
                            <p>Last Slide</p>
                            <img class="page_links_img" src="dist/img/thanks.jpg"/>
                        </div>
                        </a>
                    </div>
                </div>
            </div>';
                /*
            $slides=1;
            $htmlstring .= '             <div class="col-md-9" style="height:500px;overflow-x:scroll;font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif; ">
                    <div class="col-md-12 page_view all" id="top_slide_view_'.$slides.'" >
                        <img class="page_view_img_full" src="dist/img/ppt_main.jpg"/>
                    </div>';
            $slides++;
            $htmlstring .= '             <div class="col-md-12 page_view all" id="main_slide_view_'.$slides.'" >
                        <div class="row">
                            <div class="col-md-9">
                                <p class="slide_heading">'.$propertydata[0]['description'].'</p>
                            </div>
                            <div class="col-md-3>">
                                <img class="mini_log_img" src="dist/img/mini_logo.png"/>
                            </div>
                        </div>
                        <hr>
                        <img class="page_view_img" src="api/v1/uploads/property/'.$propertydata[0]['filenames'].'"/>
                        <hr>
                        <div class="row">
                            <div class="col-md-9">
                                <p>www.rdbrothers.com</p>
                            </div>
                            <div class="col-md-3>">
                                <p style="font-size:16px;">BUILT ON EXPERIENCE<sup>TM</sup></p>
                            </div>
                        </div>
                    </div>';
            $slides++;
            $htmlstring .= '     <div class="col-md-12 page_view all" id="details_slide_view_'.$slides.'">
                        <div class="row">
                            <div class="col-md-9">
                                <p class="slide_heading">Commercial Terms</p>
                            </div>
                            <div class="col-md-3>">
                                <img class="mini_log_img" src="dist/img/mini_logo.png"/>
                            </div>
                            
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12" style="margin:0 auto;min-height:365px;">
                                <table style="width:80%;margin:0 auto;margin-top:15px;" cellspacing="0" cellpadding="0">
                                    <tr style="border:1px solid #000000;"><td style="width:50%;padding-left:10px;">Carpet Area (sqft)</td>
                                        <td style="width:50%;"><input type="text" id="carp_area" name="carp_area" placeholder="Carpet Area (sqft)" ng-model="carp_area"  ng-init="carp_area='.$propertydata[0]['carp_area'].'" /></td>
                                    </tr>
                                    <tr style="border:1px solid #000000;"><td style="width:50%;padding-left:10px;">Built Up Area (sqft)</td>
                                        <td style="width:50%;"><input type="text"  id="sale_area" name="sale_area"  placeholder="Built Up Area (sqft)" ng-model="sale_area" ng-init="sale_area='.$propertydata[0]['sale_area'].'" /></td>
                                    </tr>
                                    <tr style="border:1px solid #000000;"><td style="width:50%;padding-left:10px;">Proposed  Floors</td>
                                        <td style="width:50%;"><input type="text" id="floor" name="floor"  placeholder="Proposed  Floors" ng-model="floor"  ng-init="floor='.$propertydata[0]['floor'].'" /></td>
                                    </tr>
                                    <tr style="border:1px solid #000000;"><td style="width:50%;padding-left:10px;">Car Park</td>
                                        <td style="width:50%;"><input type="text" id="car_park" name="car_park"  placeholder="Car Park" ng-model="car_park"   ng-init="car_park='.$propertydata[0]['car_park'].'" /></td>
                                    </tr>
                                    <tr style="border:1px solid #000000;"><td style="width:50%;padding-left:10px;">Quoted Rent  </td>
                                        <td style="width:50%;"><input type="text"  id="price_unit" name="price_unit"  placeholder="Quoted Rent" ng-model="price_unit"   ng-init="price_unit='.$propertydata[0]['price_unit'].'" /></td>
                                    </tr>
                                    <tr style="border:1px solid #000000;"><td style="width:50%;padding-left:10px;">Security Deposit</td>
                                        <td style="width:50%;"><input type="text"  id="security_depo" name="security_depo"  placeholder="Security Deposit" ng-model="security_depo"   ng-init="security_depo='.$propertydata[0]['security_depo'].'" /></td>
                                    </tr>
                                    <tr style="border:1px solid #000000;"><td style="width:50%;padding-left:10px;">Lock in period  </td>
                                        <td style="width:50%;"><input type="text" id="lock_per" name="lock_per"  placeholder="Lock in period" ng-model="lock_per" ng-init="lock_per='.$propertydata[0]['lock_per'].'" /></td>
                                    </tr>
                                    <tr style="border:1px solid #000000;"><td style="width:50%;padding-left:10px;">Lease Tenure </td>
                                        <td style="width:50%;"><input type="text" id="lease_end" name="lease_end"  placeholder="Lease Tenure " ng-model="lease_end"  ng-init="lease_end=='.$propertydata[0]['lease_end'].'"/></td>
                                    </tr>
                                    <tr style="border:1px solid #000000;"><td style="width:50%;padding-left:10px;">Rent Escalation</td>
                                        <td style="width:50%;"><input type="text"  id="escalation_lease" name="escalation_lease"  placeholder="Rent Escalation" ng-model="escalation_lease"  ng-init="escalation_lease='.$propertydata[0]['escalation_lease'].'"/></td>
                                    </tr>
                                    <tr style="border:1px solid #000000;"><td style="width:50%;padding-left:10px;">Location</td>
                                        <td style="width:50%;"><input type="text" id="location" name="location"  placeholder="Location" ng-model="location"  ng-init="location='.$propertydata[0]['location'].'"/></td>
                                    </tr>
                                    <tr style="border:1px solid #000000;"><td style="width:50%;padding-left:10px;">Furnishing Details </td>
                                        <td style="width:50%;"><input type="text" id="furnishing" name="furnishing"  placeholder="Furnishing Details " ng-model="furnishing"  ng-init="furnishing='.$propertydata[0]['furnishing'].'"/></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-9">
                                <p>www.rdbrothers.com</p>
                            </div>
                            <div class="col-md-3>">
                                <p style="font-size:16px;">BUILT ON EXPERIENCE<sup>TM</sup></p>
                            </div>
                        </div>
                    </div>';
            $slides++;

            // IMAGE SLIDES
            $inner1sql = "SELECT * from attachments WHERE category = 'property' and  category_id = $property_id and isdefault != 'true'";
            $inner1stmt = $db->getRows($inner1sql);
            $count = 1;
            if($inner1stmt->num_rows > 0)
            {
                $image_count = 1;
                while($inner1row = $inner1stmt->fetch_assoc())
                { 
                    if ($image_count==1)
                    {
                        $htmlstring .= '   <div class="col-md-12 page_view all" id="image_slide_view_'.$slides.'">
                                    <div class="row">
                                        
                                        <div class="col-md-9">
                                            <p class="slide_heading">'.$inner1row['description'].'</p>
                                        </div>
                                        <div class="col-md-3>">
                                            <img class="mini_log_img" src="dist/img/mini_logo.png"/>
                                        </div>
                                        
                                    </div>
                                    <hr>';
                        $htmlstring .= '<div class="row">';
                        $htmlstring .= '<div class="col-md-6">';
                        if ($propertydata[0]['filenames'])
                        {
                            $htmlstring .= '   <img class="page_view_img" src="api/v1/uploads/property/'.$inner1row['filenames'].'"/>';
                        }
                        else{
                            $htmlstring .= '   <img  class="page_view_img" src="dist/img/no_image.jpg" />';
                        }
                        $htmlstring .= '</div>';
                    }
                    if ($image_count==2)
                    {
                        $htmlstring .= '<div class="col-md-6">';
                        if ($propertydata[0]['filenames'])
                        {
                            $htmlstring .= '   <img class="page_view_img" src="api/v1/uploads/property/'.$inner1row['filenames'].'"/>';
                        }
                        else{
                            $htmlstring .= '   <img  class="page_view_img" src="dist/img/no_image.jpg" />';
                        }
                        $htmlstring .= '</div>';
                        $htmlstring .= '</div>';
                        $htmlstring .= '               <hr>
                                    <div class="row" >
                                        
                                        <div class="col-md-9">
                                            <p>www.rdbrothers.com</p>
                                        </div>
                                        <div class="col-md-3>">
                                            <p style="font-size:16px;">BUILT ON EXPERIENCE<sup>TM</sup></p>
                                        </div>
                                    </div>
                                </div>';
                        $slides++;
                    }
                    if ($image_count==1)
                    {
                        $image_count==2;
                    }
                    else{
                        $image_count==1;
                    }
                }
            }
            $htmlstring .= '       <div class="col-md-12 page_view all" id="location_slide_view_'.$slides.'" >
                        <div class="row">
                            <div class="col-md-9">
                                <p class="slide_heading">Location</p>
                            </div>
                            <div class="col-md-3>">
                                <img class="mini_log_img" src="dist/img/mini_logo.png"/>
                            </div>
                            
                        </div>
                        <hr>
                        <div id="map" class="page_view_img" ></div>
                        <div id="address"></div>
                        <hr>
                        <div class="row">
                            <div class="col-md-9">
                                <p>www.rdbrothers.com</p>
                            </div>
                            <div class="col-md-3>">
                                <p style="font-size:16px;">BUILT ON EXPERIENCE <sup>TM</sup></p>
                            </div>
                        </div>
                    </div>';
            $slides++;

            $htmlstring .= '       
                    <div class="col-md-12 page_view all" id="last_slide_view_'.$slides.'" style="background-image:url(\'dist/img/thanks.jpg\');background-repeat: no-repeat;background-size:100% 100%;" >
                        <!--img class="page_view_img_full" src="dist/img/thanks.jpg"/-->
                        <div style="margin:0 auto;width:95%;margin-top:45%;text-align:center;font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif; ">
                            <p style="font-size:24px;margin-bottom:0px;line-height:1.1;">'.$propertydata[0]['owner_name'].'</p>
                            <p style="font-size:20px;margin-bottom:0px;line-height:1.1;">Call:-'.$propertydata[0]['mob_no'].'</p>
                            <p style="font-size:20px;margin-bottom:0px;line-height:1.1;">Email:-'.$propertydata[0]['email'].'</p>
                        </div>
                    </div>
                </div>';
        }
    }
    $htmlstring .='<script>$("#top_slide_view_1").css("display","block");</script>';
    $htmldata['pages']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);*/


    }

});


$app->get('/newexport_report/:module_name/:id/:data/:report_type', function($module_name,$id,$data,$report_type) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $htmlstring = '';
    $filename = $module_name."_list.".$report_type;


if($module_name == 'project')
{
    $sql = "SELECT *, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date  from project as a  LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.project_id = f.category_id and f.category='project' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.developer_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.project_id in($data) GROUP BY a.project_id LIMIT 1 ";
    
    $projectdata = $db->getAllRecords($sql);
    $project_id = 0;
    if ($projectdata)
    {
        $project_id = $projectdata[0]['project_id'];
        $location_map = "location.jpg";

        $page_1 = '<div id="page_1" style="width:978px;height:728px;">
                        <img src="http://realtyhubpro.com/sqft/dist/img/sqft.jpg" style="width:958px; height:708px;" />
                   </div>';

        $page_2 =   '<div id="page_2" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                
                                <div style="width: 778px;float: left;font-size: 35px;">'.$projectdata[0]['project_name'].':'.$projectdata[0]['project_type'].' for '.$projectdata[0]['project_for'].'
                                </div>
                                <div style="float:right;">
                                    <img src="http://realtyhubpro.com//sqft//dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div>
                                <!--img src="http://realtyhubpro.com//sqft//api//v1//uploads//project//'.$projectdata[0]['filenames'].'" width="752" height="505" style="margin:50px;margin-left:75px;" /-->
                                <img src="http://realtyhubpro.com//sqft//api//v1//uploads//project//'.$projectdata[0]['filenames'].'" style="max-width: 752px; max-height: 505px;margin:50px;margin-left:75px;" />

                                

                            </div>
                        </div>
                    </div>';

        $page_3 =   '<div id="page_3" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                <div style="width:778px;float: left;font-size: 35px;color:#000000;background-color:#ffffff;">Commercial Terms
                                </div>
                                <div style="float:right;">
                                    <img src="http://realtyhubpro.com//sqft//dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div  style="margin:50px;">
                                <table style="background-color:#747272;color:#000000;font-size:30px;width:100%;border:1px solid #ffffff;">
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Total Area (sqft)</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['tot_area'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Total Unit (sqft)</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['tot_unit'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">No of Floors</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['numof_floor'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Package Price</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['pack_price'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Maintenance Charges</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['maintenance_charges'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Project Tax</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['prop_tax'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Transfer Charges</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['transfer_charge'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Parking Charges</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['park_charge'].'</td>

                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Location</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['locality'].','.$projectdata[0]['city'].'</td>
                                </tr>
                                <tr>
                                <td style="width:50%;border:1px solid #ffffff;">Project Status</td>
                                <td style="width:50%;border:1px solid #ffffff;">'.$projectdata[0]['proj_status'].'</td>
                                </tr>
                                </table>
                            </div>
                        </div>
                    </div>';

        $page_4 =   '<div id="page_4" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                <div style="width:778px;float: left;font-size: 35px;color:#000000;background-color:#ffffff;">PHOTOS
                                </div>
                                <div style="float:right;">
                                    <img src="http://realtyhubpro.com//sqft//dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div style="margin:100px;">
                                <table style="font-size:18px;">
                                    <tr>';

        $sql = "SELECT * from attachments WHERE category = 'project' and category_id = $project_id LIMIT 4";
        $stmt = $db->getRows($sql);
        $count = 1;
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {                          
        
                $page_4 .=                      '<td style="width:50%;padding:10px;text-align:center;">
                                                    <img src="http://realtyhubpro.com//sqft//api/v1/uploads/project/'.$row['filenames'].'" style="width:340px; height:228px;border:1px solid #000000;"/>
                                                    <p style="text-align:center;">'.$row['description'].'</p>
                                                </td>';
                                                $count ++;
                                                if($count==3)
                                                {
                                                    $page_4 .='</tr><tr>';
                                                }
            }
        }

        $page_4 .=                    '</tr>
                                </table>
                            </div>
                        </div>
                    </div>';

        $page_5 =   '<div id="page_5" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                <div style="width:778px;float: left;font-size: 35px;color:#000000;background-color:#ffffff;">Location Map
                                </div>
                                <div style="float:right;">
                                    <img src="http://realtyhubpro.com//sqft//dist/img/sqftlogo1.jpg" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div style="margin:100px;">
                                <img src="http://realtyhubpro.com//sqft//api/v1/uploads/project/'.$location_map.'"  style="width:752px; height:505px;" />
                            </div>
                        </div>
                    </div>';

        $page_6 =   '<div id="page_6" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;text-align:center;">
                            <div style="margin-top:200px;text-align:center;">
                                <img src="http://realtyhubpro.com//sqft//dist/img/sqftlogo1.jpg" style="width:251px; height:271px;" />
                            </div>
                            <div style="text-align:center;width:978px;margin-top:50px;">
                                <div style="text-align:center;font-size:20px;font-weight:bold;width:80%;margin:0 auto;">
                                <p>Assigned to :'.$propertydata[0]['assign_to'].'</p>
                                <p>Email :'.$propertydata[0]['email'].'</p> 
                                <p>Mob No :'.$propertydata[0]['usermobileno'].'</p> 
                                </div>
                            </div>
                        </div>
                    </div>';
    }
}//if($module_name == 'project') ends


if($module_name == 'property')
{
    $sql = "SELECT *,a.exp_price, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in($data) GROUP BY a.property_id LIMIT 1 ";
    
    $propertydata = $db->getAllRecords($sql);
    $property_id = 0;
    $t_page = 1;
    if ($propertydata)
    {
        $property_id = $propertydata[0]['property_id'];
        $location_map = "location.jpg";

        $page_1 = '<div id="page_1" style="width:978px;height:728px;">
                        <img src="http://crm.rdbothers.com//dist/img/ppt_main.jpg" style="width:958px; height:708px;" />
                   </div>';

        $page_2 =   '<div id="page_2" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                
                                <div style="width: 778px;float: left;font-size: 25px;">'.$propertydata[0]['building_plot'].'                               </div>
                                <div style="float:right;">
                                    <img src="http://crm.rdbothers.com//dist/img/minilogo.png" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div>
                               <img src="http://crm.rdbothers.com//api//v1//uploads//property//'.$propertydata[0]['filenames'].'" style="max-width: 752px; max-height: 505px;margin:50px;margin-left:75px;" />
                            </div>
                        </div>
                    </div>';

        $sql = "SELECT * from attachments WHERE category = 'property' and category_id = $property_id LIMIT 4";
        $stmt = $db->getRows($sql);
        $count = 3;
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {   

                $page_4 =   '<div id="page_"'.$count. ' style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                <div style="width:778px;float: left;font-size: 35px;color:#000000;background-color:#ffffff;">PHOTOS
                                </div>
                                <div style="float:right;">
                                    <img src="http://crm.rdbothers.com//dist/img/minilogo.png" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div style="margin:100px;">
                                <table style="font-size:18px;">
                                    <tr>
                                        <td style="width:100%;padding:10px;text-align:center;">
                                            <img src="http://crm.rdbothers.com//api/v1/uploads/property/'.$row['filenames'].'" style="width:340px; height:228px;border:1px solid #000000;"/>
                                            <p style="text-align:center;">'.$row['description'].'</p>
                                        </td>
                                        </tr>
                                </table>
                            </div>
                        </div>
                    </div>';
                $count ++;
                if($count==3)
                {
                    $page_4 .='</tr><tr>';
                }
            }
        }

        $page_5 =   '<div id="page_5" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;">
                            <div style="text-align:center;">
                                <div style="width:778px;float: left;font-size: 35px;color:#000000;background-color:#ffffff;">Location Map
                                </div>
                                <div style="float:right;">
                                    <img src="http://crm.rdbothers.com//dist/img/logo.png" style="width:79px; height:82px;"/>
                                </div>
                            </div>
                            <div style="margin:100px;">
                                <img src="http://crm.rdbothers.com//api/v1/uploads/property/'.$location_map.'"  style="width:752px; height:505px;" />
                            </div>
                        </div>
                    </div>';

        /*$page_6 =   '<div id="page_6" style="width:978px;height:728px;">
                        <div style="border:5px solid #ee0a0a;width:968px; height:718px;text-align:center;">
                            <div style="margin-top:200px;text-align:center;">
                                <img src="http://crm.rdbothers.com//dist/img/logo.png" style="width:251px; height:271px;" />
                            </div>
                            <div style="text-align:center;width:978px;margin-top:50px;">
                                <div style="text-align:center;font-size:20px;font-weight:bold;width:80%;margin:0 auto;">
                                    
                                </div>
                            </div>
                        </div>
                    </div>';*/
        $page_6 = '<div id="page_6" style="width:978px;height:728px;">
                        <img src="http://crm.rdbothers.com//dist/img/thanks.jpg" style="width:958px; height:708px;" />
                   </div>';
    }
}//if($module_name == 'property') ends


    if ($report_type=="pdf")
    {

        $htmlstring = '<table><tr><td style="width:100%;">'.$page_1.'</td></tr></table>';
        $htmlstring .= '<table><tr><td style="width:100%;">'.$page_2.'</td></tr></table>';
        $htmlstring .= '<table><tr><td style="width:100%;">'.$page_3.'</td></tr></table>';
        $htmlstring .= '<table><tr><td style="width:100%;">'.$page_4.'</td></tr></table>';
        $htmlstring .= '<table><tr><td style="width:100%;">'.$page_5.'</td></tr></table>';
        $htmlstring .= '<table><tr><td style="width:100%;">'.$page_6.'</td></tr></table>';
        
        $htmlstring = $page_1.$page_2.$page_3.$page_4.$page_5.$page_6;

        
        include("mpdf60/mpdf.php");
        
        /*$stylesheet = file_get_contents('stylesheet.css');
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf = new mPDF('',    // mode - default ''
                         '',    // format - A4, for example, default ''
                         0,     // font size - default 0
                         '',    // default font family
                         15,    // margin_left
                         15,    // margin right
                         16,    // margin top
                         16,    // margin bottom
                         9,     // margin header
                         9,     // margin footer
                         'L');  // L - landscape, P - portrait*/
        
        
        $mpdf = new mPDF('', 'Letter-L', 0, '', 5, 5, 5, 1, 0, 0,'L');
        $mpdf->SetDisplayMode('fullwidth');
        //$mpdf->SetDisplayMode('fullpage');

        //$mpdf->SetFont("Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif");
        $htmlheader="";
        $htmlfooter="";

        $mpdf->SetHTMLHeader($htmlheader, 'O', true);
        $mpdf->SetFooter($htmlfooter);
        
        $mpdf->WriteHTML($htmlstring);
        $ds          = DIRECTORY_SEPARATOR;
        //$dir = $_SERVER['DOCUMENT_ROOT'];
        $dir = $_SERVER['SERVER_NAME'] ;//. dirname(__FILE__);
        
        $mpdf->Output('uploads'.$ds.'reports'.$ds.$filename);

    }
    
    if ($report_type=="xlsx")
    {
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
        $objWriter->save('uploads'.$ds.'reports'.$ds.$filename); 
        $stmt->close();
    }


    if ($report_type=="pptx")
    {
        require_once 'ppt.php';

        $ppt = new ppt();
        $a = $ppt->create_ppt($module_name,$id,$data,$filename);
    }

    $htmldata['htmlstring']='Done';
    $htmldata['htmlstring1']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

?>