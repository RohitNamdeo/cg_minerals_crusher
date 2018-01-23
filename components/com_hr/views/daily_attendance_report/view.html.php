<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class HrViewDaily_attendance_report extends JViewLegacy
{
    function display($tpl = null)
    {
        // view to display attendance for all the employees on a particular date, edit/delete is there
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "daily_attendance_report"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDBO();
        $document = JFactory::getDocument();
        $document->setTitle( "Daily Attendance Report" );
        
        $location_id = intval(JRequest::getVar("location_id"));
        $attendance_date = JRequest::getVar("attendance_date");
        $this->location_id = $location_id;
        
        $query = "select id, location_name from `#__inventory_locations` order by `location_name`";
        $db->setQuery($query);
        $locations = $db->loadObjectlist();
        $this->locations = $locations;
        
        if($attendance_date == "") { $attendance_date = date("Y-m-d", strtotime("-1 day")); }
        else { $attendance_date = date("Y-m-d", strtotime($attendance_date)); }
        
        $query = "select dai.*, e.employee_name, e.id employee_id, l.location_name from `#__hr_employee_daily_attendance_items` dai inner join `#__hr_employees` e on dai.employee_id=e.id inner join `#__inventory_locations` l on e.location_id=l.id where dai.attendance_date='" . $attendance_date . "' " . ($location_id > 0 ? " and e.location_id=" . $location_id : "") . " order by e.employee_name";
        $db->setQuery($query);
        $daily_attendance = $db->loadObjectList();
        
        $query = "select count(id) from `#__hr_salary` where salary_month=" . date("m", strtotime($attendance_date)) . " and salary_year=" . date("Y", strtotime($attendance_date));
        $db->setQuery($query);
        $salary_generated = (intval($db->loadResult()) > 0 ? YES : NO);
        
        $this->daily_attendance = $daily_attendance;
        $this->attendance_date = $attendance_date;
        $this->salary_generated = $salary_generated;
        
        parent::display($tpl);
    }
}
?>