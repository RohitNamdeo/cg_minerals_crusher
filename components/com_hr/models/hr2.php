<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class HrModelHr2 extends JModelLegacy
{
    function generate_attendance_log()
    {
        // It syncs the machine data in s/w if employee with mentioned machine nos exists
        $db = JFactory::getDbo();
        
        $machine_no = JRequest::getVar("machine_no");
        $machine_enrollment_no = intval(JRequest::getVar("machine_enrollment_no"));
        $attendance_date = date("Y-m-d H:i:s", strtotime(JRequest::getVar('attendance_date')));
        
        $query = "select id from `#__hr_employees` where machine_no='" . $machine_no . "' and machine_enrollment_no=" . $machine_enrollment_no;
        $db->setQuery($query);
        $employee_id = intval($db->loadResult());
        
        $query = "select count(id) from `#__hr_employee_attendance_log` where employee_id=" . $employee_id . " and attendance_date='"  . $attendance_date . "'";
        $db->setQuery($query);
        $count = intval($db->loadResult());
        
        if($count == 0)
        {
            $query = "insert into `#__hr_employee_attendance_log` (`employee_id`, `attendance_date`) values (" . $employee_id . ", '" . $attendance_date . "')";
            $db->setQuery($query);
            $db->query();
            
            echo "true";
        }
        else
        {
            echo "false";
        }
    }
    
    function generate_attendance()
    {
        /*
        * All the punched data of all employees are generated as a voucher on selected date
        * Regeneration deletes the previous entries and regenarates the voucher
        * log count for any employee can be even or odd
        * If odd then 
        * count = 1 -> 1st entry is considered as in
        * count = 3 -> 1st entry is considered as in, 2nd as break1 out, 3rd is out if > 5 evening else it is break1 in
        * If even then 
        * count = 2 -> 1st entry is considered as in, last entry is considered as out
        * count = 4 -> 1st entry is considered as in, last entry is considered as out, rest are break1 out-in
        * only 1 break is allowed in code
        * 
        * If log count = 0 -> attendance is absent
        * if out time is there then if it is >= 5 evening then full day present else half day present
        * else break1 in time is checked
        * if break1 in time is >= 5 evening then full day present else half day present
        */
        
        $db = JFactory::getDbo();
        
        $attendance_date = date("Y-m-d", strtotime(JRequest::getVar("attendance_date")));
        $regenerate = intval(JRequest::getVar("regenerate"));
        
        if($regenerate == YES)
        {
            $query = "delete from `#__hr_employee_daily_attendance` where attendance_date='" . $attendance_date . "'";
            $db->setQuery($query);
            $db->query();
            
            $query = "delete from `#__hr_employee_daily_attendance_items` where attendance_date='" . $attendance_date . "'";
            $db->setQuery($query);
            $db->query();
        }
        
        $entry_count = 0;
        $query = "select id from `#__hr_employee_daily_attendance` where attendance_date='" . $attendance_date . "'";
        $db->setQuery($query);
        $voucher_id = intval($db->loadResult());
        
        if($voucher_id == 0)
        {
            $query = "insert into `#__hr_employee_daily_attendance` (`attendance_date`, `entry_by`) values ('" . $attendance_date . "'," . intval(JFactory::getUser()->id) . ")";
            $db->setQuery($query);
            $db->query();
            
            $voucher_id = intval($db->insertid());
        }
        
        $condition = "(DATE_FORMAT(al.attendance_date, '%Y-%m-%d') = '" . date('Y-m-d', strtotime($attendance_date)) . "')";
        
        $query = "select id from `#__hr_employees` e where e.account_status=" . AC_ACTIVE . " and e.doj<='" . $attendance_date . "'";
        $db->setQuery($query);
        $employee_ids = $db->loadObjectList();  
        
        if(count($employee_ids) > 0)
        {
            foreach($employee_ids as $key=>$employee)
            {
                $entry_count++;
                
                $query = "select id from `#__hr_employee_daily_attendance_items` where employee_id=" . intval($employee->id) . " and attendance_date='" . $attendance_date . "'";
                $db->setQuery($query);
                $attendance_id = intval($db->loadResult());
                
                $query = "select * from `#__hr_employee_attendance_log` al where al.employee_id=" . intval($employee->id) . " and " . $condition . " order by al.id ASC";
                $db->setQuery($query);
                $attendance_log = $db->loadObjectList();
                
                $attendance_status = 0;
                $in_date = $in_time = $out_date = $out_time = "";
                
                $break1_out_date = $break1_out_time = $break1_in_date = $break1_in_time = "";
                //$break2_out_date = $break2_out_time = $break2_in_date = $break2_in_time = "";
                //$break3_out_date = $break3_out_time = $break3_in_date = $break3_in_time = "";
                
                $log_count = count($attendance_log); 
                $last_index = $log_count - 1; 
                
                if(($log_count%2)) // odd entries
                {
                    if(isset($attendance_log[0]))
                    {
                        $in_date = date("Y-m-d",strtotime($attendance_log[0]->attendance_date));
                        $in_time = date("H:i:s",strtotime($attendance_log[0]->attendance_date));
                        unset($attendance_log[0]);
                    }
                    
                    $remaining_log_count = count($attendance_log);
                    for($i=0;$i<=$remaining_log_count;$i++)
                    {
                        switch($i)
                        {
                            case 1:
                                $break1_out_date = date("Y-m-d",strtotime($attendance_log[$i]->attendance_date));
                                $break1_out_time = date("H:i:s",strtotime($attendance_log[$i]->attendance_date));
                                break;
                            case 2:    
                                if(date("H",strtotime($attendance_log[$i]->attendance_date)) >= 17)
                                {
                                    $out_date = date("Y-m-d",strtotime($attendance_log[$i]->attendance_date));
                                    $out_time = date("H:i:s",strtotime($attendance_log[$i]->attendance_date));
                                }
                                else
                                {
                                    $break1_in_date = date("Y-m-d",strtotime($attendance_log[$i]->attendance_date));
                                    $break1_in_time = date("H:i:s",strtotime($attendance_log[$i]->attendance_date));
                                }
                                break;
                        }
                    }
                }
                else // even entries
                {
                    if(isset($attendance_log[0]))
                    {
                        $in_date = date("Y-m-d",strtotime($attendance_log[0]->attendance_date));
                        $in_time = date("H:i:s",strtotime($attendance_log[0]->attendance_date));
                        unset($attendance_log[0]);
                    }
                    if(isset($attendance_log[$last_index]))
                    {
                        $out_date = date("Y-m-d",strtotime($attendance_log[$last_index]->attendance_date));
                        $out_time = date("H:i:s",strtotime($attendance_log[$last_index]->attendance_date));
                        unset($attendance_log[$last_index]);
                    }
                    
                    $remaining_log_count = count($attendance_log);
                    for($i=0;$i<=$remaining_log_count;$i++)
                    {
                        switch($i)
                        {
                            case 1:
                                $break1_out_date = date("Y-m-d",strtotime($attendance_log[$i]->attendance_date));
                                $break1_out_time = date("H:i:s",strtotime($attendance_log[$i]->attendance_date));
                                break;
                            case 2:    
                                $break1_in_date = date("Y-m-d",strtotime($attendance_log[$i]->attendance_date));
                                $break1_in_time = date("H:i:s",strtotime($attendance_log[$i]->attendance_date));
                                break;
                        }
                    }
                }
                
                if($log_count)
                {
                    if($out_time != "")
                    {
                        if(date("H",strtotime($out_time)) >= 17)
                        {
                            $attendance_status = 1;
                        }
                        else
                        {
                            $attendance_status = 0.5;
                        }
                    }
                    else
                    {
                        if($break1_in_time != "")
                        {
                            if(date("H",strtotime($break1_in_time)) >= 17)
                            {
                                $attendance_status = 1;
                            }
                            else
                            {
                                $attendance_status = 0.5;
                            }
                        }
                        else
                        {
                            $attendance_status = 0.5;
                        }
                    }
                }
                
                $attendance = new stdClass();
                
                if($attendance_id > 0) 
                {
                    $attendance->id = $attendance_id;
                }
                $attendance->employee_id = intval($employee->id);
                $attendance->attendance_date = $attendance_date;
                $attendance->voucher_id = $voucher_id;
                
                $attendance->in_date = $in_date;
                $attendance->in_time = $in_time;
                
                $attendance->break1_out_date = $break1_out_date;
                $attendance->break1_out_time = $break1_out_time;
                $attendance->break1_in_date = $break1_in_date;
                $attendance->break1_in_time = $break1_in_time;
                
                /*$attendance->break2_out_date = $break2_out_date;
                $attendance->break2_out_time = $break2_out_time;
                $attendance->break2_in_date = $break2_in_date;
                $attendance->break2_in_time = $break2_in_time;
                
                $attendance->break3_out_date = $break3_out_date;
                $attendance->break3_out_time = $break3_out_time;
                $attendance->break3_in_date = $break3_in_date;
                $attendance->break3_in_time = $break3_in_time;*/
                
                $attendance->out_date = $out_date;
                $attendance->out_time = $out_time;
                
                $attendance->attendance = $attendance_status;
                
                if($attendance_id > 0) 
                {
                    $db->updateObject("#__hr_employee_daily_attendance_items", $attendance, 'id');
                }
                else
                {
                    $db->insertObject("#__hr_employee_daily_attendance_items", $attendance, '');
                }
            } 
        }
        
        if($entry_count == 0)
        {
            $query = "delete from `#__hr_employee_daily_attendance` where id=" . $voucher_id;
            $db->setQuery($query);
            $db->query();
            
            return "";
        }
        else
        {
            Functions::log_activity("Attendance " . ($regenerate ? "regenerated" : "generated") . " for " . date("d-M-Y", strtotime($attendance_date)) .".", "DA", $voucher_id);
            return "ok";
        } 
    }
    
    function update_attendance()
    {
        // called in edit attendance form
        $db = JFactory::getDbo();
        
        $attendance_date = date("Y-m-d", strtotime(JRequest::getVar("attendance_date")));
        $attendance_ids = JRequest::getVar("attendance_ids");
        $remarks = JRequest::getVar("remarks");
        $attendance = JRequest::getVar("attendance");
        $voucher_id = intval(JRequest::getVar("voucher_id"));

        for($i=0;$i<count($attendance_ids);$i++)
        {
           $query = "update `#__hr_employee_daily_attendance_items` set attendance=" . floatval($attendance[$i]) . ", remarks='" . addslashes($remarks[$i]) . "' where id=" . $attendance_ids[$i];
            $db->setQuery($query);
            $db->query();
        }
        
        Functions::log_activity("Attendance has been updated for " . $attendance_date . ".", "DA", $voucher_id);
        return "Attendance updated successfully.";
    }
    
    function delete_daily_attendance()
    {
        $db = JFactory::getDbo();
        
        $attendance_date = date("Y-m-d", strtotime(JRequest::getVar("attendance_date")));
        $voucher_id = intval(JRequest::getVar("voucher_id"));
        
        $query = "delete from `#__hr_employee_daily_attendance` where id=" . $voucher_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__hr_employee_daily_attendance_items` where voucher_id=" . $voucher_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Attendance has been deleted for " . date("d-M-Y", strtotime($attendance_date)) .".", "DA", $voucher_id);
        return "Attendance deleted successfully.";
    }
}
?>