<?php
jimport( 'joomla.application.component.view');

class HrViewDelete_salary extends JViewLegacy
{
    function display($tpl = null)
    {
        // view to delete generated salary
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("hr", "delete_salary"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $document = JFactory::getDocument();
        $document->setTitle( "Delete Salary" );
        
        $month = intval(JRequest::getVar('month'));
        $year = intval(JRequest::getVar('year'));
        
        $this->month = $month;
        $this->year = $year;
        
        parent::display($tpl);
    }
}
?>