<?php
jimport( 'joomla.application.component.view');

class TwoLevelMenuViewUsergroups extends JViewLegacy
{
    function display($tpl = null)
    {
        Functions::ifNotLoginRedirect();
		$db= JFactory::getDBO();
		
		$query = "select dg.*, ec.category_name from #__designations dg, #__employee_categories ec where dg.category_id=ec.id order by category_name, designation_name";
		$db->setQuery($query);
		$usergroups = $db->loadObjectList();

        $this->assignRef("usergroups" , $usergroups);
        parent::display($tpl);
    }
}
?>
