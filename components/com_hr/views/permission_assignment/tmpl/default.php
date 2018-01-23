<?
defined ("_JEXEC") or die ("Restricted Access");
?>
<script>
j(function () {
    j(document).on("click",".menu_permit", function(){
        j("input[name^='" + j(this).prop("name") + "']").removeAttr("checked");
        j(this).prop('checked',true);
        j(this).attr('checked',"checked");
        
        if (j(this).hasClass("menuparent0"))
        {
            if (j(this).val() == "0")
            {
                j(".menuparent" + j(this).attr("menuid") + "[value='1']").removeAttr("checked");
                j(".menuparent" + j(this).attr("menuid") + "[value='0']").prop("checked", true);
                j(".menuparent" + j(this).attr("menuid") + "[value='0']").attr("checked", "checked");
            }
            else if (j(this).val() == "1")
            {
                j(".menuparent" + j(this).attr("menuid") + "[value='0']").removeAttr("checked");
                j(".menuparent" + j(this).attr("menuid") + "[value='1']").prop("checked", true);
                j(".menuparent" + j(this).attr("menuid") + "[value='1']").attr("checked", "checked");
            }
        }
    });
});

function validateForm()
{
    j("#permissionForm").submit();
}

</script>
<h1>Permission Assignment for <? echo $this->designation_name; ?></h1><br />
<form method="post" action="index.php?option=com_hr&task=assign_permission" id="permissionForm">
<table class="clean" width="400">
    <tr>
        <th width="20">#</th>
        <th width="20">No</th>
        <th width="20">Yes</th>
        <th>Menu Item</th>
    </tr>
    <?
        $x = 1;
        foreach($this->menus as $menu)
        {
        ?>
            <tr>
                <td align="center">
                    <? echo $x++; ?>
                </td>
                <td>
                    <input type="radio" menuid="<? echo @$menu["id"]; ?>" name="permit[<? echo @$menu["id"]; ?>]" value="0" <? echo (@$this->permissions[$menu["id"]] == 0 ? "checked='checked'" : "");?> class="menu_permit menuparent<? echo @$menu["parent"]; ?>"/>
                </td>
                <td>
                    <input type="radio" menuid="<? echo @$menu["id"]; ?>" name="permit[<? echo @$menu["id"]; ?>]" value="1" <? echo (@$this->permissions[$menu["id"]] == 1 ? "checked='checked'" : "");?> class="menu_permit menuparent<? echo @$menu["parent"]; ?>"/>
                </td>
                <td>
                    <? echo @$menu["name"]?>
                </td>
            </tr>
            <?
            if($menu["has_children"] == true)
            {
                foreach ($menu["children"] as $childmenu)
                {
                    ?>
                    <tr>
                        <td align="center">
                            <? $x++; echo $x; ?>
                        </td>
                        <td>
                            <input type="radio" menuid="<? echo @$childmenu["id"]; ?>" name="permit[<? echo @$childmenu["id"]; ?>]" value="0" <? echo (@$this->permissions[$childmenu["id"]] == 0 ? "checked='checked'" : "");?> class="menu_permit menuparent<? echo @$childmenu["parent"]; ?>" />
                        </td>
                        <td>
                            <input type="radio" menuid="<? echo @$childmenu["id"]; ?>" name="permit[<? echo @$childmenu["id"]; ?>]" value="1"  <? echo (@$this->permissions[$childmenu["id"]] == 1 ? "checked='checked'" : "");?> class="menu_permit menuparent<? echo @$childmenu["parent"]; ?>"/>
                        </td>
                        <td>
                            <? echo str_repeat("&nbsp;", 5) . "l_ " . @$childmenu["name"]?>
                        </td>
                    </tr>
                    <?
                }
            }
        }
    ?>
</table><br />
<input type="hidden" name="designation_id" value="<? echo $this->designation_id;?>">
<input type="submit" value="Submit (Alt + Z)">
<input type="button" value="Cancel" onclick="history.go(-1);">
</form>