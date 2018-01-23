<?

defined('_JEXEC') or die( 'Restricted access' );
// manage vehicles
class MasterViewnotepad extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "notepad"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_master&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Notepad ");
        
        $query = "select * from `#__notepad` order by `due_date`";
        $db->setQuery($query);
        $notepad_list = $db->loadObjectList();
        $this->notepad_list = $notepad_list; 
        
        parent::display($tpl);
        
    } 
}
?>