<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewTransporter_account_statement extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * this is transporter account statement
        * it includes payment, purchase invoice
        * highlighted rows are for payments
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Transporter Account Statement");
        
        $transporter_id = intval(JRequest::getVar("transporter_id")); 
        $this->transporter_id = $transporter_id;
        
        $from_date = JRequest::getVar('from_date');
        $to_date = JRequest::getVar('to_date');
        
       // echo $to_date; exit;
        
        if($from_date != "" && $to_date != "")
        { 
            $from_date = date("Y-m-d", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));
        }
        else
        {
            $from_date = date("Y-m-d", strtotime("-6 months"));
            $to_date = date("Y-m-d");
        }
        
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        $condition1 = "(date between '" . $from_date . "' and '" . $to_date . "')";
        $condition2 = "(payment_date between '" . $from_date . "' and '" . $to_date . "')";
        
        $query1 = "(select id,date, bill_no, (vehicle_rate - add_cash - diesel_total_amount) bill_amount_paid, vehicle_rate vehicle_rate, '' loading_amount,'bill' type from `#__sales_invoice` where transporter_id = " . $transporter_id . ")"; 
        $query2 = "(select id,date, bill_no, loading_amount bill_amount_paid, '' vehicle_rate, loading_amount loading_amount,'bill' type  from `#__sales_invoice` where loading_transporter_id = " .$transporter_id. ")"; 
        $query3 = "(select id,payment_date date,'' bill_no, total_amount bill_amount_paid,'' vehicle_rate,'' loading_amount,  'payment' type from `#__transporter_payments` where transporter_id=" . $transporter_id . " and " . $condition2.")";
        
        $query = $query1 . " union " . $query2  . " union " . $query3 . " order by date";
        $db->setQuery($query);
        $sales_details = $db->loadObjectList();
        
        foreach($sales_details as $key=>$details)
        {
            if($details->type == 'payment')
            {
                $query = "select concat('(Bill No. : ', s.bill_no, ', Amount : ', tp.amount, ')') from `#__transporter_payment_items` tp inner join `#__sales_invoice` s on tp.invoice_id=s.id where tp.transporter_payment_id=" . intval($details->id);
                $db->setQuery($query);
                //echo $query;exit;
                $sales_details[$key]->particulars = "Payment " . implode(",", $db->loadColumn());
            }
            
           // if($details->type == 'bill')
//            {
//                $query = "select concat('(Bill No. : ', s.bill_no, ')') from `#__transporter_payment_items` tp inner join `#__sales_invoice` s on tp.invoice_id=s.id where tp.transporter_payment_id=" . intval($details->id);
                //echo $query;exit;
//                $db->setQuery($query);
//                $sales_details[$key]->particulars = "Bill No. " . implode(",", $db->loadColumn());
//            }
        }
        
         $this->sales_details = $sales_details; 
        
        /*Opening Balance Calculation*/
      //  $query = "select sum(total_amount) from `#__transporter_payments` where transporter_id=" . $transporter_id . " and payment_date < '" . $from_date . "'";
      
        $query = "select sum(total_amount) from `#__transporter_payments` where transporter_id=" . $transporter_id;
        $db->setQuery($query);
        $paid_amount = floatval($db->loadResult());    
        
        $query = "select opening_balance from `#__transporters` where id=" . $transporter_id;
        $db->setQuery($query);
        $amount_to_be_paid = floatval($db->loadResult());
        
        foreach($sales_details as $sales_detail)
        {
            if($sales_detail->vehicle_rate > 0)
            {
               $query = "select sum(vehicle_rate) from `#__sales_invoice` where transporter_id=" . $transporter_id . " and date < '" . $from_date . "'";                                                         
           }
           else
           {
               $query = "select sum(loading_amount) from `#__sales_invoice` where transporter_id=" . $transporter_id . " and date < '" . $from_date . "'";                                                         
           } 
        $db->setQuery($query);
        $amount_to_be_paid += floatval($db->loadResult());
        }
        
        $opening_balance = floatval($amount_to_be_paid - $paid_amount);
        $this->opening_balance = $opening_balance; 
        /*Opening Balance Calculation*/
        
        parent::display($tpl);
    } 
}
?>