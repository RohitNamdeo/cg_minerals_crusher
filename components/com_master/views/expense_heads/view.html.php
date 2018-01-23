<?

defined('_JEXEC') or die( 'Restricted access' );
// manage vehicles
class MasterViewexpense_heads extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "Expense Head"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_master&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Expense Head Master");
        
        $query = "select * from `#__expense_head` order by `expense_head`";
        $db->setQuery($query);
        $expense_heads = $db->loadObjectList();
        $this->expense_heads = $expense_heads;  
        
        parent::display($tpl);
        
    } 
}
?>