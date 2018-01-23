<?php
    defined('_JEXEC') or die( 'Restricted access' );
?>
<style>
    /*#customer_payment{
        width: 500px;
        height: 100%;
    }*/
</style>
<script>
    j(function(){
        amount_received = <? echo round_2dp($this->payment->amount_received); ?>;        
        amount_in_words = convert_number(amount_received);
        
        if(amount_in_words != 0)
        {
            amount_in_words = "Rupees " + amount_in_words.toLowerCase() + " only";
        }
        else
        {
            amount_in_words = "";
            //amount_in_words = "NUMBER OUT OF RANGE!";
        }
        
        j("#amount_in_words").html(amount_in_words);
        
        account_balance = <? echo round_2dp($this->payment->account_balance); ?>;        
        amount_in_words = convert_number(account_balance);
        
        if(amount_in_words != 0)
        {
            amount_in_words = "Rupees " + amount_in_words.toLowerCase() + " only";
        }
        else
        {
            amount_in_words = "";
            //amount_in_words = "NUMBER OUT OF RANGE!";
        }
        
        j("#total_current_outstanding").html(amount_in_words);
        
        window.print();
    });
</script>
<!--<fieldset id="customer_payment">-->
    <table width="70%" align="center">
        <!--<tr>
            <td colspan="5" align="center">Shri Ganeshay Namah</td>
        </tr>-->
        <tr>
            <td colspan="5" align="center" style="line-height:3px;">
                Shri Ganeshay Namah
                <h2>CHHATTISGARH MINERALS</h2>
                <font size="2">MANUFACTURE AND SUPPLIER OF STONE CHIPS AND LIME</font><br /><br /><br /><br /><br />
                <font size="2">ADD:- LALADHURWA ROAD,VILLAGE - GUDELI RAIGARH(C. G.)</font>
            </td>
        </tr>
        <tr>
            <td valign="top">Mobile #</td>
            <td><? echo $this->mobile_no; ?></td>
            <td width="70%" colspan="3"></td>
            <!--<td width="45%"></td>
            <td valign="top">TIN : </td>
            <td valign="top" align="right"><? //echo $this->tin_no; ?></td>-->
        </tr>
    </table>
    <center>
        <table>
            <tr><td><u><b>Payment Receipt</b></u></td></tr>
        </table>
    </center>
    <center>
        <table width="70%" align="center">
            <tr valign="top">
                <td align="right">Customer : </td>
                <td><b><? echo $this->payment->customer_name; ?></b></td>
                <td width="60%"></td>
                <td width="35" align="right">Date : </td>
                <td align="left"><? echo date("d-M-Y", strtotime($this->payment->payment_date)) . " " . date("h:i A"); ?></td>
            </tr>
            <tr>
                <td align="right" valign="top" width="80">Address : </td>
                <td><? echo $this->payment->customer_address . ($this->payment->customer_address != '' ? ", " : "") . $this->payment->city; ?></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td align="right" valign="top" width="80">Mode : </td>
                <td><? echo ($this->payment->payment_mode == CASH ? "Cash" : "Cheque"); ?></td>
                <td colspan="3"></td>
            </tr>
            <?
                if($this->payment->payment_mode == CHEQUE)
                {
                    ?>
                    <tr>
                        <td align="right" valign="top" width="80">Cheque No. : </td>
                        <td><? echo $this->payment->cheque_no; ?></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td align="right" valign="top" width="80">Cheque Date : </td>
                        <td><? echo date("d-M-Y", strtotime($this->payment->cheque_date)); ?></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td align="right" valign="top" width="80">Bank : </td>
                        <td colspan="2"><? echo $this->payment->bank_name; ?></td>
                        <td colspan="2"></td>
                    </tr>
                    <?
                }
            ?>
            <tr>
                <td align="right" valign="top">Remarks : </td>
                <td><? echo $this->payment->remarks; ?></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td height="20" colspan="5"></td>
            </tr>
            <tr>
                <td align="right">Amount : </td>
                <td colspan="3"></td>
                <td align="right"><b><? echo round_2dp($this->payment->amount_received); ?></b></td>
            </tr>
            <tr>
                <td colspan="5" align="right"><span id="amount_in_words"></span></td>
            </tr>
            <tr>
                <td align="left" colspan="2">Total Current Outstanding : </td>
                <td colspan="2"></td>
                <td align="right"><b><? echo round_2dp($this->payment->account_balance); ?></b></td>
            </tr>
            <tr>
                <td colspan="5" align="right"><span id="total_current_outstanding"></span></td>
            </tr>
        </table>
        <div id="footer"><br /><? echo $this->invoice_footer; ?><br /><br /></div>
    </center>
<!--</fieldset>-->