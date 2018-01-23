<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        j("#from_date, #to_date").datepicker({
            "dateFormat" : "dd-M-yy",
             changeMonth: true,
             changeYear: true
        });
        
        j("#location_id, #employee_id, #month, #year").chosen();
    });
    
    j(document).on("change", "#from_date, #to_date", function(){
        j("#month").val(0).trigger("liszt:updated");
        j("#year").val(0).trigger("liszt:updated");
    });
    
    function get_salary_payments()
    {
        go("index.php?option=com_hr&view=salary_payment_report&employee_id=" + j("#employee_id").val() + "&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val() + "&month=" + j("#month").val() + "&year=" + j("#year").val() + "&location_id=" + j("#location_id").val());
    }
    
    function delete_salary_voucher(payment_id, salary_month, salary_year)
    {
        if(confirm("Are you sure you want to delete all the entries in this voucher?"))
        {
            go("index.php?option=com_hr&task=delete_salary_voucher&payment_id=" + payment_id + "&salary_month=" + salary_month + "&salary_year=" + salary_year);
        }
        else
        {
            return false;
        }
    }
</script>                  
<h1 id="report_heading">
    Salary Payment Report
    <?
        if($this->month != 0 && $this->year != 0)
        {
            echo " for " . date("F'Y", strtotime($this->year . '-' . $this->month . '-01'));
        }
    ?>
</h1>
<table>
    <tr>
        <td>Employee : </td>
        <td>
            <select id="employee_id" style="width:130px;">
                <option value="0" <? echo ($this->employee_id == 0 ? "selected='selected'" : ""); ?>>All</option>
                <?
                    if(count($this->employees) > 0)
                    {
                        foreach($this->employees as $employee)
                        {
                            ?>
                            <option value="<? echo $employee->id; ?>" <? echo ($this->employee_id == $employee->id ? "selected='selected'" : ""); ?> ><? echo $employee->employee_name; ?></option>
                            <?
                        }
                    }
                ?>
            </select>
        </td>
        <td>Location : </td>
        <td>
            <select id="location_id" style="width:130px;">
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
        <td>From D/M/Y : </td>
        <td><input type="text" id="from_date" value="<? echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : ""); ?>" style="width:80px;"></td>
        <td>To D/M/Y : </td>
        <td><input type="text" id="to_date" value="<? echo ($this->to_date != "" ? date("d-M-Y", strtotime($this->to_date)) : ""); ?>" style="width:80px;"></td>
        <td>Payment Month : </td>
        <td>
            <select id="month" style="width:110px;">
                <?
                    $months = array("0"=>"All","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
                    foreach($months as $key=>$month)
                    {
                        ?>
                        <option value="<? echo $key; ?>" <? echo ($key == $this->month ? "selected='selected'" : ""); ?> ><? echo $month; ?></option>
                        <?
                    }
                ?>
            </select>
        </td>
        <td>Payment Year : </td>
        <td>
            <select id="year" style="width:90px;">
                <option value="0">All</option>
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
    </tr>
</table><br />
<input type="button" value="Refresh" onclick="get_salary_payments();">
<input type="button" value="Clear" onclick="go('index.php?option=com_hr&view=salary_payment_report');">
<br /><br />
<?
    if(count($this->salary_payments) > 0)
    {
        ?><a href="#" onclick="popup_print('<h1>' + j('#report_heading').html() + '</h1><br />' + j('#salary_payment_report').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><br /><br /><?
    }
?>
<div id="salary_payment_report">
    <table class="clean floatheader centreheadings" width="80%">
        <thead>
            <tr>
                <th>#</th>
                <th>Payment No.</th>
                <th>Month</th>
                <th>Payment Date</th>
                <th>Emp. Code</th>
                <th>Employee Name</th>
                <th>Location</th>
                <th>Instrument</th>
                <th>Instrument No.</th>
                <th>Instrument Bank</th>
                <th>Amount</th>
                <th>Remarks</th>
                <th class="noprint">Action</th>
            </tr>
        </thead>
        <?
            if(count($this->salary_payments) > 0)
            {
                $x = 0;
                $total_payment_amount = 0;
                foreach($this->salary_payments as $payment)
                {
                    $total_payment_amount += floatval($payment->amount);
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td align="center"><? echo $payment->payment_id; ?></td>
                        <td align="center"><? echo date("F'Y", strtotime("01-" . $payment->salary_month . "-" . $payment->salary_year)); ?></td>
                        <td align="center"><? echo date("d-M-Y", strtotime($payment->payment_date)); ?></td>
                        <td align="center"><? echo $payment->employee_id; ?></td>
                        <td><? echo $payment->employee_name; ?></td>
                        <td><? echo $payment->location_name; ?></td>
                        <td align="center"><? echo ($payment->instrument == CASH ? "Cash" : "Cheque"); ?></td>
                        <td align="center"><? echo $payment->instrument_no; ?></td>
                        <td><? echo $payment->bank_name; ?></td>
                        <td align="right"><? echo $payment->amount; ?></td>
                        <td><? echo $payment->remarks; ?></td>
                        <td class="noprint" align="center">
                            <?
                                if(is_admin())
                                {
                                    ?>
                                    <a href="#" onclick="go('index.php?option=com_hr&view=salary_payment_voucher&m=e&payment_id=<? echo $payment->payment_id; ?>');"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                                    <a href="#" onclick="delete_salary_voucher(<? echo $payment->payment_id; ?>, <? echo $payment->salary_month; ?>, <? echo $payment->salary_year; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete"></a>
                                    <?
                                }
                            ?>
                        </td>
                    </tr>
                    <?
                }
                ?>
                    <tr>
                        <td align="right" colspan="10"><b>Total :</b></td>
                        <td align="right"><b><? echo round_2dp($total_payment_amount);?></b></td>
                        <td></td>
                        <td class="noprint"></td>
                    </tr>
                <?
            }
            else
            {
                ?>
                <tr>
                    <td colspan="13" align="center">No records to display.</td>
                </tr>
                <?
            }
        ?>
    </table>
</div>    