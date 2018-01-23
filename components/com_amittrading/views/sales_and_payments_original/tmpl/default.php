<?php
    defined('_JEXEC') or die; 
?>
<style>
    .unpaid_bill{
        color: red;
    }
    
    img{
        vertical-align: middle;
    }
    
    #sales td, #payments td {
        padding: 1px;
    }
</style>
<script>
    j(function(){
        j("input[type='button']").button();
        j("#total_pending_amount").hide();
    });
    
    j(document).on("click", ".pending_bills", function(){
        if(j(".pending_bills:checked").length)
        {
            total_pending_amount = 0;
            j(".pending_bills:checked").each(function(){
                total_pending_amount += parseFloat(j(this).val());
            });
            j("#total_pending_amount").html("<b>Total Pending Amount : </b>" + total_pending_amount + "/-");
            j("#total_pending_amount").show();
        }
        else
        {
            j("#total_pending_amount").hide();
        }
    });
    
    j(document).on("click", ".action_column", function(){
        j.colorbox.remove();
    });
    
    j(document).on("click", ".sales_return_items", function(e){
        e.preventDefault();
    }); 
    
    function show_items(sales_id)
    {
        j.colorbox({href:"index.php?option=com_amittrading&view=sales_items&sales_id=" + sales_id + "&tmpl=xml", maxWidth: "100%", maxHeight: "100%", "open" : true});
        return false;
    } 
    
    function highlight_bills(payment_id)
    {
        j(".payments").closest("table").find(".clickedRow").removeClass('clickedRow');
        j("#payment_" + payment_id).addClass('clickedRow');
        var payment_type = j("#payment_" + payment_id).attr("payment_type");
        
        j(".unpaid_bill").css({"color" : "red"});
        j(".paid_bill").css({"color" : "black"});
        
        j.get("index.php?option=com_amittrading&task=get_bills&tmpl=xml&payment_id=" + payment_id + "&payment_type=" + payment_type, function(data){
            if(data != "")
            {
                bills = j.parseJSON(data);
                var bills = j.makeArray(bills);                
                
                for(i=0; i<bills.length; i++)
                {
                    bill_amount = j("#bill_" + bills[i].invoice_id).find(".bill_amount").html();
                    
                    if(bill_amount == bills[i].amount)
                    { j("#bill_" + bills[i].invoice_id).css({"color" : "blue"}); }
                    else
                    { j("#bill_" + bills[i].invoice_id).css({"color" : "#D21CD7"}); }
                }
            }
        });
    }
    
    function delete_sales_invoice(sales_id)
    {
        if(confirm("Are you sure to delete the sales invoice?"))
        {
            go("index.php?option=com_amittrading&task=delete_sales_invoice&sales_id=" + sales_id + "&customer_id=<? echo $this->customer_id; ?>");
        }
        else
        {
            return false;
        }
    }
    
    function delete_customer_payment(payment_id)
    {
        if(confirm("Are you sure to delete the payment?"))
        {
            go("index.php?option=com_amittrading&task=delete_customer_payment&payment_id=" + payment_id + "&customer_id=<? echo $this->customer_id; ?>");
        }
        else
        {
            return false;
        }
    }
    
    function show_all(type)
    {
        if(type == 'b')
        {
            var bill = "all";
            var pay = j("#pay").val();
        }
        else if(type == 'p')
        {
            var pay = "all";
            var bill = j("#bill").val();
        }
        
        j.get("index.php?option=com_amittrading&view==sales_and_payments&tmpl=xml&customer_id=<? echo $this->customer_id; ?>&bill=" + bill + "&pay=" + pay, function(data){
            if(data != "")
            {
                j("#sales_and_payments").html(j(data).filter("#sales_and_payments").html());
                j("input[type='button']").button();
            }
        });
    }
    
    function show_sales_return_items(return_id)
    {
        j.colorbox({href:"index.php?option=com_amittrading&view=sales_return_items&sales_return_id=" + return_id + "&tmpl=xml", maxWidth: "100%", maxHeight: "100%", "open" : true});
        return false;
    }
</script>
<div id="sales_and_payments">
    <input type="hidden" id="bill" value="<? echo $this->show_all_bills; ?>">
    <input type="hidden" id="pay" value="<? echo $this->show_all_pays; ?>">
    <table>
        <tr>
            <td valign="top">
                <div style="padding-bottom:29px;">
                    <span style="float:left;"><input type="button" value="Show All Bills" onclick="show_all('b'); return false;"></span>
                    <span style="float:right;">
                        <?
                            if($this->customer_account_status == AC_ACTIVE)
                            {
                                ?><input type="button" value="Create Bill" onclick="go('index.php?option=com_amittrading&view=sales_invoice&customer_id=<? echo $this->customer_id; ?>');"><?
                            }
                        ?>
                    </span>
                </div>
                <br />
                <table class="clean centreheadings" id="sales">
                    <tr>
                        <!--<th>#</th>-->
                        <th></th>
                        <th>Bill No.</th>
                        <th>Bill Date</th>
                        <th>Amount</th>
                        <th>Amount Paid</th>
                        <th>Credit Day(s)</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                    <?
                        if(count($this->bills) > 0)
                        {
                            $x = 1;
                            foreach($this->bills as $bill)
                            {
                                ?>
                                <tr class="<? echo ($bill->status == UNPAID ? "unpaid_bill" : "paid_bill"); ?>" id="bill_<? echo $bill->sales_id; ?>" onclick="show_items(<? echo $bill->sales_id; ?>);" style="cursor: pointer;">
                                    <!--<td align="center"><? //echo $x++; ?></td>-->
                                    <td align="center" class="action_column">
                                        <?
                                            if($bill->status == UNPAID)
                                            {
                                                ?><input type="checkbox" class="pending_bills" value="<? echo round_2dp($bill->bill_amount - $bill->amount_paid); ?>"><?
                                            }
                                        ?>
                                    </td>
                                    <td><? echo $bill->bill_no; ?></td>
                                    <td align="center"><? echo date("d-M-Y", strtotime($bill->bill_date)); ?></td>
                                    <td align="right" class="bill_amount"><? echo round_2dp($bill->bill_amount); ?></td> 
                                    <td align="right"><? echo round_2dp($bill->amount_paid); ?></td> 
                                    <td align="center"><? echo $bill->credit_days; ?></td> 
                                    <td>
                                        <?
                                            if($bill->status == PAID) { echo "Paid"; }
                                            else if($bill->status == UNPAID) { echo "Unpaid"; }
                                        ?>
                                    </td>
                                    <td><? echo $bill->remarks; ?></td>
                                    <!--<td align="center">
                                        <input type="button" value="Actions" data-dropdown="#action-dropdown<? //echo $bill->sales_id; ?>"/>
                                        <div id="action-dropdown<?php //echo $bill->sales_id ;?>" class="dropdown dropdown-tip dropdown-anchor-left" align="left">
                                            <ul class="dropdown-menu">
                                                <li><a href="index.php?option=com_amittrading&view=sales_invoice_print&tmpl=print&invoice_id=<? //echo $bill->sales_id; ?>" target="_blank"><img src="custom/graphics/icons/blank.gif" class="print" title="Print Invoice">&nbsp;Print Invoice</a></li>
                                                <?
                                                    /*if(floatval($bill->amount_paid) == 0 && is_admin())
                                                    {
                                                        ?>
                                                        <li class="dropdown-divider"></li>
                                                        <li><a href="index.php?option=com_amittrading&view=sales_invoice&m=e&sales_id=<? echo $bill->sales_id; ?>&r=<? echo base64_encode("index.php?option=com_amittrading&view=customer_account&customer_id=" . $this->customer_id); ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit">&nbsp;Edit Invoice</a></li>
                                                        <li class="dropdown-divider"></li>
                                                        <li><a href="#" onclick="delete_sales_invoice(<? echo $bill->sales_id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete">&nbsp;Delete Invoice</a></li>
                                                        <?
                                                    }*/
                                                ?>
                                            </ul>
                                        </div>
                                    </td>-->
                                    <td class="action_column">
                                        <a href="index.php?option=com_amittrading&view=sales_invoice_print&tmpl=print&invoice_id=<? echo $bill->sales_id; ?>" target="_blank" onclick="go('index.php?option=com_hr&view=dashboard');"><img src="custom/graphics/icons/blank.gif" class="print" title="Print Invoice"></a>
                                        <?
                                            //if( (floatval($bill->amount_paid) == 0 || $bill->cash_invoice == YES) && is_admin() )
                                            if( is_admin() && $this->customer_account_status == AC_ACTIVE )
                                            {
                                                ?>
                                                <a href="index.php?option=com_amittrading&view=sales_invoice&m=e&sales_id=<? echo $bill->sales_id; ?>&r=<? echo base64_encode("index.php?option=com_amittrading&view=customer_account&customer_id=" . $this->customer_id); ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                                                <a href="#" onclick="delete_sales_invoice(<? echo $bill->sales_id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete"></a>
                                                <?
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?
                            }
                        }
                        else
                        {
                            ?>
                            <tr>
                                <td colspan="9" align="center">No records to display.</td>
                            </tr>
                            <?
                        }
                    ?>
                </table>
                <br />
                <div id="total_pending_amount"></div>
            </td>
            <td width="20"></td>
            <td valign="top">
                <div style="padding-bottom:29px;">
                    <span style="float:left;"><input type="button" value="Show All Payments" onclick="show_all('p'); return false;"></span>
                    <span style="float:right;">
                        <?
                            if($this->customer_account_status == AC_ACTIVE)
                            {
                                ?><input type="button" value="Receive Payment" onclick="go('index.php?option=com_amittrading&view=customer_payment&customer_id=<? echo $this->customer_id; ?>');"><?
                            }
                        ?>
                    </span>
                </div>
                <br />
                <table class="clean centreheadings" id="payments">
                    <tr>
                        <!--<th>#</th>-->
                        <th>Receipt No.</th>
                        <th>Payment Date</th>
                        <th>Mode</th>
                        <th>Cheque No.</th>
                        <th>Cheque Date</th>
                        <th>Cheque Bank</th>
                        <th>Bank Account</th>
                        <th>Amount Received</th>
                        <th>Discount/<br />Claim</th>
                        <!--<th>Credit Reason</th>-->
                        <th>Total Amount</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                    <?
                        if(count($this->payments) > 0)
                        {
                            $x = 1;
                            foreach($this->payments as $payment)
                            {
                                ?>
                                <tr class="payments" id="payment_<? echo $payment->payment_id; ?>" onclick="highlight_bills(<? echo $payment->payment_id; ?>);" payment_type="<? echo $payment->type; ?>" style="cursor:pointer;">
                                    <!--<td align="center"><? //echo $x++; ?></td>-->
                                    <td><? echo ($payment->type == 'SR' ? "SR-" : "") . $payment->payment_id; ?></td>
                                    <td align="center"><? echo date("d-M-Y", strtotime($payment->payment_date)); ?></td>
                                    <td>
                                        <?
                                            if($payment->payment_mode == CASH) { echo "Cash"; }
                                            else if($payment->payment_mode == CHEQUE) { echo "Cheque"; }
                                        ?>
                                    </td>
                                    <?
                                        if($payment->payment_mode == CHEQUE)
                                        {
                                            ?>
                                            <td align="center"><? echo $payment->cheque_no; ?></td>
                                            <td align="center"><? echo date("Y-m-d", strtotime($payment->cheque_date)); ?></td>
                                            <td><? echo $payment->cheque_bank; ?></td>
                                            <td><? echo $payment->account_name . ", " . $payment->bank_name; ?></td>
                                            <?
                                        }
                                        else
                                        {
                                            ?>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <?
                                        }
                                    ?>
                                    <td align="right"><? echo round_2dp($payment->amount_received); ?></td>
                                    <td align="right"><? echo round_2dp($payment->credit_amount); ?></td>
                                    <!--<td><? //echo $payment->credit_reason; ?></td>-->
                                    <td align="right"><? echo round_2dp($payment->total_amount); ?></td>
                                    <td><? echo $payment->remarks; ?></td>
                                    <td>
                                        <?
                                            if($payment->type == 'P')
                                            {
                                                ?>
                                                <a href="index.php?option=com_amittrading&view=customer_payment_print&tmpl=print&payment_id=<? echo $payment->payment_id; ?>" target="_blank" onclick="go('index.php?option=com_hr&view=dashboard');"><img src="custom/graphics/icons/blank.gif" class="print" title="Payment Receipt"></a>
                                                <?
                                                    if(is_admin() && $payment->cash_invoice == NO && $this->customer_account_status == AC_ACTIVE && $this->allow_edit_delete_payment == true)
                                                    {
                                                        ?>
                                                        <a href="index.php?option=com_amittrading&view=customer_payment&m=e&payment_id=<? echo $payment->payment_id; ?>&customer_id=<? echo $this->customer_id; ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                                                        <a href="#" onclick="delete_customer_payment(<? echo $payment->payment_id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete"></a>
                                                        <?
                                                    }
                                                ?>
                                                <?
                                            }
                                            else if($payment->type == 'SR')
                                            {
                                                if(is_admin())
                                                {
                                                    ?><a href="#" onclick="show_sales_return_items(<? echo $payment->payment_id; ?>); return false;" class="sales_return_items"><img src="custom/graphics/icons/blank.gif" class="view" title="View"></a><?
                                                }
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?
                            }
                        }
                        else
                        {
                            ?>
                            <tr>
                                <td colspan="12" align="center">No records to display.</td>
                            </tr>
                            <?
                        }
                    ?>
                </table>
            </td>
        </tr>
    </table>
</div>