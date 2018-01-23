<?php
jimport( 'joomla.application.component.view');

class HrViewAdvance_salary_payment_voucher extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * advance payment voucher for single employee
        * employee can be paid advance greater than his eligibilty
        * Eligible amount is calculated on the basis of attendance between two dates
        * start date is 1st date of month after last month of salary generation
        * end date is current date
        */
        
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "advance_salary_payment_voucher"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $document = JFactory::getDocument();
        $document->setTitle("Advance Salary Payment Voucher");
        
        $mode = JRequest::getVar('m');
        
        $query = "select id, employee_name, gross_salary from `#__hr_employees` order by `employee_name`";
        $db->setQuery($query);
        $employees = $db->loadObjectList();           
        
        $query = "select max(salary_year) from `#__hr_salary`";
        $db->setQuery($query);
        $year = intval($db->loadResult());
        
        $condition = "";
        $total_days = 0;
        if($year != 0)
        {
            $query = "select max(salary_month) from `#__hr_salary` where salary_year=" . $year;
            $db->setQuery($query);
            $month = intval($db->loadResult());
            
            $last_salary_month_date = date("Y-m-d", strtotime("+ 1 month", strtotime($year . '-' . $month . '-01')));
            $condition = "(attendance_date>='" . $last_salary_month_date . "')";
        }
        else
        {
            $query = "select attendance_date from `#__hr_employee_daily_attendance` order by attendance_date asc limit 1";
            $db->setQuery($query);
            $last_salary_month_date = $db->loadResult();
            
            if($last_salary_month_date == "")
            { $last_salary_month_date = date("Y-m-d"); }
        }
        
        $total_days = (strtotime(date("Y-m-d")) - strtotime($last_salary_month_date)) / (60*60*24);
        
        foreach($employees as $key=>$employee)
        {
            $query = "select sum(attendance) from `#__hr_employee_daily_attendance_items` where employee_id=" . intval($employee->id) . ($condition != "" ? " and " . $condition : "");
            $db->setQuery($query);
            $attendance = floatval($db->loadResult());
            
            if($total_days == 0)
            {
                $employees[$key]->eligible_amount = 0;
            }
            else
            {
                $employees[$key]->eligible_amount = ($employee->gross_salary * $attendance) / $total_days;
            }
            
            $query = "select sum(amount-amount_cleared) from `#__hr_advance_salary_payments` where employee_id=" . intval($employee->id) . " and cleared=" . NO;
            $db->setQuery($query);
            $employees[$key]->eligible_amount -= floatval($db->loadResult());
        }
        
        $this->employees = $employees;
        
        $query = "select id, bank_name from `#__banks` order by bank_name";
        $db->setQuery($query);
        $banks = $db->loadObjectList();
        $this->banks = $banks;
            
        if($mode == 'e')
        {
            $advance_id = intval(JRequest::getVar("advance_id"));
            
            $query = "select * from `#__hr_advance_salary_payments` where id=" . $advance_id;
            $db->setQuery($query);
            $advance = $db->loadObject();
            
            $this->advance = $advance;
            $this->advance_id = $advance_id;
            
            parent::display("edit");
        }
        else
        {    
            parent::display($tpl);
        }
    }
}
?>