<?php
jimport( 'joomla.application.component.view');

class HrViewEmployee_salary_history extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * view in employee's account for his salary history
        */
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $employee_id = intval(JRequest::getVar("employee_id"));
        
        $from_month = intval(JRequest::getVar("from_month"));
        $from_year = intval(JRequest::getVar("from_year"));
        $to_month = intval(JRequest::getVar("to_month"));
        $to_year = intval(JRequest::getVar("to_year"));
        
        $salary_details = array();
        
        if($from_month == 0 || $from_year == 0 || $to_month == 0 || $to_year == 0)
        {
            $from_month = 4;
            $to_month = date("m");
            $to_year = date("Y");
            
            $from_year = ($to_month < 4 ? $to_year - 1 : $to_year);
        }
        
        $condition = "";
        $condition .= "(s.employee_id = " . $employee_id . ")";
        
        if($from_month != 0 && $from_year != 0 && $to_month != 0 && $to_year != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(DATE_FORMAT(concat(s.salary_year, '-', s.salary_month, '-01'),'%Y-%m-%d') between '" . date("Y-m-01", strtotime($from_year . '-' . $from_month . '-01')) ."' and '" . date("Y-m-01", strtotime($to_year . '-' . $to_month . '-01')) . "')";
        }
        
        $query = "select s.* from `#__hr_salary` s where " . $condition . " order by s.salary_month, s.salary_year desc";
        $db->setQuery($query);
        $salary_details = $db->loadObjectList();
        
        $this->salary_details = $salary_details;
        $this->employee_id = $employee_id;
        $this->from_month = $from_month;
        $this->from_year = $from_year;
        $this->to_month = $to_month;
        $this->to_year = $to_year;
        
        parent::display($tpl);
    }
}
?>