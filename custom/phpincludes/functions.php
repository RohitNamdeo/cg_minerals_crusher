<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class Functions
{
    static function log_activity($message, $item_type="", $item_id="")
    {
        $db = JFactory::getDBO();
        $user_id = intval(JFactory::getUser()->id);
        $date = date("Y-m-d H:i:s");
        $message = str_replace("'","\\'", $message);
        $ip_address=$_SERVER['REMOTE_ADDR'];
        $query ="insert into `#__activity_log`(`user_id`,`item_type`,`item_id`,`message`,`timestamp`,`ip_address`) values(" . $user_id . ",'" . $item_type . "','" . $item_id . "','" . $message . "','" . $date . "','". $ip_address ."')";
        $db->setQuery($query);
        $db->query();  
    }

	static function getCurrentUserid()
	{
		global $mainframe;
		$user	= JFactory::getUser();
		$userid=$user->get('id');

		//check login
		if($userid==0 or $userid==null or $userid<1 or !$userid)
		{return false;
		}
			
		else return $userid;
	}//end of getCurrentUserid

	static function islogin()
	{
		global $mainframe;
		$user	= JFactory::getUser();
		$userid=$user->get('id');

		//check login
		if($userid==0 or $userid==null or $userid<1 or !$userid)
		{
			return false;
		}
		else
            return true;
	}//end of islogin
    
    static function in_strtotime($date)
    {
        if ($date == "")
        {
            return 0;
        }
        $date = str_replace("/", "-", $date);
        $date = str_replace("\\", "-", $date);
        $date_parts = explode("-", $date);
        if (isset($date_parts[1]))
        {
            if (is_numeric($date_parts[1]))
            {
                $date_parts[1] = date("M", mktime(0, 0, 0, $date_parts[1], 10));
            }
        }
        $date = implode("-", $date_parts);
        return strtotime($date);
    }

	static function ifNotLoginRedirect($url = "index.php")
	{
//		global $mainframe;
        $mainframe = JFactory::getApplication();
		$user	= JFactory::getUser();
		$userid=$user->get('id');

		//check login
		if($userid==0 or $userid==null or $userid<1 or !$userid)
		{
			//$link="index.php?option=com_user&view=login";
			$link="index.php?option=com_users&view=login" . ($url != "" ? "&return=" . base64_encode($url) : "");
			$msg=JText::_('You need log in first');
			$mainframe->redirect( $link, $msg );
            return true;
		}
        else
		    return false;
	}//end of ifNotLoginRedirect

	static function ifLoginRedirect()
	{
		global $mainframe;

		$user	= JFactory::getUser();
		$userid=$user->get('id');

		//check login
		if($userid>0)
		{
			//$link="index.php?option=com_user&view=login";
			$link=JURI::base();
			$mainframe->redirect( $link, $msg );
		}	
		return true;
	}//end of ifLoginRedirect
    
    static function deleteJoomlaUser($user_id)
    {
        $user =  JUser::getInstance($user_id);
        return $user->delete();
    }
    
    static function is_email($email){
        return preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email);
    }    
    
    static function is_valid_username($username){
        return preg_match("/^[a-zA-Z0-9_]+$/i", $username);
    }
    
    static function no_to_words($no)
    {    
        $words = array('0'=> '' ,'1'=> 'one' ,'2'=> 'two' ,'3' => 'three','4' => 'four','5' => 'five','6' => 'six','7' => 'seven','8' => 'eight','9' => 'nine','10' => 'ten','11' => 'eleven','12' => 'twelve','13' => 'thirteen','14' => 'fouteen','15' => 'fifteen','16' => 'sixteen','17' => 'seventeen','18' => 'eighteen','19' => 'nineteen','20' => 'twenty','30' => 'thirty','40' => 'fourty','50' => 'fifty','60' => 'sixty','70' => 'seventy','80' => 'eighty','90' => 'ninty','100' => 'hundred &','1000' => 'thousand','100000' => 'lakh','10000000' => 'crore');

        if($no == 0)
            return '';
        else
        {
            $novalue='';$highno=$no;$remainno=0;$value=100;$value1=1000;       
                while($no>=100)
                {
                    if(($value <= $no) &&($no  < $value1))
                    {
                        $novalue=$words[$value];
                        $highno = (int)($no/$value);
                        $remainno = $no % $value;
                        break;
                    }
                    $value= $value1;
                    $value1 = $value * 100;
                }       
              if(array_key_exists($highno,$words))
                  return $words[$highno]." " . $novalue . " ". Functions::no_to_words($remainno);
              else {
                 $unit=$highno%10;
                 $ten =(int)($highno/10)*10;            
                 return $words[$ten]." ".$words[$unit]." ".$novalue." ".Functions::no_to_words($remainno);
               }
        }
    }
    
    static function revert_customer_payment($customer_id, $payment_id, $payment_date)
    {
        // function to revert all the sales invoice adjustments done by the payment id sent as parameter and by the payments after that payment date
        $db = JFactory::getDBO();
        
        set_time_limit(0);
        
        $query = "select id from `#__payments` where party_id=" . $customer_id . " and payment_date>='" . $payment_date . "' and payment_type=" . CUSTOMER_PAYMENT . " and id>" . $payment_id;
        $db->setQuery($query);
        $payments = $db->loadObjectList();
        
        if(count($payments) > 0)
        {
            foreach($payments as $payment)
            {
                $payment_id = intval($payment->id);
                
                $query = "update `#__payments` set amount_adjusted=0 where id=" . $payment_id;
                $db->setQuery($query);
                $db->query();
                
                $query = "select invoice_id sales_id, amount from `#__payment_items` where payment_id=" .$payment_id;
                $db->setQuery($query);
                $payment_items  = $db->loadObjectList();
                
                foreach($payment_items as $item)
                {
                    $query = "update `#__sales_invoice` set amount_paid=amount_paid-" . floatval($item->amount) . ", status=" . UNPAID . " where id=" . intval($item->sales_id);
                    $db->setQuery($query);
                    $db->query();
                    
                    $query = "update `#__payment_items` set status=" . PART_PAYMENT . " where invoice_id=" . intval($item->sales_id) . " and payment_type=" . CUSTOMER_PAYMENT;
                    $db->setQuery($query);
                    $db->query();
                }
                
                $query = "delete from `#__payment_items` where payment_id=" .$payment_id;
                $db->setQuery($query);
                $db->query();
            }
        }
    }
    
    static function adjust_customer_account($customer_id, $sales_invoice_id = 0)
    {
        /*
        * to adjust customer account
        * all the pending payments are fetched
        * then for each payment, sales invoices with pending adjustment are fetched
        * amount-wise invoices are adjusted
        * then pending sales returns are selected
        * then for each return, sales invoices with pending adjustment are fetched
        * amount-wise invoices are adjusted
        * if $sales_invoice_id > 0 then this invoice is adjusted on priority
        */
        
        $db = JFactory::getDBO();
        
        set_time_limit(0);
        $query = "select id payment_id, (total_amount - amount_adjusted) amount_to_be_adjusted from `#__payments` where party_id=" . $customer_id . " and payment_type=" . CUSTOMER_PAYMENT . " and total_amount > amount_adjusted order by payment_date";
        $db->setQuery($query);
        $pending_payment_adjustments = $db->loadObjectList();

        if(count($pending_payment_adjustments) > 0)
        {
            $total_amount_adjusted = 0;
            $adjusted_amount = 0;
            
            foreach($pending_payment_adjustments as $payment)
            {
                $query = "select (total_amount - amount_paid) amount_pending, id sales_id from `#__sales_invoice` where customer_id=" . $customer_id . " and status=" . UNPAID . " and total_amount > amount_paid order by date";
                $db->setQuery($query);
                $pending_bills = $db->loadObjectList('sales_id');
                
                if(count($pending_bills) > 0)
                {
                    $adjusted_amount = 0;
                    
                    foreach($pending_bills as $bill)
                    {
                        if( floatval($payment->amount_to_be_adjusted) == floatval($bill->amount_pending) )
                        {
                            $payment_items = new stdClass();
                            
                            $payment_items->party_id = $customer_id;
                            $payment_items->invoice_id = intval($bill->sales_id);
                            $payment_items->payment_id = intval($payment->payment_id);
                            $payment_items->amount = $payment->amount_to_be_adjusted;
                            $payment_items->status = FULL_PAYMENT;
                            $payment_items->payment_type = CUSTOMER_PAYMENT;
                            
                            $db->insertObject("#__payment_items", $payment_items, "");
                            
                            $query = "update `#__payment_items` set status=" . FULL_PAYMENT . " where invoice_id=" . intval($bill->sales_id) . " and party_id=" . $customer_id . " and payment_type=" . CUSTOMER_PAYMENT;
                            $db->setQuery($query);
                            $db->query();
                            
                            $query = "update `#__sales_invoice` set amount_paid=amount_paid+" . floatval($payment->amount_to_be_adjusted) . ", status=" . PAID . " where id=" . intval($bill->sales_id);
                            $db->setQuery($query);
                            $db->query();
                            
                            unset($pending_bills[$bill->sales_id]);
                            $adjusted_amount += floatval($payment->amount_to_be_adjusted);
                            break;
                        }            
                        else if(floatval($payment->amount_to_be_adjusted) < floatval($bill->amount_pending))
                        {
                            $payment_items = new stdClass();
                            
                            $payment_items->party_id = $customer_id;
                            $payment_items->invoice_id = intval($bill->sales_id);
                            $payment_items->payment_id = intval($payment->payment_id);
                            $payment_items->amount = $payment->amount_to_be_adjusted;
                            $payment_items->status = PART_PAYMENT;
                            $payment_items->payment_type = CUSTOMER_PAYMENT;
                            
                            $db->insertObject("#__payment_items", $payment_items, "");
                            
                            $query = "update `#__sales_invoice` set amount_paid=amount_paid+" . floatval($payment->amount_to_be_adjusted) . " where id=" . intval($bill->sales_id);
                            $db->setQuery($query);
                            $db->query();
                            
                            $adjusted_amount += floatval($payment->amount_to_be_adjusted);
                            break;
                        }
                        else if(floatval($payment->amount_to_be_adjusted) > floatval($bill->amount_pending))
                        {
                            $payment_items = new stdClass();
                            
                            $payment_items->party_id = $customer_id;
                            $payment_items->invoice_id = intval($bill->sales_id);
                            $payment_items->payment_id = intval($payment->payment_id);
                            $payment_items->amount = floatval($bill->amount_pending);
                            $payment_items->status = FULL_PAYMENT;
                            $payment_items->payment_type = CUSTOMER_PAYMENT;
                            
                            $db->insertObject("#__payment_items", $payment_items, "");
                            
                            $payment->amount_to_be_adjusted -= floatval($bill->amount_pending);
                            
                            $query = "update `#__payment_items` set status=" . FULL_PAYMENT . " where invoice_id=" . intval($bill->sales_id) . " and party_id=" . $customer_id . " and payment_type=" . CUSTOMER_PAYMENT;
                            $db->setQuery($query);
                            $db->query();
                            
                            $query = "update `#__sales_invoice` set amount_paid=amount_paid+" . floatval($bill->amount_pending) . ", status=" . PAID . " where id=" . intval($bill->sales_id);
                            $db->setQuery($query);
                            $db->query();
                            
                            unset($pending_bills[$bill->sales_id]);
                            $adjusted_amount += floatval($bill->amount_pending);
                        }
                    }
                }
                
                $query = "update `#__payments` set amount_adjusted=amount_adjusted+" . $adjusted_amount . " where id=" . intval($payment->payment_id);
                $db->setQuery($query);
                $db->query();
                
                $total_amount_adjusted += $adjusted_amount;
            }
        }
        
        /*$query = "select (bill_amount - amount_adjusted) amount_to_be_adjusted, id sale_return_id from `#__sales_returns` where customer_id=" . $customer_id . " and status=" . UNPAID . " and bill_amount > amount_adjusted order by bill_date";
        $db->setQuery($query);
        $pending_sale_return_adjustments = $db->loadObjectList();
        
        if(count($pending_sale_return_adjustments) > 0)
        {
            $total_amount_adjusted = 0;

            $adjusted_amount = 0;
            foreach($pending_sale_return_adjustments as $sale_return)
            {   
                $query = "select (bill_amount - amount_paid) amount_pending, id sales_id from `#__sales_invoice` where customer_id=" . $customer_id . " and status=" . UNPAID . " and bill_amount > amount_paid and id<>" . $sales_invoice_id . " order by bill_date";
                $db->setQuery($query);
                $pending_bills = $db->loadObjectList('sales_id');
                
                if($sales_invoice_id > 0)
                {
                    $query = "select (bill_amount - amount_paid) amount_pending, id sales_id from `#__sales_invoice` where id=" . $sales_invoice_id . " and bill_amount > amount_paid";
                    $db->setQuery($query);
                    $pending_bill_to_be_adjusted_first = $db->loadObject();
                    
                    if(is_object($pending_bill_to_be_adjusted_first) && $pending_bill_to_be_adjusted_first->amount_pending > 0)
                    {
                        $pending_bills = array($sales_invoice_id=>$pending_bill_to_be_adjusted_first) + $pending_bills;
                    }
                }
                
                if(count($pending_bills) > 0)   
                {
                    $adjusted_amount = 0;
                    foreach($pending_bills as $bill)
                    {    
                        if( floatval($sale_return->amount_to_be_adjusted) == floatval($bill->amount_pending) )
                        {
                            $sr_adjustment_items = new stdClass();
                            
                            $sr_adjustment_items->sale_return_id = intval($sale_return->sale_return_id);
                            $sr_adjustment_items->invoice_id = intval($bill->sales_id);
                            $sr_adjustment_items->amount = $sale_return->amount_to_be_adjusted;
                            
                            $db->insertObject("#__sales_return_adjustment_items", $sr_adjustment_items, "");
                            
                            $query = "update `#__sales_invoice` set amount_paid=amount_paid+" . floatval($sale_return->amount_to_be_adjusted) . ", status=" . PAID . " where id=" . intval($bill->sales_id);
                            $db->setQuery($query);
                            $db->query();
                            
                            unset($pending_bills[$bill->sales_id]);
                            $adjusted_amount += floatval($sale_return->amount_to_be_adjusted);
                            break;
                        }            
                        else if(floatval($sale_return->amount_to_be_adjusted) < floatval($bill->amount_pending))
                        {
                            $sr_adjustment_items = new stdClass();
                            
                            $sr_adjustment_items->sale_return_id = intval($sale_return->sale_return_id);
                            $sr_adjustment_items->invoice_id = intval($bill->sales_id);
                            $sr_adjustment_items->amount = $sale_return->amount_to_be_adjusted;
                            
                            $db->insertObject("#__sales_return_adjustment_items", $sr_adjustment_items, "");
                            
                            $query = "update `#__sales_invoice` set amount_paid=amount_paid+" . floatval($sale_return->amount_to_be_adjusted) . " where id=" . intval($bill->sales_id);
                            $db->setQuery($query);
                            $db->query();
                            
                            $adjusted_amount += floatval($sale_return->amount_to_be_adjusted);
                            break;
                        }
                        else if(floatval($sale_return->amount_to_be_adjusted) > floatval($bill->amount_pending))
                        {
                            $sr_adjustment_items = new stdClass();
                            
                            $sr_adjustment_items->sale_return_id = intval($sale_return->sale_return_id);
                            $sr_adjustment_items->invoice_id = intval($bill->sales_id);
                            $sr_adjustment_items->amount = floatval($bill->amount_pending);
                            
                            $db->insertObject("#__sales_return_adjustment_items", $sr_adjustment_items, "");
                            
                            $sale_return->amount_to_be_adjusted -= floatval($bill->amount_pending);
                            
                            $query = "update `#__sales_invoice` set amount_paid=amount_paid+" . floatval($bill->amount_pending) . ", status=" . PAID . " where id=" . intval($bill->sales_id);
                            $db->setQuery($query);
                            $db->query();
                            
                            unset($pending_bills[$bill->sales_id]);
                            $adjusted_amount += floatval($bill->amount_pending);
                        }
                    }
                }
                
                $query = "update `#__sales_returns` set amount_adjusted=amount_adjusted+" . $adjusted_amount . (floatval($sale_return->amount_to_be_adjusted) == $adjusted_amount ? ", status=" . PAID : "") . " where id=" . intval($sale_return->sale_return_id);
                $db->setQuery($query);
                $db->query();
                
                $total_amount_adjusted += $adjusted_amount;
            }
        } */
    }
    
    static function revert_supplier_payment($supplier_id, $payment_id, $payment_date)
    {
        // function to revert all the purchase invoice adjustments done by the payment id sent as parameter and by the payments after that payment date
        $db = JFactory::getDBO();
        
        set_time_limit(0);
        
        $query = "select id from `#__payments` where party_id=" . $supplier_id . " and payment_date>='" . $payment_date . "' and payment_type=" . SUPPLIER_PAYMENT . " and id>" . $payment_id;
        $db->setQuery($query);
        $payments = $db->loadObjectList();
        
        if(count($payments) > 0)
        {
            foreach($payments as $payment)
            {
                $payment_id = intval($payment->id);
                
                $query = "update `#__payments` set amount_adjusted=0 where id=" . $payment_id;
                $db->setQuery($query);
                $db->query();
                
                $query = "select invoice_id purchase_id, amount from `#__payment_items` where payment_id=" .$payment_id;
                $db->setQuery($query);
                $payment_items  = $db->loadObjectList();
                
                foreach($payment_items as $item)
                {
                    $query = "update `#__purchase` set amount_paid=amount_paid-" . floatval($item->amount) . ", status=" . UNPAID . " where id=" . intval($item->purchase_id);
                    $db->setQuery($query);
                    $db->query();
                    
                    $query = "update `#__payment_items` set status=" . PART_PAYMENT . " where invoice_id=" . intval($item->purchase_id) . " and payment_type=" . SUPPLIER_PAYMENT;
                    $db->setQuery($query);
                    $db->query();
                }
                
                $query = "delete from `#__payment_items` where payment_id=" .$payment_id;
                $db->setQuery($query);
                $db->query();
            }
        }
    }
    
    static function adjust_supplier_account($supplier_id)
    {
        /*
        * to adjust supplier account
        * all the pending payments are fetched
        * then for each payment, purchase invoices with pending adjustment are fetched
        * amount-wise invoices are adjusted
        * then pending purchase returns are selected
        * then for each return, purchase invoices with pending adjustment are fetched
        * amount-wise invoices are adjusted
        */
        
        $db = JFactory::getDBO();
        
        set_time_limit(0);
        $query = "select id payment_id, (total_amount - amount_adjusted) amount_to_be_adjusted from `#__payments` where party_id=" . $supplier_id . " and payment_type=" . SUPPLIER_PAYMENT . " and total_amount > amount_adjusted order by payment_date";
        $db->setQuery($query);
        $pending_payment_adjustments = $db->loadObjectList();
        
        

        if(count($pending_payment_adjustments) > 0)
        {
            $total_amount_adjusted = 0;
            
            $adjusted_amount = 0;
            foreach($pending_payment_adjustments as $payment)
            {
                $query = "select (total_amount - amount_paid) amount_pending, id purchase_id from `#__purchase` where supplier_id=" . $supplier_id . " and status=" . UNPAID . " and total_amount > amount_paid order by bill_date";
                $db->setQuery($query);
                $pending_bills = $db->loadObjectList('purchase_id');
                
                
                if(count($pending_bills) > 0)
                {
                    $adjusted_amount = 0;
                    foreach($pending_bills as $bill)
                    {
                        if( floatval($payment->amount_to_be_adjusted) == floatval($bill->amount_pending) )
                        {
                            $payment_items = new stdClass();
                            
                            $payment_items->party_id = $supplier_id;
                            $payment_items->invoice_id = intval($bill->purchase_id);
                            $payment_items->payment_id = intval($payment->payment_id);
                            $payment_items->amount = $payment->amount_to_be_adjusted;
                            $payment_items->status = FULL_PAYMENT;
                            $payment_items->payment_type = SUPPLIER_PAYMENT;
                            
                            $db->insertObject("#__payment_items", $payment_items, "");
                            
                            $query = "update `#__payment_items` set status=" . FULL_PAYMENT . " where invoice_id=" . intval($bill->purchase_id) . " and party_id=" . $supplier_id . " and payment_type=" . SUPPLIER_PAYMENT;
                            $db->setQuery($query);
                            $db->query();
                            
                            $query = "update `#__purchase` set amount_paid=amount_paid+" . floatval($payment->amount_to_be_adjusted) . ", status=" . PAID . " where id=" . intval($bill->purchase_id);
                            $db->setQuery($query);
                            $db->query();
                            
                            unset($pending_bills[$bill->purchase_id]);
                            $adjusted_amount += floatval($payment->amount_to_be_adjusted);
                            break;
                        }            
                        else if(floatval($payment->amount_to_be_adjusted) < floatval($bill->amount_pending))
                        {
                            $payment_items = new stdClass();
                            
                            $payment_items->party_id = $supplier_id;
                            $payment_items->invoice_id = intval($bill->purchase_id);
                            $payment_items->payment_id = intval($payment->payment_id);
                            $payment_items->amount = $payment->amount_to_be_adjusted;
                            $payment_items->status = PART_PAYMENT;
                            $payment_items->payment_type = SUPPLIER_PAYMENT;
                            
                            $db->insertObject("#__payment_items", $payment_items, "");
                            
                            $query = "update `#__purchase` set amount_paid=amount_paid+" . floatval($payment->amount_to_be_adjusted) . " where id=" . intval($bill->purchase_id);
                            $db->setQuery($query);
                            $db->query();
                            
                            $adjusted_amount += floatval($payment->amount_to_be_adjusted);
                            break;
                        }
                        else if(floatval($payment->amount_to_be_adjusted) > floatval($bill->amount_pending))
                        {
                            $payment_items = new stdClass();
                            
                            $payment_items->party_id = $supplier_id;
                            $payment_items->invoice_id = intval($bill->purchase_id);
                            $payment_items->payment_id = intval($payment->payment_id);
                            $payment_items->amount = floatval($bill->amount_pending);
                            $payment_items->status = FULL_PAYMENT;
                            $payment_items->payment_type = SUPPLIER_PAYMENT;
                            
                            $db->insertObject("#__payment_items", $payment_items, "");
                            
                            $payment->amount_to_be_adjusted -= floatval($bill->amount_pending);
                            
                            $query = "update `#__payment_items` set status=" . FULL_PAYMENT . " where invoice_id=" . intval($bill->purchase_id) . " and party_id=" . $supplier_id . " and payment_type=" . SUPPLIER_PAYMENT;
                            $db->setQuery($query);
                            $db->query();
                            
                            $query = "update `#__purchase` set amount_paid=amount_paid+" . floatval($bill->amount_pending) . ", status=" . PAID . " where id=" . intval($bill->purchase_id);
                            $db->setQuery($query);
                            $db->query();
                            
                            unset($pending_bills[$bill->purchase_id]);
                            $adjusted_amount += floatval($bill->amount_pending);
                        }
                    }
                }
                
                $query = "update `#__payments` set amount_adjusted=amount_adjusted+" . $adjusted_amount . " where id=" . intval($payment->payment_id);
                $db->setQuery($query);
                $db->query();
                
                $total_amount_adjusted += $adjusted_amount;
            }
        }
    }
    
    public static function has_permissions($component_name, $view_name)
    {
        $db = JFactory::getDbo();
        
        $user_id = intval(JFactory::getUser()->id);
        if ($user_id <= 0)
        {
            return false;
        }
        
        if($user_id == 265 || $user_id == 266)
        {
            return true;
        }
        
        $query = "select * from `#__menuitems` where `option` like 'com_" . $component_name . "' and `view` like '" . $view_name . "'";
        $db->setQuery($query);
        $menu_item = $db->loadObject();
        if (!is_object($menu_item))
        {
            return false;
        }
        $menu_id = $menu_item->id;
        
        $query = "select `designation_id` from `#__employeedetails` where user_id=" . $user_id;
        $db->setQuery($query);
        $designation_id = intval($db->loadResult());

        $query = "select permit from `#__employee_access_permits` where user_id=" . $user_id . " and menu_id=" . $menu_id;
        $db->setQuery($query);
        $user_permit = $db->loadResult();

        if (!is_numeric($user_permit))
        {
            $user_permit = PERMIT_INHERIT_FROM_ROLE;
        }
        else
        {
            $user_permit = intval($user_permit);
        }

        $query = "select permit from `#__designation_access_permits` where designation_id=" . $designation_id . " and menu_id=" . $menu_id;
        $db->setQuery($query);
        $role_permit = $db->loadResult();

        if (!is_numeric($role_permit))
        {
            $role_permit = NO;
        }
        else
        {
            $role_permit = intval($role_permit);
        }
        
        if($user_permit == NO)
        {
            return false;
        }
        else if($user_permit == YES)
        {
            return true;
        }
        else if($user_permit == PERMIT_INHERIT_FROM_ROLE)
        {
            if($role_permit == NO)
            {
                return false;
            }
            else if($role_permit == YES)
            {
                return true;
            }
        }
        
        /*if (($role_permit == YES || $role_permit == NO) && $user_permit == YES)
        {
            return true;
        }
        else if (($role_permit == YES) && $user_permit == PERMIT_INHERIT_FROM_ROLE)
        {
            return true;
        }
        else
        {
            return false;
        }*/
    }
    
    static function send_sms($to_mobile_array, $sms, $senderid = "")
    {
        $db = JFactory::getDbo();
        
        $sms_len = ceil(strlen($sms) / 140);
        $recepient_count = 0;
        
        if (is_array($to_mobile_array))
        {
            foreach($to_mobile_array as $mobile_num)
            {
                if (is_numeric($mobile_num))
                {
                    $recepient_count++;
                    Functions::smsapi_nc($mobile_num, $sms, $senderid);
                }
            }
        }
        else
        {
            if (is_numeric($to_mobile_array))
            {
                $recepient_count++;
                Functions::smsapi_nc($to_mobile_array, $sms, $senderid);
            }
        }
    }
    
    static function smsapi_nc($mobile_num, $message, $senderid = "")
    {
        $feedid   = '334052' ;
        if ($senderid == "")
        {
            $senderid = 'AMITTR';
        }
        $username = '9893032091';
        $password = 'twwgj';
        $url = 'http://bulkpush.mytoday.com/BulkSms/SingleMsgApi';
        $fields = array(
                        'Text'=>urlencode($message),
                        'feedid'=>urlencode($feedid),
                        'senderid'=>urlencode($senderid),
                        'username'=>urlencode($username),
                        'password'=>urlencode($password),
                        'time'=>urlencode(""),
                        'To'=>urlencode($mobile_num)
        );
        $fields_string="";
        foreach($fields as $key=>$value)
        {
            $fields_string .= $key.'='.$value.'&';
        }
        $fields_string=rtrim($fields_string,'&');
        //open connection
        $ch = curl_init($url . "?" . $fields_string);
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_POST,count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);            
        //execute post
        $result = curl_exec($ch);
        if($result)
        {
            $db = JFactory::getDbo();
            
            $query = "update `#__settings` set `value_numeric`=`value_numeric`-1 where `key`='sms_balance'";
            $db->setQuery($query);
            $db->query();
            //echo "ok";
        }
        //close connection
        //echo curl_error($ch);
        curl_close($ch);
        //echo "<pre>";
        //echo htmlentities(  $result);
        //echo "</pre>";
    }
    
    static function ImageResize($filepath,$savefilepath,$imagetype,$w,$h)
    {
        //error_log("Image resize: " . print_r(func_get_args(), true));
        $image = new SimpleImage();
        $image->load($filepath);
        $image->resize($w,$h);  
        $image->save($savefilepath,$imagetype);
        
        $filename=$filepath;
        $full_url=$savefilepath;
        
        // Get new dimensions
        list($width, $height) = getimagesize($filename);
        // image width
        $sw = $width;//$x[0];
        // image height
        $sh = $height;//$x[1];
        $fixH=0;
        $fixW=0;
        if (true)
        {
            $w_scale = $w/$sw;
            $h_scale = $h/$sh;
            //at least one of the two scaling factors is less than 1
            if (!($h_scale > 1) || !($w_scale > 1))
            {
                if ($h_scale <= $w_scale)
                {
                    $new_height = $h;
                    $new_width = floor($h_scale * $sw);
                }
                else
                {
                    $new_width = $w;
                    $new_height = $w_scale * $sh;
                }
            }
            elseif ($h_scale > 1 && $w_scale > 1)
            {
                $new_height = $sh;
                $new_width = $sw;
            }
            else
            {
                echo "Unable to resize image"; exit;
            }
        }
        // Resample
        $image_p = imagecreatetruecolor($new_width, $new_height);
        if(strtolower($imagetype)=="jpg"||strtolower($imagetype)=="jpeg")
        {
            $image = imagecreatefromjpeg($filename);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            if (!imagejpeg($image_p,$full_url,100))
            {
            }
        }
        elseif(strtolower($imagetype)=="gif")
        {
            $image = imagecreatefromgif($filename);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagegif($image_p,$full_url,100);
        }
        elseif(strtolower($imagetype)=="png")
        {
            $image = imagecreatefrompng($filename);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagepng($image_p,$full_url,100);
        }

        @imagedestroy($image); 
        $imageErr=-1;
        return $imageErr;

    }
    
    static function get_setting($setting_name)
    {
        $db = JFactory::getDbo();
        
        $query = "select * from `#__settings` where `key`='" . $setting_name .  "'";
        $db->setQuery($query);
        if($setting = $db->loadObject())
        {
            if(intval($setting->value_type) == 1)
            {
                return floatval($setting->value_numeric);
            }
            else
            {
                return $setting->value_string;
            }
        }
    }
    
    static function get_gst_state_code($state_id)
    {
        $db = JFactory::getDbo();
        
        $query = "select gst_state_code from `#__states` where `id`=" . $state_id;
        $db->setQuery($query);
        $gst_state_code = $db->loadResult();
        
        return $gst_state_code;
    }
    static function adjust_transporter_account($transporter_id, $total_amount_pending_to_be_setteled)
    {
        $db = JFactory::getDBO();
        $query = "select id payment_id, (total_amount - amount_adjusted) amount_to_be_adjusted, payment_date from `#__transporter_payments` where transporter_id=" . $transporter_id . " and status=".NOT_ADJUSTED." order by payment_date";
        $db->setQuery($query);
        $pending_payment_adjustments = $db->loadObjectList();
        if(count($pending_payment_adjustments) > 0)
        {            
            foreach($pending_payment_adjustments as $pending_payment_adjustment)
            {
                if($pending_payment_adjustment->amount_to_be_adjusted >= $total_amount_pending_to_be_setteled)
                {
                    $payment_amount_to_be_settled = $total_amount_pending_to_be_setteled;
                }
                else
                {
                    $payment_amount_to_be_settled = $pending_payment_adjustment->amount_to_be_adjusted;                     
                }
                echo $payment_amount_to_be_settled."<br />payment amount to be setteled<br />";
                Functions::adjust_transporter_payments_to_sales_invoices($pending_payment_adjustment->payment_date, $transporter_id, $payment_amount_to_be_settled, $remarks, $pending_payment_adjustment->payment_id);
            }  
        }

    }
    
    static function adjust_transporter_payments_to_sales_invoices($payment_date, $transporter_id, $amount, $remarks, $payment_id = 0)
    {
        //echo $payment_date."--".$transporter_id."--".$amount."--".$remarks."--".$payment_id;
        $db = JFactory::getDBO(); 
        $query = "(select * from `#__sales_invoice` where transporter_id = " . $transporter_id . ") union ( select * from `#__sales_invoice` where loading_transporter_id = " .$transporter_id. ")";
        $db->setQuery($query);
        $sales_invoice_details = $db->loadObjectList();
        print_r($sales_invoice_details);

        foreach($sales_invoice_details as $sales_invoice_detail)
        {
            $transporter = $sales_invoice_detail->transporter_id;
            $loader = $sales_invoice_detail->loading_transporter_id;
            $amount_pending = 0;  
            
            if($transporter == $transporter_id && $loader == $transporter_id)
            {
                $amount_pending = (($sales_invoice_detail->vehicle_rate + $sales_invoice_detail->loading_amount) - ($sales_invoice_detail->transportation_amount_paid + $sales_invoice_detail->loading_amount_paid));
                $sales_invoice_detail->type = TRANSPORTER_LOADER;
            }   
            else if($transporter == $transporter_id && $loader != $transporter_id)
            {
                $amount_pending =  ($sales_invoice_detail->vehicle_rate - $sales_invoice_detail->transportation_amount_paid);
                $sales_invoice_detail->type = TRANSPORTER;
            }   
            else if($transporter != $transporter_id && $loader == $transporter_id)
            {
                $amount_pending =  ($sales_invoice_detail->loading_amount - $sales_invoice_detail->loading_amount_paid);
                $sales_invoice_detail->type = LOADER;
            }
            $sales_invoice_detail->amount_pending = $amount_pending;
        }
        $pending_amounts = $sales_invoice_details;
        $total_amount = $amount;
        
        $payment = new stdClass();
        $payment->transporter_id = $transporter_id;
        $payment->payment_date = $payment_date;
        $payment->payment_type = CREDIT;
        $payment->remarks = $remarks;
        $payment->entry_by = intval(JFactory::getUser()->id);
        $payment->entry_date = date("Y-m-d");
        
        if($payment_id != 0)
        {
            $payment->id = $payment_id;
            $query = "select COUNT(id) from `#__transporter_payments` where id=". $payment_id; 
            $db->setQuery($query);
            $count = $db->loadResult();
            if($count > 0)
            {
               $db->updateObject("#__transporter_payments", $payment, "id");    
            }
            else
            {
                $payment->total_amount = $total_amount;
                $db->insertObject("#__transporter_payments", $payment, ""); 
                $payment_id = intval($db->insertid());        
            }
        }
        else
        {
            $payment->total_amount = $total_amount;
            $db->insertObject("#__transporter_payments", $payment, "");   
            $payment_id = intval($db->insertid());             
        }

        foreach($pending_amounts as $amt)
        {
            if($amt->amount_pending > 0 && $amount > 0)
            {                   
                if($amount < floatval($amt->amount_pending))
                {
                    $payment_items = new stdClass();                    
                    $payment_items->transporter_id = intval($transporter_id);
                    $payment_items->invoice_id = intval($amt->id);
                    $payment_items->transporter_payment_id = $payment_id;
                    $payment_items->status = PART_PAYMENT;
                        
                    if($amt->type == TRANSPORTER_LOADER)
                    {
                        $transportation_amount_paid = $amt->transportation_amount_paid;
                        $loading_amount_paid = $amt->loading_amount_paid;
                        
                        $pending_transportation_cost = $amt->vehicle_rate - $amt->transportation_amount_paid;
                        $pending_loading_cost = $amt->loading_amount - $amt->loading_amount_paid;
                        
                        if($pending_transportation_cost <= $amount)
                        {
                            if($pending_transportation_cost != 0)
                            {
                                $payment_items->amount = floatval($pending_transportation_cost);
                                $payment_items->transporter_type = TRANSPORTER;                        
                                $payment_items->status = FULL_PAYMENT;
                                $db->insertObject("#__transporter_payment_items", $payment_items, "");
                                
                                $amount -= $pending_transportation_cost;
                                $transportation_amount_paid = $amt->vehicle_rate;
                            }
                            
                            if($amount > 0 && $pending_loading_cost > 0)
                            {                    
                                $payment_items->amount = floatval($amount);
                                $payment_items->transporter_type = LOADER;                       
                                $db->insertObject("#__transporter_payment_items", $payment_items, "");
                                
                                $loading_amount_paid = $amt->loading_amount_paid + $amount;
                                $amount = 0;
                            }                            
                        }
                        else
                        {
                            $payment_items->amount = floatval($amount);
                            $payment_items->transporter_type = TRANSPORTER;                    
                            $db->insertObject("#__transporter_payment_items", $payment_items, "");
                            
                            $transportation_amount_paid += $amount;
                            $amount = 0;                            
                        }
                        $query = "update `#__sales_invoice` set transportation_amount_paid=" . $transportation_amount_paid . ", loading_amount_paid=" .$loading_amount_paid. " where id=" . intval($amt->id);         
                    }
                    else if($amt->type == TRANSPORTER)
                    {
                        $payment_items->amount = floatval($amount);
                        $payment_items->transporter_type = TRANSPORTER;
                        $db->insertObject("#__transporter_payment_items", $payment_items, "");
                        $query = "update `#__sales_invoice` set transportation_amount_paid=transportation_amount_paid+" . $amount . " where id=" . intval($amt->id);        
                        $amount = 0;  
                    }
                    else if($amt->type == LOADER)
                    {
                        $payment_items->amount = floatval($amount);
                        $payment_items->transporter_type = LOADER;
                        $db->insertObject("#__transporter_payment_items", $payment_items, "");
                        
                        $query = "update `#__sales_invoice` set loading_amount_paid=loading_amount_paid+" . $amount . " where id=" . intval($amt->id);        
                        $amount = 0;
                    }
                    $db->setQuery($query);
                    $db->query();
                }
                else if($amount >= floatval($amt->amount_pending))
                {
                    $payment_items = new stdClass();                    
                    $payment_items->transporter_id = $transporter_id;
                    $payment_items->invoice_id = intval($amt->id);
                    $payment_items->transporter_payment_id = $payment_id;
                    $payment_items->status = FULL_PAYMENT;    
                    
                    if($amt->type == TRANSPORTER_LOADER)
                    {
                        $transportation_amount_paid = $amt->transportation_amount_paid;
                        $loading_amount_paid = $amt->loading_amount_paid;
                        
                        $pending_transportation_cost = $amt->vehicle_rate - $amt->transportation_amount_paid;
                        if($pending_transportation_cost > 0)
                        {
                            $payment_items->amount = floatval($pending_transportation_cost);
                            $payment_items->transporter_type = TRANSPORTER;
                            $db->insertObject("#__transporter_payment_items", $payment_items, "");
                        }
                        
                        $pending_loading_cost = $amt->loading_amount - $amt->loading_amount_paid;
                        $payment_items->amount = floatval($pending_loading_cost);
                        $payment_items->transporter_type = LOADER;
                        $db->insertObject("#__transporter_payment_items", $payment_items, "");
                        
                            $amount = $amount - $pending_transportation_cost - $pending_loading_cost;
                        $query = "update `#__sales_invoice` set transportation_amount_paid=" . $amt->vehicle_rate . ", loading_amount_paid=" .$amt->loading_amount. " where id=" . intval($amt->id);         
                    }
                    else if($amt->type == TRANSPORTER)
                    {
                        $payment_items->amount = floatval($amt->amount_pending);
                        $payment_items->transporter_type = TRANSPORTER;                    
                        $db->insertObject("#__transporter_payment_items", $payment_items, ""); 
                        
                        $query = "update `#__sales_invoice` set transportation_amount_paid= " . $amt->vehicle_rate . " where id=" . intval($amt->id);        
                        $amount = $amount - ($amt->vehicle_rate - $amt->transportation_amount_paid); 
                    }
                    else if($amt->type == LOADER)
                    {
                        $payment_items->amount = floatval($amt->amount_pending);
                        $payment_items->transporter_type = LOADER;
                        $db->insertObject("#__transporter_payment_items", $payment_items, ""); 

                        $query = "update `#__sales_invoice` set loading_amount_paid= " . $amt->loading_amount . " where id=" . intval($amt->id);        
                        $amount = $amount - ($amt->loading_amount - $amt->loading_amount_paid);
                    }
                    
                    $db->setQuery($query);
                    $db->query();
                    print_r($db);
                }
            }
        }  

        $query = "select sum(amount) from #__transporter_payment_items where transporter_payment_id = " . $payment_id;
        $db->setQuery($query);
        $amount_adjusted = $db->loadResult();
        
        $query = "select total_amount from #__transporter_payments where id = " . $payment_id;
        $db->setQuery($query);
        $total_amount_paid = $db->loadResult();
        $status = 0;
        
        if($amount_adjusted != "" && $total_amount_paid!= "")
        {
            if($total_amount_paid == $amount_adjusted)
                $status = 1;
            
            $query = "UPDATE `#__transporter_payments` SET `amount_adjusted`=" .$amount_adjusted. ",`status`=" .$status. " WHERE `id` = " . $payment_id;
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "select sum(total_pay) from ((select sum(vehicle_rate - (add_cash + diesel_total_amount)) total_pay from #__sales_invoice where transporter_id = " . $transporter_id . ") union (select sum(loading_amount) total_pay from #__sales_invoice where loading_transporter_id = " . $transporter_id . "))b";
        $db->setQuery($query);
        $outstanding_payment = floatval($db->loadResult());
        
        $query = "select sum(total_amount) from `#__transporter_payments` where transporter_id = " . $transporter_id;
        //echo $query;exit;
        //$query = "select sum(total_amount) from `#__transporter_payments` transporter_id = " . $transporter_id ." union select sum(total_amount) from `#__transporter_payments` transporter_id=".$loading_transporter_id;
        $db->setQuery($query);
        $amount_paid = floatval($db->loadResult());
        
        //echo "<br>" . $outstanding_payment . " " . $amount_paid;
        
//        echo "<br>Account Balance: " . $account_balance . " Total Amount: " . $total_amount;
       // if($account_balance < 0 )
//            $account_balance = $account_balance + $total_amount;
//        else
//            $account_balance = $account_balance - $total_amount;

//        if($account_balance < 0 ) {
//            if($total_transport_amount > 0)
//                $account_balance = $account_balance + $total_amount - $total_transport_amount;
//            else
//                $account_balance = $account_balance + $total_amount; 
//        }
//        else
//        {
//            if($total_transport_amount > 0)
//                $account_balance = $account_balance - $total_amount + $total_transport_amount;
//            else
//                $account_balance = $account_balance - $total_amount;    
//        }
//            
//        
            
            
        
        $query = "update `#__transporters` set `account_balance`=" . floatval($outstanding_payment - $amount_paid) . " where id=" . $transporter_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "select `transporter_name` from `#__transporters` where id=" . $transporter_id;
        $db->setQuery($query);
        $transporter = $db->loadResult();   
    }
    
    
    static function delete_transporter_payments_of_sales_invoices($payment_id, $transporter_id)
    {
        $db = JFactory::getDbo();
        
        $query = "select t.transporter_name, p.total_amount from `#__transporter_payments` p inner join `#__transporters` t on p.transporter_id=t.id where p.id=" . $payment_id;
        $db->setQuery($query);
        $payment = $db->loadObject();   
        
        $query = "select invoice_id sales_id, amount,transporter_type  from `#__transporter_payment_items` where transporter_payment_id=" .$payment_id;
        $db->setQuery($query);
        $payment_items  = $db->loadObjectList();
        
        foreach($payment_items as $item)
        {
            if($item->transporter_type == TRANSPORTER){
               $query = "update `#__sales_invoice` set transportation_amount_paid=transportation_amount_paid-" . floatval($item->amount) . " where id=" . intval($item->sales_id); 
               //echo $query;exit;                                                                                                      
            }
            else if($item->transporter_type == LOADER){
               $query = "update `#__sales_invoice` set loading_amount_paid=loading_amount_paid-" . floatval($item->amount) . " where id=" . intval($item->sales_id);                                                                                                                                                                                                        
                //echo $query;exit;                                                                                                      
            }
            
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "delete from `#__transporter_payment_items` where transporter_payment_id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__transporter_payments` where id=" . $payment_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__cash_expenses` where item_id=" . $payment_id . " and item_type=" . TRANSPORTER_PAYMENT;
        $db->setQuery($query);
        $db->query();
        
        $query = "update `#__settings` set `value_numeric`=value_numeric+" . floatval($payment->total_amount) . " where `key`='cash_in_hand'";
        $db->setQuery($query);
        $db->query();
            
        $query = "update `#__transporters` set `account_balance`=account_balance+" . floatval($payment->total_amount) . " where id=" . $transporter_id;
        $db->setQuery($query);
        $db->query();  
    }
}//end of class

class SimpleImage
{
    var $image;
    var $image_type;
    
    function load($filename)
    {       
        $image_info = getimagesize($filename);
       
        $this->image_type = $image_info[2];
       
        if( $this->image_type == IMAGETYPE_JPEG )
        {
            $this->image = imagecreatefromjpeg($filename);
        }
        elseif( $this->image_type == IMAGETYPE_GIF ) 
        {  
            $this->image = imagecreatefromgif($filename); 
        } 
        elseif( $this->image_type == IMAGETYPE_PNG ) 
        {   
            $this->image = imagecreatefrompng($filename); 
        }
    } 
    
    function save($filename, $image_type, $compression=75, $permissions=null) 
    {   
        if( $image_type == IMAGETYPE_JPEG ) 
        { 
            imagejpeg($this->image,$filename,$compression); 
        } 
        elseif( $image_type == IMAGETYPE_GIF ) 
        {
           imagegif($this->image,$filename); 
        } 
        elseif( $image_type == IMAGETYPE_PNG ) 
        {   
            imagepng($this->image,$filename); 
        } 
        if( $permissions != null) 
        {   
            chmod($filename,$permissions); 
        } 
    } 
    
    function output($image_type=IMAGETYPE_JPEG) 
    {
       if( $image_type == IMAGETYPE_JPEG ) 
       { 
           imagejpeg($this->image); 
       } 
       elseif( $image_type == IMAGETYPE_GIF ) 
       {   
           imagegif($this->image); 
       } 
       elseif( $image_type == IMAGETYPE_PNG ) 
       {   imagepng($this->image); 
       } 
    } 
    
    function getWidth() 
    {   
        return imagesx($this->image); 
    } 
    
    function getHeight() 
    {   
        return imagesy($this->image); 
    } 
    
    function resizeToHeight($height) 
    {   
        $ratio = $height / $this->getHeight(); $width = $this->getWidth() * $ratio; $this->resize($width,$height); 
    }   
    
    function resizeToWidth($width) 
    { 
        $ratio = $width / $this->getWidth(); $height = $this->getheight() * $ratio; $this->resize($width,$height); 
    }   
    
    function scale($scale) 
    { 
        $width = $this->getWidth() * $scale/100; $height = $this->getheight() * $scale/100; $this->resize($width,$height); 
    }   
    
    function resize($width,$height) 
    { 
        $new_image = imagecreatetruecolor($width, $height); imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight()); $this->image = $new_image; 
    }
}

?>