<?

defined('_JEXEC') or die( 'Restricted access' );
// manage vehicles
class MasterViewmanage_vehicles_type extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "vehicle type"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_master&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Vehicle Type Master");
        
        $query = "select * from `#__vehicles_type` order by `vehicle_type`";
        $db->setQuery($query);
        $vehicles_type = $db->loadObjectList();
        $this->vehicles_type = $vehicles_type; 
        
        parent::display($tpl);
        
    } 
}
?>