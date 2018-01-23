<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewTransporter_payment extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * view to add/edit transporter payment
        * link to view account is provided
        * payment can be greater than their due amount
        * Payment mode not required
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "transporter_payment"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Transporter Payment");
        
        $mode = JRequest::getVar("m");
        
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        $this->transporter_id = $transporter_id;
        $invoices_id = JRequest::getVar("invoices_id");
        
        
       // $query = "select id from `#__sales_invoice` where transporter_id = ".$transporter_id;
//        $db->setQuery($query);
//        $transporter_details = $db->loadObject();
//        $this->transporter_details = $transporter_details;
        
        if($transporter_id)
        {
            $this->return = base64_encode("index.php?option=com_amittrading&view=transporter_account&transporter_id=" . $transporter_id);
        }
        else
        {
            //$this->return = base64_encode("index.php?option=com_amittrading&view=transporter_payment");
            $this->return = base64_encode("index.php?option=com_hr&view=dashboard");
        }
        
        $query = "select * from `#__transporters` order by transporter_name";        
        $db->setQuery($query);
        $transporters = $db->loadObjectList();
        $this->transporters = $transporters;
        
        //print_r($transporters);exit;
        
        if($mode == 'e')
        {
            $payment_id = intval(JRequest::getVar("payment_id"));
            
            $query = "select p.*, t.transporter_name, t.account_balance from `#__transporter_payments` p inner join `#__transporters` t on p.transporter_id=t.id where p.id=" . $payment_id; 
            $db->setQuery($query);
            $payment = $db->loadObject();
            
            //$query = "select * from `#__sales_invoice` where transporter_id = ".$transporter_id;
            
            /*$query = "select sum(transportation_amount - transportation_amount_paid) from `#__purchase_invoice` where transporter_id=" . $transporter_id . " and transporter_payment_mode=" . CREDIT . " and (transportation_amount - transportation_amount_paid) > 0";
            $db->setQuery($query);
            $amount_due = floatval($db->loadResult());*/
            $amount_due = floatval($payment->account_balance) + floatval($payment->total_amount);
            
            $this->payment = $payment;
            $this->amount_due = $amount_due;
            $this->payment_id = $payment_id; 
            
            parent::display("edit");
        }
        else
        {
            $sales_invoice_details = array();
            if(count($invoices_id) > 0)
            { 
                    
                foreach($invoices_id as $key => $invoice_id)
                {
                    //$query = "select p.product_name,soi.* from `#__sales_order_items` soi inner join `#__products` p on p.id=soi.product_id where soi.id=" . $item_id;
                    $query = "select * from `#__transporter_bills` where id=" . $invoice_id;
                    $db->setQuery($query);
                    $sales_invoice_details[$key] = $db->loadObject();
                }
           }
           $this->sales_invoice_detail = $sales_invoice_details;
           parent::display($tpl);
        }
    } 
}
?>