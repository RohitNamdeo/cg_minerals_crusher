<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>">
<head>
	<jdoc:include type="head" />
    <?
        include('head.php');
    ?>
</head>
<body class="contentpane">
    <jdoc:include type="message" />
	<jdoc:include type="component" />  
</body>
</html>