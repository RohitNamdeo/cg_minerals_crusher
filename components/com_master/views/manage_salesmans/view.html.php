<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewmanage_salesmans extends JViewLegacy
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
        $document = JFactory::getDocument()->setTitle("Salesmans");
        
        $query = "select * from #__salesmans order by salesman_name";
        $db->setQuery($query);
        $salesmans = $db->loadObjectList();
                
        $this->assignRef('salesmans' , $salesmans);
        
        parent::display($tpl);
    } 
}
?>