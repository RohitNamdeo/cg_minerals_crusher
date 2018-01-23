<?php
jimport( 'joomla.application.component.view');

class TwoLevelMenuViewPermitAssignment extends JViewLegacy
{
    function display($tpl = null)
    {
        Functions::ifNotLoginRedirect();
		$db= JFactory::getDBO();
		$usergroup_id = JRequest::getVar("g");
		
		if ($usergroup_id == "")
			return;
		
		$query = "select mi.*, map.permit from #__menuitems mi left join (select * from #__menu_access_permits where group_id=" . $usergroup_id . ") map on mi.id=map.menu_id order by `parent`, `order`";
		$db->setQuery($query);
		$menu_rows = $db->loadObjectList();
		$menus = array();
		//echo $db->getErrorMsg();
		foreach ($menu_rows as $menu_row)
		{
			if($menu_row->parent == 0)
				$menus[$menu_row->id] = array("id" => $menu_row->id, "name" => $menu_row->name, "parent" => $menu_row->parent, "order" => $menu_row->order, "option" => $menu_row->option, "view" => $menu_row->view, "task" => $menu_row->task, "additional_params" => $menu_row->additional_params, "direct_link" => $menu_row->direct_link, "target" => $menu_row->target, "children" => array(), "has_children" => false, "permit" => $menu_row->permit);
			else
			{
				$menus[$menu_row->parent]["children"][$menu_row->id] = array("id" => $menu_row->id, "name" => $menu_row->name, "parent" => $menu_row->parent, "order" => $menu_row->order, "option" => $menu_row->option, "view" => $menu_row->view, "task" => $menu_row->task, "additional_params" => $menu_row->additional_params, "direct_link" => $menu_row->direct_link, "target" => $menu_row->target, "children" => array(), "has_children" => false, "permit" => $menu_row->permit);
				$menus[$menu_row->parent]["has_children"] = true;
			}
		}
        $this->assignRef("menus" , $menus);
        $this->assignRef("usergroup_id" , $usergroup_id);
        parent::display($tpl);
    }
}
?>