app.controller("loanCtrl", function ($scope,$http,$filter,$rootScope, $location,CalculatorService,$window,$timeout) {
    $scope.msg = "This is loan controller";
    $scope.tab = 1;
    $scope.loanInward={
        particulars: ''
    };
    $scope.loanOutward={
        particulars: ''
    };
    $scope.btnSubmitDisableInward=false;
    $scope.btnSubmitDisableOutward=false;
    $scope.inwardSubmitStatus = false;
    $scope.outwardSubmitStatus = false;


    $scope.setTab = function(newTab){
        $scope.tab = newTab;
    };

    $scope.isSet = function(tabNum){
        return $scope.tab === tabNum;
    };

    $scope.selectedTab = {
        "color" : "white",
        "background-color" : "coral",
        "font-size" : "15px",
        "padding" : "5px"
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



    $scope.customerList={};
    $scope.loadAllCustomers=function(){
        var request = $http({
            method: "post",
            url: site_url+"/customer/get_customers",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.customerList=response.data.records;
            $scope.customerListByKey=alasql("select * from ?",[$scope.customerList]);
        });
    };//end of loadCustomer
    $scope.loadAllCustomers();
    $scope.searchCustomerByKey=function () {
        $scope.customerListByKey=alasql("select * from ? where person_name like '"+$scope.loanInward.customerSearchKey+"%'",[$scope.customerList]);
        $scope.loanInward.customer=$scope.customerListByKey[0];

    };

    $scope.dd = new Date().getDate();
    $scope.mm = new Date().getMonth()+1;
    $scope.yy = new Date().getFullYear();
    $scope.day= ($scope.dd<10)? '0'+$scope.dd : $scope.dd;
    $scope.month= ($scope.mm<10)? '0'+$scope.mm : $scope.mm;
    $scope.loanInward.loan_date=($scope.day+"/"+$scope.month+"/"+$scope.yy);
    $scope.loanOutward.loan_date=($scope.day+"/"+$scope.month+"/"+$scope.yy);

    $scope.saveLoanInward=function(loanInward){
        var inwardMaster={};
        inwardMaster.person_id=loanInward.customer.person_id;
        inwardMaster.particulars=loanInward.particulars;
        inwardMaster.inward_amount=loanInward.inward_amount;
        inwardMaster.loan_date=loanInward.loan_date;
        var request = $http({
            method: "post",
            url: site_url+"/loan/add_loan_inward_action",
            data: {
                loanIn: inwardMaster
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.inwardReportArray=response.data.records;
            if($scope.inwardReportArray.success==1){
                $scope.loanOutward.customer=$scope.loanInward.customer
                $scope.inwardSubmitStatus=true;
                $timeout(function() {
                    $scope.inwardSubmitStatus = false;
                }, 4000);
            }
            $scope.btnSubmitDisableInward=true;
        });
    };

    $scope.resetLoanInward=function(){
        $scope.loanInward.inward_amount="";
        $scope.loanInward.particulars="";
        $scope.btnSubmitDisableInward=false;
        $scope.loanInward.loan_date=($scope.day+"/"+$scope.month+"/"+$scope.yy);
    };
    $scope.saveLoanOutward=function(loanOutward){
        var outwardMaster={};
        outwardMaster.person_id=loanOutward.customer.person_id;
        outwardMaster.particulars=loanOutward.particulars;
        outwardMaster.outward_amount=loanOutward.outward_amount;
        outwardMaster.loan_date=loanOutward.loan_date;
        var request = $http({
            method: "post",
            url: site_url+"/loan/add_loan_outward_action",
            data: {
                loanOut: outwardMaster
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.outwardReportArray=response.data.records;
            if($scope.outwardReportArray.success==1){
                $scope.loanInward.customer=$scope.loanOutward.customer
                $scope.outwardSubmitStatus=true;
                $timeout(function() {
                    $scope.outwardSubmitStatus = false;
                }, 4000);
            }
            $scope.btnSubmitDisableOutward=true;
        });
    };

    $scope.resetLoanOutward=function(){
        $scope.loanOutward.outward_amount="";
        $scope.loanOutward.particulars="";
        $scope.btnSubmitDisableOutward=false;
        $scope.loanOutward.loan_date=($scope.day+"/"+$scope.month+"/"+$scope.yy);
    };

    var request = $http({
        method: "post",
        url: site_url+"/loan/get_loan_details_table",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        console.log(response.data.records);
        $scope.loanDetailsList=response.data.records;
    });

    $scope.customerLoanDetails={
        due: 0
    };

    $scope.showLoanDetailsById=function(personId){
        var request = $http({
            method: "post",
            url: site_url+"/loan/get_loan_details_by_customer_id",
            data: {
                person_id:personId
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.customerLoanDetails=response.data.records;
        });
    };

    $scope.showLoanDetails=function(loan){
        $scope.updateableLoanIndex=$scope.loanDetailsList.indexOf(loan);
        $scope.tab=4;
        $scope.showLoanDetailsById(loan.person_id);
    };

    $scope.getPreviousTotal=function (loan) {
         var x=$scope.customerLoanDetails.indexOf(loan);

         var i,sum=0;
         for(i=0;i<=x;i++){
            sum=sum+$scope.customerLoanDetails[i].outward_amount-$scope.customerLoanDetails[i].inward_amount;
         }
      return sum;
    };

    $scope.numberStyle=function(x){
        if(x>0){
            return {"color" : "green","text-align" : "right"};
        }else {
            return {"color" : "red","text-align" : "right"};
        }
    };

    $scope.deleteLoanDetails=function (loan) {
        if ($window.confirm("Do you want to delete this?")) {
            var request = $http({
                method: "post",
                url: site_url+"/loan/delete_row_from_loan_table",
                data: {
                    loan_id:loan.loan_id
                }
                ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function(response){
                //$scope.showLoanDetailsById(personId);
               // customerLoanDetails.splice(index,1);
                $scope.updateReport=response.data.records;
                if($scope.updateReport==true){
                    var i=$scope.customerLoanDetails.indexOf(loan);
                    $scope.customerLoanDetails.splice(i,1);
                    $scope.loanDetailsAfterDelete=alasql('SELECT sum(outward_amount) as outward,sum(inward_amount) as inward,(sum(outward_amount)-sum(inward_amount)) as due from ? ', [$scope.customerLoanDetails]);
                    $scope.loanDetailsList[$scope.updateableLoanIndex].outward=$scope.loanDetailsAfterDelete[0].outward;
                    $scope.loanDetailsList[$scope.updateableLoanIndex].inward=$scope.loanDetailsAfterDelete[0].inward;
                    $scope.loanDetailsList[$scope.updateableLoanIndex].due=$scope.loanDetailsAfterDelete[0].due;
                }
            });
        }

    };


});//end of Controller

