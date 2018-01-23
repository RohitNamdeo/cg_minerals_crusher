<?php
jimport( 'joomla.application.component.view');

class HrViewAdvance_deduction_report extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * report to show the list of employees whose salary has been deducted
        * advance is deducted from their salary
        * this deduction can be edited if salary has not been paid
        */
        
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        if (!Functions::has_permissions("hr", "advance_deduction_report"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        $document = JFactory::getDocument();
        $document->setTitle("Advance Deduction Report");
        
        $month = intval(JRequest::getVar("month"));
        $year = intval(JRequest::getVar("year"));
        
        $advance_deductions = array();
        
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
        
        $condition = "(s.advance_deduction>0)";
        if($month != 0 && $year != 0)
        {
            $condition .=  ($condition != "" ? " and " : "") . "(s.salary_year = " . $year . " and s.salary_month = " . $month;
        }
        
        $condition .= ($condition != "" ? ")" : "");
        
        $query = "select s.*, s.id salary_id, e.employee_name from `#__hr_salary` s inner join `#__hr_employees` e on s.employee_id=e.id " . ($condition != "" ? " where " . $condition : "") . " order by e.employee_name";
        $db->setQuery($query);
        $advance_deductions = $db->loadObjectList();        
        $this->advance_deductions = $advance_deductions;
        
        $this->month = $month;
        $this->year = $year;
        
        parent::display($tpl);
    }
}
?>