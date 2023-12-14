// ENQUIRIES

app.controller('Enquiries_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$sce, $window ) {
    var cat = $routeParams.cat;
    $scope.cat = cat;

    var property_ids = $routeParams.data;
    $scope.property_id = property_ids;

    var id = $routeParams.id;
    $scope.id = id;

    $scope.showAmount = numDifferentiation;
    $scope.listenquiries = {};

    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
    $scope.regular_list = "Yes";
    $scope.pagenavigation = function(which_side)
    {
        $scope.listenquiries = {};
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
            Data.get('enquiries_list_ctrl/'+$scope.cat+'/'+$scope.id+'/'+$scope.next_page_id).then(function (results) {
                $scope.listenquiries = {};
                $scope.listenquiries = results;
                $scope.page_range = parseInt($scope.next_page_id)+1+" - ";
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                $scope.page_range = $scope.page_range + $scope.next_page_id;
            });
        }
        else
        {
            $scope.search_enquiries($scope.searchdata,'pagenavigation');
            
        }
    }

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ($scope.cat=='residential')
    {
        if ((($str).indexOf("enquiries_residential_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("enquiries_residential_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("enquiries_residential_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("enquiries_residential_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='pre-leased')
    {

        if ((($str).indexOf("enquiries_preleased_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("enquiries_preleased_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("enquiries_preleased_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("enquiries_preleased_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='retail')
    {

        if ((($str).indexOf("enquiries_retail_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("enquiries_retail_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("enquiries_retail_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("enquiries_retail_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='commercial')
    {

        if ((($str).indexOf("enquiries_commercial_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("enquiries_commercial_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("enquiries_commercial_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("enquiries_commercial_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='others')
    {

        if ((($str).indexOf("enquiries_others_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("enquiries_others_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("enquiries_others_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("enquiries_others_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if (!$scope.view_rights)
    {
        $scope.listenquiries = {};
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
    
    $scope.convertdata= {};
    $timeout(function () { 
        Data.get('enquiries_list_ctrl/'+$scope.cat+'/'+$scope.id+'/'+$scope.next_page_id).then(function (results) {
            $scope.listenquiries = {};
            $scope.listenquiries = results;
            $scope.next_page_id = 30;
            $scope.enquiry_count = results[0].enquiry_count;
            $scope.total_records = results[0].enquiry_count;
            // console.log($scope.total_records);
            
                
            
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
                Data.get('selectdropdowns/LEAD_STATUS').then(function (results) {
                    $scope.lead_statuss = results;
                    
                });
            }, 100);
            $scope.$watch($scope.searchdata.status, function() {
                $scope.searchdata.status = ['Active'];
            });
            //$('#status').select2().select2('val', ['Active']);
            //console.log($('#status').val());
            $timeout(function () { 
                Data.get('selectdropdowns/AMENITIES').then(function (results) {
                    $rootScope.amenities = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('selectdropdownsNew/PROP_SUB_TYPE/'+cat).then(function (results) { 
                    $rootScope.propsubtypes = results; 
                }); 
            }, 100);

            $scope.change_enquirysubtype = function (enquiry_type) 
            { 
                Data.get('change_enquirysubtype/'+enquiry_type).then(function (results) { 
                    $rootScope.enquirysubtypes = results; 
                });
            }


            $scope.change_suitablefor = function (propsubtype) 
            { 
                Data.get('change_suitablefor/'+propsubtype).then(function (results) { 
                    $rootScope.suitable_for_lov = results; 
                });
            }

            $timeout(function () { 
                Data.get('selectdropdowns/FURNITURE').then(function (results) {
                    $rootScope.furnitures = results;
                });
            }, 100);
            
            $timeout(function () { 
                Data.get('selectdropdowns/CLIENT_SOURCE').then(function (results) {
                    $rootScope.client_sources = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectdropdowns/SUB_SOURCE').then(function (results) {
                    $rootScope.sub_sources = results;
                });
            }, 100);
            
            $timeout(function () { 
                Data.get('selectdropdowns/PROP_STATUS').then(function (results) {
                    $rootScope.prop_statuss = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectdropdowns/PRJ_SPECIFICATIONS').then(function (results) {
                    $rootScope.pro_specifications = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectdropdowns/PARKING_DIR').then(function (results) {
                    $rootScope.parkings = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('selectdropdowns/ENQUIRY_STAGE_LOV').then(function (results) {
                    $rootScope.enquiry_stages = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('selectcontact/Developer').then(function (results) {
                    $scope.developers = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectcontact/Client').then(function (results) {
                    $scope.clients = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectcontact/Broker').then(function (results) {
                    $scope.brokers = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectarea').then(function (results) {
                    $rootScope.areas = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectlocality').then(function (results) {
                    $rootScope.localities = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectteams').then(function (results) {
                    $rootScope.teams = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectsubteams').then(function (results) {
                    $scope.sub_teams_list = results;
                });
            }, 100);
            
            $timeout(function () { 
                Data.get('selectusers').then(function (results) {
                    $rootScope.users = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectproject').then(function (results) {
                    $rootScope.projects = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_enquiry/enquiry_id/'+cat).then(function (results) {
                    $scope.enquiry_ids = results;
                });
            }, 100);
        };
    };
    $scope.enquiryupdatestatus = function (value,enquiry_id)
    {
        var enquiryalert = confirm('Are you absolutely sure you want to change status ?');
        if (enquiryalert) {
            Data.get('enquiryupdatestatus/'+value+'/'+enquiry_id).then(function (results) {
                $timeout(function () { 
                    Data.get('enquiries_list_ctrl/'+$scope.cat+'/'+$scop.id).then(function (results) {
                        $scope.listenquiries = results;
                    });
                }, 100);
            });
        }
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
    $scope.pmatchingproperties = function(enquiry_id)
    {
          $timeout(function () { 
              Data.get('pmatchingproperties/'+enquiry_id).then(function (results) {
                  $scope.html = results[0].htmlstring;
                  var cstring = 'enqtrustedHtml_'+enquiry_id;
                  $scope[cstring] = $sce.trustAsHtml($scope.html);
              });
          }, 100);
    }

    $scope.import_enquiries = function () {
        $("#enquiries").css("display","none");
        $("#upload").css("display","block");

    }
    $scope.mp_count = function(enquiry_id)
    {
        $timeout(function () { 
            Data.get('mp_count/'+enquiry_id).then(function (results) {
                console.log(results[0].mp_count);
                $("#mp"+enquiry_id).html(results[0].mp_count);
            });
        }, 100);
    }
    $timeout(function () { 
        Data.get('selectsms_template/enquiry').then(function (results) {
            $rootScope.sms_templates = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('matching_properties_list/'+cat).then(function (results) {
            $scope.matching_properties_list = results;
        });
    }, 100);

    


    $scope.link_properties = function ()
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
            var linkproperties = confirm('Selected Enquiries:'+data+' will be linked to these properies '+property_ids);
            if (linkproperties) 
            {
                Data.get('link_to_property/'+property_ids+'/'+data).then(function (results) {

                });
            }
            //$window.open('#/properties_list/'+$scope.cat+'/0/'+data);
        }

    }

    $scope.select_sms_template  = function(sms_template_id)
    {
        $timeout(function () { 
            Data.get('select_sms_template/'+sms_template_id).then(function (results) {
                //$scope.mail_sent.cc_mail_id = $rootScope.email_id;
                $('.wysihtml5-sandbox, .wysihtml5-toolbar').remove();
                //$('.wysihtml5-toolbar').remove();
                //$("#text_message").reset();
                //
                $scope.message = results[0].text_message;
            });
        }, 100);
    }


    $scope.uploadenquiry_data = function (convertdata) {
        //convertdata.file_name = $("#file_name").val();
        var currentdate = new Date(); 
        var datetime = currentdate.getFullYear()+ "-" + (currentdate.getMonth()+1) + "-" +  currentdate.getDate()+ " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
        convertdata.created_date = datetime;
        convertdata.file_name = $("#file_name").val();
        Data.post('uploadenquiry', {
            convertdata: convertdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                console.log("inserted");
                window.location.href = '/enquiries_list/residential/0/0';
                window.location.reload();
                // $location.path('/enquiries_list/residential/0/0');
            }
        });
    };

    $scope.audit_trail = function ()
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
            $location.path('audit_trail/enquiry/enquiry_id/'+data);
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
            $location.path('batch_update/enquiry/'+$scope.cat+'/enquiry_id/'+data);
        }
    }
    $scope.manage_group = function ()
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
            $location.path('manage_group/enquiry/enquiry_id/'+data);
        }
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
            $timeout(function () { 
                Data.get('getreport_fields/enquiry/unselected').then(function (results) {
                    $rootScope.unselected_fields = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getreport_fields/enquiry/selected').then(function (results) {
                    $rootScope.selected_fields = results;
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
                Data.get('getreport_fields/enquiry/unselected').then(function (results) {
                    $rootScope.unselected_fields = results;
                });
            }, 100);
    
            $timeout(function () { 
                Data.get('getreport_fields/enquiry/selected').then(function (results) {
                    $rootScope.selected_fields = results;
                });
            }, 100);
        });
    }

    $scope.option_value = "current_page";
    $scope.exportdata = function(option_value)
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
        if (option_value=='selected' && count ==0)
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
            Data.get('exportdata/enquiry/enquiry_id/'+data+'/'+option_value).then(function (results) {
                window.location="api//v1//uploads//enquiry_list.xlsx";
            });
        }, 100);
    }

    $scope.search_enquiries = function (searchdata,from_click) 
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
        searchdata.proptype = cat;
        searchdata.next_page_id = $scope.next_page_id;
        
        Data.post('newsearch_enquiries', {
            searchdata: searchdata
        }).then(function (results) {
            if (results[0].enquiry_count>0)
            {
                $scope.listenquiries = {};
                $scope.listenquiries = results;
                $scope.enquiry_count = results[0].enquiry_count;
                $scope.total_records = results[0].enquiry_count;
                // console.log($scope.total_records);
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                $scope.page_range = $scope.page_range + $scope.next_page_id;
            }
            else
            {
                alert("Search Criteria Not Matching.. !!");
            }
        });
    };

    $scope.searchdata = {};
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
        Data.get('enquiries_list_ctrl/'+$scope.cat+'/'+$scope.id+'/0').then(function (results) {
            $scope.listenquiries = {};
            $scope.listenquiries = results;
            $scope.next_page_id = 30;
            $scope.enquiry_count = results[0].enquiry_count;
            $scope.total_records = results[0].enquiry_count;
        });
        
    }

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
            $location.path('mails_client/enquiry/'+data);
        }
    }

});
    
    
app.controller('Enquiries_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $timeout, $http, Data, $sce) {
    var cat = $routeParams.cat;
    $scope.cat = cat;
    $scope.enquiry = {};
    $scope.contact = {};
    
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ($scope.cat=='residential')
    {
        if ((($str).indexOf("enquiries_residential_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("enquiries_residential_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("enquiries_residential_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("enquiries_residential_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='pre-leased')
    {

        if ((($str).indexOf("enquiries_preleased_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("enquiries_preleased_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("enquiries_preleased_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("enquiries_preleased_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='retail')
    {

        if ((($str).indexOf("enquiries_retail_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("enquiries_retail_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("enquiries_retail_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("enquiries_retail_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='commercial')
    {

        if ((($str).indexOf("enquiries_commercial_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("enquiries_commercial_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("enquiries_commercial_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("enquiries_commercial_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='others')
    {

        if ((($str).indexOf("enquiries_others_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("enquiries_others_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("enquiries_others_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("enquiries_others_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if (!$scope.create_rights)
    {
        $scope.enquiry = {};
        alert("You don't have rights to use this option..");
        return;
    }
    $timeout(function () { 
        Data.get('selectteams').then(function (results) {
            $scope.teams = results;
        });
    }, 100);


    $timeout(function () { 
        Data.get('selectsubteams').then(function (results) {
            $scope.sub_teams = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selecttask').then(function (results) {
            $scope.task_list = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdownsNew/PROP_SUB_TYPE/'+cat).then(function (results) { 
            $rootScope.propsubtypes = results; 
        }); 
    }, 100);

    $scope.change_enquirysubtype = function (enquiry_type) 
    { 
        Data.get('change_enquirysubtype/'+enquiry_type).then(function (results) { 
            $rootScope.enquirysubtypes = results; 
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
        Data.get('selectdropdowns/POSSESSION_STATUS_LOV').then(function (results) {
            $rootScope.possession_statuss = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/FURNITURE').then(function (results) {
            $rootScope.furnitures = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/DIRECTION').then(function (results) {
            $rootScope.door_fdirs = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/AMENITIES').then(function (results) {
            $rootScope.amenities = results;
        });
    }, 100);
    
    $timeout(function () { 
        Data.get('selectdropdowns/PARKING_DIR').then(function (results) {
            $rootScope.parkings = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectcontact/Client').then(function (results) {
            $scope.clientslist = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectcontact/Broker').then(function (results) {
            $scope.brokers = results;
        });
    }, 100);

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

    $timeout(function () { 
        Data.get('selectdropdowns/SUB_SOURCE').then(function (results) {
            $rootScope.sub_sources = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/LEAD_STATUS').then(function (results) {
            $scope.lead_statuss = results;
            
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/ENQUIRY_STAGE_LOV').then(function (results) {
            $rootScope.enquiry_stages = results;
        });
    }, 100);

    $scope.calculate = function(field_name)
    {

        if ($scope.enquiry.enquiry_for=="Lease")
        {
            if (field_name == "deposite_month")
            {
                if ($scope.enquiry.deposite_month>0)
                {
                    $scope.enquiry.security_depo = ($scope.enquiry.deposite_month*$scope.enquiry.exp_price).toFixed(2);
                }
            }
        }
        
    }
    $scope.AddDeveloper = function (contact_off) 
    {  
        $scope.contact.contact_off = contact_off;
        console.log($scope.contact.contact_off);
    }

    $scope.showlocality = function (field_name,value) 
    {  

        if (field_name=='locality_id')
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+value).then(function (results) {
                    $scope.enquiry.preferred_area_id = results[0].area_id;
                    $scope.enquiry.preferred_city = results[0].city;
                    $scope.enquiry.preferred_state = results[0].state;
                    $scope.enquiry.preferred_country = results[0].country;
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
                    $scope.enquiry.preferred_city = results[0].city;
                    $scope.enquiry.preferred_state = results[0].state;
                    $scope.enquiry.preferred_country = results[0].country;
                });
            }, 100);
        }
    }

    $scope.getenquiries_exists = function(client_id)
    {
        
        $timeout(function () { 
            Data.get('getenquiries_exists/'+client_id).then(function (results) {
                if (results[0].count>0)
                {
                    $scope.html = results[0].htmlstring;
                    $scope.trustedHtml_enquiries_exists = $sce.trustAsHtml($scope.html);
                    $("#enquiries_exists").css("display","block");
                }
                else
                {
                    $("#enquiries_exists").css("display","none");
                }
            });
        }, 100);

    }
    $scope.$watch($scope.enquiry.assigned, function() {
        $scope.enquiry.assigned = [$rootScope.user_id];
    });
    
    $scope.$watch($scope.enquiry.teams, function() {
        $scope.enquiry.teams = [$rootScope.bo_id];
    });

    $scope.$watch($scope.enquiry.sub_teams, function() {
        $scope.arr = (([$rootScope.sub_teams]).toString());
        $scope.enquiry.sub_teams = [$scope.arr];
    });
    $scope.checklowhigh = function()
    {
        if (parseFloat($scope.enquiry.carp_area1)>0)
        {
            if (parseFloat($scope.enquiry.carp_area2)>0)
            {
                if (parseFloat($scope.enquiry.carp_area1)>parseFloat($scope.enquiry.carp_area2))
                {
                    alert("Carpet Area Range should be from Lowest to Highest ..!!");
                }
            }
        }
        if (parseFloat($scope.enquiry.budget_range1)>0)
        {
            if (parseFloat($scope.enquiry.budget_range2)>0)
            {
                if (parseFloat($scope.enquiry.budget_range1)>parseFloat($scope.enquiry.budget_range2))
                {
                    alert("Budget Range should be from Lowest to Highest ..!!");
                }
            }
        }
    }

    //$scope.enquiries_add_new = {enquiry:''};
    $scope.enquiries_add_new = function (enquiry) { 
        $("#add-new-btn").css("display","none");
        enquiry.enquiry_off = $scope.cat;
        enquiry.intrnal_comment = $("#intrnal_comment").val(); 
        Data.post('enquiries_add_new', {
            enquiry: enquiry
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file-document').fileinput('upload');
               //$location.path('enquiries_list/'+$scope.cat+'/0/0');
               enquiry_id = results.enquiry_id;
               $location.path('activity_add/enquiry/'+enquiry_id);
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
                        if ($scope.temptype=='PROP_SUB_TYPE')
                        {
                            controlvalue = "propsubtypes";
                        }
                        if ($scope.temptype=='POSSESSION_STATUS_LOV')
                        {
                            controlvalue = "possession_statuss";
                        }
                        if ($scope.temptype=='FURNITURE')
                        {
                            controlvalue = "furnitures";
                        }
                        if ($scope.temptype=='DIRECTION')
                        {
                            controlvalue = "door_fdirs";
                        }
                        if ($scope.temptype=='PARKING_DIR')
                        {
                            controlvalue = "parkings";
                        }
                        if ($scope.temptype=='CLIENT_SOURCE')
                        {
                            controlvalue = "client_sources";
                        }
                        if ($scope.temptype=='SUB_SOURCE')
                        {
                            controlvalue = "sub_sources";
                        }
                        if ($scope.temptype=='LEAD_STATUS')
                        {
                            controlvalue = "lead_statuss";
                        }
                        if ($scope.temptype=='ENQUIRY_STAGE_LOV')
                        {
                            controlvalue = "enquiry_stages";
                        }
                        $scope.$watch($rootScope[controlvalue], function() {
                            $rootScope[controlvalue] = results;
                        }, true);
                    });
                }, 1000);
            }
        });
    };
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
    $scope.contact_add_new = function (contact) {
        contact.file_name = $("#file_name_company_logo").val();
        //contact.contact_off = "Client";
        console.log("adding:"+contact.contact_off);
        
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                temp_contact_id = results.contact_id;
                console.log(temp_contact_id);
                $('#file_company_logo').fileinput('upload');
                $('#file_visiting_card').fileinput('upload');
                $('#file_contact_pic').fileinput('upload');
                $("#adddeveloper").modal("hide");
                $scope.clientslist = {};
                console.log("after additions:"+$scope.contact.contact_off)
                Data.get('selectcontact/'+$scope.contact.contact_off).then(function (results1) {
                    
                    if ($scope.contact.contact_off == 'Client')
                    {
                        $scope.clientslist = results1;
                        $scope.enquiry.client_id = temp_contact_id;
                    }
                    if ($scope.contact.contact_off == 'Broker')
                    {
                        $scope.brokers = results1;
                        $scope.enquiry.broker_id = temp_contact_id;
                    }
                });
            }
        });
    };


});
    
app.controller('Enquiries_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var enquiry_id = $routeParams.enquiry_id;
    $scope.activePath = null;
    $scope.cat = "";
    
    /*enquiry.internal_comment = $("#internal_comment").val(); 
       enquiryy.external_comment = $("#external_comment").val();*/

    $scope.enable_disable = true;
    $scope.enquiry = {};
    $scope.contact = {};
    
    $scope.modify = function()
    {
        $scope.enable_disable = false;
        $scope.disableAll = false;
    }

    $scope.create_new_enquiry = function(enquiry_id)
    {
        var createenquiry = confirm('This will create new enquiry from Enquiry ID '+enquiry_id);
        if (createenquiry) {

            Data.get('create_new_enquiry/'+enquiry_id).then(function (results) { 
                Data.toast(results);
                newenquiry_id = results.enquiry_id;
                $location.path('enquiries_edit/'+newenquiry_id);
            });
        }
    }
    $timeout(function () { 
        Data.get('selectteams').then(function (results) {
            $scope.teams = results;
        });
    }, 100);


    $timeout(function () { 
        Data.get('selectsubteams').then(function (results) {
            $scope.sub_teams = results;
        });
    }, 100);
    
    
    Data.get('enquiries_edit_ctrl/'+enquiry_id).then(function (results) {
        $scope.cat = results[0].enquiry_off;
        console.log(results[0]);
        $scope.create_rights = false;
        $scope.update_rights = false;
        $scope.delete_rights = false;
        $scope.view_rights = false;
        $scope.export_rights = false;  
      
        $str = ($("#permission_string").val());
        if ($scope.cat=='residential')
        {
            if ((($str).indexOf("enquiries_residential_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("enquiries_residential_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("enquiries_residential_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("enquiries_residential_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if ($scope.cat=='pre-leased')
        {

            if ((($str).indexOf("enquiries_preleased_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("enquiries_preleased_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("enquiries_preleased_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("enquiries_preleased_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if ($scope.cat=='retail')
        {

            if ((($str).indexOf("enquiries_retail_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("enquiries_retail_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("enquiries_retail_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("enquiries_retail_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if ($scope.cat=='commercial')
        {

            if ((($str).indexOf("enquiries_commercial_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("enquiries_commercial_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("enquiries_commercial_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("enquiries_commercial_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if ($scope.cat=='others')
        {

            if ((($str).indexOf("enquiries_others_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("enquiries_others_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("enquiries_others_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("enquiries_others_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if (!$scope.update_rights)
        {
            $scope.enquiry= {};
            alert("You don't have rights to use this option..");
            return;
        }
        $timeout(function () { 
            Data.get('selecttask').then(function (results) {
                $scope.task_list = results;
            });
        }, 100);
        if (results[0].teams)
        {
            $scope.arr = ((results[0].teams).split(','));
            results[0].teams = $scope.arr;
        }

        if (results[0].sub_teams)
        {
            $scope.arr = ((results[0].sub_teams).split(','));
            results[0].sub_teams = $scope.arr;
        }
        if (results[0].preferred_area_id)
        {
            $scope.arr = ((results[0].preferred_area_id).split(','));
            results[0].preferred_area_id = $scope.arr;
        }
        if (results[0].preferred_locality_id)
        {
            $scope.arr = ((results[0].preferred_locality_id).split(','));
            results[0].preferred_locality_id = $scope.arr;
        }
        if (results[0].assigned)
        {
            $scope.arr = ((results[0].assigned).split(','));
            results[0].assigned = $scope.arr;
        }
        if (results[0].source_channel)
        {
            $scope.arr = ((results[0].source_channel).split(','));
            results[0].source_channel = $scope.arr;
        }
        if (results[0].subsource_channel)
        {
            $scope.arr = ((results[0].subsource_channel).split(','));
            results[0].subsource_channel = $scope.arr;
        }
        if (results[0].groups)
        {
            $scope.arr = ((results[0].groups).split(','));
            results[0].groups = $scope.arr;
        }
        if (results[0].amenities_avl)
        {
            $scope.arr = ((results[0].amenities_avl).split(','));
            results[0].amenities_avl = $scope.arr;
        }
        if (results[0].parking)
        {
            $scope.arr = ((results[0].parking).split(','));
            results[0].parking = $scope.arr;
        }
        if (results[0].con_status)
        {
            $scope.arr = ((results[0].con_status).split(','));
            results[0].con_status = $scope.arr;
        }
   
        $scope.$watch($scope.enquiry, function() {
            $scope.enquiry = {
                enquiry_off:results[0].enquiry_off,
                amenities_avl:results[0].amenities_avl,
                area_para:results[0].area_para,
                assigned:results[0].assigned,
                bath:results[0].bath,
                bedrooms:results[0].bedrooms,
                broker_id:results[0].broker_id,
                broker_involved:results[0].broker_involved,
                budget_range1:(results[0].budget_range1),
                budget_range2:(results[0].budget_range2),
                budget_range1_para:results[0].budget_range1_para,
                budget_range2_para:results[0].budget_range2_para,
                campaign:results[0].campaign,
                carp_area1:results[0].carp_area1,
                carp_area2:results[0].carp_area2,
                carp_area_para:results[0].carp_area_para,
                client_id:results[0].client_id,
                con_status:results[0].con_status,
                con_status:results[0].con_status,
                depo_range1:(results[0].depo_range1),
                depo_range2:(results[0].depo_range2),
                depo_range1_para:results[0].depo_range1_para,
                depo_range2_para:results[0].depo_range2_para,
                dt_poss_max:results[0].dt_poss_max,
                dt_poss_min:results[0].dt_poss_min,
                email_salestrainee:results[0].email_salestrainee,
                enquiry_for:results[0].enquiry_for,
                source_from:results[0].source_from,
                enquiry_id:results[0].enquiry_id,
                floor_range1:results[0].floor_range1,
                floor_range2:results[0].floor_range2,
                frontage:results[0].frontage,
                watersup:results[0].watersup,
                powersup:results[0].powersup,
                furniture:results[0].furniture,
                groups:results[0].groups,
                height:results[0].height,
                intrnal_comment:results[0].intrnal_comment,
                lease_period:results[0].lease_period,
                loan_req:results[0].loan_req,
                portal_id:results[0].portal_id,
                pre_leased:results[0].pre_leased,
                preferred_area_id:results[0].preferred_area_id,
                preferred_city:results[0].preferred_city,
                preferred_country:results[0].preferred_country,
                preferred_locality_id:results[0].preferred_locality_id,
                preferred_project_id:results[0].preferred_project_id,
                preferred_state:results[0].preferred_state,
                priority:results[0].priority,
                pro_alerts:results[0].pro_alerts,
                enquiry_type:results[0].enquiry_type,
                enquiry_sub_type:results[0].enquiry_sub_type,
                lockin_period:results[0].lockin_period,
                lifts:results[0].lifts,
                floors:results[0].floors,
                tenant:results[0].tenant,
                tenant_other:results[0].tenant_other,
                tenant_vicinity:results[0].tenant_vicinity,
                reg_date:results[0].reg_date,
                sale_area1:results[0].sale_area1,
                sale_area2:results[0].sale_area2,
                share_agsearch:results[0].share_agsearch,
                sms_salestrainee:results[0].sms_salestrainee,
                source_channel:results[0].source_channel,
                stage:results[0].stage,
                status:results[0].status,
                subscr_email:results[0].subscr_email,
                subsource_channel:results[0].subsource_channel,
                teams:results[0].teams,
                sub_teams:results[0].sub_teams,
                tot_area1:results[0].tot_area1,
                tot_area2:results[0].tot_area2,
                tot_para:results[0].tot_para,
                vastu_comp:results[0].vastu_comp,
                created_by:results[0].created_by,
                created_date:results[0].created_date,
                zip:results[0].zip,
                mainroad:results[0].mainroad,
                internalroad:results[0].internalroad,
                door_fdir:results[0].door_fdir,
                parking:results[0].parking,
                car_park:results[0].car_park,
                portal_name:results[0].portal_name,
                tenant_name:results[0].tenant_name,
                occ_details:results[0].occ_details,
                rented_area:results[0].rented_area,
                roi:results[0].roi,
                lease_start:results[0].lease_start,
                lease_end:results[0].lease_end,
                rent_per_sqft:results[0].rent_per_sqft,
                monthle_rent:results[0].monthle_rent,
                pre_leased_rent:results[0].pre_leased_rent,
                cam_charges:results[0].cam_charges,
                fur_charges:results[0].fur_charges,
                distfrm_station:results[0].distfrm_station,
                distfrm_dairport:results[0].distfrm_dairport,
                distfrm_school:results[0].distfrm_school,
                distfrm_market:results[0].distfrm_market,
                distfrm_highway:results[0].distfrm_highway,
                price_unit:(results[0].price_unit),
                price_unit_carpet:(results[0].price_unit_carpet,results[0]),
                price_unit_para:results[0].price_unit_para,
                price_unit_carpet_para:results[0].price_unit_carpet_para,
                task_id:results[0].task_id
            };
            if (results[0].broker_involved=="1")
            {
                $('#broker_involved').prop('checked', true);
            }
            if (results[0].vastu_comp=="1")
            {
                $('#vastu_comp').prop('checked', true);
            }
            
            if (results[0].tenant=="1")
            {
               
                $('#tenant').prop('checked', true);
            }

            if (results[0].sms_salestrainee=="1")
            {
                $('#sms_salestrainee').prop('checked', true);
            }
            if (results[0].email_salestrainee=="1")
            {
                $('#email_salestrainee').prop('checked', true);
            }

            Data.get('enquiry_images/'+enquiry_id).then(function (results) {
                $rootScope.enquiry_images = results;
            });

            Data.get('enquiry_document_images/'+enquiry_id).then(function (results) {
                $scope.enquiry_document_images = results;
                
            });
            activate_fileinput_enquiry();

            
        }, true);
        
        
        $scope.cat = results[0].enquiry_off;

        Data.get('change_enquirysubtype/'+results[0].enquiry_type).then(function (results) { 
            $rootScope.enquirysubtypes = results; 
        });
        Data.get('selectdropdownsNew/PROP_SUB_TYPE/'+results[0].enquiry_off).then(function (results) { 
            $rootScope.propsubtypes = results; 
        }); 

    });
    
    
    $scope.change_enquirysubtype = function (enquiry_type) 
    { 
        Data.get('change_enquirysubtype/'+enquiry_type).then(function (results) { 
            $rootScope.enquirysubtypes = results; 
        });
    }
    $scope.AddDeveloper = function (contact_off) 
    {  
        $scope.contact.contact_off = contact_off;
        console.log($scope.contact.contact_off);
    }
    
    $timeout(function () { 
        Data.get('selectdropdowns/POSSESSION_STATUS_LOV').then(function (results) {
            $rootScope.possession_statuss = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/FURNITURE').then(function (results) {
            $rootScope.furnitures = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/DIRECTION').then(function (results) {
            $rootScope.door_fdirs = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/AMENITIES').then(function (results) {
            $rootScope.amenities = results;
        });
    }, 100);
    
    $timeout(function () { 
        Data.get('selectdropdowns/PARKING_DIR').then(function (results) {
            $rootScope.parkings = results;
        });
    }, 100);


    $timeout(function () { 
        Data.get('selectcontact/Client').then(function (results) {
            $rootScope.clientslist = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectcontact/Broker').then(function (results) {
            $scope.brokers = results;
        });
    }, 100);

    
    $timeout(function () { 
        Data.get('selectlocality').then(function (results) {
            $scope.localities_list = results;
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


    
    $scope.calculate = function(field_name)
    {
    
        if ($scope.enquiry.enquiry_for=="Lease")
        {
            if (field_name == "deposite_month")
            {
                if ($scope.enquiry.deposite_month>0)
                {
                    $scope.enquiry.security_depo = ($scope.enquiry.deposite_month*$scope.enquiry.exp_price).toFixed(2);
                }
            }
        }
        
    }
    
    $timeout(function () { 
        Data.get('selectdropdowns/CLIENT_SOURCE').then(function (results) {
            $rootScope.client_sources = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/SUB_SOURCE').then(function (results) {
            $rootScope.sub_sources = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/LEAD_STATUS').then(function (results) {
            $rootScope.lead_statuss = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/ENQUIRY_STAGE_LOV').then(function (results) {
            $rootScope.enquiry_stages = results;
        });
    }, 100);

    $scope.showlocality = function (field_name,value) 
    {  

        if (field_name=='locality_id')
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+value).then(function (results) {
                    $scope.enquiry.preferred_area_id = results[0].area_id;
                    $scope.enquiry.preferred_city = results[0].city;
                    $scope.enquiry.preferred_state = results[0].state;
                    $scope.enquiry.preferred_country = results[0].country;
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
                    $scope.enquiry.preferred_city = results[0].city;
                    $scope.enquiry.preferred_state = results[0].state;
                    $scope.enquiry.preferred_country = results[0].country;
                });
            }, 100);
        }
    }
    $scope.checklowhigh = function()
    {
        if (parseFloat($scope.enquiry.carp_area1)>0)
        {
            if (parseFloat($scope.enquiry.carp_area2)>0)
            {
                if (parseFloat($scope.enquiry.carp_area1)>parseFloat($scope.enquiry.carp_area2))
                {
                    alert("Carpet Area Range should be from Lowest to Highest ..!!");
                }
            }
        }
        if (parseFloat($scope.enquiry.budget_range1)>0)
        {
            if (parseFloat($scope.enquiry.budget_range2)>0)
            {
                if (parseFloat($scope.enquiry.budget_range1)>parseFloat($scope.enquiry.budget_range2))
                {
                    alert("Budget Range should be from Lowest to Highest ..!!");
                }
            }
        }
    }
    $scope.enquiries_update = function (enquiry) {
        enquiry.intrnal_comment = $("#intrnal_comment").val(); 
        enquiry.file_name = $("#file_name").val();
        enquiry.file_document_name = $("#file_document_name").val();
        Data.post('enquiries_update', {
            enquiry: enquiry
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file-1').fileinput('upload');
                $('#file-document').fileinput('upload');
               $location.path('enquiries_list/'+$scope.cat+'/0/0');
            }
        });
    };
    
    $scope.enquiries_delete = function (enquiry) {
        //console.log(business_unit);
        var deleteenquiry = confirm('Are you absolutely sure you want to delete?');
        if (deleteenquiry) {
            Data.post('enquiries_delete', {
                enquiry: enquiry
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('enquiries_list/'+$scope.cat+'/0/0');
                }
            });
        }
    };
    $scope.removeimage = function (attachment_id) {
        var deleteproduct = confirm('Are you absolutely sure you want to delete?');
        if (deleteproduct) {
            Data.get('removeimage/'+attachment_id).then(function (results) {
                Data.toast(results);
                Data.get('enquiry_document_images/'+enquiry_id).then(function (results) {
                    $scope.enquiry_document_images = results;
                });
            });
        }
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
                        if ($scope.temptype=='PROP_SUB_TYPE')
                        {
                            controlvalue = "propsubtypes";
                        }
                        if ($scope.temptype=='POSSESSION_STATUS_LOV')
                        {
                            controlvalue = "possession_statuss";
                        }
                        if ($scope.temptype=='FURNITURE')
                        {
                            controlvalue = "furnitures";
                        }
                        if ($scope.temptype=='DIRECTION')
                        {
                            controlvalue = "door_fdirs";
                        }
                        if ($scope.temptype=='PARKING_DIR')
                        {
                            controlvalue = "parkings";
                        }
                        if ($scope.temptype=='CLIENT_SOURCE')
                        {
                            controlvalue = "client_sources";
                        }
                        if ($scope.temptype=='SUB_SOURCE')
                        {
                            controlvalue = "sub_sources";
                        }
                        if ($scope.temptype=='LEAD_STATUS')
                        {
                            controlvalue = "lead_statuss";
                        }
                        if ($scope.temptype=='ENQUIRY_STAGE_LOV')
                        {
                            controlvalue = "enquiry_stages";
                        }
                        $scope.$watch($rootScope[controlvalue], function() {
                            $rootScope[controlvalue] = results;
                        }, true);
                    });
                }, 1000);
            }
        });
    };
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

    $scope.contact_add_new = function (contact) {
        contact.file_name = $("#file_name_company_logo").val();
        //contact.contact_off = "Client";
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                temp_contact_id = results.contact_id;
                $('#file_company_logo').fileinput('upload');
                $('#file_visiting_card').fileinput('upload');
                $('#file_contact_pic').fileinput('upload');
                $("#adddeveloper").modal("hide");
                $scope.clientslist = {};
                Data.get('selectcontact/'+$scope.contact.contact_off).then(function (results) {
                    if ($scope.contact.contact_off == 'Client')
                    {
                        $scope.clientslist = results;
                        $scope.enquiry.client_id = temp_contact_id;
                    }
                    if ($scope.contact.contact_off == 'Broker')
                    {
                        $scope.brokers = results;
                        $scope.enquiry.broker_id = temp_contact_id;
                    }
                });
            }
        });
    };

    
});
    
app.controller('SelectEnquiry', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectenquiry').then(function (results) {
            $rootScope.enquiries = results;
        });
    }, 100);
});


function activate_fileinput_enquiry()
{
    var footerTemplate = '<div class="file-thumbnail-footer" style ="height:94px">\n' +
    '  <input class="kv-input kv-new form-control input-sm form-control-sm text-center {TAG_CSS_NEW}"  value="" placeholder="Enter Title...">\n' +
    '  <input class="kv-input kv-new  {TAG_CSS_NEW}" type="checkbox" value="true" >Set as main image' +
    '  <input class="kv-input kv-new  {TAG_CSS_NEW}" type="checkbox" value="true" checked>Share' +
    '<select id="file_category" name="file_category" class="kv-input kv-new form-control input-sm form-control-sm text-center {TAG_CSS_NEW}">'+
                    '<option value="">Select Category</option>'+
                    '<option value="Not Specified">Not Specified</option>'+
                    '</select>\n'+

    '   <div class="small" style="margin:15px 0 2px 0">{size}</div> {progress}\n{indicator}\n{actions}\n' +
    '</div>';


    $("#file-document").fileinput({
        uploadAsync: false,
        showUpload: false,
        uploadUrl: './api/v1/enquiry_document_uploads',
        allowedFileExtensions : ['jpg','jpeg', 'png','gif','image','3gp','xlsx','docx','pdf','pptx'],
        overwriteInitial: false,
        maxFileSize: 15000000,
        maxFilesNum: 25,
        maxFileCount:100,
        layoutTemplates: {footer: footerTemplate},

        uploadExtraData: function() {  
            var out = {}, key, i = 0,j=1,z=1;
            $('.kv-input:visible').each(function() {
                $el = $(this);
                value = $el.val();
                if (j==1)
                {
                    key = 'file_title_'+z;
                }
                if (j==2)
                {
                    key = 'main_image_'+z;
                    if ($(this).is(':checked'))
                    {
                        value = true;
                    }
                    else{
                        value = false;
                    }
                }
                if (j==3)
                {
                    key = 'share_on_web_'+z;
                    if ($(this).is(':checked'))
                    {
                        value = true;
                    }
                    else{
                        value = false;
                    }
                }
                if (j==4)
                {
                    key = 'file_category_'+z;
                    j=0;
                    z++;
                }
                j++;
                //key = $el.hasClass('kv-new') ? 'new_' + i : 'init_' + i;
                out[key] = value;
                i++;
            });
            return out;
        },
        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });

    $('#file-document').on('fileloaded', function(event, file, previewId, index, reader) {
        $("#file_document_name").val(file.name);
        console.log("fileloaded");
    });
    
    
    
    
    $("#file-1").fileinput({
        uploadAsync: false,
        showUpload: false,
        uploadUrl: './api/v1/enquiry_uploads',
        allowedFileExtensions : ['jpg','jpeg', 'png','gif','image','3gp','docx','pptx'],
        overwriteInitial: false,
        maxFileSize: 15000000,
        maxFilesNum: 25,
        maxFileCount:100,
        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });

    $('#file-1').on('fileloaded', function(event, file, previewId, index, reader) {
        $("#file_name").val(file.name);
        console.log("fileloaded");
    });


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
