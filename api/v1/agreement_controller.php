<?php


// AGREEMENTS 

$app->get('/agreement_list_ctrl/:cat/:next_page_id/:agreement_stage', function($cat,$next_page_id,$agreement_stage) use ($app) {
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

    $sql = "SELECT * from agreement LIMIT 0";
    $countsql = "SELECT count(*) as agreement_count FROM agreement";
    $sql_cat="";
    if ($cat != 'direct')
    {
        $sql_cat = " and b.proptype = '$cat' ";
    }

    $limit_option = " LIMIT $next_page_id, 30 ";

    $agreement_stage_sql = " a.agreement_status !='Completed' ";

    if ($agreement_stage=='Open')
    {
        $agreement_stage_sql = " a.agreement_status !='Completed' ";
        $limit_option = "  ";
    }

    if ($agreement_stage=='Completed')
    {
        $agreement_stage_sql = " a.agreement_status ='Completed' ";
        $limit_option = " LIMIT $next_page_id, 30 ";
    }

    
    if (in_array("Admin", $role) || in_array("Accountant", $role) || in_array("Sub Admin", $role))
    {

        $sql = "SELECT *,DATE_FORMAT(a.agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(a.agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date,DATE_FORMAT(a.club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(a.deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(a.document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(a.security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(a.stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(a.transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(n.sub_team_name SEPARATOR ',') FROM sub_teams as n where FIND_IN_SET(n.sub_team_id , a.sub_teams)) as sub_teams,   (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' WHERE ".$agreement_stage_sql." ".$sql_cat." GROUP BY a.agreement_id ORDER by a.created_date DESC ".$limit_option." ";
        //-------------------------pks231023--------------------
        // $sql = "SELECT a.agreement_id,a.enquiry_id,a.property_id,a.buyer_brokerage,a.agreement_for,a.seller_brokerage,a.agreement_stage,a.total_brokerage,a.agreement_value,a.agreement_value_para,b.proptype,b.propsubtype,b.property_for,b.bedrooms,b.building_name,b.project_name,b.exp_price,b.exp_price_para,b.sale_area,b.pro_sale_para,b.carp_area,b.carp_area_para ,DATE_FORMAT(a.agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(a.agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date,DATE_FORMAT(a.club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(a.deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(a.document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(a.security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(a.stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(a.transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(n.sub_team_name SEPARATOR ',') FROM sub_teams as n where FIND_IN_SET(n.sub_team_id , a.sub_teams)) as sub_teams,   (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' WHERE ".$agreement_stage_sql." ".$sql_cat." ORDER by a.created_date DESC ".$limit_option." ";        // $sql = "SELECT a.agreement_id,a.enquiry_id,a.property_id,a.buyer_brokerage,a.agreement_for,a.seller_brokerage,a.agreement_stage,a.total_brokerage,a.agreement_value,a.agreement_value_para,b.proptype,b.propsubtype,b.property_for,b.bedrooms,b.building_name,b.project_name,b.exp_price,b.exp_price_para,b.sale_area,b.pro_sale_para,b.carp_area,b.carp_area_para ,DATE_FORMAT(a.agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(a.agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date,DATE_FORMAT(a.club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(a.deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(a.document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(a.security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(a.stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(a.transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(n.sub_team_name SEPARATOR ',') FROM sub_teams as n where FIND_IN_SET(n.sub_team_id , a.sub_teams)) as sub_teams,   (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' WHERE ".$agreement_stage_sql." ".$sql_cat." GROUP BY a.agreement_id ORDER by a.created_date DESC ".$limit_option." ";
        
        $countsql = "SELECT count(*) as agreement_count FROM agreement as a WHERE ".$agreement_stage_sql." ".$sql_cat." ";
    }
    else if (in_array("Branch Head", $role))
    {
        //-------------------------pks231023--------------------
        // $sql = "SELECT *,DATE_FORMAT(a.agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(a.agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(a.deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(a.document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(a.security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(a.shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(a.stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(n.sub_team_name SEPARATOR ',') FROM sub_teams as n where FIND_IN_SET(n.sub_team_id , a.sub_teams)) as sub_teams, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." ) ".$sql_cat." and ".$agreement_stage_sql." ORDER by a.created_date DESC  ".$limit_option." "; //
        $sql = "SELECT *,DATE_FORMAT(a.agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(a.agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(a.deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(a.document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(a.security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(a.shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(a.stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(n.sub_team_name SEPARATOR ',') FROM sub_teams as n where FIND_IN_SET(n.sub_team_id , a.sub_teams)) as sub_teams, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." ) ".$sql_cat." and ".$agreement_stage_sql." GROUP BY a.agreement_id ORDER by a.created_date DESC  ".$limit_option." "; //

        $countsql = "SELECT count(*) as agreement_count FROM agreement as a  WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." ) ".$sql_cat." and ".$agreement_stage_sql." ";
    }
    else
    {
        //-------------------------pks231023--------------------
        // $sql = "SELECT *,DATE_FORMAT(a.agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(a.agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(a.deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(a.document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(a.security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(a.shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(a.stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(a.transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(n.sub_team_name SEPARATOR ',') FROM sub_teams as n where FIND_IN_SET(n.sub_team_id , a.sub_teams)) as sub_teams, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)  ) ".$sql_cat." and ".$agreement_stage_sql." ORDER by a.created_date DESC  ".$limit_option." "; //".$team_query."
        $sql = "SELECT *,DATE_FORMAT(a.agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(a.agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(a.deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(a.document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(a.security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(a.shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(a.stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(a.transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(n.sub_team_name SEPARATOR ',') FROM sub_teams as n where FIND_IN_SET(n.sub_team_id , a.sub_teams)) as sub_teams, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)  ) ".$sql_cat." and ".$agreement_stage_sql." GROUP BY a.agreement_id ORDER by a.created_date DESC  ".$limit_option." "; //".$team_query."

        $countsql = "SELECT count(*) as agreement_count FROM agreement as a LEFT JOIN property as b on a.property_id = b.property_id WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)  )  ".$sql_cat." and ".$agreement_stage_sql." ";
    }


    // error_log($sql, 3, "logfile7.log");
    $agreements = $db->getAllRecords($sql);

    $agreement_count = 0;
    $agreement_countdata = $db->getAllRecords($countsql);
    if ($agreement_countdata)
    {
         $agreement_count = $agreement_countdata[0]['agreement_count'];

    }
    if ($agreement_count>0)
    {
        $agreements[0]['agreement_count']=$agreement_count;
    }
    //$agreements[0]['sql']=$sql;
    echo json_encode($agreements);
    /*$sql = "SELECT * from agreement LIMIT 0";
    $countsql = "SELECT count(*) as agreement_count FROM agreement";
    $sql_cat="";
    if ($cat != 'direct')
    {
        $sql_cat = " and b.proptype = '$cat' ";
    }
    if (in_array("Admin", $role) || in_array("Accountant", $role))
    {

        $sql = "SELECT *,DATE_FORMAT(agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(possession_date,'%d-%m-%Y') AS possession_date,DATE_FORMAT(club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' WHERE a.agreement_status ='Completed' GROUP BY a.agreement_id ORDER by a.created_date DESC LIMIT $next_page_id, 30";
        $countsql = "SELECT count(*) as agreement_count FROM agreement WHERE a.agreement_status ='Completed'";
    }
    else if (in_array("Branch Head", $role))
    {
        $sql = "SELECT *,DATE_FORMAT(agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." ) ".$sql_cat." and a.agreement_status ='Completed' GROUP BY a.agreement_id ORDER by a.created_date DESC LIMIT $next_page_id, 30"; //

        $countsql = "SELECT count(*) as agreement_count FROM agreement as a  WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." ) ".$sql_cat." and a.agreement_status ='Completed' ";
    }
    else
    {
        
        $sql = "SELECT *,DATE_FORMAT(agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(agreement_till_date,'%d-%m-%Y') AS agreement_till_date, DATE_FORMAT(possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, f.filenames, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)  ".$team_query." ) ".$sql_cat." and a.agreement_status ='Completed' GROUP BY a.agreement_id ORDER by a.created_date DESC LIMIT $next_page_id, 30"; //".$team_query."

        $countsql = "SELECT count(*) as agreement_count FROM agreement as a LEFT JOIN property as b on a.property_id = b.property_id WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)  ".$team_query.")  ".$sql_cat." and a.agreement_status ='Completed'";
    }


    $agreements_completed = $db->getAllRecords($sql);

    $agreement_completed_count = 0;
    $agreement_countdata = $db->getAllRecords($countsql);
    if ($agreement_countdata)
    {
         $agreement_completed_count = $agreement_countdata[0]['agreement_count'];

    }
    if ($agreement_completed_count>0)
    {
        $agreements_completed[0]['agreement_completed_count']=$agreement_completed_count;
    }
    $agreements_completed[0]['sql']=$sql;
    echo json_encode($agreements_completed);*/
});

$app->post('/search_agreement', function() use ($app) {
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

    $next_page_id = $r->searchdata->next_page_id;
    $listcategory = $r->searchdata->listcategory;
    
    $role = $session['role'];


    $searchsql = "SELECT *,DATE_FORMAT(agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(agreement_till_date,'%d-%m-%Y') AS agreement_till_date,DATE_FORMAT(club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id  where (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." ) ";

    
    $countsql = "SELECT count(*) as agreement_count FROM agreement ";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $searchsql = "SELECT *,DATE_FORMAT(agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(agreement_till_date,'%d-%m-%Y') AS agreement_till_date,DATE_FORMAT(club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' where (a.created_by > 0) ";

        $countsql = "SELECT count(*) as agreement_count FROM agreement as a LEFT JOIN property as b on a.property_id = b.property_id WHERE a.created_by > 0 ";
    }
    else
    {
        $searchsql = "SELECT *,DATE_FORMAT(agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(agreement_till_date,'%d-%m-%Y') AS agreement_till_date,DATE_FORMAT(club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(deal_date ,'%d-%m-%Y') AS deal_date,DATE_FORMAT(document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as dev_owner,c.mob_no as mob_no, CONCAT(d.name_title,' ',d.f_name,' ',d.l_name) as buyer, d.mob_no as buyer_mob_no,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date from agreement as a LEFT JOIN property as b on a.property_id = b.property_id LEFT JOIN contact as c on a.contact_id = c.contact_id LEFT JOIN contact as d on a.buyer_id = d.contact_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' where (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." ) ";

        $countsql = "SELECT count(*) as agreement_count FROM agreement as a LEFT JOIN property as b on a.property_id = b.property_id WHERE a.created_by > 0 and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ".$team_query." ) ";

    }

    $sql = " ";
    if (isset($r->searchdata->agreement_for))
    {
        $sql .= " and a.agreement_for = '".$r->searchdata->agreement_for."' ";
    }

    if (isset($r->searchdata->property_for))
    {
        $sql .= " and b.property_for = '".$r->searchdata->property_for."' ";
    }

    if (isset($r->searchdata->proptype))
    {
        $sql .= " and b.proptype = '".$r->searchdata->proptype."' ";
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
                $sql .= " a.broker1_id LIKE '".$value."' OR a.broker2_id LIKE '".$value."'  ";
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

    if (isset($r->searchdata->agreement_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->agreement_id;
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
                $sql .= " a.agreement_id LIKE '".$value."' ";
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
        $tcreated_date_from = substr($created_date_from,6,4)."-".substr($created_date_from,3,2)."-".substr($created_date_from,0,2)." 00:00:00";
        if (isset($r->searchdata->created_date_to))
        {
            $created_date_to = $r->searchdata->created_date_to;
            $tcreated_date_to = substr($created_date_to,6,4)."-".substr($created_date_to,3,2)."-".substr($created_date_to,0,2)." 23:59:59";
        }
        $sql .= " and a.created_date BETWEEN '".$tcreated_date_from."' AND '".$tcreated_date_to."' ";
    }

    if (isset($r->searchdata->deal_date_from))
    {
        $deal_date_from = $r->searchdata->deal_date_from;
        $tdeal_date_from = substr($deal_date_from,6,4)."-".substr($deal_date_from,3,2)."-".substr($deal_date_from,0,2)." 00:00:00";
        if (isset($r->searchdata->deal_date_to))
        {
            $deal_date_to = $r->searchdata->deal_date_to;
            $tdeal_date_to = substr($deal_date_to,6,4)."-".substr($deal_date_to,3,2)."-".substr($deal_date_to,0,2)." 23:59:59";
        }
        $sql .= " and a.deal_date BETWEEN '".$tdeal_date_from."' AND '".$tdeal_date_to."' ";
    }
    if ($listcategory=='Completed')
    {
        $sql .= " AND a.agreement_status ='Completed' ";
    }
    else
    {
        $sql .= " AND a.agreement_status !='Completed' ";
    }
    $searchsql .=  $sql .  " GROUP BY a.agreement_id ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";// CAST(a.property_id as UNSIGNED) DESC";

    $agreements = $db->getAllRecords($searchsql);

    $countsql = $countsql . $sql;
    $agreement_count = 0;
    $agreementcountdata = $db->getAllRecords($countsql);
    if ($agreementcountdata)
    {
         $agreement_count = $agreementcountdata[0]['agreement_count'];

    }
    //if ($agreement_count>0)
    //{
        $agreements[0]['agreement_count']=$agreement_count;
    //}
    //$agreements[0]['countsql']=$countsql;
    //$properties[0]['sql']=$sql;
    //echo $searchsql;
   
    echo json_encode($agreements);
});

$app->get('/getdatavalues_agreement/:field_name', function($field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM agreement  ORDER BY $field_name";
    //echo $sql;
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});

$app->get('/properties_dealclose', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,a.exp_price, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact, CONCAT(h.salu,' ',h.fname,' ',h.lname) as assign_to, h.mobile_no as usermobileno, (SELECT count(*) from property as t where a.project_id = t.project_id GROUP by t.project_id) as properties_count from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id WHERE a.deal_done = 'Yes' ORDER BY a.created_by DESC";
    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);
});

$app->get('/change_agreement_stage_only/:agreement_stage/:agreement_id', function($agreement_stage,$agreement_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE agreement set agreement_stage = '$agreement_stage' where agreement_id = $agreement_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode("done");
});
  


$app->get('/change_agreement_stage/:agreement_stage/:agreement_id', function($agreement_stage,$agreement_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    if (strtolower($agreement_stage)==strtolower('Brokerage Confirmation Letter Signing'))
    {
        $isagreementExists = $db->getOneRecord("select 1 from payments where agreement_id = $agreement_id");
        if(!$isagreementExists)
        {
            $sql = "SELECT * from agreement WHERE agreement_id = $agreement_id";
            $stmt = $db->getRows($sql);
            if($stmt->num_rows > 0)
            { 
                while($row = $stmt->fetch_assoc())
                {    
                    $buyer_id = $row['buyer_id'];
                    $due_date = "";
                    $buyer_brokerage = $row['buyer_brokerage'];
                    $service_tax = 0;
                    $service_tax_per = $row['service_tax_per'];
                    if ($service_tax_per>0)
                    {
                        $service_tax = round(($buyer_brokerage * $service_tax_per/100),2);
                    }
                    $cgst = 0;
                    $cgst_per = $row['cgst_per'];
                    if ($cgst_per>0)
                    {
                        $cgst = round(($buyer_brokerage * $cgst_per/100),2);
                    }
                    $sgst = 0;
                    $sgst_per = $row['sgst_per'];
                    if ($sgst_per>0)
                    {
                        $sgst = round(($buyer_brokerage * $sgst_per/100),2);
                    }
                    $tds = 0;
                    $tds_per = $row['tds_per'];
                    if ($tds_per>0)
                    {
                        $tds = round(($buyer_brokerage * $tds_per/100),2);
                    }
                    $total_brokerage = $buyer_brokerage + $service_tax + $cgst + $sgst - $tds;
                    $query = "INSERT INTO payments (agreement_id,sr_no,client_category, client_id, stage_name, due_per, due_date, remind_before, brokerage, service_tax_per,service_tax, cgst_per,cgst, sgst_per,sgst,tds_per,tds,total_brokerage)   VALUES('$agreement_id',1,'Buyer','$buyer_id', '$agreement_stage','','$due_date', '', '$buyer_brokerage' ,'$service_tax_per','$service_tax','$cgst_per','$cgst','$sgst_per', '$sgst', '$tds_per', '$tds', '$total_brokerage')";
                    $result = $db->insertByQuery($query);

                    $seller_id = $row['seller_id'];
                    $due_date = "";
                    $seller_brokerage = $row['seller_brokerage'];
                    $service_tax = 0;
                    $service_tax_per = $row['service_tax_per'];
                    if ($service_tax_per>0)
                    {
                        $service_tax = round(($seller_brokerage * $service_tax_per/100),2);
                    }
                    $cgst = 0;
                    $cgst_per = $row['cgst_per'];
                    if ($cgst_per>0)
                    {
                        $cgst = round(($seller_brokerage * $cgst_per/100),2);
                    }
                    $sgst = 0;
                    $sgst_per = $row['sgst_per'];
                    if ($sgst_per>0)
                    {
                        $sgst = round(($seller_brokerage * $sgst_per/100),2);
                    }
                    $tds = 0;
                    $tds_per = $row['tds_per'];
                    if ($tds_per>0)
                    {
                        $tds = round(($seller_brokerage * $tds_per/100),2);
                    }
                    $total_brokerage = $seller_brokerage + $service_tax + $cgst + $sgst - $tds;
                    $query = "INSERT INTO payments (agreement_id,sr_no,client_category, client_id, stage_name, due_per, due_date, remind_before, brokerage, service_tax_per,service_tax, cgst_per,cgst,sgst_per,sgst,tds_per,tds,total_brokerage)   VALUES('$agreement_id',2, 'Seller','$seller_id', '$agreement_stage','','$due_date', '', '$seller_brokerage' , '$service_tax_per','$service_tax','$cgst_per','$cgst','$sgst_per', '$sgst', '$tds_per', '$tds', '$total_brokerage')";
                    $result = $db->insertByQuery($query);

                }
            }
        }
    }
    $sql = "UPDATE agreement set agreement_stage = '$agreement_stage' where agreement_id = $agreement_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode("done");
});

$app->get('/agreement_stage_change/:agreement_for/:agreement_id', function($agreement_for,$agreement_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $agreement_type = "";
    $agreement_stage = "";
    $senddata =  array();
    $htmldata = array();
    if ($agreement_for=='Sale')
    {
        $agreement_type = "AGREEMENT_STAGE_SALE";
    }
    if ($agreement_for=='Lease')
    {
        $agreement_type = "AGREEMENT_STAGE_LEASE";
    }
    if ($agreement_for=='Sale Under Contructions')
    {
        $agreement_type = "AGREEMENT_STAGE_SALE_UC";
    }
    if ($agreement_for=='Sale Preleased')
    {
        $agreement_type = "AGREEMENT_STAGE_PRELEASE";
    }
    if ($agreement_id > 0)
    {
        $stagedata = $db->getAllRecords("SELECT agreement_stage FROM agreement WHERE agreement_id = $agreement_id " );
        if ($stagedata)
        {
             $agreement_stage = $stagedata[0]['agreement_stage'];

        }
    }
    $htmlstring = '';
    $htmlstring .='<ul>';

    $sql = "SELECT * from dropdowns WHERE type = '$agreement_type' ORDER BY CAST(sequence_number  as UNSIGNED) " ;
    
    $stmt = $db->getRows($sql);    
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $first = "Yes";
    $checked = "Yes";
    $next_stage="No";
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $value = $row['value'];
            $sequence_number = $row['sequence_number'];
            if ($agreement_id == 0)
            {
                $htmlstring .= '<li style="list-style-type:none;"><label style="font-weight: normal;font-size: 15px;"><input type="checkbox" class="check_element"  style="width:13px;height:13px;margin:5px;" name="agreement_stage_'.$sequence_number.'" id="agreement_stage_'.$sequence_number.'" ng-model="agreement.agreement_stage_'.$sequence_number.'"  value="'.$value.'"'; // ng-true-value="'.$value.'" ng-false-value=""
                if ($first!='Yes')
                {
                    $htmlstring .= ' disabled="disabled" ';
                }
                else{
                    $htmlstring .= ' ng-click="checkbox_clicked()" ';
                }
                $htmlstring .= '/>'.$value.'</label></li>';
                $first='No';
            }
            if ($agreement_id>0)
            {
                $htmlstring .= '<li style="list-style-type:none;"><label style="font-weight: normal;font-size: 15px;"><input type="checkbox" class="check_element"  style="width:13px;height:13px;margin:5px;" name="agreement_stage_'.$sequence_number.'" id="agreement_stage_'.$sequence_number.'" ng-model="agreement.agreement_stage_'.$sequence_number.'" value="'.$value.'"'; // ng-true-value="'.$value.'" ng-false-value=""
                if ($checked=='Yes')
                {
                    $htmlstring .= ' ng-checked="true" checked="checked" disabled="disabled" ' ;
                }
                else
                {
                    if ($next_stage=='Yes')
                    {
                        $next_stage='No';
                        $htmlstring .= ' ng-click="checkbox_clicked()" ';
                    }
                    else{
                        $htmlstring .= ' disabled="disabled" ' ;
                    }
                    
                }
                if ($value==$agreement_stage)
                {
                    $checked='No';
                    $next_stage='Yes';
                }
                $htmlstring .= '/>'.$value.'</label></li>';
            }
            
        }
    }
    $htmlstring .='</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);

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
    verifyRequiredParams(array('property_id'),$r->agreement);
    $db = new DbHandler();
    if (isset($r->agreement->agreement_from_date))
    {
        $agreement_from_date = $r->agreement->agreement_from_date;
        $tagreement_from_date = substr($agreement_from_date,6,4)."-".substr($agreement_from_date,3,2)."-".substr($agreement_from_date,0,2);
        $r->agreement->agreement_from_date = $tagreement_from_date;
    }

    if (isset($r->agreement->agreement_till_date))
    {
        $agreement_till_date = $r->agreement->agreement_till_date;
        $tagreement_till_date = substr($agreement_till_date,6,4)."-".substr($agreement_till_date,3,2)."-".substr($agreement_till_date,0,2);
        $r->agreement->agreement_till_date = $tagreement_till_date;
    }

    if (isset($r->agreement->club_house_date))
    {
        $club_house_date = $r->agreement->club_house_date;
        $tclub_house_date = substr($club_house_date,6,4)."-".substr($club_house_date,3,2)."-".substr($club_house_date,0,2);
        $r->agreement->club_house_date = $tclub_house_date;
    }

    if (isset($r->agreement->deal_date))
    {
        $deal_date = $r->agreement->deal_date;
        $tdeal_date = substr($deal_date,6,4)."-".substr($deal_date,3,2)."-".substr($deal_date,0,2);
        $r->agreement->deal_date = $tdeal_date;
    }


    if (isset($r->agreement->opening_date))
    {
        $opening_date = $r->agreement->opening_date;
        $topening_date = substr($opening_date,6,4)."-".substr($opening_date,3,2)."-".substr($opening_date,0,2);
        $r->agreement->opening_date = $topening_date;
    }

    if (isset($r->agreement->possession_date))
    {
        $possession_date = $r->agreement->possession_date;
        $tpossession_date = substr($possession_date,6,4)."-".substr($possession_date,3,2)."-".substr($possession_date,0,2);
        $r->agreement->possession_date = $tpossession_date;
    }


    if (isset($r->agreement->document_charges_date))
    {
        $document_charges_date = $r->agreement->document_charges_date;
        $tdocument_charges_date = substr($document_charges_date,6,4)."-".substr($document_charges_date,3,2)."-".substr($document_charges_date,0,2);
        $r->agreement->document_charges_date = $tdocument_charges_date;
    }

    if (isset($r->agreement->security_deposit_date))
    {
        $security_deposit_date = $r->agreement->security_deposit_date;
        $tsecurity_deposit_date = substr($security_deposit_date,6,4)."-".substr($security_deposit_date,3,2)."-".substr($security_deposit_date,0,2);
        $r->agreement->security_deposit_date = $tsecurity_deposit_date;
    }

    if (isset($r->agreement->shifting_date))
    {
        $shifting_date = $r->agreement->shifting_date;
        $tshifting_date = substr($shifting_date,6,4)."-".substr($shifting_date,3,2)."-".substr($shifting_date,0,2);
        $r->agreement->shifting_date = $tshifting_date;
    }

    if (isset($r->agreement->stamp_duty_date))
    {
        $stamp_duty_date = $r->agreement->stamp_duty_date;
        $tstamp_duty_date = substr($stamp_duty_date,6,4)."-".substr($stamp_duty_date,3,2)."-".substr($stamp_duty_date,0,2);
        $r->agreement->stamp_duty_date = $tstamp_duty_date;
    }

    if (isset($r->agreement->transfer_charges_date))
    {
        $transfer_charges_date = $r->agreement->transfer_charges_date;
        $ttransfer_charges_date = substr($transfer_charges_date,6,4)."-".substr($transfer_charges_date,3,2)."-".substr($transfer_charges_date,0,2);
        $r->agreement->transfer_charges_date = $ttransfer_charges_date;
    }
    if (isset($r->agreement->rent_due_date))
    {
        $rent_due_date = $r->agreement->rent_due_date;
        $trent_due_date = substr($rent_due_date,6,4)."-".substr($rent_due_date,3,2)."-".substr($rent_due_date,0,2);
        $r->agreement->rent_due_date = $trent_due_date;
    }

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->agreement->created_by = $created_by;
    $r->agreement->created_date = $created_date;
    $r->agreement->modified_by = $created_by;
    $r->agreement->modified_date = $created_date;

    $tabble_name = "agreement";

    $column_names = array('advance_maintenance','agreement_for','agreement_from','agreement_from_date','agreement_till_date','agreement_value','agreement_stage','agreement_status','assign_to','basic_cost','buyer_brokerage','buyer_id', 'broker1_id', 'broker2_id', 'cgst','club_house_charges','club_house_date','contact_id','corpus_fund','deal_date', 'opening_date','possession_date', 'development_charges','document_charges','document_charges_date','enquiry_id','furniture','gross_brokerage','inname','lease_period','other_expenses','our_brokerage','parking_charges','property_id','registration_charges','rent','rent_due_date','security_deposit','security_deposit_date','seller_brokerage','send_rent_sms','service_tax','sgst','shifting_date','stamp_duty','stamp_duty_date','tds','teams','sub_teams','total_brokerage','transfer_charges','transfer_charges_date','transfer_term','transfer_type','agreement_value_para','furniture_para','basic_cost_para','rent_para', 'buyer_brokerage_per', 'seller_brokerage_per','our_brokerage_per','service_tax_per','cgst_per','sgst_per','tds_per', 'created_by','created_date','modified_by','modified_date');
    
    $multiple=array("assign_to","teams","sub_teams");
    $result = $db->insertIntoTable($r->agreement, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $agreement_id = $result;
        foreach ( $r->agreement->items as $key=>$val ) 
        {
            $r->agreement->items[$key]->agreement_id = $agreement_id;
            $r->agreement->items[$key]->amount_paid = 'No';
            $tabble_name = "agreement_details";
            $column_names = array('agreement_id','contribution_by', 'contribution_to', 'contribution_per', 'contribution_amount','amount_paid');
            $multiple=array("");
            $result = $db->insertIntoTable($r->agreement->items[$key], $column_names, $tabble_name, $multiple);
        }  
        /*$buyer_id = $r->agreement->buyer_id;
        $due_date = "";
        $buyer_brokerage = $r->agreement->buyer_brokerage;
        $service_tax = 0;
        if (isset($r->agreement->service_tax_per))
        {
            if ($r->agreement->service_tax_per>0)
            {
                $service_tax = round(($buyer_brokerage * $r->agreement->service_tax_per/100),2);
            }
        }
        $cgst = 0;
        if (isset($r->agreement->cgst_per))
        {
            if ($r->agreement->cgst_per>0)
            {
                $cgst = round(($buyer_brokerage * $r->agreement->cgstper/100),2);
            }
        }
        $sgst = 0;
        if (isset($r->agreement->sgst_per))
        {
            if ($r->agreement->sgst_per>0)
            {
                $sgst = round(($buyer_brokerage * $r->agreement->sgst_per/100),2);
            }
        }
        $tds = 0;
        if (isset($r->agreement->tds_per))
        {
            if ($r->agreement->tds_per>0)
            {
                $tds = round(($buyer_brokerage * $r->agreement->tds_per/100),2);
            }
        }
        $total_brokerage = $buyer_brokerage + $service_tax + $cgst + $sgst - $tds;
        $query = "INSERT INTO payments (agreement_id,client_category, client_id, stage_name, due_per, due_date, remind_before, brokerage, service_tax, cgst,sgst,tds,total_brokerage)   VALUES('$agreement_id','Buyer','$buyer_id', '','','$due_date', '', '$buyer_brokerage' ,'$service_tax','$cgst', '$sgst','$tds', '$total_brokerage'  )";
        $result = $db->insertByQuery($query);

        $seller_id = $r->agreement->contact_id;
        $due_date = "";
        $seller_brokerage = $r->agreement->seller_brokerage;
        $service_tax = 0;
        if (isset($r->agreement->service_tax_per))
        {
            if ($r->agreement->service_tax_per>0)
            {
                $service_tax = round(($seller_brokerage * $r->agreement->service_tax_per/100),2);
            }
        }
        $cgst = 0;
        if (isset($r->agreement->cgst_per))
        {
            if ($r->agreement->cgst_per>0)
            {
                $cgst = round(($seller_brokerage * $r->agreement->cgstper/100),2);
            }
        }
        $sgst = 0;
        if (isset($r->agreement->sgst_per))
        {
            if ($r->agreement->sgst_per>0)
            {
                $sgst = round(($seller_brokerage * $r->agreement->sgst_per/100),2);
            }
        }
        $tds = 0;
        if (isset($r->agreement->tds_per))
        {
            if ($r->agreement->tds_per>0)
            {
                $tds = round(($seller_brokerage * $r->agreement->tds_per/100),2);
            }
        }
        $total_brokerage = $seller_brokerage + $service_tax + $cgst + $sgst - $tds;

        $query = "INSERT INTO payments (agreement_id,client_category, client_id, stage_name, due_per, due_date, remind_before, brokerage, service_tax, cgst, sgst, tds,total_brokerage)  VALUES('$agreement_id','Seller','$seller_id', '','','$due_date', '', '$seller_brokerage' ,'$service_tax','$cgst', '$sgst', '$tds', '$total_brokerage' )";
        $result = $db->insertByQuery($query);*/

        $response["status"] = "success";
        $response["message"] = "Agreement created successfully";
        $response["agreement_id"] = $agreement_id;
	    $_SESSION['tmpagreement_id'] = $agreement_id;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create Agreement. Please try again";
        echoResponse(201, $response);
    }            
    
});


$app->post('/agreement_uploads', function() use ($app) {
    session_start();
    $agreement_id = $_SESSION['tmpagreement_id'];
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
        $file_names = "a_".$agreement_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "agreement". $ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "agreement". $ds. "a_".$agreement_id."_".time().".".$ext;
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

            $query = "INSERT INTO attachments (category, category_id, filenames, share_on_web, isdefault, description, file_category, created_by, created_date)   VALUES('agreement', '$agreement_id','$file_names','$share_on_web','$isdefault','$description','$file_category','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->post('/agreement_uploads_videos', function() use ($app) {
    session_start();
    $agreement_id = $_SESSION['tmpagreement_id'];
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
        $file_names = "avideos_".$property_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "agreement". $ds. 'videos' .$ds. $file_names;
        
        //$target = $_SERVER['DOCUMENT_ROOT']. $ds."uploads" . $ds . "project". $ds. "p_".$project_id."_".time().".".$ext;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;

            $query = "INSERT INTO attachments (category, category_id, filenames,  created_by, created_date)   VALUES('agreement_videos', '$agreement_id','$file_names','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->get('/agreement_edit_ctrl/:agreement_id', function($agreement_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $_SESSION['tmpagreement_id']=$agreement_id;
    $sql = "SELECT *,a.agreement_id,DATE_FORMAT(a.agreement_from_date,'%d-%m-%Y') AS agreement_from_date, DATE_FORMAT(a.rent_due_date,'%d-%m-%Y') AS rent_due_date, DATE_FORMAT(a.agreement_till_date,'%d-%m-%Y') AS agreement_till_date,DATE_FORMAT(a.club_house_date,'%d-%m-%Y') AS club_house_date,DATE_FORMAT(a.deal_date ,'%d-%m-%Y') AS deal_date, a.sub_teams, DATE_FORMAT(a.opening_date ,'%d-%m-%Y') AS opening_date, DATE_FORMAT(a.possession_date ,'%d-%m-%Y') AS possession_date,  DATE_FORMAT(a.document_charges_date,'%d-%m-%Y') AS document_charges_date,DATE_FORMAT(a.security_deposit_date,'%d-%m-%Y') AS security_deposit_date,DATE_FORMAT(a.shifting_date,'%d-%m-%Y') AS shifting_date,DATE_FORMAT(a.stamp_duty_date,'%d-%m-%Y') AS stamp_duty_date,DATE_FORMAT(a.transfer_charges_date,'%d-%m-%Y') AS transfer_charges_date,a.furniture from agreement as a LEFT JOIN agreement_details as b ON a.agreement_id = b.agreement_id LEFT JOIN property as c ON a.property_id = c.property_id WHERE a.agreement_id=".$agreement_id." GROUP BY a.agreement_id";
    $agreements = $db->getAllRecords($sql);
    echo json_encode($agreements);
});

$app->get('/agreement_images/:agreement_id', function($agreement_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'agreement' and category_id = $agreement_id";
    $agreement_images = $db->getAllRecords($sql);
    echo json_encode($agreement_images);
});

$app->get('/agreement_image_update/:attachment_id/:field_name/:value', function($attachment_id,$field_name,$value) use ($app) {
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

$app->get('/agreement_videos/:agreement_id', function($agreement_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'agreement_videos' and category_id = $agreement_id";
    $agreement_videos = $db->getAllRecords($sql);
    echo json_encode($agreement_videos);
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
    verifyRequiredParams(array('agreement_id'),$r->agreement);
    $db = new DbHandler();
    $agreement_id  = $r->agreement->agreement_id;
    if (isset($r->agreement->agreement_from_date))
    {
        $agreement_from_date = $r->agreement->agreement_from_date;
        $tagreement_from_date = substr($agreement_from_date,6,4)."-".substr($agreement_from_date,3,2)."-".substr($agreement_from_date,0,2);
        $r->agreement->agreement_from_date = $tagreement_from_date;
    }

    if (isset($r->agreement->agreement_till_date))
    {
        $agreement_till_date = $r->agreement->agreement_till_date;
        $tagreement_till_date = substr($agreement_till_date,6,4)."-".substr($agreement_till_date,3,2)."-".substr($agreement_till_date,0,2);
        $r->agreement->agreement_till_date = $tagreement_till_date;
    }

    if (isset($r->agreement->club_house_date))
    {
        $club_house_date = $r->agreement->club_house_date;
        $tclub_house_date = substr($club_house_date,6,4)."-".substr($club_house_date,3,2)."-".substr($club_house_date,0,2);
        $r->agreement->club_house_date = $tclub_house_date;
    }

    if (isset($r->agreement->deal_date))
    {
        $deal_date = $r->agreement->deal_date;
        $tdeal_date = substr($deal_date,6,4)."-".substr($deal_date,3,2)."-".substr($deal_date,0,2);
        $r->agreement->deal_date = $tdeal_date;
    }

    if (isset($r->agreement->opening_date))
    {
        $opening_date = $r->agreement->opening_date;
        $topening_date = substr($opening_date,6,4)."-".substr($opening_date,3,2)."-".substr($opening_date,0,2);
        $r->agreement->opening_date = $topening_date;
    }

    if (isset($r->agreement->possession_date))
    {
        $possession_date = $r->agreement->possession_date;
        $tpossession_date = substr($possession_date,6,4)."-".substr($possession_date,3,2)."-".substr($possession_date,0,2);
        $r->agreement->possession_date = $tpossession_date;
    }

    if (isset($r->agreement->document_charges_date))
    {
        $document_charges_date = $r->agreement->document_charges_date;
        $tdocument_charges_date = substr($document_charges_date,6,4)."-".substr($document_charges_date,3,2)."-".substr($document_charges_date,0,2);
        $r->agreement->document_charges_date = $tdocument_charges_date;
    }

    if (isset($r->agreement->security_deposit_date))
    {
        $security_deposit_date = $r->agreement->security_deposit_date;
        $tsecurity_deposit_date = substr($security_deposit_date,6,4)."-".substr($security_deposit_date,3,2)."-".substr($security_deposit_date,0,2);
        $r->agreement->security_deposit_date = $tsecurity_deposit_date;
    }

    if (isset($r->agreement->shifting_date))
    {
        $shifting_date = $r->agreement->shifting_date;
        $tshifting_date = substr($shifting_date,6,4)."-".substr($shifting_date,3,2)."-".substr($shifting_date,0,2);
        $r->agreement->shifting_date = $tshifting_date;
    }

    if (isset($r->agreement->stamp_duty_date))
    {
        $stamp_duty_date = $r->agreement->stamp_duty_date;
        $tstamp_duty_date = substr($stamp_duty_date,6,4)."-".substr($stamp_duty_date,3,2)."-".substr($stamp_duty_date,0,2);
        $r->agreement->stamp_duty_date = $tstamp_duty_date;
    }

    if (isset($r->agreement->transfer_charges_date))
    {
        $transfer_charges_date = $r->agreement->transfer_charges_date;
        $ttransfer_charges_date = substr($transfer_charges_date,6,4)."-".substr($transfer_charges_date,3,2)."-".substr($transfer_charges_date,0,2);
        $r->agreement->transfer_charges_date = $ttransfer_charges_date;
    }
    if (isset($r->agreement->rent_due_date))
    {
        $rent_due_date = $r->agreement->rent_due_date;
        $trent_due_date = substr($rent_due_date,6,4)."-".substr($rent_due_date,3,2)."-".substr($rent_due_date,0,2);
        $r->agreement->rent_due_date = $trent_due_date;
    }
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->agreement->modified_by = $modified_by;
    $r->agreement->modified_date = $modified_date;
    $r->agreement->contribution_date = $contribution_date;
    $agreement_stage = $r->agreement->agreement_stage;
    $isagreementExists = $db->getOneRecord("select 1 from agreement where agreement_id = $agreement_id");
    if($isagreementExists){
        $tabble_name = "agreement";
        
        $column_names = array('advance_maintenance','agreement_for','agreement_from','agreement_from_date','agreement_till_date','agreement_value','agreement_status','agreement_stage','assign_to','basic_cost','buyer_brokerage','buyer_id', 'broker1_id', 'broker2_id', 'cgst','club_house_charges','club_house_date','contact_id','corpus_fund','deal_date', 'opening_date', 'possession_date', 'development_charges','document_charges','document_charges_date','enquiry_id','furniture','gross_brokerage','inname','lease_period','other_expenses','our_brokerage','parking_charges','property_id','registration_charges','rent','rent_due_date','security_deposit','security_deposit_date','seller_brokerage','send_rent_sms','service_tax','sgst','shifting_date','stamp_duty','stamp_duty_date','tds','teams','sub_teams','total_brokerage','transfer_charges','transfer_charges_date','transfer_term','transfer_type','agreement_value_para','furniture_para','basic_cost_para','rent_para', 'buyer_brokerage_per', 'seller_brokerage_per','our_brokerage_per','service_tax_per','cgst_per','sgst_per','tds_per','eligible_for_incentive','modified_by','modified_date');

        $multiple=array("assign_to","teams","sub_teams");
        $condition = "agreement_id='$agreement_id'";
        $history = $db->historydata( $r->agreement, $column_names, $tabble_name,$condition,$agreement_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->agreement, $column_names, $tabble_name,$condition, $multiple);
        if ($result != NULL) 
        {
            
            //$tabble_name = "agreement_details";
            //$column_names = array('agreement_id');
            //$result1 = $db->deleteIntoTable($r->agreement, $column_names, $tabble_name,$condition);

            //$tabble_name = "payments";
            //$column_names = array('agreement_id');
            //$result1 = $db->deleteIntoTable($r->agreement, $column_names, $tabble_name,$condition);

            foreach ( $r->agreement->items as $key=>$val ) 
            {
                $agreement_details_id = $r->agreement->items[$key]->agreement_details_id;
                if ($agreement_details_id=="undefined" || $agreement_details_id == "")
                {
                    $agreement_details_id = 0;
                }
                $isagreementExists = $db->getOneRecord("select 1 from agreement_details where agreement_details_id = $agreement_details_id");
                if(!$isagreementExists)
                {
                    
                    $r->agreement->items[$key]->agreement_id = $agreement_id;
                    $r->agreement->items[$key]->amount_paid = 'No';
                    $tabble_name = "agreement_details";
                    $column_names = array('agreement_id','contribution_by', 'contribution_to', 'contribution_per', 'contribution_amount','amount_paid');
                    $multiple=array("");
                    $result = $db->insertIntoTable($r->agreement->items[$key], $column_names, $tabble_name, $multiple);
                    /*if ($agreement_stage=='Brokerage Confirmation Letter Signing')
                    {
                        $tabble_name = "contributions";
                        $column_names = array('agreement_id','contribution_by', 'contribution_date' ,'contribution_to', 'contribution_per', 'contribution_amount');
                        $multiple=array("");
                        $result = $db->insertIntoTable($r->agreement->items[$key], $column_names, $tabble_name, $multiple);
                    }*/

                }
                else
                {

                    $tabble_name = "agreement_details";
                    $column_names = array('agreement_id','contribution_by', 'contribution_to', 'contribution_per', 'contribution_amount');
                    $multiple=array("");
                    $condition = "agreement_details_id='$agreement_details_id'";
                    $history = $db->historydata( $r->agreement->items[$key], $column_names, $tabble_name,$condition,$agreement_id,$multiple, $modified_by, $modified_date);
                    $result = $db->NupdateIntoTable($r->agreement->items[$key], $column_names, $tabble_name,$condition, $multiple);

                }

                
            }  
            $isagreementExists = $db->getOneRecord("select 1 from payments where agreement_id = $agreement_id");
            if(!$isagreementExists)
            {
                $buyer_id = $r->agreement->buyer_id;
                $due_date = "";
                $buyer_brokerage = $r->agreement->buyer_brokerage;
                $service_tax = 0;
                $service_tax_per = $r->agreement->service_tax_per;
                if ($service_tax_per>0)
                {
                    $service_tax = round(($buyer_brokerage * $service_tax_per/100),2);
                }
                $cgst = 0;
                $cgst_per = $r->agreement->cgst_per;
                if ($cgst_per>0)
                {
                    $cgst = round(($buyer_brokerage * $cgst_per/100),2);
                }
                $sgst = 0;
                $sgst_per = $r->agreement->sgst_per;
                if ($sgst_per>0)
                {
                    $sgst = round(($buyer_brokerage * $sgst_per/100),2);
                }
                $tds = 0;
                $tds_per = $r->agreement->tds_per;
                if ($tds_per>0)
                {
                    $tds = round(($buyer_brokerage * $tds_per/100),2);
                }
                $total_brokerage = $buyer_brokerage + $service_tax + $cgst + $sgst - $tds;
                $query = "INSERT INTO payments (agreement_id,sr_no,client_category, client_id, stage_name, due_per, due_date, remind_before, brokerage, service_tax_per,service_tax, cgst_per,cgst, sgst_per,sgst,tds_per,tds,total_brokerage,collection_status)   VALUES('$agreement_id',1,'Buyer','$buyer_id', '$agreement_stage','','$due_date', '', '$buyer_brokerage' ,'$service_tax_per','$service_tax','$cgst_per','$cgst','$sgst_per', '$sgst', '$tds_per', '$tds', '$total_brokerage','Pending')";
                $result = $db->insertByQuery($query);

                $seller_id = $r->agreement->contact_id;
                $due_date = "";
                $seller_brokerage = $r->agreement->seller_brokerage;
                $service_tax = 0;
                $service_tax_per = $r->agreement->service_tax_per;
                if ($service_tax_per>0)
                {
                    $service_tax = round(($seller_brokerage * $service_tax_per/100),2);
                }
                $cgst = 0;
                $cgst_per = $r->agreement->cgst_per;
                if ($cgst_per>0)
                {
                    $cgst = round(($seller_brokerage * $cgst_per/100),2);
                }
                $sgst = 0;
                $sgst_per = $r->agreement->sgst_per;
                if ($sgst_per>0)
                {
                    $sgst = round(($seller_brokerage * $sgst_per/100),2);
                }
                $tds = 0;
                $tds_per = $r->agreement->tds_per;
                if ($tds_per>0)
                {
                    $tds = round(($seller_brokerage * $tds_per/100),2);
                }
                $total_brokerage = $seller_brokerage + $service_tax + $cgst + $sgst - $tds;
                $query = "INSERT INTO payments (agreement_id,sr_no,client_category, client_id, stage_name, due_per, due_date, remind_before, brokerage, service_tax_per,service_tax, cgst_per,cgst,sgst_per,sgst,tds_per,tds,total_brokerage,collection_status)   VALUES('$agreement_id',2, 'Seller','$seller_id', '$agreement_stage','','$due_date', '', '$seller_brokerage' , '$service_tax_per','$service_tax','$cgst_per','$cgst','$sgst_per', '$sgst', '$tds_per', '$tds', '$total_brokerage','Pending')";
                $result = $db->insertByQuery($query);
            }
            else
            {
                $buyer_id = $r->agreement->buyer_id;
                $buyer_brokerage = $r->agreement->buyer_brokerage;
                $service_tax = 0;
                $service_tax_per = $r->agreement->service_tax_per;
                if ($service_tax_per>0)
                {
                    $service_tax = round(($buyer_brokerage * $service_tax_per/100),2);
                }
                $cgst = 0;
                $cgst_per = $r->agreement->cgst_per;
                if ($cgst_per>0)
                {
                    $cgst = round(($buyer_brokerage * $cgst_per/100),2);
                }
                $sgst = 0;
                $sgst_per = $r->agreement->sgst_per;
                if ($sgst_per>0)
                {
                    $sgst = round(($buyer_brokerage * $sgst_per/100),2);
                }
                $tds = 0;
                $tds_per = $r->agreement->tds_per;
                if ($tds_per>0)
                {
                    $tds = round(($buyer_brokerage * $tds_per/100),2);
                }
                $total_brokerage = $buyer_brokerage + $service_tax + $cgst + $sgst - $tds;

                $query = "UPDATE payments set client_id = '$buyer_id', stage_name = '$agreement_stage', due_per = '' , remind_before = '' , brokerage = '$buyer_brokerage', service_tax_per = '$service_tax_per' ,service_tax = '$service_tax' , cgst_per = '$cgst_per' ,cgst = '$cgst', sgst_per = '$sgst_per' ,sgst = '$sgst',tds_per = '$tds_per' ,tds = '$tds' ,total_brokerage = '$total_brokerage' ,collection_status = 'Pending' WHERE agreement_id = $agreement_id and sr_no = 1";
                $result = $db->updateByQuery($query);

                $seller_id = $r->agreement->contact_id;
                $seller_brokerage = $r->agreement->seller_brokerage;
                $service_tax = 0;
                $service_tax_per = $r->agreement->service_tax_per;
                if ($service_tax_per>0)
                {
                    $service_tax = round(($seller_brokerage * $service_tax_per/100),2);
                }
                $cgst = 0;
                $cgst_per = $r->agreement->cgst_per;
                if ($cgst_per>0)
                {
                    $cgst = round(($seller_brokerage * $cgst_per/100),2);
                }
                $sgst = 0;
                $sgst_per = $r->agreement->sgst_per;
                if ($sgst_per>0)
                {
                    $sgst = round(($seller_brokerage * $sgst_per/100),2);
                }
                $tds = 0;
                $tds_per = $r->agreement->tds_per;
                if ($tds_per>0)
                {
                    $tds = round(($seller_brokerage * $tds_per/100),2);
                }
                $total_brokerage = $seller_brokerage + $service_tax + $cgst + $sgst - $tds;

                $query = "UPDATE payments set client_id = '$seller_id', stage_name = '$agreement_stage', due_per = '' ,  remind_before = '' , brokerage = '$seller_brokerage', service_tax_per = '$service_tax_per' ,service_tax = '$service_tax' , cgst_per = '$cgst_per' ,cgst = '$cgst', sgst_per = '$sgst_per' ,sgst = '$sgst',tds_per = '$tds_per' ,tds = '$tds' ,total_brokerage = '$total_brokerage' ,collection_status = 'Pending' WHERE agreement_id = $agreement_id and sr_no = 2";
                $result = $db->updateByQuery($query);
            }
            $response["status"] = "success";
            $response["message"] = "Agreement Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Agreement. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Agreement with the provided Agreement does not exists!";
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
    verifyRequiredParams(array('agreement_id'),$r->agreement);
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
            
            $tabble_name = "agreement_details";
            $column_names = array('agreement_id');
            $result1 = $db->deleteIntoTable($r->agreement, $column_names, $tabble_name,$condition);

            $tabble_name = "payments";
            $column_names = array('agreement_id');
            $result1 = $db->deleteIntoTable($r->agreement, $column_names, $tabble_name,$condition);

            $response["status"] = "success";
            $response["message"] = "Agreement Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Agreement. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Agreement with the provided agreement does not exists!";
        echoResponse(201, $response);
    }
});

// COLLECTIONS


$app->get('/collections_ctrl/:next_page_id', function($next_page_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest") 
    {
        return;
    }
    $user_id = $session['user_id'];
    $role = $session['role'];

    $team_query = " ";  
    $tteams = $session['teams'];

    $first_record = "Yes";

    foreach($tteams as $value)
    {
        if ($first_record == 'Yes')
        {
            $team_query .= " AND (";
            $first_record = "No";
            $team_query .= " FIND_IN_SET(".$value." , b.teams) ";
            
        }
        else
        {
            $team_query .= " OR FIND_IN_SET(".$value." , b.teams) ";
        }
    }
    $team_query .= ")  "; 

    
    
    $sql = "SELECT * from payments LIMIT 0";
    $countsql = "SELECT count(*) as collection_count FROM payments";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT *, DATE_FORMAT(due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id  WHERE a.total_brokerage > 0 and a.collection_status != 'Received' ORDER BY payments_id LIMIT $next_page_id,30";

        $countsql = "SELECT count(*) as collection_count FROM payments as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id WHERE a.total_brokerage > 0 and a.collection_status != 'Received' ";

    }
    else if (in_array("Branch Head", $role))
    {
        $sql = "SELECT *, DATE_FORMAT(due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id WHERE a.total_brokerage > 0 and a.collection_status != 'Received' ".$team_query."  ORDER BY payments_id LIMIT $next_page_id,30";

        $countsql = "SELECT count(*) as collection_count FROM payments as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id  WHERE a.total_brokerage > 0 and a.collection_status != 'Received' ".$team_query." ";
    }
    else if (in_array("Accountant", $role) ) 
    {
        $sql = "SELECT *, DATE_FORMAT(due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id WHERE a.total_brokerage > 0 and a.collection_status != 'Received' ORDER BY payments_id LIMIT $next_page_id,30";

        $countsql = "SELECT count(*) as collection_count FROM payments as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id  WHERE a.total_brokerage > 0 and a.collection_status != 'Received'";
    }
    else
    {

        $sql = "SELECT *, DATE_FORMAT(due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,b.assign_to)  ) and a.total_brokerage > 0 and a.collection_status != 'Received' ORDER BY payments_id LIMIT $next_page_id, 30";

        $countsql = "SELECT count(*) as collection_count FROM payments as a  LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id  WHERE (b.created_by = $user_id or FIND_IN_SET ($user_id ,b.assign_to) ) and a.total_brokerage > 0 and a.collection_status != 'Received'";

    }

    

    $collections = $db->getAllRecords($sql);

    $collection_count = 0;
    $collection_countdata = $db->getAllRecords($countsql);
    if ($collection_countdata)
    {
         $collection_count = $collection_countdata[0]['collection_count'];
    }
    if ($collection_count>0)
    {
        $collections[0]['collection_count']=$collection_count;
    }
    $collections[0]['sql']=$countsql;
    echo json_encode($collections);
});

$app->get('/collections_ctrl_received/:next_page_id', function($next_page_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest") 
    {
        return;
    }
    $user_id = $session['user_id'];
    $role = $session['role'];

    $team_query = " ";  
    $tteams = $session['teams'];

    $first_record = "Yes";

    foreach($tteams as $value)
    {
        if ($first_record == 'Yes')
        {
            $team_query .= " AND (";
            $first_record = "No";
            $team_query .= " FIND_IN_SET(".$value." , b.teams) ";
            
        }
        else
        {
            $team_query .= " OR FIND_IN_SET(".$value." , b.teams) ";
        }
    }
    $team_query .= ") "; 

    
    
    $sql = "SELECT * from payments LIMIT 0";
    $countsql = "SELECT count(*) as collection_count FROM payments";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT *, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id  WHERE a.total_brokerage > 0 and a.collection_status = 'Received' ORDER BY payments_id LIMIT $next_page_id,30";

        $countsql = "SELECT count(*) as collection_count FROM payments as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id WHERE a.total_brokerage > 0 and a.collection_status = 'Received'";

    }
    else if (in_array("Branch Head", $role))
    {
        $sql = "SELECT *, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id WHERE a.total_brokerage > 0 and a.collection_status = 'Received' ".$team_query."  ORDER BY payments_id LIMIT $next_page_id,30";

        $countsql = "SELECT count(*) as collection_count FROM payments as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id WHERE a.total_brokerage > 0 and a.collection_status = 'Received' ".$team_query." ";
    }
    else if (in_array("Accountant", $role) ) 
    {
        $sql = "SELECT *, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id WHERE a.total_brokerage > 0 and a.collection_status = 'Received' ORDER BY payments_id LIMIT $next_page_id,30";

        $countsql = "SELECT count(*) as collection_count FROM payments as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id WHERE a.total_brokerage > 0 and a.collection_status = 'Received'";
    }
    else
    {

        $sql = "SELECT *, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,b.assign_to)  ) and a.total_brokerage > 0 and a.collection_status = 'Received' ORDER BY payments_id LIMIT $next_page_id, 30";

        $countsql = "SELECT count(*) as collection_count FROM payments as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id  WHERE (b.created_by = $user_id or FIND_IN_SET ($user_id ,b.assign_to) ) and a.total_brokerage > 0 and a.collection_status = 'Received'";

    }

    

    $collections = $db->getAllRecords($sql);

    $collection_count = 0;
    $collection_countdata = $db->getAllRecords($countsql);
    if ($collection_countdata)
    {
         $collection_count = $collection_countdata[0]['collection_count'];
    }
    if ($collection_count>0)
    {
        $collections[0]['collection_count']=$collection_count;
    }
    $collections[0]['sql']=$countsql;
    echo json_encode($collections);
});

$app->get('/get_collections/:agreement_id', function($agreement_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $teams = $session['teams'];
    $team_query = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $role = $session['role'];

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT *, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id WHERE b.agreement_id = $agreement_id ORDER BY payments_id";
    }
    else
    {

        $sql = "SELECT *, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date,DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id WHERE b.agreement_id = $agreement_id ORDER BY payments_id";
    }
    $collections = $db->getAllRecords($sql);
    echo json_encode($collections);
    
});


$app->get('/show_invoice/:payments_id/:invoice_no/:invoice_date', function($payments_id, $invoice_no, $invoice_date) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "UPDATE payments set invoice_date = '$invoice_date', invoice_no = '$invoice_no' WHERE payments_id = $payments_id " ;  
    $result = $db->updateByQuery($sql);


    $invoicedata = $db->getOneRecord("SELECT *, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment,DATE_FORMAT(a.invoice_date,'%d-%m-%Y') AS invoice_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id WHERE a.payments_id = $payments_id ORDER BY a.payments_id");
    $htmlstring = "";
    
    if ($invoicedata != NULL) 
    {
        $count = 1;
        $htmlstring = '<div id="pdfinvoice" style="font-size:11px;font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">';
        
        $invoice_filename = "invoice_".$payments_id.".pdf";
        $htmlstring .= '<div style="font-size:16px;"><div class="row"><div class="col-md-12" style="text-align:center;font-size:18px;font-weight:bold;width:100%;height:10px;"></div></div></div>';
        
        $htmlstring .= '<div style="border:1px solid #000;width:100%;height:100px;" >';
        $htmlstring .= '<table cellspacing="0" cellpadding="0" >
                            <tr rowspan="2">
                                <td valign="top" style="border-right:1px solid #000000; height:50px; padding:10px; width:50%;">
                                <p style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:16px;font-weight:bold;">RD BROTHERS PROPERTY CONSULTANT LLP</p>
                                <p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">OFFICE NO. 1, 2ND FLOOR, ESSPEE TOWER,</pre></p><p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">OPP. OBEROI SKY CITY, DATTA PADA RD,</pre></p><p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">OFF W.E. HIGHWAY, BORIVALI - EAST</pre></p><p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">MUMBAI- 400066</pre></p><p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">GSTIN/UIN: 27ABBFR7915B1ZC</pre></p><p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">State Name :  Maharashtra, Code : 27</pre></p><p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">E-Mail : raj@rdbrothers.com</pre></p></td>
                                <td style="width:50%; border-right:1px solid #000000; ">
                                    <table cellspacing="0" cellpadding="5">
                                        <tr>
                                            <td valign="top" style="height:22px;font-size:11px;border-bottom:1px solid #000000;"><p>Invoice No</p><p>'.$invoice_no.'</p></td>
                                            <td valign="top" style="height:22px;font-size:11px;border-bottom:1px solid #000000;"><p>Invoice Date</p><p>'.$invoice_date.'</p></td>
                                        </tr>

                                    </table>
                                </td>
                                
                            </tr>
                        </table>
                        </div>';
        /*$htmlstring .= '<div style="border:1px solid #000;width:100%;" >';
        $htmlstring .= '<table cellspacing="0" cellpadding="5" style="width:100%; " valign="top">
                            <tr rowspan="4">
                                <td valign="top" style="border-right:1px solid #000000; padding:10px; width:60%;">
                                <p style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">To,</p>
                                <p style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">'.$row['company_name'].'</p>
                                <p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">'.$row['add1'].'</pre></p><p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">'.$row['add2'].'</pre></p><p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">Email ID:'.$row['email'].'</pre></p><p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">Mob No:'.$row['mob_no'].'</pre></p><p style="word-wrap: break-word;word-break: break-all;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">Pan No:'.$row['pan_no'].'</pre></p></td>
                                <td style="width:40%; " valign="top" >
                                    <table cellspacing="0" cellpadding="5" style="width:100%;">
                                        <tr >
                                            <td valign="top" style="border-bottom:1px solid #000000;font-size:11px;width:100%;" valign="top"><p>Party GST TIN No:</p><p>'.$row['gstin_no'].'</p></td>
                                        
                                        </tr>
                                        <tr style="border-bottom:1px solid #000000;">
                                            <td valign="top" style="height:23px;border-bottom:1px solid #000000;font-size:11px;">State:'.$row['gst_state'].' Code:'.$row['state_code'].'</td>
                                            
                                        </tr>
                                        <tr>
                                            <td style="height:23px; font-size:11px;"  valign="top"><p>PO No:'.$row['po_no'].'</p><p>PO Date:'.$row['po_date'].'</p></td>
                                            
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        </div>';
                        
        $htmlstring .= '<div id="invoice_print"  ><table cellpadding="5" cellspacing="0" style="border:1px solid #000000;width:100%;"><thead><tr>';// datatable-setupnosort=""
        
        $htmlstring .= '<th style="text-align:right;font-size:11px;width:5%;">Sr.No.</th>';
        $htmlstring .= '<th style="border-left:1px solid #000;font-size:11px;text-align:left;width:10%;">HSN SAC No.</th>';
        $htmlstring .= '<th style="border-left:1px solid #000;font-size:11px;text-align:left;width:40%;">Description of Service</th>';
        $htmlstring .= '<th style="border-left:1px solid #000;font-size:11px;width:10%;text-align:left;">UOM</th>';
        $htmlstring .= '<th style="border-left:1px solid #000;font-size:11px;text-align:right;width:10%;">Qty</th>';
        $htmlstring .= '<th style="border-left:1px solid #000;font-size:11px;text-align:right;width:10%;">Rate Per Unit</th>';
        $htmlstring .= '<th style="border-left:1px solid #000;font-size:11px;text-align:right;width:15%;">Amount Rs. P.</th>';
        $htmlstring .= '</tr></thead>';

        $htmlstring .= '<tr><td style="border-top:1px solid #000000;margin-right:3px;text-align:right;font-size:11px;" valign="top">'.$sr_no.'</td>';
        $sr_no = $sr_no + 1; 
        $htmlstring .= '<td style="border-left:1px solid #000;text-align:left;border-top:1px solid #000000;font-size:11px;" valign="top">'.$invoicerow['hsn_no'].'</td>';
        $htmlstring .= '<td style="width:450px;border-left:1px solid #000000;border-top:1px solid #000000;" valign="top"><p style="overflow-wrap: break-word;word-wrap: break-word; -ms-word-break: break-all; word-break: break-all; word-break: break-word; -ms-hyphens: auto;  -moz-hyphens: auto;  -webkit-hyphens: auto;  hyphens: auto;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">'.$invoicerow['product_name'].'</pre></p>&nbsp;&nbsp;&nbsp;&nbsp;<p style="overflow-wrap: break-word;word-wrap: break-word; -ms-word-break: break-all; word-break: break-all; word-break: break-word; -ms-hyphens: auto;  -moz-hyphens: auto;  -webkit-hyphens: auto;  hyphens: auto;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;">'.$invoicerow['product_descr'].'</pre></p>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        
        $htmlstring .= '<td style="border-left:1px solid #000;text-align:left;border-top:1px solid #000000;font-size:11px;" valign="top">';
        $htmlstring .= $invoicerow['uom'];
        $htmlstring .= '</td>';

        $htmlstring .= '<td style="border-left:1px solid #000;text-align:right;border-top:1px solid #000000;font-size:11px;" valign="top">';
        $htmlstring .= myformatinv($invoicerow['qty']);
        $htmlstring .= '</td>';


        $htmlstring .= '<td style="border-left:1px solid #000;text-align:right;border-top:1px solid #000000;font-size:11px;" valign="top">';
        $htmlstring .= myformatinv($invoicerow['rate']);
        $htmlstring .= '</td>';

        $htmlstring .= '<td style="border-left:1px solid #000;text-align:right;border-top:1px solid #000000;font-size:11px;" valign="top">';
        $htmlstring .= myformatinv($invoicerow['amount']);
        $htmlstring .= '</td></tr>';

        $subtotal = $subtotal + $invoicerow['amount'];
        $line = $line + 1;

        $htmlstring .= '<tr><td style="margin-right:3px;text-align:right;font-size:11px;" valign="top"></td>';
        $htmlstring .= '<td style="border-left:1px solid #000;text-align:right;font-size:11px;"></td>';
        $htmlstring .= '<td style="width:450px;border-left:1px solid #000000;"><p style="overflow-wrap: break-word;word-wrap: break-word; -ms-word-break: break-all; word-break: break-all; word-break: break-word; -ms-hyphens: auto;  -moz-hyphens: auto;  -webkit-hyphens: auto;  hyphens: auto;"><pre style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;font-size:11px;"></pre></p>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        
        $htmlstring .= '<td style="border-left:1px solid #000;text-align:right;font-size:11px;"></td>';

        $htmlstring .= '<td style="border-left:1px solid #000;text-align:right;font-size:11px;"></td>';


        $htmlstring .= '<td style="border-left:1px solid #000;text-align:right;font-size:11px;"></td>';

        $htmlstring .= '<td style="border-left:1px solid #000;text-align:right;font-size:11px;"></td></tr>';
        $line = $line + 1;

        $htmlstring .= '<tr><td style="border-left:1px solid #000;border-top:1px solid #000;text-align:left;font-size:11px;" colspan="3" rowspan="10" valign="top"><p style="font-size:13px;font-weight:bold;margin-bottom:10px;">Rs:'.no_to_words($subtotal).' Only.</p><br/><br/><p style="margin-top:20px;">'.$terms_condition.'</p></td>';
        
        $tax_amount = round(($subtotal*$row['tax']/100),0);
        $tax1_amount = round(($subtotal*$row['tax1']/100),0);

        $dis_amount = round((($subtotal+($tax_amount+$tax1_amount))*($row['dis_per']/100)),0);
        $htmlstring .= '<td style="border-left:1px solid #000;border-top:1px solid #000;text-align:right;font-size:11px;padding:0px;" colspan="4"><table cellpadding="5" cellspacing="0" style="width:100%;">
        <tr><td style="border-right:1px solid #000;border-bottom:1px solid #000;text-align:left;font-size:11px;width:68%;">Total</td>
        <td style="width:32%;border-bottom:1px solid #000;text-align:right;font-size:11px;">'.myformatinv($subtotal).'</td></tr>
        <tr><td style="border-right:1px solid #000;border-bottom:1px solid #000;text-align:left;font-size:11px;">CGST: @'.myformatinv($row['tax']).' %</td><td style="border-bottom:1px solid #000;text-align:right;font-size:11px;">'.myformatinv($tax_amount).'</td></tr>
        <tr><td style="border-right:1px solid #000;border-bottom:1px solid #000;text-align:left;font-size:11px;">SGST: @'.myformatinv($row['tax1']).' %</td><td style="border-bottom:1px solid #000;text-align:right;font-size:11px;">'.myformatinv($tax1_amount).'</td></tr>';
        if ($row['dis_per']>0)
        {
            $htmlstring .= '<tr><td style="border-right:1px solid #000;border-bottom:1px solid #000;text-align:left;font-size:11px;">Discount: @'.myformatinv($row['dis_per']).' %</td><td style="border-bottom:1px solid #000;text-align:right;font-size:11px;">'.myformatinv($dis_amount).'</td></tr>';
        }
        $htmlstring .= '<tr><td style="border-right:1px solid #000;border-bottom:1px solid #000;text-align:left;font-size:11px;">Total</td><td style="border-bottom:1px solid #000;text-align:right;font-size:11px;">'.myformatinv($subtotal+$tax_amount+$tax1_amount-$dis_amount).'</td></tr>
        <tr><td style="border-right:1px solid #000;border-bottom:1px solid #000;text-align:left;font-size:11px;">Round Diff:</td><td style="border-bottom:1px solid #000;text-align:left;font-size:11px;"></td></tr>
        <tr><td style="border-right:1px solid #000;text-align:left;font-size:11px;">Invoice Total:</td><td style="text-align:right;font-size:11px;">'.myformatinv($subtotal+$tax_amount+$tax1_amount-$dis_amount).'</td></tr><tr style="height:70px;"><td style="border-top:1px solid #000;text-align:left;font-size:11px;" colspan="2"><p>Certified that the above particular are true & correct</p><br/><p style="font-weight:bold;font-size:13px;">M/s.'.$vendor_name.'</p><br/><br/><br/><p style="margin-top:35px;">Proprietor</p></td></tr></table></td></tr>';
        $htmlstring .= '</table></div>';*/
        $htmlstring .='</div>';
    }   

    //include("mpdf60/mpdf.php");
    include_once "mpdf60/mpdf.php";

    require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML('<h1>Hello world!</h1>');
$mpdf->Output();
echo "i m here";
    return;
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
    
    $mpdf = new mPDF('', 'Letter', 0, '', 25, 25, 35, 35, 1, 1);
    
    $mpdf->SetDisplayMode('fullwidth');
    //$mpdf->SetFont("Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif");
    $htmlheader  = '<header><div style="width:100%">';	
    $htmlheader .='<div style="width:100%;text-align:left;float:left;"><img src="uploads//vendor//'.$vendor_logo.'" style="width:100%;height:70px;margin-top:15px;"/></div>';
    
    $htmlheader .= '</div></header>';
    //
    $htmlfooter = '';
    
    $mpdf->SetHTMLHeader($htmlheader, 'O', true);
    $mpdf->SetFooter($htmlfooter);
    
    $mpdf->WriteHTML($htmlstring);
    $ds          = DIRECTORY_SEPARATOR;
    //$dir = $_SERVER['DOCUMENT_ROOT'];
    $dir = $_SERVER['SERVER_NAME'] ;//. dirname(__FILE__);
    
    $mpdf->Output('uploads'.$ds.'reports'.$ds.$invoice_filename);
    $stmt->close();
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['invoice_filename']=$invoice_filename;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->post('/search_collections', function() use ($app) {
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

    $next_page_id = $r->searchdata->next_page_id;
    
    $role = $session['role'];


    $searchsql = "SELECT *, a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id ";

    
    $countsql = "SELECT count(*) as collection_count FROM payments ";

    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $searchsql = "SELECT *, DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date , a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id where (a.payments_id > 0) ";

        $countsql = "SELECT count(*) as collection_count FROM payments as a WHERE a.payments_id > 0 ";
    }
    else
    {
        $searchsql = "SELECT *, DATE_FORMAT(a.due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(a.next_pay_date,'%d-%m-%Y') AS next_pay_date ,a.service_tax,a.cgst,a.sgst,a.tds,a.total_brokerage, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as client_name , (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Brokerage') as brokerage_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'Service Tax') as service_tax_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'CGST') as cgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'SGST') as sgst_payment, (SELECT sum(amount) FROM account WHERE payments_id = a.payments_id and adjustment_type = 'TDS') as tds_payment, DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date FROM payments as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id LEFT JOIN contact as c ON a.client_id = c.contact_id where (a.payments > 0)";

        $countsql = "SELECT count(*) as collection_count FROM payments as a WHERE a.payments_id > 0 and (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ) ";

    }

    $sql = " ";
    if (isset($r->searchdata->agreement_for))
    {
        $sql .= " and b.agreement_for = '".$r->searchdata->agreement_for."' ";
    }

    if (isset($r->searchdata->collection_status))
    {
        $sql .= " and a.collection_status = '".$r->searchdata->collection_status."' ";
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
                $sql .= " b.assign_to LIKE '%".$value."%' ";
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
                $sql .= " b.teams LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }
    
    if (isset($r->searchdata->subteams))
    {
        $first = "Yes";
        $new_data = $r->searchdata->subteams;
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
                $sql .= " b.subteams LIKE '%".$value."%' ";
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
                $sql .= " b.client_id LIKE '".$value."' ";
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
                $sql .= " b.broker_id LIKE '".$value."' ";
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
                $sql .= " b.property_id LIKE '".$value."' ";
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
                $sql .= " b.project_id LIKE '".$value."' ";
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
                $sql .= " b.enquiry_id LIKE '".$value."' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
                
            }
        }
    }

    if (isset($r->searchdata->agreement_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->agreement_id;
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
                $sql .= " b.agreement_id LIKE '".$value."' ";
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
        $tcreated_date_from = substr($created_date_from,6,4)."-".substr($created_date_from,3,2)."-".substr($created_date_from,0,2)." 00:00:00";
        if (isset($r->searchdata->created_date_to))
        {
            $created_date_to = $r->searchdata->created_date_to;
            $tcreated_date_to = substr($created_date_to,6,4)."-".substr($created_date_to,3,2)."-".substr($created_date_to,0,2)." 23:59:59";
        }
        $sql .= " and b.created_date BETWEEN '".$tcreated_date_from."' AND '".$tcreated_date_to."' ";
    }

    if (isset($r->searchdata->deal_date_from))
    {
        $deal_date_from = $r->searchdata->deal_date_from;
        $tdeal_date_from = substr($deal_date_from,6,4)."-".substr($deal_date_from,3,2)."-".substr($deal_date_from,0,2)." 00:00:00";
        if (isset($r->searchdata->deal_date_to))
        {
            $deal_date_to = $r->searchdata->deal_date_to;
            $tdeal_date_to = substr($deal_date_to,6,4)."-".substr($deal_date_to,3,2)."-".substr($deal_date_to,0,2)." 23:59:59";
        }
        $sql .= " and b.deal_date BETWEEN '".$tdeal_date_from."' AND '".$tdeal_date_to."' ";
    }

    if (isset($r->searchdata->due_date_from))
    {
        $due_date_from = $r->searchdata->due_date_from;
        $tdue_date_from = substr($due_date_from,6,4)."-".substr($due_date_from,3,2)."-".substr($due_date_from,0,2)." 00:00:00";
        if (isset($r->searchdata->due_date_to))
        {
            $due_date_to = $r->searchdata->due_date_to;
            $tdue_date_to = substr($due_date_to,6,4)."-".substr($due_date_to,3,2)."-".substr($due_date_to,0,2)." 23:59:59";
        }
        $sql .= " and a.due_date BETWEEN '".$tdue_date_from."' AND '".$tdue_date_to."' ";
    }
    
    $searchsql .=  $sql .  " GROUP BY a.agreement_id ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";// CAST(a.property_id as UNSIGNED) DESC";

    $collections = $db->getAllRecords($searchsql);

    $countsql = $countsql . $sql;
    
    $collection_count = 0;
    $collectioncountdata = $db->getAllRecords($countsql);
    if ($collectioncountdata)
    {
         $collection_count = $collectioncountdata[0]['collection_count'];

    }
    //if ($collection_count>0)
    //{
        $collections[0]['collection_count']=$collection_count;
    //}
    $collections[0]['sql']=$countsql;
    echo json_encode($collections);
});

$app->get('/getdatavalues_collections/:field_name', function($field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM agreement  ORDER BY $field_name";
    //echo $sql;
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});



$app->get('/payments_edit_ctrl/:payments_id', function($payments_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,DATE_FORMAT(due_date,'%d-%m-%Y') AS due_date,DATE_FORMAT(next_pay_date,'%d-%m-%Y') AS next_pay_date from payments WHERE payments_id=".$payments_id;
    $paymentsdata = $db->getAllRecords($sql);
    echo json_encode($paymentsdata);
});


$app->post('/payments_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('payments_id'),$r->paymentsdata);
    $db = new DbHandler();

    $payments_id  = $r->paymentsdata->payments_id;
    if (isset($r->paymentsdata->due_date))
    {
        $due_date = $r->paymentsdata->due_date;
        $tdue_date = substr($due_date,6,4)."-".substr($due_date,3,2)."-".substr($due_date,0,2);
        $r->paymentsdata->due_date = $tdue_date;
    }

    if (isset($r->paymentsdata->next_pay_date))
    {
        $next_pay_date = $r->paymentsdata->next_pay_date;
        $tnext_pay_date = substr($next_pay_date,6,4)."-".substr($next_pay_date,3,2)."-".substr($next_pay_date,0,2);
        $r->paymentsdata->next_pay_date = $tnext_pay_date;
    }
    $reminder_due_date = $r->paymentsdata->reminder_due_date;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->paymentsdata->modified_by = $modified_by;
    $r->paymentsdata->modified_date = $modified_date;
    $agreement_id = $r->paymentsdata->agreement_id;
    $client_id = $r->paymentsdata->client_id;
    $billing_name = $r->paymentsdata->billing_name;
    $client_name ="";
    $total_brokerage = $r->paymentsdata->total_brokerage;
    $ispaymentsExists = $db->getOneRecord("select 1 from payments where payments_id = $payments_id");
    if($ispaymentsExists)
    {
        $tabble_name = "payments";
        $column_names = array('client_id','billing_name','due_date','next_pay_date','brokerage','service_tax','cgst','sgst','tds','total_brokerage','collection_status','payment_received','reminder_due_date','modified_by','modified_date');
        $multiple=array();
        $condition = "payments_id='$payments_id'";
        $history = $db->historydata( $r->paymentsdata, $column_names, $tabble_name,$condition,$payments_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->paymentsdata, $column_names, $tabble_name,$condition, $multiple);
        if ($result != NULL) 
        {
            $response["status"] = "success";
            $response["message"] = "Payments Updated successfully";

            if ($reminder_due_date)
            {
                $myemaildata = $db->getAllRecords("SELECT CONCAT(name_title,' ',f_name,' ',l_name) as name FROM contact WHERE contact_id = $client_id LIMIT 1");
                if ($myemaildata)
                {
                    $client_name = $myemaildata[0]['name'];
                }                
                $myemail = "crm@rdbrothers.com";
                $toemail = "accounts@rdbrothers.com";
                //$toemail = "shekhar.lanke@gmail.com";
                $cc_mail_id = "crm@rdbrothers.com,dhaval@rdbrothers.com";
                $email_subject = "Payment Reminder Mail for [AGREEMENTID:".$agreement_id."]";
                $message_to_send = "<p>Dear Sir/Madam,</p><p></p><p>Payment Reminder [PAYMENTSID:".$payments_id."]</p><p>Client Name:".$client_name."</p><p>".$billing_name."</p><p>Due Date:".$due_date."</p><p>Next Payment Date:".$next_pay_date."</p><p>Total Brokerage:".$total_brokerage."</p><p></p><p>Thank you..</p><p></p>";
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
                    $query = "INSERT INTO client_mails (mail_date , category,category_id, subject, text_message, from_mail_id, to_mail_id, cc_mail_id,created_by,  created_date)   VALUES('$mail_date' , 'reminder_due_date', '$payments_id', '$email_subject', '$email_body', '$myemail','$toemail', '$cc_mail_id', '$created_by', '$created_date')";
                    $result = $db->insertByQuery($query);
                }
            }
            echoResponse(200, $response);
        }
        else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Payments. Please try again";
            echoResponse(201, $response);
        }            
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Payments with the provided Payments ID does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/payments_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('payments_id'),$r->paymentsdata);
    $db = new DbHandler();
    $payments_id  = $r->paymentsdata->payments_id;
    $ispaymentsExists = $db->getOneRecord("select 1 from payments where payments_id=$payments_id");
    if($ispaymentsExists)
    {
        $tabble_name = "payments";
        $column_names = array('payments_id');
        $condition = "payments_id='$payments_id'";
        $result = $db->deleteIntoTable($r->paymentsdata, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Payments Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Payments. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Payments with the provided Payments does not exists!";
        echoResponse(201, $response);
    }
});


// CONTRIBUTIONS


$app->get('/contributions_ctrl', function() use ($app) {
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
        $team_query .= " OR b.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];

    $sql = "SELECT * from agreement LIMIT 0";

    if (in_array("Admin", $role) || in_array("Accountant", $role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT *,a.agreement_id,CONCAT(d.salu,' ',d.fname,' ',d.lname) as name,a.contribution_by,a.contribution_per,a.contribution_amount,DATE_FORMAT(b.created_date,'%d-%m-%Y') AS created_date ,DATE_FORMAT(e.transaction_date,'%d-%m-%Y') AS transaction_date ,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(f.name_title,' ',f.f_name,' ',f.l_name) as client_name FROM agreement_details as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id LEFT JOIN users as c ON a.contribution_to = c.user_id LEFT JOIN employee as d ON d.emp_id = c.emp_id LEFT JOIN voucher as e ON a.agreement_details_id = e.contributions_id LEFT JOIN contact as f ON b.contact_id = f.contact_id WHERE a.contribution_amount > 0 ORDER BY b.created_date DESC";
    }

    else if (in_array("Branch Head", $role))
    {
        $sql = "SELECT *,a.agreement_id,CONCAT(d.salu,' ',d.fname,' ',d.lname) as name,a.contribution_by,a.contribution_per,a.contribution_amount,DATE_FORMAT(b.created_date,'%d-%m-%Y') AS created_date ,DATE_FORMAT(e.transaction_date,'%d-%m-%Y') AS transaction_date ,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(f.name_title,' ',f.f_name,' ',f.l_name) as client_name FROM agreement_details as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id LEFT JOIN users as c ON a.contribution_to = c.user_id LEFT JOIN employee as d ON d.emp_id = c.emp_id LEFT JOIN voucher as e ON a.agreement_details_id = e.contributions_id LEFT JOIN contact as f ON b.contact_id = f.contact_id WHERE (b.created_by = $user_id or FIND_IN_SET ($user_id ,b.assign_to) ".$team_query." ) and a.contribution_amount > 0 ORDER BY b.created_date DESC";
    }
    else
    {
        $sql = "SELECT *,a.agreement_id,CONCAT(d.salu,' ',d.fname,' ',d.lname) as name,a.contribution_by,a.contribution_per,a.contribution_amount,DATE_FORMAT(b.created_date,'%d-%m-%Y') AS created_date ,DATE_FORMAT(e.transaction_date,'%d-%m-%Y') AS transaction_date ,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(f.name_title,' ',f.f_name,' ',f.l_name) as client_name FROM agreement_details as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id LEFT JOIN users as c ON a.contribution_to = c.user_id LEFT JOIN employee as d ON d.emp_id = c.emp_id LEFT JOIN voucher as e ON a.agreement_details_id = e.contributions_id LEFT JOIN contact as f ON b.contact_id = f.contact_id WHERE (b.created_by = $user_id or FIND_IN_SET ($user_id ,b.modified_by)  ) and a.contribution_amount > 0 ORDER BY b.created_date DESC";
    }
    $collections = $db->getAllRecords($sql);
    echo json_encode($collections);    
});

$app->get('/get_contributions/:agreement_id', function($agreement_id) use ($app) {
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
        $team_query .= " OR b.teams LIKE '".$value."' ";
    }
    $user_id = $session['user_id'];
    $permissions = explode(",", $session['permissions']);
    $role = $session['role'];

    $sql = "SELECT * from agreement LIMIT 0";

    if (in_array("Admin", $role) || in_array("Accountant", $role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT *,a.agreement_id,CONCAT(d.salu,' ',d.fname,' ',d.lname) as name,a.contribution_by,a.contribution_per,a.contribution_amount,DATE_FORMAT(b.created_date,'%d-%m-%Y') AS created_date ,DATE_FORMAT(e.transaction_date,'%d-%m-%Y') AS transaction_date ,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(f.name_title,' ',f.f_name,' ',f.l_name) as client_name FROM agreement_details as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id LEFT JOIN users as c ON a.contribution_to = c.user_id LEFT JOIN employee as d ON d.emp_id = c.emp_id LEFT JOIN voucher as e ON a.agreement_details_id = e.contributions_id LEFT JOIN contact as f ON b.contact_id = f.contact_id  WHERE a.agreement_id = $agreement_id ORDER BY b.created_date DESC";
    }

    else if (in_array("Branch Head", $role))
    {
        $sql = "SELECT *,a.agreement_id,CONCAT(d.salu,' ',d.fname,' ',d.lname) as name,a.contribution_by,a.contribution_per,a.contribution_amount,DATE_FORMAT(b.created_date,'%d-%m-%Y') AS created_date ,DATE_FORMAT(e.transaction_date,'%d-%m-%Y') AS transaction_date ,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(f.name_title,' ',f.f_name,' ',f.l_name) as client_name FROM agreement_details as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id LEFT JOIN users as c ON a.contribution_to = c.user_id LEFT JOIN employee as d ON d.emp_id = c.emp_id LEFT JOIN voucher as e ON a.agreement_details_id = e.contributions_id LEFT JOIN contact as f ON b.contact_id = f.contact_id WHERE a.agreement_id = $agreement_id  ORDER BY b.created_date DESC";
    }
    else
    {
        $sql = "SELECT *,a.agreement_id,CONCAT(d.salu,' ',d.fname,' ',d.lname) as name,a.contribution_by,a.contribution_per,a.contribution_amount,DATE_FORMAT(b.created_date,'%d-%m-%Y') AS created_date ,DATE_FORMAT(e.transaction_date,'%d-%m-%Y') AS transaction_date ,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(f.name_title,' ',f.f_name,' ',f.l_name) as client_name FROM agreement_details as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id LEFT JOIN users as c ON a.contribution_to = c.user_id LEFT JOIN employee as d ON d.emp_id = c.emp_id LEFT JOIN voucher as e ON a.agreement_details_id = e.contributions_id LEFT JOIN contact as f ON b.contact_id = f.contact_id WHERE a.agreement_id = $agreement_id  ORDER BY b.created_date DESC";
    }
    $contributions = $db->getAllRecords($sql);
    echo json_encode($contributions);    
});


$app->get('/contributions_edit_ctrl/:agreement_details_id', function($agreement_details_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from agreement_details WHERE agreement_details_id=".$agreement_details_id;
    $contributionsdata = $db->getAllRecords($sql);
    echo json_encode($contributionsdata);
});


$app->post('/contributions_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('agreement_details_id'),$r->contributionsdata);
    $db = new DbHandler();
    $agreement_details_id  = $r->contributionsdata->agreement_details_id;
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->contributionsdata->modified_by = $modified_by;
    $r->contributionsdata->modified_date = $modified_date;

    $isagreementExists = $db->getOneRecord("select 1 from agreement_details where agreement_details_id = $agreement_details_id");
    if($isagreementExists)
    {
        $tabble_name = "agreement_details";

        $column_names = array('contribution_to','contribution_per','contribution_amount','payment_approved','amount_paid','modified_by','modified_date');

        $multiple=array();
        $condition = "agreement_details_id='$agreement_details_id'";
        $history = $db->historydata( $r->contributionsdata, $column_names, $tabble_name,$condition,$agreement_details_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->contributionsdata, $column_names, $tabble_name,$condition, $multiple);
        if ($result != NULL) 
        {
            $response["status"] = "success";
            $_SESSION['tmpagreement_id'] = $agreement_details_id;
            $response["message"] = "Contribution Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Contribution. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Contribution with the provided Contribution does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/contributions_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('agreement_details_id'),$r->contributionsdata);
    $db = new DbHandler();
    $agreement_details_id  = $r->contributionsdata->agreement_details_id;
    $isagreementExists = $db->getOneRecord("select 1 from agreement_details where agreement_details_id=$agreement_details_id");
    if($isagreementExists){
        $tabble_name = "agreement_details_id";
        $column_names = array('agreement_details_id');
        $condition = "agreement_details_id='$agreement_details_id'";
        $result = $db->deleteIntoTable($r->contributionsdata, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Contributions Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete contributions. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Contributions with the provided contributions does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/search_contributions', function() use ($app) {
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

    $searchsql = "SELECT *,a.agreement_id,CONCAT(d.salu,' ',d.fname,' ',d.lname) as name,a.contribution_by,a.contribution_per,a.contribution_amount,DATE_FORMAT(b.created_date,'%d-%m-%Y') AS created_date ,DATE_FORMAT(e.transaction_date,'%d-%m-%Y') AS transaction_date ,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(f.name_title,' ',f.f_name,' ',f.l_name) as client_name FROM agreement_details as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id LEFT JOIN users as c ON a.contribution_to = c.user_id LEFT JOIN employee as d ON d.emp_id = c.emp_id LEFT JOIN voucher as e ON a.agreement_details_id = e.contributions_id LEFT JOIN contact as f ON b.contact_id = f.contact_id WHERE a.contribution_amount > 0";

    
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $searchsql = "SELECT *,a.agreement_id,CONCAT(d.salu,' ',d.fname,' ',d.lname) as name,a.contribution_by,a.contribution_per,a.contribution_amount,DATE_FORMAT(b.created_date,'%d-%m-%Y') AS created_date ,DATE_FORMAT(e.transaction_date,'%d-%m-%Y') AS transaction_date ,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(f.name_title,' ',f.f_name,' ',f.l_name) as client_name FROM agreement_details as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id LEFT JOIN users as c ON a.contribution_to = c.user_id LEFT JOIN employee as d ON d.emp_id = c.emp_id LEFT JOIN voucher as e ON a.agreement_details_id = e.contributions_id LEFT JOIN contact as f ON b.contact_id = f.contact_id WHERE a.contribution_amount > 0  ";
    }
    else
    {
        $searchsql = "SELECT *,a.agreement_id,CONCAT(d.salu,' ',d.fname,' ',d.lname) as name,a.contribution_by,a.contribution_per,a.contribution_amount,DATE_FORMAT(b.created_date,'%d-%m-%Y') AS created_date ,DATE_FORMAT(e.transaction_date,'%d-%m-%Y') AS transaction_date ,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , b.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , b.sub_teams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assign_to, CONCAT(f.name_title,' ',f.f_name,' ',f.l_name) as client_name FROM agreement_details as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id LEFT JOIN users as c ON a.contribution_to = c.user_id LEFT JOIN employee as d ON d.emp_id = c.emp_id LEFT JOIN voucher as e ON a.agreement_details_id = e.contributions_id LEFT JOIN contact as f ON b.contact_id = f.contact_id WHERE a.contribution_amount > 0  ";
        
    }

    $sql = " ";

    if (isset($r->searchdata->user_id))
    {
        $sql .= " and a.contribution_to = '".$r->searchdata->user_id."' ";
    }

    if (isset($r->searchdata->voucher_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->voucher_id;
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
                $sql .= " a.voucher_id LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }

    if (isset($r->searchdata->agreement_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->agreement_id;
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
                $sql .= " a.agreement_id LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }

    if (isset($r->searchdata->agreement_details_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->agreement_details_id;
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
                $sql .= " a.agreement_details_id LIKE '%".$value."%' ";
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
    
    if (isset($r->searchdata->subteams))
    {
        $first = "Yes";
        $new_data = $r->searchdata->subteams;
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
                $sql .= " a.subteams LIKE '%".$value."%' ";
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
        $tcreated_date_from = substr($created_date_from,6,4)."-".substr($created_date_from,3,2)."-".substr($created_date_from,0,2)." 00:00:00";
        if (isset($r->searchdata->created_date_to))
        {
            $created_date_to = $r->searchdata->created_date_to;
            $tcreated_date_to = substr($created_date_to,6,4)."-".substr($created_date_to,3,2)."-".substr($created_date_to,0,2)." 23:59:59";
        }
        $sql .= " and b.created_date BETWEEN '".$tcreated_date_from."' AND '".$tcreated_date_to."' ";
    }
    $searchsql .=  $sql .  " ORDER BY b.created_date DESC";
    
    $contributions = $db->getAllRecords($searchsql);
    echo json_encode($contributions);
});

$app->get('/getdatavalues_contributions/:field_name', function($field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM agreement_details  ORDER BY $field_name";
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
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
    $user_id = $session['user_id'];
    $role = $session['role'];
    $team_query = " ";  
    $tteams = $session['teams'];

    $first_record = "Yes";

    foreach($tteams as $value)
    {
        if ($first_record == 'Yes')
        {
            $team_query .= " ";
            $first_record = "No";
            $team_query .= " WHERE FIND_IN_SET(".$value." , a.teams) ";
            
        }
        else
        {
            $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
        }
    }
    $team_query .= "  "; 

    $sql = "";
    if (in_array("Admin", $role)  || in_array("Accountant", $role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT *, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.receipt_date,'%d-%m-%Y') AS receipt_date,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , a.subteams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM account as a LEFT JOIN contact as b on a.client_id = b.contact_id  ORDER BY a.transaction_date DESC";
        
    }
    else if (in_array("Branch Head", $role))
    {
        $sql = "SELECT *, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.receipt_date,'%d-%m-%Y') AS receipt_date,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , a.subteams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM account as a LEFT JOIN contact as b on a.client_id = b.contact_id ".$team_query." ORDER BY a.transaction_date DESC";
    }
    else
    {
        $sql = "SELECT *, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.receipt_date,'%d-%m-%Y') AS receipt_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , a.subteams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM account as a LEFT JOIN contact as b on a.client_id = b.contact_id  WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.modified_by) or FIND_IN_SET ($user_id ,a.assign_to) ) ORDER BY a.transaction_date DESC";
        

    }
    $accountlist = $db->getAllRecords($sql);
    echo json_encode($accountlist);

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
    verifyRequiredParams(array('payments_id'),$r->accountdata);
    $db = new DbHandler();
    
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->accountdata->created_by = $created_by;
    $r->accountdata->created_date = $created_date;

    if (isset($r->accountdata->transaction_date))
    {
        $ttransaction_date = substr($r->accountdata->transaction_date,6,4)."-".substr($r->accountdata->transaction_date,3,2)."-".substr($r->accountdata->transaction_date,0,2)." ".substr($r->accountdata->transaction_date,11,5);
        $r->accountdata->transaction_date = $ttransaction_date;
    }
    if (isset($r->accountdata->cheque_date))
    {
        $tcheque_date = substr($r->accountdata->cheque_date,6,4)."-".substr($r->accountdata->cheque_date,3,2)."-".substr($r->accountdata->cheque_date,0,2)." ".substr($r->accountdata->cheque_date,11,5);
        $r->accountdata->cheque_date = $tcheque_date;
    }
    if (isset($r->accountdata->receipt_date))
    {
        $treceipt_date = substr($r->accountdata->receipt_date,6,4)."-".substr($r->accountdata->receipt_date,3,2)."-".substr($r->accountdata->receipt_date,0,2)." ".substr($r->accountdata->receipt_date,11,5);
        $r->accountdata->receipt_date = $treceipt_date;
    }
    $tabble_name = "account";
    $column_names = array('agreement_id','payments_id','client_id','transaction_date','adjustment_type','amount','payment_type','receipt_no','receipt_date','instrument_no','cheque_no','cheque_date','drawn_on','branch','cheque_status','comments','teams','subteams','assign_to','created_by','created_date');
    $multiple=array("teams","assign_to","subteams");
    $result = $db->insertIntoTable($r->accountdata, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Account created successfully";
        $response["account_id"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create account. Please try again";
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
    $sql = "SELECT *,DATE_FORMAT(transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(receipt_date,'%d-%m-%Y') AS receipt_date  from account where account_id=".$account_id;
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
    verifyRequiredParams(array('account_id'),$r->accountdata);
    $db = new DbHandler();
    $account_id  = $r->accountdata->account_id;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->accountdata->modified_by = $modified_by;
    $r->accountdata->modified_date = $modified_date;
    if (isset($r->accountdata->transaction_date))
    {
        $ttransaction_date = substr($r->accountdata->transaction_date,6,4)."-".substr($r->accountdata->transaction_date,3,2)."-".substr($r->accountdata->transaction_date,0,2)." ".substr($r->accountdata->transaction_date,11,5);
        $r->accountdata->transaction_date = $ttransaction_date;
    }
    if (isset($r->accountdata->cheque_date))
    {
        $tcheque_date = substr($r->accountdata->cheque_date,6,4)."-".substr($r->accountdata->cheque_date,3,2)."-".substr($r->accountdata->cheque_date,0,2)." ".substr($r->accountdata->cheque_date,11,5);
        $r->accountdata->cheque_date = $tcheque_date;
    }
    if (isset($r->accountdata->receipt_date))
    {
        $treceipt_date = substr($r->accountdata->receipt_date,6,4)."-".substr($r->accountdata->receipt_date,3,2)."-".substr($r->accountdata->receipt_date,0,2)." ".substr($r->accountdata->receipt_date,11,5);
        $r->accountdata->receipt_date = $treceipt_date;
    }

    $isaccountExists = $db->getOneRecord("select 1 from account where account_id=$account_id");
    if($isaccountExists){
        $tabble_name = "account";
        $column_names = array('agreement_id','payments_id','client_id','transaction_date','adjustment_type','amount','payment_type','receipt_no','receipt_date','instrument_no','cheque_no','cheque_date','drawn_on','branch','cheque_status','comments','teams','subteams','assign_to','modified_by','modified_date');
        $multiple=array("teams","assign_to","subteams");
        $condition = "account_id='$account_id'";
        $history = $db->historydata( $r->accountdata, $column_names, $tabble_name,$condition,$activity_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->accountdata, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Account Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Account. Please try again";
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
    verifyRequiredParams(array('account_id'),$r->accountdata);
    $db = new DbHandler();
    $account_id  = $r->accountdata->account_id;
    $isaccountExists = $db->getOneRecord("select 1 from account where account_id=$account_id");
    if($isaccountExists){
        $tabble_name = "account";
        $column_names = array('client_id','transaction_date','adjustment_type','amount','payment_type','receipt_no','receipt_date','instrument_no','cheque_no','cheque_date','drawn_on','branch','cheque_status','comments','team');
        $condition = "account_id='$account_id'";
        $result = $db->deleteIntoTable($r->accountdata, $column_names, $tabble_name,$condition);
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
    $sql = "SELECT * from account ORDER BY receipt_date DESC";
    $permissions = $db->getAllRecords($sql);
    echo json_encode($permissios);
});


$app->post('/search_account', function() use ($app) {
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


    $searchsql = "SELECT *, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.receipt_date,'%d-%m-%Y') AS receipt_date FROM account as a LEFT JOIN contact as b on a.client_id = b.contact_id  ORDER BY a.transaction_date DESC";


    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $searchsql = "SELECT *, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.receipt_date,'%d-%m-%Y') AS receipt_date FROM account as a LEFT JOIN contact as b on a.client_id = b.contact_id WHERE a.created_by > 0  ";
    }
    else
    {
        $searchsql = "SELECT *, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.receipt_date,'%d-%m-%Y') AS receipt_date FROM account as a LEFT JOIN contact as b on a.client_id = b.contact_id WHERE a.created_by > 0  ";
        
    }

    $sql = " ";

    if (isset($r->searchdata->client_id))
    {
        $sql .= " and a.client_id = '".$r->searchdata->client_id."' ";
    }

    if (isset($r->searchdata->account_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->account_id;
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
                $sql .= " a.account_id LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }

    if (isset($r->searchdata->agreement_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->agreement_id;
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
                $sql .= " a.agreement_id LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }

    if (isset($r->searchdata->payments_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->payments_id;
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
                $sql .= " a.payments_id LIKE '%".$value."%' ";
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
    
    if (isset($r->searchdata->subteams))
    {
        $first = "Yes";
        $new_data = $r->searchdata->subteams;
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
                $sql .= " a.subteams LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->transaction_date_from))
    {
        $transaction_date_from = $r->searchdata->transaction_date_from;
        $ttransaction_date_from = substr($transaction_date_from,6,4)."-".substr($transaction_date_from,3,2)."-".substr($transaction_date_from,0,2);
        if (isset($r->searchdata->transaction_date_to))
        {
            $transaction_date_to = $r->searchdata->transaction_date_to;
            $ttransaction_date_to = substr($transaction_date_to,6,4)."-".substr($transaction_date_to,3,2)."-".substr($transaction_date_to,0,2);
        }
        $sql .= " and a.transaction_date BETWEEN '".$ttransaction_date_from."' AND '".$ttransaction_date_to."' ";
    }
    $searchsql .=  $sql .  " ORDER BY a.transaction_date DESC";
    
    $accountlist = $db->getAllRecords($searchsql);
    echo json_encode($accountlist);
});

$app->get('/getdatavalues_account/:field_name', function($field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM account  ORDER BY $field_name";
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});



$app->get('/selectagreement', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT agreement_id, CONCAT('AGREEMENTID_',agreement_id) as agreement_title from agreement ORDER BY agreement_id DESC";
    $agreements = $db->getAllRecords($sql);
    echo json_encode($agreements);
});

$app->get('/selectpayments', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT payments_id, CONCAT('PAYMENTSID_',payments_id) as payments_title from payments ORDER BY payments_id DESC";
    $payments = $db->getAllRecords($sql);
    echo json_encode($payments);
});

$app->get('/getpaymentids/:agreement_id', function($agreement_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT payments_id, CONCAT('PAYMENTID_',payments_id) as payments_title from payments WHERE agreement_id=".$agreement_id." and payment_received != 'Yes' ";
    $payments = $db->getAllRecords($sql);
    echo json_encode($payments);
    
});

$app->get('/getassigned_agreement/:agreement_id', function($agreement_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT assign_to, teams FROM agreement WHERE agreement_id=".$agreement_id;
    $assigned_agreement = $db->getAllRecords($sql);
    echo json_encode($assigned_agreement);
    
});



$app->get('/getclientids/:payments_id', function($payments_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT b.contact_id, b.contact_off, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name,'   ',b.mob_no,'  ',b.company_name) as name from payments as a LEFT JOIN contact as b ON a.client_id = b.contact_id where a.payments_id=".$payments_id;
    $clients = $db->getAllRecords($sql);
    echo json_encode($clients);
    
});


$app->get('/getaccountdetails/:payments_id', function($payments_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT a.brokerage, b.teams, b.assign_to from payments as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id WHERE a.payments_id=".$payments_id;
    $accountdetails = $db->getAllRecords($sql);
    echo json_encode($accountdetails);
    
});

$app->get('/getaccount_balance/:payments_id', function($payments_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();

    $account_balance = "";

    $mainsql = "SELECT * FROM employee_asset as a LEFT JOIN users as b ON b.emp_id = a.emp_id WHERE b.user_id = $user_id ";

    $stmt = $db->getRows($mainsql);
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $account_balance = $account_balance .$row['asset_title'].',';
        }
    }
    
    $htmldata['account_balance']=$account_balance;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
    
});
    

// VOUCHER

$app->get('/voucher_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];
    $role = $session['role'];
    $team_query = " ";  
    $tteams = $session['teams'];

    $first_record = "Yes";

    foreach($tteams as $value)
    {
        if ($first_record == 'Yes')
        {
            $team_query .= " ";
            $first_record = "No";
            $team_query .= " WHERE FIND_IN_SET(".$value." , a.teams) ";
            
        }
        else
        {
            $team_query .= " OR FIND_IN_SET(".$value." , a.teams) ";
        }
    }
    $team_query .= "  ";

    $sql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.voucher_date,'%d-%m-%Y') AS voucher_date,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , a.subteams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id  ORDER BY a.transaction_date DESC";

    if (in_array("Admin", $role)  || in_array("Accountant", $role) || in_array("Sub Admin", $role))
    {
        $sql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.voucher_date,'%d-%m-%Y') AS voucher_date,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , a.subteams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id  ORDER BY a.transaction_date DESC";
        
    }
    else if (in_array("Branch Head", $role))
    {
        $sql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.voucher_date,'%d-%m-%Y') AS voucher_date,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , a.subteams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id  ".$team_query."  ORDER BY a.transaction_date DESC ";
    }
    else
    {
        $sql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.voucher_date,'%d-%m-%Y') AS voucher_date,(SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team,(SELECT GROUP_CONCAT(su.sub_team_name SEPARATOR ',') FROM sub_teams as su where FIND_IN_SET(su.sub_team_id , a.subteams)) as subteam,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.modified_by) or FIND_IN_SET ($user_id ,a.assign_to) ) ORDER BY a.transaction_date DESC";       

    }

    $voucherlist = $db->getAllRecords($sql);
    echo json_encode($voucherlist);
});


$app->post('/voucher_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('amount'),$r->voucherdata);
    $db = new DbHandler();

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->voucherdata->created_by = $created_by;
    $r->voucherdata->created_date = $created_date;

    if (isset($r->voucherdata->transaction_date))
    {
        $ttransaction_date = substr($r->voucherdata->transaction_date,6,4)."-".substr($r->voucherdata->transaction_date,3,2)."-".substr($r->voucherdata->transaction_date,0,2)." ".substr($r->voucherdata->transaction_date,11,5);
        $r->voucherdata->transaction_date = $ttransaction_date;
    }
    if (isset($r->voucherdata->cheque_date))
    {
        $tcheque_date = substr($r->voucherdata->cheque_date,6,4)."-".substr($r->voucherdata->cheque_date,3,2)."-".substr($r->voucherdata->cheque_date,0,2)." ".substr($r->voucherdata->cheque_date,11,5);
        $r->voucherdata->cheque_date = $tcheque_date;
    }
    if (isset($r->voucherdata->voucher_date))
    {
        $tvoucher_date = substr($r->voucherdata->voucher_date,6,4)."-".substr($r->voucherdata->voucher_date,3,2)."-".substr($r->voucherdata->voucher_date,0,2)." ".substr($r->voucherdata->voucher_date,11,5);
        $r->voucherdata->voucher_date = $tvoucher_date;
    }
    $tabble_name = "voucher";
    
    $column_names = array('agreement_id','contributions_id','user_id','transaction_date','amount','payment_type','voucher_no','voucher_date','instrument_no','cheque_no','cheque_date','drawn_on','branch','cheque_status','comments','teams','subteams','assign_to','created_by','created_date');
    
    $multiple=array("teams","assign_to","subteams");
    
    $result = $db->insertIntoTable($r->voucherdata, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Voucher created successfully";
        $response["voucher_id"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create voucher. Please try again";
        echoResponse(201, $response);
    }
});


$app->get('/voucher_edit_ctrl/:voucher_id', function($voucher_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT *,DATE_FORMAT(transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(voucher_date,'%d-%m-%Y') AS voucher_date  from voucher where voucher_id=".$voucher_id;
    $vouchers = $db->getAllRecords($sql);
    echo json_encode($vouchers);
    
});

$app->post('/voucher_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('voucher_id'),$r->voucherdata);
    $db = new DbHandler();
    $voucher_id  = $r->voucherdata->voucher_id;
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->voucherdata->modified_by = $modified_by;
    $r->voucherdata->modified_date = $modified_date;
    if (isset($r->voucherdata->transaction_date))
    {
        $ttransaction_date = substr($r->voucherdata->transaction_date,6,4)."-".substr($r->voucherdata->transaction_date,3,2)."-".substr($r->voucherdata->transaction_date,0,2)." ".substr($r->voucherdata->transaction_date,11,5);
        $r->voucherdata->transaction_date = $ttransaction_date;
    }
    if (isset($r->voucherdata->cheque_date))
    {
        $tcheque_date = substr($r->voucherdata->cheque_date,6,4)."-".substr($r->voucherdata->cheque_date,3,2)."-".substr($r->voucherdata->cheque_date,0,2)." ".substr($r->voucherdata->cheque_date,11,5);
        $r->voucherdata->cheque_date = $tcheque_date;
    }
    if (isset($r->voucherdata->voucher_date))
    {
        $tvoucher_date = substr($r->voucherdata->voucher_date,6,4)."-".substr($r->voucherdata->voucher_date,3,2)."-".substr($r->voucherdata->voucher_date,0,2)." ".substr($r->voucherdata->voucher_date,11,5);
        $r->voucherdata->voucher_date = $tvoucher_date;
    }

    $isvoucherExists = $db->getOneRecord("select 1 from voucher where voucher_id=$voucher_id");
    if($isvoucherExists){
        $tabble_name = "voucher";
        $column_names = array('agreement_id','contributions_id','user_id','transaction_date','amount','payment_type','voucher_no','voucher_date','instrument_no','cheque_no','cheque_date','drawn_on','branch','cheque_status','comments','teams','subteams','assign_to','modified_by','modified_date');
        $multiple=array("teams","assign_to","subteams");
        $condition = "voucher_id='$voucher_id'";
        $history = $db->historydata( $r->voucherdata, $column_names, $tabble_name,$condition,$activity_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->voucherdata, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Voucher Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Voucher. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "voucher with the provided voucher does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/voucher_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('voucher_id'),$r->voucherdata);
    $db = new DbHandler();
    $voucher_id  = $r->voucherdata->voucher_id;
    $isvoucherExists = $db->getOneRecord("select 1 from voucher where voucher_id=$voucher_id");
    if($isvoucherExists){
        $tabble_name = "voucher";
        $column_names = array('agreement_id','contribution_id','emp_id','transaction_date','adjustment_type','amount','payment_type','voucher_no','voucher_date','instrument_no','cheque_no','cheque_date','drawn_on','branch','cheque_status','comments','teams');
        $condition = "voucher_id='$voucher_id'";
        $result = $db->deleteIntoTable($r->voucherdata, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Voucher Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Voucher. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Voucher with the provided voucher does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectvoucher', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from voucher ORDER BY voucher_date DESC";
    $vouchers = $db->getAllRecords($sql);
    echo json_encode($vouchers);
});

$app->post('/search_voucher', function() use ($app) {
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

    $searchsql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.voucher_date,'%d-%m-%Y') AS voucher_date FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id  ORDER BY a.transaction_date DESC";

    
    if (in_array("Admin", $role) || in_array("Sub Admin", $role))
    {
        $searchsql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.voucher_date,'%d-%m-%Y') AS voucher_date FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.created_by > 0  ";
    }
    else
    {
        $searchsql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as emp_name,DATE_FORMAT(a.transaction_date,'%d-%m-%Y') AS transaction_date,DATE_FORMAT(a.cheque_date,'%d-%m-%Y') AS cheque_date,DATE_FORMAT(a.voucher_date,'%d-%m-%Y') AS voucher_date FROM voucher as a LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id WHERE a.created_by > 0  ";
        
    }

    $sql = " ";

    if (isset($r->searchdata->user_id))
    {
        $sql .= " and a.user_id = '".$r->searchdata->user_id."' ";
    }

    if (isset($r->searchdata->voucher_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->voucher_id;
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
                $sql .= " a.voucher_id LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }

    if (isset($r->searchdata->agreement_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->agreement_id;
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
                $sql .= " a.agreement_id LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }

    }

    if (isset($r->searchdata->contributions_id))
    {
        $first = "Yes";
        $new_data = $r->searchdata->contributions_id;
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
                $sql .= " a.contributions_id LIKE '%".$value."%' ";
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
    
    if (isset($r->searchdata->subteams))
    {
        $first = "Yes";
        $new_data = $r->searchdata->subteams;
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
                $sql .= " a.subteams LIKE '%".$value."%' ";
            }
            if ($first=='No')
            {
                $sql .= ") ";
            
            }
        }
    }

    if (isset($r->searchdata->transaction_date_from))
    {
        $transaction_date_from = $r->searchdata->transaction_date_from;
        $ttransaction_date_from = substr($transaction_date_from,6,4)."-".substr($transaction_date_from,3,2)."-".substr($transaction_date_from,0,2);
        if (isset($r->searchdata->transaction_date_to))
        {
            $transaction_date_to = $r->searchdata->transaction_date_to;
            $ttransaction_date_to = substr($transaction_date_to,6,4)."-".substr($transaction_date_to,3,2)."-".substr($transaction_date_to,0,2);
        }
        $sql .= " and a.transaction_date BETWEEN '".$ttransaction_date_from."' AND '".$ttransaction_date_to."' ";
    }
    $searchsql .=  $sql .  " ORDER BY a.transaction_date DESC";
    
    $voucherlist = $db->getAllRecords($searchsql);
    echo json_encode($voucherlist);
});

$app->get('/getdatavalues_voucher/:field_name', function($field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM voucher  ORDER BY $field_name";
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});

$app->get('/getcontributionids/:agreement_id', function($agreement_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT a.agreement_details_id, CONCAT('CONTRIBUTIONID_',a.agreement_details_id) as contributions_title, b.teams, b.assign_to FROM agreement_details as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id WHERE a.agreement_id=".$agreement_id." and a.payment_approved='Yes'";
    $contributions = $db->getAllRecords($sql);
    echo json_encode($contributions);
    
});

$app->get('/getemployeeids/:agreement_details_id', function($agreement_details_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT b.user_id,CONCAT(c.salu,' ',c.fname,' ',c.lname) as name from agreement_details as a LEFT JOIN users as b ON a.contribution_to = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id where a.agreement_details_id=".$agreement_details_id;
    $employees = $db->getAllRecords($sql);
    echo json_encode($employees);
    
});

$app->get('/getvoucheramount/:agreement_details_id', function($agreement_details_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT contribution_amount from agreement_details as a WHERE a.agreement_details_id=".$agreement_details_id;
    $voucher_amount = $db->getAllRecords($sql);
    echo json_encode($voucher_amount);
    
});


?>
