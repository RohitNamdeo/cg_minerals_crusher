<?php
    defined('_JEXEC') or die;
    
    //print_r($this->sales); 
?>
<style>
    tr.border_bottom td {
      border-bottom:1px solid black;
    }
    
    /*#items_table{
        overflow-x: hidden;
        overflow-y: scroll;
        display: inline-block;
        height: 200px;
    }*/
</style>
<script>

    j(function(){
        j("#bill_date").datepicker({"dateFormat" : "dd-M-yy", changeMonth: true, changeYear: true});
        j("#time,#bill_type,#customer_id,#vehicle,#loading,#royalty,#royalty_id").chosen({allow_single_deselect:true});
        
        j("#items tr,#second_items tr").find(".product_items,.mixing_items").chosen();
        j("#royalty_items tr,#royalty_dummy_items tr").find(".party_id").chosen();
        
          j("#royalty_name").autocomplete({
            source: [<? echo $this->customers_array; ?>],
            select: function(event, ui){
                j(this).val(ui.item.label);
                return false;     
            },
            focus: function(event, ui){
                j(this).val(ui.item.label);
                return false;
            },
            minLength: 2
        });
          
        royalty();
        second_royalty();
        loading();
        vehicle();
        calculate_total_amount();  

        invoice_type = j("#bill_type").val();
        
       // if(invoice_type == <? //echo BILL; ?>)
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
    });
            
    //j(document).on("keydown", function(e){
//        if (e.altKey && e.which == 65)
//        {
            //add_more_items();
//        }
//        /*if (e.altKey && e.which == 83)
//        {
//            e.preventDefault();
//            validateForm();
//        }*/
//    });

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
    
    
    j(document).on("keypress",".quantity, #credit_days",function(e){
        strict_numbers(e.which,e);
        j(this).css({'border':'1px solid #7F9DB9'});
    });
    
    j(document).on("change","#bill_type", function(e){
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
    ////////////////
    /*j(document).on("blur",".product_matric_tn", function(){ 
        product_calculate_items();
    });  */
    /*j(document).on("blur",".product_rate", function(){ 
        product_calculate_items();
    }); */
    
    j(document).on("keyup change","#loaded_weight", function(){ 
        get_truck_weight();
    });
    
    j(document).on("keyup change","#empty_weight", function(){ 
        get_truck_weight();
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
         product_calculate(j(this)); 
    });
    
    j(document).on("keyup change","#diesel_liter", function(){
        get_liter_amount();
    });
    j(document).on("keyup change","#diesel_rate", function(){
        get_liter_amount();
    });
    
    j(document).on("keyup change",".product_rate", function(){
        product_calculate(j(this))
    }); 
    j(document).on("keyup change",".product_mt", function(){
        product_calculate(j(this))
    });
    
    j(document).on("keyup change",".mixing_rate", function(){
        //mixing_calculate(j(this))
        product_calculate(j(this)); 
    });
    j(document).on("keyup change",".mixing_mt", function(){
       // mixing_calculate(j(this))
       product_calculate(j(this)); 
    });
    
    j(document).on("change","#vehicle",function()
    {
        vehicle();
    });
    j(document).on("change","#loading",function()
    {
        loading();
    }); 
    j(document).on("change","#royalty",function()
    {
        royalty();
    });
    
    j(document).on("change","#dummy_royalty",function()
    {
        second_royalty();
    });
    
    j(document).on("click","#add_more_royalty",function()
    {      
        j("#dummy_div").css({"display":"block"});
        j("#royalty_dummy_items").empty();
        j("#dummy_royalty").chosen({allow_single_deselect:true});
        j("#royalty_dummy_items").append(j("#royalty_self1").html());
        j("#add_more_royalty").css({"display":"none"});
    });
    
    j(document).on("click","#remove_royalty", function(){
        //j('#dummy_div').find('input[type="text"]')[0].reset();
        //j("#dummy_div").hide();
        j("#royalty_dummy_items").empty();
        j("#dummy_royalty").val("");
        j("#dummy_div").css({"display":"none"});
        j("#add_more_royalty").css({"display":"block"});
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
    
      j(document).on("blur",".royalty_no", function(){
        var royalty_no = j(this).val();
        
        current_object = j(this);
        j(current_object).parent().parent().parent().find(".royalty_mt").val("");
        if(!isNaN(royalty_no) && royalty_no!= "")
        {
            j.get("index.php?option=com_amittrading&task=get_royalty_mt&tmpl=xml&royalty_no=" + royalty_no, function(data){
                if(data > 0)
                {  
                   j(current_object).parent().parent().parent().find(".royalty_mt").val(data);
                   //j(current_object).parent().parent().parent().find(".royalty_mt").attr('readonly','readonly');
                } 
                else
                {
                    alert(data);
                    j(current_object).parent().parent().parent().find(".royalty_no").val("");
                    j(current_object).parent().parent().parent().find(".royalty_no").focus();
                }         
            }); 
        }   
    });  
    
    //function product_calculate(object)
//    {   
//        var total_amount = 0;
//        var product_mt = parseFloat(j(object).parent().parent().find(".product_mt").val());
//        var product_rate = parseFloat(j(object).parent().parent().find(".product_rate").val());
//        var gst_percent = parseFloat(j(object).parent().parent().find(".gst_percent").val());
//        
//        if(product_mt == "" || isNaN(product_mt))
//        {
//            product_mt = 0;   
//        }
//        if(product_rate == "" || isNaN(product_rate))
//        {
//            product_rate = 0;   
//        }
//        if(gst_percent == "" || isNaN(gst_percent))
//        {
//            gst_percent = 0;   
//        }
//        
//        var product_gross_amount = parseFloat(product_mt * product_rate);
//        j(object).parent().parent().find(".product_gross_amount").val(product_gross_amount); 
//        gst_amount = 0; 
//  
//        if(invoice_type == <? //echo BILL; ?>)
//        { 
//            gst_amount = parseFloat(product_gross_amount * gst_percent /100);
//            j(object).parent().parent().find(".gst_amount").text(gst_amount); 
//            j(object).parent().parent().find(".gst_amount").val(gst_amount);
//        }
//        
//        j(object).parent().parent().find(".product_total_amount").val(product_gross_amount + gst_amount); 
//        
//        calculate_total_amount();       
//    }

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
        
        //var mixing_sum = 0;   
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
        
        //var total_amount = parseFloat(product_sum + mixing_sum);
        
        //j("#product_grand_total").val(product_sum.toFixed(2));
//        j("#product_gross_amt").text(product_sum.toFixed(2));
//        j("#mixing_grand_total").val(mixing_sum.toFixed(2));
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
    
    function royalty()
    {
        if(j("#royalty").val() == 0)
        {
            j("#royalty_items").empty();
        }
        else
        {
            if(j("#royalty").val() == 1)
            {
                royalty_self = j("#royalty_self").html();
                j("#royalty_items").empty();
                j("#royalty_items").append(royalty_self);             
            }
            else
            {
                royalty_purchase = j("#royalty_purchase").html();
                j("#royalty_items").empty();
                j("#royalty_items").append(royalty_purchase);
                j("#royalty_items tr").find(".party_id").chosen();              
            }    
        }
    }
    
    function second_royalty()
    {            
        if(j("#dummy_royalty").val() == 0)
        {
            j("#royalty_dummy_items").empty();
        }
        else
        {
            if(j("#dummy_royalty").val() == 1)
            {
                royalty_self = j("#royalty_self1").html();
                j("#dummy_div").css({"display":"block"});
                j("#royalty_dummy_items").empty();
                j("#add_more_royalty").css({"display":"none"});
                j("#royalty_dummy_items").append(royalty_self); 
                j("#dummy_royalty").chosen({allow_single_deselect:true});            
            }
            else
            {
                royalty_purchase = j("#royalty_purchase1").html();
                j("#dummy_div").css({"display":"block"});
                j("#royalty_dummy_items").empty();
                j("#add_more_royalty").css({"display":"none"});
                j("#royalty_dummy_items").append(royalty_purchase); 
                j("#dummy_royalty").chosen({allow_single_deselect:true}); 
                j("#royalty_dummy_items tr").find(".party_id").chosen();           
            }    
        }
    }
    
    function vehicle()
    {
        var vehicle_mode =  j("#vehicle").find("option:selected").attr("vehicle_mode_id");
        var owner_name =  j("#vehicle").find("option:selected").attr("owner_name");
        var owner_number =  j("#vehicle").find("option:selected").attr("owner_number");
        
        if(j("#vehicle").val() == 0)
        {
            j("#vehicle_items").empty();
        }
        else
        {
            if(vehicle_mode == 1)
            {
                vehicle_self = j("#vehicle_self").html();
                j("#vehicle_items").empty();
                j("#vehicle_items").append(vehicle_self);
            }
            else
            {
                vehicle_rent = j("#vehicle_rent").html();
                j("#vehicle_items").empty();
                j("#vehicle_items").append(vehicle_rent);
                j("#owner_name").val(owner_name);              
                j("#owner_no").val(owner_number);
                j("#transporter_id").chosen({allow_single_deselect:true});              
            }    
        }     
    }
    function loading()
    {
        if(j("#loading").val() == 0)
        {
            j("#loading_items").empty();
        }
        else
        {
            if(j("#loading").val() == 1)
            {
                loading_self = j("#loading_self").html();
                j("#loading_items").empty();
                j("#loading_items").append(loading_self);
                j("#loading_vehicle_type_self").chosen({allow_single_deselect:true});             
            }
            else
            {
                loading_rent = j("#loading_rent").html();
                j("#loading_items").empty();
                j("#loading_items").append(loading_rent);
                j("#loading_transporter_id").chosen({allow_single_deselect:true});
                j("#loading_vehicle_type_rent").chosen({allow_single_deselect:true});              
            }    
        }    
    }
    
    /*j(document).on("change","#vehicle",function()
    {
        vehicle();
        var vehicle_mode =  j("#vehicle").find("option:selected").attr("vehicle_mode_id");
        var owner_name =  j("#vehicle").find("option:selected").attr("owner_name");
        var owner_number =  j("#vehicle").find("option:selected").attr("owner_number");
        
        if(j("#vehicle").val() == 0)
        {
            j("#vehicle_items").empty();
        }
        else
        {
            if(vehicle_mode == 1)
            {
                vehicle_self = j("#vehicle_self").html();
                j("#vehicle_items").empty();
                j("#vehicle_items").append(vehicle_self);
            }
            else
            {
                vehicle_rent = j("#vehicle_rent").html();
                j("#vehicle_items").empty();
                j("#vehicle_items").append(vehicle_rent);
                j("#owner_name").val(owner_name);              
                j("#owner_no").val(owner_number);              
            }    
        } 
    }); */
    
    /*j(document).on("change","#loading",function()
    {
        loading();
        if(j("#loading").val() == 0)
        {
            j("#loading_items").empty();
        }
        else
        {
            if(j("#loading").val() == 1)
            {
                loading_self = j("#loading_self").html();
                j("#loading_items").empty();
                j("#loading_items").append(loading_self);             
            }
            else
            {
                loading_rent = j("#loading_rent").html();
                j("#loading_items").empty();
                j("#loading_items").append(loading_rent);              
            }    
        }  
    }); */
    
   /* j(document).on("change","#royalty",function()
    {
        if(j("#royalty").val() == 0)
        {
            j("#royalty_items").empty();
        }
        else
        {
            if(j("#royalty").val() == 1)
            {
                royalty_self = j("#royalty_self").html();
                j("#royalty_items").empty();
                j("#royalty_items").append(royalty_self);             
            }
            else
            {
                royalty_purchase = j("#royalty_purchase").html();
                j("#royalty_items").empty();
                j("#royalty_items").append(royalty_purchase);              
            }    
        }  
        royalty();                        
         j("#party_name").autocomplete({
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
        });
        
    }); */
    
    //////////////////
    
    function get_truck_weight()
    {
        var loaded_weight = parseFloat(j("#loaded_weight").val()).toFixed(2);
        var empty_weight = parseFloat(j("#empty_weight").val()).toFixed(2);
        var net_weight = parseFloat(loaded_weight - empty_weight).toFixed(2);
        if (!isNaN(net_weight))
        {
           j("#net_weight").val(net_weight);
        }
    }
    function get_liter_amount()
    {
        var liter = parseFloat(j("#diesel_liter").val()).toFixed(2);
        var diesel_rate = parseFloat(j("#diesel_rate").val()).toFixed(2);
        var diesel_amount = parseFloat(liter * diesel_rate).toFixed(2);
        if (!isNaN(diesel_amount))
        {
           j("#diesel_amt").text(diesel_amount);
           j("#diesel_total_amount").val(diesel_amount);
        }
        
    }
   
    function show_customer_details()
    {
        j("#address").html(j("#customer_id").find("option:selected").attr("address"));
        j("#contact_no").html(j("#customer_id").find("option:selected").attr("contact_no"));
        j("#other_contact_nos").html(j("#customer_id").find("option:selected").attr("other_contact_nos"));
    } 
    
    /*j(document).on("click" ,".delete", function(){
        if(j("#items tr").length == 1)
        {
            alert("Sales must have at least 1 item.");
            return false;
        }
        j(this).closest("tr").remove(); 
        
        index = 1;
        j(".first_item_index").each(function(){
            j(this).html(index-1);
        });
    }); */
    
    function add_more_items(row)
    {
        if(row == 1)
        {
            j("#items").append(j("#dummy_item").html());
            j("#items tr").find(".product_items").chosen();
        }
        else
        {
            j("#second_items").append(j("#second_dummy_items").html());
            j("#second_items tr").find(".mixing_items").chosen();
        }
    }
    
     j(document).on("keypress",".check_num,.royalty_no",function(e){
        if(!(e.which>=48 && e.which<=57 ))
        {
            if(!((e.which == 0) || (e.which==8)))
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
    
     j(document).on("keydown","#royalty_name,#driver_name,#party_name,.mixing_note,.product_note",function (e) {
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
    });
    function validateForm()
    {
        if(j("#customer_id").val() == 0)
        {
            alert("Select customer name."); return false;
        }
        if(j("#bill_date").val() == "")
        {
            alert("Select bill date."); return false;
        }
        
        if(j("#bill_type").val() == 0)
        {
            alert("Select bill type."); return false;
        }
        if(j("#royalty_id").val() == 0)
        {
            alert("Select royalty name."); 
            return false;
        }
        
       
        if(j("#vehicle").val() == 0)
        {
            alert("Select vehicle."); return false;
        }
        else
        {
            if(j("#vehicle").val() == 1)
            {
                if(j("#starting_km").val() == "")
                {
                    alert("Fill starting KM."); return false;    
                } 
                if(j("#vehicle_rate").val() == "")
                {
                    alert("Fill vehicle rate."); return false;    
                }   
            }
            else
            {
                if(j("#vehicle").val() == 2)
                {
                    if(j("#transporter_id").val() == 0)
                    {
                        alert("Select transporter name"); return false;    
                    }
                    if(j("#driver_no").val() == "")
                    {
                        alert("Fill driver number"); 
                        return false;    
                    }
                    else 
                    {
                        if(j("#driver_no").val().length != 10)
                        {
                            alert("Contact number should have 10 Digits");
                            return false;    
                        }
                    }
                    if(j("#vehicle_rate").val() == "")
                    {
                        alert("Fill vehicle rate."); return false;    
                    }       
                }    
            }
        }
        var main_royalty_no = j("#royalty_items").find("tr").find(".royalty_no").val(); 
        var dummy_royalty_no = j("#royalty_dummy_items").find("tr").find(".royalty_no").val(); 
        if(main_royalty_no == dummy_royalty_no)
        {
            alert("Same royalty number not allowed.");return false;    
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
        //var error_in = "";
//        j("#second_items tr").each(function (){
//            if(j(this).find(".mixing_items").val() == 0)
//            {
//                error_in = "mixing_items";
//                return false;
//            }
//            if((j(this).find(".mixing_mt").val() == "" || j(this).find(".mixing_mt").val() == 0) && (j(this).find(".mixing_rate").val() == "" || j(this).find(".mixing_rate").val() == 0))
//            {
//                error_in = "mixing_mt_rate";
//                return false;
//            }
//        });
    
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
        
        var total_weight = j("#total_weight").val();
        var billed_quantity = <? echo floatval(@$this->billed_quantity);?>;
        var quantity = <? echo floatval(@$this->quantity);?>;
        if(quantity > 0){
            //if(parseFloat(total_weight) + parseFloat(billed_quantity) > parseFloat(quantity))
            if(parseFloat(total_weight) + parseFloat(billed_quantity) > parseFloat(quantity))
            {
                alert("Quantity should not be greater than ordered quantity.");
                return false;
            }  
        }
    
        j("#submit_btn").attr("disabled", "disabled");
        go("index.php?option=com_amittrading&view=sales_invoice_history");
        j("#sales_invoice").submit();
    }
    
</script>

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
<h1>Edit Sales Invoice</h1>
<form id="sales_invoice" action="index.php?option=com_amittrading&task=update_sales_invoice" method="post" target="_blank">
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
                                        <option value="<? echo $customer->id; ?>" address="<? echo $customer->customer_address; ?>" other_contact_nos="<? echo $customer->other_contact_numbers;?>" contact_no="<? echo $customer->contact_no; ?>" <? echo ($this->sales->customer_id == $customer->id ? "selected='selected'" : ""); ?> ><? echo $customer->customer_name; ?></option>
                                    <?
                                }
                            }
                        ?>
                    </select>
                </td>    
            </tr>
            
            <tr>
                <td >Address : </td>
                <td id="address" class="custom"><? echo $this->sales->customer_address;?></td>
            </tr>
            <tr>
                <td >Contact : </td>
                <td id="contact_no" class="custom check_num"><? echo $this->sales->contact_no;?></td>
            </tr>
            <tr>
                <td>Other Contact Numbers : </td>
                <td id="other_contact_nos"><? echo $this->sales->other_contact_numbers;?></td>
            </tr>
            <tr>
                <td > Date : </td>
                <td><input type="text" id="bill_date" name="bill_date" value="<? echo date("d-M-Y", strtotime($this->sales->date)); ?>" class="custom" readonly></td>
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
                        <option value="<? echo BILL; ?>" <? echo ($this->sales->bill_type == BILL ? "selected='selected'" : ""); ?>>Bill</option>
                        <option value="<? echo CHALLAN; ?>" <? echo ($this->sales->bill_type == CHALLAN ? "selected='selected'" : ""); ?>>Challan</option>
                    </select>
                </td>
            </tr>
            <!--<tr>
                <td>Bill / Challan No :</td>
                <td><input type="text" name="challan_no" id="challan_no" class="custom check_num" value="<? //echo $this->sales->bill_challan_no;?>"></td>
            </tr>-->
            <tr>
                <td>Royalty Name : </td>
                <td> 
                    <select id="royalty_id" name="royalty_id" tabindex="3" class="custom">
                        <option value="0"></option>
                        <?
                            if(count($this->royalty_list) > 0)
                            {
                                foreach($this->royalty_list as $royalty)
                                {
                                    ?>
                                        <option value="<? echo $royalty->id; ?>" <? echo ($this->sales->royalty_id == $royalty->id ? " selected='selected' " : "");?> ><? echo $royalty->royalty_name; ?></option>
                                    <?
                                }
                            }
                        ?>
                    </select>
                </td>    
            </tr>
        </table>
    </div>
    <div style="float:left;margin-right:10px;margin-left:10px;">
        <table class="clean centreheadings">
            <tr><th colspan="2" style="text-align:left;">Vehicle</th></tr>
            <tr>
                <td>Vehicle</td>
                <td>
                    <select name="vehicle_id" id="vehicle" class="custom">
                        <option value="0"></option>
                        <?
                            if(count($this->vehicles) > 0)
                            {
                                foreach($this->vehicles as $vehicle)
                                {
                                    ?>
                                        <option value="<? echo $vehicle->id ;?>" vehicle_mode_id="<? echo $vehicle->self_rent_id; ?>" owner_name="<? echo $vehicle->transporter_name; ?>" owner_number="<? echo $vehicle->owner_number;?>" <? echo ($this->sales->vehicle_id == $vehicle->id ? "selected='selected'" : ""); ?> ><? echo $vehicle->vehicle_number?></option>
                                    <?
                                }
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tbody id="vehicle_items">
            </tbody>
        </table>
    </div>
    <div style="float:left;margin-right:10px;">
        <table class="clean centreheadings">
            <tr>
                <td>Loaded Truck Weight :</td>
                <td align="center"><input type="text" name="loaded_weight" id="loaded_weight" class="point_amount" style="width:100px;" value="<? echo $this->sales->loaded_weight; ?>"><td>        
            </tr>
            <tr>
                <td>Empty Truck Weight :</td>
                <td align="center"><input type="text" name="empty_weight" id="empty_weight" class="point_amount" style="width:100px;" value="<? echo $this->sales->empty_weight;?>"><td> 
            </tr>
            <tr>
                <td>Net Weight :</td>
                <td align="center"><input type="text" name="net_weight" id="net_weight" class="" style="width:100px;" value="<? echo $this->sales->net_weight;?>"><td>
            </tr>
            <tr><th colspan="2" style="text-align:left;">Loading</th></tr>
            <tr>
                <td>Loading :</td>
                <td>
                    <select name="loading" id="loading" class="" style="width:100px;">
                        <option></option>
                        <option value="<? echo SELF; ?>" <? echo ($this->sales->loading_type == SELF ? "selected='selected'" : ""); ?>>Self</option>
                        <option value="<? echo RENT; ?>" <? echo ($this->sales->loading_type == RENT ? "selected='selected'" : ""); ?>>Rent</option>
                    </select>
                </td>
            <tr>
            <tbody id="loading_items">
            </tbody>
            <tr>
                <td>Waiverage Charge :</td>
                <td><input type="text" id="waiverage_charges" name="waiverage_charges" class="" style="width:100px;" value="<? echo $this->sales->waiverage_charges;?>"></td>
            </tr>
            <tr>
                <td >Remarks :</td>
                <td><input type="text" id="remarks" name="remarks" class="" style="width:100px;" value="<? echo $this->sales->remarks;?>"></td>
            </tr>
        </table>
    </div>
    <div style="float:left;">
        <table class="clean centreheadings">
            <tr><th colspan="2" style="text-align:left;width:100px;">Royalty</th></tr>
            <tr>
                <td>Royalty :</td>
                <td>
                    <select name="royalty[]" id="royalty" style="width:100px;">
                        <option></option>
                        <option value="<? echo SELF; ?>" <? echo ($this->sales->royalty_type == SELF ? "selected='selected'" : ""); ?>>Self</option>
                        <option value="<? echo PURCHASE; ?>" <? echo ($this->sales->royalty_type == PURCHASE ? "selected='selected'" : ""); ?>>Purchase</option>
                    </select>
                </td>
            </tr>
            <tbody id="royalty_items">
            </tbody>
            <tfoot id="add_more_tb_button">
                <tr>
                    <td colspan="2" align="right"><input type="button" id="add_more_royalty" value="Add New" ></td>
                </tr>
            </tfoot>
        </table> 
        <br />
        <table class="clean centreheadings" style="display: none;" id="dummy_div" >
            <tr><th colspan="2" style="text-align:left;width:100px;">Royalty</th></tr>
            <tr>
                <td>Royalty :</td>
                <td>
                    <select name="royalty[]" id="dummy_royalty" style="width:100px;">
                        <option></option>
                        <option value="<? echo SELF; ?>" <? echo ($this->sales->royalty_type1 == SELF ? "selected='selected'" : ""); ?>>Self</option>
                        <option value="<? echo PURCHASE; ?>" <? echo ($this->sales->royalty_type1 == PURCHASE ? "selected='selected'" : ""); ?>>Purchase</option>
                    </select>
                </td>
            </tr>
            <tbody id="royalty_dummy_items">
            </tbody>
            <tfoot id="remove_button">
                <tr>
                    <td colspan="2" align="right"><input type="button" id="remove_royalty" value="Remove" ></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div style="float:left;;margin-left:10px;">
        <table class="clean centreheadings">
            <tr>
                <td>Gross Amount : </td>
                <td><input type="text" name="gross_amount" id="gross_amount" value="<? echo $this->sales->gross_amount; ?>" readonly="readonly" style="width:70px;" /></td>
            </tr>
            <tr>
                <td>Gst Amount : </td>
                <td><input type="text" name="total_gst_amount" id="total_gst_amount" value="<? echo $this->sales->gst_amount; ?>" readonly="readonly" style="width:70px;"  /></td>
            </tr>
            <tr>
                <td>Total Amount : </td>
                <td>
                    <input type="text" name="total_amount" id="total_amount" value="<? echo $this->sales->total_amount -($this->sales->loading_amount+$this->sales->waiverage_charges); ?>" readonly="readonly" style="width:70px;"  />
                </td>
            </tr>
            <tr>
                <td>Total Weight : </td>
                <td><input type="text" name="total_weight" id="total_weight" value="<? echo $this->sales->total_weight; ?>" readonly="readonly" style="width:70px;" /></td>
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
                    <!--<td><button onclick="add_more_items(1); return false;"><u>A</u>dd More</button></td>-->
                </tr>
            </thead>
            <tbody id="items">
                <?
                    if(count($this->sales_product_items) > 0)
                    {
                        foreach($this->sales_product_items as $item)
                        {
                            
                                ?> 
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
                                                        ?><option value="<? echo $product->id; ?>" gst_percent="<? echo $product->gst_percent;?>" <? echo ($item->product_id == $product->id ? "selected='selected'" : "");?>><? echo $product->product_name; ?></option><?
                                                    }
                                                }
                                            ?>
                                            </select>
                                            <!--<input type="hidden" name="product_items_id[]" value="<? //echo $product->id; ?>" > -->
                                            <!--<input type="hidden" name="product_item_type" id="product_item_type" value="<? //echo PRODUCT ; ?>">   -->
                                        </td>
                                        <td><input type="text" name="product_mt[]" size="6%" class="product_mt" value="<? echo $item->actual_weight; ?>"></td>
                                        <td><input type="text" name="product_rate[]" size="6%" class="product_rate" style="text-align:right;" value="<? echo $item->product_rate; ?>"></td>
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
                                        <td><input type="text" name="product_total_amount[]" class="product_total_amount" readonly="readonly" style="text-align:right;" value="<? echo $item->actual_weight * $item->product_rate;?>"></td>
                                        <td><input type="text" name="product_note[]"  class="product_note add_items" value="<? echo $item->product_note; ?>"></td>
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
                    <!--<td id="product_gross_amt" align="right"><? //echo floatval($this->sales->main_items_total_amount); ?></td>-->
                    <td id="product_gross_amt" align="right"><? echo floatval($this->sales->gross_amount); ?></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td id="add_more_action" colspan="7"><button onclick="add_more_items(1); return false;"><u>A</u>dd More</button></td>
                </tr>       
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
                    <!--<td><button onclick="add_more_items(2); return false;"><u>A</u>dd More</button></td>-->
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
                                        <select name="mixing_items_id[]" class="mixing_items" id="mixing_items" style="width:200px;">
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
                                    <td><input type="text" name="mixing_mt[]" id="mixing_mt" size="6%" class="mixing_mt" value="<? echo $item->quantity;?>" ></td>
                                    <!--<td><input type="text" name="mixing_rate[]" size="6%" class="mixing_rate" style="text-align:right;" value="<? //echo $item->product_rate;?>"></td>-->
                                    <!--<td><input type="text" name="mixing_total_amount[]" class="mixing_total_amount" readonly="readonly" style="text-align:right;" value="<? //echo $item->total_amount;?>"></td>-->
                                    <td><input type="text" name="mixing_note[]"  class="mixing_note add_items" value="<? echo $item->product_note;?>"></td>
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
                <tr>
                    <td id="add_more_action" colspan="5"><button onclick="add_more_items(2); return false;"><u>A</u>dd More</button></td>
                </tr>       
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
                    <input type="button" value="Submit (Alt + Z)" id="submit_btn" onclick="validateForm();">
                    <input type="button" value="Cancel" onclick="history.go('-1');">
                </td>
            </tr>
        </table>
        </div>
    <input type="hidden" name="sales_id" value=" <? echo intval($this->sales_id); ?>">
    <input type="hidden" name="order_id_si" id="order_id_si" value="<? echo $this->order_id;?>">        
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

<table style="display:none">
    <tbody id="vehicle_self">
        <tr>
            <td>Starting KM :</td>
            <td><input type="text" name="starting_km" id="starting_km" class="" value="<? echo $this->sales->starting_km;?>"></td>
        </tr>
        <tr>
            <td>Rate :</td>
            <td><input type="text" name="vehicle_rate" id="vehicle_rate" class="" value="<? echo $this->sales->vehicle_rate;?>"></td>
        </tr>
    <tbody>
</table>

<table style="display:none">
    <tbody id="vehicle_rent">
        <tr>
            <td>Transporter Name :</td>
            <td>
                <select name="transporter_id" id="transporter_id" class="custom">
                    <option></option>
                    <?
                        if(count($this->transporters) > 0)
                        {
                            foreach($this->transporters as $transporter)
                            {
                                ?>
                                    <option value="<? echo $transporter->id; ?>" <? echo ($this->sales->transporter_id == $transporter->id ? "selected='selected'" : ""); ?>><? echo $transporter->transporter_name; ?></option>
                                <?
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr> <?//echo $this->sales->transporter_name;?>
            <td>Owner Name :</td>
            <td><input type="text" name="owner_name" id="owner_name" class="custom" value="<? echo $this->sales->transporter_name;?>"></td>
        </tr>
        <tr>
            <td>Owner Mobile No :</td>
            <td><input type="text" name="owner_no" id="owner_no" class="custom" value="<? echo $this->sales->owner_number;?>"></td>
        </tr>
        <tr>
            <td>Driver Name :</td>
            <td><input type="text" name="driver_name" id="driver_name" class="custom" value="<? echo $this->sales->driver_name;?>"></td>
        </tr>
        <tr>
            <td>Driver Mobile No :</td>
            <td><input type="text" name="driver_no" id="driver_no" class="custom check_num" value="<? echo $this->sales->driver_no;?>"></td>
        </tr>
        <!--<tr>
            <td>Driver License :</td>
            <td><input type="text" name="driver_license_no" id="driver_license_no" class="custom" value="<? //echo $this->sales->driver_license_no;?>"></td>
        </tr> -->
        <tr>
            <td>Rate :</td>
            <td><input type="text" name="vehicle_rate" id="vehicle_rate" class="custom" value="<? echo $this->sales->vehicle_rate_per_mt;?>"></td>
        </tr>
        <tr>
            <td>Add Cash :</td>
            <td><input type="text" name="add_cash" id="add_cash" class="custom" value="<? echo $this->sales->add_cash?>"></td>
        </tr>
        <tr>
            <th style="text-align:left;" colspan="2">Diesel</th>
        </tr>
         <tr >
            <td>Supplier Name: </td>
            <td>
                <select id="supplier_id" name="supplier_id" class="custom">
                    <option></option>
                    <? 
                    foreach($this->suppliers as $supplier)
                        { 
                    ?>
                            <option value="<? echo $supplier->id;?>" <? echo $this->sales->diesel_supplier_id == $supplier->id ? "selected=selected" : "";?>><? echo $supplier->supplier_name;?></option>   
                    <?  
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Liter :</td>
            <td><input type="text" name="liter" id="diesel_liter" class="custom" value="<? echo $this->sales->liter;?>"></td>
        </tr>
        <tr>
            <td>Rate :</td>
            <td><input type="text" name="diesel_rate" id="diesel_rate" class="custom" value="<? echo $this->sales->diesel_rate;?>"></td>
        </tr>
        <tr>
            <td>Total Amount :
                <input type="hidden" name="diesel_total_amount" id="diesel_total_amount" value="<? echo $this->sales->diesel_total_amount;?>">
            </td>
            <td id="diesel_amt" class="custom"><? echo $this->sales->diesel_total_amount;?></td>
        </tr>
    <tbody>
</table>

<table style="display:none">
    <tbody id="loading_self">
        <tr>
            <td>Vehicle Type :</td>
            <td>                                    
                <select name="loading_vehicle_type" class="loading_vehicle_type" id="loading_vehicle_type_self"  style="width:100px;" >
                    <option></option>
                    <?
                        if(count($this->vehicles_type) > 0)
                        {
                            foreach($this->vehicles_type as $vehicle_type)
                            {
                                ?><option value="<? echo $vehicle_type->id; ?>" <? echo ($this->sales->loading_vehicle_type == $vehicle_type->id ? "selected='selected'" : ""); ?>><? echo $vehicle_type->vehicle_type; ?></option><?
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Amount :</td>
            <td><input type="text" name="loading_amount" class="loading_vehicle_amount point_amount" style="width:100px;" value="<? echo $this->sales->loading_amount;?>"></td>
        </tr>
    <tbody>
</table>

<table style="display:none">
    <tbody id="loading_rent">
        <tr>
            <td>Name :</td>
            <td>                                                                           
                <select name="loading_transporter_id" id="loading_transporter_id" class="" style="width:100px;">
                    <option></option>
                    <?
                        if(count($this->transporters) > 0)
                        {
                            foreach($this->transporters as $transporter)
                            {
                                ?>
                                    <option value="<? echo $transporter->id; ?>" <? echo ($this->sales->loading_transporter_id == $transporter->id ? "selected='selected'" : ""); ?>><? echo $transporter->transporter_name; ?></option>
                                <?
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Vehicle Type :</td>
            <td>                                    
                <select name="loading_vehicle_type" class="loading_vehicle_type" id="loading_vehicle_type_rent" style="width:100px;" >
                    <option></option>
                    <?
                        if(count($this->vehicles_type) > 0)
                        {
                            foreach($this->vehicles_type as $vehicle_type)
                            {
                                ?><option value="<? echo $vehicle_type->id; ?>" <? echo ($this->sales->loading_vehicle_type == $vehicle_type->id ? "selected='selected'" : ""); ?>><? echo $vehicle_type->vehicle_type; ?></option><?
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Amount :</td>
            <td><input type="text" name="loading_amount" class="loading_vehicle_amount point_amount" style="width:100px;" value="<? echo $this->sales->loading_amount;?>"></td>
        </tr>
    <tbody>
</table>

<table style="display:none">
    <tbody id="royalty_self">
        <tr>
            <td>Royalty No :</td>
            <td><input type="text" name="royalty_no[0]" class="royalty_no" value="<? echo $this->sales->royalty_no;?>" style="width:100px;"> </td>
        </tr>
        <tr>
            <td>Royalty MT :</td>
            <td><input type="text" name="royalty_mt[0]" class="royalty_mt point_amount"  value="<? echo $this->sales->royalty_mt;?>" style="width:100px;"></td>
            
        </tr>
        <tr>
            <td>Rate :</td>
            <td><input type="text" name="royalty_rate[0]" class="royalty_rate point_amount" value="<? echo $this->sales->royalty_rate;?>" style="width:100px;"> </td>
        </tr>
    <tbody>
</table>

<table style="display:none">
    <tbody id="royalty_purchase">
        <tr>
            <td>Party Name</td>
            <td>
                <select name="party_id[0]" id="party_id" class="party_id" style="width:100px;">
                    <option value="0"></option>
                    <?
                        if(count($this->suppliers)>0)
                        {
                            foreach($this->suppliers as $supplier)
                            {
                                ?>
                                    <option value="<? echo $supplier->id; ?>" <? echo ($this->sales->party_id == $supplier->id ? "selected='selected'" : ""); ?>><? echo $supplier->supplier_name; ?></option>
                                <?
                            }
                        }    
                    ?> 
                </select>
            </td>
        </tr>
        <tr>
            <td>Royalty No :</td>
            <td><input type="text" name="royalty_no[0]" class="royalty_no" value="<? echo $this->sales->royalty_no;?>" style="width:100px;" > </td>
        </tr>
        <tr>
            <td>Royalty MT :</td>
            <td><input type="text" name="royalty_mt[0]" class="royalty_mt point_amount" value="<? echo $this->sales->royalty_mt;?>" style="width:100px;" ></td>
            
        </tr>
        
        <tr>
            <td>Rate :</td>
            <td><input type="text" name="royalty_rate[0]" class="royalty_rate point_amount" value="<? echo $this->sales->royalty_rate;?>" style="width:100px;"></td>
        </tr>
    <tbody>
</table>

<table style="display:none">
    <tbody id="royalty_self1">
        
        <tr>
            <td>Royalty No :</td>
            <td><input type="text" name="royalty_no[1]" class="royalty_no" value="<? echo $this->sales->royalty_no1;?>" style="width:100px;"> </td>
        </tr>
        <tr>
            <td>Royalty MT :</td>
            <td><input type="text" name="royalty_mt[1]" class="royalty_mt point_amount"  value="<? echo $this->sales->royalty_mt1;?>" style="width:100px;"></td>
            
        </tr>
        <tr>
            <td>Rate :</td>
            <td><input type="text" name="royalty_rate[1]" class="royalty_rate point_amount" value="<? echo $this->sales->royalty_rate1;?>" style="width:100px;"> </td>
        </tr>
    <tbody>
</table>

<table style="display:none">
    <tbody id="royalty_purchase1">
        
        <tr>
            <td>Party Name</td>
            <td>
                <select name="party_id[1]" id="party_id" class="party_id" style="width:100px;">
                    <option value="0"></option>
                    <?
                        if(count($this->suppliers)>0)
                        {
                            foreach($this->suppliers as $supplier)
                            {
                                ?>
                                    <option value="<? echo $supplier->id; ?>" <? echo ($this->sales->party_id1 == $supplier->id ? "selected='selected'" : ""); ?>><? echo $supplier->supplier_name; ?></option>
                                <?
                            }
                        }    
                    ?> 
                </select>
            </td>
        </tr>
        <tr>
            <td>Royalty No :</td>
            <td><input type="text" name="royalty_no[1]" class="royalty_no" value="<? echo $this->sales->royalty_no1;?>" style="width:100px;" > </td>
        </tr>
        <tr>
            <td>Royalty MT :</td>
            <td><input type="text" name="royalty_mt[1]" class="royalty_mt point_amount" value="<? echo $this->sales->royalty_mt1;?>" style="width:100px;" ></td>
            
        </tr>
        
        <tr>
            <td>Rate :</td>
            <td><input type="text" name="royalty_rate[1]" class="royalty_rate point_amount" value="<? echo $this->sales->royalty_rate1;?>" style="width:100px;"></td>
        </tr>
    <tbody>
</table>