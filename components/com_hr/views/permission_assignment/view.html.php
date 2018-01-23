<?php
jimport("joomla.application.component.view");
class HrViewPermission_assignment extends JViewLegacy
{
    function display($tpl = null)
    {
        // for permission of roles
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument();
        $document->setTitle("Permission Assignment");
        
        $designation_id = intval(JRequest::getVar("designation_id"));
        $this->designation_id = $designation_id;
        
        $query = "select mi.*, map.permit from #__menuitems mi left join #__designation_access_permits map on map.menu_id=mi.id order by `parent`,`order`";
        $db->setQuery($query);
        $menu_rows = $db->loadObjectList();

        $menus = array();
        if(is_array($menu_rows))
        {
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
        }
        $this->menus = $menus;
        
        $permissions = array();
               
        $query = "select dap.* from #__designation_access_permits dap left join #__designations d on dap.designation_id=d.id where dap.designation_id = " . $designation_id;
        $db->setQuery($query);
        $user_permits = $db->loadObjectList();
      
        if(count($user_permits)<>"")
        {
            foreach($user_permits as $user_permit)
            {
              $permissions[$user_permit->menu_id] = $user_permit->permit;
            }
        }
        
        $this->user_permits = $user_permits;
        $this->permissions = $permissions;
        
        $query = "select `designation_name` from #__designations where id=" . $designation_id;
        $db->setQuery($query);
        $designation_name = $db->loadResult();
            
        $this->designation_name = $designation_name;
        parent::display($tpl);
    }
} 
?>
