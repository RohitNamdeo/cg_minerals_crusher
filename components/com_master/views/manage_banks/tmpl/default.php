<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){         
        j( "#bank" ).dialog({
            autoOpen: false,
            modal: true,
            buttons: 
            {
                "Submit": function() 
                {    
                    if(j("#bank_name").val() == "")
                    {
                        alert("Please fill name of the bank.");
                        return false;
                    }    
                    
                    j('#bank').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");          
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_bank&tmpl=xml&" + j("#bankForm").serialize() + "&bank_id=" + j("#bank_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#bank').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j('#bank').dialog( "close" );
                                go(window.location);
                            }
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_bank&tmpl=xml&" + j("#bankForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#bank').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j('#bank').dialog( "close" );
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
    });
    
     j(document).on("keypress","#bank_name", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
    j(document).on("keydown","#bank_name",function (e) {
        if ( e.ctrlKey || e.altKey) 
        {
            e.preventDefault();
        } 
        else
        {
            var key = e.keyCode;
            if (!((key == 8) ||(key == 9) ||(key == 32)||(key == 13) || (key >= 65 && key <= 90) )) 
            {
                e.preventDefault();
            }
        }  
    });

    function add_bank()
    {   
        j("#mode").val("");
        j("#bank_id").val("");
        j("#bank_name").val("");
        
        j("#bank").dialog("open");
        j("#bank").dialog({"title":"Add Bank"});
        j('#bank').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_bank(bank_id)
    {   
        j("#bank_id").val(bank_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=bank_details&tmpl=xml&bank_id=" + bank_id, function(data){
            bank_details = j.parseJSON(data);
            
            j("#bank_name").val(bank_details.bank_name);
        });
                
        j("#bank").dialog("open");
        j("#bank").dialog({"title":"Edit Bank"});
        j('#bank').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
     
    function delete_bank(bank_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_bank&bank_id=" + bank_id);
         }
         else
         {
            return false;
         }
    }
</script>
<h1>Banks</h1>
<br />
<input type="button" value="Add Bank" id="new_bank" onclick="add_bank();"> 
<br /><br /> 
<div id="banklist">
    <table class="clean" id="banks">
        <tr>
            <th width="20">S.No.</th>
            <th>Bank Name</th>
            <?
                if(is_admin())
                {
                    ?><th>Action</th><?
                }
            ?>
        </tr>
        <?  
            if(count($this->banks) > 0)
            {
                $x = 0;
                foreach($this->banks as $bank)
                {
                    ?>
                        <tr>
                            <td align="center"><? echo ++$x; ?></td>
                            <td><? echo $bank->bank_name; ?></td>
                            <?
                                if(is_admin())
                                {
                                    ?>    
                                    <td align="center">
                                        <a onclick="edit_bank(<? echo $bank->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                        <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_bank(<? echo $bank->id; ?>);" class="delete">
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
<input type="hidden" name="bank_id" id="bank_id" value="" /> 

<div style="display: none;" id="bank">
    <form method="post" id="bankForm">
        <table class="">
            <tr>
                <td>Bank Name :</td>
                <td><input type="text" id="bank_name" name="bank_name" /></td>
            </tr> 
        </table>
    </form>
</div>