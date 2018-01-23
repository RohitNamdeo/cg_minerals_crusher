<?php  // no direct access
    defined('_JEXEC') or die('Restricted access'); 
?>
<script type="text/javascript">
    function validateForm() // Function is used in onblure
    {
        if(document.getElementById('oldpassword').value=="")
        {
            document.getElementById('oldpassword').style.borderColor="#FF0000";
            document.getElementById('oldpassword').focus();
            document.getElementById('oldpassword').select();
            return false ;
        }
        if(document.getElementById('newpassword').value=="")
        {
            document.getElementById('newpassword').style.borderColor="#FF0000";
            document.getElementById('newpassword').focus();
            document.getElementById('newpassword').select();
            return false ;
        }
        if(document.getElementById('newpassword').value != document.getElementById('verifypassword').value)
        {
            document.getElementById('verifypassword').style.borderColor="#FF0000";
            document.getElementById('verifypassword').focus();
            document.getElementById('verifypassword').select();
            return false ;
        }
    }

    function reValidate(id) //Function is used in onchange
    {
            document.getElementById(id).style.borderColor="";
    }

</script>
<h1>Change Password for <? echo JFactory::getUser()->username; ?></h1>
<form enctype="multipart/form-data" action="" method="post" id="josForm" name="josForm" class="form-ValidatePasswordFields">
    <table align="left" cellpadding="5" cellspacing="5">
        <tr>
            <td colspan="2" align="center">
                <div id="loader"></div>
            </td>
        </tr>
        
        <tr>
            <td>
                <label id="oldpwmsg" for="password">
                    <?php echo JText::_( 'Old Password' ); ?>:
                </label>
            </td>
            <td>
                <input class="" type="password" id="oldpassword" name="oldpassword" size="40" value="" onblur="" onchange="reValidate('oldpassword')"/> *
            </td>
        </tr>
        <tr>
            <td>
                <label id="newpwmsg" for="password">
                    <?php echo JText::_( 'New Password' ); ?>:
                </label>
            </td>
            <td>
                <input class="" type="password" id="newpassword" name="newpassword" size="40" value="" onblur="" onchange="reValidate('newpassword')"/> *
            </td>
        </tr>
        <tr>
            <td>
                <label id="veryfipw" for="password2">
                    <?php echo JText::_( 'Verify Password' ); ?>:
                </label>
            </td>
            <td>
                <input class="" type="password" id="verifypassword" name="verifypassword" size="40" value="" onblur="" onchange="reValidate('verifypassword')"/> *
            </td>
        </tr>
        <tr>
            <td colspan="2" align="left">
                <input type="hidden" name="option" value="com_hr">
                <input type="hidden" name="task" value="change_password">
                <input type="submit" name="changepswd" value="Submit (Alt + Z)" onclick="return validateForm();">
                <input type="button" name="cancel" value="Cancel" onclick="history.go(-1)">
            </td>
        </tr>
    </table>
</form>