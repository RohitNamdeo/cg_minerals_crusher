<?php
jimport( 'joomla.application.component.view');
class MasterViewManage_customers extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * View for add/list display of customers
        * edit/delete/de-activate account option is available in their account
        * account can be viewed on name click
        * total outstanding can be viewed on "#" click
        * sorting can be done by header clicking
        * there are some more options - setting customer category for multiple customers at once
        * Sending custom message to customers of particular category at once
        * Collection remarks can be edited here itself
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "manage_customers"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDBO();
        
        $document = JFactory::getDocument()->setTitle("Customers");
        
        $sort_order = base64_decode(JRequest::getVar("so"));
        $account_status = intval(JRequest::getVar("as"));
        $segment_id = intval(JRequest::getVar("segment_id"));
        $this->segment_id = $segment_id;
        
        if($account_status == 0)
        { $account_status = AC_ACTIVE; }
        $this->account_status = $account_status;
        
        $condition = "";
        
        if($account_status != -1)
        {
            $condition = "(cu.account_status=" . $account_status . ")";
        }
        
        if($segment_id > 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "cu.customer_segment_id=" . $segment_id;
        }
        
        if($sort_order == "") { $sort_order = "customer_name"; }
        $this->sort_order = $sort_order;
      
        $query = "select cu.*, c.city, cc.customer_category, cs.customer_segment,st.name state, st.gst_state_code from `#__customers` cu inner join `#__cities` c on c.id=cu.city_id left join `#__states` st on cu.state_id=st.id inner join `#__customer_categories` cc on cu.customer_category_id=cc.id inner join `#__customer_segments` cs on cu.customer_segment_id=cs.id" . ($condition != "" ? " where " . $condition : "") . " order by " . $sort_order;
        $db->setQuery($query);
        $customers = $db->loadObjectList();
        
        $query = "select * from `#__cities` order by city";
        $db->setQuery($query);
        $cities = $db->loadObjectList();
        
        $query = "select * from `#__customer_categories` order by `customer_category`";
        $db->setQuery($query);
        $customer_categories = $db->loadObjectList();
        $this->customer_categories = $customer_categories;
        
        $query = "select * from `#__customer_segments` order by `customer_segment`";
        $db->setQuery($query);
        $customer_segments = $db->loadObjectList();
        $this->customer_segments = $customer_segments;
        
        $this->customers = $customers;
        $this->cities = $cities;
        
        $query = "select * from #__routes order by route_name";
        $db->setQuery($query);
        $routes = $db->loadObjectList("id");
                      
        $this->assignRef('routes' , $routes);
        
        parent::display($tpl);        
    }
}
?>