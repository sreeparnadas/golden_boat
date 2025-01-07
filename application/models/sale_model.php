<?php
class sale_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }

    function select_inforce_products(){
       $sql="select * from product
        inner join product_group ON product_group.group_id = product.group_id
        where product.inforce=1";
       $result = $this->db->query($sql,array());
       return $result;
    }

    //Insert Data into bill_master and bill

    function insert_new_sale($saleMaster,$saleDetailToSave){
        $return_array=array();
        $financial_year=get_financial_year();
        try{
            //$this->db->query("START TRANSACTION");
            $this->db->trans_start();
            //insert into maxtable
            $sql="insert into maxtable (subject_name, current_value, financial_year,prefix)
            	values('bill_one',1,?,'SONA')
				on duplicate key UPDATE id=last_insert_id(id), current_value=current_value+1";
            $result = $this->db->query($sql, array($financial_year));
            if($result==FALSE){
                throw new Exception('Increasing Maxtable for bill_master');
            }

            //getting from maxtable
            $sql="select * from maxtable where id=last_insert_id()";
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting maxtable');
            }
            $bill_number=$result->row()->prefix.'-'.leading_zeroes($result->row()->current_value,4).'-'.$financial_year;
            $return_array['bill_number']=$bill_number;


            //        adding New Bill Master
            $sql="insert into bill_master (
               bill_number
              ,customer_id
              ,employee_id
              ,memo_no
              ,order_no
              ,order_date
              ,sale_date
              ,roundedOff
              ,transaction_mode
              ,card_number
              ,inforce
            ) VALUES (?,?,?,?,?,?,?,?,?,?,1)";
            $result=$this->db->query($sql,array(
                 $bill_number
                ,$saleMaster->person_id
                ,$this->session->userdata('person_id')
                ,$saleMaster->memo_no
                ,$saleMaster->order_no
                ,to_sql_date($saleMaster->order_date)
                ,$saleMaster->sale_date
                ,$saleMaster->roundedOff
                ,$saleMaster->transaction_mode
                ,$saleMaster->card_number
            ));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }
            // adding bill_master completed
            //adding bill details
            $sql="insert into bill_details (
               bill_details_id
              ,bill_number
              ,product_id
              ,product_quality
              ,quantity
              ,gross_weight
              ,net_weight
              ,rate
              ,making_charge_type
  			  ,making_rate
              ,other_charge
              ,other_charge_for
              ,sgst
              ,cgst
              ,igst
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            foreach($saleDetailToSave as $index=>$value){
                    $row=(object)$value;
                    $result = $this->db->query($sql, array(
                      $bill_number . '-' . ($index+1)
                    , $bill_number
                    ,$row->product_id
                    ,$row->product_quality
                    ,$row->quantity
                    ,$row->gross_weight
                    ,$row->net_weight
                    ,$row->rate
                    ,$row->making_charge_type
                    ,$row->making_rate
                    ,$row->other_charge
                    ,$row->other_charge_for
                    ,$row->sgstRate
                    ,$row->cgstRate
                    ,$row->igstRate
                    ));

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['bill_number']=$bill_number;
            $return_array['message']='Successfully recorded';
        }catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_opening',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'sale_model','insert_new_sale',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function

    function select_bill_master_by_bill_id($bill_number){
        $sql="select 
            bill_master.bill_number
            , bill_master.customer_id
            , bill_master.employee_id
            , bill_master.memo_no
            , bill_master.order_no
            , bill_master.order_date
            , bill_master.sale_date
            , bill_master.roundedOff
            , bill_master.transaction_mode
            , bill_master.card_number
            , bill_master.record_time
            , date_format(bill_master.record_time,'%d-%m-%Y') as bill_date
            , customer.person_name as customer_name
            , customer.billing_name as customer_billing_name
            , customer.mobile_no, customer.phone_no
            , customer.email_id, customer.gst_number
            , customer.address1
            ,customer.address2
            , customer.city
            , customer.post_office
            , customer.pin
            , customer.pan_no
            , customer.area
            , customer.state_id
            , employee.person_name as employee_name
            , districts.district_name
            from bill_master
            inner join person as customer on bill_master.customer_id = customer.person_id
            inner join person as employee on employee.person_id=bill_master.employee_id
            left join districts on customer.district_id = districts.district_id
             where bill_master.bill_number=?";
        $result = $this->db->query($sql,array($bill_number));
        if($result==null){
           $return_array['bill_number']='0000';
        }else{
            return $result;
        }
    }


    function select_bill_details_by_bill_number($bill_number){
                $sql="select bill_details_id
                ,bill_number
                ,product_quality
                ,product_group_id
                ,gst_rate
                ,quantity
                ,gross_weight
                ,net_weight
                ,rate
                ,sale_value
                ,making_charge_type
                ,making_rate,making_charge
                ,other_charge
                ,other_charge_for
                ,sgst
                ,cgst
                ,igst
                ,sgst_value
                ,cgst_value
                ,igst_value
                ,round(sale_value+making_charge+other_charge,2) as taxable_amount
                ,round(sale_value+making_charge+other_charge+sgst_value+cgst_value+igst_value,2) as total_amount
                ,hsn_code
                ,product_id
                ,product_name
                from(select 
                        bill_details.bill_details_id
                        , bill_details.bill_number
                        , bill_details.product_quality
                        , product_group.group_id as product_group_id
                        , product_group.gst_rate
                        , bill_details.quantity
                        , bill_details.gross_weight
                        , bill_details.net_weight
                        , bill_details.rate
                        , bill_details.net_weight*bill_details.rate as sale_value
                        , bill_details.making_charge_type
                        , bill_details.making_rate
                        ,if(bill_details.making_charge_type=2,bill_details.making_rate,bill_details.making_rate*bill_details.net_weight) as making_charge
                        , bill_details.other_charge
                        , bill_details.other_charge_for
                        , bill_details.sgst
                        , bill_details.cgst
                        , bill_details.igst
                        ,round(((bill_details.net_weight*bill_details.rate)+if(bill_details.making_charge_type=2,bill_details.making_rate,bill_details.making_rate*bill_details.net_weight)+bill_details.other_charge) * bill_details.sgst,2) as sgst_value
                        ,round(((bill_details.net_weight*bill_details.rate)+if(bill_details.making_charge_type=2,bill_details.making_rate,bill_details.making_rate*bill_details.net_weight)+bill_details.other_charge) * bill_details.cgst,2) as cgst_value
                       ,round(((bill_details.net_weight*bill_details.rate)+if(bill_details.making_charge_type=2,bill_details.making_rate,bill_details.making_rate*bill_details.net_weight)+bill_details.other_charge) * bill_details.igst,2) as igst_value
                       
                        , product_group.hsn_code
                        , product.product_id
                        , product.product_name
                        from bill_details 
                        inner join product ON product.product_id = bill_details.product_id
                        inner join product_group ON product_group.group_id = product.group_id
                        where bill_number=?) as table1";
        $result = $this->db->query($sql,array($bill_number));
        if($result==null){
            $return_array['success']='0';
        }else{
            return $result;
        }
    }
    function select_all_sales(){
        $sql="select total_bill_table.*,person.person_name as customer_name from (select 
        max(record_time) as bill_date
        ,sale_date
        ,bill_number
        ,customer_id
        ,sum(total_amount)+ roundedOff as total_bill_amount
        from (select bill_master.bill_number
        , DATE_FORMAT(bill_master.record_time,'%d/%m/%Y  %H:%i:%S') as record_time
        , bill_master.customer_id
        , bill_master.roundedOff
        , bill_master.sale_date
        , bill_details.rate
        , bill_details.net_weight
        , bill_details.making_charge_type
        , bill_details.making_rate
        , bill_details.other_charge
        , bill_details.sgst
        , bill_details.cgst
        , bill_details.igst
        , round((if(bill_details.making_charge_type=2,bill_details.making_rate,bill_details.making_rate*bill_details.net_weight) + (net_weight*rate)+other_charge)*(1+sgst+cgst+igst),2) as total_amount
        from bill_master
        inner join person ON person.person_id = bill_master.customer_id
        inner join bill_details on bill_details.bill_number = bill_master.bill_number) as sales_table
        group by bill_number, customer_id, roundedOff) as total_bill_table
        inner join person on person.person_id=total_bill_table.customer_id ";
        $result = $this->db->query($sql,array());
        if($result==null){
            return null;
        }else{
            return $result;
        }
    }


    //Bill 2
    function select_all_sales_from_bill2($limit=50){
        $sql=" select 
 total_bill_table.*,person.person_name as customer_name
 from (select 
 max(record_time) as bill_date
        ,bill2_number
        ,customer_id
        ,sum(total_amount)+ roundedOff as total_bill_amount
        from
  (select bill2_master.bill2_number
        , DATE_FORMAT(bill2_master.record_time,'%d/%m/%Y  %H:%i:%S') as record_time
        , bill2_master.customer_id
        , bill2_master.roundedOff
        , bill2_details.rate
        , bill2_details.net_weight
        , bill2_details.making_charge_type
        , bill2_details.making_rate
        , bill2_details.other_charge
        ,round(if(bill2_details.making_charge_type=2,(bill2_details.net_weight*bill2_details.rate+bill2_details.making_rate+bill2_details.other_charge),(bill2_details.net_weight*bill2_details.rate+bill2_details.net_weight* bill2_details.making_rate+bill2_details.other_charge)),2) as total_amount
        from bill2_master
        inner join person ON person.person_id = bill2_master.customer_id
        inner join bill2_details on bill2_details.bill2_number = bill2_master.bill2_number) as sales_table
         group by bill2_number, customer_id, roundedOff) as total_bill_table
         inner join person on person.person_id=total_bill_table.customer_id
        limit ? ";
        $result = $this->db->query($sql,array($limit));
        if($result==null){
            return null;
        }else{
            return $result;
        }
    }
    function insert_new_sale_for_bill_two($bill2SaleMaster,$bill2SaleDetailToSave){
        $return_array=array();
        $financial_year=get_financial_year();
        try{
            //$this->db->query("START TRANSACTION");
            $this->db->trans_start();
            //insert into maxtable
            $sql="insert into maxtable (subject_name, current_value, financial_year,prefix)
            	values('bill_two',1,?,'SONA')
				on duplicate key UPDATE id=last_insert_id(id), current_value=current_value+1";
            $result = $this->db->query($sql, array($financial_year));
            if($result==FALSE){
                throw new Exception('Increasing Maxtable for bill_master');
            }

            //getting from maxtable
            $sql="select * from maxtable where id=last_insert_id()";
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting maxtable');
            }
            $bill_number=$result->row()->prefix.'-'.leading_zeroes($result->row()->current_value,4).'-'.$financial_year;
            $return_array['bill_number']=$bill_number;


            //        adding New Bill Master
            $sql="insert into bill2_master (
               bill2_number
              ,customer_id
              ,employee_id
              ,memo_no
              ,order_no
              ,roundedOff
              ,order_date
              ,sales_date
              ,inforce
            ) VALUES (?,?,?,?,?,?,?,?,1)";
            $result=$this->db->query($sql,array(
                $bill_number
            ,$bill2SaleMaster->person_id
            ,$this->session->userdata('person_id')
            ,$bill2SaleMaster->memo_no
            ,$bill2SaleMaster->order_no
            ,$bill2SaleMaster->roundedOff
            ,to_sql_date($bill2SaleMaster->order_date)
            ,to_sql_date($bill2SaleMaster->sales_date)
            ));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }
            // adding bill_master completed
            //adding bill details
            $sql="insert into bill2_details (
               bill2_details_id
              ,bill2_number
              ,product_id
              ,product_quality
              ,quantity
              ,gross_weight
              ,net_weight
              ,rate
              ,making_charge_type
  			  ,making_rate
              ,other_charge
              ,other_charge_for

            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

            foreach($bill2SaleDetailToSave as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                    $bill_number . '-' . ($index+1)
                , $bill_number
                ,$row->product_id
                ,$row->product_quality
                ,$row->quantity
                ,$row->gross_weight
                ,$row->net_weight
                ,$row->rate
                ,$row->making_charge_type
                ,$row->making_rate
                ,$row->other_charge
                ,$row->other_charge_for
                ));

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['bill_number']=$bill_number;
            $return_array['message']='Successfully recorded';
        }catch(mysqli_sql_exception $e){
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

    function select_bill2_master_by_bill_id($bill_number){
        $sql="select 
            bill2_master.bill2_number
            , bill2_master.customer_id
            , bill2_master.employee_id
            , bill2_master.memo_no
            , bill2_master.order_no
            , bill2_master.roundedOff
            , bill2_master.order_date
            , date_format(bill2_master.record_time,'%d-%m-%Y') as bill_date
           ,TIME_FORMAT(bill2_master.record_time, '%r') as bill_time
            , customer.person_name as customer_name
            , customer.billing_name as customer_billing_name
            , customer.mobile_no, customer.phone_no
            , customer.email_id, customer.gst_number
            , customer.address1
            ,customer.address2
            , customer.city
            , customer.post_office
            , customer.pin
            , customer.pan_no
            , customer.area
            , employee.person_name as employee_name
            , districts.district_name
            from bill2_master
            inner join person as customer on bill2_master.customer_id = customer.person_id
            inner join person as employee on employee.person_id=bill2_master.employee_id
            left join districts on customer.district_id = districts.district_id
             where bill2_master.bill2_number=?";
        $result = $this->db->query($sql,array($bill_number));
        if($result==null){
            $return_array['bill_number']='0000';
        }else{
            return $result;
        }
    }

    function select_bill2_details_by_bill_number($bill_number){
        $sql="select bill2_details_id
                ,bill2_number
                ,product_quality
                ,quantity
                ,gross_weight
                ,net_weight
                ,rate
                ,sale_value
                ,making_charge_type
                ,making_rate,making_charge
                ,other_charge
                ,round(sale_value+making_charge+other_charge,2) as total_amount
                ,hsn_code
                ,product_name
                from(select 
                        bill2_details.bill2_details_id
                        , bill2_details.bill2_number
                        , bill2_details.product_quality
                        , bill2_details.quantity
                        , bill2_details.gross_weight
                        , bill2_details.net_weight
                        , bill2_details.rate
                        , bill2_details.net_weight*bill2_details.rate as sale_value
                        , bill2_details.making_charge_type
                        , bill2_details.making_rate
                        ,if(bill2_details.making_charge_type=2,bill2_details.making_rate,bill2_details.making_rate*bill2_details.net_weight) as making_charge
                        , bill2_details.other_charge                       
                        , product_group.hsn_code
                        , product.product_name
                        from bill2_details 
                        inner join product ON product.product_id = bill2_details.product_id
                        inner join product_group ON product_group.group_id = product.group_id
                        where bill2_number=?) as table1";
        $result = $this->db->query($sql,array($bill_number));
        if($result==null){
            $return_array['success']='0';
        }else{
            return $result;
        }
    }

    function select_other_charges_particulars_by_bill_number($bill_number){
        $sql="SELECT SUBSTRING_INDEX(bill_details_id,'-',-1) as serial_key,other_charge_for
from bill_details where not isnull(other_charge_for) and bill_number=? and length(other_charge_for)>0";
        $result = $this->db->query($sql,array($bill_number));
        if($result==null){
            $return_array['success']='0';
        }else{
            return $result;
        }

    }

    function update_bill_one_from_db($billMaster,$billDetailsToSave){
        $return_array=array();
        try{
            //$this->db->query("START TRANSACTION");
            $this->db->trans_start();
            $bill_number = $billMaster->bill_number;

            $deleteDetailsSql = "delete from bill_details where bill_number like ?";

            $result = $this->db->query($deleteDetailsSql, array($bill_number));

            if($result==FALSE){
                throw new Exception('Deleting bill old details');
            }

            $insertNewDetailsSql = "insert into bill_details (
                bill_details_id
               ,bill_number
               ,product_id
               ,product_quality
               ,quantity
               ,gross_weight
               ,net_weight
               ,rate
               ,making_charge_type
                 ,making_rate
               ,other_charge
               ,other_charge_for
               ,sgst
               ,cgst
               ,igst
             ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            foreach($billDetailsToSave as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($insertNewDetailsSql, array(
                  $bill_number . '-' . ($index+1)
                , $bill_number
                ,$row->product_id
                ,$row->product_quality
                ,$row->quantity
                ,$row->gross_weight
                ,$row->net_weight
                ,$row->rate
                ,$row->making_charge_type
                ,$row->making_rate
                ,$row->other_charge
                ,''
                ,$row->sgst
                ,$row->cgst
                ,$row->igst
                ));

                if($result==FALSE){
                    throw new Exception('error adding bill_details');
                }

                $updateBillMasterSql = "update bill_master set roundedOff = ?, updated_at = ? where bill_number = ?";

                $result = $this->db->query($updateBillMasterSql, array($billMaster->roundedOff, date("Y-m-d h:i:s", time()) ,$bill_number));

                if($result==FALSE){
                    throw new Exception('Updating bill master');
                }
            }
            $this->db->trans_complete();

            $return_array['success']=1;
            $return_array['bill_number'] = $bill_number;
            $return_array['message']='Successfully updated';

        }
        catch(mysqli_sql_exception $e){
            
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'sale_model','update_bill_one_from_db',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");

        }
        catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'sale_model','update_bill_one_from_db',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
        }

        return (object)$return_array;
    }


}//End of Model Class


?>