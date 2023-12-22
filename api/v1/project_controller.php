<?php
// PROJECT 

$app->get('/project_list_ctrl/:id/:next_page_id', function ($id, $next_page_id) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";
    $tteams = $session['teams'];
    foreach ($tteams as $value) {
        $team_query .= " OR a.teams LIKE '" . $value . "' ";
    }

    $user_id = $session['user_id'];
    $permissions = $session['permissions'];
    $role = $session['role'];

    $sql = "SELECT * from project LIMIT 0";
    $countsql = "SELECT count(*) as project_count from project";
    $contact_sql = "";
    if ($id == 0) {

    } else {
        $contact_sql = " and a.developer_id = $id ";
    }

    if (in_array("Admin", $role) || in_array("Sub Admin", $role)) {
        // $sql = "SELECT *,e.company_name as developer,SUBSTRING(e.f_name,1,1) as f_char,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,a.mob1,a.email, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT CONCAT(max(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price , (SELECT CONCAT(max(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as max_pack_price,(SELECT CONCAT(min(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as min_pack_price , (SELECT CONCAT(max(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as max_carp,(SELECT CONCAT(min(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as min_carp ,   (SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by FROM project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id WHERE a.market_project != 1 ".$contact_sql." ORDER BY a.modified_date DESC LIMIT $next_page_id,30"; //GROUP BY a.project_id 
        $sql = "SELECT *,e.company_name as developer,SUBSTRING(e.f_name,1,1) as f_char,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,a.mob1,a.email, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.modified_by) as modified_by,(SELECT CONCAT(max(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price , (SELECT CONCAT(max(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as max_pack_price,(SELECT CONCAT(min(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as min_pack_price , (SELECT CONCAT(max(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as max_carp,(SELECT CONCAT(min(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as min_carp ,   (SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by FROM project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id WHERE a.market_project != 1 " . $contact_sql . " ORDER BY a.modified_date DESC LIMIT $next_page_id,30"; //GROUP BY a.project_id 
        $countsql = "SELECT count(*)  as project_count from project WHERE a.market_project != 1  " . $contact_sql . "  ";

    } else {

        // $sql = "SELECT *,e.company_name as developer,SUBSTRING(e.f_name,1,1) as f_char,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact, a.mob1,a.email, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT CONCAT(max(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price, (SELECT CONCAT(max(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as max_pack_price,(SELECT CONCAT(min(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as min_pack_price ,(SELECT CONCAT(max(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and CAST(carp_area as UNSIGNED) > 0 ) as max_carp,(SELECT CONCAT(min(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and CAST(carp_area as UNSIGNED) > 0 ) as min_carp ,  (SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE a.market_project != 1  ".$contact_sql."  and  (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query.") ORDER BY a.modified_date DESC LIMIT $next_page_id,30"; // GROUP BY a.project_id 
        $sql = "SELECT *,e.company_name as developer,SUBSTRING(e.f_name,1,1) as f_char,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact, a.mob1,a.email, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.modified_by) as modified_by,(SELECT CONCAT(max(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price, (SELECT CONCAT(max(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as max_pack_price,(SELECT CONCAT(min(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as min_pack_price ,(SELECT CONCAT(max(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and CAST(carp_area as UNSIGNED) > 0 ) as max_carp,(SELECT CONCAT(min(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and CAST(carp_area as UNSIGNED) > 0 ) as min_carp ,  (SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE a.market_project != 1  " . $contact_sql . "  and  (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) " . $team_query . ") ORDER BY a.modified_date DESC LIMIT $next_page_id,30"; // GROUP BY a.project_id 
        $countsql = "SELECT count(*)  as project_count from project as a WHERE a.market_project != 1  " . $contact_sql . "  and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) " . $team_query . ") ";

    }
    
    $projects = $db->getAllRecords($sql);

    $projectcountdata = $db->getAllRecords($countsql);
    $project_count = 0;
    if ($projectcountdata) {
        $project_count = $projectcountdata[0]['project_count'];

    }
    if ($project_count > 0) {
        $projects[0]['project_count'] = $project_count;
    }
    $projects[0]['sql'] = $sql;
    echo json_encode($projects);


});


$app->get('/mproject_list/:next_page_id', function ($next_page_id) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";
    $tteams = $session['teams'];
    foreach ($tteams as $value) {
        $team_query .= " OR a.teams LIKE '" . $value . "' ";
    }

    $user_id = $session['user_id'];
    $permissions = $session['permissions'];
    $role = $session['role'];

    $sql = "SELECT * from project LIMIT 0";
    //$countsql = "SELECT count(*) as project_count from project";
// pks here commented if else condition 22042023
    // if (in_array("Admin", $role))
    // {   
    $sql = "SELECT *,e.company_name as developer,SUBSTRING(e.f_name,1,1) as f_char,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,a.mob1,a.email, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT CONCAT(max(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price , (SELECT CONCAT(max(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as max_pack_price,(SELECT CONCAT(min(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as min_pack_price , (SELECT CONCAT(max(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as max_carp,(SELECT CONCAT(min(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as min_carp ,   (SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by FROM project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id WHERE a.market_project = 1 ORDER BY a.modified_date DESC LIMIT $next_page_id,30"; //GROUP BY a.project_id 
    //     $sql="SELECT 
    //     *,
    //     e.company_name AS developer,
    //     SUBSTRING(e.f_name, 1, 1) AS f_char,
    //     a.pack_price,
    //     CONCAT(a.salu, ' ', a.fname, ' ', a.lname) AS project_contact,
    //     a.mob1,
    //     a.email,
    //     DATE_FORMAT(a.possession_date, '%d-%m-%Y') AS possession_date,
    //     (
    //         SELECT GROUP_CONCAT(CONCAT(k.salu, ' ', k.fname, ' ', k.lname) SEPARATOR ',') 
    //         FROM users AS j 
    //         LEFT JOIN employee AS k ON k.emp_id = j.emp_id 
    //         WHERE FIND_IN_SET(j.user_id, a.assign_to)
    //     ) AS assign_to,
    //     (
    //         SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') 
    //         FROM teams AS h 
    //         WHERE FIND_IN_SET(h.team_id, a.teams)
    //     ) AS team,
    //     (
    //         SELECT COUNT(*) 
    //         FROM attachments AS t 
    //         WHERE t.category_id = a.project_id AND t.category = 'project'
    //     ) AS project_image_count,
    //     a.add1,
    //     a.add2,
    //     DATE_FORMAT(a.created_date, '%d-%m-%Y %H:%i') AS created_date,
    //     DATE_FORMAT(a.modified_date, '%d-%m-%Y %H:%i') AS modified_date,
    //     CONCAT(e.name_title, ' ', e.f_name, ' ', e.l_name) AS developer_name,
    //     f.locality AS locality,
    //     g.area_name AS area_name,
    //     (
    //         SELECT CONCAT(MAX(exp_price), ' ', MAX(exp_price_para)) 
    //         FROM property 
    //         WHERE project_id = a.project_id AND exp_price > 0
    //     ) AS max_price,
    //     (
    //         SELECT CONCAT(MIN(exp_price), ' ', MIN(exp_price_para)) 
    //         FROM property 
    //         WHERE project_id = a.project_id AND exp_price > 0
    //     ) AS min_price,
    //     (
    //         SELECT CONCAT(MAX(pack_price), ' ', MAX(pack_price_para)) 
    //         FROM property 
    //         WHERE project_id = a.project_id AND pack_price > 0
    //     ) AS max_pack_price,
    //     (
    //         SELECT CONCAT(MIN(pack_price), ' ', MIN(pack_price_para)) 
    //         FROM property 
    //         WHERE project_id = a.project_id AND pack_price > 0
    //     ) AS min_pack_price,
    //     (
    //         SELECT CONCAT(MAX(CAST(carp_area AS UNSIGNED)), ' ', MAX(carp_area_para)) 
    //         FROM property 
    //         WHERE project_id = a.project_id AND carp_area > 0
    //     ) AS max_carp,
    //     (
    //         SELECT CONCAT(MIN(CAST(carp_area AS UNSIGNED)), ' ', MIN(carp_area_para)) 
    //         FROM property 
    //         WHERE project_id = a.project_id AND carp_area > 0
    //     ) AS min_carp,
    //     (
    //         SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') 
    //         FROM project AS q 
    //         LEFT JOIN property AS r ON q.project_id = r.project_id 
    //         WHERE r.bedrooms IS NOT NULL AND r.bedrooms > 0 AND r.project_id = a.project_id
    //     ) AS bedroom_list,
    //     (
    //         SELECT filenames 
    //         FROM attachments 
    //         WHERE a.project_id = category_id AND category = 'Project' AND isdefault = 'true' 
    //         LIMIT 1
    //     ) AS filenames,
    //     (
    //         SELECT CONCAT(t.salu, ' ', t.fname, ' ', t.lname) 
    //         FROM users AS s 
    //         LEFT JOIN employee AS t ON t.emp_id = s.emp_id 
    //         WHERE s.user_id = a.created_by
    //     ) AS created_by
    // FROM 
    //     project AS a
    // LEFT JOIN 
    //     contact AS e ON a.developer_id = e.contact_id
    // LEFT JOIN 
    //     locality AS f ON a.locality_id = f.locality_id
    // LEFT JOIN 
    //     areas AS g ON a.area_id = g.area_id
    // WHERE 
    //     a.market_project = 1
    // ORDER BY 
    //     a.modified_date DESC LIMIT $next_page_id,30";
    //$countsql = "SELECT count(*)  as project_count from project ";

    // }
    // else
    // {

    // $sql = "SELECT *,e.company_name as developer,SUBSTRING(e.f_name,1,1) as f_char,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact, a.mob1,a.email, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT CONCAT(max(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price, (SELECT CONCAT(max(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as max_pack_price,(SELECT CONCAT(min(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as min_pack_price , (SELECT CONCAT(max(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and CAST(carp_area as UNSIGNED) > 0 ) as max_carp,(SELECT CONCAT(min(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and CAST(carp_area as UNSIGNED) > 0 ) as min_carp ,  (SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE a.market_project = 1 and  (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query.") ORDER BY a.modified_date DESC LIMIT $next_page_id,30"; // GROUP BY a.project_id 

    //$countsql = "SELECT count(*)  as project_count from project as a WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query.") ";

    // }

    $projects = $db->getAllRecords($sql);

    // print_r($projects);
    // exit;
    /*$projectcountdata = $db->getAllRecords($countsql);
    $project_count = 0;
    if ($projectcountdata)
    {
         $project_count = $projectcountdata[0]['project_count'];

    }
    if ($project_count>0)
    {
        $projects[0]['project_count']=$project_count;
    }*/
    $projects[0]['sql'] = $sql;
    echo json_encode($projects);


});


$app->get('/mproject_list', function () use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];
    $role = $session['role'];
    $sql = "SELECT *,e.company_name as developer,SUBSTRING(e.f_name,1,1) as f_char,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN contact as e on a.developer_id = e.contact_id LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id WHERE a.created_by = $user_id and a.market_project = '1' GROUP BY a.project_id ORDER BY a.modified_date DESC LIMIT 30";

    $mprojects = $db->getAllRecords($sql);
    echo json_encode($mprojects);
});

$app->post('/project_add_new', function () use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('project_name'), $r->project);
    $db = new DbHandler();
    $project_name = $r->project->project_name;
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->project->created_by = $created_by;
    $r->project->created_date = $created_date;
    $r->project->modified_date = $created_date;
    if (isset($r->project->possession_date)) {
        $possession_date = $r->project->possession_date;
        $tpossession_date = substr($possession_date, 6, 4) . "-" . substr($possession_date, 3, 2) . "-" . substr($possession_date, 0, 2);
        $r->project->possession_date = $tpossession_date;
    }

    if (isset($r->project->completion_date)) {
        $completion_date = $r->project->completion_date;
        $tcompletion_date = substr($completion_date, 6, 4) . "-" . substr($completion_date, 3, 2) . "-" . substr($completion_date, 0, 2);
        $r->project->completion_date = $tcompletion_date;
    }

    if (isset($r->project->park_charge)) {
        if (isset($r->project->park_charge_para)) {
            $park_charge = ($r->project->park_charge);
            $r->project->park_charge = $park_charge;
        }
    }
    if (isset($r->project->maintenance_charges)) {
        if (isset($r->project->main_charge_para)) {
            $maintenance_charges = ($r->project->maintenance_charges);
            $r->project->maintenance_charges = $maintenance_charges;
        }
    }
    if (isset($r->project->prop_tax)) {
        if (isset($r->project->prop_tax_para)) {
            $prop_tax = ($r->project->prop_tax);
            $r->project->prop_tax = $prop_tax;
        }
    }
    if (isset($r->project->transfer_charge)) {
        if (isset($r->project->transfer_charge_para)) {
            $transfer_charge = ($r->project->transfer_charge);
            $r->project->transfer_charge = $transfer_charge;
        }
    }

    $isProjectExists = $db->getOneRecord("select 1 from project where project_name='$project_name'");
    if (!$isProjectExists) {
        $tabble_name = "project";
        $column_names = array('project_name', 'developer_id', 'developer_name', 'project_for', 'con_status', 'possession_date', 'completion_date', 'rera_num', 'add1', 'add2', 'exlocation', 'locality_id', 'area_id', 'road_no', 'landmark', 'city', 'state', 'country', 'zip', 'lattitude', 'longitude', 'salu', 'fname', 'lname', 'mob1', 'mob2', 'email', 'designation', 'salu_2', 'fname_2', 'lname_2', 'mob1_2', 'mob2_2', 'email_2', 'designation_2', 'tot_area', 'tot_unit', 'rent_unit_carpet', 'pack_price', 'pack_price_comments', 'app_bankloan', 'amenities_avl', 'pro_specification', 'parking', 'car_park', 'park_charge', 'maintenance_charges', 'prop_tax', 'transfer_charge', 'teams', 'sub_teams', 'assign_to', 'source_channel', 'subsource_channel', 'groups', 'file_name', 'internal_comment', 'external_comment', 'numof_building', 'numof_floor', 'lifts', 'floor_rise', 'distfrm_station', 'distfrm_dairport', 'distfrm_highway', 'distfrm_school', 'distfrm_market', 'com_certification', 'youtube_link', 'rating', 'review', 'proj_status', 'pro_inspect', 'vastu_comp', 'occu_certi', 'soc_reg', 'cc', 'created_by', 'created_date', 'modified_date', 'mainroad', 'internalroad', 'area_parameter', 'project_type', 'park_charge_para', 'main_charge_para', 'transfer_charge_para', 'prop_tax_para', 'market_project', 'share_on_website', 'share_on_99', 'acers_99_projid', 'task_id', 'sms_saletrainee', 'email_saletrainee');

        $multiple = array("amenities_avl", "app_bankloan", "assign_to", "groups", "parking", "pro_specification", "source_channel", "subsource_channel", "teams", "sub_teams");
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
    } else {
        $response["status"] = "error";
        $response["message"] = "A project with the provided project exists!";
        echoResponse(201, $response);
    }
});

$app->get('/getdatavalues_project/:field_name', function ($field_name) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM project  ORDER BY $field_name";
    if ($field_name == 'bedrooms') {
        $sql = "SELECT DISTINCT b.bedrooms FROM project as a LEFT JOIN property as b ON a.project_id = b.project_id ORDER BY b.bedrooms";
    }
    //echo $sql;
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});

$app->post('/newsearch_projects', function () use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());

    $bo_id = $session['bo_id'];
    $teams = $session['teams'];
    $team_query = " ";
    $tteam_query = " ";
    $tteams = $session['teams'];
    foreach ($tteams as $value) {
        $team_query .= " OR a.teams LIKE '" . $value . "' ";
    }

    $user_id = $session['user_id'];
    $permissions = $session['permissions'];
    $next_page_id = $r->searchdata->next_page_id;

    $role = $session['role'];


    $searchsql = "SELECT a.*,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='project' and isdefault = 'true' LIMIT 1) as filenames from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN contact as e on a.developer_id = e.contact_id LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id WHERE a.created_by = $user_id ";

    $countsql = "SELECT count(*) as project_count FROM project ";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role)) {
        $searchsql = "SELECT a.*,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames, (SELECT CONCAT(max(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as max_pack_price,(SELECT CONCAT(min(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as min_pack_price , (SELECT CONCAT(max(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and CAST(carp_area as UNSIGNED) > 0 ) as max_carp,(SELECT CONCAT(min(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and CAST(carp_area as UNSIGNED) > 0 ) as min_carp,(SELECT CONCAT(u.salu,' ',u.fname,' ',u.lname) FROM users as s LEFT JOIN employee as u on u.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE a.created_by > 0 ";

        $countsql = "SELECT count(*) as project_count FROM project as a  WHERE a.created_by > 0  ";
    } else {
        $searchsql = "SELECT a.*,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames, (SELECT CONCAT(max(carp_area),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as max_carp,(SELECT CONCAT(min(carp_area),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as min_carp, (SELECT CONCAT(max(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as max_pack_price,(SELECT CONCAT(min(pack_price),' ',MAX(pack_price_para)) FROM property WHERE project_id= a.project_id and pack_price > 0 ) as min_pack_price ,(SELECT CONCAT(u.salu,' ',u.fname,' ',u.lname) FROM users as s LEFT JOIN employee as u on u.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id   LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) " . $team_query . " ) ";

        $countsql = "SELECT count(*) as project_count FROM project as a WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) " . $team_query . " ) ";

    }


    // TOP SEARCH 
    $sql = " ";

    if (isset($r->searchdata->con_status)) {
        $first = "Yes";
        $new_data = $r->searchdata->con_status;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.con_status LIKE '%" . $value . "%' ";
            }
        }
        if ($first == 'No') {
            $sql .= ") ";

        }
    }

    if (isset($r->searchdata->project_id)) {

        $first = 'Yes';
        $new_data = $r->searchdata->project_id;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.project_id = " . $value . " ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }
    if (isset($r->searchdata->proj_status)) {
        $first = "Yes";
        $new_data = $r->searchdata->proj_status;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.proj_status LIKE '%" . $value . "%' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }




    if (isset($r->searchdata->project_for)) {
        $sql .= " and a.project_for = '" . $r->searchdata->project_for . "' ";
    }

    if (isset($r->searchdata->proj_type)) {
        $first = "Yes";
        $new_data = $r->searchdata->proj_type;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.proj_type LIKE '%" . $value . "%' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }




    if (isset($r->searchdata->proj_type)) {
        $first = "Yes";
        $new_data = $r->searchdata->proj_type;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.proj_type LIKE '%" . $value . "%' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }

    }

    if (isset($r->searchdata->area_id)) {
        $first = "Yes";
        $new_data = $r->searchdata->area_id;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.area_id = " . $value . " ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }

    }

    if (isset($r->searchdata->locality_id)) {
        $first = "Yes";
        $new_data = $r->searchdata->locality_id;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.locality_id = " . $value . " ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }

    if (isset($r->searchdata->city_id)) {
        $first = "Yes";
        $new_data = $r->searchdata->city_id;
        if ($new_data) {

            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.city_id = " . $value . " ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }

    if (isset($r->searchdata->project_name)) {
        $first = "Yes";
        $new_data = $r->searchdata->project_name;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.project_name LIKE '%" . $value . "%' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }
    /*if (isset($r->searchdata->bedrooms))
    {
        $first = "Yes";
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
                $sql .= " l.bedrooms = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }*/
    if (isset($r->searchdata->numof_floor)) {
        $first = "Yes";
        $new_data = $r->searchdata->numof_floor;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.numof_floor = '" . $value . "' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }
    if (isset($r->searchdata->developer_id)) {
        $first = "Yes";
        $new_data = $r->searchdata->developer_id;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.developer_id = " . $value . " ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }


    }
    if (isset($r->searchdata->possession_date_from)) {
        $possession_date_from = $r->searchdata->possession_date_from;
        $tpossession_date_from = substr($possession_date_from, 6, 4) . "-" . substr($possession_date_from, 3, 2) . "-" . substr($possession_date_from, 0, 2);
        if (isset($r->searchdata->possession_date_to)) {
            $possession_date_to = $r->searchdata->possession_date_to;
            $tpossession_date_to = substr($possession_date_to, 6, 4) . "-" . substr($possession_date_to, 3, 2) . "-" . substr($possession_date_to, 0, 2);
        }
        $sql .= " and a.possession_date BETWEEN '" . $tpossession_date_from . "' AND '" . $tpossession_date_to . "' ";
    }

    if (isset($r->searchdata->created_date_from)) {
        $created_date_from = $r->searchdata->created_date_from;
        $tcreated_date_from = substr($created_date_from, 6, 4) . "-" . substr($created_date_from, 3, 2) . "-" . substr($created_date_from, 0, 2);
        if (isset($r->searchdata->created_date_to)) {
            $created_date_to = $r->searchdata->created_date_to;
            $tcreated_date_to = substr($created_date_to, 6, 4) . "-" . substr($created_date_to, 3, 2) . "-" . substr($created_date_to, 0, 2);
        }
        $sql .= " and a.created_date BETWEEN '" . $tcreated_date_from . "' AND '" . $tcreated_date_to . "' ";
    }


    if (isset($r->searchdata->source_channel)) {
        $first = "Yes";
        $new_data = $r->searchdata->source_channel;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.source_channel LIKE '%" . $value . "%' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }
    if (isset($r->searchdata->teams)) {
        $first = "Yes";
        $new_data = $r->searchdata->teams;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.teams LIKE '%" . $value . "%' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }

    if (isset($r->searchdata->sub_teams)) {
        $first = "Yes";
        $new_data = $r->searchdata->sub_teams;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.sub_teams LIKE '%" . $value . "%' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }


    if (isset($r->searchdata->assign_to)) {
        $first = "Yes";
        $new_data = $r->searchdata->assign_to;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.assign_to LIKE '%" . $value . "%' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }

    }

    if (isset($r->searchdata->created_by)) {
        $first = "Yes";
        $new_data = $r->searchdata->created_by;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.created_by LIKE '" . $value . "' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }

    }

    if (isset($r->searchdata->source_channel)) {
        $first = "Yes";
        $new_data = $r->searchdata->source_channel;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.source_channel LIKE '%" . $value . "%' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }

    if (isset($r->searchdata->subsource_channel)) {
        $first = "Yes";
        $new_data = $r->searchdata->subsource_channel;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.subsource_channel LIKE '%" . $value . "%' ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }
    }

    if (isset($r->searchdata->rera_num)) {
        $sql .= " OR a.rera_num LIKE '%" . $r->searchdata->rera_num . "%' ";
    }
    if (isset($r->searchdata->share_on_website)) {
        $sql .= " and a.share_on_website = '" . $r->searchdata->share_on_website . "' ";
    }
    if (isset($r->searchdata->tot_area) && isset($r->searchdata->tot_area1)) {
        $sql .= " and a.tot_area >= '" . $r->searchdata->tot_area . "' and a.tot_area <= '" . $r->searchdata->tot_area1 . "'";
    }

    if (isset($r->searchdata->share_website)) {
        if ($r->searchdata->share_website == 'Yes') {
            $sql .= " and a.share_on_website = '" . $r->searchdata->share_website . "' ";
        } else {

            $sql .= " and a.share_on_website != 'Yes' ";
        }

    }


    if (isset($r->searchdata->owner_id)) {
        $first = "Yes";
        $new_data = $r->searchdata->owner_id;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.dev_owner_id = " . $value . " ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }

    }



    if (isset($r->searchdata->broker_id)) {
        $first = "Yes";
        $new_data = $r->searchdata->broker_id;
        if ($new_data) {
            foreach ($new_data as $value) {
                if ($first == 'Yes') {
                    $sql .= " AND (";
                    $first = "No";
                } else {
                    $sql .= " OR ";
                }
                $sql .= " a.broker_id = " . $value . " ";
            }
            if ($first == 'No') {
                $sql .= ") ";

            }
        }

    }

    $searchsql .= $sql . " ORDER BY a.modified_date DESC LIMIT $next_page_id, 30"; //  GROUP BY a.project_id  CAST(a.property_id as UNSIGNED) DESC";
    $projects = $db->getAllRecords($searchsql);

    $countsql = $countsql . $sql;
    $project_count = 0;
    $projectcountdata = $db->getAllRecords($countsql);
    if ($projectcountdata) {
        $project_count = $projectcountdata[0]['project_count'];

    }
    //if ($project_count>0)
    //{
    $projects[0]['project_count'] = $project_count;
    //}
    $projects[0]['countsql'] = $countsql;
    //$projects[0]['searchsql']=$searchsql;
    //echo $sql;
    echo json_encode($projects);

});

$app->get('/getassignedprojects', function () use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];

    $teams = $session['teams'];
    $team_query = " ";
    $tteams = $session['teams'];
    foreach ($tteams as $value) {
        $team_query .= " OR a.teams LIKE '" . $value . "' ";
    }

    if (in_array("Admin", $role) || in_array("Sub Admin", $role)) {

        $sql = "SELECT CONCAT('PROJECTID_',a.project_id,' ',a.project_name) as project_title, a.project_id FROM project as a WHERE a.proj_status = 'Available' ORDER by a.project_id DESC ";
    } else {
        $sql = "SELECT CONCAT('PROJECTID_',a.project_id,' ',a.project_name) as project_title, a.project_id FROM project as a WHERE a.proj_status = 'Available' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) " . $team_query . " ) ORDER by a.project_id DESC";

    }

    $m_projects = $db->getAllRecords($sql);
    echo json_encode($m_projects);
});

// pks changes 15-05-2023
// $app->get('/project_count', function() use ($app) {
//     $sql  = "";
//     $db = new DbHandler();
//     $session = $db->getSession();
//     if ($session['username']=="Guest")
//     {
//         return;
//     }
//     $senddata =  array();
//     $htmldata = array();

//     $sql = "SELECT count(*) as project_count from project ";
//     $projectdata = $db->getAllRecords($sql);
//     $project_count = 0;
//     if ($projectdata)
//     {
//         $project_count = $projectdata[0]['project_count'];
//     }

//     $htmldata['project_count']=$project_count;
//     $senddata[]=$htmldata;
//     echo json_encode($senddata);
// });

$app->get('/project_count', function () use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $username = $session['username'];
    $user_id = $session['user_id'];
    $teams = $session['teams']['0'];

    // error_log($username, 3, "logfile.log");
    // error_log($user_id, 3, "logfile.log");
    // error_log($teams, 3, "logfile.log");
    $senddata = array();
    $htmldata = array();
    if ($username != "admin") {
        $sql = "SELECT count(*)  as project_count from project as a WHERE a.market_project != 1  and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) OR a.teams LIKE '" . $teams . "') ";
    } else {
        $sql = "SELECT count(*) as project_count from project as a WHERE a.market_project != 1";
    }
    $projectdata = $db->getAllRecords($sql);
    $project_count = 0;
    if ($projectdata) {
        $project_count = $projectdata[0]['project_count'];
    }

    $htmldata['project_count'] = $project_count;
    $senddata[] = $htmldata;
    echo json_encode($senddata);
});

$app->post('/search_projects', function () use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());

    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];

    $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='project' and isdefault = 'true' LIMIT 1) as filenames from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN contact as e on a.developer_id = e.contact_id LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id WHERE a.created_by = $user_id ";

    if (in_array("Admin Level", $permissions)) {
        $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE a.created_by > 0 ";
    }

    if (in_array("Branch Head Level", $permissions)) {
        $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,   (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to,  (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE z.bo_id = $bo_id ";
    }

    if (in_array("Branch User Level", $permissions)) {
        $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE z.bo_id = $bo_id ";
    }

    if (in_array("User Level", $permissions)) {
        $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE a.created_by = $user_id ";
    }

    if (isset($r->searchdata->assign_to)) {
        $assign_to = implode(",", $r->searchdata->assign_to);
        $sql .= " and a.assign_to in ('" . $assign_to . "') ";
    }

    if (isset($r->searchdata->source_channel)) {
        $source_channel = implode(",", $r->searchdata->source_channel);
        $sql .= " and a.source_channel in ('" . $source_channel . "') ";
    }

    if (isset($r->searchdata->subsource_channel)) {
        $subsource_channel = implode(",", $r->searchdata->subsource_channel);
        $sql .= " and a.subsource_channel in ('" . $subsource_channel . "') ";
    }

    if (isset($r->searchdata->teams)) {
        $teams = implode(",", $r->searchdata->teams);
        $sql .= " and a.teams in ('" . $teams . "') ";
    }

    if (isset($r->searchdata->area_id)) {
        $sql .= " and a.area_id = " . $r->searchdata->area_id . " ";
    }

    if (isset($r->searchdata->city_id)) {
        $sql .= " and a.city_id = '" . $r->searchdata->city_id . "' ";
    }

    if (isset($r->searchdata->locality_id)) {
        $sql .= " and a.locality_id = '" . $r->searchdata->locality_id . "' ";
    }

    if (isset($r->searchdata->developer_id)) {
        $sql .= " and a.developer_id = '" . $r->searchdata->developer_id . "' ";
    }

    if (isset($r->searchdata->tot_area)) {
        $sql .= " and a.tot_area = '" . $r->searchdata->tot_area . "' ";
    }

    if (isset($r->searchdata->con_status)) {
        $sql .= " and a.con_status = '" . $r->searchdata->con_status . "' ";
    }

    if (isset($r->searchdata->possession_date)) {
        $possession_date = $r->searchdata->possession_date;
        $tpossession_date = substr($possession_date, 6, 4) . "-" . substr($possession_date, 3, 2) . "-" . substr($possession_date, 0, 2);
        $sql .= " and a.possession_date = '" . $tpossession_date . "' ";
    }

    if (isset($r->searchdata->created_date)) {
        $created_date = $r->searchdata->created_date;
        $tcreated_date = substr($created_date, 6, 4) . "-" . substr($created_date, 3, 2) . "-" . substr($created_date, 0, 2);
        $sql .= " and a.created_date LIKE '" . $tcreated_date . "%' ";
    }

    if (isset($r->searchdata->proj_status)) {
        $sql .= " and a.proj_status = '" . $r->searchdata->proj_status . "' ";
    }

    if (isset($r->searchdata->project_type)) {
        $sql .= " and a.project_type = '" . $r->searchdata->project_type . "' ";
    }

    if (isset($r->searchdata->project_name)) {
        $sql .= " and a.project_name = '" . $r->searchdata->project_name . "' ";
    }

    if (isset($r->searchdata->numof_floor)) {
        $sql .= " and a.numof_floor = '" . $r->searchdata->numof_floor . "' ";
    }

    $sql .= "GROUP BY a.project_id ORDER BY CAST(a.project_id as UNSIGNED) DESC";
    //echo $sql;
    //exit(0);
    $properties = $db->getAllRecords($sql);
    //$properties[0].['sql']=$sql;
    echo json_encode($properties);
});

$app->get('/getdeveloper_names/:developer_name/:cat', function ($developer_name, $cat) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }

    $sql = "SELECT contact_id,contact_off, CONCAT(name_title,' ',f_name,' ',l_name,'   ',mob_no,'  ',company_name) as name from contact WHERE (company_name LIKE '%$developer_name%' or f_name LIKE '%$developer_name%' or l_name LIKE '%$developer_name%' or mob_no LIKE '%$developer_name%') and contact_off = '$cat' ORDER BY name LIMIT 20 ";

    if ($developer_name == 'blank') {
        //$sql = "SELECT contact_id,contact_off, CONCAT(name_title,' ',f_name,' ',l_name,'   ',mob_no,'  ',company_name) as name from contact WHERE contact_off = '$cat' ORDER BY company_name LIMIT 20 ";
        $sql = "SELECT * from contact LIMIT 0";
    }

    $senddata = array();
    $htmldata = array();
    $htmlstring = '';

    $htmlstring = '<ul class="mydropdown-menu" style="display:block;">';

    $stmt = $db->getRows($sql);
    $count = 0;
    if ($stmt->num_rows > 0) {
        while ($row = $stmt->fetch_assoc()) {
            $htmlstring .= '<li><a href="javascript:void(0)" ng-click = "getdevelopername(' . $row['contact_id'] . ',\'' . $row['name'] . '\')">' . $row['name'] . ' - ' . $row['mob_no'] . '</a></li>';
            $count = $count + 1;

        }
    }
    $htmlstring .= '</ul>';

    $htmldata['htmlstring'] = $htmlstring;
    $htmldata['count'] = $count;
    $senddata[] = $htmldata;
    echo json_encode($senddata);
});

$app->get('/getproject_exists/:contact_id', function ($contact_id) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }

    $sql = "SELECT *, CONCAT(b.locality,' (',c.area_name,')') as locality, CONCAT(c.area_name,' (',d.city,')') as area_name,DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date, CONCAT(e.name_title,' ',e.f_name,' ',e.l_name,'   ',e.mob_no,'  ',e.company_name) as developer_name from project as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c on a.area_id = c.area_id LEFT JOIN city as d on c.city_id = d.city_id LEFT JOIN contact as e ON e.contact_id = a.developer_id where a.developer_id=" . $contact_id . " LIMIT 10";

    $senddata = array();
    $htmldata = array();
    $htmlstring = '';

    $htmlstring = '<ul class="mydropdown-menu1" style="display:block;">';
    $stmt = $db->getRows($sql);
    $count = 0;
    if ($stmt->num_rows > 0) {
        while ($row = $stmt->fetch_assoc()) {
            $htmlstring .= '<li><a href="#/project_edit/' . $row['project_id'] . '"> Project_ID' . $row['project_id'] . ' - ' . $row['project_name'] . ' for ' . $row['project_for'] . '</a></li>';
            $count = $count + 1;

        }
    }
    $htmlstring .= '</ul>';
    $htmldata['htmlstring'] = $htmlstring;
    $htmldata['count'] = $count;
    $senddata[] = $htmldata;
    echo json_encode($senddata);
});

$app->post('/project_uploads', function () use ($app) {
    session_start();
    $project_id = $_SESSION['tmpproject_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }

    $user_id = $session['user_id'];

    if (empty($_FILES['file-1'])) {
        echo json_encode(['error' => 'No files found for upload.']);
        return; // terminate
    }

    $watermark_png_file = 'watermark.png'; //watermark png file

    $images = $_FILES['file-1'];
    $paths = [];
    $filenames = $images['name'];
    $count = 1;
    $ds = DIRECTORY_SEPARATOR;
    for ($i = 0; $i < count($filenames); $i++) {
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "p_" . $project_id . "_" . time() . "_" . $count . "." . $ext;
        $target = dirname(__FILE__) . $ds . "uploads" . $ds . "project" . $ds . $file_names;
        $target_thumb = dirname(__FILE__) . $ds . "uploads" . $ds . "project" . $ds . "thumb" . $ds . $file_names;

        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        //if(move_uploaded_file($images['tmp_name'][$i], $target)) {

        switch (strtolower($images['type'][$i])) { //determine uploaded image type 
            //Create new image from file
            case 'image/png':
                $image_resource = imagecreatefrompng($images['tmp_name'][$i]);
                break;
            case 'image/gif':
                $image_resource = imagecreatefromgif($images['tmp_name'][$i]);
                break;
            case 'image/jpeg':
            case 'image/pjpeg':
                $image_resource = imagecreatefromjpeg($images['tmp_name'][$i]);
                break;
            default:
                $image_resource = false;
        }
        if ($image_resource) {
            //Copy and resize part of an image with resampling
            list($img_width, $img_height) = getimagesize($images['tmp_name'][$i]);

            $new_canvas = imagecreatetruecolor($img_width, $img_height);
            if (imagecopyresampled($new_canvas, $image_resource, 0, 0, 0, 0, $img_width, $img_height, $img_width, $img_height)) {
                /*$watermark_left = ($img_width/2)-(247/2); //watermark left
                $watermark_bottom = ($img_height/2)-(77/2); //watermark bottom
                $watermark = imagecreatefrompng($watermark_png_file); //watermark image
                imagecopy($new_canvas, $watermark, $watermark_left, $watermark_bottom, 0, 0, 247, 77);*/
                imagejpeg($new_canvas, $target, 90);
            }
            $new_canvas = imagecreatetruecolor(245, 185);
            if (imagecopyresampled($new_canvas, $image_resource, 0, 0, 0, 0, 245, 185, $img_width, $img_height)) {
                /*$watermark_left = (245/2)-(247/2); //watermark left
                $watermark_bottom = (185/2)-(77/2); //watermark bottom
                $watermark = imagecreatefrompng($watermark_png_file); //watermark image
                imagecopy($new_canvas, $watermark, $watermark_left, $watermark_bottom, 0, 0, 247, 77); */
                imagejpeg($new_canvas, $target_thumb, 90);
            }

        }
        $success = true;
        $paths[] = $ext;

        if (isset($_POST['file_title_' . $count])) {
            $description = $_POST['file_title_' . $count];
        } else {
            $description = "";
        }

        if (isset($_POST['file_category_' . $count])) {
            $file_category = $_POST['file_category_' . $count];
        } else {
            $file_category = "";
        }

        if (isset($_POST['file_sub_category_' . $count])) {
            $file_sub_category = $_POST['file_sub_category_' . $count];
        } else {
            $file_sub_category = "";
        }

        if (isset($_POST['main_image_' . $count])) {
            $isdefault = $_POST['main_image_' . $count];
        } else {
            $isdefault = "false";
        }
        if (isset($_POST['share_on_web_' . $count])) {
            $share_on_web = $_POST['share_on_web_' . $count];
        } else {
            $share_on_web = "false";
        }

        $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, file_sub_category,  created_by, created_date)   VALUES('project', '$project_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$file_sub_category','$user_id', now() )";
        $result = $db->insertByQuery($query);
        $count++;

        /*} else {
            $success = false;
            break;
        }*/
    }
    echo json_encode(['success' => 'files uploaded.' . $file_names]);

});

$app->post('/project_uploads_org', function () use ($app) {
    session_start();
    $project_id = $_SESSION['tmpproject_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }

    $user_id = $session['user_id'];

    if (empty($_FILES['file-1'])) {
        echo json_encode(['error' => 'No files found for upload.']);
        return; // terminate
    }

    $watermark_png_file = 'watermark.png'; //watermark png file

    $images = $_FILES['file-1'];
    $paths = [];
    $filenames = $images['name'];
    $count = 1;
    $ds = DIRECTORY_SEPARATOR;
    for ($i = 0; $i < count($filenames); $i++) {
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "p_" . $project_id . "_" . time() . "_" . $count . "." . $ext;
        $target = dirname(__FILE__) . $ds . "uploads" . $ds . "project" . $ds . $file_names;
        $target_thumb = dirname(__FILE__) . $ds . "uploads" . $ds . "project" . $ds . "thumb" . $ds . $file_names;

        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        //if(move_uploaded_file($images['tmp_name'][$i], $target)) {

        switch (strtolower($images['type'][$i])) { //determine uploaded image type 
            //Create new image from file
            case 'image/png':
                $image_resource = imagecreatefrompng($images['tmp_name'][$i]);
                break;
            case 'image/gif':
                $image_resource = imagecreatefromgif($images['tmp_name'][$i]);
                break;
            case 'image/jpeg':
            case 'image/pjpeg':
                $image_resource = imagecreatefromjpeg($images['tmp_name'][$i]);
                break;
            default:
                $image_resource = false;
        }
        if ($image_resource) {
            //Copy and resize part of an image with resampling
            list($img_width, $img_height) = getimagesize($images['tmp_name'][$i]);

            $new_canvas = imagecreatetruecolor($img_width, $img_height);
            if (imagecopyresampled($new_canvas, $image_resource, 0, 0, 0, 0, $img_width, $img_height, $img_width, $img_height)) {
                $watermark_left = ($img_width / 2) - (247 / 2); //watermark left
                $watermark_bottom = ($img_height / 2) - (77 / 2); //watermark bottom
                $watermark = imagecreatefrompng($watermark_png_file); //watermark image
                imagecopy($new_canvas, $watermark, $watermark_left, $watermark_bottom, 0, 0, 247, 77);
                imagejpeg($new_canvas, $target, 90);
            }
            $new_canvas = imagecreatetruecolor(245, 185);
            if (imagecopyresampled($new_canvas, $image_resource, 0, 0, 0, 0, 245, 185, $img_width, $img_height)) {
                $watermark_left = (245 / 2) - (247 / 2); //watermark left
                $watermark_bottom = (185 / 2) - (77 / 2); //watermark bottom
                $watermark = imagecreatefrompng($watermark_png_file); //watermark image
                imagecopy($new_canvas, $watermark, $watermark_left, $watermark_bottom, 0, 0, 247, 77);
                imagejpeg($new_canvas, $target_thumb, 90);
            }

        }
        $success = true;
        $paths[] = $ext;

        if (isset($_POST['file_title_' . $count])) {
            $description = $_POST['file_title_' . $count];
        } else {
            $description = "";
        }

        if (isset($_POST['file_category_' . $count])) {
            $file_category = $_POST['file_category_' . $count];
        } else {
            $file_category = "";
        }
        if (isset($_POST['main_image_' . $count])) {
            $isdefault = $_POST['main_image_' . $count];
        } else {
            $isdefault = "false";
        }
        if (isset($_POST['share_on_web_' . $count])) {
            $share_on_web = $_POST['share_on_web_' . $count];
        } else {
            $share_on_web = "false";
        }

        $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('project', '$project_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$user_id', now() )";
        $result = $db->insertByQuery($query);
        $count++;

        /*} else {
            $success = false;
            break;
        }*/
    }
    echo json_encode(['success' => 'files uploaded.' . $file_names]);

});

$app->post('/project_upload_temp', function () use ($app) {
    session_start();
    $project_id = $_SESSION['tmpproject_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }

    $user_id = $session['user_id'];

    if (empty($_FILES['file-1'])) {
        echo json_encode(['error' => 'No files found for upload.']);
        return; // terminate
    }



    $images = $_FILES['file-1'];
    $paths = [];
    $filenames = $images['name'];
    $count = 1;
    $ds = DIRECTORY_SEPARATOR;
    for ($i = 0; $i < count($filenames); $i++) {
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "p_" . $project_id . "_" . time() . "_" . $count . "." . $ext;
        $target = dirname(__FILE__) . $ds . "uploads" . $ds . "project" . $ds . $file_names;
        $target_thumb = dirname(__FILE__) . $ds . "uploads" . $ds . "project" . $ds . "thumb" . $ds . $file_names;

        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if (move_uploaded_file($images['tmp_name'][$i], $target_thumb)) {

            if (move_uploaded_file($images['tmp_name'][$i], $target)) {


                $success = true;
                $paths[] = $ext;

                if (isset($_POST['file_title_' . $count])) {
                    $description = $_POST['file_title_' . $count];
                } else {
                    $description = "";
                }

                if (isset($_POST['file_category_' . $count])) {
                    $file_category = $_POST['file_category_' . $count];
                } else {
                    $file_category = "";
                }
                if (isset($_POST['main_image_' . $count])) {
                    $isdefault = $_POST['main_image_' . $count];
                } else {
                    $isdefault = "false";
                }
                if (isset($_POST['share_on_web_' . $count])) {
                    $share_on_web = $_POST['share_on_web_' . $count];
                } else {
                    $share_on_web = "false";
                }

                $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('project', '$project_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$user_id', now() )";
                $result = $db->insertByQuery($query);
                $count++;

            } else {
                $success = false;
                break;
            }
        }
    }
    echo json_encode(['success' => 'files uploaded.' . $file_names]);

});

$app->post('/project_uploads_occu', function () use ($app) {
    session_start();
    $project_id = $_SESSION['tmpproject_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }

    $user_id = $session['user_id'];

    if (empty($_FILES['file_occu'])) {
        echo json_encode(['error' => 'No files found for upload.']);
        return; // terminate
    }

    $images = $_FILES['file_occu'];
    $paths = [];
    $filenames = $images['name'];
    $count = 1;
    for ($i = 0; $i < count($filenames); $i++) {
        $ds = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "poc_" . $project_id . "_" . time() . "_" . $count . "." . $ext;
        $target = dirname(__FILE__) . $ds . "uploads" . $ds . "project" . $ds . "occu_cert" . $ds . $file_names;

        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if (move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            if (isset($_POST['file_title_' . $count])) {
                $description = $_POST['file_title_' . $count];
            } else {
                $description = "";
            }

            if (isset($_POST['file_category_' . $count])) {
                $file_category = $_POST['file_category_' . $count];
            } else {
                $file_category = "";
            }
            if (isset($_POST['main_image_' . $count])) {
                $isdefault = $_POST['main_image_' . $count];
            } else {
                $isdefault = "false";
            }
            if (isset($_POST['share_on_web_' . $count])) {
                $share_on_web = $_POST['share_on_web_' . $count];
            } else {
                $share_on_web = "false";
            }

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('project_occu_cert', '$project_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success' => 'files uploaded.' . $file_names]);

});




$app->get('/project_edit_ctrl/:project_id', function ($project_id) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }

    $sql = "SELECT *, CONCAT(b.locality,' (',c.area_name,')') as locality, CONCAT(c.area_name,' (',d.city,')') as area_name,DATE_FORMAT(a.possession_date,'%d/%m/%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d/%m/%Y') AS completion_date from project as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c on a.area_id = c.area_id LEFT JOIN city as d on c.city_id = d.city_id where a.project_id=" . $project_id;
    $projects = $db->getAllRecords($sql);
    echo json_encode($projects);

});

$app->get('/project_images/:project_id', function ($project_id) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'project' and category_id = $project_id";
    $project_images = $db->getAllRecords($sql);
    echo json_encode($project_images);
});


$app->get('/project_imagesslide/:project_id', function ($project_id) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }

    $sql = "SELECT attachment_id as id , description as title, file_category as 'desc', CONCAT('api/v1/uploads/project/thumb/',filenames) as thumbUrl, CONCAT('api/v1/uploads/project/thumb/',filenames) as bubbleUrl, CONCAT('api/v1/uploads/project/',filenames) as url   from attachments WHERE category = 'project' and category_id = $project_id";

    $project_images = $db->getAllRecords($sql);
    echo json_encode($project_images);
});

$app->get('/project_image_update/:attachment_id/:field_name/:value', function ($attachment_id, $field_name, $value) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $sql = "UPDATE attachments set $field_name = '$value' where attachment_id = $attachment_id ";
    $result = $db->updateByQuery($sql);
    $response["status"] = "success";
    $response["message"] = "Value Changed !!!";
    echo json_encode($response);
});


$app->get('/project_occu_cert/:project_id', function ($project_id) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'project_occu_cert' and category_id = $project_id";
    $project_occu_certs = $db->getAllRecords($sql);
    echo json_encode($project_occu_certs);
});



$app->post('/project_update', function () use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('project_id'), $r->project);
    $db = new DbHandler();
    $project_id = $r->project->project_id;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->project->modified_by = $modified_by;
    $r->project->modified_date = $modified_date;

    if (isset($r->project->possession_date)) {
        $possession_date = $r->project->possession_date;
        $tpossession_date = substr($possession_date, 6, 4) . "-" . substr($possession_date, 3, 2) . "-" . substr($possession_date, 0, 2);
        $r->project->possession_date = $tpossession_date;
    }

    if (isset($r->project->completion_date)) {
        $completion_date = $r->project->completion_date;
        $tcompletion_date = substr($completion_date, 6, 4) . "-" . substr($completion_date, 3, 2) . "-" . substr($completion_date, 0, 2);
        $r->project->completion_date = $tcompletion_date;
    }

    if (isset($r->project->park_charge)) {
        if (isset($r->project->park_charge_para)) {
            $park_charge = ($r->project->park_charge);
            $r->project->park_charge = $park_charge;
        }
    }
    if (isset($r->project->maintenance_charges)) {
        if (isset($r->project->main_charge_para)) {
            $maintenance_charges = ($r->project->maintenance_charges);
            $r->project->maintenance_charges = $maintenance_charges;
        }
    }
    if (isset($r->project->prop_tax)) {
        if (isset($r->project->prop_tax_para)) {
            $prop_tax = ($r->project->prop_tax);
            $r->project->prop_tax = $prop_tax;
        }
    }
    if (isset($r->project->transfer_charge)) {
        if (isset($r->project->park_charge_para)) {
            $transfer_charge = ($r->project->transfer_charge);
            $r->project->transfer_charge = $transfer_charge;
        }
    }

    $isprojectExists = $db->getOneRecord("select 1 from project where project_id=$project_id");
    if ($isprojectExists) {
        $tabble_name = "project";
        $column_names = array('project_name', 'developer_id', 'developer_name', 'project_for', 'con_status', 'possession_date', 'completion_date', 'rera_num', 'add1', 'add2', 'exlocation', 'locality_id', 'area_id', 'road_no', 'landmark', 'city', 'state', 'country', 'zip', 'lattitude', 'longitude', 'salu', 'fname', 'lname', 'mob1', 'mob2', 'email', 'designation', 'salu_2', 'fname_2', 'lname_2', 'mob1_2', 'mob2_2', 'email_2', 'designation_2', 'tot_area', 'tot_unit', 'rent_unit_carpet', 'pack_price', 'pack_price_comments', 'app_bankloan', 'amenities_avl', 'pro_specification', 'parking', 'car_park', 'park_charge', 'maintenance_charges', 'prop_tax', 'transfer_charge', 'teams', 'sub_teams', 'assign_to', 'source_channel', 'subsource_channel', 'groups', 'file_name', 'internal_comment', 'external_comment', 'numof_building', 'numof_floor', 'lifts', 'floor_rise', 'distfrm_station', 'distfrm_dairport', 'distfrm_highway', 'distfrm_school', 'distfrm_market', 'com_certification', 'youtube_link', 'rating', 'review', 'proj_status', 'pro_inspect', 'vastu_comp', 'occu_certi', 'soc_reg', 'cc', 'modified_by', 'modified_date', 'mainroad', 'internalroad', 'area_parameter', 'project_type', 'park_charge_para', 'main_charge_para', 'transfer_charge_para', 'prop_tax_para', 'market_project', 'share_on_website', 'share_on_99', 'acers_99_projid', 'task_id', 'sms_saletrainee', 'email_saletrainee');

        $multiple = array("amenities_avl", "app_bankloan", "assign_to", "groups", "parking", "pro_specification", "source_channel", "subsource_channel", "teams", "sub_teams");
        $condition = "project_id='$project_id'";

        $history = $db->historydata($r->project, $column_names, $tabble_name, $condition, $project_id, $multiple, $modified_by, $modified_date);

        $result = $db->NupdateIntoTable($r->project, $column_names, $tabble_name, $condition, $multiple);
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
    } else {
        $response["status"] = "error";
        $response["message"] = "Project with the provided Project does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/project_delete', function () use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('project_id'), $r->project);
    $db = new DbHandler();
    $project_id = $r->project->project_id;
    $isprojectExists = $db->getOneRecord("select 1 from project where project_id=$project_id");
    if ($isprojectExists) {
        $tabble_name = "project";
        $column_names = array('name');
        $condition = "project_id='$project_id'";
        $result = $db->deleteIntoTable($r->project, $column_names, $tabble_name, $condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Project Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Project. Please try again";
            echoResponse(201, $response);
        }
    } else {
        $response["status"] = "error";
        $response["message"] = "Project with the provided Project does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectproject', function () use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $sql = "SELECT * from project ORDER BY project_name";
    $projects = $db->getAllRecords($sql);
    echo json_encode($projects);
});


$app->get('/matchingenquiries/:project_id', function ($project_id) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $senddata = array();
    $htmldata = array();
    $htmlstring = '';
    $htmlstring .= '<div class="panel-body">
                        <div class="box-body table-responsive">
                            <table class="table" datatable-setupnosort2 ="" >
                                <thead>
                                    <tr>
					<th>Enquiry ID</th>
                                        <th>Enquiry Type</th>
                                        <th>Status</th>
                                        <th>Preferred Area</th>
                                        <th>Preferred Locality</th>
                                        <th>Client</th>
                                        <th>Mobile No.</th>
                                    </tr>
                                </thead>
                                <tbody>';
    $project_for = "";
    $area_id = 0;
    $pack_price = 0;

    $projectdata = $db->getAllRecords("SELECT * FROM project where project_id = $project_id");

    if ($projectdata) {
        $project_for = $projectdata[0]['project_for'];
        if ($project_for == 'Sale') {
            $project_for = 'Buy';
        }

        if ($project_for == 'Rent') {
            $project_for = 'Lease';
        }

        $area_id = $projectdata[0]['area_id'];
        $pack_price = $projectdata[0]['pack_price'];
    }

    $bo_id = $session['bo_id'];
    $role = $session['role'];

    $ifAdmin = false;
    if (in_array("Admin", $role) || in_array("Sub Admin", $role)) {
        $ifAdmin = true;
    }

    $sql = "SELECT *,  CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob, e.locality as preferred_locality, f.area_name as preferred_area  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN users as c on a.assigned = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id ";

    if (!$ifAdmin) {
        $sql .= " LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id ";
    }

    $sql .= " WHERE ";

    if ($project_for != "") {
        $sql .= " a.enquiry_for = '$project_for' and ";
    }

    if ($area_id != 0) {
        $sql .= " a.preferred_area_id  = $area_id and ";
    }

    if ($pack_price > 0) {
        $sql .= " ( a.budget_range1>=$pack_price and budget_range2 <=$pack_price) ";
    }

    if (!$ifAdmin) {
        $sql .= " and z.bo_id = $bo_id ";
    }
    $sql .= " ORDER BY a.enquiry_for";

    $stmt = $db->getRows($sql);

    if ($stmt->num_rows > 0) {
        while ($row = $stmt->fetch_assoc()) {
            $htmlstring .= '<tr>
				<td><a href="#/enquiries_edit/' . $row['enquiry_id'] . '">' . $row['enquiry_off'] . '_' . $row['enquiry_id'] . '</a></td>
                                <td>' . $row['enquiry_type'] . '</td>
                                <td>' . $row['status'] . '</td>
                                <td>' . $row['preferred_area'] . '</td>
                                <td>' . $row['preferred_locality'] . '</td>
                                <td>' . $row['client_name'] . '</td>
                                <td>' . $row['client_mob'] . '</td>
                            </tr>';
        }
    }
    $htmlstring .= '</tbody>
                </table>    
            </div> 
        </div>';

    $htmldata['htmlstring'] = $htmlstring;
    $htmldata['sql'] = $sql;
    $senddata[] = $htmldata;
    echo json_encode($senddata);
});


$app->get('/projectonemailerreports/:module_name/:id/:data', function ($module_name, $id, $data) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];

    $sql = "SELECT * from porperty limit 0"; //CAST(a.project_id as UNSIGNED) DESC";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role)) {

        $sql = "SELECT *,e.company_name as developer,SUBSTRING(e.f_name,1,1) as f_char,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,a.mob1,a.email, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT CONCAT(max(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price , (SELECT CONCAT(max(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as max_carp,(SELECT CONCAT(min(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as min_carp ,   (SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by FROM project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id WHERE a.project_id in ($data) GROUP BY a.project_id "; // CAST(a.project_id as UNSIGNED) DESC";
    }

    /*if (in_array("Branch Head Level", $permissions))
    {
        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, h.email as useremail, (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from project as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.project_id = f.category_id and f.category='project' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE  a.project_id in ($data)  and z.bo_id = $bo_id GROUP BY a.project_id ORDER BY a.modified_date DESC";// CAST(a.project_id as UNSIGNED) DESC";
    }

    if (in_array("Branch User Level", $permissions))
    {
        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, h.email as useremail, (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from project as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.project_id = f.category_id and f.category='project' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE a.project_id in ($data)  and z.bo_id = $bo_id GROUP BY a.project_id ORDER BY a.modified_date DESC ";//CAST(a.project_id as UNSIGNED) DESC";
    }

    if (in_array("User Level", $permissions))
    {*/else {
        $sql = "SELECT *,e.company_name as developer,SUBSTRING(e.f_name,1,1) as f_char,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,a.mob1,a.email, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT CONCAT(max(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',MAX(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price , (SELECT CONCAT(max(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as max_carp,(SELECT CONCAT(min(CAST(carp_area as UNSIGNED)),' ',MAX(carp_area_para)) FROM property WHERE project_id= a.project_id and carp_area > 0 ) as min_carp ,   (SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by FROM project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id WHERE a.project_id in ($data) GROUP BY a.project_id "; // CAST(a.project_id as UNSIGNED) DESC";
    }


    $projectdata = $db->getAllRecords($sql);

    $proptype = $projectdata[0]['proptype'];
    $carp_area = $projectdata[0]['carp_area'];
    $sale_area = $projectdata[0]['sale_area'];
    $floor = $projectdata[0]['floor'];
    $car_park = $projectdata[0]['car_park'];
    $price_unit = $projectdata[0]['price_unit'];
    $security_depo = $projectdata[0]['security_depo'];
    $lock_per = $projectdata[0]['lock_per'];
    $lease_end = $projectdata[0]['lease_end'];
    $escalation_lease = $projectdata[0]['escalation_lease'];
    $owner_name = $projectdata[0]['owner_name'];
    $mob_no = $projectdata[0]['mob_no'];
    $email = $projectdata[0]['email'];

    $internal_comment = $projectdata[0]['internal_comment'];
    $external_comment = $projectdata[0]['external_comment'];
    $prop_tax = (int) $projectdata[0]['prop_tax'];
    $cam_charges = (int) $projectdata[0]['cam_charges'];
    $monthle_rent = (int) $projectdata[0]['monthle_rent'];
    $ag_tenure = $projectdata[0]['ag_tenure'];
    $rent_esc = (int) $projectdata[0]['rent_esc'];
    $lease_start = $projectdata[0]['lease_start'];


    $location = $projectdata[0]['location'] . ',' . $projectdata[0]['wing'] . ',' . $projectdata[0]['unit'] . ',' . $projectdata[0]['floor'] . ',' . $projectdata[0]['road_no'] . ',' . $projectdata[0]['building_name'] . ',' . $projectdata[0]['landmark'] . ',' . $projectdata[0]['locality'] . ',' . $projectdata[0]['area_name'] . ',' . $projectdata[0]['city'];

    $furnishing = $projectdata[0]['furnishing'] . ',' . $projectdata[0]['rece'] . ' Reception,' . $projectdata[0]['workstation'] . ' Workstation,' . $projectdata[0]['cabins'] . ' Cabins,' . $projectdata[0]['cubicals'] . ' Cubicals,' . $projectdata[0]['conferences'] . ' Conferences,' . $projectdata[0]['kitchen'] . ' Kitchen,' . $projectdata[0]['washrooms'] . ' washrooms';


    $area_id = $projectdata[0]['area_id'];
    $area_name = $projectdata[0]['area_name'];
    $locality_id = $projectdata[0]['locality_id'];
    $locality = $projectdata[0]['locality'];
    $propsubtype = $projectdata[0]['propsubtype'];
    $suitable_for = $projectdata[0]['suitable_for'];

    $project_contact = $projectdata[0]['project_contact'];
    $usermobileno = $projectdata[0]['usermobileno'];
    $useremail = $projectdata[0]['useremail'];



    /*$sqldelete = "DELETE FROM report_template WHERE (slide_no>3 and slide_no<98) or slide_no=0";
    $result = $db->updateByQuery($sqldelete);*/

    $sqldelete = "DELETE FROM project_report_template WHERE user_id = $user_id";
    $result = $db->updateByQuery($sqldelete);

    $sql = "SELECT * from attachments WHERE category = 'project' and  category_id in ($data) and isdefault = 'true' LIMIT 1";
    $stmt = $db->getRows($sql);
    $category = $module_name;
    $category_id = $data;
    if ($stmt->num_rows > 0) {
        while ($row = $stmt->fetch_assoc()) {
            $description = $row['description'];
            $image_1 = $row['filenames'];
            $category = $row['category'];
            $category_id = $row['category_id'];
            $slide_no = 2;

            $query = "INSERT INTO project_report_template (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','$description','$image_1','$category','$category_id','$user_id')";
            $result = $db->insertByQuery($query);

            /*$sql = "UPDATE report_template set description  = '$description' , image_1 = '$image_1' WHERE slide_no = 2";
            $result = $db->updateByQuery($sql);
            $sql = "UPDATE report_template set category = '$category' , category_id = '$category_id' WHERE slide_no=1 or slide_no=2 or slide_no = 3 or slide_no = 98 or slide_no=99";
            $result = $db->updateByQuery($sql);*/
        }
    }

    $slide_no = 1;
    $query = "INSERT INTO project_report_template (report_id, slide_no, proptype, description, image_1, category, category_id, carp_area, sale_area, floor, car_park, price_unit, security_depo, lock_per, lease_end, escalation_lease, location, furnishing, owner_name, mob_no, email, project_contact, usermobileno, useremail, area_id, area_name, locality, locality_id, propsubtype,suitable_for, internal_comment,external_comment,prop_tax, cam_charges,monthle_rent,ag_tenure,rent_esc,lease_start,user_id)   VALUES(1,'$slide_no','$proptype' ,'Main','ppt_main.jpg','$category','$category_id', '$carp_area','$sale_area', '$floor', '$car_park', '$price_unit', '$security_depo', '$lock_per', '$lease_end','$escalation_lease', '$location', '$furnishing', '$owner_name', '$mob_no', '$email', '$project_contact', '$usermobileno', '$useremail','$area_id','$area_name','$locality','$locality_id', '$propsubtype','$suitable_for','$internal_comment','$external_comment', '$prop_tax', '$cam_charges',  '$monthle_rent', '$ag_tenure', '$rent_esc', '$lease_start', '$user_id')";

    $result = $db->insertByQuery($query);

    $slide_no = 3;
    $query = "INSERT INTO project_report_template (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Commercial Terms','details.jpg','$category','$category_id','$user_id')";
    $result = $db->insertByQuery($query);

    $slide_no = 98;
    $query = "INSERT INTO project_report_template (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Location Map','location.jpg','$category','$category_id','$user_id')";
    $result = $db->insertByQuery($query);

    $slide_no = 99;
    $query = "INSERT INTO project_report_template (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Thanks','thanks.jpg','$category','$category_id','$user_id')";
    $result = $db->insertByQuery($query);

    /*$sql = "UPDATE report_template set carp_area  = '$carp_area',sale_area  = '$sale_area',floor  = '$floor',car_park  = '$car_park',price_unit  = '$price_unit',security_depo  = '$security_depo',lock_per  = '$lock_per',lease_end  = '$lease_end',escalation_lease  = '$escalation_lease',location  = '$location',furnishing  = '$furnishing', owner_name='$owner_name', mob_no='$mob_no', email='$email', project_contact='$project_contact', usermobileno='$usermobileno', useremail='$useremail', area_id = '$area_id', area_name='$area_name', locality='$locality',locality_id='$locality_id',propsubtype='$propsubtype',suitable_for='$suitable_for',col1_value = '',col2_value = '',col3_value = '',col4_value = '',col5_value = '' WHERE slide_no = 1";
    $result = $db->updateByQuery($sql);*/
    $slide_no = 4;

    $slide_no = 6;
    $sql = "SELECT * from attachments WHERE category = 'project' and  category_id = $category_id and isdefault != 'true'";
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0) {
        $image_count = 1;

        $image_1 = "";
        $image_2 = "";
        $image_3 = "";
        $image_4 = "";
        $description = "";
        $data_add = 'yes';
        while ($row = $stmt->fetch_assoc()) {

            if ($image_count == 1) {
                $description = $row['description'];
                $image_1 = $row['filenames'];
            }
            if ($image_count == 2) {
                if ($description) {
                    $description .= " & " . $row['description'];
                }
                $image_2 = $row['filenames'];
            }
            if ($image_count == 3) {
                if ($description) {
                    $description .= " & " . $row['description'];
                }
                $image_3 = $row['filenames'];
            }
            if ($image_count == 4) {
                if ($description) {
                    $description .= " & " . $row['description'];
                }
                $image_4 = $row['filenames'];
            }
            $image_count++;
            if ($image_count > 4) {
                $query = "INSERT INTO project_report_template (report_id, slide_no, description, image_1, image_2, image_3, image_4, category, category_id,user_id)   VALUES(1,'$slide_no','$description','$image_1','$image_2','$image_3','$image_4','$category','$category_id','$user_id')";
                $result = $db->insertByQuery($query);
                $description = "";
                $image_1 = "";
                $image_2 = "";
                $image_3 = "";
                $image_4 = "";
                $slide_no++;
                $data_add = 'No';
                $image_count = 1;
            }

        }

        if ($data_add == 'No') {
            $query = "INSERT INTO project_report_template (report_id, slide_no, description, image_1, image_2, image_3, image_4, category, category_id,user_id)   VALUES(1,'$slide_no','$description','$image_1','$image_2','$image_3','$image_4','$category','$category_id','$user_id')";
            $result = $db->insertByQuery($query);
        }
    }

    $sql = "SELECT * from project_report_template WHERE user_id = $user_id ORDER BY slide_no";
    $slide_data = $db->getAllRecords($sql);
    echo json_encode($slide_data);

});


$app->get('/createreportsproject/:module_name/:id/:data', function ($module_name, $id, $data) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];

    $sql = "SELECT * from project limit 0"; //CAST(a.property_id as UNSIGNED) DESC";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role)) {

        //SELECT *,a.proptype,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, h.off_email as useremail, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date,  e.city, k.state,l.country,  DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,concat(c.locality,',',e.city) as locality, concat(d.area_name,',',e.city) as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN state as k ON e.state_id = k.state_id LEFT JOIN country as l ON k.country_id = l.country_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id

        $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE  a.project_id in ($data) GROUP BY a.project_id ";
    } else {
        $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE  a.project_id in ($data) GROUP BY a.project_id";
    }


    $propertydata = $db->getAllRecords($sql);

    $proptype = $propertydata[0]['proptype'];
    $carp_area = $propertydata[0]['carp_area'];
    $sale_area = $propertydata[0]['sale_area'];
    $floor = $propertydata[0]['floor'];
    $car_park = $propertydata[0]['car_park'];
    $price_unit = $propertydata[0]['price_unit'];
    $security_depo = $propertydata[0]['security_depo'];
    $lock_per = $propertydata[0]['lock_per'];
    $lease_end = $propertydata[0]['lease_end'];
    $escalation_lease = $propertydata[0]['escalation_lease'];
    $owner_name = $propertydata[0]['owner_name'];
    $mob_no = $propertydata[0]['mob_no'];
    $email = $propertydata[0]['email'];

    $internal_comment = $propertydata[0]['internal_comment'];
    $external_comment = $propertydata[0]['external_comment'];
    $prop_tax = (int) $propertydata[0]['prop_tax'];
    $cam_charges = (int) $propertydata[0]['cam_charges'];
    $monthle_rent = (int) $propertydata[0]['monthle_rent'];
    $ag_tenure = $propertydata[0]['ag_tenure'];
    $rent_esc = (int) $propertydata[0]['rent_esc'];
    $lease_start = $propertydata[0]['lease_start'];


    $location = $propertydata[0]['location'] . ',' . $propertydata[0]['wing'] . ',' . $propertydata[0]['unit'] . ',' . $propertydata[0]['floor'] . ',' . $propertydata[0]['road_no'] . ',' . $propertydata[0]['building_name'] . ',' . $propertydata[0]['landmark'] . ',' . $propertydata[0]['locality'] . ',' . $propertydata[0]['area_name'] . ',' . $propertydata[0]['city'];
    $furnishing = $propertydata[0]['furniture'];
    if ($propertydata[0]['furniture'] == 'Furnished') {
        $furnishing = $propertydata[0]['furnishing'] . ',' . $propertydata[0]['rece'] . ' Reception,' . $propertydata[0]['workstation'] . ' Workstation,' . $propertydata[0]['cabins'] . ' Cabins,' . $propertydata[0]['cubicals'] . ' Cubicals,' . $propertydata[0]['conferences'] . ' Conferences,' . $propertydata[0]['kitchen'] . ' Kitchen,' . $propertydata[0]['washrooms'] . ' washrooms';
    }



    $area_id = $propertydata[0]['area_id'];
    $area_name = $propertydata[0]['area_name'];
    $locality_id = $propertydata[0]['locality_id'];
    $locality = $propertydata[0]['locality'];

    $map_address = $propertydata[0]['locality'] . ' ' . $propertydata[0]['area_name'] . ' ' . $propertydata[0]['city'] . ' ' . $propertydata[0]['state'] . ' ' . $propertydata[0]['country'];

    $propsubtype = $propertydata[0]['propsubtype'];
    $suitable_for = $propertydata[0]['suitable_for'];

    $project_contact = $propertydata[0]['project_contact'];
    $usermobileno = $propertydata[0]['usermobileno'];
    $useremail = $propertydata[0]['useremail'];


    $sqldelete = "DELETE FROM report_template_project WHERE user_id = $user_id";
    $result = $db->updateByQuery($sqldelete);

    $sql = "SELECT * from attachments WHERE category = 'project' and  category_id in ($data) and isdefault = 'true' LIMIT 1";
    $stmt = $db->getRows($sql);
    $category = $module_name;
    $category_id = $data;
    if ($stmt->num_rows > 0) {
        while ($row = $stmt->fetch_assoc()) {
            $description = $row['description'];
            $image_1 = $row['filenames'];
            $category = $row['category'];
            $category_id = $row['category_id'];
            $slide_no = 2;

            $query = "INSERT INTO report_template_project (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','$description','$image_1','$category','$category_id','$user_id')";
            $result = $db->insertByQuery($query);

            /*$sql = "UPDATE report_template set description  = '$description' , image_1 = '$image_1' WHERE slide_no = 2";
            $result = $db->updateByQuery($sql);
            $sql = "UPDATE report_template set category = '$category' , category_id = '$category_id' WHERE slide_no=1 or slide_no=2 or slide_no = 3 or slide_no = 98 or slide_no=99";
            $result = $db->updateByQuery($sql);*/
        }
    }
    $disclaimer = "The information contained in this electronic message and any attachments to this message are intended for the exclusive use of the addressee(s) and may contain proprietary, confidential or privileged information. If you are not the intended recipient, you must not disseminate, distribute or copy this e-mail or any attachments to it. If you have received this e-mail by error, please let us know immediately by return e-mail and also destroy all copies of this message and the attachments, if any. It may be noted RD BROTHRES PROPERTY CONSULTANT LLP  as real estate solution  provider hereby states that any negotiation or terms of agreement or agreement shall be considered to have been executed only when hard copy of the document is signed by the authorized signatory of the company.";

    $slide_no = 1;
    $query = "INSERT INTO report_template_project (report_id, slide_no, proptype, description, image_1, category, category_id, carp_area, sale_area, floor, car_park, price_unit, security_depo, lock_per, lease_end, escalation_lease, location, map_address, furnishing, owner_name, mob_no, email, project_contact, usermobileno, useremail, area_id, area_name, locality, locality_id, propsubtype,suitable_for, internal_comment,external_comment,prop_tax, cam_charges,monthle_rent,ag_tenure,rent_esc,lease_start,user_id)   VALUES(1,'$slide_no','$proptype' ,'Main','ppt_main.jpg','$category','$category_id', '$carp_area','$sale_area', '$floor', '$car_park', '$price_unit', '$security_depo', '$lock_per', '$lease_end','$escalation_lease', '$location','$map_address','$furnishing', '$owner_name', '$mob_no', '$email', '$project_contact', '$usermobileno', '$useremail','$area_id','$area_name','$locality','$locality_id', '$propsubtype','$suitable_for','$internal_comment','$external_comment', '$prop_tax', '$cam_charges',  '$monthle_rent', '$ag_tenure', '$rent_esc', '$lease_start', '$user_id')";

    $result = $db->insertByQuery($query);

    $slide_no = 3;
    $query = "INSERT INTO report_template_project (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Commercial Terms','details.jpg','$category','$category_id','$user_id')";
    $result = $db->insertByQuery($query);

    $slide_no = 98;
    $query = "INSERT INTO report_template_project (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Location Map','location.jpg','$category','$category_id','$user_id')";
    $result = $db->insertByQuery($query);

    $slide_no = 99;
    $query = "INSERT INTO report_template_project (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Thanks','thanks.jpg','$category','$category_id','$user_id')";
    $result = $db->insertByQuery($query);

    $slide_no = 4;

    $query = "INSERT INTO report_template_project (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Floor Plan','floor_plan.jpg','$category','$category_id','$user_id')";
    $result = $db->insertByQuery($query);

    $slide_no = 6;
    $sql = "SELECT * from attachments WHERE category = 'project' and  category_id = $category_id and isdefault != 'true'";
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0) {
        $image_count = 1;

        $image_1 = "";
        $image_2 = "";
        $image_3 = "";
        $image_4 = "";
        $description = "";
        $data_add = 'yes';
        while ($row = $stmt->fetch_assoc()) {

            if ($image_count == 1) {
                $description = $row['description'];
                $image_1 = $row['filenames'];
            }
            if ($image_count == 2) {
                if ($description) {
                    $description .= " & " . $row['description'];
                }
                $image_2 = $row['filenames'];
            }
            if ($image_count == 3) {
                if ($description) {
                    $description .= " & " . $row['description'];
                }
                $image_3 = $row['filenames'];
            }
            if ($image_count == 4) {
                if ($description) {
                    $description .= " & " . $row['description'];
                }
                $image_4 = $row['filenames'];
            }
            $image_count++;
            if ($image_count > 4) {
                $query = "INSERT INTO report_template_project (report_id, slide_no, description, image_1, image_2, image_3, image_4, category, category_id,user_id)   VALUES(1,'$slide_no','$description','$image_1','$image_2','$image_3','$image_4','$category','$category_id','$user_id')";
                $result = $db->insertByQuery($query);
                $description = "";
                $image_1 = "";
                $image_2 = "";
                $image_3 = "";
                $image_4 = "";
                $slide_no++;
                $data_add = 'No';
                $image_count = 1;
            }

        }

        if ($data_add == 'No') {
            $query = "INSERT INTO report_template_project (report_id, slide_no, description, image_1, image_2, image_3, image_4, category, category_id,user_id)   VALUES(1,'$slide_no','$description','$image_1','$image_2','$image_3','$image_4','$category','$category_id','$user_id')";
            $result = $db->insertByQuery($query);
        }
    }

    $sql = "SELECT * from report_template_project WHERE user_id = $user_id ORDER BY slide_no";
    $slide_data = $db->getAllRecords($sql);
    echo json_encode($slide_data);

});


$app->get('/getprojectslidedata', function () use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];

    $sql = "SELECT * from report_template_project WHERE user_id = $user_id ORDER BY slide_no";
    $slide_data = $db->getAllRecords($sql);
    echo json_encode($slide_data);

});



$app->get('/delete_project_image_record/:slide_no/:id', function ($slide_no, $id) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];
    if ($id == 1) {
        $sql = "UPDATE report_template_project set image_1 = '' WHERE slide_no = $slide_no and user_id = $user_id";
    }
    if ($id == 2) {
        $sql = "UPDATE report_template_project set image_2 = '' WHERE slide_no = $slide_no  and user_id = $user_id";
    }
    if ($id == 3) {
        $sql = "UPDATE report_template set image_3 = '' WHERE slide_no = $slide_no  and user_id = $user_id ";
    }
    if ($id == 4) {
        $sql = "UPDATE report_template_project set image_4 = '' WHERE slide_no = $slide_no  and user_id = $user_id";
    }
    $result = $db->updateByQuery($sql);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Image Deleted successfully";
        echoResponse(200, $response);
    }
});

$app->get('/save_project_ppt_description/:slide_no/:description', function ($slide_no, $description) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];
    $sql = "UPDATE report_template_project set description = '$description' WHERE slide_no = $slide_no  and user_id = $user_id";

    $result = $db->updateByQuery($sql);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Description Updated successfully";
        echoResponse(200, $response);
    }
});


$app->get('/removeprojectslide/:slide_no', function ($slide_no) use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];
    $sql = "DELETE FROM report_template_project WHERE slide_no = $slide_no ";
    $result = $db->updateByQuery($sql);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Slide Deleted successfully";
        echoResponse(200, $response);
    }
});

$app->post('/saveprojectslide', function () use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    $r->slides->report_id = 1;

    $r->slides->image_1 = $r->slides->image_1_name;
    $r->slides->image_2 = $r->slides->image_2_name;
    $r->slides->image_3 = $r->slides->image_3_name;
    $r->slides->image_4 = $r->slides->image_4_name;

    $tabble_name = "report_template_project";
    $column_names = array('report_id', 'slide_no', 'description', 'image_1', 'image_2', 'image_3', 'image_4', 'category', 'category_id');

    $multiple = array("");

    $result = $db->insertIntoTable($r->slides, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Slide created successfully";
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create Slide. Please try again";
        echoResponse(201, $response);
    }

});

$app->post('/saveprojectslidedata', function () use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    $user_id = $session['user_id'];

    $multiple = array("");
    $condition = "slide_no=1 and user_id = $user_id";
    $tabble_name = "report_template_project";

    $column_names = array('carp_area', 'sale_area', 'floor', 'car_park', 'price_unit', 'security_depo', 'lock_per', 'lease_end', 'escalation_lease', 'location', 'furnishing', 'internal_comment', 'external_comment', 'prop_tax', 'cam_charges', 'monthle_rent', 'ag_tenure', 'rent_esc', 'lease_start', 'col1_heading', 'col1_value', 'col2_heading', 'col2_value', 'col3_heading', 'col3_value', 'col4_heading', 'col4_value', 'col5_heading', 'col5_value', 'wings', 'highlights', 'amenities', 'occu_certi');
    $result = $db->NupdateIntoTable($r->slides, $column_names, $tabble_name, $condition, $multiple);

    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Data Updated successfully";
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to update Data. Please try again";
        echoResponse(201, $response);
    }

});

$app->post('/save_project_image', function () use ($app) {
    session_start();
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }

    $user_id = $session['user_id'];

    $response = array();
    $r = json_decode($app->request->getBody());
    $slide_no = $r->image_data->slide_no;
    $image_number = $r->image_data->image_number;

    //echo $r;
    $file_name = explode('.', $r->image_data->file_name);
    $tfile_name = $file_name[0] . '_edited.' . $file_name[1];
    $ds = DIRECTORY_SEPARATOR;
    $imageData = $r->image_data->file_data;
    list($type, $imageData) = explode(';', $imageData);
    list(, $extension) = explode('/', $type);
    list(, $imageData) = explode(',', $imageData);
    $fileName = dirname(__FILE__) . $ds . "uploads" . $ds . "project" . $ds . $tfile_name; // uniqid().'.'.$extension;

    $imageData = base64_decode($imageData);
    if (file_put_contents($fileName, $imageData)) {
        $sql = "";
        if ($image_number == 1) {
            $sql = "UPDATE report_template_project set image_1 = '$tfile_name' WHERE slide_no = '$slide_no' and user_id = $user_id";
        }
        if ($image_number == 2) {
            $sql = "UPDATE report_template_project set image_2 = '$tfile_name' WHERE slide_no = '$slide_no' and user_id = $user_id";
        }
        if ($image_number == 3) {
            $sql = "UPDATE report_template_project set image_3 = '$tfile_name' WHERE slide_no = '$slide_no' and user_id = $user_id";
        }
        if ($image_number == 4) {
            $sql = "UPDATE report_template_project set image_4 = '$tfile_name' WHERE slide_no = '$slide_no' and user_id = $user_id";
        }
        $result = $db->updateByQuery($sql);
        $response["status"] = "success";
        $response["message"] = "Image Saved successfully ";
        $response["image_name"] = $tfile_name;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to Save Image. Please try again";
        echoResponse(201, $response);
    }
});

$app->post('/save_project_map_image', function () use ($app) {
    session_start();
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }

    $user_id = $session['user_id'];

    $response = array();
    $r = json_decode($app->request->getBody());
    $category = $r->image_data->category;
    $category_id = $r->image_data->category_id;
    $project_id = $r->image_data->category_id;

    $ds = DIRECTORY_SEPARATOR;
    $imageData = $r->image_data->file_data;
    list($type, $imageData) = explode(';', $imageData);
    list(, $extension) = explode('/', $type);
    list(, $imageData) = explode(',', $imageData);

    $count = 1;
    $file_names = "p_" . $project_id . "_" . time() . "_" . $count . "." . $extension;
    $target = dirname(__FILE__) . $ds . "uploads" . $ds . "project" . $ds . $file_names;
    $target_thumb = dirname(__FILE__) . $ds . "uploads" . $ds . "project" . $ds . "thumb" . $ds . $file_names;

    $imageData = base64_decode($imageData);
    if (file_put_contents($target, $imageData)) {
        file_put_contents($target_thumb, $imageData);
        $sql = "UPDATE report_template_project set image_1 = '$file_names' WHERE slide_no = '98' and user_id = $user_id";
        $result = $db->updateByQuery($sql);

        $ismapExists = $db->getOneRecord("select 1 from attachments where category_id=$project_id and category = 'project' and file_category='Location' ");
        if (!$ismapExists) {
            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('project', '$project_id','$file_names','No','false','Location Map','Location','$user_id', now() )";
            $result = $db->insertByQuery($query);
        } else {
            $sql = "UPDATE attachments set filenames = '$file_names' where category_id=$project_id and category = 'project' and file_category='Location' ";
            $result = $db->updateByQuery($sql);
        }
        $response["status"] = "success";
        $response["message"] = "Map Image Saved successfully ";
        $response["image_name"] = $file_names;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to Save Image. Please try again";
        echoResponse(201, $response);
    }
});

$app->get('/getprojectpdf', function () use ($app) {
    $sql = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username'] == "Guest") {
        return;
    }

    $response = array();
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];
    $htmlstring1 = 'hello !!!';

    $sql = "SELECT * from report_template_project WHERE user_id = $user_id ORDER BY slide_no";
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0) {

        while ($row = $stmt->fetch_assoc()) {


        }
    }
    $ds = DIRECTORY_SEPARATOR;

    //https://crm.rdbrothers.com/api/v1/uploads/project/p_518_1648449595_1.jpeg
    $main_heading = "Residential Project for Sale";

    //$main_image = dirname( __FILE__ ). $ds."uploads" . $ds . "project". $ds. "p_518_1648449595_1.jpeg";
    //$logo = dirname( __FILE__ ). $ds."uploads" . $ds . "project". $ds. "mini_logo.png";

    $main_image = "uploads" . $ds . "project" . $ds . "p_518_1648449595_1.jpeg";
    $logo = "uploads" . $ds . "project" . $ds . "mini_logo.png";

    $address = "Astrum, Malad East" . $main_image . "-" . $logo;
    $htmlstring1 = '<div style="font-family: "Source Sans Pro","Helvetica Neue",Helvetica,Arial,sans-serif;padding:0px;margin:0px;">
    <div style="width:1190px;height:245px;background-color:#000000;">
        <div style="width:900px;float:left;padding-left:30px;">
            <p style="font-size:60px;color:#bc9c53;font-weight: bold;margin-bottom:0px;">Residential Project for Sale</p>
            <p style="font-size:45px;color:#bc9c53;margin:10px;">Astrum, Malad East</p>
        </div>
        <div style="width:250px;float:left;">
            <img src="https://crm.rdbrothers.com/api/v1/uploads/project/mini_logo.png" style="width:150px;height:150px;padding-left:100px;padding-top:25px;">
        </div>
    </div>
    <div style="width:1190px;margin-top:5px;">
        <img src="https://crm.rdbrothers.com/api/v1/uploads/project/p_518_1648449595_1.jpeg" style="height:540px;width:1190px;">
    </div>
    <div style="width:1190px;padding:50px;padding-top:20px;">
        <p style="font-size:35px;color:#bc9c53;font-weight: bold;">PROJECT DESCRIPTION</p>
        <div style="width:595px;float:left;">
            <p style="font-size:25px;color:#000000;">Area Available</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">-&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;9th Floor</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;1748 Carpet Area</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">-&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;10th Floor</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;2050 Carpet Area</p>
            <p style="font-size:25px;color:#000000;">Floor Highlights</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">-&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;9th Floor</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;1748 Carpet Area</p>
            
            <p style="font-size:25px;color:#000000;">Furnishings Details</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">-&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;Fully Furnished</p>                           

        </div>
        <div style="width:595px;float:left;">
            <p style="font-size:25px;color:#000000;">Project Features</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">-&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;No. of Floors - 25</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">-&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;No. of Wings - 5</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">-&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;No. of Parking - 1</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">-&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;All Modern Amenities availble</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">-&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;GYM, Swimming Pool, Yoga Room,</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">-&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;Indoor/ Ourdoor Games</p>
            <p style="font-size:25px;color:#000000;">Occupation Certificate</p>
            <p style="font-size:18px;color:#bc9c53;margin-bottom: 5px;margin-top: 10px;">-&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;Yes</p>
        </div>


        
    </div>
    <div style="clear:both"></div>
    <div style="width:1190px;padding:70px;">
        <div style="width:370px;float:left;">
            <div style="width:250px;background-color:#000000;color:#bc9c53;height:60px;">
                <p style="font-size:20px;font-weight: bold;padding-top:15px;padding-left:20px;">Floor Plan</p>
            </div>
        </div>
        <div style="width:370px;float:left;">
            <div style="width:250px;background-color:#000000;color:#bc9c53;height:60px;">
                <p  style="font-size:20px;font-weight: bold;padding-top:15px;padding-left:20px;">Location Map</p>
            </div>
        </div>
        <div style="width:370px;float:left;">
            <div style="width:250px;background-color:#000000;color:#bc9c53;height:60px;">
                <p style="font-size:20px;font-weight: bold;padding-top:15px;padding-left:20px;">Images and Features</p>
            </div>
        </div>
    </div>
    <div style="clear:both"></div>
    <div style="border:3px solid #bc9c53;margin-top:10px;width:1190px;"></div>
    <div style="width:1190px;padding-left:50px">    
        <div style="width:595px;float:left;">
            <p style="font-size:25px;color:#000000;margin-bottom: 5px;margin-top: 10px;">PRAVIN THATIPAMUL</p>
            <p style="font-size:20px;color:#000000;margin-bottom: 5px;margin-top: 10px;">+ 91 9265506644</p>
            <p style="font-size:20px;color:#000000;margin-bottom: 5px;margin-top: 10px;">andhericommercial@rdbrothers.com </p>                     

        </div>
        <div style="width:595px;float:left;">
            <p style="font-size:25px;color:#000000;margin-bottom: 5px;margin-top: 10px;">SUNNY THAKKAR</p>
            <p style="font-size:20px;color:#000000;margin-bottom: 5px;margin-top: 10px;">+ 91 9265506857</p>
            <p style="font-size:20px;color:#000000;margin-bottom: 5px;margin-top: 10px;">sunny@rdbrothers.com </p>
        </div>
    </div>
    <div style="clear:both"></div>
    <div style="border:3px solid #bc9c53;margin-top:10px;width:1190px;"></div>
    <div style="width:1190px;padding-left:50px;">
        <div style="width:370px;float:left;font-size:20px">
            <p>BUILT ON EXPERIENCE<sup>TM</sup></p>
        </div>
        <div style="width:370px;float:left;font-size:20px">
            <p>www.rdbrothers.com</p>                            
        </div>
        <div style="width:370px;float:left;font-size:20px">
            <p>Maha RERA No. A51800025192</p>
        </div>
    </div>
</div>';



    require('newpdf/WriteHTML.php');

    $pdf = new PDF_HTML();

    $pdf->AliasNbPages();
    $pdf->SetAutoPageBreak(true, 15);

    $pdf->AddPage();
    //$pdf->Image('newpdf/logo.png',18,13,33);
    //$pdf->SetFont('Arial','B',14);
    //$pdf->WriteHTML('<para><h1>Techzax Programming Blog, Tutorials, jQuery, Ajax, PHP, MySQL and Demos</h1><br>
    //Website: <u>www.techzax.com</u></para><br><br>How to Convert HTML to PDF with fpdf example');

    //$pdf->SetFont('Arial','B',7); 

    $pdf->WriteHTML("$htmlstring1");
    //$pdf->SetFont('Arial','B',6);
    $ds = DIRECTORY_SEPARATOR;

    $filename = "pdfreport.pdf";
    $target = dirname(__FILE__) . $ds . "uploads" . $ds . "reports" . $ds . $filename;
    $pdf->Output($target, 'F');


    $response["status"] = "success";
    $response["message"] = "PDF created successfully !!! ";
    $response["htmlstring1"] = $htmlstring1;
    echoResponse(200, $response);


});







?>
