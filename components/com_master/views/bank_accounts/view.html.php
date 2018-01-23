<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewBank_accounts extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * View for add/edit/list display of bank accounts
        * bank account can be closed & re-opened
        * delete option is not provided
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "bank_accounts"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Bank Accounts");
        
        $query = "select * from `#__bank_accounts` order by `account_name`";
        $db->setQuery($query);
        $bank_accounts = $db->loadObjectList();
        $this->bank_accounts = $bank_accounts;
        
        parent::display($tpl);
    } 
}
?>