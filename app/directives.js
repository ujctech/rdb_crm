app.directive('focus', function() {
    return function(scope, element) {
        element[0].focus();
    }      
});

app.directive('wysihtml5t', ['$timeout',
function($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {

      $timeout(function () {
      $('.wysihtml5-toolbar').remove();

        $(element).wysihtml5();
        alert("A");
      },1000);
    }
  }
}
]);

app.directive('ratingSetup', ['$timeout',
function($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {

      $timeout(function () {
          $("#rating").rating({'showCaption':false,showClear: false});
      },1000);
    }
  }
}
]);


app.directive('passwordMatch', [function () {
    return {
        restrict: 'A',
        scope:true,
        require: 'ngModel',
        link: function (scope, elem , attrs,control) {
            var checker = function () {
 
                //get the value of the first password
                var e1 = scope.$eval(attrs.ngModel); 
 
                //get the value of the other password  
                var e2 = scope.$eval(attrs.passwordMatch);
                if(e2!=null)
                return e1 == e2;
            };
            scope.$watch(checker, function (n) {
 
                //set the form control to valid if both 
                //passwords are the same, else invalid
                control.$setValidity("passwordNoMatch", n);
            });
        }
    };
}]);

app.directive('datatableSetup', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            element.dataTable(
                {
                    "bPaginate": true,
                    "bLengthChange": false,
                    "deferRender": true,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    'iDisplayLength': 50,
                    "asSorting": [[ 0, "asc" ]],
                    "bAutoWidth": false
                 });
                //scope.$apply();  
        },10000);
          
      }
    }
  }
]);

app.directive('datatableSetupt', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            element.dataTable(
                {
                    "bPaginate": true,
                    "bLengthChange": false,
                    "deferRender": true,
                    "bFilter": true,
                    "bSort": false,
                    "bInfo": true,
                    'iDisplayLength': 10,
                    "bAutoWidth": false
                 });
                //scope.$apply();  
        },10000);
          
      }
    }
  }
]);

app.directive('datatableSetupnew', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            element.dataTable(
                {
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    'iDisplayLength': 50,
                    "bAutoWidth": false
                 });
                //scope.$apply();  
        },1000);
          
      }
    }
  }
]);


app.directive('datatableSetupnosort', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            element.dataTable(
                {
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": true,
                    "bSort": false,
                    "bInfo": true,
                    'iDisplayLength': 10,
                    /*"deferRender": true,
                    "deferLoading": 10,
                    "processing": true,
                    "serverSide": true,*/
                    "bAutoWidth": false
                 });
            
        },10000);
      }
    }
  }
]);

app.directive('datatableSetupnosortext', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            element.dataTable(
                {
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": true,
                    "bSort": false,
                    "bInfo": true,
                    'iDisplayLength': 10,
                    "bAutoWidth": false
                 });
            
        },1000);
      }
    }
  }
]);

app.directive('datatableSetupnosort1', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            element.dataTable(
                {
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": false,
                    "bInfo": true,
                    'iDisplayLength': 5,
                    "bAutoWidth": false
                 });
            
        },10000);
      }
    }
  }
]);

app.directive('datatableSetupnosort2', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            element.dataTable(
                {
                    "bPaginate": false,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": false,
                    "bInfo": true,
                    'iDisplayLength': 50,
                    "bAutoWidth": false
                 });
            
        },1000);
      }
    }
  }
]);

app.directive('datatableSetupnosort3', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            element.dataTable(
                {
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": true,
                    "bSort": false,
                    "bInfo": true,
                    'iDisplayLength': 10,
                    "bAutoWidth": false
                 });
            
        },1000);
      }
    }
  }
]);

app.directive('datatableSetupnosortnofilter', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            element.dataTable(
                {
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": false,
                    "bInfo": true,
                    'iDisplayLength': 50,
                    "asSorting": [[ 0, "asc" ]],
                    "bAutoWidth": false
                 });
            
        },1000);
      }
    }
  }
]);

app.directive('datatableSetupno', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            element.dataTable(
                {
                    "bPaginate": false,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": false,
                    "bInfo": false,
                    'iDisplayLength': 50,
                    "bAutoWidth": false
                 });
            
        },1000);
      }
    }
  }
]);



app.directive('imagePicker', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            $("#matching_images1").imagepicker()
            $("#matching_images2").imagepicker()
        },1000);
      }
    }
  }
]);

app.directive('flexSlider', ['$timeout',
function($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {

      $timeout(function () {
          $('.flexslider').flexslider({
            slideshow:true,
            animation: "slide",
            itemWidth: 192,
            itemMargin: 2
          });
      },5000);
    }
  }
}
]);

app.directive('flexSliderrep', ['$timeout',
function($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {

      $timeout(function () {
          $('.flexslider').flexslider({
            slideshow:true,
            animation: "slide",
            slideshowSpeed: 5000,
            animationLoop: "true",
            itemWidth: 988,
            itemMargin: 40
          });
      },5000);
    }
  }
}
]);


app.directive('flexSliderimage', ['$timeout',
function($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {

      $timeout(function () {
          $('.flexslider').flexslider({
            slideshow:true,
            animation: "slide",
            slideshowSpeed: 5000,
            animationLoop: "true"
          });
      },5000);
    }
  }
}
]);

app.directive('lightSlider', ['$timeout',
function($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {
      $('#image-gallery').lightSlider({
        gallery:false,
        item:1,
        thumbItem:0,
        slideMargin: 10,
        speed:500,
        auto:true,
        pauseOnHover:true,
        loop:true,
        
        onSliderLoad: function() {
            $('#image-gallery').removeClass('cS-hidden');
            
        }  ,
        onAfterSlide: function (el) {
          
              // WHEN GETTING CLOSE TO END OF SLIDES, ADD MORE
              //currentSlide = '#apod' + (window.imgIds.length - 3);
              //if ($(currentSlide).parent().hasClass("active")) {
                  //generateNewSlides();
                
              //};
          
          },

    });
      
    }
  }
}
]);





app.directive('flexSlider1', ['$timeout',
function($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {
      $timeout(function () {
          $('#flexslider1').flexslider({
            slideshow:true,
            animation: "slide",
            itemWidth: 192,
            itemMargin: 2
          });
        },1000);
    }
  }
}
]);
app.directive('flexSlider2', ['$timeout',
function($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {

      $timeout(function () {
          $('#flexslider2').flexslider({
            slideshow:true,
            animation: "slide",
            itemWidth: 192,
            itemMargin: 2
          });
      },5000);
    }
  }
}
]);

app.directive('flexSlider3', ['$timeout',
function($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {

      $timeout(function () {
          $('#flexslider3').flexslider({
            slideshow:true,
            animation: "slide",
            itemWidth: 192,
            itemMargin: 2
          });
      },5000);
    }
  }
}
]);

app.directive('flexSlider4', ['$timeout',
function($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {

      $timeout(function () {
          $('#flexslider4').flexslider({
            slideshow:true,
            animation: "slide",
            
            itemWidth: 192,
            itemMargin: 2
          });
      },5000);
    }
  }
}
]);



/*app.directive('select2', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
          $(".select2").select2({tags:["4", "24"]}); 
          

        },1000);
      }
    }
  }
]);*/

app.directive("select2",function($timeout,$parse){
  return {
      restrict: 'AC',
      link: function(scope, element, attrs) {
          $timeout(function() {
              $(element).select2();              
          },1200); 
      }
  };
});

app.directive("select2filter",function($timeout,$parse){
  return {
      restrict: 'AC',
      link: function(scope, element, attrs) {
          $timeout(function() {
              $(element).select2({minimumInputLength: 2,
                allowClear: true,});                          
          },1200); 
      }
  };
});

app.directive("select2new", function ($timeout, $parse) {
  return {
      restrict// Code goes here
  : 'AC',
      require: 'ngModel',
      link: function (scope, element, attrs) {
          console.log(attrs);
          $timeout(function () {
              element.select2();
              element.select2Initialized = true;
          });

          var refreshSelect = function () {
              if (!element.select2Initialized) return;
              $timeout(function () {
                  element.trigger('change');
              });
          };

          var recreateSelect = function () {
              if (!element.select2Initialized) return;
              $timeout(function () {
                  element.select2('destroy');
                  element.select2();
              });
          };

          scope.$watch(attrs.ngModel, refreshSelect);

          if (attrs.ngOptions) {
              var list = attrs.ngOptions.match(/ in ([^ ]*)/)[1];
              // watch for option list change
              scope.$watch(list, recreateSelect);
          }

          if (attrs.ngDisabled) {
              scope.$watch(attrs.ngDisabled, refreshSelect);
          }
      }
  };
});


app.directive('stringToNumber', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ngModel) {
      ngModel.$parsers.push(function(value) {
        return '' + value;
      });
      ngModel.$formatters.push(function(value) {
        return parseFloat(value);
      });
    }
  };
});



app.directive('fileUploading', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
           $("#file-1").fileinput({
                uploadAsync: false,
                showUpload: false,
                uploadUrl: './api/v1/upload_files',
                allowedFileExtensions : ['jpg', 'png','gif','xls','xlsx','txt', 'image', 'video','3gp','docs','docx','doc','pdf'],
                overwriteInitial: false,
                maxFileSize: 500000,
                maxFilesNum: 1,
                maxFileCount:1,
                //uploadExtraData: function() {
                //    return {
                //        company_id1: $("#company_id1").val(),
                //    };
                //},
                slugCallback: function(filename) {
                    return filename.replace('(', '_').replace(']', '_');
                }
            });
            $('#file-1').on('fileloaded', function(event, file, previewId, index, reader) {
                $("#file_name").val(file.name);
                console.log("fileloaded");
            });
        },1000);
      }
    }
  }
]);
app.directive('bfi', function () {
  return {
    restrict: 'A',
    link: function (scope, element, attrs) {
      //element.bootstrapFileInput();
      element.fileinput('change');
      ///$('#input-id').fileinput('refresh');
    }
  }
});

app.directive('varianceValue', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, el, attr) {
          $timeout(function(){
            $(el).toggleClass("text-red", ($(el).text().indexOf('-') > -1));
                //if ($(el).text().indexOf('-') > -1)
                //{
                //    $(el).replace("-","..");
                //}
            //$(el).replace('-',' ');
          },0);
        },
    }
});

app.directive('required1', function() {
    return {
        restrict: 'A', // only for attributes
        compile: function(element) {
            // insert asterisk after elment 
            element.parent().find("label").append("<span class='required'>*</span>");
            //element.after("<span class='required'>*</span>");
        }
    };
});

app.directive('compileHtml',['$sce', '$parse', '$compile', 
  function($sce, $parse, $compile){
  return {
    link: function(scope,element,attr){
      var parsed = $parse(attr.compileHtml);
      function getStringValue() { return (parsed(scope) || '').toString(); }            
      scope.$watch(getStringValue, function (value) {
        var el = $compile($sce.getTrustedHtml(parsed(scope)) || '')(scope);
        element.empty();
        element.append(el);        
      });       
    } 
  };
}]);


app.directive('datepickerLocaldate', ['$parse', function ($parse) {
        var directive = {
            restrict: 'A',
            require: ['ngModel'],
            link: link
        };
        return directive;

        function link(scope, element, attr, ctrls) {
            var ngModelController = ctrls[0];

            // called with a JavaScript Date object when picked from the datepicker
            ngModelController.$parsers.push(function (viewValue) {
                // undo the timezone adjustment we did during the formatting
                viewValue.setMinutes(viewValue.getMinutes() - viewValue.getTimezoneOffset());
                // we just want a local date in ISO format
                return viewValue.toISOString().substring(0, 10);
            });

            // called with a 'yyyy-mm-dd' string to format
            ngModelController.$formatters.push(function (modelValue) {
                if (!modelValue) {
                    return undefined;
                }
                // date constructor will apply timezone deviations from UTC (i.e. if locale is behind UTC 'dt' will be one day behind)
                var dt = new Date(modelValue);
                // 'undo' the timezone offset again (so we end up on the original date again)
                dt.setMinutes(dt.getMinutes() + dt.getTimezoneOffset());
                return dt;
            });
        }
    }]);

app.directive('toggle', function(){
  return {
    restrict: 'A',
    link: function(scope, element, attrs){
      if (attrs.toggle=="tooltip"){
        $(element).tooltip();
      }
      if (attrs.toggle=="popover"){
        $(element).popover();
      }
    }
  };
})


app.directive('datepickerLocaldate', ['$parse', function ($parse) {
        var directive = {
            restrict: 'A',
            require: ['ngModel'],
            link: link
        };
        return directive;

        function link(scope, element, attr, ctrls) {
            var ngModelController = ctrls[0];

            // called with a JavaScript Date object when picked from the datepicker
            ngModelController.$parsers.push(function (viewValue) {
                // undo the timezone adjustment we did during the formatting
                viewValue.setMinutes(viewValue.getMinutes() - viewValue.getTimezoneOffset());
                // we just want a local date in ISO format
                return viewValue.toISOString().substring(0, 10);
            });

            // called with a 'yyyy-mm-dd' string to format
            ngModelController.$formatters.push(function (modelValue) {
                if (!modelValue) {
                    return undefined;
                }
                // date constructor will apply timezone deviations from UTC (i.e. if locale is behind UTC 'dt' will be one day behind)
                var dt = new Date(modelValue);
                // 'undo' the timezone offset again (so we end up on the original date again)
                dt.setMinutes(dt.getMinutes() + dt.getTimezoneOffset());
                return dt;
            });
        }
    }]);

app.directive('dateDirective', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        $timeout(function () {
            $(element).datepicker({
                autoclose:true,
                useCurrent:true,
                todayHighlight:true,
			    format: 'dd/mm/yyyy',
    		}).on("changeDate", function(e){
     		console.log(e.date);
    	});
        },1000);
      }
    }
  }
]);

app.directive('dateDirective_c', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        $timeout(function () {
            $(element).datepicker({
                autoclose:true,
                useCurrent:true,
                todayHighlight:true,
			    format: 'dd/mm/yyyy',
    		}).on("changeDate", function(e){
     		console.log(e.date);
        
    	});
        },1000);
      }
    }
  }
]);

/*app.directive('dateDirectivetime', ['$timeout',
function($timeout) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {
      $timeout(function () {
          $(element).datetimepicker({
            format: 'DD-MM-YYYY HH:mm'
      }).on("changeDate", function(e){
       console.log(e.date);
    });
      },1000);
    }
  }
}
]);*/

app.directive('datetimepicker', function(){
	return {
		require: '?ngModel',
		restrict: 'A',
		link: function(scope, element, attrs, ngModel){

			if(!ngModel) return; // do nothing if no ng-model

			ngModel.$render = function(){
				element.find('input').val( ngModel.$viewValue || '' );
			}

			element.datetimepicker({ 
        format: 'DD/MM/YYYY HH:mm'
				//language: 'it' 
			});

			element.on('dp.change', function(){
				scope.$apply(read);
			});

			read();

			function read() {
				var value = element.find('input').val();
				ngModel.$setViewValue(value);
			}
		}
	}
});

app.directive('datetimepickeractivity', function(){
	return {
		require: '?ngModel',
		restrict: 'A',
		link: function(scope, element, attrs, ngModel){

			if(!ngModel) return; // do nothing if no ng-model

			ngModel.$render = function(){
				element.find('input').val( ngModel.$viewValue || '' );
			}

			element.datetimepicker({ 
        defaultDate: new Date(),
        format: 'DD/MM/YYYY HH:mm'
				//language: 'it' 
			});

			element.on('dp.change', function(){
				scope.$apply(read);
			});

			read();

			function read() {
				var value = element.find('input').val();
				ngModel.$setViewValue(value);
			}
		}
	}
});




app.directive('datetimepicker1', function(){
	return {
		require: '?ngModel',
		restrict: 'A',
		link: function(scope, element, attrs, ngModel){

			if(!ngModel) return; // do nothing if no ng-model

			ngModel.$render = function(){
				element.find('input').val( ngModel.$viewValue || '' );
			}

			element.datetimepicker({ 
        format: 'DD-MM-YYYY HH:mm'
				//language: 'it' 
			});

			element.on('dp.change', function(){
				scope.$apply(read);
			});

			read();

			function read() {
				var value = element.find('input').val();
				ngModel.$setViewValue(value);
			}
		}
	}
});



/*app.directive('dateDirectivetime', function() {
  return {
      restrict: 'A',
      scope: {
          onChange: '&'
      },
      link: function(scope, element, attrs) {
        element.datetimepicker({
          format: "DD-MM-YYYY HH:mm"
          //all your options here
        }).on('changeDate', function(e) {
          scope.$apply(function(scope) {
            scope.onChange(e.date);
          });
        });
      }
  };
});*/



app.directive('dateDirective1', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        $timeout(function () {
            $(element).datepicker({
                autoclose:true,
                todayHighlight:true,
			    format: 'dd.mm.yyyy',
    		}).on("changeDate", function(e){
     		console.log(e.date);
    	});
        },1000);
      }
    }
  }
]);

app.directive('newdateDirective', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        $timeout(function () {
            $(element).datepicker({
                autoclose:true,
                todayHighlight:true,
			    format: 'mm-yyyy',
                minViewMode: 'months',
                viewMode: 'months',
                startView: "months", 
    		}).on("changeDate", function(e){
     		console.log(e.date);
    	});
        },1000);
      }
    }
  }
]);

app.directive('dateDirective4', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
        var nowDate = new Date();
        var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
        $timeout(function () {
            $(element).datepicker({
                autoclose:true,
                todayHighlight:true,
                startDate: 'now',
			    format: 'dd/mm/yyyy',
    		}).on("changeDate", function(e){
     		console.log(e.date);
    	});
        },1000);
      }
    }
  }
]);



app.directive('marquee', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            createMarquee({duration:300000});
        },1000);
      }
    }
  }
]);

app.directive('marquee1', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            createMarquee({duration:300000,direction:'up'});
        },1000);
      }
    }
  }
]);


app.directive('top', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
          $timeout(function () {
             $("#top_performer").find("div").each(function(){ 
                 next_div = $(this);
                 $(next_div).css("display","block");
                 console.log(next_div);
                 jQuery(next_div).stop().animate({ width: '333px' }, 1000, function() {
                 });
             });
        },1000);
      }
    }
  }
]);

app.directive('top1', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
          $timeout(function () {
             $("#top_performer1").find("div").each(function(){ 
                 next_div = $(this);
                 $(next_div).css("display","block");
                 console.log(next_div);
                 jQuery(next_div).stop().animate({ width: '333px' }, 1000, function() {
                 });
             });
        },1000);
      }
    }
  }
]);

app.directive('top3', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
          $timeout(function () {
             $("#top_performer3").find("div").each(function(){ 
                 next_div = $(this);
                 $(next_div).css("display","block");
                 console.log(next_div);
                 jQuery(next_div).stop().animate({ width: '333px' }, 1000, function() {
                 });
             });
        },1000);
      }
    }
  }
]);

app.directive('top4', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {
          $timeout(function () {
             $("#top_performer4").find("div").each(function(){ 
                 next_div = $(this);
                 $(next_div).css("display","block");
                 console.log(next_div);
                 jQuery(next_div).stop().animate({ width: '333px' }, 1000, function() {
                 });
             });
        },1000);
      }
    }
  }
]);


app.directive('ratingSetup', ['$timeout',
  function($timeout) {
    return {
      restrict: 'A',
      link: function(scope, element, attrs) {

        $timeout(function () {
            /*$("#rating1").rating({'showCaption':false,showClear: false});
            $("#rating3").rating({'showCaption':false,showClear: false});
            $("#rating4").rating({'showCaption':false,showClear: false});
            //$("#cc_rating").rating({'showCaption':false,showClear: false, 'stars':'2','max':'3'});
            $('#cc_rating').rating({'showCaption':false,showClear: false, 'stars':'2'});
            $('#golden_rating').rating({'showCaption':false,showClear: false, 'stars':'3'});
            $('#cd_rating').rating({'showCaption':false,showClear: false});
            $('#ph_rating').rating({'showCaption':false,showClear: false});
            $("#rating5").rating({});
            $("#rating2").rating({});
            $(".rating").rating({'showCaption':false,showClear: false});
            $(".red_rating").rating({'showCaption':false,showClear: false});*/
            $(".yellow_rating").rating({'showCaption':false,showClear: false,'stars':'4'});
            
        },1000);
      }
    }
  }
]);

app.directive('hideZero', [ function() {
    return {
      require: '?ngModel', // get a hold of NgModelController
      link: function(scope, element, attrs, ngModel) {
        ngModel.$formatters.push(function(value) {
          if (value == 0) {
              return '';
          }
          return value;
        });
      }
    };
  }]);

app.directive('showAmount', [ function() {
  return {
    require: '?ngModel', // get a hold of NgModelController
    link: function(scope, element, attrs, ngModel) {
      ngModel.$formatters.push(function(value) {
        if(val >= 10000000) val = (val/10000000).toFixed(2) + ' Cr';
        else if(val >= 100000) val = (val/100000).toFixed(2) + ' Lac';
        else if(val >= 1000) val = (val/1000).toFixed(2) + ' K';
        return val;
      });
    }
  };
}]);


  


app.directive('numbersOnly', function () {
        return {
            require: 'ngModel',
            link: function (scope, element, attr, ngModelCtrl) {
                function fromUser(text) {
                    if (text) {
                        var transformedInput = text.replace(/[^0-9-]/g, '');
                        if (transformedInput !== text) {
                            ngModelCtrl.$setViewValue(transformedInput);
                            ngModelCtrl.$render();
                        }
                        return transformedInput;
                    }
                    return undefined;
                }
                ngModelCtrl.$parsers.push(fromUser);
            }
        };
    });


app.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            
            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });
            });
        }
    };
}]);

app.service('fileUpload', ['$http', function ($http) {
    this.uploadFileToUrl = function(file, uploadUrl){
        var fd = new FormData();
        fd.append('file-1', file);
        $http.post(uploadUrl, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(){
        })
        .error(function(){
        });
    }
}]);


app.directive("drawing", function() {
  return {
    restrict: "A",
    link: function(scope, element) {
      var canvas = element[0];
      var ctx = canvas.getContext('2d');

      //loadImage("https://crm.rdbrothers.com/api/v1/uploads/property/{{edited_image}}");
      //loadImage("http://placehold.it/100x80");
      //ctx.drawImage("api/v1/uploads/property/"+scope.edited_image, left, top, width, height);
      
      // variable that decides if something should be drawn on mousemove
      var drawing = false;

      // the last coordinates before the current move
      var lastX;
      var lastY;

      element.bind('mousedown', function(event) {
        if (event.offsetX !== undefined) {
          lastX = event.offsetX;
          lastY = event.offsetY;
        } else {
          lastX = event.layerX - event.currentTarget.offsetLeft;
          lastY = event.layerY - event.currentTarget.offsetTop;
        }

        // begins new line
        ctx.beginPath();

        drawing = true;
      });
      element.bind('mousemove', function(event) {
        if (drawing) {
          // get current mouse position
          if (event.offsetX !== undefined) {
            currentX = event.offsetX;
            currentY = event.offsetY;
          } else {
            currentX = event.layerX - event.currentTarget.offsetLeft;
            currentY = event.layerY - event.currentTarget.offsetTop;
          }

          draw(lastX, lastY, currentX, currentY);

          // set current coordinates to last one
          lastX = currentX;
          lastY = currentY;
        }

      });

      element.bind('mouseup', function(event) {
        // stop drawing  
        drawing = false;
      });

      // canvas reset
      function reset() {
        element[0].width = element[0].width;
      }

      function loadImage(source){
        var img = new Image();
        img.src = source;

        img.onload = function() {
          ctx.drawImage(img, 0, 0, img.width, img.height,
                             0, 0, canvas.width, canvas.height);
        };  
      };
      
      function draw(startX, startY, endX, endY) {
        ctx.moveTo(startX, startY);
        ctx.lineTo(endX, endY);
        ctx.strokeStyle = "red";
        ctx.stroke();
      }
    }
  };
});

app.filter('removeHTMLTags', function() {
	return function(text) {
		return  text ? String(text).replace(/<[^>]+>/gm, '') : '';
	};
});
