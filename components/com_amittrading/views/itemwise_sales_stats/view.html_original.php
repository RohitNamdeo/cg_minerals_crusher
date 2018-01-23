<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewItemwise_sales_stats extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Item wise Sales Stats");
        
        $d = JRequest::getVar("d");
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        $customer_id = intval(JRequest::getVar("customer_id"));
        
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
        
        if($from_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(s.bill_date='" . date("Y-m-d", strtotime($from_date)) . "')";
        }
        
        //$query = "select cu.customer_name, i.item_name, sum(si.pack) quantity, si.item_id from `#__sales_invoice_items` si inner join `#__sales_invoice` s on si.sales_id=s.id inner join `#__customers` cu on s.customer_id=cu.id inner join `#__items` i on si.item_id=i.id " . ($condition != "" ? " where " . $condition : "") . " group by si.item_id, s.customer_id order by i.item_name, cu.customer_name";
        $query = "select cu.customer_name, i.item_name, sum(si.pack) quantity, si.item_id, concat(DATE_FORMAT(s.bill_date, '%d-%b-%Y'), '(', GROUP_CONCAT(s.bill_no), ')') bill_details, GROUP_CONCAT(s.bill_no) bill_nos from `#__sales_invoice_items` si inner join `#__sales_invoice` s on si.sales_id=s.id inner join `#__customers` cu on s.customer_id=cu.id inner join `#__items` i on si.item_id=i.id " . ($condition != "" ? " where " . $condition : "") . " group by si.item_id, s.customer_id order by i.item_name, cu.customer_name";
        $db->setQuery($query);
        $item_sales_stats = $db->loadObjectList(); 
        
        $limit = 100;
        $total = count($item_sales_stats);
        $limit = JRequest::getVar('limit',$limit, '', 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $db->setQuery( $query, $limitstart, $limit );
        $item_sales_stats = $db->loadObjectlist();
        
        $this->pagination = $pagination;
        $this->total = $total;
        $this->limit = $limit;        
        $this->limitstart = $limitstart;
        
        $query = "select c.id, c.customer_name from `#__customers` c order by c.customer_name";
        $db->setQuery($query);
        $customers = $db->loadObjectList();                
        $this->customers = $customers;  
       
        $this->item_sales_stats = $item_sales_stats;
        $this->customer_id = $customer_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        parent::display($tpl);
    } 
}
?>