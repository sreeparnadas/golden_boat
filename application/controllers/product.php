<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('person');
        $this -> load -> model('product_model');
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

    function get_inforce_products(){
        $result=$this->product_model->select_inforce_products()->result_array();
        $test=array();
        $k[]=array('id'=>100,'name'=>'xyz');
        $k[]=array('id'=>101,'name'=>'pqr');
        foreach($result as $row){

            $row['units']=$this->db->query('select * from unit_to_product inner join units on unit_to_product.unit_id = units.unit_id where product_id=?',array($row['product_id']))->result_array();
            array_push($test,$row);
        }
        $report_array['records']=$test;
        echo json_encode($report_array);
    }
    public function angular_view_product(){
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
                    #product-div{
                        margin-top: 0px;
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
                    #productForm{
                        margin-top: 10px;
                     }
                     input.ng-invalid {
                        background-color: pink;
                    }
                    growl-notification{
                        color: green;
                    }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="product-div">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#" role="tab" ng-click="setTab(1)"><i class="fa fa-user" ></i> New Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(2)"><i class="fa fa-heart"></i> Product List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(3)"><i class="fa fa-envelope"></i>About Product</a>
                    </li>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="my-tab-1">
                            <span class="text-center"><growl-notification ng-if="successNotification">Product successfully added</growl-notification></span>
                            <span class="text-center"><growl-notification ng-if="reportArray.error_code===1062">Product already exist</growl-notification></span>
                            <form name="productForm" class="form-horizontal" id="productForm">
                                <div class="form-group">
                                    <label  class="control-label col-md-2">ID</label>
                                    <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                        <input name="productName" class="textinput textInput form-control capitalizeWord" type="text" ng-disabled="true"  ng-model="product.product_id" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label  class="control-label col-md-2">Product Name</label>
                                    <div class="controls col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <input name="productName" class="textinput textInput form-control" type="text"  required ng-model="product.product_name" ng-change="product.product_name=(product.product_name | capitalize)" />
                                    </div>
                                </div>
                                <!-- Mailing name-->
                                <div class="form-group">
                                    <label  class="control-label col-md-2">Product Group</label>
                                    <div class="controls col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <select ng-model="product.group_id" required>
                                            <option value="{{group.group_id}}"  ng-repeat="group in productGroup">  {{group.group_name}} </option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Email id-->
                                <div class="form-group">
                                    <label  class="control-label col-md-2">Quality</label>
                                    <div class="controls col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <select required
                                                ng-model="product.quality"
                                                ng-options="ql for ql in productQuality">
                                        </select>

                                    </div>
                                </div>

                                
                                <div class="form-group">
                                    <div class="controls col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    </div>
                                    <div class="controls col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    </div>
                                    <div class="controls col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <input type="button" ng-click="resetProduct()" value="Reset" />
                                        <input type="button" id="submit-product" ng-click="saveProduct(product)" value="Save" ng-disabled="productForm.$invalid" ng-show="!isUpdateable"/>
                                        <input type="button" id="update-vendor" ng-click="updateProductByProductId(product)" value="Update" ng-show="isUpdateable" ng-disabled="productForm.$pristine"/>
                                    </div>
                                </div>
                            </form>
<!--                            <pre>reportArray = {{reportArray | json}}</pre>-->
<!--                            <pre>vendor = {{vendor | json}}</pre>-->
<!--                            <pre>master = {{master | json}}</pre>-->
<!--                            <pre>database Report = {{reportArray | json}}</pre>-->
                        </div>
                    </div>
                    <!--/.Panel 1-->
                    <!--Panel 2-->
                    <div ng-show="isSet(2)">
                        <style type="text/css">
                            .bee{
                                background-color: #d9edf7;
                            }
                            .banana{
                                background-color: #c4e3f3;
                            }
                            #vendor-table-div table th{
                                background-color: #1b6d85;
                                color: #a6e1ec;
                                cursor: pointer;
                            }
                            a[ng-click]{
                                cursor: pointer;
                            }

                        </style>
                        <p><input type="text" ng-model="searchItem"><span class="glyphicon glyphicon-search"></span> Search </p>
                        <div id="vendor-table-div">
                            <table cellpadding="0" cellspacing="0" class="table table-bordered">
                                <tr>
                                    <th>SL></th>
                                    <th ng-click="changeSorting('product_id')">ID<i class="glyphicon" ng-class="getIcon('product_id')"></i></th>
                                    <th ng-click="changeSorting('product_name')">Name<i class="glyphicon" ng-class="getIcon('product_name')"></i></th>
                                    <th ng-click="changeSorting('group_name')">Group<i class="glyphicon" ng-class="getIcon('group_name')"></i></th>
                                    <th ng-click="changeSorting('quality')">Quality<i class="glyphicon" ng-class="getIcon('quality')"></i></th>
                                    <th>Edit</th>
                                </tr>
                                <tbody ng-repeat="product in productList | filter : searchItem  | orderBy:sort.active:sort.descending">
                                <tr ng-class-even="'banana'" ng-class-odd="'bee'">
                                    <td>{{ $index+1}}</td>
                                    <td>{{product.product_id}}</td>
                                    <td>{{product.product_name}}</td>
                                    <td>{{product.group_name}}</td>
                                    <td>{{product.quality}}</td>
                                    <td ng-click="updateProductFromTable(product)"><a href="#"><i class="glyphicon glyphicon-edit"></i></a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

<!--                        <pre>vendor List = {{vendorList | json}}</pre>-->
<!--                        <pre>vendor = {{vendor | json}}</pre>-->
<!--                        <pre>vendors = {{vendorList | json}}</pre>-->
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

    public function get_product_groups(){
        $result=$this->product_model->select_product_groups()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }


    public function insert_product(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->product_model->insert_new_product((object)$post_data['product']);
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

    function update_product_by_product_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->product_model->update_product_by_product_id((object)$post_data['product']);
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

    public function get_all_products(){
        $result=$this->product_model->select_inforce_product()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }


}
?>