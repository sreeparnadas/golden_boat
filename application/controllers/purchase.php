<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('person');
        $this -> load -> model('purchase_model');
        $this -> load -> model('product_model');
        $this -> is_logged_in();
    }
    function is_logged_in() {
		$is_logged_in = $this -> session -> userdata('is_logged_in');
		if (!isset($is_logged_in) || $is_logged_in != 1) {
			echo 'you have no permission to use developer area'. '<a href="">Login</a>';
			die();
		}
	}



    public function angular_view_purchase(){
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
                    #custoner-details{
                        list-style-type: none;
                    }
                    growl-notification{
                        color: red;
                    }


    </style>
    <div class="container-fluid">
        <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="vendor-div">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#" role="tab" ng-click="setTab(1)"><span class="glyphicon glyphicon-shopping-cart"></span>&nbspNew Purchase</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(2)"><span class="glyphicon glyphicon-file"></span>Show Purchases</a>
                    </li>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="my-tab-1">
                            <form name="purchaseForm" class="form-horizontal" id="purchaseForm">
                                <div class="row">
                                    <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-lg-6col-md-6 col-sm-6 col-xs-6" style="background-color: papayawhip">
                                        <div class="form-group">
                                            <label  class="control-label  col-lg-2 col-md-2 col-sm-2 col-xs-2 ">Supplier</label>
                                            <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                                <select
                                                    ng-model="purchaseMaster.vendor"
                                                    ng-options="vendor as vendor.person_name for vendor in vendorList" ng-change="setGstFactor()">
                                                </select>
                                            </div>
                                        </div>

                                        <ul id="custoner-details">
                                            <li>Name: {{purchaseMaster.vendor.mailing_name}}</li>
                                            <li>{{purchaseMaster.vendor.address1}}</li>
                                            <li>{{purchaseMaster.vendor.city}}, {{purchaseMaster.vendor.post_office}}</li>
                                            <li>Dist. - {{purchaseMaster.vendor.district_name}}, {{purchaseMaster.vendor.state_name}}</li>
                                            <li>GST: {{purchaseMaster.vendor.gst_number}}</li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="background-color: lavender">

                                            <div class="form-group">

                                                <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Invoice number</label>
                                                <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                    <input type="text" class="textinput textInput form-control capitalizeWord" ng-model="purchaseMaster.invoice_no" />
                                                </div>
                                            </div>
                                            <div class="form-group">

                                                <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Date</label>
                                                <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                    <input type="date" id=FromDate" name="purchaseDate"  ng-model="purchaseMaster.purchase_date" date-format="yyyy-MM-dd"/>
                                                </div>
                                            </div>
                                        <div class="form-group">
                                            <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">E-WayBill No</label>
                                            <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                <input type="text" class="textinput textInput form-control capitalizeWord" ng-model="purchaseMaster.ewaybillNo" />
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                </div>
                                <div class="row" id="purchase-details-div" ng-show="purchaseMaster.vendor">
                                    <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="table-responsive" style="background-color: #b2dba1;">
                                             <table class="table" id="purchase-table">
                                                <thead>
                                                    <tr>
                                                        <th>Product Name</th>
                                                        <th>Quantity</th>
                                                        <th>Rate</th>
                                                        <th>Discount</th>
                                                        <th>Amount</th>
                                                        <th>SGST &nbsp;{{purchaseDetails.sgstRate*100 | number:0}}%</th>
                                                        <th>CGST &nbsp;{{purchaseDetails.cgstRate*100 | number:0}}%</th>
                                                        <th>IGST &nbsp;{{purchaseDetails.igstRate*100 | number:0}}%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                <!--                                            <select ng-model="purchaseDetails.product_id" ng-change="setUnit()" id="product-name">-->
                <!--                                                <option value="{{product.product_id}}"  ng-repeat="product in prductList" unit-id="{{product.unit_id}}" unit-name="{{product.unit_name}}">  {{product.product_name}} </option>-->
                <!--                                            </select>-->
                                                            <select
                                                                ng-model="purchaseDetails.product"
                                                                ng-options="product as product.product_name for product in prductList" ng-change="gstRateChangeOfProduct();setGst()">
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="controls col-md-3" id="purchase-quantity" name="purchaseQuantity" ng-keyup="setAmount()" ng-model="purchaseDetails.quantity" ng-change="setGst()">
                                                            &nbsp;<a href="#" ng-bind="purchaseDetails.unit==null?purchaseDetails.product.unit_name:purchaseDetails.unit.unit_name" ng-click="showUnit =! showUnit" ng-init="showUnit=false"></a>
                                                            {{purchaseDetails.product.units.length}}
                                                            {{purchaseDetails.product.units.length>1}} and {{showUnit}}
                                                            <div class="form-group" ng-show="purchaseDetails.product.units.length>1 && showUnit">
                                                                <label  class="control-label col-lg-1 col-md-1 col-sm-1 col-xs-1" style="color: darkgreen;">Alternate Unit</label>
                                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                                    <select class="form-control"
                                                                        ng-change="showUnit = !showUnit"
                                                                        ng-model="purchaseDetails.unit"
                                                                        ng-options="unit as unit.unit_name for unit in purchaseDetails.product.units ">
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input  type="text" class="controls col-md-4" id="purchase-rate" name="purchaseRate" ng-keyup="setAmount()" ng-change="setGst()" ng-model="purchaseDetails.rate">&nbsp;&nbsp;Per&nbsp;{{purchaseDetails.unit==null?purchaseDetails.product.unit_name:purchaseDetails.unit.unit_name}}
                                                        </td>
                                                        <td>
                                                            <input class="controls col-md-4 coll-sm-4 col-xs-4 col-lg-4" type="text" id="purchase-discount" name="purchaseDiscount" ng-init="0.00" step="0.01" ng-model="purchaseDetails.discount">
                                                        </td>
                                                        <td>
                                                            <span   id="purchase-amount" name="purchaseAmount"  ng-bind="(purchaseDetails.amount | number)"></span>
                                                        </td>
                                                        <td>
                                                            <span   id="purchase-sgst" name="purchaseSgst"   ng-bind="(purchaseDetails.sgst | number:2)"></span>
                                                        </td>
                                                        <td>
                                                            <span   id="purchase-cgst" name="purchaseCgst"  ng-bind="(purchaseDetails.cgst | number:2)"></span>
                                                        </td>
                                                        <td>
                                                            <span   id="purchase-igst" name="purchaseIgst"   ng-bind="(purchaseDetails.igst | number:2)"></span>
                                                        </td>

                                                        <td></td>
                                                        <td><input type="button" value="Add" ng-click="addPurchaseDetailsData(purchaseDetails)"></td>
                                                    </tr>
                                                </tbody>
                                                 <tfoot>
                                                 <tr>
                                                     <td colspan="8" class="text-right"><growl-notification ng-if="showNotification">!! Duplicate entry</growl-notification></td>
                                                 </tr>
                                                 </tfoot>

                                            </table>
                                        </div>
                                        <div class="table-responsive" style="background-color: #b8b8b8;">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Rate</th>
                                                <th>Discount</th>
                                                <th>amt.disc</th>
                                                <th>SGST</th>
                                                <th>CGST</th>
                                                <th>IGST</th>
                                                <th>Amount</th>
                                            </tr>
                                            </thead>
                                            <tbody ng-repeat="p in purchaseDetailsDataList">
                                            <tr>
                                                <td>{{$index+1}}</td>
                                                <td>{{p.product.product_name}}</td>
                                                <td>{{p.quantity}}&nbsp;{{p.product.unit_name}}</td>
                                                <td><i class="fa fa-inr"></i> {{p.rate | number}} Per {{p.product.unit_name}}</td>
                                                <td>{{p.discount}}</td>
                                                <td>{{getDiscount()}}</td>
                                                <td>{{p.sgst}}</td>
                                                <td>{{p.cgst}}</td>
                                                <td>{{p.igst}}</td>
                                                <td class="text-right"><i class="fa fa-inr"></i> {{p.amount | number}}</td>
                                                <td> <a href="#" data-ng-click="removeRow($index)"><span class="glyphicon glyphicon-remove"></span> Remove </a></td>
                                            </tr>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td>Total:</td>
                                                <td colspan="9" class="text-right">{{totalPurchaseAmount | number}}</td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                        <div class="form-group">
                                            <div class="controls col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <input type="button" class="btn pull-right" id="update-purchas-data" ng-click="savePurchase(purchaseMaster,purchaseDetailsDataList)" value="Save"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row" style="background-color: cornsilk;">
                                {{purchaseDetails.product.units[0].unit_name}}
                                <pre>purchaseMaster = {{purchaseMaster | json}}</pre>
                                <pre>purchaseDetails = {{purchaseDetails | json}}</pre>
<!--                                <pre>prductList = {{prductList | json}}</pre>-->
                                <pre>purchaseDetailsDataList = {{purchaseDetailsDataList | json}}</pre>
                                <pre>vendor = {{purchaseMaster.vendor | json}}</pre>
<!--                                <pre>vendorList = {{vendorList | json}}</pre>-->
                            </div>
                        </div>
                    </div>
                    <!--/.Panel 1-->
                    <!--Panel 2-->
                    <div ng-show="isSet(2)">
                    </div>
                    <!--/.Panel 2-->
                    <!--Panel 3-->
                    <div ng-show="isSet(3)">
                        This is our help area
                    </div>
                    <!--/.Panel 3-->
                </div>
            </div>

        </div>
    </div>

        <?php
    }
    function save_new_purchase(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        print_r($post_data['purchase_master']);
        print_r($post_data['purchse_details']);
        //print_r($post_data['purchase_data']);
       //$result=$this->purchase_model->insert_new_purchase((object)$post_data['purchase_data']);
        //$report_array['records']=$result;
       // echo json_encode($report_array);

    }


}
?>