<?php // no direct access
    defined('_JEXEC') or die('Restricted access'); 
?>
<style>
    input[type='text'], select{
        width: 300px;
    }
</style>
<script>
    j(function(){
        /*j("#fy_year").datepicker({"dateFormat" : "dd-M-yy"});
        show_fy_year();*/
        
        j("#users_allowed_backdate_payments").chosen();
    });
    
    j(document).on("keypress","#opening_cash_in_hand",function(e){
        prevent_char(e.which,e);
    });
    
    j(document).on("keypress","#credit_days",function(e){
        strict_numbers(e.which,e);
    });
    
    /*function show_fy_year()
    {
        financial_year = "";
        
        fy_year = j("#fy_year").val();
        if(fy_year != "")
        {
            fy_year = j('#fy_year').datepicker('getDate');
            month = fy_year.getMonth() + 1;
            year = fy_year.getFullYear().toString().substr(2,2);
            
            y = parseInt(year) + 1;
            financial_year = (month < 4 ? parseInt(year) - 1 + "-" + year : year + "-" + y);
        }
        
        j("#financial_year").html(financial_year);
    }*/

    function validateForm()
    {
        /*if(j("#fy_year").val() == "")
        {
            alert("Select financial year.");
            return false;
        }*/
        if(j("#credit_days").val() == "")
        {
            alert("Please enter credit days.");
            return false;
        }
        
        if(j("#default_location_id").val() == 0)
        {
            alert("Please select default location.");
            return false;
        }
        
        if(j("#cash_sale_customer_id").val() == 0)
        {
            alert("Please select cash sale account.");
            return false;
        }
        
        /*if(j("#mobile_no").val() == "")
        {
            alert("Please enter valid 10 digit mobile no.");
            return false;
        }
        else
        {
            mobile_nos = j("#mobile_no").val().split(',');
            
            for(i=0; i<mobile_nos.length; i++)
            {
                if(mobile_nos[i].length != 10 || isNaN(mobile_nos[i]))
                {
                    alert("Enter valid numeric 10 digit mobile number");
                    return false;
                }
            }
        }*/
        
        if(j("#tin_no").val() == "")
        {
            alert("Please enter tin no.");
            return false;
        }
        if(j("#invoice_footer").val() == "")
        {
            alert("Please enter invoice footer.");
            return false;
        }
        
        j.post("index.php?option=com_settings&task=save_settings&tmpl=xml",j("#SettingsForm").serialize(),function(data){
           if(data == "ok")
           {
               alert("Settings saved successfully.");
               go(window.location);
           }
           else
           {
               alert(data);
           }
        });
    }
</script>
<div class="view_title"> 
    <h1>Settings</h1>
</div>
<form method="post" id="SettingsForm" action="">
    <table class="clean">
        <!--<tr>
            <td>Financial Year</td>
            <td>
                <input type="text" id="fy_year" name="fy_year" value="<? //echo $this->fy_year; ?>" onchange="show_fy_year();">
                <span id="financial_year"></span>
            </td> 
        </tr> -->
        <tr>
            <td>Credit Days</td>
            <td>
                <input type="text" id="credit_days" name="credit_days" value="<? echo $this->credit_days; ?>">
           </td>          
        </tr>
        <tr>
            <td>Default Location</td>
            <td>
                <select id="default_location_id" name="default_location_id">
                <option value="0"></option>
                <?
                    if(count($this->locations) > 0)
                    {
                        foreach($this->locations as $location)
                        {
                            ?><option value="<? echo $location->id; ?>" <? echo ($this->default_location_id == $location->id ? "selected='selected'" : ""); ?> ><? echo $location->location_name; ?></option><?
                        }
                    }
                ?>
            </select>
           </td>          
        </tr>
        <tr>
            <td>Cash Sale Account</td>
            <td>
                <select id="cash_sale_customer_id" name="cash_sale_customer_id">
                <option value="0"></option>
                <?
                    if(count($this->customers) > 0)
                    {
                        foreach($this->customers as $customer)
                        {
                            ?><option value="<? echo $customer->id; ?>" <? echo ($this->cash_sale_customer_id == $customer->id ? "selected='selected'" : ""); ?> ><? echo $customer->customer_name; ?></option><?
                        }
                    }
                ?>
            </select>
           </td>          
        </tr>
        <tr>
            <td>Opening Cash in Hand</td>
            <td>
                <input type="text" id="opening_cash_in_hand" name="opening_cash_in_hand" value="<? echo round_2dp($this->opening_cash_in_hand); ?>">
           </td>          
        </tr>
        <tr>
            <td>Users Allowed Back-date Payments</td>
            <td>
                <select id="users_allowed_backdate_payments" name="users_allowed_backdate_payments[]" multiple="multiple">
                <option value="0"></option>
                <?
                    if(count($this->users) > 0)
                    {
                        foreach($this->users as $user)
                        {
                            ?><option value="<? echo $user->id; ?>" <? echo (in_array($user->id, $this->users_allowed_backdate_payments) ? "selected='selected'" : ""); ?> ><? echo $user->name; ?></option><?
                        }
                    }
                ?>
            </select>
           </td>          
        </tr>
        <tr>
            <td>Mobile No.</td>
            <td>
                <input type="text" id="mobile_no" name="mobile_no" value="<? echo $this->mobile_no; ?>">
                <br />
                <small>Note: You may insert multiple nos by separating them with commas(,)</small>
            </td> 
        </tr>
        <tr>
            <td>Day Munshi Mobile No.</td>
            <td>
                <input type="text" id="day_munshi_mobile_no" name="day_munshi_mobile_no" value="<? echo $this->day_munshi_mobile_no; ?>">
            </td> 
        </tr>
        <tr>
            <td>Night Munshi Mobile No.</td>
            <td>
                <input type="text" id="night_munshi_mobile_no" name="night_munshi_mobile_no" value="<? echo $this->night_munshi_mobile_no; ?>">
            </td> 
        </tr>
        <tr>
            <td>Tin No.</td>
            <td>
                <input type="text" id="tin_no" name="tin_no" value="<? echo $this->tin_no; ?>">
            </td> 
        </tr>
        <tr>
            <td>GST No.</td>
            <td>
                <input type="text" id="gst_no" name="gst_no" value="<? echo $this->gst_no; ?>">
            </td> 
        </tr>
        <tr>
            <td>Invoice Footer</td>
            <td>
                <input type="text" id="invoice_footer" name="invoice_footer" value="<? echo $this->invoice_footer; ?>">
            </td> 
        </tr>
        <tr>
            <td>SMS Balance</td>
            <td><? echo $this->sms_balance; ?></td> 
        </tr>
        <tr>
            <td>Self GST State Code</td>
            <td><input type="text" id="self_gst_state_code" name="self_gst_state_code" value="<? echo $this->self_gst_state_code; ?>"/></td>
        </tr>
         <tr>
            <td>Default Product</td>
            <td>
                <select id="product_type_diesel" name="product_type_diesel">
                <option value="0"></option>
                <?
                    if(count($this->products) > 0)
                    {
                        foreach($this->products as $product)
                        {
                            ?><option value="<? echo $product->id; ?>" <? echo ($this->product_type_diesel == $product->id ? "selected='selected'" : ""); ?> ><? echo $product->product_name; ?></option><?
                        }
                    }
                ?>
            </select>
           </td>          
        </tr>
    </table>
    <br />
    <input type="submit" name="submit" id="submit" value="Submit (Alt + Z)" onclick="validateForm(); return false;">
</form>