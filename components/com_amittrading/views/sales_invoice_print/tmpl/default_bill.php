<?php
    defined('_JEXEC') or die( 'Restricted access' );
    $godown_data_locations = array();
    $godown_data_items = array();
?>
<style>
    /*#sales_invoice{
        width: 500px;
        height: 100%;
        border: 1px solid black;
    }*/
    
    #invoice_items td{
        padding: 0;
    }
    
    #invoice_items .tdf td{
        border-bottom: none;
    }
    
    #invoice_items .tdm td{
        border-top: none;
        border-bottom: none;
    }
    
    #invoice_items .tdl td{
        border-top: none;
    }
    
    #godown_items td{
        padding: 0;
    }
    
    #godown_items .tdf td{
        border-bottom: none;
    }
    
    #godown_items .tdm td{
        border-top: none;
        border-bottom: none;
    }
    
    #godown_items .tdl td{
        border-top: none;
    }
</style>
<script>
    j(function(){
        window.print();
    });
</script>
<div id="sales_invoice">
    <table width="70%" align="center">
        <tr>
            <td colspan="5" align="center" style="line-height:3px;">
                Shri Ganeshay Namah
                <h2>CHHATTISGARH MINERALS</h2>
                <font size="2">MANUFACTURE AND SUPPLIER OF STONE CHIPS AND LIME</font><br /><br /><br /><br /><br />
                <font size="2">ADD:- LALADHURWA ROAD,VILLAGE - GUDELI RAIGARH(C. G.)</font>
            </td>
        </tr>
    </table>
    <br /><br />   
    <table width="70%" align="center">
        <tr align="left">
            <td valign="top" width="150">Day Munshi Mobile #</td>
            <td colspan="8"><? echo $this->day_munshi_mobile_no; ?></td>
            <td valign="top" align="right">GSTIN : <? echo $this->gst_no; ?></td>
        </tr>
        <tr align="left">
            <td valign="top" width="55">Night Munshi Mobile #</td>
            <td colspan="8"><? echo $this->night_munshi_mobile_no; ?></td>
        </tr>
        <tr align="left">
            <td valign="top" width="55">Mobile #</td>
            <td colspan="8"><? echo $this->mobile_no; ?></td>
        </tr>
    </table>
    <center>
        <table>
            <tr><td><u><b>BILL</b></u></td></tr>
        </table>
    </center>
     <table width="70%" align="center">
        <tr valign="top">
            <td align="right" width="100">No. : </td>
            <td width="250"><? echo $this->sales->bill_no; ?></td>
            <td colspan="2" align="right">Date : <? echo date("d-M-Y", strtotime($this->sales->date)) . " " . date("h:i A"); ?></td>
        </tr>
        <tr>
            <td align="right" valign="top">Royalty Name : </td>
            <td><b><? echo @$this->royalty_list[$this->sales->royalty_id]->royalty_name; ?></b></td>
            <td align="right" width="120" valign="top">Vehicle No. : </td>
            <td><? echo $this->sales->vehicle_number; ?></td>
        </tr>
        <tr>
            <td align="right" valign="top" width="75">Customer : </td>
            <td><b><? echo $this->sales->customer_name; ?></b></td>
            <td align="right" valign="top">Transporter Name : </td>
            <td><? echo @$this->transporter_list[$this->sales->transporter_id]->transporter_name; ?></td>
        </tr>
        <tr>
            <td align="right" valign="top" width="75">Address : </td>
            <td><? echo $this->sales->customer_address . ($this->sales->customer_address != '' ? ", " : "") . $this->sales->city; ?></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td align="right" valign="top" width="75">Contact No. : </td>
            <td><? echo $this->sales->contact_no; ?></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <br />
    <center>
        <table class="clean" width="75%" align="center" id="invoice_items">
            <tr align="center" valign="top">
                <td width="10">#</td>
                <td width="400">Item</td>
                <td width="100">HSN Code</td>
                <td width="40">Qty</td>
                <td width="36">Rate</td>
                <td width="50">Gross Amt</td>
                <td width="36">GST %</td>
                <td width="50">GST Amount</td>
                <td width="50">Total Amount</td>
            </tr>
            <?
                $x = 1;
                $total_amount = 0;
                $gst_amount = 0;
                $unti_rate = 0;
                $total_items = count($this->sales_items);
                foreach($this->sales_items as $key => $item)
                {                          
                    if($x == 1) { $td_class = "tdf"; }
                    else if($x == $total_items) { $td_class = "tdl"; }
                    else { $td_class = "tdm"; }
                    ?>
                    <tr class="<? echo $td_class; ?>">
                        <td align="center"><? echo $x++; ?></td>
                        <td><? echo $item->product_name; ?></td>
                        <td align="center"><? echo ($item->hsn_code!="" && $item->hsn_code!=0 ? $item->hsn_code : ""); ?></td>
                        <td align="center"><? echo abs($item->total_weight); ?></td>
                        <td align="right"><? echo round_2dp($item->product_rate); ?></td>
                        <td align="right"><? echo(abs(round_2dp($item->total_weight * $item->product_rate))); ?></td> 
                        <td align="right"><? echo abs($item->gst_percent); ?></td>
                        <td align="right"><? echo round_2dp($item->gst_amount); $gst_amount += $item->gst_amount; ?></td>
                        <td align="right"><? echo(abs(round_2dp($item->total_amount))); $total_amount+= $item->total_amount; ?></td>
                    </tr>
                    <?
                }
            ?>
            <tfoot>
                <tr>
                    <td colspan="8" align="right"> Amount : </td>
                    <td align="right"><? echo $total_amount = (round_2dp($this->sales->total_amount)); ?></td>
                </tr>
                <tr>
                    <td colspan="8" align="right">GST : </td>
                    <td align="right"><? echo  round_2dp($this->sales->gst_amount); ?></td>
                </tr>
                <tr>
                    <td colspan="8" align="right">Loading Charges : </td>
                    <td align="right"><? echo round_2dp($this->sales->loading_amount); ?></td>
                </tr>
                <tr>
                    <td colspan="8" align="right">Waiverage Charges : </td>
                    <td align="right"><? echo round_2dp($this->sales->waiverage_charges); ?></td>
                </tr>
                <tr>
                    <td colspan="8"  align="right">Royalty Charges <? echo ($this->sales->royalty_mt + $this->sales->royalty_mt1) . " MT " . " * " . $this->sales->royalty_rate . " (" . $this->sales->royalty_no . ($this->sales->royalty_no1!="" && $this->sales->royalty_no1 > 0 ? " & " . $this->sales->royalty_no1 : "") . ")"; ?>: </td>
                    <td align="right"><b>Rs.<? echo $royalty_charges =  round_2dp(($this->sales->royalty_mt + $this->sales->royalty_mt1) * $this->sales->royalty_rate); ?></b></td>
                </tr>
                <tr>
                    <td colspan="8"  align="right">Transportation Charges <? echo ($this->sales->net_weight!="" ? $this->sales->net_weight ." MT " : "") . ($this->sales->vehicle_rate!="" ? " @ " . $this->sales->vehicle_rate : "") ?> : </td>
                    <td align="right"><b>Rs.<? echo $transportation_charges =  round_2dp($this->sales->vehicle_rate * $this->sales->net_weight); ?></b></td>
                </tr>
                <?
                    $bill_total_amount = floatval($total_amount + $royalty_charges + $transportation_charges);  
                ?>
                <tr>
                    <td colspan="8"  align="right">Net Amount : </td>
                    <td align="right"><b>Rs.<? echo round_2dp($bill_total_amount);?></b></td>
                </tr>
                
            </tfoot>
        </table>
    </center>
    <br /><br />
    <table width="70%" align="center">
        <tr>
            <td>Remarks : <? echo $this->sales->remarks;?></td>
        </tr>
    </table>
    <br /><br />
    <center>
        <table width="70%">
            <tr align="center">
                <td width="24">Driver Sign.</td>
                <td width="24">Receiver Sign.</td>
                <td width="24">Munshi Sign.</td>
            </tr>       
        </table>
    </center>        
</div>