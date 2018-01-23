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
</style>
<script>
j(function(){   
    j("#customers_dialog").dialog({
        autoOpen: false,
        width: 450,
        height: 300,
        buttons:
        {
            "Submit": function()
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
            } 
        }
    });
    
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
            } 
        }
    });
                                    
    j("#customerList").find("tr.customer").first().addClass("clickedRow").click();   
});

j(document).on("keypress", "#opening_balance", function(e){
    prevent_char(e.which,e);
});

var isTableActive = false;

// every time that there's a click, detect if it was on the table or outside of it
j(document).on("click", function(e) {
    isTableActive = j.contains(document.getElementById("customerList"), e.target);
});

j(document).on("click", ".customer", function(){
    j(this).closest("table").find(".clickedRow").removeClass('clickedRow');
    j(this).addClass('clickedRow');
});

j(document).on("keydown", function(e) {
    switch(e.keyCode) {
        case 38: // up
            if (j("#customerList tr.clickedRow").prev().length) {
                j("#customerList tr.clickedRow").removeClass("clickedRow").prev().addClass("clickedRow");
            }  
            break;
        case 40: // down
            if (j("#customerList tr.clickedRow").next().length) {
                j("#customerList tr.clickedRow").removeClass("clickedRow").next().addClass("clickedRow");
            }  
            break;
        case 13: // enter
            if(j("#customer_sms_dialog").dialog( "isOpen" ))
            { break; }
            var customer_id = j("#customerList tr.clickedRow").attr("customer_id");
            show_customer_account(customer_id);     
            break;
    }
     
    //j("#customerList tbody tr.clickedRow")[0].scrollIntoView();
    if (j("#customerList tbody tr.clickedRow").offset().top < window.scrollY)
    {     
        j("html, body").scrollTop(j("#customerList tbody tr.clickedRow").offset().top);    
    }else if (j("#customerList tbody tr.clickedRow").offset().top + j("#customerList tbody tr.clickedRow").height() > window.scrollY + (window.innerHeight || document.documentElement.clientHeight) - 50)
    {
        j("html, body").scrollTop(j("#customerList tbody tr.clickedRow").offset().top + j("#customerList tbody tr.clickedRow").height() - (window.innerHeight || document.documentElement.clientHeight) + 50);
    }
    //var scrolltop_value =  parseFloat(j("#customerList tbody tr.clickedRow").offset().top.toFixed(2)) + parseFloat(j("#customerList tbody").scrollTop().toFixed(2)) - parseFloat(j("#customerList tbody").offset().top.toFixed(2));
    //j(window).scrollTop(scrolltop_value.toFixed(2));

    //if (isTableActive) 
    if (e.keyCode != 70 && e.keyCode != 127 && e.keyCode != 17 && e.keyCode != 116)
    {   
        if ( j("#customer_sms_dialog").dialog( "isClose" ) && j("#customers_dialog").dialog( "isClose" ) ) 
        {
            e.preventDefault();
            return false;
        }
    }
}); 

function add_customer()
{
    j("#customer_id").val("");    
    j("#mode").val("");
    j("#customer_name").val("");
    j("#customer_address").val("");
    j("#city_id").val(0);
    j("#contact_no").val("");
    j("#opening_balance").val("");
    j("#customer_category_id").val(0);
    j("#comment").val("");
    j("#customers_dialog").dialog("open");
    j("#customers_dialog").dialog({"title":"Add Customer"});
    j('#customers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
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
            j("#contact_no").val(customer_details.contact_no);
            j("#opening_balance").val(customer_details.opening_balance);
            j("#customer_category_id").val(customer_details.customer_category_id);
            j("#comment").val(customer_details.comment);
       } 
    });   
    j("#customers_dialog").dialog("open");
    j("#customers_dialog").dialog({"title":"Edit Customer"});
    j('#customers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
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
    }
}

function send_sms_to_customers()
{
    j("#mcustomerCategoryID").val(0);
    j("#message").val("");
    j("#customer_sms_dialog").dialog("open");
    j("#customer_sms_dialog").dialog({"title":"Send SMS"});
    j('#customer_sms_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
}
</script>
<h1>Customers</h1>
<input type="button" value="Add Customer" id="add_customer" onclick="add_customer();">
<input type="button" value="Set Customer Category" onclick="set_customer_category();">
<input type="button" value="Send SMS" onclick="send_sms_to_customers();">
<br /><br />

<form id="customersForm" method="post">
    <div id="customers_list">
        <table id="customerList" class="clean centreheadings floatheader">
            <tr>
                <th>#</th>
                <th></th>
                <th><a href="index.php?option=com_master&view=manage_customers&so=<? echo base64_encode("customer_name"); ?>" <? echo ($this->sort_order == "customer_name" ? "style='color:green;'" : ""); ?>>Name</a></th>
                <th><a href="index.php?option=com_master&view=manage_customers&so=<? echo base64_encode("account_balance"); ?>" <? echo ($this->sort_order == "account_balance" ? "style='color:green;'" : ""); ?>>Account Balance</a></th>
                <th><a href="index.php?option=com_master&view=manage_customers&so=<? echo base64_encode("customer_address"); ?>" <? echo ($this->sort_order == "customer_address" ? "style='color:green;'" : ""); ?>>Address</a></th>
                <th><a href="index.php?option=com_master&view=manage_customers&so=<? echo base64_encode("city"); ?>" <? echo ($this->sort_order == "city" ? "style='color:green;'" : ""); ?>>City</a></th>
                <th>Contact No</th>
                <!--<th><a href="index.php?option=com_master&view=manage_customers&so=<? //echo base64_encode("opening_balance"); ?>" <? //echo ($this->sort_order == "opening_balance" ? "style='color:green;'" : ""); ?>>Opening Balance</a></th>-->
                <th><a href="index.php?option=com_master&view=manage_customers&so=<? echo base64_encode("customer_category"); ?>" <? echo ($this->sort_order == "customer_category" ? "style='color:green;'" : ""); ?>>Customer Category</a></th>
                <th>Comments</th>
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
                        <td align="center"><input type="checkbox" class="customer_checkbox" name="customer_ids[]" value="<? echo $customer->id; ?>"></td>
                        <td><a href="index.php?option=com_amittrading&view=customer_account&customer_id=<? echo $customer->id; ?>"><? echo $customer->customer_name; ?></a></td>
                        <td align="right"><? echo $customer->account_balance; ?></td>
                        <td><? echo $customer->customer_address; ?></td>
                        <td><? echo $customer->city; ?></td>
                        <td><? echo $customer->contact_no; ?></td>
                        <!--<td align="right"><? //echo $customer->opening_balance; ?></td>-->
                        <td><? echo $customer->customer_category; ?></td>
                        <td><? echo $customer->comment; ?></td>
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
                    <select id="city_id" name="city_id" style="width:275px;">
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
                <td>Contact No</td>
                <td><input type="text" name="contact_no" id="contact_no" style="width:270px;"></td>
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