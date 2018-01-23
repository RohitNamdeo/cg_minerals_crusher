<?php
jimport( 'joomla.application.component.view');

class HrViewPaid_salary_report extends JViewLegacy
{
    function display($tpl = null)
    {
        // salary report which shows paid and balance amount of salary for any month
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        if (!Functions::has_permissions("hr", "paid_salary_report"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        $document = JFactory::getDocument();
        $document->setTitle("Paid Salary Report");
        
        $month = intval(JRequest::getVar("month"));
        $year = intval(JRequest::getVar("year"));
        $location_id = intval(JRequest::getVar("location_id"));
        $employee_id = intval(JRequest::getVar("employee_id"));
        
        $salary_details = array();
        
        if($month == 0 || $year == 0)
        {
            $query = "select max(salary_year) from `#__hr_salary`";
            $db->setQuery($query);
            $year = intval($db->loadResult());
            
            if($year != 0)
            {
                $query = "select max(salary_month) from `#__hr_salary` where salary_year=" . $year;
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
        
        $condition = "";        
        if($month != 0 && $year != 0)
        {
            $condition .= "(s.salary_year = " . $year . " and s.salary_month = " . $month;
        }
        
        if($employee_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "(") . "(s.employee_id=" . $employee_id . ")";
        }
        
        if($location_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "(") . "e.location_id=" . $location_id;
        }
        
        $condition .= ($condition != "" ? ")" : "");
        
        $query = "select s.*, e.employee_name, e.id employee_id, e.doj from `#__hr_salary` s inner join `#__hr_employees` e on s.employee_id=e.id " . ($condition != "" ? " where " . $condition : "") . " order by e.employee_name";
        $db->setQuery($query);
        $salary_details = $db->loadObjectList();
        
        $query = "select * from `#__inventory_locations` order by `location_name`"; 
        $db->setQuery($query); 
        $locations = $db->loadObjectList();
        $this->locations = $locations;
        
        $query = "select e.id employee_id, e.employee_name from `#__hr_employees` e where e.account_status=" . AC_ACTIVE . " order by e.employee_name";
        $db->setQuery($query);
        $employees = $db->loadObjectList();        
        $this->employees = $employees;
        
        $this->salary_details = $salary_details;
        
        $this->month = $month;
        $this->year = $year;
        $this->location_id = $location_id;
        $this->employee_id = $employee_id;
        
        parent::display($tpl);
    }
}
?>