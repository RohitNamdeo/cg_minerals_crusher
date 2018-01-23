<?php
jimport( 'joomla.application.component.view');

class HrViewAdvance_payment_report extends JViewLegacy
{
    function display($tpl = null)
    {
        $db = JFactory::getDBO();
        
        /*
        * advance payment report
        * record can be edited/deleted only if amount cleared against that advance is 0
        * 
        */
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "advance_payment_report"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $document = JFactory::getDocument();
        $document->setTitle("Advance Payment Report");
        
        $employee_id = intval(JRequest::getVar("employee_id"));
        $location_id = intval(JRequest::getVar("location_id"));
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        
        $this->employee_id = $employee_id;
        $this->location_id = $location_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        $condition = "";
        
        if($employee_id != 0)
        { $condition .= ($condition != "" ? " and " : "" ) . "(a.employee_id=" . $employee_id . ")"; }
        if($location_id != 0)
        { $condition .= ($condition != "" ? " and " : "" ) . "(e.location_id=" . $location_id . ")"; }
        
        if($from_date != "" && $to_date != "")
        {
            $from_date = date("Y-m-d", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));
            
            $condition .= ($condition != "" ? " and " : "" ) . "(a.payment_date between '" . $from_date . "' and '" . $to_date . "')";
        }
        
        $query = "select a.*, e.employee_name, e.id employee_id, l.location_name, b.bank_name from `#__hr_advance_salary_payments` a inner join `#__hr_employees` e on a.employee_id=e.id inner join `#__inventory_locations` l on e.location_id=l.id left join `#__banks` b on a.instrument_bank=b.id " . ($condition != "" ? " where " . $condition : "") . " order by a.payment_date desc, e.employee_name";
        $db->setQuery($query);
        $advance_payments = $db->loadObjectList(); 
        $this->advance_payments = $advance_payments;
        
        $query = "select * from `#__inventory_locations` order by `location_name`"; 
        $db->setQuery($query); 
        $locations = $db->loadObjectList();
        $this->locations = $locations;
        
        $query = "select id, employee_name from `#__hr_employees` order by employee_name"; 
        $db->setQuery($query); 
        $employees = $db->loadObjectList();
        $this->employees = $employees;

        parent::display($tpl);
    }
}
?>