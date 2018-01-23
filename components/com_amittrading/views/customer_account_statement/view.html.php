<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewCustomer_account_statement extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * this is customer account statement
        * it includes payment, sales invoice and sales return
        * highlighted rows are for payments
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Customer Account Statement");
        
        $customer_id = intval(JRequest::getVar("customer_id"));  
        $this->customer_id = $customer_id;
        
        $from_date = JRequest::getVar('from_date');
        $to_date = JRequest::getVar('to_date');
        
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
        
        /*$query1 = "select id, bill_date date, concat('Bill No. : ', bill_no) particulars, bill_amount amount, 'bill' type from `#__sales_invoice` where customer_id=" . $customer_id . " and " . $condition1;
        $query2 = "select id, payment_date date, 'Payment' particulars, total_amount amount, 'payment' type from `#__payments` where party_id=" . $customer_id . " and payment_type=" . CUSTOMER_PAYMENT . " and " . $condition2;
        $query3 = "select id, bill_date date, 'Sales Return' particulars, bill_amount amount, 'return' type from `#__sales_returns` where customer_id=" . $customer_id . " and " . $condition1;*/
        //$query = $query1 . " union " . $query2  . " union " . $query3 . " order by date";
        
        $query1 = "select id, date, concat('Bill No. : ', bill_no) particulars, total_amount amount, 'bill' type from `#__sales_invoice` where customer_id=" . $customer_id . " and " . $condition1;
        $query2 = "select id, payment_date date, 'Payment' particulars, total_amount amount, 'payment' type from `#__payments` where party_id=" . $customer_id . " and payment_type=" . CUSTOMER_PAYMENT . " and " . $condition2;
        //$query3 = "select id, bill_date date, 'Sales Return' particulars, bill_amount amount, 'return' type from `#__sales_returns` where customer_id=" . $customer_id . " and " . $condition1;
        
        $query = $query1 . " union " . $query2  . " order by date";
        $db->setQuery($query);
        $account_details = $db->loadObjectList();
        //print_r($account_details);exit;
        
        foreach($account_details as $key=>$details)
        {
            if($details->type == 'payment')
            {
                $query = "select concat('(Bill No. : ', s.bill_no, ', Amount : ', tp.amount, ')') from `#__payment_items` tp inner join `#__sales_invoice` s on tp.invoice_id=s.id where tp.payment_id=" . intval($details->id);
                $db->setQuery($query);
                $account_details[$key]->particulars = "Payment " . implode(",", $db->loadColumn());
            }
            /*if($details->type == 'return')
            {
                $query = "select concat('(Bill No. : ', s.bill_no, ', Amount : ', sra.amount, ')') from `#__sales_return_adjustment_items` sra inner join `#__sales_invoice` s on sra.invoice_id=s.id where sra.sale_return_id=" . intval($details->id);
                $db->setQuery($query);
                $particulars = implode(",", $db->loadColumn());
                
                $account_details[$key]->particulars .= $particulars;
            }*/
        }
        $this->account_details = $account_details;
        
        //print_r($account_details);exit;
        
        /*Opening Balance Calculation*/
        $query = "select sum(total_amount) from `#__payments` where party_id=" . $customer_id . " and payment_type=" . CUSTOMER_PAYMENT . " and payment_date < '" . $from_date . "'";
        $db->setQuery($query);
        $paid_amount = floatval($db->loadResult());
        
        /*$query = "select sum(total_amount) from `#__sales_returns` where customer_id=" . $customer_id . " and bill_date < '" . $from_date . "'";
        $db->setQuery($query);
        $paid_amount += floatval($db->loadResult());*/
        
        $query = "select opening_balance from `#__customers` where id=" . $customer_id;
        $db->setQuery($query);
        $amount_to_be_paid = floatval($db->loadResult());
        
        //print_r($amount_to_be_paid);exit; 
        
        $query = "select sum(total_amount) from `#__sales_invoice` where customer_id=" . $customer_id . " and date < '" . $from_date . "'";
        $db->setQuery($query);
        $amount_to_be_paid += floatval($db->loadResult());
        
        //print_r($amount_to_be_paid);exit;
        
        $opening_balance = floatval($amount_to_be_paid - $paid_amount);
        $this->opening_balance = $opening_balance;
        
        /*Opening Balance Calculation*/
        
        parent::display($tpl);
    } 
}
?>