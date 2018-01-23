<?php
    defined('_JEXEC') or die;
    $item_name = ""; 
    $category_name = ""; 
?>
<style>    
    tr.header th {
        background-color: #e0e0e0;
        border: 1px solid #c0c0c0;
    }
</style>
<script>
    j(function(){
        j("#from_date, #to_date").datepicker({"dateFormat" : "dd-M-yy" , changeMonth:true, changeYear:true});
        j("#item_id").chosen();
    });
    
    function show_item_sales_stats(validate)
    {
        if(validate)
        {
            if(j("#item_id").val() == 0 || j("#from_date").val() == "" || j("#to_date").val() == "")
            {
                alert("Select Filters.");
                return false;
            }
            else
            {
                go("index.php?option=com_amittrading&view=itemwise_sales_stats&item_id=" + j("#item_id").val() + "&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=itemwise_sales_stats");
        }
    }
</script>
<h1>Itemwise Sales Stats</h1>
<br />
<table>
    <tr>
        <td>Item : </td>
        <td>
            <select id="item_id" name="item_id" style="width:200px;">
                <option value="0"></option>    
                <?
                    if(count($this->items) > 0)
                    {
                        $category_id = 0;
                        foreach($this->items as $item)
                        {
                            if($this->item_id == $item->id)
                            {
                                $item_name = $item->item_name;
                                $category_name = $item->category_name;
                            }
                            
                            if($category_id != $item->category_id)
                            {
                                if($category_id != 0)
                                {
                                    ?></optgroup><?
                                }
                                ?>
                                <optgroup label="<? echo $item->category_name; ?>">
                                <?
                                $category_id = $item->category_id;
                            }
                            ?><option value="<? echo $item->id; ?>" <? echo ($this->item_id == $item->id ? "selected='selected'" : ""); ?> ><? echo $item->item_name; ?></option><?
                        }
                        ?></optgroup><?
                    }
                ?>
            </select>
        </td>
        <td>From Date : </td>
        <td><input type="text" id="from_date" value="<? echo ($this->from_date != "" ? date("d-M-Y", strtotime($this->from_date)) : "") ?>" readonly="readonly" style="width:80px;"></td>
        <td>To Date : </td>
        <td><input type="text" id="to_date" value="<? echo ($this->to_date != "" ? date("d-M-Y", strtotime($this->to_date)) : "") ?>" readonly="readonly" style="width:80px;"></td>
        <td>
            <input type="button" value="Refresh" onclick="show_item_sales_stats(1); return false;">
            <input type="button" value="Clear" onclick="show_item_sales_stats(0);">
        </td>
    </tr>
</table>
<?
    if($this->item_id > 0)
    {
        ?><a href="#" onclick="popup_print('<center>' + j('#item_sales_stats').html() + '</center>'); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><?
    }
?>
<div id="item_sales_stats">
    <?
        if($this->item_id > 0)
        {
            $closing_data = array();
            $location_header = "";
            $location_count = count($this->locations);
            ?>
            <h3>
                <?
                    echo $category_name . " " . $item_name;
                    
                    if(strtotime(($this->from_date)) == strtotime($this->to_date))
                    { echo " (" . date("d-M-Y", strtotime($this->from_date)) . ")"; }
                    else
                    { echo " (" . date("d-M-Y", strtotime($this->from_date)) . " to " . date("d-M-Y", strtotime($this->to_date)) . ")" ; }
                ?>
            </h3>
            <?
                foreach($this->locations as $location)
                {
                    $opening = 0;
                    $opening += (isset($this->opening_data->opening[$location->id]) ? $this->opening_data->opening[$location->id]->opening_stock : 0);
                    $opening += (isset($this->opening_data->purchase_opening[$location->id]) ? $this->opening_data->purchase_opening[$location->id]->opening_credit : 0);
                    $opening -= (isset($this->opening_data->purchase_return_opening[$location->id]) ? $this->opening_data->purchase_return_opening[$location->id]->opening_debit : 0);
                    $opening -= (isset($this->opening_data->sale_opening[$location->id]) ? $this->opening_data->sale_opening[$location->id]->opening_debit : 0);
                    $opening += (isset($this->opening_data->sale_return_opening[$location->id]) ? $this->opening_data->sale_return_opening[$location->id]->opening_credit : 0);
                    $opening += (isset($this->opening_data->stock_transfer_opening_credit[$location->id]) ? $this->opening_data->stock_transfer_opening_credit[$location->id]->opening_credit : 0);
                    $opening -= (isset($this->opening_data->stock_transfer_opening_debit[$location->id]) ? $this->opening_data->stock_transfer_opening_debit[$location->id]->opening_debit : 0);
                    
                    $closing_data[$location->id] = $opening;
                    $location_header .= "<th>" . $location->location_name . "</th>";
                }
            ?>
            <table class="clean">
                <tr class="header">
                    <th rowspan="2">Date</th>
                    <th rowspan="2">Particulars</th>
                    <th colspan="<? echo $location_count; ?>">Debit</th>
                    <th colspan="<? echo $location_count; ?>">Credit</th>
                    <th colspan="<? echo $location_count; ?>">Balance</th>
                </tr>
                <tr class="header">
                    <?
                        echo $location_header;
                        echo $location_header;
                        echo $location_header;
                    ?>
                </tr>
                <tr style="background-color:#eee; font-weight:bold;">
                    <td align="center" colspan="<? echo ($location_count * 2) + 2; ?>">Opening</td>
                    <?
                        foreach($this->locations as $location)
                        {
                            ?><td align="right"><? echo $closing_data[$location->id]; ?></td><?
                        }
                    ?>
                </tr>
                <?
                    if(count($this->locationwise_item_sales_stats) > 0)
                    {
                        foreach($this->locationwise_item_sales_stats as $stat)
                        {
                            $quantity = "";
                            ?>
                            <tr>
                                <td align="center"><? echo date("d-M-Y", strtotime($stat->date)); ?></td>
                                <td><? echo $stat->particular; ?></td>
                                <?
                                    if($stat->item_type == 'DEBIT')
                                    {
                                        foreach($this->locations as $location)
                                        {
                                            if($stat->location_id == $location->id)
                                            {
                                                $closing_data[$location->id] -= floatval($stat->quantity);
                                                $quantity = floatval($stat->quantity);
                                            }
                                            ?>
                                            <td align="right"><? echo $quantity; ?></td>
                                            <?
                                            $quantity = "";
                                        }
                                        
                                        for($i=0; $i<$location_count; $i++)
                                        {
                                            echo "<td></td>";
                                        }
                                    }
                                    else if($stat->item_type == 'CREDIT')
                                    {
                                        for($i=0; $i<$location_count; $i++)
                                        {
                                            echo "<td></td>";
                                        }
                                        
                                        foreach($this->locations as $location)
                                        {
                                            if($stat->location_id == $location->id)
                                            {
                                                $closing_data[$location->id] += floatval($stat->quantity);
                                                $quantity = floatval($stat->quantity);
                                            }
                                            ?>
                                            <td align="right"><? echo $quantity; ?></td>
                                            <?
                                            $quantity = "";
                                        }
                                    }
                                    else if($stat->item_type == 'ST')
                                    {
                                        foreach($this->locations as $location)
                                        {
                                            if($stat->location_from_id == $location->id)
                                            {
                                                $closing_data[$location->id] -= floatval($stat->quantity);
                                                $quantity = floatval($stat->quantity);
                                            }
                                            ?>
                                            <td align="right"><? echo $quantity; ?></td>
                                            <?
                                            $quantity = "";
                                        }
                                        foreach($this->locations as $location)
                                        {
                                            if($stat->location_to_id == $location->id)
                                            {
                                                $closing_data[$location->id] += floatval($stat->quantity);
                                                $quantity = floatval($stat->quantity);
                                            }
                                            ?>
                                            <td align="right"><? echo $quantity; ?></td>
                                            <?
                                            $quantity = "";
                                        }
                                    }
                                    
                                    foreach($this->locations as $location)
                                    {
                                        ?>
                                        <td align="right"><? echo $closing_data[$location->id]; ?></td>
                                        <?
                                    }
                                ?>
                            </tr>
                            <?
                        }
                    }
                    else
                    {
                        ?>
                        <tr>
                            <td align="center" colspan="<? echo ($location_count * 3) + 2; ?>"></td>
                        </tr>
                        <?
                    }
                ?>
                <tr style="background-color:#eee; font-weight:bold;">
                    <td align="center" colspan="<? echo ($location_count * 2) + 2; ?>">Closing</td>
                    <?
                        foreach($this->locations as $location)
                        {
                            ?><td align="right"><? echo $closing_data[$location->id]; ?></td><?
                        }
                    ?>
                </tr>
            </table>
            <?
        }
    ?>
</div>