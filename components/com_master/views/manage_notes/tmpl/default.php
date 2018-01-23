<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){
        j("#date_of_note").datepicker({"dateFormat": "dd-M-yy"});
        
        j(".scrollIntoView").scrollIntoView({
            rowSelector : 'note',
        });
                 
        j( "#notes" ).dialog({
            autoOpen: false,
            height: 270,
            width: 390,
            modal: true,
            buttons: 
            {
                "Submit": function() 
                {    
                    if(j("#date_of_note").val() == "")
                    {
                        alert("Please select date.");
                        return false;
                    }
                    
                    if(j("#note").val() == "")
                    {
                        alert("Please enter note.");
                        return false;
                    }
                    
                    if(!j("input[name='note_type']").is(":checked"))
                    {
                        alert("Please mention note type.");
                        return false;
                    }
                    
                    j('#notes').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    j.get("index.php?option=com_master&task=save_note&tmpl=xml&" + j("#notesForm").serialize(), function(data){
                        if(data != "")
                        {
                            alert(data);
                            j('#notes').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                        }
                        else
                        {
                            j("#notes").dialog( "close" );
                            go(window.location); 
                        }
                    });
                },
                Cancel: function() {
                     j( this ).dialog( "close" );
                }
            },
        });
    });
    
     j(document).on("keypress","#note", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
    
    j(document).on("change", "#check_all", function(){
        if(j(this).is(":checked"))
        {
            j(".note_checkbox").attr("checked", true);
        }
        else
        {
            j(".note_checkbox").attr("checked", false);
        }
    });
    
    j(document).on("change", ".note_checkbox", function(){
        if(j(".note_checkbox:checked").length == j(".note_checkbox").length)
        {
            j("#check_all").attr("checked", true);
        }
        else
        {
            j("#check_all").attr("checked", false);
        }
    });

    function add_note()
    {   
        j("#note").val("");
        j("input[name='note_type']").attr("checked" , false);
        j("#date_of_note").val(j("#current_date").val());
        
        j("#notes").dialog("open");
        j("#date_of_note").datepicker("hide");
        j("#note").focus();
        j("#notes").dialog({"title":"Add Note"});
        j('#notes').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function delete_note(note_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_note&note_id=" + note_id);
         }
         else
         {
            return false;
         }
    }
    
    function print_notes()
    {
        var note_ids = new Array();
        
        if(j(".note_checkbox:checked").length == 0)
        {
            alert("Please select note(s) for printing."); return false;
        }
        else
        {
            j(".note_checkbox:checked").each(function(){
                note_ids.push(j(this).val());
            });
            
            window.open("index.php?option=com_master&view=print_notes&tmpl=print&n_ids=" + btoa(note_ids));
        }
    }
</script>
<h1>Notes</h1>
<input type="button" value="Add Note" onclick="add_note();">
<input type="hidden" id="current_date" value="<? echo date("d-M-Y"); ?>">
<br /><br />
<?
    if(count($this->notes) > 0)
    {
        ?>
        <a href="#" onclick="print_notes(); return false;"><img src="custom/graphics/icons/blank.gif" class="print" title="Print"></a>
        <br /><br />
        <?
    }
?>
<div>
    <table class="clean scrollIntoView">
        <tr>
            <th width="20">S.No.</th>
            <th><input type="checkbox" id="check_all"></th>
            <th>Date</th>
            <th>Note</th>
            <?
                if(is_admin())
                {
                    ?><th width="50">Action</th><?
                }
            ?>
        </tr>
        <?  
            if(count($this->notes) > 0)
            {
                $x = 0;
                $note_type = 0;
                foreach($this->notes as $note)
                {
                    if($note_type != $note->note_type)
                    {
                        ?>
                        <tr>
                            <th colspan="5" style="font-size:medium;"><? echo ($note->note_type == SPECIFIC ? "Specific Note" : "General Note"); ?></th>
                        </tr>
                        <?
                        $note_type = $note->note_type;
                    }
                    ?>
                    <tr class="note">
                        <td align="center" valign="top"><? echo ++$x; ?></td>
                        <td align="center"><input type="checkbox" class="note_checkbox" value="<? echo $note->id; ?>"></td>
                        <td align="center"><? echo ($note->date_of_note != '0000-00-00' ? date("d-M-Y", strtotime($note->date_of_note)) : ""); ?></td>
                        <td style="font-size:medium;"><? echo $note->note; ?></td>
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td align="center" valign="top">
                                    <?
                                        if($note->deleted == NO)
                                        {
                                            ?><img src="custom/graphics/icons/blank.gif" id="delete" title="Mark As Delete" onclick="delete_note(<? echo $note->id; ?>);" class="delete"><?
                                        }
                                    ?>
                                </td>
                                <?
                            }
                        ?> 
                    </tr>
                    <?
                }
            }
            else
            {
                ?>
                <tr>
                    <td colspan="5" align="center">No notes found.</td>
                </tr>
                <?
            }
        ?>
             
    </table>
</div>

<div style="display: none;" id="notes">
    <form method="post" id="notesForm">
        <table class="">
            <tr>
                <td>Date :</td>
                <td><input type="text" id="date_of_note" name="date_of_note" style="width:280px;" readonly="readonly" /></td>
            </tr>
            <tr>
                <td valign="top">Note :</td>
                <td><textarea id="note" name="note" style="resize:none; width:280px; height:100px;"></textarea></td>
            </tr>
            <tr>
                <td>Type :</td>
                <td>
                    <input type="radio" name="note_type" value="<? echo GENERAL; ?>">General
                    <input type="radio" name="note_type" value="<? echo SPECIFIC; ?>">Specific
                </td>
            </tr>
        </table>
    </form>
</div>