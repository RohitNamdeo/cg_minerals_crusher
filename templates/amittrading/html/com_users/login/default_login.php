<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * 
 * Please refer to login_docs.txt to understand the flow of login in KCRM Frontend
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?> 
<style>
body
{
    margin : 0px;
    padding: 0px;
    background-image :url('templates/amittrading/images/background3.jpg');
    text-align: center !important;
}
#main-Content
{
    /*vertical-align: middle;*/
    /*position : absolute;*/
    /*margin :180px 200px 200px 450px;*/
    margin : 200px auto;
    display: block;
    /*vertical-align: middle;*/
    white-space: normal;
    text-align: left;
    width: 400px;
    max-width: 1000px;
    background: none repeat scroll 0% 0% rgb(255, 255, 255);
    border-width: medium 1px 1px medium;
    border-style: none solid solid none;
    border-color: -moz-use-text-color rgb(204, 204, 204) rgb(204, 204, 204) -moz-use-text-color;
    -moz-border-top-colors: none;
    -moz-border-right-colors: none;
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    border-image: none;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    border-radius: 3px;
    padding: 20px;
    position: static !important;
    float: none;
}
fieldset
{
    border : transparent;
    padding : 0px;
    margin : 0px;
    
}
#sitelogo
{
    background-color: #0f3657;
    color: white;
    height: 35px;
    padding : 8px;
    width: 285px;
    border-radius: 2px;
}
#username,#password
{
    padding : 8px;
    width : 94%;
    border: 1px solid #5E80A6;
}
span.star
{
    display: none;
}

#login_form
{
    width:300px;
    display: block;
    margin: 15% auto;
    background: #fff;
    padding: 40px 25px;
    border-radius: 2px;
}
a:hover
{
    text-decoration: underline;
    color: #333;
}
</style>
<script>    
j(function(){
    j("#dummy_system_message").html(j("#system-message-container").html()); 
    j("#system-message-container").html("");
    j("#username").focus();
    j("body").html(j("#login-container").html());
});
</script>
<div id="login_form_container">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	<div class="login-description">
	<?php endif; ?>

		<?php if ($this->params->get('logindescription_show') == 1) : ?>
			<?php echo $this->params->get('login_description'); ?>
		<?php endif; ?>

		<?php if (($this->params->get('login_image') != '')) :?>
			<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JTEXT::_('COM_USER_LOGIN_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	</div>
	<?php endif; ?>
    <!--
        <img src="templates/lotuserp_admin/images/logo2.png" style="padding:10px;"/>
    -->
    <?php //echo JRoute::_('index.php?option=com_users&task=user.login'); ?>
    <!--<form method="post" action="<?php //echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" class="form-horizontal" id="login_form">-->
	<form method="post" action="<?php echo JRoute::_('index.php?option=com_hr&task=user_login'); ?>" class="form-horizontal" id="login_form">
        <div id="sitelogo" align="center" style="valign:middle;"><h1>CG Minerals</h1></div><br />
             <?php //echo $field->label; ?>                            
                   <?php //echo $field->input; ?>                            
		<fieldset class="well">
            <input id="username" class="validate-username required" placeholder="Username" type="text" size="20" value="" name="username"/><br /><br />
            <input id="password" class="validate-password required" placeholder="Password" type="password" size="20" value="" name="password"/><br /><br />
			<?php //foreach ($this->form->getFieldset('credentials') as $field) : ?>
				<?php //if (!$field->hidden) : ?>
                   <?php //echo $field->label; ?>                            
                   <?php //echo $field->input; ?>                            
				<?php //endif; ?>
			<?php //endforeach; ?> 
			<div class="control-group">
				<div class="controls" align="right">
                    <button type="submit" class="btn btn-primary"><?php echo JText::_('JLOGIN'); ?></button>
				</div>
			</div>
            <input type="hidden" name="return" value="<?php echo base64_encode("./index.php"); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
</div>
<div id="device_authorisation_container" style="display:none;">
    <h1>Device Authorisation Required</h1><br />
    <table>
        <tr>
            <td>You don't have permission to access CRM. You need to request access.</td>
        </tr>
        <tr>
            <td align="right">
                <br /><br />
                <img src="custom/graphics/icons/blank.gif" id="btn_loader">
                <input type="button" id="request_access_btn" onclick="request_access();" value="Request Access">
            </td>
        </tr>
    </table>    
</div>
