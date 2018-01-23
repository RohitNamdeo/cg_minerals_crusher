<?php
jimport( 'joomla.application.component.view');

class HrViewUser_registration extends JViewLegacy
{
    function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDBO();
        $mode = JRequest::getVar("m");
        $employee_id = intval(JRequest::getVar("e"));
        $user_id = intval(JFactory::getUser()->id);
        
        JFactory::getDocument()->setTitle("User Registration");
        
        $menu_permit = JRequest::getVar("menu_permit");
        $employee_exists = false;
        
        if ($employee_id <> "")
        {                 
            $query = "select e.*, u.email from `#__employeedetails` e inner join `#__users` u on e.user_id=u.id where e.id=" . $employee_id;
            $db->setQuery($query);
            $employee = $db->loadObject();
            
            if(is_object($employee))
            {
                $employee_exists = true;
            }
            $this->employee = $employee;
        }
                            
        $query = "select mi.*, map.permit from `#__menuitems` mi left join (select * from `#__employee_access_permits` ) map on mi.id=map.menu_id " . ($employee_exists == false ? ""  :  " and map.user_id=" . intval($employee->user_id) . " "). " order by `parent`,`order`";
        $db->setQuery($query);
        $menu_rows = $db->loadObjectList();

        $menus = array();

        foreach ($menu_rows as $menu_row)
        {
            if($menu_row->parent == 0)
            {
                $menus[$menu_row->id] = array("id" => $menu_row->id, "name" => $menu_row->name, "parent" => $menu_row->parent, "order" => $menu_row->order, "option" => $menu_row->option, "view" => $menu_row->view, "task" => $menu_row->task, "additional_params" => $menu_row->additional_params, "direct_link" => $menu_row->direct_link, "target" => $menu_row->target, "children" => array(), "has_children" => false, "permit" => $menu_row->permit);                
            }
            else
            {
                $menus[$menu_row->parent]["children"][$menu_row->id] = array("id" => $menu_row->id, "name" => $menu_row->name, "parent" => $menu_row->parent, "order" => $menu_row->order, "option" => $menu_row->option, "view" => $menu_row->view, "task" => $menu_row->task, "additional_params" => $menu_row->additional_params, "direct_link" => $menu_row->direct_link, "target" => $menu_row->target, "children" => array(), "has_children" => false, "permit" => $menu_row->permit);
                $menus[$menu_row->parent]["has_children"] = true;
            }
        }
        $this->menus = $menus;
        $this->menu_permit = $menu_permit;
        $permissions = array();
        
        $query = "select eap.* from `#__employee_access_permits` eap left join `#__employeedetails` ed on eap.user_id=ed.user_id " . ($employee_id != 0 ? " where ed.id=" . $employee_id : "");
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

        $query = "select * from `#__designations` order by `designation_name`";
        $db->setQuery($query);
        $designations = $db->loadObjectList();
        
        $this->designations = $designations;
        $this->employee_id = $employee_id;

        if ($mode == "e" && $employee_exists) 
        {        
            parent::display("edit");
		}
        else
        {
            parent::display($tpl);            
        }
	}
}
?>