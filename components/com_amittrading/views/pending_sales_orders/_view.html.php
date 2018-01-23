<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewPending_sales_orders extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Pending Sales Orders");
        
        /*$d = JRequest::getVar("d");
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");*/
        $customer_id = intval(JRequest::getVar("customer_id"));
        
        /*if($d != "")
        {
            if($d == 'p')
            {
                $from_date = date("Y-m-d", strtotime("-1 day", strtotime($from_date)));
                $to_date = $from_date;
            }
            else if($d == 'n')
            {
                $from_date = date("Y-m-d", strtotime("+1 day", strtotime($from_date)));
                $to_date = $from_date;
            }
        }*/
        
        /*if($customer_id == 0 && ($from_date == "" || $to_date == ""))
        {
            $from_date = date("Y-m-d");
            $to_date = date("Y-m-d");
        }*/
        
        $condition = "(soi.status=" . UNBILLED . ")";
        
        if($customer_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(so.customer_id=" . $customer_id . ")";
        }
        
        /*if($from_date != "" && $to_date != "")
        {
            //$condition .= ($condition != "" ? " and " : "") . "(so.order_date between '" . date("Y-m-d", strtotime($from_date)) . "' and '" . date("Y-m-d", strtotime($to_date)) . "')";
            $condition .= ($condition != "" ? " and " : "") . "(so.order_date='" . date("Y-m-d", strtotime($from_date)) . "')";
        }*/
        
        $query = "select so.id order_no, so.order_date, so.customer_id, so.total_amount, soi.*, cu.customer_name, cu.customer_address, c.category_name, i.item_name from `#__sales_order_items` soi inner join `#__sales_orders` so on soi.sales_order_id=so.id inner join `#__customers` cu on so.customer_id=cu.id inner join `#__category_list` c on soi.category_id=c.id inner join `#__items` i on soi.item_id=i.id " . ($condition != "" ? " where " . $condition : "") . " order by so.order_date, so.id";
        $db->setQuery($query);
        $pending_sales_order_items = $db->loadObjectList();
        
        $query = "select distinct so.id order_no from `#__sales_order_items` soi inner join `#__sales_orders` so on soi.sales_order_id=so.id inner join `#__customers` cu on so.customer_id=cu.id inner join `#__category_list` c on soi.category_id=c.id inner join `#__items` i on soi.item_id=i.id " . ($condition != "" ? " where " . $condition : "") . " order by so.order_date, so.id";
        $db->setQuery($query);
        $pending_orders = $db->loadColumn();   
        
        if(count($pending_orders) > 0)
        {
            foreach($pending_orders as $key=>$order)
            {
                $query = "select count(*) from `#__sales_order_items` where status<>" . UNBILLED . " and sales_order_id=" . intval($order);
                $db->setQuery($query);
                $count = intval($db->loadResult());
                if($count > 0)
                { unset($pending_orders[$key]); }
            }
        }
        
        $query = "select c.id, c.customer_name from `#__customers` c order by c.customer_name";
        $db->setQuery($query);
        $customers = $db->loadObjectList();                
        $this->customers = $customers;
       
        $this->pending_orders = $pending_orders;
        $this->pending_sales_order_items = $pending_sales_order_items;
        $this->customer_id = $customer_id;
        /*$this->from_date = $from_date;
        $this->to_date = $to_date;*/
        
        parent::display($tpl);
    } 
}
?>