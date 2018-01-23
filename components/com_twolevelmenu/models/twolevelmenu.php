<?php
class TwolevelmenuModelTwolevelmenu extends JModelLegacy
{
    function create_menu()
    {
        $db = JFactory::getDBO();
        
        $name = JRequest::getVar("name");
        $parent = JRequest::getVar("parent");
        $order = JRequest::getVar("order");
        $option = JRequest::getVar("m_option");
        $view = JRequest::getVar("m_view");
        $task = JRequest::getVar("m_task");
        $additional_params = JRequest::getVar("additional_params");
        $direct_link = JRequest::getVar("direct_link");
        $target = JRequest::getVar("target");
		
		$query = "insert into #__menuitems(`name`,`parent`,`order`,`option`,`view`,`task`, `additional_params`,`direct_link`,`target`) values('" . $name . "', '" . $parent . "', '" . $order . "', '" . $option . "', '" . $view . "', '" . $task . "', '" . $additional_params . "', '" . $direct_link . "', '" . $target . "')";
		$db->setQuery($query);
		$db->query();
		
		return "Menu created.";
    }
    function update_menu()
    {
        $db = JFactory::getDBO();
        
        $menuitem_id=JRequest::getVar("mi");
        $name = JRequest::getVar("name");
        $parent = JRequest::getVar("parent");
        $order = JRequest::getVar("order");
        $option = JRequest::getVar("m_option");
        $view = JRequest::getVar("m_view");
        $task = JRequest::getVar("m_task");
        $additional_params = JRequest::getVar("additional_params");
        $direct_link = JRequest::getVar("direct_link");
        $target = JRequest::getVar("target");
		
		$query = "delete from #__menuitems where id=" . $menuitem_id;
		$db->setQuery($query);
		$db->query();
		
		$query = "insert into #__menuitems(`id`, `name`,`parent`,`order`,`option`,`view`,`task`, `additional_params`,`direct_link`,`target`) values('" . $menuitem_id . "', '" . $name . "', '" . $parent . "', '" . $order . "', '" . $option . "', '" . $view . "', '" . $task . "', '" . $additional_params . "', '" . $direct_link . "', '" . $target . "')";
		$db->setQuery($query);
		$db->query();
		
		return "Menu updated.";
    }
    
    function delete_menu()
    {
        $db = JFactory::getDBO();
        
        $menuitem_id=JRequest::getVar("mi");
        $query = "delete from #__menuitems where id=" . $menuitem_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from #__menuitems where parent=" . $menuitem_id;
        $db->setQuery($query);
        $db->query();
        
		return "Menu deleted.";
    }
    
    function assign_permits()
    {
		$db = JFactory::getDBO();
		$usergroup_id=JRequest::getVar("g");
		$menu_permits = JRequest::getVar("menu_permit");
		
		$query = "delete from #__menu_access_permits where group_id=" . $usergroup_id;
		$db->setQuery($query);
		$db->query();			

		foreach($menu_permits as $menu_permit)
		{
			$query = "insert into #__menu_access_permits(`group_id`,`menu_id`,`permit`) values(" . $usergroup_id . ", " . $menu_permit . ", 1)";
			$db->setQuery($query);
			$db->query();			
		}
		return "Permissions Assigned Successfully.";
    }
}
?>