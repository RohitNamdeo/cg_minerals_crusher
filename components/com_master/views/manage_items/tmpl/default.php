<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        j("#new_item,#merge_item").button(); 
        
        j( "#merge_item_dialog" ).dialog({
            autoOpen: false,
            width: 410,
            modal: true,
            title:"Merge Items",
            buttons: 
            {
                "Submit": function()
                {    
                    var values = new Array();
                    var prefix = "";
                    item_ids = "";
                    j.each(j("input[name='items[]']:checked"), function() {
                        values.push(j(this).val());
                        item_ids += prefix + j(this).val();
                        prefix = ",";
                    });
                                        
                    var merge_to_item = j("#merge_to_item").val();
                    
                    if(j("input[name='items[]']:checked").length <= 1)
                    {
                        alert("Please select atleast two items for merge.");
                        return false;
                    }
                    if(j.inArray(merge_to_item,values) >= 0)
                    {
                        alert("Please select an item to which you want to merge. Can't select an item which is selected for merge, choose another item.");
                        return false;   
                    }

                    j.get("index.php?option=com_master&task=merge_items&tmpl=xml&merge_to_item=" + j("#merge_to_item").val() + "&location_from=" + j("#location_from").val() + "&location_to=" + j("#location_to").val() + "&items=" + btoa(item_ids) + "",function(data){
                         if(data == "ok")
                         {
                             alert("Items merged successfully.");
                         }
                    });
                    
                    j( this ).dialog( "close" );
                },
                Cancel: function() {
                     j( this ).dialog( "close" );
                }
            }
        });
    }); 
    
    function merge_items(item_category_id,item_category_name)
    {
        j("#merge_item_dialog").dialog("open");
    }
     
</script>
<h2>Items in <? echo $this->category_name; ?></h2>
<input type="button" value="New item" id="new_item" onclick="add_item(<? echo $this->category_id; ?>, '<? echo $this->category_name; ?>');">
<input type="button" value="Merge items" id="merge_item" onclick="merge_items(<? echo $this->category_id; ?>, '<? echo $this->category_name; ?>');">
<br /><br />
<div>
    <table class="clean centreheadings floatheader">
        <tr>
            <th width="20">#</th>
            <th>Item Name</th>
            <th>HSN Code</th>
            <th>GST %</th>
            <th>Last<br />Purchase Rt.</th>
            <th>Sale Price1</th>
            <th>Sale Price2</th>
            <th>Piece/pack</th>
            <?
                if(is_admin())
                {
                    ?><th>Action</th><?
                }
            ?>
        </tr>
        <?  
            if(count($this->items) > 0)
            {
                $x = 0;
                foreach($this->items as $item)
                {
                    ?>
                        <tr>
                            <td align="center"><? echo ++$x; ?></td>
                            <td><? echo $item->item_name; ?></td>
                            <td><? echo $item->hsn_code; ?></td>
                            <td><? echo $item->gst_percent; ?></td>
                            <td align="right"><? echo $item->last_purchase_rate; ?></td>
                            <td align="right"><? echo $item->sale_price1; ?></td>
                            <td align="right"><? echo $item->sale_price2; ?></td>
                            <td><? echo $item->piece_per_pack; ?></td>
                            <?
                                if(is_admin())
                                {
                                    ?>
                                    <td align="center">
                                        <a href="#" onclick="view_inventory(<? echo $item->id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" title="View Inventory" class="view"></a>
                                        <a href="#" onclick="edit_item(<? echo $item->id; ?>, <? echo $this->category_id; ?>, '<? echo $this->category_name; ?>'); return false;"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                        <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="return delete_item(<? echo $item->id; ?>, <? echo $this->category_id; ?>);" class="delete">
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

<div  id="merge_item_dialog" style="display:none;">
    <form method="post" id="mergeItemForm">
        <table class="clean spread centreheadings">
            <tr>    
                <th colspan="2">Choose Items For Merge</th>
            </tr>
            <tr>
                <td colspan="2">
                    <?
                        if(count($this->items) > 0)
                        {
                            foreach($this->items as $item)
                            {
                                ?>
                                    <li style="list-style-type:none;"><input type="checkbox" name="items[]" value="<? echo $item->id; ?>" /><? echo $item->item_name; ?></li>
                                <?
                            } 
                        }
                        else
                        {
                            echo "<center>No items to be merged.</center>";
                        }
                        
                    ?>
                </td>
            </tr>
            <tr>
                <td>Merge to item</td>
                <td>
                    <select name="merge_to_item" id="merge_to_item" style="width:200px;">
                        <option></option>
                        <?
                            foreach($this->items as $item)
                            {
                                ?>
                                    <option value="<? echo $item->id;?>"><? echo $item->item_name;?></option>
                                <?
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Location From</td>
                <td>
                    <select name="location_from" id="location_from" style="width:200px;">
                        <option></option>
                        <?
                            foreach($this->locations as $location)
                            {
                                ?>
                                    <option value="<? echo $location->id;?>"><? echo $location->location_name;?></option>
                                <?
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Location To</td>
                <td>
                    <select name="location_to" id="location_to" style="width:200px;">
                        <option></option>
                        <?
                            foreach($this->locations as $location)
                            {
                                ?>
                                    <option value="<? echo $location->id;?>"><? echo $location->location_name;?></option>
                                <?
                            }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
    </form>
</div>