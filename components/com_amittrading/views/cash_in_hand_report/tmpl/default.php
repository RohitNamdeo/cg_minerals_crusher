<?php
    defined('_JEXEC') or die;
?>
<script>
    j(function(){
        j(".date_field").datepicker({"dateFormat" : "dd-M-yy"});
    });
    
    function get_records()
    {
        go("index.php?option=com_amittrading&view=cash_in_hand_report&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val());
    }
</script>
<h1>Cash Statement</h1>
<div id="cash_statement">
    <table>
        <tr>
            <td>From :</td>
            <td><input type="text" class="date_field" id="from_date" value="<? echo date("d-M-Y", strtotime($this->from_date)); ?>" /></td>
            <td>To :</td>
            <td><input type="text" class="date_field" id="to_date" value="<? echo date("d-M-Y", strtotime($this->to_date)); ?>" /></td>
            <td>
                <input type="button" value="Refresh" onclick="get_records();">
                <input type="button" value="Clear" onclick="go('index.php?option=com_amittrading&view=cash_in_hand_report');">
            </td>
        </tr>
    </table>
    <br />
    <?
        if(count($this->cash_statements) > 0)
        {
            ?>
            <div style="float:right;"><b>Opening : <? echo abs($this->opening_balance) . ($this->opening_balance == 0 ? "" : ($this->opening_balance > 0 ? " Cr." : " Dr." ) ); ?></b></div>
            <br /><br />
            <table class="clean spread centreheadings" id="account_details">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Particulars</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Balance</th>
                </tr>
                <?
                    $x = 0;
                    $balance = 0;
                    ($this->opening_balance > 0 ? $balance += abs($this->opening_balance) : $balance -= abs($this->opening_balance));
                    
                    foreach($this->cash_statements as $statement)
                    {
                        ?>
                        <tr>
                            <td align="center"><? echo ++$x; ?></td>
                            <td align="center"><? echo date("d-M-Y", strtotime($statement->date)); ?></td>
                            <td><? echo $statement->item_type; ?></td>
                            <td><? echo $statement->particulars; ?></td>
                            <?
                                if($statement->type == 'debit')
                                {
                                    $balance -= floatval($statement->amount);
                                    ?>
                                    <td align="right"><? echo round_2dp($statement->amount); ?></td>
                                    <td></td>
                                    <?
                                }
                                else if($statement->type == 'credit')
                                {
                                    $balance += floatval($statement->amount);
                                    ?>
                                    <td></td>
                                    <td align="right"><? echo round_2dp($statement->amount); ?></td>
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
            <div style="float:right;"><b>Closing : <? echo round_2dp(abs($balance)) . ($balance == 0 ? "" : ($balance > 0 ? " Cr." : " Dr." ) ); ?></b></div>
            <?
        }
        else
        {
            echo "No records found!";
        }
    ?>
</div>