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
    
    #purchases td, #payments td {
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
    
    function show_items(purchase_id)
    {
        j.colorbox({href:"index.php?option=com_amittrading&view=purchase_items&purchase_id=" + purchase_id + "&tmpl=xml", maxWidth: "100%", maxHeight: "100%", "open" : true});
        return false;
    }
    
    function highlight_bills(payment_id)
    {
        j(".payments").closest("table").find(".clickedRow").removeClass('clickedRow');
        j("#payment_" + payment_id).addClass('clickedRow');
        
        j(".unpaid_bill").css({"color" : "red"});
        j(".paid_bill").css({"color" : "black"});
        
        j.get("index.php?option=com_amittrading&task=get_bills&tmpl=xml&payment_id=" + payment_id + "&payment_type=P", function(data){
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
    
    function delete_purchase_invoice(purchase_id)
    {
        if(confirm("Are you sure to delete the purchase invoice?"))
        {
            go("index.php?option=com_amittrading&task=delete_purchase_invoice&purchase_id=" + purchase_id + "&supplier_id=<? echo $this->supplier_id; ?>");
        }
        else
        {
            return false;
        }
    }
    
    function delete_supplier_payment(payment_id)
    {
        if(confirm("Are you sure to delete the payment?"))
        {
            go("index.php?option=com_amittrading&task=delete_supplier_payment&payment_id=" + payment_id + "&supplier_id=<? echo $this->supplier_id; ?>");
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
        
        j.get("index.php?option=com_amittrading&view==purchases_and_payments&tmpl=xml&supplier_id=<? echo $this->supplier_id; ?>&bill=" + bill + "&pay=" + pay, function(data){
            if(data != "")
            {
                j("#purchases_and_payments").html(j(data).filter("#purchases_and_payments").html());
                j("input[type='button']").button();
            }
        });
    }
</script>
<div id="purchases_and_payments">
    <input type="hidden" id="bill" value="<? echo $this->show_all_bills; ?>">
    <input type="hidden" id="pay" value="<? echo $this->show_all_pays; ?>">
    <table>
        <tr>
            <td valign="top">
                <div style="padding-bottom:29px;">
                    <span style="float:left;"><input type="button" value="Show All Bills" onclick="show_all('b'); return false;"></span>
                    <span style="float:right;"><input type="button" value="Create Bill" onclick="go('index.php?option=com_amittrading&view=purchase_entry&supplier_id=<? echo $this->supplier_id; ?>');"></span>
                </div>
                <br />
                <table class="clean centreheadings" id="purchases">
                    <tr>
                        <th></th>
                        <th>Bill No.</th>
                        <th>Bill Date</th>
                        <th>Amount</th>
                        <th>Amount Paid</th>
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
                                <tr class="<? echo ($bill->status == UNPAID ? "unpaid_bill" : "paid_bill"); ?>" id="bill_<? echo $bill->purchase_id; ?>" <? echo ($bill->royalty_purchase_id == 0 ? "onclick='show_items(" .intval($bill->purchase_id) . ");'" : '');?> style="cursor: pointer;">
                                    <!--<td align="center"><? //echo $x++; ?></td>-->
                                    <td align="center" class="action_column">
                                        <?
                                            if($bill->status == UNPAID)
                                            {
                                                ?><input type="checkbox" class="pending_bills" value="<? echo round_2dp($bill->total_amount - $bill->amount_paid); ?>"><?
                                            }
                                        ?>
                                    </td>
                                    <td><? echo $bill->bill_no; ?></td>
                                    <td align="center"><? echo date("d-M-Y", strtotime($bill->bill_date)); ?></td>
                                    <td align="right" class="bill_amount"><? echo round_2dp($bill->total_amount); ?></td> 
                                    <td align="right"><? echo round_2dp($bill->amount_paid); ?></td> 
                                    <td>
                                        <?
                                            if($bill->status == PAID) { echo "Paid"; }
                                            else if($bill->status == UNPAID) { echo "Unpaid"; }
                                        ?>
                                    </td>
                                    <td><? echo $bill->remarks; ?></td>
                                   <!-- <td align="center">
                                        <?
                                            /*if(floatval($bill->amount_paid) == 0 && is_admin())
                                            {
                                                ?>
                                                <input type="button" value="Actions" data-dropdown="#action-dropdown<? echo $bill->purchase_id; ?>"/>
                                                <div id="action-dropdown<?php echo $bill->purchase_id ;?>" class="dropdown dropdown-tip dropdown-anchor-left" align="left">
                                                    <ul class="dropdown-menu">
                                                        <li><a href="index.php?option=com_amittrading&view=purchase_invoice&m=e&purchase_id=<? echo $bill->purchase_id; ?>&r=<? echo base64_encode("index.php?option=com_amittrading&view=supplier_account&supplier_id=" . $this->supplier_id); ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit">&nbsp;Edit Invoice</a></li>
                                                        <!--<li class="dropdown-divider"></li>
                                                        <li><a href="#" onclick="delete_purchase_invoice(<? //echo $bill->purchase_id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete">&nbsp;Delete Invoice</a></li>-->
                                                    </ul>
                                                </div>
                                                <?
                                            } */
                                        ?>
                                    </td> -->
                                    <td align="center" class="action_column">
                                        <?
                                            //if( is_admin() && ( ($bill->transporter_payment_mode == CREDIT && $bill->transportation_amount_paid == 0) || $bill->transporter_payment_mode != CREDIT ) )
                                            //{
                                            if($bill->royalty_purchase_id == 0)
                                            {
                                                ?>
                                                    <a href="index.php?option=com_amittrading&view=purchase_entry&m=e&purchase_id=<? echo $bill->purchase_id; ?>&r=<? echo base64_encode("index.php?option=com_amittrading&view=supplier_account&supplier_id=" . $this->supplier_id); ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                                                <?
                                            }    
                                            //}
                                            
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
                                <td colspan="8" align="center">No records to display.</td>
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
                    <span style="float:right;"><input type="button" value="Make Payment" onclick="go('index.php?option=com_amittrading&view=supplier_payment&supplier_id=<? echo $this->supplier_id; ?>');"></span>
                </div>
                <br />
                <table class="clean centreheadings" id="payments">
                    <tr>
                        <th>Receipt No.</th>
                        <th>Payment Date</th>
                        <th>Mode</th>
                        <th>Cheque No.</th>
                        <th>Cheque Date</th>
                        <th>Bank Account</th>
                        <th>Amount</th>
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
                                <tr class="payments" id="payment_<? echo $payment->payment_id; ?>" onclick="highlight_bills(<? echo $payment->payment_id; ?>);" style="cursor:pointer;">
                                    <!--<td align="center"><? //echo $x++; ?></td>-->
                                    <td><? echo $payment->payment_id; ?></td>
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
                                            <td><? echo $payment->account_name . ", " . $payment->bank_name; ?></td>
                                            <?
                                        }
                                        else
                                        {
                                            ?>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <?
                                        }
                                    ?>
                                    <td align="right"><? echo round_2dp($payment->total_amount); ?></td>
                                    <td><? echo $payment->remarks; ?></td>
                                    <td align="center">
                                        <?
                                            if(is_admin() && $this->allow_edit_delete_payment == true)
                                            {
                                                ?>
                                                <a href="index.php?option=com_amittrading&view=supplier_payment&m=e&payment_id=<? echo $payment->payment_id; ?>&supplier_id=<? echo $this->supplier_id; ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                                                <a href="#" onclick="delete_supplier_payment(<? echo $payment->payment_id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete"></a>
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
                                <td colspan="11" align="center">No records to display.</td>
                            </tr>
                            <?
                        }
                    ?>
                </table>
            </td>
        </tr>
    </table>
</div>