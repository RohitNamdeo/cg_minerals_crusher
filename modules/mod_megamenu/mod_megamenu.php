<?php
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$menus = modMegaMenuHelper::getMenu();

require(JModuleHelper::getLayoutPath('mod_megamenu'));