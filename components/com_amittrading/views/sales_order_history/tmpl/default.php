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
        j("#from_date").datepicker({"dateFormat" : "dd-M-yy", changeMonth : true, changeYear : true});
        j("#customer_id").chosen();
        j(".scrollIntoView").scrollIntoView({
            rowSelector : 'salse_invoice',
            rowAttribute : 'sales_id',
            task : 'show_sales_items'
        });
        
        j(".salse_invoice").click(function(){
          // var order_id = (j(this).closest("tr").attr("order_id")); 
            j("tr").removeClass("clickedRow");
            j(this).closest("tr").addClass("clickedRow");
            //show_items(order_id);
        });
    });
    
    j(document).on("change", "#from_date", function(){
        go("index.php?option=com_amittrading&view=sales_order_history&customer_id=" + j("#customer_id").val() + "&from_date=" + j("#from_date").val());
    });
    
    //j(document).on("click", ".show_sales", function(){
//        var sales_id = j(this).parent("tr").attr("sales_id");
//        show_sales_items(sales_id);
//    });
    
    j(document).on("click", ".edit", function(){
        j.colorbox.remove();
    });
    
    //function show_sales_items(sales_id)
//    {
//        j.colorbox({href:"index.php?option=com_amittrading&view=sales_order_history&sales_id=" + sales_id + "&tmpl=xml", maxWidth: "100%", maxHeight: "100%", "open" : true});
//        return false;
//    }
    
    function edit_sales_invoice(sales_id)
    {
        go("index.php?option=com_amittrading&view=sales_order&m=e&sales_id=" + sales_id);
    }
    
    function delete_sales_invoice(sales_id)
    {
        go("index.php?option=com_amittrading&task=delete_sales_order&sales_id=" + sales_id);     
    }
    
    function show_sales_history(validate)
    {
        if(validate)
        {
            if(j("#customer_id").val() == 0 && j("#from_date").val() == "")
            {
                alert("Select filters.");
                return false;
            }
            else
            {
                go("index.php?option=com_amittrading&view=sales_order_history&customer_id=" + j("#customer_id").val() + "&from_date=" + j("#from_date").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=sales_order_history");
        }
    }
    
    function view_purchase(d)
    {
        go("index.php?option=com_amittrading&view=sales_order_history&customer_id=" + j("#customer_id").val() + "&from_date=" + j("#from_date").val() + "&d=" + d);
    }
    
    function generatefromtable() 
    {
        var data = [], fontSize = 8, height = 0, doc;
        doc = new jsPDF('p', 'pt', 'a4', true);
        doc.setFont("helvetica", "normal");
        doc.setFontSize(fontSize);
        doc.text(270, 20, "Sales Order History ");
        data = [];
        data = doc.tableToJson('sales_order_history');//table id name
        
        height = doc.drawTable(data, {
            xstart : 10,
            ystart : 10,
            tablestart : 30, // margin top
            marginright : 5, 
            xOffset : 5, // column width
            yOffset : 12,
            columnWidths:[15,40,50,45,40,140,85,40,30,40,50,00]
            //columnWidths:[15,45,50,150,35,45,45,60,40,45,45,00]
        });
        
        doc.text(50, height + 20, '');
        //doc.textStyle("center");
        doc.save("Sales Order History.pdf");
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
        <!--<td>From Date : </td>-->
        <td>
            <button onclick="view_purchase('p');"><b>&lt; Previous</b></button>
            <input type="text" id="from_date" value="<? echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : "") ?>" readonly="readonly" style="width:80px;">
            <button onclick="view_purchase('n');"><b>&gt; Next</b></button>
        </td>
        <!--<td>To Date : </td>
        <td><input type="text" id="to_date" value="<? //echo ($this->to_date != "" ? date("d-M-Y", strtotime($this->to_date)) : "") ?>" style="width:80px;"></td>-->
        <td>
            <input type="button" value="Refresh" onclick="show_sales_history(1); return false;">
            <input type="button" value="Clear" onclick="show_sales_history(0);">
        </td>
        <td><button type="button" id='pdfExport' onclick="generatefromtable();">Export PDF</button></td>
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
    //if(count($this->purchases) > 0)
    //{
        ?><!--<a href="#" onclick="popup_print('<h1>Purchase History</h1><br />' + j('#purchase_history').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><br /><br />--><?
    //}
?>
<div id="purchase_history">
    <table class="clean centreheadings floatheader scrollIntoView" width="80%" id="sales_order_history">
        <tr>
            <th>#</th>
            <th width="50">Bill No.</th>
            <!--<th width="50">Bill Challan No.</th>-->
            <th width="80">Bill Date</th>
            <th>Time</th>
            <th>Bill Type</th>
            <th>Customer Name</th>
            <th>Royalty Name</th>
            <th>Royalty Rate</th>
            <th>Qty</th>
            <th>Billed Qty</th>
            <th>Qty Remaining</th>
            <th class="noprint" width="80" >Action</th>
        </tr>
        <?
            if(count($this->sales_invoices) > 0)
            {
                $x = $this->limitstart;
                //$total_amount = 0;
                foreach($this->sales_invoices as $sales_invoice)
                {
                    //$total_amount += round_2dp($purchase->bill_amount);
                    ?>
                    <tr sales_id="<? echo $sales_invoice->id; ?>" class="salse_invoice">
                        <td align="center" class="show_sales"><? echo ++$x; ?></td>
                        <td class="show_sales"><? echo "CG" . $sales_invoice->bill_no; ?></td>
                        <!--<td class="show_sales"><? //echo $sales_invoice->bill_challan_no; ?></td> -->
                        <td align="center" class="show_sales"><? echo date("d-M-Y", strtotime($sales_invoice->order_date)); ?></td>
                        <td class="show_sales"><? echo date('H:i:s',strtotime($sales_invoice->creation_date)) ; ?></td>
                        <td class="show_sales"><? echo $sales_invoice->bill_type == BILL ? "Bill" : "Challan" ;?></td>
                        <td class="show_sales"><? echo $sales_invoice->customer_name; ?></td>
                        <td class="show_sales"><? echo $sales_invoice->royalty_name; ?></td>
                        <td class="show_sales"><? echo $sales_invoice->royalty_rate; ?></td>
                        <td><? echo $sales_invoice->total_weight; ?></td>
                        <td><? echo $sales_invoice->billed_quantity; ?></td>
                        <td><? echo ($sales_invoice->total_weight - $sales_invoice->billed_quantity);?></td>
                        
                        <td>
                            <a href="#" onclick="edit_sales_invoice(<? echo $sales_invoice->id; ?>);"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                            <a href="#" onclick="delete_sales_invoice(<? echo $sales_invoice->id; ?>);"><img  src="custom/graphics/icons/blank.gif" class="delete" title="Delete Row"></a>
                        </td>
                    </tr>
                    <?
                }
                ?>
                <!--<tfoot>
                    <tr>
                        <td align="right" colspan="11"><b>Total : </b></td>
                        <td align="right"><b><? //echo round_2dp($total_amount); ?></b></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>-->
                <?
            }
            else
            {
                ?><td colspan="15" align="center">No records to display.</td><?
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