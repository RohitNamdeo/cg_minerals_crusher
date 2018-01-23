<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        window.print();
    });
</script>
<h1>Salary Payment for <? echo date("F'Y", strtotime($this->data->salary_year . '-' . $this->data->salary_month . '-01')); ?></h1>
<table>
    <tr>
        <th align="right">Payment Date</th>
        <th> : </th>
        <td><? echo date("d-M-Y", strtotime($this->data->payment_date)); ?></td>
    </tr>
    <tr>
        <th align="right">Payment No.</th>
        <th> : </th>
        <td><? echo $this->data->payment_id; ?></td>
    </tr>
</table>
<br />
<div>
    <table class="clean centreheadings spread">
        <thead>
            <tr>
                <th>#</th>
                <th>Emp. Code</th>
                <th>Employee Name</th>
                <th>Location</th>
                <th>Instrument</th>
                <th>Instrument No.</th>
                <th>Instrument Bank</th>
                <th>Amount</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <?
            if(count($this->salary_payments) > 0)
            {
                $x = 0;
                $total_payment_amount = 0;
                foreach($this->salary_payments as $payment)
                {
                    $total_payment_amount += floatval($payment->amount);
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td align="center"><? echo $payment->employee_id; ?></td>
                        <td><? echo $payment->employee_name; ?></td>
                        <td><? echo $payment->location_name; ?></td>
                        <td align="center"><? echo ($payment->instrument == CASH ? "Cash" : "Cheque"); ?></td>
                        <td align="center"><? echo $payment->instrument_no; ?></td>
                        <td><? echo $payment->bank_name; ?></td>
                        <td align="right"><? echo $payment->amount; ?></td>
                        <td><? echo $payment->remarks; ?></td>
                    </tr>
                    <?
                }
                ?>
                <tr>
                    <td align="right" colspan="7"><b>Total :</b></td>
                    <td align="right"><b><? echo round_2dp($total_payment_amount);?></b></td>
                    <td></td>
                </tr>
                <?
            }
        ?>
    </table>
</div>