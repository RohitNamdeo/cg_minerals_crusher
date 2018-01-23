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
    j(".scrollIntoView").scrollIntoView({
        rowSelector : 'transporter',
        rowAttribute : 'transporter_id',
        task : 'show_transporter_account'
    });
    
    j("#footer_div").hide();
    
    j(document).on("keydown", function(e){
        if(e.keyCode == 27)
        {
            enable_scrollIntoView_plugin();
        }
    });
    
    j(document).on("click", ".ui-dialog-titlebar-close", function(e){
        enable_scrollIntoView_plugin();
    });
    
    //j(document).on("keyup","#transporter_address",function () {
//        var maxLength = 50;
//        var text = j(this).val();
//        var textLength = text.length;
//        if (textLength > maxLength) 
//        {
//            j(this).val(text.substring(0, (maxLength)));
//            alert("Sorry, only " + maxLength + " characters are allowed");
//        }
//    });
      
    j("#transporters_dialog").dialog({
        autoOpen: false,
        width: 450,
        //height: 300,
        buttons:
        {
            "Submit (Alt+Z)": function()
            {
                if(j("#transporter_name").val() == "")
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
                
                if(j("#mode").val() == "e")
                {
                    j.get("index.php?option=com_master&task=update_transporter&tmpl=xml&" + j("#transporter_form").serialize() + "&transporter_id=" + j("#transporter_id").val(), function(data){
                       if(data == "ok") 
                       {
                           j("#transporters_dialog").dialog("close");
                           enable_scrollIntoView_plugin();
                           alert("Supplier updated successfully.");
                           go(window.location);  
                       }
                       else
                       {
                           alert(data);
                           j('#transporters_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                       }
                    });
                }
                else
                {
                    j.get("index.php?option=com_master&task=create_transporter&tmpl=xml&" + j("#transporter_form").serialize() , function(data){
                        alert(data);
                        if(data == "ok") 
                       {
                           j("#transporters_dialog").dialog("close");
                           enable_scrollIntoView_plugin();
                           alert("Transporter saved successfully.");
                           go(window.location);  
                       }
                       else
                       {
                           alert(data);
                           j('#transporters_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
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
});

j(document).on("keypress","#transporter_name", function(e) {
    if (e.which === 32 && !this.value.length)
        e.preventDefault();
});

j(document).on("keydown", function(e){
    if (e.altKey && e.which == 65)
    {
        add_transporter();
    }
});
j(document).on("keyup", function(e){
    if (e.altKey && e.which == 90)
    {
       j('#submit_button').click();  
    }
});

j(document).on("click", ".transporter", function(){
    j(this).closest("table").find(".clickedRow").removeClass('clickedRow');
    j(this).addClass('clickedRow');
}); 

j(document).on("keypress", "#opening_balance", function(e){
    prevent_char(e.which,e);
});
j(document).on("keypress","#other_contact_numbers",function(e){
    if(!(e.which>=48 && e.which<=57 ))
    {
        if(!((e.which == 0) || (e.which==8) || (e.which==44)))
        e.preventDefault();    
    }
});   
function toggle_footer()
{
    j("#footer_div").toggle();
}

function add_transporter()
{
    //j("#transporter_id").val("");
//    j("#mode").val("");
//    j("#transporter_name").val("");
//    j("#transporter_address").val("");
//    j("#city_id").val(0);
//    j("#contact_no").val("");
//    j("#opening_balance").val("");
//    j("#comment").val("");
    j("#transporter_form")[0].reset();
    j("#transporters_dialog").dialog("open");
    j("#transporters_dialog").dialog({"title":"Add Transporters"});
    j("table tr").find("#city_id,#gst_registration_type").chosen();
    j('#transporters_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    disable_scrollIntoView_plugin();
}

function edit_transporter(transporter_id)
{
    j("#mode").val("e");
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
    j("#transporters_dialog").dialog("open");
    j("#transporters_dialog").dialog({"title":"Edit Transporters"});
    j('#transporters_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    disable_scrollIntoView_plugin();
}

function delete_transporter(transporter_id)
{
    if(confirm("Are you sure?"))
    {
        j.get("index.php?option=com_master&task=delete_transporter&tmpl=xml&transporter_id=" + transporter_id, function(data){
            if(data == "ok")
            {
                alert("transporter delete successfully.");
                go(window.location);  
            }
        });
    }
    else
    {
        return false;
    }
}

function show_transporter_account(transporter_id)
{
    window.open('index.php?option=com_amittrading&view=transporter_account&transporter_id=' + transporter_id, "transporter_account" + transporter_id, "height=" + screen.height + ", width=" + screen.width).focus();
}

function disable_scrollIntoView_plugin()
{
    j("#transporterList").removeClass("scrollIntoView");
}

function enable_scrollIntoView_plugin()
{
    j("#transporterList").addClass("scrollIntoView");
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
<h1>Transporters</h1>
<!--<input type="button" value="New Transporters" id="add_transporter" onclick="add_transporter();">-->
<button type="button" id="add_transporter" onclick="add_transporter();"><u>A</u>dd New Transporters</button>
&nbsp;<a href="javascript:void(0);" onclick="toggle_footer();">#</a><br /><br />

<div id="suppliers_list">
    <table id="transporterList" class="clean centreheadings scrollIntoView">
        <tr>
            <th>#</th>
            <th><a href="index.php?option=com_master&view=manage_transporters&so=<? echo base64_encode("transporter_name"); ?>" <? echo ($this->sort_order == "transporter_name" ? "style='color:green;'" : ""); ?>>Name</a></th>
            <th><a href="index.php?option=com_master&view=manage_transporters&so=<? echo base64_encode("account_balance"); ?>" <? echo ($this->sort_order == "account_balance" ? "style='color:green;'" : ""); ?>>Account Balance</a></th>
            <th><a href="index.php?option=com_master&view=manage_transporters&so=<? echo base64_encode("transporter_address"); ?>" <? echo ($this->sort_order == "transporter_address" ? "style='color:green;'" : ""); ?>>Address</a></th>
            <th><a href="index.php?option=com_master&view=manage_transporters&so=<? echo base64_encode("city"); ?>" <? echo ($this->sort_order == "city" ? "style='color:green;'" : ""); ?>>City</a></th>
            <!--<th><a href="index.php?option=com_master&view=manage_transporters&so=<? //echo base64_encode("state"); ?>" <? //echo ($this->sort_order == "state" ? "style='color:green;'" : ""); ?>>State</a></th>
            <th>GST State Code</th>
            <th>GST Registration Type</th>
            <th>GSTIN</th>-->
            <th>Contact No</th>
            <th>Other Contact No.</th>
            <!--<th><a href="index.php?option=com_master&view=manage_transporters&so=<? //echo base64_encode("opening_balance"); ?>" <? //echo ($this->sort_order == "opening_balance" ? "style='color:green;'" : ""); ?>>Opening Balance</a></th>-->
            <th>Comments</th>
            <!--<th>Action</th>-->
        </tr>
        <?
            $x = 1;
            foreach($this->transporters as $transporter)
            {
                $total_account_balance += $transporter->account_balance;
                ?>
                <tr style="cursor:pointer;" ondblclick="show_transporter_account(<? echo $transporter->id;?>);" transporter_id="<? echo $transporter->id;?>" class="transporter">
                    <td align="center"><? echo $x++; ?></td>
                    <td><a href="index.php?option=com_amittrading&view=transporter_account&transporter_id=<? echo $transporter->id; ?>"><? echo $transporter->transporter_name; ?></a></td>
                    <td align="right"><? echo $transporter->account_balance; ?></td>
                    <td style="width: 20%;"><? echo $transporter->transporter_address; ?></td>
                    <td><? echo $transporter->city; ?></td>
                    <!--<td><? //echo $transporter->state; ?></td>
                    <td align="center"><? //echo $transporter->gst_state_code; ?></td>
                    <td>
                    <?
                       /* switch($transporter->gst_registration_type)
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
                    <td><? //echo $transporter->gstin; ?></td>-->
                    <td><? echo $transporter->contact_no; ?></td>
                    <td><? echo $transporter->other_contact_numbers; ?></td>
                    <!--<td align="right"><? //echo $supplier->opening_balance; ?></td>-->
                    <td><? echo $transporter->comment; ?></td>
                    <!--<td align="center">
                        <a href="index.php?option=com_amittrading&view=supplier_account&transporter_id=<? //echo $supplier->id; ?>"><img src="custom/graphics/icons/blank.gif" title="View Account" class="view"></a>
                        <?
                            /*if(is_admin())
                            {
                                ?>
                                <a href="#" onclick="edit_transporter(<? //echo $transporter->id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_transporter(<? //echo $transporter->id; ?>); return false;" class="delete">
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
<div id="footer_div">
    <b>Total Outstanding Amount : <? echo round_2dp($total_account_balance); ?>/-</b>
</div>

<input type="hidden" id="mode" value="">
<input type="hidden" id="transporter_id" value="" name="transporter_id">
<div id="transporters_dialog" style="display:none;">
    <form id="transporter_form">
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
                <td>Comments</td>
                <td><input type="text" name="comment" id="comment" style="width:270px;"></td>
              </tr>
        </table>
    </form>
</div>