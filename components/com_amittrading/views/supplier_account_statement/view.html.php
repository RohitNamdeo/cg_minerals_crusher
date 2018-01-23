<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewSupplier_account_statement extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * this is supplier account statement
        * it includes payment, purchase invoice and purchase return
        * highlighted rows are for payments
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Supplier Account Statement");
        
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        $this->supplier_id = $supplier_id;
        
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
        
        
        $condition1 = "(bill_date between '" . $from_date . "' and '" . $to_date . "')";
        $condition2 = "(payment_date between '" . $from_date . "' and '" . $to_date . "')";
        $condition3 = "(challan_date between '" . $from_date . "' and '" . $to_date . "')";
        
        /*$query1 = "select id, bill_date date, concat('Purchase (Bill No. : ', bill_no, ')') particulars, bill_amount amount, 'bill' type from `#__purchase_invoice` where supplier_id=" . $supplier_id . " and " . $condition1;
        $query2 = "select id, payment_date date, '' particulars, total_amount amount, 'payment' type from `#__payments` where party_id=" . $supplier_id . " and payment_type=" . SUPPLIER_PAYMENT . " and " . $condition2;
        $query3 = "select id, challan_date date, concat('Purchase Return (Challan No. : ', challan_no, ')') particulars, challan_amount amount, 'return' type from `#__purchase_returns` where supplier_id=" . $supplier_id . " and " . $condition3;*/
        
        $query1 = "select id, bill_date date, loading_charges, waiverage_charges, concat('Purchase (Bill No. : ', bill_no, ')') particulars, total_amount amount, 'bill' type from `#__purchase` where supplier_id=" . $supplier_id . " and " . $condition1;
        $query2 = "select id, payment_date date, '' loading_charges, '' waiverage_charges, '' particulars, total_amount amount, 'payment' type from `#__payments` where party_id=" . $supplier_id . " and payment_type=" . SUPPLIER_PAYMENT . " and " . $condition2;
        //$query3 = "select id, bill_date date from `#__purchase` where id=" . $supplier_id ;
        //$query3 = "select id, challan_date date, concat('Purchase Return (Challan No. : ', challan_no, ')') particulars, challan_amount amount, 'return' type from `#__purchase_returns` where supplier_id=" . $supplier_id . " and " . $condition3;
        
        //$query = $query1 . " union " . $query2  . " union " . $query3 . " order by date";
        
        $query = $query1 . " union " . $query2  . " order by date";
        $db->setQuery($query);
        $account_details = $db->loadObjectList();
        
       //print_r($account_details);exit;
        
        foreach($account_details as $key=>$details)
        {
            if($details->type == 'payment')
            {
                $query = "select concat('(Bill No. : ', p.bill_no, ', Amount : ', tp.amount, ')') from `#__payment_items` tp inner join `#__purchase` p on tp.invoice_id=p.id where tp.payment_id=" . intval($details->id);
                $db->setQuery($query);
                $account_details[$key]->particulars = "Payment " . implode(",", $db->loadColumn());
            }
            if($details->type == 'return')
            {
                $query = "select concat('(Bill No. : ', p.bill_no, ', Amount : ', pra.amount, ')') from `#__purchase_return_adjustment_items` pra inner join `#__purchase` p on pra.invoice_id=p.id where pra.purchase_return_id=" . intval($details->id);
                $db->setQuery($query);
                $particulars = implode(",", $db->loadColumn());
                
                $account_details[$key]->particulars .= $particulars;
            }
        }
        $this->account_details = $account_details;
        
        //print_r($account_details);exit; 
        
       /* foreach($account_details as $key=>$details)
        {
            if($details->type == 'payment')
            {
                $query = "select concat('(Bill No. : ', p.bill_no, ', Amount : ', tp.amount, ')') from `#__payment_items` tp inner join `#__purchase_invoice` p on tp.invoice_id=p.id where tp.payment_id=" . intval($details->id);
                $db->setQuery($query);
                $account_details[$key]->particulars = "Payment " . implode(",", $db->loadColumn());
            }
            if($details->type == 'return')
            {
                $query = "select concat('(Bill No. : ', p.bill_no, ', Amount : ', pra.amount, ')') from `#__purchase_return_adjustment_items` pra inner join `#__purchase_invoice` p on pra.invoice_id=p.id where pra.purchase_return_id=" . intval($details->id);
                $db->setQuery($query);
                $particulars = implode(",", $db->loadColumn());
                
                $account_details[$key]->particulars .= $particulars;
            }
        }
        $this->account_details = $account_details;*/
        
        /*Opening Balance Calculation*/
        
        $query = "select sum(total_amount) from `#__payments` where party_id=" . $supplier_id . " and payment_type=" . SUPPLIER_PAYMENT . " and payment_date < '" . $from_date . "'";
        $db->setQuery($query);
        $paid_amount = floatval($db->loadResult());
        
       /* $query = "select sum(challan_amount) from `#__purchase_returns` where supplier_id=" . $supplier_id . " and challan_date < '" . $from_date . "'";
        $db->setQuery($query);
        $paid_amount += floatval($db->loadResult());*/
        
        $query = "select opening_balance from `#__suppliers` where id=" . $supplier_id;
        $db->setQuery($query);
        $amount_to_be_paid = floatval($db->loadResult());
        
        $query = "select sum(total_amount) from `#__purchase` where supplier_id=" . $supplier_id . " and bill_date < '" . $from_date . "'";
        $db->setQuery($query);
        $amount_to_be_paid += floatval($db->loadResult());
        
        $opening_balance = floatval($amount_to_be_paid - $paid_amount);
        $this->opening_balance = $opening_balance;
        
        /*Opening Balance Calculation*/
        
        parent::display($tpl);
    } 
}
?>