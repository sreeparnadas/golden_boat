app.controller("customerCtrl", function ($scope,$http,$compile,$timeout) {
    $scope.msg = "This is Customer controller";
    $scope.tab = 1;

    $scope.isUpdateable=false;
    $scope.isDuplicateCust=false;
    $scope.postOffices=[];
    $scope.sort = {
        active: '',
        descending: undefined
    };
    $scope.setTab = function(newTab){
        $scope.customer=angular.copy($scope.defaultCustomer);
        $scope.tab = newTab;
        if(newTab==1){
            $scope.isUpdateable=false;
            $scope.isDuplicateCust=false;
            $scope.submitNewCustomer=false;
        }
    };

    $scope.isSet = function(tabNum){
        return $scope.tab === tabNum;
    };
    // staff authentication
    $scope.staffAuthenticated=false;
    $scope.pswCheckingDiv=true;
    $scope.staff_password="";
    $scope.checkStaffAuthentication=function(staff_password){
        if(staff_password=="AW131-201819"){
            $scope.staffAuthenticated=true;
            $scope.pswCheckingDiv=false;
        }
        else{
            alert("Incorrect password");
        }
    };



    $scope.defaultCustomer={
        person_id: "",
        person_name: "",
        billing_name: "",
        sex: "",
        mobile_no: "",
        phone_no: "",
        email_id: "",
        aadhar_no: "",
        pan_no: "",
        address1: "",
        address2: "",
        city: "",
        area: "",
        district_id: "",
        post_office: "",
        pin: "",
        gst_number: "",
        state_id: "19"
    };
    $scope.customer={

    };
    $scope.districts=[
        {district_id: "0", district_name: "--Select--"}
    ];

    $scope.reportArray={message:'New Customer',success:"0"};

    $scope.reMobile = /^(\+\d{1,3}[- ]?)?\d{10}$/;
    $scope.reGST = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;


    $scope.saveCustomer=function(customer){
        $scope.isDuplicateCust=false;
        $scope.submitNewCustomer=false;
        $scope.master=angular.copy($scope.customer);
        var request = $http({
            method: "post",
            url: site_url+"/customer/insert_customer",
            data: {
                customer: $scope.master
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.reportArray=response.data.records;
            if($scope.reportArray.success==1){
                $scope.isUpdateable=true;
                $scope.submitNewCustomer=true;
                $timeout(function() {
                    $scope.submitNewCustomer = false;
                }, 4000);
                $scope.customer.person_id=$scope.reportArray.person_id;
                //insert into post office object if not exist//
                var poIndex=$scope.postOffices.findIndex(k=>k.post_office===customer.post_office);
                if(poIndex== -1){
                    var po={};
                    po.post_office=customer.post_office;
                    po.pin=customer.pin;
                    $scope.postOffices .unshift(po);
                }
                //insert into addressOne object if not exist//
                var addressOne =$scope.addressOne .findIndex(k=>k.address1===customer.address1);
                if(addressOne== -1){
                    var address={};
                    address.address1=customer.address1;
                    $scope.addressOne .unshift(address);
                }
                //insert into areas object if not exist//
                var areaIndex=$scope.areas.findIndex(k=>k.area===customer.area);
                if(areaIndex== -1){
                    var ar={};
                    ar.area=customer.area;
                    $scope.areas .unshift(ar);
                }
                //insert into city object if not exist//
                var cityIndex=$scope.cities .findIndex(k=>k.city===customer.city);
                if(cityIndex== -1){
                    var ct={};
                    ct.city=customer.city;
                    $scope.cities.unshift(ct);
                }

                $scope.customerForm.$setPristine();
                $scope.customerList.unshift($scope.customer);
                $scope.updateableCustomerIndex=0;
                return {"color" : "green","text-align" : "right"};
            }else {
                if ($scope.reportArray.error_code == 1062) {
                    $scope.isDuplicateCust=true;
                }else {
                    alert($scope.reportArray.error);
                }
            }
        });
    };
    //GET ALL STATES
    var request = $http({
        method: "post",
        url: site_url+"/customer/get_states",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.states=response.data.records;
    });


    //select all areas
    var request = $http({
        method: "post",
        url: site_url+"/customer/get_areas",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.areas=response.data;
    });

    //select all Cities
    var request = $http({
        method: "post",
        url: site_url+"/customer/get_cities",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.cities=response.data;
    });

    //select all Post_office
    var request = $http({
        method: "post",
        url: site_url+"/customer/get_post_office",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.postOffices=response.data.records;
    });

    //select address1

    var request = $http({
        method: "post",
        url: site_url+"/customer/get_address_one",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.addressOne=response.data;
    });

    $scope.selectState=function(stateID){
        var request = $http({
            method: "post",
            url: site_url+"/customer/get_districts",
            data: {
                stateID: stateID
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            //$('#feature').html(response);
            $scope.districts=response.data.records;
        });
    };
    $scope.customer=angular.copy($scope.defaultCustomer);
    $scope.copyCustomerName=function(){
        $scope.customer.billing_name=$scope.customer.person_name;
    };
    $scope.resetCustomer=function(){
        $scope.customer=$scope.defaultCustomer;
        $scope.isUpdateable=false;
        $scope.submitNewCustomer=false;
        $scope.updateStatus=false;
        $scope.isDuplicateCust=false;
    };
    var request = $http({
        method: "post",
        url: site_url+"/customer/get_customers",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.customerList=response.data.records;
    });
    $scope.getIcon = function(column) {

        var sort = $scope.sort;

        if (sort.active == column) {
            return sort.descending
                ? 'glyphicon-chevron-up'
                : 'glyphicon-chevron-down';
        }

        return 'glyphicon-star';
    };
    $scope.changeSorting = function(column) {

        var sort = $scope.sort;

        if (sort.active == column) {
            sort.descending = !sort.descending;
        }
        else {
            sort.active = column;
            sort.descending = false;
        }
    };

    $scope.updateableVendorIndex=-1;

    $scope.updateCustomerFromTable=function(customer){
        $scope.customer=angular.copy(customer);
        var index=$scope.customerList.indexOf(customer);
        $scope.updateableCustomerIndex=index;
        $scope.tab=1;
        $scope.isUpdateable=true;
        $scope.selectState(customer.state_id);
        $scope.customerForm.$setPristine();
    };

    $scope.updateCustomerByCustomerId=function(customer){
        $scope.updateStatus=false;
        $scope.master=angular.copy(customer);
        var request = $http({
            method: "post",
            url: site_url+"/customer/update_customer_by_customer_id",
            data: {
                customer: $scope.master
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.reportArray=response.data.records;
            if($scope.reportArray.success==1){
                $scope.isUpdateable=true;
                $scope.updateStatus=true;
                $timeout(function() {
                    $scope.updateStatus = false;
                }, 4000);
                $scope.customerList[$scope.updateableCustomerIndex]=$scope.customer;
                $scope.customerForm.$setPristine();
            }

        });
    };

    $scope.testState=function(){
        $scope.testResult=alasql('SELECT count(*) as number_of_state from ? where state_id="19"',[$scope.customerList]);
    };



    //*************************************************
    $scope.myOption = {
        options: {
            html: true,
            focusOpen: true,
            onlySelectValid: false,
            source: function (request, response) {
                //var result = objArray.map(a => a.area);
                var data=$scope.areas.map(function(a) {return a.area;});
                data = $scope.myOption.methods.filter(data, request.term);
                if (!data.length) {
                    data.push({
                        label: 'not found',
                        value: ''
                    });
                }
                // add "Add Language" button to autocomplete menu bottom
                /*data.push({
                    //label: $compile('<a class="btn btn-link ui-menu-add" ng-click="addLanguage()">Add Area</a>')($scope),
                    label: $compile('<a class="btn btn-link ui-menu-add" ng-click="addLanguage()">Add Area</a>')($scope),
                    value: ''
                });*/
                response(data);
            }
        },
        methods: {}
    };

    //*************************************************

    $scope.myOptionCity = {
        options: {
            html: true,
            focusOpen: true,
            onlySelectValid: false,
            source: function (request, response) {
                //var result = objArray.map(a => a.area);
                var data=$scope.cities.map(function(a) {return a.city;});
                data = $scope.myOptionCity.methods.filter(data, request.term);
                if (!data.length) {
                    data.push({
                        label: 'not found',
                        value: ''
                    });
                }
                // add "Add Language" button to autocomplete menu bottom
               /* data.push({
                    //label: $compile('<a class="btn btn-link ui-menu-add" ng-click="addLanguage()">Add Area</a>')($scope),
                    label: $compile('<a class="btn btn-link ui-menu-add" ng-click="addLanguage()">Add Area</a>')($scope),
                    value: ''
                });*/
                response(data);
            }
        },
        methods: {}
    };
    //*************************************************
    $scope.myOptionPostOffice = {
        options: {
            html: true,
            focusOpen: false,
            onlySelectValid: false,
            source: function (request, response) {
                //var result = objArray.map(a => a.area);
                var data=$scope.postOffices.map(function(a) {return a.post_office;});
                data = $scope.myOptionPostOffice.methods.filter(data, request.term);
                if (!data.length) {
                    data.push({
                        label: 'not found',
                        value: ''
                    });
                }
                // add "Add Language" button to autocomplete menu bottom
                data.push({
                    //label: $compile('<a class="btn btn-link ui-menu-add" ng-click="addLanguage()">Add Area</a>')($scope),
                    label: '',
                    value: ''
                });
                response(data);
            }
        },
        methods: {}
    };
    //*************************************************

    $scope.myOptionAddressOne = {
        options: {
            html: true,
            focusOpen: false,
            onlySelectValid: false,
            source: function (request, response) {
                //var result = objArray.map(a => a.area);
                var data=$scope.addressOne.map(function(a) {return a.address1;});
                data = $scope.myOptionAddressOne.methods.filter(data, request.term);
                if (!data.length) {
                    data.push({
                        label: 'not found',
                        value: ''
                    });
                }
                // add "Add Language" button to autocomplete menu bottom
                data.push({
                    //label: $compile('<a class="btn btn-link ui-menu-add" ng-click="addLanguage()">Add Area</a>')($scope),
                    label: '',
                    value: ''
                });
                response(data);
            }
        },
        methods: {}
    };
    //*************************************************
    $scope.saveToExcel=function (fileName) {
        //$scope.testVendorList=alasql('select SUM(CONVERT(number,state_id)) as tot from ?',[$scope.vendorList]);
        //alasql('SELECT * INTO CSV("Myfile.csv",{headers:true}) FROM ?', [$scope.vendorList]);
        //alasql('SELECT * INTO XLSX("Myfile.xlsx",{headers:true}) FROM ?', [$scope.vendorList]);
        var mystyle = {
            headers:true,
            column: {style:{Font:{Bold:"1"}}}
            /* rows: {3:{style:{Font:{Color:"#FF0077"}}}},
             cells: {1:{1:{
                         style: {Font:{Color:"#00FFFF"}}
                     }}}*/
        };
        alasql('SELECT * INTO XLSXML(?,?) FROM ?',[fileName,mystyle,$scope.customerList]);
    };
    $scope.saveToCSV=function (fileName) {
        //$scope.testVendorList=alasql('select SUM(CONVERT(number,state_id)) as tot from ?',[$scope.vendorList]);
        //alasql('SELECT * INTO CSV("Myfile.csv",{headers:true}) FROM ?', [$scope.vendorList]);
        //alasql('SELECT * INTO XLSX("Myfile.xlsx",{headers:true}) FROM ?', [$scope.vendorList]);
        var mystyle = {
            headers:true,
            column: {style:{Font:{Bold:"1"}}}
        };

        alasql('SELECT * INTO CSV(?,?) FROM ? order by person_name',[fileName,mystyle,$scope.customerList]);
    };



    $scope.getPinByPostOffice=function(cust){
        var p=cust.post_office;
        var poIndex=$scope.postOffices.findIndex(k=>k.post_office===p);
        cust.pin=$scope.postOffices[poIndex].pin;
    };





});
