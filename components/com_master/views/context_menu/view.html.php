<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewContext_menu extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Context Menu");
        
        parent::display($tpl);
    } 
}
?>