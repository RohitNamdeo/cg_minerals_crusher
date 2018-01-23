<?php defined('_JEXEC') or die('Restricted access'); ?>
<h1>Menu Manager</h1>
<button onclick="window.location='index.php?option=com_twolevelmenu&view=menudef';">New Menu Item</button>
<br /><br />
<table class="clean spread">
<tr>
	<th width="20">
	    #
	</th>
	<th>
	    Menu Item
	</th>
	<th>
	    Location
	</th>
	<th width="100">
	    Order
	</th>
	<th width="100">
	    Actions
	</th>
</tr>
<?
    global $x;
    $x = 0;
    foreach($this->menus as $menu)
    {
        if ($menu["parent"] == 0 )
        {
            ?>
                <tr>
	                <td align="center">
                        <? $x++; echo $x; ?>
                    </td>
	                <td>
                        <a href="index.php?option=com_twolevelmenu&view=menudef&m=e&mi=<? echo $menu["id"]; ?>"><? echo $menu["name"]?></a>
                    </td>
	                <td>
                        <? $link = ($menu["has_children"] == true ? "" : ( $menu["direct_link"] !="" ? $menu["direct_link"] : "index.php" . ($menu["option"] !="" ? "?option=" . $menu["option"] . ( $menu["view"] != "" ? "&view=" . $menu["view"] : ( $menu["task"] != "" ? "&task=" . $menu["task"] : "" ) ) . ( $menu["additional_params"] != "" ? $menu["additional_params"] : "" ) : ""))); ?>
                        <a href="<? echo $link; ?>" target="_new"><? echo $link; ?></a>
                    </td>
	                <td align="center">
                        <? echo $menu['order']; ?>
                    </td>
                    <td align="center">
                        <a href="index.php?option=com_twolevelmenu&view=menudef&m=e&mi=<? echo $menu["id"]; ?>" alt="Edit" title="Edit Menu"><img src="custom/graphics/icons/blank.gif" class="edit"></a>
				        <a href="index.php?option=com_twolevelmenu&task=delete_menu&mi=<? echo $menu["id"]; ?>" onclick="if(confirm('Do you really want to delete this menu and all of its submenus?')) {return true;} else {return false;}" alt="Delete Menu" title="Delete Menu"><img src="custom/graphics/icons/blank.gif" class="delete"></a>
	                </td>
                </tr>
            <?
            if ($menu["has_children"] == true && count($menu["children"]) > 0)
            {
                foreach($menu["children"] as $menu_id)
                {
                    print_sublevel($menu_id, 1, $this->menus);
                }
            }
        }
    }
    
    function print_sublevel($menu_id, $level, &$menus)
    {
        global $x;
        $menu = $menus[$menu_id];
        ?>
           <tr>
                <td align="center">
                    <? $x++; echo $x; ?>
                </td>
                <td>
                   <? echo str_repeat("&nbsp; ", $level * 2); ?> - <a href="index.php?option=com_twolevelmenu&view=menudef&m=e&mi=<? echo $menu["id"]; ?>"><? echo $menu["name"]?></a>
                </td>
                <td>
                    <? $link = ($menu["has_children"] == true ? "" : ( $menu["direct_link"] !="" ? $menu["direct_link"] : "index.php" . ($menu["option"] !="" ? "?option=" . $menu["option"] . ( $menu["view"] != "" ? "&view=" . $menu["view"] : ( $menu["task"] != "" ? "&task=" . $menu["task"] : "" ) ) . ( $menu["additional_params"] != "" ? $menu["additional_params"] : "" ) : ""))); ?>
                    <a href="<? echo $link; ?>" target="_new"><? echo $link; ?></a>
                </td>
                <td align="center">
                    <? echo $menu['order']; ?>
                </td>
                <td align="center">
                    <a href="index.php?option=com_twolevelmenu&view=menudef&m=e&mi=<? echo $menu["id"]; ?>" alt="Edit" title="Edit Menu"><img src="custom/graphics/icons/blank.gif" class="edit"></a>
                    <a href="index.php?option=com_twolevelmenu&task=delete_menu&mi=<? echo $menu["id"]; ?>" onclick="if(confirm('Do you really want to delete this menu and all of its submenus?')) {return true;} else {return false;}" alt="Delete Menu" title="Delete Menu"><img src="custom/graphics/icons/blank.gif" class="delete"></a>
                </td>
           </tr>
        <?
        if ($menu["has_children"] == true && count($menu["children"]) > 0)
        {
            foreach($menu["children"] as $menu_id)
            {
                print_sublevel($menu_id, $level + 1, $menus);
            }
        }
    }
?>
</table>
