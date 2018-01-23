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
        j("#supplier_id").chosen();
        j(".scrollIntoView").scrollIntoView({
            rowSelector : 'purchase_invoice',
            rowAttribute : 'purchase_id',
            task : 'show_purchase_items'
            
        });
        
        j(".purchase_invoice").click(function(){
            //var order_id = (j(this).closest("tr").attr("order_id")); 
            j("tr").removeClass("clickedRow");
            j(this).closest("tr").addClass("clickedRow");
            //show_items(order_id);
        });
    });
    
    j(document).on("change", "#from_date", function(){
        
        go("index.php?option=com_amittrading&view=purchase_history&supplier_id=" + j("#supplier_id").val() + "&from_date=" + j("#from_date").val());
    });
    
    j(document).on("click", ".edit", function(){
        j.colorbox.remove();
    });
    
     j(document).on("click", ".show_purchase", function(){
        var purchase_id = j(this).parent("tr").attr("purchase_id");
        show_purchase_items(purchase_id);
    });
    
    function show_purchase_items(purchase_id)
    {
        j.colorbox({href:"index.php?option=com_amittrading&view=purchase_items&purchase_id=" + purchase_id + "&tmpl=xml", maxWidth: "100%", maxHeight: "100%", "open" : true});
        return false;
    }                                                                          
    
    function edit_purchase(purchase_id)
    {
        go("index.php?option=com_amittrading&view=purchase_entry&m=e&purchase_id=" + purchase_id);
    }
    function delete_purchase(purchase_id)
    {
        go("index.php?option=com_amittrading&task=delete_purchase&purchase_id=" + purchase_id);    
    }
    
    function show_purchase(validate)
    {
        if(validate)
        {
            if((j("#supplier_id").val() == 0 && j("#from_date").val() == "") || (j("#product_id").val() == 0 && j("#from_date").val() == "" ))
            {
                alert("Select filters.");
                return false;
            }
            else
            {
                go("index.php?option=com_amittrading&view=purchase_history&supplier_id=" + j("#supplier_id").val()+"&product_id=" + j("#product_id").val() + "&from_date=" + j("#from_date").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=purchase_history");
        }
    }
    
    function view_purchase(d)
    {
        go("index.php?option=com_amittrading&view=purchase_history&supplier_id=" + j("#supplier_id").val() + "&from_date=" + j("#from_date").val() + "&d=" + d);
    }
    
    function generatefromtable() 
    {
        var data = [], fontSize = 7, height = 0, doc;
        doc = new jsPDF('p', 'pt', 'a4', true);
        doc.setFont("helvetica", "normal");
        doc.setFontSize(fontSize);
        doc.text(270, 20, "Purchase Invoice History ");
        data = [];
        data = doc.tableToJson('purchase_invoice_history');//table id name
        height = doc.drawTable(data, {
            xstart : 10,
            ystart : 10,
            tablestart : 30,
            marginright : 5, 
            xOffset : 5,
            yOffset : 9,
            columnWidths:[15,45,50,150,35,45,45,60,40,45,45,00]
        });
        
        doc.text(50, height + 20, '');
        doc.save("Purchase History.pdf");
    }
   
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
                            ?><option value="<? echo $supplier->id; ?>" <? echo ($this->supplier_id == $supplier->id ? "selected='selected'" : ""); ?> ><? echo $supplier->supplier_name; ?></option><?
                        }
                    }
                ?>
            </select>
        </td>
       <!-- <td> Product : </td>
        <td>
            <select id="product_id" name="product_id" style="width:150px;">
                <option value="0"></option>
                <?
                    if(count($this->products) > 0)
                    {
                        foreach($this->products as $product)
                        {
                            ?><option value="<? echo $product->id; ?>" <? echo ($this->product_id == $product->id ? "selected='selected'" : ""); ?> ><? echo $product->product_name; ?></option><?
                        }
                    }       
                ?>
            </select>
        </td>-->
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
        ?>
<!--        <a href="#" onclick="popup_print('<h1>Purchase History</h1><br />' + j('#purchase_history').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><br /><br />
-->        <?
   // }
?>
        
<div id="purchase_history">
    <table class="clean spread centreheadings floatheader scrollIntoView " width="80%" id="purchase_invoice_history">
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Bill No</th>
            <th>Supplier Name</th>
            <th>Bill Type</th>
            <th>Supplier <br> Challan No </th>
            <th>Challan No</th>
            <th>Vehicle No</th>
            <th>Loading <br>Charge</th>
            <th>Waiverage <br> Charges</th>
            <th>Remark</th>
            <th class="noprint" width="50">Action</th>
        </tr>
        <tbody>
        <?
            if(count($this->purchases) > 0)
            {
                $x = $this->limitstart;
                //$total_amount = 0;
                foreach($this->purchases as $purchase)
                {
                    //$total_amount += round_2dp($purchase->total_amount);
                    ?>
                    <tr style="cursor: pointer;" purchase_id="<? echo $purchase->id; ?>" class=" purchase_invoice">
                        <td align="center" class="show_purchase"><? echo ++$x; ?></td>
                        <td class="show_purchase"><? echo  date("d-m-Y", strtotime($purchase->bill_date)); ?></td>
                        <td class="show_purchase"><? echo $purchase->bill_no;  ?></td>
                        <td class="show_purchase"><? echo $purchase->supplier_name;  ?></td>
                        <td class="show_purchase"><? echo $purchase->bill_type == BILL ? "Bill" : "Challan" ;?></td>
                        <td class="show_purchase"><? echo $purchase->supplier_challan_no;  ?></td>
                        <td class="show_purchase"><? echo $purchase->challan_no;  ?></td>
                        <td class="show_purchase"><? echo $purchase->vehicle_number;  ?></td>
                        <td class="show_purchase"><? echo $purchase->loading_charges; ?></td>
                        <td class="show_purchase"><? echo $purchase->waiverage_charges; ?></td>
                        <td class="show_purchase"><? echo $purchase->remarks; ?></td>
                        <td> <a href="#" onclick="edit_purchase(<? echo $purchase->id; ?>);"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                            <img src="custom/graphics/icons/blank.gif" class="delete" title="Delete Row" onclick="delete_purchase(<? echo $purchase->id; ?>)">
                        </td>
                    </tr>
                    <?
                }
                ?>
                <?
            }
            else
            {
                ?><td colspan="14" align="center">No records to display.</td><?
            }
        ?>
        </tbody>
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


