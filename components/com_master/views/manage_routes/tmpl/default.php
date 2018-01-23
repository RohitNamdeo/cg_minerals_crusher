<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){         
        j( "#route" ).dialog({
            autoOpen: false,
           // height: 150,
            width: 330,
            modal: true,
            buttons: 
            {
                "Submit": function() 
                {    
                    if(j("#route_name").val() == "")
                    {
                        alert("Please fill route name.");
                        return false;
                    }
                    
                    j('#route').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_route&tmpl=xml&" + j("#routeForm").serialize() + "&route_id=" + j("#route_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#route').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#route").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_route&tmpl=xml&" + j("#routeForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#route').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#route").dialog( "close" );
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

    function add_route()
    {   
        j("#mode").val("");
        j("#route_id").val("");
        j("#route_name").val("");
        
        j("#route").dialog("open");
        j("#route").dialog({"title":"Add route"});
        j('#route').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_route(route_id)
    {   
        j("#route_id").val(route_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=route_details&tmpl=xml&route_id=" + route_id, function(data){
            route_details = j.parseJSON(data);
            j("#route_name").val(route_details.route_name);
        });                                          
                
        j("#route").dialog("open");
        j("#route").dialog({"title":"Edit Route"});
        j('#route').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
     
    function delete_route(route_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_route&route_id=" + route_id);
         }
         else
         {
            return false;
         }
    }
</script>
<h1>Routes</h1>
<input type="button" value="Add Route" id="new_route" onclick="add_route();"> 
<!--<br /><br />
<a href="#" onclick="tableToExcel('cities', 'Export.xls'); return false;" class="export"><img src="custom/graphics/icons/blank.gif" class="spreadsheet"></a>
<a href="#" id="printrouteList" onclick="popup_print('<h1>Cities</h1><br />' + j('#routelist').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a>-->
<br /><br />                                                                                                                                                                        
<div id="routelist">
    <table class="clean" width="500" id="cities">
        <tr>
            <th width="20">S.No.</th>
            <th>Route</th>
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
            if(count($this->routes) > 0)
            {
                $state_id = 0;
                $x = 0;
                foreach($this->routes as $route)
                {   
                    ?>
                        <tr>
                            <td align="center">
                               <? echo ++$x; ?> 
                            </td>
                            <td>
                                <? echo $route->route_name; ?>
                            </td> 
                            <?
                                if(is_admin())
                                {
                                    ?>    
                                        <td class="noprint" align="center">
                                            <a onclick="edit_route(<? echo $route->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                            <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_route(<? echo $route->id; ?>);" class="delete">
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
<input type="hidden" name="route_id" id="route_id" value="" /> 

<div style="display: none;" id="route">
    <form method="post" id="routeForm">
        <table class="">
            <tr>
                <td>Route Name :</td>
                <td><input type="text" id="route_name" name="route_name" size="27" />
            </tr>
        </table>
    </form>
</div>