<?php
    defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<script>
    j(function(){
        j("#attendance_date").datepicker({
            "dateFormat" : "dd-M-yy",
             changeMonth: true,
             changeYear: true
        });
        
        get_day();
        j("#location_id").chosen();
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
        j("#attendance_day").html("Day : <b>" + day[attendanceDay] + "</b>");
    }
    
    function get_daily_attendance()
    {
        if(j("#attendance_date").val() == "")
        {
            alert("Select attendance date.");
            return false;
        }
        
        go("index.php?option=com_hr&view=daily_attendance_report&location_id=" + j("#location_id").val() + "&attendance_date=" + j("#attendance_date").val());
    }
    
    function delete_daily_attendance(voucher_id, attendance_date)
    {
        if(confirm("Are you sure you want to delete all entries in this voucher?"))
        {
            go("index.php?option=com_hr&task=delete_daily_attendance&voucher_id=" + voucher_id + "&attendance_date=" + attendance_date);
        }
        else
        {
            return false;
        }
    }
    
</script>

<h1 id="report_heading">
    Daily Attendance Report
    <?
        echo ($this->attendance_date != "" ? " of " . date("d-M-Y", strtotime($this->attendance_date)) : "");
    ?>
</h1>
<table>
    <tr>
        <td>Location : </td>
        <td>
            <select name="location_id" id="location_id" style="width:150px;">
                <option value="0" <? echo ($this->location_id == 0 ? "selected='selected'" : ""); ?> >All</option>
                <?
                    foreach($this->locations as $location)
                    {
                        ?><option value="<? echo $location->id; ?>" <? echo ($location->id == $this->location_id ? "selected='selected'" : ""); ?>><? echo $location->location_name; ?></option><?
                    }
                ?>
            </select>
        </td>
        <td>Attendance Date : </td>
        <td><input type="text" id="attendance_date" value="<? echo date("d-M-Y", strtotime($this->attendance_date)); ?>" style="width:120px;"></td>
        <td id="attendance_day">Day : </td>
        <td>
            <input type="button" value="Refresh" onclick="get_daily_attendance(); return false;">
            <input type="button" value="Clear" onclick="go('index.php?option=com_hr&view=daily_attendance_report');">
        </td>
    </tr>
</table>

<br />
<?
    if(count($this->daily_attendance) > 0)
    {
        ?><a href="#" onclick="popup_print('<h1>' + j('#report_heading').html() + '</h1>' + j('#daily_attendance').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print noprint"></a><?
    }
?>
 
<div id="daily_attendance">
    <table class="clean centreheadings floatheader" width="80%" id="exporttable">
        <!--<thead>-->
            <tr>
                <th>#</th>
                <th>Emp. Code</th>
                <th>Employee Name</th>
                <th>Location</th>
                <th>Attendance</th>
                <th>In Time</th>
                <th>Break 1 Out/In Time</th>
                <!--<th>Break 2 Out/In Time</th>
                <th>Break 3 Out/In Time</th>-->
                <th>Out Time</th>
                <th>Remarks</th>
                <th class="noprint">Action</th>
            </tr>
        <!--</thead>-->
        <?
            if(count($this->daily_attendance) > 0)
            {
                $x = 0;
                foreach($this->daily_attendance as $attendance)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td align="center"><? echo $attendance->employee_id; ?></td>
                        <td><? echo $attendance->employee_name; ?></td>
                        <td><? echo $attendance->location_name; ?></td>
                        <td align="center">
                            <?
                                if(floatval($attendance->attendance) == 0) { echo "AB"; }
                                else if(floatval($attendance->attendance) == -1) { echo "L"; }
                                else { echo round_2dp($attendance->attendance); }
                            ?>
                        </td>
                        <td align="center"><? echo ($attendance->in_date != "0000-00-00" ? date("d-M-Y H:i", strtotime($attendance->in_date . $attendance->in_time)) : ""); ?></td>
                        <td align="center">
                            <? echo ($attendance->break1_out_date != "0000-00-00" ? date("d-M-Y H:i", strtotime($attendance->break1_out_date . $attendance->break1_out_time)) : "") . "<br />" . ($attendance->break1_in_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($attendance->break1_in_date . $attendance->break1_in_time)) : ""); ?>
                        </td>
                        <!--<td align="center">
                            <? //echo ($attendance->break2_out_date != "0000-00-00" ? date("d-M-Y H:i", strtotime($attendance->break2_out_date . $attendance->break2_out_time)) : "") . "<br />" . ($attendance->break2_in_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($attendance->break2_in_date . $attendance->break2_in_time)) : ""); ?>
                        </td>
                        <td align="center">
                            <? //echo ($attendance->break3_out_date != "0000-00-00" ? date("d-M-Y H:i", strtotime($attendance->break3_out_date . $attendance->break3_out_time)) : "") . "<br />" . ($attendance->break3_in_date != "0000-00-00" ? date("d-M-Y H:i:s", strtotime($attendance->break3_in_date . $attendance->break3_in_time)) : ""); ?>
                        </td>-->
                        <td align="center"><? echo ($attendance->out_date != "0000-00-00" ? date("d-M-Y H:i", strtotime($attendance->out_date . $attendance->out_time)) : ""); ?></td>
                        <td><? echo $attendance->remarks; ?></td>
                        <td align="center" class="noprint">
                            <?
                                if(is_admin() && $this->salary_generated == NO)
                                {
                                    ?>
                                    <img src="custom/graphics/icons/blank.gif" title="Edit" onclick="go('index.php?option=com_hr&view=attendance_entry&m=e&voucher_id=<? echo $attendance->voucher_id; ?>&attendance_date=<? echo $attendance->attendance_date; ?>');" class="edit">
                                    <a href="#" onclick="delete_daily_attendance(<? echo $attendance->voucher_id; ?>,'<? echo $attendance->attendance_date; ?>'); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete"></a>
                                    <?
                                }
                            ?>
                        </td>
                    </tr>
                    <?
                }
            }
            else
            {
                ?>
                <tr>
                    <td colspan="12" align="center">No employees to display.</td>
                </tr>
                <?
            }
        ?>
    </table>
</div>    
