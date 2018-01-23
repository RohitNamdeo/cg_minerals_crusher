<?php
    defined('_JEXEC') or die;
?>
<style>
    #sales_invoice_items {
        padding: 20px;
    }
    
    tr.border_bottom td {
      border-bottom:1px solid black;
    }
</style>
 
<div style="width:100%;" id="sales_invoice_items">
    <h1>Sales Items</h1>
    <?
        //if( is_admin() && ( ($this->purchase->transporter_payment_mode == CREDIT && $this->purchase->transportation_amount_paid == 0) || $this->purchase->transporter_payment_mode != CREDIT ) )
       // {
            ?><!--<br /><a href="index.php?option=com_amittrading&view=sales_invoice&m=e&sales_id=<? echo $this->id; ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>--><?
        //}
    ?>
    <div style="float:left;">
        <table class="clean" width="300px">
            <tr>
                <td width="80px">Date</td>
                <td><? echo date("d-M-Y", strtotime($this->sales_invoice->date)); ?></td>
            </tr>
            <tr>
                <td>Time</td>
                <td><? echo $this->sales_invoice->time; ?></td>
            </tr>
            <tr>
                <td>Bill No</td>
                <td><? echo $this->sales_invoice->bill_no; ?></td>
            </tr>
            <tr>
                <td>Bill Type</td>
                <td><? echo $this->sales_invoice->bill_type == BILL ? "Bill" : "Chalan" ;?></td>
            </tr>
            <tr>
                <td>Royalty Name</td>
                <td><? echo $this->sales_invoice->royalty_name; ?></td>
                
            </tr>
            <tr>
                <td>Customer</td>
                <td width="80"><? echo $this->sales_invoice->customer_name; ?></td>
            </tr>
            <tr>
                <td>Address</td>
                <td><? echo $this->sales_invoice->customer_address; ?></td>
            </tr>
            <tr>
                <td>Contact</td>
                <td><? echo $this->sales_invoice->contact_no; ?></td>
            </tr>
            <tr>
                <td>Other Contact No.</td>
                <td><? echo $this->sales_invoice->other_contact_numbers; ?></td>
            </tr>
            </table>
    </div>
    <div style="float:left;margin-right:20px;margin-left:20px;">
        <table class="clean centreheadings">
        <tr><th colspan="2">Vehicle</th></tr> 
        <tr>
            <td>Vehicle No</td>
            <td><? echo $this->sales_invoice->vehicle_number; ?></td>
        </tr>
        <?  
            if($this->sales_invoice->vehicle_status == SELF)
            {
                ?>
                    <tr>
                        <td>Starting KM</td>        
                        <td><? echo $this->sales_invoice->starting_km; ?></td>        
                    </tr>
                    <tr>
                        <td>Vehicle Rate</td>        
                        <td><? echo $this->sales_invoice->vehicle_rate; ?></td>        
                    </tr>        
                <? 
            }
            
            if($this->sales_invoice->vehicle_status == RENT)
            {
                ?>
                    <tr>
                        <td>Transporter Name</td>
                        <td><? echo $this->sales_invoice->transporter_name; ?></td>
                    </tr>
                    <tr>
                        <td>Owner Name</td>
                        <td><? echo $this->sales_invoice->owner_name; ?></td>
                    </tr>
                    <tr>    
                        <td>Owner No</td>
                        <td><? echo $this->sales_invoice->owner_number; ?></td>
                    </tr>
                    <tr>
                        <td>Driver Name</td>
                        <td><? echo $this->sales_invoice->driver_name;?></td>
                    </tr>
                    <tr>    
                        <td>Driver No</td>
                        <td><? echo $this->sales_invoice->driver_no;?></td>
                    </tr>
                    <!--<tr>    
                        <td>Driver License No</td>
                        <td><? //echo $this->sales_invoice->driver_license_no;?></td>
                    </tr>-->
                    <tr>
                        <td>Rate</td>
                        <td><? echo $this->sales_invoice->vehicle_rate;?></td>
                    </tr>
                    <tr>   
                        <td>Add Cash</td>
                        <td><? echo $this->sales_invoice->add_cash;?></td>
                    </tr>
                    <tr><th colspan="2">Vehicle Diesel</th></tr> 
                    <tr>   
                        <td>Liter</td>
                        <td><? echo $this->sales_invoice->liter;?></td>
                    </tr>
                    <tr>   
                        <td>Liter Rate</td>
                        <td><? echo $this->sales_invoice->diesel_rate;?></td>
                    </tr>
                    <tr>   
                        <td>Diesel Total Amount</td>
                        <td><? echo $this->sales_invoice->diesel_total_amount; ?></td>
                    </tr>
                
                <?
            }
        ?>
    </table>
    </div>
    <div style="float:left;margin-right:15px;">
        <table class="clean">  
            <tr>
                <td width="80px">Loaded Weight</td>
                <td width="80px"><? echo $this->sales_invoice->loaded_weight; ?></td>
            </tr>
            <tr>
                <td>Empty Weight</td>
                <td><? echo $this->sales_invoice->empty_weight; ?></td>
            </tr>
            <tr>
                <td>Net Weight</td>
                <td><? echo $this->sales_invoice->net_weight; ?></td>
            </tr>    
            <tr>
                <th colspan="2">Loading</th>
            </tr>
            
            <tr>
                <td>Loading Type</td>
                <td><? echo $this->sales_invoice->loading_type == SELF ? "Self" : "Rent" ; ?></td>
            </tr>
            <?
                if($this->sales_invoice->loading_type == RENT)
                {
                    ?>
                        <tr>
                            <td>Transporter Name</td>
                            <td><? echo $this->sales_invoice->loading_transporter_name; ?></td>
                        </tr>        
                    <?
                }
            ?>
            
            <tr>
                <td>Vehicle Type</td>
                <td><? echo $this->sales_invoice->vehicle_type; ?></td>
            </tr>
            <tr>
                <td>Amount</td>
                <td><? echo $this->sales_invoice->loading_amount; ?></td>
            </tr>
            <tr>
                <td>Waiverage Charge </td>
                <td><? echo $this->sales_invoice->waiverage_charges; ?></td>
            </tr>
            <tr>
                <td>Remarks</td>
                <td><? echo $this->sales_invoice->remarks;?></td>
            </tr>
        </table>
    </div>
    <div style="float:left;">
        <?
            if($this->sales_invoice->royalty_mt!=0 || $this->sales_invoice->royalty_no!=0 || $this->sales_invoice->royalty_rate!=0)
            {
                ?>
                    <table class="clean">
                        <tr><th colspan="2">Royalty</th></tr>
                        <tr>
                            <td>Royalty Type</td> 
                            <td><? echo $this->sales_invoice->royalty_id == SELF ? "Self" : "Purchase" ;?></td>    
                        </tr>
                        <? 
                            if($this->sales_invoice->royalty_id == PURCHASE)
                            {
                                ?>
                                    <tr>
                                        <td>Party Name</td>
                                        <td><? echo $this->suppliers[$this->sales_invoice->party_id]->supplier_name;?></td>    
                                    </tr>
                                <?
                            }    
                        ?>
                        
                        <tr>
                            <td>Royalty MT</td>
                            <td><? echo $this->sales_invoice->royalty_mt; ?></td>
                        </tr>
                        <tr>
                            <td>Royalty No</td>
                            <td><? echo $this->sales_invoice->royalty_no;?></td>
                        </tr>
                        <tr>
                            <td>Rate</td>
                            <td><? echo $this->sales_invoice->royalty_rate;?></td>
                        </tr>
                    </table>
                <?
            }
        ?>
        <?
            if($this->sales_invoice->royalty_mt1!=0 || $this->sales_invoice->royalty_no1!=0 || $this->sales_invoice->royalty_rate1!=0)
            {
                ?>
                    <table class="clean">
                        <tr><th colspan="2">Royalty</th></tr>
                        <tr>
                            <td>Royalty Type</td> 
                            <td><? echo $this->sales_invoice->royalty_id1 == SELF ? "Self" : "Purchase" ;?></td>    
                        </tr>
                        <? 
                            if($this->sales_invoice->royalty_id1 == PURCHASE)
                            {
                                ?>
                                    <tr>
                                        <td>Party Name</td>
                                        <td><? echo $this->suppliers[$this->sales_invoice->party_id1]->supplier_name;?></td>    
                                    </tr>
                                <?
                            }    
                        ?>
                        
                        <tr>
                            <td>Royalty MT</td>
                            <td><? echo $this->sales_invoice->royalty_mt1; ?></td>
                        </tr>
                        <tr>
                            <td>Royalty No</td>
                            <td><? echo $this->sales_invoice->royalty_no1;?></td>
                        </tr>
                        <tr>
                            <td>Rate</td>
                            <td><? echo $this->sales_invoice->royalty_rate1;?></td>
                        </tr>
                    </table>
                <?
            }
        ?>
    </div>
        <div style="float:left;;margin-left:15px;">
        <table class="clean centreheadings">
            <tr>
                <td>Gross Amount : </td>
                <td><? echo $this->sales_invoice->gross_amount; ?></td>
            </tr>
            <tr>
                <td>Gst Amount : </td>
                <td><? echo $this->sales_invoice->gst_amount; ?></td>
            </tr>
            <tr>
                <td>Total Amount : </td>
                <td><? echo $this->sales_invoice->total_amount -(floatval($this->sales_invoice->loading_amount) + floatval($this->sales_invoice->waiverage_charges)); ?></td>
            </tr>
            <tr>
                <td>Total Weight : </td>
                <td><? echo $this->sales_invoice->total_weight; ?></td>
            </tr>
        </table>
    </div>    
    <br />
    <div style="padding-top:5px;clear:both; width:750px;">
        <table class="clean spread centreheadings floatheader" >
            <tr><th colspan="9" style="text-align:left;">Items</th></tr> 
            <tr>
                <th>#</th>
                <th>Items</th>
                <th>Qty</th>
                <th>Rate</th>
                <!--<th>Gross Amt</th>
                <th>GST%</th>
                <th>GST Amt</th>-->
                <th>Total Amount</th>
                <th>Note</th>
            </tr>
            <?
                if(count($this->product_sales_items) > 0)
                {
                    $x = 0;
                    $grand_total = 0;
                    foreach($this->product_sales_items as $product_items)
                    {
                        if($product_items->item_type == 1)
                        {
                        ?>
                            <tr>
                                <td align="center"><? echo ++$x; ?></td>
                                <td><? echo $product_items->product_name; ?></td>
                                <td align="center"><? echo $product_items->actual_weight; ?></td>
                                <td align="right"><? echo $product_items->product_rate; ?></td>
                                <!--<td align="right"><? //echo $product_items->gross_amount; ?></td>
                                <td align="right"><? //echo $product_items->gst_percent; ?></td>
                                <td align="right"><? //echo $product_items->gst_amount; ?></td>-->
                                <td align="right"><? echo $product_items->actual_weight * $product_items->product_rate; $grand_total+=($product_items->actual_weight * $product_items->product_rate); ?></td>
                                <td align="center"><? echo ($product_items->product_note == "" ? "NA" : $product_items->product_note); ?></td>
                            </tr>
                        <?          
                        }
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <td align="right" colspan="4"><b>Total : </b></td>
                            <td align="right"><? echo $grand_total; ?></td>
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
        <table class="clean spread centreheadings floatheader" >
            <tr><th colspan="7" style="text-align:left;">Mixing</th></tr> 
            <tr>
            
                <th>#</th>
                <th>Items</th>
                <th>Qty</th>
                <!--<th>Rate</th>
                <th>Amount</th>-->
                <th>Note</th>
            </tr>
            <?
                if(count($this->product_sales_items) > 0)
                {
                    $x = 0;
                    $grand_total = 0;
                    foreach($this->product_sales_items as $product_items)
                    {
                        if($product_items->item_type == 2)
                        {
                        ?>
                            <tr>
                                <td align="center"><? echo ++$x; ?></td>
                                <td><? echo $product_items->product_name; ?></td>
                                <td align="center"><? echo $product_items->actual_weight; ?></td>
                               <!-- <td align="right"><? //echo $product_items->product_rate; ?></td>
                                <td align="right"><? //echo $product_items->total_amount; ?></td>-->
                                <td align="center"><? echo ($product_items->product_note == "" ? "NA" : $product_items->product_note); ?></td>
                            </tr>
                        <?
                        }
                    }
                }
                else
                {
                    ?><td colspan="8" align="center">No items to display.</td><?
                }
            ?>
        </table>
    </div>
    <br />
</div>