<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewManage_banks extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * View for add/edit/delete/list display of banks
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "manage_banks"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Banks"); 
        
        $query = "select * from `#__banks` order by `bank_name`";
        $db->setQuery($query);
        $banks = $db->loadObjectList(); 
                
        $this->assignRef("banks", $banks);
                
        parent::display($tpl);
    } 
}
?>