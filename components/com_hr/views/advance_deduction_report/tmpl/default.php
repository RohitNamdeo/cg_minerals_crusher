<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        j("#month, #year").chosen();
    });
    
    function get_advance_deductions()
    {
        if(j("#month").val() == 0 || j("#year").val() == 0)
        {
            alert("Please select both month and year.");
            return false;
        }
        go("index.php?option=com_hr&view=advance_deduction_report&month=" + j("#month").val() + "&year=" + j("#year").val());
    }
</script>
<h1 id="report_heading">
    Advance Deduction Report
    <?
        if($this->month != 0 && $this->year != 0)
        {
            echo " for " . date("F'Y", strtotime($this->year . '-' . $this->month . '-01'));
        } 
    ?>
</h1>
<table>
    <tr>
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
        <td>
            <input type="button" value="Refresh" onclick="get_advance_deductions();">
            <input type="button" value="Clear" onclick="go('index.php?option=com_hr&view=advance_deduction_report');">
        </td>
    </tr>
</table>
<br />
<div id="advance_deductions">
    <table class="clean floatheader centreheadings" width="60%">
       <!--<thead>-->
            <tr>
                <th>#</th>
                <th>Emp. Code</th>
                <th>Employee Name</th>
                <th>Salary Payable Before Advance Deduction</th>
                <th>Advance Deduction</th>
                <th>Total Salary</th> 
                <th>Action</th> 
            </tr>
        <!--</thead>-->
        <?
            if(count($this->advance_deductions) > 0)
            {
                $x = 1;
                $net_gross_salary = 0;
                $net_advance_deduction = 0;
                $net_payable = 0;
                foreach($this->advance_deductions as $advance)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo $x++; ?></td>
                        <td align="center"><? echo $advance->employee_id; ?></td>
                        <td><? echo $advance->employee_name; ?></td>
                        <td align="right"><? echo round_2dp($advance->gross_salary); $net_gross_salary += round_2dp($advance->gross_salary); ?></td>
                        <td align="right"><? echo round_2dp($advance->advance_deduction); $net_advance_deduction += round_2dp($advance->advance_deduction); ?></td>
                        <td align="right"><? echo round_2dp($advance->total_salary); $net_payable += round_2dp($advance->total_salary); ?></td>
                        <td align="center">
                            <?
                                if(is_admin() && $advance->paid_salary == 0)
                                {
                                    ?><a href="index.php?option=com_hr&view=edit_advance_deduction&salary_id=<? echo $advance->salary_id; ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit Advance Deduction"></a><?
                                }
                            ?>
                        </td>
                    </tr>
                    <?
                }
                ?>
                <tr>
                    <td colspan="3" align="right"><b>Total : </b></td>
                    <td align="right"><b><? echo round_2dp($net_gross_salary); ?></b></td>
                    <td align="right"><b><? echo round_2dp($net_advance_deduction); ?></b></td>
                    <td align="right"><b><? echo round_2dp($net_payable); ?></b></td>
                    <td></td>
                </tr>
                <?
            }
            else
            {
                ?>
                <tr>
                    <td colspan="7" align="center">No records to display.</td>
                </tr>
                <?
            }
        ?>
    </table>
</div>