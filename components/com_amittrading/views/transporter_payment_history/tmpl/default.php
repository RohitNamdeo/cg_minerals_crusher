<?php
    defined('_JEXEC') or die; 
?>
<style>
    .pagination span, a {
        padding: 3px;
    }
</style>
<script>
    j(function(){
        j("#from_date").datepicker({"dateFormat" : "dd-M-yy"});
        j("#transporter_id").chosen();
        j(".scrollIntoView").scrollIntoView({
            rowSelector : 'payment'
        })
    });
    
    j(document).on("change", "#from_date", function(){
        go("index.php?option=com_amittrading&view=transporter_payment_history&transporter_id=" + j("#transporter_id").val() + "&from_date=" + j("#from_date").val());
    });
    
    function show_payments(validate)
    {
        if(validate)
        {
            if(j("#transporter_id").val() == 0 && j("#from_date").val() == "")
            {
                alert("Select filters.");
                return false;
            }
            else
            {
                go("index.php?option=com_amittrading&view=transporter_payment_history&transporter_id=" + j("#transporter_id").val() + "&from_date=" + j("#from_date").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=transporter_payment_history");
        }
    }
    
    function view_payments(d)
    {
        go("index.php?option=com_amittrading&view=transporter_payment_history&transporter_id=" + j("#transporter_id").val() + "&from_date=" + j("#from_date").val() + "&d=" + d);
    }
</script>
<h1>Transporter Payment History</h1>
<br />
<table>
    <tr>
        <td>Transporter : </td>
        <td>
            <select id="transporter_id" name="transporter_id" style="width:250px;">
                <option value="0"></option>
                <?
                    if(count($this->transporters) > 0)
                    {
                        foreach($this->transporters as $transporter)
                        {
                            ?><option value="<? echo $transporter->id; ?>" <? echo ($this->transporter_id == $transporter->id ? "selected='selected'" : ""); ?> ><? echo $transporter->transporter; ?></option><?
                        }
                    }
                ?>
            </select>
        </td>
        <!--<td>From Date : </td>-->
        <td>
            <button onclick="view_payments('p');"><b>&lt; Previous</b></button>
            <input type="text" id="from_date" value="<? echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : "") ?>" readonly="readonly" style="width:80px;">
            <button onclick="view_payments('n');"><b>&gt; Next</b></button>
        </td>
        <!--<td>To Date : </td>
        <td><input type="text" id="to_date" value="<? //echo ($this->to_date != "" ? date("d-M-Y", strtotime($this->to_date)) : "") ?>" style="width:80px;"></td>-->
        <td>
            <input type="button" value="Refresh" onclick="show_payments(1); return false;">
            <input type="button" value="Clear" onclick="show_payments(0);">
        </td>
    </tr>
</table>
<table width="50%">
    <tr align="center">
        <td>
            <?         
                if($this->total > 100)
                {
                    echo "<br />";
                    echo $this->pagination->getPagesLinks();
                    echo "<br /><br />";
                }
                else
                {
                    echo "<br />";
                }
            ?>
        </td>
    </tr>
</table>
<?
    if(count($this->payments) > 0)
    {
        ?><a href="#" onclick="popup_print('<h1>Transporter Payment History</h1><br />' + j('#transporter_payment_history').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><br /><br /><?
    }
?>
<div id="transporter_payment_history">
    <table class="clean centreheadings floatheader scrollIntoView" width="50%">
        <tr>
            <th>#</th>
            <th>Receipt No.</th>
            <th>Transporter</th>
            <th>Payment Date</th>
            <th>Amount</th>
            <th>Remarks</th>
        </tr>
        <?
            if(count($this->payments) > 0)
            {
                $x = $this->limitstart;
                $total_amount = 0;
                foreach($this->payments as $payment)
                {
                    $total_amount += round_2dp($payment->total_amount);
                    ?>
                    <tr class="payment">
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $payment->payment_id; ?></td>
                        <td><? echo $payment->transporter; ?></td>
                        <td align="center"><? echo date("d-M-Y", strtotime($payment->payment_date)); ?></td>
                        <td align="right"><? echo round_2dp($payment->total_amount); ?></td>
                        <td><? echo $payment->remarks; ?></td>
                    </tr>
                    <?
                }
                ?>
                <tfoot>
                    <tr>
                        <td align="right" colspan="4"><b>Total : </b></td>
                        <td align="right"><b><? echo round_2dp($total_amount); ?></b></td>
                        <td></td>
                    </tr>
                </tfoot>
                <?
            }
            else
            {
                ?><td colspan="6" align="center">No records to display.</td><?
            }
        ?>
    </table>
</div>
<table width="50%">
    <tr align="center">
        <td>
            <?
                if($this->total > 100)
                {
                    echo "<br />";
                    echo $this->pagination->getPagesLinks();
                }
            ?>
        </td>
    </tr>
</table>