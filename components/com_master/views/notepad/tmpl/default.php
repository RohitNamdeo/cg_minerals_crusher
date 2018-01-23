<?php
    defined('_JEXEC') or die;
    //manage product 
?>

<script>
    j(function(){ 
        j( ".datepicker" ).datepicker({"dateFormat" : "dd-M-yy", changeMonth: true, changeYear: true});        
        j( "#notepad_form" ).dialog({
            autoOpen: false,
            height: 200,
            width: 380,
            modal: true,
            buttons: 
            {  
                "Submit (Alt+Z)": function() 
                { 
                    if(j("#notepad").val() == "")
                    {
                        alert("Please fill Notepad.");
                        return false;
                    }
                    if(j("#due_date").val() == "")
                    {
                        alert("Please Select Due Date.");
                        return false;
                    }
                    
                    j('#notepad_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_notepad&tmpl=xml&" + j("#notepadForm").serialize() + "&notepad_id=" + j("#notepad_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#notepad_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#notepad_form").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_notepad&tmpl=xml&" + j("#notepadForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#notepad_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#notepad_form").dialog( "close" );
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
     
    /*j(document).on("keydown", function(e){
        if(e.altKey && e.which == 65)
        {
            add_product();
        }
    });*/
    j(document).on("keyup", function(e){
        //if (e.keyCode == 13)
        if ((e.altKey && e.which == 90))
        {
           j('#submit_button').click();  
        }
    });
    
    function show_notepad()
    {   
        j("#mode").val("");
        j("#notepad_id").val("");
        
        j("#notepad").val("");
        
        j("#notepad_form").dialog("open");
        j("#notepad_form").dialog({"title":"Add Notepad"});
        j('#notepad_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_notepad(notepad_id)
    {   
        j("#notepad_id").val(notepad_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=notepad_details&tmpl=xml&notepad_id=" + notepad_id, function(data){
            notepad_details = j.parseJSON(data);
            
            j("#notepad").val(notepad_details.notepad);  
            j("#due_date").val(notepad_details.due_date);  
        });
                
        j("#notepad_form").dialog("open");
        j("#notepad_form").dialog({"title":"Edit Notepad"});
        j('#notepad_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    } 
     
    function delete_notepad(notepad_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_notepad&notepad_id=" + notepad_id);
         }
         else
         {
            return false;
         }
    } 
    
</script>
<h1>Notepads </h1>
<button type="button" onclick="show_notepad();">Notepad</button> 
<br /><br />                                                                                                                                                                        
<div id="notepad_list" style="width:400px;height:425px;overflow:scroll;">
    <table class="clean" id="notepads">
        <tr>
            <th width="20">S.No.</th>
            <th>Notepad</th>
            <th>Due Date</th>
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
            if(count($this->notepad_list) > 0)
            {
                $x = 0;
                foreach($this->notepad_list as $notepad_list)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $notepad_list->notepad; ?></td>
                        <td><? echo $notepad_list->due_date; ?></td>
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td class="noprint" align="center">
                                    <a onclick="edit_notepad(<? echo $notepad_list->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_notepad(<? echo $notepad_list->id; ?>);" class="delete">
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
<input type="hidden" name="notepad_id" id="notepad_id" value="" /> 

<div style="display: none;" id="notepad_form">
    <form method="post" id="notepadForm">
        <table class="">
            <tr>
                <td> Notepad :</td>
                <td><textarea name="notepad" id="notepad" style="width:260px;"></textarea></td>
            </tr>
            <tr>
                <td>Due Date :</td>
                <td><input type="text" name="due_date" id="due_date" class="datepicker" value="<?= $this->due_date; ?>" style="width : 260px;"/> </td>                           
            </tr>
        </table>
    </form>
</div>