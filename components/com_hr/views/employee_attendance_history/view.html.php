<?php
jimport('joomla.application.component.view');

class HrViewEmployee_attendance_history extends JViewLegacy
{
    function display($tpl = null)
    {
        /* view in employee's account for his attendance history */
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db= JFactory::getDBO();
        
        $employee_id = intval(JRequest::getVar("employee_id"));
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        
        if($from_date == "" && $to_date == "")
        {
            $from_date = date("Y-m-01");
            $to_date = date("Y-m-d");
        }
        else
        {
            $from_date = date("Y-m-d", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));
        }
        
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        $attendance = array();
        
        $query = "select da.attendance, da.attendance_date from `#__hr_employee_daily_attendance_items` da  where da.attendance_date between '" . $from_date . "' and '" . $to_date . "' and da.employee_id=" . $employee_id;
        $db->setQuery($query);
        $attendance = $db->loadObjectList("attendance_date");
        
        /*$date_diff = abs(floor((strtotime($from_date) - strtotime($to_date))/(60 * 60 * 24)));*/
        /*for($i=0; $i<=$date_diff; $i++)
        {
            $attendance_date = (date("Y-m-d", strtotime("+" . $i . " days",strtotime($from_date))));
            
            $query = "select DATE_FORMAT(da.attendance_date, '%Y-%M') month, da.attendance, da.attendance_date from `#__hr_employee_daily_attendance_items` da  where da.attendance_date='" . $attendance_date . "' and da.employee_id=" . $employee_id;
            $db->setQuery($query);
            $attendance[$attendance_date] = $db->loadObject();
        }*/
        
        $this->attendance = $attendance;
        $this->employee_id = $employee_id;
        
        $months = array();
        
        for ($i = strtotime($from_date); $i <= strtotime($to_date);)
        {                              
            $months[] = date('Y-m', $i);
            $i = strtotime('+1 months', strtotime(date('Y-m-01', $i)));
        }
        
        $this->months = $months;
        
        parent::display($tpl);
    }
}
?>