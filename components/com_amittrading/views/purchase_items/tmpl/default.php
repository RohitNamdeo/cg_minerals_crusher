<?php
    defined('_JEXEC') or die;
?>
<style>
    #purchase_items {
        padding: 20px;
    }
    
    tr.border_bottom td {
      border-bottom:1px solid black;
    }
</style>
 
<div  id="purchase_items">
    <h1>Purchase Items</h1>
    <?
        //if( is_admin() && ( ($this->purchase->transporter_payment_mode == CREDIT && $this->purchase->transportation_amount_paid == 0) || $this->purchase->transporter_payment_mode != CREDIT ) )
       // {
            ?><!--<br /><a href="index.php?option=com_amittrading&view=sales_invoice&m=e&sales_id=<? echo $this->id; ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>--><?
        //}
    ?>
    <div style="float:left;">
        <table class="clean" width="550px">
            <tr>
                <td width="70px">Date</td>
                <td width="70px"><? echo date("d-M-Y", strtotime($this->purchase->bill_date)); ?></td>
                
                <td>Challan No</td>
                <td><? echo $this->purchase->challan_no; ?></td>
                
            </tr>
            <tr>
                <td>Supplier</td>
                <td ><? echo $this->purchase->supplier_name; ?></td>
                
                <td width="70px">Vehicle No</td>
                <td width="70px"><? echo $this->purchase->vehicle_number;?></td>
                
            </tr>
            <tr>
                <td>Bill No</td>
                <td><? echo $this->purchase->bill_no; ?></td>
                
                <td>Loading Charges</td>
                <td><? echo $this->purchase->loading_charges;?></td>
                
            </tr>
            <tr>
                <td>Bill Type</td>
                <td id="bill_type"><? echo $this->purchase->bill_type == BILL ? "Bill" : "Chalan" ;?></td>
                
                <td>Waiverage Charges</td>
                <td><? echo $this->purchase->waiverage_charges;?></td>
            </tr>
            <tr>
                <td>Supplier Challan No</td>
                <td><? echo $this->purchase->supplier_challan_no; ?></td>
                
                <td>Remarks</td>
                <td><? echo $this->purchase->remarks;?></td>
            </tr>
            
            
            </table>
    </div>
    <br />
    <? 
        if($this->purchase->bill_type == BILL)
        {
        ?> 
            <div >
                <table class="clean centreheadings floatheader" style="width:550px;" >
                    <tr><th colspan="10" style="text-align:left;">Items</th></tr> 
                    <tr>
                        <th>#</th>
                        <th>Items</th>
                        <th>Unit</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Gross Amt</th>
                        <th class="gst_fields">GST Percent</th>
                        <th class="gst_fields">GST Amount</th>
                        <th>Total Amount</th>
                        <th>Note</th>
                    </tr>
                    <?
                        if(count($this->purchase_items) > 0)
                        {
                            $x = 0;
                            foreach($this->purchase_items as $purchase_items)
                            {
                                ?>
                                    <tr>
                                        <td align="center"><? echo ++$x; ?></td>
                                        <td><? echo $purchase_items->product_name; ?></td>
                                        <td><? echo $purchase_items->unit; ?></td>
                                        <td><? echo $purchase_items->product_mt; ?></td>
                                        <td align="right"><? echo $purchase_items->product_rate; ?></td>
                                        <td align="right"><? echo $purchase_items->gross_amount; ?></td>
                                        <td align="right" class="gst_fields"><? echo $purchase_items->gst_percent; ?></td>
                                        <td align="right" class="gst_fields"><? echo $purchase_items->gst_amount; ?></td>
                                        <td align="right"><? echo $purchase_items->total_amount; ?></td>
                                        <td><? echo ($purchase_items->note == "" ? "NA" : $purchase_items->note); ?></td>
                                    </tr>
                                <?          
                            }
                            ?>
                            <tfoot>
                                <tr>
                                    <td align="right" colspan="8"><b>Total : </b></td>
                                    <td align="right"><? echo $this->purchase->total_amount; ?></td>
                                </tr>
                            </tfoot>
                            <?
                        }
                        else
                        {
                            ?><td colspan="8" align="center">No items to display.</td><?
                        }
                    ?>
                </table>
                <br />
            </div>
        <?
            }
            else
            {
            ?>
            
            <div >
                <table class="clean centreheadings floatheader" style="width:550px;">
                    <tr><th colspan="9" style="text-align:left;">Items</th></tr> 
                    <tr>
                        <th>#</th>
                        <th>Items</th>
                        <th>Unit</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Total Amount</th>
                        <th>Note</th>
                    </tr>
                    <?
                        if(count($this->purchase_items) > 0)
                        {
                            $x = 0;
                            foreach($this->purchase_items as $purchase_items)
                            {
                                ?>
                                    <tr>
                                        <td align="center"><? echo ++$x; ?></td>
                                        <td><? echo $purchase_items->product_name; ?></td>
                                        <td><? echo $purchase_items->unit; ?></td>
                                        <td><? echo $purchase_items->product_mt; ?></td>
                                        <td align="right"><? echo $purchase_items->product_rate; ?></td>
                                        <td align="right"><? echo $purchase_items->total_amount; ?></td>
                                        <td><? echo ($purchase_items->note == "" ? "NA" : $purchase_items->note); ?></td>
                                    </tr>
                                <?          
                            }
                            ?>
                            <tfoot>
                                <tr>
                                    <td align="right" colspan="5"><b>Total : </b></td>
                                    <td align="right"><? echo $this->purchase->total_amount; ?></td>
                                </tr>
                            </tfoot>
                            <?
                        }
                        else
                        {
                            ?><td colspan="8" align="center">No items to display.</td><?
                        }
                    ?>
                </table>
                <br />
            </div>
        <?
            }
        ?>    
    <br />
</div>