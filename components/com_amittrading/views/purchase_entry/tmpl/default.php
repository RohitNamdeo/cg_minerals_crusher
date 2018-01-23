<?php
    defined('_JEXEC') or die('Restricted access');
?>
<style>
    .add_items
    {
        width:100px;
    }
    tbody
    {
        counter-reset: Count-Value;
    }
    table
    {
        border-collapse: separate;
    }
    tbody tr #idd:first-child:before
    {
        counter-increment: Count-Value;   
        content: counter(Count-Value);
    }
    .custom
    {
        width:150px;
    }
   
</style>
<script>
    
     j(function(){
        j("#purchase_date").datepicker({"dateFormat" : "dd-M-yy", changeMonth: true, changeYear: true});
        j("#supplier_id,#bill_type,#vehicle_id,#product_id").chosen({allow_single_deselect:true});
        j("#supplier_id").trigger("liszt:activate");
        j("#items").append(j("#dummy_item").html());
        j("#items tr").find(".items").chosen();
        //j(".items").chosen({allow_single_deselect:true});

        
        <? 
            if($this->supplier_id > 0)
            {
                
                ?>show_supplier_details();<?
            }    
        ?>
        
    }); 
    
    j(document).on("change","#bill_type", function(e)
    {
        invoice_type = j(this).val();
        
        if(invoice_type == <? echo BILL; ?>)
        {
            j(".gst_fields").show();
            j("#total_header").attr("colspan", "8");
            j("#add_more_action").attr("colspan", "10");
        }
        else
        {
            j(".gst_fields").hide();
            j("#total_header").attr("colspan", "5");
            j("#add_more_action").attr("colspan", "8");
            j(".product_gross_amount, .gst_amount, .gst_percent").val("");
            j(".gst_percent").html("");
        }
        
        j("#items tr").each(function(){
            var total_amount = 0;
            var product_mt = parseFloat(j(this).find(".product_mt").val());
            var product_rate = parseFloat(j(this).find(".product_rate").val());
            
            var gst_percent =  j(this).find(".items").find("option:selected").attr("gst_percent"); 
            //var gst_percent = parseFloat(j(this).find(".gst_percent").val());
            
            if(product_mt == "" || isNaN(product_mt))
            {
                product_mt = 0;   
            }
            if(product_rate == "" || isNaN(product_rate))
            {
                product_rate = 0;   
            }
            if(gst_percent == "" || isNaN(gst_percent))
            {
                gst_percent = 0;   
            }

            var product_gross_amount = parseFloat(product_mt * product_rate);
            j(this).find(".product_gross_amount").val(product_gross_amount); 
            gst_amount = 0; 
             
            if(invoice_type == <? echo BILL; ?>)
            { 
                gst_amount = parseFloat(product_gross_amount * gst_percent /100);
                j(this).find(".gst_amount").text(gst_amount); 
                j(this).find(".gst_amount").val(gst_amount);
                
                j(this).find(".gst_percent").html(gst_percent);
                j(this).find(".gst_percent").val(gst_percent);
            }
            
            j(this).find(".product_total_amount").val(product_gross_amount + gst_amount); 
            
            calculate_total_amount();
        });
    });
    
    j(document).on("keydown", function(e){
        if(e.altKey && e.which == 65)
        {
            add_more_items();
        }
    });
    function show_supplier_details()
    {
        var supplier_name = j("#supplier_id").find("option:selected").attr("supplier_name");
        j(this).html(supplier_name);
    }
    
    j(document).on("keypress",".quantity, #credit_days",function(e){
        strict_numbers(e.which,e);
        j(this).css({'border':'1px solid #7F9DB9'});
    });
    ////////////////
    
    j(document).on("click",".product_delete", function(){
        if(j("#items tr").length == 1)
        {
            alert("Purchase must have at least 1 item.");
            return false;
        }
        j(this).parent().parent().remove();
        product_calculate(j(this));
    });
    
    
    j(document).on("keyup change",".product_rate", function(){
        product_calculate(j(this))
    }); 
    j(document).on("keyup change",".product_mt", function(){
        product_calculate(j(this))
    });
    /*j(document).on("change","#product_id",function(){
        j.get("index.php?option=com_amittrading&task=fetch_unit_gst&tmpl=xml&product_id=" + j("#product_id").val(), function(data){
            var product_details = j.parseJSON(data);  
            if(j("#product_id").val() == "")
            {
               j(".unit_name").text("");
            }
            j(".unit_name").text(product_details.unit);
        });
    });*/
    
    j(document).on("change",".items", function(){
        var invoice_type = j("#bill_type").val();
        
        if(invoice_type == <? echo BILL;?>)
        {
            var gst_percent =  j(this).find("option:selected").attr("gst_percent"); 
            var unit_id =  j(this).find("option:selected").attr("unit_id");
            var unit_name =  j(this).find("option:selected").attr("unit_name");
             
            j(this).parent().parent().find(".gst_percent").text(gst_percent);
            j(this).parent().parent().find(".gst_percent").val(gst_percent);
            
            j(this).parent().parent().find(".unit_id").text(unit_name);
            j(this).parent().parent().find(".unit_id").val(unit_id);
        }
        else
        {
            var unit_id =  j(this).find("option:selected").attr("unit_id");
            var unit_name =  j(this).find("option:selected").attr("unit_name");
            j(this).parent().parent().find(".unit_id").text(unit_name);
            j(this).parent().parent().find(".unit_id").val(unit_id);    
        }
        product_calculate(j(this));
    });
    
    function product_calculate(object)
    {   
        var total_amount = 0;
        var invoice_type = j("#bill_type").val();
        var product_mt = parseFloat(j(object).parent().parent().find(".product_mt").val());
        var product_rate = parseFloat(j(object).parent().parent().find(".product_rate").val());
        var gst_percent = parseFloat(j(object).parent().parent().find(".gst_percent").val());
        
       
        if(product_mt == "" || isNaN(product_mt))
        {
            product_mt = 0;   
        }
        if(product_rate == "" || isNaN(product_rate))
        {
            product_rate = 0;   
        }
        if(gst_percent == "" || isNaN(gst_percent))
        {
            gst_percent = 0;   
        }
        
        var product_gross_amount = parseFloat(product_mt * product_rate);
        j(object).parent().parent().find(".product_gross_amount").val(product_gross_amount); 
        gst_amount = 0; 
        
        if(invoice_type == <? echo BILL; ?>)
        { 
            gst_amount = parseFloat(product_gross_amount * gst_percent /100);
            j(object).parent().parent().find(".gst_amount").text(gst_amount); 
            j(object).parent().parent().find(".gst_amount").val(gst_amount);
        }
        
        j(object).parent().parent().find(".product_total_amount").val(product_gross_amount + gst_amount); 
        calculate_total_amount();       
    } 
    
    function calculate_total_amount()
    {
        var product_total_amount = 0;   
        j(".product_total_amount").each(function(){
            total_amount = parseFloat(j(this).val());
            if (!isNaN(total_amount))
            {
                product_total_amount += total_amount;
            }  
        });
        
        var total_gross_amount = 0;   
        j(".product_gross_amount").each(function(){
            var gross_amount = parseFloat(j(this).val());
            if (!isNaN(gross_amount))
            {
                total_gross_amount += gross_amount;
            }  
        });
        
        var total_gst_amount = 0;   
        j(".gst_amount").each(function(){
            gst_amount = parseFloat(j(this).val());
            if (!isNaN(gst_amount))
            {
                total_gst_amount += gst_amount;
            }  
        });  
        
        var total_amount = parseFloat(product_total_amount);
        
        j("#product_grand_total").val(product_total_amount.toFixed(2));
        j("#product_total_amt").html(product_total_amount.toFixed(2));
        j("#total_amount").val(product_total_amount.toFixed(2));
        j("#total_gross_amount").val(total_gross_amount.toFixed(2));
        j("#total_gst_amount").val(total_gst_amount.toFixed(2));
    }
    
    function add_more_items()
    {
        j("#items").append(j("#dummy_item").html());
        j("#items tr").find(".items").chosen();
    }
    
    j(document).on("keypress","#bill_no,#supplier_challan_no,#challan_no,.product_mt",function(e){
        if(!(e.which>=48 && e.which<=57 ))
        {
            if(!((e.which == 0) || (e.which==8)))
            e.preventDefault();    
        }
    }); 
    
    /*j(document).on("blur","#bill_no",function(){
        j.get("index.php?option=com_amittrading&task=get_bill_no&tmpl=xml&bill_no=" + bill_no, function(data){
            if(data == true)
            {
                alert("duplicate bill no."); 
                j("#bill_no").focus();
                return false;
            }
        });
    });*/
    
    j(document).on("keypress",".product_rate,#loading_charges,#waiverage_charges",function(e){
        if(!(e.which>=48 && e.which<=57 ))
        {
            if(!((e.which == 0) || (e.which==8) ||(e.which==46)))
            e.preventDefault();    
        }
    });
    
    j(document).on("keydown","#remarks,.item_note",function (e) {
        if ( e.ctrlKey || e.altKey) 
        {
            e.preventDefault();
        } 
        else
        {
            var key = e.keyCode;
            if (!((key == 8) ||(key == 9) ||(key == 32)||(key == 13) ||(key >= 37 && key <= 40)|| (key >= 65 && key <= 90) )) 
            {
                e.preventDefault();
            }
        }  
    }); 
    
    j(document).on("keyup","#supplier_challan_no, #bill_no, #challan_no",function () {
        var maxLength = 15;
        var text = j(this).val();
        var textLength = text.length;
        if (textLength > maxLength) 
        {
            j(this).val(text.substring(0, (maxLength)));
            alert("Sorry, only " + maxLength + " numbers are allowed");
        }
    });
    
    //j(document).on("keyup","#bill_no",function () {
//        var maxLength = 15;
//        var text = j(this).val();
//        var textLength = text.length;
//        if (textLength > maxLength) 
//        {
//            j(this).val(text.substring(0, (maxLength)));
//            alert("Sorry, you only " + maxLength + " numbers are allowed");
//        }
//    });
    
     //j(document).on("keyup","#supplier_challan_no",function () {
//        var maxLength = 15;
//        var text = j(this).val();
//        var textLength = text.length;
//        if (textLength > maxLength) 
//        {
//            j(this).val(text.substring(0, (maxLength)));
//            alert("Sorry, you only " + maxLength + " numbers are allowed");
//        }
//    });
    // j(document).on("keyup","#supplier_challan_no",function () {
//        var maxLength = 10;
//        var text = j(this).val();
//        var textLength = text.length;
//        if (textLength > maxLength) 
//        {
//            j(this).val(text.substring(0, (maxLength)));
//            alert("Sorry, you only " + maxLength + " numbers are allowed");
//        }
//    });
    
    /*j(document).on("blur","#bill_no", function(){
        var bill_no = j("#bill_no").val();
        current_object = j(this);
         
        if(!isNaN(bill_no) && bill_no!= "")
        {
            j.get("index.php?option=com_amittrading&task=get_bill_no&tmpl=xml&bill_no=" + bill_no, function(data){
                if(data == "true")
                {  
                   alert("Bill number already used.");
                    j(current_object).parent().parent().parent().find("#bill_no").val("");
                    j(current_object).parent().parent().parent().find("#bill_no").focus();
                }        
            }); 
        }   
    });*/  
    
    function validateForm()
    {
        if(j("#purchase_date").val() == "")
        {
            alert("Please select purchase date");
            return false;
        }
        if(j("#supplier_id").val() == 0)
        {
            alert("Please select supplier name");
            return false;    
        }
        if(j("#bill_no").val() == "")
        {
            alert("Please select bill number");
            return false;    
        }
        if(j("#supplier_challan_no").val() == "")
        {
            alert("Please select supplier challan number");
            return false;    
        }
        if(j("#challan_no").val() == "")
        {
            alert("Please select challan number");
            return false;    
        }
        if(j("#vehicle_id").val() == 0)
        {
            alert("Please select vehicle number");
            return false;    
        }  
        
        var error_in = "";
        j("#items tr").each(function (){
            if(j(this).find(".items").val() == 0)
            {
                error_in = "items";
                return false;
            }
            if(j(this).find(".product_mt").val() == "" || j(this).find(".product_mt").val() == 0) 
            {
                error_in = "product_mt";
                return false;
            }
            if(j(this).find(".product_rate").val() == "" || j(this).find(".product_rate").val() == 0)
            {
                error_in = "product_rate";
                return false;
            }
        });
        if(error_in != "")
        {
            if(error_in == "items")
            {
                alert("Select product item."); return false;
            }
            if(error_in == "product_mt")
            {
                alert("Fill Product quantity(MT) "); return false;
            }
            if(error_in == "product_rate")
            {
                alert("Fill Product rate"); return false;
            }
        } 
        
        /*var bill_no = j("#bill_no").val();
        var supplier_challan_no = j("#supplier_challan_no").val();
        var challan_no = j("#challan_no").val();
        current_object = j(this);
        if(!isNaN(bill_no) && bill_no!= "")
        {
            j.get("index.php?option=com_amittrading&task=get_bill_no&tmpl=xml&bill_no=" + bill_no + "&supplier_challan_no=" + supplier_challan_no + "&challan_no=" + challan_no, function(data){
                //alert(data);exit;
                if(data == "true")
                {  
                   alert("Bill number already used.");
                    j("#bill_no").focus();
                } 
                else
                {
                    
                    j("#purchase_invoice").submit();
                    j("#submit_btn").attr("disabled", "disabled");    
                }       
            }); 
        }*/
        
        var bill_no = j("#bill_no").val();
        var supplier_challan_no = j("#supplier_challan_no").val();
        var challan_no = j("#challan_no").val();
        if( (!isNaN(bill_no) && bill_no!= "") || (!isNaN(supplier_challan_no) && supplier_challan_no!= "") || (!isNaN(challan_no) && challan_no!= "") )
        {
            j.get("index.php?option=com_amittrading&task=get_bill_no&tmpl=xml&bill_no=" + bill_no + "&supplier_challan_no=" + supplier_challan_no + "&challan_no=" + challan_no, function(data){
                if(data == "bill_no")
                {  
                   alert("Bill number already used.");
                    j("#bill_no").focus();
                } 
                else
                {
                    if(data == "supplier_challan_no")
                    {  
                       alert("Supplier challan number already used.");
                        j("#supplier_challan_no").focus();
                    }
                    else
                    {
                        if(data == "challan_no")
                        {  
                           alert("Challan number already used.");
                            j("#challan_no").focus();
                        }
                        else
                        {
                            j("#purchase_invoice").submit();
                            j("#submit_btn").attr("disabled", "disabled");    
                        }
                    }
                }       
            }); 
        }
    }
          
</script>
<br><br>
<h1>Purchase Invoice</h1>
<form method="post" action="index.php?option=com_amittrading&task=save_purchase_entry" id="purchase_invoice" >
<div style="width:100%;">
    <div style="float:left;">
        <table class="clean">
            <tr>
                <td>Purchase Date</td>
                <td><input type="text" name="purchase_date" id="purchase_date" class="custom" value="<? echo date("d-M-Y"); ?>" tabindex="1" readonly="readonly"></td>
            </tr>
            <tr>
                <td>Supplier Name</td>     
                <td>
                    <select id="supplier_id" name="supplier_id" class="custom" onchange="show_supplier_details();" tabindex="2">
                        <option></option>
                        <?
                            foreach($this->suppliers as $supplier)
                            {
                                //echo "<option value='".$supplier->id."'>" . $supplier->supplier_name . "</option>";
                                ?>
                                    <option value="<? echo $supplier->id; ?>" supplier_name="<? echo $supplier->supplier_name ; ?>" <? echo ($supplier->id == $this->supplier_id ? "selected='selected'" : "");?>> <? echo $supplier->supplier_name ;?></option>
                                <?
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td >Bill Type : </td>
                <td>
                    <select name="bill_type" id="bill_type" class="custom" tabindex="3">
                        <option value="0"></option>
                        <option value="<? echo BILL; ?>" selected="selected">Bill</option>
                        <option value="<? echo CHALLAN; ?>" >Challan</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Bill No</td>
                <td><input type="text" name="bill_no" id="bill_no" class="custom" tabindex="4"></td>
            </tr>
            <tr>
                <td>Supplier Challan No</td>
                <td><input type="text" name="supplier_challan_no" id="supplier_challan_no" class="custom" tabindex="5"></td>
            </tr>
            <tr>
                <td>Challan No</td>
                <td><input type="text" name="challan_no" id="challan_no" class="custom" tabindex="6"></td>
            </tr>
        </table>
        </div>
    <div style="float:left;margin-right:20px;margin-left:20px;">
        <table class="clean centreheadings">
            <tr>
                <td>Vehicle No</td>
                <td>
                    <select name="vehicle_id" id="vehicle_id" class="custom" tabindex="7">
                        <option></option>
                        <?
                            foreach($this->vehicles as $vehicle)
                            {
                                echo "<option value='".$vehicle->id."'>" . $vehicle->vehicle_number . "</option>";
                            }
                        ?>
                </select>
                </td>
            </tr>
            
            <tr>
                <td>Loading Charges</td>     
                <td><input type="text" name="loading_charges" id="loading_charges" class="custom" tabindex="8"></td>
             </tr>
             <tr>
                <td>Waiverage Charges</td>     
                <td><input type="text" name="waiverage_charges" id="waiverage_charges" class="custom" tabindex="9"></td>
             </tr>
             <tr>
                <td>Remarks</td>     
                <td><input type="text" name="remarks" id="remarks" class="custom" tabindex="10"></td>
             </tr>
        </table>
    </div>
    
    <br /><br /><br />
    <div style="float:left;margin-top:40px;clear:both;">
        <table class="clean centreheadings">
            <thead>
                <tr><td style="font-size:16px;" colspan="9">Items</td></tr>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Unit</th>
                    <th>Qty(MT)</th>
                    <th>Rate</th>
                    <th class="gst_fields">Gross Amt</th>
                    <th class="gst_fields">GST Percent</th>
                    <th class="gst_fields">GST Amount</th>
                    <th>Total Amount</th>
                    <th>Note</th>
                    <th>Action</th>
               </tr>
            </thead>
            <tbody id="items">
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" align="right" id="total_header"><b>Total : </b></td>
                    <td id="product_total_amt" align="right"></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td id="add_more_action" colspan="7"><button onclick="add_more_items(); return false;"><u>A</u>dd More</button></td>
                </tr>       
            </tfoot>
        </table>
        <table>  
            <tr style="border: none;">
                <td>
                    <input type="button" value="Submit (Alt + Z)" id="submit_btn" onclick="validateForm();return false;">
                    <input type="button" value="Cancel" onclick="history.go('-1');">
                </td>
            </tr>
        </table>
    </div>
    <input type="hidden" name="product_grand_total" id="product_grand_total" />
    <input type="hidden" name="total_amount" id="total_amount" />
    <input type="hidden" name="total_gst_amount" id="total_gst_amount" />
    <input type="hidden" name="total_gross_amount" id="total_gross_amount" />
</div>
</form>

<table style="display:none;">
    <tbody id="dummy_item">
         <tr>
            <td id="idd"></td>
            <td>
                <select name="item_id[]" class="items" id="item" style="width:200px;" tabindex="11">
                    <option></option>
                    <?
                    if(count($this->products) > 0)
                    {
                        foreach($this->products as $product)
                        {
                            ?><option value="<? echo $product->id; ?>" gst_percent="<? echo $product->gst_percent;?>" unit_id="<? echo $product->unit_id;?>"  unit_name="<? echo $product->unit_name;?>"><? echo $product->product_name; ?></option><?
                        }
                    }
                ?>
                </select>
            </td>
            <td>
                <span class="unit_id"></span>
                <input type="hidden" name="unit_id[]" class="unit_id" id="unit_id" value="<? echo $product->unit_id;?>">    
            </td>
            <td><input type="text" name="product_mt[]" size="6%" class="product_mt" tabindex="12"></td>
            <td><input type="text" name="product_rate[]" size="6%" class="product_rate" style="text-align:right;" tabindex="13"></td>
            <td class="gst_fields"><input type="text" name="product_gross_amount[]" class="product_gross_amount" readonly="readonly" style="text-align:right;" tabindex="14"></td>
            <td class="gst_fields" align="right">
                <span class="gst_percent"></span>
                <input type="hidden" name="gst_percent[]" class="gst_percent" id="gst_percent">
            </td>
            <td class="gst_fields">
                <input type="text" name="gst_amount[]" class="gst_amount" id="gst_amount" readonly="readonly" style="text-align:right;" tabindex="15">
            </td>
            <td><input type="text" name="product_total_amount[]" class="product_total_amount" readonly="readonly" style="text-align:right;" tabindex="16"></td>
            <td><input type="text" name="item_note[]"  class="item_note add_items" tabindex="17"></td>
            <td align="center"><img src="custom/graphics/icons/blank.gif" class="delete product_delete" title="Delete Row" ></td>   
        </tr>
    </tbody>
</table>


   






