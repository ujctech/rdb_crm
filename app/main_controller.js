function HomeCtrl($scope, $location, $http) { 
    $scope.username = "Sign in ";
    $scope.company_name = "SG Analytics"; 
    $scope.loggedIn = false; 
}
    
function ScrollCtrl($scope, $location, $anchorScroll)  
{
    $scope.displaytab = function (id, position) {   
        $('html,body').animate({ scrollTop: $('#' + id).offset().top - position }, 500); 
    };
} 
function Contact_usController($scope, $location, $anchorScroll)   
{
     
}

 
function numDifferentiation(val) {
    if(val >= 10000000) val = (val/10000000).toFixed(2) + ' Cr';
    else if(val >= 100000) val = (val/100000).toFixed(2) + ' Lac';
    else if(val >= 1000) val = (val/1000).toFixed(2) + ' Th';
    return val;
}

function numActual(val,inValue) {
    if (inValue=='Actual')
    {
        return val;
    }

    if (inValue=='Thousand')
    {
        return val*1000;
    }

    if (inValue=='Lac')
    {
        return val*100000;
    }
    if (inValue=='Crore')
    {
        return val*10000000;
    }

}

function numactual_new(val,inValue) {
    if (inValue=='Abs')
    {
        return val;
    }

    if (inValue=='Th')
    {
        return val*1000;
    }

    if (inValue=='Lac')
    {
        return val*100000;
    }
    if (inValue=='Cr')
    {
        return val*10000000;
    }

}

function ConvertAmount(amount,para) {
    if (amount > 0)
    {
        if (para=='Abs')
        {
            return amount;
        }

        if (para=='Th')
        {
            return (parseFloat((amount)/1000)).toFixed(2);
        }

        if (para=='Lac')
        {
            return (parseFloat((amount)/100000)).toFixed(2);
        }
        if (para=='Cr')
        {
            return (parseFloat((amount)/10000000)).toFixed(2);
        }
    }
    else{
        return amount;
    }
}


// MENUS

app.controller('MenuController', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {

    $timeout(function () { 
        Data.get('menus').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_menu = $sce.trustAsHtml($scope.html);
            $('#ribbon_menu').css("display","block");
            //$(".content").css("padding","0px");
        });
    }, 100);
});

// DASHBOARD

app.controller('DashboardController', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {

    $timeout(function () { 
        Data.get('dashboardcontroller').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_dashboard = $sce.trustAsHtml($scope.html);
        });
    }, 100);
    
});



// USERS

app.controller('User_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    $scope.path="user_list";
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("user_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("user_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("user_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("user_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.view_rights)
    {
        $rootScope.users = {};
        alert("You don't have rights to use this option..");
        return;
    }

    $timeout(function () { 
        Data.get('user_list_ctrl').then(function (results) {
        $rootScope.users = results;
        });
    }, 100);
});

app.controller('uploadCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    $scope.upload = function () {
        Data.post('upload', {            
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('user_list');
            }
        });
    };
});


app.controller('User_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    $scope.user_add_new = {username:'',password:'', role:'USER' };
    $scope.user_add_new = function (user) {
        Data.post('user_add_new', {
            user: user
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('user_list');
            }
        });
    };
});

app.controller('User_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout,$sce) {
    var user_id = $routeParams.user_id;
    $scope.activePath = null;

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("user_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("user_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("user_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("user_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.update_rights)
    {
        $rootScope.user = {};
        alert("You don't have rights to use this option..");
        return;
    }


    $timeout(function () { 
        Data.get('selectrole').then(function (results) {
            $rootScope.rolesdata = results;
        });
    }, 100);

    Data.get('user_edit_ctrl/'+user_id).then(function (results) {
        //$scope.arr = ((results[0].roles).split(','));
        //results[0].roles = $scope.arr;
        role_id = results[0].roles;
        $scope.user = {};
        $scope.$watch($scope.user, function() {
            $scope.user = {
                user_id:results[0].user_id,
                username:results[0].username,
                employee_name:results[0].employee_name,
                roles:results[0].roles
            }
        });
        //$rootScope.fusers = results;
        
        Data.get('getuserroles/'+user_id+'/'+role_id).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_roles = $sce.trustAsHtml($scope.html);
        });
    }, 100);

    $scope.getuserroles = function (roles) {
        console.log(roles);
        var changeRole = confirm('This will permanently change permissions for this user. Are you sure ?');
        if (changeRole) {
            Data.get('getuserroles/'+user_id+'/'+roles).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_roles = $sce.trustAsHtml($scope.html);
            });
        }
    };
    
    $scope.update_user_role_details = function(action,user_role_details_id,action_value)
    {
        console.log(user_role_details_id);
        if (!action_value)
        {
            action_value = "false";
        }
        Data.get('update_user_role_details/'+action+'/'+user_role_details_id+'/'+action_value).then(function (results) {
            
        });
    }

    $scope.user_update = function (user) {
        Data.post('user_update', {
            user: user
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('user_list');
            }
        });
    };
    
    $scope.user_delete = function (user) {
        //console.log(user);
        var deleteUser = confirm('Are you absolutely sure you want to delete?');
        if (deleteUser) {
            Data.post('user_delete', {
                user: user
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('user_list');
                }
            });
        }
    };
    
});

// Behaviour_List_Ctrl

app.controller('Behaviour_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout,$sce) {
    var user_id = $routeParams.user_id;
    $scope.activePath = null;

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("CRM_Behaviour_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("CRM_Behaviour_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("CRM_Behaviour_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("CRM_Behaviour_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.update_rights = true;
    if (!$scope.update_rights)
    {
        $rootScope.user = {};
        alert("You don't have rights to use this option..");
        return;
    }

    $scope.show_behaviour = function(for_month)
    {
        nfor_month = for_month.substr(6,4)+"-"+for_month.substr(3,2)+"-"+for_month.substr(0,2); 
        Data.get('show_behaviour/'+nfor_month).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_behaviour = $sce.trustAsHtml($scope.html);
        });
    }

    $scope.update_behaviour = function(action,behaviour_id,action_value)
    {
        console.log(behaviour_id);
        if (!action_value)
        {
            action_value = "false";
        }
        Data.get('update_behaviour/'+action+'/'+behaviour_id+'/'+action_value).then(function (results) {
            
        });
    }


});


// SOCIAL MEDIA

app.controller('Social_Media_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout,$sce) {
    var user_id = $routeParams.user_id;
    $scope.activePath = null;

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("social_media_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("social_media_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("social_media_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("social_media_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.view_rights = true;
    if (!$scope.view_rights)
    {
        $rootScope.user = {};
        alert("You don't have rights to use this option..");
        return;
    }

    $timeout(function () { 
        Data.get('social_media_list').then(function (results) {
            $scope.social_medias = results;
        });
    }, 100);

    $scope.show_social_media = function(for_month)
    {
        var nfor_month = for_month.substr(6,4)+"-"+for_month.substr(3,2)+"-"+for_month.substr(0,2); 
        Data.get('show_social_media/'+for_month).then(function (results) {
            $scope.social_medias = results;                
        });
    }

    $scope.update_social_media = function(action,social_media_id,action_value)
    {
        console.log(social_media_id);
        if (!action_value)
        {
            action_value = 0;
        }
        Data.get('update_social_media/'+action+'/'+social_media_id+'/'+action_value).then(function (results) {
            
        });
    }

    var values_loaded = "false";
    $scope.open_search = function()
    {
        if (values_loaded=="false")
        {
            values_loaded="true";
            console.log("opening");
            $timeout(function () { 
                Data.get('getdatavalues_social_media/employee_name').then(function (results) {
                    $scope.employee_names = results;
                });
            }, 100);
            

        }
    };

    $scope.search_social_media = function (searchdata) 
    {
        Data.post('search_social_media', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.social_medias, function() {
                $scope.social_medias = {};
                $scope.social_medias = results;
                
            },true);
        });
    };

    $scope.resetForm = function()
    {
        
        $scope.searchdata = {};
        $scope.$watch($scope.searchdata, function() {
            $scope.searchdata = {
            }
        });
        $("li.select2-selection__choice").remove();
        $(".select2").each(function() { $(this).val([]); });
        
        Data.get('social_media_list').then(function (results) {
            $scope.social_medias = results;
        });
    }



});

app.controller('Social_Media_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, Data, $sce) {
    
    $scope.activePath = null;

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;
    $scope.social_media = {};
    $str = ($("#permission_string").val());
    if ((($str).indexOf("social_media_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("social_media_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("social_media_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("social_media_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.create_rights = true;
    if (!$scope.create_rights)
    {
        $scope.social_media = {};
        alert("You don't have rights to use this option..");
        return;
    }

    $scope.social_media_add = function (social_media) {        
        Data.post('social_media_add', {
            social_media: social_media
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_documents').fileinput('upload');
                $location.path('social_media_list');
            }
        });
    }; 
    
    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.users = results;
        });
    }, 100);

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

    $scope.select_assign_to = function(teams,sub_teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/'+sub_teams).then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }


});

app.controller('Social_Media_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, Data, $sce) {
    var social_media_id = $routeParams.social_media_id;
    $scope.activePath = null;

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;
    $scope.social_media = {};
    $str = ($("#permission_string").val());
    if ((($str).indexOf("social_media_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("social_media_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("social_media_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("social_media_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.update_rights = true;
    if (!$scope.update_rights)
    {
        $scope.social_media = {};
        alert("You don't have rights to use this option..");
        return;
    }

    Data.get('social_media_edit_ctrl/'+social_media_id).then(function (results) {
        $scope.social_media = {};
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
        if (results[0].assign_to)
        {
            $scope.arr = ((results[0].assign_to).split(','));
            results[0].assign_to = $scope.arr;
        }
        $scope.$watch($scope.social_media, function() {
            $scope.social_media = {
                social_media_id:results[0].social_media_id,
                data_date:results[0].data_date,
                media_category:results[0].media_category,
                no_of_posts:results[0].no_of_posts,
                no_of_likes:results[0].no_of_likes,
                no_of_followers:results[0].no_of_followers,
                no_of_subscribers:results[0].no_of_subscribers,
                no_of_views:results[0].no_of_views,
                lead_generation:results[0].lead_generation,
                teams:results[0].teams,
                sub_teams:results[0].sub_teams,
                assign_to:results[0].assign_to

            }
            Data.get('social_media_documents/'+social_media_id).then(function (results) {
                $scope.social_media_documents = results;
            });
        });
    });

    $scope.removeimage = function (attachment_id) {
        var deleteemployee = confirm('Are you absolutely sure you want to delete?');
        if (deleteemployee) {
            Data.get('removeimage/'+attachment_id).then(function (results) {
                Data.toast(results);
                Data.get('social_media_documents/'+agreement_id).then(function (results) {
                    $scope.social_media_documents = results;
                });
            });
        }
    };
    $scope.social_media_image_update = function (attachment_id,field_name,value) 
    { 
        Data.get('social_media_image_update/'+attachment_id+'/'+field_name+'/'+value).then(function (results) {
        });
    };

    $scope.social_media_update = function (social_media) {        
        Data.post('social_media_update', {
            social_media: social_media
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_documents').fileinput('upload');
                $location.path('social_media_list');
            }
        });
    };  

    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.users = results;
        });
    }, 100);

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

    $scope.select_assign_to = function(teams,sub_teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/'+sub_teams).then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }

});


app.controller('Social_Media_List_Ctrl_old', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout,$sce) {
    var user_id = $routeParams.user_id;
    $scope.activePath = null;

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("Social_Media_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("Social_Media_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("Social_Media_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("Social_Media_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.update_rights = true;
    if (!$scope.update_rights)
    {
        $rootScope.user = {};
        alert("You don't have rights to use this option..");
        return;
    }

    $scope.show_social_media = function(for_month)
    {
        nfor_month = for_month.substr(6,4)+"-"+for_month.substr(3,2)+"-"+for_month.substr(0,2); 
        Data.get('show_social_media/'+nfor_month).then(function (results) {
            $scope.social_medias = results;                
        });
    }

    $scope.update_social_media = function(action,social_media_id,action_value)
    {
        console.log(social_media_id);
        if (!action_value)
        {
            action_value = 0;
        }
        Data.get('update_social_media/'+action+'/'+social_media_id+'/'+action_value).then(function (results) {
            
        });
    }


});

app.controller('Social_Media_Edit_Ctrl_old', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, Data, $sce) {
    var social_media_id = $routeParams.social_media_id;
    $scope.activePath = null;

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;
    $scope.social_media = {};
    $str = ($("#permission_string").val());
    if ((($str).indexOf("Social_Media_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("Social_Media_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("Social_Media_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("Social_Media_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.update_rights = true;
    if (!$scope.update_rights)
    {
        $scope.social_media = {};
        alert("You don't have rights to use this option..");
        return;
    }

    Data.get('social_media_edit_ctrl/'+social_media_id).then(function (results) {
        $scope.social_media = {};
        $scope.$watch($scope.social_media, function() {
            $scope.social_media = {
                social_media_id:results[0].social_media_id,
                name:results[0].name,
                instagram_likes:results[0].instagram_likes,
                instagram_followers:results[0].instagram_followers,
                facebook_likes:results[0].facebook_likes,
                facebook_followers:results[0].facebook_followers,
                old_youtube_subscribers:results[0].old_youtube_subscribers,
                new_youtube_subscribers:results[0].new_youtube_subscribers,
                old_linkedin_followers:results[0].old_linkedin_followers,
                new_linkedin_followers:results[0].new_linkedin_followers,
                old_twitter_followers:results[0].old_twitter_followers,
                new_twitter_followers:results[0].new_twitter_followers,
                lead_generation:results[0].lead_generation
            }
            Data.get('social_media_documents/'+social_media_id).then(function (results) {
                $scope.social_media_documents = results;
            });
        });
    });

    $scope.removeimage = function (attachment_id) {
        var deleteemployee = confirm('Are you absolutely sure you want to delete?');
        if (deleteemployee) {
            Data.get('removeimage/'+attachment_id).then(function (results) {
                Data.toast(results);
                Data.get('social_media_documents/'+agreement_id).then(function (results) {
                    $scope.social_media_documents = results;
                });
            });
        }
    };
    $scope.social_media_image_update = function (attachment_id,field_name,value) 
    { 
        Data.get('social_media_image_update/'+attachment_id+'/'+field_name+'/'+value).then(function (results) {
        });
    };

    $scope.social_media_update = function (social_media) {        
        Data.post('social_media_update', {
            social_media: social_media
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_documents').fileinput('upload');
                $location.path('social_media_list');
            }
        });
    };    
});

// 99Acres

app.controller('99Acres_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout,$sce) {
    var user_id = $routeParams.user_id;
    $scope.activePath = null;

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("99Acres_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("99Acres_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("99Acres_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("99Acres_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.view_rights = true;
    if (!$scope.view_rights)
    {
        $rootScope.user = {};
        alert("You don't have rights to use this option..");
        return;
    }


    Data.get('show_99acres').then(function (results) {
        $scope.html = results[0].htmlstring;
        $scope.trustedHtml_99acres = $sce.trustAsHtml($scope.html);
    });
    

    $scope.show_99acres = function(for_month)
    {
        nfor_month = for_month.substr(6,4)+"-"+for_month.substr(3,2)+"-"+for_month.substr(0,2); 
        Data.get('show_99acres/'+nfor_month).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_99acres = $sce.trustAsHtml($scope.html);
        });
    }

    $scope.update_behaviour = function(action,behaviour_id,action_value)
    {
        console.log(behaviour_id);
        if (!action_value)
        {
            action_value = "false";
        }
        Data.get('update_behaviour/'+action+'/'+behaviour_id+'/'+action_value).then(function (results) {
            
        });
    }


});



// Website lead
app.controller('Weblead_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout,$sce) {
 
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("weblead_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("weblead_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("weblead_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("weblead_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.view_rights = true;
    if (!$scope.view_rights)
    {
        $scope.weblead = {};
        alert("You don't have rights to use this option..");
        return;
    }

    $timeout(function () { 
        Data.get('weblead_list_ctrl').then(function (results) {
        $rootScope.users = results;
        });
    }, 100);

});



// MAIL TEMPLATE

app.controller('Mail_Template_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('mail_template_list_ctrl').then(function (results) {
            $rootScope.mail_templates = results;
        });
    }, 100);
});
    
    
app.controller('Mail_Template_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    
    $scope.mail_template_add_new = {mail_template:''};
    $scope.mail_template_add_new = function (mail_template) {
        mail_template.text_message = $("#text_message").val();
        mail_template.footer_note = $("#footer_note").val();
        Data.post('mail_template_add_new', {
            mail_template: mail_template
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('mail_template_list');
            }
        });
    };
});
    
app.controller('Mail_Template_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var mail_template_id = $routeParams.mail_template_id;
    $scope.activePath = null;
    $scope.mail_template={};
    Data.get('mail_template_edit_ctrl/'+mail_template_id).then(function (results) {
        $scope.mail_template={};
        $scope.$watch($scope.mail_template, function() {
            $scope.mail_template = {
                module_name:results[0].module_name,
                template_title:results[0].template_title, 
                subject:results[0].subject,
                text_message:results[0].text_message,
                footer_note:results[0].footer_note,
                sequence_number:results[0].sequence_number,
                mail_template_id:results[0].mail_template_id
            }
        });
        $('.textarea').wysihtml5(
			{
				"html":true
				
			}
		);
    });
    
    
    $scope.mail_template_update = function (mail_template) {
        mail_template.text_message = $("#text_message").val();
        mail_template.footer_note = $("#footer_note").val();
        Data.post('mail_template_update', {
            mail_template: mail_template
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('mail_template_list');
            }
        });
    };
    
    $scope.mail_template_delete = function (mail_template) {
        //console.log(business_unit);
        var deletetmail_template = confirm('Are you absolutely sure you want to delete?');
        if (deletetmail_template) {
            Data.post('mail_template_delete', {
                mail_template: mail_template
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('mail_template_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectMail_Template', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectmail_template').then(function (results) {
            $rootScope.mail_templates = results;
        });
    }, 100);
});


// RESUMES

app.controller('Resume_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('resume_list_ctrl').then(function (results) {
            $scope.resumes = results;
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
                Data.get('getdatavalues_resumes/employee_name').then(function (results) {
                    $scope.employee_names = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_resumes/designation_id').then(function (results) {
                    $scope.designations = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_resumes/mobile_no').then(function (results) {
                    $scope.mobile_nos = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_resumes/email').then(function (results) {
                    $scope.emails = results;
                });
            }, 100);

        }
    };

    $scope.search_resumes = function (searchdata) 
    {
        Data.post('search_resumes', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.resumes, function() {
                $scope.resumes = {};
                $scope.resumes = results;
                
            },true);
        });
    };

    $scope.resetForm = function()
    {
        
        $scope.searchdata = {};
        $scope.$watch($scope.searchdata, function() {
            $scope.searchdata = {
            }
        });
        $("li.select2-selection__choice").remove();
        $(".select2").each(function() { $(this).val([]); });
        
        Data.get('resume_list_ctrl').then(function (results) {
            $scope.resumes = results;
        });
    }


});
    
    
app.controller('Resume_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    
    $scope.resume_add_new = {resume:''};
    $scope.resume_add_new = function (resume) {
        resume.file_name = $("#filename").val();
        Data.post('resume_add_new', {
            resume: resume
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file-1').fileinput('upload');
                $location.path('resume_list');
            }
        });
    };
});
    
app.controller('Resume_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var resume_id = $routeParams.resume_id;
    $scope.activePath = null;
    $scope.resume={};
    Data.get('resume_edit_ctrl/'+resume_id).then(function (results) {
        $scope.resume={};
        $scope.$watch($scope.resume, function() {
            $scope.resume = {
                employee_name:results[0].employee_name,
                designation_id:results[0].designation_id, 
                mobile_no:results[0].mobile_no, 
                email:results[0].email,
                remarks:results[0].remarks,
                status:results[0].status,
                filename:results[0].filename,
                resume_id:results[0].resume_id
            }
        });
    });
    
    
    $scope.resume_update = function (resume) {
        if ($("#filename").val()!="")
        {
            resume.filename = $("#filename").val();
        }
        Data.post('resume_update', {
            resume: resume
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file-1').fileinput('upload');
                $location.path('resume_list');
            }
        });
    };
    
    $scope.resume_delete = function (resume) {
        //console.log(business_unit);
        var deleteresume = confirm('Are you absolutely sure you want to delete?');
        if (deleteresume) {
            Data.post('resume_delete', {
                resume: resume
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('resume_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectResume', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectresume').then(function (results) {
            $rootScope.resumes = results;
        });
    }, 100);
});

// SMS TEMPLATE

app.controller('SMS_Template_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('sms_template_list_ctrl').then(function (results) {
            $rootScope.sms_templates = results;
        });
    }, 100);
});
    
    
app.controller('SMS_Template_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    
    $scope.sms_template_add_new = {sms_template:''};
    $scope.sms_template_add_new = function (sms_template) {
        sms_template.text_message = $("#text_message").val();
        sms_template.footer_note = $("#footer_note").val();
        Data.post('sms_template_add_new', {
            sms_template: sms_template
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('sms_template_list');
            }
        });
    };
});
    
app.controller('SMS_Template_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var sms_template_id = $routeParams.sms_template_id;
    $scope.activePath = null;
    
    Data.get('sms_template_edit_ctrl/'+sms_template_id).then(function (results) {
        $rootScope.sms_templates = results;
    });
    
    
    $scope.sms_template_update = function (sms_template) {
        sms_template.text_message = $("#text_message").val();
        sms_template.footer_note = $("#footer_note").val();
        Data.post('sms_template_update', {
            sms_template: sms_template
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('sms_template_list');
            }
        });
    };
    
    $scope.sms_template_delete = function (sms_template) {
        //console.log(business_unit);
        var deletetsms_template = confirm('Are you absolutely sure you want to delete?');
        if (deletetsms_template) {
            Data.post('sms_template_delete', {
                sms_template: sms_template
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('sms_template_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectSMS_Template', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectsms_template').then(function (results) {
            $rootScope.sms_templates = results;
        });
    }, 100);
});

app.controller('SMS_Sent_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('sms_sent_list_ctrl').then(function (results) {
            $scope.sms_sents = results;
        });
    }, 100);
});

app.controller('Email_Sent_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
    $scope.regular_list = "Yes";
    $scope.email_sents = {};
    $scope.pagenavigation = function(which_side)
    {
        $scope.inboxdata = {};
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

            $timeout(function () { 
                Data.get('email_sent_list_ctrl/'+$scope.next_page_id).then(function (results) {
                    $scope.email_sents = results;
                    $scope.page_range = parseInt($scope.next_page_id)+1+" - ";
                    $scope.next_page_id = parseInt($scope.next_page_id)+30;
                    $scope.page_range = $scope.page_range + $scope.next_page_id;
                });
            }, 100);

        }
        else
        {
            
            //$scope.search_contacts($scope.searchdata,'pagenavigation');
            
        }
    }

    
    $timeout(function () { 
        Data.get('email_sent_list_ctrl/'+$scope.next_page_id).then(function (results) {
            $scope.email_sents = results;
            $scope.next_page_id = 30;
            $scope.mail_count = results[0].mail_count;
            $scope.total_records = results[0].mail_count;
            console.log(results[0].mail_count);
        });
    }, 100);

    $scope.close_message_box = function ()
	{
        $("#view_message").modal("hide");
	}
    

    $scope.view_message = function(mail_id,text_message)
    {
        $('#view_message_data').html(text_message);
        $('#view_message').modal("show");
    }
});

// COUNTRY

app.controller('Country_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('country_list_ctrl').then(function (results) {
        $rootScope.countries = results;
        });
    }, 100);
});


app.controller('Country_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    $scope.country_add_new = {country:''};
    $scope.country_add_new = function (country) {
        Data.post('country_add_new', {
            country: country
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('country_list');
            }
        });
    };
});

app.controller('Country_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var country_id = $routeParams.country_id;
    $scope.activePath = null;
    
    Data.get('country_edit_ctrl/'+country_id).then(function (results) {
        $rootScope.countries = results;
    });
    
    
    $scope.country_update = function (country) {
        Data.post('country_update', {
            country: country
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('country_list');
            }
        });
    };
    
    $scope.country_delete = function (country) {
        //console.log(country);
        var deleteCountry = confirm('Are you absolutely sure you want to delete?');
        if (deleteCountry) {
            Data.post('country_delete', {
                country: country
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('country_list');
                }
            });
        }
    };
    
});


app.controller('SelectCountry', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectedcountry').then(function (results) {
        $rootScope.countries = results;
        });
    }, 100);
    
});

// BRANCH OFFICE

app.controller('Branch_Office_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('branch_office_list_ctrl').then(function (results) {
            $rootScope.branch_offices = results;
        });
    }, 100);
});


app.controller('Branch_Office_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    $scope.branch_office = {};

    $scope.showlocality = function (field_name,value) 
    {  

        if (field_name=='locality_id')
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+value).then(function (results) {
                    $scope.branch_office.area_id = results[0].area_id;
                    $scope.branch_office.city = results[0].city;
                    $scope.branch_office.state = results[0].state;
                    $scope.branch_office.country = results[0].country;
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
                    $scope.branch_office.city = results[0].city;
                    $scope.branch_office.state = results[0].state;
                    $scope.branch_office.country = results[0].country;
                });
            }, 100);
        }
    }

    $scope.branch_office_add_new = {bo_name:''};
    $scope.branch_office_add_new = function (branch_office) {
        Data.post('branch_office_add_new', {
            branch_office: branch_office
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('branch_office_list');
            }
        });
    };
});

app.controller('Branch_Office_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var bo_id = $routeParams.bo_id;
    $scope.activePath = null;
    $scope.branch_office = {};

    
    Data.get('branch_office_edit_ctrl/'+bo_id).then(function (results) {
        //$rootScope.fbranch_offices = results;
        $scope.$watch($scope.project, function() {
            $scope.branch_office={};
            $scope.branch_office = {
                bo_name:results[0].bo_name,
                office_description:results[0].office_description,
                office_lead_id:results[0].office_lead_id,
                address1:results[0].address1,
                address2:results[0].address2,
                locality_id:results[0].locality_id,
                area_id:results[0].area_id,
                bo_id:results[0].bo_id
            };
        },true);

    });
    
    $scope.showlocality = function (field_name,value) 
    {  

        if (field_name=='locality_id')
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+value).then(function (results) {
                    $scope.branch_office.area_id = results[0].area_id;
                    $scope.branch_office.city = results[0].city;
                    $scope.branch_office.state = results[0].state;
                    $scope.branch_office.country = results[0].country;
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
                    $scope.branch_office.city = results[0].city;
                    $scope.branch_office.state = results[0].state;
                    $scope.branch_office.country = results[0].country;
                });
            }, 100);
        }
    }
    
    $scope.branch_office_update = function (branch_office) {
        Data.post('branch_office_update', {
            branch_office: branch_office
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('branch_office_list');
            }
        });
    };
    
    $scope.branch_office_delete = function (branch_office) {
        //console.log(business_unit);
        var deletebranch_office = confirm('Are you absolutely sure you want to delete?');
        if (deletebranch_office) {
            Data.post('branch_office_delete', {
                branch_office: branch_office
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('branch_office_list');
                }
            });
        }
    };
});

app.controller('SelectBranch_Office', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectbranch_office').then(function (results) {
        $rootScope.branch_offices = results;
        });
    }, 100);
});

// DESIGNATION

app.controller('Designation_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('designation_list_ctrl').then(function (results) {
            $rootScope.designations = results;
        });
    }, 100);
});


app.controller('Designation_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    $scope.designation_add_new = {designation:''};
    $scope.designation_add_new = function (designation) {
        Data.post('designation_add_new', {
            designation: designation
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('designation_list');
            }
        });
    };
});

app.controller('Designation_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var designation_id = $routeParams.designation_id;
    $scope.activePath = null;
    
    Data.get('designation_edit_ctrl/'+designation_id).then(function (results) {
        $rootScope.designations = results;
    });
    
    
    $scope.designation_update = function (designation) {
        Data.post('designation_update', {
            designation: designation
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('designation_list');
            }
        });
    };
    
    $scope.designation_delete = function (designation) {
        //console.log(business_unit);
        var deletedesignation = confirm('Are you absolutely sure you want to delete?');
        if (deletedesignation) {
            Data.post('designation_delete', {
                designation: designation
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('designation_list');
                }
            });
        }
    };
    
});

app.controller('SelectDesignation', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectdesignation').then(function (results) {
            $rootScope.designations = results;
        });
    }, 100);
});


// AREA

app.controller('Area_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('area_list_ctrl').then(function (results) {
            $rootScope.areas = results;
        });
    }, 100);
});
    
    
app.controller('Area_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    $scope.area_add_new = {area:''};
    $scope.area_add_new = function (area) {
        Data.post('area_add_new', {
            area: area
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('area_list');
            }
        });
    };
});
    
app.controller('Area_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var area_id = $routeParams.area_id;
    $scope.activePath = null;
    
    Data.get('area_edit_ctrl/'+area_id).then(function (results) {
        $rootScope.areas = results;
    });
    
    
    $scope.area_update = function (area) {
        Data.post('area_update', {
            area: area
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('area_list');
            }
        });
    };
    
    $scope.area_delete = function (area) {
        //console.log(business_unit);
        var deletearea = confirm('Are you absolutely sure you want to delete?');
        if (deletearea) {
            Data.post('area_delete', {
                area: area
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('area_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectArea', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $scope.showcity = function (field_name,value) 
    {  

        if (field_name=='area_id')
        {
            $timeout(function () { 
                Data.get('getfromarea/'+value).then(function (results) {
                    $scope.locality.city = results[0].city;
                    $scope.locality.state = results[0].state;
                    $scope.locality.country = results[0].country;
                });
            }, 100);
        }

};
    $timeout(function () { 
        Data.get('selectarea').then(function (results) {
            $rootScope.areas = results;
        });
    }, 100);
});

// LOCALITY

app.controller('Locality_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('locality_list_ctrl').then(function (results) {
            $rootScope.localities = results;
        });
    }, 100);
});
    
    
app.controller('Locality_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    $scope.locality_add_new = {locality:''};
    $scope.locality_add_new = function (locality) {
        Data.post('locality_add_new', {
            locality: locality
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('locality_list');
            }
        });
    };
});
    
app.controller('Locality_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var locality_id = $routeParams.locality_id;
    $scope.activePath = null;
    
    Data.get('locality_edit_ctrl/'+locality_id).then(function (results) {
        $rootScope.localities = results;
    });
    
    
    $scope.locality_update = function (locality) {
        Data.post('locality_update', {
            locality: locality
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('locality_list');
            }
        });
    };
    
    $scope.locality_delete = function (locality) {
        //console.log(business_unit);
        var deletelocality = confirm('Are you absolutely sure you want to delete?');
        if (deletelocality) {
            Data.post('locality_delete', {
                locality: locality
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('locality_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectLocality', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectlocality').then(function (results) {
            $rootScope.localities = results;
        });
    }, 100);
});

// GROUPS

app.controller('Group_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('group_list_ctrl').then(function (results) {
            $rootScope.groups = results;
        });
    }, 100);
});
    
    
app.controller('Group_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    
    $scope.group_add_new = function (groupdata) {
        Data.post('group_add_new', {
            groupdata: groupdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('group_list');
            }
        });
    };
});
    
app.controller('Group_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var group_id = $routeParams.group_id;
    $scope.activePath = null;
    
    Data.get('group_edit_ctrl/'+group_id).then(function (results) {
        /*$scope.arr = ((results[0].teams).split(','));
        results[0].teams = $scope.arr;*/
        $scope.fgroups = results;
    });
    
    $scope.group_update = function (groupdata) {
        Data.post('group_update', {
            groupdata: groupdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('group_list');
            }
        });
    };
    
    $scope.group_delete = function (groupdata) {
        //console.log(business_unit);
        var deletegroup = confirm('Are you absolutely sure you want to delete?');
        if (deletegroup) {
            Data.post('group_delete', {
                groupdata: groupdata
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('group_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectGroup', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectgroup').then(function (results) {
            $rootScope.groups = results;
        });
    }, 100);
});

// ROLES

app.controller('Role_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$sce) {
    
    $timeout(function () { 
        Data.get('selectrole').then(function (results) {
            $rootScope.rolesRecords = results;
        });
    }, 100);

    $scope.getroles = function(role_id)
    {
        Data.get('getroles/'+role_id).then(function (results) {
            //$rootScope.permissions = results;
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_roles = $sce.trustAsHtml($scope.html);
        });
    }
    $scope.update_role_details = function(action,permission_id,action_value)
    {
        console.log(action_value);
        if (!action_value)
        {
            action_value = "false";
        }
        Data.get('update_role_details/'+action+'/'+permission_id+'/'+action_value).then(function (results) {
            
        });
    }
});
    
    
app.controller('Role_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    $scope.role_add_new = {zrole:''};
    $scope.role_add_new = function (zrole) {
        Data.post('role_add_new', {
            zrole: zrole
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('role_list');
            }
        });
    };
});
    
app.controller('Role_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var role_id = $routeParams.role_id;
    $scope.activePath = null;
    $scope.zrole= {};
    Data.get('role_edit_ctrl/'+role_id).then(function (results) {
        $scope.arr = ((results[0].permissions).split(','));
        results[0].permissions = $scope.arr;
        $scope.zrole = {};
        $scope.zrole = {
            role:results[0].role,
            permissions:results[0].permissions,
            role_id:results[0].role_id
        };
        
    });
    
    
    $scope.role_update = function (zrole) {
        Data.post('role_update', {
            zrole: zrole
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('role_list');
            }
        });
    };
    
    $scope.role_delete = function (zrole) {
        //console.log(business_unit);
        var deleterole = confirm('Are you absolutely sure you want to delete?');
        if (deleterole) {
            Data.post('role_delete', {
                zrole: zrole
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('role_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectRole', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectrole').then(function (results) {
            $rootScope.rolesdata = results;
        });
    }, 100);
});

// PERMISSION

app.controller('Permission_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('permission_list_ctrl').then(function (results) {
            $rootScope.permissions = results;
        });
    }, 100);
});  
    
app.controller('Permission_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $window) {
    
    $scope.permission_add_new = {permisson:''};
    $scope.permission_add_new = function (permission) {
        Data.post('permission_add_new', {
            permission: permission
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                //$(".modal").modal("hide");
                //$window.history.back();
                $location.path('permission_list');
            }
        });
    };
});
    
app.controller('Permission_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var permission_id = $routeParams.permission_id;
    $scope.activePath = null;
    
    Data.get('permission_edit_ctrl/'+permission_id).then(function (results) {
        $rootScope.fpermissions = results;
    });
    
    
    $scope.permission_update = function (permission) {
        Data.post('permission_update', {
            permission: permission
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('permission_list');
            }
        });
    };
    
    $scope.permission_delete = function (permission) {
        //console.log(business_unit);
        var deletepermission = confirm('Are you absolutely sure you want to delete?');
        if (deletepermission) {
            Data.post('permission_delete', {
                permission: permission
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('permission_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectPermission', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectpermission').then(function (results) {
            $rootScope.permissions = results;
        });
    }, 100);
});



// STATE

app.controller('State_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('state_list_ctrl').then(function (results) {
        $rootScope.states = results;
        });
    }, 100);
});

app.controller('State_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    $scope.state_add_new = {state:'',country:' ',country_id:0};
    $scope.state_add_new = function (state) {
        Data.post('state_add_new', {
            state: state
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('state_list');
            }
        });
    };
});

app.controller('State_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var state_id = $routeParams.state_id;
    $scope.activePath = null;
    
    Data.get('state_edit_ctrl/'+state_id).then(function (results) {
        $rootScope.states = results;
    });
    
    $scope.state_update = function (state) {
        Data.post('state_update', {
            state: state
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('state_list');
            }
        });
    };
    
    $scope.state_delete = function (state) {
        //console.log(state);
        var deleteState = confirm('Are you absolutely sure you want to delete?');
        if (deleteState) {
            Data.post('state_delete', {
                state: state
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('state_list');
                }
            });
        }
    };
    
});

app.controller('SelectState', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $scope.showcountry = function (field_name,value) 
    {  

        if (field_name=='state_id')
        {
            $timeout(function () { 
                Data.get('getfromstate/'+value).then(function (results) {
                    $scope.city.country = results[0].country;
                });
            }, 100);
        }
};
    $timeout(function () { 
        Data.get('selectedstate').then(function (results) {
        $rootScope.states = results;
        });
    }, 100);
});


// CITY

app.controller('City_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
   
    $timeout(function () { 
        Data.get('city_list_ctrl').then(function (results) {
        $rootScope.cities = results;
        });
    }, 100);
});


app.controller('City_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    $scope.city_add_new = {city:'',state:' ',state_id:0};
    $scope.city_add_new = function (city) {
        Data.post('city_add_new', {
            city: city
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('city_list');
            }
        });
    };
});

app.controller('City_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var city_id = $routeParams.city_id;
    $scope.activePath = null;
    
    Data.get('city_edit_ctrl/'+city_id).then(function (results) {
        $rootScope.cities = results;
    });
    
    $scope.city_update = function (city) {
        Data.post('city_update', {
            city: city
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('city_list');
            }
        });
    };
    
    $scope.city_delete = function (city) {
        //console.log(city);
        var deleteCity = confirm('Are you absolutely sure you want to delete?');
        if (deleteCity) {
            Data.post('city_delete', {
                city: city
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('city_list');
                }
            });
        }
    };
    
});



app.controller('LogoChange', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    $scope.logochange = function()
    {
        if($("body").hasClass("sidebar-collapse"))
        {
            $("#logobig").attr("src","dist/img/logo.jpg");
            $("#logobig").height(52);
            alert("a");

        }
        else{
            $("#logobig").attr("src","dist/img/logobig.png");
            $("#logobig").height(77);
            alert("b");
        }

    }
});


app.controller('SelectCity', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $scope.showstate = function (field_name,value) 
        {  
            if (field_name=='city_id')
            {
                $timeout(function () { 
                    Data.get('getfromcity/'+value).then(function (results) {
                        $scope.area.state = results[0].state;
                        $scope.area.country = results[0].country;
                    });
                }, 100);
            }
    };
    $timeout(function () { 
        Data.get('selectcity').then(function (results) {
        $rootScope.cities = results;
        });
    }, 100);
});

app.controller('MISEnquiries_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {

    $scope.misreport={};
    var currentdate = new Date();
    var beforedate = new Date (new Date().setDate(currentdate.getDate()-30)); 
    dd =  currentdate.getDate();
    if (dd<10)
    {
        dd = "0"+dd;
    }
    mm =  (currentdate.getMonth()+1);
    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var datetime = dd + "/" + mm + "/" + yy;

    dd =  beforedate.getDate();
    if (dd<10)
    {
        dd = "0"+dd;
    }
    mm =  (beforedate.getMonth()+1);
    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = beforedate.getFullYear();
    var beforedatetime = dd+ "/" + mm + "/" +  yy;

    $scope.$watch($scope.misreport.start_date, function() {
        $scope.misreport.start_date = beforedatetime;
    }, true);

    $scope.$watch($scope.misreport.end_date, function() {
        $scope.misreport.end_date = datetime;
    }, true);

    $scope.showmisreport = function (misreport) {
        Data.post('showmisreport', {
            misreport: misreport
        }).then(function (results) {
            $rootScope.select_enquiries = results;
        });
    };


});




// AUDIT TRAIL

app.controller('Audit_Trail', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var module_name = $routeParams.module_name;
    var id = $routeParams.id;
    var data = $routeParams.data;

    $scope.module_name = module_name;
    $scope.activePath = null;
    
    Data.get('audit_trail/'+module_name+'/'+id+'/'+data).then(function (results) {
        $rootScope.histories = results;
    });
});

// REPORTS

app.controller('Reports', function ($scope, $rootScope, $routeParams, $location, $http, Data, $sce) {
    var module_name = $routeParams.module_name;
    var id = $routeParams.id;
    var data = $routeParams.data;

    $scope.module_name = module_name;
    $scope.activePath = null;
    
    Data.get('reports/'+module_name+'/'+id+'/'+data).then(function (results) {
        $scope.trustedHtml_pages = $sce.trustAsHtml(results[0].pages);
        /*$scope.fpage_1 = results[0].page_1;
        $scope.fpage_2 = results[0].page_2;
        $scope.fpage_3 = results[0].page_3;
        $scope.fpage_4 = results[0].page_4;
        $scope.fpage_5 = results[0].page_5;
        $scope.fpage_6 = results[0].page_6;
        /*$("#fpage_1").wysihtml5();
        $("#fpage_2").wysihtml5();
        $("#fpage_3").wysihtml5();
        $("#fpage_4").wysihtml5();
        $("#fpage_5").wysihtml5();
        $("#fpage_6").wysihtml5();
        $scope.trustedHtml_page_1 = $sce.trustAsHtml($scope.fpage_1);
        $scope.trustedHtml_page_2 = $sce.trustAsHtml($scope.fpage_2);
        $scope.trustedHtml_page_3 = $sce.trustAsHtml($scope.fpage_3);
        $scope.trustedHtml_page_4 = $sce.trustAsHtml($scope.fpage_4);
        $scope.trustedHtml_page_5 = $sce.trustAsHtml($scope.fpage_5);
        $scope.trustedHtml_page_6 = $sce.trustAsHtml($scope.fpage_6);*/

        //$scope.html = results[0].htmlstring;
        //$scope.trustedHtml_show_report = $sce.trustAsHtml($scope.html);
    });


    $scope.export_report = function(report_type)
    {
        Data.get('export_report/'+module_name+'/'+id+'/'+data+'/'+report_type).then(function (results) {
            //window.open("api//v1//uploads//reports//"+module_name+"//_list."+report_type,"_blank");
            window.location.href = "api//v1//uploads//reports//"+module_name+"_list."+report_type;
            //window.open("api//v1//uploads//reports//"+module_name+"_list_"+id+"."+report_type);

        });
    }
});


app.controller('OneMailer1', function ($scope, $rootScope, $routeParams, $location, $http, Data, $sce) {
    var module_name = $routeParams.module_name;
    var id = $routeParams.id;
    var data = $routeParams.data;

    $scope.module_name = module_name;
    $scope.activePath = null;
    
    Data.get('onemailer/'+module_name+'/'+id+'/'+data).then(function (results) {

        $scope.fpage_1 = results[0].page_1;
        $scope.trustedHtml_page_1 = $sce.trustAsHtml($scope.fpage_1);
    });


    $scope.export_report = function(report_type)
    {
        Data.get('export_onemailer/'+module_name+'/'+id+'/'+data+'/'+report_type).then(function (results) {
            window.location.href = "api//v1//uploads//reports//"+module_name+"_list."+report_type;
        });
    }
});



// MAILS CLIENT

app.controller('SendBroucher', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce) 
{

    var module_name = $routeParams.module_name;
    var id = $routeParams.id;
    var data = $routeParams.data;
    $scope.back_to = "user";
    $heading = "Properties ShortListed for you - SQFT";
    if (module_name=='project')
    {
        $heading = "Project for you - SQFT";
    }
    $scope.mail_sent = {};
    $scope.mail_sent = {subject: $heading,
                        text_message:'',
			            cc_mail_id : ''
                       };
            

    $timeout(function () { 
        Data.get('select_broucher/'+module_name+'/'+id+'/'+data).then(function (results) {
            //$scope.mail_sent.subject = results[0].subject;
            //$('.wysihtml5-sandbox, .wysihtml5-toolbar').remove();
            $scope.mail_sent.text_message = results[0].text_message;//+" "+results[0].footer_note;
            CKEDITOR.replace( 'text_message',
            { height:1200 }) ;
            
            
            
            $('#text_message').css("display","block");
            
        });
    }, 100);

    $timeout(function () { 
        Data.get('getsentitems/'+module_name+'/'+id+'/0').then(function (results) {
            //$scope.html = results[0].htmlstring;
            //$scope.trustedHtml_sentitems = $sce.trustAsHtml($scope.html);
            $scope.inboxdata = results;
        });
    }, 100);

    $("#mails_client_inner").css("display","block");

    
    
    $scope.send_broucher = function(mail_sent) 
    { 
        var mailconfirm= confirm('Are you sure ! You want send mail?');
        if (mailconfirm) 
        {
        
        }
        else
        {
            return;        
        }
        var currentdate = new Date(); 
        var currday = (currentdate.getDate());
        var currmonth = (currentdate.getMonth()+1);
        if (currmonth<10)
        {
            currmonth = "0"+currmonth;
        }
        if (currday<10)
        {
            currday = "0"+currday;
        }
        
        var datetime = currentdate.getFullYear()+ "-" + (currmonth) + "-" +  (currday)+ " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
        var in_time = currentdate.getHours()+":"+currentdate.getMinutes();
        mail_sent.created_date = datetime;
	    mail_sent.category = module_name;

	    mail_sent.category_id = id;
        mail_sent.mail_date = currentdate.getFullYear()+ "-" + (currmonth) + "-" +  (currday);
        console.log(mail_sent);
        Data.post('send_broucher', {
            mail_sent: mail_sent
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
				
			}
        });
    }
    
    $scope.showclientmail = function(mail_id)
    {
        $timeout(function () { 
            Data.get('showclientmail/'+mail_id).then(function (results) {
                $scope.mail_sent = {};
                $scope.mail_sent = {client_id:results[0].client_id,
                                    subject:results[0].subject,
                                    text_message:results[0].text_message,
                                    to_mail_id:results[0].to_mail_id,
                                    cc_mail_id:results[0].cc_mail_id,
                                    bcc_mail_id:results[0].bcc_mail_id,
                                    attachments:results[0].attachments
                                   };
            });
        }, 100);
    }
    
    
    $scope.showmailids = function (client_id,mail_type) {
		if (!client_id)
		{
			alert("Please Select Client First !!");
			return;
		}
        $("#showtomailids .mydropdown-menu").css("display","none");
        $("#showccmailids .mydropdown-menu").css("display","none"); 
        $("#showbccmailids .mydropdown-menu").css("display","none");
        $("#showattachments .mydropdown-menu").css("display","none");
        $("#showpdffile .mydropdown-menu").css("display","none");
		$timeout(function () { 
        	Data.get('showmailids/'+client_id+'/'+mail_type).then(function (results) {
				$scope.html = results[0].htmlstring;
                if (mail_type == 'to')
                {
                    $scope.trustedHtml_tomailids = $sce.trustAsHtml($scope.html);
				    $("#showtomailids .mydropdown-menu").css("display","block");        
                }
                if (mail_type == 'cc')
                {
                    $scope.trustedHtml_ccmailids = $sce.trustAsHtml($scope.html);
				    $("#showccmailids .mydropdown-menu").css("display","block");        
                }
                if (mail_type == 'bcc')
                {
                    $scope.trustedHtml_bccmailids = $sce.trustAsHtml($scope.html);
				    $("#showbccmailids .mydropdown-menu").css("display","block");        
                }
          	});
     	}, 100);
    };
    
    $scope.showattachments = function (client_id) {
		if (!client_id)
		{
			alert("Please Select Client First !!");
			return;
		}
        $("#showtomailids .mydropdown-menu").css("display","none");
        $("#showccmailids .mydropdown-menu").css("display","none"); 
        $("#showbccmailids .mydropdown-menu").css("display","none"); 
        $("#showpdffile .mydropdown-menu").css("display","none");
		$timeout(function () { 
        	Data.get('showattachments/'+client_id).then(function (results) {
				$scope.html = results[0].htmlstring;
                $scope.trustedHtml_attachments = $sce.trustAsHtml($scope.html);
				$("#showattachments .mydropdown-menu").css("display","block");        
          	});
     	}, 100);
    };
    
    $scope.showpdffile = function (client_id) {
		if (!client_id)
		{
			alert("Please Select Client First !!");
			return;
		}
        $("#showtomailids .mydropdown-menu").css("display","none");
        $("#showccmailids .mydropdown-menu").css("display","none"); 
        $("#showbccmailids .mydropdown-menu").css("display","none"); 
        $("#showpdffile .mydropdown-menu").css("display","none");
		$timeout(function () { 
        	Data.get('showpdffile/'+client_id).then(function (results) {
				$scope.html = results[0].htmlstring;
                $scope.trustedHtml_pdffile = $sce.trustAsHtml($scope.html);
				$("#showpdffile .mydropdown-menu").css("display","block");        
          	});
     	}, 100);
    };
    
    $scope.showpdfdata = function (invoice_filename) {
		$("#showtomailids .mydropdown-menu").css("display","none");
        $("#showccmailids .mydropdown-menu").css("display","none"); 
        $("#showbccmailids .mydropdown-menu").css("display","none");
        $("#showattachments .mydropdown-menu").css("display","none");
        $("#showpdffile .mydropdown-menu").css("display","none");
        $scope.html = '<embed src="//sgaeasy//application//api//v1//uploads//'+invoice_filename +'" style="width:875px;height:1800px;" type="application/pdf">';
        $scope.trustedHtml_show_pdf = $sce.trustAsHtml($scope.html);
        $("#show_pdf").modal("show");
    };
    
    $scope.close_pdf = function ()
	{
        $("#show_pdf").modal("hide");
	}
    
    $scope.getemailid = function (email_id,mail_type) {
        if (mail_type == 'to')
        {
            if ($scope.mail_sent.to_mail_id)
            {
                $scope.mail_sent.to_mail_id = $scope.mail_sent.to_mail_id + email_id +";";
            }
		    else
            {
                $scope.mail_sent.to_mail_id = email_id+';';
            }
		    $("#showtomailids .mydropdown-menu").css("display","none");
        }
        if (mail_type == 'cc')
        {
            if ($scope.mail_sent.cc_mail_id)
            {
                $scope.mail_sent.cc_mail_id = $scope.mail_sent.cc_mail_id +";"+ email_id;
            }
		    else
            {
                $scope.mail_sent.cc_mail_id = email_id+';';
            }
		    $("#showccmailids .mydropdown-menu").css("display","none");
        }
        if (mail_type == 'bcc')
        {
            if ($scope.mail_sent.bcc_mail_id)
            {
                $scope.mail_sent.bcc_mail_id = $scope.mail_sent.bcc_mail_id +";"+ email_id;
            }
		    else
            {
                $scope.mail_sent.bcc_mail_id = email_id+';';
            }
		    $("#showbccmailids .mydropdown-menu").css("display","none");
        }
    };
    
    $scope.getattachment = function (invoice_filename) {
        if ($scope.mail_sent.attachments)
        {
            $scope.mail_sent.attachments = $scope.mail_sent.attachments + invoice_filename +";";
        }
		else
        {
            $scope.mail_sent.attachments = invoice_filename+';';
        }
		$("#showattachments .mydropdown-menu").css("display","none");
    };
    
    $scope.addnewemail = function (client_id) {
		if (!client_id)
		{
			alert("Please Select Client First !!");
			return;
		}
        $("#showtomailids .mydropdown-menu").css("display","none");
        $("#showccmailids .mydropdown-menu").css("display","none"); 
        $("#showbccmailids .mydropdown-menu").css("display","none");
        $("#showattachments .mydropdown-menu").css("display","none");
        $("#addnewemailid").css("display","block");        
    };
    
    $scope.newemail = function (client_id) {
		if (!client_id)
		{
			alert("Please Select Client First !!");
			return;
		}
        $("#addnewemailid").css("display","none");
        newemailid = $("#newemailid").val();
        if (newemailid)
        {
            Data.get('newemail/'+client_id+'/'+newemailid).then(function (results) {
            
            });
        }
    };
    
});

app.controller('CreateReports', function ($scope, $rootScope, $routeParams, $location, $http, Data, $sce,$timeout) {
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
        $scope.property_images_slide = results;
    });
    $scope.slides.category = "";
    $scope.slides.category_id = 0;
    $scope.arrTabRows = {};
    var url;
    Data.get('createreports/'+module_name+'/'+id+'/'+data).then(function (results) {
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
            $("#image_1").html('<img class="page_links_img" src="api/v1/uploads/property/'+image_name+'" style="padding:10px;"/>');
            $scope.slides.image_1_id=attachment_id;
            $scope.slides.image_1_name=image_name;
            $scope.slides.description_1 = description;
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            return;
        }
        else{
            if ($scope.slides.image_2_id==0)
            {
                $("#image_2").html('<img class="page_links_img" src="api/v1/uploads/property/'+image_name+'" style="padding:10px;"/>');
                $scope.slides.image_2_id=attachment_id;
                $scope.slides.image_2_name=image_name;
                $scope.slides.description_2 = description;
                $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;

                return;
            }
            else{
                if ($scope.slides.image_3_id==0)
                {
                    $("#image_3").html('<img class="page_links_img" src="api/v1/uploads/property/'+image_name+'" style="padding:10px;"/>');
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
                        $("#image_4").html('<img class="page_links_img" src="api/v1/uploads/property/'+image_name+'" style="padding:10px;"/>');
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
                path: 'api/v1/uploads/property/'+image_name,
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
        //loadImage("https://crm.rdbrothers.com/api/v1/uploads/property/{{edited_image}}");
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
                $("#image_"+slide_no+"_"+image_number).attr("src","api/v1/uploads/property/"+results.image_name)
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
            Data.post('save_map_image', {
                image_data: $scope.image_data
            }).then(function (results) {
                Data.toast(results);
                $("#image_98_1").attr("src","api/v1/uploads/property/"+results.image_name);
                $scope.map_image_name = results.image_name;
                //$("#image_"+slide_no+"_"+image_number).attr("src","api/v1/uploads/property/"+results.image_name)
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
                        slide.addImage({ path: "api/v1/uploads/property/"+value.image_1, x:0.25, y:0.5, w:'96%', h:'83%' });
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
                        slide.addImage({ path: "api/v1/uploads/property/"+value.image_1, x:0.25, y:0.5, w:'96%', h:'83%' });
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
                            slide.addImage({ path: "api/v1/uploads/property/"+value.image_1, x:0.25, y:0.5, w:'45%', h:'38%' });
                        }
                        if (value.image_2)
                        {
                            slide.addImage({ path: "api/v1/uploads/property/"+value.image_2, x:5.0, y:0.5, w:'45%', h:'38%' });
                        }
                        if (value.image_3)
                        {
                            slide.addImage({ path: "api/v1/uploads/property/"+value.image_3, x:0.25, y:3.0, w:'45%', h:'38%' });
                        }
                        if (value.image_4)
                        {
                            slide.addImage({ path: "api/v1/uploads/property/"+value.image_4, x:5.0, y:3.0, w:'45%', h:'38%' });
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
                        slide.addImage({ path: "api/v1/uploads/property/"+value.image_1, x:0.25, y:0.5, w:'95%', h:'84%' });
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
                    slide2.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'96%', h:'83%' });
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

                    slide4.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'45%', h:'38%' });
                    slide4.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:5.0, y:0.5, w:'45%', h:'38%' });
                    slide4.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:0.25, y:3.0, w:'45%', h:'38%' });
                    slide4.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:5.0, y:3.0, w:'45%', h:'38%' });


                    slide4.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide4.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                    slide4.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});

                    var slide5 = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                    slide5.addNotes('Image Page ');
                    slide5.addText('Image Page', { placeholder:'title',x:0.3,fontSize:20 });
                    slide5.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                    slide5.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    
                    slide5.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'45%', h:'38%' });
                    slide5.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:5.0, y:0.5, w:'45%', h:'38%' });
                    slide5.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:0.25, y:3.0, w:'45%', h:'38%' });
                    slide5.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:5.0, y:3.0, w:'45%', h:'38%' });

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
                            //$("#image_98_1").attr("src","api/v1/uploads/property/"+results.image_name);
                            //$scope.map_image_name = results.image_name;
                            //$("#image_"+slide_no+"_"+image_number).attr("src","api/v1/uploads/property/"+results.image_name)
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




app.controller('MultiProperty', function ($scope, $rootScope, $routeParams, $location, $http, Data, $sce,$timeout) {
    var module_name = $routeParams.module_name;
    var id = $routeParams.id;
    var data = $routeParams.data;

    $scope.module_name = module_name;
    $scope.activePath = null;
    $scope.slides = {};
    Data.get('properties_images/'+data).then(function (results) {
        $rootScope.property_images_slide = results;
    });
    $scope.slides.category = "";
    $scope.slides.category_id = 0;
    $scope.arrTabRows = {};
    var url;
    Data.get('MultiProperty/'+module_name+'/'+id+'/'+data).then(function (results) {
        $scope.slide_data = results;
        $scope.arrTabRows  = results;
        $("#slide_view_1").css("display","block");
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
        $scope.slides.attachment_id = results[0].attachment_id;
        $scope.slides.main_image = results[0].filenames;
        $scope.slides.description = results[0].description;

        $scope.slides.area_id = results[0].area_id;
        $scope.slides.area_name = results[0].area_name;
        $scope.slides.locality_id = results[0].locality_id;
        $scope.slides.locality = results[0].locality;
        $scope.slides.propsubtype = results[0].propsubtype;
        $scope.slides.suitable_for = results[0].suitable_for;



        console.log("area"+results[0].area_id);
        console.log("area_name"+results[0].area_name);
        console.log("locality_id"+results[0].locality_id);
        console.log("locality"+results[0].locality);
        console.log("propsubtype"+results[0].propsubtype);
        console.log("suitable_for"+results[0].suitable_for);

        /*if (results[0].area_id)
        {
            address = results[0].area_name;
        }
        else{
            address = results[0].locality;
        }*/
        var address="Andheri West, Mumbai";
        /*if (results[0].locality)
        {
            address = results[0].locality;
        }
        else
        {
            address = results[0].area_name;
        }*/

        if ($scope.slides.locality_id>0)
        {
            $timeout(function () { 
                Data.get('getfromlocality/'+$scope.slides.locality_id).then(function (results) {
                    address = results[0].locality+results[0].city+results[0].state+results[0].country;
                });
            }, 100);
            
        }

        if ($scope.slides.area_id>0)
        {
            $timeout(function () { 
                Data.get('getfromarea/'+$scope.slides.area_id).then(function (results) {
                    address = results[0].area_name+results[0].city+results[0].state+results[0].country;
                });
            }, 100);
        }

        suitable_for = results[0].propsubtype;
        if (results[0].suitable_for)
        {
            suitable_for = results[0].suitable_for;
        }
        
        console.log('address:'+address);
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
                        console.log(results[0].formatted_address);
                        var myLatLng = {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()};

                        //map.setCenter(results[0].geometry.location);
                        //map.setZoom(12);
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
                        console.log("suitable_for"+suitable_for);
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
    });    

    /*$scope.main_image = "no_image.jpg";
    Data.get('getpropertiesbyid1/'+data).then(function (results) {
        $rootScope.properties_slide = results;
        $scope.property = {};
        $scope.$watch($scope.property, function() {
            $scope.property = {
                area_name:results[0].area_name,
                
                owner_name:results[0].owner_name,
                mob_no:results[0].mob_no,
                email:results[0].email,

                locality:results[0].locality,
                building_name:results[0].building_name,
                carp_area:results[0].carp_area,
                sale_area:results[0].sale_area,
                floor:results[0].floor,
                price_unit:results[0].price_unit,
                car_park:results[0].car_park,
                lock_per:results[0].lock_per,
                lease_end:results[0].lease_end,
                location:results[0].wing+','+results[0].unit+','+results[0].floor+','+results[0].road_no+','+results[0].building_name+','+results[0].landmark+','+results[0].locality+','+results[0].area_name+','+results[0].city,
                escalation_lease:results[0].escalation_lease,
                furnishing:results[0].rece+' Reception,'+results[0].workstation+' Workstation,'+results[0].cabins+' Cabins,'+results[0].cubicals+' Cubicals,'+results[0].conferences+' Conferences,'+results[0].kitchen+' Kitchen,'+results[0].washrooms+' washrooms',
                attachment_id:results[0].attachment_id,
                main_image:results[0].filenames,
                description:results[0].description,
                area_id:results[0].area_id
            }
        });
        if (results[0].area_id)
        {
            address = results[0].area_name;
        }
        else{
            address = results[0].locality;
        }
        suitable_for = results[0].propsubtype;
        if (results[0].suitable_for)
        {
            suitable_for = $scope.property.area_name;
        }
        console.log('address:'+address);
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
                        console.log(results[0].formatted_address);
                        var myLatLng = {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()};
                        
                        /*var marker = new google.maps.Marker({
                            position: myLatLng,
                            map: map,
                            draggable: true,
                            title: address
                        });*/
                        /*marker.setPosition(results[0].geometry.location);
                        updateMarkerAddress(results[0].formatted_address);
                        if (results[0].geometry.viewport) 
                        {
                            map.fitBounds(results[0].geometry.viewport);
                        } else 
                        {
                            map.fitBounds(results[0].geometry.bounds);
                        }
                        
                        var request = {
                            location: results[0].geometry.location,
                            radius: '1500',
                            //type: ['restaurant']
                            type: suitable_for
                          };
                        
                          service = new google.maps.places.PlacesService(map);
                          service.nearbySearch(request, $scope.callback);

                          html2canvas(document.getElementById('map'), {
                            useCORS: true,
                            optimized: false,
                            allowTaint: false,
                            onrendered: function (canvas) {
                                url = canvas.toDataURL("image/png");
                                $('#map_image').attr('src',url).show();
                                var a = document.createElement('a');
                                a.href = url;
                                a.download = 'myfile.png';
                                a.click();
                                console.log("url"+url);
                            }
                        });
                          
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
    });*/
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
            $("#image_1").html('<img class="page_links_img" src="api/v1/uploads/property/'+image_name+'" style="padding:10px;"/>');
            $scope.slides.image_1_id=attachment_id;
            $scope.slides.image_1_name=image_name;
            $scope.slides.description_1 = description;
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            return;
        }
        else{
            if ($scope.slides.image_2_id==0)
            {
                $("#image_2").html('<img class="page_links_img" src="api/v1/uploads/property/'+image_name+'" style="padding:10px;"/>');
                $scope.slides.image_2_id=attachment_id;
                $scope.slides.image_2_name=image_name;
                $scope.slides.description_2 = description;
                $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;

                return;
            }
            else{
                if ($scope.slides.image_3_id==0)
                {
                    $("#image_3").html('<img class="page_links_img" src="api/v1/uploads/property/'+image_name+'" style="padding:10px;"/>');
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
                        $("#image_4").html('<img class="page_links_img" src="api/v1/uploads/property/'+image_name+'" style="padding:10px;"/>');
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
        }if (id==3)
        {
            $("#image_3").html('');
            $scope.slides.image_3_id=0;
            $scope.slides.image_3_name="";
            $scope.slides.description_3 = "";
            $scope.slides.description = $scope.slides.description_1 +"  "+$scope.slides.description_2+"  "+$scope.slides.description_3 + " "+$scope.slides.description_4;
            $("#slide_view_1").css("display","block");
            return;
        }if (id==4)
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
    /*Data.get('createreports/'+module_name+'/'+id+'/'+data).then(function (results) {
        $scope.trustedHtml_pages = $sce.trustAsHtml(results[0].pages);
        $scope.html = results[0].htmlstring;
        $scope.trustedHtml_show_report = $sce.trustAsHtml($scope.html);
        $("#top_slide_view_1").css("display","block");
    });*/
    
    $scope.capture_image = function()
    {        
        html2canvas(document.getElementById('map'), {
            useCORS: true,
            optimized: false,
            allowTaint: false,
            onrendered: function (canvas) {
                url = canvas.toDataURL("image/png");
                /*$('#map_image').attr('src',url).show();
                var a = document.createElement('a');
                a.href = url;
                a.download = 'myfile.png';
                a.click();*/
                //console.log("url"+url);
                //return url;

            }
        });
    }

    $scope.export_report = function(report_type)
    {
        $scope.slide_data = {};
        var url = $("#map_image").prop('src');

        var pptx = new PptxGenJS();

        pptx.addSection({ title: 'Masters' });
        var slide1 = pptx.addSlide({masterName:'TITLE_SLIDE', sectionTitle:'Masters'});
        slide1.addImage({ path: "dist/img/ppt_main.jpg", x:0.0, y:0.0, w:'100%', h:'100%' });
        slide1.addNotes('Welcome Page');
        Data.get('getslidedata').then(function (results) {
            $scope.slide_data = results;
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
            
            angular.forEach(results,function(value,key){
                console.log(value);
                if (value.slide_no==2)
                {
                    var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                    slide.addNotes('Main Image');
                    slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                    slide.addImage({ path: "api/v1/uploads/property/"+value.image_1, x:0.25, y:0.5, w:'96%', h:'83%' });
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
                            { text: 'Quoted Rate', options: { valign:'top', align:'left'  } },
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
                if (value.slide_no>3 && value.slide_no<98)
                {
                    var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                    slide.addNotes('Images');
                    slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                    slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                    slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    if (value.image_1)
                    {
                        slide.addImage({ path: "api/v1/uploads/property/"+value.image_1, x:0.25, y:0.5, w:'45%', h:'38%' });
                    }
                    if (value.image_2)
                    {
                        slide.addImage({ path: "api/v1/uploads/property/"+value.image_2, x:5.0, y:0.5, w:'45%', h:'38%' });
                    }
                    if (value.image_3)
                    {
                        slide.addImage({ path: "api/v1/uploads/property/"+value.image_3, x:0.25, y:3.0, w:'45%', h:'38%' });
                    }
                    if (value.image_4)
                    {
                        slide.addImage({ path: "api/v1/uploads/property/"+value.image_4, x:5.0, y:3.0, w:'45%', h:'38%' });
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
                    slide.addImage({ path: url, x:0.25, y:0.5, w:'45%', h:'38%' });
                    slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                    slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                    slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                }
                if (value.slide_no==99)
                {
                    var slide = pptx.addSlide({masterName:'THANKS_SLIDE', sectionTitle:'Masters'});
                    slide.addImage({ path: "dist/img/thanks.jpg",  x:0.0, y:0.0, w:'100%', h:'100%'  });
                    slide.addNotes('Thanks Page');
                    slide.addText($scope.slides.owner_name, { placeholder:'title',x:3.2,y:4,fontSize:24 });
                    slide.addText('Call:'+$scope.slides.mob_no, { placeholder:'title',x:3.6,y:4.4,fontSize:14 });
                    slide.addText('Email:'+$scope.slides.email, { placeholder:'title',x:3.5,y:4.7,fontSize:14 });
                }

                    /*var slide2 = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                slide2.addNotes('Main Image');
                slide2.addText('Main Building', { placeholder:'title',x:0.3,fontSize:20 });
                slide2.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'96%', h:'83%' });
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

                slide4.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'45%', h:'38%' });
                slide4.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:5.0, y:0.5, w:'45%', h:'38%' });
                slide4.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:0.25, y:3.0, w:'45%', h:'38%' });
                slide4.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:5.0, y:3.0, w:'45%', h:'38%' });


                slide4.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                slide4.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                slide4.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});

                var slide5 = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                slide5.addNotes('Image Page ');
                slide5.addText('Image Page', { placeholder:'title',x:0.3,fontSize:20 });
                slide5.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                slide5.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                
                slide5.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:0.25, y:0.5, w:'45%', h:'38%' });
                slide5.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:5.0, y:0.5, w:'45%', h:'38%' });
                slide5.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:0.25, y:3.0, w:'45%', h:'38%' });
                slide5.addImage({ path: "api/v1/uploads/property/p_271_1606296257_8.jpg", x:5.0, y:3.0, w:'45%', h:'38%' });

                slide5.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                slide5.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                slide5.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});

                var slide6 = pptx.addSlide({masterName:'THANKS_SLIDE', sectionTitle:'Masters'});
                slide6.addImage({ path: "dist/img/thanks.jpg",  x:0.0, y:0.0, w:'100%', h:'100%'  });
                slide6.addNotes('Thanks Page');*/
            
            });
            pptx.writeFile('properties_report')
            .then(function(fileName){ console.log('Saved! File Name: '+fileName) });

    });
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

app.controller('PreLeased', function ($scope, $rootScope, $routeParams, $location, $http, Data, $sce,$timeout) {
    var module_name = $routeParams.module_name;
    var id = $routeParams.id;
    var data = $routeParams.data;

    $scope.module_name = module_name;
    $scope.activePath = null;
    $timeout(function () { 
        Data.get('preleased/property/property_id/'+data).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.preleaseHtml = $sce.trustAsHtml($scope.html);
            //window.location.href = "api//v1//uploads//reports//export.xlsx";//+module_name+"_list."+report_type;
        });
    }, 100);

   
    $scope.export_report = function(report_type)
    {
        console.log(report_type);
        if (report_type=="pptx")
        {
            $scope.slide_data = {};
            var url = $("#map_image").prop('src');

            var pptx = new PptxGenJS();

            pptx.addSection({ title: 'Masters' });
            var slide1 = pptx.addSlide({masterName:'TITLE_SLIDE', sectionTitle:'Masters'});
            slide1.addImage({ path: "dist/img/ppt_main.jpg", x:0.0, y:0.0, w:'100%', h:'100%' });
            slide1.addNotes('Welcome Page');
            Data.get('getslidedata').then(function (results) {
                $scope.slide_data = results;
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
                
                angular.forEach(results,function(value,key){
                    console.log(value);
                    if (value.slide_no==2)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Main Image');
                        slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addImage({ path: "api/v1/uploads/property/"+value.image_1, x:0.25, y:0.5, w:'96%', h:'83%' });
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
                                { text: 'Quoted Rate', options: { valign:'top', align:'left'  } },
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
                    if (value.slide_no>3 && value.slide_no<98)
                    {
                        var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                        slide.addNotes('Images');
                        slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                        slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        if (value.image_1)
                        {
                            slide.addImage({ path: "api/v1/uploads/property/"+value.image_1, x:0.25, y:0.5, w:'45%', h:'38%' });
                        }
                        if (value.image_2)
                        {
                            slide.addImage({ path: "api/v1/uploads/property/"+value.image_2, x:5.0, y:0.5, w:'45%', h:'38%' });
                        }
                        if (value.image_3)
                        {
                            slide.addImage({ path: "api/v1/uploads/property/"+value.image_3, x:0.25, y:3.0, w:'45%', h:'38%' });
                        }
                        if (value.image_4)
                        {
                            slide.addImage({ path: "api/v1/uploads/property/"+value.image_4, x:5.0, y:3.0, w:'45%', h:'38%' });
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
                        slide.addImage({ path: url, x:0.25, y:0.5, w:'45%', h:'38%' });
                        slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                        slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                        slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                    }
                    if (value.slide_no==99)
                    {
                        var slide = pptx.addSlide({masterName:'THANKS_SLIDE', sectionTitle:'Masters'});
                        slide.addImage({ path: "dist/img/thanks.jpg",  x:0.0, y:0.0, w:'100%', h:'100%'  });
                        slide.addNotes('Thanks Page');
                        slide.addText($scope.slides.owner_name, { placeholder:'title',x:3.2,y:4,fontSize:24 });
                        slide.addText('Call:'+$scope.slides.mob_no, { placeholder:'title',x:3.6,y:4.4,fontSize:14 });
                        slide.addText('Email:'+$scope.slides.email, { placeholder:'title',x:3.5,y:4.7,fontSize:14 });
                    }

                });
                pptx.writeFile('preleased_report')
                .then(function(fileName){ console.log('Saved! File Name: '+fileName) });
            

            });
        }
        if (report_type=="xlsx")
        {
            
        }

    };


});

app.controller('OneMailer', function ($scope, $rootScope, $routeParams, $location, $http, Data, $sce,$timeout) {
    var module_name = $routeParams.module_name;
    var id = $routeParams.id;
    var data = $routeParams.data;

    $scope.module_name = module_name;
    $scope.activePath = null;
    $scope.slides = {};
    Data.get('properties_images/'+data).then(function (results) {
        $scope.property_images_slide = results;
    });
    $scope.slides.category = "";
    $scope.slides.category_id = 0;
    $scope.slides.image_1_id = 0;
    $scope.slides.image_2_id = 0;
    $scope.slides.image_3_id = 0;
    $scope.arrTabRows = {};
    Data.get('onemailer/'+module_name+'/'+id+'/'+data).then(function (results) {
        $scope.slide_data = results;
        
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
        $scope.slides.attachment_id = results[0].attachment_id;
        $scope.slides.main_image = results[0].filenames;
        $scope.slides.description = results[0].description;

        $scope.slides.area_id = results[0].area_id;
        $scope.slides.area_name = results[0].area_name;
        $scope.slides.locality_id = results[0].locality_id;
        $scope.slides.locality = results[0].locality;
        $scope.slides.propsubtype = results[0].propsubtype;
        $scope.slides.suitable_for = results[0].suitable_for;
        $scope.slides.exp_price = results[0].exp_price+" "+results[0].exp_price_para;
        $scope.slides.frontage = results[0].frontage;
        
        $scope.slides.occu_certi = results[0].occu_certi;
        $scope.slides.top_description = results[0].top_description;
        $scope.slides.disclaimer = results[0].disclaimer;

        $scope.slides.image_1_id = 0;
        $scope.slides.image_2_id = 0;
        $scope.slides.image_3_id = 0;

        console.log("area"+results[0].area_id);
        console.log("area_name"+results[0].area_name);
        console.log("locality_id"+results[0].locality_id);
        console.log("locality"+results[0].locality);
        console.log("propsubtype"+results[0].propsubtype);
        console.log("suitable_for"+results[0].suitable_for);

    });    

    $scope.close = function()
    {
        $("#view_download").css("display","none");
    }

    $scope.addimage = function(attachment_id,image_name,description)
    {
        console.log($scope.slides.image_1_id);
        console.log($scope.slides.image_2_id);
        console.log($scope.slides.image_3_id);
        if ($scope.slides.image_1_id==0){
            $("#image_1").html('<img class="page_links_img" src="api/v1/uploads/property/'+image_name+'" />');
            $scope.slides.image_1_id=attachment_id;
            $scope.slides.image_1_name=image_name;
            return;
        }
        else{
            if ($scope.slides.image_2_id==0)
            {
                $("#image_2").html('<img class="page_links_img" src="api/v1/uploads/property/'+image_name+'" />');
                $scope.slides.image_2_id=attachment_id;
                $scope.slides.image_2_name=image_name;
                return;
            }
            else{
                if ($scope.slides.image_3_id==0)
                {
                    $("#image_3").html('<img class="page_links_img" src="api/v1/uploads/property/'+image_name+'" />');
                    $scope.slides.image_3_id=attachment_id;
                    $scope.slides.image_3_name=image_name;
                    $scope.slides.description_3 = description;
                    return;
                }
                else{
                    alert("Maximum 3 Images Allowed..!!!");
                    return;
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
            
            return;
        }
        if (id==2)
        {
            $("#image_2").html('');
            $scope.slides.image_2_id=0;
            $scope.slides.image_2_name="";
            
        }
        if (id==3)
        {
            $("#image_3").html('');
            $scope.slides.image_3_id=0;
            $scope.slides.image_3_name="";
            
        }
    }


    $scope.saveslidedata = function(slides)
    {
        Data.post('onemailer_saveslidedata', {
            slides: slides
        }).then(function (results) {
            Data.toast(results);
            
        });
    }

    $scope.export_report = function(report_type)
    {
        $scope.slides = {};
        var url = $("#map_image").prop('src');

        if (report_type=="PDF")
        {
            var pptx = new PptxGenJS();

            pptx.addSection({ title: 'Masters' });
            var slide1 = pptx.addSlide({masterName:'TITLE_SLIDE', sectionTitle:'Masters'});
            slide1.addImage({ path: "dist/img/ppt_main.jpg", x:0.0, y:0.0, w:'100%', h:'100%' });
            slide1.addNotes('Welcome Page');
            Data.get('onemailer_getslidedata').then(function (results) {
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
                $scope.slides.attachment_id = results[0].attachment_id;
                $scope.slides.main_image = results[0].filenames;
                $scope.slides.description = results[0].description;
        
                $scope.slides.area_id = results[0].area_id;
                $scope.slides.area_name = results[0].area_name;
                $scope.slides.locality_id = results[0].locality_id;
                $scope.slides.locality = results[0].locality;
                $scope.slides.propsubtype = results[0].propsubtype;
                $scope.slides.suitable_for = results[0].suitable_for;
                $scope.slides.exp_price = results[0].exp_price+" "+results[0].exp_price_para;
                $scope.slides.frontage = results[0].frontage;
                
                $scope.slides.occu_certi = results[0].occu_certi;
                $scope.slides.top_description = results[0].top_description;
                $scope.slides.disclaimer = results[0].disclaimer;
                $scope.slides.image_1 = results[0].image_1;
                $scope.slides.image_2 = results[0].image_2;
                $scope.slides.image_3 = results[0].image_3;

                var slide = pptx.addSlide({masterName:"MASTER_SLIDE", sectionTitle:'Masters'});
                slide.addNotes('Main Image');
                slide.addText(value.description, { placeholder:'title',x:0.3,fontSize:20 });
                slide.addImage({ path: "api/v1/uploads/property/"+$scope.slides.image_1, x:0.25, y:0.5, w:'96%', h:'83%' });
                slide.addImage({ path: "dist/img/mini_logo.png", x: 9.2, y: 0.1, w: 0.3, h: 0.3 });
                slide.addShape(pptx.shapes.LINE, { x:0.1 , y:0.4, w:'98%', h:0, line:'d2aa4b', line_size:1});
                slide.addShape(pptx.shapes.LINE, { x:0.1 , y:5.3, w:'98%', h:0, line:'d2aa4b', line_size:1});
                slide.addText('www.rdbrothers.com',{ x:0.3 , y:5.4,fontSize:12});
                slide.addText('BUILT ON EXPERIENCE',{ x:7.5 , y:5.4,fontSize:12});
                console.log($scope.slides.slide_no);

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
                        { text: 'Quoted Rate', options: { valign:'top', align:'left'  } },
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
                slide.addTable(
                    arrTabRows, { x: 2.0, y: 0.8, w: 6.5, rowH: 0.25, fontSize:12, color:'363636', border:{pt:'1', color:'d2aa4b'} }
                );
            });
            pptx.writeFile('onemailer_report')
            .then(function(fileName){ console.log('Saved! File Name: '+fileName) });
        }
        if (report_type=="img")
        {
            Data.get('onemailer_getslidedata').then(function (results) 
            {
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
                $scope.slides.attachment_id = results[0].attachment_id;
                $scope.slides.main_image = results[0].filenames;
                $scope.slides.description = results[0].description;

                $scope.slides.area_id = results[0].area_id;
                $scope.slides.area_name = results[0].area_name;
                $scope.slides.locality_id = results[0].locality_id;
                $scope.slides.locality = results[0].locality;
                $scope.slides.propsubtype = results[0].propsubtype;
                $scope.slides.suitable_for = results[0].suitable_for;
                $scope.slides.exp_price = results[0].exp_price+" "+results[0].exp_price_para;
                $scope.slides.frontage = results[0].frontage;
                $scope.slides.occu_certi = results[0].occu_certi;
                $scope.slides.top_description = results[0].top_description;
                $scope.slides.disclaimer = results[0].disclaimer;
                
                $scope.slides.project_contact = results[0].project_contact;
                $scope.slides.usermobileno = results[0].usermobileno;
                $scope.slides.contact_photo = results[0].contact_photo;
                
                $scope.slides.image_1 = results[0].image_1;
                $scope.slides.image_2 = results[0].image_2;
                $scope.slides.image_3 = results[0].image_3;
                //width:1280px;height:933px;
                $htmlstring = '<div style="background-color:#d2aa4b;padding-top:35px;">'+
                        '<div style="width:75%;float:left;margin-left:90px;">'+
                            '<h1 style="font-size:54px;margin:20px;color:#ffffff;text-transform: uppercase;">'+
                             $scope.slides.description+'</h1>'+                            
                        '</div>'+
                        '<div style="float:right;width:14%;">'+
                        '<img class="mini_log_img" src="dist/img/onemailer_logo.jpeg"/>'+
                        '</div>'+
                        '<div style="padding:20px;">'+
                        '<div style="width:75%;margin-left:90px;">'+
                        '<p style="font-size:20px;color:#ffffff;">'+
                        ' '+$scope.slides.top_description+'</p>'+
                        '</div>'+
                        '</div>'+
                        '<div style="margin-top:45px;margin-left:90px;" >'+
                        '<div id="image_1" style="width:31.5%;float:left;">'+
                        '<img src="api/v1/uploads/property/'+$scope.slides.image_1+'"'+ 
                        ' style="width:360px;height:240px;margin-right:5px;-webkit-box-shadow: 0'+ '27px 34px rgba(0, 0, 0, 0.5);'+
                        'box-shadow: 0 27px 34px rgba(0, 0, 0, 0.5);"/>'+ 
                        '</div>'+
                        '<div id="image_2"  style="width:31.5%;float:left;">'+
                        '<img src="api/v1/uploads/property/'+$scope.slides.image_2+'"'+
                        ' style="width:360px;height:240px;margin-right:5px;-webkit-box-shadow: 0'+ '27px 34px rgba(0, 0, 0, 0.5);'+
                        'box-shadow: 0 27px 34px rgba(0, 0, 0, 0.5);"/>'+ 
                        '</div>'+
                        '<div id="image_3"  style="width:33%;float:left;">'+
                        '<img src="api/v1/uploads/property/'+$scope.slides.image_3+'"'+
                        ' style="width:360px;height:240px;-webkit-box-shadow: 0 27px 34px rgba(0'+ ',0, 0, 0.5);'+
                        'box-shadow: 0 27px 34px rgba(0, 0, 0, 0.5);"/>'+ 
                        '</div>'+
                        '</div>'+
                        '<div style="padding:5px;background-color:#ffffff;width:99%;margin:6px;margin-top:208px;height:468px;">'+
                        '<div style="width:32.5%;float:left;padding:45px;margin-left:35px;border-right:1px dotted #9d9696;padding-bottom:10px;">'+
                        '<p style="font-size:20px;margin-bottom:20px;">      Location:<span style="color:#9d9696;">'+$scope.slides.location+'</span></p>'+
                        '<p style="font-size:20px;margin-bottom:20px;">Area: <span style="color:#9d9696;">     '+$scope.slides.carp_area+'</span></p>'+
                        '<p style="font-size:20px;margin-bottom:20px;">Height / Fontage: <span style="color:#9d9696;">     '+$scope.slides.frontage+'</span></p>'+
                        '</div>'+
                        '<div style="width:30%;float:left;padding:45px;border-right:1px dotted #9d9696;">'+
                        '<p style="font-size:20px;margin-bottom:20px;">    Rent:<span style="color:#9d9696;">  '+$scope.slides.exp_price+'</span></p>'+
                        '<p style="font-size:20px;margin-bottom:20px;"> Suitable For: <span style="color:#9d9696;">    '+$scope.slides.suitable_for+'</span></p>'+
                        '<p style="font-size:20px;margin-bottom:20px;">  OC:  <span style="color:#9d9696;">  '+$scope.slides.occu_certi+'</span></p>'+
                        '</div>'+
                        '<div style="width:20%;float:left;padding:45px;padding-bottom:5px;">'+
                        '<p><img src="api/v1/uploads/employee/'+$scope.slides.contact_photo+'"'+ 
                        ' style="width:75px;height:90px;"/></p>'+ 
                        '<p style="font-size:20px;margin-bottom:10px;"> CONTACT</p>'+
                        '<p style="font-size:20px;margin-bottom:10px;color:#9d9696;">'+$scope.slides.usermobileno+'</p>'+
                        '<p style="font-size:20px;margin-bottom:10px;color:#9d9696;">'+$scope.slides.project_contact+'</p>'+
                        '</div>'+
                        '<div style="clear:both;"></div>'+
                        '<div style="width:92%;padding:10px;margin-left:75px;">'+
                        '<p style="font-size:10px;color:#000000;">Disclaimer:</p>'+
                        '<p style="font-size:10px;color:#000000;">'+$scope.slides.disclaimer+'</p>'+
                        '</div>'+
                    '</div>'+
                '</div>';
                $("#image_page").html($htmlstring);
                console.log($("#image_page").html());
                
                html2canvas(document.getElementById("image_page"),{allowTaint : true}).then(function(canvas) {
                    var link = document.createElement("a");
                    document.body.appendChild(link);
                    link.download = "onemailer.jpg"; 
                    //console.log(canvas.toDataURL())  ;                
                    //link.href = canvas.toDataURL();
                    link.href = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
                    link.target = '_blank';
                    link.click();
                });
            });
            


                /*html2canvas(document.getElementById("image_page"),{
                    width: 1280,
                    height: 933
                  }).then(function(canvas) {
                    data = canvas.toDataURL();
                    var link = document.createElement("a");
                    document.body.appendChild(link);
                    link.download = "onemailer.jpg";
                    link.href = canvas.toDataURL();
                    link.target = '_blank';
                    link.click();
                    $scope.image_data={
                        category :$scope.slides.category,
                        category_id :$scope.slides.category_id,
                        file_data:''
                    };
                    $scope.image_data.file_data = data;
                    console.log($scope.image_data.file_data);
                    Data.post('save_onemailer', {
                        image_data: $scope.image_data
                    }).then(function (results) {
                        Data.toast(results);
                        $scope.image_to_view = results.image_name;
                        $("#view_download").css("display","block");
                        //$("#image_98_1").attr("src","api/v1/uploads/property/"+results.image_name);
                        //$scope.map_image_name = results.image_name;
                        //$("#image_"+slide_no+"_"+image_number).attr("src","api/v1/uploads/property/"+results.image_name)
                    });

                    $("#image_page").html('<p>.</p>');
                });*/

                
                
                /*html2canvas(document.getElementById("image_div")).then(function(canvas) {
                    var link = document.createElement("a");
                    document.body.appendChild(link);
                    link.download = "onemailer.jpg";
                    link.href = canvas.toDataURL();
                    link.target = '_blank';
                    link.click();
                });*/
            
            
        }

    };

});

// PROPERTIES


var geocoder;
var map;
var marker;

function myMap() {
    var mapCanvas = document.getElementById("map");
    var mapOptions = {
      center: new google.maps.LatLng(19.1201452,72.85193070000003),
      zoom: 14
    };
    map = new google.maps.Map(mapCanvas, mapOptions);
    geocoder = new google.maps.Geocoder();

    var myLatLng = {lat: 19.1201452, lng: 72.85193070000003};
    marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        draggable: true,
        title: 'My Location'
    });
    google.maps.event.addListener(marker, 'dragend', function() {
        //updateMarkerStatus('Drag ended');
        geocodePosition(marker.getPosition());
        //alert(marker.getPosition());
    });
    /*google.maps.event.addListenerOnce(map, 'idle', function(){
        // do something only the first time the map is loaded
        takeScreenshot = () => {
            let googleMapsView = document.querySelector('.google-map');
    
            html2canvas(googleMapsView).then((canvas) => {
                let imgData = canvas.toDataURL('image/png');
                return imgData;
                console.log(imgData);
             });
         }
    });*/

    
  }
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
  function updateMarkerAddress(str) {
    document.getElementById('address').innerHTML = str;
  }


  
app.controller('tp', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) 
{
});
// AGREEMENTS

app.controller('Agreement_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    var cat = $routeParams.cat;
    $scope.cat = cat;

    $scope.searchdata = {};
    $scope.agreements = {};
    $scope.agreements_completed = {};
    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
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
            Data.get('agreement_list_ctrl/'+$scope.cat+'/'+$scope.next_page_id+'/Open').then(function (results) {
                $scope.agreements = results;
                //$scope.page_range = parseInt($scope.next_page_id)+1+" - ";
                //if (which_side == 'next')
                //{
                    //$scope.next_page_id = parseInt($scope.next_page_id)+30;
                    //$scope.page_range = $scope.page_range + $scope.next_page_id;
                //}
            });
            Data.get('agreement_list_ctrl/'+$scope.cat+'/'+$scope.next_page_id+'/Completed').then(function (results) {
                $scope.agreements_completed = results;
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
            
            $scope.search_agreement($scope.searchdata,'pagenavigation');
            $scope.search_agreement($scope.searchdata,'pagenavigation');
            
        }
    }

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("agreement_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("agreement_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("agreement_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("agreement_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.view_rights)
    {
        $scope.agreements = {};
        alert("You don't have rights to use this option..");
        return;
    }


    /*$timeout(function () { 
        Data.get('properties_dealclose').then(function (results) {
            $rootScope.dealclosed = results;
        });
    }, 100);*/

    /*$timeout(function () { 
        Data.get('enquiries_open').then(function (results) {
            $rootScope.enquiries = results;
        });
    }, 100);*/


    $timeout(function () { 
        Data.get('activity_open').then(function (results) {
            $scope.activities = results;
        });
    }, 100);

    $scope.select_assign_to = function(teams,sub_teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/'+sub_teams).then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }

    $timeout(function () { 
        Data.get('getquarter_data').then(function (results) {
            $scope.eligibility=results[0].eligibility;
            $scope.goal_per_to_get=results[0].goal_per_to_get;
            $scope.goal_percent=results[0].goal_percent;
            $scope.goals_achieved=results[0].goals_achieved;
            $scope.goals_to_achieve=results[0].goals_to_achieve;
            $scope.target_achieved=results[0].target_achieved;
            console.log($scope.eligibility);
            console.log($scope.goals_achieved);
            console.log($scope.goals_to_achieve);

        });
    }, 100);

    $timeout(function () { 
        Data.get('agreement_list_ctrl/'+$scope.cat+'/'+$scope.next_page_id+'/Open').then(function (results) {
            console.log(results[0].agreement_count);
            $scope.agreements = results;
            //$scope.next_page_id = 30;
            $scope.agreement_open_count = results[0].agreement_count;
            //$scope.total_records = results[0].agreement_open_count;
        });
    }, 100);
    

    $timeout(function () { 
        Data.get('agreement_list_ctrl/'+$scope.cat+'/'+$scope.next_page_id+'/Completed').then(function (results) {
            console.log(results[0].agreement_count);
            $scope.agreements_completed = results;
            $scope.next_page_id = 30;
            $scope.agreement_completed_count = results[0].agreement_count;
            $scope.total_records = results[0].agreement_count;
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
                Data.get('selectcontact/Developer').then(function (results) {
                    $scope.developers = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('selectusers').then(function (results) {
                    $scope.users = results;
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
                Data.get('selectenquiry_with_broker').then(function (results) {
                    $scope.enquiries = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('getdatavalues_agreement/property_id').then(function (results) {
                    $scope.property_ids = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_agreement/project_id').then(function (results) {
                    $scope.project_ids = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_agreement/enquiry_id').then(function (results) {
                    $scope.enquiry_ids = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_agreement/agreement_id').then(function (results) {
                    $scope.agreement_ids = results;
                });
            }, 100);
        }
    };

    $scope.search_agreement = function (searchdata,from_click) 
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
        criteria_not_matching = "false";
        searchdata.listcategory = 'Open';
        Data.post('search_agreement', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.agreements, function() {
                if (results[0].agreement_count)
                {
                    $scope.agreements = {};
                    $scope.agreements = results;
                    $scope.agreement_open_count = results[0].agreement_count;
                    $scope.total_records = results[0].agreement_count;
                    $scope.next_page_id = parseInt($scope.next_page_id)+30;
                    $scope.page_range = $scope.page_range + $scope.next_page_id;
                }
                else
                {
                    //alert("Search Criteria Not Matching.. !!");
                    criteria_not_matching = "true";
                }
            },true);
            searchdata.listcategory = 'Completed';
            Data.post('search_agreement', {
                searchdata: searchdata
            }).then(function (results) {
                $scope.$watch($scope.agreements, function() {
                    if (results[0].agreement_count)
                    {

                        $scope.agreements_completed = {};
                        $scope.agreements_completed = results;
                        $scope.agreement_completed_count = results[0].agreement_count;
                        $scope.total_records = results[0].agreement_count;
                    }
                    else
                    {
                        if (criteria_not_matching == 'true')
                        {
                            alert("Search Criteria Not Matching.. !!");
                        }
                    }

                },true);
            });
            
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
        
        Data.get('agreement_list_ctrl/'+$scope.cat+'/0/Open').then(function (results) {
            $scope.agreements = results;
            //$scope.next_page_id = 30;
            $scope.agreement_open_count = results[0].agreement_count;
            //$scope.total_records = results[0].agreement_open_count;
        });

        Data.get('agreement_list_ctrl/'+$scope.cat+'/0/Completed').then(function (results) {
            $scope.agreements_completed = results;
            $scope.next_page_id = 30;
            $scope.agreement_completed_count = results[0].agreement_count;
            $scope.total_records = results[0].agreement_count;
        });


    }
    Data.get('selectdropdowns/AGREEMENT_STAGE_LEASE').then(function (results) {
        $scope.agreement_stages_lease = results;
    });

    Data.get('selectdropdowns/AGREEMENT_STAGE_SALE').then(function (results) {
        $scope.agreement_stages_sale = results;
    });

    Data.get('selectdropdowns/AGREEMENT_STAGE_SALE_UC').then(function (results) {
        $scope.agreement_stages_sale_uc = results;
    });

    Data.get('selectdropdowns/AGREEMENT_STAGE_PRELEASE').then(function (results) {
        $scope.agreement_stages_prelease = results;
    });

    $scope.change_agreement_stage_only = function(agreement_stage,agreement_id)
    {
        Data.get('change_agreement_stage_only/'+agreement_stage+'/'+agreement_id).then(function (results) {
            
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
                Data.get('getreport_fields/agreement/unselected').then(function (results) {
                    $scope.unselected_fields = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getreport_fields/agreement/selected').then(function (results) {
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
                Data.get('getreport_fields/agreement/unselected').then(function (results) {
                    $rootScope.unselected_fields = results;
                });
            }, 100);
    
            $timeout(function () { 
                Data.get('getreport_fields/agreement/selected').then(function (results) {
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
            Data.get('exportdata/agreement/agreement_id/'+data+'/'+$scope.option_value).then(function (results) {
                window.location="api//v1//uploads//agreement_list.xlsx";
            });
        }, 100);
      }






});

app.controller('Agreement_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, Data, $sce) {
    var id = $routeParams.id;
    var category = $routeParams.category;
    $scope.cat = "direct";
    $scope.agreement = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("agreement_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("agreement_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("agreement_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("agreement_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.create_rights)
    {
        $scope.agreement = {};
        alert("You don't have rights to use this option..");
        return;
    }

    /*$timeout(function () { 
        Data.get('selectenquiry').then(function (results) {
            $rootScope.enquiries = results;
        });
    }, 100);
    
    $timeout(function () { 
        Data.get('selectproperty').then(function (results) {
            $rootScope.properties = results;
        });
    }, 100);*/

    $scope.agreement={
        project_id:0,
        proptype:'',
        items:[{
            
        }]
    };
    
    $scope.addItem=function(){
          $scope.agreement.items.push({
              contribution_by:'Broker',
              contribution_to:0,
              contribution_per:0,
              contribution_amount:0
        });
    };

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
        Data.get('selectcontact/Developer').then(function (results) {
            $scope.developers = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.users = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectbranch_office').then(function (results) {
            $scope.franchisees = results;
        });
    }, 100);



    Data.get('selectdropdowns/AGREEMENT_STAGE_LEASE').then(function (results) {
        $scope.agreement_stages_lease = results;
    });

    Data.get('selectdropdowns/AGREEMENT_STAGE_SALE').then(function (results) {
        $scope.agreement_stages_sale = results;
    });

    Data.get('selectdropdowns/AGREEMENT_STAGE_SALE_UC').then(function (results) {
        $scope.agreement_stages_sale_uc = results;
    });

    Data.get('selectdropdowns/AGREEMENT_STAGE_PRELEASE').then(function (results) {
        $scope.agreement_stages_prelease = results;
    });

    Data.get('selectsubteams').then(function (results) {
        $scope.sub_teams_list = results;
    });
    
    $scope.getowner_developer = function(property_id)
    {
        Data.get('getowner_developer/'+property_id).then(function (results) {
            $scope.$watch($scope.agreement.agreement_from, function() {
                $scope.agreement.agreement_from = results[0].propfrom;
            }, true);
            if (results[0].propfrom=='Owner' || results[0].propfrom=='Developer')
            {
                $scope.agreement.contact_id = results[0].dev_owner_id;
                $scope.$watch($scope.agreement.contact_id, function() {
                    $scope.agreement.contact_id = results[0].dev_owner_id;
                }, true);
            }
            else if (results[0].propfrom=='Broker')
            {   
                $scope.$watch($scope.agreement.broker1_id, function() {
                    $scope.agreement.broker1_id = results[0].dev_owner_id;
                }, true);
            }
        }); 
    }

    $scope.getenquiry_buyer = function(enquiry_id)
    {
        Data.get('getenquiry_buyer/'+enquiry_id).then(function (results) {
            $scope.$watch($scope.agreement.buyer_id, function() {
                $scope.agreement.buyer_id = results[0].client_id;
            }, true);
        }); 
    }

    $scope.agreement_stage_change = function(agreement_for,agreement_id)
    {
        Data.get('agreement_stage_change/'+agreement_for+'/'+agreement_id).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_agreement_stage = $sce.trustAsHtml($scope.html);
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

    
    if (category == 'direct')
    {
        Data.get('getassignedproperties').then(function (results) {
            $scope.m_properties = results;
        });

        Data.get('selectenquiry_with_broker').then(function (results) {
            $scope.m_enquiries = results;
        });


    }
/*
    `agreement_value` int(15) NOT NULL,
    `agreement_value_para` varchar(10) NOT NULL,
    `furniture` int(15) NOT NULL,
    `furniture_para` varchar(10) NOT NULL,
    `basic_cost` int(15) NOT NULL,
    `basic_cost_para` varchar(10) NOT NULL,
    `rent` int(15) NOT NULL,
    `rent_para` varchar(10) NOT NULL,
    `buyer_brokerage` int(15) NOT NULL,
    `buyer_brokerage_para` varchar(10) NOT NULL,
    `seller_brokerage` int(15) NOT NULL,
    `seller_brokerage_para` varchar(10) NOT NULL,
    `total_bokerage` int(15) NOT NULL,
    `total_brokerage_para` varchar(10) NOT NULL,
    `our_brokerage` int(15) NOT NULL,
    `our_brokerage_para` varchar(10) NOT NULL,
    `service_tax` decimal(7,2) NOT NULL,
    `cgst` decimal(7,2) NOT NULL,
    `sgst` decimal(7,2) NOT NULL,
    `tds` int(15) NOT NULL,
    `tds_para` varchar(10) NOT NULL,
    `gross_brokerage` int(15) NOT NULL,
    `gross_brokerage_para` varchar(10) NOT NULL,*/
    

    $scope.calculations = function()
    {
        console.log($scope.agreement.agreement_value);
        agreement_value = numactual_new($scope.agreement.agreement_value,$scope.agreement.agreement_value_para);
        console.log("agreement_value"+agreement_value);
        if ($scope.agreement.buyer_brokerage_per>0)
        {
            $scope.agreement.buyer_brokerage = parseFloat(agreement_value * (($scope.agreement.buyer_brokerage_per)/100)).toFixed(2);

        }
        console.log("buyer_brokerage",$scope.agreement.buyer_brokerage);
        if ($scope.agreement.seller_brokerage_per>0)
        {
            $scope.agreement.seller_brokerage = parseFloat(agreement_value * (($scope.agreement.seller_brokerage_per)/100)).toFixed(2);

        }
        console.log("seller_brokerage",$scope.agreement.seller_brokerage);

        $scope.agreement.total_brokerage = parseFloat($scope.agreement.buyer_brokerage) + parseFloat($scope.agreement.seller_brokerage);

        if ($scope.agreement.our_brokerage_per>0)
        {
            $scope.agreement.our_brokerage = parseFloat(agreement_value * (($scope.agreement.our_brokerage_per)/100)).toFixed(2);
        }
        else
        {
            //$scope.agreement.our_brokerage = parseFloat($scope.agreement.total_brokerage);
        }
        console.log("our_brokerage",$scope.agreement.our_brokerage);

        //$scope.agreement.gross_brokerage =  parseFloat($scope.agreement.total_brokerage) + parseFloat($scope.agreement.our_brokerage);
        
        if ($scope.agreement.service_tax_per>0)
        {
            $scope.agreement.service_tax = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.service_tax_per)/100)).toFixed(2);
        }
        console.log("service_tax",$scope.agreement.service_tax);
        if ($scope.agreement.cgst_per>0)
        {
            $scope.agreement.cgst = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.cgst_per)/100)).toFixed(2);
        }
        console.log("cgst",$scope.agreement.cgst);
        if ($scope.agreement.sgst_per>0)
        {
            $scope.agreement.sgst = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.sgst_per)/100)).toFixed(2);
        }
        console.log("sgst",$scope.agreement.sgst);
        if ($scope.agreement.tds_per>0)
        {
            $scope.agreement.tds = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.tds_per)/100)).toFixed(2);
        }
        console.log("tds",$scope.agreement.tds);
        $scope.agreement.gross_brokerage =  parseFloat($scope.agreement.total_brokerage) +  parseFloat($scope.agreement.service_tax) + parseFloat($scope.agreement.cgst) + parseFloat($scope.agreement.sgst) - parseFloat($scope.agreement.tds);

    }

    $scope.calculate_contribution = function(contribution_per,id)
    {
        item=$scope.agreement.items[id];
        item.contribution_amount = parseFloat($scope.agreement.our_brokerage * ((contribution_per)/100)).toFixed(2);
        console.log("contribution_amount",item.contribution_amount);
    }
    
    if (category == 'property')
    {
        Data.get('getenquiries_properties/'+id).then(function (results) {
            $scope.m_enquiries = results;
        });


        Data.get('getfromproperty/'+id).then(function (results) {
            arr_assign_to = ((results[0].assign_to).split(','));
            arr_teams = ((results[0].teams).split(','));
            $scope.$watch($scope.agreement, function() {
                $scope.agreement = {};
                $scope.agreement = {
                                    agreement_for : results[0].property_for,
                                    property_id : results[0].property_id,
                                    agreement_from : results[0].propertyfrom,
                                    agreement_contact_id : results[0].dev_owner_id,
                                    transfer_type : results[0].tranf_charge,
                                    assign_to : arr_assign_to,
                                    teams : arr_teams
                };
            });
        });
    }

    if (category == 'enquiry')
    {
        Data.get('getproperties_enquiries/'+id).then(function (results) {
            $scope.m_properties = results;
        });

        $timeout(function () { 
            Data.get('getfromenquiry/'+id).then(function (results) {
                arr_assign_to = ((results[0].assigned).split(','));
                arr_teams = ((results[0].teams).split(','));
                $scope.$watch($scope.agreement, function() {
                    $scope.agreement = {};
                    $scope.agreement = {
                                        agreement_for : results[0].enquiry_for,
                                        enquiry_id : results[0].enquiry_id,
                                        buyer_id :results[0].client_id,
                                        transfer_type : results[0].tranf_charge,
                                        assign_to : arr_assign_to,
                                        teams : arr_teams
                    };
                });
            });
        }, 100);
    }

    if (category == 'activity')
    {
        $timeout(function () { 
            Data.get('getfromactivity/'+id).then(function (results) {
                arr_assign_to = ((results[0].assign_to).split(','));
                arr_teams = ((results[0].teams).split(','));

                $scope.$watch($scope.agreement, function() {
                    $scope.agreement = {};
                    $scope.agreement = {
                                    agreement_for : results[0].property_for,
                                    property_id : results[0].property_id,
                                    enquiry_id : results[0].enquiry_id,
                                    contact_id : results[0].contact_id,
                                    buyer_id : results[0].client_id,
                                    assign_to : arr_assign_to,
                                    teams : arr_teams
                    };
                });
            });
        }, 100);
    }

    

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
                        if ($scope.temptype=='AGREEMENT_STAGE')
                        {
                            controlvalue = "agreement_stages";
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
    $scope.agreement_stage_clicked = "No";
    $scope.checkbox_clicked = function()
    {
        console.log("checkbox clicked...");
        $scope.agreement_stage_clicked="Yes";
        console.log($scope.agreement_stage_clicked);
        $("#agreement").removeClass("active");
        $("#documents").addClass("active");
        $("#agreement_tab").removeClass("active");
        $("#documents_tab").addClass("active");
        window.scrollTo(0,0);
    }

    //$scope.agreement_add_new = {agreement:''};
    $scope.agreement_add_new = function (agreement) {
        agreement_stage = "";
        $(":checked.check_element").each(
            function(index) 
            {
                console.log(this.value);
                agreement_stage = this.value;
            }
        );
        agreement.agreement_stage = agreement_stage;
        if ($scope.agreement_stage_clicked=='Yes')
        {
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
        }
        
        Data.post('agreement_add_new', {
            agreement: agreement
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
		        $('#file-1').fileinput('upload');
                $location.path('agreement_list/'+$scope.cat);
            }
        });
    };
});
    
app.controller('Agreement_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, Data, $sce) {
    var agreement_id = $routeParams.agreement_id;
    $scope.activePath = null;

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("agreement_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("agreement_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    // if ((($str).indexOf("agreement_update"))!=-1)
    // {
        $scope.update_rights = true;
    //     console.log($scope.update_rights);
    // }
    if ((($str).indexOf("agreement_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.update_rights)
    {
        $scope.fagreements = {};
        alert("You don't have rights to use this option..");
        return;
    }
    
    $scope.agreement={
        items:[{
            
        }]
    };
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
        Data.get('selectcontact/Developer').then(function (results) {
            $scope.developers = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.users = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectbranch_office').then(function (results) {
            $scope.franchisees = results;
        });
    }, 100);

    /*Data.get('selectdropdowns/AGREEMENT_STAGE_LEASE').then(function (results) {
        $rootScope.agreement_stages_lease = results;
    });

    Data.get('selectdropdowns/AGREEMENT_STAGE_SALE').then(function (results) {
        $rootScope.agreement_stages_sale = results;
    });

    Data.get('selectdropdowns/AGREEMENT_STAGE_SALE_UC').then(function (results) {
        $rootScope.agreement_stages_sale_uc = results;
    });

    Data.get('selectdropdowns/AGREEMENT_STAGE_PRELEASE').then(function (results) {
        $rootScope.agreement_stages_prelease = results;
    });*/

    $timeout(function () { 
        Data.get('getassignedproperties').then(function (results) {
            $scope.m_properties = results;
        });
    },100);
    $timeout(function () { 
        Data.get('selectenquiry_with_broker').then(function (results) {
            $scope.m_enquiries = results;
        });
    });
    Data.get('agreement_images/'+agreement_id).then(function (results) {
        $scope.agreement_images = results;
    });

    Data.get('agreement_videos/'+agreement_id).then(function (results) {
        $scope.agreement_videos = results;
    });

    $scope.agreement_stage_change = function(agreement_for,agreement_id)
    {
        Data.get('agreement_stage_change/'+agreement_for+'/'+agreement_id).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_agreement_stage = $sce.trustAsHtml($scope.html);
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

    Data.get('selectsubteams').then(function (results) {
        $scope.sub_teams_list = results;
    });
    
    Data.get('agreement_edit_ctrl/'+agreement_id).then(function (results) {
        $scope.arr = ((results[0].assign_to).split(','));
        results[0].assign_to = $scope.arr;
        $scope.arr = ((results[0].teams).split(','));
        results[0].teams = $scope.arr;
        $scope.arr = ((results[0].sub_teams).split(','));
        results[0].sub_teams = $scope.arr;
        agreement_for = results[0].agreement_for;
        //$rootScope.fagreements = results;
        $scope.$watch($scope.agreement, function() {
            $scope.agreement = {
                agreement_id:results[0].agreement_id,
                advance_maintenance:results[0].advance_maintenance,
                agreement_for:results[0].agreement_for,
                agreement_from:results[0].agreement_from,
                agreement_from_date:results[0].agreement_from_date,
                agreement_till_date:results[0].agreement_till_date,
                agreement_value:results[0].agreement_value,
                agreement_stage:results[0].agreement_stage,
                agreement_status:results[0].agreement_status,
                assign_to:results[0].assign_to,
                basic_cost:results[0].basic_cost,
                buyer_brokerage:results[0].buyer_brokerage,
                buyer_id:results[0].buyer_id,
                broker1_id:results[0].broker1_id,
                broker2_id:results[0].broker2_id,
                cgst:results[0].cgst,
                club_house_charges:results[0].club_house_charges,
                club_house_date:results[0].club_house_date,
                contact_id:results[0].contact_id,
                corpus_fund:results[0].corpus_fund,
                opening_date:results[0].opening_date,
                deal_date:results[0].deal_date,
                possession_date:results[0].possession_date,
                development_charges:results[0].development_charges,
                document_charges:results[0].document_charges,
                document_charges_date:results[0].document_charges_date,
                enquiry_id:results[0].enquiry_id,
                furniture:results[0].furniture,
                gross_brokerage:results[0].gross_brokerage,
                inname:results[0].inname,
                lease_period:results[0].lease_period,
                other_expenses:results[0].other_expenses,
                our_brokerage:results[0].our_brokerage,
                parking_charges:results[0].parking_charges,
                property_id:results[0].property_id,
                project_id:results[0].project_id,
                proptype:results[0].proptype,
                registration_charges:results[0].registration_charges,
                rent:results[0].rent,
                rent_due_date:results[0].rent_due_date,
                security_deposit:results[0].security_deposit,
                security_deposit_date:results[0].security_deposit_date,
                seller_brokerage:results[0].seller_brokerage,
                send_rent_sms:results[0].send_rent_sms,
                service_tax:results[0].service_tax,
                sgst:results[0].sgst,
                shifting_date:results[0].shifting_date,
                stamp_duty:results[0].stamp_duty,
                stamp_duty_date:results[0].stamp_duty_date,
                tds:results[0].tds,
                teams:results[0].teams,
                sub_teams:results[0].sub_teams,
                total_brokerage:results[0].total_brokerage,
                transfer_charges:results[0].transfer_charges,
                transfer_charges_date:results[0].transfer_charges_date,
                transfer_term:results[0].transfer_term,
                transfer_type:results[0].transfer_type,
                agreement_value_para:results[0].agreement_value_para,
                furniture_para:results[0].furniture_para,
                basic_cost_para:results[0].basic_cost_para,
                rent_para:results[0].rent_para,
                buyer_brokerage_per:results[0].buyer_brokerage_per,
                seller_brokerage_per:results[0].seller_brokerage_per,
                our_brokerage_per:results[0].our_brokerage_per,
                service_tax_per:results[0].service_tax_per,
                cgst_per:results[0].cgst_per,
                sgst_per:results[0].sgst_per,
                tds_per:results[0].tds_per,
                items:[]
            }
            angular.forEach(results, function(item,key){
                $scope.agreement.items.push({
                    agreement_details_id:item.agreement_details_id,
                    agreement_id:item.agreement_id,
                    contribution_by:item.contribution_by,
                    contribution_to:item.contribution_to,
                    contribution_per:item.contribution_per,
                    contribution_amount:item.contribution_amount
                });
            });
            Data.get('get_collections/'+agreement_id).then(function (results) {
                $scope.collections = results;
            });
            Data.get('get_contributions/'+agreement_id).then(function (results) {
                $scope.contributions = results;
            });
            Data.get('agreement_stage_change/'+agreement_for+'/'+agreement_id).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_agreement_stage = $sce.trustAsHtml($scope.html);
            }); 
        });

    });

    $scope.addItem=function(){
          $scope.agreement.items.push({
              contribution_by:'Broker',
              contribution_to:0,
              contribution_per:0,
              contribution_amount:0
        });
    };

    
    $scope.calculations = function()
    {
        console.log($scope.agreement.agreement_value);
        agreement_value = numactual_new($scope.agreement.agreement_value,$scope.agreement.agreement_value_para);
        console.log("agreement_value"+agreement_value);
        if ($scope.agreement.buyer_brokerage_per>0)
        {
            $scope.agreement.buyer_brokerage = parseFloat(agreement_value * (($scope.agreement.buyer_brokerage_per)/100)).toFixed(2);

        }
        console.log("buyer_brokerage",$scope.agreement.buyer_brokerage);
        if ($scope.agreement.seller_brokerage_per>0)
        {
            $scope.agreement.seller_brokerage = parseFloat(agreement_value * (($scope.agreement.seller_brokerage_per)/100)).toFixed(2);

        }
        console.log("seller_brokerage",$scope.agreement.seller_brokerage);

        $scope.agreement.total_brokerage = parseFloat($scope.agreement.buyer_brokerage) + parseFloat($scope.agreement.seller_brokerage);

        if ($scope.agreement.our_brokerage_per>0)
        {
            $scope.agreement.our_brokerage = parseFloat(agreement_value * (($scope.agreement.our_brokerage_per)/100)).toFixed(2);
        }
        else
        {
            //$scope.agreement.our_brokerage = parseFloat($scope.agreement.total_brokerage);
        }
        console.log("our_brokerage",$scope.agreement.our_brokerage);

        //$scope.agreement.gross_brokerage =  parseFloat($scope.agreement.total_brokerage) + parseFloat($scope.agreement.our_brokerage);
        
        if ($scope.agreement.service_tax_per>0)
        {
            $scope.agreement.service_tax = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.service_tax_per)/100)).toFixed(2);
        }
        console.log("service_tax",$scope.agreement.service_tax);
        if ($scope.agreement.cgst_per>0)
        {
            $scope.agreement.cgst = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.cgst_per)/100)).toFixed(2);
        }
        console.log("cgst",$scope.agreement.cgst);
        if ($scope.agreement.sgst_per>0)
        {
            $scope.agreement.sgst = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.sgst_per)/100)).toFixed(2);
        }
        console.log("sgst",$scope.agreement.sgst);
        if ($scope.agreement.tds_per>0)
        {
            $scope.agreement.tds = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.tds_per)/100)).toFixed(2);
        }
        console.log("tds",$scope.agreement.tds);
        $scope.agreement.gross_brokerage =  parseFloat($scope.agreement.total_brokerage) +  parseFloat($scope.agreement.service_tax) + parseFloat($scope.agreement.cgst) + parseFloat($scope.agreement.sgst) - parseFloat($scope.agreement.tds);

    }

    $scope.calculate_contribution = function(contribution_per,id)
    {
        item=$scope.agreement.items[id];
        item.contribution_amount = parseFloat($scope.agreement.our_brokerage * ((contribution_per)/100)).toFixed(2);
        console.log("contribution_amount",item.contribution_amount);
    }

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
                        if ($scope.temptype=='AGREEMENT_STAGE')
                        {
                            controlvalue = "agreement_stages";
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
    $scope.removeimage = function (attachment_id) {
        var deleteemployee = confirm('Are you absolutely sure you want to delete?');
        if (deleteemployee) {
            Data.get('removeimage/'+attachment_id).then(function (results) {
                Data.toast(results);
                Data.get('agreement_images/'+agreement_id).then(function (results) {
                    $scope.agreement_images = results;
                });
            });
        }
    };

    $scope.agreement_image_update = function (attachment_id,field_name,value) 
    { 
        Data.get('agreement_image_update/'+attachment_id+'/'+field_name+'/'+value).then(function (results) {
        });
    };

    $scope.agreement_stage_clicked = "No";
    $scope.checkbox_clicked = function()
    {
        console.log("checkbox clicked...");
        $scope.agreement_stage_clicked="Yes";
        console.log($scope.agreement_stage_clicked);
        $("#agreement").removeClass("active");
        $("#documents").addClass("active"); 
        $("#agreement_tab").removeClass("active");
        $("#documents_tab").addClass("active");
        window.scrollTo(0,0);
    }
    $scope.agreement_update = function (agreement) {
        agreement_stage = "";
        $(":checked.check_element").each(
            function(index) 
            {
                console.log(this.value);
                agreement_stage = this.value;
            }
        );
        agreement.agreement_stage = agreement_stage;
        if ($scope.agreement_stage_clicked=='Yes')
        {
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
        }
        Data.post('agreement_update', {
            agreement: agreement
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file-1').fileinput('upload');
                $('#file_videos').fileinput('upload');
                $location.path('agreement_list/direct');
            }
        });
    };
    
    $scope.agreement_delete = function (agreement) {
        //console.log(business_unit);
        var deleteagreement = confirm('Are you absolutely sure you want to delete?');
        if (deleteagreement) {
            Data.post('agreement_delete', {
                agreement: agreement
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('agreement_list/direct');
                }
            });
        }
    };
    
});
    
app.controller('SelectAgreement', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectagreement').then(function (results) {
            $scope.agreements = results;
        });
    }, 100);
});


app.controller('Payments_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, Data) {
    var payments_id = $routeParams.payments_id;
    $scope.activePath = null;

    $scope.create_rights = true;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("agreement_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("agreement_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("agreement_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("agreement_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.update_rights)
    {
        $scope.paymentsdata = {};
        alert("You don't have rights to use this option..");
        return;
    }
    
    $scope.paymentsdata={    };

    $timeout(function () { 
        Data.get('selectcontact/Client').then(function (results) {
            $scope.clients = results;
        });
    }, 100);

    Data.get('payments_edit_ctrl/'+payments_id).then(function (results) {
        
        $scope.$watch($scope.paymentsdata, function() {
            $scope.paymentsdata = {
                payments_id:results[0].payments_id,
                agreement_id:results[0].agreement_id,
                client_category:results[0].client_category,
                client_id:results[0].client_id,
                billing_name:results[0].billing_name,
                due_date:results[0].due_date,
                next_pay_date:results[0].next_pay_date,
                //reminder_due_date:results[0].reminder_due_date,
                brokerage:results[0].brokerage,
                service_tax:results[0].service_tax,
                cgst:results[0].cgst,
                sgst:results[0].sgst,
                tds:results[0].tds,
                total_brokerage:results[0].total_brokerage,
                collection_status:results[0].collection_status,
                payment_received:results[0].payment_received
            }
            
        });

    });

    $scope.paymentsdata.total_brokerage = (parseFloat($scope.paymentsdata.brokerage)+parseFloat($scope.paymentsdata.service_tax)+parseFloat($scope.paymentsdata.cgst)+parseFloat($scope.paymentsdata.sgst)-parseFloat($scope.paymentsdata.tds));

    $scope.calculations = function()
    {
        console.log($scope.agreement.agreement_value);
        agreement_value = numactual_new($scope.agreement.agreement_value,$scope.agreement.agreement_value_para);
        console.log("agreement_value"+agreement_value);
        if ($scope.agreement.buyer_brokerage_per>0)
        {
            $scope.agreement.buyer_brokerage = parseFloat(agreement_value * (($scope.agreement.buyer_brokerage_per)/100)).toFixed(2);

        }
        console.log("buyer_brokerage",$scope.agreement.buyer_brokerage);
        if ($scope.agreement.seller_brokerage_per>0)
        {
            $scope.agreement.seller_brokerage = parseFloat(agreement_value * (($scope.agreement.seller_brokerage_per)/100)).toFixed(2);

        }
        console.log("seller_brokerage",$scope.agreement.seller_brokerage);

        $scope.agreement.total_brokerage = parseFloat($scope.agreement.buyer_brokerage) + parseFloat($scope.agreement.seller_brokerage);

        if ($scope.agreement.our_brokerage_per>0)
        {
            $scope.agreement.our_brokerage = parseFloat(agreement_value * (($scope.agreement.our_brokerage_per)/100)).toFixed(2);
        }
        else
        {
            $scope.agreement.our_brokerage = parseFloat($scope.agreement.total_brokerage);
        }
        console.log("our_brokerage",$scope.agreement.our_brokerage);

        //$scope.agreement.gross_brokerage =  parseFloat($scope.agreement.total_brokerage) + parseFloat($scope.agreement.our_brokerage);
        
        if ($scope.agreement.service_tax_per>0)
        {
            $scope.agreement.service_tax = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.service_tax_per)/100)).toFixed(2);
        }
        console.log("service_tax",$scope.agreement.service_tax);
        if ($scope.agreement.cgst_per>0)
        {
            $scope.agreement.cgst = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.cgst_per)/100)).toFixed(2);
        }
        console.log("cgst",$scope.agreement.cgst);
        if ($scope.agreement.sgst_per>0)
        {
            $scope.agreement.sgst = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.sgst_per)/100)).toFixed(2);
        }
        console.log("sgst",$scope.agreement.sgst);
        if ($scope.agreement.tds_per>0)
        {
            $scope.agreement.tds = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.tds_per)/100)).toFixed(2);
        }
        console.log("tds",$scope.agreement.tds);
        $scope.agreement.gross_brokerage =  parseFloat($scope.agreement.total_brokerage) +  parseFloat($scope.agreement.service_tax) + parseFloat($scope.agreement.cgst) + parseFloat($scope.agreement.sgst) - parseFloat($scope.agreement.tds);

    }

    
    $scope.payments_update = function (paymentsdata) {
        if (paymentsdata.due_date=='00-00-0000' || paymentsdata.due_date=='')
        {
            alert("Blank due date not allowed..");
            return;
        }
        if (paymentsdata.next_pay_date=='00-00-0000' || paymentsdata.due_date=='')
        {
            alert("Blank Next Payment date not allowed..");
            return;
        }
        paymentsdata.total_brokerage = (parseFloat(paymentsdata.brokerage)+parseFloat(paymentsdata.service_tax)+parseFloat(paymentsdata.cgst)+parseFloat(paymentsdata.sgst)-parseFloat(paymentsdata.tds));
        Data.post('payments_update', {
            paymentsdata: paymentsdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('collections');
            }
        });
    };
    
    $scope.payments_delete = function (paymentsdata) {
        //console.log(business_unit);
        var deletepayments = confirm('Are you absolutely sure you want to delete?');
        if (deletepayments) {
            Data.post('payments_delete', {
                paymentsdata: paymentsdata
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('collections');
                }
            });
        }
    };
    
});

app.controller('Contributions_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, Data) {
    var agreement_details_id = $routeParams.agreement_details_id;
    $scope.activePath = null;

    $scope.create_rights = true;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("agreement_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("agreement_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("agreement_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("agreement_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.update_rights)
    {
        $scope.contributionsdata = {};
        alert("You don't have rights to use this option..");
        return;
    }
    
    $scope.contributionsdata={};
    

    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.users = results;
        });
    }, 100);

    
    Data.get('contributions_edit_ctrl/'+agreement_details_id).then(function (results) {
        
        $scope.$watch($scope.contributionsdata, function() {
            $scope.contributionsdata = {
                contributions_id:results[0].contributions_id,
                agreement_id:results[0].agreement_id,
                agreement_details_id:results[0].agreement_details_id,
                contribution_by:results[0].contribution_by,
                contribution_to:results[0].contribution_to,
                contribution_per:results[0].contribution_per,
                payment_approved:results[0].payment_approved,
                amount_paid:results[0].amount_paid,
                contribution_amount:results[0].contribution_amount
            }
            
        });

    });

    
    $scope.calculations = function()
    {
        console.log($scope.agreement.agreement_value);
        agreement_value = numactual_new($scope.agreement.agreement_value,$scope.agreement.agreement_value_para);
        console.log("agreement_value"+agreement_value);
        if ($scope.agreement.buyer_brokerage_per>0)
        {
            $scope.agreement.buyer_brokerage = parseFloat(agreement_value * (($scope.agreement.buyer_brokerage_per)/100)).toFixed(2);

        }
        console.log("buyer_brokerage",$scope.agreement.buyer_brokerage);
        if ($scope.agreement.seller_brokerage_per>0)
        {
            $scope.agreement.seller_brokerage = parseFloat(agreement_value * (($scope.agreement.seller_brokerage_per)/100)).toFixed(2);

        }
        console.log("seller_brokerage",$scope.agreement.seller_brokerage);

        $scope.agreement.total_brokerage = parseFloat($scope.agreement.buyer_brokerage) + parseFloat($scope.agreement.seller_brokerage);

        if ($scope.agreement.our_brokerage_per>0)
        {
            $scope.agreement.our_brokerage = parseFloat(agreement_value * (($scope.agreement.our_brokerage_per)/100)).toFixed(2);
        }
        else
        {
            $scope.agreement.our_brokerage = parseFloat($scope.agreement.total_brokerage);
        }
        console.log("our_brokerage",$scope.agreement.our_brokerage);

        //$scope.agreement.gross_brokerage =  parseFloat($scope.agreement.total_brokerage) + parseFloat($scope.agreement.our_brokerage);
        
        if ($scope.agreement.service_tax_per>0)
        {
            $scope.agreement.service_tax = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.service_tax_per)/100)).toFixed(2);
        }
        console.log("service_tax",$scope.agreement.service_tax);
        if ($scope.agreement.cgst_per>0)
        {
            $scope.agreement.cgst = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.cgst_per)/100)).toFixed(2);
        }
        console.log("cgst",$scope.agreement.cgst);
        if ($scope.agreement.sgst_per>0)
        {
            $scope.agreement.sgst = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.sgst_per)/100)).toFixed(2);
        }
        console.log("sgst",$scope.agreement.sgst);
        if ($scope.agreement.tds_per>0)
        {
            $scope.agreement.tds = parseFloat($scope.agreement.total_brokerage * (($scope.agreement.tds_per)/100)).toFixed(2);
        }
        console.log("tds",$scope.agreement.tds);
        $scope.agreement.gross_brokerage =  parseFloat($scope.agreement.total_brokerage) +  parseFloat($scope.agreement.service_tax) + parseFloat($scope.agreement.cgst) + parseFloat($scope.agreement.sgst) - parseFloat($scope.agreement.tds);

    }
   
    $scope.contributions_update = function (contributionsdata) {
        Data.post('contributions_update', {
            contributionsdata: contributionsdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('contributions');
            }
        });
    };
    
    $scope.contribution_delete = function (contributionsdata) {
        var deletecontribution = confirm('Are you absolutely sure you want to delete?');
        if (deletecontribution) {
            Data.post('contribution_delete', {
                contributionsdata: contributionsdata
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('contributions');
                }
            });
        }
    };
    
});


// COLLECTIONS

app.controller('Collections_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {
       
    $scope.parseFloat = function(value)
    {
        if (value)
        {
            return parseFloat(value);
        }
        else
        {
            return 0;
        }
    }

    $scope.searchdata = {};
    $scope.collections = {};
    $scope.collections_received = {};
    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
    $scope.regular_list = "Yes";
    $scope.pagenavigation = function(which_side)
    {
        $scope.collections = {};
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
            Data.get('collections_ctrl/'+$scope.next_page_id).then(function (results) {
                $scope.collections = results;
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
            
            $scope.search_collections($scope.searchdata,'pagenavigation');
            
        }
    }

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("collections_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("collections_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("collections_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("collections_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.view_rights)
    {
        alert("You don't have rights to use this option..");
        return;
    }
    console.log($scope.next_page_id);
    $timeout(function () { 
        Data.get('collections_ctrl/'+$scope.next_page_id).then(function (results) {
            $scope.collections = results;
            $scope.next_page_id = 30;
            $scope.collection_count = results[0].collection_count;
            $scope.total_records = results[0].collection_count;
        });
    }, 100);

    $timeout(function () { 
        Data.get('collections_ctrl_received/'+$scope.next_page_id).then(function (results) {
            $scope.collections_received = results;
            $scope.collection_received_count = results[0].collection_count;
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

    $scope.create_invoice = function(payments_id, invoice_no,invoice_date)
    {
        
        console.log(invoice_date);
        if (invoice_date=='00-00-0000')
        {
            var currentdate = new Date();
            dd =  currentdate.getDate();
            dd = "01";
            mm =  (currentdate.getMonth()+1);
            if (mm<10)
            {
                mm = "0"+mm;
            }
            yy = currentdate.getFullYear();
            var invoice_date = dd + "/" + mm + "/" + yy ;
        }
        
        console.log(payments_id);
        console.log(invoice_date);
        $("#create_invoice").css("display","block");
        $scope.invoice_no = invoice_no;
        $scope.invoice_date = invoice_date;
        $scope.payments_id = payments_id;
        
    }

    $scope.close_create_invoice = function()
    {
        $scope.invoice_no = "";
        $("#create_invoice").css("display","none");
    }

    $scope.show_invoice = function(invoice_no,invoice_date)
    {
        console.log(invoice_no);
        console.log(invoice_date);


        tinvoice_date = invoice_date.substr(6,4)+"-"+invoice_date.substr(3,2)+"-"+invoice_date.substr(0,2);
        Data.get('show_invoice/'+$scope.payments_id+'/'+invoice_no+'/'+tinvoice_date).then(function (results) {
            invoice_filename = results[0].invoice_filename;
            console.log(invoice_filename);
            $scope.html = '<embed src="https://crm.rdbrothers.com//api//v1//uploads//'+invoice_filename +'" style="width:875px;height:1800px;" type="application/pdf">';
            $scope.trustedHtml_show_pdf = $sce.trustAsHtml($scope.html);

            $scope.invoice_no = "";
            $("#create_invoice").css("display","none");
            $("#show_pdf").modal("show");
    
        });
    
        $scope.close_pdf = function ()
        {
            $("#show_pdf").modal("hide");
        }
    }



    var values_loaded = "false";
    $scope.open_search = function()
    {
        if (values_loaded=="false")
        {
            values_loaded="true";
            console.log("opening");

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
                Data.get('selectcontact/Developer').then(function (results) {
                    $scope.developers = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('selectusers').then(function (results) {
                    $scope.users = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectteams').then(function (results) {
                    $scope.teams = results;
                });
            }, 100);            

            $timeout(function () { 
                Data.get('selectenquiry_with_broker').then(function (results) {
                    $scope.enquiries = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('getdatavalues_collections/property_id').then(function (results) {
                    $scope.property_ids = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_collections/project_id').then(function (results) {
                    $scope.project_ids = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_collections/enquiry_id').then(function (results) {
                    $scope.enquiry_ids = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_collections/agreement_id').then(function (results) {
                    $scope.agreement_ids = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_collections/payments_id').then(function (results) {
                    $scope.payment_ids = results;
                });
            }, 100);
        }
    };

    $scope.search_collections = function (searchdata,from_click) 
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
        Data.post('search_collections', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.collections, function() {
                if (results[0].collection_count>0)
                {
                    $scope.collections = {};
                    $scope.collections = results;
                    $scope.collection_count = results[0].collection_count;
                    $scope.total_records = results[0].collection_count;
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
        
        Data.get('collections_ctrl/0').then(function (results) {
            $scope.collections = results;
            $scope.next_page_id = 30;
            $scope.collection_count = results[0].collection_count;
            $scope.total_records = results[0].collection_count;
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
                Data.get('getreport_fields/collections/unselected').then(function (results) {
                    $scope.unselected_fields = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getreport_fields/collections/selected').then(function (results) {
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
                Data.get('getreport_fields/collections/unselected').then(function (results) {
                    $rootScope.unselected_fields = results;
                });
            }, 100);
    
            $timeout(function () { 
                Data.get('getreport_fields/collections/selected').then(function (results) {
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
            Data.get('exportdata/collections/payment_id/'+data+'/'+$scope.option_value).then(function (results) {
                window.location="api//v1//uploads//collections_list.xlsx";
            });
        }, 100);
      }
});


// attendance

app.controller('Attendance_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ,$anchorScroll) {
       
    $scope.parseFloat = function(value)
    {
        if (value)
        {
            return parseFloat(value);
        }
        else
        {
            return 0;
        }
    }
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("attendance_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("attendance_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("attendance_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("attendance_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    $scope.view_rights = true;
    if (!$scope.view_rights)
    {
        alert("You don't have rights to use this option..");
        return;
    }
    $scope.attendancedata = {};
    var currentdate = new Date();
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;

    $scope.$watch($scope.attendancedata.start_date, function() {
        $scope.attendancedata.start_date = start_date;
    }, true);

    $scope.$watch($scope.attendancedata.end_date, function() {
        $scope.attendancedata.end_date = end_date;
    }, true);
    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
        $scope.listusers = results;
        });
    }, 100);
    $scope.selected_user = 0;
    $scope.getattendance = function(attendancedata)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if (attendancedata.start_date)
        {
            start_date = attendancedata.start_date.substr(6,4)+"-"+attendancedata.start_date.substr(3,2)+"-"+attendancedata.start_date.substr(0,2);         
        }
        if (attendancedata.end_date)
        {
            end_date = attendancedata.end_date.substr(6,4)+"-"+attendancedata.end_date.substr(3,2)+"-"+attendancedata.end_date.substr(0,2);         
        }
        $("#attendance_div").css("display","block");
        $("#attendance_details").css("display","none");
        $scope.selected_user = attendancedata.user_id;
        Data.get('attendance_ctrl/'+start_date+'/'+end_date+'/'+attendancedata.user_id).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.attendanceHtml = $sce.trustAsHtml($scope.html);
            //$scope.attendancelist = results;
        });
    };

    $scope.show_operting = function(log_date)
    {
        console.log(log_date);
        Data.get('show_operating/'+log_date+'/'+$scope.selected_user).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.attendance_details_Html = $sce.trustAsHtml($scope.html);
            $("#attendance_details").css("display","block");
            $("#attendance_div").css("display","none");
            document.documentElement.scrollTop = 0;
            /*$timeout(function() {
                $location.hash('attendance_details');
                $anchorScroll();
            })*/
            //$scope.attendancelist = results;
        });
    }
    $scope.back = function()
    {
        $("#attendance_details").css("display","none");
        $("#attendance_div").css("display","block");
    }
});


// attendance

app.controller('DailyAttendance_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {
       
    $scope.parseFloat = function(value)
    {
        if (value)
        {
            return parseFloat(value);
        }
        else
        {
            return 0;
        }
    }
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("attendance_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("attendance_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("attendance_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("attendance_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    $scope.view_rights = true;
    if (!$scope.view_rights)
    {
        alert("You don't have rights to use this option..");
        return;
    }
    $scope.attendancedata = {};
    var currentdate = new Date();
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;

    $scope.$watch($scope.attendancedata.start_date, function() {
        $scope.attendancedata.start_date = start_date;
    }, true);

    $scope.$watch($scope.attendancedata.end_date, function() {
        $scope.attendancedata.end_date = end_date;
    }, true);
    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
        $scope.listusers = results;
        });
    }, 100);
    $scope.getdailyattendance = function(attendancedata)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if (attendancedata.start_date)
        {
            start_date = attendancedata.start_date.substr(6,4)+"-"+attendancedata.start_date.substr(3,2)+"-"+attendancedata.start_date.substr(0,2);         
        }
        if (attendancedata.end_date)
        {
            end_date = attendancedata.end_date.substr(6,4)+"-"+attendancedata.end_date.substr(3,2)+"-"+attendancedata.end_date.substr(0,2);         
        }
        
        Data.get('dailyattendance_ctrl/'+start_date+'/'+end_date).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.attendanceHtml = $sce.trustAsHtml($scope.html);
        });
    };

    $scope.show_operting = function(log_date)
    {
        console.log(log_date);
    }
});


app.controller('Holidays_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {
       
    $scope.parseFloat = function(value)
    {
        if (value)
        {
            return parseFloat(value);
        }
        else
        {
            return 0;
        }
    }
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("holidays_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("holidays_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("holidays_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("holidays_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    $scope.view_rights = true;
    if (!$scope.view_rights)
    {
        alert("You don't have rights to use this option..");
        return;
    }
    $timeout(function () { 
        Data.get('holidays_ctrl').then(function (results) {
            $scope.holidaylist = results;
        });
    }, 100);

    
});


// CONTRIBUTIONS

app.controller('Contributions_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
       
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("contributions_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("contributions_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("contributions_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("contributions_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.view_rights = true;
    if (!$scope.view_rights)
    {
        //alert("You don't have rights to use this option..");
        //return;
    }
    $timeout(function () { 
        Data.get('selectdropdowns/PAYMENT_TYPE').then(function (results) {
            $scope.payment_typelist = results;
        });
    }, 100);
    $scope.contributions = {};
    $timeout(function () { 
        Data.get('contributions_ctrl').then(function (results) {
            $scope.contributions = results;
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
                Data.get('selectusers').then(function (results) {
                    $scope.users = results;
                });
            }, 100);
            
            $timeout(function () { 
                Data.get('selectteams').then(function (results) {
                    $scope.teams = results;
                });
            }, 100);  
            
            $timeout(function () { 
                Data.get('getdatavalues_contributions/voucher_id').then(function (results) {
                    $scope.voucher_ids = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('getdatavalues_contributions/agreement_id').then(function (results) {
                    $scope.agreement_ids = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_contributions/agreement_details_id').then(function (results) {
                    $scope.contributions_ids = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('selectcontact/Client').then(function (results) {
                    $scope.clients = results;
                });
            }, 100);

        }
    };

    $scope.search_contributions = function (searchdata,from_click) 
    {
        Data.post('search_contributions', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.contributions, function() {
                $scope.contributions = {};
                $scope.contributions = results;
            },true);
        });
    };

    $scope.resetForm = function()
    {
        
        $scope.searchdata = {};
        $scope.$watch($scope.searchdata, function() {
            $scope.searchdata = {
            }
        });
        $("li.select2-selection__choice").remove();
        $(".select2").each(function() { $(this).val([]); });
        
        Data.get('contributions_ctrl').then(function (results) {
            $scope.contributions = results;            
        });
    }

    $scope.select_assign_to = function(teams)
    {
        console.log(teams);
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/0').then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }
});



// ACCOUNTS

app.controller('Account_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;  
    $scope.accountlist = {};  
    $str = ($("#permission_string").val());
    if ((($str).indexOf("account_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("account_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("account_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("account_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.view_rights)
    {
        $scope.accountlist = {};
        alert("You don't have rights to use this option..");
        return;
    }
    
    $timeout(function () { 
        Data.get('account_list_ctrl').then(function (results) {
            $scope.accountlist = results;
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
                Data.get('selectusers').then(function (results) {
                    $scope.users = results;
                });
            }, 100);
            
            $timeout(function () { 
                Data.get('selectteams').then(function (results) {
                    $scope.teams = results;
                });
            }, 100);  
            
            $timeout(function () { 
                Data.get('getdatavalues_account/account_id').then(function (results) {
                    $scope.account_ids = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('getdatavalues_account/agreement_id').then(function (results) {
                    $scope.agreement_ids = results;
                });
            }, 100); 

            $timeout(function () { 
                Data.get('getdatavalues_account/payments_id').then(function (results) {
                    $scope.payment_ids = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('selectcontact/Client').then(function (results) {
                    $scope.clients = results;
                });
            }, 100);

        }
    };

    $scope.search_account = function (searchdata,from_click) 
    {
        Data.post('search_account', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.accountlist, function() {
                $scope.accountlist = {};
                $scope.accountlist = results;
            },true);
        });
    };

    $scope.resetForm = function()
    {
        
        $scope.searchdata = {};
        $scope.$watch($scope.searchdata, function() {
            $scope.searchdata = {
            }
        });
        $("li.select2-selection__choice").remove();
        $(".select2").each(function() { $(this).val([]); });
        
        Data.get('account_list_ctrl').then(function (results) {
            $scope.accountlist = results;            
        });
    }

    $scope.select_assign_to = function(teams)
    {
        console.log(teams);
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/0').then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }
});

    
    
app.controller('Account_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {
    $scope.accountdata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("account_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("account_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("account_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("account_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.create_rights)
    {
        $scope.accountdata = {};
        alert("You don't have rights to use this option..");
        return;
    }

    $timeout(function () { 
        Data.get('selectagreement').then(function (results) {
            $scope.agreements = results;
        });
    }, 100);

    $scope.getpaymentids = function(agreement_id)
    {
        $timeout(function () { 
            Data.get('getpaymentids/'+agreement_id).then(function (results) {
                $scope.payments = results;
            });
        }, 100);

        $timeout(function () { 
            Data.get('getassigned_agreement/'+agreement_id).then(function (results) {
                $scope.arr = ((results[0].assign_to).split(','));
                results[0].assign_to = $scope.arr;
                $scope.arr = ((results[0].teams).split(','));
                results[0].teams = $scope.arr;

                $scope.$watch($scope.accountdata.assign_to, function() {
                    $scope.accountdata.assign_to = results[0].assign_to;
                    $scope.accountdata.assign_to = results[0].assign_to;
                    console.log($scope.accountdata.assign_to);
                },true);

                $scope.$watch($scope.accountdata.teams, function() {
                    $scope.accountdata.teams = results[0].teams;
                    $scope.accountdata.teams = results[0].teams;
                    console.log($scope.accountdata.teams);
                },true)
                $(".select2").select2();
            });
        }, true);
        $(".select2").select2();

    }

    $timeout(function () { 
        Data.get('selectpayments').then(function (results) {
            $scope.payments = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectcontact/all').then(function (results) {
            $scope.clients = results;
        });
    }, 100);

    $scope.getclientids = function(payments_id)
    {
        Data.get('getclientids/'+payments_id).then(function (results) {
            console.log(results[0].contact_id);
            console.log(results[0].contact_off);
            //Data.get('selectcontact/'+results[0].contact_off).then(function (results_1) {
            //    $scope.clients = results_1;
                $scope.$watch($scope.accountdata.client_id, function() {
                    $scope.accountdata.client_id = results[0].contact_id;
                    console.log($scope.accountdata.client_id);
                },true);
            //});
            
        });

        /*$timeout(function () { 
            Data.get('getaccount_balance/'+payments_id).then(function (results) {
                $scope.account_balance = results[0].contact_id;
            });
        }, 100);*/

        $timeout(function () { 
            Data.get('getaccountdetails/'+payments_id).then(function (results) {

                $scope.$watch($scope.accountdata.amount, function() {
                    $scope.accountdata.amount = results[0].brokerage;
                    console.log($scope.accountdata.amount);
                },true);
                
            });

        }, 100);
    }

    $timeout(function () { 
        Data.get('selectdropdowns/ADJUSTMENT_TYPE').then(function (results) {
            $scope.adjustment_typelist = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/PAYMENT_TYPE').then(function (results) {
            $scope.payment_typelist = results;
        });
    }, 100);

    
    $timeout(function () { 
        Data.get('selectdropdowns/CHEQUE_STATUS').then(function (results) {
            $scope.cheque_statuslist = results;
        });
    }, 100);

    


    $scope.account_add_new = function (accountdata) {
        Data.post('account_add_new', {
            accountdata: accountdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('account_list');
            }
        });
    };
});
    
app.controller('Account_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {
    var account_id = $routeParams.account_id;
    $scope.activePath = null;
    $scope.accountdata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("account_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("account_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("account_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("account_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.update_rights)
    {
        $scope.accountdata = {};
        alert("You don't have rights to use this option..");
        return;
    }    
    $timeout(function () { 
        Data.get('selectcontact/Client').then(function (results) {
            $scope.clients = results;
        });
    }, 100);
    Data.get('account_edit_ctrl/'+account_id).then(function (results) {
        $scope.accountdata={};
        $scope.arr = ((results[0].assign_to).split(','));
        results[0].assign_to = $scope.arr;
        $scope.arr = ((results[0].teams).split(','));
        results[0].teams = $scope.arr;
        $scope.arr1 = ((results[0].subteams).split(','));
        results[0].subteams = $scope.arr1;
        $scope.$watch($scope.accountdata, function() {
            $scope.accountdata = {};
            $scope.accountdata = {
                agreement_id:results[0].agreement_id,
                payments_id:results[0].payments_id,
                client_id:results[0].client_id,
                transaction_date:results[0].transaction_date,
                adjustment_type:results[0].adjustment_type,
                amount:results[0].amount,
                payment_type:results[0].payment_type,
                receipt_no:results[0].receipt_no,
                receipt_date:results[0].receipt_date,
                instrument_no:results[0].instrument_no,
                cheque_no:results[0].cheque_no,
                cheque_date:results[0].cheque_date,
                drawn_on:results[0].drawn_on,
                branch:results[0].branch,
                cheque_status:results[0].cheque_status,
                comments:results[0].comments,
                teams:results[0].teams,
                subteams:results[0].subteams,
                assign_to:results[0].assign_to,
                account_id:results[0].account_id
            }
        });
    });
    
    $timeout(function () { 
        Data.get('selectagreement').then(function (results) {
            $scope.agreements = results;
        });
    }, 100);

    $scope.getpaymentids = function(agreement_id)
    {
        $timeout(function () { 
            Data.get('getpaymentids/'+agreement_id).then(function (results) {
                $scope.payments = results;
            });
        }, 100);
    }

    $timeout(function () { 
        Data.get('selectpayments').then(function (results) {
            $scope.payments = results;
        });
    }, 100);

    $scope.getclientids = function(payments_id)
    {
        Data.get('getclientids/'+payments_id).then(function (results) {
            console.log(results[0].contact_id);
            console.log(results[0].contact_off);
            Data.get('selectcontact/'+results[0].contact_off).then(function (results_1) {
                $scope.clients = results_1;
                $scope.$watch($scope.accountdata.client_id, function() {
                    $scope.accountdata.client_id = results[0].contact_id;
                    console.log($scope.accountdata.client_id);
                },true);
            });
        });
    }

    $timeout(function () { 
        Data.get('selectdropdowns/ADJUSTMENT_TYPE').then(function (results) {
            $scope.adjustment_typelist = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/PAYMENT_TYPE').then(function (results) {
            $scope.payment_typelist = results;
        });
    }, 100);

    
    $timeout(function () { 
        Data.get('selectdropdowns/CHEQUE_STATUS').then(function (results) {
            $scope.cheque_statuslist = results;
        });
    }, 100);

    


    $scope.account_update = function (accountdata) {
        Data.post('account_update', {
            accountdata: accountdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('account_list');
            }
        });
    };
    
    $scope.account_delete = function (accountdata) {
        //console.log(business_unit);
        var deleteaccount = confirm('Are you absolutely sure you want to delete?');
        if (deleteaccount) {
            Data.post('account_delete', {
                accountdata: accountdata
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('account_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectAccount', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectaccount').then(function (results) {
            $rootScope.accounts = results;
        });
    }, 100);
});


// PAYMENTS

app.controller('Voucher_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $scope.vouchers = {};
    $str = ($("#permission_string").val());
    if ((($str).indexOf("Voucher_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("Voucher_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("Voucher_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("Voucher_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    //$scope.view_rights = true;
    //$scope.create_rights = true;
    if (!$scope.view_rights)
    {
        $scope.vouchers = {};
        alert("You don't have rights to use this option..");
        return;
    }
    
    $timeout(function () { 
        Data.get('voucher_list_ctrl').then(function (results) {
            $scope.voucherlist = results;
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
                Data.get('selectusers').then(function (results) {
                    $scope.users = results;
                });
            }, 100);
            
            $timeout(function () { 
                Data.get('selectteams').then(function (results) {
                    $scope.teams = results;
                });
            }, 100);  
            
            $timeout(function () { 
                Data.get('getdatavalues_voucher/voucher_id').then(function (results) {
                    $scope.voucher_ids = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('getdatavalues_voucher/agreement_id').then(function (results) {
                    $scope.agreement_ids = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_voucher/contributions_id').then(function (results) {
                    $scope.contributions_ids = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('selectcontact/Client').then(function (results) {
                    $scope.clients = results;
                });
            }, 100);

        }
    };

    $scope.search_voucher = function (searchdata,from_click) 
    {
        Data.post('search_voucher', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.voucherlist, function() {
                $scope.voucherlist = {};
                $scope.voucherlist = results;
            },true);
        });
    };

    $scope.resetForm = function()
    {
        
        $scope.searchdata = {};
        $scope.$watch($scope.searchdata, function() {
            $scope.searchdata = {
            }
        });
        $("li.select2-selection__choice").remove();
        $(".select2").each(function() { $(this).val([]); });
        
        Data.get('voucher_list_ctrl').then(function (results) {
            $scope.voucherlist = results;            
        });
    }

    $scope.select_assign_to = function(teams)
    {
        console.log(teams);
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/0').then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }
});
    
    
app.controller('Voucher_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {
    $scope.voucherdata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("Voucher_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("Voucher_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("Voucher_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("Voucher_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    
    if (!$scope.create_rights)
    {
        $scope.voucherdata = {};
        alert("You don't have rights to use this option..");
        return;
    }

    $timeout(function () { 
        Data.get('selectagreement').then(function (results) {
            $scope.agreements = results;
        });
    }, 100);

    $scope.getcontributionids = function(agreement_id)
    {
        $timeout(function () { 
            Data.get('getcontributionids/'+agreement_id).then(function (results) {
                $scope.contributions = results;
                $scope.arr = ((results[0].assign_to).split(','));
                results[0].assign_to = $scope.arr;
                $scope.arr = ((results[0].teams).split(','));
                results[0].teams = $scope.arr;
                
                $scope.$watch($scope.voucherdata.assign_to, function() {
                    $scope.voucherdata.assign_to = results[0].assign_to;
                    console.log($scope.voucherdata.assign_to);
                },true);
                
                $scope.$watch($scope.voucherdata.teams, function() {
                    $scope.voucherdata.teams = results[0].teams;
                    console.log($scope.voucherdata.teams);
                },true);
                
            });
        }, 100);
    }

    /*$timeout(function () { 
        Data.get('selectpayments').then(function (results) {
            $scope.payments = results;
        });
    }, 100);*/

    $scope.getemployeeids = function(contributions_id)
    {
        $timeout(function () { 
            Data.get('getemployeeids/'+contributions_id).then(function (results) {
                $scope.employees = results;
                console.log(results[0].user_id);
                $scope.$watch($scope.voucherdata.user_id, function() {
                    $scope.voucherdata.user_id = results[0].user_id;
                    console.log($scope.voucherdata.user_id);
                },true);
            });
        }, 100);
        $timeout(function () { 
            Data.get('getvoucheramount/'+contributions_id).then(function (results) {
                $scope.$watch($scope.voucherdata.amount, function() {
                    $scope.voucherdata.amount = results[0].contribution_amount;
                    console.log($scope.voucherdata.amount);
                },true);
            });
        }, 100);
    }

    $timeout(function () { 
        Data.get('selectdropdowns/ADJUSTMENT_TYPE').then(function (results) {
            $scope.adjustment_typelist = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/PAYMENT_TYPE').then(function (results) {
            $scope.payment_typelist = results;
        });
    }, 100);

    
    $timeout(function () { 
        Data.get('selectdropdowns/CHEQUE_STATUS').then(function (results) {
            $scope.cheque_statuslist = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectcontact/Client').then(function (results) {
            $scope.clients = results;
        });
    }, 100);


    $scope.voucher_add_new = function (voucherdata) {
        Data.post('voucher_add_new', {
            voucherdata: voucherdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('voucher_list');
            }
        });
    };
});
    
app.controller('Voucher_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {
    var voucher_id = $routeParams.voucher_id;
    $scope.activePath = null;
    $scope.voucherdata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("Voucher_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("Voucher_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("Voucher_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("Voucher_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    if (!$scope.update_rights)
    {
        $scope.voucherdata = {};
        alert("You don't have rights to use this option..");
        return;
    }    

    $timeout(function () { 
        Data.get('selectcontact/Client').then(function (results) {
            $scope.clients = results;
        });
    }, 100);

    Data.get('voucher_edit_ctrl/'+voucher_id).then(function (results) {
        $scope.accountdata={};
        $scope.arr = ((results[0].assign_to).split(','));
        results[0].assign_to = $scope.arr;
        $scope.arr = ((results[0].teams).split(','));
        results[0].teams = $scope.arr;
        $scope.arr1 = ((results[0].subteams).split(','));
        results[0].subteams = $scope.arr1;
        $scope.$watch($scope.voucherdata, function() {
            $scope.voucherdata = {};
            $scope.voucherdata = {

                /*
                    `voucher_id` int(11) NOT NULL,
                    `agreement_id` int(11) NOT NULL,
                    `contribution_id` int(11) NOT NULL,
                    `emp_id` int(11) NOT NULL,
                    `transaction_date` date NOT NULL,
                    `amount` decimal(12,2) NOT NULL,
                    `payment_type` varchar(20) NOT NULL,
                    `voucher_no` varchar(20) NOT NULL,
                    `voucher_date` date NOT NULL,
                    `instrument_no` varchar(50) NOT NULL,
                    `cheque_no` varchar(20) NOT NULL,
                    `cheque_date` date NOT NULL,
                    `drawn_on` varchar(200) NOT NULL,
                    `branch` varchar(200) NOT NULL,
                    `cheque_status` varchar(20) NOT NULL,
                    `comments` text NOT NULL,
                    `assign_to` varchar(250) NOT NULL,
                    `teams` varchar(200) NOT NULL,
                    `created_by` int(11) NOT NULL,
                    `created_date` datetime NOT NULL,
                    `modified_by` int(11) NOT NULL,
                    `modified_date` datetime NOT NULL
                  */
                  

                voucher_id:results[0].voucher_id,
                agreement_id:results[0].agreement_id,
                contribution_id:results[0].contribution_id,                
                emp_id:results[0].emp_id,
                transaction_date:results[0].transaction_date,
                amount:results[0].amount,
                payment_type:results[0].payment_type,
                voucher_no:results[0].voucher_no,
                voucher_date:results[0].voucher_date,
                instrument_no:results[0].instrument_no,
                cheque_no:results[0].cheque_no,
                cheque_date:results[0].cheque_date,
                drawn_on:results[0].drawn_on,
                branch:results[0].branch,
                cheque_status:results[0].cheque_status,
                comments:results[0].comments,
                teams:results[0].teams,
                subteams:results[0].subteams,
                assign_to:results[0].assign_to
            }
        });
    });
    
    $timeout(function () { 
        Data.get('selectagreement').then(function (results) {
            $scope.agreements = results;
        });
    }, 100);

    $scope.getcontributionids = function(agreement_details_id)
    {
        $timeout(function () { 
            Data.get('getcontributionids/'+agreement_details_id).then(function (results) {
                $scope.contributions = results;
            });
        }, 100);
    }
    $scope.getemployeeids = function(contributions_id)
    {
        $timeout(function () { 
            Data.get('getemployeeids/'+contributions_id).then(function (results) {
                $scope.employees = results;
                console.log(results[0].user_id);
                $scope.$watch($scope.voucherdata.user_id, function() {
                    $scope.voucherdata.user_id = results[0].user_id;
                    console.log($scope.voucherdata.user_id);
                },true);
            });
        }, 100);
        /*$timeout(function () { 
            Data.get('getvoucheramount/'+contributions_id).then(function (results) {
                $scope.$watch($scope.voucherdata.amount, function() {
                    $scope.voucherdata.amount = results[0].contribution_amount;
                    console.log($scope.voucherdata.amount);
                },true);
            });
        }, 100);*/
    }


    $timeout(function () { 
        Data.get('selectdropdowns/PAYMENT_TYPE').then(function (results) {
            $scope.payment_typelist = results;
        });
    }, 100);

    
    $timeout(function () { 
        Data.get('selectdropdowns/CHEQUE_STATUS').then(function (results) {
            $scope.cheque_statuslist = results;
        });
    }, 100);

    $scope.voucher_update = function (voucherdata) {
        Data.post('voucher_update', {
            voucherdata: voucherdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('voucher_list');
            }
        });
    };
    
    $scope.voucher_delete = function (voucherdata) {
        //console.log(business_unit);
        var deletevoucher = confirm('Are you absolutely sure you want to delete?');
        if (deletevoucher) {
            Data.post('voucher_delete', {
                voucherdata: voucherdata
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('voucher_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectVoucher', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectvoucher').then(function (results) {
            $scope.vouchers = results;
        });
    }, 100);
});

//pks start -------------------------------------14102023------------------------------------------------------
// EXPENSE HEAD

app.controller('Expense_Head_Sub_Type_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    //console.log("pks");
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $scope.expenses = {};
    $str = ($("#permission_string").val());
    if ((($str).indexOf("expense_head_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("expense_head_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("expense_head_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("expense_head_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.view_rights = true;
    $scope.create_rights = true;
    if (!$scope.view_rights)
    {
        $scope.expenses = {};
        alert("You don't have rights to use this option..");
        return;
    }
    
    $timeout(function () { 
        Data.get('Expense_Head_Sub_Type_List_Ctrl').then(function (results) {
            $scope.expense_heads = results;
        });
    }, 100);
});  
    
app.controller('Expense_Head_Sub_Type_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $window, $timeout) {
    $scope.expense_headdata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("expense_head_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("expense_head_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("expense_head_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("expense_head_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.create_rights = true;
    if (!$scope.create_rights)
    {
        $scope.expense_headdata = {};
        alert("You don't have rights to use this option..");
        return;
    }
    
    $timeout(function () { 
        Data.get('selectexpense_head').then(function (results) {
            $scope.expense_heads = results;
        });
    }, 100);

    $scope.expense_head_add_new_sub_type = function (expense_headdata) {
        Data.post('expense_head_add_new_sub_type', {
            expense_headdata: expense_headdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('expense_head_sub_type_list');
            }
        });
    };
});
    
app.controller('Expense_Head_Sub_Type_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var expense_head_sub_type_id = $routeParams.expense_head_sub_type_id;
    $scope.activePath = null;

    $scope.expense_headdata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("expense_head_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("expense_head_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("expense_head_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("expense_head_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.update_rights = true;
    if (!$scope.update_rights)
    {
        $scope.expense_headdata = {};
        alert("You don't have rights to use this option..");
        return;
    }    
    $timeout(function () { 
        Data.get('selectexpense_head').then(function (results) {
            $scope.expense_heads = results;
        });
    }, 100);

    Data.get('Expense_Head_Sub_Type_Edit_Ctrl/'+expense_head_sub_type_id).then(function (results) {
        $scope.expense_headdata={};
        console.log(results);
        $scope.$watch($scope.expense_headdata, function() {
            $scope.expense_headdata = {};
            $scope.expense_headdata = {
                expense_sub_type_id:results[0].expense_sub_type_id,
                expense_sub_title:results[0].expense_sub_title,
                expense_head_id:results[0].expense_head_id
            }
        });
    });  
    
    
    $scope.expense_head_update_sub_type = function (expense_headdata) {
        Data.post('expense_head_update_sub_type', {
            expense_headdata: expense_headdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('expense_head_sub_type_list');
            }
        });
    };
    
    $scope.expense_head_delete_sub_type = function (expense_headdata) {
        var deleteexpense_head = confirm('Are you absolutely sure you want to delete?');
        if (deleteexpense_head) {
            Data.post('expense_head_delete_sub_type', {
                expense_headdata: expense_headdata
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('expense_head_sub_type_list');
                }
            });
        }
    };
    
});


//------------------------------------------------------------------------------------------------------pks end--------------------------------------


app.controller('Expense_Head_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) 
{
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $scope.expenses = {};
    $str = ($("#permission_string").val());
    if ((($str).indexOf("expense_head_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("expense_head_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("expense_head_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("expense_head_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.view_rights = true;
    $scope.create_rights = true;
    if (!$scope.view_rights)
    {
        $scope.expenses = {};
        alert("You don't have rights to use this option..");
        return;
    }
    
    $timeout(function () { 
        Data.get('expense_head_list_ctrl').then(function (results) {
            $scope.expense_heads = results;
        });
    }, 100);
});  
    
app.controller('Expense_Head_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $window) 
{
    $scope.expense_headdata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("expense_head_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("expense_head_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("expense_head_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("expense_head_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.create_rights = true;
    if (!$scope.create_rights)
    {
        $scope.expense_headdata = {};
        alert("You don't have rights to use this option..");
        return;
    }

    $scope.expense_head_add_new = {expense_title:''};
    $scope.expense_head_add_new = function (expense_headdata) {
        Data.post('expense_head_add_new', {
            expense_headdata: expense_headdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                
                $location.path('expense_head_list');
            }
        });
    };
});
    
app.controller('Expense_Head_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var expense_head_id = $routeParams.expense_head_id;
    $scope.activePath = null;

    $scope.expense_headdata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("expense_head_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("expense_head_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("expense_head_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("expense_head_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.update_rights = true;
    if (!$scope.update_rights)
    {
        $scope.expense_headdata = {};
        alert("You don't have rights to use this option..");
        return;
    }    

    Data.get('expense_head_edit_ctrl/'+expense_head_id).then(function (results) {
        $scope.expense_headdata={};
        $scope.$watch($scope.expense_headdata, function() {
            $scope.expense_headdata = {};
            $scope.expense_headdata = {
                expense_head_id:results[0].expense_head_id,
                expense_title:results[0].expense_title
            }
        });
    });  
    
    
    $scope.expense_head_update = function (expense_headdata) {
        Data.post('expense_head_update', {
            expense_headdata: expense_headdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('expense_head_list');
            }
        });
    };
    
    $scope.expense_head_delete = function (expense_headdata) {
        var deleteexpense_head = confirm('Are you absolutely sure you want to delete?');
        if (deleteexpense_head) {
            Data.post('expense_head_delete', {
                expense_headdata: expense_headdata
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('expense_head_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectExpense_Head', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectexpense_head').then(function (results) {
            $scope.expense_heads = results;
        });
    }, 100);
});

// EMPLOYEE ASSET

app.controller('Employee_Asset_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) 
{
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $scope.expenses = {};
    $str = ($("#permission_string").val());
    if ((($str).indexOf("employee_asset_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("employee_asset_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("employee_asset_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("employee_asset_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    
    if (!$scope.view_rights)
    {
        $scope.expenses = {};
        alert("You don't have rights to use this option..");
        return;
    }
    
    $timeout(function () { 
        Data.get('employee_asset_list_ctrl').then(function (results) {
            $scope.employee_assets = results;
        });
    }, 100);
});  
    
app.controller('Employee_Asset_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $window, $timeout) 
{
    $scope.employee_assetdata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("employee_asset_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("employee_asset_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("employee_asset_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("employee_asset_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    
    if (!$scope.create_rights)
    {
        $scope.employee_assetdata = {};
        alert("You don't have rights to use this option..");
        return;
    }

    var currentdate = new Date();
    dd =  currentdate.getDate();
    mm =  (currentdate.getMonth()+1);
    yy = currentdate.getFullYear();
    today_date = dd+'/'+mm+'/'+yy;

    $scope.$watch($scope.employee_assetdata.issued_date, function() {
        $scope.employee_assetdata.issued_date = today_date;
        console.log($scope.employee_assetdata.issued_date);
    }, true);

    $timeout(function () { 
        Data.get('selectemployee').then(function (results) {
            $scope.employees = results;
        });
    }, 100);

    
    $scope.employee_asset_add_new = function (employee_assetdata) {
        Data.post('employee_asset_add_new', {
            employee_assetdata: employee_assetdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                
                $location.path('employee_asset_list');
            }
        });
    };
});
    
app.controller('Employee_Asset_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {
    var employee_asset_id = $routeParams.employee_asset_id;
    $scope.activePath = null;

    $scope.employee_assetdata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("employee_asset_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("employee_asset_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("employee_asset_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("employee_asset_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
   
    if (!$scope.update_rights)
    {
        $scope.employee_assetdata = {};
        alert("You don't have rights to use this option..");
        return;
    }    
    $timeout(function () { 
        Data.get('selectemployee').then(function (results) {
            $scope.employees = results;
        });
    }, 100);
    Data.get('employee_asset_edit_ctrl/'+employee_asset_id).then(function (results) {
        $scope.employee_assetdata={};
        $scope.$watch($scope.employee_assetdata, function() {
            $scope.employee_assetdata = {};
            $scope.employee_assetdata = {
                emp_id:results[0].emp_id,
                issued_date:results[0].issued_date,
                asset_title:results[0].asset_title,
                asset_spec:results[0].asset_spec,
                employee_asset_id:results[0].employee_asset_id
            }
        });
    });  
    
    
    $scope.employee_asset_update = function (employee_assetdata) {
        Data.post('employee_asset_update', {
            employee_assetdata: employee_assetdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('employee_asset_list');
            }
        });
    };
    
    $scope.employee_asset_delete = function (employee_assetdata) {
        var deleteemployee_asset = confirm('Are you absolutely sure you want to delete?');
        if (deleteemployee_asset) {
            Data.post('employee_asset_delete', {
                employee_assetdata: employee_assetdata
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('employee_asset_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectEmployee_Asset', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectemployee_asset').then(function (results) {
            $scope.employee_assets = results;
        });
    }, 100);
});

// EMPLOYEE LEAVE

app.controller('Employee_Leave_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) 
{
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $scope.expenses = {};
    $str = ($("#permission_string").val());
    if ((($str).indexOf("employee_leave_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("employee_leave_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("employee_leave_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("employee_leave_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.view_rights = true;
    $scope.create_rights = true;
    if (!$scope.view_rights)
    {
        $scope.expenses = {};
        alert("You don't have rights to use this option..");
        return;
    }
    
    $timeout(function () { 
        Data.get('employee_leave_list_ctrl').then(function (results) {
            $scope.employee_leaves = results;
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
                Data.get('getdatavalues_leaves/employee_name').then(function (results) {
                    $scope.employee_names = results;
                });
            }, 100);
            

        }
    };

    $scope.search_leaves = function (searchdata) 
    {
        Data.post('search_leaves', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.employee_leaves, function() {
                $scope.employee_leaves = {};
                $scope.employee_leaves = results;
                
            },true);
        });
    };

    $scope.resetForm = function()
    {
        
        $scope.searchdata = {};
        $scope.$watch($scope.searchdata, function() {
            $scope.searchdata = {
            }
        });
        $("li.select2-selection__choice").remove();
        $(".select2").each(function() { $(this).val([]); });
        
        Data.get('employee_leave_list_ctrl').then(function (results) {
            $scope.employee_leaves = results;
        });
    }

});  
    
app.controller('Employee_Leave_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $window ,$timeout) 
{
    $scope.employee_leavedata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("employee_leave_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("employee_leave_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("employee_leave_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("employee_leave_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.create_rights = true;
    if (!$scope.create_rights)
    {
        $scope.employee_leavedata = {};
        alert("You don't have rights to use this option..");
        return;
    }
    var currentdate = new Date();
    dd =  currentdate.getDate();
    // mm =  (currentdate.getMonth()+1);
    mm =  String(currentdate.getMonth()+1).padStart(2, "0");
    yy = currentdate.getFullYear();
    today_date = dd+'/'+mm+'/'+yy;

    $scope.$watch($scope.employee_leavedata.leave_date_from, function() {
        $scope.employee_leavedata.leave_date_from = today_date;
        console.log($scope.employee_leavedata.leave_date_from);
    }, true);

    $scope.$watch($scope.employee_leavedata.leave_date_to, function() {
        $scope.employee_leavedata.leave_date_to = today_date;
        console.log($scope.employee_leavedata.leave_date_to);
    }, true);

    $scope.$watch($scope.employee_leavedata.days, function() {
        $scope.employee_leavedata.days = 1;
        console.log($scope.employee_leavedata.days);
    }, true);

    $timeout(function () { 
        Data.get('selectemployee').then(function (results) {
            $scope.employees = results;
        });
    }, 100);
    
    $scope.employee_leave_add_new = function (employee_leavedata) {
        Data.post('employee_leave_add_new', {
            employee_leavedata: employee_leavedata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                
                $location.path('employee_leave_list');
            }
        });
    };
});
    
app.controller('Employee_Leave_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data ,$timeout) {
    var employee_leave_id = $routeParams.employee_leave_id;
    $scope.activePath = null;

    $scope.employee_leavedata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("employee_leave_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("employee_leave_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("employee_leave_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("employee_leave_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.update_rights = true;
    if (!$scope.update_rights)
    {
        $scope.employee_leavedata = {};
        alert("You don't have rights to use this option..");
        return;
    }    
    $timeout(function () { 
        Data.get('selectemployee').then(function (results) {
            $scope.employees = results;
        });
    }, 100);
    Data.get('employee_leave_edit_ctrl/'+employee_leave_id).then(function (results) {
        $scope.employee_leavedata={};
        $scope.$watch($scope.employee_leavedata, function() {
            $scope.employee_leavedata = {};
            $scope.employee_leavedata = {
                employee_leave_id:results[0].employee_leave_id,
                emp_id:results[0].emp_id,
                leave_date_from:results[0].leave_date_from,
                leave_date_to:results[0].leave_date_to,
                days:results[0].days,
                leave_reason:results[0].leave_reason,
                leave_status:results[0].leave_status
            }
        });
    });  
    
    
    $scope.employee_leave_update = function (employee_leavedata) {
        Data.post('employee_leave_update', {
            employee_leavedata: employee_leavedata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('employee_leave_list');
            }
        });
    };
    
    $scope.employee_leave_delete = function (employee_leavedata) {
        var deleteemployee_leave = confirm('Are you absolutely sure you want to delete?');
        if (deleteemployee_leave) {
            Data.post('employee_leave_delete', {
                employee_leavedata: employee_leavedata
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('employee_leave_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectEmployee_Leave', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectemployee_leave').then(function (results) {
            $scope.employee_leaves = results;
        });
    }, 100);
});


// EXPENSES

app.controller('Expense_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $scope.expenselist = {};
    $str = ($("#permission_string").val());
    if ((($str).indexOf("expense_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("expense_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("expense_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("expense_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.view_rights = true;
    $scope.create_rights = true;
    if (!$scope.view_rights)
    {
        $scope.expenselist = {};
        alert("You don't have rights to use this option..");
        return;
    }
    
    $timeout(function () { 
        Data.get('expense_list_ctrl').then(function (results) {
            $scope.expenselist = results;
        });
    }, 100);
    
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
            $location.path('batch_update/expenses/0/0/'+data);
        }
    }
    
    $scope.select_unselect = function(isChecked)
    {
        console.log(11);
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

    var values_loaded = "false";
    $scope.open_search = function()
    {
        if (values_loaded=="false")
        {
            values_loaded="true";
            console.log("opening");

            $timeout(function () { 
                Data.get('selectusers').then(function (results) {
                    $scope.employees = results;
                });
            }, 100);
        
            $timeout(function () { 
                Data.get('selectexpense_head').then(function (results) {
                    $scope.expense_heads = results;
                });
            }, 100);
            
            $timeout(function () { 
                Data.get('selectteams').then(function (results) {
                    $scope.teams = results;
                });
            }, 100);            

            $timeout(function () { 
                Data.get('getdatavalues_expenses/payments_id').then(function (results) {
                    $scope.payment_ids = results;
                });
            }, 100);
        }
    };

    $scope.search_expenses = function (searchdata,from_click) 
    {
        Data.post('search_expenses', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.expenselist, function() {
                $scope.expenselist = {};
                $scope.expenselist = results;
            },true);
        });
    };

    $scope.resetForm = function()
    {
        
        $scope.searchdata = {};
        $scope.$watch($scope.searchdata, function() {
            $scope.searchdata = {
            }
        });
        $("li.select2-selection__choice").remove();
        $(".select2").each(function() { $(this).val([]); });
        
        Data.get('expense_list_ctrl').then(function (results) {
            $scope.expenselist = results;            
        });
    }

});
    
    
app.controller('Expense_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {
    $scope.expensedata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("account_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("account_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("account_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("account_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.create_rights = true;
    if (!$scope.create_rights)
    {
        $scope.expensedata = {};
        alert("You don't have rights to use this option..");
        return;
    }
    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.employees = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectexpense_head').then(function (results) {
            $scope.expense_heads = results;
        });
    }, 100);

    $scope.select_expense_sub_type = function(expense_head)
    {
        $timeout(function () {
            Data.get('select_expense_sub_types/'+expense_head).then(function (results) {
                $scope.$watch($scope.expense_sub_types, function() {
                    $scope.expense_sub_types = results;
                },true);
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
            $scope.sub_teams_list = results;
        });
    }, 100);

    $scope.selectteamfromid = function(user_id)
    {
        $timeout(function () { 
            Data.get('selectteamfromid/'+user_id).then(function (results) {
                $scope.$watch($scope.expensedata.teams, function() {
                    console.log(results[0].teams);
                    $scope.expensedata.teams = results[0].teams;
                    console.log($scope.expensedata.teams);
                },true);
            });
        }, 100);
    }

    $scope.expense_add_new = function (expensedata) {
        var count = $("#file_documents").fileinput("getFilesCount");
        console.log(count);
        if (count>0)
        {
        }
        else
        {
            alert("Minimum 1 Image Required...!!!!");
            return;
        }
        Data.post('expense_add_new', {
            expensedata: expensedata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_documents').fileinput('upload');
                $location.path('expense_list');
            }
        });
    };
});
    
app.controller('Expense_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {
    var expense_id = $routeParams.expense_id;
    $scope.activePath = null;
    $scope.expensedata = {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("expense_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("expense_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("expense_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("expense_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.update_rights = true;
    if (!$scope.update_rights)
    {
        $scope.expensedata = {};
        alert("You don't have rights to use this option..");
        return;
    }    
    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.employees = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectexpense_head').then(function (results) {
            $scope.expense_heads = results;
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

    $scope.selectteamfromid = function(user_id)
    {
        $timeout(function () { 
            Data.get('selectteamfromid/'+user_id).then(function (results) {
                $scope.$watch($scope.expensedata.teams, function() {
                    console.log(results[0].teams);
                    $scope.expensedata.teams = results[0].teams;
                    console.log($scope.expensedata.teams);
                },true);
            });
        }, 100);
    }
    
    $scope.select_expense_sub_type = function(expense_head)
    {
        $timeout(function () {
            Data.get('select_expense_sub_types/'+expense_head).then(function (results) {
                console.log(results);
                $scope.$watch($scope.expense_sub_types, function() {
                    $scope.expense_sub_types = results;
                },true);
            });
        }, 100);
    } 
    
    
//pks changes edit expanse 18042023 add line 9811
    Data.get('expense_edit_ctrl/'+expense_id).then(function (results) {
        $scope.expensedata={};
        $scope.$watch($scope.expensedata, function() { 
            $scope.expensedata = {};
            $scope.expensedata = {
                expense_type:results[0].expense_type,
                expense_head_id:results[0].expense_head_id,
                expense_id:results[0].expense_id,
                user_id:results[0].user_id,
                amount:results[0].amount,
                expense_no:results[0].expense_no,
                expense_date:results[0].expense_date,
                comments:results[0].comments,
                teams:results[0].teams,
                sub_teams:results[0].sub_teams,
                payment_status:results[0].payment_status
            }
        });
    });

    Data.get('expense_documents/'+expense_id).then(function (results) {
        $scope.expense_documents = results;
    });

    $scope.removeimage = function (attachment_id) {
        var deleteemployee = confirm('Are you absolutely sure you want to delete?');
        if (deleteemployee) {
            Data.get('removeimage/'+attachment_id).then(function (results) {
                Data.toast(results);
                Data.get('expense_documents/'+expense_id).then(function (results) {
                    $scope.expense_documents = results;
                });
            });
        }
    };

    $scope.expense_update = function (expensedata) {
        Data.post('expense_update', {
            expensedata: expensedata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_documents').fileinput('upload');
                $location.path('expense_list');
            }
        });
    };
    
    $scope.expense_delete = function (expensedata) {
        //console.log(business_unit);
        var deleteexpense = confirm('Are you absolutely sure you want to delete?');
        if (deleteexpense) {
            Data.post('expense_delete', {
                expensedata: expensedata
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('expense_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectExpense', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectexpense').then(function (results) {
            $rootScope.expenses = results;
        });
    }, 100);
});

app.controller('Expense_Report', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $scope.expenses = {};
    $str = ($("#permission_string").val());
    if ((($str).indexOf("expense_report_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("expense_head_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("expense_head_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("expense_head_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    
    if (!$scope.view_rights)
    {
        $scope.expenses = {};
        alert("You don't have rights to use this option..");
        return;
    }

    var currentdate = new Date();
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    lmm = currentdate.toDateString().substr(4, 3);
    $scope.today_date = currentdate.getDate()+" "+lmm;

    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;
    console.log(start_date);
    console.log(end_date);

    $scope.employeedata = {};
    $scope.$watch($scope.employeedata.start_date, function() {
        $scope.employeedata.start_date = start_date;
        console.log($scope.employeedata.start_date);
    }, true);

    $scope.$watch($scope.employeedata.end_date, function() {
        $scope.employeedata.end_date = end_date;
        console.log($scope.employeedata.end_date);
    }, true);

    $timeout(function () { 
        Data.get('selectteams').then(function (results) {
            $scope.teams = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.listusers = results;
        });
    }, 100);
    $timeout(function () { 
        Data.get('selectexpense_head').then(function (results) {
            $scope.expense_heads = results;
        });
    }, 100);
    
    $scope.getexpense_report = function(employeedata)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        
        if ($scope.employeedata.start_date)
        {
           start_date = $scope.employeedata.start_date.substr(6,4)+"-"+$scope.employeedata.start_date.substr(3,2)+"-"+$scope.employeedata.start_date.substr(0,2);         
        }
        if ($scope.employeedata.end_date)
        {
           end_date = $scope.employeedata.end_date.substr(6,4)+"-"+$scope.employeedata.end_date.substr(3,2)+"-"+$scope.employeedata.end_date.substr(0,2);         
        }
        
        Data.get('expense_report/'+start_date+'/'+end_date+'/'+$scope.employeedata.expense_head_id+'/'+$scope.employeedata.user_id+'/'+$scope.employeedata.teams).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.employee_reportHtml = $sce.trustAsHtml($scope.html); 
        }); 
    }

    $scope.select_assign_to = function(teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/0').then(function (results) {
                $scope.listusers = results;
            });
        }, 100);
    }

});


// FRANCHAISEE

app.controller('Franchisee_Report', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{  
    $scope.activeTab = 0;
    console.log($scope.toggleTab);
    $scope.toggleTab = function(tabIndex) {
        $scope.activeTab = tabIndex;
    };
    
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $scope.expenses = {};
    $str = ($("#permission_string").val());
    if ((($str).indexOf("franchisee_report_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("franchisee_head_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("franchisee_head_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("franchisee_head_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    
    if (!$scope.view_rights)
    {
        $scope.expenses = {};
        alert("You don't have rights to use this option..");
        return;
    }

    var currentdate = new Date();
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    lmm = currentdate.toDateString().substr(4, 3);
    $scope.today_date = currentdate.getDate()+" "+lmm;

    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;
    console.log(start_date);
    console.log(end_date);
    $scope.employeedata = {};
    $scope.$watch($scope.employeedata.start_date, function() {
        $scope.employeedata.start_date = start_date;
        console.log($scope.employeedata.start_date);
    }, true);

    $scope.$watch($scope.employeedata.end_date, function() {
        $scope.employeedata.end_date = end_date;
        console.log($scope.employeedata.end_date);
    }, true);

    $scope.agentdata = {};
    $scope.$watch($scope.agentdata.start_date, function() {
        $scope.agentdata.start_date = start_date;
        console.log($scope.agentdata.start_date);
    }, true);

    $scope.$watch($scope.agentdata.end_date, function() {
        $scope.agentdata.end_date = end_date;
        console.log($scope.agentdata.end_date);
    }, true);

    $scope.franchiseedata = {};
    $scope.$watch($scope.franchiseedata.start_date, function() {
        $scope.franchiseedata.start_date = start_date;
        console.log($scope.franchiseedata.start_date);
    }, true);

    $scope.$watch($scope.franchiseedata.end_date, function() {
        $scope.franchiseedata.end_date = end_date;
        console.log($scope.franchiseedata.end_date);
    }, true);

    $scope.poweredbydata = {};
    $scope.$watch($scope.poweredbydata.start_date, function() {
        $scope.poweredbydata.start_date = start_date;
        console.log($scope.poweredbydata.start_date);
    }, true);

    $scope.$watch($scope.poweredbydata.end_date, function() {
        $scope.poweredbydata.end_date = end_date;
        console.log($scope.poweredbydata.end_date);
    }, true);


    $timeout(function () { 
        Data.get('selectteams').then(function (results) {
            $scope.teams = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectteams_team_type/All').then(function (results) {
            $scope.all_teams = results;
        });
    }, 100);
    $timeout(function () { 
        Data.get('selectteams_team_type/Branch').then(function (results) {
            $scope.branch_teams = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectteams_team_type/Franchisee').then(function (results) {
            $scope.franchisee_teams = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.listusers = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectusers_employee_type/Employee').then(function (results) {
            $scope.employee_listusers = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectusers_employee_type/Agent').then(function (results) {
            $scope.agent_listusers = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectusers_employee_type/Franchisee').then(function (results) {
            $scope.franchisee_listusers = results;
        });
    }, 100);

    // Fetch data for the default active tab	
    $timeout(function() {	
        $scope.toggleTab($scope.activeTab);	
    }, 100);

    
    
    $scope.getemployeedata_report = function(employeedata)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        
        if ($scope.employeedata.start_date)
        {
           start_date = $scope.employeedata.start_date.substr(6,4)+"-"+$scope.employeedata.start_date.substr(3,2)+"-"+$scope.employeedata.start_date.substr(0,2);         
        }
        if ($scope.employeedata.end_date)
        {
           end_date = $scope.employeedata.end_date.substr(6,4)+"-"+$scope.employeedata.end_date.substr(3,2)+"-"+$scope.employeedata.end_date.substr(0,2);         
        }
        if ($scope.employeedata.user_id)
        {

        }
        else
        {
            $scope.employeedata.user_id = 0;
        }
        if ($scope.employeedata.teams)
        {

        }
        else
        {
            $scope.employeedata.teams = 0;
        }
        if ($scope.employeedata.sub_teams)
        {

        }
        else
        {
            $scope.employeedata.sub_teams = 0;
        }
        Data.get('employeedata_report/'+start_date+'/'+end_date+'/'+$scope.employeedata.user_id+'/'+$scope.employeedata.teams+'/'+$scope.employeedata.sub_teams).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.employee_reportHtml = $sce.trustAsHtml($scope.html); 
        }); 
    }

    $scope.getagentdata_report = function(agentdata)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        
        if ($scope.agentdata.start_date)
        {
           start_date = $scope.agentdata.start_date.substr(6,4)+"-"+$scope.agentdata.start_date.substr(3,2)+"-"+$scope.agentdata.start_date.substr(0,2);         
        }
        if ($scope.agentdata.end_date)
        {
           end_date = $scope.agentdata.end_date.substr(6,4)+"-"+$scope.agentdata.end_date.substr(3,2)+"-"+$scope.agentdata.end_date.substr(0,2);         
        }
        if ($scope.agentdata.user_id)
        {

        }
        else
        {
            $scope.agentdata.user_id = 0;
        }
        if ($scope.agentdata.teams)
        {

        }
        else
        {
            $scope.agentdata.teams = 0;
        }
        if ($scope.agentdata.sub_teams)
        {

        }
        else
        {
            $scope.agentdata.sub_teams = 0;
        }
        //pks changes 17042023 add agent report($scope.agentdata.teams)
        Data.get('agentdata_report/'+start_date+'/'+end_date+'/'+$scope.agentdata.user_id+'/'+$scope.agentdata.teams+'/'+$scope.agentdata.sub_teams).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.agent_reportHtml = $sce.trustAsHtml($scope.html); 
        }); 
    }

    $scope.getfranchiseedata_report = function(franchiseedata)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        
        if ($scope.franchiseedata.start_date)
        {
           start_date = $scope.franchiseedata.start_date.substr(6,4)+"-"+$scope.franchiseedata.start_date.substr(3,2)+"-"+$scope.franchiseedata.start_date.substr(0,2);         
        }
        if ($scope.franchiseedata.end_date)
        {
           end_date = $scope.franchiseedata.end_date.substr(6,4)+"-"+$scope.franchiseedata.end_date.substr(3,2)+"-"+$scope.franchiseedata.end_date.substr(0,2);         
        }
        
        Data.get('franchiseedata_report/'+start_date+'/'+end_date+'/'+$scope.franchiseedata.teams+'/'+$scope.franchiseedata.sub_teams).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.franchisee_reportHtml = $sce.trustAsHtml($scope.html); 
        }); 
    }

    $scope.getpoweredbydata_report = function(poweredbydata)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        
        if ($scope.poweredbydata.start_date)
        {
           start_date = $scope.poweredbydata.start_date.substr(6,4)+"-"+$scope.poweredbydata.start_date.substr(3,2)+"-"+$scope.poweredbydata.start_date.substr(0,2);         
        }
        if ($scope.poweredbydata.end_date)
        {
           end_date = $scope.poweredbydata.end_date.substr(6,4)+"-"+$scope.poweredbydata.end_date.substr(3,2)+"-"+$scope.poweredbydata.end_date.substr(0,2);         
        }
        if ($scope.poweredbydata.teams)
        {

        }
        else
        {
            $scope.poweredbydata.teams = 0;
        }
        if ($scope.poweredbydata.sub_teams)
        {

        }
        else
        {
            $scope.poweredbydata.sub_teams = 0;
        }
        Data.get('poweredbydata_report/'+start_date+'/'+end_date+'/'+$scope.poweredbydata.teams+'/'+$scope.poweredbydata.sub_teams).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.poweredby_reportHtml = $sce.trustAsHtml($scope.html); 
        }); 
    }


    $scope.select_assign_to = function(teams,sub_teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/'+sub_teams).then(function (results) {
                $scope.employee_listusers = results;
            });
        }, 100);
    }

    $timeout(function () { 
        Data.get('selectsubteams').then(function (results) {
            $scope.sub_teams_list = results;
        });
    }, 100);

});

// ACTIVITY

app.controller('Activity_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {
    var cat = $routeParams.cat;
    $scope.cat = cat;
    var id = $routeParams.id;
    $scope.id = id;
    $scope.searchdata = {};
    $scope.listactivities = {};
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
            Data.get('activity_list_ctrl/'+$scope.cat+'/'+$scope.id+'/'+$scope.next_page_id).then(function (results) {
                $scope.listactivities = results;
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
            
            $scope.search_activities($scope.searchdata,'pagenavigation');
            
        }
    }
    $scope.convertdata= {};
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("activity_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("activity_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("activity_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("activity_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.view_rights)
    {
        $scope.listactivities = {};
        alert("You don't have rights to use this option..");
        return;
    }
    // $timeout(function () { 
    //     Data.get('activity_list_ctrl/'+$scope.cat+'/'+$scope.id+'/'+$scope.next_page_id).then(function (results) {
    //         // console.log($results);
    //         // alert($results);
    //         $scope.listactivities = results;
    //         $scope.next_page_id = 30;
    //         $scope.activity_count = results[0].activity_count;
    //         $scope.total_records = results[0].activity_count;
    //     });
    // }, 100);
    
    console.log($rootScope.user_id); 
    
    if ($rootScope.user_id!=0)
     {
         $timeout(function () { 
        Data.get('activity_list_ctrl/'+$scope.cat+'/'+$scope.id+'/'+$scope.next_page_id).then(function (results) {
            $scope.listactivities = results;
            $scope.next_page_id = 30;
            $scope.activity_count = results[0].activity_count;
            $scope.total_records = results[0].activity_count;
        });
        }, 100);
    }
    
    $scope.select_assign_to = function(teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/0').then(function (results) {
                $scope.users = results;
            });
        }, 100);
    }

    var values_loaded = "false";
    $scope.open_search = function()
    {
        if (values_loaded=="false")
        {
            values_loaded="true";
            console.log("opening");

            $timeout(function () { 
                Data.get('selectdropdowns/ACTIVITY_TYPE').then(function (results) {
                    $rootScope.factivity_type = results;
                    
                });
            }, 100);
            $timeout(function () { 
                Data.get('selectcontact/Client').then(function (results) {
                    $rootScope.clients = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectcontact/Broker').then(function (results) {
                    $rootScope.brokers = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectusers').then(function (results) {
                    $scope.users = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectteams').then(function (results) {
                    $scope.teams = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('selectcontact/Developer').then(function (results) {
                    $rootScope.developers = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectenquiry_with_broker').then(function (results) {
                    $scope.enquiries = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('getdatavalues_activity/property_id').then(function (results) {
                    $scope.property_ids = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_activity/project_id').then(function (results) {
                    $scope.project_ids = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_activity/enquiry_id').then(function (results) {
                    $scope.enquiry_ids = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_activity/activity_id').then(function (results) {
                    $scope.activity_ids = results;
                });
            }, 100);
        }
    };


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
            $location.path('audit_trail/activity/activity_id/'+data);
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
            $location.path('batch_update/activity/activity_id/'+data);
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
            $location.path('manage_group/activity/activity_id/'+data);
        }
    }
    
    $scope.option_value = "current_page";
    // ------------------------made by pks----------------------------
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
            Data.get('exportdata/activity/activity_id/'+data+'/'+$scope.option_value).then(function (results) {
                window.location="api//v1//uploads//activity_list.xlsx";
            });
        }, 100);
    }
    
    
    //made pks 09102023
    $scope.import_activities = function () 
    {         
        $("#activities").css("display","none");
        $("#upload").css("display","block");
    }
    
    $scope.uploadactivity_data = function (convertdata) {
        // console.log(convertdata);
        var currentdate = new Date(); 
        var datetime = currentdate.getFullYear()+ "-" + (currentdate.getMonth()+1) + "-" +  currentdate.getDate()+ " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
        convertdata.created_date = datetime;
        convertdata.file_name = $("#file_name").val();
        Data.post('uploadactivity', {
            convertdata: convertdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                console.log("inserted");
                window.location.href = '/activity_list/direct/0';
                window.location.reload();
            }
        });
    };

    $scope.change_created_date = function(activity_id,change_created_date)
    {
        console.log(activity_id);
        console.log(change_created_date);
    }

    $scope.scheduled_visits = function(activity_id)
    {
        $timeout(function () { 
            Data.get('scheduled_visits/'+activity_id).then(function (results) {
                $scope.html = results[0].htmlstring;
                var cstring = 'visittrustedHtml_'+activity_id;
                $scope[cstring] = $sce.trustAsHtml($scope.html);
            });
        }, 100);
    }

    $scope.search_activities = function (searchdata,from_click) 
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
        Data.post('search_activities', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.listactivities, function() {
                if (results[0].activity_count>0)
                {
                    $scope.listactivities = {};
                    $scope.listactivities = results;
                    $scope.activity_count = results[0].activity_count;
                    $scope.total_records = results[0].activity_count;
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
        
        Data.get('activity_list_ctrl/'+$scope.cat+'/'+$scope.id+'/0').then(function (results) {
            $scope.listactivities = results;
            $scope.next_page_id = 30;
            $scope.activity_count = results[0].activity_count;
            $scope.total_records = results[0].activity_count;
        });


        /*$scope.searchdata = {};
        $scope.$watch($scope.searchdata, function() {
            $scope.searchdata = {
               
            }
        });
        $timeout(function () { 
            Data.get('selectdropdowns/ACTIVITY_TYPE').then(function (results) {
                $rootScope.factivity_type = results;
                
            });
        }, 100);

        $timeout(function () { 
            Data.get('selectenquiry').then(function (results) {
                $rootScope.enquiries = results;
            });
        }, 100);
        
    
        $timeout(function () { 
            Data.get('selectcontact/Client').then(function (results) {
                $rootScope.clients = results;
            });
        }, 100);
    
        $timeout(function () { 
            Data.get('selectcontact/Broker').then(function (results) {
                $rootScope.brokers = results;
            });
        }, 100);
    
        $timeout(function () { 
            Data.get('selectcontact/Developer').then(function (results) {
                $rootScope.developers = results;
            });
        }, 100);

        $timeout(function () { 
            Data.get('activity_list_ctrl/'+$scope.cat+"/"+$scope.id).then(function (results) {
                $scope.listactivities = results;
            });
        }, 100);*/

    }

    

});    
    
    
// app.controller('Activity_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
//     var id = $routeParams.id;
//     var category = $routeParams.category;
//     $scope.contact = {};
//     console.log("user_id"+$rootScope.user_id);
//     $scope.activitydata = {
        
//     };
//     $scope.$watch($scope.activitydata.assign_to, function() {
//         $scope.activitydata.assign_to = [$rootScope.user_id];
//     });
    
//     $scope.$watch($scope.activitydata.teams, function() {
//         $scope.activitydata.teams = [$rootScope.bo_id];
//     });
//     $timeout(function () { 
//         Data.get('selectdropdowns/ACTIVITY_TYPE').then(function (results) {
//             $scope.factivity_type = results;
            
//         });
//     }, 100);
//     //$scope.activitydata.assign_to = $rootScope.user;
//     $scope.create_rights = false;
//     $scope.create_developer = false;
//     $scope.update_rights = false;
//     $scope.delete_rights = false;
//     $scope.view_rights = false;
//     $scope.export_rights = false;    
//     Data.get('session').then(function (results) {
//         if (results.user_id) 
//         {
//             $str =  results.permissions;
//             if ((($str).indexOf("activity_view"))!=-1)
//             {
//                 $scope.view_rights = true;
//                 console.log($scope.view_rights);
//             }
//             if ((($str).indexOf("activity_create"))!=-1)
//             {
//                 $scope.create_rights = true;
//                 console.log($scope.create_rights);
//             }
//             if ((($str).indexOf("activity_update"))!=-1)
//             {
//                 $scope.update_rights = true;
//                 console.log($scope.update_rights);
//             }
//             if ((($str).indexOf("activity_delete"))!=-1)
//             {
//                 $scope.delete_rights = true;
//                 console.log($scope.delete_rights);
//             }

//             if ((($str).indexOf("contacts_developer_create"))!=-1)
//             {
//                 $scope.create_developer = true;
//                 console.log($scope.create_developer);
//             }
        
//             if (!$scope.update_rights)
//             {
//                 $scope.activity = {};
//                 alert("You don't have rights to use this option..");
//                 return;
//             }
//         }
//     });
//     /*$str = ($("#permission_string").val());
//     if ((($str).indexOf("activity_view"))!=-1)
//     {
//         $scope.view_rights = true;
//         console.log($scope.view_rights);
//     }
//     if ((($str).indexOf("activity_create"))!=-1)
//     {
//         $scope.create_rights = true;
//         console.log($scope.create_rights);
//     }
//     if ((($str).indexOf("activity_update"))!=-1)
//     {
//         $scope.update_rights = true;
//         console.log($scope.update_rights);
//     }
//     if ((($str).indexOf("activity_delete"))!=-1)
//     {
//         $scope.delete_rights = true;
//         console.log($scope.delete_rights);
//     }

//     if (!$scope.create_rights)
//     {
//         $scope.activitydata = {};
//         alert("You don't have rights to use this option..");
//         return;
//     }*/
    
    
//     $timeout(function () { 
//         Data.get('selectusers').then(function (results) {
//             $scope.users = results;
//         });
//     }, 100);
//     $timeout(function () { 
//         Data.get('selectteams').then(function (results) {
//             $scope.teams = results;
//         });
//     }, 100);



//     /*Data.get('selectenquiry_with_broker').then(function (results) {
//         $scope.selectenquiries = results;
//     });*/

//     Data.get('getassignedenquiries').then(function (results) {
//         $scope.selectenquiries = results;
//     });
 

//     Data.get('getassignedproperties').then(function (results) {
//         $scope.m_properties = results;
//     });

//     Data.get('getassignedprojects').then(function (results) {
//         $scope.m_projects = results;
//     });

    

//     $timeout(function () { 
//         Data.get('selectcontact/Client').then(function (results) {
//             $scope.clients = results;
//         });
//     }, 100);

//     $timeout(function () { 
//         Data.get('selectcontact/Broker').then(function (results) {
//             $scope.brokers = results;
//         });
//     }, 100);

//     $timeout(function () { 
//         Data.get('selectcontact/Developer').then(function (results) {
//             $scope.developers = results;
//         });
//     }, 100);

//     $timeout(function () { 
//         Data.get('selectdropdowns/CLIENT_SOURCE').then(function (results) {
//             $scope.client_sources = results;
//         });
//     }, 100);

//     $timeout(function () { 
//         Data.get('selectdropdowns/SUB_SOURCE').then(function (results) {
//             $scope.sub_sources = results;
//         });
//     }, 100);

//     $scope.change_sub_source = function (source_channel) 
//     { 
//         Data.get('change_sub_source/'+source_channel).then(function (results) { 
//             $scope.sub_sources = results; 
//         });
//     }

//     $scope.getclientfromenquiry = function (enquiry_id)
//     {
//         Data.get('getclientfromenquiry/'+enquiry_id).then(function (results) { 
//             $scope.$watch($scope.activitydata.client_id, function() {
//                 $scope.activitydata.client_id = results[0].client_id;
//                 $("#client_id").val(results[0].client_id);
//                 $("#client_id").select2();
//                 //console.log($scope.activitydata.client_id);              
//             });
//         });
//     }

//     $scope.select_assign_to = function(teams)
//     {
//         $timeout(function () { 
//             Data.get('select_assign_to/'+teams+'/0').then(function (results) {
//                 $scope.users = results;
//             });
//         }, 100);
//     }

//     /*$timeout(function () { 
//         Data.get('selectdesignation').then(function (results) {
//             $scope.designations = results;
//         });
//     }, 100);

//     $timeout(function () { 
//         Data.get('selectarea').then(function (results) {
//             $scope.areas = results;
//         });
//     }, 100);

//     $timeout(function () { 
//         Data.get('selectlocality').then(function (results) {
//             $scope.localities = results;
//         });
//     }, 100);*/

//     property_id = 0;
//     enquiry_id = 0;
//     project_id = 0;
//     //var datetime = "27/02/2021 13:45";
//     if (category == 'property')
//     {
//         $timeout(function () { 
//             Data.get('getfromproperty/'+id).then(function (results) {
                
//                 arr_teams = 0;
                
//                 if (results[0].teams)
//                 {
//                     arr_teams = ((results[0].teams).split(','));
//                 }
                

//                 $scope.$watch($scope.activitydata.assign_to, function() {
//                     $scope.activitydata.assign_to = [$rootScope.user_id];
//                 });

//                 $scope.$watch($scope.activitydata.activity_type, function() {
//                     $scope.activitydata.activity_type = 'Property Visit';
//                 });

//                 $scope.$watch($scope.activitydata.property_id, function() {
//                     $scope.activitydata.property_id = [results[0].property_id];
//                 });

//                 $scope.$watch($scope.activitydata.project_id, function() {
//                     $scope.activitydata.project_id = [results[0].project_id];
//                 });


//                 $scope.$watch($scope.activitydata.client_id, function() {
//                     $scope.activitydata.client_id = results[0].dev_owner_id;
//                 });

//                 $scope.$watch($scope.activitydata.developer_id, function() {
//                     $scope.activitydata.developer_id = [results[0].developer_id];
//                 });

//                 $scope.$watch($scope.activitydata.broker_id, function() {
//                     $scope.activitydata.broker_id = [results[0].broker_id];
//                 });

//                 $scope.$watch($scope.activitydata.teams, function() {
//                     $scope.activitydata.teams = [$rootScope.bo_id];
//                 });

//                 $scope.$watch($scope.activitydata.status, function() {
//                     $scope.activitydata.status = 'Open';
//                 });

//                 $scope.$watch($scope.activitydata.assign_to, function() {
//                     $scope.activitydata.assign_to = [$rootScope.user_id];
//                 });

//                 /*$scope.$watch($scope.activitydata.activity_start, function() {
//                     $scope.activitydata.activity_start = datetime;
//                 });


//                 $scope.$watch($scope.activitydata.activity_end, function() {
//                     $scope.activitydata.activity_end = datetime;
//                 });*/





//                 /*$scope.$watch($scope.activitydata, function() {
//                     $scope.activitydata = {
//                                         activity_type : 'Property Visit',
//                                         activity_start:datetime,
//                                         activity_end:datetime,
//                                         property_id : [results[0].property_id],
//                                         project_id : results[0].project_id,
//                                         client_id : results[0].dev_owner_id,
//                                         developer_id : results[0].developer_id,
//                                         broker_id : [results[0].broker_id],
//                                         assign_to : [arr_assign_to],
//                                         teams : [arr_teams],
//                                         status : 'Open'
//                                         //enquiry_id : enquiry_id
//                                       // activity_start:"24/02/2021 15:40"
//                                         /*property_id : id,
//                                         activity_type : 'Property Visit',
//                                         //properties : results[0].properties,
//                                         assign_to : arr_assign_to,
//                                         teams : arr_teams
//                     };*/
//                     /*$("#activity_type").select2();
//                     $("#property_id").select2();
//                     $("#project_id").select2();
//                     $("#client_id").select2();
//                     $("#developer_id").select2();
//                     $("#assign_to").select2();
//                     $("#teams").select2();*/

//                 //},true);
		
//             });
//         }, true);
        
	
//     }

//     if (category == 'project')
//     {
// 	    project_id = 0;
//         Data.get('activityproject/'+id).then(function (results) {
//             $scope.selectprojects = {};
//             $scope.selectprojects = results;
// 	        project_id = results[0].project_id;
// 	        Data.get('getproject_enquiries/'+project_id).then(function (results) {
//             	$scope.m_projects = results;
//             });
//         });

//         $timeout(function () { 
//             Data.get('getfromproject/'+id).then(function (results) {
//                 arr_assign_to = 0;
//                 arr_teams = 0;
//                 if (results[0].assign_to)
//                 {
//                     arr_assign_to = (((results[0].assign_to)).split(','));
//                 }
//                 if (results[0].teams)
//                 {
//                     arr_teams = ((results[0].teams).split(','));
//                 }
//                 arr_assign_to = $rootScope.user_id;
//                 console.log("user"+$rootScope.user_id);
//                 $scope.$watch($scope.activitydata, function() {
//                     $scope.activitydata = {};
//                     $scope.activitydata = {
//                                         developer_id : results[0].developer_id,
//                                         project_id : id,
//                                         activity_type : 'Property Visit',
//                                         //properties : results[0].properties,
//                                         assign_to : arr_assign_to,
//                                         teams : arr_teams
//                     };
//                 });
		
//             });
//         }, true);
//     }

//     if (category == 'enquiry')
//     {
	
//         Data.get('activityselectenquiry/'+id).then(function (results) {
//           $scope.selectenquiries = results;
// 	       enquiry_id = results[0].enquiry_id;
// 	       /*Data.get('getproperties_enquiries/'+enquiry_id).then(function (results) {
//                 $rootScope.m_properties = results;
//                 property_id = results[0].property_id;
//           });*/
//         });

//         $timeout(function () { 
//             Data.get('getfromenquiry/'+id).then(function (results) {
//                 /*arr_assign_to = 0;
//                 arr_teams = 0;
//                 if (results[0].assigned)
//                 {
//                     arr_assign_to = (((results[0].assigned)).split(','));
//                 }
//                 if (results[0].teams)
//                 {
//                     arr_teams = ((results[0].teams).split(','));
//                 }*/


//                 //$scope.$watch($scope.activitydata.activity_type, function() {
//                     $scope.activitydata.activity_type = 'Site Visit';
//                 //});
                

//                 //$scope.$watch($scope.activitydata.property_id, function() {
//                     $scope.activitydata.property_id = [results[0].property_id];
//                 //});
                
//                 //$scope.$watch($scope.activitydata.enquiry_id, function() {
//                     $scope.activitydata.enquiry_id = results[0].enquiry_id;
//                 //});


//                 //$scope.$watch($scope.activitydata.project_id, function() {
//                     $scope.activitydata.project_id = results[0].project_id;
//                 //});


//                 //$scope.$watch($scope.activitydata.client_id, function() {
//                     $scope.activitydata.client_id = results[0].client_id;
//                 //});

                
//                 //$scope.$watch($scope.activitydata.broker_id, function() {
//                     $scope.activitydata.broker_id = [results[0].broker_id];
//                 //});

//                 //$scope.$watch($scope.activitydata.teams, function() {
//                     $scope.activitydata.teams = [$rootScope.bo_id];
//               // });

//                 //$scope.$watch($scope.activitydata.status, function() {
//                     $scope.activitydata.status = 'Open';
//                 //});

//                 //$scope.$watch($scope.activitydata.assign_to, function() {
//                     $scope.activitydata.assign_to = [$rootScope.user_id];
//                 //});

//                 /*$scope.$watch($scope.activitydata, function() {
//                     $scope.activitydata = {};
//                     $scope.activitydata = { 
//                                         client_id : results[0].client_id,
//                                         activity_type : 'Site Visit',
//                                         agreement_for : results[0].enquiry_for,
//                                         enquiry_id : results[0].enquiry_id,
//                                         property_id : property_id,
//                                         buyer_id :results[0].client_id,
//                                         assign_to : arr_assign_to,
//                                         teams : arr_teams
//                     };
//                 });*/
//             });
//         }, true);
//     }

//     if (category == 'contact')
//     {
        
//         $scope.activitydata.activity_type = 'Site Visit';
//         $scope.activitydata.client_id = id;
//     }

//     $scope.activity_add_new = {activitydata:''};
//     $scope.activity_add_new = function (activitydata) {
//         console.log(activitydata.activity_type);
//         console.log(activitydata.property_id);
//         if ((activitydata.activity_type=='Site Visit' || activitydata.activity_type=='Property Visit') && (!activitydata.property_id))
//         {
//             if (activitydata.project_id)
//             {

//             }
//             else
//             {
//                 alert("Selection of Property or Project is mandatory.. !!!");
//                 return;
//             }
//         }
//         if (activitydata.activity_type=='Meeting')
//         {
//             if (activitydata.client_id || activitydata.broker_id || activitydata.developer_id )
//             {

//             }
//             else
//             {
//                 alert("Selection of Client or Broker or Developer in mandatory.. !!!");
//                 return;
//             }
//         }
//         $("#add-new-btn").css("display","none");
//         Data.post('activity_add_new', {
//             activitydata: activitydata
//         }).then(function (results) {
//             Data.toast(results);
//             if (results.status == "success") {
//                 $('#file_activity').fileinput('upload');
//                 $location.path('activity_list/direct/0');
//             }
//         });
//     };

//     $scope.change_activity_sub_type = function (activity_type) 
//     { 
//         Data.get('change_activity_sub_type/'+activity_type).then(function (results) { 
//             $scope.activity_sub_types = results; 
//         });
//     }
//     $scope.GetProperties = function (enquiry_id)
//     {
//         $timeout(function () { 
//             Data.get('getproperties_enquiries/'+enquiry_id).then(function (results) {
//                 $scope.m_properties = results;
//             });
//         }, 100);
//     }
//     $scope.AddListValue = function (type)
//     {
//         $scope.temptype = type;
//         $timeout(function () { 
//             Data.get('selectparentlist').then(function (results) {
//                 $scope.parentlists = results;
//             });
//         }, 100);

//         $scope.listvalues = {
//                                 type:type,
//                             }
//     }

//     $scope.listvalues_add = function (listvalues) {
//         Data.post('listvalues_add', {
//             listvalues: listvalues
//         }).then(function (results) {
//             Data.toast(results);
//             if (results.status == "success") {
//                 $("#addvalues").modal("hide");
//                 $timeout(function () { 
//                     Data.get('selectdropdowns/'+$scope.temptype).then(function (results) {
//                         var controlvalue = (($scope.temptype).toLowerCase());
//                         $scope.$watch($scope[controlvalue], function() {
//                             $scope[controlvalue] = results;
//                         }, true);
//                     });
//                 }, 1000);
//             }
//         });
//     };

//     $scope.add_option = "Client";

//     $scope.AddContact = function(add_option)
//     {
//         $scope.add_option = add_option;
//     }


//     $scope.contact_details = function (field_name,value) 
//     {  

//         if (field_name=='locality_id')
//         {
//             $timeout(function () { 
//                 Data.get('getfromlocality/'+value).then(function (results) {
//                     $scope.contact.area_id = results[0].area_id;
//                     $scope.contact.city = results[0].city;
//                     $scope.contact.state = results[0].state;
//                     $scope.contact.country = results[0].country;
//                 });
//             }, 100);
//             $timeout(function () { 
//                 $("#contact.area_id").select2();
//             },2000);
//         }

//         if (field_name=='area_id')
//         {
//             $timeout(function () { 
//                 Data.get('getfromarea/'+value).then(function (results) {
//                     $scope.contact.city = results[0].city;
//                     $scope.contact.state = results[0].state;
//                     $scope.contact.country = results[0].country;
//                 });
//             }, 100);
//         }
//         if (field_name=='off_locality')
//         {
//             $timeout(function () { 
//                 Data.get('getfromlocality/'+value).then(function (results) {
//                     $scope.contact.off_area = results[0].area_id;
//                     $scope.contact.off_city = results[0].city;
//                     $scope.contact.off_state = results[0].state;
//                     $scope.contact.off_country = results[0].country;
//                 });
//             }, 100);
//         }

//         if (field_name=='off_area')
//         {
//             $timeout(function () { 
//                 Data.get('getfromarea/'+value).then(function (results) {
//                     $scope.contact.off_city = results[0].city;
//                     $scope.contact.off_state = results[0].state;
//                     $scope.contact.off_country = results[0].country;
//                 });
//             }, 100);
//         }
//         if (field_name=='opp_area')
//         {
//             $timeout(function () { 
//                 Data.get('getfromarea/'+value).then(function (results) {
//                     $scope.contact.opp_city = results[0].city;
//                 });
//             }, 100);
//         }
//     }

//     $scope.mob_error = "";
//     $scope.email_error = "";
//     $scope.checkcontact = function (field,field_name) 
//     { 
//         Data.get('checkcontact/'+field+'/'+field_name).then(function (results) {
//             console.log(results);
//             if (results[0].found=='Yes')
//             {
//                 if (field_name=='mob_no')
//                 {
//                     alert("Mobile Number already registered ... !!");
//                     $scope.mob_error = "Mobile Number already registered";
//                 }
//                 if (field_name=='email')
//                 {
//                     alert("Email ID already registered ... !!");
//                     $scope.email_error = "Email ID already registered";
//                 }
//             }
//             else{
//                 if (field_name=='mob_no')
//                 {
//                     $scope.mob_error = "";
//                 }
//                 if (field_name=='email')
//                 {
//                     $scope.email_error = "";
//                 }
//             }
//         });
//     }

//     $scope.contact_add_new = function (contact) {
//         contact.file_name = $("#file_name_company_logo").val();
//         contact.contact_off = $scope.add_option;
//         Data.post('contact_add_new', {
//             contact: contact
//         }).then(function (results) {
//             Data.toast(results);
//             if (results.status == "success") {
//                 $('#file_company_logo').fileinput('upload');
//                 $('#file_visiting_card').fileinput('upload');
//                 $('#file_contact_pic').fileinput('upload');
//                 $("#adddeveloper").modal("hide");
//                 $scope.clients = {};
//                 Data.get('selectcontact/Client').then(function (results) {
//                     $scope.clients = results;
//                 });
//             }
//         });
//     };


// });
    
    
// app.controller('Activity_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
//     var id = $routeParams.id;
//     var category = $routeParams.category;
//     $scope.contact = {};
//     console.log("user_id"+$rootScope.user_id);
    
//     $scope.activitydata = {
        
//     };
//     $scope.$watch($scope.activitydata.assign_to, function() {
//         $scope.activitydata.assign_to = [$rootScope.user_id];
//     });
    
//     $scope.$watch($scope.activitydata.teams, function() {
//         $scope.activitydata.teams = [$rootScope.bo_id];
//     });

//     // $timeout(function () { 
//         Data.get('selectdropdowns/ACTIVITY_TYPE').then(function (results) {
//             $scope.factivity_type = results;
//         });
//     // }, 100);
//     Data.get('selectusers').then(function (results) {
//             $scope.users = results;
//             console.log($scope.users);
//         });

//         // $timeout(function () { 
//         Data.get('selectteams').then(function (results) {
//             $scope.teams = results;
//         });
//     // }, 100);

//     /*Data.get('selectenquiry_with_broker').then(function (results) {
//         $scope.selectenquiries = results;
//     });*/

//     Data.get('getassignedenquiries').then(function (results) {
//         $scope.selectenquiries = results;
//     });
 

//     Data.get('getassignedproperties').then(function (results) {
//         $scope.m_properties = results;
//     });

//     Data.get('getassignedprojects').then(function (results) {
//         $scope.m_projects = results;
//     });

    

//     // $timeout(function () { 
//         Data.get('selectcontact/Client').then(function (results) {
//             $scope.clients = results;
//         });
//     //}, 100);

//     // $timeout(function () { 
//         Data.get('selectcontact/Broker').then(function (results) {
//             $scope.brokers = results;
//         });
//     // }, 100);

//     // $timeout(function () { 
//         Data.get('selectcontact/Developer').then(function (results) {
//             $scope.developers = results;
//         });
//     // }, 100);

//     // $timeout(function () { 
//         Data.get('selectdropdowns/CLIENT_SOURCE').then(function (results) {
//             $scope.client_sources = results;
//         });
//     // }, 100);

//     // $timeout(function () { 
//         Data.get('selectdropdowns/SUB_SOURCE').then(function (results) {
//             $scope.sub_sources = results;
//         });
//     // }, 100);


//     //$scope.activitydata.assign_to = $rootScope.user;

//     $scope.create_rights = true;
//     $scope.create_developer = true; 
//     $scope.update_rights = true;
//     $scope.delete_rights = true;
//     $scope.view_rights = true;
//     $scope.export_rights = true;    
//     Data.get('session').then(function (results) {

//         if (results.user_id) 
//         {
//             $str =  results.permissions;
//             if ((($str).indexOf("activity_view"))!=-1)
//             {
//                 $scope.view_rights = true;
//                 console.log($scope.view_rights);
//             }
//             if ((($str).indexOf("activity_create"))!=-1)
//             {
//                 $scope.create_rights = true;
//                 console.log($scope.create_rights);
//             }
//             if ((($str).indexOf("activity_update"))!=-1)
//             {
//                 $scope.update_rights = true;
//                 console.log($scope.update_rights);
//             }
//             if ((($str).indexOf("activity_delete"))!=-1)
//             {
//                 $scope.delete_rights = true;
//                 console.log($scope.delete_rights);
//             }

//             if ((($str).indexOf("contacts_developer_create"))!=-1)
//             {
//                 $scope.create_developer = true;
//                 console.log($scope.create_developer);
//             }
        
//             if (!$scope.update_rights)
//             {
//                 $scope.activity = {};
//                 alert("You don't have rights to use this option..");
//                 return;
//             }
//         }
//     });
    

//     /*$str = ($("#permission_string").val());
//     if ((($str).indexOf("activity_view"))!=-1)
//     {
//         $scope.view_rights = true;
//         console.log($scope.view_rights);
//     }
//     if ((($str).indexOf("activity_create"))!=-1)
//     {

//         $scope.create_rights = true;
//         console.log($scope.create_rights);
//     }
//     if ((($str).indexOf("activity_update"))!=-1)
//     {
//         $scope.update_rights = true;
//         console.log($scope.update_rights);
//     }
//     if ((($str).indexOf("activity_delete"))!=-1)
//     {
//         $scope.delete_rights = true;
//         console.log($scope.delete_rights);
//     }

//     if (!$scope.create_rights)
//     {
//         $scope.activitydata = {};
//         alert("You don't have rights to use this option..");
//         return;
//     }*/
//     // $timeout(function () { 
        
//     // }, 100);
//     // $timeout(function () { 
//         Data.get('selectteams').then(function (results) {
//             $scope.teams = results;
//         });
//     // }, 100);



//     /*Data.get('selectenquiry_with_broker').then(function (results) {
//         $scope.selectenquiries = results;
//     });*/

//     Data.get('getassignedenquiries').then(function (results) {
//         $scope.selectenquiries = results;
//     });
 

//     Data.get('getassignedproperties').then(function (results) {
//         $scope.m_properties = results;
//     });

//     Data.get('getassignedprojects').then(function (results) {
//         $scope.m_projects = results;
//     });

    

//     // $timeout(function () { 
//         Data.get('selectcontact/Client').then(function (results) {
//             $scope.clients = results;
//         });
//     //}, 100);

//     // $timeout(function () { 
//         Data.get('selectcontact/Broker').then(function (results) {
//             $scope.brokers = results;
//         });
//     // }, 100);

//     // $timeout(function () { 
//         Data.get('selectcontact/Developer').then(function (results) {
//             $scope.developers = results;
//         });
//     // }, 100);

//     // $timeout(function () { 
//         Data.get('selectdropdowns/CLIENT_SOURCE').then(function (results) {
//             $scope.client_sources = results;
//         });
//     // }, 100);

//     // $timeout(function () { 
//         Data.get('selectdropdowns/SUB_SOURCE').then(function (results) {
//             $scope.sub_sources = results;
//         });
//     // }, 100);

//     $scope.change_sub_source = function (source_channel) 
//     { 
//         Data.get('change_sub_source/'+source_channel).then(function (results) { 
//             $scope.sub_sources = results; 
//         });
//     }

//     $scope.getclientfromenquiry = function (enquiry_id)
//     {
//         Data.get('getclientfromenquiry/'+enquiry_id).then(function (results) { 
//             $scope.$watch($scope.activitydata.client_id, function() {
//                 $scope.activitydata.client_id = results[0].client_id;
//                 $("#client_id").val(results[0].client_id);
//                 $("#client_id").select2();
//                 //console.log($scope.activitydata.client_id);              
//             });
//         });
//     }

//     $scope.select_assign_to = function(teams)
//     {
//         $timeout(function () { 
//             Data.get('select_assign_to/'+teams+'/0').then(function (results) {
//                 $scope.users = results;
//             });
//         }, 100);
//     }

//     /*$timeout(function () { 
//         Data.get('selectdesignation').then(function (results) {
//             $scope.designations = results;
//         });
//     }, 100);

//     $timeout(function () { 
//         Data.get('selectarea').then(function (results) {
//             $scope.areas = results;
//         });
//     }, 100);

//     $timeout(function () { 
//         Data.get('selectlocality').then(function (results) {
//             $scope.localities = results;
//         });
//     }, 100);*/

//     property_id = 0;
//     enquiry_id = 0;
//     project_id = 0;
//     //var datetime = "27/02/2021 13:45";
//     if (category == 'property')
//     {
//         $timeout(function () { 
//             Data.get('getfromproperty/'+id).then(function (results) {
                
//                 arr_teams = 0;
                
//                 if (results[0].teams)
//                 {
//                     arr_teams = ((results[0].teams).split(','));
//                 }
                

//                 $scope.$watch($scope.activitydata.assign_to, function() {
//                     $scope.activitydata.assign_to = [$rootScope.user_id];
//                 });

//                 $scope.$watch($scope.activitydata.activity_type, function() {
//                     $scope.activitydata.activity_type = 'Property Visit';
//                 });

//                 $scope.$watch($scope.activitydata.property_id, function() {
//                     $scope.activitydata.property_id = [results[0].property_id];
//                 });

//                 $scope.$watch($scope.activitydata.project_id, function() {
//                     $scope.activitydata.project_id = [results[0].project_id];
//                 });


//                 $scope.$watch($scope.activitydata.client_id, function() {
//                     $scope.activitydata.client_id = results[0].dev_owner_id;
//                 });

//                 $scope.$watch($scope.activitydata.developer_id, function() {
//                     $scope.activitydata.developer_id = [results[0].developer_id];
//                 });

//                 $scope.$watch($scope.activitydata.broker_id, function() {
//                     $scope.activitydata.broker_id = [results[0].broker_id];
//                 });

//                 $scope.$watch($scope.activitydata.teams, function() {
//                     $scope.activitydata.teams = [$rootScope.bo_id];
//                 });

//                 $scope.$watch($scope.activitydata.status, function() {
//                     $scope.activitydata.status = 'Open';
//                 });

//                 $scope.$watch($scope.activitydata.assign_to, function() {
//                     $scope.activitydata.assign_to = [$rootScope.user_id];
//                 });

//                 /*$scope.$watch($scope.activitydata.activity_start, function() {
//                     $scope.activitydata.activity_start = datetime;
//                 });


//                 $scope.$watch($scope.activitydata.activity_end, function() {
//                     $scope.activitydata.activity_end = datetime;
//                 });*/





//                 /*$scope.$watch($scope.activitydata, function() {
//                     $scope.activitydata = {
//                                         activity_type : 'Property Visit',
//                                         activity_start:datetime,
//                                         activity_end:datetime,
//                                         property_id : [results[0].property_id],
//                                         project_id : results[0].project_id,
//                                         client_id : results[0].dev_owner_id,
//                                         developer_id : results[0].developer_id,
//                                         broker_id : [results[0].broker_id],
//                                         assign_to : [arr_assign_to],
//                                         teams : [arr_teams],
//                                         status : 'Open'
//                                         //enquiry_id : enquiry_id
//                                       // activity_start:"24/02/2021 15:40"
//                                         /*property_id : id,
//                                         activity_type : 'Property Visit',
//                                         //properties : results[0].properties,
//                                         assign_to : arr_assign_to,
//                                         teams : arr_teams
//                     };*/
//                     /*$("#activity_type").select2();
//                     $("#property_id").select2();
//                     $("#project_id").select2();
//                     $("#client_id").select2();
//                     $("#developer_id").select2();
//                     $("#assign_to").select2();
//                     $("#teams").select2();*/

//                 //},true);
        
//             });
//         }, true);
        
    
//     }

//     if (category == 'project')
//     {
//         project_id = 0;
//         Data.get('activityproject/'+id).then(function (results) {
//             $scope.selectprojects = {};
//             $scope.selectprojects = results;
//             project_id = results[0].project_id;
//             Data.get('getproject_enquiries/'+project_id).then(function (results) {
//                 $scope.m_projects = results;
//             });
//         });

//         $timeout(function () { 
//             Data.get('getfromproject/'+id).then(function (results) {
//                 arr_assign_to = 0;
//                 arr_teams = 0;
//                 if (results[0].assign_to)
//                 {
//                     arr_assign_to = (((results[0].assign_to)).split(','));
//                 }
//                 if (results[0].teams)
//                 {
//                     arr_teams = ((results[0].teams).split(','));
//                 }
//                 arr_assign_to = $rootScope.user_id;
//                 console.log("user"+$rootScope.user_id);
//                 $scope.$watch($scope.activitydata, function() {
//                     $scope.activitydata = {};
//                     $scope.activitydata = {
//                                         developer_id : results[0].developer_id,
//                                         project_id : id,
//                                         activity_type : 'Property Visit',
//                                         //properties : results[0].properties,
//                                         assign_to : arr_assign_to,
//                                         teams : arr_teams
//                     };
//                 });
        
//             });
//         }, true);
//     }

//     if (category == 'enquiry')
//     {
    
//         Data.get('activityselectenquiry/'+id).then(function (results) {
//           $scope.selectenquiries = results;
//           enquiry_id = results[0].enquiry_id;
//           /*Data.get('getproperties_enquiries/'+enquiry_id).then(function (results) {
//                 $rootScope.m_properties = results;
//                 property_id = results[0].property_id;
//           });*/
//         });

//         $timeout(function () { 
//             Data.get('getfromenquiry/'+id).then(function (results) {
//                 /*arr_assign_to = 0;
//                 arr_teams = 0;
//                 if (results[0].assigned)
//                 {
//                     arr_assign_to = (((results[0].assigned)).split(','));
//                 }
//                 if (results[0].teams)
//                 {
//                     arr_teams = ((results[0].teams).split(','));
//                 }*/


//                 //$scope.$watch($scope.activitydata.activity_type, function() {
//                     $scope.activitydata.activity_type = 'Site Visit';
//                 //});
                

//                 //$scope.$watch($scope.activitydata.property_id, function() {
//                     $scope.activitydata.property_id = [results[0].property_id];
//                 //});
                
//                 //$scope.$watch($scope.activitydata.enquiry_id, function() {
//                     $scope.activitydata.enquiry_id = results[0].enquiry_id;
//                 //});


//                 //$scope.$watch($scope.activitydata.project_id, function() {
//                     $scope.activitydata.project_id = results[0].project_id;
//                 //});


//                 //$scope.$watch($scope.activitydata.client_id, function() {
//                     $scope.activitydata.client_id = results[0].client_id;
//                 //});

                
//                 //$scope.$watch($scope.activitydata.broker_id, function() {
//                     $scope.activitydata.broker_id = [results[0].broker_id];
//                 //});

//                 //$scope.$watch($scope.activitydata.teams, function() {
//                     $scope.activitydata.teams = [$rootScope.bo_id];
//               // });

//                 //$scope.$watch($scope.activitydata.status, function() {
//                     $scope.activitydata.status = 'Open';
//                 //});

//                 //$scope.$watch($scope.activitydata.assign_to, function() {
//                     $scope.activitydata.assign_to = [$rootScope.user_id];
//                 //});

//                 /*$scope.$watch($scope.activitydata, function() {
//                     $scope.activitydata = {};
//                     $scope.activitydata = { 
//                                         client_id : results[0].client_id,
//                                         activity_type : 'Site Visit',
//                                         agreement_for : results[0].enquiry_for,
//                                         enquiry_id : results[0].enquiry_id,
//                                         property_id : property_id,
//                                         buyer_id :results[0].client_id,
//                                         assign_to : arr_assign_to,
//                                         teams : arr_teams
//                     };
//                 });*/
//             });
//         }, true);
//     }

//     if (category == 'contact')
//     {
        
//         $scope.activitydata.activity_type = 'Site Visit';
//         $scope.activitydata.client_id = id;
//     }

//     $scope.activity_add_new = {activitydata:''};
//     $scope.activity_add_new = function (activitydata) {
//         console.log(activitydata.activity_type);
//         console.log(activitydata.property_id);
//         if ((activitydata.activity_type=='Site Visit' || activitydata.activity_type=='Property Visit') && (!activitydata.property_id))
//         {
//             if (activitydata.project_id)
//             {

//             }
//             else
//             {
//                 alert("Selection of Property or Project is mandatory.. !!!");
//                 return;
//             }
//         }
//         if (activitydata.activity_type=='Meeting')
//         {
//             if (activitydata.client_id || activitydata.broker_id || activitydata.developer_id )
//             {

//             }
//             else
//             {
//                 alert("Selection of Client or Broker or Developer in mandatory.. !!!");
//                 return;
//             }
//         }
//         $("#add-new-btn").css("display","none");
//         Data.post('activity_add_new', {
//             activitydata: activitydata
//         }).then(function (results) {
//             Data.toast(results);
//             if (results.status == "success") {
//                 $('#file_activity').fileinput('upload');
//                 $location.path('activity_list/direct/0');
//             }
//         });
//     };

//     $scope.change_activity_sub_type = function (activity_type) 
//     { 
//         Data.get('change_activity_sub_type/'+activity_type).then(function (results) { 
//             $scope.activity_sub_types = results; 
//         });
//     }
//     $scope.GetProperties = function (enquiry_id)
//     {
//         $timeout(function () { 
//             Data.get('getproperties_enquiries/'+enquiry_id).then(function (results) {
//                 $scope.m_properties = results;
//             });
//         }, 100);
//     }
//     $scope.AddListValue = function (type)
//     {
//         $scope.temptype = type;
//         $timeout(function () { 
//             Data.get('selectparentlist').then(function (results) {
//                 $scope.parentlists = results;
//             });
//         }, 100);

//         $scope.listvalues = {
//                                 type:type,
//                             }
//     }

//     $scope.listvalues_add = function (listvalues) {
//         Data.post('listvalues_add', {
//             listvalues: listvalues
//         }).then(function (results) {
//             Data.toast(results);
//             if (results.status == "success") {
//                 $("#addvalues").modal("hide");
//                 $timeout(function () { 
//                     Data.get('selectdropdowns/'+$scope.temptype).then(function (results) {
//                         var controlvalue = (($scope.temptype).toLowerCase());
//                         $scope.$watch($scope[controlvalue], function() {
//                             $scope[controlvalue] = results;
//                         }, true);
//                     });
//                 }, 1000);
//             }
//         });
//     };

//     $scope.add_option = "Client";

//     $scope.AddContact = function(add_option)
//     {
//         $scope.add_option = add_option;
//     }


//     $scope.contact_details = function (field_name,value) 
//     {  

//         if (field_name=='locality_id')
//         {
//             $timeout(function () { 
//                 Data.get('getfromlocality/'+value).then(function (results) {
//                     $scope.contact.area_id = results[0].area_id;
//                     $scope.contact.city = results[0].city;
//                     $scope.contact.state = results[0].state;
//                     $scope.contact.country = results[0].country;
//                 });
//             }, 100);
//             $timeout(function () { 
//                 $("#contact.area_id").select2();
//             },2000);
//         }

//         if (field_name=='area_id')
//         {
//             $timeout(function () { 
//                 Data.get('getfromarea/'+value).then(function (results) {
//                     $scope.contact.city = results[0].city;
//                     $scope.contact.state = results[0].state;
//                     $scope.contact.country = results[0].country;
//                 });
//             }, 100);
//         }
//         if (field_name=='off_locality')
//         {
//             $timeout(function () { 
//                 Data.get('getfromlocality/'+value).then(function (results) {
//                     $scope.contact.off_area = results[0].area_id;
//                     $scope.contact.off_city = results[0].city;
//                     $scope.contact.off_state = results[0].state;
//                     $scope.contact.off_country = results[0].country;
//                 });
//             }, 100);
//         }

//         if (field_name=='off_area')
//         {
//             $timeout(function () { 
//                 Data.get('getfromarea/'+value).then(function (results) {
//                     $scope.contact.off_city = results[0].city;
//                     $scope.contact.off_state = results[0].state;
//                     $scope.contact.off_country = results[0].country;
//                 });
//             }, 100);
//         }
//         if (field_name=='opp_area')
//         {
//             $timeout(function () { 
//                 Data.get('getfromarea/'+value).then(function (results) {
//                     $scope.contact.opp_city = results[0].city;
//                 });
//             }, 100);
//         }
//     }

//     $scope.mob_error = "";
//     $scope.email_error = "";
//     $scope.checkcontact = function (field,field_name) 
//     { 
//         Data.get('checkcontact/'+field+'/'+field_name).then(function (results) {
//             console.log(results);
//             if (results[0].found=='Yes')
//             {
//                 if (field_name=='mob_no')
//                 {
//                     alert("Mobile Number already registered ... !!");
//                     $scope.mob_error = "Mobile Number already registered";
//                 }
//                 if (field_name=='email')
//                 {
//                     alert("Email ID already registered ... !!");
//                     $scope.email_error = "Email ID already registered";
//                 }
//             }
//             else{
//                 if (field_name=='mob_no')
//                 {
//                     $scope.mob_error = "";
//                 }
//                 if (field_name=='email')
//                 {
//                     $scope.email_error = "";
//                 }
//             }
//         });
//     }

//     $scope.contact_add_new = function (contact) {
//         contact.file_name = $("#file_name_company_logo").val();
//         contact.contact_off = $scope.add_option;
//         Data.post('contact_add_new', {
//             contact: contact
//         }).then(function (results) {
//             Data.toast(results);
//             if (results.status == "success") {
//                 $('#file_company_logo').fileinput('upload');
//                 $('#file_visiting_card').fileinput('upload');
//                 $('#file_contact_pic').fileinput('upload');
//                 $("#adddeveloper").modal("hide");
//                 $scope.clients = {};
//                 Data.get('selectcontact/Client').then(function (results) {
//                     $scope.clients = results;
//                 });
//             }
//         });
//     };


// }); 

app.controller('Activity_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var id = $routeParams.id;
    var category = $routeParams.category;
    $scope.contact = {};
    console.log("user_id"+$rootScope.user_id);
    // $team_new = $rootScope.teams[0];
    $team_new = $rootScope.teams;
    console.log($team_new);
    
    $scope.activitydata = {
        
    };
    $scope.$watch($scope.activitydata.assign_to, function() {
        $scope.activitydata.assign_to = [$rootScope.user_id];
        console.log($scope.activitydata.assign_to);
    });
    
    $scope.$watch($scope.activitydata.teams, function() {
        $scope.activitydata.teams = $team_new;
        console.log($scope.activitydata.teams);
        // $scope.activitydata.teams = [$rootScope.bo_id];
    });

    // $timeout(function () { 
        Data.get('selectdropdowns/ACTIVITY_TYPE').then(function (results) {
            $scope.factivity_type = results;
        });
    // }, 100);
    Data.get('selectusers').then(function (results) {
            $scope.users = results;
            console.log($scope.users);
        });

        // $timeout(function () { 
        Data.get('selectteams').then(function (results) {
            $scope.teams = results;
        });
    // }, 100);

    /*Data.get('selectenquiry_with_broker').then(function (results) {
        $scope.selectenquiries = results;
    });*/

    Data.get('getassignedenquiries').then(function (results) {
        $scope.selectenquiries = results;
    });
 

    Data.get('getassignedproperties').then(function (results) {
        $scope.m_properties = results;
    });

    Data.get('getassignedprojects').then(function (results) {
        $scope.m_projects = results;
    });

    

    // $timeout(function () { 
        Data.get('selectcontact/Client').then(function (results) {
            $scope.clients = results;
        });
    //}, 100);

    // $timeout(function () { 
        Data.get('selectcontact/Broker').then(function (results) {
            $scope.brokers = results;
        });
    // }, 100);

    // $timeout(function () { 
        Data.get('selectcontact/Developer').then(function (results) {
            $scope.developers = results;
        });
    // }, 100);

    // $timeout(function () { 
        Data.get('selectdropdowns/CLIENT_SOURCE').then(function (results) {
            $scope.client_sources = results;
        });
    // }, 100);

    // $timeout(function () { 
        Data.get('selectdropdowns/SUB_SOURCE').then(function (results) {
            $scope.sub_sources = results;
        });
    // }, 100);


    //$scope.activitydata.assign_to = $rootScope.user;

    $scope.create_rights = true;
    $scope.create_developer = true; 
    $scope.update_rights = true;
    $scope.delete_rights = true;
    $scope.view_rights = true;
    $scope.export_rights = true;    
    Data.get('session').then(function (results) {

        if (results.user_id) 
        {
            $str =  results.permissions;
            if ((($str).indexOf("activity_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("activity_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("activity_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("activity_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }

            if ((($str).indexOf("contacts_developer_create"))!=-1)
            {
                $scope.create_developer = true;
                console.log($scope.create_developer);
            }
        
            if (!$scope.update_rights)
            {
                $scope.activity = {};
                alert("You don't have rights to use this option..");
                return;
            }
        }
    });
    

    /*$str = ($("#permission_string").val());
    if ((($str).indexOf("activity_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("activity_create"))!=-1)
    {

        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("activity_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("activity_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.create_rights)
    {
        $scope.activitydata = {};
        alert("You don't have rights to use this option..");
        return;
    }*/
    // $timeout(function () { 
        
   



   

    $scope.change_sub_source = function (source_channel) 
    { 
        Data.get('change_sub_source/'+source_channel).then(function (results) { 
            $scope.sub_sources = results; 
        });
    }

    $scope.getclientfromenquiry = function (enquiry_id)
    {
        Data.get('getclientfromenquiry/'+enquiry_id).then(function (results) { 
            $scope.$watch($scope.activitydata.client_id, function() {
                $scope.activitydata.client_id = results[0].client_id;
                $("#client_id").val(results[0].client_id);
                $("#client_id").select2();
                //console.log($scope.activitydata.client_id);              
            });
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

    /*$timeout(function () { 
        Data.get('selectdesignation').then(function (results) {
            $scope.designations = results;
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
    }, 100);*/

    property_id = 0;
    enquiry_id = 0;
    project_id = 0;
    //var datetime = "27/02/2021 13:45";
    if (category == 'property')
    {
        $timeout(function () { 
            Data.get('getfromproperty/'+id).then(function (results) {
                
                arr_teams = 0;
                
                if (results[0].teams)
                {
                    arr_teams = ((results[0].teams).split(','));
                }
                

                $scope.$watch($scope.activitydata.assign_to, function() {
                    $scope.activitydata.assign_to = [$rootScope.user_id];
                });

                $scope.$watch($scope.activitydata.activity_type, function() {
                    $scope.activitydata.activity_type = 'Property Visit';
                });

                $scope.$watch($scope.activitydata.property_id, function() {
                    $scope.activitydata.property_id = [results[0].property_id];
                });

                $scope.$watch($scope.activitydata.project_id, function() {
                    $scope.activitydata.project_id = [results[0].project_id];
                });


                $scope.$watch($scope.activitydata.client_id, function() {
                    $scope.activitydata.client_id = results[0].dev_owner_id;
                });

                $scope.$watch($scope.activitydata.developer_id, function() {
                    $scope.activitydata.developer_id = [results[0].developer_id];
                });

                $scope.$watch($scope.activitydata.broker_id, function() {
                    $scope.activitydata.broker_id = [results[0].broker_id];
                });

                $scope.$watch($scope.activitydata.teams, function() {
                    $scope.activitydata.teams = [$rootScope.bo_id];
                });

                $scope.$watch($scope.activitydata.status, function() {
                    $scope.activitydata.status = 'Open';
                });

                $scope.$watch($scope.activitydata.assign_to, function() {
                    $scope.activitydata.assign_to = [$rootScope.user_id];
                });

                /*$scope.$watch($scope.activitydata.activity_start, function() {
                    $scope.activitydata.activity_start = datetime;
                });


                $scope.$watch($scope.activitydata.activity_end, function() {
                    $scope.activitydata.activity_end = datetime;
                });*/





                /*$scope.$watch($scope.activitydata, function() {
                    $scope.activitydata = {
                                        activity_type : 'Property Visit',
                                        activity_start:datetime,
                                        activity_end:datetime,
                                        property_id : [results[0].property_id],
                                        project_id : results[0].project_id,
                                        client_id : results[0].dev_owner_id,
                                        developer_id : results[0].developer_id,
                                        broker_id : [results[0].broker_id],
                                        assign_to : [arr_assign_to],
                                        teams : [arr_teams],
                                        status : 'Open'
                                        //enquiry_id : enquiry_id
                                       // activity_start:"24/02/2021 15:40"
                                        /*property_id : id,
                                        activity_type : 'Property Visit',
                                        //properties : results[0].properties,
                                        assign_to : arr_assign_to,
                                        teams : arr_teams
                    };*/
                    /*$("#activity_type").select2();
                    $("#property_id").select2();
                    $("#project_id").select2();
                    $("#client_id").select2();
                    $("#developer_id").select2();
                    $("#assign_to").select2();
                    $("#teams").select2();*/

                //},true);
        
            });
        }, true);
        
    
    }

    if (category == 'project')
    {
        project_id = 0;
        Data.get('activityproject/'+id).then(function (results) {
            $scope.selectprojects = {};
            $scope.selectprojects = results;
            project_id = results[0].project_id;
            Data.get('getproject_enquiries/'+project_id).then(function (results) {
                $scope.m_projects = results;
            });
        });

        $timeout(function () { 
            Data.get('getfromproject/'+id).then(function (results) {
                arr_assign_to = 0;
                arr_teams = 0;
                if (results[0].assign_to)
                {
                    arr_assign_to = (((results[0].assign_to)).split(','));
                }
                if (results[0].teams)
                {
                    arr_teams = ((results[0].teams).split(','));
                }
                arr_assign_to = $rootScope.user_id;
                console.log("user"+$rootScope.user_id);
                $scope.$watch($scope.activitydata, function() {
                    $scope.activitydata = {};
                    $scope.activitydata = {
                                        developer_id : results[0].developer_id,
                                        project_id : id,
                                        activity_type : 'Property Visit',
                                        //properties : results[0].properties,
                                        assign_to : arr_assign_to,
                                        teams : arr_teams
                    };
                });
        
            });
        }, true);
    }

    if (category == 'enquiry')
    {
    
        Data.get('activityselectenquiry/'+id).then(function (results) {
           $scope.selectenquiries = results;
           enquiry_id = results[0].enquiry_id;
           /*Data.get('getproperties_enquiries/'+enquiry_id).then(function (results) {
                $rootScope.m_properties = results;
                property_id = results[0].property_id;
           });*/
        });

        $timeout(function () { 
            Data.get('getfromenquiry/'+id).then(function (results) {
                /*arr_assign_to = 0;
                arr_teams = 0;
                if (results[0].assigned)
                {
                    arr_assign_to = (((results[0].assigned)).split(','));
                }
                if (results[0].teams)
                {
                    arr_teams = ((results[0].teams).split(','));
                }*/


                //$scope.$watch($scope.activitydata.activity_type, function() {
                    $scope.activitydata.activity_type = 'Site Visit';
                //});
                

                //$scope.$watch($scope.activitydata.property_id, function() {
                    $scope.activitydata.property_id = [results[0].property_id];
                //});
                
                //$scope.$watch($scope.activitydata.enquiry_id, function() {
                    $scope.activitydata.enquiry_id = results[0].enquiry_id;
                //});


                //$scope.$watch($scope.activitydata.project_id, function() {
                    $scope.activitydata.project_id = results[0].project_id;
                //});


                //$scope.$watch($scope.activitydata.client_id, function() {
                    $scope.activitydata.client_id = results[0].client_id;
                //});

                
                //$scope.$watch($scope.activitydata.broker_id, function() {
                    $scope.activitydata.broker_id = [results[0].broker_id];
                //});

                //$scope.$watch($scope.activitydata.teams, function() {
                    $scope.activitydata.teams = [$rootScope.bo_id];
               // });

                //$scope.$watch($scope.activitydata.status, function() {
                    $scope.activitydata.status = 'Open';
                //});

                //$scope.$watch($scope.activitydata.assign_to, function() {
                    $scope.activitydata.assign_to = [$rootScope.user_id];
                //});

                /*$scope.$watch($scope.activitydata, function() {
                    $scope.activitydata = {};
                    $scope.activitydata = { 
                                        client_id : results[0].client_id,
                                        activity_type : 'Site Visit',
                                        agreement_for : results[0].enquiry_for,
                                        enquiry_id : results[0].enquiry_id,
                                        property_id : property_id,
                                        buyer_id :results[0].client_id,
                                        assign_to : arr_assign_to,
                                        teams : arr_teams
                    };
                });*/
            });
        }, true);
    }

    if (category == 'contact')
    {
        
        $scope.activitydata.activity_type = 'Site Visit';
        $scope.activitydata.client_id = id;
    }

    $scope.activity_add_new = {activitydata:''};
    $scope.activity_add_new = function (activitydata) {
        console.log(activitydata.activity_type);
        console.log(activitydata.property_id);
        if ((activitydata.activity_type=='Site Visit' || activitydata.activity_type=='Property Visit') && (!activitydata.property_id))
        {
            // if (activitydata.project_id)
            if (activitydata.project_id || activitydata.enquiry_id || activitydata.property_id )
            {

            }
            else
            {
                alert("Selection of Property or Project or Enquary is mandatory.. !!!");
                // alert("Selection of Property or Project is mandatory.. !!!");
                return;
            }
        }
        if (activitydata.activity_type=='Meeting')
        {
            if (activitydata.client_id || activitydata.broker_id || activitydata.developer_id )
            {

            }
            else
            {
                alert("Selection of Client or Broker or Developer in mandatory.. !!!");
                return;
            }
        }
        $("#add-new-btn").css("display","none");
        Data.post('activity_add_new', {
            activitydata: activitydata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_activity').fileinput('upload');
                $location.path('activity_list/direct/0');
            }
        });
    };

    $scope.change_activity_sub_type = function (activity_type) 
    { 
        Data.get('change_activity_sub_type/'+activity_type).then(function (results) { 
            $scope.activity_sub_types = results; 
        });
    }
    $scope.GetProperties = function (enquiry_id)
    {
        $timeout(function () { 
            Data.get('getproperties_enquiries/'+enquiry_id).then(function (results) {
                $scope.m_properties = results;
            });
        }, 100);
    }
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
                        $scope.$watch($scope[controlvalue], function() {
                            $scope[controlvalue] = results;
                        }, true);
                    });
                }, 1000);
            }
        });
    };

    $scope.add_option = "Client";

    $scope.AddContact = function(add_option)
    {
        $scope.add_option = add_option;
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
        contact.contact_off = $scope.add_option;
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_company_logo').fileinput('upload');
                $('#file_visiting_card').fileinput('upload');
                $('#file_contact_pic').fileinput('upload');
                $("#adddeveloper").modal("hide");
                $scope.clients = {};
                Data.get('selectcontact/Client').then(function (results) {
                    $scope.clients = results;
                });
            }
        });
    };


});

app.controller('Activity_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var activity_id = $routeParams.activity_id;
    $scope.activePath = null;
    $scope.contact = {};
    $timeout(function () { 
        Data.get('selectdropdowns/ACTIVITY_TYPE').then(function (results) {
            $scope.factivity_type = results;
            
        });
    }, 100);
    $timeout(function () { 
        Data.get('selectdropdowns/ACTIVITY_SUB_TYPE').then(function (results) {
            $scope.activity_sub_types = results;
        });
    }, 100);
    $scope.activity = {};

    $scope.create_rights = false;
    $scope.create_developer = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    Data.get('session').then(function (results) {
        if (results.user_id) 
        {
            $str =  results.permissions;
            if ((($str).indexOf("activity_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("activity_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("activity_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("activity_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
            if ((($str).indexOf("contacts_developer_create"))!=-1)
            {
                $scope.create_developer = true;
                console.log($scope.create_developer);
            }
        
            if (!$scope.update_rights)
            {
                $scope.activity = {};
                alert("You don't have rights to use this option..");
                return;
            }
        }
    });
    /*$str = ($("#permission_string").val());
    if ((($str).indexOf("activity_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("activity_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("activity_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("activity_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.update_rights)
    {
        $scope.activity = {};
        alert("You don't have rights to use this option..");
        return;
    }*/
    

    

    $scope.change_activity_sub_type = function (activity_type) 
    { 
        Data.get('change_activity_sub_type/'+activity_type).then(function (results) { 
            $scope.activity_sub_types = results; 
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
        Data.get('selectenquiry_with_broker').then(function (results) {
            $scope.enquiries = results;
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

    $scope.change_sub_source = function (source_channel) 
    { 
        Data.get('change_sub_source/'+source_channel).then(function (results) { 
            $scope.sub_sources = results; 
        });
    }

    Data.get('getassignedenquiries').then(function (results) {
        $scope.selectenquiries = results;
    });
 

    Data.get('getassignedproperties').then(function (results) {
        $scope.m_properties = results;
    });

    Data.get('getassignedprojects').then(function (results) {
        $scope.m_projects = results;
    });

    $scope.activity = {};
    $scope.activity_images = {};
    Data.get('activity_edit_ctrl/'+activity_id).then(function (results) {
        $scope.arr = ((results[0].assign_to).split(','));
        results[0].assign_to = $scope.arr;

        $scope.arr = ((results[0].teams).split(','));
        results[0].teams = $scope.arr;
        if (results[0].project_id)
        {
            $scope.arr = ((results[0].project_id).split(','));
            results[0].project_id = $scope.arr;
        }
        $scope.arr = ((results[0].property_id).split(','));
        results[0].property_id = $scope.arr;

        $scope.arr = ((results[0].broker_id).split(','));
        results[0].broker_id = $scope.arr;


        //'activity_type','activity_sub_type','activity_start','activity_end','assign_to','broker_id','client_id','developer_id','enquiry_id','property_id','description', 'remind','remind_before','remind_time','status','teams',


        $scope.$watch($scope.activity, function() {
            $scope.activity = {};
            $scope.activity = {
                activity_type:results[0].activity_type,
                activity_sub_type:results[0].activity_sub_type,
                calling_count:results[0].calling_count,
                activity_start:results[0].activity_start,
                activity_end:results[0].activity_end,
                assign_to:results[0].assign_to,
                broker_id:results[0].broker_id,
                client_id:results[0].client_id,
                developer_id:results[0].developer_id,
                enquiry_id:results[0].enquiry_id,
                property_id:results[0].property_id,
                project_id:results[0].project_id,
                description:results[0].description,
                closure_comment:results[0].closure_comment,
                remind:results[0].remind,
                remind_before:results[0].remind_before,
                remind_time:results[0].remind_time,
                status:results[0].status,
                teams:results[0].teams,
                activity_id:results[0].activity_id
            }
        },true);
        //$timeout(function () { 
        //    Data.get('getproperties_enquiries/'+results[0].enquiry_id).then(function (results) {
                //$scope.m_properties = results;
        //    });
        //}, 100);
        $timeout(function () { 
            Data.get('activity_schedules/'+activity_id).then(function (results) {
                $scope.activity_schedules = results;
            });
        }, 100);
        Data.get('activity_images/'+activity_id).then(function (results) {
            $scope.activity_images = results;
        });
    });

    
    $scope.GetProperties = function (enquiry_id)
    {
        $timeout(function () { 
            Data.get('getproperties_enquiries/'+enquiry_id).then(function (results) {
                $scope.m_properties = results;
            });
        }, 100);
    }
    $scope.RemoveID = function(id)
    {
        console.log($scope.activity[id]);
        $scope.activity[id] = 0;
        console.log($scope.activity[id]);
    }

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
                        $scope.$watch($scope[controlvalue], function() {
                            $scope[controlvalue] = results;
                        }, true);
                    });
                }, 1000);
            }
        });
    };

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

    $scope.addschedule = function ()
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
            $timeout(function () { 
                Data.get('add_activity_schedules/'+activity_id+'/'+data).then(function (results) {
                    $rootScope.activity_schedules = results;
                });
            }, 100);
        }
    }
    $scope.Get_Activity_Schedule = function(activity_details_id)
    {
        $timeout(function () { 
            Data.get('get_activity_schedules/'+activity_details_id).then(function (results) {
                $scope.start_date = results[0].start_date;
                $scope.end_date = results[0].end_date;
                $scope.visitied_on = results[0].visited_on;
                $scope.description = results[0].description;
                $scope.status = results[0].status;
                $scope.activity_details_id = activity_details_id;
            });
        }, 100);
    }

    $scope.Update_Activity_Schedule = function()
    {
        $timeout(function () { 
            if ($scope.description)
            {
                
            }
            else
            {
                $scope.description = '--';
            }
            Data.get('update_activity_schedules/'+$scope.activity_details_id+'/'+$scope.start_date+'/'+$scope.end_date+'/'+$scope.visited_on+'/'+$scope.description+'/'+$scope.status+'/'+activity_id).then(function (results) {
                $scope.activity_schedules = results;
            });
        }, 100);
    }



    $scope.addschdulemanually = function(activity_id, manual_property_id)
    {
        $timeout(function () { 
            Data.get('addschdulemanually/'+activity_id+'/'+manual_property_id).then(function (results) {
                $scope.activity_schedules = results;
            });
        }, 100);
    }
    $timeout(function () { 
        Data.get('manualproperties').then(function (results) {
            $scope.manual_properties = results;
        });
    }, 100);

    $scope.activity_update = function (activity) {
        if (activity.activity_type=='Meeting')
        {
            if (activity.client_id || activity.broker_id || activity.developer_id )
            {

            }
            else
            {
                alert("Selection of Client or Broker or Developer in mandatory.. !!!");
                return;
            }
        }
        Data.post('activity_update', {
            activity: activity
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_activity').fileinput('upload');
                $location.path('activity_list/direct/0');
            }
        });
    };
    
    $scope.activity_delete = function (activity) {
        //console.log(business_unit);
        var deleteactivity = confirm('Are you absolutely sure you want to delete?');
        if (deleteactivity) {
            Data.post('activity_delete', {
                activity: activity
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('activity_list/direct/0');
                }
            });
        }
    };

    $scope.add_option = "Client";
    
    $scope.AddContact = function(add_option)
    {
        $scope.add_option = add_option;
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
        contact.contact_off = $scope.add_option;
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_company_logo').fileinput('upload');
                $('#file_visiting_card').fileinput('upload');
                $('#file_contact_pic').fileinput('upload');
                $("#adddeveloper").modal("hide");
                $scope.clients = {};
                Data.get('selectcontact/Client').then(function (results) {
                    $scope.clients = results;
                });
            }
        });
    };

    
});
    
app.controller('SelectActivity', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectactivity').then(function (results) {
            $scope.activities = results;
        });
    }, 100);
});


// TASK

app.controller('Task_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {
   
    $scope.searchdata = {};
    $scope.listtasks = {};
    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
    $scope.regular_list = "Yes";
    $scope.pagenavigation = function(which_side)
    {
        $scope.listtask = {};
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
            Data.get('task_list_ctrl/'+$scope.next_page_id).then(function (results) {
                $scope.listtasks = results;
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
            
            $scope.search_tasks($scope.searchdata,'pagenavigation');
            
        }
    }

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("task_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("task_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("task_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("task_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    
    if (!$scope.view_rights)
    {
        $scope.listtasks = {};
        alert("You don't have rights to use this option..");
        return;
    }
    $timeout(function () { 
        Data.get('task_list_ctrl/'+$scope.next_page_id).then(function (results) {
            $scope.listtasks = results;
            $scope.next_page_id = 30;
            $scope.task_count = results[0].task_count;
            $scope.total_records = results[0].task_count;
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
                Data.get('selectcontact/Client').then(function (results) {
                    $rootScope.clients = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectcontact/Broker').then(function (results) {
                    $rootScope.brokers = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectusers').then(function (results) {
                    $scope.users = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('selectteams').then(function (results) {
                    $scope.teams = results;
                });
            }, 100);


            $timeout(function () { 
                Data.get('selectcontact/Developer').then(function (results) {
                    $rootScope.developers = results;
                });
            }, 100);

        }
    };
    $scope.task_export = function()
    {
        $("#view_download").css("display","block");
        $timeout(function () { 
            Data.get('selectusers').then(function (results) {
                $scope.users = results;
            });
        }, 100);
        $scope.user_id = 0;
    }

    $scope.excel_download = function(from_date,to_date,user_id)
    {
        console.log(from_date);
        console.log(to_date);
        $("#view_download").css("display","none");

        tfrom_date = from_date.substr(6,4)+"-"+from_date.substr(3,2)+"-"+from_date.substr(0,2);
        tto_date = to_date.substr(6,4)+"-"+to_date.substr(3,2)+"-"+to_date.substr(0,2);
        Data.get('task_excel_download/'+tfrom_date+'/'+tto_date+'/'+user_id).then(function (results) {
            console.log(results);
            window.location="api//v1//uploads//task_list.xlsx";
        });

    }

    $scope.search_tasks = function (searchdata,from_click) 
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
        Data.post('search_tasks', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.listtasks, function() {
                $scope.listtasks = {};
                $scope.listtasks = results;
                $scope.task_count = results[0].task_count;
                $scope.total_records = results[0].task_count;
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                $scope.page_range = $scope.page_range + $scope.next_page_id;
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
        
        Data.get('task_list_ctrl/'+$scope.id+'/0').then(function (results) {
            $scope.listtasks = results;
            $scope.next_page_id = 30;
            $scope.task_count = results[0].task_count;
            $scope.total_records = results[0].task_count;
        });


    }
    $scope.show_task_comments = function(id)
    {
        option = "t";
        $timeout(function () { 
            Data.get('showcomments/'+id+'/'+option).then(function (results) {
                console.log(results);
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_task_comment = $sce.trustAsHtml($scope.html);
                $("#task_comment").css("display","block");
            });
        }, 100);
    }
    $scope.close_task_comment = function()
    {
        $("#task_comment").css("display","none");
    }

});    
    
    
app.controller('Task_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var id = $routeParams.id;
    console.log("user_id"+$rootScope.user_id);
    $scope.taskdata = {
        
    };
    $scope.$watch($scope.taskdata.assign_to, function() {
        $scope.taskdata.assign_to = [$rootScope.user_id];
    });
    
    $scope.$watch($scope.taskdata.teams, function() {
        $scope.taskdata.teams = [$rootScope.bo_id];
    });

    //$scope.taskdata.assign_to = $rootScope.user;
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    Data.get('session').then(function (results) {
        if (results.user_id) 
        {
            $str =  results.permissions;
            if ((($str).indexOf("task_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("task_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("task_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("task_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        
            if (!$scope.update_rights)
            {
                $scope.task = {};
                alert("You don't have rights to use this option..");
                return;
            }
        }
    });
    
    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.users = results;
        });
    }, 100);
    $timeout(function () { 
        Data.get('selectteams').then(function (results) {
            $scope.teams = results;
        });
    }, 100);

    
    $scope.clients = {};
    $timeout(function () { 
        Data.get('selectcontact/Client').then(function (results) {
            $scope.clients = results;
        });
    }, 100);



    $scope.task_add_new = {task:''};
    $scope.task_add_new = function (task) {
        $("#add-new-btn").css("display","none");
        Data.post('task_save_new', {
            task: task
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('task_list');
            }
        });
    };

    $scope.contact_save_new = function (contact) {
        contact.assign_to = [$rootScope.user_id];
        contact.teams = [$rootScope.bo_id];
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $("#addclient").css("display","none");
                contact_id = results.contact_id;
                console.log(contact_id);
                $scope.clients = {};
                Data.get('selectcontact/Client').then(function (results1) {
                    $scope.clients = results1;
                    $scope.task.client_id = 0;
                    $scope.$watch($scope.task.client_id, function() {
                        $scope.task.client_id = contact_id;
                        console.log($scope.task.client_id);
                        $(".select2").select2();
                    },true);
                },3000);
            }
        });
    };
    $scope.addclient = function()
    {
        $("#addclient").css("display","block");
    }
    $scope.close = function()
    {
        $("#addclient").css("display","none");
    }



});
    
app.controller('Task_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var task_id = $routeParams.task_id;
    $scope.activePath = null;
    $scope.contact = {};
    $scope.task = {};

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    Data.get('session').then(function (results) {
        if (results.user_id) 
        {
            $str =  results.permissions;
            if ((($str).indexOf("task_view"))!=-1)
            {
                $scope.view_rights = true;
                console.log($scope.view_rights);
            }
            if ((($str).indexOf("task_create"))!=-1)
            {
                $scope.create_rights = true;
                console.log($scope.create_rights);
            }
            if ((($str).indexOf("task_update"))!=-1)
            {
                $scope.update_rights = true;
                console.log($scope.update_rights);
            }
            if ((($str).indexOf("task_delete"))!=-1)
            {
                $scope.delete_rights = true;
                console.log($scope.delete_rights);
            }
        
            if (!$scope.update_rights)
            {
                $scope.task = {};
                alert("You don't have rights to use this option..");
                return;
            }
        }
    });
    
    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.users = results;
        });
    }, 100);
    $timeout(function () { 
        Data.get('selectteams').then(function (results) {
            $scope.teams = results;
        });
    }, 100);

    
    $scope.clients = {};
    $timeout(function () { 
        Data.get('selectcontact/all').then(function (results) {
            $scope.clients = results;
        });
    }, 100);

    $scope.taskdata = {};
    Data.get('task_edit_ctrl/'+task_id).then(function (results) {
        $scope.arr = ((results[0].assign_to).split(','));
        results[0].assign_to = $scope.arr;
        $scope.arr = ((results[0].teams).split(','));
        results[0].teams = $scope.arr;
        $scope.arr = ((results[0].sub_teams).split(','));
        results[0].sub_teams = $scope.arr;

        //'task_type','task_sub_type','task_start','task_end','assign_to','broker_id','client_id','developer_id','enquiry_id','property_id','description', 'remind','remind_before','remind_time','status','teams',


        $scope.$watch($scope.taskdata, function() {
            $scope.taskdata = {};
            $scope.taskdata = {
                task_title:results[0].task_title,
                description:results[0].description,
                assign_to:results[0].assign_to,
                client_id:results[0].client_id,
                status:results[0].status,
                task_status:results[0].task_status,
                teams:results[0].teams,
                sub_teams:results[0].sub_teams,
                task_id:results[0].task_id
            }
        });
        
    });
    
    $scope.task_update = function (taskdata) {
        Data.post('task_update', {
            taskdata: taskdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('task_list');
            }
        });
    };
    
    $scope.task_delete = function (taskdata) {
        //console.log(business_unit);
        var deletetask = confirm('Are you absolutely sure you want to delete?');
        if (deletetask) {
            Data.post('task_delete', {
                taskdata: taskdata
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('task_list');
                }
            });
        }
    };

    $scope.contact_save_new = function (contact) {
        contact.assign_to = [$rootScope.user_id];
        contact.teams = [$rootScope.bo_id];
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $("#addclient").css("display","none");
                contact_id = results.contact_id;
                console.log(contact_id);
                $scope.clients = {};
                Data.get('selectcontact/Client').then(function (results1) {
                    $scope.clients = results1;
                    $scope.task.client_id = 0;
                    $scope.$watch($scope.task.client_id, function() {
                        $scope.task.client_id = contact_id;
                        console.log($scope.task.client_id);
                        $(".select2").select2();
                    },true);
                },3000);
            }
        });
    };
    $scope.addclient = function()
    {
        $("#addclient").css("display","block");
    }
    $scope.close = function()
    {
        $("#addclient").css("display","none");
    }

    
});
    
app.controller('Selecttask', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selecttask').then(function (results) {
            $scope.task = results;
        });
    }, 100);
});


// REFERRALS

app.controller('Referrals_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce, $window ) {
    var cat = $routeParams.cat;
    $scope.cat = cat;
   
    $scope.searchdata = {};
    $scope.listreferrals = {};
    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
    $scope.regular_list = "Yes";
    
        // -------------------------------------pks start 17-07-2023--------------changes 22072023---------------
    $scope.loadReferralsData = function() {
         $scope.next_page_id -= 30;

        // Ensure $scope.next_page_id is not negative
        if ($scope.next_page_id < 0) {
            $scope.next_page_id = 0;
        }
        Data.get('referrals_list_ctrl/' + $scope.cat + '/' + $scope.next_page_id).then(function (results) {
            $scope.listreferrals = results;
            $scope.next_page_id = parseInt($scope.next_page_id)+30;
            $scope.referrals_count = results[0].referrals_count;
            $scope.total_records = results[0].referrals_count;
        });
    };
    
    $scope.loadSearchReferralsData = function() {
        // console.log(123456);
        $scope.next_page_id -= 30;
        // console.log("pksin gh");
        if ($scope.next_page_id < 0) {
            $scope.next_page_id = 0;
        }
        searchdata = $scope.searchdata;
        Data.post('search_referrals', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.listreferrals, function() {
                $scope.listreferrals = results;
                $scope.referrals_count = results[0].referrals_count;
                $scope.total_records = results[0].referrals_count;
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                // $scope.page_range = $scope.page_range + $scope.next_page_id; 
            },true);
        });
    };
    // -------------------------------------pks end-----------------------------

    $scope.pagenavigation = function(which_side)
    {
        $scope.listreferrals = {};
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
            Data.get('referrals_list_ctrl/'+$scope.cat+'/'+$scope.next_page_id).then(function (results) {
                $scope.listreferrals = results;
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
            
            $scope.search_referrals($scope.searchdata,'pagenavigation');
            
        }
    }

    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("referrals_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("referrals_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("referrals_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("referrals_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.view_rights = true;
    if (!$scope.view_rights)
    {
        $scope.listreferrals = {};
        alert("You don't have rights to use this option..");
        return;
    }


    $scope.close_otp = function()
    {
        $("#otp_div").css("display","none");
        $scope.otp = "";
        $('.modal-backdrop').hide();

    }

    $timeout(function () { 
        Data.get('create_otp').then(function (results) {
            Data.toast(results);
        });
    }, 100);

    $scope.verify_otp = function(otp)
    {
        if( otp!=undefined){


        Data.get('verify_otp/'+otp).then(function (results) {
           console.log(results);
            Data.toast(results);
            if (results.status == "success") 
            {
            $('.modal-backdrop').hide();              
                $("#otp_div").css("display","none");
                $scope.otp = "";
                $timeout(function () { 
                    Data.get('referrals_list_ctrl/'+$scope.cat+'/0').then(function (results) {
                        $scope.listreferrals = results;
                        $scope.next_page_id = 30;
                        $scope.referrals_count = results[0].referrals_count;
                        $scope.total_records = results[0].referrals_count;
                    });
                }, 100);
            }
        }); 
    }
        else{
            Data.toast({status:"error",message:"Please Enter Valid  OTP"});        
        }    
        // }   
    }


    var values_loaded = "false";
    $scope.open_search = function()
    {
        if (values_loaded=="false")
        {
            values_loaded="true";
            console.log("opening");
            
            $timeout(function () { 
                Data.get('getdatavalues_referrals/'+$scope.cat+'/broker_id').then(function (results) {
                    $scope.broker_ids = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals/'+$scope.cat+'/assigned_to').then(function (results) {
                    $scope.assigned_tos = results;
                });     
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals/'+$scope.cat+'/teams').then(function (results) {
                    $scope.teams = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals/'+$scope.cat+'/sub_team').then(function (results) {
                    $scope.sub_teams = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals/'+$scope.cat+'/groups_names').then(function (results) {
                    $scope.groups_namess = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals/'+$scope.cat+'/sub_group').then(function (results) {
                    $scope.sub_groups = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals/'+$scope.cat+'/mobile_no').then(function (results) {
                    $scope.mobile_nos = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals/'+$scope.cat+'/email').then(function (results) {
                    $scope.emails = results;
                });
            }, 100);

        }
    };

    $scope.search_referrals = function (searchdata,from_click) 
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
        searchdata.cat = $scope.cat;
        Data.post('search_referrals', {
            searchdata: searchdata
        }).then(function (results) {
            // alert(results);
            $scope.$watch($scope.listreferrals, function() {
                $scope.listreferrals = {};
                $scope.listreferrals = results;
                $scope.referrals_count = results[0].referrals_count;
                $scope.total_records = results[0].referrals_count;
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                $scope.page_range = $scope.page_range + $scope.next_page_id; 
            },true);
        });
    };

    $scope.convertdata = {};
    $scope.uploadreferrals_data = function (convertdata) {
        //convertdata.file_name = $("#file_name").val();
        var currentdate = new Date(); 
        var datetime = currentdate.getFullYear()+ "-" + (currentdate.getMonth()+1) + "-" +  currentdate.getDate()+ " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
        convertdata.created_date = datetime;
        convertdata.file_name = $("#file_name_imports").val();
        convertdata.contact_off = $scope.cat;
        Data.post('uploadreferrals_data', {
            convertdata: convertdata
        }).then(function (results) {
            Data.toast(results);
            $scope.html = results.htmlstring;
            $scope.trustedHtml_uploadeddata = $sce.trustAsHtml($scope.html);
            if (results.status == "success") {
                $timeout(function () { 
                    Data.get('referrals_list_ctrl/'+$scope.cat+'/'+$scope.next_page_id).then(function (results) {
                        $scope.listreferrals = results;
                        $scope.next_page_id = 30;
                        $scope.referrals_count = results[0].referrals_count;
                        $scope.total_records = results[0].referrals_count;
                    });
                }, 100);
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
        $("li.select2-selection__choice").remove();
        $(".select2").each(function() { $(this).val([]); });

        $scope.next_page_id = 0;
        
        Data.get('referrals_list_ctrl/'+$scope.cat+'/0').then(function (results) {
            $scope.listreferrals = results;
            $scope.next_page_id = 30;
            $scope.referrals_count = results[0].referrals_count;
            $scope.total_records = results[0].referrals_count;
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

    $scope.reassign_to = "";
    $scope.setreassign_to = function()
    {
        $("#setreassign_to").css("display","block");
    }

    $scope.closereassign_to = function()
    {
        $("#setreassign_to").css("display","none");
    }

    $scope.change_assign_to = function ()
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
            $("#setreassign_to").css("display","none");
            var delete_selected = confirm('Are you sure you want to reassign these IDS '+data+' with ' +$scope.reassign_to +' ? ');
            if (delete_selected) 
            {
                
                Data.get('change_assign_to_referral/'+data+'/'+$scope.reassign_to).then(function (results) {
                    Data.toast(results);
                    $scope.next_page_id = 0;        
                    Data.get('referrals_list_ctrl/'+$scope.cat+'/0').then(function (results) {
                        $scope.listreferrals = results;
                        $scope.next_page_id = 30;
                        $scope.referrals_count = results[0].referrals_count;
                        $scope.total_records = results[0].referrals_count;
                    });
                });
                
            }
        }
    }

    $scope.delete_selected = function ()
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
            var delete_selected = confirm('Are you sure to delete selected records ? ');
            if (delete_selected) 
            {
                
                Data.get('delete_selected_referrals/'+data).then(function (results) {
                    Data.toast(results);
                    $scope.next_page_id = 0;        
                    Data.get('referrals_list_ctrl/'+$scope.cat+'/0').then(function (results) {
                        $scope.listreferrals = results;
                        $scope.next_page_id = 30;
                        $scope.referrals_count = results[0].referrals_count;
                        $scope.total_records = results[0].referrals_count;
                    });
                });
                
            }
        }
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
            $location.path('mails_client/referrals/'+data);
        }
    }
    $scope.send_smss = function ()
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
            $scope.SendSMS("referrals",data);
        }
    }
    $scope.sms_data = {};

    $scope.SendSMS = function(cat,id)
    {
        console.log(cat);
        console.log(id);
        $scope.sms_data.receipient = "";
        $scope.sms_data.category_id = id;
        $scope.sms_data.category = "referrals";
        $scope.sms_data.id = id;
        Data.get('getsms_data/referrals/'+id).then(function (results) {
            console.log(results[0].mobile_nos);
            $scope.sms_data.receipient = results[0].mobile_nos;
        });
    }

    $scope.smssend = function(sms_data)
    {
        console.log(sms_data.text_message);
        var smssending = confirm('Want to send SMS ? ');
        if (smssending) 
        {
            console.log(sms_data);
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
    $scope.wa_data = {};

    $scope.send_wamessage = function(cat,id)
    {
        console.log(cat);
        console.log(id);
        $scope.wa_data.receipient = "";
        $scope.wa_data.category_id = id;
        $scope.wa_data.category = "referrals";
        $scope.wa_data.id = id;
        Data.get('getwa_data/referrals/'+id).then(function (results) {
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
    $scope.whatsapp_message = function(message,receipient)
    {
        console.log(message);
        $window.open("https://api.whatsapp.com/send?phone=91"+receipient+"&text="+message, "_target");
    }

    $scope.referrals_id = 0;
    $scope.add_comments = function(referrals_id)
    {
        $scope.referrals_id = referrals_id;
        $("#referrals_comment").css("display","block");
    }
    
    $scope.save_referrals_comment = function(closure_comment,next_date)
    {
        // console.log(next_date);

        referrals_id = $scope.referrals_id;
        tnext_date = next_date.substr(6,4)+"-"+next_date.substr(3,2)+"-"+next_date.substr(0,2)+" "+next_date.substr(11,2)+":"+next_date.substr(14,2);
        Data.get('save_referrals_comment/'+referrals_id+'/'+closure_comment+'/'+tnext_date).then(function (results) {
            // pks add 17/02/2023
            // $window.location.reload();
            // $scope.loadReferralsData();
            if (!$scope.searchdata || Object.keys($scope.searchdata) == 0 || typeof $scope.searchdata  === 'undefined') {
                // console.log("pks1224");
                // console.log(123);
                $scope.loadReferralsData();
            } else {
                // console.log(456);
                // console.log("pks1225");
                $scope.loadSearchReferralsData();
            }
        });
        $("#referrals_comment").css("display","none");
        $scope.closure_comment = "";
        $scope.next_date = "";
    }

    $scope.close_referrals_comment = function()
    {
        $("#referrals_comment").css("display","none");
        $scope.closure_comment = "";
        $scope.next_date = "";
    }

    $scope.show_history_referrals = function(id)
    {
        console.log(id);
        $timeout(function () { 
            Data.get('show_history_referrals/'+id).then(function (results) {
                $scope.html = results[0].htmlstring;
                var cstring = 'trustedHtml_h'+id;
                $scope[cstring] = $sce.trustAsHtml($scope.html);
            });
        }, 100);
    }

    $scope.create_referral_client = function(referrals_id)
    {
        console.log(referrals_id);
        Data.get('create_referral_client/'+referrals_id).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                //$location.path('document_list'); 
            }
        });
    }
    $scope.create_referral_enquiry = function(referrals_id)
    {
        console.log(referrals_id);
        Data.get('create_referral_client/'+referrals_id).then(function (results) {
            Data.toast(results);
            $location.path('enquiries_add/residential'); 
        });
    }
    $scope.create_referral_property = function(referrals_id)
    {
        console.log(referrals_id);
        Data.get('create_referral_client/'+referrals_id).then(function (results) {
            Data.toast(results);
            $location.path('properties_add/residential');
        });
    }
    $scope.create_referral_task = function(referrals_id)
    {
        console.log(referrals_id);
        Data.get('create_referral_client/'+referrals_id).then(function (results) {
            Data.toast(results);
            $location.path('task_add');
        });
    }

    $scope.change_referral_status = function(status,referrals_id)
    {
        console.log(referrals_id);
        Data.get('change_referral_status/'+status+'/'+referrals_id).then(function (results) {
            
        });
    }


});   

app.controller('Referrals_Property_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce, $window ) {
    
   
    $scope.searchdata = {};
    $scope.listreferrals = {};
    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
    $scope.regular_list = "Yes";
    $scope.otp_ref="0000";
    $scope.pagenavigation = function(which_side)
    {
        $scope.listreferrals = {};
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
            Data.get('referrals_property_ctrl/'+$scope.next_page_id+'/'+$scope.otp_ref).then(function (results) {

                
                if (results.status == "error") 
                {
                    Data.toast(results);

                    return;
                }
                $scope.listreferrals = results;
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
            
            //$scope.search_referrals($scope.searchdata,'pagenavigation');
            $scope.searchall($scope.searchdata,'pagenavigation');
        }
    }
    
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("referrals_property_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("referrals_property_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("referrals_property_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("referrals_property_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }
    $scope.view_rights = true;
    if (!$scope.view_rights)
    {
        $scope.listreferrals = {};
        alert("You don't have rights to use this option..");
        return;
    }


    $scope.close_otp = function()
    {
        $("#otp_div").css("display","none");
        $scope.otp = "";
        $('.modal-backdrop').hide();     
    }
    
    $timeout(function () { 
        Data.get('create_propertydata_otp').then(function (results) {
            Data.toast(results);
        });
    }, 100);
    
    $scope.verify_otp = function(otp)
    {

        if( otp!=undefined){
        

        $scope.otp_ref = otp;
        Data.get('verify_propertydata_otp/'+otp).then(function (results) {
            Data.toast(results);
            //alert(results.status);
            if (results.status == "success") 
            {
                $('.modal-backdrop').hide();
                $("#otp_div").css("display","none");
                $scope.otp = "";
                $timeout(function () { 
                    Data.get('referrals_property_ctrl/0/'+$scope.otp_ref).then(function (results) {
                        if (results.status == "error") 
                        {
                            Data.toast(results);

                            return;
                        }
                        $scope.listreferrals = results;
                        $scope.next_page_id = 30;
                        $scope.referrals_count = results[0].referrals_count;
                        $scope.total_records = results[0].referrals_count;
                    });
                }, 100);
            }
        }); 
    }
    
        else{
            Data.toast({status:"error",message:"Please Enter Valid OTP"});
        
        }
   
        
    }


    var values_loaded = "false";
    $scope.open_search = function()
    {
        if (values_loaded=="false")
        {
            values_loaded="true";
            console.log("opening");
            
            $timeout(function () { 
                Data.get('getdatavalues_referrals_property/contact_person').then(function (results) {
                    $scope.contact_persons = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals_property/mobile_no').then(function (results) {
                    $scope.mobile_no = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals_property/email_id').then(function (results) {
                    $scope.email_ids = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_referrals_property/building_name').then(function (results) {
                    $scope.building_names = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals_property/assign_to').then(function (results) {
                    $scope.assign_tos = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals_property/team').then(function (results) {
                    $scope.teams = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_referrals_property/sub_team').then(function (results) {
                    $scope.sub_teams = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_referrals_property/group_name').then(function (results) {
                    $scope.group_names = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_referrals_property/sub_group').then(function (results) {
                    $scope.sub_groups = results;
                });
            }, 100);

            

        }
    };

    $scope.change_sub_group = function (group_name) 
    {
        Data.get('change_sub_group/'+group_name).then(function (results) {
            $scope.sub_groups = results;
        });
    }

    $scope.search_referrals = function (searchdata,from_click) 
    {
        // console.log("test");
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
        searchdata.cat = $scope.cat;
        searchdata.otp = $scope.otp_ref;
        Data.post('search_referrals_property', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.listreferrals, function() {
                $scope.listreferrals = {};
                if (results.status == "error") 
                {
                    Data.toast(results);

                    return;
                }
                $scope.listreferrals = results;
                $scope.referrals_count = results[0].referrals_count;
                $scope.total_records = results[0].referrals_count;
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                $scope.page_range = $scope.page_range + $scope.next_page_id;
            },true);
        });
    };


    $scope.searchall = function(searchdata,from_click)
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
        searchdata.cat = $scope.cat;
        searchdata.otp = $scope.otp_ref;
        //searchdata.find_what = $scope.find_what;
        Data.post('search_referrals_propertyall', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.listreferrals, function() {
                $scope.listreferrals = {};
                if (results.status == "error") 
                {
                    Data.toast(results);

                    return;
                }
                $scope.listreferrals = results;
                $scope.referrals_count = results[0].referrals_count;
                $scope.total_records = results[0].referrals_count;
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                $scope.page_range = $scope.page_range + $scope.next_page_id;
            },true);
        });
    }


    $scope.convertdata = {};
    $scope.uploadreferrals_property_data = function (convertdata) {
        //convertdata.file_name = $("#file_name").val();
        var currentdate = new Date(); 
        var datetime = currentdate.getFullYear()+ "-" + (currentdate.getMonth()+1) + "-" +  currentdate.getDate()+ " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
        convertdata.created_date = datetime;
        convertdata.file_name = $("#file_name_imports").val();
        convertdata.contact_off = $scope.cat;
        Data.post('uploadreferrals_property_data', {
            convertdata: convertdata
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $timeout(function () { 
                    Data.get('referrals_property_ctrl/'+$scope.next_page_id+'/'+$scope.otp_ref).then(function (results) {
                        if (results.status == "error") 
                        {
                            Data.toast(results);

                            return;
                        }
                        $scope.listreferrals = results;
                        $scope.next_page_id = 30;
                        $scope.referrals_count = results[0].referrals_count;
                        $scope.total_records = results[0].referrals_count;
                    });
                }, 100);
            }
        });
    };


    /*$timeout(function () { 
        Data.get('referrals_property_ctrl/0').then(function (results) {
            $scope.listreferrals = results;
            $scope.next_page_id = 30;
            $scope.referrals_count = results[0].referrals_count;
            $scope.total_records = results[0].referrals_count;
        });
    }, 100);*/

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
        
        Data.get('referrals_property_ctrl/0'+'/'+$scope.otp_ref).then(function (results) {
            if (results.status == "error") 
            {
                Data.toast(results);

                return;
            }
            $scope.listreferrals = results;
            $scope.next_page_id = 30;
            $scope.referrals_count = results[0].referrals_count;
            $scope.total_records = results[0].referrals_count;
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

    $scope.delete_selected = function ()
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
            var delete_selected = confirm('Are you sure to delete selected records ? ');
            if (delete_selected) 
            {
                
                Data.get('delete_selected_propertydata/'+data).then(function (results) {
                    Data.toast(results);
                    $scope.next_page_id = 0;        
                    Data.get('referrals_property_ctrl/0/0').then(function (results) {

                        $scope.listreferrals = results;
                        $scope.next_page_id = 30;
                        $scope.referrals_count = results[0].referrals_count;
                        $scope.total_records = results[0].referrals_count;
                    });
                });
                
            }
        }
    }
    $scope.reassign_to = "";
    $scope.setreassign_to = function()
    {
        $("#setreassign_to").css("display","block");
    }

    $scope.closereassign_to = function()
    {
        $("#setreassign_to").css("display","none");
    }

    $scope.change_assign_to = function ()
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
            $("#setreassign_to").css("display","none");
            var delete_selected = confirm('Are you sure you want to reassign these IDS '+data+' with ' +$scope.reassign_to +' ? ');
            if (delete_selected) 
            {
                
                Data.get('change_assign_to/'+data+'/'+$scope.reassign_to).then(function (results) {
                    Data.toast(results);
                    $scope.next_page_id = 0;        
                    Data.get('referrals_property_ctrl/0/0').then(function (results) {
                        $scope.listreferrals = results;
                        $scope.next_page_id = 30;
                        $scope.referrals_count = results[0].referrals_count;
                        $scope.total_records = results[0].referrals_count;
                    });
                });
                
            }
        }
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
            $location.path('mails_client/referrals_property/'+data);
        }
    }
    $scope.send_smss = function ()
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
            $scope.SendSMS("referrals_property",data);
        }
    }
    $scope.sms_data = {};

    $scope.SendSMS = function(cat,id)
    {
        console.log(cat);
        console.log(id);
        $scope.sms_data.receipient = "";
        $scope.sms_data.category_id = id;
        $scope.sms_data.category = "referrals";
        $scope.sms_data.id = id;
        Data.get('getsms_data/referrals_property/'+id).then(function (results) {
            console.log(results[0].mobile_nos);
            $scope.sms_data.receipient = results[0].mobile_nos;
        });
    }

    $scope.smssend = function(sms_data)
    {
        console.log(sms_data.text_message);
        var smssending = confirm('Want to send SMS ? ');
        if (smssending) 
        {
            console.log(sms_data);
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
            $scope.send_wamessage("referrals_property",data);
        }
    }
    $scope.wa_data = {};

    $scope.send_wamessage = function(cat,id)
    {
        console.log(cat);
        console.log(id);
        $scope.wa_data.receipient = "";
        $scope.wa_data.category_id = id;
        $scope.wa_data.category = "referrals";
        $scope.wa_data.id = id;
        Data.get('getwa_data/referrals_property/'+id).then(function (results) {
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
    $scope.whatsapp_message = function(message,receipient)
    {
        console.log(message);
        $window.open("https://api.whatsapp.com/send?phone=91"+receipient+"&text="+message, "_target");
    }

    $scope.referrals_property_id = 0;
    $scope.add_comments = function(referral_property_id)
    {
        $scope.referral_property_id = referral_property_id;
        $("#referrals_comment").css("display","block");
    }
    
    $scope.save_referrals_property_comment = function(closure_comment,next_date)
    {
        console.log(next_date);

        referral_property_id = $scope.referral_property_id;
        tnext_date = next_date.substr(6,4)+"-"+next_date.substr(3,2)+"-"+next_date.substr(0,2)+" "+next_date.substr(11,2)+":"+next_date.substr(14,2);
        Data.get('save_referrals_property_comment/'+referral_property_id+'/'+closure_comment+'/'+tnext_date).then(function (results) {
            
        });
        $("#referrals_comment").css("display","none");
        $scope.closure_comment = "";
        $scope.next_date = "";
    }

    $scope.close_referrals_property_comment = function()
    {
        $("#referrals_comment").css("display","none");
        $scope.closure_comment = "";
        $scope.next_date = "";
    }

    $scope.show_history_referral_property = function(id)
    {
        console.log(id);
        $timeout(function () { 
            Data.get('show_history_referral_property/'+id).then(function (results) {
                $scope.html = results[0].htmlstring;
                var cstring = 'trustedHtml_h'+id;
                $scope[cstring] = $sce.trustAsHtml($scope.html);
            });
        }, 100);
    }

    $scope.create_referral_client = function(referral_property_id)
    {
        console.log(referral_property_id);
        Data.get('create_referral_client/'+referral_property_id).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                //$location.path('document_list'); 
            }
        });
    }
    $scope.create_referral_enquiry = function(referral_property_id)
    {
        console.log(referral_property_id);
        Data.get('create_referral_property_client/'+referral_property_id).then(function (results) {
            Data.toast(results);
            $location.path('enquiries_add/residential'); 
        });
    }
    $scope.create_referral_property = function(referral_property_id)
    {
        $location.path('properties_add/residential');
       
    }
    $scope.create_referral_task = function(referral_property_id)
    {
        $location.path('task_add');
        
    }

    $scope.delete_referral_property = function(referral_property_id)
    {

        var deletetdocument = confirm('Are you absolutely sure you want to delete?');
        if (deletetdocument) {
            Data.get('delete_referral_property/'+referral_property_id).then(function (results) {
               
                
                $timeout(function () { 
                    Data.get('referrals_property_ctrl/0').then(function (results) {
                        $scope.listreferrals = results;
                        $scope.next_page_id = 30;
                        $scope.referrals_count = results[0].referrals_count;
                        $scope.total_records = results[0].referrals_count;
                    });
                }, 100);
            });
        }
    }

    $scope.change_referral_property_status = function(status,referral_property_id)
    {
        console.log(referral_property_id);
        Data.get('change_referral_property_status/'+status+'/'+referral_property_id).then(function (results) {
            
        });
    }


});   


app.controller('GetCallingData_List', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('getcallingdata_list').then(function (results) {
            $scope.callingdata_list = results;
        });
    }, 100);
});


app.controller('CallingHistory_List', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce, $interval ) 
{
    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
    $scope.regular_list = "Yes";
    $scope.find_what = "";
    $scope.call_arrived = "Waiting for Incoming Call";
    $scope.call_id = 0;
    $scope.getsearch_client_history = function()
    {
        console.log($scope.find_what);
        $timeout(function () { 
            Data.get('callinghistory_list', { phoneNumber: $scope.find_what }).then(function (results) {
                $scope.callinghistory_list = results;
                $("#search_result").css("display","none");
                $("#callinghistory_div").css("display","block");
            });
        }, 100);
    }
    
    // $scope.getsearch_client_history = function()
    // {
    //     $timeout(function () { 
    //         Data.get('callinghistory_list').then(function (results) {
    //             $scope.callinghistory_list = results;
    //             $("#search_result").css("display","none");
    //             $("#callinghistory_div").css("display","block");
    //         });
    //     }, 100);

    // }
    $scope.close = function()
    {
        $("#search_result").css("display","none"); 
    }

    $scope.search_client_information = function(find_what)
    {
        $timeout(function () { 
            Data.get('search_client_information/'+find_what).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_searchall = $sce.trustAsHtml($scope.html);
                $("#search_result").css("display","block");
                $("#callinghistory_div").css("display","none");
                $scope.call_arrived = "Waiting for Incoming Call.....";  
                $scope.process_interval();
            });
        }, 100);
    }
    $diff_in_seconds = "";
    // $scope.process_interval = function()
    // {
    //     var interval = $interval(function() {
    //         console.log('say hello');
    //         $timeout(function () { 
    //             Data.get('getlivecall').then(function (results) {
    //                 if (results[0].caller_id_number>0)
    //                 {
    //                     $interval.cancel(interval); 
    //                     $scope.call_arrived = "Received Incoming Call...";                
    //                     $scope.find_what = results[0].caller_id_number;
    //                     $scope.call_id = results[0].call_id;
    //                     $scope.diff_in_seconds = results[0].diff_in_seconds;
    //                     $scope.search_client_information(results[0].caller_id_number);
    //                     console.log($scope.find_what);
    //                     console.log($scope.diff_in_seconds);
    //                 } 
    //             });
    //         }, 100);
    //     }, 3500);
    // }
    
    $scope.process_interval = function() {
        var interval = $interval(function() {
            Data.get('getlivecall').then(function(results) {
                if (results && results.length > 0 && results[0].caller_id_number > 0) {
                    $interval.cancel(interval);
                    $scope.call_arrived = "Received Incoming Call...";
                    $scope.find_what = results[0].caller_id_number;
                    $scope.call_id = results[0].call_id;
                    $scope.diff_in_seconds = results[0].diff_in_seconds;
                    $scope.search_client_information(results[0].caller_id_number);
                    console.log($scope.find_what);
                    console.log($scope.diff_in_seconds);
                }
            }).catch(function(error) {
                // Handle the error if necessary
                console.error(error);
            });
        }, 3500);
    };
    $scope.process_interval();
    //var intervalListener = $interval(updateTaxies, 2000);
    $scope.$on('$destroy', function() {
        $interval.cancel(interval);
    });

    //$interval.cancel(interval);
    
    $scope.save_callinguser_comment = function(user_comment)
    {
        Data.get('save_callinguser_comment/'+$scope.call_id+'/'+user_comment).then(function (results) {
            Data.toast(results);
        });
    }

    $scope.update_calling_status = function(call_status)
    {
        Data.get('update_calling_status/'+$scope.call_id+'/'+call_status).then(function (results) {            
        });
    }
    
});
// DOCUMENT

app.controller('Document_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('document_list_ctrl').then(function (results) {
            $rootScope.tdocuments = results;
        });
    }, 100);

    $scope.document_delete = function (tdocument_id) {
        //console.log(business_unit);
        var deletetdocument = confirm('Are you absolutely sure you want to delete?');
        if (deletetdocument) {
            Data.get('document_delete/'+tdocument_id).then(function (results) {
                $rootScope.tdocuments = {};
                $timeout(function () { 
                    Data.get('document_list_ctrl').then(function (results) {
                        $rootScope.tdocuments = results;
                    });
                }, 100);
            });
        }
    };

});
    
    
app.controller('Document_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    
    $timeout(function () { 
        Data.get('selectsubteams').then(function (results) {
            $scope.sub_teams = results;
        });
    }, 100);

    $scope.document_add_new = {tdocument:''};
    $scope.document_add_new = function (tdocument) {
        tdocument.file_name = $("#file_name").val();
        Data.post('document_add_new', {
            tdocument: tdocument
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file-1').fileinput('upload');
                $location.path('document_list');
            }
        });
    };
    
});
    
app.controller('Document_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var tdocument_id = $routeParams.tdocument_id;
    $scope.activePath = null;
    $timeout(function () { 
        Data.get('selectsubteams').then(function (results) {
            $scope.sub_teams = results;
        });
    }, 100);
    
    Data.get('document_edit_ctrl/'+tdocument_id).then(function (results) {
        $scope.tdocuments = results;
    });
    
    
    $scope.document_update = function (tdocument) {
        Data.post('document_update', {
            tdocument: tdocument
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('document_list');
            }
        });
    };
    
    $scope.document_delete = function (tdocument) {
        //console.log(business_unit);
        var deletetdocument = confirm('Are you absolutely sure you want to delete?');
        if (deletetdocument) {
            Data.post('document_delete', {
                tdocument: tdocument
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('document_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectDocument', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectdocument').then(function (results) {
            $rootScope.tdocuments = results;
        });
    }, 100);
});

app.controller('Showdocuments', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('showdocuments').then(function (results) {
            $scope.showdocuments = results;
        });
    }, 100);

    /*$scope.document_delete = function (tdocument_id) {
        //console.log(business_unit);
        var deletetdocument = confirm('Are you absolutely sure you want to delete?');
        if (deletetdocument) {
            Data.get('document_delete/'+tdocument_id).then(function (results) {
                $rootScope.tdocuments = {};
                $timeout(function () { 
                    Data.get('document_list_ctrl').then(function (results) {
                        $rootScope.tdocuments = results;
                    });
                }, 100);
            });
        }
    };*/

});


// ALERTS

app.controller('Alerts_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('alerts_list_ctrl').then(function (results) {
            $rootScope.alerts = results;
        });
    }, 100);
});
    
    
app.controller('Alerts_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    $scope.alerts_add_new = {alert:''};
    $scope.alerts_add_new = function (alert) {
        Data.post('alerts_add_new', {
            alerts: alerts
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('alerts_list');
            }
        });
    };
});
    
app.controller('Alerts_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var alert_id = $routeParams.alert_id;
    $scope.activePath = null;
    
    Data.get('alerts_edit_ctrl/'+alert_id).then(function (results) {
        $rootScope.alerts = results;
    });
    
    
    $scope.alerts_update = function (alert) {
        Data.post('alerts_update', {
            alert: alert
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('alerts_list');
            }
        });
    };
    
    $scope.alerts_delete = function (alerts) {
        //console.log(business_unit);
        var deletealerts = confirm('Are you absolutely sure you want to delete?');
        if (deletealerts) {
            Data.post('alerts_delete', {
                alert: alert
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('alerts_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectAlerts', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectalerts').then(function (results) {
            $rootScope.alerts = results;
        });
    }, 100);
});




// GENERATE FORM
    
app.controller('Generate_Form', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, $sce, Data) {
    $scope.activePath = null;
    Data.get('tablelist').then(function (results) {
        $rootScope.tablelists = results;
    });
    
    $scope.showcolumns = function (table_name) {
        
        $("#show").css("display","block");
        $("#showhtml").css("display","none");

        Data.get('showcolumns/'+table_name).then(function (results) {
            $rootScope.columnlists = results;
        });
    };

    $scope.update_direct = function(column_name,form_id,value)
    {
        Data.get('update_direct/'+column_name+'/'+form_id+'/'+value).then(function (results) {

        });
    }
    /*$scope.select_unselect = function(isChecked)
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

    };*/

    $scope.generateform = function(table_name)
    {
        /*var data = '';
        $(":checked.check_element").each(
            function(index) {
                data = data+this.value+',';
            }
        );
        
        console.log(data);
        console.log(table_name);*/
        $timeout(function () { 
            Data.get('generateform/'+table_name).then(function (results) {
                $("#show").css("display","none");
                $("#showhtml").css("display","block");
                $scope.html = results[0].htmlstring;
                $scope.jsscript = results[0].jsscript;
                $scope.phpcode = results[0].phpcode;
            });
        }, 100);
    };
});


app.controller('SelectBusiness_Unit', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectbusiness_unit').then(function (results) {
        $rootScope.business_units = results;
        });
    }, 100);
});



app.controller('SelectUserRights', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectuserrights').then(function (results) {
            $rootScope.user_rights = results;
        });
    }, 100);
});

app.controller('User_Rights_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {

    $scope.new_user_role = function (new_role)
    {
        $timeout(function () { 
            Data.get('user_rights_ctrl/'+new_role).then(function (results) {
				$rootScope.user_rights = {};
				$scope.$apply();
                $rootScope.user_rights = results;
				$scope.$apply();
            });
        }, 100);
    };
    
    $scope.show_user_rights = function (user_rights_id)
    {
        $scope.user_rights_id = user_rights_id;
        $timeout(function () { 
            Data.get('show_user_rights/'+user_rights_id).then(function (results) {
				$rootScope.allowed_activities = {};
				$scope.$apply();
                $rootScope.allowed_activities = results;
				$scope.$apply();
            });
        }, 100);
    };
    
    $scope.show_user_rights_bu = function (user_rights_id,bu_id)
    {
        $scope.user_rights_id = user_rights_id;
        $scope.bu_id = bu_id;
        $timeout(function () { 
            Data.get('show_user_rights_bu/'+user_rights_id+'/'+bu_id).then(function (results) {
				$rootScope.allowed_activities = {};
				$scope.$apply();
                $rootScope.allowed_activities = results;
				$scope.$apply();
            });
        }, 100);
    };
    
    $scope.delete_user_rights = function (allowed_activities_id)
    {
        $user_rights_id = $scope.user_rights_id;
        $timeout(function () { 
            Data.get('delete_user_rights/'+allowed_activities_id+'/'+$user_rights_id).then(function (results) {
				$rootScope.allowed_activities = {};
				$scope.$apply();
                $rootScope.allowed_activities = results;
				$scope.$apply();
            });
        }, 100);
    };
    
    $scope.user_rights_add = function (user_rights_id, bu_id)
    {
        $scope.bu_id = bu_id;
        $timeout(function () { 
            Data.get('get_menu_options').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml = $sce.trustAsHtml($scope.html);
                $("#show_menu_options").modal('show');
            });
        }, 100);
        
        $scope.selectmenuoptions = function()
        {
            var data = '';
            $(":checked.check_element").each(
                function(index) {
                    data = data+this.value+',';
                }
            );
            //console.log(data);
            $timeout(function () { 
                Data.get('show_user_rights/'+user_rights_id+'/'+data+'/'+bu_id).then(function (results) {
                    $rootScope.allowed_activities = results;
                });
            }, 100);
        };
    };
});

app.controller('Change_Password', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    $scope.activePath = null;
    $scope.change_password_update = function (user) {
        Data.post('change_password_update', {
            user: user
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('/home');
            }
        });
    };
});

app.controller('ForgetPassword', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    $scope.activePath = null;
    $scope.forgetpassword = function (user) {
        var sendrequest = confirm('This will reset your Password and Accordingly you will receive a mail on your mail id, Are you Sure ?');
        if (sendrequest) 
        {
            Data.post('forgetpassword', {
                user: user
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('/login');
                }
            });
        }
    };
});





// DESIGNATION

app.controller('SelectDesignation', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {
    $timeout(function () { 
        Data.get('selectdesignation').then(function (results) {
            $rootScope.designations = results;
        });
    }, 100);
});

// Team Member

app.controller('SelectTeamMember', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce ) {
    $timeout(function () { 
        Data.get('selectteammember').then(function (results) {
            $rootScope.teammembers = results;
        });
    }, 100);
});

app.controller('DoNothing', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce, $interval) {
    $scope.donothing = function()
    {
        Data.get('donothing').then(function (results) {
        });
    }
});


app.controller('testing', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce, $interval) {
    Data.get('testing').then(function (results) {
    });
});

app.controller('SelectUsers', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $rootScope.users = results;
        });
    }, 100);
});

// DROPDOWNS

app.controller('Dropdowns_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('dropdowns_list_ctrl').then(function (results) {
            $scope.dropdowns = results;
        });
    });

    
    /*$timeout(function () { 
        Data.get('dropdowns_list_ctrl').then(function (results) {
            $rootScope.dropdowns = results;
            var table = new Tabulator("#temp", {
                data:results,           //load row data from array
                layout:"fitColumns",      //fit columns to width of table
                responsiveLayout:"hide",  //hide columns that dont fit on the table
                tooltips:true,            //show tool tips on cells
                addRowPos:"top",          //when adding a new row, add it to the top of the table
                history:true,             //allow undo and redo actions on the table
                pagination:"local",       //paginate the data
                paginationSize:20,         //allow 7 rows per page of data
                movableColumns:true,      //allow column order to be changed
                resizableRows:true,       //allow row order to be changed
                initialSort:[             //set the initial sort order of the data
                    {column:"type", dir:"asc"},
                ],
                columns:[

                    {title:"type", field:"type", sortable:true, width:200},
                    {title:"display_value", field:"display_value", sortable:true, sorter:"number"},
                    {title:"isdefault", field:"isdefault", sortable:true},
                    {title:"depth", field:"depth", sortable:false},
                    {title:"parent_type", field:"parent_type"},
                    {title:"sequence_number", field:"sequence_number"},
                    {title:"", field:"dropdowns_id", formatter:the_Function,
                    cellClick:function(e, cell){
                        
                        var Btn = document.createElement('Button');
                        Btn.id = "Btn_Id";
                        console.log(Btn);
                        //e - the click event object
                        //cell - cell component
                        //$location.path('dropdowns_edit/'+dropdowns_id);
                        },
                    }
                    
                    /*{title:"", field:"dropdown_id", hozAlign:"center", formatter:"tickCross", sorter:"boolean"},

                  ],
                });
                table.setData(results);
        });
    }, 100);*/
});
    
    
app.controller('Dropdowns_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http,$timeout, Data) {
    $timeout(function () { 
        Data.get('selectparentlist').then(function (results) {
            $rootScope.parentlists = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selecttypelist').then(function (results) {
            $rootScope.typelists = results;
        });
    }, 100);
    
    $scope.dropdowns_add_new = {dropdown:''};
    $scope.dropdowns_add_new = function (dropdown) {
        Data.post('dropdowns_add_new', {
            dropdown: dropdown
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('dropdowns_list');
            }
        });
    };
});
    
app.controller('Dropdowns_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var dropdowns_id = $routeParams.dropdowns_id;
    $scope.activePath = null;

    $timeout(function () { 
        Data.get('selectparentlist').then(function (results) {
            $rootScope.parentlists = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selecttypelist').then(function (results) {
            $rootScope.typelists = results;
        });
    }, 100);

    $scope.dropdown={};
    Data.get('dropdowns_edit_ctrl/'+dropdowns_id).then(function (results) {
        $rootScope.datadropdowns = results;
    });
    
    
    $scope.dropdowns_update = function (dropdown) {
        Data.post('dropdowns_update', {
            dropdown: dropdown
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('dropdowns_list');
            }
        });
    };
    
    $scope.dropdowns_delete = function (dropdown) {
        //console.log(business_unit);
        var deletedropdowns = confirm('Are you absolutely sure you want to delete?');
        if (deletedropdowns) {
            Data.post('dropdowns_delete', {
                dropdown: dropdown
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('dropdowns_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectDropdowns', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    $timeout(function () { 
        Data.get('selectdropdowns/'+dropdown_type).then(function (results) {
            $rootScope.dropdowns = results;
        });
    }, 100);
});


// TEAMS

app.controller('Teams_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('teams_list_ctrl').then(function (results) {
            $rootScope.teams = results;
        });
    }, 100);
});
    
    
app.controller('Teams_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    $scope.teams_add_new = {team:''};
    $scope.teams_add_new = function (team) {
        Data.post('teams_add_new', {
            team: team
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('teams_list');
            }
        });
    };
});
    
app.controller('Teams_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var team_id = $routeParams.team_id;
    $scope.activePath = null;
    
    Data.get('teams_edit_ctrl/'+team_id).then(function (results) {
        $rootScope.teams = results;
    });
    
    $scope.teams_update = function (team) {
        Data.post('teams_update', {
            team: team
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('teams_list');
            }
        });
    };
    
    $scope.teams_delete = function (team) {
        //console.log(business_unit);
        var deleteteams = confirm('Are you absolutely sure you want to delete?');
        if (deleteteams) {
            Data.post('teams_delete', {
                team: team
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('teams_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectTeams', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectteams').then(function (results) {
            $rootScope.teams = results;
        });
    }, 100);
});

// SUB TEAMS

app.controller('Sub_Teams_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('sub_teams_list_ctrl').then(function (results) {
            $scope.sub_teams_list = results;
        });
    }, 100);
});
    
    
app.controller('Sub_Teams_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    
    $scope.sub_teams_add_new = {sub_team:''};
    $scope.sub_teams_add_new = function (sub_team) {
        Data.post('sub_teams_add_new', {
            sub_team: sub_team
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('sub_teams_list');
            }
        });
    };
});
    
app.controller('Sub_Teams_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    var sub_team_id = $routeParams.sub_team_id;
    $scope.activePath = null;
    
    Data.get('sub_teams_edit_ctrl/'+sub_team_id).then(function (results) {

        $scope.$watch($scope.sub_team_data, function() {
            $scope.sub_team_data = {};
            $scope.sub_team_data = {
                sub_team_id:results[0].sub_team_id,
                sub_team_name:results[0].sub_team_name,
                sub_team_description:results[0].sub_team_description,
            }
        });
        
    });
    
    $scope.sub_teams_update = function (sub_team_data) {
        Data.post('sub_teams_update', {
            sub_team_data: sub_team_data
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('sub_teams_list');
            }
        });
    };
    
    $scope.sub_teams_delete = function (sub_team_data) {
        //console.log(business_unit);
        var deletesub_teams = confirm('Are you absolutely sure you want to delete?');
        if (deletesub_teams) {
            Data.post('sub_teams_delete', {
                sub_team_data: sub_team_data
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('sub_teams_list');
                }
            });
        }
    };
    
});
    
app.controller('Selectsub_teams', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectsub_teams').then(function (results) {
            $scope.sub_teams = results;
        });
    }, 100);
});

// ADMIN DASHBOARD

app.controller('AdminDashboard', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    $scope.expiring_lease_count = 0;
    $scope.birthdays_count = 0;
    $scope.anniversaries_count = 0;
    $scope.open_activities_count = 0;
    $scope.unassigned_enquiries_count = 0;

    /*Data.get('count_total_listings').then(function (results) {
        $scope.total_list_commercial = results[0].total_list_commercial;
        $scope.total_list_residential = results[0].total_list_residential;
        
    });
    Data.get('total_listings').then(function (results) {
        $scope.listtotal_listings = results;
        /*$('#flexslider1').flexslider({
            slideshow:true,
             animation: "slide",
            itemWidth: 192,
            itemMargin: 2
          });
          
    }); */
    

    $scope.goalsdata = {};
    var cdays = [
        "Sun",
        "Mon",
        "Tue",
        "Wed",
        "Thu",
        "Fri",
        "Sat"
    ];
    var cmonths = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec"

    ];
    var currentdate = new Date();
    
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    lmm = currentdate.toDateString().substr(4, 3);
    $scope.today_date = currentdate.getDate()+" "+lmm;

    var dt = new Date(); // current date of week
    var currentWeekDay = dt.getDay();
    var lessDays = currentWeekDay == 0 ? 6 : currentWeekDay - 1;
    var wkStart = new Date(new Date(dt).setDate(dt.getDate() - lessDays));
    temp_date = wkStart;
    $scope.weekdays = '<div class="days"';
    if (dt.getDate()==temp_date.getDate())
    {
        $scope.weekdays =  $scope.weekdays + ' style="background:#4b4be1;" ';    
    }
    $scope.weekdays =  $scope.weekdays + '><p>'+wkStart.getDate()+'</p><p class="days_day">'+cdays[temp_date.getDay()]+'</p></div>';
    for (i=1;i<=5;i++)
    {
        temp_date = new Date(new Date(temp_date).setDate(temp_date.getDate() + 1));
        $scope.weekdays = $scope.weekdays + '<div class="days"';
        if (dt.getDate()==temp_date.getDate())
        {
            $scope.weekdays =  $scope.weekdays + ' style="background:#4b4be1;" ';    
        }
        $scope.weekdays =  $scope.weekdays + '><p>'+temp_date.getDate()+'</p><p class="days_day">'+cdays[temp_date.getDay()]+'</p></div>';        
    }
    $scope.trustedHtml_days = $sce.trustAsHtml($scope.weekdays);
    var wkEnd = new Date(new Date(wkStart).setDate(wkStart.getDate() + 5));

    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;

    $scope.$watch($scope.goalsdata.start_date, function() {
        $scope.goalsdata.start_date = start_date;
    }, true);

    $scope.$watch($scope.goalsdata.end_date, function() {
        $scope.goalsdata.end_date = end_date;
    }, true);


    $timeout(function () { 
        Data.get('selectusers_subordinates').then(function (results) {
            $scope.listusers = results;
        });
    }, 100);
    

    $timeout(function () { 
        Data.get('getemployee_details/0').then(function (results) {
            $scope.increment_per = results[0].increment_per;
            $scope.increment_month = results[0].increment_month;
        });
    }, 100);

    $timeout(function () { 
        Data.get('getquarter_data').then(function (results) {
            $scope.target_achieved = results[0].target_achieved;
            $scope.eligibility = results[0].eligibility;
        });
    }, 100);

    $timeout(function () {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.goalsdata.start_date)
        {
           start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
           end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        
        Data.get('userdashboard/'+start_date+'/'+end_date+'/0').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.dashboardHtml = $sce.trustAsHtml($scope.html);   
            $scope.total_target_achieved = results[0].total_target_achieved;     
        });
        
    },true);



    $scope.getuserdashboard = function(goalsdata)
    {
        $timeout(function () {
            start_date="0000-00-00";
            end_date="0000-00-00";
            if (goalsdata.start_date)
            {
                start_date = goalsdata.start_date.substr(6,4)+"-"+goalsdata.start_date.substr(3,2)+"-"+goalsdata.start_date.substr(0,2);         
            }
            if (goalsdata.end_date)
            {
                end_date = goalsdata.end_date.substr(6,4)+"-"+goalsdata.end_date.substr(3,2)+"-"+goalsdata.end_date.substr(0,2);         
            }
            
            Data.get('userdashboard/'+start_date+'/'+end_date+'/'+goalsdata.user_id).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.dashboardHtml = $sce.trustAsHtml($scope.html);  
                $scope.total_target_achieved = results[0].total_target_achieved;  
                $scope.eligibility=results[0].eligibility;
                $scope.goal_per_to_get=results[0].goal_per_to_get;
                $scope.goal_percent=results[0].goal_percent;
                $scope.goals_achieved=results[0].goals_achieved;
                $scope.goals_to_achieve=results[0].goals_to_achieve;
                $scope.target_achieved=results[0].target_achieved;
                console.log(results[0].total_target_achieved);   
            });
            $timeout(function () { 
                Data.get('getemployee_details/'+goalsdata.user_id).then(function (results) {
                    $scope.increment_per = results[0].increment_per;
                    $scope.increment_month = results[0].increment_month;
                });
            }, 100);

        },true);
    }

    $timeout(function () {
        Data.get('checkfornewcomment').then(function (results) {
            console.log(results[0].count);
            $scope.newcomment_count = results[0].count;
        });
    },true);


    $scope.userdashboardmore = function (goal_category,goal_sub_category,user_id)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.goalsdata.start_date)
        {
            start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
            end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        console.log($scope.goalsdata.user_id);
        Data.get('userdashboardmore/'+start_date+'/'+end_date+'/'+goal_category+'/'+goal_sub_category+'/'+user_id).then(function (results) {
            $("#maindashboard").css("display","none");
            $("#more_info").css("display","block");
            $scope.html = results[0].htmlstring;
            $scope.dashboardMoreHtml = $sce.trustAsHtml($scope.html);        
        });
    }




    $scope.showdashboard = function()
    {
        $("#maindashboard").css("display","block");
        $("#more_info").css("display","none");
    }
    
    

    $scope.addusercomments = function(id,comments,option)
    {
        /*console.log(id);
        console.log(comments);
        console.log(option);*/
        if (comments=='')
        {
            alert("Empty Comment not allowed");
            return;
        }
        var addcomments = confirm('Want to Add Comment ?');
        if (addcomments) {
            Data.get('addusercomments/'+id+'/'+comments+'/'+option).then(function (results) {
                Data.toast(results);
                $timeout(function () { 
                    Data.get('show_tasks').then(function (results) {
                        $scope.html = results[0].htmlstring;
                        $scope.trustedHtml_task = $sce.trustAsHtml($scope.html);
                        
                    });
                }, 1000);


            });
        }
        
    }

    /*$scope.client_meeting_count=0;
    $scope.client_meeting_goal=0;
    $scope.deal_count=0;
    $scope.deal_goal=0;
    $scope.enquiry_count=0;
    $scope.enquiry_goal=0;
    $scope.site_visit_count=0;
    $scope.site_visit_goal=0;

    Data.get('total_listings').then(function (results) {
        $rootScope.total_listings = results;
        
    });

    Data.get('branch_listings').then(function (results) {
        $rootScope.branch_listings = results;
        
    });


    Data.get('count_total_listings').then(function (results) {
        $scope.total_list_commercial = results[0].total_list_commercial;
        $scope.total_list_residential = results[0].total_list_residential;
        
    });
    

    $timeout(function () { 
        Data.get('completed_transactions').then(function (results) {
            $rootScope.completed_transactions = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('total_billings').then(function (results) {
            $rootScope.total_billings = results;
        });
    }, 100);
*/
    /*$timeout(function () { 
        Data.get('getgoaldata').then(function (results) {
            $timeout(function () { 
                $scope.client_meeting_count=results[0].client_meeting_count;
                $scope.client_meeting_goal=results[0].client_meeting_goal;
                $scope.deal_count=results[0].deal_count;
                $scope.deal_goal=results[0].deal_goal;
                $scope.enquiry_count=results[0].enquiry_count;
                $scope.enquiry_goal=results[0].enquiry_goal;
                $scope.site_visit_count=results[0].site_visit_count;
                $scope.site_visit_goal=results[0].site_visit_goal;
            }, 200);
            
        });
    }, 100);*/
    
    $scope.transactions=0;
    $scope.transactions_commercial=0;
    $scope.transactions_retail=0;
    $scope.transactions_residential=0;
    $scope.transactions_preleased=0;
    $scope.brokerage_received=0;
    $scope.brokerage_invoiced=0;
    $scope.brokerage_expected=0;
    $scope.gettransactionsdata = function(selectedmonth)
    {
        $timeout(function () { 
            Data.get('gettransactionsdata/'+selectedmonth).then(function (results) {
                $timeout(function () { 
                    $scope.transactions=results[0].transactions;
                    $scope.transactions_commercial=789;
                    $scope.transactions_retail=results[0].transactions_retail;
                    $scope.transactions_residential=results[0].transactions_residential;
                    $scope.transactions_preleased=results[0].transactions_preleased;
                    $scope.brokerage_received=results[0].brokerage_received;
                    $scope.brokerage_invoiced=results[0].brokerage_invoiced;
                    $scope.brokerage_expected=results[0].brokerage_expected;
                }, 200);
            });
        }, 100);
    }
    $scope.gettransactionsdata(0);

    $scope.task = {};
    /*$scope.$watch($scope.task.assign_to, function() {
        $scope.task.assign_to = [$rootScope.user_id];
    });*/
    
    $scope.$watch($scope.task.teams, function() {
        $scope.task.teams = [$rootScope.bo_id];
    });


    $timeout(function () { 
        Data.get('selectteams').then(function (results) {
            $scope.teams_list = results;
            $scope.teams = results;
        });
    }, 100);
    $timeout(function () { 
        Data.get('selectsubteams').then(function (results) {
            $scope.sub_teams_list = results;
        });
    }, 100);

    $scope.getsubodrniate = function(teams,sub_teams)
    {
        Data.get('getsubodrniate/'+teams+'/'+sub_teams).then(function (results) {
            $scope.listusers = results;
        });
    }

    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.listusers = results;
        });
    }, 100);

    $scope.clients = {};
    $timeout(function () { 
        Data.get('selectcontact_with_broker/Client').then(function (results) {
            $scope.clients = results;
        });
    }, 100);


    $timeout(function () { 
        Data.get('assist/user').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_assist = $sce.trustAsHtml($scope.html); 
        });
    }, 100);

    //$timeout(function () { 
    //    Data.get('todays_alerts').then(function (results) {
           // $scope.html = results[0].htmlstring;
           // $scope.trustedHtml_todays_alerts = $sce.trustAsHtml($scope.html);

    //    });
    //});
    $scope.reminder_count = 0;
    $timeout(function () { 
        Data.get('reminders').then(function (results1) {
            //console.log(results1.length);
            if (results1.length>0)
            {
                $("#reminders").css("display","block");
                $scope.reminders_list = results1;
                $scope.reminder_count = results1.length;
            }
           
            
        });
    },1000);

    
    $scope.reminders = function()
    {
        //$scope.task = {};
        
        $("#reminders").css("display","block");
    }

    $scope.close_reminders = function()
    {
        $("#reminders").css("display","none");
        $("#activity_id").val(0);
        $("#closure_comment").val();
        $scope.closure_comment="";
    }
    $scope.activity_id = 0;
    $scope.close_activity = function(activity_id)
    {
        $("#temp_activity_id").val(activity_id)
        $("#reminders_comment").css("display","block");
        $("#first_comment").css("display","block");
        $("#second_comment").css("display","none");
        $scope.activity_id = activity_id;
        $scope.closure_comment = "";
    }
    $scope.save_reminders_comment = function(closure_comment)
    {
        activity_id = $("#temp_activity_id").val();
        console.log(activity_id);
        $scope.activity_id = activity_id;
        Data.get('save_reminders_comment/'+$scope.activity_id+'/'+closure_comment).then(function (results) {
            Data.toast(results);
            $("#first_comment").css("display","none");
            $("#second_comment").css("display","block");
            Data.get('reminders').then(function (results1) {
                //console.log(results1.length);
                if (results1.length>0)
                {
                    
                    $scope.reminders_list = results1;
                    $scope.reminder_count = results1.length;
                }
                
            });
            $timeout(function () { 
                Data.get('show_tasks').then(function (results) {
                    $scope.html = results[0].htmlstring;
                    $scope.trustedHtml_task = $sce.trustAsHtml($scope.html);
                    
                });
            }, 1000);
        });
        //$("#reminders_comment").css("display","none");
        //$scope.closure_comment = "";
    }
    $scope.create_new_activity = function(next_date)
    {
        activity_id = $("#temp_activity_id").val();
        console.log(activity_id);
        var createactivity = confirm('Are you sure ? You want Create New Activity ?');
        if (createactivity) 
        {
            cnext_date="0000-00-00";
            if (next_date)
            {
            cnext_date = next_date.substr(6,4)+"-"+next_date.substr(3,2)+"-"+next_date.substr(0,2)+" "+next_date.substr(11,5);        
            
            }
            Data.get('create_new_activity/'+activity_id+'/'+cnext_date).then(function (results) {
                Data.toast(results);
                $("#reminders").css("display","none");
                $("#reminders_comment").css("display","none");
                Data.get('reminders').then(function (results1) {
                    //console.log(results1.length);
                    if (results1.length>0)
                    {
                        $("#reminders").css("display","block");
                        $scope.reminders_list = results1;
                        $scope.reminder_count = results1.length;
                    }
                    Data.get('show_tasks').then(function (results2) {
                        $scope.html = results2[0].htmlstring;
                        $scope.trustedHtml_task = $sce.trustAsHtml($scope.html);
                        
                    });
                    
                });
                
            });
        }
        //$("#reminders_comment").css("display","none");
        //$scope.closure_comment = "";
    }

    $scope.close_reminders_comment = function()
    {
        $("#reminders_comment").css("display","none");
        $scope.closure_comment = "";
    }

    $scope.assign_task = function()
    {
        $scope.task = {};
        $("#file_task").val(0);
        
        //$("#assign_task").modal("show");
    }

    $scope.task_save_new = function(task)
    {
        task.file_name = $("#file_task_name").val();
        Data.post('task_save_new', {
            task: task
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $("#assign_task").modal("hide");
                $('#file_task').fileinput('upload');
                $timeout(function () { 
                    Data.get('assist/user').then(function (results) {
                        $scope.html = results[0].htmlstring;
                        $scope.trustedHtml_assist = $sce.trustAsHtml($scope.html); 
                    });
                }, 100);
            }
        });
    };

    $scope.contact_save_new = function (contact) {
        contact.assign_to = [$rootScope.user_id];
        contact.teams = [$rootScope.bo_id];
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $("#addclient").css("display","none");
                contact_id = results.contact_id;
                console.log(contact_id);
                $scope.clients = {};
                Data.get('selectcontact/Client').then(function (results1) {
                    $scope.clients = results1;
                    $scope.task.client_id = 0;
                    $scope.$watch($scope.task.client_id, function() {
                        $scope.task.client_id = contact_id;
                        console.log($scope.task.client_id);
                        $(".select2").select2();
                    },true);
                },3000);
            }
        });
    };
    $scope.addclient = function()
    {
        $("#addclient").css("display","block");
    }
    $scope.close = function()
    {
        $("#addclient").css("display","none");
    }


});

app.controller('AdminDashboardMore', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    var goal_id = $routeParams.goal_id;
    $scope.searchdata = {};
    var currentdate = new Date();
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;

    $scope.$watch($scope.searchdata.start_date, function() {
        $scope.searchdata.start_date = start_date;
    }, true);

    $scope.$watch($scope.searchdata.end_date, function() {
        $scope.searchdata.end_date = end_date;
    }, true);

    $timeout(function () {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.searchdata.start_date)
        {
           start_date = $scope.searchdata.start_date.substr(6,4)+"-"+$scope.searchdata.start_date.substr(3,2)+"-"+$scope.searchdata.start_date.substr(0,2);         
        }
        if ($scope.searchdata.end_date)
        {
           end_date = $scope.searchdata.end_date.substr(6,4)+"-"+$scope.searchdata.end_date.substr(3,2)+"-"+$scope.searchdata.end_date.substr(0,2);         
        }
        Data.get('userdashboardmore/'+goal_id+'/'+start_date+'/'+end_date).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.dashboardMoreHtml = $sce.trustAsHtml($scope.html);        
        });
        
    },true);

    $scope.getuserdashboardmore = function(searchdata)
    {
        $timeout(function () {
            start_date="0000-00-00";
            end_date="0000-00-00";
            if (searchdata.start_date)
            {
                start_date = searchdata.start_date.substr(6,4)+"-"+searchdata.start_date.substr(3,2)+"-"+searchdata.start_date.substr(0,2);         
            }
            if (searchdata.end_date)
            {
                end_date = searchdata.end_date.substr(6,4)+"-"+searchdata.end_date.substr(3,2)+"-"+searchdata.end_date.substr(0,2);         
            }
            
            Data.get('userdashboardmore/'+goal_id+'/'+start_date+'/'+end_date).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.dashboardMoreHtml = $sce.trustAsHtml($scope.html);        
            });
        },true);
    }

});

app.controller('AdminDashboard_org', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{

    $scope.expiring_lease_count = 0;
    $scope.birthdays_count = 0;
    $scope.anniversaries_count = 0;
    $scope.open_activities_count = 0;
    $scope.unassigned_enquiries_count = 0;

    $scope.goalsdata = {};
    var currentdate = new Date();
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;

    $scope.$watch($scope.goalsdata.start_date, function() {
        $scope.goalsdata.start_date = start_date;
    }, true);

    $scope.$watch($scope.goalsdata.end_date, function() {
        $scope.goalsdata.end_date = end_date;
    }, true);
    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
        $scope.listusers = results;
        });
    }, 100);
    /*$timeout(function () {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.goalsdata.start_date)
        {
           start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
           end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        
        Data.get('userdashboard/'+start_date+'/'+end_date).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.dashboardHtml = $sce.trustAsHtml($scope.html);        
        });
    },true);*/

    $scope.getadmindashboard = function(goalsdata)
    {
        $("#maindashboard").css("display","block");
        $("#more_info").css("display","none");
        $timeout(function () {
            start_date="0000-00-00";
            end_date="0000-00-00";
            if (goalsdata.start_date)
            {
                start_date = goalsdata.start_date.substr(6,4)+"-"+goalsdata.start_date.substr(3,2)+"-"+goalsdata.start_date.substr(0,2);         
            }
            if (goalsdata.end_date)
            {
                end_date = goalsdata.end_date.substr(6,4)+"-"+goalsdata.end_date.substr(3,2)+"-"+goalsdata.end_date.substr(0,2);         
            }
            
            Data.get('admindashboard/'+start_date+'/'+end_date+'/'+goalsdata.user_id).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.dashboardHtml = $sce.trustAsHtml($scope.html);        
            });
        },true);
    }
    $scope.task = {};
    /*$scope.$watch($scope.task.assign_to, function() {
        $scope.task.assign_to = [$rootScope.user_id];
    });*/
    
    $scope.$watch($scope.task.teams, function() {
        $scope.task.teams = [$rootScope.bo_id];
    });

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
    $scope.clients = {};
    $timeout(function () { 
        Data.get('selectcontact/Client').then(function (results) {
            $scope.clients = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('assist/admin').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_assist = $sce.trustAsHtml($scope.html); 
            
        });
    }, 100);

    /*$timeout(function () { 
        Data.get('todays_alerts').then(function (results) {
            //$scope.html = results[0].htmlstring;
            //$scope.trustedHtml_todays_alerts = $sce.trustAsHtml($scope.html);

        });
    });*/

    
    $scope.assign_task = function()
    {
        //$scope.task = {};
        
        //$("#assign_task").modal("show");
    }
    $scope.task_save_new = function(task)
    {
        Data.post('task_save_new', {
            task: task
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $("#assign_task").modal("hide");
                $timeout(function () { 
                    Data.get('assist/admin').then(function (results) {
                        $scope.html = results[0].htmlstring;
                        $scope.trustedHtml_assist = $sce.trustAsHtml($scope.html); 
                    });
                }, 100);
            }
        });
    };
    

   
    $scope.contact_save_new = function (contact) {
        contact.assign_to = [$rootScope.user_id];
        contact.teams = [$rootScope.bo_id];
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $("#addclient").css("display","none");
                contact_id = results.contact_id;
                console.log(contact_id);
                $scope.clients = {};
                Data.get('selectcontact/Client').then(function (results1) {
                    $scope.clients = results1;
                    $scope.task.client_id = 0;
                    $scope.$watch($scope.task.client_id, function() {
                        $scope.task.client_id = contact_id;
                        console.log($scope.task.client_id);
                        $(".select2").select2();
                    },true);
                },3000);
            }
        });
    };
    $scope.addclient = function()
    {
        $("#addclient").css("display","block");
    }
    $scope.close = function()
    {
        $("#addclient").css("display","none");
    }
    $scope.admindashboardmore = function (goal_category,goal_sub_category)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.goalsdata.start_date)
        {
            start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
            end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        Data.get('admindashboardmore/'+$scope.goalsdata.user_id+'/'+start_date+'/'+end_date+'/'+goal_category+'/'+goal_sub_category).then(function (results) {
            $("#maindashboard").css("display","none");
            $("#more_info").css("display","block");
            $scope.html = results[0].htmlstring;
            $scope.dashboardMoreHtml = $sce.trustAsHtml($scope.html);        
        });
    }

    $scope.showdashboard = function()
    {
        $("#maindashboard").css("display","block");
        $("#more_info").css("display","none");
    }

    $timeout(function () { 
        Data.get('reminders').then(function (results1) {
            //console.log(results1.length);
            
            if (results1.length>0)
            {
                $("#reminders").css("display","block");
                $scope.reminders_list = results1;
                $scope.reminder_count = results1.length;
            }
            
        });
    },1000);

    
    $scope.reminders = function()
    {
        //$scope.task = {};
        
        $("#reminders").css("display","block");
    }

    $scope.close_reminders = function()
    {
        $("#reminders").css("display","none");
    }

    $scope.close_activity = function(activity_id)
    {
        $("#reminders_comment").css("display","block");
        $scope.activity_id = activity_id;
        $scope.closure_comment = "";
    }
    $scope.save_reminders_comment = function(closure_comment)
    {
        activity_id = $("#temp_activity_id").val();
        console.log(activity_id);
        
        Data.get('save_reminders_comment/'+activity_id+'/'+closure_comment).then(function (results) {
            Data.get('reminders').then(function (results1) {
                //console.log(results1.length);
                if (results1.length>0)
                {
                    $("#reminders").css("display","block");
                    $scope.reminders_list = results1;
                    $scope.reminder_count = results1.length;
                }
                
            });
            $scope.show_tasks = function () {
                $timeout(function () { 
                    Data.get('show_tasks').then(function (results) {
                        $scope.html = results[0].htmlstring;
                        $scope.trustedHtml_task = $sce.trustAsHtml($scope.html);
                        
                    });
                }, 1000);
            };
        });
        $("#reminders_comment").css("display","none");
        $scope.closure_comment = "";
    }
    $scope.close_reminders_comment = function()
    {
        $("#reminders_comment").css("display","none");
        $scope.closure_comment = "";
    }

    /*$scope.client_meeting_count=0;
    $scope.client_meeting_goal=0;
    $scope.deal_count=0;
    $scope.deal_goal=0;
    $scope.enquiry_count=0;
    $scope.enquiry_goal=0;
    $scope.site_visit_count=0;
    $scope.site_visit_goal=0;

    Data.get('total_listings').then(function (results) {
        $rootScope.total_listings = results;
        
    });

    Data.get('branch_listings').then(function (results) {
        $rootScope.branch_listings = results;
        
    });


    Data.get('count_total_listings').then(function (results) {
        $scope.total_list_commercial = results[0].total_list_commercial;
        $scope.total_list_residential = results[0].total_list_residential;
        
    });
    

    $timeout(function () { 
        Data.get('completed_transactions').then(function (results) {
            $rootScope.completed_transactions = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('total_billings').then(function (results) {
            $rootScope.total_billings = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('getgoaldata').then(function (results) {
            $timeout(function () { 
                $scope.client_meeting_count=results[0].client_meeting_count;
                $scope.client_meeting_goal=results[0].client_meeting_goal;
                $scope.deal_count=results[0].deal_count;
                $scope.deal_goal=results[0].deal_goal;
                $scope.enquiry_count=results[0].enquiry_count;
                $scope.enquiry_goal=results[0].enquiry_goal;
                $scope.site_visit_count=results[0].site_visit_count;
                $scope.site_visit_goal=results[0].site_visit_goal;
            }, 200);
            
        });
    }, 100);*/




});


// ASSIST

app.controller('Assist_Readmore', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{

    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
    $scope.regular_list = "Yes";
    $scope.pagenavigation = function(which_side)
    {
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
            
            Data.get('assist_readmore/user/'+$scope.next_page_id).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_assist_readmore = $sce.trustAsHtml($scope.html); 
                $scope.page_range = parseInt($scope.next_page_id)+1+" - ";
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                $scope.page_range = $scope.page_range + $scope.next_page_id;
                
            });
            
        }
        else
        {
//            $scope.newsearch_properties($scope.searchdata,'pagenavigation');
            
        }
    }

    $timeout(function () { 
        Data.get('assist_readmore/user/0').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_assist_readmore = $sce.trustAsHtml($scope.html); 
            $scope.next_page_id = 30;
            
        });
    }, 100);

    $scope.showcomments = function(id,option)
    {
        console.log(id+"-"+option);
        $timeout(function () { 
            Data.get('showcomments/'+id+'/'+option).then(function (results) {
                $scope.html = results[0].htmlstring;
                var cstring = 'trustedHtml_c'+option+id;
                $scope[cstring] = $sce.trustAsHtml($scope.html);
            });
        }, 100);
    }

    $scope.addusercomments = function(id,comments,option)
    {
        /*console.log(id);
        console.log(comments);
        console.log(option);*/
        if (comments=='')
        {
            alert("Empty Comment not allowed");
            return;
        }
        var addcomments = confirm('Want to Add Comment ?');
        if (addcomments) {
            Data.get('addusercomments/'+id+'/'+comments+'/'+option).then(function (results) {
                Data.toast(results);
            });
            $("#user_comments_"+option+"_"+id).val("");
        }
        
    }

    $scope.showhistory = function(id,option)
    {
        console.log(id+"-"+option);
        $timeout(function () { 
            Data.get('showhistory/'+id+'/'+option).then(function (results) {
                $scope.html = results[0].htmlstring;
                var cstring = 'trustedHtml_h'+option+id;
                $scope[cstring] = $sce.trustAsHtml($scope.html);
            });
        }, 100);
    }

    $scope.deletetask = function(id,option)
    {
        var deletetask = confirm('Are you sure you want to delete this task ?');
        if (deletetask) {
            Data.get('deletetask/'+id+'/'+option).then(function (results) {
                Data.toast(results);
            });
        }
    }

    $scope.change_task_status = function(task_status,task_id)
    {
        Data.get('change_task_status/'+task_status+'/'+task_id).then(function (results) {
            Data.toast(results);
            /*Data.get('assist_readmore/user/0').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_assist_readmore = $sce.trustAsHtml($scope.html); 
                $scope.next_page_id = 30;
                
            });*/
        });
    }

    var values_loaded = "false";
    $scope.open_search = function()
    {
        if (values_loaded=="false")
        {
            values_loaded="true";
            console.log("opening");

            
            $timeout(function () { 
                Data.get('selectcontact/Client').then(function (results) {
                    $rootScope.clients = results;
                });
            }, 100);

            

            $timeout(function () { 
                Data.get('selectusers').then(function (results) {
                    $scope.listusers = results;
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
                Data.get('getdatavalues_activity/property_id').then(function (results) {
                    $scope.property_ids = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_activity/project_id').then(function (results) {
                    $scope.project_ids = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_activity/enquiry_id').then(function (results) {
                    $scope.enquiry_ids = results;
                });
            }, 100);

            $timeout(function () { 
                Data.get('getdatavalues_activity/activity_id').then(function (results) {
                    $scope.activity_ids = results;
                });
            }, 100);
        }
    };

    $scope.select_assign_to = function(teams,sub_teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/'+sub_teams).then(function (results) {
                $scope.listusers = results;
            });
        }, 100);
    }
    
    $scope.getsubodrniate = function(teams,sub_teams)
    {
        Data.get('getsubodrniate/'+teams+'/'+sub_teams).then(function (results) {
            $scope.listusers = results;
        });
    }


    $scope.search_assist = function (searchdata,from_click) 
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
        Data.post('search_assist', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.listactivities, function() {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_assist_readmore = $sce.trustAsHtml($scope.html);                 
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                $scope.page_range = $scope.page_range + $scope.next_page_id;
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
        $timeout(function () { 
            Data.get('assist_readmore/user/0').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_assist_readmore = $sce.trustAsHtml($scope.html); 
                $scope.next_page_id = 30;
                
            });
        }, 100);
    }
});


// MANAGER DASHBOARD

app.controller('ManagerDashboard', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{

    $scope.client_meeting_count=0;
    $scope.client_meeting_goal=0;
    $scope.deal_count=0;
    $scope.deal_goal=0;
    $scope.enquiry_count=0;
    $scope.enquiry_goal=0;
    $scope.site_visit_count=0;
    $scope.site_visit_goal=0;
    //$timeout(function () { 
        Data.get('total_listings').then(function (results) {
            $rootScope.total_listings = results;
            
        });
    //}, 100);
    
    $timeout(function () { 
        Data.get('branch_listings').then(function (results) {
            $rootScope.branch_listings = results;
            
        });
    }, 100);

    $timeout(function () { 
        Data.get('completed_transactions').then(function (results) {
            $rootScope.completed_transactions = results;
            
        });
    }, 100);

    $timeout(function () { 
        Data.get('total_billings').then(function (results) {
            $rootScope.total_billings = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('getgoaldata').then(function (results) {
            $timeout(function () { 
                $scope.client_meeting_count=results[0].client_meeting_count;
                $scope.client_meeting_goal=results[0].client_meeting_goal;
                $scope.deal_count=results[0].deal_count;
                $scope.deal_goal=results[0].deal_goal;
                $scope.enquiry_count=results[0].enquiry_count;
                $scope.enquiry_goal=results[0].enquiry_goal;
                $scope.site_visit_count=results[0].site_visit_count;
                $scope.site_visit_goal=results[0].site_visit_goal;
            }, 200);
            
        });
    }, 100);



});

// SETUP

app.controller('Setup', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
   
});


// USER DASHBOARD

app.controller('UserDashboard', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    $scope.expiring_lease_count = 0;
    $scope.birthdays_count = 0;
    $scope.anniversaries_count = 0;
    $scope.open_activities_count = 0;
    $scope.unassigned_enquiries_count = 0;

    /*Data.get('count_total_listings').then(function (results) {
        $scope.total_list_commercial = results[0].total_list_commercial;
        $scope.total_list_residential = results[0].total_list_residential;
        
    });
    Data.get('total_listings').then(function (results) {
        $scope.listtotal_listings = results;
        /*$('#flexslider1').flexslider({
            slideshow:true,
             animation: "slide",
            itemWidth: 192,
            itemMargin: 2
          });
          
    }); */
    /*$scope.labels = ["January", "February", "March", "April", "May", "June", "July"];
    $scope.series = ['Series A', 'Series B'];
    $scope.data = [
        [65, 59, 80, 81, 56, 55, 40],
        [28, 48, 40, 19, 86, 27, 90]
    ];

    $scope.datasetOverride = [{ yAxisID: 'y-axis-1' }, { yAxisID: 'y-axis-2' }];
    $scope.options = {
        scales: {
        yAxes: [
            {
            id: 'y-axis-1',
            type: 'linear',
            display: true,
            position: 'left'
            },
            {
            id: 'y-axis-2',
            type: 'linear',
            display: true,
            position: 'right'
            }
        ]
        }
    };
    */

    



    $scope.goalsdata = {};
    var cdays = [
        "Sun",
        "Mon",
        "Tue",
        "Wed",
        "Thu",
        "Fri",
        "Sat"
    ];
    var cmonths = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec"

    ];
    month_dis = [];
    month_dis[1] = "jan";
    month_dis[2] = "feb";
    month_dis[3] = "mar";
    month_dis[4] = "apr";
    month_dis[5] = "may"; 
    month_dis[6] = "jun";
    month_dis[7] = "jul";
    month_dis[8] = "aug";
    month_dis[9] = "sep";
    month_dis[10] = "oct";
    month_dis[11] = "nov";
    month_dis[12] = "dec";

    var currentdate = new Date();
    
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    lmm = currentdate.toDateString().substr(4, 3);
    $scope.today_date = currentdate.getDate()+" "+lmm;

    var dt = new Date(); // current date of week
    var currentWeekDay = dt.getDay();
    var lessDays = currentWeekDay == 0 ? 6 : currentWeekDay - 1;
    var wkStart = new Date(new Date(dt).setDate(dt.getDate() - lessDays));
    temp_date = wkStart;
    $scope.weekdays = '<div class="days"';
    if (dt.getDate()==temp_date.getDate())
    {
        $scope.weekdays =  $scope.weekdays + ' style="background:#4b4be1;" ';    
    }
    $scope.weekdays =  $scope.weekdays + '><p>'+wkStart.getDate()+'</p><p class="days_day">'+cdays[temp_date.getDay()]+'</p></div>';
    for (i=1;i<=5;i++)
    {
        temp_date = new Date(new Date(temp_date).setDate(temp_date.getDate() + 1));
        $scope.weekdays = $scope.weekdays + '<div class="days"';
        if (dt.getDate()==temp_date.getDate())
        {
            $scope.weekdays =  $scope.weekdays + ' style="background:#4b4be1;" ';    
        }
        $scope.weekdays =  $scope.weekdays + '><p>'+temp_date.getDate()+'</p><p class="days_day">'+cdays[temp_date.getDay()]+'</p></div>';        
    }
    $scope.trustedHtml_days = $sce.trustAsHtml($scope.weekdays);
    var wkEnd = new Date(new Date(wkStart).setDate(wkStart.getDate() + 5));

    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;
    
    $scope.$watch($scope.goalsdata.start_date, function() {
        $scope.goalsdata.start_date = start_date;
    }, true);

    $scope.$watch($scope.goalsdata.end_date, function() {
        $scope.goalsdata.end_date = end_date;
    }, true);

    $scope.tr_start_date = start_date;
    $scope.tr_end_date = end_date;
    
    $scope.appraisals = '<p style="margin-top:15px;"><div class="days_growth">Jan</div><div class="days_growth">Feb</div><div class="days_growth">Mar</div></p>';

    $scope.trustedHtml_appraisals = $sce.trustAsHtml($scope.appraisals);   

    $timeout(function () { 
        Data.get('selectusers_subordinates').then(function (results) {
            $scope.listusers = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectteams').then(function (results) {
            $scope.teams_list = results;
            $scope.teams = results;
        });
    }, 100);
    $timeout(function () { 
        Data.get('selectsubteams').then(function (results) {
            $scope.sub_teams_list = results;
        });
    }, 100);

    $scope.getsubodrniate = function(teams,sub_teams)
    {
        Data.get('getsubodrniate/'+teams+'/'+sub_teams).then(function (results) {
            $scope.listusers = results;
        });

    }
    
    $scope.select_assign_to = function(teams,sub_teams)
    {
        $timeout(function () { 
            Data.get('select_assign_to/'+teams+'/'+sub_teams).then(function (results) {
                $scope.listusers = results;
            });
        }, 100);
    }

    $timeout(function () { 
        Data.get('getemployee_details/0').then(function (results) {
            $scope.increment_per = results[0].increment_per;
            $scope.increment_month = results[0].increment_month;
            
            $scope.appraisals = '<p style="margin-top:15px;">';
            if (results[0].increment_month==0)
            {
                $scope.increment_month = results[0].joining_month;
            }
            console.log(parseInt($scope.increment_month)+1);
            for (var i = (parseInt($scope.increment_month)+1); i<=(parseInt($scope.increment_month)+6); i++) {
                this_month = i;
                if (this_month>12)
                {
                    this_month=i-12;
                }
                $scope.appraisals += '<div class="days_growth green_color">'+month_dis[this_month]+'</div>';
            }
            $scope.appraisals += '</p>';
            console.log($scope.appraisals);
            $scope.trustedHtml_appraisals = $sce.trustAsHtml($scope.appraisals); 
        });
    }, 100);

    $timeout(function () { 
        Data.get('getquarter_data').then(function (results) {
            $scope.target_achieved = results[0].target_achieved;
            $scope.eligibility = results[0].eligibility;
        });
    }, 100);

    $timeout(function () { 
        Data.get('getemployee_record/0').then(function (results) {
            $scope.leave_balance=results[0].leave_balance;
            $scope.assets_issued=results[0].assets_issued;
        });
    }, 100);

    

    $timeout(function () {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.goalsdata.start_date)
        {
           start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
           end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        
        Data.get('userdashboard/'+start_date+'/'+end_date+'/0').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.dashboardHtml = $sce.trustAsHtml($scope.html);   
            $scope.total_target_achieved = results[0].total_target_achieved;     
        });
        
    },true);



    $scope.getuserdashboard = function(goalsdata)
    {
        $timeout(function () {
            start_date="0000-00-00";
            end_date="0000-00-00";
            if (goalsdata.start_date)
            {
                start_date = goalsdata.start_date.substr(6,4)+"-"+goalsdata.start_date.substr(3,2)+"-"+goalsdata.start_date.substr(0,2);         
            }
            if (goalsdata.end_date)
            {
                end_date = goalsdata.end_date.substr(6,4)+"-"+goalsdata.end_date.substr(3,2)+"-"+goalsdata.end_date.substr(0,2);         
            }
            
            Data.get('userdashboard/'+start_date+'/'+end_date+'/'+goalsdata.user_id).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.dashboardHtml = $sce.trustAsHtml($scope.html);  
                $scope.total_target_achieved = results[0].total_target_achieved;  
                $scope.eligibility=results[0].eligibility;
                $scope.goal_per_to_get=results[0].goal_per_to_get;
                $scope.goal_percent=results[0].goal_percent;
                $scope.goals_achieved=results[0].goals_achieved;
                $scope.goals_to_achieve=results[0].goals_to_achieve;
                $scope.target_achieved=results[0].target_achieved;
                
                console.log(results[0].total_target_achieved);   
            });
        },true);
        $timeout(function () { 
            Data.get('getemployee_details/'+goalsdata.user_id).then(function (results) {
                $scope.increment_per = results[0].increment_per;
                $scope.increment_month = results[0].increment_month;
                
                $scope.appraisals = '<p style="margin-top:15px;">';
                if (results[0].increment_month==0)
                {
                    $scope.increment_month = results[0].joining_month;
                }
                console.log(parseInt($scope.increment_month)+1);
                for (var i = (parseInt($scope.increment_month)+1); i<=(parseInt($scope.increment_month)+6); i++) {
                    this_month = i;
                    if (this_month>12)
                    {
                        this_month=i-12;
                    }
                    $scope.appraisals += '<div class="days_growth green_color">'+month_dis[this_month]+'</div>';
                }
                $scope.appraisals += '</p>';
                console.log($scope.appraisals);
                $scope.trustedHtml_appraisals = $sce.trustAsHtml($scope.appraisals); 
            });
        }, 100);
        $timeout(function () { 
            Data.get('getemployee_record/'+goalsdata.user_id).then(function (results) {
                $scope.leave_balance=results[0].leave_balance;
                $scope.assets_issued=results[0].assets_issued;
            });
        }, 100);

    }

    $timeout(function () {
        Data.get('checkfornewcomment').then(function (results) {
            console.log(results[0].count);
            $scope.newcomment_count = results[0].count;
        });
    },true);


    $scope.userdashboardmore = function (goal_category,goal_sub_category,user_id)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.goalsdata.start_date)
        {
            start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
            end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        console.log($scope.goalsdata.user_id);
        Data.get('userdashboardmore/'+start_date+'/'+end_date+'/'+goal_category+'/'+goal_sub_category+'/'+user_id).then(function (results) {
            $("#maindashboard").css("display","none");
            $("#more_info").css("display","block");
            $scope.html = results[0].htmlstring;
            $scope.dashboardMoreHtml = $sce.trustAsHtml($scope.html);        
        });
    }




    $scope.showdashboard = function()
    {
        $("#maindashboard").css("display","block");
        $("#more_info").css("display","none");
    }
    
    

    $scope.addusercomments = function(id,comments,option)
    {
        /*console.log(id);
        console.log(comments);
        console.log(option);*/
        if (comments=='')
        {
            alert("Empty Comment not allowed");
            return;
        }
        var addcomments = confirm('Want to Add Comment ?');
        if (addcomments) {
            Data.get('addusercomments/'+id+'/'+comments+'/'+option).then(function (results) {
                Data.toast(results);
                $timeout(function () { 
                    Data.get('show_tasks').then(function (results) {
                        $scope.html = results[0].htmlstring;
                        $scope.trustedHtml_task = $sce.trustAsHtml($scope.html);
                        
                    });
                }, 1000);


            });
        }
        
    }

    /*$scope.client_meeting_count=0;
    $scope.client_meeting_goal=0;
    $scope.deal_count=0;
    $scope.deal_goal=0;
    $scope.enquiry_count=0;
    $scope.enquiry_goal=0;
    $scope.site_visit_count=0;
    $scope.site_visit_goal=0;

    Data.get('total_listings').then(function (results) {
        $rootScope.total_listings = results;
        
    });

    Data.get('branch_listings').then(function (results) {
        $rootScope.branch_listings = results;
        
    });


    Data.get('count_total_listings').then(function (results) {
        $scope.total_list_commercial = results[0].total_list_commercial;
        $scope.total_list_residential = results[0].total_list_residential;
        
    });
    

    $timeout(function () { 
        Data.get('completed_transactions').then(function (results) {
            $rootScope.completed_transactions = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('total_billings').then(function (results) {
            $rootScope.total_billings = results;
        });
    }, 100);
*/
    /*$timeout(function () { 
        Data.get('getgoaldata').then(function (results) {
            $timeout(function () { 
                $scope.client_meeting_count=results[0].client_meeting_count;
                $scope.client_meeting_goal=results[0].client_meeting_goal;
                $scope.deal_count=results[0].deal_count;
                $scope.deal_goal=results[0].deal_goal;
                $scope.enquiry_count=results[0].enquiry_count;
                $scope.enquiry_goal=results[0].enquiry_goal;
                $scope.site_visit_count=results[0].site_visit_count;
                $scope.site_visit_goal=results[0].site_visit_goal;
            }, 200);
            
        });
    }, 100);*/
    
    $scope.transactions=0;
    $scope.transactions_commercial=0;
    $scope.transactions_retail=0;
    $scope.transactions_residential=0;
    $scope.transactions_preleased=0;
    $scope.brokerage_received=0;
    $scope.brokerage_invoiced=0;
    $scope.brokerage_expected=0;
    $scope.gettransactionsdata = function(tr_start_date,tr_end_date)
    {
        t_start_date="0000-00-00";
        t_end_date="0000-00-00";
        if (tr_start_date)
        {
           t_start_date = tr_start_date.substr(6,4)+"-"+tr_start_date.substr(3,2)+"-"+tr_start_date.substr(0,2);         
        }
        if (tr_end_date)
        {
           t_end_date = tr_end_date.substr(6,4)+"-"+tr_end_date.substr(3,2)+"-"+tr_end_date.substr(0,2);         
        }
        $timeout(function () { 
            Data.get('gettransactionsdata/'+t_start_date+'/'+t_end_date).then(function (results) {
                $timeout(function () { 
                    $scope.transactions=results[0].transactions;
                    $scope.transactions_commercial=results[0].transactions_commercial;
                    $scope.transactions_retail=results[0].transactions_retail;
                    $scope.transactions_residential=results[0].transactions_residential;
                    $scope.transactions_preleased=results[0].transactions_preleased;
                    $scope.brokerage_received=results[0].brokerage_received;
                    $scope.brokerage_invoiced=results[0].brokerage_invoiced;
                    $scope.brokerage_expected=results[0].brokerage_expected;
                }, 200);
            });
        }, 100);
    }
    $scope.gettransactionsdata($scope.tr_start_date,$scope.tr_end_date);

    $scope.labels = ["January", "February", "March", "April", "May", "June", "July","August","September","October","Novembar","December"];
    $scope.series = ['Brokerage'];
    $scope.data = [
        [65, 59, 80, 81, 56, 55, 40,0,0,0,0,0]
        
    ];    
    
    $scope.options = {
        scales: {
        yAxes: [
            {
            id: 'y-axis-1',
            type: 'linear',
            display: true,
            position: 'left'
            }
        ]
        }
    };
    

    $timeout(function () { 
        Data.get('getdashboardgraph').then(function (results) {  
            console.log(results[0].datastr);
            $scope.data = [results[0].datastr];
            console.log($scope.data);
        });
    }, 100);

    $scope.task = {};
    /*$scope.$watch($scope.task.assign_to, function() {
        $scope.task.assign_to = [$rootScope.user_id];
    });*/
    
    $scope.$watch($scope.task.teams, function() {
        $scope.task.teams = [$rootScope.bo_id];
    });

    

    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
            $scope.listusers = results;
        });
    }, 100);

    $scope.clients = {};
    $timeout(function () { 
        Data.get('selectcontact_with_broker/Client').then(function (results) {
            $scope.clients = results;
        });
    }, 100);


    $timeout(function () { 
        Data.get('assist/user').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_assist = $sce.trustAsHtml($scope.html); 
        });
    }, 100);

    //$timeout(function () { 
    //    Data.get('todays_alerts').then(function (results) {
           // $scope.html = results[0].htmlstring;
           // $scope.trustedHtml_todays_alerts = $sce.trustAsHtml($scope.html);

    //    });
    //});
    $scope.reminder_count = 0;
    $timeout(function () { 
        Data.get('reminders').then(function (results1) {
            //console.log(results1.length);
            if (results1.length>0)
            {
                $("#reminders").css("display","block");
                $scope.reminders_list = results1;
                $scope.reminder_count = results1.length;
            }
           
            
        });
    },1000);

    
    $scope.reminders = function()
    {
        //$scope.task = {};
        
        $("#reminders").css("display","block");
    }

    $scope.close_reminders = function()
    {
        $("#reminders").css("display","none");
        $("#activity_id").val(0);
        $("#closure_comment").val();
        $scope.closure_comment="";
    }
    $scope.activity_id = 0;
    $scope.close_activity = function(activity_id)
    {
        $("#temp_activity_id").val(activity_id)
        $("#reminders_comment").css("display","block");
        $("#first_comment").css("display","block");
        $("#second_comment").css("display","none");
        $scope.activity_id = activity_id;
        $scope.closure_comment = "";
    }
    $scope.save_reminders_comment = function(closure_comment)
    {
        activity_id = $("#temp_activity_id").val();
        console.log(activity_id);
        $scope.activity_id = activity_id;
        Data.get('save_reminders_comment/'+$scope.activity_id+'/'+closure_comment).then(function (results) {
            Data.toast(results);
            $("#first_comment").css("display","none");
            $("#second_comment").css("display","block");
            Data.get('reminders').then(function (results1) {
                //console.log(results1.length);
                if (results1.length>0)
                {
                    
                    $scope.reminders_list = results1;
                    $scope.reminder_count = results1.length;
                }
                
            });
            $timeout(function () { 
                Data.get('show_tasks').then(function (results) {
                    $scope.html = results[0].htmlstring;
                    $scope.trustedHtml_task = $sce.trustAsHtml($scope.html);
                    
                });
            }, 1000);
        });
        //$("#reminders_comment").css("display","none");
        //$scope.closure_comment = "";
    }
    $scope.create_new_activity = function(next_date)
    {
        activity_id = $("#temp_activity_id").val();
        console.log(activity_id);
        var createactivity = confirm('Are you sure ? You want Create New Activity ?');
        if (createactivity) 
        {
            cnext_date="0000-00-00";
            if (next_date)
            {
            cnext_date = next_date.substr(6,4)+"-"+next_date.substr(3,2)+"-"+next_date.substr(0,2)+" "+next_date.substr(11,5);        
            
            }
            Data.get('create_new_activity/'+activity_id+'/'+cnext_date).then(function (results) {
                Data.toast(results);
                $("#reminders").css("display","none");
                $("#reminders_comment").css("display","none");
                Data.get('reminders').then(function (results1) {
                    //console.log(results1.length);
                    if (results1.length>0)
                    {
                        $("#reminders").css("display","block");
                        $scope.reminders_list = results1;
                        $scope.reminder_count = results1.length;
                    }
                    Data.get('show_tasks').then(function (results2) {
                        $scope.html = results2[0].htmlstring;
                        $scope.trustedHtml_task = $sce.trustAsHtml($scope.html);
                        
                    });
                    
                });
                
            });
        }
        //$("#reminders_comment").css("display","none");
        //$scope.closure_comment = "";
    }

    $scope.close_reminders_comment = function()
    {
        $("#reminders_comment").css("display","none");
        $scope.closure_comment = "";
    }

    $scope.assign_task = function()
    {
        $scope.task = {};
        $("#file_task").val(0);
        
        //$("#assign_task").modal("show");
    }

    $scope.task_save_new = function(task)
    {
        task.file_name = $("#file_task_name").val();
        Data.post('task_save_new', {
            task: task
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") { 
                $("#assign_task").modal("hide");
                $('#file_task').fileinput('upload');
                $timeout(function () { 
                    Data.get('assist/user').then(function (results) {
                        $scope.html = results[0].htmlstring;
                        $scope.trustedHtml_assist = $sce.trustAsHtml($scope.html); 
                    });
                }, 100);
                location.reload();
            }
        });
    };

    $scope.contact_save_new = function (contact) {
        contact.assign_to = [$rootScope.user_id];
        contact.teams = [$rootScope.bo_id];
        Data.post('contact_add_new', {
            contact: contact
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $("#addclient").css("display","none");
                contact_id = results.contact_id;
                console.log(contact_id);
                $scope.clients = {};
                Data.get('selectcontact/Client').then(function (results1) {
                    $scope.clients = results1;
                    $scope.task.client_id = 0;
                    $scope.$watch($scope.task.client_id, function() {
                        $scope.task.client_id = contact_id;
                        console.log($scope.task.client_id);
                        $(".select2").select2();
                    },true);
                },3000);
            }
        });
    };
    $scope.addclient = function()
    {
        $("#addclient").css("display","block");
    }
    $scope.close = function()
    {
        $("#addclient").css("display","none");
    }


});

app.controller('UserDashboardMore', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    var goal_id = $routeParams.goal_id;
    $scope.searchdata = {};
    var currentdate = new Date();
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;

    $scope.$watch($scope.searchdata.start_date, function() {
        $scope.searchdata.start_date = start_date;
    }, true);

    $scope.$watch($scope.searchdata.end_date, function() {
        $scope.searchdata.end_date = end_date;
    }, true);

    $timeout(function () {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.searchdata.start_date)
        {
           start_date = $scope.searchdata.start_date.substr(6,4)+"-"+$scope.searchdata.start_date.substr(3,2)+"-"+$scope.searchdata.start_date.substr(0,2);         
        }
        if ($scope.searchdata.end_date)
        {
           end_date = $scope.searchdata.end_date.substr(6,4)+"-"+$scope.searchdata.end_date.substr(3,2)+"-"+$scope.searchdata.end_date.substr(0,2);         
        }
        Data.get('userdashboardmore/'+goal_id+'/'+start_date+'/'+end_date).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.dashboardMoreHtml = $sce.trustAsHtml($scope.html);        
        });
        
    },true);

    $scope.getuserdashboardmore = function(searchdata)
    {
        $timeout(function () {
            start_date="0000-00-00";
            end_date="0000-00-00";
            if (searchdata.start_date)
            {
                start_date = searchdata.start_date.substr(6,4)+"-"+searchdata.start_date.substr(3,2)+"-"+searchdata.start_date.substr(0,2);         
            }
            if (searchdata.end_date)
            {
                end_date = searchdata.end_date.substr(6,4)+"-"+searchdata.end_date.substr(3,2)+"-"+searchdata.end_date.substr(0,2);         
            }
            
            Data.get('userdashboardmore/'+goal_id+'/'+start_date+'/'+end_date).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.dashboardMoreHtml = $sce.trustAsHtml($scope.html);        
            });
        },true);
    }

});

app.controller('AdminDashboardMore_org', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    var user_id = $routeParams.user_id;
    
    var start_date = $routeParams.start_date;
    var end_date = $routeParams.end_date;

    var goal_category = $routeParams.goal_category;
    var goal_sub_category = $routeParams.goal_sub_category;
    $scope.searchdata = {};
    /*var currentdate = new Date();
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;*/

    $scope.$watch($scope.searchdata.start_date, function() {
        tstart_date = start_date.substr(8,2)+"-"+start_date.substr(5,2)+"-"+start_date.substr(0,4);  
        console.log(tstart_date);
        $scope.searchdata.start_date = tstart_date;
    }, true);

    $scope.$watch($scope.searchdata.end_date, function() {
        tend_date = end_date.substr(8,2)+"-"+end_date.substr(5,2)+"-"+end_date.substr(0,4);  
        console.log(tend_date);
        $scope.searchdata.end_date = tend_date;
    }, true);

    $timeout(function () {
        /*start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.searchdata.start_date)
        {
           start_date = $scope.searchdata.start_date.substr(6,4)+"-"+$scope.searchdata.start_date.substr(3,2)+"-"+$scope.searchdata.start_date.substr(0,2);         
        }
        if ($scope.searchdata.end_date)
        {
           end_date = $scope.searchdata.end_date.substr(6,4)+"-"+$scope.searchdata.end_date.substr(3,2)+"-"+$scope.searchdata.end_date.substr(0,2);         
        }*/
        Data.get('admindashboardmore/'+user_id+'/'+start_date+'/'+end_date+'/'+goal_category+'/'+goal_sub_category).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.dashboardMoreHtml = $sce.trustAsHtml($scope.html);        
        });
        
    },true);

    $scope.getadmindashboardmore = function(searchdata)
    {
        $timeout(function () {
            start_date="0000-00-00";
            end_date="0000-00-00";
            if (searchdata.start_date)
            {
                start_date = searchdata.start_date.substr(6,4)+"-"+searchdata.start_date.substr(3,2)+"-"+searchdata.start_date.substr(0,2);         
            }
            if (searchdata.end_date)
            {
                end_date = searchdata.end_date.substr(6,4)+"-"+searchdata.end_date.substr(3,2)+"-"+searchdata.end_date.substr(0,2);         
            }
            
            Data.get('admindashboardmore/'+user_id+'/'+start_date+'/'+end_date).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.dashboardMoreHtml = $sce.trustAsHtml($scope.html);        
            });
        },true);
    }

});


// GOALS ACHIEVED

app.controller('Goals_Achieved', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    
    var currentdate = new Date();
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    lmm = currentdate.toDateString().substr(4, 3);
    $scope.today_date = currentdate.getDate()+" "+lmm;

    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;
    $scope.goalsdata = {};
    $scope.$watch($scope.goalsdata.start_date, function() {
        $scope.goalsdata.start_date = start_date;
    }, true);

    $scope.$watch($scope.goalsdata.end_date, function() {
        $scope.goalsdata.end_date = end_date;
    }, true);
    
    $timeout(function () {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.goalsdata.start_date)
        {
           start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
           end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        
        Data.get('goals_achieved/'+start_date+'/'+end_date).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.goals_achievedHtml = $sce.trustAsHtml($scope.html); 
        });
        
    },true);

    $scope.getgoals_achieved = function(goalsdata)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.goalsdata.start_date)
        {
           start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
           end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        
        Data.get('goals_achieved/'+start_date+'/'+end_date).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.goals_achievedHtml = $sce.trustAsHtml($scope.html); 
        }); 
    }


});

// CRM ACTIVITIES

app.controller('CRM_Activities', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    
    var currentdate = new Date();
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    lmm = currentdate.toDateString().substr(4, 3);
    $scope.today_date = currentdate.getDate()+" "+lmm;

    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;
    $scope.goalsdata = {};
    $scope.$watch($scope.goalsdata.start_date, function() {
        $scope.goalsdata.start_date = start_date;
    }, true);

    $scope.$watch($scope.goalsdata.end_date, function() {
        $scope.goalsdata.end_date = end_date;
    }, true);
    
    $timeout(function () {
        start_date="0000-00-00";
        end_date="0000-00-00";
        // REMOVED FOR ALL DATA REPORT
        /*if ($scope.goalsdata.start_date)
        {
           start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
           end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        */
        Data.get('crm_activities/'+start_date+'/'+end_date).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.crm_activitiesHtml = $sce.trustAsHtml($scope.html); 
        });
        
    },true);

    $scope.getcrm_activities = function(goalsdata)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.goalsdata.start_date)
        {
           start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
           end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        
        Data.get('crm_activities/'+start_date+'/'+end_date).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.crm_activitiesHtml = $sce.trustAsHtml($scope.html); 
        }); 
    }


});


// ACTIVITY LOG

app.controller('Activity_Log', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    
    var currentdate = new Date();
    dd =  currentdate.getDate();
    dd = "01";
    mm =  (currentdate.getMonth()+1);
    lmm = currentdate.toDateString().substr(4, 3);
    $scope.today_date = currentdate.getDate()+" "+lmm;

    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var start_date = dd + "/" + mm + "/" + yy ;
    if (mm=='01' || mm=='03' || mm=='05' || mm=='07' || mm=='08' || mm=='10' || mm=='12')
    {
        dd = "31";
    }
    if (mm=='04' || mm=='06' || mm=='09' || mm=='11')
    {
        dd = "30";
    }
    if (mm=='02')
    {
        dd = "28";
    }
    var end_date = dd+ "/" + mm + "/" +  yy ;
    $scope.goalsdata = {};
    $scope.$watch($scope.goalsdata.start_date, function() {
        $scope.goalsdata.start_date = start_date;
    }, true);

    $scope.$watch($scope.goalsdata.end_date, function() {
        $scope.goalsdata.end_date = end_date;
    }, true);
    
    $timeout(function () {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.goalsdata.start_date)
        {
           start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
           end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        
        Data.get('activity_log/'+start_date+'/'+end_date).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.activity_logHtml = $sce.trustAsHtml($scope.html); 
        });
        
    },true);

    $scope.getactivity_log = function(goalsdata)
    {
        start_date="0000-00-00";
        end_date="0000-00-00";
        if ($scope.goalsdata.start_date)
        {
           start_date = $scope.goalsdata.start_date.substr(6,4)+"-"+$scope.goalsdata.start_date.substr(3,2)+"-"+$scope.goalsdata.start_date.substr(0,2);         
        }
        if ($scope.goalsdata.end_date)
        {
           end_date = $scope.goalsdata.end_date.substr(6,4)+"-"+$scope.goalsdata.end_date.substr(3,2)+"-"+$scope.goalsdata.end_date.substr(0,2);         
        }
        
        Data.get('activity_log/'+start_date+'/'+end_date).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.activity_logHtml = $sce.trustAsHtml($scope.html); 
        }); 
    }


});



// EMPLOYEE

// app.controller('Employee_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
//     $timeout(function () { 
//         Data.get('employee_list_ctrl').then(function (results) {
//             $rootScope.employees = results;
//         });
//     }, 100);
// });
    app.controller('Employee_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    $timeout(function () { 
        Data.get('employee_List_Ctrl').then(function (results) {
            $scope.resumes = results;
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
                Data.get('selectemployees').then(function (results) {
                    $scope.employees = results;
                });
            }, 100);
             $timeout(function () { 
                Data.get('getdatavalues_employee/manager_id').then(function (results) {
                    $scope.managers = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_employee/mobile_no').then(function (results) {
                    $scope.mobile_nos = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_employee/off_email').then(function (results) {
                    $scope.emails = results;
                });
            }, 100);
            $timeout(function () { 
                Data.get('getdatavalues_employee/dob').then(function (results) {
                    $scope.dobs = results;
                });
            }, 100);
             $timeout(function () { 
                Data.get('getdatavalues_employee/status').then(function (results) {
                    $scope.statuss = results;
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

    $scope.search_employees = function (searchdata) 
    {
        Data.post('search_employees', {
            searchdata: searchdata
        }).then(function (results) {
            $scope.$watch($scope.resumes, function() {
                $scope.resumes = {};
                $scope.resumes = results;
            },true);
        });
    };

    $scope.resetForm = function()
    {
        $scope.searchdata = {};
        $scope.$watch($scope.searchdata, function() {
            $scope.searchdata = {
            }
        });
        $("li.select2-selection__choice").remove();
        $(".select2").each(function() { $(this).val([]); });
        
        Data.get('employee_List_Ctrl').then(function (results) {
            $scope.resumes = results;
        });
    }
});
    
// app.controller('Employee_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
//     $scope.employee_add_new = {employee:''};
//     $scope.employee_add_new = function (employee) {
//         employee.file_name = $("#file_name_profile_pic").val();
//         Data.post('employee_add_new', {
//             employee: employee
//         }).then(function (results) {
//             Data.toast(results);
//             if (results.status == "success") {
//                 emp_id = results.emp_id;
//                 $('#file_profile_pic').fileinput('upload');
//                 $location.path('employee_list');
//             }
//         });
//     };
    
//     $timeout(function () { 
//         Data.get('selectteams').then(function (results) {
//             $scope.teams = results;
//         });
//     }, 100);
//     $timeout(function () { 
//         Data.get('selectsubteams').then(function (results) {
//             $scope.sub_teams = results;
//         });
//     }, 100);
// });
    
app.controller('Employee_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    $scope.employee_add_new = {employee:''};
      
    $scope.employee_add_new = function (employee) {
        var year=$('#year1').val();
        

        console.log(employee);
        employee.file_name = $("#file_name_profile_pic").val();
        Data.post('employee_add_new', {
            employee: employee,year:year
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                emp_id = results.emp_id;
                $('#file_profile_pic').fileinput('upload');
                $location.path('employee_list');
                // window.location.reload();
            }
        });
        
    };
    
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
});

app.controller('Employee_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    var emp_id = $routeParams.emp_id;
    // console.log(emp_id);
    $scope.activePath = null;
    $scope.employee={};
    Data.get('employee_edit_ctrl/'+emp_id).then(function (results) {
        // console.log(results);
        $scope.arr = ((results[0].teams).split(','));
        results[0].teams = $scope.arr;
        $scope.arr = ((results[0].sub_teams).split(','));
        results[0].sub_teams = $scope.arr;
        $scope.$watch($scope.employee, function() {
            $scope.employee = {};
            $scope.employee = {
                salu:results[0].salu,
                emp_id:results[0].emp_id,
                fname:results[0].fname,
                lname:results[0].lname,  
                address:results[0].address,
                emp_user:results[0].emp_user,
                bo_id:results[0].bo_id,
                teams:results[0].teams,
                sub_teams:results[0].sub_teams,
                company_name:results[0].company_name,
                mobile_no:results[0].mobile_no,
                alt_mobile_no:results[0].alt_mobile_no,
                off_phone:results[0].off_phone,
                off_email:results[0].off_email,
                designation_id:results[0].designation_id,
                manager_id:results[0].manager_id,
                doj:results[0].doj,
                dob:results[0].dob,
                basic_salary:results[0].basic_salary,
                sharing_per:results[0].sharing_per,
                employee_type:results[0].employee_type,
                leave_allowed:results[0].leave_allowed,
                leave_opening:results[0].leave_opening,
                travel_allowance:results[0].travel_allowance,
                year1:results[0].year1,
                incentive_per:results[0].incentive_per,
                increment_per:results[0].increment_per,
                increment_date:results[0].increment_date,
                weekly_off:results[0].weekly_off,
                rera_no:results[0].rera_no,
                allow_mobile:results[0].allow_mobile,
                allowed_ip:results[0].allowed_ip,
                status:results[0].status, 
                mobile_device_id:results[0].mobile_device_id

            };
            $timeout(function () {
                if (results[0].allow_mobile=="1")
                {
                    $('#allow_mobile').prop('checked', true);
                }
            },3000);
                
        },true);
    });
    $timeout(function () { 
        Data.get('selectemployee').then(function (results) {
            $scope.employees = results;
        });
    }, 100);
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
    Data.get('employee_images/'+emp_id).then(function (results) {
        $rootScope.employee_images = results;
    });
    Data.get('employee_documents/'+emp_id).then(function (results) {
        $rootScope.employee_documents = results;
    });
    
    $scope.employee_image_update = function (attachment_id,field_name,value) 
    { 
        Data.get('employee_image_update/'+attachment_id+'/'+field_name+'/'+value).then(function (results) {
        });
    };

    $scope.removeimage = function (attachment_id) {
        var deleteemployee = confirm('Are you absolutely sure you want to delete?');
        if (deleteemployee) {
            Data.get('removeimage/'+attachment_id).then(function (results) {
                Data.toast(results);
                Data.get('employee_images/'+emp_id).then(function (results) {
                    $rootScope.employee_images = results;
                });
            });
        }
    };
    $scope.removedocuments = function (attachment_id) {
        var deleteemployee = confirm('Are you absolutely sure you want to delete?');
        if (deleteemployee) {
            Data.get('removeimage/'+attachment_id).then(function (results) {
                Data.toast(results);
                Data.get('employee_documents/'+emp_id).then(function (results) {
                    $rootScope.employee_documents = results;
                });
            });
        }
    };
    
    $scope.employee_update = function (employee) {
      var year=$('#year1').val();
        Data.post('employee_update', {
            employee: employee,year:year
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $('#file_profile_pic').fileinput('upload');
                $('#file_documents').fileinput('upload');
                $location.path('employee_list');
                // window.location.reload();
            }
        });
    };
    
    $scope.employee_delete = function (employee) {
        //console.log(business_unit);
        var deleteemployee = confirm('Are you absolutely sure you want to delete?');
        if (deleteemployee) {
            Data.post('employee_delete', {
                employee: employee
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('employee_list');
                }
            });
        }
    };
    
});

app.controller('Myprofile_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    Data.get('myprofile').then(function (results) {
        $rootScope.datamyprofile = results;
    });

    $scope.myprofile_update = function (employee) {
        Data.post('myprofile_update', {
            employee: employee
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('/home');
            }
        });
    };
    
});

    
app.controller('SelectEmployee', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectemployee').then(function (results) {
            $rootScope.employees = results;
        });
    }, 100);
});


// MAILS CLIENT

app.controller('Mails_Client', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout, $sce) 
{

    var mailcategory = $routeParams.category;
    var mailcategory_id = $routeParams.category_id;
    $scope.back_to = "user";

    $scope.mail_sent = {};
    $scope.mail_sent = {subject:"",
                        text_message:'',
			cc_mail_id : ''
                       };
    $scope.client_name = "";
    $scope.email_ids = "";
    $timeout(function () { 
        Data.get('selectmail_template/'+mailcategory).then(function (results) {
            $rootScope.mail_templates = results;
        });
    }, 100);

    $scope.page_range = "1 - 30";
    $scope.total_records = 0;
    $scope.next_page_id = 0;
    $scope.regular_list = "Yes";
    $scope.inboxdata = {};
    $scope.pagenavigation = function(which_side)
    {
        $scope.inboxdata = {};
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

            Data.get('getsentitems/'+mailcategory+'/'+mailcategory_id+'/'+$scope.next_page_id).then(function (results) {
                $scope.inboxdata = results;
                $scope.page_range = parseInt($scope.next_page_id)+1+" - ";
                $scope.next_page_id = parseInt($scope.next_page_id)+30;
                $scope.page_range = $scope.page_range + $scope.next_page_id;
            });
        }
        else
        {
            
            //$scope.search_contacts($scope.searchdata,'pagenavigation');
            
        }
    }

    
    if (mailcategory == 'property')
    {
        $timeout(function () { 
            Data.get('getoneproperty/'+mailcategory_id).then(function (results) {
                $scope.mail_sent.to_mail_id = results[0].email_ids;
                $scope.email_ids =  results[0].email_ids;
                $scope.client_name = results[0].client_name;//results[0].name_title+' '+results[0].f_name+' '+results[0].l_name;
                $scope.back_to = "properties_list/"+results[0].proptype+'/0/0';
                
                
            });
        }, 100);
        $timeout(function () { 
            Data.get('getproperties_from_property/'+mailcategory_id).then(function (results) {
                $scope.selectproperties = results;
            });
        }, 100);
        
        $timeout(function () { 
            Data.get('getdocs_of_properties/'+mailcategory_id).then(function (results) {
                $scope.selectdocs = results;
            });
        }, 100);

        $timeout(function () { 
            Data.get('getreports_of_properties/'+mailcategory_id).then(function (results) {
                $scope.selectreports = results;
            });
        }, 100);


    }


    if (mailcategory == 'project')
    {
        $timeout(function () { 
            Data.get('getoneproject/'+mailcategory_id).then(function (results) {
                $scope.mail_sent.to_mail_id = results[0].email;
                $scope.email_ids =  results[0].email;
                $scope.developer_name = results[0].name_title+' '+results[0].f_name+' '+results[0].l_name;  
                $scope.client_name = results[0].name_title+' '+results[0].f_name+' '+results[0].l_name;
                $scope.back_to = "project_list/0";
            });
        }, 100);

    }

    if (mailcategory == 'enquiry')
    {
        $timeout(function () { 
            Data.get('getoneenquiry/'+mailcategory_id).then(function (results) {
                $scope.mail_sent.to_mail_id = results[0].email_ids;   
                $scope.email_ids =  results[0].email_ids;              
                $scope.client_name = results[0].client_name;//results[0].name_title+' '+results[0].f_name+' '+results[0].l_name;
                $scope.back_to = "enquiries_list/"+results[0].enquiry_off+"/0/0";
            });
        }, 100);

        $timeout(function () { 
            Data.get('getreports_of_enquiries/'+mailcategory_id).then(function (results) {
                $scope.selectreports = results;
            });
        }, 100);

        
        $timeout(function () { 
            Data.get('getproperties_from_enquiry/'+mailcategory_id).then(function (results) {
                $scope.selectproperties = results;
                getproperty_id = "";
                first = "yes";
                angular.forEach(results,function(value,key){
                    console.log("property_id"+value.property_id);
                    console.log("key"+key);
                    if (value.property_id)
                    {
                        
                        if (first=="yes")
                        {
                            
                            getproperty_id = value.property_id;
                            first = 'no';
                        }
                        else
                        {
                            if (value.property_id=='')
                            {

                            }
                            else{
                                getproperty_id =  getproperty_id + ',' + value.property_id;
                            }
                        }
                        
                    }
                });
                console.log(getproperty_id);
                Data.get('getreports_of_enquiries/'+getproperty_id).then(function (results) {
                    $scope.selectreports = results;
                });
            });
        }, 100);


        
    }

    if (mailcategory == 'referrals')
    {
        $timeout(function () { 
            Data.get('getonereferrals/'+mailcategory_id).then(function (results) {
                $scope.mail_sent.to_mail_id = results[0].email;  
                $scope.email_ids =  results[0].email;               
                $scope.client_name = results[0].name_title+' '+results[0].f_name+' '+results[0].l_name;
                $scope.back_to = "referrals_list/"+results[0].contact_off;
            });
        }, 100);

    }

    if (mailcategory == 'contacts')
    {
        $timeout(function () { 
            Data.get('getonecontacts/'+mailcategory_id).then(function (results) {
                memail = "";
                angular.forEach(results,function(value,key){
                    if (value.email)
                    {
                        memail = memail + value.email + ";";
                        console.log(memail);
                    }
                });
                $scope.mail_sent.to_mail_id = memail; //results[0].email;    
                $scope.email_ids =  memail;             
                $scope.client_name = results[0].name_title+' '+results[0].f_name+' '+results[0].l_name;
                $scope.back_to = "contacts_list/"+results[0].contact_off;
            });
        }, 100);

    }
    
    
    $scope.properties_selected = "";
    $scope.mail_sent.selected_properties = "";
    $scope.selectproperty_for_mail = function(property_id)
    {
        console.log(property_id);
        
        $scope.properties_selected = ""

        var data = '';
        var data1 = 0;
        var first = "Yes";
        var count = 0;
        $(":checked.check_element").each(
            function(index) 
            {
                if (first == 'Yes')
                {
                    data = "PROPERTYID_"+this.value;
                    data1 = this.value;
                    first = 'No';
                }
                else
                {
                    data = data+','+"PROPERTYID_"+this.value;
                    data1 = data1+','+this.value;
                }
                count = count + 1;
                
            }
        );
        $scope.properties_selected = data;
        $scope.mail_sent.selected_properties = data1;
        console.log($scope.mail_sent.selected_properties);

    }

    $scope.select_mail_template  = function(mail_template_id)
    {
        console.log("email ids:"+$scope.email_ids);
        console.log("client name:"+$scope.client_name);
        console.log("emp"+$rootScope.emp_name);
        $timeout(function () { 
            Data.get('select_mail_template/'+mail_template_id).then(function (results) {
                $('.wysihtml5-sandbox, .wysihtml5-toolbar').remove();
                str = results[0].text_message + results[0].footer_note;;
                str = str.replace("{{client_name}}",$scope.client_name);
                str = str.replace("{{mail_sender_name}}",$rootScope.emp_name);
                $scope.$watch($scope.mail_sent, function() {
                    $scope.mail_sent = {
                        to_mail_id : $scope.email_ids,   
                        subject : results[0].subject,   
                        text_message : str
                    }
                    $("#text_message").wysihtml5();
                    $('#text_message').css("display","block");
                })
                
            });
        }, 100);
    }


    $timeout(function () { 
        Data.get('getsentitems/'+mailcategory+'/'+mailcategory_id+'/'+$scope.next_page_id).then(function (results) {
            $scope.inboxdata = results;
            $scope.next_page_id = 30;
            $scope.mail_count = results[0].mail_count;
            $scope.total_records = results[0].mail_count;
            console.log(results[0].mail_count);
        });
    }, 100);


    $("#mails_client_inner").css("display","block");

    $scope.show_mails_to_send = function(client_id)
    {
        $timeout(function () { 
            Data.get('getsentitems/'+client_id).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_sentitems = $sce.trustAsHtml($scope.html);
            });
        }, 100);
        
        
        $("#mails_client_inner").css("display","block");
    };
    
    $scope.mail_send = function(mail_sent) 
    { 
        var mailconfirm= confirm('Are you sure ! You want send mail?');
        if (mailconfirm) 
        {
        
        }
        else
        {
            return;        
        }
        mail_sent.file_name = $("#file_name").val();
        $timeout(function () { 
            $('#file-1').fileinput('upload');
        },2000);
        var currentdate = new Date(); 
        var currday = (currentdate.getDate());
        var currmonth = (currentdate.getMonth()+1);
        if (currmonth<10)
        {
            currmonth = "0"+currmonth;
        }
        if (currday<10)
        {
            currday = "0"+currday;
        }
        
        var datetime = currentdate.getFullYear()+ "-" + (currmonth) + "-" +  (currday)+ " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
        var in_time = currentdate.getHours()+":"+currentdate.getMinutes();
        mail_sent.created_date = datetime;
	    mail_sent.category = mailcategory; 

	    mail_sent.category_id = mailcategory_id;
        mail_sent.mail_date = currentdate.getFullYear()+ "-" + (currmonth) + "-" +  (currday);
        
        console.log(mail_sent);
        var data = '';
        var first = "Yes";
        $(":checked.check_element").each(
            function(index) 
            {
                if (first == 'Yes')
                {
                    data = this.value;
                    first = 'No';
                }
                else
                {
                    data = data+','+this.value;
                }
            }
        );
        console.log(data);
        mail_sent.selected_files = data;
        mail_sent.text_message = $("#text_message").val(); 
        Data.post('mail_sent', {            
            mail_sent: mail_sent
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
				$scope.mail_sent = {};
                $scope.mail_sent = {subject:"",
                                    text_message:'',
                                    data:'',
                };
            }
            $timeout(function () { 
                Data.get('getsentitems/'+mailcategory+'/'+mailcategory_id+'/'+$scope.next_page_id).then(function (results) {
                    $scope.inboxdata = results;
                    $scope.next_page_id = 30;
                    $scope.mail_count = results[0].mail_count;
                    $scope.total_records = results[0].mail_count;
                    console.log(results[0].mail_count);
                    $("#email_accountstab").click();
                    
                });
            }, 100);
        });
    }
    
    $scope.showclientmail = function(mail_id)
    {
        $timeout(function () { 
            Data.get('showclientmail/'+mail_id).then(function (results) {
                $scope.mail_sent = {};
                $scope.mail_sent = {client_id:results[0].client_id,
                                    subject:results[0].subject,
                                    text_message:results[0].text_message,
                                    to_mail_id:results[0].to_mail_id,
                                    cc_mail_id:results[0].cc_mail_id,
                                    bcc_mail_id:results[0].bcc_mail_id,
                                    attachments:results[0].attachments
                                   };
            });
        }, 100);
    }
    
    
    $scope.showmailids = function (client_id,mail_type) {
		if (!client_id)
		{
			alert("Please Select Client First !!");
			return;
		}
        $("#showtomailids .mydropdown-menu").css("display","none");
        $("#showccmailids .mydropdown-menu").css("display","none"); 
        $("#showbccmailids .mydropdown-menu").css("display","none");
        $("#showattachments .mydropdown-menu").css("display","none");
        $("#showpdffile .mydropdown-menu").css("display","none");
		$timeout(function () { 
        	Data.get('showmailids/'+client_id+'/'+mail_type).then(function (results) {
				$scope.html = results[0].htmlstring;
                if (mail_type == 'to')
                {
                    $scope.trustedHtml_tomailids = $sce.trustAsHtml($scope.html);
				    $("#showtomailids .mydropdown-menu").css("display","block");        
                }
                if (mail_type == 'cc')
                {
                    $scope.trustedHtml_ccmailids = $sce.trustAsHtml($scope.html);
				    $("#showccmailids .mydropdown-menu").css("display","block");        
                }
                if (mail_type == 'bcc')
                {
                    $scope.trustedHtml_bccmailids = $sce.trustAsHtml($scope.html);
				    $("#showbccmailids .mydropdown-menu").css("display","block");        
                }
          	});
     	}, 100);
    };
    
    $scope.showattachments = function (client_id) {
		if (!client_id)
		{
			alert("Please Select Client First !!");
			return;
		}
        $("#showtomailids .mydropdown-menu").css("display","none");
        $("#showccmailids .mydropdown-menu").css("display","none"); 
        $("#showbccmailids .mydropdown-menu").css("display","none"); 
        $("#showpdffile .mydropdown-menu").css("display","none");
		$timeout(function () { 
        	Data.get('showattachments/'+client_id).then(function (results) {
				$scope.html = results[0].htmlstring;
                $scope.trustedHtml_attachments = $sce.trustAsHtml($scope.html);
				$("#showattachments .mydropdown-menu").css("display","block");        
          	});
     	}, 100);
    };
    
    $scope.showpdffile = function (client_id) {
		if (!client_id)
		{
			alert("Please Select Client First !!");
			return;
		}
        $("#showtomailids .mydropdown-menu").css("display","none");
        $("#showccmailids .mydropdown-menu").css("display","none"); 
        $("#showbccmailids .mydropdown-menu").css("display","none"); 
        $("#showpdffile .mydropdown-menu").css("display","none");
		$timeout(function () { 
        	Data.get('showpdffile/'+client_id).then(function (results) {
				$scope.html = results[0].htmlstring;
                $scope.trustedHtml_pdffile = $sce.trustAsHtml($scope.html);
				$("#showpdffile .mydropdown-menu").css("display","block");        
          	});
     	}, 100);
    };
    
    $scope.showpdfdata = function (invoice_filename) {
		$("#showtomailids .mydropdown-menu").css("display","none");
        $("#showccmailids .mydropdown-menu").css("display","none"); 
        $("#showbccmailids .mydropdown-menu").css("display","none");
        $("#showattachments .mydropdown-menu").css("display","none");
        $("#showpdffile .mydropdown-menu").css("display","none");
        $scope.html = '<embed src="//sgaeasy//application//api//v1//uploads//'+invoice_filename +'" style="width:875px;height:1800px;" type="application/pdf">';
        $scope.trustedHtml_show_pdf = $sce.trustAsHtml($scope.html);
        $("#show_pdf").modal("show");
    };
    
    $scope.close_pdf = function ()
	{
        $("#show_pdf").modal("hide");
	}
    
    $scope.getemailid = function (email_id,mail_type) {
        if (mail_type == 'to')
        {
            if ($scope.mail_sent.to_mail_id)
            {
                $scope.mail_sent.to_mail_id = $scope.mail_sent.to_mail_id + email_id +";";
            }
		    else
            {
                $scope.mail_sent.to_mail_id = email_id+';';
            }
		    $("#showtomailids .mydropdown-menu").css("display","none");
        }
        if (mail_type == 'cc')
        {
            if ($scope.mail_sent.cc_mail_id)
            {
                $scope.mail_sent.cc_mail_id = $scope.mail_sent.cc_mail_id +";"+ email_id;
            }
		    else
            {
                $scope.mail_sent.cc_mail_id = email_id+';';
            }
		    $("#showccmailids .mydropdown-menu").css("display","none");
        }
        if (mail_type == 'bcc')
        {
            if ($scope.mail_sent.bcc_mail_id)
            {
                $scope.mail_sent.bcc_mail_id = $scope.mail_sent.bcc_mail_id +";"+ email_id;
            }
		    else
            {
                $scope.mail_sent.bcc_mail_id = email_id+';';
            }
		    $("#showbccmailids .mydropdown-menu").css("display","none");
        }
    };
    
    $scope.getattachment = function (invoice_filename) {
        if ($scope.mail_sent.attachments)
        {
            $scope.mail_sent.attachments = $scope.mail_sent.attachments + invoice_filename +";";
        }
		else
        {
            $scope.mail_sent.attachments = invoice_filename+';';
        }
		$("#showattachments .mydropdown-menu").css("display","none");
    };
    
    $scope.addnewemail = function (client_id) {
		if (!client_id)
		{
			alert("Please Select Client First !!");
			return;
		}
        $("#showtomailids .mydropdown-menu").css("display","none");
        $("#showccmailids .mydropdown-menu").css("display","none"); 
        $("#showbccmailids .mydropdown-menu").css("display","none");
        $("#showattachments .mydropdown-menu").css("display","none");
        $("#addnewemailid").css("display","block");        
    };
    
    $scope.newemail = function (client_id) {
		if (!client_id)
		{
			alert("Please Select Client First !!");
			return;
		}
        $("#addnewemailid").css("display","none");
        newemailid = $("#newemailid").val();
        if (newemailid)
        {
            Data.get('newemail/'+client_id+'/'+newemailid).then(function (results) {
            
            });
        }
    };

    $scope.removedocument = function (attachment_id) {
        var deletedocument = confirm('Are you absolutely sure you want to delete?');
        if (deletedocument) {
            Data.get('removedocument/'+attachment_id).then(function (results) {
                Data.toast(results);
                $timeout(function () { 
                    Data.get('getreports_of_properties/'+mailcategory_id).then(function (results) {
                        $scope.selectreports = results;
                    });
                }, 100);
            });
        }
    };
    $scope.close_message_box = function ()
	{
        $("#view_message").modal("hide");
	}
    

    $scope.view_message = function(mail_id,text_message)
    {
        $('#view_message_data').html(text_message);
        $('#view_message').modal("show");
    }
    
});




// ALTERS

app.controller('MDashboard', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    $scope.detmdbattendance = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        start_date="0000-00-00";
        end_date="0000-00-00";
        $timeout(function () { 
            Data.get('detmdbattendance/'+start_date+'/'+end_date).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detmdbattendance = $sce.trustAsHtml($scope.html);
                $scope.start_date = results[0].start_date;
                $scope.end_date = results[0].end_date;
                $scope.detheading = "Attendance";
            });
        }, 100); 
    };
    
    $scope.detattendance = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        start_date = "0000-00-00";
        end_date = "0000-00-00";
        if ($scope.start_date)
        {
           start_date = $scope.start_date.substr(6,4)+"-"+$scope.start_date.substr(3,2)+"-"+$scope.start_date.substr(0,2);         
        }
        if ($scope.end_date)
        {
           end_date = $scope.end_date.substr(6,4)+"-"+$scope.end_date.substr(3,2)+"-"+$scope.end_date.substr(0,2);         
        }
        $timeout(function () { 
            Data.get('detmdbattendance/'+start_date+'/'+end_date).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detmdbattendance = $sce.trustAsHtml($scope.html);
                $scope.start_date = results[0].start_date;
                $scope.end_date = results[0].end_date;
            });
        }, 100); 
    };
    
    $scope.detmdblatecount = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        date = "0000-00-00";
        $timeout(function () { 
            Data.get('detmdblatecount').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detmdbattendance = $sce.trustAsHtml($scope.html);
                $scope.detheading = "Late Count";
            });
        }, 100); 
    };
    
    $scope.detmdblowaverage = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        date = "0000-00-00";
        $timeout(function () { 
            Data.get('detmdblowaverage').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detmdbattendance = $sce.trustAsHtml($scope.html);
                $scope.detheading = "Low Average";
            });
        }, 100); 
    };
    
    $scope.detmdbhighaverage = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        date = "0000-00-00";
        $timeout(function () { 
            Data.get('detmdbhighaverage').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detmdbattendance = $sce.trustAsHtml($scope.html);
                $scope.detheading = "High Average";
            });
        }, 100); 
    };
    
    $scope.detmdbapprovals = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        date = "0000-00-00";
        $timeout(function () { 
            Data.get('detmdbapprovals').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detmdbattendance = $sce.trustAsHtml($scope.html);
                $scope.detheading = "Approvals";
            });
        }, 100); 
    };
    
    $scope.detmdbabsent = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        date = "0000-00-00";
        $timeout(function () { 
            Data.get('detmdbabsent').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detmdbattendance = $sce.trustAsHtml($scope.html);
                $scope.detheading = "Absent";
            });
        }, 100); 
    };
    
    
    $scope.update_forapprovals = function(value,attendance_id)
    {
        Data.get('update_forapprovals/'+value+'/'+attendance_id).then(function (results) {
            alert('Status Updated ... !!!');
            $timeout(function () { 
                Data.get('detmdbapprovals').then(function (results) {
                    $scope.html = results[0].htmlstring;
                    $scope.trustedHtml_detmdbattendance = $sce.trustAsHtml($scope.html);
                    $scope.detheading = "Approvals";
                });
            }, 100);
        });
    };
            
    
    $scope.detattendance_excel = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        start_date = "0000-00-00";
        end_date = "0000-00-00";
        if ($scope.start_date)
        {
           start_date = $scope.start_date.substr(6,4)+"-"+$scope.start_date.substr(3,2)+"-"+$scope.start_date.substr(0,2);         
        }
        if ($scope.end_date)
        {
           end_date = $scope.end_date.substr(6,4)+"-"+$scope.end_date.substr(3,2)+"-"+$scope.end_date.substr(0,2);         
        }
        $timeout(function () { 
            Data.get('detmdbattendance_excel/'+start_date+'/'+end_date).then(function (results) {
                window.location="//sgaeasy//application//api//v1//uploads//attendance.xlsx";
            });
        }, 100); 
    };
    $scope.backmdashboard = function () {
        $("#detaileddashboard").css("display","none");
        $("#layoutdashboard").css("display","block");
    };
    
    $scope.close = function () 
    {
        $(".form_linechart").css("display","none");
        $(".form_attendance").css("display","none");
    };
    
    $scope.showgraph = function (emp_id)
    {
        $timeout(function () { 
            Data.get('showgraph/'+emp_id).then(function (results) {
                $timeout(function()
                {
                    $scope.$apply(function()
                    {
                        var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                        $("#line-chart").empty();
                        Morris.Line({element: 'line-chart',data:null});
                        Morris.Line({
                          element: 'line-chart',
                          data: results,
                          xkey: 'y',
                          ykeys: ["a"],
                          goals:[9],
                          goalLineColors:["#FCCB93"],
                          labels: ["Average"],
                          lineColors: ["#232C51"],
                          hideHover: 'auto',
                          redraw: true,
                          xLabelFormat: function(x) { 
                            var month = months[x.getMonth()];
                            return month;
                          },
                          dateFormat: function(x) {
                            var month = months[new Date(x).getMonth()];
                            return month;
                          },
                        });
                    });
                },200);
                $scope.employee_name = results[0].employee_name;
                $(".form_linechart").css("display","block");
            });
        }, 100); 
    }
    
    $scope.showatt = function (emp_id)
    {
        $timeout(function () { 
            Data.get('showatt/'+emp_id).then(function (results) {
                $timeout(function()
                {
                    $scope.$apply(function()
                    {
                        $scope.html = results[0].htmlstring;
                        $scope.trustedHtml_mdbatt = $sce.trustAsHtml($scope.html);  
                    });
                },200);
                $scope.employee_name = results[0].employee_name;
                $(".form_attendance").css("display","block");
            });
        }, 100); 
    }
    
    $scope.showattdet = function (emp_id,tdate)
    {
        $timeout(function () { 
            Data.get('showattdet/'+emp_id+'/'+tdate).then(function (results) {
                $timeout(function()
                {
                    $scope.$apply(function()
                    {
                        $scope.html = results[0].htmlstring;
                        $scope.trustedHtml_mdbatt = $sce.trustAsHtml($scope.html);  
                    });
                },200);
                $scope.employee_name = results[0].employee_name;
                $(".form_attendance").css("display","block");
            });
        }, 100); 
    }
    
    
});

app.controller('MDbNearBirthDay', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    $("#detaileddashboard").css("display","none");
    $("#layoutdashboard").css("display","block");
    $timeout(function () { 
        Data.get('mdbnearbirthday').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_mdbnearbirthday = $sce.trustAsHtml($scope.html);
        });
    }, 100);
    
});


// app.controller('MDbLeaves', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
// {
//     $("#detaileddashboard").css("display","none");
//     $("#layoutdashboard").css("display","block");
//     $timeout(function () { 
//         Data.get('mdbleaves').then(function (results) {
//             $scope.html = results[0].htmlstring;
//             $scope.trustedHtml_mdbleaves = $sce.trustAsHtml($scope.html);
//         });
//     }, 100);
    
// });

app.controller('MDbLeaves', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    $("#detaileddashboard").css("display","none");
    $("#layoutdashboard").css("display","block");
    $timeout(function () { 
        Data.get('mdbleaves').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_mdbleaves = $sce.trustAsHtml($scope.html);
        });
    }, 100);
    
});

app.controller('MDbLeavesApprovals', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    $("#detaileddashboard").css("display","none");
    $("#layoutdashboard").css("display","block");
    $timeout(function () { 
        Data.get('mdbleavesapprovals').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_mdbleavesapprovals = $sce.trustAsHtml($scope.html);
        });
    }, 100);
    $scope.employee_leave_id = 0;
    $scope.change_leave_status = function(value,employee_leave_id)
    {
        $scope.employee_leave_id = employee_leave_id;
        Data.get('change_leave_status/'+value+'/'+employee_leave_id).then(function (results) {
        });
        if (value=="Rejected")
        {
            $scope.setreject_comment();
        }
        if (value=="Approved")
        {
         window.location.reload();
            
        }
    }

    
    $scope.rejection_reason_data = "";
    $scope.setreject_comment = function()
    {
        $scope.rejection_reason_data = "";
        $("#reject_comment").css("display","block");
    }

    $scope.closerejection_reason = function()
    {
        console.log($scope.rejection_reason_data);
        $("#reject_comment").css("display","none");
    }

    $scope.rejection_reason = function()
    {
        $("#reject_comment").css("display","none");
        Data.get('rejection_reason/'+$scope.rejection_reason_data+'/'+$scope.employee_leave_id).then(function (results) {
            alert("Status Updated ...!!!");
        });
                window.location.reload();
    }

    
});


app.controller('MDbExpiringLease', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    $("#detaileddashboard").css("display","none");
    $("#layoutdashboard").css("display","block");
    $timeout(function () { 
        Data.get('mdbexpiringlease').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_mdbexpiringlease = $sce.trustAsHtml($scope.html);
        });
    }, 100);
});

app.controller('MDbPendingEnquiries', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    $("#detaileddashboard").css("display","none");
    $("#layoutdashboard").css("display","block");
    $timeout(function () { 
        Data.get('mdbpendingenquiries').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_mdbpendingenquiries = $sce.trustAsHtml($scope.html);
        });
    }, 100);
});



app.controller('MDbPendingActivities', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    $("#detaileddashboard").css("display","none");
    $("#layoutdashboard").css("display","block");
    $timeout(function () { 
        Data.get('mdbpendingactivities').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_mdbpendingactivities = $sce.trustAsHtml($scope.html);
        });
    }, 100);
});

app.controller('MDbPendingAgreement', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    $("#detaileddashboard").css("display","none");
    $("#layoutdashboard").css("display","block");
    $timeout(function () { 
        Data.get('mdbpendingagreement').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_mdbpendingagreement = $sce.trustAsHtml($scope.html);
        });
    }, 100);
});

app.controller('MDbReminders', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) 
{
    $("#detaileddashboard").css("display","none");
    $("#layoutdashboard").css("display","block");
    $timeout(function () { 
        Data.get('mdbreminders').then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.trustedHtml_mdbreminders = $sce.trustAsHtml($scope.html);
        });
    }, 100);
});

app.controller('ShowAlerts_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {


    $scope.client_meeting_count=0;
    $scope.client_meeting_goal=0;
    $scope.deal_count=0;
    $scope.deal_goal=0;
    $scope.enquiry_count=0;
    $scope.enquiry_goal=0;
    $scope.site_visit_count=0;
    $scope.site_visit_goal=0;

    /*Data.get('total_listings').then(function (results) {
        $rootScope.total_listings = results;
        
    });

    Data.get('branch_listings').then(function (results) {
        $rootScope.branch_listings = results;
        
    });


    Data.get('count_total_listings').then(function (results) {
        $scope.total_list_commercial = results[0].total_list_commercial;
        $scope.total_list_residential = results[0].total_list_residential;
        
    });
    

    $timeout(function () { 
        Data.get('completed_transactions').then(function (results) {
            $rootScope.completed_transactions = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('total_billings').then(function (results) {
            $rootScope.total_billings = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('getgoaldata').then(function (results) {
            $timeout(function () { 
                $scope.client_meeting_count=results[0].client_meeting_count;
                $scope.client_meeting_goal=results[0].client_meeting_goal;
                $scope.deal_count=results[0].deal_count;
                $scope.deal_goal=results[0].deal_goal;
                $scope.enquiry_count=results[0].enquiry_count;
                $scope.enquiry_goal=results[0].enquiry_goal;
                $scope.site_visit_count=results[0].site_visit_count;
                $scope.site_visit_goal=results[0].site_visit_goal;
            }, 200);
            
        });
    }, 100);*/

    
    /*$timeout(function () { 
        Data.get('showalerts_list_ctrl/birthday').then(function (results) {
            $rootScope.birthdayalerts = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('showalerts_list_ctrl/lease').then(function (results) {
            $rootScope.leasealerts = results;
        });
    }, 100);*/


    $scope.gourl = function(module_name,id)
    {
        $location.path(module_name+'_edit/'+id);
    }

    $timeout(function () { 
        Data.get('reminder_list').then(function (results) {
            $rootScope.listreminders = results;
        });
    });

    $scope.reminder_add = function (reminder) {
        Data.post('reminder_add', {
            reminder: reminder
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $rootScope.listreminders = {};
                $scope.reminder = {};
                Data.get('reminder_list').then(function (results) {
                    $rootScope.listreminders = results;
                });
            }
        });
    };

    $scope.deletereminder = function (reminder_id) {
        //console.log(business_unit);
        var deleteproject = confirm('Are you absolutely sure you want to delete?');
        if (deleteproject) {
            Data.get('deletereminder/'+reminder_id).then(function (results) {
                $rootScope.listreminders = {};
                Data.get('reminder_list').then(function (results) {
                    $rootScope.listreminders = results;
                });
            });
        }
    };


    $scope.detmdbnearbirthday = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        date = "0000-00-00";
        $timeout(function () { 
            Data.get('detmdbnearbirthday').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detaileddashboard = $sce.trustAsHtml($scope.html);
                $scope.detheading = "Near Birth Day";
            });
        }, 100); 
    };

    $scope.detmdbexpiringlease = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        date = "0000-00-00";
        $timeout(function () { 
            Data.get('detmdbexpiringlease').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detaileddashboard = $sce.trustAsHtml($scope.html);
                $scope.detheading = "Expiring Lease";
            });
        }, 100); 
    };

    $scope.detmdbpendingenquiries = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        date = "0000-00-00";
        $timeout(function () { 
            Data.get('detmdbpendingenquiries').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detmdbpendingenquiries = $sce.trustAsHtml($scope.html);
                $scope.detheading = "Pending Enquiries";
            });
        }, 100); 
    };

    $scope.detmdbpendingactivities = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        date = "0000-00-00";
        $timeout(function () { 
            Data.get('detmdbpendingactivities').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detaileddashboard = $sce.trustAsHtml($scope.html);
                $scope.detheading = "Pending Activities";
            });
        }, 100); 
    };

    $scope.detmdbpendingagreement = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        date = "0000-00-00";
        $timeout(function () { 
            Data.get('detmdbpendingagreement').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detaileddashboard = $sce.trustAsHtml($scope.html);
                $scope.detheading = "Pending Agreement";
            });
        }, 100); 
    };

    $scope.detmdbreminders = function () {
        $("#detaileddashboard").css("display","block");
        $("#layoutdashboard").css("display","none");
        date = "0000-00-00";
        $timeout(function () { 
            Data.get('detmdbreminders').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_detaileddashboard = $sce.trustAsHtml($scope.html);
                $scope.detheading = "Reminders";
            });
        }, 100); 
    };

    

    $scope.backmdashboard = function()
    {
        $("#detaileddashboard").css("display","none");
        $("#layoutdashboard").css("display","block");
    }

    $scope.change_leave_status = function(value,employee_leave_id)
    {
        Data.get('change_leave_status/'+value+'/'+employee_leave_id).then(function (results) {
        });
        if (value=="Rejected")
        {
            $scope.setreject_comment();
        }
    }

    $scope.reject_comment = "";
    $scope.rejection_reason_data = "";
    $scope.setreject_comment = function()
    {
        $("#reject_comment").css("display","block");
    }

    $scope.closerejection_reason= function()
    {
        
        $("#reject_comment").css("display","none");
    }

    $scope.rejection_reason = function()
    {
        console.log($scope.rejection_reason_data);
        $("#reject_comment").css("display","none");
    }


});


// EXPORT CONFIGURATIONS
    
app.controller('Export_Config', function ($scope, $rootScope, $routeParams, $location, $http, $timeout, $sce, Data) {
    $scope.activePath = null;
    Data.get('exporttablelist').then(function (results) {
        $rootScope.tablelists = results;
    });
    
    $scope.showcolumns = function (table_name) {
        
        $("#show").css("display","block");
        $("#showhtml").css("display","none");

        Data.get('showexportcolumns/'+table_name).then(function (results) {
            $rootScope.columnlists = results;
        });
    };

    $scope.update_exportdirect = function(column_name,form_id,value)
    {
        Data.get('update_exportdirect/'+column_name+'/'+form_id+'/'+value).then(function (results) {

        });
    }
    
});

// GOALS

app.controller('Goals_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    $scope.create_rights = false;
    $scope.update_rights = false;
    $scope.delete_rights = false;
    $scope.view_rights = false;
    $scope.export_rights = false;    
    $str = ($("#permission_string").val());
    if ((($str).indexOf("goals_view"))!=-1)
    {
        $scope.view_rights = true;
        console.log($scope.view_rights);
    }
    if ((($str).indexOf("goals_create"))!=-1)
    {
        $scope.create_rights = true;
        console.log($scope.create_rights);
    }
    if ((($str).indexOf("goals_update"))!=-1)
    {
        $scope.update_rights = true;
        console.log($scope.update_rights);
    }
    if ((($str).indexOf("goals_delete"))!=-1)
    {
        $scope.delete_rights = true;
        console.log($scope.delete_rights);
    }

    if (!$scope.view_rights)
    {
        $rootScope.listgoals = {};
        alert("You don't have rights to use this option..");
        return;
    }
  
    $timeout(function () { 
        Data.get('goals_list_ctrl').then(function (results) {
            $rootScope.listgoals = results;
        });
    }, 100);

});  
    
app.controller('Goals_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {
    
    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
        $scope.listusers = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/GOAL_CATEGORY').then(function (results) {
            $rootScope.goal_categories = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/GOAL_SUB_CATEGORY').then(function (results) {
            $rootScope.goal_sub_categories = results;
        });
    }, 100);

    $scope.change_goal_sub_category = function (goal_category) 
    { 
        Data.get('change_goal_sub_category/'+goal_category).then(function (results) { 
            $rootScope.goal_sub_categories = results; 
        });
    }
    $scope.goals = {};
    $scope.goals_add_new = function (goals) {
        Data.post('goals_add_new', {
            goals: goals
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('goals_list');
            }
        });
    };
    $scope.apply_goal = function(goals_jan)
    {
        console.log(goals_jan);
        console.log($scope.goals.goal_feb);
        if (!$scope.goals.goal_feb)
        {
           $scope.goals.goal_feb = goals_jan; 
        }
        if (!$scope.goals.goal_mar)
        {
           $scope.goals.goal_mar = goals_jan; 
        }
        if (!$scope.goals.goal_apr)
        {
           $scope.goals.goal_apr = goals_jan; 
        }
        if (!$scope.goals.goal_may)
        {
           $scope.goals.goal_may = goals_jan; 
        }
        if (!$scope.goals.goal_jun)
        {
           $scope.goals.goal_jun = goals_jan; 
        }
        if (!$scope.goals.goal_jul)
        {
           $scope.goals.goal_jul = goals_jan; 
        }
        if (!$scope.goals.goal_aug)
        {
           $scope.goals.goal_aug = goals_jan; 
        }
        if (!$scope.goals.goal_sep)
        {
           $scope.goals.goal_sep = goals_jan; 
        }
        if (!$scope.goals.goal_oct)
        {
           $scope.goals.goal_oct = goals_jan; 
        }
        if (!$scope.goals.goal_nov)
        {
           $scope.goals.goal_nov = goals_jan; 
        }
        if (!$scope.goals.goal_dec)
        {
           $scope.goals.goal_dec = goals_jan; 
        }

    }
});

app.controller('Goals_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {
    var goal_id = $routeParams.goal_id;
    $scope.activePath = null;

    $timeout(function () { 
        Data.get('selectusers').then(function (results) {
        $scope.listusers = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/GOAL_CATEGORY').then(function (results) {
            $rootScope.goal_categories = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectdropdowns/GOAL_SUB_CATEGORY').then(function (results) {
            $rootScope.goal_sub_categories = results;
        });
    }, 100);

    $scope.change_goal_sub_category = function (goal_category) 
    { 
        Data.get('change_goal_sub_category/'+goal_category).then(function (results) { 
            $rootScope.goal_sub_categories = results; 
        });
    }

    $scope.goals={};
    Data.get('goals_edit_ctrl/'+goal_id).then(function (results) {
        $scope.$watch($scope.goals, function() {
            $scope.goals = {};
            $scope.goals = {
                goal_id:results[0].goal_id,
                goal_category:results[0].goal_category,
                goal_sub_category:results[0].goal_sub_category,
                sequence_number:results[0].sequence_number,
                remarks:results[0].remarks,
                user_id:results[0].user_id,
                fy_year:results[0].fy_year,
                goal_jan:results[0].goal_jan,
                goal_feb:results[0].goal_feb,
                goal_mar:results[0].goal_mar,
                goal_apr:results[0].goal_apr,
                goal_may:results[0].goal_may,
                goal_jun:results[0].goal_jun,
                goal_jul:results[0].goal_jul,
                goal_aug:results[0].goal_aug,
                goal_sep:results[0].goal_sep,
                goal_oct:results[0].goal_oct,
                goal_nov:results[0].goal_nov,
                goal_dec:results[0].goal_dec,
                goal_per:results[0].goal_per
            }
        });
    });

    $scope.goals_update = function (goals) {
        Data.post('goals_update', {
            goals: goals
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('goals_list');
            }
        });
    };
    
    $scope.goals_delete = function (goals) {
        //console.log(business_unit);
        var deletegoals = confirm('Are you absolutely sure you want to delete?');
        if (deletegoals) {
            Data.post('goals_delete', {
                goals: goals
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('goals_list');
                }
            });
        }
    };
    
});

app.controller('SelectgGoals', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectgoals').then(function (results) {
            $rootScope.goalss = results;
        });
    }, 100);
});


app.controller('Goals_Report_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ,$sce) {

    var category = $routeParams.category;
    $scope.report_category = $routeParams.category;
    /*if (category=='enquiry')
    {
        $scope.report_category = "Enquiries";
    }
    if (category=='site_visit')
    {
        $scope.report_category = "Site Visits";
    }
    if (category=='client_meeting')
    {
        $scope.report_category = "Client Meetings";
    }
    if (category=='deal')
    {
        $scope.report_category = "Deal Done";
    }
    if (category=='direct')
    {
        $scope.report_category = "All Reports";
    }

    $scope.activePath = null;
    $rootScope.selectenquiries = {};
    $rootScope.selectsite_visits = {};
    $rootScope.selectclient_meetings = {};
    $rootScope.selectdeals = {};
    if (category=='enquiry')
    {
        $timeout(function () { 
            Data.get('goals_report_ctrl/'+category).then(function (results) {
                $rootScope.selectenquiries = results;
            });
        }, 100);
    }
    if (category=='site_visit')
    {
        $timeout(function () { 
            Data.get('goals_report_ctrl/'+category).then(function (results) {
                $rootScope.selectsite_visits = results;
            });
        }, 100);
    }
    if (category=='client_meeting')
    {
        $timeout(function () { 
            Data.get('goals_report_ctrl/'+category).then(function (results) {
                $rootScope.selectclient_meetings = results;
            });
        }, 100);
    }
    if (category=='deal')
    {
        $timeout(function () { 
            Data.get('goals_report_ctrl/'+category).then(function (results) {
                $rootScope.selectdeals = results;
            });
        }, 100);
    }

    if (category=='direct')
    {
        $timeout(function () { 
            Data.get('goals_report_ctrl/enquiry').then(function (results) {
                $rootScope.selectenquiries = results;
            });
        }, 100);

        $timeout(function () { 
            Data.get('goals_report_ctrl/site_visit').then(function (results) {
                $rootScope.selectsite_visits = results;
            });
        }, 100);

        $timeout(function () { 
            Data.get('goals_report_ctrl/client_meeting').then(function (results) {
                $rootScope.selectclient_meetings = results;
            });
        }, 100);

        $timeout(function () { 
            Data.get('goals_report_ctrl/deal').then(function (results) {
                $rootScope.selectdeals = results;
            });
        }, 100);


    }

    $scope.client_meeting_count=0;
    $scope.client_meeting_goal=0;
    $scope.deal_count=0;
    $scope.deal_goal=0;
    $scope.enquiry_count=0;
    $scope.enquiry_goal=0;
    $scope.site_visit_count=0;
    $scope.site_visit_goal=0;

    $timeout(function () { 
        Data.get('getgoaldata').then(function (results) {
            $timeout(function () { 
                $scope.client_meeting_count=results[0].client_meeting_count;
                $scope.client_meeting_goal=results[0].client_meeting_goal;
                $scope.deal_count=results[0].deal_count;
                $scope.deal_goal=results[0].deal_goal;
                $scope.enquiry_count=results[0].enquiry_count;
                $scope.enquiry_goal=results[0].enquiry_goal;
                $scope.site_visit_count=results[0].site_visit_count;
                $scope.site_visit_goal=results[0].site_visit_goal;
            }, 200);
            
        });
    }, 100);*/



    $timeout(function () { 
        Data.get('selectusers_subordinates').then(function (results) {
            $scope.listusers = results;
        });
    }, 100);

    $timeout(function () { 
        Data.get('selectteams').then(function (results) {
            $scope.teams_list = results;
            $scope.teams = results;
        });
    }, 100);
    $timeout(function () { 
        Data.get('selectsubteams').then(function (results) {
            $scope.sub_teams_list = results;
        });
    }, 100);

    $scope.getsubodrniate = function(teams,sub_teams)
    {
        Data.get('getsubodrniate/'+teams+'/'+sub_teams).then(function (results) {
            $scope.listusers = results;
        });
    }


    $scope.goalsdata = {};
    var currentdate = new Date();
    var afterdate = new Date (new Date().setDate(currentdate.getDate()+90)); 
    dd =  currentdate.getDate();
    if (dd<10)
    {
        dd = "0"+dd;
    }
    mm =  (currentdate.getMonth()+1);
    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = currentdate.getFullYear();
    var datetime = dd + "/" + mm + "/" + yy ;

    dd =  afterdate.getDate();
    if (dd<10)
    {
        dd = "0"+dd;
    }
    mm =  (afterdate.getMonth()+1);
    if (mm<10)
    {
        mm = "0"+mm;
    }
    yy = afterdate.getFullYear();
    var afterdatetime = dd+ "/" + mm + "/" +  yy ;

    $scope.$watch($scope.goalsdata.start_date, function() {
        $scope.goalsdata.start_date = datetime;
    }, true);

    $scope.$watch($scope.goalsdata.end_date, function() {
        $scope.goalsdata.end_date = afterdatetime;
    }, true);


    $scope.getgoaldatanew = function (goalsdata) {
        Data.post('getgoaldatanew', {
            goalsdata: goalsdata
        }).then(function (results) {
            $scope.html = results[0].htmlstring;
            $scope.goalsHtml = $sce.trustAsHtml($scope.html);
        });
    };
    
});  


// EMAIL ACCOUNTS

app.controller('Email_Accounts_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
  
    $timeout(function () { 
        Data.get('email_accounts_list_ctrl').then(function (results) {
            $rootScope.listemail_accounts = results;
        });
    }, 100);

});  
    
app.controller('Email_Accounts_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {    
     
    Data.get('selectusers').then(function (results) {
        $rootScope.listusers = results;
    });
    Data.get('selectteams').then(function (results) {
        $rootScope.listteams = results;
    });
    
    $scope.email_accounts_add_new = {email_accounts:''};
    $scope.email_accounts_add_new = function (email_accounts) {
        Data.post('email_accounts_add_new', {
            email_accounts: email_accounts
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('email_accounts_list');
            }
        });
    };
    
});

app.controller('Email_Accounts_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data,$timeout) {
    var email_accounts_id = $routeParams.email_accounts_id;
    $scope.activePath = null;

    Data.get('selectusers').then(function (results) {
        $rootScope.listusers = results;
    });
    Data.get('selectteams').then(function (results) {
        $rootScope.listteams = results;
    });

    $scope.email_accounts={};
    Data.get('email_accounts_edit_ctrl/'+email_accounts_id).then(function (results) {
        $scope.arr = ((results[0].assign_to).split(','));
        results[0].assign_to = $scope.arr;
        $scope.arr = ((results[0].teams).split(','));
        results[0].teams = $scope.arr;
        $scope.$watch($scope.email_accounts, function() {
            $scope.email_accounts = {};
            $scope.email_accounts = {
                email_accounts_id:results[0].email_accounts_id,
                email_account:results[0].email_account,
                smtp_host:results[0].smtp_host,
                smtp_username:results[0].smtp_username,
                mail_properties:results[0].mail_properties,
                from_name:results[0].from_name,
                smtp_port:results[0].smtp_port,
                password:results[0].password,
                confirm_password:results[0].password,
                teams:results[0].teams,
                assign_to:results[0].assign_to
            }
        });
    },true);

    $scope.email_accounts_update = function (email_accounts) {
        Data.post('email_accounts_update', {
            email_accounts: email_accounts
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('email_accounts_list');
            }
        });
    };
    
    $scope.email_accounts_delete = function (email_accounts) {
        //console.log(business_unit);
        var deleteemail_accounts = confirm('Are you absolutely sure you want to delete?');
        if (deleteemail_accounts) {
            Data.post('email_accounts_delete', {
                email_accounts: email_accounts
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('email_accounts_list');
                }
            });
        }
    };
    
});

app.controller('SelectEmail_Accounts', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selectemail_accounts').then(function (results) {
            $rootScope.email_accountss = results;
        });
    }, 100);

});


app.controller('SearchAll', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$sce ) {
    $scope.searchall = function(find_what)
    {
        $timeout(function () { 
            Data.get('searchall/'+find_what).then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_searchall = $sce.trustAsHtml($scope.html);
            });
        }, 100);
    }
});


app.controller('task_manager', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) {
    $scope.task = {};


    

    $scope.close_task_manager = function()
    {
        jQuery("#task_manager").stop().animate({ right: '-31%' }, 700, function() {
            $(".right_drawer_task").css("display","block");
            $(".close_drawer_task").css("display","none");
        });
    }
    
    $scope.open_task_manager = function()
    {
         //$scope.gettraining_data();
         $(".right_drawer_task").css("display","none");
         /*$timeout(function () { 
            Data.get('selectteammember').then(function (results) {
                $rootScope.teammembers = results;
            });
         }, 100);*/
         $scope.show_tasks();
         jQuery("#task_manager").stop().animate({ right: '-10' }, 700, function() {
             $(".close_drawer_task").css("display","block");
         });
    }
    
    $scope.assign_task = function()
    {
        //$scope.task = {};
        
        //$("#assign_task").modal("show");
    }
    
    $scope.show_tasks = function () {
        $timeout(function () { 
            Data.get('show_tasks').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_task = $sce.trustAsHtml($scope.html);
                
            });
        }, 1000);
    };
    $scope.activity_id = "";
    $scope.status = "";
    $scope.change_activity_status = function(status,activity_id)
    {
        $("#temp_activity_id").val(activity_id);
        console.log(status,activity_id);
        $scope.activity_id = activity_id;
        $("#temp_activity_id").val(activity_id);
        
        Data.get('change_activity_status/'+status+'/'+activity_id).then(function (results) {
            $("#reminders_comment").css("display","block");
            Data.get('show_tasks').then(function (results2) {
                $scope.html = results2[0].htmlstring;
                $scope.trustedHtml_task = $sce.trustAsHtml($scope.html);
                
            });
        }); 
        /*if (status=="Cancelled")
        {
            $("#reminders_comment").css("display","block");
        }
        else
        {
            Data.get('change_activity_status/'+status+'/'+activity_id).then(function (results) {
                if (status=='Completed')
                {
                    $location.path('activity_edit/'+activity_id);
                }
            }); 
        }*/
    }

    $scope.changevalueinput = function()
    {
        if ($scope.task.reminder_type=="Weekly")
        {
            $("#reminderdate").css("display","none");
            $("#reminderweek").css("display","block");
        }
        else
        {
            $("#reminderdate").css("display","block");
            $("#reminderweek").css("display","none");   
        }
    }
    
    $scope.task_save = function(task)
    {
        Data.post('task_save', {
            task: task
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $("#assign_task").modal("hide");
                $scope.show_tasks();
            }
        });
    };

    $scope.task_save_new = function(task)
    {
        Data.post('task_save_new', {
            task: task
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $("#assign_task").modal("hide");
                $scope.show_tasks();
            }
        });
    };
    

    

    $scope.update_taskstatus = function(value,task_manager_id)
    {
        Data.get('update_taskstatus/'+value+'/'+task_manager_id).then(function (results) {
             $scope.show_tasks();  
        });
    }
    
    $scope.remove_task = function(task_manager_id)
    {
        Data.get('remove_task/'+task_manager_id).then(function (results) {
             $scope.show_tasks();  
        });
    }
    
    $scope.show_tasks();
    
    
});

app.controller('task_manager_new', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout,$route, $sce ) {
    $scope.task = {};

    /*$scope.$watch($scope.task.assign_to, function() {
        $scope.task.assign_to = [$rootScope.user_id];
    });*/
    
    $scope.$watch($scope.task.teams, function() {
        $scope.task.teams = [$rootScope.bo_id];
    });
    $scope.close_task_manager_new = function()
    {
        jQuery("#task_manager_new").stop().animate({ right: '-31%' }, 700, function() {
            $(".right_drawer_task_new").css("display","block");
            $(".close_drawer_task_new").css("display","none");
        });
    }
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
        Data.get('selectcontact/Client').then(function (results) {
            $scope.clients = results;
        });
    }, 100);

    $scope.open_task_manager_new = function()
    {
         //$scope.gettraining_data();
         $(".right_drawer_task_new").css("display","none");
         /*$timeout(function () { 
            Data.get('selectteammember').then(function (results) {
                $rootScope.teammembers = results;
            });
         }, 100);*/
         $scope.show_tasks_new();
         jQuery("#task_manager_new").stop().animate({ right: '-10' }, 700, function() {
             $(".close_drawer_task_new").css("display","block");
         });
    }
    
    $scope.assign_task_new = function()
    {
        
        
        //$("#assign_task").modal("show");
    }
    
    $scope.show_tasks_new = function () {
        $timeout(function () { 
            Data.get('show_tasks_new').then(function (results) {
                $scope.html = results[0].htmlstring;
                $scope.trustedHtml_task_new = $sce.trustAsHtml($scope.html);
                
            });
        }, 1000);
    };
    
    $scope.change_activity_status = function(status,activity_id)
    {
        Data.get('change_activity_status/'+status+'/'+activity_id).then(function (results) {
            if (status=='Completed')
            {
                $location.path('activity_edit/'+activity_id);
            }
       }); 
    }

    $scope.changevalueinput = function()
    {
        if ($scope.task.reminder_type=="Weekly")
        {
            $("#reminderdate").css("display","none");
            $("#reminderweek").css("display","block");
        }
        else
        {
            $("#reminderdate").css("display","block");
            $("#reminderweek").css("display","none");   
        }
    }
    
    $scope.task_save_new = function(task)
    {
        Data.post('task_save_new', {
            task: task
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $("#assign_task").modal("hide");
                $scope.show_tasks_new();
            }
        });
    };
    
    $scope.update_taskstatus_new = function(value,task_manager_id)
    {
        Data.get('update_taskstatus_new/'+value+'/'+task_manager_id).then(function (results) {
             $scope.show_tasks_new();  
        });
    }
    
    $scope.remove_task_new = function(task_manager_id)
    {
        Data.get('remove_task_new/'+task_manager_id).then(function (results) {
             $scope.show_tasks_new();  
        });
    }
    
    $scope.show_tasks_new();
    
});

