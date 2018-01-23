<?php
    defined('_JEXEC') or die( 'Restricted access' );
?>
<script>
    j(function(){
        j("#payment_date, #cheque_date").datepicker({"dateFormat" : "dd-M-yy"});
        j("#bank_id, #bank_account_id").chosen();
        j("#amount").focus();
        
        if(j("input[name='payment_mode']:checked").val() == <? echo CASH; ?>)
        {
            j(".cheque_details").hide();
        }
        
        amount_received = j("#amount").val();
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
    });
    
    /*j(document).on("keydown", function(e){
        if (e.altKey && e.which == 83)
        {
            e.preventDefault();
            validateForm();
        }
    });*/
    
    j(document).on("keypress","#amount, #credit_amount",function(e){
        prevent_char(e.which,e);
    });
    
    j(document).on("change",".payment_mode",function(e){
        j(".cheque_details").toggle();
        if(j(this).val() == <? echo CASH; ?>)
        {
            j("#cheque_no").val("");
            j("#cheque_date").val("");
            j("#bank_id").val(0).trigger("liszt:updated");
            j("#bank_account_id").val(0).trigger("liszt:updated");
        }
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
    
    function validateForm()
    {
        if(j("#payment_date").val() == "")
        {
            alert("Select date."); return false;
        }
        
        if(j("#customer_id").val() == 0)
        {
            alert("Select customer."); return false;
        }
        
        /*if(j("#amount_due").val() == 0)
        {
            alert("Selected customer does not have any dues. Select another customer."); return false;
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
            
            if(j("#bank_id").val() == 0)
            {
                alert("Select cheque bank."); return false;
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
        /*if(parseFloat(j("#amount").val()) > parseFloat(j("#amount_due").val()))
        {
            alert("You can't make payment greater than due amount");return false;
        }*/
        
        if( (j("#credit_amount").val() != 0 && j("#credit_amount").val() != "") && j("#remarks").val() == "" )
        {
            alert("Enter remarks."); return false;
        }
        
        /*credit_amount = parseFloat(j("#credit_amount").val());
        if(credit_amount == "" || isNaN(credit_amount))
        {
            credit_amount = 0;
        }
        
        if(parseFloat(j("#amount").val()) + parseFloat(credit_amount) - parseFloat(j("#amount_due").val()) > 1)
        {
            alert("Sum of amount and credit amount cannot be greater than due amount."); return false;
        }*/
        
        j("#customer_payment").submit();
        //go("index.php?option=com_hr&view=dashboard");
        go("index.php?option=com_master&view=manage_customers");
    }
</script>
<h1>Edit Received Payment</h1>
<form id="customer_payment" method="post" action="index.php?option=com_amittrading&task=update_customer_payment" target="_blank">
    <table>
        <tr>
            <td valign="top">
                <table class="clean">
                    <tr>
                        <td align="right">Date : </td>
                        <td><input type="text" name="payment_date" id="payment_date" value="<? echo date("d-M-Y", strtotime($this->payment->payment_date)); ?>" style="width:250px;"></td>
                    </tr>
                    <tr>
                        <td align="right">Customer : </td>
                        <td>
                            <? echo $this->payment->customer_name; ?>
                            <input type="hidden" id="customer_id" name="customer_id" value="<? echo $this->customer_id; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Location : </td>
                        <td id="location"><? echo $this->payment->customer_address . ($this->payment->customer_address != '' ? ", " : "") . $this->payment->city; ?></td>
                    </tr>
                    <tr>
                        <td align="right">Mode : </td>
                        <td>
                            <input type="radio" name="payment_mode" class="payment_mode" value="<? echo CASH; ?>" <? echo ($this->payment->payment_mode == CASH ? "checked='checked'" : ""); ?> >Cash
                            <input type="radio" name="payment_mode" class="payment_mode" value="<? echo CHEQUE; ?>" <? echo ($this->payment->payment_mode == CHEQUE ? "checked='checked'" : ""); ?> >Cheque
                        </td>
                    </tr>
                    <tr class="cheque_details">
                        <td align="right">Cheque No. : </td>
                        <td><input type="text" name="cheque_no" id="cheque_no" value="<? echo $this->payment->cheque_no; ?>" style="width:250px;"></td>
                    </tr>
                    <tr class="cheque_details">
                        <td align="right">Cheque Date : </td>
                        <td><input type="text" name="cheque_date" id="cheque_date" value="<? echo ($this->payment->payment_mode != CASH ? date("d-M-Y", strtotime($this->payment->cheque_date)) : ""); ?>" readonly="readonly" style="width:250px;"></td>
                    </tr>
                    <tr class="cheque_details">
                        <td align="right">Cheque Bank : </td>
                        <td>
                            <select name="bank_id" id="bank_id" style="width:250px;">
                                <option value="0"></option>
                                <?
                                    if(count($this->banks) > 0)
                                    {
                                        foreach($this->banks as $bank)
                                        {
                                            ?><option value="<? echo $bank->id; ?>" <? echo ($this->payment->bank_id == $bank->id ? "selected='selected'" : ""); ?> ><? echo $bank->bank_name; ?></option><?
                                        }
                                    }
                                ?>
                            </select>
                        </td>
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
                                            ?><option value="<? echo $account->id; ?>" <? echo ($this->payment->bank_account_id == $account->id ? "selected='selected'" : ""); ?> ><? echo $account->account_name . ", " . $account->bank_name; ?></option><?
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Amount(Rounded) : </td>
                        <td><input type="text" name="amount" id="amount" value="<? echo $this->payment->amount_received; ?>" style="width:250px;"></td>
                    </tr>
                    <tr>
                        <td align="right">Discount/Claim : </td>
                        <td>
                            <input type="text" name="credit_amount" id="credit_amount" value="<? echo $this->payment->credit_amount; ?>" style="width:250px;">
                            <br />
                            <small>Enter remarks if discount/claim is greater than 0.</small>
                        </td>
                    </tr>
                    <!--<tr>
                        <td align="right">Credit Reason : </td>
                        <td><input type="text" name="credit_reason" id="credit_reason" value="<? //echo $this->payment->credit_reason; ?>" style="width:250px;"></td>
                    </tr>-->
                    <tr>
                        <td align="right">Remarks : </td>
                        <td><input type="text" name="remarks" id="remarks" value="<? echo $this->payment->remarks; ?>" style="width:250px;"></td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <fieldset style="height:150px;">
                    <legend>Account Details</legend>
                    <table>
                        <tr>
                            <td align="right">Amount Due : </td>
                            <td><input type="text" id="amount_due" value="<? echo $this->amount_due; ?>" readonly="readonly" tabindex="-1" style="width:250px;"></td>
                        </tr>
                        <tr>
                            <td align="right">Amount Received : </td>
                            <td><input type="text" id="amount_received" value="<? echo $this->payment->amount_received; ?>" readonly="readonly" tabindex="-1" style="width:250px;"></td>
                        </tr>
                        <tr>
                            <td align="right">Balance Due : </td>
                            <td><input type="text" id="balance_due" value="<? echo $this->amount_due - $this->payment->amount_received; ?>" readonly="readonly" tabindex="-1" style="width:250px;"></td>
                        </tr>
                        <tr>
                            <td valign="top">Amount(in words) : </td>
                            <td width="200"><span id="amount_in_words"></span></td>
                        </tr>
                    </table>
                </fieldset>            
            </td>
        </tr>
    </table>
    <br />
    <input type="button" value="Update (Alt + Z)" id="submit_button" onclick="validateForm(); return false;">
    <input type="button" value="Cancel" onclick="history.go('-1');">
    <input type="hidden" name="r" value="<? echo $this->return; ?>">
    <input type="hidden" name="payment_id" value="<? echo $this->payment_id; ?>">
</form>