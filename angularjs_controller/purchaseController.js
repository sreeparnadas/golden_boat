app.controller("purchaseCtrl", function ($scope,$http,$filter) {
    $scope.msg = "This is Purchase controller";
    $scope.tab = 1;
    $scope.showNotification=false;
    $scope.purchaseDetails={
        discount: 0,
        sgst: 0,
        cgst: 0,
        igst: 0,
        sgstRate: 0,
        cgstRate: 0,
        igstRate: 0,
        rate:0,
        discount:0,
        quantity:0,
        amount:0
    };

    $scope.purchaseMaster={
        cgstFactor: 0,
        sgstFactor: 0,
        igstFactor: 0
    };
    $scope.setGstFactor=function(){
        if($scope.purchaseMaster.vendor.state_id!=19){
            $scope.purchaseMaster.cgstFactor=0;
            $scope.purchaseMaster.sgstFactor=0;
            $scope.purchaseMaster.igstFactor=1;
        }else{
            $scope.purchaseMaster.cgstFactor=0.5;
            $scope.purchaseMaster.sgstFactor=0.5;
            $scope.purchaseMaster.igstFactor=0;
        }

        var gstRate=$scope.purchaseDetails.product.gst_rate;
        $scope.purchaseDetails.sgstRate=(gstRate*$scope.purchaseMaster.sgstFactor)/100;
        $scope.purchaseDetails.cgstRate=(gstRate*$scope.purchaseMaster.cgstFactor)/100;
        $scope.purchaseDetails.igstRate=(gstRate*$scope.purchaseMaster.igstFactor)/100;

        var purchaseValue=($scope.purchaseDetails.quantity)*($scope.purchaseDetails.rate);
        $scope.purchaseDetails.sgst=(purchaseValue*$scope.purchaseDetails.sgstRate);
        $scope.purchaseDetails.cgst=(purchaseValue*$scope.purchaseDetails.cgstRate);
        $scope.purchaseDetails.igst=(purchaseValue*$scope.purchaseDetails.igstRate);
    };

    $scope.gstRateChangeOfProduct=function(){
        var gstRate=$scope.purchaseDetails.product.gst_rate;
        $scope.purchaseDetails.sgstRate=(gstRate*$scope.purchaseMaster.sgstFactor)/100;
        $scope.purchaseDetails.cgstRate=(gstRate*$scope.purchaseMaster.cgstFactor)/100;
        $scope.purchaseDetails.igstRate=(gstRate*$scope.purchaseMaster.igstFactor)/100;
    };


    $scope.setTab = function(newTab){
        $scope.tab = newTab;
    };

    $scope.isSet = function(tabNum){
        return $scope.tab === tabNum;
    };

      // $scope.purcaseData.purchase_date = new Date();
    $scope.totalPurchaseAmount=0;
    $scope.$watchCollection('purchaseDetailsDataList', function (newNames) {
        var totalAmount = 0;
        for(var i = 0; i < newNames.length; i++){
            var product = newNames[i];
            totalAmount += (product.amount);
        }
        $scope.totalPurchaseAmount=totalAmount;
    });



    $scope.vendorList={};
    $scope.loadAllVendors=function(){
        var request = $http({
            method: "post",
            url: site_url+"/vendor/get_vendors",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.vendorList=response.data.records;
        });
    };//end of loadVendors
    $scope.loadAllVendors();

    $scope.prductList={};
    $scope.loadAllProducts=function(){
        var request = $http({
            method: "post",
            url: site_url+"/product/get_inforce_products",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.prductList=response.data.records;
        });
    };//end of loadVendors
    $scope.loadAllProducts();

    $scope.removeRow = function(index){
        // remove the row specified in index
        $scope.purchaseDetailsDataList.splice( index, 1);
        // if no rows left in the array create a blank array
        if ($scope.purchaseDetailsDataList.length() === 0){
            $scope.purchaseDetailsDataList = [];
        }
    };



    $scope.purchaseDetailsDataList=[];
   /* $scope.setUnit=function(){
        $scope.purchaseDetails.unit_name=($("#product-name option:selected").attr('unit-name'));
        $scope.purchaseDetails.unit_id=($("#product-name option:selected").attr('unit-id'));
        $scope.purchaseDetails.product_name=$("#product-name option:selected").text();
    };*/
    $scope.setAmount=function(){
         $scope.purchaseDetails.amount=($scope.purchaseDetails.quantity)*($scope.purchaseDetails.rate);
    };

    $scope.currentIndex=-1;
    //$scope.purchaseDetailsDataList = [{}];
    $scope.addPurchaseDetailsData=function(purchase){
        $scope.showNotification=false;
        var test=0;
        angular.forEach($scope.purchaseDetailsDataList, function(value, key) {

            if(angular.equals(value,purchase))
                test++;

        });
        if(test==0){
            var test=angular.copy(purchase);
            var total=0;
            $scope.purchaseDetailsDataList.unshift(test);
        }else{
            $scope.showNotification=true;

        }

    };
    $scope.getDiscount=function(){
        var purchaseValue=($scope.purchaseDetails.quantity)*($scope.purchaseDetails.rate);
        var disc_rate=$scope.purchaseDetails.discount;
        var amt_disc=purchaseValue*(disc_rate/100);
        return amt_disc;
    };
    $scope.setGst=function(){
        var purchaseValue=($scope.purchaseDetails.quantity)*($scope.purchaseDetails.rate);
            $scope.purchaseDetails.sgst=(purchaseValue*$scope.purchaseDetails.sgstRate);
            $scope.purchaseDetails.cgst=(purchaseValue*$scope.purchaseDetails.cgstRate);
            $scope.purchaseDetails.igst=(purchaseValue*$scope.purchaseDetails.igstRate);
    };

    $scope.setDefaultUnit=function(){
        $scope.purchaseDetails.test=$scope.purchaseDetails.product.unit_id;
    };

    $scope.savePurchase=function(purchaseMaster,purchaseDetails){
        var pm=angular.copy(purchaseMaster);
        var pdl=angular.copy(purchaseDetails);
        pm.purchase_date = $filter('date')(pm.purchase_date, 'yyyy-MM-dd');
        var request = $http({
            method: "post",
            url: site_url+"/purchase/save_new_purchase",
            data: {
                purchase_master: pm
                ,purchse_details: pdl
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){

           // $scope.reportArray=response.data.records;
           /* if($scope.reportArray.success==1){
                $scope.isUpdateable=true;
                $scope.vendor.person_id=$scope.reportArray.person_id;
                $scope.vendorList.unshift($scope.vendor);
            }*/
        });



    };

});

