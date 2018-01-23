<?php
jimport( 'joomla.application.component.view');
class TwolevelmenuViewDashboard extends JViewLegacy
{
    function display($tpl = null)
    {
        if (Functions::ifNotLoginRedirect("index.php?option=com_twolevelmenu&view=dashboard"))
        {
            return false;
        }
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        
        $document = JFactory::getDocument();
        $user_id= $user->get("id");
        if (!$user_id > 0)
        {
            return false;
        }
        $query = "select designation_id from #__employeedetails where user_id=" . $user_id;
        $db->setQuery($query);
        $designation_id = $db->loadResult();
        if (intval($designation_id) == 0)
        {
            return false;
        }
        $query = "select mi.*,dap.permit from #__menuitems mi left join (select * from #__designation_access_permits where designation_id=" . $designation_id . ") dap on mi.id=dap.menu_id order by `parent`, `order`";
        $db->setQuery($query);
        $menu_rows = $db->loadObjectList();
        $menus = array();
        echo $db->getErrorMsg();
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
        
        $query = "select mi.*,eap.permit from #__menuitems mi, #__employee_access_permits eap where mi.id=eap.menu_id and eap.user_id=" . $user_id . " order by `parent`, `order`";
        $db->setQuery($query);
        $menu_rows = $db->loadObjectList();
        foreach ($menu_rows as $menu_row)
        {
            if ($menu_row->permit != "-1" && $menu_row->parent > 0)
            {
                $menus[$menu_row->parent]["children"][$menu_row->id]["permit"] = $menu_row->permit;
            }
            elseif($menu_row->permit == "0")
            {
                $menus[$menu_row->id]["permit"] = $menu_row->permit;
            }
        }
        $this->assignRef("menus", $menus);
        parent::display($tpl);        
    }
}
?>