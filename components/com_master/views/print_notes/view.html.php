<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewPrint_notes extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Print Notes");
        
        $note_ids = base64_decode(JRequest::getVar("n_ids"));
        $note_ids = explode(",", $note_ids);
        
        $condition = "";
        foreach($note_ids as $key=>$note_id)
        {
            $condition .= ($condition != "" ? " or " : "") . "(id=" . $note_id . ")";
        }
        
        $query = "select * from `#__notes` " . ($condition != "" ? " where " . $condition : "") . " order by note_type, date_of_note";
        $db->setQuery($query);
        $notes = $db->loadObjectList();
        $this->notes = $notes;
        
        parent::display($tpl);
    } 
}
?>