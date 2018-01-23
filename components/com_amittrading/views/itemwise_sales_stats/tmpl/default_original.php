<?php
    defined('_JEXEC') or die; 
?>
<style>
    .pagination span, a {
        padding: 3px;
    }
    
    tr.footer td{
        background-color: #EFEFEF;
        border: 1px solid #c0c0c0;
        font-weight: bold;
    }
</style>
<script>
    j(function(){
        j("#from_date").datepicker({"dateFormat" : "dd-M-yy"});
        j("#customer_id").chosen();
    });
    
    j(document).on("change", "#from_date", function(){
        go("index.php?option=com_amittrading&view=itemwise_sales_stats&customer_id=" + j("#customer_id").val() + "&from_date=" + j("#from_date").val());
    });
    
    j(document).on("change", "#check_all", function(){
        if(j(this).is(":checked"))
        {
            j(".stat_checkbox").attr("checked", true);
        }
        else
        {
            j(".stat_checkbox").attr("checked", false);
        }
    });
    
    j(document).on("change", ".stat_checkbox", function(){
        if(j(".stat_checkbox:checked").length == j(".stat_checkbox").length)
        {
            j("#check_all").attr("checked", true);
        }
        else
        {
            j("#check_all").attr("checked", false);
        }
    });
    
    function show_item_sales_stats(validate)
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
                go("index.php?option=com_amittrading&view=itemwise_sales_stats&customer_id=" + j("#customer_id").val() + "&from_date=" + j("#from_date").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=itemwise_sales_stats");
        }
    }
    
    function view_item_sales_stats(d)
    {
        go("index.php?option=com_amittrading&view=itemwise_sales_stats&customer_id=" + j("#customer_id").val() + "&from_date=" + j("#from_date").val() + "&d=" + d);
    }
    
    function print_itemwise_sales_stat()
    {
        if(j(".stat_checkbox:checked").length == 0)
        {
            alert("Please select sales stats for printing."); return false;
        }
        else
        {
            j("#item_stats").submit();
        }
    }
</script>
<h1>Itemwise Sales Stats</h1>
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
            <button onclick="view_item_sales_stats('p');"><b>&lt; Previous</b></button>
            <input type="text" id="from_date" value="<? echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : "") ?>" readonly="readonly" style="width:80px;">
            <button onclick="view_item_sales_stats('n');"><b>&gt; Next</b></button>
        </td>
        <!--<td>To Date : </td>
        <td><input type="text" id="to_date" value="<? //echo ($this->to_date != "" ? date("d-M-Y", strtotime($this->to_date)) : "") ?>" style="width:80px;"></td>-->
        <td>
            <input type="button" value="Refresh" onclick="show_item_sales_stats(1); return false;">
            <input type="button" value="Clear" onclick="show_item_sales_stats(0);">
        </td>
    </tr>
</table>
<table width="400">
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
    if(count($this->item_sales_stats) > 0)
    {
        ?>
        <!--<a href="#" onclick="popup_print('<h1>Itemwise Sales Stats</h1><br />' + j('#item_sales_stats').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a>-->
        <a href="#" onclick="print_itemwise_sales_stat(); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a>
        <br /><br />
        <?
    }
?>
<div id="item_sales_stats">
    <form id="item_stats" action="index.php?option=com_amittrading&view=itemwise_sales_stats_print&tmpl=print" method="post" target="_blank">
        <input type="hidden" id="bill_date" name="bill_date" value="<? echo date("d-M-Y", strtotime($this->from_date)); ?>">
        <table class="clean centreheadings">
            <tr>
                <th>#</th>
                <th><input type="checkbox" id="check_all"></th>
                <th>Item</th>
                <th>Customer</th>
                <th width="50">Quantity</th>
                <th>Bill Details<br />Date(No.)</th>
            </tr>
            <?
                if(count($this->item_sales_stats) > 0)
                {
                    $x = $this->limitstart;
                    $item_id = 0;
                    $total_item_quantity = 0;
                    foreach($this->item_sales_stats as $key=>$stat)
                    {
                        if($item_id != 0 && $item_id != $stat->item_id)
                        {
                            ?>
                            <tr class="footer">
                                <td colspan="4" align="right">Total : </td>
                                <td align="right"><? echo $total_item_quantity; ?></td>
                                <td></td>
                            </tr>
                            <?
                        }
                        if($item_id != $stat->item_id)
                        {
                            $item_id = $stat->item_id;
                            $total_item_quantity = 0;
                        }
                        ?>
                        <tr>
                            <td align="center"><? echo ++$x; ?></td>
                            <td align="center">
                                <input type="checkbox" class="stat_checkbox" name="bill_nos[<? echo $key; ?>]" value="<? echo $stat->bill_nos; ?>">
                                <input type="hidden" name="item_ids[<? echo $key; ?>]" value="<? echo $stat->item_id; ?>">
                            </td>
                            <td><? echo $stat->item_name; ?></td>
                            <td><? echo $stat->customer_name; ?></td>
                            <td align="right"><? echo $stat->quantity; $total_item_quantity += $stat->quantity; ?></td>
                            <td><? echo $stat->bill_details; ?></td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr class="footer">
                        <td colspan="4" align="right">Total : </td>
                        <td align="right"><? echo $total_item_quantity; ?></td>
                        <td></td>
                    </tr>
                    <?
                }
                else
                {
                    ?><td colspan="6" align="center">No records to display.</td><?
                }
            ?>
        </table>
    </form>
</div>
<table width="400">
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