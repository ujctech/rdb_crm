<?php  
// ENQUIRY
//pks commented  apply new code 

// $app->get('/enquiries_list_ctrl/:cat/:id/:next_page_id', function($cat,$id,$next_page_id) use ($app) {
//     $sql  = ""; 
//     $db = new DbHandler();
//     $session = $db->getSession();
//     if ($session['username']=="Guest")
//     {
//         return;
//     }
//     $bo_id = $session['bo_id'];
//     $teams = $session['teams'];
//     $team_query = " ";   
//     $tteams = $session['teams'];
//     foreach($tteams as $value)
//     {
//         $team_query .= " OR a.teams LIKE '".$value."' ";
//     }
//     $user_id = $session['user_id'];
//     $permissions = $session['permissions'];
//     $role = $session['role'];
            
//     $sql="SELECT *  from enquiry LIMIT 0";
//     $countsql = "SELECT count(*) as enquiry_count from enquiry";
//     if ($id==0)
//     {
//         if (in_array("Admin", $role))
//         {
    
//           $sql="SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name,b.company_name, b.email as client_email, b.mob_no as client_mob,b.alt_phone_no as client_mob_alt, SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name,c.company_name as brokerc_name, c.email as broker_email, c.mob_no as broker_mob,c.alt_phone_no as broker_mob_alt,e.locality as preferred_locality, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.modified_by) as modified_by,(SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id where a.enquiry_off = '$cat'  ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";
//             $countsql = "SELECT count(*)  as enquiry_count from enquiry WHERE enquiry_off = '$cat' ";
//         }
//         else
//         {
//              $sql="SELECT *,  SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.company_name, b.email as client_email, b.mob_no as client_mob,b.alt_phone_no as client_mob_alt,SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name,c.company_name as brokerc_name, c.email as broker_email, c.mob_no as broker_mob,e.locality as preferred_locality, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.modified_by) as modified_by, (SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id WHERE a.enquiry_off = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assigned) ".$team_query." ) ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";
//             // $countsql = "SELECT count(*)  as enquiry_count from enquiry WHERE enquiry_off = '$cat' and (created_by = $user_id or FIND_IN_SET ($user_id ,assigned) ".$team_query." )";
//         $countsql = "SELECT count(*)  as enquiry_count from enquiry WHERE enquiry_off = '$cat' and (created_by = $user_id or FIND_IN_SET ($user_id ,assigned) )";
            
//         }                                                                   
//     }
//     else
//     {
//         $property_for = "";
//         $property_type = "";
//         $project_id = 0;
//         $area_id = 0;
//         $exp_price = 0;
//         $bedrooms = 0;
//         $bathrooms = 0;
    
//         $propertydata = $db->getAllRecords("SELECT * FROM property where property_id = $id");
        
//         if ($propertydata)
//         {
//             $property_for = $propertydata[0]['property_for'];
//             if ($property_for == 'Sale')
//             {
//                 $property_for = 'Buy';
//             }
//             if ($property_for == 'Rent/Lease')
//             {
//                 $property_for = 'Lease';
//             }
//             $property_type = $propertydata[0]['propsubtype'];
//             $project_id = $propertydata[0]['project_id'];
//             $area_id = $propertydata[0]['area_id'];
//             $exp_price = $propertydata[0]['exp_price'];
//             $bedrooms = $propertydata[0]['bedrooms'];
//             $bathrooms = $propertydata[0]['bathrooms'];
//         }
    
//         $bo_id = $session['bo_id'];
//         $role = $session['role'];
    
//         $ifAdmin = false;
//         if (in_array("Admin", $role))
//         {
//             $ifAdmin = true; 
//         }
    
//         /*$sql = "SELECT *,SUBSTRING(b.f_name,1,1) as client_first_char,CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,SUBSTRING(g.f_name,1,1) as broker_first_char, CONCAT(g.name_title,' ',g.f_name,' ',g.l_name) as broker_name, g.email as broker_email, g.mob_no as broker_mob, e.locality as preferred_locality, f.area_name as preferred_area  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as g on a.broker_id = g.contact_id and g.contact_off = 'broker' LEFT JOIN users as c on a.assigned = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id ";
          
//         if (!$ifAdmin)
//         {
//           $sql .= " LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id ";
//         }
    
//         $sql .= " WHERE ";*/
//         $tsql_matching = "  ";
//         $temp_sql = "SELECT * FROM matching WHERE (property_id = $id) " ;

//         $temp_stmt = $db->getRows($temp_sql);
    
//         if($temp_stmt->num_rows > 0)
//         {
//             while($temp_row = $temp_stmt->fetch_assoc())
//             {
//                 $tsql_matching .= " OR a.enquiry_id = ".$temp_row['enquiry_id']." ";
//             }
//         }



//         $tsql = " ( ";
//         $insert_and = "No";
//         if ($property_for !="")
//         {
//             $tsql .= " a.enquiry_for = '$property_for' ";
//             $insert_and = "Yes";
//         }
    
//         if ($property_type !="")
//         {
//             if ($insert_and=="Yes")
//             {
//                 $tsql .= " and ";
//             }        
//             $tsql .= " a.enquiry_type = '$property_type' ";
//             $insert_and = "Yes";
//         }
    
//         if ($project_id !=0)
//         {
//             if ($insert_and == "Yes")
//             {
//                 $tsql .= " and ";
//             }
//             $tsql .= " a.preferred_project_id  = $project_id ";
//             $insert_and = "Yes";
//         }
        
    
//         if ($area_id !=0)
//         {
//             if ($insert_and == "Yes")
//             {
//                 $tsql .= " and ";
//             }
//             $tsql .= " a.preferred_area_id  = $area_id  ";
//             $insert_and = "Yes";
//         }
    
//         if ($exp_price >0)
//         {
//             if ($insert_and == "Yes")
//             {
//                 $tsql .= " and ";
//             }
    
//             $tsql .= " ( $exp_price>=a.budget_range1 and $exp_price<=a.budget_range2) ";
//             $insert_and = "Yes";
//         }
//         $tsql .= " ) ";




//         if (in_array("Admin", $role))
//         {
    
//             $sql="SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob, SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name, c.email as broker_email, c.mob_no as broker_mob,e.locality as preferred_locality,(SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id where ".$tsql." ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";

//             $countsql = "SELECT count(*)  as enquiry_count from enquiry as a WHERE ".$tsql;
//         }
//         else
//         {
//             $sql="SELECT *,  SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name, c.email as broker_email, c.mob_no as broker_mob,e.locality as preferred_locality, (SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id WHERE ".$tsql." and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assigned) ".$team_query." ) ".$tsql_matching." ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";

//             $countsql = "SELECT count(*)  as enquiry_count from enquiry as a WHERE  ".$tsql." and (created_by = $user_id or FIND_IN_SET ($user_id ,assigned) ".$team_query." ) OR ".$tsql_matching." ";
        
//         }
        
        
        
//         /*if ($bedrooms >0)
//         {
//             $sql .= " ( a.bedrooms = $bedrooms) and ";
//         }
    
//         if ($bathrooms >0)
//         {
//             $sql .= " ( a.bath = $bathrooms)  and ";
//         }
        
//         $sql .= " a.status = 'Active' ";*/
    
//         /*if (!$ifAdmin)
//         {
//             if ($insert_and == "Yes")
//             {
//                 $sql .= " and ";
//             }
//             $sql .= " z.bo_id = $bo_id ";
//         }
    
//         $sql .= " ORDER BY a.enquiry_for"; */


//     }

//     $enquiries = $db->getAllRecords($sql);
//     $enquirycountdata = $db->getAllRecords($countsql);
//     $enquiry_count = 0;
//     if ($enquirycountdata)
//     {
//          $enquiry_count = $enquirycountdata[0]['enquiry_count'];

//     }
//     if ($enquiry_count>0)
//     {
//         $enquiries[0]['enquiry_count']=$enquiry_count;
//     }
//     //$enquiries[0]['sql']=$tsql_matching;
//     echo json_encode($enquiries);
// });

// pks start

$app->get('/enquiries_list_ctrl/:cat/:id/:next_page_id', function($cat,$id,$next_page_id) use ($app) {
    $sql  = ""; 
    $users = array();
    
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $enquiries =array();
    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $user_id1 = $session['user_id'];
    $permissions = $session['permissions'];
    $role = $session['role'];
    $users = array($user_id1);
    $emp_ids = $db->getOneRecord("SELECT emp_id FROM users WHERE user_id = $user_id1");
    $emp_id = 0;
    foreach($emp_ids as $emp){
        $emp_id = $emp;
    }
    $result = $db->getAllRecords("SELECT emp_id FROM employee WHERE manager_id = $emp_id");
    
    foreach ($result as $item) { 
        $datac =  $item['emp_id'];
        $result1 = $db->getAllRecords("SELECT user_id FROM users WHERE emp_id = $datac");
        foreach ($result1 as $item1) { 
        $users[] = $item1['user_id'];
    }
}
        // error_log(print_r($users, true), 3, "logfile.log");
    foreach($users as $user_id){
        // error_log($user_id, 3, "logfile.log");
    $sql="SELECT *  from enquiry LIMIT 0";
    $countsql = "SELECT count(*) as enquiry_count from enquiry";
    if ($id==0)
    {
        if (in_array("Admin", $role) || in_array("Sub Admin", $role))
        {
    
           $sql="SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name,b.company_name, b.email as client_email, b.mob_no as client_mob,b.alt_phone_no as client_mob_alt, SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name,c.company_name as brokerc_name, c.email as broker_email, c.mob_no as broker_mob,c.alt_phone_no as broker_mob_alt,e.locality as preferred_locality, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.modified_by) as modified_by,(SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id where a.enquiry_off = '$cat'  ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";
            $countsql = "SELECT count(*)  as enquiry_count from enquiry WHERE enquiry_off = '$cat' ";
        }
        else
        {
             $sql="SELECT *,  SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.company_name, b.email as client_email, b.mob_no as client_mob,b.alt_phone_no as client_mob_alt,SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name,c.company_name as brokerc_name, c.email as broker_email, c.mob_no as broker_mob,e.locality as preferred_locality, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.modified_by) as modified_by, (SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id WHERE a.enquiry_off = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assigned)) ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";

            //  ($user_id ,a.assigned) ".$team_query." ) ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";
            // $countsql = "SELECT count(*)  as enquiry_count from enquiry WHERE enquiry_off = '$cat' and (created_by = $user_id or FIND_IN_SET ($user_id ,assigned) ".$team_query." )";
        $countsql = "SELECT count(*)  as enquiry_count from enquiry WHERE enquiry_off = '$cat' and (created_by = $user_id or FIND_IN_SET ($user_id ,assigned) )";
            
        }                                                                   
    }
    else
    {
        $property_for = "";
        $property_type = "";
        $project_id = 0;
        $area_id = 0;
        $exp_price = 0;
        $bedrooms = 0;
        $bathrooms = 0;
    
        $propertydata = $db->getAllRecords("SELECT * FROM property where property_id = $id");
        
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
        $role = $session['role'];
    
        $ifAdmin = false;
        if (in_array("Admin", $role) || in_array("Sub Admin", $role))
        {
            $ifAdmin = true; 
        }
    
        /*$sql = "SELECT *,SUBSTRING(b.f_name,1,1) as client_first_char,CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,SUBSTRING(g.f_name,1,1) as broker_first_char, CONCAT(g.name_title,' ',g.f_name,' ',g.l_name) as broker_name, g.email as broker_email, g.mob_no as broker_mob, e.locality as preferred_locality, f.area_name as preferred_area  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as g on a.broker_id = g.contact_id and g.contact_off = 'broker' LEFT JOIN users as c on a.assigned = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id ";
          
        if (!$ifAdmin)
        {
           $sql .= " LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id ";
        }
    
        $sql .= " WHERE ";*/
        $tsql_matching = "  ";
        $temp_sql = "SELECT * FROM matching WHERE (property_id = $id) " ;

        $temp_stmt = $db->getRows($temp_sql);
    
        if($temp_stmt->num_rows > 0)
        {
            while($temp_row = $temp_stmt->fetch_assoc())
            {
                $tsql_matching .= " OR a.enquiry_id = ".$temp_row['enquiry_id']." ";
            }
        }



        $tsql = " ( ";
        $insert_and = "No";
        if ($property_for !="")
        {
            $tsql .= " a.enquiry_for = '$property_for' ";
            $insert_and = "Yes";
        }
    
        if ($property_type !="")
        {
            if ($insert_and=="Yes")
            {
                $tsql .= " and ";
            }        
            $tsql .= " a.enquiry_type = '$property_type' ";
            $insert_and = "Yes";
        }
    
        if ($project_id !=0)
        {
            if ($insert_and == "Yes")
            {
                $tsql .= " and ";
            }
            $tsql .= " a.preferred_project_id  = $project_id ";
            $insert_and = "Yes";
        }
        
    
        if ($area_id !=0)
        {
            if ($insert_and == "Yes")
            {
                $tsql .= " and ";
            }
            $tsql .= " a.preferred_area_id  = $area_id  ";
            $insert_and = "Yes";
        }
    
        if ($exp_price >0)
        {
            if ($insert_and == "Yes")
            {
                $tsql .= " and ";
            }
    
            $tsql .= " ( $exp_price>=a.budget_range1 and $exp_price<=a.budget_range2) ";
            $insert_and = "Yes";
        }
        $tsql .= " ) ";




        if (in_array("Admin", $role) || in_array("Sub Admin", $role))
        {
    
            $sql="SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob, SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name, c.email as broker_email, c.mob_no as broker_mob,e.locality as preferred_locality,(SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id where ".$tsql." ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";

            $countsql = "SELECT count(*)  as enquiry_count from enquiry as a WHERE ".$tsql;
        }
        else
        {
            $sql="SELECT *,  SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name, c.email as broker_email, c.mob_no as broker_mob,e.locality as preferred_locality, (SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id WHERE ".$tsql." and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assigned)) ".$tsql_matching." ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";

            $countsql = "SELECT count(*)  as enquiry_count from enquiry as a WHERE  ".$tsql." and (created_by = $user_id or FIND_IN_SET ($user_id ,assigned) ".$team_query." ) OR ".$tsql_matching." ";
        
        }
        
        
        
        /*if ($bedrooms >0)
        {
            $sql .= " ( a.bedrooms = $bedrooms) and ";
        }
    
        if ($bathrooms >0)
        {
            $sql .= " ( a.bath = $bathrooms)  and ";
        }
        
        $sql .= " a.status = 'Active' ";*/
    
        /*if (!$ifAdmin)
        {
            if ($insert_and == "Yes")
            {
                $sql .= " and ";
            }
            $sql .= " z.bo_id = $bo_id ";
        }
    
        $sql .= " ORDER BY a.enquiry_for"; */


    }
    
    // error_log($user_id, 3, "logfile.log");
    $cde = $db->getAllRecords($sql);
    foreach($cde as $cd){
        $enquiries[] = $cd;
    }
    // array_push($enquiries,$cde);
   
    // error_log(print_r($enquiries, true), 3, "logfile.log");
    $enquirycountdata = $db->getAllRecords($countsql);
    $enquiry_count = 0;
    if ($enquirycountdata)
    {
         $enquiry_count = $enquirycountdata[0]['enquiry_count'];

    }
    if ($enquiry_count>0)
    {
        $enquiries[0]['enquiry_count']=$enquiry_count;
    }
    
}
    //$enquiries[0]['sql']=$tsql_matching;
    echo json_encode($enquiries);
});

//pks end

$app->get('/enquiryupdatestatus/:value/:enquiry_id', function($value, $enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE enquiry set status = '$value' where enquiry_id = $enquiry_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
});


$app->get('/pmatchingproperties/:enquiry_id', function($enquiry_id) use ($app) {
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
					    <th>Property ID</th>
                                            <th>Title</th>
                                            <th>Saleable Area</th>
                                            <th>Carpet Area</th>
                                            <th>Plot Area</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

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

        $sql = "SELECT * from property as a LEFT JOIN project as b on a.project_id = b.project_id";

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
            if ($budget_range1==0)
            {
                $budget_range1 = $budget_range2;
            }
            if ($budget_range2==0)
            {
                $budget_range2 = $budget_range1;
            }
            $sql .= " ( a.exp_price >=  $budget_range1 and a.exp_price <= $budget_range2) ";
        }
        
        /*if ($bedrooms >0)
        {
            $sql .= " ( a.bedrooms = $bedrooms) and ";
        }
    
        if ($bath >0)
        {
            $sql .= " ( a.bathrooms = $bath) and ";
        }*/
        if (!$ifAdmin)
	    {
        	$sql .= " z.bo_id = $bo_id and ";
        }

	    //$sql .= " a.deal_done != 'Yes' ";

        $sql .= " ORDER BY a.property_for"; 
        $stmt = $db->getRows($sql);
    
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            {
                $htmlstring .= '<tr>
				    <td><a href="#/properties_edit/'.$row['property_id'].'">'.$row['proptype'].'_'.$row['property_id'].'</a></td>
                                    <td>'.$row['project_name'].' '.$row['propsubtype'].' '.$row['property_for'].'</td>
                                    <td>'.$row['sale_area'].'</td>
                                    <td>'.$row['carp_area'].'</td>
                                    <td>'.$row['exp_price'].'</td>
                                    <td>'.$row['exp_price'].'</td>
                                </tr>';
            }
        }
        $htmlstring .= '</tbody>
                    </table>    
                </div> 
            </div>';
    
        $htmldata['htmlstring']=$htmlstring;
        $htmldata['sql']=$sql;
        $senddata[]=$htmldata;
        echo json_encode($senddata);
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
    $created_date = date('Y-m-d H:i:s');
    $modified_date = $created_date;
    $assign_to = $r->enquiry->assigned;
    if (isset($r->enquiry->old_data))
    {
        if ($r->enquiry->old_data=="Yes")
        {
            $created_date = date('2020-12-31 10:55:23');
        }
    }

    $r->enquiry->created_by = $created_by;
    $r->enquiry->created_date = $created_date;
    $r->enquiry->modified_date = $created_date;

    if (isset($r->enquiry->dt_poss_min))
    {
        $dt_poss_min = $r->enquiry->dt_poss_min;
        $tdt_poss_min = substr($dt_poss_min,6,4)."-".substr($dt_poss_min,3,2)."-".substr($dt_poss_min,0,2);
        $r->enquiry->dt_poss_min = $tdt_poss_min;
    }

    if (isset($r->enquiry->dt_poss_max))
    {
        $dt_poss_max = $r->enquiry->dt_poss_max;
        $tdt_poss_max = substr($dt_poss_max,6,4)."-".substr($dt_poss_max,3,2)."-".substr($dt_poss_max,0,2);
        $r->enquiry->dt_poss_max = $tdt_poss_max;
    }

    if (isset($r->enquiry->lease_start))
    {
        $lease_start = $r->enquiry->lease_start;
        $tlease_start = substr($lease_start,6,4)."-".substr($lease_start,3,2)."-".substr($lease_start,0,2);
        $r->enquiry->lease_start = $tlease_start;
    }

    if (isset($r->enquiry->lease_end))
    {
        $lease_end = $r->enquiry->lease_end;
        $tlease_end = substr($lease_end,6,4)."-".substr($lease_end,3,2)."-".substr($lease_end,0,2);
        $r->enquiry->lease_end = $tlease_end;
    }

    if (isset($r->enquiry->budget_range1))
    {
        if (isset($r->enquiry->budget_range1_para))
        {
            $budget_range1 = ($r->enquiry->budget_range1);
            $r->enquiry->budget_range1 = $budget_range1;
        }
    }

    if (isset($r->enquiry->budget_range2))
    {
        if (isset($r->enquiry->budget_range2_para))
        {
            $budget_range2 =($r->enquiry->budget_range2);
            $r->enquiry->budget_range2 = $budget_range2;
        }
    }

    if (isset($r->enquiry->depo_range1))
    {
        if (isset($r->enquiry->depo_range1_para))
        {
            $depo_range1 = ($r->enquiry->depo_range1);
            $r->enquiry->depo_range1 = $depo_range1;
        }
    }

    if (isset($r->enquiry->depo_range2))
    {
        if (isset($r->enquiry->depo_range2_para))
        {
            $depo_range2 = ($r->enquiry->depo_range2);
            $r->enquiry->depo_range2 = $depo_range2;
        }
    }

    if (isset($r->enquiry->price_unit))
    {
        if (isset($r->enquiry->price_unit_para))
        {
            $price_unit = ($r->enquiry->price_unit);
            $r->enquiry->price_unit = $price_unit;
        }
    }

    if (isset($r->enquiry->price_unit_carpet))
    {
        if (isset($r->enquiry->price_unit_carpet_para))
        {
            $price_unit_carpet = ($r->enquiry->price_unit_carpet);
            $r->enquiry->price_unit_carpet = $price_unit_carpet;
        }
    }
    

    $tabble_name = "enquiry";
    $column_names = array('enquiry_off', 'amenities_avl','area_para','assigned','bath','bedrooms','broker_id','broker_involved','budget_range1','budget_range2','budget_range1_para','budget_range2_para','campaign','carp_area1','carp_area2','carp_area_para','client_id','con_status','depo_range1','depo_range2','depo_range1_para','depo_range2_para','dt_poss_max','dt_poss_min','email_salestrainee','enquiry_for', 'source_from', 'floor_range1','floor_range2','frontage','watersup','powersup','furniture','groups','height','intrnal_comment','lease_period','loan_req','portal_id','pre_leased','preferred_area_id','preferred_city','preferred_country','preferred_locality_id','preferred_project_id','preferred_state','priority','pro_alerts','enquiry_type','enquiry_sub_type','lockin_period','lifts','floors','tenant','tenant_other', 'tenant_vicinity', 'reg_date','sale_area1','sale_area2','share_agsearch','sms_salestrainee','source_channel','stage','status','subscr_email','subsource_channel','teams','sub_teams','tot_area1','tot_area2','tot_para','vastu_comp','created_by','created_date','modified_date','zip','mainroad','internalroad','door_fdir','parking','car_park','portal_name','tenant_name','occ_details','rented_area','roi','lease_start','lease_end','rent_per_sqft','monthle_rent','pre_leased_rent','cam_charges','fur_charges','distfrm_station','distfrm_dairport','distfrm_school','distfrm_market','distfrm_highway','price_unit','price_unit_carpet','price_unit_para','price_unit_carpet_para','task_id');

    $multiple=array("amenities_avl","con_status","assigned","groups","source_channel","subsource_channel","teams","sub_teams","parking","con_status",'preferred_area_id');
    $result = $db->insertIntoTable($r->enquiry, $column_names, $tabble_name, $multiple);
   
    /*if (isset($r->enquiry->required_area_id))
    {
        $required_area_id = $r->enquiry->required_area_id;
        foreach($required_area_id as $area_id)
        {
            $r->enquiry->preferred_area_id = $area_id;
            $result = $db->insertIntoTable($r->enquiry, $column_names, $tabble_name, $multiple);
        }
    }
    else{
        
    }*/
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Enquiry created successfully";
        $response["enquiry_id"] = $result;
        $_SESSION['tmpenquiry_id'] = $result;
        $category_id = $result;
        //if ($created_by==64)
        //{
            
            $myemail = "crm@rdbrothers.com";
            $toemail = "crm@rdbrothers.com";

            $myemaildata = $db->getAllRecords("SELECT off_email FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = $created_by LIMIT 1");
            if ($myemaildata)
            {
                $myemail = $myemaildata[0]['off_email'];
            }
            

            $first_id="Yes";
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
            }

            //$toemail = "shekhar.lanke@gmail.com";


            $cc_mail_id = "";
            $email_subject = "New Enquiry Assigned to you [ENQUIRYID:".$result."]";
            $message_to_send = "<p>New Enquiry Assigned to you [ENQUIRYID:".$result."]</p><p></p><p>Thank you..</p><p></p>";
            $email_body = "<html><body>"."<div><p>Dear Sir,</p> <p></p>".
                        " ".$message_to_send.
                        " <p> Team RD Brothers </p>"."</div><html><body>".
            
            $headers = "From: $myemail\n";
            $headers .= "Reply-To: $myemail";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            if(!mail($toemail,$email_subject,$email_body,$headers))
            {
                $response["message1"] = "Mail not Sent ...!";
            }
            else
            {
                $response["message1"] = "Message has been sent ...!";
                $mail_date = date('Y-m-d');
                $created_date = date('Y-m-d H:i:s');
                $query = "INSERT INTO client_mails (mail_date , category,category_id, subject, text_message, from_mail_id, to_mail_id, cc_mail_id,created_by,  created_date)   VALUES('$mail_date' , 'enquiry', '$category_id', '$email_subject', '$email_body', '$myemail','$toemail', '$cc_mail_id', '$created_by', '$created_date')";
                $result = $db->insertByQuery($query);
            }
        //}
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create Enquiry. Please try again";
        echoResponse(201, $response);
    }
});

$app->get('/getoneenquiry/:mailcategory_id', function($mailcategory_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT (SELECT GROUP_CONCAT(r.email SEPARATOR ';') from enquiry as q LEFT JOIN contact as r on q.client_id = r.contact_id WHERE q.enquiry_id IN( $mailcategory_id) and r.email != '') as email_ids , (SELECT GROUP_CONCAT(CONCAT(t.name_title,' ',t.f_name,' ',t.l_name) SEPARATOR '/') from enquiry as s LEFT JOIN contact as t on s.client = t.contact_id WHERE s.enquiry_id IN( $mailcategory_id)) as client_name ";


    $getoneenquiries = $db->getAllRecords($sql);
    echo json_encode($getoneenquiries);
});

$app->get('/enquiries_edit_ctrl/:enquiry_id', function($enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * ,DATE_FORMAT(dt_poss_min,'%d/%m/%Y') AS dt_poss_min,DATE_FORMAT(dt_poss_max,'%d/%m/%Y') AS dt_poss_max,DATE_FORMAT(lease_start,'%d/%m/%Y') AS lease_start,DATE_FORMAT(lease_end,'%d/%m/%Y') AS lease_end from enquiry where enquiry_id=".$enquiry_id;
    $enquiries = $db->getAllRecords($sql);
    echo json_encode($enquiries);
    
});

$app->get('/getenquiries_exists/:client_id', function($client_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT *, CONCAT(a.enquiry_off,'_',a.enquiry_id,' ',a.enquiry_type,'-',b.name_title,' ',b.f_name,' ',b.l_name) as enquiry_title from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id where a.client_id=$client_id LIMIT 10";

    $senddata =  array();
    $htmldata = array();
    $htmlstring = '';

    $htmlstring = '<ul class="mydropdown-menu1" style="display:block;">';
    $stmt = $db->getRows($sql);
    $count = 0;
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<li><a href="#/enquiries_edit/'.$row['enquiry_id'].'"> '.$row['enquiry_title'].'</a></li>';
            $count = $count + 1;
   
        }
    }
    $htmlstring .='</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['count']=$count;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/getclientfromenquiry/:enquiry_id', function($enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT client_id FROM enquiry where enquiry_id=".$enquiry_id;
    $clientfromenquiries = $db->getAllRecords($sql);
    echo json_encode($clientfromenquiries);
    
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
    $modified_date = date('Y-m-d H:i:s');
    $r->enquiry->modified_by = $modified_by;
    $r->enquiry->modified_date = $modified_date;

    if (isset($r->enquiry->dt_poss_min))
    {
        $dt_poss_min = $r->enquiry->dt_poss_min;
        $tdt_poss_min = substr($dt_poss_min,6,4)."-".substr($dt_poss_min,3,2)."-".substr($dt_poss_min,0,2);
        $r->enquiry->dt_poss_min = $tdt_poss_min;
    }

    if (isset($r->enquiry->dt_poss_max))
    {
        $dt_poss_max = $r->enquiry->dt_poss_max;
        $tdt_poss_max = substr($dt_poss_max,6,4)."-".substr($dt_poss_max,3,2)."-".substr($dt_poss_max,0,2);
        $r->enquiry->dt_poss_max = $tdt_poss_max;
    }

    if (isset($r->enquiry->lease_start))
    {
        $lease_start = $r->enquiry->lease_start;
        $tlease_start = substr($lease_start,6,4)."-".substr($lease_start,3,2)."-".substr($lease_start,0,2);
        $r->enquiry->lease_start = $tlease_start;
    }

    if (isset($r->enquiry->lease_end))
    {
        $lease_end = $r->enquiry->lease_end;
        $tlease_end = substr($lease_end,6,4)."-".substr($lease_end,3,2)."-".substr($lease_end,0,2);
        $r->enquiry->lease_end = $tlease_end;
    }
    
    if (isset($r->enquiry->budget_range1))
    {
        if (isset($r->enquiry->budget_range1_para))
        {
            $budget_range1 = ($r->enquiry->budget_range1);
            $r->enquiry->budget_range1 = $budget_range1;
        }
    }

    if (isset($r->enquiry->budget_range2))
    {
        if (isset($r->enquiry->budget_range2_para))
        {
            $budget_range2 = ($r->enquiry->budget_range2);
            $r->enquiry->budget_range2 = $budget_range2;
        }
    }

    if (isset($r->enquiry->depo_range1))
    {
        if (isset($r->enquiry->depo_range1_para))
        {
            $depo_range1 = ($r->enquiry->depo_range1);
            $r->enquiry->depo_range1 = $depo_range1;
        }
    }

    if (isset($r->enquiry->depo_range2))
    {
        if (isset($r->enquiry->depo_range2_para))
        {
            $depo_range2 = ($r->enquiry->depo_range2);
            $r->enquiry->depo_range2 = $depo_range2;
        }
    }

    if (isset($r->enquiry->price_unit))
    {
        if (isset($r->enquiry->price_unit_para))
        {
            $price_unit = ($r->enquiry->price_unit);
            $r->enquiry->price_unit = $price_unit;
        }
    }

    if (isset($r->enquiry->price_unit_carpet))
    {
        if (isset($r->enquiry->price_unit_carpet_para))
        {
            $price_unit_carpet = ($r->enquiry->price_unit_carpet);
            $r->enquiry->price_unit_carpet = $price_unit_carpet;
        }
    }



    $enquiry_id  = $r->enquiry->enquiry_id;
    $isenquiryExists = $db->getOneRecord("select 1 from enquiry where enquiry_id=$enquiry_id");
    if($isenquiryExists){
        $tabble_name = "enquiry";
        $column_names = array('enquiry_off', 'amenities_avl','area_para','assigned','bath','bedrooms','broker_id','broker_involved','budget_range1','budget_range2','budget_range1_para','budget_range2_para','campaign','carp_area1','carp_area2','carp_area_para','client_id','con_status','depo_range1','depo_range2','depo_range1_para','depo_range2_para','dt_poss_max','dt_poss_min','email_salestrainee','enquiry_for', 'source_from', 'floor_range1','floor_range2','frontage','watersup','powersup','furniture','groups','height','intrnal_comment','lease_period','loan_req','portal_id','pre_leased','preferred_area_id','preferred_city','preferred_country','preferred_locality_id','preferred_project_id','preferred_state','priority','pro_alerts','enquiry_type','enquiry_sub_type','lockin_period','lifts','floors','tenant','tenant_other', 'tenant_vicinity', 'reg_date','sale_area1','sale_area2','share_agsearch','sms_salestrainee','source_channel','stage','status','subscr_email','subsource_channel','teams','sub_teams','tot_area1','tot_area2','tot_para','vastu_comp','modified_by','modified_date','zip','mainroad','internalroad','door_fdir','parking','car_park','portal_name','tenant_name','occ_details','rented_area','roi','lease_start','lease_end','rent_per_sqft','monthle_rent','pre_leased_rent','cam_charges','fur_charges','distfrm_station','distfrm_dairport','distfrm_school','distfrm_market','distfrm_highway','price_unit','price_unit_carpet','price_unit_para','price_unit_carpet_para','task_id');

        $multiple=array("amenities_avl","con_status","assigned","groups","source_channel","subsource_channel","teams","sub_teams","parking","con_status",'preferred_area_id');
        $condition = "enquiry_id='$enquiry_id'";
        
        $history = $db->historydata( $r->enquiry, $column_names, $tabble_name,$condition,$enquiry_id,$multiple, $modified_by, $modified_date);

        $result = $db->NupdateIntoTable($r->enquiry, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Enquiry Updated successfully";
            $_SESSION['tmpenquiry_id'] = $enquiry_id;
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

$app->post('/enquiry_uploads', function() use ($app) {
    session_start();
    $enquiry_id = $_SESSION['tmpenquiry_id'];
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
        $file_names = "e_".$enquiry_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "enquiry". $ds. $file_names;
        $target_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "enquiry". $ds. "thumb" .$ds. $file_names;
        
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

            if (isset($_POST['file_category_'.$count]))
            {
                $file_category = $_POST['file_category_'.$count];
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

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('enquiry', '$enquiry_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->post('/enquiry_document_uploads', function() use ($app) {
    session_start();
    $enquiry_id = $_SESSION['tmpenquiry_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file-document'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }

    $images = $_FILES['file-document'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "ed_".$enquiry_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "enquiry_doc". $ds. $file_names;
        $target_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "enquiry_doc". $ds. "thumb" .$ds. $file_names;
        
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

            if (isset($_POST['file_category_'.$count]))
            {
                $file_category = $_POST['file_category_'.$count];
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

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('enquiry_doc', '$enquiry_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});


$app->get('/enquiry_images/:enquiry_id', function($enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'enquiry' and category_id = $enquiry_id";
    $enquiry_images = $db->getAllRecords($sql);
    echo json_encode($enquiry_images);
});


$app->get('/enquiry_document_images/:enquiry_id', function($enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'enquiry_doc' and category_id = $enquiry_id";
    $enquiry_document_images = $db->getAllRecords($sql);
    echo json_encode($enquiry_document_images);
});


$app->get('/create_new_enquiry/:enquiry_id', function($enquiry_id) use ($app) {
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
    $modified_date = date('Y-m-d H:i:s');

    $sql=
    "INSERT INTO enquiry(prefix,  enquiry_for, enquiry_type, enquiry_sub_type, enquiry_off, client, client_id, broker_involved, broker, broker_id, con_status, dt_poss_min, dt_poss_max, loan_req, pr_name, pre_leased, priority, preferred_project_id, preferred_locality_id, preferred_area_id, preferred_streets, preferred_areas, preferred_city, preferred_state, preferred_country, zip, bedrooms, bath, furniture, floor_range1, floor_range2, door_fdir, sale_area1, sale_area2, area_para, tot_area1, tot_area2, tot_para, carp_area1, carp_area2, carp_area_para, budget_range1, budget_range1_para, budget_range2, budget_range2_para, price_unit, price_unit_carpet, depo_range1, depo_range1_para, depo_range2, depo_range2_para, price_unit_para, price_unit_carpet_para, teams, assigned, sms_salestrainee, email_salestrainee, lease_period, lockin_period, source_channel, subsource_channel, campaign, status, stage, vastu_comp, tenant, groups, mainroad, internalroad, car_park, portal_name, intrnal_comment, external_comment, share_agsearch, pro_alerts, subscr_email, height, frontage, powersup, watersup, lifts, floors, reg_date, amenities_avl, parking, portal_id, asset_id, published, created_by, created_date, modified_by, modified_date, tenant_name, occ_details, rented_area, roi, lease_start, lease_end, rent_per_sqft, monthle_rent, pre_leased_rent, cam_charges, fur_charges, distfrm_station, distfrm_dairport, distfrm_school, distfrm_market, distfrm_highway, source_from, tenant_other, tenant_vicinity)   SELECT prefix,enquiry_for, enquiry_type, enquiry_sub_type, enquiry_off, client, client_id, broker_involved, broker, broker_id, con_status, dt_poss_min, dt_poss_max, loan_req, pr_name, pre_leased, priority, preferred_project_id, preferred_locality_id, preferred_area_id, preferred_streets, preferred_areas, preferred_city, preferred_state, preferred_country, zip, bedrooms, bath, furniture, floor_range1, floor_range2, door_fdir, sale_area1, sale_area2, area_para, tot_area1, tot_area2, tot_para, carp_area1, carp_area2, carp_area_para, budget_range1, budget_range1_para, budget_range2, budget_range2_para, price_unit, price_unit_carpet, depo_range1, depo_range1_para, depo_range2, depo_range2_para, price_unit_para, price_unit_carpet_para, teams, assigned, sms_salestrainee, email_salestrainee, lease_period, lockin_period, source_channel, subsource_channel, campaign, status, stage, vastu_comp, tenant, groups, mainroad, internalroad, car_park, portal_name, intrnal_comment, external_comment, share_agsearch, pro_alerts, subscr_email, height, frontage, powersup, watersup, lifts, floors, reg_date, amenities_avl, parking, portal_id, asset_id, published, created_by, created_date, modified_by, modified_date, tenant_name, occ_details, rented_area, roi, lease_start, lease_end, rent_per_sqft, monthle_rent, pre_leased_rent, cam_charges, fur_charges, distfrm_station, distfrm_dairport, distfrm_school, distfrm_market, distfrm_highway, source_from, tenant_other, tenant_vicinity FROM enquiry WHERE enquiry_id = $enquiry_id";
    $result = $db->insertByQuery($sql);
    $response["enquiry_id"] = $result;
    $new_enquiry_id = $result;
    $sql = "UPDATE enquiry set created_by = '$created_by' , created_date = '$created_date' , modified_date = '$modified_date' WHERE enquiry_id = $new_enquiry_id";
    $result = $db->updateByQuery($sql);
    $ds = DIRECTORY_SEPARATOR;
    $sql = "SELECT * from attachments WHERE category = 'Enquiry' and enquiry_id = $enquiry_id";
    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        $count = 1;
        $ext = 'jpg';
        while($row = $stmt->fetch_assoc())
        {
            $source_file_names = $row['filenames'];
            $source = dirname( __FILE__ ). $ds."uploads" . $ds . "enquiry". $ds. $source_file_names;

            $source_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "enquiry". $ds. "thumb" .$ds. $source_file_names;

            $file_names = "e_".$new_enquiry_id."_".time()."_".$count.".".$ext;
            $target = dirname( __FILE__ ). $ds."uploads" . $ds . "enquiry". $ds. $file_names;
            $target_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "enquiry". $ds. "thumb" .$ds. $file_names;

            copy($source,$target);
            copy($source_thumb,$target_thumb);

            $share_on_web = $row['share_on_web'];
            $isdefault = $row['isdefault'];
            $description = $row['description'];
            $file_category = $row['$file_category'];

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('enquiry', '$new_enquiry_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$created_by', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        }
    }

    $response["status"] = "success";
    $response["message"] = "Enquiry Created !!!";
    echo json_encode($response);

});


$app->get('/selectenquiry', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *, CONCAT(a.enquiry_off,'_',a.enquiry_id,' ',a.enquiry_type,'-',b.name_title,' ',b.f_name,' ',b.l_name) as enquiry_title from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id where a.status='Active' ORDER BY a.created_date DESC";
    $enquiries = $db->getAllRecords($sql);
    echo json_encode($enquiries);
});

$app->get('/selectenquiry_with_broker', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT enquiry_id, CONCAT(a.enquiry_off,'_',a.enquiry_id,' ',a.enquiry_type,'-',b.name_title,' ',b.f_name,' ',b.l_name) as enquiry_title from enquiry as a LEFT JOIN contact as b on (a.client_id = b.contact_id OR a.broker_id = b.contact_id ) where a.status='Active' ORDER BY a.created_date DESC";
    $enquiries = $db->getAllRecords($sql);
    echo json_encode($enquiries);
});


$app->get('/getassignedenquiries', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];
    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    
    $sql = "SELECT enquiry_id, CONCAT(a.enquiry_off,'_',a.enquiry_id,' ',a.enquiry_type,'-',b.name_title,' ',b.f_name,' ',b.l_name) as enquiry_title from enquiry as a LEFT JOIN contact as b on (a.client_id = b.contact_id OR a.broker_id = b.contact_id ) where (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET ($user_id ,a.assigned) ".$team_query." )  and a.status='Active' ORDER BY a.created_date DESC";
    $selectenquiries = $db->getAllRecords($sql);
    echo json_encode($selectenquiries);
});



$app->post('/search_enquiries', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());

    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];

    $cat = $r->searchdata->proptype;

    $sql="SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob, SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name, c.email as broker_email, c.mob_no as broker_mob, e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assign_to  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id WHERE a.enquiry_off = '$cat' and a.created_by = $user_id ";

    
    if (in_array("Admin Level", $permissions))
    {
 
        $sql="SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assign_to from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id where a.enquiry_off = '$cat'  ";
    }

    if (in_array("Branch Head Level", $permissions))
    { 
	    $sql="SELECT *,  SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assign_to from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id WHERE a.enquiry_off = '$cat' and z.bo_id = $bo_id ";
     
    }

    if (in_array("Branch User Level", $permissions))
    { 
        $sql="SELECT *,  SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assign_to  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id WHERE a.enquiry_off = '$cat' and z.bo_id = $bo_id ";
      
    }

    if (in_array("User Level", $permissions))
    { 
        $sql="SELECT *,  SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id WHERE a.enquiry_off = '$cat' and a.created_by = $user_id ";
      
    }

    
    if (isset($r->searchdata->amenities_avl))
    {
        $amenities_avl = implode(",",$r->searchdata->amenities_avl);
        $sql .= " and a.amenities_avl in ('".$amenities_avl."') ";
    }

    if (isset($r->searchdata->assign_to))
    {
        $assign_to = implode(",",$r->searchdata->assign_to);
        $sql .= " and a.assign_to in ('".$assign_to."') ";
    }

    if (isset($r->searchdata->source_channel))
    {
        $source_channel = implode(",",$r->searchdata->source_channel);
        $sql .= " and a.source_channel in ('".$source_channel."') ";
    }

    if (isset($r->searchdata->subsource_channel))
    {
        $subsource_channel = implode(",",$r->searchdata->subsource_channel);
        $sql .= " and a.subsource_channel in ('".$subsource_channel."') ";
    }

    if (isset($r->searchdata->teams))
    {
        $teams = implode(",",$r->searchdata->teams);
        $sql .= " and a.teams in ('".$teams."') ";
    }

    if (isset($r->searchdata->area_id))
    {
        $sql .= " and a.area_id = ".$r->searchdata->area_id." ";
        
    }

    if (isset($r->searchdata->broker_id))
    {
        $sql .= " and a.broker_id = ".$r->searchdata->broker_id." ";
    }

    if (isset($r->searchdata->building_name))
    {
        $sql .= " and a.building_name = '".$r->searchdata->building_name."' ";
    }
    if (isset($r->searchdata->carp_area))
    {
        $sql .= " and a.carp_area = '".$r->searchdata->carp_area."' ";
    }

    if (isset($r->searchdata->carp_area2))
    {
        $sql .= " and a.carp_area2 = '".$r->searchdata->carp_area2."' ";
    }
    if (isset($r->searchdata->carp_area_para))
    {
        $sql .= " and a.carp_area_para = '".$r->searchdata->carp_area_para."' ";
    }
    if (isset($r->searchdata->city_id))
    {
        $sql .= " and a.city_id = '".$r->searchdata->city_id."' ";
    }
    if (isset($r->searchdata->con_status))
    {
        $sql .= " and a.con_status = '".$r->searchdata->con_status."' ";
    }
    if (isset($r->searchdata->developer_id))
    {
        $sql .= " and a.dev_owner_id = '".$r->searchdata->developer_id."' ";
    }
    if (isset($r->searchdata->exp_price))
    {
        $sql .= " and a.exp_price = '".$r->searchdata->exp_price."' ";
    }
    if (isset($r->searchdata->exp_price2))
    {
        $sql .= " and a.exp_price2 = '".$r->searchdata->exp_price2."' ";
    }
    /*if (isset($r->searchdata->exp_price2_para))
    {
        $sql .= " and a.exp_price2_para = '".$r->searchdata->exp_price2_para."' ";
    }
    if (isset($r->searchdata->exp_price_para))
    {
        $sql .= " and a.exp_price_para = '".$r->searchdata->exp_price_para."' ";
    }*/
    if (isset($r->searchdata->exp_rent))
    {
        $sql .= " and a.exp_rent = '".$r->searchdata->exp_rent."' ";
    }
    if (isset($r->searchdata->exp_rent2))
    {
        $sql .= " and a.exp_rent2 = '".$r->searchdata->exp_rent2."' ";
    }
    /*if (isset($r->searchdata->exp_rent2_para))
    {
        $sql .= " and a.exp_rent2_para = '".$r->searchdata->exp_rent2_para."' ";
    }
    if (isset($r->searchdata->exp_rent_para))
    {
        $sql .= " and a.exp_rent_para = '".$r->searchdata->exp_rent_para."' ";
    }*/
    if (isset($r->searchdata->floor))
    {
        $sql .= " and a.floor = '".$r->searchdata->floor."' ";
    }
    if (isset($r->searchdata->furniture))
    {
        $sql .= " and a.furniture = '".$r->searchdata->furniture."' ";
    }
    if (isset($r->searchdata->locality_id))
    {
        $sql .= " and a.locality_id = '".$r->searchdata->locality_id."' ";
    }
    if (isset($r->searchdata->owner_id))
    {
        $sql .= " and a.dev_owner_id = '".$r->searchdata->owner_id."' ";
    }
    if (isset($r->searchdata->possession_date))
    {
        $possession_date = $r->searchdata->possession_date;
        $tpossession_date = substr($possession_date,6,4)."-".substr($possession_date,3,2)."-".substr($possession_date,0,2);
        $sql .= " and a.possession_date = '".$tpossession_date."' ";
    }

    if (isset($r->searchdata->created_date))
    {
        $created_date = $r->searchdata->created_date;
        $tcreated_date = substr($created_date,6,4)."-".substr($created_date,3,2)."-".substr($created_date,0,2);
        $sql .= " and a.created_date LIKE '".$tcreated_date."%' ";
    }

    if (isset($r->searchdata->posted_by))
    {
        $sql .= " and a.posted_by = '".$r->searchdata->posted_by."' ";
    }
    if (isset($r->searchdata->pre_leased))
    {
        $sql .= " and a.pre_leased = '".$r->searchdata->pre_leased."' ";
    }
    if (isset($r->searchdata->pro_sale_para))
    {
        $sql .= " and a.pro_sale_para = '".$r->searchdata->pro_sale_para."' ";
    }
    if (isset($r->searchdata->proj_status))
    {
        $sql .= " and a.proj_status = '".$r->searchdata->proj_status."' ";
    }
    if (isset($r->searchdata->project_name))
    {
        $sql .= " and a.project_name = '".$r->searchdata->project_name."' ";
    }
    if (isset($r->searchdata->property_for))
    {
        $sql .= " and a.property_for = '".$r->searchdata->property_for."' ";
    }
    if (isset($r->searchdata->propsubtype))
    {
        $sql .= " and a.propsubtype = '".$r->searchdata->propsubtype."' ";
    }
    if (isset($r->searchdata->proptype))
    {
        $sql .= " and a.proptype = '".$r->searchdata->proptype."' ";
    }
    if (isset($r->searchdata->push_website))
    {
        $sql .= " and a.push_website = '".$r->searchdata->push_website."' ";
    }
    if (isset($r->searchdata->rera_num))
    {
        $sql .= " and a.rera_num = '".$r->searchdata->rera_num."' ";
    }
    if (isset($r->searchdata->sale_area))
    {
        $sql .= " and a.sale_area = '".$r->searchdata->sale_area."' ";
    }
    if (isset($r->searchdata->sale_area2))
    {
        $sql .= " and a.sale_area2 = '".$r->searchdata->sale_area2."' ";
    }
    if (isset($r->searchdata->share_website))
    {
        $sql .= " and a.share_website = '".$r->searchdata->share_website."' ";
    }
    
    if (isset($r->searchdata->unit))
    {
        $sql .= " and a.unit = '".$r->searchdata->unit."' ";
    }

    if (isset($r->searchdata->enquiry_id))
    {
        $sql .= " and a.enquity_id = '".$r->searchdata->enquiry_id."' ";
    }

    if (isset($r->searchdata->wing))
    {
        $sql .= " and a.building_name = '".$r->searchdata->wing."' ";
    }

    $sql .= " ORDER BY CAST(a.enquiry_id as UNSIGNED) DESC"; // GROUP BY a.enquiry_id 
    //echo $sql;
    //exit(0);

    $enquiries = $db->getAllRecords($sql);
    //$properties[0].['sql']=$sql;
    echo json_encode($enquiries);
});

// $app->post('/newsearch_enquiries', function() use ($app) {
//     $db = new DbHandler();
//     $session = $db->getSession();
//     if ($session['username']=="Guest")
//     {
//         return;
//     }
//     $response = array();
//     $r = json_decode($app->request->getBody());

//     $bo_id = $session['bo_id'];
//     $teams = $session['teams'];
//     $team_query = " ";   
//     $tteams = $session['teams'];
//     foreach($tteams as $value)
//     {
//         $team_query .= " OR a.teams LIKE '".$value."' ";
//     }
//     $user_id = $session['user_id'];
//     $permissions = $session['permissions'];


//     $cat = $r->searchdata->proptype;

//     $next_page_id = $r->searchdata->next_page_id;
    
//     $role = $session['role'];

//     $searchsql = "SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob, SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name, c.email as broker_email, c.mob_no as broker_mob, e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id WHERE a.enquiry_off = '$cat' and a.created_by = $user_id ";

//     $countsql = "SELECT count(*) as enquiry_count FROM enquiry ";
//     if (in_array("Admin", $role))
//     {
//         $searchsql="SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by, if(a.budget_range1_para='th',a.budget_range1*1000,if (a.budget_range1_para = 'lac', a.budget_range1 * 100000, if(a.budget_range1_para = 'cr', a.budget_range1 * 10000000,a.budget_range1))) as budget_range1value , if (a.budget_range2_para='th',a.budget_range2*1000,if (a.budget_range2_para = 'lac', a.budget_range2 * 100000, if(a.budget_range2_para = 'cr', a.budget_range2 * 10000000,a.budget_range2))) as budget_range2value FROM enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id where a.enquiry_off = '$cat'  ";

//         $countsql = "SELECT count(*) as enquiry_count FROM enquiry as a WHERE a.enquiry_off = '$cat'  ";

//     }
//     else
//     { 
//         $searchsql="SELECT *,  SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by,if(a.budget_range1_para='th',a.budget_range1*1000,if (a.budget_range1_para = 'lac', a.budget_range1 * 100000, if(a.budget_range1_para = 'cr', a.budget_range1 * 10000000,a.budget_range1))) as budget_range1value , if (a.budget_range2_para='th',a.budget_range2*1000,if (a.budget_range2_para = 'lac', a.budget_range2 * 100000, if(a.budget_range2_para = 'cr', a.budget_range2 * 10000000,a.budget_range2))) as budget_range2value from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id WHERE a.enquiry_off = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assigned) ".$team_query." ) ";

//         $countsql = "SELECT count(*) as enquiry_count FROM enquiry as a WHERE a.enquiry_off = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assigned) ".$team_query." ) ";
      
//     }
//     if (isset($r->searchdata->status))
//     {
//         $first='Yes';
//         $new_data = $r->searchdata->status;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.status = '".$value."' ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

//     if (isset($r->searchdata->enquiry_for))
//     {
//         $sql .= " and a.enquiry_for = '".$r->searchdata->enquiry_for."' ";
//     }

//     if (isset($r->searchdata->enquiry_type))
//     {
//         $first='Yes';
//         $new_data = $r->searchdata->enquiry_type;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.enquiry_type LIKE '%".$value."%' ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }
    


//     if (isset($r->searchdata->bedrooms))
//     {
//         $first='Yes';
//         $new_data = $r->searchdata->bedrooms;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.bedrooms LIKE '%".$value."%' ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

//     if (isset($r->searchdata->area_id))
//     {
//         $first='Yes';
//         $new_data = $r->searchdata->area_id;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.preferred_area_id = ".$value." ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

//     if (isset($r->searchdata->locality_id))
//     {
//         $first='Yes';
//         $new_data = $r->searchdata->locality_id;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.preferred_locality_id = ".$value." ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

//     if (isset($r->searchdata->project_id))
//     {
//         $first='Yes';
//         $new_data = $r->searchdata->project_id;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.preferred_project_id = ".$value." ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

//     if (isset($r->searchdata->city_id))
//     {
//         $first='Yes';
//         $new_data = $r->searchdata->city_id;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.preferred_city LIKE '".$value."' ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }
    
//     /*if (isset($r->searchdata->budget_range1))
//     {
//         $budget_range1 = (float)$r->searchdata->budget_range1;
//         $budget_range2 = (float)$r->searchdata->budget_range2;
//         $budget_range1_para = $r->searchdata->budget_range1_para;
//         $budget_range2_para = $r->searchdata->budget_range2_para;

//         if ($budget_range1>0)
//         {
//             if ($budget_range2>0)
//             {
//                 $sql .= " and (a.budget_range1 >= $budget_range1 and a.budget_range2 <= $budget_range2 and a.budget_range1_para = '$budget_range1_para'  and  a.budget_range1_para = '$budget_range2_para' ) ";
//             }
//         }
//     }*/

//     if (isset($r->searchdata->enquiry_id))
//     {
//         $first='Yes';
//         $new_data = $r->searchdata->enquiry_id;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.enquiry_id = ".$value." ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

//     if (isset($r->searchdata->stage))
//     {
//         $first='Yes';
//         $new_data = $r->searchdata->stage;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.stage = '".$value."' ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

//     if (isset($r->searchdata->client_id))
//     {
//         $first='Yes';
//         $new_data = $r->searchdata->client_id;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.client_id = ".$value." ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

//     if (isset($r->searchdata->broker_id))
//     {
//         $first='Yes';
//         $new_data = $r->searchdata->broker_id;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.broker_id = ".$value." ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }
//     if (isset($r->searchdata->teams))
//     {
//         $first = "Yes";
//         $new_data = $r->searchdata->teams;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.teams LIKE '%".$value."%' ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

//     if (isset($r->searchdata->assigned))
//     {
//         $first = "Yes";
//         $new_data = $r->searchdata->assigned;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.assigned LIKE '%".$value."%' ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }

//     }

//     if (isset($r->searchdata->created_by))
//     {
//         $first = "Yes";
//         $new_data = $r->searchdata->created_by;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.created_by LIKE '".$value."' ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }

//     }

    
//     if (isset($r->searchdata->tot_area1))
//     {

//         if (isset($r->searchdata->tot_area2))
//         {
//             $tot_area1 = (float)$r->searchdata->tot_area1;
//             $tot_area2 = (float)$r->searchdata->tot_area2;
//             $sql .= " and (a.tot_area1 >= $tot_area1 and a.tot_area2 <= $tot_area2) ";
//         }
//     }

//     if (isset($r->searchdata->sale_area1))
//     {

//         if (isset($r->searchdata->sale_area2))
//         {
//             $sale_area1 = (float)$r->searchdata->sale_area1;
//             $sale_area2 = (float)$r->searchdata->sale_area2;
//             $sql .= " and (a.sale_area1 >= $sale_area1 and a.sale_area2 <= $sale_area2) ";
//         }
//     }

//     if (isset($r->searchdata->carp_area1))
//     {

//         if (isset($r->searchdata->carp_area2))
//         {
//             $carp_area1 = (float)$r->searchdata->carp_area1;
//             $carp_area2 = (float)$r->searchdata->carp_area2;
//             $sql .= " and (a.carp_area1 >= $carp_area1 and a.carp_area2 <= $carp_area2) ";
//         }
//     }

//     if (isset($r->searchdata->created_date_from))
//     {
//         $created_date_from = $r->searchdata->created_date_from;
//         $tcreated_date_from = substr($created_date_from,6,4)."-".substr($created_date_from,3,2)."-".substr($created_date_from,0,2);
//         if (isset($r->searchdata->created_date_to))
//         {
//             $created_date_to = $r->searchdata->created_date_to;
//             $tcreated_date_to = substr($created_date_to,6,4)."-".substr($created_date_to,3,2)."-".substr($created_date_to,0,2);
//         }
//         $sql .= " and a.created_date BETWEEN '".$tcreated_date_from."' AND '".$tcreated_date_to."' ";
//     }

//     if (isset($r->searchdata->furniture))
//     {
//         $sql .= " and (a.furniture = '$r->searchdata->furniture') ";
//     }

//     if (isset($r->searchdata->floor_range1))
//     {

//         if (isset($r->searchdata->floor_range2))
//         {
//             $sql .= " and (a.floor_range1 >= $r->searchdata->floor_range1 and a.floor_range2 <= $r->searchdata->floor_range2) ";
//         }
//     }

//     if (isset($r->searchdata->door_fdir))
//     {
//         $sql .= " and (a.door_fdir =  $r->searchdata->door_fdir ) ";
//     }

//     if (isset($r->searchdata->loan_req))
//     {
//         $sql .= " and (a.loan_req =  $r->searchdata->loan_req ) ";
//     }

//     if (isset($r->searchdata->source_channel))
//     {
//         $first = "Yes";
//         $new_data = $r->searchdata->source_channel;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.source_channel LIKE '%".$value."%' ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

    
//     if (isset($r->searchdata->subsource_channel))
//     {
//         $first = "Yes";
//         $new_data = $r->searchdata->subsource_channel;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.subsource_channel LIKE '%".$value."%' ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

//     if (isset($r->searchdata->con_status))
//     {
//         $first = "Yes";
//         $new_data = $r->searchdata->con_status;
//         if ($new_data)
//         {
//             foreach($new_data as $value)
//             {
//                 if ($first=='Yes')
//                 {
//                     $sql .= " AND (";
//                     $first = "No";
//                 }
//                 else{
//                     $sql .= " OR ";
//                 } 
//                 $sql .= " a.con_status LIKE '%".$value."%' ";
//             }
//             if ($first=='No')
//             {
//                 $sql .= ") ";
            
//             }
//         }
//     }

//     if (isset($r->searchdata->dt_poss_min))
//     {
//         $dt_poss_min = $r->searchdata->dt_poss_min;
//         $tdt_poss_min = substr($dt_poss_min,6,4)."-".substr($dt_poss_min,3,2)."-".substr($dt_poss_min,0,2);
//         if (isset($r->searchdata->dt_poss_max))
//         {
//             $dt_poss_max = $r->searchdata->dt_poss_max;
//             $tdt_poss_max = substr($dt_poss_max,6,4)."-".substr($dt_poss_max,3,2)."-".substr($dt_poss_max,0,2);
//         }
//         $sql .= " and a.created_date BETWEEN '".$tdt_poss_min."' AND '".$tdt_poss_max."' ";
//     }

//     if (isset($r->searchdata->priority))
//     {
        
//         $sql .= " and (a.priority =  '".$r->searchdata->priority."' ) ";
//     }
//     $bsql = "";
//     if (isset($r->searchdata->budget_range1))
//     {
//         $budget_range1 = $db->ConvertAmount((float)$r->searchdata->budget_range1,$r->searchdata->budget_range1_para);
        
//         $budget_range2 = $db->ConvertAmount((float)$r->searchdata->budget_range2,$r->searchdata->budget_range2_para);
        
//         //$budget_range1_para = $r->searchdata->budget_range1_para;
//         //$budget_range2_para = $r->searchdata->budget_range2_para;

//         if ($budget_range1>0)
//         {
//             if ($budget_range2>0)
//             {
//                 $bsql .= " HAVING (budget_range1value >= $budget_range1 and budget_range2value <= $budget_range2 ) ";
//             }
//         }
//     }
    
//     $searchsql .=  $sql . "  ".$bsql." ORDER BY a.modified_date DESC LIMIT $next_page_id, 30"; // GROUP BY a.enquiry_id

//     $enquiries = $db->getAllRecords($searchsql);
//     $countsql = $countsql . $sql;
//     $enquiry_count = 0;
//     $enquirycountdata = $db->getAllRecords($countsql);
//     if ($enquirycountdata)
//     {
//          $enquiry_count = $enquirycountdata[0]['enquiry_count'];

//     }
//     //if ($enquiry_count>0)
//     //{
//         $enquiries[0]['enquiry_count']=$enquiry_count;
//     //}
//     //$enquiries[0]['sql']=$countsql;
//     //$enquiries[0]['sql']=$searchsql;
//     echo json_encode($enquiries);
//     // BOTTOM
// });

$app->post('/newsearch_enquiries', function() use ($app) {
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


    $cat = $r->searchdata->proptype;

    $next_page_id = $r->searchdata->next_page_id;
    
    $role = $session['role'];

    $searchsql = "SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob, SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name, c.email as broker_email, c.mob_no as broker_mob, e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id WHERE a.enquiry_off = '$cat' and a.created_by = $user_id ";

    $countsql = "SELECT count(*) as enquiry_count FROM enquiry ";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $searchsql="SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by, if(a.budget_range1_para='th',a.budget_range1*1000,if (a.budget_range1_para = 'lac', a.budget_range1 * 100000, if(a.budget_range1_para = 'cr', a.budget_range1 * 10000000,a.budget_range1))) as budget_range1value , if (a.budget_range2_para='th',a.budget_range2*1000,if (a.budget_range2_para = 'lac', a.budget_range2 * 100000, if(a.budget_range2_para = 'cr', a.budget_range2 * 10000000,a.budget_range2))) as budget_range2value FROM enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id where a.enquiry_off = '$cat'  ";

        $countsql = "SELECT count(*) as enquiry_count FROM enquiry as a WHERE a.enquiry_off = '$cat'  ";

    }
    else
    { 
        $searchsql="SELECT *,  SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by,if(a.budget_range1_para='th',a.budget_range1*1000,if (a.budget_range1_para = 'lac', a.budget_range1 * 100000, if(a.budget_range1_para = 'cr', a.budget_range1 * 10000000,a.budget_range1))) as budget_range1value , if (a.budget_range2_para='th',a.budget_range2*1000,if (a.budget_range2_para = 'lac', a.budget_range2 * 100000, if(a.budget_range2_para = 'cr', a.budget_range2 * 10000000,a.budget_range2))) as budget_range2value from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id WHERE a.enquiry_off = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assigned) ".$team_query." ) ";

        $countsql = "SELECT count(*) as enquiry_count FROM enquiry as a WHERE a.enquiry_off = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assigned) ".$team_query." ) ";  
    }
    if (isset($r->searchdata->status))
    {
        $first='Yes';
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
                $sql .= " a.status = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->enquiry_for))
    {
        $sql .= " and a.enquiry_for = '".$r->searchdata->enquiry_for."' ";
    }

    if (isset($r->searchdata->enquiry_type))
    {
        $first='Yes';
        $new_data = $r->searchdata->enquiry_type;
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
                $sql .= " a.enquiry_type LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }
    
    if (isset($r->searchdata->enquiry_sub_type))
    {
        $first='Yes';
        $new_data = $r->searchdata->enquiry_sub_type;
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
                $sql .= " a.enquiry_sub_type LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }


    if (isset($r->searchdata->bedrooms))
    {
        $first='Yes';
        $new_data = $r->searchdata->bedrooms;
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
                $sql .= " a.bedrooms LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->area_id))
    {
        $first='Yes';
        $new_data = $r->searchdata->area_id;
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
                $sql .= " a.preferred_area_id = ".$value." ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->locality_id))
    {
        $first='Yes';
        $new_data = $r->searchdata->locality_id;
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
                $sql .= " a.preferred_locality_id = ".$value." ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->project_id))
    {
        $first='Yes';
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
                $sql .= " a.preferred_project_id = ".$value." ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->city_id))
    {
        $first='Yes';
        $new_data = $r->searchdata->city_id;
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
                $sql .= " a.preferred_city LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }
    
    /*if (isset($r->searchdata->budget_range1))
    {
        $budget_range1 = (float)$r->searchdata->budget_range1;
        $budget_range2 = (float)$r->searchdata->budget_range2;
        $budget_range1_para = $r->searchdata->budget_range1_para;
        $budget_range2_para = $r->searchdata->budget_range2_para;

        if ($budget_range1>0)
        {
            if ($budget_range2>0)
            {
                $sql .= " and (a.budget_range1 >= $budget_range1 and a.budget_range2 <= $budget_range2 and a.budget_range1_para = '$budget_range1_para'  and  a.budget_range1_para = '$budget_range2_para' ) ";
            }
        }
    }*/

    if (isset($r->searchdata->enquiry_id))
    {
        $first='Yes';
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
                $sql .= " a.enquiry_id = ".$value." ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->stage))
    {
        $first='Yes';
        $new_data = $r->searchdata->stage;
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
                $sql .= " a.stage = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->client_id))
    {
        $first='Yes';
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
                $sql .= " a.client_id = ".$value." ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->broker_id))
    {
        $first='Yes';
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
                $sql .= " a.broker_id = ".$value." ";
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

    if (isset($r->searchdata->assigned))
    {
        $first = "Yes";
        $new_data = $r->searchdata->assigned;
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
                $sql .= " a.assigned LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }

    if (isset($r->searchdata->created_by))
    {
        $first = "Yes";
        $new_data = $r->searchdata->created_by;
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
                $sql .= " a.created_by LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }

    
    if (isset($r->searchdata->tot_area1))
    {

        if (isset($r->searchdata->tot_area2))
        {
            $tot_area1 = (float)$r->searchdata->tot_area1;
            $tot_area2 = (float)$r->searchdata->tot_area2;
            $sql .= " and (a.tot_area1 >= $tot_area1 and a.tot_area2 <= $tot_area2) ";
        }
    }

    if (isset($r->searchdata->sale_area1))
    {

        if (isset($r->searchdata->sale_area2))
        {
            $sale_area1 = (float)$r->searchdata->sale_area1;
            $sale_area2 = (float)$r->searchdata->sale_area2;
            $sql .= " and (a.sale_area1 >= $sale_area1 and a.sale_area2 <= $sale_area2) ";
        }
    }

    if (isset($r->searchdata->carp_area1))
    {

        if (isset($r->searchdata->carp_area2))
        {
            $carp_area1 = (float)$r->searchdata->carp_area1;
            $carp_area2 = (float)$r->searchdata->carp_area2;
            $sql .= " and (a.carp_area1 >= $carp_area1 and a.carp_area2 <= $carp_area2) ";
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

    if (isset($r->searchdata->furniture))
    {
        $sql .= " and (a.furniture = '$r->searchdata->furniture') ";
    }

    if (isset($r->searchdata->floor_range1))
    {

        if (isset($r->searchdata->floor_range2))
        {
            $sql .= " and (a.floor_range1 >= $r->searchdata->floor_range1 and a.floor_range2 <= $r->searchdata->floor_range2) ";
        }
    }

    if (isset($r->searchdata->door_fdir))
    {
        $sql .= " and (a.door_fdir =  $r->searchdata->door_fdir ) ";
    }

    if (isset($r->searchdata->loan_req))
    {
        $sql .= " and (a.loan_req =  $r->searchdata->loan_req ) ";
    }

    if (isset($r->searchdata->source_channel))
    {
        $first = "Yes";
        $new_data = $r->searchdata->source_channel;
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
                $sql .= " a.source_channel LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    
    if (isset($r->searchdata->subsource_channel))
    {
        $first = "Yes";
        $new_data = $r->searchdata->subsource_channel;
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
                $sql .= " a.subsource_channel LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->con_status))
    {
        $first = "Yes";
        $new_data = $r->searchdata->con_status;
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
                $sql .= " a.con_status LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->dt_poss_min))
    {
        $dt_poss_min = $r->searchdata->dt_poss_min;
        $tdt_poss_min = substr($dt_poss_min,6,4)."-".substr($dt_poss_min,3,2)."-".substr($dt_poss_min,0,2);
        if (isset($r->searchdata->dt_poss_max))
        {
            $dt_poss_max = $r->searchdata->dt_poss_max;
            $tdt_poss_max = substr($dt_poss_max,6,4)."-".substr($dt_poss_max,3,2)."-".substr($dt_poss_max,0,2);
        }
        $sql .= " and a.created_date BETWEEN '".$tdt_poss_min."' AND '".$tdt_poss_max."' ";
    }

    if (isset($r->searchdata->priority))
    {
        
        $sql .= " and (a.priority =  '".$r->searchdata->priority."' ) ";
    }
    $bsql = "";
    if (isset($r->searchdata->budget_range1))
    {
        $budget_range1 = $db->ConvertAmount((float)$r->searchdata->budget_range1,$r->searchdata->budget_range1_para);
        
        $budget_range2 = $db->ConvertAmount((float)$r->searchdata->budget_range2,$r->searchdata->budget_range2_para);
        
        //$budget_range1_para = $r->searchdata->budget_range1_para;
        //$budget_range2_para = $r->searchdata->budget_range2_para;

        if ($budget_range1>0)
        {
            if ($budget_range2>0)
            {
                $bsql .= " HAVING (budget_range1value >= $budget_range1 and budget_range2value <= $budget_range2 ) ";
            }
        }
    }
    
    $searchsql .=  $sql . "  ".$bsql." ORDER BY a.modified_date DESC LIMIT $next_page_id, 30"; // GROUP BY a.enquiry_id

    $enquiries = $db->getAllRecords($searchsql);
    $countsql = $countsql . $sql;
    $enquiry_count = 0;
    $enquirycountdata = $db->getAllRecords($countsql);
    if ($enquirycountdata)
    {
         $enquiry_count = $enquirycountdata[0]['enquiry_count'];

    }
    //if ($enquiry_count>0)
    //{
        $enquiries[0]['enquiry_count']=$enquiry_count;
    //}
    //$enquiries[0]['sql']=$countsql;
    //$enquiries[0]['sql']=$searchsql;
    echo json_encode($enquiries);
    // BOTTOM
});

$app->get('/getdatavalues_enquiry/:field_name/:cat', function($field_name,$cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM enquiry WHERE enquiry_off = '$cat' ORDER BY $field_name";
    //echo $sql;
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});

$app->get('/activityselectenquiry/:enquiry_id', function($enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $bo_id = $session['bo_id'];
    $role = $session['role'];

    $ifAdmin = false;
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $ifAdmin = true; 
     }
       
    $sql = "SELECT *, CONCAT(a.enquiry_off,'_',a.enquiry_id,' ',a.enquiry_type,'-',b.name_title,' ',b.f_name,' ',b.l_name) as enquiry_title from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id where a.enquiry_id = $enquiry_id";
    
    if (!$ifAdmin)
    {
	    $sql = "SELECT *, CONCAT(a.enquiry_off,'_',a.enquiry_id,' ',a.enquiry_type,'-',b.name_title,' ',b.f_name,' ',b.l_name) as enquiry_title from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id WHERE a.enquiry_id = $enquiry_id and z.bo_id = $bo_id ";
    }

    $selectenquiries = $db->getAllRecords($sql);
    echo json_encode($selectenquiries);
});


$app->get('/getenquiries_properties/:property_id', function($property_id) use ($app) {
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
    $role = $session['role'];

    $ifAdmin = false;
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $ifAdmin = true; 
    }

    $sql = "SELECT *,  CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob, e.locality as preferred_locality, f.area_name as preferred_area  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN users as c on a.assigned = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id ";
      
    if (!$ifAdmin)
    {
       $sql .= " LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id ";
    }

    $sql .= " WHERE ";
    
    $insert_and = "No";
    if ($property_for !="")
    {
        $sql .= " a.enquiry_for = '$property_for' ";
        $insert_and = "Yes";
    }

    if ($property_type !="")
    {
        if ($insert_and=="Yes")
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

        $sql .= " ( $exp_price>=a.budget_range1 and $exp_price<=a.budget_range2) ";
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
    
    $sql .= " a.status = 'Active' ";*/

    if (!$ifAdmin)
    {
        if ($insert_and == "Yes")
        {
            $sql .= " and ";
        }
        $sql .= " z.bo_id = $bo_id ";
    }

    $sql .= " ORDER BY a.enquiry_for";  
    
    $m_enquiries = $db->getAllRecords($sql);
    echo json_encode($m_enquiries);
});


$app->get('/enquiry_count/:cat', function($cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();

    $sql = "SELECT count(*) as enquiry_count from enquiry WHERE enquiry_off = '$cat' ";
    $enquirydata = $db->getAllRecords($sql);
    $enquiry_count = 0;
    if ($enquirydata)
    {
        $enquiry_count = $enquirydata[0]['enquiry_count'];
    }

    $htmldata['enquiry_count']=$enquiry_count;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/matching_enquiries_list/:cat', function($cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
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
            
    $sql="SELECT *  from enquiry LIMIT 0";
    $countsql = "SELECT count(*) as enquiry_count from enquiry";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {

        $sql="SELECT a.enquiry_id,CONCAT('Enquiry_','',a.enquiry_id,' ',b.name_title,' ',b.f_name,' ',b.l_name) as select_enquiry_value from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' where a.enquiry_off = '$cat'  ORDER BY a.enquiry_id";
    }
    else
    {
        

        $sql="SELECT a.enquiry_id,CONCAT('Enquiry_','',a.enquiry_id,'-',b.name_title,' ',b.f_name,' ',b.l_name) as select_enquiry_value from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' where a.enquiry_off = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assigned) ".$team_query." ) ORDER BY a.enquiry_id";

    }
    
    $enquiries = $db->getAllRecords($sql);
    echo json_encode($enquiries);
});


$app->get('/link_to_enquiry/:property_id/:enquiry_id', function($property_id, $enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');

   //SELECT `matching_id`, `matching_with_cateogry`, `matching_with_id`, `matching_to_category`, `matching_to_id`, `created_by`, `created_date`, `modified_by`, `modified_date` FROM `matching` WHERE 1

    $query = "INSERT INTO matching (matching_with_category , matching_with_id,matching_to_category, matching_to_id, created_by,  created_date)   VALUES('enquiry' , '$enquiry_id', 'property', '$property_id', '$created_by', '$created_date')";
    $result = $db->insertByQuery($query);

    echo json_encode($result);
});

?>
