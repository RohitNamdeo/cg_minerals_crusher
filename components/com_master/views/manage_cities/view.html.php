<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewmanage_cities extends JViewLegacy
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
        
        if (!Functions::has_permissions("master", "manage_cities"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Cities");
        
        $query = "select c.*,c.id city_id,s.name state_name from #__cities c left join #__states s on c.state_id=s.id order by state_name, c.city";
        $db->setQuery($query);
        $cities = $db->loadObjectList();
                
        $this->assignRef('cities' , $cities);
        
        $query = "select * from `#__states` order by name";
        $db->setQuery($query);
        $states = $db->loadObjectList();
                
        $this->assignRef('states' , $states);
        
        parent::display($tpl);
    } 
}
?>