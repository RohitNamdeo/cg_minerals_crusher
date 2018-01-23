<?php
    defined('_JEXEC') or die('Restricted access');
    $total_account_balance = 0;
?>
<style>
    #footer_div {
        position: fixed;
        left: 80px;
        bottom : 1px;
        height: 40px;
        background-color: white;
        padding: 6px;
        border: 1px solid lightgray;
        text-align: center; -webkit-border-radius: 6px; -moz-border-radius: 6px; border-radius: 6px;
    }
    
    .context-menu {
        border: 1px solid #008;
        margin: 3px;
        padding: 5px;
        width: 30px;
    }

    .ui-menu {
        width: 150px;
    }
</style>
<script>
j(function(){   
    j(".scrollIntoView").scrollIntoView({
        rowSelector : 'customer',
        rowAttribute : 'customer_id',
        task : 'show_customer_account'
    });
    
    j("#footer_div").hide();
    
    j("#account_status, #segment_id").chosen({allow_single_deselect: true});
    j(".edit_textboxes").hide();
    
    j(document).on("keydown", function(e){
        if(e.keyCode == 27)
        {
            enable_scrollIntoView_plugin();
        }
    });
    
    j(document).on("click", ".ui-dialog-titlebar-close", function(e){
        enable_scrollIntoView_plugin();
    });
    
  //  j(document).on("keyup","#customer_address",function () {
//        var maxLength = 50;
//        var text = j(this).val();
//        var textLength = text.length;
//        if (textLength > maxLength) 
//        {
//            j(this).val(text.substring(0, (maxLength)));
//            alert("Sorry, only " + maxLength + " characters are allowed");
//        }
//    });
        
    j("#customers_dialog").dialog({
        autoOpen: false,
        width: 450,
        //height: 320,
        buttons:
        {
            "Submit (Alt+Z)": function()
            {
                if(j("#customer_name").val() == "")
                {
                    alert("Please enter customer name.");
                    return false;
                }                
                if(j("#city_id").val() == 0)
                {
                    alert("Please select city.");
                    return false;
                }
                if(j("#contact_no").val() != "")
                {
                     if(j("#contact_no").val().length != 10 || isNaN(j("#contact_no").val()))
                     {
                        alert("Please enter valid numeric 10 digit number.");
                        return false;
                    }                    
                }
                if(j("#customer_category_id").val() == 0)
                {
                    alert("Please select customer category.");
                    return false;
                }
                
                if(j("#customer_segment_id").val() == 0)
                {
                    alert("Please select customer segment.");
                    return false;
                }
                /*if(j("#address").val() == "")
                {
                    alert("Please enter address.");
                    return false;
                }
                if(j("#opening_balance").val() == "")
                {
                    alert("Please enter opening balance.");
                    return false;
                }
                if(j("#comment").val() == "")
                {
                    alert("Please enter comment.");
                    return false;
                }*/
                
                j('#customers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                if(j("#mode").val() == "e")
                {
                    j.get("index.php?option=com_master&task=update_customer&tmpl=xml&" + j("#customer_form").serialize() + "&customer_id=" + j("#customer_id").val(), function(data){
                       if(data == "ok") 
                       {
                            j("#customers_dialog").dialog("close");
                            enable_scrollIntoView_plugin();
                            alert("Customer updated successfully.");
                            go(window.location);  
                       }
                       else
                       {
                           alert(data);
                           j('#customers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                       }
                    });
                }
                else
                {
                    j.get("index.php?option=com_master&task=create_customer&tmpl=xml&" + j("#customer_form").serialize() , function(data){
                       if(data == "ok") 
                       {
                            j("#customers_dialog").dialog("close");
                            enable_scrollIntoView_plugin();
                            alert("Customer saved successfully.");
                            go(window.location);  
                       }
                       else
                       {
                           alert(data);
                           j('#customers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                       }
                    });
                }
            },
            "Close": function()
            {
                j(this).dialog("close");
                enable_scrollIntoView_plugin();
            } 
        }
    });
    j('button:contains(Submit)').attr("id","submit_button");
    
    j("#customer_category_dialog").dialog({
        autoOpen: false,
        width: 450,
        height: 150,
        buttons:
        {
            "Submit": function()
            {
                if(j("#customerCategoryID").val() == 0)
                {
                    alert("Please select customer category.");
                    return false;
                }
                
                j('#customer_category_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                
                j.get("index.php?option=com_master&task=update_customers_category&tmpl=xml&" + j("#customersForm").serialize() + "&customer_category_id=" + j("#customerCategoryID").val(), function(data){
                   if(data == "ok") 
                   {
                        j("#customer_category_dialog").dialog("close");
                        enable_scrollIntoView_plugin();
                        alert("Customers category updated successfully.");
                        go(window.location);  
                   }
                   else
                   {
                       alert(data);
                       j('#customer_category_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                   }
                });
            },
            "Close": function()
            {
                j(this).dialog("close");
                enable_scrollIntoView_plugin();
            } 
        }
    });
    
    j("#customer_sms_dialog").dialog({
        autoOpen: false,
        width: 450,
        height: 250,
        buttons:
        {
            "Submit": function()
            {
                if(j("#mcustomerCategoryID").val() == 0)
                {
                    alert("Please select customer category.");
                    return false;
                }
                
                if(j("#message").val() == "")
                {
                    alert("Please enter message.");
                    return false;
                }
                
                j('#customer_sms_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                
                j.get("index.php?option=com_master&task=send_sms_to_customers&tmpl=xml&" + j("#smsForm").serialize() , function(data){
                   if(data == "ok") 
                   {
                        j("#customer_sms_dialog").dialog("close");
                        enable_scrollIntoView_plugin();
                        alert("SMS has been sent successfully.");
                        //go(window.location);  
                   }
                   else
                   {
                       alert(data);
                       j('#customer_sms_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                   }
                });
            },
            "Close": function()
            {
                j(this).dialog("close");
                enable_scrollIntoView_plugin();
            } 
        }
    });
    
    j("#collection_remarks_dialog").dialog({
        autoOpen: false,
        width: 430,
        //height: 250,
        buttons:
        {
            "Submit": function()
            {
                //if(j("#collection_remarks").val() != "")
//                {
                j('#collection_remarks_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                
                
                j.get("index.php?option=com_master&task=save_collection_remarks&tmpl=xml&customer_id=" + j("#customer_id").val() + "",j("#collection_remarks_form").serialize(), function(data){
                    var response = j.parseJSON(data)
                    if(response.success == true)
                    {
                        //toggle_collection_remarks(customer_id);
                        j("#collection_remarks_dialog").dialog("close");
                        j("#customer_collection_remark" + j("#customer_id").val()).html(j("#collection_remarks").val());
                        j("#customer_collection_remark" + j("#customer_id").val()).attr("title", j("#collection_remarks").val());
                    }
                    else
                    {
                        alert("Some Error Occurred!!!\n Please Try Again.");
                        j('#collection_remarks_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                    }
                });
                //}
                //else
//                {
//                    alert("Please enter remarks.");
//                    return false;
//                }
            },
            "Close": function()
            {
                j(this).dialog("close");
                enable_scrollIntoView_plugin();
            } 
        }
    });
});


// this context menu opens on document click of keyboard
j(document).contextmenu({
    delegate: ".context_menu",
    autoFocus: true,
    preventContextMenuForPopup: true,
    preventSelect: true,
    taphold: true,
    menu: [{
        title: "Create Bill",
        cmd: "create_bill"
    }, {
        title: "Recieve Payment",
        cmd: "payment"
    }],

    select: function (event, ui) {
        var target = ui.target;
        var customer_id = j(target).attr("customer_id");
        switch (ui.cmd) {
            case "create_bill":
                window.open("index.php?option=com_amittrading&view=sales_invoice&customer_id=" + customer_id).focus();
                break
            case "payment":
                window.open("index.php?option=com_amittrading&view=customer_payment&customer_id=" + customer_id).focus();
                break
        }
    }
});

j(document).on("keypress","#customer_name", function(e) {
    if (e.which === 32 && !this.value.length)
        e.preventDefault();
});

j(document).on("keydown", function(e){
    if (e.altKey && e.which == 65)
    {
        add_customer();
    }
});
j(document).on("keyup", function(e){
    if (e.altKey && e.which == 90)
    {
       j('#submit_button').click();  
    }
});

j(document).on("keypress", "#opening_balance", function(e){
    prevent_char(e.which,e);
});

j(document).on("click", ".customer", function(){
    j(this).closest("table").find(".clickedRow").removeClass('clickedRow');
    j(this).addClass('clickedRow');
});

function toggle_footer()
{
    j("#footer_div").toggle();
} 

function add_customer()
{
    //j("#customer_id").val("");    
//    j("#mode").val("");
//    j("#customer_name").val("");
//    j("#customer_address").val("");
//    j("#city_id").val(0);
//    j("#contact_no").val("");
//    j("#opening_balance").val("");
//    j("#customer_category_id").val(0);
//    j("#customer_segment_id").val(0);
//    j("#comment").val("");

    j("#customer_form")[0].reset();
    j("#customers_dialog").dialog("open");
    j("#customers_dialog").dialog({"title":"Add Customer"});
    j("table tr").find("#city_id,#route_id,#gst_registration_type,#customer_category_id,#customer_segment_id").chosen();
    j('#customers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    disable_scrollIntoView_plugin();
}

function edit_customer(customer_id)
{
    j("#mode").val("e");
    j("#customer_id").val(customer_id);
    j.get("index.php?option=com_master&task=customer_details&tmpl=xml&customer_id=" + customer_id, function(data){
       if(data != "")
       {
            var customer_details = j.parseJSON(data);
            
            j("#customer_name").val(customer_details.customer_name);
            j("#customer_address").val(customer_details.customer_address);
            j("#city_id").val(customer_details.city_id);
            j("#state_id").val(customer_details.state_id);
            j("#gstStateCode").text(customer_details.gst_state_code);
            j("#route_id").val(customer_details.route_id);
            j("#gst_state_code").val(customer_details.gst_state_code);
            j("#gstin").val(customer_details.gstin);
            j("#gst_registration_type").val(customer_details.gst_registration_type);
            j("#contact_no").val(customer_details.contact_no);
            j("#other_contact_numbers").val(customer_details.other_contact_numbers);
            j("#opening_balance").val(customer_details.opening_balance);
            j("#customer_category_id").val(customer_details.customer_category_id);
            j("#customer_segment_id").val(customer_details.customer_segment_id);
            j("#comment").val(customer_details.comment);
       } 
    });   
    j("#customers_dialog").dialog("open");
    j("#customers_dialog").dialog({"title":"Edit Customer"});
    j('#customers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    disable_scrollIntoView_plugin();
}

function delete_customer(customer_id)
{
    if(confirm("Are you sure?"))
    {
        j.get("index.php?option=com_master&task=delete_customer&tmpl=xml&customer_id=" + customer_id, function(data){
            if(data == "ok")
            {
                alert("Customer deleted successfully.");
                go(window.location);  
            }
        });
    }
    else
    {
        return false;
    }
}

function show_customer_account(customer_id)
{
    window.open('index.php?option=com_amittrading&view=customer_account&customer_id=' + customer_id, "customer_account" + customer_id, "height=" + screen.height + ", width=" + screen.width).focus();
}

function set_customer_category()
{
    if(j(".customer_checkbox:checked").length == 0)
    {
        alert("Select customers."); return false;
    }
    else
    {
        j("#customerCategoryID").val(0);
        j("#customer_category_dialog").dialog("open");
        j("#customer_category_dialog").dialog({"title":"Set Customer Category"});
        j('#customer_category_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
        disable_scrollIntoView_plugin();
    }
}

function send_sms_to_customers()
{
    j("#mcustomerCategoryID").val(0);
    j("#message").val("");
    j("#customer_sms_dialog").dialog("open");
    j("#customer_sms_dialog").dialog({"title":"Send SMS"});
    j('#customer_sms_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    disable_scrollIntoView_plugin();
}

function show_customers()
{
    go("index.php?option=com_master&view=manage_customers&as=" + j("#account_status").val() + "&segment_id=" + j("#segment_id").val());
}

function disable_scrollIntoView_plugin()
{
    j("#customerList").removeClass("scrollIntoView");
}

function enable_scrollIntoView_plugin()
{
    j("#customerList").addClass("scrollIntoView");
}

function edit_collection_remarks(customer_id)
{
    j("#collection_remarks").val("");
    j('#collection_remarks_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    
    //toggle_collection_remarks(customer_id);
    j("#customer_id").val(customer_id);
    collection_remarks = j("#customer_collection_remark" + customer_id).attr("title");
    
    j("#collection_remarks").val(collection_remarks);
    j("#collection_remarks_dialog").dialog("open");
    j("#collection_remarks_dialog").dialog({"title":"Collection Remarks"});
}

function cancel_collection_remarks_edit(customer_id)
{
    toggle_collection_remarks(customer_id);
}

//function save_collection_remarks(customer_id, x)
//{
    // x unsed here but same function from collection report sends 2 parameters
//    var collection_remarks = j("#collection_remarks" + customer_id).val();
//    
//    j.get("index.php?option=com_master&task=save_collection_remarks&tmpl=xml&customer_id=" + customer_id + "&collection_remarks=" + collection_remarks, function(data){
//        var response = j.parseJSON(data)
//        if(response.success == true)
//        {
//            toggle_collection_remarks(customer_id);
//            j("#customer_collection_remark" + customer_id).html(response.data);
//            j("#customer_collection_remark" + customer_id).attr("title", collection_remarks);
//        }
//        else
//        {
//            alert("Some Error Occurred!!!\n Please Try Again.");
//            return false;
//        }
//    });
//}

function toggle_collection_remarks(customer_id)
{
    j("#text_mode" + customer_id).toggle();
    j("#edit_mode" + customer_id).toggle();
}

function get_state(city_id)
{
    j.get("index.php?option=com_master&task=state_details&city_id=" + city_id + "&tmpl=xml",function(data){
        if(data != "")
        {
            var state_details = j.parseJSON(data);
            
            j("#state").text(state_details.state_name);
            j("#state_id").val(state_details.state_id);
            j("#gstStateCode").text(state_details.gst_state_code);
            j("#gst_state_code").val(state_details.gst_state_code);
        }
    });
}
</script>
<h1>Customers</h1>
<!--<input type="button" value="Add Customer" id="add_customer" onclick="add_customer();">-->
<button type="button" id="add_customer" onclick="add_customer();"><u>A</u>dd Customer</button>
<input type="button" value="Set Customer Category" onclick="set_customer_category();">
<input type="button" value="Send SMS" onclick="send_sms_to_customers();">&nbsp;
<select id="account_status" style="width:160px;">
    <option value="<? echo AC_ACTIVE; ?>" <? echo ($this->account_status == AC_ACTIVE ? "selected='selected'" : ""); ?> >Active Customers</option>
    <option value="<? echo AC_CLOSED; ?>" <? echo ($this->account_status == AC_CLOSED ? "selected='selected'" : ""); ?> >Inactive Customers</option>
    <option value="-1" <? echo ($this->account_status == -1 ? "selected='selected'" : ""); ?> >All Customers</option>
</select>&nbsp;
<select id="segment_id" style="width:160px;">
    <option value="0"></option>
    <?
        if(count($this->customer_segments) > 0)
        {
            foreach($this->customer_segments as $segment)
            {
                ?>
                    <option value="<? echo intval($segment->id); ?>"<? echo ($this->segment_id == $segment->id ? "selected='selected'" : ""); ?>><? echo $segment->customer_segment; ?></option>
                <?
            }
        }
    ?>
</select>
<input type="button" value="Show" onclick="show_customers();">
&nbsp;<a href="javascript:void(0);" onclick="toggle_footer();">#</a>
<br /><br />

<form id="customersForm" method="post">
    <div id="customers_list">
        <table id="customerList" class="clean centreheadings scrollIntoView">
            <tr>
                <th>#</th>
                <th></th>
                <th><a href="index.php?option=com_master&view=manage_customers&so=<? echo base64_encode("customer_name"); ?>" <? echo ($this->sort_order == "customer_name" ? "style='color:green;'" : ""); ?>>Name</a></th>
                <th><a href="index.php?option=com_master&view=manage_customers&so=<? echo base64_encode("account_balance"); ?>" <? echo ($this->sort_order == "account_balance" ? "style='color:green;'" : ""); ?>>Balance</a></th>
                <th><a href="index.php?option=com_master&view=manage_customers&so=<? echo base64_encode("customer_address"); ?>" <? echo ($this->sort_order == "customer_address" ? "style='color:green;'" : ""); ?>>Address</a></th>
                <th><a href="index.php?option=com_master&view=manage_customers&so=<? echo base64_encode("city"); ?>" <? echo ($this->sort_order == "city" ? "style='color:green;'" : ""); ?>>City</a></th>
                <!--<th><a href="index.php?option=com_master&view=manage_customers&so=<? //echo base64_encode("state"); ?>" <? //echo ($this->sort_order == "state" ? "style='color:green;'" : ""); ?>>State</a></th>
                <th>GST State Code</th>-->
                <th>Route</th>
                <!--<th>GST Registration Type</th>
                <th>GSTIN</th>-->
                <th>Contact No</th>
                <th>Other Contact No.</th>
                <!--<th><a href="index.php?option=com_master&view=manage_customers&so=<? //echo base64_encode("opening_balance"); ?>" <? //echo ($this->sort_order == "opening_balance" ? "style='color:green;'" : ""); ?>>Opening Balance</a></th>-->
                <th><a href="index.php?option=com_master&view=manage_customers&so=<? echo base64_encode("customer_category"); ?>" <? echo ($this->sort_order == "customer_category" ? "style='color:green;'" : ""); ?>>Category</a></th>
                <th><a href="index.php?option=com_master&view=manage_customers&so=<? echo base64_encode("customer_segment"); ?>" <? echo ($this->sort_order == "customer_segment" ? "style='color:green;'" : ""); ?>>Segment</a></th>
                <th width="90">Comments</th>
                <th width="100">Collection Remarks</th>
                <!--<th>Action</th>-->
            </tr>
            <?
                $x = 1;
                foreach($this->customers as $customer)
                {
                    $total_account_balance += $customer->account_balance;
                    ?>
                    <tr style="cursor:pointer;" ondblclick="show_customer_account(<? echo $customer->id; ?>);" customer_id="<? echo $customer->id; ?>" class="customer">
                        <td align="center"><? echo $x++; ?></td>
                        <td align="center">
                            <?
                                if($customer->account_status == AC_ACTIVE)
                                {
                                    ?><input type="checkbox" class="customer_checkbox" name="customer_ids[]" value="<? echo $customer->id; ?>"><?
                                }
                            ?>
                        </td>
                        <td><a href="index.php?option=com_amittrading&view=customer_account&customer_id=<? echo $customer->id; ?>"><span class="context_menu" customer_id="<? echo $customer->id; ?>"><? echo ucwords(strtolower($customer->customer_name)); ?></a></span></td>
                        <td align="right"><? echo $customer->account_balance; ?></td>
                        <td style="width: 20%;"><? echo ucwords(strtolower($customer->customer_address)); ?></td>
                        <td><? echo ucwords(strtolower($customer->city)); ?></td>
                        <!--<td><? //echo $customer->state; ?></td>
                        <td align="center"><? //echo $customer->gst_state_code; ?></td>-->
                        <td><? echo @$this->routes[$customer->route_id]->route_name; ?></td>
                        <!--<td>
                        <?
                            /*switch($customer->gst_registration_type)
                            {
                                case RD:
                                    echo "Registered Dealer";
                                    break;
                                case URD:
                                    echo "Unregistered Dealer";
                                    break;
                                case CSD:
                                    echo "Composition Scheme Dealer";
                                    break;
                            } */
                        ?>
                        </td>
                        <td><? //echo $customer->gstin; ?></td>-->
                        <td><? echo $customer->contact_no; ?></td>
                        <td><? echo $customer->other_contact_numbers; ?></td>
                        <!--<td align="right"><? //echo $customer->opening_balance; ?></td>-->
                        <td><? echo ucwords(strtolower($customer->customer_category)); ?></td>
                        <td><? echo ucwords(strtolower($customer->customer_segment)); ?></td>
                        <td title="<? echo $customer->comment; ?>"><? echo substr($customer->comment, 0 , 20); ?></td>
                        <!--<td id="customer_collection_remark<? //echo $customer->id; ?>" title="<? //echo $customer->collection_remarks; ?>">
                            <span id="text_mode<? //echo $customer->id; ?>"><a href="#" onclick="edit_collection_remarks(<? //echo $customer->id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>&nbsp;&nbsp;<? //echo substr($customer->collection_remarks, 0, 20); ?></span>
                            <span id="edit_mode<? //echo $customer->id; ?>" class="edit_textboxes"><input type="text" id="collection_remarks<? //echo $customer->id; ?>" value="<? //echo $customer->collection_remarks; ?>" style="width:90px;"><br /><a href="#" onclick="save_collection_remarks(<? //echo $customer->id; ?>, 0); return false;"><img src="custom/graphics/icons/16x16/tick.png" title="Save"></a><a href="#" onclick="cancel_collection_remarks_edit(<? //echo $customer->id; ?>); return false;"><img src="custom/graphics/icons/cancel.png" title="Cancel"></a></span>
                        </td> --> 
                        <td>
                            <a href="#" onclick="edit_collection_remarks(<? echo intval($customer->id); ?>); return false;"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit"></a>
                            <br />
                            <span id="customer_collection_remark<? echo $customer->id; ?>" title="<? echo $customer->collection_remarks; ?>"><? echo substr($customer->collection_remarks, 0, 20); ?></span> 
                            <!--<a href="#" onclick="save_collection_remarks(<? //echo $customer->id; ?>, 0); return false;"><img src="custom/graphics/icons/16x16/tick.png" title="Save"></a>-->
                            <!--<a href="#" onclick="cancel_collection_remarks_edit(<? //echo $customer->id; ?>); return false;"><img src="custom/graphics/icons/cancel.png" title="Cancel"></a>-->
                        </td>
                        <!--<td align="center">
                            <a href="index.php?option=com_amittrading&view=customer_account&customer_id=<? //echo $customer->id; ?>"><img src="custom/graphics/icons/blank.gif" title="View Account" class="view"></a>
                            <?
                                /*if(is_admin())
                                {
                                    ?>
                                    <a href="#" onclick="edit_customer(<? //echo $customer->id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_customer(<? //echo $customer->id; ?>); return false;" class="delete">
                                    <?
                                }*/
                            ?>
                        </td>-->
                    </tr>
                    <?
                }
            ?>
        </table>
    </div>
</form>
<div id="footer_div">
    <b>Total Outstanding Amount : <? echo round_2dp($total_account_balance); ?>/-</b>
</div>

<input type="hidden" id="mode" value="">
<input type="hidden" id="customer_id" value="" name="customer_id">

<div id="collection_remarks_dialog" style="display:none;">
    <form id="collection_remarks_form">
        <table>
              <tr>
                <td>Remarks</td>
                <td><input type="text" name="collection_remarks" id="collection_remarks" style="width:270px;"></td>
              </tr>
        </table>
    </form>
</div>
<div id="customers_dialog" style="display:none;">
    <form id="customer_form">
        <table>
              <tr>
                <td>Name</td>
                <td><input type="text" name="customer_name" id="customer_name" style="width:270px;"></td>
              </tr>
              <tr>
                <td>Address</td>
                <td><input type="text" name="customer_address" id="customer_address" style="width:270px;"></td>
              </tr>
              <tr>
                <td>City</td>
                <td>
                    <select id="city_id" name="city_id" style="width:275px;" onchange="get_state(this.value);">
                        <option value="0"></option>
                        <?
                            foreach($this->cities as $city)
                            {
                                ?>
                                    <option value="<? echo $city->id; ?>"><? echo $city->city; ?></option>
                                <?
                            }
                        ?>
                    </select>
                </td>
              </tr>
              <tr>
                <td>State</td>
                <td>
                    <span id="state"> - Select City First - </span>
                    <input type="hidden" name="state_id" id="state_id" />
                </td>
              </tr>
              <tr>
                <td>GST State Code</td>
                <td>
                    <span id="gstStateCode"> - Select City First - </span>
                    <input type="hidden" name="gst_state_code" id="gst_state_code" />
                </td>
              </tr>
              <tr>
                <td>Route</td>
                <td>
                    <select id="route_id" name="route_id" style="width:275px;">
                        <option value="0"></option>
                        <?
                            foreach($this->routes as $route)
                            {
                                ?>
                                    <option value="<? echo $route->id; ?>"><? echo $route->route_name; ?></option>
                                <?
                            }
                        ?>
                    </select>
                </td>
              </tr>
              <tr>
                <td>GSTIN</td>
                <td><input type="text" name="gstin" id="gstin" style="width:270px;"></td>
              </tr>
              <tr>
                <td>GST Registration Type</td>
                <td>
                    <select name="gst_registration_type" id="gst_registration_type" style="width:270px;">
                        <option value="0"></option>
                        <option value="<?= RD; ?>"/>Registered Dealer</option>
                        <option value="<?= URD; ?>"/>Unregistered Dealer</option>
                        <option value="<?= CSD; ?>"/>Composition Scheme Dealer</option>
                    </select>
                </td>
              </tr>
              <tr>
                <td>Contact No</td>
                <td><input type="text" name="contact_no" id="contact_no" style="width:270px;"></td>
              </tr>
              <tr>
                <td>Other Contact Numbers</td>
                <td><input type="text" name="other_contact_numbers" id="other_contact_numbers" style="width:270px;"></td>
              </tr>
              <tr>
                <td>Opening Balance</td>
                <td><input type="text" name="opening_balance" id="opening_balance" style="width:270px;"></td>
              </tr>
              <tr>
                <td>Customer Category</td>
                <td>
                    <select id="customer_category_id" name="customer_category_id" style="width:275px;">
                        <option value="0"></option>
                        <?
                            foreach($this->customer_categories as $customer_category)
                            {
                                ?>
                                    <option value="<? echo $customer_category->id; ?>"><? echo $customer_category->customer_category; ?></option>
                                <?
                            }
                        ?>
                    </select>
                </td>
              </tr>
              <tr>
                <td>Customer Segment</td>
                <td>
                    <select id="customer_segment_id" name="customer_segment_id" style="width:275px;">
                        <option value="0"></option>
                        <?
                            foreach($this->customer_segments as $customer_segment)
                            {
                                ?>
                                    <option value="<? echo $customer_segment->id; ?>"><? echo $customer_segment->customer_segment; ?></option>
                                <?
                            }
                        ?>
                    </select>
                </td>
              </tr>
              <tr>
                <td>Comments</td>
                <td><input type="text" name="comment" id="comment" style="width:270px;"></td>
              </tr>
        </table>
    </form>
</div>

<div id="customer_category_dialog" style="display:none;">
    <table>
          <tr>
            <td>Customer Category</td>
            <td>
                <select id="customerCategoryID" name="customerCategoryID" style="width:275px;">
                    <option value="0"></option>
                    <?
                        foreach($this->customer_categories as $customer_category)
                        {
                            ?>
                                <option value="<? echo $customer_category->id; ?>"><? echo $customer_category->customer_category; ?></option>
                            <?
                        }
                    ?>
                </select>
            </td>
          </tr>
    </table>
</div>

<div id="customer_sms_dialog" style="display:none;">
    <form id="smsForm" method="post">
        <table>
              <tr>
                <td>Customer Category</td>
                <td>
                    <select id="mcustomerCategoryID" name="customerCategoryID" style="width:275px;">
                        <option value="0"></option>
                        <?
                            foreach($this->customer_categories as $customer_category)
                            {
                                ?>
                                    <option value="<? echo $customer_category->id; ?>"><? echo $customer_category->customer_category; ?></option>
                                <?
                            }
                        ?>
                    </select>
                </td>
              </tr>
              <tr valign="top">
                <td>Message</td>
                <td>
                    <textarea id="message" name="message" style="width:275px; height:100px; resize:none;"></textarea>
                </td>
              </tr>
        </table>
    </form>
</div>