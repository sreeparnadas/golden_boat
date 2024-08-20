<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('person');
        $this -> load -> model('loan_model');
        $this -> is_logged_in();
    }
    function is_logged_in() {
		$is_logged_in = $this -> session -> userdata('is_logged_in');
		if (!isset($is_logged_in) || $is_logged_in != 1) {
			echo 'you have no permission to use developer area'. '<a href="">Login</a>';
			die();
		}
	}


    public function angular_view_loan(){
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
                    #customer-details{
                        list-style: none;
                    }




    </style>

        <div class="row" ng-show="pswCheckingDiv">

            <form class="form-inline">
                <div class="controls col-lg-4 col-md-4 col-sm-4 col-xs-4"></div>
                <div class="form-group">
                    <label for="pwd">Password:</label>
                    <input type="password" class="form-control" ng-model="staff_password">
                </div>

                <button type="submit" class="btn btn-default" ng-click="checkStaffAuthentication(staff_password)">Submit</button>
            </form>
        </div>

            <div class="container-fluid" ng-show="staffAuthenticated">
                <div class="row">
                    <button ng-click="staffAuthenticated=false;pswCheckingDiv=true" class="btn btn-warning">Hide</button>
                     <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="customer-div">
                         <!-- Nav tabs -->
                            <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link " data-toggle="tab" href="#" role="tab" ng-click="setTab(1)" ng-style="tab==1 && selectedTab"><span class="glyphicon glyphicon-shopping-cart"></span>&nbspStart Inward</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(2)" ng-style="tab==2 && selectedTab"><span class="glyphicon glyphicon-file"></span>Start Outward</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(3)" ng-style="tab==3 && selectedTab"><span class="glyphicon glyphicon-file"></span>Loan List</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click=""><span class="glyphicon glyphicon-file"></span>Show Loan Details</a>
                                </li>
                            </ul>
                        <!-- Tab panels -->
                             <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="my-tab-1">
                            <form name="loanForm" class="form-horizontal" id="saleForm">
                                <div class="row">
                                        <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="col-lg-6col-md-6 col-sm-6 col-xs-6" style="background-color: papayawhip">
                                            <div class="form-group">
                                                <label  class="control-label  col-lg-2 col-md-2 col-sm-2 col-xs-2 ">Customer</label>
                                                <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                                    <input type="text" class="textinput textInput form-control" ng-change="searchCustomerByKey()" ng-model="loanInward.customerSearchKey"/>
                                                </div>
                                                <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                    <select
                                                        ng-model="loanInward.customer"
                                                        ng-options="customer as customer.person_name for customer in customerListByKey">
                                                    </select>
                                                </div>
                                            </div>

                                            <ul id="customer-details" ng-show="loanInward.customer">
                                                <li>{{loanInward.customer.person_id}}</li>
                                                <li>{{loanInward.customer.billing_name}}</li>
                                                <li ng-show="loanInward.customer.mobile_no.length>0">{{loanInward.customer.mobile_no}}</li>
                                                <li ng-show="loanInward.customer.address1.length>0">{{loanInward.customer.address1}}</li>
                                                <li>{{loanInward.customer.city}}, {{loanInward.customer.post_office}}</li>
                                                <li>Dist. - {{loanInward.customer.district_name}}, {{loanInward.customer.state_name}}</li>
                                                <li>GST: {{loanInward.customer.gst_number}}</li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" ng-show="loanInward.customer" style="background-color: lavender">

                                            <div class="form-group">
                                                <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Date</label>
                                                <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                    <input type="text" class="textinput textInput form-control capitalizeWord" ng-pattern="/\d\d/\d\d/\d\d\d\d/" ng-model="loanInward.loan_date"  required/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                    <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Inward amount</label>
                                                    <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                        <input type="text" class="textinput textInput form-control capitalizeWord" numeric-value ng-model="loanInward.inward_amount" required=""/>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">particulars</label>
                                                    <div class="controls col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                                        <input type="text" class="textinput textInput form-control capitalizeWord" ng-model="loanInward.particulars" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls col-lg-9 col-md-9 col-sm-9 col-xs-9"></div>
                                                    <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                        <input type="button" class="btn pull-right" id="save-sale-data" ng-click="saveLoanInward(loanInward)" value="Save" ng-disabled="loanForm.$invalid || btnSubmitDisableInward"/>
                                                        <input type="button" class="btn pull-left" id="reset-sale-data" ng-click="resetLoanInward()" value="Reset"/>
                                                    </div>
                                                </div>

                                            <div class="controls col-lg-4 col-md-4 col-sm-4 col-xs-4" ng-show="inwardSubmitStatus">
                                                <span class="text-center text-success">Inward successfully added</span>
                                            </div>
<!--                                          <pre>loanInward= {{loanInward | json}}</pre>-->


                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row" style="background-color: cornsilk;">

<!--                                <pre>loanOutward = {{loanOutward | json}}</pre>->
<!--                                <pre>Database Report = {{reportArray | json}}</pre>-->

                            </div>
                        </div>
                    </div>
                    <!--/.Panel 1-->
                    <!--Panel 2-->
                    <div ng-show="isSet(2)">
                        <div id="my-tab-2">
                            <form name="loanOutwardForm" class="form-horizontal" id="loan-outward-form">
                                <div class="row">
                                    <div class="row col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="col-lg-6col-md-6 col-sm-6 col-xs-6" style="background-color: papayawhip">
                                            <div class="form-group">
                                                <label  class="control-label  col-lg-2 col-md-2 col-sm-2 col-xs-2 ">Customer</label>
                                                <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                                    <input type="text" class="textinput textInput form-control" ng-change="searchCustomerByKey()" ng-model="loanInward.customerSearchKey"/>
                                                </div>
                                                <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                    <select
                                                        ng-model="loanOutward.customer"
                                                        ng-options="customer as customer.person_name for customer in customerListByKey">
                                                    </select>
                                                </div>
                                            </div>

                                            <ul id="customer-details" ng-show="loanOutward.customer">
                                                <li>Name: {{loanOutward.customer.person_id}}</li>
                                                <li>Name: {{loanOutward.customer.billing_name}}</li>
                                                <li ng-show="loanInward.customer.mobile_no.length>0">{{loanOutward.customer.mobile_no}}</li>
                                                <li>{{loanOutward.customer.address1}}</li>
                                                <li>{{loanOutward.customer.city}}, {{loanInward.customer.post_office}}</li>
                                                <li>Dist. - {{loanOutward.customer.district_name}}, {{loanOutward.customer.state_name}}</li>
                                                <li>GST: {{loanOutward.customer.gst_number}}</li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" ng-show="loanOutward.customer" style="background-color: lavender">

                                            <div class="form-group">
                                                <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Date</label>
                                                <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                    <input type="text" class="textinput textInput form-control capitalizeWord" ng-pattern="^[0-9]{2}[\/][0-9]{2}[\/][0-9]{4}$" ng-model="loanInward.loan_date" date-format="yyyy-MM-dd" required/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">Outward amount</label>
                                                <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                    <input type="text" class="textinput textInput form-control capitalizeWord" numeric-value ng-model="loanOutward.outward_amount" required=""/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label  class="control-label col-lg-3 col-md-3 col-sm-3 col-xs-3">particulars</label>
                                                <div class="controls col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                                    <input type="text" class="textinput textInput form-control capitalizeWord" ng-model="loanOutward.particulars" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="controls col-lg-9 col-md-9 col-sm-9 col-xs-9"></div>
                                                <div class="controls col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                    <input type="button" class="btn pull-right" id="save-outward-data" ng-click="saveLoanOutward(loanOutward)" value="Save" ng-disabled="loanOutwardForm.$invalid || btnSubmitDisableOutward"/>
                                                    <input type="button" class="btn pull-left" id="reset-sale-data" ng-click="resetLoanOutward()" value="Reset"/>
                                                </div>
                                            </div>

                                            <div class="controls col-lg-4 col-md-4 col-sm-4 col-xs-4" ng-show="outwardSubmitStatus">
                                                <span class="text-center text-success">Outward successfully added</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row" style="background-color: cornsilk;">

<!--                                <pre>inwardReportArray = {{inwardReportArray | json}}</pre>-->
<!--                                <pre>Database Report = {{reportArray | json}}</pre>-->

                            </div>
                        </div>
                    </div>
                    <!--/.Panel 2-->
                    <!--Panel 3-->
                    <div ng-show="isSet(3)">
                        <style type="text/css">

                        </style>
                        <p><input type="text" ng-model="searchItem"><span class="glyphicon glyphicon-search"></span> Search </p>
                        <div id="sales-list-table-div">
                            <table cellpadding="0" cellspacing="0" class="table table-bordered">
                                <tr>
                                    <th ng-click="changeSorting('person_id')">Sl<i class="glyphicon" ng-class="getIcon('person_id')"></i></th>
                                    <th ng-click="changeSorting('person_id')">ID<i class="glyphicon" ng-class="getIcon('person_id')"></i></th>
                                    <th ng-click="changeSorting('person_name')">Name<i class="glyphicon" ng-class="getIcon('person_name')"></i></th>
                                    <th ng-click="changeSorting('mobile_no')">Outward<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                    <th ng-click="changeSorting('mobile_no')">Inward<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                    <th ng-click="changeSorting('mobile_no')">Due<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                    <th ng-click="changeSorting('mobile_no')">Details<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                </tr>
                                <tbody ng-repeat="loan in loanDetailsList | filter : searchItem  | orderBy:sort.active:sort.descending">
                                <tr ng-class-even="'banana'" ng-class-odd="'bee'">
                                    <td>{{ $index+1}}</td>
                                    <td>{{loan.person_id}}</td>
                                    <td>{{loan.person_name}}</td>
                                    <td>{{loan.outward}}</td>
                                    <td>{{loan.inward}}</td>
                                    <td class="text-right">{{loan.due}}</td>
                                    <td style="padding-left: 20px;" ng-click="showLoanDetails(loan)"><a href="#"><i class="glyphicon glyphicon-info-sign"></i></a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
<!--                        <pre>loanDetailsList= {{loanDetailsList | json}}</pre>-->
                    </div>
                    <!--/.Panel 3-->
                    <!--/.Panel 4-->
                    <div ng-show="isSet(4)">
                        <style type="text/css">

                        </style>
                        <p><input type="text" ng-model="searchItem"><span class="glyphicon glyphicon-search"></span> Search </p>
                        <ul id="customer-details" ng-show="customerLoanDetails">
                            <li>ID {{getPreviousTotal(customerLoanDetails.length)}}</li>
                            <li>Name {{customerLoanDetails[0].person_id}}</li>
                            <li>Name {{customerLoanDetails[0].person_name}}</li>
                        </ul>
                        <div id="sales-list-table-div">
                            <table cellpadding="0" cellspacing="0" class="table table-bordered" ng-init="due = 0">
                                <tr>
                                    <th ng-click="changeSorting('person_id')">Date<i class="glyphicon" ng-class="getIcon('person_id')"></i></th>
                                    <th ng-click="changeSorting('person_id')">Particulars<i class="glyphicon" ng-class="getIcon('person_id')"></i></th>
                                    <th ng-click="changeSorting('mobile_no')">Outward<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                    <th ng-click="changeSorting('mobile_no')">Inward<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                    <th ng-click="changeSorting('mobile_no')">Due<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                    <th ng-click="changeSorting('mobile_no')">Delete<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                </tr>
                                <tbody ng-repeat="loan in customerLoanDetails | filter : searchItem  | orderBy:sort.active:sort.descending">
                                <tr ng-class-even="'banana'" ng-class-odd="'bee'">
                                    <td ng-init="key=$index">{{ loan.loan_date}}</td>
                                    <td>{{ loan.particulars}}</td>
                                    <td class="text-right">{{loan.outward_amount}}</td>
                                    <td class="text-right">{{loan.inward_amount}}</td>
                                    <td ng-style="numberStyle(getPreviousTotal(loan))">{{getPreviousTotal(loan) | number:2}}</td>
                                    <td style="padding-left: 20px;" ng-click="deleteLoanDetails(loan)"><a href="#" class="glyphicon glyphicon-trash"></a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
<!--                        <pre>loanDetailsAfterDelete= {{loanDetailsAfterDelete | json}}</pre>-->
<!--                        <pre>customerLoanDetails= {{customerLoanDetails | json}}</pre>-->
                    </div>
                </div>
                    </div>

                </div>
            </div>

        <?php
    }

    function add_loan_inward_action(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->loan_model->insert_new_loan_inward((object)$post_data['loanIn']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
        //echo json_encode($result);
    }

    function add_loan_outward_action(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->loan_model->insert_new_loan_outward((object)$post_data['loanOut']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
        //echo json_encode($result);
    }

    function get_loan_details_table(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->loan_model->select_all_loan_customer_details((object)$post_data)->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function get_loan_details_by_customer_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->loan_model->select_loan_details_by_customer_id($post_data['person_id'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function delete_row_from_loan_table(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->loan_model->delete_loan_details_by_loan_id($post_data['loan_id']);
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

}
?>