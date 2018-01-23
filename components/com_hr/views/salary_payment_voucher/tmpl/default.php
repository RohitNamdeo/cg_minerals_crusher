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
        
        if(j("#month").val() == 0 || j("#year").val() == 0)
        {
            j("#salary").html("");
        }
        
        j(".instrument_bank, #month, #year").chosen();
        
        j("#month, #year").change(function(){
            j("#salary").html("");
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
    
    j(document).on("keypress",".payment_amount",function(e){
        prevent_char(e.which,e);
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
            alert("Voucher must have atleast 1 entry.");
        }
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
    
    function get_salary_list()
    {
        if(j("#month").val() == 0 || j("#year").val() == 0)
        {
            alert("Please select both month and year.");
            return false;
        }
        go("index.php?option=com_hr&view=salary_payment_voucher&month=" + j("#month").val() + "&year=" + j("#year").val());
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
        {
            j("#salary_payment_voucher").submit();
        }
    }
</script>                  
<h1>Salary Payment Voucher</h1>
<table>
    <tr>
        <td>Month : </td>
        <td>
            <select id="month" style="width:90px;">
                <?
                    $months = array("0"=>"","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
                    foreach($months as $key=>$month)
                    {
                        ?>
                        <option value="<? echo $key; ?>" <? echo ($key == $this->month ? "selected='selected'" : ""); ?> ><? echo $month; ?></option>
                        <?
                    }
                ?>
            </select>
        </td>
        <td>Year : </td>
        <td>
            <select id="year" style="width:90px;">
                <option value="0"></option>
                <?
                    for($y=date("Y");$y>=2015;$y--)
                    {
                        ?>
                        <option value="<? echo $y; ?>" <? echo ($y == $this->year ? "selected='selected'" : ""); ?> ><? echo $y; ?></option>
                        <?
                    }
                ?>
            </select>
        </td>
        <td>
            <input type="button" value="Show" onclick="get_salary_list();">
        </td>
    </tr>
</table>
<div id="salary">
    <form method="post" id="salary_payment_voucher" action="index.php?option=com_hr&task=save_salary_voucher">
        <?
            if(count($this->salary_vouchers) > 0)
            {
                ?>
                <br />
                <table>
                    <tr>
                        <td>Payment Date : </td>
                        <td><input type="text" name="payment_date" id="payment_date" style="width:80px;"></td>
                    </tr>
                </table>
                <br />
                <table class="clean floatheader centreheadings spread">
                    <tr>
                        <th>#</th>
                        <th>Emp. Code</th>
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
                            $total_amount += floatval($salary->salary);
                            ?>
                            <tr class="payments">
                                <td align="center" class="index"><? echo ++$x; ?></td>
                                <td align="center">
                                    <? echo $salary->employee_id; ?>
                                    <input type="hidden" name="salary_ids[]" value="<? echo $salary->id; ?>">
                                    <input type="hidden" name="employee_ids[]" value="<? echo $salary->employee_id; ?>">
                                </td>
                                <td><? echo $salary->employee_name; ?></td>
                                <td>
                                    <input type="radio" name="instrument<? echo $salary->id; ?>" class="instrument_type" value="<? echo CASH; ?>">Cash<br />
                                    <input type="radio" name="instrument<? echo $salary->id; ?>" class="instrument_type" value="<? echo CHEQUE; ?>">Cheque
                                    <input type="hidden" name="instrument[]" class="instrument">
                                </td>
                                <td align="center"><input type="text" name="instrument_no[]" class="instrument_no" style="width:100px;"></td>
                                <td width="200">
                                    <div class="instruments">
                                        <select name="instrument_bank[]" class="instrument_bank" style="width:200px;">
                                            <option value="0"></option>
                                            <?
                                                if(count($this->banks) > 0)
                                                {
                                                    foreach($this->banks as $bank)
                                                    {
                                                        ?><option value="<? echo $bank->id; ?>" ><? echo $bank->bank_name; ?></option><?
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                                <td align="center">
                                    <input type="text" name="payment_amount[]" class="payment_amount" value="<? echo $salary->salary; ?>" style="width:100px;">
                                    <input type="hidden" class="eligible_payment_amount" value="<? echo $salary->salary; ?>">
                                </td>
                                <td align="center"><input type="text" name="remarks[]" style="width:200px;"></td>
                                <td align="center"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete"></td>
                            </tr>
                            <?
                        }
                    ?>
                </table>
                <br />
                <div><b>Total Amount : </b><span id="total_amount"><? echo round_2dp($total_amount); ?></span>/-</div>
                <br />
                <input type="hidden" name="salary_month" value="<? echo $this->month; ?>">
                <input type="hidden" name="salary_year" value="<? echo $this->year; ?>">
                <input type="button" value="Submit (Alt + Z)" onclick="validateForm();">
                <input type="button" value="Cancel" onclick="history.go(-1);">
                <?
            }
            else
            {
                echo "<br />Employees with unpaid salary not found.";
            }
        ?>
    </form>
</div>