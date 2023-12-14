var app = angular.module("myApp", ['ngRoute', 'ngAnimate', 'toaster', 'ui.bootstrap' ,'thatisuday.ng-image-gallery', "chart.js"]); //,'oi.select'

app.config(['$locationProvider', function($locationProvider) {
  $locationProvider.hashPrefix('');
}]);

app.config(function ($routeProvider) {
    $routeProvider
    
    //
    // Home Page route
    //	       
    
        // Login & Signup  ?nd=" + Date.now()
        .when("/login", { controller: 'authCtrl', templateUrl: "pages/login.html?nd=" + Date.now() })
        .when("/newlogin", { controller: 'authCtrl', templateUrl: "pages/newlogin.html?nd=" + Date.now() })

        .when("/forgetpassword", { controller: 'ForgetPassword', templateUrl: "pages/admin/forgetpassword.html?nd=" + Date.now() })
        .when('/logout', { controller: 'logoutCtrl', templateUrl: 'pages/index.html?nd=' + Date.now()})

        // Home Menu
        .when("/", { controller: HomeCtrl, templateUrl: "pages/login.html?nd=" + Date.now() })
        .when("/home", { controller: HomeCtrl, templateUrl: "pages/home.html" , label: 'Home'})
        .when("/contact_us", { controller:'Contact_us_Ctrl', templateUrl: "pages/contact_us.html" })
        
        .when("/ppt", { controller: 'Ppt', templateUrl: "pages/admin/ppt.html?nd=" + Date.now()})

        
        // Dashboards
        .when("/admin", { controller: 'UserDashboard', templateUrl: "pages/user/dashboard.html?nd=" + Date.now() ,label: 'Dashboard' })
        .when("/manager", { controller: 'ManagerDashboard', templateUrl: "pages/manager/dashboard.html?nd=" + Date.now() ,label: 'Dashboard' })
        .when("/user", { controller: 'UserDashboard', templateUrl: "pages/user/dashboard.html?nd=" + Date.now(),label: 'Dashboard' })

        .when("/goals_achieved", { controller: 'Goals_Achieved', templateUrl: "pages/admin/goals_achieved.html?nd=" + Date.now(),label: 'Dashboard' })

        .when("/crm_activities", { controller: 'CRM_Activities', templateUrl: "pages/admin/crm_activities.html?nd=" + Date.now(),label: 'Dashboard' })

        .when("/activity_log", { controller: 'Activity_Log', templateUrl: "pages/admin/activity_log.html?nd=" + Date.now(),label: 'Dashboard' })


        .when("/dashboardmore/:goal_id", { controller: 'UserDashboardMore', templateUrl: "pages/user/dashboardmore.html?nd=" + Date.now(),label: 'Dashboard' })

        .when("/admindashboardmore/:user_id/:start_date/:end_date/:goal_category/:goal_sub_category", { controller: 'AdminDashboardMore', templateUrl: "pages/admin/admindashboardmore.html?nd=" + Date.now(),label: 'Dashboard' })
        
        .when("/mdashboard", { controller: 'MDashboard', templateUrl: "pages/admin/mdashboard.html?nd=" + Date.now() })

        .when("/setup", { controller: 'Setup', templateUrl: "pages/admin/setup.html?nd=" + Date.now() })
    
        // MAIL TEMPLATE
        .when("/mail_template_add", { controller:'Mail_Template_Add_Ctrl', templateUrl: 'pages/admin/mail_template_add.html?nd=' + Date.now() })
        .when("/mail_template_edit/:mail_template_id", { controller:'Mail_Template_Edit_Ctrl', templateUrl: 'pages/admin/mail_template_edit.html?nd=' + Date.now() })
        .when('/mail_template_list', { controller:'Mail_Template_List_Ctrl', templateUrl: 'pages/admin/mail_template_list.html?nd=' + Date.now() })

        // RESUMES
        .when("/resume_add", { controller:'Resume_Add_Ctrl', templateUrl: 'pages/admin/resume_add.html?nd=' + Date.now() })
        .when("/resume_edit/:resume_id", { controller:'Resume_Edit_Ctrl', templateUrl: 'pages/admin/resume_edit.html?nd=' + Date.now() })
        .when('/resume_list', { controller:'Resume_List_Ctrl', templateUrl: 'pages/admin/resume_list.html?nd=' + Date.now() })


        // SMS TEMPLATE
        .when("/sms_template_add", { controller:'SMS_Template_Add_Ctrl', templateUrl: 'pages/admin/sms_template_add.html?nd=' + Date.now() })
        .when("/sms_template_edit/:sms_template_id", { controller:'SMS_Template_Edit_Ctrl', templateUrl: 'pages/admin/sms_template_edit.html?nd=' + Date.now() })
        .when('/sms_template_list', { controller:'SMS_Template_List_Ctrl', templateUrl: 'pages/admin/sms_template_list.html?nd=' + Date.now() })

        .when('/sms_sent_list', { controller:'SMS_Sent_List_Ctrl', templateUrl: 'pages/admin/sms_sent_list.html?nd=' + Date.now() })
       

        // MAILS
        .when('/mails_client/:category/:category_id', { controller:'Mails_Client', templateUrl: 'pages/admin/mails_client.html?nd='+ Date.now() })

        .when('/email_sent_list', { controller:'Email_Sent_List_Ctrl', templateUrl: 'pages/admin/email_sent_list.html?nd=' + Date.now() })

        // SMS
        .when('/sms_client/:category/:category_id', { controller:'SMS_Client', templateUrl: 'pages/admin/sms_client.html?nd='+ Date.now() })

        
        // City
        .when("/city_add", { controller:'City_Add_Ctrl', templateUrl: "pages/admin/city_add.html?nd=" + Date.now() })
        .when("/city_edit/:city_id", { controller:'City_Edit_Ctrl', templateUrl: "pages/admin/city_edit.html?nd=" + Date.now()    })
        .when("/city_list", { controller:'City_List_Ctrl', templateUrl: "pages/admin/city_list.html?nd=" + Date.now() })
    
        // Country
        .when("/country_add", { controller:'Country_Add_Ctrl', templateUrl: "pages/admin/country_add.html?nd=" + Date.now() })
        .when("/country_edit/:country_id", { controller:'Country_Edit_Ctrl', templateUrl: "pages/admin/country_edit.html?nd=" + Date.now() })
        .when("/country_list", { controller:'Country_List_Ctrl', templateUrl: "pages/admin/country_list.html?nd=" + Date.now() })
    
        // AREAS
        .when("/area_add", { controller:'Area_Add_Ctrl', templateUrl: "pages/admin/area_add.html?nd=" + Date.now()})
        .when("/area_edit/:area_id", { controller:'Area_Edit_Ctrl', templateUrl: "pages/admin/area_edit.html?nd=" + Date.now() })
        .when("/area_list", { controller:'Area_List_Ctrl', templateUrl: "pages/admin/area_list.html?nd=" + Date.now() })
    
        // LOCALITY
        .when("/locality_add", { controller:'Locality_Add_Ctrl', templateUrl: "pages/admin/locality_add.html?nd=" + Date.now()})
        .when("/locality_edit/:locality_id", { controller:'Locality_Edit_Ctrl', templateUrl: "pages/admin/locality_edit.html?nd=" + Date.now() })
        .when("/locality_list", { controller:'Locality_List_Ctrl', templateUrl: "pages/admin/locality_list.html?nd=" + Date.now() })
    
        // GROUPS
        .when("/group_add", { controller:'Group_Add_Ctrl', templateUrl: "pages/admin/group_add.html?nd=" + Date.now()})
        .when("/group_edit/:group_id", { controller:'Group_Edit_Ctrl', templateUrl: "pages/admin/group_edit.html?nd=" + Date.now() })
        .when("/group_list", { controller:'Group_List_Ctrl', templateUrl: "pages/admin/group_list.html?nd=" + Date.now() })
        
        // TEAMS
        .when("/teams_add", { controller:'Teams_Add_Ctrl', templateUrl: "pages/admin/team_add.html?nd=" + Date.now()})
        .when("/teams_edit/:team_id", { controller:'Teams_Edit_Ctrl', templateUrl: "pages/admin/team_edit.html?nd=" + Date.now() })
        .when("/teams_list", { controller:'Teams_List_Ctrl', templateUrl: "pages/admin/team_list.html?nd=" + Date.now() })

        // SUB TEAMS
        .when("/sub_teams_add", { controller:'Sub_Teams_Add_Ctrl', templateUrl: "pages/admin/sub_team_add.html?nd=" + Date.now()})
        .when("/sub_teams_edit/:sub_team_id", { controller:'Sub_Teams_Edit_Ctrl', templateUrl: "pages/admin/sub_team_edit.html?nd=" + Date.now() })
        .when("/sub_teams_list", { controller:'Sub_Teams_List_Ctrl', templateUrl: "pages/admin/sub_team_list.html?nd=" + Date.now() })


        // BRANCH OFFICE
        .when("/branch_office_add", { controller:'Branch_Office_Add_Ctrl', templateUrl: "pages/admin/branch_office_add.html?nd=" + Date.now()})
        .when("/branch_office_edit/:bo_id", { controller:'Branch_Office_Edit_Ctrl', templateUrl: "pages/admin/branch_office_edit.html?nd=" + Date.now() })
        .when("/branch_office_list", { controller:'Branch_Office_List_Ctrl', templateUrl: "pages/admin/branch_office_list.html?nd=" + Date.now() })
    
        // DESIGNATION
        .when("/designation_add", { controller:'Designation_Add_Ctrl', templateUrl: "pages/admin/designation_add.html?nd=" + Date.now()})
        .when("/designation_edit/:designation_id", { controller:'Designation_Edit_Ctrl', templateUrl: "pages/admin/designation_edit.html?nd=" + Date.now() })
        .when("/designation_list", { controller:'Designation_List_Ctrl', templateUrl: "pages/admin/designation_list.html?nd=" + Date.now() })
    
        // State
        .when("/state_add", { controller:'State_Add_Ctrl', templateUrl: "pages/admin/state_add.html?nd=" + Date.now() })
        .when("/state_edit/:state_id", { controller:'State_Edit_Ctrl', templateUrl: "pages/admin/state_edit.html?nd=" + Date.now()})
        .when("/state_list", { controller:'State_List_Ctrl', templateUrl: "pages/admin/state_list.html?nd=" + Date.now() })
    
        // User Rights
        .when("/user_rights", { controller:'User_Rights_Ctrl', templateUrl: "pages/admin/user_rights.html?nd=" + Date.now() })
  
        // EMPLOYEES
        
        .when("/employee_add", { controller:'Employee_Add_Ctrl', templateUrl: 'pages/admin/employee_add.html?nd=' + Date.now() })
        .when("/employee_edit/:emp_id", { controller:'Employee_Edit_Ctrl', templateUrl: 'pages/admin/employee_edit.html?nd=' + Date.now() })
        .when('/employee_list', { controller:'Employee_List_Ctrl', templateUrl: 'pages/admin/employee_list.html?nd=' + Date.now() })

        // EMPLOYEES ASSET

        .when("/employee_asset_add", { controller:'Employee_Asset_Add_Ctrl', templateUrl: 'pages/admin/employee_asset_add.html?nd=' + Date.now() })
        .when("/employee_asset_edit/:employee_asset_id", { controller:'Employee_Asset_Edit_Ctrl', templateUrl: 'pages/admin/employee_asset_edit.html?nd=' + Date.now() })
        .when('/employee_asset_list', { controller:'Employee_Asset_List_Ctrl', templateUrl: 'pages/admin/employee_asset_list.html?nd=' + Date.now() })

        // EMPLOYEES LEAVE

        .when("/employee_leave_add", { controller:'Employee_Leave_Add_Ctrl', templateUrl: 'pages/admin/employee_leave_add.html?nd=' + Date.now() })
        .when("/employee_leave_edit/:employee_leave_id", { controller:'Employee_Leave_Edit_Ctrl', templateUrl: 'pages/admin/employee_leave_edit.html?nd=' + Date.now() })
        .when('/employee_leave_list', { controller:'Employee_Leave_List_Ctrl', templateUrl: 'pages/admin/employee_leave_list.html?nd=' + Date.now() })


        
        
        // Users
        .when("/user_add", { controller:'User_Add_Ctrl', templateUrl: 'pages/admin/user_add.html?nd=' + Date.now() })
        .when("/user_edit/:user_id", { controller:'User_Edit_Ctrl', templateUrl: 'pages/admin/user_edit.html?nd=' + Date.now() })
        .when('/user_list', { controller:'User_List_Ctrl', templateUrl: 'pages/admin/user_list.html?nd=' + Date.now() })

        // MY PROFILE
        .when("/myprofile", { controller:'Myprofile_Ctrl', templateUrl: 'pages/admin/myprofile.html?nd=' + Date.now() })

  
        // ROLES
        .when("/role_add", { controller:'Role_Add_Ctrl', templateUrl: 'pages/admin/role_add.html?nd=' + Date.now() })
        .when("/role_edit/:role_id", { controller:'Role_Edit_Ctrl', templateUrl: 'pages/admin/role_edit.html?nd=' + Date.now() })
        .when('/role_list', { controller:'Role_List_Ctrl', templateUrl: 'pages/admin/role_list.html?nd=' + Date.now() })
  
        // PERMISSIONS
        .when("/permission_add", { controller:'Permission_Add_Ctrl', templateUrl: 'pages/admin/permission_add.html?nd=' + Date.now() })
        .when("/permission_edit/:permission_id", { controller:'Permission_Edit_Ctrl', templateUrl: 'pages/admin/permission_edit.html?nd=' + Date.now() })
        .when('/permission_list', { controller:'Permission_List_Ctrl', templateUrl: 'pages/admin/permission_list.html?nd=' + Date.now() })

        // CHANGE PASSWORD
        .when('/change_password', { controller:'Change_Password', templateUrl: 'pages/admin/change_password.html?nd=' + Date.now() })

        // EXPORT CONFIGURATIONS
        .when('/exportconfig', { controller:'Export_Config', templateUrl: 'pages/admin/exportconfig.html?nd=' + Date.now() })
        
        // PROJECTS
        .when("/project_add", { controller:'Project_Add_Ctrl', templateUrl: 'pages/admin/project_add.html?nd=' + Date.now() })
        .when("/project_edit/:project_id", { controller:'Project_Edit_Ctrl', templateUrl: 'pages/admin/project_edit.html?nd=' + Date.now() })
        .when('/project_list/:id', { controller:'Project_List_Ctrl', templateUrl: 'pages/admin/project_list.html?nd=' + Date.now() })
  
        // PROPERTIES
        .when("/properties_add/:cat", { controller:'Properties_Add_Ctrl', templateUrl: 'pages/admin/properties_add.html?nd=' + Date.now() })
        .when("/properties_edit/:property_id", { controller:'Properties_Edit_Ctrl', templateUrl: 'pages/admin/properties_edit.html?nd=' + Date.now() })
        .when('/properties_list/:cat/:id/:data', { controller:'Properties_List_Ctrl', templateUrl: 'pages/admin/properties_list.html?nd=' + Date.now() })


        .when('/batch_update/:module_name/:cat/:id/:data', { controller:'Batch_Update_Ctrl', templateUrl: 'pages/admin/batch_update.html?nd=' + Date.now() })

        .when('/test/:cat/:id', { controller:'Test_Ctrl', templateUrl: 'pages/admin/test_list.html?nd=' + Date.now() })
        
        // ENQUIRIES
        .when("/enquiries_add/:cat", { controller:'Enquiries_Add_Ctrl', templateUrl: 'pages/admin/enquiries_add.html?nd=' + Date.now() })
        .when("/enquiries_edit/:enquiry_id", { controller:'Enquiries_Edit_Ctrl', templateUrl: 'pages/admin/enquiries_edit.html?nd=' + Date.now() })
        .when('/enquiries_list/:cat/:id/:data', { controller:'Enquiries_List_Ctrl', templateUrl: 'pages/admin/enquiries_list.html?nd=' + Date.now() })

        .when('/mis_enquiries', { controller:'MISEnquiries_Ctrl', templateUrl: 'pages/admin/misenquiries_list.html?nd=' + Date.now() })

        // GOALS

        .when("/goals_add", { controller:'Goals_Add_Ctrl', templateUrl: "pages/admin/goals_add.html?nd=" + Date.now() })
        .when("/goals_edit/:goal_id", { controller:'Goals_Edit_Ctrl', templateUrl: "pages/admin/goals_edit.html?nd=" + Date.now()})
        .when('/goals_list', { controller:'Goals_List_Ctrl', templateUrl: 'pages/admin/goals_list.html?nd=' + Date.now() })

        .when('/goals_report/:category', { controller:'Goals_Report_Ctrl', templateUrl: 'pages/admin/goals_report.html?nd=' + Date.now() })

        //Behaviour
        
        .when('/behaviour_list', { controller:'Behaviour_List_Ctrl', templateUrl: 'pages/admin/behaviour_list.html?nd=' + Date.now() })

        //SOCIAL MEDIA
        .when("/social_media_add", { controller:'Social_Media_Add_Ctrl', templateUrl: "pages/admin/social_media_add.html?nd=" + Date.now() })
        .when('/social_media_list', { controller:'Social_Media_List_Ctrl', templateUrl: 'pages/admin/social_media_list.html?nd=' + Date.now() })
        .when("/social_media_edit/:social_media_id", { controller:'Social_Media_Edit_Ctrl', templateUrl: 'pages/admin/social_media_edit.html?nd=' + Date.now() })

        // 99 ACRES
        
        .when('/99acres_list', { controller:'99Acres_List_Ctrl', templateUrl: 'pages/admin/99acres_list.html?nd=' + Date.now() })


        // //Website_leade 
        // .when('/weblead_list', { controller:'weblead_List_Ctrl', templateUrl: 'pages/admin/weblead_list.html?nd=' + Date.now() })
        
        //Website_leade 
        .when('/weblead_list', { controller:'Weblead_List_Ctrl', templateUrl: 'pages/admin/weblead_list.html?nd=' + Date.now() })
        

        // EMAIL ACCOUNTS

        .when("/email_accounts_add", { controller:'Email_Accounts_Add_Ctrl', templateUrl: "pages/admin/email_accounts_add.html?nd=" + Date.now() })
        .when("/email_accounts_edit/:email_accounts_id", { controller:'Email_Accounts_Edit_Ctrl', templateUrl: "pages/admin/email_accounts_edit.html?nd=" + Date.now()})
        .when('/email_accounts_list', { controller:'Email_Accounts_List_Ctrl', templateUrl: 'pages/admin/email_accounts_list.html?nd=' + Date.now() })


        // CONTACTS
        .when("/contacts_add/:cat", { controller:'Contacts_Add_Ctrl', templateUrl: 'pages/admin/contacts_add.html?nd=' + Date.now() })
        .when("/contacts_edit/:contact_id", { controller:'Contacts_Edit_Ctrl', templateUrl: 'pages/admin/contacts_edit.html?nd=' + Date.now() })
        .when('/contacts_list/:cat', { controller:'Contacts_List_Ctrl', templateUrl: 'pages/admin/contacts_list.html?nd=' + Date.now() })
  
        // AGREEMENTS
       .when("/agreement_add/:category/:id", { controller:'Agreement_Add_Ctrl', templateUrl: 'pages/admin/agreement_add.html?nd=' + Date.now() })
        .when("/agreement_edit/:agreement_id", { controller:'Agreement_Edit_Ctrl', templateUrl: 'pages/admin/agreement_edit.html?nd=' + Date.now() })
        .when('/agreement_list/:cat', { controller:'Agreement_List_Ctrl', templateUrl: 'pages/admin/agreement_list.html?nd=' + Date.now() })
  
        .when("/payments_edit/:payments_id", { controller:'Payments_Edit_Ctrl', templateUrl: 'pages/admin/payments_edit.html?nd=' + Date.now() })

        .when("/contributions_edit/:agreement_details_id", { controller:'Contributions_Edit_Ctrl', templateUrl: 'pages/admin/contributions_edit.html?nd=' + Date.now() })

        // ACCOUNTS
        .when("/account_add", { controller:'Account_Add_Ctrl', templateUrl: 'pages/admin/account_add.html?nd=' + Date.now() })
        .when("/account_edit/:account_id", { controller:'Account_Edit_Ctrl', templateUrl: 'pages/admin/account_edit.html?nd=' + Date.now() })
        .when('/account_list', { controller:'Account_List_Ctrl', templateUrl: 'pages/admin/account_list.html?nd=' + Date.now() })

        // VOUCHERS 
        .when("/voucher_add", { controller:'Voucher_Add_Ctrl', templateUrl: 'pages/admin/voucher_add.html?nd=' + Date.now() })
        .when("/voucher_edit/:voucher_id", { controller:'Voucher_Edit_Ctrl', templateUrl: 'pages/admin/voucher_edit.html?nd=' + Date.now() })
        .when('/voucher_list', { controller:'Voucher_List_Ctrl', templateUrl: 'pages/admin/voucher_list.html?nd=' + Date.now() })

        // EXPENSES
        .when("/voucher_add", { controller:'Voucher_Add_Ctrl', templateUrl: 'pages/admin/voucher_add.html?nd=' + Date.now() })
        .when("/voucher_edit/:voucher_id", { controller:'Voucher_Edit_Ctrl', templateUrl: 'pages/admin/voucher_edit.html?nd=' + Date.now() })
        .when('/voucher_list', { controller:'Voucher_List_Ctrl', templateUrl: 'pages/admin/voucher_list.html?nd=' + Date.now() })

        
        .when('/contributions', { controller:'Contributions_Ctrl', templateUrl: 'pages/admin/contributions.html?nd=' + Date.now() })


        //  EXPENSE HEAD Sub Type pks-14-10-2023
        .when("/expense_head_add_sub_type", { controller:'Expense_Head_Sub_Type_Add_Ctrl', templateUrl: 'pages/admin/expense_head_add_sub_type.html?nd=' + Date.now() })
        .when("/expense_head_edit_sub_type/:expense_head_sub_type_id", { controller:'Expense_Head_Sub_Type_Edit_Ctrl', templateUrl: 'pages/admin/expense_head_edit_sub_type.html?nd=' + Date.now() })
        .when("/expense_head_sub_type_list", { controller:'Expense_Head_Sub_Type_List_Ctrl', templateUrl: 'pages/admin/expense_head_sub_type_list.html?nd=' + Date.now() })
        
        
        
        // EXPENSE HEAD
        .when("/expense_head_add", { controller:'Expense_Head_Add_Ctrl', templateUrl: 'pages/admin/expense_head_add.html?nd=' + Date.now() })
        .when("/expense_head_edit/:expense_head_id", { controller:'Expense_Head_Edit_Ctrl', templateUrl: 'pages/admin/expense_head_edit.html?nd=' + Date.now() })
        .when('/expense_head_list', { controller:'Expense_Head_List_Ctrl', templateUrl: 'pages/admin/expense_head_list.html?nd=' + Date.now() })


        // EXPENSES
        .when("/expense_add", { controller:'Expense_Add_Ctrl', templateUrl: 'pages/admin/expense_add.html?nd=' + Date.now() })
        .when("/expense_edit/:expense_id", { controller:'Expense_Edit_Ctrl', templateUrl: 'pages/admin/expense_edit.html?nd=' + Date.now() })
        .when('/expense_list', { controller:'Expense_List_Ctrl', templateUrl: 'pages/admin/expense_list.html?nd=' + Date.now() })

        .when("/expense_report", { controller: 'Expense_Report', templateUrl: "pages/admin/expense_report.html?nd=" + Date.now(),label: 'Dashboard' })


        .when('/tp', { controller:'tp', templateUrl: 'pages/admin/tp.html?nd=' + Date.now() })

        // FRANCHISEE REPORTS

        .when("/franchisee_report", { controller: 'Franchisee_Report', templateUrl: "pages/admin/franchisee_report.html?nd=" + Date.now(),label: 'Dashboard' })


        // ELIGIBILITY
        .when("/eligibility_add", { controller:'Eligibility_Add_Ctrl', templateUrl: 'pages/admin/eligibility_add.html?nd=' + Date.now() })
        .when("/eligibility_edit/:eligibility_id", { controller:'Eligibility_Edit_Ctrl', templateUrl: 'pages/admin/eligibility_edit.html?nd=' + Date.now() })
        .when('/eligibility_list', { controller:'Eligibility_List_Ctrl', templateUrl: 'pages/admin/eligibility_list.html?nd=' + Date.now() })



        // ACTIVITIES
       /* .when("/activity_add", { controller:'Activity_Add_Ctrl', templateUrl: 'pages/admin/activity_add.html?nd=' + Date.now() })*/
        .when("/activity_add/:category/:id", { controller:'Activity_Add_Ctrl', templateUrl: 'pages/admin/activity_add.html?nd=' + Date.now() })
        .when("/activity_edit/:activity_id", { controller:'Activity_Edit_Ctrl', templateUrl: 'pages/admin/activity_edit.html?nd=' + Date.now() })
        .when('/activity_list/:cat/:id', { controller:'Activity_List_Ctrl', templateUrl: 'pages/admin/activity_list.html?nd=' + Date.now() })

        // TASKS

       .when("/task_add", { controller:'Task_Add_Ctrl', templateUrl: 'pages/admin/task_add.html?nd=' + Date.now() })
       .when("/task_edit/:task_id", { controller:'Task_Edit_Ctrl', templateUrl: 'pages/admin/task_edit.html?nd=' + Date.now() })
       .when('/task_list', { controller:'Task_List_Ctrl', templateUrl: 'pages/admin/task_list.html?nd=' + Date.now() })

       // REFERRALS

       .when('/referrals_list/:cat', { controller:'Referrals_List_Ctrl', templateUrl: 'pages/admin/referrals_list.html?nd=' + Date.now() })  

       .when('/referrals_property', { controller:'Referrals_Property_Ctrl', templateUrl: 'pages/admin/referrals_property.html?nd=' + Date.now() })  
      

       .when('/getcallingdata', { controller:'GetCallingData_List', templateUrl: 'pages/admin/callingdata.html?nd=' + Date.now() })
       .when('/callinghistory', { controller:'CallingHistory_List', templateUrl: 'pages/admin/callinghistory.html?nd=' + Date.now() })  

        // AUDIT TRAIL

        .when("/audit_trail/:module_name/:id/:data", { controller:'Audit_Trail', templateUrl: 'pages/admin/audit_trail.html?nd=' + Date.now() })

        // REPORT

        //.when("/reports/:module_name/:id/:data", { controller:'Reports', templateUrl: 'pages/admin/reports.html?nd=' + Date.now() })

        .when("/reports/:module_name/:id/:data", { controller:'CreateReports', templateUrl: 'pages/admin/createreports.html?nd=' + Date.now() })

        .when("/reports_project/:module_name/:id/:data", { controller:'CreateReportsProject', templateUrl: 'pages/admin/createreportsproject.html?nd=' + Date.now() })

        .when("/send_broucher/:module_name/:id/:data", { controller:'SendBroucher', templateUrl: 'pages/admin/send_broucher.html?nd=' + Date.now() })

        .when("/one_mailer/:module_name/:id/:data", { controller:'OneMailer', templateUrl: 'pages/admin/one_mailer.html?nd=' + Date.now() })


        .when("/project_one_mailer/:module_name/:id/:data", { controller:'ProjectOneMailer', templateUrl: 'pages/admin/project_one_mailer.html?nd=' + Date.now() })

        .when("/multiproperty/:module_name/:id/:data", { controller:'MultiProperty', templateUrl: 'pages/admin/multiproperty.html?nd=' + Date.now() })

        .when("/preleased/:module_name/:id/:data", { controller:'PreLeased', templateUrl: 'pages/admin/preleased.html?nd=' + Date.now() })



        // DOCUMENTS 
        .when("/document_add", { controller:'Document_Add_Ctrl', templateUrl: 'pages/admin/document_add.html?nd=' + Date.now() }) 
        .when("/document_edit/:document_id", { controller:'Document_Edit_Ctrl', templateUrl: 'pages/admin/document_edit.html?nd=' + Date.now() }) 
        .when('/document_list', { controller:'Document_List_Ctrl', templateUrl: 'pages/admin/document_list.html?nd=' + Date.now() })

        .when('/showdocuments', { controller:'Showdocuments', templateUrl: 'pages/admin/showdocuments.html?nd=' + Date.now() })

        .when('/searchall', { controller:'SearchAll', templateUrl: 'pages/admin/searchall.html?nd=' + Date.now() })
  
        // ALERTS
        .when("/alerts_add", { controller:'Alerts_Add_Ctrl', templateUrl: 'pages/admin/alerts_add.html?nd=' + Date.now() })
        .when("/alerts_edit/:alerts_id", { controller:'Alerts_Edit_Ctrl', templateUrl: 'pages/admin/alerts_edit.html?nd=' + Date.now() })
        .when('/alerts_list', { controller:'Alerts_List_Ctrl', templateUrl: 'pages/admin/alerts_list.html?nd=' + Date.now() })

        .when('/showalerts_list', { controller:'ShowAlerts_List_Ctrl', templateUrl: 'pages/admin/showalerts.html?nd=' + Date.now() })

        
        // COLLECTIONS

        .when('/collections', { controller:'Collections_Ctrl', templateUrl: 'pages/admin/collections.html?nd=' + Date.now() })

        // ATTENDANCE

        .when('/attendance', { controller:'Attendance_Ctrl', templateUrl: 'pages/admin/attendance.html?nd=' + Date.now() })
        .when('/daily_attendance', { controller:'DailyAttendance_Ctrl', templateUrl: 'pages/admin/dailyattendance.html?nd=' + Date.now() })
        .when('/holidays_list', { controller:'Holidays_Ctrl', templateUrl: 'pages/admin/holidays.html?nd=' + Date.now() })


        .when('/assist_readmore', { controller:'Assist_Readmore', templateUrl: 'pages/admin/assist_readmore.html?nd=' + Date.now() })


        // DROPDOWNS
  
        .when("/dropdowns_add", { controller:'Dropdowns_Add_Ctrl', templateUrl: 'pages/admin/dropdowns_add.html?nd=' + Date.now() })
        .when("/dropdowns_edit/:dropdowns_id", { controller:'Dropdowns_Edit_Ctrl', templateUrl: 'pages/admin/dropdowns_edit.html?nd=' + Date.now() })
        .when('/dropdowns_list', { controller:'Dropdowns_List_Ctrl', templateUrl: 'pages/admin/dropdowns_list.html?nd=' + Date.now() })
          

        // GENERATE FORM

        .when("/generate_form", { controller:'Generate_Form', templateUrl: 'pages/admin/genform.html?nd=' + Date.now() })

        .otherwise({ redirectTo: '/login' });

});

app.filter('unsafe', function($sce) { return $sce.trustAsHtml; });

/*app.filter('convertToWord', function() {
    return function(amount) {
        var words = new Array();
        words[0] = '';
        words[1] = 'One';
        words[2] = 'Two';
        words[3] = 'Three';
        words[4] = 'Four';
        words[5] = 'Five';
        words[6] = 'Six';
        words[7] = 'Seven';
        words[8] = 'Eight';
        words[9] = 'Nine';
        words[10] = 'Ten';
        words[11] = 'Eleven';
        words[12] = 'Twelve';
        words[13] = 'Thirteen';
        words[14] = 'Fourteen';
        words[15] = 'Fifteen';
        words[16] = 'Sixteen';
        words[17] = 'Seventeen';
        words[18] = 'Eighteen';
        words[19] = 'Nineteen';
        words[20] = 'Twenty';
        words[30] = 'Thirty';
        words[40] = 'Forty';
        words[50] = 'Fifty';
        words[60] = 'Sixty';
        words[70] = 'Seventy';
        words[80] = 'Eighty';
        words[90] = 'Ninety';
        amount = amount.toString();
        var atemp = amount.split(".");
        var number = atemp[0].split(",").join("");
        var n_length = number.length;
        var words_string = "";
        if (n_length <= 9) {
            var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
            var received_n_array = new Array();
            for (var i = 0; i < n_length; i++) {
                received_n_array[i] = number.substr(i, 1);
            }
            for (var i = 9 - n_length, j = 0; i < 9; i++, j++) {
                n_array[i] = received_n_array[j];
            }
            for (var i = 0, j = 1; i < 9; i++, j++) {
                if (i == 0 || i == 2 || i == 4 || i == 7) {
                    if (n_array[i] == 1) {
                        n_array[j] = 10 + parseInt(n_array[j]);
                        n_array[i] = 0;
                    }
                }
            }
            value = "";
            for (var i = 0; i < 9; i++) {
                if (i == 0 || i == 2 || i == 4 || i == 7) {
                    value = n_array[i] * 10;
                } else {
                    value = n_array[i];
                }
                if (value != 0) {
                    words_string += words[value] + " ";
                }
                if ((i == 1 && value != 0) || (i == 0 && value != 0 && n_array[i + 1] == 0)) {
                    words_string += "Crores ";
                }
                if ((i == 3 && value != 0) || (i == 2 && value != 0 && n_array[i + 1] == 0)) {
                    words_string += "Lakhs ";
                }
                if ((i == 5 && value != 0) || (i == 4 && value != 0 && n_array[i + 1] == 0)) {
                    words_string += "Thousand ";
                }
                if (i == 6 && value != 0 && (n_array[i + 1] != 0 && n_array[i + 2] != 0)) {
                    words_string += "Hundred and ";
                } else if (i == 6 && value != 0) {
                    words_string += "Hundred ";
                }
            }
            words_string = words_string.split("  ").join(" ");
        }
        return words_string;
    };
});*/

app.service("$globalVars", function () {
    var myName = "Shekhar";
    return {
        getMyName: function () {
            return myName;
        },
        setMyName: function (value) {
            myName = value;
        }
    }
});
app.value("chsh",17);
app.run(function ($rootScope, $location, Data, $route,$templateCache) {
    $rootScope.authenticated = false;
    /*$rootScope.$on('$viewContentLoaded', function() {
      if($templateCache) {
         $templateCache.removeAll();
      }
   });*/
    $rootScope.$on("$routeChangeStart", function (event, next, current) {
        //event.preventDefault(); 
        console.log(next.$$route.originalPath);
        temp = next.$$route.originalPath.split("/");
        console.log(temp[0]);
        exactpath = "";
        var log = [];
        angular.forEach(next.params, function(value, key) {
            exactpath = exactpath + " "+key+': '+value;
            //this.push(key + ': ' + value);
        });
        $activity_data = temp[1]+"->"+exactpath;
        console.log($activity_data);
        Data.get('activity_data/'+$activity_data).then(function (results) {
        });
        $rootScope.propertiesmenu = false;
        $rootScope.hasPermissions = false;
        $rootScope.hasRoles = false;
        $rootScope.admin = false;
        $rootScope.username = "Sign in ";
        $rootScope.company_name = "RD Brothers";
        $rootScope.goindex = "user";
        $rootScope.current_user_id  = 0;
        $rootScope.current_team = "";
        Data.get('session').then(function (results) {
            if (results.user_id) 
            {
                $rootScope.authenticated = true;
                $rootScope.admin = false;
                $rootScope.user_id = results.user_id;
                $rootScope.username = results.username;
                $rootScope.emp_name = results.emp_name;
                $rootScope.emp_image = results.emp_image;
                $rootScope.email_id = results.email_id;
                $rootScope.bo_id = results.bo_id;
                $rootScope.bo_name = results.bo_name;
                $rootScope.teams = results.teams;
                $rootScope.sub_teams = results.sub_teams;
                $rootScope.role = results.role;
                $rootScope.hasRoles = results.role;
                //alert($rootScope.hasRoles);
                //console.log($rootScope.hasRoles);
                $rootScope.hasPermissions = results.permissions;
                $("#permission_string").val(results.permissions);
                /*console.log("session"+$("#permission_string").val());*/
                
                $rootScope.current_user_id  = results.user_id;
                $rootScope.current_team = "";
                $rootScope.goindex = "user";
                if ((results.role).includes("Admin"))
                {

                  $rootScope.goindex = "admin";

                }

                /*else if ((results.role).includes("Manager") || (results.role).includes("Branch Head"))

                {

                  $rootScope.goindex = "manager";

                }*/
                $("#logged_user_id").val(results.user_id);
                
                /*if ((results.role).includes=="Admin")
                {
                    $rootScope.admin = true;
                    $rootScope.emp_name = "Admin";
                    $rootScope.bo_name = "";
                }*/
                
            } 
            else 
            {
                var nextUrl = next.$$route.originalPath;
                if (nextUrl == '/signup' || nextUrl == '/login' || nextUrl == '/newlogin' || nextUrl == '/forgetpassword' ) 
                {
                    
                } 
                else 
                {
                    $location.path('login');
                }
            }
        });
    });
});

app.controller('DatepickerDemoCtrl', function ($scope) {
  $scope.today = function() {
    $scope.dt = new Date();
  };
  $scope.today();

  $scope.clear = function () {
    $scope.dt = null;
  };

  // Disable weekend selection
  $scope.disabled = function(date, mode) {
    return ( mode === 'day' && ( date.getDay() === 0 || date.getDay() === 6 ) );
  };

  $scope.toggleMin = function() {
    $scope.minDate = $scope.minDate ? null : new Date();
  };
  $scope.toggleMin();

  $scope.open = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.opened = true;
  };

  $scope.dateOptions = {
    formatYear: 'yy',
    startingDay: 1
  };

  $scope.formats = ['dd-MM-yyyy','dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
  $scope.format = $scope.formats[0];

  var tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  var afterTomorrow = new Date();
  afterTomorrow.setDate(tomorrow.getDate() + 2);
  $scope.events =
    [
      {
        date: tomorrow,
        status: 'full'
      },
      {
        date: afterTomorrow,
        status: 'partially'
      }
    ];
	

  $scope.getDayClass = function(date, mode) {
    if (mode === 'day') {
      var dayToCheck = new Date(date).setHours(0,0,0,0);

      for (var i=0;i<$scope.events.length;i++){
        var currentDay = new Date($scope.events[i].date).setHours(0,0,0,0);

        if (dayToCheck === currentDay) {
          return $scope.events[i].status;
        }
      }
    }

    return '';
  };
});



