<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

class HrViewEmployee_account extends JViewLegacy
{
	function display($tpl = null)
	{
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
		$db = JFactory::getDBO();
		$document = JFactory::getDocument();
        $document->setTitle( "Employee Account" );

		$employee_id = intval(JRequest::getVar("employee_id"));
		$this->employee_id = $employee_id;
		
		$query = "select e.*, l.location_name, d.name machine_name from `#__hr_employees` e inner join `#__inventory_locations` l on e.location_id=l.id inner join `#__hr_devices` d on e.machine_id=d.id where e.id=" .$employee_id;
		$db->setQuery($query);
		$employee = $db->loadObject();
        $this->employee = $employee;
        
		parent::display($tpl);
	}
}
?>