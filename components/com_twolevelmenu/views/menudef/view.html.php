<?php
jimport( 'joomla.application.component.view');

class TwoLevelMenuViewMenuDef extends JViewLegacy
{
    function display($tpl = null)
    {
        Functions::ifNotLoginRedirect();
		$db= JFactory::getDBO();
		
		$mode = JRequest::getVar("m");
		$editmode = false;
		
		$menuitem_id = JRequest::getVar("mi");
		
        $query = "select mi.* from #__menuitems mi order by `parent`, `order`";
        $db->setQuery($query);
        $menu_rows = $db->loadObjectList();
        
        $menus = array();
        foreach ($menu_rows as $menu_row)
        {
            if (!isset($menus[$menu_row->id]))
            {
                $menus[$menu_row->id] = array(
                                            "id" => $menu_row->id,
                                            "name" => $menu_row->name,
                                            "parent" => $menu_row->parent,
                                            "children" => array(),
                                            "has_children" => false);
                if ($menu_row->parent > 0)
                {
                    if (!isset($menus[$menu_row->parent]))
                    {
                        $menus[$menu_row->parent] = array();
                        $menus[$menu_row->parent]["id"] = $menu_row->parent;
                        $menus[$menu_row->parent]["children"] = array();
                    }
                        $menus[$menu_row->parent]["children"][] = $menu_row->id;
                        $menus[$menu_row->parent]["has_children"] = true;
                }
            }
            else
            {
                $menus[$menu_row->id]["id"] = $menu_row->id;
                $menus[$menu_row->id]["name"] = $menu_row->name;
                $menus[$menu_row->id]["parent"] = $menu_row->parent;
            }
        }

        $this->assignRef("menus" , $menus);
        
        if ($mode == "e" && $menuitem_id != "" && is_numeric($menuitem_id) )
        {
			$query = "select * from #__menuitems mi where id=" . $menuitem_id;
			$db->setQuery($query);
			$menu = $db->loadObject();
			$this->assignRef("menu", $menu);
			$this->assignRef("menuitem_id", $menuitem_id);
			$editmode = true;
        }
        if ($editmode)
        	parent::display("edit");
		else
			parent::display($tpl);
    }
}
?>
