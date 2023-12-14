<?php


// CONTACT

$app->get('/contact_list_ctrl/:cat/:next_page_id', function($cat,$next_page_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $bo_id = $session['bo_id'];
    
    $teams = $session['teams'];
    $team_query = " and ( ";
    $tteams = $session['teams'];

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

    $user_id = $session['user_id'];
    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];

    $sql = "SELECT * from contact LIMIT 0";
    $countsql = "SELECT count(*) as contact_count FROM contact";


    $sql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(s.sub_team_name SEPARATOR ',') FROM sub_teams as s where FIND_IN_SET(s.sub_team_id , a.sub_teams)) as sub_teams , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to,(SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id  where a.contact_off = '$cat' and a.created_by = $user_id ORDER BY a.created_date DESC LIMIT $next_page_id, 30"; //  GROUP BY a.contact_id 
    
    $countsql = "SELECT count(*) as contact_count FROM contact";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(s.sub_team_name SEPARATOR ',') FROM sub_teams as s where FIND_IN_SET(s.sub_team_id , a.sub_teams)) as sub_teams ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id where a.contact_off = '$cat'   ORDER BY a.created_date DESC LIMIT $next_page_id, 30"; // GROUP BY a.contact_id

        $countsql = "SELECT count(*) as contact_count FROM contact as a  where a.contact_off = '$cat'  ";

    }


    else if (in_array("Branch Head", $role))
    {
	    $sql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(s.sub_team_name SEPARATOR ',') FROM sub_teams as s where FIND_IN_SET(s.sub_team_id , a.sub_teams)) as sub_teams ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id where a.contact_off = '$cat'  ".$team_query."  ORDER BY a.created_date DESC LIMIT $next_page_id, 30"; // GROUP BY a.contact_id

        $countsql = "SELECT count(*) as contact_count FROM contact as a WHERE a.contact_off = '$cat'  ".$team_query." ";

    }
    else
    {
        
        $sql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team ,(SELECT GROUP_CONCAT(s.sub_team_name SEPARATOR ',') FROM sub_teams as s where FIND_IN_SET(s.sub_team_id , a.sub_teams)) as sub_teams , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id where a.contact_off = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to))  ORDER BY a.created_date DESC LIMIT $next_page_id, 30";// GROUP BY a.contact_id

        $countsql = "SELECT count(*) as contact_count FROM contact as a WHERE a.contact_off = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) )";
    
        
    }


    $contacts = $db->getAllRecords($sql);

    $contact_count = 0;
    $contact_countdata = $db->getAllRecords($countsql);
    if ($contact_countdata)
    {
         $contact_count = $contact_countdata[0]['contact_count'];

    }
    if ($contact_count>0)
    {
        $contacts[0]['contact_count']=$contact_count;
    }
    $contacts[0]['sql']=$sql;
    echo json_encode($contacts);
});

$app->get('/update_visited/:visited/:contact_id', function($visited,$contact_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE contact set visited = '$visited' where contact_id = $contact_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode("done");
});


$app->post('/search_contacts', function() use ($app) {
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
    $permissions = explode(",", $session['permissions']);

    $cat = $r->searchdata->contact_off;
    $next_page_id = $r->searchdata->next_page_id;    
    $role = $session['role'];

    // $searchsql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, c.area_name as area_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id  where a.contact_off = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";
    $searchsql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(s.sub_team_name SEPARATOR ',') FROM sub_teams as s where FIND_IN_SET(s.sub_team_id , a.sub_teams)) as sub_teams ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id  where a.contact_off = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";
    
    $countsql = "SELECT count(*) as contact_count FROM contact as a  WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";
    // $countsql = "SELECT count(*) as contact_count FROM contact as a  WHERE and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";
    
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $searchsql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(s.sub_team_name SEPARATOR ',') FROM sub_teams as s where FIND_IN_SET(s.sub_team_id , a.sub_teams)) as sub_teams ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id  where a.contact_off = '$cat'  ";
        // $searchsql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, c.area_name as area_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id  where a.contact_off = '$cat'  ";

        $countsql = "SELECT count(*) as contact_count FROM contact as a WHERE a.contact_off = '$cat'  ";
    }

    else if (in_array("Branch Head", $role))
    {
        $searchsql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(s.sub_team_name SEPARATOR ',') FROM sub_teams as s where FIND_IN_SET(s.sub_team_id , a.sub_teams)) as sub_teams ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id  where a.contact_off = '$cat'  ";
        // $searchsql = "SELECT *,prefix,concat(a.contact_off,'_',a.contact_id) as contact_code,concat(a.name_title,' ',a.f_name,' ',a.l_name) as name ,SUBSTRING(a.f_name,1,1) as f_char ,a.add1,a.company_name, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date , CONCAT(b.locality,' ',c.area_name,' ',d.city) AS location_name, c.area_name as area_name, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'residential') as residential_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'commercial') as commercial_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'retail') as retail_properties_count, (SELECT count(*) from property as t where a.contact_id = t.dev_owner_id and t.proptype = 'pre-leased') as pre_leased_properties_count, (SELECT count(*) from project as t where a.contact_id = t.developer_id) as project_count, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , (SELECT filenames FROM attachments WHERE a.contact_id = category_id and category='contact' LIMIT 1) as filenames from contact as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c ON a.area_id = c.area_id LEFT JOIN city as d ON c.city_id = d.city_id  where a.contact_off = '$cat'  ";

        $countsql = "SELECT count(*) as contact_count FROM contact as a WHERE a.contact_off = '$cat'  ";
    }

    $sql = " ";
    if (isset($r->searchdata->company_name))
    {
        $first = "Yes";
        $new_data = $r->searchdata->company_name;
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
                $sql .= " a.company_name LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->name))
    {
        $first = "Yes";
        $new_data = $r->searchdata->name;
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
                $sql .= " CONCAT(a.name_title,' ',a.f_name,' ',a.l_name) LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->mob_no))
    {
        $first = "Yes";
        $new_data = $r->searchdata->mob_no;
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
                $sql .= " a.mob_no LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->area_id))
    {
        $first = "Yes";
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
                $sql .= " a.area_id = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->locality_id))
    {
        $first = "Yes";
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
                $sql .= " a.locality_id = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    if (isset($r->searchdata->city_id))
    {
        $first = "Yes";
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
                $sql .= " a.city_id = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->client_type))
    {
        $first = "Yes";
        $new_data = $r->searchdata->client_type;
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
                $sql .= " a.client_type = '".$value."' ";
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
                $sql .= " a.contact_id = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }
    if (isset($r->searchdata->developer_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->developer_id;
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
                $sql .= " a.contact_id = '".$value."' ";
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
                $sql .= " a.contact_id = '".$value."' ";
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
                $sql .= " a.sub_teams LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->groups))
    {
        $first = "Yes";
        $new_data = $r->searchdata->groups;
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
                $sql .= " a.groups LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
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

    if (isset($r->searchdata->registration_date_from))
    {
        $registration_date_from = $r->searchdata->registration_date_from;
        $tregistration_date_from = substr($registration_date_from,6,4)."-".substr($registration_date_from,3,2)."-".substr($registration_date_from,0,2) ." 00:00:00";
        if (isset($r->searchdata->registration_date_to))
        {
            $registration_date_to = $r->searchdata->registration_date_to;
            $tregistration_date_to = substr($registration_date_to,6,4)."-".substr($registration_date_to,3,2)."-".substr($registration_date_to,0,2)." 23:59:59";
        }
        $sql .= " and a.created_date BETWEEN '".$tregistration_date_from."' AND '".$tregistration_date_to."' ";
    }

    if (isset($r->searchdata->alt_phone_no))
    {
        $first = "Yes";
        $new_data = $r->searchdata->alt_phone_no;
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
                $sql .= " a.alt_phone_no = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->off_phone))
    {
        $first = "Yes";
        $new_data = $r->searchdata->off_phone;
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
                $sql .= " a.off_phone = '".$value."' ";
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
                $sql .= " a.email = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->visited))
    {
        if ($r->searchdata->visited=='Yes')
        {
            $sql .= " AND a.visited = 'Yes' ";
        }
        else if ($r->searchdata->visited=='No')
        {
            $sql .= " AND a.visited != 'Yes' ";
        }
    }


    $searchsql .=  $sql .  "  ORDER BY a.created_date DESC LIMIT $next_page_id, 30"; // GROUP BY a.contact_id

    $contacts = $db->getAllRecords($searchsql);
    $countsql = $countsql . $sql;
    $contact_count = 0;
    $contactcountdata = $db->getAllRecords($countsql);
    if ($contactcountdata)
    {
         $contact_count = $contactcountdata[0]['contact_count'];

    }
    //if ($contact_count>0)
    //{
        $contacts[0]['contact_count']=$contact_count;
    //}
    //$contacts[0]['sql']=$searchsql;
    echo json_encode($contacts);
});

$app->get('/getdatavalues_contact/:field_name/:cat', function($field_name,$cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $sql = "SELECT DISTINCT $field_name FROM contact WHERE contact_off = '$cat' ORDER BY $field_name";

    if ($field_name=="name")
    {
        $sql = "SELECT DISTINCT CONCAT(name_title,' ',f_name,' ',l_name) as name FROM contact WHERE contact_off = '$cat' ORDER BY f_name,l_name";
    }
    //echo $sql;
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
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
    verifyRequiredParams(array('contact_off'),$r->contact);
    //$db = new DbHandler();
    //$rera = $r->contact->rera_no;
    $lname = $r->contact->l_name;
    $fname = $r->contact->f_name;
    
    
    $mob_no = $r->contact->mob_no;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->contact->created_by = $created_by;
    $r->contact->created_date = $created_date;

    if (isset($r->contact->birth_date))
    {
        $birth_date = $r->contact->birth_date;
        $tbirth_date = substr($birth_date,6,4)."-".substr($birth_date,3,2)."-".substr($birth_date,0,2);
        $r->contact->birth_date = $tbirth_date;
    }


    $contact_off = $r->contact->contact_off;
    if (isset($r->contact->mob_no))
    {
        $iscontactExists = $db->getOneRecord("select 1 from contact where mob_no='$mob_no' and contact_off = '$contact_off' ");
        if($iscontactExists){
            $response["status"] = "error";
            $response["message"] = "Mobile Number already Registered .. !!!";
            echoResponse(201, $response);
            return;
        }
    }
    $email = "";
    if (isset($r->contact->email))
    {
        $email = $r->contact->email;
        $iscontactExists = $db->getOneRecord("select 1 from contact where email='$email' and contact_off = '$contact_off' ");
        if($iscontactExists){
            $response["status"] = "error";
            $response["message"] = "Email ID already Registered .. !!!";
            echoResponse(201, $response);
            return;
        }
    }
    $iscontactExists = $db->getOneRecord("select 1 from contact where f_name='$fname' and l_name='$lname' and mob_no = '$mob_no' and contact_off = '$contact_off' " );
    if(!$iscontactExists){
        $tabble_name = "contact";
        $column_names = array('company_name','contact_off','add1','add2','locality_id','area_id','zip','comp_logo','name_title','f_name','l_name','mob_no','mob_no1','email','alt_phone_no','alt_phone_no1','contact_pic','designation','birth_date','teams','sub_teams','assign_to','groups','rera_no','gst_no','comments','testimonial','dnd','other_phone','pan_no','tan_no','aadhar_no','occupation','off_email','off_phone','off_phone1','off_phone2','off_fax','off_add1','off_locality','off_area','off_zip','source_channel','source_sub_channel','reg_date','website','rating','opp_area','about','invoice_name','created_by','created_date');
         $multiple=array("teams","sub_teams","assign_to","groups","source_channel","source_sub_channel","opp_area");

        /*$history = $db->historydata( $r->contact, $column_names, $tabble_name,$condition,$contact_id,$multiple, $modified_by, $modified_date);*/
        
        $result = $db->insertIntoTable($r->contact, $column_names, $tabble_name, $multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Contact created successfully";
            $response["contact_id"] = $result;
            $_SESSION['tmpcontact_id'] = $result;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create Contact. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "A Contact with the provided Contact Details already exists!";
        echoResponse(201, $response);
    }

});

$app->post('/contact_uploads', function() use ($app) {
    session_start();
    $contact_id = $_SESSION['tmpcontact_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_company_logo'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_company_logo'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "c_".$contact_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "contact". $ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            
            
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, created_by, created_date)   VALUES('contact', '$contact_id','$file_names','Yes','Yes','Main','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->post('/contact_uploads_documents', function() use ($app) {
    session_start();
    $contact_id = $_SESSION['tmpcontact_id'];
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
        $file_names = "cd_".$contact_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "contact". $ds. "documents" . $ds . $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            
            
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, created_by, created_date)   VALUES('contact_documents', '$contact_id','$file_names','Yes','Yes','Main','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->post('/contact_uploads_visiting_card', function() use ($app) {
    session_start();
    $contact_id = $_SESSION['tmpcontact_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_visiting_card'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_visiting_card'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "cv_".$contact_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "contact". $ds. "visiting_card" .$ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, created_by, created_date)   VALUES('contact_visiting_card', '$contact_id','$file_names','Yes','Yes','Main','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->post('/contact_uploads_contact_pic', function() use ($app) {
    session_start();
    $contact_id = $_SESSION['tmpcontact_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_contact_pic'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_contact_pic'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "cp_".$contact_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "contact". $ds. "contact_pic" .$ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, created_by, created_date)   VALUES('contact_contact_pic', '$contact_id','$file_names','Yes','Yes','Main','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});



$app->get('/contact_edit_ctrl/:contact_id', function($contact_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();

    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *, DATE_FORMAT(birth_date,'%d/%m/%Y') AS birth_date from contact where contact_id=".$contact_id;
    $contacts = $db->getAllRecords($sql);
    echo json_encode($contacts);
    
});

$app->get('/contact_images/:contact_id', function($contact_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'contact' and category_id = $contact_id";
    $contact_images = $db->getAllRecords($sql);
    echo json_encode($contact_images);
});

$app->get('/contact_images_documents/:contact_id', function($contact_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'contact_documents' and category_id = $contact_id";
    $contact_images_documents = $db->getAllRecords($sql);
    echo json_encode($contact_images_documents);
});

$app->get('/contact_contact_pic/:contact_id', function($contact_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'contact_contact_pic' and category_id = $contact_id";
    $contact_pics = $db->getAllRecords($sql);
    echo json_encode($contact_pics);
});

$app->get('/contact_visiting_card/:contact_id', function($contact_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'contact_visiting_card' and category_id = $contact_id";
    $visiting_cards = $db->getAllRecords($sql);
    echo json_encode($visiting_cards);
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
     verifyRequiredParams(array('contact_id'),$r->contact);
  
    $contact_id  = $r->contact->contact_id;

    $lname = $r->contact->l_name;
    $fname = $r->contact->f_name;
    $mob_no = $r->contact->mob_no;

    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->contact->modified_by = $modified_by;
    $r->contact->modified_date = $modified_date;
    
    if (isset($r->contact->birth_date))
    {
        $birth_date = $r->contact->birth_date;
        $tbirth_date = substr($birth_date,6,4)."-".substr($birth_date,3,2)."-".substr($birth_date,0,2);
        $r->contact->birth_date = $tbirth_date;
    }


    $contact_off = $r->contact->contact_off;

    $iscontactExists = $db->getOneRecord("select 1 from contact where contact_id != $contact_id and f_name = '$fname' and l_name = '$lname' and mob_no = '$mob_no' and contact_off = '$contact_off' ");
    if($iscontactExists){
        $response["status"] = "error";
        $response["message"] = "Duplicate Contact ...!!";
        echoResponse(201, $response);
        return;
    }
    $iscontactExists = $db->getOneRecord("select 1 from contact where contact_id=$contact_id");
    if($iscontactExists){
        $tabble_name = "contact";
        $column_names = array('company_name','contact_off','add1','add2','locality_id','area_id','zip','comp_logo','name_title','f_name','l_name','mob_no','mob_no1','email','alt_phone_no','alt_phone_no1','contact_pic','designation','birth_date','teams','sub_teams','assign_to','groups','rera_no','gst_no','comments','testimonial','dnd','other_phone','pan_no','tan_no','aadhar_no','occupation','off_email','off_phone','off_phone1','off_phone2','off_fax','off_add1','off_locality','off_area','off_zip','source_channel','source_sub_channel','reg_date','website','rating','opp_area','about','invoice_name','modified_by','modified_date');
   
        $multiple=array("teams","sub_teams","assign_to","groups","source_channel","source_sub_channel","opp_area");
        $condition = "contact_id='$contact_id'";

        $history = $db->historydata( $r->contact, $column_names, $tabble_name,$condition,$contact_id,$multiple, $modified_by, $modified_date);
        
        $result = $db->NupdateIntoTable($r->contact, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "contact Updated successfully";
             $_SESSION['tmpcontact_id'] = $contact_id;
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
    verifyRequiredParams(array('contact_id'),$r->contact);
    $db = new DbHandler();
    $contact_id  = $r->contact->contact_id;
    $contact = $r->contact->contact_id;
    $iscontactExists = $db->getOneRecord("select 1 from contact where contact_id=$contact_id");
    if($iscontactExists){
        $tabble_name = "contact";
        $column_names = array('company_name','contact_off','add1','add2','locality_id','area_id','zip','comp_logo','name_title','f_name','l_name','mob_no','mob_no1','email','alt_phone_no','alt_phone_no1','contact_pic','designation','birth_date','team','assign_to','groups','rera_no','gst_no','comments','dnd','other_phone','pan_no','tan_no','occupation','off_email','off_phone','off_phone1','off_phone2','off_fax','off_add1','off_locality','off_area','off_city','off_state','off_country','off_zip','source_channel','source_sub_channel','reg_date','website','rating','opp_city','opp_area','about','invoice_name');
        $condition = "contact_id='$contact_id'";
        $result = $db->deleteIntoTable($r->contact, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Contact Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete contact. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Contact with the provided contact does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/create_new_contact/:contact_id', function($contact_id) use ($app) {
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
    "INSERT INTO contact(prefix,  contact_code, contact_off, name, company_name, add1, add2, locality_id, area_id, city, state, country, zip, comp_logo, name_title, f_name, l_name, mob_no, mob_no1, email, alt_phone_no, alt_phone_no1, contact_pic, designation, birth_date, teams,'sub_teams', assign_to, groups, rera_no, gst_no, comments, testimonial, dnd, other_phone, pan_no, tan_no, aadhar_no, occupation, off_email, off_phone, off_phone1, off_phone2, off_fax, off_add1, off_locality, off_area, off_city, off_state, off_country, off_zip, source_channel, source_sub_channel, reg_date, website, rating, opp_city, opp_area, about, invoice_name, created_by, created_date, modified_by, modified_date)  SELECT prefix, contact_code, contact_off, name, company_name, add1, add2, locality_id, area_id, city, state, country, zip, comp_logo, name_title, f_name, l_name, mob_no, mob_no1, email, alt_phone_no, alt_phone_no1, contact_pic, designation, birth_date, teams, sub_teams, assign_to, groups, rera_no, gst_no, comments, testimonial, dnd, other_phone, pan_no, tan_no, aadhar_no, occupation, off_email, off_phone, off_phone1, off_phone2, off_fax, off_add1, off_locality, off_area, off_city, off_state, off_country, off_zip, source_channel, source_sub_channel, reg_date, website, rating, opp_city, opp_area, about, invoice_name, created_by, created_date, modified_by, modified_date FROM contact WHERE contact_id = $contact_id";
    $result = $db->insertByQuery($sql);
    $response["contact_id"] = $result;
    $new_contact_id = $result;
    $sql = "UPDATE contact set created_by = '$created_by' , created_date = '$created_date' , modified_date = '$modified_date' WHERE contact_id = $new_contact_id";
    $result = $db->updateByQuery($sql);
    $ds = DIRECTORY_SEPARATOR;
    $sql = "SELECT * from attachments WHERE category = 'Contact' and contact_id = $contact_id";
    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        $count = 1;
        $ext = 'jpg';
        while($row = $stmt->fetch_assoc())
        {
            $source_file_names = $row['filenames'];
            $source = dirname( __FILE__ ). $ds."uploads" . $ds . "contact". $ds. $source_file_names;

            $source_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "contact". $ds. "thumb" .$ds. $source_file_names;

            $file_names = "c_".$new_contact_id."_".time()."_".$count.".".$ext;
            $target = dirname( __FILE__ ). $ds."uploads" . $ds . "contact". $ds. $file_names;
            $target_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "contact". $ds. "thumb" .$ds. $file_names;

            copy($source,$target);
            copy($source_thumb,$target_thumb);

            $share_on_web = $row['share_on_web'];
            $isdefault = $row['isdefault'];
            $description = $row['description'];
            $file_category = $row['$file_category'];

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('contact', '$new_contact_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$created_by', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        }
    }

    $response["status"] = "success";
    $response["message"] = "Contact Created !!!";
    echo json_encode($response);

});

$app->get('/selectcontact/:cat', function($cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    $user_id = $session['user_id'];
    if ($session['username']=="Guest")
    {
        return;
    }
    
    // $sql = "SELECT contact_id,contact_off, CONCAT(name_title,' ',f_name,' ',l_name,'   ',mob_no,'  ',company_name) as name from contact WHERE contact_off = '$cat' ORDER BY f_name,l_name";
    $sql = "SELECT contact_id,contact_off, CONCAT(name_title,' ',f_name,' ',l_name,' ',mob_no,'  ',company_name) as name from contact WHERE contact_off = '$cat' and (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) ORDER BY f_name,l_name";
    if ($cat == 'all')
    {
        // $sql = "SELECT contact_id,contact_off, CONCAT(name_title,' ',f_name,' ',l_name,'   ',mob_no,'  ',company_name) as name from contact  ORDER BY f_name,l_name";
        $sql = "SELECT contact_id,contact_off, CONCAT(name_title,' ',f_name,' ',l_name,'   ',mob_no,'  ',company_name) as name from contact  WHERE (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) ORDER BY f_name,l_name";
    }
    $contacts = $db->getAllRecords($sql);
    echo json_encode($contacts);
});

$app->get('/selectcontact_with_broker/:cat', function($cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT contact_id,contact_off, CONCAT(name_title,' ',f_name,' ',l_name,'   ',mob_no,'  ',company_name) as name from contact WHERE (contact_off = 'client' or contact_off = 'broker')  ORDER BY f_name,l_name";
    $contacts = $db->getAllRecords($sql);
    echo json_encode($contacts);
});


$app->get('/checkcontact/:field/:field_name', function($field,$field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "";
    $response = array();

    if ($field_name=="mob_no")
    {
        $sql = "SELECT * from contact WHERE mob_no = '$field' or  mob_no1 = '$field' ";
    }
    if ($field_name=="email")
    {
        $sql = "SELECT * from contact WHERE email = '$field' ";
    }
    $iscontactExists = $db->getOneRecord($sql);
    $found = "No";
    if($iscontactExists)
    {
        $found = "Yes";
    }
    $senddata =  array();
    $htmldata = array();
    
    $htmldata['found']=$found;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/getclientemail/:contact_off/:contact_id', function($contact_off,$contact_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT  mob_no as client_mobile_number , email as client_email_id from contact WHERE contact_off = '$contact_off' and contact_id = $contact_id ";
    $contacts = $db->getAllRecords($sql);
    echo json_encode($contacts);
});


$app->get('/getowner_names/:dev_owner_name/:cat', function($dev_owner_name,$cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT contact_id,contact_off, CONCAT(name_title,' ',f_name,' ',l_name,'   ',mob_no,'  ',company_name) as name from contact WHERE (company_name LIKE '%$dev_owner_name%' or f_name LIKE '%$dev_owner_name%' or l_name LIKE '%$dev_owner_name%' or mob_no LIKE '%$dev_owner_name%') and contact_off = '$cat' ORDER BY name LIMIT 20 ";

    if ($dev_owner_name=='blank')
    {
        //$sql = "SELECT contact_id,contact_off, CONCAT(name_title,' ',f_name,' ',l_name,'   ',mob_no,'  ',company_name) as name from contact WHERE contact_off = '$cat' ORDER BY company_name LIMIT 20 ";
        $sql = "SELECT * from contact LIMIT 0";
    }

    $senddata =  array();
    $htmldata = array();
    $htmlstring = '';

    $htmlstring = '<ul class="mydropdown-menu" style="display:block;">';
    
    $stmt = $db->getRows($sql);
    $count = 0;
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<li><a href="javascript:void(0)" ng-click = "getcustomername('.$row['contact_id'].',\''.$row['name'].'\')">'.$row['name'].' - '.$row['mob_no'].'</a></li>';
            $count = $count + 1;
   
        }
    }
    $htmlstring .='</ul>';

    $htmldata['htmlstring']=$htmlstring;
    $htmldata['count']=$count;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

?>

