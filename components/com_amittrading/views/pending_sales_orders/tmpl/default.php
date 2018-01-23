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
        j("#customer_id, #customer_segment_id").chosen();
        j(".scrollIntoView").scrollIntoView({
            rowSelector : 'sale_order',
            rowAttribute : 'order_id',
            task : 'show_items',
            
        });
        
        j(".sale_order").click(function(){
           var order_id = (j(this).closest("tr").attr("order_id")); 
           //j(this).prev().css("background-color","white");
//           j(this).prev().css("color","black");
            //j("tr").css("background-color","white");
//            j("tr").css("color","black");

            j("tr").removeClass("clickedRow");
            j(this).closest("tr").addClass("clickedRow");
            show_items(order_id);
        });
    });
    
    function show_sales_orders(validate)
    {
        if(validate)
        {
            if(j("#customer_id").val() == 0 && j("#customer_segment_id").val() == 0)
            {
                alert("Select filter.");
                return false;
            }
            else
            {
                go("index.php?option=com_amittrading&view=pending_sales_orders&customer_id=" + j("#customer_id").val());// + "&cs_id=" + j("#customer_segment_id").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=pending_sales_orders");
        }
    }
    
    function show_items(order_id)
    {
        
        j.get("index.php?option=com_amittrading&view=pending_sales_orders&tmpl=xml&m=d&order_id=" + order_id, function(order_items){
            if(order_items != "")
            {
                j("#order_items").html(order_items);
            }
        });
    }
    
    function cancel_sales_order_items(order_id, order_item_id)
    {
       // alert(order_id);
//        alert(order_item_id);
        if(confirm("Are you sure?"))
        {
            j.get("index.php?option=com_amittrading&task=cancel_order_items&tmpl=xml&type=s&order_id=" + order_id + "&order_item_id=" + order_item_id, function(data){
                if(data == "ok")
                {
                    alert("Item cancelled successfully.");
                    go(window.location);
                    show_items(order_id);
                    //j("#action" + order_id).html("");
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
       // alert(order_id);
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
            j("#pending_items").submit();
        }
        else
        {
            alert("At least 1 item should be selected to create invoice.");
            return false;
        }
    }
</script>
<h1>Pending Sales Orders</h1>
<input type="button" value="Create Sales Order" onclick="go('index.php?option=com_amittrading&view=sales_order');">
<?
    if(count($this->pending_orders) > 0)
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
        <!--<td>Segment : </td>
        <td>
            <select id="customer_segment_id" style="width:150px;">
                <option value="0"></option>
                <?
                   // if(count($this->customer_segments) > 0)
//                    {
//                        foreach($this->customer_segments as $segment)
//                        {
                            ?><option value="<? //echo $segment->id; ?>" <? //echo ($this->customer_segment_id == $segment->id ? "selected='selected'" : ""); ?> ><? //echo $segment->customer_segment; ?></option><?
//                        }
//                    }
                ?>
            </select>
        </td>-->
        <td>
            <input type="button" value="Refresh" onclick="show_sales_orders(1); return false;">
            <input type="button" value="Clear" onclick="show_sales_orders(0);">
        </td>
    </tr>
</table>
<br />
<table>
    <tr>
        <td valign="top">
            <table class="clean centreheadings scrollIntoView">
                <tr>
                    <th>#</th>
                    <th width="20">Order No.</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <!--<th>Bill Type</th>-->
                    <th>City</th>
                    <!--<th>Royalty Name</th>
                    <th>Royalty rate</th>-->
                    <th>Qty</th>
                    <th>Billed Qty</th>
                    <th>Qty Remaining</th>
                    <th>Action</th>
                </tr>
                <?
                    if(count($this->pending_orders) > 0)
                    {
                        $x = 1;
                        foreach($this->pending_orders as $order)
                        {
                            ?>
                            <tr style="cursor:pointer;" order_id="<? echo $order->order_id; ?>" class="sale_order">
                                <td align="center"><? echo $x++; ?></td>
                                <td><? echo $order->order_id; ?></td>
                                <td align="center"><? echo date("d-M-Y", strtotime($order->order_date)); ?></td>
                                <td><? echo $order->customer_name; ?></td>
                                <td><? echo $order->city; ?></td>
                                <!--<td><? //echo $order->royalty_name; ?></td>
                                <td><? //echo $order->royalty_rate; ?></td>-->
                                <td><? echo $order->total_weight; ?></td>
                                <td><? echo $order->billed_quantity; ?></td>
                                <td><? echo ($order->total_weight - $order->billed_quantity);?></td>
                                <td align="center" id="action<? echo $order->order_id; ?>">
                                    <?
                                        //if( is_admin() && in_array($order->order_id, $this->pending_order_ids) )
//                                        {
                                            ?>
                                            <a href="index.php?option=com_amittrading&view=sales_order&m=e&sales_id=<? echo $order->order_id;?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit Order"></a>
                                            <a href="index.php?option=com_amittrading&task=delete_sales_order_from_pending_view&sales_id=<? echo $order->order_id;?>"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete Order"></a>
                                            <?
                                       // }
                                    ?>
                                </td>
                            </tr>
                            <?
                        }
                    }
                    else
                    {
                        ?><td colspan="6" align="center">No records to display.</td><?
                    }
                ?>
            </table>
        </td>
        <td width="20"></td>
        <td id="order_items" valign="top"></td>
    </tr>
</table>