<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class HrModelHr1 extends JModelLegacy
{
    function check_attendance_machine_duplicity()
    {
        // no two employees can have same machine details on same machine
        
        $db = JFactory::getDbo();
        
        $machine_id = intval(JRequest::getVar("machine_id"));
        $machine_enrollment_no = intval(JRequest::getVar("machine_enrollment_no"));
        $employee_id = intval(JRequest::getVar("employee_id"));
        
        $query = "select count(id) from `#__hr_employees` where machine_id=" . $machine_id . " and machine_enrollment_no=" . $machine_enrollment_no . ($employee_id > 0 ? " and id<>" . $employee_id : "");
        $db->setQuery($query);
        $count = intval($db->loadResult());
        
        echo $count;
    }
    
    function employee_registration()
    {
        $db = JFactory::getDbo();
        
        $employee_name = ucwords(JRequest::getVar("employee_name"));
        $designation = ucwords(JRequest::getVar('designation')); 
        $location_id = intval(JRequest::getVar("location_id")); 
        $doj = date("Y-m-d", strtotime(JRequest::getVar("doj"))); 
        $gross_salary = floatval(JRequest::getVar("gross_salary")); 
        $mobile_no = JRequest::getVar('mobile_no');
        $address = ucfirst(JRequest::getVar('address'));
        $remarks = ucfirst(JRequest::getVar('remarks'));
        $machine_id = intval(JRequest::getVar("machine_id"));
        $machine_no = JRequest::getVar("machine_no");
        $machine_enrollment_no = intval(JRequest::getVar("machine_enrollment_no"));
        
        $registered_by = intval(JFactory::getUser()->id);
        
        $employee = new stdClass();
        
        $employee->employee_name = $employee_name;
        $employee->address = $address;
        $employee->mobile_no = $mobile_no;
        $employee->designation = $designation;
        $employee->doj = $doj;
        $employee->location_id = $location_id;
        $employee->gross_salary = $gross_salary;
        $employee->machine_id = $machine_id;
        $employee->machine_no = $machine_no;
        $employee->machine_enrollment_no = $machine_enrollment_no;
        $employee->account_status = AC_ACTIVE;
        $employee->remarks = $remarks;
        $employee->registered_by = $registered_by;
        
        $db->insertObject("#__hr_employees", $employee, "");
        $employee_id = intval($db->insertid());
            
        Functions::log_activity("New employee " . $employee_name .  "(" . $employee_id . ") has been added.");
        return "Employee registered successfully.";
    }
    
    function update_employee_profile()
    {
        $db = JFactory::getDbo();
        
        $employee_id = intval(JRequest::getVar("employee_id"));
        
        $employee_name = ucwords(JRequest::getVar("employee_name"));
        $designation = ucwords(JRequest::getVar('designation')); 
        $location_id = intval(JRequest::getVar("location_id")); 
        $doj = date("Y-m-d", strtotime(JRequest::getVar("doj"))); 
        $gross_salary = floatval(JRequest::getVar("gross_salary")); 
        $mobile_no = JRequest::getVar('mobile_no');
        $address = ucfirst(JRequest::getVar('address'));
        $remarks = ucfirst(JRequest::getVar('remarks'));
        $machine_id = intval(JRequest::getVar("machine_id"));
        $machine_no = JRequest::getVar("machine_no");
        $machine_enrollment_no = intval(JRequest::getVar("machine_enrollment_no"));
        
        $employee = new stdClass();
        
        $employee->id = $employee_id;
        $employee->employee_name = $employee_name;
        $employee->address = $address;
        $employee->mobile_no = $mobile_no;
        $employee->designation = $designation;
        $employee->doj = $doj;
        $employee->location_id = $location_id;
        $employee->gross_salary = $gross_salary;
        $employee->machine_id = $machine_id;
        $employee->machine_no = $machine_no;
        $employee->machine_enrollment_no = $machine_enrollment_no;
        $employee->remarks = $remarks;
        
        $db->updateObject("#__hr_employees", $employee, "id");
            
        Functions::log_activity("Profile of employee " . $employee_name .  "(" . $employee_id . ") has been updated.");
        return "Employee profile updated successfully.";
    }
    
    function activate_account()
    {
        $db = JFactory::getDbo();
        
        $employee_id = intval(JRequest::getVar("employee_id"));
        
        $query = "update `#__hr_employees` set `account_status`=" . AC_ACTIVE . " where id=" . $employee_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "select `employee_name` from `#__hr_employees` where id=" . $employee_id;
        $db->setQuery($query);
        $employee_name = $db->loadResult();
        
        Functions::log_activity("Account of employee " . $employee_name .  "(" . $employee_id . ") has been activated.");
        return "Employee account activated successfully.";
    }
    
    function deactivate_account()
    {
        $db = JFactory::getDbo();
        
        $employee_id = intval(JRequest::getVar("employee_id"));
        
        $query = "update `#__hr_employees` set `account_status`=" . AC_CLOSED . " where id=" . $employee_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "select `employee_name` from `#__hr_employees` where id=" . $employee_id;
        $db->setQuery($query);
        $employee_name = $db->loadResult();
        
        Functions::log_activity("Account of employee " . $employee_name .  "(" . $employee_id . ") has been deactivated.");
        return "Employee account deactivated successfully.";
    }
}
?>