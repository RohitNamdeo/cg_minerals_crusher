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
        j("#expense_date").datepicker({
            "dateFormat" : "dd-M-yy",
             changeMonth: true,
             changeYear: true
        });
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
    
    function validateForm()
    {   
        if(j("#expense_date").val() == "")
        {
            alert("Select expense date."); return false;
        }
        if(j("#amount").val() == "" || j("#amount").val() == 0)
        {
            alert("Enter valid amount."); return false;
        }
        if(j("#description").val() == "")
        {
            alert("Enter description."); return false;
        }
        
        j("#cash_expense_form").submit();
    }
</script>
<h1>Cash Expense Entry</h1>
<form id="cash_expense_form" action="index.php?option=com_amittrading&task=save_cash_expense_entry" method="post">
    <table class="clean">
        <tr>
            <td>Expense Date</td>
            <td><input type="text" id="expense_date" name="expense_date" value="<? echo date("d-M-Y"); ?>"></td>
        </tr>
        <tr>
            <td>Expense Head</td>
            <td>
                <select name="expense_head_id" id="expense_head_id">
                    <option></option>
                    <?
                        foreach($this->expense_heads as $head)
                        {
                            echo "<option value='" . intval($head->id) . "'>" . $head->expense_head . "</option>";
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Amount</td>
            <td><input type="text" id="amount" name="amount"></td>
        </tr>
        <tr>
            <td>Description</td>
            <td><input type="text" id="description" name="description"></td>
        </tr>
    </table>
    <br />
    <input type="button" value="Submit (Alt + Z)" onclick="validateForm(); return false;">
    <input type="button" value="Cancel" onclick="history.go('-1');">
</form>