<?

defined('_JEXEC') or die( 'Restricted access' );
// manage vehicles
class MasterViewmanage_vehicles extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "vehicle"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_master&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Vehicle Master");
        
        $query = "select * from `#__vehicles_type` order by `vehicle_type`";
        $db->setQuery($query);
        $vehicles_type = $db->loadObjectList();
        $this->vehicles_type = $vehicles_type;
        
        //$query = "select * from `#__vehicles` order by `vehicle_type`";
        $query = "select v.*,vt.vehicle_type,t.transporter_name,t.id transporter_id from `#__vehicles` v inner join `#__vehicles_type` vt on v.vehicle_type=vt.id inner join `#__transporters` t on t.id=v.transporter_id" ; 
        //echo $query;exit;
        $db->setQuery($query);
        $vehicles = $db->loadObjectList();
        $this->vehicles = $vehicles;
        
        $query = "select * from `#__transporters`";  
        //echo $query;exit;  
        $db->setQuery($query);
        $transporters = $db->loadObjectList();
        //print_r($transporters);exit;
        $this->transporters = $transporters;
        
        parent::display($tpl);
        
    } 
}
?>