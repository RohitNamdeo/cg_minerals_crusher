<?php
jimport( 'joomla.application.component.view');
class HrViewDesignations extends JViewLegacy
{
    function display($tpl = null)
    {
        // Roles for software users, not for employees
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "designations"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDBO();
        
        $document = JFactory::getDocument();
        $document->setTitle("Designation Manager");
        
        $query = "select * from `#__designations` order by designation_name";
        $db->setQuery($query);
        $designations = $db->loadObjectList();
        $this->designations = $designations;
        
        parent::display($tpl);        
    }
}
?>