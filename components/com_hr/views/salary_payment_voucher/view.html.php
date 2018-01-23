<?php
jimport( 'joomla.application.component.view');

class HrViewSalary_payment_voucher extends JViewLegacy
{
    function display($tpl = null)
    {
        /* salary can be paid in parts */
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "salary_payment_voucher"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $document = JFactory::getDocument();
        $document->setTitle("Salary Payment Voucher");
        
        $month = intval(JRequest::getVar("month"));
        $year = intval(JRequest::getVar("year"));
        
        $mode = JRequest::getVar('m');
        
        $this->month = $month;
        $this->year = $year;
        
        $salary_vouchers = array();
        
        if($mode == 'e')
        {
            $payment_id = intval(JRequest::getVar("payment_id"));
            
            $query = "select payment_date, salary_month, salary_year from `#__hr_salary_payments` where id=" . $payment_id;
            $db->setQuery($query);
            $voucher = $db->loadObject();
            
            $query = "select p.*, e.employee_name, e.id employee_id from `#__hr_salary_payment_items` p inner join `#__hr_employees` e on p.employee_id=e.id where p.payment_id=" . $payment_id . " order by e.employee_name";
            $db->setQuery($query);
            $salary_vouchers = $db->loadObjectList();
            
            foreach($salary_vouchers as $key=>$salary)
            {
                $query = "select (total_salary - paid_salary) from `#__hr_salary` where employee_id=" . intval($salary->employee_id) . " and salary_month=" . intval($voucher->salary_month) . " and salary_year=" . intval($voucher->salary_year);
                $db->setQuery($query);
                $salary_vouchers[$key]->eligible_payment_amount = floatval(($db->loadResult())) + floatval($salary->amount);
            }
            
            $this->payment_id = $payment_id;
            $this->voucher = $voucher;
        }
        else
        {
            if($month != 0 && $year != 0)
            {
                $condition = "(s.salary_year = " . $year . " and s.salary_month = " . $month . " and (s.total_salary - s.paid_salary)>0)";
                
                $query = "select s.id, s.employee_id, (s.total_salary - s.paid_salary) salary, e.employee_name, e.id employee_id from `#__hr_salary` s inner join `#__hr_employees` e on s.employee_id=e.id where " . $condition . " order by e.employee_name";
                $db->setQuery($query);
                $salary_vouchers = $db->loadObjectList();
            }
        }
        
        $query = "select id, bank_name from `#__banks` order by bank_name";
        $db->setQuery($query);
        $banks = $db->loadObjectList();
        $this->banks = $banks;

        $this->salary_vouchers = $salary_vouchers;
        
        if($mode == 'e')
        { parent::display("edit"); }
        else
        { parent::display($tpl); }
    }
}
?>