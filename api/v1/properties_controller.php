<?php 


// property

$app->get('/properties_list_ctrl/:cat/:id/:next_page_id', function($cat,$id,$next_page_id) use ($app) {
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
    $permissions = explode(",", $session['permissions']);
    $role = $session['role']; 
    $sql = "select * from properties limit 0"; //CAST(a.property_id as UNSIGNED) DESC";
    if ($id==0)
    {
        /*if (in_array("User Level", $permissions))
        {
            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms,i.contact_off,a.share_on_website,a.area_id,a.locality_id, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to))  GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
        }

        if (in_array("Branch User Level", $permissions))
        {
            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms,i.contact_off,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE a.proptype = '$cat' and z.bo_id = $bo_id GROUP BY a.property_id ORDER BY a.modified_date DESC ";//CAST(a.property_id as UNSIGNED) DESC";
        }



        if (in_array("Branch Head Level", $permissions))
        {
            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms,i.contact_off,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE a.proptype = '$cat' and z.bo_id = $bo_id GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
        }


        if (in_array("Admin Level", $permissions))
        {

            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms, i.contact_off,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
        }*/
        if (in_array("Admin", $role) || in_array("Sub Admin", $role))
        {

            // $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms, i.contact_off,i.company_name,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' and a.marketing_property !=1  ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";// GROUP BY a.property_id CAST(a.property_id as UNSIGNED) DESC";
             $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms, i.contact_off,i.company_name,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality,e.city as city, d.area_name as area_name,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.modified_by) as modified_by, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to,  concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' and a.marketing_property !=1  ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";// GROUP BY a.property_id CAST(a.property_id as UNSIGNED) DESC";

        }

        else
        {   
            // $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms,i.contact_off,i.company_name,a.share_on_website,a.area_id,a.locality_id, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status, (SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat'  and a.marketing_property !=1  and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." )  ORDER BY a.modified_date DESC LIMIT $next_page_id,  30";// GROUP BY a.property_id   CAST(a.property_id as UNSIGNED) DESC";
            //   $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms,i.contact_off,i.company_name,a.share_on_website,a.area_id,a.locality_id, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality, d.area_name as area_name,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.modified_by) as modified_by, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status, (SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat'  and a.marketing_property !=1  and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." )  ORDER BY a.modified_date DESC LIMIT $next_page_id,  30";// GROUP BY a.property_id   CAST(a.property_id as UNSIGNED) DESC";
               $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms,i.contact_off,i.company_name,a.share_on_website,a.area_id,a.locality_id, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality, d.area_name as area_name,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.modified_by) as modified_by, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status, (SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat'  and a.marketing_property !=1  and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to))  ORDER BY a.modified_date DESC LIMIT $next_page_id,  30";// GROUP BY a.property_id   CAST(a.property_id as UNSIGNED) DESC";
        }
        // error_log($sql, 3, "logfile1.log");

    }
    else
    {
        $enquiry_for = "";
        $enquiry_type = "";
        $preferred_project_id = 0;
        $preferrred_area_id = 0;
        $budget_range1 = 0;
        $budget_range2 = 0;
        $bedrooms = 0;
        $bath = 0;
    
        $enquirydata = $db->getAllRecords("SELECT * FROM enquiry where enquiry_id = $id");
        
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

        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms, i.contact_off,i.company_name,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by   from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id ";

        /*if (!$ifAdmin)
        {
            $sql .= " LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id ";
        }*/

     
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

        $tsql_matching = "  ";
        $temp_sql = "SELECT * FROM matching WHERE (enquiry_id = $id) " ;

        $temp_stmt = $db->getRows($temp_sql);
    
        if($temp_stmt->num_rows > 0)
        {
            while($temp_row = $temp_stmt->fetch_assoc())
            {
                $tsql_matching .= " OR a.property_id = ".$temp_row['property_id']." ";
            }
        }
        /*if ($bedrooms >0)
        {
            $sql .= " ( a.bedrooms = $bedrooms) and ";
        }
    
        if ($bath >0)
        {
            $sql .= " ( a.bathrooms = $bath) and ";
        }*/
        /*if (!$ifAdmin)
        {
            $sql .= " z.bo_id = $bo_id and ";
        }*/

        //$sql .= " a.deal_done != 'Yes' ";

        $sql .= " ".$tsql_matching." GROUP BY a.property_id ORDER BY a.modified_date DESC "; 

    }

    $properties = $db->getAllRecords($sql);
    //$properties[0]['sql']=$sql;
    echo json_encode($properties);
});


$app->get('/m_properties_list_ctrl/:cat/:id/:next_page_id', function($cat,$id,$next_page_id) use ($app) {
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
    $permissions = explode(",", $session['permissions']);
    $role = $session['role']; 
    $sql = "select * from properties limit 0"; //CAST(a.property_id as UNSIGNED) DESC";
    if ($id==0)
    {
        /*if (in_array("User Level", $permissions))
        {
            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms,i.contact_off,a.share_on_website,a.area_id,a.locality_id, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to))  GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
        }

        if (in_array("Branch User Level", $permissions))
        {
            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms,i.contact_off,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE a.proptype = '$cat' and z.bo_id = $bo_id GROUP BY a.property_id ORDER BY a.modified_date DESC ";//CAST(a.property_id as UNSIGNED) DESC";
        }



        if (in_array("Branch Head Level", $permissions))
        {
            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms,i.contact_off,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE a.proptype = '$cat' and z.bo_id = $bo_id GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
        }


        if (in_array("Admin Level", $permissions))
        {

            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms, i.contact_off,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
        }*/
        //pks commented if else for show data to all 24-04-2023
        // if (in_array("Admin", $role) || in_array("Sub Admin", $role))
        // {

            $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms, i.contact_off,i.company_name,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' and a.marketing_property = 1 ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";// GROUP BY a.property_id CAST(a.property_id as UNSIGNED) DESC";


        // }

        // else
        // {
        //     $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms,i.contact_off,i.company_name,a.share_on_website,a.area_id,a.locality_id, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status, (SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat' and a.marketing_property = 1 and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." )  ORDER BY a.modified_date DESC LIMIT $next_page_id,  30";// GROUP BY a.property_id   CAST(a.property_id as UNSIGNED) DESC";

        // }

    }
    else
    {
        $enquiry_for = "";
        $enquiry_type = "";
        $preferred_project_id = 0;
        $preferrred_area_id = 0;
        $budget_range1 = 0;
        $budget_range2 = 0;
        $bedrooms = 0;
        $bath = 0;
    
        $enquirydata = $db->getAllRecords("SELECT * FROM enquiry where enquiry_id = $id");
        
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

        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms, i.contact_off,i.company_name,a.share_on_website,a.area_id,a.locality_id,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by   from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id ";

        /*if (!$ifAdmin)
        {
            $sql .= " LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id ";
        }*/

     
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
        /*if (!$ifAdmin)
        {
            $sql .= " z.bo_id = $bo_id and ";
        }*/

        //$sql .= " a.deal_done != 'Yes' ";

        $sql .= "  GROUP BY a.property_id ORDER BY a.modified_date DESC "; 

    }

    $m_properties = $db->getAllRecords($sql);
    //$properties[0]['sql']=$sql;
    echo json_encode($m_properties);
});



$app->get('/property_record_count/:cat/:id', function($cat,$id) use ($app) {
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
        $team_query .= " OR teams LIKE '".$value."' ";
    }

    $user_id = $session['user_id'];
    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];
    $sql = "select * from properties limit 0"; //CAST(a.property_id as UNSIGNED) DESC";
    if ($id==0)
    {
        
        if (in_array("Admin", $role) || in_array("Sub Admin", $role))
        {

            $sql = "SELECT count(*) as property_count from property WHERE proptype = '$cat' ";
        }
        else
        {
            // $sql = "SELECT count(*) as property_count from property WHERE proptype = '$cat' and (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) ".$team_query.") ";
            $sql = "SELECT count(*) as property_count from property WHERE proptype = '$cat' and (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) ";

        }

    }
    else
    {
        $enquiry_for = "";
        $enquiry_type = "";
        $preferred_project_id = 0;
        $preferrred_area_id = 0;
        $budget_range1 = 0;
        $budget_range2 = 0;
        $bedrooms = 0;
        $bath = 0;
    
        $enquirydata = $db->getAllRecords("SELECT * FROM enquiry where enquiry_id = $id");
        
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

        $sql = "SELECT count(*) as property_count from property ";

        /*if (!$ifAdmin)
        {
            $sql .= " LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id ";
        }*/

     
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
        /*if (!$ifAdmin)
        {
            $sql .= " z.bo_id = $bo_id and ";
        }*/

        //$sql .= " a.deal_done != 'Yes' ";

        $sql .= " "; 

    }

    $property_counts = $db->getAllRecords($sql);
    //$property_counts[0]['sql']=$sql;
    echo json_encode($property_counts);
});


$app->get('/getassignedproperties', function() use ($app) {
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

    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {

        $sql = "SELECT REPLACE(CONCAT(a.proptype,'-',a.property_id,' ',c.name_title,' ',c.f_name,' ',c.l_name,' ', a.building_name, ' ', a.bedrooms,' BHK ' ,a.propsubtype,' ', a.property_for,' ', a.project_name), '0 BHK', '') as property_title, a.property_id FROM property as a LEFT JOIN contact as c ON a.dev_owner_id = c.contact_id WHERE a.proj_status = 'Available' and a.dev_owner_id >0 GROUP BY a.property_id ORDER BY a.modified_date ";
    }
    else
    {
        $sql = "SELECT REPLACE(CONCAT(a.proptype,'-',a.property_id,' ',c.name_title,' ',c.f_name,' ',c.l_name,' ', a.building_name, ' ', a.bedrooms,' BHK ' ,a.propsubtype,' ', a.property_for,' ', a.project_name), '0 BHK', '') as property_title, a.property_id FROM property as a LEFT JOIN contact as c ON a.dev_owner_id = c.contact_id WHERE (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." ) and a.proj_status = 'Available'  and a.dev_owner_id >0 GROUP BY a.property_id ORDER BY a.modified_date ";

    }

    $properties = $db->getAllRecords($sql);
    $properties[0]['sql']=$sql;
    echo json_encode($properties);
});


$app->get('/share_on_website/:module_name/:id', function($module_name,$id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE property set share_on_website = 'Yes' where property_id = $id " ;  
    if ($module_name == 'project')
    {
        $sql = "UPDATE project set share_on_website = 'Yes' where project_id = $id " ;  
    }
    $result = $db->updateByQuery($sql);
    echo json_encode("done");
});


$app->post('/batch_update', function() use ($app) {
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
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $module_name = $r->batchdata->module_name;
    $r->batchdata->modified_by = $modified_by;
    $r->batchdata->modified_date = $modified_date;
    
    $data = ($r->batchdata->data);
    if ($module_name=='property')
    {
        $sql = "UPDATE property set  modified_by = '$modified_by', modified_date = '$modified_date' ";
        if (isset($r->batchdata->exp_price_para))
        {
            $exp_price_para = $r->batchdata->exp_price_para;
            $sql.=", exp_price_para = '".$exp_price_para."' ";
        }
        
        if (isset($r->batchdata->proj_status))
        {
            $proj_status = $r->batchdata->proj_status;
            $sql.=", proj_status = '".$proj_status."' ";
        }
        if (isset($r->batchdata->subsource_channel))
        {
            $subsource_channel= implode(",",$r->batchdata->subsource_channel);
            $sql.=", subsource_channel = '".$subsource_channel."' ";
            
        }
        if (isset($r->batchdata->source_channel))
        {
            $source_channel = implode(",",$r->batchdata->source_channel);
            $sql.=", source_channel = '".$source_channel."' ";

        }
        if (isset($r->batchdata->locality_id))
        {
            $locality_id = implode(",",$r->batchdata->locality_id);
            $sql.=", locality_id = '".$locality_id."' ";
        }
        if (isset($r->batchdata->area_id))
        {
            $area_id = implode(",",$r->batchdata->area_id);
            $sql.=", area_id = '".$area_id."' ";
        }
        if (isset($r->batchdata->teams))
        {
            $teams = implode(",",$r->batchdata->teams);
            $sql.=", teams = '".$teams."' ";
            
        }
        if (isset($r->batchdata->sub_teams))
        {
            $sub_teams = implode(",",$r->batchdata->sub_teams);
            $sql.=", sub_teams = '".$sub_teams."' ";
            
        }
        if (isset($r->batchdata->assign_to))
        {
            $assign_to = implode(",",$r->batchdata->assign_to);
            $sql.=", assign_to = '".$assign_to."' ";
        }
        if (isset($r->batchdata->con_status))
        {
            $con_status = $r->batchdata->con_status;
            $sql.=", con_status = '".$con_status."' ";
        }
        if (isset($r->batchdata->exp_price))
        {
            $exp_price = $r->batchdata->exp_price;
            $sql.=", exp_price = '".$exp_price."' ";
        }
        if (isset($r->batchdata->pro_sale_para))
        {
            $pro_sale_para = $r->batchdata->pro_sale_para;
            $sql.=", pro_sale_para = '".$pro_sale_para."' ";
        }
        if (isset($r->batchdata->sale_area))
        {
            $sale_area = $r->batchdata->sale_area;
            $sql.=", sale_area = '".$sale_area."' ";
        }
        if (isset($r->batchdata->carp_area))
        {
            $carp_area = $r->batchdata->carp_area;
            $sql.=", carp_area = '".$carp_area."' ";
        }
        if (isset($r->batchdata->carp_area_para))
        {
            $carp_area_para = $r->batchdata->carp_area_para;
            $sql.=", carp_area_para = '".$carp_area_para."' ";
        }
        
        $sql.=" WHERE property_id in (".$data.")";
    }

    if ($module_name=='project')
    {
        $sql = "UPDATE project set  modified_by = '$modified_by', modified_date = '$modified_date' ";
        if (isset($r->batchdata->exp_price_para))
        {
            $exp_price_para = $r->batchdata->exp_price_para;
            $sql.=", exp_price_para = '".$exp_price_para."' ";
        }
        
        if (isset($r->batchdata->proj_status))
        {
            $proj_status = $r->batchdata->proj_status;
            $sql.=", proj_status = '".$proj_status."' ";
        }
        if (isset($r->batchdata->subsource_channel))
        {
            $subsource_channel= implode(",",$r->batchdata->subsource_channel);
            $sql.=", subsource_channel = '".$subsource_channel."' ";
            
        }
        if (isset($r->batchdata->source_channel))
        {
            $source_channel = implode(",",$r->batchdata->source_channel);
            $sql.=", source_channel = '".$source_channel."' ";

        }
        if (isset($r->batchdata->locality_id))
        {
            $locality_id = implode(",",$r->batchdata->locality_id);
            $sql.=", locality_id = '".$locality_id."' ";
        }
        if (isset($r->batchdata->area_id))
        {
            $area_id = implode(",",$r->batchdata->area_id);
            $sql.=", area_id = '".$area_id."' ";
        }
        if (isset($r->batchdata->teams))
        {
            $teams = implode(",",$r->batchdata->teams);
            $sql.=", teams = '".$teams."' ";
            
        }

        if (isset($r->batchdata->sub_teams))
        {
            $sub_teams = implode(",",$r->batchdata->sub_teams);
            $sql.=", sub_teams = '".$sub_teams."' ";
            
        }

        if (isset($r->batchdata->assign_to))
        {
            $assign_to = implode(",",$r->batchdata->assign_to);
            $sql.=", assign_to = '".$assign_to."' ";
        }
        if (isset($r->batchdata->con_status))
        {
            $con_status = $r->batchdata->con_status;
            $sql.=", con_status = '".$con_status."' ";
        }
        if (isset($r->batchdata->exp_price))
        {
            $exp_price = $r->batchdata->exp_price;
            $sql.=", exp_price = '".$exp_price."' ";
        }
        if (isset($r->batchdata->pro_sale_para))
        {
            $pro_sale_para = $r->batchdata->pro_sale_para;
            $sql.=", pro_sale_para = '".$pro_sale_para."' ";
        }
        if (isset($r->batchdata->sale_area))
        {
            $sale_area = $r->batchdata->sale_area;
            $sql.=", sale_area = '".$sale_area."' ";
        }
        if (isset($r->batchdata->carp_area))
        {
            $carp_area = $r->batchdata->carp_area;
            $sql.=", carp_area = '".$carp_area."' ";
        }
        if (isset($r->batchdata->carp_area_para))
        {
            $carp_area_para = $r->batchdata->carp_area_para;
            $sql.=", carp_area_para = '".$carp_area_para."' ";
        }
        
        $sql.=" WHERE project_id in (".$data.")";
    }

    if ($module_name=='enquiry')
    {
        $sql = "UPDATE enquiry set  modified_by = '$modified_by', modified_date = '$modified_date' ";
        /*if (isset($r->batchdata->exp_price_para))
        {
            $exp_price_para = $r->batchdata->exp_price_para;
            $sql.=", exp_price_para = '".$exp_price_para."' ";
        }
        
        if (isset($r->batchdata->proj_status))
        {
            $proj_status = $r->batchdata->proj_status;
            $sql.=", proj_status = '".$proj_status."' ";
        }*/
        if (isset($r->batchdata->subsource_channel))
        {
            $subsource_channel= implode(",",$r->batchdata->subsource_channel);
            $sql.=", subsource_channel = '".$subsource_channel."' ";
            
        }
        if (isset($r->batchdata->source_channel))
        {
            $source_channel = implode(",",$r->batchdata->source_channel);
            $sql.=", source_channel = '".$source_channel."' ";

        }
        if (isset($r->batchdata->locality_id))
        {
            $locality_id = implode(",",$r->batchdata->locality_id);
            $sql.=", locality_id = '".$locality_id."' ";
        }
        if (isset($r->batchdata->area_id))
        {
            $area_id = implode(",",$r->batchdata->area_id);
            $sql.=", area_id = '".$area_id."' ";
        }
        if (isset($r->batchdata->teams))
        {
            $teams = implode(",",$r->batchdata->teams);
            $sql.=", teams = '".$teams."' ";
            
        }

        if (isset($r->batchdata->sub_teams))
        {
            $sub_teams = implode(",",$r->batchdata->sub_teams);
            $sql.=", sub_teams = '".$sub_teams."' ";
            
        }
        if (isset($r->batchdata->created_date))
        {   
            $created_date = $r->batchdata->created_date;
            $tcr_date = substr($created_date,6,4)."-".substr($created_date,3,2)."-".substr($created_date,0,2);
            $sql.=", created_date = '".$tcr_date."' ";
        }

        if (isset($r->batchdata->reg_date))
        {
            $reg_date = $r->batchdata->reg_date;
            $treg_date = substr($reg_date,6,4)."-".substr($reg_date,3,2)."-".substr($reg_date,0,2);
            $sql.=", reg_date = '".$treg_date."' ";
            
        }

        if (isset($r->batchdata->assign_to))
        {
            $assign_to = implode(",",$r->batchdata->assign_to);
            $sql.=", assigned = '".$assign_to."' ";
        }
        /*if (isset($r->batchdata->con_status))
        {
            $con_status = $r->batchdata->con_status;
            $sql.=", con_status = '".$con_status."' ";
        }
        if (isset($r->batchdata->exp_price))
        {
            $exp_price = $r->batchdata->exp_price;
            $sql.=", exp_price = '".$exp_price."' ";
        }
        if (isset($r->batchdata->pro_sale_para))
        {
            $pro_sale_para = $r->batchdata->pro_sale_para;
            $sql.=", pro_sale_para = '".$pro_sale_para."' ";
        }
        if (isset($r->batchdata->sale_area))
        {
            $sale_area = $r->batchdata->sale_area;
            $sql.=", sale_area = '".$sale_area."' ";
        }
        if (isset($r->batchdata->carp_area))
        {
            $carp_area = $r->batchdata->carp_area;
            $sql.=", carp_area = '".$carp_area."' ";
        }
        if (isset($r->batchdata->carp_area_para))
        {
            $carp_area_para = $r->batchdata->carp_area_para;
            $sql.=", carp_area_para = '".$carp_area_para."' ";
        }*/
        
        $sql.=" WHERE enquiry_id in (".$data.")";
    }

    if ($module_name=='contact')
    {
        $sql = "UPDATE contact set  modified_by = '$modified_by', modified_date = '$modified_date' ";
        /*if (isset($r->batchdata->exp_price_para))
        {
            $exp_price_para = $r->batchdata->exp_price_para;
            $sql.=", exp_price_para = '".$exp_price_para."' ";
        }
        
        if (isset($r->batchdata->proj_status))
        {
            $proj_status = $r->batchdata->proj_status;
            $sql.=", proj_status = '".$proj_status."' ";
        }
        if (isset($r->batchdata->subsource_channel))
        {
            $subsource_channel= implode(",",$r->batchdata->subsource_channel);
            $sql.=", subsource_channel = '".$subsource_channel."' ";
            
        }
        if (isset($r->batchdata->source_channel))
        {
            $source_channel = implode(",",$r->batchdata->source_channel);
            $sql.=", source_channel = '".$source_channel."' ";

        }*/
        if (isset($r->batchdata->locality_id))
        {
            $locality_id = implode(",",$r->batchdata->locality_id);
            $sql.=", locality_id = '".$locality_id."' ";
        }
        if (isset($r->batchdata->area_id))
        {
            $area_id = implode(",",$r->batchdata->area_id);
            $sql.=", area_id = '".$area_id."' ";
        }
        if (isset($r->batchdata->teams))
        {
            $teams = implode(",",$r->batchdata->teams);
            $sql.=", teams = '".$teams."' ";
            
        }
        
        if (isset($r->batchdata->sub_teams))
        {
            $sub_teams = implode(",",$r->batchdata->sub_teams);
            $sql.=", sub_teams = '".$sub_teams."' ";
            
        }

        if (isset($r->batchdata->assign_to))
        {
            $assign_to = implode(",",$r->batchdata->assign_to);
            $sql.=", assign_to = '".$assign_to."' ";
        }
        /*if (isset($r->batchdata->con_status))
        {
            $con_status = $r->batchdata->con_status;
            $sql.=", con_status = '".$con_status."' ";
        }
        if (isset($r->batchdata->exp_price))
        {
            $exp_price = $r->batchdata->exp_price;
            $sql.=", exp_price = '".$exp_price."' ";
        }
        if (isset($r->batchdata->pro_sale_para))
        {
            $pro_sale_para = $r->batchdata->pro_sale_para;
            $sql.=", pro_sale_para = '".$pro_sale_para."' ";
        }
        if (isset($r->batchdata->sale_area))
        {
            $sale_area = $r->batchdata->sale_area;
            $sql.=", sale_area = '".$sale_area."' ";
        }
        if (isset($r->batchdata->carp_area))
        {
            $carp_area = $r->batchdata->carp_area;
            $sql.=", carp_area = '".$carp_area."' ";
        }
        if (isset($r->batchdata->carp_area_para))
        {
            $carp_area_para = $r->batchdata->carp_area_para;
            $sql.=", carp_area_para = '".$carp_area_para."' ";
        }*/
        
        $sql.=" WHERE contact_id in (".$data.")";
    }
    
    // ----------------------------------------------------------------------28/07/2023--pks--------------------
    if ($module_name=='expenses')
    {
        $sql = "UPDATE expense set  modified_by = '$modified_by', modified_date = '$modified_date' ";
        if (isset($r->batchdata->payment_status))
        {
            // $payment_status = implode(",",$r->batchdata->payment_status);
            // $sql.=", payment_status = '".$payment_status."' ";
            $payment_status = $r->batchdata->payment_status;
            $sql.=", payment_status = '".$payment_status."' ";
        }
        if (isset($r->batchdata->expense_type))
        {
            // $expense_type = implode(",",$r->batchdata->expense_type);
            // $sql.=", expense_type = '".$expense_type."' ";
            $expense_type = $r->batchdata->expense_type;
            $sql.=", expense_type = '".$expense_type."' ";
        }
        if (isset($r->batchdata->teams))
        {
            $teams = implode(",",$r->batchdata->teams);
            $sql.=", teams = '".$teams."' ";
            
        }
        
        if (isset($r->batchdata->sub_teams))
        {
            $sub_teams = implode(",",$r->batchdata->sub_teams);
            $sql.=", sub_teams = '".$sub_teams."' ";
            error_log("pks1914", 3, "logfile1.log");
            
        }

        if (isset($r->batchdata->assign_to))
        {
            $assign_to = implode(",",$r->batchdata->assign_to);
            $sql.=", assign_to = '".$assign_to."' ";
        }
        
        $sql.=" WHERE expense_id in (".$data.")";
    }
    // ----------------------------------------------------------------------28/07/2023----------------------

    $result = $db->updateByQuery($sql);

    if ($result != NULL)
    {
        $response["status"] = "success";
        $response["message"] = "Data Updated successfully";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Failed to Update Data. Please try again";
        echoResponse(201, $response);
    }

});

$app->get('/getoneproperty/:mailcategory_id', function($mailcategory_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    

    $sql = "SELECT (SELECT GROUP_CONCAT(r.email SEPARATOR ';') from property as q LEFT JOIN contact as r on q.dev_owner_id = r.contact_id WHERE q.property_id IN( $mailcategory_id) and r.email != '') as email_ids , (SELECT GROUP_CONCAT(CONCAT(t.name_title,' ',t.f_name,' ',t.l_name) SEPARATOR '/') from property as s LEFT JOIN contact as t on s.dev_owner_id = t.contact_id WHERE s.property_id IN( $mailcategory_id)) as client_name";
    $getoneproperties = $db->getAllRecords($sql);
    echo json_encode($getoneproperties);
});


$app->get('/getpropertiesbyid/:data', function($data) use ($app) {
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

    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);
});

$app->get('/createreports/:module_name/:id/:data', function($module_name,$id,$data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];
    $sql = "SELECT * from porperty limit 0"; //CAST(a.property_id as UNSIGNED) DESC";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        
        $sql = "SELECT *,a.proptype,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, h.off_email as useremail, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date,  e.city, k.state,l.country,  DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,concat(c.locality,',',e.city) as locality, concat(d.area_name,',',e.city) as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN state as k ON e.state_id = k.state_id LEFT JOIN country as l ON k.country_id = l.country_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE  a.property_id in ($data) GROUP BY a.property_id ";// CAST(a.property_id as UNSIGNED) DESC";
    }

    /*if (in_array("Branch Head Level", $permissions))
    {
        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, h.email as useremail, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE  a.property_id in ($data)  and z.bo_id = $bo_id GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
    }

    if (in_array("Branch User Level", $permissions))
    {
        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, h.email as useremail, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE a.property_id in ($data)  and z.bo_id = $bo_id GROUP BY a.property_id ORDER BY a.modified_date DESC ";//CAST(a.property_id as UNSIGNED) DESC";
    }

    if (in_array("User Level", $permissions))
    {*/
    else
    {
        $sql = "SELECT *,a.proptype,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, h.off_email as useremail, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, e.city, k.state,l.country, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,concat(c.locality,',',e.city) as locality, concat(d.area_name,',',e.city) as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN state as k ON e.state_id = k.state_id LEFT JOIN country as l ON k.country_id = l.country_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE  a.property_id in ($data) GROUP BY a.property_id ";// CAST(a.property_id as UNSIGNED) DESC";
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
    $prop_tax = (int)$propertydata[0]['prop_tax'];
    $cam_charges = (int)$propertydata[0]['cam_charges'];
    $monthle_rent = (int)$propertydata[0]['monthle_rent'];
    $ag_tenure = $propertydata[0]['ag_tenure'];
    $rent_esc = (int) $propertydata[0]['rent_esc'];
    $lease_start = $propertydata[0]['lease_start'];

    
    $location = $propertydata[0]['location'].','.$propertydata[0]['wing'].','.$propertydata[0]['unit'].','.$propertydata[0]['floor'].','.$propertydata[0]['road_no'].','.$propertydata[0]['building_name'].','.$propertydata[0]['landmark'].','.$propertydata[0]['locality'].','.$propertydata[0]['area_name'].','.$propertydata[0]['city'];    
    $furnishing = $propertydata[0]['furniture'];
    if ($propertydata[0]['furniture']=='Furnished')
    {
        $furnishing = $propertydata[0]['furnishing'].','.$propertydata[0]['rece'].' Reception,'.$propertydata[0]['workstation'].' Workstation,'.$propertydata[0]['cabins'].' Cabins,'.$propertydata[0]['cubicals'].' Cubicals,'.$propertydata[0]['conferences'].' Conferences,'.$propertydata[0]['kitchen'].' Kitchen,'.$propertydata[0]['washrooms'].' washrooms';
    }
    

    
    $area_id = $propertydata[0]['area_id'];    
    $area_name = $propertydata[0]['area_name'];
    $locality_id = $propertydata[0]['locality_id'];
    $locality = $propertydata[0]['locality'];

    $map_address = $propertydata[0]['locality'].' '.$propertydata[0]['area_name'].' '.$propertydata[0]['city'].' '.$propertydata[0]['state'].' '.$propertydata[0]['country'];

    $propsubtype = $propertydata[0]['propsubtype'];
    $suitable_for = $propertydata[0]['suitable_for'];
    
    $project_contact = $propertydata[0]['project_contact'];
    $usermobileno = $propertydata[0]['usermobileno'];
    $useremail = $propertydata[0]['useremail'];

    
    
    /*$sqldelete = "DELETE FROM report_template WHERE (slide_no>3 and slide_no<98) or slide_no=0";
    $result = $db->updateByQuery($sqldelete);*/

    $sqldelete = "DELETE FROM report_template WHERE user_id = $user_id";
    $result = $db->updateByQuery($sqldelete);

    $sql = "SELECT * from attachments WHERE category = 'property' and  category_id in ($data) and isdefault = 'true' LIMIT 1";
    $stmt = $db->getRows($sql);
    $category = $module_name;
    $category_id = $data;
    if($stmt->num_rows > 0)
    { 
        while($row = $stmt->fetch_assoc())
        {    
            $description = $row['description'];
            $image_1 = $row['filenames'];
            $category = $row['category'];
            $category_id = $row['category_id'];
            $slide_no = 2;

            $query = "INSERT INTO report_template (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','$description','$image_1','$category','$category_id','$user_id')";
            $result = $db->insertByQuery($query);
            
            /*$sql = "UPDATE report_template set description  = '$description' , image_1 = '$image_1' WHERE slide_no = 2";
            $result = $db->updateByQuery($sql);
            $sql = "UPDATE report_template set category = '$category' , category_id = '$category_id' WHERE slide_no=1 or slide_no=2 or slide_no = 3 or slide_no = 98 or slide_no=99";
            $result = $db->updateByQuery($sql);*/            
        }
    }
    $disclaimer = "The information contained in this electronic message and any attachments to this message are intended for the exclusive use of the addressee(s) and may contain proprietary, confidential or privileged information. If you are not the intended recipient, you must not disseminate, distribute or copy this e-mail or any attachments to it. If you have received this e-mail by error, please let us know immediately by return e-mail and also destroy all copies of this message and the attachments, if any. It may be noted RD BROTHRES PROPERTY CONSULTANT LLP  as real estate solution  provider hereby states that any negotiation or terms of agreement or agreement shall be considered to have been executed only when hard copy of the document is signed by the authorized signatory of the company.";

    $slide_no = 1;
    $query = "INSERT INTO report_template (report_id, slide_no, proptype, description, image_1, category, category_id, carp_area, sale_area, floor, car_park, price_unit, security_depo, lock_per, lease_end, escalation_lease, location, map_address, furnishing, owner_name, mob_no, email, project_contact, usermobileno, useremail, area_id, area_name, locality, locality_id, propsubtype,suitable_for, internal_comment,external_comment,prop_tax, cam_charges,monthle_rent,ag_tenure,rent_esc,lease_start,user_id)   VALUES(1,'$slide_no','$proptype' ,'Main','ppt_main.jpg','$category','$category_id', '$carp_area','$sale_area', '$floor', '$car_park', '$price_unit', '$security_depo', '$lock_per', '$lease_end','$escalation_lease', '$location','$map_address','$furnishing', '$owner_name', '$mob_no', '$email', '$project_contact', '$usermobileno', '$useremail','$area_id','$area_name','$locality','$locality_id', '$propsubtype','$suitable_for','$internal_comment','$external_comment', '$prop_tax', '$cam_charges',  '$monthle_rent', '$ag_tenure', '$rent_esc', '$lease_start', '$user_id')";
    
    $result = $db->insertByQuery($query);

    $slide_no = 3;
    $query = "INSERT INTO report_template (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Commercial Terms','details.jpg','$category','$category_id','$user_id')";
    $result = $db->insertByQuery($query);

    $slide_no = 98;
    $query = "INSERT INTO report_template (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Location Map','location.jpg','$category','$category_id','$user_id')";
    $result = $db->insertByQuery($query);

    $slide_no = 99;
    $query = "INSERT INTO report_template (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Thanks','thanks.jpg','$category','$category_id','$user_id')";
    $result = $db->insertByQuery($query);
    
    /*$sql = "UPDATE report_template set carp_area  = '$carp_area',sale_area  = '$sale_area',floor  = '$floor',car_park  = '$car_park',price_unit  = '$price_unit',security_depo  = '$security_depo',lock_per  = '$lock_per',lease_end  = '$lease_end',escalation_lease  = '$escalation_lease',location  = '$location',furnishing  = '$furnishing', owner_name='$owner_name', mob_no='$mob_no', email='$email', project_contact='$project_contact', usermobileno='$usermobileno', useremail='$useremail', area_id = '$area_id', area_name='$area_name', locality='$locality',locality_id='$locality_id',propsubtype='$propsubtype',suitable_for='$suitable_for',col1_value = '',col2_value = '',col3_value = '',col4_value = '',col5_value = '' WHERE slide_no = 1";
    $result = $db->updateByQuery($sql);*/
    $slide_no = 4;
    if ($proptype=='pre-leased')
    {
        $query = "INSERT INTO report_template (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Floor Plan','floor_plan.jpg','$category','$category_id','$user_id')";
        $result = $db->insertByQuery($query);
        $slide_no = 5;
        $query = "INSERT INTO report_template (report_id, slide_no, description, image_1, category, category_id,user_id)   VALUES(1,'$slide_no','Tenant Details','tenant_details.jpg','$category','$category_id','$user_id')";
        $result = $db->insertByQuery($query);
    }
    $slide_no = 6;
    $sql = "SELECT * from attachments WHERE category = 'property' and  category_id = $category_id and isdefault != 'true'";
    $stmt = $db->getRows($sql);
    if($stmt->num_rows > 0)
    { 
        $image_count = 1;
        
        $image_1 = "";
        $image_2 = "";
        $image_3 = "";
        $image_4 = "";
        $description = "";
        $data_add='yes';
        while($row = $stmt->fetch_assoc())
        {   
            
            if ($image_count==1)
            {
                $description = $row['description'];
                $image_1 = $row['filenames'];
            }
            if ($image_count==2)
            {
                if ($description)
                {
                    $description .= " & ".$row['description'];
                }
                $image_2 = $row['filenames'];
            }
            if ($image_count==3)
            {
                if ($description)
                {
                    $description .= " & ".$row['description'];
                }
                $image_3 = $row['filenames'];
            }
            if ($image_count==4)
            {
                if ($description)
                {
                    $description .= " & ".$row['description'];
                }
                $image_4 = $row['filenames'];
            }
            $image_count++;
            if ($image_count>4)
            {
                $query = "INSERT INTO report_template (report_id, slide_no, description, image_1, image_2, image_3, image_4, category, category_id,user_id)   VALUES(1,'$slide_no','$description','$image_1','$image_2','$image_3','$image_4','$category','$category_id','$user_id')";
                $result = $db->insertByQuery($query);
                $description="";
                $image_1 = "";
                $image_2 = "";
                $image_3 = "";
                $image_4 = "";
                $slide_no ++;
                $data_add='No';
                $image_count = 1;
            }
            
        }
        
        if ($data_add=='No')
        {
            $query = "INSERT INTO report_template (report_id, slide_no, description, image_1, image_2, image_3, image_4, category, category_id,user_id)   VALUES(1,'$slide_no','$description','$image_1','$image_2','$image_3','$image_4','$category','$category_id','$user_id')";
            $result = $db->insertByQuery($query);
        }
    }

    $sql = "SELECT * from report_template WHERE user_id = $user_id ORDER BY slide_no";
    $slide_data = $db->getAllRecords($sql);
    echo json_encode($slide_data);
    

});

$app->get('/multiproperty/:module_name/:id/:data', function($module_name,$id,$data) use ($app) {
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

    $sql = "SELECT *,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for, SUBSTRING(i.f_name,1,1) as f_char,CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, , (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.property_id in ($data) and a.created_by = $user_id GROUP BY a.property_id ORDER BY a.modified_date DESC"; //CAST(a.property_id as UNSIGNED) DESC";

    if (in_array("Admin Level", $permissions))
    {

        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in ($data)  GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
    }

    if (in_array("Branch Head Level", $permissions))
    {
        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE  a.property_id in ($data)  and z.bo_id = $bo_id GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
    }

    if (in_array("Branch User Level", $permissions))
    {
        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id  WHERE a.property_id in ($data)  and z.bo_id = $bo_id GROUP BY a.property_id ORDER BY a.modified_date DESC ";//CAST(a.property_id as UNSIGNED) DESC";
    }

    if (in_array("User Level", $permissions))
    {
        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE  a.property_id in ($data) and a.created_by = $user_id GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
    }


    $propertydata = $db->getAllRecords($sql);

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

    $location = $propertydata[0]['location'].','.$propertydata[0]['wing'].','.$propertydata[0]['unit'].','.$propertydata[0]['floor'].','.$propertydata[0]['road_no'].','.$propertydata[0]['building_name'].','.$propertydata[0]['landmark'].','.$propertydata[0]['locality'].','.$propertydata[0]['area_name'].','.$propertydata[0]['city'];    

    $furnishing = $propertydata[0]['furnishing'].','.$propertydata[0]['rece'].' Reception,'.$propertydata[0]['workstation'].' Workstation,'.$propertydata[0]['cabins'].' Cabins,'.$propertydata[0]['cubicals'].' Cubicals,'.$propertydata[0]['conferences'].' Conferences,'.$propertydata[0]['kitchen'].' Kitchen,'.$propertydata[0]['washrooms'].' washrooms';

    
    $area_id = $propertydata[0]['area_id'];    
    $area_name = $propertydata[0]['area_name'];
    $locality_id = $propertydata[0]['locality_id'];
    $locality = $propertydata[0]['locality'];
    $propsubtype = $propertydata[0]['propsubtype'];
    $suitable_for = $propertydata[0]['suitable_for'];

    $sql = "UPDATE report_template set carp_area  = '$carp_area',sale_area  = '$sale_area',floor  = '$floor',car_park  = '$car_park',price_unit  = '$price_unit',security_depo  = '$security_depo',lock_per  = '$lock_per',lease_end  = '$lease_end',escalation_lease  = '$escalation_lease',location  = '$location',furnishing  = '$furnishing', owner_name='$owner_name', mob_no='$mob_no', email='$email', area_id = '$area_id', area_name='$area_name', locality='$locality',locality_id='$locality_id',propsubtype='$propsubtype',suitable_for='$suitable_for' WHERE slide_no = 1";
    $result = $db->updateByQuery($sql);

    $sqldelete = "DELETE FROM report_template WHERE (slide_no>3 and slide_no<98) or slide_no=0";
    $result = $db->updateByQuery($sqldelete);
    
    $sql = "SELECT * from attachments WHERE category = 'property' and  category_id in ($data) and isdefault = 'true' LIMIT 1";
    $stmt = $db->getRows($sql);
    if($stmt->num_rows > 0)
    { 
        while($row = $stmt->fetch_assoc())
        {    
            $description = $row['description'];
            $image_1 = $row['filenames'];
            $category = $row['category'];
            $category_id = $row['category_id'];
            $sql = "UPDATE report_template set description  = '$description' , image_1 = '$image_1' WHERE slide_no = 2";
            $result = $db->updateByQuery($sql);
            $sql = "UPDATE report_template set category = '$category' , category_id = '$category_id' WHERE slide_no=1 or slide_no=2 or slide_no = 3 or slide_no = 98 or slide_no=99";
            $result = $db->updateByQuery($sql);            
        }
    }

    $sql = "SELECT * from attachments WHERE category = 'property' and  category_id in ($data) and isdefault != 'true'";
    $stmt = $db->getRows($sql);
    if($stmt->num_rows > 0)
    { 
        $image_count = 1;
        $slide_no = 4;
        $image_1 = "";
        $image_2 = "";
        $image_3 = "";
        $image_4 = "";
        $description = "";
        $data_add='yes';
        while($row = $stmt->fetch_assoc())
        {   
            $category = $row['category'];
            $category_id = $row['category_id'];
            if ($image_count==1)
            {
                $description = $row['description'];
                $image_1 = $row['filenames'];
            }
            if ($image_count==2)
            {
                if ($description)
                {
                    $description .= " & ".$row['description'];
                }
                $image_2 = $row['filenames'];
            }
            if ($image_count==3)
            {
                if ($description)
                {
                    $description .= " & ".$row['description'];
                }
                $image_3 = $row['filenames'];
            }
            if ($image_count==4)
            {
                if ($description)
                {
                    $description .= " & ".$row['description'];
                }
                $image_4 = $row['filenames'];
            }
            $image_count++;
            if ($image_count>4)
            {
                $query = "INSERT INTO report_template (report_id, slide_no, description, image_1, image_2, image_3, image_4, category, category_id)   VALUES(1,'$slide_no','$description','$image_1','$image_2','$image_3','$image_4','$category','$category_id')";
                $result = $db->insertByQuery($query);
                $description="";
                $image_1 = "";
                $image_2 = "";
                $image_3 = "";
                $image_4 = "";
                $slide_no ++;
                $data_add='No';
                $image_count = 1;
            }
            
        }
        
        if ($data_add=='No')
        {
            $query = "INSERT INTO report_template (report_id, slide_no, description, image_1, image_2, image_3, image_4, category, category_id)   VALUES(1,'$slide_no','$description','$image_1','$image_2','$image_3','$image_4','$category','$category_id')";
            $result = $db->insertByQuery($query);
        }
    }

    $sql = "SELECT * from report_template ORDER BY slide_no";
    $slide_data = $db->getAllRecords($sql);
    echo json_encode($slide_data);

});



$app->get('/getslidedata', function() use ($app) {
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

    $sql = "SELECT * from report_template WHERE user_id = $user_id ORDER BY slide_no";
    $slide_data = $db->getAllRecords($sql);
    echo json_encode($slide_data);

});



$app->get('/delete_image_record/:slide_no/:id', function($slide_no,$id) use ($app) {
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
    if ($id==1)
    {
        $sql = "UPDATE report_template set image_1 = '' WHERE slide_no = $slide_no and user_id = $user_id" ;
    }
    if ($id==2)
    {
        $sql = "UPDATE report_template set image_2 = '' WHERE slide_no = $slide_no  and user_id = $user_id" ;
    }
    if ($id==3)
    {
        $sql = "UPDATE report_template set image_3 = '' WHERE slide_no = $slide_no  and user_id = $user_id " ;
    }
    if ($id==4)
    {
        $sql = "UPDATE report_template set image_4 = '' WHERE slide_no = $slide_no  and user_id = $user_id" ;
    }
    $result = $db->updateByQuery($sql);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Image Deleted successfully";
        echoResponse(200, $response);
    }
});

$app->get('/save_ppt_description/:slide_no/:description', function($slide_no,$description) use ($app) {
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
    $sql = "UPDATE report_template set description = '$description' WHERE slide_no = $slide_no  and user_id = $user_id" ;
    
    $result = $db->updateByQuery($sql);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Description Updated successfully";
        echoResponse(200, $response);
    }
});


$app->get('/removeslide/:slide_no', function($slide_no) use ($app) {
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
    $sql = "DELETE FROM report_template WHERE slide_no = $slide_no " ;
    $result = $db->updateByQuery($sql);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Slide Deleted successfully";
        echoResponse(200, $response);
    }
});

$app->post('/saveslide', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    $r->slides->report_id = 1;

    $r->slides->image_1 = $r->slides->image_1_name;
    $r->slides->image_2 = $r->slides->image_2_name;
    $r->slides->image_3 = $r->slides->image_3_name;
    $r->slides->image_4 = $r->slides->image_4_name;

    $tabble_name = "report_template";
    $column_names = array('report_id', 'slide_no', 'description', 'image_1', 'image_2', 'image_3', 'image_4', 'category', 'category_id');

    $multiple=array("");

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

$app->post('/saveslidedata', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    $user_id = $session['user_id'];
    
    $multiple=array("");
    $condition = "slide_no=1 and user_id = $user_id";     
    $tabble_name = "report_template";

    $column_names = array('carp_area','sale_area','floor','car_park','price_unit','security_depo','lock_per','lease_end','escalation_lease','location','furnishing', 'internal_comment','external_comment', 'prop_tax', 'cam_charges', 'monthle_rent', 'ag_tenure', 'rent_esc', 'lease_start','col1_heading','col1_value','col2_heading','col2_value','col3_heading','col3_value','col4_heading','col4_value','col5_heading','col5_value');      
    $result = $db->NupdateIntoTable($r->slides, $column_names, $tabble_name,$condition,$multiple);

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

$app->post('/save_image', function() use ($app) {
    session_start();
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];

    $response = array();
    $r = json_decode($app->request->getBody());
    $slide_no = $r->image_data->slide_no;
    $image_number = $r->image_data->image_number;

    //echo $r;
    $file_name = explode('.',$r->image_data->file_name);
    $tfile_name = $file_name[0].'_edited.'.$file_name[1];
    $ds          = DIRECTORY_SEPARATOR;
    $imageData= $r->image_data->file_data;
    list($type, $imageData) = explode(';', $imageData);
    list(,$extension) = explode('/',$type);
    list(,$imageData)      = explode(',', $imageData);
    $fileName = dirname( __FILE__ ). $ds."uploads" . $ds . "property" .$ds. $tfile_name;// uniqid().'.'.$extension;
   
    $imageData = base64_decode($imageData);
    if (file_put_contents($fileName, $imageData))
    {
        $sql = "";
        if ($image_number==1)
        {
            $sql = "UPDATE report_template set image_1 = '$tfile_name' WHERE slide_no = '$slide_no' and user_id = $user_id" ;  
        }
        if ($image_number==2)
        {
            $sql = "UPDATE report_template set image_2 = '$tfile_name' WHERE slide_no = '$slide_no' and user_id = $user_id" ;  
        }
        if ($image_number==3)
        {
            $sql = "UPDATE report_template set image_3 = '$tfile_name' WHERE slide_no = '$slide_no' and user_id = $user_id" ;  
        }
        if ($image_number==4)
        {
            $sql = "UPDATE report_template set image_4 = '$tfile_name' WHERE slide_no = '$slide_no' and user_id = $user_id" ;  
        }
        $result = $db->updateByQuery($sql);
        $response["status"] = "success";
        $response["message"] = "Image Saved successfully ";
        $response["image_name"] = $tfile_name;
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Failed to Save Image. Please try again";
        echoResponse(201, $response); 
    }
});

$app->post('/save_map_image', function() use ($app) {
    session_start();
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];

    $response = array();
    $r = json_decode($app->request->getBody());
    $category = $r->image_data->category;
    $category_id = $r->image_data->category_id;
    $property_id = $r->image_data->category_id;
    
    $ds          = DIRECTORY_SEPARATOR;
    $imageData= $r->image_data->file_data;
    list($type, $imageData) = explode(';', $imageData);
    list(,$extension) = explode('/',$type);
    list(,$imageData)      = explode(',', $imageData);

    $count = 1;
    $file_names = "p_".$property_id."_".time()."_".$count.".".$extension;
    $target = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. $file_names;
    $target_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. "thumb" .$ds. $file_names;

    $imageData = base64_decode($imageData);
    if (file_put_contents($target, $imageData))
    {
        file_put_contents($target_thumb, $imageData);
        $sql = "UPDATE report_template set image_1 = '$file_names' WHERE slide_no = '98' and user_id = $user_id" ;  
        $result = $db->updateByQuery($sql);

        $ismapExists = $db->getOneRecord("select 1 from attachments where category_id=$property_id and category = 'property' and file_category='Location' ");
        if(!$ismapExists)
        {
            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('property', '$property_id','$file_names','No','false','Location Map','Location','$user_id', now() )";
            $result = $db->insertByQuery($query);
        }
        else{
            $sql = "UPDATE attachments set filenames = '$file_names' where category_id=$property_id and category = 'property' and file_category='Location' " ;  
            $result = $db->updateByQuery($sql);
        }
        $response["status"] = "success";
        $response["message"] = "Map Image Saved successfully ";
        $response["image_name"] = $file_names;
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Failed to Save Image. Please try again";
        echoResponse(201, $response); 
    }
});


$app->post('/save_ppt', function() use ($app) {
    session_start();
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    $response = array();
    $r = json_decode($app->request->getBody());
    
    $category = $r->image_data->category;
    $category_id = $r->image_data->category_id;
    $property_id = $r->image_data->category_id;
    
    $ds          = DIRECTORY_SEPARATOR;
    $imageData= $r->image_data->file_data;
    //echo $imageData;
    //return;
    //list($type, $imageData) = explode(';', $imageData);
    //list(,$extension) = explode('/',$type);
    //list(,$imageData)      = explode(',', $imageData);
    

    $count = 1;
    $file_names = "r_".$property_id."_".time()."_".$count.".pptx";
    $target = dirname( __FILE__ ). $ds."uploads" . $ds . "reports". $ds. $file_names;

    $imageData = base64_decode($imageData);
    if (file_put_contents($target, $imageData))
    {

        $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('property_reports', '$property_id','$file_names','No','false','Property Report','Property Report','$user_id', now() )";
        $result = $db->insertByQuery($query);
        $response["status"] = "success";
        $response["message"] = "PPT Saved successfully ";
        $response["image_name"] = $file_names;
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Failed to Save Ppt. Please try again";
        echoResponse(201, $response); 
    }
});


$app->post('/save_onemailer', function() use ($app) {
    session_start();
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    $response = array();
    $r = json_decode($app->request->getBody());
    
    $category = $r->image_data->category;
    $category_id = $r->image_data->category_id;
    $property_id = $r->image_data->category_id;
    
    $ds          = DIRECTORY_SEPARATOR;
    $imageData= $r->image_data->file_data;
    //echo $imageData;
    //return;
    //list($type, $imageData) = explode(';', $imageData);
    //list(,$extension) = explode('/',$type);
    //list(,$imageData)      = explode(',', $imageData);
    

    $count = 1;
    $file_names = "r_".$property_id."_".time()."_".$count.".jpg";
    $target = dirname( __FILE__ ). $ds."uploads" . $ds . "reports". $ds. $file_names;

    $imageData = base64_decode($imageData);
    if (file_put_contents($target, $imageData))
    {

        $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('property_reports', '$property_id','$file_names','No','false','One Mailer Report','One Mailer','$user_id', now() )";
        $result = $db->insertByQuery($query);
        $response["status"] = "success";
        $response["message"] = "One Mailer Saved successfully ";
        $response["image_name"] = $file_names;
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Failed to Save Onemailer. Please try again";
        echoResponse(201, $response); 
    }
});



$app->get('/onemailer/:module_name/:id/:data', function($module_name,$id,$data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $bo_id = $session['bo_id'];
    $user_id = $session['user_id'];
    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];

    $sql = "SELECT *,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for, SUBSTRING(i.f_name,1,1) as f_char,CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, , (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.property_id in ($data) and a.created_by = $user_id GROUP BY a.property_id ORDER BY a.modified_date DESC"; //CAST(a.property_id as UNSIGNED) DESC";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {

        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in ($data)  GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
    }
    else{
        
        $sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.suitable_for,SUBSTRING(i.f_name,1,1) as f_char, a.frontage, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno,(SELECT filenames FROM attachments WHERE h.emp_id = category_id and category='employee') as contact_photo, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE  a.property_id in ($data) and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) GROUP BY a.property_id";// CAST(a.property_id as UNSIGNED) DESC";
    }

    $propertydata = $db->getAllRecords($sql);

    $carp_area = $propertydata[0]['carp_area'];
    $sale_area = $propertydata[0]['sale_area'];
    $floor = $propertydata[0]['floor'];
    $frontage = $propertydata[0]['frontage'];
    
    $car_park = $propertydata[0]['car_park'];
    $price_unit = $propertydata[0]['price_unit'];
    $exp_price = $propertydata[0]['exp_price'];
    $exp_price_para = $propertydata[0]['exp_price_para'];
    $security_depo = $propertydata[0]['security_depo'];
    $lock_per = $propertydata[0]['lock_per'];
    $lease_end = $propertydata[0]['lease_end'];
    $escalation_lease = $propertydata[0]['escalation_lease'];    
    $owner_name = $propertydata[0]['owner_name'];
    $mob_no = $propertydata[0]['mob_no'];
    $email = $propertydata[0]['email'];   

    $project_contact = $propertydata[0]['project_contact'];
    $usermobileno = $propertydata[0]['usermobileno'];
    $contact_photo = $propertydata[0]['contact_photo'];    

    $location = $propertydata[0]['location'].','.$propertydata[0]['wing'].','.$propertydata[0]['unit'].','.$propertydata[0]['floor'].','.$propertydata[0]['road_no'].','.$propertydata[0]['building_name'].','.$propertydata[0]['landmark'].','.$propertydata[0]['locality'].','.$propertydata[0]['area_name'].','.$propertydata[0]['city'];    

    $furnishing = $propertydata[0]['furnishing'].','.$propertydata[0]['rece'].' Reception,'.$propertydata[0]['workstation'].' Workstation,'.$propertydata[0]['cabins'].' Cabins,'.$propertydata[0]['cubicals'].' Cubicals,'.$propertydata[0]['conferences'].' Conferences,'.$propertydata[0]['kitchen'].' Kitchen,'.$propertydata[0]['washrooms'].' washrooms';
    
    $area_id = $propertydata[0]['area_id'];    
    $area_name = $propertydata[0]['area_name'];
    $locality_id = $propertydata[0]['locality_id'];
    $locality = $propertydata[0]['locality'];
    $propsubtype = $propertydata[0]['propsubtype'];
    $suitable_for = $propertydata[0]['suitable_for'];
    $occu_certi = $propertydata[0]['occu_certi'];

    $description = $propertydata[0]['propsubtype'].' for '.$propertydata[0]['property_for'];
    $top_description = $propertydata[0]['external_comment'];
    $disclaimer = "The information contained in this electronic message and any attachments to this message are intended for the exclusive use of the addressee(s) and may contain proprietary, confidential or privileged information. If you are not the intended recipient, you must not disseminate, distribute or copy this e-mail or any attachments to it. If you have received this e-mail by error, please let us know immediately by return e-mail and also destroy all copies of this message and the attachments, if any. It may be noted RD BROTHRES PROPERTY CONSULTANT LLP  as real estate solution  provider hereby states that any negotiation or terms of agreement or agreement shall be considered to have been executed only when hard copy of the document is signed by the authorized signatory of the company.";

    $sqldelete = "DELETE FROM onemailer_template WHERE user_id = $user_id";
    $result = $db->updateByQuery($sqldelete);


    $sql = "INSERT INTO onemailer_template ( description, top_description, disclaimer ,carp_area, sale_area, frontage, floor , exp_price ,exp_price_para,car_park ,price_unit, security_depo, lock_per, lease_end, escalation_lease, location, furnishing , owner_name,  mob_no, email, area_id , area_name, locality, locality_id , propsubtype, suitable_for, occu_certi, project_contact, usermobileno, contact_photo ,category,category_id, user_id) VALUES( '$description', '$top_description', '$disclaimer', '$carp_area', '$sale_area', '$frontage', '$floor', '$exp_price',  '$exp_price_para', '$car_park', '$price_unit', '$security_depo', '$lock_per', '$lease_end', '$escalation_lease', '$location', '$furnishing', '$owner_name', '$mob_no', '$email', '$area_id', '$area_name', '$locality', '$locality_id', '$propsubtype','$suitable_for', '$occu_certi', '$project_contact', '$usermobileno',  '$contact_photo', '$module_name', '$data', '$user_id') ";
    //echo $sql;
    //return;
    $result = $db->insertByQuery($sql);

    /*$sql = "UPDATE onemailer_template set description = '$description', carp_area  = '$carp_area',sale_area  = '$sale_area', frontage  = '$frontage', floor  = '$floor', exp_price  = '$exp_price',exp_price_para  = '$exp_price_para',car_park  = '$car_park',price_unit  = '$price_unit',security_depo  = '$security_depo',lock_per  = '$lock_per',lease_end  = '$lease_end',escalation_lease  = '$escalation_lease',location  = '$location',furnishing  = '$furnishing', owner_name='$owner_name', mob_no='$mob_no', email='$email', area_id = '$area_id', area_name='$area_name', locality='$locality',locality_id='$locality_id',propsubtype='$propsubtype',suitable_for='$suitable_for', occu_certi = '$occu_certi', project_contact = '$project_contact', usermobileno = '$usermobileno', contact_photo = '$contact_photo'  WHERE slide_no = 1";
    $result = $db->updateByQuery($sql);*/

    $sql = "SELECT * from onemailer_template WHERE user_id = $user_id";
    $slide_data = $db->getAllRecords($sql);
    echo json_encode($slide_data);

});


$app->post('/onemailer_saveslidedata', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    $user_id = $session['user_id'];
    $multiple=array("");
    $condition = "user_id=$user_id";     
    $tabble_name = "onemailer_template";
    $r->slides->image_1 = $r->slides->image_1_name;
    $r->slides->image_2 = $r->slides->image_2_name;
    $r->slides->image_3 = $r->slides->image_3_name;
    $column_names = array('location','carp_area','sale_area','exp_price','frontage','suitable_for','occu_certi','top_description','description','disclaimer','image_1','image_2','image_3');      
    $result = $db->NupdateIntoTable($r->slides, $column_names, $tabble_name,$condition,$multiple);

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

$app->get('/onemailer_getslidedata', function() use ($app) {
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

    $sql = "SELECT * from onemailer_template WHERE user_id = $user_id ";
    $slide_data = $db->getAllRecords($sql);
    echo json_encode($slide_data);

});

$app->get('/removeimage/:attachment_id', function($attachment_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        //return;
    }
    $response = array();

    $sql = "DELETE FROM attachments WHERE attachment_id = $attachment_id " ;  
    $result = $db->updateByQuery($sql);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Image Deleted successfully";
        echoResponse(200, $response);
    }
});

$app->get('/getoneproject/:mailcategory_id', function($mailcategory_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from project as a LEFT JOIN contact as b on a.developer_id = b.contact_id WHERE a.project_id = $mailcategory_id ";
    $getoneproject = $db->getAllRecords($sql);
    echo json_encode($getoneproject);
});





$app->get('/getproperties/:project_id', function($project_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $sql = "SELECT *,a.exp_price,a.rera_num, a.pack_price, a.pack_price_para,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.project_id = $project_id GROUP BY a.property_id ORDER BY a.modified_date DESC";

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
                                        <th>Property ID</th>
                                        <th>Floor</th>
                                        <th>Title</th>
                                        <th>Saleable Area</th>
                                        <th>Carpet Area</th>
                                        <th>Price</th>
                                        <th>Package</th>
                                        <th>Matching</th>
                                    </tr>
                                </thead>
                                <tbody>';

    $sql = "SELECT *,a.pack_price, a.pack_price_para from property as a LEFT JOIN project as b on a.project_id = b.project_id WHERE a.project_id = $project_id ORDER BY a.created_date DESC";   
    $stmt = $db->getRows($sql);

    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<tr>
                <td><a href="#/properties_edit/'.$row['property_id'].'">'.$row['proptype'].'_'.$row['property_id'].'</a></td>
                                <td>'.$row['numof_floor'].'</td>
                                <td>'.$row['project_name'].' ';
                                if ($row['bedrooms'])
                                {
                                    $htmlstring .= ' '.$row['bedrooms'].' BHK ';
                                }

                                $htmlstring .= $row['propsubtype'].' '.$row['property_for'].'</td>
                                <td>'.$row['sale_area'].' '.$row['pro_sale_para'].'</td>
                                <td>'.$row['carp_area'].' '.$row['carp_area_para'].'</td>
                                <td>'.$row['exp_price'].' '.$row['exp_price_para'].'</td>
                                <td>'.$row['pack_price'].' '.$row['pack_price_para'].'</td>
                                <td><a href="#/enquiries_list/'.$row['proptype'].'/'.$row['property_id'].'"><p id="me'.$row['property_id'].'" style="font-size: 14px;text-align: center;background-color: #2b57a0;color: #ffffff;border-radius: 12px;width: 22px;height: 22px;padding-top:1px;"  ng-mouseover="me_count('.$row['property_id'].')">.</p></a>
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


$app->get('/getproperties_enquiries/:enquiry_id', function($enquiry_id) use ($app) {
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
        if ($enquiry_for == 'Lease')
        {
            $enquiry_for = 'Rent/Lease';
        }
        if ($enquiry_for == 'Buy')
        {
            $enquiry_for = 'Sale';
        }
        $enquiry_type = $enquirydata[0]['enquiry_type'];
        $preferred_project_id = $enquirydata[0]['preferred_project_id'];
        $preferred_area_id = $enquirydata[0]['preferred_area_id'];
        $budget_range1 = $enquirydata[0]['budget_range1'];
        $budget_range2 = $enquirydata[0]['budget_range2'];
        $bedrooms = $enquirydata[0]['bedrooms'];
        $bath = $enquirydata[0]['bath'];
    }


    $sql = "SELECT *, CONCAT(a.proptype,'-',a.property_id,' ',c.name_title,' ',c.f_name,' ',c.l_name) as property_title from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN contact as c ON a.dev_owner_id = c.contact_id WHERE ";

    
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
        $sql .= " ( a.exp_price >=  $budget_range1 and a.exp_price <= $budget_range2) and ";
    }
    
    if ($bedrooms >0)
    {
        $sql .= " ( a.bedrooms = $bedrooms) and ";
    }

    if ($bath >0)
    {
        $sql .= " ( a.bathrooms = $bath)  and ";
    }

    $sql .=" deal_done != 'Yes' ";
    
    $sql .= " ORDER BY a.property_id DESC"; 
    //echo $sql;
    $m_properties = $db->getAllRecords($sql);
    echo json_encode($m_properties);
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
    //echo $ifAdmin;                          
    //echo $sql;
    //exit(0);
    $stmt = $db->getRows($sql);

    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<tr>
                <td><a href="#/enquiries_edit/'.$row['enquiry_id'].'">'.$row['enquiry_off'].'_'.$row['enquiry_id'].'</a></td>
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
    $htmldata['sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/me_count/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();

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

    $sql = "SELECT count(*) as me_count from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN users as c on a.assigned = c.user_id LEFT JOIN employee as d on c.emp_id = d.emp_id LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id ";
      
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
    
    $enquirydata = $db->getAllRecords($sql);
    $me_count = 0;
    if ($enquirydata)
    {
        $me_count = $enquirydata[0]['me_count'];
    }

    $htmldata['me_count']=$me_count;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/property_count/:cat', function($cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();

    $sql = "SELECT count(*) as property_count from property WHERE proptype = '$cat' ";
    $propertydata = $db->getAllRecords($sql);
    $property_count = 0;
    if ($propertydata)
    {
        $property_count = $propertydata[0]['property_count'];
    }

    $htmldata['property_count']=$property_count;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/mp_count/:enquiry_id', function($enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
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

    $sql = "SELECT count(*) mp_count from property as a LEFT JOIN project as b on a.project_id = b.project_id";

    if (!$ifAdmin)
    {
        $sql .= " LEFT JOIN users as y ON a.created_by = y.user_id LEFT JOIN employee as z ON y.emp_id = z.emp_id ";
    }
 
    $sql .= " WHERE a.proj_status = 'Available' and ";
    
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


    $tsql_matching = "  ";
    $temp_sql = "SELECT * FROM matching WHERE (enquiry_id IN($enquiry_id)) " ;

    $temp_stmt = $db->getRows($temp_sql);

    if($temp_stmt->num_rows > 0)
    {
        while($temp_row = $temp_stmt->fetch_assoc())
        {
            $tsql_matching .= " OR a.property_id = ".$temp_row['property_id']." ";
        }
    }

    if ($tsql_matching != "  ")
    {
        $sql .= " ".$tsql_matching." ";
    }

    $sql .= " ORDER BY a.property_for"; 
    
    $propertydata = $db->getAllRecords($sql);
    $mp_count = 0;
    if ($propertydata)
    {
        $mp_count = $propertydata[0]['mp_count'];
    }

    $htmldata['mp_count']=$mp_count;
    $htmldata['sql']=$sql;

    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/getproperties_from_property/:category_id', function($category_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];

    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }

    $senddata =  array();
    $htmldata = array();
    
    
    $bo_id = $session['bo_id'];
    $role = $session['role'];

    $ifAdmin = false;
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $ifAdmin = true; 
    }

    $sql = "SELECT *,a.exp_price,a.rera_num,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, a.share_on_website, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames ,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE (a.property_id IN ($category_id) ) GROUP by a.property_id "; 
    //echo $sql;
    //return
    $selectproperties = $db->getAllRecords($sql);
    echo json_encode($selectproperties);
});

$app->get('/getreports_of_properties/:category_id', function($category_id) use ($app) {
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
    
    
    $bo_id = $session['bo_id'];
    $role = $session['role'];

    $sql = "SELECT *,DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS created_date FROM attachments WHERE category = 'property_reports' and  category_id IN ($category_id) "; 
    $selectreports = $db->getAllRecords($sql);
    echo json_encode($selectreports);
});

$app->get('/getdocs_of_properties/:category_id', function($category_id) use ($app) {
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
    
    
    $bo_id = $session['bo_id'];
    $role = $session['role'];

    $sql = "SELECT *,DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS created_date FROM attachments WHERE category = 'property_docs' and  category_id IN ($category_id) "; 
    $selectreports = $db->getAllRecords($sql);
    echo json_encode($selectreports);
});


$app->get('/getreports_of_enquiries/:category_id', function($category_id) use ($app) {
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
    
    $bo_id = $session['bo_id'];
    $role = $session['role'];

    $sql = "SELECT * FROM attachments WHERE category = 'property_reports' and  category_id IN ($category_id) "; 
    $selectreports = $db->getAllRecords($sql);
    echo json_encode($selectreports);
});


$app->get('/getproperties_from_enquiry/:enquiry_id', function($enquiry_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];

    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }

    $senddata =  array();
    $htmldata = array();
    $enquiry_for = "";
    $enquiry_type = "";
    $preferred_project_id = 0;
    $preferrred_area_id = 0;
    $budget_range1 = 0;
    $budget_range2 = 0;
    $bedrooms = 0;
    $bath = 0;

    $enquirydata = $db->getAllRecords("SELECT * FROM enquiry where enquiry_id IN($enquiry_id) ");
    
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

    $sql = "SELECT *,a.exp_price,a.rera_num,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, a.share_on_website, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames ,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proj_status = 'Available' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." )";

    
    $sql .= " AND (";
    $first = "No";

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
    

    $tsql_matching = "  ";
    $temp_sql = "SELECT * FROM matching WHERE (enquiry_id IN($enquiry_id)) " ;

    $temp_stmt = $db->getRows($temp_sql);

    if($temp_stmt->num_rows > 0)
    {
        while($temp_row = $temp_stmt->fetch_assoc())
        {
            $tsql_matching .= " OR a.property_id = ".$temp_row['property_id']." ";
        }
    }

    /*if ($bedrooms >0)
    {
        $sql .= " ( a.bedrooms = $bedrooms) and ";
    }

    if ($bath >0)
    {
        $sql .= " ( a.bathrooms = $bath) and ";
    }*/
    

    //$sql .= " a.deal_done != 'Yes' ";

    $sql .= ") ".$tsql_matching." GROUP by a.property_id "; 

    $selectproperties = $db->getAllRecords($sql);
    
    echo json_encode($selectproperties);
});


$app->post('/search_properties', function() use ($app) {
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

    $role = $session['role'];

    $sql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char,CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, , (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {

        $sql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' ";
    }
    else
    {
        $sql = "SELECT *,a.exp_price,a.rera_num,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";
    }


    
    if (isset($r->searchdata->amenities_avl))
    {
        /*$amenities_avl = implode(",",$r->searchdata->amenities_avl);
        $sql .= " and a.amenities_avl in ('".$amenities_avl."') ";
        */
        $sql .= " or a.amenities_avl IN ('".implode("') OR a.amenities_avl IN ( '", $r->searchdata->amenities_avl)."') " ;

        //$condition = ' ('.implode(' OR a.amenities_avl = ', $r->searchdata->amenities_avl).') ';
        //$sql .= $condition;

    }

    if (isset($r->searchdata->assign_to))
    {
        /*$assign_to = implode(",",$r->searchdata->assign_to);
        $sql .= " and a.assign_to in ('".$assign_to."') ";
        */
        $sql .= " or a.assign_to IN (".implode(") OR a.assign_to IN (", $r->searchdata->assign_to).") " ;
        
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
        //$sql .= " and a.area_id = ".$r->searchdata->area_id." ";
        $area_id = implode(",",$r->searchdata->area_id);
        $sql .= " and a.area_id in ('".$area_id."') ";
    }

    if (isset($r->searchdata->broker_id))
    {
        $sql .= " and a.broker_id = ".$r->searchdata->broker_id." ";
    }

    if (isset($r->searchdata->building_name))
    {
        $sql .= " and a.building_name LIKE '%".$r->searchdata->building_name."%' ";
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
        //$sql .= " and a.locality_id = '".$r->searchdata->locality_id."' ";
        $locality_id = implode(",",$r->searchdata->locality_id);
        $sql .= " and a.locality_id in ('".$locality_id."') ";
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
        $sql .= " and a.project_name LIKE '%".$r->searchdata->project_name."%' ";
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
    if (isset($r->searchdata->wing))
    {
        $sql .= " and a.building_name = '".$r->searchdata->wing."' ";
    }

    if (isset($r->searchdata->property_id))
    {
        $sql .= " and a.property_id IN ('".$r->searchdata->property_id."') ";
    }
    
    if (isset($r->searchdata->is_config))
    {
        //$sql .= " and a.project_id > 0 ";
    }


    $sql .= " ORDER BY a.modified_date DESC LIMIT 50";// GROUP BY a.property_id CAST(a.property_id as UNSIGNED) DESC";
    //echo $sql;
    $properties = $db->getAllRecords($sql);
    //$properties[0].['sql']=$sql;
    //echo $sql;
    echo json_encode($properties);
});


$app->post('/newsearch_properties1', function() use ($app) {
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

    $role = $session['role'];

    $sql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char,CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, , (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {

        $sql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' ";
    }
    else
    {
        $sql = "SELECT *,a.exp_price,a.rera_num,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";
    }

    if (isset($r->searchdata->proj_status))
    {
        $new_data = $r->searchdata->proj_status;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.proj_status LIKE '%".$value."%' ";
            }
        }
    }

    if (isset($r->searchdata->property_for))
    {
        $sql .= " and a.property_for = '".$r->searchdata->property_for."' ";
    }

    if (isset($r->searchdata->propsubtype))
    {
        $new_data = $r->searchdata->propsubtype;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.propsubtype LIKE '%".$value."%' ";
            }
        }
        $sql .= " and a.propsubtype = '".$r->searchdata->propsubtype."' ";
    }

    if (isset($r->searchdata->area_id))
    {
        $new_data = $r->searchdata->area_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.area_id LIKE '%".$value."%' ";
            }
        }
    }

    if (isset($r->searchdata->locality_id))
    {
        $new_data = $r->searchdata->locality_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.locality_id LIKE '%".$value."%' ";
            }
        }
    }

    if (isset($r->searchdata->city_id))
    {
        $new_data = $r->searchdata->city_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.city_id LIKE '%".$value."%' ";
            }
        }
    }

    if (isset($r->searchdata->exp_price) && isset($r->searchdata->exp_price2))
    {
        $sql .= " and a.exp_price >= '".$r->searchdata->exp_price."' and a.exp_price2 <= '".$r->searchdata->exp_price2."'";
    }
    else if (isset($r->searchdata->exp_price))
    {
        $sql .= " and a.exp_price = '".$r->searchdata->exp_price."' ";
    }
    else if (isset($r->searchdata->exp_price2))
    {
        $sql .= " and a.exp_price2 = '".$r->searchdata->exp_price2."' ";
    }

    if (isset($r->searchdata->exp_rent) && isset($r->searchdata->exp_rent2))
    {
        $sql .= " and a.exp_rent >= '".$r->searchdata->exp_rent."' and a.exp_rent2 <= '".$r->searchdata->exp_rent2."' ";
    }
    else if (isset($r->searchdata->exp_rent))
    {
        $sql .= " and a.exp_rent = '".$r->searchdata->exp_rent."' ";
    }
    else if (isset($r->searchdata->exp_rent2))
    {
        $sql .= " and a.exp_rent2 = '".$r->searchdata->exp_rent2."' ";
    }

    if (isset($r->searchdata->sale_area) && isset($r->searchdata->sale_area2) )
    {
        $sql .= " and a.sale_area >= '".$r->searchdata->sale_area."' and a.sale_area2 <= '".$r->searchdata->sale_area2."' ";
    }
    else if (isset($r->searchdata->sale_area))
    {
        $sql .= " and a.sale_area = '".$r->searchdata->sale_area."' ";
    }
    else if (isset($r->searchdata->sale_area2))
    {
        $sql .= " and a.sale_area2 = '".$r->searchdata->sale_area2."' ";
    }

    if (isset($r->searchdata->carp_area) && isset($r->searchdata->carp_area2))
    {
        $sql .= " and a.carp_area >= '".$r->searchdata->carp_area."' and a.carp_area2 <= '".$r->searchdata->carp_area2."'";
    }
    else if (isset($r->searchdata->carp_area))
    {
        $sql .= " and a.carp_area = '".$r->searchdata->carp_area."' ";
    }
    else if (isset($r->searchdata->carp_area2))
    {
        $sql .= " and a.carp_area2 = '".$r->searchdata->carp_area2."' ";
    }

    if (isset($r->searchdata->project_name))
    {
        /*$new_data = $r->searchdata->project_name;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.project_name LIKE '%".$value."%' ";
            }
        }*/
        $sql .= " OR a.project_name LIKE '%".$r->searchdata->project_name."%' ";
    }
    if (isset($r->searchdata->building_name))
    {
        /*$new_data = $r->searchdata->building_name;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.building_name LIKE '%".$value."%' ";
            }
        }*/
        $sql .= " OR a.building_name LIKE '%".$r->searchdata->building_name."%' ";
    }
    if (isset($r->searchdata->furniture))
    {
        $new_data = $r->searchdata->furniture;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.furniture LIKE '%".$value."%' ";
            }
        }
    }

    if (isset($r->searchdata->source_channel))
    {
        $new_data = $r->searchdata->source_channel;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.source_channel LIKE '%".$value."%' ";
            }
        }
    }

    if (isset($r->searchdata->source_channel))
    {
        $new_data = $r->searchdata->source_channel;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.source_channel LIKE '%".$value."%' ";
            }
        }
    }

    if (isset($r->searchdata->subsource_channel))
    {
        $new_data = $r->searchdata->subsource_channel;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.subsource_channel LIKE '%".$value."%' ";
            }
        }
    }

    if (isset($r->searchdata->share_on_website))
    {
        $sql .= " and a.share_on_website = '".$r->searchdata->share_on_website."' ";
    }

    if (isset($r->searchdata->owner_id))
    {
        $new_data = $r->searchdata->owner_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.owner_id LIKE '%".$value."%' ";
            }
        }

    }

    if (isset($r->searchdata->developer_id))
    {
        $new_data = $r->searchdata->developer_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.developer_id LIKE '%".$value."%' ";
            }
        }

    }

    if (isset($r->searchdata->broker_id))
    {
        $new_data = $r->searchdata->broker_id;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.broker_id LIKE '%".$value."%' ";
            }
        }

    }

    if (isset($r->searchdata->posted_by))
    {
        $new_data = $r->searchdata->posted_by;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.posted_by LIKE '%".$value."%' ";
            }
        }

    }
    

    if (isset($r->searchdata->possession_date_from))
    {
        $possession_date_from = $r->searchdata->possession_date_from;
        $tpossession_date_from = substr($possession_date_from,6,4)."-".substr($possession_date_from,3,2)."-".substr($possession_date_from,0,2);
        if (isset($r->searchdata->possession_date_to))
        {
            $possession_date_to = $r->searchdata->possession_date_to;
            $tpossession_date_to = substr($possession_date_to,6,4)."-".substr($possession_date_to,3,2)."-".substr($possession_date_to,0,2);
        }
        $sql .= " and a.created_date BETWEEN '".$tpossession_date_from."' AND '".$tpossession_date_to."' ";
    }

    if (isset($r->searchdata->con_status))
    {
        $new_data = $r->searchdata->con_status;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.con_status LIKE '%".$value."%' ";
            }
        }
    }

    if (isset($r->searchdata->amenities_avl))
    {
        $new_data = $r->searchdata->amenities_avl;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.amenities_avl LIKE '%".$value."%' ";
            }
        }


    }
    if (isset($r->searchdata->teams))
    {
        $new_data = $r->searchdata->teams;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.teams LIKE '%".$value."%' ";
            }
        }
    }

    if (isset($r->searchdata->assign_to))
    {
        $new_data = $r->searchdata->assign_to;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.assign_to LIKE '%".$value."%' ";
            }
        }

    }

    if (isset($r->searchdata->wing))
    {
        /*$new_data = $r->searchdata->wing;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.wing LIKE '%".$value."%' ";
            }
        }*/
        $sql .= " OR a.wing LIKE '%".$r->searchdata->wing."%' ";
    }

    if (isset($r->searchdata->unit))
    {
        $sql .= " OR a.unit LIKE'%".$r->searchdata->unit."%' ";
    }   

    if (isset($r->searchdata->floor))
    {
        /*$new_data = $r->searchdata->floor;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.floor LIKE '%".$value."%' ";
            }
        }*/
        $sql .= " OR a.floor LIKE '%".$r->searchdata->floor."%' ";
    }
    
    if (isset($r->searchdata->rera_num))
    {
        $sql .= " OR a.rera_num LIKE '%".$r->searchdata->rera_num."%' ";
    }
    
    if (isset($r->searchdata->is_config))
    {
        if ($r->searchdata->is_config=='Yes')
        {
            $sql .= " and a.is_config = '".$r->searchdata->is_config."' ";
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

   
    if (isset($r->searchdata->property_id))
    {
        //echo "property_id:".$r->searchdata->property_id;
        /*if ($new_data)
        {
            foreach($new_data as $value)
            {
                //echo "inner:".$value;
                $sql .= " OR a.property_id LIKE '%".$value."%' ";
            }
        }*/
        $sql .= " and a.property_id LIKE '%".$r->searchdata->property_id."%' ";
    }
    
    $sql .= "ORDER BY a.modified_date DESC LIMIT 30";// GROUP BY a.property_id  CAST(a.property_id as UNSIGNED) DESC";
    //echo $sql;
    $properties = $db->getAllRecords($sql);
    //$properties[0].['sql']=$sql;
    //echo $sql;
    echo json_encode($properties);
});

$app->post('/newsearch_properties', function() use ($app) {
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

    $searchsql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char,CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, h.mobile_no as usermobileno,a.share_on_website, a.pro_inspect,(SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, , (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames, if(a.exp_price_para='th',a.exp_price*1000,if (a.exp_price_para = 'lac', a.exp_price * 100000, if(a.exp_price_para = 'cr', a.exp_price * 10000000,a.exp_price))) as exp_pricevalue , if (a.exp_price2_para='th',a.exp_price2*1000,if (a.exp_price2_para = 'lac', a.exp_price2 * 100000, if(a.exp_price2_para = 'cr', a.exp_price2 * 10000000,a.exp_price2))) as exp_price2value, if(a.exp_rent_para='th',a.exp_rent*1000,if (a.exp_rent_para = 'lac', a.exp_rent * 100000, if(a.exp_rent_para = 'cr', a.exp_rent * 10000000,a.exp_rent))) as exp_rentvalue , if (a.exp_rent2_para='th',a.exp_rent2*1000,if (a.exp_rent2_para = 'lac', a.exp_rent2 * 100000, if(a.exp_rent2_para = 'cr', a.exp_rent2 * 10000000,a.exp_rent2))) as exp_rent2value from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";

    $countsql = "SELECT count(*) as property_count FROM property ";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {

        $searchsql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno,a.share_on_website, a.pro_inspect,(SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames ,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by , if(a.exp_price_para='th',a.exp_price*1000,if (a.exp_price_para = 'lac', a.exp_price * 100000, if(a.exp_price_para = 'cr', a.exp_price * 10000000,a.exp_price))) as exp_pricevalue , if (a.exp_price2_para='th',a.exp_price2*1000,if (a.exp_price2_para = 'lac', a.exp_price2 * 100000, if(a.exp_price2_para = 'cr', a.exp_price2 * 10000000,a.exp_price2))) as exp_price2value, if(a.exp_rent_para='th',a.exp_rent*1000,if (a.exp_rent_para = 'lac', a.exp_rent * 100000, if(a.exp_rent_para = 'cr', a.exp_rent * 10000000,a.exp_rent))) as exp_rentvalue , if (a.exp_rent2_para='th',a.exp_rent2*1000,if (a.exp_rent2_para = 'lac', a.exp_rent2 * 100000, if(a.exp_rent2_para = 'cr', a.exp_rent2 * 10000000,a.exp_rent2))) as exp_rent2value from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' ";

        $countsql = "SELECT count(*) as property_count FROM property as a WHERE a.proptype = '$cat'  ";
    }
    else
    {
        $searchsql = "SELECT *,a.exp_price,a.rera_num,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, a.share_on_website,a.pro_inspect, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames ,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by, if(a.exp_price_para='th',a.exp_price*1000,if (a.exp_price_para = 'lac', a.exp_price * 100000, if(a.exp_price_para = 'cr', a.exp_price * 10000000,a.exp_price))) as exp_pricevalue , if (a.exp_price2_para='th',a.exp_price2*1000,if (a.exp_price2_para = 'lac', a.exp_price2 * 100000, if(a.exp_price2_para = 'cr', a.exp_price2 * 10000000,a.exp_price2))) as exp_price2value, if(a.exp_rent_para='th',a.exp_rent*1000,if (a.exp_rent_para = 'lac', a.exp_rent * 100000, if(a.exp_rent_para = 'cr', a.exp_rent * 10000000,a.exp_rent))) as exp_rentvalue , if (a.exp_rent2_para='th',a.exp_rent2*1000,if (a.exp_rent2_para = 'lac', a.exp_rent2 * 100000, if(a.exp_rent2_para = 'cr', a.exp_rent2 * 10000000,a.exp_rent2))) as exp_rent2value  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." ) ";

        $countsql = "SELECT count(*) as property_count FROM property as a WHERE a.proptype = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query.") ";

    }
    $sql = " ";
    if (isset($r->searchdata->proj_status))
    {
        $first = "Yes";
        $new_data = $r->searchdata->proj_status;
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
                $sql .= " a.proj_status LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->property_for))
    {
        $sql .= " and a.property_for = '".$r->searchdata->property_for."' ";
    }

    if (isset($r->searchdata->propsubtype))
    {
        $first = "Yes";
        $new_data = $r->searchdata->propsubtype;
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
                $sql .= " a.propsubtype LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
        
    }

    if (isset($r->searchdata->suitable_for))
    {
        $first = "Yes";
        $new_data = $r->searchdata->suitable_for;
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
                $sql .= " a.suitable_for LIKE '%".$value."%' ";
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
                $sql .= " a.area_id = ".$value." ";
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
                $sql .= " a.locality_id = ".$value." ";
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
                $sql .= " a.city_id = ".$value." ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }
    $bsql = "";
    if (isset($r->searchdata->exp_price) && isset($r->searchdata->exp_price2))
    {

        $exp_price = $db->ConvertAmount((float)$r->searchdata->exp_price,$r->searchdata->exp_price_para);
        
        $exp_price2 = $db->ConvertAmount((float)$r->searchdata->exp_price2,$r->searchdata->exp_price2_para);
        
        if ($exp_price>0)
        {
            if ($exp_price2>0)
            {
                $bsql .= " HAVING (exp_pricevalue >= $exp_price and exp_pricevalue <= $exp_price2 ) ";
            }
        }

        //$sql .= " and a.exp_price >= '".$r->searchdata->exp_price."' and a.exp_price2 <= '".$r->searchdata->exp_price2."'";
    }
    else if (isset($r->searchdata->exp_price))
    {
        $sql .= " and a.exp_price = '".$r->searchdata->exp_price."' ";
    }
    else if (isset($r->searchdata->exp_price2))
    {
        $sql .= " and a.exp_price2 = '".$r->searchdata->exp_price2."' ";
    }

    if (isset($r->searchdata->exp_rent) && isset($r->searchdata->exp_rent2))
    {
        $exp_rent = $db->ConvertAmount((float)$r->searchdata->exp_rent,$r->searchdata->exp_rent_para);
        
        $exp_rent2 = $db->ConvertAmount((float)$r->searchdata->exp_rent2,$r->searchdata->exp_rent2_para);
        
        if ($exp_rent>0)
        {
            if ($exp_rent2>0)
            {
                $bsql .= " HAVING (exp_pricevalue >= $exp_rent and exp_pricevalue <= $exp_rent2 ) ";
            }
        }

        //$sql .= " and a.exp_rent >= '".$r->searchdata->exp_rent."' and a.exp_rent2 <= '".$r->searchdata->exp_rent2."' ";
    }
    else if (isset($r->searchdata->exp_rent))
    {
        $sql .= " and a.exp_rent = '".$r->searchdata->exp_rent."' ";
    }
    else if (isset($r->searchdata->exp_rent2))
    {
        $sql .= " and a.exp_rent2 = '".$r->searchdata->exp_rent2."' ";
    }

    if (isset($r->searchdata->sale_area) && isset($r->searchdata->sale_area2) )
    {
        $sql .= " and a.sale_area >= '".$r->searchdata->sale_area."' and a.sale_area2 <= '".$r->searchdata->sale_area2."' ";
    }
    else if (isset($r->searchdata->sale_area))
    {
        $sql .= " and a.sale_area = '".$r->searchdata->sale_area."' ";
    }
    else if (isset($r->searchdata->sale_area2))
    {
        $sql .= " and a.sale_area2 = '".$r->searchdata->sale_area2."' ";
    }

    if (isset($r->searchdata->carp_area) && isset($r->searchdata->carp_area2))
    {
        $sql .= " and a.carp_area >= ".$r->searchdata->carp_area." and a.carp_area <= ".$r->searchdata->carp_area2." ";
    }
    else if (isset($r->searchdata->carp_area))
    {
        $sql .= " and a.carp_area = ".$r->searchdata->carp_area." ";
    }
    else if (isset($r->searchdata->carp_area2))
    {
        $sql .= " and a.carp_area2 = ".$r->searchdata->carp_area2." ";
    }

    if (isset($r->searchdata->project_name))
    {
        $first = "Yes";
        $new_data = $r->searchdata->project_name;
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
                $sql .= " a.project_name LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
        /*$sql .= " OR a.project_name LIKE '%".$r->searchdata->project_name."%' ";*/
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
                $sql .= " a.building_name LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
        /*$sql .= " OR a.building_name LIKE '%".$r->searchdata->building_name."%' ";*/
    }
    if (isset($r->searchdata->furniture))
    {
        $first = "Yes";
        $new_data = $r->searchdata->furniture;
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
                $sql .= " a.furniture = '".$value."' ";
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

    if (isset($r->searchdata->share_website))
    {
        if ($r->searchdata->share_website == 'Yes')
        {
            $sql .= " and a.share_on_website = '".$r->searchdata->share_website."' ";
        }
        else
        {
            $sql .= " and a.share_on_website != 'Yes' ";
        }
        
    }

    if (isset($r->searchdata->pro_inspect))
    {
        if ($r->searchdata->pro_inspect == 'Yes')
        {
            $sql .= " and a.pro_inspect = 'Yes' ";
        }
        else
        {
            $sql .= " and a.pro_inspect != 'Yes' ";
        }
        
    }

    if (isset($r->searchdata->owner_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->owner_id;
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
                $sql .= " a.dev_owner_id = ".$value." ";
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
                $sql .= " a.developer_id = ".$value." ";
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
                $sql .= " a.broker_id = ".$value." ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }

    if (isset($r->searchdata->posted_by))
    {
        $first = "Yes";
        $new_data = $r->searchdata->posted_by;
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
                $sql .= " a.posted_by LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }
    

    if (isset($r->searchdata->possession_date_from))
    {
        $possession_date_from = $r->searchdata->possession_date_from;
        $tpossession_date_from = substr($possession_date_from,6,4)."-".substr($possession_date_from,3,2)."-".substr($possession_date_from,0,2);
        if (isset($r->searchdata->possession_date_to))
        {
            $possession_date_to = $r->searchdata->possession_date_to;
            $tpossession_date_to = substr($possession_date_to,6,4)."-".substr($possession_date_to,3,2)."-".substr($possession_date_to,0,2);
        }
        $sql .= " and a.possession_date BETWEEN '".$tpossession_date_from."' AND '".$tpossession_date_to."' ";
    }

    if (isset($r->searchdata->con_status))
    {
        $first = "Yes";
        $new_data = $r->searchdata->con_status;
        if ($new_data)
        {
            foreach($new_data as $value)
            {
                $sql .= " OR a.con_status LIKE '%".$value."%' ";
            }
        }
        if ($first=='No')
        {
            $sql .= ") ";
        
        }
    }

    if (isset($r->searchdata->amenities_avl))
    {
        $first = "Yes";
        $new_data = $r->searchdata->amenities_avl;
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
                $sql .= " a.amenities_avl LIKE '%".$value."%' ";
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

    if (isset($r->searchdata->wing))
    {
        $first = "Yes";
        $new_data = $r->searchdata->wing;
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
                $sql .= " a.wing = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
        //$sql .= " OR a.wing LIKE '%".$r->searchdata->wing."%' ";
    }

    if (isset($r->searchdata->unit))
    {
        $first = "Yes";
        $new_data = $r->searchdata->unit;
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
                $sql .= " a.unit = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
       // $sql .= " OR a.unit LIKE'%".$r->searchdata->unit."%' ";
    }   

    if (isset($r->searchdata->floor))
    {
        $first = "Yes";
        $new_data = $r->searchdata->floor;
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
                $sql .= " a.floor = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
        //$sql .= " OR a.floor LIKE '%".$r->searchdata->floor."%' ";
    }

    if (isset($r->searchdata->bedrooms))
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
                $sql .= " a.bedrooms = '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
        //$sql .= " OR a.floor LIKE '%".$r->searchdata->floor."%' ";
    }

    
    if (isset($r->searchdata->rera_num))
    {
        $sql .= " OR a.rera_num LIKE '%".$r->searchdata->rera_num."%' ";
    }
    
    if (isset($r->searchdata->is_config))
    {
        if ($r->searchdata->is_config=='Yes')
        {
            $sql .= " and a.project_id > 0 ";
        }
        else{
            $sql .= " and a.project_id = 0 ";
        }
        
    }

    if (isset($r->searchdata->created_date_from))
    {
        $created_date_from = $r->searchdata->created_date_from;
        $tcreated_date_from = substr($created_date_from,6,4)."-".substr($created_date_from,3,2)."-".substr($created_date_from,0,2)." 00:00:00";
        if (isset($r->searchdata->created_date_to))
        {
            $created_date_to = $r->searchdata->created_date_to;
            $tcreated_date_to = substr($created_date_to,6,4)."-".substr($created_date_to,3,2)."-".substr($created_date_to,0,2)." 23:59:59";
        }
        $sql .= " and a.created_date BETWEEN '".$tcreated_date_from."' AND '".$tcreated_date_to."' ";
    }

   
    if (isset($r->searchdata->property_id))
    {
        //echo "property_id:".$r->searchdata->property_id;
        $first='Yes';
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
                $sql .= " a.property_id = ".$value." ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
        //$sql .= " and a.property_id LIKE '%".$r->searchdata->property_id."%' ";
    }
    $searchsql .=  $sql . "  ".$bsql. " ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";//GROUP BY a.property_id   CAST(a.property_id as UNSIGNED) DESC";

    $properties = $db->getAllRecords($searchsql);

    $countsql = $countsql . $sql;
    $property_count = 0;
    $propertycountdata = $db->getAllRecords($countsql);
    if ($propertycountdata)
    {
         $property_count = $propertycountdata[0]['property_count'];

    }
    //if ($property_count>0)
    //{
        $properties[0]['property_count']=$property_count;
    //}
    //$properties[0]['countsql']=$countsql;
    $properties[0]['sql']=$searchsql;
    //echo $sql;
    echo json_encode($properties);
});

$app->get('/find_data/:find_what/:cat', function($find_what,$cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];
    $permissions = $session['permissions'];
    $role = $session['role'];
    
    $sql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char,CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, , (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team , (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {

        $sql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' ";
    }
    else
    {
        $sql = "SELECT *,a.exp_price,a.rera_num,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id WHERE a.proptype = '$cat' and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ";
    }

    $sql .= " and (a.proj_status LIKE '%".$find_what."%' OR a.propsubtype LIKE '%$find_what%'  OR a.property_for LIKE '%$find_what%'   OR a.project_name LIKE '%$find_what%' OR a.building_name LIKE '%$find_what%' OR d.area_name LIKE '%$find_what%' OR c.locality LIKE '%$find_what%'  OR e.city LIKE '%$find_what%' OR i.f_name LIKE '%$find_what%' OR i.l_name LIKE '%$find_what%' OR a.wing LIKE '%$find_what%' OR a.unit LIKE '%$find_what%' OR a.rera_num LIKE '%$find_what%'  OR a.furniture LIKE '%$find_what%' OR a.source_channel LIKE '%$find_what%'  OR a.subsource_channel LIKE '%$find_what%'  OR a.amenities_avl LIKE '%$find_what%' OR a.floor LIKE '%$find_what%'  OR a.con_status LIKE '%$find_what%'  OR i.mob_no LIKE '%$find_what%'   OR i.email LIKE '%$find_what%'  OR a.landmark LIKE '%$find_what%' OR a.parking LIKE '%$find_what%'   OR a.road_no LIKE '%$find_what%'  OR a.priority LIKE '%$find_what%'  OR a.property_id LIKE '%$find_what%' ) ";

    $sql .= "GROUP BY a.property_id ORDER BY a.modified_date DESC LIMIT 30";// CAST(a.property_id as UNSIGNED) DESC";
    //echo $sql;
    $properties = $db->getAllRecords($sql);
    //$properties[0].['sql']=$sql;
    //echo $sql;
    echo json_encode($properties);
});

$app->get('/getdatavalues/:field_name/:cat', function($field_name,$cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM property WHERE proptype = '$cat' ORDER BY $field_name";
    //echo $sql;
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});


$app->get('/getdropdown_values/:field_name/:value/:cat', function($field_name,$value,$cat) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT '$field_name' FROM property  WHERE a.proptype = '$cat'  and '$field_name' LIKE '%$value%' ORDER BY '$field_name' LIMIT 20";
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});


$app->post('/properties_add_new', function() use ($app) 
{ 
    // alert json_decode('hiii');
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('property_for'),$r->property);
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $assign_to = $r->property->assign_to;       

    

    if (isset($r->property->old_data))
    {
        if ($r->property->old_data=="Yes")
        {
            $created_date = date('2020-12-31 10:55:23');
        }
    }
    
    $r->property->created_by = $created_by;
    $r->property->created_date = $created_date;
    $r->property->modified_date = $created_date;


    if (isset($r->property->possession_date))
    {
        $possession_date = $r->property->possession_date;
        $tpossession_date = substr($possession_date,6,4)."-".substr($possession_date,3,2)."-".substr($possession_date,0,2);
        $r->property->possession_date = $tpossession_date;
    }

    if (isset($r->property->completion_date))
    {
        $completion_date = $r->property->completion_date;
        $tcompletion_date = substr($completion_date,6,4)."-".substr($completion_date,3,2)."-".substr($completion_date,0,2);
        $r->property->completion_date= $tcompletion_date;
    }

    if (isset($r->property->lease_start))
    {
        $lease_start = $r->property->lease_start;
        $tlease_start = substr($lease_start,6,4)."-".substr($lease_start,3,2)."-".substr($lease_start,0,2);
        $r->property->lease_start= $tlease_start;
    }

    if (isset($r->property->lease_end))
    {
        $lease_end = $r->property->lease_end;
        $tlease_end = substr($lease_end,6,4)."-".substr($lease_end,3,2)."-".substr($lease_end,0,2);
        $r->property->lease_end= $tlease_end;
    }

    if (isset($r->property->reg_date))
    {
        $reg_date = $r->property->reg_date;
        $treg_date = substr($reg_date,6,4)."-".substr($reg_date,3,2)."-".substr($reg_date,0,2);
        $r->property->reg_date= $treg_date;
    }

    if (isset($r->property->exp_price))
    {
        if (isset($r->property->exp_price_para))
        {
            $exp_price = ($r->property->exp_price);
            $r->property->exp_price = $exp_price;
        }
    }

    if (isset($r->property->exp_price2))
    {
        if (isset($r->property->exp_price2_para))
        {
            $exp_price2 = ($r->property->exp_price2);
            $r->property->exp_price2 = $exp_price2;
        }
    }

    if (isset($r->property->price_unit))
    {
        if (isset($r->property->price_unit_para))
        {
            $price_unit = ($r->property->price_unit);
            $r->property->price_unit = $price_unit;
        }
    }
    
    if (isset($r->property->park_charge))
    {
        if (isset($r->property->park_charge_para))
        {
            $park_charge = $r->property->park_charge;
            $r->property->park_charge = $park_charge;
        }
    }

    if (isset($r->property->price_unit_carpet))
    {
        if (isset($r->property->price_unit_carpet_para))
        {
            $price_unit_carpet = ($r->property->price_unit_carpet);
            $r->property->price_unit_carpet = $price_unit_carpet;
        }
    }

    if (isset($r->property->pack_price))
    {
        if (isset($r->property->pack_price_para))
        {
            $pack_price =($r->property->pack_price);
            $r->property->pack_price = $pack_price;
        }
    }

    if (isset($r->property->security_depo))
    {
        if (isset($r->property->security_depo_para))
        {
            $security_depo = ($r->property->security_depo);
            $r->property->security_depo = $security_depo;
        }
    }

    if (isset($r->property->main_charges))
    {
        if (isset($r->property->main_charges_para))
        {
            $main_charges = ($r->property->main_charges);
            $r->property->main_charges = $main_charges;
        }
    }
     
    $tabble_name = "property";
    $column_names = array('ageofprop', 'availablefor', 'amenities_avl','area_id','area_para','assign_to','balconies','bathrooms','bedrooms','broke_involved','cabins','campaign','car_park','carp_area','carp_area2','city','completion_date','con_status','config','country','cubicals','dev_owner_id','distfrm_dairport','distfrm_market','distfrm_school','distfrm_station','distfrm_highway','door_fdir','efficiency','email_saletrainee','exlocation','exp_price','exp_price2','external_comment','floor','frontage','furniture','groups','height','internal_comment','keywith','kitchen','washrooms','lift','loading','locality_id','mpm_cam','mpm_tot_tax','mpm_unit','numof_floor','occu_certi','cc','oth_charges','pack_price','pack_price_comments','floor_rise','parking','possession_date','powersup','pre_leased','price_unit','price_unit_carpet','deposite_month','security_depo','ag_tenure','lock_per','rent_esc','notice_period','stamp_duty','main_charges','prop_tax','mainroad','internalroad','park_charge','conferences','tenant','other_tenant','pro_inspect','vastu_comp','pro_sale_para','pro_specification','proj_status','project_id','property_for','propfrom','propsubtype','proptype','reg_date','rera_num','review','sale_area','sale_area2','seaters','sms_saletrainee','sms_saletrainee_office','marketing_property','soc_reg','source_channel','share_on_website','share_on_99','acers_99_projid' , 'featured','state','subsource_channel','suitable_for', 'teams','sub_teams','terrace','tranf_charge','unit','usd_area','watersup','wing','wings','workstation','meeting_room','server_room', 'youtube_link','created_by','created_date','modified_date','building_name','road_no','landmark','zip','multi_size','tenant_name','occ_details','rented_area','roi','lease_start','lease_end','rent_per_sqft','monthle_rent','pre_leased_rent','cam_charges','fur_charges','priority','pooja_room','study_room','servent_room','store_room','dry_room','garden_facing','tenant1','carp_area_para','terrace_para','exp_price_para', 'exp_price2_para', 'exp_rent_para', 'exp_rent2_para', 'price_unit_para', 'price_unit_carpet_para', 'main_charges_para', 'pack_price_para', 'security_depo_para','park_charge_para','rece','task_id');

    $multiple=array("amenities_avl","assign_to","groups","parking","pro_specification","source_channel","subsource_channel","teams","sub_teams",'suitable_for');

    $result = $db->insertIntoTable($r->property, $column_names, $tabble_name, $multiple);

    if ($result != NULL) 
    {
        $response["status"] = "success";
        $response["message"] = "Property created successfully";
        $response["property_id"] = $result;
        $_SESSION['tmpproperty_id'] = $result;
        $category_id = $result;

        // custom line start  
        if ($r->property->sms_saletrainee == true) 
        {                  
            $assgn=json_encode($r->property->assign_to);
            $Arr_assign=json_decode($assgn);
            $ArrMob=array();
            $ass_name='';
            foreach($Arr_assign as $ass){
                $sql = "SELECT * FROM users  WHERE user_id =".$ass;
                $getdropdown_values = $db->getAllRecords($sql); 
                $emp_id=$getdropdown_values[0]['emp_id'];
                $sql = "SELECT * FROM employee  WHERE emp_id =".$emp_id;
                $mobile = $db->getAllRecords($sql);
                array_push($ArrMob,$mobile[0]['mobile_no']);  
                $name1  = $mobile[0]['salu']; 
                $name2  = $mobile[0]['fname'];  
                $name3  = $mobile[0]['lname'];  
                $ass_name .=$name1 .$name2 .' ' .$name3.',';
             }
                $ass_mobile_no=implode(',', $ArrMob);
                $ass_name=rtrim($ass_name, ", ");

            $owner_idd = json_encode($r->property->dev_owner_id);
            $Arr_owner = json_decode($owner_idd);
            $sql       = "SELECT * FROM contact  WHERE contact_id =".$Arr_owner;
            $getdropdown_values = $db->getAllRecords($sql);
            $mob_number  = $getdropdown_values[0]['mob_no'];            
            $curl        = curl_init();
            $post_fields = array();
            $post_fields["method"]  = "sendMessage";
            $post_fields["send_to"] = $mob_number;  
            $post_fields["msg"]     = "Thank you for Registering your Enquiry with R.D. Brothers Properties! Please note your Enquiry Registration ID : $result for future correspondence. For more details contact: $ass_name on $ass_mobile_no ";    
            $post_fields["msg_type"] = "TEXT";
            $post_fields["userid"]   = "2000196081";
            $post_fields["password"] = "Rdbrothers@123";
            $post_fields["v"]        = "1.1";
            $post_fields["auth_scheme"] = "PLAIN";
            $post_fields["format"]      = "JSON";    
            curl_setopt_array($curl, array(CURLOPT_URL => "https://enterprise.smsgupshup.com/GatewayAPI/rest",CURLOPT_RETURNTRANSFER => true,CURLOPT_ENCODING => "",CURLOPT_MAXREDIRS => 10,CURLOPT_TIMEOUT =>30,CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,CURLOPT_CUSTOMREQUEST => "POST",CURLOPT_POSTFIELDS => $post_fields));
           $tresponse = curl_exec($curl);
           $t         = json_decode($tresponse);
           $data      = $t->response->id;
           $res       = curl_error($curl);
           curl_close($curl);
           if ($res) {
             $response["status"] = "error";
            $response["message"] = "SMS not Sent ...! ".$res;
            } else 
            {
            $response["status"] = "success";
            $response["message"] = "SMS has been sent ...!";
            $response["response_received"] = $data;        
            $mail_date = date('Y-m-d');
            $created_date = date('Y-m-d H:i:s');        
            $query = "INSERT INTO client_sms (category,category_id, receipient, message, sms_response,status,created_by,  created_date)  VALUES('$category', '$category_id', '$receipient', '$message','$data','success','$created_by', '$created_date')";       
            $result = $db->insertByQuery($query);
            }
        } 

        if ($r->property->sms_saletrainee_office == true) 
        {           

            $assgn=json_encode($r->property->assign_to);
            $Arr_assign=json_decode($assgn);
            $ArrMob=array();
            foreach($Arr_assign as $ass){
                $sql = "SELECT * FROM users  WHERE user_id =".$ass;
                $getdropdown_values = $db->getAllRecords($sql); 
                $emp_id=$getdropdown_values[0]['emp_id'];
                $sql = "SELECT * FROM employee  WHERE emp_id =".$emp_id;
                $mobile = $db->getAllRecords($sql);
                array_push($ArrMob,$mobile[0]['mobile_no']);   
            }
            $mobile_no=implode(',', $ArrMob);

            $owner_idd = json_encode($r->property->dev_owner_id);
            $Arr_owner = json_decode($owner_idd);
            $sql       = "SELECT * FROM contact  WHERE contact_id =".$Arr_owner;
            $getdropdown_values = $db->getAllRecords($sql);
            $name1  = $getdropdown_values[0]['name_title'];  
            $name2 = $getdropdown_values[0]['f_name'];  
            $name3= $getdropdown_values[0]['l_name'];  
            $name =$name1  .$name2 .' ' .$name3;
            $email  = $getdropdown_values[0]['email'];  
           $curl = curl_init();
           $post_fields = array();
           $post_fields["method"]   = "sendMessage";
           $post_fields["send_to"]  = $mobile_no;       
           $post_fields["msg"]      =  "Property Id: $category_id is assigned to you, Client contact details:$name on $mob_number, $email";    
           $post_fields["msg_type"] = "TEXT";
           $post_fields["userid"]   = "2000196081";
           $post_fields["password"] = "Rdbrothers@123";
           $post_fields["v"]        = "1.1";
           $post_fields["auth_scheme"] = "PLAIN";
           $post_fields["format"]      = "JSON";    
           curl_setopt_array($curl, array(CURLOPT_URL => "https://enterprise.smsgupshup.com/GatewayAPI/rest",CURLOPT_RETURNTRANSFER => true,CURLOPT_ENCODING => "",CURLOPT_MAXREDIRS => 10,CURLOPT_TIMEOUT =>30,CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,CURLOPT_CUSTOMREQUEST => "POST",CURLOPT_POSTFIELDS => $post_fields));
           $tresponse = curl_exec($curl);
           $t         = json_decode($tresponse);
           $data      = $t->response->id;
           $res       = curl_error($curl);
           curl_close($curl);
           if ($res) {
             $response["status"] = "error";
            $response["message"] = "SMS not Sent ...! ".$res;
            } else 
            {
            $response["status"]  = "success";
            $response["message"] = "SMS has been sent ...!";
            $response["response_received"] = $data;        
            $mail_date    = date('Y-m-d');
            $created_date = date('Y-m-d H:i:s');        
            $query        = "INSERT INTO client_sms (category,category_id, receipient, message, sms_response,status,created_by,  created_date)  VALUES('$category', '$category_id', '$receipient', '$message','$data','success','$created_by', '$created_date')";       
            $result       = $db->insertByQuery($query);
            }
        }           
        // custom line end


            
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

            $toemail = "shekhar.lanke@gmail.com";


            $cc_mail_id = "";
            $email_subject = "New Property Assigned to you [PROPERTYID:".$result."]";
            $message_to_send = "<p>New Property Assigned to you [PROPERTYID:".$result."]</p><p></p><p>Thank you..</p><p></p>";
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
                $response["message1"] = "Mail has been sent ...!";
                $mail_date = date('Y-m-d');
                $created_date = date('Y-m-d H:i:s');
                $query = "INSERT INTO client_mails (mail_date , category,category_id, subject, text_message, from_mail_id, to_mail_id, cc_mail_id,created_by,  created_date)   VALUES('$mail_date' , 'property', '$category_id', '$email_subject', '$email_body', '$myemail','$toemail', '$cc_mail_id', '$created_by', '$created_date')";
                $result = $db->insertByQuery($query);
            }
        //}
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
    $watermark_png_file = 'watermark.png'; //watermark png file
    $images = $_FILES['file-1'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "p_".$property_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. $file_names;
        $target_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. "thumb" .$ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        //if(move_uploaded_file($images['tmp_name'][$i], $target)) {

            switch(strtolower($images['type'][$i])){ //determine uploaded image type 
                //Create new image from file
                case 'image/png': 
                    $image_resource =  imagecreatefrompng($images['tmp_name'][$i]);
                    break;
                case 'image/gif':
                    $image_resource =  imagecreatefromgif($images['tmp_name'][$i]);
                    break;          
                case 'image/jpeg': case 'image/pjpeg':
                    $image_resource = imagecreatefromjpeg($images['tmp_name'][$i]);
                    break;
                default:
                    $image_resource = false;
            }
            if($image_resource){
                //Copy and resize part of an image with resampling
                list($img_width, $img_height) = getimagesize($images['tmp_name'][$i]);
        
                $new_canvas         = imagecreatetruecolor($img_width , $img_height);
                if(imagecopyresampled($new_canvas, $image_resource , 0, 0, 0, 0, $img_width, $img_height, $img_width, $img_height))
                {
                    $watermark_left = ($img_width/2)-(247/2); //watermark left
                    $watermark_bottom = ($img_height/2)-(77/2); //watermark bottom
                    $watermark = imagecreatefrompng($watermark_png_file); //watermark image
                    imagecopy($new_canvas, $watermark, $watermark_left, $watermark_bottom, 0, 0, 247, 77); 
                    imagejpeg($new_canvas, $target , 100);
                    imagedestroy($new_canvas);
                }
                // ORIGINAL 
                /*$new_canvas         = imagecreatetruecolor(245 , 185);
                if(imagecopyresampled($new_canvas, $image_resource , 0, 0, 0, 0, 245, 185, $img_width, $img_height))
                {
                    $watermark_left = (245/2)-(247/2); //watermark left
                    $watermark_bottom = (185/2)-(77/2); //watermark bottom
                    $watermark = imagecreatefrompng($watermark_png_file); //watermark image
                    imagecopy($new_canvas, $watermark, $watermark_left, $watermark_bottom, 0, 0, 247, 77); 
                    imagejpeg($new_canvas, $target_thumb , 90);
                }*/

                // SUGGESTED BY KEVIN
                $new_canvas         = imagecreatetruecolor(397 , 260);
                if(imagecopyresampled($new_canvas, $image_resource , 0, 0, 0, 0, 397, 260, $img_width, $img_height))
                {
                    $watermark_left = (397/2)-(247/2); //watermark left
                    $watermark_bottom = (260/2)-(77/2); //watermark bottom
                    $watermark = imagecreatefrompng($watermark_png_file); //watermark image
                    imagecopy($new_canvas, $watermark, $watermark_left, $watermark_bottom, 0, 0, 247, 77); 
                    imagejpeg($new_canvas, $target_thumb , 90);
                    imagedestroy($new_canvas);
                }
        
            }

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

            if (isset($_POST['file_sub_category_'.$count]))
            {
                $file_sub_category = $_POST['file_sub_category_'.$count];
            }
            else
            {
                $file_sub_category = "";
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

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, file_sub_category, created_by, created_date)   VALUES('property', '$property_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$file_sub_category','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        /*} else {
            $success = false;
            break;
        }*/
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});


$app->post('/property_uploads_occu', function() use ($app) {
    session_start();
    $property_id = $_SESSION['tmpproperty_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_occu'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_occu'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "poc_".$property_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. 'occu_cert' .$ds. $file_names;
        
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

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('property_occu_cert', '$property_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->post('/property_uploads_docs', function() use ($app) {
    session_start();
    $property_id = $_SESSION['tmpproperty_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_docs'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_docs'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "pdocs_".$property_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. 'docs' .$ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames,  created_by, created_date)   VALUES('property_docs', '$property_id','$file_names','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->post('/property_uploads_videos', function() use ($app) {
    session_start();
    $property_id = $_SESSION['tmpproperty_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_videos'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_videos'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "pvideos_".$property_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. 'videos' .$ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames,  created_by, created_date)   VALUES('property_videos', '$property_id','$file_names','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->post('/project_uploads_videos', function() use ($app) {
    session_start();
    $project_id = $_SESSION['tmpproject_id'];
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $user_id = $session['user_id'];
    
    if (empty($_FILES['file_videos'])) {
        echo json_encode(['error'=>'No files found for upload.']); 
        return; // terminate
    }
    
    $images = $_FILES['file_videos'];
    $paths= [];
    $filenames = $images['name'];
    $count = 1;
    for($i=0; $i < count($filenames); $i++){
        $ds          = DIRECTORY_SEPARATOR;
        $ext = strtolower(pathinfo($filenames[$i], PATHINFO_EXTENSION));
        $file_names = "pvideos_".$property_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "project". $ds. 'videos' .$ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames,  created_by, created_date)   VALUES('project_videos', '$project_id','$file_names','$user_id', now() )";
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
    $created_date = date('Y-m-d H:i:s');
    $r->multiproperty->created_by = $created_by;
    $r->multiproperty->created_date = $created_date;
    $r->multiproperty->modified_date = $created_date;
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
        $column_names = array('project_name','propfrom','dev_owner_id','property_for','con_status','possession_date','completion_date','rera_num','add1','add2','exlocation','locality_id','area_id','zip','amenities_avl','parking','teams','assign_to','source_channel','subsource_channel','groups','internal_comment','external_comment','numof_floor','distfrm_station','distfrm_dairport','distfrm_school','distfrm_market','wing','floor','bathrooms','bedrooms','building_name','multi_size','oth_charges','project_id','propsubtype','proptype','sale_area','sale_area2','pro_sale_para','carp_area','carp_area2','carp_area_para','exp_price','exp_price_para','exp_price2','exp_price2_para','price_unit','price_unit_carpet','pack_price','pack_price_para','floor_rise','created_by','created_date','modified_date');
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
    $sql = "SELECT *,a.area_id,a.locality_id, CONCAT(b.locality,' (',c.area_name,')') as locality, CONCAT(c.area_name,' (',d.city,')') as area_name,DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end, CONCAT(e.name_title,' ',e.f_name,' ',e.l_name,'   ',e.mob_no,'  ',e.company_name) as dev_owner_name,a.assign_to,a.source_channel,a.subsource_channel,a.propsubtype from property as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c on a.area_id = c.area_id LEFT JOIN city as d on c.city_id = d.city_id LEFT JOIN contact as e ON e.contact_id = a.dev_owner_id where a.property_id=".$property_id;
    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);
    
});

$app->get('/getcategory_from_id/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT proptype from property as a where a.property_id=".$property_id;
    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);    
});




$app->get('/create_new_property/:property_id', function($property_id) use ($app) {
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
    "INSERT INTO property (prefix, property_code, project_id, project_name, proptype, propsubtype, suitable_for, propfrom, dev_owner, dev_owner_id, property_for, building_plot, owner_mobile, owner_email, config, con_status, possession_date, completion_date, rera_num, bedrooms, building_name, unit, floor, road_no, wing, landmark, add1, add2, exlocation, locality, locality_id, area, area_id, city, state, country, zip, multi_size, sale_area, sale_area2, sale_area_upside, pro_sale_para, carp_area, carp_area2, carp_area_upside, loading, efficiency, usd_area, area_para, exp_price, exp_price2, exp_price_upside, exp_rent, exp_rent2, exprice_para, price_unit, price_unit_carpet, psale_area, car_area, price_carpet, pack_price, package_para, pack_price_comments, deposite_month, security_depo, security_para, rent_esc, main_charges, prop_tax, lease_lock, agree_month, escalation_lease, bathrooms, terrace, kitchen, tenant, tenant1, other_tenant, workstation, cabins, cubicals, conferences, seaters, furniture, teams, assign_to, sms_saletrainee, email_saletrainee, source_channel, subsource_channel, campaign, proj_status, broke_involved, pro_inspect, vastu_comp, groups, img, internal_comment, external_comment, amenities_avl, pro_specification, parking, mainroad, internalroad, door_fdir, keywith, ageofprop, mpm_unit, mpm_unit_para, mpm_cam, mpm_tot_tax, oth_charges, tranf_charge, car_park, park_charge, lift, numof_floor, balconies, wings, distfrm_station, distfrm_dairport, distfrm_highway, distfrm_school, distfrm_market, height, frontage, watersup, powersup, occu_certi, soc_reg, cc, suitablefor, youtube_link, review, reg_date, pre_leased, tenant_name, occ_details, lease_tot_area, roi, rented_area, pre_leased_rent, cam_charges, fur_charges, lease_start, lease_end, rent_per_sqft, rent_per_sqft_para, monthle_rent, dep_months, sec_dep, ag_tenure, lock_per, tenure_year, escalation, asset_id, published, deal_done, priority, pooja_room, study_room, servent_room, store_room, dry_room, washrooms, garden_facing, carp_area_para, terrace_para, exp_price_para, exp_price2_para, exp_rent_para, exp_rent2_para, price_unit_para, price_unit_carpet_para, main_charges_para, pack_price_para, security_depo_para, availablefor, rece, park_charge_para)  SELECT prefix, property_code, project_id, project_name, proptype, propsubtype, suitable_for, propfrom, dev_owner, dev_owner_id, property_for, building_plot, owner_mobile, owner_email, config, con_status, possession_date, completion_date, rera_num, bedrooms, building_name, unit, floor, road_no, wing, landmark, add1, add2, exlocation, locality, locality_id, area, area_id, city, state, country, zip, multi_size, sale_area, sale_area2, sale_area_upside, pro_sale_para, carp_area, carp_area2, carp_area_upside, loading, efficiency, usd_area, area_para, exp_price, exp_price2, exp_price_upside, exp_rent, exp_rent2, exprice_para, price_unit, price_unit_carpet, psale_area, car_area, price_carpet, pack_price, package_para, pack_price_comments, deposite_month, security_depo, security_para, rent_esc, main_charges, prop_tax, lease_lock, agree_month, escalation_lease, bathrooms, terrace, kitchen, tenant, tenant1, other_tenant, workstation, cabins, cubicals, conferences, seaters, furniture, teams, assign_to, sms_saletrainee, email_saletrainee, source_channel, subsource_channel, campaign, proj_status, broke_involved, pro_inspect, vastu_comp, groups, img, internal_comment, external_comment, amenities_avl, pro_specification, parking, mainroad, internalroad, door_fdir, keywith, ageofprop, mpm_unit, mpm_unit_para, mpm_cam, mpm_tot_tax, oth_charges, tranf_charge, car_park, park_charge, lift, numof_floor, balconies, wings, distfrm_station, distfrm_dairport, distfrm_highway, distfrm_school, distfrm_market, height, frontage, watersup, powersup, occu_certi, soc_reg, cc, suitablefor, youtube_link, review, reg_date, pre_leased, tenant_name, occ_details, lease_tot_area, roi, rented_area, pre_leased_rent, cam_charges, fur_charges, lease_start, lease_end, rent_per_sqft, rent_per_sqft_para, monthle_rent, dep_months, sec_dep, ag_tenure, lock_per, tenure_year, escalation, asset_id, published, deal_done, priority, pooja_room, study_room, servent_room, store_room, dry_room, washrooms, garden_facing, carp_area_para, terrace_para, exp_price_para, exp_price2_para, exp_rent_para, exp_rent2_para, price_unit_para, price_unit_carpet_para, main_charges_para, pack_price_para, security_depo_para, availablefor, rece, park_charge_para FROM property WHERE property_id = $property_id";
    $result = $db->insertByQuery($sql);
    $response["property_id"] = $result;
    $new_property_id = $result;
    $sql = "UPDATE property set created_by = '$created_by' , created_date = '$created_date' , modified_date = '$modified_date' WHERE property_id = $new_property_id";
    $result = $db->updateByQuery($sql);
    $ds = DIRECTORY_SEPARATOR;
    $sql = "SELECT * from attachments WHERE category = 'Property' and category_id = $property_id";
    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        $count = 1;
        $ext = 'jpg';
        while($row = $stmt->fetch_assoc())
        {
            $source_file_names = $row['filenames'];
            $source = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. $source_file_names;

            $source_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. "thumb" .$ds. $source_file_names;

            $file_names = "p_".$new_property_id."_".time()."_".$count.".".$ext;
            $target = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. $file_names;
            $target_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. "thumb" .$ds. $file_names;

            copy($source,$target);
            copy($source_thumb,$target_thumb);

            $share_on_web = $row['share_on_web'];
            $isdefault = $row['isdefault'];
            $description = $row['description'];
            $file_category = $row['$file_category'];

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('property', '$new_property_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$created_by', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        }
    }

    $response["status"] = "success";
    $response["message"] = "Property Created !!!";
    echo json_encode($response);

});

$app->get('/copy_project_images/:project_id', function($project_id) use ($app) {
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

    $ds = DIRECTORY_SEPARATOR;

    $sql = "SELECT * from property WHERE project_id = $project_id";
    $stmt = $db->getRows($sql);
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $property_id = $row['property_id'];
            $sqlattachment = "SELECT * from attachments WHERE category = 'Project' and category_id = $project_id";
            $stmtattachment = $db->getRows($sqlattachment);
    
            if($stmtattachment->num_rows > 0)
            {
                $count = 1;
                $ext = 'jpg';
                while($rowattachment = $stmtattachment->fetch_assoc())
                {
                    $property_id = $row['property_id'];
                    $source_file_names = $rowattachment['filenames'];
                    $source = dirname( __FILE__ ). $ds."uploads" . $ds . "project". $ds. $source_file_names;

                    $source_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "project". $ds. "thumb" .$ds. $source_file_names;

                    $file_names = "p_".$property_id."_".time()."_".$count.".".$ext;
                    $target = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. $file_names;
                    $target_thumb = dirname( __FILE__ ). $ds."uploads" . $ds . "property". $ds. "thumb" .$ds. $file_names;

                    copy($source,$target);
                    copy($source_thumb,$target_thumb);

                    $share_on_web = $rowattachment['share_on_web'];
                    $isdefault = $rowattachment['isdefault'];
                    $description = $rowattachment['description'];
                    $file_category = $rowattachment['$file_category'];
                    $file_sub_category = $rowattachment['$file_sub_category'];

                    $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, file_sub_category, created_by, created_date)   VALUES('property', '$property_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$file_sub_category','$created_by', now() )";
                    $result = $db->insertByQuery($query);
                    $count ++;


                }
            }
        }

    }
    $response["status"] = "success";
    $response["message"] = "Copied Property Images !!!";
    echo json_encode($response);

});


$app->get('/properties_images/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'property' and category_id = $property_id";
    $property_images = $db->getAllRecords($sql);
    echo json_encode($property_images);
});

$app->get('/properties_docs/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'property_docs' and category_id = $property_id";
    $property_docs = $db->getAllRecords($sql);
    echo json_encode($property_docs);
});

$app->get('/properties_videos/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'property_videos' and category_id = $property_id";
    $property_videos = $db->getAllRecords($sql);
    echo json_encode($property_videos);
});

$app->get('/project_videos/:project_id', function($project_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'project_videos' and category_id = $project_id";
    $project_videos = $db->getAllRecords($sql);
    echo json_encode($project_videos);
});

$app->get('/properties_imagesslide/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
   
    $sql = "SELECT attachment_id as id , description as title, file_category as 'desc', CONCAT('api/v1/uploads/property/thumb/',filenames) as thumbUrl, CONCAT('api/v1/uploads/property/thumb/',filenames) as bubbleUrl, CONCAT('api/v1/uploads/property/',filenames) as url  from attachments WHERE category = 'property' and category_id = $property_id";

    $property_images = $db->getAllRecords($sql);
    echo json_encode($property_images);
});

$app->get('/property_occu_cert/:property_id', function($property_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'property_occu_cert' and category_id = $property_id";
    $property_occu_certs = $db->getAllRecords($sql);
    echo json_encode($property_occu_certs);
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
    $modified_date = date('Y-m-d H:i:s');
    if (isset($r->property->old_data))
    {
        if ($r->property->old_data=="Yes")
        {
            $modified_date = date('2020-12-31 10:55:23');
        }
    }
    $r->property->modified_by = $modified_by;
    $r->property->modified_date = $modified_date;

    if (isset($r->property->possession_date))
    {
        $possession_date = $r->property->possession_date;
        $tpossession_date = substr($possession_date,6,4)."-".substr($possession_date,3,2)."-".substr($possession_date,0,2);
        $r->property->possession_date = $tpossession_date;
    }

    if (isset($r->property->completion_date))
    {
        $completion_date = $r->property->completion_date;
        $tcompletion_date = substr($completion_date,6,4)."-".substr($completion_date,3,2)."-".substr($completion_date,0,2);
        $r->property->completion_date= $tcompletion_date;
    }

    if (isset($r->property->park_charge))
    {
        if (isset($r->property->park_charge_para))
        {
            $park_charge = ($r->property->park_charge);
            $r->property->park_charge = $park_charge;
        }
    }
    if (isset($r->property->lease_start))
    {
        $lease_start = $r->property->lease_start;
        $tlease_start = substr($lease_start,6,4)."-".substr($lease_start,3,2)."-".substr($lease_start,0,2);
        $r->property->lease_start= $tlease_start;
    }

    if (isset($r->property->lease_end))
    {
        $lease_end = $r->property->lease_end;
        $tlease_end = substr($lease_end,6,4)."-".substr($lease_end,3,2)."-".substr($lease_end,0,2);
        $r->property->lease_end= $tlease_end;
    }

    if (isset($r->property->reg_date))
    {
        $reg_date = $r->property->reg_date;
        $treg_date = substr($reg_date,6,4)."-".substr($reg_date,3,2)."-".substr($reg_date,0,2);
        $r->property->reg_date= $treg_date;
    }

    if (isset($r->property->exp_price))
    {
        if (isset($r->property->exp_price_para))
        {
            $exp_price = ($r->property->exp_price);
            $r->property->exp_price = $exp_price;
        }
    }

    if (isset($r->property->exp_price2))
    {
        if (isset($r->property->exp_price2_para))
        {
            $exp_price2 = ($r->property->exp_price2);
            $r->property->exp_price2 = $exp_price2;
        }
    }

    if (isset($r->property->price_unit))
    {
        if (isset($r->property->price_unit_para))
        {
            $price_unit = ($r->property->price_unit);
            $r->property->price_unit = $price_unit;
        }
    }

    if (isset($r->property->price_unit_carpet))
    {
        if (isset($r->property->price_unit_carpet_para))
        {
            $price_unit_carpet = ($r->property->price_unit_carpet);
            $r->property->price_unit_carpet = $price_unit_carpet;
        }
    }

    if (isset($r->property->pack_price))
    {
        if (isset($r->property->pack_price_para))
        {
            $pack_price =($r->property->pack_price);
            $r->property->pack_price = $pack_price;
        }
    }

    if (isset($r->property->security_depo))
    {
        if (isset($r->property->security_depo_para))
        {
            $security_depo = ($r->property->security_depo);
            $r->property->security_depo = $security_depo;
        }
    }

    if (isset($r->property->main_charges))
    {
        if (isset($r->property->main_charges_para))
        {
            $main_charges = ($r->property->main_charges);
            $r->property->main_charges = $main_charges;
        }
    }


    $property_id  = $r->property->property_id;
    $ispropertyExists = $db->getOneRecord("select 1 from property where property_id=$property_id");
    if($ispropertyExists){
        $tabble_name = "property";
        $column_names = array('ageofprop', 'availablefor', 'amenities_avl','area_id','area_para','assign_to','balconies','bathrooms','bedrooms','broke_involved','cabins','campaign','car_park','carp_area','carp_area2','city','completion_date','con_status','config','country','cubicals','dev_owner_id','distfrm_dairport','distfrm_market','distfrm_school','distfrm_station','distfrm_highway','door_fdir','efficiency','email_saletrainee','exlocation','exp_price','exp_price2','external_comment','floor','frontage','furniture','groups','height','internal_comment','keywith','kitchen','washrooms','lift','loading','locality_id','mpm_cam','mpm_tot_tax','mpm_unit','numof_floor','occu_certi','cc','oth_charges','pack_price','pack_price_comments','floor_rise','parking','possession_date','powersup','pre_leased','price_unit','price_unit_carpet','deposite_month','security_depo','ag_tenure','lock_per','rent_esc','notice_period','stamp_duty','main_charges','prop_tax','mainroad','internalroad','park_charge','conferences','tenant','other_tenant','pro_inspect','vastu_comp','pro_sale_para','pro_specification','share_on_website','share_on_99','acers_99_projid' ,'featured','proj_status','project_id','property_for','propfrom','propsubtype','proptype','reg_date','rera_num','review','sale_area','sale_area2','seaters','sms_saletrainee','marketing_property','soc_reg','source_channel','state','subsource_channel','suitable_for', 'teams','sub_teams','terrace','tranf_charge','unit','usd_area','watersup','wing','wings','workstation','meeting_room','server_room','youtube_link','modified_by','modified_date','building_name','road_no','landmark','zip','multi_size','tenant_name','occ_details','rented_area','roi','lease_start','lease_end','rent_per_sqft','monthle_rent','pre_leased_rent','cam_charges','fur_charges','priority','pooja_room','study_room','servent_room','store_room','dry_room','garden_facing','tenant1','carp_area_para','terrace_para','exp_price_para', 'exp_price2_para', 'exp_rent_para', 'exp_rent2_para', 'price_unit_para', 'price_unit_carpet_para','park_charge_para', 'main_charges_para', 'pack_price_para', 'security_depo_para','rece','task_id');

        $multiple=array("amenities_avl","assign_to","groups","parking","pro_specification","source_channel","subsource_channel","teams","sub_teams",'suitable_for');

        $condition = "property_id='$property_id'";
        $history = $db->historydata( $r->property, $column_names, $tabble_name,$condition,$property_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->property, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Property Updated successfully";
            $_SESSION['tmpproperty_id'] = $property_id;
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
    $modified_date = date('Y-m-d H:i:s');
    $r->multiproperty->modified_by = $modified_by;
    $r->multiproperty->modified_date = $modified_date;

    $property_id  = $r->multiproperty->property_id;
    $ispropertyExists = $db->getOneRecord("select 1 from property where property_id=$property_id");
    if($ispropertyExists){
        $tabble_name = "property";
        $column_names = array('wing','floor','bathrooms','bedrooms','building_plot','carp_area','carp_area_upside','exp_price','exp_price_upside','multi_size','oth_charges','pack_price','floor_rise','price_unit','price_unit_carpet','project_id','propsubtype','proptype','sale_area','sale_area_upside','modified_by','modified_date');
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
    $sql = "SELECT *, CONCAT(a.proptype,'_',a.property_id,'-',i.name_title,' ',i.f_name,' ',i.l_name) as property_title, CONCAT(a.project_name,' ', a.propsubtype, ' for ', a.property_for) as project_name ,a.exp_price, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from property as t where a.project_id = t.project_id GROUP by t.project_id) as properties_count from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i on i.contact_id = a.dev_owner_id WHERE a.proj_status = 'Available' ORDER BY a.created_by DESC";
    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);
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

$app->get('/property_image_update/:attachment_id/:field_name/:value', function($attachment_id,$field_name,$value) use ($app) {
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


$app->get('/getproperties_exists/:contact_id', function($contact_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT *, CONCAT(b.locality,' (',c.area_name,')') as locality, CONCAT(c.area_name,' (',d.city,')') as area_name,DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date, CONCAT(e.name_title,' ',e.f_name,' ',e.l_name,'   ',e.mob_no,'  ',e.company_name) as dev_owner_name from property as a LEFT JOIN locality as b on a.locality_id = b.locality_id LEFT JOIN areas as c on a.area_id = c.area_id LEFT JOIN city as d on c.city_id = d.city_id LEFT JOIN contact as e ON e.contact_id = a.dev_owner_id where a.dev_owner_id=".$contact_id." LIMIT 10";

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
            $htmlstring .= '<li><a href="#/properties_edit/'.$row['property_id'].'"> Property_ID'.$row['property_id'].' - '.$row['buiding_name'].' '.$row['propsubtype'].' for '.$row['property_for'].'</a></li>';
            $count = $count + 1;
   
        }
    }
    $htmlstring .='</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['count']=$count;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/matching_properties_list/:cat', function($cat) use ($app) {
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
            
    $sql="SELECT *  from property LIMIT 0";
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {

        $sql="SELECT a.property_id,CONCAT('Property_','',a.property_id,' ',b.name_title,' ',b.f_name,' ',b.l_name) as select_property_value from property as a LEFT JOIN contact as b on a.dev_owner_id = b.contact_id  where a.proptype = '$cat'  ORDER BY a.property_id";
    }
    else
    {
        

        $sql="SELECT a.property_id,CONCAT('Property_','',a.property_id,' ',b.name_title,' ',b.f_name,' ',b.l_name) as select_property_value from property as a LEFT JOIN contact as b on a.dev_owner_id = b.contact_id  where a.proptype = '$cat'  and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." )  ORDER BY a.property_id";

    }
    
    $properties = $db->getAllRecords($sql);
    $properties[0]['sql']=$sql;
    echo json_encode($properties);
});


$app->get('/link_to_property/:property_ids/:enquiry_ids', function($property_ids, $enquiry_ids) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $property_ids = explode(",", $property_ids);
    $enquiry_ids = explode(",", $enquiry_ids);
    foreach($property_ids as $property_value)
    {
        $property_id = $property_value;
        foreach($enquiry_ids as $enquiry_value)
        {
            
            $enquiry_id = $enquiry_value;
            $query = "INSERT INTO matching (property_id,enquiry_id, created_by,  created_date)   VALUES( '$property_id', '$enquiry_id', '$created_by', '$created_date')";
            $result = $db->insertByQuery($query);
        }
    }
    echo json_encode($result);
});


?>