<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewCustomer_payment extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * view to add/edit customer payment
        * link to view account is provided
        * payment can be greater than their due amount
        * Payment mode can be cash, cheque
        * Only user of "users_allowed_backdate_payments" settings can make payment on back dates
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "customer_payment"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Receive Payment");
        
        $mode = JRequest::getVar("m");
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        $this->customer_id = $customer_id;
        
        if($customer_id)
        {
            $this->return = base64_encode("index.php?option=com_amittrading&view=customer_account&customer_id=" . $customer_id);
        }
        else
        {
            $this->return = base64_encode("index.php?option=com_amittrading&view=customer_payment");
        }
        
        $query = "select `value_numeric` from `#__settings` where `key`='cash_sale_customer_id'";
        $db->setQuery($query);
        $cash_sale_customer_id = intval($db->loadResult());
        $this->cash_sale_customer_id = $cash_sale_customer_id;
        
        $query = "select cu.*, c.city from `#__customers` cu inner join `#__cities` c on cu.city_id=c.id where cu.id<>" . $cash_sale_customer_id . " and cu.account_status=" . AC_ACTIVE . " order by cu.customer_name";        
        $db->setQuery($query);
        $customers = $db->loadObjectList();
        $this->customers = $customers;
        
        $query = "select * from `#__banks` order by bank_name";        
        $db->setQuery($query);
        $banks = $db->loadObjectList();
        $this->banks = $banks;
        
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
            
            $query = "select p.*, cu.customer_name, cu.customer_address, cu.contact_no, c.city, cu.account_balance from `#__payments` p inner join `#__customers` cu on p.party_id=cu.id inner join `#__cities` c on cu.city_id=c.id where p.id=" . $payment_id;
            $db->setQuery($query);
            $payment = $db->loadObject();
            
            /*$query = "select sum(bill_amount - amount_paid) from `#__sales_invoice` where customer_id=" . $customer_id . " and status=" . UNPAID . " and (bill_amount - amount_paid) > 0";
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