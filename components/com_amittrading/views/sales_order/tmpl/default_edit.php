<?php
    defined('_JEXEC') or die; 
?>

<script>

    j(function(){
        j("#bill_date").datepicker({"dateFormat" : "dd-M-yy", changeMonth: true, changeYear: true});
        j("#time,#bill_type,#customer_id,#vehicle,#royalty,#loading,#royalty_id").chosen({allow_single_deselect:true});
        j("#customer_id").trigger("liszt:activate");
        j("#items tr,#second_items tr").find(".product_items,.mixing_items").chosen();

       /*j("#royalty_name").autocomplete({
            source: [<? //echo $this->customers_array; ?>],
            select: function(event, ui){
                 j(this).val(ui.item.label);
                return false;     
            },
            focus: function(event, ui){
                j(this).val(ui.item.label);
                return false;
            },
            minLength: 2
        });*/
    }); 
            
    j(document).on("keydown", function(e){
        if (e.altKey && e.which == 65)
        {
            add_more_items(1)
        }
        else if(e.altKey && e.which == 77)
        {
            add_more_items(2);
        }
    });
    
    j(document).on("change","#bill_type", function(e)
    {
        //invoice_type = j(this).val();
        //if(invoice_type == <? //echo BILL; ?>)
//        {
//            j(".gst_fields").show();
//            j("#total_header").attr("colspan", "7");
//            j("#add_more_action").attr("colspan", "9");
//        }
//        else
//        {
//            j(".gst_fields").hide();
//            j("#total_header").attr("colspan", "4");
//            j("#add_more_action").attr("colspan", "7");
//            j(".product_gross_amount, .gst_amount, .gst_percent").val("");
//            j(".gst_percent").html("");
//        }
        
        j("#items tr").each(function(){
            product_calculate(j(this).find(".product_items"));
        });
    });
    
    j(document).on("keypress",".quantity, #credit_days",function(e){
        strict_numbers(e.which,e);
        j(this).css({'border':'1px solid #7F9DB9'});
    });
   
    j(document).on("click",".product_delete", function(){
         if(j("#items tr").length == 1)
         {
            alert("Sales must have at least 1 item.");
            return false;
         } 
        j(this).parent().parent().remove();
        product_calculate(j(this));
    });
    j(document).on("click",".delete_mixing", function(){
        if(j("#second_items tr").length == 1)
         {
            alert("Sales must have at least 1 mixing item.");
            return false;
         } 
        j(this).parent().parent().remove();
        //mixing_calculate(j(this));
        product_calculate(j(this));  
    });
    
    j(document).on("keyup change",".product_rate", function(){
        product_calculate(j(this));
    }); 
    j(document).on("keyup change",".product_mt", function(){
        product_calculate(j(this));
    });
    
    j(document).on("keyup change",".mixing_rate", function(){
        product_calculate(j(this));
    });
    j(document).on("keyup change",".mixing_mt", function(){
        product_calculate(j(this))
    });
    
    j(document).on("change",".product_items", function(){
        var invoice_type = j("#bill_type").val();
        
        if(invoice_type == <? echo BILL;?>)
        {
            var gst_percent =  j(this).find("option:selected").attr("gst_percent"); 
            j(this).parent().parent().find(".gst_percent").text(gst_percent);
            j(this).parent().parent().find(".gst_percent").val(gst_percent);
        }
        product_calculate(j(this));
    });
    
    function product_calculate(object)
    {   
        var total_amount = 0;
        var invoice_type = j("#bill_type").val();
        
        var product_mt = parseFloat(j(object).parent().parent().find(".product_mt").val());
        var product_rate = parseFloat(j(object).parent().parent().find(".product_rate").val());
        
        var main_item_qty = parseFloat(j("#items").first("tr").find(".product_mt").val());
        var main_item_rate = parseFloat(j("#items").first("tr").find(".product_rate").val());
        //var gst_percent = parseFloat(j("#items").first("tr").find(".gst_percent").val());
        var gst_percent = parseFloat(0.05);
        
        if(main_item_qty == "" || isNaN(main_item_qty))
        {
            main_item_qty = 0;   
        }
        if(main_item_rate == "" || isNaN(main_item_rate))
        {
            main_item_rate = 0;   
        }
        if(gst_percent == "" || isNaN(gst_percent))
        {
            gst_percent = 0;   
        }
        
        product_total_amount = parseFloat(product_mt * product_rate);
        
        if(!isNaN(product_total_amount))
        {
            j(object).parent().parent().find(".product_total_amount").val(product_total_amount); 
        }
        
        var main_items_total_quantity = 0;
        var main_items_total_amount = 0;
        j("#items").find(".product_mt").each(function(){
            var mi_total_amount = parseFloat(j(this).parent().parent().find(".product_total_amount").val());
            
            if(j(this).val() > 0)
            {
                main_items_total_quantity = main_items_total_quantity + parseFloat(j(this).val());
                main_items_total_amount = main_items_total_amount + mi_total_amount;
            }
        });
                
        var mixing_total_quantity = 0;
        j("#second_items").find(".mixing_mt").each(function(){
            if(j(this).val() > 0)
            {
                mixing_total_quantity = mixing_total_quantity + parseFloat(j(this).val());
            }
        });
        
        total_mixing_amount = parseFloat(mixing_total_quantity) * parseFloat(main_item_rate); 
        
        if(main_items_total_quantity == "" || isNaN(main_items_total_quantity))
        {
            main_items_total_quantity = 0;   
        }
        
        if(mixing_total_quantity == "" || isNaN(mixing_total_quantity))
        {
            mixing_total_quantity = 0;   
        }
        
        var gross_amount = main_items_total_amount + total_mixing_amount;
        var total_qty = parseFloat(main_items_total_quantity) + parseFloat(mixing_total_quantity);
        
        var gst_amount = 0;
        if(invoice_type == <? echo BILL; ?>)
        {      
            gst_amount = parseFloat(gross_amount * gst_percent);
            //j(object).parent().parent().find(".gst_amount").val(gst_amount);
        }
        
        total_amount = parseFloat(gross_amount) + parseFloat(gst_amount);
        
        if(!isNaN(gross_amount))
        {
            j("#gross_amount").val(gross_amount.toFixed(2));
        }
        if(!isNaN(gst_amount))
        {
            j("#total_gst_amount").val(gst_amount.toFixed(2)); 
        }
        if(!isNaN(total_amount))
        {
            j("#total_amount").val(total_amount.toFixed(2));
        }
        
        //var product_gross_amount = parseFloat(product_mt * product_rate);
//        j(object).parent().parent().find(".product_gross_amount").val(product_gross_amount); 
//        gst_amount = 0; 
//  
        //if(invoice_type == <? //echo BILL; ?>)
//        {      
//            gst_amount = parseFloat(product_gross_amount * gst_percent /100);
//            j(object).parent().parent().find(".gst_amount").text(gst_amount); 
//            j(object).parent().parent().find(".gst_amount").val(gst_amount);
//        }
//        
//        j(object).parent().parent().find(".product_total_amount").val(product_gross_amount + gst_amount); 
        
        calculate_total_amount();       
    } 
    
    function mixing_calculate(object)
    {   
        var total_amount = 0;
        var mixing_mt = parseFloat(j(object).parent().parent().find(".mixing_mt").val());
        var mixing_rate = parseFloat(j(object).parent().parent().find(".mixing_rate").val());
        
        mixing_mt = mixing_mt || 0;
        mixing_rate = mixing_rate || 0;
        
        var mixing_total_amount = mixing_mt >= 1 && mixing_rate >= 1 ? parseFloat(mixing_mt * mixing_rate) : 0;
        
        j(object).parent().parent().find(".mixing_total_amount").val(mixing_total_amount);
        
        calculate_total_amount();
    }
    
    function calculate_total_amount()
    {
        var product_sum = 0;   
        j(".product_total_amount").each(function(){
            total_amount = parseFloat(j(this).val());
            if (!isNaN(total_amount))
            {
                product_sum += total_amount;
            }  
        });
        
        var main_items_total_weight = 0;
        j("#items").find(".product_mt").each(function(){
            if(j(this).val() > 0)
            {
                main_items_total_weight = main_items_total_weight + parseFloat(j(this).val());
            }
        });
                
        var mixing_total_weight = 0;
        j("#second_items").find(".mixing_mt").each(function(){
            if(j(this).val() > 0)
            {
                mixing_total_weight = mixing_total_weight + parseFloat(j(this).val());
            }
        });
        
        total_weight = parseFloat(main_items_total_weight) + parseFloat(mixing_total_weight);
               
    //    var mixing_sum = 0;   
//        j(".mixing_total_amount").each(function(){
//            gross_amount = parseFloat(j(this).val());
//            if (!isNaN(gross_amount))
//            {
//                mixing_sum += gross_amount;
//            }  
//        });
//        
//        var total_gst_amount = 0;   
//        j(".gst_amount").each(function(){
//            gst_amount = parseFloat(j(this).val());
//            if (!isNaN(gst_amount))
//            {
//                total_gst_amount += gst_amount;
//            }  
//        });  
//        
//        var total_amount = parseFloat(product_sum + mixing_sum);
        //j("#mixing_grand_total").val(mixing_sum.toFixed(2));
//        j("#mixing_gross_amt").text(mixing_sum.toFixed(2));
//        j("#total_amount").val(total_amount.toFixed(2));
//        j("#total_gst_amount").val(total_gst_amount.toFixed(2));
        
        j("#product_grand_total").val(product_sum.toFixed(2));
        j("#product_gross_amt").text(product_sum.toFixed(2));
        
        if(!isNaN(total_weight))
        {
            j("#total_weight").val(total_weight.toFixed(2));        
        }
    }
    
    function show_customer_details()
    {
        j("#address").html(j("#customer_id").find("option:selected").attr("address"));
        j("#contact_no").html(j("#customer_id").find("option:selected").attr("contact_no"));
        j("#other_contact_nos").html(j("#customer_id").find("option:selected").attr("other_contact_nos"));
    } 
    
    function add_more_items(row)
    {
        if(row == 1)
        {
            index = j("#items tr").length;
            j("#items").append(j("#dummy_item").html());
            j("#items tr").find(".product_items").chosen();
            if(index != 0 ) j("#items").find("tr:last").find(".product_items").trigger("liszt:activate");
        }
        else
        {
            index = j("#second_items tr").length;
            j("#second_items").append(j("#second_dummy_items").html());
            j("#second_items tr").find(".mixing_items").chosen();
            if(index != 0 ) j("#second_items").find("tr:last").find(".mixing_items").trigger("liszt:activate");
        }
        return false;
    }
    
    j(document).on("keypress",".check_num,.royalty_no",function(e){
        if(!(e.which>=48 && e.which<=57 ))
        {
            if(!((e.which == 0) || (e.which==8)))
            //j("#errmsg").html("Digits Only").show().fadeOut("slow");
            e.preventDefault();    
        }
    }); 
    
    j(document).on("keypress",".product_mt,.product_rate,.mixing_mt,.mixing_rate,.point_amount,#waiverage_charges",function(e){
        if(!(e.which>=48 && e.which<=57 ))
        {
            if(!((e.which == 0) || (e.which==8) ||(e.which==46)))
            e.preventDefault();    
        }
    });
    
    j(document).on("keydown","#party_id,.mixing_note,.product_note",function (e) {
        if ( e.ctrlKey || e.altKey) 
        {
            e.preventDefault();
        } 
        else
        {
            var key = e.keyCode;
            if (!((key == 8) ||(key == 9) ||(key == 32)||(key == 13) ||(key >= 37 && key <= 40)|| (key >= 97 && key <= 122) || (key >= 65 && key <= 90) )) 
            {
                e.preventDefault();
            }
        }  
        
    }); 
    
    function validateForm()
    {
        if(j("#bill_date").val() == "")
        {
            alert("Select bill date.");
            return false;
        }
        
        if(j("#bill_type").val() == 0)
        {
            alert("Select bill type."); 
            return false;
        }
        
        if(j("#customer_id").val() == 0)
        {
            alert("Select customer name."); 
            return false;
        }
    
        var error_in = "";
        j("#items tr").each(function (){
            if(j(this).find(".product_items").val() == 0)
            {
                error_in = "items";
                return false;
            }
            if((j(this).find(".product_mt").val() == "" || j(this).find(".product_mt").val() == 0) && (j(this).find(".product_rate").val() == "" || j(this).find(".product_rate").val() == 0))
            {
                error_in = "mt_rate";
                return false;
            }
        });
        if(error_in != "")
        {
            if(error_in == "items")
            {
                alert("Select product item."); return false;
            }
            if(error_in == "mt_rate")
            {
                alert("Fill product quantity(MT) and rate items."); return false;
            }
            
        } 
        if(error_in != "")
        {
            if(error_in == "mixing_items")
            {
                alert("Select mixing item."); return false;
            }
            if(error_in == "mixing_mt_rate")
            {
                alert("Select mixing quantity(MT)."); return false;
            }    
        }   
    
        j("#sales_invoice").submit();
        j("#submit_btn").attr("disabled", "disabled");
    }                                 
    
</script>

<style>

    tr.border_bottom td 
    {
        border-bottom:1px solid black;
    }
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
<br /><br />
<h1>Edit Sales Order</h1>
<form id="sales_invoice" action="index.php?option=com_amittrading&task=update_sales_order" method="post" >
<div style="width:100%;">
    <div style="float:left;">
        <table class="clean">
            <tr>
                <td >Customer : </td>
                <td>
                    <select id="customer_id" name="customer_id" onchange="show_customer_details();" tabindex="1" class="custom">
                        <option value="0"></option>
                        <?
                            if(count($this->customers) > 0)
                            {
                                foreach($this->customers as $customer)
                                {
                                    ?>
                                        <option value="<? echo $customer->id; ?>" address="<? echo $customer->customer_address; ?>" other_contact_nos="<? echo $customer->other_contact_numbers;?>" contact_no="<? echo $customer->contact_no; ?>" <? echo ($this->sales_order->customer_id == $customer->id ? "selected='selected'" : ""); ?> ><? echo $customer->customer_name; ?></option>
                                    <?
                                }
                            }
                        ?>
                    </select>
                </td>    
            </tr>
            
            <tr>
                <td >Address : </td>
                <td id="address" class="custom"><? echo $this->sales_order->customer_address;?></td>
            </tr>
            <tr>
                <td >Contact : </td>
                <td id="contact_no" class="custom check_num"><? echo $this->sales_order->contact_no;?></td>
            </tr>
            <tr>
                <td>Other Contact Numbers : </td>
                <td id="other_contact_nos"><? echo $this->sales_order->other_contact_numbers;?></td>
            </tr>
            <tr>
                <td > Date : </td>
                <td><input type="text" id="bill_date" name="bill_date" value="<? echo date("d-M-Y", strtotime($this->sales_order->order_date)); ?>" tabindex="1" class="custom" readonly></td>
            </tr>
            <!--<tr>
                <td > Time : </td>
                <td>
                    <select name="time" id="time" class="custom">
                        <option></option>
                        <?
                            //$start=strtotime('00:00');
//                            $end=strtotime('23:55');
//                            for ($i=$start;$i<=$end;$i = $i + 5*60)
//                            { 
                              ?>
                                <option value="<? //echo date('H:i A',$i); ?>" <? //echo (date('H:i A',$i) == date('H:i A', strtotime($this->sales->time)) ? "selected=selected" : "");?>><? //echo date('H:i A',$i); ?></option>        
                              <?
                            //}
                        ?>
                        
                    </select>
                </td>
            </tr>-->
            <tr>
                <td>Bill Type : </td>
                <td>
                    <select name="bill_type" id="bill_type" tabindex="2" class="custom"> 
                        <option value="0"></option>
                        <option value="<? echo BILL; ?>" <? echo ($this->sales_order->bill_type == BILL ? "selected='selected'" : ""); ?>>Bill</option>
                        <option value="<? echo CHALLAN; ?>" <? echo ($this->sales_order->bill_type == CHALLAN ? "selected='selected'" : ""); ?>>Challan</option>
                    </select>
                </td>
            </tr>
            <!--<tr>
                <td>Bill / Challan No :</td>
                <td><input type="text" name="challan_no" id="challan_no" class="custom check_num" value="<? //echo $this->sales_order->bill_challan_no;?>"></td>
            </tr>-->
            <tr>
                <td>Royalty Name :</td>
                <td>
                    <select name="royalty_id" id="royalty_id" class="custom" tabindex="2">
                        <option></option>
                        <?
                            if(count($this->royalties) > 0 )
                            {
                                foreach($this->royalties as $royalty)
                                {
                                    ?>
                                        <option value="<? echo $royalty->id;?>" <? echo ($this->sales_order->royalty_id == $royalty->id ? "selected='selected'" : ""); ?>><? echo $royalty->royalty_name;?></option>
                                    <?    
                                }
                            }
                        ?>
                    </select>    
                </td>
            </tr>
            <tr>
                <td>Royalty Rate :</td>
                <td><input type="text" name="royalty_rate" id="royalty_rate" class="custom point_amount" value="<? echo $this->sales_order->royalty_rate;?>" tabindex="3"/></td>
            </tr>
            
        </table>
    </div>
    
    <div style="float:left;;margin-left:10px;">
        <table class="clean centreheadings">
            <tr>
                <td>Gross Amount : </td>
                <td><input type="text" name="gross_amount" id="gross_amount" value="<? echo $this->sales_order->gross_amount; ?>" readonly="readonly" tabindex="-1"/></td>
            </tr>
            <tr>
                <td>Gst Amount : </td>
                <td><input type="text" name="total_gst_amount" id="total_gst_amount" value="<? echo $this->sales_order->gst_amount; ?>" readonly="readonly" tabindex="-1"/></td>
            </tr>
            <tr>
                <td>Total Amount : </td>
                <td>
                    <input type="text" name="total_amount" id="total_amount" value="<? echo $this->sales_order->total_amount; ?>" readonly="readonly" tabindex="-1"/>
                </td>
            </tr>
            <tr>
                <td>Total Weight : </td>
                <td><input type="text" name="total_weight" id="total_weight" value="<? echo $this->sales_order->total_weight; ?>" readonly="readonly" tabindex="-1"/></td>
            </tr>
        </table>
    </div>
    <br /><br />
    <div style="float:left;margin-top:40px;clear:both;">
        <table class="clean centreheadings">
            <thead>
                <tr><td style="font-size:16px;" colspan="9">Items</td></tr>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Qty(MT)</th>
                    <th>Rate</th>                         
                <!--<th class="gst_fields">Gross Amt</th>
                    <th class="gst_fields">GST Percent</th>
                    <th class="gst_fields">GST Amount</th>-->
                    <th>Total Amount</th>
                    <th>Note</th>
                    <th>Action</th>
                    <td><button onclick="add_more_items(1); return false;">Add More</button></td>
                </tr>
            </thead>
            <tbody id="items">
                <?
                $product_total_amt = 0;   
                    if(count($this->sales_product_items) > 0)
                    {
                        
                        foreach($this->sales_product_items as $item)
                        {
                                ?> 
                                   <tr>
                                        <td id="idd"></td>
                                        <td>
                                            <select name="product_item_id[]" class="product_items" id="product_items" style="width:200px;" tabindex="5">
                                                <option></option>
                                                <?
                                                if(count($this->products) > 0)
                                                {
                                                    foreach($this->products as $product)
                                                    {
                                                        ?><option value="<? echo $product->id; ?>" gst_percent="<? echo $product->gst_percent;?>" <? echo ($item->product_id == $product->id ? "selected='selected'" : "");?>><? echo $product->product_name; ?></option><?
                                                    }
                                                }
                                            ?>
                                            </select>
                                            <!--<input type="hidden" name="product_items_id[]" value="<? //echo $product->id; ?>" > -->
                                            <!--<input type="hidden" name="product_item_type" id="product_item_type" value="<? //echo PRODUCT ; ?>">   -->
                                        </td>
                                        <td><input type="text" name="product_mt[]" size="6%" class="product_mt" value="<? echo $item->actual_weight; ?>" tabindex="6"></td>
                                        <td><input type="text" name="product_rate[]" size="6%" class="product_rate" style="text-align:right;" value="<? echo $item->product_rate; ?>" tabindex="7"></td>
                                        <!--<td class="gst_fields"><input type="text" name="product_gross_amount[]" class="product_gross_amount" readonly="readonly" style="text-align:right;" value="<? //echo $item->gross_amount; ?>"></td>
                                        <td class="gst_fields" align="right">
                                            <span class="gst_percent"><? //echo $item->gst_percent;?></span>
                                            <input type="hidden" name="gst_percent[]" class="gst_percent" id="gst_percent" value="<? //echo $item->gst_percent;?>">
                                        </td>
                                        <td class="gst_fields" >
                                            <!--<span class="gst_amount"></span>-->
                                            <!--<input type="hidden" name="gst_amount[]" class="gst_amount" id="gst_amount">
                                            <input type="text" name="gst_amount[]" class="gst_amount" id="gst_amount" readonly="readonly" style="text-align:right;" value="<? //echo $item->gst_amount;?>">
                                        </td>      -->
                                        <td><input type="text" name="product_total_amount[]" class="product_total_amount" readonly="readonly" style="text-align:right;" value="<? $product_total_amt += $item->actual_weight * $item->product_rate;   echo $item->actual_weight * $item->product_rate;?>"></td>
                                        <td><input type="text" name="product_note[]"  class="product_note add_items" value="<? echo $item->product_note; ?>" tabindex="8"></td>
                                        <td align="center"><img src="custom/graphics/icons/blank.gif" class="delete product_delete" title="Delete Row" ></td>   
                                    </tr>
                                <?
                        }
                    } 
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" align="right" id="total_header"><b>Total : </b></td>
                    <td id="product_gross_amt" align="right"><? echo $product_total_amt; ?></td>
                    <td colspan="2"></td>
                </tr>
                <!--<tr>
                    <td id="add_more_action" colspan="7"><button onclick="add_more_items(1); return false;"><u>A</u>dd More</button></td>
                </tr>-->       
            </tfoot>
            <!--<tfoot>
                <tr>
                    <td colspan="6" align="right" id="total_header"><b>Total : </b>
                        <input type="hidden" name="product_gross_amount" id="product_gross_amount">  
                    </td>
                    <td id="product_gross_amt" align="right"><? //echo $total_amount;?></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td id="add_more_action" colspan="9"><button onclick="add_more_items(1); return false;"><u>A</u>dd More</button></td>
                </tr>       
            </tfoot>-->
        </table>
        <table class="clean centreheadings">
            <thead>
                <tr><td style="font-size:16px;" colspan="7">Mixing</td></tr>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Qty(MT)</th>
                    <!--<th>Rate</th>
                    <th>Amount</th>-->
                    <th>Note</th>
                    <th>Action</th>
                    <td><button onclick="add_more_items(2); return false;" tabindex="13"><u>A</u>dd More</button></td>
                </tr>
            </thead>
            <tbody id="second_items">
                <?
                    if(count($this->sales_mixing_items) > 0)
                    {
                        foreach($this->sales_mixing_items as $item)
                        {
                            ?>
                                <tr>
                                    <td id="idd"></td>
                                    <td>
                                        <select name="mixing_items_id[]" class="mixing_items" id="mixing_items" style="width:200px;" tabindex="10">
                                            <option></option>
                                            <?
                                                if(count($this->products) > 0)
                                                {
                                                   foreach($this->products as $product)
                                                    {
                                                        ?><option value="<? echo $product->id; ?>" <? echo ($item->product_id == $product->id ? "selected='selected'" : "");?>><? echo $product->product_name; ?></option><?
                                                    }
                                                }
                                            ?>
                                        </select> 
                                        <!--<input type="hidden" name="mixing_item_type" value="<? //echo MIXING ; ?>">   -->
                                    </td>
                                    <td><input type="text" name="mixing_mt[]" id="mixing_mt" size="6%" class="mixing_mt" value="<? echo $item->quantity;?>" tabindex="11"></td>
                                    <!--<td><input type="text" name="mixing_rate[]" size="6%" class="mixing_rate" style="text-align:right;" value="<? //echo $item->product_rate;?>"></td>-->
                                    <!--<td><input type="text" name="mixing_total_amount[]" class="mixing_total_amount" readonly="readonly" style="text-align:right;" value="<? //echo $item->total_amount;?>"></td>-->
                                    <td><input type="text" name="mixing_note[]"  class="mixing_note add_items" value="<? echo $item->product_note;?>" tabindex="12"></td>
                                    <td align="center"><img src="custom/graphics/icons/blank.gif" class="delete delete_mixing" title="Delete Row" ></td>
                                </tr>
                            <?
                        }
                    }
                ?>
            </tbody>
            <tfoot>
                <!--<tr>
                    <td colspan="4" align="right" id="total_header2"><b>Total : </b></td>
                    <td id="mixing_gross_amt" align="right"> <? //echo floatval($this->sales->mixing_items_total_amount);?></td>
                </tr>-->
                <!--<tr>
                    <td id="add_more_action" colspan="5"><button onclick="add_more_items(2); return false;"><u>A</u>dd More</button></td>
                </tr>-->       
            </tfoot>
            <!--<tfoot>
                <tr>
                    <td colspan="4" align="right" id="total_header2"><b>Total : </b>
                        <input type="hidden" name="mixing_gross_amount" id="mixing_gross_amount"> 
                    </td>
                    <td id="mixing_gross_amt" align="right"><? //echo $total_amount;?></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td id="add_more_action" colspan="7"><button onclick="add_more_items(2); return false;"><u>A</u>dd More</button></td>
                </tr>       
            </tfoot>-->
        </table>
        <table>  
            <tr style="border: none;">
                <td >
                    <input type="button" value="Submit (Alt + Z)" id="submit_btn" onclick="validateForm();" tabindex="14">
                    <input type="button" value="Cancel" onclick="history.go('-1');" tabindex="15">
                </td>
            </tr>
        </table>
        </div>
    <input type="hidden" name="sales_id" value=" <? echo intval($this->sales_id); ?>">
    <!--<input type="hidden" name="r" value="<? //echo base64_encode($this->return); ?>">-->
</div>
</form>


<table style="display:none;">
    <tbody id="dummy_item">
        
         <tr>
            <td id="idd"></td>
            <td>
                <select name="product_item_id[]" class="product_items" id="product_items" style="width:200px;">
                    <option></option>
                    <?
                    if(count($this->products) > 0)
                    {
                        foreach($this->products as $product)
                        {
                            ?><option value="<? echo $product->id; ?>" gst_percent="<? echo $product->gst_percent;?>"><? echo $product->product_name; ?></option><?
                        }
                    }
                ?>
                </select>
                <!--<input type="hidden" name="product_items_id[]" value="<? //echo $product->id; ?>" > -->
                <!--<input type="hidden" name="product_item_type" id="product_item_type" value="<? //echo PRODUCT ; ?>">   -->
            </td>
            <td><input type="text" name="product_mt[]" size="6%" class="product_mt"></td>
            <td><input type="text" name="product_rate[]" size="6%" class="product_rate" style="text-align:right;"></td>
            <!--<td class="gst_fields"><input type="text" name="product_gross_amount[]" class="product_gross_amount" readonly="readonly" style="text-align:right;"></td>
            <td class="gst_fields" align="right">
                <span class="gst_percent"></span>
                <input type="hidden" name="gst_percent[]" class="gst_percent" id="gst_percent">
            </td>
            <td class="gst_fields" >
                <!--<span class="gst_amount"></span>-->
                <!--<input type="hidden" name="gst_amount[]" class="gst_amount" id="gst_amount">
                <input type="text" name="gst_amount[]" class="gst_amount" id="gst_amount" readonly="readonly" style="text-align:right;">
            </td>-->
            <td><input type="text" name="product_total_amount[]" class="product_total_amount" readonly="readonly" style="text-align:right;"></td>
            <td><input type="text" name="product_note[]"  class="product_note add_items"></td>
            <td align="center"><img src="custom/graphics/icons/blank.gif" class="delete product_delete" title="Delete Row" ></td>   
        </tr>
        
    </tbody>
</table>

<table style="display:none;">
    <tbody id="second_dummy_items">
         <tr>
            <td id="idd"></td>
            <td>
                <select name="mixing_items_id[]" class="mixing_items" id="mixing_items" style="width:200px;">
                    <option></option>
                    <?
                    if(count($this->products) > 0)
                    {
                        foreach($this->products as $product)
                        {
                            ?><option value="<? echo $product->id; ?>"><? echo $product->product_name; ?></option><?
                        }
                    }
                ?>
                </select>
                <!--<input type="hidden" name="mixing_items_id[]" value="<? //echo $product->id; ?>" >--> 
                <!--<input type="hidden" name="mixing_item_type" value="<? //echo MIXING ; ?>">  --> 
            </td>
            <td><input type="text" name="mixing_mt[]" size="6%" class="mixing_mt"></td>
            <!--<td><input type="text" name="mixing_rate[]" size="6%" class="mixing_rate" style="text-align:right;"></td>-->
            <!--<td><input type="text" name="mixing_total_amount[]" class="mixing_total_amount" readonly="readonly" style="text-align:right;"></td>-->
            <td><input type="text" name="mixing_note[]"  class="mixing_note add_items"></td>
            <td align="center"><img src="custom/graphics/icons/blank.gif" class="delete delete_mixing" title="Delete Row" ></td>   
        </tr>
    </tbody>
</table>

