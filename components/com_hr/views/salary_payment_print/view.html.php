<?php
jimport('joomla.application.component.view');

class HrViewSalary_payment_print extends JViewLegacy
{
    function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db= JFactory::getDBO();
        
        $document = JFactory::getDocument();
        $document->setTitle("Salary Payment Print");
        
        $payment_id = intval(JRequest::getVar("payment_id"));
        
        $query = "select id payment_id, salary_month, salary_year, payment_date from `#__hr_salary_payments` where id=" . $payment_id;
        $db->setQuery($query);
        $data = $db->loadObject();
        $this->data = $data;
        
        $query = "select p.*, e.employee_name, e.id employee_id, l.location_name, b.bank_name from `#__hr_salary_payment_items` p inner join `#__hr_employees` e on p.employee_id=e.id inner join `#__inventory_locations` l on e.location_id=l.id left join `#__banks` b on p.instrument_bank=b.id where p.payment_id=" . $payment_id . " order by e.employee_name";
        $db->setQuery($query);
        $salary_payments = $db->loadObjectList();  
        $this->salary_payments = $salary_payments;
        
        parent::display($tpl);
    }
}
?>