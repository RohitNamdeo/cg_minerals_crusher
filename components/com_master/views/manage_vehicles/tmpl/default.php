<?php
    defined('_JEXEC') or die;
    //manage vehicles 
?>

<script>

    j(function(){         
        j( "#vehicle" ).dialog({
            autoOpen: false,
            //height: 310,
            width: 410,
            modal: true,
            buttons: 
            {
                "Submit (Alt+Z)": function() 
                {    
                    if(j("#vehicle_number").val() == "")
                    {
                        alert("Fill vehicle number.");
                        return false;
                    }
                    
                    if(j("#vehicle_type").val() == "")
                    {
                        alert("Fill vehicle type.");
                        return false;
                    }
                    
                    if(j("#owner_number").val() == "" )
                    {
                        alert("Fill owner number.");
                        return false;
                    }
                    else
                    {
                        if(j("#owner_number").val().length != 10)
                        {
                            alert("Contact number must be 10 digits only.");return false;
                        }
                    }
                    if(j("#other_contact_numbers").val() != "")
                    {
                        if(j("#other_contact_numbers").val().length !=10)
                        {
                            alert("Other contact number. invalid.");return false;    
                        }
                    }
                    j('#vehicle').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_vehicle&tmpl=xml&" + j("#vehicleForm").serialize() + "&vehicle_id=" + j("#vehicle_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#vehicle').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#vehicle").dialog("close");
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_vehicle&tmpl=xml&" + j("#vehicleForm").serialize(), function(data){
                            //alert(data);
                            if(data != "")
                            {
                                alert(data);
                                j('#vehicle').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#vehicle").dialog( "close" );
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
    
   j(document).keypress("#vehicle_number",function(e){
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str) || e.which == 8 || e.which == 9 || e.which == 32 || e.which == 13 || e.which == 45)   
        {
            return true;
        }
        e.preventDefault();
        return false;
    });
    
    j(document).on("keypress","#owner_name", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
   
    
    j(document).on("keydown","#owner_name" ,function (e) {
        if ( e.ctrlKey || e.altKey) 
        {
            e.preventDefault();
        } 
        else
        {
            var key = e.keyCode;
            if (!((key == 8) ||(key == 9) || (key == 32) || (key == 13) || (key >= 65 && key <= 90))) 
            {
                e.preventDefault();
            }
        }  
    });
    
    j(document).on("keypress",".contact_no",function(e){
        if(!(e.which>=48 && e.which<=57 ))
        {
            if(!((e.which == 0) || (e.which==8)))
            e.preventDefault();    
        }
    });
    
    j(document).on("keydown", function(e){
        if (e.altKey && e.which == 65)
        {
            add_vehicle();
        }
    });
    j(document).on("keyup", function(e){
        //if (e.keyCode == 13)
        if (e.altKey && e.which == 90)
        {
           j('#submit_button').click();  
        }
    });
    
    function add_vehicle()
    {   
        j("#mode").val("");
        j("#vehicle_id").val("");
        j("#vehicle_number").val("");
        j("#vehicle_type").val("");
        j("#owner_name").val("");
        j("#owner_address").val("");
        j("#owner_number").val("");
        j("#other_contact_numbers").val("");
        
        j("#vehicle").dialog("open");
        j("#vehicle").dialog({"title":"Add Vehicle"});
        j("#vehicle_type, #owner_name").chosen({allow_single_deselect: true});
        j('#vehicle').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_vehicle(vehicle_id)
    {   
        j("#vehicle_id").val(vehicle_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=vehicle_details&tmpl=xml&vehicle_id=" + vehicle_id, function(data){
            vehicle_details = j.parseJSON(data);
            alert(data);
            
            j("#vehicle_number").val(vehicle_details.vehicle_number);  
            j("#vehicle_type").val(vehicle_details.vehicle_type);  
            j("#owner_name").val(vehicle_details.transporter_id);
            j("input[name=self_rent][value='"+vehicle_details.self_rent_id+"']").prop("checked",true);
            j("#owner_address").val(vehicle_details.owner_address);  
            j("#owner_number").val(vehicle_details.owner_number);  
            j("#other_contact_numbers").val(vehicle_details.other_contact_numbers);  
        });
                
        j("#vehicle").dialog("open");
        j("#vehicle").dialog({"title":"Edit Vehicle"});
     //   j("#vehicle_type").chosen({allow_single_deselect: true});
        j('#vehicle').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    } 
     
    function delete_vehicle(vehicle_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_vehicle&vehicle_id=" + vehicle_id);
         }
         else
         {
            return false;
         }
    }
    
    function generatefromtable() 
    {
        var data = [], fontSize = 7, height = 0, doc;
        doc = new jsPDF('p', 'pt', 'a4', true);
        doc.setFont("helvetica", "normal");
        doc.setFontSize(fontSize);
        doc.text(270, 20, "Manage Vehicles Report");
        data = [];
        data = doc.tableToJson('vehicles');
        height = doc.drawTable(data, {
            xstart : 10,
            ystart : 10,
            tablestart : 30,
            marginright : 5, 
            xOffset : 5,
            yOffset : 9,
            columnWidths:[30,75,60,100,65,95,60,90,00]
        });
        doc.text(50, height + 20, '');
        doc.save("Vehicles Report.pdf");
    } 
    
</script>
<h1>Manage Vehicles</h1>
<button type="button" onclick="add_vehicle();"><u>A</u>dd Vehicle</button>
<button type="button" id='pdfExport' onclick="generatefromtable();">Export PDF</button>
 
<br /><br />                                                                                                                                                                        
<div id="vehiclelist" >
    <table class="clean" id="vehicles">
        <thead>
            <tr>
                <th width="20">S.No.</th>
                <th>Vehicle Number</th>
                <th>Vehicle Type</th>
                <th>Owner Name</th>
                <th>Self Or Rent</th>
                <th>Owner Address</th>
                <th>Mobile Number</th>
                <th>Other Contact Numbers</th>
                <?
                    if(is_admin())
                    {
                        ?>    
                        <th class="noprint">Action</th>
                        <?
                    }
                ?>
            </tr>
        </thead>
        <tbody>
            <?  
                if(count($this->vehicles) > 0)
                {
                    $x = 0;
                    foreach($this->vehicles as $vehicles)
                    {
                        ?>
                        <tr>
                            <td align="center"><? echo ++$x; ?></td>
                            <td><? echo $vehicles->vehicle_number; ?></td>
                            <td><? echo $vehicles->vehicle_type; ?></td>
                            <td><? echo $vehicles->transporter_name; ?></td>
                            <td><? if($vehicles->self_rent_id == 1){echo "Self";} else {echo "Rent";}?></td>
                            <td><? echo $vehicles->owner_address; ?></td>
                            <td><? echo $vehicles->owner_number; ?></td>
                            <td><? echo $vehicles->other_contact_numbers; ?></td>
                            <?
                                if(is_admin())
                                {
                                    ?>    
                                    <td class="noprint" align="center">
                                        <a onclick="edit_vehicle(<? echo $vehicles->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                        <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_vehicle(<? echo $vehicles->id; ?>);" class="delete">
                                    </td>
                                    <?
                                }
                            ?> 
                        </tr>
                        <?
                    }
                }
            ?>
        </tbody>
    </table>
</div>
<br />
<input type="hidden" name="mode" id="mode" value="" /> 
<input type="hidden" name="vehicle_id" id="vehicle_id" value="" /> 

<div style="display: none;" id="vehicle">
    <form method="post" id="vehicleForm">
        <table class="">
            <tr>
                <td> Vehicle Number :</td>
                <td><input type="text" id="vehicle_number" name="vehicle_number" style="width:220px;"/></td>
            </tr>
            <tr>
                <td> Vehicle Type :</td>
                <td><!--<input type="text" id="vehicle_type" name="vehicle_type" />-->
                    <select id="vehicle_type" name="vehicle_type" style="width:224px;">
                        <option></option>
                        <?
                            foreach($this->vehicles_type as $vehicles_type)
                            {
                                ?>
                                <option value="<? echo $vehicles_type->id; ?>"><? echo $vehicles_type->vehicle_type; ?></option>
                                <?
                            }
                        ?> 
                    </select>        
                </td>
            </tr>
            <tr>
                <td> Owner Name :</td>
                <td>
                    <select id="owner_name" name="owner_name" style="width:224px;">
                        <option></option>
                        <?
                            foreach($this->transporters as $transporter)
                            {
                                ?>
                                <option value="<? echo $transporter->id; ?>"><? echo $transporter->transporter_name; ?></option>
                                <?
                            }
                        ?> 
                    </select> 
                </td>
            </tr>
            <tr>
                <td>Self Or Rent</td>
                <td>
                    <input type="radio" name="self_rent" class="self_rent" value="<? echo SELF; ?>" checked="checked">Self &nbsp; &nbsp;&nbsp;
                    <input type="radio" name="self_rent" class="self_rent" value="<? echo RENT; ?>">Rent
                </td>
                
            </tr>
            <tr>
                <td> Owner Address :</td>
                <td><textarea name="owner_address" id="owner_address" style="width:220px;"></textarea></td>
            </tr>
            <tr>
                <td> Owner Mobile Number :</td>
                <td><input type="text" id="owner_number" name="owner_number" class="contact_no" style="width:220px;" /></td>
            </tr>
            <tr>
                <td>Other Contact Numbers :</td>
                <td><input type="text" id="other_contact_numbers" name="other_contact_numbers" class="contact_no" style="width:220px;" /></td>
            </tr>
        </table>
    </form>
</div>

<div id="grid"></div>