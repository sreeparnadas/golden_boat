app.controller("saleCtrl", function ($scope,$http,$filter,$rootScope, $location,CalculatorService,$compile,$modal,$log) {
    $scope.msg = "This is sale controller";
    $scope.tab = 1;
    $scope.showQuality=true;
    $scope.productNull=false;
    $scope.showBillNo=false;
    $scope.addProductDiv=false;

    $scope.changeDateFormat=function(userDate){
        return moment(userDate).format('YYYY-MM-DD');
    };

    $scope.reportArray={
        bill_number:"00000"
    };
    $scope.btnSubmitDisable=false;



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


    $scope.changeDateFormat=function(userDate){
        return moment(userDate).format('YYYY-MM-DD');
    };

    $scope.saleMaster={
        memo_no: ''
        ,order_no: ''
        ,transaction_mode: "Cash"
        ,card_number: ''
        ,order_date: ''
        ,roundedOff: 0
    };
    // $scope.saleMaster.roundedOff=0;
    // $scope.saleMaster.memo_no="XXXX";
    // $scope.saleMaster.order_no="XXXX";
    // $scope.saleMaster.transaction_mode="Cash";
    // $scope.saleMaster.card_number="";
    // $scope.saleMaster.order_date="";
    $scope.defaultSaleDetails={
        quality:null,
        rate: 0,
        sgstFactor: 0,
        cgstFactor: 0,
        igstFactor: 0,
        taxableAmount: 0,
        making_rate: 0,
        making_charge: 0,
        making_charge_type: 1,
        other_charge: 0,
        other_charge_for: "",
        amount: 0,
        sgst: 0,
        cgst: 0,
        igst: 0,
        totalAmount: 0
    };

    $scope.defaultBillDetails={
        quality:null,
        rate: 0,
        sgstFactor: 0,
        cgstFactor: 0,
        igstFactor: 0,
        taxableAmount: 0,
        making_rate: 0,
        making_charge: 0,
        making_charge_type: 1,
        other_charge: 0,
        other_charge_for: "",
        amount: 0,
        sgst_value: 0,
        cgst_value: 0,
        igst_value: 0,
        sgst: 0,
        cgst: 0,
        igst: 0,
        totalAmount: 0
    };

    $scope.saleDetails = angular.copy($scope.defaultSaleDetails);

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
        var searchKey = $scope.saleMaster.customerSearchKey;
        $scope.customerListByKey=alasql("select * from ? where person_name like '"+searchKey+"%' or mobile_no like '"+ searchKey + "%' or phone_no like '" + searchKey + "%'",[$scope.customerList]);
        $scope.saleMaster.customer=$scope.customerListByKey[0];
    };



    $scope.setGstFactor=function () {
        if($scope.saleMaster.customer){
            var stateId=$scope.saleMaster.customer.state_id;
            if(stateId==19){
                $scope.saleDetails.cgstFactor=0.5;
                $scope.saleDetails.sgstFactor=0.5;
                $scope.saleDetails.igstFactor=0.0;
            }else{
                $scope.saleDetails.cgstFactor=0.0;
                $scope.saleDetails.sgstFactor=0.0;
                $scope.saleDetails.igstFactor=1;
            }
        }
    };


    $scope.productList={};
    $scope.selectProductQuality=["HM","22K","18K","Gini","Nil"];
    $scope.transactionMode=["Cash","Card","Both"];
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
        $scope.productByGroup=alasql('SELECT distinct product_id,product_name,quality  from ? where group_id=?',[$scope.productList,$scope.saleDetails.productGroup.group_id]);
    };



    $scope.setAmount=function () {
            $scope.saleDetails.amount=$rootScope.roundNumber($scope.saleDetails.net_weight*$scope.saleDetails.rate,2);

    };

    $scope.setProductQuality=function(){
      if($scope.saleDetails.quality==null){

          $scope.saleDetails.quality=$scope.saleDetails.product.quality;
      }
    };

    $scope.getMakingCharge=function () {
        if($scope.saleDetails.making_charge_type==1){
           // $scope.saleDetails.making_charge=parseFloat(($scope.saleDetails.net_weight*$scope.saleDetails.making_rate).toFixed(2));
            $scope.saleDetails.making_charge=$rootScope.roundNumber($scope.saleDetails.net_weight*$scope.saleDetails.making_rate,2);
        }else{
            $scope.saleDetails.making_charge=$rootScope.roundNumber($scope.saleDetails.making_rate,2);
        }
    };

    $scope.saleDetailsList=[];
    $scope.addSaleDetailsData=function(sale){
        $scope.showNotification=false;
        $scope.productNull=false;
        var test=0;
        if($scope.saleDetails.product==null){
            $scope.productNull=true;
            return;
        }
        angular.forEach($scope.saleDetailsList, function(value, key) {

            if(angular.equals(value,sale))
                test++;

        });
        if(test==0){

            //if no quality is selected by user then default quality will be the quality
            sale.quality=sale.quality;
            var tempSale=angular.copy(sale);
            var total=0;
            $scope.saleDetailsList.unshift(tempSale);
            $scope.saleDetails=angular.copy($scope.defaultSaleDetails);
            $scope.setGstFactor();
        }else{
            $scope.showNotification=true;

        }

    };

    //Get sgst cgst igst rate
    $scope.setGstRate=function(){
        var gst=$scope.saleDetails.productGroup.gst_rate;
        $scope.saleDetails.cgstRate=(gst * $scope.saleDetails.cgstFactor)/100;
        $scope.saleDetails.sgstRate=(gst * $scope.saleDetails.sgstFactor)/100;
        $scope.saleDetails.igstRate=(gst * $scope.saleDetails.igstFactor)/100;
    };


   $scope.$watch("[saleDetails.amount,saleDetails.making_charge,saleDetails.other_charge,saleDetails.productGroup]", function(newValue, oldValue){
        if(newValue != oldValue){
            var taxableAmount=0;
            taxableAmount+=$rootScope.roundNumber((newValue[0])+parseFloat(newValue[1])+parseFloat(newValue[2]),2);
            $scope.saleDetails.taxableAmount=$rootScope.roundNumber(taxableAmount,2);
            $scope.saleDetails.sgst=$rootScope.roundNumber($scope.saleDetails.taxableAmount * $scope.saleDetails.sgstRate,2);
            $scope.saleDetails.cgst=$rootScope.roundNumber($scope.saleDetails.taxableAmount * $scope.saleDetails.cgstRate,2);
            $scope.saleDetails.igst=$rootScope.roundNumber($scope.saleDetails.taxableAmount * $scope.saleDetails.igstRate,2);
            $scope.saleDetails.totalAmount=$rootScope.roundNumber(($scope.saleDetails.taxableAmount+$scope.saleDetails.sgst+$scope.saleDetails.cgst+$scope.saleDetails.igst),2);
        }
    });

   $scope.$watchCollection("saleDetailsList", function(newValue, oldValue){
        if(newValue != oldValue){
            $scope.saleTableFooter=alasql('SELECT sum(other_charge) as totalOtherCharge,sum(making_charge) as totalMakingCharge, sum(sgst)as totalSgst,sum(cgst) as totalCgst,sum(igst) as totalIgst,sum(totalAmount) as totalSaleAmount  from ? ',[newValue]);
            //$scope.saleTableFooter[0].totalSaleAmount=$rootScope.roundNumber(($scope.saleTableFooter[0].totalSaleAmount),2);
            var totalSale=$scope.saleTableFooter[0].totalSaleAmount;
            var roundDecimal=$rootScope.roundNumber(totalSale-parseInt(totalSale),2);
            if(roundDecimal==0){
                $scope.saleMaster.roundedOff=0;
            }else if(roundDecimal>0.49) {
                $scope.saleMaster.roundedOff = $rootScope.roundNumber(1-roundDecimal,2);
            }else{
                $scope.saleMaster.roundedOff = $rootScope.roundNumber(0-roundDecimal,2);
            }
            $scope.saleMaster.grandTotal=$scope.saleTableFooter[0].totalSaleAmount+$scope.saleMaster.roundedOff;//get bill_amount using roundoff

            //prepated data to save in table purchase details
            $scope.saleDetailToSave=alasql('SELECT ' +
                'product->product_id as product_id  ' +
                ',productGroup->group_id as group_id  ' +
                ',quality as product_quality  ' +
                ',quantity  ' +
                ',gross_weight  ' +
                ',net_weight  ' +
                ',rate  ' +
                ',making_charge_type '+
                ',making_rate '+
                ',other_charge '+
                ',other_charge_for '+
                ',cgstRate '+
                ',sgstRate '+
                ',igstRate '+
                'from ? ',[newValue]);


        }
    });

    $scope.removeRow=function(index){
        $scope.saleDetailsList.splice(index, 1);
    };

   //testing
    $scope.number=6;
    $scope.doSquare = function() {
        $scope.answer = CalculatorService.square($scope.number);
    };

    //save sale_details

    $scope.saveSaleDetails=function(saleMaster,saleDetailToSave){
        var sm={};
        sm.person_id=saleMaster.customer.person_id;
        sm.memo_no=saleMaster.memo_no;
        sm.order_no=saleMaster.order_no;
        sm.transaction_mode=saleMaster.transaction_mode;
        sm.card_number=saleMaster.card_number;
        sm.order_date=saleMaster.order_date;
        sm.sale_date=saleMaster.sale_date;
        sm.roundedOff=saleMaster.roundedOff;
         $scope.sdl=angular.copy($scope.saleDetailToSave);
         var request = $http({
             method: "post",
             url: site_url+"/sale/save_new_sale",
             data: {
                 sale_master: sm,
                 sale_details_list: $scope.sdl
             }
             ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
         }).then(function(response){
             $scope.btnSubmitDisable=true;
             $scope.reportArray=response.data.records;
             if($scope.reportArray.success==1){
                 $scope.showBillNo=true;
                 $scope.saleMaster.bill_id=$scope.reportArray.bill_number;
                 var tempSaleDetail={
                     // "bill_date": "02/05/2018  19:44:11",
                     // "bill_number": $sc,
                     // "customer_id": "C-000661718",
                     // "total_bill_amount": 450,
                     // "customer_name": "ja khushi"
                 };
                 // tempSaleDetail.bill_date='Today';
                 tempSaleDetail.sale_date=$scope.changeDateFormat($scope.saleMaster.sale_date);
                 tempSaleDetail.bill_number=$scope.saleMaster.bill_id;
                 tempSaleDetail.customer_id=$scope.saleMaster.customer.person_id;
                 tempSaleDetail.total_bill_amount=$scope.saleMaster.grandTotal;
                 tempSaleDetail.customer_name=$scope.saleMaster.customer.person_name;
                 $scope.allSaleList.unshift(tempSaleDetail);
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
            url: site_url+"/sale/get_all_sales",
            data: {

            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allSaleList=response.data.records;
        });
    };
    //loading sale bills
    $scope.loadAllSales();


    $scope.showBillByBillId=function (billNo) {
        var request = $http({
            method: "post",
            url: site_url+"/sale/get_bill_master_by_bill_id",
            data: {
                bill_number:billNo
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
           $scope.billMaster = response.data.records;
        });
        var request = $http({
            method: "post",
            url: site_url+"/sale/get_bill_details_by_bill_id",
            data: {
                bill_number:billNo
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.billDetails = response.data.records;
            if($scope.tab == 4){
                $scope.billDetailsEditProductObj = angular.copy($scope.defaultBillDetails);
                $scope.setGstFactorForEditBill();
            }
        });

        var request = $http({
            method: "post",
            url: site_url+"/sale/get_other_charges_particulars_by_bill_number",
            data: {
                bill_number:billNo
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.otherChargeDetailsList=response.data.records;
        });


    };
    // to call function from sale list
    $scope.showSaleBill=function (sale) {
        $scope.tab=3;
        $scope.showBillByBillId(sale.bill_number);

    }
    $scope.sendToEditBillPage=function (sale) {
        $scope.tab=4;
        $scope.showBillByBillId(sale.bill_number);
    }

    $scope.$watch("billDetails", function(newValue, oldValue){
        if(newValue != oldValue){
            var result=alasql('SELECT sum(quantity) as totalQuantity,sum(sale_value) as totalSaleValue,sum(taxable_amount) as totalTaxableAmount,sum(making_charge) as totalMakingCharge,sum(other_charge) as totalOtherCharge,round(sum(sgst_value),2) as totalSgstValue,round(sum(cgst_value),2) as totalCgstValue,round(sum(igst_value),2) as totalIgstValue,sum(total_amount) as grandTotalAmount  from ? ',[newValue]);
            $scope.showTableFooter=result[0];
            $scope.showTableFooter.finalBillTotal=$scope.showTableFooter.grandTotalAmount+$scope.billMaster.roundedOff;
            var tempGstTable=alasql('SELECT hsn_code,gst_rate,max(cgst) as cgst_rate, max(sgst) as sgst_rate, max(igst) as igst_rate,sum(sgst_value) as sum_of_sgst,sum(cgst_value) as sum_of_cgst,sum(igst_value)as sum_of_igst,sum(taxable_amount) as sum_of_taxable_amount from ? group by hsn_code,gst_rate',[newValue]);
            $scope.gstTable=tempGstTable;
            var temp=alasql('SELECT SUM(sum_of_sgst) AS total_sgst,SUM(sum_of_cgst) AS total_cgst,SUM(sum_of_igst) AS total_igst from ?',[tempGstTable]);

            $scope.gstTableFooter=temp[0];

            $scope.rateWiseGST=alasql('SELECT gst_rate,sum(sgst_value+cgst_value+igst_value)as sum_of_gst from ? group by gst_rate',[newValue]);
        }
    });

    $scope.newSaleForBill1=function () {
        $scope.btnSubmitDisable=false;
        $scope.showBillNo=false;
        $scope.saleMaster.order_date="";
        $scope.saleMaster.transaction_mode=$scope.transactionMode[0];
        $scope.saleMaster.card_number="XXXX";
        $scope.saleDetails=angular.copy($scope.defaultSaleDetails);
        $scope.saleDetailsList=[];
        $scope.setGstFactor();

    };

    $scope.addProductFromSale=function(){
        $scope.addProductDiv=true;
        $scope.newProduct.product_name="";
        $scope.newProduct.group_name=$scope.saleDetails.productGroup;
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
                $scope.addProductDiv=false;
                $scope.saleDetails.product=$scope.reportArray.product_name;
                $scope.productList.push($scope.product);
                $scope.productByGroup .push(tempProduct);
            }else {
                $scope.errorNotification=true;
            }
        });
    };



                    // =====    bill edit section start     ======

    $scope.billDetailsEditProductObj = {};
    $scope.productListForEditBill = [];
    $scope.getProductByGroupForBillEdit=function () {
        $scope.productListForEditBill=alasql('SELECT distinct product_id,product_name,quality  from ? where group_id=?',[$scope.productList,$scope.billDetailsEditProductObj.productGroup.group_id]);
    };

    $scope.setProductQualityForBillEdit=function(){
        if($scope.billDetailsEditProductObj.quality==null){
            $scope.billDetailsEditProductObj.quality=$scope.billDetailsEditProductObj.product.quality;
        }
    };

    $scope.setGstFactorForEditBill=function () {
        if($scope.billMaster){
            var stateId=$scope.billMaster.state_id;
            if(stateId==19){
                $scope.billDetailsEditProductObj.cgstFactor=0.5;
                $scope.billDetailsEditProductObj.sgstFactor=0.5;
                $scope.billDetailsEditProductObj.igstFactor=0.0;
            }else{
                $scope.billDetailsEditProductObj.cgstFactor=0.0;
                $scope.billDetailsEditProductObj.sgstFactor=0.0;
                $scope.billDetailsEditProductObj.igstFactor=1;
            }
        }
    };

    //Get sgst cgst igst rate
    $scope.setGstRateForEditBill=function(){
        var gst=$scope.billDetailsEditProductObj.productGroup.gst_rate;
        $scope.billDetailsEditProductObj.cgst=(gst * $scope.billDetailsEditProductObj.cgstFactor)/100;
        $scope.billDetailsEditProductObj.sgst=(gst * $scope.billDetailsEditProductObj.sgstFactor)/100;
        $scope.billDetailsEditProductObj.igst=(gst * $scope.billDetailsEditProductObj.igstFactor)/100;
    };

    $scope.getMakingChargeForEditBill=function () {
        if($scope.billDetailsEditProductObj.making_charge_type==1){
            $scope.billDetailsEditProductObj.making_charge=$rootScope.roundNumber($scope.billDetailsEditProductObj.net_weight*$scope.billDetailsEditProductObj.making_rate,2);
        }else{
            $scope.billDetailsEditProductObj.making_charge=$rootScope.roundNumber($scope.billDetailsEditProductObj.making_rate,2);
        }
    };

    $scope.setAmountForEditBill=function () {
        $scope.billDetailsEditProductObj.amount=$rootScope.roundNumber($scope.billDetailsEditProductObj.net_weight*$scope.billDetailsEditProductObj.rate,2);
    };


    $scope.newProductInEditBill = {};
    $scope.addProductDivInEditBill=false;
    $scope.addProductInEditBill=function(){
        $scope.addProductDivInEditBill=true;
        $scope.newProductInEditBill.product_name="";
        $scope.newProductInEditBill.group_name=$scope.billDetailsEditProductObj.productGroup;
        $scope.newProductInEditBill.quality="";
    };


    $scope.$watch("[billDetailsEditProductObj.amount,billDetailsEditProductObj.making_charge,billDetailsEditProductObj.other_charge,billDetailsEditProductObj.productGroup]", function(newValue, oldValue){
        if(newValue != oldValue){
            var taxableAmount=0;
            taxableAmount+=$rootScope.roundNumber((newValue[0])+parseFloat(newValue[1])+parseFloat(newValue[2]),2);
            $scope.billDetailsEditProductObj.taxableAmount=$rootScope.roundNumber(taxableAmount,2);
            $scope.billDetailsEditProductObj.sgst_value=$rootScope.roundNumber($scope.billDetailsEditProductObj.taxableAmount * $scope.billDetailsEditProductObj.sgst,2);
            $scope.billDetailsEditProductObj.cgst_value=$rootScope.roundNumber($scope.billDetailsEditProductObj.taxableAmount * $scope.billDetailsEditProductObj.cgst,2);
            $scope.billDetailsEditProductObj.igst_value=$rootScope.roundNumber($scope.billDetailsEditProductObj.taxableAmount * $scope.billDetailsEditProductObj.igst,2);
            $scope.billDetailsEditProductObj.totalAmount=$rootScope.roundNumber(($scope.billDetailsEditProductObj.taxableAmount+$scope.billDetailsEditProductObj.sgst_value+$scope.billDetailsEditProductObj.cgst_value+$scope.billDetailsEditProductObj.igst_value),2);
        }
    });


    $scope.calculateTotalFromEditBillTable = function(){
        var result=alasql('SELECT sum(quantity) as totalQuantity,sum(sale_value) as totalSaleValue,sum(taxable_amount) as totalTaxableAmount,sum(making_charge) as totalMakingCharge,sum(other_charge) as totalOtherCharge,round(sum(sgst_value),2) as totalSgstValue,round(sum(cgst_value),2) as totalCgstValue,round(sum(igst_value),2) as totalIgstValue,sum(total_amount) as grandTotalAmount  from ? ',[$scope.billDetails]);
        $scope.showTableFooter=result[0];
        var roundDecimal=$rootScope.roundNumber($scope.showTableFooter.grandTotalAmount-parseInt($scope.showTableFooter.grandTotalAmount),2);

        if(roundDecimal==0){
            $scope.billMaster.roundedOff=0;
        }else if(roundDecimal>0.49) {
            $scope.billMaster.roundedOff = $rootScope.roundNumber(1-roundDecimal,2);
        }else{
            $scope.billMaster.roundedOff = $rootScope.roundNumber(0-roundDecimal,2);
        }

        $scope.showTableFooter.finalBillTotal=$scope.showTableFooter.grandTotalAmount+$scope.billMaster.roundedOff;
        var tempGstTable=alasql('SELECT hsn_code,gst_rate,max(cgst) as cgst_rate, max(sgst) as sgst_rate, max(igst) as igst_rate,sum(sgst_value) as sum_of_sgst,sum(cgst_value) as sum_of_cgst,sum(igst_value)as sum_of_igst,sum(taxable_amount) as sum_of_taxable_amount from ? group by hsn_code,gst_rate',[$scope.billDetails]);
        $scope.gstTable=tempGstTable;
        var temp=alasql('SELECT SUM(sum_of_sgst) AS total_sgst,SUM(sum_of_cgst) AS total_cgst,SUM(sum_of_igst) AS total_igst from ?',[tempGstTable]);

        $scope.gstTableFooter=temp[0];

        $scope.rateWiseGST=alasql('SELECT gst_rate,sum(sgst_value+cgst_value+igst_value)as sum_of_gst from ? group by gst_rate',[$scope.billDetails]);
    };



    isDuplicateDataInEditBill = false;
    $scope.changeProductInExistingBill=function(data){
        data.bill_number = $scope.billDetails[0].bill_number;
        data.product_group_id = data.productGroup.group_id;
        data.product_id = data.product.product_id;
        data.product_name = data.product.product_name;
        data.sale_value = data.amount;
        data.total_amount = data.totalAmount;
        data.product_quality = data.quality;

        var tempData = angular.copy(data)
        delete tempData.product;
        delete tempData.productGroup;
        delete tempData.amount;
        delete tempData.totalAmount;
        
        var test=0;
        angular.forEach($scope.billDetails, function(value, key) {
            if(angular.equals(value,tempData))
                test++;
        });

        if(test==0){
            //if no quality is selected by user then default quality will be the quality
            tempData.quality=data.product.quality;
            $scope.billDetails.unshift(tempData);
            $scope.calculateTotalFromEditBillTable();
            $scope.billDetailsEditProductObj = angular.copy($scope.defaultBillDetails);
            $scope.setGstFactorForEditBill();
        }else{
            $scope.isDuplicateDataInEditBill=true;
        }

    };

    $scope.removeRowFromEditBillTable=function(index){
        $scope.billDetails.splice(index, 1);
        $scope.calculateTotalFromEditBillTable();
    };

    $scope.populateBillEditDataToForm = function(index){
        let obj = $scope.billDetails[index];
        console.log(obj);
        let pGrpIdx =$scope.productGroupList .findIndex(k=>k.group_id==obj.product_group_id);
        $scope.billDetailsEditProductObj.productGroup = $scope.productGroupList[pGrpIdx];
        $scope.getProductByGroupForBillEdit();

        let pIdx =$scope.productListForEditBill .findIndex(k=>k.product_id==obj.product_id);
        $scope.billDetailsEditProductObj.product = $scope.productListForEditBill[pIdx];

        $scope.billDetailsEditProductObj.quality = obj.product_quality;
        $scope.billDetailsEditProductObj.rate = obj.rate;
        $scope.billDetailsEditProductObj.taxableAmount = '';
        $scope.billDetailsEditProductObj.making_rate = obj.making_rate;
        $scope.billDetailsEditProductObj.making_charge = obj.making_charge;
        $scope.billDetailsEditProductObj.making_charge_type = obj.making_charge_type;
        $scope.billDetailsEditProductObj.other_charge = obj.other_charge;
        $scope.billDetailsEditProductObj.other_charge_for = '';
        $scope.billDetailsEditProductObj.amount = obj.sale_value;
        $scope.billDetailsEditProductObj.sgst = obj.sgst;
        $scope.billDetailsEditProductObj.cgst = obj.cgst;
        $scope.billDetailsEditProductObj.igst = obj.igst;
        $scope.billDetailsEditProductObj.sgst_value = obj.sgst_value;
        $scope.billDetailsEditProductObj.cgst_value = obj.cgst_value;
        $scope.billDetailsEditProductObj.igst_value = obj.igst_value;
        $scope.billDetailsEditProductObj.totalAmount = obj.total_amount;
        $scope.billDetailsEditProductObj.quantity = obj.quantity;
        $scope.billDetailsEditProductObj.gross_weight = obj.gross_weight;
        $scope.billDetailsEditProductObj.net_weight = obj.net_weight;
        $scope.removeRowFromEditBillTable(index);
    };


    $scope.updateBill = function(billMasterData, billDetailsData){
        $scope.billMasterObj = {
            bill_number: billMasterData.bill_number,
            roundedOff: billMasterData.roundedOff
        }
        
        $scope.billDetailsToUpdate=alasql('SELECT ' +
            'product_id  ' +
            ',product_group_id  ' +
            ',product_quality  ' +
            ',quantity  ' +
            ',gross_weight  ' +
            ',net_weight  ' +
            ',rate  ' +
            ',making_charge_type '+
            ',making_rate '+
            ',other_charge '+
            ',cgst '+
            ',sgst '+
            ',igst '+
            'from ? ',[billDetailsData]);

            var request = $http({
                method: "post",
                url: site_url+"/sale/update_bill_one",
                data: {
                    bill_master: $scope.billMasterObj,
                    bill_details: $scope.billDetailsToUpdate
                }
                ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function(response){
                let result = response.data.records;
                console.log(result);
            })
    };

});//end of Controller