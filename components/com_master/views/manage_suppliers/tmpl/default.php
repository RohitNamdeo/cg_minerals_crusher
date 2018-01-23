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
        rowSelector : 'supplier',
        rowAttribute : 'supplier_id',
        task : 'show_supplier_account'
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
      
    j("#suppliers_dialog").dialog({
        autoOpen: false,
        width: 450,
        //height: 300,
        buttons:
        {
            "Submit (Alt+Z)": function()
            {
                
                
                 //alert(j("#supplier_name").val().charAt(0)); exit;
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
                
                if(j("#mode").val() == "e")
                {
                    j.get("index.php?option=com_master&task=update_supplier&tmpl=xml&" + j("#supplier_form").serialize() + "&supplier_id=" + j("#supplier_id").val(), function(data){
                       if(data == "ok") 
                       {
                           j("#suppliers_dialog").dialog("close");
                           enable_scrollIntoView_plugin();
                           alert("Supplier updated successfully.");
                           go(window.location);  
                       }
                       else
                       {
                           alert(data);
                           j('#suppliers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                       }
                    });
                }
                else
                {
                    j.get("index.php?option=com_master&task=create_supplier&tmpl=xml&" + j("#supplier_form").serialize() , function(data){
                       if(data == "ok") 
                       {
                           j("#suppliers_dialog").dialog("close");
                           enable_scrollIntoView_plugin();
                           alert("Supplier saved successfully.");
                           go(window.location);  
                       }
                       else
                       {
                           alert(data);
                           j('#suppliers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
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

/*j(document).on("keydown","#supplier_name",function (e) {
    if ( e.ctrlKey || e.altKey) 
    {
        e.preventDefault();
    } 
    else
    {
        var key = e.keyCode;
        if (!((key == 8) ||(key == 9) ||(key == 32)||(key == 13) || (key >= 65 && key <= 90) )) 
        {
            e.preventDefault();
        }
    }  
}); */
    
j(document).on("keypress","#supplier_name", function(e) {
    if (e.which === 32 && !this.value.length)
        e.preventDefault();
});

j(document).on("keypress","#contact_no",function(e){
    if(!(e.which>=48 && e.which<=57 ))
    {
        if(!((e.which == 0) || (e.which==8)))
        e.preventDefault();    
    }
});
j(document).on("keypress","#other_contact_numbers",function(e){
    if(!(e.which>=48 && e.which<=57 ))
    {
        if(!((e.which == 0) || (e.which==8) || (e.which==44)))
        e.preventDefault();    
    }
});   

j(document).on("keydown", function(e){
    if (e.altKey && e.which == 65)
    {
        add_supplier();
    }
});
j(document).on("keyup", function(e){
    if (e.altKey && e.which == 90)
    {
       j('#submit_button').click();  
    }
});

j(document).on("click", ".supplier", function(){
    j(this).closest("table").find(".clickedRow").removeClass('clickedRow');
    j(this).addClass('clickedRow');
}); 

j(document).on("keypress", "#opening_balance", function(e){
    prevent_char(e.which,e);
});

 //j(document).on("keyup","#supplier_address",function () {
//        var maxLength = 50;
//        var text = j(this).val();
//        var textLength = text.length;
//        if (textLength > maxLength) 
//        {
//            j(this).val(text.substring(0, (maxLength)));
//            alert("Sorry, only " + maxLength + " characters are allowed");
//        }
//    });

function toggle_footer()
{
    j("#footer_div").toggle();
}


function add_supplier()
{
    //j("#supplier_id").val("");
//    j("#mode").val("");
//    j("#supplier_name").val("");
//    j("#supplier_address").val("");
//    j("#city_id").val(0);
//    j("#contact_no").val("");
//    j("#opening_balance").val("");
//    j("#comment").val("");
    j("#supplier_form")[0].reset();
    j("#suppliers_dialog").dialog("open");
    j("#suppliers_dialog").dialog({"title":"Add Supplier"});
    j("table tr").find("#city_id,#gst_registration_type").chosen();
    j('#suppliers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    disable_scrollIntoView_plugin();
}

function edit_supplier(supplier_id)
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
    j('#suppliers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    disable_scrollIntoView_plugin();
}

function delete_supplier(supplier_id)
{
    if(confirm("Are you sure?"))
    {
        j.get("index.php?option=com_master&task=delete_supplier&tmpl=xml&supplier_id=" + supplier_id, function(data){
            if(data == "ok")
            {
                alert("Supplier delete successfully.");
                go(window.location);  
            }
        });
    }
    else
    {
        return false;
    }
}

function show_supplier_account(supplier_id)
{
    window.open('index.php?option=com_amittrading&view=supplier_account&supplier_id=' + supplier_id, "supplier_account" + supplier_id, "height=" + screen.height + ", width=" + screen.width).focus();
}

function disable_scrollIntoView_plugin()
{
    j("#supplierList").removeClass("scrollIntoView");
}

function enable_scrollIntoView_plugin()
{
    j("#supplierList").addClass("scrollIntoView");
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
<h1>Suppliers</h1>

    <!--<input type="button" value="Add Supplier" id="add_supplier" onclick="add_supplier();">-->
    <button type="button" id="add_supplier" onclick="add_supplier();"><u>A</u>dd Supplier</button>
    &nbsp;&nbsp;<a href="javascript:void(0);" onclick="toggle_footer();">#</a><br><br>
 

<div id="suppliers_list">
    <table id="supplierList" class="clean centreheadings scrollIntoView">
        <tr>
            <th>#</th>
            <th><a href="index.php?option=com_master&view=manage_suppliers&so=<? echo base64_encode("supplier_name"); ?>" <? echo ($this->sort_order == "supplier_name" ? "style='color:green;'" : ""); ?>>Name</a></th>
            <th><a href="index.php?option=com_master&view=manage_suppliers&so=<? echo base64_encode("account_balance"); ?>" <? echo ($this->sort_order == "account_balance" ? "style='color:green;'" : ""); ?>>Account Balance</a></th>
            <th><a href="index.php?option=com_master&view=manage_suppliers&so=<? echo base64_encode("supplier_address"); ?>" <? echo ($this->sort_order == "supplier_address" ? "style='color:green;'" : ""); ?>>Address</a></th>
            <th><a href="index.php?option=com_master&view=manage_suppliers&so=<? echo base64_encode("city"); ?>" <? echo ($this->sort_order == "city" ? "style='color:green;'" : ""); ?>>City</a></th>
           <!-- <th><a href="index.php?option=com_master&view=manage_suppliers&so=<? //echo base64_encode("state"); ?>" <? //echo ($this->sort_order == "state" ? "style='color:green;'" : ""); ?>>State</a></th>
            <th>GST State Code</th>
            <th>GST Registration Type</th>
            <th>GSTIN</th>-->
            <th>Contact No</th>
            <th>Other Contact No.</th>
            <!--<th><a href="index.php?option=com_master&view=manage_suppliers&so=<? //echo base64_encode("opening_balance"); ?>" <? //echo ($this->sort_order == "opening_balance" ? "style='color:green;'" : ""); ?>>Opening Balance</a></th>-->
            <th>Comments</th>
            <!--<th>Action</th>-->
        </tr>
        <?
            $x = 1;
            foreach($this->suppliers as $supplier)
            {
                $total_account_balance += $supplier->account_balance;
                ?>
                <tr style="cursor:pointer;" ondblclick="show_supplier_account(<? echo $supplier->id;?>);" supplier_id="<? echo $supplier->id;?>" class="supplier">
                    <td align="center"><? echo $x++; ?></td>
                    <td><a href="index.php?option=com_amittrading&view=supplier_account&supplier_id=<? echo $supplier->id; ?>"><? echo $supplier->supplier_name; ?></a></td>
                    <td align="right"><? echo $supplier->account_balance; ?></td>
                    <td style="width: 20%;"><? echo $supplier->supplier_address; ?></td>
                    <td><? echo $supplier->city; ?></td>
                    <!--<td><? //echo $supplier->state; ?></td>
                    <td align="center"><? //echo $supplier->gst_state_code; ?></td>
                    <td>
                    <?
                        /*switch($supplier->gst_registration_type)
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
                    <td><? //echo $supplier->gstin; ?></td>-->
                    <td><? echo $supplier->contact_no; ?></td>
                    <td><? echo $supplier->other_contact_numbers; ?></td>
                    <!--<td align="right"><? //echo $supplier->opening_balance; ?></td>-->
                    <td><? echo $supplier->comment; ?></td>
                    <!--<td align="center">
                        <a href="index.php?option=com_amittrading&view=supplier_account&supplier_id=<? //echo $supplier->id; ?>"><img src="custom/graphics/icons/blank.gif" title="View Account" class="view"></a>
                        <?
                            /*if(is_admin())
                            {
                                ?>
                                <a href="#" onclick="edit_supplier(<? //echo $supplier->id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_supplier(<? //echo $supplier->id; ?>); return false;" class="delete">
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
<input type="hidden" id="supplier_id" value="" name="supplier_id">
<div id="suppliers_dialog" style="display:none;">
    <form id="supplier_form">
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
                    <select id="city_id" name="city_id" style="width:166px;" onchange="get_state(this.value);">
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