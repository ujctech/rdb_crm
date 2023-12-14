// PROJECT 

app.controller('Project_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {


    var id = $routeParams.id;
    $scope.id = id;

    $scope.showAmount = numDifferentiation;
    $scope.searchdata = {};
    $scope.project = {};
    $scope.listprojects = {};
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
            Data.get('project_list_ctrl/'+id+'/'+$scope.next_page_id).then(function (results) {
                $scope.listprojects =  {};
                $scope.listprojects = results;
                $scope.page_range = parseInt($scope.next_page_id)+1+" - ";
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                $scope.page_range = $scope.page_range + $scope.next_page_id;
            });
        }
        else
        {
            $scope.search_projects($scope.searchdata,'pagenavigation');
            
        }
    }


    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("project_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("project_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("project_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("project_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.view_rights)
    {
        $scope.listprojects = {};
        alert("You don't have rights to use this option..");
        return;
    }
    Data.get('project_list_ctrl/'+id+'/'+$scope.next_page_id).then(function (results) {
        $scope.listprojects = {};
        $scope.listprojects = results;
        $scope.next_page_id = 30;
        $scope.project_count = results[0].project_count;
        $scope.total_records = results[0].project_count;
    });
    $timeout(function () { 
        Data.get('mproject_list/0').then(function (results) {
            $scope.mprojects = results;
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
                Data.get('selectdropdowns/PROJ_STATUS').then(function (results) {
                    $scope.proj_statuss = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectdropdowns/PROJ_TYPE').then(function (results) {
                    $scope.proj_types = results;
                });
            }, 100);

            
            

            $timeout(function () { 
                Data.get('selectdropdowns/AMENITIES').then(function (results) {
                    $scope.amenities = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectcontact/Developer').then(function (results) {
                    $scope.developers = results;
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
                Data.get('selectdropdowns/PROP_STATUS').then(function (results) {
                    $scope.prop_statuss = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('getdatavalues_project/bedrooms').then(function (results) {
                    $scope.bedrooms = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_project/project_id').then(function (results) {
                    $scope.project_ids = results;
                });
            }, 100);
            
            $timeout(function () { 
                Data.get('getdatavalues_project/project_name').then(function (results) {
                    $scope.project_names = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_project/numof_floor').then(function (results) {
                    $scope.numof_floors = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('selectarea').then(function (results) {
                    $scope.areas = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectcity').then(function (results) {
                    $scope.cities = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectlocality').then(function (results) {
                    $scope.localities = results;
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

            $timeout(function () { 
                Data.get('selectusers').then(function (results) {
                    $scope.users = results;
                });
            }, 100);
        }
    }

    $scope.select_assign_to = function(teams,sub_teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/'+sub_teams).then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }

    
    $scope.share_on_website = function(project_id)
    {
        var shareonwebsite = confirm('Are you Sure?');
        if (shareonwebsite) {
            Data.get('share_on_website/project/'+project_id).then(function (results) {
            });
        }
    }

    $scope.configurations = function(project_id)
    {
        $timeout(function () { 
            Data.get('getconfigurations/'+project_id).then(function (results) {
                $scope.html = results[0].htmlstring;
                var cstring = 'trustedHtml_'+project_id;
                $scope[cstring] = $sce.trustAsHtml($scope.html);
            });
        }, 100);
    }

    $scope.me_count = function(property_id)
    {
        $timeout(function () { 
            Data.get('me_count/'+property_id).then(function (results) {
                console.log(results[0].me_count);
                $("#me"+property_id).html(results[0].me_count);
            });
        }, 100);

    }

    $scope.matchingenquiries = function(project_id)
    {
        $timeout(function () { 
            Data.get('matchingenquiries/'+project_id).then(function (results) {
                $scope.html = results[0].htmlstring;
                var cstring = 'enqtrustedHtml_'+project_id;
                $scope[cstring] = $sce.trustAsHtml($scope.html);
            });
        }, 100);
    }
    
    $scope.methods = {};
    $scope.images = {};
    $scope.conf = {
        imgAnim : 'fadeup'
    };
    $scope.showimages = function(project_id,project_heading)
    {
        
        Data.get('project_imagesslide/'+project_id).then(function (results) {
            $scope.project_heading = project_heading;
            $rootScope.images = results;
            $scope.images = results;
        });
        $scope.methods.open();
		
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
            $location.path('audit_trail/project/project_id/'+data);
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
            

            $location.path('batch_update/project/dummy/project_id/'+data);
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
            $location.path('manage_group/project/project_id/'+data);
        }
    }


    $scope.reports = function (report_type)
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
            if (report_type=='report')
            {
                $location.path('reports_project/project/project_id/'+data);
            }
            if (report_type=='broucher')
            {
                $location.path('send_broucher/project/project_id/'+data);
            }

            if (report_type=='one_mailer')
            {
                $location.path('project_one_mailer/project/project_id/'+data);
            }
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
                Data.get('getreport_fields/project/unselected').then(function (results) {
                    $rootScope.unselected_fields = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getreport_fields/project/selected').then(function (results) {
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
                Data.get('getreport_fields/project/unselected').then(function (results) {
                    $rootScope.unselected_fields = results;
                });
            }, 100);
    
            $timeout(function () { 
                Data.get('getreport_fields/project/selected').then(function (results) {
                    $rootScope.selected_fields = results;
                });
            }, 100);
        });
    }

    $scope.option_value = "current_page";
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
            Data.get('exportdata/project/project_id/'+data+'/'+$scope.option_value).then(function (results) {
                window.location="api//v1//uploads//project_list.xlsx";
            });
        }, 100);
    }

   $timeout(function () { 
        Data.get('project_count').then(function (results) {
            $scope.project_count = results[0].project_count;
            $scope.total_records = results[0].project_count;
            /*console.log($scope.total_records);*/
        });
    }, 100);


    $scope.search_projects = function (searchdata,from_click) 
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

        Data.post('newsearch_projects', {
            searchdata: searchdata
        }).then(function (results) {
            
            if (results[0].project_count>0)
            {
                $scope.listprojects = {};
                $scope.listprojects = results;
                $scope.project_count = results[0].project_count;
                $scope.total_records = results[0].project_count;
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                $scope.page_range = $scope.page_range + $scope.next_page_id;
            }
            else
            {
                alert("Search Criteria Not Matching.. !!");
            }

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

       /* $("select").each(function () { //added a each loop here
            $(this).select2('val', '')
        });*/

        $("li.select2-selection__choice").remove();
        $(".select2").each(function() { $(this).val([]); });

        $scope.next_page_id = 0;
       
        Data.get('project_list_ctrl/0/0').then(function (results) {
            $scope.listprojects = {};
            $scope.listprojects = results;
            
            $scope.next_page_id = 30;
            $scope.project_count = results[0].project_count;
            $scope.total_records = results[0].project_count;
        });

    }

});
    
    
app.controller('Project_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, Data,$sce) {
    
    $scope.project = {};
    $scope.contact = {};

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("project_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("project_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("project_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("project_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.create_rights)
    {
        $scope.project = {};
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
        Data.get('selectusers').then(function (results) {
            $rootScope.users = results;
        });
    }, 100);

    /*$scope.$watch($scope.project.assign_to, function() {
        $scope.project.assign_to = $("#logged_user_id").val();
        
    }, true);*/

    $scope.$watch($scope.project.assign_to, function() {
        $scope.project.assign_to = [$rootScope.user_id];
    });
    
    $scope.$watch($scope.project.teams, function() {
        $scope.project.teams = [$rootScope.bo_id];
    });

    $scope.$watch($scope.project.sub_teams, function() {
        $scope.arr = (([$rootScope.sub_teams]).toString());
        console.log($scope.arr);
        $scope.project.sub_teams = [$scope.arr];
    });

    $timeout(function () { 
        $("#assign_to").select2();
        //alert($scope.project.assign_to);
    },2000);

    $timeout(function () { 
        Data.get('selectdropdowns/PROJ_STATUS').then(function (results) {
            $rootScope.proj_status = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selecttask').then(function (results) {
            $scope.task_list = results;
        });
    }, 100);


    $timeout(function () { 
        Data.get('selectdropdowns/PROJ_TYPE').then(function (results) {
            $rootScope.proj_type = results;
        });
    }, 100);

    

    $timeout(function () { 
        Data.get('selectdropdowns/AMENITIES').then(function (results) {
            $rootScope.amenities = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectcontact/Developer').then(function (results) {
            $rootScope.contacts = results;
        });
    }, 100);
    
    $timeout(function () { 
        Data.get('selectdropdowns/BANK_LOANS').then(function (results) {
            $rootScope.bank_loans = results;
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
        Data.get('selectdropdowns/PROP_STATUS').then(function (results) {
            $rootScope.prop_statuss = results;
        });
    }, 100);

    $scope.getdeveloper_names = function(developer_name,cat)
    {
        if (developer_name==' ' || developer_name =='')
        {
            developer_name = "blank";
        }
        $timeout(function () { 
            Data.get('getdeveloper_names/'+developer_name+'/'+cat).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_developer = $sce.trustAsHtml($scope.html);
                $(".mydropdown-menu").css("display","block");
            });
        }, 100);
    };

    
    $scope.select_assign_to = function(teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/0').then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }



    $scope.getdevelopername = function(contact_id,name)
    {
        $scope.project.developer_id = contact_id;
        $scope.project.developer_name = name;
        console.log(contact_id);
        console.log(name);
        console.log($scope.project.contact_id);
        console.log($scope.project.developer_name);
        
        $(".mydropdown-menu").css("display","none");
        $timeout(function () { 
            Data.get('getproject_exists/'+contact_id).then(function (results) {
                if (results[0].count>0)
                {
                    $scope.html = results[0].htmlstring;
                    $scope.trustedHtml_project_exists = $sce.trustAsHtml($scope.html);
                    $("#project_exists").css("display","block");
                }
                else
                {
                    $("#project_exists").css("display","none");
                }
            });
        }, 100);

    }



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

    $scope.calculate_loading = function(field_name)
    {

        if (field_name == "sale_area")
        {
            if ($scope.multiproperty.carp_area>0)
            {
                diff = $scope.multiproperty.sale_area - $scope.multiproperty.carp_area;
                $scope.multiproperty.loading = (diff * (100 / $scope.multiproperty.carp_area)).toFixed(2);
            }
        }
        if (field_name == "carp_area")
        {
            if ($scope.multiproperty.sale_area>0)
            {
                diff = $scope.multiproperty.sale_area - $scope.multiproperty.carp_area;
                $scope.multiproperty.loading = (diff * (100 / $scope.multiproperty.carp_area)).toFixed(2);
            }
        }
        if (field_name == "loading")
        {
            if ($scope.multiproperty.sale_area>0)
            {
                diff = ((100+parseFloat($scope.multiproperty.loading))/100);
                $scope.multiproperty.carp_area = ($scope.multiproperty.sale_area / diff).toFixed(2);

            }
        }
        if (field_name == "exp_price_para")
        {
            $scope.multiproperty.price_unit_para = $scope.multiproperty.exp_price_para;
            $scope.multiproperty.price_unit_carpet_para = $scope.multiproperty.exp_price_para;
        }
        if (field_name == "price_unit_para")
        {
            $scope.multiproperty.price_unit_carpet_para = $scope.multiproperty.price_unit_para;
            $scope.multiproperty.exp_price_para = $scope.multiproperty.price_unit_para;
        }

        if (field_name == "price_unit_carpet_para")
        {
            $scope.multiproperty.price_unit_para = $scope.multiproperty.price_unit_carpet_para;
            $scope.multiproperty.exp_price_para = $scope.multiproperty.price_unit_carpet_para;
        }


        if ($scope.project.property_for=="Sale")
        {
        
            if (field_name == "exp_price")
            {
                if ($scope.multiproperty.exp_price>0 && $scope.multiproperty.sale_area>0)
                {
                    $scope.multiproperty.price_unit = ($scope.multiproperty.exp_price/$scope.multiproperty.sale_area).toFixed(2); 
                    $scope.multiproperty.price_unit_para = $scope.multiproperty.exp_price_para;
                }
                if ($scope.multiproperty.exp_price>0 && $scope.multiproperty.carp_area>0)
                {
                    $scope.multiproperty.price_unit_carpet = ($scope.multiproperty.exp_price/$scope.multiproperty.carp_area).toFixed(2); 
                    $scope.multiproperty.price_unit_carpet_para = $scope.multiproperty.exp_price_para;
                }
            }

            if (field_name == "price_unit")
            {
                if ($scope.multiproperty.sale_area>0 && $scope.multiproperty.price_unit>0)
                {
                    $scope.multiproperty.exp_price = ($scope.multiproperty.sale_area*$scope.multiproperty.price_unit).toFixed(2);
                    $scope.multiproperty.exp_price_para = $scope.multiproperty.price_unit_para;
                }
                if ($scope.multiproperty.carp_area>0 && $scope.multiproperty.price_unit>0)
                {
                    $scope.multiproperty.price_unit_carpet = ($scope.multiproperty.exp_price/$scope.multiproperty.carp_area).toFixed(2); 
                    $scope.multiproperty.price_unit_carpet_para = $scope.multiproperty.price_unit_para;

                }
            }
        }
        if ($scope.project.project_for=="Rent")
        {
        
            if (field_name == "exp_price")
            {
                if ($scope.multiproperty.exp_price>0 && $scope.multiproperty.sale_area>0)
                {
                    $scope.multiproperty.price_unit = ($scope.multiproperty.exp_price/$scope.multiproperty.sale_area).toFixed(2); 
                    $scope.multiproperty.price_unit_area_para = $scope.multiproperty.exp_price_para;
                }
                if ($scope.multiproperty.exp_price>0 && $scope.multiproperty.carp_area>0)
                {
                    $scope.multiproperty.price_unit_carpet = ($scope.multiproperty.exp_price/$scope.multiproperty.carp_area).toFixed(2); 
                    $scope.multiproperty.price_unit_carpet_para = $scope.multiproperty.exp_price_para;
                }
            }

            if (field_name == "price_unit")
            {
                if ($scope.multiproperty.sale_area>0 && $scope.multiproperty.price_unit>0)
                {
                    $scope.multiproperty.exp_price = ($scope.multiproperty.sale_area*$scope.multiproperty.price_unit).toFixed(2); 
                    $scope.multiproperty.exp_price_para = $scope.multiproperty.price_unit_para;
                }
                if ($scope.multiproperty.carp_area>0 && $scope.multiproperty.price_unit>0)
                {
                    $scope.multiproperty.price_unit_carpet = ($scope.multiproperty.carp_area*$scope.multiproperty.price_unit).toFixed(2); 
                    $scope.multiproperty.price_unit_carpet_para = $scope.multiproperty.price_unit_para;
                }
            }
        }

    }

    $scope.geocodeAddress = function (field_name,value) 
    {  

        if (field_name=='locality_id')
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+value).then(function (results) {
                    $scope.project.area_id = results[0].area_id;
                    $scope.project.city = results[0].city;
                    $scope.project.state = results[0].state;
                    $scope.project.country = results[0].country;
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
                    $scope.project.city = results[0].city;
                    $scope.project.state = results[0].state;
                    $scope.project.country = results[0].country;
                });
            }, 100);
        }

        address = $('#'+field_name+' :selected').text();

        if (field_name=='add1')
        {
            if ($scope.project.add1)
            {
                address = $('#add1').val();
            }
            else{
                return;
            }
        }

        if (field_name=='exlocation')
        {
            if ($scope.project.exlocation)
            {
                address = $('#add1').val();
            }
            else{
                return;
            }
        }

        if (address != "None") 
        {
		    geocoder.geocode({
		        'address': address
            }, function(results, status) 
            {
			    if (status === 'OK') 
                {
                    //$scope.currentaddress=results[0].formatted_address;
                    document.getElementById('address').innerHTML = results[0].formatted_address;

                    var myLatLng = {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()};
                    
                    /*var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map,
                        draggable: true,
                        title: address
                    });*/
                    marker.setPosition(results[0].geometry.location);
                    updateMarkerAddress(results[0].formatted_address);
                    if (results[0].geometry.viewport) 
                    {
                        map.fitBounds(results[0].geometry.viewport);
                    } else 
                    {
                        map.fitBounds(results[0].geometry.bounds);
                    }
                    
                } else 
                {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
		    });
        } 
        else 
        { // set back to initial zoom and center
		    map.setOptions({
		        center: new google.maps.LatLng(40.685646, -76.195499),
		        zoom: 40
		    });
        }

    };

    
    $scope.project_add_new = function (project) {
        project.internal_comment = $("#internal_comment").val(); 
        project.external_comment = $("#external_comment").val();
        project.file_name = $("#file_name").val();
        Data.post('project_add_new', {
            project: project
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
		        project_id = results.project_id;
                $('#file-1').fileinput('upload');
                $('#file_occu').fileinput('upload');
                $('#file_videos').fileinput('upload');
                $location.path('project_edit/'+project_id);
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
                        if ($scope.temptype=='PROP_STATUS')
                        {
                            controlvalue = "prop_statuss";
                        }

                        if ($scope.temptype=='PRJ_SPECIFICATIONS')
                        {
                            controlvalue = "pro_specifications";
                        }
                        $rootScope[controlvalue] = {};
                        $scope.$watch($rootScope[controlvalue], function() {
                            $rootScope[controlvalue] = results;
                        }, true);
                    });
                }, 1000);
            }
        });
    };

    $scope.contact_add_new = function (contact) {
        contact.contact_off = "Developer";
        contact.file_name = $("#file_name_company_logo").val();
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                developer_id = results.contact_id;
                $('#file_company_logo').fileinput('upload');
                $('#file_visiting_card').fileinput('upload');
                $('#file_contact_pic').fileinput('upload');
                $("#adddeveloper").modal("hide");
                $timeout(function () {
                    $rootScope.contacts = {};
                    Data.get('selectcontact/Developer').then(function (results) {
                        $rootScope.contacts = results;
                    });
                },3000);
                $scope.$watch($scope.project.developer_id, function() {
                    $scope.project.developer_id = developer_id;
                }, true);
                $scope.$watch($scope.contact, function() {
                    $scope.contact = {};
                }, true);

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

app.controller('CreateReportsProject', function ($scope, $rootScope, $routeParams, $location, $http, Data, $sce,$timeout) {
    //$("body").addClass("sidebar-collapse");
    $("#biglogo").css("height","50px");
    $("#logobig").css("height","50px");
    $scope.image_captured = "No";
    var module_name = $routeParams.module_name;
    var id = $routeParams.id;
    var data = $routeParams.data;

    $scope.module_name = module_name;
    $scope.activePath = null;
    $scope.slides = {};
    $scope.slidedata = {};
    Data.get('project_images/'+data).then(function (results) {
        $scope.project_images_slide = results;
    });
    $scope.slides.category = "";
    $scope.slides.category_id = 0;
    $scope.arrTabRows = {};
    var url;
    Data.get('createreportsproject/'+module_name+'/'+id+'/'+data).then(function (results) {
        $scope.slide_data = results;
        $scope.arrTabRows  = results;
        $("#slide_view_1").css("display","block");
        $scope.slides.proptype = results[0].proptype;
        $scope.slides.category = results[0].category;
        $scope.slides.category_id = results[0].category_id;
        $scope.slides.carp_area = results[0].carp_area;
        $scope.slides.sale_area = results[0].sale_area;
        $scope.slides.floor = results[0].floor;
        $scope.slides.car_park = results[0].car_park;
        $scope.slides.price_unit = results[0].price_unit;
        $scope.slides.security_depo = results[0].security_depo;
        $scope.slides.lock_per = results[0].lock_per;
        $scope.slides.lease_end = results[0].lease_end;
        $scope.slides.escalation_lease = results[0].escalation_lease;
        $scope.slides.location = results[0].location;
        $scope.slides.furnishing = results[0].furnishing;
        $scope.slides.owner_name = results[0].owner_name;
        $scope.slides.mob_no = results[0].mob_no;
        $scope.slides.email = results[0].email;

        $scope.slides.project_contact = results[0].project_contact;
        $scope.slides.usermobileno = results[0].usermobileno;
        $scope.slides.useremail	 = results[0].useremail;

        $scope.slides.attachment_id = results[0].attachment_id;
        $scope.slides.main_image = results[0].filenames;
        $scope.slides.description = results[0].description;

        $scope.slides.area_id = results[0].area_id;
        $scope.slides.area_name = results[0].area_name;
        $scope.slides.locality_id = results[0].locality_id;
        $scope.slides.locality = results[0].locality;
        $scope.slides.propsubtype = results[0].propsubtype;
        $scope.slides.suitable_for = results[0].suitable_for;

        $scope.slides.internal_comment = results[0].internal_comment; 
        $scope.slides.external_comment = results[0].external_comment;
        $scope.slides.prop_tax = results[0].prop_tax;
        $scope.slides.cam_charges = results[0].cam_charges;
        $scope.slides.monthle_rent = results[0].monthle_rent;
        $scope.slides.ag_tenure = results[0].ag_tenure;
        $scope.slides.rent_esc = results[0].rent_esc;
        $scope.slides.lease_start = results[0].lease_start;


        $scope.slides.col1_heading = results[0].col1_heading;
        $scope.slides.col1_value = results[0].col1_value;
        $scope.slides.col2_heading = results[0].col2_heading;
        $scope.slides.col2_value = results[0].col2_value;
        $scope.slides.col3_heading = results[0].col3_heading;
        $scope.slides.col3_value = results[0].col3_value;
        $scope.slides.col4_heading = results[0].col4_heading;
        $scope.slides.col4_value = results[0].col4_value;
        $scope.slides.col5_heading = results[0].col5_heading;
        $scope.slides.col5_value = results[0].col5_value;

        console.log("area"+results[0].area_id);
        console.log("area_name"+results[0].area_name);
        console.log("locality_id"+results[0].locality_id);
        console.log("locality"+results[0].locality);
        console.log("propsubtype"+results[0].propsubtype);
        console.log("suitable_for"+results[0].suitable_for);
        var address="Andheri West, Mumbai";
        $scope.area_name = results[0].area_name;
        //address = results[0].locality+ ","+results[0].area_name;
        //address = results[0].location;
        address = results[0].map_address;
        $suitable_for = results[0].propsubtype;
        /*if (results[0].suitable_for!==" ")
        {
            $suitable_for = results[0].suitable_for;
        }*/
        

        

        
        console.log('address:'+address);
        
        $timeout(function () {
        if (address != "None") 
        {
            geocoder.geocode({
                'address': address
            }, function(map_results, status) 
            {
                if (status === 'OK') 
                {
                    //$scope.currentaddress=results[0].formatted_address;
                    document.getElementById('address').innerHTML = map_results[0].formatted_address;
                    console.log(map_results[0].formatted_address);
                    var myLatLng = {lat: map_results[0].geometry.location.lat(), lng: map_results[0].geometry.location.lng()};
                    
                    //map.setCenter(results[0].geometry.location);
                    map.setZoom(12);
                    /*var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map,
                        draggable: true,
                        title: address
                    });*/
                    marker.setPosition(map_results[0].geometry.location);
                    updateMarkerAddress(map_results[0].formatted_address);                       
                    

                    if (map_results[0].geometry.viewport) 
                    {
                        map.fitBounds(map_results[0].geometry.viewport);
                    } else 
                    {
                        map.fitBounds(map_results[0].geometry.bounds);
                    }
                    //map.setCenter(results[0].geometry.location);
                    
                
                    
                        /*var request = {
                        location: results[0].geometry.location,
                        radius: '1500',
                        //type: ['restaurant']
                        type: suitable_for
                        };
                    
                        service = new google.maps.places.PlacesService(map);
                    service.nearbySearch(request, $scope.callback);*/
                    
                    console.log($suitable_for);
                    var request = {
                        location: map_results[0].geometry.location,
                        radius: '1500',
                        //type: ['restaurant']
                        type: $suitable_for
                    };
                
                    service = new google.maps.places.PlacesService(map);
                    service.nearbySearch(request, $scope.callback);
                } else 
                {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        } 
        else 
        { // set back to initial zoom and center
            console.log("else");
            map.setOptions({
                center: new google.maps.LatLng(40.685646, -76.195499),
                zoom: 40
            });
        }
        },10000);
    });

    $scope.callback = function (results, status) {
        if (status !== google.maps.places.PlacesServiceStatus.OK) {
          console.error(status);
          return;
        }
        for (var i = 0, result; result = results[i]; i++) {
          $scope.addMarker(result);
          //$scope.currentaddress=results[i].formatted_address;
        }
        
      }

    

      
    $scope.addMarker = function (place) {
        var marker = new google.maps.Marker({
          map: map,
          position: place.geometry.location,
          icon: {
            //url: 'https://developers.google.com/maps/documentation/javascript/images/circle.png',
            //url: 'dist/img/green-locator.png',
            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
            anchor: new google.maps.Point(10, 10),
            scaledSize: new google.maps.Size(30,30)
          }
        });
    };    

    $scope.show_slide_view = function(slide_name,id)
    {
        console.log(slide_name);
        console.log(id);
        $(".all").css("display","none");
        $("#new_slide").css("display","none");
        $("#"+slide_name+id).css("display","block");
    }
    $scope.slides.image_1_id = 0;
    $scope.slides.image_2_id = 0;
    $scope.slides.image_3_id = 0;
    $scope.slides.image_4_id = 0;
    $scope.slides.image_1_name = 0;
    $scope.slides.image_2_name = 0;
    $scope.slides.image_3_name = 0;
    $scope.slides.image_4_name = 0;
    $scope.new_slide = function()
    {
        $(".all").css("display","none");
        $("#new_slide").css("display","none");
        $("#new_slide").css("display","block");
        $scope.slides.image_1_id = 0;
        $scope.slides.image_2_id = 0;
        $scope.slides.image_3_id = 0;
        $scope.slides.image_4_id = 0;
        $scope.slides.image_1_name = 0;
        $scope.slides.image_2_name = 0;
        $scope.slides.image_3_name = 0;
        $scope.slides.image_4_name = 0;
    }
    $scope.addimage = function(attachment_id,image_name,description)
    {
        console.log($scope.slides.image_1_id);
        console.log($scope.slides.image_2_id);
        console.log($scope.slides.image_3_id);
        console.log($scope.slides.image_4_id);
        if ($scope.slides.image_1_id==0){
            $("#image_1").html('<img class="page_links_img" src="api/v1/uploads/project/'+image_name+'" style="padding:10px;"/>');
            $scope.slides.image_1_id=attachment_id;
            $scope.slides.image_1_name=image_name;
            $scope.slides.description_1 = description;
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            return;
        }
        else{
            if ($scope.slides.image_2_id==0)
            {
                $("#image_2").html('<img class="page_links_img" src="api/v1/uploads/project/'+image_name+'" style="padding:10px;"/>');
                $scope.slides.image_2_id=attachment_id;
                $scope.slides.image_2_name=image_name;
                $scope.slides.description_2 = description;
                $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;

                return;
            }
            else{
                if ($scope.slides.image_3_id==0)
                {
                    $("#image_3").html('<img class="page_links_img" src="api/v1/uploads/project/'+image_name+'" style="padding:10px;"/>');
                    $scope.slides.image_3_id=attachment_id;
                    $scope.slides.image_3_name=image_name;
                    $scope.slides.description_3 = description;
                    $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;

                    return;
                }
                else
                {
                    if ($scope.slides.image_4_id==0)
                    {
                        $("#image_4").html('<img class="page_links_img" src="api/v1/uploads/project/'+image_name+'" style="padding:10px;"/>');
                        $scope.slides.image_4_id=attachment_id;
                        $scope.slides.image_4_name=image_name;
                        $scope.slides.description_4 = description;
                        $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;

                        return;
                    }
                    else{
                        alert("Maximum 4 Images Allowed..!!!");
                        return;
                    }
                }
            }
        }
    }

    $scope.delete_image = function(id)
    {
        if (id==1)
        {
            $("#image_1").html('');
            $scope.slides.image_1_id=0;
            $scope.slides.image_1_name="";
            $scope.slides.description_1 = "";
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            $("#slide_view_1").css("display","block");
            return;
        }
        if (id==2)
        {
            $("#image_2").html('');
            $scope.slides.image_2_id=0;
            $scope.slides.image_2_name="";
            $scope.slides.description_2 = "";
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            $("#slide_view_1").css("display","block");
            return;
        }
        if (id==3)
        {
            $("#image_3").html('');
            $scope.slides.image_3_id=0;
            $scope.slides.image_3_name="";
            $scope.slides.description_3 = "";
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            $("#slide_view_1").css("display","block");
            return;
        }
        if (id==4)
        {
            $("#image_4").html('');
            $scope.slides.image_4_id=0;
            $scope.slides.image_4_name="";
            $scope.slides.description_4 = "";
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            $("#slide_view_1").css("display","block");
            return;
        } 
    }

    $scope.delete_image_record = function(slide_no,id)
    {
        Data.get('delete_project_image_record/'+slide_no+'/'+id).then(function (results) {
            $scope.slides={};
            Data.get('getprojectslidedata').then(function (results) {
                $scope.slide_data = results;
                $("#slide_view_1").css("display","block");
            });
        });
    }

    $scope.save_ppt_description = function(slide_no,description)
    {
        Data.get('save_project_ppt_description/'+slide_no+'/'+description).then(function (results) {
            
        });
    }
    
    $scope.removeslide = function(slide_no)
    {
        var deleteslide = confirm('Are you absolutely sure you want to delete?');
        if (deleteslide) {
            Data.get('removeprojectslide/'+slide_no).then(function (results) {
                $scope.slides={};
                Data.get('getprojectslidedata').then(function (results) {
                    $scope.slide_data = results;
                    $("#slide_view_1").css("display","block");
                });
            });
        };
    }

    $scope.saveslide = function(slides)
    {
        Data.post('saveprojectslide', {
            slides: slides
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $scope.slides={};
                Data.get('getprojectslidedata').then(function (results) {
                    $scope.slide_data = results;
                    $(".all").css("display","none");
                    $("#new_slide").css("display","none");
                    $("#slide_view_1").css("display","block");
                    $scope.slides.category = results[0].category;
                    $scope.slides.category_id = results[0].category_id;
                });
            }
        });
    }
    
    $scope.saveslidedata = function(slides)
    {
        Data.post('saveprojectslidedata', {
            slides: slides
        }).then(function (results) {
            Data.toast(results);
        });
    }
    $scope.close_image = function()
    {
        $("#mainimage").css("display","block");
        $("#editimage").css("display","none");
    }
    $scope.edit_image = function(image_name,slide_no,image_number)
    {

        if (slide_no==98)
        {
            image_name = $scope.map_image_name;
        }
        /*var canvas =  document.getElementById("canvas");
        var ctx = canvas.getContext('2d');
        ctx.drawImage(document.getElementById("image_1"), 0, 0);*/
        /*var img = new Image();
        img.src = document.getElementById("image_1");

        img.onload = function() {
          ctx.drawImage(img, 0, 0, img.width, img.height,
                             0, 0, canvas.width, canvas.height);
        }; */ 
        $("#mainimage").css("display","none");
        $("#editimage").css("display","block");
        var myEditor = new tui.ImageEditor(document.querySelector('#tui-image-editor'), {
            includeUI: {
              loadImage: {
                path: 'api/v1/uploads/project/'+image_name,
                 name: image_name
              },
              theme: blackTheme, // or whiteTheme
              //menu: ['shape'],
              //initMenu: 'shape',
              menuBarPosition: 'right'
            },
            cssMaxWidth: 1200,
            cssMaxHeight: 700,
            selectionStyle: {
              cornerSize: 20,
              rotatingPointOffset: 70
            }
        });
        $scope.edited_image = image_name;
        $scope.edited_image_slide_no = slide_no;
        $scope.edited_image_image_number = image_number;
        $scope.image_data={
            file_name :image_name,
            slide_no : slide_no,
            image_number:image_number,
            file_data:''
        };
        //loadImage("https://crm.rdbrothers.com/api/v1/uploads/project/{{edited_image}}");
        //loadImage("http://placehold.it/100x80");
        //ctx.drawImage(, 0, 0);
        
        $scope.save_image = function()
        {
            console.log(myEditor.toDataURL());
            $scope.image_data.file_data = myEditor.toDataURL();
            console.log($scope.image_data);
            Data.post('save_project_image', {
                image_data: $scope.image_data
            }).then(function (results) {
                Data.toast(results);
                $("#mainimage").css("display","block");
                $("#editimage").css("display","none");
                $("#image_"+slide_no+"_"+image_number).attr("src","api/v1/uploads/project/"+results.image_name)
            });


        }

    }
    
    

    
    $scope.map_image_name="";
    $scope.capture_image = function()
    {   
        //
        $(".gmnoprint").css("display","none");
        $(".gm-style-mtc").css("display","none");
        $(".gm-fullscreen-control").css("display","none");
        $(".gm-svpc").css("display","none");
        $("#capture_image").css("display","none");
        $scope.image_data={
            slide_no : 98,
            image_number:1,
            category : $scope.slides.category,
            category_id : $scope.slides.category_id,
            file_data:''
        };
        html2canvas(document.getElementById("map"),
        //html2canvas(document.getElementsByClassName('gm-style'),
        {
            /*width: 1280,
            height: 933,*/ 
            useCORS: true,
            optimized: false,
            allowTaint: true
        }).then(function(canvas) {
            img_data = canvas.toDataURL();
            console.log(canvas.toDataURL());
            $("#capture_image").css("display","none");
            $("#map").css("display","none");
            $("canvas").css("display","none");
            $("#image_98_1").attr("src",img_data);
            $("#map_image").css("display","block");
            
            console.log("map_img"+$("#image_98_1").prop("src"));
           
            $scope.image_captured = "Yes";
            /*var link = document.createElement("a");
            document.body.appendChild(link);
            link.download = "map.jpg";
            link.href = canvas.toDataURL();
            link.target = '_blank';
            link.click();*/

            $scope.image_data.file_data = img_data;
            
            console.log($scope.image_data);
            Data.post('save_project_map_image', {
                image_data: $scope.image_data
            }).then(function (results) {
                Data.toast(results);
                $("#image_98_1").attr("src","api/v1/uploads/project/"+results.image_name);
                $scope.map_image_name = results.image_name;
                //$("#image_"+slide_no+"_"+image_number).attr("src","api/v1/uploads/project/"+results.image_name)
            });

        });

        /*html2canvas(document.getElementById('map'), {
            useCORS: true,
            optimized: false,
            allowTaint: false,
            onrendered: function (canvas) {
                url = canvas.toDataURL("image/png");
                /*$('#map_image').attr('src',url).show();
                var a = document.createElement('a');
                a.href = url;
                a.download = 'myfile.png';
                a.click();
                console.log("url"+url);
                return url;
            }
        });*/
    }

    $scope.export_report = function(report_type)
    {
        if ($scope.image_captured=="No")
        {
            alert("Please capture map first ..");
            return;
        }
        //makePDF();
        Data.get('getprojectpdf').then(function (results) {
            console.log(results);
        });
    }


    $scope.export_report1 = function(report_type)
    {
        if ($scope.image_captured=="No")
        {
            alert("Please capture map first ..");
            return;
        }
        $scope.slide_data = {};
        /*$(".gmnoprint").css("display","none");
        $(".gm-style-mtc").css("display","none");
        $(".gm-fullscreen-control").css("display","none");
        $(".gm-svpc").css("display","none");
        //html2canvas(document.getElementById("map"),{
           
        html2canvas(document.getElementsByClassName('gm-style'),
        {
            /*width: 1280,
            height: 933,*/ 
            /*useCORS: true,
            optimized: false,
            allowTaint: false
        }).then(function(canvas) {
            var url = canvas.toDataURL();
            console.log(url);
            /*if (url='data:,')
            {
                url = 'dist/img/location.jpg';
            }
            //$("#map_image").attr("src",canvas.toDataURL());
            //console.log("map_img"+$("#map_image").prop("src"));
        
            //    var url = $("#map_image").prop('src');
            //    console.log("url"+url);*/

            var pptx = new PptxGenJS();

            pptx.addSection({ title: 'Masters' });
            var slide1 = pptx.addSlide({masterName:'TITLE_SLIDE', sectionTitle:'Masters'});
            slide1.addImage({ path: "dist/img/ppt_main.jpg", x:0.0, y:0.0, w:'100%', h:'100%' });
            slide1.addNotes('Welcome Page');
            Data.get('getslidedata').then(function (results) {
                $scope.slide_data = results;
                $scope.slides.proptype = results[0].proptype;
                $scope.slides.category = results[0].category;
                $scope.slides.category_id = results[0].category_id;
                $scope.slides.carp_area = results[0].carp_area;
                $scope.slides.sale_area = results[0].sale_area;
                $scope.slides.floor = results[0].floor;
                $scope.slides.car_park = results[0].car_park;
                $scope.slides.price_unit = results[0].price_unit;
                $scope.slides.security_depo = results[0].security_depo;
                $scope.slides.lock_per = results[0].lock_per;
                $scope.slides.lease_end = results[0].lease_end;
                $scope.slides.escalation_lease = results[0].escalation_lease;
                $scope.slides.location = results[0].location;
                $scope.slides.furnishing = results[0].furnishing;
                $scope.slides.owner_name = results[0].owner_name;
                $scope.slides.mob_no = results[0].mob_no;
                $scope.slides.email = results[0].email;
                $scope.slides.project_contact = (results[0].project_contact).toUpperCase() ;
                $scope.slides.usermobileno = results[0].usermobileno;
                $scope.slides.useremail	 = results[0].useremail;
                $scope.slides.col1_heading = results[0].col1_heading;
                $scope.slides.col1_value = results[0].col1_value;
                $scope.slides.col2_heading = results[0].col2_heading;
                $scope.slides.col2_value = results[0].col2_value;
                $scope.slides.col3_heading = results[0].col3_heading;
                $scope.slides.col3_value = results[0].col3_value;
                $scope.slides.col4_heading = results[0].col4_heading;
                $scope.slides.col4_value = results[0].col4_value;
                $scope.slides.col5_heading = results[0].col5_heading;
                $scope.slides.col5_value = results[0].col5_value;

                $scope.slides.internal_comment = results[0].internal_comment; 
                $scope.slides.external_comment = results[0].external_comment;
                $scope.slides.prop_tax = results[0].prop_tax;
                $scope.slides.cam_charges = results[0].cam_charges;
                $scope.slides.monthle_rent = results[0].monthle_rent;
                $scope.slides.ag_tenure = results[0].ag_tenure;
                $scope.slides.rent_esc = results[0].rent_esc;
                $scope.slides.lease_start = results[0].lease_start;

                angular.forEach(results,function(value,key){
                    console.log(value);
                    if (value.slide_no==2)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Main Image');
                        slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addImage({ path: "api/v1/uploads/project/"+value.image_1, x:0.25, y:0.5, w:'96%', h:'83%' });
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                        console.log(value.slide_no);
                    }
                    if (value.slide_no==3)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Commericial Term');
                        slide.addText('Commercial Terms', { placeholder:'title',x:0.3,fontSize:20 });

                        var arrTabRows = [];


                        if ($scope.slides.proptype=="pre-leased")
                        {
                            if ($scope.slides.carp_area)
                            {
                                arrTabRows.push([{ text: 'Carpet Area (sqft)', options: { valign:'top', align:'left'  } },{ text: $scope.slides.carp_area, options: { valign:'top', align:'left' } }]);
                            }

                            if ($scope.slides.sale_area)
                            {
                                arrTabRows.push([{ text: 'Built Up Area (sqft)', options: { valign:'top', align:'left'  } },{ text: $scope.slides.sale_area, options: { valign:'top', align:'left' } }]);
                            }
                        }
                        else
                        {
                            if ($scope.slides.carp_area)
                            {
                                arrTabRows.push([{ text: 'Carpet Area (sqft)', options: { valign:'top', align:'left'  } },{ text: $scope.slides.carp_area, options: { valign:'top', align:'left' } }]);
                            }

                            if ($scope.slides.sale_area)
                            {
                                arrTabRows.push([{ text: 'Built Up Area (sqft)', options: { valign:'top', align:'left'  } },{ text: $scope.slides.sale_area, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.floor)
                            {
                                arrTabRows.push([{ text: 'Proposed  Floors', options: { valign:'top', align:'left'  } },{ text: $scope.slides.floor, options: { valign:'top', align:'left' } }]);
                                
                            }

                            if ($scope.slides.car_park)
                            {
                                arrTabRows.push([{ text: 'Car Park', options: { valign:'top', align:'left'  } },{ text: $scope.slides.car_park, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.price_unit)
                            {
                                arrTabRows.push([{ text: 'Quoted Rate', options: { valign:'top', align:'left'  } },{ text: $scope.slides.price_unit, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.security_depo)
                            {
                                arrTabRows.push([{ text: 'Security Deposit', options: { valign:'top', align:'left'  } },{ text: $scope.slides.security_depo, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.lock_per)
                            {
                                arrTabRows.push([{ text: 'Lock in period', options: { valign:'top', align:'left'  } },{ text: $scope.slides.lock_per, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.lease_end)
                            {
                                arrTabRows.push([ { text: 'Lease Tenure', options: { valign:'top', align:'left'  } },{ text: $scope.slides.lease_end, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.escalation_lease)
                            {
                                arrTabRows.push([{ text: 'Rent Escalation', options: { valign:'top', align:'left'  } },{ text: $scope.slides.escalation_lease, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.location)
                            {
                                arrTabRows.push([{ text: 'Location', options: { valign:'top', align:'left'  } },{ text: $scope.slides.location, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.furnishing)
                            {
                                arrTabRows.push([{ text: 'Furnishing Details', options: { valign:'top', align:'left'  } },{ text: $scope.slides.furnishing, options: { valign:'top', align:'left' } }]);
                            }
                        }
                        if ($scope.slides.col1_value)
                        {
                            arrTabRows.push([{ text: $scope.slides.col1_heading, options: { valign:'top', align:'left'  } },{ text: $scope.slides.col1_value, options: { valign:'top', align:'left' } }]);
                        }
                        if ($scope.slides.col2_value)
                        {
                            arrTabRows.push([{ text: $scope.slides.col2_heading, options: { valign:'top', align:'left'  } },{ text: $scope.slides.col2_value, options: { valign:'top', align:'left' } }]);
                        }
                        if ($scope.slides.col3_value)
                        {
                            arrTabRows.push([{ text: $scope.slides.col3_heading, options: { valign:'top', align:'left'  } },{ text: $scope.slides.col3_value, options: { valign:'top', align:'left' } }]);
                        }
                        if ($scope.slides.col4_value)
                        {
                            arrTabRows.push([{ text: $scope.slides.col4_heading, options: { valign:'top', align:'left'  } },{ text: $scope.slides.col4_value, options: { valign:'top', align:'left' } }]);
                        }
                        if ($scope.slides.col5_value)
                        {
                            arrTabRows.push([{ text: $scope.slides.col5_heading, options: { valign:'top', align:'left'  } },{ text: $scope.slides.col5_value, options: { valign:'top', align:'left' } }]);
                        }
                        
                        /*var arrTabRows = [
                            [
                                { text: 'Carpet Area (sqft)', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.carp_area, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Built Up Area (sqft)', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.sale_area, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Proposed  Floors', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.floor, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Car Park', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.car_park, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Quoted Rent', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.price_unit, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Security Deposit', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.security_depo, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Lock in period', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.lock_per, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Lease Tenure', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.lease_end, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Rent Escalation', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.escalation_lease, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Location', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.location, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Furnishing Details', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.furnishing, options: { valign:'top', align:'left' } }
                                
                            ]
                            
                        ];*/
                        slide.addTable(
                            arrTabRows, { x: 2.0, y: 0.8, w: 6.5, rowH: 0.25, fontSize:12, color:'363636', border:{pt:'1', color:'d2aa4b'} }
                        );
                        
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                        console.log(value.slide_no);

                    }
                    if (value.slide_no==4)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Main Image');
                        slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addImage({ path: "api/v1/uploads/project/"+value.image_1, x:0.25, y:0.5, w:'96%', h:'83%' });
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                        console.log(value.slide_no);
                    }
                    if (value.slide_no==5)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Main Image');
                        slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addText($scope.slides.external_comment, { placeholder:'title',x:0.3,y:1,fontSize:20 });
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                        console.log(value.slide_no);
                    }

                    if (value.slide_no>5 && value.slide_no<98)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Images');
                        slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        if (value.image_1)
                        {
                            slide.addImage({ path: "api/v1/uploads/project/"+value.image_1, x:0.25, y:0.5, w:'45%', h:'38%' });
                        }
                        if (value.image_2)
                        {
                            slide.addImage({ path: "api/v1/uploads/project/"+value.image_2, x:5.0, y:0.5, w:'45%', h:'38%' });
                        }
                        if (value.image_3)
                        {
                            slide.addImage({ path: "api/v1/uploads/project/"+value.image_3, x:0.25, y:3.0, w:'45%', h:'38%' });
                        }
                        if (value.image_4)
                        {
                            slide.addImage({ path: "api/v1/uploads/project/"+value.image_4, x:5.0, y:3.0, w:'45%', h:'38%' });
                        }
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                    }
                    if (value.slide_no==98)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Location ');
                        slide.addText('Location', { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});                    
                        slide.addImage({ path: "api/v1/uploads/project/"+value.image_1, x:0.25, y:0.5, w:'95%', h:'84%' });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                    }
                    if (value.slide_no==99)
                    {
                        var slide = pptx.addSlide({masterName:'THANKS_SLIDE', sectionTitle:'Masters'});
                        slide.addImage({ path: "dist/img/thanks.jpg",  x:0.0, y:0.0, w:'100%', h:'100%'  });
                        slide.addNotes('Thanks Page');
                        slide.addText($scope.slides.project_contact, { placeholder:'title',x:1.2,y:4,fontSize:24 , align:'center'});
                        slide.addText('Call:'+$scope.slides.usermobileno, { placeholder:'title',x:1.2,y:4.4,fontSize:14, align:'center' });
                        slide.addText('Email:'+$scope.slides.useremail, { placeholder:'title',x:1.2,y:4.7,fontSize:14, align:'center' });
                    }

                        /*var slide2 = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                    slide2.addNotes('Main Image');
                    slide2.addText('Main Building', { placeholder:'title',x:0.3,fontSize:20 });
                    slide2.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'96%', h:'83%' });
                    slide2.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                    slide2.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide2.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide2.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                    slide2.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});

                    var slide3 = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                    slide3.addNotes('Commericial Term');
                    slide3.addText('Commercial Terms', { placeholder:'title',x:0.3,fontSize:20 });
                    
                    var arrTabRows = [
                        [
                            { text: 'Carpet Area (sqft)', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.carp_area, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Built Up Area (sqft)', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.sale_area, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Proposed  Floors', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.floor, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Car Park', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.car_park, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Quoted Rent', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.price_unit, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Security Deposit', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.security_depo, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Lock in period', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.lock_per, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Lease Tenure', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.lease_end, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Rent Escalation', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.escalation_lease, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Location', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.location, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Furnishing Details', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.furnishing, options: { valign:'top', align:'left' } }
                            
                        ]
                        
                    ];
                    slide3.addTable(
                        arrTabRows, { x: 2.0, y: 0.8, w: 6.5, rowH: 0.25, fontSize:12, color:'363636', border:{pt:'1', color:'d2aa4b'} }
                    );
                    
                    slide3.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                    slide3.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide3.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide3.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                    slide3.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});

                    var slide4 = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                    slide4.addNotes('Images');
                    slide4.addText('Image Page', { placeholder:'title',x:0.3,fontSize:20 });
                    slide4.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                    slide4.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});

                    slide4.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'45%', h:'38%' });
                    slide4.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:5.0, y:0.5, w:'45%', h:'38%' });
                    slide4.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:0.25, y:3.0, w:'45%', h:'38%' });
                    slide4.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:5.0, y:3.0, w:'45%', h:'38%' });


                    slide4.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide4.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                    slide4.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});

                    var slide5 = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                    slide5.addNotes('Image Page ');
                    slide5.addText('Image Page', { placeholder:'title',x:0.3,fontSize:20 });
                    slide5.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                    slide5.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    
                    slide5.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'45%', h:'38%' });
                    slide5.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:5.0, y:0.5, w:'45%', h:'38%' });
                    slide5.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:0.25, y:3.0, w:'45%', h:'38%' });
                    slide5.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:5.0, y:3.0, w:'45%', h:'38%' });

                    slide5.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide5.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                    slide5.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});

                    var slide6 = pptx.addSlide({masterName:'THANKS_SLIDE', sectionTitle:'Masters'});
                    slide6.addImage({ path: "dist/img/thanks.jpg",  x:0.0, y:0.0, w:'100%', h:'100%'  });
                    slide6.addNotes('Thanks Page');*/
                
                });
                //pptx.writeFile('projects_report'+Date.now())
                //.then(function(fileName){ console.log('Saved! File Name: '+fileName) });
                pptx.write("base64")
                    .then((data) => {

                        console.log("write as base64: Here are 0-100 chars of `data`:\n");
                        //console.log(data.substring(0, 100));
                        //console.log(data);
                        $scope.image_data={
                            category :$scope.slides.category,
                            category_id :$scope.slides.category_id,
                            file_data:''
                        };
                        $scope.image_data.file_data = data;
                        console.log($scope.image_data.file_data);
                        Data.post('save_ppt', {
                            image_data: $scope.image_data
                        }).then(function (results) {
                            Data.toast(results);
                            //$("#image_98_1").attr("src","api/v1/uploads/project/"+results.image_name);
                            //$scope.map_image_name = results.image_name;
                            //$("#image_"+slide_no+"_"+image_number).attr("src","api/v1/uploads/project/"+results.image_name)
                        });
                    })
                    .catch((err) => {
                        console.error(err);
                    });

            });
    
        /*});
        /*
        var slide = pptx.addSlide();
        var optsTitle = { color:'9F9F9F', marginPt:3, border:[0,0,{pt:'1',color:'CFCFCF'},0] };
        pptx.layout = 'LAYOUT_WIDE';
        //pptx.layout({ name:'A3', width:16.5, height:11.7 });
        //slide.slideNumber({ x:0.5, y:'90%' });
        slide.addTable( [ [{ text:'Simple Example', options:optsTitle }] ], { x:0.5, y:0.13, w:12.5 } );

        //slide.addText('Hello World!', { x:0.5, y:0.7, w:6, h:1, color:'0000FF' });
        slide.addText('Hello 45! ', { x:0.5, y:0.5, w:6, h:1, fontSize:36, color:'0000FF', shadow:{type:'outer', color:'00AAFF', blur:2, offset:10, angle: 45, opacity:0.25} });
        slide.addText('Hello 180!', { x:0.5, y:1.0, w:6, h:1, fontSize:36, color:'0000FF', shadow:{type:'outer', color:'ceAA00', blur:2, offset:10, angle:180, opacity:0.5} });
        slide.addText('Hello 355!', { x:0.5, y:1.5, w:6, h:1, fontSize:36, color:'0000FF', shadow:{type:'outer', color:'aaAA33', blur:2, offset:10, angle:355, opacity:0.75} });

        // Bullet Test: Number
        slide.addText(999, { x:0.5, y:2.0, w:'50%', h:1, color:'0000DE', bullet:true });
        // Bullet Test: Text test
        slide.addText('Bullet text', { x:0.5, y:2.5, w:'50%', h:1, color:'00AA00', bullet:true });
        // Bullet Test: Multi-line text test
        slide.addText('Line 1\nLine 2\nLine 3', { x:0.5, y:3.5, w:'50%', h:1, color:'AACD00', bullet:true });

        // Table cell margin:0
        slide.addTable([['margin:0']], { x: 0.5, y: 1.1, margin: 0, w: 0.75, fill: { color: 'FFFCCC' } });

        // Fine-grained Formatting/word-level/line-level Formatting
        slide.addText(
            [
                { text:'right line', options:{ fontSize:24, fontFace:'Courier New', color:'99ABCC', align:'right', breakLine:true } },
                { text:'ctr line',   options:{ fontSize:36, fontFace:'Arial',       color:'FFFF00', align:'center', breakLine:true } },
                { text:'left line',  options:{ fontSize:48, fontFace:'Verdana',     color:'0088CC', align:'left' } }
            ],
            { x: 0.5, y: 3.0, w: 8.5, h: 4, margin: 0.1, fill: { color: '232323' } }
        );


        /*slide.addText(
        [
            { text:'Did You Know?', options:{ fontSize:48, color:pptx.SchemeColor.accent1, breakLine:true } },
            { text:'writeFile() returns a Promise', options:{ fontSize:24, color:pptx.SchemeColor.accent6, breakLine:true } },
            { text:'!', options:{ fontSize:24, color:pptx.SchemeColor.accent6, breakLine:true } },
            { text:'(pretty cool huh?)', options:{ fontSize:24, color:pptx.SchemeColor.accent3 } }
        ],
        { x:1, y:1, w:'80%', h:3, align:'center', fill:{ color:pptx.SchemeColor.background2, transparency:50 } }
        );

        //+getTimestamp())
        pptx.writeFile('projects_report')
        .then(function(fileName){ console.log('Saved! File Name: '+fileName) });

        /*Data.get('export_report/'+module_name+'/'+id+'/'+data+'/'+report_type).then(function (results) {
            //window.open("api//v1//uploads//reports//"+module_name+"//_list."+report_type,"_blank");
            window.location.href = "api//v1//uploads//reports//"+module_name+"_list."+report_type;
            //window.open("api//v1//uploads//reports//"+module_name+"_list_"+id+"."+report_type);

        });*/
    };

});



function makePDF() {

    var quotes = document.getElementById('quotes');
    //var quotes = '';
    console.log(quotes);
    alert("a");

    html2canvas(quotes, {
        onrendered: function(canvas) {

        //! MAKE YOUR PDF
        var pdf = new jsPDF('p', 'pt', 'letter');

        for (var i = 0; i <= quotes.clientHeight/980; i++) {
            //! This is all just html2canvas stuff
            var srcImg  = canvas;
            var sX      = 0;
            var sY      = 980*i; // start 980 pixels down for every new page
            var sWidth  = 900;
            var sHeight = 980;
            var dX      = 0;
            var dY      = 0;
            var dWidth  = 900;
            var dHeight = 980;

            window.onePageCanvas = document.createElement("canvas");
            onePageCanvas.setAttribute('width', 900);
            onePageCanvas.setAttribute('height', 980);
            var ctx = onePageCanvas.getContext('2d');
            // details on this usage of this function: 
            // https://developer.mozilla.org/en-US/docs/Web/API/Canvas_API/Tutorial/Using_images#Slicing
            ctx.drawImage(srcImg,sX,sY,sWidth,sHeight,dX,dY,dWidth,dHeight);

            // document.body.appendChild(canvas);
            var canvasDataURL = onePageCanvas.toDataURL("image/png", 1.0);

            var width         = onePageCanvas.width;
            var height        = onePageCanvas.clientHeight;

            //! If we're on anything other than the first page,
            // add another page
            if (i > 0) {
                pdf.addPage(612, 791); //8.5" x 11" in pts (in*72)
            }
            //! now we declare that we're working on that page
            pdf.setPage(i+1);
            //! now we add content to that page!
            pdf.addImage(canvasDataURL, 'PNG', 20, 40, (width*.62), (height*.62));

        }
        alert("i m here ..!!!");
        //! after the for loop is finished running, we save the pdf.
        pdf.save('test.pdf');
    }
  });
}

app.controller('Project_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, Data,$sce) {
    var project_id = $routeParams.project_id;
    $scope.activePath = null;
    
    $scope.project = {};
    $scope.contact = {};
    /* project.internal_comment = $("#internal_comment").val(); 
        project.external_comment = $("#external_comment").val();*/
    $scope.isNavCollapsed = true;
    $scope.isCollapsed = false;
    $scope.isCollapsedHorizontal = false;
    $scope.enable_disable = true;

    $scope.modify = function()
    {
        $scope.enable_disable = false;
        $scope.disableAll = false;
    }

    
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;       
    $str = '';
    // $str = ($("#permission_string").val());
    console.log("pks-----------13112023----------------------");
    // $timeout(function () { 
async function as(){
        // Data.get('session').then(function (results) {
            const results = await Data.get('session');
            if (results.user_id) 
            {
                $str = results.permissions;
                console.log("pks");
            }
        // });
    // }, 1000);
    console.log($str);
    console.log("pks-------------------------------------------");
    if ((($str).indexOf("project_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("project_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("project_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("project_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.update_rights)
    {
        
        alert("You don't have rights to use this option..");
        return;
    }
}
as();
    $timeout(function () { 
        Data.get('selecttask').then(function (results) {
            $scope.task_list = results;
        });
    }, 100);

    var cat = "";
    $timeout(function () { 
        Data.get('selectdropdowns/PROJ_STATUS').then(function (results) {
            $rootScope.proj_status = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/PROJ_TYPE').then(function (results) {
            $rootScope.proj_type = results;
        });
    }, 100);

    

    $timeout(function () { 
        Data.get('selectdropdowns/AMENITIES').then(function (results) {
            $rootScope.amenities = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectcontact/Developer').then(function (results) {
            $rootScope.contacts = results;
        });
    }, 100);
    
    $timeout(function () { 
        Data.get('selectdropdowns/BANK_LOANS').then(function (results) {
            $rootScope.bank_loans = results;
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
        Data.get('selectdropdowns/CLIENT_SOURCE').then(function (results) {
            $rootScope.client_sources = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/SUB_SOURCE').then(function (results) {
            $rootScope.sub_sources = results;
        });
    }, 100);

    $scope.change_sub_source = function (source_channel) 
    { 
        Data.get('change_sub_source/'+source_channel).then(function (results) { 
            $rootScope.sub_sources = results; 
        });
    }


    $timeout(function () { 
        Data.get('selectdropdowns/PROP_STATUS').then(function (results) {
            $rootScope.prop_statuss = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/PROP_SUB_TYPE').then(function (results) {
            $rootScope.propsubtypes = results;
        });
    }, 100);

    $scope.change_propsubtype = function (proptype) 
    { 
        $timeout(function () { 
            Data.get('selectdropdownsNew/PROP_SUB_TYPE/'+proptype).then(function (results) { 
                $rootScope.propsubtypes = results; 
            }); 
        }, 100);
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
    
    $scope.select_assign_to = function(teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/0').then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }


    
    $scope.multiproperty={};
    Data.get('project_edit_ctrl/'+project_id).then(function (results) {
        $scope.arr = ((results[0].app_bankloan).split(','));
        results[0].app_bankloan = $scope.arr;
        $scope.arr = ((results[0].amenities_avl).split(','));
        results[0].amenities_avl = $scope.arr;
        $scope.arr = ((results[0].pro_specification).split(','));
        results[0].pro_specification = $scope.arr;
        $scope.arr = ((results[0].parking).split(','));
        results[0].parking = $scope.arr;
        $scope.arr = ((results[0].teams).split(','));
        results[0].teams = $scope.arr;
        $scope.arr = ((results[0].sub_teams).split(','));
        results[0].sub_teams = $scope.arr;
        $scope.arr = ((results[0].assign_to).split(','));
        results[0].assign_to = $scope.arr;
        $scope.arr = ((results[0].source_channel).split(','));
        results[0].source_channel = $scope.arr;
        $scope.arr = ((results[0].subsource_channel).split(','));
        results[0].subsource_channel = $scope.arr;
        $scope.arr = ((results[0].groups).split(','));
        results[0].groups = $scope.arr;
        //$rootScope.dataprojects = results;
        
        $scope.$watch($scope.project, function() {
            $scope.project={};
            $scope.project = {
                project_name:results[0].project_name,
                developer_id:results[0].developer_id,
                project_for:results[0].project_for,
                con_status:results[0].con_status,
                possession_date:results[0].possession_date,
                completion_date:results[0].completion_date,
                rera_num:results[0].rera_num,
                add1:results[0].add1,
                add2:results[0].add2,
                exlocation:results[0].exlocation,
                locality_id:results[0].locality_id,
                area_id:results[0].area_id,
                road_no:results[0].road_no,
                landmark:results[0].landmark,
                zip:results[0].zip,
                lattitude:results[0].lattitude,
                longitude:results[0].longitude,
                salu:results[0].salu,
                fname:results[0].fname,
                lname:results[0].lname,
                mob1:results[0].mob1,
                mob2:results[0].mob2,
                email:results[0].email,
                designation:results[0].designation,
                salu_2:results[0].salu_2,
                fname_2:results[0].fname_2,
                lname_2:results[0].lname_2,
                mob1_2:results[0].mob1_2,
                mob2_2:results[0].mob2_2,
                email_2:results[0].email_2,
                designation_2:results[0].designation_2,
                tot_area:results[0].tot_area,
                tot_unit:results[0].tot_unit,
                rent_unit_carpet:results[0].rent_unit_carpet,
                pack_price:results[0].pack_price,
                pack_price_comments:results[0].pack_price_comments,
                app_bankloan:results[0].app_bankloan,
                amenities_avl:results[0].amenities_avl,
                pro_specification:results[0].pro_specification,
                parking:results[0].parking,
                car_park:results[0].car_park,
                /*park_charge:results[0].park_charge,
                maintenance_charges:results[0].maintenance_charges,
                prop_tax:results[0].prop_tax,
                transfer_charge:results[0].transfer_charge,*/
                park_charge:(results[0].park_charge),
                park_charge_para:results[0].park_charge_para,
                maintenance_charges:(results[0].maintenance_charges),
                main_charge_para:results[0].main_charge_para,
                prop_tax:(results[0].prop_tax),
                prop_tax_para:results[0].prop_tax_para,
                transfer_charge:(results[0].transfer_charge),
                transfer_charge_para:results[0].transfer_charge_para,
                teams:results[0].teams,
                sub_teams:results[0].sub_teams,
                assign_to:results[0].assign_to,
                sms_saletrainee:results[0].sms_saletrainee,
                email_saletrainee:results[0].email_saletrainee,
                source_channel:results[0].source_channel,
                subsource_channel:results[0].subsource_channel,
                groups:results[0].groups,
                file_name:results[0].file_name,
                internal_comment:results[0].internal_comment,
                external_comment:results[0].external_comment,
                numof_building:results[0].numof_building,
                numof_floor:results[0].numof_floor,
                lifts:results[0].lifts,
                floor_rise:results[0].floor_rise,
                distfrm_station:results[0].distfrm_station,
                distfrm_dairport:results[0].distfrm_dairport,
                distfrm_highway:results[0].distfrm_highway,
                distfrm_school:results[0].distfrm_school,
                distfrm_market:results[0].distfrm_market,
                com_certification:results[0].com_certification,
                youtube_link:results[0].youtube_link,
                rating:results[0].rating,
                review:results[0].review,
                proj_status:results[0].proj_status,
                pro_inspect:results[0].pro_inspect,
                market_project:results[0].market_project,
                vastu_comp:results[0].vastu_comp,
                occu_certi:results[0].occu_certi,
                soc_reg:results[0].soc_reg,
                cc:results[0].cc,
                mainroad:results[0].mainroad,
                internalroad:results[0].internalroad,
                area_parameter:results[0].area_parameter,
                project_type:results[0].project_type,
                share_on_website:results[0].share_on_website,
                share_on_99:results[0].share_on_99,
                acers_99_projid :results[0].acers_99_projid,
                task_id:results[0].task_id,
                project_id:results[0].project_id
            }
        },true);
        
        $timeout(function () {
            $('.select2').select2();

		    $('.textarea').wysihtml5();
             if (results[0].market_project=="1")
            {
                $('#market_project').prop('checked', true);
            }

            if (results[0].exlocation=="1")
            {
                $('#exlocation').prop('checked', true);
            }

            if (results[0].vastu_comp=="1")
            {
                $('#vastu_comp').prop('checked', true);
            }

            $timeout(function () { 
                Data.get('getfromarea/'+results[0].area_id).then(function (results1) {
                    $scope.project.city = results1[0].city;
                    $scope.project.state = results1[0].state;
                    $scope.project.country = results1[0].country;
                });
            }, 100);

            address = "";
            if (results[0].area_id)
            {
                address = results[0].area_name;
            }
            else{
                address = results[0].locality;
            }
            if (address != "None") 
            {
                geocoder.geocode({
                    'address': address
                }, function(results, status) 
                {
                    if (status === 'OK') 
                    {
                        //$scope.currentaddress=results[0].formatted_address;
                        document.getElementById('address').innerHTML = results[0].formatted_address;
                        var myLatLng = {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()};
                        
                        /*var marker = new google.maps.Marker({
                            position: myLatLng,
                            map: map,
                            draggable: true,
                            title: address
                        });*/
                        marker.setPosition(results[0].geometry.location);
                        updateMarkerAddress(results[0].formatted_address);
                        if (results[0].geometry.viewport) 
                        {
                            map.fitBounds(results[0].geometry.viewport);
                        } else 
                        {
                            map.fitBounds(results[0].geometry.bounds);
                        }
                        
                    } else 
                    {
                        alert('Geocode was not successful for the following reason: ' + status);
                    }
                });
            } 
            else 
            { // set back to initial zoom and center
                map.setOptions({
                    center: new google.maps.LatLng(40.685646, -76.195499),
                    zoom: 40
                });
            }
        },10000);
        
    });
    Data.get('project_images/'+project_id).then(function (results) {
        $scope.project_images = results;
    });

    Data.get('project_videos/'+project_id).then(function (results) {
        $scope.project_videos = results;
    });

    Data.get('project_occu_cert/'+project_id).then(function (results) {
        $rootScope.project_occu_certs = results;
    });

    
    Data.get('getproperties/'+project_id).then(function (results) {
        $rootScope.properties = results;
    });
    
    $scope.addproperty = function(project_id)
    {
        
        //$("#multiproperty").css("display","block");
        $scope.multiproperty = {};
        $("#multiproperty").show();
        //$("#addbutton").css("display","none");
    }

    $scope.changeproperty = function(property_id)
    {
        $scope.multiproperty = {};
        $timeout(function () {
            Data.get('properties_edit_ctrl/'+property_id).then(function (results) {
                /* $scope.cat=results[0].proptype;*/
                $rootScope.multiproperties = results;
            });
        }, 100);
    }

    $scope.calculate_loading = function(field_name)
    {
        if (field_name == "sale_area")
        {
            if ($scope.multiproperty.carp_area>0)
            {
                diff = $scope.multiproperty.sale_area - $scope.multiproperty.carp_area;
                $scope.multiproperty.loading = (diff * (100 / $scope.multiproperty.carp_area)).toFixed(2);
            }
            field_name = "exp_price";
        }
        if (field_name == "carp_area")
        {
            if ($scope.multiproperty.sale_area>0)
            {
                diff = $scope.multiproperty.sale_area - $scope.multiproperty.carp_area;
                $scope.multiproperty.loading = (diff * (100 / $scope.multiproperty.carp_area)).toFixed(2);
            }
            field_name = "exp_price";
        }
        if (field_name == "loading")
        {
            if ($scope.multiproperty.sale_area>0)
            {
                diff = ((100+parseFloat($scope.multiproperty.loading))/100);
                $scope.multiproperty.carp_area = ($scope.multiproperty.sale_area / diff).toFixed(2);

            }
            field_name = "exp_price";
        }

        if (field_name == "exp_price_para")
        {
            
            $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price)/$scope.multiproperty.sale_area).toFixed(2);
            $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price)/$scope.multiproperty.carp_area).toFixed(2);
            if ($scope.multiproperty.exp_price_para == "Th")
            {
               $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.sale_area).toFixed(2);
               $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.carp_area).toFixed(2); 
              
            } 
            if ($scope.multiproperty.exp_price_para == "Lac")
            {
                $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.sale_area).toFixed(2); 
                $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.carp_area).toFixed(2);
               
            } 
            if ($scope.multiproperty.exp_price_para == "Cr")
            {
               $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.sale_area).toFixed(2);
               $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.carp_area).toFixed(2); 
               
            } 

        }
        if (field_name == "price_unit_para")
        {
          
            $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price)/$scope.multiproperty.sale_area).toFixed(2);
            $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price)/$scope.multiproperty.carp_area).toFixed(2);
            if ($scope.multiproperty.price_unit_para == "Th")
            {
               $scope.multiproperty.exp_price =(($scope.multiproperty.sale_area*1000)/$scope.multiproperty.price_unit).toFixed(2);
               $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.carp_area).toFixed(2); 
               
            } 
            if ($scope.multiproperty.price_unit_para == "Lac")
            {
               $scope.multiproperty.exp_price =(($scope.multiproperty.sale_area*100000)/$scope.multiproperty.price_unit).toFixed(2);
               $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.carp_area).toFixed(2); 
               
            } 
            if ($scope.multiproperty.price_unit_para == "Cr")
            {
               $scope.multiproperty.exp_price =(($scope.multiproperty.sale_area*10000000)/$scope.multiproperty.price_unit).toFixed(2);
               $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.carp_area).toFixed(2); 
               
            } 

        }

        if (field_name == "price_unit_carpet_para")
        {
            
            $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price)/$scope.multiproperty.sale_area).toFixed(2);
            $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price)/$scope.multiproperty.carp_area).toFixed(2);
            if ($scope.multiproperty.price_unit_carpet_para == "Th")
            {
               $scope.multiproperty.exp_price =(($scope.multiproperty.carp_area*10000)/$scope.multiproperty.price_unit_carpet).toFixed(2);
               $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*10000)/$scope.multiproperty.sale_area).toFixed(2); 
               
            } 
            if ($scope.multiproperty.price_unit_carpet_para == "Lac")
            {
              $scope.multiproperty.exp_price =(($scope.multiproperty.carp_area*100000)/$scope.multiproperty.price_unit_carpet).toFixed(2);
               $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.sale_area).toFixed(2); 
               
            } 
            if ($scope.multiproperty.price_unit_carpet_para == "Cr")
            {
              $scope.multiproperty.exp_price =(($scope.multiproperty.carp_area*10000000)/$scope.multiproperty.price_unit_carpet).toFixed(2);
               $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.sale_area).toFixed(2); 
               
            } 
        }

        if (field_name == "exp_price")
        {
            if ($scope.multiproperty.exp_price>0 && $scope.multiproperty.sale_area>0)
            {
                $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price)/$scope.multiproperty.sale_area).toFixed(2);
                $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price)/$scope.multiproperty.carp_area).toFixed(2);
                if ($scope.multiproperty.exp_price_para == "Th")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.carp_area).toFixed(2); 
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Lac")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.sale_area).toFixed(2); 
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.carp_area).toFixed(2);
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Cr")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.carp_area).toFixed(2); 
                    
                } 
             } 
        
            if ($scope.multiproperty.exp_price>0 && $scope.multiproperty.carp_area>0)
            {
                $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price)/$scope.multiproperty.sale_area).toFixed(2);
                $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price)/$scope.multiproperty.carp_area).toFixed(2);
                if ($scope.multiproperty.exp_price_para == "Th")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.carp_area).toFixed(2); 
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Lac")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.sale_area).toFixed(2); 
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.carp_area).toFixed(2);
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Cr")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.carp_area).toFixed(2); 
                   
                } 
            }
            
        }

        if (field_name == "price_unit")
        {
            if ($scope.multiproperty.sale_area>0 && $scope.multiproperty.price_unit>0)
            {
                $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price)/$scope.multiproperty.sale_area).toFixed(2);
                $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price)/$scope.multiproperty.carp_area).toFixed(2);
                if ($scope.multiproperty.exp_price_para == "Th")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.carp_area).toFixed(2); 
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Lac")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.sale_area).toFixed(2); 
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.carp_area).toFixed(2);
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Cr")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.carp_area).toFixed(2); 
                    
                } 
            }
            if ($scope.multiproperty.carp_area>0 && $scope.multiproperty.price_unit>0)
            {
                $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price)/$scope.multiproperty.sale_area).toFixed(2);
                $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price)/$scope.multiproperty.carp_area).toFixed(2);
                if ($scope.multiproperty.exp_price_para == "Th")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.carp_area).toFixed(2); 
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Lac")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.sale_area).toFixed(2); 
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.carp_area).toFixed(2);
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Cr")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.carp_area).toFixed(2); 
                    
                } 

            }
            
        }

        if (field_name == "price_unit_carpet")
        {
            if ($scope.multiproperty.carp_area>0 && $scope.multiproperty.price_unit_carpet>0)
            {
                $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price)/$scope.multiproperty.sale_area).toFixed(2);
                $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price)/$scope.multiproperty.carp_area).toFixed(2);
                if ($scope.multiproperty.exp_price_para == "Th")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.carp_area).toFixed(2); 
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Lac")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.sale_area).toFixed(2); 
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.carp_area).toFixed(2);
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Cr")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.carp_area).toFixed(2); 
                    
                } 

            }
            if ($scope.multiproperty.sale_area>0 && $scope.multiproperty.price_unit_carpet>0)
            {
                $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price)/$scope.multiproperty.sale_area).toFixed(2);
                $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price)/$scope.multiproperty.carp_area).toFixed(2);
                if ($scope.multiproperty.exp_price_para == "Th")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*1000)/$scope.multiproperty.carp_area).toFixed(2); 
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Lac")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.sale_area).toFixed(2); 
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*100000)/$scope.multiproperty.carp_area).toFixed(2);
                    
                } 
                if ($scope.multiproperty.exp_price_para == "Cr")
                {
                    $scope.multiproperty.price_unit = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.sale_area).toFixed(2);
                    $scope.multiproperty.price_unit_carpet = (($scope.multiproperty.exp_price*10000000)/$scope.multiproperty.carp_area).toFixed(2); 
                    
                } 
            }
            
           
        }
    }

    $scope.multiproperty_add = {multiproperty:''};
    $scope.multiproperty_add = function (multiproperty) {
        multiproperty.project_id = project_id;
        Data.post('multiproperty_add', {
            multiproperty: multiproperty
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $timeout(function () {
                    Data.get('getproperties/'+project_id).then(function (results) {
                        $rootScope.properties = results;
                    });
                }, 100);
                $('#multiproperty').modal('hide');
            }
        });
    };
    $scope.multiproperty_update = function (multiproperty) {
        Data.post('multiproperty_update', {
            multiproperty: multiproperty
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $timeout(function () {
                    Data.get('getproperties/'+project_id).then(function (results) {
                        $rootScope.properties = results;
                    });
                }, 100);
                $('#changeproperty').modal('hide');
            }
        });
    };

    $scope.multiproperty_delete = function (multiproperty) {
        var deleteproperty = confirm('Are you absolutely sure you want to delete?');
        if (deleteproperty) 
        {
            Data.post('multiproperty_delete', {
                multiproperty: multiproperty
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $timeout(function () {
                        Data.get('getproperties/'+project_id).then(function (results) {
                            $rootScope.properties = results;
                        });
                    }, 100);
                    $('#changeproperty').modal('hide');
                }
            });
        }
    };

    $scope.copy_project_images = function(project_id)
    {
        console.log(project_id);
        var copyproject = confirm('This will copy all images of this project to related properties..!!!');
        if (copyproject) {
            Data.get('copy_project_images/'+project_id).then(function (results) {
                Data.toast(results);
            });s

        }
        
    }
    
    $scope.geocodeAddress = function (field_name,value) 
    {  

        if (field_name=='locality_id')
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+value).then(function (results) {
                    $scope.project.area_id = results[0].area_id;
                    $scope.project.city = results[0].city;
                    $scope.project.state = results[0].state;
                    $scope.project.country = results[0].country;
                });
            }, 100);
            $timeout(function () { 
                $("#area_id").select2();
            },3000);
        }

        if (field_name=='area_id')
        {
            $timeout(function () { 
                Data.get('getfromarea/'+value).then(function (results) {
                    $scope.project.city = results[0].city;
                    $scope.project.state = results[0].state;
                    $scope.project.country = results[0].country;
                });
            }, 100);
        }

        address = $('#'+field_name+' :selected').text();
        if (address != "None") 
        {
		    geocoder.geocode({
		        'address': address
            }, function(results, status) 
            {
			    if (status === 'OK') 
                {
                    //$scope.currentaddress=results[0].formatted_address;
                    document.getElementById('address').innerHTML = results[0].formatted_address;
                    var myLatLng = {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()};
                    
                    /*var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map,
                        draggable: true,
                        title: address
                    });*/
                    marker.setPosition(results[0].geometry.location);
                    if (results[0].geometry.viewport) 
                    {
                        map.fitBounds(results[0].geometry.viewport);
                    } else 
                    {
                        map.fitBounds(results[0].geometry.bounds);
                    }
                    
                } else 
                {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
		    });
        } 
        else 
        { // set back to initial zoom and center
		    map.setOptions({
		        center: new google.maps.LatLng(40.685646, -76.195499),
		        zoom: 40
		    });
        }
    };
    $scope.project_update = function (project) {
        project.internal_comment = $("#internal_comment").val(); 
        project.external_comment = $("#external_comment").val();
        project.file_name = $("#file_name").val();
        Data.post('project_update', {
            project: project
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file-1').fileinput('upload');
                $('#file_occu').fileinput('upload');
                $('#file_videos').fileinput('upload');
               $location.path('project_list/0');
            }
        });
    };
    
    $scope.project_delete = function (project) {
        //console.log(business_unit);
        var deleteproject = confirm('Are you absolutely sure you want to delete?');
        if (deleteproject) {
            Data.post('project_delete', {
                project: project
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('project_list/0');
                }
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
                        if ($scope.temptype=='PROP_STATUS')
                        {
                            controlvalue = "prop_statuss";
                        }
                        $rootScope[controlvalue] = {};
                        $scope.$watch($rootScope[controlvalue], function() {
                            $rootScope[controlvalue] = results;
                        }, true);
                        //$rootScope[controlvalue] = results;
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
        contact.contact_off = "Developer";
        contact.file_name = $("#file_name_company_logo").val();
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                developer_id = results.contact_id;
                $('#file_company_logo').fileinput('upload');
                $('#file_visiting_card').fileinput('upload');
                $('#file_contact_pic').fileinput('upload');
                $("#adddeveloper").modal("hide");
                $timeout(function () {
                    $rootScope.contacts = {};
                    Data.get('selectcontact/Developer').then(function (results) {
                        $rootScope.contacts = results;
                    });
                },3000);
                $scope.$watch($scope.project.developer_id, function() {
                    $scope.project.developer_id = developer_id;
                }, true);
                $scope.$watch($scope.contact, function() {
                    $scope.contact = {};
                }, true);

            }
        });
    };

    $scope.project_image_update = function (attachment_id,field_name,value) 
    { 
        Data.get('project_image_update/'+attachment_id+'/'+field_name+'/'+value).then(function (results) {
        });
    };
    $scope.removeimage = function (attachment_id) {
        var deleteproduct = confirm('Are you absolutely sure you want to delete?');
        if (deleteproduct) {
            Data.get('removeimage/'+attachment_id).then(function (results) {
                Data.toast(results);
                Data.get('project_images/'+project_id).then(function (results) {
                    $scope.project_images = results;
                });
            });
        }
    };

    
});

app.controller('SelectProject', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    $timeout(function () { 
        Data.get('selectproject').then(function (results) {
            $rootScope.projects = results;
        });
    }, 100);
});


app.controller('ProjectOneMailer', function ($scope, $rootScope, $routeParams, $location, $http, Data, $sce,$timeout) {
    //$("body").addClass("sidebar-collapse");
    $("#biglogo").css("height","50px");
    $("#logobig").css("height","50px");
    $scope.image_captured = "No";
    var module_name = $routeParams.module_name;
    var id = $routeParams.id;
    var data = $routeParams.data;

    $scope.module_name = module_name;
    $scope.activePath = null;
    $scope.slides = {};
    $scope.slidedata = {};
    Data.get('properties_images/'+data).then(function (results) {
        $scope.project_images_slide = results;
    });
    $scope.slides.category = "";
    $scope.slides.category_id = 0;
    $scope.arrTabRows = {};
    var url;
    Data.get('projectonemailerreports/'+module_name+'/'+id+'/'+data).then(function (results) {
        $scope.slide_data = results;
        $scope.arrTabRows  = results;
        $("#slide_view_1").css("display","block");
        $scope.slides.proptype = results[0].proptype;
        $scope.slides.category = results[0].category;
        $scope.slides.category_id = results[0].category_id;
        $scope.slides.carp_area = results[0].carp_area;
        $scope.slides.sale_area = results[0].sale_area;
        $scope.slides.floor = results[0].floor;
        $scope.slides.car_park = results[0].car_park;
        $scope.slides.price_unit = results[0].price_unit;
        $scope.slides.security_depo = results[0].security_depo;
        $scope.slides.lock_per = results[0].lock_per;
        $scope.slides.lease_end = results[0].lease_end;
        $scope.slides.escalation_lease = results[0].escalation_lease;
        $scope.slides.location = results[0].location;
        $scope.slides.furnishing = results[0].furnishing;
        $scope.slides.owner_name = results[0].owner_name;
        $scope.slides.mob_no = results[0].mob_no;
        $scope.slides.email = results[0].email;

        $scope.slides.project_contact = results[0].project_contact;
        $scope.slides.usermobileno = results[0].usermobileno;
        $scope.slides.useremail	 = results[0].useremail;

        $scope.slides.attachment_id = results[0].attachment_id;
        $scope.slides.main_image = results[0].filenames;
        $scope.slides.description = results[0].description;

        $scope.slides.area_id = results[0].area_id;
        $scope.slides.area_name = results[0].area_name;
        $scope.slides.locality_id = results[0].locality_id;
        $scope.slides.locality = results[0].locality;
        $scope.slides.propsubtype = results[0].propsubtype;
        $scope.slides.suitable_for = results[0].suitable_for;

        $scope.slides.internal_comment = results[0].internal_comment; 
        $scope.slides.external_comment = results[0].external_comment;
        $scope.slides.prop_tax = results[0].prop_tax;
        $scope.slides.cam_charges = results[0].cam_charges;
        $scope.slides.monthle_rent = results[0].monthle_rent;
        $scope.slides.ag_tenure = results[0].ag_tenure;
        $scope.slides.rent_esc = results[0].rent_esc;
        $scope.slides.lease_start = results[0].lease_start;


        $scope.slides.col1_heading = results[0].col1_heading;
        $scope.slides.col1_value = results[0].col1_value;
        $scope.slides.col2_heading = results[0].col2_heading;
        $scope.slides.col2_value = results[0].col2_value;
        $scope.slides.col3_heading = results[0].col3_heading;
        $scope.slides.col3_value = results[0].col3_value;
        $scope.slides.col4_heading = results[0].col4_heading;
        $scope.slides.col4_value = results[0].col4_value;
        $scope.slides.col5_heading = results[0].col5_heading;
        $scope.slides.col5_value = results[0].col5_value;

        console.log("area"+results[0].area_id);
        console.log("area_name"+results[0].area_name);
        console.log("locality_id"+results[0].locality_id);
        console.log("locality"+results[0].locality);
        console.log("propsubtype"+results[0].propsubtype);
        console.log("suitable_for"+results[0].suitable_for);
        var address="Andheri West, Mumbai";
        $scope.area_name = results[0].area_name;
        address = results[0].locality+ ","+results[0].area_name;
        //address = results[0].location;
        $suitable_for = results[0].propsubtype;
        /*if (results[0].suitable_for!==" ")
        {
            $suitable_for = results[0].suitable_for;
        }*/
        

        

        
        console.log('address:'+address);
        /*const drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.MARKER,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [
            google.maps.drawing.OverlayType.MARKER,
            google.maps.drawing.OverlayType.CIRCLE,
            google.maps.drawing.OverlayType.POLYGON,
            google.maps.drawing.OverlayType.POLYLINE,
            google.maps.drawing.OverlayType.RECTANGLE,
            ],
        },
        markerOptions: {
            icon:
            "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
        },
        circleOptions: {
            fillColor: "#ffff00",
            fillOpacity: 1,
            strokeWeight: 5,
            clickable: false,
            editable: true,
            zIndex: 1,
        },
        polygonOptions: {
            editable: true
        },
        polylineOptions : {
            strokeColor: "red",
            strokeOpacity: 1.0,
            strokeWeight: 2,
            editable: true
        }
        });
        drawingManager.setMap(map);
        $scope.deleteSelectedShape = function() 
        {
            if (selectedShape) {
              selectedShape.setMap(null);
              // To show:
              drawingManager.setOptions({
                drawingControl: true
              });
            }
        }
        function clearSelection() {
        if (selectedShape) {
            selectedShape.setEditable(false);
            selectedShape = null;
        }
        }
        
        function setSelection(shape) {
        clearSelection();
        selectedShape = shape;
        shape.setEditable(true);
        selectColor(shape.get('fillColor') || shape.get('strokeColor'));
        }
        
        function deleteSelectedShape() {
        if (selectedShape) {
            selectedShape.setMap(null);
            // To show:
            drawingManager.setOptions({
            drawingControl: true
            });
        }
        }
          */
        $timeout(function () {
        if (address != "None") 
        {
            geocoder.geocode({
                'address': address
            }, function(map_results, status) 
            {
                if (status === 'OK') 
                {
                    //$scope.currentaddress=results[0].formatted_address;
                    document.getElementById('address').innerHTML = map_results[0].formatted_address;
                    console.log(map_results[0].formatted_address);
                    var myLatLng = {lat: map_results[0].geometry.location.lat(), lng: map_results[0].geometry.location.lng()};
                    
                    //map.setCenter(results[0].geometry.location);
                    map.setZoom(12);
                    /*var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map,
                        draggable: true,
                        title: address
                    });*/
                    marker.setPosition(map_results[0].geometry.location);
                    updateMarkerAddress(map_results[0].formatted_address);                       
                    

                    if (map_results[0].geometry.viewport) 
                    {
                        map.fitBounds(map_results[0].geometry.viewport);
                    } else 
                    {
                        map.fitBounds(map_results[0].geometry.bounds);
                    }
                    //map.setCenter(results[0].geometry.location);
                    
                
                    
                        /*var request = {
                        location: results[0].geometry.location,
                        radius: '1500',
                        //type: ['restaurant']
                        type: suitable_for
                        };
                    
                        service = new google.maps.places.PlacesService(map);
                    service.nearbySearch(request, $scope.callback);*/
                    
                    console.log($suitable_for);
                    var request = {
                        location: map_results[0].geometry.location,
                        radius: '1500',
                        //type: ['restaurant']
                        type: $suitable_for
                    };
                
                    service = new google.maps.places.PlacesService(map);
                    service.nearbySearch(request, $scope.callback);
                } else 
                {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        } 
        else 
        { // set back to initial zoom and center
            console.log("else");
            map.setOptions({
                center: new google.maps.LatLng(40.685646, -76.195499),
                zoom: 40
            });
        }
        },10000);
    });

    $scope.callback = function (results, status) {
        if (status !== google.maps.places.PlacesServiceStatus.OK) {
          console.error(status);
          return;
        }
        for (var i = 0, result; result = results[i]; i++) {
          $scope.addMarker(result);
          //$scope.currentaddress=results[i].formatted_address;
        }
        
      }

    

      
    $scope.addMarker = function (place) {
        var marker = new google.maps.Marker({
          map: map,
          position: place.geometry.location,
          icon: {
            //url: 'https://developers.google.com/maps/documentation/javascript/images/circle.png',
            //url: 'dist/img/green-locator.png',
            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
            anchor: new google.maps.Point(10, 10),
            scaledSize: new google.maps.Size(30,30)
          }
        });
    };    

    $scope.show_slide_view = function(slide_name,id)
    {
        console.log(slide_name);
        console.log(id);
        $(".all").css("display","none");
        $("#new_slide").css("display","none");
        $("#"+slide_name+id).css("display","block");
    }
    $scope.slides.image_1_id = 0;
    $scope.slides.image_2_id = 0;
    $scope.slides.image_3_id = 0;
    $scope.slides.image_4_id = 0;
    $scope.slides.image_1_name = 0;
    $scope.slides.image_2_name = 0;
    $scope.slides.image_3_name = 0;
    $scope.slides.image_4_name = 0;
    $scope.new_slide = function()
    {
        $(".all").css("display","none");
        $("#new_slide").css("display","none");
        $("#new_slide").css("display","block");
        $scope.slides.image_1_id = 0;
        $scope.slides.image_2_id = 0;
        $scope.slides.image_3_id = 0;
        $scope.slides.image_4_id = 0;
        $scope.slides.image_1_name = 0;
        $scope.slides.image_2_name = 0;
        $scope.slides.image_3_name = 0;
        $scope.slides.image_4_name = 0;
    }
    $scope.addimage = function(attachment_id,image_name,description)
    {
        console.log($scope.slides.image_1_id);
        console.log($scope.slides.image_2_id);
        console.log($scope.slides.image_3_id);
        console.log($scope.slides.image_4_id);
        if ($scope.slides.image_1_id==0){
            $("#image_1").html('<img class="page_links_img" src="api/v1/uploads/project/'+image_name+'" style="padding:10px;"/>');
            $scope.slides.image_1_id=attachment_id;
            $scope.slides.image_1_name=image_name;
            $scope.slides.description_1 = description;
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            return;
        }
        else{
            if ($scope.slides.image_2_id==0)
            {
                $("#image_2").html('<img class="page_links_img" src="api/v1/uploads/project/'+image_name+'" style="padding:10px;"/>');
                $scope.slides.image_2_id=attachment_id;
                $scope.slides.image_2_name=image_name;
                $scope.slides.description_2 = description;
                $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;

                return;
            }
            else{
                if ($scope.slides.image_3_id==0)
                {
                    $("#image_3").html('<img class="page_links_img" src="api/v1/uploads/project/'+image_name+'" style="padding:10px;"/>');
                    $scope.slides.image_3_id=attachment_id;
                    $scope.slides.image_3_name=image_name;
                    $scope.slides.description_3 = description;
                    $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;

                    return;
                }
                else
                {
                    if ($scope.slides.image_4_id==0)
                    {
                        $("#image_4").html('<img class="page_links_img" src="api/v1/uploads/project/'+image_name+'" style="padding:10px;"/>');
                        $scope.slides.image_4_id=attachment_id;
                        $scope.slides.image_4_name=image_name;
                        $scope.slides.description_4 = description;
                        $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;

                        return;
                    }
                    else{
                        alert("Maximum 4 Images Allowed..!!!");
                        return;
                    }
                }
            }
        }
    }

    $scope.delete_image = function(id)
    {
        if (id==1)
        {
            $("#image_1").html('');
            $scope.slides.image_1_id=0;
            $scope.slides.image_1_name="";
            $scope.slides.description_1 = "";
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            $("#slide_view_1").css("display","block");
            return;
        }
        if (id==2)
        {
            $("#image_2").html('');
            $scope.slides.image_2_id=0;
            $scope.slides.image_2_name="";
            $scope.slides.description_2 = "";
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            $("#slide_view_1").css("display","block");
            return;
        }
        if (id==3)
        {
            $("#image_3").html('');
            $scope.slides.image_3_id=0;
            $scope.slides.image_3_name="";
            $scope.slides.description_3 = "";
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            $("#slide_view_1").css("display","block");
            return;
        }
        if (id==4)
        {
            $("#image_4").html('');
            $scope.slides.image_4_id=0;
            $scope.slides.image_4_name="";
            $scope.slides.description_4 = "";
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            $("#slide_view_1").css("display","block");
            return;
        } 
    }

    $scope.delete_image_record = function(slide_no,id)
    {
        Data.get('delete_image_record/'+slide_no+'/'+id).then(function (results) {
            $scope.slides={};
            Data.get('getslidedata').then(function (results) {
                $scope.slide_data = results;
                $("#slide_view_1").css("display","block");
            });
        });
    }

    $scope.save_ppt_description = function(slide_no,description)
    {
        Data.get('save_ppt_description/'+slide_no+'/'+description).then(function (results) {
            
        });
    }


    $scope.removeslide = function(slide_no)
    {
        var deleteslide = confirm('Are you absolutely sure you want to delete?');
        if (deleteslide) {
            Data.get('removeslide/'+slide_no).then(function (results) {
                $scope.slides={};
                Data.get('getslidedata').then(function (results) {
                    $scope.slide_data = results;
                    $("#slide_view_1").css("display","block");
                });
            });
        };
    }

    $scope.saveslide = function(slides)
    {
        Data.post('saveslide', {
            slides: slides
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $scope.slides={};
                Data.get('getslidedata').then(function (results) {
                    $scope.slide_data = results;
                    $(".all").css("display","none");
                    $("#new_slide").css("display","none");
                    $("#slide_view_1").css("display","block");
                    $scope.slides.category = results[0].category;
                    $scope.slides.category_id = results[0].category_id;
                });
            }
        });
    }
    
    $scope.saveslidedata = function(slides)
    {
        Data.post('saveslidedata', {
            slides: slides
        }).then(function (results) {
            Data.toast(results);
        });
    }
    $scope.close_image = function()
    {
        $("#mainimage").css("display","block");
        $("#editimage").css("display","none");
    }
    $scope.edit_image = function(image_name,slide_no,image_number)
    {

        if (slide_no==98)
        {
            image_name = $scope.map_image_name;
        }
        /*var canvas =  document.getElementById("canvas");
        var ctx = canvas.getContext('2d');
        ctx.drawImage(document.getElementById("image_1"), 0, 0);*/
        /*var img = new Image();
        img.src = document.getElementById("image_1");

        img.onload = function() {
          ctx.drawImage(img, 0, 0, img.width, img.height,
                             0, 0, canvas.width, canvas.height);
        }; */ 
        $("#mainimage").css("display","none");
        $("#editimage").css("display","block");
        var myEditor = new tui.ImageEditor(document.querySelector('#tui-image-editor'), {
            includeUI: {
              loadImage: {
                path: 'api/v1/uploads/project/'+image_name,
                 name: image_name
              },
              theme: blackTheme, // or whiteTheme
              //menu: ['shape'],
              //initMenu: 'shape',
              menuBarPosition: 'right'
            },
            cssMaxWidth: 1200,
            cssMaxHeight: 700,
            selectionStyle: {
              cornerSize: 20,
              rotatingPointOffset: 70
            }
        });
        $scope.edited_image = image_name;
        $scope.edited_image_slide_no = slide_no;
        $scope.edited_image_image_number = image_number;
        $scope.image_data={
            file_name :image_name,
            slide_no : slide_no,
            image_number:image_number,
            file_data:''
        };
        //loadImage("https://crm.rdbrothers.com/api/v1/uploads/project/{{edited_image}}");
        //loadImage("http://placehold.it/100x80");
        //ctx.drawImage(, 0, 0);
        
        $scope.save_image = function()
        {
            console.log(myEditor.toDataURL());
            $scope.image_data.file_data = myEditor.toDataURL();
            console.log($scope.image_data);
            Data.post('save_image', {
                image_data: $scope.image_data
            }).then(function (results) {
                Data.toast(results);
                $("#mainimage").css("display","block");
                $("#editimage").css("display","none");
                $("#image_"+slide_no+"_"+image_number).attr("src","api/v1/uploads/project/"+results.image_name)
            });


        }

    }
    
    

    /*Data.get('createreports/'+module_name+'/'+id+'/'+data).then(function (results) {
        $scope.trustedHtml_pages = $sce.trustAsHtml(results[0].pages);
        $scope.html = results[0].htmlstring;
        $scope.trustedHtml_show_report = $sce.trustAsHtml($scope.html);
        $("#top_slide_view_1").css("display","block");
    });*/
    $scope.map_image_name="";
    $scope.capture_image = function()
    {   
        //
        $(".gmnoprint").css("display","none");
        $(".gm-style-mtc").css("display","none");
        $(".gm-fullscreen-control").css("display","none");
        $(".gm-svpc").css("display","none");
        $("#capture_image").css("display","none");
        $scope.image_data={
            slide_no : 98,
            image_number:1,
            category : $scope.slides.category,
            category_id : $scope.slides.category_id,
            file_data:''
        };
        //html2canvas(document.getElementById("map"),{
        html2canvas(document.getElementsByClassName('gm-style'),
        {
            /*width: 1280,
            height: 933,*/ 
            useCORS: true,
            optimized: false,
            allowTaint: false
        }).then(function(canvas) {
            img_data = canvas.toDataURL();
            console.log(canvas.toDataURL());
            $("#capture_image").css("display","none");
            $("#map").css("display","none");
            $("canvas").css("display","none");
            $("#image_98_1").attr("src",img_data);
            $("#map_image").css("display","block");
            
            console.log("map_img"+$("#image_98_1").prop("src"));
           
            $scope.image_captured = "Yes";
            /*var link = document.createElement("a");
            document.body.appendChild(link);
            link.download = "map.jpg";
            link.href = canvas.toDataURL();
            link.target = '_blank';
            link.click();*/

            $scope.image_data.file_data = img_data;
            
            console.log($scope.image_data);
            Data.post('save_map_image', {
                image_data: $scope.image_data
            }).then(function (results) {
                Data.toast(results);
                $("#image_98_1").attr("src","api/v1/uploads/project/"+results.image_name);
                $scope.map_image_name = results.image_name;
                //$("#image_"+slide_no+"_"+image_number).attr("src","api/v1/uploads/project/"+results.image_name)
            });

        });

        /*html2canvas(document.getElementById('map'), {
            useCORS: true,
            optimized: false,
            allowTaint: false,
            onrendered: function (canvas) {
                url = canvas.toDataURL("image/png");
                /*$('#map_image').attr('src',url).show();
                var a = document.createElement('a');
                a.href = url;
                a.download = 'myfile.png';
                a.click();
                console.log("url"+url);
                return url;
            }
        });*/
    }

    $scope.export_report = function(report_type)
    {
        if ($scope.image_captured=="No")
        {
            alert("Please capture map first ..");
            return;
        }
        $scope.slide_data = {};
        /*$(".gmnoprint").css("display","none");
        $(".gm-style-mtc").css("display","none");
        $(".gm-fullscreen-control").css("display","none");
        $(".gm-svpc").css("display","none");
        //html2canvas(document.getElementById("map"),{
           
        html2canvas(document.getElementsByClassName('gm-style'),
        {
            /*width: 1280,
            height: 933,*/ 
            /*useCORS: true,
            optimized: false,
            allowTaint: false
        }).then(function(canvas) {
            var url = canvas.toDataURL();
            console.log(url);
            /*if (url='data:,')
            {
                url = 'dist/img/location.jpg';
            }
            //$("#map_image").attr("src",canvas.toDataURL());
            //console.log("map_img"+$("#map_image").prop("src"));
        
            //    var url = $("#map_image").prop('src');
            //    console.log("url"+url);*/

            var pptx = new PptxGenJS();

            pptx.addSection({ title: 'Masters' });
            var slide1 = pptx.addSlide({masterName:'TITLE_SLIDE', sectionTitle:'Masters'});
            slide1.addImage({ path: "dist/img/ppt_main.jpg", x:0.0, y:0.0, w:'100%', h:'100%' });
            slide1.addNotes('Welcome Page');
            Data.get('getslidedata').then(function (results) {
                $scope.slide_data = results;
                $scope.slides.proptype = results[0].proptype;
                $scope.slides.category = results[0].category;
                $scope.slides.category_id = results[0].category_id;
                $scope.slides.carp_area = results[0].carp_area;
                $scope.slides.sale_area = results[0].sale_area;
                $scope.slides.floor = results[0].floor;
                $scope.slides.car_park = results[0].car_park;
                $scope.slides.price_unit = results[0].price_unit;
                $scope.slides.security_depo = results[0].security_depo;
                $scope.slides.lock_per = results[0].lock_per;
                $scope.slides.lease_end = results[0].lease_end;
                $scope.slides.escalation_lease = results[0].escalation_lease;
                $scope.slides.location = results[0].location;
                $scope.slides.furnishing = results[0].furnishing;
                $scope.slides.owner_name = results[0].owner_name;
                $scope.slides.mob_no = results[0].mob_no;
                $scope.slides.email = results[0].email;
                $scope.slides.project_contact = (results[0].project_contact).toUpperCase() ;
                $scope.slides.usermobileno = results[0].usermobileno;
                $scope.slides.useremail	 = results[0].useremail;
                $scope.slides.col1_heading = results[0].col1_heading;
                $scope.slides.col1_value = results[0].col1_value;
                $scope.slides.col2_heading = results[0].col2_heading;
                $scope.slides.col2_value = results[0].col2_value;
                $scope.slides.col3_heading = results[0].col3_heading;
                $scope.slides.col3_value = results[0].col3_value;
                $scope.slides.col4_heading = results[0].col4_heading;
                $scope.slides.col4_value = results[0].col4_value;
                $scope.slides.col5_heading = results[0].col5_heading;
                $scope.slides.col5_value = results[0].col5_value;

                $scope.slides.internal_comment = results[0].internal_comment; 
                $scope.slides.external_comment = results[0].external_comment;
                $scope.slides.prop_tax = results[0].prop_tax;
                $scope.slides.cam_charges = results[0].cam_charges;
                $scope.slides.monthle_rent = results[0].monthle_rent;
                $scope.slides.ag_tenure = results[0].ag_tenure;
                $scope.slides.rent_esc = results[0].rent_esc;
                $scope.slides.lease_start = results[0].lease_start;

                angular.forEach(results,function(value,key){
                    console.log(value);
                    if (value.slide_no==2)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Main Image');
                        slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addImage({ path: "api/v1/uploads/project/"+value.image_1, x:0.25, y:0.5, w:'96%', h:'83%' });
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                        console.log(value.slide_no);
                    }
                    if (value.slide_no==3)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Commericial Term');
                        slide.addText('Commercial Terms', { placeholder:'title',x:0.3,fontSize:20 });

                        var arrTabRows = [];


                        if ($scope.slides.proptype=="pre-leased")
                        {
                            if ($scope.slides.carp_area)
                            {
                                arrTabRows.push([{ text: 'Carpet Area (sqft)', options: { valign:'top', align:'left'  } },{ text: $scope.slides.carp_area, options: { valign:'top', align:'left' } }]);
                            }

                            if ($scope.slides.sale_area)
                            {
                                arrTabRows.push([{ text: 'Built Up Area (sqft)', options: { valign:'top', align:'left'  } },{ text: $scope.slides.sale_area, options: { valign:'top', align:'left' } }]);
                            }
                        }
                        else
                        {
                            if ($scope.slides.carp_area)
                            {
                                arrTabRows.push([{ text: 'Carpet Area (sqft)', options: { valign:'top', align:'left'  } },{ text: $scope.slides.carp_area, options: { valign:'top', align:'left' } }]);
                            }

                            if ($scope.slides.sale_area)
                            {
                                arrTabRows.push([{ text: 'Built Up Area (sqft)', options: { valign:'top', align:'left'  } },{ text: $scope.slides.sale_area, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.floor)
                            {
                                arrTabRows.push([{ text: 'Proposed  Floors', options: { valign:'top', align:'left'  } },{ text: $scope.slides.floor, options: { valign:'top', align:'left' } }]);
                                
                            }

                            if ($scope.slides.car_park)
                            {
                                arrTabRows.push([{ text: 'Car Park', options: { valign:'top', align:'left'  } },{ text: $scope.slides.car_park, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.price_unit)
                            {
                                arrTabRows.push([{ text: 'Quoted Rent', options: { valign:'top', align:'left'  } },{ text: $scope.slides.price_unit, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.security_depo)
                            {
                                arrTabRows.push([{ text: 'Security Deposit', options: { valign:'top', align:'left'  } },{ text: $scope.slides.security_depo, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.lock_per)
                            {
                                arrTabRows.push([{ text: 'Lock in period', options: { valign:'top', align:'left'  } },{ text: $scope.slides.lock_per, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.lease_end)
                            {
                                arrTabRows.push([ { text: 'Lease Tenure', options: { valign:'top', align:'left'  } },{ text: $scope.slides.lease_end, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.escalation_lease)
                            {
                                arrTabRows.push([{ text: 'Rent Escalation', options: { valign:'top', align:'left'  } },{ text: $scope.slides.escalation_lease, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.location)
                            {
                                arrTabRows.push([{ text: 'Location', options: { valign:'top', align:'left'  } },{ text: $scope.slides.location, options: { valign:'top', align:'left' } }]);
                            }
                            if ($scope.slides.furnishing)
                            {
                                arrTabRows.push([{ text: 'Furnishing Details', options: { valign:'top', align:'left'  } },{ text: $scope.slides.furnishing, options: { valign:'top', align:'left' } }]);
                            }
                        }
                        if ($scope.slides.col1_value)
                        {
                            arrTabRows.push([{ text: $scope.slides.col1_heading, options: { valign:'top', align:'left'  } },{ text: $scope.slides.col1_value, options: { valign:'top', align:'left' } }]);
                        }
                        if ($scope.slides.col2_value)
                        {
                            arrTabRows.push([{ text: $scope.slides.col2_heading, options: { valign:'top', align:'left'  } },{ text: $scope.slides.col2_value, options: { valign:'top', align:'left' } }]);
                        }
                        if ($scope.slides.col3_value)
                        {
                            arrTabRows.push([{ text: $scope.slides.col3_heading, options: { valign:'top', align:'left'  } },{ text: $scope.slides.col3_value, options: { valign:'top', align:'left' } }]);
                        }
                        if ($scope.slides.col4_value)
                        {
                            arrTabRows.push([{ text: $scope.slides.col4_heading, options: { valign:'top', align:'left'  } },{ text: $scope.slides.col4_value, options: { valign:'top', align:'left' } }]);
                        }
                        if ($scope.slides.col5_value)
                        {
                            arrTabRows.push([{ text: $scope.slides.col5_heading, options: { valign:'top', align:'left'  } },{ text: $scope.slides.col5_value, options: { valign:'top', align:'left' } }]);
                        }
                        
                        /*var arrTabRows = [
                            [
                                { text: 'Carpet Area (sqft)', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.carp_area, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Built Up Area (sqft)', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.sale_area, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Proposed  Floors', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.floor, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Car Park', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.car_park, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Quoted Rent', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.price_unit, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Security Deposit', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.security_depo, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Lock in period', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.lock_per, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Lease Tenure', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.lease_end, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Rent Escalation', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.escalation_lease, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Location', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.location, options: { valign:'top', align:'left' } }
                                
                            ],
                            [
                                { text: 'Furnishing Details', options: { valign:'top', align:'left'  } },
                                { text: $scope.slides.furnishing, options: { valign:'top', align:'left' } }
                                
                            ]
                            
                        ];*/
                        slide.addTable(
                            arrTabRows, { x: 2.0, y: 0.8, w: 6.5, rowH: 0.25, fontSize:12, color:'363636', border:{pt:'1', color:'d2aa4b'} }
                        );
                        
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                        console.log(value.slide_no);

                    }
                    if (value.slide_no==4)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Main Image');
                        slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addImage({ path: "api/v1/uploads/project/"+value.image_1, x:0.25, y:0.5, w:'96%', h:'83%' });
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                        console.log(value.slide_no);
                    }
                    if (value.slide_no==5)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Main Image');
                        slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addText($scope.slides.external_comment, { placeholder:'title',x:0.3,y:1,fontSize:20 });
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                        console.log(value.slide_no);
                    }

                    if (value.slide_no>5 && value.slide_no<98)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Images');
                        slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        if (value.image_1)
                        {
                            slide.addImage({ path: "api/v1/uploads/project/"+value.image_1, x:0.25, y:0.5, w:'45%', h:'38%' });
                        }
                        if (value.image_2)
                        {
                            slide.addImage({ path: "api/v1/uploads/project/"+value.image_2, x:5.0, y:0.5, w:'45%', h:'38%' });
                        }
                        if (value.image_3)
                        {
                            slide.addImage({ path: "api/v1/uploads/project/"+value.image_3, x:0.25, y:3.0, w:'45%', h:'38%' });
                        }
                        if (value.image_4)
                        {
                            slide.addImage({ path: "api/v1/uploads/project/"+value.image_4, x:5.0, y:3.0, w:'45%', h:'38%' });
                        }
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                    }
                    if (value.slide_no==98)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Location ');
                        slide.addText('Location', { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});                    
                        slide.addImage({ path: "api/v1/uploads/project/"+value.image_1, x:0.25, y:0.5, w:'95%', h:'84%' });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                    }
                    if (value.slide_no==99)
                    {
                        var slide = pptx.addSlide({masterName:'THANKS_SLIDE', sectionTitle:'Masters'});
                        slide.addImage({ path: "dist/img/thanks.jpg",  x:0.0, y:0.0, w:'100%', h:'100%'  });
                        slide.addNotes('Thanks Page');
                        slide.addText($scope.slides.project_contact, { placeholder:'title',x:1.2,y:4,fontSize:24 , align:'center'});
                        slide.addText('Call:'+$scope.slides.usermobileno, { placeholder:'title',x:1.2,y:4.4,fontSize:14, align:'center' });
                        slide.addText('Email:'+$scope.slides.useremail, { placeholder:'title',x:1.2,y:4.7,fontSize:14, align:'center' });
                    }

                        /*var slide2 = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                    slide2.addNotes('Main Image');
                    slide2.addText('Main Building', { placeholder:'title',x:0.3,fontSize:20 });
                    slide2.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'96%', h:'83%' });
                    slide2.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                    slide2.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide2.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide2.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                    slide2.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});

                    var slide3 = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                    slide3.addNotes('Commericial Term');
                    slide3.addText('Commercial Terms', { placeholder:'title',x:0.3,fontSize:20 });
                    
                    var arrTabRows = [
                        [
                            { text: 'Carpet Area (sqft)', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.carp_area, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Built Up Area (sqft)', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.sale_area, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Proposed  Floors', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.floor, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Car Park', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.car_park, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Quoted Rent', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.price_unit, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Security Deposit', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.security_depo, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Lock in period', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.lock_per, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Lease Tenure', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.lease_end, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Rent Escalation', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.escalation_lease, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Location', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.location, options: { valign:'top', align:'left' } }
                            
                        ],
                        [
                            { text: 'Furnishing Details', options: { valign:'top', align:'left'  } },
                            { text: $scope.slides.furnishing, options: { valign:'top', align:'left' } }
                            
                        ]
                        
                    ];
                    slide3.addTable(
                        arrTabRows, { x: 2.0, y: 0.8, w: 6.5, rowH: 0.25, fontSize:12, color:'363636', border:{pt:'1', color:'d2aa4b'} }
                    );
                    
                    slide3.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                    slide3.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide3.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide3.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                    slide3.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});

                    var slide4 = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                    slide4.addNotes('Images');
                    slide4.addText('Image Page', { placeholder:'title',x:0.3,fontSize:20 });
                    slide4.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                    slide4.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});

                    slide4.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'45%', h:'38%' });
                    slide4.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:5.0, y:0.5, w:'45%', h:'38%' });
                    slide4.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:0.25, y:3.0, w:'45%', h:'38%' });
                    slide4.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:5.0, y:3.0, w:'45%', h:'38%' });


                    slide4.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide4.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                    slide4.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});

                    var slide5 = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                    slide5.addNotes('Image Page ');
                    slide5.addText('Image Page', { placeholder:'title',x:0.3,fontSize:20 });
                    slide5.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                    slide5.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    
                    slide5.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'45%', h:'38%' });
                    slide5.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:5.0, y:0.5, w:'45%', h:'38%' });
                    slide5.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:0.25, y:3.0, w:'45%', h:'38%' });
                    slide5.addImage({ path: "api/v1/uploads/project/p_271_1606296257_8.jpg", x:5.0, y:3.0, w:'45%', h:'38%' });

                    slide5.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide5.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                    slide5.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});

                    var slide6 = pptx.addSlide({masterName:'THANKS_SLIDE', sectionTitle:'Masters'});
                    slide6.addImage({ path: "dist/img/thanks.jpg",  x:0.0, y:0.0, w:'100%', h:'100%'  });
                    slide6.addNotes('Thanks Page');*/
                
                });
                //pptx.writeFile('properties_report'+Date.now())
                //.then(function(fileName){ console.log('Saved! File Name: '+fileName) });
                pptx.write("base64")
                    .then((data) => {

                        console.log("write as base64: Here are 0-100 chars of `data`:\n");
                        //console.log(data.substring(0, 100));
                        //console.log(data);
                        $scope.image_data={
                            category :$scope.slides.category,
                            category_id :$scope.slides.category_id,
                            file_data:''
                        };
                        $scope.image_data.file_data = data;
                        console.log($scope.image_data.file_data);
                        Data.post('save_ppt', {
                            image_data: $scope.image_data
                        }).then(function (results) {
                            Data.toast(results);
                            //$("#image_98_1").attr("src","api/v1/uploads/project/"+results.image_name);
                            //$scope.map_image_name = results.image_name;
                            //$("#image_"+slide_no+"_"+image_number).attr("src","api/v1/uploads/project/"+results.image_name)
                        });
                    })
                    .catch((err) => {
                        console.error(err);
                    });

            });
    
        /*});
        /*
        var slide = pptx.addSlide();
        var optsTitle = { color:'9F9F9F', marginPt:3, border:[0,0,{pt:'1',color:'CFCFCF'},0] };
        pptx.layout = 'LAYOUT_WIDE';
        //pptx.layout({ name:'A3', width:16.5, height:11.7 });
        //slide.slideNumber({ x:0.5, y:'90%' });
        slide.addTable( [ [{ text:'Simple Example', options:optsTitle }] ], { x:0.5, y:0.13, w:12.5 } );

        //slide.addText('Hello World!', { x:0.5, y:0.7, w:6, h:1, color:'0000FF' });
        slide.addText('Hello 45! ', { x:0.5, y:0.5, w:6, h:1, fontSize:36, color:'0000FF', shadow:{type:'outer', color:'00AAFF', blur:2, offset:10, angle: 45, opacity:0.25} });
        slide.addText('Hello 180!', { x:0.5, y:1.0, w:6, h:1, fontSize:36, color:'0000FF', shadow:{type:'outer', color:'ceAA00', blur:2, offset:10, angle:180, opacity:0.5} });
        slide.addText('Hello 355!', { x:0.5, y:1.5, w:6, h:1, fontSize:36, color:'0000FF', shadow:{type:'outer', color:'aaAA33', blur:2, offset:10, angle:355, opacity:0.75} });

        // Bullet Test: Number
        slide.addText(999, { x:0.5, y:2.0, w:'50%', h:1, color:'0000DE', bullet:true });
        // Bullet Test: Text test
        slide.addText('Bullet text', { x:0.5, y:2.5, w:'50%', h:1, color:'00AA00', bullet:true });
        // Bullet Test: Multi-line text test
        slide.addText('Line 1\nLine 2\nLine 3', { x:0.5, y:3.5, w:'50%', h:1, color:'AACD00', bullet:true });

        // Table cell margin:0
        slide.addTable([['margin:0']], { x: 0.5, y: 1.1, margin: 0, w: 0.75, fill: { color: 'FFFCCC' } });

        // Fine-grained Formatting/word-level/line-level Formatting
        slide.addText(
            [
                { text:'right line', options:{ fontSize:24, fontFace:'Courier New', color:'99ABCC', align:'right', breakLine:true } },
                { text:'ctr line',   options:{ fontSize:36, fontFace:'Arial',       color:'FFFF00', align:'center', breakLine:true } },
                { text:'left line',  options:{ fontSize:48, fontFace:'Verdana',     color:'0088CC', align:'left' } }
            ],
            { x: 0.5, y: 3.0, w: 8.5, h: 4, margin: 0.1, fill: { color: '232323' } }
        );


        /*slide.addText(
        [
            { text:'Did You Know?', options:{ fontSize:48, color:pptx.SchemeColor.accent1, breakLine:true } },
            { text:'writeFile() returns a Promise', options:{ fontSize:24, color:pptx.SchemeColor.accent6, breakLine:true } },
            { text:'!', options:{ fontSize:24, color:pptx.SchemeColor.accent6, breakLine:true } },
            { text:'(pretty cool huh?)', options:{ fontSize:24, color:pptx.SchemeColor.accent3 } }
        ],
        { x:1, y:1, w:'80%', h:3, align:'center', fill:{ color:pptx.SchemeColor.background2, transparency:50 } }
        );

        //+getTimestamp())
        pptx.writeFile('properties_report')
        .then(function(fileName){ console.log('Saved! File Name: '+fileName) });

        /*Data.get('export_report/'+module_name+'/'+id+'/'+data+'/'+report_type).then(function (results) {
            //window.open("api//v1//uploads//reports//"+module_name+"//_list."+report_type,"_blank");
            window.location.href = "api//v1//uploads//reports//"+module_name+"_list."+report_type;
            //window.open("api//v1//uploads//reports//"+module_name+"_list_"+id+"."+report_type);

        });*/
    };

});
