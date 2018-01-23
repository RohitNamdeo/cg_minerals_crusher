<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){         
        j( "#customer_segment_dialog" ).dialog({
            autoOpen: false,
            height: 150,
            width: 330,
            modal: true,
            buttons: 
            {
                "Submit": function() 
                {    
                    if(j("#customer_segment").val() == "")
                    {
                        alert("Please fill customer segment.");
                        return false;
                    }
                    
                    j('#customer_segment_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_customer_segment&tmpl=xml&" + j("#customer_segment_form").serialize() + "&customer_segment_id=" + j("#customer_segment_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#customer_segment_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#customer_segment_dialog").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_customer_segment&tmpl=xml&" + j("#customer_segment_form").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#customer_segment_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#customer_segment_dialog").dialog( "close" );
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
    
     j(document).on("keypress","#customer_segment", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
    
    j(document).on("keydown","#customer_segment",function (e) {
        if ( e.ctrlKey || e.altKey) 
        {
            e.preventDefault();
        } 
        else
        {
            var key = e.keyCode;
            if (!((key == 8) ||(key == 9) || (key == 32)||(key == 13) || (key >= 65 && key <= 90) )) 
            {
                e.preventDefault();
            }
        }  
    });

    function add_customer_segment()
    {   
        j("#mode").val("");
        j("#customer_segment_id").val("");
        j("#customer_segment").val("");
        
        j("#customer_segment_dialog").dialog("open");
        j("#customer_segment_dialog").dialog({"title":"Add Customer Segment"});
        j('#customer_segment_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_customer_segment(customer_segment_id)
    {   
        j("#customer_segment_id").val(customer_segment_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=customer_segment_details&tmpl=xml&customer_segment_id=" + customer_segment_id, function(data){
            details = j.parseJSON(data);
            j("#customer_segment").val(details.customer_segment);  
        });
                
        j("#customer_segment_dialog").dialog("open");
        j("#customer_segment_dialog").dialog({"title":"Edit Customer Segment"});
        j('#customer_segment_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
     
    function delete_customer_segment(customer_segment_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_customer_segment&customer_segment_id=" + customer_segment_id);
         }
         else
         {
            return false;
         }
    }
</script>
<h1>Customer Segments</h1>
<input type="button" value="Add Customer Segment" onclick="add_customer_segment();"> 
<br /><br />                                                                                                                                                                        
<div id="customer_segment_list">
    <table class="clean" id="customer_segments">
        <tr>
            <th width="20">#</th>
            <th>Customer Segment</th>
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
            if(count($this->customer_segments) > 0)
            {
                $x = 0;
                foreach($this->customer_segments as $customer_segment)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $customer_segment->customer_segment; ?></td>
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td class="noprint" align="center">
                                    <a onclick="edit_customer_segment(<? echo $customer_segment->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_customer_segment(<? echo $customer_segment->id; ?>);" class="delete">
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
<input type="hidden" name="customer_segment_id" id="customer_segment_id" value="" /> 

<div style="display: none;" id="customer_segment_dialog">
    <form method="post" id="customer_segment_form">
        <table class="">
            <tr>
                <td>Customer Segment :</td>
                <td><input type="text" id="customer_segment" name="customer_segment" />
            </tr>
        </table>
    </form>
</div>