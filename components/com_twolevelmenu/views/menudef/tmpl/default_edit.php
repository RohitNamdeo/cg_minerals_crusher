<?php defined('_JEXEC') or die('Restricted access'); ?>
<h1>Menu Definition</h1>
<form method="post" action="index.php?option=com_twolevelmenu&task=update_menu">
<table class="clean">
  <tr>
    <td width="50%">Name :</td>
    <td width="50%"><input type="text" size="20" name="name" class="required" value="<? echo $this->menu->name; ?>"></td>
  </tr>
  <tr>
    <td width="50%">Parent :</td>
    <td width="50%">
        <select name="parent" class="required">
            <option value="0">None</option>
            <?
            global $menu_parent_id;
            $menu_parent_id = $this->menu->parent;
            foreach($this->menus as $menu)
            {
                $menu = (object) $menu;
                if ($menu->parent == 0 )
                {
                    ?>
                        <option value="<? echo $menu->id; ?>" <? echo ($menu->id == $menu_parent_id ? "selected='selected'" : ""); ?> ><? echo $menu->name; ?></option>
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
                global $menu_parent_id;
                $menu = $menus[$menu_id];
                $menu = (object) $menu;
                ?>
                    <option value="<? echo $menu->id; ?>" <? echo ($menu->id == $menu_parent_id ? "selected='selected'" : ""); ?> ><? echo str_repeat("&nbsp;", $level * 2). " - " . $menu->name; ?></option>
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
    <td width="50%"><input type="text" size="3" name="order" class="required" value="<? echo $this->menu->order; ?>"></td>
  </tr>
  <tr>
    <td width="50%">Option :</td>
    <td width="50%"><input type="text" size="20" name="m_option" class="required" value="<? echo $this->menu->option; ?>"></td>
  </tr>
  <tr>
    <td width="50%">View :</td>
    <td width="50%"><input type="text" size="20" name="m_view" value="<? echo $this->menu->view; ?>"></td>
  </tr>
  <tr>
    <td width="50%">Task :</td>
    <td width="50%"><input type="text" size="20" name="m_task" value="<? echo $this->menu->task; ?>"></td>
  </tr>
  <tr>
    <td width="50%">Additional Parameters :</td>
    <td width="50%"><input type="text" size="20" name="additional_params" value="<? echo $this->menu->additional_params; ?>"></td>
  </tr>
  <tr>
    <td width="50%">Direct Link :</td>
    <td width="50%"><input type="text" size="20" name="direct_link" value="<? echo $this->menu->direct_link; ?>"></td>
  </tr>
  <tr>
    <td width="50%">Target:</td>
    <td width="50%"><input type="text" size="20" name="target" value="<? echo $this->menu->target; ?>"></td>
  </tr>
</table>
<br />
<input type="hidden" name="mi" value="<? echo $this->menuitem_id; ?>">
<input type="submit" name="submit" value="Save Menu">
<button onclick="history.go(-1); return false;">Cancel</button>
</form>