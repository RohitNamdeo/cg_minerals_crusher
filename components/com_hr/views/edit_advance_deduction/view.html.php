<?php
jimport( 'joomla.application.component.view');

class HrViewEdit_advance_deduction extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * view to edit advance deduction in one's salary
        * multiple advances can be deducted in salary
        * if the employee does not want to deduct salary then this deduction can be edited or removed from his salary
        */
        
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        $document = JFactory::getDocument();
        $document->setTitle("Edit Advance Deduction");
        
        $salary_id = intval(JRequest::getVar("salary_id"));
        
        $query = "select s.employee_id, s.salary_month, s.salary_year, s.advance_deduction, s.total_salary, s.paid_salary, e.employee_name, l.location_name from `#__hr_salary` s inner join `#__hr_employees` e on s.employee_id=e.id inner join `#__inventory_locations` l on e.location_id=l.id where s.id=" . $salary_id;
        $db->setQuery($query);
        $salary = $db->loadObject();
        
        if(is_admin() && floatval($salary->paid_salary) == 0 && floatval($salary->advance_deduction) > 0)
        {
            $query = "select si.*, si.id item_id, a.amount advance_amount from `#__hr_salary_items` si inner join `#__hr_advance_salary_payments` a on si.advance_id=a.id where si.salary_id=" . $salary_id;
            $db->setquery($query);
            $advances = $db->loadObjectlist();
        }
        else
        {
            echo "Unable to edit advance deduction.";
            return;
        }
        
        $this->salary_id = $salary_id;
        $this->salary = $salary;
        $this->advances = $advances;
        
        parent::display($tpl);
    }
}
?>