<?php
    defined('_JEXEC') or die; 
    //collection report
?>
<style>
    .pagination span, a {
        padding: 3px;
    }
</style>
<script>
    j(function(){
        //j("#till_date").datepicker({"dateFormat" : "dd-M-yy"});
        j("#customer_id, #segment_id, #city_id").chosen();
        j(".scrollIntoView").scrollIntoView({
            rowSelector : 'collection',
            rowAttribute : 'sales_id',
            task : 'show_items'
        });
        
        j(".edit_textboxes").hide();
    });
    
    j(document).on("change", "#check_all", function(){
        if(j(this).is(":checked"))
        {
            j(".bill_checkbox").attr("checked", true);
        }
        else
        {
            j(".bill_checkbox").attr("checked", false);
        }
    });
    
    j(document).on("change", ".bill_checkbox", function(){
        if(j(".bill_checkbox:checked").length == j(".bill_checkbox").length)
        {
            j("#check_all").attr("checked", true);
        }
        else
        {
            j("#check_all").attr("checked", false);
        }
    });
    
    j(document).on("change", ".bill_checkbox", function(){
        j.colorbox.remove();
    });
    
    function show_collections(validate)
    {
        if(validate)
        {
            //if(j("#customer_id").val() == 0 && j("#city_id").val() == 0 && j("#till_date").val() == "")
            if(j("#customer_id").val() == 0 && j("#city_id").val() == 0 && j("#segment_id").val() == 0)
            {
                alert("Select filters.");
                return false;
            }
            else
            {
                go("index.php?option=com_amittrading&view=collection_report&customer_id=" + j("#customer_id").val() + "&segment_id=" + j("#segment_id").val() + "&city_id=" + j("#city_id").val() + "&till_date=" + j("#till_date").val());
            }
        }
        else
        {
            go("index.php?option=com_amittrading&view=collection_report");
        }
    }
    
    function show_items(sales_id)
    {
        j.colorbox({href:"index.php?option=com_amittrading&view=sales_invoice_items&sales_id=" + sales_id + "&tmpl=xml", maxWidth: "100%", maxHeight: "100%", "open" : true});
        return false;
    }
    
    function print_collection_report()
    {
        var sales_ids = new Array();
        
        if(j(".bill_checkbox:checked").length == 0)
        {
            alert("Please select bills for printing."); return false;
        }
        else
        {
            j(".bill_checkbox:checked").each(function(){
                sales_ids.push(j(this).val());
            });
            
            window.open("index.php?option=com_amittrading&view=collection_report_print&tmpl=print&s_ids=" + btoa(sales_ids));
        }
    }
    
    function send_sms()
    {
        var sales_ids = new Array();
        
        if(j(".bill_checkbox:checked").length == 0)
        {
            alert("Please select bills for sending payment reminder."); return false;
        }
        else
        {
            j(".bill_checkbox:checked").each(function(){
                sales_ids.push(j(this).val());
            });
            
            j.get("index.php?option=com_amittrading&task=send_payment_reminder_to_customers&tmpl=xml&s_ids=" + btoa(sales_ids), function(data){
                if(data == "ok")
                {
                    alert("Message sent successfully.");
                    j(".bill_checkbox, #check_all").attr("checked", false);
                }
                else
                {
                    alert("Some error occurred. Please try again!!.");
                }
            });
        }
    }
    
    function edit_collection_remarks(x)
    {
        toggle_collection_remarks(x);
    }

    function cancel_collection_remarks_edit(x)
    {
        toggle_collection_remarks(x);
    }

    function save_collection_remarks(customer_id, x)
    {
        var collection_remarks = j("#collection_remarks" + x).val();
        
        j.get("index.php?option=com_master&task=save_collection_remarks&tmpl=xml&customer_id=" + customer_id + "&collection_remarks=" + collection_remarks + "&x=" + x, function(data){
            var response = j.parseJSON(data)
            if(response.success == true)
            {
                toggle_collection_remarks(x);
                j(".customer_collection_remark" + customer_id).html(response.data);
                j(".customer_collection_remark" + customer_id).attr("title", collection_remarks);
                
                j(".customer_collection_remark" + customer_id).each(function(){
                    index = parseFloat(j(this).closest("tr").find(".index").html()) + 1;
                    j(this).find("#text_mode" + x).attr("id", "text_mode" + index);
                    j(this).find("#edit_mode" + x).attr("id", "edit_mode" + index);
                    j(this).find("#collection_remarks" + x).attr("id", "collection_remarks" + index);
                    j(this).find("#text_mode" + index).find("a").attr("onclick", "edit_collection_remarks(" + index + ");");
                    j(this).find("#edit_mode" + index).find(".link1").attr("onclick", "save_collection_remarks(" + customer_id + ", " + index + "); return false;");
                    j(this).find("#edit_mode" + index).find(".link2").attr("onclick", "cancel_collection_remarks_edit(" + index + "); return false;");
                });
            }
            else
            {
                alert("Some Error Occurred!!!\n Please Try Again.");
                return false;
            }
        });
    }

    function toggle_collection_remarks(x)
    {
        j("#text_mode" + x).toggle();
        j("#edit_mode" + x).toggle();
    }
</script>

<h1>Collection Report</h1>
<br />
<table>
    <tr>
        <td>City : </td>
        <td>
            <select id="city_id" name="city_id" style="width:250px;">
                <option value="0"></option>
                <?
                    if(count($this->cities) > 0)
                    {
                        foreach($this->cities as $city)
                        {
                            ?><option value="<? echo $city->id; ?>" <? echo ($this->city_id == $city->id ? "selected='selected'" : ""); ?> ><? echo $city->city; ?></option><?
                        }
                    }
                ?>
            </select>
        </td>
        <td>Customer : </td>
        <td>
            <select id="customer_id" name="customer_id" style="width:250px;">
                <option value="0"></option>
                <?
                    if(count($this->customers) > 0)
                    {
                        foreach($this->customers as $customer)
                        {
                            ?><option value="<? echo $customer->id; ?>" <? echo ($this->customer_id == $customer->id ? "selected='selected'" : ""); ?> ><? echo $customer->customer_name; ?></option><?
                        }
                    }
                ?>
            </select>
        </td>
        <td>Segment : </td>
        <td>
            <select id="segment_id" name="segment_id" style="width:250px;">
                <option value="0"></option>
                <?
                    if(count($this->customer_segments) > 0)
                    {
                        foreach($this->customer_segments as $segment)
                        {
                            ?><option value="<? echo $segment->id; ?>" <? echo ($this->segment_id == $segment->id ? "selected='selected'" : ""); ?> ><? echo $segment->customer_segment; ?></option><?
                        }
                    }
                ?>
            </select>
        </td>
        <td>Bills Dated Before : </td>
        <td>
            <input type="hidden" id="till_date" value="<? echo ($this->till_date != "" ? date("d-M-Y", strtotime($this->till_date)) : "") ?>" style="width:80px;">
            <? echo ($this->till_date != "" ? date("d-M-Y", strtotime($this->till_date)) : "") ?>
        </td>
        <td>
            <input type="button" value="Refresh" onclick="show_collections(1); return false;">
            <input type="button" value="Clear" onclick="show_collections(0);">
        </td>
    </tr>
</table>
<!--<table width="80%">
    <tr align="center">
        <td>
            <?         
                /*if($this->total > 100)
                {
                    echo "<br />";
                    echo $this->pagination->getPagesLinks();
                    echo "<br /><br />";
                }
                else
                {
                    echo "<br />";
                }*/
            ?>
        </td>
    </tr>
</table>-->
<?
    if(count($this->collections) > 0)
    {
        ?>
        <a href="#" onclick="print_collection_report(); return false;"><img src="custom/graphics/icons/blank.gif" class="print" title="Print"></a>
        <a href="#" onclick="send_sms(); return false;"><img src="custom/graphics/icons/sms.png" title="Send SMS" style="width:16px; height:16px; cursor:pointer;"></a>
        <br /><br />
        <?
    }
?>
<div id="collection_report">
    <table class="clean centreheadings scrollIntoView">
        <tr>
            <th>#</th>
            <th><input type="checkbox" id="check_all"></th>
            <th><a href="index.php?option=com_amittrading&view=collection_report&so=<? echo base64_encode("customer_name"); ?>" <? echo ($this->sort_order == "customer_name" ? "style='color:green;'" : ""); ?>>Name</a></th>
            <th><a href="index.php?option=com_amittrading&view=collection_report&so=<? echo base64_encode("customer_address"); ?>" <? echo ($this->sort_order == "customer_address" ? "style='color:green;'" : ""); ?>>Address</a></th>
            <th><a href="index.php?option=com_amittrading&view=collection_report&so=<? echo base64_encode("city"); ?>" <? echo ($this->sort_order == "city" ? "style='color:green;'" : ""); ?>>City</a></th>
            <th>Contact No.</th>
            <th>Bill No.</th>
            <th>Bill Date</th>
            <th><a href="index.php?option=com_amittrading&view=collection_report&so=<? echo base64_encode("bill_amount"); ?>" <? echo ($this->sort_order == "bill_amount" ? "style='color:green;'" : ""); ?>>Bill Amount</a></th>
            <th>Amount Paid</th>
            <th>Remind<br />After (Days)</th>
            <th>Amount<br />Pending</th>
            <th>Collection Remarks</th>
        </tr>
        <?
            if(count($this->collections) > 0)
            {
                $x = 1;
                $total_bill_amount = 0;
                $total_paid_amount = 0;
                $total_pending_amount = 0;
                $customerwise_pending_amount = 0;
                $customer_id = 0;
                
                foreach($this->collections as $collection)
                {
                    if($customer_id != $collection->customer_id)
                    {
                        $customerwise_pending_amount = 0;
                        $customer_id = $collection->customer_id;
                    }
                    
                    $customerwise_pending_amount += round_2dp($collection->bill_amount - $collection->amount_paid);
                    if($customerwise_pending_amount > 0)
                    {
                        ?> 
                            <tr sales_id="<? echo $collection->sales_id; ?>" class="collection" style="cursor:pointer;">
                                <td align="center" onclick="show_items(<? echo $collection->sales_id; ?>);" class="index"><? echo $x++; ?></td>
                                <td align="center" onclick="show_items(<? echo $collection->sales_id; ?>);"><input type="checkbox" class="bill_checkbox" value="<? echo $collection->sales_id; ?>"></td>
                                <td onclick="show_items(<? echo $collection->sales_id; ?>);"><? echo $collection->customer_name; ?></td>
                                <td onclick="show_items(<? echo $collection->sales_id; ?>);"><? echo $collection->customer_address; ?></td>
                                <td onclick="show_items(<? echo $collection->sales_id; ?>);"><? echo $collection->city; ?></td>
                                <td onclick="show_items(<? echo $collection->sales_id; ?>);"><? echo $collection->contact_no; ?></td>
                                <td onclick="show_items(<? echo $collection->sales_id; ?>);"><? echo $collection->bill_no; ?></td>
                                <td align="center" onclick="show_items(<? echo $collection->sales_id; ?>);"><? echo date("d-M-Y", strtotime($collection->bill_date)); ?></td>
                                <td align="right" onclick="show_items(<? echo $collection->sales_id; ?>);"><? echo round_2dp($collection->bill_amount); $total_bill_amount += round_2dp($collection->bill_amount); ?></td>
                                <td align="right" onclick="show_items(<? echo $collection->sales_id; ?>);"><? echo round_2dp($collection->amount_paid); $total_paid_amount += round_2dp($collection->amount_paid); ?></td>
                                <td onclick="show_items(<? echo $collection->sales_id; ?>);"><? echo intval($collection->credit_days); ?></td>
                                <td align="right" onclick="show_items(<? echo $collection->sales_id; ?>);">
                                    <?
                                        //echo round_2dp($collection->bill_amount - $collection->amount_paid);
                                        echo round_2dp($customerwise_pending_amount);
                                        $total_pending_amount += round_2dp($collection->bill_amount - $collection->amount_paid);
                                    ?>
                                </td>
                                <td class="customer_collection_remark<? echo $collection->customer_id; ?>" title="<? echo $collection->collection_remarks; ?>">
                                    <span id="text_mode<? echo $x; ?>"><a href="#" onclick="edit_collection_remarks(<? echo $x; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>&nbsp;&nbsp;<? echo substr($collection->collection_remarks, 0, 20); ?></span>
                                    <span id="edit_mode<? echo $x; ?>" class="edit_textboxes"><input type="text" id="collection_remarks<? echo $x; ?>" value="<? echo $collection->collection_remarks; ?>" style="width:90px;"><br /><a href="#" class="link1" onclick="save_collection_remarks(<? echo $collection->customer_id; ?>, <? echo $x; ?>); return false;"><img src="custom/graphics/icons/16x16/tick.png" title="Save"></a><a href="#" class="link2" onclick="cancel_collection_remarks_edit(<? echo $x; ?>); return false;"><img src="custom/graphics/icons/cancel.png" title="Cancel"></a></span>
                                </td>
                            </tr>
                        <?
                    }
                }
                ?>
                <tr>
                    <td colspan="8" align="right"><b>Total : </b></td>
                    <td align="right"><b><? echo round_2dp($total_bill_amount); ?></b></td>
                    <td align="right"><b><? echo round_2dp($total_paid_amount); ?></b></td>
                    <td></td>
                    <td align="right"><b><? echo round_2dp($total_pending_amount); ?></b></td>
                    <td></td>
                </tr>
                <?
            }
            else
            {
                ?><td colspan="13" align="center">No records to display.</td><?
            }
        ?>
    </table>
</div>
<!--<table width="80%">
    <tr align="center">
        <td>
            <?
                /*if($this->total > 100)
                {
                    echo "<br />";
                    echo $this->pagination->getPagesLinks();
                }*/
            ?>
        </td>
    </tr>
</table>-->