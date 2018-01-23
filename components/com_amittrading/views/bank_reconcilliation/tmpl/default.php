<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){
        j("#clearance_date").datepicker({"dateFormat" : "dd-M-yy"});
        
        j("#clear_transaction").dialog({
            autoOpen: false,
            width: 280,
            height: 150,
            title: "Clear Cheque Transaction",
            buttons:
            {
                "Submit": function()
                {
                    if(j("#clearance_date").val() == "")
                    {
                        alert("Please select clearance date.");
                        return false;
                    }
                    
                    if(j("#multiple_clear").val() == 1)
                    {
                        j("#customer_cheque_clearance_date").val(j("#clearance_date").val());
                        j("#customer_payments").submit();
                    }
                    else
                    {
                        var transaction_id = j("#transaction_id").val();
                        
                        j(this).dialog("close");
                        j.get("index.php?option=com_amittrading&task=clear_cheque&tmpl=xml", j("#clearTransactionForm").serialize(), function(data){
                            if(data == "ok")
                            {
                               j("#action_" + transaction_id).html("Cleared");
                               j("#checkbox_" + transaction_id).html("");
                               j("#transaction_id, #item_type").val("");
                            }
                            else
                            {
                                alert("Some error occurred. Please try again.");
                            }
                        });
                    }
                },
                "Close": function()
                {
                    j(this).dialog("close");
                } 
            }
        });
    });
    
    j(document).on("change", "#check_all", function(){
        if(j(this).is(":checked"))
        {
            j(".item_id").attr("checked", true);
        }
        else
        {
            j(".item_id").attr("checked", false);
        }
    });
    
    j(document).on("change", ".item_id", function(){
        if(j(".item_id:checked").length == j(".item_id").length)
        {
            j("#check_all").attr("checked", true);
        }
        else
        {
            j("#check_all").attr("checked", false);
        }
    });
    
    function clear_cheque(transaction_id, item_type)
    {
        j("#transaction_id").val(transaction_id);
        j("#item_type").val(item_type);
        j("#multiple_clear").val(0);
        
        j("#clearance_date").val("");
        j("#clear_transaction").dialog("open");
    }
    
    function clear_customer_cheque()
    {
        if(j(".item_id:checked").length == 0)
        {
            alert("Please select cheques.");
            return false;
        }
        else
        {
            j("#clearance_date").val("");
            j("#multiple_clear").val(1);
            j("#clear_transaction").dialog("open");
        }
    }
    
    function delete_customer_cheque_payment(transaction_id, item_type)
    {
        if(confirm("Are you sure?"))
        {
            j.get("index.php?option=com_amittrading&task=delete_customer_cheque_payment&tmpl=xml&transaction_id=" + transaction_id + "&item_type=" + item_type, function(data){
                if(data == "ok")
                {
                   j("#action_" + transaction_id).html("Deleted");
                }
                else
                {
                    alert("Some error occurred. Please try again.");
                }
            });
        }
        else
        {
            return false;
        }
    }
    
</script>
<h1>Bank Reconcilliations</h1>
<h2>Customer Payments</h2>
<?
    if(count($this->customer_payments) > 0)
    {
        ?><input type="button" value="Clear Cheque" onclick="clear_customer_cheque();"><br /><br /><?
    }
?>
<form id="customer_payments" method="post" action="index.php?option=com_amittrading&task=clear_customer_cheque">
    <input type="hidden" id="customer_cheque_clearance_date" name="customer_cheque_clearance_date">
    <table class="clean centreheadings">
        <tr>
            <th>#</th>
            <th width="20"><input type="checkbox" id="check_all"></th>
            <th>Bank Account</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Cheque No.</th>
            <th>Cheque Date</th>
            <th>Bank</th>
            <th>Action</th>
        </tr>
        <?
            if(count($this->customer_payments) > 0)
            {
                $x = 1;
                foreach($this->customer_payments as $payment)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo $x++; ?></td>
                        <td align="center" id="checkbox_<? echo $payment->id; ?>"><input type="checkbox" name="item_ids[]" class="item_id" value="<? echo $payment->id; ?>"></td>
                        <td><? echo $payment->bank_account; ?></td>
                        <td><? echo $payment->party_name; ?></td>
                        <td><? echo date("d-M-Y", strtotime($payment->transaction_date)); ?></td>
                        <td align="right"><? echo round_2dp($payment->amount); ?></td>
                        <td><? echo $payment->cheque_no; ?></td>
                        <td><? echo date("d-M-Y", strtotime($payment->cheque_date)); ?></td>
                        <td><? echo $payment->bank_name; ?></td>
                        <td id="action_<? echo $payment->id; ?>">
                            <input type="button" value="Clear" onclick="clear_cheque(<? echo $payment->id; ?>, <? echo CUSTOMER_PAYMENT; ?>); return false;">
                            <input type="button" value="Delete" onclick="delete_customer_cheque_payment(<? echo $payment->id; ?>, <? echo CUSTOMER_PAYMENT; ?>); return false;">
                        </td>
                    </tr>
                    <?
                }
            }
            else
            {
                ?>
                <tr>
                    <td colspan="10" align="center">No records to display.</td>
                </tr>
                <?
            }
        ?>
    </table>
</form>
<!--<br />
<h2>Supplier Payments</h2>
<table class="clean centreheadings">
    <tr>
        <th>#</th>
        <th>Bank Account</th>
        <th>Supplier</th>
        <th>Date</th>
        <th>Amount</th>
        <th>Cheque No.</th>
        <th>Cheque Date</th>
        <th>Action</th>
    </tr>
    <?
        /*if(count($this->supplier_payments) > 0)
        {
            $x = 1;
            foreach($this->supplier_payments as $payment)
            {
                ?>
                <tr>
                    <td align="center"><? echo $x++; ?></td>
                    <td><? echo $payment->bank_account; ?></td>
                    <td><? echo $payment->party_name; ?></td>
                    <td><? echo date("d-M-Y", strtotime($payment->transaction_date)); ?></td>
                    <td align="right"><? echo round_2dp($payment->amount); ?></td>
                    <td><? echo $payment->cheque_no; ?></td>
                    <td><? echo date("d-M-Y", strtotime($payment->cheque_date)); ?></td>
                    <td id="action_<? echo $payment->id; ?>"><input type="button" value="Clear" onclick="clear_cheque(<? echo $payment->id; ?>, <? echo SUPPLIER_PAYMENT; ?>); return false;"></td>
                </tr>
                <?
            }
        }
        else
        {
            ?>
            <tr>
                <td colspan="8" align="center">No records to display.</td>
            </tr>
            <?
        }*/
    ?>
</table>-->

<input type="hidden" id="multiple_clear">
<div id="clear_transaction" style="display:none;">
    <form id="clearTransactionForm">
        <input type="hidden" id="transaction_id" name="transaction_id">
        <input type="hidden" id="item_type" name="item_type">
        <table>
              <tr>
                <td>Clearance Date</td>
                <td><input type="text" id="clearance_date" name="clearance_date" readonly="readonly" style="width:150px;"></td>
              </tr>
        </table>
    </form>
</div>