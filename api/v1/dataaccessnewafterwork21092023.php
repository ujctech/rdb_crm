<?php  
$app->get('/onemailer1/:module_name/:id/:data', function($module_name,$id,$data) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")  
    {  
        return;
    }
    $senddata =  array(); 
    $htmldata = array();

    if($module_name == 'property')
    {
        $sql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in($data) GROUP BY a.property_id ORDER BY CAST(a.property_id as UNSIGNED) DESC LIMIT 1";
        
        $stmt = $db->getRows($sql);
         
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            { 

                $page_1 =   '<div id="page_1" style="width:978px;height:728px;margin:20px;">
                                <div style="width:968px; height:718px;background-color:#ffffff;">
                                    <div style="text-align:center;height:95px;">
                                        <div style="float:right;">
                                            <img src="dist/img/sqftlogo1.jpg" style="width:95px; height:95px;"/>
                                        </div>
                                    </div>
                                    <div style="text-align:center;">
                                        <div style="width: 100%;font-size: 45px;">Available on <span style="color:#ff0000;">';
                                            if ($row['property_for']=='Sale')
                                            {
                                                $page_1 .= 'Outright';
                                            }

                                            if ($row['property_for']=='Rent/Lease')
                                            {
                                                $page_1 .= 'Lease';
                                            }
                                            $page_1 .= '</span>
                                        </div>
                                    </div>
                                    <div style="text-align:left;margin-top:40px;margin-left:90px;font-size:22px;font-family:Helvetica;font-weight:bold;">
                                        <p><span><img src="dist/img/mailer_home.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['project_name'].' '.$row['building_name'].'</span><span>
                                        '.$row['propsubtype'].'</span></p>
                                        <p><span><img src="dist/img/mailer_area.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['carp_area'].' sqft Carpet</span></p>
                                        <p><span><img src="dist/img/mailer_parking.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['car_park'].' Parking</span></p>
                                        <p><span><img src="dist/img/mailer_location.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['locality'].',
                                        <span>'.$row['area_name'].', </span>
                                        <span>'.$row['city'].'</span></p>
                                        <p><span><img src="dist/img/mailer_rupee.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">Price '.$db->ShowAmount($row['exp_price'],$row['exp_price_para']).' '.$row['exp_price_para'].' (Negotiable)</span></p>
                                    </div>
                                    <div style="width:100%;margin-top:49px;height:125px;background-color:#dc0000;text-align:center;color:#ffffff;font-size:24px;">
                                        <p style="margin:0px;" >Maha Rera No: A51800000818</p>
                                        <p style="font-weight:bold;margin:0px;">Yash Hinduja - 8879584685 / Pravin Agrawal - 9321920381</p>
                                        <p style="margin:0px;">yash@sqftindia.co.in / andheriresidential.sqftindia.co.in</p>
                                        <p style="margin:0px;font-size:14px;margin-left:10px;"><span style="float:left;">www.sqft.co.in</span><span style="float:right;margin-right:10px;"><i>an unbiased broking house</i><sup>TM</sup></span></p>
                                    </div>
                                </div>
                            </div>';

                $htmldata['page_1']=$page_1;
            }
        }
    }

    if($module_name == 'project')
    {
        $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE a.project_id in($data) GROUP BY a.project_id ORDER BY CAST(a.project_id as UNSIGNED) DESC";
        
        $stmt = $db->getRows($sql);
        
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            { 

                $page_1 =   '<div id="page_1" style="width:978px;height:728px;margin:20px;">
                                <div style="width:968px; height:718px;background-color:#ffffff;">
                                    <div style="text-align:center;height:95px;">
                                        <div style="float:right;">
                                            <img src="dist/img/sqftlogo1.jpg" style="width:95px; height:95px;"/>
                                        </div>
                                    </div>
                                    <div style="text-align:center;">
                                        <div style="width: 100%;font-size: 45px;">Available on <span style="color:#ff0000;">';
                                            if ($row['project_for']=='Sale')
                                            {
                                                $page_1 .= 'Outright';
                                            }

                                            if ($row['project_for']=='Rent')
                                            {
                                                $page_1 .= 'Lease';
                                            }
                                            $page_1 .= '</span>
                                        </div>
                                    </div>
                                    <div style="text-align:left;margin-top:40px;margin-left:90px;font-size:22px;font-family:Helvetica;font-weight:bold;">
                                        <p><span><img src="dist/img/mailer_home.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['project_name'].'</span></p>
                                        <p><span><img src="dist/img/mailer_area.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['tot_area'].' sqft Carpet</span></p>
                                        <p><span><img src="dist/img/mailer_parking.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['car_park'].' '.$row['parking'].'</span></p>
                                        <p><span><img src="dist/img/mailer_location.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['locality'].',
                                        <span>'.$row['area_name'].', </span>
                                        <span>'.$row['city'].'</span></p>
                                        <p><span><img src="dist/img/mailer_rupee.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">Price '.$row['pack_price'].' (Negotiable)</span></p>
                                    </div>
                                    <div style="width:100%;margin-top:49px;height:125px;background-color:#dc0000;text-align:center;color:#ffffff;font-size:24px;">
                                        <p style="margin:0px;" >Maha Rera No: A51800000818</p>
                                        <p style="font-weight:bold;margin:0px;">Yash Hinduja - 8879584685 / Pravin Agrawal - 9321920381</p>
                                        <p style="margin:0px;">yash@sqftindia.co.in / andheriresidential.sqftindia.co.in</p>
                                        <p style="margin:0px;font-size:14px;margin-left:10px;"><span style="float:left;">www.sqft.co.in</span><span style="float:right;margin-right:10px;"><i>an unbiased broking house</i><sup>TM</sup></span></p>
                                    </div>
                                </div>
                            </div>';

                $htmldata['page_1']=$page_1;
            }
        }
    }


    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/export_onemailer/:module_name/:id/:data/:report_type', function($module_name,$id,$data,$report_type) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $htmlstring = '';
    $filename = $module_name."_list.".$report_type;
    $page_1 = "";
    if($module_name == 'property')
    {
        $sql = "SELECT *,a.exp_price,a.rera_num, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id in($data) GROUP BY a.property_id ORDER BY CAST(a.property_id as UNSIGNED) DESC LIMIT 1";
        
        $stmt = $db->getRows($sql);
        
        if($stmt->num_rows > 0)
        {
            while($row = $stmt->fetch_assoc())
            { 

                $page_1 =   '<div id="page_1" style="width:978px;height:728px;margin:20px;">
                                <div style="width:968px; height:718px;background-color:#ffffff;">
                                    <div style="text-align:center;height:95px;">
                                        <div style="float:right;">
                                            <img src="dist/img/sqftlogo1.jpg" style="width:95px; height:95px;"/>
                                        </div>
                                    </div>
                                    <div style="text-align:center;">
                                        <div style="width: 100%;font-size: 45px;">Available on <span style="color:#ff0000;">';
                                            if ($row['property_for']=='Sale')
                                            {
                                                $page_1 .= 'Outright';
                                            }

                                            if ($row['property_for']=='Rent/Lease')
                                            {
                                                $page_1 .= 'Lease';
                                            }
                                            $page_1 .= '</span>
                                        </div>
                                    </div>
                                    <div style="text-align:left;margin-top:40px;margin-left:90px;font-size:22px;font-family:Helvetica;font-weight:bold;">
                                        <p><span><img src="dist/img/mailer_home.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['project_name'].' '.$row['building_name'].'</span><span>
                                        '.$row['propsubtype'].'</span></p>
                                        <p><span><img src="dist/img/mailer_area.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['carp_area'].' sqft Carpet</span></p>
                                        <p><span><img src="dist/img/mailer_parking.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['car_park'].' Parking</span></p>
                                        <p><span><img src="dist/img/mailer_location.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">'.$row['locality'].',
                                        <span>'.$row['area_name'].', </span>
                                        <span>'.$row['city'].'</span></p>
                                        <p><span><img src="dist/img/mailer_rupee.png" style="width:68px; height:61px;"></span><span style="margin-left:20px;">Price '.$db->ShowAmount($row['exp_price'],$row['exp_price_para']).' '.$row['exp_price_para'].' (Negotiable)</span></p>
                                    </div>
                                    <div style="width:100%;margin-top:49px;height:125px;background-color:#dc0000;text-align:center;color:#ffffff;font-size:24px;">
                                        <p style="margin:0px;" >Maha Rera No: '.$row['rera_num'].'</p>
                                        <p style="font-weight:bold;margin:0px;">Yash Hinduja - 8879584685 / Pravin Agrawal - 9321920381</p>
                                        <p style="margin:0px;">yash@sqftindia.co.in / andheriresidential.sqftindia.co.in</p>
                                        <p style="margin:0px;font-size:14px;margin-left:10px;"><span style="float:left;">www.sqft.co.in</span><span style="float:right;margin-right:10px;"><i>an unbiased broking house</i><sup>TM</sup></span></p>
                                    </div>
                                </div>
                            </div>';

                $htmldata['page_1']=$page_1;
            }
        }
    }

    if ($report_type=="pdf")
    {

        $htmlstring = '<table><tr><td style="width:100%;">'.$page_1.'</td></tr></table>';
        
        $htmlstring = $page_1;

        
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
        require_once 'ppt_onemailer.php';

        $ppt = new ppt();
        $a = $ppt->create_ppt($module_name,$id,$data,$filename);
    }

    $htmldata['htmlstring']='Done';
    $htmldata['htmlstring1']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});



$app->get('/scheduled_visits/:activity_id', function($activity_id) use ($app) {
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
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Property ID</th>
                                        <th>Property</th>
                                        <th>Description</th>
                                        <th>Visited On</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>';

    $sql = "SELECT *,c.dev_owner_id as contact_id, DATE_FORMAT(a.start_date,'%d/%m/%Y %H:%i') AS start_date, DATE_FORMAT(a.end_date,'%d/%m/%Y %H:%i') AS end_date, DATE_FORMAT(a.visited_on,'%d/%m/%Y %H:%i') AS visited_on from activity_details as a LEFT JOIN activity as b on a.activity_id = b.activity_id LEFT JOIN property as c on c.property_id = a.property_id where a.activity_id = $activity_id";

    $stmt = $db->getRows($sql);

    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<tr>
				                <td>'.$row['start_date'].'</td>
                                <td>'.$row['end_date'].'</td>
                                <td><a href="#/properties_edit/'.$row['property_id'].'">'.$row['proptype'].'_'.$row['property_id'].'</a></td>
                                <td>'.$row['project_name'].' '.$row['propsubtype'].' '.$row['property_for'].'</td>
                                <td>'.$row['description'].'</td>
                                <td>'.$row['visited_on'].'</td>
                                <td>'.$row['status'].'</td>
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


// ALERTS

$app->get('/mdbattendance', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    $emp_id = $session['emp_id'];
    if ($session['username']=="Guest")
    {
        return;
    }
    $dbattendance = $db->getMDbAttendance();


    $db = new DbHandler();
    $senddata =  array();
    $htmldata = array();
    
    $session = $db->getSession();
    $emp_id = $session['emp_id'];
    $bu_id = $session['bu_id'];
    $role = $session['role'];
    
    $query = "SELECT *, DATE_FORMAT(b.date,'%d/%m/%Y') as date FROM employee as a LEFT JOIN attendance as b on a.emp_id = b.emp_id WHERE superior_emp_id=$emp_id and b.short_code='P' order by b.date DESC, b.in_time DESC LIMIT 5";
    if ($role=='bu_head' || $emp_id == 767)
    {
        $query = "SELECT *, DATE_FORMAT(b.date,'%d/%m/%Y') as date FROM employee as a LEFT JOIN attendance as b on a.emp_id = b.emp_id WHERE a.bu_id = $bu_id and b.short_code='P' order by b.date DESC, b.in_time DESC LIMIT 5";
        
    }
    $htmlstring = '';
    $show = 0;
    $stmt = $this->conn->query($query);
    
    //$htmlstring .= '<div class="row" style="margin-left:0px;"><div class="col-md-3">Date</div><div class="col-md-1">Code</div><div class="col-md-2">In Time</div><div class="col-md-2">Out Time</div><div class="col-md-2">Hours</div>
    $htmlstring .='<ul style="padding:3px;border: 1px solid #e8e7e7;">';
    $htmlstring .= '<li style="overflow:hidden;"><div class="row" style="background-color: #909090;color: #fff;"><div class="col-md-4">Name</div><div class="col-md-3">Date</div><div class="col-md-2">In</div><div class="col-md-2">Out</div></div></li>';
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        { 
            $show = 1;
            $htmlstring .= '<li style="overflow:hidden;" title="Click to view full attendance of this employee" ng-click="showatt('.$row['emp_id'].')"  class="lihover"><div class="row"><div class="col-md-4" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">'.$row['name'].' </div><div class="col-md-3">'.$row['date'].' </div><div class="col-md-2" >'.$row['in_time'].'</div><div class="col-md-2" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">'.$row['out_time'].'</div></div></li>';
        }
    }
    $htmlstring .= '</ul>';


    echo json_encode($dbattendance);
});

$app->get('/detmdbnearbirthday/:start_date/:end_date', function($start_date,$end_date) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    $emp_id = $session['emp_id'];
    if ($session['username']=="Guest")
    {
        return;
    }
    $dbattendance = $db->getDetMDbAttendance($start_date,$end_date);
    $senddata =  array();
    $htmldata = array();
    
    $session = $db->getSession();
    $emp_id = $session['emp_id'];
    $bu_id = $session['bu_id'];
    $role = $session['role'];
    if ($role=='bu_head' || $emp_id == 767)        
    {
        $data = $db->getAllRecords("SELECT *, DATE_FORMAT(b.date,'%d/%m/%Y') as tdate FROM employee as a LEFT JOIN attendance as b on a.emp_id = b.emp_id WHERE a.bu_id = $bu_id order by b.date DESC, a.name LIMIT 1");
    }
    else
    {
        $data = $db->getAllRecords("SELECT *, DATE_FORMAT(b.date,'%d/%m/%Y') as tdate FROM employee as a LEFT JOIN attendance as b on a.emp_id = b.emp_id WHERE superior_emp_id=$emp_id order by b.date DESC, a.name LIMIT 1");
    }
    
    $s_date='0000-00-00';
    $e_date='0000-00-00';
    $ts_date = "00/00/0000";
    $te_date = "00/00/0000";
    if ($data)
    {
        $s_date = $data[0]['date'];
        $ts_date = $data[0]['tdate'];
        $e_date = $data[0]['date'];
        $te_date = $data[0]['tdate'];
    }
    if ($role=='bu_head'  || $emp_id == 767)
    {
       $query = "SELECT *, DATE_FORMAT(b.date,'%d/%m/%Y') as date FROM employee as a LEFT JOIN attendance as b on a.emp_id = b.emp_id WHERE a.bu_id=$bu_id and b.date BETWEEN '$s_date' and '$e_date' ORDER BY b.date DESC, a.name"; 
    }
    else
    {
        $query = "SELECT *, DATE_FORMAT(b.date,'%d/%m/%Y') as date FROM employee as a LEFT JOIN attendance as b on a.emp_id = b.emp_id WHERE superior_emp_id=$emp_id and b.date BETWEEN '$s_date' and '$e_date' ORDER BY b.date DESC, a.name";
    }
    if ($start_date=="0000-00-00" and $end_date=='0000-00-00')
    {
        $start_date = $ts_date;
        $end_date = $te_date;
    }
    else
    {
        if ($role=='bu_head'  || $emp_id == 767 )
        {
            $query = "SELECT *, DATE_FORMAT(b.date,'%d/%m/%Y') as date FROM employee as a LEFT JOIN attendance as b on a.emp_id = b.emp_id WHERE a.bu_id=$bu_id and b.date BETWEEN '$start_date' and '$end_date' order by b.date DESC, a.name"; 
        }
        else
        {
            $query = "SELECT *, DATE_FORMAT(b.date,'%d/%m/%Y') as date FROM employee as a LEFT JOIN attendance as b on a.emp_id = b.emp_id WHERE superior_emp_id=$emp_id and b.date BETWEEN '$start_date' and '$end_date' order by b.date DESC, a.name"; 
        }
        $ts_date = substr($start_date,8,2)."/".substr($start_date,5,2)."/".substr($start_date,0,4);
        $te_date = substr($end_date,8,2)."/".substr($end_date,5,2)."/".substr($end_date,0,4);
    }
    
    $htmlstring = '';
    $show = 0;
    $stmt = $this->conn->query($query);
    $htmlstring .='<div class="row"><div class="col-md-3"><label for="name">Start Date:</label><div class="input-group date" id="start_date" date-directive="">
                            <input class="form-control" type="text" ng-model="start_date" value="'.$ts_date.'" placeholder="Date" required>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div></div><div class="col-md-3"><label for="name">End Date:</label><div class="input-group date" id="end_date" date-directive="">
                            <input class="form-control" type="text" ng-model="end_date" value="'.$te_date.'" placeholder="Date" required>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div></div><div class="col-md-3">
            <a class="btn btn-primary" href="javascript:void(0)" ng-click="detattendance()" data-backdrop="false" style="float:left;margin-top:22px;" >Show</a>
            <a href="javascript:void(0)"><img src="img/excel.png" style="width:35px;height:35px;margin:13px;margin-top:22px;float:left;" alt="Export to Excel" title="Export to Excel" ng-click="detattendance_excel()"></a>
        </div></div>';
    $htmlstring .='<ul style="padding:3px;border: 1px solid #e8e7e7;">';
    $htmlstring .= '<li style="overflow:hidden;"><div class="row" style="background-color: #909090;color: #fff;"><div class="col-md-3">Name</div><div class="col-md-2">Date</div><div class="col-md-1">Short Code</div><div class="col-md-2">In</div><div class="col-md-2">Out</div><div class="col-md-2">Hrs Worked</div></div></li>';
    
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        { 
            $show = 1;
            $htmlstring .= '<li style="overflow:hidden;" title="Click to view full attendance of this employee" ng-click="showatt('.$row['emp_id'].')" class="lihover"><div class="row"><div class="col-md-3">'.$row['name'].' </div><div class="col-md-2">'.$row['date'].'</div><div class="col-md-1">'.$row['short_code'].'</div><div class="col-md-2">'.$row['in_time'].'</div><div class="col-md-2">'.$row['out_time'].'</div><div class="col-md-2">'.$row['hrs_worked'].'</div></div></li>';
        }
    }
    $htmlstring .= '</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['show']=$show;
    $htmldata['start_date']=$ts_date;
    $htmldata['end_date']=$te_date;
    $senddata[]=$htmldata; 

    echo json_encode($dbattendance);
});

$app->get('/mdbnearbirthday', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $htmlstring ='<ul style="padding:3px;border: 1px solid #e8e7e7;">';
    $htmlstring .= '<li style="overflow:hidden;"><div class="row" style="background-color: #909090;color: #fff;"><div class="col-md-6">Name</div><div class="col-md-6">Date</div></div></li>';


    $sql = "SELECT CONCAT(fname,' ',lname) as name, DATE_FORMAT(dob, '%d-%m') AS birthday FROM employee WHERE DATE_FORMAT(dob, '%m-%d') BETWEEN DATE_FORMAT( curdate(), '%m-%d') AND DATE_FORMAT( curdate() + INTERVAL 30 DAY, '%m-%d') AND status = 'Active' ORDER BY birthday ";

    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<li style="overflow:hidden;" title="Click to view Contact" class="lihover"><div class="row"><div class="col-md-6" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">'.$row['name'].' </div><div class="col-md-6">'.$row['birthday'].' </div></div></li>';
        }
    }
    $htmlstring .= '</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

// $app->get('/mdbleaves', function() use ($app) {
//     $sql  = "";
//     $db = new DbHandler();
//     $session = $db->getSession();
//     if ($session['username']=="Guest")
//     {
//         return;
//     }
//     $senddata =  array();
//     $htmldata = array();
//     $user_id = $session['user_id'];
//     $emp_id = 0;
//     $user = $db->getOneRecord("select emp_id from users where user_id= $user_id ");
//     if ($user != NULL) 
//     {
//         $emp_id = $user['emp_id'];
//     }

//     $htmlstring ='<ul style="padding:3px;border: 1px solid #e8e7e7;">';
//     $htmlstring .= '<li style="overflow:hidden;"><div class="row" style="background-color: #909090;color: #fff;"><div class="col-md-4">Name</div><div class="col-md-2">From Date</div><div class="col-md-2">To Date</div><div class="col-md-1">Days</div><div class="col-md-3">Reason</div></div></li>';

//     $sql = "SELECT CONCAT(c.fname,' ',c.lname) as name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to,a.days,a.leave_reason FROM employee_leave as a LEFT JOIN users as b ON b.emp_id = a.emp_id LEFT JOIN employee as c ON a.emp_id = c.emp_id WHERE b.user_id = $user_id and c.status = 'Active'  and a.leave_date >= CURDATE() ORDER BY a.leave_date_from  ";
//     if ($emp_id>0)
//     {
//         $sql = "SELECT CONCAT(c.fname,' ',c.lname) as name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to,a.days FROM employee_leave as a LEFT JOIN users as b ON b.emp_id = a.emp_id LEFT JOIN employee as c ON a.emp_id = c.emp_id WHERE (b.user_id = $user_id or c.manager_id = $emp_id ) and c.status = 'Active' and a.leave_status = 'Approved' and a.leave_date_from >= CURDATE() ORDER BY a.leave_date_from  ";
//     }

//     $stmt = $db->getRows($sql);
    
//     if($stmt->num_rows > 0)
//     {
//         while($row = $stmt->fetch_assoc())
//         {
//             $htmlstring .= '<li style="overflow:hidden;color:green;" title="Click to view Contact" class="lihover"><div class="row"><div class="col-md-4" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">'.$row['name'].' </div><div class="col-md-2">'.$row['leave_date_from'].' </div><div class="col-md-2">'.$row['leave_date_to'].' </div><div class="col-md-1">'.$row['days'].' </div><div class="col-md-3">'.$row['leave_reason'].' </div></div></li>';
//         }
//     }
//     $htmlstring .= '</ul>';
//     $htmldata['htmlstring']=$htmlstring;
//     $htmldata['sql']=$sql;
//     $senddata[]=$htmldata;
//     echo json_encode($senddata);
// });

$app->get('/mdbleaves', function() use ($app) {
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
    $role = $session['role'];
    $emp_id = 0;
    $user = $db->getOneRecord("select emp_id from users where user_id= $user_id ");
    if ($user != NULL) 
    {
        $emp_id = $user['emp_id'];
    }

    $htmlstring ='<ul style="padding:3px;border: 1px solid #e8e7e7;">';
    $htmlstring .= '<li style="overflow:hidden;"><div class="row" style="background-color: #909090;color: #fff;"><div class="col-md-4">Name</div><div class="col-md-2">From Date</div><div class="col-md-2">To Date</div><div class="col-md-1">Days</div><div class="col-md-3">Reason</div></div></li>';

    $sql = "SELECT CONCAT(c.fname,' ',c.lname) as name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to,a.days,a.leave_reason FROM employee_leave as a LEFT JOIN users as b ON b.emp_id = a.emp_id LEFT JOIN employee as c ON a.emp_id = c.emp_id WHERE b.user_id = $user_id and c.status = 'Active'  and a.leave_date >= CURDATE() ORDER BY a.leave_date_from  ";
    if ($emp_id>0)
    {
        $sql = "SELECT CONCAT(c.fname,' ',c.lname) as name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to,a.days,a.leave_reason FROM employee_leave as a LEFT JOIN users as b ON b.emp_id = a.emp_id LEFT JOIN employee as c ON a.emp_id = c.emp_id WHERE (b.user_id = $user_id or c.manager_id = $emp_id ) and c.status = 'Active' and a.leave_status = 'Approved' and a.leave_date_from >= CURDATE() ORDER BY a.leave_date_from  ";
    }
    
    if ( (in_array("Admin", $role)) )
    {
    $sql = "SELECT CONCAT(c.fname,' ',c.lname) as name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to,a.days,a.leave_reason FROM employee_leave as a LEFT JOIN users as b ON b.emp_id = a.emp_id LEFT JOIN employee as c ON a.emp_id = c.emp_id WHERE c.status = 'Active' and a.leave_status = 'Approved' and a.leave_date_from >= CURDATE()  ORDER BY a.leave_date_from  ";
    }

    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<li style="overflow:hidden;color:green;" title="Click to view Contact" class="lihover"><div class="row"><div class="col-md-4" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">'.$row['name'].' </div><div class="col-md-2">'.$row['leave_date_from'].' </div><div class="col-md-2">'.$row['leave_date_to'].' </div><div class="col-md-1">'.$row['days'].' </div><div class="col-md-3">'.$row['leave_reason'].' </div></div></li>';
        }
    }
    $htmlstring .= '</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/mdbleavesapprovals', function() use ($app) {
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
    $role = $session['role'];
    $emp_id = 0;
    $user = $db->getOneRecord("select emp_id from users where user_id= $user_id ");
    if ($user != NULL) 
    {
        $emp_id = $user['emp_id'];
    }

    $htmlstring ='<ul style="padding:3px;border: 1px solid #e8e7e7;">';
    $htmlstring .= '<li style="overflow:hidden;"><div class="row" style="background-color: #909090;color: #fff;"><div class="col-md-2">Name</div><div class="col-md-1">From Date</div><div class="col-md-1">To Date</div><div class="col-md-1">Days</div><div class="col-md-2">Status</div><div class="col-md-5">Reason</div></div></li>';

    $sql = "SELECT CONCAT(c.fname,' ',c.lname) as name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to,a.days, a.employee_leave_id, a.leave_status,b.user_id,a.leave_reason FROM employee_leave as a LEFT JOIN users as b ON b.emp_id = a.emp_id LEFT JOIN employee as c ON a.emp_id = c.emp_id WHERE b.user_id = $user_id and c.status = 'Active' and a.leave_status = 'Requested' ORDER BY a.leave_date_from  ";
    if ($emp_id>0)
    {
        $sql = "SELECT CONCAT(c.fname,' ',c.lname) as name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to,a.days, a.employee_leave_id , a.leave_status,b.user_id,a.leave_reason FROM employee_leave as a LEFT JOIN users as b ON b.emp_id = a.emp_id LEFT JOIN employee as c ON a.emp_id = c.emp_id WHERE (b.user_id = $user_id or c.manager_id = $emp_id ) and c.status = 'Active' and a.leave_status = 'Requested' ORDER BY a.leave_date_from  ";
    }
    if ( (in_array("Admin", $role)) || (in_array("Human Resource new", $role)) )
    {
        $sql = "SELECT CONCAT(c.fname,' ',c.lname) as name, DATE_FORMAT(a.leave_date_from,'%d/%m/%Y') AS leave_date_from, DATE_FORMAT(a.leave_date_to,'%d/%m/%Y') AS leave_date_to,a.days, a.employee_leave_id, a.leave_status , b.user_id,a.leave_reason FROM employee_leave as a LEFT JOIN users as b ON b.emp_id = a.emp_id LEFT JOIN employee as c ON a.emp_id = c.emp_id WHERE c.status = 'Active' and a.leave_status = 'Requested' ORDER BY a.leave_date_from  ";
    }

    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<li style="overflow:hidden;color:red;" title="Click to view Contact" class="lihover"><div class="row"><div class="col-md-2" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">'.$row['name'].' </div><div class="col-md-1">'.$row['leave_date_from'].' </div><div class="col-md-1">'.$row['leave_date_to'].' </div><div class="col-md-1">'.$row['days'].' </div><div class="col-md-2">';
            if ($row['user_id']==$user_id)
            {
                $htmlstring .= $row['leave_status'];
            }
            else{
                $htmlstring .= '<select id="status_'.$row['employee_leave_id'].'" name="status_'.$row['employee_leave_id'].'"  data-placeholder="Select status"  style="margin-left:5px;width:100px;" ng-model="status_'.$row['employee_leave_id'].'" ng-init="status_'.$row['employee_leave_id'].'=\''.$row['leave_status'].'\'" ng-click="change_leave_status(status_'.$row['employee_leave_id'].','.$row['employee_leave_id'].')">
                <option value="Requested">Requested</option>
                <option value="Approved" >Approved</option><option value="Rejected" >Rejected</option></select>';
                
            }
            

            $htmlstring .= '</div><div class="col-md-5" >'.$row['leave_reason'].'</div></div></div></li>';
        }
    }

    $htmlstring .= '</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/change_leave_status/:value/:employee_leave_id', function($value, $employee_leave_id) use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE employee_leave set leave_status = '$value' where employee_leave_id = $employee_leave_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
});

$app->get('/rejection_reason/:value/:employee_leave_id', function($value, $employee_leave_id) use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE employee_leave set rejection_reason = '$value' where employee_leave_id = $employee_leave_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
});

$app->get('/mdbexpiringlease', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $htmlstring ='<ul style="padding:3px;border: 1px solid #e8e7e7;">';
    $htmlstring .= '<li style="overflow:hidden;"><div class="row" style="background-color: #909090;color: #fff;"><div class="col-md-6">Agreement ID</div><div class="col-md-6">Till Date</div></div></li>';

    $sql = "SELECT *, DATE_FORMAT(agreement_till_date, '%d/%m/%Y') AS till_date  FROM agreement WHERE DATE_FORMAT(agreement_till_date, '%m-%d') BETWEEN DATE_FORMAT( curdate(), '%m-%d') AND DATE_FORMAT( curdate() + INTERVAL 15 DAY, '%m-%d') and a.agreement_id > 0 ORDER BY agreement_till_date LIMIT 5";

    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $module_name="agreement";
            $htmlstring .= '<li style="overflow:hidden;" title="Click to view Agreement" class="lihover" ng-click="gourl(\''.$module_name.'\', '.$row['agreement_id'].')" ><div class="row"><div class="col-md-6" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">Agreement_'.$row['agreement_id'].' </div><div class="col-md-6">'.$row['till_date'].' </div></div></li>';
        }
    }
    $htmlstring .= '</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/mdbpendingenquiries', function() use ($app) {
    // error_log(print_r($arr, true), 3, "logfile.log");
    // error_log("pks2023", 3, "logfile.log");
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    $user_id = $session['user_id'];
    $role = $session['role'];
    // error_log($user_id, 3, "logfile.log");
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $htmlstring ='<ul style="padding:3px;border: 1px solid #e8e7e7;">';
    $htmlstring .= '<li style="overflow:hidden;"><div class="row" style="background-color: #909090;color: #fff;"><div class="col-md-6">Enquiry ID</div><div class="col-md-6">Created Date</div></div></li>';

    // $sql = "SELECT *,DATE_FORMAT(created_date, '%d/%m/%Y') AS zcreated_date FROM enquiry WHERE status = 'Active' ORDER BY created_date DESC LIMIT 8";
    if(in_array("Admin", $role)){
        $sql = "SELECT *,DATE_FORMAT(created_date, '%d/%m/%Y') AS zcreated_date FROM enquiry WHERE status = 'Active' ORDER BY created_date DESC LIMIT 5";
    }else{
        $sql = "SELECT *, DATE_FORMAT(created_date, '%d/%m/%Y') AS zcreated_date  FROM enquiry  WHERE FIND_IN_SET($user_id, assigned) AND status = 'Active' ORDER BY created_date DESC  LIMIT 5";
    }
    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $module_name="enquiries";
            $htmlstring .= '<li style="overflow:hidden;" title="Click to view Enquiry" class="lihover" ng-click="gourl(\''.$module_name.'\', '.$row['enquiry_id'].')" ><div class="row"><div class="col-md-6" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">Enquiry_'.$row['enquiry_id'].' </div><div class="col-md-6">'.$row['zcreated_date'].' </div></div></li>';
        }
    }
    $htmlstring .= '</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/mdbpendingactivities', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    $user_id = $session['user_id'];
    $role = $session['role'];
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $htmlstring ='<ul style="padding:3px;border: 1px solid #e8e7e7;">';
    $htmlstring .= '<li style="overflow:hidden;"><div class="row" style="background-color: #909090;color: #fff;"><div class="col-md-6">Activity ID</div><div class="col-md-6">Activity Date</div></div></li>';

    if(in_array("Admin", $role)){
        $sql = "SELECT *,a.activity_id,DATE_FORMAT(a.start_date, '%d/%m/%Y') AS start_date  FROM activity_details as a LEFT JOIN activity as b ON a.activity_id = b.activity_id WHERE a.status = 'Visit Pending' and a.activity_id > 0 GROUP BY a.activity_id ORDER BY a.start_date DESC LIMIT 5";
    }else{
        $sql = "SELECT *, a.activity_id, DATE_FORMAT(a.start_date, '%d/%m/%Y') AS start_date FROM activity_details AS a LEFT JOIN activity AS b ON a.activity_id = b.activity_id WHERE a.status = 'Visit Pending' AND a.activity_id > 0 AND FIND_IN_SET($user_id, assign_to) GROUP BY a.activity_id ORDER BY a.start_date DESC LIMIT 5";
    }

    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $module_name="activity";
            $htmlstring .= '<li style="overflow:hidden;" title="Click to view Activity" class="lihover" ng-click="gourl(\''.$module_name.'\', '.$row['activity_id'].')" ><div class="row"><div class="col-md-6" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">Activity_'.$row['activity_id'].' </div><div class="col-md-6">'.$row['start_date'].' </div></div></li>';
        }
    }
    $htmlstring .= '</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/mdbpendingagreement', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    $user_id = $session['user_id'];
    $role = $session['role'];
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $htmlstring ='<ul style="padding:3px;border: 1px solid #e8e7e7;">';
    $htmlstring .= '<li style="overflow:hidden;"><div class="row" style="background-color: #909090;color: #fff;"><div class="col-md-6">Activity ID</div><div class="col-md-6">Activity Date</div></div></li>';

    if(in_array("Admin", $role)){
        $sql = "SELECT *,a.activity_id,DATE_FORMAT(a.start_date, '%d/%m/%Y') AS start_date  FROM activity_details as a LEFT JOIN activity as b ON a.activity_id = b.activity_id WHERE a.status = 'Deal Done' and a.activity_id > 0 GROUP BY a.activity_id ORDER BY a.start_date DESC LIMIT 5";
    }else{
        $sql = "SELECT *, a.activity_id, DATE_FORMAT(a.start_date, '%d/%m/%Y') AS start_date FROM activity_details AS a LEFT JOIN activity AS b ON a.activity_id = b.activity_id WHERE a.status = 'Deal Done' AND a.activity_id > 0 AND FIND_IN_SET($user_id, assign_to) GROUP BY a.activity_id ORDER BY a.start_date DESC LIMIT 5";
    }
    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $module_name="activity";
            $htmlstring .= '<li style="overflow:hidden;" title="Click to view Activity" class="lihover" ng-click="gourl(\''.$module_name.'\', '.$row['activity_id'].')" ><div class="row"><div class="col-md-6" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">Activity_'.$row['activity_id'].' </div><div class="col-md-6">'.$row['start_date'].' </div></div></li>';
        }
    }
    $htmlstring .= '</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/mdbreminders', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    $htmlstring ='<ul style="padding:3px;border: 1px solid #e8e7e7;">';
    $htmlstring .= '<li style="overflow:hidden;"><div class="row" style="background-color: #909090;color: #fff;"><div class="col-md-4">Reminder</div><div class="col-md-4">Type</div><div class="col-md-4">Reminder On</div></div></li>';

    $sql = "SELECT * FROM reminder WHERE status = 'Open' ORDER BY created_date DESC LIMIT 5";

    $stmt = $db->getRows($sql);
    
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<li style="overflow:hidden;" title="Click to view Reminder" class="lihover" data-target="#reminders" data-toggle="tab" ><div class="row"><div class="col-md-4" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">'.$row['reminder_for'].' </div><div class="col-md-4">'.$row['reminder_type'].' </div><div class="col-md-4">'.$row['reminder_on'].' </div></div></li>';
        }
    }
    $htmlstring .= '</ul>';
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


// REMIDERS

$app->get('/reminder_list', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT *, (SELECT GROUP_CONCAT(CONCAT(g.salu,' ',g.fname,' ',g.lname) SEPARATOR ',') FROM users as f LEFT JOIN employee as g on g.emp_id = f.emp_id WHERE FIND_IN_SET(f.user_id , a.assign_to)) as assigned_users FROM reminder as a ORDER BY a.created_by DESC";

    $listreminders = $db->getAllRecords($sql);
    echo json_encode($listreminders);
});


$app->post('/reminder_add', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('reminder_title'),$r->reminder);
    $db = new DbHandler();

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->reminder->created_by = $created_by;
    $r->reminder->created_date = $created_date;
    
    $tabble_name = "reminder";
    $column_names = array('reminder_for', 'reminder_title', 'description', 'reminder_type', 'assign_to', 'reminder_on', 'status', 'created_by', 'created_date');
    $multiple=array("assign_to");
    $result = $db->insertIntoTable($r->reminder, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Reminder created successfully";
        $response["reminder_id"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create Reminder. Please try again";
        echoResponse(201, $response);
    } 
});

$app->get('/deletereminder/:reminder_id', function($reminder_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "DELETE from reminder where reminder_id = $reminder_id ";
    $result = $db->insertByQuery($sql);
});



$app->get('/exporttablelist', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT DISTINCT(table_name) FROM report_fields ";
    $tablelists = $db->getAllRecords($sql);
    echo json_encode($tablelists);
});

$app->get('/showexportcolumns/:table_name', function($table_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $isTableExists = $db->getOneRecord("select 1 from report_fields where table_name='".$table_name."'");
    
    $senddata =  array();
    $htmldata = array();
    $htmlstring = '';
    
    $sql = "SELECT * FROM report_fields WHERE table_name='".$table_name."'";
    $columnlists = $db->getAllRecords($sql);
    echo json_encode($columnlists);
});


$app->post('/showmisreport', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('start_date'),$r->misreport);
    
    if (isset($r->misreport->start_date))
    {
        $start_date = $r->misreport->start_date;
        $tstart_date = substr($start_date,6,4)."-".substr($start_date,3,2)."-".substr($start_date,0,2);
        $r->misreport->start_date = $tstart_date;
    }

    if (isset($r->misreport->end_date))
    {
        $end_date = $r->misreport->end_date;
        $tend_date = substr($end_date,6,4)."-".substr($end_date,3,2)."-".substr($end_date,0,2);
        $r->misreport->end_date = $tend_date;
    }
    $assign_to = $r->misreport->assign_to;
    $sql="SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assign_to from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id where a.assigned = '$assign_to'  ORDER BY CAST(a.enquiry_id as UNSIGNED) DESC";
    $select_enquiries = $db->getAllRecords($sql);
    echo json_encode($select_enquiries);

});


// GOALS

$app->get('/goals_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT *, CONCAT(c.salu,' ',c.fname,' ',c.lname) as username from goals as a  LEFT JOIN users as b ON a.user_id = b.user_id LEFT JOIN employee as c on b.emp_id = c.emp_id LEFT JOIN dropdowns as d ON a.goal_sub_category = d.display_value group by a.goal_id ORDER BY c.lname,c.fname,a.fy_year,a.goal_category, CAST(d.sequence_number as UNSIGNED)";
    $listgoals = $db->getAllRecords($sql);
    echo json_encode($listgoals);
});

$app->get('/change_goal_sub_category/:goal_category', function($goal_category) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from dropdowns WHERE type = 'GOAL_SUB_CATEGORY' and parent_type in ('$goal_category') ORDER BY CAST(sequence_number as UNSIGNED)";
    $dropdowns = $db->getAllRecords($sql);
    echo json_encode($dropdowns);
});



$app->post('/goals_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('user_id'),$r->goals);
    $db = new DbHandler();
    $user_id = $r->goals->user_id;
    $category = $r->goals->category;

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->goals->created_by = $created_by;
    $r->goals->created_date = $created_date;
    
    $tabble_name = "goals";
    $column_names = array('goal_category', 'goal_sub_category', 'remarks', 'user_id','fy_year', 'goal_jan', 'goal_feb', 'goal_mar', 'goal_apr', 'goal_may', 'goal_jun', 'goal_jul', 'goal_aug', 'goal_sep', 'goal_oct', 'goal_nov', 'goal_dec', 'goal_per', 'created_by','created_date');
    $multiple=array("");
    $result = $db->insertIntoTable($r->goals, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Goal created successfully";
        $response["goal_id"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create Goal. Please try again";
        echoResponse(201, $response);
    }
});


$app->get('/goals_edit_ctrl/:goal_id', function($goal_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from goals where goal_id=".$goal_id;
    $datagoals = $db->getAllRecords($sql);
    echo json_encode($datagoals);
    
});

$app->post('/goals_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('user_id'),$r->goals);
    $db = new DbHandler();
    $goal_id  = $r->goals->goal_id;

    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->goals->modified_by = $modified_by;
    $r->goals->modified_date = $modified_date;

    $isRoleExists = $db->getOneRecord("select 1 from goals where goal_id=$goal_id");
    if($isRoleExists){
        $tabble_name = "goals";
        $column_names = array('goal_category', 'goal_sub_category', 'remarks', 'user_id', 'fy_year','goal_jan', 'goal_feb', 'goal_mar', 'goal_apr', 'goal_may', 'goal_jun', 'goal_jul', 'goal_aug', 'goal_sep', 'goal_oct', 'goal_nov', 'goal_dec', 'goal_per','modified_by','modified_date');
        $condition = "goal_id='$goal_id'";
        $multiple=array("");
        $history = $db->historydata( $r->goals, $column_names, $tabble_name,$condition,$goal_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->goals, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Goal Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Goal. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Goal with the provided goal does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/goals_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('user_id'),$r->goals);
    $db = new DbHandler();
    $goal_id  = $r->goals->goal_id;
    $isGoalExists = $db->getOneRecord("select 1 from goals where goal_id=$goal_id");
    if($isGoalExists){
        $tabble_name = "goals";
        $column_names = array('user_id');
        $condition = "goal_id='$goal_id'";
        $result = $db->deleteIntoTable($r->goals, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Goal Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Goal. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Goal with the provided Goal does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectgoals', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from goals ORDER BY user_id";
    $selectgoals = $db->getAllRecords($sql);
    echo json_encode($selectgoals);
});


$app->get('/getgoaldata', function() use ($app) {
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
    $this_month = date('m');
    $enquiry_count = 0;
    $enquirydata = $db->getAllRecords("SELECT count(*) as enquiry_count FROM enquiry where (created_by = $user_id or modified_by = $user_id or $user_id in (assigned)) and month(created_date) = $this_month " );
    if ($enquirydata)
    {
         $enquiry_count = $enquirydata[0]['enquiry_count'];

    }
    $enquiry_goal = 0;
    $goaldata = $db->getAllRecords("SELECT goal_achieve FROM goals where user_id = $user_id and category='Enquiries' " );
    if ($goaldata)
    {
         $enquiry_goal = $goaldata[0]['goal_achieve'];

    }

    $site_visit_count = 0;
    $activitydata = $db->getAllRecords("SELECT count(*) as site_visit_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $this_month) and (activity_type='Site Visit') " );
    if ($activitydata)
    {
         $site_visit_count = $activitydata[0]['site_visit_count'];

    }
    $site_visit_goal = 0;
    $goaldata = $db->getAllRecords("SELECT goal_achieve FROM goals where user_id = $user_id and category='Site Visit' " );
    if ($goaldata)
    {
         $site_visit_goal = $goaldata[0]['goal_achieve'];

    } 


    $client_meeting_count = 0;
    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $this_month) and (activity_type='Meeting') " );
    if ($activitydata)
    {
         $client_meeting_count = $activitydata[0]['client_meeting_count'];

    }
    $client_meeting_goal = 0;
    $goaldata = $db->getAllRecords("SELECT goal_achieve FROM goals where user_id = $user_id and (category='New Client Meeting' or category='Old Client Meeting') " );
    if ($goaldata)
    {
        $client_meeting_goal = $goaldata[0]['goal_achieve'];

    }
    
    
    $deal_count = 0;
    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (month(a.created_date) = $this_month) and (b.status = 'Deal Done') " );
    if ($activitydata)
    {
         $deal_count = $activitydata[0]['deal_count'];

    }

    $deal_goal = 0;
    $goaldata = $db->getAllRecords("SELECT goal_achieve FROM goals where user_id = $user_id and (category='Deal') " );
    if ($goaldata)
    {
        $deal_goal = $goaldata[0]['goal_achieve'];

    }

    $htmldata['enquiry_count']=$enquiry_count;
    $htmldata['enquiry_goal']=$enquiry_goal;
    $htmldata['site_visit_count']=$site_visit_count;
    $htmldata['site_visit_goal']=$site_visit_goal;
    $htmldata['client_meeting_count']=$client_meeting_count;
    $htmldata['client_meeting_goal']=$client_meeting_goal;
    $htmldata['deal_count']=$deal_count;
    $htmldata['deal_goal']=$deal_goal;

    $senddata[]=$htmldata;
    echo json_encode($senddata);

});

$app->post('/getgoaldatanew', function() use ($app) {
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

    $senddata =  array();
    $htmldata = array();
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
    $ttsql = "";
    $htmlstring = '';
    $htmlstring = '<div style="width:100%;margin-top:20px;">
    <table style="border:1px solid #000000;width:100%;table-layout:fixed;text-align: center;" id = "goaldata" class="goaldata" cellspacing="3">';
    $start_date = $r->goalsdata->start_date;
    $end_date = $r->goalsdata->end_date;
    
    $start_month = substr($start_date,3,2);
    $end_month = substr($end_date,3,2);

    $start_year = substr($start_date,6,4);
    $end_year = substr($end_date,6,4);

    $this_year = (int)$end_year;

    $year_query = " and (year(created_date))>=$start_year and (year(created_date))<=$end_year ";

    $byear_query = " and (year(b.created_date))>=$start_year and (year(b.created_date))<=$end_year ";
    
    $total_months = $end_month - $start_month+1;
    if ($end_month<$start_month)
    {
        $total_months = $end_month+12 - $start_month + 1;
    }
    /*$htmlstring .='total months:'.$total_months;
    $htmlstring .='start month:'.$start_month;
    $htmlstring .='end month:'.$end_month;*/
    $user_id = $r->goalsdata->user_id;
    $no_cols = 0;
    $sql = "SELECT * from goals as a LEFT JOIN dropdowns as b ON a.goal_sub_category = b.display_value WHERE a.user_id = $user_id and b.parent_type = a.goal_category and a.fy_year = '$this_year' group by a.goal_sub_category ORDER BY CAST(b.sequence_number as UNSIGNED)";

    $stmt = $db->getRows($sql);
    $htmlstring .='<tr style="background-color:#d2aa4b;color:#ffffff;"><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top">Month</td>';  
    $total_per = array(); 
    $goals_to_achieve = array(); 
    $temp = array();
    $goal_per_to_get = array();
    $y = 1;
    $goal_category = "Property Team";
    $tgoal_category = array();
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $goal_category = $row['goal_category'];
            $goal_sub_category = $row['goal_sub_category'];
            $tgoal_category[$y]=$goal_sub_category;
            $goal_per = $row['goal_per'];
            $ncount = 0;
            for ($z=(int)$start_month;$z<=(int)$end_month;$z++)
            {
                if ($row['goal_'.$month_dis[$z]]>0)
                {
                    $temp[$y][$z]= $row['goal_'.$month_dis[$z]];
                    $goal_per_to_get[$y][$z] = $row['goal_per'];
                    //$tgoal_category[$y][$z]=$goal_sub_category;
                    $ncount ++;
                }
                
            }
            if ($ncount>0)
            {
                $htmlstring .='<td colspan="3"  style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top">'.$goal_sub_category.'<p>'.$row['goal_per'].'%</p></td>';
                //$total_per[$no_cols+1]=$row['goal_per'];
                for ($z=(int)$start_month;$z<=(int)$end_month;$z++)
                {
                    $temp[$y][$z]= $row['goal_'.$month_dis[$z]];
                    $goal_per_to_get[$y][$z] = $row['goal_per'];
                }
                $no_cols = $no_cols + 1;
                $y++;
            }

        }
    }
    $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top">Total Target</td></tr>';
    
    $htmlstring .='<tr style="background-color:#b3b2b2;"><td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td>';
    for ($n=1; $n<=$no_cols;++$n)
    {
        $htmlstring .= '<td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;color:#ffffff;" valign="top">Goal</td><td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;color:#ffffff;" valign="top">Achieved</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;color:red;font-size:16px;color:#ffffff;width:50px;" valign="top">Per</td>';
    }
    $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top"></td></tr>';
    $total = array();
    $total_achieved = array();
    $grand_total_per = array();

    $per = array(); 
    $agreement_sql="";
    $abc_sql="";
    //goals_top
    $i = 1;
    for ($j=(int)$start_month;$j<=$end_month;$j++)
    {

        for ($i=1;$i<=$no_cols;$i++)
        {
            $goals_achieved[$i][$j]=0;
            $goal_sub_category = $tgoal_category[$i];
            if ($goal_sub_category == "CRM Training")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) ".$year_query." and (activity_type='Task') and (activity_sub_type='CRM Training')  ";
            }
            if ($goal_sub_category == "CRM Error_Resolved")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='CRM Error_Resolved')  ";
            }
            if ($goal_sub_category == "Sharing Properties" || $goal_sub_category == "Commercial  Sharing" || $goal_sub_category == "Residential Sharing")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Sharing Properties' OR activity_sub_type='Commercial  Sharing' OR activity_sub_type='Residential Sharing' )  ";
            }
            if ($goal_sub_category == "Sharing Projects" || $goal_sub_category == "Project Sharing")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Sharing Projects' or activity_sub_type='Project Sharing')  ";
            }
            if ($goal_sub_category == "Reports (Sales, Data Updation, HR & Marketing)")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Reports (Sales, Data Updation, HR & Marketing)')  ";
            }
            if ($goal_sub_category == "CRM Updation")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='CRM Updation')  ";
            }
            
            if ($goal_sub_category == "Account Report & Departmental Monthly Reports")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Account Report & Departmental Monthly Reports')  ";
            }
            if ($goal_sub_category == "Certification + Behaviour+ CRM")
            {
                $sql = "SELECT * FROM behaviour WHERE user_id = $user_id  and (month(data_date) = $j) and (year(data_date) = $this_year) LIMIT 1";
                $crmdata = $db->getAllRecords($sql);
                $total_crm = 0;
                if ($crmdata)
                {
                    
                    if ($crmdata[0]['monthly_attendance']=="true")
                    {
                        $total_crm = $total_crm + 2;
                    }
                    if ($crmdata[0]['new_idea']=="true")
                    {
                        $total_crm = $total_crm + 2;
                    }
                    if ($crmdata[0]['activity_participation']=="true")
                    {
                        $total_crm = $total_crm + 1;
                    }
                    if ($crmdata[0]['daily_activity']=="true")
                    {
                        $total_crm = $total_crm + 1;
                    }
                    if ($crmdata[0]['response_assigned']=="true")
                    {
                        $total_crm = $total_crm + 2;
                    }
                    if ($crmdata[0]['transaction_process']=="true")
                    {
                        $total_crm = $total_crm + 2;
                    }

                    $goals_achieved[$i][$j] = $total_crm;
                }
            }
            if ($goal_sub_category == "New Project Posting" || $goal_sub_category == "Project  Scouted")
            {
                $sql = "SELECT count(*) as goals_achieved FROM project where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (month(created_date) = $j)  ".$year_query." ";
            }
            
            if ($goal_sub_category == "New Property Posting" || $goal_sub_category == "Property Scouted" || $goal_sub_category == "New Property Generated")
            {
                $sql = "SELECT count(*) as goals_achieved FROM property where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and project_id = 0 and (month(created_date) = $j)  ".$year_query." ";
                $ttsql = $sql;
            }
            if ($goal_sub_category == "New Enquiries Generated")
            {
                $sql = "SELECT count(*) as goals_achieved FROM enquiry where (created_by = $user_id  or modified_by = $user_id or FIND_IN_SET ($user_id ,assigned)) and (month(created_date) = $j)  ".$year_query." ";
            }
            if ($goal_sub_category == "System  Updation")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='System  Updation')  ";
            }
            if ($goal_sub_category == "Other Design")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Other Design')  ";
            }

            if ($goal_sub_category == "Data Calling")
            {
                $sql = "SELECT sum(calling_count) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Data Calling')  ";
            }

            if ($goal_sub_category == "Social Media")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Social Media') and created_date<'2022-07-01' ";
                
                $extracteddata = $db->getAllRecords($sql);
                $tgoals_achived = 0;

                if ($extracteddata)
                {
                    $tgoals_achived = $extracteddata[0]['goals_achieved'];
                }
                
                $social_sql = "SELECT * FROM social_media where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET($user_id , assign_to)) and (month(data_date) = $j) and (year(data_date))>=$start_year and (year(data_date))<=$end_year ";
                $abc_sql = "SELECT * FROM social_media where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET($user_id , assign_to)) and (month(data_date) = $j) and (year(data_date))>=$start_year and (year(data_date))<=$end_year ";

                $sql_query = $social_sql;
                $social_stmt = $db->getRows($social_sql);
                $instagram_no_of_posts = 0;
                $facebook_no_of_posts = 0;
                $linkedin_no_of_posts = 0;
                $twitter_no_of_posts = 0;
                $youtube_no_of_posts = 0;
                $other_no_of_posts = 0;
                if($social_stmt->num_rows > 0)
                {
                    while($row = $social_stmt->fetch_assoc())
                    {
                        if ($row['media_category'] == 'Instagram')
                        {
                            $instagram_no_of_posts =  $instagram_no_of_posts + $row['no_of_posts'];
                        }
                        if ($row['media_category'] == 'Facebook')
                        {
                            $facebook_no_of_posts =  $facebook_no_of_posts + $row['no_of_posts'];
                        }
                        if ($row['media_category'] == 'Linkedin')
                        {
                            $linkedin_no_of_posts =  $linkedin_no_of_posts + $row['no_of_posts'];
                        }
                        if ($row['media_category'] == 'Twitter')
                        {
                            $twitter_no_of_posts =  $twitter_no_of_posts + $row['no_of_posts'];
                        }
                        if ($row['media_category'] == 'Youtube')
                        {
                            $youtube_no_of_posts =  $youtube_no_of_posts + $row['no_of_posts'];
                        }
                        if ($row['media_category'] == 'Other')
                        {
                            $other_no_of_posts =  $other_no_of_posts + $row['no_of_posts'];
                        }

                        if ($row['no_of_likes']>=30 || $row['no_of_views']>=30 || $row['no_of_followers'] >=5 || $row['no_of_subscribers'] >=5 )
                        {
                            $tgoals_achived = $tgoals_achived + 1;
                        }
                    }
                }
                /*$instagram_no_of_posts = 0;
                $instagram_no_of_likes = 0;
                $instagram_no_of_followers = 0;
                $instagram_no_of_subscribers = 0;
                $instagram_no_of_views = 0;
                $instagram_lead_generation = 0;

                $facebook_no_of_posts = 0;
                $facebook_no_of_likes = 0;
                $facebook_no_of_followers = 0;
                $facebook_no_of_subscribers = 0;
                $facebook_no_of_views = 0;
                $facebook_lead_generation = 0;

                $linkedin_no_of_posts = 0;
                $linkedin_no_of_likes = 0;
                $linkedin_no_of_followers = 0;
                $linkedin_no_of_subscribers = 0;
                $linkedin_no_of_views = 0;
                $linkedin_lead_generation = 0;

                $twitter_no_of_posts = 0;
                $twitter_no_of_likes = 0;
                $twitter_no_of_followers = 0;
                $twitter_no_of_subscribers = 0;
                $twitter_no_of_views = 0;
                $twitter_lead_generation = 0;

                $youtube_no_of_posts = 0;
                $youtube_no_of_likes = 0;
                $youtube_no_of_followers = 0;
                $youtube_no_of_subscribers = 0;
                $youtube_no_of_views = 0;
                $youtube_lead_generation = 0;

                $other_no_of_posts = 0;
                $other_no_of_likes = 0;
                $other_no_of_followers = 0;
                $other_no_of_subscribers = 0;
                $other_no_of_views = 0;
                $other_lead_generation = 0;

                    
                if($social_stmt->num_rows > 0)
                {
                    while($row = $social_stmt->fetch_assoc())
                    {
                        
                        if ($row['media_category'] == 'Instagram')
                        {
                            $instagram_no_of_posts =  $instagram_no_of_posts + $row['no_of_posts'];
                            $instagram_no_of_likes =  $instagram_no_of_likes + $row['no_of_likes'];
                            $instagram_no_of_followers =  $instagram_no_of_followers + $row['no_of_followers'];
                            $instagram_no_of_subscribers =  $instagram_no_of_subscribers + $row['no_of_subscribers'];
                            $instagram_no_of_views =  $instagram_no_of_views + $row['no_of_views'];
                            $instagram_lead_generation =  $instagram_lead_generation + $row['lead_generation'];
                        }
                        if ($row['media_category'] == 'Facebook')
                        {
                            $facebook_no_of_posts =  $facebook_no_of_posts + $row['no_of_posts'];
                            $facebook_no_of_likes =  $facebook_no_of_likes + $row['no_of_likes'];
                            $facebook_no_of_followers =  $facebook_no_of_followers + $row['no_of_followers'];
                            $facebook_no_of_subscribers =  $facebook_no_of_subscribers + $row['no_of_subscribers'];
                            $facebook_no_of_views =  $facebook_no_of_views + $row['no_of_views'];
                            $facebook_lead_generation =  $facebook_lead_generation + $row['lead_generation'];
                        }
                        if ($row['media_category'] == 'Linkedin')
                        {
                            $linkedin_no_of_posts =  $linkedin_no_of_posts + $row['no_of_posts'];
                            $linkedin_no_of_likes =  $linkedin_no_of_likes + $row['no_of_likes'];
                            $linkedin_no_of_followers =  $linkedin_no_of_followers + $row['no_of_followers'];
                            $linkedin_no_of_subscribers =  $linkedin_no_of_subscribers + $row['no_of_subscribers'];
                            $linkedin_no_of_views =  $facebook_no_of_views + $row['no_of_views'];
                            $linkedin_lead_generation =  $linkedin_lead_generation + $row['lead_generation'];
                        }
                        if ($row['media_category'] == 'Twitter')
                        {
                            $twitter_no_of_posts =  $twitter_no_of_posts + $row['no_of_posts'];
                            $twitter_no_of_likes =  $twitter_no_of_likes + $row['no_of_likes'];
                            $twitter_no_of_followers =  $twitter_no_of_followers + $row['no_of_followers'];
                            $twitter_no_of_subscribers =  $twitter_no_of_subscribers + $row['no_of_subscribers'];
                            $twitter_no_of_views =  $twitter_no_of_views + $row['no_of_views'];
                            $twitter_lead_generation =  $twitter_lead_generation + $row['lead_generation'];
                        }
                        if ($row['media_category'] == 'Youtube')
                        {
                            $youtube_no_of_posts =  $youtube_no_of_posts + $row['no_of_posts'];
                            $youtube_no_of_likes =  $youtube_no_of_likes + $row['no_of_likes'];
                            $youtube_no_of_followers =  $youtube_no_of_followers + $row['no_of_followers'];
                            $youtube_no_of_subscribers =  $youtube_no_of_subscribers + $row['no_of_subscribers'];
                            $youtube_no_of_views =  $youtube_no_of_views + $row['no_of_views'];
                            $youtube_lead_generation =  $youtube_lead_generation + $row['lead_generation'];
                        }
                        if ($row['media_category'] == 'Other')
                        {
                            $other_no_of_posts =  $other_no_of_posts + $row['no_of_posts'];
                            $other_no_of_likes =  $other_no_of_likes + $row['no_of_likes'];
                            $other_no_of_followers =  $other_no_of_followers + $row['no_of_followers'];
                            $other_no_of_subscribers =  $other_no_of_subscribers + $row['no_of_subscribers'];
                            $other_no_of_views =  $other_no_of_views + $row['no_of_views'];
                            $other_lead_generation =  $other_lead_generation + $row['lead_generation'];
                        }


                    }
                }
                if ($instagram_no_of_posts >=70)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($facebook_no_of_posts >=60)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($linkedin_no_of_posts >=60)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($twitter_no_of_posts >=50)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($youtube_no_of_posts >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($other_no_of_posts >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }


                if ($instagram_no_of_likes >=30)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($facebook_no_of_likes >=30)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($linkedin_no_of_likes >=30)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($twitter_no_of_likes >=30)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($youtube_no_of_likes >=30)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($other_no_of_likes >=30)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($instagram_no_of_followers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($facebook_no_of_followers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($linkedin_no_of_followers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($twitter_no_of_followers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($youtube_no_of_followers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($other_no_of_followers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }


                if ($instagram_no_of_subscribers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($facebook_no_of_subscribers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($linkedin_no_of_subscribers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($twitter_no_of_subscribers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($youtube_no_of_subscribers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($other_no_of_subscribers >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($instagram_no_of_views >=30)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($facebook_no_of_views >=30)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($linkedin_no_of_views >=30)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($twitter_no_of_views >=30)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($youtube_no_of_views >=305)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($other_no_of_views >=30)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($instagram_lead_generation >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($facebook_lead_generation >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($linkedin_lead_generation >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }

                if ($twitter_lead_generation >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($youtube_lead_generation >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }
                if ($other_lead_generation >=5)
                {
                    $tgoals_achived = $tgoals_achived + 1;
                }*/
                
                $goals_achieved[$i][$j] = $tgoals_achived;
                $abc_sql.="result:".$goals_achieved[$i][$j]."done";
                
            }
            if ($goal_sub_category == "Property Design")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Property Design')  ";
            }
            if ($goal_sub_category == "Project Design")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Project Design')  ";

            }
            
            if ($goal_sub_category == "No. Of Recruitment" || $goal_sub_category == "Recruitment")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='No. Of Recruitment' or  activity_sub_type='Recruitment')  ";
            }
            if ($goal_sub_category == "Training" || $goal_sub_category == "HR Training")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Training' OR activity_sub_type='HR Training')  ";
            }
            if ($goal_sub_category == "Engagement Activity")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Engagement Activity')  ";
            }
            
            if ($goal_sub_category == "Client Inspection" || $goal_sub_category == "Broker Inspection" )
            {
                $sql1 = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Site Visit') GROUP BY day(created_date),client_id ";
                $temp_sql = $sql1;

                $stmt1 = $db->getRows($sql1); 
                $inspection_count = 0;
                if($stmt1->num_rows > 0)
                {
                    while($row1 = $stmt1->fetch_assoc())
                    {
                        $inspection_count = $inspection_count + 1;
                    }
                }
                $goals_achieved[$i][$j] = $inspection_count;
                $stmt1->close();
                
            }

            if ($goal_sub_category == "Transaction")
            {
                $stage_query = " and b.agreement_stage IN(".getstages().") ";

                //$sql = "SELECT sum(b.our_brokerage) as goals_achieved FROM agreement_details  as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id where (a.contribution_to=$user_id) and (month(b.created_date) = $j)  and (UPPER(b.agreement_stage) = 'BROKERAGE BILL SUBMISSION' ||  UPPER(b.agreement_stage) = 'TESTIMONIAL') ";

                $sql = "SELECT sum(b.our_brokerage) as goals_achieved FROM agreement_details  as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id where (a.contribution_to=$user_id) and (month(b.created_date) = $j) ".$byear_query." ".$stage_query." ";

            }
            $agreement_sql .=$sql;
            if ($goal_sub_category == "New Clients Meeting"  || $goal_sub_category == "New Broker Meeting" || $goal_sub_category == "Retail Meeting" || $goal_sub_category == "New Developer Meeting")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Meeting') ";
            }
            
            if ($goal_sub_category == "Mandate  Clients")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Mandate  Clients')  ";
            }
            if ($goal_sub_category == "Mandate Property")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Mandate Property')  ";
            }
            if ($goal_sub_category == "Proposals for Mandate projects")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Proposals for Mandate projects')  ";
            }
            if ($goal_sub_category == "Residential Project")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Residential Project')  ";
            }

            if ($goal_sub_category == "Commercial Project")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Commercial Project')  ";
            }

            if ($goal_sub_category == "Lead generation")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Lead generation')  ";
            }

            if ($goal_sub_category == "Creative Activities")
            {
                $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j)  ".$year_query."  and (activity_type='Task') and (activity_sub_type='Creative Activities')  ";
            }
            if ($goal_sub_category == "Certification + Behaviour+ CRM" || $goal_sub_category == "Client Inspection" || $goal_sub_category == "Broker Inspection" || $goal_sub_category == "Social Media")
            {
            }
            else{
                $extracteddata = $db->getAllRecords($sql);
                if ($extracteddata)
                {
                    $goals_achieved[$i][$j] = $extracteddata[0]['goals_achieved'];
                    $ttsql .="achived info:".$goals_achieved[$i][$j]."=".$i.",".$j;
                
                }
            }
        }
    }
    
    $grand_total_months = 0;
    for ($j=(int)$start_month;$j<=$end_month;$j++)
    {
        $total_target_achieved = 0;
        $htmlstring .='<tr><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;" valign="top">'.$month_dis[$j].'</td>';
        for ($i=1;$i<=$no_cols;$i++)
        {
            $htmlstring .= '<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;" valign="top">'.$temp[$i][$j].'</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;" valign="top">';
            $tper = 0;
            $htmlstring .= $goals_achieved[$i][$j];
            $total_achieved[$i] = $total_achieved[$i] + (int)$goals_achieved[$i][$j];
            $total[$i] = $total[$i] + $temp[$i][$j];


            $tper = 0;
            $target_achieved = 0;
            if ($goals_achieved[$i][$j]>0 && $temp[$i][$j]>0)
            {
                $tper = ($goals_achieved[$i][$j] / $temp[$i][$j])*100;
                $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                if ($target_achieved>$goal_per_to_get[$i][$j])
                {
                    $target_achieved = $goal_per_to_get[$i][$j];
                }
                $total_target_achieved = $total_target_achieved + $target_achieved;
            }
            $htmlstring .= '</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;" valign="top">'.number_format($tper,2).'<p>('.$target_achieved.'%)</p></td>';
            $grand_total_per[$i] = $grand_total_per[$i] + $target_achieved;
        }
        $grand_total_months ++;
        $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;color:red;" valign="top">'.$total_target_achieved.'%</td></tr>';
    }
//goals_end
    $htmlstring .='<tr style="background-color:#000000;font-size:16px;color:#ffffff;"><td style="border-right:1px solid #ffffff;border-bottom:1px solid #000000;font-size:16px;" valign="top">Total</td>';
    $total_of_grand_total = 0;
    for ($n=1;$n<=$no_cols;++$n)
    {
        $htmlstring .= '<td style="border-right:1px solid #ffffff;border-bottom:1px solid #000000;font-size:16px;">'.$total[$n].'</td><td style="border-right:1px solid #ffffff;border-bottom:1px solid #000000;font-size:16px;">'.$total_achieved[$n].'</td><td style="border-right:1px solid #ffffff;border-bottom:1px solid #000000;font-size:16px;">'.number_format(($grand_total_per[$n]/$grand_total_months),2).'%</td>';
        $total_of_grand_total = $total_of_grand_total + ($grand_total_per[$n]/$grand_total_months);
    }
    
    $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top">'.number_format(($total_of_grand_total),2).'%</td></tr>';

    $htmlstring .='<tr><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;color:red;font-size:16px;" valign="top">Balance</td>';

    for ($n=1;$n<=$no_cols;++$n)
    {
        $htmlstring .= '<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;"></td>';
    }
    
    $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top"></td></tr>';
    //$htmlstring .='</tr>';
    $htmlstring .='</table></div>';

    $htmldata['htmlstring']=$htmlstring;
    $htmldata['ttsql']=$ttsql;
    $htmldata['abc_sql']=$abc_sql;
    
    $htmldata['agreement_sql']=$agreement_sql;
    $htmldata['print_goal_sub_category'] = $print_goal_sub_category;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->post('/getgoaldatanewbackup', function() use ($app) {
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

    $senddata =  array();
    $htmldata = array();
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
    $htmlstring = '';
    $htmlstring = '<div style="width:100%;margin-top:20px;">
    <table style="border:1px solid #000000;width:100%;table-layout:fixed;text-align: center;" id = "goaldata" class="goaldata" cellspacing="3">';
    $start_date = $r->goalsdata->start_date;
    $end_date = $r->goalsdata->end_date;

    $start_month = substr($start_date,3,2);
    $end_month = substr($end_date,3,2);
    $total_months = $end_month - $start_month+1;
    if ($end_month<$start_month)
    {
        $total_months = $end_month+12 - $start_month + 1;
    }
    /*$htmlstring .='total months:'.$total_months;
    $htmlstring .='start month:'.$start_month;
    $htmlstring .='end month:'.$end_month;*/
    $user_id = $r->goalsdata->user_id;
    $no_cols = 0;
    $sql = "SELECT * from goals as a LEFT JOIN dropdowns as b ON a.goal_sub_category = b.display_value WHERE a.user_id = $user_id and b.parent_type = a.goal_category group by a.goal_sub_category ORDER BY CAST(b.sequence_number as UNSIGNED)";

    $stmt = $db->getRows($sql);
    $htmlstring .='<tr style="background-color:#d2aa4b;color:#ffffff;"><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top">Month</td>';  
    $total_per = array(); 
    $goals_to_achieve = array(); 
    $temp = array();
    $goal_per_to_get = array();
    $y = 1;
    $goal_category = "Property Team";
    $tgoal_category = array();
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $goal_category = $row['goal_category'];
            $goal_sub_category = $row['goal_sub_category'];
            $tgoal_category[$y]=$goal_sub_category;
            $goal_per = $row['goal_per'];
            $ncount = 0;
            for ($z=(int)$start_month;$z<=(int)$end_month;$z++)
            {
                if ($row['goal_'.$month_dis[$z]]>0)
                {
                    $temp[$y][$z]= $row['goal_'.$month_dis[$z]];
                    $goal_per_to_get[$y][$z] = $row['goal_per'];
                    //$tgoal_category[$y][$z]=$goal_sub_category;
                    $ncount ++;
                }
                
            }
            if ($ncount>0)
            {
                $htmlstring .='<td colspan="3"  style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top">'.$goal_sub_category.'<p>'.$row['goal_per'].'%</p></td>';
                //$total_per[$no_cols+1]=$row['goal_per'];
                for ($z=(int)$start_month;$z<=(int)$end_month;$z++)
                {
                    $temp[$y][$z]= $row['goal_'.$month_dis[$z]];
                    $goal_per_to_get[$y][$z] = $row['goal_per'];
                }
                $no_cols = $no_cols + 1;
                $y++;
            }

        }
    }
    $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top">Total Target</td></tr>';
    
    $htmlstring .='<tr style="background-color:#b3b2b2;"><td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td>';
    for ($n=1; $n<=$no_cols;++$n)
    {
        $htmlstring .= '<td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;color:#ffffff;" valign="top">Goal</td><td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;color:#ffffff;" valign="top">Achieved</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;color:red;font-size:16px;color:#ffffff;width:50px;" valign="top">Per</td>';
    }
    $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top"></td></tr>';
    $total = array();
    $total_achieved = array();
    $per = array(); 

    //goals_top
    $Property_Scouted = array();
    $Project_Scouted = array();
    $Client_Inspection = array();
    $Transaction = array();
    $Certification_Behaviour_CRM = array();
    $New_Clients_Meeting = array();
    $Properties_Added = array();
    $NewEnquiries_Generated = array();
    $Enquiries_Generated = array();

    $Data_Gathering = array();
    $System_Updation = array();
    $Recruitment = array();
    $Training = array();
    $Engagement_Activity = array();
    $CRM_Updation = array();
    $Data_Calling = array();
    $Social_Media = array();
    $Property_Design = array();
    $Project_Design = array();
    $CRM_Training = array();
    $CRM_Error_Resolved = array();    
    $Mandate_Clients = array();
    $Mandate_Property = array();
    $Proposals_for_Mandate_projects = array();           
                

    $Residential_Project = array();
    $Commercial_Project = array();
    $Lead_Generation = array();
    $Creative_Activities = array();


    for ($j=(int)$start_month;$j<=$end_month;$j++)
    {
        

        $Property_Scouted[$j] = 0;
        // CHANGES ON 08.04.2021
        //$activitydata = $db->getAllRecords("SELECT count(*) as property_visit_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Property Visit') and (property_id > 0) " );
        
        $activitydata = $db->getAllRecords("SELECT count(*) as property_visit_count FROM property where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to))  and project_id = 0 and (month(created_date) = $j) " );

        if ($activitydata)
        {
            $Property_Scouted[$j] = $activitydata[0]['property_visit_count'];

        }


        $Project_Scouted[$j] = 0;
        //$activitydata = $db->getAllRecords("SELECT count(*) as property_visit_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Property Visit') and (project_id > 0) " );
        $activitydata = $db->getAllRecords("SELECT count(*) as project_visit_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (month(created_date) = $j) " );
        if ($activitydata)
        {
            $Project_Scouted[$j] = $activitydata[0]['project_visit_count'];

        }

        $Client_Inspection[$j] = 0;
        /*$activitydata = $db->getAllRecords("SELECT count(*) as client_inspection_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Site Visit') GROUP BY day(created_date) " );
        if ($activitydata)
        {
            $Client_Inspection[$j] = $activitydata[0]['client_inspection_count'];

        }*/

        $sql1 = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and ((month(created_date) = $j)) and (activity_type='Site Visit') GROUP BY day(created_date),client_id ";

        $stmt1 = $db->getRows($sql1);
        $inspection_count = 0;
        if($stmt1->num_rows > 0)
        {
            while($row1 = $stmt1->fetch_assoc())
            {
                $inspection_count = $inspection_count + 1;
            }
        }
        $stmt1->close();
        $Client_Inspection[$j] = $inspection_count;

        
        $Transaction[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT sum(b.our_brokerage) as deal_amount FROM agreement_details  as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id where (a.contribution_to=$user_id)  and (month(b.created_date) = $j) " );
        if ($activitydata)
        {
            $Transaction[$j] = $activitydata[0]['deal_amount'];

        }
        

        $New_Clients_Meeting[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as New_Clients_Meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Meeting') " );
        if ($activitydata)
        {
            $New_Clients_Meeting[$j] = $activitydata[0]['New_Clients_Meeting_count'];

        }

        $NewEnquiries_Generated[$j] = 0;
        $enquirydata = $db->getAllRecords("SELECT count(*) as enquiry_count FROM enquiry where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assigned)) and month(created_date) = $j " );
        if ($enquirydata)
        {
            $NewEnquiries_Generated[$j] = $enquirydata[0]['enquiry_count'];

        }

        $Enquiries_Generated[$j] = 0;
        $enquirydata = $db->getAllRecords("SELECT count(*) as Enquiries_Generated_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (enquiry_id > 0) " );
        if ($enquirydata)
        {
            $Enquiries_Generated[$j] = $enquirydata[0]['Enquiries_Generated_count'];

        }

        $Properties_Added[$j] = 0;
        $enquirydata = $db->getAllRecords("SELECT count(*) as Properties_Added_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (property_id > 0) " );

        if ($enquirydata)
        {
            $Properties_Added[$j] = $enquirydata[0]['Properties_Added_count'];

        }

    
        $Data_Gathering[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Data_Gathering_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Data Gathering' ) " );
        if ($activitydata)
        {
            $Data_Gathering[$j] = $activitydata[0]['Data_Gathering_count'];

        }

        $System_Updation[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as system_updation_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'System  Updation') " );
        if ($activitydata)
        {
            $System_Updation[$j] = $activitydata[0]['system_updation_count'];

        }

        $Recruitment[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Recruitment_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Recruitment') " );
        if ($activitydata)
        {
            $Recruitment[$j] = $activitydata[0]['Recruitment_count'];

        }

        $Training[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Training_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Training') " );
        if ($activitydata)
        {
            $Training[$j] = $activitydata[0]['Training_count'];

        }

        $Engagement_Activity[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Engagement_Activity_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Engagement_Activity') " );
        if ($activitydata)
        {
            $Engagement_Activity[$j] = $activitydata[0]['Engagement_Activity_count'];

        }

        $CRM_Updation[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as CRM_Updation_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'CRM Updation') " );
        if ($activitydata)
        {
            $CRM_Updation[$j] = $activitydata[0]['CRM_Updation_count'];

        }
        $Data_Calling[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT sum(calling_count) as Data_Calling_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Data Calling') " );
        $data_calling_query = "SELECT sum(calling_count) as Data_Calling_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Data Calling') ";
        if ($activitydata)
        {
            $Data_Calling[$j] = $activitydata[0]['Data_Calling_count'];

        }
        $Social_Media[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Social_Media_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Social Media') " );
        if ($activitydata)
        {
            $Social_Media[$j] = $activitydata[0]['Social_Media_count'];

        }


        $Mandate_Clients[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Mandate_Clients_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Mandate  Clients') " );
        if ($activitydata)
        {
            $Mandate_Clients[$j] = $activitydata[0]['Mandate_Clients_count'];

        }
        $Mandate_Property[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Mandate_Property_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Mandate Property') " );
        if ($activitydata)
        {
            $Mandate_Property[$j] = $activitydata[0]['Mandate_Property_count'];

        }
        
        $Proposals_for_Mandate_projects[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Proposals_for_Mandate_projects_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Proposals for Mandate projects') " );
        if ($activitydata)
        {
            $Proposals_for_Mandate_projects[$j] = $activitydata[0]['Proposals_for_Mandate_projects_count'];

        }

        $Residential_Project[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Residential_Project_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Residential Project') " );
        if ($activitydata)
        {
            $Residential_Project[$j] = $activitydata[0]['Residential_Project_count'];

        }

        $Commercial_Project[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Commercial_Project_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Commercial Project') " );
        if ($activitydata)
        {
            $Commercial_Project[$j] = $activitydata[0]['Commercial_Project_count'];

        }
        $Lead_Generation[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Lead_Generation_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Lead generation') " );
        if ($activitydata)
        {
            $Lead_Generation[$j] = $activitydata[0]['Lead_Generation_count'];

        }
        $Creative_Activities[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Creative_Activities_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Creative Activities') " );
        if ($activitydata)
        {
            $Creative_Activities[$j] = $activitydata[0]['Creative_Activities_count'];

        }

        $Property_Design[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Property_Design_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Property Design') " );
        if ($activitydata)
        {
            $Property_Design[$j] = $activitydata[0]['Property_Design_count'];

        }

        $Project_Design[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as Project_Design_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'Project Design') " );
        if ($activitydata)
        {
            $Project_Design[$j] = $activitydata[0]['Project_Design_count'];

        }
        $CRM_Training [$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as CRM_Training_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'CRM_Training ') " );
        if ($activitydata)
        {
            $CRM_Training [$j] = $activitydata[0]['CRM_Training_count'];

        }

        $CRM_Error_Resolved[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as CRM_Error_Resolved_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Task') and (activity_sub_type = 'CRM Error_Resolved') " );
        if ($activitydata)
        {
            $CRM_Error_Resolved[$j] = $activitydata[0]['CRM_Error_Resolved_count'];

        }
        $Certification_Behaviour_CRM[$j] = 0;

        $crmsql = "SELECT * FROM behaviour WHERE user_id = $user_id and month(data_date) = $j LIMIT 1";
        $crmdata = $db->getAllRecords($crmsql);
        $total_crm = 0;
        if ($crmdata)
        {
            
            if ($crmdata[0]['monthly_attendance']=="true")
            {
                $total_crm = $total_crm + 2;
            }
            if ($crmdata[0]['new_idea'])
            {
                $total_crm = $total_crm + 2;
            }
            if ($crmdata[0]['activity_participation']=="true")
            {
                $total_crm = $total_crm + 1;
            }
            if ($crmdata[0]['daily_activity']=="true")
            {
                $total_crm = $total_crm + 1;
            }
            if ($crmdata[0]['response_assigned']=="true")
            {
                $total_crm = $total_crm + 2;
            }
            if ($crmdata[0]['transaction_process']=="true")
            {
                $total_crm = $total_crm + 2;
            }
            $Certification_Behaviour_CRM[$j] = $total_crm;
        }
    }
    
    for ($j=(int)$start_month;$j<=$end_month;$j++)
    {
        $total_target_achieved = 0;
        $htmlstring .='<tr><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;" valign="top">'.$month_dis[$j].'</td>';
        for ($i=1;$i<=$no_cols;$i++)
        {
            $htmlstring .= '<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;" valign="top">'.$temp[$i][$j].'</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;" valign="top">';
            $tper = 0;
            //goals_center
            if ($goal_category == 'Property Team')
            {
                if ($i==1)
                {
                    $htmlstring .= $Property_Scouted[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Property_Scouted[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Property_Scouted[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Property_Scouted[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }

                    
                

                    
                }
                /*if ($i==2)
                {
                    $htmlstring .= $Project_Scouted[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Project_Scouted[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    if ($Project_Scouted[$j]>0)
                    {
                        $tper = ($Project_Scouted[$j] / $temp[$i][$j])*100;
                    }
                }*/
                if ($i==2)
                {
                    $htmlstring .= $Client_Inspection[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Client_Inspection[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Client_Inspection[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Client_Inspection[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==3)
                {
                    $htmlstring .= $Transaction[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Transaction[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                   
                    if ($Transaction[$j]>0  && $temp[$i][$j]>0)
                    {
                        $tper = ($Transaction[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==4)
                {
                    $htmlstring .= $Certification_Behaviour_CRM[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Certification_Behaviour_CRM[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Certification_Behaviour_CRM[$j]>0  && $temp[$i][$j]>0)
                    {
                        $tper = ($Certification_Behaviour_CRM[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
            }

            

            if ($goal_category == 'Transaction Team')
            {
                if ($i==1)
                {

                    $htmlstring .= $Property_Scouted[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Property_Scouted[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Property_Scouted[$j]>0  && $temp[$i][$j]>0)
                    {
                        $tper = ($Property_Scouted[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                    
                    
                }
                if ($i==2)
                {
                    $htmlstring .= $NewEnquiries_Generated[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $NewEnquiries_Generated[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($NewEnquiries_Generated[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($NewEnquiries_Generated[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                /*if ($i==3)
                {
                    $htmlstring .= $Enquiries_Generated[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Enquiries_Generated[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    if ($Enquiries_Generated[$j]>0)
                    {
                        $tper = ($Enquiries_Generated[$j] / $temp[$i][$j])*100;
                    }
                }*/
                if ($i==3)
                {
                    $htmlstring .= $New_Clients_Meeting[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $New_Clients_Meeting[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($New_Clients_Meeting[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($New_Clients_Meeting[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==4)
                {
                    $htmlstring .= $Client_Inspection[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Client_Inspection[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Client_Inspection[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Client_Inspection[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==5)
                {
                    $htmlstring .= $Transaction[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Transaction[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Transaction[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Transaction[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==6)
                {
                    $htmlstring .= $Certification_Behaviour_CRM[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Certification_Behaviour_CRM[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Certification_Behaviour_CRM[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Certification_Behaviour_CRM[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
            }


            

            if ($goal_category == 'Business Development Team')
            {
                if ($i==1)
                {
                    $htmlstring .= $New_Clients_Meeting[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $New_Clients_Meeting[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($New_Clients_Meeting[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($New_Clients_Meeting[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                    
                }
                if ($i==2)
                {
                    $htmlstring .= $Enquiries_Generated[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Enquiries_Generated[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Enquiries_Generated[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Enquiries_Generated[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==3)
                {
                    $htmlstring .= $Data_Gathering[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Data_Gathering[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Data_Gathering[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Data_Gathering[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==4)
                {
                    $htmlstring .= $Certification_Behaviour_CRM[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Certification_Behaviour_CRM[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Certification_Behaviour_CRM[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Certification_Behaviour_CRM[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==5)
                {
                    
                }
            }

            if ($goal_category == 'Vertical Head')
            {
                
                if ($i==1)
                {
                    $htmlstring .= $Mandate_Clients[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Mandate_Clients[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Mandate_Clients[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Mandate_Clients[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                    
                }
                if ($i==2)
                {
                    $htmlstring .= $Mandate_Property[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Mandate_Property[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Mandate_Property[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Mandate_Property[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==3)
                {
                    $htmlstring .= $Proposals_for_Mandate_projects[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Proposals_for_Mandate_projects[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Proposals_for_Mandate_projects[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Proposals_for_Mandate_projects[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==4)
                {
                    $htmlstring .= $Transaction[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Transaction[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Transaction[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Transaction[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==5)
                {
                    $htmlstring .= $Certification_Behaviour_CRM[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Certification_Behaviour_CRM[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Certification_Behaviour_CRM[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Certification_Behaviour_CRM[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==5)
                {
                    
                }
            }

            /*$Property_Scouted = array();
            $Project_Scouted = array();
            $Client_Inspection = array();
            $Transaction = array();
            $Certification_Behaviour_CRM = array();
            $New_Clients_Meeting = array();
            $Enquiries_Generated = array();
        
            $Data_Gathering = array();
            $System_Updation = array();
            $Recruitment = array();
            $Training = array();
            $Engagement_Activity = array();
            $CRM_Updation = array();
            $Data_Calling = array();
            $Social_Media = array();
            $Property_Design = array();
            $Project_Design = array();
            $CRM_Training = array();
            $Error_Resolved_Solution = array();*/

            if ($goal_category == 'Front Office')
            {
                if ($i==1)
                {   
                    $print_goal_sub_category = $tgoal_category[$i];
                    if ($tgoal_category[$i]=='New Project Posting')
                    {
                        $htmlstring .= $Project_Scouted[$j];
                        $total_achieved[$i] = $total_achieved[$i] + $Project_Scouted[$j];
                        $total[$i] = $total[$i] + $temp[$i][$j];
                        $tper = 0;
                        $target_achieved = 0;
                        if ($Project_Scouted[$j]>0 && $temp[$i][$j]>0)
                        {
                            $tper = ($Project_Scouted[$j] / $temp[$i][$j])*100;
                            $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                            if ($target_achieved>$goal_per_to_get[$i][$j])
                            {
                                $target_achieved = $goal_per_to_get[$i][$j];
                            }
                            $total_target_achieved = $total_target_achieved + $target_achieved;
                        }

                    }
                    else if ($tgoal_category[$i]=='New Property Posting')
                    {
                        $htmlstring .= $Property_Scouted[$j];
                        $total_achieved[$i] = $total_achieved[$i] + $Property_Scouted[$j];
                        $total[$i] = $total[$i] + $temp[$i][$j];
                        $tper = 0;
                        $target_achieved = 0;
                        if ($Property_Scouted[$j]>0 && $temp[$i][$j]>0)
                        {
                            $tper = ($Property_Scouted[$j] / $temp[$i][$j])*100;
                            $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                            if ($target_achieved>$goal_per_to_get[$i][$j])
                            {
                                $target_achieved = $goal_per_to_get[$i][$j];
                            }
                            $total_target_achieved = $total_target_achieved + $target_achieved;
                        }
                    }
                    else
                    {
                        $htmlstring .= $Properties_Added[$j];
                        $total_achieved[$i] = $total_achieved[$i] + $Properties_Added[$j];
                        $total[$i] = $total[$i] + $temp[$i][$j];
                        $tper = 0;
                        $target_achieved = 0;
                        if ($Properties_Added[$j]>0 && $temp[$i][$j]>0)
                        {
                            $tper = ($Properties_Added[$j] / $temp[$i][$j])*100;
                            $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                            if ($target_achieved>$goal_per_to_get[$i][$j])
                            {
                                $target_achieved = $goal_per_to_get[$i][$j];
                            }
                            $total_target_achieved = $total_target_achieved + $target_achieved;
                        }
                    }
                    
                }
                if ($i==2)
                {
                    $htmlstring .= $NewEnquiries_Generated[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $NewEnquiries_Generated[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($NewEnquiries_Generated[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($NewEnquiries_Generated[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==3)
                {
                    $htmlstring .= $System_Updation[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $System_Updation[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($System_Updation[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($System_Updation[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==4)
                {
                    $htmlstring .= $Certification_Behaviour_CRM[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Certification_Behaviour_CRM[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Certification_Behaviour_CRM[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Certification_Behaviour_CRM[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==5)
                {
                    
                }
            }
            if ($goal_category == 'Graphic Design Team')
            {
                if ($i==1)
                {
                    $htmlstring .= $Social_Media[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Social_Media[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Social_Media[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Social_Media[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                    
                }
                if ($i==2)
                {
                    $htmlstring .= $Property_Design[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Property_Design[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Property_Design[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Property_Design[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                if ($i==3)
                {
                    $htmlstring .= $Project_Design[$j];
                    $total_achieved[$i] = $total_achieved[$i] + $Project_Design[$j];
                    $total[$i] = $total[$i] + $temp[$i][$j];
                    $tper = 0;
                    $target_achieved = 0;
                    if ($Project_Design[$j]>0 && $temp[$i][$j]>0)
                    {
                        $tper = ($Project_Design[$j] / $temp[$i][$j])*100;
                        $target_achieved = round((($goal_per_to_get[$i][$j]*$tper)/100),2);
                        if ($target_achieved>$goal_per_to_get[$i][$j])
                        {
                            $target_achieved = $goal_per_to_get[$i][$j];
                        }
                        $total_target_achieved = $total_target_achieved + $target_achieved;
                    }
                }
                
                if ($i==4)
                {
                    
                }
            }

            $htmlstring .= '</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;" valign="top">'.number_format($tper,2).'<p>('.$target_achieved.'%)</p></td>';
            
        }
        $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;color:red;" valign="top">'.$total_target_achieved.'%</td></tr>';
    }
//goals_end
    $htmlstring .='<tr style="background-color:#000000;font-size:16px;color:#ffffff;"><td style="border-right:1px solid #ffffff;border-bottom:1px solid #000000;font-size:16px;" valign="top">Total</td>';

    for ($n=1;$n<=$no_cols;++$n)
    {
        $htmlstring .= '<td style="border-right:1px solid #ffffff;border-bottom:1px solid #000000;font-size:16px;">'.$total[$n].'</td><td style="border-right:1px solid #ffffff;border-bottom:1px solid #000000;font-size:16px;">'.$total_achieved[$n].'</td><td style="border-right:1px solid #ffffff;border-bottom:1px solid #000000;font-size:16px;"></td>';
    }
    
    $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top"></td></tr>';


    $htmlstring .='<tr><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;color:red;font-size:16px;" valign="top">Balance</td>';

    for ($n=1;$n<=$no_cols;++$n)
    {
        $htmlstring .= '<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:16px;"></td>';
    }
    
    $htmlstring .='<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:18px;" valign="top"></td></tr>';
    //$htmlstring .='</tr>';
    $htmlstring .='</table></div>';

    $htmldata['htmlstring']=$htmlstring;
    $htmldata['print_goal_sub_category'] = $print_goal_sub_category;
    $htmldata['data_calling_query'] = $data_calling_query;
   
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/userdashboard/:start_date/:end_date/:user_id', function($start_date,$end_date,$user_id) use ($app) {
    $sql  = "";
    $db = new DbHandler(); 
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

     if(!empty($user_id)){
    
      $login_user_id = $session['user_id'];
   
      $sql1 = "SELECT manager_id from users as a LEFT JOIN employee as b ON b.emp_id=a.emp_id where a.user_id = $user_id";
      $result1 = $db->getOneRecord($sql1);

      $sql2 = "SELECT emp_id from users  where user_id = $login_user_id";
      $result2 = $db->getOneRecord($sql2);    
       if ($result2['emp_id'] != $result1['manager_id'] && $session['username']!='admin' && $session['user_id']!=$user_id)
        {
            return;
        }
    }


      if ($user_id ==0)
    {
        $user_id = $session['user_id'];

    }
    $this_month = (int)substr($end_date,5,2);
    $this_year = (int)substr($end_date,0,4);

    $start_date .= " 00:00:00";
    $end_date   .= " 23:59:59";
    $temp_sql = "";

    //$this_month=12;
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
    
    $goals_achieved = array();
    $to_url = array();
    $box_color = array();

    $box_color[1] = '#2facaa';
    $box_color[2] = '#2451a4';
    $box_color[3] = '#0a2051';
    $box_color[4] = '#159073';
    $box_color[5] = '#9c0b0e';
    $box_color[6] = '#2facaa';
    $box_color[7] = '#2facaa';
    $box_color[8] = '#2facaa';
    $box_color[9] = '#2facaa';
    $box_color[10] = '#2facaa';
    // SHEKHAR TOP

    $senddata =  array();
    $htmldata = array();

    $htmlstring = '';
    $htmlstring .= '<div class="row" >';
    
    $mainsql = "SELECT * from goals as a LEFT JOIN dropdowns as b ON a.goal_sub_category = b.display_value WHERE a.user_id = $user_id and b.parent_type = a.goal_category and fy_year = '$this_year' group by a.goal_sub_category ORDER BY CAST(b.sequence_number as UNSIGNED)";
    $stmt = $db->getRows($mainsql);
    $total_target_achieved = 0;
    $sql_query = "";
    $abc_sql = "";
    if($stmt->num_rows > 0)
    {
        $i = 1;
        while($row = $stmt->fetch_assoc())
        {
            $goal_id = $row['goal_id'];
            $goal_category = $row['goal_category'];
            $goal_sub_category = $row['goal_sub_category'];
            $goal_per_to_get = $row['goal_per'];
            $goals_to_achieve = $row['goal_'.$month_dis[$this_month]];

            if ($goals_to_achieve>0)
            {
                $color_code = $box_color[$i];
                $goals_achieved[$i] = 0;
                $sql = "nothing";
                if ($goal_sub_category == "CRM Training")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='CRM Training')  ";
                }
                if ($goal_sub_category == "CRM Error_Resolved")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='CRM Error_Resolved')  ";
                    // error_log($sql, 3, "logfile5.log");
                    
                }
                if ($goal_sub_category == "Sharing Properties" || $goal_sub_category == "Commercial  Sharing" || $goal_sub_category == "Residential Sharing")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Sharing Properties' OR activity_sub_type='Commercial  Sharing' OR activity_sub_type='Residential Sharing' )  ";
                }
                if ($goal_sub_category == "Sharing Projects" || $goal_sub_category == "Project Sharing")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Sharing Projects' or activity_sub_type='Project Sharing')  ";
                }
                if ($goal_sub_category == "Reports (Sales, Data Updation, HR & Marketing)")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Reports (Sales, Data Updation, HR & Marketing)')  ";
                }
                if ($goal_sub_category == "CRM Updation")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='CRM Updation')  ";
                }
                
                if ($goal_sub_category == "Account Report & Departmental Monthly Reports")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Account Report & Departmental Monthly Reports')  ";
                }
                if ($goal_sub_category == "Certification + Behaviour+ CRM")
                {
                    $sql = "SELECT * FROM behaviour WHERE user_id = $user_id and month(data_date) = $this_month and year(data_date) = $this_year LIMIT 1";
                    $sql_query .= $sql;
                    $crmdata = $db->getAllRecords($sql);
                    $total_crm = 0;
                    if ($crmdata)
                    {
                       
                        if ($crmdata[0]['monthly_attendance']=="true")
                        {
                            $total_crm = $total_crm + 2;
                        }
                        if ($crmdata[0]['new_idea']=="true")
                        {
                            $total_crm = $total_crm + 2;
                        }
                        if ($crmdata[0]['activity_participation']=="true")
                        {
                            $total_crm = $total_crm + 1;
                        }
                        if ($crmdata[0]['daily_activity']=="true")
                        {
                            $total_crm = $total_crm + 1;
                        }
                        if ($crmdata[0]['response_assigned']=="true")
                        {
                            $total_crm = $total_crm + 2;
                        }
                        if ($crmdata[0]['transaction_process']=="true")
                        {
                            $total_crm = $total_crm + 2;
                        }

                        $goals_achieved[$i] = $total_crm;
                    }
                }
                if ($goal_sub_category == "New Project Posting" || $goal_sub_category == "Project  Scouted")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM project where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') ";
                }
                if ($goal_sub_category == "New Property Posting" || $goal_sub_category == "Property Scouted" || $goal_sub_category == "New Property Generated")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM property where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and project_id = 0 and (created_date BETWEEN '$start_date' AND '$end_date') ";
                }
                if ($goal_sub_category == "New Enquiries Generated")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM enquiry where (created_by = $user_id  or modified_by = $user_id or FIND_IN_SET ($user_id ,assigned)) and (created_date BETWEEN '$start_date' AND '$end_date') ";
                }
                if ($goal_sub_category == "System  Updation")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type LIKE '%System%')";
                    // $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='System  Updation')  ";
                }
                if ($goal_sub_category == "Other Design")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Other Design')  ";
                }
                
                if ($goal_sub_category == "Data Calling")
                {
                    $sql = "SELECT sum(calling_count) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Data Calling')  ";
                }
                if ($goal_sub_category == "Social Media")
                {
                    // $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Social Media')";
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Social Media')  and created_date<'2022-07-01'  ";
                    // error_log($sql, 3, "logfile2.log");
                    $extracteddata = $db->getAllRecords($sql);
                    $tgoals_achived = 0;
                    if ($extracteddata)
                    {
                        $tgoals_achived = $extracteddata[0]['goals_achieved'];
                    }

                    $social_sql = "SELECT * FROM social_media where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET($user_id , assign_to)) and (data_date BETWEEN '$start_date' AND '$end_date') ";
                    $tsql_query = $social_sql;
                    $abc_sql = $social_sql;
                    $social_stmt = $db->getRows($social_sql);
                    $instagram_no_of_posts = 0;
                    $facebook_no_of_posts = 0;
                    $linkedin_no_of_posts = 0;
                    $twitter_no_of_posts = 0;
                    $youtube_no_of_posts = 0;
                    $other_no_of_posts = 0;
                    if($social_stmt->num_rows > 0)
                    {
                        while($row = $social_stmt->fetch_assoc())
                        {
                            if ($row['media_category'] == 'Instagram')
                            {
                                $instagram_no_of_posts =  $instagram_no_of_posts + $row['no_of_posts'];
                            }
                            if ($row['media_category'] == 'Facebook')
                            {
                                $facebook_no_of_posts =  $facebook_no_of_posts + $row['no_of_posts'];
                            }
                            if ($row['media_category'] == 'Linkedin')
                            {
                                $linkedin_no_of_posts =  $linkedin_no_of_posts + $row['no_of_posts'];
                            }
                            if ($row['media_category'] == 'Twitter')
                            {
                                $twitter_no_of_posts =  $twitter_no_of_posts + $row['no_of_posts'];
                            }
                            if ($row['media_category'] == 'Youtube')
                            {
                                $youtube_no_of_posts =  $youtube_no_of_posts + $row['no_of_posts'];
                            }
                            if ($row['media_category'] == 'Other')
                            {
                                $other_no_of_posts =  $other_no_of_posts + $row['no_of_posts'];
                            }

                            if ($row['no_of_likes']>=30 || $row['no_of_views']>=30 || $row['no_of_followers'] >=5 || $row['no_of_subscribers'] >=5 )
                            {
                                $tgoals_achived = $tgoals_achived + 1;
                            }
                        }
                    }
                    

                    /*$instagram_no_of_posts = 0;
                    $instagram_no_of_likes = 0;
                    $instagram_no_of_followers = 0;
                    $instagram_no_of_subscribers = 0;
                    $instagram_no_of_views = 0;
                    $instagram_lead_generation = 0;

                    $facebook_no_of_posts = 0;
                    $facebook_no_of_likes = 0;
                    $facebook_no_of_followers = 0;
                    $facebook_no_of_subscribers = 0;
                    $facebook_no_of_views = 0;
                    $facebook_lead_generation = 0;

                    $linkedin_no_of_posts = 0;
                    $linkedin_no_of_likes = 0;
                    $linkedin_no_of_followers = 0;
                    $linkedin_no_of_subscribers = 0;
                    $linkedin_no_of_views = 0;
                    $linkedin_lead_generation = 0;

                    $twitter_no_of_posts = 0;
                    $twitter_no_of_likes = 0;
                    $twitter_no_of_followers = 0;
                    $twitter_no_of_subscribers = 0;
                    $twitter_no_of_views = 0;
                    $twitter_lead_generation = 0;

                    $youtube_no_of_posts = 0;
                    $youtube_no_of_likes = 0;
                    $youtube_no_of_followers = 0;
                    $youtube_no_of_subscribers = 0;
                    $youtube_no_of_views = 0;
                    $youtube_lead_generation = 0;

                    $other_no_of_posts = 0;
                    $other_no_of_likes = 0;
                    $other_no_of_followers = 0;
                    $other_no_of_subscribers = 0;
                    $other_no_of_views = 0;
                    $other_lead_generation = 0;

                    
                    if($social_stmt->num_rows > 0)
                    {
                        while($row = $social_stmt->fetch_assoc())
                        {
                            
                            if ($row['media_category'] == 'Instagram')
                            {
                                $instagram_no_of_posts =  $instagram_no_of_posts + $row['no_of_posts'];
                                $instagram_no_of_likes =  $instagram_no_of_likes + $row['no_of_likes'];
                                $instagram_no_of_followers =  $instagram_no_of_followers + $row['no_of_followers'];
                                $instagram_no_of_subscribers =  $instagram_no_of_subscribers + $row['no_of_subscribers'];
                                $instagram_no_of_views =  $instagram_no_of_views + $row['no_of_views'];
                                $instagram_lead_generation =  $instagram_lead_generation + $row['lead_generation'];
                            }
                            if ($row['media_category'] == 'Facebook')
                            {
                                $facebook_no_of_posts =  $facebook_no_of_posts + $row['no_of_posts'];
                                $facebook_no_of_likes =  $facebook_no_of_likes + $row['no_of_likes'];
                                $facebook_no_of_followers =  $facebook_no_of_followers + $row['no_of_followers'];
                                $facebook_no_of_subscribers =  $facebook_no_of_subscribers + $row['no_of_subscribers'];
                                $facebook_no_of_views =  $facebook_no_of_views + $row['no_of_views'];
                                $facebook_lead_generation =  $facebook_lead_generation + $row['lead_generation'];
                            }
                            if ($row['media_category'] == 'Linkedin')
                            {
                                $linkedin_no_of_posts =  $linkedin_no_of_posts + $row['no_of_posts'];
                                $linkedin_no_of_likes =  $linkedin_no_of_likes + $row['no_of_likes'];
                                $linkedin_no_of_followers =  $linkedin_no_of_followers + $row['no_of_followers'];
                                $linkedin_no_of_subscribers =  $linkedin_no_of_subscribers + $row['no_of_subscribers'];
                                $linkedin_no_of_views =  $facebook_no_of_views + $row['no_of_views'];
                                $linkedin_lead_generation =  $linkedin_lead_generation + $row['lead_generation'];
                            }
                            if ($row['media_category'] == 'Twitter')
                            {
                                $twitter_no_of_posts =  $twitter_no_of_posts + $row['no_of_posts'];
                                $twitter_no_of_likes =  $twitter_no_of_likes + $row['no_of_likes'];
                                $twitter_no_of_followers =  $twitter_no_of_followers + $row['no_of_followers'];
                                $twitter_no_of_subscribers =  $twitter_no_of_subscribers + $row['no_of_subscribers'];
                                $twitter_no_of_views =  $twitter_no_of_views + $row['no_of_views'];
                                $twitter_lead_generation =  $twitter_lead_generation + $row['lead_generation'];
                            }
                            if ($row['media_category'] == 'Youtube')
                            {
                                $youtube_no_of_posts =  $youtube_no_of_posts + $row['no_of_posts'];
                                $youtube_no_of_likes =  $youtube_no_of_likes + $row['no_of_likes'];
                                $youtube_no_of_followers =  $youtube_no_of_followers + $row['no_of_followers'];
                                $youtube_no_of_subscribers =  $youtube_no_of_subscribers + $row['no_of_subscribers'];
                                $youtube_no_of_views =  $youtube_no_of_views + $row['no_of_views'];
                                $youtube_lead_generation =  $youtube_lead_generation + $row['lead_generation'];
                            }
                            if ($row['media_category'] == 'Other')
                            {
                                $other_no_of_posts =  $other_no_of_posts + $row['no_of_posts'];
                                $other_no_of_likes =  $other_no_of_likes + $row['no_of_likes'];
                                $other_no_of_followers =  $other_no_of_followers + $row['no_of_followers'];
                                $other_no_of_subscribers =  $other_no_of_subscribers + $row['no_of_subscribers'];
                                $other_no_of_views =  $other_no_of_views + $row['no_of_views'];
                                $other_lead_generation =  $other_lead_generation + $row['lead_generation'];
                            }


                        }
                    }
                    
                    if ($instagram_no_of_posts >=70)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($facebook_no_of_posts >=60)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($linkedin_no_of_posts >=60)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($twitter_no_of_posts >=50)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($youtube_no_of_posts >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($other_no_of_posts >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }


                    if ($instagram_no_of_likes >=30)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($facebook_no_of_likes >=30)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($linkedin_no_of_likes >=30)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($twitter_no_of_likes >=30)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($youtube_no_of_likes >=30)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($other_no_of_likes >=30)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($instagram_no_of_followers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($facebook_no_of_followers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($linkedin_no_of_followers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($twitter_no_of_followers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($youtube_no_of_followers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($other_no_of_followers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }


                    if ($instagram_no_of_subscribers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($facebook_no_of_subscribers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($linkedin_no_of_subscribers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($twitter_no_of_subscribers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($youtube_no_of_subscribers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($other_no_of_subscribers >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($instagram_no_of_views >=30)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($facebook_no_of_views >=30)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($linkedin_no_of_views >=30)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($twitter_no_of_views >=30)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($youtube_no_of_views >=305)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($other_no_of_views >=30)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($instagram_lead_generation >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($facebook_lead_generation >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($linkedin_lead_generation >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }

                    if ($twitter_lead_generation >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($youtube_lead_generation >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }
                    if ($other_lead_generation >=5)
                    {
                        $tgoals_achived = $tgoals_achived + 1;
                    }*/
                    
                    $goals_achieved[$i] = $tgoals_achived;
                    $abc_sql.="result".$goals_achieved[$i]."done";
                    
                }
                if ($goal_sub_category == "Property Design")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Property Design')  ";
                }
                if ($goal_sub_category == "Project Design")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Project Design')  ";

                }
                
                if ($goal_sub_category == "No. Of Recruitment" || $goal_sub_category == "Recruitment")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='No. Of Recruitment' or  activity_sub_type='Recruitment')  ";
                }
                if ($goal_sub_category == "Training" || $goal_sub_category == "HR Training")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Training' OR activity_sub_type='HR Training')  ";
                }
                if ($goal_sub_category == "Engagement Activity")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Engagement Activity')  ";
                }
               
                if ($goal_sub_category == "Client Inspection" || $goal_sub_category == "Broker Inspection")
                {
                    // $sql1 = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Site Visit') and (activity_sub_type='$goal_sub_category') GROUP BY day(created_date),client_id ";
                    //$temp_sql = $sql1;
                    
                    $sql1 = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Site Visit') GROUP BY day(created_date),client_id ";

                    $stmt1 = $db->getRows($sql1); 
                    $inspection_count = 0;
                    if($stmt1->num_rows > 0)
                    {
                        while($row1 = $stmt1->fetch_assoc())
                        {
                            $inspection_count = $inspection_count + 1;
                        }
                    }
                    $goals_achieved[$i] = $inspection_count;
                    $stmt1->close();
                    
                }

                if ($goal_sub_category == "Transaction")
                {
                    //$sql = "SELECT sum(b.our_brokerage) as goals_achieved FROM agreement_details  as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id where (a.contribution_to=$user_id) and (b.created_date BETWEEN '$start_date' AND '$end_date') and (UPPER(b.agreement_stage) = 'BROKERAGE BILL SUBMISSION' ||  UPPER(b.agreement_stage) = 'TESTIMONIAL') ";
                    $stage_query = " and b.agreement_stage IN(".getstages().") ";

                    $sql = "SELECT sum(b.our_brokerage) as goals_achieved FROM agreement_details  as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id WHERE (a.contribution_to=$user_id) and (b.created_date BETWEEN '$start_date' AND '$end_date') ".$stage_query." ";
                    $temp_sql .= $sql;

                }
                
                if ($goal_sub_category == "New Clients Meeting" || $goal_sub_category == "New Broker Meeting" || $goal_sub_category == "Retail Meeting" || $goal_sub_category == "New Developer Meeting")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') ";
                }
                
                if ($goal_sub_category == "Mandate  Clients")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Mandate  Clients')  ";
                }
                if ($goal_sub_category == "Mandate Property")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Mandate Property')  ";
                }
                if ($goal_sub_category == "Proposals for Mandate projects")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Proposals for Mandate projects')  ";
                }
                if ($goal_sub_category == "Residential Project")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Residential Project')  ";
                }

                if ($goal_sub_category == "Commercial Project")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Commercial Project')  ";
                }

                if ($goal_sub_category == "Lead generation")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Lead generation')  ";
                }

                if ($goal_sub_category == "Creative Activities")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Creative Activities')  ";
                }
                //new add pks 02092023
                if ($goal_sub_category == "Other Activity")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_sub_type='Other Activity') ";    
                }
                if ($goal_sub_category == "System Updation")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_sub_type LIKE '%System%') ";    
                    // $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_sub_type='System Updation') ";    
                }
                if ($goal_sub_category == "Presentation Update")
                {
                    $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_sub_type='Presentation Update') ";    
                }
                
                // if ($goal_sub_category == "Social Media")
                // {
                //     $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_sub_type='Social Media') ";
                // }

                if ($goal_sub_category == "Certification + Behaviour+ CRM" || $goal_sub_category == "Client Inspection" || $goal_sub_category == "Broker Inspection" || $goal_sub_category == "Social Media")
                {
                    //  $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and ((activity_sub_type='Certification + Behaviour+ CRM') or (activity_sub_type='Social Media') or (activity_sub_type='Broker Inspection') or (activity_sub_type='Client Inspection'))";
                }
                else{
                    // error_log($sql, 3, "logfile5.log");
                    $extracteddata = $db->getAllRecords($sql);
                    if ($extracteddata)
                    {
                        $goals_achieved[$i] = $extracteddata[0]['goals_achieved'];
                    }
                }

                $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
            
                $goal_percent = 0;
                $target_achieved = 0;
                if ($goals_achieved[$i]>0 && $goals_to_achieve >0)
                {
                    $goal_percent = round((($goals_achieved[$i] / $goals_to_achieve)*100),2);
                    $target_achieved = round((($goal_per_to_get*$goal_percent)/100),2);
                    if ($target_achieved>$goal_per_to_get)
                    {
                        $target_achieved = $goal_per_to_get;
                    }
                    $total_target_achieved = $total_target_achieved + $target_achieved;
                }
                
                $to_url = "#/dashboardmore/".$goal_id;
                $htmlstring .= '<div class="col-lg-2 col-xs-6 smallbox" >
                    <div class="small-box" style="background-color:'.$color_code.';border-radius:15px;">
                    <div class="inner">
                        <p class="nowrapping catname" style="color:#ffffff;">'.$goal_sub_category.'</p>
                        <p class="">'.$goals_to_achieve.'<span style="float:right;">('.$goals_achieved[$i].')</span></p><p style="text-align:center;font-size:18px;">'.$target_achieved.'%</p>
                    </div>
                    <!--div class="icon">
                        <i class="ion ion-bag"></i>
                    </div-->
                    <!--a href="'.$to_url.'" class="small-box-footer ">More info <i class="fa fa-arrow-circle-right"></i></a-->

                    <a href="javascript:void(0);" class="small-box-footer " ng-click="userdashboardmore(\''.$goal_category.'\',\''.$goal_sub_category.'\','.$user_id.')">More info <i class="fa fa-arrow-circle-right"></i></a>

                    </div>
                </div>';
                $i++;
            }
    
        }
    }  
    $htmlstring .= '</div><p style="text-align:center;color:#b50909;font-size:18px;">Target Achieved:'.$total_target_achieved.'</p>';
    

    $start_quarter = "2021-09-01";
    $end_quarter = "2021-11-30";
    
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
    $stage_query = " and a.agreement_stage IN(".getstages().") ";

    //$sql = "SELECT sum(a.our_brokerage) as goals_achieved FROM agreement as a where (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to)) and a.deal_date BETWEEN '$start_quarter' AND '$end_quarter'  and (UPPER(a.agreement_stage) = 'BROKERAGE BILL SUBMISSION' ||  UPPER(a.agreement_stage) = 'TESTIMONIAL')  " ;

                    // error_log($sql, 3, "logfile2.log");
    $sql = "SELECT sum(a.our_brokerage) as goals_achieved FROM agreement as a where (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET($user_id , a.assign_to)) and a.deal_date BETWEEN '$start_quarter' AND '$end_quarter' ".$stage_query." " ;

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

    $htmldata['htmlstring']=$htmlstring;
    $htmldata['total_target_achieved']=$total_target_achieved;
    $htmldata['temp_sql']=$temp_sql;
    $htmldata['eligibility']=$eligibility;
    $htmldata['goal_per_to_get']=$goal_per_to_get;
    $htmldata['goal_percent']=$goal_percent;
    $htmldata['goals_achieved']=$goals_achieved;
    $htmldata['goals_to_achieve']=$goals_to_achieve;
    $htmldata['target_achieved']=$target_achieved;
    $htmldata['sql']=$sql_query;
    $htmldata['abc_sql']=$abc_sql;

    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

function getstages()
{
    $stages="'Agreement Approval','Allotment letter from builder','Bank Sanction letter','Brokerage Bill Submission','Brokerage bill submission at the time of Registration','Brokerage Confirmation Letter Signing','Brokerage confirmation letter signing','Complete own contribution','Documentation','Documentation (Resale Draft Agreement)','Follow up with client and builder till the time of possession','Keys Handover','List of Documents & transfer charges handover to the society','List of documents handover to the Buyer','Maintenance & electricity last bill paid copy from the seller','Maintenance and electricity last bill paid copy from the seller','No due certificate from society','NOC from Builder','NOC from the society in the Bank format','Payment from Bank','Payment of Maintenance and other charges','Payment receiving from bank','Possession','Possession letter','Receipt to be taken','Sanction letter from Bank','Stamp duty & Registration','Stamp duty and Registration','Stamp Duty, Registration and Police Verification','Testimonial','Token Cheque handover to builder List of documents from builder to be submitted to bank'";
    return $stages;
}
$app->get('/goals_achieved/:start_date/:end_date', function($start_date,$end_date) use ($app) {
    // error_log("pks1149", 3, "logfile5.log");
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    if ($user_id ==0)
    {
        $user_id = $session['user_id'];
    }
    $this_month = (int)substr($end_date,5,2);
    $this_year = (int)substr($end_date,1,4);
    $start_date .= " 00:00:00";
    $end_date   .= " 23:59:59";

    //$this_month=12;
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
    
    $goals_achieved = array();
    $to_url = array();
    $box_color = array();

    $box_color[1] = '#2facaa';
    $box_color[2] = '#2451a4';
    $box_color[3] = '#0a2051';
    $box_color[4] = '#159073';
    $box_color[5] = '#9c0b0e';
    $box_color[6] = '#2facaa';
    $box_color[7] = '#2facaa';
    $box_color[8] = '#2facaa';
    $box_color[9] = '#2facaa';
    $box_color[10] = '#2facaa';
// SHEKHAR TOP

    $senddata =  array();
    $htmldata = array();

    $htmlstring = '';

    $tsql = "SELECT *,CONCAT(b.salu,' ',b.fname,' ',b.lname) as name FROM users as a LEFT JOIN employee as b ON a.emp_id = b.emp_id WHERE a.status !='InActive' and a.username!='admin' ORDER BY b.lname,b.fname";
    $tstmt = $db->getRows($tsql);
    
    if ($tstmt->num_rows > 0)
    {
        while($trow = $tstmt->fetch_assoc())
        {
            
            $user_id = $trow['user_id'];
            $mainsql = "SELECT * from goals as a LEFT JOIN dropdowns as b ON a.goal_sub_category = b.display_value WHERE a.user_id = $user_id and b.parent_type = a.goal_category group by a.goal_sub_category ORDER BY CAST(b.sequence_number as UNSIGNED)";
            $stmt = $db->getRows($mainsql);
            $total_target_achieved = 0;

            if($stmt->num_rows > 0)
            {
                $first = "Yes";
                

                $i = 1;
                while($row = $stmt->fetch_assoc())
                {
                    $goal_id = $row['goal_id'];
                    $goal_category = $row['goal_category'];
                    $goal_sub_category = $row['goal_sub_category'];
                    $goal_per_to_get = $row['goal_per'];
                    $goals_to_achieve = $row['goal_'.$month_dis[$this_month]];
                    if ($first=="Yes")
                    {
                        $htmlstring .= '<h3>Employee Name:'.$trow['name'].'</h3>';
                        $htmlstring .= '<p style="font-size:18px;">'.$goal_category.'</p>';
                        $htmlstring .= '<div class="row" >';
                        $first = "No";
                    }

                    if ($goals_to_achieve>0)
                    {
                        $color_code = $box_color[$i];
                        $goals_achieved[$i] = 0;
                        $sql = "nothing";
                        if ($goal_sub_category == "CRM Training")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='CRM Training')  ";
                        }
                        
                        if ($goal_sub_category == "CRM Error_Resolved")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='CRM Error_Resolved')  ";
                        }

                        if ($goal_sub_category == "Sharing Properties" || $goal_sub_category == "Commercial  Sharing" || $goal_sub_category == "Residential Sharing")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Sharing Properties' OR activity_sub_type='Commercial  Sharing' OR activity_sub_type='Residential Sharing' )  ";
                        }
                        if ($goal_sub_category == "Sharing Projects")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Sharing Projects')  ";
                        }
                        if ($goal_sub_category == "Reports (Sales, Data Updation, HR & Marketing)")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Reports (Sales, Data Updation, HR & Marketing)')  ";
                        }
                        if ($goal_sub_category == "CRM Updation")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='CRM Updation')  ";
                        }
                        
                        if ($goal_sub_category == "Account Report & Departmental Monthly Reports")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Account Report & Departmental Monthly Reports')  ";
                        }
                        if ($goal_sub_category == "Certification + Behaviour+ CRM")
                        {
                            $sql = "SELECT * FROM behaviour WHERE user_id = $user_id and month(data_date) = $this_month and year(data_date) = $end_year LIMIT 1";
                            $crmdata = $db->getAllRecords($sql);
                            $total_crm = 0;
                            if ($crmdata)
                            {
                            
                                if ($crmdata[0]['monthly_attendance']=="true")
                                {
                                    $total_crm = $total_crm + 2;
                                }
                                if ($crmdata[0]['new_idea'])
                                {
                                    $total_crm = $total_crm + 2;
                                }
                                if ($crmdata[0]['activity_participation']=="true")
                                {
                                    $total_crm = $total_crm + 1;
                                }
                                if ($crmdata[0]['daily_activity']=="true")
                                {
                                    $total_crm = $total_crm + 1;
                                }
                                if ($crmdata[0]['response_assigned']=="true")
                                {
                                    $total_crm = $total_crm + 2;
                                }
                                if ($crmdata[0]['transaction_process']=="true")
                                {
                                    $total_crm = $total_crm + 2;
                                }

                                $goals_achieved[$i] = $total_crm;
                            }
                        }
                        if ($goal_sub_category == "New Project Posting" || $goal_sub_category == "Project  Scouted")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM project where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') ";
                        }
                        if ($goal_sub_category == "New Property Posting" || $goal_sub_category == "Property Scouted" || $goal_sub_category == "New Property Generated")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM property where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to))  and project_id = 0 and (created_date BETWEEN '$start_date' AND '$end_date') ";
                        }
                        if ($goal_sub_category == "New Enquiries Generated")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM enquiry where (created_by = $user_id  or modified_by = $user_id or FIND_IN_SET ($user_id ,assigned)) and (created_date BETWEEN '$start_date' AND '$end_date') ";
                        }
                        if ($goal_sub_category == "System  Updation")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='System  Updation')  ";
                        }
                        if ($goal_sub_category == "Other Design")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Other Design')  ";
                        }
                        if ($goal_sub_category == "Data Calling")
                        {
                            $sql = "SELECT sum(calling_count) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Data Calling')  ";
                        }
                        if ($goal_sub_category == "Social Media")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Social Media')";
                        }

                        if ($goal_sub_category == "Property Design")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Property Design')  ";
                        }
                        if ($goal_sub_category == "Project Design")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Project Design')  ";

                        }
                        
                        if ($goal_sub_category == "No. Of Recruitment" || $goal_sub_category == "Recruitment")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='No. Of Recruitment' or  activity_sub_type='Recruitment')  ";
                        }
                        if ($goal_sub_category == "Training" || $goal_sub_category == "HR Training")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Training' OR activity_sub_type='HR Training')  ";
                        }
                        if ($goal_sub_category == "Engagement Activity")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Engagement Activity')  ";
                        }
                    
                        if ($goal_sub_category == "Client Inspection" || $goal_sub_category == "Broker Inspection")
                        {
                            $sql1 = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Site Visit') GROUP BY day(created_date),client_id ";

                            $stmt1 = $db->getRows($sql1);
                            $inspection_count = 0;
                            if($stmt1->num_rows > 0)
                            {
                                while($row1 = $stmt1->fetch_assoc())
                                {
                                    $inspection_count = $inspection_count + 1;
                                }
                            }
                            $goals_achieved[$i] = $inspection_count;
                            $stmt1->close();
                        }

                        if ($goal_sub_category == "Transaction")
                        {
                            $stage_query = " and b.agreement_stage IN(".getstages().") ";

                            //$sql = "SELECT sum(b.our_brokerage) as goals_achieved FROM agreement_details  as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id where (a.contribution_to=$user_id) and (b.created_date BETWEEN '$start_date' AND '$end_date')  and (UPPER(a.agreement_stage) = 'BROKERAGE BILL SUBMISSION' ||  UPPER(a.agreement_stage) = 'TESTIMONIAL') ";

                            $sql = "SELECT sum(b.our_brokerage) as goals_achieved FROM agreement_details  as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id where (a.contribution_to=$user_id) and (b.created_date BETWEEN '$start_date' AND '$end_date') ".$stage_query." ";


                        }
                        
                        if ($goal_sub_category == "New Clients Meeting"  || $goal_sub_category == "New Broker Meeting"  || $goal_sub_category == "Retail Meeting" || $goal_sub_category == "New Developer Meeting")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') ";
                        }
                        
                        if ($goal_sub_category == "Mandate  Clients")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Mandate  Clients')  ";
                        }
                        if ($goal_sub_category == "Mandate Property")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Mandate Property')  ";
                        }
                        if ($goal_sub_category == "Proposals for Mandate projects")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Proposals for Mandate projects')  ";
                        }

                        if ($goal_sub_category == "Residential Project")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Residential Project')  ";
                        }

                        if ($goal_sub_category == "Commercial Project")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Commercial Project')  ";
                        }

                        if ($goal_sub_category == "Lead generation")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Lead generation')  ";
                        }

                        if ($goal_sub_category == "Creative Activities")
                        {
                            $sql = "SELECT count(*) as goals_achieved FROM activity where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Creative Activities')  ";
                        }
                        
                        if ($goal_sub_category == "Certification + Behaviour+ CRM" || $goal_sub_category == "Client Inspection" || $goal_sub_category == "Broker Inspection")
                        {
                        }
                        else{
                            $extracteddata = $db->getAllRecords($sql);
                            if ($extracteddata)
                            {
                                $goals_achieved[$i] = $extracteddata[0]['goals_achieved'];
                            }
                        }
                        $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                    
                        $goal_percent = 0;
                        $target_achieved = 0;
                        if ($goals_achieved[$i]>0 && $goals_to_achieve >0)
                        {
                            $goal_percent = round((($goals_achieved[$i] / $goals_to_achieve)*100),2);
                            $target_achieved = round((($goal_per_to_get*$goal_percent)/100),2);
                            if ($target_achieved>$goal_per_to_get)
                            {
                                $target_achieved = $goal_per_to_get;
                            }
                            $total_target_achieved = $total_target_achieved + $target_achieved;
                        }
                        
                        $to_url = "#/dashboardmore/".$goal_id;
                        $htmlstring .= '<div class="col-lg-2 col-xs-6 smallbox" >
                            <div class="small-box" style="background-color:'.$color_code.';border-radius:15px;">
                            <div class="inner">
                                <p class="nowrapping catname" style="color:#ffffff;">'.$goal_sub_category.'</p>
                                <p class="">'.$goals_to_achieve.'<span style="float:right;">('.$goals_achieved[$i].')</span></p><p style="text-align:center;font-size:18px;">'.$target_achieved.'%</p>
                            </div>
                            </div>
                        </div>';
                        $i++;
                    }
            
                }
            }
            
            if ($total_target_achieved>0)
            {  
                $htmlstring .= '<div class="clearfix"></div><p style="text-align:center;color:#b50909;font-size:18px;">Target Achieved:'.$total_target_achieved.'</p>';
            }
            $htmlstring .= '</div>';
        }
    }
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['total_target_achieved']=$total_target_achieved;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/crm_activities/:start_date/:end_date', function($start_date,$end_date) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    if ($user_id ==0)
    {
        $user_id = $session['user_id'];
    }
    $this_month = (int)substr($end_date,5,2);
    // SHEKHAR TOP

    $senddata =  array();
    $htmldata = array();

    $htmlstring = '';
    $htmlstring .= '<div class="box-body table-responsive">
                        <table class="table table-bordered table-striped"  >
                            <thead>';
    $htmlstring .= '            <tr>
                                    <th style="width:250px;">Employee Name</th>
                                    <th style="width:100px;text-align:right;">Project Created</th>
                                    <th style="width:100px;text-align:right;">Project Modified</th>
                                    <th style="width:100px;text-align:right;">Project Assigned To</th>
                                    <th style="width:100px;text-align:right;">Properties Created</th>
                                    <th style="width:100px;text-align:right;">Properties Modified</th>
                                    <th style="width:100px;text-align:right;">Assigned To</th>
                                    <th style="width:100px;text-align:right;">Properties Shared</th>
                                    <th style="width:100px;text-align:right;">Enquiries Created</th>
                                    <th style="width:100px;text-align:right;">Enquiries Modified</th>
                                    <th style="width:100px;text-align:right;">Enquiries Assigned</th>
                                    <th style="width:100px;text-align:right;">Site Visit</th>
                                    <th style="width:100px;text-align:right;">Mails sent</th>
                                    <th style="width:100px;text-align:right;">SMS sent</th>
                                </tr>
                            </thead>
                        <tbody>';

    $tsql = "SELECT *,CONCAT(b.salu,' ',b.fname,' ',b.lname) as name FROM users as a LEFT JOIN employee as b ON a.emp_id = b.emp_id WHERE a.status !='InActive' and a.username!='admin' ORDER BY b.lname,b.fname";
    $tstmt = $db->getRows($tsql);
    
    if ($tstmt->num_rows > 0)
    {
        while($trow = $tstmt->fetch_assoc())
        {
            $user_id = $trow['user_id'];
            $name = $trow['name'];
            $mainsql = "SELECT * FROM property where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to))  and project_id = 0 and (created_date BETWEEN '$start_date' AND '$end_date') ";
            if ($this_month=='00')
            {
                $mainsql = "SELECT * FROM property where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to))  and project_id = 0  ";
            }
            $stmt = $db->getRows($mainsql);
            $properties_created = 0;
            $properties_modified = 0;
            $assigned_to = 0;
            if($stmt->num_rows > 0)
            {
                while($row = $stmt->fetch_assoc())
                {
                    if ($row['created_by']==$user_id)
                    {
                        $properties_created = $properties_created + 1;
                    }
                    if ($row['modified_by']==$user_id)
                    {
                        $properties_modified = $properties_modified + 1;
                    }
                    $str = explode(",",$row['assign_to']);
                    if (in_array($user_id, $str))
                    {
                        $assigned_to = $assigned_to + 1;
                    }
                }
            }

            $properties_shared = 0;
            if ($this_month=='00')
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as properties_shared FROM property WHERE (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and share_on_website='Yes'" );
            }
            else
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as properties_shared FROM property WHERE (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') and share_on_website='Yes'");
            }
            if ($extracteddata)
            {
                $properties_shared = $extracteddata[0]['properties_shared'];
            }

            $mails_sent = 0;
            if ($this_month=='00')
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as mails_sent FROM client_mails WHERE created_by = $user_id ");
            }
            else
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as mails_sent FROM client_mails WHERE created_by = $user_id and (created_date BETWEEN '$start_date' AND '$end_date')");

            }
            if ($extracteddata)
            {
                $mails_sent = $extracteddata[0]['mails_sent'];
            }

            $project_created = 0;
            if ($this_month=='00')
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as project_created FROM project WHERE created_by = $user_id");
            }
            else 
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as project_created FROM project WHERE created_by = $user_id and (created_date BETWEEN '$start_date' AND '$end_date')");
            }
            if ($extracteddata)
            {
                $project_created = $extracteddata[0]['project_created'];
            }

            $project_modified = 0;
            if ($this_month=='00')
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as project_modified FROM project WHERE modified_by = $user_id");
            }
            else
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as project_modified FROM project WHERE modified_by = $user_id and (created_date BETWEEN '$start_date' AND '$end_date')");
            }
            if ($extracteddata)
            {
                $project_modified = $extracteddata[0]['project_modified'];
            }

            $project_assigned = 0;

            if ($this_month=='00')
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as project_assigned FROM project WHERE FIND_IN_SET ($user_id ,assign_to) ");
            }
            else
            {

                $extracteddata = $db->getAllRecords("SELECT count(*) as project_assigned FROM project WHERE FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date')");
            }
            if ($extracteddata)
            {
                $project_assigned = $extracteddata[0]['project_assigned'];
            }

            $enquiries_created = 0;
            if ($this_month=='00')
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as enquiries_created FROM enquiry WHERE created_by = $user_id ");
            }
            else
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as enquiries_created FROM enquiry WHERE created_by = $user_id and (created_date BETWEEN '$start_date' AND '$end_date')");
            }
            if ($extracteddata)
            {
                $enquiries_created = $extracteddata[0]['enquiries_created'];
            }

            $enquiries_modified = 0;
            if ($this_month=='00')
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as enquiries_modified FROM enquiry WHERE modified_by = $user_id");
            }
            else
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as enquiries_modified FROM enquiry WHERE modified_by = $user_id and (created_date BETWEEN '$start_date' AND '$end_date')");
            }
            
            if ($extracteddata)
            {
                $enquiries_modified = $extracteddata[0]['enquiries_modified'];
            }

            $enquiries_assigned = 0;
            if ($this_month=='00')
            {
               $extracteddata = $db->getAllRecords("SELECT count(*) as enquiries_assigned FROM enquiry WHERE FIND_IN_SET ($user_id ,assigned) ");
            }
            else
            {
                $extracteddata = $db->getAllRecords("SELECT count(*) as enquiries_assigned FROM enquiry WHERE FIND_IN_SET ($user_id ,assigned) and (created_date BETWEEN '$start_date' AND '$end_date')"); 
            }
            if ($extracteddata)
            {
                $enquiries_assigned = $extracteddata[0]['enquiries_assigned'];
            }

            $site_visit = 0;

            /*$extracteddata = $db->getAllRecords("SELECT count(*) as site_visit FROM activity WHERE (created_by = $user_id or modified_by = $user_id) and activity_type = 'Site Visit'  and (created_date BETWEEN '$start_date' AND '$end_date')");
            if ($extracteddata)
            {
                $site_visit = $extracteddata[0]['site_visit'];
            }*/
            if ($this_month=='00')
            {
               $sql1 = "SELECT count(*) as site_visit FROM activity where (created_by = $user_id or modified_by = $user_id)  and (activity_type='Site Visit') GROUP BY day(created_date),client_id ";
            }
            else
            {
               $sql1 = "SELECT count(*) as site_visit FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Site Visit') GROUP BY day(created_date),client_id ";
            }
            $temp_sql = $sql1;

            $stmt1 = $db->getRows($sql1); 
            $inspection_count = 0;
            if($stmt1->num_rows > 0)
            {
                while($row1 = $stmt1->fetch_assoc())
                {
                    $inspection_count = $inspection_count + 1;
                }
            }
            $site_visit = $inspection_count;
            $stmt1->close();

            
            $sms_sent = 0;
            if ($this_month=='00')
            {
                $extracteddata = $db->getAllRecords("SELECT count(*)  as sms_sent FROM client_sms WHERE created_by = $user_id ");
            }
            else
            {
                $extracteddata = $db->getAllRecords("SELECT count(*)  as sms_sent FROM client_sms WHERE created_by = $user_id and (created_date BETWEEN '$start_date' AND '$end_date')");
            }
            if ($extracteddata)
            {
                $sms_sent = $extracteddata[0]['sms_sent'];
            }
            //$total = 0;
            //$total = $properties_created + $properties_modified + $assigned_to + $mails_sent + /////$sms_sent + $project_created + $project_modified +  $properties_shared + $site_visit + $enquiries_created;
            //if ($total>0)
            //{
                $htmlstring .= '<tr><td>'.$name.'</td>';
                $htmlstring .= '<td style="text-align:right;width:100px;">'.$project_created.'</td><td style="text-align:right;width:100px;">'.$project_modified.'</td><td style="text-align:right;width:100px;">'.$project_assigned.'</td><td style="text-align:right;width:100px;">'.$properties_created.'</td><td style="text-align:right;width:100px;">'.$properties_modified.'</td><td style="text-align:right;width:100px;">'.$assigned_to.'</td><td style="text-align:right;width:100px;">'.$properties_shared.'</td><td style="text-align:right;width:100px;">'.$enquiries_created.'</td><td style="text-align:right;width:100px;">'.$enquiries_modified.'</td><td style="text-align:right;width:100px;">'.$enquiries_assigned.'</td><td style="text-align:right;width:100px;">'.$site_visit.'</td><td style="text-align:right;width:100px;">'.$mails_sent.'</td><td style="text-align:right;width:100px;">'.$sms_sent.'</td></tr>';
            //}
        }
    }
    $htmlstring .= '</table></div>';
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/activity_log/:start_date/:end_date', function($start_date,$end_date) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    if ($user_id ==0)
    {
        $user_id = $session['user_id'];
    }
    $this_month = (int)substr($end_date,5,2);
    // SHEKHAR TOP

    $senddata =  array();
    $htmldata = array();

    $htmlstring = '';
    $htmlstring .= '<div class="box-body table-responsive">
                        <table class="table table-bordered table-striped"  >
                            <thead>';
    $htmlstring .= '            <tr>
                                    <th style="width:150px;">Date & Time</th>
                                    <th>Activity</th>
                                </tr>
                            </thead>
                        <tbody>';

    $tsql = "SELECT *,CONCAT(b.salu,' ',b.fname,' ',b.lname) as name FROM users as a LEFT JOIN employee as b ON a.emp_id = b.emp_id WHERE a.status !='InActive' and a.username!='admin' ORDER BY b.lname,b.fname";
    $tstmt = $db->getRows($tsql);
    
    if ($tstmt->num_rows > 0)
    {
        while($trow = $tstmt->fetch_assoc())
        {
            $user_id = $trow['user_id'];
            $name = $trow['name'];
            $first = "Yes";
            
            $mainsql = "SELECT *,DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS created_date FROM crm_activity where (created_by = $user_id ) and (date(created_date) BETWEEN '$start_date' AND '$end_date') ORDER BY created_date DESC";
            $stmt = $db->getRows($mainsql);
            if($stmt->num_rows > 0)
            {
                while($row = $stmt->fetch_assoc())
                {
                    if ($first=="Yes")
                    {
                        $htmlstring .= '<tr><td style="font-size:18px;" colspan="2">User Name:-'.$name.'</td></tr>';
                        $first = "No";
                    }
                    $htmlstring .= '<tr><td>'.$row['created_date'].'</td><td>'.$row['activity_data'].'<td></tr>';
                }
            }
            
        }
    }
    $htmlstring .= '</table></div>';
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/activity_data/:activity_data', function($activity_data) use ($app) {
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
    $query = "INSERT INTO crm_activity (activity_data, created_by ,created_date)   VALUES('$activity_data','$created_by','$created_date' )";
    $result = $db->insertByQuery($query);
    $response["message"] = "done";
    echoResponse(200, $response);
});


// SHEKHAR 

$app->get('/userdashboard_org/:start_date/:end_date/:user_id', function($start_date,$end_date,$user_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    if ($user_id ==0)
    {
        $user_id = $session['user_id'];
    }
    $this_month = (int)substr($end_date,5,2);

    //$this_month=12;
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
    
    $goals_achieved = array();
    $to_url = array();

    $senddata =  array();
    $htmldata = array();

    $htmlstring = '';
    $htmlstring .= '<div class="row" >';
    
    $sql = "SELECT * from goals as a LEFT JOIN dropdowns as b ON a.goal_sub_category = b.display_value WHERE a.user_id = $user_id and b.parent_type = a.goal_category group by a.goal_sub_category ORDER BY CAST(b.sequence_number as UNSIGNED)";
    $stmt = $db->getRows($sql);
    $total_target_achieved = 0;
    if($stmt->num_rows > 0)
    {
        $i = 1;
        while($row = $stmt->fetch_assoc())
        {
            $goal_id = $row['goal_id'];
            $goal_category = $row['goal_category'];
            $goal_sub_category = $row['goal_sub_category'];
            $goal_per_to_get = $row['goal_per'];
            $goals_to_achieve = $row['goal_'.$month_dis[$this_month]];
            
            if ($goal_category == 'Property Team')
            {
                if ($i==1)
                {
                    $color_code = '#2facaa';
                    
                    $goals_achieved[$i] = 0;
                    // CHANGES as on 08.04.2021
                    //$activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and property_id > 0 " );
                    //(b.created_by != $user_id and FIND_IN_SET ($user_id ,b.assigned))

                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM property where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') " );

                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;


                }
                /*if ($i==2)
                {
                    $color_code = '#d56523';
                    $goals_achieved[$i] = 0;
                    //$activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and project_id > 0 " );
                    
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date') " );

                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;

                }*/
                if ($i==2)
                {
                    $color_code = '#2451a4';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Site Visit') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==3)
                {
                    $color_code = '#0a2051';
                    $goals_achieved[$i] = 0;
                    /*$activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }*/
                    $activitydata = $db->getAllRecords("SELECT sum(contribution_amount) as deal_amount FROM agreement_details  as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id where (a.contribution_to=$user_id) and (b.created_date BETWEEN '$start_date' AND '$end_date') " );
                    
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_amount'];

                    }

                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==4)
                {
                    $color_code = '#159073';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==5)
                {
                    $color_code = '#9c0b0e';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
            }
            if ($goal_category == 'Transaction Team')
            {
                if ($i==1)
                {
                    $color_code = '#2facaa';
                    $goals_achieved[$i] = 0;

                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM property where (created_by = $user_id or modified_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') " );

                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }


                    
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;

                }
                if ($i==2)
                {
                    $color_code = '#d56523';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as Enquiries_Generated_count FROM enquiry where (created_by = $user_id  or modified_by = $user_id or FIND_IN_SET ($user_id ,assigned)) and (created_date BETWEEN '$start_date' AND '$end_date') " );

                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['Enquiries_Generated_count'];

                    }

                    
                    /*$activitydata = $db->getAllRecords("SELECT count(*) as Enquiries_Generated_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and enquiry_id > 0 " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['Enquiries_Generated_count'];

                    }*/

                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==3)
                {
                    $color_code = '#2451a4';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }    
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category; 
                }              


                if ($i==4)
                {
                    $color_code = '#2451a4';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_inspection_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Site Visit')  GROUP BY day(created_date) " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_inspection_count'];

                    }
                    
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==5)
                {
                    $color_code = '#0a2051';
                    $goals_achieved[$i] = 0;
                    /*$activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }*/
                    $activitydata = $db->getAllRecords("SELECT sum(contribution_amount) as deal_amount FROM agreement_details  as a LEFT JOIN agreement as b on a.agreement_id = b.agreement_id where (a.contribution_to=$user_id) and (b.created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_amount'];

                    }

                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                
                if ($i==6)
                {
                    $color_code = '#9c0b0e';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
            }
            if ($goal_category == 'Business Development Team')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    //$activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and property_id > 0 " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM property where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    //$activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and project_id > 0 " );
                    
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date') " );

                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and property_id > 0 " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==6)
                {
                    $color_code = '#9c0b0e';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
            }
            if ($goal_category == 'Vertical Head')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    //$activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and property_id > 0 " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM property where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    //$activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and project_id > 0 " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==6)
                {
                    $color_code = '#9c0b0e';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
            }
            if ($goal_category == 'Front Office')
            {
                if ($i==1)
                {

                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and property_id > 0 " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    /*$activitydata = $db->getAllRecords("SELECT count(*) as enquiry_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and enquiry_id > 0 " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['enquiry_count'];

                    }*/

                    $activitydata = $db->getAllRecords("SELECT count(*) as Enquiries_Generated_count FROM enquiry where (created_by = $user_id  or modified_by = $user_id or FIND_IN_SET ($user_id ,assigned)) and (created_date BETWEEN '$start_date' AND '$end_date') " );

                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['Enquiries_Generated_count'];

                    }


                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==6)
                {
                    $color_code = '#9c0b0e';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
            }
            if ($goal_category == 'HR')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    //$activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and property_id > 0 " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM property where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    //$activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and project_id > 0 " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/activity_list";
                }
                if ($i==6)
                {
                    $color_code = '#9c0b0e';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
            }
            
            
            if ($goal_category == 'Graphic Design Team')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as social_media_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Social Media') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['social_media_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_design_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Property Design') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_design_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_design_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Project Design')  " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_design_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                
                if ($i==4)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==6)
                {
                    $color_code = '#9c0b0e';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
            }

            if ($goal_category == 'Customer Support')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT sum(calling_count) as datacalling_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Data Calling') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['datacalling_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    $goals_achieved[$i] = 0;
                    //$activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and project_id > 0 " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==6)
                {
                    $color_code = '#9c0b0e';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
            }

            if ($goal_category == 'CRM Team')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    //$activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and property_id > 0 " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM property where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    //$activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and project_id > 0 " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_scout_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
                if ($i==6)
                {
                    $color_code = '#9c0b0e';
                    $goals_achieved[$i] = 0;
                    $to_url[$i] = "#/dashboardmore/:".$goal_category."/:".$goal_sub_category;
                }
            }
            $goal_percent = 0;
            $target_achieved = 0;
            if ($goals_achieved[$i]>0 && $goals_to_achieve >0)
            {
                $goal_percent = round((($goals_achieved[$i] / $goals_to_achieve)*100),2);
                $target_achieved = round((($goal_per_to_get*$goal_percent)/100),2);
                if ($target_achieved>$goal_per_to_get)
                {
                    $target_achieved = $goal_per_to_get;
                }
                $total_target_achieved = $total_target_achieved + $target_achieved;
            }
            
            $to_url = "#/dashboardmore/".$goal_id;
            $htmlstring .= '<div class="col-lg-2 col-xs-6 smallbox" >
                <div class="small-box" style="background-color:'.$color_code.';border-radius:15px;">
                <div class="inner">
                    <p class="nowrapping catname" style="color:#ffffff;">'.$goal_sub_category.'</p>
                    <p class="">'.$goals_to_achieve.'<span style="float:right;">('.$goals_achieved[$i].')</span></p><p style="text-align:center;font-size:18px;">'.$target_achieved.'%</p>
                </div>
                <!--div class="icon">
                    <i class="ion ion-bag"></i>
                </div-->
                <!--a href="'.$to_url.'" class="small-box-footer ">More info <i class="fa fa-arrow-circle-right"></i></a-->

                <a href="javascript:void(0);" class="small-box-footer " ng-click="userdashboardmore(\''.$goal_category.'\',\''.$goal_sub_category.'\','.$user_id.')">More info <i class="fa fa-arrow-circle-right"></i></a>

                </div>
            </div>';
            $i++;
    
        }
    }  
    $htmlstring .= '</div><p style="text-align:center;color:#b50909;font-size:18px;">Target Achieved:'.$total_target_achieved.'</p>';
    /*$htmlstring .= '<div class="row" style="margin-top: 5px;">';
    $to_url = "#/dashboardmore/0";
    for ($n=1; $n<=5;++$n)
    {
        $goals_to_achieve = 0;
        $goal_sub_category = "";
        $goals_achieved[$n] = 0;
        if ($n==1)
        {
            $color_code = '#2cb5ab';
            $goal_sub_category = "Expiring Lease Agreement";
        }
        if ($n==2)
        {
            $color_code = '#21c3eb';
            $goal_sub_category = "BirthDays";
        }
        if ($n==3)
        {
            $color_code = '#18a55e';
            $goal_sub_category = "Anniversaries";
        }
        if ($n==4)
        {
            $color_code = '#f05b90';
            $goal_sub_category = "My Open Activities";
        }
        if ($n==5)
        {
            $color_code = '#f99a36';
            $goal_sub_category = "Unassigned Enquiries";
        }
        
        $htmlstring .= '<div class="col-lg-2 col-xs-6 smallbox">
                    <div class="small-box" style="background-color:'.$color_code.'">
                    <div class="inner">
                        <h3>'.$goals_to_achieve.'</h3>
                        <p class="hidden-xs hidden-sm nowrapping catname">'.$goal_sub_category.'</p>
                        <p class="hidden-xs hidden-sm">('.$goals_achieved[$n].')</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="'.$to_url.'" class="small-box-footer hidden-xs hidden-sm">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>';
    }
    $htmlstring .= '</div>';
    $htmlstring .= '<div class="row" style="margin-top: 5px;">';
    $to_url = "#/dashboardmore/0";
    for ($n=1; $n<=5;++$n)
    {
        $goals_to_achieve = 0;
        $goal_sub_category = "";
        $goals_achieved[$n] = 0;
        if ($n==1)
        {
            $color_code = '#2cb5ab';
            $goal_sub_category = "Enquiries";
        }
        if ($n==2)
        {
            $color_code = '#21c3eb';
            $goal_sub_category = "Total Transactions";
        }
        if ($n==3)
        {
            $color_code = '#18a55e';
            $goal_sub_category = "Total Brokerage Expected";
        }
        if ($n==4)
        {
            $color_code = '#f05b90';
            $goal_sub_category = "Total Brokerage Invoiced";
        }
        if ($n==5)
        {
            $color_code = '#f99a36';
            $goal_sub_category = "Total Brokerage Receivable";
        }
        
        $htmlstring .= '<div class="col-lg-2 col-xs-6 smallbox">
                    <div class="small-box" style="background-color:'.$color_code.'">
                    <div class="inner">
                        <h3>'.$goals_to_achieve.'</h3>
                        <p class="hidden-xs hidden-sm nowrapping catname">'.$goal_sub_category.'</p>
                        <p class="hidden-xs hidden-sm">('.$goals_achieved[$n].')</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="'.$to_url.'" class="small-box-footer hidden-xs hidden-sm">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>';
    }
    $htmlstring .= '</div>';*/
    /*$month_dis = array();
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
    $htmlstring = '';
    $htmlstring = '<div style="width:95%;margin-top:20px;">
    <table style="border:1px solid #000000;width:100%;table-layout:fixed;text-align: center;" id = "goaldata" class="goaldata" cellspacing="3">';
    $start_date = $r->goalsdata->start_date;
    $end_date = $r->goalsdata->end_date;

    $start_month = substr($start_date,3,2);
    $end_month = substr($end_date,3,2);
    $total_months = $end_month - $start_month+1;
    if ($end_month<$start_month)
    {
        $total_months = $end_month+12 - $start_month + 1;
    }
    $htmlstring .='total months:'.$total_months;

    $user_id = $r->goalsdata->user_id;
    $no_cols = 0;
    $sql = "SELECT * from goals WHERE user_id = $user_id";
    $stmt = $db->getRows($sql);
    $htmlstring .='<tr style="background-color:#f7b3b3;"><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;" valign="top">Sales Person</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;" valign="top">Month</td>';  
    $total_per = array();  
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $goal_sub_category = $row['goal_sub_category'];
            $htmlstring .='<td colspan="3"  style="border-right:1px solid #000000;border-bottom:1px solid #000000;" valign="top">'.$goal_sub_category.'</td>';
            $total_per[$no_cols+1]=$row['goal_per'];
            $no_cols = $no_cols + 1;
        }
    }
    $htmlstring .='</tr>';
    $htmlstring .='<tr><td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td>';
    for ($n=1; $n<=$no_cols;++$n)
    {
        $htmlstring .= '<td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#f0e867;" valign="top">Goal</td><td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#4fec93;" valign="top">Achieved</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;color:red;" valign="top">Per '.$total_per[$n].'%</td>';
    }
    $htmlstring .='</tr>';
    
    $sql = "SELECT * from goals WHERE user_id = $user_id";
    $stmt = $db->getRows($sql);
    $total = array();
    $per = array();
    $i = 1;
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $goal_id = $row['goal_id'];
            $total[$i] = $total[$i] + $row['goal_jan']+$row['goal_feb']+$row['goal_mar']+$row['goal_apr']+$row['goal_may']+$row['goal_jun']+$row['goal_jul']+$row['goal_aug']+$row['goal_sep']+$row['goal_oct']+$row['goal_nov']+$row['goal_dec'];
            
            $i++;
            
        }
    }

    $month_new_enquiries = array();
    $month_new_properties = array();
    $month_new_porjects = array();
    $month_new_site_visit = array();
    $month_new_client_meeting = array();
    $month_new_deal = array();
    for ($j=1;$j<=12;++$j)
    {
        $month_new_enquiries[$j] = 0;
        $enquirydata = $db->getAllRecords("SELECT count(*) as enquiry_count FROM enquiry where (created_by = $user_id or modified_by = $user_id or $user_id in (assigned)) and month(created_date) = $j " );
        if ($enquirydata)
        {
            $month_new_enquiries[$j] = $enquirydata[0]['enquiry_count'];

        }

        $month_new_site_visit[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as site_visit_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Site Visit') " );
        if ($activitydata)
        {
            $month_new_site_visit[$j] = $activitydata[0]['site_visit_count'];

        }
        $month_new_client_meeting[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Meeting') " );
        if ($activitydata)
        {
            $month_new_client_meeting[$j] = $activitydata[0]['client_meeting_count'];

        }
        $month_new_deal[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (month(a.created_date) = $j) and (b.status = 'Deal Done') " );
        if ($activitydata)
        {
            $month_new_deal[$j] = $activitydata[0]['deal_count'];

        }
        $month_new_properties[$j] = 0;
        $propertydata = $db->getAllRecords("SELECT count(*) as property_count FROM property where (created_by = $user_id or modified_by = $user_id or $user_id in (assign_to)) and month(created_date) = $j " );
        if ($propertydata)
        {
            $month_new_properties[$j] = $propertydata[0]['property_count'];

        }
        $month_new_projects[$j] = 0;
        $projectdata = $db->getAllRecords("SELECT count(*) as project_count FROM project where (created_by = $user_id or modified_by = $user_id or $user_id in (assign_to)) and month(created_date) = $j " );
        if ($projectdata)
        {
            $month_new_projects[$j] = $projectdata[0]['project_count'];

        }
    }
    
    $htmlstring .='<tr style="background-color:#b3b2b2;"><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;" valign="top">Total</td>';

    for ($n=1;$n<=$no_cols;++$n)
    {
        $htmlstring .= '<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$total[$n].'</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td>';
    }
    
    $htmlstring .='</tr>';
    
    $month_num = array();
    
    $i = 1;
    $sql = "SELECT * from goals WHERE user_id = $user_id";
    $stmt = $db->getRows($sql);
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $month_num[$i] = $row['goal_'.$month_dis[$i]];
            $i++;
        }
    }

    

    for ($j=1;$j<=12;++$j)
    {
        $htmlstring .='<tr><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;" valign="top">'.$month_dis[$j].'</td>';
        for ($i=1;$i<=$no_cols;++$i)
        {
            $htmlstring .= '<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$month_num[$i].'</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">';
            if ($i==1)
            {
                $htmlstring .= $month_new_client_meeting[$j];
            }
            if ($i==3)
            {
                $htmlstring .= $month_new_projects[$j];
            }
            if ($i==4)
            {
                $htmlstring .= $month_new_properties[$j];
            }
            if ($i==5)
            {
                $htmlstring .= $month_new_deal[$j];
            }
            $htmlstring .= '</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td>';
        }
        $htmlstring .='</tr>';
    }
    $htmlstring .='<tr><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;color:red;" valign="top">Balance</td>';

    for ($n=1;$n<=$no_cols;++$n)
    {
        $htmlstring .= '<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td>';
    }
    
    $htmlstring .='</tr>';
    //$htmlstring .='</tr>';
    $htmlstring .='</table></div>';*/

    $htmldata['htmlstring']=$htmlstring;
    $htmldata['total_target_achieved']=$total_target_achieved;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/userdashboardmore/:start_date/:end_date/:goal_category/:goal_sub_category/:user_id', function($start_date,$end_date,$goal_category,$goal_sub_category,$user_id) use ($app) {
    $sql  = "";
    // error_log("pks1444", 3, "logfile5.log");
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
   
    $senddata =  array();
    $htmldata = array();
    $this_month = (int)substr($end_date,5,2);
    $start_date .= " 00:00:00";
    $end_date   .= " 23:59:59";
    
    $htmlstring = '';
    $htmlstring .= '<div class="box-body table-responsive">
                        <table class="table table-bordered table-striped"  >
                            <thead>';
    
    if ($goal_sub_category == "New Project Posting" || $goal_sub_category == "Project  Scouted")
    {
        
        $htmlstring .= '<tr>
            <th style="width:125px;">Project ID</th>
            <th style="width:125px;">Project Description</th>
            <th style="width:125px;">Created By</th>
            <th style="width:125px;">Assigned To</th>
            <th style="width:125px;">Team</th>
            <th style="width:125px;">Created Date</th>
            </tr>
        </thead>
        <tbody>';

        $sql = "SELECT *,e.company_name as developer,SUBSTRING(e.f_name,1,1) as f_char,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact, a.mob1,a.email, DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count,a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT CONCAT(max(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price, (SELECT CONCAT(max(CAST(carp_area as UNSIGNED)),' ',any_value(carp_area_para)) FROM property WHERE project_id= a.project_id and CAST(carp_area as UNSIGNED) > 0 ) as max_carp,(SELECT CONCAT(min(CAST(carp_area as UNSIGNED)),' ',any_value(carp_area_para)) FROM property WHERE project_id= a.project_id and CAST(carp_area as UNSIGNED) > 0 ) as min_carp ,  (SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames,(SELECT CONCAT(t.salu,' ',t.fname,' ',t.lname) FROM users as s LEFT JOIN employee as t on t.emp_id = s.emp_id WHERE s.user_id = a.created_by) as created_by from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  WHERE (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to) ) and (a.created_date BETWEEN '$start_date' AND '$end_date')  GROUP BY a.project_id ORDER BY a.modified_date DESC ";
        $stmt = $db->getRows($sql);
        if($stmt->num_rows > 0)
        {
            $i = 1;
            while($row = $stmt->fetch_assoc())
            {
                $htmlstring .= '<tr>
                                    <td><a href="#/project_edit/'.$row['project_id'].'" target="_blank">PROJECTID_'.$row['project_id'].'</a></td>';
                $description = "";
                if ($row['project_name'])
                {
                    $description .= $row['project_name'].' ';
                }
                $htmlstring .= '    <td>'.$description.'</td>
                                    <td>'.$row['created_by'].'</td>
                                    <td>'.$row['assign_to'].'</td>
                                    <td>'.$row['team'].'</td>
                                    <td>'.$row['created_date'].'</td>
                                </tr>';
            }
        }

    }
    else if ($goal_sub_category == "New Property Posting" || $goal_sub_category == "Property Scouted" || $goal_sub_category == "New Property Generated")
    {
        $htmlstring .= '<tr>
            <th style="width:125px;">Property ID</th>
            <th style="width:125px;">Type</th>
            <th style="width:125px;">Property Description</th>
            <th style="width:125px;">Created By</th>
            <th style="width:125px;">Assigned To</th>
            <th style="width:125px;">Team</th>
            <th style="width:125px;">Created Date</th>
            </tr>
        </thead>
        <tbody>';

        $sql = "SELECT a.property_id,a.proptype,a.project_name, a.building_name, a.bedrooms ,a.propsubtype,a.property_for, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date FROM property as a  where (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) and a.project_id = 0  and (a.created_date BETWEEN '$start_date' AND '$end_date') ORDER BY a.created_date DESC";
        $stmt = $db->getRows($sql);
        if($stmt->num_rows > 0)
        {
            $i = 1;
            while($row = $stmt->fetch_assoc())
            {
                $htmlstring .= '<tr>
                                    <td><a href="#/properties_edit/'.$row['property_id'].'" target="_blank">PROPERTYID_'.$row['property_id'].'</a></td>
                                    <td>'.$row['proptype'].'</td>';
                $description = "";
                if ($row['project_name'])
                {
                    $description .= $row['project_name'].' ';
                }
                else
                {
                    $description .= $row['building_name'].' ';
                }
                if ($row['bedrooms'])
                {
                    $description .= $row['bedrooms'].' BHK ';
                }
                $description .= $row['propsubtype'].' : '.$row['property_for'];
                $htmlstring .= '    <td>'.$description.'</td>
                                    <td>'.$row['created_by'].'</td>
                                    <td>'.$row['assign_to'].'</td>
                                    <td>'.$row['teams'].'</td>
                                    <td>'.$row['created_date'].'</td>
                                </tr>';
            }
        }
    }
    else if ($goal_sub_category == "New Enquiries Generated")
    {
        $htmlstring .= '<tr>
                            <th style="width:125px;">Enquiry ID</th>
                            <th style="width:125px;">Type</th>
                            <th style="width:125px;">Description</th>
                            <th style="width:125px;">Created By</th>
                            <th style="width:125px;">Assigned To</th>
                            <th style="width:125px;">Team</th>
                            <th style="width:125px;">Created Date</th>
                        </tr>
                    </thead>
                    <tbody>';

        $sql="SELECT a.enquiry_id,a.enquiry_for,a.enquiry_type,a.enquiry_off, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by FROM enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker'  WHERE  (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET ($user_id ,a.assigned)) and (a.created_date BETWEEN '$start_date' AND '$end_date') ORDER BY a.created_date DESC";

        $stmt = $db->getRows($sql);
        if($stmt->num_rows > 0)
        {
            $i = 1;
            while($row = $stmt->fetch_assoc())
            {
                $htmlstring .= '<tr>
                                    <td><a href="#/enquiries_edit/'.$row['enquiry_id'].'" target="_blank">ENQUIRYID_'.$row['enquiry_id'].'</a></td>
                                    <td>'.$row['enquiry_off'].'</td>';


                $description = "";
                $description .= 'Looking to '.$row['enquiry_for'].'  : '.$row['enquiry_type'];
                $description .= '<p>'.$row['client_name'].'</p>';
                $htmlstring .= '    <td>'.$description.'</td>
                                    <td>'.$row['created_by'].'</td>
                                    <td>'.$row['assigned'].'</td>
                                    <td>'.$row['teams'].'</td>
                                    <td>'.$row['created_date'].'</td>
                                </tr>';
            }
        }
    }
    else if (stripos($goal_sub_category,"Behaviour"))
    {
        $htmlstring .= '<tr>
                            <th style="width:125px;text-align:center;">Monthly Attendance</th>
                            <th style="width:125px;text-align:center;">New Idea</th>
                            <th style="width:125px;text-align:center;">Activity Participation</th>
                            <th style="width:125px;text-align:center;">Daily Activity</th>
                            <th style="width:125px;text-align:center;">Response Assigned</th>
                            <th style="width:125px;text-align:center;">Transaction Process</th>
                            <th style="width:125px;text-align:center;">Total</th>
                        </tr>
                    </thead>
                    <tbody>';

        $sql = "SELECT * FROM behaviour WHERE user_id = $user_id and month(data_date) = $this_month LIMIT 1";
        $crmdata = $db->getAllRecords($sql);
        $total_crm = 0;
        if ($crmdata)
        {
            $htmlstring .= '<tr>';
            if ($crmdata[0]['monthly_attendance']=="true")
            {
                $total_crm = $total_crm + 2;
                $htmlstring .= '<td style="text-align:center;">2</td>';
            }
            else{
                $htmlstring .= '<td style="text-align:center;">0</td>';
            }
            if ($crmdata[0]['new_idea'])
            {
                $total_crm = $total_crm + 2;
                $htmlstring .= '<td style="text-align:center;">2</td>';
            }
            else{
                $htmlstring .= '<td style="text-align:center;">0</td>';
            }
            if ($crmdata[0]['activity_participation']=="true")
            {
                $total_crm = $total_crm + 1;
                $htmlstring .= '<td style="text-align:center;">1</td>';
            }
            else{
                $htmlstring .= '<td style="text-align:center;">0</td>';
            }
            if ($crmdata[0]['daily_activity']=="true")
            {
                $total_crm = $total_crm + 1;
                $htmlstring .= '<td style="text-align:center;">1</td>';
            }
            else{
                $htmlstring .= '<td style="text-align:center;">0</td>';
            }
            if ($crmdata[0]['response_assigned']=="true")
            {
                $total_crm = $total_crm + 2;
                $htmlstring .= '<td style="text-align:center;">2</td>';
            }
            else{
                $htmlstring .= '<td style="text-align:center;">0</td>';
            }
            if ($crmdata[0]['transaction_process']=="true")
            {
                $total_crm = $total_crm + 2;
                $htmlstring .= '<td style="text-align:center;">2</td>';
            }
            else{
                $htmlstring .= '<td style="text-align:center;">0</td>';
            }
            $htmlstring .= '<td style="text-align:center;">'.$total_crm.'</td></tr>';
        }
    }
    else if ($goal_sub_category == "Transaction")
    {
        $htmlstring .= '<tr>
                            <th style="width:125px;">Agreement ID</th>
                            <th style="width:125px;">Buyer</th>
                            <th style="width:125px;">Seller</th>
                            <th style="width:125px;">Our Brokerage</th>
                            <th style="width:125px;">Created By</th>
                            <th style="width:125px;">Assigned To</th>
                            <th style="width:125px;">Team</th>
                            <th style="width:125px;">Created Date</th>
                        </tr>
                    </thead>
                    <tbody>';


        $sql="SELECT a.agreement_id,CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as buyer,CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as seller, a.our_brokerage, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as teams, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assigned,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by FROM agreement as a LEFT JOIN contact as b on a.contact_id = b.contact_id LEFT JOIN contact as c on a.buyer_id = c.contact_id  WHERE (a.created_by = $user_id or a.modified_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) and (a.created_date BETWEEN '$start_date' AND '$end_date') ORDER BY a.created_date DESC";

        $stmt = $db->getRows($sql);
        if($stmt->num_rows > 0)
        {
            $i = 1;
            while($row = $stmt->fetch_assoc())
            {
                $htmlstring .= '<tr>
                                    <td><a href="#/agreement_edit/'.$row['agreement_id'].'" target="_blank">AGREEMENTID_'.$row['agreement_id'].'</a></td>
                                    <td>'.$row['buyer'].'</td>';
                $htmlstring .= '    <td>'.$row['seller'].'</td>
                                    <td>'.$row['our_brokerage'].'</td>
                                    <td>'.$row['created_by'].'</td>
                                    <td>'.$row['assigned'].'</td>
                                    <td>'.$row['teams'].'</td>
                                    <td>'.$row['created_date'].'</td>
                                </tr>';
            }
        }
    }
    else
    {
        $htmlstring .= '<tr>
            <th style="width:125px;">Activity ID</th>
            <th>Activity Type</th>
            <th>Activity Sub Type</th>
            <th style="width:125px;">ID</th>
            <th>Activity Date</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th style="width:325px;">Description</th>
        </tr>
        </thead>
        <tbody>';
        if(($goal_sub_category == "Client Inspection") || ($goal_sub_category == "Broker Inspection")){
            $sql = "SELECT *, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(activity_start,'%d-%m-%Y %H:%i') AS activity_start, DATE_FORMAT(activity_end,'%d-%m-%Y %H:%i') AS activity_end FROM activity WHERE (created_by = $user_id or modified_by = $user_id or FIND_IN_SET($user_id , assign_to)) and (activity_type='Site Visit') and (created_date BETWEEN '$start_date' AND '$end_date')";
        }elseif($goal_sub_category == "Lead generation"){
            $sql = "SELECT *, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(activity_start,'%d-%m-%Y %H:%i') AS activity_start, DATE_FORMAT(activity_end,'%d-%m-%Y %H:%i') AS activity_end FROM activity WHERE (created_by = $user_id or modified_by = $user_id or FIND_IN_SET($user_id , assign_to))  and (activity_type='Task') and (activity_sub_type = '".$goal_sub_category."') and (created_date BETWEEN '$start_date' AND '$end_date')";
        }elseif ($goal_sub_category == "Sharing Properties" || $goal_sub_category == "Commercial  Sharing" || $goal_sub_category == "Residential Sharing"){
            $sql = "SELECT *, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(activity_start,'%d-%m-%Y %H:%i') AS activity_start, DATE_FORMAT(activity_end,'%d-%m-%Y %H:%i') AS activity_end FROM activity WHERE (created_by = $user_id or modified_by = $user_id or FIND_IN_SET($user_id , assign_to))  and (activity_type='Task') and (activity_sub_type='Sharing Properties' OR activity_sub_type='Commercial  Sharing' OR activity_sub_type='Residential Sharing') and (created_date BETWEEN '$start_date' AND '$end_date')";
        }elseif ($goal_sub_category == "New Clients Meeting" || $goal_sub_category == "New Broker Meeting" || $goal_sub_category == "Retail Meeting" || $goal_sub_category == "New Developer Meeting"){
            $sql = "SELECT *, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(activity_start,'%d-%m-%Y %H:%i') AS activity_start, DATE_FORMAT(activity_end,'%d-%m-%Y %H:%i') AS activity_end FROM activity WHERE (created_by = $user_id or modified_by = $user_id or FIND_IN_SET($user_id , assign_to))  and (activity_type='Meeting') and (created_date BETWEEN '$start_date' AND '$end_date')";
        }else{
            $sql = "SELECT *, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(activity_start,'%d-%m-%Y %H:%i') AS activity_start, DATE_FORMAT(activity_end,'%d-%m-%Y %H:%i') AS activity_end FROM activity WHERE (created_by = $user_id or modified_by = $user_id or FIND_IN_SET($user_id , assign_to))  and (activity_sub_type = '".$goal_sub_category."') and (created_date BETWEEN '$start_date' AND '$end_date')";
            // error_log($sql, 3, "logfile5.log");
        }
        // $sql = "SELECT *, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(activity_start,'%d-%m-%Y %H:%i') AS activity_start, DATE_FORMAT(activity_end,'%d-%m-%Y %H:%i') AS activity_end FROM activity WHERE (created_by = $user_id)  and (activity_sub_type = '".$goal_sub_category."') and (created_date BETWEEN '$start_date' AND '$end_date')";
        
        // $condition = getgoalcondition($goal_sub_category);

        // if ($condition!="")
        // {
        //     $sql .= " AND " .$condition;    
        // }
        $sql .= " ORDER by created_date DESC";
        $stmt = $db->getRows($sql);
        if($stmt->num_rows > 0)
        {
            $i = 1;
            while($row = $stmt->fetch_assoc())
            {
                
                $htmlstring .= '<tr>
                            <td><a href="#/activity_edit/'.$row['activity_id'].'" target="_blank">Activity ID:'.$row['activity_id'].'</a></td>
                            <td>'.$row['activity_type'].'</td>
                            <td>'.$row['activity_sub_type'].'</td>';
                            
                $htmlstring .= '<td>';
                if ($row['property_id'] >0)
                {
                            
                    $htmlstring .= '<p><a href="#/properties_edit/'.$row['property_id'].'" target="_blank">Property ID:'.$row['property_id'].'</a></p>';
                }
                if ($row['project_id'] >0)
                {
                    $htmlstring .= '<p><a href="#/project_edit/'.$row['project_id'].'" target="_blank">Project ID:'.$row['project_id'].'</a></p>';
                }
                if ($row['enquiry_id'] >0)
                {
                    $htmlstring .= '<p><a href="#/enquiry_edit/'.$row['enquiry_id'].'" target="_blank">Enquiry ID:'.$row['enquiry_id'].'</a></p>';
                }
                $htmlstring .= '</td>';
                $htmlstring .= '<td>'.$row['created_date'].'</td>
                                <td>'.$row['activity_start'].'</td>
                                <td>'.$row['activity_end'].'</td>
                                <td>'.$row['status'].'</td>
                                <td>'.$row['description'].'</td>
                        </tr>';
            }
        }
    } 
    $htmlstring .= '</tbody></table> 
                </div> 
            </div>';
    
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


function getgoalcondition($goal_sub_category)
{
    $condition="";
    if ($goal_sub_category=='Property Scouted')
    {
        $condition = " (activity_type='Property Visit' and property_id > 0) ";
    }
    if ($goal_sub_category == 'Project Scouted')
    {
        $condition = "  (activity_type='Property Visit' and project_id > 0) ";
    }   
    if ($goal_sub_category == 'Client Inspection' || $goal_sub_category == 'Broker Inspection' )
    {  
        $condition = " (activity_type='Site Visit') ";
    }

    

    if ($goal_sub_category == 'Transaction')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='Deal Done') ";
    }

    if ($goal_sub_category == 'New Clients Meeting' || $goal_sub_category == 'New Broker Meeting' || $goal_sub_category == 'Retail Meeting' || $goal_sub_category == "New Developer Meeting")
    {  
        $condition = "  (activity_type='Meeting') ";
    }
    if ($goal_sub_category == 'New Enquiries Generated')
    {  
        $condition = "  (activity_type='Task') and (enquiry_id > 0) ";
    }
    if ($goal_sub_category == 'Data Gathering')
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'Data Gathering' ) ";
    }
    if ($goal_sub_category == 'Certification + Behaviour+ CRM')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='Certification + Behaviour+ CRM')";
    }
    if ($goal_sub_category == 'CRM Error_Resolved')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='CRM Error_Resolved')";
    }
    if ($goal_sub_category == 'CRM Updation')
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'CRM Updation') ";
    }
    if ($goal_sub_category == 'Training')
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'CRM Training') ";
    }
    if ($goal_sub_category == 'Data Calling')
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'Data Calling') ";
    }
    if ($goal_sub_category == 'System  Updation')
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'System  Updation') ";
    }
    if ($goal_sub_category == 'New Property Posting')
    {  
        $condition = "  (activity_type='Task' AND property_id > 0)";
    }
    if ($goal_sub_category == 'Social Media')
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'Social Media') ";
    }
    if ($goal_sub_category == 'Property Design')
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'Property Design') ";
    }
    if ($goal_sub_category == 'Project Design')
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'Project Design') ";
    }
    if ($goal_sub_category == 'No. Of Recruitment')
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'Recruitment') ";
    }
    if ($goal_sub_category == 'Engagement Activity')
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'Engagement_Activity') ";
    }

    if ($goal_sub_category == 'Project Sharing' || $goal_sub_category == 'Sharing Projects' )
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'Project Sharing' or activity_sub_type = 'Sharing Projects') ";
    }

    if ($goal_sub_category == 'Residential Sharing' || $goal_sub_category == 'Commercial Sharing')
    {  
        $condition = "  (activity_type='Task') and (activity_sub_type = 'Residential Sharing' or activity_sub_type = 'Commercial Sharing') ";
    }


    
    if ($goal_sub_category == 'Project Report')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='Project Report') ";
    }

    if ($goal_sub_category == 'New Market Research')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='New Market Research') ";
    }

    if ($goal_sub_category == 'Newsletter')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='Newsletter') ";
    }
    if ($goal_sub_category == 'New Enquiries Generated ( Other vertical)')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='New Enquiries Generated ( Other vertical)') ";
    }

    if ($goal_sub_category == 'Lead generation')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='Lead generation') ";
    }
    if ($goal_sub_category == 'Creative Activities')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='Creative Activities') ";
    }

    if ($goal_sub_category == 'Residential Project')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='Residential Project') ";
    }
    if ($goal_sub_category == 'Commercial Project')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='Commercial Project') ";
    }

    if ($goal_sub_category == 'Account Report & Departmental Monthly Reports')
    {  
        $condition = "  (activity_type='Task' AND activity_sub_type='Account Report & Departmental Monthly Reports') ";
    }


    	

    return $condition;
}

$app->get('/admindashboardmore/:user_id/:start_date/:end_date/:goal_category/:goal_sub_category', function($user_id, $start_date,$end_date,$goal_category,$goal_sub_category) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    //$user_id = $session['user_id'];
    
    $senddata =  array();
    $htmldata = array();
    
    $htmlstring = '';
    $htmlstring .= '<div class="box-body table-responsive">
                        
                        <table class="table table-bordered table-striped"  >
                            <thead>
                                <tr>
                                    <th style="width:125px;">Activity ID</th>
                                    <th>Activity Type</th>
                                    <th>Activity Sub Type</th>
                                    <th style="width:125px;">ID</th>
                                    <th>Activity Date</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th style="width:325px;">Description</th>
                                </tr>
                                </thead>
                                <tbody>';

        $sql = "SELECT *, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(activity_start,'%d-%m-%Y %H:%i') AS activity_start, DATE_FORMAT(activity_end,'%d-%m-%Y %H:%i') AS activity_end FROM activity WHERE (created_by = $user_id)   and (activity_sub_type = '".$goal_sub_category."') and (created_date BETWEEN '$start_date' AND '$end_date')";
        
        $condition = getgoalcondition($goal_sub_category);

        if ($condition!="")
        {
            $sql .= " AND " .$condition;    
        }
        $sql .= " ORDER by created_date DESC";  
        
        $stmt = $db->getRows($sql);
        if($stmt->num_rows > 0)
        {
            $i = 1;
            while($row = $stmt->fetch_assoc())
            {
                
                $htmlstring .= '<tr>
                            <td><a href="#/activity_edit/'.$row['activity_id'].'"  target="_blank">Activity ID:'.$row['activity_id'].'</a></td>
                            <td>'.$row['activity_type'].'</td>
                            <td>'.$row['activity_sub_type'].'</td>';
                            
                $htmlstring .= '<td>';
                if ($row['property_id'] >0)
                {
                            
                    $htmlstring .= '<p><a href="#/properties_edit/'.$row['property_id'].'"  target="_blank">Property ID:'.$row['property_id'].'</a></p>';
                }
                if ($row['project_id'] >0)
                {
                    $htmlstring .= '<p><a href="#/project_edit/'.$row['project_id'].'" target="_blank">Project ID:'.$row['project_id'].'</a></p>';
                }
                if ($row['enquiry_id'] >0)
                {
                    $htmlstring .= '<p><a href="#/enquiry_edit/'.$row['enquiry_id'].'"  target="_blank">Enquiry ID:'.$row['enquiry_id'].'</a></p>';
                }
                $htmlstring .= '</td>';
                $htmlstring .= '<td>'.$row['created_date'].'</td>
                                <td>'.$row['activity_start'].'</td>
                                <td>'.$row['activity_end'].'</td>
                                <td>'.$row['status'].'</td>
                                <td>'.$row['description'].'</td>
                        </tr>';
                }
            }
            $htmlstring .= '</tbody></table>   
        </div> 
    </div>';
    
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/admindashboard/:start_date/:end_date/:user_id', function($start_date,$end_date,$user_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    //$user_id = $session['user_id'];
    

    
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
    
    $goals_achieved = array();
    $goals_to_achieve = array();
    $this_month = (int)substr($end_date,5,2); 

    $senddata =  array();
    $htmldata = array();

    $htmlstring = '';
    $htmlstring .= '<div class="row" >';
    if ($user_id==0)
    {
        $sql = "SELECT * from goals ORDER BY CAST(sequence_number as UNSIGNED) ";
        return;
    }
    else
    {
        $sql = "SELECT * from goals as a LEFT JOIN dropdowns as b ON a.goal_sub_category = b.display_value WHERE a.user_id = $user_id group by a.goal_sub_category ORDER BY CAST(b.sequence_number as UNSIGNED) ";
    }
    $stmt = $db->getRows($sql);
    if($stmt->num_rows > 0)
    {
        $i = 1;
        while($row = $stmt->fetch_assoc())
        {
            $goal_id = $row['goal_id'];
            $goal_category = $row['goal_category'];
            $goal_sub_category = $row['goal_sub_category'];
            $goal_per = $row['goal_per'];
            $goals_to_achieve = $row['goal_'.$month_dis[$this_month]];
            if ($goal_category == 'Property Team')
            {
                if ($i==1)
                {

                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    // CHANGES ON 08.04.2021
                    //$activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Property Visit') and property_id > 0 " );
                    
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM property where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') " );

                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }
                    

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_scout_count'];

                    }
                    

                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Site Visit') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }

                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                }
            }
            if ($goal_category == 'Transaction Team')
            {
                if ($i==1)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as Enquiries_Generated_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and enquiry_id > 0 " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['Enquiries_Generated_count'];

                    }

                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Site Visit') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }

                    
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                }
            }
            if ($goal_category == 'Business Development Team')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    
                    //$propertydata = $db->getAllRecords("SELECT count(*) as property_count FROM property where (created_by = $user_id or modified_by = $user_id or $user_id in (assign_to)) and created_date BETWEEN '$start_date' AND '$end_date' " );
                    
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM property where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') " );

                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    //$projectdata = $db->getAllRecords("SELECT count(*) as project_count FROM project where (created_by = $user_id or modified_by = $user_id or $user_id in (assign_to)) and created_date BETWEEN '$start_date' AND '$end_date' " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_scout_count'];

                    }
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                }
            }
            if ($goal_category == 'Vertical Head')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    //$propertydata = $db->getAllRecords("SELECT count(*) as property_count FROM property where (created_by = $user_id or modified_by = $user_id or $user_id in (assign_to)) and created_date BETWEEN '$start_date' AND '$end_date' " );
                    
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM property where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    //$projectdata = $db->getAllRecords("SELECT count(*) as project_count FROM project where (created_by = $user_id or modified_by = $user_id or $user_id in (assign_to)) and created_date BETWEEN '$start_date' AND '$end_date' " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_scout_count'];

                    }
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                }
            }
            if ($goal_category == 'Front Office')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    //$activitydata = $db->getAllRecords("SELECT count(*) as property_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and property_id > 0 " );

                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM property where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }
                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as enquiry_count FROM activity where (created_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and enquiry_id > 0 " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['enquiry_count'];

                    }
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                }
            }
            if ($goal_category == 'HR')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    //$propertydata = $db->getAllRecords("SELECT count(*) as property_count FROM property where (created_by = $user_id or modified_by = $user_id or $user_id in (assign_to)) and created_date BETWEEN '$start_date' AND '$end_date' " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_scout_count FROM property where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to)) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_scout_count'];

                    }

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    //$projectdata = $db->getAllRecords("SELECT count(*) as project_count FROM project where (created_by = $user_id or modified_by = $user_id or $user_id in (assign_to)) and created_date BETWEEN '$start_date' AND '$end_date' " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_count'];

                    }
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                }
            }

            if ($goal_category == 'Graphic Design Team')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as social_media_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Social Media') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['social_media_count'];

                    }

                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as property_design_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Property Design') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['property_design_count'];

                    }
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_design_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Project Design') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_design_count'];

                    }
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                }
            }
            if ($goal_category == 'Customer Support')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT sum(calling_count) as datacalling_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='Data Calling') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['datacalling_count'];

                    }
                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    //$projectdata = $db->getAllRecords("SELECT count(*) as project_count FROM project where (created_by = $user_id or modified_by = $user_id or $user_id in (assign_to)) and created_date BETWEEN '$start_date' AND '$end_date' " );
                    $activitydata = $db->getAllRecords("SELECT count(*) as project_scout_count FROM project where (created_by = $user_id or FIND_IN_SET ($user_id ,assign_to) and (created_date BETWEEN '$start_date' AND '$end_date') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['project_scout_count'];

                    }
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Meeting') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['client_meeting_count'];

                    }
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                }
                
            }
            if ($goal_category == 'CRM Team')
            {
                if ($i==1)
                {
                    $color_code = '#2cb5ab';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as crmerror_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='CRM Error/ Resolved') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['crmerror_count'];

                    }
                    
                }
                if ($i==2)
                {
                    $color_code = '#21c3eb';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as crmtraining_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='CRM Training') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['crmtraining_count'];

                    }
                }
                if ($i==3)
                {
                    $color_code = '#18a55e';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as crmupdation_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (activity_type='Task') and (activity_sub_type='CRM Updation') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['crmupdation_count'];

                    }
                }
                if ($i==4)
                {
                    $color_code = '#f05b90';
                    $goals_achieved[$i] = 0;
                    $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (created_date BETWEEN '$start_date' AND '$end_date') and (b.status = 'Deal Done') " );
                    if ($activitydata)
                    {
                        $goals_achieved[$i] = $activitydata[0]['deal_count'];

                    }
                }
                if ($i==5)
                {
                    $color_code = '#f99a36';
                    $goals_achieved[$i] = 0;
                }
                
            }
            
            $to_url = "#/admindashboardmore/".$user_id.'/'.$start_date.'/'.$end_date.'/'.$goal_category.'/'.$goal_sub_category;

            $htmlstring .= '<div class="col-lg-2 col-xs-6 smallbox">
                <div class="small-box" style="background-color:'.$color_code.'">
                <div class="inner">
                    <h3>'.$goals_to_achieve.'</h3>
                    <p class="hidden-xs hidden-sm nowrapping">'.$goal_sub_category.'</p>
                    <p class="hidden-xs hidden-sm">('.$goals_achieved[$i].')</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <!--a href="'.$to_url.'" class="small-box-footer hidden-xs hidden-sm">More info <i class="fa fa-arrow-circle-right"></i></a-->

                <a href="javascript:void(0);" class="small-box-footer hidden-xs hidden-sm" ng-click="admindashboardmore(\''.$goal_category.'\',\''.$goal_sub_category.'\')">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>';
            $i++;
    
        }
    }   
    $htmlstring .= '</div>';
    /*$htmlstring .= '<div class="row" style="margin-top: 5px;">';
    $to_url = "#/dashboardmore/0";
    for ($n=1; $n<=5;++$n)
    {
        $goals_to_achieve = 0;
        $goal_sub_category = "";
        $goals_achieved[$n] = 0;
        if ($n==1)
        {
            $color_code = '#2cb5ab';
            $goal_sub_category = "Expiring Lease Agreement";
        }
        if ($n==2)
        {
            $color_code = '#21c3eb';
            $goal_sub_category = "BirthDays";
        }
        if ($n==3)
        {
            $color_code = '#18a55e';
            $goal_sub_category = "Anniversaries";
        }
        if ($n==4)
        {
            $color_code = '#f05b90';
            $goal_sub_category = "My Open Activities";
        }
        if ($n==5)
        {
            $color_code = '#f99a36';
            $goal_sub_category = "Unassigned Enquiries";
        }
        
        $htmlstring .= '<div class="col-lg-2 col-xs-6 smallbox">
                    <div class="small-box" style="background-color:'.$color_code.'">
                    <div class="inner">
                        <h3>'.$goals_to_achieve.'</h3>
                        <p class="hidden-xs hidden-sm nowrapping catname">'.$goal_sub_category.'</p>
                        <p class="hidden-xs hidden-sm">('.$goals_achieved[$n].')</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="'.$to_url.'" class="small-box-footer hidden-xs hidden-sm">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>';
    }
    $htmlstring .= '</div>';
    $htmlstring .= '<div class="row" style="margin-top: 5px;">';
    $to_url = "#/dashboardmore/0";
    for ($n=1; $n<=5;++$n)
    {
        $goals_to_achieve = 0;
        $goal_sub_category = "";
        $goals_achieved[$n] = 0;
        if ($n==1)
        {
            $color_code = '#2cb5ab';
            $goal_sub_category = "Enquiries";
        }
        if ($n==2)
        {
            $color_code = '#21c3eb';
            $goal_sub_category = "Total Transactions";
        }
        if ($n==3)
        {
            $color_code = '#18a55e';
            $goal_sub_category = "Total Brokerage Expected";
        }
        if ($n==4)
        {
            $color_code = '#f05b90';
            $goal_sub_category = "Total Brokerage Invoiced";
        }
        if ($n==5)
        {
            $color_code = '#f99a36';
            $goal_sub_category = "Total Brokerage Receivable";
        }
        
        $htmlstring .= '<div class="col-lg-2 col-xs-6 smallbox">
                    <div class="small-box" style="background-color:'.$color_code.'">
                    <div class="inner">
                        <h3>'.$goals_to_achieve.'</h3>
                        <p class="hidden-xs hidden-sm nowrapping catname">'.$goal_sub_category.'</p>
                        <p class="hidden-xs hidden-sm">('.$goals_achieved[$n].')</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="'.$to_url.'" class="small-box-footer hidden-xs hidden-sm">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>';
    }
    $htmlstring .= '</div>';*/
    
    /*$month_dis = array();
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
    $htmlstring = '';
    $htmlstring = '<div style="width:95%;margin-top:20px;">
    <table style="border:1px solid #000000;width:100%;table-layout:fixed;text-align: center;" id = "goaldata" class="goaldata" cellspacing="3">';
    $start_date = $r->goalsdata->start_date;
    $end_date = $r->goalsdata->end_date;

    $start_month = substr($start_date,3,2);
    $end_month = substr($end_date,3,2);
    $total_months = $end_month - $start_month+1;
    if ($end_month<$start_month)
    {
        $total_months = $end_month+12 - $start_month + 1;
    }
    $htmlstring .='total months:'.$total_months;

    $user_id = $r->goalsdata->user_id;
    $no_cols = 0;
    $sql = "SELECT * from goals WHERE user_id = $user_id";
    $stmt = $db->getRows($sql);
    $htmlstring .='<tr style="background-color:#f7b3b3;"><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;" valign="top">Sales Person</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;" valign="top">Month</td>';  
    $total_per = array();  
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $goal_sub_category = $row['goal_sub_category'];
            $htmlstring .='<td colspan="3"  style="border-right:1px solid #000000;border-bottom:1px solid #000000;" valign="top">'.$goal_sub_category.'</td>';
            $total_per[$no_cols+1]=$row['goal_per'];
            $no_cols = $no_cols + 1;
        }
    }
    $htmlstring .='</tr>';
    $htmlstring .='<tr><td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td>';
    for ($n=1; $n<=$no_cols;++$n)
    {
        $htmlstring .= '<td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#f0e867;" valign="top">Goal</td><td  style="border-right:1px solid #000000;border-bottom:1px solid #000000;background-color:#4fec93;" valign="top">Achieved</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;color:red;" valign="top">Per '.$total_per[$n].'%</td>';
    }
    $htmlstring .='</tr>';
    
    $sql = "SELECT * from goals WHERE user_id = $user_id";
    $stmt = $db->getRows($sql);
    $total = array();
    $per = array();
    $i = 1;
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $goal_id = $row['goal_id'];
            $total[$i] = $total[$i] + $row['goal_jan']+$row['goal_feb']+$row['goal_mar']+$row['goal_apr']+$row['goal_may']+$row['goal_jun']+$row['goal_jul']+$row['goal_aug']+$row['goal_sep']+$row['goal_oct']+$row['goal_nov']+$row['goal_dec'];
            
            $i++;
            
        }
    }

    $month_new_enquiries = array();
    $month_new_properties = array();
    $month_new_porjects = array();
    $month_new_site_visit = array();
    $month_new_client_meeting = array();
    $month_new_deal = array();
    for ($j=1;$j<=12;++$j)
    {
        $month_new_enquiries[$j] = 0;
        $enquirydata = $db->getAllRecords("SELECT count(*) as enquiry_count FROM enquiry where (created_by = $user_id or modified_by = $user_id or $user_id in (assigned)) and month(created_date) = $j " );
        if ($enquirydata)
        {
            $month_new_enquiries[$j] = $enquirydata[0]['enquiry_count'];

        }

        $month_new_site_visit[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as site_visit_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Site Visit') " );
        if ($activitydata)
        {
            $month_new_site_visit[$j] = $activitydata[0]['site_visit_count'];

        }
        $month_new_client_meeting[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as client_meeting_count FROM activity where (created_by = $user_id or modified_by = $user_id) and (month(created_date) = $j) and (activity_type='Meeting') " );
        if ($activitydata)
        {
            $month_new_client_meeting[$j] = $activitydata[0]['client_meeting_count'];

        }
        $month_new_deal[$j] = 0;
        $activitydata = $db->getAllRecords("SELECT count(*) as deal_count FROM activity as a LEFT JOIN activity_details as b on a.activity_id = b.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (month(a.created_date) = $j) and (b.status = 'Deal Done') " );
        if ($activitydata)
        {
            $month_new_deal[$j] = $activitydata[0]['deal_count'];

        }
        $month_new_properties[$j] = 0;
        $propertydata = $db->getAllRecords("SELECT count(*) as property_count FROM property where (created_by = $user_id or modified_by = $user_id or $user_id in (assign_to)) and month(created_date) = $j " );
        if ($propertydata)
        {
            $month_new_properties[$j] = $propertydata[0]['property_count'];

        }
        $month_new_projects[$j] = 0;
        $projectdata = $db->getAllRecords("SELECT count(*) as project_count FROM project where (created_by = $user_id or modified_by = $user_id or $user_id in (assign_to)) and month(created_date) = $j " );
        if ($projectdata)
        {
            $month_new_projects[$j] = $projectdata[0]['project_count'];

        }
    }
    
    $htmlstring .='<tr style="background-color:#b3b2b2;"><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;" valign="top">Total</td>';

    for ($n=1;$n<=$no_cols;++$n)
    {
        $htmlstring .= '<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$total[$n].'</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td>';
    }
    
    $htmlstring .='</tr>';
    
    $month_num = array();
    
    $i = 1;
    $sql = "SELECT * from goals WHERE user_id = $user_id";
    $stmt = $db->getRows($sql);
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $month_num[$i] = $row['goal_'.$month_dis[$i]];
            $i++;
        }
    }

    

    for ($j=1;$j<=12;++$j)
    {
        $htmlstring .='<tr><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;" valign="top">'.$month_dis[$j].'</td>';
        for ($i=1;$i<=$no_cols;++$i)
        {
            $htmlstring .= '<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">'.$month_num[$i].'</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">';
            if ($i==1)
            {
                $htmlstring .= $month_new_client_meeting[$j];
            }
            if ($i==3)
            {
                $htmlstring .= $month_new_projects[$j];
            }
            if ($i==4)
            {
                $htmlstring .= $month_new_properties[$j];
            }
            if ($i==5)
            {
                $htmlstring .= $month_new_deal[$j];
            }
            $htmlstring .= '</td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td>';
        }
        $htmlstring .='</tr>';
    }
    $htmlstring .='<tr><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;color:red;" valign="top">Balance</td>';

    for ($n=1;$n<=$no_cols;++$n)
    {
        $htmlstring .= '<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td><td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td>';
    }
    
    $htmlstring .='</tr>';
    //$htmlstring .='</tr>';
    $htmlstring .='</table></div>';*/

    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/goals_report_ctrl/:category', function($category) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $user_id = $session['user_id'];
    $this_month = date('m');

    $enquiry_goal = 0;
    $goaldata = $db->getAllRecords("SELECT goal_achieve FROM goals where user_id = $user_id and category='Enquiries' " );
    if ($goaldata)
    {
         $enquiry_goal = $goaldata[0]['goal_achieve'];

    }
    if ($category=='enquiry')
    {
        $sql="SELECT *,  SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob,e.locality as preferred_locality, f.area_name as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team  from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and contact_off = 'client' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id WHERE  a.created_by = $user_id and month(a.created_date)=$this_month ORDER BY CAST(a.enquiry_id as UNSIGNED) DESC";

        $selectenquiries = $db->getAllRecords($sql);
        echo json_encode($selectenquiries);
    }

    if ($category=='site_visit')
    {
        $sql = "SELECT *, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date, DATE_FORMAT(i.end_date,'%d-%m-%Y %H:%i') AS end_date, a.status as status,a.activity_id as activity_id from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (month(a.created_date) = $this_month) and (a.activity_type='Site Visit') GROUP BY a.activity_id ORDER BY a.created_date DESC";

        $selectsite_visits = $db->getAllRecords($sql);
        echo json_encode($selectsite_visits);
    }

    if ($category=='client_meeting')
    {
        $sql = "SELECT *, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date, DATE_FORMAT(i.end_date,'%d-%m-%Y %H:%i') AS end_date, a.status as status,a.activity_id as activity_id from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (month(a.created_date) = $this_month) and (a.activity_type='Meeting') GROUP BY a.activity_id ORDER BY a.created_date DESC";

        $selectclient_meetings = $db->getAllRecords($sql);
        echo json_encode($selectclient_meetings);
    }


    if ($category=='deal')
    {
        $sql = "SELECT *, CONCAT(h.salu,' ',h.fname,' ', h.lname) as employee_name, CONCAT(c.name_title,' ',c.f_name,' ', c.l_name) as client, CONCAT(d.name_title,' ',d.f_name,' ', d.l_name) as developer, CONCAT(e.name_title,' ',e.f_name,' ', e.l_name) as broker,  DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, DATE_FORMAT(i.start_date,'%d-%m-%Y %H:%i') AS start_date, DATE_FORMAT(i.end_date,'%d-%m-%Y %H:%i') AS end_date, a.status as status,a.activity_id as activity_id from activity as a LEFT JOIN enquiry as b on a.enquiry_id = b.enquiry_id LEFT JOIN contact as c ON a.client_id = c.contact_id and c.contact_off = 'Client' LEFT JOIN contact as d on a.developer_id = d.contact_id and d.contact_off = 'Developer' LEFT JOIN contact e on a.broker_id = e.contact_id and e.contact_off = 'Broker' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on h.emp_id = g.emp_id LEFT JOIN activity_details as i on a.activity_id = i.activity_id where (a.created_by = $user_id or a.modified_by = $user_id) and (month(a.created_date) = $this_month) and (i.status = 'Deal Done') GROUP BY a.activity_id ORDER BY a.created_date DESC";

        $selectdeals = $db->getAllRecords($sql);
        echo json_encode($selectdeals);
    }

});

// EMAIL ACCOUNTS

$app->get('/email_accounts_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }

    $sql = "SELECT * FROM email_accounts ORDER BY email_account";
    $listemail_accounts = $db->getAllRecords($sql);
    echo json_encode($listemail_accounts);
});

$app->post('/email_accounts_add_new', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email_account'),$r->email_accounts);
    $db = new DbHandler();
    $user_id = $r->email_accounts->user_id;

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $r->email_accounts->created_by = $created_by;
    $r->email_accounts->created_date = $created_date;
    
    $tabble_name = "email_accounts";

    $column_names = array('assign_to', 'email_account', 'from_name', 'password', 'smtp_host', 'smtp_port', 'smtp_username', 'mail_properties', 'teams', 'created_by','created_date');
    $multiple=array("assign_to","teams");
    $result = $db->insertIntoTable($r->email_accounts, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Email Account created successfully";
        $response["email_accounts_id"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create Email Account. Please try again";
        echoResponse(201, $response);
    }
});


$app->get('/email_accounts_edit_ctrl/:email_accounts_id', function($email_accounts_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from email_accounts where email_accounts_id=".$email_accounts_id;
    $dataemail_accounts = $db->getAllRecords($sql);
    echo json_encode($dataemail_accounts);
    
});

$app->post('/email_accounts_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email_account'),$r->email_accounts);
    $db = new DbHandler();
    $email_accounts_id  = $r->email_accounts->email_accounts_id;

    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->email_accounts->modified_by = $modified_by;
    $r->email_accounts->modified_date = $modified_date;

    $isRoleExists = $db->getOneRecord("select 1 from email_accounts where email_accounts_id=$email_accounts_id");
    if($isRoleExists){
        $tabble_name = "email_accounts";
        $column_names = array('assign_to', 'email_account', 'from_name', 'password', 'smtp_host', 'smtp_port', 'smtp_username', 'mail_properties', 'teams', 'modified_by','modified_date');
        $condition = "email_accounts_id='$email_accounts_id'";
        $multiple=array("assign_to","teams");
        $history = $db->historydata( $r->email_accounts, $column_names, $tabble_name,$condition,$email_account_id,$multiple, $modified_by, $modified_date);
        $result = $db->NupdateIntoTable($r->email_accounts, $column_names, $tabble_name,$condition,$multiple);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Email Account Updated successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Email Account. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Email Account with the provided email_account does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/email_accounts_delete', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email_account'),$r->email_accounts);
    $db = new DbHandler();
    $email_accounts_id  = $r->email_accounts->email_accounts_id;
    $isGoalExists = $db->getOneRecord("select 1 from email_accounts where email_accounts_id=$email_accounts_id");
    if($isGoalExists){
        $tabble_name = "email_accounts";
        $column_names = array('email_account');
        $condition = "email_accounts_id='$email_accounts_id'";
        $result = $db->deleteIntoTable($r->email_accounts, $column_names, $tabble_name,$condition);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Email Account Deleted successfully";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to Delete Email Account. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "Email Account with the provided email_account does not exists!";
        echoResponse(201, $response);
    }
});

$app->get('/selectemail_accounts', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from email_accounts ORDER BY email_accounts_id";
    $selectemail_accounts = $db->getAllRecords($sql);
    echo json_encode($selectemail_accounts);
});


// API FOR PROPERTIES  https://crm.rdbrothers.com/api/v1/dfjhjdf/kdfgjijirllDFDFkoEERERppppp/0/12
$app->get('/dfjhjdf/:api_key/:start_number/:numofrows', function($api_key,$start_number,$numofrows) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($api_key!="kdfgjijirllDFDFkoEERERppppp")
    {
        echo json_encode("API KEY Incorrect...!!!");
        return;
    }

    if ($start_number<=0)
    {
        $start_number = 0;
    }
    if ($numofrows<=0)
    {
        $numofrows = 12;
    }
    //$sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms, i.contact_off,a.share_on_website,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
    

    $sql = "SELECT a.property_id, a.proptype, b.project_name, a.building_name, a.bedrooms, a.propsubtype, a.property_for,c.locality, d.area_name,e.city,a.exp_price,a.exp_price_para,carp_area,carp_area_para, a.furniture,a.featured, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS added_on,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames, (SELECT GROUP_CONCAT(n.filenames SEPARATOR ',') FROM attachments as n where n.category_id = a.property_id and n.category = 'property' and n.share_on_web= 'true' and n.file_categoty!='Floor Plans') as image_files from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.share_on_website = 'Yes' and a.proj_status='Available' GROUP BY a.property_id ORDER BY a.modified_date DESC LIMIT $start_number, $numofrows";
    
    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);
});

// API FOR PROPERTIES  https://crm.rdbrothers.com/api/v1/dfjhjdf_f/kdfgjijirllDFDFkoEERERppppp/0/12 (FLOOR PLANS)
$app->get('/dfjhjdf_f/:api_key/:start_number/:numofrows', function($api_key,$start_number,$numofrows) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($api_key!="kdfgjijirllDFDFkoEERERppppp")
    {
        echo json_encode("API KEY Incorrect...!!!");
        return;
    }

    if ($start_number<=0)
    {
        $start_number = 0;
    }
    if ($numofrows<=0)
    {
        $numofrows = 12;
    }
    //$sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms, i.contact_off,a.share_on_website,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
    

    $sql = "SELECT a.property_id, a.proptype, b.project_name, a.building_name, a.bedrooms, a.propsubtype, a.property_for,c.locality, d.area_name,e.city,a.exp_price,a.exp_price_para,carp_area,carp_area_para, a.furniture,a.featured, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS added_on,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames, (SELECT GROUP_CONCAT(n.filenames SEPARATOR ',') FROM attachments as n where n.category_id = a.property_id and n.category = 'property' and n.share_on_web= 'true' and n.file_categoty='Floor Plans') as image_files from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.share_on_website = 'Yes' and a.proj_status='Available' GROUP BY a.property_id ORDER BY a.modified_date DESC LIMIT $start_number, $numofrows";
    
    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);
});


// API FOR PROJECTS https://crm.rdbrothers.com/api/v1/qryrees/kdfgjijirllDFDFkoEERERppppp/0/12
$app->get('/qryrees/:api_key/:start_number/:numofrows', function($api_key,$start_number,$numofrows) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($api_key!="kdfgjijirllDFDFkoEERERppppp")
    {
        echo json_encode("API KEY Incorrect...!!!");
        return;
    }
    if ($start_number<=0)
    {
        $start_number = 0;
    }
    if ($numofrows<=0)
    {
        $numofrows = 12;
    }

    //$sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  GROUP BY a.project_id ORDER BY CAST(a.project_id as UNSIGNED) DESC";

    $sql = "SELECT a.project_id,a.project_name,a.project_type,a.tot_area as plot_area,a.area_parameter as plot_area_in,a.numof_building,a.numof_floor,a.tot_unit as units,a.con_status as construction_status,a.amenities_avl as amenities,a.external_comment as about_project, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS added_on,  f.locality as locality, g.area_name as area_name,h.city, a.pack_price, e.company_name as developer ,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames, (SELECT GROUP_CONCAT(n.filenames SEPARATOR ',') FROM attachments as n where n.category_id = a.project_id and n.category = 'Project' and n.share_on_web = 'true' and n.file_category!='Floor Plans') as image_files, a.distfrm_station as dist_station,a.distfrm_dairport as dist_airport, a.distfrm_school as dist_school,a.distfrm_market as dist_market,DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date,(SELECT CONCAT(max(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price,(SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list, (SELECT GROUP_CONCAT(CONCAT(file_sub_category,':',filenames) SEPARATOR ',') from attachments where category='Project' and category_id=a.project_id and file_category='Floor Plans') as configuration from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id LEFT JOIN city as h on h.city_id = g.city_id WHERE a.share_on_website = 'Yes' and a.proj_status='Available' GROUP BY a.project_id ORDER BY a.created_date DESC LIMIT $start_number, $numofrows";
    

    //$sql = "SELECT a.proptype, b.project_name, a.building_name, a.bedrooms, a.propsubtype, a.property_for,c.locality, d.area_name,e.city,a.exp_price,a.exp_price_para,carp_area,carp_area_para, a.furniture, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS added_on,f.filenames from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.share_on_website = 'Yes' and a.proj_status='Available' GROUP BY a.property_id ORDER BY a.modified_date DESC";
    $projects = $db->getAllRecords($sql);
    echo json_encode($projects);
});

// API FOR PROJECTS https://crm.rdbrothers.com/api/v1/qryrees_testing/kdfgjijirllDFDFkoEERERppppp/0/12
$app->get('/qryrees_testing/:api_key/:start_number/:numofrows', function($api_key,$start_number,$numofrows) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($api_key!="kdfgjijirllDFDFkoEERERppppp")
    {
        echo json_encode("API KEY Incorrect...!!!");
        return;
    }
    if ($start_number<=0)
    {
        $start_number = 0;
    }
    if ($numofrows<=0)
    {
        $numofrows = 12;
    }

    //$sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  GROUP BY a.project_id ORDER BY CAST(a.project_id as UNSIGNED) DESC";

    $sql = "SELECT a.project_id,a.project_name,a.project_type,a.tot_area as plot_area,a.area_parameter as plot_area_in,a.numof_building,a.numof_floor,a.tot_unit as units,a.con_status as construction_status,a.amenities_avl as amenities,a.external_comment as about_project, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS added_on,  f.locality as locality, g.area_name as area_name,h.city, a.pack_price, e.company_name as developer ,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames, (SELECT GROUP_CONCAT(n.filenames SEPARATOR ',') FROM attachments as n where n.category_id = a.project_id and n.category = 'Project' and n.share_on_web = 'true' and n.file_category!='Floor Plans') as image_files, a.distfrm_station as dist_station,a.distfrm_dairport as dist_airport, a.distfrm_school as dist_school,a.distfrm_market as dist_market,DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date,(SELECT CONCAT(max(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price,(SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list, (SELECT GROUP_CONCAT(CONCAT(file_sub_category,':',filenames) SEPARATOR ',') from attachments where category='Project' and category_id=a.project_id and file_category='Floor Plans') as configuration from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id LEFT JOIN city as h on h.city_id = g.city_id WHERE a.share_on_website = 'Yes' and a.proj_status='Available' GROUP BY a.project_id ORDER BY a.created_date DESC LIMIT $start_number, $numofrows";
    
    //TESTED OK LINE ---->>>(SELECT CONCAT(GROUP_CONCAT(file_sub_category SEPARATOR ','),',',GROUP_CONCAT(filenames SEPARATOR ',')) from attachments where category='Project' and category_id=a.project_id and file_category='Floor Plans') as configuration

    //$sql = "SELECT a.proptype, b.project_name, a.building_name, a.bedrooms, a.propsubtype, a.property_for,c.locality, d.area_name,e.city,a.exp_price,a.exp_price_para,carp_area,carp_area_para, a.furniture, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS added_on,f.filenames from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.share_on_website = 'Yes' and a.proj_status='Available' GROUP BY a.property_id ORDER BY a.modified_date DESC";
    $projects = $db->getAllRecords($sql);
    echo json_encode($projects);
});



// API FOR PROJECTS https://crm.rdbrothers.com/api/v1/qryrees_f/kdfgjijirllDFDFkoEERERppppp/0/12 (FLOOR PLANS)
$app->get('/qryrees_f/:api_key/:start_number/:numofrows', function($api_key,$start_number,$numofrows) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($api_key!="kdfgjijirllDFDFkoEERERppppp")
    {
        echo json_encode("API KEY Incorrect...!!!");
        return;
    }
    if ($start_number<=0)
    {
        $start_number = 0;
    }
    if ($numofrows<=0)
    {
        $numofrows = 12;
    }

    //$sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  GROUP BY a.project_id ORDER BY CAST(a.project_id as UNSIGNED) DESC";

    $sql = "SELECT a.project_id,a.project_name,a.project_type,a.tot_area as plot_area,a.area_parameter as plot_area_in,a.numof_building,a.numof_floor,a.tot_unit as units,a.con_status as construction_status,a.amenities_avl as amenities,a.external_comment as about_project, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS added_on,  f.locality as locality, g.area_name as area_name,h.city, a.pack_price, e.company_name as developer ,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames, (SELECT GROUP_CONCAT(n.filenames SEPARATOR ',') FROM attachments as n where n.category_id = a.project_id and n.category = 'Project' and n.share_on_web = 'true' and n.file_category='Floor Plans') as image_files, a.distfrm_station as dist_station,a.distfrm_dairport as dist_airport, a.distfrm_school as dist_school,a.distfrm_market as dist_market,DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date,(SELECT CONCAT(max(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price,(SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id LEFT JOIN city as h on h.city_id = g.city_id WHERE a.share_on_website = 'Yes' and a.proj_status='Available' GROUP BY a.project_id ORDER BY a.created_date DESC LIMIT $start_number, $numofrows";
    

    //$sql = "SELECT a.proptype, b.project_name, a.building_name, a.bedrooms, a.propsubtype, a.property_for,c.locality, d.area_name,e.city,a.exp_price,a.exp_price_para,carp_area,carp_area_para, a.furniture, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS added_on,f.filenames from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.share_on_website = 'Yes' and a.proj_status='Available' GROUP BY a.property_id ORDER BY a.modified_date DESC";
    $projects = $db->getAllRecords($sql);
    echo json_encode($projects);
});

// API FOR 99 ACRES PROPERTIES  https://crm.rdbrothers.com/api/v1/tiuytiy/ruirotnkjfbkjfgndk4344
$app->get('/tiuytiy/:api_key', function($api_key) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();

    return;


    if ($api_key!="ruirotnkjfbkjfgndk4344")
    {
        echo json_encode("API KEY Incorrect...!!!");
        return;
    }

    //$sql = "SELECT *,a.amenities_avl,a.exp_price,a.rera_num,a.parking,a.car_park,a.furniture,a.pro_inspect,a.numof_floor,a.washrooms,a.occu_certi,a.floor,a.bedrooms, i.contact_off,a.share_on_website,SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,DATE_FORMAT(a.lease_start,'%d-%m-%Y') AS lease_start,DATE_FORMAT(a.lease_end,'%d-%m-%Y') AS lease_end,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.proptype = '$cat' GROUP BY a.property_id ORDER BY a.modified_date DESC";// CAST(a.property_id as UNSIGNED) DESC";
    
    
    $sql = "SELECT a.property_id, a.proptype, b.project_name, a.building_name, a.bedrooms, a.propsubtype, a.property_for,c.locality, d.area_name,e.city,a.exp_price,a.exp_price_para,carp_area,carp_area_para, a.furniture,a.featured, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS added_on,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames,(SELECT GROUP_CONCAT(n.filenames SEPARATOR ',') FROM attachments as n where n.category_id = a.property_id and n.category = 'property' and share_on_web= 'true') as image_files from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.share_on_99 = 'Yes' and a.proj_status='Available' GROUP BY a.property_id ORDER BY a.modified_date DESC"; // LIMIT $start_number, $numofrows";
    $properties = $db->getAllRecords($sql);
    echo json_encode($properties);
});


// API FOR 99 ACRES PROJECTS https://crm.rdbrothers.com/api/v1/djsdfjalskdf/zlkjdflaksjdfwuiweo

$app->get('/djsdfjalskdf/:api_key', function($api_key) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($api_key!="zlkjdflaksjdfwuiweo")
    {
        echo json_encode("API KEY Incorrect...!!!");
        return;
    }
    

    //$sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name from project as a LEFT JOIN attachments as b ON a.project_id = b.category_id and b.category='Project' LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id  GROUP BY a.project_id ORDER BY CAST(a.project_id as UNSIGNED) DESC";

    //$sql = "SELECT a.project_id,a.project_name,a.project_type,a.tot_area as plot_area,a.area_parameter as plot_area_in,a.numof_building,a.numof_floor,a.tot_unit as units,a.con_status as construction_status,a.amenities_avl as amenities,a.external_comment as about_project, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS added_on,  f.locality as locality, g.area_name as area_name,h.city, a.pack_price, e.company_name as developer, ,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames, (SELECT GROUP_CONCAT(n.filenames SEPARATOR ',') FROM attachments as n where n.category_id = a.project_id and n.category = 'Project' and n.share_on_web = 'true') as image_files, a.distfrm_station as dist_station,a.distfrm_dairport as dist_airport, a.distfrm_school as dist_school,a.distfrm_market as dist_market,DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date,(SELECT CONCAT(max(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price,(SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id LEFT JOIN city as h on h.city_id = g.city_id WHERE a.share_on_99 = 'Yes' and (a.proj_status='Available' OR a.proj_status='Completed')  GROUP BY a.project_id ORDER BY a.created_date DESC";// LIMIT $start_number, $numofrows";

    // ORIGINAL
    /*$sql = "SELECT a.project_id,a.project_name,a.project_type,a.tot_area as plot_area,a.area_parameter as plot_area_in,a.numof_building,a.numof_floor,a.tot_unit as units,a.con_status as construction_status,a.amenities_avl as amenities,a.external_comment as about_project, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS added_on,  f.locality as locality, g.area_name as area_name,h.city, a.pack_price, e.company_name as developer, ,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames, (SELECT GROUP_CONCAT(n.filenames SEPARATOR ',') FROM attachments as n where n.category_id = a.project_id and n.category = 'Project' and n.share_on_web = 'true') as image_files, a.distfrm_station as dist_station,a.distfrm_dairport as dist_airport, a.distfrm_school as dist_school,a.distfrm_market as dist_market,DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date,(SELECT CONCAT(max(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as max_price,(SELECT CONCAT(min(exp_price),' ',any_value(exp_price_para)) FROM property WHERE project_id= a.project_id and exp_price > 0 ) as min_price,(SELECT GROUP_CONCAT(DISTINCT(r.bedrooms) SEPARATOR ',') from project as q left join property as r on q.project_id = r.project_id where r.bedrooms is not null and r.bedrooms > 0 and r.project_id = a.project_id) as bedroom_list from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id LEFT JOIN city as h on h.city_id = g.city_id WHERE a.share_on_99 = 'Yes' and (a.proj_status='Available' OR a.proj_status='Completed')  GROUP BY a.project_id ORDER BY a.created_date DESC"*/

    $sql = "SELECT project.project_name, CONCAT(employee.salu,' ',employee.fname,' ',employee.lname) as name, employee.mobile_no,employee.off_email as email, DATE_FORMAT(project.created_date,'%d-%m-%Y') as creation_date  FROM project LEFT JOIN users on project.assign_to = users.user_id LEFT JOIN employee on employee.emp_id = users.emp_id WHERE project.share_on_99 = 'Yes' and (project.proj_status='Available' OR project.proj_status='Completed')";

    //$sql = "SELECT a.proptype, b.project_name, a.building_name, a.bedrooms, a.propsubtype, a.property_for,c.locality, d.area_name,e.city,a.exp_price,a.exp_price_para,carp_area,carp_area_para, a.furniture, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS added_on,f.filenames from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id LEFT JOIN attachments as f on a.property_id = f.category_id and f.category='property' and f.isdefault = 'true' LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.share_on_website = 'Yes' and a.proj_status='Available' GROUP BY a.property_id ORDER BY a.modified_date DESC";
    $projects = $db->getAllRecords($sql);
    echo json_encode($projects);
});



// API FOR MARKETING  https://crm.rdbrothers.com/api/v1/marketing/tyutiyutiyutjgg867590550/9820129163/0
$app->get('/marketing/:api_key/:mobile_no/:email_id', function($api_key,$mob_no,$email_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($api_key!="tyutiyutiyutjgg867590550")
    {
        echo json_encode("API KEY Incorrect...!!!");
        return;
    }
    if ($mob_no || $mob_no !='0')
    {

    }
    else{
        $mob_no = "---";
    }
    if ($email_id || $email_id !='0' )
    {

    }
    else{
        $email_id = "---";
    }
    $response = array();
    $sql = "SELECT contact_id, CONCAT(name_title,' ',f_name,' ',l_name) as client_name, contact_id, mob_no, email FROM contact WHERE mob_no = '$mob_no' OR email = '$email_id' LIMIT 1";

    $searched_mob_no = "";
    $searched_contact_id = "";
    $searched_email_id = "";
    $searched_client_name = "";
    $searched_in = "";
    $data = $db->getAllRecords($sql);
    $record_found = "No";
    if ($data)
    {
         $searched_mob_no = $data[0]['mob_no'];
         $searched_email_id = $data[0]['email'];
         $searched_client_name = $data[0]['client_name'];
         $searched_contact_id = $data[0]['contact_id'];
         $record_found = "Yes";
         $searched_in = "contact";
    }
    else
    {
        $sql1 = "SELECT referrals_id, broker_id, mobile_no, email FROM referrals WHERE mobile_no = '$mob_no' OR email =  '$email_id' LIMIT 1";
        $data1 = $db->getAllRecords($sql1);
        if ($data1)
        {
            $searched_mob_no = $data1[0]['mobile_no'];
            $searched_email_id = $data1[0]['email'];
            $searched_client_name = $data1[0]['client_name'];
            $searched_contact_id = $data[0]['referrals_id'];
            $record_found = "Yes";
            $searched_in = "referrals";
        }
    }

    if ($record_found=='Yes')
    {
        $response["status"] = "success";
        $response["searched_mob_no"] = $searched_mob_no;
        $response["searched_email_id"] = $searched_email_id;
        $response["searched_client_name"] = $searched_client_name;
        $response["searched_contact_id"] = $searched_contact_id;
        $response["searched_in"] = $searched_in;
        $response["message"] = "Contact Found .. !!!";
    
        //$response["sql"] = $sql;
        //$response["sql1"] = $sql1;
        echoResponse(200, $response);

    }
    else
    {
        $response["status"] = "not in database";
        $response["message"] = "Contact Not Found .. !!!";
        //$response["sql"] = $sql;
        //$response["sql1"] = $sql1;
        echoResponse(200, $response);
    }
});


// API FOR TATA TELESERVICES
$app->post('/getcallingdata', function() use ($app) {
    
    $db = new DbHandler();
    $session = $db->getSession();
    $r = json_decode($app->request->getBody());
    
    $created_by = 0;
    $created_date = date('Y-m-d H:i:s');

    $call_id="";
    $call_to_number="";   
    $caller_id_number="";   
    $start_stamp="";   
    $start_date="";   
    $start_time="";   
    $direction="";   
    $call_flow="";   
    $lead_fields="";   
    $campaign_name="";   
    $campaign_id="";   
    $billing_circle="";   
    $agent="";   
    $agent_name="";   
    $agent_number="";
    $uuid="";
    $date="";
    $dept_name="";
    $ivr_name="";

    if (isset($r->call_id)){
        $call_id=$r->call_id;
    }
    if (isset($r->call_to_number)){
        $call_to_number=$r->call_to_number; 
    }   
    if (isset($r->caller_id_number)){
        $caller_id_number=$r->caller_id_number; 
    }   

    if (isset($r->uuid)){
        $uuid=$r->uuid;
    }

    if (isset($r->date)){
        $date=$r->date;
    }

    if (isset($r->agent_name)){
        $agent_name=$r->agent_name;
    }   
    if (isset($r->agent_number)){
        $agent_number=$r->agent_number;
    }
    
    $call_flow = ($app->request->getBody());
    $query = "INSERT INTO received_data (uuid, date, call_id, call_to_number, caller_id_number, call_flow, agent_name,agent_number, created_date)   VALUES('$uuid','$date','$call_id','$call_to_number','$caller_id_number', '$call_flow','$agent_name','$agent_number','$created_date')";
    $result = $db->insertByQuery($query);
/*
    

    //$sql = "UPDATE received_data set call_flow = '$call_flow' where tata_calling_id = $result " ;  
    //$result1 = $db->updateByQuery($sql);

    if (isset($r->start_stamp)){
        $start_stamp=$r->start_stamp; 
    }   
    if (isset($r->start_date)){
        $start_date=$r->start_date;
    }   
    if (isset($r->start_time)){
        $start_time=$r->start_time; 
    }   
    if (isset($r->direction)){
        $direction=$r->direction; 
    }   
    if (isset($r->call_flow)){
        $call_flow=$r->call_flow;  
    }   
    if (isset($r->lead_fields)){
        $lead_fields=$r->lead_fields; 
    }   
    if (isset($r->campaign_name)){
        $campaign_name=$r->campaign_name;  
    }   
    if (isset($r->campaign_id)){
        $campaign_id=$r->campaign_id;
    }   
    if (isset($r->billing_circle)){
        $billing_circle=$r->billing_circle;
    }   
    if (isset($r->agent)){
        $agent=$r->agent;
     }   
    

    if (isset($r->dept_name)){
        $dept_name=$r->dept_name;
    }

    if (isset($r->ivr_name)){
        $ivr_name=$r->ivr_name;
    }

    if (isset($r->uuid))
    {
     
        $query = "INSERT INTO tata_calling (call_id, call_to_number, caller_id_number, start_stamp, start_date, start_time, direction, call_flow, lead_fields, campaign_name, campaign_id,  agent, agent_name, agent_number, created_by, created_date, uuid, date,dept_name,ivr_name,billing_circle)   VALUES('$call_id','$call_to_number','$caller_id_number','$start_stamp','$start_date','$start_time','$direction','$call_flow','$lead_fields','$campaign_name','$campaign_id','$agent','$agent_name','$agent_number','$created_by','$created_date', '$uuid','$date', '$dept_name', '$ivr_name', '$billing_circle' )"; 
        
        $result = $db->insertByQuery($query);
        echo json_encode(['success'=>'Data Received']); 
    }
    else{
        echo json_encode(['error'=>'Data Not Received']);
    }
    */

});


$app->get('/getcallingdata_list', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from tata_calling ORDER BY created_date DESC";
    $callingdata_list = $db->getAllRecords($sql);
    echo json_encode($callingdata_list);
});

$app->get('/callinghistory_list', function() use ($app) {
    $sql  = "";
   // error_log("Vipin22", 3, "logfile.log");
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api-smartflo.tatateleservices.com/v1/call/records?limit=2500",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjI1OTQ2OSwiaXNzIjoiaHR0cHM6XC9cL2Nsb3VkcGhvbmUudGF0YXRlbGVzZXJ2aWNlcy5jb21cL3Rva2VuXC9nZW5lcmF0ZSIsImlhdCI6MTY1MTEzOTIwNSwiZXhwIjoxOTUxMTM5MjA1LCJuYmYiOjE2NTExMzkyMDUsImp0aSI6ImxuZ0ZmWkxNY3FJM016QkMifQ.Ik57KgZ9qjCphq8ymxjifKSnSin6-yU6pU5qJsjtoMk"
        ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    } else 
    {
        //echo $response;
        $json = json_decode($response, true);
        foreach($json as $key => $val) {
            //echo "KEY:".$key;
            if ($key=='results')
            {
                foreach($val as $key1 => $val1) 
                {
                    $id = $val1['id'];
                    $call_id = $val1['call_id'];
                    $uuid= $val1['uuid'];
                    $direction = $val1['direction'];
                    $description = $val1['description'];
                    $detailed_description = $val1['detailed_description'];
                    $status = $val1['status'];
                    $blocked_number_id = $val1['blocked_number_id'];
                    $recording_url = $val1['recording_url'];
                    $service = $val1['service'];
                    $date = $val1['date'];
                    $time = $val1['time'];
                    $end_stamp = $val1['end_stamp'];
                    $broadcast_id = $val1['broadcast_id'];
                    $dtmf_input = $val1['dtmf_input'];
                    $call_duration = $val1['call_duration'];
                    $answered_seconds = $val1['answered_seconds'];
                    $minutes_consumed = $val1['minutes_consumed'];
                    $charges = $val1['charges'];
                    $department_name = $val1['department_name'];
                    $agent_number = $val1['agent_number'];
                    $agent_name = $val1['agent_name'];
                    $client_number = $val1['client_number'];
                    $did_number = $val1['did_number'];
                    $reason = $val1['reason'];
                    $hangup_cause = $val1['hangup_cause'];
                    $notes = $val1['notes'];
                    $contact_details = $val1['contact_details'];
                    
                    $isExists = $db->getOneRecord("select 1 from callinghistory WHERE id = '$id'");
                    if(!$isExists)
                    {
                        $query = "INSERT INTO callinghistory (id,call_id,uuid,direction,c_description,detailed_description,c_status,blocked_number_id,recording_url,c_service,c_date,c_time,end_stamp,broadcast_id,dtmf_input,call_duration,answered_seconds,minutes_consumed,charges,department_name,agent_number,agent_name,client_number,did_number,reason,hangup_cause,notes,contact_details,created_by, created_date)   VALUES('$id','$call_id','$uuid','$direction','$description','$detailed_description','$status','$blocked_number_id','$recording_url','$service','$date','$time','$end_stamp','$broadcast_id','$dtmf_input','$call_duration','$answered_seconds','$minutes_consumed','$charges','$department_name','$agent_number','$agent_name','$client_number','$did_number','$reason','$hangup_cause','$notes','$contact_details','$created_by','$created_date' )";
                        $result = $db->insertByQuery($query);
                        
                    }
                    else{
                        //echo json_encode(['error'=>'Data Not Received']);
                    }

                }
            }  
        }  
        //print_r($json);
        //echo json_encode(['success'=>'Data Received']); */
    }   

    $sql = "SELECT *, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') AS created_date,DATE_FORMAT(c_date,'%d-%m-%Y') AS c_date,DATE_FORMAT(end_stamp,'%d-%m-%Y %H:%i') AS tend_stamp from callinghistory ORDER BY end_stamp DESC";
    $callinghistory_list = $db->getAllRecords($sql);
    
    // error_log("Vipin33", 3, "logfile.log");
    // error_log(print_r($callinghistory_list, true), 3, "logfile.log");
    // error_log("Vipin33", 3, "logfile.log");
    echo json_encode($callinghistory_list);
});

// =-0op[']\
// ']+ 1`
$app->get('/searchall/:find_what', function($find_what) use ($app) {
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
    $htmlstring .= '<table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Search Result</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>';

    $sql = "SELECT *,a.exp_price,a.rera_num,a.property_id, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id > 0";
    
    $sql .= " and (a.proj_status LIKE '%".$find_what."%' OR a.propsubtype LIKE '%$find_what%'  OR a.property_for LIKE '%$find_what%'   OR a.project_name LIKE '%$find_what%' OR a.building_name LIKE '%$find_what%' OR d.area_name LIKE '%$find_what%' OR c.locality LIKE '%$find_what%'  OR e.city LIKE '%$find_what%' OR i.f_name LIKE '%$find_what%' OR i.l_name LIKE '%$find_what%' OR a.wing LIKE '%$find_what%' OR a.unit LIKE '%$find_what%' OR a.rera_num LIKE '%$find_what%'  OR a.furniture LIKE '%$find_what%' OR a.source_channel LIKE '%$find_what%'  OR a.subsource_channel LIKE '%$find_what%'  OR a.amenities_avl LIKE '%$find_what%' OR a.floor LIKE '%$find_what%'  OR a.con_status LIKE '%$find_what%'  OR i.mob_no LIKE '%$find_what%'   OR i.email LIKE '%$find_what%'  OR a.landmark LIKE '%$find_what%' OR a.parking LIKE '%$find_what%'   OR a.road_no LIKE '%$find_what%'  OR a.priority LIKE '%$find_what%'  OR a.property_id LIKE '%$find_what%' ) ";

    $sql .= " GROUP BY a.property_id ORDER BY a.modified_date DESC ";  
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            
            $htmlstring .= '<tr><td>Property</td><td><p style="font-size:18px;"><a href="properties_edit/'.$row['property_id'].'">'.$row['proptype'].'_'.$row['property_id'].'</a>';
            if ($row['project_name'])
            {
                $htmlstring .= '&nbsp;'.$row['project_name'];
            }
            if ($row['building_name'])
            {
                $htmlstring .= '&nbsp;'.$row['building_name'];
            }
            $htmlstring .= '&nbsp;'.$row['bedrooms'].' BHK '.$row['propsubtype']. ' for '.$row['property_for'].'</p>';
            $htmlstring .= '<p>'.$row['area_name'].' '.$row['locality'].' '.$row['city'].'</p>';
            $htmlstring .= '</td><td>'.$row['created_date'].'</td></tr>';
        }
    }

    $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id LEFT JOIN city as i on g.city_id = i.city_id WHERE a.created_by > 0 ";
    
    $sql .= " and (a.project_name LIKE '%$find_what%' OR g.area_name LIKE '%$find_what%' OR f.locality LIKE '%$find_what%'  OR i.city LIKE '%$find_what%' OR a.project_id LIKE '%$find_what%' ) ";

    $sql .= " GROUP BY a.project_id ORDER BY a.created_date DESC ";  
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            
            $htmlstring .= '<tr><td>Project</td><td><p style="font-size:18px;"><a href="project_edit/'.$row['project_id'].'">Project_'.$row['project_id'].'</a>';
            if ($row['project_name'])
            {
                $htmlstring .= '&nbsp;'.$row['project_name'];
            }
            $htmlstring .= '&nbsp;</p>';
            $htmlstring .= '<p>'.$row['area_name'].' '.$row['locality'].' '.$row['city'].'</p>';
            $htmlstring .= '</td><td>'.$row['created_date'].'</td></tr>';
        }
    }

    $sql = "SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob, SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name, c.email as broker_email, c.mob_no as broker_mob,e.locality as preferred_locality,(SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id LEFT JOIN city as i ON i.city_id = f.city_id WHERE a.created_by > 0 ";
    
    $sql .= " and (f.area_name LIKE '%$find_what%' OR e.locality LIKE '%$find_what%'  OR i.city LIKE '%$find_what%' OR a.enquiry_id LIKE '%$find_what%' ) ";

    $sql .= " GROUP BY a.enquiry_id ORDER BY a.created_date DESC ";  
    //echo $sql;
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<tr><td>Enquiry</td><td><p style="font-size:18px;"><a href="enquiries_edit/'.$row['enquiry_id'].'">'.$row['enquiry_off'].'_'.$row['enquiry_id'].'</a> Looing to '.$row['enquiry_for'].' : '.$row['enquiry_type'];
            $htmlstring .= '&nbsp;</p>';
            $htmlstring .= '<p>'.$row['area_name'].' '.$row['locality'].' '.$row['city'].'</p>';
            $htmlstring .= '</td><td>'.$row['created_date'].'</td></tr>';
        }
    }

    $htmlstring .= '    </tbody>
                    </table>';

    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/search_client_information/:find_what', function($find_what) use ($app) {
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
    $htmlstring .= '<table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Client Activity Details</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>';

    $sql = "SELECT *,concat(name_title,' ',f_name,' ',l_name) as name FROM contact WHERE mob_no LIKE '%$find_what%' OR mob_no1 LIKE '%$find_what%' OR email LIKE '%$find_what%' OR alt_phone_no LIKE '%$find_what%' OR alt_phone_no1 LIKE '%$find_what%' LIMIT 1 ";  
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<tr><td style="font-size:18px;font-weight:500;">Client:</td><td><p style="font-size:18px;"><a href="/#/contacts_edit/'.$row['contact_id'].'"  target="_blank">'.$row['contact_off'].'_'.$row['contact_id'].' - '.$row['name'].'</a></td><td></td></tr>';
        }
    }

    $sql = "SELECT *,a.exp_price,a.rera_num,a.property_id, SUBSTRING(i.f_name,1,1) as f_char, CONCAT(h.salu,' ',h.fname,' ',h.lname) as project_contact,  h.mobile_no as usermobileno, (SELECT count(*) from attachments as t where t.category_id = a.property_id and t.category = 'property') as property_image_count, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,  DATE_FORMAT(a.possession_date,'%d-%m-%Y') AS possession_date, DATE_FORMAT(a.completion_date,'%d-%m-%Y') AS completion_date,c.locality as locality,e.city as city, d.area_name as area_name, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, concat(i.name_title,' ',i.f_name,' ',i.l_name) as owner_name,a.proj_status,(SELECT filenames FROM attachments WHERE a.property_id = category_id and category='property' and isdefault = 'true' LIMIT 1) as filenames  from property as a LEFT JOIN project as b on a.project_id = b.project_id LEFT JOIN locality as c on a.locality_id = c.locality_id LEFT JOIN areas as d on a.area_id = d.area_id LEFT JOIN city as e on d.city_id = e.city_id  LEFT JOIN users as g on a.assign_to = g.user_id LEFT JOIN employee as h on g.emp_id = h.emp_id LEFT JOIN contact as i ON a.dev_owner_id = i.contact_id LEFT JOIN users as z on a.created_by = z.user_id WHERE a.property_id > 0";
    
    $sql .= " and (a.proj_status LIKE '%".$find_what."%' OR a.propsubtype LIKE '%$find_what%'  OR a.property_for LIKE '%$find_what%'   OR a.project_name LIKE '%$find_what%' OR a.building_name LIKE '%$find_what%' OR d.area_name LIKE '%$find_what%' OR c.locality LIKE '%$find_what%'  OR e.city LIKE '%$find_what%' OR i.f_name LIKE '%$find_what%' OR i.l_name LIKE '%$find_what%' OR a.wing LIKE '%$find_what%' OR a.unit LIKE '%$find_what%' OR a.rera_num LIKE '%$find_what%'  OR a.furniture LIKE '%$find_what%' OR a.source_channel LIKE '%$find_what%'  OR a.subsource_channel LIKE '%$find_what%'  OR a.amenities_avl LIKE '%$find_what%' OR a.floor LIKE '%$find_what%'  OR a.con_status LIKE '%$find_what%'  OR i.mob_no LIKE '%$find_what%'   OR i.email LIKE '%$find_what%'  OR a.landmark LIKE '%$find_what%' OR a.parking LIKE '%$find_what%'   OR a.road_no LIKE '%$find_what%'  OR a.priority LIKE '%$find_what%'  OR a.property_id LIKE '%$find_what%' OR i.mob_no LIKE '%$find_what%' OR i.mob_no1 LIKE '%$find_what%' OR i.email LIKE '%$find_what%' OR i.alt_phone_no LIKE '%$find_what%' OR i.alt_phone_no1 LIKE '%$find_what%' ) ";

    $sql .= " GROUP BY a.property_id ORDER BY a.modified_date DESC ";  
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            
            $htmlstring .= '<tr><td style="font-size:18px;font-weight:500;">Property</td><td><p style="font-size:18px;"><a href="/#/properties_edit/'.$row['property_id'].'"  target="_blank">'.$row['proptype'].'_'.$row['property_id'].'</a>';
            if ($row['project_name'])
            {
                $htmlstring .= '&nbsp;'.$row['project_name'];
            }
            if ($row['building_name'])
            {
                $htmlstring .= '&nbsp;'.$row['building_name'];
            }
            $htmlstring .= '&nbsp;'.$row['bedrooms'].' BHK '.$row['propsubtype']. ' for '.$row['property_for'].'</p>';
            $htmlstring .= '<p>'.$row['area_name'].' '.$row['locality'].' '.$row['city'].'</p>';
            $htmlstring .= '</td><td>'.$row['created_date'].'</td></tr>';
        }
    }

    $sql = "SELECT *,e.company_name as developer,a.pack_price, CONCAT(a.salu,' ',a.fname,' ',a.lname) as project_contact,  (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(h.team_name SEPARATOR ',') FROM teams as h where FIND_IN_SET(h.team_id , a.teams)) as team , (SELECT count(*) from attachments as t where t.category_id = a.project_id and t.category = 'project') as project_image_count, a.add1,a.add2, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, concat(e.name_title,' ',e.f_name,' ',e.l_name) as developer_name, f.locality as locality, g.area_name as area_name,(SELECT filenames FROM attachments WHERE a.project_id = category_id and category='Project' and isdefault = 'true' LIMIT 1) as filenames from project as a  LEFT JOIN contact as e on a.developer_id = e.contact_id  LEFT JOIN locality as f ON a.locality_id = f.locality_id LEFT JOIN areas as g ON a.area_id = g.area_id LEFT JOIN city as i on g.city_id = i.city_id WHERE a.created_by > 0 ";
    
    $sql .= " and (a.project_name LIKE '%$find_what%' OR g.area_name LIKE '%$find_what%' OR f.locality LIKE '%$find_what%'  OR i.city LIKE '%$find_what%' OR a.project_id LIKE '%$find_what%' OR e.mob_no LIKE '%$find_what%' OR e.mob_no1 LIKE '%$find_what%' OR e.email LIKE '%$find_what%' OR e.alt_phone_no LIKE '%$find_what%' OR e.alt_phone_no1 LIKE '%$find_what%' ) ";

    $sql .= " GROUP BY a.project_id ORDER BY a.created_date DESC ";  
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            
            $htmlstring .= '<tr><td  style="font-size:18px;font-weight:500;">Project</td><td><p style="font-size:18px;"><a href="/#/project_edit/'.$row['project_id'].'"  target="_blank">Project_'.$row['project_id'].'</a>';
            if ($row['project_name'])
            {
                $htmlstring .= '&nbsp;'.$row['project_name'];
            }
            $htmlstring .= '&nbsp;</p>';
            $htmlstring .= '<p>'.$row['area_name'].' '.$row['locality'].' '.$row['city'].'</p>';
            $htmlstring .= '</td><td>'.$row['created_date'].'</td></tr>';
        }
    }

    $sql = "SELECT *, SUBSTRING(b.f_name,1,1) as client_first_char, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name, b.email as client_email, b.mob_no as client_mob, SUBSTRING(c.f_name,1,1) as broker_first_char, CONCAT(c.name_title,' ',c.f_name,' ',c.l_name) as broker_name, c.email as broker_email, c.mob_no as broker_mob,e.locality as preferred_locality,(SELECT GROUP_CONCAT(area_name SEPARATOR ',') FROM areas as u where FIND_IN_SET(u.area_id , a.preferred_area_id)) as preferred_area, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT GROUP_CONCAT(g.team_name SEPARATOR ',') FROM teams as g where FIND_IN_SET(g.team_id , a.teams)) as team, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assigned from enquiry as a LEFT JOIN contact as b on a.client_id = b.contact_id and b.contact_off = 'client' LEFT JOIN contact as c on a.broker_id = c.contact_id and c.contact_off = 'broker' LEFT JOIN locality as e on a.preferred_locality_id = e.locality_id LEFT JOIN areas as f on a.preferred_area_id = f.area_id LEFT JOIN city as i ON i.city_id = f.city_id WHERE a.created_by > 0 ";
    
    $sql .= " and (f.area_name LIKE '%$find_what%' OR e.locality LIKE '%$find_what%'  OR i.city LIKE '%$find_what%' OR a.enquiry_id LIKE '%$find_what%' OR b.mob_no LIKE '%$find_what%' OR b.mob_no1 LIKE '%$find_what%' OR b.email LIKE '%$find_what%' OR b.alt_phone_no LIKE '%$find_what%' OR b.alt_phone_no1 LIKE '%$find_what%' OR c.mob_no LIKE '%$find_what%' OR c.mob_no1 LIKE '%$find_what%' OR c.email LIKE '%$find_what%' OR c.alt_phone_no LIKE '%$find_what%' OR c.alt_phone_no1 LIKE '%$find_what%' ) ";

    $sql .= " GROUP BY a.enquiry_id ORDER BY a.created_date DESC ";  
    //echo $sql;
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $htmlstring .= '<tr><td style="font-size:18px;font-weight:500;">Enquiry</td><td><p style="font-size:18px;"><a href="/#/enquiries_edit/'.$row['enquiry_id'].'"  target="_blank">'.$row['enquiry_off'].'_'.$row['enquiry_id'].'</a> Looking to '.$row['enquiry_for'].' : '.$row['enquiry_type'];
            $htmlstring .= '&nbsp;</p>';
            $htmlstring .= '<p>'.$row['area_name'].' '.$row['locality'].' '.$row['city'].'</p>';
            $htmlstring .= '</td><td>'.$row['created_date'].'</td></tr>';
        }
    }

    $htmlstring .= '    </tbody>
                    </table>';
    $sql = "UPDATE received_data set status = 'Done' " ;  
    $result = $db->updateByQuery($sql);
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/getlivecall', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $senddata =  array();
    $htmldata = array();
    
    $caller_id_number = "";
    
    $ingore_numbers = "7969207777,7969207776";
    $diff_in_seconds = "";

    $sql = "SELECT caller_id_number, TIME_TO_SEC(TIMEDIFF(now(),created_date)) as diff_in_seconds,call_id from received_data WHERE caller_id_number NOT IN ($ingore_numbers) and status !='Done' HAVING diff_in_seconds < 60  ORDER BY created_date DESC LIMIT 1 ";    

    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
           $caller_id_number = $row['caller_id_number'];
           $call_id = $row['call_id'];
           $diff_in_seconds =  $row['diff_in_seconds'];
        }
    }

    $htmldata['caller_id_number']=$caller_id_number;
    $htmldata['call_id']=$call_id;
    $htmldata['diff_in_seconds']=$diff_in_seconds;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/assist_org/:report_of', function($report_of) use ($app) 
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
    $htmlstring = '<div style="overflow-y:auto;height:385px;">
                        <ul style="display:block;margin-left:-41px;">'; 

    if ($report_of=="admin")
    {
        $sql = "SELECT 'Property' as data_type, property_id,building_name,DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assigned,a.modified_date as sort_on FROM property as a UNION SELECT 'Task' as data_type ,b.task_id as property_id,CONCAT(b.task_title) as building_name, DATE_FORMAT(b.created_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = b.created_by) as created_by, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assigned, b.created_date as sort_on FROM task as b WHERE  (FIND_IN_SET ($user_id ,b.assign_to)) ORDER BY sort_on DESC LIMIT 30  ";

        $stmt = $db->getRows($sql);
        if ($stmt->num_rows > 0)    {
            while($row = $stmt->fetch_assoc())
            {            
                $htmlstring .= '<li style="border:1px solid #DACECE;padding:10px;margin-bottom:3px;"><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;"><span>';
                if ($row['data_type']=='Task')
                {
                    $htmlstring .= 'Task added :-';
                    $htmlstring .= '<a href="#/task_edit/'.$row['property_id'].'" target="_blank">TASKID_'.$row['property_id'].'</a>';
                }
                else{
                    $htmlstring .= 'New Property added :-';
                    $htmlstring .= '<a href="#/properties_edit/'.$row['property_id'].'" target="_blank">PROPERTYID_'.$row['property_id'].'</a>';
                }
                $htmlstring .= '</span><span style="float:right;">'.$row['modified_date'].'</span></p><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;font-weight:bold;">'.$row['building_name'].'</p>
                <p>Created By:'.$row['created_by'].'</p><p>Assigned to:'.$row['assigned'].'</p></li>';
            }
        }
        $stmt->close();
    }

    if ($report_of=='user')
    {
        $sql = "SELECT 'PROPERTY' as data_type,a.property_id as id,a.building_name, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS created_date,a.modified_date as sort_on FROM property as a WHERE  (a.created_by != $user_id AND FIND_IN_SET ($user_id,a.assign_to)) 
        
        UNION SELECT 'ENQUIRY' as data_type ,b.enquiry_id as id,CONCAT(b.enquiry_for,' : ', enquiry_type, ' - ', b.enquiry_off) as building_name, DATE_FORMAT(b.created_date,'%d-%m-%Y %H:%i') AS created_date,b.created_date as sort_on FROM enquiry as b WHERE  (b.created_by != $user_id and FIND_IN_SET ($user_id ,b.assigned)) 
        
        UNION SELECT 'TASK' as data_type ,c.task_id as id, c.task_title as building_name, DATE_FORMAT(c.created_date,'%d-%m-%Y %H:%i') AS modified_date, c.created_date as sort_on FROM task as c WHERE  (c.created_by != $user_id AND FIND_IN_SET ($user_id ,c.assign_to)) 
        
        ORDER BY sort_on DESC LIMIT 30";
        $stmt = $db->getRows($sql);
        if ($stmt->num_rows > 0)    {
            while($row = $stmt->fetch_assoc())
            {            
                $htmlstring .= '<li style="border:1px solid #DACECE;padding:10px;margin-bottom:3px;"><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;"><span>';
                if ($row['data_type']=="TASK")
                {
                    $htmlstring .= 'New Task assigned to you:- <a href="#/task_edit/'.$row['id'].'" target="_blank">TASKID_'.$row['id'].'</a>';
                }
                if ($row['data_type']=="PROPERTY")
                {
                    $htmlstring .= 'New property assigned to you:- <a href="#/properties_edit/'.$row['id'].'" target="_blank">PROPERTYID_'.$row['id'].'</a>';
                }
                if ($row['data_type']=="ENQUIRY")
                {
                    $htmlstring .= 'New Enquiry Assigned to you:- <a href="#/enquiries_edit/'.$row['id'].'" target="_blank">ENQUIRYID_'.$row['id'].'</a>';
                }
                $htmlstring .= '</span><span style="float:right;">'.$row['created_date'].'</span></p><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;font-weight:bold;">'.$row['building_name'].'</p></li>';
            }
        }
        $stmt->close();
    }
    
    $htmlstring .='</ul></div>';        
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/assist/:report_of', function($report_of) use ($app) 
{
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];   
    $role = $session['role'];
    $senddata =  array();
    $htmldata = array();
    $htmlstring = '';
    $htmlstring .= '<div class="myTicker"><ul>';
    $today_date = date('Y-m-d');

    if ($report_of=="admin")
    {
        $sql = "SELECT 'Property' as data_type, property_id,building_name,DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assigned,a.modified_date as sort_on, date(a.modified_date) as compare_date FROM property as a 
        
        UNION SELECT 'Task' as data_type ,b.task_id as property_id,CONCAT(b.task_title) as building_name, DATE_FORMAT(b.created_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = b.created_by) as created_by, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assigned, b.created_date as sort_on, date(b.created_date) as compare_date FROM task as b WHERE  (FIND_IN_SET ($user_id ,b.assign_to)) ORDER BY sort_on DESC LIMIT 30  ";
        //echo $sql;

        $stmt = $db->getRows($sql);
        if ($stmt->num_rows > 0)    {
            while($row = $stmt->fetch_assoc())
            {            
                $htmlstring .= '<li style="border:1px solid #DACECE;padding:10px;margin-bottom:3px;" ><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;color:#000000;"><span>';
                if ($row['data_type']=='Task')
                {
                    if ($row['compare_date']==$today_date)
                    {
                        $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                    }
                    $htmlstring .= 'Task added :-';
                    $htmlstring .= '<a href="#/assist_readmore"  style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">TASKID_'.$row['property_id'].'</a>';
                }
                else{
                    if ($row['compare_date']==$today_date)
                    {
                        $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                    }
                    $htmlstring .= 'Property added :-';
                    $htmlstring .= '<a href="#/properties_edit/'.$row['property_id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">PROPERTYID_'.$row['property_id'].'</a>';
                }
                $htmlstring .= '</span><span style="float:right;margin-left:3px;">'.$row['modified_date'].'</span></p><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;font-weight:bold;">'.$row['building_name'].'</p>
                <p>Created By:'.$row['created_by'].'</p><p>Assigned to:'.$row['assigned'].'</p></li>';
            }
        }
        $stmt->close();
    }

    if ($report_of=='user')
    {
        $sql = "SELECT 'PROPERTY' as data_type,a.property_id as id,a.building_name, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS created_date,a.modified_date as sort_on, date(a.modified_date) as compare_date,a.user_comments FROM property as a WHERE  (a.created_by != $user_id AND FIND_IN_SET ($user_id,a.assign_to)) 
        
        UNION SELECT 'ENQUIRY' as data_type ,b.enquiry_id as id,CONCAT(b.enquiry_for,' : ', enquiry_type, ' - ', b.enquiry_off) as building_name, DATE_FORMAT(b.created_date,'%d-%m-%Y %H:%i') AS created_date,b.created_date as sort_on, date(b.created_date) as compare_date,b.user_comments FROM enquiry as b WHERE  (b.created_by != $user_id and FIND_IN_SET ($user_id ,b.assigned)) 
        
        UNION SELECT 'TASK' as data_type ,c.task_id as id, c.task_title as building_name, DATE_FORMAT(c.created_date,'%d-%m-%Y %H:%i') AS modified_date, c.created_date as sort_on , date(c.created_date) as compare_date,c.user_comments FROM task as c WHERE  (c.created_by != $user_id AND FIND_IN_SET ($user_id ,c.assign_to))

        UNION SELECT 'AGREEMENT' as data_type ,d.agreement_id as id, concat(q.project_name,' ',q.building_name) as building_name, DATE_FORMAT(d.created_date,'%d-%m-%Y %H:%i') AS modified_date, d.created_date as sort_on , date(d.created_date) as compare_date, '' as user_comments FROM agreement as d LEFT JOIN property as q ON d.property_id = q.property_id WHERE d.property_id = q.property_id 

        UNION SELECT 'PAYMENTS' as data_type ,e.agreement_id as id, concat('Next Payment Date:',' ',DATE_FORMAT(e.next_pay_date,'%d-%m-%Y')) as building_name, DATE_FORMAT(r.created_date,'%d-%m-%Y %H:%i') AS modified_date, r.created_date as sort_on , date(r.created_date) as compare_date, '' as user_comments FROM payments as e LEFT JOIN agreement as r ON e.agreement_id = r.agreement_id LEFT JOIN property as s ON r.property_id = s.property_id WHERE s.property_id = r.property_id 
        
        ORDER BY sort_on DESC LIMIT 30";
        $stmt = $db->getRows($sql);
        
        if ($stmt->num_rows > 0)    {
            while($row = $stmt->fetch_assoc())
            {     
                $data_type =  $row['data_type'];
                $category = "";
                $category_id = $row['id'];

                if ($data_type=="TASK")
                {
                    $category = "Task";
                }
                if ($data_type=="PROPERTY")
                {
                    $category = "Property";
                }
                if ($data_type=="ENQUIRY")
                {
                    $category = "Enquiry";
                }
                if ($data_type=="AGREEMENT")
                {
                    $category = "Agreement";
                } 
                if ($data_type=="PAYMENTS")
                {
                    $category = "Payment";
                } 
                if ((in_array("Admin", $role) || in_array("Accountant", $role) || in_array("CRM Manager", $role)) && ($data_type=="AGREEMENT" || $data_type=="PAYMENTS"))
                {
                  
                    $htmlstring .= '<li style="border:1px solid #DACECE;padding:10px;margin-bottom:3px;"><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;color:#000000;"><span>';
                    $option = "";
                    if ($row['compare_date']==$today_date)
                    {
                        $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                    }
                    if ($data_type=="AGREEMENT")
                    {
                        $htmlstring .= '<a href="#/agreement_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">AGREEMENTID_'.$row['id'].'</a>';
                    }
                    if ($data_type=="PAYMENTS")
                    {
                        $htmlstring .= '<a href="#/payments_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">PAYMENTSID_'.$row['id'].'</a>';
                    }

                    $option = "a";
                    $htmlstring .= '</span><span style="float:right;margin-left:3px;">'.$row['created_date'].'</span></p><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;font-weight:bold;">'.$row['building_name'].'</p>';
                    $htmlstring .= '</li>';
                }
                else
                {
                    $commentdata = $db->getAllRecords("SELECT a.created_by,a.user_comment,CONCAT(c.salu,' ',c.fname,' ',c.lname) as employee_name FROM commenting as a LEFT JOIN users as b ON b.user_id = a.created_by LEFT JOIN employee as c ON c.emp_id = b.emp_id WHERE a.category_id = $category_id and a.category = '$category' ORDER by a.created_date DESC LIMIT 1 " );
                    $comment_user_id = 0;
                    $user_comment = "";
                    $employee_name = "";
                    if ($commentdata)
                    {
                        $comment_user_id = $commentdata[0]['created_by'];
                        $user_comment = $commentdata[0]['user_comment'];
                        $employee_name = $commentdata[0]['employee_name'];
                    }
                    if ($comment_user_id>0 && $comment_user_id == $user_id)
                    {
                    }
                    else
                    {
                        if ($data_type!="AGREEMENT" && $data_type!='PAYMENTS')
                        {   
                            $htmlstring .= '<li style="border:1px solid #DACECE;padding:10px;margin-bottom:3px;"><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;color:#000000;"><span>';
                            $option = "";
                            if ($row['data_type']=="TASK")
                            {
                                if ($row['compare_date']==$today_date)
                                {
                                    $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                                }
                                $htmlstring .= 'Task assigned to you:- <a href="#/assist_readmore" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">TASKID_'.$row['id'].'</a>';
                                $option = "t";
                            }
                            if ($row['data_type']=="PROPERTY")
                            {
                                if ($row['compare_date']==$today_date)
                                {
                                    $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                                }
                                $htmlstring .= 'property assigned to you:- <a href="#/properties_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">PROPERTYID_'.$row['id'].'</a>';
                                $option = "p";
                            }
                            if ($row['data_type']=="ENQUIRY")
                            {
                                if ($row['compare_date']==$today_date)
                                {
                                    $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                                }
                                $htmlstring .= 'Enquiry Assigned to you:- <a href="#/enquiries_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">ENQUIRYID_'.$row['id'].'</a>';
                                $option = "e";
                            }
                            $htmlstring .= '</span><span style="float:right;margin-left:3px;">'.$row['created_date'].'</span></p><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;font-weight:bold;">'.$row['building_name'].'</p>';

                            if ($comment_user_id>0 && $comment_user_id != $user_id)
                            {
                                $htmlstring .= '<p>'.$employee_name.':-<span style="color:#d96325;">'.$user_comment.'</span></p>';
                                $htmlstring .= '<p><div class="input-group"><input type="text" class="form-control " id="user_comments_'.$option.'_'.$row['id'].'"  name="user_comments_'.$option.'_'.$row['id'].'" placeholder="Type Message" ng-model="user_comments_'.$option.'_'.$row['id'].'"  ng-init="user_comments_'.$option.'_'.$row['id'].'=\''.$row['user_comments'].'\'"/><div class="input-group-addon" ng-click="addusercomments('.$row['id'].',user_comments_'.$option.'_'.$row['id'].',\''.$option.'\')"><i class="fa fa-arrow-circle-o-right" style="font-size:20px;" title="Save Comment" alt="Save Comment"></i></div></div></p>';
                            }
                            if ($comment_user_id == 0)
                            {
                                $htmlstring .= '<p><div class="input-group"><input type="text" class="form-control " id="user_comments_'.$option.'_'.$row['id'].'"  name="user_comments_'.$option.'_'.$row['id'].'" placeholder="Type Message" ng-model="user_comments_'.$option.'_'.$row['id'].'"  ng-init="user_comments_'.$option.'_'.$row['id'].'=\''.$row['user_comments'].'\'"/><div class="input-group-addon" ng-click="addusercomments('.$row['id'].',user_comments_'.$option.'_'.$row['id'].',\''.$option.'\')"><i class="fa fa-arrow-circle-o-right" style="font-size:20px;" title="Save Comment" alt="Save Comment"></i></div></div></p>';
                            }
                            $htmlstring .= '</li>';
                        }
                    }
                }

            }
        }
        $stmt->close();
    }
    
    $htmlstring .='</ul></div><script>$(".myTicker1").easyTicker({direction: "up",height:"320px"});</script>';        
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/assist_readmore/:report_of/:next_page_id', function($report_of,$next_page_id) use ($app) 
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
    $htmlstring .= '<a href="#/user" class="btn btn-default btn-sm" style="margin:5px;float:right;" title="Back.." alt="Back.."><i class="glyphicon glyphicon-arrow-left"></i> &nbsp;&nbsp;&nbsp;Back</a><div class="myTicker"><ul>';
    
    $today_date = date('Y-m-d');

    if ($report_of=="admin")
    {
        $sql = "SELECT 'Property' as data_type, property_id,building_name,DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS modified_date,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assigned,a.modified_date as sort_on, date(a.modified_date) as compare_date FROM property as a 
        
        UNION SELECT 'Task' as data_type ,b.task_id as property_id,CONCAT(b.task_title) as building_name, DATE_FORMAT(b.created_date,'%d-%m-%Y %H:%i') AS modified_date, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = b.created_by) as created_by, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assign_to)) as assigned, b.created_date as sort_on, date(b.created_date) as compare_date FROM task as b WHERE  (FIND_IN_SET ($user_id ,b.assign_to)) ORDER BY sort_on DESC LIMIT $next_page_id,30  ";
        //echo $sql;

        $stmt = $db->getRows($sql);
        if ($stmt->num_rows > 0)    {
            while($row = $stmt->fetch_assoc())
            {            
                $htmlstring .= '<li style="border:1px solid #DACECE;padding:10px;margin-bottom:3px;" ><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;color:#000000;"><span>';
                if ($row['data_type']=='Task')
                {
                    if ($row['compare_date']==$today_date)
                    {
                        $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                    }
                    $htmlstring .= 'Task added :-';
                    $htmlstring .= '<a href="#/task_edit/'.$row['property_id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">TASKID_'.$row['property_id'].'</a>';
                }
                else{
                    if ($row['compare_date']==$today_date)
                    {
                        $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                    }
                    $htmlstring .= 'Property added :-';
                    $htmlstring .= '<a href="#/properties_edit/'.$row['property_id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">PROPERTYID_'.$row['property_id'].'</a>';
                }
                $htmlstring .= '</span><span style="float:right;margin-left:3px;">'.$row['modified_date'].'</span></p><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;font-weight:bold;">'.$row['building_name'].'</p>
                <p>Created By:'.$row['created_by'].'</p><p>Assigned to:'.$row['assigned'].'</p></li>';
            }
        }
        $stmt->close();
    }

    if ($report_of=='user')
    {
        //$sql = "SELECT 'PROPERTY' as data_type,a.property_id as id,a.building_name, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS created_date,a.modified_date as sort_on, date(a.modified_date) as compare_date,a.user_comments,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assigned FROM property as a WHERE  (a.created_by != $user_id AND FIND_IN_SET ($user_id,a.assign_to)) 
        
        //UNION SELECT 'ENQUIRY' as data_type ,b.enquiry_id as id,CONCAT(b.enquiry_for,' : ', enquiry_type, ' - ', b.enquiry_off) as building_name, DATE_FORMAT(b.created_date,'%d-%m-%Y %H:%i') AS created_date,b.created_date as sort_on, date(b.created_date) as compare_date,b.user_comments,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = b.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assigned)) as assigned FROM enquiry as b WHERE  (b.created_by != $user_id and FIND_IN_SET ($user_id ,b.assigned)) 
        
        //UNION SELECT 'TASK' as data_type ,c.task_id as id, c.task_title as building_name, DATE_FORMAT(c.created_date,'%d-%m-%Y %H:%i') AS modified_date, c.created_date as sort_on , date(c.created_date) as compare_date,c.user_comments,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = c.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , c.assign_to)) as assigned FROM task as c WHERE  (c.created_by != $user_id AND FIND_IN_SET ($user_id ,c.assign_to)) 
        
        //ORDER BY sort_on DESC LIMIT $next_page_id,30";


        // QUERY CHANGED AS PER DHAVAL SIR'S INSTRUCTIONS

        $sql = "SELECT 'PROPERTY' as data_type,'' as task_file_name, '' as task_status, a.property_id as id,a.building_name, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS created_date,a.modified_date as sort_on, date(a.modified_date) as compare_date,a.user_comments,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assigned,a.created_by as created_by_id FROM property as a WHERE  (a.created_by = $user_id or FIND_IN_SET ($user_id,a.assign_to)) 
        
        UNION SELECT 'ENQUIRY' as data_type ,'' as task_file_name, '' as task_status, b.enquiry_id as id,CONCAT(b.enquiry_for,' : ', enquiry_type, ' - ', b.enquiry_off) as building_name, DATE_FORMAT(b.created_date,'%d-%m-%Y %H:%i') AS created_date,b.created_date as sort_on, date(b.created_date) as compare_date,b.user_comments,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = b.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assigned)) as assigned,b.created_by as created_by_id FROM enquiry as b WHERE  (b.created_by = $user_id or FIND_IN_SET ($user_id ,b.assigned)) 
        
        UNION SELECT 'TASK' as data_type , (SELECT filenames FROM attachments WHERE c.task_id = category_id and category='task' LIMIT 1) as task_file_name, c.task_status as task_status, c.task_id as id, c.task_title as building_name, DATE_FORMAT(c.created_date,'%d-%m-%Y %H:%i') AS modified_date, c.created_date as sort_on , date(c.created_date) as compare_date,c.user_comments,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = c.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , c.assign_to)) as assigned,c.created_by as created_by_id FROM task as c WHERE  (c.created_by = $user_id or  FIND_IN_SET ($user_id ,c.assign_to)) and c.task_status = 'Active'
        
        UNION SELECT 'ACTIVITY' as data_type ,'' as task_file_name,'' as task_status, d.activity_id as id, d.description as building_name, DATE_FORMAT(d.activity_start,'%d-%m-%Y %H:%i') AS modified_date, d.activity_start as sort_on , date(d.activity_start) as compare_date,d.closure_comment as user_comments,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = d.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , d.assign_to)) as assigned,d.created_by as created_by_id FROM activity as d WHERE  (d.created_by = $user_id or  FIND_IN_SET ($user_id ,d.assign_to)) 
        
        ORDER BY sort_on DESC";// LIMIT $next_page_id,30";
        $sql_query = $sql;
        $stmt = $db->getRows($sql);
        if ($stmt->num_rows > 0)    {
            while($row = $stmt->fetch_assoc())
            {            
                if ($row['created_by']==$row['assigned'])
                {

                }
                else
                {
                    $data_type =  $row['data_type'];
                    $category = "";
                    $category_id = $row['id'];

                    if ($data_type=="TASK")
                    {
                        $category = "Task";
                    }
                    if ($data_type=="PROPERTY")
                    {
                        $category = "Property";
                    }
                    if ($data_type=="ENQUIRY")
                    {
                        $category = "Enquiry";
                    }      
                    if ($data_type=="ACTIVITY")
                    {
                        $category = "Activity";
                    }      

                    $commentdata = $db->getAllRecords("SELECT a.created_by,a.user_comment,CONCAT(c.salu,' ',c.fname,' ',c.lname) as employee_name FROM commenting as a LEFT JOIN users as b ON b.user_id = a.created_by LEFT JOIN employee as c ON c.emp_id = b.emp_id WHERE a.category_id = $category_id and a.category = '$category' ORDER by a.created_date DESC LIMIT 1 " );
                    $comment_user_id = 0;
                    $user_comment = "";
                    $employee_name = "";
                    if ($commentdata)
                    {
                        $comment_user_id = $commentdata[0]['created_by'];
                        $user_comment = $commentdata[0]['user_comment'];
                        $employee_name = $commentdata[0]['employee_name'];
                    }
                    
                    $htmlstring .= '<li style="border:1px solid #DACECE;padding:10px;margin-bottom:3px;"><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;color:#000000;"><span>';
                    $option = "";
                    if ($row['data_type']=="TASK")
                    {
                        if ($row['compare_date']==$today_date)
                        {
                            $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                        }
                        $htmlstring .= 'Task assigned to you:- <a href="#/task_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">TASKID_'.$row['id'].'</a>';
                        $option = "t";
                    }
                    if ($row['data_type']=="PROPERTY")
                    {
                        if ($row['compare_date']==$today_date)
                        {
                            $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                        }
                        $htmlstring .= 'property assigned to you:- <a href="#/properties_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">PROPERTYID_'.$row['id'].'</a>';
                        $option = "p";
                    }
                    if ($row['data_type']=="ENQUIRY")
                    {
                        if ($row['compare_date']==$today_date)
                        {
                            $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                        }
                        $htmlstring .= 'Enquiry Assigned to you:- <a href="#/enquiries_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">ENQUIRYID_'.$row['id'].'</a>';
                        $option = "e";
                    }
                    if ($row['data_type']=="ACTIVITY")
                    {
                        if ($row['compare_date']==$today_date)
                        {
                            $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                        }
                        $htmlstring .= 'Activity Assigned :- <a href="#/activity_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">ACTIVITYID_'.$row['id'].'</a>';
                        $option = "e";
                    }
                    $htmlstring .= '</span><span style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;font-weight:bold;font-size:16px;">&nbsp;&nbsp;&nbsp;'.$row['building_name'].'</span><span style="float:right;margin-left:3px;">'.$row['created_date'].'</span></p><p style="margin-bottom:0px;">Created By:'.$row['created_by'].' &nbsp;&nbsp;&nbsp; Assigned to:'.$row['assigned'].'</p>';

                    if ($comment_user_id>0 && $comment_user_id != $user_id)
                    {
                        $htmlstring .= '<p style="font-weight:bold;margin-bottom:0px;">'.$employee_name.':-'.$user_comment.'</p>';
                    }

                    $htmlstring .= '<div style="margin-bottom:0px;"><a class="btn btn-primary" href="javascript:void(0)" style="margin:10px;margin-right:2px;margin-top:6px;" data-toggle="collapse" data-target="#collapsec'.$option.$row['id'].'" ng-click="showcomments('.$row['id'].',\''.$option.'\')">Show Comments &nbsp;<i class="fa fa-angle-down"></i></a><a class="btn btn-primary" href="javascript:void(0)" style="margin:10px;margin-right:2px;margin-top:6px;" data-toggle="collapse" data-target="#collapseh'.$option.$row['id'].'" ng-click="showhistory('.$row['id'].',\''.$option.'\')">Show History &nbsp;<i class="fa fa-angle-down"></i></a>';

                    if ($row['created_by_id']==$user_id && $row['data_type']=="TASK")
                    {
                        
                        $htmlstring .= '<a class="btn btn-primary" href="javascript:void(0)" style="margin:10px;margin-right:2px;margin-top:6px;" ng-click="deletetask('.$row['id'].',\''.$option.'\')">Delete</a>';
                        
                        $htmlstring .= '<select id="task_status_'.$row['id'].'" name="task_status_'.$row['id'].'"  data-placeholder="Select status"  style="margin-left:5px;" ng-model="task_status_'.$row['id'].'" ng-init="task_status_'.$row['id'].'=\''.$row['task_status'].'\'" ng-change="change_task_status(task_status_'.$row['id'].','.$row['id'].')">
                        <option value="Active">Active</option>
                        <option value="InActive" >InActive</option></select>';                        
                    }
                    $task_file_name = $row['task_file_name'];
                    if ($task_file_name)
                    {
                        $htmlstring .= '<a class="btn btn-primary" href="api/v1/uploads/task/'.$task_file_name.'" target="_blank" style="margin:10px;margin-right:2px;margin-top:6px;">View Attachment</a>';
                    }
                    $htmlstring .= '<div class="input-group" style="width:50%;float:left;margin-top:5px;"><input type="text" class="form-control " id="user_comments_'.$option.'_'.$row['id'].'"  name="user_comments_'.$option.'_'.$row['id'].'" placeholder="Type Message" ng-model="user_comments_'.$option.'_'.$row['id'].'"  ng-init="user_comments_'.$option.'_'.$row['id'].'=\''.$row['user_comments'].'\'"/>
                    <div class="input-group-addon" ng-click="addusercomments('.$row['id'].',user_comments_'.$option.'_'.$row['id'].',\''.$option.'\')"><i class="fa fa-arrow-circle-o-right" style="font-size:20px;" title="Save Comment" alt="Save Comment"></i></div></div>
                    </div></li>
                    <div class="panel-group">
                        <div class="panel panel-default">
                            <div id="collapsec'.$option.$row['id'].'" class="panel-collapse collapse html_content"  compile-html="trustedHtml_c'.$option.$row['id'].'">
                            </div>
                        </div>
                    </div>
                    <div class="panel-group">
                        <div class="panel panel-default">
                            <div id="collapseh'.$option.$row['id'].'" class="panel-collapse collapse html_content"  compile-html="trustedHtml_h'.$option.$row['id'].'"></div>
                        </div>
                    </div>';
                }

            }
        }
        $stmt->close();
    }
    
    $htmlstring .='</ul></div><script>$(".myTicker1").easyTicker({direction: "up",height:"320px"});</script>';        
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql_query']=$sql_query;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/deletetask/:id/:option', function($id,$option) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "DELETE FROM task WHERE task_id = $id " ;
    $result = $db->updateByQuery($sql);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Task Deleted successfully";
        echoResponse(200, $response);
    }
});
$app->get('/showcomments/:id/:option', function($id,$option) use ($app) 
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
                                    <th style="width:30%">Name</th>
                                    <th style="width:15%">Time</th>
                                    <th style="width:55%">Comments</th>
                                </tr>
                            </thead> 
                            <tbody>';
    $today_date = date('Y-m-d');
    $sql = "";
    if ($option=='p')
    {
        $sql = "SELECT *,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date FROM commenting as a WHERE a.category = 'Property' and a.category_id = $id ";
    }
    if ($option=='e')
    {
        $sql = "SELECT *,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date  FROM commenting as a WHERE a.category = 'Enquiry' and a.category_id = $id ";
    }
    if ($option=='a')
    {
        $sql = "SELECT *,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date  FROM commenting as a WHERE a.category = 'Activity' and a.category_id = $id ";
    }
    if ($option=='t')
    {
        $sql = "SELECT *,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date  FROM commenting as a WHERE a.category = 'Task' and a.category_id = $id ";
    }
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)    
    {
        while($row = $stmt->fetch_assoc())
        {
            if ($option=='p')
            {
                $htmlstring .= '<tr><td>'.$row['created_by'].'</td><td>'.$row['created_date'].'</td><td>'.$row['user_comment'].'</td></tr>';
            }
            if ($option=='e')
            {
                $htmlstring .= '<tr><td>'.$row['created_by'].'</td><td>'.$row['created_date'].'</td><td>'.$row['user_comment'].'</td></tr>';
            }
            if ($option=='a')
            {
                $htmlstring .= '<tr><td>'.$row['created_by'].'</td><td>'.$row['created_date'].'</td><td>'.$row['user_comment'].'</td></tr>';
            }
            if ($option=='t')
            {
                $htmlstring .= '<tr><td>'.$row['created_by'].'</td><td>'.$row['created_date'].'</td><td>'.$row['user_comment'].'</td></tr>';
            }
        }
        
    }
    $stmt->close();
    $htmlstring .='</table></div>';        
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/checkfornewcomment', function() use ($app) 
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
    $sql = "SELECT 'PROPERTY' as data_type,a.property_id as id,a.building_name, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS created_date,a.modified_date as sort_on, date(a.modified_date) as compare_date,a.user_comments FROM property as a WHERE  (a.created_by != $user_id AND FIND_IN_SET ($user_id,a.assign_to)) 
    
    UNION SELECT 'ENQUIRY' as data_type ,b.enquiry_id as id,CONCAT(b.enquiry_for,' : ', enquiry_type, ' - ', b.enquiry_off) as building_name, DATE_FORMAT(b.created_date,'%d-%m-%Y %H:%i') AS created_date,b.created_date as sort_on, date(b.created_date) as compare_date,b.user_comments FROM enquiry as b WHERE  (b.created_by != $user_id and FIND_IN_SET ($user_id ,b.assigned)) 
    
    UNION SELECT 'TASK' as data_type ,c.task_id as id, c.task_title as building_name, DATE_FORMAT(c.created_date,'%d-%m-%Y %H:%i') AS modified_date, c.created_date as sort_on , date(c.created_date) as compare_date,c.user_comments FROM task as c WHERE  (c.created_by != $user_id AND FIND_IN_SET ($user_id ,c.assign_to)) 
    
    ORDER BY sort_on DESC LIMIT 30";
    $stmt = $db->getRows($sql);
    $count = 0;
    if ($stmt->num_rows > 0)    {
        while($row = $stmt->fetch_assoc())
        {     
            $data_type =  $row['data_type'];
            $category = "";
            $category_id = $row['id'];

            if ($data_type=="TASK")
            {
                $category = "Task";
            }
            if ($data_type=="PROPERTY")
            {
                $category = "Property";
            }
            if ($data_type=="ENQUIRY")
            {
                $category = "Enquiry";
            }      
            $commentdata = $db->getAllRecords("SELECT a.created_by,a.user_comment,CONCAT(c.salu,' ',c.fname,' ',c.lname) as employee_name FROM commenting as a LEFT JOIN users as b ON b.user_id = a.created_by LEFT JOIN employee as c ON c.emp_id = b.emp_id WHERE a.category_id = $category_id and a.category = '$category' ORDER by a.created_date DESC LIMIT 1 " );
            $comment_user_id = 0;
            $user_comment = "";
            $employee_name = "";
            if ($commentdata)
            {
                $comment_user_id = $commentdata[0]['created_by'];
                $user_comment = $commentdata[0]['user_comment'];
                $employee_name = $commentdata[0]['employee_name'];
            }
            if ($comment_user_id>0 && $comment_user_id != $user_id)
            {
                $count++;
            }
        }
    }
    $stmt->close();
           
    $htmldata['count']=$count;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});



$app->get('/showhistory/:id/:option', function($id,$option) use ($app) 
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
                                    <th style="width:12%">ID</th>
                                    <th style="width:15%">Date</th>
                                    <th style="width:15%">Type</th>
                                    <th style="width:38%">Description</th>
                                    <th style="width:20%">Assign to</th>
                                </tr>
                            </thead> 
                            <tbody>';
    $today_date = date('Y-m-d');

    $sql = "";
    if ($option=='p' || $option=='e')
    {
        if ($option=='p')
        {
            $sql = "SELECT *,DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start,DATE_FORMAT(a.activity_end,'%d-%m-%Y %H:%i') AS activity_end, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM activity as a LEFT JOIN contact as b ON a.client_id = b.contact_id  where a.property_id  = $id ORDER BY a.activity_end ";
        }
        if ($option=='e')
        {
            $sql = "SELECT *,DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start,DATE_FORMAT(a.activity_end,'%d-%m-%Y %H:%i') AS activity_end, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date ,CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM activity as a LEFT JOIN contact as b ON a.client_id = b.contact_id  where a.enquiry_id  = $id ORDER BY a.activity_end ";
        }
        

        $stmt = $db->getRows($sql);
        if ($stmt->num_rows > 0)    
        {
            while($row = $stmt->fetch_assoc())
            {
                if ($option=='p')
                {
                    $htmlstring .= '<tr><td><a href="#/activity_edit/'.$row['activity_id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">ACTIVITYID_'.$row['activity_id'].'</a></td><td>'.$row['created_date'].'</td><td>'.$row['activity_type'].'</td><td><p><strong>Client:</strong>'.$row['client_name'].'</p><p><strong>Description:</strong>'.$row['description'].'</p><p><strong>Closure Comment:</strong>'.$row['closure_comment'].'</p></td><td>'.$row['assign_to'].'</td></tr>';
                }
                if ($option=='e')
                {
                    $htmlstring .= '<tr><td><a href="#/activity_edit/'.$row['activity_id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">ACTIVITYID_'.$row['activity_id'].'</a></td><td>'.$row['created_date'].'</td><td>'.$row['activity_type'].'</td><td><p><strong>Client:</strong>'.$row['client_name'].'</p><p><strong>Description:</strong>'.$row['description'].'</p><p><strong>Closure Comment:</strong>'.$row['closure_comment'].'</p></td><td>'.$row['assign_to'].'</td></tr>';
                }
            }
            
        }
        $stmt->close();
    }

    if ($option=='t')
    {
        $sql = "SELECT *, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to  FROM property as a LEFT JOIN contact as b ON a.dev_owner_id = b.contact_id WHERE a.task_id = $id ORDER BY a.created_date DESC";
        $stmt = $db->getRows($sql);
        if ($stmt->num_rows > 0)    
        {
            while($row = $stmt->fetch_assoc())
            {
                $description="";
                $closure_comment="";
                $htmlstring .= '<tr><td><a href="#/property_edit/'.$row['property_id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">PROPERTYID_'.$row['property_id'].'</a></td><td>'.$row['created_date'].'</td><td>Property</td><td><p><strong>Client:</strong>'.$row['client_name'].'</p><p><strong>Description:</strong>'.$description.'</p><p><strong>Closure Comment:</strong>'.$closure_comment.'</p></td><td>'.$row['assign_to'].'</td></tr>';
            }
        }
        $stmt->close();

        $sql = "SELECT *, DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date, CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assigned)) as assign_to  FROM enquiry as a LEFT JOIN contact as b ON a.client_id = b.contact_id WHERE a.task_id = $id ORDER BY a.created_date DESC";
        $stmt = $db->getRows($sql);
        if ($stmt->num_rows > 0)    
        {
            while($row = $stmt->fetch_assoc())
            {
                $description="";
                $closure_comment="";
                $htmlstring .= '<tr><td><a href="#/enquiry_edit/'.$row['enquiry_id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">ENQUIRYID_'.$row['enquiry_id'].'</a></td><td>'.$row['created_date'].'</td><td>Enquiry</td><td><p><strong>Client:</strong>'.$row['client_name'].'</p><p><strong>Description:</strong>'.$description.'</p><p><strong>Closure Comment:</strong>'.$closure_comment.'</p></td><td>'.$row['assign_to'].'</td></tr>';
            }
        }
        $stmt->close();
    }
    $htmlstring .='</table></div>';        
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/show_history_referrals/:referrals_id', function($referrals_id) use ($app) 
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

    $sql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on,DATE_FORMAT(a.next_date,'%d-%m-%Y %H:%i') AS next_date FROM referrals_comment as a where a.referrals_id  = $referrals_id ORDER BY a.created_date ";
    
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

$app->get('/show_tasks', function() use ($app) 
{
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];    
    $today_date = date('Y-m-d');
    $senddata =  array();
    $htmldata = array();
    $htmlstring = '<div style="overflow-y:auto;height:385px;">
                        <ul style="display:block;margin-left:-41px;">'; 

    
    /*$sql = "SELECT *,DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start,CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM activity as a LEFT JOIN contact as b ON a.client_id = b.contact_id  where FIND_IN_SET ($user_id ,a.assign_to) and (a.activity_type='Meeting') and (a.status = 'Open' ) ORDER BY a.activity_start DESC LIMIT 30  ";*/

    // MODIFIED FOR ALL OPEN TASKS

    $sql = "SELECT *,DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start,CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM activity as a LEFT JOIN contact as b ON a.client_id = b.contact_id  WHERE FIND_IN_SET ($user_id ,a.assign_to) and (a.status = 'Open' ) and DATE(a.activity_start)='$today_date' ORDER BY a.activity_start DESC LIMIT 30  ";


    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)    {
        while($row = $stmt->fetch_assoc())
        {            
            $htmlstring .= '<li style="border:1px solid #DACECE;padding:10px;margin-bottom:3px;height:100px;">
            <p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;"><span>';
            //$htmlstring .= 'Meeting :-';
            $htmlstring .= '<a href="#/activity_edit/'.$row['activity_id'].'" target="_blank" style="color:#d2aa4b;">ACTIVITYID_'.$row['activity_id'].'</a></span><span style="float:right;">'.$row['activity_start'].'</span></p>';
            $htmlstring .= '<p>Assigned to:'.$row['assign_to'].'</p>';
            $htmlstring .= '<p><span style="width:60%;float:left;">';
            if ($row['client_name'])
            {
                $htmlstring .= 'Client: '.$row['client_name'];
            }
            $htmlstring .= '</span><span style="width:40%;float:right;">'.$row['status'].'<select id="status_'.$row['activity_id'].'" name="status_'.$row['activity_id'].'"  data-placeholder="Select status"  style="margin-left:5px;float:right;" ng-model="status_'.$row['activity_id'].'" ng-init="status_'.$row['activity_id'].'=\''.$row['status'].'\'" ng-change="change_activity_status(status_'.$row['activity_id'].','.$row['activity_id'].')">
            <option value="Open">Open</option>
            <option value="Completed" >Completed</option>
            <option value="Cancelled" >Cancelled</option></select></span></p></li>';

            /**/ 

        }
    }
    $stmt->close();
    
    $htmlstring .='</ul></div>';        
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->post('/search_assist', function() use ($app) {
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
    $role = $session['role'];

    $today_date = date('Y-m-d');
    $next_page_id = $r->searchdata->next_page_id;
    
    $senddata =  array();
    $htmldata = array();
    $tproperty = "";
    $tenquiry = "";
    $ttask = "";
    $tactivity = "";

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
                    $tproperty .= " AND (";
                    $tenquiry .= " AND (";
                    $ttask .= " AND (";
                    $tactivity .= " AND (";
                    $first = "No";
                }
                else{
                    $tproperty .= " OR ";
                    $tenquiry .= " OR ";
                    $ttask .= " OR ";
                    $tactivity .= " OR ";

                }
                 
                $tproperty .= " a.assign_to LIKE '%".$value."%' ";
                $tenquiry .= " b.assigned LIKE '%".$value."%' ";
                $ttask .= " c.assign_to LIKE '%".$value."%' ";
                $tactivity .= " d.assign_to LIKE '%".$value."%' ";
            }

            if ($first=='No')
            {
                $tproperty .= ") ";
                $tenquiry .= ") ";
                $ttask .= ") ";
                $tactivity .= ") ";
            
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
                    $tproperty .= " AND (";
                    $tenquiry .= " AND (";
                    $ttask .= " AND (";
                    $tactivity .= " AND (";
                    $first = "No";
                }
                else{
                    $tproperty .= " OR ";
                    $tenquiry .= " OR ";
                    $ttask .= " OR ";
                    $tactivity .= " OR ";
                }                  
                $tproperty .= " a.client_id LIKE '".$value."' ";
                $tenquiry .= " b.client_id LIKE '".$value."' ";
                $ttask .= " c.client_id LIKE '".$value."' ";
                $tactivity .= " d.client_id LIKE '".$value."' ";

            }
            if ($first=='No')
            {
                $tproperty .= ") ";
                $tenquiry .= ") ";
                $ttask .= ") ";
                $tactivity .= ") ";
                
            }
        }
    }
    
    $gquery = "";
    if (isset($r->searchdata->created_date_from))
    {
        $created_date_from = $r->searchdata->created_date_from;
        $tcreated_date_from = substr($created_date_from,6,4)."-".substr($created_date_from,3,2)."-".substr($created_date_from,0,2);
        if (isset($r->searchdata->created_date_to))
        {
            $created_date_to = $r->searchdata->created_date_to;
            $tcreated_date_to = substr($created_date_to,6,4)."-".substr($created_date_to,3,2)."-".substr($created_date_to,0,2);
        }
        $tcreated_date_from = $tcreated_date_from." 00:00:00";
        $tcreated_date_to = $tcreated_date_to." 23:59:59";
        $tproperty .= " and a.created_date BETWEEN '".$tcreated_date_from."' AND '".$tcreated_date_to."' ";
        $tenquiry .= " and b.created_date BETWEEN '".$tcreated_date_from."' AND '".$tcreated_date_to."' ";
        $ttask .= " and c.created_date BETWEEN '".$tcreated_date_from."' AND '".$tcreated_date_to."' ";
        $tactivity .= " and d.created_date BETWEEN '".$tcreated_date_from."' AND '".$tcreated_date_to."' ";
    }


    if (isset($r->searchdata->property_id))
    {
        $new_data = $r->searchdata->property_id;
        if ($new_data)
        {
            $tproperty .= " and a.property_id = LIKE '".$r->searchdata->property_id."' ";
        }
    }

    if (isset($r->searchdata->enquiry_id))
    {
        $new_data = $r->searchdata->enquiry_id;
        if ($new_data)
        {
            $tenquiry .= " and b.enquiry_id = LIKE '".$r->searchdata->enquiry_id."' ";
        }
    }

    if (isset($r->searchdata->activity_id))
    {
        $new_data = $r->searchdata->activity_id;
        if ($new_data)
        {
            $tactivity .= " and d.activity_id = LIKE '".$r->searchdata->activity_id."' ";
        }
    }


    $htmlstring = '';
    $htmlstring .= '<a href="#/user" class="btn btn-default btn-sm" style="margin:5px;float:right;" title="Back.." alt="Back.."><i class="glyphicon glyphicon-arrow-left"></i> &nbsp;&nbsp;&nbsp;Back</a><div class="myTicker"><ul>';
    
    $today_date = date('Y-m-d');
    
    $sql = "SELECT 'PROPERTY' as data_type,a.property_id as id,a.building_name, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS created_date,a.modified_date as sort_on, date(a.modified_date) as compare_date,a.user_comments,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assigned,a.created_by as created_by_id FROM property as a WHERE  (a.created_by = $user_id or FIND_IN_SET ($user_id,a.assign_to)) ".$tproperty." ";
    
    $sql .= " UNION SELECT 'ENQUIRY' as data_type ,b.enquiry_id as id,CONCAT(b.enquiry_for,' : ', enquiry_type, ' - ', b.enquiry_off) as building_name, DATE_FORMAT(b.created_date,'%d-%m-%Y %H:%i') AS created_date,b.created_date as sort_on, date(b.created_date) as compare_date,b.user_comments,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = b.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , b.assigned)) as assigned,b.created_by as created_by_id FROM enquiry as b WHERE  (b.created_by = $user_id or FIND_IN_SET ($user_id ,b.assigned)) ".$tenquiry." ";

    
    $sql .= "UNION SELECT 'TASK' as data_type ,c.task_id as id, c.task_title as building_name, DATE_FORMAT(c.created_date,'%d-%m-%Y %H:%i') AS modified_date, c.created_date as sort_on , date(c.created_date) as compare_date,c.user_comments,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = c.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , c.assign_to)) as assigned,c.created_by as created_by_id FROM task as c WHERE  (c.created_by = $user_id or  FIND_IN_SET ($user_id ,c.assign_to)) ".$ttask." ";
    
    $sql .= " UNION SELECT 'ACTIVITY' as data_type ,d.activity_id as id, d.description as building_name, DATE_FORMAT(d.activity_start,'%d-%m-%Y %H:%i') AS modified_date, d.activity_start as sort_on , date(d.activity_start) as compare_date,d.closure_comment as user_comments,(SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = d.created_by) as created_by,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , d.assign_to)) as assigned,d.created_by as created_by_id FROM activity as d WHERE  (d.created_by = $user_id or  FIND_IN_SET ($user_id ,d.assign_to)) ".$tactivity." ";
    
    $sql .= "  ORDER BY sort_on DESC LIMIT $next_page_id,30";
    

    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)    
    {
        while($row = $stmt->fetch_assoc())
        {            
            if ($row['created_by']==$row['assigned'])
            {

            }
            else
            {
                $data_type =  $row['data_type'];
                $category = "";
                $category_id = $row['id'];

                if ($data_type=="TASK")
                {
                    $category = "Task";
                }
                if ($data_type=="PROPERTY")
                {
                    $category = "Property";
                }
                if ($data_type=="ENQUIRY")
                {
                    $category = "Enquiry";
                }      
                if ($data_type=="ACTIVITY")
                {
                    $category = "Activity";
                }      

                $commentdata = $db->getAllRecords("SELECT a.created_by,a.user_comment,CONCAT(c.salu,' ',c.fname,' ',c.lname) as employee_name FROM commenting as a LEFT JOIN users as b ON b.user_id = a.created_by LEFT JOIN employee as c ON c.emp_id = b.emp_id WHERE a.category_id = $category_id and a.category = '$category' ORDER by a.created_date DESC LIMIT 1 " );
                $comment_user_id = 0;
                $user_comment = "";
                $employee_name = "";
                if ($commentdata)
                {
                    $comment_user_id = $commentdata[0]['created_by'];
                    $user_comment = $commentdata[0]['user_comment'];
                    $employee_name = $commentdata[0]['employee_name'];
                }
                
                $htmlstring .= '<li style="border:1px solid #DACECE;padding:10px;margin-bottom:3px;"><p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;color:#000000;"><span>';
                $option = "";
                if ($row['data_type']=="TASK")
                {
                    if ($row['compare_date']==$today_date)
                    {
                        $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                    }
                    $htmlstring .= 'Task assigned to you:- <a href="#/task_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">TASKID_'.$row['id'].'</a>';
                    $option = "t";
                }
                if ($row['data_type']=="PROPERTY")
                {
                    if ($row['compare_date']==$today_date)
                    {
                        $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                    }
                    $htmlstring .= 'property assigned to you:- <a href="#/properties_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">PROPERTYID_'.$row['id'].'</a>';
                    $option = "p";
                }
                if ($row['data_type']=="ENQUIRY")
                {
                    if ($row['compare_date']==$today_date)
                    {
                        $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                    }
                    $htmlstring .= 'Enquiry Assigned to you:- <a href="#/enquiries_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">ENQUIRYID_'.$row['id'].'</a>';
                    $option = "e";
                }
                if ($row['data_type']=="ACTIVITY")
                {
                    if ($row['compare_date']==$today_date)
                    {
                        $htmlstring .= '<span style="background-color:#bb0e0e;width:20px;height:20px;border:1px solid #bb0e0e;border-radius:20px;color:#ffffff;margin-right:5px;padding:5px;">New</span>';
                    }
                    $htmlstring .= 'Activity Assigned :- <a href="#/activity_edit/'.$row['id'].'" target="_blank" style="color:#d2aa4b;text-decoration: underline;font-weight:bold;">ACTIVITYID_'.$row['id'].'</a>';
                    $option = "e";
                }
                $htmlstring .= '</span><span style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;font-weight:bold;font-size:16px;">&nbsp;&nbsp;&nbsp;'.$row['building_name'].'</span><span style="float:right;margin-left:3px;">'.$row['created_date'].'</span></p><p style="margin-bottom:0px;">Created By:'.$row['created_by'].' &nbsp;&nbsp;&nbsp; Assigned to:'.$row['assigned'].'</p>';

                if ($comment_user_id>0 && $comment_user_id != $user_id)
                {
                    $htmlstring .= '<p style="font-weight:bold;margin-bottom:0px;">'.$employee_name.':-'.$user_comment.'</p>';
                }

                $htmlstring .= '<div style="margin-bottom:0px;"><a class="btn btn-primary" href="javascript:void(0)" style="margin:10px;margin-right:2px;margin-top:6px;" data-toggle="collapse" data-target="#collapsec'.$option.$row['id'].'" ng-click="showcomments('.$row['id'].',\''.$option.'\')">Show Comments &nbsp;<i class="fa fa-angle-down"></i></a><a class="btn btn-primary" href="javascript:void(0)" style="margin:10px;margin-right:2px;margin-top:6px;" data-toggle="collapse" data-target="#collapseh'.$option.$row['id'].'" ng-click="showhistory('.$row['id'].',\''.$option.'\')">Show History &nbsp;<i class="fa fa-angle-down"></i></a>';

                if ($row['created_by_id']==$user_id && $row['data_type']=="TASK")
                {
                    $htmlstring .= '<a class="btn btn-primary" href="javascript:void(0)" style="margin:10px;margin-right:2px;margin-top:6px;" ng-click="deletetask('.$row['id'].',\''.$option.'\')">Delete</a>';
                }
                
                $htmlstring .= '<div class="input-group" style="width:50%;float:left;margin-top:5px;"><input type="text" class="form-control " id="user_comments_'.$option.'_'.$row['id'].'"  name="user_comments_'.$option.'_'.$row['id'].'" placeholder="Type Message" ng-model="user_comments_'.$option.'_'.$row['id'].'"  ng-init="user_comments_'.$option.'_'.$row['id'].'=\''.$row['user_comments'].'\'"/>
                <div class="input-group-addon" ng-click="addusercomments('.$row['id'].',user_comments_'.$option.'_'.$row['id'].',\''.$option.'\')"><i class="fa fa-arrow-circle-o-right" style="font-size:20px;" title="Save Comment" alt="Save Comment"></i></div></div>
                </div></li>
                <div class="panel-group">
                    <div class="panel panel-default">
                        <div id="collapsec'.$option.$row['id'].'" class="panel-collapse collapse html_content"  compile-html="trustedHtml_c'.$option.$row['id'].'">
                        </div>
                    </div>
                </div>
                <div class="panel-group">
                    <div class="panel panel-default">
                        <div id="collapseh'.$option.$row['id'].'" class="panel-collapse collapse html_content"  compile-html="trustedHtml_h'.$option.$row['id'].'"></div>
                    </div>
                </div>';
            }

        }
    }
    $stmt->close();
    
    $htmlstring .='</ul></div>';        
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['sql_query']=$sql;
    $senddata[]=$htmldata;
    echo json_encode($senddata);

    /*$sql = " ";
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
    
    $searchsql = $searchsql .  $sql .  " GROUP BY a.activity_id ORDER BY a.modified_date DESC LIMIT $next_page_id, 30";// CAST(a.property_id as UNSIGNED) DESC";

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
    echo json_encode($listactivities);*/



});



$app->get('/change_activity_status/:status/:activity_id', function($status, $activity_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE activity set status = '$status' where activity_id = $activity_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
});


$app->get('/change_task_status/:task_status/:task_id', function($task_status, $task_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $sql = "UPDATE task set task_status = '$task_status' where task_id = $task_id " ;  
    $result = $db->updateByQuery($sql);
    $response["status"] = "success";
    $response["message"] = "Task Status Updated";
    echoResponse(200, $response);
});


$app->get('/show_tasks_new', function() use ($app) 
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
    $htmlstring = '<div style="overflow-y:auto;height:385px;">
                        <ul style="display:block;margin-left:-41px;">'; 

    
    $sql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_date,CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to FROM task as a LEFT JOIN contact as b ON a.client_id = b.contact_id  where FIND_IN_SET ($user_id ,a.assign_to)  ORDER BY a.created_date DESC LIMIT 30  ";

    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)    {
        while($row = $stmt->fetch_assoc())
        {            
            $htmlstring .= '<li style="border:1px solid #DACECE;padding:10px;margin-bottom:3px;height:100px;">
            <p style="margin-bottom:0px;white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;"><span>';
            $htmlstring .= 'Task :-';
            $htmlstring .= $row['task_title'].'</span></p>';
            $htmlstring .= '<p>'.$row['description'].'</p>';
            $htmlstring .= '<p>Assigned to:'.$row['assign_to'].'</p>';
            if ($row['client_name'])
            {
                $htmlstring .= '<p>Client: '.$row['client_name'].'</p>';
            }
            
            $htmlstring .= '</li>';
        }
    }
    $stmt->close();
    
    $htmlstring .='</ul></div>';        
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});




$app->get('/todays_alerts', function() use ($app) 
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
    //and (DATE(a.created_date) = curdate() OR DATE(a.modified_date)=curdate())
    
    $sql = "SELECT 'PROPERTY' as data_type,a.property_id as id,a.building_name, DATE_FORMAT(a.modified_date,'%d-%m-%Y %H:%i') AS created_date,a.modified_date as sort_on FROM property as a WHERE  (FIND_IN_SET ($user_id,a.assign_to)) 
    
    UNION SELECT 'ENQUIRY' as data_type ,b.enquiry_id as id,CONCAT(b.enquiry_for,' : ', enquiry_type, ' - ', b.enquiry_off) as building_name, DATE_FORMAT(b.created_date,'%d-%m-%Y %H:%i') AS created_date,b.created_date as sort_on FROM enquiry as b WHERE  ( FIND_IN_SET ($user_id ,b.assigned))  
    
    UNION SELECT 'TASK' as data_type ,c.task_id as id, c.task_title as building_name, DATE_FORMAT(c.created_date,'%d-%m-%Y %H:%i') AS modified_date, c.created_date as sort_on FROM task as c WHERE  (FIND_IN_SET ($user_id ,c.assign_to))
    
    ORDER BY sort_on DESC LIMIT 30";
    $stmt = $db->getRows($sql);

    $show = 0;
    $htmlstring = '<div class="marquee-sibling1">';
    $htmlstring .= 'Today\'s Alerts';
    $htmlstring .= '</div>';
    
    $htmlstring .= '<div class="container">';
    $htmlstring .= '<div class="marquee" marquee=""><ul class="marquee-content-items">';
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        { 
            if ($row['data_type']=="TASK")
            {
                $htmlstring .= '<li><a href="#/task_edit/'.$row['id'].'" target="_blank" style="color:#ffffff;">TASKID_'.$row['id'].'--'.$row['building_name'].'</a></li>';
            }
            if ($row['data_type']=="PROPERTY")
            {
                $htmlstring .= '<li><a href="#/properties_edit/'.$row['id'].'" target="_blank" style="color:#ffffff;">PROPERTYID_'.$row['id'].'--'.$row['building_name'].'</a></li>';
            }
            if ($row['data_type']=="ENQUIRY")
            {
                $htmlstring .= '<li><a href="#/enquiries_edit/'.$row['id'].'" target="_blank" style="color:#ffffff;">ENQUIRYID_'.$row['id'].'--'.$row['building_name'].'</a></li>';
            }
            $show = 1;
        }
    }
    $htmlstring .='</ul></div></div>';
    $stmt->close();
    $htmldata['htmlstring']=$htmlstring;
    $htmldata['show']=$show;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/reminders', function() use ($app) 
{
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];    
    $today_date = date("Y-m-d");
    $sql = "SELECT *,DATE_FORMAT(a.activity_start,'%d-%m-%Y %H:%i') AS activity_start,DATE_FORMAT(a.activity_end,'%d-%m-%Y %H:%i') AS activity_end,CONCAT(b.name_title,' ',b.f_name,' ',b.l_name) as client_name ,(SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to,a.description FROM activity as a LEFT JOIN contact as b ON a.client_id = b.contact_id  where FIND_IN_SET ($user_id ,a.assign_to) and (a.status = 'Open' ) and (DATE(a.activity_start) != '$today_date') ORDER BY a.activity_end DESC LIMIT 30  ";
    $reminder_list = $db->getAllRecords($sql);
    echo json_encode($reminder_list);
});



// ATTENDANCE



$app->get('/attendance_ctrl_org/:start_date/:end_date/:user_id', function($start_date,$end_date,$user_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $session_login_details_id = $_SESSION['session_login_details_id'];

    $start_date = $start_date.'00:00:00';
    $end_date = $end_date.'23:59:59';
    $sql = "SELECT a.emp_id,b.user_id,CONCAT(a.salu,' ',a.fname,' ',a.lname) as emp_name FROM employee as a LEFT JOIN users as b ON a.emp_id = b.emp_id WHERE a.fname != ' ' and b.user_id = $user_id ORDER BY a.fname,a.lname "; 

    $htmldata = array();
    $senddata =  array();
    $htmlstring = '<table class="table table-bordered table-striped" >
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Date</th>
                                <th>Logged in</th>
                                <th>Logged out</th>
                                <th>IP Address</th>
                                <th></th>
                            </tr>
                        </thead> 
                        <tbody>';
                              

    $stmt = $db->getRows($sql);
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        { 
            $emp_id = $row['emp_id'];
            $emp_name = $row['emp_name'];
            $user_id = $row['user_id'];

            $innersql = "SELECT date(log_date) as log_date, DATE_FORMAT(log_date,'%d-%m-%Y') as print_log_date FROM login_details WHERE emp_id = $user_id and log_date >='$start_date' and log_date<= '$end_date' GROUP BY date(log_date) ORDER BY log_date DESC";
            
            $innerstmt = $db->getRows($innersql);
            if($innerstmt->num_rows > 0)
            {
                while($innerrow = $innerstmt->fetch_assoc())
                {
                    $print_log_date = $innerrow['print_log_date'];
                    $log_date = $innerrow['log_date'];
                    $ip_address = "";
                    $login_details_id = 0;
                    $innerinnersql = "SELECT log_date,DATE_FORMAT(log_date,'%d-%m-%Y %H:%i') as print_log_date,ip_address,status,login_details_id FROM login_details WHERE emp_id = $user_id and date(log_date) ='$log_date' ORDER BY log_date";
                    
                    $innerinnerstmt = $db->getRows($innerinnersql);
                    $logged_in_datetime = "";
                    $logged_out_datetime = "";
                    if ($innerinnerstmt->num_rows > 0)
                    {
                        while($innerinnerrow = $innerinnerstmt->fetch_assoc())
                        {   
                            if ($logged_in_datetime!="")
                            {

                            }
                            else if ($innerinnerrow['log_date'])
                            {
                                if ($innerinnerrow['status'] == 'logged_in')
                                {
                                    $logged_in_datetime = $innerinnerrow['print_log_date'];
                                    $ip_address = $innerinnerrow['ip_address'];
                                    $login_details_id = $innerinnerrow['login_details_id'];
                                }
                            }
                            if ($logged_out_datetime!="")
                            {

                            }
                            else if ($innerinnerrow['log_date'])
                            {
                                if ($innerinnerrow['status'] == 'logged_out')
                                {
                                    $logged_out_datetime = $innerinnerrow['print_log_date'];
                                    //$ip_address = $innerinnerrow['$ip_address'];
                                }
                            }
                        }
                    }
                    $htmlstring .= '<tr>
                                <td>'.$emp_name.'</a></td>
                                <td>'.$print_log_date.'</td>
                                <td>'.$logged_in_datetime.'</td>
                                <td>'.$logged_out_datetime.'</td>
                                <th>'.$ip_address.'</th>
                                <th><a href="javascript:void(0)" class="btn btn-default btn-sm" ng-click="show_operting('.$log_date.')" title="View Screen Activity.." alt="View Screen Activity.."><i class="glyphicon glyphicon-pencil"></i></a></th>
                            </tr>';

                }
                
                
            }
            
        }
    }
    $htmlstring .'</table>';  
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
    
});


$app->get('/attendance_ctrl/:start_date/:end_date/:user_id', function($start_date,$end_date,$user_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $session_login_details_id = $_SESSION['session_login_details_id'];

    $start_date = $start_date.'00:00:00';
    $end_date = $end_date.'23:59:59';
    $sql = "SELECT a.emp_id,b.user_id,CONCAT(a.salu,' ',a.fname,' ',a.lname) as emp_name, a.weekly_off FROM employee as a LEFT JOIN users as b ON a.emp_id = b.emp_id WHERE a.fname != ' ' and b.user_id = $user_id ORDER BY a.fname,a.lname "; 

    $htmldata = array();
    $senddata =  array();
    $htmlstring = '<table class="table table-bordered table-striped" >
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Date</th>
                                <th>Short Code</th>
                                <th>Logged in</th>
                                <th>First Logout</th>
                                <th>Last Login</th>
                                <th>Logged out</th>
                                <th>IP Address</th>
                                <th></th>
                            </tr>
                        </thead> 
                        <tbody>';
                              

    $stmt = $db->getRows($sql);
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        { 
            $emp_id = $row['emp_id'];
            $emp_name = $row['emp_name'];
            $user_id = $row['user_id'];
            $weekly_off = strtoupper($row['weekly_off']);
            $start = strtotime($start_date);
            $end = strtotime($end_date);
            $today = strtotime(date("Y-m-d"));
            if ($end>$today)
            {
                $end = $today;
            }
            while($start <= $end)
            {
                $log_date = date('Y-m-d', $start).PHP_EOL;
                $print_log_date = date('d-m-Y', $start).PHP_EOL; 
                $ip_address = "";
                $login_details_id = 0;
                $innerinnersql = "SELECT log_date,DATE_FORMAT(log_date,'%H:%i') as logged_in_time , DATE_FORMAT(log_date,'%d-%m-%Y %H:%i') as print_log_date,ip_address,status,login_details_id FROM login_details WHERE emp_id = $user_id and date(log_date) ='$log_date' ORDER BY log_date";                
                $innerinnerstmt = $db->getRows($innerinnersql);
                $logged_in_datetime = "";
                $logged_in_time = "";
                $first_logged_out_datetime = "";
                $first_logged_out_time = "";

                $last_logged_in_datetime = "";
                $logged_out_datetime = "";
                $login_count = 0;
                if ($innerinnerstmt->num_rows > 0)
                {
                    while($innerinnerrow = $innerinnerstmt->fetch_assoc())
                    {   
                        if ($logged_in_datetime!="")
                        {

                        }
                        else if ($innerinnerrow['log_date'])
                        {
                            if ($innerinnerrow['status'] == 'logged_in')
                            {
                                $logged_in_datetime = $innerinnerrow['print_log_date'];
                                $ip_address = $innerinnerrow['ip_address'];
                                $login_details_id = $innerinnerrow['login_details_id'];
                                $logged_in_time = $innerinnerrow['logged_in_time'];
                            }
                        }
                        if ($login_count==1)
                        {
                            if ($innerinnerrow['status'] == 'logged_out')
                            {
                                $first_logged_out_datetime = $innerinnerrow['print_log_date'];
                                $first_logged_out_time = $innerinnerrow['logged_in_time'];
                            }
                        }

                        if ($innerinnerrow['status'] == 'logged_in')
                        {
                            $last_logged_in_datetime = $innerinnerrow['print_log_date'];
                            $login_count = $login_count + 1;
                        }
                        if ($innerinnerrow['log_date'])
                        {
                            if ($innerinnerrow['status'] == 'logged_out')
                            {
                                $logged_out_datetime = $innerinnerrow['print_log_date'];
                            }
                        }
                    }
                }
                
                $week_day = date('l', strtotime( $log_date));
                $short_code="A";
                if ($logged_in_datetime!='')
                {
                    $short_code="P";
                    if (strtoupper($week_day) == strtoupper($weekly_off)) 
                    {
                        $short_code="WOP";  
                    }
                    
                    $isDataExists = $db->getOneRecord("SELECT 1 FROM holidays where hdate = '".$log_date."'");
                    if($isDataExists)
                    {
                        $short_code="HP"; 
                    }
                    
                    if ($logged_in_time>="14:00")
                    {
                        $short_code="HDP"; 
                    }
                    if ($logged_in_time>"10:30" &&  $logged_in_time<"14:00")
                    {
                        $short_code = "P [L]";
                        $htmlstring .= '<tr style="color:#e16464;">';   
                    }
                    else
                    {
                        $htmlstring .= '<tr>';
                    }
                    
                }
                if ($short_code=="A")
                {
                    if (strtoupper($week_day) == strtoupper($weekly_off)) 
                    {
                        $short_code="WO";  
                        $htmlstring .= '<tr style="background-color:#637d06;color:#ffffff;">';  
                    }
                    else
                    {
                        $isDataExists = $db->getOneRecord("SELECT 1 FROM holidays where hdate = '".$log_date."'");
                        if($isDataExists)
                        {
                            $short_code="H";  
                            $htmlstring .= '<tr style="background-color:#637d06;color:#ffffff;">';
                        }
                        else
                        {
                            $htmlstring .= '<tr style="background-color:#e16464;color:#ffffff;">'; 
                        }
                    }
                }
                $diff = "Error"; 
                if ($first_logged_out_datetime)
                {
                    $datetime1 = new DateTime('2021-04-23 '.$logged_in_time);
                    $datetime2 = new DateTime('2021-04-23 '.$first_logged_out_time);
                    $interval = $datetime1->diff($datetime2);
                    $diff = $interval->format('%H:%i');
                }
                $htmlstring .= '<td>'.$emp_name.'</td>
                                <td>'.$print_log_date.'('.$week_day.')</td>
                                <td>'.$short_code.'</td>';

                $htmlstring .= '<td>'.$logged_in_datetime.'</td>
                                <td>'.$first_logged_out_datetime.'<p>('.$diff.' Hrs)</p></td>';

                $htmlstring .= '<td>'.$last_logged_in_datetime.'</td>
                                <td>'.$logged_out_datetime.'</td>
                                <td>'.$ip_address.'</td>
                                <td><a href="javascript:void(0)" class="btn btn-default btn-sm" ng-click="show_operting(\''.$log_date.'\')" title="View Screen Activity.." alt="View Screen Activity.."><i class="glyphicon glyphicon-th-list"></i></a></td>
                            </tr>';
                $start = strtotime("+1 day", $start);
            }
            
        }
    }
    $htmlstring .='</table>';  
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
    
});

$app->get('/show_operating/:log_date/:user_id', function($log_date,$user_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $session_login_details_id = $_SESSION['session_login_details_id'];

    $htmldata = array();
    $senddata =  array();
    $htmlstring = '<a href="javascript:void(0)" class="btn btn-default btn-sm" style="margin:5px;float:right;" ng-click="back()" title="Back.." alt="Back.."><i class="glyphicon glyphicon-remove"></i></a><table class="table table-bordered table-striped" >
                        <thead> 
                            <tr>
                                <th>Log Date</th>
                                <th>Status</th>
                                <th>IP Address</th>
                                <th></th>
                            </tr>
                        </thead> 
                        <tbody>';
                              

    
    $innerinnersql = "SELECT log_date,DATE_FORMAT(log_date,'%H:%i') as logged_in_time , DATE_FORMAT(log_date,'%d-%m-%Y %H:%i') as print_log_date,ip_address,status,login_details_id FROM login_details WHERE emp_id = $user_id and date(log_date) ='$log_date' ORDER BY log_date";                
    $innerinnerstmt = $db->getRows($innerinnersql);
                
    if ($innerinnerstmt->num_rows > 0)
    {
        while($innerinnerrow = $innerinnerstmt->fetch_assoc())
        {  
            $print_log_date = $innerinnerrow['print_log_date'];
            $ip_address = $innerinnerrow['ip_address'];
            $status = $innerinnerrow['status'];
            $htmlstring .= '<td>'.$print_log_date.'</td>
                            <td>'.$status.'</td>
                            <td>'.$ip_address.'</td>
                            <td></td>
                        </tr>';
        }
    }
    $htmlstring .='</table>';
    
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
    
});


$app->get('/dailyattendance_ctrl/:start_date/:end_date', function($start_date,$end_date) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $session_login_details_id = $_SESSION['session_login_details_id'];

    $start_date = $start_date.'00:00:00';
    $end_date = $end_date.'23:59:59';
    $sql = "SELECT a.emp_id,b.user_id,CONCAT(a.salu,' ',a.fname,' ',a.lname) as emp_name, a.weekly_off FROM employee as a LEFT JOIN users as b ON a.emp_id = b.emp_id WHERE a.fname != ' ' and a.fname!='admin' and a.status = 'Active' ORDER BY a.fname,a.lname "; 

    $htmldata = array();
    $senddata =  array();
    $htmlstring = '<table class="table table-bordered table-striped" >
                        <thead>
                            <tr>
                                <th>Employee Name</th>';
    $start = strtotime($start_date);
    $end = strtotime($end_date);
    $today = strtotime(date("Y-m-d"));
    if ($end>$today)
    {
        $end = $today;
    }
    while($start <= $end)
    {
        $day = date('d',$start).PHP_EOL;
        $htmlstring .= '<th>'.$day.'</th>'; 
        $start = strtotime("+1 day", $start);
    }
    $htmlstring .= '        </tr>
                        </thead> 
                        <tbody>';
                              

    $stmt = $db->getRows($sql);
    if($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        { 
            $emp_id = $row['emp_id'];
            $emp_name = $row['emp_name'];
            $user_id = $row['user_id'];
            $weekly_off = strtoupper($row['weekly_off']);
            $start = strtotime($start_date);
            $end = strtotime($end_date);
            $today = strtotime(date("Y-m-d"));
            $htmlstring .= '<tr><td>'.$emp_name.'</td>';
            if ($end>$today)
            {
                $end = $today;
            }
            while($start <= $end)
            {
                $log_date = date('Y-m-d', $start).PHP_EOL;
                $print_log_date = date('d-m-Y', $start).PHP_EOL; 
                $ip_address = "";
                $login_details_id = 0;
                $innerinnersql = "SELECT log_date,DATE_FORMAT(log_date,'%H:%i') as logged_in_time , DATE_FORMAT(log_date,'%d-%m-%Y %H:%i') as print_log_date,ip_address,status,login_details_id FROM login_details WHERE emp_id = $user_id and date(log_date) ='$log_date' ORDER BY log_date";                
                $innerinnerstmt = $db->getRows($innerinnersql);
                $logged_in_datetime = "";
                $logged_in_time = "";
                $logged_out_datetime = "";
                if ($innerinnerstmt->num_rows > 0)
                {
                    while($innerinnerrow = $innerinnerstmt->fetch_assoc())
                    {   
                        if ($logged_in_datetime!="")
                        {

                        }
                        else if ($innerinnerrow['log_date'])
                        {
                            if ($innerinnerrow['status'] == 'logged_in')
                            {
                                $logged_in_datetime = $innerinnerrow['print_log_date'];
                                $ip_address = $innerinnerrow['ip_address'];
                                $login_details_id = $innerinnerrow['login_details_id'];
                                $logged_in_time = $innerinnerrow['logged_in_time'];
                            }
                        }
                        if ($innerinnerrow['log_date'])
                        {
                            if ($innerinnerrow['status'] == 'logged_out')
                            {
                                $logged_out_datetime = $innerinnerrow['print_log_date'];
                                //$ip_address = $innerinnerrow['$ip_address'];
                            }
                        }
                    }
                }
                
                $week_day = date('l', strtotime( $log_date));
                $short_code="A";
                if ($logged_in_datetime!='')
                {
                    $short_code="P";
                    if (strtoupper($week_day) == strtoupper($weekly_off)) 
                    {
                        $short_code="WOP";  
                    }
                    
                    $isDataExists = $db->getOneRecord("SELECT 1 FROM holidays where hdate = '".$log_date."'");
                    if($isDataExists)
                    {
                        $short_code="HP"; 
                    }
                    
                    if ($logged_in_time>="14:00")
                    {
                        $short_code="HDP"; 
                    }
                    if ($logged_in_time>"10:30" &&  $logged_in_time<"14:00")
                    {
                        $short_code = "P [L]";
                        //$htmlstring .= '<tr style="color:#e16464;">';   
                    }
                    else
                    {
                        //$htmlstring .= '<tr>';
                    }
                    
                }
                if ($short_code=="A")
                {
                    if (strtoupper($week_day) == strtoupper($weekly_off)) 
                    {
                        $short_code="WO";  
                        //$htmlstring .= '<tr style="background-color:#637d06;color:#ffffff;">';  
                    }
                    else
                    {
                        $isDataExists = $db->getOneRecord("SELECT 1 FROM holidays where hdate = '".$log_date."'");
                        if($isDataExists)
                        {
                            $short_code="H";  
                            //$htmlstring .= '<tr style="background-color:#637d06;color:#ffffff;">';
                        }
                        else
                        {
                            //$htmlstring .= '<tr style="background-color:#e16464;color:#ffffff;">'; 
                        }
                    }
                } 
                if ($short_code=='A' || $short_code == 'P [L]')
                {
                    $htmlstring .= '<td style="color:#e16464">'.$short_code.'</td>';
                }
                else{
                    $htmlstring .= '<td  style="color:#333;">'.$short_code.'</td>';
                }

                $start = strtotime("+1 day", $start);
            }
            $htmlstring .='</tr>';
            
        }
    }
    $htmlstring .'</table>';  
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
    
});

$app->get('/holidays_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    
    $sql = "SELECT hdate, DATE_FORMAT(hdate,'%d-%m-%Y') AS htdate,holiday_title from holidays ORDER BY hdate ";
    $holidaylist = $db->getAllRecords($sql);
    echo json_encode($holidaylist);
    
});

$app->get('/addusercomments/:id/:comments/:option', function($id, $comments,$option) use ($app) {
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
    if ($option=='p')
    {
        $query = "INSERT INTO commenting (category_id, category, user_comment, created_by ,created_date)   VALUES('$id', 'Property', '$comments','$created_by','$created_date' )";
    }
    if ($option=='e')
    {
        $query = "INSERT INTO commenting (category_id, category, user_comment, created_by ,created_date)   VALUES('$id', 'Enquiry', '$comments','$created_by','$created_date' )";
    }
    if ($option=='a')
    {
        $query = "INSERT INTO commenting (category_id, category, user_comment, created_by ,created_date)   VALUES('$id', 'Activity', '$comments','$created_by','$created_date' )";
    }
    if ($option=='t')
    {
        $query = "INSERT INTO commenting (category_id, category, user_comment, created_by ,created_date)   VALUES('$id', 'Task', '$comments','$created_by','$created_date' )";
    }
    $result = $db->insertByQuery($query);
    $response["status"] = "success";
    $response["message"] = "Message Added successfully";
    echoResponse(200, $response);
});

$app->get('/save_reminders_comment/:activity_id/:closure_comment', function($activity_id, $closure_comment) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest") 
    {
        return;
    }
    $response = array();
    $modified_date = date('Y-m-d H:i:s');
    $sql = "UPDATE activity set closure_comment = '$closure_comment', status = 'Completed' WHERE activity_id = $activity_id ";
    $result = $db->updateByQuery($sql);
    $response["status"] = "success";
    $response["message"] = "Message Added successfully";
    echoResponse(200, $response);
});

$app->get('/save_referrals_comment/:referrals_id/:closure_comment/:next_date', function($referrals_id, $closure_comment,$next_date) use ($app) {
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

    $sql = "INSERT INTO referrals_comment (referrals_id, closure_comment,next_date,created_by,created_date) VALUES ('$referrals_id','$closure_comment','$next_date','$created_by','$created_date')";    
    $result = $db->insertByQuery($sql);
    $response["comment_id"] = $result;
    $response["status"] = "success";
    $response["message"] = "Comment Added successfully";
    echoResponse(200, $response);
});


$app->get('/save_callinguser_comment/:call_id/:user_comment', function($call_id, $user_comment) use ($app) {
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

    $sql = "INSERT INTO call_commenting (call_id, user_comment,created_by,created_date) VALUES ('$call_id','$user_comment','$created_by','$created_date')";    
    $result = $db->insertByQuery($sql);
    $response["comment_id"] = $result;
    $response["status"] = "success";
    $response["message"] = "Comment Added successfully";
    echoResponse(200, $response);
});

$app->get('/update_calling_status/:call_id/:call_status', function($call_id, $call_status) use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE received_data set call_status = '$call_status' where call_id = $call_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
});

$app->get('/create_referral_client/:referrals_id', function($referrals_id) use ($app) {
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

    $contact_off = "";
    $broker_id = "";
    $company_name = "";
    $mobile_no = "";
    $email = "";
    $address1 = "";
    $referralsdata = $db->getAllRecords("SELECT * FROM referrals WHERE referrals_id = $referrals_id");
    if ($referralsdata)
    {
        $contact_off = $referralsdata[0]['contact_off'];
        $broker_id = $referralsdata[0]['broker_id'];
        $company_name = $referralsdata[0]['company_name'];
        $mobile_no = $referralsdata[0]['mobile_no'];
        $email = $referralsdata[0]['email'];
        $address1 = $referralsdata[0]['address1'];
    }
    $isExists = $db->getOneRecord("select 1 from contact WHERE mob_no = '$mobile_no'");
    if(!$isExists)
    {
        $f_name="";
        $l_name="";

        $actual_name = explode(" ",$broker_id);
        
        $f_name = $actual_name[0];
        $l_name = $actual_name[1];
        $sql = "INSERT INTO contact (contact_off, f_name,l_name, company_name, mob_no, email, add1, created_by,created_date) VALUES ('$contact_off','$f_name','$l_name','$company_name','$mobile_no','$email','$add1','$created_by','$created_date')";    
        $result = $db->insertByQuery($sql);
        $response["contact_id"] = $result;    
        $response["status"] = "success";
        $response["message"] = "Contact Added successfully";
    }
    else
    {
        $response["status"] = "error";
        $response["message"] = "Contact Already Exist..!!"; 
    }
    echoResponse(200, $response);
});



$app->get('/create_new_activity/:activity_id/:next_date', function($activity_id,$next_date) use ($app) {
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

    $sql = "INSERT INTO activity (activity_type, activity_sub_type,activity_start,activity_end, description, enquiry_id, property_id, project_id, client_id, broker_id, developer_id, target_id, teams, assign_to, remind, remind_before, remind_time,created_date) SELECT activity_type, activity_sub_type, activity_start, activity_end , description, enquiry_id, property_id, project_id, client_id, broker_id, developer_id, target_id, teams, assign_to, remind, remind_before, remind_time, created_date FROM activity WHERE activity_id = $activity_id";

    
    $result = $db->insertByQuery($sql);
    $response["activity_id"] = $result;
    $new_activity_id = $result;

    $sql = "UPDATE activity set status = 'Open' , activity_start= '$next_date' , activity_end='$next_date', created_by = '$created_by' , created_date = '$created_date' WHERE activity_id = $new_activity_id";

    $result = $db->updateByQuery($sql);
    
    $response["status"] = "success";
    $response["message"] = "Activity Created [ACTIVITYID:".$new_activity_id."].. !!!";
    echo json_encode($response);

});






// BEHAVIOUR

$app->get('/show_behaviour/:for_month', function($for_month) use ($app) 
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

    $this_month = substr($for_month,5,2);
    $this_year = substr($for_month,0,4);
    $sql = "SELECT * FROM users WHERE status !='InActive' and username!='admin'";
    $stmt = $db->getRows($sql);
    
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            
            $user_id = $row['user_id'];
            $sql = "select 1 from behaviour where user_id = $user_id and month(data_date) = $this_month and year(data_date) = $this_year ";
            
            $isExists = $db->getOneRecord("select 1 from behaviour where user_id = $user_id and month(data_date) = $this_month  and year(data_date) = $this_year ");
            
            if(!$isExists)
            {
                $query = "INSERT INTO behaviour (user_id, data_date)  VALUES('$user_id','$for_month') ";
                
                $result = $db->insertByQuery($query);
            }
        }
        $stmt->close();
    }
    
    $sql = "SELECT *,b.user_id, CONCAT(c.salu,' ',c.fname,' ',c.lname) as name FROM behaviour as a LEFT JOIN users as b on a.user_id = b.user_id LEFT JOIN employee as c ON b.emp_id = c.emp_id WHERE month(a.data_date) = $this_month and b.status != 'InActive'  and year(data_date) = $this_year ORDER BY c.lname,c.fname ";

    $htmlstring .= '<table class="table table-bordered table-striped" >
                                <thead>
                                    <tr>
                                        <th>Employee Name<p>2 Points</p></th>
                                        <th>Monthly Attendance<p>2 Points</p></th>
                                        <th>New Idea<p>2 Points</p></th>
                                        <th>Activity Participation<p>1 Point</p></th>
                                        <th>Daily Activity<p>1 Point</p></th>
                                        <th>Response Assigned<p>2 Points</p></th>
                                        <th>Transaction Process<p>2 Points</p></th>
                                    </tr>
                                </thead>
                                <tbody>';

    $stmt = $db->getRows($sql);

    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $behaviour_id = $row['behaviour_id'];
            $htmlstring .= '<tr>
                                <td>'.$row['name'].'</td>
                                <td><label><input type="checkbox" class="check_element" name="monthly_attendance_'.$behaviour_id.'" id="monthly_attendance_'.$behaviour_id.'" ng-model="monthly_attendance_'.$behaviour_id.'" ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_behaviour(\'monthly_attendance\','.$behaviour_id.',monthly_attendance_'.$behaviour_id.')" value="'.$row['monthly_attendance'].'"';
                                if ($row['monthly_attendance']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                }
                                $htmlstring .= '/></label></td>';

                                
                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="new_idea_'.$behaviour_id.'" id="new_idea_'.$behaviour_id.'" ng-model="new_idea_'.$behaviour_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_behaviour(\'new_idea\','.$behaviour_id.',new_idea_'.$behaviour_id.')" value="'.$row['new_idea'].'"';
                                if ($row['new_idea']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';

                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="activity_participation_'.$behaviour_id.'" id="activity_participation_'.$behaviour_id.'" ng-model="activity_participation_'.$behaviour_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_behaviour(\'activity_participation\','.$behaviour_id.',activity_participation_'.$behaviour_id.')" value="'.$row['activity_participation'].'"';
                                if ($row['activity_participation']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';
                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="daily_activity_'.$behaviour_id.'" id="daily_activity_'.$behaviour_id.'" ng-model="daily_activity_'.$behaviour_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_behaviour(\'daily_activity\','.$behaviour_id.',daily_activity_'.$behaviour_id.')" value="'.$row['daily_activity'].'"';
                                if ($row['daily_activity']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';

                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="response_assigned_'.$behaviour_id.'" id="response_assigned_'.$behaviour_id.'" ng-model="response_assigned_'.$behaviour_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_behaviour(\'response_assigned\','.$behaviour_id.',response_assigned_'.$behaviour_id.')" value="'.$row['response_assigned'].'"';
                                if ($row['response_assigned']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';

                                $htmlstring .= '<td><label><input type="checkbox" class="check_element" name="transaction_process_'.$behaviour_id.'" id="transaction_process_'.$behaviour_id.'" ng-model="transaction_process_'.$behaviour_id.'"  ng-true-value="true" ng-false-value="false" style="width:20px;height:20px;"  ng-click="update_behaviour(\'transaction_process\','.$behaviour_id.',transaction_process_'.$behaviour_id.')" value="'.$row['transaction_process'].'"';
                                if ($row['transaction_process']=='true')
                                {
                                    $htmlstring .= ' ng-checked="true" checked="checked" ' ;
                                    
                                }
                                $htmlstring .= '/></label></td>';
                $htmlstring .= '    </tr>';
        }
        $htmlstring .='</tbody>
                            <tfoot>
                                
                            </tfoot>
                        </table>'; 
    }  
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/update_behaviour/:action/:behaviour_id/:action_value', function($action, $behaviour_id,$action_value) use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE behaviour set $action = '$action_value' where behaviour_id = $behaviour_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
});

// Social Media

$app->get('/show_social_media_old/:for_month', function($for_month) use ($app) 
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

    $this_month = substr($for_month,5,2);
    $sql = "SELECT * FROM users WHERE status !='InActive' and username!='admin'";
    $stmt = $db->getRows($sql);
    
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            
            $user_id = $row['user_id'];
            $sql = "select 1 from social_media where user_id = $user_id and month(data_date) = $this_month";
            
            $isExists = $db->getOneRecord("select 1 from social_media where user_id = $user_id and month(data_date) = $this_month ");
            
            if(!$isExists)
            {
                $query = "INSERT INTO social_media (user_id, data_date)  VALUES('$user_id','$for_month') ";
                
                $result = $db->insertByQuery($query);
            }
        }
        $stmt->close();
    }
    
    $sql = "SELECT *,b.user_id, CONCAT(c.salu,' ',c.fname,' ',c.lname) as name FROM social_media as a LEFT JOIN users as b on a.user_id = b.user_id LEFT JOIN employee as c ON b.emp_id = c.emp_id WHERE month(a.data_date) = $this_month ORDER BY c.lname,c.fname ";


    

    /*$htmlstring .= '<table class="table table-bordered table-striped" >
                                <thead>
                                    <tr>
                                        <th>Employee Name</th>
                                        <th>Instagram Likes</th>
                                        <th>Instagram Followers</th>
                                        <th>Facebook Likes</th>
                                        <th>Facebook Followers</th>
                                        <th>Old Youtube Subscribers</th>
                                        <th>New Youtube Subscribers</th>
                                        <th>Old Linkedin Followers</th>
                                        <th>New Linkedin Followers</th>
                                        <th>Old Twitter Followers</th>
                                        <th>New Twitter Followers</th>
                                        <th>Lead Generation</th>
                                    </tr>
                                </thead>
                                <tbody>';

    $stmt = $db->getRows($sql);

    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $social_media_id = $row['social_media_id'];
            $htmlstring .= '<tr>
                                <td>'.$row['name'].'</td>
                                

                                <td><input type="text" class="form-control numbers" id="instagram_likes_'.$social_media_id.'" name="instagram_likes_'.$social_media_id.'" placeholder="Instagram Likes" ng-model="social_media.instagram_likes_'.$social_media_id.'" ng-init = "social_media.instagram_likes_'.$social_media_id.'='.$row['instagram_likes'].'"  ng-change="update_social_media(\'instagram_likes\','.$social_media_id.',social_media.instagram_likes_'.$social_media_id.')" value="'.$row['instagram_likes'].'"/></td>

                                <td><input type="text" class="form-control numbers" id="instagram_followers_'.$social_media_id.'" name="instagram_followers_'.$social_media_id.'" placeholder="Instagram Followers" ng-model="social_media.instagram_followers_'.$social_media_id.'"  ng-init = "social_media.instagram_followers_'.$social_media_id.'='.$row['instagram_followers'].'" ng-change="update_social_media(\'instagram_followers\','.$social_media_id.',social_media.instagram_followers_'.$social_media_id.')" value="'.$row['instagram_followers'].'"/></td>

                                <td><input type="text" class="form-control numbers" id="facebook_likes_'.$social_media_id.'" name="facebook_likes_'.$social_media_id.'" placeholder="Facebook Likes" ng-model="social_media.facebook_likes_'.$social_media_id.'"  ng-init = "social_media.facebook_likes_'.$social_media_id.'='.$row['facebook_likes'].'" ng-change="update_social_media(\'facebook_likes\','.$social_media_id.',social_media.facebook_likes_'.$social_media_id.')" value="'.$row['facebook_likes'].'"/></td>

                                <td><input type="text" class="form-control numbers" id="facebook_followers_'.$social_media_id.'" name="facebook_followers_'.$social_media_id.'" placeholder="Facebook Followers" ng-model="social_media.facebook_followers_'.$social_media_id.'"  ng-init = "social_media.facebook_followers_'.$social_media_id.'='.$row['facebook_followers'].'" ng-change="update_social_media(\'facebook_followers\','.$social_media_id.',social_media.facebook_followers_'.$social_media_id.')" value="'.$row['facebook_followers'].'"/></td>

                                <td><input type="text" class="form-control numbers" id="old_youtube_subscribers_'.$social_media_id.'" name="old_youtube_subscribers_'.$social_media_id.'" placeholder="Old Youtube Subscribers" ng-model="social_media.old_youtube_subscribers_'.$social_media_id.'"  ng-init = "social_media.old_youtube_subscribers_'.$social_media_id.'='.$row['old_youtube_subscribers'].'" ng-change="update_social_media(\'old_youtube_subscribers\','.$social_media_id.',social_media.old_youtube_subscribers_'.$social_media_id.')" value="'.$row['old_youtube_subscribers'].'"/></td>

                                <td><input type="text" class="form-control numbers" id="new_youtube_subscribers_'.$social_media_id.'" name="new_youtube_subscribers_'.$social_media_id.'" placeholder="Instagram Likes" ng-model="social_media.new_youtube_subscribers_'.$social_media_id.'"  ng-init = "social_media.new_youtube_subscribers_'.$social_media_id.'='.$row['new_youtube_subscribers'].'" ng-change="update_social_media(\'new_youtube_subscribers\','.$social_media_id.',social_media.new_youtube_subscribers_'.$social_media_id.')" value="'.$row['new_youtube_subscribers'].'"/></td>
                                
                                <td><input type="text" class="form-control numbers" id="old_linkedin_followers_'.$social_media_id.'" name="old_linkedin_followers_'.$social_media_id.'" placeholder="Old Linkedin Followers" ng-model="social_media.old_linkedin_followers_'.$social_media_id.'"  ng-init = "social_media.old_linkedin_followers_'.$social_media_id.'='.$row['old_linkedin_followers'].'" ng-change="update_social_media(\'old_linkedin_followers\','.$social_media_id.',social_media.old_linkedin_followers_'.$social_media_id.')" value="'.$row['old_linkedin_followers'].'"/></td>

                                <td><input type="text" class="form-control numbers" id="new_linkedin_followers_'.$social_media_id.'" name="new_linkedin_followers_'.$social_media_id.'" placeholder="New Linkedin Followers" ng-model="social_media.new_linkedin_followers_'.$social_media_id.'"  ng-init = "social_media.new_linkedin_followers_'.$social_media_id.'='.$row['new_linkedin_followers'].'" ng-change="update_social_media(\'new_linkedin_followers\','.$social_media_id.',social_media.new_linkedin_followers_'.$social_media_id.')" value="'.$row['new_linkedin_followers'].'"/></td>

                                <td><input type="text" class="form-control numbers" id="old_twitter_followers_'.$social_media_id.'" name="old_twitter_followers_'.$social_media_id.'" placeholder="Old Twitter Followers" ng-model="social_media.old_twitter_followers_'.$social_media_id.'"  ng-init = "social_media.old_twitter_followers_'.$social_media_id.'='.$row['old_twitter_followers'].'" ng-change="update_social_media(\'old_twitter_followers\','.$social_media_id.',social_media.old_twitter_followers_'.$social_media_id.')" value="'.$row['old_twitter_followers'].'"/></td>

                                <td><input type="text" class="form-control numbers" id="new_twitter_followers_'.$social_media_id.'" name="new_twitter_followers_'.$social_media_id.'" placeholder="New Twitter Followers" ng-model="social_media.new_twitter_followers_'.$social_media_id.'"  ng-init = "social_media.new_twitter_followers_'.$social_media_id.'='.$row['new_twitter_followers'].'" ng-change="update_social_media(\'new_twitter_followers\','.$social_media_id.',social_media.new_twitter_followers_'.$social_media_id.')" value="'.$row['new_twitter_followers'].'"/></td>

                                <td><input type="text" class="form-control numbers" id="lead_generation_'.$social_media_id.'" name="lead_generation_'.$social_media_id.'" placeholder="Lead Generation" ng-model="social_media.lead_generation_'.$social_media_id.'"  ng-init = "social_media.lead_generation_'.$social_media_id.'='.$row['lead_generation'].'"  ng-change="update_social_media(\'lead_generation\','.$social_media_id.',social_media.lead_generation_'.$social_media_id.')" value="'.$row['lead_generation'].'"/></td>';


                $htmlstring .= '    </tr>';
        }
        $htmlstring .='</tbody>
                            <tfoot>
                                
                            </tfoot>
                        </table>'; 
    }  
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;*/
    $social_medias = $db->getAllRecords($sql);
    echo json_encode($social_medias);
});

$app->get('/update_social_media/:action/:social_media_id/:action_value', function($action, $social_media_id,$action_value) use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE social_media set $action = '$action_value' where social_media_id = $social_media_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
});


$app->get('/social_media_list', function() use ($app) 
{
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];
    $role = $session['role'];

    $sql = "SELECT *, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by_name, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(f.team_name SEPARATOR ',') FROM teams as f where FIND_IN_SET(f.team_id , a.teams)) as teams,(SELECT GROUP_CONCAT(h.sub_team_name SEPARATOR ',') FROM sub_teams as h where FIND_IN_SET(h.sub_team_id , a.sub_teams)) as sub_teams, DATE_FORMAT(a.data_date,'%d/%m/%Y') AS data_date FROM social_media as a ORDER BY c.lname,c.fname ";

    if (in_array("Admin", $role))
    {
        $sql = "SELECT *, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by_name, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(f.team_name SEPARATOR ',') FROM teams as f where FIND_IN_SET(f.team_id , a.teams)) as teams,(SELECT GROUP_CONCAT(h.sub_team_name SEPARATOR ',') FROM sub_teams as h where FIND_IN_SET(h.sub_team_id , a.sub_teams)) as sub_teams, DATE_FORMAT(a.data_date,'%d/%m/%Y') AS data_date FROM social_media as a ORDER BY a.data_date DESC ";
    }
    else{
        $sql = "SELECT *, (SELECT CONCAT(q.salu,' ',q.fname,' ',q.lname) FROM users as p LEFT JOIN employee as q on p.emp_id = q.emp_id WHERE p.user_id = a.created_by) as created_by_name, (SELECT GROUP_CONCAT(CONCAT(k.salu,' ',k.fname,' ',k.lname) SEPARATOR ',') FROM users as j LEFT JOIN employee as k on k.emp_id = j.emp_id WHERE FIND_IN_SET(j.user_id , a.assign_to)) as assign_to, (SELECT GROUP_CONCAT(f.team_name SEPARATOR ',') FROM teams as f where FIND_IN_SET(f.team_id , a.teams)) as teams,(SELECT GROUP_CONCAT(h.sub_team_name SEPARATOR ',') FROM sub_teams as h where FIND_IN_SET(h.sub_team_id , a.sub_teams)) as sub_teams, DATE_FORMAT(a.data_date,'%d/%m/%Y') AS data_date FROM social_media as a WHERE (a.created_by = $user_id or FIND_IN_SET ($user_id ,a.assign_to)) ORDER BY a.data_date DESC";
    }
    
    $social_medias = $db->getAllRecords($sql);
    echo json_encode($social_medias);
});


$app->post('/social_media_add', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('media_category'),$r->social_media);
    $db = new DbHandler();

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:m:s');
    $r->social_media->created_by = $created_by;
    $r->social_media->created_date = $created_date;

    if (isset($r->social_media->data_date))
    {
        $data_date = $r->social_media->data_date;
        $tdata_date = substr($data_date,6,4)."-".substr($data_date,3,2)."-".substr($data_date,0,2);
        $r->social_media->data_date = $tdata_date;
    }
    
    $tabble_name = "social_media";
    $column_names = array('data_date','media_category','no_of_posts','no_of_likes','no_of_followers','no_of_subscribers','no_of_views','lead_generation','teams','sub_teams','assign_to','created_by','created_date');
    $multiple=array("teams","sub_teams","assign_to");
    $result = $db->insertIntoTable($r->social_media, $column_names, $tabble_name, $multiple);
    if ($result != NULL) {
        $response["status"] = "success";
        $response["message"] = "Social Media Entry created successfully";
        $response["social_media_id"] = $result;
        $_SESSION['tmpsocial_media_id'] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "error";
        $response["message"] = "Failed to create Social Media Entry. Please try again";
        echoResponse(201, $response);
    }            
    
});


$app->get('/social_media_edit_ctrl/:social_media_id', function($social_media_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $_SESSION['tmpsocial_mdeia_id']=$social_media_id;

    $sql = "SELECT *,DATE_FORMAT(a.data_date,'%d-%m-%Y') AS data_date FROM social_media as a WHERE a.social_media_id = $social_media_id ";
    $social_media = $db->getAllRecords($sql);
    echo json_encode($social_media);
});

$app->get('/social_media_documents/:social_media_id', function($social_media_id) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "SELECT * from attachments WHERE category = 'social_media_docs' and category_id = $social_media_id";
    $social_media_documents = $db->getAllRecords($sql);
    echo json_encode($social_media_documents);
});

$app->post('/social_media_update', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('social_media_id'),$r->social_media);
    $db = new DbHandler();
    $social_media_id  = $r->social_media->social_media_id;
    
    $modified_by = $session['user_id'];
    $modified_date = date('Y-m-d H:i:s');
    $r->social_media->modified_by = $modified_by;
    $r->social_media->modified_date = $modified_date;

    if (isset($r->social_media->data_date))
    {
        $data_date = $r->social_media->data_date;
        $tdata_date = substr($data_date,6,4)."-".substr($data_date,3,2)."-".substr($data_date,0,2);
        $r->social_media->data_date = $tdata_date;
    }

    $issocial_mediaExists = $db->getOneRecord("select 1 from social_media where social_media_id = $social_media_id");
    if($issocial_mediaExists)
    {
        
        $tabble_name = "social_media";
        
        $column_names = array('data_date','media_category','no_of_posts','no_of_likes','no_of_followers','no_of_subscribers','no_of_views','lead_generation','teams','sub_teams','assign_to','modified_by','modified_date');
        $multiple=array("teams","sub_teams","assign_to");
        $condition = "social_media_id=$social_media_id";
        $result = $db->NupdateIntoTable($r->social_media, $column_names, $tabble_name,$condition, $multiple);
        if ($result != NULL) 
        {
            $response["status"] = "success";
            $_SESSION['tmpsocial_media_id'] = $social_media_id;
            $response["message"] = "Social Media Updated successfully";
            echoResponse(200, $response);
        }
        else 
        {
            $response["status"] = "error";
            $response["message"] = "Failed to Update Social Media. Please try again";
            echoResponse(201, $response);
        }            
    }
    else
    {
        $response["status"] = "error";
        $response["message"] = "Social Media with the provided Social Media does not exists!";
        echoResponse(201, $response);
    }
});

$app->post('/social_media_documents_uploads', function() use ($app) {
    session_start();
    $social_media_id = $_SESSION['tmpsocial_media_id'];
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
        $file_names = "sm_".$social_media_id."_".time()."_".$count.".".$ext;
        $target = dirname( __FILE__ ). $ds."uploads" . $ds . "social_media_docs". $ds. $file_names;
        if(move_uploaded_file($images['tmp_name'][$i], $target)) {
            $success = true;
            $paths[] = $ext;
            if (isset($_POST['docs_category_'.$count]))
            {
                $file_category = $_POST['docs_category_'.$count];
            }
            else
            {
                $file_category = "";
            }

            $query = "INSERT INTO attachments (category, category_id, filenames, file_category, created_by, created_date)   VALUES('social_media_docs', '$social_media_id','$file_names','$file_category','$user_id', now() )";
            $result = $db->insertByQuery($query);
            $count ++;

        } else {
            $success = false;
            break;
        }
    }
    echo json_encode(['success'=>'files uploaded.'.$file_names]); 
    
});

$app->get('/social_media_image_update/:attachment_id/:field_name/:value', function($attachment_id,$field_name,$value) use ($app) {
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

$app->get('/getdatavalues_social_media/:field_name', function($field_name) use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    { 
        return;
    }
    $sql = "SELECT DISTINCT $field_name FROM social_media ORDER BY $field_name";
    
    $getdropdown_values = $db->getAllRecords($sql);
    echo json_encode($getdropdown_values);
});

$app->post('/search_social_media', function() use ($app) {
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

    $searchsql = "select * from social_media limit 0";
    
    if (in_array("Admin", $role))
    { 
        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM social_media as a LEFT JOIN designation as b ON a.designation_id = b.designation_id WHERE a.social_media_id > 0 ";

    }
    else if (in_array("Branch Head", $role))
    {
        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM social_media as a LEFT JOIN designation as b ON a.designation_id = b.designation_id WHERE a.social_media_id > 0 ";

    }
    else
    { 

        $searchsql = "SELECT *,DATE_FORMAT(a.created_date,'%d-%m-%Y %H:%i') AS created_on FROM social_media as a LEFT JOIN designation as b ON a.designation_id = b.designation_id WHERE a.social_media_id > 0 ";


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


//Web lead
function getwebsiteLeads($allParams){
    Log::info($allParams->name);
}

$app->post('/get-data/:web_lead',function($value) use ($app){
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    $response = array();

    $created_date = date('Y-m-d H:i:s');
    $name = $_POST['name'];
    $email = $_POST['email'];
    $city = $_POST['city'];
    $phone = $_POST['phone'];
    $budget = $_POST['budget'];
    $source = $_POST['source'];
    $slug = '0';
    $fetchdata="SELECT * FROM `website_lead` WHERE phone = '$phone'";
    $result4 = $db->getAllRecords($fetchdata);

    if  ( $result4==null) 
    {
        $sql = "INSERT INTO website_lead (name,  email, city, phone, budget, source,created_date,slug) VALUES(' $name',  '$email', '$city', '$phone', '$budget', '$source','$created_date','$slug')";   
        $result = $db->insertByQuery($sql);
        $sql2 = "INSERT INTO contact (f_name, email, mob_no, contact_off ,created_date,created_by) VALUES(' $name',  '$email', '$phone', 'Client',  '$created_date','1')";        
        $result2 = $db->insertByQuery($sql2);
         $websitedata = array(
          "Name"=>$name, 
          "Phone"=>$phone,  
          "Email"=>$email,
          "Budget"=>$budget,
          "City"=>$city,
          "Source"=>$source,
        );
    $obj = (object) $websitedata;
    echo json_encode($obj);
    } 
    else
    {
        $response["status"] = "error";
        $response["message"] = "Mobile Number Already Exists !!";
        echoResponse(201, $response);
    }
});

// $app->get('/show_weblead/:id/:next_page_id', function($id,$next_page_id) use ($app) {
   

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
            
//     $sql="SELECT *  from website_lead LIMIT 0";
//     $countsql = "SELECT count(*) as weblead_count from website_lead";
//     $htmlstring = '';
//     if (in_array("Admin", $role))
//     {
//         // $sql = "SELECT * FROM website_lead ORDER BY id DESC LIMIT $next_page_id, 30";
//         // $countsql = "SELECT count(*)  as weblead_count from website_lead ";
        
//         $sql = "SELECT * FROM website_lead WHERE slug = '0' ORDER BY id DESC LIMIT $next_page_id, 30";
//         $countsql = "SELECT count(*)  as weblead_count from website_lead WHERE slug = '0'";

//     }
//     else
//     {
//         $sql = "SELECT * FROM website_lead  WHERE slug = '0' ORDER BY id DESC LIMIT $next_page_id, 30";
//         $countsql = "SELECT count(*)  as weblead_count from website_lead  WHERE slug = '0' ";

//     }
//     $htmlstring .= '<table class="table table-bordered table-striped" datatable-Setupnosort="" >
//                                 <thead>
//                                     <tr>
//                                         <th>Name</th>
//                                         <th>Email</th>
//                                         <th>Phone</th>
//                                         <th>City</th>
//                                         <th>Budget</th>
//                                         <th>Source</th>
//                                         <th>Date</th>
//                                     </tr>
//                                 </thead>
//                                 <tbody>';

//     $stmt = $db->getRows($sql);
//     $webleadcountdata = $db->getAllRecords($countsql);
//     $weblead_count = 0;
    

//     if ($stmt->num_rows > 0)
//     {
//         while($row = $stmt->fetch_assoc())
//         {
//             $id = $row['id'];
//             $htmlstring .= '<tr>
//                                 <td>'.$row['name'].'</td>
//                                 <td>'.$row['email'].'</td>
//                                 <td>'.$row['phone'].'</td>
//                                 <td>'.$row['city'].'</td>
//                                 <td>'.$row['budget'].'</td>
//                                 <td>'.$row['source'].'</td>
//                                 <td>'.$row['created_date'].'</td>
//                                 <td><a href="#/enquiries_add/residential/?'.$row['id'].'" data-uder_id="'.$row['id'].'" title="Create Enquiry" alt="Create Enquiry"><i class="glyphicon glyphicon-pencil"></i></a></td>
//                             </tr>';
//         }
//         $htmlstring .='</tbody></table>'; 

//     } 
//     $stmt->close();
//     $htmldata['htmlstring']=$htmlstring;
//     $senddata[]=$htmldata;
//     if ($webleadcountdata)
//     {
//          $weblead_count = $webleadcountdata[0]['weblead_count'];
//     }
//     if ($weblead_count>0)
//     {
//         $senddata[0]['weblead_count']=$weblead_count;
//     }
//     echo json_encode($senddata);
// });

$app->get('/weblead_list_ctrl', function() use ($app) {
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    $permissions = $session['permissions'];  
    $role = $session['role'];
    if ($session['username']=="Guest")
    {
        return;
    }
    if (in_array("Admin", $role))
    {
    $sql = "SELECT * FROM website_lead WHERE slug = '0' ORDER BY id DESC";
    }
    else
    {
    $sql = "SELECT * FROM website_lead WHERE slug = '0' ORDER BY id DESC";
    }

    $users = $db->getAllRecords($sql);
    echo json_encode($users);
});


// 99 ACERS


function get99AcresLeads($allParams,$url){
    $crl = curl_init($url);
    curl_setopt ($crl, CURLOPT_POST, 1);
    curl_setopt ($crl, CURLOPT_POSTFIELDS, $allParams);
    curl_setopt ($crl, CURLOPT_RETURNTRANSFER,1);
    
    return curl_exec ($crl);
}


// 99ACRES

$app->get('/get_99acres_data/:process_date', function($process_date) use ($app) 
{
    $sql  = "";
    $db = new DbHandler();
    $session = $db->getSession();
    $created_by = 0;
    $created_date = date('Y-m-d H:i:s');
    $today_date = date('Y-m-d');
    if ($process_date!="0")
    {
        $today_date = $process_date;
    }
    $start_date = $today_date." 00:00:00";
    $end_date = $today_date." 23:59:59";
    $user_name = "";
    $password = "";
    $sql = "SELECT * FROM 99acres_users";
    $stmt = $db->getRows($sql);

    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $user_name = $row['user_name'];
            $password = $row['password'];
            $url =  "https://www.99acres.com/99api/v1/getmy99Response/OeAuXClO43hwseaXEQ/uid/";
            $request = "<?xml version='1.0'?><query><user_name>".$user_name."</user_name><pswd>".$password."</pswd><start_date>".$start_date."</start_date><end_date>".$end_date."</end_date></query>";
            
            $allParams = array('xml'=>$request);
            $leads = get99AcresLeads($allParams,$url);
            
            $xml = simplexml_load_string($leads, null, LIBXML_NOCDATA);
            //echo var_dump($xml);
            //echo $request;
            
            $count = 0;
            foreach ($xml->Resp as $main_node) 
            {
                $QueryId = "";
                $count = $count + 1;
                foreach ($xml->Resp->QryDtl as $element) {
                    $QueryId = $xml->Resp->QryDtl->attributes()->QueryId;
                    $CmpctLabl = "";
                    $QryInfo = "";
                    $RcvdOn = "";
                    $ProjId = "";
                    $ProjName = "";
                    $PROPERTY_CODE = "";
                    $SubUserName = "";
                    $ProdId = "";
                    $Name = "";
                    $Email = "";
                    $Phone = "";
                    foreach($element as $key => $val) {
                        if ($key=='CmpctLabl')
                        {
                            $CmpctLabl = "{$val}";
                        }      
                        if ($key=='QryInfo')
                        {
                            $QryInfo = "{$val}";
                        }                    

                        if ($key=='RcvdOn')
                        {
                            $RcvdOn = "{$val}";
                        }                    

                        if ($key=='ProjId')
                        {
                            $ProjId = "{$val}";
                        }                    

                        if ($key=='ProjName')
                        {
                            $ProjName = "{$val}";
                        }                    


                        if ($key=='PROPERTY_CODE')
                        {
                            $PROPERTY_CODE = "{$val}";
                        }                    

                        if ($key=='SubUserName')
                        {
                            $SubUserName = "{$val}";
                        }                    

                        if ($key=='ProdId')
                        {
                            $ProdId = "{$val}";
                        } 
                    }
                }
                foreach ($xml->Resp->CntctDtl as $element) {
                    foreach($element as $key => $val) {
                        if ($key=='Name')
                        {
                            $Name = "{$val}";
                        }                    

                        if ($key=='Email')
                        {
                            $Email = "{$val}";
                        }                    

                        if ($key=='Phone')
                        {
                            $Phone = "{$val}";
                        }  
                    }
                }
                $isqueryExists = $db->getOneRecord("select 1 from 99acres where queryid='$QueryId'");
                if(!$isqueryExists)
                {
                    if ($QueryId!="")
                    {
                        $query = "INSERT INTO 99acres (queryid, cmpctlabl, qryinfo, rcvdon, projid, projname, property_code, subusername,prodid, name, email, phone, created_by, created_date) VALUES( '$QueryId','$CmpctLabl','$QryInfo','$RcvdOn','$ProjId','$ProjName','$PROPERTY_CODE','$SubUserName','$ProdId','$Name','$Email','$Phone','$created_by','$created_date')";        
                        $result = $db->insertByQuery($query);
                    }
                }
            }
        }
    }
    $stmt->close();
    echo "done";
});

$app->get('/show_99acres', function() use ($app) 
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
    $user_id = $session['user_id'];
    $email_id = $session['email_id'];
    $role = $session['role'];
    $htmlstring = '';
    if (in_array("Admin", $role) || in_array("Branch Head", $role))
    {
        $sql = "SELECT *,DATE_FORMAT(rcvdon,'%d-%m-%Y %H:%i') AS trcvdon FROM 99acres ORDER BY rcvdon DESC ";
    }
    else
    {
        $sql = "SELECT *,DATE_FORMAT(rcvdon,'%d-%m-%Y %H:%i') AS trcvdon FROM 99acres WHERE subusername = '$email_id' ORDER BY rcvdon DESC ";
    }
    $htmlstring .= '<table class="table table-bordered table-striped" datatable-Setupnosort="" >
                                <thead>
                                    <tr>
                                        <th>Query Id</th>
                                        <th>Project ID</th>
                                        <th>Project Name</th>
                                        <th>Query</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Received On</th>
                                        <th>User Name</th>
                                        <th>Create Enquiry</th>
                                    </tr>
                                </thead>
                                <tbody>';

    $stmt = $db->getRows($sql);

    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $id = $row['id'];
            $htmlstring .= '<tr><td>'.$row['queryid'].'</td>
                                <td>'.$row['projid'].'</td>
                                <td>'.$row['projname'].'</td>
                                <td>'.$row['qryinfo'].'</td>
                                <td>'.$row['name'].'</td>
                                <td>'.$row['email'].'</td>
                                <td>'.$row['phone'].'</td>
                                <td>'.$row['trcvdon'].'</td>
                                <td>'.$row['subusername'].'</td>
                                <td><a href="#/enquiries_add/residential" title="Create Enquiry" alt="Create Enquiry"><i class="glyphicon glyphicon-pencil"></i></a></td>
                                </tr>';
        }
        $htmlstring .='</tbody></table>'; 
    }  
    $stmt->close();
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});


$app->get('/show_99acres_ORG/:for_month', function($for_month) use ($app) 
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
    

    $created_by = $session['user_id'];
    $created_date = date('Y-m-d H:i:s');
    
    $this_month = substr($for_month,5,2);
    if ($for_month==0)
    {
        $today_date = date('Y-m-d');
        $start_date = $today_date." 00:00:00";
        $end_date = $today_date." 23:59:59";
        $for_month = $today_date;

    }
    $user_name = "";
    $password = "";
    $sql = "SELECT * FROM 99acres_users";
    $stmt = $db->getRows($sql);
    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $user_name = $row['user_name'];
            $password = $row['password'];
            $url =  "http://www.99acres.com/99api/v1/getmy99Response/OeAuXClO43hwseaXEQ/uid/";
            $request = "<?xml version='1.0'?><query><user_name>".$user_name."</user_name><pswd>".$password."</pswd><start_date>".$for_month." 00:00:00</start_date><end_date>".$for_month." 23:59:00</end_date></query>";
            $allParams = array('xml'=>$request);
            $leads = get99AcresLeads($allParams,$url);

            //SELECT `id`, `queryid`, `cmpctlabl`, `qryinfo`, `rcvdon`, `projid`, `projname`, `property_code`, `subusername`, `name`, `email`, `phone`, `created_by`, `created_date`,
            $xml = simplexml_load_string($leads, null, LIBXML_NOCDATA);
            //echo $xml;
            //echo "NOW:->>";
            //echo $xml->Resp->QryDtl->attributes()->QueryId.'<br />';
            $count = 0;
            foreach ($xml->Resp as $main_node) 
            {
                $QueryId = "";
                $count = $count + 1;
                foreach ($xml->Resp->QryDtl as $element) {
                    $QueryId = $xml->Resp->QryDtl->attributes()->QueryId;
                    //echo "queryid".$QueryId;
                    $CmpctLabl = "";
                    $QryInfo = "";
                    $RcvdOn = "";
                    $ProjId = "";
                    $ProjName = "";
                    $PROPERTY_CODE = "";
                    $SubUserName = "";
                    $ProdId = "";
                    $Name = "";
                    $Email = "";
                    $Phone = "";
                    foreach($element as $key => $val) {
                        if ($key=='CmpctLabl')
                        {
                            $CmpctLabl = "{$val}";
                        }      
                        if ($key=='QryInfo')
                        {
                            $QryInfo = "{$val}";
                        }                    

                        if ($key=='RcvdOn')
                        {
                            $RcvdOn = "{$val}";
                        }                    

                        if ($key=='ProjId')
                        {
                            $ProjId = "{$val}";
                        }                    

                        if ($key=='ProjName')
                        {
                            $ProjName = "{$val}";
                        }                    


                        if ($key=='PROPERTY_CODE')
                        {
                            $PROPERTY_CODE = "{$val}";
                        }                    

                        if ($key=='SubUserName')
                        {
                            $SubUserName = "{$val}";
                        }                    

                        if ($key=='ProdId')
                        {
                            $ProdId = "{$val}";
                        }                    
                        
                        //echo "{$key}";
                        //echo "{$val}";
                        //echo "{$key}: {$val}";
            
                    }


                }
                foreach ($xml->Resp->CntctDtl as $element) {
                    foreach($element as $key => $val) {
                        if ($key=='Name')
                        {
                            $Name = "{$val}";
                        }                    

                        if ($key=='Email')
                        {
                            $Email = "{$val}";
                        }                    

                        if ($key=='Phone')
                        {
                            $Phone = "{$val}";
                        }  
                    }
                }
                /*echo "queryid".$QueryId;
                echo "name".$Name;
                echo $CmpctLabl."--".$QryInfo."--".$RcvdOn;*/
                $isqueryExists = $db->getOneRecord("select 1 from 99acres where queryid='$QueryId'");
                if(!$isqueryExists)
                {
                    if ($QueryId!="")
                    {
                        $query = "INSERT INTO 99acres (queryid, cmpctlabl, qryinfo, rcvdon, projid, projname, property_code, subusername,prodid, name, email, phone, created_by, created_date) VALUES( '$QueryId','$CmpctLabl','$QryInfo','$RcvdOn','$ProjId','$ProjName','$PROPERTY_CODE','$SubUserName','$ProdId','$Name','$Email','$Phone','$created_by','$created_date')";        
                        $result = $db->insertByQuery($query);
                    }
                }
            }
            
            //echo $count;
        }
    }
    $stmt->close();
    
    $htmlstring = '';
    $sql = "SELECT *,DATE_FORMAT(rcvdon,'%d-%m-%Y %H:%i') AS trcvdon FROM 99acres ORDER BY rcvdon DESC ";
    $htmlstring .= '<table class="table table-bordered table-striped" datatable-Setupnosort="" >
                                <thead>
                                    <tr>
                                        <th>Query Id</th>
                                        <th>Project ID</th>
                                        <th>Project Name</th>
                                        <th>Query</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Received On</th>
                                        <th>User Name</th>
                                        <th>Create Enquiry</th>
                                    </tr>
                                </thead>
                                <tbody>';

    $stmt = $db->getRows($sql);

    if ($stmt->num_rows > 0)
    {
        while($row = $stmt->fetch_assoc())
        {
            $id = $row['id'];
            $htmlstring .= '<tr><td>'.$row['queryid'].'</td>
                                <td>'.$row['projid'].'</td>
                                <td>'.$row['projname'].'</td>
                                <td>'.$row['qryinfo'].'</td>
                                <td>'.$row['name'].'</td>
                                <td>'.$row['email'].'</td>
                                <td>'.$row['phone'].'</td>
                                <td>'.$row['trcvdon'].'</td>
                                <td>'.$row['subusername'].'</td>
                                <td><a href="javascript:void(0)" title="Create Enquiry" alt="Create Enquiry"><i class="glyphicon glyphicon-pencil"></i></a><!--a href="javascript:void(0)" title="Create Enquiry" alt="Create Enquiry" ng-click="create_new_enquiry()"><i class="glyphicon glyphicon-pencil"></i></a--></td>
                                </tr>';
        }
        $htmlstring .='</tbody></table>'; 
    }  
    $stmt->close();
    $htmldata['htmlstring']=$htmlstring;
    $senddata[]=$htmldata;
    echo json_encode($senddata);
});

$app->get('/update_99acres/:action/:behaviour_id/:action_value', function($action, $behaviour_id,$action_value) use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $sql = "UPDATE behaviour set $action = '$action_value' where behaviour_id = $behaviour_id " ;  
    $result = $db->updateByQuery($sql);
    echo json_encode($result);
});

$app->post('/uploadreferrals_files', function() use ($app) {
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

$app->post('/uploadreferrals_data', function() use ($app) {
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
    //Broker ID	Designation	Company Name	Mobile No.	Alternative Mobile	Email	Alternative Email	Operates In Area	Operates in City	Address1	Area	Locality / Street	City	Off. State	Country	Comments	Teams	Sub Teams Assigned To	Groups Names Sub Group	Office Phone	Website    
    
    $htmlstring = '<p>Data Not Uploaded ...</p>
    <table class="table table-bordered table-striped" >
        <thead>
            <tr>
                <th>Broker ID</th>
                <th>Company Name</th>
                <th>Designation</th>
                <th>Mobile No</th>
                <th>Alternative Mobile</th>
                <th>Email</th>
                <th>Alternative Email</th>
                
            </tr>
        </thead><tbody>';
    
    
    //'broker_id','designation','company_name','mobile_no','alternative_mobile','email','alternative_email','operates_in_area','operates_in_city','address1','area','locality_street','city','state','country','comments','teams','sub_teams','assigned_to','groups_names','sub_group','office_phone','website'

    for ($row = 1; $row <= $highestRow; ++$row) 
    {
        
        if ($objWorksheet->getCellByColumnAndRow(2, $row)->getValue()=='Company Name')
        {

        }
        else
        {

            $mobile_no = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
            
            $isClientExists = $db->getOneRecord("select 1 from referrals where mobile_no='$mobile_no'");
            if(!$isClientExists)
            {
                
                $count ++;
                $broker_id = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
                $broker_id = str_replace("'","\'",$broker_id);
                $r->convertdata->broker_id=$broker_id;
                $r->convertdata->designation=str_replace("'","\'",$objWorksheet->getCellByColumnAndRow(1, $row)->getValue());
                $company_name = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
                $company_name = str_replace("'","\'",$company_name);
                $r->convertdata->company_name=$company_name;
                $r->convertdata->mobile_no=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
                $r->convertdata->alternative_mobile=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
                $r->convertdata->email=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
                $r->convertdata->alternative_email=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
                $r->convertdata->client_type=$objWorksheet->getCellByColumnAndRow(7, $row)->getValue();

                $r->convertdata->operates_in_area=$objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
                $r->convertdata->operates_in_city=$objWorksheet->getCellByColumnAndRow(9, $row)->getValue();

                $address1 = $objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
                $address1 = str_replace("'","\'",$address1);
                
                $r->convertdata->address1=$address1;
                
                $r->convertdata->area=$objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
                $r->convertdata->locality_street=$objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
                $r->convertdata->city=$objWorksheet->getCellByColumnAndRow(13, $row)->getValue();
                $r->convertdata->state=$objWorksheet->getCellByColumnAndRow(14, $row)->getValue();
                $r->convertdata->country=$objWorksheet->getCellByColumnAndRow(15, $row)->getValue();
                $r->convertdata->comments=$objWorksheet->getCellByColumnAndRow(16, $row)->getValue();
                $r->convertdata->teams=$objWorksheet->getCellByColumnAndRow(17, $row)->getValue();
                $r->convertdata->sub_teams=$objWorksheet->getCellByColumnAndRow(18, $row)->getValue();
                $r->convertdata->assigned_to=$objWorksheet->getCellByColumnAndRow(19, $row)->getValue();
                $r->convertdata->groups_names=$objWorksheet->getCellByColumnAndRow(20, $row)->getValue();
                $r->convertdata->sub_group=$objWorksheet->getCellByColumnAndRow(21, $row)->getValue();
                $r->convertdata->office_phone=$objWorksheet->getCellByColumnAndRow(22, $row)->getValue();
                $r->convertdata->website=$objWorksheet->getCellByColumnAndRow(23, $row)->getValue();
                $tabble_name = "referrals";

                $column_names = array('contact_off','broker_id','designation','company_name','mobile_no','alternative_mobile','email','alternative_email','client_type','operates_in_area','operates_in_city','address1','area','locality_street','city','state','country','comments','teams','sub_teams','assigned_to','groups_names','sub_group','office_phone','website','created_by','created_date');

                $multiple=array("");
                $result = $db->insertIntoTable($r->convertdata, $column_names, $tabble_name, $multiple);
                if ($result!=NULL)
                {
                    $success = true;
                }
            }
            else{

                $broker_id = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
                $broker_id = str_replace("'","\'",$broker_id);
                $r->convertdata->broker_id=$broker_id;
                $r->convertdata->designation=str_replace("'","\'",$objWorksheet->getCellByColumnAndRow(1, $row)->getValue());
                $company_name = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
                $company_name = str_replace("'","\'",$company_name);
                $r->convertdata->company_name=$company_name;
                $r->convertdata->mobile_no=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
                $r->convertdata->alternative_mobile=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
                $r->convertdata->email=$objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
                $r->convertdata->alternative_email=$objWorksheet->getCellByColumnAndRow(6, $row)->getValue();

                $htmlstring .= '<tr>
                                    <td>'.$broker_id.'</td>
                                    <td>'.$company_name.'</td>
                                    <td>'.$designation.'</td>
                                    <td>'.$mobile_no.'</td>
                                    <td>'.$alternative_mobile.'</td>
                                    <td>'.$email.'</td>
                                    <td>'.$alternative_email.'</td>
                                </tr>';

            }
        }
    }

    $htmlstring  .= '</tbody>
    </table>';
           
    if ($success)
    {
        $response["status"] = "success";
        $response["message"] = "Referrals Data uploaded [Total Records - ".$count."] !! ";
        $response["htmlstring"] = $htmlstring;
        echoResponse(201, $response);
    }
    else
    {
        $response["status"] = "error";
        $response["message"] = "Referrals Data not uploaded, Please Check Excel file !! ";
        $response["htmlstring"] = "<p>ERROR</p>";
        echoResponse(201, $response);
    }
});



$app->get('/create_otp', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];
    $username = $session['username'];
    $referral_otp_created = date('Y-m-d H:i:s');

    $sql = "SELECT TIMESTAMPDIFF(MINUTE, referral_otp_created, '$referral_otp_created') as duration FROM users WHERE user_id = $user_id and referral_otp_created != '0000-00-00 00:00:00' "; 
    
    $otpdata = $db->getAllRecords($sql);
    $duration = 0;
    if ($otpdata)
    {
        $duration = $otpdata[0]['duration'];
    }
    $result=0;
    $response = array();
    $role = $session['role'];
    if (in_array("Admin", $role))
    {
        $response["status"] = "success";
        $response["message"] = "";
    }
    else{
        
        if ($duration>=30 || $duration==0)
        {
            $otp = substr(str_shuffle("0123456789"), 0, 4); 
            $sql = "UPDATE users set referral_otp = $otp, referral_otp_created = '$referral_otp_created' WHERE user_id = $user_id " ; 
            
            $result = $db->updateByQuery($sql);

            $message = "One time Password to access For referral data is ".$otp." for ".$username;

            $myemail = "crm@rdbrothers.com";
            $toemail = "crm@rdbrothers.com";
            $cc_mail_id  = "dhaval@rdbrothers.com, rajat@reyank.com";
            $email_subject = "OTP to access referral data for ".$username." is ".$otp;
            $email_body = "<html><body>".$message."</html></body>";
        
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
        
            $nmessage .= "--".$uid."--";
        
            if(!mail($toemail,$email_subject,$nmessage,$headers))
            {
                $response["status"] = "error";
                $response["message"] = "Mail not Sent ...!";
            }
            else
            {
                $response["status"] = "success";
                $response["message"] = "You will receive OTP on your mobile or on email account...!";
            }
        }
        else
        {
                $response["status"] = "error";
                $response["message"] = "Use Previous OTP ...!";
        }
    }
    echoResponse(201, $response);
   
});

$app->get('/verify_otp/:otp', function($otp) use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $user_id = $session['user_id'];
    $time_now  = date('Y-m-d H:i:s');
    $role = $session['role'];
    
    $sql = "SELECT referral_otp, referral_otp_created FROM users WHERE user_id = $user_id"; 
    $otpdata = $db->getAllRecords($sql);
    if ($otpdata)
    {
        $org_otp = $otpdata[0]['referral_otp'];
        $org_referral_otp_created = $otpdata[0]['referral_otp_created'];
    }
    
    if ($org_otp == $otp  || in_array("Admin", $role))
    {
        $response["status"] = "success";
        $response["message"] = "Otp Verified";
    }
    else
    {
        $response["status"] = "error";
        $response["message"] = "Otp Not Matching, try again ...";
    }
    echoResponse(201, $response);
});


$app->get('/create_propertydata_otp', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $user_id = $session['user_id'];
    $username = $session['username'];
    $referral_propertydata_otp_created = date('Y-m-d H:i:s');

    $sql = "SELECT TIMESTAMPDIFF(MINUTE, referral_propertydata_otp_created, '$referral_propertydata_otp_created') as duration FROM users WHERE user_id = $user_id and referral_propertydata_otp_created != '0000-00-00 00:00:00' "; 
    
    $otpdata = $db->getAllRecords($sql);
    $duration = 0;
    if ($otpdata)
    {
        $duration = $otpdata[0]['duration'];
    }
    $result=0;
    $response = array();
    $role = $session['role'];
    if (in_array("Admin", $role))
    {
        $response["status"] = "success";
        $response["message"] = "";
    }
    else{
        
        if ($duration>=30 || $duration==0)
        {
            $otp = substr(str_shuffle("0123456789"), 0, 4); 
            $sql = "UPDATE users set referral_propertydata_otp = $otp, referral_propertydata_otp_created = '$referral_propertydata_otp_created' WHERE user_id = $user_id " ; 
            
            $result = $db->updateByQuery($sql);

            $message = "One time Password to access For referral data is ".$otp." for ".$username;

            $myemail = "crm@rdbrothers.com";
            $toemail = "crm@rdbrothers.com";
            $cc_mail_id  = "dhaval@rdbrothers.com";
            $email_subject = "OTP to access referral data for ".$username." is ".$otp;
            $email_body = "<html><body>".$message."</html></body>";
        
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
        
            $nmessage .= "--".$uid."--";
        
            if(!mail($toemail,$email_subject,$nmessage,$headers))
            {
                $response["status"] = "error";
                $response["message"] = "Mail not Sent ...!";
            }
            else
            {
                $response["status"] = "success";
                $response["message"] = "You will receive OTP on your mobile or on email account...!";
            }
        }
        else
        {
                $response["status"] = "error";
                $response["message"] = "Use Previous OTP ...!";
        }
    }
    echoResponse(201, $response);
   
});

$app->get('/verify_propertydata_otp/:otp', function($otp) use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    if ($session['username']=="Guest")
    {
        return;
    }
    $response = array();
    $user_id = $session['user_id'];
    $time_now  = date('Y-m-d H:i:s');
    $role = $session['role'];
    
    $sql = "SELECT referral_propertydata_otp, referral_propertydata_otp_created FROM users WHERE user_id = $user_id"; 
    $otpdata = $db->getAllRecords($sql);
    if ($otpdata)
    {
        $org_otp = $otpdata[0]['referral_propertydata_otp'];
        $org_referral_otp_created = $otpdata[0]['referral_propertydata_otp_created'];
    }
    
    if ($org_otp == $otp  || in_array("Admin", $role))
    {
        $response["status"] = "success";
        $response["message"] = "Otp Verified";
    }
    else
    {
        $response["status"] = "error";
        $response["message"] = "Otp Not Matching, try again ...";
    }
    echoResponse(201, $response);
});

?>