<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
defined( 'DS') || define( 'DS', DIRECTORY_SEPARATOR );
include_once (dirname(__FILE__).DS.'/ja_vars.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<?
include("head.php");
?>
<jdoc:include type="head" />
<?php JHTML::_('behavior.framework'); ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />

<link href="<?php echo $tmpTools->templateurl();?>/css/template.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $tmpTools->templateurl();?>/css/graphics.css" rel="stylesheet" type="text/css" />

<script language="javascript" type="text/javascript" src="<?php echo $tmpTools->templateurl();?>/scripts/ja.script.js"></script>

<?php $tmpTools->genMenuHead(); ?>
<style>
.noprint
{
    display: none;
}
</style>
</head>

<body style="background-color: white; margin: 10px 10px 10px 10px;" id="bd" class="<?php echo $tmpTools->getParam(JA_TOOL_SCREEN);?> fs<?php echo $tmpTools->getParam(JA_TOOL_FONT);?>">
<jdoc:include type="component" />
</body>
</html>