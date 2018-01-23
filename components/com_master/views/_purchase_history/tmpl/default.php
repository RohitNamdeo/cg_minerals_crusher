<?php
    defined('_JEXEC') or die; 
?>
<style>
    .pagination span, a {
        padding: 3px;
    }
</style>
<script>

   // j(function(){
//        j("#from_date").datepicker({"dateFormat" : "dd-M-yy"});
//        j("#supplier_id").chosen();
//        /*j(".scrollIntoView").scrollIntoView({
//            rowSelector : 'purchase_invoice',
//            rowAttribute : 'purchase_id',
//            task : 'show_items'
//        });*/
//    });
    
    //j(document).on("change", "#from_date", function(){
//        go("index.php?option=com_master&view=purchase_history&supplier_id=" + j("#supplier_id").val() + "&from_date=" + j("#from_date").val());
//    });
//    
//    j(document).on("click", ".edit", function(){
//        j.colorbox.remove();
//    });
//    
//    function show_items(purchase_id)
//    {
//        j.colorbox({href:"index.php?option=com_amittrading&view=purchase_items&purchase_id=" + purchase_id + "&tmpl=xml", maxWidth: "100%", maxHeight: "100%", "open" : true});
//        return false;
//    }
//    
//    /*function edit_purchase(purchase_id)
//    {
//        go("index.php?option=com_amittrading&view=purchase_invoice&m=e&purchase_id=" + purchase_id);
//    } */
//    
//    function show_purchase(validate)
//    {
//        if(validate)
//        {
//            if(j("#supplier_id").val() == 0 && j("#from_date").val() == "")
//            {
//                alert("Select filters.");
//                return false;
//            }
//            else
//            {
//                go("index.php?option=com_master&view=purchase_history&supplier_id=" + j("#supplier_id").val() + "&from_date=" + j("#from_date").val());
//            }
//        }
//        else
//        {
//            go("index.php?option=com_master&view=purchase_history");
//        }
//    }
    
    //function view_purchase(d)
//    {
       // alert(d);exit;
//        go("index.php?option=com_master&view=purchase_history&supplier_id=" + j("#supplier_id").val() + "&from_date=" + j("#from_date").val() + "&d=" + d);
//    } 
</script> 
<h1>Purchase History</h1>
<br />
<table>
    <tr>
        <td>Supplier : </td>
        <td>
            <select id="supplier_id" name="supplier_id" style="width:250px;">
                <option value="0"></option>
                <?
                    if(count($this->suppliers) > 0)
                    {
                        foreach($this->suppliers as $supplier)
                        {
                        ?>
                            <option value="<? echo $supplier->id; ?>" <? echo ($this->supplier_id == $supplier->id ? "selected='selected'" : ""); ?> ><? echo $supplier->supplier_name; ?></option>
                        <?
                        }
                    }
                ?>
            </select>
        </td>
        <!--<td>From Date : </td>-->
        <td>
            <button onclick="view_purchase('p');"><b>&lt; Previous</b></button>
            <input type="text" id="from_date" value="<? echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : "") ?>" readonly="readonly" style="width:80px;">
            <button onclick="view_purchase('n');"><b>&gt; Next</b></button>
        </td>
        <!--<td>To Date : </td>
        <td><input type="text" id="to_date" value="<? //echo ($this->to_date != "" ? date("d-M-Y", strtotime($this->to_date)) : "") ?>" style="width:80px;"></td>-->
        <td>
            <input type="button" value="Refresh" onclick="show_purchase(1); return false;">
            <input type="button" value="Clear" onclick="show_purchase(0);">
        </td>
    </tr>
</table>
<table>
    <tr align="center" width="80%">
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
    if(count($this->purchases) > 0)
    {
        ?><a href="#" onclick="popup_print('<h1>Purchase History</h1><br />' + j('#purchase_history').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><br /><br /><?
    }
?>
<div id="purchase_history">
    <table class="clean spread" width="80%">
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Supplier</th>
            <th>Supplier Challan No </th>
            <th>Challan No</th>
            <th>Vehicle No</th>
            <th>Product</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Gross Amount</th>
            <th>GST Amount</th>
            <th>Payable Amount</th>
            <th>Total Amount</th>
            <th>Reverse Charge</th>
            <th>Loading Charge</th>
            <th>Royalty</th>
            <th>Waiverage Charges</th>
            <th>Remark</th>
            <th>Creation Date</th>
            <th class="noprint" width="50">Action</th>
        </tr>
        <?
        $x=1;
        foreach($this->purchase_history as $purchase_item)
        {
        
            echo "<tr>";
            echo "<td>".$x."</td>";
            //echo "<td><input value=".$purchase_item->purchase_id."></td>";
            echo "<td>".$purchase_item->date."</td>";
            echo "<td>".$purchase_item->supplier_name."</td>";
            echo "<td>".$purchase_item->supplier_challan_no."</td>";
            echo "<td>".$purchase_item->challan_no."</td>";
            echo "<td>".$purchase_item->vehicle_no."</td>";
            echo "<td>".$purchase_item->product_name."</td>";
            echo "<td>".$purchase_item->unit."</td>";
            echo "<td>".$purchase_item->quantity."</td>";
            echo "<td>".$purchase_item->rate."</td>";
            echo "<td>".$purchase_item->gross_amount."</td>";
            echo "<td>".$purchase_item->gst_percent."</td>";
            echo "<td>".$purchase_item->gst_amount."</td>";
            echo "<td>".$purchase_item->payable_amount."</td>";
            echo "<td>".$purchase_item->total_amount."</td>";
            echo "<td>".$purchase_item->loading_charges."</td>";
            echo "<td>".$purchase_item->royalty."</td>";
            echo "<td>".$purchase_item->waiverage_charges."</td>";
            echo "<td>".$purchase_item->remarks."</td>";
            echo "<td>".$purchase_item->creation_date."</td>";
            ?>
            <td><a href="index.php?option=com_master&view=purchase_entry&mode=e&purchase_id=<? echo $purchase_item->purchase_id; ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a></td>
            <?
            echo "</tr>";
            $x++;
        } ?>
       <!-- <tfoot>
            <tr>
                <td align="right" colspan="12"><b>Total : </b></td>
                <td align="right"><b><?//echo round_2dp($total_amount); ?></b></td>
                <td colspan="6"></td>
            </tr>
        </tfoot>-->
    </table>
</div>

<!--<div id="purchase_history">
    <table class="clean centreheadings floatheader scrollIntoView" width="80%">
        <tr>
            <th>#</th>
            <th width="50">Bill No.</th>
            <th width="80">Bill Date</th>
            <th>Supplier</th>
            <th>Miller</th>
            <th>Broker</th>
            <th>Transporter</th>
            <th>Actual<br />Trans Amt</th>
            <th>Trans<br />Discount</th>
            <th>Trans<br />Amt</th>
            <th>Gross Amt</th>
            <th>GST Amt</th>
            <th>Amt</th>
            <th>Remarks</th>
            <th>Creation Time</th>
            <th class="noprint" width="50">Action</th>
        </tr>
        <?
            if(count($this->purchases) > 0)
            {
                $x = $this->limitstart;
                $total_amount = 0;
                foreach($this->purchases as $purchase)
                {
                    $total_amount += round_2dp($purchase->bill_amount);
                    ?>
                    <tr style="cursor: pointer;" onclick="show_items(<? echo $purchase->purchase_id; ?>);" purchase_id="<? echo $purchase->purchase_id; ?>" class="purchase_invoice">
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $purchase->bill_no; ?></td>
                        <td align="center"><? echo date("d-M-Y", strtotime($purchase->bill_date)); ?></td>
                        <td><? echo $purchase->supplier_name; ?></td>
                        <td><? echo $purchase->miller; ?></td>
                        <td><? echo $purchase->broker; ?></td>
                        <td><? echo ($purchase->transporter == "" ? "Self" : $purchase->transporter); ?></td>
                        <td align="right"><? echo round_2dp($purchase->actual_transportation_amount); ?></td>
                        <td align="right"><? echo round_2dp($purchase->transportation_discount); ?></td>
                        <td align="right"><? echo round_2dp($purchase->transportation_amount); ?></td>
                        <td align="right"><? echo round_2dp($purchase->gross_amount); ?></td>
                        <td align="right"><? echo round_2dp($purchase->gst_amount); ?></td>
                        <td align="right"><? echo round_2dp($purchase->bill_amount); ?></td>
                        <td><? echo $purchase->remarks; ?></td>
                        <td align="center"><? echo ($purchase->creation_date != '0000-00-00 00:00:00' ? date("d-M-Y H:i", strtotime($purchase->creation_date)) : ""); ?></td>
                        <td align="center" class="noprint">
                            <?
                                //if( $purchase->amount_paid == 0 && is_admin() && ( ($purchase->transporter_payment_mode == CREDIT && $purchase->transportation_amount_paid == 0) || $purchase->transporter_payment_mode != CREDIT ) )
                                if( is_admin() && ( ($purchase->transporter_payment_mode == CREDIT && $purchase->transportation_amount_paid == 0) || $purchase->transporter_payment_mode != CREDIT ) )
                                {
                                    ?><a href="#" onclick="edit_purchase(<? echo $purchase->purchase_id; ?>);"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a><?
                                }
                            ?>
                        </td>
                    </tr>
                    <?
                }
                ?>
                <tfoot>
                    <tr>
                        <td align="right" colspan="12"><b>Total : </b></td>
                        <td align="right"><b><? echo round_2dp($total_amount); ?></b></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                <?
            }
            else
            {
                ?><td colspan="14" align="center">No records to display.</td><?
            }
        ?>
    </table>
</div> -->

<!--<table width="80%">
    <tr align="center">
        <td>
            <?
                /* if($this->total > 100)
                {
                    echo "<br />";
                    echo $this->pagination->getPagesLinks();
                }  */
            ?>
        </td>
    </tr>
</table>-->