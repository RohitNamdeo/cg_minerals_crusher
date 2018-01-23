<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class HrModelHr3 extends JModelLegacy
{
    function generate_salary()
    {
        /*
        * function to generate salary of all employees at a time for a month
        * salary is not generated if attendance is not found
        * salary is generated from his gross salary as per his attendance
        * multiple advances can be deducted from one salary if the employee is on multiple advances
        * With each advance, amount claered is maintained
        */
        
        $db = JFactory::getDbo();
        
        $month = intval(JRequest::getVar("month"));
        $year = intval(JRequest::getVar("year"));
        
        $query = "select count(id) from `#__hr_salary` where salary_month=" . $month . " and salary_year=" . $year;
        $db->setQuery($query);
        $salary_generated = (intval($db->loadResult()) > 0 ? YES : NO);
        
        if($salary_generated)
        {
            return "Salary for selected month, year already generated.";
        }
        else
        {
            $query = "select count(id) from `#__hr_employee_daily_attendance` where DATE_FORMAT(attendance_date, '%Y-%m') = '" . date("Y-m", strtotime($year . '-' .$month . '-01')) . "'";
            $db->setQuery($query);
            $count = intval($db->loadResult());
            
            if($count == 0)
            {
                return "Unable to generate salary. Attendance not found.";
            }
            else
            {
                $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                
                $entry_by = intval(JFactory::getUser()->id);
                $entry_date = date("Y-m-d");
                
                $fy = "";
                $fy_year = date("y", strtotime($year .'-' . $month . '-01'));
                if($month < 4)
                {
                    $fy = ($fy_year - 1) . '-' . $fy_year;
                }
                else
                {
                    $fy = $fy_year . '-' . ($fy_year + 1);
                }
                
                $query = "select e.id employee_id, e.gross_salary from `#__hr_employees` e where e.account_status=" . AC_ACTIVE . " and e.doj<='" . date("Y-m-d", strtotime($year .'-' . $month . '-01')) . "' order by e.employee_name";
                $db->setQuery($query);
                $employees = $db->loadObjectList();
                
                foreach($employees as $employee)
                {
                    $salary_items = array();
                    $query = "select sum(attendance) from `#__hr_employee_daily_attendance_items` where employee_id=" . intval($employee->employee_id) . " and attendance_date between '" . date("Y-m-01", strtotime($year . '-' .$month . '-01')) . "' and '" . date("Y-m-t", strtotime($year . '-' .$month . '-01')) . "'";
                    $db->setQuery($query);
                    $attendance = floatval($db->loadResult());
                    
                    $gross_salary = round($attendance * floatval($employee->gross_salary) / $days_in_month);
                    
                    $query = "select id, amount, amount_cleared from `#__hr_advance_salary_payments` where employee_id=" . intval($employee->employee_id) . " and cleared=" . NO . " and payment_date<'" . date("Y-m-d", strtotime("+1 month", strtotime($year . '-' . $month . '-01'))) ."' order by payment_date";
                    $db->setQuery($query);
                    $advances = $db->loadObjectList();
                    
                    $advance_deduction = 0;
                    $total_salary = $gross_salary;
                    if(count($advances) > 0)
                    {
                        foreach($advances as $advance)
                        {
                            if($total_salary == 0) { break; }
                            $amount = 0;
                            if($total_salary >= floatval($advance->amount - $advance->amount_cleared))
                            {
                                $amount = floatval($advance->amount - $advance->amount_cleared);
                                $advance_deduction += $amount;
                                $total_salary -= $amount;
                                
                                $query = "update `#__hr_advance_salary_payments` set amount_cleared = amount_cleared + " . $amount . ", cleared=" . YES . " where id=" . intval($advance->id);
                                $db->setQuery($query);
                                $db->query();
                                
                                $salary_items[] = array("advance_id"=>intval($advance->id), "amount"=>$amount);
                            }
                            else if($total_salary < floatval($advance->amount - $advance->amount_cleared))
                            {
                                $amount = $total_salary;
                                $advance_deduction += $amount;
                                $total_salary -= $amount;
                                
                                $query = "update `#__hr_advance_salary_payments` set amount_cleared = amount_cleared + " . $amount . " where id=" . intval($advance->id);
                                $db->setQuery($query);
                                $db->query();
                                
                                $salary_items[] = array("advance_id"=>intval($advance->id), "amount"=>$amount);
                            }
                        }
                    }
                    
                    $salary = new stdClass();
                    
                    $salary->employee_id = intval($employee->employee_id);
                    $salary->actual_gross_salary = floatval($employee->gross_salary);
                    $salary->gross_salary = $gross_salary;
                    $salary->advance_deduction = $advance_deduction;
                    $salary->total_salary = $total_salary;
                    $salary->salary_month = $month;
                    $salary->salary_year = $year;
                    $salary->attendance = $attendance;
                    $salary->working_days = $days_in_month;
                    $salary->entry_by = $entry_by;
                    $salary->entry_date = $entry_date;
                    $salary->fy = $fy;
                    
                    $db->insertObject("#__hr_salary", $salary, "");
                    $salary_id = intval($db->insertid());
                    
                    if(count($salary_items) > 0)
                    {
                        $data = "";
                        foreach($salary_items as $item)
                        {
                            $data .= ($data != "" ? "," : "") . "(" . $salary_id . "," . $item["advance_id"] . "," . $item["amount"] . ")";
                        }
                        
                        $query = "insert into `#__hr_salary_items` (`salary_id`,`advance_id`,`amount`) values " . $data;
                        $db->setQuery($query);
                        $db->query();
                    }
                }
                
                Functions::log_activity("Salary for " . date("M-Y", strtotime($year .'-' . $month . '-01')) ." has been generated.", "Salary");
                return "Salary generated successfully.";
            }
        }
    }
    
    function delete_salary()
    {
        /*
        * only last month salary can be deleted
        * all the advances cleared in this salary are reverted
        */
        
        $db = JFactory::getDbo();
        
        $month = intval(JRequest::getVar('month'));
        $year = intval(JRequest::getVar('year'));
        
        $query = "select (s.salary_year * 100 + s.salary_month) from `#__hr_salary` s order by s.salary_year, s.salary_month desc limit 1";
        $db->setQuery($query);
        $last_salary_month = $db->loadResult();
        
        $is_last_month = 0;
        if($last_salary_month == ($year * 100 + $month))
        {
            $is_last_month = YES;
        }
        else
        {
            $is_last_month = NO;
        }
        
        if($is_last_month)
        {
            $query = "select count(id) from `#__hr_salary` where salary_month=" . $month . " and salary_year=" . $year . " and paid_salary>0";
            $db->setQuery($query);
            $salary_payment_count = intval($db->loadResult());
            
            if($salary_payment_count > 0)
            {
                return "Salary cannot be deleted. Salary has been paid for the month.";
            }
            else
            {   
                $query = "update `#__hr_advance_salary_payments` a inner join `#__hr_salary_items` si on a.id=si.advance_id inner join `#__hr_salary` s on si.salary_id=s.id set a.amount_cleared = a.amount_cleared - si.amount, a.cleared=" . NO . " where s.salary_month=" . $month . " and s.salary_year=" . $year;
                $db->setQuery($query);
                $db->query();
                
                $query = "delete si.* from `#__hr_salary_items` si inner join `#__hr_salary` s on si.salary_id=s.id where s.salary_month=" . $month . " and s.salary_year=" . $year;
                $db->setQuery($query);
                $db->query();
                
                $query = "delete from `#__hr_salary` where salary_month=" . $month . " and salary_year=" . $year;
                $db->setQuery($query);
                $db->query();
                
                Functions::log_activity("Salary for month " . date("F'Y", strtotime($year . '-' . $month . '-01')) . " has been deleted.");
                return "Salary deleted successfully.";
            }
        }
        else
        {
            return "Salary cannot be deleted. Selected month is not the last month.";
        }
    }
    
    function save_salary_voucher()
    {
        // function to save salary payments -> salary can be paid in parts
        // cash_in_hand setting changes if the instrument is cash
        
        $db = JFactory::getDbo();
        
        $salary_month = intval(JRequest::getVar("salary_month"));
        $salary_year = intval(JRequest::getVar("salary_year"));
        $payment_date = date("Y-m-d", strtotime(JRequest::getVar("payment_date")));
        $salary_ids = JRequest::getVar("salary_ids");
        $employee_ids = JRequest::getVar("employee_ids");
        $instrument = JRequest::getVar("instrument");
        $instrument_nos = JRequest::getVar("instrument_no");
        $instrument_banks = JRequest::getVar("instrument_bank");
        $payment_amount = JRequest::getVar("payment_amount");
        $remarks = JRequest::getVar("remarks");
        
        $entry_by = intval(JFactory::getUser()->id);
        $entry_date = date("Y-m-d");
        
        $query = "insert into `#__hr_salary_payments` (`salary_month`, `salary_year`, `payment_date`, `entry_by`, `entry_date`) values (" . $salary_month . ", " . $salary_year . ", '" . $payment_date . "', " . $entry_by . ", '" . $entry_date . "')";  
        $db->setQuery($query);
        $db->query();
        $payment_id = intval($db->insertid());
        
        for($i=0;$i<count($employee_ids);$i++)
        {
            if(floatval($payment_amount[$i]) != 0)
            {
                $payment = new stdClass();

                $payment->employee_id = intval($employee_ids[$i]);
                $payment->payment_id = $payment_id;
                $payment->item_id = intval($salary_ids[$i]);
                $payment->amount = floatval($payment_amount[$i]);
                $payment->instrument = intval($instrument[$i]);
                $payment->instrument_no = $instrument_nos[$i];
                $payment->instrument_bank = intval($instrument_banks[$i]);
                $payment->remarks = $remarks[$i];
                
                $db->insertObject( "#__hr_salary_payment_items", $payment, "" );
                
                $query = "update `#__hr_salary` set paid_salary = paid_salary + " . floatval($payment_amount[$i]) . " where id=" . intval($salary_ids[$i]);
                $db->setQuery($query);
                $db->query();
                
                if(intval($instrument[$i]) == CASH)
                {
                    $query = "update `#__settings` set `value_numeric`=value_numeric-" . floatval($payment_amount[$i]) . " where `key`='cash_in_hand'";
                    $db->setQuery($query);
                    $db->query();
                }
            }
        }
        
        Functions::log_activity("Salary payment voucher(" . $payment_id . ") for the month " . date("M-Y", strtotime($salary_year . '-' . $salary_month . '-01')) . " has been saved.");
        return "Salary payment voucher saved successfully.<a href='index.php?option=com_hr&view=salary_payment_print&tmpl=print&payment_id=" . $payment_id . "' target='_blank'>Click here to print.</a>";
    }
    
    function update_salary_voucher()
    {
        // cash_in_hand setting changes if the instrument was/is cash
        $db = JFactory::getDbo();
        
        $payment_id = intval(JRequest::getVar("payment_id"));
        $payment_date = date("Y-m-d", strtotime(JRequest::getVar("payment_date")));
        $salary_ids = JRequest::getVar("salary_ids");
        $employee_ids = JRequest::getVar("employee_ids");
        $instrument = JRequest::getVar("instrument");
        $instrument_nos = JRequest::getVar("instrument_no");
        $instrument_banks = JRequest::getVar("instrument_bank");
        $payment_amount = JRequest::getVar("payment_amount");
        $remarks = JRequest::getVar("remarks");
        
        $salary_month = intval(JRequest::getVar("salary_month"));
        $salary_year = intval(JRequest::getVar("salary_year"));
        
        $query = "select employee_id, amount, instrument from `#__hr_salary_payment_items` where payment_id=" . $payment_id;
        $db->setQuery($query);
        $payments = $db->loadObjectList();
        
        foreach($payments as $payment)
        {
            $query = "update `#__hr_salary` set `paid_salary` = paid_salary - " . floatval($payment->amount) . " where employee_id=" . intval($payment->employee_id) . " and salary_month=" . $salary_month . " and salary_year=" . $salary_year;
            $db->setQuery($query);
            $db->query();
            
            if($payment->instrument == CASH)
            {
                $query = "update `#__settings` set `value_numeric`=value_numeric+" . floatval($payment->amount) . " where `key`='cash_in_hand'";
                $db->setQuery($query);
                $db->query();
            }
        }
        
        $query = "delete from `#__hr_salary_payment_items` where payment_id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "update `#__hr_salary_payments` set payment_date='" . $payment_date . "' where id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
        
        for($i=0;$i<count($employee_ids);$i++)
        {
            if(floatval($payment_amount[$i]) != 0)
            {
                $payment = new stdClass();

                $payment->employee_id = intval($employee_ids[$i]);
                $payment->payment_id = $payment_id;
                $payment->item_id = intval($salary_ids[$i]);
                $payment->amount = floatval($payment_amount[$i]);
                $payment->instrument = intval($instrument[$i]);
                $payment->instrument_no = $instrument_nos[$i];
                $payment->instrument_bank = intval($instrument_banks[$i]);
                $payment->remarks = $remarks[$i];
                
                $db->insertObject( "#__hr_salary_payment_items", $payment, "");
                
                $query = "update `#__hr_salary` set paid_salary = paid_salary + " . floatval($payment_amount[$i]) . " where id=" . intval($salary_ids[$i]);
                $db->setQuery($query);
                $db->query();
                
                if(intval($instrument[$i]) == CASH)
                {
                    $query = "update `#__settings` set `value_numeric`=value_numeric-" . floatval($payment_amount[$i]) . " where `key`='cash_in_hand'";
                    $db->setQuery($query);
                    $db->query();
                }
            }
        }
        
        Functions::log_activity("Salary payment voucher(" . $payment_id . ") for the month " . date("M-Y", strtotime($salary_year . '-' . $salary_month . '-01')) . " has been updated.");
        return "Salary payment voucher has been updated successfully.<a href='index.php?option=com_hr&view=salary_payment_print&tmpl=print&payment_id=" . $payment_id . "' target='_blank'>Click here to print.</a>";
    }
    
    function delete_salary_voucher()
    {
        // cash_in_hand setting changes if the instrument was cash before salary payment delete
        $db = JFactory::getDbo();
        
        $payment_id = intval(JRequest::getVar("payment_id"));
        $salary_month = intval(JRequest::getVar("salary_month"));
        $salary_year = intval(JRequest::getVar("salary_year"));
        
        $query = "select employee_id, amount, instrument from `#__hr_salary_payment_items` where payment_id=" . $payment_id;
        $db->setQuery($query);
        $payments = $db->loadObjectList();
        
        foreach($payments as $payment)
        {
            $query = "update `#__hr_salary` set `paid_salary` = paid_salary - " . floatval($payment->amount) . " where employee_id=" . intval($payment->employee_id) . " and salary_month=" . $salary_month . " and salary_year=" . $salary_year;
            $db->setQuery($query);
            $db->query();
            
            if($payment->instrument == CASH)
            {
                $query = "update `#__settings` set `value_numeric`=value_numeric+" . floatval($payment->amount) . " where `key`='cash_in_hand'";
                $db->setQuery($query);
                $db->query();
            }
        }
        
        $query = "delete from `#__hr_salary_payments` where id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__hr_salary_payment_items` where payment_id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Salary payment voucher(" . $payment_id . ") has been deleted.");
        return "Salary Payment Voucher deleted successfully.";
    }
    
    function save_advance_salary_voucher()
    {
        // cash_in_hand setting changes if the instrument is cash
        $db = JFactory::getDbo();
        
        $payment_date = date("Y-m-d", strtotime(JRequest::getVar("payment_date")));
        $employee_id = intval(JRequest::getVar("employee_id"));
        $instrument = intval(JRequest::getVar("instrument"));
        $instrument_no = JRequest::getVar("instrument_no");
        $instrument_bank = intval(JRequest::getVar("instrument_bank"));
        $amount = floatval(JRequest::getVar("amount"));
        $remarks = JRequest::getVar("remarks");
        
        $advance = new stdClass();

        $advance->employee_id = $employee_id;
        $advance->payment_date = $payment_date;
        $advance->amount = $amount;
        $advance->instrument = $instrument;
        $advance->instrument_no = $instrument_no;
        $advance->instrument_bank = $instrument_bank;
        $advance->remarks = $remarks;
        $advance->entry_by = intval(JFactory::getUser()->id);
        $advance->entry_date = date("Y-m-d");;
        
        $db->insertObject( "#__hr_advance_salary_payments", $advance, "" );
        $advance_id = intval($db->insertid());
        
        if($instrument == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric-" . floatval($amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "select employee_name from `#__hr_employees` where id=" . $employee_id;
        $db->setQuery($query);
        $employee_name = $db->loadResult();
        
        Functions::log_activity("Advance payment voucher of " . $amount . "/- for " . $employee_name . "(" . $employee_id . ") has been saved.", "", $advance_id);
        return "Advance salary payment voucher saved successfully.";
    }
    
    function update_advance_salary_voucher()
    {
        // cash_in_hand setting changes if the instrument was/is cash
        $db = JFactory::getDbo();
        
        $advance_id = intval(JRequest::getVar("advance_id"));
        
        $payment_date = date("Y-m-d", strtotime(JRequest::getVar("payment_date")));
        $employee_id = intval(JRequest::getVar("employee_id"));
        $instrument = intval(JRequest::getVar("instrument"));
        $instrument_no = JRequest::getVar("instrument_no");
        $instrument_bank = intval(JRequest::getVar("instrument_bank"));
        $amount = floatval(JRequest::getVar("amount"));
        $remarks = JRequest::getVar("remarks");
        
        $query = "select amount, instrument from `#__hr_advance_salary_payments` where id=" . $advance_id;
        $db->setQuery($query);
        $advance = $db->loadObject();
        
        if($advance->instrument == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric+" . floatval($advance->amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
        }
        
        $advance = new stdClass();

        $advance->id = $advance_id;
        $advance->employee_id = $employee_id;
        $advance->payment_date = $payment_date;
        $advance->amount = $amount;
        $advance->instrument = $instrument;
        $advance->instrument_no = ($instrument_no != "" ? $instrument_no : "");
        $advance->instrument_bank = $instrument_bank;
        $advance->remarks = $remarks;
        
        $db->updateObject( "#__hr_advance_salary_payments", $advance, "id" );
        
        if($instrument == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric-" . floatval($amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "select employee_name from `#__hr_employees` where id=" . $employee_id;
        $db->setQuery($query);
        $employee_name = $db->loadResult();
        
        Functions::log_activity("Advance payment voucher of " . $amount . "/- for " . $employee_name . "(" . $employee_id . ") has been updated.", "", $advance_id);
        return "Advance payment voucher updated successfully.";
    }
    
    function delete_advance_voucher()
    {
        // cash_in_hand setting changes if the instrument was cash
        $db = JFactory::getDbo();
        
        $advance_id = intval(JRequest::getVar("advance_id"));
        
        $query = "select e.employee_name, a.employee_id, a.amount, a.instrument from `#__hr_advance_salary_payments` a inner join `#__hr_employees` e on a.employee_id=e.id where a.id=" . $advance_id;
        $db->setQuery($query);
        $advance = $db->loadObject();
        
        $query = "delete from `#__hr_advance_salary_payments` where id=" . $advance_id;
        $db->setQuery($query);
        $db->query();
        
        if($advance->instrument == CASH)
        {
            $query = "update `#__settings` set `value_numeric`=value_numeric+" . floatval($advance->amount) . " where `key`='cash_in_hand'";
            $db->setQuery($query);
            $db->query();
        }
        
        Functions::log_activity("Advance payment voucher of " . $advance->amount . "/- for " . $advance->employee_name . "(" . $advance->employee_id . ") has been deleted.", "", $advance_id);
        return "Advance payment deleted successfully.";
    }
    
    function edit_advance_deduction()
    {
        // all the advances that were recovered in salary are updated by this function
        // amount_cleared, cleared column, advance_deduction are altered according to the changes
         
        $db = JFactory::getDbo();
        
        $month = intval(JRequest::getVar("month"));
        $year = intval(JRequest::getVar("year"));
        $salary_id = intval(JRequest::getVar("salary_id"));
        $employee_id = intval(JRequest::getVar("employee_id"));
        $total_salary = floatval(JRequest::getVar("total_salary"));
        $item_ids = JRequest::getVar("item_ids");
        $advance_ids = JRequest::getVar("advance_ids");
        $deduction_amount = JRequest::getVar("deduction_amount");
        $original_deduction_amount = JRequest::getVar("original_deduction_amount");
        
        $advance_deduction = 0;
        
        for($i=0;$i<count($item_ids);$i++)
        {
            if(floatval($deduction_amount[$i]) == 0)
            {
                $query = "delete from `#__hr_salary_items` where id=" . intval($item_ids[$i]);
                $db->setQuery($query);
                $db->query();
                
                $query = "update `#__hr_advance_salary_payments` set amount_cleared=amount_cleared - " . floatval($original_deduction_amount[$i]) . " where id=" . intval($advance_ids[$i]);
                $db->setQuery($query);
                $db->query();
                
                $query = "update `#__hr_advance_salary_payments` set cleared=" . NO . " where amount_cleared<amount and id=" . intval($advance_ids[$i]);
                $db->setQuery($query);
                $db->query();
                
                $total_salary = $total_salary + floatval($original_deduction_amount[$i]);
            }
            else
            {
                $query = "update `#__hr_salary_items` set amount=" . floatval($deduction_amount[$i]) . " where id=" . intval($item_ids[$i]);
                $db->setQuery($query);
                $db->query();
                
                $query = "update `#__hr_advance_salary_payments` set amount_cleared=amount_cleared - " . floatval($original_deduction_amount[$i]) . " + " . floatval($deduction_amount[$i]) . " where id=" . intval($advance_ids[$i]);
                $db->setQuery($query);
                $db->query();
                
                $query = "update `#__hr_advance_salary_payments` set cleared=" . NO . " where amount_cleared<amount and id=" . intval($advance_ids[$i]);
                $db->setQuery($query);
                $db->query();
                
                $total_salary = $total_salary + floatval($original_deduction_amount[$i]) - floatval($deduction_amount[$i]);
                $advance_deduction += floatval($deduction_amount[$i]);
            }
        }
        
        $query = "update `#__hr_salary` set total_salary=" . $total_salary . ", advance_deduction=" . $advance_deduction . " where id=" . $salary_id;
        $db->setquery($query);
        $db->query();
        
        $query = "select employee_name from `#__hr_employees` where id=" . $employee_id;
        $db->setQuery($query);
        $employee_name = $db->loadResult();
        
        Functions::log_activity("Advance deduction in salary has been changed for " . $employee_name . "(" . $employee_id . ") for month " . date("F'Y", strtotime($year . '-' . $month . '-01')) . ".");
        return "Advance deduction updated successfully.";
    }
}
?>