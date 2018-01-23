<?php
jimport( 'joomla.application.component.view');

class HrViewSalary_payment_report extends JViewLegacy
{
    function display($tpl = null)
    {
        /* view for edit/delete/list display of salary payments */
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "salary_payment_report"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $document = JFactory::getDocument();
        $document->setTitle("Salary Payment Report");
        
        $employee_id = intval(JRequest::getVar("employee_id"));
        $location_id = intval(JRequest::getVar("location_id"));
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        $month = intval(JRequest::getVar("month"));
        $year = intval(JRequest::getVar("year"));
        
        if($month == 0 && $year == 0 && $from_date == "" && $to_date == "")
        {
            $query = "select max(salary_year) from `#__hr_salary_payments`";
            $db->setQuery($query);
            $year = intval($db->loadResult());
            
            if($year != 0)
            {
                $query = "select max(salary_month) from `#__hr_salary_payments` where salary_year=" . $year;
                $db->setQuery($query);
                $month = intval($db->loadResult());
            }
            else
            {
                $previous_month = date("Y-m", strtotime("-1 month", strtotime(date("Y-m-d"))));
                $month = date("m", strtotime($previous_month));
                $year = date("Y", strtotime($previous_month));
            }
        }
        
        $this->employee_id = $employee_id;
        $this->location_id = $location_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->month = $month;
        $this->year = $year;
        
        $condition = "";
        
        if($employee_id != 0)
        { $condition .= ($condition != "" ? " and " : "" ) . "(pi.employee_id=" . $employee_id . ")"; }
        if($location_id != 0)
        { $condition .= ($condition != "" ? " and " : "" ) . "(e.location_id=" . $location_id . ")"; }
        
        if($from_date != "" && $to_date != "")
        {
            $from_date = date("Y-m-d", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));
            
            $condition .= ($condition != "" ? " and " : "" ) . "(p.payment_date between '" . $from_date . "' and '" . $to_date . "')";
        }
        
        if($month != 0)
        {  $condition .= ($condition != "" ? " and " : "" ) . "(p.salary_month=" . $month . ")"; }
        if($year != 0)
        {  $condition .= ($condition != "" ? " and " : "" ) . "(p.salary_year=" . $year . ")"; }
        
        $query = "select pi.*, p.salary_month, p.salary_year, p.payment_date, e.employee_name, e.id employee_id, l.location_name, b.bank_name from `#__hr_salary_payment_items` pi inner join `#__hr_salary_payments` p on pi.payment_id=p.id inner join `#__hr_employees` e on pi.employee_id=e.id inner join `#__inventory_locations` l on e.location_id=l.id left join `#__banks` b on pi.instrument_bank=b.id where " . $condition . " order by p.payment_date desc, e.employee_name";
        $db->setQuery($query);
        $salary_payments = $db->loadObjectList(); 
        $this->salary_payments = $salary_payments;
        
        $query = "select * from `#__inventory_locations` order by `location_name`"; 
        $db->setQuery($query); 
        $locations = $db->loadObjectList();
        $this->locations = $locations;
        
        $query = "select id, employee_name from `#__hr_employees` order by employee_name"; 
        $db->setQuery($query); 
        $employees = $db->loadObjectList();
        $this->employees = $employees;

        parent::display($tpl);
    }
}
?>