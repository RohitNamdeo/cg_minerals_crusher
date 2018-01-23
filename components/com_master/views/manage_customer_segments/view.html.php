<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewManage_customer_segments extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * View for add/edit/delete/list display of customer segments
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "manage_customer_segments"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Customer Segments");
        
        $query = "select * from `#__customer_segments` order by customer_segment";
        $db->setQuery($query);
        $customer_segments = $db->loadObjectList();                
        $this->customer_segments = $customer_segments;
        
        parent::display($tpl);
    } 
}
?>