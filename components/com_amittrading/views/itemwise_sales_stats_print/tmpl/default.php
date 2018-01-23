<?php
    defined('_JEXEC') or die; 
?>
<style>    
    tr.footer td{
        background-color: #EFEFEF;
        border: 1px solid #c0c0c0;
        font-weight: bold;
    }
    
    #stat_items td{
        padding: 0;
    }
    
    #stat_items .tdf td{
        border-bottom: none;
    }
    
    #stat_items .tdm td{
        border-top: none;
        border-bottom: none;
    }
    
    #stat_items .tdl td{
        border-top: none;
    }
</style>
<script>
    j(function(){
        window.print();
    });
</script>
<div id="item_sales_stats" align="center">
    <h1>Itemwise Sales Stats</h1>
    <br />
    <table class="clean centreheadings" id="stat_items">
        <tr>
            <!--<th>#</th>-->
            <th>Item</th>
            <th>Customer</th>
            <th width="50">Quantity</th>
            <th>Bill Details<br />Date(No.)</th>
        </tr>
        <?
            if(count($this->item_sales_stats) > 0)
            {
                $x = 1;
                $item_id = 0;
                $total_item_quantity = 0;
                foreach($this->item_sales_stats as $key=>$stat)
                {
                    $td_class = "";
                    
                    if($item_id != 0 && $item_id != $stat->item_id)
                    {
                        ?>
                        <tr class="footer">
                            <td colspan="2" align="right">Total : </td>
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
                    
                    if($item_id != @$this->item_sales_stats[$key - 1]->item_id && $item_id == @$this->item_sales_stats[$key + 1]->item_id)
                    {
                        $td_class = "tdf";
                    }
                    else if($item_id == @$this->item_sales_stats[$key - 1]->item_id && $item_id == @$this->item_sales_stats[$key + 1]->item_id)
                    {
                        $td_class = "tdm";
                    }
                    else if($item_id == @$this->item_sales_stats[$key - 1]->item_id && $item_id != @$this->item_sales_stats[$key + 1]->item_id)
                    {
                        $td_class = "tdl";
                    }
                    ?>
                    <tr class="<? echo $td_class; ?>">
                        <!--<td align="center"><? //echo $x++; ?></td>-->
                        <td><? echo $stat->item_name; ?></td>
                        <td><? echo $stat->customer_name; ?></td>
                        <td align="right"><? echo $stat->quantity; $total_item_quantity += $stat->quantity; ?></td>
                        <td><? echo $stat->bill_details; ?></td>
                    </tr>
                    <?
                }
                ?>
                <tr class="footer">
                    <td colspan="2" align="right">Total : </td>
                    <td align="right"><? echo $total_item_quantity; ?></td>
                    <td></td>
                </tr>
                <?
            }
        ?>
    </table>
</div>