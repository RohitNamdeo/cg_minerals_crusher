<?php
jimport( 'joomla.application.component.view');

class HrViewGenerate_salary extends JViewLegacy
{
    function display($tpl = null)
    {
        // view to generate salary
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "generate_salary"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        $document = JFactory::getDocument();
        $document->setTitle("Generate Salary");
        
        $month = intval(JRequest::getVar("month"));
        $year = intval(JRequest::getVar("year"));
        
        
        if($month == 0 || $year == 0)
        {
            $query = "select max(salary_year) from `#__hr_salary`";
            $db->setQuery($query);
            $year = intval($db->loadResult());
            
            if($year != 0)
            {
                $query = "select max(salary_month) from `#__hr_salary` where salary_year=" . $year;
                $db->setQuery($query);
                $month = intval($db->loadResult());
            }
            else
            {
                $previous_month = date("Y-m", strtotime("-1 month", strtotime(date("Y-m-d"))));
                $month = date("m", strtotime($previous_month));
                $year = date("Y", strtotime($previous_month));
            }
        }
        
        $this->month = $month;
        $this->year = $year;
        
        $query = "select count(id) from `#__hr_salary` where salary_month=" . $month . " and salary_year=" . $year;
        $db->setQuery($query);
        $this->salary_generated = (intval($db->loadResult()) > 0 ? YES : NO);
        
        parent::display($tpl);
    }
}
?>