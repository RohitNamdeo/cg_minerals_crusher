<?php 
defined('_JEXEC') or die('Restricted access'); 
?>
<script>
j(function(){
    j("#from,#to").datepicker({ dateFormat: "dd-M-yy"});
    j("#employee_id").chosen({allow_single_deselect: true });
});

function delete_activity_log(activity_id)
{
    if(confirm("Are you sure?"))
    {
        go('index.php?option=com_master&task=delete_activity_log&activity_id=' + activity_id);
    }
    else return false;
}

function get_activity_log()
{
    go("index.php?option=com_master&view=activitylog&from=" + j("#from").val() + "&to=" + j("#to").val() + "&emp_id=" + j("#employee_id").val());
}
</script>
<h1>Activity Log</h1>
<div>
    User Name :
    <select name="employee_id" id="employee_id" style="width:200px;" data-placeholder="-Select-">
    <?
        echo "<option value=''></option>";
        foreach($this->employees as $employee)
        {
            echo "<option value=" . $employee->user_id . " "  .($employee->user_id == $this->employee_id ? "selected='selected'" : ""). ">" . $employee->user_name . "</option>";    
        }
    ?>
    </select>
    From : <input type="text" name="from" id="from" value="<? echo $this->from; ?>" style="width:100px;" readonly="readonly"/>
    To : <input type="text" name="to" id="to" value="<? echo $this->to; ?>" style="width:100px;" readonly="readonly"/>
    <input type="button" value="Refresh" onclick="get_activity_log();">
    <br />
</div> 
<br />
<span>
    <?
        if($this->current_page >= 1 && $this->pages != 0)
        {
            if ($this->current_page == 1)
            {   
                ?>
                    Prev&nbsp;  
                <?
            }
            else
            {
                $prev = $this->current_page - 1;
                ?>
                    <a href="index.php?option=com_master&view=activitylog&p=<? echo $prev; ?>&emp_id=<? echo $this->employee_id; ?>&from=<? echo $this->from; ?>&to=<? echo $this->to; ?>">Prev</a>
                <?
            }
        }
        if($this->pages != 0)
        {
    ?>    
            <select onchange="window.location.href='index.php?option=com_master&view=activitylog&emp_id=<? echo $this->employee_id; ?>&from=<? echo $this->from; ?>&to=<? echo $this->to; ?>&p=' + this.value;">
                <?       
                for($x = 1; $x <= $this->pages; $x++)
                {
                    ?>
                        <option <? echo ($this->current_page == $x ? "selected='selected'" : "")?> value="<? echo $x; ?>"><? echo $x; ?></option>
                    <?
                }
                ?>
            </select>         
    <?
        }
        if($this->current_page <= $this->pages)
        {
            if ($this->current_page == $this->pages)
            {
                 ?>
                    &nbsp;Next
                 <?
            }
            else
            {
                $next = $this->current_page + 1;
                ?>
                    <a href="index.php?option=com_master&view=activitylog&p=<? echo $next; ?>&emp_id=<? echo $this->employee_id; ?>&from=<? echo $this->from; ?>&to=<? echo $this->to; ?>">Next</a>
                <?
            }
        }
    ?>
</span>
<br /><br />
<table class="clean spread">
    <tr align="center"> 
        <th width="20">S.No.</th>
        <th>Time</th>
        <th>Activity</th>
        <th>Activity By</th>
        <th>IP Address</th>
    <tr>
    <?php
    if(count($this->activity_logs) > 0)
    { 
        $x = ($this->current_page - 1) * 100;  
        foreach($this->activity_logs as $activity_log)
        { 
            ?>
            <tr>
                <td align="center">
                    <? echo ++$x; ?>
                </td>
                <td align="center">
                    <?php if($activity_log->timestamp!="0000-00-00" && $activity_log->timestamp!="1970-01-01") echo date("d-M-Y H:i:s" ,strtotime($activity_log->timestamp)); ?>
                </td>
                <td style="width:600px;">
                    <?php echo $activity_log->message; ?>
                </td>
                <td align="center">
                    <?php echo $activity_log->user_name; ?>
                </td>
                <td align="center">
                    <?php echo $activity_log->ip_address; ?>
                </td>
            </tr>
        <?php 
        }
    }
    ?>
</table>
<br /><br />
<span>
    <?
        if($this->current_page >= 1 && $this->pages != 0)
        {
            if ($this->current_page == 1)
            {   
                ?>
                    Prev&nbsp;  
                <?
            }
            else
            {
                $prev = $this->current_page - 1;
                ?>
                    <a href="index.php?option=com_master&view=activitylog&p=<? echo $prev; ?>&emp_id=<? echo $this->employee_id; ?>&from=<? echo $this->from; ?>&to=<? echo $this->to; ?>">Prev</a>
                <?
            }
        }
        if($this->pages != 0)
        {
    ?>    
            <select onchange="window.location.href='index.php?option=com_master&view=activitylog&emp_id=<? echo $this->employee_id; ?>&from=<? echo $this->from; ?>&to=<? echo $this->to; ?>&p=' + this.value;">
                <?       
                for($x = 1; $x <= $this->pages; $x++)
                {
                    ?>
                        <option <? echo ($this->current_page == $x ? "selected='selected'" : "")?> value="<? echo $x; ?>"><? echo $x; ?></option>
                    <?
                }
                ?>
            </select>         
    <?
        }
        if($this->current_page <= $this->pages)
        {
            if ($this->current_page == $this->pages)
            {
                 ?>
                    &nbsp;Next
                 <?
            }
            else
            {
                $next = $this->current_page + 1;
                ?>
                    <a href="index.php?option=com_master&view=activitylog&p=<? echo $next; ?>&emp_id=<? echo $this->employee_id; ?>&from=<? echo $this->from; ?>&to=<? echo $this->to; ?>">Next</a>
                <?
            }
        }
    ?>
</span>