<?php
    defined('_JEXEC') or die('Restricted access');
?>
<style>
    input[type='text'], select, textarea{
        width: 300px;
    }
    
    textarea{
        resize: none;
    }
</style>
<script>
    j(function(){
        j("#doj").datepicker({"dateFormat" : "dd-M-yy", "changeMonth": true, "changeYear": true, "yearRange":"1940:<? echo date("Y") + 50;?>"});
    
    
    j(document).on("keypress","#gross_salary" ,function(e){
        prevent_char(e.which,e);
    });
    
    j(document).on("keypress","#mobile_no, #machine_enrollment_no" ,function(e){
        strict_numbers(e.which,e);
    });  
    
    //j("#product_name").change(function(){
//         alert("hello");
//    });  

j("#product_name").on("change",function(){
    j.get("index.php?option=com_master&task=fetch_unit_gst&tmpl=xml&product_name=" + j("#product_name").val(), function(data){
            var product_details = j.parseJSON(data);  
            if(j("#product_name").val() == "")
            {
               j(".unit_name").text("");
               j(".gst_percent").text(""); 
               j("#unit_name").val("");
               j("#gst_percent").val(""); 
            }
            j(".unit_name").text(product_details.unit);
            j(".gst_percent").text(product_details.gst_percent);
            j("#unit_name").val(product_details.unit);
            j("#gst_percent").val(product_details.gst_percent);
            j("#unit_id").val(product_details.id);
            
            calculate();
            calculate_total_amount();
            
        });
    
});

j("#quantity").on("keyup",function(){
   calculate();
    calculate_total_amount(); 
});
j("#rate").on("keyup",function(){
   calculate();
    calculate_total_amount(); 
});

j("input").bind("change","#gst_percent",function(){
    calculate_total_amount(); 
});

j("input").bind("change","#gross_amount",function(){
    calculate_total_amount(); 
});


}); 
                
    function validateForm()
    {  
        if(j("#doj").val() == "")
        {
            alert("Select date of joining.");
            return false;
        } 
        else if(j("#supplier_name").val() == 0)
        {
            alert("Please select supplier name.");
            return false;
        }
        else if(j("#supplier_cn").val() == "")
        {
            alert("Please enter supplier challan no.");
            return false;
        }
        else if(j("#challan_no").val() == "")
        {
            alert("Please enter challan no.");
            return false;
        }
        else if(j("#product_name").val() == 0)
        {
            alert("Please select product name.");
            return false;
        }
        else if(j("#quantity").val() == 0)
        {
            alert("Please enter valid quantity.");
            return false;
        }
        else if(j("#rate").val() == 0)
        {
            alert("Please enter valid rate.");
            return false;
        }
        else{
            go("index.php?option=com_master&task=purchase_registration&tmpl=xml&" + j("#registration_form").serialize());
        }
      
                // j.get("index.php?option=com_master&task=save_unit&tmpl=xml&" + j("#unitForm").serialize(), function(data){ 

        //j.get("index.php?option=com_hr&task=check_attendance_machine_duplicity&tmpl=xml&machine_id=" + j("#machine_id").val() + "&machine_enrollment_no=" + j("#machine_enrollment_no").val(), function(count){
//            if(count > 0)
//            {
//                alert("Employee with same machine enrollment no. for the selected machine already exists!");
//                return false;
//            }
//            else
//            {
//                j("#submit_button").attr("disabled", true);
//                j("#registration_form").submit();
//            }
//        });
    }
    
    function calculate()
    {
        var total_amount = 0;
        var qty = j("#quantity").val();
        var rate = j("#rate").val();   
        qty = (isNaN(qty) ? 0 : qty);
        rate = (isNaN(rate) ? 0 : rate);
        var total = (rate >= 1 && qty >= 1 ? parseFloat(rate * qty) : 0);  
               j(".gross_amount").text(total.toFixed(2));
               j("#gross_amount").val(total.toFixed(2));  
            calculate_total_amount()
    }
             
    function calculate_total_amount()
    {
        var gst_percent =  parseInt(j("#gst_percent").val());
        var gross_amount = parseInt(j("#gross_amount").val());
        var gst_amount = parseFloat(gross_amount*gst_percent/100);
        
        
       // alert(parseFloat(parseFloat(gross_amount)*parseFloat(gst_percent)/100 + gross_amount));
        gst_amount = (isNaN(gst_amount) ? 0 : gst_amount);
        gross_amount = (isNaN(gross_amount) ? 0 : gross_amount);
        var total_amount =  parseFloat(gst_amount+gross_amount);
        j(".gst_amount").text(parseFloat(gst_amount.toFixed(2)));
        j("#gst_amount").val(parseFloat(gst_amount.toFixed(2)));    
        j(".total_amount").text(parseFloat(total_amount.toFixed(2)));
        j("#total_amount").val(parseFloat(total_amount.toFixed(2)));    
    } 
    
    // function calculate(object)
//             {
//                var total_amount = 0;
//                var total_quantity = 0;
//                var qty = parseFloat($(object).parent().parent().find(".quantity").val());
//                var rate = parseFloat($(object).parent().parent().find(".rate").val());   
//                qty = (isNaN(qty) ? 0 : qty);
//                rate = (isNaN(rate) ? 0 : rate);
//                var total = (rate >= 1 && qty >= 1 ? parseFloat(rate * qty) : 0);  
//                    object.parent().parent().find(".total").text(total.toFixed(2));
//                    object.parent().parent().find(".total").val(total.toFixed(2));  
//                    object.parent().parent().find(".quantity").val(qty); 
//                    object.parent().parent().find(".quantity").text(qty); 
//                grandTotal();
//                grandQuant();
//             }
//             function grandTotal()
//             {
//                var totalamount = 0
//                $(".total").each(function (){
//                    total_amount =  parseFloat($(this).val());
                    //alert(total_amount);
//                    if(!isNaN(total_amount))
//                    {
//                        totalamount += total_amount;
//                    }
//                });
//                $(".grandcalc").val(totalamount.toFixed(2));
//             }      
//       
       
</script>
<h1>Purchase Entry</h1>
<form id="registration_form" method="post">
    <table class="clean">
        <tr>
            <td>Date of Joining</td>
            <td><input type="text" name="doj" id="doj"></td>
        </tr>
        <tr>
            <td>Supplier Name</td>     
            <td>
                <select id="supplier_name" name="supplier_name">
                <option></option>
                <?
                  foreach($this->suppliers as $supplier)
                  {
                      echo "<option value='".$supplier->id."'>".$supplier->supplier_name."</option>";
                  }
                ?>
                </select>
            
            
            <!--<input type="text" name="employee_name" id="employee_name">--></td>
        </tr>
        <tr>
            <td>Supplier Challan No</td>
            <td><input type="text" name="supplier_cn" id="supplier_cn"></td>
        </tr>
        <tr>
            <td>Challan No</td>
            <td><input type="text" name="challan_no" id="challan_no"></td>
        </tr>
        <tr>
            <td>Vehicle No</td>
            <td><input type="text" name="vehicle_no" id="vehicle_no"></td>
        </tr>
        <tr>
         <tr>
            <td>Product</td>  
            <td> 
            <select id="product_name" name="product_name">
                <option></option>
                <?
                  foreach($this->products as $product)
                  {
                      echo "<option value='".$product->id."'>".$product->product_name."</option>";
                  }
                ?>
                </select>
            
             </td> 
            <!--<input type="text" name="employee_name" id="employee_name">-->
         </tr>
         <tr>
            <td>Unit</td>     
            <td><span class="unit_name"></span></td>
         </tr>
         <tr>
            <td>Quantity</td>     
            <td><input type="text" name="quantity" id="quantity"></td>
         </tr>
         <tr>
            <td>Rate</td>     
            <td><input type="text" name="rate" id="rate"></td>
         </tr>
         <tr>
            <td>Gross Amount</td>     
            <td><span class="gross_amount"></span></td>
         </tr>
         <tr>
            <td>GST Percent</td>     
            <td><span class="gst_percent"></span></td>
         </tr>
         <tr>
            <td>GST Amount</td>     
            <td><span class="gst_amount"></span></td>
         </tr>
         <tr>
            <td>Total Amount</td>     
            <td><span class="total_amount"></span></td>
         </tr>
            <td>payable Amount</td>     
            <td><input type="text" name="payable_amount" id="payable_amount"></td>
         </tr>
         
         <tr>
            <td>Loading Charges</td>     
            <td><input type="text" name="loading_charges" id="loading_charges"></td>
         </tr>
         <tr>
            <td>Royalty</td>     
            <td><input type="text" name="royalty" id="royalty"></td>
         </tr>
         <tr>
            <td>Waiverage Charges</td>     
            <td><input type="text" name="waiverage_charges" id="waiverage_charges"></td>
         </tr>
         <tr>
            <td>Remarks</td>     
            <td><input type="text" name="remarks" id="remarks"></td>
         </tr>
    </table>
    <br />
    <input type="hidden" name="unit_id" id="unit_id">
    <input type="hidden" name="gst_percent" id="gst_percent" >
    <input type="hidden" name="gross_amount" id="gross_amount">
    <input type="hidden" name="gst_amount" id="gst_amount">     
    <input type="hidden" name="unit_name" id="unit_name">
   <input type="hidden" name="total_amount" id="total_amount" >
    <input type="button" value="Submit (Alt + Z)" id="submit_button" onclick="validateForm();">
    <input type="button" value="Cancel" onclick="history.go(-1);">
</form>