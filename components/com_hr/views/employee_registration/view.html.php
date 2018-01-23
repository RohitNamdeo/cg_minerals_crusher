<?php
jimport( 'joomla.application.component.view');

class HrViewEmployee_registration extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * view for add/edit of employee
        * salary is defined as gross salary, no salary structure as other HR s/w
        * Employee is on machine attendance
        * Non-machine attendance does not exists
        */
        
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
                
        $document = JFactory::getDocument();
        $document->setTitle("Employee Registration");
        
        $mode = JRequest::getVar("m");
        
        $query = "select * from `#__inventory_locations` order by `location_name`";
        $db->setQuery($query);
        $locations = $db->loadObjectList();
        $this->locations = $locations;
        
        $query = "select * from `#__hr_devices`";
        $db->setQuery($query);
        $devices = $db->loadObjectList();
        $this->devices = $devices;
        
        if($mode == 'e')
        {
            $employee_id = intval(JRequest::getVar("employee_id"));
            $this->employee_id = $employee_id;
            
            $query = "select * from `#__hr_employees` where id=" . $employee_id;
            $db->setQuery($query);
            $employee = $db->loadObject();
            $this->employee = $employee;
            
            parent::display("edit");
        }
        else
        {
            parent::display($tpl);
        }
	}
}
?>