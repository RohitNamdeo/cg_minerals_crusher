<?php
jimport( 'joomla.application.component.view');
class MasterViewManage_items extends JViewLegacy
{
    function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        $db = JFactory::getDBO();
        
        $document = JFactory::getDocument();
        $document->setTitle("Items");
        
        $category_id = intval(JRequest::getVar("category_id"));
        $this->category_id = $category_id;
        
        $query = "select * from `#__items` where `category_id`=" . $category_id . " order by `item_name`";
        $db->setQuery($query);
        $items = $db->loadObjectList();

        $this->items = $items;
        
        $query = "select * from `#__inventory_locations` order by `location_name`";
        $db->setQuery($query);
        $locations = $db->loadObjectList();
        $this->locations = $locations; 
        
        $query = "select `category_name` from `#__category_list` where id=" . $category_id;
        $db->setQuery($query);
        $this->category_name = $db->loadResult();
        
        parent::display($tpl);        
    }
}
?>