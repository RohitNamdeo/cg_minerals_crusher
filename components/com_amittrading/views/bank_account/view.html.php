<?php
jimport( 'joomla.application.component.view');

class AmittradingViewBank_account extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * this is account statement for any bank account
        * this includes customer payment, supplier payment, cash withdraw, cash deposit, bank charges for that bank account
        * add/edit/delete options are provided for the above last 3 mentioned entries
        */
        
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $document = JFactory::getDocument();
        $document->setTitle( "Bank Account" );
        
        $bank_account_id = intval(JRequest::getVar('bank_account_id'));
        $this->bank_account_id = $bank_account_id;
        
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
        
        $condition = "(bt.transaction_date between '" . $from_date . "' and '" . $to_date . "')";
        
        //$query = "select bt.*, b.bank_name from `#__bank_transactions` bt left join `#__banks` b on bt.bank_id=b.id where bt.bank_account_id=" . $bank_account_id . " and " . $condition . " order by bt.transaction_date";
        $query = "select bt.*, b.bank_name, ba.bank_name from `#__bank_transactions` bt left join `#__banks` b on bt.bank_id=b.id inner join `#__bank_accounts` ba on bt.bank_account_id=ba.id where bt.bank_account_id=" . $bank_account_id . " and " . $condition . " order by bt.transaction_date";
        $db->setQuery($query);
        $account_details = $db->loadObjectList();
        
        if(count($account_details) > 0)
        {
            foreach($account_details as $details)
            {
                if($details->item_type == CUSTOMER_PAYMENT)
                {
                    $query = "select c.customer_name from `#__customers` c inner join `#__payments` p on c.id=p.party_id where p.id=" . intval($details->item_id);
                    $db->setQuery($query);
                    $details->party_name = $db->loadResult();
                }
                else if($details->item_type == SUPPLIER_PAYMENT)
                {
                    $query = "select s.supplier_name from `#__suppliers` s inner join `#__payments` p on s.id=p.party_id where p.id=" . intval($details->item_id);
                    $db->setQuery($query);
                    $details->party_name = $db->loadResult();
                }
            }
        }
        
        $this->account_details = $account_details;
        
        $query = "select * from `#__bank_accounts` where id=" . $bank_account_id;
        $db->setQuery($query);
        $this->bank_account = $db->loadObject();
        
        $query = "select * from `#__bank_accounts` order by id asc";
        $db->setQuery($query);
        $this->bank_accounts = $db->loadObjectList("id");
    
        /*Opening Balance Calculation*/
        
        $query = "select sum(amount) from `#__bank_transactions` where bank_account_id=" . $bank_account_id . " and transaction_date < '" . $from_date . "' and (item_type=" . CUSTOMER_PAYMENT . " or transaction_type=" . CASH_DEPOSIT . ")";
        $db->setQuery($query);
        $debit = floatval($db->loadResult());
        
        $query = "select sum(amount) from `#__bank_transactions` where bank_account_id=" . $bank_account_id . " and transaction_date < '" . $from_date . "' and (item_type=" . SUPPLIER_PAYMENT . " or transaction_type=" . CASH_WITHDRAW . " or transaction_type=" . BANK_CHARGES . ")";
        $db->setQuery($query);
        $credit = floatval($db->loadResult());
        
        $opening_balance = floatval($debit - $credit) + floatval($this->bank_account->opening_balance);
        $this->opening_balance = $opening_balance;
        
        /*Opening Balance Calculation*/
        
        parent::display($tpl);
    }
}
?>