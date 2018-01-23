<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        j(document).on("keypress",".deduction_amount",function(e){
            prevent_char(e.which,e);
            j(this).css({'border':'1px solid #7F9DB9'});
        });
    });
    
    j(document).on("click",".delete",function(){
        j(this).closest("tr").hide();
        j(this).closest("tr").find(".deduction_amount").val(0);
        calculate_total_salary();
        var x = 1;
        j(".index").each(function(){
            if(j(this).parent().is(":visible"))
            {
                j(this).html(x++);
            }
        });
    });
        
    function calculate_total_salary()
    {
        total_deduction_amount = 0;
        new_salary_amount = <? echo $this->salary->total_salary; ?>;
        j(".deductions").each(function(){
            new_deduction_amount = (j(this).find(".deduction_amount").val() == "" ? 0 : j(this).find(".deduction_amount").val());            
            original_deduction_amount = j(this).find(".original_deduction_amount").val();
            total_deduction_amount += parseFloat(new_deduction_amount); 
            
            new_salary_amount = parseFloat(new_salary_amount) + parseFloat(original_deduction_amount) - parseFloat(new_deduction_amount);
            
            if(new_salary_amount < 0)
            { j(this).find(".deduction_amount").css({'border':'1px solid red'}); }           
        });
        
        j("#salary_amount").html(new_salary_amount);
        j("#total_deduction_amount").html(total_deduction_amount.toFixed(2));
    }
    
    function validateForm()
    {
        row_count = 0;
        error_type = "";
        j(".deductions").each(function(){
            if(j(this).is(":visible"))
            {
                row_count++;
                if(j(this).find(".deduction_amount").val() == "" || j(this).find(".deduction_amount").val() == 0)
                {
                    error_type = "blank";
                    return false;
                }
                else if(j(this).find(".deduction_amount").val() > parseFloat(j(this).find(".advance_amount").html()))
                {
                    error_type = "greater_than_advance_paid";
                    return false;
                }
            }
        });
        
        if(error_type != "")
        {
            if(error_type == "blank")
            {
                alert("Enter valid deduction amount.");
                return false;
            }
            if(error_type == "greater_than_advance_paid")
            {
                alert("Deduction amount cannot be greater than advance paid.");
                return false;
            }
        }
        
        if(parseFloat(j("#salary_amount").html()) < 0)
        {
            alert("Total salary cannot be negative.");
            return false;
        }
        
        if(row_count == 0)
        {
            if(confirm("Are you sure you want to remove all advance deductions from salary?"))
            {
                j("#advance_deduction_form").submit();
            }
            else
            {
                return false;
            }
        }
        else
        {
            j("#advance_deduction_form").submit();
        }
    }
</script>
<h1>Edit Advance Deduction</h1>
<table class="clean" width="200">
    <tr>
        <td>Employee</td>
        <td><? echo $this->salary->employee_name; ?></td>
    </tr>
    <tr>
        <td>Employee Code</td>
        <td><? echo $this->salary->employee_id; ?></td>
    </tr>
    <tr>
        <td>Location</td>
        <td><? echo $this->salary->location_name; ?></td>
    </tr>
</table>
<br />
<form id="advance_deduction_form" method="post" action="index.php?option=com_hr&task=edit_advance_deduction">
    <?
        if(count($this->advances) > 0)
        {
            ?>
            <table class="clean centreheadings">
                <tr>
                    <th align="center">#</th>
                    <th>Advance Amount</th>
                    <th>Deducted Amount</th>
                    <th>Deduction Amount</th>
                    <th>Action</th>
                </tr>
                <?
                    $x = 0;
                    $total_deduction_amount = 0;
                    foreach($this->advances as $advance)
                    {
                        $total_deduction_amount += $advance->amount;
                        ?>
                        <tr class="deductions">
                            <td align="center" class="index"><? echo ++$x; ?></td>
                            <td class="advance_amount"><? echo $advance->advance_amount; ?></td>
                            <td><? echo $advance->amount; ?></td>
                            <td>
                                <input type="text" name="deduction_amount[]" class="deduction_amount" value="<? echo $advance->amount; ?>" onblur="calculate_total_salary();">
                                <input type="hidden" name="original_deduction_amount[]" class="original_deduction_amount" value="<? echo $advance->amount; ?>">
                                <input type="hidden" name="item_ids[]" value="<? echo $advance->item_id; ?>">
                                <input type="hidden" name="advance_ids[]" value="<? echo $advance->advance_id; ?>">
                            </td>
                            <td align="center">
                                <img src="custom/graphics/icons/blank.gif" class="delete" title="Delete">
                            </td>
                        </tr>
                        <?
                    }
                ?>
                <tfoot>
                    <tr>
                        <td colspan="3" align="right"><b>Total : </b></td>
                        <td align="right" id="total_deduction_amount"><? echo round_2dp($total_deduction_amount); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <?
        }
    ?>
    <br />
    <div><b>Total Salary : </b><span id="salary_amount"><? echo $this->salary->total_salary; ?></span> /-</div>
    <br />
    <input type="hidden" name="total_salary" value="<? echo $this->salary->total_salary; ?>">
    <input type="hidden" name="employee_id" value="<? echo $this->salary->employee_id; ?>">
    <input type="hidden" name="salary_id" value="<? echo $this->salary_id; ?>">
    <input type="hidden" name="month" value="<? echo $this->salary->salary_month; ?>">
    <input type="hidden" name="year" value="<? echo $this->salary->salary_year; ?>">
    <input type="button" value="Submit (Alt + Z)" onclick="validateForm();">
    <input type="button" value="Cancel" onclick="history.go('-1')">
</form>