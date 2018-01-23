<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewPending_sales_orders extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * almost same as pending purchase order
        * display is different -> list of orders is shown, items visible on row click (only unbilled items are shown - > default_items)
        * those sales orders are displayed which have items with unbilled status
        * Any item can be cancelled
        * sales order can be edited/deleted but only if all its items are unbilled
        * sales invoice can be created by items of sales order
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "pending_sales_orders"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Pending Sales Orders");
        
        $mode = JRequest::getVar("m"); // $mode = 'd' means item details
        
        if($mode == 'd')
        {
            $order_id = intval(JRequest::getVar("order_id"));
            
            $query = "select `customer_id` from `#__sales_orders` where id=" . $order_id;
            $db->setQuery($query);
            $customer_id = intval($db->loadResult()); 
            
            //$query = "select soi.*, c.category_name, i.item_name,i.piece_per_pack from `#__sales_order_items` soi inner join `#__category_list` c on soi.category_id=c.id inner join `#__items` i on soi.item_id=i.id where soi.sales_order_id=" . $order_id . " and soi.status=" . UNBILLED . " order by soi.id";
            
            $query = "select soi.*, p.product_name from `#__sales_order_items` soi inner join `#__products` p on soi.product_id=p.id where soi.sales_order_id=" . $order_id . " and soi.status=" . UNBILLED . " order by soi.id";
            $db->setQuery($query);
            $pending_sales_order_items = $db->loadObjectList();
            
            $this->order_id = $order_id;
            $this->customer_id = $customer_id;
            $this->pending_sales_order_items = $pending_sales_order_items;
            
            parent::display("items");
        }
        else
        {
            $customer_id = intval(JRequest::getVar("customer_id"));
            $customer_segment_id = intval(JRequest::getVar("cs_id"));
            
            $condition = "(soi.status=" . UNBILLED . ")";
            
            if($customer_id != 0)
            {
                $condition .= ($condition != "" ? " and " : "") . "(so.customer_id=" . $customer_id . ")";
            }
            
            if($customer_segment_id != 0)
            {
                $condition .= ($condition != "" ? " and " : "") . "(cu.customer_segment_id=" . $customer_segment_id . ")";
            }
            
            $query = "select distinct so.id order_id, so.total_weight, so.billed_quantity, so.order_date,so.royalty_id,so.royalty_rate, cu.customer_name, cu.customer_address,cu.city_id , r.royalty_name , cit.city from `#__sales_order_items` soi inner join `#__sales_orders` so on soi.sales_order_id=so.id inner join `#__customers` cu on so.customer_id=cu.id inner join `#__royalty` r on so.royalty_id=r.id inner join `#__cities` cit on cu.city_id=cit.id " . ($condition != "" ? " where " . $condition : "") . " and so.total_weight <> so.billed_quantity order by so.order_date, so.id";
            $db->setQuery($query);
            $pending_orders = $db->loadObjectList();
            
            $pending_order_ids = array();
            
            if(count($pending_orders) > 0)
            {
                foreach($pending_orders as $key=>$order)
                {
                    $query = "select count(*) from `#__sales_order_items` where status<>" . UNBILLED . " and sales_order_id=" . intval($order->order_id);
                    //echo $query;exit;
                    $db->setQuery($query);
                    $count = intval($db->loadResult());
                    
                    if($count == 0)
                    { $pending_order_ids[$order->order_id] = $order->order_id; }
                }
            }
            
            $query = "select c.id, c.customer_name from `#__customers` c where account_status=" . AC_ACTIVE . " order by c.customer_name";
            $db->setQuery($query);
            $customers = $db->loadObjectList();                
            $this->customers = $customers;
            
            $query = "select cs.id, cs.customer_segment from `#__customer_segments` cs order by cs.customer_segment";
            $db->setQuery($query);
            $customer_segments = $db->loadObjectList();                
            $this->customer_segments = $customer_segments;
           
            $this->pending_orders = $pending_orders;
            $this->pending_order_ids = $pending_order_ids;
            $this->customer_id = $customer_id;
            $this->customer_segment_id = $customer_segment_id;
            //$this->order_id = $order_id;   
            
            parent::display($tpl);
        }
    } 
}
?>