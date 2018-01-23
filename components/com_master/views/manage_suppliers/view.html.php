<?php
jimport( 'joomla.application.component.view');
class MasterViewManage_suppliers extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * View for add/list display of suppliers
        * edit/delete option is available in their account
        * account can be viewed on name click
        * total outstanding can be viewed on "#" click
        * sorting can be done by header clicking
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "manage_suppliers"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDBO();
        
        $document = JFactory::getDocument()->setTitle("Suppliers");
        
        $sort_order = base64_decode(JRequest::getVar("so"));
        
        if($sort_order == "") { $sort_order = "supplier_name"; }
        $this->sort_order = $sort_order;
      
        $query = "select s.*,c.city,st.name state,st.gst_state_code from `#__suppliers` s inner join `#__cities` c on c.id=s.city_id left join `#__states` st on s.state_id=st.id order by " . $sort_order;
        $db->setQuery($query);
        $suppliers = $db->loadObjectList();
        
        //print_r($suppliers);exit;
        
        $query = "select * from `#__cities` order by city";
        $db->setQuery($query);
        $cities = $db->loadObjectList();
        
         
        
        $this->suppliers = $suppliers;
        $this->cities = $cities;
        parent::display($tpl);        
    }
}
?>