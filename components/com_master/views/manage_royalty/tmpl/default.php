<?php
    defined('_JEXEC') or die;
?>

<script>
    j(function(){         
        j( "#royalty_form" ).dialog({
            autoOpen: false,
            height: 150,
            width: 350,
            modal: true,
            buttons: 
            {  
                "Submit (Alt+Z)": function() 
                { 
                    if(j("#royalty_name").val() == "")
                    {
                        alert("Please fill Royalty Name.");
                        return false;
                    }
                    
                    j('#royalty_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_royalty&tmpl=xml&" + j("#royaltyForm").serialize() + "&royalty_id=" + j("#royalty_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#royalty_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#royalty_form").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_royalty&tmpl=xml&" + j("#royaltyForm").serialize(), function(data){
                            if(data != "")
                            {
                               alert(data);
                                j('#royalty_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#royalty_form").dialog( "close" );
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
    
     j(document).on("keypress","#royalty_name", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
    
    
    j(document).on("keydown","#royalty_name",function (e) {
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
     
    j(document).on("keydown", function(e){
        if(e.altKey && e.which == 65)
        {
            add_royalty();
        }
    });
    j(document).on("keyup", function(e){
        //if (e.keyCode == 13)
        if ((e.altKey && e.which == 90))
        {
           j('#submit_button').click();  
        }
    });
    
    function add_royalty()
    {   
        j("#mode").val("");
        j("#royalty_id").val("");
        
        j("#royalty_name").val("");
        
        j("#royalty_form").dialog("open");
        j("#royalty_form").dialog({"title":"Add Royalty"});
        j('#royalty_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_royalty(royalty_id)
    {   
        j("#royalty_id").val(royalty_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=royalty_details&tmpl=xml&royalty_id=" + royalty_id, function(data){
            royalty_details = j.parseJSON(data);
            
            j("#royalty_name").val(royalty_details.royalty_name);  
        });
                
        j("#royalty_form").dialog("open");
        j("#royalty_form").dialog({"title":"Edit Royalty"});
        j('#royalty_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    } 
     
    function delete_royalty(royalty_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_royalty&royalty_id=" + royalty_id);
         }
         else
         {
            return false;
         }
    }
    
</script>
<h1>Manage Royalty</h1>
<!--<input type="button" value="Add Vehicle Type" onclick="add_royalty_name();">-->
<button type="button" onclick="add_royalty();"><u>A</u>dd Royalty</button> 
<br /><br />                                                                                                                                                                        
<div id="royalty_list">
    <table class="clean" id="royalty">
        <tr>
            <th width="20">S.No.</th>
            <th>Royalty Name</th>
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
            if(count($this->royalties) > 0)
            {
                $x = 0;
                foreach($this->royalties as $royalty)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $royalty->royalty_name; ?></td>
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td class="noprint" align="center">
                                    <a onclick="edit_royalty(<? echo $royalty->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_royalty(<? echo $royalty->id; ?>);" class="delete">
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
<input type="hidden" name="royalty_id" id="royalty_id" value="" /> 

<div style="display: none;" id="royalty_form">
    <form method="post" id="royaltyForm">
        <table class="">
            <tr>
                <td>Royalty Name :</td>
                <td><input type="text" id="royalty_name" name="royalty_name" style="width:200px;"/></td>
            </tr>
        </table>
    </form>
</div>