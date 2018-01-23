<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewSales_and_payments extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * this view can be viewed from customer account
        * It shows the list of all the sales invoices and payments for that customer
        * payments includes payments and sales returns 
        * payments can be edited/deleted only for the users mentioned in users_allowed_backdate_payments settings
        * items can be viewed for sales return
        * by default last 2 months old bills which are unpaid are shown. Complete list can be viewed by "show all bills button"
        * by default payments and sales returns which are not fully adjusted are shown. Complete list can be viewed by "show all payments button"
        * sales invoice edit/delete/print option is provided
        * most of the options are provided only if the customer's account is active
        * unpaid bills total can be viewed by clicking on the checkbox provided
        * unpaid bills are red and paid are black
        * on clicking on payment section rows, all the sales invoices that were adjusted in that payment (payment or sales return) are highlighted by different color depending on whether the it was fully or partly adjusted
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Sales and Payments");
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        $show_all_bills = JRequest::getVar("bill");
        $show_all_pays = JRequest::getVar("pay");
        
        
        $query = "select `account_status` from `#__customers` where id=" . $customer_id;
        $db->setQuery($query);
        $this->customer_account_status = intval($db->loadResult());
        
        $this->customer_id = $customer_id;
        $this->show_all_bills = $show_all_bills;
        $this->show_all_pays = $show_all_pays;
        
        //$query = "select p.*, p.id payment_id, b.bank_name cheque_bank, ba.account_name, ba.bank_name from `#__payments` p left join `#__banks` b on p.bank_id=b.id left join `#__bank_accounts` ba on p.bank_account_id=ba.id where p.party_id=" . $customer_id . " and p.payment_type=" . CUSTOMER_PAYMENT . " order by p.payment_date asc, p.id";
        
        $query1 = "select 'P' type, p.id payment_id, p.payment_date, p.payment_mode, p.cheque_no, p.cheque_date, p.amount_received, p.credit_amount, p.total_amount, p.remarks, p.cash_invoice, b.bank_name cheque_bank, ba.account_name, ba.bank_name from `#__payments` p left join `#__banks` b on p.bank_id=b.id left join `#__bank_accounts` ba on p.bank_account_id=ba.id where p.party_id=" . $customer_id . " and p.payment_type=" . CUSTOMER_PAYMENT . ($show_all_pays == "" ? " and total_amount>amount_adjusted" : "");
        //$query2 = "select 'SR' type, sr.id payment_id, sr.bill_date payment_date, " . CASH . " payment_mode, '' cheque_no, '' cheque_date, sr.amount_adjusted amount_received, '' credit_amount, sr.amount_adjusted total_amount, 'Sale Return' remarks, '0' cash_invoice, '' cheque_bank, '' account_name, '' bank_name from `#__sales_returns` sr where sr.customer_id=" . $customer_id . " and sr.amount_adjusted>0" . ($show_all_pays == "" ? " and bill_amount>amount_adjusted" : "");
        
        $query = $query1 . " order by payment_date asc";
        $db->setQuery($query);
        $payments = $db->loadObjectList();        
        $this->payments = $payments;
        
        $query = "select s.*, s.id sales_id, s.date bill_date from `#__sales_invoice` s where s.customer_id=" . $customer_id . ($show_all_bills == "" ? " and s.status=" . UNPAID : "") . " order by s.date asc, s.id";
        $db->setQuery($query);
        $bills = $db->loadObjectList();
        //print_r($bills);exit;
        
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
        
        // Payment Adjustments
        
        $query = "select opening_balance from `#__customers` where id=" . $customer_id;
        $db->setQuery($query);
        $account_balance = floatval($db->loadResult());
        
        
        $query = "select sum(total_amount) from `#__sales_invoice` where customer_id=" . $customer_id;
        $db->setQuery($query);
        $account_balance += floatval($db->loadResult());
        
        
        $query = "select sum(total_amount) from `#__payments` where party_id=" . $customer_id . " and payment_type=" . CUSTOMER_PAYMENT;
        $db->setQuery($query);
        $account_balance -= floatval($db->loadResult());
        
         

        /*$query = "select sum(bill_amount) from `#__sales_returns` where customer_id=" . $customer_id;
        $db->setQuery($query);
        $account_balance -= floatval($db->loadResult());*/
        
        if($account_balance <= 0)
        {
            $outstanding_bill_found = NO;
        }
        else
        {
            $outstanding_bill_found = YES;
            $bills = array_reverse($bills);
            
            foreach ($bills as $bill)
            {
                if($account_balance == 0)
                {
                    $bill->amount_paid = $bill->total_amount; 
                    $bill->status = PAID;
                }
                else
                {
                    if($bill->total_amount <= $account_balance)
                    {
                        if($bill->total_amount == $account_balance)
                        {
                            $account_balance = 0;
                        }
                        else
                        {
                            $account_balance -= $bill->total_amount;
                        }
                        $bill->status = UNPAID;
                    }
                    else
                    {
                        $bill->amount_paid = $bill->total_amount - $account_balance;
                        $account_balance = 0;
                        $bill->status = UNPAID;
                    }
                }
            }
            $bills = array_reverse($bills);
        }
        
        $this->outstanding_bill_found = $outstanding_bill_found;
        $this->bills = $bills;
        
        //print_r($bills);exit;
        
        parent::display($tpl);
    } 
}
?>