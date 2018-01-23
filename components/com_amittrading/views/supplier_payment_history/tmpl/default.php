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
        j("#supplier_id").chosen();
        j(".scrollIntoView").scrollIntoView({
            rowSelector : 'payment'
        });
    });
    
    j(document).on("change", "#from_date", function(){
        go("index.php?option=com_amittrading&view=supplier_payment_history&supplier_id=" + j("#supplier_id").val() + "&from_date=" + j("#from_date").val());
    });
    
    function show_payments(validate)
    {
        if(validate)
        {
            if(j("#supplier_id").val() == 0 && j("#from_date").val() == "")
            {
                alert("Select filters.");
                return false;
            }
            else
            {
                go("index.php?option=com_amittrading&view=supplier_payment_history&supplier_id=" + j("#supplier_id").val() + "&from_date=" + j("#from_date").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=supplier_payment_history");
        }
    }
    
    function view_payments(d)
    {
        go("index.php?option=com_amittrading&view=supplier_payment_history&supplier_id=" + j("#supplier_id").val() + "&from_date=" + j("#from_date").val() + "&d=" + d);
    }
</script>
<h1>Supplier Payment History</h1>
<br />
<table>
    <tr>
        <td>Supplier : </td>
        <td>
            <select id="supplier_id" name="supplier_id" style="width:250px;">
                <option value="0"></option>
                <?
                    if(count($this->suppliers) > 0)
                    {
                        foreach($this->suppliers as $supplier)
                        {
                            ?><option value="<? echo $supplier->id; ?>" <? echo ($this->supplier_id == $supplier->id ? "selected='selected'" : ""); ?> ><? echo $supplier->supplier_name; ?></option><?
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
<table width="80%">
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
        ?><a href="#" onclick="popup_print('<h1>Supplier Payment History</h1><br />' + j('#supplier_payment_history').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><br /><br /><?
    }
?>
<div id="supplier_payment_history">
    <table class="clean centreheadings floatheader scrollIntoView" width="80%">
        <tr>
            <th>#</th>
            <th>Receipt No.</th>
            <th>Supplier</th>
            <th>Payment Date</th>
            <th>Mode</th>
            <th>Cheque No.</th>
            <th>Cheque Date</th>
            <th>Bank Account</th>
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
                        <td><? echo $payment->supplier_name; ?></td>
                        <td align="center"><? echo date("d-M-Y", strtotime($payment->payment_date)); ?></td>
                        <td>
                            <?
                                if($payment->payment_mode == CASH) { echo "Cash"; }
                                else if($payment->payment_mode == CHEQUE) { echo "Cheque"; }
                            ?>
                        </td>
                        <?
                            if($payment->payment_mode == CHEQUE)
                            {
                                ?>
                                <td align="center"><? echo $payment->cheque_no; ?></td>
                                <td align="center"><? echo date("Y-m-d", strtotime($payment->cheque_date)); ?></td>
                                <td><? echo $payment->account_name . ", " . $payment->bank_name; ?></td>
                                <?
                            }
                            else
                            {
                                ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <?
                            }
                        ?>
                        <td align="right"><? echo round_2dp($payment->total_amount); ?></td>
                        <td><? echo $payment->remarks; ?></td>
                    </tr>
                    <?
                }
                ?>
                <tfoot>
                    <tr>
                        <td align="right" colspan="8"><b>Total : </b></td>
                        <td align="right"><b><? echo round_2dp($total_amount); ?></b></td>
                        <td></td>
                    </tr>
                </tfoot>
                <?
            }
            else
            {
                ?><td colspan="10" align="center">No records to display.</td><?
            }
        ?>
    </table>
</div>
<table width="80%">
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