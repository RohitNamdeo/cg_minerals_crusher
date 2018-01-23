<?

defined('_JEXEC') or die( 'Restricted access' );
class AmittradingViewroyalty_sales extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "royalty_sales"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_master&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Royalty Sales");
        
        $query = "select id,customer_name from `#__customers`";
        $db->setQuery($query);
        $customers = $db->loadObjectList();
        $this->customers = $customers;
        
        $this->pages = 0;
        $this->total_pages = 0;
        $this->booklet_id = 0;
        $this->booklet_name = "";
        $this->quantity = 0.00;
        $this->rate = 0.00;
        $this->min = 0;
        $this->max = 0;
        
        $booklet_id = JRequest::getVar("hidden");
        $this->booklet_id = $booklet_id;
        $pages = JRequest::getVar("checkbox");
        
        //echo $booklet_id; 
        
        $condition = "";
        if(is_array($pages))
        {
             foreach($pages as $page)
            {
                $condition .= ($condition == "" ? "" : " or ") . " id=" . $page;      
            } 
            $query = "select `rb_no` from `#__royalty_booklet_items` " . ($condition != "" ? " where " : "") . $condition;
            $db->setQuery($query);
            $rb_no = $db->loadAssocList();
            
            $this->total_pages = count($rb_no);
            $this->pages = implode(',',$pages);   
        
            if(count($rb_no) > 0)
            {
                $min = $rb_no[0]["rb_no"];
                $max = $rb_no[0]["rb_no"];
                foreach($rb_no as $key => $val){
                    if($min > $val["rb_no"])
                    {
                        $min = $val["rb_no"];
                    }
                    if($max < $val["rb_no"])
                    {
                        $max = $val["rb_no"];
                    }
                }    
                $this->min = $min;
                $this->max = $max; 
            }
        }    
        
        if($booklet_id > 0)
        {
            $query = "select rb.id,rb.booklet_name,rb.quantity,rb.rate from `#__royalty_booklets` rb where rb.id=" . $booklet_id;
            $db->setQuery($query);
            $royalty_booklets = $db->loadObject();
            
            $this->booklet_id = $royalty_booklets->id;
            $this->booklet_name = $royalty_booklets->booklet_name;
            $this->quantity = $royalty_booklets->quantity;
            $this->rate = $royalty_booklets->rate; 
        }
          
        parent::display($tpl);
        
    } 
}
?>