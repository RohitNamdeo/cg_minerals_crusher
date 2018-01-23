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
            rowSelector : 'royalty_sales',
            rowAttribute : 'royalty_id',
            task : 'show_sales_items'
        });
        
         j(".salse_invoice").click(function(){
            j("tr").removeClass("clickedRow");
            j(this).closest("tr").addClass("clickedRow");
        });
    });
    
    j(document).on("change", "#from_date", function(){
        go("index.php?option=com_amittrading&view=royalty_sales_history&customer_id=" + j("#customer_id").val() + "&from_date=" + j("#from_date").val());
    });
    
   /* j(document).on("click", ".show_sales", function(){
        var sales_id = j(this).parent("tr").attr("sales_id");
        show_sales_items(sales_id);
    });
    
    j(document).on("click", ".edit", function(){
        j.colorbox.remove();
    });
    
    function show_sales_items(sales_id)
    {
        j.colorbox({href:"index.php?option=com_amittrading&view=sales_invoice_items&sales_id=" + sales_id + "&tmpl=xml", maxWidth: "100%", maxHeight: "100%", "open" : true});
        return false;
    }
    
    function edit_sales_invoice(sales_id,order_id)
    {
        go("index.php?option=com_amittrading&view=sales_invoice&m=e&sales_id=" + sales_id + "&order_id=" + order_id);
    }
    
    function delete_sales_invoice(sales_id,order_id)
    {
        go("index.php?option=com_amittrading&task=delete_sales_invoice&sales_id=" + sales_id + "&order_id=" + order_id);     
    } */
    
    function show_royalty_history(validate)
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
                go("index.php?option=com_amittrading&view=royalty_sales_history&customer_id=" + j("#customer_id").val() + "&from_date=" + j("#from_date").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=royalty_sales_history");
        }
    }
    
    function view_royalty(d)
    {
        go("index.php?option=com_amittrading&view=royalty_sales_history&customer_id=" + j("#customer_id").val() + "&from_date=" + j("#from_date").val() + "&d=" + d);
    }
    
    function generatefromtable() 
    {
        var data = [], fontSize = 7, height = 0, doc;
        doc = new jsPDF('p', 'pt', 'a4', true);
        doc.setFont("helvetica", "normal");
        doc.setFontSize(fontSize);
        doc.text(270, 20, "Royalty Sales History ");
        data = [];
        data = doc.tableToJson('royalty_sales_history');//table id name
        height = doc.drawTable(data, {
            xstart : 10,
            ystart : 10,
            tablestart : 30,
            marginright : 5, 
            xOffset : 5,
            yOffset : 9,
            columnWidths:[35,50,150,100,40,40,45,65,50]
        });
        
        doc.text(50, height + 20, '');
        doc.save("Sales History.pdf");
    }
    
</script>
<h1>Royalty Sales History</h1>
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
        
        <td>
            <button onclick="view_royalty('p');"><b>&lt; Previous</b></button>
            <input type="text" id="from_date" value="<? echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : "") ?>" readonly="readonly" style="width:80px;">
            <button onclick="view_royalty('n');"><b>&gt; Next</b></button>
        </td>
        
        <td>
            <input type="button" value="Refresh" onclick="show_royalty_history(1); return false;">
            <input type="button" value="Clear" onclick="show_royalty_history(0);">
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

<div id="sales_history">
    <table class="clean centreheadings floatheader scrollIntoView" width="80%" id="royalty_sales_history">
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Customer Name</th>
            <th>Booklet Name</th>
            <th>Booklet <br>Page From</th>
            <th>Booklet <br>Page To</th>
            <th>Total Pages</th>
            <th>Amount</th>
            <th>Comments</th>
            <!--<th class="noprint" width="80" >Action</th>-->
        </tr>
        <?
            if(count($this->royalty_sales) > 0)
            {
                $x = $this->limitstart;
                $x = 0;
                foreach($this->royalty_sales as $royalty_sales)
                {
                    ?>
                    <tr style="cursor: pointer;" royalty_id="<? echo $royalty_sales->id; ?>" class="royalty_sales">
                        <td align="center" ><? echo ++$x; ?></td>
                        <td align="center" ><? echo date("d-M-Y", strtotime($royalty_sales->date)); ?></td>
                        <td align="center" ><? echo $royalty_sales->customer_name; ?></td>
                        <td align="center" ><? echo $royalty_sales->booklet_name; ?></td>
                        <td align="center" ><? echo $royalty_sales->from_booklet_no; ?></td>
                        <td align="center" ><? echo $royalty_sales->to_booklet_no; ?></td>
                        <td align="center" ><? echo $royalty_sales->total_pages; ?></td>
                        <td align="center" ><? echo $royalty_sales->amount; ?></td>
                        <td align="center" ><? echo $royalty_sales->comments; ?></td>
                        
                        <!--<td>
                            <a href="index.php?option=com_amittrading&view=sales_invoice_print&tmpl=print&invoice_id=<? //echo intval($sales_invoice->id); ?>" target="_blank"><img src="custom/graphics/icons/blank.gif" class="print" title="Print Invoice"></a>
                            <a href="#" onclick="edit_sales_invoice(<? //echo $sales_invoice->id .",". $sales_invoice->order_id;?>);"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                            <a href="#" onclick="delete_sales_invoice(<? //echo $sales_invoice->id.",". $sales_invoice->order_id; ?>);"><img  src="custom/graphics/icons/blank.gif" class="delete" title="Delete Row"></a>
                        </td> -->
                    </tr>
                    <?
                }
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