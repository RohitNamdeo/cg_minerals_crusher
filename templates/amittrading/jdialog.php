<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" class="asd">
<head>
<?
JFactory::getDocument()->setGenerator('Kragos Technologies(kragos.com)');
?>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/jquery.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.core.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.widget.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.mouse.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.position.min.js"></script>

<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.draggable.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.droppable.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.resizable.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.selectable.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.sortable.min.js"></script>

<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.accordion.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.autocomplete.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.button.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.dialog.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/ui/minified/jquery.ui.tabs.min.js"></script>

<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/jquery-impromptu.3.1.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/minified/jquery.tablesorter.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/minified/jquery.validate.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/minified/picnet.table.filter.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/minified/jquery.timeago.min.js"></script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/minified/jquery.colorbox-min.js"></script>


<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/ui.dropdownchecklist.js"></script>

<link rel="stylesheet" href="<?php echo $this->baseurl ?>/custom/packages/jquery1.7.1/themes/base/jquery.ui.all.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/css/colorbox.css" type="text/css" />
<script>
    j=jQuery.noConflict();
    j(function() {
        j("body").bind("contextmenu", function(){
            //return false;
        });
        j("abbr.timeago").timeago();
        j( "input:submit,input:reset,input:button, button").button();
        j(".tablelist tr td:first-child").attr("align", "right");
        
        if(typeof String.prototype.trim !== 'function') {
          String.prototype.trim = function() {
            return this.replace(/^\s+|\s+$/g, ''); 
          }
        }
    });
    function go(place)
    {
        document.location.href = place;
    }
    function isemail(email)
    {
       var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
       return reg.test(email);
    }
</script>
<script src="<?php echo $this->baseurl ?>/custom/packages/jquery/custom/jquery.betterTooltip.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl; ?>/custom/js/flashobject.js"></script>
<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/custom/css/template.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/custom/css/graphics.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/general.css" type="text/css" />
<body>
<jdoc:include type="message" />
<jdoc:include type="component" />
</body>
</html>