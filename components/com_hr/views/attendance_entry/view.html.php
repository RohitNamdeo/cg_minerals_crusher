<?php
jimport('joomla.application.component.view');

class HrViewAttendance_entry extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * view to add/edit attendance
        * add/edit not possible if salary has been generated for that month
        * Attendance voucher cannot be generated for today and future dates
        * attendance is generated with entry by = user id
        * regenerate option is provided in add if the synced data is incomplete
        * generated voucher is displayed
        * If attendance status calculated by code is wrong it can be corrected in edit form
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "attendance_entry"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db= JFactory::getDBO();
        
        $document = JFactory::getDocument();
        $document->setTitle("Employee Daily Attendance");
        
        $attendance_date = JRequest::getVar("attendance_date");
        $employees = array();
        $force_no_redirect = intval(JRequest::getVar("fnr"));
        $allow_attendance_entry = YES;
        $msg = "";
        
        $mode = JRequest::getVar('m');
        if($mode == 'e')
        {
            $voucher_id = intval(JRequest::getVar("voucher_id"));
            
            $query = "select count(id) from `#__hr_salary` where salary_month=" . date("m", strtotime($attendance_date)) . " and salary_year=" . date("Y", strtotime($attendance_date));
            $db->setQuery($query);
            $salary_generated = (intval($db->loadResult()) > 0 ? YES : NO);
            
            if($salary_generated || !is_admin())
            {
                echo "Attendance voucher cannot be updated for selected date. Salary already generated.";
                exit;
            }
            
            $query = "select e.employee_name, e.id employee_id, dai.id attendance_id, dai.* from `#__hr_employee_daily_attendance_items` dai inner join `#__hr_employees` e on dai.employee_id=e.id where dai.voucher_id=" . $voucher_id;
            $db->setQuery($query);
            $employees = $db->loadObjectList();
            
            $this->voucher_id = $voucher_id;
        }
        else
        {
            if($attendance_date != "")
            {
                $attendance_date = date("Y-m-d", strtotime($attendance_date));
                
                $query = "select count(id) from `#__hr_salary` where salary_month=" . date("m", strtotime($attendance_date)) . " and salary_year=" . date("Y", strtotime($attendance_date));
                $db->setQuery($query);
                $salary_generated = (intval($db->loadResult()) > 0 ? YES : NO);
                
                if($salary_generated)
                {
                    $msg = "Attendance voucher cannot be generated for selected date. Salary already generated.";
                    $allow_attendance_entry = NO;
                }
                
                if(strtotime($attendance_date) >= strtotime(date("Y-m-d")) && $allow_attendance_entry)
                {
                    $msg = "Attendance voucher cannot be generated for today and future dates.";
                    $allow_attendance_entry = NO;
                }

                if($allow_attendance_entry)
                {
                    $query = "select count(*) from `#__hr_employees` e where e.account_status=" . AC_ACTIVE . " and e.doj<='" . $attendance_date . "'";
                    $db->setQuery($query);
                    $employee_count = intval($db->loadResult());
                    
                    if($employee_count > 0)
                    {
                        $query = "select count(*) from `#__hr_employee_daily_attendance` where attendance_date='" . $attendance_date . "'";
                        $db->setQuery($query);
                        $count = intval($db->loadResult());
                        
                        if($count == 0)
                        {
                            header("Location:index.php?option=com_hr&task=generate_attendance&attendance_date=" . $attendance_date);
                        }
                        else
                        {
                            if($force_no_redirect == 0)
                            {
                                $msg = "Attendance for date '" . date("d-m-Y", strtotime($attendance_date)) . "' has been generated.";
                            }
                            else
                            {
                                $query = "select e.employee_name, e.id employee_id from `#__hr_employees` e where e.account_status=" . AC_ACTIVE . " and e.doj<='" . $attendance_date . "' order by e.employee_name";
                                $db->setQuery($query);
                                $employees = $db->loadObjectList();
                                
                                foreach($employees as $key=>$employee)
                                {
                                    $query = "select dai.id attendance_id, dai.* from `#__hr_employee_daily_attendance_items` dai where dai.employee_id=" . intval($employee->employee_id) . " and dai.attendance_date='" . $attendance_date . "'";
                                    $db->setQuery($query);
                                    $attendance = $db->loadObject();
                                    
                                    if(count($attendance) > 0)
                                    {   
                                        $employees[$key]->attendance_id = $attendance->attendance_id;
                                        $employees[$key]->in_date = $attendance->in_date;
                                        $employees[$key]->in_time = $attendance->in_time;
                                        $employees[$key]->break1_out_date = $attendance->break1_out_date;
                                        $employees[$key]->break1_out_time = $attendance->break1_out_time;
                                        $employees[$key]->break1_in_date = $attendance->break1_in_date;
                                        $employees[$key]->break1_in_time = $attendance->break1_in_time;
                                        $employees[$key]->break2_out_date = $attendance->break2_out_date;
                                        $employees[$key]->break2_out_time = $attendance->break2_out_time;
                                        $employees[$key]->break2_in_date = $attendance->break2_in_date;
                                        $employees[$key]->break2_in_time = $attendance->break2_in_time;
                                        $employees[$key]->break3_out_date = $attendance->break3_out_date;
                                        $employees[$key]->break3_out_time = $attendance->break3_out_time;
                                        $employees[$key]->break3_in_date = $attendance->break3_in_date;
                                        $employees[$key]->break3_in_time = $attendance->break3_in_time;
                                        $employees[$key]->out_date = $attendance->out_date;
                                        $employees[$key]->out_time = $attendance->out_time;
                                        $employees[$key]->attendance = $attendance->attendance;
                                        $employees[$key]->remarks = $attendance->remarks;
                                    }
                                    else
                                    {
                                        $employees[$key]->attendance_id = 0;
                                        $employees[$key]->in_date = "0000-00-00";
                                        $employees[$key]->in_time = "00:00:00";
                                        $employees[$key]->break1_out_date = "0000-00-00";
                                        $employees[$key]->break1_out_time = "00:00:00";
                                        $employees[$key]->break1_in_date = "0000-00-00";
                                        $employees[$key]->break1_in_time = "00:00:00";
                                        $employees[$key]->break2_out_date = "0000-00-00";
                                        $employees[$key]->break2_out_time = "00:00:00";
                                        $employees[$key]->break2_in_date = "0000-00-00";
                                        $employees[$key]->break2_in_time = "00:00:00";
                                        $employees[$key]->break3_out_date = "0000-00-00";
                                        $employees[$key]->break3_out_time = "00:00:00";
                                        $employees[$key]->break3_in_date = "0000-00-00";
                                        $employees[$key]->break3_in_time = "00:00:00";
                                        $employees[$key]->out_date = "0000-00-00";
                                        $employees[$key]->out_time = "00:00:00";
                                        $employees[$key]->attendance = 0;
                                        $employees[$key]->remarks = "";
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        $msg = "Employees does not exists.";
                    }
                }
            }
        }
        
        $this->attendance_date = $attendance_date;
        $this->employees = $employees;
        $this->msg = $msg;
        
        if($mode == 'e')
        {
            parent::display("edit");
        }
        else
        {
            parent::display($tpl);
        }
    }
}
?>