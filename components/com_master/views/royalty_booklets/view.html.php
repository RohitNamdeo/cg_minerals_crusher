<?

defined('_JEXEC') or die( 'Restricted access' );
// manage vehicles
class MasterViewroyalty_booklets extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "royalty_booklets"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_master&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Royalty Booklet Master");
        
        $mode = JRequest::getVar("mode");
        $this->mode = $mode;
         
        $query = "select r.*,s.supplier_name from `#__royalty_booklets` r left join `#__suppliers` s on r.supplier_id=s.id  order by `booklet_name`";
        $db->setQuery($query);
        $booklets= $db->loadObjectList();
        $this->booklets = $booklets;
        
        $used_pages = array();
        $sold_pages = array();
        foreach($booklets as $booklet)
        {
            $query = "select count(id) from `#__royalty_booklet_items` where used=1 and booklet_id=" . intval($booklet->id);
            $db->setQuery($query);
            $totalPages = intval($db->loadResult());
            
            $used_pages[$booklet->id] = $totalPages; 
            
            $query = "select count(id) from `#__royalty_booklet_items` where used=2 and booklet_id=" . intval($booklet->id);
            $db->setQuery($query);
            $totalPages = intval($db->loadResult());
            
            $sold_pages[$booklet->id] = $totalPages;    
        }
        
        $this->used_pages = $used_pages;
        $this->sold_pages = $sold_pages;
        
        $query = "select * from `#__suppliers`";
        $db->setQuery($query);
        $suppliers= $db->loadObjectList();
        $this->suppliers = $suppliers;
           
        parent::display($tpl);
        
    } 
}
?>