<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewSupplier_payment extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * view to add/edit supplier payment
        * link to view account is provided
        * payment can be greater than their due amount
        * Payment mode can be cash, cheque
        * Cheque payment amount is validated by bank account balance
        * Only user of "users_allowed_backdate_payments" settings can make payment on back dates
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "supplier_payment"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Make Payment");
        
        $mode = JRequest::getVar("m");
        
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        $this->supplier_id = $supplier_id;
        
        if($supplier_id)
        {
            $this->return = base64_encode("index.php?option=com_amittrading&view=supplier_account&supplier_id=" . $supplier_id);
        }
        else
        {
            //$this->return = base64_encode("index.php?option=com_amittrading&view=supplier_payment");
            $this->return = base64_encode("index.php?option=com_hr&view=dashboard");
        }
        
        $query = "select s.*, c.city from `#__suppliers` s inner join `#__cities` c on s.city_id=c.id order by s.supplier_name";        
        $db->setQuery($query);
        $suppliers = $db->loadObjectList();
        $this->suppliers = $suppliers;
        
        $query = "select * from `#__bank_accounts` where account_status=" . AC_ACTIVE . " order by account_name";        
        $db->setQuery($query);
        $bank_accounts = $db->loadObjectList();
        $this->bank_accounts = $bank_accounts;
        
        $query = "select `value_string` from `#__settings` where `key`='users_allowed_backdate_payments'";
        $db->setQuery($query);
        $users_allowed_backdate_payments = explode(",", $db->loadResult());

        $user_id = intval(JFactory::getUser()->id);
        $allow_backdate_payment = false;
        if(in_array($user_id, $users_allowed_backdate_payments))
        {
            $allow_backdate_payment = true;
        }
        $this->allow_backdate_payment = $allow_backdate_payment;
        
        if($mode == 'e')
        {
            $payment_id = intval(JRequest::getVar("payment_id"));
            
            $query = "select p.*, s.supplier_name, s.supplier_address, s.contact_no, c.city, s.account_balance from `#__payments` p inner join `#__suppliers` s on p.party_id=s.id inner join `#__cities` c on s.city_id=c.id where p.id=" . $payment_id;
            $db->setQuery($query);
            $payment = $db->loadObject();
            
            /*$query = "select sum(bill_amount - amount_paid) from `#__purchase_invoice` where supplier_id=" . $supplier_id . " and status=" . UNPAID . " and (bill_amount - amount_paid) > 0";
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
            parent::display($tpl);
        }
    } 
}
?>