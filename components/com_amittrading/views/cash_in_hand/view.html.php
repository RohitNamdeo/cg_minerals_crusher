<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewCash_in_hand extends JViewLegacy
{
    public function display($tpl = null)
    {
        // view to display cash-in-hand
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "cash_in_hand"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Cash In Hand");
        
        $query = "select `value_numeric` from `#__settings` where `key`='cash_in_hand'";
        $db->setQuery($query);
        $this->cash_in_hand = floatval($db->loadResult());
        
        $query = "select `value_numeric` from `#__settings` where `key`='opening_cash_in_hand'";
        $db->setQuery($query);
        $this->cash_in_hand += floatval($db->loadResult());
        
        parent::display($tpl);
    } 
}
?>