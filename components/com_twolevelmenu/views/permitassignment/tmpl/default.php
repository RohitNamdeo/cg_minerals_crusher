<?php defined('_JEXEC') or die('Restricted access'); ?>
<h1>Permission Assignment</h1>
<form method="post" action="index.php?option=com_twolevelmenu&task=assign_permits">
<?
$x=0;
?>
<table class="clean" width="300">
<tr>
	<th width="20">
	    #
	</th>
	<th width="20">
	    <input type="checkbox" id="all_permits" onclick="toggle_all();">
	    <script>
	    function toggle_all()
	    {
	    	j('.menu_permit').each( function() {
				(j('#all_permits').attr('checked')=='checked' ? j(this).attr('checked','checked') : j(this).removeAttr('checked'));
			});
		}
	    </script>
	</th>
	<th>
	    Menu Item
	</th>
</tr>
<?
    foreach($this->menus as $menu)
    {
        ?>
        <tr>
	        <td align="center">
                <? $x++; echo $x; ?>
            </td>
	        <td align="center">
                <input type="checkbox" class="menu_permit" name="menu_permit[]" value="<? echo $menu["id"]; ?>" <? echo ($menu['permit'] == "1" ? "checked" : "") ?> >
            </td>
	        <td>
                <? echo $menu["name"]?>
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
			        <td align="center">
		                <input type="checkbox" class="menu_permit" name="menu_permit[]" value="<? echo $childmenu["id"]; ?>" <? echo ($childmenu['permit'] == "1" ? "checked" : "") ?> >
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
<br />
<input type="hidden" name="g" value="<? echo $this->usergroup_id; ?>">
<input type="submit" name="submit" value="Assign">
<button onclick="history.go(-1); return false;">Cancel</button>
</form>