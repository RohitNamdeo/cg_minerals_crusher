<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewCollection_report extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * Any sales invoice becomes visible in collection report after days mentioned in credit days are passed
        * Row click shows items of that invoice
        * collection remarks for customers can be updated
        * payment reminders can be sent to multiple customers
        */

        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "collection_report"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Collection Report");
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        $segment_id = intval(JRequest::getVar("segment_id"));
        $city_id = intval(JRequest::getVar("city_id"));
        $till_date = JRequest::getVar("till_date");
        
        $sort_order = base64_decode(JRequest::getVar("so"));
        
        if($sort_order == "") { $sort_order = "customer_name, bill_date"; }
        $this->sort_order = $sort_order;
        
        if($till_date == "") { $till_date = date("Y-m-d"); }
        else { $till_date = date("Y-m-d", strtotime($till_date)); }
        
        $condition = "(s.status=" . UNPAID . ") and (DATEDIFF('" . $till_date . "', s.`bill_date`) >= s.credit_days)";
        
        if($customer_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(s.customer_id=" . $customer_id . ")";
        }
        
        if($segment_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(cu.customer_segment_id=" . $segment_id . ")";
        }
        
        if($city_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(cu.city_id=" . $city_id . ")";
        }
        
        if($till_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(s.bill_date < '" . $till_date . "')";
        }
        
        $query = "select cu.customer_name, cu.customer_address, cu.contact_no, cu.collection_remarks, s.*, s.id sales_id, c.city from `#__sales_invoice` s inner join `#__customers` cu on s.customer_id=cu.id inner join `#__cities` c on cu.city_id=c.id " . ($condition != "" ? " where " . $condition : "") . " order by " . $sort_order;
        $db->setQuery($query);
        $collections = $db->loadObjectList();
        
        /*$limit = 100;
        $total = count($collections);
        $limit = JRequest::getVar('limit',$limit, '', 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $db->setQuery( $query, $limitstart, $limit );
        $collections = $db->loadObjectlist();
        
        $this->pagination = $pagination;
        $this->total = $total;
        $this->limit = $limit;        
        $this->limitstart = $limitstart;*/
        
        $query = "select c.id, c.customer_name from `#__customers` c where c.account_status=" . AC_ACTIVE . " order by c.customer_name";
        $db->setQuery($query);
        $customers = $db->loadObjectList();                
        $this->customers = $customers;
        
        $query = "select * from `#__customer_segments` order by customer_segment";
        $db->setQuery($query);
        $customer_segments = $db->loadObjectList();                
        $this->customer_segments = $customer_segments;
        
        $query = "select * from `#__cities` order by city";
        $db->setQuery($query);
        $cities = $db->loadObjectList();                
        $this->cities = $cities;  
       
        $this->collections = $collections;
        $this->customer_id = $customer_id;
        $this->segment_id = $segment_id;
        $this->city_id = $city_id;
        $this->till_date = $till_date;
        
        parent::display($tpl);
    } 
}
?>