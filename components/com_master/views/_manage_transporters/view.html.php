<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewManage_transporters extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * View for add/list display of transporters
        * edit/delete option is available in their account
        * account can be viewed on name click
        * total outstanding can be viewed on "#" click
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "manage_transporters"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Transporters");
        
        $sort_order = base64_decode(JRequest::getVar("so"));
        
        if($sort_order == "") { $sort_order = "transporter"; }
        $this->sort_order = $sort_order;
        
        $query = "select * from `#__transporters` order by " . $sort_order;
        $db->setQuery($query);
        $transporters = $db->loadObjectList();
        $this->transporters = $transporters;
        
        parent::display($tpl);
    } 
}
?>