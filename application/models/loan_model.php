<?php
class loan_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }

    //Insert Data into bill_master and bill

    function insert_new_loan_inward($loanIn){
        $return_array=array();
        try{
            $this->db->trans_start();
            $sql="insert into loan_table (
				   loan_id
				  ,person_id
				  ,particulars
				  ,outward_amount
				  ,inward_amount
				  ,loan_date
				) VALUES (null,?,?,0,?,?)";
            $result=$this->db->query($sql,array(
                $loanIn->person_id
                , $loanIn->particulars
                , $loanIn->inward_amount
                , to_sql_date($loanIn->loan_date)
            ));
            $return_array['dberror']=$this->db->error();
            if($result==FALSE){
                throw new Exception('error adding loan outward customer');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $loan=$this->db->query("select * from loan_table where loan_id= LAST_INSERT_ID()")->row();
            $return_array['person_id']=$loanIn->person_id;
            $return_array['particulars']=$loanIn->particulars;
            $return_array['inward_amount']=$loanIn->inward_amount;
            $return_array['loan_date']=$loanIn->loan_date;
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
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function


    function insert_new_loan_outward($loanOut){
        $return_array=array();
        try{
            $this->db->trans_start();
            $sql="insert into loan_table (
				   loan_id
				  ,person_id
				  ,particulars
				  ,outward_amount
				  ,inward_amount
				  ,loan_date
				) VALUES (null,?,?,?,0,?)";
            $result=$this->db->query($sql,array(
                $loanOut->person_id
            , $loanOut->particulars
            , $loanOut->outward_amount
            , to_sql_date($loanOut->loan_date)
            ));
            $return_array['dberror']=$this->db->error();
            if($result==FALSE){
                throw new Exception('error adding loan outward customer');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $loan=$this->db->query("select * from loan_table where loan_id= LAST_INSERT_ID()")->row();
            $return_array['person_id']=$loanOut->person_id;
            $return_array['particulars']=$loanOut->particulars;
            $return_array['outward_amount']=$loanOut->outward_amount;
            $return_array['loan_date']=$loanOut->loan_date;
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
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function


    function select_all_loan_customer_details(){
        $sql="select person.person_id
		,person.person_name
					, sum(loan_table.outward_amount) as outward
					, sum(loan_table.inward_amount) as inward
					, (sum(loan_table.outward_amount)-sum(loan_table.inward_amount)) as due
					from loan_table
					inner join person ON person.person_id = loan_table.person_id
          where loan_table.deleted=0
          group by loan_table.person_id,person.person_name order by person.person_name";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }

    function select_loan_details_by_customer_id($person_id){
        $sql="
            select loan_table.loan_id,loan_table.loan_date,loan_table.deleted
            ,loan_table.person_id
		,person.person_name
		, loan_table.outward_amount
		, loan_table.inward_amount
		, loan_table.particulars
		 from loan_table
		inner join person ON person.person_id = loan_table.person_id
		where loan_table.person_id=? and loan_table.deleted=0 order by loan_table.record_time ";
        $result=$this->db->query($sql,array($person_id));
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }
    function delete_loan_details_by_loan_id($loan_id){
        $sql="delete from loan_table WHERE loan_id = ?";
        $result=$this->db->query($sql,array($loan_id));
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }



}//final

?>