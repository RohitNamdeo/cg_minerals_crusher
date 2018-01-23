<?php
    defined('_JEXEC') or die; 
?>
<style>
    #collection_items td{
        padding: 0;
    }
    
    #collection_items .tdf td{
        border-bottom: none;
    }
    
    #collection_items .tdm td{
        border-top: none;
        border-bottom: none;
    }
    
    #collection_items .tdl td{
        border-top: none;
    }
</style>
<script>
    j(function(){
        window.print();
    });
</script>
<div id="collection_report" align="center">
    <!--<h1>Collection Report</h1>
    <br />-->
    <table class="clean centreheadings" id="collection_items">
        <tr>
            <!--<th>#</th>-->
            <th width="65">Bill Date</th>
            <th width="60">Bill Amt.</th>
            <th width="60">Amt. Paid</th>
            <th width="60">Pending</th>
        </tr>
        <?
            if(count($this->collections) > 0)
            {
                $x = 1;
                $customer_id = 0;
                $customerwise_pending_amount = 0;
                //$address = array();
                foreach($this->collections as $key=>$collection)
                {
                    $td_class = "";
                    if($customer_id != $collection->customer_id)
                    {
                        $customer_id = $collection->customer_id;
                        $customerwise_pending_amount = 0;
                        //$address = explode(" ", $collection->customer_address);
                        $customer_name = explode(" ", $collection->customer_name);
                        $display_name = $customer_name[0] . (isset($customer_name[1]) ? " " . substr($customer_name[1], 0 ,1) : "");
                        $x = 1;
                        ?>
                        <tr>
                            <!--<td colspan="5" align="center"><b><? //echo $collection->customer_name . ($address[0] != "" ? "," . $address[0] . "..." : "") . ($collection->contact_no != "" ? "(" . $collection->contact_no . ")" : ""); ?></b></td>-->
                            <td colspan="5" align="center"><b><? echo ucwords(strtolower($display_name)) . ($collection->customer_address != "" ? ", " . ucfirst(strtolower($collection->customer_address)) . ", " . ucwords(strtolower($collection->city)) : ", " . ucwords(strtolower($collection->city))) . ($collection->contact_no != "" ? "(" . substr($collection->contact_no, 0, 10) . ")" : ""); ?></b></td>
                        </tr>
                        <?
                    }
                    
                    if($customer_id != @$this->collections[$key - 1]->customer_id && $customer_id == @$this->collections[$key + 1]->customer_id)
                    {
                        $td_class = "tdf";
                    }
                    else if($customer_id == @$this->collections[$key - 1]->customer_id && $customer_id == @$this->collections[$key + 1]->customer_id)
                    {
                        $td_class = "tdm";
                    }
                    else if($customer_id == @$this->collections[$key - 1]->customer_id && $customer_id != @$this->collections[$key + 1]->customer_id)
                    {
                        $td_class = "tdl";
                    }                    
                    ?>
                    <tr class="<? echo $td_class; ?>">
                        <!--<td align="center"><? //echo $x++; ?></td>-->
                        <td align="center"><? echo date("d-M-Y", strtotime($collection->bill_date)); ?></td>
                        <td align="right"><? echo round_2dp($collection->bill_amount); ?></td>
                        <td align="right"><? echo round_2dp($collection->amount_paid); ?></td>
                        <td align="right">
                            <b>
                            <?
                                $customerwise_pending_amount += round_2dp($collection->bill_amount - $collection->amount_paid);
                                echo round_2dp($customerwise_pending_amount);
                            ?>
                            </b>
                        </td>
                    </tr>
                    <?
                }
            }
        ?>
    </table>
</div>