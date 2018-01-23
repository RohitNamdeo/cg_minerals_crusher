<?php
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

class MasterViewActivityLog extends JViewLegacy
{
	function display($tpl = null)
	{
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "activitylog"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDBO();
        
        $document = JFactory::getDocument()->setTitle("Activity Log");
        
        $current_page = intval(JRequest::getVar("p"));
        if ($current_page == 0)
        {
            $current_page = 1;
        }
        
        $from = JRequest::getVar("from");
        $to = JRequest::getVar("to");
        if ($from == "")
        {
            $from = date("01-M-Y");
        }
        if ($to == "")
        {
            $to = date("d-M-Y");
        }
        $from = date("d-M-Y", strtotime($from));
        $to = date("d-M-Y", strtotime($to));
        
        $this->assignRef("from", $from);
        $this->assignRef("to", $to);
        
        $employee_id = intval(JRequest::getVar('emp_id'));
        $this->assignRef("employee_id", $employee_id);
        
        $user_id = "";
        if($employee_id > 0 )
        {
            $user_id = " and al.user_id=" . $employee_id . "";
        }
        else $user_id="";
        
        $this->assignRef('employee_id' ,$employee_id);
        
        $query ="select count(*) from #__activity_log al left join #__employeedetails empd on al.user_id=empd.user_id where al.timestamp between '" . date("Y-m-d", strtotime($from)) . "' and '" . date("Y-m-d", strtotime($to)) . " 23:59:59' " . $user_id . " ";                
        $db->setQuery($query);                 
        $pages = ceil(intval($db->loadResult()) / 100);
        
        $this->assignRef("current_page", $current_page);
        $this->assignRef("pages" , $pages);               
        
        $query = "select al.*,concat(empd.first_name,' ',empd.last_name) user_name from #__activity_log al left join #__employeedetails empd on al.user_id=empd.user_id where al.timestamp between '" . date("Y-m-d", strtotime($from)) . "' and '" . date("Y-m-d", strtotime($to)) . " 23:59:59' " . $user_id . " order by al.id DESC limit " . (($current_page - 1) * 100) . ", 100";
        $db->setQuery($query);
        $activity_logs = $db->loadObjectList();
        $this->assignRef('activity_logs' , $activity_logs);
        
//        all employees are shown : as confused whether blocked or unblocked employees to be shown
        $query = "select user_id,concat(first_name,' ',last_name) user_name from #__employeedetails order by first_name,last_name";
        $db->setQuery($query);
        $employees = $db->loadObjectList();
        $this->assignRef('employees' , $employees);
        
		parent::display($tpl);
	} 
}
?>