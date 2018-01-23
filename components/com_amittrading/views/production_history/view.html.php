<?

defined('_JEXEC') or die( 'Restricted access' );
// Production History
class AmittradingViewproduction_history extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "production_history"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Production History ");
        
        $month_start_date = date('01-M-Y');
        $current_date = date('d-M-Y');
        
        //$product_type = JRequest::getVar('product_type');
        $from_date = JRequest::getVar('from_date');
        $to_date = JRequest::getVar('to_date');
        
        $condition = "";
        
        if($from_date == "" && $to_date == "")
        { 
            $condition .= ($condition != "" ? " and " : "") . " (dpe.production_date between '". date("Y-m-d", strtotime($month_start_date)) . "' and '" . date("Y-m-d", strtotime($current_date)) . "')";
            $this->from_date=$month_start_date;
            $this->to_date=$current_date;
        }
        
        if($from_date != "" && $to_date != "")
        { 
            $condition .= ($condition != "" ? " and " : "") . " (dpe.production_date between '". date("Y-m-d", strtotime($from_date)) . "' and '" . date("Y-m-d", strtotime($to_date)) . "')";
            $this->from_date=$from_date;
            $this->to_date=$to_date;
        }
        
       /* if($product_type != "" )
        { 
            $condition .= ($condition != "" ? " and " : "") . " dpe.product_id='".$product_type."'";
            
        }  */
        
         //if(($from_date != "" && $to_date != "")|| ($product_type != ""))
         //{    
           // $query = "select dpe.*,p.product_name from `#__daily_production_entry` dpe inner join `#__products` p on dpe.product_id=p.id where dpe.production_date between '". $from_date."' and '". $to_date."' or dpe.product_id='".$product_type."'" ;
            //$db->setQuery($query);
           // $productions = $db->loadObjectList();
         //}
        
        $query = "select dpe.*,p.product_name from `#__daily_production_entry` dpe inner join `#__products` p on dpe.product_id=p.id " . ($condition!="" ? " where " . $condition : "");
        $db->setQuery($query);
        $productions = $db->loadObjectList();   
        
        
        $query = "select * from `#__products` order by `product_name`";
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $this->products = $products;
        
        $this->productions = $productions;
        
        parent::display($tpl);
        
    } 
}
?>