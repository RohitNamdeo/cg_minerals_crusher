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
    j(function(){
        //j("#from_date, #to_date").datepicker({"dateFormat" : "dd-M-yy"});
        j("#customer_id").chosen();
    });
    
    function show_sales_orders(validate)
    {
        if(validate)
        {
            //if(j("#customer_id").val() == 0 && j("#from_date").val() == "" && j("#to_date").val() == "")
            if(j("#customer_id").val() == 0)
            {
                alert("Select filter.");
                return false;
            }
            else
            {
                go("index.php?option=com_amittrading&view=pending_sales_orders&customer_id=" + j("#customer_id").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=pending_sales_orders");
        }
    }
    
    function view_sales_orders(d)
    {
        go("index.php?option=com_amittrading&view=pending_sales_orders&customer_id=" + j("#customer_id").val() + "&from_date=" + j("#from_date").val() + "&d=" + d);
    }
    
    function cancel_sales_order_items(order_id, order_item_id)
    {
        if(confirm("Are you sure?"))
        {
            j.get("index.php?option=com_amittrading&task=cancel_order_items&tmpl=xml&type=s&order_id=" + order_id + "&order_item_id=" + order_item_id, function(data){
                if(data == "ok")
                {
                    alert("Item cancelled successfully.");
                    go(window.location);
                }
                else
                {
                    alert("Unable to cancel item. Please try again.");
                }
            });
        }
        else
        {
            return false;
        }
    }
    
    function delete_sales_order(order_id)
    {
        if(confirm("Are you sure?"))
        {
            go("index.php?option=com_amittrading&task=delete_sales_order&order_id=" + order_id)
        }
        else
        {
            return false;
        }
    }
    
    function validateForm()
    {
        if(j(".item_id:checked").length)
        {
            order_no = 0;
            items_accepted = 1;
            j(".item_id:checked").each(function(){
                if(order_no == 0)
                {
                    order_no = j(this).attr("order_no");
                }
                else
                {
                    if(order_no != j(this).attr("order_no"))
                    {
                        items_accepted = 0;
                        return false;
                    }
                }
            });
            
            if(items_accepted)
            {
                j("input[name='customer_id']").val(j(".item_id:checked:first").attr("customer_id"));
                j("#pending_items").submit();
            }
            else
            {
                alert("Selected items must be of same order.");
                return false;
            }
        }
        else
        {
            alert("At least 1 item should be selected to create invoice.");
            return false;
        }
    }
</script>
<h1>Pending Sales Orders</h1>
<input type="button" value="Create Sales Order" onclick="go('index.php?option=com_amittrading&view=add_sales_order');">
<?
    if(count($this->pending_sales_order_items) > 0)
    {
        ?><input type="button" value="Create Sales Invoice" onclick="validateForm(); return false;"><?
    }
?>
<br /><br />
<table>
    <tr>
        <td>Customer : </td>
        <td>
            <select id="customer_id" style="width:250px;">
                <option value="0"></option>
                <?
                    if(count($this->customers) > 0)
                    {
                        foreach($this->customers as $customer)
                        {
                            ?><option value="<? echo $customer->id; ?>" <? echo ($this->customer_id == $customer->id ? "selected='selected'" : ""); ?> ><? echo $customer->customer_name; ?></option><?
                        }
                    }
                ?>
            </select>
        </td>
        <!--<td>From Date : </td>
        <td>
            <button onclick="view_sales_orders('p');"><b>&lt; Previous</b></button>
            <input type="text" id="from_date" value="<? //echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : "") ?>" readonly="readonly" style="width:80px;">
            <button onclick="view_sales_orders('n');"><b>&gt; Next</b></button>
        </td>
        <td>To Date : </td>
        <td><input type="text" id="to_date" value="<? //echo ($this->to_date != "" ? date("d-M-Y", strtotime($this->to_date)) : "") ?>" style="width:80px;"></td>-->
        <td>
            <input type="button" value="Refresh" onclick="show_sales_orders(1); return false;">
            <input type="button" value="Clear" onclick="show_sales_orders(0);">
        </td>
    </tr>
</table>
<br />
<form id="pending_items" action="index.php?option=com_amittrading&view=sales_invoice" method="post">
    <input type="hidden" name="customer_id">
    <table class="clean centreheadings floatheader" width="80%">
        <tr>
            <th>#</th>
            <th width="20"></th>
            <th>Category</th>
            <th>Item</th>
            <th>Box/<br />Pack</th>
            <th>Pieces/<br />Pack</th>
            <th>Rate/<br />Piece</th>
            <th>Amt</th>
            <th width="200">Description</th>
            <th width="40">Action</th>
        </tr>
        <?
            if(count($this->pending_sales_order_items) > 0)
            {
                $x = 1;
                $order_no = 0;
                $total_amount = 0;
                foreach($this->pending_sales_order_items as $item)
                {
                    if($order_no != $item->order_no)
                    {
                        if($order_no != 0)
                        {
                            ?>
                            <tr class="footer">
                                <td colspan="7" align="right">Total : </td>
                                <td align="right"><? echo round_2dp($total_amount); ?></td>
                                <td colspan="2"></td>
                            </tr>
                            <?
                            $total_amount = 0;
                        }
                        ?>
                        <tr class="header">
                            <td colspan="10">
                                Order No. <? echo $item->order_no . "( " . date("d-M-Y", strtotime($item->order_date)) . ", " . $item->customer_name . ", " . $item->customer_address . ")"; ?>
                                <span style="float:right;">
                                    <?               
                                        if(in_array($item->order_no, $this->pending_orders) && is_admin())
                                        {
                                            //unset($this->pending_orders[$item->order_no]);
                                            ?>
                                            <a href="index.php?option=com_amittrading&view=add_sales_order&m=e&order_id=<? echo $item->order_no; ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit Order"></a>
                                            <a href="#" onclick="delete_sales_order(<? echo $item->order_no; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete Order"></a>
                                            <?
                                        }
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <?
                        $order_no = $item->order_no;
                    }
                    ?>
                    <tr>
                        <td align="center"><? echo $x++; ?></td>
                        <td><input type="checkbox" name="item_ids[]" class="item_id" order_no="<? echo $item->order_no; ?>" customer_id="<? echo $item->customer_id; ?>" value="<? echo $item->id; ?>"></td>
                        <td><? echo $item->category_name; ?></td>
                        <td><? echo $item->item_name; ?></td>
                        <td><? echo floatval($item->pack); ?></td>
                        <td><? echo intval($item->quantity); ?></td>
                        <td align="right"><? echo round_2dp($item->unit_rate); ?></td>
                        <td align="right"><? echo round_2dp($item->amount); $total_amount += floatval($item->amount); ?></td>
                        <td><? echo $item->description; ?></td>
                        <td align="center">
                            <a href="#" onclick="cancel_sales_order_items(<? echo $item->order_no; ?>, <? echo $item->id; ?>); return false;"><img src="custom/graphics/icons/cancel.png" title="Cancel Order"></a>
                        </td>
                    </tr>
                    <?
                }
                ?>
                <tr class="footer">
                    <td colspan="7" align="right">Total : </td>
                    <td align="right"><? echo round_2dp($total_amount); ?></td>
                    <td colspan="2"></td>
                </tr>
                <?
            }
            else
            {
                ?><td colspan="10" align="center">No records to display.</td><?
            }
        ?>
    </table>
</form>