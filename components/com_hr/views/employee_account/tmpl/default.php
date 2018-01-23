<?php 
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function (){
        j("#employee-profile").tabs();
	    j("#salary").tabs();
    });
</script>
<h1><? echo $this->employee->employee_name; ?>'s Profile</h1>
<?
    if(count($this->employee)> 0)
    {
        ?>
        <div id="employee-profile">
	        <ul>
                <li><a href="#employee_profile"><span>Profile</span></a></li>
                <li><a href="index.php?option=com_hr&view=employee_attendance_history&tmpl=xml&employee_id=<? echo $this->employee->id; ?>"><span>Attendance History</span></a></li>
                <li><a href="#salary"><span>Salary</span></a></li>
	        </ul>
            <div id="salary">
                <ul>
                    <li><a href="index.php?option=com_hr&view=employee_salary_history&tmpl=xml&employee_id=<? echo $this->employee->id; ?>"><span>Salary History</span></a></li>
                    <li><a href="index.php?option=com_hr&view=employee_salary_statement&tmpl=xml&employee_id=<? echo $this->employee->id; ?>"><span>Salary Statement</span></a></li>
                </ul>
            </div>
            <div id="employee_profile" style="padding:20px;">
                <h2>Employee Profile</h2>
                <table class="clean" width="400">
                    <tr>
                        <td>Employee Code</td>
                        <td><? echo $this->employee->id; ?></td>
                    </tr>
                    <tr>
                        <td>Employee Name</td>
                        <td><? echo $this->employee->employee_name; ?></td>
                    </tr>
                    <tr>
                        <td>Designation</td>
                        <td><? echo $this->employee->designation; ?></td>
                    </tr>
                    <tr>
                        <td>Location Name</td>
                        <td><? echo $this->employee->location_name; ?></td>
                    </tr>
                    <tr>
                        <td>DOJ</td>
                        <td><? echo date("d-M-Y", strtotime($this->employee->doj)); ?></td>
                    </tr>
                    <tr>
                        <td>Gross Salary</td>
                        <td><? echo round_2dp($this->employee->gross_salary); ?></td>
                    </tr>
                    <tr>
                        <td>Attendance M/c</td>
                        <td><? echo $this->employee->machine_name; ?></td>
                    </tr>
                    <tr>
                        <td>Machine No.</td>
                        <td><? echo $this->employee->machine_no; ?></td>
                    </tr>
                    <tr>
                        <td>Machine Enrollment No.</td>
                        <td><? echo $this->employee->machine_enrollment_no; ?></td>
                    </tr>
                    <tr>
                        <td>Mobile No.</td>
                        <td><? echo $this->employee->mobile_no; ?></td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td><? echo $this->employee->address; ?></td>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td><? echo $this->employee->remarks; ?></td>
                    </tr>
                    <tr>
                        <td>Account Status</td>
                        <td><? echo ($this->employee->account_status == AC_ACTIVE ? "Active" : "Closed"); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?
    }
    else
    {
        echo  "<br /><h3><b>"."No employee found" ."</b></h3>";   
    }
?>