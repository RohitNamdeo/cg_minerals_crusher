<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        j("#attendance_date").datepicker({"dateFormat" : "dd-M-yy" , changeMonth:true, changeYear:true});
        
        if(j("#attendance_date").val()!="")
        {
            get_day();
        }
        
        j("#attendance_date").change(function(){
            get_day();
        }); 
    });

    function get_day()
    {
        var attendanceDate = j("#attendance_date").datepicker("getDate");
        var attendanceDate = new Date(attendanceDate);
        
        var attendanceDay = attendanceDate.getDay();
        
        day=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
        j("#attendance_day").html("<b>" + day[attendanceDay] + "</b>");
    }

    function get_employee_list()
    {
        if(j("#attendance_date").val() == "")
        {
            alert("Please select attendance date.");
            return false;
        }
        go("index.php?option=com_hr&view=attendance_entry&attendance_date=" + j("#attendance_date").val());
    }

    function clear_records()
    {
        j("#employee_attendance").html("");
    }

    function regenerate_attendance()
    {
        if(confirm("Are you sure?"))
        {
            go("index.php?option=com_hr&task=generate_attendance&attendance_date=<? echo $this->attendance_date; ?>&regenerate=<? echo YES; ?>");
        }
        else
        {
            return false;
        }
    }
</script>
<h1>Employee Daily Attendance</h1>
<table> 
    <tr>
        <td>Attendance Date : </td>
        <td>
            <input type="text" id="attendance_date" value="<? echo ($this->attendance_date != "" ? date("d-M-Y",strtotime($this->attendance_date)) : ""); ?>" onchange="clear_records();">
        </td>
        <td>Day : </td>
        <td id="attendance_day">
        </td>
        <td>
            <input type="button" value="Show" onclick="get_employee_list();">
            <input type="button" value="Reset" onclick="go('index.php?option=com_hr&view=attendance_entry');">
        </td>
    </tr>
</table>
<div id="employee_attendance">
    <?
        if(count($this->employees) > 0)
        {
            ?>
            <br />
            <input type="button" value="Regenerate Attendance" onclick="regenerate_attendance(); return false;">
            <br /><br />
            <table id="" class="clean centreheadings floatheader">
                <!--<thead>-->
                    <tr>
                        <th width="20" rowspan="2">#</th>
                        <th width="20" rowspan="2">Emp. Code</th>
                        <th width="200" rowspan="2">Employee Name</th>
                        <th rowspan="2">In Time</th>
                        <th colspan="2">Break 1</th>
                        <!--<th colspan="2">Break 2</th>
                        <th colspan="2">Break 3</th>-->
                        <th rowspan="2">Out Time</th>
                        <th rowspan="2">Attendance</th>
                    </tr>
                    <tr>
                        <th>Out Time</th>
                        <th>In Time</th>
                        <!--<th>Out Time</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th>In Time</th>-->
                    </tr>
                <!--</thead>-->
                <?
                    $x = 1;
                    foreach($this->employees as $employee)
                    {
                        ?>
                        <tr>
                            <td align="center"><? echo $x++; ?></td>
                            <td align="center"><? echo $employee->employee_id; ?></td>
                            <td><? echo $employee->employee_name; ?></td>
                            <td align="center"><? echo ($employee->in_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->in_date . $employee->in_time)) : ""); ?></td>
                            <td align="center"><? echo ($employee->break1_out_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break1_out_date . $employee->break1_out_time)) : ""); ?></td>
                            <td align="center"><? echo ($employee->break1_in_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break1_in_date . $employee->break1_in_time)) : ""); ?></td>
                            <!--<td align="center"><? //echo ($employee->break2_out_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break2_out_date . $employee->break2_out_time)) : ""); ?></td>
                            <td align="center"><? //echo ($employee->break2_in_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break2_in_date . $employee->break2_in_time)) : ""); ?></td>
                            <td align="center"><? //echo ($employee->break3_out_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break3_out_date . $employee->break3_out_time)) : ""); ?></td>
                            <td align="center"><? //echo ($employee->break3_in_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->break3_in_date . $employee->break3_in_time)) : ""); ?></td>-->
                            <td align="center"><? echo ($employee->out_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($employee->out_date . $employee->out_time)) : ""); ?></td>
                            <td align="center">
                                <?
                                    if(floatval($employee->attendance) == 0) { echo "AB"; }
                                    else if(floatval($employee->attendance) == -1) { echo "L"; }
                                    else { echo round_2dp($employee->attendance); }
                                ?>
                            </td>
                        </tr>
                        <?
                    }
                ?>
            </table>
            <?
        }
        else
        {
            echo $this->msg;
        }
    ?>
</div>