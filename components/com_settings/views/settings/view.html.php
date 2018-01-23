<?php
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class SettingsViewSettings extends JViewLegacy
{
	function display($tpl = null)
	{
        if(Functions::ifNotLoginRedirect())
        {
            return;
        }
        
        if (!Functions::has_permissions("settings", "settings"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }

		$db = JFactory::getDBO();
		$document = JFactory::getDocument();
		$document->setTitle( "Settings" );
        
        /* default credit days -> after passing these many days bill is shown in collection report */
        $query = "select `value_numeric` from `#__settings` where `key`='credit_days'";
        $db->setQuery($query);
        $credit_days = intval($db->loadResult());
        
        /* default location for all vouchers */
        $query = "select `value_numeric` from `#__settings` where `key`='default_location_id'";
        $db->setQuery($query);
        $default_location_id = intval($db->loadResult());
        
        /* sales invoice of this customer does not need separate payment, cash payment is received in bill creation itself */
        $query = "select `value_numeric` from `#__settings` where `key`='cash_sale_customer_id'";
        $db->setQuery($query);
        $cash_sale_customer_id = intval($db->loadResult());
        
        /* opening of cash in hand when s/w started */
        $query = "select `value_numeric` from `#__settings` where `key`='opening_cash_in_hand'";
        $db->setQuery($query);
        $opening_cash_in_hand = floatval($db->loadResult());
        
        /* next 3 settings are for bill print */
        $query = "select `value_string` from `#__settings` where `key`='mobile_no'";
        $db->setQuery($query);
        $mobile_no = $db->loadResult();
        
        $query = "select `value_string` from `#__settings` where `key`='tin_no'";
        $db->setQuery($query);
        $tin_no = $db->loadResult();
        
        $query = "select `value_string` from `#__settings` where `key`='invoice_footer'";
        $db->setQuery($query);
        $invoice_footer = $db->loadResult();
        
        /* to keep track of sms balance after sending sms */
        $query = "select `value_numeric` from `#__settings` where `key`='sms_balance'";
        $db->setQuery($query);
        $sms_balance = intval($db->loadResult());
        
        /* These user can receive payments on back date also */
        $query = "select `value_string` from `#__settings` where `key`='users_allowed_backdate_payments'";
        $db->setQuery($query);
        $users_allowed_backdate_payments = explode(",", $db->loadResult());
        
        $query = "select id, location_name from `#__inventory_locations` order by location_name";
        $db->setQuery($query);
        $locations = $db->loadObjectList();
        $this->locations = $locations;
        
        $query = "select id, customer_name from `#__customers` order by customer_name";
        $db->setQuery($query);
        $customers = $db->loadObjectList();
        $this->customers = $customers;
        
        $query = "select id, name from `#__users` order by name";
        $db->setQuery($query);
        $users = $db->loadObjectList();
        $this->users = $users;
        
        $query = "select id, product_name from `#__products` order by product_name";
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $this->products = $products;
        
        $query = "select value_string from `#__settings`  where `key`='self_gst_state_code'";
        $db->setQuery($query);
        $self_gst_state_code = $db->loadResult();
        $this->self_gst_state_code = $self_gst_state_code;
        
        $query = "select value_string from `#__settings`  where `key`='day_munshi_mobile_no'";
        $db->setQuery($query);
        $day_munshi_mobile_no = $db->loadResult();
        $this->day_munshi_mobile_no = $day_munshi_mobile_no;
        
        $query = "select value_string from `#__settings`  where `key`='night_munshi_mobile_no'";
        $db->setQuery($query);
        $night_munshi_mobile_no = $db->loadResult();
        $this->night_munshi_mobile_no = $night_munshi_mobile_no;
        
        $query = "select `value_string` from `#__settings` where `key`='gst_no'";
        $db->setQuery($query);
        $gst_no = $db->loadResult();
        
        $query = "select `value_numeric` from `#__settings` where `key`='product_type_diesel'";
        $db->setQuery($query);
        $product_type_diesel = $db->loadResult();
        
        $this->default_location_id = $default_location_id;
        $this->cash_sale_customer_id = $cash_sale_customer_id;
        $this->opening_cash_in_hand = $opening_cash_in_hand;
        $this->credit_days = $credit_days;
        $this->mobile_no = $mobile_no;
        $this->tin_no = $tin_no;
        $this->invoice_footer = $invoice_footer;
        $this->sms_balance = $sms_balance;
        $this->users_allowed_backdate_payments = $users_allowed_backdate_payments;
        $this->gst_no = $gst_no;
        $this->product_type_diesel = $product_type_diesel;
        
		parent::display($tpl);
	}
}
?>