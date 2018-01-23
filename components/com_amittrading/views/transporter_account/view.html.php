<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewTransporter_account extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * this is transporter account
        * there are 3 tabs
        * 1st tab is transports & payments -> transports_and_payments view
        * 2nd tab is for transporter details, edit and delete account options are provided -> in same view
        * 3rd tab id account statement -> transporter_account_statement view
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Transporter Account");
        
        $transporter_id = intval(JRequest::getVar("transporter_id"));  
        
        //$query = "(select date, bill_no, transportation_amount_paid bill_amount_paid, vehicle_rate vehicle_rate, '' loading_amount from `#__sales_invoice` where transporter_id = " . $transporter_id . ") union ( select date, bill_no, loading_amount_paid bill_amount_paid, '' vehicle_rate, loading_amount loading_amount  from `#__sales_invoice` where loading_transporter_id = " .$transporter_id. ")";
        //echo $query;
//        $db->setQuery($query);
//        $sales_invoice_details = $db->loadObjectList(); 
        
        
        $this->transporter_id = $transporter_id;
        
        $query = "select t.*,c.city,st.name state,st.gst_state_code from `#__transporters` t inner join `#__cities` c on c.id=t.city_id left join `#__states` st on t.state_id=st.id where t.id= " . $transporter_id;
        //echo $query;exit;
        $db->setQuery($query);
        $transporter = $db->loadObject();
        
        $this->transporter = $transporter;
        
        
        $query = "select * from `#__cities` order by city";
        $db->setQuery($query);
        $cities = $db->loadObjectList();
        //print_r($cities);exit;
        
        $this->cities = $cities;
        
        parent::display($tpl);
    } 
}
?>