<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<jdoc:include type="head" />
<?
include('head.php');
?>
<script>
    j(function(){
        adjust_wrapper();
        j(window).resize(function(){
            adjust_wrapper();
        });
    });
    function adjust_wrapper()
    {
        if (j("#wrapper").width() + 40 < j(document).width())
        {
            j("#wrapper").css("width", j(document).width() - 8);
        }
    }
</script>
<style>
    #wrapper
    {
        padding: 10px 4px 4px 4px;
        margin: 0 auto;
        float: left;
    }
    #status
    {
        background: none repeat scroll 0 0 #EDEDED;
        border-top: 1px solid #DDDDDD;
        margin-top: 1px;
        padding: 5px 0 0 5px;
        position: fixed;
        height: 20px;
        left: 0px;
        right: 0;
        bottom: 0px;
        overflow: hidden;
        z-index: 1;
    }
    .status_copyright
    {
        float: right;
        padding-right: 10px;
    }
    .placeholder
    {
        height: 40px;
        width: 100%;
        clear: both;
    }
</style>
</head>
<body>
<div id="menu" style="z-index: 1;left: 0;right: 0;"><jdoc:include type="modules" name="top" style="none"/></div>
<?
    $user = JFactory::getUser();
    if ($user->id > 0 )
    {
        ?>
            <div id="status">
                <div class="status_copyright">&copy; amittrading.com 2015</div>
                <div>Logged in as <? echo $user->name; ?> [ <a href="index.php?option=com_users&task=user.logout&return=<? echo base64_encode("index.php?option=com_twolevelmenu&view=dashboard"); ?>&<? echo JSession::getFormToken(); ?>=1" tabindex="-1">Logout</a> ]</div>
            </div>
        <?
    }
?>
<div id="wrapper">
    <div align="center" id="page-loader" style="margin-top:100px;">
        Please wait <img src="custom/graphics/fast-loader.gif">
    </div>
    <div id="content" style="min-height: 400px; display: none;" valign="top" align="left">
        <div id="messagealert">
            <jdoc:include type="message" />
        </div>
        <jdoc:include type="component" />
    </div>
    <div class="placeholder" style=""></div>
</div>
<div>
    <jdoc:include type="modules" name="footer" />
</div>
<jdoc:include type="modules" name="debug" />
</body>
</html>