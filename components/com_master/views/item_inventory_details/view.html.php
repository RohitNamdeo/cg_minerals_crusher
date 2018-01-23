<?php
jimport( 'joomla.application.component.view');
class MasterViewItem_inventory_details extends JViewLegacy
{
    function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        $db = JFactory::getDBO();
        
        $document = JFactory::getDocument();
        $document->setTitle("Item Inventory");
        
        $item_id = intval(JRequest::getVar('item_id'));
        
        $query = "select i.*, c.category_name from `#__items` i inner join `#__category_list` c on i.category_id=c.id where i.id=" . $item_id;
        $db->setQuery($query);
        $item = $db->loadObject();
        $this->item = $item;
        
        $query = "select ii.*, l.location_name from `#__inventory_items` ii inner join `#__inventory_locations` l on ii.location_id=l.id where ii.item_id=" . $item_id . " order by l.location_name";
        $db->setQuery($query);
        $inventories = $db->loadObjectList();
        $this->inventories = $inventories;
        
        parent::display($tpl);        
    }
}
?>