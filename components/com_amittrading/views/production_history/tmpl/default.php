<?php
    defined('_JEXEC') or die;
    //manage product 
?>

<script>
    j(function(){ 
        j( ".datepicker" ).datepicker({"dateFormat" : "dd-M-yy", changeMonth: true, changeYear: true});        
        j( "#production_form" ).dialog({
            autoOpen: false,
            height: 230,
            width: 380,
            modal: true,
            buttons: 
            {  
                "Submit (Alt+Z)": function() 
                { 
                    if(j("#prloduction_date").val() == "")
                    {
                        alert("Please fill Production Date.");
                        return false;
                    }
                    if(j("#product_id").val() == "")
                    {
                        alert("Please Select Product Name.");
                        return false;
                    }
                    if(j("#total_production").val() == "")
                    {
                        alert("Please Fill Total Production.");
                        return false;
                    }
                    if(j("#comment").val() == "")
                    {
                        alert("Please Fill Comment.");
                        return false;
                    }
                    
                    j('#production_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_amittrading&task=update_production&tmpl=xml&" + j("#productionForm").serialize() + "&production_id=" + j("#production_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#production_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#production_form").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_amittrading&task=save_production&tmpl=xml&" + j("#productionForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#production_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#production_form").dialog( "close" );
                                go(window.location); 
                            }
                        });
                    }
                },
                Cancel: function() {
                     j( this ).dialog( "close" );
                }
            },
        }); 
        j('button:contains(Submit)').attr("id","submit_button");
    });
     
    j(document).on("keydown", function(e){
        if(e.altKey && e.which == 65)
        {
            new_production();
        }
    });
    j(document).on("keyup", function(e){
        //if (e.keyCode == 13)
        if ((e.altKey && e.which == 90))
        {
           j('#submit_button').click();  
        }
    });
    
    function new_production()
    {   
        j("#mode").val("");
        j("#production_id").val("");
        
        j("#production_date").val("");
        j("#product_id").val("");
        j("#total_production").val("");
        j("#comment").val("");
        
        j("#production_form").dialog("open");
        j("#production_form").dialog({"title":"New Production"});
        j('#production_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_production(production_id)
    {   
        j("#production_id").val(production_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_amittrading&task=production_details&tmpl=xml&production_id=" + production_id, function(data){
            production_details = j.parseJSON(data);
            
            j("#production_date").val(production_details.production_date);  
            j("#product_id").val(production_details.product_id);  
            j("#total_production").val(production_details.total_production);  
            j("#comment").val(production_details.comment);  
        });
                
        j("#production_form").dialog("open");
        j("#production_form").dialog({"title":"Edit Production"});
        j('#production_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    } 
     
    function delete_production(production_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_amittrading&task=delete_production&production_id=" + production_id);
         }
         else
         {
            return false;
         }
    }
    
    function refresh()
    {
        go("index.php?option=com_amittrading&view=production_history&from_date=" + j("#from_date").val() + "&to_date=" + j("#to_date").val() + "&product_type=" + j("#product_type").val());
    } 
    
</script>
<h1>Production History </h1>
<button type="button" onclick="new_production();">New Production Entry</button> 
<br /><br />

<table>
    <tr>
        <td>From Date : </td>
        <td><input type="text" name="from_date" id="from_date" class="datepicker" value="<?= ($this->from_date!= "" ? date("d-M-Y", strtotime($this->from_date)) : ""); ?>" style="width : 80px;"/> </td>                           
        <td>To Date : </td>
        <td><input type="text" name="to_date" id="to_date" class="datepicker" value="<?= ($this->to_date!= "" ? date("d-M-Y", strtotime($this->to_date)) : ""); ?>" style="width : 80px;"/> </td>                           
    
        <!--<td>Product Name : </td>
        <td>
            <select id="product_type" name="product_type" style="width:150px;" >
                <option></option>    
                <?
                    //foreach($this->products as $products)
                    //{
                        ?>
                            <option value="<? //echo $products->id; ?>"><? //echo $products->product_name; ?></option>
                        <?
                    //}   
                ?>
            </select>
        </td>--> 
        
        <td>
            <input type="button" value="Refresh" onclick="refresh();">
            <input type="button" value="Clear" onclick='go("index.php?option=com_amittrading&view=production_history");'/>
        </td>
    </tr>
</table>
<br /><br />
                                                                                                                                                                        
<div id="production_list" >
    <table class="clean" id="productions">
        <tr>
            <th width="20">S.No.</th>
            <th>Production Date</th>
            <th>Product Name</th>
            <th>Total Production</th>
            <th>Comments</th>
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
            if(count($this->productions) > 0)
            {
                $x = 0;
                foreach($this->productions as $productions)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td><? echo date("d-M-Y", strtotime($productions->production_date)); ?></td>
                        <td><? echo $productions->product_name; ?></td>
                        <td><? echo $productions->total_production; ?></td>
                        <td><? echo $productions->comment; ?></td>
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td class="noprint" align="center">
                                    <a onclick="edit_production(<? echo $productions->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_production(<? echo $productions->id; ?>);" class="delete">
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
<input type="hidden" name="production_id" id="production_id" value="" /> 

<div style="display: none;" id="production_form">
    <form method="post" id="productionForm">
        <table class="">
            <tr>
                <td>Production Date :</td>
                <td><input type="text" name="production_date" id="production_date" class="datepicker" value="<?= $this->production_date; ?>" style="width : 200px;"/> </td>                           
            </tr>
            <tr>
                <td>Product Name :</td>
                <td>
                    <select name="product_id" id="product_id" style="width:204px;">
                        <option></option>
                        <?
                            foreach($this->products as $products)
                            {
                                ?>
                                <option value="<? echo $products->id; ?>"><? echo $products->product_name; ?></option>
                                <?
                            }
                        ?>
                        
                    </select>
                </td>
            </tr>
            <tr>
                <td>Total Production :</td>
                <td><input type="text" name="total_production" id="total_production" style="width : 200px;" /></td>
            </tr>
            <tr>
                <td>Comments :</td>
                <td><input type="text" name="comment" id="comment" style="width : 200px;"/></td>
            </tr>
        </table>
    </form>
</div>