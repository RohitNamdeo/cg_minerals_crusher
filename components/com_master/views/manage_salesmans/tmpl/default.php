<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){         
        j( "#salesman" ).dialog({
            autoOpen: false,
           // height: 150,
            width: 330,
            modal: true,
            buttons: 
            {
                "Submit": function() 
                {    
                    if(j("#salesman_name").val() == "")
                    {
                        alert("Please fill salesman name.");
                        return false;
                    }
                    
                    j('#salesman').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_salesman&tmpl=xml&" + j("#salesmanForm").serialize() + "&sm_id=" + j("#salesman_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#salesman').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#salesman").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_salesman&tmpl=xml&" + j("#salesmanForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#salesman').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#salesman").dialog( "close" );
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

    function add_salesman()
    {   
        j("#mode").val("");
        j("#salesman_id").val("");
        j("#salesman_name").val("");
        
        j("#salesman").dialog("open");
        j("#salesman").dialog({"title":"Add Salesman"});
        j('#salesman').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_salesman(salesman_id)
    {   
        j("#salesman_id").val(salesman_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=salesman_details&tmpl=xml&sm_id=" + salesman_id, function(data){
            salesman_details = j.parseJSON(data);
            j("#salesman_name").val(salesman_details.salesman_name);
        });                                          
                
        j("#salesman").dialog("open");
        j("#salesman").dialog({"title":"Edit Salesman"});
        j('#salesman').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
     
    function delete_salesman(salesman_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_salesman&sm_id=" + salesman_id);
         }
         else
         {
            return false;
         }
    }
</script>
<h1>Salesmans</h1>
<input type="button" value="Add New" id="new_salesman" onclick="add_salesman();"> 
<!--<br /><br />
<a href="#" onclick="tableToExcel('cities', 'Export.xls'); return false;" class="export"><img src="custom/graphics/icons/blank.gif" class="spreadsheet"></a>
<a href="#" id="printSalesmanList" onclick="popup_print('<h1>Cities</h1><br />' + j('#salesmanlist').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a>-->
<br /><br />                                                                                                                                                                        
<div id="salesmanlist">
    <table class="clean" width="500" id="cities">
        <tr>
            <th width="20">S.No.</th>
            <th>Name</th>
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
            if(count($this->salesmans) > 0)
            {
                $state_id = 0;
                $x = 0;
                foreach($this->salesmans as $salesman)
                {   
                    ?>
                        <tr>
                            <td align="center">
                               <? echo ++$x; ?> 
                            </td>
                            <td>
                                <? echo $salesman->salesman_name; ?>
                            </td> 
                            <?
                                if(is_admin())
                                {
                                    ?>    
                                        <td class="noprint" align="center">
                                            <a onclick="edit_salesman(<? echo $salesman->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                            <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_salesman(<? echo $salesman->id; ?>);" class="delete">
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
<input type="hidden" name="salesman_id" id="salesman_id" value="" /> 

<div style="display: none;" id="salesman">
    <form method="post" id="salesmanForm">
        <table class="">
            <tr>
                <td>Name :</td>
                <td><input type="text" id="salesman_name" name="salesman_name" size="27" />
            </tr>
        </table>
    </form>
</div>