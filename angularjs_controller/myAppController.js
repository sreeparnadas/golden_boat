// site_url='http://127.0.0.1/golden_boat/index.php/';
// var project_url='http://127.0.0.1/golden_boat/';
var url=location.href;
var urlAux = url.split('/');

var base_url=urlAux[0]+'/'+urlAux[1]+'/'+urlAux[2]+'/'+urlAux[3]+'/';
var site_url=urlAux[0]+'/'+urlAux[1]+'/'+urlAux[2]+'/'+urlAux[3]+'/index.php/';
var project_url=base_url;
// var app = angular.module("myApp", ["ui.bootstrap"]);
var app = angular.module("myApp", ["ngRoute","ui.autocomplete","ui.bootstrap"]);
app.config(function($routeProvider) {
    $routeProvider
        .when("/", {
            templateUrl : site_url+"base/angular_view_main",
            controller : "mainController"
        })
        .when("/london", {
            templateUrl : site_url+"base/angular_view_london",
            controller : "londonCtrl"
        })
        .when("/paris", {
            templateUrl : site_url+"base/angular_view_paris",
            controller : "parisCtrl"
        }).when("/kolkata", {
        templateUrl : site_url+"base/angular_view_kolkata",
        controller : "kolkataCtrl"
    }).when("/vendor", {
        //templateUrl : site_url+"vendor/test",
        templateUrl : site_url+"vendor/angular_view_vendor",
        controller : "vendorCtrl"
    }).when("/customer", {
        templateUrl : site_url+"customer/angular_view_customer",
        controller : "customerCtrl"
    }).when("/product", {
        templateUrl : site_url+"product/angular_view_product",
        controller : "productCtrl"
    }).when("/sale", {
        //templateUrl : site_url+"sale/angular_view_sale",
        //templateUrl : site_url+"customer/angular_view_customer",
        templateUrl : site_url+"sale/angular_view_sale",
        controller : "saleCtrl"
    }).when("/bill2", {
            templateUrl : site_url+"sale/angular_view_bill2",
            controller : "bill2Ctrl"
     }).when("/loan", {
        templateUrl : site_url+"loan/angular_view_loan",
        controller : "loanCtrl"
    });
});

app.directive('a', function() {
    return {
        restrict: 'E',
        link: function(scope, elem, attrs) {
            if(attrs.ngClick || attrs.href === '' || attrs.href === '#'){
                elem.on('click', function(e){
                    e.preventDefault();
                });
            }
        }
    };
});

app.controller("londonCtrl", function ($scope,$http) {
    $scope.msg = "I love London";
    //$http.get("person.php").then(function(response) {
    $http.get(site_url+"base/get_persons").then(function(response) {
        $scope.myData = response.data.records;
    });
    $scope.removeItem = function (x) {
        // $scope.myData.splice(x, 1);
        var r_id='row_id_'+x;
        $('#'+r_id).remove();
    };
    $scope.orderByMe = function(x) {
        $scope.myOrderBy = x;
    };
});

app.controller("parisCtrl", function ($scope) {
    $scope.msg = "I love Paris";
});
app.controller("mainController", function ($scope) {
    $scope.msg = "I love Paris";
    wow = new WOW({}).init();
});

app.filter('capitalize', function() {
    return function(input) {
        return (!!input) ? input.split(' ').map(function(wrd){return wrd.charAt(0).toUpperCase() + wrd.substr(1).toLowerCase();}).join(' ') : '';
    }
});
app.run(function($rootScope,$timeout) {
    $rootScope.huiPrintDiv = function(printDetails,userCSSFile, numberOfCopies) {
        var divContents=$('#'+printDetails).html();
        var printWindow = window.open('', '', 'height=400,width=800');

        printWindow.document.write('<!DOCTYPE html>');
        printWindow.document.write('\n<html>');
        printWindow.document.write('\n<head>');
        printWindow.document.write('\n<title>');
        //printWindow.document.write(docTitle);
        printWindow.document.write('</title>');
        printWindow.document.write('\n<link href="'+project_url+'bootstrap-4.0.0/dist/css/bootstrap.min.css" type="text/css" rel="stylesheet" media="all">\n');
        printWindow.document.write('\n<link href="'+project_url+'css/print_style/basic_print.css" type="text/css" rel="stylesheet" media="all">\n');
        printWindow.document.write('\n<script src="angularjs/angularjs_1.6.4_angular.min.js"></script>\n');
        printWindow.document.write('\n<link href="'+project_url+'css/print_style/');
        printWindow.document.write(userCSSFile);
        printWindow.document.write('?v='+ Math.random()+'" rel="stylesheet" type="text/css" media="all"/>');


        printWindow.document.write('\n</head>');
        printWindow.document.write('\n<body>');
        printWindow.document.write(divContents);
        if(numberOfCopies==2) {
            printWindow.document.write('\n<hr>');
            printWindow.document.write(divContents);
        }
        printWindow.document.write('\n</body>');
        printWindow.document.write('\n</html>');
        $timeout(function() {
            printWindow.print();
        }, 2000);
        //printWindow.document.close();
//        printWindow.print();
        //printWindow.close();
    };
});


app.directive('goldDecimalPlaces',function(){
    return {
        link:function(scope,ele,attrs){
            ele.bind('keypress',function(e){
                var newVal=$(this).val()+(e.charCode!==0?String.fromCharCode(e.charCode):'');
                if($(this).val().search(/(.*)\.[0-9][0-9][0-9]/)===0 && newVal.length>$(this).val().length){
                    e.preventDefault();
                }
            });
        }
    };
});

//currency decimal places
app.directive('currencyDecimalPlaces',function(){
    return {
        link:function(scope,ele,attrs){
            ele.bind('keypress',function(e){
                var newVal=$(this).val()+(e.charCode!==0?String.fromCharCode(e.charCode):'');
                if($(this).val().search(/(.*)\.[0-9][0-9]/)===0 && newVal.length>$(this).val().length){
                    e.preventDefault();
                }
            });
        }
    };
});

//it will allow integer values
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
//it will allow decimal values
app.directive('numericValue', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                if (text) {
                    var transformedInput = text.replace(/[^0-9-.]/g, '');
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






app.run(function($rootScope){
    $rootScope.roundNumber=function(number, decimalPlaces){
        return parseFloat(parseFloat(number).toFixed(decimalPlaces));
    };
});




app.service('MathService', function() {
    this.add = function(a, b) { return a + b };
    this.subtract = function(a, b) { return a - b };
    this.multiply = function(a, b) { return a * b };
    this.divide = function(a, b) { return a / b };
});

app.service('CalculatorService', function(MathService){
    this.square = function(a) { return MathService.multiply(a,a); };
    this.cube = function(a) { return MathService.multiply(a, MathService.multiply(a,a)); };
});


app.run(function($rootScope,$timeout) {
    $rootScope.huiPrintDiv = function(printDetails,userCSSFile) {
        var divContents=$('#'+printDetails).html();
        var printWindow = window.open('', '', 'height=400,width=800');

        printWindow.document.write('<!DOCTYPE html>');
        printWindow.document.write('\n<html>');
        printWindow.document.write('\n<head>');
        printWindow.document.write('\n<title>');
        //printWindow.document.write(docTitle);
        printWindow.document.write('</title>');
        printWindow.document.write('\n<link href="'+project_url+'bootstrap-3.3.7-dist/css/bootstrap.min.css" type="text/css" rel="stylesheet" media="all">\n');
        printWindow.document.write('\n<link href="'+project_url+'css/print_style/basic_print.css" type="text/css" rel="stylesheet" media="all">\n');
        printWindow.document.write('\n<script src="angularjs/angularjs_1.6.4_angular.min.js"></script>\n');

        printWindow.document.write('\n<link href="'+project_url+'css/print_style/');
        printWindow.document.write(userCSSFile);
        printWindow.document.write('?v='+ Math.random()+'" rel="stylesheet" type="text/css" media="all"/>');


        printWindow.document.write('\n</head>');
        printWindow.document.write('\n<body>');
        printWindow.document.write(divContents);
        printWindow.document.write('\n</body>');
        printWindow.document.write('\n</html>');
        $timeout(function() {
            printWindow.print();
        }, 2000);
        //printWindow.document.close();
        //printWindow.print();
        // printWindow.close();
    };
});

app.filter('AmountConvertToWord', function() {
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
        return "Rupees "+words_string+" Only";
    };
});







