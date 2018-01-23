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
        window.print();
    });
</script>
<div id="account_statement">
    <table>
        <tr>
            <td align="right"><b><? echo ($this->type == 'c' ? "Customer " : ($this->type == 's' ? "Supplier " : "Transporter ")) . "Name"; ?> : </b></td>
            <td colspan="4"><? echo $this->party_name; ?></td>
        </tr>
        <tr>
            <td align="right"><b>Account Statement : </b></td>
            <td><b>From&nbsp;&nbsp;&nbsp;</b></td>
            <td><? echo date("d-M-Y", strtotime($this->from_date)); ?></td>
            <td><b>&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;&nbsp;</b></td>
            <td><? echo date("d-M-Y", strtotime($this->to_date)); ?></td>
        </tr>
    </table>
    <br />
    <?
        if(count($this->account_details) > 0)
        {
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
            <div style="float:right;"><b>Closing : <? echo round_2dp(abs($balance)) . ($balance == 0 ? "" : ($balance > 0 ? " Cr. (Payable)" : " Dr. (Advance)" ) ); ?></b></div>
            <?
        }
    ?>
</div>