<?php
    defined('_JEXEC') or die('Restricted access');
?>
<style>
    input[type='text'], select{
        width: 300px;
    }
</style>
<script>
     j(function(){
        j("#payment_date").datepicker({
            "dateFormat" : "dd-M-yy",
             changeMonth: true,
             changeYear: true
        });
        
        j("#employee_id, #instrument_bank").chosen();
        j(".cheque_details").hide();
    });
    
    j(document).on("change",".instrument",function(e){
        j(".cheque_details").toggle();
        if(j(this).val() == <? echo CASH; ?>)
        {
            j("#instrument_no").val("");
            j("#instrument_bank").val(0).trigger("liszt:updated");
        }
    });
    
    j(document).on("keypress","#amount",function(e){
        prevent_char(e.which,e);
    });
    
    j(document).on("change", "#employee_id", function(){
        if(j(this).val() == 0)
        {
            j("#gross_salary, #eligible_amount").html("");
        }
        else
        {
            j("#gross_salary").html(j(this).find("option:selected").attr("gross_salary"));
            j("#eligible_amount").html(j(this).find("option:selected").attr("eligible_amount"));
        }
    });
    
    function validateForm()
    {
        if(j("#payment_date").val() == "")
        {
            alert("Select payment date.");
            return false;
        }
        
        if(j("#employee_id").val() == 0)
        {
            alert("Select employee.");
            return false;
        }
        
        if(!j("input[name='instrument']").is(":checked"))
        {
            alert("Select instrument."); return false;
        }
        
        if(j("input[name='instrument']:checked").val() != <? echo CASH; ?>)
        {
            if(j("#instrument_no").val() == "")
            {
                alert("Enter instrument no."); return false;
            }
            
            if(j("#instrument_bank").val() == 0)
            {
                alert("Select instrument bank."); return false;
            }
        }
        
        if(j("#amount").val() == 0 || j("#amount").val() == "")
        {
            alert("Enter valid amount."); return false;
        }
        
        j("#advance_salary_payment_voucher").submit(); 
    }
</script>                  
<h1>Advance Salary Payment Voucher</h1>
<form id="advance_salary_payment_voucher" action="index.php?option=com_hr&task=save_advance_salary_voucher" method="post">
    <table class="clean">
        <tr>
            <td>Payment Date</td>
            <td><input type="text" name="payment_date" id="payment_date"></td>
        </tr>
        <tr>
            <td>Employee</td>
            <td>
                <select id="employee_id" name="employee_id">
                    <option value="0"></option>
                    <?
                        if(count($this->employees) > 0)
                        {
                            foreach($this->employees as $employee)
                            {
                                ?><option value="<? echo $employee->id; ?>" gross_salary="<? echo round_2dp($employee->gross_salary); ?>" eligible_amount="<? echo round_2dp($employee->eligible_amount); ?>" ><? echo $employee->employee_name; ?></option><?
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Gross Salary</td>
            <td id="gross_salary"></td>
        </tr>
        <tr>
            <td>Eligible Amount</td>
            <td id="eligible_amount"></td>
        </tr>
        <tr>
            <td>Instrument</td>
            <td>
                <input type="radio" name="instrument" class="instrument" value="<? echo CASH; ?>" checked="checked">Cash
                <input type="radio" name="instrument" class="instrument" value="<? echo CHEQUE; ?>">Cheque
            </td>
        </tr>
        <tr class="cheque_details">
            <td>Instrument No.</td>
            <td><input type="text" name="instrument_no" id="instrument_no"></td>
        </tr>
        <tr class="cheque_details">
            <td>Instrument Bank</td>
            <td>
                <select name="instrument_bank" id="instrument_bank">
                    <option value="0"></option>
                    <?
                        if(count($this->banks) > 0)
                        {
                            foreach($this->banks as $bank)
                            {
                                ?><option value="<? echo $bank->id; ?>"><? echo $bank->bank_name; ?></option><?
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Amount</td>
            <td><input type="text" name="amount" id="amount"></td>
        </tr>
        <tr>
            <td>Remarks</td>
            <td><input type="text" name="remarks" id="remarks"></td>
        </tr>
    </table>
    <br />
    <input type="button" value="Submit (Alt + Z)" onclick="validateForm(); return false;">
    <input type="button" value="Cancel" onclick="history.go('-1');">
</form>