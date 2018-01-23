<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        j("#month, #year, #location_id, #employee_id").chosen();
        
        j("#month, #year, #location_id, #employee_id").change(function(){
            j("#salary").html("");
        });
    });
    
    function get_salary()
    {
        if(j("#month").val() == 0 || j("#year").val() == 0)
        {
            alert("Please select both month and year.");
            return false;
        }
        go("index.php?option=com_hr&view=paid_salary_report&month=" + j("#month").val() + "&year=" + j("#year").val() + "&location_id=" + j("#location_id").val() + "&employee_id=" + j("#employee_id").val());
    }
</script>
<h1 id="report_heading">
    Paid Salary Report
    <?
        if($this->month != 0 && $this->year != 0)
        {
            echo " for " . date("F'Y", strtotime($this->year . '-' . $this->month . '-01'));
        } 
    ?>
</h1>
<table>
    <tr>
        <td>Employee:</td>
        <td>
            <select id="employee_id" style="width:120px;">
                <option value="0" <? echo ($this->employee_id == 0 ? "selected='selected'" : ""); ?> >All</option>
                <?
                    if(count($this->employees) > 0)
                    {
                        foreach($this->employees as $employee)
                        {
                            ?>
                            <option value="<? echo $employee->employee_id; ?>" <? echo ($this->employee_id == $employee->employee_id ? "selected='selected'" : ""); ?> ><? echo $employee->employee_name; ?></option>
                            <?
                        }
                    }
                ?>
            </select>
        </td>
        <td>Month : </td>
        <td>
            <select id="month" style="width:90px;">
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
        <td>Year :</td>
        <td>
            <select id="year" style="width:90px;">
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
        <td>Location : </td>
        <td>
            <select id="location_id" style="width:150px;">
                <option value="0" <? echo ($this->location_id == 0 ? "selected='selected'" : ""); ?>>All</option>
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
        <td>
            <input type="button" value="Refresh" onclick="get_salary();">
            <input type="button" value="Clear" onclick="go('index.php?option=com_hr&view=paid_salary_report');">
        </td>
    </tr>
</table>
<br />
<?
    if(count($this->salary_details) > 0)
    {
        ?><a href="#" onclick="popup_print('<h1>' + j('#report_heading').html() + '</h1>' + j('#salary').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print noprint"></a><br /><?
    }
?>
<div id="salary">
    <table class="clean floatheader centreheadings" width="80%">
       <!-- <thead>-->
            <tr>
                <th>#</th>
                <th>Emp. Code</th>
                <th>Employee Name</th>
                <th>Gross Salary</th>
                <th>Total Days</th>
                <th>Total Days Payable</th>
                <th>Gross Payable Before Deduction</th>
                <th>Advance Deduction</th>
                <th>Net Payable Amount</th> 
                <th>Amount Paid</th> 
                <th>Balance</th> 
            </tr>
        <!--</thead>-->
        <?
            if(count($this->salary_details) > 0)
            {
                $x = 1;
                $net_gross_salary = 0;
                $net_advance_deduction = 0;
                $net_payable = 0;
                $net_paid = 0;
                $net_balance = 0;
                foreach($this->salary_details as $salary)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo $x++; ?></td>
                        <td align="center"><? echo $salary->employee_id; ?></td>
                        <td><? echo $salary->employee_name; ?></td>
                        <td align="right"><? echo round_2dp($salary->actual_gross_salary); ?></td>
                        <td align="center"><? echo $salary->working_days; ?></td>
                        <td align="center"><? echo $salary->attendance; ?></td>
                        <td align="right"><? echo round_2dp($salary->gross_salary); $net_gross_salary += round_2dp($salary->gross_salary); ?></td>
                        <td align="right"><? echo round_2dp($salary->advance_deduction); $net_advance_deduction += round_2dp($salary->advance_deduction); ?></td>
                        <td align="right"><? echo round_2dp($salary->total_salary); $net_payable += round_2dp($salary->total_salary); ?></td>
                        <td align="right"><? echo round_2dp($salary->paid_salary); $net_paid += round_2dp($salary->paid_salary); ?></td>
                        <td align="right"><? echo round_2dp($salary->total_salary - $salary->paid_salary); $net_balance += round_2dp($salary->total_salary - $salary->paid_salary); ?></td>
                    </tr>
                    <?
                }
                ?>
                <tr>
                    <td colspan="6" align="right"><b>Total : </b></td>
                    <td align="right"><b><? echo round_2dp($net_gross_salary); ?></b></td>
                    <td align="right"><b><? echo round_2dp($net_advance_deduction); ?></b></td>
                    <td align="right"><b><? echo round_2dp($net_payable); ?></b></td>
                    <td align="right"><b><? echo round_2dp($net_paid); ?></b></td>
                    <td align="right"><b><? echo round_2dp($net_balance); ?></b></td>
                </tr>
                <?
            }
            else
            {
                ?>
                <tr>
                    <td colspan="11" align="center">No records to display.</td>
                </tr>
                <?
            }
        ?>
    </table>
</div>