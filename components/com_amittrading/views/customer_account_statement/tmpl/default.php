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
        j.get("index.php?option=com_amittrading&view=customer_account_statement&tmpl=xml&customer_id=<? echo $this->customer_id; ?>&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val(), function(data){
            if(data != "")
            {
                j("#account_statement").html(j(data).filter("#account_statement").html());
            }
        });
    }
    
    function clear_records()
    {
        j.get("index.php?option=com_amittrading&view=customer_account_statement&tmpl=xml&customer_id=<? echo $this->customer_id; ?>", function(data){
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
        if(count($this->account_details) > 0)
        {
            ?>
            <div style="float:right;"><b>Opening : <? echo round_2dp(abs($this->opening_balance)) . (round_2dp(abs($this->opening_balance)) == 0 ? "" : ($this->opening_balance > 0 ? " Cr." : " Dr." ) ); ?></b></div>
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
                    
                    foreach($this->account_details as $details)
                    {
                        ?>
                        <tr class="<? echo ($details->type == 'payment' ? "payment_row" : ""); ?>">
                            <td align="center"><? echo ++$x; ?></td>
                            <td align="center"><? echo date("d-M-Y", strtotime($details->date)); ?></td>
                            <td><? echo $details->particulars; ?></td>
                            <?
                                if($details->type == 'payment')
                                {
                                    $balance -= floatval($details->amount);
                                    ?>
                                    <td></td>
                                    <td align="right"><? echo round_2dp($details->amount); ?></td>
                                    <?
                                }
                                else if($details->type == 'return')
                                {
                                    $balance -= floatval($details->amount);
                                    ?>
                                    <td></td>
                                    <td align="right"><? echo round_2dp($details->amount); ?></td>
                                    <?
                                }
                                else if($details->type == 'bill')
                                {
                                    $balance += floatval($details->amount);
                                    ?>
                                    <td align="right"><? echo round_2dp($details->amount); ?></td>
                                    <td></td>
                                    <?
                                }
                            ?>
                            <td align="right"><? echo round_2dp(abs($balance)) . ($balance == 0 ? "" : ($balance > 0 ? " Cr." : " Dr." ) ); ?></td>
                        </tr>
                        <?
                    }
                ?>
            </table>
            <br />
            <div style="float:right;"><b>Closing : <? echo round_2dp(abs($balance)) . (round_2dp(abs($balance)) == 0 ? "" : ($balance > 0 ? " Cr. (Payable)" : " Dr. (Advance)" ) ); ?></b></div>
            <br /><br />
            <div style="float:right;"><input type="button" value="Print" onclick="window.open('index.php?option=com_amittrading&view=account_statement_print&tmpl=print&type=c&party_id=<? echo $this->customer_id; ?>&from_date=<? echo $this->from_date; ?>&to_date=<? echo $this->to_date; ?>');go('index.php?option=com_hr&view=dashboard');"></div>
            <br /><br />            
            <?
        }
    ?>
</div>