<?php
    defined('_JEXEC') or die;
    //Royalty Booklet 
?>
<style >
    tr > td
    {
        text-align: center;
    }
</style>

<script>
    j(function(){
        j("#purchase_date").datepicker({"dateFormat" : "dd-M-yy", changeMonth: true, changeYear: true});
                 
        j( "#royalty_booklet_form" ).dialog({
            autoOpen: false,
            //height: 270,
            width: 360,
            modal: true,
            buttons: 
            {  
                "Submit (Alt+Z)": function() 
                { 
                    
                    if(j("#booklet_name").val() == "")
                    {
                        alert("Please Fill Booklet Name.");
                        return false;
                    }
                    if(j("#rb_no_from").val() == "")
                    {
                        alert("Please Fill RB No From.");
                        return false;
                    }
                    if(j("#rb_no_to").val() == "" )
                    {
                        alert("Please Fill RB No to.");
                        return false;
                    }
                    else
                    {
                        var rb_no_to = parseInt(j("#rb_no_to").val());
                        var rb_no_from = parseInt(j("#rb_no_from").val());
                        if(rb_no_to < rb_no_from)
                        {
                            alert("Please Put Greater Value RB No From");
                            return false;
                        }
                    }
                 
                    j('#royalty_booklet_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_royalty_booklet&tmpl=xml&" + j("#royaltybookletForm").serialize() + "&royalty_booklet_id=" + j("#royalty_booklet_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#royalty_booklet_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#royalty_booklet_form").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_royalty_booklet&tmpl=xml&" + j("#royaltybookletForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#royalty_booklet_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#royalty_booklet_form").dialog( "close" );
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
    
    /*j(document).on("keydown","#booklet_name",function (e) {
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
    });*/
    
    j(document).on("keypress","#rb_no_from,#rb_no_to,#quantity",function(e){
        if(!(e.which>=48 && e.which<=57 ))
        {
            if(!((e.which == 0) || (e.which==8)))
            e.preventDefault();    
        }
    });
    
    j(document).on("keypress","#rate",function(e){
        if(!(e.which>=48 && e.which<=57 ))
        {
            if(!((e.which == 0) || (e.which==8) ||(e.which==46)))
            e.preventDefault();    
        }
    }); 
     
    j(document).on("keydown", function(e){
        if(e.altKey && e.which == 65)
        {
            add_new_booklet();
        }
    });
     j(document).on("keypress","#booklet_name", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
    j(document).on("keyup", function(e){
        if ((e.altKey && e.which == 90))
        {
           j('#submit_button').click();  
        }
    });
    
    function add_new_booklet()
    {   
        j("#mode").val("");
        j("#royalty_booklet_id").val("");
        
        j("#booklet_name").val("");
        j("#rb_no_from").val("");
        j("#rb_no_to").val("");
        j("#supplier_id").val("");
        j("#quantity").val("");
        j("#rate").val("");
        j("#total_pages").val("");
        j("#self").attr('checked', 'checked');
        show_purchase_field(<? echo SELF ;?>);
        
        j("#royalty_booklet_form").dialog("open");
        j("#royalty_booklet_form").dialog({"title":"Add Booklet"});
        j('#royalty_booklet_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    j(document).on("blur","#rb_no_from",function(){
        total_pages();            
    });
    j(document).on("blur","#rb_no_to",function(){
        total_pages();            
    });
    function total_pages()
    {
        j("#total_pages").val("");
        var rb_no_to = parseInt(j("#rb_no_to").val());
        var rb_no_from = parseInt(j("#rb_no_from").val());
        var total_pages = parseInt(rb_no_to - rb_no_from);
        if (!isNaN(total_pages))
        {
            j("#total_pages").val(total_pages + 1);    
        }
    }
 
    function edit_booklet(royalty_booklet_id)
    {   
        j("#royalty_booklet_id").val(royalty_booklet_id);
        j("#mode").val("e");
        j("#rb_no_from").prop('readonly', true);
        j("#rb_no_to").prop('readonly', true);
        
        j.get("index.php?option=com_master&task=royalty_booklet_details&tmpl=xml&royalty_booklet_id=" + royalty_booklet_id, function(data){
            royalty_booklet_details = j.parseJSON(data);
            
            j("#booklet_name").val(royalty_booklet_details.booklet_name);  
            j("#rb_no_from").val(royalty_booklet_details.rb_no_from);  
            j("#rb_no_to").val(royalty_booklet_details.rb_no_to);
            j("input[name=royalty_type][value='"+royalty_booklet_details.royalty_type+"']").prop("checked",true);
            j("#purchase_date").val(j.datepicker.formatDate('dd-M-yy', new Date(royalty_booklet_details.purchase_date)));
            j("#supplier_id").val(royalty_booklet_details.supplier_id);
            j("#quantity").val(royalty_booklet_details.quantity);  
            j("#rate").val(royalty_booklet_details.rate);  
            j("#total_pages").val(royalty_booklet_details.total_pages);
            show_purchase_field(royalty_booklet_details.royalty_type) ;
             
        });
       
        j("#royalty_booklet_form").dialog("open");
        j("#royalty_booklet_form").dialog({"title":"Edit Booklet"});
        
        j('#royalty_booklet_form').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    } 
     
    function delete_booklet(royalty_booklet_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_royalty_booklet&royalty_booklet_id=" + royalty_booklet_id);
         }
         else
         {
            return false;
         }
    } 
    
    function show_royalty_items(rb_id)
    {
        j.colorbox({href:"index.php?option=com_master&view=royalty_booklet_items&rb_id=" + rb_id + "&tmpl=xml", maxWidth: "100%", maxHeight: "100%", "open" : true});
        return false;
    }
 
    function show_purchase_field(royalty_type)
    {
        if(royalty_type == "<? echo SELF;?>")
        {
            j(".purchase").hide();
        }
        else
        {
            j(".purchase").show();
            j("#date").datepicker({"dateFormat" : "dd-M-yy", changeMonth: true, changeYear: true});   
        }            
    }
    
    function show_all(mode)
    {
        go("index.php?option=com_master&view=royalty_booklets&mode=" + mode);
    }
    
    
</script>
<h1>Royalty Booklets </h1>
<button type="button" onclick="add_new_booklet();"><u>A</u>dd New</button> 
<button type="button" onclick="show_all('show_all');">Show All</button> 
<button type="button" onclick="go('index.php?option=com_master&view=royalty_booklets');">Clear</button> 
<br /><br />                                                                                                                                                                        
<div id="royalty_booklet_list">
    <table class="clean" width="900" id="royalty_booklets">
        <tr>
            <th width="20">S.No.</th>
            
            <th>Booklet Name</th>
            <th>RB No From</th>
            <th>RB No To</th>
            <th>Royalty Type</th>
            <th>Purchase Date</th>
            <th>Supplier</th>
            <th>Qty (MT)</th>
            <th>Rate</th>
            <th>Total Pages</th>
            <th>Used Pages</th>
            <th>Sold Pages</th>
            
            <th>Balance</th>
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
            if(count($this->booklets) > 0)
            {
                $x = 0;
                $hide = "";
                foreach($this->booklets as $booklet)
                {
                    if($this->mode != "show_all")
                    {
                        $hide = ($booklet->total_pages - intval($this->used_pages[$booklet->id]) == 0 ? "none" : "");
                    }
                    ?>
                    <tr style="cursor: pointer;display: <?= $hide; ?>; " royalty_id="<? echo $booklet->id; ?>" >
                        <td align="center" ><? echo ++$x; ?></td>
                        
                        <td ><? echo $booklet->booklet_name; ?></td>
                        <td ><? echo $booklet->rb_no_from; ?></td>
                        <td ><? echo $booklet->rb_no_to; ?></td>
                        <td ><? if($booklet->royalty_type == 1){echo "Self";} else {echo "Purchase";}; ?></td>
                        <td ><? echo ($booklet->purchase_date != "1970-01-01" && $booklet->purchase_date != "0000-00-00" ? date("d-m-Y", strtotime($booklet->purchase_date)) : ""); ?></td>
                        <td ><? echo $booklet->supplier_name; ?></td>
                        <td ><? echo $booklet->quantity; ?></td>
                        <td ><? echo $booklet->rate; ?></td>
                        <td ><? echo $booklet->total_pages; ?></td>
                        <td ><? echo (isset($this->used_pages[$booklet->id])? $this->used_pages[$booklet->id] : 0); ?></td>
                        <td ><? echo (isset($this->sold_pages[$booklet->id])? $this->sold_pages[$booklet->id] : 0); ?></td>
                        <td ><? echo $booklet->total_pages - (intval($this->used_pages[$booklet->id]) + $this->sold_pages[$booklet->id]); ?></td>
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td class="noprint" align="center">
                                    <img src="custom/graphics/icons/blank.gif" id="view" title="View Royalty numbers" onclick="show_royalty_items(<? echo $booklet->id; ?>);" class="view">
                                    <a onclick="edit_booklet(<? echo $booklet->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_booklet(<? echo $booklet->id; ?>);" class="delete">
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
<input type="hidden" name="royalty_booklet_id" id="royalty_booklet_id" value="" /> 

<div style="display: none;" id="royalty_booklet_form">
    <form method="post" id="royaltybookletForm">
        <table class="">
            <tr>
                <td> Booklet Name :</td>
                <td><input type="text" id="booklet_name" name="booklet_name" style="width:200px;"/></td>
            </tr>
            <tr>
                <td> RB No From :</td>
                <td><input type="text" id="rb_no_from" name="rb_no_from" style="width:200px;"/></td>
            </tr>
            <tr>
                <td> RB No To :</td>
                <td><input type="text" id="rb_no_to" name="rb_no_to" style="width:200px;"/></td>
            </tr>
            <tr>
                <td>Total Pages :</td>
                <td><input type="text" name="total_pages" id="total_pages" style="width:200px;" readonly="readonly"></td>
            </tr>
            <tr>
                <td>Royalty Type :</td>
                <td>
                    <input type="radio" name="royalty_type" id="self" class="royalty_type" value="<? echo SELF; ?>" checked="checked" onclick="show_purchase_field(<? echo SELF ;?>);">&nbsp;&nbsp;Self &nbsp;&nbsp; &nbsp;&nbsp;
                    <input type="radio" name="royalty_type" id="purchase" class="royalty_type" value="<? echo PURCHASE; ?>" onclick="show_purchase_field(<? echo PURCHASE ;?>);">&nbsp;&nbsp;Purchase &nbsp;
                </td>
            </tr>
            <tr class="purchase">
                <td>Purchase Date :</td>
                <td><input type="text" name="purchase_date" id="purchase_date" style="width:200px;" value="<? echo date("d-M-Y"); ?>" readonly="readonly"/></td>
            </tr>
            <tr class="purchase">
                <td>Supplier :</td>
                <td>
                    <select name="supplier_id" id="supplier_id" style="width:200px;">
                        <option></option>
                        <?
                         if(count($this->suppliers) > 0)
                            {
                                foreach($this->suppliers as $supplier)
                                {
                                ?>
                                <option value="<? echo $supplier->id; ?>" ><? echo $supplier->supplier_name; ?></option>
                                <?
                                }
                            }
                        ?>
                    </select>
                </td>    
            </tr>
            
            <tr>
                <td>Quantity :</td>
                <td><input type="text" name="quantity" id="quantity" style="width:200px;"></td>
            </tr>
            <tr>
                <td>Rate :</td>
                <td><input type="text" name="rate" id="rate" style="width:200px;"></td>
            </tr>
           
        </table>
    </form>
</div>