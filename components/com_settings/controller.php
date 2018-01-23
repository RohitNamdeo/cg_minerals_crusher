<?
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class SettingsController extends JControllerLegacy
{
	function display($cachable = false, $urlparams = Array())
	{
		parent::display();
	}
    
    function save_settings()
    {
        $model = $this->getModel('settings');
        $model->save_settings();
    }
    
    function create_setting()
    {
        $model = $this->getModel('settings');
        $model->create_setting();
    }
    
}
?>