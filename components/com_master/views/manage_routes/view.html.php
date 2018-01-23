<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewmanage_routes extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * View for add/edit/delete/list display of cities
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        //if (!Functions::has_permissions("master", "manage_cities"))
//        {
//            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
//            return;
//        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Routes");
        
        $query = "select * from #__routes order by route_name";
        $db->setQuery($query);
        $routes = $db->loadObjectList();
                
        $this->assignRef('routes' , $routes);
        
        parent::display($tpl);
    } 
}
?>