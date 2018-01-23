<?php
    defined('_JEXEC') or die('Restricted access');
?>
<style>
    .category{
        cursor: pointer;
    }
</style>
<script>
    j(function(){
        j( "#category_dialog" ).dialog({
            autoOpen: false,
            height: 150,
            width: 450,
            modal: true,
            buttons: 
            {
                "Submit": function()
                {    
                    if(j("#category_name").val() == "")
                    {
                        alert("Enter category name.");
                        return false;
                    }
                    
                    j('#category_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_category&tmpl=xml&" + j("#category_Form").serialize() + "&category_id=" + j("#category_id").val(), function(data){
                           if(data == "ok")
                           {
                               j("#category_dialog").dialog( "close" );
                               go(window.location);
                           }
                           else
                           {
                               alert(data);
                               j('#category_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                           }
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=create_category&tmpl=xml&" + j("#category_Form").serialize(), function(data){
                            if(data != "")
                            { 
                                alert(data);
                                j('#category_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            { 
                                j("#category_dialog").dialog( "close" );
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
        
        j( "#item_dialog" ).dialog({
            autoOpen: false,
            width: 450,
            modal: true,
            buttons: 
            {
                "Submit": function()
                {    
                    item_category_id = j("#item_category_id").val();
                    
                    if(j("#item_name").val() == "")
                    {
                        alert("Enter item name.");
                        return false;
                    }
                    
                    if(j("#gst_percent").val() != "" && (j("#gst_percent").val() > 100 || isNaN(j("#gst_percent").val())))
                    {
                        alert("GST percent cannot be greater than 100%.");
                        return false;
                    }
                    
                    if(j("#piece_per_pack").val() == "" || j("#piece_per_pack").val() == 0)
                    {
                        alert("Enter piece/pack.");
                        return false;
                    }
                    
                    j('#item_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_item&tmpl=xml&" + j("#item_Form").serialize() + "&item_id=" + j("#item_id").val(), function(data){
                           if(data == "ok")
                           {
                               j("#item_dialog").dialog( "close" );
                               j("#category_" + item_category_id).click();
                           }
                           else
                           {
                               alert(data);
                               j('#item_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                           }
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=create_item&tmpl=xml&" + j("#item_Form").serialize(), function(data){
                            if(data != "")
                            { 
                                alert(data);
                                j('#item_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            { 
                                j("#item_dialog").dialog( "close" );
                                j("#category_" + item_category_id).click();
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
    
    j(document).delegate(".category", "click", function() {
        j(this).closest("table").find(".clickedRow").removeClass('clickedRow');
        j(this).addClass('clickedRow');
    });
    
    j(document).on("keypress",".opening_stock, #gst_percent, #last_purchase_rate, #sale_price1, #sale_price2",function(e){
        prevent_char(e.which,e);
    });
    
    j(document).on("keypress","#piece_per_pack",function(e){
        strict_numbers(e.which,e);
    });

    function add_category()
    {   
        j("#mode").val("");
        j("#category_id").val("");
        j("#category_name").val("");
        
        j("#category_dialog").dialog("open");
        j("#category_dialog").dialog({"title":"Add New Category"});
        j('#category_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_category(category_id)
    {   
        j("#category_id").val(category_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=category_details&tmpl=xml&category_id=" + category_id, function(data){
            category_name = j.parseJSON(data);
            
            j("#category_name").val(category_name);
        });
                
        j("#category_dialog").dialog("open");
        j("#category_dialog").dialog({"title":"Edit Category"});
        j('#category_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function delete_category(category_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_category&category_id=" + category_id);
         }
         else
         {
            return false;
         }
    }
    
    function get_items(category_id)
    {
        j.get("index.php?option=com_master&view=manage_items&tmpl=xml&category_id=" + category_id, function(data){
            if(data != "")
            {
                j("#merge_item_dialog").remove();
                j("#item_list").html("");
                j("#item_list").html(data); 
            }
            else
            {
                j("#item_list").html("");
            }
        });
    }
    
    function add_item(item_category_id, item_category_name)
    {   
        j("#mode").val("");
        j("#item_id").val("");
        j("#item_name").val("");
        j("#item_category_id").val(item_category_id);
        j("#item_category_name").val(item_category_name);
        j("#gst_percent").val("");
        j("#last_purchase_rate_row").show();
        j("#last_purchase_rate, #sale_price1, #sale_price2").val("");
        j("#piece_per_pack").val("");
        j(".opening_stock").val("");

        j("#item_dialog").dialog("open");
        j("#item_dialog").dialog({"title":"Add New Item"});
        j("#item_name").focus();
        j('#item_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function edit_item(item_id, item_category_id, item_category_name)
    {   
        j("#item_id").val(item_id);
        j("#item_category_id").val(item_category_id);
        j("#item_category_name").val(item_category_name);
        j(".opening_stock").val("");
        j("#last_purchase_rate_row").hide();
        j("#last_purchase_rate, #sale_price1, #sale_price2").val("");
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=item_details&tmpl=xml&item_id=" + item_id, function(data){
            item_name = j.parseJSON(data);
            
            j("#item_name").val(item_name.item_name);
            j("#hsn_code").val(item_name.hsn_code);
            j("#gst_percent option[value=" + parseFloat(item_name.gst_percent) + "]").attr("selected",true);
            j("#piece_per_pack").val(item_name.piece_per_pack);
            j("#sale_price1").val(item_name.sale_price1);
            j("#sale_price2").val(item_name.sale_price2);
            
            j.get("index.php?option=com_master&task=get_locationwise_items_opening_balance&tmpl=xml&item_id=" + item_id, function(data){
                opening_balances = j.parseJSON(data); 
                
                j.each(opening_balances,function(index, value){
                    var location_id = value.location_id;
                    j("#opening_stock" + location_id).val(value.opening_stock);
                });
            });
        });
                
        j("#item_dialog").dialog("open");
        j("#item_dialog").dialog({"title":"Edit Item"});
        j("#item_name").focus();
        j('#item_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function delete_item(item_id, item_category_id)
    {
         if(confirm("Are You Sure?"))
         {
            j.get("index.php?option=com_master&task=delete_item&tmpl=xml&item_id=" + item_id, function(data){
                alert(data);
                j("#category_" + item_category_id).click();
            });
         }
         else
         {
            return false;
         }
    }
    
    function view_inventory(item_id)
    {
        j.colorbox({href:"index.php?option=com_master&view=item_inventory_details&item_id=" + item_id + "&tmpl=xml", maxWidth: "100%", maxHeight: "100%", "open" : true});
        return false;
    }
</script>
<h1>Categories</h1>
<input type="button" value="New Category" onclick="add_category();">
<br /><br />
<div>
    <table>
        <tr>
            <td valign="top">
                <div style="overflow-x:hidden; overflow-y:scroll; display:inline-block; height:500px;">
                    <table class="clean centreheadings floatheader">
                        <tr>
                            <th width="20">#</th>
                            <th>Category Name</th>
                            <?
                                if(is_admin())
                                {
                                    ?><th>Action</th><?
                                }
                            ?>
                        </tr>
                        <?  
                            if(count($this->categories) > 0)
                            {
                                $x = 0;
                                foreach($this->categories as $category)
                                {
                                    ?>
                                        <tr class="category" id="category_<? echo $category->id; ?>" onclick="get_items(<? echo $category->id; ?>);">
                                            <td align="center"><? echo ++$x; ?></td>
                                            <td><? echo $category->category_name; ?></td>
                                            <?
                                                if(is_admin())
                                                {
                                                    ?>
                                                    <td align="center">
                                                        <a href="#" onclick="edit_category(<? echo $category->id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                                        <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="return delete_category(<? echo $category->id; ?>);" class="delete">
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
            </td>
            <td id="item_list" valign="top"></td>
        </tr>
    </table>
</div>
<br />
<input type="hidden" name="mode" id="mode" value="" />
<input type="hidden" name="category_id" id="category_id" value="" />

<div style="display: none;" id="category_dialog">
    <form method="post" id="category_Form">
        <table>
            <tr>
                <td>Category Name</td>
                <td>&nbsp;:&nbsp;</td>
                <td>
                    <input type="text" id="category_name" name="category_name" style="width:270px;" />
                </td>
            </tr>
        </table>
    </form>
</div>

<input type="hidden" name="mode" id="mode" value="" />
<input type="hidden" name="item_id" id="item_id" value="" />

<div style="display: none;" id="item_dialog">
    <form method="post" id="item_Form">
        <input type="hidden" name="item_category_id" id="item_category_id" />
        <table class="clean centreheadings">
            <tr>
                <td>Category Name</td>
                <td>
                    <input type="text" id="item_category_name" name="item_category_name" style="width:270px;" readonly="readonly" />
                </td>
            </tr>
            <tr>
                <td>Item Name</td>
                <td>
                    <input type="text" id="item_name" name="item_name" style="width:270px;" />
                </td>
            </tr>
            <tr>
                <td>HSN Code</td>
                <td>
                    <input type="text" id="hsn_code" name="hsn_code" style="width:270px;" />
                </td>
            </tr>
            <tr>
                <td>GST Percent</td>
                <td>
                    <!--<input type="text" id="vat_percent" name="vat_percent" style="width:270px;" />-->
                    <select id="gst_percent" name="gst_percent" style="width: 270px;">
                        <!--<option value="0">-Select-</option>-->
                        <option value="<?= GST_PERCENT_0; ?>">0</option>
                        <option value="<?= GST_PERCENT_5; ?>">5</option>
                        <option value="<?= GST_PERCENT_12; ?>">12</option>
                        <option value="<?= GST_PERCENT_18; ?>">18</option>
                        <option value="<?= GST_PERCENT_28; ?>">28</option>
                    </select>
                </td>
            </tr>
            <tr id="last_purchase_rate_row">
                <td>Last Purchase Rate</td>
                <td>
                    <input type="text" id="last_purchase_rate" name="last_purchase_rate" style="width:270px;" />
                </td>
            </tr>
            <tr>
                <td>Sale Price1</td>
                <td>
                    <input type="text" id="sale_price1" name="sale_price1" style="width:270px;" />
                </td>
            </tr>
            <tr>
                <td>Sale Price2</td>
                <td>
                    <input type="text" id="sale_price2" name="sale_price2" style="width:270px;" />
                </td>
            </tr>
            <tr>
                <td>Piece/pack</td>
                <td>
                    <input type="text" id="piece_per_pack" name="piece_per_pack" style="width:270px;" />
                </td>
            </tr>
            <?
                if(count($this->locations) > 0)
                {
                    ?>
                    <tr>
                        <th>Location</th>
                        <th>Opening Stock</th>
                    </tr>
                    <?
                        foreach($this->locations as $location)
                        {
                            ?>
                            <tr>
                                <td>
                                    <? echo $location->location_name; ?>
                                    <input type="hidden" class="location_id" name="location_ids[]" value="<? echo $location->id; ?>">
                                </td>
                                <td>
                                    <input type="text" class="opening_stock" id="opening_stock<? echo $location->id;?>" name="opening_stocks[]" style="width:270px;">
                                </td>
                            </tr>
                            <?
                        }
                    ?>
                    <?
                }
            ?>
        </table>
    </form>
</div>