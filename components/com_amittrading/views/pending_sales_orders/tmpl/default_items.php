<?php
    defined('_JEXEC') or die; 
?>
<style>
    tr.header td, tr.footer td{
        background-color: #EFEFEF;
        border: 1px solid #c0c0c0;
        font-weight: bold;
    }
</style>
<script>
//j(document).ready(function(){
//    alert("Fds");
//});
    j(document).on("change", "#check_all", function(){
        if(j(this).is(":checked"))
        {
            j(".item_id").attr("checked", true);
        }
        else
        {
            j(".item_id").attr("checked", false);
        }
    });
    
    j(document).on("change", ".item_id", function(){
        if(j(".item_id:checked").length == j(".item_id").length)
        {
            j("#check_all").attr("checked", true);
        }
        else
        {
            j("#check_all").attr("checked", false);
        }
    });
    
</script>
<h3>Items in Order No. <? echo $this->order_id; ?></h3>
<form id="pending_items" action="index.php?option=com_amittrading&view=sales_invoice" method="post">
    <input type="hidden" name="customer_id" value="<? echo $this->customer_id; ?>">
    <table class="clean centreheadings">
        <tr>
            <th>#</th>
            <th width="20"><input type="checkbox" id="check_all"></th>
            <th>Product</th>
            <th>Item Type</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Gross<br />Amount</th>
            <th>Gst %</th>
            <th>GST<br />Amount</th>
            <th>Total<br />Amount</th>
            <th width="200">Description</th>
            <th width="40">Action</th>
        </tr>
        <?        
            if(count($this->pending_sales_order_items) > 0)
            { 
                $x = 1;
                $total_amount = 0;
                foreach($this->pending_sales_order_items as $item) 
                {
                    ?>
                    <tr>
                        <td align="center"><? echo $x++; ?></td>
                        <td><input type="checkbox" name="item_ids[]" class="item_id" value="<? echo $item->id; ?>"></td>
                        <td><? echo $item->product_name; ?></td>
                        <?if($item->item_type == PRODUCT)
                        {
                            $item->item_type="Main Item";
                        }
                        else if($item->item_type == MIXING)
                        {
                            $item->item_type="Mixing Item";
                        }
                        ?>
                        <td><? echo $item->item_type; ?></td>
                        <td><? echo floatval($item->quantity); ?></td>
                        <td><? echo $item->product_rate;?></td>
                        <td><? echo intval($item->gross_amount); ?></td>
                         <td><? echo intval(($item->gst_percent));?></td> 
                        <td align="right"><? echo floatval(($item->gst_amount)); ?></td>
                        <td align="right"><? echo round_2dp($item->total_amount); $total_amount += floatval($item->total_amount); ?></td>
                        <td><? echo $item->product_note; ?></td>
                        <td align="center">
                            <a href="#" onclick="cancel_sales_order_items(<? echo $this->order_id; ?>, <? echo $item->id; ?>); return false;"><img src="custom/graphics/icons/cancel.png" title="Cancel Order"></a>
                        </td>
                    </tr>
                    <?
                }
                ?>
                <tr class="footer">
                    <td colspan="9" align="right">Total : </td>
                    <td align="right"><? echo round_2dp($total_amount); ?></td>
                    <td colspan="2"></td>
                </tr>
                <?
            }
            else
            {
                ?><td colspan="10" align="center">No items to display.</td><?
            }
        ?>
    </table>
    <input type="hidden" id="order_id" name="order_id" value="<? echo $this->order_id; ?>">
</form>