<?php 


$app->get('/gettransactionsdata/:tr_start_date/:tr_end_date', function($tr_start_date,$tr_end_date) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $teams = $session['teams'];
    $team_query = " ";
    $team_query_b = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
        $team_query_b .= " OR b.teams LIKE '".$value."' ";
    }

    $user_id = $session['user_id'];
    /*$this_month = date('m');

    if ($selectedmonth>0 || $selectedmonth=='All')
    {
        $this_month = $selectedmonth;
    }*/
    $tsql = "";
    $transactions = 0;
    
    $sql = "SELECT sum(a.our_brokerage) as transactions FROM agreement as a where (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.") ";
    if ($selectedmonth=='All')
    {
        
    }
    else{
        $sql .= " and a.deal_date BETWEEN '$tr_start_date' and  '$tr_end_date' " ;
    }
    $transactionsdata = $db->getAllRecords($sql);

    $tsql .= $sql;
    if ($transactionsdata)
    {
         $transactions = $transactionsdata[0]['transactions'];

    }
    
    $transactions_commercial=0;
    $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.")  ";

    if ($selectedmonth=='All')
    {
        
    }
    else{
        $sql .= " and a.deal_date BETWEEN '$tr_start_date' and  '$tr_end_date' " ;
    }
        // error_log($sql, 3, "logfile1.log");
    $transactionsdata = $db->getAllRecords($sql);
    $tsql .= $sql;
    if ($transactionsdata)
    {
         $transactions_commercial = $transactionsdata[0]['transactions_commercial'];

    }
    $transactions_retail=0;
    $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.")   ";

    if ($selectedmonth=='All')
    {
        
    }
    else{
        $sql .= " and a.deal_date BETWEEN '$tr_start_date' and  '$tr_end_date' " ;
    }

    $transactionsdata = $db->getAllRecords($sql);
    $tsql .= $sql;
    if ($transactionsdata)
    {
         $transactions_retail = $transactionsdata[0]['transactions_retail'];

    }

    $transactions_residential=0;
    //$temp_sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.") and month(a.created_date) = $this_month ";

    $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.")  ";

    if ($selectedmonth=='All')
    {
        
    }
    else{
        $sql .= " and a.deal_date BETWEEN '$tr_start_date' and  '$tr_end_date' " ;
    }
    $transactionsdata = $db->getAllRecords($sql);
    $tsql .= $sql;
    if ($transactionsdata)
    {
         $transactions_residential = $transactionsdata[0]['transactions_residential'];

    }
    $transactions_preleased=0;
    $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.")   ";

    if ($selectedmonth=='All')
    {
        
    }
    else{
        $sql .= " and a.deal_date BETWEEN '$tr_start_date' and  '$tr_end_date' " ;
    }
    $transactionsdata = $db->getAllRecords($sql);
    $tsql .= $sql;
    if ($transactionsdata)
    {
         $transactions_preleased = $transactionsdata[0]['transactions_preleased'];


    }
    
    $brokerage_received=0;
    $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where (b.created_by = $user_id or b.modified_by = $user_id or FIND_IN_SET($user_id , b.assign_to) ".$team_query_b.")   ";

    if ($selectedmonth=='All')
    {
        
    }
    else{
        $sql .= " and b.deal_date BETWEEN '$tr_start_date' and  '$tr_end_date' " ;
    }
    $transactionsdata = $db->getAllRecords($sql );
    $tsql .= $sql;
    if ($transactionsdata)
    {
         $brokerage_received = $transactionsdata[0]['brokerage_received'];

    }

    $brokerage_invoiced=0;
    /*$transactionsdata = $db->getAllRecords("SELECT sum(our_brokerage) as brokerage_invoiced FROM agreement where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET($user_id , assign_to) ".$team_query.") and month(created_date) = $this_month " );
    if ($transactionsdata)
    {
         $brokerage_invoiced = $transactionsdata[0]['brokerage_invoiced'];

    }*/
    
    $htmldata['transactions']=$transactions;
    $htmldata['transactions_commercial']=$transactions_commercial;
    $htmldata['transactions_retail']=$transactions_retail;
    $htmldata['transactions_residential']=$transactions_residential;
    $htmldata['transactions_preleased']=$transactions_preleased;
    $htmldata['brokerage_received']=$brokerage_received;
    $htmldata['brokerage_invoiced']=$brokerage_invoiced;
    $htmldata['brokerage_expected']=($transactions-$brokerage_received);
    $htmldata['$tsql'] = $tsql;
    //$htmldata['temp_sql']=$temp_sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/getquarter_data', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $user_id = $session['user_id'];
    $start_quarter = "2021-09-01 00:00:00";
    $end_quarter = "2021-11-30 23:59:59";
    
    $mainsql = "SELECT * from goals as a WHERE a.user_id = $user_id and a.goal_sub_category = 'Transaction' ";
    $stmt = $db->getRows($mainsql);
    $goals_to_achieve = 0;
    $goals_achieved = 0;
    $goal_per_to_get = 0;
    $target_achieved = 0;
    $eligibility ="No";
    if($stmt->num_rows > 0)
    {
        $i = 1;
        while($row = $stmt->fetch_assoc())
        {
            $goal_per_to_get = $row['goal_per'];
            $goals_to_achieve = $row['goal_sep']+$row['goal_oct']+$row['goal_nov'];
        }
    }
    if ($user_id==64)
    {
        $goal_per_to_get = 15;
        $goals_to_achieve = 120000;
    }
    
    $sql = "SELECT sum(a.our_brokerage) as goals_achieved FROM agreement as a where (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to)) and a.deal_date BETWEEN '$start_quarter' AND '$end_quarter'  " ;
    $goals_achieveddata = $db->getAllRecords($sql);
    if ($goals_achieveddata)
    {
        $goals_achieved = $goals_achieveddata[0]['goals_achieved'];
    }

    if ($goals_achieved>0 && $goals_to_achieve >0)
    {
        $goal_percent = round((($goals_achieved / $goals_to_achieve)*100),2);
        $target_achieved = round((($goal_per_to_get*$goal_percent)/100),2);
        if ($target_achieved>=$goal_per_to_get)
        {
            $eligibility="Yes";
        }
        
    }

    $htmldata['eligibility']=$eligibility;
    $htmldata['goal_per_to_get']=$goal_per_to_get;
    $htmldata['goal_percent']=$goal_percent;
    $htmldata['goals_achieved']=$goals_achieved;
    $htmldata['goals_to_achieve']=$goals_to_achieve;
    $htmldata['target_achieved']=$target_achieved;

    $htmldata['temp_sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/getemployee_record/:user_id', function($user_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $sql = ""; 
    if ($user_id>0)
    {

    }
    else
    {
        $user_id = $session['user_id'];
    }

    $leavesql = "SELECT leave_allowed,leave_opening FROM employee as a LEFT JOIN users as b ON b.emp_id = a.emp_id WHERE b.user_id = $user_id ";

    $leavedata = $db->getAllRecords($leavesql);
    $leave_allowed = 0;
    $leave_opening = 0;
    if ($leavedata)
    {
        $leave_allowed = $leavedata[0]['leave_allowed'];
        $leave_opening = $leavedata[0]['leave_opening'];
    }

    // $mainsql = "SELECT *,ROUND(days,2) as days FROM employee_leave as a LEFT JOIN users as b ON b.emp_id = a.emp_id WHERE b.user_id = $user_id and a.leave_status = 'Approved' ";
      $mainsql = "SELECT *,ROUND(days,2) as days FROM employee_leave as a LEFT JOIN users as b ON b.emp_id = a.emp_id WHERE b.user_id = $user_id and a.leave_status = 'Approved' and a.leave_date_from >= '2023-01-01'  ";

    $stmt = $db->getRows($mainsql);
    $leave_taken = 0;
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $leave_taken = $leave_taken + $row['days'];
            
        }
    }
    $leave_balance = $leave_allowed + $leave_opening - $leave_taken;
    $sql .= $mainsql;
    $assets_issued = "";
    $mainsql = "SELECT * FROM employee_asset as a LEFT JOIN users as b ON b.emp_id = a.emp_id WHERE b.user_id = $user_id ";

    $stmt = $db->getRows($mainsql);
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $assets_issued = $assets_issued .$row['asset_title'].',';
        }
    }
    $sql .= $mainsql;
    $htmldata['leave_balance']=$leave_balance;
    $htmldata['assets_issued']=$assets_issued;
    $htmldata['temp_sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});




$app->get('/getdashboardgraph', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $teams = $session['teams'];
    $team_query = " ";
    $team_query_b = " ";   
    $tteams = $session['teams'];
    foreach($tteams as $value)
    {
        $team_query .= " OR a.teams LIKE '".$value."' ";
        $team_query_b .= " OR b.teams LIKE '".$value."' ";
    }

    $user_id = $session['user_id'];
    $transactions = 0;
    
    $sql = "SELECT sum(a.our_brokerage) as brokerage,month(deal_date) as month_no FROM agreement as a where (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.") and year(a.deal_date) = 2022 GROUP BY month(a.deal_date)";
    $stmt = $db->getRows($sql);
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
    $month_dis_amt = array();
    $month_dis_amt[1] = 0;
    $month_dis_amt[2] = 0;
    $month_dis_amt[3] = 0;
    $month_dis_amt[4] = 0;
    $month_dis_amt[5] = 0; 
    $month_dis_amt[6] = 0;
    $month_dis_amt[7] = 0;
    $month_dis_amt[8] = 0;
    $month_dis_amt[9] = 0;
    $month_dis_amt[10] = 0;
    $month_dis_amt[11] = 0; 
    $month_dis_amt[12] = 0;
    $datastr = array();
    

    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
           $month_dis_amt[$row['month_no']]= $row['brokerage'];
           array_push($datastr,$row['brokerage']);
        }
    }
    
    /*$transactions_commercial=0;
    $sql = "SELECT sum(a.our_brokerage) as transactions_commercial FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='commercial' and (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.")";

    if ($selectedmonth=='All')
    {
        
    }
    else{
        $sql .= " and month(a.deal_date) = $this_month " ;
    }

    $transactionsdata = $db->getAllRecords($sql);
    if ($transactionsdata)
    {
         $transactions_commercial = $transactionsdata[0]['transactions_commercial'];

    }
    $transactions_retail=0;
    $sql = "SELECT sum(a.our_brokerage) as transactions_retail FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='retail' and (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.")";

    if ($selectedmonth=='All')
    {
        
    }
    else{
        $sql .= " and month(a.deal_date) = $this_month " ;
    }

    $transactionsdata = $db->getAllRecords($sql);
    if ($transactionsdata)
    {
         $transactions_retail = $transactionsdata[0]['transactions_retail'];

    }

    $transactions_residential=0;
    //$temp_sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.") and month(a.created_date) = $this_month ";

    $sql = "SELECT sum(a.our_brokerage) as transactions_residential FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='residential' and (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.") ";

    if ($selectedmonth=='All')
    {
        
    }
    else{
        $sql .= " and month(a.deal_date) = $this_month " ;
    }
    $transactionsdata = $db->getAllRecords($sql);
    if ($transactionsdata)
    {
         $transactions_residential = $transactionsdata[0]['transactions_residential'];

    }
    $transactions_preleased=0;
    $sql = "SELECT sum(a.our_brokerage) as transactions_preleased FROM agreement as a LEFT JOIN property as b ON a.property_id = b.property_id where b.proptype='pre-leased' and (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to) ".$team_query.") ";

    if ($selectedmonth=='All')
    {
        
    }
    else{
        $sql .= " and month(a.deal_date) = $this_month " ;
    }
    $transactionsdata = $db->getAllRecords($sql);
    if ($transactionsdata)
    {
         $transactions_preleased = $transactionsdata[0]['transactions_preleased'];


    }
    
    $brokerage_received=0;
    $sql = "SELECT sum(amount) as brokerage_received FROM account as a LEFT JOIN agreement as b ON a.agreement_id = b.agreement_id where (b.created_by = $user_id or b.modified_by = $user_id or FIND_IN_SET($user_id , b.assign_to) ".$team_query_b.") ";

    if ($selectedmonth=='All')
    {
        
    }
    else{
        $sql .= " and month(a.deal_date) = $this_month " ;
    }
    $transactionsdata = $db->getAllRecords($sql );
    if ($transactionsdata)
    {
         $brokerage_received = $transactionsdata[0]['brokerage_received'];

    }

    $brokerage_invoiced=0;
    /*$transactionsdata = $db->getAllRecords("SELECT sum(our_brokerage) as brokerage_invoiced FROM agreement where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET($user_id , assign_to) ".$team_query.") and month(created_date) = $this_month " );
    if ($transactionsdata)
    {
         $brokerage_invoiced = $transactionsdata[0]['brokerage_invoiced'];

    }*/
    
    $htmldata['data']=$month_dis_amt;
    $htmldata['datastr']=$datastr;
    /*$htmldata['transactions_commercial']=$transactions_commercial;
    $htmldata['transactions_retail']=$transactions_retail;
    $htmldata['transactions_residential']=$transactions_residential;
    $htmldata['transactions_preleased']=$transactions_preleased;
    $htmldata['brokerage_received']=$brokerage_received;
    $htmldata['brokerage_invoiced']=$brokerage_invoiced;
    $htmldata['brokerage_expected']=($transactions-$brokerage_received);*/
    //$htmldata['temp_sql']=$temp_sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

?>