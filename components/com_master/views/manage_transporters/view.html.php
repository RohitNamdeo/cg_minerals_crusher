<?php
jimport( 'joomla.application.component.view');
class MasterViewManage_transporters extends JViewLegacy
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
        
        if (!Functions::has_permissions("master", "manage_transporters"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_master&view=not_found");
            return;
        }
        
        $db = JFactory::getDBO();
        
        $document = JFactory::getDocument()->setTitle("Transporters");
        
        $sort_order = base64_decode(JRequest::getVar("so"));
        
        if($sort_order == "") { $sort_order = "transporter_name"; }
        $this->sort_order = $sort_order;
      
        $query = "select t.*,c.city,st.name state,st.gst_state_code from `#__transporters` t inner join `#__cities` c on c.id=t.city_id left join `#__states` st on t.state_id=st.id order by " . $sort_order;
        $db->setQuery($query);
        //$suppliers = $db->loadObjectList();
        $transporters = $db->loadObjectList();
        
        $query = "select * from `#__cities` order by city";
        $db->setQuery($query);
        $cities = $db->loadObjectList();
        
        $this->transporters = $transporters;
        $this->cities = $cities;
        parent::display($tpl);        
    }
}
?>