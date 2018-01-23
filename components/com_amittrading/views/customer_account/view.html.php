<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewCustomer_account extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * this is customer account
        * there are 3 tabs
        * 1st tab is sales & payments -> sales_and_payments view
        * 2nd tab is for customer details, edit delete and activate, deactivate account options are provided -> in same view
        * 3rd tab id account statement -> customer_account_statement view
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Customer Account");
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        $this->customer_id = $customer_id;
        
        $query = "select cu.*, c.city, cc.customer_category, cs.customer_segment,st.name state_name from `#__customers` cu inner join `#__cities` c on cu.city_id=c.id left join `#__states` st on cu.state_id=st.id inner join `#__customer_categories` cc on cu.customer_category_id=cc.id inner join `#__customer_segments` cs on cu.customer_segment_id=cs.id where cu.id=" . $customer_id;
        $db->setQuery($query);
        $customer = $db->loadObject();                
        $this->customer = $customer;
        
        //print_r($customer); exit;
        
        $query = "select * from `#__cities` order by city";
        $db->setQuery($query);
        $cities = $db->loadObjectList();
        $this->cities = $cities;
        
        $query = "select * from `#__customer_categories` order by `customer_category`";
        $db->setQuery($query);
        $customer_categories = $db->loadObjectList();
        $this->customer_categories = $customer_categories;
        
        $query = "select * from `#__customer_segments` order by `customer_segment`";
        $db->setQuery($query);
        $customer_segments = $db->loadObjectList();
        $this->customer_segments = $customer_segments;
        
        $query = "select * from #__routes order by route_name";
        $db->setQuery($query);
        $routes = $db->loadObjectList("id");
                      
        $this->assignRef('routes' , $routes);
        
        parent::display($tpl);
    } 
}
?>