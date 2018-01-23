<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){         
        j( "#customer_categories" ).dialog({
            autoOpen: false,
            height: 150,
            width: 390,
            modal: true,
            buttons: 
            {
                "Submit": function() 
                {    
                    if(j("#customer_category").val() == "")
                    {
                        alert("Please fill customer category.");
                        return false;
                    }
                    
                    j('#customer_categories').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_customer_category&tmpl=xml&" + j("#customerCategoriesForm").serialize() + "&customer_category_id=" + j("#customer_category_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#customer_categories').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#customer_categories").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_customer_category&tmpl=xml&" + j("#customerCategoriesForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#customer_categories').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#customer_categories").dialog( "close" );
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
    
     j(document).on("keypress","#customer_category", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
    j(document).on("keydown","#customer_category",function (e) {
        if ( e.ctrlKey || e.altKey) 
        {
            e.preventDefault();
        } 
        else
        {
            var key = e.keyCode;
            if (!((key == 8) ||(key == 9) ||(key == 32)||(key == 13) || (key >= 65 && key <= 90) )) 
            {
                e.preventDefault();
            }
        }  
    });

    function add_customer_category()
    {   
        j("#mode").val("");
        j("#customer_category_id").val("");
        j("#customer_category").val("");
        
        j("#customer_categories").dialog("open");
        j("#customer_categories").dialog({"title":"Add Customer Category"});
        j('#customer_categories').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_customer_category(customer_category_id)
    {   
        j("#customer_category_id").val(customer_category_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=customer_category_details&tmpl=xml&customer_category_id=" + customer_category_id, function(data){
            customer_category_details = j.parseJSON(data);
            
            j("#customer_category").val(customer_category_details.customer_category);  
        });
                
        j("#customer_categories").dialog("open");
        j("#customer_categories").dialog({"title":"Edit Customer Category"});
        j('#customer_categories').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
     
    function delete_customer_category(customer_category_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_customer_category&customer_category_id=" + customer_category_id);
         }
         else
         {
            return false;
         }
    }
</script>
<h1>Customer Categories</h1>
<input type="button" value="Add Customer Category" id="new_customer_categories" onclick="add_customer_category();">
<br /><br />
<div id="customer_categorieslist">
    <table class="clean">
        <tr>
            <th width="20">S.No.</th>
            <th>Customer Category</th>
            <?
                if(is_admin())
                {
                    ?><th>Action</th><?
                }
            ?>
        </tr>
        <?  
            if(count($this->customer_categories) > 0)
            {
                $x = 0;
                foreach($this->customer_categories as $customer_category)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $customer_category->customer_category; ?></td>
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td align="center">
                                    <a onclick="edit_customer_category(<? echo $customer_category->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_customer_category(<? echo $customer_category->id; ?>);" class="delete">
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
<input type="hidden" name="customer_category_id" id="customer_category_id" value="" /> 

<div style="display: none;" id="customer_categories">
    <form method="post" id="customerCategoriesForm">
        <table class="">
            <tr>
                <td>Customer Category :</td>
                <td><input type="text" id="customer_category" name="customer_category" size="27" />
            </tr>
        </table>
    </form>
</div>