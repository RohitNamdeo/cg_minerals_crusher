<?php
    defined('_JEXEC') or die('Restricted access');
?>
<style>
    input[type='text'], select, textarea{
        width: 300px;
    }
    
    textarea{
        resize: none;
    }
</style>
<script>
    j(function(){
        j("#doj").datepicker({"dateFormat" : "dd-M-yy", "changeMonth": true, "changeYear": true, "yearRange":"1940:<? echo date("Y") + 50;?>"});
        j("#location_id, #machine_id").chosen();
    });
    
    j(document).on("keypress","#gross_salary" ,function(e){
        prevent_char(e.which,e);
    });
    
    j(document).on("keypress","#mobile_no, #machine_enrollment_no" ,function(e){
        strict_numbers(e.which,e);
    });    
                
    function validateForm()
    {   
        if(j("#employee_name").val() == "")
        {
            alert("Please enter employee name.");
            return false;
        }
        
        if(j("#location_id").val() == 0)
        {
            alert("Please select location.");
            return false;
        }
        
        if(j("#doj").val() == "")
        {
            alert("Select date of joining.");
            return false;
        }
        
        if(j("#gross_salary").val() == "" || j("#gross_salary").val() == 0)
        {
            alert("Enter valid gross salary.");
            return false;
        }
        
        if(j("#machine_id").val() == 0)
        {
            alert("Select attendance machine.");
            return false;
        }
        if(j("#machine_no").val() == "")
        {
            alert("Enter attendance machine no.");
            return false;
        }
        if(j("#machine_enrollment_no").val() == "") 
        {
            alert("Enter machine enrollment no.");
            return false;
        }
            
        if(j("#mobile_no").val() == "" || j("#mobile_no").val().length != 10)
        {
            alert("Enter valid numeric 10 digits mobile no.");
            return false;
        }
        
        if(j("#address").val() == "")
        {
            alert("Enter address.");
            return false;
        }
        
        j.get("index.php?option=com_hr&task=check_attendance_machine_duplicity&tmpl=xml&machine_id=" + j("#machine_id").val() + "&machine_enrollment_no=" + j("#machine_enrollment_no").val(), function(count){
            if(count > 0)
            {
                alert("Employee with same machine enrollment no. for the selected machine already exists!");
                return false;
            }
            else
            {
                j("#submit_button").attr("disabled", true);
                j("#registration_form").submit();
            }
        });
    }
</script>
<h1>Employee Registration</h1>
<form id="registration_form" method="post" action="index.php?option=com_hr&task=employee_registration">
    <table class="clean">
        <tr>
            <td>Employee Name</td>
            <td><input type="text" name="employee_name" id="employee_name"></td>
        </tr>
        <tr>
            <td>Designation</td>
            <td><input type="text" name="designation" id="designation"></td>
        </tr>
        <tr>
            <td>Location</td>
            <td>
                <select name="location_id" id="location_id">
                    <option value="0"></option>
                    <?
                        foreach($this->locations as $location)
                        {
                            ?><option value="<? echo $location->id; ?>"><? echo $location->location_name; ?></option><?
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Date of Joining</td>
            <td><input type="text" name="doj" id="doj"></td>
        </tr>
        <tr>
            <td>Gross Salary</td>
            <td><input type="text" name="gross_salary" id="gross_salary"></td>
        </tr>
        <tr>
            <td>Attendance Machine</td>
            <td>
                <select name="machine_id" id="machine_id" style='width:300px;'>
                    <option value="0"></option>
                    <?
                        if(count($this->devices) > 0)
                        {
                            foreach($this->devices as $device)
                            {
                                ?><option value="<? echo $device->id; ?>"><? echo $device->name; ?></option><?
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Machine No.</td>
            <td><input type="text" name="machine_no" id="machine_no" /></td>
        </tr>
        <tr>
            <td>Machine Enrollment No.</td>
            <td><input type="text" name="machine_enrollment_no" id="machine_enrollment_no" /></td>
        </tr>
        <tr>
            <td>Mobile No.</td>
            <td><input type="text" name="mobile_no" id="mobile_no"></td>
        </tr>
        <tr>
            <td valign="top">Address</td>
            <td><textarea name="address" id="address"></textarea></td>
        </tr>
        <tr>
            <td valign="top">Remarks</td>
            <td><textarea name="remarks" id="remarks"></textarea></td>
        </tr>
    </table>
    <br />
    <input type="button" value="Submit (Alt + Z)" id="submit_button" onclick="validateForm(); return false;">
    <input type="button" value="Cancel" onclick="history.go(-1);">
</form>