<?php
    defined('_JEXEC') or die; 
?>
<style>
    #inventory{
        padding: 15px;
    }
</style>
<div id="inventory">
    <h1>Inventory</h1>
    <table class="clean">
        <tr>
            <td>Item Name</td>
            <td><? echo $this->item->item_name; ?></td>
        </tr>
        <tr>
            <td>Category</td>
            <td><? echo $this->item->category_name; ?></td>
        </tr>
        <tr>
            <td>HSN Code</td>
            <td><? echo $this->item->hsn_code; ?></td>
        </tr>
        <tr>
            <td>GST Percent</td>
            <td><? echo $this->item->gst_percent; ?></td>
        </tr>
        <tr>
            <td>Last Purchase Rate</td>
            <td><? echo $this->item->last_purchase_rate; ?></td>
        </tr>
        <tr>
            <td>Piece/pack</td>
            <td><? echo $this->item->piece_per_pack; ?></td>
        </tr>
    </table>
    <br /><br />
    <?
        if(count($this->inventories) > 0)
        {
            ?>
            <table class="clean centreheadings">
                <tr>
                    <th>#</th>
                    <th>Location</th>
                    <th>Opening Stock</th>
                    <th>Stock</th>
                </tr>
                <?
                    $i = 1;
                    foreach($this->inventories as $inventory)
                    {
                        ?>
                        <tr>
                            <td align="center"><? echo $i++; ?></td>
                            <td><? echo $inventory->location_name; ?></td>
                            <td><? echo $inventory->opening_stock; ?></td>
                            <td><? echo $inventory->stock; ?></td>
                        </tr>
                        <?
                    }
                ?>
            </table>
            <?
        }
        else
        {
            echo "Stock details not found.";
        }
    ?>
</div>