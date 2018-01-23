<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<script>
j(function(){
    j("#dummy_system_message").html(j("#system-message-container").html()); 
    j("#system-message-container").html("");
    j("#username").focus();
    j("body").html(j("#login-container").html());
});
</script>
<div id="login-container">
    <style> 
        body
        {
            background: url('templates/amittrading/images/login-bg.jpg');
            /*background-image: radial-gradient(circle, #FEFEFE, #787273);
            background-repeat: no-repeat;
            background-color: #A2B6E3; */
            font-family: arial;
        }
        
        fieldset
        {
            border: 0px;
        }
         
        .container
        {
            max-width: 340px;
            border: 1px solid #7D7D7D;
            margin: 8% auto; 
            padding: 10px;
            border-radius : 5px;
            /*background: #fff;*/
            box-shadow: 0px 0px 60px rgba(0, 0, 0, 0.5), 0px 1px 0px rgba(255, 255, 255, 0.9) inset;
            text-align: center;
            background: none repeat scroll 0% 0% #F9F7F7;
        }
         
        .control-group
        {
            margin: 10px 0px 0px;
        }
        
        .control-group label{
            text-align: right;    
        }
        
        #password {
            margin-left: 10px;
        }
        
        label
        {
            margin-right: 10px;
        }
        
        input[type="text"], input[type="password"] {
            width: 200px;
            padding: 4px 2px;
            border: 1px solid #8A8A8A;
            border-radius : 5px;
            color: #333333;
        }
        
        input[type="text"]:hover , input[type="password"]:hover 
        {
            border-color :#6cade1; 

            -moz-box-shadow: 0 0 5px #6cade1;
            -webkit-box-shadow: 0 0 5px #6cade1;
            box-shadow: 0 0 5px #6cade1;

            transition: all 0.10s ease-in-out;
            -webkit-transition: all 0.10s ease-in-out;
            -moz-transition: all 0.10s ease-in-out;
        }
        
        span.star
        {
            display: none;
        }
        
        .btn {
          /*background: #423c42;
          background-image: -webkit-linear-gradient(top, #423c42, #363636);
          background-image: -moz-linear-gradient(top, #423c42, #363636);
          background-image: -ms-linear-gradient(top, #423c42, #363636);
          background-image: -o-linear-gradient(top, #423c42, #363636);
          background-image: linear-gradient(to bottom, #423c42, #363636);*/
/*          background: #0f3657;*/
          background: #0FAFF3;
          background-image: -webkit-linear-gradient(top, #0f3657);
          background-image: -moz-linear-gradient(top, #0f3657);
          background-image: -ms-linear-gradient(top, #0f3657);
          background-image: -o-linear-gradient(top, #0f3657);
          background-image: linear-gradient(to bottom, #0f3657);
          -webkit-border-radius: 7;
          -moz-border-radius: 7;
          border-radius: 7px;
          color: #ffffff;
          font-size: 13px;
          padding: 3px 2px;
          text-decoration: none;
        }

        .btn:hover {
          /*background: #323333;
          background-image: -webkit-linear-gradient(top, #323333, #535557);
          background-image: -moz-linear-gradient(top, #323333, #535557);
          background-image: -ms-linear-gradient(top, #323333, #535557);
          background-image: -o-linear-gradient(top, #323333, #535557);
          background-image: linear-gradient(to bottom, #323333, #535557);*/
          border: 1px solid transparent !important;
          text-decoration: none;
        }
        
        .title
        {
/*            text-transform: uppercase;*/
            font-size: 15pt;
            padding: 10px 0px 10px 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            color: white;
/*            background-color: #0f3657;*/
            background-color: #0FAFF3;
        }
    </style>
    <div class="container<?php echo $this->pageclass_sfx?>">
        <hr class="examplefour">
        <div class="title">Login</div>
        <hr class="examplefour">
        
        <?php if ($this->params->get('show_page_heading')) : ?>
            <div class="page-header"  id="system_message">
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
        
        <!--<form action="<?php //echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-horizontal">-->
        <form action="<?php echo JRoute::_('index.php?option=com_hr&task=user_login'); ?>" method="post" class="form-horizontal">
            <fieldset class="well">
                <div id="dummy_system_message" style="margin: 0 -5px;"></div>
                <?php foreach ($this->form->getFieldset('credentials') as $field) : ?>
                    <?php if (!$field->hidden) : ?>
                        <div class="control-group">
                            <!--<div class="control-label">-->
                                <?php echo $field->label; ?>
                            <!--</div>-->
                            <!--<div class="controls">-->
                                <?php echo $field->input; ?>
                            <!--</div>-->
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="control-group">
                    <div class="controls" id="login-button" align="right" style="margin-right: 23px;">
                        <button type="submit" class="btn btn-primary"><?php echo JText::_('JLOGIN'); ?></button>
                    </div>
                </div>
                <input type="hidden" name="return" value="<?php echo base64_encode("./index.php"); ?>" />
                <?php echo JHtml::_('form.token'); ?>
            </fieldset>
        </form>
    </div>
</div>