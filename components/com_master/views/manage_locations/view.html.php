<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewmanage_locations extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * View for add/edit/delete/list display of locations
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "manage_locations"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Locations");
        
        $query = "select * from `#__inventory_locations` order by location_name";
        $db->setQuery($query);
        $locations = $db->loadObjectList();                
        $this->locations = $locations;
        
        parent::display($tpl);
    } 
}
?>