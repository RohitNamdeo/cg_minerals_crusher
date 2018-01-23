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
        j("#from_date").datepicker({"dateFormat" : "dd-M-yy"});
        j("#customer_id, #location_id").chosen();
        j(".scrollIntoView").scrollIntoView({
            rowSelector : 'sales_item'
        });
    });
    
    j(document).on("change", "#from_date", function(){
        go("index.php?option=com_amittrading&view=itemwise_sales_history&customer_id=" + j("#customer_id").val() + "&location_id=" + j("#location_id").val() + "&from_date=" + j("#from_date").val());
    });
    
    function show_sales_items(validate)
    {
        if(validate)
        {
            if(j("#customer_id").val() == 0 && j("#location_id").val() == 0 && j("#from_date").val() == "")
            {
                alert("Select filters.");
                return false;
            }
            else
            {
                go("index.php?option=com_amittrading&view=itemwise_sales_history&customer_id=" + j("#customer_id").val() + "&location_id=" + j("#location_id").val() + "&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=itemwise_sales_history");
        }
    }
    
    function view_sales_items(d)
    {
        go("index.php?option=com_amittrading&view=itemwise_sales_history&customer_id=" + j("#customer_id").val() + "&location_id=" + j("#location_id").val() + "&from_date=" + j("#from_date").val() + "&d=" + d);
    }
    
    function delete_sales_invoice(sales_id)
    {
        if(confirm("Are you sure to delete the sales invoice?"))
        {
            go("index.php?option=com_amittrading&task=delete_sales_invoice&sales_id=" + sales_id);
        }
        else
        {
            return false;
        }
    }
</script>
<h1>Itemwise Sales History</h1>
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
        <td>Location : </td>
        <td>
            <select id="location_id" name="location_id" style="width:250px;">
                <option value="0"></option>
                <?
                    if(count($this->locations) > 0)
                    {
                        foreach($this->locations as $location)
                        {
                            ?><option value="<? echo $location->id; ?>" <? echo ($this->location_id == $location->id ? "selected='selected'" : ""); ?> ><? echo $location->location_name; ?></option><?
                        }
                    }
                ?>
            </select>
        </td>
        <!--<td>From Date : </td>-->
        <td>
            <button onclick="view_sales_items('p');"><b>&lt; Previous</b></button>
            <input type="text" id="from_date" value="<? echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : "") ?>" readonly="readonly" style="width:80px;">
            <button onclick="view_sales_items('n');"><b>&gt; Next</b></button>
        </td>
        <!--<td>To Date : </td>
        <td><input type="text" id="to_date" value="<? //echo ($this->to_date != "" ? date("d-M-Y", strtotime($this->to_date)) : "") ?>" style="width:80px;"></td>-->
        <td>
            <input type="button" value="Refresh" onclick="show_sales_items(1); return false;">
            <input type="button" value="Clear" onclick="show_sales_items(0);">
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
    if(count($this->sales_items) > 0)
    {
        ?><a href="#" onclick="popup_print('<h1>Itemwise Sales History</h1><br />' + j('#sales_history').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><br /><br /><?
    }
?>
<div id="sales_history">
    <table class="clean centreheadings floatheader scrollIntoView" width="80%">
        <tr>
            <th>#</th>
            <th width="50">Bill No.</th>
            <th width="80">Bill Date</th>
            <th>Bill Type</th>
            <th>Location</th>
            <th>Customer</th>
            <th>Category</th>
            <th>Item</th>
            <th>Box/<br />Pack</th>
            <th>Pieces/<br />Pack</th>
            <th>Rate/<br />Piece</th>
            <th>Discount</th>
            <th>GST %</th>
            <th>Amt</th>
            <th>Description</th>
            <th>Trans<br />Amt</th>
            <th>Discount</th> 
            <th>Credit Day(s)</th>
            <th>Remarks</th>
            <th>Creation Time</th>
            <th class="noprint" width="50">Action</th>
        </tr>
        <?
            if(count($this->sales_items) > 0)
            {
                $x = $this->limitstart;
                $total_amount = 0;
                foreach($this->sales_items as $item)
                {
                    $total_amount += round_2dp($item->amount);
                    ?>
                    <tr class="sales_item">
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $item->bill_no; ?></td>
                        <td align="center"><? echo date("d-M-Y", strtotime($item->bill_date)); ?></td>
                        <td>
                            <?
                                if($item->invoice_type == BILL) { echo "Bill"; }
                                else if($item->invoice_type == QUOTATION) { echo "Quotation"; }
                            ?>
                        </td>
                        <td><? echo $item->location_name; ?></td>
                        <td><? echo $item->customer_name; ?></td>
                        <td><? echo $item->category_name; ?></td>
                        <td><? echo $item->item_name; ?></td>
                        <td><? echo floatval($item->pack); ?></td>
                        <td><? echo intval($item->quantity); ?></td>
                        <td align="right"><? echo round_2dp($item->unit_rate); ?></td>
                        <td align="right"><? echo $item->item_discount; ?></td>
                        <td align="right"><? echo ($item->invoice_type == BILL ? round_2dp($item->gst_percent) : "N/A"); ?></td>
                        <td align="right"><? echo round_2dp($item->amount); ?></td>
                        <td><? echo $item->description; ?></td>
                        <td align="right"><? echo round_2dp($item->transportation_amount); ?></td>
                        <td align="right"><? echo $item->discount; ?></td>
                        <td><? echo intval($item->credit_days); ?></td>
                        <td><? echo $item->remarks; ?></td>
                        <td align="center"><? echo ($item->creation_date != '0000-00-00 00:00:00' ? date("d-M-Y H:i", strtotime($item->creation_date)) : ""); ?></td>
                        <td align="center" class="noprint">
                            <?
                                //if( ($item->amount_paid == 0 || $item->cash_invoice == YES) && is_admin() )
                                if( is_admin() )
                                {
                                    ?>
                                    <a href="index.php?option=com_amittrading&view=sales_invoice&m=e&sales_id=<? echo $item->sales_id; ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                                    <a href="#" onclick="delete_sales_invoice(<? echo $item->sales_id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete"></a>
                                    <?
                                }
                            ?>
                        </td>
                    </tr>
                    <?
                }
                ?>
                <tfoot>
                    <tr>
                        <td align="right" colspan="13"><b>Total : </b></td>
                        <td align="right"><b><? echo round_2dp($total_amount); ?></b></td>
                        <td colspan="8"></td>
                    </tr>
                </tfoot>
                <?
            }
            else
            {
                ?><td colspan="21" align="center">No records to display.</td><?
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