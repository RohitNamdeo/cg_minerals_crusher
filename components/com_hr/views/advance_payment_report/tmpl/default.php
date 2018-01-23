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
        
        j("#location_id, #employee_id").chosen();
    });
    
    function get_advance_payments()
    {
        go("index.php?option=com_hr&view=advance_payment_report&employee_id=" + j("#employee_id").val() + "&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val() + "&location_id=" + j("#location_id").val());
    }
    
    function delete_advance_voucher(advance_id)
    {
        if(confirm("Are you sure?"))
        {
            go("index.php?option=com_hr&task=delete_advance_voucher&advance_id=" + advance_id);
        }
        else
        {
            return false;
        }
    }
</script>                  
<h1 id="report_heading">Advance Payment Report</h1>
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
    </tr>
</table><br />
<input type="button" value="Refresh" onclick="get_advance_payments();">
<input type="button" value="Clear" onclick="go('index.php?option=com_hr&view=advance_payment_report');">
<br /><br />
<?
    if(count($this->advance_payments) > 0)
    {
        ?><a href="#" onclick="popup_print('<h1>' + j('#report_heading').html() + '</h1><br />' + j('#advance_payment_report').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><br /><br /><?
    }
?>
<div id="advance_payment_report">
    <table class="clean floatheader centreheadings" width="80%">
        <thead>
            <tr>
                <th>#</th>
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
            if(count($this->advance_payments) > 0)
            {
                $x = 0;
                $total_payment_amount = 0;
                foreach($this->advance_payments as $payment)
                {
                    $total_payment_amount += floatval($payment->amount);
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
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
                                if(is_admin() && $payment->amount_cleared == 0)
                                {
                                    ?>
                                    <a href="#" onclick="go('index.php?option=com_hr&view=advance_salary_payment_voucher&m=e&advance_id=<? echo $payment->id; ?>');"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                                    <a href="#" onclick="delete_advance_voucher(<? echo $payment->id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete"></a>
                                    <?
                                }
                            ?>
                        </td>
                    </tr>
                    <?
                }
                ?>
                    <tr>
                        <td align="right" colspan="8"><b>Total :</b></td>
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
                    <td colspan="11" align="center">No records to display.</td>
                </tr>
                <?
            }
        ?>
    </table>
</div>    