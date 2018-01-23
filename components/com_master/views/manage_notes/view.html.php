<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewManage_notes extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * View for add/edit/delete/list display of notes
        * just like notepad -> notes can be specific or general
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "manage_notes"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Notepad");    
        
        $query = "select * from `#__notes` where deleted=0 order by note_type, date_of_note";
        $db->setQuery($query);
        $notes = $db->loadObjectList();
        $this->notes = $notes;
        
        parent::display($tpl);
    } 
}
?>