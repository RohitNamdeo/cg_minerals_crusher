<?php
jimport( 'joomla.application.component.view');
class HrViewRole_management extends JViewLegacy
{
    function display($tpl = null)
    {
        // roles are the designations
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "role_management"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDBO();
        
        $document = JFactory::getDocument();
        $document->setTitle("Role Management");
        
        $query = "select * from `#__designations` order by `designation_name`";
        $db->setQuery($query);
        $designations = $db->loadObjectList();
        
        $this->designations = $designations;
        
        parent::display($tpl);        
    }
}
?>