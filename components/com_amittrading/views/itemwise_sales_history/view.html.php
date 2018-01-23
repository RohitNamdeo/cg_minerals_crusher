<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewItemwise_sales_history extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * view for list display of sales invoice
        * items are displayed instead of invoice
        * edit/delete option is provided
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Item wise Sales History");
        
        $d = JRequest::getVar("d");
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        $customer_id = intval(JRequest::getVar("customer_id"));
        $location_id = intval(JRequest::getVar("location_id"));
        
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
        
        if($customer_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(s.customer_id=" . $customer_id . ")";
        }
        
        if($location_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(si.location_id=" . $location_id . ")";
        }
        
        if($from_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(s.bill_date='" . date("Y-m-d", strtotime($from_date)) . "')";
        }
        
        $query = "select s.*, s.id sales_id, si.*, cu.customer_name, l.location_name, c.category_name, i.item_name, i.gst_percent from `#__sales_invoice_items` si inner join `#__sales_invoice` s on si.sales_id=s.id inner join `#__customers` cu on s.customer_id=cu.id inner join `#__inventory_locations` l on si.location_id=l.id inner join `#__category_list` c on si.category_id=c.id inner join `#__items` i on si.item_id=i.id " . ($condition != "" ? " where " . $condition : "") . " order by s.bill_date asc, s.id";
        $db->setQuery($query);
        $sales_items = $db->loadObjectList();       
        
        $limit = 100;
        $total = count($sales_items);
        $limit = JRequest::getVar('limit',$limit, '', 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $db->setQuery( $query, $limitstart, $limit );
        $sales_items = $db->loadObjectlist();
        
        $this->pagination = $pagination;
        $this->total = $total;
        $this->limit = $limit;        
        $this->limitstart = $limitstart;
        
        $query = "select c.id, c.customer_name from `#__customers` c where account_status=" . AC_ACTIVE . " order by c.customer_name";
        $db->setQuery($query);
        $customers = $db->loadObjectList();                
        $this->customers = $customers;
        
        $query = "select id, location_name from `#__inventory_locations` order by location_name";
        $db->setQuery($query);
        $locations = $db->loadObjectList();
        $this->locations = $locations;
       
        $this->sales_items = $sales_items;
        $this->customer_id = $customer_id;
        $this->location_id = $location_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        parent::display($tpl);
    } 
}
?>