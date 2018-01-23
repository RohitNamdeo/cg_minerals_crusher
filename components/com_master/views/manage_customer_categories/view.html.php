<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewmanage_customer_categories extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * View for add/edit/delete/list display of customer categories
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "manage_customer_categories"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Customer Categories");
        
        $query = "select * from `#__customer_categories` order by `customer_category`";
        $db->setQuery($query);
        $customer_categories = $db->loadObjectList();
        $this->customer_categories = $customer_categories;
        
        parent::display($tpl);
    } 
}
?>