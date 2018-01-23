<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewPurchases_and_payments extends JViewLegacy
{
    public function display($tpl = null)
    {
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Purchases and Payments");
        
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        $show_all_bills = JRequest::getVar("bill");
        $show_all_pays = JRequest::getVar("pay");
        
        $this->supplier_id = $supplier_id;
        $this->show_all_bills = $show_all_bills;
        $this->show_all_pays = $show_all_pays;
        
        $query = "select p.*, p.id payment_id, ba.account_name, ba.bank_name from `#__payments` p left join `#__bank_accounts` ba on p.bank_account_id=ba.id where p.party_id=" . $supplier_id . " and p.payment_type=" . SUPPLIER_PAYMENT . ($show_all_pays == "" ? " and total_amount>amount_adjusted" : "") . " order by p.payment_date asc, p.id";
        $db->setQuery($query);
        $payments = $db->loadObjectList();        
        $this->payments = $payments;
        
        //print_r($payments); exit;
        
        //$query = "select p.*, p.id purchase_id from `#__purchase` p where p.supplier_id=" . $supplier_id . ($show_all_bills == "" ? " and p.status=" . UNPAID : "") . " order by p.bill_date asc, p.id";
        $query = "select p.*, p.id purchase_id from `#__purchase` p where p.supplier_id=" . $supplier_id . ($show_all_bills == "" ? " and p.status=" . UNPAID : "") . " order by p.bill_date asc, p.id";
        $db->setQuery($query);
        $bills = $db->loadObjectList();
        $this->bills = $bills;
        
        $query = "select `value_string` from `#__settings` where `key`='users_allowed_backdate_payments'";
        $db->setQuery($query);
        $users_allowed_backdate_payments = explode(",", $db->loadResult());
        
        $user_id = intval(JFactory::getUser()->id);
        $allow_edit_delete_payment = false;
        if(in_array($user_id, $users_allowed_backdate_payments))
        {
            $allow_edit_delete_payment = true;
        }
        $this->allow_edit_delete_payment = $allow_edit_delete_payment;
        
        parent::display($tpl);
    } 
}
?>