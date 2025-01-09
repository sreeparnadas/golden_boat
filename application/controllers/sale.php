<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sale extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('person');
        $this -> load -> model('sale_model');
        $this -> is_logged_in();
    }
    function is_logged_in() {
		$is_logged_in = $this -> session -> userdata('is_logged_in');
		if (!isset($is_logged_in) || $is_logged_in != 1) {
			echo 'you have no permission to use developer area'. '<a href="">Login</a>';
			die();
		}
	}
    function get_products(){
        $result=$this->sale_model->select_inforce_products()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

    // bill1 functions and html forms code
    public function angular_view_sale(){
        ?>
                <style type="text/css">
                    .navbar-fixed-top {
                        border: none;
                        background: #ac2925;

                        margin-top: -20px;
                    }
                    .navbar-fixed-top a{
                        color: #a6e1ec;
                    }
                    #vendor-div{
                        margin-top: 10px;
                    }
                    h1{
                        color: blue;
                    }
                    #mySidenav a[ng-click]{
                        cursor: pointer;
                        position: absolute;
                        left: -20px;
                        transition: 0.3s;
                        padding: 15px;
                        width: 140px;
                        text-decoration: none;
                        font-size: 15px;
                        color: white;
                        border-radius: 0 5px 5px 0;
                        background-color: #ac2925;
                    }

                    #mySidenav a[ng-click]:hover {
                        left: 0;
                    }

                    #mySidenav a:hover {
                        left: 0;
                    }
                    #new-vendor {
                        top: 20px;
                        background-color: #4CAF50;
                    }

                    #update-vendor {
                        top: 78px;
                        background-color: #2196F3;
                    }

                    #show-vendor{
                        top: 136px;
                        background-color: #f44336;
                    }
                    #main-working-div h1{
                        color: darkblue;
                    }
                    #vendor-form input{
                        border-radius: 5px;
                    }
                    #vendorForm{
                        margin-top: 10px;
                     }
                     input.ng-invalid {
                        background-color: pink;
                    }
                    #customer-details{
                        list-style-type: none;
                    }
                    growl-notification{
                        color: red;
                    }
                    .td-input{
                        padding: 2px;
                        margin-left: 0px;
                        margin-right: 0px;
                    }
                    table tr td{
                        padding: 2px !important;
                        margin: 0 !important;
                        margin-left: 2px;
                    }
                    /*#sale-details-list-table tbody tr td{*/
                        /*border-right: 1px solid #8ba8af !important;*/
                    /*}*/
                    /*#sale-details-list-table thead tr th{*/
                        /*border-right: 1px solid #8ba8af !important;*/
                    /*}*/
                    /*#sale-details-list-table tfoot thead > tr > td{*/
                        /*border-top: 1px solid #0D8BBD !important;*/
                    /*}*/
                    #show-bill-details-table > tfoot > tr > td{
                        /*border-top: none !important;*/

                    }
                    #add-product-div{
                        margin-top: 15px;
                        margin-left: 30px;
                        padding-left: 250px;
                    }
                    #product-outer-div{
                        background-color: lightgray;
                    }
                    #upper-right{
                        padding-bottom: 8px;
                        background-color: #EAF2F5;
                        height: 140px;
                        padding-top: 0px;
                        margin-left: 0px
                    }
                    .highlighted {
                        background-color: #ffff99; /* Example highlight color */
                    }
                    .hover-row {
                        background-color: #f0f0f0; /* Example: Light gray */
                    }




    </style>
    <div class="container-fluid">
        <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#" role="tab" ng-style="tab==1 && selectedTab" ng-click="setTab(1)"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;Start Sale</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-style="tab==2 && selectedTab" ng-click="setTab(2)"><span class="glyphicon glyphicon-list-alt"></span>Show Sale Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-style="tab==3 && selectedTab" ng-click=""><span class="glyphicon glyphicon-file"></span>Show Bill</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-style="tab==4 && selectedTab" ng-click=""><span class="glyphicon glyphicon-edit"></span>Edit Bill</a>
                    </li>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="my-tab-1">
                            <form name="saleForm" class="form-horizontal" id="saleForm">
                                <div class="row">
                                    <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12" id="customer-div">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="background-color: #e3d2d2;height: 140px" id="upper-left">
                                            <div class="form-group">
                                                <label  class="control-label  col-lg-2 col-md-2 col-sm-2 col-xs-2 ">Customer</label>
                                                <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                                    <input type="text" class="textinput textInput form-control" ng-change="searchCustomerByKey();setGstFactor()" ng-model="saleMaster.customerSearchKey"/>
                                                </div>
                                                <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                    <select
                                                        ng-change="setGstFactor()"
                                                        ng-model="saleMaster.customer"
                                                        ng-options="customer as customer.person_name for customer in customerListByKey" >
                                                    </select>
                                                </div>
                                            </div>

                                            <ul id="customer-details" ng-show="saleMaster.customer" style="margin-top: 0px;padding-top: 0px;padding-left: 0px;font-size: 12px;line-height:17px">
                                                <li ng-show="saleMaster.customer.billing_name.length>0"><span class="glyphicon glyphicon-user"></span> {{saleMaster.customer.billing_name}}</li>
                                                <li>
                                                    <span class="glyphicon glyphicon-earphone"></span> {{saleMaster.customer.mobile_no || 'empty'}}
                                                </li>
                                                <li ng-show="saleMaster.customer.address1.length>0"><i class="fa fa-map-marker" aria-hidden="true"></i>
                                                {{saleMaster.customer.address1}}</li>
                                                <li ng-show="saleMaster.customer.city.length>0 || saleMaster.customer.post_office.length>0">
                                                    {{saleMaster.customer.city}}, {{saleMaster.customer.post_office}}
                                                </li>
                                                <li ng-show="saleMaster.customer.district_name.length>0 || saleMaster.customer.state_name.length>0">
                                                    Dist. - {{saleMaster.customer.district_name}}, {{saleMaster.customer.state_name}}
                                                </li>
                                                <li ng-show="saleMaster.customer.gst_number.length>0">GST: {{saleMaster.customer.gst_number}}</li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" id="upper-right">

                                            <div class="form-group">

                                                <label  class="control-label col-lg-2 col-md-2 col-sm-2 col-xs-2">Memo No&nbsp;<span class="text-danger">*</span></label>
                                                <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                                    <input type="text" class="textinput textInput form-control capitalizeWord" ng-model="saleMaster.memo_no" />
                                                </div>
                                                <label  class="control-label col-lg-2 col-md-2 col-sm-2 col-xs-2">Order Number&nbsp;<span class="text-danger">*</span></label>
                                                <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                                    <input type="text" class="textinput textInput form-control capitalizeWord" ng-model="saleMaster.order_no" />
                                                </div>
                                                <label  class="control-label col-lg-2 col-md-2 col-sm-2 col-xs-2">Order Date&nbsp;<span class="text-danger">*</span></label>
                                                <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                                    <input type="text" class="textinput textInput form-control capitalizeWord"   ng-pattern="/\d\d/\d\d/\d\d\d\d/" ng-model="saleMaster.order_date" placeholder="DD/MM/YYYY" required/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label  class="control-label col-lg-1 col-md-1 col-sm-1 col-xs-1">Sl.dt</label>
                                                <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                                    <input type="date" class="form-control" ng-model="saleMaster.sale_date" ng-change="saleMaster.sale_date=changeDateFormat(saleMaster.sale_date)"  placeholder="DD/MM/YYYY" required/>
                                                </div>
                                                <label  class="control-label col-lg-1 col-md-1 col-sm-1 col-xs-1">By</label>
                                                <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                                    <select  name="transactionMode"
                                                            ng-model="saleMaster.transaction_mode"
                                                            ng-options="mode for mode in transactionMode">
                                                    </select>
                                                </div>
                                                <label  class="control-label col-lg-2 col-md-2 col-sm-2 col-xs-2" ng-hide="saleMaster.transaction_mode==='Cash'">Details</label>
                                                <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3" ng-hide="saleMaster.transaction_mode==='Cash'">
                                                    <input  type="text" class="form-control col-lg-12 col-md-12 col-sm-12 col-xs-12" id="transaction details" ng-model="saleMaster.card_number">
                                                </div>
                                            </div>

                                            <div class="form-group" ng-show="showBillNo">
                                                <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Bill Number:</label>
                                                <div class="controls col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                    <input type="text" class="form-control" ng-model="saleMaster.bill_id" disabled/>
                                                </div>
                                                <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                    <a href="#" class="btn btn-default" ng-click="setTab(3)">
                                                        <span class="glyphicon glyphicon-print"></span> Show bill
                                                    </a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12" ng-show="addProductDiv" id="product-outer-div">
                                    <div class="form-group" id="add-product-div">
                                        <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                            <input type="text" class="textinput textInput form-control" ng-model="newProduct.product_name" placeholder="Product" ng-change="newProduct.product_name=(newProduct.product_name | capitalize)"/>
                                        </div>
                                        <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                            <span>Group</span>
                                            <select ng-model="newProduct.group_id">
                                                <option value="{{group.group_id}}"  ng-repeat="group in productGroupList ">  {{group.group_name}} </option>
                                            </select>
                                        </div>

                                        <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                            <span>Quality</span>
                                            <select
                                                    ng-model="newProduct.quality"
                                                    ng-options="ql for ql in selectProductQuality">
                                            </select>
                                        </div>
                                        <input type="button" ng-click="saveNewProduct(newProduct)" value="Save" />
                                    </div>
                                </div>

                                <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12"  id="product-outer-div" ng-show="saleDetails.other_charge>0">
                                    <div class="form-group" id="add-product-div">
                                        <div class="controls col-lg-6 col-md-6 col-sm-12 col-xs-6">
                                            <input type="text" class="textinput textInput form-control" ng-model="saleDetails.other_charge_for" placeholder="Other Charge For"
                                                   ng-change="saleDetails.other_charge_for=(saleDetails.other_charge_for | capitalize)"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="purchase-details-div" ng-show="saleMaster.customer">
                                    <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="table-responsive" style="background-color: #b2dba1;">
                                             <table class="table" id="sale-table">
                                                <thead>
                                                    <tr>
                                                        <th>P.Group</th>
                                                        <th>Product</th>
                                                        <th></th>
                                                        <th>Quality</th>
                                                        <th>Quantity</th>
                                                        <th>G.wt</th>
                                                        <th>Net.wt</th>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                        <th>Mk.ch.Type</th>
                                                        <th>Mk.Rt</th>
                                                        <th>Mk.Ch</th>
                                                        <th>Oth.Ch</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <select
                                                                ng-model="saleDetails.productGroup"
                                                                ng-options="pGroup as pGroup.group_name for pGroup in productGroupList" ng-change="getProductByGroup();setGstRate()">
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >
                                                                    <select
                                                                            ng-model="saleDetails.product"
                                                                            ng-options="pName as pName.product_name for pName in productByGroup"
                                                                            ng-change="setProductQuality()">
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td ng-show="saleDetails.productGroup">
                                                            <a href="#"><span ng-click="addProductFromSale()" class="glyphicon glyphicon-plus"ng-show="!addProductDiv"></span></a>
                                                            <a href="#"><span ng-click="minimizeAddProductDiv()" class="glyphicon glyphicon-minus" ng-show="addProductDiv"></span></a>
                                                        </td>
                                                        <td>
                                                            <a href="#"  ng-click="showQuality=!showQuality" ng-init="showQuality=false" >{{saleDetails.quality}}</a>
                                                            <div class="form-group" ng-show="showQuality">
                                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >
                                                                    <select
                                                                            ng-change="showQuality = !showQuality"
                                                                            ng-model="saleDetails.quality"
                                                                            ng-options="quality.quality as quality.quality for quality in productQualityList ">
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <td><input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" numbers-only ng-model="saleDetails.quantity" ></td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" gold-decimal-places numeric-value  ng-keyup="" ng-model="saleDetails.gross_weight" ng-change="setGst()">
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" gold-decimal-places numeric-value ng-keyup="setAmount(); getMakingCharge()" ng-model="saleDetails.net_weight">
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" numeric-value currency-decimal-places ng-keyup="setAmount()" ng-model="saleDetails.rate">
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" currency-decimal-places disabled  ng-model="saleDetails.amount" ng-value="saleDetails.amount ? (saleDetails.amount | number:2) : ''">
                                                        </td>
                                                        <td>
                                                            <select ng-model="saleDetails.making_charge_type" ng-change="getMakingCharge()">
                                                                <option value="1">Normal</option>
                                                                <option value="2">Fixed</option>
                                                            </select>

                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" currency-decimal-places numeric-value ng-model="saleDetails.making_rate" ng-change="getMakingCharge()">
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" ng-model="saleDetails.making_charge" ng-value="saleDetails.making_charge ? (saleDetails.making_charge | number:2) : ''"  disabled ng-change="setGst()">
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" currency-decimal-places numeric-value ng-model="saleDetails.other_charge" ng-change="saleDetails.other_charge=roundNumber(saleDetails.other_charge)">
                                                        </td>

                                                        <td><input ng-disabled="btnSubmitDisable" type="button" value="Add" ng-click="addSaleDetailsData(saleDetails);"></td>
                                                    </tr>
                                                </tbody>
                                                 <tfoot>
                                                 <tr>
                                                     <td colspan="2" class="text-right"><growl-notification ng-if="productNull">!! Select Product</growl-notification></td>
                                                     <td colspan="10" class="text-right"><growl-notification ng-if="showNotification">!! Duplicate entry</growl-notification></td>
                                                 </tr>
                                                 </tfoot>

                                            </table>
                                        </div>
                                        <div class="table-responsive" style="background-color: #B3D38D;">
                                            <table class="table" id="sale-details-list-table">
                                            <thead>
                                            <tr>
                                                <th class="text-center">SL</th>
                                                <th class="text-center">Product</th>
                                                <th class="text-center">Quality</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-center">G.wt</th>
                                                <th class="text-center">Net.wt</th>
                                                <th class="text-center">Rate</th>
                                                <th class="text-center">Amt</th>
                                                <th class="text-center">Mk.Rt(g)</th>
                                                <th class="text-center">Mk.Chg</th>
                                                <th class="text-center">Oth.Chg</th>
                                                <th class="text-center">SGST</th>
                                                <th class="text-center">CGST</th>
                                                <th class="text-center">IGST</th>
                                                <th class="text-center">Total amt</th>
                                            </tr>
                                            </thead>
                                            <tbody ng-repeat="s in saleDetailsList">
                                            <tr>
                                                <td class="text-right">{{$index+1}}</td>
                                                <td class="text-center">{{s.product.product_name}}</td>
                                                <td class="text-center">{{s.quality}}</td>
                                                <td class="text-right">{{s.quantity}}&nbsp;</td>
                                                <td class="text-right"> {{s.gross_weight | number: 3}}</td>
                                                <td class="text-right">{{s.net_weight | number: 3}}</td>
                                                <td class="text-right"><i class="fa fa-inr"></i> {{s.rate}}</td>
                                                <td class="text-right"><i class="fa fa-inr"></i> {{s.amount | number:2}}</td>
                                                <td class="text-right"><i class="fa fa-inr"></i> {{s.making_charge_type=="2"?"NIL":s.making_rate}}</td>
                                                <td class="text-right"><i class="fa fa-inr"></i> {{s.making_charge}}</td>
                                                <td class="text-right"><i class="fa fa-inr"></i> {{s.other_charge}}</td>
                                                <td class="text-right"><i class="fa fa-inr"></i> {{s.sgst | number:2}}</td>
                                                <td class="text-right"><i class="fa fa-inr"></i> {{s.cgst | number:2}}</td>
                                                <td class="text-right"><i class="fa fa-inr"></i> {{s.igst | number:2}}</td>
                                                <td class="text-right"><i class="fa fa-inr"></i> {{roundNumber(s.totalAmount,2) | number: 2}}</td>
                                                <td> <a href="#" ng-hide="btnSubmitDisable" data-ng-click="removeRow($index)"><span class="glyphicon glyphicon-remove"></span> Remove </a></td>
                                            </tr>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="9" class="text-center">Total:</td>
                                                <td  class="text-right">{{saleTableFooter[0].totalMakingCharge | number:2}}</td>
                                                <td  class="text-right">{{saleTableFooter[0].totalOtherCharge | number:2}}</td>
                                                <td  class="text-right">{{saleTableFooter[0].totalSgst | number:2}}</td>
                                                <td  class="text-right">{{saleTableFooter[0].totalCgst | number:2}}</td>
                                                <td  class="text-right">{{saleTableFooter[0].totalIgst | number:2}}</td>
                                                <td  class="text-right">{{saleTableFooter[0].totalSaleAmount | number:2}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="14" class="text-right">Round:</td>

                                                <td  class="text-right">{{saleMaster.roundedOff |number:2}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="14" class="text-right">Grand Total:</td>

                                                <td  class="text-right">{{saleMaster.grandTotal |number:2}}</td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <div class="controls col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <input type="button" class="btn pull-right" ng-disabled="btnSubmitDisable || saleForm.$invalid" id="save-sale-data" ng-click="saveSaleDetails(saleMaster,saleDetailToSave)" value="Save"/>
                                                <input type="button" class="btn pull-left"  id="new-sale" ng-click="newSaleForBill1()" value="New Sale"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            </div>
                        </div>
                    </div>
                    <!-- <pre>saleMaster = {{saleMaster | json}}</pre>-->
                    <!--/.Panel 1-->
                    <!--Panel 2-->
                    <div ng-show="isSet(2)">
                        <p><input type="text" ng-model="searchItem"><span class="glyphicon glyphicon-search"></span> Search </p>
                        <div id="sales-list-table-div">
                            <table cellpadding="0" cellspacing="0" class="table table-bordered">
                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th ng-click="changeSorting('person_id')">ID<i class="glyphicon" ng-class="getIcon('person_id')"></i></th>
                                    <th ng-click="changeSorting('person_name')">Name<i class="glyphicon" ng-class="getIcon('person_name')"></i></th>
                                    <th ng-click="changeSorting('mobile_no')">Total<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                    <th>Action</th>
                                </tr>
                                <tbody>
                                    <tr ng-repeat="sale in allSaleList | filter : searchItem  | orderBy:sort.active:sort.descending" id="bill-{{ sale.bill_number }}">
                                        <td><b>{{ $index+1}}</b></td>
                                        <td>{{sale.sale_date  | date : "dd-MM-y" }}</td>
                                        <td>{{sale.bill_number}}</td>
                                        <td>{{sale.customer_name}}</td>
                                        <td class="text-right">{{sale.total_bill_amount | number:2}}</td>
                                        <td style="padding-left: 20px;">
                                            <a href="#" ng-click="showSaleBill(sale)"><i class=" glyphicon glyphicon-eye-open"></i></a>
                                            &nbsp;
                                            <a href="#" ng-click="sendToEditBillPage(sale)"><i class="glyphicon glyphicon-edit"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--/.Panel 2-->
                    <!--Panel 3-->
                    <div ng-show="isSet(3)">
                        <style type="text/css">
                            #print-customer-details-ul{
                                list-style: none;
                            }
                            #print-sale-details-ul{
                                list-style: none;
                            }
                            #show-bill-details-table tfoot{
                                border: 1px solid black !important;
                            }
                        </style>

                        <div id="show-bill-div">
                            <h3 class="text-center">TAX INVOICE</h3>
                            <h5 class="text-center">Original Buyers/ Sellers Copy</h5>
                            <table class="table" id="show-bill-customer-table">
                                <tbody>
                                    <tr>
                                        <td >
                                            <strong>Customer</strong><br>
                                            <ul id="print-customer-details-ul">
                                                <li>{{billMaster.customer_billing_name}}</li>
                                                <li>{{billMaster.address1}}</li>
                                                <li ng-show="billMaster.address2.length>0">{{billMaster.address2}}</li>
                                                <li>{{billMaster.city}}</li>
                                                <li>{{billMaster.district_name}}</li>
                                                <li ng-show="billMaster.post_office.length>0">Post: {{billMaster.post_office}}</li>
                                                <li ng-show="billMaster.pin.length>0">PIN: {{billMaster.pin}}</li>
                                                <li ng-show="billMaster.pan_no.length>0">PAN: {{billMaster.pan_no}}</li>
                                                <li ng-show="billMaster.gst_number.length>0">GST: {{billMaster.gst_number}}</li>
                                                <li>{{billMaster.mobile_no}}</li>
                                                <li>{{billMaster.phone_no}}</li>
                                            </ul>
                                        </td>
                                        <td >
                                            <ul id="print-sale-details-ul">
                                                <li>Bill Number</li>
                                                <li>Date </li>
                                                <li>O.Date</li>
                                                <li ng-show="billMaster.memo_no.length>0">Memo No</li>
                                                <li ng-show="billMaster.order_no.length>0">Order No</li>
<!--                                                <li>Seller</li>-->
                                                <li>VAT</li>
                                                <li>GST</li>
                                                <li>BIS CM/L</li>
												<li>HM/C</li>
                                                <li>PAN</li>
                                            </ul>
                                        </td>
                                        <td style="padding-left: 0px">
                                            <ul id="print-sale-details-ul">
                                                <li>:&nbsp;{{billMaster.bill_number}}</li>
                                                <li>:&nbsp;{{billMaster.sale_date  | date : "dd-MM-y" }}</li>
                                                <li>:&nbsp;{{billMaster.order_date | date : "dd-MM-y" }}</li>
                                                <li ng-show="billMaster.memo_no.length>0">:&nbsp;{{billMaster.memo_no}}</li>
                                                <li ng-show="billMaster.order_no.length>0">:&nbsp;{{billMaster.order_no}}</li>
<!--                                                <li>:&nbsp;{{billMaster.employee_name}}</li>-->
                                                <li>:&nbsp;19778268088</li>
                                                <li>:&nbsp;19BJXPS7073N1ZT</li>
                                                <li>:&nbsp;5427667</li>
												<li>:&nbsp;5190136611</li>
                                                <li>:&nbsp;BJXPS7073N</li>
                                            </ul>
                                        </td>
                                    </tr>


                                </tbody>

                            </table>
<!--                            table for sale details-->
                            <style>
                                #other-charge-details-list{
                                    list-style: none;
                                }
                                #hsn-table th{
                                    border: 1px solid black;
                                }
                                #hsn-table td{
                                    border: 1px solid black;
                                }

                                /*#signature-table > tbody:nth-child(1) > tr:nth-child(1){*/
                                    /*border-top: 2px solid black;*/
                                /*}*/
                                #signature-table{
                                    margin-top: 200px;
                                }
                                #show-bill-details-table > tfoot > tr{
                                    border-right: 1px solid black; !important;
                                }
                                #detail-div{
                                    border-bottom: 1px solid black!important;
                                }
                            </style>
                            <table class="table" id="show-bill-details-table" >
                                <thead>
                                    <tr>
                                        <th class="wy-text-center">Sl</th>
                                        <th class="wy-text-center">Item</th>
                                        <th class="wy-text-center">HSN</th>
                                        <th class="wy-text-center">Ql</th>
                                        <th class="wy-text-center">Qnt</th>
                                        <th class="wy-text-center">Gr Wt(gm)</th>
                                        <th class="wy-text-center">Net Wt(gm)</th>
                                        <th class="wy-text-center">Rate(gm)</th>
                                        <th class="wy-text-center">Value</th>
                                        <th class="wy-text-center">Mk Rate(gm)</th>
                                        <th class="wy-text-center">Mk Chrg</th>
                                        <th class="wy-text-center">Other Charge</th>
                                        <th class="wy-text-center">SGST</th>
                                        <th class="wy-text-center">CGST</th>
                                        <th class="wy-text-center">IGST</th>
                                        <th class="wy-text-center">Amt(<i class="fa fa-inr"></i>)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <tr ng-repeat="x in billDetails">
                                       <td class="text-right">{{$index+1}}</td>
                                       <td class="text-left">{{x.product_name}}</td>
                                       <td class="text-center">{{x.hsn_code}}</td>
                                       <td class="text-center">{{x.product_quality}}</td>
                                       <td class="text-right">{{x.quantity}}</td>
                                       <td class="text-right">{{x.gross_weight | number:3}}</td>
                                       <td class="text-right">{{x.net_weight | number:3}}</td>
                                       <td class="text-right">{{x.rate | number:2}}</td>
                                       <td class="text-right">{{x.sale_value | number:2}}</td>
                                       <td class="text-right">{{x.making_charge_type=="2"?"NIL":x.making_rate}}</td>
                                       <td class="text-right">{{x.making_charge | number:2}}</td>
                                       <td class="text-right">{{x.other_charge | number:2}}</td>
                                       <td class="text-right">{{x.sgst_value | number:2}}</td>
                                       <td class="text-right">{{x.cgst_value | number:2}}</td>
                                       <td class="text-right">{{x.igst_value | number:2}}</td>
                                       <td class="text-right">{{x.total_amount | number:2}}</td>
                                   </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td  colspan="12" rowspan="5" style="border: 1px solid black !important;">
                                            <span style="padding-left: 25px;">Other charge Details</span>
                                            <div class="row" ng-repeat="x in otherChargeDetailsList">
                                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="text-align: center">{{x.serial_key}}</div>
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">{{x.other_charge_for}}</div>
                                            </div>
                                        </td>
                                        <td colspan="2">Total</td>
                                        <td style="text-align: right" colspan="2">{{showTableFooter.grandTotalAmount | number:2}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Taxable Amount</td>
                                        <td style="text-align: right" colspan="2">{{showTableFooter.totalTaxableAmount | number:2}}</td>
                                    </tr>
                                    <tr ng-repeat="g in rateWiseGST">
                                        <td colspan="2">GST({{g.gst_rate | number:2}})%&nbsp;</td>
                                        <td style="text-align: right" colspan="2">{{(g.sum_of_gst) | number:2}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Grand Total:</td>
                                        <td style="text-align: right" colspan="2">{{showTableFooter.grandTotalAmount | number:2}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Rounded off:</td>
                                        <td style="text-align: right" colspan="2">{{billMaster.roundedOff | number:2}}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="row" style="margin-top: 0px">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                  <b ng-if="(showTableFooter.grandTotalAmount + billMaster.roundedOff)>0">In words {{(showTableFooter.grandTotalAmount + billMaster.roundedOff) | AmountConvertToWord}}</b>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="text-align: right">Bill Amount:</div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="text-align: right">
                                    <b>{{(showTableFooter.grandTotalAmount + billMaster.roundedOff) | number:2}}</b>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"><b>Transaction By</b></div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="text-align: left">{{billMaster.transaction_mode}}</div>
                            </div>
                            <div class="row" id="detail-div">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"><b>Details</b></div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="text-align: left">{{billMaster.card_number}}</div>
                            </div>

                            <table class="table" id="signature-table">
                                <tbody>
                                    <tr>
                                        <td class="text-center">
                                            ___________________________________<br>Customer Signature
                                        </td>
                                        <td class="text-center">
                                            ___________________________________<br>Authorised Signatory
                                        </td>
                                    </tr>
                                </tbody>
                            </table>


                            <table class="table table-condensed  table-responsive" id="hsn-table">
                                <thead>
                                    <tr>
                                        <th   class="text-center align-top">HSN Code</th>
                                        <th  class="text-center align-top">Taxable Amount</th>
                                        <th colspan="2" class="text-center">SGST</th>
                                        <th colspan="2" class="text-center">CGST</th>
                                        <th colspan="2" class="text-center">IGST</th>
                                        <th class="text-center">Total Tax</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center">Rate %</td>
                                        <td class="text-center">SGST</td>
                                        <td class="text-center">Rate %</td>
                                        <td class="text-center">CGST</td>
                                        <td class="text-center">Rate %</td>
                                        <td class="text-center">IGST</td>
                                        <td class="text-center"></td>
                                    </tr>
                                </thead>
                                <tbody ng-repeat="y in gstTable">
                                    <tr>
                                        <td style="text-align: right">{{y.hsn_code}}</td>
                                        <td style="text-align: right">{{y.sum_of_taxable_amount | number:2}}</td>
                                        <td style="text-align: right">{{y.sgst_rate * 100 | number:2}}%</td>
                                        <td style="text-align: right">{{y.sum_of_sgst | number:2}}</td>
                                        <td style="text-align: right">{{y.cgst_rate * 100 | number:2}}%</td>
                                        <td style="text-align: right">{{y.sum_of_cgst | number:2}}</td>
                                        <td style="text-align: right">{{y.igst_rate * 100| number:2}}%</td>
                                        <td style="text-align: right">{{y.sum_of_igst | number:2}}</td>
                                        <td style="text-align: right">{{y.sum_of_cgst+y.sum_of_cgst+y.sum_of_igst | number:2}}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <td class="text-center">Total</td>
                                    <td style="text-align: right">{{showTableFooter.totalTaxableAmount | number:2}}</td>
                                    <td style="text-align: right"></td>
                                    <td style="text-align: right">{{gstTableFooter.total_sgst | number:2}}</td>
                                    <td style="text-align: right"></td>
                                    <td style="text-align: right">{{gstTableFooter.total_cgst | number:2}}</td>
                                    <td style="text-align: right"></td>
                                    <td style="text-align: right">{{gstTableFooter.total_igst | number:2}}</td>
                                    <td style="text-align: right">{{gstTableFooter.total_sgst+gstTableFooter.total_cgst+gstTableFooter.total_igst | number:2}}</td>
                                </tfoot>
                            </table>
                        </div>
                        <p>
                            <a href="#" ng-click="huiPrintDiv('show-bill-div','my_printing_style.css')" class="btn btn-info btn-lg no-print">
                                <span class="glyphicon glyphicon-print"></span> Print
                            </a>
                        </p>
<!--                        <pre>billMaster={{billMaster | json}}</pre>-->
                    </div>
                    <!--/.Panel 3-->



                    <!--Panel 4-->
                    <!-- By Sreeparna on 23.10.2024 -->

                    <div ng-show="isSet(4)">
                        <div id="my-tab-4">
                            <form name="billEditForm" class="form-horizontal" id="bill-edit-form">
                                <div class="row">
                                    <h5 class="text-center" style="font-weight: bold;font-size: large;">Edit Bill</h5>
                                    <table class="table" id="show-bill-customer-table">
                                        <tbody>
                                            <tr>
                                                <td >
                                                    <strong>Customer</strong><br>
                                                    <ul id="print-customer-details-ul">
                                                        <li>{{billMaster.customer_billing_name}}</li>
                                                        <li>{{billMaster.address1}}</li>
                                                        <li ng-show="billMaster.address2.length>0">{{billMaster.address2}}</li>
                                                        <li>{{billMaster.city}}</li>
                                                        <li>{{billMaster.district_name}}</li>
                                                        <li ng-show="billMaster.post_office.length>0">Post: {{billMaster.post_office}}</li>
                                                        <li ng-show="billMaster.pin.length>0">PIN: {{billMaster.pin}}</li>
                                                        <li ng-show="billMaster.pan_no.length>0">PAN: {{billMaster.pan_no}}</li>
                                                        <li ng-show="billMaster.gst_number.length>0">GST: {{billMaster.gst_number}}</li>
                                                        <li>{{billMaster.mobile_no}}</li>
                                                        <li>{{billMaster.phone_no}}</li>
                                                    </ul>
                                                    <button type="button" class="btn" ng-click=backTolist(billMaster.bill_number) style="background-color:darkturquoise;font-weight: 800;">Back to list</button>
                                                </td>
                                                <td >
                                                    <ul id="print-sale-details-ul">
                                                        <li>Bill Number</li>
                                                        <li>Date </li>
                                                        <li>O.Date</li>
                                                        <li ng-show="billMaster.memo_no.length>0">Memo No</li>
                                                        <li ng-show="billMaster.order_no.length>0">Order No</li>
                                                        <li>VAT</li>
                                                        <li>GST</li>
                                                        <li>BIS CM/L</li>
                                                        <li>HM/C</li>
                                                        <li>PAN</li>
                                                    </ul>
                                                </td>
                                                <td style="padding-left: 0px">
                                                    <ul id="print-sale-details-ul">
                                                        <li>:&nbsp;{{billMaster.bill_number}}</li>
                                                        <li>:&nbsp;{{billMaster.sale_date  | date : "dd-MM-y" }}</li>
                                                        <li>:&nbsp;{{billMaster.order_date | date : "dd-MM-y" }}</li>
                                                        <li ng-show="billMaster.memo_no.length>0">:&nbsp;{{billMaster.memo_no}}</li>
                                                        <li ng-show="billMaster.order_no.length>0">:&nbsp;{{billMaster.order_no}}</li>
                                                        <li>:&nbsp;19778268088</li>
                                                        <li>:&nbsp;19BJXPS7073N1ZT</li>
                                                        <li>:&nbsp;5427667</li>
                                                        <li>:&nbsp;5190136611</li>
                                                        <li>:&nbsp;BJXPS7073N</li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12"  ng-show="billDetailsEditProductObj.other_charge>0" style="padding-bottom: 3px;padding-top: 3px;background-color: cornflowerblue">
                                    <div class="form-controls col-sm-2"><label>Oth chrg for: </label></div>
                                    <div class="form-controls col-sm-6">
                                        <input type="text" class="textinput textInput form-control" ng-model="billDetailsEditProductObj.other_charge_for" placeholder="Other Charge For" ng-change="billDetailsEditProductObj.other_charge_for=(billDetailsEditProductObj.other_charge_for | capitalize)"/>
                                    </div>
                                </div>
                                
                                <div class="row" ng-if="isBillUpdated">
                                    <div class="col-md-12 text-center">
                                        <h1>Bill updated successfully!</h1>
                                    </div>
                                </div>

                                <div class="row" id="bill-edit-div">
                                    <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="table-responsive" style="background-color: #6f3d3d4a;">
                                             <table class="table" id="sale-table">
                                                <thead>
                                                    <tr>
                                                        <th>P.Group</th>
                                                        <th>Product</th>
                                                        <th></th>
                                                        <th>Quality</th>
                                                        <th>Quantity</th>
                                                        <th>G.wt</th>
                                                        <th>Net.wt</th>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                        <th>Mk.ch.Type</th>
                                                        <th>Mk.Rt</th>
                                                        <th>Mk.Ch</th>
                                                        <th>Oth.Ch</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <select
                                                                ng-model="billDetailsEditProductObj.productGroup"
                                                                ng-options="pGroup as pGroup.group_name for pGroup in productGroupList" ng-change="getProductByGroupForBillEdit();setGstRateForEditBill()">
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >
                                                                    <select
                                                                            ng-model="billDetailsEditProductObj.product"
                                                                            ng-options="pName as pName.product_name for pName in productListForEditBill"
                                                                            ng-change="setProductQualityForBillEdit()">
                                                                            <option value="">select product</option> 
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span ng-show="billDetailsEditProductObj.productGroup">
                                                                <a href="#">
                                                                    <span ng-click="addProductInEditBill()" class="glyphicon glyphicon-plus"ng-show="!addProductDivInEditBill"></span>
                                                                </a>
                                                                <a href="#">
                                                                    <span ng-click="minimizeAddProductDiv()" class="glyphicon glyphicon-minus" ng-show="addProductDivInEditBill"></span>
                                                                </a>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="#"  ng-click="showQuality=!showQuality" ng-init="showQuality=false" >{{billDetailsEditProductObj.quality}}</a>
                                                            <div class="form-group" ng-show="showQuality">
                                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >
                                                                    <select
                                                                            ng-change="showQuality = !showQuality"
                                                                            ng-model="billDetailsEditProductObj.quality"
                                                                            ng-options="quality.quality as quality.quality for quality in productQualityList ">
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <td><input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" numbers-only ng-model="billDetailsEditProductObj.quantity" ng-keyup="setAmountForEditBill()"></td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" gold-decimal-places numeric-value  ng-keyup="" ng-model="billDetailsEditProductObj.gross_weight" ng-change="setGstRateForEditBill()">
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" gold-decimal-places numeric-value ng-keyup="setAmountForEditBill(); getMakingChargeForEditBill()" ng-model="billDetailsEditProductObj.net_weight">
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" numeric-value currency-decimal-places ng-keyup="setAmountForEditBill()" ng-model="billDetailsEditProductObj.rate">
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" currency-decimal-places disabled  ng-model="billDetailsEditProductObj.amount">
                                                        </td>
                                                        <td>
                                                            <select ng-model="billDetailsEditProductObj.making_charge_type" ng-change="getMakingChargeForEditBill()">
                                                                <option value="1">Normal</option>
                                                                <option value="2">Fixed</option>
                                                            </select>

                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" currency-decimal-places numeric-value ng-model="billDetailsEditProductObj.making_rate" ng-change="getMakingChargeForEditBill()">
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" ng-model="billDetailsEditProductObj.making_charge" disabled ng-change="setGstRateForEditBill()">
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" currency-decimal-places numeric-value ng-model="billDetailsEditProductObj.other_charge" ng-change="billDetailsEditProductObj.other_charge=roundNumber(billDetailsEditProductObj.other_charge)">
                                                        </td>

                                                        <td><input ng-disabled="btnSubmitDisable" type="button" value="Add" ng-click="changeProductInExistingBill(billDetailsEditProductObj);"></td>
                                                    </tr>
                                                </tbody>
                                                 <tfoot>
                                                 <tr>
                                                     <td colspan="2" class="text-right"><growl-notification ng-if="!billDetails.product && billDetails.productGroup && billDetails.amount">!! Select Product</growl-notification></td>
                                                     <td colspan="10" class="text-right"><growl-notification ng-if="isDuplicateDataInEditBill">!! Duplicate entry</growl-notification></td>
                                                 </tr>
                                                 </tfoot>

                                            </table>
                                        </div>
                                        <div class="table-responsive" style="background-color: #c4abc4;">
                                            <table class="table" id="sale-details-list-table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">SL</th>
                                                        <th class="text-center">Product</th>
                                                        <th class="text-center">Quality</th>
                                                        <th class="text-center">Quantity</th>
                                                        <th class="text-center">G.wt</th>
                                                        <th class="text-center">Net.wt</th>
                                                        <th class="text-center">Rate</th>
                                                        <th class="text-center">Amt</th>
                                                        <th class="text-center">Mk.Rt(g)</th>
                                                        <th class="text-center">Mk.Chg</th>
                                                        <th class="text-center">Oth.Chg</th>
                                                        <th class="text-center">SGST</th>
                                                        <th class="text-center">CGST</th>
                                                        <th class="text-center">IGST</th>
                                                        <th class="text-center">Total amt</th>
                                                    </tr>
                                                </thead>
                                                <tbody ng-repeat="s in billDetails">
                                                    <tr>
                                                        <td class="text-right">{{$index+1}}</td>
                                                        <td class="text-center">{{s.product_name}}</td>
                                                        <td class="text-center">{{s.product_quality}}</td>
                                                        <td class="text-right">{{s.quantity}}&nbsp;</td>
                                                        <td class="text-right"> {{s.gross_weight | number: 3}}</td>
                                                        <td class="text-right">{{s.net_weight | number: 3}}</td>
                                                        <td class="text-right"><i class="fa fa-inr"></i> {{s.rate}}</td>
                                                        <td class="text-right"><i class="fa fa-inr"></i> {{s.sale_value | number:2}}</td>
                                                        <td class="text-right"><i class="fa fa-inr"></i> {{s.making_charge_type=="2"?"NIL":s.making_rate}}</td>
                                                        <td class="text-right"><i class="fa fa-inr"></i> {{s.making_charge | number:2}}</td>
                                                        <td class="text-right"><i class="fa fa-inr"></i> 
                                                            {{s.other_charge | number:2}}
                                                        </td>
                                                        <td class="text-right"><i class="fa fa-inr"></i> {{s.sgst_value | number:2}}</td>
                                                        <td class="text-right"><i class="fa fa-inr"></i> {{s.cgst_value | number:2}}</td>
                                                        <td class="text-right"><i class="fa fa-inr"></i> {{s.igst_value | number:2}}</td>
                                                        <td class="text-right"><i class="fa fa-inr"></i> {{roundNumber(s.total_amount,2) | number: 2}}</td>
                                                        <td> 
                                                            <!-- <a href="#" ng-hide="btnSubmitDisable" data-ng-click="removeRow($index)">
                                                            <span class="glyphicon glyphicon-trash"></span>
                                                            
                                                            </a> -->
                                                            <button type="button" class="btn btn-default btn-sm" ng-hide="btnSubmitDisable" data-ng-click="removeRowFromEditBillTable($index)">
                                                                <span class="glyphicon glyphicon-trash"></span> 
                                                            </button>

                                                            <button type="button" class="btn btn-default btn-sm" ng-hide="btnSubmitDisable" data-ng-click="populateBillEditDataToForm($index)">
                                                                <span class="glyphicon glyphicon-edit"></span> 
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="8" class="text-center">Total:</td>
                                                        <td  class="text-right">{{showTableFooter.totalQuantity}}</td>
                                                        <td  class="text-right">{{showTableFooter.totalMakingCharge | number:2}}</td>
                                                        <td  class="text-right">{{saleTableFooter[0].totalOtherCharge | number:2}}</td>
                                                        <td  class="text-right">{{saleTableFooter[0].totalSgst | number:2}}</td>
                                                        <td  class="text-right">{{saleTableFooter[0].totalCgst | number:2}}</td>
                                                        <td  class="text-right">{{saleTableFooter[0].totalIgst | number:2}}</td>
                                                        <td  class="text-right">{{showTableFooter.grandTotalAmount | number:2}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="14" class="text-right">Round:</td>

                                                        <td  class="text-right">{{billMaster.roundedOff |number:2}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="14" class="text-right">Grand Total:</td>

                                                        <td  class="text-right">{{(showTableFooter.grandTotalAmount + billMaster.roundedOff) | number:2}}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-top:5px">
                                    <div class="controls col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <input type="button" class="btn pull-right" ng-disabled="false" ng-click="updateBill(billMaster,billDetails)" value="Update"/>
                                    </div>
                                </div>

                                <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12" ng-show="false">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <pre>billDetailsEditProductObj = {{billDetailsEditProductObj | json}}</pre>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <pre>billMaster = {{billMaster | json}}</pre>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <pre>billDetails = {{billDetails | json}}</pre>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                        <pre>showTableFooter = {{showTableFooter | json}}</pre>
                                    </div>
                                </div>
                            </form>

                            </div>
                        </div>
                    </div>
                    <!--/.Panel 4-->


                </div>
            </div>

        </div>
    </div>

        <?php
    }


    
                                 // BILL 2 FUNCTIONS & HTML FORMS CODE

    public function angular_view_bill2(){
        ?>
        <style type="text/css">
            .navbar-fixed-top {
                border: none;
                background: #36d278;


                margin-top: -20px;
            }
            .navbar-fixed-top a{
                color: #a6e1ec;
            }
            #bill2-customer-div{
                margin-top: 10px;
            }
            h1{
                color: blue;
            }
            #mySidenav a[ng-click]{
                cursor: pointer;
                position: absolute;
                left: -20px;
                transition: 0.3s;
                padding: 15px;
                width: 140px;
                text-decoration: none;
                font-size: 15px;
                color: white;
                border-radius: 0 5px 5px 0;
                background-color: #ac2925;
            }

            #mySidenav a[ng-click]:hover {
                left: 0;
            }

            #mySidenav a:hover {
                left: 0;
            }
            #new-vendor {
                top: 20px;
                background-color: #4CAF50;
            }

            #update-vendor {
                top: 78px;
                background-color: #2196F3;
            }

            #show-vendor{
                top: 136px;
                background-color: #f44336;
            }
            #main-working-div h1{
                color: darkblue;
            }
            #vendor-form input{
                border-radius: 5px;
            }
            #vendorForm{
                margin-top: 10px;
            }
            input.ng-invalid {
                background-color: pink;
            }
            #bill2-customer-details{
                list-style-type: none;
            }
            growl-notification{
                color: red;
            }
            .td-input{
                padding: 2px;
                margin-left: 0px;
                margin-right: 0px;
            }
            table tr td{
                padding: 2px !important;
                margin: 0 !important;
                margin-left: 2px;
            }
            #show-bill-details-table > tfoot > tr > td{
                border-top: none !important;

            }
            #add-product-div{
                margin-top: 15px;
                margin-left: 30px;
                padding-left: 250px;
            }
            #product-outer-div{
                background-color: lightgray;
            }

        </style>

                                    <!-- BILL 2 FUNCTIONS & HTML FORMS CODE -->
        <div class="container-fluid">
        <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="bill2-customer-div">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-justified indigo" role="tablist">
            <li class="nav-item">
                <a class="nav-link " data-toggle="tab" href="#" role="tab" ng-click="setTab(1)"><span class="glyphicon glyphicon-shopping-cart"></span>&nbspStart Sale</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click=""><span class="glyphicon glyphicon-list-alt"></span>Show Sale Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click=""><span class="glyphicon glyphicon-file"></span>Show Bill</a>
            </li>
        </ul>
        <!-- Tab panels -->
        <div class="tab-content">
        <!--Panel 1-->
        <div ng-show="isSet(1)">
        <div id="my-tab-1">
        <form name="bill2SaleForm" class="form-horizontal" id="bill2-form">
        <div class="row">
            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="background-color: #e3d2d2;height: 130px" id="upper-left">
                    <div class="form-group">
                        <label  class="control-label  col-lg-2 col-md-2 col-sm-2 col-xs-2 ">Customer</label>
                        <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <input type="text" class="textinput textInput form-control" ng-change="bill2SearchCustomerByKey()" ng-model="bill2SaleMaster.customerSearchKey"/>
                        </div>
                        <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <select
                                ng-model="bill2SaleMaster.customer"
                                ng-options="customer as customer.person_name for customer in bill2CustomerListByKey">
                            </select>
                        </div>
                    </div>

                    <ul id="bill2-customer-details" ng-show="bill2SaleMaster.customer" style="margin-top: 0px;padding-top: 0px;padding-left: 0px;font-size: 12px;line-height:17px">
                        <li ng-show="bill2SaleMaster.customer.billing_name.length>0"><span class="glyphicon glyphicon-user"></span> {{bill2SaleMaster.customer.billing_name}}</li>
                        <li><span class="glyphicon glyphicon-earphone"></span> {{bill2SaleMaster.customer.mobile_no || 'N/A'}}
                        <li ng-show="bill2SaleMaster.customer.address1.length>0"><i class="fa fa-map-marker" aria-hidden="true"></i> {{bill2SaleMaster.customer.address1}}</li>
                        <li ng-show="bill2SaleMaster.customer.city.length>0 || " purchaseMaster.vendor.post_office>0
                            {{bill2SaleMaster.customer.city}}, {{purchaseMaster.vendor.post_office}}
                        </li>
                        <li ng-show="bill2SaleMaster.customer.district_name.length>0 || bill2SaleMaster.customer.state_name.length>0">
                            Dist. - {{bill2SaleMaster.customer.district_name}}, {{bill2SaleMaster.customer.state_name}}</li>
                        <li ng-show="bill2SaleMaster.customer.gst_number.length>0">GSTIN: {{bill2SaleMaster.customer.gst_number}}</li>
                    </ul>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="background-color: #EAF2F5;height: 130px;padding-top: 2px" id="upper-right">

                    <div class="form-group">

                        <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Memo number</label>
                        <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <input type="text" class="textinput textInput form-control capitalizeWord" ng-model="bill2SaleMaster.memo_no" required/>
                        </div>
                        <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Order Number</label>
                        <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <input type="text" class="textinput textInput form-control capitalizeWord" ng-model="bill2SaleMaster.order_no"required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Order Date</label>
                        <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <input type="text" ng-pattern="/\d\d/\d\d/\d\d\d\d/"  id="FromDate" name="orderDate"   ng-model="bill2SaleMaster.order_date"  required/>
                        </div>
                        <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Sales Date</label>
                        <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <input type="text" ng-pattern="/\d\d/\d\d/\d\d\d\d/" id="FromDate" name="salesDate"  ng-model="bill2SaleMaster.sales_date"  required/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Bill Number:</label>
                        <div class="controls col-lg-4 col-md-4 col-sm-4 col-xs-4" ng-show="showBill2No">
                            <input type="text" class="textinput textInput form-control capitalizeWord" ng-model="bill2SaleMaster.bill_id" disabled/>
                        </div>
                        <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3" ng-show="showBill2No">
                            <a href="#" class="btn btn-default" ng-click="setTab(3)">
                                <span class="glyphicon glyphicon-print"></span> Show bill
                            </a>
                        </div>
                    </div>


                </div>
            </div>
        </div>

            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12" ng-show="addProductDiv" id="product-outer-div">
                <div class="form-group" id="add-product-div">
                    <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <input type="text" class="textinput textInput form-control" ng-model="newProduct.product_name" placeholder="Product" ng-change="newProduct.product_name=(newProduct.product_name | capitalize)"/>
                    </div>
                    <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <span>Group</span>
                        <select ng-model="newProduct.group_id">
                            <option value="{{group.group_id}}"  ng-repeat="group in productGroupList ">  {{group.group_name}} </option>
                        </select>
                    </div>

                    <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <span>Quality</span>
                        <select
                                ng-model="newProduct.quality"
                                ng-options="ql for ql in selectProductQuality">
                        </select>
                    </div>
                    <input type="button" ng-click="saveNewProduct(newProduct)" value="Save" />
                </div>
            </div>

            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12"  id="product-outer-div" ng-show="bill2SaleDetails.other_charge>0">
                <div class="form-group" id="add-product-div">
                    <div class="controls col-lg-6 col-md-6 col-sm-12 col-xs-6">
                        <input type="text" class="textinput textInput form-control" ng-model="bill2SaleDetails.other_charge_for" placeholder="Other Charge For"
                               ng-change="bill2SaleDetails.other_charge_for=(bill2SaleDetails.other_charge_for | capitalize)"/>
                    </div>
                </div>
            </div>
        <div class="row" id="purchase-details-div" ng-show="bill2SaleMaster.customer">
            <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="table-responsive" style="background-color: #b2dba1;">
                    <table class="table" id="sale-table">
                        <thead>
                        <tr>
                            <th>P.Group</th>
                            <th>Product</th>
                            <th></th>
                            <th>Quality</th>
                            <th>Quantity</th>
                            <th>G.wt</th>
                            <th>Net.wt</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Mk.ch.Type</th>
                            <th>Mk.Rt</th>
                            <th>Mk.Ch</th>
                            <th>Oth.Ch</th>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <select
                                    ng-model="bill2SaleDetails.productGroup"
                                    ng-options="pGroup as pGroup.group_name for pGroup in productGroupList" ng-change="getProductByGroup()">
                                </select>
                            </td>
                            <td>
                                <div class="form-group">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >
                                        <select
                                            ng-model="bill2SaleDetails.product"
                                            ng-options="pName as pName.product_name for pName in productByGroup"
                                            ng-change="setProductQuality()">
                                        </select>


                                    </div>
                                </div>

                            </td>
                            <td ng-show="bill2SaleDetails.productGroup">
                                <a href="#"><span ng-click="addProductFromSale()" class="glyphicon glyphicon-plus"ng-show="!addProductDiv"></span></a>
                                <a href="#"><span ng-click="minimizeAddProductDiv()" class="glyphicon glyphicon-minus" ng-show="addProductDiv"></span></a>
                            </td>
                            <td>
                                <a href="#"  ng-click="showQuality=!showQuality" ng-init="showQuality=false" >{{bill2SaleDetails.quality}}</a>
                                <div class="form-group" ng-show="showQuality">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >
                                        <select
                                            ng-change="showQuality = !showQuality"
                                            ng-model="bill2SaleDetails.quality"
                                            ng-options="quality.quality as quality.quality for quality in productQualityList ">
                                        </select>
                                    </div>
                                </div>
                            </td>

                            <td><input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" numbers-only ng-model="bill2SaleDetails.quantity" ></td>
                            <td>
                                <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" gold-decimal-places numeric-value  ng-keyup="" ng-model="bill2SaleDetails.gross_weight">
                            </td>
                            <td>
                                <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" gold-decimal-places numeric-value ng-keyup="setAmount(); getMakingCharge()" ng-model="bill2SaleDetails.net_weight">
                            </td>
                            <td>
                                <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" numeric-value currency-decimal-places ng-keyup="setAmount()" ng-model="bill2SaleDetails.rate">
                            </td>
                            <td>
                                <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" currency-decimal-places disabled  ng-model="bill2SaleDetails.amount"  ng-value="bill2SaleDetails.amount ? (bill2SaleDetails.amount | number:2) : ''">
                            </td>
                            <td>
                                <select ng-model="bill2SaleDetails.making_charge_type" ng-change="getMakingCharge()">
                                    <option value="1">Normal</option>
                                    <option value="2">Fixed</option>
                                </select>

                            </td>
                            <td>
                                <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" currency-decimal-places numeric-value ng-model="bill2SaleDetails.making_rate" ng-change="getMakingCharge()">
                            </td>
                            <td>
                                <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" ng-model="bill2SaleDetails.making_charge" ng-value="bill2SaleDetails.making_charge ? (bill2SaleDetails.making_charge | number:2) : ''" disabled>
                            </td>
                            <td>
                                <input  type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 td-input text-right" currency-decimal-places numeric-value ng-model="bill2SaleDetails.other_charge" ng-change="bill2SaleDetails.other_charge=roundNumber(bill2SaleDetails.other_charge,2)">
                            </td>

                            <td><input type="button" ng-disabled="btnSubmitDisable" value="Add" ng-click="addSaleDetailsData(bill2SaleDetails);"></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" class="text-right"><growl-notification ng-if="productNull">!! Select Product</growl-notification></td>
                            <td colspan="10" class="text-right"><growl-notification ng-if="showNotification">!! Duplicate entry</growl-notification></td>
                        </tr>
                        </tfoot>

                    </table>
                </div>
                <div class="table-responsive" style="background-color: #B3D38D;">
                    <table class="table" id="sale-details-list-table">
                        <thead>
                        <tr>
                            <th class="text-center">SL</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Quality</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">G.wt</th>
                            <th class="text-center">Net.wt</th>
                            <th class="text-center">Rate</th>
                            <th class="text-center">Amt</th>
                            <th class="text-center">Mk.Rt(g)</th>
                            <th class="text-center">Mk.Chg</th>
                            <th class="text-center">Oth.Chg</th>
                            <th class="text-center">Total amt</th>
                        </tr>
                        </thead>
                        <tbody ng-repeat="s in bill2SaleDetailsList">
                        <tr>
                            <td class="text-right">{{$index+1}}</td>
                            <td class="text-center">{{s.product.product_name}}</td>
                            <td class="text-center">{{s.quality}}</td>
                            <td class="text-right">{{s.quantity}}&nbsp;</td>
                            <td class="text-right"> {{s.gross_weight | number: 3}}</td>
                            <td class="text-right">{{s.net_weight | number: 3}}</td>
                            <td class="text-right"><i class="fa fa-inr"></i> {{s.rate | number: 2}}</td>
                            <td class="text-right"><i class="fa fa-inr"></i> {{s.amount | number:2}}</td>
                            <td class="text-right"><i class="fa fa-inr"></i> {{s.making_charge_type=="2"?"NIL":s.making_rate}}</td>
                            <td class="text-right"><i class="fa fa-inr"></i> {{s.making_charge | number: 2}}</td>
                            <td class="text-right"><i class="fa fa-inr"></i> {{s.other_charge |number: 2}}</td>
                            <td class="text-right"><i class="fa fa-inr"></i> {{roundNumber(s.totalAmount,2) | number: 2}}</td>
                            <td ng-hide="btnSubmitDisable"> <a href="#" data-ng-click="removeRow($index)"><span class="glyphicon glyphicon-remove"></span> Remove </a></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-center">Total:</td>
                            <td class="text-right">{{bill2SaleTableFooter[0].totalQuantity}}</td>
                            <td colspan="6" class="text-right">{{bill2SaleTableFooter[0].totalMakingCharge | number:2}}</td>
                            <td  class="text-right">{{bill2SaleTableFooter[0].totalOtherCharge | number:2}}</td>
                            <td  class="text-right">{{bill2SaleTableFooter[0].totalSaleAmount | number:2}}</td>
                        </tr>
                        <tr>
                            <td colspan="11" class="text-right">Round:</td>

                            <td  class="text-right">{{bill2SaleMaster.roundedOff |number:2}}</td>
                        </tr>
                        <tr>
                            <td colspan="11" class="text-right">Grand Total:</td>

                            <td  class="text-right">{{bill2SaleMaster.grandTotal |number:2}}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <br>
                <div class="form-group">
                    <div class="controls col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input type="button" class="btn pull-right" ng-disabled="btnSubmitDisable || bill2SaleForm.$invalid" id="update-purchas-data" ng-click="saveBill2SaleDetails(bill2SaleMaster,bill2SaleDetailToSave)" value="Save"/>
                        <input type="button" class="btn pull-left" id="new-sale-for-bill2" ng-click="newSaleForBill12()" value="New Sale"/>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <div class="row" style="background-color: cornsilk;">



<!--            <pre>bill2CustomerListByKey = {{bill2CustomerListByKey | json}}</pre>-->

<!--            <pre>bill2CustomerList = {{bill2CustomerList | json}}</pre>-->

        </div>
        </div>
        </div>
        <!--/.Panel 1-->
        <!--Panel 2-->
        <div ng-show="isSet(2)">
            <p><input type="text" ng-model="searchItem"><span class="glyphicon glyphicon-search"></span> Search </p>
            <div id="sales-list-table-div">
                <table cellpadding="0" cellspacing="0" class="table table-bordered">
                    <tr>
                        <th>SL</th>
                        <th>Date</th>
                        <th ng-click="changeSorting('person_id')">ID<i class="glyphicon" ng-class="getIcon('person_id')"></i></th>
                        <th ng-click="changeSorting('person_name')">Name<i class="glyphicon" ng-class="getIcon('person_name')"></i></th>
                        <th ng-click="changeSorting('mobile_no')">Total<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                        <th>Action</th>
                    </tr>
                    <tbody ng-repeat="sale in allSaleListFromBill2 | filter : searchItem  | orderBy:sort.active:sort.descending">
                    <tr ng-class-even="'banana'" ng-class-odd="'bee'">
                        <td>{{ $index+1}}</td>
                        <td>{{sale.bill_date}}</td>
                        <td>{{sale.bill2_number}}</td>
                        <td>{{sale.customer_name}}</td>
                        <td class="text-right">{{sale.total_bill_amount}}</td>
                        <td style="padding-left: 20px;" ng-click="showSaleBill2(sale)"><a href="#"><i class=" glyphicon glyphicon-eye-open"></i></a></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--/.Panel 2-->
        <!--Panel 3-->
        <div ng-show="isSet(3)">
        <style type="text/css">
            #print-customer-details-ul{
                list-style: none;
            }
            #print-sale-details-ul{
                list-style: none;
            }
        </style>

        <div id="show-bill-div">
            <div id="bill-part-one">
                <h3 class="text-center">Estimated</h3>
                <table class="table" id="show-bill-customer-table">
                    <tbody>
                     <tr>
                        <td >
                            <strong>Customer</strong><br>
                            <ul id="print-customer-details-ul">
                                <li>{{bill2Master.customer_billing_name}}</li>
                                <li>{{bill2Master.address1}}</li>
                                <li ng-show="bill2Master.address2.length>0">{{bill2Master.address2}}</li>
                                <li>{{bill2Master.city}}</li>
                                <li>{{bill2Master.district_name}}</li>
                                <li ng-show="bill2Master.post_office.length>0">Post: {{bill2Master.post_office}}</li>
                                <li ng-show="bill2Master.pin.length>0">PIN: {{bill2Master.pin}}</li>
                                <li ng-show="bill2Master.pan_no.length>0">PAN: {{bill2Master.pan_no}}</li>
                                <li ng-show="bill2Master.gst_number.length>0">GST: {{bill2Master.gst_number}}</li>
                                <li >{{bill2Master.mobile_no}}</li>
                                <li>{{bill2Master.phone_no}}</li>
                            </ul>
                        </td>
                        <td >
                            <ul id="print-sale-details-ul">
                                <li>Date</li>
                                <li>ED</li>
                            </ul>
                        </td>
                        <td style="padding-left: 0px">
                            <ul id="print-sale-details-ul">
                                <li>:&nbsp;{{(bill2Master.bill_date)}}</li>
                                <li>:&nbsp;{{(bill2Master.order_date) | date : "dd-MM-y" }}</li>
                            </ul>
                        </td>
                    </tr>


                    </tbody>

                </table>
                <!--                            table for sale details-->
                <style>

                </style>
                <table class="table" id="show-bill-details-table" >
                    <thead>
                    <tr>
                        <th class="wy-text-center">Sl</th>
                        <th class="wy-text-center" colspan="2">Item</th>
<!--                        <th disabled="yes" class="wy-text-center">HSN</th>-->
                        <th class="wy-text-center">Ql</th>
                        <th class="wy-text-center">Qnt</th>
                        <th class="wy-text-center">Gr Wt(gm)</th>
                        <th class="wy-text-center">Net Wt(gm)</th>
                        <th class="wy-text-center">Rate(gm)</th>
                        <th class="wy-text-center">Value</th>
                        <th class="wy-text-center">Mk Rate(gm)</th>
                        <th class="wy-text-center">Mk Chrg</th>
                        <th class="wy-text-center">Other Charge</th>
                        <th class="wy-text-center">Amt(<i class="fa fa-inr"></i>)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr ng-repeat="x in bill2Details">
                        <td class="text-right">{{$index+1}}</td>
                        <td class="text-left" colspan="2">{{x.product_name}}</td>
<!--                        <td disabled="yes" class="text-center">{{x.hsn_code}}</td>-->
                        <td class="text-center">{{x.product_quality}}</td>
                        <td class="text-right">{{x.quantity}}</td>
                        <td class="text-right">{{x.gross_weight | number:3}}</td>
                        <td class="text-right">{{x.net_weight | number:3}}</td>
                        <td class="text-right">{{x.rate | number:2}}</td>
                        <td class="text-right">{{x.sale_value | number:2}}</td>
                        <td class="text-right">{{x.making_charge_type=="2"?"NIL":x.making_rate}}</td>
                        <td class="text-right">{{x.making_charge | number:2}}</td>
                        <td class="text-right">{{x.other_charge | number:2}}</td>
                        <td class="text-right">{{x.total_amount | number:2}}</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="12" style="text-align: right;">Total</td>
                        <td style="text-align: right">{{showTableFooter.grandTotalAmount | number:2}}</td>
                    </tr>
                    <tr>
						<td colspan="9"></td>
                        <td colspan="3">Grand Total: </td>
                        <td style="text-align: right">{{showTableFooter.grandTotalAmount | number:2}}</td>
                    </tr>
                    <tr>
                        <td colspan="9"></td>
                        <td colspan="3">Rounded off: </td>
                        <td style="text-align: right;">{{bill2Master.roundedOff | number:2}}</td>
                    </tr>
                    <tr>
                        <td colspan="9"><b ng-if="(showTableFooter.grandTotalAmount + bill2Master.roundedOff)>0">In words {{(showTableFooter.grandTotalAmount + bill2Master.roundedOff) | AmountConvertToWord}}</b></td>
                        <td colspan="3">Bill Amount: </td>
                        <td style="text-align: right"><b>{{(showTableFooter.grandTotalAmount + bill2Master.roundedOff) | number:2}}</b></td>


                    </tr>
                    </tfoot>

                </table>
            </div>
            <hr>
            <div id="bill-part-two">
                <table class="table" id="show-bill-customer-table">
                    <tbody>
                    <tr>
                        <td >
                            <ul id="print-customer-details-ul">
                                <li>{{bill2Master.customer_billing_name}}</li>
                                <li>{{bill2Master.address1}}</li>
                                <li ng-show="bill2Master.address2.length>0">{{bill2Master.address2}}</li>
                                <li>{{bill2Master.city}}</li>
                                <li>{{bill2Master.district_name}}</li>
                                <li ng-show="bill2Master.post_office.length>0">Post: {{bill2Master.post_office}}</li>
                                <li ng-show="bill2Master.pin.length>0">PIN: {{bill2Master.pin}}</li>
                                <li ng-show="bill2Master.pan_no.length>0">PAN: {{bill2Master.pan_no}}</li>
                                <li ng-show="bill2Master.gst_number.length>0">GST: {{bill2Master.gst_number}}</li>
                                <li >{{bill2Master.mobile_no}}</li>
                                <li >{{bill2Master.phone_no}}</li>
                            </ul>
                        </td>
                        <td >
                            <ul id="print-sale-details-ul">
                                <li>Date</li>
                                <li>Time</li>
                                <li>ED</li>
                            </ul>
                        </td>
                        <td style="padding-left: 0px">
                            <ul id="print-sale-details-ul">
                                <li>:&nbsp;{{(bill2Master.bill_date)}}</li>
                                <li>:&nbsp;{{bill2Master.bill_time}}</li>
                                <li>:&nbsp;{{(bill2Master.order_date)}}</li>
                            </ul>
                        </td>
                    </tr>


                    </tbody>

                </table>
                <!--                            table for sale details-->
                <style>

                </style>
                <table class="table" id="show-bill-details-table" >
                    <thead>
                    <tr>
                        <th class="wy-text-center">Sl</th>
                        <th class="wy-text-center" colspan="2">Item</th>
<!--                        <th class="wy-text-center">HSN</th>-->
                        <th class="wy-text-center">Ql</th>
                        <th class="wy-text-center">Qnt</th>
                        <th class="wy-text-center">Gr Wt(gm)</th>
                        <th class="wy-text-center">Net Wt(gm)</th>
                        <th class="wy-text-center">Rate(gm)</th>
                        <th class="wy-text-center">Value</th>
                        <th class="wy-text-center">Mk Rate(gm)</th>
                        <th class="wy-text-center">Mk Chrg</th>
                        <th class="wy-text-center">Other Charge</th>
                        <th class="wy-text-center">Amt(<i class="fa fa-inr"></i>)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr ng-repeat="x in bill2Details">
                        <td class="text-right">{{$index+1}}</td>
                        <td class="text-left" colspan="2">{{x.product_name}}</td>
<!--                        <td class="text-center">{{x.hsn_code}}</td>-->
                        <td class="text-center">{{x.product_quality}}</td>
                        <td class="text-right">{{x.quantity}}</td>
                        <td class="text-right">{{x.gross_weight | number:3}}</td>
                        <td class="text-right">{{x.net_weight | number:3}}</td>
                        <td class="text-right">{{x.rate | number:2}}</td>
                        <td class="text-right">{{x.sale_value | number:2}}</td>
                        <td class="text-right">{{x.making_charge_type=="2"?"NIL":x.making_rate}}</td>
                        <td class="text-right">{{x.making_charge | number:2}}</td>
                        <td class="text-right">{{x.other_charge | number:2}}</td>
                        <td class="text-right">{{x.total_amount | number:2}}</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td ></td>
                        <td></td>
                        <td>Total</td>
                        <td class="text-right">{{showTableFooter.grandTotalAmount | number:2}}</td>
                    </tr>
                    <tr>
                        <td colspan="9"></td>
                        <td colspan="3">Rounded off: </td>
                        <td class="text-right">{{bill2Master.roundedOff | number:2}}</td>
                    </tr>
                    <tr>
                        <td colspan="9"><b ng-if="(showTableFooter.grandTotalAmount + bill2Master.roundedOff)>0">In words {{(showTableFooter.grandTotalAmount + bill2Master.roundedOff) | AmountConvertToWord}}</b></td>
                        <td colspan="3">Bill Amount: </td>
                        <td class="text-right"><b>{{(showTableFooter.grandTotalAmount + bill2Master.roundedOff) | number:2}}</b></td>


                    </tr>
                    </tfoot>

                </table>
            </div>
        </div>
            <p>
                <a href="#" ng-click="huiPrintDiv('show-bill-div','my_printing_style.css')" class="btn btn-info btn-lg no-print">
                    <span class="glyphicon glyphicon-print"></span> Print
                </a>
            </p>
<!--            <pre>bill2Master = {{bill2Master | json}}</pre>-->
<!--            <pre>bill2Details = {{bill2Details | json}}</pre>-->
        </div>
        <!--/.Panel 3-->
        </div>
        </div>

        </div>
        </div>

    <?php
    }
    function save_new_sale(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $sale_master=(object)$post_data['sale_master'];
        $sale_details_list=(object)$post_data['sale_details_list'];
        $result=$this->sale_model->insert_new_sale($sale_master,$sale_details_list);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    function save_new_bill2_sale(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $bill2_sale_master=(object)$post_data['sale_master'];
        $bill2_sale_details_list=(object)$post_data['sale_details_list'];
        $result=$this->sale_model->insert_new_sale_for_bill_two($bill2_sale_master,$bill2_sale_details_list);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    function get_bill_master_by_bill_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        //echo $post_data[];
        $result=$this->sale_model->select_bill_master_by_bill_id($post_data['bill_number'])->row();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function get_bill_details_by_bill_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        //echo $post_data[];
        $result=$this->sale_model->select_bill_details_by_bill_number($post_data['bill_number'])->result_array();
        $report_array['records']=$result;
        //echo json_encode($report_array);
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function get_all_sales(){
        //$post_data =json_decode(file_get_contents("php://input"), true);
        //echo $post_data[];
        $result=$this->sale_model->select_all_sales()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    function get_all_sales_from_bill2(){
        //$post_data =json_decode(file_get_contents("php://input"), true);
        //echo $post_data[];
        $result=$this->sale_model->select_all_sales_from_bill2(50)->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    function get_bill2_master_by_bill_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->sale_model->select_bill2_master_by_bill_id($post_data['bill_number'])->row();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    function get_bill2_details_by_bill_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        //echo $post_data[];
        $result=$this->sale_model->select_bill2_details_by_bill_number($post_data['bill_number'])->result_array();
        $report_array['records']=$result;
        //echo json_encode($report_array);
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    public function insert_new_product_from_sale(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->product_model->insert_new_product((object)$post_data['product']);
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

    function get_other_charges_particulars_by_bill_number(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        //echo $post_data[];
        $result=$this->sale_model->select_other_charges_particulars_by_bill_number($post_data['bill_number'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    function update_bill_one(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $bill_master=(object)$post_data['bill_master'];
        $bill_details=(object)$post_data['bill_details'];
        $result=$this->sale_model->update_bill_one_from_db($bill_master,$bill_details);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
}
?>