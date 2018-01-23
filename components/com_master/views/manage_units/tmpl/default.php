<?php
    defined('_JEXEC') or die; 
?>
<script>          
    j(function(){         
        j( "#unit" ).dialog({
            autoOpen: false,
            height: 150,
            width: 330,
            modal: true,
            buttons: 
            {   
                "Submit": function() 
                {    
                    if(j("#unit_name").val() == "")
                    {
                        alert("Please fill unit.");
                        return false;
                    }
                     j('#unit').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");      
                   
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_unit&tmpl=xml&" + j("#unitForm").serialize() + "&unit_id=" + j("#unit_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#unit').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            } 
                            else{
                                j("#unit").dialog( "close" );
                                go(window.location);
                            }
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_unit&tmpl=xml&" + j("#unitForm").serialize(), function(data){
                            if(data != "")
                            {
                               alert(data);
                                j('#unit').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");  
                            }
                            else{
                                j("#unit").dialog( "close" );
                                go(window.location); 
                            }
                        });
                    }
                },
                Cancel: function() {
                     j(this).dialog( "close" );
                }
            },
        });
        j('button:contains(Submit)').attr("id","submit_button");   
    });
    
     j(document).on("keypress","#unit_name", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
    j(document).on("keydown","#unit_name",function (e) {
        if ( e.ctrlKey || e.altKey) 
        {
            e.preventDefault();
        } 
        else
        {
            var key = e.keyCode;
            if (!((key == 8) ||(key == 9) ||(key == 32)||(key == 13) || (key >= 48 && key <= 57) || (key >= 65 && key <= 90) )) 
            {
                e.preventDefault();
            }
        }  
    });
    
    function add_unit()
    {   
        j("#mode").val("");
        j("#unit_id").val("");
        j("#unit_name").val("");
        
        j("#unit").dialog("open");
        j("#unit").dialog({"title":"Add Unit"});
        j('#unit').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function edit_unit(unit_id)
    {   
        j("#unit_id").val(unit_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=unit_details&tmpl=xml&unit_id=" + unit_id, function(data){
            unit_details = j.parseJSON(data);
            
            j("#unit_name").val(unit_details.unit);
        });                                          
                
        j("#unit").dialog("open");
        j("#unit").dialog({"title":"Edit Unit"});
        j('#unit').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
     
    function delete_unit(unit_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_unit&unit_id=" + unit_id);
         }
         else
         {
            return false;
         }
    }
</script>
<h1>Units</h1>
<input type="button" value="Add Units" id="new_unit" onclick="add_unit();"> 
<!--<br /><br />
<a href="#" onclick="tableToExcel('cities', 'Export.xls'); return false;" class="export"><img src="custom/graphics/icons/blank.gif" class="spreadsheet"></a>
<a href="#" id="printCityList" onclick="popup_print('<h1>Cities</h1><br />' + j('#citylist').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a>-->
<br /><br />                                                                                                                                                                        
<div id="unitlist">
    <table class="clean" width="500" id="units">
        <tr>
            <th width="20">S.No.</th>
            <th>Unit Name</th>
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
            if(count($this->units) > 0)
            {
                $x = 0;
                foreach($this->units as $unit)
                {
                    ?>
                        <tr>
                            <td align="center">
                               <? echo ++$x; ?> 
                            </td>
                            <td>
                                <? echo $unit->unit; ?>
                            </td> 
                            <?
                                if(is_admin())
                                {
                                    ?>    
                                        <td class="noprint" align="center">
                                            <a onclick="edit_unit(<? echo $unit->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                            <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_unit(<? echo $unit->id; ?>);" class="delete">
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
</div> <br />
<input type="hidden" name="mode" id="mode" value="" /> 
<input type="hidden" name="unit_id" id="unit_id" value="" /> 

<div style="display: none;" id="unit">
    <form method="post" id="unitForm">
        <table class="">
            <tr>
                <td>Unit Name :</td>
                <td><input type="text" id="unit_name" name="unit_name" size="27" />
            </tr>
        </table>
    </form>
</div>

