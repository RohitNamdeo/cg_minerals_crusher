<?php
    defined('_JEXEC') or die('Restricted access');
?>
<style>
    .sunday{
/*        color: #FF00FF;*/
        background-color: #73E1E1;
    }
</style>
<script>
    j(function(){
        j("#from_date, #to_date").datepicker({"dateFormat" : "dd-M-yy"});
        j( "input:submit,input:reset,input:button, button").button();
    });
    
    function get_attendance()
    {
        from_date = j("#from_date").val();
        to_date = j("#to_date").val();
        
        if(from_date != "" && to_date != "")
        {
            j.get("index.php?option=com_hr&view=employee_attendance_history&tmpl=xml&from_date=" + from_date + "&to_date=" + to_date + "&employee_id=<? echo $this->employee_id; ?>", function(data){
                if(data != "")
                {
                    j("#attendance_history").html(data);
                }
            });
        }
        else
        {
            alert("Select dates.");
            return false;
        }
    }
    
    function clear_attendance()
    {
        j.get("index.php?option=com_hr&view=employee_attendance_history&tmpl=xml&employee_id=<? echo $this->employee_id; ?>", function(data){
            if(data != "")
            {
                j("#attendance_history").html(data);
            }
        });
    }
</script>
<div id="attendance_history">
    <table>
        <tr>
            <td>From Date : </td>
            <td><input type="text" id="from_date" value="<? echo date("d-M-Y", strtotime($this->from_date)); ?>" style="width:80px;"></td>
            <td>To Date : </td>
            <td><input type="text" id="to_date" value="<? echo date("d-M-Y", strtotime($this->to_date)); ?>" style="width:80px;"></td>
            <td>
                <input type="button" value="Refresh" onclick="get_attendance(); return false;">
                <input type="button" value="Clear" onclick="clear_attendance(); return false;">
            </td>
        </tr>
    </table>
    <?
        if(count($this->attendance) > 0)
        {
            ?>
            <table class="clean centreheadings">
                <tr>
                    <th>Month</th>
                    <?
                        for($i=1; $i<=31; $i++)
                        {
                            ?><th><? echo $i; ?></th><?
                        }
                    ?>
                    <th>Total</th>
                </tr>
                <?  
                    $grand_total = 0;
                    foreach($this->months as $key=>$month)
                    {
                        $total_attendance = 0;
                        ?>
                        <tr>
                            <th style="text-align:left;"><? echo date("Y-F", strtotime($month . '-01')); ?></th>
                            <?
                                for($i=1; $i<=31; $i++)
                                {
                                    $attendance_date = date("Y-m-d", strtotime($month . '-' . $i));
                                    ?>
                                    <td align="center" class="<? echo (date('w', strtotime($attendance_date)) == 0 ? "sunday" : ""); ?>" >
                                        <?
                                            if(isset($this->attendance[$attendance_date]))
                                            {
                                                 echo $this->attendance[$attendance_date]->attendance;
                                                 $total_attendance += $this->attendance[$attendance_date]->attendance;
                                            }
                                        ?>
                                    </td>
                                    <?
                                }
                            ?>
                            <td align="center"><? echo round_2dp($total_attendance); $grand_total += $total_attendance; ?></td>
                        </tr>
                        <?
                    }
                ?>
                <tr>
                    <td colspan="32" align="right"><b>Total : </b></td>
                    <td align="center"><b><? echo round_2dp($grand_total); ?></b></td>
                </tr>
            </table>
            <?
        }
        else
        {
            echo "No records found!";
        }
    ?>
</div>