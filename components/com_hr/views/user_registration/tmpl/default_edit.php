<?php defined('_JEXEC') or die('Restricted access'); ?>
<script>
function toggle_all()
{
    j('.menu_permit').each( function() {
        //(j('#all_permits').attr('checked')=='checked' ? j(this).attr('checked','checked') : j(this).removeAttr('checked'));
        //(j('#all_permits').attr('checked')=='checked' ? j("#all_permits").attr('checked','checked') : j("#all_permits").removeAttr('checked'));
    });
}
j(function () {
    
    j(document).on("click",".menu_permit", function (){
        j("input[name^='" + j(this).attr("name") + "']").removeAttr("checked");
        j(this).attr('checked','checked');
        
        if (j(this).hasClass("menuparent0"))
        {
            if (j(this).val() == "-1")
            {
                j(".menuparent" + j(this).attr("menuid") + "[value='0']").removeAttr("checked");
                j(".menuparent" + j(this).attr("menuid") + "[value='1']").removeAttr("checked");
                j(".menuparent" + j(this).attr("menuid") + "[value='-1']").prop("checked", true);
                j(".menuparent" + j(this).attr("menuid") + "[value='-1']").attr("checked", "checked");
            }
            else if (j(this).val() == "0")
            {
                j(".menuparent" + j(this).attr("menuid") + "[value='-1']").removeAttr("checked");
                j(".menuparent" + j(this).attr("menuid") + "[value='1']").removeAttr("checked");
                j(".menuparent" + j(this).attr("menuid") + "[value='0']").prop("checked", true);
                j(".menuparent" + j(this).attr("menuid") + "[value='0']").attr("checked", "checked");
            }
            else if (j(this).val() == "1")
            {
                j(".menuparent" + j(this).attr("menuid") + "[value='-1']").removeAttr("checked");
                j(".menuparent" + j(this).attr("menuid") + "[value='0']").removeAttr("checked");
                j(".menuparent" + j(this).attr("menuid") + "[value='1']").prop("checked", true);
                j(".menuparent" + j(this).attr("menuid") + "[value='1']").attr("checked", "checked");
            }
        }
    });
    
    j( "#username" ).on("keydown", function( event ) {
        if(event.which == 32)
        {
            return false;
        }
    }); 
});
function setChildMenuCheckboxState(menu_id)
{
    if (j(".menu_permit[value=" + menu_id + "]").attr("checked") != "checked")
    {
        j(".menu_cb_parent_" + menu_id).removeAttr("checked");
    }
}
function setParentMenuCheckboxState(cb, menu_id)
{
    if (j(cb).attr("checked") == "checked")
    {
        j(".menu_permit[value=" + menu_id + "]").attr("checked" , "checked"); 
    }
}
var txt;
function show_permissions()
{   
    txt = j("#permission_details").html();
    j("#permission_details").html("");  
    //j("<div></div>").html("<div id='prompthtml'>" + txt + "</div>").dialog({
    j( "#permission_dialog" ).html("<div id='prompthtml'>" + txt + "</div>").dialog({
        title: "Permission Assignment",
        autoOpen: true,
         height: 400,
        width: 440,
        modal: true,
        buttons: {
            "Assign": function() {
                    //alert('assign');
                    txt = j("#prompthtml").html();
                    j( this ).dialog( "close" );
            },
            Cancel: function() {
                //alert('cancel');
                j( this ).dialog( "close" );
            }
        },
        close : function() {
            //alert(txt);
            j("#permission_details").html(txt);
            j("#prompthtml").remove();
        }
    });
}

function validateForm()
{   
    var emailPattern = /^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if(j("#first_name").val() == "")
    {
        alert("Please enter First Name."); return false;
    } 
    if(j("#last_name").val() == "")
    {
        alert("Please enter Last Name."); return false;
    }
    if(j("#email").val() == "")
    {
        alert("Please enter Email."); return false;
    }
    if(!emailPattern.test(j("#email").val()))
    {
        alert("Email should be formatted."); return false;
    } 
    if(j("#mobile_number").val() == "" )
    {
        alert("Please enter Mobile number."); return false;
    }
    else if(isNaN(j("#mobile_number").val()))
    {
        alert("Mobile Number should be numeric."); return false;
    }
    else if(j("#mobile_number").val().length != 10)
    {
        alert("Mobile Number should contain 10 digits."); return false;
    }
    else if(j("#designation_id").val() == "0")
    {
        alert("Please select Designation."); return false;
    }
    
    if(!confirm("Are you sure?"))
    {
        return false;            
    }   
    j("#user_registration_update").submit();
}
</script>
<h1>Update User</h1>
<form method="post" id="user_registration_update" action="index.php?option=com_hr&task=user_registration_update">
    <table class="clean">
        <tr>
            <td>First Name</td>
            <td><input type="text" name="first_name" style="width:300px;" id="first_name" value="<? echo $this->employee->first_name; ?>"></td>
        </tr>
        <tr>
            <td>Last Name</td>
            <td><input type="text" name="last_name" style="width:300px;" value="<? echo $this->employee->last_name; ?>"></td>
        </tr>
        <tr>
            <td>Password</td>
            <td>
                <input type="password" name="password" id="password" style="width:300px;"><br />
                <small>Specify password if you want to change. Else leave it blank.</small>
            </td>
        </tr>
        <tr>
            <td>Email ID</td>
            <td><input type="text" name="email" id="email" style="width:300px;" value="<? echo $this->employee->email; ?>"></td>
        </tr>
        <tr>
            <td>Mobile Number</td>
            <td><input type="text" name="mobile_number" id="mobile_number" style="width:300px;" value="<? echo $this->employee->mobile_number ;?>"></td>
        </tr>
        <tr>
            <td>Designation :</td>
            <td>
                <select name="designation_id" id="designation_id" style="width:300px;">
                    <option value="0">Select Designation</option>
                    <? 
                        foreach ($this->designations as $designation)
                        {
                            ?>
                                <option value="<? echo $designation->id; ?>" <? echo ( $this->employee->designation_id == $designation->id) ? "selected='selected'" : "" ?> ><? echo $designation->designation_name; ?></option>
                            <?
                        }
                    ?>
                </select>
            </td>
        </tr>
    </table><br />
    <input type="submit" value="Submit (Alt + Z)" onclick="return validateForm();">&nbsp;
    <input type="button" onclick="show_permissions(); return false;" value="Change Permissions">
    <input type="button" onclick="history.go(-1);" value="Cancel">       
    <input type="hidden" name="e" value="<? echo $this->employee_id; ?>">

    <div id="permission_dialog"></div>

    <div style="display: none;" id="permission_details">
        <?
            $x=0;
        ?>
        <table class="clean" width="400">
            <tr>
                <th>#</th>
                <th>Default</th>
                <th>No</th>
                <th>Yes</th>
                <th>Menu Item</th>
            </tr>
            <?
                foreach($this->menus as $menu)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td>
                            <input type="radio" menuid="<? echo $menu["id"]; ?>" name="permit[<? echo $menu["id"]; ?>]" value="-1"  <?php echo (@$this->permissions[$menu["id"]] == -1 ? "checked='checked'" : "");?> class="menu_permit menuparent<? echo $menu["parent"]; ?>"/>
                        </td>
                        <td>
                            <input type="radio" menuid="<? echo $menu["id"]; ?>" name="permit[<? echo $menu["id"]; ?>]" value="0" <?php echo (@$this->permissions[$menu["id"]] == 0 ? "checked='checked'" : "");?> class="menu_permit menuparent<? echo $menu["parent"]; ?>"/>
                        </td>
                        <td>
                            <input type="radio" menuid="<? echo $menu["id"]; ?>" name="permit[<? echo $menu["id"]; ?>]" value="1" <?php echo (@$this->permissions[$menu["id"]] == 1 ? "checked='checked'" : "");?> class="menu_permit menuparent<? echo $menu["parent"]; ?>"/>
                        </td>
                        <td><? echo $menu["name"]?></td>
                    </tr>
                    <?
                    if($menu["has_children"] == true)
                    {
                        foreach ($menu["children"] as $childmenu)
                        {
                            ?>
                            <tr>
                                <td align="center"><? echo ++$x; ?></td>
                                <td>
                                    <input type="radio" menuid="<? echo $childmenu["id"]; ?>" name="permit[<? echo $childmenu["id"]; ?>]"  <?php echo (@$this->permissions[$childmenu["id"]] == -1 ? "checked='checked'" : "");?> value="-1" class="menu_permit menuparent<? echo $childmenu["parent"]; ?>"/>
                                </td>
                                <td>
                                    <input type="radio" menuid="<? echo $childmenu["id"]; ?>" name="permit[<? echo $childmenu["id"]; ?>]"  <?php echo (@$this->permissions[$childmenu["id"]] == 0 ? "checked='checked'" : "");?> value="0" class="menu_permit menuparent<? echo $childmenu["parent"]; ?>"/>
                                </td>
                                <td>
                                    <input type="radio" menuid="<? echo $childmenu["id"]; ?>" name="permit[<? echo $childmenu["id"]; ?>]"  <?php echo (@$this->permissions[$childmenu["id"]] == 1 ? "checked='checked'" : "");?> value="1" class="menu_permit menuparent<? echo $childmenu["parent"]; ?>"/>
                                </td>
                                <td>
                                    <? echo str_repeat("&nbsp;", 5) . "l_ " . $childmenu["name"]?>
                                </td>
                            </tr>
                            <?
                        }
                    }
                } 
            ?>
        </table>
    </div>
</form>