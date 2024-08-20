<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('person');
    }
    public function index()
    {
        $this->load->view('public/index_top');
        $this->load->view('public/index_header');
        $this->load->view('public/index_main');
        $this->load->view('public/index_login');
        //$this->load->view('index_slider');
       // $this->load->view('public/index_feature');
        //$this->load->view('public/index_gallery');
        //$this->load->view('public/index_parallax_window');
        //$this->load->view('public/index_pricing');
        //$this->load->view('public/index_our_team');
        //$this->load->view('public/index_contact');
        $this->load->view('public/index_end');
    }
    public function get_persons(){
        $result=$this->person->select_person();
        $persons=array();
        $person=array();
        $sl=0;
        foreach($result->result() as $k=>$v){
            $person['SL']=++$sl;
            $person['Name']=$v->person_name;
            $person['Mobile']=$v->mobile_no;
            $person['Sex']=$v->sex;
            $persons[]=$person;
        }
        $report_array['records']=$persons;
        echo json_encode($report_array);
    }

    public function angular_view_london(){
        $this->load->view('angular_views/london');
    }
    public function angular_view_kolkata(){
        $this->load->view('angular_views/kolkata');
    }
    public function angular_view_paris(){
        $this->load->view('angular_views/paris');
    }
    public function angular_view_main(){
        $this->load->view('angular_views/main');
    }

    public function validate_credential(){

        $result=$this->person->get_person_by_authentication();
        $newdata = array(
            'person_id'  => $result->person_id,
            'person_name'     => $result->person_name,
            'person_cat_id'     => $result->person_cat_id,
            'is_logged_in' => $result->is_logged_in
        );
        $this->session->set_userdata($newdata);
        echo json_encode($result);
       // echo $result->person_cat_id;
        //when password does not match
        /*$return_array['person_name']=$result->person_name;
        if($result->person_cat_id==0){
           $this->load->view('menus/index_header_login_error',$return_array);
        }
        if($result->person_cat_id==3){
           $this->load->view('menus/index_header_staff',$return_array);
        }
        if($result->person_cat_id==1){
           $this->load->view('menus/index_header_admin',$return_array);
        }*/
    }
    public function show_headers(){
        if($_GET['person_cat_id']==3){
            $this->load->view('menus/index_header_staff');
        }
        if($_GET['person_cat_id']==1){
            $this->load->view('menus/index_header_admin');
        }
    }
    public function test(){
//        print_r($_GET['mas']);
        $master=$_GET['mas'];
        echo $master['name'];
    }
}
?>