<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class AmittradingModelAmittrading2 extends JModelItem
{
    function calculate_due_amount()
    {
        /* single function to calculate due amount of customer, supplier and transporter depends on $type */

        $db = JFactory::getDBO();
        
        $party_id = intval(JRequest::getVar("party_id"));
        $type = JRequest::getVar("type");
        
        if($type == 'c')
        {
            $query = "select sum(bill_amount - amount_paid) from `#__sales_invoice` where customer_id=" . $party_id . " and status=" . UNPAID . " and (bill_amount - amount_paid) > 0";
        }
        else if($type == 's')
        {
            $query = "select sum(bill_amount - amount_paid) from `#__purchase_invoice` where supplier_id=" . $party_id . " and status=" . UNPAID . " and (bill_amount - amount_paid) > 0";
        }
        else if($type == 't')
        {
            $query = "select sum(transportation_amount - transportation_amount_paid) from `#__purchase_invoice` where transporter_id=" . $party_id . " and transporter_payment_mode=" . CREDIT . " and (transportation_amount - transportation_amount_paid) > 0";
        }
        
        $db->setQuery($query);
        $amount_due = floatval($db->loadResult());
        
        echo $amount_due;
    }
    
    function save_customer_payment()
    {   
        /*
        * entry in payments table with payment type CUSTOMER_PAYMENT
        * adjust_customer_account is called to adjust this payment in pending sales invoice
        * paid amount is $amount but account balance is decremented by ($amount + $credit_amount)
        * cash payment mode alters cash_in_hand setting, record saved in bank transactions
        * cheque payment mode alters balance for selected bank account, record saved in bank transactions
        * cheque is assumed as cleared from bank
        */
        
        $db = JFactory::getDBO();
        
        $payment_date = date("Y-m-d", strtotime(JRequest::getVar("payment_date")));
        $customer_id = intval(JRequest::getVar("customer_id"));
        $payment_mode = intval(JRequest::getVar("payment_mode"));
        $cheque_no = JRequest::getVar("cheque_no");
        $cheque_date = JRequest::getVar("cheque_date");
        $bank_id = intval(JRequest::getVar("bank_id"));
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $amount = floatval(JRequest::getVar("amount"));
        $credit_amount = floatval(JRequest::getVar("credit_amount"));
        //$credit_reason = JRequest::getVar("credit_reason");
        $remarks = JRequest::getVar("remarks");
        
        $total_amount = $amount + $credit_amount;
        
        $payment = new stdClass();
        
        $payment->party_id = $customer_id;
        $payment->payment_date = $payment_date;
        $payment->payment_mode = $payment_mode;
        $payment->cheque_no = $cheque_no;
        $payment->cheque_date = ($cheque_date != "" ? date("Y-m-d", strtotime($cheque_date)) : "");
        $payment->bank_id = $bank_id;
        $payment->bank_account_id = $bank_account_id;
        $payment->amount_received = $amount;
        $payment->credit_amount = $credit_amount;
        $payment->total_amount = $total_amount;
        //$payment->credit_reason = $credit_reason;
        $payment->payment_type = CUSTOMER_PAYMENT;
        $payment->remarks = $remarks;
        $payment->entry_by = intval(JFactory::getUser()->id);
        $payment->entry_date = date("Y-m-d");
        
        $db->insertObject("#__payments", $payment, "");
        $payment_id = intval($db->insertid());
        
        Functions::adjust_customer_account($customer_id);
        
        if($payment_mode == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric+" . floatval($amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
            
            $bank_transaction = new stdClass();
        
            $bank_transaction->transaction_date = $payment_date;
            $bank_transaction->amount = $amount;
            $bank_transaction->cleared = YES;
            $bank_transaction->item_type = CUSTOMER_PAYMENT;
            $bank_transaction->item_id = $payment_id;
            $bank_transaction->instrument = CASH;
            $bank_transaction->entry_by = intval(JFactory::getUser()->id);
            $bank_transaction->entry_date = date("Y-m-d");
            
            $db->insertobject("#__bank_transactions", $bank_transaction, "");
        }
        else
        {
            $bank_transaction = new stdClass();
        
            $bank_transaction->bank_account_id = $bank_account_id;
            $bank_transaction->transaction_date = $payment_date;
            $bank_transaction->amount = $amount;
            $bank_transaction->cleared = NO;
            $bank_transaction->item_type = CUSTOMER_PAYMENT;
            $bank_transaction->item_id = $payment_id;
            $bank_transaction->instrument = CHEQUE;
            $bank_transaction->cheque_no = $cheque_no;
            $bank_transaction->cheque_date = date("Y-m-d", strtotime($cheque_date));
            $bank_transaction->bank_id = $bank_id;
            $bank_transaction->entry_by = intval(JFactory::getUser()->id);
            $bank_transaction->entry_date = date("Y-m-d");
            
            // Bank Reconcilliation view removed
            
            $bank_transaction->cleared = YES;
            $bank_transaction->clearance_date = date("Y-m-d", strtotime($cheque_date));
            
            $db->insertobject("#__bank_transactions", $bank_transaction, "");
            
            //$query = "update `#__bank_accounts` set `uncleared_balance`=uncleared_balance+" . $amount . " where id=" . $bank_account_id;
            $query = "update `#__bank_accounts` set `balance`=balance+" . $amount . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "update `#__customers` set `account_balance`=account_balance-" . floatval($amount + $credit_amount) . " where id=" . $customer_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "select `customer_name` from `#__customers` where id=" . $customer_id;
        $db->setQuery($query);
        $customer_name = $db->loadResult();
        
        Functions::log_activity("Payment of " . $amount . "/- has been received from customer " . $customer_name . ".", "PC", $payment_id);
        return $payment_id;
    }
    
    function update_customer_payment()
    {   
        /*
        * entry from bank transaction is deleted and depending upon payment mode, either setting or bank account balance is reverted
        * $bank_transaction_cleared is always yes as cheque payment is set to cleared in add form
        * sales invoice(s) adjusted by this payment are reverted
        * payment entry is updated
        * revert_customer_payment is called to revert the adjustments of all the payment after this payment date so that this payment adjusts the sales invoice sequentially
        * adjust_customer_account is called to adjust this payment in pending sales invoice
        * paid amount is $amount but account balance is decremented by ($amount + $credit_amount)
        * cash payment mode alters cash_in_hand setting, record saved in bank transactions
        * cheque payment mode alters balance for selected bank account, record saved in bank transactions
        * cheque is assumed as cleared from bank
        */
        
        $db = JFactory::getDBO();
        
        $payment_id = intval(JRequest::getVar("payment_id"));
        
        $payment_date = date("Y-m-d", strtotime(JRequest::getVar("payment_date")));
        $customer_id = intval(JRequest::getVar("customer_id"));
        $payment_mode = intval(JRequest::getVar("payment_mode"));
        $cheque_no = JRequest::getVar("cheque_no");
        $cheque_date = JRequest::getVar("cheque_date");
        $bank_id = intval(JRequest::getVar("bank_id"));
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $amount = floatval(JRequest::getVar("amount"));
        $credit_amount = floatval(JRequest::getVar("credit_amount"));
        //$credit_reason = JRequest::getVar("credit_reason");
        $remarks = JRequest::getVar("remarks");
        
        $query = "select c.customer_name, p.amount_received, p.total_amount, p.payment_mode, p.bank_account_id, p.payment_date from `#__payments` p inner join `#__customers` c on p.party_id=c.id where p.id=" . $payment_id;
        $db->setQuery($query);
        $payment = $db->loadObject();
        $customer_name = $payment->customer_name;
        $old_payment_date = $payment->payment_date;
        
        $query = "select `cleared` from `#__bank_transactions` where item_type=" . CUSTOMER_PAYMENT . " and item_id=" . $payment_id;
        $db->setQuery($query);
        $bank_transaction_cleared = intval($db->loadResult());
        
        $query = "delete from `#__bank_transactions` where item_type=" . CUSTOMER_PAYMENT . " and item_id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
            
        if($payment->payment_mode == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric-" . floatval($payment->amount_received) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
        }
        else
        {
            if($bank_transaction_cleared)
            {
                $query = "update `#__bank_accounts` set `balance`=balance-" . floatval($payment->amount_received) . " where id=" . intval($payment->bank_account_id);
            }
            else
            {
                $query = "update `#__bank_accounts` set `uncleared_balance`=uncleared_balance-" . floatval($payment->amount_received) . " where id=" . intval($payment->bank_account_id);
            }
            
            
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "update `#__customers` set `account_balance`=account_balance+" . floatval($payment->total_amount) . " where id=" . $customer_id;
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
        
        $total_amount = $amount + $credit_amount;
        
        $payment = new stdClass();
        
        $payment->id = $payment_id;
        $payment->party_id = $customer_id;
        $payment->payment_date = $payment_date;
        $payment->payment_mode = $payment_mode;
        $payment->cheque_no = $cheque_no;
        $payment->cheque_date = ($cheque_date != "" ? date("Y-m-d", strtotime($cheque_date)) : "");
        $payment->bank_id = $bank_id;
        $payment->bank_account_id = $bank_account_id;
        $payment->amount_received = $amount;
        $payment->credit_amount = $credit_amount;
        $payment->total_amount = $total_amount;
        $payment->amount_adjusted = 0;
        //$payment->credit_reason = $credit_reason;
        $payment->payment_type = CUSTOMER_PAYMENT;
        $payment->remarks = $remarks;
        
        $db->updateObject("#__payments", $payment, "id");
        
        $query = "delete from `#__payment_items` where payment_id=" .$payment_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::revert_customer_payment($customer_id, $payment_id, $old_payment_date);
        Functions::adjust_customer_account($customer_id);
        
        if($payment_mode == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric+" . floatval($amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
            
            $bank_transaction = new stdClass();
        
            $bank_transaction->transaction_date = $payment_date;
            $bank_transaction->amount = $amount;
            $bank_transaction->cleared = YES;
            $bank_transaction->item_type = CUSTOMER_PAYMENT;
            $bank_transaction->item_id = $payment_id;
            $bank_transaction->instrument = CASH;
            $bank_transaction->entry_by = intval(JFactory::getUser()->id);
            $bank_transaction->entry_date = date("Y-m-d");
            
            $db->insertobject("#__bank_transactions", $bank_transaction, "");
        }
        else
        {
            $bank_transaction = new stdClass();
        
            $bank_transaction->bank_account_id = $bank_account_id;
            $bank_transaction->transaction_date = $payment_date;
            $bank_transaction->amount = $amount;
            $bank_transaction->cleared = NO;
            $bank_transaction->item_type = CUSTOMER_PAYMENT;
            $bank_transaction->item_id = $payment_id;
            $bank_transaction->instrument = CHEQUE;
            $bank_transaction->cheque_no = $cheque_no;
            $bank_transaction->cheque_date = date("Y-m-d", strtotime($cheque_date));
            $bank_transaction->bank_id = $bank_id;
            $bank_transaction->entry_by = intval(JFactory::getUser()->id);
            $bank_transaction->entry_date = date("Y-m-d");
            
            // Bank Reconcilliation view removed
            
            $bank_transaction->cleared = YES;
            $bank_transaction->clearance_date = date("Y-m-d", strtotime($cheque_date));
            
            $db->insertobject("#__bank_transactions", $bank_transaction, "");
            
            //$query = "update `#__bank_accounts` set `uncleared_balance`=uncleared_balance+" . $amount . " where id=" . $bank_account_id;
            $query = "update `#__bank_accounts` set `balance`=balance+" . $amount . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "update `#__customers` set `account_balance`=account_balance-" . floatval($amount + $credit_amount) . " where id=" . $customer_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Payment of " . $amount . "/- has been updated for customer " . $customer_name . ".", "PC", $payment_id);
        return $payment_id;
    }
    
    function delete_customer_account()
    {
        // customer can be deleted if his balance is 0 and there are no associated dependencies
        
        $db = JFactory::getDbo();
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        
        $query = "select opening_balance, account_balance from `#__customers` where id=" .$customer_id;
        $db->setQuery($query);
        $balances = $db->loadObject();
        
        $balance = abs( floatval($balances->opening_balance) - floatval($balances->account_balance) );
        
        if($balance > 0)
        {
            return "Customer's account cannot be deleted. Opening balance differs from account balance.";
        }
        
        $count = 0;
        
        $query = "select count(id) from `#__payments` where party_id=" . $customer_id . " and payment_type=" . CUSTOMER_PAYMENT;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__payment_items` where party_id=" . $customer_id . " and payment_type=" . CUSTOMER_PAYMENT; 
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_orders` where customer_id=" . $customer_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_invoice` where customer_id=" . $customer_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        /*$query = "select count(id) from `#__sales_returns` where customer_id=" . $customer_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());*/
        
        if($count > 0)
        {
            return "Unable to delete customer. It has dependencies.";
        }
        
        $query = "select `customer_name` from `#__customers` where id=" .$customer_id;
        $db->setQuery($query);
        $customer_name = $db->loadResult();
        
        $query = "delete from `#__customers` where id=" .$customer_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Customer " . $customer_name . " has been deleted.");
        return "Customer account deleted successfully.";
    }
    
    function get_bills()
    {
        // this function is called to get the list of vouchers adjusted in selected payment
        // adjustment can be for transporter payment, customer payment (payment or sales return)
        
        $db = JFactory::getDbo();
        
        $payment_id = intval(JRequest::getVar("payment_id"));
        $payment_type = JRequest::getVar("payment_type");
        $type = JRequest::getVar("type");
        
        if($type == 't')
        {
            $query = "select invoice_id, amount from `#__transporter_payment_items` where transporter_payment_id=" . $payment_id;
        }
        else
        {
            if($payment_type == 'P')
            {
                $query = "select invoice_id, amount from `#__payment_items` where payment_id=" . $payment_id;
            }
            else if($payment_type == 'SR')
            {
                $query = "select invoice_id, amount from `#__sales_return_adjustment_items` where sale_return_id=" . $payment_id;
            }
        }
        
        $db->setQuery($query);
        $bills = $db->loadObjectList();
        
        echo json_encode($bills);
    }
    
    function delete_customer_payment()
    {
        /*
        * apart from delete, sales invoices are reverted
        * entry from bank transaction is deleted and depending upon payment mode, either setting or bank account balance is reverted
        * revert_customer_payment, adjust_customer_account is called
        */
        
        $db = JFactory::getDbo();
        
        $payment_id = intval(JRequest::getVar("payment_id"));
        $customer_id = intval(JRequest::getVar("customer_id"));
        
        
        $query = "select c.customer_name, p.amount_received, p.total_amount, p.payment_mode, p.bank_account_id, p.payment_date from `#__payments` p inner join `#__customers` c on p.party_id=c.id where p.id=" . $payment_id;
        $db->setQuery($query);
        $payment = $db->loadObject();   
        
        $query = "delete from `#__payments` where id=" .$payment_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "select invoice_id sales_id, amount from `#__payment_items` where payment_id=" .$payment_id;
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
        
        $query = "select `cleared` from `#__bank_transactions` where item_type=" . CUSTOMER_PAYMENT . " and item_id=" . $payment_id;
        $db->setQuery($query);
        $bank_transaction_cleared = intval($db->loadResult());
        
        $query = "delete from `#__bank_transactions` where item_type=" . CUSTOMER_PAYMENT . " and item_id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
        
        if($payment->payment_mode == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric-" . floatval($payment->amount_received) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
        }
        else
        {
            if($bank_transaction_cleared)
            {
                $query = "update `#__bank_accounts` set `balance`=balance-" . floatval($payment->amount_received) . " where id=" . intval($payment->bank_account_id);
            }
            else
            {
                $query = "update `#__bank_accounts` set `uncleared_balance`=uncleared_balance-" . floatval($payment->amount_received) . " where id=" . intval($payment->bank_account_id);
            }
            
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "update `#__customers` set `account_balance`=account_balance+" . floatval($payment->total_amount) . " where id=" . $customer_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::revert_customer_payment($customer_id, $payment_id, $payment->payment_date);
        Functions::adjust_customer_account($customer_id);
        
        Functions::log_activity("Payment of " . floatval($payment->amount_received) . "/- from customer " . $payment->customer_name . " has been deleted.", "PC", $payment_id);
        return "Payment deleted successfully.";
    }
    
    function save_supplier_payment()
    {
        /*
        * entry in payments table with payment type SUPPLIER_PAYMENT
        * record inserted in bank transaction table
        * depending upon payment mode, either cash_in_hand setting or bank account balance changes
        * adjust_supplier_account is called
        */

        $db = JFactory::getDBO();
        
        $payment_date = date("Y-m-d", strtotime(JRequest::getVar("payment_date")));
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        $payment_mode = intval(JRequest::getVar("payment_mode"));
        $cheque_no = JRequest::getVar("cheque_no");
        $cheque_date = JRequest::getVar("cheque_date");
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $amount = floatval(JRequest::getVar("amount"));
        $remarks = JRequest::getVar("remarks");
        
        $total_amount = $amount;
        
        $payment = new stdClass();
        
        $payment->party_id = $supplier_id;
        $payment->payment_date = $payment_date;
        $payment->payment_mode = $payment_mode;
        $payment->cheque_no = $cheque_no;
        $payment->cheque_date = ($cheque_date != "" ? date("Y-m-d", strtotime($cheque_date)) : "");
        $payment->bank_account_id = $bank_account_id;
        $payment->total_amount = $total_amount;
        $payment->payment_type = SUPPLIER_PAYMENT;
        $payment->remarks = $remarks;
        $payment->entry_by = intval(JFactory::getUser()->id);
        $payment->entry_date = date("Y-m-d");
        
        $db->insertObject("#__payments", $payment, "");
        $payment_id = intval($db->insertid());
        
        Functions::adjust_supplier_account($supplier_id);
        
        if($payment_mode == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric-" . floatval($total_amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
            
            $bank_transaction = new stdClass();
        
            $bank_transaction->transaction_date = $payment_date;
            $bank_transaction->amount = $total_amount;
            $bank_transaction->cleared = YES;
            $bank_transaction->item_type = SUPPLIER_PAYMENT;
            $bank_transaction->item_id = $payment_id;
            $bank_transaction->instrument = CASH;
            $bank_transaction->entry_by = intval(JFactory::getUser()->id);
            $bank_transaction->entry_date = date("Y-m-d");
            
            $db->insertobject("#__bank_transactions", $bank_transaction, "");
        }
        else
        {
            $bank_transaction = new stdClass();
        
            $bank_transaction->bank_account_id = $bank_account_id;
            $bank_transaction->transaction_date = $payment_date;
            $bank_transaction->amount = $total_amount;
            $bank_transaction->cleared = YES;
            $bank_transaction->clearance_date = date("Y-m-d", strtotime($cheque_date));
            $bank_transaction->item_type = SUPPLIER_PAYMENT;
            $bank_transaction->item_id = $payment_id;
            $bank_transaction->instrument = CHEQUE;
            $bank_transaction->cheque_no = $cheque_no;
            $bank_transaction->cheque_date = date("Y-m-d", strtotime($cheque_date));
            $bank_transaction->entry_by = intval(JFactory::getUser()->id);
            $bank_transaction->entry_date = date("Y-m-d");
            
            $db->insertobject("#__bank_transactions", $bank_transaction, "");
            
            $query = "update `#__bank_accounts` set `balance`=balance-" . $total_amount . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "update `#__suppliers` set `account_balance`=account_balance-" . floatval($total_amount) . " where id=" . $supplier_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "select `supplier_name` from `#__suppliers` where id=" . $supplier_id;
        $db->setQuery($query);
        $supplier_name = $db->loadResult();
        
        Functions::log_activity("Payment of " . $total_amount . "/- has been made to supplier " . $supplier_name . ".", "PS", $payment_id);
        return "Payment saved successfully.";
    }
    
    function update_supplier_payment()
    {
        /*
        * bank transaction, purchase invoice adjustment, cash_in_hand setting or bank account balance is reverted
        * entry updated in payments table with payment type SUPPLIER_PAYMENT
        * record inserted in bank transaction table
        * depending upon payment mode, either cash_in_hand setting or bank account balance changes
        */
        
        $db = JFactory::getDBO();
        
        $payment_id = intval(JRequest::getVar("payment_id"));
        
        $payment_date = date("Y-m-d", strtotime(JRequest::getVar("payment_date")));
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        $payment_mode = intval(JRequest::getVar("payment_mode"));
        $cheque_no = JRequest::getVar("cheque_no");
        $cheque_date = JRequest::getVar("cheque_date");
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $amount = floatval(JRequest::getVar("amount"));
        $remarks = JRequest::getVar("remarks");
        
        $total_amount = $amount;
        
        $query = "select p.party_id, p.total_amount, p.payment_mode, p.bank_account_id, p.payment_date from `#__payments` p where p.id=" . $payment_id;
        $db->setQuery($query);
        $payment = $db->loadObject();
        $old_payment_date = $payment->payment_date;
        
        $query = "delete from `#__bank_transactions` where item_type=" . SUPPLIER_PAYMENT . " and item_id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
        
        if($payment->payment_mode == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric+" . floatval($payment->total_amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
        }
        else
        {
            $query = "update `#__bank_accounts` set `balance`=balance+" . floatval($payment->total_amount) . " where id=" . intval($payment->bank_account_id);
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "update `#__suppliers` set `account_balance`=account_balance+" . floatval($payment->total_amount) . " where id=" . intval($payment->party_id);
        $db->setQuery($query);
        $db->query();
        
        $query = "select invoice_id purchase_id, amount from `#__payment_items` where payment_id=" .$payment_id;
        $db->setQuery($query);
        $payment_items  = $db->loadObjectList();
        
        foreach($payment_items as $item)
        {
            $query = "update `#__purchase` set amount_paid=amount_paid-" . floatval($item->amount) . ", status=" . UNPAID . " where id=" . intval($item->purchase_id);
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__payment_items` set status=" . PART_PAYMENT . " where invoice_id=" . intval($item->purchase_id) . " and payment_type=" . SUPPLIER_PAYMENT;
            $db->setQuery($query);
            $db->query();
        }
        
        $payment = new stdClass();
        
        $payment->id = $payment_id;
        $payment->party_id = $supplier_id;
        $payment->payment_date = $payment_date;
        $payment->payment_mode = $payment_mode;
        $payment->cheque_no = ($cheque_no != "" ? $cheque_no : "");
        $payment->cheque_date = ($cheque_date != "" ? date("Y-m-d", strtotime($cheque_date)) : "");
        $payment->bank_account_id = $bank_account_id;
        $payment->total_amount = $total_amount;
        $payment->amount_adjusted = 0;
        $payment->payment_type = SUPPLIER_PAYMENT;
        $payment->remarks = $remarks;
        
        $db->updateObject("#__payments", $payment, "id");
        
        $query = "delete from `#__payment_items` where payment_id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::revert_supplier_payment($supplier_id, $payment_id, $old_payment_date);
        Functions::adjust_supplier_account($supplier_id);
        
        if($payment_mode == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric-" . floatval($total_amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
            
            $bank_transaction = new stdClass();
        
            $bank_transaction->transaction_date = $payment_date;
            $bank_transaction->amount = $total_amount;
            $bank_transaction->cleared = YES;
            $bank_transaction->item_type = SUPPLIER_PAYMENT;
            $bank_transaction->item_id = $payment_id;
            $bank_transaction->instrument = CASH;
            $bank_transaction->entry_by = intval(JFactory::getUser()->id);
            $bank_transaction->entry_date = date("Y-m-d");
            
            $db->insertobject("#__bank_transactions", $bank_transaction, "");
        }
        else
        {
            $bank_transaction = new stdClass();
        
            $bank_transaction->bank_account_id = $bank_account_id;
            $bank_transaction->transaction_date = $payment_date;
            $bank_transaction->amount = $total_amount;
            $bank_transaction->cleared = YES;
            $bank_transaction->clearance_date = date("Y-m-d", strtotime($cheque_date));
            $bank_transaction->item_type = SUPPLIER_PAYMENT;
            $bank_transaction->item_id = $payment_id;
            $bank_transaction->instrument = CHEQUE;
            $bank_transaction->cheque_no = $cheque_no;
            $bank_transaction->cheque_date = date("Y-m-d", strtotime($cheque_date));
            $bank_transaction->entry_by = intval(JFactory::getUser()->id);
            $bank_transaction->entry_date = date("Y-m-d");
            
            $db->insertobject("#__bank_transactions", $bank_transaction, "");
            
            $query = "update `#__bank_accounts` set `balance`=balance-" . $total_amount . " where id=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "update `#__suppliers` set `account_balance`=account_balance-" . floatval($total_amount) . " where id=" . $supplier_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "select `supplier_name` from `#__suppliers` where id=" . $supplier_id;
        $db->setQuery($query);
        $supplier_name = $db->loadResult();
        
        Functions::log_activity("Payment of " . $total_amount . "/- has been updated for supplier " . $supplier_name . ".", "PS", $payment_id);
        return "Payment updated successfully.";
    }
    
    function delete_supplier_payment()
    {
        /*
        * before deleting, bank transactions and purchase invoice adjustments are reverted
        * either setting or bank account balance is reverted depending upon payment mode
        * revert_supplier_payment is called to revert adjustments of payments after this date
        * adjust_supplier_account is called
        */
        
        $db = JFactory::getDbo();
        
        $payment_id = intval(JRequest::getVar("payment_id"));
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        
        $query = "select s.supplier_name, p.total_amount, p.payment_mode, p.bank_account_id, p.payment_date from `#__payments` p inner join `#__suppliers` s on p.party_id=s.id where p.id=" . $payment_id;
        $db->setQuery($query);
        $payment = $db->loadObject();   
        
        $query = "delete from `#__payments` where id=" .$payment_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "select invoice_id purchase_id, amount from `#__payment_items` where payment_id=" .$payment_id;
        $db->setQuery($query);
        $payment_items  = $db->loadObjectList();
        
        foreach($payment_items as $item)
        {
            $query = "update `#__purchase` set amount_paid=amount_paid-" . floatval($item->amount) . ", status=" . UNPAID . " where id=" . intval($item->purchase_id);
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__payment_items` set status=" . PART_PAYMENT . " where invoice_id=" . intval($item->purchase_id) . " and payment_type=" . SUPPLIER_PAYMENT;
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "delete from `#__payment_items` where payment_id=" .$payment_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__bank_transactions` where item_type=" . SUPPLIER_PAYMENT . " and item_id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
        
        if($payment->payment_mode == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric+" . floatval($payment->total_amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
        }
        else
        {
            $query = "update `#__bank_accounts` set `balance`=balance+" . floatval($payment->total_amount) . " where id=" . intval($payment->bank_account_id);
            $db->setQuery($query);
            $db->query();
        }
            
        $query = "update `#__suppliers` set `account_balance`=account_balance+" . floatval($payment->total_amount) . " where id=" . $supplier_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::revert_supplier_payment($supplier_id, $payment_id, $payment->payment_date);
        Functions::adjust_supplier_account($supplier_id);
        
        Functions::log_activity("Payment of " . floatval($payment->total_amount) . "/- for supplier " . $payment->supplier_name . " has been deleted.", "PS", $payment_id);
        return "Payment deleted successfully.";
    }
    
    function delete_supplier_account()
    {
        // supplier can be deleted if his balance is 0 and there are no associated dependencies
        
        $db = JFactory::getDbo();
        
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        
        $query = "select opening_balance, account_balance from `#__suppliers` where id=" .$supplier_id;
        $db->setQuery($query);
        $balances = $db->loadObject();
        
        $balance = abs( floatval($balances->opening_balance) - floatval($balances->account_balance) );
        
        if($balance > 0)
        {
            return "Supplier's account cannot be deleted. Opening balance differs from account balance.";
        }
        
        $count = 0;
        
        $query = "select count(id) from `#__payments` where party_id=" . $supplier_id . " and payment_type=" . SUPPLIER_PAYMENT;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__payment_items` where party_id=" . $supplier_id . " and payment_type=" . SUPPLIER_PAYMENT; 
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        /*$query = "select count(id) from `#__purchase_orders` where supplier_id=" . $supplier_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());*/
        
        $query = "select count(id) from `#__purchase` where supplier_id=" . $supplier_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        /*$query = "select count(id) from `#__purchase_returns` where supplier_id=" . $supplier_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());*/
        
        if($count > 0)
        {
            return "Unable to delete supplier. It has dependencies.";
        }
        
        $query = "select `supplier_name` from `#__suppliers` where id=" .$supplier_id;
        $db->setQuery($query);
        $supplier_name = $db->loadResult();
        
        $query = "delete from `#__suppliers` where id=" .$supplier_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Supplier " . $supplier_name . " has been deleted.");
        return "Supplier account deleted successfully.";
    }
    
    function save_transporter_payment()
    {
        /*
        * transporter payment is saved in jos_transporter_payments
        * purchase invoice is updated for the paid transportation_amount_paid and these adjustments are saved in jos_transporter_payment_items table
        * entry in cash expense table
        * cash in hand setting is updated
        * transporter's account balance is updated
        */
        $db = JFactory::getDbo();
        
        $payment_date = date("Y-m-d", strtotime(JRequest::getVar("payment_date")));
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        $amount = floatval(JRequest::getVar("amount")); 
        $balance_due = floatval(JRequest::getVar("balance_due"));
        $remarks = JRequest::getVar("remarks");
        
        $transporter_payment = new stdClass();
        $transporter_payment->transporter_id = $transporter_id;
        $transporter_payment->amount_paid = $amount;
        $transporter_payment->discount = $discount;
        $transporter_payment->total_amount = $total_amount;
        $db->insertObject("#__transporter_payments",$transporter_payment,""); 
        
        //Functions::adjust_transporter_payments_to_sales_invoices($payment_date, $transporter_id, $amount, $remarks); 
        
        Functions::log_activity("Payment of " . $amount . "/- has been made to transporter " . $transporter_id . ".", "PT", $payment_id);
        return "Payment saved successfully.";    
    }
    
    function update_transporter_payment()
    {
        /*
        * previous purchase invoice adjustment for transporter payment, cash expense table entry, cash in hand setting, transporter's account balance is reverted
        * then update is performed like add task
        */
        
        $db = JFactory::getDBO();
        
        $payment_id = intval(JRequest::getVar("payment_id"));
        $payment_date = date("Y-m-d", strtotime(JRequest::getVar("payment_date")));
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        $amount = floatval(JRequest::getVar("amount")); 
        $balance_due = floatval(JRequest::getVar("balance_due"));
        $remarks = JRequest::getVar("remarks");  
        
        Functions::delete_transporter_payments_of_sales_invoices($payment_id, $transporter_id);
        Functions::adjust_transporter_payments_to_sales_invoices($payment_date, $transporter_id, $amount, $remarks, $payment_id);
        
        Functions::log_activity("Payment of " . $amount . "/- has been updated for transporter " . $transporter_id . ".", "PT", $payment_id);
        return "Payment updated successfully."; 

        
        //$query = "select * from `#__sales_invoice` where transporter_id = ".$transporter_id;
//        $db->setQuery($query);
//        $sales_invoice_details = $db->loadObject();  
//        $total_amount = $amount;
//        
//        $query = "select t.transporter_name, p.total_amount from `#__transporter_payments` p inner join `#__transporters` t on p.transporter_id=t.id where p.id=" . $payment_id;
//        $db->setQuery($query);
//        $payment = $db->loadObject();
//        $transporter = $payment->transporter;
//        
//        $query = "select invoice_id sales_id, amount from `#__transporter_payment_items` where transporter_payment_id=" .$payment_id;
//        $db->setQuery($query);
//        $payment_items  = $db->loadObjectList();
//        
//        
//        foreach($payment_items as $item)
//        {
//            $query = "update `#__sales_invoice` set transportation_amount_paid=transportation_amount_paid-" . floatval($item->amount) . " where id=" . intval($item->sales_id);
//            $db->setQuery($query);
//            $db->query();
//            
//            $query = "update `#__transporter_payment_items` set status=" . PART_PAYMENT . " where invoice_id=" . intval($item->sales_id);
//            $db->setQuery($query);
//            $db->query();
//        }
//        $query = "delete from `#__transporter_payment_items` where transporter_payment_id=" . $payment_id;
//        $db->setQuery($query);
//        $db->query();
//        
//        $query = "delete from `#__cash_expenses` where item_id=" . $payment_id . " and item_type=" . TRANSPORTER_PAYMENT;
//        $db->setQuery($query);
//        $db->query();
//        
//        $query = "update `#__settings` set `value_numeric`=value_numeric+" . floatval($payment->total_amount) . " where `key`='cash_in_hand'";
//        $db->setQuery($query);
//        $db->query();
//            
//        $query = "update `#__transporters` set `account_balance`=account_balance+" . floatval($payment->total_amount) . " where id=" . $transporter_id;
//        $db->setQuery($query);
//        $db->query();
//    
//       if($sales_invoice_details->transporter_id == $sales_invoice_details->loading_transporter_id)
//        {
//            $query = "select (vehicle_rate + loading_amount - transportation_amount_paid) amount_pending, id sales_id from `#__sales_invoice` where transporter_id=" . $transporter_id . " and (vehicle_rate + loading_amount - transportation_amount_paid) > 0 order by date";
//            $db->setQuery($query);
//            $pending_amounts = $db->loadObjectlist();  
//        }
//        else{
//            $query = "select (vehicle_rate - transportation_amount_paid) amount_pending, id sales_id from `#__sales_invoice` where transporter_id=" . $transporter_id . " and (vehicle_rate - transportation_amount_paid) > 0 order by date";
//            $db->setQuery($query);
//            $pending_amounts = $db->loadObjectlist(); 
//        }
//       
//        
//        
//        $payment = new stdClass();
//        
//        $payment->id = $payment_id;
//        $payment->transporter_id = $transporter_id;
//        $payment->payment_date = $payment_date;
//        $payment->payment_type = CREDIT;
//        $payment->total_amount = $total_amount;
//        $payment->remarks = $remarks;
//        
//        $db->updateObject("#__transporter_payments", $payment, "id");
//         
//        foreach($pending_amounts as $amt)
//        {
//            if( ($amount == floatval($amt->amount_pending)) || (abs($amount - floatval($amt->amount_pending)) <= 1) )    
//            {             
//                $payment_items = new stdClass();
//                
//                $payment_items->transporter_id = $transporter_id;
//                $payment_items->invoice_id = intval($amt->sales_id);
//                $payment_items->transporter_payment_id = $payment_id;
//                $payment_items->amount = $amount;
//                $payment_items->status = FULL_PAYMENT;
//                
//                $db->insertObject("#__transporter_payment_items", $payment_items, "");
//                
//                $query = "update `#__transporter_payment_items` set status=" . FULL_PAYMENT . " where invoice_id=" . intval($amt->sales_id) . " and transporter_id=" . $transporter_id;
//                $db->setQuery($query);
//                $db->query();
//                
//                $query = "update `#__sales_invoice` set transportation_amount_paid=transportation_amount_paid+" . $amount . " where id=" . intval($amt->sales_id);
//                $db->setQuery($query);
//                $db->query();
//                
//                break;
//            }  
//                      
//            else if($amount < floatval($amt->amount_pending))
//            {
//                $payment_items = new stdClass();
//                
//                $payment_items->transporter_id = $transporter_id;
//                $payment_items->invoice_id = intval($amt->sales_id);
//                $payment_items->transporter_payment_id = $payment_id;
//                $payment_items->amount = $amount;
//                $payment_items->status = PART_PAYMENT;
//                
//                $db->insertObject("#__transporter_payment_items", $payment_items, "");
//                
//                $query = "update `#__sales_invoice` set transportation_amount_paid=transportation_amount_paid+" . $amount . " where id=" . intval($amt->sales_id);
//                $db->setQuery($query);
//                $db->query();
//                
//                break;
//            }
//            
//            else if($amount > floatval($amt->amount_pending))
//            {
//                $payment_items = new stdClass();
//                
//                $payment_items->transporter_id = $transporter_id;
//                $payment_items->invoice_id = intval($amt->sales_id);
//                $payment_items->transporter_payment_id = $payment_id;
//                $payment_items->amount = floatval($amt->amount_pending);
//                $payment_items->status = FULL_PAYMENT;
//                
//                $db->insertObject("#__transporter_payment_items", $payment_items, "");
//                
//                $amount -= floatval($amt->amount_pending);
//                
//                $query = "update `#__transporter_payment_items` set status=" . FULL_PAYMENT . " where invoice_id=" . intval($amt->sales_id) . " and transporter_id=" . $transporter_id;
//                $db->setQuery($query);
//                $db->query();
//                
//                $query = "update `#__sales_invoice` set transportation_amount_paid=transportation_amount_paid+" . floatval($amt->amount_pending) . " where id=" . intval($amt->sales_id);
//                $db->setQuery($query);
//                $db->query();
//            }
//        }
        
        //
//        $cash_expense = new stdClass();
//        
//        $cash_expense->expense_date = $payment_date;
//        $cash_expense->amount = $total_amount;
//        $cash_expense->item_type = TRANSPORTER_PAYMENT;
//        $cash_expense->item_id = $payment_id;
//        $cash_expense->description = $remarks;
//        $cash_expense->entry_by = intval(JFactory::getUser()->id);
//        $cash_expense->entry_date = date("Y-m-d");
//        
//        $db->insertobject("#__cash_expenses", $cash_expense, "");
//        
//        $query = "update `#__settings` set `value_numeric`=value_numeric-" . $total_amount . " where `key`='cash_in_hand'";
//        $db->setQuery($query);
//        $db->query();
//        
//        $query = "update `#__transporters` set `account_balance`=account_balance-" . $total_amount . " where id=" . $transporter_id;
//        $db->setQuery($query);
//        $db->query();
//        
//        Functions::log_activity("Payment of " . $total_amount . "/- has been updated for transporter " . $transporter . ".", "PT", $payment_id);
//        return "Payment updated successfully.";
    }
    
    function delete_transporter_payment()
    {
        /*
        * along with delete, purchase invoice adjustment for transporter payment, cash expense table entry, cash in hand setting, transporter's account balance is reverted
        */
        
        $payment_id = intval(JRequest::getVar("payment_id"));
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        
        Functions::delete_transporter_payments_of_sales_invoices($payment_id, $transporter_id);   
        
        Functions::log_activity("Payment of " . floatval($payment->total_amount) . "/- for transporter " . $payment->transporter . " has been deleted.", "PT", $payment_id);
        return "Payment deleted successfully.";
    }
    
    function delete_transporter_account()
    {   
        // transporter can be deleted if his balance is 0 and there are no associated dependencies
        
        $db = JFactory::getDBO();
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        
        $query = "select account_balance from `#__transporters` where id=" . $transporter_id;
        $db->setQuery($query);
        $balance = floatval($db->loadResult());

        if($balance > 0)
        {
            return "Transporter's account cannot be deleted. Account balance is greater than zero.";
        }
        
        $count = 0;
        
        /*$query = "select count(id) from `#__purchase` where transporter_id=" . $transporter_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            return "Unable to delete transporters. It has dependencies.";
        }*/

        $query = "select transporter_name from `#__transporters` where id=" . $transporter_id;
        $db->setQuery($query);
        $transporter = $db->loadResult();
        
        $query = "delete from `#__transporters` where `id`=" . $transporter_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Transporter " . $transporter . " has been deleted."); 
        return "Transporter deleted successfully.";
    }
    
    
    
     
   
    
}
?>