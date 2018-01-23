<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewItemwise_sales_stats_print extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * view to print item-wise sales stats
        * not in use
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Item wise Sales Stats Print");
        
        $bill_date = JRequest::getVar("bill_date");
        $bill_nos = JRequest::getVar("bill_nos");
        $item_ids = JRequest::getVar("item_ids");
        
        $condition = "";
        $separator = "";
        
        foreach($bill_nos as $key=>$bill_no)
        {
            $billNos = explode(",", $bill_no);
            
            foreach($billNos as $no)
            {
                $condition .= $separator . "(s.bill_no=" . $no . " and si.item_id=" . $item_ids[$key]. ")";
                $separator = " or ";
            }
        }
        
        $condition = "(" . $condition . ")";
        
        if($bill_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(s.bill_date='" . date("Y-m-d", strtotime($bill_date)) . "')";
        }
        
        $query = "select cu.customer_name, i.item_name, sum(si.pack) quantity, si.item_id, concat(DATE_FORMAT(s.bill_date, '%d-%b-%Y'), '(', GROUP_CONCAT(s.bill_no), ')') bill_details from `#__sales_invoice_items` si inner join `#__sales_invoice` s on si.sales_id=s.id inner join `#__customers` cu on s.customer_id=cu.id inner join `#__items` i on si.item_id=i.id " . ($condition != "" ? " where " . $condition : "") . " group by si.item_id, s.customer_id order by i.item_name, cu.customer_name";
        $db->setQuery($query);
        $item_sales_stats = $db->loadObjectList(); 
        $this->item_sales_stats = $item_sales_stats;
        
        parent::display($tpl);
    } 
}
?>