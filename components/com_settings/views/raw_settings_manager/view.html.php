<?php 
defined('_JEXEC') or die;
class settingsViewRaw_settings_manager extends JViewLegacy
{ 	
	public function display($tpl = null)
	{
        if(Functions::ifNotLoginRedirect())
        {
            return;
        }

        $db = JFactory::getDbo();
        $document = JFactory::getDocument();
        $document->setTitle( "Raw Settings Manager" );

        $settings = array();
        
        //$current_month = date("n");
        //$default_fy = ( $current_month < 4 ? date("y", strtotime("-1 year")) . "-" . date("y") : date("y") . "-" . date("y", strtotime("+1 year")) );

        //$settings["fy_year"] = array("found" => 0, "value_type" => 0, "default_value" => $default_fy);
        $settings["credit_days"] = array("found" => 0, "value_type" => 1 , "default_value" => 8);
        $settings["mobile_no"] = array("found" => 0, "value_type" => 0 , "default_value" => "" );
        $settings["day_munshi_mobile_no"] = array("found" => 0, "value_type" => 0 , "default_value" => "" );
        $settings["night_munshi_mobile_no"] = array("found" => 0, "value_type" => 0 , "default_value" => "" );
        
        $settings["tin_no"] = array("found" => 0, "value_type" => 0 , "default_value" => "" );
        $settings["invoice_footer"] = array("found" => 0, "value_type" => 0 , "default_value" => "" );
        $settings["cash_in_hand"] = array("found" => 0, "value_type" => 1 , "default_value" => 0 );
        $settings["opening_cash_in_hand"] = array("found" => 0, "value_type" => 1 , "default_value" => 0 );
        $settings["default_location_id"] = array("found" => 0, "value_type" => 1 , "default_value" => 0 );
        $settings["cash_sale_customer_id"] = array("found" => 0, "value_type" => 1 , "default_value" => 0 );
        $settings["sms_balance"] = array("found" => 0, "value_type" => 1 , "default_value" => 0 );
        $settings["users_allowed_backdate_payments"] = array("found" => 0, "value_type" => 0 , "default_value" => "265" );
        $settings["self_gst_state_code"] = array("found" => 0, "value_type" => 0 , "default_value" => "0" );
        $settings["gst_no"] = array("found" => 0, "value_type" => 0 , "default_value" => "0" );
        $settings["product_type_diesel"] = array("found" => 0, "value_type" => 1 , "default_value" => "0" );


        $query = "select * from #__settings";
        $db->setQuery($query);
        $existing_settings = $db->loadAssocList();
        
        $setting_to_create = 0;
        foreach($existing_settings as $setting)
        {
            if(isset($settings[$setting["key"]]))
            {
                $settings[$setting["key"]]["key"]=$setting["key"];
                $settings[$setting["key"]]["value_string"]=$setting["value_string"];
                $settings[$setting["key"]]["value_numeric"]=$setting["value_numeric"];
                $settings[$setting["key"]]["found"] = 1;
            }
        }
        
        $setting_to_create = count($settings) - count($existing_settings);
        
        $this->assignRef('existing_settings',$existing_settings);
        $this->assignRef('settings',$settings);
        $this->assignRef('setting_to_create',$setting_to_create);

        parent::display($tpl);
    }
}
?>