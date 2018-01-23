
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
</style>
<script>
    j(function(){
        j("input[type='button']").button();
        j("#total_pending_amount").hide();
    });
    
    j(document).on("change", "#check_all", function(){
        if(j(this).is(":checked"))
        {
            j(".pending_bills").attr("checked", true);
            total_pending_amount = 0;
            j(".pending_bills:checked").each(function(){ 
                total_pending_amount += parseFloat(j(this).closest("tr").find(".bill_amount").text()); 
                //total_pending_amount += parseFloat(j(this).val());
            });
            j("#total_pending_amount").html("<b>Total Pending Amount : </b>" + total_pending_amount + "/-");
            j("#total_pending_amount").show();
        }
        else
        {
            j(".pending_bills").attr("checked", false);
            j("#total_pending_amount").hide(); 
        }
    });
    
     j(document).on("change", ".pending_bills", function(){
        if(j(".pending_bills:checked").length == j(".pending_bills").length)
        {   
            j("#check_all").attr("checked", true);  
        }
        else
        {   
            j("#check_all").attr("checked", false);
        }
    });  
    
    j(document).on("click", ".pending_bills", function(){
        if(j(".pending_bills:checked").length)
        {
            total_pending_amount = 0;
            j(".pending_bills:checked").each(function(){ 
                total_pending_amount += parseFloat(j(this).closest("tr").find(".bill_amount").text()); 
                //total_pending_amount += parseFloat(j(this).val());
            });
            j("#total_pending_amount").html("<b>Total Pending Amount : </b>" + total_pending_amount + "/-");
            j("#total_pending_amount").show();
        }
        else
        {
            j("#total_pending_amount").hide();
        }
    });
    
    function highlight_bills(payment_id)
    {
        j(".payments").closest("table").find(".clickedRow").removeClass('clickedRow');
        j("#payment_" + payment_id).addClass('clickedRow');
        
        j(".unpaid_bill").css({"color" : "red"});
        j(".paid_bill").css({"color" : "black"});
        
        j.get("index.php?option=com_amittrading&task=get_bills&type=t&tmpl=xml&payment_id=" + payment_id, function(data){
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
    
    function delete_transporter_payment(payment_id)
    {
        if(confirm("Are you sure to delete the payment?"))
        {
            go("index.php?option=com_amittrading&task=delete_transporter_payment&payment_id=" + payment_id + "&transporter_id=<? echo $this->transporter_id; ?>");
        }
        else
        {
            return false;
        }
    }
    
    function get_records()
    {
        j.get("index.php?option=com_amittrading&view=transports_and_payments&tmpl=xml&transporter_id=<? echo $this->transporter_id; ?>&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val(), function(data){
            if(data != "")
            {
                j("#transports_and_payments").html(j(data).filter("#transports_and_payments").html());
            }
        });
    }
    
    function clear_records()
    {
        j.get("index.php?option=com_amittrading&view=transports_and_payments&tmpl=xml&transporter_id=<? echo $this->transporter_id; ?>", function(data){
            if(data != "")
            {
                j("#transports_and_payments").html(j(data).filter("#transports_and_payments").html());
            }
        });
    }
    
    function validateForm()
    {
        if(j(".pending_bills:checked").length)
        {
           j("#sales_invoices").submit(); 
        }
        else
        {
            go('index.php?option=com_amittrading&view=transporter_payment&transporter_id=<? echo $this->transporter_id; ?>');
            //alert("At least 1 invoice should be selected for payment.");
//            return false;
        }
    }
</script>
<div id="transports_and_payments" >
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
    <form id="sales_invoices" method="post" action="index.php?option=com_amittrading&view=transporter_payment&transporter_id=<? echo $this->transporter_id; ?>">   
        <table>
            <tr>
                <td valign="top">
                    <table class="clean centreheadings">
                        <tr>
                            <th>#</th>
                            <th width="20"><input type="checkbox" id="check_all"></th> 
                            <th>Bill No.</th>
                            <th>Bill Date</th>
                            <th>Amount</th>
                            <th>Cash Paid To Driver</th>
                            <th>Diesel Amount</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                        </tr>
                        <?
                            if(count($this->bills) > 0)
                            {
                                //echo "dgfdg";exit;
                                //print_r($this->bills);exit; 
                               
                                $x = 1;
                                $total_amount = 0; 
                                foreach($this->bills as $bill)
                                {
                                    ?>
                                        <tr class="<? echo ($bill->status == PAYMENT_ADJUSTED ? "paid_bill" : "unpaid_bill"); ?>" id="bill_<? echo $bill->id; ?>">          
                                        <td align="center"><? echo $x++; ?></td>
                                        <td align="center">
                                            <?
                                                if($bill->status == NOT_ADJUSTED)
                                                {
                                                    ?><input type="checkbox" name="invoices_id[]" class="pending_bills" value="<? echo $bill->id; ?>"><?
                                                }
                                            ?>
                                        </td>
                                        <td><? echo $bill->id; ?></td>
                                        <td align="center"><? echo date("d-M-Y", strtotime($bill->date)); ?></td>
                                        

                                        <td align="right" class="bill_amount"><? echo round_2dp($bill->amount); ?></td> 
                                        <td align="right"><? echo round_2dp($bill->cash_paid_to_driver); ?></td>
                                        <td align="right"><? echo round_2dp($bill->diesel_amount); ?></td>
                                        <td align="right"><? echo round_2dp($bill->status == PAYMENT_ADJUSTED ? $bill->amount : "0"); ?></td> 
                                        <td align="right"><? echo ($bill->status == PAYMENT_ADJUSTED ? "Paid" : "Not Paid"); ?></td>
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
                </form>
                <br />
                <div id="total_pending_amount"></div>
                <!--<div> -->
                <? //if(count($this->bills) > 0)
//                    {
                        ?>
                        <!--<input type="button" value="Invoices Payment" onclick="validateForm(); return false;">    -->
                        <?
                    //}?>
                    
                <!--</div>  -->
            </td>
            <td width="20"></td>
            <td valign="top">                                     
                <div>
                    <!--<input type="button" value="Make Payment" onclick="go('index.php?option=com_amittrading&view=transporter_payment&transporter_id=<? //echo $this->transporter_id; ?>');">-->
                    <input type="button" value="Make Payment" onclick="validateForm(); return false;">
                </div>
                <br />
                <table class="clean centreheadings">
                    <tr>
                        <th>#</th>
                        <th>Receipt No.</th>
                        <th>Payment Date</th>
                        <th>Amount Paid</th>
                        <th>Discount</th>
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
                                <tr class="payments" id="payment_<? echo $payment->id; ?>" onclick="highlight_bills(<? echo $payment->id; ?>);" style="cursor:pointer;">
                                    <td align="center"><? echo $x++; ?></td>
                                    <td><? echo $payment->id; ?></td>
                                    <td align="center"><? echo date("d-M-Y", strtotime($payment->payment_date)); ?></td>
                                    <td align="right"><? echo round_2dp($payment->amount_paid); ?></td>
                                    <td align="right"><? echo $payment->discount; ?></td>
                                    <td align="right"><? echo $payment->total_amount; ?></td> 
                                    <td><? echo $payment->remarks; ?></td>
                                    <td align="center">
                                        <?
                                            if(is_admin() && $payment->payment_type == CREDIT)
                                            {
                                                ?>
                                                <a href="index.php?option=com_amittrading&view=transporter_payment&m=e&payment_id=<? echo $payment->payment_id; ?>&transporter_id=<? echo $this->transporter_id; ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                                                <a href="#" onclick="delete_transporter_payment(<? echo $payment->payment_id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" title="Delete"></a>
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
                                <td colspan="6" align="center">No records to display.</td>
                            </tr>
                            <?
                        }
                    ?>
                </table>
            </td>
        </tr>
    </table>
</div>