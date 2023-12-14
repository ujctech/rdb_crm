
// CONTACTS

app.controller('Contacts_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$window ) {
    
    var cat = $routeParams.cat;
    $scope.cat = cat;

    
    

    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
    $scope.contacts = {};
    $scope.regular_list = "Yes";
    $scope.pagenavigation = function(which_side)
    {
        $scope.agreements = {};
        if (which_side == 'prev') 
        {
            $scope.next_page_id = parseInt($scope.next_page_id)-60;
            if ($scope.next_page_id<0)
            {
                $scope.next_page_id = 0;
            }
        }
        if ($scope.regular_list=="Yes")
        {
            Data.get('contact_list_ctrl/'+$scope.cat+'/'+$scope.next_page_id).then(function (results) {
                $scope.contacts = results;
                $scope.page_range = parseInt($scope.next_page_id)+1+" - ";
                //if (which_side == 'next')
                //{
                    $scope.next_page_id = parseInt($scope.next_page_id)+30;
                    $scope.page_range = $scope.page_range + $scope.next_page_id;
                //}
            });
        }
        else
        {
            
            $scope.search_contacts($scope.searchdata,'pagenavigation');
            
        }
    }

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ($scope.cat=='developer')
    {
        if ((($str).indexOf("contacts_developer_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("contacts_developer_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("contacts_developer_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("contacts_developer_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='broker')
    {

        if ((($str).indexOf("contacts_broker_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("contacts_broker_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("contacts_broker_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("contacts_broker_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='client')
    {

        if ((($str).indexOf("contacts_client_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("contacts_client_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("contacts_client_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("contacts_client_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='referrals')
    {

        if ((($str).indexOf("contacts_referral_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("contacts_referral_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("contacts_referral_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("contacts_referral_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='target')
    {

        if ((($str).indexOf("contacts_target_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("contacts_target_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("contacts_target_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("contacts_target_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    $scope.contacts = {};
    if (!$scope.view_rights)
    {
        $scope.contacts = {};
        alert("You don't have rights to use this option..");
        return;
    }
    $scope.select_assign_to = function(teams,sub_teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/'+sub_teams).then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }

    $scope.searchdata = {};

    $timeout(function () { 
        Data.get('contact_list_ctrl/'+$scope.cat+'/'+$scope.next_page_id).then(function (results) {
            
            if (results[0].contact_count>0)
            {
                $scope.contacts = results;
                $scope.next_page_id = 30;
                $scope.contact_count = results[0].contact_count;
                $scope.total_records = results[0].contact_count;
            }
            else{
                alert("Search Criteria Not Matching.. !!");
            }
            
        });
    }, 100);

    var values_loaded = "false";
    $scope.open_search = function()
    {
        if (values_loaded=="false")
        {
            values_loaded="true";
            console.log("opening");

            $timeout(function () { 
                Data.get('getdatavalues_contact/company_name/'+cat).then(function (results) {
                    $scope.company_names = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('getdatavalues_contact/name/'+cat).then(function (results) {
                    $scope.names = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('getdatavalues_contact/mob_no/'+cat).then(function (results) {
                    $scope.mob_nos = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('getdatavalues_contact/contact_off/'+cat).then(function (results) {
                    $scope.client_types  = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('getdatavalues_contact/contact_id/'+cat).then(function (results) {
                    $scope.client_ids = results;
                });
            }, 100);
        
            /*$timeout(function () { 
                Data.get('getdatavalues_contact/name/developer').then(function (results) {
                    $scope.developer_ids = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('getdatavalues_contact/name/broker').then(function (results) {
                    $scope.broker_ids = results;
                });
            }, 100);*/
        
            $timeout(function () { 
                Data.get('getdatavalues_contact/alt_phone_no/'+cat).then(function (results) {
                    $scope.alt_phone_nos = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('getdatavalues_contact/off_phone/'+cat).then(function (results) {
                    $scope.off_phones = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('getdatavalues_contact/email/'+cat).then(function (results) {
                    $scope.emails = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('selectdropdowns/CLIENT_SOURCE').then(function (results) {
                    $scope.client_sources = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('selectdropdowns/SUB_SOURCE').then(function (results) {
                    $scope.sub_sources = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectarea').then(function (results) {
                    $scope.areas = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('selectlocality').then(function (results) {
                    $scope.localities = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectcity').then(function (results) {
                    $scope.cities = results;
                });

            }, 100);

            $timeout(function () { 
                console.log("pks22555");
                Data.get('selectusers').then(function (results) {
                    console.log("pks1");
                    console.log(results);
                    $scope.users = results;
                });
            }, 100);

            $timeout(function () { 
                console.log("pks3456");
                Data.get('selectgroups').then(function (results) {
                    console.log("pks2");
                    console.log(results);
                    $scope.groups = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectteams').then(function (results) {
                    $scope.teams = results;
                });
            }, 100); 
            $timeout(function () { 
                Data.get('selectsubteams').then(function (results) {
                    $scope.sub_teams_list = results;
                });
            }, 100);
            
        }
    };

    $scope.search_contacts = function (searchdata,from_click) 
    {
        if ($scope.regular_list == "Yes")
        {
            $scope.next_page_id = 0;
            $scope.regular_list = "No"
        }
        if (from_click=='form')
        {
            $scope.next_page_id = 0;
        }
        $scope.page_range = parseInt($scope.next_page_id)+1+" - ";
        console.log($scope.next_page_id);
        searchdata.next_page_id = $scope.next_page_id;
        searchdata.contact_off = cat;
        Data.post('search_contacts', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.contacts, function() {
                if (results[0].contact_count>0)
                {
                    $scope.contacts = {};
                    $scope.contacts = results;
                    $scope.contact_count = results[0].contact_count;
                    $scope.total_records = results[0].contact_count;
                    $scope.next_page_id = parseInt($scope.next_page_id)+30;
                    $scope.page_range = $scope.page_range + $scope.next_page_id;
                }
                else
                {
                    alert("Search Criteria Not Matching.. !!");
                }
                
            },true);
        });
    };

    $scope.resetForm = function()
    {
        $scope.page_range = "1 - 30";
        $scope.total_records = 0;
        $scope.next_page_id = 0;
        $scope.regular_list = "Yes";
        $scope.searchdata = {};
        $scope.$watch($scope.searchdata, function() {
            $scope.searchdata = {
            }
        });
        $("li.select2-selection__choice").remove();
        $(".select2").each(function() { $(this).val([]); });

        $scope.next_page_id = 0;
        
        Data.get('contact_list_ctrl/'+$scope.cat+'/0').then(function (results) {
            $scope.contacts = results;
            $scope.next_page_id = 30;
            $scope.contact_count = results[0].contact_count;
            $scope.total_records = results[0].contact_count;
        });
    }

    $scope.update_visited = function(visited,contact_id)
    {
        Data.get('update_visited/'+visited+'/'+contact_id).then(function (results) {
            
        });
    }

    $scope.select_unselect = function(isChecked)
    {
        if (isChecked)
        {
            $(".check_element").each(
                function(index) {
                    this.checked=true;
                }
            );
        }
        else{
            $(".check_element").each(
                function(index) {
                    this.checked=false;
                }
            );
        }

    };

    
    $scope.send_mails = function ()
    {
        var data = '';
        var first = "Yes";
        var count = 0;
        $(":checked.check_element").each(
            function(index) 
            {
                if (first == 'Yes')
                {
                    data = data+this.value;
                    first = 'No';
                }
                else
                {
                    data = data+','+this.value;
                }
                count = count + 1;
                
            }
        );
        if (count == 0)
        {
            alert("Select atleast one record ... !!");
        }
        else
        {
            $location.path('mails_client/contacts/'+data);
        }
    }
    
   

    
    $scope.send_wamessages = function ()
    {
        var data = '';
        var first = "Yes";
        var count = 0;
        $(":checked.check_element").each(
            function(index) 
            {
                if (first == 'Yes')
                {
                    data = data+this.value;
                    first = 'No';
                }
                else
                {
                    data = data+','+this.value;
                }
                count = count + 1;
                
            }
        );
        if (count == 0)
        {
            alert("Select atleast one record ... !!");
        }
        else
        {
            $scope.send_wamessage("referrals",data);
        }

    
    }



    $scope.batch_update = function ()
    {
        var data = '';
        var first = "Yes";
        var count = 0;
        $(":checked.check_element").each(
            function(index) 
            {
                if (first == 'Yes')
                {
                    data = data+this.value;
                    first = 'No';
                }
                else
                {
                    data = data+','+this.value;
                }
                count = count + 1;
                
            }
        );
        if (count == 0)
        {
            alert("Select atleast one record ... !!");
        }
        else
        {
            $location.path('batch_update/contact/'+$scope.cat+'/contact_id/'+data);
        }
    }

    
    $scope.convertdata= {};
    $scope.wa_data = {};

    $scope.send_wamessage = function(cat,id)
    {
        console.log(cat);
        console.log(id);
        $scope.wa_data.receipient = "";
        $scope.wa_data.category_id = id;
        $scope.wa_data.category = "contacts";
        $scope.wa_data.id = id;
        Data.get('getwa_data/contacts/'+id).then(function (results) {
            console.log(results[0].mobile_nos);
            $scope.wa_data.receipient = results[0].mobile_nos;
        });
    }

    $scope.wasend = function(wa_data)
    {
        console.log(wa_data.message);
        message = wa_data.message;
        receipient = wa_data.receipient;
        var wasending = confirm('Want to send Message ? ');
        if (wasending) 
        {
            Data.post('wasend', {
                wa_data: wa_data
            }).then(function (results) {
                $scope.whatsapp_message(message,receipient);
            });
        }
    }
    
    $scope.uploadcontact_data = function (convertdata) {
        // console.log("hii pks");
        // console.log(convertdata);
        // console.log(123);
        //convertdata.file_name = $("#file_name").val();
        var currentdate = new Date(); 
        var datetime = currentdate.getFullYear()+ "-" + (currentdate.getMonth()+1) + "-" +  currentdate.getDate()+ " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
        convertdata.created_date = datetime;
        convertdata.file_name = $("#file_name").val();
        Data.post('uploadcontact', {
            convertdata: convertdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                console.log("inserted");
                window.location.href = '/contacts_list/developer';
                window.location.reload();
                // $location.path('/contacts_list/developer');
            }
        });
    };
    
    $scope.whatsapp_message = function(message,receipient)
    {
        console.log(message);
        $window.open("https://api.whatsapp.com/send?phone=91"+receipient+"&text="+message, "_target");
    }

    $scope.export = function ()
    {
        var data = '';
        var first = "Yes";
        var count = 0;
        $(":checked.check_element").each(
            function(index) 
            {
                if (first == 'Yes')
                {
                    data = data+this.value;
                    first = 'No';
                }
                else
                {
                    data = data+','+this.value;
                }
                count = count + 1;
                
            }
        );
        
        if (count == 0)
        {
            alert("Select atleast one record ... !!");
            $("#exportdetails").modal("hide");
        }
        else
        {
            console.log(count);
            $timeout(function () { 
                Data.get('getreport_fields/contacts/unselected').then(function (results) {
                    $scope.unselected_fields = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getreport_fields/contacts/selected').then(function (results) {
                    $scope.selected_fields = results;
                });
            }, 100);

            $("#exportdetails").modal("show");
            //$location.path('manage_group/project/project_id/'+data);
        }

    }
    $scope.reportfieldsselect = function (report_fields_id,selected_action)
    {
        Data.get('reportfieldsselect/'+report_fields_id+'/'+selected_action).then(function (results) {
            $timeout(function () { 
                Data.get('getreport_fields/contacts/unselected').then(function (results) {
                    $rootScope.unselected_fields = results;
                });
            }, 100);
    
            $timeout(function () { 
                Data.get('getreport_fields/contacts/selected').then(function (results) {
                    $rootScope.selected_fields = results;
                });
            }, 100);
        });
    }

    $scope.option_value = "current_page";
    
    
    // scope.import_contacts = function () {
    //     $("#contact_info").css("display","none");
    //     $("#upload").css("display","block");
    // }
    
    $scope.exportdata = function()
    {
        
        var data = '';
        var first = "Yes";
        var count = 0;
        $(":checked.check_element").each(
            function(index) 
            {
                if (first == 'Yes')
                {
                    data = data+this.value;
                    first = 'No';
                }
                else
                {
                    data = data+','+this.value;
                }
                count = count + 1;
                
            }
        );
        if ($scope.option_value=='selected' && count ==0)
        {
            alert("Please select atleast one record ?");
            $("#exportdetails").modal("hide");
            return;
        }
        if (count==0)
        {
            data="empty";
        }
        
        $("#exportdetails").modal("hide");
        $timeout(function () { 
            Data.get('exportdata/contacts/contact_id/'+data+'/'+$scope.option_value).then(function (results) {
                window.location="api//v1//uploads//contacts_list.xlsx";
            });
        }, 100);
      }

    
});
    
    
app.controller('Contacts_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {


    var cat = $routeParams.cat;
    $scope.cat = cat;
    console.log($scope.cat);
    $scope.contact = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ($scope.cat=='developer')
    {
        if ((($str).indexOf("contacts_developer_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("contacts_developer_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("contacts_developer_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("contacts_developer_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='broker')
    {
        if ((($str).indexOf("contacts_broker_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("contacts_broker_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("contacts_broker_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("contacts_broker_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='client')
    {

        if ((($str).indexOf("contacts_client_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("contacts_client_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("contacts_client_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("contacts_client_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='referrals')
    {

        if ((($str).indexOf("contacts_referral_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("contacts_referral_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("contacts_referral_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("contacts_referral_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='target')
    {
        if ((($str).indexOf("contacts_target_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("contacts_target_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("contacts_target_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("contacts_target_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if (!$scope.create_rights)
    {
        $scope.contact = {};
        alert("You don't have rights to use this option..");
        return;
    }

    $timeout(function () { 
        Data.get('selectdropdowns/CLIENT_SOURCE').then(function (results) {
            $rootScope.client_sources = results;
        });
    }, 100);


    $scope.change_sub_source = function (source_channel) 
    { 
        Data.get('change_sub_source/'+source_channel).then(function (results) { 
            $rootScope.sub_sources = results; 
        });
    }
    
    
    $scope.select_assign_to = function(teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/0').then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }
    
    $timeout(function () {
        Data.get('selectgroups').then(function (results) {
            $scope.groups = results;
        });
    }, 100);
    
    $timeout(function () { 
            Data.get('selectsubteams').then(function (results) {
                $scope.sub_teams = results;
            });
        }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/SUB_SOURCE').then(function (results) {
            $rootScope.sub_sources = results;
        });
    }, 100);

   
    $scope.contact_details = function (field_name,value) 
    {  

        if (field_name=='locality_id')
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+value).then(function (results) {
                    $scope.contact.area_id = results[0].area_id;
                    $scope.contact.city = results[0].city;
                    $scope.contact.state = results[0].state;
                    $scope.contact.country = results[0].country;
                });
            }, 100);
            $timeout(function () { 
                $("#contact.area_id").select2();
            },2000);
        }

        if (field_name=='area_id')
        {
            $timeout(function () { 
                Data.get('getfromarea/'+value).then(function (results) {
                    $scope.contact.city = results[0].city;
                    $scope.contact.state = results[0].state;
                    $scope.contact.country = results[0].country;
                });
            }, 100);
        }
        if (field_name=='off_locality')
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+value).then(function (results) {
                    $scope.contact.off_area = results[0].area_id;
                    $scope.contact.off_city = results[0].city;
                    $scope.contact.off_state = results[0].state;
                    $scope.contact.off_country = results[0].country;
                });
            }, 100);
        }

        if (field_name=='off_area')
        {
            $timeout(function () { 
                Data.get('getfromarea/'+value).then(function (results) {
                    $scope.contact.off_city = results[0].city;
                    $scope.contact.off_state = results[0].state;
                    $scope.contact.off_country = results[0].country;
                });
            }, 100);
        }
        if (field_name=='opp_area')
        {
            $timeout(function () { 
                Data.get('getfromarea/'+value).then(function (results) {
                    $scope.contact.opp_city = results[0].city;
                });
            }, 100);
        }
    }
    

    $scope.contact_add_new = {contact:''};
    $scope.contact_add_new = function (contact) {
        contact.contact_off = cat;
        contact.file_name = $("#file_name_company_logo").val();
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_company_logo').fileinput('upload');
                $('#file_documents').fileinput('upload');
                $('#file_visiting_card').fileinput('upload');
                $('#file_contact_pic').fileinput('upload');
                $location.path('contacts_list/'+$scope.cat);
            }
        });
    };
    
   
    $scope.AddListValue = function (type)
    {
        $scope.temptype = type;
        $timeout(function () { 
            Data.get('selectparentlist').then(function (results) {
                $rootScope.parentlists = results;
            });
        }, 100);

        $scope.listvalues = {
                                type:type,
                            }
    }

    $scope.listvalues_add = function (listvalues) {
        Data.post('listvalues_add', {
            listvalues: listvalues
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $("#addvalues").modal("hide");
                $timeout(function () { 
                    Data.get('selectdropdowns/'+$scope.temptype).then(function (results) {
                        var controlvalue = (($scope.temptype).toLowerCase());
                        
                        if ($scope.temptype=='CLIENT_SOURCE')
                        {
                            controlvalue = "client_sources";
                        }
                        if ($scope.temptype=='SUB_SOURCE')
                        {
                            controlvalue = "sub_sources";
                        }
                       
                        $scope.$watch($rootScope[controlvalue], function() {
                            $rootScope[controlvalue] = results;
                        }, true);
                    });
                }, 1000);
            }
        });
    };
    $scope.mob_error = "";
    $scope.email_error = "";
    $scope.checkcontact = function (field,field_name) 
    { 
        Data.get('checkcontact/'+field+'/'+field_name).then(function (results) {
            console.log(results);
            if (results[0].found=='Yes')
            {
                if (field_name=='mob_no')
                {
                    alert("Mobile Number already registered ... !!");
                    $scope.mob_error = "Mobile Number already registered";
                }
                if (field_name=='email')
                {
                    alert("Email ID already registered ... !!");
                    $scope.email_error = "Email ID already registered";
                }
            }
            else{
                if (field_name=='mob_no')
                {
                    $scope.mob_error = "";
                }
                if (field_name=='email')
                {
                    $scope.email_error = "";
                }
            }
        });
    }
});

function activate_document_input()
{ 

    $("#file_company_logo").fileinput({
        uploadAsync: false,
        showUpload: false,
        uploadUrl: './api/v1/contact_uploads',
        allowedFileExtensions : ['jpg','jpeg', 'png','gif','image','3gp'],
        overwriteInitial: false,
        maxFileSize: 1500000,
        maxFilesNum: 25,
        maxFileCount:100,
        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });

    $('#file_company_logo').on('fileloaded', function(event, file, previewId, index, reader) {
        $("#file_name_company_logo").val(file.name);
        console.log("fileloaded");
    });

    $("#file_documents").fileinput({
        uploadAsync: false,
        showUpload: false,
        uploadUrl: './api/v1/contact_uploads_documents',
        allowedFileExtensions : ['jpg','jpeg', 'png','gif','image','3gp'],
        overwriteInitial: false,
        maxFileSize: 1500000,
        maxFilesNum: 25,
        maxFileCount:100,
        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });

    $('#file_documents').on('fileloaded', function(event, file, previewId, index, reader) {
        $("#file_name_documents").val(file.name);
        console.log("fileloaded");
    });

    $("#file_visiting_card").fileinput({
        uploadAsync: false,
        showUpload: false,
        uploadUrl: './api/v1/contact_uploads_visiting_card',
        allowedFileExtensions : ['jpg','jpeg', 'png','gif','image','3gp'],
        overwriteInitial: false,
        maxFileSize: 1500000,
        maxFilesNum: 25,
        maxFileCount:100,
        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });

    $('#file_visitng_card').on('fileloaded', function(event, file, previewId, index, reader) {
        $("#file_name_visiting_card").val(file.name);
        console.log("fileloaded");
    });

    $("#file_contact_pic").fileinput({
        uploadAsync: false,
        showUpload: false,
        uploadUrl: './api/v1/contact_uploads_contact_pic',
        allowedFileExtensions : ['jpg','jpeg', 'png','gif','image','3gp'],
        overwriteInitial: false,
        maxFileSize: 1500000,
        maxFilesNum: 25,
        maxFileCount:100,
        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });

    $('#file_contact_pic').on('fileloaded', function(event, file, previewId, index, reader) {
        $("#file_name_contact_pic").val(file.name);
        console.log("fileloaded");
    });
}
    
app.controller('Contacts_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {
    
    var contact_id = $routeParams.contact_id;
    $scope.activePath = null;
    $scope.contact = {};

    $scope.enable_disable = true;
    
    $scope.modify = function()
    {
        $scope.enable_disable = false;
        $scope.disableAll = false;
    }

    $scope.create_new_contact = function(contact_id)
    {
        var createcontact = confirm('This will create new contact from Contact ID '+contact_id);
        if (createcontact) {

            Data.get('create_new_contact/'+contact_id).then(function (results) { 
                Data.toast(results);
                newcontact_id = results.contact_id;
                $location.path('contact_edit/'+newcontact_id);
            });
        }
    }


    Data.get('contact_edit_ctrl/'+contact_id).then(function (results) {
        $scope.cat=results[0].contact_off;
        console.log($scope.cat);
        $scope.create_rights = false;
        $scope.update_rights = false;
        $scope.delete_rights = false;
        $scope.view_rights = false;
        $scope.export_rights = false;    
        $str = ($("#permission_string").val());
        if (($scope.cat).toLowerCase()=='developer')
        {
            console.log($str);
            if ((($str).indexOf("contacts_developer_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("contacts_developer_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("contacts_developer_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("contacts_developer_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if (($scope.cat).toLowerCase()=='broker')
        {

            if ((($str).indexOf("contacts_broker_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("contacts_broker_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("contacts_broker_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("contacts_broker_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if (($scope.cat).toLowerCase()=='client')
        {

            if ((($str).indexOf("contacts_client_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("contacts_client_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("contacts_client_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("contacts_client_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if (($scope.cat).toLowerCase()=='referrals')
        {

            if ((($str).indexOf("contacts_referral_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("contacts_referral_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("contacts_referral_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("contacts_referral_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if (($scope.cat).toLowerCase()=='target')
        {

            if ((($str).indexOf("contacts_target_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("contacts_target_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("contacts_target_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("contacts_target_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if (!$scope.update_rights)
        {
            $scope.contact = {};
            alert("You don't have rights to use this option..");
            return;
        }

        $scope.arr = ((results[0].assign_to).split(','));
        results[0].assign_to = $scope.arr;
        $scope.arr = ((results[0].source_channel).split(','));
        results[0].source_channel = $scope.arr;
        $scope.arr = ((results[0].source_sub_channel).split(','));
        results[0].source_sub_channel = $scope.arr;
        $scope.arr = ((results[0].groups).split(','));
        results[0].groups = $scope.arr;
        $scope.arr = ((results[0].teams).split(','));
        results[0].teams = $scope.arr;
        $scope.arr = ((results[0].sub_teams).split(','));
        results[0].sub_teams = $scope.arr;
        $scope.arr = ((results[0].opp_area).split(','));
        results[0].opp_area = $scope.arr;

        $scope.$watch($scope.contact, function() {
            $scope.contact = {};
            $scope.contact = {
                company_name:results[0].company_name,
                contact_off:results[0].contact_off,
                add1:results[0].add1,
                add2:results[0].add2,
                locality_id:results[0].locality_id,
                area_id:results[0].area_id,
                zip:results[0].zip,
                comp_logo:results[0].comp_logo,
                name_title:results[0].name_title,
                f_name:results[0].f_name,
                l_name:results[0].l_name,
                mob_no:results[0].mob_no,
                mob_no1:results[0].mob_no1,
                email:results[0].email,
                alt_phone_no:results[0].alt_phone_no,
                alt_phone_no1:results[0].alt_phone_no1,
                contact_pic:results[0].contact_pic,
                designation:results[0].designation,
                birth_date:results[0].birth_date,
                teams:results[0].teams,
                sub_teams:results[0].sub_teams,
                assign_to:results[0].assign_to,
                groups:results[0].groups,
                rera_no:results[0].rera_no,
                gst_no:results[0].gst_no,
                comments:results[0].comments,
                testimonial:results[0].testimonial,
                dnd:results[0].dnd,
                other_phone:results[0].other_phone,
                pan_no:results[0].pan_no,
                tan_no:results[0].tan_no,
                aadhar_no:results[0].aadhar_no,
                occupation:results[0].occupation,
                off_email:results[0].off_email,
                off_phone:results[0].off_phone,
                off_phone1:results[0].off_phone1,
                off_phone2:results[0].off_phone2,
                off_fax:results[0].off_fax,
                off_add1:results[0].off_add1,
                off_locality:results[0].off_locality,
                off_area:results[0].off_area,
                off_zip:results[0].off_zip,
                source_channel:results[0].source_channel,
                source_sub_channel:results[0].source_sub_channel,
                reg_date:results[0].reg_date,
                website:results[0].website,
                rating:results[0].rating,
                opp_area:results[0].opp_area,
                about:results[0].about,
                invoice_name:results[0].invoice_name,
                contact_id:results[0].contact_id
            };
            if (results[0].locality_id>0)
            {
                Data.get('getfromlocality/'+results[0].locality_id).then(function (results1) {
                    $scope.contact.area_id = results1[0].area_id;
                    $scope.contact.city = results1[0].city;
                    $scope.contact.state = results1[0].state;
                    $scope.contact.country = results1[0].country;
                });
            }
            $timeout(function () { 
                $("#area_id").select2();
            },2000);
            if (results[0].off_locality>0)
            {
                $timeout(function () { 
                    Data.get('getfromlocality/'+results[0].off_locality).then(function (results1) {
                        $scope.contact.off_area = results1[0].area_id;
                        $scope.contact.off_city = results1[0].city;
                        $scope.contact.off_state = results1[0].state;
                        $scope.contact.off_country = results1[0].country;
                    });
                }, 100);
            }
            $timeout(function () { 
                $("#off_area").select2();
            },2000);
            if (results[0].opp_area>0)
            {
                $timeout(function () { 
                    Data.get('getfromarea/'+results[0].opp_area).then(function (results) {
                        $scope.contact.opp_city = results[0].city;
                    });
                }, 100);
            }
            if (results[0].dnd=="1")
            {
                $('#dnd').prop('checked', true);
            }
            $('#rating').rating('update', results[0].rating);
            activate_document_input();
        },true);
        
    
        $timeout(function () {
            Data.get('selectgroups').then(function (results) {
                $scope.groups = results;
            });
        }, 100);
        
        $timeout(function () {            
            Data.get('change_sub_source/'+results[0].source_channel).then(function (results) { 
                $rootScope.sub_sources = results;                 
            });
        },3000);
    });

    Data.get('contact_images/'+contact_id).then(function (results) {
        $scope.contact_images = results;
    });

    Data.get('contact_images_documents/'+contact_id).then(function (results) {
        $scope.contact_images_documents = results;
    });

    Data.get('contact_contact_pic/'+contact_id).then(function (results) {
        $scope.contact_pics = results;
    });

    Data.get('contact_visiting_card/'+contact_id).then(function (results) {
        $scope.visiting_cards = results;
    });

    $scope.removeimage_docs = function (attachment_id) {
        var deleteproduct = confirm('Are you absolutely sure you want to delete?');
        if (deleteproduct) {
            Data.get('removeimage/'+attachment_id).then(function (results) {
                Data.toast(results);
                Data.get('contact_images_documents/'+contact_id).then(function (results) {
                    $scope.contact_images_documents = results;
                });
            });
        }
    };

    
    $timeout(function () { 
        Data.get('selectdropdowns/CLIENT_SOURCE').then(function (results) {
            $rootScope.client_sources = results;
        });
    }, 100);


    $scope.change_sub_source = function (source_channel) 
    { 
        Data.get('change_sub_source/'+source_channel).then(function (results) { 
            $rootScope.sub_sources = results; 
        });
    }
    

    $scope.select_assign_to = function(teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/0').then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }
    
    $timeout(function () { 
        Data.get('selectsubteams').then(function (results) {
            $scope.sub_teams = results;
        });
    }, 100);

    
    $timeout(function () { 
        Data.get('selectdropdowns/SUB_SOURCE').then(function (results) {
            $rootScope.sub_sources = results;
        });
    }, 100);

    
    
    $scope.contact_details = function (field_name,value) 
    {  

        if (field_name=='locality_id')
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+value).then(function (results) {
                    $scope.contact.area_id = results[0].area_id;
                    $scope.contact.city = results[0].city;
                    $scope.contact.state = results[0].state;
                    $scope.contact.country = results[0].country;
                });
            }, 100);
            $timeout(function () { 
                $("#area_id").select2();
            },2000);
        }

        if (field_name=='area_id')
        {
            $timeout(function () { 
                Data.get('getfromarea/'+value).then(function (results) {
                    $scope.contact.city = results[0].city;
                    $scope.contact.state = results[0].state;
                    $scope.contact.country = results[0].country;
                });
            }, 100);
        }

        if (field_name=='off_locality')
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+value).then(function (results) {
                    $scope.contact.off_area = results[0].area_id;
                    $scope.contact.off_city = results[0].city;
                    $scope.contact.off_state = results[0].state;
                    $scope.contact.off_country = results[0].country;
                });
            }, 100);
            $timeout(function () { 
                $("#off_area").select2();
            },2000);
        }

        if (field_name=='off_area')
        {
            $timeout(function () { 
                Data.get('getfromarea/'+value).then(function (results) {
                    $scope.contact.off_city = results[0].city;
                    $scope.contact.off_state = results[0].state;
                    $scope.contact.off_country = results[0].country;
                });
            }, 100);
        }
        if (field_name=='opp_area')
        {
            $timeout(function () { 
                Data.get('getfromarea/'+value).then(function (results) {
                    $scope.contact.opp_city = results[0].city;
                });
            }, 100);
        }
    }

    
    $scope.contact_update = function (contact) {
        Data.post('contact_update', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_company_logo').fileinput('upload');
                $('#file_documents').fileinput('upload');
                $('#file_visiting_card').fileinput('upload');
                $('#file_contact_pic').fileinput('upload');
                $location.path('contacts_list/'+$scope.cat);
            }
        });
    };
    
    $scope.contact_delete = function (contact) {
        //console.log(business_unit);
        var deletecontact = confirm('Are you absolutely sure you want to delete?');
        if (deletecontact) {
            Data.post('contact_delete', {
                contact: contact
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('contacts_list/'+$scope.cat);
                }
            });
        }
    };
    $scope.mob_error = "";
    $scope.email_error = "";
    $scope.checkcontact = function (field,field_name) 
    { 
        Data.get('checkcontact/'+field+'/'+field_name).then(function (results) {
            console.log(results);
            if (results[0].found=='Yes')
            {
                if (field_name=='mob_no')
                {
                    alert("Mobile Number already registered ... !!");
                    $scope.mob_error = "Mobile Number already registered";
                }
                if (field_name=='email')
                {
                    alert("Email ID already registered ... !!");
                    $scope.email_error = "Email ID already registered";
                }
            }
            else{
                if (field_name=='mob_no')
                {
                    $scope.mob_error = "";
                }
                if (field_name=='email')
                {
                    $scope.email_error = "";
                }
            }
        });
    }

    
});
    
app.controller('SelectContact', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('selectcontact').then(function (results) {
            $rootScope.contacts = results;
        });
    }, 100);
});
