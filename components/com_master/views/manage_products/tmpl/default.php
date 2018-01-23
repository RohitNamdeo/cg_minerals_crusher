<?php
    defined('_JEXEC') or die;
    //manage product 
?>

<script>
    j(function(){         
        j( "#product_form" ).dialog({
            autoOpen: false,
            width: 350,
            height:280,
            modal: true,
            buttons: 
            {  
                "Submit (Alt+Z)": function() 
                { 
                    if(j("#product_name").val() == "")
                    {
                        alert("Fill product name.");
                        return false;
                    }
                    if(j("#unit_name").val() == null)
                    {
                        alert("Select unit name.");
                        return false;
                    }
                    if(j("#gst_percent").val() == "")
                    {
                        alert("Select gst percent.");
                        return false;
                    }
                    if(j("#hsn_code").val() == 0)
                    {
                        alert("Fill hsn code.");
                        return false;
                    }
                    
                    j('#product_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_product&tmpl=xml&" + j("#productForm").serialize() + "&product_id=" + j("#product_id").val(), function(data){
               
                           if(data != "ok")
                            {
                                alert(data);
                                j('#product_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#product_form").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_product&tmpl=xml&" + j("#productForm").serialize(), function(data){
                            if(data != "ok")
                            {
                                alert(data);
                                j('#product_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#product_form").dialog( "close" );
                                go(window.location); 
                            }
                        });
                    }
                },
                Cancel: function() {
                     j(this).dialog( "close" );
                }
            },
        }); 
        j('button:contains(Submit)').attr("id","submit_button");
    });
    
    j(document).on("keypress","#product_name", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
    /*j(document).on("blur","#product_name", function(){
        var product_name = j(this).val();   
        current_object = j(this);
        if(isNaN(product_name) && product_name!= "")
        {
            j.get("index.php?option=com_amittrading&task=get_product_name&tmpl=xml&product_name=" + product_name, function(data){
            //alert(data);
                if(data != "")
                {  
                    alert(data);
                    j(current_object).parent().parent().parent().find("#product_name").val("");
                    j(current_object).parent().parent().parent().find("#product_name").focus();
                }        
            }); 
        }   
    });*/ 
    
    j(document).on("keypress","#product_name",function (e) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str) || e.keyCode == 8 ||(key == 32) || e.keyCode == 9 || e.keyCode == 32 || e.keyCode == 13)   
        {
            return true;
        }
        e.preventDefault();
        return false;
    });
    
    j(document).on("keypress","#hsn_code",function(e){
        if(!(e.which>=48 && e.which<=57 ))
        {
            if(!((e.which == 0) || (e.which==8) ||(e.which==46)))
            e.preventDefault();    
        }
    });
     
    j(document).on("keydown", function(e){
        if(e.altKey && e.which == 65)
        {
            add_product();
        }
    });
    j(document).on("keyup", function(e){
        //if (e.keyCode == 13)
        if ((e.altKey && e.which == 90))
        {
           j('#submit_button').click();  
        }
    });
    
    function add_product()
    {   
        j("#mode").val("");
        j("#product_id").val("");
        
        j("#product_name").val("");
        j("#unit_name").val("");
        j("#gst_percent").val("");
        j("#hsn_code").val("");
        
        j("#product_form").dialog("open");
        j("#product_form").dialog({"title":"Add Product"});
        j("#unit_name, #gst_percent").chosen({allow_single_deselect: true});
        j('#product_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_product(product_id)
    {   
        //alert(product_id);
        j("#product_id").val(product_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=product_details&tmpl=xml&product_id=" + product_id, function(data){
            product_details = j.parseJSON(data);
            
            j("#product_name").val(product_details.product_name); 
            j("#unit_name").val(product_details.unit_id); 
            j("#gst_percent").val(product_details.gst_percent);
            j("#hsn_code").val(product_details.hsn_code);
        });
                
        j("#product_form").dialog("open");
        j("#product_form").dialog({"title":"Edit Product"});
         //j("#unit_name, #gst_percent").chosen({allow_single_deselect: true});
        j('#product_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    } 
     
    function delete_product(product_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_product&product_id=" + product_id);
         }
         else
         {
            return false;
         }
    } 
    
</script>
<h1>Manage Products </h1>
<button type="button" onclick="add_product();"><u>A</u>dd Product</button> 
<br /><br />                                                                                                                                                                        
<div id="product_list">
    <table class="clean" width="600" id="products">
        <tr>
            <th width="20">S.No.</th>
            <th>Product Name</th>
             <th>Unit</th> 
              <th>GST percent</th> 
              <th>HSN Code</th> 
            <?
                if(is_admin())
                {
                    ?>    
                    <th class="noprint">Action</th>
                    <?
                }
            ?>
        </tr>
        <?  
            if(count($this->products_name) > 0)
            {
                $x = 0;
                foreach($this->products_name as $products_name)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo $products_name->product_name; ?></td>
                        <td><? echo $products_name->unit; ?></td>
                        <td><? echo $products_name->gst_percent; ?></td>  
                        <td><? echo $products_name->hsn_code; ?></td>  
                       
                        
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td class="noprint" align="center">
                                    <a onclick="edit_product(<? echo $products_name->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_product(<? echo $products_name->id; ?>);" class="delete">
                                </td>
                                <?
                            }
                        ?> 
                    </tr>
                    <?
                }
            }
        ?>
    </table>
</div>
<br />
<input type="hidden" name="mode" id="mode" value="" /> 
<input type="hidden" name="product_id" id="product_id" value="" /> 

<div style="display: none;" id="product_form">
    <form method="post" id="productForm">
        <table class="" id="product_table">
            <tr>
                <td> Product Name :</td>
                <td><input type="text" id="product_name" name="product_name" style="width:200px;"/></td>
                <tr>
                <tr>
                <td>Unit</td>                      
                <td>
                    <!--<input type="text" id="vat_percent" name="vat_percent" style="width:270px;" />-->
                    <select id="unit_name" name="unit_name" style="width: 200px;">
                        
                        <?
                            if(count($this->units) > 0)
                            {
                                foreach($this->units as $unit)
                                {
                                    ?>
                                        <option value="<? echo $unit->id; ?>"> <? echo $unit->unit ;?></option>
                                    <?    
                                }
                            }    
                        ?>                        
                    </select>
                </td>
            </tr>
                
            <tr>
                <td>GST Percent</td>
                <td>
                    <select id="gst_percent" name="gst_percent" style="width: 200px;">
                        <option></option>
                        <option value="<?= GST_PERCENT_0; ?>">0</option>
                        <option value="<?= GST_PERCENT_5; ?>">5</option>
                        <option value="<?= GST_PERCENT_12; ?>">12</option>
                        <option value="<?= GST_PERCENT_18; ?>">18</option>
                        <option value="<?= GST_PERCENT_28; ?>">28</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>HSN Code</td>
                <td><input type="text" name="hsn_code" id="hsn_code" style="width: 200px;"/></td>
            <tr>
            </tr>
        </table>
    </form>
</div>