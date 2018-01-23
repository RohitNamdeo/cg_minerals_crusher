<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){
        j("#transporter_account").tabs(); 
        
        if( window.opener == null )
        {
            j("#closeBtn").hide();
        }
        
        j("#transporters").dialog({
            autoOpen: false,
            width: 450,
           // height: 150,
            buttons:
            {
                "Submit": function()
                {
                    if(j("#transporter").val() == "")
                    {
                        alert("Please fill transporter.");
                        return false;
                    }
                    
                    j.get("index.php?option=com_master&task=update_transporter&tmpl=xml&" + j("#transporterForm").serialize(), function(data){
                       if(data == "ok") 
                       {
                           j("#transporters").dialog("close");
                           alert("Transporter updated successfully.");
                           go(window.location);  
                       }
                       else
                       {
                           alert(data);
                           j('#transporters').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
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
    
    function edit_transporter_account(transporter_id)
    {
        j("#transporter_id").val(transporter_id);
        j.get("index.php?option=com_master&task=transporter_details&tmpl=xml&transporter_id=" + transporter_id, function(data){
           if(data != "")
           {
                var transporter_details = j.parseJSON(data); 
                
                j("#transporter_name").val(transporter_details.transporter_name);
                j("#transporter_address").val(transporter_details.transporter_address);
                j("#city_id").val(transporter_details.city_id);
                j("#state").text(transporter_details.state_name);
                j("#state_id").val(transporter_details.state_id);
                j("#gstStateCode").text(transporter_details.gst_state_code);
                j("#gst_state_code").val(transporter_details.gst_state_code);
                j("#gstin").val(transporter_details.gstin);
                j("#gst_registration_type").val(transporter_details.gst_registration_type);
                j("#contact_no").val(transporter_details.contact_no);
                j("#other_contact_numbers").val(transporter_details.other_contact_numbers);
                j("#opening_balance").val(transporter_details.opening_balance);
                j("#comment").val(transporter_details.comment);
           } 
        });   
        j("#transporters").dialog("open");
        j("#transporters").dialog({"title":"Edit Transporter"});
        j("table tr").find("#city_id,#gst_registration_type").chosen();
        j('#transporters').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function delete_transporter_account()
    {
        balance = parseFloat(j("#balance").val());
        
        if(balance > 0)
        {
            alert("Transporter account cannot be deleted. Account balance is greater than zero."); return false;
        }
        else
        {
            if(confirm("Are you sure to delete the transporter account?"))
            {
                go("index.php?option=com_amittrading&task=delete_transporter_account&transporter_id=<? echo $this->transporter_id; ?>");
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
<h1>Account Details of <? echo $this->transporter->transporter_name; ?></h1>
<div id="transporter_account">
    <ul>
        <li><a href="index.php?option=com_amittrading&view=transports_and_payments&tmpl=xml&transporter_id=<? echo $this->transporter_id; ?>"><span>Transports and Payments</span></a></li>
        <li><a href="#transporter_details"><span>Transporter Details</span></a></li>
        <li><a href="index.php?option=com_amittrading&view=transporter_account_statement&tmpl=xml&transporter_id=<? echo $this->transporter_id; ?>"><span>Account Statement</span></a></li>
    </ul>
    <div id="transporter_details">
        <table class="clean" width="400">
            <tr>
                <td>Transporter Name</td>
                <td name="transporter_name"><? echo $this->transporter->transporter_name; ?></td>
            </tr>
            <tr>
                <td>Address</td>        
                <td><? echo $this->transporter->transporter_address; ?></td>        
            </tr>
             <tr>
                <td>City</td>        
                <td><? echo $this->transporter->city; ?></td>        
            </tr>
             <tr>
                <td>State</td>        
                <td><? echo $this->transporter->state; ?></td>        
            </tr>
             <tr>
                <td>GST Registration Type</td>        
                <td>
                    <?
                        switch($this->transporter->gst_registration_type)
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
                <td>Contact No</td>        
                <td><? echo $this->transporter->contact_no;?></td>        
            </tr>
             <tr>
                <td>Other Contact No</td>        
                <td><? echo $this->transporter->other_contact_numbers;?></td>        
            </tr>
             <tr>
                <td>Opening Balance</td>        
                <td><? echo round_2dp($this->transporter->opening_balance);?></td>        
            </tr>
            <tr>
                <td>Account Balance</td>
                <td><? echo round_2dp($this->transporter->account_balance); ?></td>
            </tr> 
        </table>
        <br />
        <input type="hidden" id="balance" value="<? echo round_2dp($this->transporter->account_balance); ?>">
        <?
            if(is_admin())
            {
                ?>
                <input type="button" value="Edit Account" onclick="edit_transporter_account(<? echo $this->transporter_id; ?>);">
                <input type="button" value="Delete Account" onclick="delete_transporter_account(); return false;">
                <?
            }
        ?>
    </div>
    <input type="button" id="closeBtn" value="Close" onclick="window.close();" style="float:right; margin-top:15px;">
</div>

<div style="display: none;" id="transporters">
    <form method="post" id="transporterForm">
        <input type="hidden" name="transporter_id" id="transporter_id" value="" />
        <table>
              <tr>
                <td>Name</td>
                <td><input type="text" name="transporter_name" id="transporter_name" style="width:270px;"></td>
              </tr>
              <tr>
                <td>Address</td>
                <td><input type="text" name="transporter_address" id="transporter_address" style="width:270px;"></td>
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
                <td>Other Contact Numbers</td>
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