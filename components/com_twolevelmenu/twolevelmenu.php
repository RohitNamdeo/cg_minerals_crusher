<?php
defined('_JEXEC') or die('Restricted access');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// Require the com_content helper library
require_once(JPATH_BASE.DS.'custom'.DS.'phpincludes'.DS.'functions.php');
require_once(JPATH_BASE.DS.'custom'.DS.'phpincludes'.DS.'constants.php');
require_once(JPATH_COMPONENT.DS.'controller.php');
//require_once(JPATH_COMPONENT.DS.'includes'.DS.'constants.php');

// Component Helper
jimport('joomla.application.component.helper');

// Create the controller
$controller = new TwolevelmenuController();

// Perform the Request task
$controller->registerDefaultTask('menus');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

?>
