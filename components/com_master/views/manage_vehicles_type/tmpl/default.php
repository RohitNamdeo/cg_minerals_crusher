<?php
    defined('_JEXEC') or die;
    //vehicle type 
?>

<script>
    j(function(){         
        j( "#vehicle_type_form" ).dialog({
            autoOpen: false,
            height: 150,
            width: 350,
            modal: true,
            buttons: 
            {  
                "Submit (Alt+Z)": function() 
                { 
                    if(j("#vehicle_type").val() == "")
                    {
                        alert("Please fill Vehicle Type.");
                        return false;
                    }
                    
                    j('#vehicle_type_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_vehicle_type&tmpl=xml&" + j("#vehicletypeForm").serialize() + "&vehicle_type_id=" + j("#vehicle_type_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#vehicle_type_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#vehicle_type_form").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_vehicle_type&tmpl=xml&" + j("#vehicletypeForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#vehicle_type_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#vehicle_type_form").dialog( "close" );
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
    
    j(document).on("keypress","#vehicle_type",function (e) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str) || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 32 || e.keyCode == 13)   
        {
            return true;
        }
        e.preventDefault();
        return false;
    });
     
    j(document).on("keydown", function(e){
        if(e.altKey && e.which == 65)
        {
            add_vehicle_type();
        }
    });
    j(document).on("keyup", function(e){
        //if (e.keyCode == 13)
        if ((e.altKey && e.which == 90))
        {
           j('#submit_button').click();  
        }
    });
    
    function add_vehicle_type()
    {   
        j("#mode").val("");
        j("#vehicle_type_id").val("");
        
        j("#vehicle_type").val("");
        
        j("#vehicle_type_form").dialog("open");
        j("#vehicle_type_form").dialog({"title":"Add Vehicle Type"});
        j('#vehicle_type_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_vehicle_type(vehicle_type_id)
    {   
        j("#vehicle_type_id").val(vehicle_type_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=vehicle_type_details&tmpl=xml&vehicle_type_id=" + vehicle_type_id, function(data){
            vehicle_type_details = j.parseJSON(data);
            
            j("#vehicle_type").val(vehicle_type_details.vehicle_type);  
        });
                
        j("#vehicle_type_form").dialog("open");
        j("#vehicle_type_form").dialog({"title":"Edit Vehicle Type"});
        j('#vehicle_type_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    } 
     
    function delete_vehicle_type(vehicle_type_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_vehicle_type&vehicle_type_id=" + vehicle_type_id);
         }
         else
         {
            return false;
         }
    } 
    
</script>
<h1>Manage Vehicle Type</h1>
<!--<input type="button" value="Add Vehicle Type" onclick="add_vehicle_type();">-->
<button type="button" onclick="add_vehicle_type();"><u>A</u>dd Vehicle Type</button> 
<br /><br />                                                                                                                                                                        
<div id="vehicletype_list">
    <table class="clean" id="vehicles_type">
        <tr>
            <th width="20">S.No.</th>
            <th>Vehicle Type</th>
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
            if(count($this->vehicles_type) > 0)
            {
                $x = 0;
                foreach($this->vehicles_type as $vehicles_type)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $vehicles_type->vehicle_type; ?></td>
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td class="noprint" align="center">
                                    <a onclick="edit_vehicle_type(<? echo $vehicles_type->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_vehicle_type(<? echo $vehicles_type->id; ?>);" class="delete">
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
<input type="hidden" name="vehicle_type_id" id="vehicle_type_id" value="" /> 

<div style="display: none;" id="vehicle_type_form">
    <form method="post" id="vehicletypeForm">
        <table class="">
            <tr>
                <td> Vehicle Type :</td>
                <td><input type="text" id="vehicle_type" name="vehicle_type" style="width:200px;"/></td>
            </tr>
        </table>
    </form>
</div>