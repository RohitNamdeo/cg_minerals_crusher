<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class HrViewEmployee_management extends JViewLegacy
{
	public function display($tpl = null)
	{
        // view for employee management
        // add/edit option
        // account can be deactivated
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "employee_management"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
		$db = JFactory::getDBO();
        $document = JFactory::getDocument();
        $document->setTitle( "Employee Management" );
        
        $location_id = intval(JRequest::getVar("location_id"));
        $employee_id = intval(JRequest::getVar("employee_id"));
        $account_status = intval(JRequest::getVar("account_status"));
        
        if($account_status == 0)
        {
            $account_status = AC_ACTIVE;
        }
        
        $condition = "(e.account_status=" . $account_status . ")"; 
        
        if($location_id > 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(e.location_id=" . $location_id . ")";
        }
        
        if($employee_id > 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(e.id=" . $employee_id . ")";
        }
        
		$query = "select e.*, l.location_name from `#__hr_employees` e inner join `#__inventory_locations` l on e.location_id=l.id where " . $condition . " order by e.employee_name";
        $db->setQuery( $query );
        $employees = $db->loadObjectList();
        
        $limit = 100;
        $total = count($employees);
        $limit = JRequest::getVar('limit',$limit, '', 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $db->setQuery( $query, $limitstart, $limit );
        $employees = $db->loadObjectlist();
        $this->employees  = $employees;
        
        $this->pagination = $pagination;
        $this->total = $total;
        $this->limit = $limit;        
        $this->limitstart = $limitstart;
        
        $employee_names = array();
        $locations = array();
        $location = array();
        
        $x = 1;
        foreach($employees as $key => $employee)
        {
            $employee_names[] = array("id"=>$employee->id, "employee_name"=>$employee->employee_name);
            
            if(!in_array($employee->location_id, $location))
            {
                $location[] = $employee->location_id;
                $locations[] = array("id"=>$employee->location_id, "location_name"=>$employee->location_name);
            }
        }
        
        $this->employee_names = $employee_names;
        $this->locations = $locations;
        $this->location_id = $location_id;
        $this->employee_id = $employee_id;
        $this->account_status = $account_status;
            
		parent::display($tpl);
	}
}
?>