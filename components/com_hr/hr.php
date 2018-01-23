<?php
defined('_JEXEC') or die('Restricted access');

// Require the com_content helper library
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once (JPATH_ROOT.DS.'custom'.DS.'phpincludes'.DS.'constants.php');
require_once (JPATH_ROOT.DS.'custom'.DS.'phpincludes'.DS.'functions.php');

if (JRequest::getVar("task") != "sos" && JRequest::getVar("task") != "user_login" && JRequest::getVar("task") != "check_user")
{
    if (Functions::ifNotLoginRedirect())
    {
        return;
    }
}
require_once(JPATH_COMPONENT.DS.'controller.php');

// Component Helper
jimport('joomla.application.component.helper');

// Create the controller
$controller = new HrController();

// Perform the Request task
$controller->registerDefaultTask('employees');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
?>