<?php
class HrModelHr extends JModelLegacy
{
    function user_login()
    {
        $db = JFactory::getDBO();
        $username = JRequest::getVar('username');
        $password = JRequest::getVar('password');
        
        $app = JFactory::getApplication();
        $credentials = array();
        $credentials["username"] = $username;
        $credentials["password"] = $password;
        $options = array();
        
        $query = "select count(id) from `#__users` where `username`='" . $credentials['username'] ."'";
        $db->setQuery($query);
        $user_count = intval($db->loadResult());
        
        if($user_count == 0)
        {
            return "not_present";
        }
        
        $query = "select id, password from `#__users` where`username`='" . $credentials['username'] ."'";
        $db->setQuery($query);
        $result = $db->loadObject();
        
        $match = JUserHelper::verifyPassword($credentials['password'], $result->password, $result->id);
        
        if($match == true)
        {
            $result = $app->login($credentials, $options);
        }
        else
        {
            return "not_present";
        }
    }
    
    // designations
    
    function create_designation()
    {
        $db = JFactory::getDBO();
        $designation_name = ucwords(addslashes(JRequest::getVar("designation_name")));
        
        $query = "select count(*) from `#__designations` where `designation_name`='" . $designation_name . "'";
        $db->setQuery($query);
        $count = intval($db->loadResult());
        
        if($count > 0)
        {
            echo "Designation already exists.";
        }
        else
        {
            $query = "insert into `#__designations` (`designation_name`) values('" . $designation_name . "')";
            $db->setQuery($query);
            $db->query();
            
            Functions::log_activity("DESIGNATION CREATED : " . $designation_name . "");
        }
    }
    
    function designation_details()
    {
        $db = JFactory::getDBO();
        $designation_id = intval(JRequest::getVar("designation_id"));
        
        $query = "select `designation_name` from `#__designations` where id=" . $designation_id;
        $db->setQuery($query);
        $designation_name = $db->loadResult();
        echo json_encode($designation_name);
    }
    
    function update_designation()
    {
        $db = JFactory::getDBO();
        $designation_id = intval(JRequest::getVar("designation_id"));
        $designation_name = ucwords(addslashes(JRequest::getVar("designation_name")));
        
        $query = "select count(*) from `#__designations` where designation_name='" . $designation_name . "' and id<>" . $designation_id;
        $db->setQuery($query);
        $count = intval($db->loadResult());
        
        if($count > 0)
        {
            echo "Designation already exists.";
        }
        else
        {
            $query = "update `#__designations` set `designation_name`='" . $designation_name . "' where id=" . $designation_id;                                                
            $db->setQuery($query);
            $db->query();
            
            Functions::log_activity("DESIGNATION UPDATED : " . $designation_name . "");
            echo "ok";
        }
    }
    
    function delete_designation()
    {
        $db = JFactory::getDBO();
        $designation_id = JRequest::getVar("designation_id");
        $count = 0;
        
        $query = "select count(*) from `#__employeedetails` where designation_id=" . $designation_id;
        $db->setQuery($query);
        $count += intval($db->loadResult()); 
        
        if($count > 0)
        {
            return "Designation cannot be deleted. It has dependencies.";
        }
        else
        {
            $query = "select `designation_name` from `#__designations` where id=" . $designation_id;
            $db->setQuery($query);
            $designation_name = $db->loadResult();
            
            $query = "delete from `#__designations` where id=" . $designation_id;
            $db->setQuery($query);
            $db->query();
            
            Functions::log_activity("DESIGNATION DELETED : " . $designation_name . ""); 

            return "Designation deleted successfully.";
        }
    }
    
    // Software user
    
    function user_registration()
    {
        $db = JFactory::getDbo();
        date_default_timezone_set('Asia/Calcutta');
        $registered_by = JFactory::getUser()->id;
        srand((double)microtime()*10000);
        
        $first_name = JRequest::getVar("first_name"); 
        $last_name = JRequest::getVar("last_name"); 
        $full_name = JRequest::getVar("first_name") . " " . JRequest::getVar("last_name");
        $password = JRequest::getVar('password');
        $username = JRequest::getVar('username');
        $email = JRequest::getVar("email");
        $mobile_number = JRequest::getVar('mobile_number');
        $designation_id = intval(JRequest::getVar('designation_id'));
        
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $query = "select count(*) from #__users where `username` like '" . $username . "'";
        $db->setQuery($query);
        if (intval($db->loadResult()) > 0)
        {
            return "Username already exists, please choose another username to create the user account.";
        }
        
        $user = new JUser();
        $user->name = $full_name;
        $user->username = $username;
        $user->email = $email;
        $user->password = JUserHelper::hashPassword($password);
        $user->block = 0;
        $user->registerDate = date("Y-m-d H:i:s");
        $user->lastvisitDate = date("Y-m-d H:i:s");
        //$user->activation = md5( JUserHelper::genRandomPassword() );

        if (!$user->save())
        {
            return "An error occured while creating your account. The specific error message retrieved is: " . $this->getErrors();
        }
        
        $user_id = intval($user->id);
        
        $query = "insert into `#__user_usergroup_map` (user_id, group_id) values (".$user_id.", 2)";
        $db->setQuery($query);
        $db->query();
        
        $employee = new stdClass();
        $employee->first_name = $first_name;
        $employee->last_name = $last_name;
        $employee->mobile_number = $mobile_number;
        $employee->designation_id = $designation_id;
        $employee->registered_by = $registered_by;
        $employee->user_id = $user_id;
        
        if(!$db->insertObject("#__employeedetails", $employee, ""))
        {
            echo mysql_error();
            exit;
            return;
        }
        else
        {        
            $menu_permits = JRequest::getVar("permit");
            if (count($menu_permits) > 0)
            {
                foreach($menu_permits as $menu_id => $menu_permit)
                {   
                    $query = "insert into `#__employee_access_permits` (`user_id`,`menu_id`,`permit`) values('" . $user_id . "', '" . $menu_id . "','" . $menu_permit . "')";
                    $db->setQuery($query);
                    $db->query();            
                }
            }
            
            Functions::log_activity("New User " . $full_name .  " has been created.");
            return "Employee information saved!";
        }
    }
    
    function user_registration_update()
    {
        $db = JFactory::getDbo();
        
        $full_name = JRequest::getVar('first_name') . " " . JRequest::getVar('last_name');
        $first_name = JRequest::getVar('first_name');
        $last_name = JRequest::getVar('last_name');
        $password = JRequest::getVar('password');
        $email = JRequest::getVar('email');
        $mobile_number = JRequest::getVar('mobile_number');
        $designation_id = intval(JRequest::getVar("designation_id"));
        $employee_id = intval(JRequest::getVar("e"));
        
        $query = "select concat(first_name , ' ' , last_name) name from `#__employeedetails` where id=" . $employee_id;
        $db->setQuery($query);
        $employee_name = $db->loadResult();
        
        $employee = new stdClass();
        $employee->id = $employee_id;
        $employee->first_name = $first_name;
        $employee->last_name = $last_name;
        $employee->mobile_number = $mobile_number;
        $employee->designation_id = $designation_id;
        
        if(!$db->updateObject("#__employeedetails", $employee, "id"))
        {
            echo mysql_error();
            exit;
            return;
        }
        else
        {               
            $query = "select `user_id` from `#__employeedetails` where `id`=" . $employee_id;
            $db->setQuery($query);
            $user_id = intval($db->loadResult());
            
            if($password != "")
            {
                $query = "UPDATE `#__users` SET `name` ='".$full_name ."', `email`='".$email."', `password`='". JUserHelper::hashPassword($password) ."' where `id`=" .$user_id;    
            }
            else
            {
                $query = "UPDATE `#__users` SET `name` ='".$full_name ."', `email`='".$email."' where `id`=" .$user_id;
            }
            $db->setQuery($query);
            $db->query();
            
            $query = "delete from `#__employee_access_permits` where user_id=" . $user_id;
            $db->setQuery($query);
            $db->query();
            
            $menu_permits = JRequest::getVar("permit");
            if (count($menu_permits) > 0)
            {
                foreach($menu_permits as $menu_id => $menu_permit)
                {   
                    $query = "insert into `#__employee_access_permits`(`user_id`,`menu_id`,`permit`) values('" . $user_id . "', '" . $menu_id . "','" . $menu_permit . "')";
                    $db->setQuery($query);
                    $db->query();            
                }
            }
            
            Functions::log_activity("User " . $employee_name .  " has been updated.");
            return "Employee information updated!";
        }
    }
    
    function disable_user()
    {
        $db = JFactory::getDbo();
        
        $employee_user_id = intval(JRequest::getVar("employee_u_id"));
        
        $query = "select concat(first_name , ' ' , last_name) name from `#__employeedetails` where user_id=" . $employee_user_id;
        $db->setQuery($query);
        $employee_name = $db->loadResult();
        
        $query = "update `#__users` set password='' where id=" . $employee_user_id;
        $db->setQuery($query);
        if($db->query())
        {
            Functions::log_activity("User " . $employee_name . " has been disabled.");
            echo "ok";
        }        
    }
    
    function enable_user()
    {
        $db = JFactory::getDbo();
        
        $employee_user_id = intval(JRequest::getVar("employee_u_id"));
        
        $query = "select concat(first_name , ' ' , last_name) name from `#__employeedetails` where user_id=" . $employee_user_id;
        $db->setQuery($query);
        $employee_name = $db->loadResult();
        
        $password = (string)rand(10000, 99999);
        
        $new_password = $password;
        $new_password = JUserHelper::hashPassword($new_password);
        
        $query = "update `#__users` set `password`='" . $new_password . "' where id=" . $employee_user_id;
        $db->setQuery($query);
        if($db->query())
        {
            Functions::log_activity("User " . $employee_name . " has been enabled.");
            echo "User enabled successfully.Your password is " . $password;
        }        
    }
    
    function assign_permission()
    {
        $db = JFactory::getDbo();
        $designation_id = intval(JRequest::getVar("designation_id"));
        
        $query = "select `designation_name` from `#__designations` where id=" . $designation_id;
        $db->setQuery($query);
        $designation_name = $db->loadResult();
        
        $query = "delete from #__designation_access_permits where designation_id=" . $designation_id;
        $db->setQuery($query);
        $db->query();    
        
        $menu_permits = JRequest::getVar("permit");
        if (count($menu_permits) > 0)
        {
            foreach($menu_permits as $menu_id => $menu_permit)
            {   
                $query = "insert into #__designation_access_permits(`designation_id`,`menu_id`,`permit`) values('" . $designation_id . "', '" . $menu_id . "','" . $menu_permit . "')";
                $db->setQuery($query);
                $db->query();
            }
        }
        
        Functions::log_activity("Permissions for " . $designation_name . " has been changed.");
        return "Permissions changed successfully.";
    }
    
    function change_password()
    {
        $db = JFactory::getDBO();
        
        $old_password = JRequest::getVar("oldpassword");
        $new_password = JRequest::getVar("newpassword");
                
        $userid = intval(JFactory::getUser()->id);
        
        $query = "select `password` from `#__users` where `id`=" . $userid;
        $db->setQuery($query);
        $password = $db->loadResult();
        
        $match = JUserHelper::verifyPassword($old_password, $password, $userid);
        
        if($match == true)
        {
            $new_password = JUserHelper::hashPassword($new_password);
            
            $query = "update `#__users` set `password`='" . $new_password . "' where `id`=" . $userid;
            $db->setQuery($query);
            if($db->query())
            {
                $query = "select name from `#__users` where id=" . $userid;
                $db->setQuery($query);
                $user_name = $db->loadResult();
                
                Functions::log_activity("Password for user " . $user_name . " has been changed.");
                return "Your password has been changed successfully!";
            }
            else
            {
                return "Unable to change the password!";
            }
        }
        else
        {
            return "Incorrect old password.";
        }
    }
}
?>