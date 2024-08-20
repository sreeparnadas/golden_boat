app.controller("productCtrl", function ($scope,$http,$timeout) {
    $scope.msg = "This is Product controller";
    $scope.tab = 1;
    $scope.successNotification=false;
    $scope.errorNotification=false;

    $scope.setTab = function(newTab){
        $scope.tab = newTab;
    };

    $scope.isSet = function(tabNum){
        return $scope.tab === tabNum;
    };

    $scope.showSuccess = function(){
        growl.success('This is success message.',{title: 'Success!'});
    }
    $scope.defaultProduct={
        product_id: "",
        product_name: "",
        group_name: "",
        quality: ""
    };

    $scope.product={};
    var request = $http({
        method: "post",
        url: site_url+"/product/get_product_groups",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){

        $scope.productGroup=response.data.records;
    });

    $scope.productQuality=["HM","22K","18K","Gini","Nil"];

    $scope.saveProduct=function(product){
        $scope.master=angular.copy($scope.product);
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
                $scope.successNotification=true;
                $scope.isUpdateable=true;
                $scope.product.product_id=$scope.reportArray.product_id;
                $scope.productForm.$setPristine();
                $scope.productList.unshift($scope.product);
            }else {
                $scope.errorNotification=true;
            }
        });
    };

    $scope.resetProduct=function(){
        $scope.successNotification=false;
        $scope.errorNotification=false;
        $scope.product=angular.copy($scope.defaultProduct);
        $scope.isUpdateable=false;
    };

    $scope.updateProductByProductId=function(product){
        $scope.master = angular.copy(product);
        var request = $http({
            method: "post",
            url: site_url+"/product/update_product_by_product_id",
            data: {
                product: $scope.master
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            console.log(data);
            $scope.reportArray=response.data.records;
            if($scope.reportArray.success==1){
                $scope.isUpdateable=true;
                $scope.productList[$scope.updateableProductIndex]=$scope.product;
                $scope.productForm.$setPristine();
            }

        });
    };

    $scope.updateProductFromTable = function(product) {
        $scope.product = angular.copy(product);
        var index=$scope.productList.indexOf(product);
        $scope.updateableProductIndex=index;
        $scope.tab=1;
        $scope.isUpdateable=true;
        $scope.productForm.$setPristine();
    };

    var request = $http({
        method: "post",
        url: site_url+"/product/get_all_products",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){

        $scope.productList=response.data.records;
    });



});

