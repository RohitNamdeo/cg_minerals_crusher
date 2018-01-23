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
    j("#suppliers_dialog").dialog({
        autoOpen: false,
        width: 450,
        height: 300,
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
                
                if(j("#mode").val() == "e")
                {
                    j.get("index.php?option=com_master&task=update_supplier&tmpl=xml&" + j("#supplier_form").serialize() + "&supplier_id=" + j("#supplier_id").val(), function(data){
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
                }
                else
                {
                    j.get("index.php?option=com_master&task=create_supplier&tmpl=xml&" + j("#supplier_form").serialize() , function(data){
                       if(data == "ok") 
                       {
                           j("#suppliers_dialog").dialog("close");
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
            } 
        }
    });
    
    j("#supplierList").find("tr.supplier").first().addClass("clickedRow").click();   
});

var isTableActive = false;

// every time that there's a click, detect if it was on the table or outside of it
j(document).on("click", function(e) {
    isTableActive = j.contains(document.getElementById("supplierList"), e.target);
});

j(document).on("click", ".supplier", function(){
    j(this).closest("table").find(".clickedRow").removeClass('clickedRow');
    j(this).addClass('clickedRow');
});

j(document).on("keydown", function(e) {
    switch(e.keyCode) {
        case 38: // up
            if (j("#supplierList tr.clickedRow").prev().length) {
                j("#supplierList tr.clickedRow").removeClass("clickedRow").prev().addClass("clickedRow");
            }
            break;
        case 40: // down
            if (j("#supplierList tr.clickedRow").next().length) {
                j("#supplierList tr.clickedRow").removeClass("clickedRow").next().addClass("clickedRow");
            }
            break;
        case 13: // enter
            var supplier_id = j("#supplierList tr.clickedRow").attr("supplier_id");
            show_supplier_account(supplier_id);
            break;
    }
    
    //j("#supplierList tbody tr.clickedRow")[0].scrollIntoView();
    if (j("#supplierList tbody tr.clickedRow").offset().top < window.scrollY)
    {
        j("html, body").scrollTop(j("#supplierList tbody tr.clickedRow").offset().top);
    }else if (j("#supplierList tbody tr.clickedRow").offset().top + j("#supplierList tbody tr.clickedRow").height() > window.scrollY + (window.innerHeight || document.documentElement.clientHeight) - 50)
    {
        j("html, body").scrollTop(j("#supplierList tbody tr.clickedRow").offset().top + j("#supplierList tbody tr.clickedRow").height() - (window.innerHeight || document.documentElement.clientHeight) + 50);
    }
     
    //var scrolltop_value =  parseFloat(j("#supplierList tbody tr.clickedRow").offset().top.toFixed(2)) + parseFloat(j("#supplierList tbody").scrollTop().toFixed(2)) - parseFloat(j("#supplierList tbody").offset().top.toFixed(2));
    //j(window).scrollTop(scrolltop_value.toFixed(2));

    //if (isTableActive)
    if (e.keyCode != 70 && e.keyCode != 127 && e.keyCode != 17)
    {
        e.preventDefault();
        return false;
    }
}); 

j(document).on("keypress", "#opening_balance", function(e){
    prevent_char(e.which,e);
});

function add_supplier()
{
    j("#supplier_id").val("");
    j("#mode").val("");
    j("#supplier_name").val("");
    j("#supplier_address").val("");
    j("#city_id").val(0);
    j("#contact_no").val("");
    j("#opening_balance").val("");
    j("#comment").val("");
    j("#suppliers_dialog").dialog("open");
    j("#suppliers_dialog").dialog({"title":"Add Supplier"});
    j('#suppliers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
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
            j("#contact_no").val(supplier_details.contact_no);
            j("#opening_balance").val(supplier_details.opening_balance);
            j("#comment").val(supplier_details.comment);
       } 
    });   
    j("#suppliers_dialog").dialog("open");
    j("#suppliers_dialog").dialog({"title":"Edit Supplier"});
    j('#suppliers_dialog').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
}

function  delete_supplier(supplier_id)
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
</script>
<h1>Suppliers</h1>
<input type="button" value="Add Supplier" id="add_supplier" onclick="add_supplier();"><br /><br />

<div id="suppliers_list">
    <table id="supplierList" class="clean centreheadings floatheader">
        <tr>
            <th>#</th>
            <th><a href="index.php?option=com_master&view=manage_suppliers&so=<? echo base64_encode("supplier_name"); ?>" <? echo ($this->sort_order == "supplier_name" ? "style='color:green;'" : ""); ?>>Name</a></th>
            <th><a href="index.php?option=com_master&view=manage_suppliers&so=<? echo base64_encode("account_balance"); ?>" <? echo ($this->sort_order == "account_balance" ? "style='color:green;'" : ""); ?>>Account Balance</a></th>
            <th><a href="index.php?option=com_master&view=manage_suppliers&so=<? echo base64_encode("supplier_address"); ?>" <? echo ($this->sort_order == "supplier_address" ? "style='color:green;'" : ""); ?>>Address</a></th>
            <th><a href="index.php?option=com_master&view=manage_suppliers&so=<? echo base64_encode("city"); ?>" <? echo ($this->sort_order == "city" ? "style='color:green;'" : ""); ?>>City</a></th>
            <th>Contact No</th>
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
                    <td><? echo $supplier->supplier_address; ?></td>
                    <td><? echo $supplier->city; ?></td>
                    <td><? echo $supplier->contact_no; ?></td>
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
                <td>Comments</td>
                <td><input type="text" name="comment" id="comment" style="width:270px;"></td>
              </tr>
        </table>
    </form>
</div>