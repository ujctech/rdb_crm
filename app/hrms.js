// ELIBIBILITY

app.controller('Eligibility_List_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {
    
    $timeout(function () { 
        Data.get('eligibility_list_ctrl').then(function (results) {
            $scope.eligibilities = results;
        });
    }, 100);
});
    
    
app.controller('Eligibility_Add_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) 
{

    $timeout(function () { 
        Data.get('selectdesignation').then(function (results) {
            $scope.designations = results;
        });
    }, 100);

    Data.get('selectdropdowns/GOAL_CATEGORY').then(function (results) {
        $scope.dropdowns = results;
    });
    
    $scope.eligibility_add_new = {eligibility:''};
    $scope.eligibility_add_new = function (eligibility) {
        Data.post('eligibility_add_new', {
            eligibility: eligibility
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('eligibility_list');
            }
        });
    };
});
    
app.controller('Eligibility_Edit_Ctrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout) {
    var eligibility_id = $routeParams.eligibility_id;
    $scope.activePath = null;
    
    $timeout(function () { 
        Data.get('selectdesignation').then(function (results) {
            $scope.designations = results;
        });
    }, 100);

    Data.get('selectdropdowns/GOAL_CATEGORY').then(function (results) {
        $scope.dropdowns = results;
    });

    $scope.eligibility={};
    Data.get('eligibility_edit_ctrl/'+eligibility_id).then(function (results) {
        $scope.eligibility={};
        $scope.$watch($scope.eligibility, function() {
            $scope.eligibility = {
                eligibility_id:results[0].eligibility_id,
                eligibility_title:results[0].eligibility_title,
                designation_id:results[0].designation_id, 
                team:results[0].team,
                incentives:results[0].incentives,
                other_benefits:results[0].other_benefits,
                para_hike:results[0].para_hike
            }
        });
    });
    
    
    $scope.eligibility_update = function (eligibility) {
        
        Data.post('eligibility_update', {
            eligibility: eligibility
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                
                $location.path('eligibility_list');
            }
        });
    };
    
    $scope.eligibility_delete = function (eligibility) {
        //console.log(business_unit);
        var deleteeligibility = confirm('Are you absolutely sure you want to delete?');
        if (deleteeligibility) {
            Data.post('eligibility_delete', {
                eligibility: eligibility
            }).then(function (results) {
                Data.toast(results);
                if (results.status == "success") {
                    $location.path('eligibility_list');
                }
            });
        }
    };
    
});
    
app.controller('SelectEligibility', function ($scope, $rootScope, $routeParams, $location, $http, Data, $timeout ) {

    $timeout(function () { 
        Data.get('selecteligibility').then(function (results) {
            $scope.eligibilities = results;
        });
    }, 100);
});