<?php
jimport( 'joomla.application.component.view');

class AmittradingViewBank_reconcilliation extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * unused
        * used to clear the cheque payments manually for bank accounts
        * now cheque payments are set to cleared while making payment itself
        */
        
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "bank_reconcilliation"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $document = JFactory::getDocument();
        $document->setTitle( "Bank Reconcillication" );
        
        $condition = "(bt.cleared=" . NO . ")";
        
        $query = "select bt.*, concat(ba.account_name, ', ', ba.bank_name) bank_account, b.bank_name, c.customer_name party_name from `#__bank_transactions` bt inner join `#__bank_accounts` ba on bt.bank_account_id=ba.id inner join `#__payments` p on bt.item_id=p.id inner join `#__customers` c on p.party_id=c.id inner join `#__banks` b on bt.bank_id=b.id where bt.item_type=" . CUSTOMER_PAYMENT . " and " . $condition . " order by bt.transaction_date";
        $db->setQuery($query);
        $customer_payments = $db->loadObjectList();
        $this->customer_payments = $customer_payments;
        
        /*$query = "select bt.*, concat(ba.account_name, ', ', ba.bank_name) bank_account, s.supplier_name party_name from `#__bank_transactions` bt inner join `#__bank_accounts` ba on bt.bank_account_id=ba.id inner join `#__payments` p on bt.item_id=p.id inner join `#__suppliers` s on p.party_id=s.id where bt.item_type=" . SUPPLIER_PAYMENT . " and " . $condition . " order by bt.transaction_date";
        $db->setQuery($query);
        $supplier_payments = $db->loadObjectList();
        $this->supplier_payments = $supplier_payments;*/
        
        parent::display($tpl);
    }
}
?>