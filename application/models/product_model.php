<?php
class product_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }

    function select_product_groups(){
        $sql="select * from product_group where inforce=1 order by group_name";
        $result=$this->db->query($sql);
        if($result!=null){
            return $result;
        }
        else{
            return null;
        }
    }//end of function select_product_group_names

    function insert_new_product($product){
        $return_array=array();
        try{
            $this->db->trans_start();
            $sql="insert into product (
               product_id
              ,group_id
              ,product_name
              ,quality
              ,inforce
           ) VALUES (null,?,?,?,1)";
            $result=$this->db->query($sql,array(
                $product->group_id
            ,$product->product_name
            ,$product->quality
            ));
            $return_array['dberror']=$this->db->error();
            if($result==FALSE){
                throw new Exception('error adding purchase master');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $product=$this->db->query("select * from product where product_id= LAST_INSERT_ID()")->row();
            $return_array['product_id']=$product->product_id;
            $return_array['product_name']=$product->product_name;
            $return_array['group_id']=$product->group_id;
            $return_array['quality']=$product->quality;
            $return_array['message']='Successfully recorded';
        }
        catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_opening',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_opening',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $return_array['error_code']=$err->code;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function

    function update_product_by_product_id($product){
        $return_array=array();
        try{
            $this->db->trans_start();
            $sql="update product set group_id=?, product_name=?, quality=? where product_id=?";
            $result=$this->db->query($sql,array(
                $product->group_id
                ,$product->product_name
                ,$product->quality
                ,$product->product_id
            ));
            $return_array['dberror']=$this->db->error();
            if($result==FALSE){
                throw new Exception('error adding purchase master');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['product_id']=$product->product_id;
            $return_array['product_name']=$product->product_name;
            $return_array['group_id']=$product->group_id;
            $return_array['quality']=$product->quality;
            $return_array['message']='Successfully recorded';
        }
        catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_opening',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_opening',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $return_array['error_code']=$err->code;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function

    function select_inforce_product(){
        $sql="select * from product
inner join product_group ON product_group.group_id = product.group_id where product.inforce=1
";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }
}//final

?>