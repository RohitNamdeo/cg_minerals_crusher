<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewSales_order_history extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Sales Order History");
        
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        $customer_id = intval(JRequest::getVar("customer_id"));
        
        if($customer_id == 0 && ($from_date == "" || $to_date == ""))
        {
            $from_date = date("Y-m-d");
            $to_date = date("Y-m-d");
        }
        
        $condition = "";
        
        if($customer_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(so.customer_id=" . $customer_id . ")";
        }
        
        if($from_date != "" && $to_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(so.order_date between '" . date("Y-m-d", strtotime($from_date)) . "' and '" . date("Y-m-d", strtotime($to_date)) . "')";
        }
        
        $query = "select so.id order_no, so.order_date, so.total_amount, so.creation_date, cu.customer_name from `#__sales_orders` so inner join `#__customers` cu on so.customer_id=cu.id " . ($condition != "" ? " where " . $condition : "") . " order by so.order_date asc, so.id";
        $db->setQuery($query);
        $sales_orders = $db->loadObjectList();       
        
        $limit = 100;
        $total = count($sales_orders);
        $limit = JRequest::getVar('limit',$limit, '', 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $db->setQuery( $query, $limitstart, $limit );
        $sales_orders = $db->loadObjectlist();
        
        $this->pagination = $pagination;
        $this->total = $total;
        $this->limit = $limit;        
        $this->limitstart = $limitstart;
        
        $query = "select c.id, c.customer_name from `#__customers` c order by c.customer_name";
        $db->setQuery($query);
        $customers = $db->loadObjectList();                
        $this->customers = $customers;
       
        $this->sales_orders = $sales_orders;
        $this->customer_id = $customer_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        parent::display($tpl);
    } 
}
?>