<?php
jimport( 'joomla.application.component.view');

class AmittradingViewCash_expense_entry extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * view to add cash expense entry
        */
        
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "cash_expense_entry"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $document = JFactory::getDocument();
        $document->setTitle( "Cash Expense Entry" );
        
        $query = "select * from #__expense_head order by expense_head";
        $db->setQuery($query);
        $expense_heads = $db->loadObjectList();
        
        $this->expense_heads = $expense_heads;
        parent::display($tpl);
    }
}
?>