<?
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewRoyalty_booklet_items extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Royalty Items");
        
        $rb_id = intval(JRequest::getVar('rb_id'));
        //echo $rb_id; 
        $this->rb_id = $rb_id;
        
        $query = "select * from `#__royalty_booklet_items` where booklet_id=".$rb_id;
        $db->setQuery($query);  
        $pages = $db->loadObjectList("id");
        $this->pages = $pages;
        
        $query =  "select rbi.*,s.date,s.bill_no,c.customer_name from `#__royalty_booklets` rb inner join `#__royalty_booklet_items` rbi on rb.id=rbi.booklet_id left join `#__sales_invoice` s on rbi.sales_invoice_id=s.id left join `#__customers` c on s.customer_id=c.id where rb.id=" . $rb_id . " and used=1 order by rbi.rb_no asc";
        $db->setQuery($query);  
        $royalty_numbers = $db->loadObjectList("id");
        $this->royalty_numbers = $royalty_numbers;
        
        $query = "select rs.id `sale_id`, rs.customer_id `sale_customer_id` ,rs.date `sale_date` ,rs.royalty_booklet_id ,c.customer_name `sale_customer_name` ,rbi.* from `#__royalty_sales` rs inner join `#__customers` c on rs.customer_id=c.id inner join `#__royalty_booklet_items` rbi on rs.royalty_booklet_id=rbi.booklet_id where rs.royalty_booklet_id=" . intval($rb_id)." and rbi.used=". SALE ;
        $db->setQuery($query);        
        $total_sale_pages = $db->loadObjectList("id");
        $this->total_sale_pages = $total_sale_pages;
        
       // $sale_pages = array();
//        foreach($royalty_numbers as $royalty_number)
//        {
//           // $query = "select * from `#__royalty_booklet_items` where used=" . SALE . " and booklet_id=" . intval($royalty_number->booklet_id);
//            $query = "select rs.id `sale_id`, rs.customer_id `sale_customer_id` ,rs.date `sale_date` ,rs.royalty_booklet_id ,c.customer_name `sale_customer_name` ,rbi.* from `#__royalty_sales` rs inner join `#__customers` c on rs.customer_id=c.id inner join `#__royalty_booklet_items` rbi on rs.royalty_booklet_id=rbi.booklet_id where rs.royalty_booklet_id=" . intval($royalty_number->booklet_id)." and rbi.used=". SALE ;
//            $db->setQuery($query);
//            $total_sale_pages = $db->loadObjectList();
//            
//        }
//        $this->total_sale_pages = $total_sale_pages;
//        print_r($total_sale_pages); 
        
                
        parent::display($tpl);
    } 
}
?>