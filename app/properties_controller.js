app.controller('Properties_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce, $templateCache,$route,$window ) {
    var cat = $routeParams.cat;
    $scope.cat = cat;
    
    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
    $scope.regular_list = "Yes";
    $scope.pagenavigation = function(which_side)
    {
        $scope.listproperties = {};
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
            Data.get('properties_list_ctrl/'+$scope.cat+'/'+$scope.id+'/'+$scope.next_page_id).then(function (results) {
                $scope.listproperties = results;
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
            $scope.newsearch_properties($scope.searchdata,'pagenavigation');
            
        }
    }

    var id = $routeParams.id;
    $scope.id = id;
    $scope.no_image = "no_image.jpg";
    $rootScope.propertiesmenu = true;
    $scope.showAmount = numDifferentiation;
    $scope.searchdata = {};
    $scope.listproperties = {};

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ($scope.cat=='residential')
    {
        console.log($str);
        if ((($str).indexOf("properties_residential_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("properties_residential_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("properties_residential_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("properties_residential_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='pre-leased')
    {

        if ((($str).indexOf("properties_preleased_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("properties_preleased_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("properties_preleased_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("properties_preleased_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='retail')
    {

        if ((($str).indexOf("properties_retail_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("properties_retail_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("properties_retail_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("properties_retail_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='commercial')
    {

        if ((($str).indexOf("properties_commercial_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("properties_commercial_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("properties_commercial_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("properties_commercial_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='others')
    {

        if ((($str).indexOf("properties_others_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("properties_others_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("properties_others_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("properties_others_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if (!$scope.view_rights)
    {
        $scope.listproperties = {};
        alert("You don't have rights to use this option..");
        return;
    }

    $scope.sms_data = {};
    var values_loaded = "false";
    $scope.open_search = function()
    {
        if (values_loaded=="false")
        {
            values_loaded="true";
            console.log("opening");
            $timeout(function () { 
                Data.get('selectdropdownsNew/PROP_SUB_TYPE/'+cat).then(function (results) { 
                    $scope.propsubtypes = results; 
                }); 
            }, 100);

            $timeout(function () { 
                Data.get('selectdropdowns/SUITABLE_FOR').then(function (results) { 
                    $scope.suitable_fors = results; 
                }); 
            }, 100);

        
            $scope.change_suitablefor = function (propsubtype) 
            { 
                Data.get('change_suitablefor/'+propsubtype).then(function (results) { 
                    $scope.suitable_fors = results; 
                });
            }
        
            
            $scope.change_sub_source = function (source_channel) 
            { 
                console.log(source_channel);
                Data.get('change_sub_source/'+source_channel).then(function (results) { 
                    $scope.sub_sources = results; 
                });
            }
        
            $timeout(function () { 
                Data.get('selectdropdowns/FURNITURE').then(function (results) {
                    $scope.furnitures = results;
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
                Data.get('selectdropdowns/AMENITIES').then(function (results) {
                    $scope.amenities = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('selectdropdowns/PRJ_SPECIFICATIONS').then(function (results) {
                    $scope.pro_specifications = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('selectdropdowns/PARKING_DIR').then(function (results) {
                    $scope.parkings = results;
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
        
        
            Data.get('selectdropdowns/AGREEMENT_STAGE').then(function (results) {
                $scope.agreement_stages = results;
            });
        
            $timeout(function () { 
                Data.get('getdatavalues/wing/'+cat).then(function (results) {
                    $scope.wings = results;
                });
            }, 100);
        
        
            $timeout(function () { 
                Data.get('getdatavalues/unit/'+cat).then(function (results) {
                    $scope.units = results;
                });
            }, 100);
        
        
            $timeout(function () { 
                Data.get('getdatavalues/floor/'+cat).then(function (results) {
                    $scope.floors = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('getdatavalues/bedrooms/'+cat).then(function (results) {
                    $scope.bedrooms = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues/property_id/'+cat).then(function (results) {
                    $scope.property_ids = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('getdatavalues/project_name/'+cat).then(function (results) {
                    $scope.project_names = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('getdatavalues/building_name/'+cat).then(function (results) {
                    $scope.building_names = results;
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
        /*$timeout(function () { 
            field_name = "property_id";
            value = "1"
            Data.get('getdropdown_values/'+field_name+'/'+value+'/'+$scope.cat).then(function (results) {
                $scope.search_propertys = results;
            });
        }, 100);*/
    };

    $timeout(function () { 
        Data.get('matching_enquiries_list/'+cat).then(function (results) {
            $scope.matching_enquiries_list = results;
        });
    }, 100);

    $scope.link_to_enquiry = function(property_id,enquiry_id)
    {       
        Data.get('link_to_enquiry/'+property_id+'/'+enquiry_id).then(function (results) {
        });
    }
    
    $scope.select_assign_to = function(teams,sub_teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/'+sub_teams).then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }

    $timeout(function () { 
        Data.get('properties_list_ctrl/'+$scope.cat+'/'+$scope.id+'/'+$scope.next_page_id).then(function (results) {
            $scope.listproperties = results;
            $scope.next_page_id = 30;
            
        });
    }, 100);
    
    

    $timeout(function () { 
        Data.get('m_properties_list_ctrl/'+$scope.cat+'/'+$scope.id+'/'+$scope.next_page_id).then(function (results) {
            $scope.m_properties = results;
        });
    }, 100);
    
    $scope.me_count = function(property_id)
    {
        $timeout(function () { 
            Data.get('me_count/'+property_id).then(function (results) {
                console.log(results[0].me_count);
                $("#me"+property_id).html(results[0].me_count);
            });
        }, 100);

    }
    $timeout(function () { 
        Data.get('property_record_count/'+$scope.cat+'/'+$scope.id).then(function (results) {
            $scope.total_records = results[0].property_count;
            $scope.property_count = results[0].property_count;
        });
    }, 100);

    /*$timeout(function () { 
        Data.get('property_count/'+$scope.cat).then(function (results) {
            $scope.property_count = results[0].property_count;
        });
    }, 100);*/

    

    $scope.change_agreement_stage = function(agreement_stage,agreement_id)
    {
        var changeagreementstage = confirm('Are you Sure?');
        if (changeagreementstage) {
            Data.get('change_agreement_stage/'+agreement_stage+'/'+agreement_id).then(function (results) {

            });
        }

    }
    $scope.share_on_website = function(property_id)
    {
        var shareonwebsite = confirm('Are you Sure?');
        if (shareonwebsite) {
            Data.get('share_on_website/property/'+property_id).then(function (results) {
            });
        }
    }

    

    $scope.newsearch_properties = function (searchdata,from_click) {
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
        Data.post('newsearch_properties', {
            searchdata: searchdata
        }).then(function (results) {
            
            if (results[0].property_count>0)
            {
                $scope.listproperties = {};
                $scope.listproperties = results;
                $scope.property_count = results[0].property_count;
                $scope.total_records = results[0].property_count;
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

        /*$timeout(function () { 
            Data.get('selectdropdowns/AMENITIES').then(function (results) {
                $scope.amenities = results;
            });
        }, 100);
        $timeout(function () { 
            Data.get('selectdropdownsNew/PROP_SUB_TYPE/'+cat).then(function (results) { 
                $scope.propsubtypes = results; 
            }); 
        }, 100);
    
        $scope.change_suitablefor = function (propsubtype) 
        { 
            Data.get('change_suitablefor/'+propsubtype).then(function (results) { 
                $scope.suitable_for_lov = results; 
            });
        }
    
        $timeout(function () { 
            Data.get('selectdropdowns/FURNITURE').then(function (results) {
                $scope.furnitures = results;
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
            Data.get('selectdropdowns/AMENITIES').then(function (results) {
                $scope.amenities = results;
            });
        }, 100);
    
        $timeout(function () { 
            Data.get('selectdropdowns/PRJ_SPECIFICATIONS').then(function (results) {
                $scope.pro_specifications = results;
            });
        }, 100);
    
        $timeout(function () { 
            Data.get('selectdropdowns/PARKING_DIR').then(function (results) {
                $scope.parkings = results;
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
                $scope.areas = results;
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
            Data.get('selectusers').then(function (results) {
                $scope.users = results;
            });
        }, 100);


        

        $timeout(function () { 
            Data.get('getdatavalues/wing/'+cat).then(function (results) {
                $scope.wings = results;
            });
        }, 100);
    
    
        $timeout(function () { 
            Data.get('getdatavalues/unit/'+cat).then(function (results) {
                $scope.units = results;
            });
        }, 100);
    
    
        $timeout(function () { 
            Data.get('getdatavalues/floor/'+cat).then(function (results) {
                $scope.floors = results;
            });
        }, 100);

        $timeout(function () { 
            Data.get('getdatavalues/bedrooms/'+cat).then(function (results) {
                $scope.bedrooms = results;
            });
        }, 100);

    
        $timeout(function () { 
            Data.get('getdatavalues/property_id/'+cat).then(function (results) {
                $scope.property_ids = results;
            });
        }, 100);
    
        $timeout(function () { 
            Data.get('getdatavalues/project_name/'+cat).then(function (results) {
                $scope.project_names = results;
            });
        }, 100);
    
        $timeout(function () { 
            Data.get('getdatavalues/building_name/'+cat).then(function (results) {
                $scope.building_names = results;
            });
        }, 100);*/
        $scope.next_page_id = 0;
        
        Data.get('properties_list_ctrl/'+$scope.cat+'/'+$scope.id+'/0').then(function (results) {
            $scope.listproperties = results;
            $scope.next_page_id = 30;
        });
    }

    /*$scope.showimages = function(property_id,project_name,propsubtype,property_for)
    {
        Data.get('properties_images/'+property_id).then(function (results) {
            heading = '';
            if (project_name)
            {
                heading += project_name;
            }
            if (propsubtype)
            {
                heading += ' '+propsubtype;
            }
            if (property_for)
            {
                heading += ' for '+property_for;
            }

            $scope.property_heading = heading;
            $rootScope.property_images = results;
        });
    }*/

    $scope.methods = {};
    $scope.images = {};
    $scope.conf = {
        imgAnim : 'fadeup'
    };
    $scope.showimages = function(property_id,project_heading)
    {
        
        Data.get('properties_imagesslide/'+property_id).then(function (results) {
            $scope.project_heading = project_heading;
            $rootScope.images = results;
            $scope.images = results;
//            console.log($scope.images);
            /*angular.forEach(results,function(value,key){
                console.log(value);
                $scope.images.id = value.attachment_id;
                $scope.images.title = value.description;
                $scope.images.desc = value.file_category;
                $scope.images.thumbUrl = "api/v1/uploads/project/thumb/"+value.filenames;
                $scope.images.bubbleUrl = "api/v1/uploads/project/thumb/"+value.filenames;
                $scope.images.url = "api/v1/uploads/project/"+value.filenames;

                /*angular.forEach(value,function(v1,k1){
                    console.log(k1+":"+v1);
                    $scope.images.push(value.project_id);
                });
                console.log($scope.images);
            });*/
        });
        $scope.methods.open();
		
    }

    $scope.convertdata = {};
    $scope.import_properties = function () {
        $("#properties").css("display","none");
        $("#upload").css("display","block");

    }

    $scope.deal_done = function(property_id)
    {
        Data.get('deal_done/'+property_id).then(function (results) {
            Data.toast(results);

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
            $location.path('audit_trail/property/property_id/'+data);
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
            $location.path('batch_update/property/'+$scope.cat+'/property_id/'+data);
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
            $location.path('manage_group/property/property_id/'+data);
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
                //$location.path('reports/property/property_id/'+data);
                $window.open('#/reports/property/property_id/'+data)
            }
            if (report_type=='multi_property')
            {
                $location.path('multiproperty/property/property_id/'+data);
            }
            if (report_type=='broucher')
            {
                $location.path('send_broucher/property/property_id/'+data);
            }

            if (report_type=='one_mailer')
            {
                $location.path('one_mailer/property/property_id/'+data);
            }
            if (report_type=='pre_leased')
            {
                $location.path('preleased/property/property_id/'+data);
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
                Data.get('getreport_fields/property/unselected').then(function (results) {
                    $rootScope.unselected_fields = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getreport_fields/property/selected').then(function (results) {
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
                Data.get('getreport_fields/property/unselected').then(function (results) {
                    $rootScope.unselected_fields = results;
                });
            }, 100);
    
            $timeout(function () { 
                Data.get('getreport_fields/property/selected').then(function (results) {
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
            Data.get('exportdata/property/property_id/'+data+'/'+$scope.option_value).then(function (results) {
                window.location="api//v1//uploads//property_list.xlsx";
            });
        }, 100);
      }


    $scope.link_enquiries = function ()
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
            $window.open('#/enquiries_list/'+$scope.cat+'/0/'+data);
        }

    }

    

    $scope.uploadproperty_data = function (convertdata) {
        //convertdata.file_name = $("#file_name").val();
        var currentdate = new Date(); 
        var datetime = currentdate.getFullYear()+ "-" + (currentdate.getMonth()+1) + "-" +  currentdate.getDate()+ " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
        convertdata.created_date = datetime;
        convertdata.file_name = $("#file_name").val();
        Data.post('uploadproperty', {
            convertdata: convertdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('/properties_list/'+$scope.cat);
            }
        });
    };

    $scope.pmatchingenquiries = function(property_id)
    {
        $timeout(function () { 
            Data.get('pmatchingenquiries/'+property_id).then(function (results) {
                $scope.html = results[0].htmlstring;
                var cstring = 'enqtrustedHtml_'+property_id;
                $scope[cstring] = $sce.trustAsHtml($scope.html);
            });
        }, 100);
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
            $location.path('mails_client/property/'+data);
        }
    }

    $timeout(function () { 
        Data.get('selectsms_template/property').then(function (results) {
            $scope.sms_templates = results;
        });
    }, 100);

    $scope.select_sms_template  = function(sms_template_id)
    {
        $timeout(function () { 
            Data.get('get_sms_template/property/'+$scope.sms_data.id+'/'+sms_template_id).then(function (results) {
                $('.wysihtml5-sandbox, .wysihtml5-toolbar').remove();
                $scope.sms_data.message = results[0].text_message;
            });
        }, 100);
    }

    $scope.SendSMS = function(cat,id,mob_no)
    {

        console.log(cat);
        console.log(id);
        console.log(mob_no);
        $scope.sms_data.receipient = mob_no;
        $scope.sms_data.category_id = id;
        $scope.sms_data.category = "property";
        $scope.sms_data.id = id;
    }

    $scope.smssend = function(sms_data)
    {
        console.log(sms_data.text_message);
        var smssending = confirm('Want to send SMS ? ');
        if (smssending) 
        {

            Data.post('smssend', {
                sms_data: sms_data
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                   // $location.path('/properties_list/'+$scope.cat);
                }
            });
        }
    }
    

});


app.controller('Batch_Update_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $sce,$timeout) {
    var module_name = $routeParams.module_name;
    var id = $routeParams.id;
    var data = $routeParams.data;
    var cat = $routeParams.cat;
    // console.log(id);
    // console.log(data);
    // console.log(cat);
    $scope.cat = cat;
    $scope.module_name = module_name;
    $scope.sbatchdata = {dummy:""};
    
    $timeout(function () { 
        Data.get('selectdropdowns/CLIENT_SOURCE').then(function (results) {
            $rootScope.client_sources = results;
        });
    }, 100);

    $scope.change_sub_source = function (source_channel) 
    { 
        console.log(source_channel);
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

    $timeout(function () { 
        Data.get('selectsubteams').then(function (results) {
            $scope.sub_teams_list = results;
        });
    }, 100);

    
    $scope.backto="/login";
    if (module_name=='property')
    {
        $scope.backto = '/properties_list/'+$scope.cat+'/0/0';
    }

    if (module_name=='enquiry')
    {
        $scope.backto = '/enquiries_list/'+$scope.cat+'/0/0';
    }

    if (module_name=='project')
    {
        $scope.backto = '/project_list/0';
    }

    if (module_name=='contact')
    {
        $scope.backto = '/contacts_list/'+$scope.cat;
    }
    
    if (module_name=='expenses')
    {
        $scope.backto = '/expense_list';
    }

    $scope.batch_update = function (sbatchdata) {
        // console.log(sbatchdata);
        sbatchdata.data = data;
        sbatchdata.id = id;
        sbatchdata.module_name = module_name;
        // console.log(sbatchdata);
        Data.post('batch_update', {
            batchdata: sbatchdata
        }).then(function (results) {
            Data.toast(results);
            // console.log($scope.backto);
            $location.path($scope.backto);

        });
    };

});

   
function geocodePosition(pos) {
    geocoder.geocode({
      latLng: pos
    }, function(responses) {
      if (responses && responses.length > 0) {
        updateMarkerAddress(responses[0].formatted_address);
      } else {
        updateMarkerAddress('Cannot determine address at this location.');
      }
    });
}
   
  
function convertToWord (amount) 
{
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

function activate_fileinput()
{
var footerTemplate = '<div class="file-thumbnail-footer" style ="height:94px">\n' +	
'  <input class="kv-input kv-new form-control input-sm form-control-sm text-center {TAG_CSS_NEW}"  value="" placeholder="Enter Title...">\n' +
'  <input class="kv-input kv-new  {TAG_CSS_NEW}" type="checkbox" value="true" >Set as main image' +
'  <input class="kv-input kv-new  {TAG_CSS_NEW}" type="checkbox" value="true" checked>Share' +
'<select id="file_category" name="file_category" class="kv-input kv-new form-control input-sm form-control-sm text-center {TAG_CSS_NEW}">'+
                '<option value="">Select Category</option>'+
                '<option value="Special Offer Detail">Special Offer Detail</option>'+
                '<option value="Amenities">Amenities</option>'+
                '<option value="Site Plan">Site Plan</option>'+
                '<option value="Special Offer Listing">Special Offer Listing</option>'+
                '<option value="special offer">special offer</option>'+
                '<option value="Elevation">Elevation</option>'+
                '<option value="Location">Location</option>'+
                '<option value="Floor Plans">Floor Plans</option>'+
                '<option value="General">General</option>'+
                '<option value="Sample Flat">Sample Flat</option>'+
                '</select>\n'+
'  <input class="kv-input kv-new form-control input-sm form-control-sm text-center {TAG_CSS_NEW}"  value="" placeholder="Enter Sub Category...">\n' +


'   <div class="small" style="margin:15px 0 2px 0">{size}</div> {progress}\n{indicator}\n{actions}\n' +
'</div>';

var footerTemplate_cert = '<div class="file-thumbnail-footer" style ="height:94px">\n' +
'<select id="cert_category" name="cert_category" class="cert_images kv-input kv-new form-control input-sm form-control-sm text-center {TAG_CSS_NEW}">'+
                '<option value="">Select Certificate</option>'+
                '<option value="Occupation Cert">Occupation Cert</option>'+
                '<option value="Society Reg">Society Reg</option>'+
                '<option value="Commencement">Commencement</option>'+
                '<option value="Other">Other</option>'+
                '</select>\n'+

'   <div class="small" style="margin:15px 0 2px 0">{size}</div> {progress}\n{indicator}\n{actions}\n' +
'</div>';




    $("#file-1").fileinput({
        uploadAsync: false,
        showUpload: false,
        uploadUrl: './api/v1/property_uploads',
        allowedFileExtensions : ['jpg','jpeg', 'png','gif','image','3gp'],
        overwriteInitial: false,
        maxFileSize: 1500000,
        maxFilesNum: 25,
        maxFileCount:100,
        layoutTemplates: {footer: footerTemplate},

        uploadExtraData: function() {  
        var out = {}, key, i = 0,j=1,z=1;
        $('.kv-input').each(function() {
            if ($(this).hasClass('main_images'))
            {
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
                }
                if (j==5)
                {
                    key = 'file_sub_category_'+z;
                    j=0;
                    z++;
                }
                j++;
                //key = $el.hasClass('kv-new') ? 'new_' + i : 'init_' + i;
                out[key] = value;
                i++;
            }
        });
        return out;
    },

        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });

    $('#file-1').on('fileloaded', function(event, file, previewId, index, reader) {
        $("#file_name").val(file.name);
        console.log("fileloaded");
    });

    $("#file_docs").fileinput({
        uploadAsync: false,
        showUpload: false,
        uploadUrl: './api/v1/property_uploads_docs',
        allowedFileExtensions : ['jpg','jpeg', 'png','gif','image','3gp','pdf','docx','xlxs','pptx'],
        overwriteInitial: false,
        maxFileSize: 15000000,
        maxFilesNum: 25,
        maxFileCount:100,
        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });

    $('#file_docs').on('fileloaded', function(event, file, previewId, index, reader) {
        $("#file_docs_name").val(file.name);
        console.log("fileloaded");
    });

    $("#file_videos").fileinput({
        uploadAsync: false,
        showUpload: false,
        uploadUrl: './api/v1/property_uploads_videos',
        allowedFileExtensions : ['MP4','3GP','OGG','avi','vob'],
        overwriteInitial: false,
        maxFileSize: 999000000,
        maxFilesNum: 25,
        maxFileCount:100,
        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });

    $('#file_videos').on('fileloaded', function(event, file, previewId, index, reader) {
        $("#file_videos_name").val(file.name);
        console.log("fileloaded");
    });

    

    $("#file_occu").fileinput({
        uploadAsync: false,
        showUpload: false,
        uploadUrl: './api/v1/property_uploads_occu',
        allowedFileExtensions : ['jpg','jpeg', 'png','gif','image','3gp','pdf','docx','xlxs'],
        overwriteInitial: false,
        maxFileSize: 1500000,
        maxFilesNum: 25,
        maxFileCount:100,
        layoutTemplates: {footer: footerTemplate_cert},
        
        uploadExtraData: function() {  
        var out = {}, key, z=1;
        $('.kv-input').each(function() {
            $el = $(this);
            if ($($el).hasClass('cert_images'))
            {
                
                value = $el.val();
                key = 'cert_category_'+z;
                out[key] = value;
            }
        });
        return out;
    },
        slugCallback: function(filename) {
            return filename.replace('(', '_').replace(']', '_');
        }
    });

    $('#file_occu').on('fileloaded', function(event, file, previewId, index, reader) {
        $("#file_occu_name").val(file.name);
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


app.controller('Properties_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, Data, $sce) {
    var cat = $routeParams.cat;
    $scope.cat = cat;
    $scope.project_id = 0;

    $scope.property = {};
    $scope.$watch($scope.property.assign_to, function() {
        $scope.property.assign_to = [$rootScope.user_id];
    });
    
    $scope.$watch($scope.property.teams, function() {
        $scope.property.teams = [$rootScope.bo_id];
    });

    $scope.$watch($scope.property.sub_teams, function() {
        $scope.arr = (([$rootScope.sub_teams]).toString());
        console.log($scope.arr);
        $scope.property.sub_teams = [$scope.arr];
    });

    $scope.contact = {};
    $scope.towords = function(number)
    {
        console.log(number);
        return convertToWord(number);
        
    }
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ($scope.cat=='residential')
    {
        if ((($str).indexOf("properties_residential_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("properties_residential_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("properties_residential_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("properties_residential_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='pre-leased')
    {

        if ((($str).indexOf("properties_preleased_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("properties_preleased_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("properties_preleased_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("properties_preleased_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='retail')
    {

        if ((($str).indexOf("properties_retail_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("properties_retail_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("properties_retail_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("properties_retail_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='commercial')
    {

        if ((($str).indexOf("properties_commercial_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("properties_commercial_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("properties_commercial_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("properties_commercial_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if ($scope.cat=='others')
    {

        if ((($str).indexOf("properties_others_view"))!=-1)
        {
            $scope.view_rights = true;
            console.log($scope.view_rights);
        }
        if ((($str).indexOf("properties_others_create"))!=-1)
        {
            $scope.create_rights = true;
            console.log($scope.create_rights);
        }
        if ((($str).indexOf("properties_others_update"))!=-1)
        {
            $scope.update_rights = true;
            console.log($scope.update_rights);
        }
        if ((($str).indexOf("properties_others_delete"))!=-1)
        {
            $scope.delete_rights = true;
            console.log($scope.delete_rights);
        }
    }
    if (!$scope.create_rights)
    {
        $scope.property = {};
        alert("You don't have rights to use this option..");
        return;
    }
    /*var currentdate = new Date(); 
    var dd = currentdate.getDate(); 
    var MM = (currentdate.getMonth()+1); 
    var yyyy = currentdate.getFullYear(); 
    if (dd<10) { dd='0'+dd; } 
    if (MM<10) { mm='0'+mm; }
     $scope.property.reg_date = dd+'/'+MM+'/'+yyyy;*/
  /* $scope.reg_date = $filter("date")(Date.now(), 'dd/MM/yyyy');*/
    
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
        Data.get('selectdropdownsNew/PROP_SUB_TYPE/'+cat).then(function (results) { 
            $rootScope.propsubtypes = results; 
        }); 
    }, 100);

    $timeout(function () { 
        Data.get('selecttask').then(function (results) {
            $scope.task_list = results;
        });
    }, 100);
    
    $scope.change_suitablefor = function (propsubtype) 
    { 
        Data.get('change_suitablefor/'+propsubtype).then(function (results) { 
            $scope.suitable_for_lov = results; 
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

    $scope.change_sub_source = function (source_channel) 
    { 
        console.log(source_channel);
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
        Data.get('selectdropdowns/PROP_STATUS').then(function (results) {
            $rootScope.prop_statuss = results;
            //$("#proj_status").select2().select2('val', "Available");
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/AMENITIES').then(function (results) {
            $rootScope.amenities = results;
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

    
    $scope.getclientemail = function(contact_off,contact_id)
    {
        Data.get('getclientemail/'+contact_off+"/"+contact_id).then(function (results) {
            $scope.client_mobile_number = results[0].client_mobile_number;
            $scope.client_email_id = results[0].client_email_id;
        });
    }


    $scope.getowner_names = function(dev_owner_name,cat)
    {
        if (cat=='Owner')
        {
            cat = 'Client';
        }
        if (dev_owner_name==' ' || dev_owner_name =='')
        {
            dev_owner_name = "blank";
        }
        $timeout(function () { 
            Data.get('getowner_names/'+dev_owner_name+'/'+cat).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_owner = $sce.trustAsHtml($scope.html);
                $(".mydropdown-menu").css("display","block");
            });
        }, 100);
    };

    $scope.getcustomername = function(contact_id,name)
    {
        $scope.property.dev_owner_id = contact_id;
        $scope.property.dev_owner_name = name;
        $(".mydropdown-menu").css("display","none");
        $timeout(function () { 
            Data.get('getproperties_exists/'+contact_id).then(function (results) {
                if (results[0].count>0)
                {
                    $scope.html = results[0].htmlstring;
                    $scope.trustedHtml_properties_exists = $sce.trustAsHtml($scope.html);
                    $("#properties_exists").css("display","block");
                }
                else
                {
                    $("#properties_exists").css("display","none");
                }
            });
        }, 100);

    }
    $scope.calculate_exp_price = function(field_name)
    {

        if (field_name=='price_unit')
        {
            $scope.property.exp_price = (parseFloat($scope.property.sale_area) * parseFloat($scope.property.price_unit)).toFixed(2);
        }
        if (field_name=='price_unit_carpet')
        {
            $scope.property.exp_price = (parseFloat($scope.property.carp_area) * parseFloat($scope.property.price_unit_carpet)).toFixed(2);
        }
    }

    $scope.calculate_loading = function(field_name)
    {

        // CALCULATE LOADING

        if ($scope.property.carp_area>0 && $scope.property.sale_area>0)
        {
            diff = $scope.property.sale_area - $scope.property.carp_area;
            $scope.property.loading = (diff * (100 / $scope.property.carp_area)).toFixed(2);
        }
        /*else if ($scope.property.sale_area1>0 && $scope.property.carp_area==0 && $scope.property.loading>0 )
        {
            diff = ((100+parseFloat($scope.property.loading))/100);
            $scope.property.carp_area = ($scope.property.sale_area / diff).toFixed(2);
            console.log("diff"+diff);
            console.log("carp"+$scope.property.carp_area);

        }*/
        if ($scope.property.exp_price>0)
        {
            
            if ($scope.property.sale_area>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                }
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                }
            }

            if ($scope.property.carp_area>0)
            {
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                }
            }
        }
        if (field_name == "deposite_month")
        {
            if ($scope.property.deposite_month>0 && $scope.property.exp_price>0)
            {
                $scope.property.security_depo = ($scope.property.deposite_month*$scope.property.exp_price).toFixed(2); 

                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.security_depo = (($scope.property.exp_price*1000)*$scope.property.deposite_month).toFixed(2); 
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.security_depo = (($scope.property.exp_price*100000)*$scope.property.deposite_month).toFixed(2);
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.security_depo = (($scope.property.exp_price*10000000)*$scope.property.deposite_month).toFixed(2); 
                    
                }
            }
        }
    }

    /*$scope.calculate_loading = function(field_name)
    {

        if (field_name == "sale_area")
        {
            if ($scope.property.carp_area>0)
            {
                diff = $scope.property.sale_area - $scope.property.carp_area;
                $scope.property.loading = (diff * (100 / $scope.property.carp_area)).toFixed(2);
            }
            field_name = "exp_price";
        }
        if (field_name == "carp_area")
        {
            if ($scope.property.sale_area>0)
            {
                diff = $scope.property.sale_area - $scope.property.carp_area;
                $scope.property.loading = (diff * (100 / $scope.property.carp_area)).toFixed(2);
            }
            field_name = "exp_price";
        }
        if (field_name == "loading")
        {
            if ($scope.property.sale_area>0)
            {
                diff = ((100+parseFloat($scope.property.loading))/100);
                $scope.property.carp_area = ($scope.property.sale_area / diff).toFixed(2);

            }
            field_name = "exp_price";
        }
        if (field_name == "exp_price_para")
        {
            $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
            $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2); 
            
            if ($scope.property.exp_price_para == "Th")
            {
               $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
               $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
            } 
            if ($scope.property.exp_price_para == "Lac")
            {
                $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
            } 
            if ($scope.property.exp_price_para == "Cr")
            {
               $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
               $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); nit_carpet_para = "Cr";
            } 

        }
        
        if (field_name == "exp_price")
        {
            if ($scope.property.exp_price>0 && $scope.property.sale_area>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2); 
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
             } 
        
            if ($scope.property.exp_price>0 && $scope.property.carp_area>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
            }
            
        }

        if (field_name == "price_unit")
        {
            if ($scope.property.sale_area>0 && $scope.property.price_unit>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
            }
            if ($scope.property.carp_area>0 && $scope.property.price_unit>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                } 

            }
            
        }

        if (field_name == "price_unit_carpet")
        {
            if ($scope.property.carp_area>0 && $scope.property.price_unit_carpet>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(3);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                   
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                } 

            }
            if ($scope.property.sale_area>0 && $scope.property.price_unit_carpet>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(3);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
            }
            
           
        }
        if (field_name == "deposite_month")
        {
            if ($scope.property.deposite_month>0 && $scope.property.exp_price>0)
            {
                $scope.property.security_depo = ($scope.property.deposite_month*$scope.property.exp_price).toFixed(2); 
                
            }
        }
        


    }
    */

    
    $scope.geocodeAddress = function (field_name,value) 
    {  

        if (field_name=='locality_id')
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+value).then(function (results) {
                    $scope.property.area_id = results[0].area_id;
                    $scope.property.city = results[0].city;
                    $scope.property.state = results[0].state;
                    $scope.property.country = results[0].country;
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
                    $scope.property.city = results[0].city;
                    $scope.property.state = results[0].state;
                    $scope.property.country = results[0].country;
                });
            }, 100);
        }
        //var geocoder = new google.maps.Geocoder();
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
                    updateMarkerAddress(results[0].formatted_address);
                    if (results[0].geometry.viewport) 
                    {
                        map.fitBounds(results[0].geometry.viewport);
                    } else 
                    {
                        map.fitBounds(results[0].geometry.bounds);
                    }
                    suitable_for = $scope.property.propsubtype;
                    if ($scope.property.suitable_for)
                    {
                        suitable_for = $scope.property.suitable_for;
                    }
                    var request = {
                        location: results[0].geometry.location,
                        radius: '1500',
                        //type: ['restaurant']
                        type: suitable_for
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
		    map.setOptions({
		        center: new google.maps.LatLng(40.685646, -76.195499),
		        zoom: 40
		    });
        }

    };

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
    
    //$scope.properties_add_new = {property:''};
    $scope.properties_add_new = function (property) {
        property.proptype = cat;
        property.internal_comment = $("#internal_comment").val(); 
        property.external_comment = $("#external_comment").val();
        var count = $("#file-1").fileinput("getFilesCount");
        console.log(count);
        if (count>0)
        {
        }
        else
        {
            alert("Minimum 1 Image Required...!!!!");
            return;
        }
        property.file_name = $("#file_name").val();
        Data.post('properties_add_new', {
            property: property
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                property_id = results.property_id;
                $('#file-1').fileinput('upload');
                $('#file_occu').fileinput('upload');
                $('#file_docs').fileinput('upload');
                $('#file_videos').fileinput('upload');
                $location.path('reports/property/property_id/'+property_id);
                //$location.path('properties_list/'+$scope.cat+'/0/0');
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
                    if ($scope.temptype=='PROP_SUB_TYPE')
                    {
                        controlvalue = "propsubtypes";
                        Data.get('selectdropdownsNew/PROP_SUB_TYPE/'+cat).then(function (results) { 
                            $rootScope[controlvalue] = {};
                            $scope.$watch($rootScope[controlvalue], function() {
                                $rootScope[controlvalue] = results;
                            }, true);
                        }); 
                    }
                    else
                    {
                        Data.get('selectdropdowns/'+$scope.temptype).then(function (results) {
                            var controlvalue = (($scope.temptype).toLowerCase());
                            if ($scope.temptype=='PARKING_DIR')
                            {
                                controlvalue = "parkings";
                            }
                            if ($scope.temptype=='PROP_STATUS')
                            {
                                controlvalue = "prop_statuss";
                            }
                            
                            if ($scope.temptype=='FURNITURE')
                            {
                                controlvalue = "furnitures";
                            }
                            if ($scope.temptype=='CLIENT_SOURCE')
                            {
                                controlvalue = "client_sources";
                            }
                            if ($scope.temptype=='SUB_SOURCE')
                            {
                                controlvalue = "sub_sources";
                            }
                            if ($scope.temptype=='DIRECTION')
                            {
                                controlvalue = "door_fdirs";
                            }
                            $rootScope[controlvalue] = {};
                            $scope.$watch($rootScope[controlvalue], function() {
                                $rootScope[controlvalue] = results;
                            }, true);
                        });
                    }
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
        contact.contact_off = $scope.property.propfrom;
        contact.file_name = $("#file_name_company_logo").val();
        if ($scope.property.propfrom=='Owner')
        {
            contact.contact_off = "Client";
        }
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_company_logo').fileinput('upload');
                $('#file_visiting_card').fileinput('upload');
                $('#file_contact_pic').fileinput('upload');
                $("#adddeveloper").modal("hide");
                if ($scope.property.propfrom=='Owner')
                {
                    $scope.clients = {};
                    Data.get('selectcontact/Client').then(function (results) {
                        $scope.clients = results;
                    });
                }
                if ($scope.property.propfrom=='Developer')
                {
                    $scope.developers = {};
                    Data.get('selectcontact/Developer').then(function (results) {
                        $scope.developers = results;
                    });
                }
                if ($scope.property.propfrom=='Broker')
                {
                    $scope.brokers = {};
                    Data.get('selectcontact/Broker').then(function (results) {
                        $scope.brokers = results;
                    });
                }
            }
        });
    };


});
 
app.controller('Properties_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, Data, $sce) {
    
    
    //var cat = $routeParams.cat;0000000000000000000000000000000000000000000000000
    //$scope.cat = cat;
    
    var cat="";
    var property_id = $routeParams.property_id;
    $scope.activePath = null;

    $scope.property = {};

    
    $scope.contact = {};
    $scope.enable_disable = true;

    $scope.modify = function()
    {
        
        $scope.enable_disable = false;
        $scope.disableAll = false;
    }

    

    $scope.create_new_property = function(property_id)
    {
        var createproperty = confirm('This will create new property from Property ID '+property_id);
        if (createproperty) {

            Data.get('create_new_property/'+property_id).then(function (results) { 
                Data.toast(results);
                newproperty_id = results.property_id;
                $location.path('properties_edit/'+newproperty_id);
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
    $timeout(function () { 
        Data.get('selecttask').then(function (results) {
            $scope.task_list = results;
        });
    }, 100);
    /*property.internal_comment = $("#internal_comment").val();
        property.external_comment = $("#external_comment").val();*/
    Data.get('properties_edit_ctrl/'+property_id).then(function (results) {
        $scope.cat = results[0].proptype;
        console.log($scope.cat);
        cat = results[0].proptype;
        $scope.create_rights = false;
        $scope.update_rights = false;
        $scope.delete_rights = false;
        $scope.view_rights = false;
        $scope.export_rights = false;    
        $str = ($("#permission_string").val());
        if ($scope.cat.toLowerCase()=='residential')
        {
            console.log("in");
            if ((($str).indexOf("properties_residential_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("properties_residential_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("properties_residential_update"))!=-1)
            {
                console.log("in_update");
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("properties_residential_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if ($scope.cat.toLowerCase()=='pre-leased')
        {

            if ((($str).indexOf("properties_preleased_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("properties_preleased_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("properties_preleased_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("properties_preleased_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if ($scope.cat.toLowerCase()=='retail')
        {

            if ((($str).indexOf("properties_retail_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("properties_retail_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("properties_retail_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("properties_retail_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if ($scope.cat.toLowerCase()=='commercial')
        {

            if ((($str).indexOf("properties_commercial_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("properties_commercial_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("properties_commercial_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("properties_commercial_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        if ($scope.cat.toLowerCase()=='others')
        {

            if ((($str).indexOf("properties_others_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("properties_others_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("properties_others_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("properties_others_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        }
        
        if (!$scope.update_rights)
        {
            //$rootScope.listproperties = {};
            alert("You don't have rights to use this option..");
            return;
        }

        if (results[0].amenities_avl)
        {
            $scope.arr = ((results[0].amenities_avl).split(','));
            results[0].amenities_avl = $scope.arr;
        }
        if (results[0].pro_specification)
        {
            $scope.arr = ((results[0].pro_specification).split(','));
            results[0].pro_specification = $scope.arr;
        }
        if (results[0].parking)
        {
            $scope.arr = ((results[0].parking).split(','));
            results[0].parking = $scope.arr;
        }
        if (results[0].assign_to)
        {
            $scope.arr = ((results[0].assign_to).split(','));
            results[0].assign_to = $scope.arr;
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
        if (results[0].suitable_for)
        {
            $scope.arr = ((results[0].suitable_for).split(','));
            results[0].suitable_for = $scope.arr;
        }
        $scope.property = {};
        $scope.$watch($scope.property, function() {
            $scope.property = {
                add1:results[0].add1,
                add2:results[0].add2,
		        ag_tenure:results[0].ag_tenure,
                ageofprop:results[0].ageofprop,
                agree_month:results[0].agree_month,
                amenities_avl:results[0].amenities_avl,
                area:results[0].area,
                area_id:results[0].area_id,
                area_name:results[0].area_name,
                area_para:results[0].area_para,
                asset_id:results[0].asset_id,
                assign_to:results[0].assign_to,
                availablefor:results[0].availablefor,
                balconies:results[0].balconies,
                bathrooms:results[0].bathrooms,
                bedrooms:results[0].bedrooms,
                broke_involved:results[0].broke_involved,
                building_name:results[0].building_name,
                building_plot:results[0].building_plot,
                cabins:results[0].cabins,
                cam_charges:results[0].cam_charges,
                campaign:results[0].campaign,
                car_area:results[0].car_area,
                car_park:results[0].car_park,
                carp_area:results[0].carp_area,
                carp_area2:results[0].carp_area2,
                carp_area_para:results[0].carp_area_para,
                carp_area_upside:results[0].carp_area_upside,
                cc:results[0].cc,
                city:results[0].city,
                city_id:results[0].city_id,
                completion_date:results[0].completion_date,
                con_status:results[0].con_status,
                conferences:results[0].conferences,
                config:results[0].config,
                country:results[0].country,
                created_by:results[0].created_by,
                created_date:results[0].created_date,
                cubicals:results[0].cubicals,
                deal_done:results[0].add1,
                dep_months:results[0].dep_months,
                deposite_month:results[0].deposite_month,
                description:results[0].description,
                dev_owner:results[0].dev_owner,
                dev_owner_name:results[0].dev_owner_name,
                dev_owner_id:results[0].dev_owner_id,
                distfrm_dairport:results[0].distfrm_dairport,
                distfrm_highway:results[0].distfrm_highway,
                distfrm_market:results[0].distfrm_market,
                distfrm_school:results[0].distfrm_school,
                distfrm_station:results[0].distfrm_station,
                door_fdir:results[0].door_fdir,
                dry_room:results[0].dry_room,
                efficiency:results[0].efficiency,
                email_saletrainee:results[0].email_saletrainee,
                escalation:results[0].escalation,
                escalation_lease:results[0].escalation_lease,
                exlocation:results[0].exlocation,
                exp_price:(results[0].exp_price),
                exp_price2:(results[0].exp_price2),
                exp_price2_para:results[0].exp_price2_para,
                exp_price_para:results[0].exp_price_para,
                exp_price_upside:results[0].exp_price_upside,
                exp_rent:results[0].exp_rent,
                exp_rent2:results[0].exp_rent2,
                exp_rent2_para:results[0].exp_rent2_para,
                exp_rent_para:results[0].exp_rent_para,
                exprice_para:results[0].exprice_para,
                external_comment:results[0].external_comment,
                floor:results[0].floor,
                frontage:results[0].frontage,
                fur_charges:results[0].fur_charges,
                furniture:results[0].furniture,
                groups:results[0].groups,
                garden_facing:results[0].garden_facing,
                height:results[0].height,
                internal_comment:results[0].internal_comment,
                internalroad:results[0].internalroad,
                keywith:results[0].keywith,
                kitchen:results[0].kitchen,
                landmark:results[0].landmark,
                latitude:results[0].latitude,
                lease_end:results[0].lease_end,
                lease_lock:results[0].lease_lock,
                lease_start:results[0].lease_start,
                lease_tot_area:results[0].lease_tot_area,
                lift:results[0].lift,
                loading:results[0].loading,
                locality:results[0].locality,
                locality_id:results[0].locality_id,
                lock_per:results[0].lock_per,
                longitude:results[0].longitude,
                notice_period:results[0].notice_period,
                stamp_duty:results[0].stamp_duty,
                main_charges:(results[0].main_charges),
                main_charges_para:results[0].main_charges_para,
                mainroad:results[0].mainroad,
                modified_by:results[0].modified_by,
                modified_date:results[0].modified_date,
                monthle_rent:results[0].monthle_rent,
                mpm_cam:results[0].mpm_cam,
                mpm_tot_tax:results[0].mpm_tot_tax,
                mpm_unit:results[0].mpm_unit,
                mpm_unit_para:results[0].mpm_unit_para,
                multi_size:results[0].multi_size,
                numof_floor:results[0].numof_floor,
                occ_details:results[0].occ_details,
                occu_certi:results[0].occu_certi,
                washrooms:results[0].washrooms,
                oth_charges:results[0].oth_charges,
                other_tenant:results[0].other_tenant,
                owner_email:results[0].owner_email,
                owner_mobile:results[0].owner_mobile,
                floor_rise:(results[0].floor_rise),
                pack_price:(results[0].pack_price),
                pack_price_comments:results[0].pack_price_comments,
                pack_price_para:results[0].pack_price_para,
                package_para:results[0].package_para,
                /*park_charge:results[0].park_charge,*/
                park_charge:(results[0].park_charge),
                park_charge_para:results[0].park_charge_para,
                parking:results[0].parking,
                pooja_room:results[0].pooja_room,
                possession_date:results[0].possession_date,
                powersup:results[0].powersup,
                pre_leased:results[0].pre_leased,
                pre_leased_rent:results[0].pre_leased_rent,
                price_carpet:results[0].price_carpet,
                price_unit:(results[0].price_unit),
                price_unit_carpet:(results[0].price_unit_carpet),
                price_unit_carpet_para:results[0].price_unit_carpet_para,
                price_unit_para:results[0].price_unit_para,
                priority:results[0].priority,
                pro_inspect:results[0].pro_inspect,
                pro_sale_para:results[0].pro_sale_para,
                pro_specification:results[0].pro_specification,
                proj_status:results[0].proj_status,
                project_id:results[0].project_id,
                project_name:results[0].project_name,
                prop_tax:results[0].prop_tax,
                property_code:results[0].property_code,
                property_for:results[0].property_for,
                property_id:results[0].property_id,
                propfrom:results[0].propfrom,
                propsubtype:results[0].propsubtype,
                proptype:results[0].proptype,
                psale_area:results[0].psale_area,
                published:results[0].published,
                reg_date:results[0].reg_date,
                rent_esc:results[0].rent_esc,
                rent_per_sqft:results[0].rent_per_sqft,
                rent_per_sqft_para:results[0].rent_per_sqft_para,
                rented_area:results[0].rented_area,
                rera_num:results[0].rera_num,
                review:results[0].review,
                road_no:results[0].road_no,
                roi:results[0].roi,
                rece:results[0].rece,
                sale_area:results[0].sale_area,
                sale_area2:results[0].sale_area2,
                sale_area_upside:results[0].sale_area_upside,
                seaters:results[0].seaters,
                sec_dep:results[0].sec_dep,
                security_depo:(results[0].security_depo),
                security_depo_para:results[0].security_depo_para,
                security_para:results[0].security_para,
                servent_room:results[0].servent_room,
                sms_saletrainee:results[0].sms_saletrainee,
                marketing_property:results[0].marketing_property,
                soc_reg:results[0].soc_reg,
                source_channel:results[0].source_channel,
                share_on_website:results[0].share_on_website,
                share_on_99:results[0].share_on_99,                
                acers_99_projid :results[0].acers_99_projid,
                featured:results[0].featured,
                state:results[0].state,
                state_id:results[0].state_id,
                store_room:results[0].store_room,
                study_room:results[0].study_room,
                subsource_channel:results[0].subsource_channel,
                suitable_for:results[0].suitable_for,
                suitablefor:results[0].suitablefor,
                teams:results[0].teams,
                sub_teams:results[0].sub_teams,
                tenant:results[0].tenant,
                tenant1:results[0].tenant1,
                tenant_name:results[0].tenant_name,
                tenure_year:results[0].tenure_year,
                terrace:results[0].terrace,
                terrace_para:results[0].terrace_para,
                tranf_charge:results[0].tranf_charge,
                unit:results[0].unit,
                usd_area:results[0].usd_area,
                vastu_comp:results[0].vastu_comp,
                watersup:results[0].watersup,
                wing:results[0].wing,
                wings:results[0].wings,
                workstation:results[0].workstation,
                meeting_room:results[0].meeting_room,
                server_room:results[0].server_room,
                youtube_link:results[0].youtube_link,
                task_id:results[0].task_id,
                zip:results[0].zip
            };
            activate_fileinput();
        }, true);
         
        

        //setTimeout(function(){
        $timeout(function () {
            if (results[0].multi_size=="1")
            {
                $('#multi_size').prop('checked', true);
            }

            if (results[0].exlocation=="1")
            {
                $('#exlocation').prop('checked', true);
            }

            if (results[0].vastu_comp=="1")
            {
                $('#vastu_comp').prop('checked', true);
            }

            if (results[0].tenant=="1")
            {
                $('#tenant').prop('checked', true);
            }
            if (results[0].sms_saletrainee=="1")
            {
                $('#sms_saletrainee').prop('checked', true);
            }

            if (results[0].marketing_propery=="1")
            {
                $('#marketing_propery').prop('checked', true);
            }

            if (results[0].email_saletrainee=="1")
            {
                $('#email_saletrainee').prop('checked', true);
            }
            
            if (results[0].area_id)
            {
                address = results[0].area_name;
            }
            else{
                address = results[0].locality;
            }
            Data.get('selectdropdownsNew/PROP_SUB_TYPE/'+results[0].proptype).then(function (results) { 
                $scope.propsubtypes = results; 
            });
            Data.get('change_suitablefor/'+results[0].propsubtype).then(function (results) { 
                $scope.suitable_for_lov = results; 
            });
            
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
                        suitable_for = results[0].propsubtype;
                        /*if (results[0].suitable_for)
                        {
                            suitable_for = $scope.property.area_name;
                        }*/
                        var request = {
                            location: results[0].geometry.location,
                            radius: '1500',
                            //type: ['restaurant']
                            type: suitable_for
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
                map.setOptions({
                    center: new google.maps.LatLng(40.685646, -76.195499),
                    zoom: 40
                });
            }
            
        },10000)

    });

    
    $scope.select_assign_to = function(teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/0').then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }

    

    $scope.getowner_names = function(dev_owner_name,cat)
    {
        if (cat=='Owner')
        {
            cat = 'Client';
        }
        if (dev_owner_name==' ' || dev_owner_name =='')
        {
            dev_owner_name = "blank";
        }
        $timeout(function () { 
            Data.get('getowner_names/'+dev_owner_name+'/'+cat).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_owner = $sce.trustAsHtml($scope.html);
                $(".mydropdown-menu").css("display","block");
            });
        }, 100);
    };

    $scope.getcustomername = function(contact_id,name)
    {
        $scope.property.dev_owner_id = contact_id;
        $scope.property.dev_owner_name = name;
        $(".mydropdown-menu").css("display","none");

    }

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

    $scope.property_image_update = function (attachment_id,field_name,value) 
    { 
        Data.get('property_image_update/'+attachment_id+'/'+field_name+'/'+value).then(function (results) {
        });
    };

    $scope.change_suitablefor = function (propsubtype) 
    { 
        Data.get('change_suitablefor/'+propsubtype).then(function (results) { 
            $scope.suitable_for_lov = results; 
        });
    }

    $scope.change_sub_source = function (source_channel) 
    { 
        Data.get('change_sub_source/'+source_channel).then(function (results) { 
            $scope.sub_sources = results; 
        });
    }

    Data.get('properties_images/'+property_id).then(function (results) {
        $scope.property_images = results;
    });

    Data.get('property_occu_cert/'+property_id).then(function (results) {
        $scope.property_occu_certs = results;
    });

    Data.get('properties_docs/'+property_id).then(function (results) {
        $scope.property_docs = results;
    });

    Data.get('properties_videos/'+property_id).then(function (results) {
        $scope.property_videos = results;
    });
     
    $timeout(function () { 
        Data.get('selectdropdowns/FURNITURE').then(function (results) {
            $scope.furnitures = results;
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
        Data.get('selectdropdowns/AMENITIES').then(function (results) {
            $scope.amenities = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/PRJ_SPECIFICATIONS').then(function (results) {
            $scope.pro_specifications = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/PARKING_DIR').then(function (results) {
            $scope.parkings = results;
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
        Data.get('selectdropdowns/DIRECTION').then(function (results) {
            $scope.door_fdirs = results;
        });
    }, 100);

    
    $scope.getclientemail = function(contact_off,contact_id)
    {
        Data.get('getclientemail/'+contact_off+"/"+contact_id).then(function (results) {
            $scope.client_mobile_number = results[0].client_mobile_number;
            $scope.client_email_id = results[0].client_email_id;
        });
    }
    $scope.calculate_exp_price = function(field_name)
    {
        if ($scope.property.exp_price==0)
        {
            console.log("inside"+$scope.property.exp_price);
            if ($scope.property.sale_area>0)
            {
                $scope.property.exp_price = (parseFloat($scope.property.sale_area) * parseFloat($scope.property.price_unit)).toFixed(2);
            }
            if ($scope.property.carp_area>0)
            {
                $scope.property.exp_price = (parseFloat($scope.property.carp_area) * parseFloat($scope.property.price_unit_carpet)).toFixed(2);
            }
            console.log("inside"+$scope.property.exp_price);
        }
    }

    $scope.calculate_loading = function(field_name)
    {

        // CALCULATE LOADING

        if ($scope.property.carp_area>0 && $scope.property.sale_area>0)
        {
            diff = $scope.property.sale_area - $scope.property.carp_area;
            $scope.property.loading = (diff * (100 / $scope.property.carp_area)).toFixed(2);
        }
        else if ($scope.property.sale_area>0)
        {
            diff = ((100+parseFloat($scope.property.loading))/100);
            $scope.property.carp_area = ($scope.property.sale_area / diff).toFixed(2);

        }
        if ($scope.property.exp_price>0)
        {
            
            if ($scope.property.sale_area>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                }
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                }
            }

            if ($scope.property.carp_area>0)
            {
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                }
            }
        }
        

/*
        if (field_name == "sale_area")
        {
            if ($scope.property.carp_area>0)
            {
                diff = $scope.property.sale_area - $scope.property.carp_area;
                $scope.property.loading = (diff * (100 / $scope.property.carp_area)).toFixed(2);
            }
            field_name = "exp_price";
        }
        if (field_name == "carp_area")
        {
            if ($scope.property.sale_area>0)
            {
                diff = $scope.property.sale_area - $scope.property.carp_area;
                $scope.property.loading = (diff * (100 / $scope.property.carp_area)).toFixed(2);
            }
            field_name = "exp_price";
        }
        if (field_name == "loading")
        {
            if ($scope.property.sale_area>0)
            {
                diff = ((100+parseFloat($scope.property.loading))/100);
                $scope.property.carp_area = ($scope.property.sale_area / diff).toFixed(2);

            }
            field_name = "exp_price";
        }

        if (field_name == "exp_price_para")
        {
            
            $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
            $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
            if ($scope.property.exp_price_para == "Th")
            {
               $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
               $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
              
            } 
            if ($scope.property.exp_price_para == "Lac")
            {
                $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
               
            } 
            if ($scope.property.exp_price_para == "Cr")
            {
               $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
               $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
               
            } 

        }
        if (field_name == "price_unit_para")
        {
          
            $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
            $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
            if ($scope.property.price_unit_para == "Th")
            {
               $scope.property.exp_price =(($scope.property.sale_area*1000)/$scope.property.price_unit).toFixed(2);
               $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
               
            } 
            if ($scope.property.price_unit_para == "Lac")
            {
               $scope.property.exp_price =(($scope.property.sale_area*100000)/$scope.property.price_unit).toFixed(2);
               $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2); 
               
            } 
            if ($scope.property.price_unit_para == "Cr")
            {
               $scope.property.exp_price =(($scope.property.sale_area*10000000)/$scope.property.price_unit).toFixed(2);
               $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
               
            } 

        }

        if (field_name == "price_unit_carpet_para")
        {
            
            $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
            $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
            if ($scope.property.price_unit_carpet_para == "Th")
            {
               $scope.property.exp_price =(($scope.property.carp_area*10000)/$scope.property.price_unit_carpet).toFixed(2);
               $scope.property.price_unit = (($scope.property.exp_price*10000)/$scope.property.sale_area).toFixed(2); 
               
            } 
            if ($scope.property.price_unit_carpet_para == "Lac")
            {
              $scope.property.exp_price =(($scope.property.carp_area*100000)/$scope.property.price_unit_carpet).toFixed(2);
               $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
               
            } 
            if ($scope.property.price_unit_carpet_para == "Cr")
            {
              $scope.property.exp_price =(($scope.property.carp_area*10000000)/$scope.property.price_unit_carpet).toFixed(2);
               $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2); 
               
            } 
        }

        if (field_name == "exp_price")
        {
            if ($scope.property.exp_price>0 && $scope.property.sale_area>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
             } 
        
            if ($scope.property.exp_price>0 && $scope.property.carp_area>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                   
                } 
            }
            
        }

        if (field_name == "price_unit")
        {
            if ($scope.property.sale_area>0 && $scope.property.price_unit>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
            }
            if ($scope.property.carp_area>0 && $scope.property.price_unit>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                } 

            }
            
        }

        if (field_name == "price_unit_carpet")
        {
            if ($scope.property.carp_area>0 && $scope.property.price_unit_carpet>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                } 

            }
            if ($scope.property.sale_area>0 && $scope.property.price_unit_carpet>0)
            {
                $scope.property.price_unit = (($scope.property.exp_price)/$scope.property.sale_area).toFixed(2);
                $scope.property.price_unit_carpet = (($scope.property.exp_price)/$scope.property.carp_area).toFixed(2);
                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*1000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*1000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*100000)/$scope.property.sale_area).toFixed(2); 
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*100000)/$scope.property.carp_area).toFixed(2);
                    
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.price_unit = (($scope.property.exp_price*10000000)/$scope.property.sale_area).toFixed(2);
                    $scope.property.price_unit_carpet = (($scope.property.exp_price*10000000)/$scope.property.carp_area).toFixed(2); 
                    
                } 
            }
            
           
        }*/
        if (field_name == "deposite_month")
        {
            if ($scope.property.deposite_month>0 && $scope.property.exp_price>0)
            {
                $scope.property.security_depo = ($scope.property.deposite_month*$scope.property.exp_price).toFixed(2); 

                if ($scope.property.exp_price_para == "Th")
                {
                    $scope.property.security_depo = (($scope.property.exp_price*1000)*$scope.property.deposite_month).toFixed(2); 
                } 
                if ($scope.property.exp_price_para == "Lac")
                {
                    $scope.property.security_depo = (($scope.property.exp_price*100000)*$scope.property.deposite_month).toFixed(2);
                } 
                if ($scope.property.exp_price_para == "Cr")
                {
                    $scope.property.security_depo = (($scope.property.exp_price*10000000)*$scope.property.deposite_month).toFixed(2); 
                    
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
                    $scope.property.area_id = results[0].area_id;
                    $scope.property.city = results[0].city;
                    $scope.property.state = results[0].state;
                    $scope.property.country = results[0].country;
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
                    $scope.property.city = results[0].city;
                    $scope.property.state = results[0].state;
                    $scope.property.country = results[0].country;
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
                    suitable_for = $scope.property.propsubtype;
                    if ($scope.property.suitable_for)
                    {
                        suitable_for = $scope.property.suitable_for;
                    }
                    var request = {
                        location: results[0].geometry.location,
                        radius: '1500',
                        //type: ['restaurant']
                        type: suitable_for
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
		    map.setOptions({
		        center: new google.maps.LatLng(40.685646, -76.195499),
		        zoom: 40
		    });
        }

    };

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

    $scope.properties_update = function (property) {
        property_id = property.property_id;
        property.internal_comment = $("#internal_comment").val(); 
        property.external_comment = $("#external_comment").val();
        property.file_name = $("#file_name").val();
        Data.post('properties_update', {
            property: property
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file-1').fileinput('upload');
                $('#file_occu').fileinput('upload');
                $('#file_docs').fileinput('upload');
                $('#file_videos').fileinput('upload');
                //$location.path('reports/property/property_id/'+property_id);
               $location.path('properties_list/'+cat+'/0/0');
            }
        });
    };
    
    $scope.properties_delete = function (property) {
        //console.log(business_unit);
        var deleteproperty = confirm('Are you absolutely sure you want to delete?');
        if (deleteproperty) {
            Data.post('properties_delete', {
                property: property
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('properties_list/'+cat+'/0/0');
                }
            });
        }
    };
    $scope.AddListValue = function (type)
    {
        $scope.temptype = type;
        $timeout(function () { 
            Data.get('selectparentlist').then(function (results) {
                $scope.parentlists = results;
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
                        if ($scope.temptype=='PROP_STATUS')
                        {
                            controlvalue = "prop_statuss";
                        }
                        if ($scope.temptype=='PROP_SUB_TYPE')
                        {
                            controlvalue = "propsubtypes";
                        }
                        if ($scope.temptype=='FURNITURE')
                        {
                            controlvalue = "furnitures";
                        }
                        if ($scope.temptype=='CLIENT_SOURCE')
                        {
                            controlvalue = "client_sources";
                        }
                        if ($scope.temptype=='SUB_SOURCE')
                        {
                            controlvalue = "sub_sources";
                        }
                        if ($scope.temptype=='DIRECTION')
                        {
                            controlvalue = "door_fdirs";
                        }
                        $scope[controlvalue] = {};
                        $scope.$watch($scope[controlvalue], function() {
                            $scope[controlvalue] = results;
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
        contact.contact_off = $scope.property.propfrom;
        contact.file_name = $("#file_name_company_logo").val();
        if ($scope.property.propfrom=='Owner')
        {
            contact.contact_off = "Client";
        }
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_company_logo').fileinput('upload');
                $('#file_visiting_card').fileinput('upload');
                $('#file_contact_pic').fileinput('upload');
                $("#adddeveloper").modal("hide");
                if ($scope.property.propfrom=='Owner')
                {
                    $scope.clients = {};
                    Data.get('selectcontact/Client').then(function (results) {
                        $scope.clients = results;
                    });
                }
                if ($scope.property.propfrom=='Developer')
                {
                    $scope.developers = {};
                    Data.get('selectcontact/Developer').then(function (results) {
                        $scope.developers = results;
                    });
                }
                if ($scope.property.propfrom=='Broker')
                {
                    $scope.brokers = {};
                    Data.get('selectcontact/Broker').then(function (results) {
                        $scope.brokers = results;
                    });
                }
            }
        });
    };
    $scope.removeimage = function (attachment_id) {
        var deleteproduct = confirm('Are you absolutely sure you want to delete?');
        if (deleteproduct) {
            Data.get('removeimage/'+attachment_id).then(function (results) {
                Data.toast(results);
                Data.get('properties_images/'+property_id).then(function (results) {
                    $scope.property_images = results;
                });
            });
        }
    };

    $scope.removeimage_docs = function (attachment_id) {
        var deleteproduct = confirm('Are you absolutely sure you want to delete?');
        if (deleteproduct) {
            Data.get('removeimage/'+attachment_id).then(function (results) {
                Data.toast(results);
                Data.get('properties_docs/'+property_id).then(function (results) {
                    $scope.property_docs = results;
                });
            });
        }
    };
    
});




app.controller('SelectProperty', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectproperty').then(function (results) {
            $rootScope.properties = results;
        });
    }, 100);
});
