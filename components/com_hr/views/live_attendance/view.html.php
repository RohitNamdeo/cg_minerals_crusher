<?php
jimport( 'joomla.application.component.view');

class HrViewLive_attendance extends JViewLegacy
{
    function display($tpl = null)
    {
        // If someone has punched then he is considered as present else absent
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        if (!Functions::has_permissions("hr", "live_attendance"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $document = JFactory::getDocument();
        $document->setTitle("Live Attendance");
        
        $location_id = intval(JRequest::getVar("location_id"));
        $attendance_date = date("Y-m-d");
        $condition = "";
        
        $employees = array();
        
        $query = "select e.id, e.employee_name, l.location_name from `#__hr_employees` e inner join `#__inventory_locations` l on e.location_id=l.id where e.account_status=" . AC_ACTIVE . ($location_id > 0 ? " and e.location_id=" . $location_id : "");
        $db->setQuery($query);
        $employees = $db->loadObjectList();
        
        $condition = "(DATE_FORMAT(attendance_date, '%Y-%m-%d') = '" . date('Y-m-d', strtotime($attendance_date)) . "')";

        foreach($employees as $key=>$employee)
        {
            $query = "select DATE_FORMAT(attendance_date, '%d-%b-%Y %H:%i:%s') from `#__hr_employee_attendance_log` where employee_id=" . intval($employee->id) . " and " . $condition . " order by `attendance_date` ASC";
            $db->setQuery($query);
            $attendance_log = $db->loadColumn();
            
            if(count($attendance_log) > 0)
            {
                $employees[$key]->present = "Yes";
                foreach($attendance_log as $log)
                {
                    $employees[$key]->in_out_entry = implode("<br />", $attendance_log);
                }    
            }
            else
            {
                $employees[$key]->present = "No";
                $employees[$key]->in_out_entry = "";
            }
        }
        
        $query = "select * from `#__inventory_locations` order by `location_name`";
        $db->setQuery($query);
        $locations = $db->loadObjectList();
        $this->locations = $locations;
        
        $this->employees = $employees;
        $this->location_id = $location_id;
        
        parent::display($tpl);
    }
}
?>