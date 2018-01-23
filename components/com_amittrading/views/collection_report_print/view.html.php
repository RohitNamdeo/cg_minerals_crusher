<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewCollection_report_print extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * to print the amount details of invoices selected from collection report
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Collection Report Print");
        
        $sales_ids = base64_decode(JRequest::getVar("s_ids"));
        $sales_ids = explode(",", $sales_ids);
        
        $condition = "";
        foreach($sales_ids as $key=>$sales_id)
        {
            $condition .= ($condition != "" ? " or " : "") . "(s.id=" . $sales_id . ")";
        }
        
        $query = "select cu.customer_name, cu.customer_address, cu.contact_no, c.city, s.bill_date, s.bill_amount, s.amount_paid, s.customer_id from `#__sales_invoice` s inner join `#__customers` cu on s.customer_id=cu.id inner join `#__cities` c on cu.city_id=c.id " . ($condition != "" ? " where " . $condition : "") . " order by cu.customer_name, s.bill_date";
        $db->setQuery($query);
        $collections = $db->loadObjectList();
        $this->collections = $collections;
        
        parent::display($tpl);
    } 
}
?>