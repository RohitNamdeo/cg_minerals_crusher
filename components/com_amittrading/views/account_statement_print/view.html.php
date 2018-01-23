<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewAccount_statement_print extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * single view to print account statement of supplier & customer ($type)
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Account Statement Print");
        
        $type = JRequest::getVar("type");
        $party_id = intval(JRequest::getVar("party_id"));
        $from_date = date("Y-m-d", strtotime(JRequest::getVar('from_date')));
        $to_date = date("Y-m-d", strtotime(JRequest::getVar('to_date')));
        
        $this->type = $type;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        $condition1 = "(bill_date between '" . $from_date . "' and '" . $to_date . "')";
        $condition2 = "(payment_date between '" . $from_date . "' and '" . $to_date . "')";
        $condition3 = "(challan_date between '" . $from_date . "' and '" . $to_date . "')";
        
        if($type == 'c')
        {
            $query1 = "select id, bill_date date, concat('Bill No. : ', bill_no) particulars, bill_amount amount, 'bill' type from `#__sales_invoice` where customer_id=" . $party_id . " and " . $condition1;
            $query2 = "select id, payment_date date, 'Payment' particulars, total_amount amount, 'payment' type from `#__payments` where party_id=" . $party_id . " and payment_type=" . CUSTOMER_PAYMENT . " and " . $condition2;
            $query3 = "select id, bill_date date, concat('Sales Return (Bill No. : ', bill_no, ')') particulars, bill_amount amount, 'return' type from `#__sales_returns` where customer_id=" . $party_id . " and " . $condition1;
            $query4 = "select `customer_name` from `#__customers` where id=" . $party_id;
        }
        else if($type == 's')
        {
            $query1 = "select id, bill_date date, concat('Purchase (Bill No. : ', bill_no, ')') particulars, bill_amount amount, 'bill' type from `#__purchase_invoice` where supplier_id=" . $party_id . " and " . $condition1;
            $query2 = "select id, payment_date date, 'Payment' particulars, total_amount amount, 'payment' type from `#__payments` where party_id=" . $party_id . " and payment_type=" . SUPPLIER_PAYMENT . " and " . $condition2;
            $query3 = "select id, challan_date date, concat('Purchase Return (Challan No. : ', challan_no, ')') particulars, challan_amount amount, 'return' type from `#__purchase_returns` where supplier_id=" . $party_id . " and " . $condition3;
            $query4 = "select `supplier_name` from `#__suppliers` where id=" . $party_id;
        }
        else if($type == 't')
        {
            $query1 = "select id, bill_date date, concat('Bill No. : ', bill_no) particulars, transportation_amount amount, 'bill' type from `#__purchase_invoice` where transporter_id=" . $party_id . " and " . $condition1;
            $query2 = "select id, payment_date date, 'Payment' particulars, total_amount amount, 'payment' type from `#__transporter_payments` where transporter_id=" . $party_id . " and " . $condition2;
            $query4 = "select `transporter` from `#__transporters` where id=" . $party_id;
        }
        
        $query = $query1 . " union " . $query2  . ($type != 't' ? " union " . $query3 : "") . " order by date";
        $db->setQuery($query);
        $account_details = $db->loadObjectList();
        
        /*foreach($account_details as $key=>$details)
        {
            if($details->type == 'payment')
            {
                if($type == 't')
                {
                    $query = "select concat('(Bill No. : ', p.bill_no, ', Amount : ', tp.amount, ')') from `#__transporter_payment_items` tp inner join `#__purchase_invoice` p on tp.invoice_id=p.id where tp.transporter_payment_id=" . intval($details->id);
                }
                else if($type == 's')
                {
                    $query = "select concat('(Bill No. : ', p.bill_no, ', Amount : ', tp.amount, ')') from `#__payment_items` tp inner join `#__purchase_invoice` p on tp.invoice_id=p.id where tp.payment_id=" . intval($details->id);
                }
                else if($type == 'c')
                {
                    $query = "select concat('(Bill No. : ', s.bill_no, ', Amount : ', tp.amount, ')') from `#__payment_items` tp inner join `#__sales_invoice` s on tp.invoice_id=s.id where tp.payment_id=" . intval($details->id);
                }
                
                $db->setQuery($query);
                $account_details[$key]->particulars = "Payment " . implode(",", $db->loadColumn());
            }
            else if($details->type == 'return')
            {
                if($type == 's')
                {
                    $query = "select concat('(Bill No. : ', p.bill_no, ', Amount : ', pra.amount, ')') from `#__purchase_return_adjustment_items` pra inner join `#__purchase_invoice` p on pra.invoice_id=p.id where pra.purchase_return_id=" . intval($details->id);
                }
                else if($type == 'c')
                {
                    $query = "select concat('(Bill No. : ', s.bill_no, ', Amount : ', sra.amount, ')') from `#__sales_return_adjustment_items` sra inner join `#__sales_invoice` s on sra.invoice_id=s.id where sra.sale_return_id=" . intval($details->id);
                }
                
                $db->setQuery($query);
                $particulars = implode(",", $db->loadColumn());
                
                $account_details[$key]->particulars .= $particulars;
            }
        }*/
        
        $this->account_details = $account_details;
        
        $db->setQuery($query4);
        $this->party_name = $db->loadResult();
        
        /*Opening Balance Calculation*/
        
        if($type == 'c')
        {
            $query1 = "select sum(total_amount) from `#__payments` where party_id=" . $party_id . " and payment_type=" . CUSTOMER_PAYMENT . " and payment_date < '" . $from_date . "'";
            $query2 = "select sum(bill_amount) from `#__sales_invoice` where customer_id=" . $party_id . " and bill_date < '" . $from_date . "'";
        }
        else if($type == 's')
        {
            $query1 = "select sum(total_amount) from `#__payments` where party_id=" . $party_id . " and payment_type=" . SUPPLIER_PAYMENT . " and payment_date < '" . $from_date . "'";
            $query2 = "select sum(bill_amount) from `#__purchase_invoice` where supplier_id=" . $party_id . " and bill_date < '" . $from_date . "'";
        }
        else if($type == 't')
        {
            $query1 = "select sum(total_amount) from `#__transporter_payments` where transporter_id=" . $party_id . " and payment_date < '" . $from_date . "'";
            $query2 = "select sum(transportation_amount) from `#__purchase_invoice` where transporter_id=" . $party_id . " and bill_date < '" . $from_date . "'";
        }
        
        $db->setQuery($query1);
        $paid_amount = floatval($db->loadResult());
        
        $db->setQuery($query2);
        $amount_to_be_paid = floatval($db->loadResult());
        
        $opening_balance = floatval($amount_to_be_paid - $paid_amount);
        $this->opening_balance = $opening_balance;
        /*Opening Balance Calculation*/
        
        parent::display($tpl);
    } 
}
?>