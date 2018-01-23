<?php
    defined('_JEXEC') or die( 'Restricted access' );
?>
<script>
    j(function(){
        <?
            if($this->allow_backdate_payment)
            {
                ?>j("#payment_date").datepicker({"dateFormat" : "dd-M-yy"});<?
            }
        ?>
        j("#cheque_date").datepicker({"dateFormat" : "dd-M-yy"});
        j("#supplier_id, #bank_account_id").chosen();
        j(".cheque_details").hide();
        j("#bank_account_details").hide();
        j("input[name='payment_mode'][value='<? echo CASH; ?>']").attr("checked", true);
        j("#supplier_id").trigger("liszt:activate");
        
        <?
            if($this->supplier_id > 0)
            {
                ?>show_supplier_details();<?
            }
        ?>
    });
    
    /*j(document).on("keydown", function(e){
        if (e.altKey && e.which == 83)
        {
            e.preventDefault();
            validateForm();
        }
    });*/
    
    j(document).on("keypress","#amount",function(e){
        prevent_char(e.which,e);
    });
    
    j(document).on("change",".payment_mode",function(e){
        j(".cheque_details").toggle();
        j("#bank_account_details").toggle();
        if(j(this).val() == <? echo CASH; ?>)
        {
            j("#bank_account_balance").html("");
            j("#cheque_no").val("");
            j("#cheque_date").val("");
            j("#bank_account_id").val(0).trigger("liszt:updated");
        }
    });
    
    j(document).on("change", "#bank_account_id", function(e){
        j("#bank_account_balance").html(j(this).find("option:selected").attr("account_balance") + "/-");
    });
    
    j(document).on("keyup","#amount",function(e){
        amount_received = j(this).val();
        amount_due = j("#amount_due").val();
        if(amount_received == "") { amount_received = 0; }
        if(amount_due == "") { amount_due = 0; }
        
        amount_in_words = convert_number(amount_received);
        
        if(amount_in_words != 0)
        {
            amount_in_words = "Rupees " + amount_in_words.toLowerCase() + " only";
        }
        else
        {
            amount_in_words = "NUMBER OUT OF RANGE!";
        }
        
        j("#amount_in_words").html(amount_in_words);
        
        j("#amount_received").val(amount_received);
        
        balance_due = parseFloat(amount_due) - parseFloat(amount_received);
        j("#amount_due").val(amount_due);
        j("#balance_due").val(balance_due);
    });
    
    function show_supplier_details()
    {
        if(j("#supplier_id").val() > 0)
        { j("#show_account_btn, .separator").show(); }
        else
        { j("#show_account_btn, .separator").hide(); }
        
        j("#location").html(j("#supplier_id").find("option:selected").attr("location"));
        j("#amount_due").val(j("#supplier_id").find("option:selected").attr("account_balance"));
        j("#amount_received").val("");
        j("#balance_due").val("");
        j("#amount").val("");
        j("#amount_in_words").html("");
        
        /*j.get("index.php?option=com_amittrading&task=calculate_due_amount&tmpl=xml&type=s&party_id=" + j("#supplier_id").val(), function(amount_due){
            if(amount_due != 0)
            {
                j("#amount_due").val(amount_due);
                //j("#submit_button").prop("disabled", false);
            }
            else
            {
                j("#amount_due").val(amount_due);
                //j("#submit_button").prop("disabled", true);
            }
        });*/
    }
    
    function show_account()
    {
        var supplier_id = j("#supplier_id").val();
        
        if(supplier_id > 0)
        {
            if(window.opener == null)
            {
                window.open('index.php?option=com_amittrading&view=supplier_account&supplier_id=' + supplier_id, "supplier_account" + supplier_id, "height=" + screen.height + ", width=" + screen.width).focus();
            }
            else
            {
                go('index.php?option=com_amittrading&view=supplier_account&supplier_id=' + supplier_id)
            }
        }
        else
        {
            return false;
        }
    }
    
    function validateForm()
    {
        if(j("#payment_date").val() == "")
        {
            alert("Select date."); return false;
        }
        
        if(j("#supplier_id").val() == 0)
        {
            alert("Select supplier."); return false;
        }
        
        /*if(j("#amount_due").val() == 0)
        {
            alert("Account balance for the selected supplier is 0. Select another supplier."); return false;
        }*/
        
        if(!j("input[name='payment_mode']").is(":checked"))
        {
            alert("Select payment mode."); return false;
        }
        
        if(j("input[name='payment_mode']:checked").val() != <? echo CASH; ?>)
        {
            if(j("#cheque_no").val() == "")
            {
                alert("Enter cheque no."); return false;
            }
            
            if(j("#cheque_date").val() == "")
            {
                alert("Enter cheque date."); return false;
            }
            
            if(j("#bank_account_id").val() == 0)
            {
                alert("Select bank account."); return false;
            }
        }
        
        if(j("#amount").val() == 0 || j("#amount").val() == "")
        {
            alert("Enter valid amount."); return false;
        }
        /*if( parseFloat(j("#amount").val()) > parseFloat(j("#amount_due").val()) )
        {
            alert("You can't make payment greater than due amount");return false;
        } */
        /*else if(parseFloat(j("#amount").val()) - parseFloat(j("#amount_due").val()) > 1)
        {
            alert("Amount cannot be greater than due amount."); return false;
        }*/
        
        if(j("input[name='payment_mode']:checked").val() != <? echo CASH; ?>)
        {
            if(parseFloat(j("#amount").val()) > parseFloat(j("#bank_account_id").find("option:selected").attr("account_balance")))
            {
                alert("Amount cannot be greater than bank account balance."); return false;
            }
        }
        
        j("#supplier_payment").submit();
    }
</script>
<h1>Supplier Payment</h1>
<form id="supplier_payment" method="post" action="index.php?option=com_amittrading&task=save_supplier_payment">
    <table>
        <tr>
            <td valign="top">
                <table class="clean">
                    <tr>
                        <td align="right">Date : </td>
                        <td><input type="text" name="payment_date" id="payment_date" value="<? echo date("d-M-Y"); ?>" readonly="readonly" style="width:250px;"></td>
                    </tr>
                    <tr>
                        <td align="right">Supplier : </td>
                        <td>
                            <select id="supplier_id" name="supplier_id" onchange="show_supplier_details();" style="width:250px;">
                                <option value="0"></option>
                                <?
                                    if(count($this->suppliers) > 0)
                                    {
                                        foreach($this->suppliers as $supplier)
                                        {
                                            ?><option value="<? echo $supplier->id; ?>" location="<? echo $supplier->supplier_address . ($supplier->supplier_address != '' ? ", " : "") . $supplier->city; ?>" account_balance="<? echo $supplier->account_balance; ?>" <? echo ($this->supplier_id == $supplier->id ? "selected='selected'" : ($this->supplier_id != 0 ? "disabled='disabled'" : "")); ?> ><? echo $supplier->supplier_name; ?></option><?
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Location : </td>
                        <td id="location"></td>
                    </tr>
                    <tr>
                        <td align="right">Mode : </td>
                        <td>
                            <input type="radio" name="payment_mode" class="payment_mode" value="<? echo CASH; ?>">Cash
                            <input type="radio" name="payment_mode" class="payment_mode" value="<? echo CHEQUE; ?>">Cheque
                            <!--<input type="radio" name="payment_mode" value="<? //echo DRAFT; ?>" disabled="disabled">Draft-->
                        </td>
                    </tr>
                    <tr class="cheque_details">
                        <td align="right">Cheque No. : </td>
                        <td><input type="text" name="cheque_no" id="cheque_no" style="width:250px;"></td>
                    </tr>
                    <tr class="cheque_details">
                        <td align="right">Cheque Date : </td>
                        <td><input type="text" name="cheque_date" id="cheque_date" readonly="readonly" style="width:250px;"></td>
                    </tr>
                    <tr class="cheque_details">
                        <td align="right">Bank Account : </td>
                        <td>
                            <select name="bank_account_id" id="bank_account_id" style="width:250px;">
                                <option value="0"></option>
                                <?
                                    if(count($this->bank_accounts) > 0)
                                    {
                                        foreach($this->bank_accounts as $account)
                                        {
                                            ?><option value="<? echo $account->id; ?>" account_balance="<? echo round_2dp($account->balance); ?>"><? echo $account->account_name . ", " . $account->bank_name; ?></option><?
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Amount(Rounded) : </td>
                        <td><input type="text" name="amount" id="amount" style="width:250px;"></td>
                    </tr>
                    <tr>
                        <td align="right">Remarks : </td>
                        <td><input type="text" name="remarks" id="remarks" style="width:250px;"></td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <input type="button" id="show_account_btn" value="Show Account" onclick="show_account();" style="display:none;">
                <br />
                <fieldset style="height:150px;">
                    <legend>Supplier Account Details</legend>
                    <table>
                        <tr>
                            <td align="right">Amount Due : </td>
                            <td><input type="text" id="amount_due" readonly="readonly" tabindex="-1" style="width:250px;"></td>
                        </tr>
                        <tr>
                            <td align="right">Amount Received : </td>
                            <td><input type="text" id="amount_received" readonly="readonly" tabindex="-1" style="width:250px;"></td>
                        </tr>
                        <tr>
                            <td align="right">Balance Due : </td>
                            <td><input type="text" id="balance_due" readonly="readonly" tabindex="-1" style="width:250px;"></td>
                        </tr>
                        <tr>
                            <td valign="top">Amount(in words) : </td>
                            <td width="200"><span id="amount_in_words"></span></td>
                        </tr>
                    </table>
                </fieldset>            
            </td>
            <td valign="top" id="bank_account_details">
                <br class="separator" style="display:none;" />
                <br class="separator" />
                <fieldset style="height:150px;">
                    <legend>Bank Account Details</legend>
                    <table>
                        <tr>
                            <td valign="top">Account Balance : </td>
                            <td width="200"><span id="bank_account_balance"></span></td>
                        </tr>
                    </table>
                </fieldset>            
            </td>
        </tr>
    </table>
    <br />
    <input type="button" value="Submit (Alt + Z)" id="submit_button" onclick="validateForm(); return false;">
    <input type="button" value="Cancel" onclick="history.go('-1');">
    <input type="hidden" name="r" value="<? echo $this->return; ?>">
</form>