<?php
    defined('_JEXEC') or die('Restricted access');
    $dayNames = array('Su','Mo','Tu','We','Th','Fr','Sa');
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
?>
<script> 
    j(function(){
        j("#location_id, #month, #year").chosen();
    });
    
    function get_employees()
    {
        go("index.php?option=com_hr&view=monthly_attendance_report&month=" + j("#month").val() + "&year=" + j("#year").val() + "&location_id=" + j("#location_id").val());
    }
    
</script>
<h1 id="report_heading">Monthly Attendance Report 
    <? 
        if($this->month != 0 && $this->year != 0)
        {
            echo "for " . date("F'Y", strtotime($this->year . '-' . $this->month . '-01'));
        }
    ?>
</h1>

<table>
    <tr>
        <td>Location : </td>
        <td>
            <select id="location_id" style="width:150px;">
                <option value="0"></option>
                <?
                    if(count($this->locations) > 0)
                    {
                        foreach($this->locations as $location)
                        {
                            ?>
                            <option value="<? echo $location->id; ?>" <? echo ($this->location_id == $location->id ? "selected='selected'" : ""); ?> ><? echo $location->location_name; ?></option>
                            <?
                        }
                    }
                ?>
            </select>
        </td>
        <td>Month : </td>
        <td>
            <select id="month" style="width:100px;">
                <?
                    $months = array("0"=>"","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
                    foreach($months as $key=>$month)
                    {
                        ?>
                        <option value="<? echo $key; ?>" <? echo ($key == $this->month ? "selected='selected'" : ""); ?> ><? echo $month; ?></option>
                        <?
                    }
                ?>
            </select>
        </td>
        <td>Year : </td>
        <td>
            <select id="year" style="width:80px;">
                <option value="0"></option>
                <?
                    for($y=date("Y");$y>=2015;$y--)
                    {
                        ?>
                        <option value="<? echo $y; ?>" <? echo ($y == $this->year ? "selected='selected'" : ""); ?> ><? echo $y; ?></option>
                        <?
                    }
                ?>
            </select>
        </td>
        <td valign="bottom"><input type="button" value="Refresh" onclick="get_employees();"></td>
        <td valign="bottom"><input type="button" value="Clear" onclick="go('index.php?option=com_hr&view=monthly_attendance_report');"></td>
    </tr>
</table>
<br />
<?
    if(count($this->employees) > 0)
    {
        ?><a href="#" onclick="popup_print('<h1>' + j('#report_heading').html() + '</h1>' + j('#monthly_attendance_report').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print noprint"></a><br /><?
    }
?>
<div id="monthly_attendance_report">
    <table class="clean floatheader centreheadings spread" >
        <!--<thead>-->
            <tr>
                <th>#</th>
                <th>Emp.<br />Code</th>
                <th>Employee Name</th>
                <th>Location</th>
                <th>Total</th>
                <?
                    for($i=1;$i<=$days_in_month;$i++)
                    {
                        echo "<th>" . $i . "<br />(" . $dayNames[date('w', strtotime($this->year . '-' . $this->month . '-' . $i))] . ")" . "</th>";
                    }
                    
                ?>
            </tr>
        <!--</thead>-->
        <?
            if(count($this->employees) > 0)
            {
                $x = 1;
                foreach($this->employees as $employee)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo $x++; ?></td>
                        <td align="center"><? echo $employee->employee_id; ?></td>
                        <td><? echo $employee->employee_name; ?></td>
                        <td><? echo $employee->location_name; ?></td>
                        <td align="center"><? echo round_2dp($employee->total_attendance); ?></td>
                        <?
                            foreach($employee->attendance as $attendance)
                            {
                                ?><td align="center">
                                    <?
                                        if(isset($attendance))
                                        {
                                            if(floatval($attendance->attendance) == 0) { echo "AB"; }
                                            else if(floatval($attendance->attendance) == -1) { echo "L"; }
                                            else { echo round_2dp($attendance->attendance); }
                                        }
                                    ?>
                                </td><?
                            }
                        ?>
                    </tr>
                    <?
                }
                ?>
                <tr>
                    <td colspan="4" align="right"><b>Total : </b></td>
                    <td align="right"><? echo round_2dp($this->grand_total);?></td>
                    <?
                        foreach($this->total_daywise_attendance as $index=>$total_daywise_attendance)
                        {
                            ?>
                            <td align="center"><? echo round_2dp($total_daywise_attendance);?></td>
                            <?
                        }
                    ?>
                </tr>
                <?
            }
            else
            {
                ?>
                <tr>
                    <td colspan="<? echo 5 + $days_in_month; ?>" align="center">No employees to display.</td>
                </tr>
                <?
            }
        ?>
    </table>
</div>