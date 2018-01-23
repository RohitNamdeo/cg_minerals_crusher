<?php // Check to ensure this file is included in Joomla!
    defined( '_JEXEC' ) or die( 'Restricted access' );
    jimport( 'joomla.application.component.view');

    class HrViewChangePassword extends JViewLegacy
    {
        function display($tpl = null)
        {
            global $mainframe;
            if (Functions::ifNotLoginRedirect("index.php"))
            {
                return;
            }
            $document = JFactory::getDocument();

            $document->setTitle('Change Password');
            
            // custom code of current user start
            $user_id= JFactory::getUser()->id;
            
            parent::display($tpl);
        }// End of function

    }//end of class
?>