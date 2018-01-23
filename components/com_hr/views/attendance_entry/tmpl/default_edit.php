<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        j(".attendance").chosen();
    });
    
    function validateForm()
    {
        var error = "";
        j(".attendance").each(function(){
            if(j(this).val() == "")
            {
                error = "invalid";
                return false;
            }
        });
        
        if(error != "")
        {
            alert("Select attendance.");
            return false;
        }
        else
        {
            j("#submit_button").prop("disabled",true);
            j("#attendance_form").submit();
        }
    }
</script>
<h1>Edit Daily Attendance</h1>
<table> 
    <tr>
        <td><b>Attendance Date : </b></td>
        <td><b><? echo date("d-M-Y",strtotime($this->attendance_date)); ?></b></td>
</table>
<div id="employee_attendance">
    <form method="post" id="attendance_form" action="index.php?option=com_hr&task=update_attendance&r=<? echo base64_encode('index.php?option=com_hr&view=daily_attendance_report&attendance_date=' . $this->attendance_date); ?>">
        <?
            if(count($this->employees) > 0)
            {
                ?>
                <table class="clean centreheadings floatheader">
                    <!--<thead>-->
                        <tr>
                            <th width="20" rowspan="2">#</th>
                            <th width="20" rowspan="2">Emp. Code</th>
                            <th width="200" rowspan="2">Employee Name</th>
                            <th rowspan="2">In Time</th>
                            <th colspan="2">Break 1</th>
                            <th colspan="2">Break 2</th>
                            <th colspan="2">Break 3</th>
                            <th rowspan="2">Out Time</th>
                            <th rowspan="2">Attendance</th>
                            <th rowspan="2">Remarks</th>
                        </tr>
                        <tr>
                            <th>Out Time</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>In Time</th>
                        </tr>
                    <!--</thead>-->
                    <?
                        $x = 1;
                        foreach($this->employees as $employee)
                        {
                            ?>
                            <input type="hidden" name="attendance_ids[]" value="<? echo $employee->attendance_id; ?>">
                            <tr>
                                <td align="center"><? echo $x++; ?></td>
                                <td align="center"><? echo $employee->employee_id; ?></td>
                                <td><? echo $employee->employee_name; ?></td>
                                <td align="center"><? echo ($employee->in_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->in_date . $employee->in_time)) : ""); ?></td>
                                <td align="center"><? echo ($employee->break1_out_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break1_out_date . $employee->break1_out_time)) : ""); ?></td>
                                <td align="center"><? echo ($employee->break1_in_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break1_in_date . $employee->break1_in_time)) : ""); ?></td>
                                <td align="center"><? echo ($employee->break2_out_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break2_out_date . $employee->break2_out_time)) : ""); ?></td>
                                <td align="center"><? echo ($employee->break2_in_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break2_in_date . $employee->break2_in_time)) : ""); ?></td>
                                <td align="center"><? echo ($employee->break3_out_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break3_out_date . $employee->break3_out_time)) : ""); ?></td>
                                <td align="center"><? echo ($employee->break3_in_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break3_in_date . $employee->break3_in_time)) : ""); ?></td>
                                <td align="center"><? echo ($employee->out_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->out_date . $employee->out_time)) : ""); ?></td>
                                <td align="center">
                                    <select name="attendance[]" class="attendance" style="width:100px; text-align:right;">
                                        <option value="">- Select -</option>
                                        <option value="1" <? echo ($employee->attendance == 1 ? "selected='selected'" : ""); ?> style="text-align:left;">1</option>
                                        <option value="0.5" <? echo ($employee->attendance == 0.5 ? "selected='selected'" : ""); ?> style="text-align:left;">0.5</option>
                                        <option value="0" <? echo ($employee->attendance == 0 ? "selected='selected'" : ""); ?> style="text-align:left;">AB</option>
                                        <option value="-1" <? echo ($employee->attendance == -1 ? "selected='selected'" : ""); ?> style="text-align:left;">L</option>
                                    </select>
                                </td>
                                <td align="center"><input type="text" name="remarks[]" value="<? echo $employee->remarks; ?>" style="width:300px;"></td>
                            </tr>
                            <?
                        }
                    ?>
                </table>
                <br />
                <input type="hidden" name="voucher_id" value="<? echo $this->voucher_id; ?>">
                <input type="hidden" name="attendance_date" value="<? echo $this->attendance_date; ?>">
                <input type="button" value="Update (Alt + Z)" id="submit_button" onclick="validateForm();">
                <input type="button" value="Cancel" onclick="history.go(-1);">
                <?
            }
        ?>
    </form>
</div>