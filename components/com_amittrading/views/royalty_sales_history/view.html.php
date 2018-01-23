<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewroyalty_sales_history extends JViewLegacy
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
        
        if (!Functions::has_permissions("amittrading", "royalty_sales_history"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Royalty Sales History");
        
        $d = JRequest::getVar("d");
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        $customer_id = intval(JRequest::getVar("customer_id"));  
        //$bill_type = intval(JRequest::getVar("bill_type"));
        
        
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
            $condition .= ($condition != "" ? " and " : "") . "(rs.customer_id=" . $customer_id . ")";
        }
        
        /*if($bill_type != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(si.bill_type=" . $bill_type . ")";
        } */
        
        if($from_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(rs.date='" . date("Y-m-d", strtotime($from_date)) . "')";
        }
        
        $query = "select rs.*,c.customer_name,rb.booklet_name from `#__royalty_sales` rs inner join `#__customers` c on rs.customer_id=c.id inner join `#__royalty_booklets` rb on rs.royalty_booklet_id=rb.id" . ($condition != "" ? " where " . $condition : "");
        $db->setQuery($query);
        $royalty_sales = $db->loadObjectList();
        
        $this->royalty_sales = $royalty_sales;
        
        
        $limit = 100;
        $total = count($royalty_sales);
        $limit = JRequest::getVar('limit',$limit, '', 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $db->setQuery( $query, $limitstart, $limit );
        
        $this->pagination = $pagination;
        $this->total = $total;
        $this->limit = $limit;        
        $this->limitstart = $limitstart;
        
        $query = "select c.id, c.customer_name from `#__customers` c order by c.customer_name";   
        $db->setQuery($query);
        $customers = $db->loadObjectList();                
        $this->customers = $customers;
        
        $this->customer_id = $customer_id;
        //$this->bill_type = $bill_type;
        $this->from_date = $from_date;
        $this->to_date = $to_date; 
        
        
        
        
        /*$query = "select rs.*,c.customer_name,rb.booklet_name from `#__royalty_sales` rs inner join `#__customers` c on rs.customer_id=c.id inner join `#__royalty_booklets` rb on rs.royalty_booklet_id=rb.id";
        $db->setQuery($query);
        $royalty_sales = $db->loadObjectList();
        
        $this->royalty_sales = $royalty_sales;*/
        
        //print_r($royalty_sales);exit;
        
       
         
        
        parent::display($tpl);
    } 
}
?>