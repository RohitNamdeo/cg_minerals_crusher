<?php
    jimport('joomla.application.component.controller');
    class HrController extends JControllerLegacy
    {
        function display($cachable = false, $urlparams = Array())
        {
          parent::display();
        }
        
        function user_login()
        {
            $model = $this->getModel('hr');
            $return = $model->user_login();
            $msg = "";
        
            /*if($return === "not_present")
            {
                $msg = JFactory::getApplication()->enqueueMessage(JText::_("User not present. Please contact your administrator."), 'error');
                  
            }*/
            
            if($return === "not_present")
            {
                $link = 'index.php?option=com_users';
                $this->setRedirect($link, 'User not present. Please contact your administrator.');
            }    
        }
        
        function create_designation()
        {
            $model = $this->getModel("hr");
            $model->create_designation();
        }
        
        function designation_details()
        {
            $model = $this->getModel("hr");
            $model->designation_details();
        }

        function update_designation()
        {
            $model = $this->getModel("hr");
            $model->update_designation();
        }

        function delete_designation()
        {
            $model = $this->getModel("hr");
            $msg = $model->delete_designation();
            $link = "index.php?option=com_hr&view=designations" ;
            $this->setRedirect($link, $msg);
        }
        
        function user_registration()
        {
            $model = $this->getModel("hr");
            $msg = $model->user_registration();
            $this->setRedirect("index.php?option=com_hr&view=user_management", $msg);
        }
        
        function user_registration_update()
        {
            $model = $this->getModel("hr");
            $msg = $model->user_registration_update();
            $this->setRedirect("index.php?option=com_hr&view=user_management", $msg);
        }
        
        function disable_user()
        {
            $model = $this->getModel("hr");
            $model->disable_user();        
        }
        
        function enable_user()
        {
            $model = $this->getModel("hr");
            $model->enable_user();        
        }
        
        function assign_permission()
        {
            $model = $this->getModel("hr");
            $msg = $model->assign_permission();
            $this->setRedirect("index.php?option=com_hr&view=role_management", $msg);    
        }
        
        function change_password()
        {
            $model = $this->getModel('hr');
            $msg = $model->change_password();
            $link = 'index.php?option=com_hr&view=changepassword';
            $this->setRedirect($link, $msg);
        }
        
        function check_attendance_machine_duplicity()
        {
            $model = $this->getModel("hr1");
            $model->check_attendance_machine_duplicity();
        }
        
        function employee_registration()
        {
            $model = $this->getModel('hr1');
            $msg = $model->employee_registration();
            $this->setRedirect("index.php?option=com_hr&view=employee_management", $msg);
        }
        
        function update_employee_profile()
        {
            $model = $this->getModel('hr1');
            $msg = $model->update_employee_profile();
            $this->setRedirect("index.php?option=com_hr&view=employee_management", $msg);
        }
        
        function activate_account()
        {
            $model = $this->getModel('hr1');
            $msg = $model->activate_account();
            $this->setRedirect("index.php?option=com_hr&view=employee_management", $msg);
        }
        
        function deactivate_account()
        {
            $model = $this->getModel('hr1');
            $msg = $model->deactivate_account();
            $this->setRedirect("index.php?option=com_hr&view=employee_management", $msg);
        }
        
        function generate_attendance_log()
        {
            $model = $this->getModel('hr2');
            $model->generate_attendance_log();
        }
        
        function generate_attendance()
        {
            $model = $this->getModel("hr2");
            $model->generate_attendance();
            $attendance_date = JRequest::getVar("attendance_date");
            $this->setRedirect("index.php?option=com_hr&view=attendance_entry&fnr=1&attendance_date=" . $attendance_date);
        }
        
        function update_attendance()
        {
            $model = $this->getModel("hr2");
            $msg = $model->update_attendance();
            $link = base64_decode(JRequest::getVar("r"));
            $this->setRedirect($link, $msg);
        }
        
        function delete_daily_attendance()
        {
            $model = $this->getModel("hr2");
            $msg = $model->delete_daily_attendance();
            $attendance_date = JRequest::getVar("attendance_date");
            $this->setRedirect("index.php?option=com_hr&view=daily_attendance_report&attendance_date=" . $attendance_date, $msg);
        }
        
        function generate_salary()
        {
            $model = $this->getModel("hr3");
            $msg = $model->generate_salary();
            $month = intval(JRequest::getVar("month"));
            $year = intval(JRequest::getVar("year"));
            $this->setRedirect("index.php?option=com_hr&view=generate_salary&month=" . $month . "&year=" . $year, $msg);
        }
        
        function delete_salary()
        {
            $model = $this->getModel("hr3");
            $msg = $model->delete_salary();
            $month = intval(JRequest::getVar('month'));
            $year = intval(JRequest::getVar('year'));
            $this->setRedirect("index.php?option=com_hr&view=delete_salary&month=" . $month . "&year=" . $year,$msg);
        }
        
        function save_salary_voucher()
        {
            $model = $this->getModel("hr3");
            $msg = $model->save_salary_voucher();
            $this->setRedirect("index.php?option=com_hr&view=salary_payment_voucher",$msg);
        }
        
        function update_salary_voucher()
        {
            $model = $this->getModel("hr3");
            $msg = $model->update_salary_voucher();
            $this->setRedirect("index.php?option=com_hr&view=salary_payment_report",$msg);
        }
        
        function delete_salary_voucher()
        {
            $model = $this->getModel("hr3");
            $msg = $model->delete_salary_voucher();
            $this->setRedirect("index.php?option=com_hr&view=salary_payment_report",$msg);
        }
        
        function save_advance_salary_voucher()
        {
            $model = $this->getModel("hr3");
            $msg = $model->save_advance_salary_voucher();
            $this->setRedirect("index.php?option=com_hr&view=advance_salary_payment_voucher",$msg);
        }
        
        function update_advance_salary_voucher()
        {
            $model = $this->getModel("hr3");
            $msg = $model->update_advance_salary_voucher();
            $this->setRedirect("index.php?option=com_hr&view=advance_payment_report",$msg);
        }
        
        function delete_advance_voucher()
        {
            $model = $this->getModel("hr3");
            $msg = $model->delete_advance_voucher();
            $this->setRedirect("index.php?option=com_hr&view=advance_payment_report",$msg);
        }
        
        function edit_advance_deduction()
        {
            $model = $this->getModel('hr3');
            $msg = $model->edit_advance_deduction();
            $month = intval(JRequest::getVar("month"));
            $year = intval(JRequest::getVar("year"));
            $this->setRedirect("index.php?option=com_hr&view=advance_deduction_report&month=" . $month . "&year=" . $year,$msg);
        }
    }
?>
