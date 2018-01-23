<?php
jimport( 'joomla.application.component.view');
class MasterViewManage_categories extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * View for add/edit/delete/list display of product categories
        * Item details of any category can be viewed on row click -> manage_items view
        * view gives option to add/edit/delete items
        * Item has 2 sale price & location-wise stock
        * location wise stock can also be viewed -> item_inventory_details view
        * more than 1 item under any category for any location can be merged to any item of that category to any location. 
        * When adding new item, last purchase rate is mentioned but not updated in edit because it gets auomatically updated by purchase invoice
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "manage_categories"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDBO();
        
        $document = JFactory::getDocument();
        $document->setTitle("Categories");
        
        $query = "select * from `#__category_list` order by `category_name`";
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        $this->categories = $categories;
        
        $query = "select * from `#__inventory_locations` order by `location_name`";
        $db->setQuery($query);
        $locations = $db->loadObjectList();
        $this->locations = $locations;
        
        parent::display($tpl);        
    }
}
?>