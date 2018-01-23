<?

defined('_JEXEC') or die( 'Restricted access' );
// manage vehicles
class MasterViewmanage_products extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "product master"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_master&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Product Master");
        
        
         //$query = "select p.id, p.product_name, p.gst_percent, u.unit from `jos_products` as p inner join `jos_units` as u where p.unit_id=u.id";
         $query = "select p.*, u.unit from `jos_products` as p inner join `jos_units` as u where p.unit_id=u.id";
         $db->setQuery($query);
         $products_name = $db->loadObjectList();
         $this->products_name = $products_name; 
        
        //$query = "select * from `#__products` order by `product_name`";
//        $db->setQuery($query);
//        $products_name = $db->loadObjectList();
//        $this->products_name = $products_name; 
        
        $query_units = "select * from `#__units` order by `unit`"; 
        $db->setQuery($query_units);
        $units = $db->loadObjectList(); 
        $this->units = $units;   

        $query = "";
        
        parent::display($tpl);
        
    } 
}
?>