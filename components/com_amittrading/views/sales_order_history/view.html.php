<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewSales_order_history extends JViewLegacy
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
        
        if (!Functions::has_permissions("amittrading", "sales_order_history"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Sales Order History");
        
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
            $condition .= ($condition != "" ? " and " : "") . "(so.customer_id=" . $customer_id . ")";
        }
        
        if($from_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(so.order_date='" . date("Y-m-d", strtotime($from_date)) . "')";
        }
        
       //$query = "select si.*,c.customer_name,t.transporter_name,vt.vehicle_type,v.vehicle_number from #__sales_invoice si inner join #__customers c on si.customer_id=c.id inner join #__vehicles v on si.vehicle_id=v.id inner join #__transporters t on si.transporter_id=t.id inner join #__vehicles_type vt on si.loading_vehicle_type=vt.id" . ($condition != "" ? " where " . $condition : "") ;
        $query = "select so.*,c.customer_name,r.royalty_name from `#__sales_orders` so inner join `#__customers` c on so.customer_id=c.id inner join `#__royalty` r on so.royalty_id=r.id " . ($condition != "" ? " where " . $condition : "") ;
        //echo $query;exit;
        $db->setQuery($query);
        $sales_invoices = $db->loadObjectList();
        
        $limit = 100;
        $total = count($sales_invoices);
        $limit = JRequest::getVar('limit',$limit, '', 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $db->setQuery( $query, $limitstart, $limit );
        $sales_invoices = $db->loadObjectlist();
        
        $this->pagination = $pagination;
        $this->total = $total;
        $this->limit = $limit;        
        $this->limitstart = $limitstart;
        
        $query = "select c.id, c.customer_name from `#__customers` c order by c.customer_name";
        $db->setQuery($query);
        $customers = $db->loadObjectList();                
        $this->customers = $customers;
        
        $this->sales_invoices = $sales_invoices;
        $this->customer_id = $customer_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;  
        
        parent::display($tpl);
    } 
}
?>