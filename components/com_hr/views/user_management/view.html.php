<?php
jimport( 'joomla.application.component.view');
class HrViewUser_management extends JViewLegacy
{
    function display($tpl = null)
    {
        // software users
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "user_management"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDBO();
        $current_user_id = intval(JFactory::getUser()->id);
        
        $document = JFactory::getDocument();
        $document->setTitle("User Management");
        $condition = "";
        
        $user_status = intval(JRequest::getVar("user_status"));
                
        if($user_status == "0")
        {
            $condition = "(u.password<>'' or u.password='')";    
        }
        if($user_status == U_ACTIVE)
        {
            $condition = " u.password<>''";
        }
        if($user_status == U_DISABLED)
        {
            $condition = " u.password=''";
        }
        
        $query = "select e.* , u.username, u.email, dg.designation_name, (CASE u.password WHEN '' THEN " . U_DISABLED . " ELSE " . U_ACTIVE . " END) user_status from `#__employeedetails` e inner join `#__users` u on e.user_id=u.id inner join `#__designations` dg on e.designation_id=dg.id where u.block=0 " . ($condition != "" ? " and " . $condition : "") . " order by e.first_name, e.last_name";
        $db->setQuery($query);
        $employees = $db->loadObjectList();
        
        $this->employees = $employees;
        $this->user_status = $user_status;
        
        parent::display($tpl);        
    }
}
?>
