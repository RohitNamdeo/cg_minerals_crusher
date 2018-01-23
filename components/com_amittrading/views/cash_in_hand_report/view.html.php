<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewCash_in_hand_report extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * this is an account statement for payment mode cash only
        * it includes cash expense(cash expense or transporter payment), customer, supplier, salary, advance payment and bank transaction (cash deposit or withdrawal)
        * opening calculation considers opening_cash_in_hand setting
        */

        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "cash_in_hand_report"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Cash In Hand Report");
        
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
        
        $condition1 = "(ce.expense_date between '" . $from_date . "' and '" . $to_date . "')";
        $condition2 = "(p.payment_date between '" . $from_date . "' and '" . $to_date . "')";
        $condition3 = "(bt.transaction_date between '" . $from_date . "' and '" . $to_date . "')";
        
        $query1 = "select 'debit' type, ce.expense_date date, 'Cash Expense' item_type, ce.amount, ce.description particulars, '' bank_account from `#__cash_expenses` ce where " . $condition1 . " and ce.item_type=0";
        $query2 = "select 'debit' type, ce.expense_date date, 'Transporter Payment' item_type, ce.amount, concat('Transporter : ', t.transporter_name) particulars, '' bank_account from `#__cash_expenses` ce inner join `#__transporter_payments` tp on ce.item_id=tp.id inner join `#__transporters` t on tp.transporter_id=t.id where " . $condition1 . " and ce.item_type=" . TRANSPORTER_PAYMENT;
        $query3 = "select 'credit' type, p.payment_date date, 'Customer Payment' item_type, p.amount_received amount, concat('Customer : ', c.customer_name) particulars, '' bank_account from `#__payments` p inner join `#__customers` c on p.party_id=c.id where " . $condition2 . " and p.payment_mode=" . CASH . " and p.payment_type=" . CUSTOMER_PAYMENT;
        $query4 = "select 'debit' type, p.payment_date date, 'Supplier Payment' item_type, p.total_amount amount, concat('Supplier : ', s.supplier_name) particulars, '' bank_account from `#__payments` p inner join `#__suppliers` s on p.party_id=s.id where " . $condition2 . " and p.payment_mode=" . CASH . " and p.payment_type=" . SUPPLIER_PAYMENT;
        $query5 = "select 'debit' type, p.payment_date date, 'Salary Payment' item_type, pi.amount, concat('Employee : ', e.employee_name, ', Month : ', DATE_FORMAT(concat(p.salary_year, ',', p.salary_month, ',01'), '%M\'%Y')) particulars, '' bank_account from `#__hr_salary_payments` p inner join `#__hr_salary_payment_items` pi on p.id=pi.payment_id inner join `#__hr_employees` e on pi.employee_id=e.id where " . $condition2 . " and pi.instrument=" . CASH;
        $query6 = "select 'debit' type, p.payment_date date, 'Advance Payment' item_type, p.amount, concat('Employee : ', e.employee_name) particulars, '' bank_account from `#__hr_advance_salary_payments` p inner join `#__hr_employees` e on p.employee_id=e.id where " . $condition2 . " and p.instrument=" . CASH;
        $query7 = "select (CASE bt.transaction_type WHEN " . CASH_DEPOSIT . " THEN 'debit' ELSE 'credit' END) type, bt.transaction_date date, (CASE bt.transaction_type WHEN " . CASH_DEPOSIT . " THEN 'Cash Deposit' ELSE 'Cash Withdrawal' END) item_type, bt.amount, bt.description particulars, concat(ba.account_name, ', ', ba.bank_name) bank_account from `#__bank_transactions` bt inner join `#__bank_accounts` ba on bt.bank_account_id=ba.id where " . $condition3 . " and (bt.transaction_type=" . CASH_DEPOSIT . " or bt.transaction_type=" . CASH_WITHDRAW . ")";
        
        $query = $query1 . " union " . $query2  . " union " . $query3 . " union " . $query4 . " union " . $query5 . " union " . $query6 . " union " . $query7 . " order by date";
        $db->setQuery($query);
        $cash_statements = $db->loadObjectList();
        $this->cash_statements = $cash_statements;
                
        /*Opening Balance Calculation*/
        
        $query = "select `value_numeric` from `#__settings` where `key`='opening_cash_in_hand'";
        $db->setQuery($query);
        $opening_cash_in_hand = floatval($db->loadResult());
        
        $query = "select sum(amount) from `#__cash_expenses` where expense_date < '" . $from_date . "'";
        $db->setQuery($query);
        $cash_expense = floatval($db->loadResult());
        
        $query = "select sum(amount_received) from `#__payments` where payment_type=" . CUSTOMER_PAYMENT . " and payment_mode=" . CASH . " and payment_date < '" . $from_date . "'";
        $db->setQuery($query);
        $customer_payment = floatval($db->loadResult());
        
        $query = "select sum(total_amount) from `#__payments` where payment_type=" . SUPPLIER_PAYMENT . " and payment_mode=" . CASH . " and payment_date < '" . $from_date . "'";
        $db->setQuery($query);
        $supplier_payment = floatval($db->loadResult());
        
        $query = "select sum(pi.amount) from `#__hr_salary_payments` p inner join `#__hr_salary_payment_items` pi on p.id=pi.payment_id where pi.instrument=" . CASH . " and p.payment_date < '" . $from_date . "'";
        $db->setQuery($query);
        $salary_payment = floatval($db->loadResult());
        
        $query = "select sum(amount) from `#__hr_advance_salary_payments` where instrument=" . CASH . " and payment_date < '" . $from_date . "'";
        $db->setQuery($query);
        $advance_payment = floatval($db->loadResult());
        
        $query = "select sum(amount) from `#__bank_transactions` where transaction_type=" . CASH_DEPOSIT . " and transaction_date < '" . $from_date . "'";
        $db->setQuery($query);
        $cash_deposit = floatval($db->loadResult());
        
        $query = "select sum(amount) from `#__bank_transactions` where transaction_type=" . CASH_WITHDRAW . " and transaction_date < '" . $from_date . "'";
        $db->setQuery($query);
        $cash_withdrawal = floatval($db->loadResult());
        
        $opening_balance = $opening_cash_in_hand - $cash_expense + $customer_payment - $supplier_payment - $salary_payment - $advance_payment - $cash_deposit + $cash_withdrawal;
        $this->opening_balance = $opening_balance;
        
        /*Opening Balance Calculation*/
        
        parent::display($tpl);
    } 
}
?>