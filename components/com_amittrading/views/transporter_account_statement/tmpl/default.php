<?php
    defined('_JEXEC') or die;
?>
<style>
    tr.payment_row{
        background-color: #F0F0F0;
    }
</style>
<script>
    j(function(){
        j(".date_field").datepicker({"dateFormat" : "dd-M-yy"});
        j("input[type='button']").button();
    });
    
    function get_records()
    {
        j.get("index.php?option=com_amittrading&view=transporter_account_statement&tmpl=xml&transporter_id=<? echo $this->transporter_id; ?>&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val(), function(data){
            if(data != "")
            {
                j("#account_statement").html(j(data).filter("#account_statement").html());
            }
        });
    }
    
    function clear_records()
    {
        j.get("index.php?option=com_amittrading&view=transporter_account_statement&tmpl=xml&transporter_id=<? echo $this->transporter_id; ?>", function(data){
            if(data != "")
            {
                j("#account_statement").html(j(data).filter("#account_statement").html());
            }
        });
    }
</script>
<div id="account_statement">
    <table>
        <tr>
            <td>From :</td>
            <td>
                <script>
                    j(function(){
                        j(".date_field").datepicker({"dateFormat" : "dd-M-yy"});
                        j("input[type='button']").button();
                    });
                </script>
                <input type="text" class="date_field" id="from_date" value="<? echo date("d-M-Y", strtotime($this->from_date)); ?>" />
            </td>
            <td>To :</td>
            <td><input type="text" class="date_field" id="to_date" value="<? echo date("d-M-Y", strtotime($this->to_date)); ?>" /></td>
            <td>
                <input type="button" value="Refresh" onclick="get_records();">
                <input type="button" value="Clear" onclick="clear_records();">
            </td>
        </tr>
    </table>
    <br />
    <?
         
        if(count($this->sales_details) > 0)
        {
         //print_r($this->account_details);   
            ?>
            <div style="float:right;"><b>Opening : <? echo abs($this->opening_balance) . ($this->opening_balance == 0 ? "" : ($this->opening_balance > 0 ? " Cr." : " Dr." ) ); ?></b></div>
            <br /><br />
            <table class="clean spread centreheadings" id="account_details">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Particulars</th>
                    <th>Bill</th>
                    <th>Payment</th>
                    <th>Balance</th>
                </tr>
                <?
                    $x = 0;
                    $balance = 0;
                    ($this->opening_balance > 0 ? $balance += abs($this->opening_balance) : $balance -= abs($this->opening_balance));
                    
                    foreach($this->sales_details as $details)
                    {
                        //print_r($details);
                        ?>
                        <tr class="<? echo ($details->type == 'payment' ? "payment_row" : ""); ?>">
                            <td align="center"><? echo ++$x; ?></td>
                            <td align="center"><? echo date("d-M-Y", strtotime($details->date)); ?></td>
                            <td><? echo ($details->type == 'payment' ? $details->particulars : "Bill No : ".$details->bill_no); ?></td>
                            <?
                                if($details->type == 'payment')
                                {
                                    //echo $details->type;
                                    $balance -= floatval($details->bill_amount_paid);
                                    ?>
                                    <td></td>
                                    <td align="right"><? echo round_2dp($details->bill_amount_paid); ?></td>
                                    <?
                                }
                                else if($details->type == 'bill')
                                {
                                    //echo $details->type; 
                                    $balance += floatval($details->bill_amount_paid);  
                                    ?>
                                    <td align="right"><? echo round_2dp($details->bill_amount_paid); ?></td>
                                    <td></td>
                                    <?
                                }
                                 //echo $balance."<br />";
                            ?>
                           
                            <td align="right"><? echo round_2dp(abs($balance)) . ($balance == 0 ? "" : ($balance > 0 ? " Cr." : " Dr." ) ); ?></td>
                        </tr>
                        <?
                    }
                ?>
            </table>
            <br />
            <div style="float:right;"><b>Closing : <? echo round_2dp(abs($balance)) . ($balance == 0 ? "" : ($balance > 0 ? " Cr. (Payable)" : " Dr. (Advance)" ) ); ?></b></div>
            <br /><br />
            <br /><br />            
            <?
        }
    ?>
</div>