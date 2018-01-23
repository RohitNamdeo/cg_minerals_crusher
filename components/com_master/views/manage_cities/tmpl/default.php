<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){         
        j( "#city" ).dialog({
            autoOpen: false,
            width: 330,
            modal: true,
            buttons: 
            {
                "Submit": function() 
                {    
                    if(j("#city_name").val() == "")
                    {
                        alert("Please fill city.");
                        return false;
                    }
                    if(j("#state_id").val() == 0)
                    {
                       alert("Please select state."); 
                       return false; 
                    }
                    
                    
                    j('#city').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_city&tmpl=xml&" + j("#cityForm").serialize() + "&city_id=" + j("#city_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#city').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#city").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_city&tmpl=xml&" + j("#cityForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#city').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#city").dialog( "close" );
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
    
     j(document).on("keypress","#city_name", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
    j(document).on("keydown","#city_name",function (e) {
        if ( e.ctrlKey || e.altKey) 
        {
            e.preventDefault();
        } 
        else
        {
            var key = e.keyCode;
            if (!((key == 8) ||(key == 9) ||(key == 32)||(key == 13) ||(key >= 37 && key <= 40)|| (key >= 65 && key <= 90) )) 
            {
                e.preventDefault();
            }
        }  
    });

    function add_city()
    {   
        j("#mode").val("");
        j("#city_id").val("");
        j("#city_name").val("");
        
        j("#city").dialog("open");
        j("#city").dialog({"title":"Add City"});
        j('#city').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_city(city_id)
    {   
        j("#city_id").val(city_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=city_details&tmpl=xml&city_id=" + city_id, function(data){
            city_details = j.parseJSON(data);
            
            j("#city_name").val(city_details.city);
            j("#state_id").val(city_details.state_id);  
        });                                          
                
        j("#city").dialog("open");
        j("#city").dialog({"title":"Edit City"});
        j('#city').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
     
    function delete_city(city_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_city&city_id=" + city_id);
         }
         else
         {
            return false;
         }
    }
</script>
<h1>Cities</h1>
<input type="button" value="Add City" id="new_city" onclick="add_city();"> 
<!--<br /><br />
<a href="#" onclick="tableToExcel('cities', 'Export.xls'); return false;" class="export"><img src="custom/graphics/icons/blank.gif" class="spreadsheet"></a>
<a href="#" id="printCityList" onclick="popup_print('<h1>Cities</h1><br />' + j('#citylist').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a>-->
<br /><br />                                                                                                                                                                        
<div id="citylist">
    <table class="clean" width="500" id="cities">
        <tr>
            <th width="20">S.No.</th>
            <th>City Name</th>
            <th>State Name</th>
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
            if(count($this->cities) > 0)
            {
                $state_id = 0;
                $x = 0;
                foreach($this->cities as $city)
                {
                    if($state_id != $city->state_id)
                    {
                        ?>
                            <tr>
                                <th colspan="5" style="text-align:left;">
                                    <?= $city->state_name; ?>
                                </th>
                            </tr>
                        <?
                        $state_id = $city->state_id;
                    }
                    ?>
                        <tr>
                            <td align="center">
                               <? echo ++$x; ?> 
                            </td>
                            <td>
                                <? echo $city->city; ?>
                            </td> 
                            <td>
                                <? echo $city->state_name; ?>
                            </td>
                            <?
                                if(is_admin())
                                {
                                    ?>    
                                        <td class="noprint" align="center">
                                            <a onclick="edit_city(<? echo $city->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                            <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_city(<? echo $city->id; ?>);" class="delete">
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
<input type="hidden" name="city_id" id="city_id" value="" /> 

<div style="display: none;" id="city">
    <form method="post" id="cityForm">
        <table class="">
            <tr>
                <td>City Name :</td>
                <td><input type="text" id="city_name" name="city_name" size="27" />
            </tr>
            <tr>
                <td>State : </td>
                <td>
                    <select name="state_id" id="state_id"> 
                        <option value="0"> - Select State - </option>
                        <?
                            foreach($this->states as $state)
                            {
                                echo "<option value='" . intval($state->id) . "'>" . $state->name . "</option>";
                            }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
    </form>
</div>