app.controller("bill2Ctrl", function ($scope,$http,$filter,$rootScope, $location,CalculatorService) {
    $scope.msg = "This is Bil2 sale controller";
    $scope.tab = 1;
    $scope.showQuality=true;
    $scope.productNull=false;
    $scope.showBill2No=false;
    $scope.addProductDiv=false;


    $scope.reportArray={
        bill_number:"00000"
    };

    $scope.selectProductQuality=["HM","22K","18K","Gini","Nil"];
    $scope.btnSubmitDisable=false;


    $scope.setTab = function(newTab){
        $scope.tab = newTab;
    };

    $scope.isSet = function(tabNum){
        return $scope.tab === tabNum;
    };



    $scope.bill2SaleMaster={};
    $scope.bill2SaleMaster.roundedOff=0;
    $scope.bill2SaleMaster.memo_no="XXXX";
    $scope.bill2SaleMaster.order_no="XXXX";
    $scope.bill2SaleMaster.order_date="";
    $scope.bill2SaleMaster.sales_date="";

    $scope.defaultBill2SaleDetails={
        quality:null,
        rate: 0,
        making_rate: 0,
        making_charge: 0,
        making_charge_type: 1,
        other_charge: 0,
        other_charge_for: "",
        amount: 0,
        totalAmount: 0

    };
    $scope.bill2SaleDetails=angular.copy($scope.defaultBill2SaleDetails);

    $scope.bill2CustomerList={};
    $scope.loadAllCustomers=function(){
        var request = $http({
            method: "post",
            url: site_url+"/customer/get_customers",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.bill2CustomerList=response.data.records;
            $scope.bill2CustomerListByKey=alasql("select * from ?",[$scope.bill2CustomerList]);
        });
    };//end of loadCustomer
    $scope.loadAllCustomers();

    $scope.bill2SearchCustomerByKey=function () {
        var searchKey = $scope.bill2SaleMaster.customerSearchKey;
        $scope.bill2CustomerListByKey=alasql("select * from ? where person_name like '"+searchKey + "%' or mobile_no like '"+ searchKey + "%' or phone_no like '" + searchKey + "%'",[$scope.bill2CustomerList]);
        $scope.bill2SaleMaster.customer=$scope.bill2CustomerListByKey[0];

    };

    $scope.productList={};
    $scope.loadAllProducts=function(){
        var request = $http({
            method: "post",
            url: site_url+"/sale/get_products",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.productList=response.data.records;
            $scope.productGroupList=alasql('SELECT distinct group_id,group_name,gst_rate  from ? ',[$scope.productList]);
            $scope.productQualityList=alasql('SELECT distinct quality  from ? ',[$scope.productList]);
        });
    };//end of loadProducts
    $scope.loadAllProducts();

    $scope.getProductByGroup=function () {
        $scope.productByGroup=alasql('SELECT distinct product_id,product_name,quality  from ? where group_id=?',[$scope.productList,$scope.bill2SaleDetails.productGroup.group_id]);
    };

    $scope.setAmount=function () {
            $scope.bill2SaleDetails.amount=$rootScope.roundNumber($scope.bill2SaleDetails.net_weight*$scope.bill2SaleDetails.rate,2);

    };

    $scope.setProductQuality=function(){
      if($scope.bill2SaleDetails.quality==null){

          $scope.bill2SaleDetails.quality=$scope.bill2SaleDetails.product.quality;
          console.log(quality);
      }
    };

    $scope.getMakingCharge=function () {
        if($scope.bill2SaleDetails.making_charge_type==1){
            $scope.bill2SaleDetails.making_charge=$rootScope.roundNumber($scope.bill2SaleDetails.net_weight*$scope.bill2SaleDetails.making_rate,2);
        }else{
            $scope.bill2SaleDetails.making_charge=$rootScope.roundNumber($scope.bill2SaleDetails.making_rate,2);
        }
    };

    $scope.bill2SaleDetailsList=[];
    $scope.addSaleDetailsData=function(sale){
        $scope.showNotification=false;
        $scope.productNull=false;
        var test=0;
        if($scope.bill2SaleDetails.product==null){
            $scope.productNull=true;
            return;
        }
        angular.forEach($scope.bill2SaleDetailsList, function(value, key) {

            if(angular.equals(value,sale))
                test++;

        });
        if(test==0){

            //if no quality is selected by user then default quality will be the quality
            sale.quality=sale.quality;
            var tempSale=angular.copy(sale);
            var total=0;
            $scope.bill2SaleDetailsList.unshift(tempSale);
            $scope.bill2SaleDetails=angular.copy($scope.defaultBill2SaleDetails);
        }else{
            $scope.showNotification=true;

        }

    };


   $scope.$watch("[bill2SaleDetails.amount,bill2SaleDetails.making_charge,bill2SaleDetails.other_charge,bill2SaleDetails.productGroup]", function(newValue, oldValue){
        if(newValue != oldValue){
            var totalAmount=0;
            totalAmount+=$rootScope.roundNumber((newValue[0])+parseFloat(newValue[1])+parseFloat(newValue[2]),2);
            $scope.bill2SaleDetails.totalAmount=$rootScope.roundNumber(totalAmount,2);
        }
    });

   $scope.$watchCollection("bill2SaleDetailsList", function(newValue, oldValue){
        if(newValue != oldValue){
            $scope.bill2SaleTableFooter=alasql('SELECT sum(cast(quantity as int)) as totalQuantity,sum(other_charge) as totalOtherCharge,sum(making_charge) as totalMakingCharge,sum(totalAmount) as totalSaleAmount  from ? ',[newValue]);
            var totalSale=$scope.bill2SaleTableFooter[0].totalSaleAmount;
            var roundDecimal=$rootScope.roundNumber(totalSale-parseInt(totalSale),2);
            if(roundDecimal==0){
                $scope.bill2SaleMaster.roundedOff=0;
            }else if(roundDecimal>0.49) {
                $scope.bill2SaleMaster.roundedOff = $rootScope.roundNumber(1-roundDecimal,2);
            }else{
                $scope.bill2SaleMaster.roundedOff = $rootScope.roundNumber(0-roundDecimal,2);
            }
            $scope.bill2SaleMaster.grandTotal=$scope.bill2SaleTableFooter[0].totalSaleAmount+$scope.bill2SaleMaster.roundedOff;//get bill_amount using roundoff

            //prepated data to save in table purchase details
            $scope.bill2SaleDetailToSave=alasql('SELECT ' +
                'product->product_id as product_id  ' +
                ',quality as product_quality  ' +
                ',quantity  ' +
                ',gross_weight  ' +
                ',net_weight  ' +
                ',rate  ' +
                ',making_charge_type '+
                ',making_rate '+
                ',other_charge '+
                ',other_charge_for '+
                'from ? ',[newValue]);


        }
    });

    $scope.removeRow=function(index){
        $scope.bill2SaleDetailsList.splice(index, 1);
    };

   //testing
    $scope.number=6;
    $scope.doSquare = function() {
        $scope.answer = CalculatorService.square($scope.number);
    };





    //save sale_details

    $scope.saveBill2SaleDetails=function(bill2SaleMaster,bill2SaleDetailToSave){
        var sm={};
        sm.person_id=bill2SaleMaster.customer.person_id;
        sm.memo_no=bill2SaleMaster.memo_no;
        sm.order_no=bill2SaleMaster.order_no;
        sm.roundedOff=bill2SaleMaster.roundedOff;
        sm.order_date=bill2SaleMaster.order_date;
        sm.sales_date=bill2SaleMaster.sales_date;
       // sm.order_date = $filter('date')(sm.order_date, 'yyyy-MM-dd');
       // sm.sales_date = $filter('date')(sm.sales_date, 'yyyy-MM-dd');
        //console.log(sm);
         $scope.sdl=angular.copy($scope.bill2SaleDetailToSave);
         var request = $http({
             method: "post",
             url: site_url+"/sale/save_new_bill2_sale",
             data: {
                 sale_master: sm,
                 sale_details_list: $scope.sdl
             }
             ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
         }).then(function(response){
             $scope.btnSubmitDisable=true;
             $scope.reportArray=response.data.records;
             console.log($scope.reportArray);
             if($scope.reportArray.success==1){
                 $scope.showBill2No=true;
                 $scope.bill2SaleMaster.bill_id=$scope.reportArray.bill_number;
                 //$scope.customerForm.$setPristine();
                 $scope.bill2CustomerList.unshift($scope.bill2SaleMaster,$scope.bill2SaleDetails);

             }
         });
    };

    $scope.$watch("reportArray", function(newValue, oldValue){

        if(newValue != oldValue){
            $scope.showBillByBillId(newValue.bill_number);
        }
    });
    $scope.loadAllSales=function(){
        var request = $http({
            method: "post",
            url: site_url+"/sale/get_all_sales_from_bill2",
            data: {

            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allSaleListFromBill2=response.data.records;
        });
    };
    //loading sale bills
    $scope.loadAllSales();


    $scope.showBillByBillId=function (billNo) {
        var request = $http({
            method: "post",
            url: site_url+"/sale/get_bill2_master_by_bill_id",
            data: {
                bill_number:billNo
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
           $scope.bill2Master=response.data.records;
           console.log($scope.bill2Master);
        });


        var request = $http({
            method: "post",
            url: site_url+"/sale/get_bill2_details_by_bill_id",
            data: {
                bill_number:billNo
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.bill2Details=response.data.records;
        });


    };
    // to call function from sale list
    $scope.showSaleBill2=function (sale) {
        $scope.tab=3;
        $scope.showBillByBillId(sale.bill2_number);

    }
    $scope.$watch("bill2Details", function(newValue, oldValue){

        if(newValue != oldValue){
            var result=alasql('SELECT sum(quantity) as totalQuantity,sum(sale_value) as totalSaleValue,sum(making_charge) as totalMakingCharge,sum(other_charge) as totalOtherCharge,sum(total_amount) as grandTotalAmount  from ? ',[newValue]);
            $scope.showTableFooter=result[0];
            $scope.showTableFooter.finalBillTotal=$scope.showTableFooter.grandTotalAmount+$scope.billMaster.roundedOff;
        }
    });

    $scope.dd = new Date().getDate();
    $scope.mm = new Date().getMonth()+1;
    $scope.yy = new Date().getFullYear();
    $scope.day= ($scope.dd<10)? '0'+$scope.dd : $scope.dd;
    $scope.month= ($scope.mm<10)? '0'+$scope.mm : $scope.mm;
    $scope.bill2SaleMaster.order_date=($scope.day+"/"+$scope.month+"/"+$scope.yy);
    $scope.bill2SaleMaster.sales_date=($scope.day+"/"+$scope.month+"/"+$scope.yy);

    $scope.newSaleForBill12=function () {
        $scope.btnSubmitDisable=false;
        $scope.showBill2No=false;
        $scope.bill2SaleDetails=angular.copy($scope.defaultBill2SaleDetails);
        $scope.bill2SaleDetailsList=[];
    };
    $scope.addProductFromSale=function(){
        $scope.addProductDiv=true;
        $scope.newProduct.product_name="";
        $scope.newProduct.group_name=$scope.bill2SaleDetails.productGroup;
        $scope.newProduct.quality="";
    };
    $scope.minimizeAddProductDiv=function(){
        $scope.addProductDiv=false;
        $scope.newProduct.product_name="";
        $scope.newProduct.group_name="";
        $scope.newProduct.quality="";
    };

    $scope.saveNewProduct=function(newProduct){
        $scope.master=angular.copy($scope.newProduct);
        var request = $http({
            method: "post",
            url: site_url+"/product/insert_product",
            data: {
                product: $scope.master
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.reportArray=response.data.records;
            if($scope.reportArray.success==1){
                var tempProduct={};
                tempProduct.product_id=$scope.reportArray.product_id;
                tempProduct.product_name=$scope.reportArray.product_name;
                tempProduct.quality=$scope.reportArray.quality;
                console.log(tempProduct);
                $scope.addProductDiv=false;
                $scope.bill2SaleDetails.product=$scope.reportArray.product_name;
                $scope.productList.push($scope.product);
                $scope.productByGroup .push(tempProduct);
            }else {
                $scope.errorNotification=true;
            }
        });
    };






});//end of Controller

