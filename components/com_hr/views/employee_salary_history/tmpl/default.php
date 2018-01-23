<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        j("#refresh, #clear").addClass("ui-button ui-widget ui-state-default ui-corner-all");
    });
    
    function get_salary_details()
    {
        if(j("#from_month").val() == 0 || j("#from_year").val() == 0 || j("#to_month").val() == 0 || j("#to_year").val() == 0)
        {
            alert("Please select both from and to month,year.");
            return false;
        }
        
        j.get("index.php?option=com_hr&view=employee_salary_history&tmpl=xml&employee_id=<? echo $this->employee_id; ?>&from_month=" + j("#from_month").val() + "&from_year=" + j("#from_year").val() + "&to_month=" + j("#to_month").val() + "&to_year=" + j("#to_year").val(), function(data){
            if(data != "")
            {
                j("#salary_history").html(j(data).filter("#salary_history").html());
                j("#refresh, #clear").addClass("ui-button ui-widget ui-state-default ui-corner-all");
            }
        });
    }
    
    function clear_records()
    {
        j.get("index.php?option=com_hr&view=employee_salary_history&tmpl=xml&employee_id=<? echo $this->employee_id; ?>", function(data){
            if(data != "")
            {
                j("#salary_history").html(j(data).filter("#salary_history").html());
                j("#refresh, #clear").addClass("ui-button ui-widget ui-state-default ui-corner-all");
            }
        });
    }
</script>
<div id="salary_history">
    <table>
        <tr>
            <td align='right'>From : </td>
            <td align='right'>
                <select id="from_month" style="width:90px;">
                    <?
                        $months = array("0"=>"","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
                        foreach($months as $key=>$month)
                        {
                            ?>
                            <option value="<? echo $key; ?>" <? echo ($key == $this->from_month ? "selected='selected'" : ""); ?> ><? echo $month; ?></option>
                            <?
                        }
                    ?>
                </select>
            </td>
            <td align='right'>
                <select id="from_year" style="width:60px;">
                    <option value="0"></option>
                    <?
                        for($y=date("Y");$y>=2015;$y--)
                        {
                            ?>
                            <option value="<? echo $y; ?>" <? echo ($y == $this->from_year ? "selected='selected'" : ""); ?> ><? echo $y; ?></option>
                            <?
                        }
                    ?>
                </select>
            </td>
            <td align='right'>To : </td>
            <td align='right'>
                <select id="to_month" style="width:90px;">
                    <?
                        $months = array("0"=>"","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
                        foreach($months as $key=>$month)
                        {
                            ?>
                            <option value="<? echo $key; ?>" <? echo ($key == $this->to_month ? "selected='selected'" : ""); ?> ><? echo $month; ?></option>
                            <?
                        }
                    ?>
                </select>
            </td>
            <td align='right'>
                <select id="to_year" style="width:60px;">
                    <option value="0"></option>
                    <?
                        for($y=date("Y");$y>=2015;$y--)
                        {
                            ?>
                            <option value="<? echo $y; ?>" <? echo ($y == $this->to_year ? "selected='selected'" : ""); ?> ><? echo $y; ?></option>
                            <?
                        }
                    ?>
                </select>
            </td>
            <td align='right'>
                <input type="button" value="Refresh" id="refresh" onclick="get_salary_details();">
                <input type="button" value="Clear" id="clear" onclick="clear_records();">
            </td>
        </tr>
    </table>
    <br />
    <table class="clean floatheader centreheadings spread">
        <tr>
            <th>#</th>
            <th>Month</th>
            <th>Gross Salary</th>
            <th>Total Days</th>
            <th>Total Days Payable</th>
            <th>Net Payable Amount</th> 
        </tr>
        <?
            if(count($this->salary_details) > 0)
            {
                $x = 1;
                $net_payable = 0;
                foreach($this->salary_details as $salary)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo $x++; ?></td>
                        <td align="center"><? echo date("F'Y", strtotime($salary->salary_year . '-' . $salary->salary_month . "-01")); ?></td>
                        <td align="right"><? echo round_2dp($salary->gross_salary); ?></td>
                        <td align="center"><? echo $salary->working_days; ?></td>
                        <td align="center"><? echo $salary->attendance; ?></td>
                        <td align="right"><? echo round_2dp($salary->total_salary); $net_payable += round_2dp($salary->total_salary); ?></td>
                    </tr>
                    <?
                }
                ?>
                <tr>
                    <td colspan="5" align="right"><b>Total : </b></td>
                    <td align="right"><b><? echo round_2dp($net_payable); ?></b></td>
                </tr>
                <?
            }
            else
            {
                ?>
                <tr>
                    <td colspan="6" align="center">No records to display.</td>
                </tr>
                <?
            }
        ?>
    </table>
</div>