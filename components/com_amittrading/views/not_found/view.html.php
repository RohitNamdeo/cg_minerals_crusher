<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

class AmittradingViewNot_found extends JViewLegacy
{
    public function display($tpl = null)
    {
        if (Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        parent::display($tpl);
    }
}
?>