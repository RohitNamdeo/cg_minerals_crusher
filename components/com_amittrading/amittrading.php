<?php
/**
* @version        $Id: student.php 10381 2008-06-01 03:35:53Z pasamio $
* @package        Joomla
* @subpackage    Students
* @copyright    Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Require the abacus controller
require_once (JPATH_ROOT.DS.'custom'.DS.'phpincludes'.DS.'constants.php');
require_once (JPATH_ROOT.DS.'custom'.DS.'phpincludes'.DS.'functions.php');

// Create the controller            
$controller = new AmittradingController();

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
?>