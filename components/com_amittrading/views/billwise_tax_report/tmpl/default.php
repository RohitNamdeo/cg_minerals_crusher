<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){
        j("#from_date, #to_date").datepicker({"dateFormat" : "dd-M-yy"});
        j("#c_id").chosen({"allow_single_deselect" : true});
    });
    
    function show_report()
    {
        go("index.php?option=com_amittrading&view=billwise_tax_report&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val() + "&c_id=" + j("#c_id").val());
    }
</script>
<h1>Billwise Tax Report</h1>
<br />
<table>
    <tr>
        <td>Customers : </td>
        <td>
            <select id="c_id" style="width:250px;">
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
        <td>
            <input type="text" id="from_date" value="<? echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : "") ?>" readonly="readonly" style="width:80px;">
        </td>
        <td>To Date : </td>
        <td><input type="text" id="to_date" value="<? echo ($this->to_date != "" ? date("d-M-Y", strtotime($this->to_date)) : "") ?>" style="width:80px;"></td>
        <td>
            <input type="button" value="Refresh" onclick="show_report(); return false;">
            <input type="button" value="Clear" onclick="go('index.php?option=com_amittrading&view=billwise_tax_report');">
        </td>
    </tr>
</table>
<?
    if(count($this->bills) > 0)
    {
        ?>
        <a href="#" onclick="tableToExcel('billwise_tax_report', 'Export.xls'); return false;" class="export"><img src="custom/graphics/icons/blank.gif" class="spreadsheet"></a>
        <a href="#" onclick="popup_print('<h1>Billwise Tax Report</h1><br />' + j('#bills').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a>
        <br /><br />
        <?
    }
?>
<div id="bills">
    <table class="clean centreheadings floatheader" width="80%" id="billwise_tax_report">
        <tr>
            <th>#</th>
            <th width="80">Date</th>
            <th width="50">Bill No.</th> 
            <th>Party Name & Address</th>
            <th>Taxfree Sales</th>
            <th>Sale@5%</th>
            <th>VAT@5%</th>
            <th>Sale@14.5%</th>
            <th>VAT@14.5%</th>
            <th>Total</th>
        </tr>
        <?
            if(count($this->bills) > 0)
            {
                $x = 1;
                $bill_no = 0;
                
                $taxfree_sale = 0;
                $sale_at_rate1 = 0;
                $sale_at_rate2 = 0;
                $vat_at_rate1 = 0;
                $vat_at_rate2 = 0;
                
                $total_amount = 0;
                
                foreach($this->bills as $key=>$bill)
                {
                    if($bill_no != $bill->bill_no)
                    {
                        if($bill_no > 0)
                        {
                            $total_amount = $taxfree_sale + $sale_at_rate1 + $vat_at_rate1 + $sale_at_rate2 + $vat_at_rate2;
                            ?>
                            <tr valign="top">
                                <td align="center"><? echo $x++; ?></td>
                                <td align="center"><? echo date("d-M-Y", strtotime($this->bills[$key-1]->bill_date)); ?></td>
                                <td><? echo $this->bills[$key-1]->bill_no; ?></td>
                                <td><? echo $this->bills[$key-1]->customer_name . "<br />" . $this->bills[$key-1]->customer_address . ($this->bills[$key-1]->customer_address != '' ? ", " : "") . $this->bills[$key-1]->city; ?></td>
                                <td align="right"><? echo round_2dp($taxfree_sale); ?></td>
                                <td align="right"><? echo round_2dp($sale_at_rate1); ?></td>
                                <td align="right"><? echo round_2dp($vat_at_rate1); ?></td>
                                <td align="right"><? echo round_2dp($sale_at_rate2); ?></td>
                                <td align="right"><? echo round_2dp($vat_at_rate2); ?></td>
                                <td align="right"><? echo round_2dp($total_amount); ?></td>
                            </tr>
                            <?
                        }
                
                        $taxfree_sale = 0;
                        $sale_at_rate1 = 0;
                        $sale_at_rate2 = 0;
                        $vat_at_rate1 = 0;
                        $vat_at_rate2 = 0;
                        
                        $bill_no = $bill->bill_no;
                    }
                    
                    $rate = ($bill->unit_rate * 100) / (100 + $bill->vat_percent);
                    if($bill->vat_percent == 0)
                    {
                        $taxfree_sale += $rate * $bill->pack * $bill->quantity;
                    }
                    
                    if($bill->vat_percent == 5)
                    {
                        $sale_at_rate1 += $rate * $bill->pack * $bill->quantity;
                        $vat_at_rate1 += ( ($bill->unit_rate - (($bill->unit_rate * 100) / (100 + $bill->vat_percent))) * $bill->pack * $bill->quantity );
                    }
                    
                    if($bill->vat_percent == 14.5)
                    {
                        $sale_at_rate2 += $rate * $bill->pack * $bill->quantity;
                        $vat_at_rate2 += ( ($bill->unit_rate - (($bill->unit_rate * 100) / (100 + $bill->vat_percent))) * $bill->pack * $bill->quantity );
                    }
                }
                
                $total_amount = $taxfree_sale + $sale_at_rate1 + $vat_at_rate1 + $sale_at_rate2 + $vat_at_rate2;
                
                ?>
                <tr valign="top">
                    <td align="center"><? echo $x++; ?></td>
                    <td align="center"><? echo date("d-M-Y", strtotime($bill->bill_date)); ?></td>
                    <td><? echo $bill->bill_no; ?></td>
                    <td><? echo $bill->customer_name . "<br />" . $bill->customer_address . ($bill->customer_address != '' ? ", " : "") . $bill->city; ?></td>
                    <td align="right"><? echo round_2dp($taxfree_sale); ?></td>
                    <td align="right"><? echo round_2dp($sale_at_rate1); ?></td>
                    <td align="right"><? echo round_2dp($vat_at_rate1); ?></td>
                    <td align="right"><? echo round_2dp($sale_at_rate2); ?></td>
                    <td align="right"><? echo round_2dp($vat_at_rate2); ?></td>
                    <td align="right"><? echo round_2dp($total_amount); ?></td>
                </tr>
                <?
            }
            else
            {
                ?><td colspan="10" align="center">No records to display.</td><?
            }
        ?>
    </table>
</div>