<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class AmittradingModelAmittrading4 extends JModelItem
{
    function save_cash_expense_entry()
    {
        // function to save cash expense entry -> cash_in_hand setting is updated
        
        $db = JFactory::getDbo();
        
        $expense_date = date("Y-m-d", strtotime(JRequest::getVar("expense_date")));
        $expense_head_id = intval(JRequest::getVar("expense_head_id"));
        $amount = floatval(JRequest::getVar("amount"));
        $description = JRequest::getVar("description");
        
        $cash_expense = new stdClass();
        
        $cash_expense->expense_date = $expense_date;
        $cash_expense->expense_head_id = $expense_head_id;
        $cash_expense->amount = $amount;
        $cash_expense->description = $description;
        $cash_expense->entry_by = intval(JFactory::getUser()->id);
        $cash_expense->entry_date = date("Y-m-d");
        
        $db->insertobject("#__cash_expenses", $cash_expense, "");
        $cash_expense_id = intval($db->insertid());
        
        $query = "update `#__settings` set `value_numeric`=value_numeric-" . $amount . " where `key`='cash_in_hand'";
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Cash expense dated " . date("d-M-Y", strtotime($expense_date)) . " of " . $amount . "/- has been added.", "CE", $cash_expense_id);
        return "Cash expense saved successfully.";   
    }
    
    function save_cash_transaction()
    {
        /*
        * function to save cash transaction for any bank account
        * 3 types are CASH_WITHDRAW, CASH_DEPOSIT, BANK_CHARGES
        * 1st 2 changes the cash_in_hand setting value
        * All 3 affects the balance of account statement
        */
        
        $db = JFactory::getDbo();
        
        $cash_transaction_type = intval(JRequest::getVar("cash_transaction_type"));
        $fund_transfer_type = intval(JRequest::getVar("fund_transfer_type"));
        
        
        $transaction_date = date("Y-m-d", strtotime(JRequest::getVar("transaction_date")));
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $amount = floatval(JRequest::getVar("amount"));
        $transfer_to_bank_account_id = intval(JRequest::getVar("transfer_to_bank_account_id"));
        $description = JRequest::getVar("description");
        
        $query = "select concat(account_name, ', ', bank_name) from `#__bank_accounts` where id=" . $bank_account_id;
        $db->setQuery($query);
        $bank_account = $db->loadResult();
        
        $query = "select concat(account_name, '(', bank_name,')') from `#__bank_accounts` where id=" . $bank_account_id;
        $db->setQuery($query);
        $bank_account_from = $db->loadResult();
        
        $query = "select concat(account_name, '(', bank_name,')') from `#__bank_accounts` where id=" . $transfer_to_bank_account_id;
        $db->setQuery($query);
        $bank_account_to = $db->loadResult();
        
        $description1 = "";
        $description2 = "";
        if($cash_transaction_type == CASH_WITHDRAW) 
        { 
            if($description == "")
            {
                $description = "Cash Withdraw";    
            }
            else
            {
                $description = "Cash Withdraw : ".$description;    
            }
             
        }
        if($cash_transaction_type == CASH_DEPOSIT)
        {
            if($description == "")
            {
                $description = "Cash Deposit";    
            }
            else
            {
                $description = "Cash Deposit : ".$description;    
            }    
        }
        if($cash_transaction_type == BANK_CHARGES)
        {
             if($description == "")
            {
                $description = "Bank Charges";    
            }
            else
            {
                $description = "Bank Charges : ".$description;    
            }    
        }
        if($fund_transfer_type == FUND_TRANSFER) 
        { 
            if($description == "")
            {
                $description1 = "Fund Transfer " . ($bank_account_to!="" ? " : to " . $bank_account_to : "") ;    
            }
            else
            {
                $description1 = "Fund Transfer " . ($bank_account_to!="" ? " : to " . $bank_account_to : "") . " " . "(" . $description . ")";    
            }
            
            $fund_transfer = new stdClass();
        
            $fund_transfer->bank_account_id = $bank_account_id;
            $fund_transfer->transaction_date = $transaction_date;
            $fund_transfer->amount = $amount;
            $fund_transfer->transaction_type = $fund_transfer_type;
            $fund_transfer->item_type = TRANSFER_FROM_BANK_ACCOUNT;
            
            $fund_transfer->cleared = YES;
            $fund_transfer->instrument = CASH;
            $fund_transfer->description = $description1;
            $fund_transfer->entry_by = intval(JFactory::getUser()->id);
            $fund_transfer->entry_date = date("Y-m-d");
            
            $db->insertobject("#__bank_transactions", $fund_transfer, "");
            $bank_transaction_id = intval($db->insertid());
            
            $query = "update `#__bank_transactions` set `fund_transfer_from_id`=" . intval($bank_transaction_id) . " where id=" . $bank_transaction_id;
            $db->setQuery($query);
            $db->query();
            
            if($description == "")
            {
                $description2 = "Fund Received " . ($bank_account_from!="" ? " : from " . $bank_account_from : "") ;    
            }
            else
            {
                $description2 = "Fund Received " . ($bank_account_from!="" ? " : from " . $bank_account_from : "") . " " . "(" . $description . ")";    
            }
            
            $fund_transfer = new stdClass();
        
            $fund_transfer->bank_account_id = $transfer_to_bank_account_id;
            $fund_transfer->transaction_date = $transaction_date;
            $fund_transfer->amount = $amount;
            $fund_transfer->transaction_type = $fund_transfer_type;
            $fund_transfer->item_type = TRANSFER_TO_BANK_ACCOUNT;
            
            $fund_transfer->cleared = YES;
            $fund_transfer->instrument = CASH;
            $fund_transfer->description = $description2;
            $fund_transfer->entry_by = intval(JFactory::getUser()->id);
            $fund_transfer->entry_date = date("Y-m-d");
            $fund_transfer->fund_transfer_from_id = $bank_transaction_id;
            
            $db->insertobject("#__bank_transactions", $fund_transfer, ""); 
            //$bank_transaction_id = intval($db->insertid());
        }
        else
        {
            $cash_transaction = new stdClass();
            
            $cash_transaction->bank_account_id = $bank_account_id;
            $cash_transaction->transaction_date = $transaction_date;
            $cash_transaction->transaction_type = $cash_transaction_type;
            $cash_transaction->amount = $amount;
            $cash_transaction->cleared = YES;
            $cash_transaction->instrument = CASH;
            $cash_transaction->description = $description;
            $cash_transaction->entry_by = intval(JFactory::getUser()->id);
            $cash_transaction->entry_date = date("Y-m-d");
            
            $db->insertobject("#__bank_transactions", $cash_transaction, "");
            $bank_transaction_id = intval($db->insertid());
        }
       
        if($cash_transaction_type == CASH_WITHDRAW)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric+" . $amount . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__bank_accounts` set `balance`=balance-" . $amount . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
        }
        else if($cash_transaction_type == CASH_DEPOSIT) 
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric-" . $amount . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__bank_accounts` set `balance`=balance+" . $amount . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
        }
        else if($cash_transaction_type == BANK_CHARGES) 
        {
            $query = "update `#__bank_accounts` set `balance`=balance-" . $amount . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
        }
        else if($fund_transfer_type == FUND_TRANSFER) 
        {
            $query = "update `#__bank_accounts` set `balance`=balance-" . $amount . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__bank_accounts` set `balance`=balance+" . $amount . " where id=" . $transfer_to_bank_account_id;
            $db->setQuery($query);
            $db->query();
        }
        
        if($fund_transfer_type == FUND_TRANSFER)
        {
            Functions::log_activity("Fund of " . $amount . "/- has been transferred from bank account " . $bank_account_from . " to bank account " . $bank_account_to . " on date " . date("d-M-Y", strtotime($transaction_date)) . ".", "FT", $fund_transfer_id);
        }
        else
        {
            Functions::log_activity("Cash of amount " . $amount . "/- has been " . ($cash_transaction_type == CASH_WITHDRAW ? "withdrawn from " : ($cash_transaction_type == CASH_DEPOSIT ? "deposited to " : "charged to ")) . $bank_account  . " dated " . date("d-M-Y", strtotime($transaction_date)) . ".", "BT", $bank_transaction_id);
        }
        return "Transaction saved successfully.";   
    }
    
    function cash_transaction_details()
    {
        $db = JFactory::getDbo();
        
        $cash_transaction_id = intval(JRequest::getVar("cash_transaction_id"));
        $fund_transfer_id = intval(JRequest::getVar("fund_transfer_id"));
        $fund_transfer_type = intval(JRequest::getVar("fund_transfer_type"));
        
        if($fund_transfer_id > 0)
        {
            $query = "select fund_transfer_from_id from `#__bank_transactions` where id=" . $fund_transfer_id;
            $db->setQuery($query);
            $fund_transfer_from_id = intval($db->loadResult());
            
            $query = "select * from `#__bank_transactions` where fund_transfer_from_id=" . $fund_transfer_from_id . " order by id asc";
            $db->setQuery($query);
            $bank_transactions = $db->loadObjectList();
            
            $query = "select concat(account_name,'(',bank_name,')') from `#__bank_accounts` where id=" . intval($bank_transactions[0]->bank_account_id);
            $db->setQuery($query);
            $fund_transfer_from_account_name = $db->loadResult();
            
            $transfer_details = array( "transfer_date" => date("d-M-Y", strtotime($bank_transactions[0]->transaction_date)),
                                       "fund_transfer_from_acount_id" => intval($bank_transactions[0]->bank_account_id),
                                       "fund_transfer_from_account_name" => $fund_transfer_from_account_name,
                                       "amount_transferred" => floatval($bank_transactions[0]->amount), 
                                       "fund_transfer_to_account_id" => intval($bank_transactions[1]->bank_account_id),
                                       "description" => $bank_transactions[0]->description
                                     );
            
            echo json_encode($transfer_details);
            return;            
        }
        else
        {
            $query = "select `amount`, `transaction_date`, `description` from `#__bank_transactions` where id=" . $cash_transaction_id;
            $db->setQuery($query);
            $cash_transaction = $db->loadObject();
            
            $cash_transaction->transaction_date = date("d-M-Y", strtotime($cash_transaction->transaction_date));
            echo json_encode($cash_transaction);
            return;     
        }
    }
    
    function edit_cash_transaction()
    {
        /*
        * function revert_cash_transaction is called to revert the changes 
        * rest is same as add task
        * cash transaction type remains same before and after edit
        */
        
        $db = JFactory::getDbo();
        
        $cash_transaction_id = intval(JRequest::getVar("cash_transaction_id"));
        $cash_transaction_type = intval(JRequest::getVar("cash_transaction_type"));
        
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $transaction_date = date("Y-m-d", strtotime(JRequest::getVar("transaction_date")));
        $amount = floatval(JRequest::getVar("amount"));
        $description = JRequest::getVar("description");
        
        if($cash_transaction_type == CASH_WITHDRAW) 
        { 
            if($description == "")
            {
                $description = "Cash Withdraw";    
            }
            else
            {
                $description = "Cash Withdraw : ".$description;    
            }
             
        }
        if($cash_transaction_type == CASH_DEPOSIT)
        {
            if($description == "")
            {
                $description = "Cash Deposit";    
            }
            else
            {
                $description = "Cash Deposit : ".$description;    
            }    
        }
        if($cash_transaction_type == BANK_CHARGES)
        {
             if($description == "")
            {
                $description = "Bank Charges";    
            }
            else
            {
                $description = "Bank Charges : ".$description;    
            }    
        }
       
        $this->revert_cash_transaction($cash_transaction_id, $cash_transaction_type);
    
        $cash_transaction = new stdClass();
        
        $cash_transaction->id = $cash_transaction_id;
        $cash_transaction->transaction_date = $transaction_date;
        $cash_transaction->amount = $amount;
        $cash_transaction->description = $description;
        
        $db->updateobject("#__bank_transactions", $cash_transaction, "id");       
       
        
        if($cash_transaction_type == CASH_WITHDRAW)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric+" . $amount . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__bank_accounts` set `balance`=balance-" . $amount . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
        }
        else if($cash_transaction_type == CASH_DEPOSIT) 
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric-" . $amount . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__bank_accounts` set `balance`=balance+" . $amount . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
        }
        else if($cash_transaction_type == BANK_CHARGES) 
        {
            $query = "update `#__bank_accounts` set `balance`=balance-" . $amount . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "select concat(account_name, ', ', bank_name) from `#__bank_accounts` where id=" . $bank_account_id;
        $db->setQuery($query);
        $bank_account = $db->loadResult();
        
        Functions::log_activity("(Entry Updated) Cash of amount " . $amount . "/- has been " . ($cash_transaction_type == CASH_WITHDRAW ? "withdrawn from " : ($cash_transaction_type == CASH_DEPOSIT ? "deposited to " : "charged to ")) . $bank_account  . " dated " . date("d-M-Y", strtotime($transaction_date)) . ".", "BT", $cash_transaction_id);
        return "Transaction updated successfully.";   
    }
    
    function edit_fund_transfer()
    {
        $db = JFactory::getDbo();
        
        $fund_transfer_id = intval(JRequest::getVar("fund_transfer_id"));
        $fund_transfer_type = intval(JRequest::getVar("fund_transfer_type"));
        
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $transaction_date = date("Y-m-d", strtotime(JRequest::getVar("transaction_date")));
        $transfer_to_bank_account_id = intval(JRequest::getVar("transfer_to_bank_account_id"));
        $amount = floatval(JRequest::getVar("amount"));
        $description = JRequest::getVar("description");
        
        $query = "select * from `#__bank_transactions` where id=" . $fund_transfer_id;
        $db->setQuery($query);
        $previous_details = $db->loadObject();
        
        $query = "select concat(account_name, ', ', bank_name) from `#__bank_accounts` where id=" . $bank_account_id;
        $db->setQuery($query);
        $bank_account_from = $db->loadResult();
        
        $query = "select concat(account_name, ', ', bank_name) from `#__bank_accounts` where id=" . $transfer_to_bank_account_id;
        $db->setQuery($query);
        $bank_account_to = $db->loadResult();
        
        $description1 = "";
        $description2 = "";
        if($fund_transfer_type == FUND_TRANSFER)
        {
            if($description == "")
            {
                $description1 = "Fund Transfer " . ($bank_account_to!="" ? " : to " . $bank_account_to : "") ;    
            }
            else
            {
                $description1 = "Fund Transfer " . ($bank_account_to!="" ? " : to " . $bank_account_to : "") . " " . "(" . $description . ")";    
            }
            
            $fund_transfer = new stdClass();
        
            $fund_transfer->id = $fund_transfer_id ;
            $fund_transfer->bank_account_id = $bank_account_id;
            $fund_transfer->transaction_date = $transaction_date;
            $fund_transfer->amount = $amount;
            $fund_transfer->description = $description1;
            
            $db->updateObject("#__bank_transactions", $fund_transfer, "id");
            
            $query = "select id,bank_account_id,amount from `#__bank_transactions` where fund_transfer_from_id=". $fund_transfer_id ." and item_type=" .TRANSFER_TO_BANK_ACCOUNT;
            $db->setQuery($query);
            $second_row_id = $db->loadObject();
            $second_bank_id = intval($second_row_id->bank_account_id);
            
            if($description == "")
            {
                $description2 = "Fund Received " . ($bank_account_from!="" ? " : from " . $bank_account_from : "") ;    
            }
            else
            {
                $description2 = "Fund Received " . ($bank_account_from!="" ? " : from " . $bank_account_from : "") . " " . "(" . $description . ")";    
            }
            
            $fund_transfer = new stdClass();
        
            $fund_transfer->id = $second_row_id->id;
            
            $fund_transfer->bank_account_id = $transfer_to_bank_account_id;
            $fund_transfer->transaction_date = $transaction_date;
            $fund_transfer->amount = $amount;
            $fund_transfer->description = $description2;
            
            $db->updateObject("#__bank_transactions", $fund_transfer, "id"); 
            
        }
       
        if($fund_transfer_type == FUND_TRANSFER)
        {
            $query = "update `#__bank_accounts` set `balance`=balance-" . $amount . "+" . floatval($previous_details->amount) . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__bank_accounts` set `balance`=balance-" . floatval($second_row_id->amount) . " where id=" . $second_bank_id ;
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__bank_accounts` set `balance`=balance+" . $amount . " where id=" . intval($transfer_to_bank_account_id);
            $db->setQuery($query);
            $db->query();    
        }
        
        Functions::log_activity("(Entry Updated) Fund of " . $amount . "/- has been transferred from bank account " . $bank_account_from . " to bank account " . $bank_account_to . " on date " . date("d-M-Y", strtotime($transaction_date)) . ".", "FT", $fund_transfer_id);
        
        return "Transaction updated successfully.";        
    }
    
    function delete_cash_transaction()
    {
        $db = JFactory::getDbo();
        
        $cash_transaction_id = intval(JRequest::getVar("cash_transaction_id"));
        $cash_transaction_type = intval(JRequest::getVar("cash_transaction_type"));
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        
        list($amount, $transaction_date, $bank_account) = $this->revert_cash_transaction($cash_transaction_id, $cash_transaction_type);
        
        $query = "delete from `#__bank_transactions` where id=" . $cash_transaction_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Cash of amount " . $amount . "/- " . ($cash_transaction_type == CASH_WITHDRAW ? "withdrawn from " : ($cash_transaction_type == CASH_DEPOSIT ? "deposited to " : "charged to ")) . $bank_account  . " dated " . date("d-M-Y", strtotime($transaction_date)) . " has been deleted.", "BT", $cash_transaction_id);
        return "Transaction deleted successfully.";
    }
    
    function delete_fund_transfer()
    {
        $db = JFactory::getDbo();
        
        $fund_transfer_id = intval(JRequest::getVar("fund_transfer_id"));
        $fund_transfer_type = intval(JRequest::getVar("fund_transfer_type"));
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        
        $query = "select * from `#__bank_transactions` where id=" . $fund_transfer_id;
        $db->setQuery($query);
        $first_details = $db->loadObject();
        $first_bank_id = intval($first_details->bank_account_id);
        $first_bank_amount = intval($first_details->amount);
        
        $query = "select id,bank_account_id,amount from `#__bank_transactions` where fund_transfer_from_id=". $fund_transfer_id ." and item_type=" .TRANSFER_TO_BANK_ACCOUNT;
        $db->setQuery($query);
        $second_details = $db->loadObject();
        $second_bank_id = intval($second_details->bank_account_id);
        $second_bank_amount = intval($second_details->amount);
        
        $query = "update `#__bank_accounts` set `balance`=balance+" . $first_bank_amount . " where id=" . intval($first_bank_id);
        $db->setQuery($query);
        $db->query(); 
        
        $query = "update `#__bank_accounts` set `balance`=balance-" . $second_bank_amount . " where id=" . intval($second_bank_id);
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__bank_transactions` where fund_transfer_from_id=" . $fund_transfer_id;
        $db->setQuery($query);
        $db->query();

        Functions::log_activity("(Entry Updated) Fund of amount " . $amount . "/- has been " . $fund_transfer_type == FUND_TRANSFER ? "fund transfer from " : $bank_account  . " dated " . date("d-M-Y", strtotime($transaction_date)) . ".", "BT", $fund_transfer_id);
        return "Transaction updated successfully.";    
    }
    
    
    
    function revert_cash_transaction($cash_transaction_id, $cash_transaction_type)
    {
        $db = JFactory::getDbo();
        
        $query = "select `amount`, `bank_account_id`, `transaction_date` from `#__bank_transactions` where id=" . $cash_transaction_id;
        $db->setQuery($query);
        $cash_transaction = $db->loadObject();
        
        if($cash_transaction_type == CASH_WITHDRAW)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric-" . floatval($cash_transaction->amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__bank_accounts` set `balance`=balance+" . floatval($cash_transaction->amount) . " where id=" . intval($cash_transaction->bank_account_id);
            $db->setQuery($query);
            $db->query();
        }
        else if($cash_transaction_type == CASH_DEPOSIT) 
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric+" . floatval($cash_transaction->amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__bank_accounts` set `balance`=balance-" . floatval($cash_transaction->amount) . " where id=" . intval($cash_transaction->bank_account_id);
            $db->setQuery($query);
            $db->query();
        }
        else if($cash_transaction_type == BANK_CHARGES) 
        {
            $query = "update `#__bank_accounts` set `balance`=balance+" . floatval($cash_transaction->amount) . " where id=" . intval($cash_transaction->bank_account_id);
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "select concat(account_name, ', ', bank_name) from `#__bank_accounts` where id=" . intval($cash_transaction->bank_account_id);
        $db->setQuery($query);
        $bank_account = $db->loadResult();
        
        return array(floatval($cash_transaction->amount), $cash_transaction->transaction_date, $bank_account);
    }
    
    // next 3 function are not in use as cheque payment is set to cleared while receiving payment itself- called via bank_reconciliiation view which is not in used
    function clear_customer_cheque()
    {
        $item_ids = JRequest::getVar("item_ids");
        $customer_cheque_clearance_date = date("Y-m-d", strtotime(JRequest::getVar("customer_cheque_clearance_date")));
        
        if(count($item_ids) > 0)
        {
            foreach($item_ids as $item_id)
            {
                $this->clear_cheque($item_id, CUSTOMER_PAYMENT, $customer_cheque_clearance_date);
            }
        }
        
        return "Customer cheque payments cleared successfully.";
    }
    
    function clear_cheque($transaction_id=0, $item_type=0, $clearance_date="")
    {
        $db = JFactory::getDbo();
        
        if($transaction_id == 0)
        {
            $transaction_id = intval(JRequest::getVar("transaction_id"));
            $item_type = intval(JRequest::getVar("item_type"));
            $clearance_date = date("Y-m-d", strtotime(JRequest::getVar("clearance_date")));
        }
        
        $query = "update `#__bank_transactions` set cleared=" . YES . ", clearance_date='" . $clearance_date . "' where id=" . $transaction_id;
        $db->setQuery($query);
        $db->query();
        
        if($item_type == CUSTOMER_PAYMENT)
        {
            $query = "select bt.bank_account_id, bt.amount, bt.transaction_date, concat(ba.account_name, ', ', ba.bank_name) bank_account, b.bank_name, c.customer_name party_name from `#__bank_transactions` bt inner join `#__bank_accounts` ba on bt.bank_account_id=ba.id inner join `#__payments` p on bt.item_id=p.id inner join `#__customers` c on p.party_id=c.id inner join `#__banks` b on bt.bank_id=b.id where bt.id=" . $transaction_id;
            $db->setQuery($query);
            $payment = $db->loadObject();
            
            $query = "update `#__bank_accounts` set `balance`=balance+" . floatval($payment->amount) . ", `uncleared_balance`=uncleared_balance-" . floatval($payment->amount) . " where id=" . intval($payment->bank_account_id);
            $db->setQuery($query);
            $db->query();
        }
        elseif($item_type == SUPPLIER_PAYMENT)
        {
            $query = "select bt.bank_account_id, bt.amount, bt.transaction_date, concat(ba.account_name, ', ', ba.bank_name) bank_account, s.supplier_name party_name from `#__bank_transactions` bt inner join `#__bank_accounts` ba on bt.bank_account_id=ba.id inner join `#__payments` p on bt.item_id=p.id inner join `#__suppliers` s on p.party_id=s.id where bt.id=" . $transaction_id;
            $db->setQuery($query);
            $payment = $db->loadObject();
            
            $query = "update `#__bank_accounts` set `balance`=balance-" . floatval($payment->amount) . " where id=" . intval($payment->bank_account_id);
            $db->setQuery($query);
            $db->query();
        }
        
        Functions::log_activity(($item_type == CUSTOMER_PAYMENT ? "Customer " : "Supplier ") . $payment->party_name .  "'s cheque payment of amount " . floatval($payment->amount) . "/- has been cleared for bank account " . $payment->bank_account  . " dated " . date("d-M-Y", strtotime($payment->transaction_date)) . ".", "BT", $transaction_id);
        echo "ok";   
    }
    
    function delete_customer_cheque_payment()
    {
        $db = JFactory::getDbo();
        
        $transaction_id = intval(JRequest::getVar("transaction_id"));
        $item_type = intval(JRequest::getVar("item_type"));
        
        $query = "select `item_id` from `#__bank_transactions` where id=" . $transaction_id;
        $db->setQuery($query);
        $payment_id = intval($db->loadResult());
        
        $query = "select c.id customer_id, c.customer_name, p.amount_received, p.total_amount, p.payment_mode, p.bank_account_id from `#__payments` p inner join `#__customers` c on p.party_id=c.id where p.id=" . $payment_id;
        $db->setQuery($query);
        $payment = $db->loadObject();   
        
        $query = "delete from `#__payments` where id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "select invoice_id sales_id, amount from `#__payment_items` where payment_id=" . $payment_id;
        $db->setQuery($query);
        $payment_items  = $db->loadObjectList();
        
        foreach($payment_items as $item)
        {
            $query = "update `#__sales_invoice` set amount_paid=amount_paid-" . floatval($item->amount) . ", status=" . UNPAID . " where id=" . intval($item->sales_id);
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__payment_items` set status=" . PART_PAYMENT . " where invoice_id=" . intval($item->sales_id) . " and payment_type=" . CUSTOMER_PAYMENT;
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "delete from `#__payment_items` where payment_id=" .$payment_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__bank_transactions` where id=" . $transaction_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "update `#__bank_accounts` set `uncleared_balance`=uncleared_balance-" . floatval($payment->amount_received) . " where id=" . intval($payment->bank_account_id);
        $db->setQuery($query);
        $db->query();
        
        $query = "update `#__customers` set `account_balance`=account_balance+" . floatval($payment->total_amount) . " where id=" . intval($payment->customer_id);
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Payment of " . floatval($payment->amount_received) . "/- from customer " . $payment->customer_name . " has been deleted as the cheque was not cleared.", "PC", $transaction_id);
        echo "ok";
    } 
    
    function send_payment_reminder_to_customers()
    {
        // function to send payment reminder to customers selected in collection report
         
        $db = JFactory::getDbo();
        
        $sales_ids = base64_decode(JRequest::getVar("s_ids"));
        $sales_ids = explode(",", $sales_ids);
        
        $condition = "";
        foreach($sales_ids as $key=>$sales_id)
        {
            $condition .= ($condition != "" ? " or " : "") . "(s.id=" . $sales_id . ")";
        }
        
        $query = "select cu.customer_name, cu.contact_no, (s.bill_amount - s.amount_paid) amount_pending from `#__sales_invoice` s inner join `#__customers` cu on s.customer_id=cu.id " . ($condition != "" ? " where " . $condition : "");
        $db->setQuery($query);
        $reminders = $db->loadObjectList();
        
        $org_name = "AMIT TRADING COMPANY";
        $sent_sms_count = 0;
        
        foreach($reminders as $reminder)
        {
            $due = floatval($reminder->amount_pending);
            $mobile_no = $reminder->contact_no;
            if(strlen($mobile_no) == 10)
            {
                $query = "select `value_numeric` from `#__settings` where `key`='sms_balance'";
                $db->setQuery($query);
                $sms_balance = intval($db->loadResult());
                
                if($sms_balance > 0)
                {
                    //$sms = "Dear " . $reminder->customer_name . ",\nKindly pay your due of Rs.". $due .".\nIgnore if already paid.\n-" . $org_name;
                    $sms = "Dear Sir,\nYour outstanding amount on " . date("d/m/Y") . " is Rs. ". round_2dp($due) .". Please deposit this amount.\nThanks\n" . $org_name;
                    Functions::send_sms($mobile_no,$sms);
                    $sent_sms_count++;
                }
            }
        }
        
        if($sent_sms_count > 0)
        { Functions::log_activity("Payment reminder (total " . $sent_sms_count . " SMS) has been sent to customers."); }
        
        echo "ok";
    }
    
    // next 3 functions are for stock correction, customer & supplier account balance correction
    function update_item_stock()
    {
        $db = JFactory::getDbo();
        
        $query = "select id from `#__items`";
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        $query = "select id from `#__inventory_locations`";
        $db->setQuery($query);
        $locations = $db->loadObjectList();
        
        set_time_limit(0);
        foreach($locations as $location)
        {
            foreach($items as $item)
            {
                $stock = 0;
                
                $query = "select `opening_stock` from `#__inventory_items` where item_id=" . intval($item->id) . " and location_id=" . intval($location->id);
                $db->setQuery($query);
                $stock += floatval($db->loadResult());
                
                $query = "select sum(pack) from `#__purchase_invoice_items` where item_id=" . intval($item->id) . " and location_id=" . intval($location->id);
                $db->setQuery($query);
                $stock += floatval($db->loadResult());
                
                $query = "select sum(pack) from `#__purchase_return_items` where item_id=" . intval($item->id) . " and location_id=" . intval($location->id);
                $db->setQuery($query);
                $stock -= floatval($db->loadResult());
                
                $query = "select sum(pack) from `#__sales_invoice_items` where item_id=" . intval($item->id) . " and location_id=" . intval($location->id);
                $db->setQuery($query);
                $stock -= floatval($db->loadResult());
                
                $query = "select sum(pack) from `#__sales_return_items` where item_id=" . intval($item->id) . " and location_id=" . intval($location->id);
                $db->setQuery($query);
                $stock += floatval($db->loadResult());
                
                $query = "select sum(sti.pack) from `#__stock_transfer_items` sti inner join `#__stock_transfer` st on sti.stock_transfer_id=st.id where sti.item_id=" . intval($item->id) . " and st.location_from_id=" . intval($location->id);
                $db->setQuery($query);
                $stock -= floatval($db->loadResult());
                
                $query = "select sum(sti.pack) from `#__stock_transfer_items` sti inner join `#__stock_transfer` st on sti.stock_transfer_id=st.id where sti.item_id=" . intval($item->id) . " and st.location_to_id=" . intval($location->id);
                $db->setQuery($query);
                $stock += floatval($db->loadResult());
                
                $query = "update `#__inventory_items` set `stock`=" . $stock . " where item_id=" . intval($item->id) . " and location_id=" . intval($location->id);
                $db->setQuery($query);
                $db->query();
                
                /*$query = "select count(id) from `#__inventory_items` where item_id=" . intval($item->id) . " and location_id=" . intval($location->id);
                $db->setQuery($query);
                if(intval($db->loadResult()) > 0)
                {
                    $query = "update `#__inventory_items` set `stock`=" . $stock . " where item_id=" . intval($item->id) . " and location_id=" . intval($location->id);
                    $db->setQuery($query);
                    $db->query();
                }
                else
                {
                    $query = "insert into `#__inventory_items` (`item_id`,`location_id`,`opening_stock`,`stock`) values(" . intval($item->id) . "," . intval($location->id) . "," . $stock . "," . $stock . ")";
                    $db->setQuery($query);
                    $db->query();
                }*/
            }
        }
        
        echo "Done.";exit;
    }
    
    function update_customer_account_balance()
    {
        $db = JFactory::getDbo();
        
        $query = "select id, opening_balance from `#__customers`";
        $db->setQuery($query);
        $customers = $db->loadObjectList();
        
        set_time_limit(0);
        foreach($customers as $customer)
        {
            $query = "select sum(bill_amount) from `#__sales_invoice` where customer_id=" . intval($customer->id);
            $db->setQuery($query);
            $account_balance = floatval($customer->opening_balance) + floatval($db->loadResult());
            
            $query = "select sum(total_amount) from `#__payments` where party_id=" . intval($customer->id) . " and payment_type=" . CUSTOMER_PAYMENT;
            $db->setQuery($query);
            $account_balance -= floatval($db->loadResult()); 

            $query = "select sum(bill_amount) from `#__sales_returns` where customer_id=" . intval($customer->id);
            $db->setQuery($query);
            $account_balance -= floatval($db->loadResult()); 

            $query = "update `#__customers` set account_balance=" . $account_balance . " where id=" . intval($customer->id);
            $db->setQuery($query);
            $db->query();
        }
        
        echo "Done.";exit;
    }
    
    function update_supplier_account_balance()
    {
        $db = JFactory::getDbo();
        
        $query = "select id, opening_balance from `#__suppliers`";
        $db->setQuery($query);
        $suppliers = $db->loadObjectList();
        
        set_time_limit(0);
        foreach($suppliers as $supplier)
        {    
            $query = "select sum(bill_amount) from `#__purchase_invoice` where supplier_id=" . intval($supplier->id);
            $db->setQuery($query);
            $account_balance = floatval($supplier->opening_balance) + floatval($db->loadResult());
            
            $query = "select sum(total_amount) from `#__payments` where party_id=" . intval($supplier->id) . " and payment_type=" . SUPPLIER_PAYMENT;
            $db->setQuery($query);
            $account_balance -= floatval($db->loadResult());
            
            $query = "select sum(challan_amount) from `#__purchase_returns` where supplier_id=" . intval($supplier->id);
            $db->setQuery($query);
            $account_balance -= floatval($db->loadResult());
            
            $query = "update `#__suppliers` set account_balance=" . $account_balance . " where id=" . intval($supplier->id);
            $db->setQuery($query);
            $db->query();
        }
        
        echo "Done.";exit;
    } 
}
?>