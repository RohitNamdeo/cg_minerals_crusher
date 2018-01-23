<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){
        j("#supplier_account").tabs();
        
        if( window.opener == null )
        {
            j("#closeBtn").hide();
        }
        
        j("#suppliers_dialog").dialog({
            autoOpen: false,
            width: 450,
            //height: 350,
            buttons:
            {
                "Submit": function()
                {
                    if(j("#supplier_name").val() == "")
                    {
                        alert("Please enter supplier name.");
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
                    
                    j.get("index.php?option=com_master&task=update_supplier&tmpl=xml&" + j("#supplier_form").serialize(), function(data){
                       if(data == "ok") 
                       {
                           j("#suppliers_dialog").dialog("close");
                           alert("Supplier updated successfully.");
                           go(window.location);  
                       }
                       else
                       {
                           alert(data);
                           j('#suppliers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
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
    
    function edit_supplier_account(supplier_id)
    {
        j("#mode").val("e");
        j("#supplier_id").val(supplier_id);
        j.get("index.php?option=com_master&task=supplier_details&tmpl=xml&supplier_id=" + supplier_id, function(data){
           if(data != "")
           {
                var supplier_details = j.parseJSON(data); 
                
                j("#supplier_name").val(supplier_details.supplier_name);
                j("#supplier_address").val(supplier_details.supplier_address);
                j("#city_id").val(supplier_details.city_id);
                j("#state").text(supplier_details.state_name);
                j("#state_id").val(supplier_details.state_id);
                j("#gstStateCode").text(supplier_details.gst_state_code);
                j("#gst_state_code").val(supplier_details.gst_state_code);
                j("#gstin").val(supplier_details.gstin);
                j("#gst_registration_type").val(supplier_details.gst_registration_type);
                j("#contact_no").val(supplier_details.contact_no);
                j("#other_contact_numbers").val(supplier_details.other_contact_numbers);
                j("#opening_balance").val(supplier_details.opening_balance);
                j("#comment").val(supplier_details.comment);
           } 
        });   
        j("#suppliers_dialog").dialog("open");
        j("#suppliers_dialog").dialog({"title":"Edit Supplier"});
        j("table tr").find("#city_id,#gst_registration_type").chosen();
        j('#suppliers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function delete_supplier_account()
    {
        balance = parseFloat(j("#balance").val());
        
        if(balance > 0)
        {
            alert("Supplier account cannot be deleted. Opening balance differs from account balance."); return false;
        }
        else
        {
            if(confirm("Are you sure to delete the supplier account?"))
            {
                go("index.php?option=com_amittrading&task=delete_supplier_account&supplier_id=<? echo $this->supplier_id; ?>");
            }
            else
            {
                return false;
            }
        }
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
<h1>Account Details of <? echo $this->supplier->supplier_name; ?></h1>
<div id="supplier_account">
    <ul>
        <li><a href="index.php?option=com_amittrading&view=purchases_and_payments&tmpl=xml&supplier_id=<? echo $this->supplier_id; ?>"><span>Purchases and Payments</span></a></li>
        <li><a href="#supplier_details"><span>Supplier Details</span></a></li>
        <li><a href="index.php?option=com_amittrading&view=supplier_account_statement&tmpl=xml&supplier_id=<? echo $this->supplier_id; ?>"><span>Account Statement</span></a></li>
    </ul>
    <div id="supplier_details">
        <table class="clean" width="400">
            <tr>
                <td>Supplier</td>
                <td><? echo $this->supplier->supplier_name; ?></td>
            </tr>
            <tr>
                <td>Address</td>
                <td><? echo $this->supplier->supplier_address; ?></td>
            </tr>
            <tr>
                <td>City</td>
                <td><? echo $this->supplier->city_name; ?></td>
            </tr>
            <tr>
                <td>State</td>
                <td><? echo $this->supplier->state_name; ?></td>
            </tr>
            <tr>
                <td>GST Registration Type</td>
                <td>
                <?
                    switch($this->supplier->gst_registration_type)
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
                <td><? echo $this->supplier->contact_no; ?></td>
            </tr>
            <tr>
                <td>Other Contact No.</td>
                <td><? echo $this->supplier->other_contact_numbers; ?></td>
            </tr>
            <tr>
                <td>Opening Balance</td>
                <td><? echo round_2dp($this->supplier->opening_balance); ?></td>
            </tr>
            <tr>
                <td>Account Balance</td>
                <td><? echo round_2dp($this->supplier->account_balance); ?></td>
            </tr>
        </table>
        <br />
        <input type="hidden" id="balance" value="<? echo abs(round_2dp($this->supplier->opening_balance) - round_2dp($this->supplier->account_balance)); ?>">
        <?
            if(is_admin())
            {
                ?>
                <input type="button" value="Edit Account" onclick="edit_supplier_account(<? echo intval($this->supplier_id); ?>);">
                <input type="button" value="Delete Account" onclick="delete_supplier_account(); return false;">
                <?
            }
        ?>
    </div>
    <input type="button" id="closeBtn" value="Close" onclick="window.close();" style="float:right; margin-top:15px;">
</div>

<div id="suppliers_dialog" style="display:none;">
    <form id="supplier_form">          
        <input type="hidden" id="supplier_id" value="" name="supplier_id">
        <table>
              <tr>
                <td>Name</td>
                <td><input type="text" name="supplier_name" id="supplier_name" style="width:270px;"></td>
              </tr>
              <tr>
                <td>Address</td>
                <td><input type="text" name="supplier_address" id="supplier_address" style="width:270px;"></td>
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
                <td>Other Contact No</td>
                <td><input type="text" name="other_contact_numbers" id="other_contact_numbers" style="width:270px;"></td>
              </tr>
              <tr>
                <td>Opening Balance</td>
                <td><input type="text" name="opening_balance" id="opening_balance" style="width:270px;"></td>
              </tr>
              <tr>
                <td>Comments</td>
                <td><input type="text" name="comment" id="comment" style="width:270px;"></td>
              </tr>
        </table>
    </form>
</div>