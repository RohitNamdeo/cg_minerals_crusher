<?php
    jimport('joomla.application.component.controller');
    class TwolevelmenuController extends JControllerLegacy
    {
        function display($cachable = false, $urlparams = Array())
        {
            parent::display();
        }
        
        function create_menu()
        {
			$model = $this->getModel("twolevelmenu");
			$msg = $model->create_menu();
			if ($msg != "")
			{
			  $link = "index.php?option=com_twolevelmenu&view=menus";
			}
			else
			{
			  $link = "index.php?option=com_twolevelmenu&view=menudef";
			  $msg = "Unable to create menu!";
			}
			$this->setRedirect($link, $msg);
        }

        function update_menu()
        {
			$model = $this->getModel("twolevelmenu");
			$msg = $model->update_menu();
			if ($msg != "")
			{
			  $link = "index.php?option=com_twolevelmenu&view=menus";
			}
			else
			{
			  $link = "index.php?option=com_twolevelmenu&view=menudef&m=e&e=" . JRequest::getVar("me");
			  $msg = "Unable to update menu!";
			}
			$this->setRedirect($link, $msg);
        }
        
        function delete_menu()
        {
			$model = $this->getModel("twolevelmenu");
			$msg = $model->delete_menu();
			if ($msg == "")
			{
			  $msg = "Unable to delete menu!";
			}
			$link = "index.php?option=com_twolevelmenu&view=menus";
			$this->setRedirect($link, $msg);
        }
        
        function assign_permits()
        {
			$model = $this->getModel("twolevelmenu");
			$msg = $model->assign_permits();
			if ($msg != "")
			{
			  $link = "index.php?option=com_twolevelmenu&view=usergroups";
			}
			else
			{
			  $link = "index.php?option=com_twolevelmenu&view=usergroups";
			  $msg = "Unable to assign permissions!";
			}
			$this->setRedirect($link, $msg);
        }
    }
?>
