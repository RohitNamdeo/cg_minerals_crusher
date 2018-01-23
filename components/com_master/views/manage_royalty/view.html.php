<?

defined('_JEXEC') or die( 'Restricted access' );
// manage vehicles
class MasterViewmanage_royalty extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "manage_royalty"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_master&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Royalty Master");
        
        $query = "select * from `#__royalty` order by `royalty_name`";
        $db->setQuery($query);
        $royalties = $db->loadObjectList();
        $this->royalties = $royalties; 
        
        parent::display($tpl);
        
    } 
}
?>