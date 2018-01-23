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
        j("#from_date, #to_date").datepicker({"dateFormat" : "dd-M-yy"});
    });
    
    function show_expenses(validate)
    {
        if(validate)
        {
            if(j("#from_date").val() == "" && j("#to_date").val() == "")
            {
                alert("Select filters.");
                return false;
            }
            else
            {
                go("index.php?option=com_amittrading&view=cash_expense_history&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=cash_expense_history");
        }
    }
</script>
<h1>Cash Expense History</h1>
<br />
<table>
    <tr>
        <td>From Date : </td>
        <td><input type="text" id="from_date" value="<? echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : "") ?>" style="width:80px;"></td>
        <td>To Date : </td>
        <td><input type="text" id="to_date" value="<? echo ($this->to_date != "" ? date("d-M-Y", strtotime($this->to_date)) : "") ?>" style="width:80px;"></td>
        <td>
            <input type="button" value="Refresh" onclick="show_expenses(1); return false;">
            <input type="button" value="Clear" onclick="show_expenses(0);">
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
    if(count($this->expenses) > 0)
    {
        ?><a href="#" onclick="popup_print('<h1>Cash Expense History</h1><br />' + j('#expense_history').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><br /><br /><?
    }
?>
<div id="expense_history">
    <table class="clean centreheadings floatheader" width="50%">
        <tr>
            <th>#</th>
            <th>Expense Date</th>
            <th>Expense Head</th>
            <th>Amount</th>
            <th>Type</th>
            <th>Description</th>
        </tr>
        <?
            if(count($this->expenses) > 0)
            {
                $x = $this->limitstart;
                $total_amount = 0;
                foreach($this->expenses as $expense)
                {
                    $total_amount += round_2dp($expense->amount);
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td align="center"><? echo date("d-M-Y", strtotime($expense->expense_date)); ?></td>
                        <td><? echo $expense->expense_head; ?></td>
                        <td align="right"><? echo round_2dp($expense->amount); ?></td>
                        <td><? echo ($expense->item_type == TRANSPORTER_PAYMENT ? "Transporter<br />Payment" : ""); ?></td>
                        <td><? echo $expense->description; ?></td>
                    </tr>
                    <?
                }
                ?>
                <tfoot>
                    <tr>
                        <td align="right" colspan="3"><b>Total : </b></td>
                        <td align="right"><b><? echo round_2dp($total_amount); ?></b></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
                <?
            }
            else
            {
                ?><td colspan="5" align="center">No records to display.</td><?
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