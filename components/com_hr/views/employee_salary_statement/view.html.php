<?php
defined ("_JEXEC") or die("Restricted Access");
jimport( 'joomla.application.component.view');

class HrViewEmployee_salary_statement extends JViewLegacy
{
    function display($tpl = null)
    {
        // view in employee's account, salary generated and its payment is shown as account statement
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDBO();

        $employee_id = intval(JRequest::getVar("employee_id"));
        
        $from_month = intval(JRequest::getVar("from_month"));
        $from_year = intval(JRequest::getVar("from_year"));
        $to_month = intval(JRequest::getVar("to_month"));
        $to_year = intval(JRequest::getVar("to_year"));
        
        $condition1 = "";
        $condition2 = "";
        
        if($from_month == 0 || $from_year == 0 || $to_month == 0 || $to_year == 0)
        {
            $from_month = 4;
            $to_month = date("m");
            $to_year = date("Y");
            
            $from_year = ($to_month < 4 ? $to_year - 1 : $to_year);
        }
        
        if($from_month != 0 && $from_year != 0 && $to_month != 0 && $to_year != 0)
        {
            $condition1 = " and (DATE_FORMAT(p.payment_date, '%Y-%m') between '" . date("Y-m", strtotime($from_year . '-' . $from_month)) ."' and '" . date("Y-m", strtotime($to_year . '-' . $to_month)) . "')";
            $condition2 = " and s.salary_month between " . $from_month . " and " . $to_month . " and s.salary_year between " . $from_year . " and " . $to_year;
        }
        
        $query = "select coalesce(sum(pi.amount),0) from `#__hr_salary_payment_items` pi inner join `#__hr_salary_payments` p on pi.payment_id=p.id where pi.employee_id=" . $employee_id . ($from_month != "" && $from_year != "" ? " and DATE_FORMAT(p.payment_date, '%Y-%m')<'" . date("Y-m", strtotime($from_year . '-' . $from_month)) ."'" : "") . "";
        $db->setQuery($query);
        $debit_amount = floatval($db->loadResult());

        $query = "select sum(s.total_salary) from `#__hr_salary` s where s.employee_id=" . $employee_id . ($from_month != "" && $from_year != "" ? " and s.salary_month<" . intval($from_month) . " and s.salary_year<=" . $from_year : "");
        $db->setQuery($query);
        $credit_amount = floatval($db->loadResult());
                
        $opening_balance = $debit_amount - $credit_amount;

        $query1 = "select 0 is_last_day, p.payment_date date, 'Salary Payment' particulars, (CASE pi.instrument WHEN " . CASH . " THEN 'Cash' WHEN " . CHEQUE . " THEN 'Cheque' END) instrument, pi.instrument_no, b.bank_name instrument_bank, concat(DATE_FORMAT(p.salary_month, '%M'), '\'', p.salary_year) statement_month, pi.amount debit, '' credit from `#__hr_salary_payment_items` pi inner join `#__hr_salary_payments` p on pi.payment_id=p.id left join `#__banks` b on pi.instrument_bank=b.id where pi.employee_id=" . $employee_id  . $condition1;
        $query2 = "select 1 is_last_day, DATE_FORMAT(concat(s.salary_year, ',', s.salary_month, ',27'), '%Y-%m-%d') date, concat('Salary for the month of ', DATE_FORMAT(STR_TO_DATE(concat(s.salary_year,',', s.salary_month), '%Y,%m'), '%M\'%Y')) particulars, '' instrument, '' instrument_no, '' instrument_bank, DATE_FORMAT(STR_TO_DATE(concat(s.salary_year,',', s.salary_month), '%Y,%m'), '%M\'%Y') statement_month, '' debit, (s.total_salary) credit from `#__hr_salary` s where s.employee_id=" . $employee_id . $condition2;
        
        $query = $query1 . " union all " . $query2 . " order by date";
        $db->setQuery($query);
        $salary_statement = $db->loadObjectList();
        
        $this->employee_id = $employee_id;
        $this->from_month = $from_month;
        $this->from_year = $from_year;
        $this->to_month = $to_month;
        $this->to_year = $to_year;
        $this->salary_statement = $salary_statement;
        $this->opening_balance = $opening_balance;
        
        parent::display($tpl);
    }
}
?>