<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class SettingsModelSettings extends JModelItem
{
    function save_settings()
    {
        $db = JFactory::getDBO();
        
        /*$fy_year = date("Y-m-d", strtotime(JRequest::getVar("fy_year")));
        
        $month = date("n", strtotime($fy_year));
        $year = date("y", strtotime($fy_year));
         
        $fy_year = ( $month < 4 ? ($year - 1) . "-" . $year : $year . "-" . ($year + 1) );

        $query = "update `#__settings` set value_string='" . $fy_year . "' where `key`='fy_year'";
        $db->setQuery($query);
        $db->query();*/
        
        $credit_days = intval(JRequest::getVar("credit_days"));
        $default_location_id = intval(JRequest::getVar("default_location_id"));
        $cash_sale_customer_id = intval(JRequest::getVar("cash_sale_customer_id"));
        $opening_cash_in_hand = floatval(JRequest::getVar("opening_cash_in_hand"));
        $mobile_no = JRequest::getVar("mobile_no");
        $day_munshi_mobile_no = JRequest::getVar("day_munshi_mobile_no");
        $night_munshi_mobile_no = JRequest::getVar("night_munshi_mobile_no");
        $tin_no = JRequest::getVar("tin_no");
        $gst_no = JRequest::getVar("gst_no");
        $invoice_footer = JRequest::getVar("invoice_footer");
        $self_gst_state_code = JRequest::getVar("self_gst_state_code");
        $product_type_diesel = JRequest::getVar("product_type_diesel");
        
        $users_allowed_backdate_payments = JRequest::getVar("users_allowed_backdate_payments");
        if($users_allowed_backdate_payments != "")
        {
            $users_allowed_backdate_payments = implode(",", $users_allowed_backdate_payments);
        }
       
        $query = "update `#__settings` set `value_numeric`=" . $credit_days . " where `key`='credit_days'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();
        
        $query = "update `#__settings` set `value_numeric`=" . $default_location_id . " where `key`='default_location_id'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();
        
        $query = "update `#__settings` set `value_numeric`=" . $cash_sale_customer_id . " where `key`='cash_sale_customer_id'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();
        
        $query = "update `#__settings` set `value_numeric`=" . $opening_cash_in_hand . " where `key`='opening_cash_in_hand'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();
        
        $query = "update `#__settings` set `value_string`='" . $mobile_no .  "' where `key`='mobile_no'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();
        
        $query = "update `#__settings` set `value_string`='" . $day_munshi_mobile_no .  "' where `key`='day_munshi_mobile_no'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();
        
        $query = "update `#__settings` set `value_string`='" . $night_munshi_mobile_no .  "' where `key`='night_munshi_mobile_no'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();        
       
        $query = "update `#__settings` set `value_string`='" . $tin_no . "' where `key`='tin_no'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();        
        
        $query = "update `#__settings` set `value_string`='" . $invoice_footer . "' where `key`='invoice_footer'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();        
        
        $query = "update `#__settings` set `value_string`='" . $users_allowed_backdate_payments . "' where `key`='users_allowed_backdate_payments'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg(); 
        
        $query = "update `#__settings` set value_string='" . $self_gst_state_code . "' where `key`='self_gst_state_code'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();
        
        $query = "update `#__settings` set value_string='" . $gst_no . "' where `key`='gst_no'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();  
        
        $query = "update `#__settings` set value_numeric='" . $product_type_diesel . "' where `key`='product_type_diesel'";
        $db->setQuery($query);
        $db->query();
        echo $db->getErrorMsg();            
        
        echo "ok";
    }
    
    function create_setting()
    {
       $db = JFactory::getDbo();
       
       $key = JRequest::getVar("key");
       $default_value = JRequest::getVar("default_value");
       $value_type = intval(JRequest::getVar("value_type"));
       
       $value_numeric = 0;
       $value_string = "";
       
       if($value_type == 1)
       {
           $value_numeric = floatval($default_value);
       }
       else if($value_type == 0)
       {
           $value_string = $default_value;
       }
       
       $query = "insert into #__settings(`key`,`value_string`,`value_numeric`,`value_type`) values('". $key ."','". $value_string ."',". $value_numeric .",". $value_type .")";
       $db->setQuery($query);
       if($db->query())
       {
           echo json_encode($default_value);
       }
    }
}
?>