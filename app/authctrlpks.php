app.controller('authCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $route, $interval, $sce, $timeout, $window) {
    //initially set those objects to null to avoid undefined error
    $scope.login = {};
    $scope.signup = {};
    $scope.doLogin = function (login) {
        Data.post('login', {
            login: login
        }).then(function (results) {
            Data.toast(results);
            //alert(" Please remember to LOGOUT before moving away from the system. Logging out is compulsory, which might affect your attendance. THANKS for your SUPPORT... CRM Team !!!")
            if (results.status == "success") {
                $("#forlogin").css("display","none");
                $("#navlogin").css("display","none");
                $("#logologin").css("display","none");
                $(".content-wrapper").css("background-color","#f5f4fd");
                //$(".wrapper").css("background","none");
                $(".wrapper").css("background","#000000");
                
                $(".wrapper").css("background-color","#000000");
                $(".content").css("background-color","#f5f4fd");
                console.log(results);
                $("#permission_string").val(results.permissions);
                //console.log("session"+$("#permission_string").val());
                if ((results.role).includes("Admin"))
                {
                    $location.path('admin');
                }
                /*else if ((results.role).includes("Manager") || (results.role).includes("Branch Head"))
                {
                    $location.path('manager');
                }*/
                else
                {
                    $location.path('user');
                }

                                
                /*$timeout(function () { 
                    Data.get('reminders').then(function (results1) {
                        $scope.reminders_list = results1;
                        $("#reminders").css("display","block");
                    });
                },1000);*/
                //$window.location.href = "page.html#/user";
                /*var today = new Date()
                var curHr = today.getHours();
                htmlstring = '';
                if(curHr<12){
                    htmlstring +='<p>Good Morning</p>';
                }else if(curHr<18){
                    htmlstring +='<p>Good Afternoon</p>';
                }else{
                    htmlstring +='<p>Good Evening</p>';
                }
                htmlstring +='<p>'+results.emp_name+'</p>';
                htmlstring +='<p></p>';
                htmlstring +='<p></p>';
                htmlstring +='<p></p>';
                htmlstring +='<p style="font-size:15px;text-align:right;">Team SGAEASy</p>';
                $(".welcomecenter").html(htmlstring);
                $interval(function () {
                    $(".welcomeDiv").fadeIn("slow").fadeOut("slow")
                }, 500, 5);
                if (results.role=="ADMIN")
                {
                    $location.path('admin');
                }
                else
                {
                    $location.path('user');
                    $timeout(function () { 
                        Data.get('menus').then(function (results) {
                            $scope.html = results[0].htmlstring;
                            //$scope.trustedHtml_menu = $sce.trustAsHtml($scope.html);
                            $("#ribbons").html($scope.html);
                            $("#ribbon_menu").css('display','block');
                        });
                    }, 100);
                }*/
            }
        });
    };
    
    $scope.signup = {username:'',password:''};
    $scope.signUp = function (signup) {
        Data.post('signUp', {
            signup: signup
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('dashboard');
            }
        });
    };
        
    $scope.logout = function () {
        Data.get('logout').then(function (results) {
            Data.toast(results);
            $(".wrapper").css("background","url(dist/img/banner.jpg)");
            $(".wrapper").css("background-size","100% 100%");

            $("#permission_string").val("0");
            console.log("session"+$("#permission_string").val());
            Data.get('session').then(function (results) {
                $rootScope.authenticated = false;
                $rootScope.admin = false;
                $rootScope.user_id = results.user_id;
                $rootScope.username = results.username;
                $rootScope.bo_id = results.bo_id;
                $rootScope.bo_name = results.bo_name;
                $rootScope.role = results.role;
                $rootScope.hasPermissions = results.permissions;
                $("#ribbons").html('');
                $("#ribbon_menu").css('display','none');
                $("#reminders").css("display","none");
                $location.path('login');
            });
            /*Data.get('menus').then(function (results) {
                $rootScope.menus = results;
            });*/
            /*Data.get('routes').then(function (results) {
                angular.forEach(results, function (route) {
                template_path = "pages/";
                if (route.role == "ALL")
                {
                    template_path = "pages/";
                    ///$("#leftmenu_button").css("display","none");  
                    //$("#rightmenu_button").css("display","none");  
                    //$("#login_button").css("display","block"); 
                }
                else
                {
                    template_path = "pages/"+route.role.toLowerCase()+"/"; 
                    //$("#leftmenu_button").css("display","none");  
                    //$("#rightmenu_button").css("display","none");  
                    //$("#login_button").css("display","block"); 
                }
                $routeProviderReference.when( "/"+route.menu_link, { templateUrl: template_path+route.menu_template, controller: route.menu_controller, reloadOnSearch: false } );
                });
                $routeProviderReference.otherwise({ redirectTo: '/dashboard' });
                $route.reload();
            });*/
            
            
            
        });
    }
    
});

app.controller('logoutCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
        
    
    
});




