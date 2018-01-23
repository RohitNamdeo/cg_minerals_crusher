<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewCustomer_payment_print extends JViewLegacy
{
    public function display($tpl = null)
    {
        // view to print customer payment
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Payment Receipt");
        
        $payment_id = intval(JRequest::getVar("payment_id"));
        
        $query = "select p.*, cu.customer_name, cu.customer_address, c.city, b.bank_name, cu.account_balance from `#__payments` p inner join `#__customers` cu on p.party_id=cu.id inner join `#__cities` c on cu.city_id=c.id left join `#__banks` b on p.bank_id=b.id where p.id=" . $payment_id;
        $db->setQuery($query);
        $payment = $db->loadObject();
        $this->payment = $payment;
        
        $query = "select `value_string` from `#__settings` where `key`='mobile_no'";
        $db->setQuery($query);
        $mobile_no = $db->loadResult();
        $mobile_no = explode(",", $mobile_no);
        $this->mobile_no = implode("<br />", $mobile_no);
        
        /*$query = "select `value_string` from `#__settings` where `key`='tin_no'";
        $db->setQuery($query);
        $this->tin_no = $db->loadResult();*/
        
        $query = "select `value_string` from `#__settings` where `key`='invoice_footer'";
        $db->setQuery($query);
        $this->invoice_footer = $db->loadResult();
        
        parent::display($tpl);
    } 
}
?>