<?php
    defined('_JEXEC') or die( 'Restricted access' );
?>
<script>
    j(function(){
        j("#payment_date").datepicker({"dateFormat" : "dd-M-yy"});
       // j("#payment_date").datepicker({
//            showOtherMonths: true,
//            selectOtherMonths: true,
//            changeMonth: true,
//            changeYear: true,
//            showButtonPanel: true,
//            dateFormat: 'yy-mm-dd',
//            minDate: 0
//        });

        j("#transporter_id").chosen().trigger("liszt:activate");
        <?
            if($this->transporter_id > 0)
            {
                ?>show_transporter_details();<?
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
    
    j(document).on("keyup","#amount",function(e){
        amount_received = j(this).val();
        amount_due = j("#amount_due").val();
        //alert("gfdg");
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
        j("#balance_due").text(balance_due); 
    });
    
    function show_transporter_details()
    {
        if(j("#transporter_id").val() > 0)
        { j("#show_account_btn").show(); }
        else
        { j("#show_account_btn").hide(); }
        
        j("#amount_due").val(j("#transporter_id").find("option:selected").attr("account_balance"));
        j("#amount_received").val("");
        j("#balance_due").val("");
        j("#amount").val("");
        j("#amount_in_words").html("");
        
        /*j.get("index.php?option=com_amittrading&task=calculate_due_amount&tmpl=xml&type=t&party_id=" + j("#transporter_id").val(), function(amount_due){
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
        var transporter_id = j("#transporter_id").val();
        
        if(transporter_id > 0)
        {
            if(window.opener == null)
            {
                window.open('index.php?option=com_amittrading&view=transporter_account&transporter_id=' + transporter_id, "transporter_account" + transporter_id, "height=" + screen.height + ", width=" + screen.width).focus();
            }
            else
            {
                go('index.php?option=com_amittrading&view=transporter_account&transporter_id=' + transporter_id)
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
        
        if(j("#transporter_id").val() == 0)
        {
            alert("Select transporter."); return false;
        }
        
        //if(j("#amount_due").val() == 0)
//        {
//            alert("Account balance for the selected transporter is 0. Select another transporter."); return false;
//        }
//        
        if(j("#amount").val() == 0 || j("#amount").val() == "")
        {
            alert("Enter valid amount."); return false;
        }

        /*if( parseFloat(j("#amount").val()) > parseFloat(j("#amount_due").val()))
        {
            alert("You can't make payment greater than due amount");return false;
        }*/
        /*else if(parseFloat(j("#amount").val()) - parseFloat(j("#amount_due").val()) > 1)
        {
            alert("Amount cannot be greater than due amount."); return false;
        }*/
        
        j("#transporter_payment").submit();
    }
</script>

<table>
<tr>

     <? 
if(count($this->sales_invoice_detail) > 0)
{
     ?>
     <td valign="top"> 
     <h1>Selected Invoices</h1>
     <table class="clean centreheadings">  
        <tr>
            <th>#</th>
           <!-- <th width="20"><input type="checkbox" id="check_all"></th>    -->
            <th>Bill No.</th>
            <th>Bill Date</th>
            <th>Amount</th>
            <th>Cash Paid To Driver</th>
            <th>Diesel Amount</th>
            <th>Amount Paid</th>
            <th>Status</th>
        </tr> 
        <?                                 
            $x = 1;
            $total_amount = 0; 
            foreach($this->sales_invoice_detail as $sales_invoice_detail)
            {
        ?>
                <tr class="<? echo ($sales_invoice_detail->status == PAYMENT_ADJUSTED ? "paid_bill" : "unpaid_bill"); ?>" id="bill_<? echo $sales_invoice_detail->id; ?>">          
                    <td align="center"><? echo $x++; ?></td>
                    <!--<td align="center"> -->
                        <?
                            //if($sales_invoice_detail->status == NOT_ADJUSTED)
//                            {
                                ?><!--<input type="checkbox" name="invoices_id[]" class="pending_bills" value="<? //echo $sales_invoice_detail->id; ?>">--><?
//                            }
                        ?>
                   <!-- </td> -->
                    <td><? echo $sales_invoice_detail->id; ?></td>
                    <td align="center"><? echo date("d-M-Y", strtotime($sales_invoice_detail->date)); ?></td>
                    

                    <td align="right" class="bill_amount"><? echo round_2dp($sales_invoice_detail->amount); ?></td> 
                    <td align="right"><? echo round_2dp($sales_invoice_detail->cash_paid_to_driver); ?></td>
                    <td align="right"><? echo round_2dp($sales_invoice_detail->diesel_amount); ?></td>
                    <td align="right"><? echo round_2dp($sales_invoice_detail->status == PAYMENT_ADJUSTED ? $sales_invoice_detail->amount : "0"); ?></td> 
                    <td align="right"><? echo ($sales_invoice_detail->status == PAYMENT_ADJUSTED ? "Paid" : "Not Paid"); ?></td>
                </tr>
                <?
            }
            ?>
     </table> 
     </td>  
     <?
        }
     ?>
 
 <td valign="top">
<h1>Transporter Payment</h1>
<form id="transporter_payment" method="post" action="index.php?option=com_amittrading&task=save_transporter_payment">
    <table>
        <tr>
            <td valign="top">
                <table class="clean">
                    <tr>
                        <td align="right">Date : </td>
                        <td><input type="text" name="payment_date" id="payment_date" value="<? echo date("d-M-Y"); ?>" style="width:250px;"></td>
                    </tr>
                    <tr>
                        <td align="right">Transporter : </td>
                        <td>
                            <select id="transporter_id" name="transporter_id" onchange="show_transporter_details();" style="width:250px;">
                                <option value="0"></option>
                                <?
                                    if(count($this->transporters) > 0)
                                    {
                                        foreach($this->transporters as $transporter)
                                        {
                                            ?><option value="<? echo $transporter->id; ?>" account_balance=<? echo $transporter->account_balance; ?> <? echo ($this->transporter_id == $transporter->id ? "selected='selected'" : ($this->transporter_id != 0 ? "disabled='disabled'" : "")); ?> ><? echo $transporter->transporter_name; ?></option><?
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
                    <legend>Account Details</legend>
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
                            <td><input type="text" name="balance_due" id="balance_due" readonly="readonly" tabindex="-1" style="width:250px;"></td>
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
    <input type="button" value="Submit (Alt + Z)" id="submit_button" onclick="validateForm(); return false;">
    <input type="button" value="Cancel" onclick="history.go('-1');">
    <input type="hidden" name="r" value="<? echo $this->return; ?>">
    <!--<input type="hidden" name="transporter_id" value="<? //echo $this->transporters_id; ?>">  -->
</form>
</td>
</tr>
</table>