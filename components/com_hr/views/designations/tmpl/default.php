<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        j( "#designation" ).dialog({
            autoOpen: false,
            height: 150,
            width: 450,
            modal: true,
            buttons: 
            {
                "Submit": function()
                {    
                    if(j("#designation_name").val() == "")
                    {
                        alert("Enter designation name.");
                        return false;
                    }
                    
                    j('#designation').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_hr&task=update_designation&tmpl=xml&" + j("#designationForm").serialize() + "&designation_id=" + j("#designation_id").val(), function(data){
                           if(data == "ok")
                           {
                               j("#designation").dialog( "close" );
                               go(window.location);
                           }
                           else
                           {
                               alert(data);
                               j('#designation').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                           }
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_hr&task=create_designation&tmpl=xml&" + j("#designationForm").serialize(), function(data){
                            if(data != "")
                            { 
                                alert(data);
                                j('#designation').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            { 
                                j("#designation").dialog( "close" );
                                go(window.location); 
                            }
                        });
                    }
                },
                Cancel: function() {
                     j( this ).dialog( "close" );
                }
            }
        });
    });

    function add_designation()
    {   
        j("#mode").val("");
        j("#designation_id").val("");
        j("#designation_name").val("");
        
        j("#designation").dialog("open");
        j("#designation").dialog({"title":"Add Designation"});
        j('#designation').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_designation(designation_id)
    {   
        j("#designation_id").val(designation_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_hr&task=designation_details&tmpl=xml&designation_id=" + designation_id, function(data){
            designation_name = j.parseJSON(data);
            
            j("#designation_name").val(designation_name);
        });
                
        j("#designation").dialog("open");
        j("#designation").dialog({"title":"Edit Designation"});
        j('#designation').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function delete_designation(designation_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_hr&task=delete_designation&designation_id=" + designation_id);
         }
         else
         {
            return false;
         }
    }
</script>
<h1>Designations</h1>
<br />
<input type="button" value="New Designation" onclick="add_designation();">
<br /><br />
<div>
    <table class="clean centreheadings floatheader">
        <tr>
            <th width="20">#</th>
            <th>Designation Name</th>
            <?
                if(is_admin())
                {
                    ?><th>Action</th><?
                }
            ?>
        </tr>
        <?  
            if(count($this->designations) > 0)
            {
                $x = 0;
                foreach($this->designations as $designation)
                {
                    ?>
                        <tr>
                            <td align="center"><? echo ++$x; ?></td>
                            <td><? echo $designation->designation_name; ?></td>
                            <?
                                if(is_admin())
                                {
                                    ?>
                                    <td align="center">
                                        <a href="#" onclick="edit_designation(<? echo $designation->id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                        <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="return delete_designation(<? echo $designation->id; ?>);" class="delete">
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
<input type="hidden" name="designation_id" id="designation_id" value="" />

<div style="display: none;" id="designation">
    <form method="post" id="designationForm">
        <table>
            <tr>
                <td>Designation Name</td>
                <td>&nbsp;:&nbsp;</td>
                <td>
                    <input type="text" id="designation_name" name="designation_name" style="width:270px;" />
                </td>
            </tr>
        </table>
    </form>
</div>