<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){         
        j( "#expense_head_form" ).dialog({
            autoOpen: false,
            height: 150,
            width: 350,
            modal: true,
            buttons: 
            {
                "Submit (Alt+Z)": function() 
                { 
                    if(j("#expense_head").val() == "")
                    {
                        alert("Please fill expense head.");
                        return false;
                    }   
                    if(j("#vehicle_type").val() == "")
                    {
                        alert("Please fill Vehicle Type.");
                        return false;
                    }
                    
                    j('#expense_head_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_expense_head&tmpl=xml&" + j("#expenseheadForm").serialize() + "&expense_head_id=" + j("#expense_head_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#expense_head_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#expense_head_form").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_expense_head&tmpl=xml&" + j("#expenseheadForm").serialize(), function(data){
                            //alert(data);
                            if(data != "")
                            {
                                alert(data);
                                j('#expense_head_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#expense_head_form").dialog( "close" );
                                go(window.location); 
                            }
                        });
                    }
                },
                Cancel: function() {
                     j( this ).dialog( "close" );
                }
            },
        });
        j('button:contains(Submit)').attr("id","submit_button");
    });
    
     j(document).on("keypress","#expense_head", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
    j(document).on("keydown", function(e){
        if (e.altKey && e.which == 65)
        {
            add_expense_head();
        }
    });
    j(document).on("keyup", function(e){
        if (e.altKey && e.which == 90)
        {
           j('#submit_button').click();  
        }
    });

    function add_expense_head()
    {   
        j("#mode").val("");
        j("#expense_head_id").val("");
        
        j("#expense_head").val("");
        
        j("#expense_head_form").dialog("open");
        j("#expense_head_form").dialog({"title":"Add Expense Head"});
        j('#expense_head_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_expense(expense_head_id)
    {   
        j("#expense_head_id").val(expense_head_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=details_expense_head&tmpl=xml&expense_head_id=" + expense_head_id, function(data){
            details_expense_head = j.parseJSON(data);
            
            j("#expense_head").val(details_expense_head.expense_head);  
        });
                
        j("#expense_head_form").dialog("open");
        j("#expense_head_form").dialog({"title":"Edit Expense Head"});
        j('#expense_head_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    } 
     
    function delete_expense(expense_head_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_expense_head&expense_head_id=" + expense_head_id);
         }
         else
         {
            return false;
         }
    } 
    
</script>
<h1>Expense Head</h1>
<!--<input type="button" value="Add Expense Head" onclick="add_expense_head();">-->
<button type="button" onclick="add_expense_head();"><u>A</u>dd Expense Head</button> 
<br /><br />                                                                                                                                                                        
<div id="expensehead_list">
    <table class="clean" id="expense_heads" >
        <tr>
            <th width="20">S.No.</th>
            <th>Expense Head</th>
            <?
                if(is_admin())
                {
                    ?>    
                    <th class="noprint">Action</th>
                    <?
                }
            ?>
        </tr>
        <?  
            if(count($this->expense_heads) > 0)
            {
                $x = 0;
                foreach($this->expense_heads as $expense_heads)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $expense_heads->expense_head; ?></td>
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td class="noprint" align="center">
                                    <a onclick="edit_expense(<? echo $expense_heads->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_expense(<? echo $expense_heads->id; ?>);" class="delete">
                                </td>
                                <?
                            }
                        ?> 
                    </tr>
                    <?
                }
            }
        ?>
             
    </table>
</div>
<br />
<input type="hidden" name="mode" id="mode" value="" /> 
<input type="hidden" name="expense_head_id" id="expense_head_id" value="" /> 

<div style="display: none;" id="expense_head_form">
    <form method="post" id="expenseheadForm">
        <table class="">
            <tr>
                <td> Expense Head :</td>
                <td><input type="text" id="expense_head" name="expense_head" style="width:200px;"/></td>
            </tr>
        </table>
    </form>
</div>