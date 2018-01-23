<?php
    defined('_JEXEC') or die; 
?>
<style>
#fundTransferForm>table>tr>td>td
{
    width:300px;
}
</style>

<script>
    j(function(){
        j("#transaction_date,#transfer_date, .date_field").datepicker({"dateFormat" : "dd-M-yy"});
        
        j("#cash_transaction").dialog({
            autoOpen: false,
            width: 400,
            height: 200,
            buttons:
            {
                "Submit": function()
                {
                    if(j("#transaction_date").val() == "")
                    {
                        alert("Please enter date.");
                        return false;
                    }
                                    
                    if(j("#amount").val() == "" || j("#amount").val() == 0)
                    {
                        alert("Please enter amount.");
                        return false;
                    }
                    j("#cashTransactionForm").submit();
                },
                "Close": function()
                {
                    j(this).dialog("close");
                } 
            }
        });
    });
    
    j(function(){
        //j("#transaction_date, .date_field").datepicker({"dateFormat" : "dd-M-yy"});
        
        j("#fund_transfer").dialog({
            autoOpen: false,
            width: 500,
            //height: 200,
            buttons:
            {
                "Submit": function()
                {
                    var account_balance = parseFloat(j("#account_balance").text());
                        account_balance = (isNaN(account_balance) ? 0 : account_balance);
                    if(j("#transfer_date").val() == "")
                    {
                        alert("Please enter date.");
                        return false;
                    }
                                    
                    if(j("#transfer_amount").val() == "" || j("#transfer_amount").val() == 0)
                    {
                        alert("Please enter amount.");
                        return false;
                    }
                    
                    if(j("#transfer_amount").val() > account_balance)
                    {
                        alert("Can not transfer amount more than account balance.");
                        return false;
                    }
                    
                   /* if(j("#transfer_to_bank_account_id").val() == "" || j("#transfer_to_bank_account_id").val() == 0)
                    {
                         alert("Please select bank account.");
                        return false;    
                    } */
                    
                    j("#fundTransferForm").submit();
                },
                "Close": function()
                {
                    j(this).dialog("close");
                } 
            }
        });
    });
    
    j(document).on("keypress","#amount",function(e){
        prevent_char(e.which,e);
    });
    
    function cash_transaction(cash_transaction_type)
    {
        //alert(cash_transaction_type); exit;
        j("#cashTransactionForm").attr("action", "index.php?option=com_amittrading&task=save_cash_transaction");
        j("#cash_transaction_type").val(cash_transaction_type); 
        j("#cash_transaction_id, #transaction_date, #amount, #description").val(""); 
        j("#cash_transaction").dialog("open");
        
        j("#transaction_date").val(j("#current_date").val());
        j("#transaction_date").datepicker("hide");
        j("#amount").focus();
        
        if(cash_transaction_type == <? echo CASH_WITHDRAW; ?>) { title = "Cash Withdraw"; }
        else if(cash_transaction_type == <? echo CASH_DEPOSIT; ?>) { title = "Cash Deposit"; }
        else if(cash_transaction_type == <? echo BANK_CHARGES; ?>) { title = "Bank Charges"; }
        j("#cash_transaction").dialog({"title":title});
        j('#cash_transaction').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function edit_cash_transaction(cash_transaction_id, cash_transaction_type)
    {
        
        j("#cashTransactionForm").attr("action", "index.php?option=com_amittrading&task=edit_cash_transaction");
        j("#cash_transaction_id").val(cash_transaction_id); 
        j("#cash_transaction_type").val(cash_transaction_type);
        j("#transaction_date, #amount, #description").val(""); 
        j("#cash_transaction").dialog("open");
        
        j("#transaction_date").datepicker("hide");
        j("#amount").focus();
        
        j.get("index.php?option=com_amittrading&task=cash_transaction_details&tmpl=xml&cash_transaction_id=" + cash_transaction_id, function(data){
            if(data != "")
            {
                var cash_transaction = j.parseJSON(data);
                
                j("#transaction_date").val(cash_transaction.transaction_date);
                j("#amount").val(cash_transaction.amount);
                j("#description").val(cash_transaction.description);
            }
        });
        
        if(cash_transaction_type == <? echo CASH_WITHDRAW; ?>) { title = "Cash Withdraw"; }
        else if(cash_transaction_type == <? echo CASH_DEPOSIT; ?>) { title = "Cash Deposit"; }
        else if(cash_transaction_type == <? echo BANK_CHARGES; ?>) { title = "Bank Charges"; }
        j("#cash_transaction").dialog({"title":title});
        j('#cash_transaction').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function delete_cash_transaction(cash_transaction_id, cash_transaction_type)
    {
        if(confirm("Are you sure?"))
        {
            go("index.php?option=com_amittrading&task=delete_cash_transaction&tmpl=xml&cash_transaction_id=" + cash_transaction_id + "&cash_transaction_type=" + cash_transaction_type + "&bank_account_id=<? echo $this->bank_account_id; ?>");
        }
        else
        {
            return false;
        }
    }
    
    function fund_transfer(fund_transfer_type)
    {   
        j("#fundTransferForm").attr("action", "index.php?option=com_amittrading&task=save_cash_transaction");
        j("#fund_transfer_type").val(fund_transfer_type); 
        j("#transfer_date,#transfer_from_bank_id,#amount,#transfer_to_bank_id,#description").val(""); 
        j("#fund_transfer").dialog("open");
        
        j("#transfer_date").val(j("#current_date").val());
        j("#transfer_date").datepicker("hide");
        j("#amount").focus();
        
        j("#fund_transfer").dialog({"title":"Fund Transfer"});
        j('#fund_transfer').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
        
    }
    
    function edit_fund_transfer(fund_transfer_id, fund_transfer_type)
    {
        j("#fundTransferForm").attr("action", "index.php?option=com_amittrading&task=edit_fund_transfer");
        j("#fund_transfer_id").val(fund_transfer_id); 
        j("#fund_transfer_type").val(fund_transfer_type);
        j("#transfer_date,#transfer_from_bank_id,#amount,#transfer_to_bank_id,#description").val(""); 
        j("#fund_transfer").dialog("open");
        
        //j.get("index.php?option=com_amittrading&task=cash_transaction_details&tmpl=xml&fund_transfer_id=" + fund_transfer_id + "&fund_transfer_type=" + fund_transfer_type, function(data){
        j.get("index.php?option=com_amittrading&task=cash_transaction_details&tmpl=xml&fund_transfer_id=" + fund_transfer_id, function(data){
            if(data != "")
            {
                var fund_transfer = j.parseJSON(data);
                
                j("#transfer_date").val(fund_transfer.transfer_date);
                j("#transfer_amount").val(fund_transfer.amount_transferred);
                j("#tranfer_from_bank").text(fund_transfer.fund_transfer_from_account_name);
                j("#transfer_to_bank_account_id").val(fund_transfer.fund_transfer_to_account_id);
                j("#description1").val(fund_transfer.description);
            }
        });
        
        j("#fund_transfer").dialog({"title":"Edit Fund Transfer"});
        j('#fund_transfer').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
            
    }
    function delete_fund_transfer(fund_transfer_id, fund_transfer_type)
    {
        if(confirm("Are you sure?"))
        {
            go("index.php?option=com_amittrading&task=delete_fund_transfer&tmpl=xml&fund_transfer_id=" + fund_transfer_id + "&fund_transfer_type=" + fund_transfer_type + "&bank_account_id=<? echo $this->bank_account_id; ?>");
        }
        else
        {
            return false;
        }
    } 
    
    function get_records()
    {
        if(j("#from_date").val() == "" || j("#to_date").val() == "")
        {
            alert("Select from and to date."); return false;
        }
        go("index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $this->bank_account_id; ?>&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val());
    }
    
    function export_data()
    {
        j(".noprint").remove();
        tableToExcel('bank_account_statement', 'Export.xls');
    }    
</script>
<h1><? echo $this->bank_account->account_name . ", " . $this->bank_account->bank_name; ?> Account Statement</h1>
<input type="hidden" id="current_date" value="<? echo date("d-M-Y"); ?>">
<?
    if($this->bank_account->account_status == AC_ACTIVE)
    {
        ?>
            <input type="button" value="Cash Withdraw" onclick="cash_transaction(<? echo CASH_WITHDRAW; ?>);">
            <input type="button" value="Cash Deposit" onclick="cash_transaction(<? echo CASH_DEPOSIT; ?>);">
            <input type="button" value="Bank Charges" onclick="cash_transaction(<? echo BANK_CHARGES; ?>);">
            <input type="button" value="Fund Transfer" onclick="fund_transfer(<? echo FUND_TRANSFER; ?>);">
            <br /><br />
        <?
    }
?>
<table>
    <tr>
        <td>From :</td>
        <td><input type="text" class="date_field" id="from_date" value="<? echo date("d-M-Y", strtotime($this->from_date)); ?>" /></td>
        <td>To :</td>
        <td><input type="text" class="date_field" id="to_date" value="<? echo date("d-M-Y", strtotime($this->to_date)); ?>" /></td>
        <td>
            <input type="button" value="Refresh" onclick="get_records(); return false">
            <input type="button" value="Clear" onclick="go('index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $this->bank_account_id; ?>');">
        </td>
    </tr>
</table>
<br />
<?
    if(count($this->account_details) > 0)
    {
        ?>
        <a href="#" onclick="export_data(); return false;" class="export"><img src="custom/graphics/icons/blank.gif" class="spreadsheet" title="excel"></a>
        <a href="#" onclick="popup_print('<h1><? echo $this->bank_account->account_name . ", " . $this->bank_account->bank_name; ?> Account Statement (<? echo date("d-M-Y", strtotime($this->from_date)); ?> to <? echo date("d-M-Y", strtotime($this->to_date)); ?>)</h1><br />' + j('#bank_account_statement').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print" title="print"></a>
        <br /><br />
        <?
    }
?>
<div id="bank_account_statement"> 
    <?
        if(count($this->account_details) > 0)
        {
            ?>
            <div style="float:right;"><b>Opening : <? echo abs($this->opening_balance) . ($this->opening_balance == 0 ? "" : ($this->opening_balance > 0 ? " Dr." : " Cr." ) ); ?></b></div>
            <br /><br />
            <table class="clean spread centreheadings" id="account_details">
                <tr>
                    <th>#</th>
                    <th class="noprint"></th>
                    <th>Date</th>
                    <th>Particulars</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <!--<th>Credit</th>
                    <th>Debit</th>-->
                    <th>Balance</th>
                </tr>
                <?
                    $x = 0;
                    $balance = 0;
                    ($this->opening_balance > 0 ? $balance += abs($this->opening_balance) : $balance -= abs($this->opening_balance));
                    
                    foreach($this->account_details as $details)
                    {
                        ?>
                        <tr>
                            <td align="center"><? echo ++$x; ?></td>
                            <td align="center" class="noprint">
                                <?
                                    if($details->transaction_type == CASH_DEPOSIT || $details->transaction_type == CASH_WITHDRAW || $details->transaction_type == BANK_CHARGES )
                                    {
                                        ?>
                                            <a href="#" onclick="edit_cash_transaction(<? echo $details->id; ?>, <? echo $details->transaction_type; ?>); return false;"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                            <a href="#" onclick="delete_cash_transaction(<? echo $details->id; ?>, <? echo $details->transaction_type; ?>); return false;"><img src="custom/graphics/icons/blank.gif" title="Delete" class="delete"></a>
                                        <?
                                    }
                                    if($details->transaction_type == FUND_TRANSFER && $details->item_type == TRANSFER_FROM_BANK_ACCOUNT )
                                    {
                                        ?>
                                            <a href="#" onclick="edit_fund_transfer(<? echo $details->id; ?>, <? echo $details->transaction_type; ?>); return false;"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                            <a href="#" onclick="delete_fund_transfer(<? echo $details->id; ?>, <? echo $details->transaction_type; ?>); return false;"><img src="custom/graphics/icons/blank.gif" title="Delete" class="delete"></a>
                                        <?
                                    }
                                ?>
                            </td>
                            
                            <td align="center"><? echo date("d-M-Y", strtotime($details->transaction_date)); ?></td>
                            <td>
                                <?
                                  
                                    if($details->transaction_type == CASH_DEPOSIT)
                                    {
                                        //echo "Cash Deposit (" . $details->description . ")";
                                        echo  $details->description ;
                                    }
                                    else if($details->transaction_type == CASH_WITHDRAW)
                                    {
                                        //echo "Cash Withdrawal (" . $details->description . ")";
                                        echo $details->description ;            
                                    }
                                    else if($details->transaction_type == BANK_CHARGES)
                                    {
                                        //echo "Bank Charges (" . $details->description . ")";            
                                        echo $details->description ;            
                                    }
                                    else if($details->transaction_type == FUND_TRANSFER && $details->item_type == TRANSFER_FROM_BANK_ACCOUNT)
                                    {
                                        //echo $details->description . " " . "From" . " " ."(" . $details->bank_name . ")" ;            
                                        echo $details->description ;            
                                    }
                                    
                                    else if($details->transaction_type == FUND_TRANSFER && $details->item_type == TRANSFER_TO_BANK_ACCOUNT )
                                    {
                                        //echo $details->description . " " . "To" . " " ."(" . $details->bank_name . ")" ;            
                                        echo $details->description;            
                                    }
                                    
                                    else if($details->item_type == CUSTOMER_PAYMENT)
                                    {
                                        echo "Customer - " . $details->party_name . " payment (Cheque No : " . $details->cheque_no . ", Cheque Date : " . date("d-M-Y", strtotime($details->cheque_date)) . ",  Bank : " . $details->bank_name . ")";
                                    }
                                    else if($details->item_type == SUPPLIER_PAYMENT)
                                    {
                                        echo "Supplier - " . $details->party_name . " payment (Cheque No : " . $details->cheque_no . ", Cheque Date : " . date("d-M-Y", strtotime($details->cheque_date)) . ")";
                                    }
                                ?>
                            </td>
                            <?
                                
                                if($details->transaction_type == CASH_DEPOSIT || $details->item_type == CUSTOMER_PAYMENT)
                                {
                                    $balance += floatval($details->amount);
                                    ?>
                                    <td></td>
                                    <td align="right"><? echo round_2dp($details->amount); ?></td>
                                     
                                    <?
                                }
                                else if($details->transaction_type == CASH_WITHDRAW || $details->item_type == SUPPLIER_PAYMENT || $details->transaction_type == BANK_CHARGES)
                                {
                                    $balance -= floatval($details->amount);
                                    ?>
                                    
                                    <td align="right"><? echo round_2dp($details->amount); ?></td>
                                    <td></td>
                                    <?
                                }
                                else if($details->transaction_type == FUND_TRANSFER && $details->item_type == TRANSFER_FROM_BANK_ACCOUNT)
                                {
                                    $balance -= floatval($details->amount);
                                    ?>
                                    
                                    <td align="right" ><? echo round_2dp($details->amount);?></td>
                                    <td ></td>
                                    <?    
                                }
                                else if( $details->transaction_type == FUND_TRANSFER && $details->item_type == TRANSFER_TO_BANK_ACCOUNT )
                                {
                                    $balance += floatval($details->amount);
                                    ?>
                                    <td></td> 
                                    <td align="right"><? echo round_2dp($details->amount); ?></td>
                                    
                                    <?    
                                }
                                
                            ?>
                            <td align="right"><? echo round_2dp(abs($balance)) . ($balance == 0 ? "" : ($balance > 0 ? " Dr." : " Cr." ) ); ?></td>
                        </tr>
                        <?
                    }
                ?>
            </table>
            <br />
            <div style="float:right;"><b>Closing : <? echo round_2dp(abs($balance)) . ($balance == 0 ? "" : ($balance > 0 ? " Dr." : " Cr." ) ); ?></b></div>
            <?
        }
        else
        {
            echo "Account details not found!";
        }
    ?>
</div>

<div id="cash_transaction" style="display:none;">
    <form id="cashTransactionForm" method="post" action="index.php?option=com_amittrading&task=save_cash_transaction">
        <input type="hidden" id="cash_transaction_type" name="cash_transaction_type">
        <input type="hidden" id="cash_transaction_id" name="cash_transaction_id">
        <input type="hidden" id="bank_account_id" name="bank_account_id" value="<? echo $this->bank_account_id; ?>">
        <table>
              <tr>
                <td>Date</td>
                <td><input type="text" name="transaction_date" id="transaction_date" style="width:270px;"></td>
              </tr>
              <tr>
                <td>Amount</td>
                <td><input type="text" name="amount" id="amount" style="width:270px;"></td>
              </tr>
              <tr>
                <td>Description</td>
                <td><input type="text" name="description" id="description" style="width:270px;"></td>
              </tr>
        </table>
    </form>
</div>

<div id="fund_transfer" style="display:none;">
    <form id="fundTransferForm" action="index.php?option=com_amittrading&task=save_cash_transaction" method="post">
        <input type="hidden" id="fund_transfer_type" name="fund_transfer_type">
        <input type="hidden" id="fund_transfer_id" name="fund_transfer_id">
        <input type="hidden" id="bank_account_id" name="bank_account_id" value="<? echo $this->bank_account_id; ?>">
        <table>
            <tr>
                <td>Transfer Date : </td>
                <td><input type="text" name="transaction_date" id="transfer_date" style="width:250px;"/></td>
            </tr>
            <tr>
                <td>Transfer From Bank Account : </td>
                <td id="transfer_from_bank">
                    <!--<input type="text" name="transfer_from_bank_id" id="transfer_from_bank_id" value="<? //echo @$this->bank_accounts[$this->bank_account_id]->account_name . " " . "(". @$this->bank_accounts[$this->bank_account_id]->bank_name . ")"; ?>"/> -->
                    <? echo @$this->bank_accounts[$this->bank_account_id]->account_name . " " . "(". @$this->bank_accounts[$this->bank_account_id]->bank_name  .")"; ?>
                </td>
            </tr>
            <tr>
                <td>Account Balance : </td>
                <td id="account_balance">
                    <? echo (@$this->bank_accounts[$this->bank_account_id]->balance > 0 ? @$this->bank_accounts[$this->bank_account_id]->balance."/-" : "0.00/-"); ?>
                </td>
            </tr>
            <tr>
                <td>Amount : </td>
                <td><input type="text" name="amount" id="transfer_amount"  style="width:250px;"/></td>
            </tr>
            <tr>
                <td>Transfer To Bank Account : </td>
                <td>
                    <select name="transfer_to_bank_account_id" id="transfer_to_bank_account_id" style="width:250px;" >
                        <option value="0"></option>
                        <?
                            if(count($this->bank_accounts) > 0)
                            {
                                foreach($this->bank_accounts as $bank)
                                {
                                    if($bank->id != $this->bank_account_id)
                                    {
                                        ?>
                                            <option value="<? echo $bank->id; ?>"> <? echo $bank->account_name ." " . "(". $bank->bank_name .")";?></option>
                                        <?
                                    }
                                }
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Description : </td>
                <td><input type="text" name="description" id="description1" style="width:250px;"/></td>
            </tr>
        </table>
    </form>
</div>