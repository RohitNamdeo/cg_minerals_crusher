<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewmanage_units extends JViewLegacy
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
        
        if (!Functions::has_permissions("master", "manage_units"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Units");
        
        $query = "SELECT * FROM `jos_units`";
        $db->setQuery($query);
        $units = $db->loadObjectList();
                
        $this->assignRef('units' , $units);
        
        
        parent::display($tpl);
    } 
}
?>