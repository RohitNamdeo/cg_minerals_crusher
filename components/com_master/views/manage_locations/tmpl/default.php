<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){         
        j( "#location" ).dialog({
            autoOpen: false,
            height: 150,
            width: 330,
            modal: true,
            buttons: 
            {
                "Submit": function() 
                {    
                    if(j("#location_name").val() == "")
                    {
                        alert("Please fill location.");
                        return false;
                    }
                    j('#location').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_location&tmpl=xml&" + j("#locationForm").serialize() + "&location_id=" + j("#location_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#location').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#location").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_location&tmpl=xml&" + j("#locationForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#location').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#location").dialog( "close" );
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
    j(document).on("keypress","#location_name", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });

    function add_location()
    {   
        j("#mode").val("");
        j("#location_id").val("");
        j("#location_name").val("");
        
        j("#location").dialog("open");
        j("#location").dialog({"title":"Add Location"});
        j('#location').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_location(location_id)
    {   
        j("#location_id").val(location_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=location_details&tmpl=xml&location_id=" + location_id, function(data){
            location_details = j.parseJSON(data);
            
            j("#location_name").val(location_details.location_name);  
        });
                
        j("#location").dialog("open");
        j("#location").dialog({"title":"Edit Location"});
        j('#location').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
     
    function delete_location(location_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_location&location_id=" + location_id);
         }
         else
         {
            return false;
         }
    }
</script>
<h1>Locations</h1>
<input type="button" value="Add Location" onclick="add_location();"> 
<br /><br />                                                                                                                                                                        
<div id="locationlist">
    <table class="clean" id="locations">
        <tr>
            <th width="20">S.No.</th>
            <th>Location</th>
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
            if(count($this->locations) > 0)
            {
                $x = 0;
                foreach($this->locations as $location)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $location->location_name; ?></td>
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td class="noprint" align="center">
                                    <a onclick="edit_location(<? echo $location->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_location(<? echo $location->id; ?>);" class="delete">
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
<input type="hidden" name="location_id" id="location_id" value="" /> 

<div style="display: none;" id="location">
    <form method="post" id="locationForm">
        <table class="">
            <tr>
                <td>Location Name :</td>
                <td><input type="text" id="location_name" name="location_name" />
            </tr>
        </table>
    </form>
</div>