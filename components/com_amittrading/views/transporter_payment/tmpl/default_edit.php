<?php
    defined('_JEXEC') or die( 'Restricted access' );
?>
<script>
    j(function(){
        j("#payment_date").datepicker({"dateFormat" : "dd-M-yy"});
        j("#amount").focus();
        
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
    
    j(document).on("keypress","#amount",function(e){
        prevent_char(e.which,e);
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
        
        if(j("#transporter_id").val() == 0)
        {
            alert("Select transporter."); return false;
        }
        
       // if(j("#amount_due").val() == 0)
//        {
//            alert("Account balance for the selected transporter is 0. Select another transporter."); return false;
//        }
        
        if(j("#amount").val() == 0 || j("#amount").val() == "")
        {
            alert("Enter valid amount."); return false;
        }
        /*if(parseFloat(j("#amount").val()) > parseFloat(j("#amount_due").val()))
        {
            alert(j("#amount").val());
            alert(j("#amount_due").val())
            alert("You can't make payment greater than due amount");return false;
        }*/
        /*else if(parseFloat(j("#amount").val()) - parseFloat(j("#amount_due").val()) > 1)
        {
            alert("Amount cannot be greater than due amount."); return false;
        }*/
        
        j("#transporter_payment").submit();
    }
</script>
<h1>Edit Payment</h1>
<form id="transporter_payment" method="post" action="index.php?option=com_amittrading&task=update_transporter_payment">
    <table>
        <tr>
            <td valign="top">
                <table class="clean">
                    <tr>
                        <td align="right">Date : </td>
                        <td><input type="text" name="payment_date" id="payment_date" value="<? echo date("d-M-Y", strtotime($this->payment->payment_date)); ?>" style="width:250px;"></td>
                    </tr>
                    <tr>
                        <td align="right">Transporter : </td>
                        <td>
                            <? echo $this->payment->transporter_name; ?>
                            <input type="hidden" id="transporter_id" name="transporter_id" value="<? echo $this->transporter_id; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Amount(Rounded) : </td>
                        <td><input type="text" name="amount" id="amount" value="<? echo $this->payment->total_amount; ?>" style="width:250px;"></td>
                    </tr>
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
                            <td><input type="text" id="amount_received" value="<? echo $this->payment->total_amount; ?>" readonly="readonly" tabindex="-1" style="width:250px;"></td>
                        </tr>
                        <tr>
                            <td align="right">Balance Due : </td>
                            <td><input type="text" id="balance_due" value="<? echo $this->amount_due - $this->payment->total_amount; ?>" readonly="readonly" tabindex="-1" style="width:250px;"></td>
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