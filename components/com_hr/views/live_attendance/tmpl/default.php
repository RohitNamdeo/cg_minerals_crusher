<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        j("#location_id").chosen();
    });
    
    function get_live_attendance()
    {
        go("index.php?option=com_hr&view=live_attendance&location_id=" + j("#location_id").val());
    }
</script>                  
<h1>Live Attendance</h1>
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
        <td><input type="button" value="Show" onclick="get_live_attendance(); return false;"></td>
        <td><input type="button" value="Clear" onclick="go('index.php?option=com_hr&view=live_attendance');"></td>
    </tr>
</table>
<br />
<table class="clean centreheadings floatheader" width="500">
    <!--<thead>-->
        <tr>
            <th>#</th>
            <th>Emp. Code</th>
            <th>Name</th>
            <th>Present</th>
            <th>In/Out</th>
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
                    <td align="center"><? echo $x++;?></td>
                    <td align="center"><? echo $employee->id;?></td>
                    <td><? echo $employee->employee_name;?></td>
                    <td align="center"><? echo $employee->present;?></td>
                    <td width="200" align="center"><? echo $employee->in_out_entry;?></td>
                </tr>
                <?
            }
        }
        else
        {
            ?>
            <tr>
                <td colspan="5" align="center">No employees found.</td>
            </tr>
            <?
        }
    ?>
</table>