<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

class MasterViewJsPrintPopup extends JViewLegacy
{
	function display($tpl = null)
	{
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
		parent::display($tpl);
	}
}
?>