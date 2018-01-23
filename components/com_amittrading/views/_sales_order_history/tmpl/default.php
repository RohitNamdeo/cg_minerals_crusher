<?php
    defined('_JEXEC') or die; 
?>
<style>
    .pagination span, a {
        padding: 3px;
    }
</style>
<script>
    j(function(){
        j("#from_date, #to_date").datepicker({"dateFormat" : "dd-M-yy"});
        j("#customer_id").chosen();
    });
    
    function show_items(order_id)
    {
        j.colorbox({href:"index.php?option=com_amittrading&view=order_items&order_id=" + order_id + "&type=s&tmpl=xml", maxWidth: "100%", maxHeight: "100%", "open" : true});
        return false;
    }
    
    function show_sales_orders(validate)
    {
        if(validate)
        {
            if(j("#customer_id").val() == 0 && j("#from_date").val() == "" && j("#to_date").val() == "")
            {
                alert("Select filters.");
                return false;
            }
            else
            {
                go("index.php?option=com_amittrading&view=sales_order_history&customer_id=" + j("#customer_id").val() + "&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=sales_order_history");
        }
    }
</script>
<h1>Sales Order History</h1>
<br />
<table>
    <tr>
        <td>Customer : </td>
        <td>
            <select id="customer_id" name="customer_id" style="width:250px;">
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
        <td>From Date : </td>
        <td><input type="text" id="from_date" value="<? echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : "") ?>" style="width:80px;"></td>
        <td>To Date : </td>
        <td><input type="text" id="to_date" value="<? echo ($this->to_date != "" ? date("d-M-Y", strtotime($this->to_date)) : "") ?>" style="width:80px;"></td>
        <td>
            <input type="button" value="Refresh" onclick="show_sales_orders(1); return false;">
            <input type="button" value="Clear" onclick="show_sales_orders(0);">
        </td>
    </tr>
</table>
<table width="80%">
    <tr align="center">
        <td>
            <?         
                if($this->total > 100)
                {
                    echo "<br />";
                    echo $this->pagination->getPagesLinks();
                    echo "<br /><br />";
                }
                else
                {
                    echo "<br />";
                }
            ?>
        </td>
    </tr>
</table>
<?
    if(count($this->sales_orders) > 0)
    {
        ?><a href="#" onclick="popup_print('<h1>Sales Order History</h1><br />' + j('#sales_order_history').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><br /><br /><?
    }
?>
<div id="sales_order_history">
    <table class="clean centreheadings floatheader" width="80%">
        <tr>
            <th>#</th>
            <th>Order No.</th>
            <th>Order Date</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Creation Time</th>
            <th>Action</th>
        </tr>
        <?
            if(count($this->sales_orders) > 0)
            {
                $total_amount = 0;
                $x = $this->limitstart;
                foreach($this->sales_orders as $order)
                {
                    $total_amount += floatval($order->total_amount);
                    ?>
                    <tr style="cursor: pointer" onclick="show_items(<? echo $order->order_no; ?>);">
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $order->order_no; ?></td>
                        <td align="center"><? echo date("d-M-Y", strtotime($order->order_date)); ?></td>
                        <td><? echo $order->customer_name; ?></td>
                        <td align="right"><? echo round_2dp($order->total_amount); ?></td>
                        <td align="center"><? echo ($order->creation_date != '0000-00-00 00:00:00' ? date("d-M-Y H:i:s", strtotime($order->creation_date)) : ""); ?></td>
                        <td align="center"><a href="#"><img src="custom/graphics/icons/blank.gif" class="view" title="View"></a></td>
                    </tr>
                    <?
                }
                ?>
                <tr>
                    <td align="right" colspan="4"><b>Total : </b></td>
                    <td align="right"><b><? echo round_2dp($total_amount); ?></b></td>
                    <td colspan="2"></td>
                </tr>
                <?
            }
            else
            {
                ?><td colspan="7" align="center">No records to display.</td><?
            }
        ?>
    </table>
</div>
<table width="80%">
    <tr align="center">
        <td>
            <?
                if($this->total > 100)
                {
                    echo "<br />";
                    echo $this->pagination->getPagesLinks();
                }
            ?>
        </td>
    </tr>
</table>