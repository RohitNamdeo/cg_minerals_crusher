<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
     j(function(){
        j("#payment_date").datepicker({
            "dateFormat" : "dd-M-yy",
             changeMonth: true,
             changeYear: true
        });
        
        j(".instrument_bank").chosen(); 
        
        j(".payments").each(function(){
            if(j(this).find(".instrument_type:checked").val() == <? echo CASH; ?>)
            {
                j(this).find(".instrument_no").val("");
                j(this).find(".instrument_no").hide();
                j(this).find(".instrument_bank").val("0").trigger("liszt:updated");
                j(this).find(".instruments").hide();
            }
        });
    });
    
    j(document).on("click",".instrument_type",function(){
        var instrument_type = j(this).val();
        j(this).parent().find(".instrument").val(instrument_type);
        if(instrument_type == <? echo CASH; ?>)
        { 
            j(this).parent().parent().find(".instrument_no").val("");
            j(this).parent().parent().find(".instrument_no").hide();
            j(this).parent().parent().find(".instrument_bank").val("0").trigger("liszt:updated");
            j(this).parent().parent().find(".instruments").hide();
        }
        else
        { 
            j(this).parent().parent().find(".instrument_no").show();
            j(this).parent().parent().find(".instruments").show();
        }
    });
    
    j(document).on("click", ".delete", function(){
        if(j(".payments").length > 1)
        {
            j(this).closest("tr").remove();
            
            var x = 1;
            j(".index").each(function(){
                j(this).html(x++);
            });
            
            calculate_total_amount();
        }
        else
        {
            alert("Voucher must have atleast 1 entry. To delete the voucher click on delete button.");
        }
    });
    
    j(document).on("keypress",".payment_amount",function(e){
        prevent_char(e.which,e);
    });
    
    j(document).on("blur",".payment_amount",function(){
        payment_amount = j(this).val();
        eligible_payment_amount = parseFloat(j(this).parent().find(".eligible_payment_amount").val());
        if(payment_amount == 0 || payment_amount == "" || (payment_amount > eligible_payment_amount) )
        {
            alert("Payment amount cannot be 0 and greater than " + eligible_payment_amount);
            var self = j(this);
            setTimeout(function() { j(self).focus(); }, 10); 
        }
        calculate_total_amount();
    });
    
    function calculate_total_amount()
    {
        total_amount = 0;
        j(".payment_amount").each(function(){
            if(j(this).val() != 0 && j(this).val() != "")
            {
                total_amount += parseFloat(j(this).val());
            }
        });
        j("#total_amount").html(total_amount.toFixed(2));
    }
    
    function validateForm()
    {
        if(j("#payment_date").val() == "")
        {
            alert("Select payment date.");
            return false;
        }
        
        error_in = "";
        j(".payments").each(function(){
            if(!j(this).find(".instrument_type").is(":checked"))
            {
                error_in = "instrument_type";
                return false;
            }
            if(j(this).find(".instrument").val() == <? echo CHEQUE; ?>)
            {
                if(j(this).find(".instrument_no").val() == "")
                {
                    error_in = "instrument_no";
                    return false;
                }
                if(j(this).find(".instrument_bank").val() == 0)
                {
                    error_in = "instrument_bank";
                    return false;
                }
            }
            if(j(this).find(".payment_amount").val() == "" || j(this).find(".payment_amount").val() == 0)
            {
                error_in = "payment_amount";
                return false;
            }
        });
        
        if(error_in != "")
        {
            if(error_in == "instrument_type")
            {
                alert ("Select instrument.");
                return false;
            }
            if(error_in == "instrument_no")
            {
                alert ("Enter instrument no.");
                return false;
            }
            if(error_in == "instrument_bank")
            {
                alert ("Enter instrument bank.");
                return false;
            }
            if(error_in == "payment_amount")
            {
                alert ("Enter valid payment amount.");
                return false;
            }
        }
        else
        { j("#salary_payment_voucher").submit(); }
    }
    
    function delete_salary_voucher()
    {
        if(confirm("Are you sure?"))
        {
            go("index.php?option=com_hr&task=delete_salary_voucher&payment_id=<? echo $this->payment_id; ?>&salary_month=<? echo $this->voucher->salary_month; ?>&salary_year=<? echo $this->voucher->salary_year; ?>");
        }
        else
        {
            return false;
        }
    }
</script>                  
<h1>Edit Salary Payment Voucher</h1>
<table>
    <tr>
        <td><b>Month : </b></td>
        <td><b><? echo date("F'Y", strtotime($this->voucher->salary_year . '-' . $this->voucher->salary_month . '-01')); ?></b></td>
    </tr>
</table>
<form method="post" id="salary_payment_voucher" action="index.php?option=com_hr&task=update_salary_voucher">
    <?
        if(count($this->salary_vouchers) > 0)
        {
            ?>
            <br />
            <table>
                <tr>
                    <td>Payment Date : </td>
                    <td><input type="text" name="payment_date" id="payment_date" style="width:80px;" value="<? echo date("d-M-Y", strtotime($this->voucher->payment_date)); ?>"></td>
                </tr>
            </table>
            <br />
            <table class="clean floatheader centreheadings spread">
                <tr>
                    <th>#</th>
                    <th>Employee Code</th>
                    <th>Employee Name</th>
                    <th>Instrument</th>
                    <th>Instrument No</th>
                    <th>Instrument Bank</th>
                    <th>Amount</th>
                    <th>Remarks</th> 
                    <th>Action</th>
                </tr>
                <?
                    $x = 0;
                    $total_amount = 0;
                    foreach($this->salary_vouchers as $salary)
                    {
                        $total_amount += floatval($salary->amount);
                        ?>
                        <tr class="payments">
                            <td align="center" class="index"><? echo ++$x; ?></td>
                            <td align="center">
                                <? echo $salary->employee_id; ?>
                                <input type="hidden" name="salary_ids[]" value="<? echo $salary->item_id; ?>">
                                <input type="hidden" name="employee_ids[]" class="employee_ids" value="<? echo $salary->employee_id; ?>">
                            </td>
                            <td><? echo $salary->employee_name; ?></td>
                            <td>
                                <input type="radio" name="instrument<? echo $salary->item_id; ?>" class="instrument_type" value="<? echo CASH; ?>" <? echo ($salary->instrument == CASH ? "checked='checked'" : ""); ?> >Cash<br />
                                <input type="radio" name="instrument<? echo $salary->item_id; ?>" class="instrument_type" value="<? echo CHEQUE; ?>" <? echo ($salary->instrument == CHEQUE ? "checked='checked'" : ""); ?> >Cheque
                                <input type="hidden" name="instrument[]" class="instrument" value="<? echo $salary->instrument; ?>">
                            </td>
                            <td align="center"><input type="text" name="instrument_no[]" class="instrument_no" style="width:100px;" value="<? echo ($salary->instrument == CHEQUE ? $salary->instrument_no : ""); ?>"></td>
                            <td width="200">
                                <div class="instruments">
                                    <select name="instrument_bank[]" class="instrument_bank" style="width:200px;">
                                        <option value="0"></option>
                                        <?
                                            if(count($this->banks) > 0)
                                            {
                                                foreach($this->banks as $bank)
                                                {
                                                    ?><option value="<? echo $bank->id; ?>" <? echo ($salary->instrument_bank == $bank->id ? "selected='selected'" : ""); ?> ><? echo $bank->bank_name; ?></option><?
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td align="center">
                                <input type="text" name="payment_amount[]" class="payment_amount" value="<? echo $salary->amount; ?>" style="width:100px;">
                                <input type="hidden" class="eligible_payment_amount" value="<? echo $salary->eligible_payment_amount; ?>">
                            </td>
                            <td align="center"><input type="text" name="remarks[]" style="width:200px;" value="<? echo $salary->remarks; ?>"></td>
                            <td align="center"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete"></td>
                        </tr>
                        <?
                    }
                ?>
            </table>
            <br />
            <div><b>Total Amount : </b><span id="total_amount"><? echo round_2dp($total_amount); ?></span>/-</div>
            <br />
            <input type="hidden" name="salary_month" value="<? echo $this->voucher->salary_month; ?>">
            <input type="hidden" name="salary_year" value="<? echo $this->voucher->salary_year; ?>">
            <input type="hidden" name="payment_id" value="<? echo $this->payment_id; ?>">
            <input type="button" value="Update (Alt + Z)" onclick="validateForm(); return false;">
            <input type="button" value="Delete" onclick="delete_salary_voucher(); return false;">
            <input type="button" value="Cancel" onclick="history.go(-1);">
            <?
        }
    ?>
</form>