<?php
    defined('_JEXEC') or die;
    //echo $this->customer_id; 
?>
<script>
    j(function(){
        j("#customer_account").tabs();
        
        if( window.opener == null )
        {
            j("#closeBtn").hide();
        }
        
        j("#customers_dialog").dialog({
            autoOpen: false,
            width: 450,
            //height: 300,
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
                    
                    j.get("index.php?option=com_master&task=update_customer&tmpl=xml&" + j("#customer_form").serialize(), function(data){
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
                },
                "Close": function()
                {
                    j(this).dialog("close");
                } 
            }
        });
    });
    
    function edit_customer_account(customer_id)
    {
        j("#customer_id").val(customer_id);
        j.get("index.php?option=com_master&task=customer_details&tmpl=xml&customer_id=" + customer_id, function(data){
           if(data != "")
           {
                var customer_details = j.parseJSON(data);
                
                j("#customer_name").val(customer_details.customer_name);
                j("#customer_address").val(customer_details.customer_address);
                j("#city_id").val(customer_details.city_id);
                j("#state").text(customer_details.state_name);
                j("#state_id").val(customer_details.state_id);
                j("#gstStateCode").text(customer_details.gst_state_code);
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
        j("table tr").find("#city_id,#route_id,#gst_registration_type,#customer_category_id,#customer_segment_id").chosen();
        j('#customers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function change_customer_account_status(status)
    {
        if(confirm("Are you sure you want to " + (status == <? echo AC_ACTIVE; ?> ? "activate" : "deactivate") + " customer's account?"))
        {
            j.get("index.php?option=com_master&task=change_customer_account_status&tmpl=xml&customer_id=<? echo $this->customer_id; ?>&status=" + status, function(data){
                if(data == "ok")
                {
                    j("#deactivateAct, #activateAct").toggle();
                }
                else
                {
                    alert("Please try again!!!");
                    return false;
                }
            });
        }
        else
        {
            return false;
        }
    }
    
    function delete_customer_account()
    {
        balance = parseFloat(j("#balance").val());
        
        if(balance > 0)
        {
            alert("Customer account cannot be deleted. Opening balance differs from account balance."); return false;
        }
        else
        {
            if(confirm("Are you sure to delete the customer account?"))
            {
                go("index.php?option=com_amittrading&task=delete_customer_account&customer_id=<? echo $this->customer_id; ?>");
            }
            else
            {
                return false;
            }
        }            
    }
    
    function get_state(city_id)
    {
        j("#state").text("");
        j("#state_id").val(0);
        j("#gstStateCode").text("");
        j("#gst_state_code").val(0);
        
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
<h1>Account Details of <? echo $this->customer->customer_name; ?></h1>
<?
    if($this->customer->account_status == AC_CLOSED)
    {
        echo "<div style='color:red;'>This account has been deactivated.</div><br />";
    }
?>
<div id="customer_account">
    <ul>
        <li><a href="index.php?option=com_amittrading&view=sales_and_payments&tmpl=xml&customer_id=<? echo $this->customer_id; ?>"><span>Sales and Payments</span></a></li>
        <li><a href="#customer_details"><span>Customer Details</span></a></li>
        <li><a href="index.php?option=com_amittrading&view=customer_account_statement&tmpl=xml&customer_id=<? echo $this->customer_id; ?>"><span>Account Statement</span></a></li>
    </ul>
    <div id="customer_details">
        <table class="clean" width="400">
            <tr>
                <td>Customer</td>
                <td><? echo $this->customer->customer_name; ?></td>
            </tr>
            <tr>
                <td>Address</td>
                <td><? echo $this->customer->customer_address; ?></td>
            </tr>
            <tr>
                <td>City</td>
                <td><? echo $this->customer->city; ?></td>
            </tr>
            <tr>
                <td>State</td>
                <td><? echo $this->customer->state_name; ?></td>
            </tr>
            <tr>
                <td>Route</td>
                <td><? echo @$this->routes[$this->customer->route_id]->route_name; ?></td>
            </tr>
            <tr>
                <td>GST Registration Type</td>
                <td>
                <?
                    switch($this->customer->gst_registration_type)
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
                    }
                ?>
                </td>
            </tr>
            <tr>
                <td>Contact No.</td>
                <td><? echo $this->customer->contact_no; ?></td>
            </tr>
            <tr>
                <td>Other Contact No.</td>
                <td><? echo $this->customer->other_contact_numbers; ?></td>
            </tr>
            <tr>
                <td>Opening Balance</td>
                <td><? echo round_2dp($this->customer->opening_balance); ?></td>
            </tr>
            <tr>
                <td>Account Balance</td>
                <td><? echo round_2dp($this->customer->account_balance); ?></td>
            </tr>
            <tr>
                <td>Customer Category</td>
                <td><? echo $this->customer->customer_category; ?></td>
            </tr>
            <tr>
                <td>Customer Segment</td>
                <td><? echo $this->customer->customer_segment; ?></td>
            </tr>
        </table>
        <br />
        <input type="hidden" id="balance" value="<? echo abs(round_2dp($this->customer->opening_balance) - round_2dp($this->customer->account_balance)); ?>"> 
        <?
            if(is_admin())
            {
                ?>
                <input type="button" value="Edit Account" onclick="edit_customer_account(<? echo $this->customer_id; ?>);">
                <?
                    if($this->customer->account_status == AC_ACTIVE)
                    {
                        ?>
                        <input type="button" id="deactivateAct" value="Deactivate Account" onclick="change_customer_account_status(<? echo AC_CLOSED; ?>);">
                        <input type="button" id="activateAct" value="Activate Account" onclick="change_customer_account_status(<? echo AC_ACTIVE; ?>);" style="display:none;">
                        <?
                    }
                    else if($this->customer->account_status == AC_CLOSED)
                    {
                        ?>
                        <input type="button" id="activateAct" value="Activate Account" onclick="change_customer_account_status(<? echo AC_ACTIVE; ?>);">
                        <input type="button" id="deactivateAct" value="Deactivate Account" onclick="change_customer_account_status(<? echo AC_CLOSED; ?>);" style="display:none;">
                        <?
                    }
                ?>
                <input type="button" value="Delete Account" onclick="delete_customer_account(); return false;">
                <?
            }
        ?>
    </div>
    <input type="button" id="closeBtn" value="Close" onclick="window.close();" style="float:right; margin-top:15px;">
</div>

<div id="customers_dialog" style="display:none;">
    <form id="customer_form">
        <input type="hidden" id="customer_id" value="" name="customer_id">
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
                        <option value="0"> - Select - </option>
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
                <td>Other Contact No</td>
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