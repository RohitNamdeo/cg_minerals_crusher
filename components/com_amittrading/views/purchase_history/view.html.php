<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewPurchase_history extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * view to display list of invoices
        * details can be viewed by purchase_items view
        * link to delete is not provided
        * edit is possible only if transporter payment mode is cash or not paid if it is credit
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "purchase_history"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Purchase History");
        
        $d = JRequest::getVar("d");
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        $product_id = intval(JRequest::getVar("product_id"));
        
        if($d != "")
        {
            if($d == 'p')
            {
                $from_date = date("Y-m-d", strtotime("-1 day", strtotime($from_date)));
            }
            else if($d == 'n')
            {
                $from_date = date("Y-m-d", strtotime("+1 day", strtotime($from_date)));
            }
        }
        else
        {
            if($from_date != "")
            { $from_date = date("Y-m-d", strtotime($from_date)); }
        }
        
        if($from_date == "")
        {
            $from_date = date("Y-m-d");
        }
        
        $condition = "";
        
        if($supplier_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(pu.supplier_id=" . $supplier_id . ")";
        }
        /*if($product_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(pu.product_id=" . $product_id . ")";
        }*/
        
        if($from_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(pu.bill_date='" . date("Y-m-d", strtotime($from_date)) . "')";
        } 
        
        //$query = "select p.*,s.supplier_name,pr.product_name,u.unit from `#__purchase` p inner join `#__suppliers` s on p.supplier_id=s.id inner join `#__products` pr on p.product_id=pr.id inner join `#__units` u on p.unit_id=u.id" . ($condition != "" ? " where " . $condition : "") . "";
        $query = "select pu.*,s.supplier_name,v.vehicle_number from `#__purchase` pu inner join `#__suppliers` s on pu.supplier_id=s.id inner join `#__vehicles` v on pu.vehicle_id=v.id" . ($condition != "" ? " where " . $condition : "") . " " ;
        $db->setQuery($query);
        $purchases = $db->loadObjectList();
        
        $limit = 100;
        $total = count($purchases);
        $limit = JRequest::getVar('limit',$limit, '', 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $db->setQuery( $query, $limitstart, $limit );
        $purchases = $db->loadObjectlist();
        
        $this->pagination = $pagination;
        $this->total = $total;
        $this->limit = $limit;        
        $this->limitstart = $limitstart; 
        
        $query = "select s.id, s.supplier_name from `#__suppliers` s order by s.supplier_name";
        $db->setQuery($query);
        $suppliers = $db->loadObjectList();                
        $this->suppliers = $suppliers;
        
        /*$query = "select * from `#__products` order by product_name";
        $db->setQuery($query);
        $products = $db->loadObjectList();                
        $this->products = $products;*/
       
        $this->purchases = $purchases;
        $this->supplier_id = $supplier_id;
        $this->product_id = $product_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;    
        
        parent::display($tpl);
    } 
}
?>