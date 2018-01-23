<?php defined('_JEXEC') or die('Restricted access'); ?>
<script>
j(document).ready(function(){
    j("#menudef").validate();
});
</script>
<h1>Menu Definition</h1>
<form method="post" action="index.php?option=com_twolevelmenu&task=create_menu" id="menudef">
<table class="clean">
  <tr>
    <td width="50%">Name :</td>
    <td width="50%"><input type="text" size="20" name="name" class="required"></td>
  </tr>
  <tr>
    <td width="50%">Parent :</td>
    <td width="50%">
    <select name="parent" class="required">
    	<option value="0">None</option>
    	<?
    	foreach($this->menus as $menu)
    	{
            $menu = (object) $menu;
            if ($menu->parent == 0 )
            {
    		    ?>
    			    <option value="<? echo $menu->id; ?>"><? echo $menu->name; ?></option>
    		    <?
                if ($menu->has_children == true && count($menu->children) > 0)
                {
                    foreach($menu->children as $menu_id)
                    {
                        print_sublevel($menu_id, 1, $this->menus);
                    }
                }
            }
		}
        function print_sublevel($menu_id, $level, &$menus)
        {
            $menu = $menus[$menu_id];
            $menu = (object) $menu;
            ?>
                <option value="<? echo $menu->id; ?>"><? echo str_repeat("&nbsp;", $level * 2). " - " . $menu->name; ?></option>
            <?
            if ($menu->has_children == true && count($menu->children) > 0)
            {
                foreach($menu->children as $menu_id)
                {
                    print_sublevel($menu_id, 1, $menus);
                }
            }
        }
		?>
    </select>
    </td>
  </tr>
  <tr>
    <td width="50%">Order :</td>
    <td width="50%"><input type="text" size="3" name="order" class="required"></td>
  </tr>
  <tr>
    <td width="50%">Option :</td>
    <td width="50%"><input type="text" size="20" name="m_option" class="required"></td>
  </tr>
  <tr>
    <td width="50%">View :</td>
    <td width="50%"><input type="text" size="20" name="m_view"></td>
  </tr>
  <tr>
    <td width="50%">Task :</td>
    <td width="50%"><input type="text" size="20" name="m_task"></td>
  </tr>
  <tr>
    <td width="50%">Additional Parameters :</td>
    <td width="50%"><input type="text" size="20" name="additional_params"></td>
  </tr>
  <tr>
    <td width="50%">Direct Link :</td>
    <td width="50%"><input type="text" size="20" name="direct_link"></td>
  </tr>
  <tr>
    <td width="50%">Target:</td>
    <td width="50%"><input type="text" size="20" name="target"></td>
  </tr>
</table>
<br />
<input type="submit" name="submit" value="Save Menu">
<button onclick="history.go(-1); return false;">Cancel</button>
</form>