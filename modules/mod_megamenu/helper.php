<?php

defined('_JEXEC') or die;
class modMegaMenuHelper
{
    static function set_parent_active($menu_id, &$menus)
    {
        $menus[$menu_id]["active"] = 1;
        if ($menus[$menu_id]["parent"] > 0)
        {
            self::set_parent_active($menus[$menu_id]["parent"], $menus);
        }
        return;
    }
    
    static function getMenu()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        if ($user->guest)
            return;
        $document = JFactory::getDocument();
        $user_id= $user->get("id");
        if ($user_id == 0)
            $user_id = -1;

      
        $NavigationID = JRequest::getVar("NavID");

        $query = "select designation_id from #__employeedetails where user_id=" . $user_id;
        $db->setQuery($query);
        $designation_id = intval($db->loadResult());

        $query = "select mi.*,dap.permit from #__menuitems mi left join (select * from #__designation_access_permits where designation_id=" . $designation_id . ") dap on mi.id=dap.menu_id where (mi.id is not null) order by `parent`, `order`";
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
                                            "order" => $menu_row->order,
                                            "option" => $menu_row->option,
                                            "view" => $menu_row->view,
                                            "task" => $menu_row->task,
                                            "additional_params" => $menu_row->additional_params,
                                            "direct_link" => $menu_row->direct_link,
                                            "target" => $menu_row->target,
                                            "children" => array(),
                                            "has_children" => false,
                                            "permit" => $menu_row->permit,
                                            "active" => false);
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
                $menus[$menu_row->id]["order"] = $menu_row->order;
                $menus[$menu_row->id]["option"] = $menu_row->option;
                $menus[$menu_row->id]["view"] = $menu_row->view;
                $menus[$menu_row->id]["task"] = $menu_row->task;
                $menus[$menu_row->id]["additional_params"] = $menu_row->additional_params;
                $menus[$menu_row->id]["direct_link"] = $menu_row->direct_link;
                $menus[$menu_row->id]["target"] = $menu_row->target;
                $menus[$menu_row->id]["permit"] = $menu_row->permit;
                $menus[$menu_row->id]["active"] = false;
            }
        }
        
        $query = "select mi.*,eap.permit from #__menuitems mi, #__employee_access_permits eap where mi.id=eap.menu_id and eap.user_id=" . $user_id . " order by `parent`, `order`";
        $db->setQuery($query);
        $menu_rows = $db->loadObjectList();
        foreach ($menu_rows as $menu_row)
        {
            if ($menu_row->permit >=0 )
            {
                $menus[$menu_row->id]["permit"] = $menus[$menu_row->id]["permit"] || $menu_row->permit;
            }
        }
        foreach($menus as $key => $menu)
        {
            $menu = (object) $menu;
            if ($user_id == 506)
            {
                $menu->permit = 1;
                $menus[$menu->id]["permit"] = 1;
            }
            if ($menu->permit == 0)
            {
                unset($menus[$key]);
            }
        }
        if (isset($menus[$NavigationID]))
        {
            if ($menus[$NavigationID])
            {
                $menus[$NavigationID]["active"] = 1;
                if ($menus[$NavigationID]["parent"] > 0)
                {
                    self::set_parent_active($menus[$NavigationID]["parent"], $menus);
                }
            }
        }
        return $menus;
    }
}