<?php
jimport( 'joomla.application.component.view');

class HrViewMonthly_attendance_report extends JViewLegacy
{
    function display($tpl = null)
    {
        // view to show day-wise attendance for all employees for whole month
        
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "monthly_attendance_report"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $document = JFactory::getDocument();
        $document->setTitle("Monthly Attendance Report");
        
        $location_id = intval(JRequest::getVar("location_id"));
        $month = intval(JRequest::getVar("month"));
        $year = intval(JRequest::getVar("year"));
        
        $this->location_id = $location_id;
        
        if($month == 0 || $year == 0)
        {
            $previous_month = date("Y-m", strtotime("-1 month", strtotime(date("Y-m-d"))));
            $month = date("m", strtotime($previous_month));
            $year = date("Y", strtotime($previous_month));
        }
        $this->month = $month;
        $this->year = $year;
        
        $employees = array();
        $total_daywise_attendance = array();
        $grand_total = 0;
        $condition = "";   
        
        if($location_id != 0)
        { $condition .= ($condition != "" ? " and " : "" ) . "(e.location_id=" . $location_id . ")"; }
        
        $from_date = date("Y-m-01", strtotime($year . '-' . $month . '-01'));
        $to_date = date("Y-m-t", strtotime($year . '-' . $month . '-01'));
        
        $date_diff = abs(floor((strtotime($from_date) - strtotime($to_date))/(60 * 60 * 24)));
        
        $query = "select e.employee_name, e.id employee_id, l.location_name from `#__hr_employees` e inner join `#__inventory_locations` l on e.location_id=l.id where e.account_status=" . AC_ACTIVE . ($condition != "" ? " and " . $condition : "") . " order by e.employee_name";
        $db->setQuery($query);
        $employees = $db->loadObjectList();
        
        foreach($employees as $key=>$employee)
        {
            $attendance = array();
            $total_attendance = 0;
            
            $query = "select attendance_date, attendance from `#__hr_employee_daily_attendance_items` where (attendance_date between '" . $from_date . "' and '" . $to_date . "') and (employee_id=" . intval($employee->employee_id) . ")";
            $db->setQuery($query);
            $data = $db->loadObjectlist('attendance_date'); 
            
            for($i=0; $i<=$date_diff; $i++)
            {
                $attendance_date = date("Y-m-d", strtotime("+" . $i . " days",strtotime($from_date)));
                $index = $i + 1;
                
                if(!isset($total_daywise_attendance[$index]))
                {
                    $total_daywise_attendance[$index] = 0;
                }
                
                if(isset($data[$attendance_date]))
                {
                    $total_attendance += $data[$attendance_date]->attendance;
                    $attendance[$index] = $data[$attendance_date];
                    
                    $total_daywise_attendance[$index] += $data[$attendance_date]->attendance;
                }
                else
                {
                    $attendance[$index] = null;
                }
            }
            $employees[$key]->attendance = $attendance;
            $employees[$key]->total_attendance = $total_attendance;
            
            $grand_total += $total_attendance;
        }
        
        $query = "select id, location_name from `#__inventory_locations` order by `location_name`";
        $db->setQuery($query);
        $locations = $db->loadObjectlist();
        $this->locations = $locations;
        
        $this->employees = $employees;
        $this->total_daywise_attendance = $total_daywise_attendance;
        $this->grand_total = $grand_total;
        
        parent::display($tpl);
    }
}
?>