<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class AmittradingModelAmittrading1 extends JModelItem
{
    function get_pending_bills()
    {
        // function to get list of those invoices which are pending to pay for a customer
        
        $db = JFactory::getDBO();
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        
        $query = "select id, bill_date, bill_no, (bill_amount - amount_paid) amount_pending from `#__sales_invoice` where customer_id=" . $customer_id . " and status=" . UNPAID . " order by bill_date";
        $db->setQuery($query);
        $pending_bills = $db->loadObjectList();
        
        $html = "";
        
        $html .= "<option value='0'></option>";
        
        if(count($pending_bills) > 0)
        {
            foreach($pending_bills as $bill)
            {
                $html .= "<option value='" . $bill->id . "'>Bill No. " . $bill->bill_no . " dated " . date("d-M-Y", strtotime($bill->bill_date)) . ", Amount Pending : " . $bill->amount_pending . "/-</option>";
            }
        }
        
        echo $html;
    }
    
    function save_sales_invoice()
    {
        $db = JFactory::getDbO();
        date_default_timezone_set('Asia/Kolkata');
         
        $query = "select state_id, gst_registration_type from `#__customers` where id=" . intval(JRequest::getVar("customer_id"));
        $db->setQuery($query);
        $customer_details = $db->loadObject();
       
        $self_gst_state_code = Functions::get_setting("self_gst_state_code");
        $customers_gst_state_code = Functions::get_gst_state_code($customer_details->state_id);
        
        $bill_date = date("Y-m-d", strtotime(JRequest::getVar("bill_date"))); 
        $customer_id = intval(JRequest::getVar('customer_id'));
        $order_id = intval(JRequest::getVar('order_id_si'));
        $time = date("h:i:s"); 
        $challan_no = intval(JRequest::getVar('challan_no')); 
        $bill_type = intval(JRequest::getVar('bill_type')); 
        $royalty_id = JRequest::getVar('royalty_id'); 
        $customer_id = JRequest::getVar('customer_id');  
        
        $vehicle_id = intval(JRequest::getVar('vehicle_id')); 
        $starting_km = floatval(JRequest::getVar('starting_km')); 
        
        $transporter_id = intval(JRequest::getVar('transporter_id'));
        $driver_name = JRequest::getVar('driver_name');
        $driver_no = JRequest::getVar('driver_no');
        $driver_license_no = JRequest::getVar('driver_license_no');
        $vehicle_rate_per_mt = floatval(JRequest::getVar('vehicle_rate'));
        $add_cash = floatval(JRequest::getVar('add_cash'));
        $liter = floatval(JRequest::getVar('liter'));
        $diesel_rate = floatval(JRequest::getVar('diesel_rate'));
        $diesel_total_amount = floatval(JRequest::getVar('diesel_total_amount'));
        $transportation_paid_amount = floatval($add_cash + $diesel_total_amount);
        
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        //echo $supplier_id;exit;
        // items  
        
        $product_item_type = JRequest::getVar('product_item_type');
        $product_items_id = JRequest::getVar('product_item_id'); 
        $product_mt = JRequest::getVar('product_mt');
        $product_rate = JRequest::getVar('product_rate');
        
        if($bill_type == BILL && $order_id == 0)
        {
            $gst_percent = GST_PERCENT_5; 
            $total_gst_amount = floatval(JRequest::getVar("total_gst_amount"));
        }
        else{
             $gst_percent = GST_PERCENT_0;
             $total_gst_amount = 0;
        }
        $product_note = JRequest::getVar('product_note');
        $product_total_amount = JRequest::getVar('product_total_amount');
        
        $mixing_item_type = JRequest::getVar('mixing_item_type');
        $mixing_items_id = JRequest::getVar('mixing_item_id');
        $mixing_mt = JRequest::getVar('mixing_mt');
        $mixing_rate = JRequest::getVar('mixing_rate');
        $mixing_note = JRequest::getVar('mixing_note');
        //if($order_id == 0)
//        {
            $gross_amount = floatval(JRequest::getVar("gross_amount"));
            $total_amount = floatval(JRequest::getVar("total_amount"));
        //}
        $total_weight = floatval(JRequest::getVar("total_weight"));
        $loaded_weight = floatval(JRequest::getVar('loaded_weight'));
        $empty_weight = floatval(JRequest::getVar('empty_weight'));
        $net_weight = floatval(JRequest::getVar('net_weight'));
        $vehicle_rate = $vehicle_rate_per_mt * $net_weight;
        $transporter_total_amount = $vehicle_rate - $transportation_paid_amount;
        
        $loading_type = intval(JRequest::getVar('loading'));
        $loading_vehicle_type = intval(JRequest::getVar('loading_vehicle_type'));
        $loading_transporter_id = intval(JRequest::getVar('loading_transporter_id'));
        $loading_amount = floatval(JRequest::getVar('loading_amount'));
        
        $royalty_type = JRequest::getVar('royalty');
        $royalty_mt = JRequest::getVar('royalty_mt');
        $royalty_no = JRequest::getVar('royalty_no');
        $royalty_rate = JRequest::getVar('royalty_rate');
        $party_id = JRequest::getVar('party_id');
        
        $waiverage_charges = JRequest::getVar('waiverage_charges');
        $remarks = JRequest::getVar('remarks');
        $grand_total_amount = $total_amount;
        
                
        $query = "select max(bill_no) from `#__sales_invoice` where `bill_type`=" . $bill_type . "";
        $db->setQuery($query);
        $bill_no = intval($db->loadResult()) + 1;
        
        $query = "select billed_quantity from #__sales_orders where id=".$order_id;
        $db->setQuery($query);
        $previous_billed_qty = floatval($db->loadResult());
                        
        $query="select account_balance from `#__transporters` where id =".$transporter_id;
        $db->setQuery($query);
        $transporter_account_balance = floatval($db->loadResult());
        $loading_transporter_account_balance = 0;
        if($loading_transporter_id != $transporter_id)
        {
            $query="select account_balance from `#__transporters` where id =".$loading_transporter_id;
            $db->setQuery($query);
            $loading_transporter_account_balance = floatval($db->loadResult());
        }
        
        $sales_invoice = new stdClass();
        
        $sales_invoice->date = $bill_date;
        $sales_invoice->bill_no = $bill_no;
        $sales_invoice->time = $time;
        $sales_invoice->bill_challan_no = $challan_no;
        $sales_invoice->bill_type = $bill_type;
        $sales_invoice->royalty_id = $royalty_id;
        $sales_invoice->customer_id = $customer_id;
        $sales_invoice->order_id = $order_id;
        
        
        $sales_invoice->gross_amount = $gross_amount;
        $sales_invoice->gst_amount = $total_gst_amount;
        $sales_invoice->total_amount = $total_amount + $loading_amount + $waiverage_charges;
        $sales_invoice->total_weight = $total_weight; 
        
        $sales_invoice->vehicle_id = $vehicle_id;
        $sales_invoice->starting_km = $starting_km;
        $sales_invoice->vehicle_rate_per_mt = $vehicle_rate_per_mt;
        $sales_invoice->transportation_amount_paid = $transportation_paid_amount;
        
        $sales_invoice->transporter_id = $transporter_id;
        $sales_invoice->driver_name = $driver_name;
        $sales_invoice->driver_no = $driver_no;
        $sales_invoice->add_cash = $add_cash;
        $sales_invoice->diesel_supplier_id = $supplier_id;
        $sales_invoice->liter = $liter;
        $sales_invoice->diesel_rate = $diesel_rate;
        $sales_invoice->diesel_total_amount = $diesel_total_amount;
        $sales_invoice->loaded_weight = $loaded_weight;
        $sales_invoice->empty_weight = $empty_weight;
        $sales_invoice->net_weight = $net_weight;
        $sales_invoice->vehicle_rate = $vehicle_rate;
        
        $sales_invoice->loading_type = $loading_type;
        $sales_invoice->loading_vehicle_type = $loading_vehicle_type;
        $sales_invoice->loading_transporter_id = $loading_transporter_id;
        $sales_invoice->loading_amount = $loading_amount;
        $sales_invoice->loading_amount_paid = $loading_paid_amount;
        $loading_total_amount =  $loading_amount - $loading_paid_amount;
        
        $sales_invoice->royalty_type = $royalty_type[0];
        $sales_invoice->royalty_mt = $royalty_mt[0];
        $sales_invoice->royalty_no = $royalty_no[0];
        $sales_invoice->royalty_rate = $royalty_rate[0];
        $sales_invoice->party_id = $party_id[0];
        
        $sales_invoice->royalty_type1 = $royalty_type[1];
        $sales_invoice->royalty_mt1 = $royalty_mt[1];
        $sales_invoice->royalty_no1 = $royalty_no[1];
        $sales_invoice->royalty_rate1 = $royalty_rate[1];
        $sales_invoice->party_id1 = $party_id[1];
        
        $sales_invoice->waiverage_charges = $waiverage_charges;
        $sales_invoice->remarks = $remarks;
        
        $db->insertObject("#__sales_invoice", $sales_invoice, "");
        $sales_id = intval($db->insertid());
        
        if($order_id != 0)
        {
          $sales_order = new stdClass();  
          $sales_order->id = $order_id;
          $sales_order->billed_quantity = $previous_billed_qty + $total_weight;
          $db->updateObject("#__sales_orders",$sales_order,"id");     
        }    
        
        if($diesel_total_amount > 0 && $sales_id > 0)
        {
             $purchase_entry = new stdClass();
             $purchase_entry->bill_date = $bill_date;
             $purchase_entry->bill_no = 0;
             $purchase_entry->supplier_id = $supplier_id;
             $purchase_entry->sales_invoice_id = $sales_id;
             $purchase_entry->bill_type = CHALLAN;
             $purchase_entry->supplier_challan_no = 0;
             $purchase_entry->challan_no = 0;
             $purchase_entry->vehicle_id = $vehicle_id;
             $purchase_entry->gross_amount = $diesel_total_amount;
             $purchase_entry->gst_amount = GST_PERCENT_0;
             $purchase_entry->total_amount = $diesel_total_amount;
             $purchase_entry->creation_date = date("Y-m-d h:i:sa");
             $db->insertObject("#__purchase",$purchase_entry,"");
             $purchase_id = intval($db->insertid());
             
             $purchase_entry_items = new stdClass();
             $purchase_entry_items->purchase_id = $purchase_id;
             $purchase_entry_items->item_id = Functions::get_setting("product_type_diesel");
             
             $query = "select `unit_id` from `#__products` where id=".$purchase_entry_items->item_id;
             $db->setQuery($query);
             $unit_id = $db->loadResult();
             
             $purchase_entry_items->unit_id = $unit_id;
             $purchase_entry_items->product_mt = $liter;
             $purchase_entry_items->product_rate = $diesel_rate;
             $purchase_entry_items->gross_amount = $diesel_total_amount;
             $purchase_entry_items->gst_percent = GST_PERCENT_0;
             $purchase_entry_items->gst_amount = 0.00;
             $purchase_entry_items->cgst_percent = 0.00;
             $purchase_entry_items->cgst_amount = 0.00;
             $purchase_entry_items->sgst_percent = 0.00;
             $purchase_entry_items->sgst_amount = 0.00;
             $purchase_entry_items->igst_percent = 0.00;
             $purchase_entry_items->igst_amount = 0.00;
             $purchase_entry_items->total_amount = $diesel_total_amount;
             $db->insertObject("#__purchase_items", $purchase_entry_items,"");                
        }
                    
        $total_mixing_weight = 0;
        if($sales_id > 0)
        {
            if(count($mixing_items_id) > 0)
            {
                for($x = 0; $x < count($mixing_items_id);$x++)
                {
                    if(intval($mixing_items_id[$x]) > 0)
                    {
                        $total_mixing_weight += floatval($mixing_mt[$x]);
                    }
                }    
            }
            
            if(count($product_items_id) > 0)
            {
                for($i = 0; $i < count($product_items_id);$i++)
                {
                    if(intval($product_items_id[$i]) > 0)
                    {
                        $product_items = new stdClass();
                    
                        $product_items->sales_invoice_id = intval($sales_id);
                        $product_items->item_type = PRODUCT;
                        
                        $product_items->product_id = intval($product_items_id[$i]);
                        $product_items->quantity = floatval($product_mt[$i]);
                        
                        $product_items->actual_weight = floatval($product_mt[$i]);
                        if($i == 0)
                        {                        
                            $product_items->mixing_weight = $total_mixing_weight;
                            $total_weight = floatval($product_mt[$i]) + $total_mixing_weight;
                            $gross_amount = floatval($product_rate[$i]) * $total_weight;
                            $total_mixing_weight = 0;
                        }
                        else 
                        {
                            $total_weight = floatval($product_mt[$i]);
                            $gross_amount = floatval($product_total_amount[$i]);
                        }
                        
                        $product_items->total_weight = $total_weight;
                        $product_items->product_rate = floatval($product_rate[$i]);
                        
                        $gst_amount = floatval($product_rate[$i] * $total_weight * $gst_percent)/100 ;
                        $product_items->gst_amount = $gst_amount;
                        $product_items->gross_amount = floatval($gross_amount);
                        $product_items->gst_percent = $gst_percent; 
                        
                        $total_amount = $gross_amount + $gst_amount;
                        
                        $product_items->total_amount = floatval($total_amount);
                        $product_items->product_note = $product_note[$i];
                        
                        if($customer_details->gst_registration_type == CSD)
                        {
                            $product_items->cgst_percent = floatval(0); 
                            $product_items->cgst_amount = floatval(0);
                            $product_items->sgst_percent = floatval(0); 
                            $product_items->sgst_amount = floatval(0);
                            $product_items->igst_percent = floatval(0);
                            $product_items->igst_amount = floatval(0);
                        }
                        else
                        {
                            if($self_gst_state_code == $customers_gst_state_code)
                            {
                                $product_items->cgst_percent = floatval($gst_percent / 2); 
                                $product_items->cgst_amount = floatval($gst_amount / 2);
                                $product_items->sgst_percent = floatval($gst_percent / 2); 
                                $product_items->sgst_amount = floatval($gst_amount / 2);
                            }
                            else
                            {
                                $product_items->igst_percent = floatval($gst_percent);
                                $product_items->igst_amount = floatval($gst_amount);
                            }
                        }
                        
                        $db->insertObject("#__sales_invoice_items", $product_items, ""); 
                    }
                }  
            }
            if(count($mixing_items_id) > 0)
            {
                for($x = 0; $x < count($mixing_items_id);$x++)
                {
                    if(intval($mixing_items_id[$x]) > 0)
                    {
                        $mixing_items = new stdClass();
                    
                        $mixing_items->sales_invoice_id = intval($sales_id);
                        $mixing_items->item_type = MIXING;
                        
                        $mixing_items->product_id = intval($mixing_items_id[$x]);
                        $mixing_items->quantity = floatval($mixing_mt[$x]);
                        $mixing_items->actual_weight = floatval($mixing_mt[$x]);
                        $mixing_items->mixing_weight = 0;
                        $mixing_items->total_weight = floatval($mixing_mt[$x]);
                        $mixing_items->product_rate = floatval($mixing_rate[$x]);
                        $mixing_items->gross_amount = floatval($mixing_total_amount[$x]);
                        $mixing_items->total_amount = floatval($mixing_total_amount[$x]);
                        $mixing_items->product_note = $mixing_note[$x];
                        
                        $db->insertObject("#__sales_invoice_items", $mixing_items, ""); 
                    }
                }    
            }
        }
        
        $query = "select `self_rent_id` from `#__vehicles` where id=" . $vehicle_id;
        $db->setQuery($query);
        $self_rent_id = $db->loadResult();
        
        if($self_rent_id == RENT)
        {
            //if($transporter_account_balance < 0)
//            {
//                if($transporter_id == $loading_transporter_id)
//                    $total_transport_amount = floatval($loading_amount) + floatval($vehicle_rate) - (floatval($add_cash) + floatval($diesel_total_amount));                    
//                else
//                    $total_transport_amount = floatval($vehicle_rate) - (floatval($add_cash) + floatval($diesel_total_amount));                                                
//                
//                if(abs($transporter_account_balance) >= $total_transport_amount)
//                    Functions::adjust_transporter_account($transporter_id, $total_transport_amount);
//                else
//                    Functions::adjust_transporter_account($transporter_id, abs($transporter_account_balance));
            //}
           // else
            //{
                $transport_amount = floatval($add_cash) + floatval($diesel_total_amount);  
                if($transporter_id == $loading_transporter_id)
                {
                   $total_transport_amount = floatval($vehicle_rate) + floatval($loading_amount) - floatval($transport_amount); 
                }
                else
                {
                   $total_transport_amount = floatval($vehicle_rate) - floatval($transport_amount); 
                }
                $query = "update `#__transporters` set `account_balance`=account_balance+" . $total_transport_amount . " where `id`=". $transporter_id;
                $db->setQuery($query);
                $db->query(); 
                
                $transporter_bills = new stdClass();
                $transporter_bills->transporter_id = $transporter_id;
                $transporter_bills->sales_invoice_id = $sales_id;
                $transporter_bills->transporter_type = TRANSPORTER;
                $transporter_bills->amount = $vehicle_rate;
                $transporter_bills->cash_paid_to_driver = $add_cash;
                $transporter_bills->diesel_amount = $diesel_total_amount;
                $transporter_bills->status = NOT_ADJUSTED;
                $transporter_bills->date = date("Y:m:d h:i:sa");  
                $db->insertObject("#__transporter_bills", $transporter_bills,"");
                    
           // }
        }
        
        if($loading_type == RENT)
        {
            //if($loading_transporter_account_balance < 0)
//            {
//                if($transporter_id != $loading_transporter_id)
//                {
//                    if(abs($loading_transporter_account_balance) >= $loading_amount)
//                        Functions::adjust_transporter_account($loading_transporter_id, $loading_amount);
//                    else
//                        Functions::adjust_transporter_account($loading_transporter_id, abs($loading_transporter_account_balance));        
//                }
//            }   
//            else if($loading_transporter_account_balance >= 0 && ($transporter_id != $loading_transporter_id))
//            {
                $query = "update `#__transporters` set `account_balance`=account_balance+" . $loading_amount .  " where `id`=". $loading_transporter_id;
                $db->setQuery($query);
                $db->query();    
                
                $loader_bills = new stdClass();
                $loader_bills->transporter_id = $transporter_id;
                $loader_bills->sales_invoice_id = $sales_id;
                $loader_bills->transporter_type = LOADER;
                $loader_bills->amount = $loading_amount;
                $transporter_bills->cash_paid_to_driver = $add_cash;
                $transporter_bills->diesel_amount = $diesel_total_amount;
                $loader_bills->status = NOT_ADJUSTED;
                $loader_bills->date = date("Y:m:d h:i:sa");  
                $db->insertObject("#__transporter_bills", $loader_bills,"");
            //}
        }
        
        if(intval($party_id[0]) > 0)
        {
            $total_royalty = floatval($royalty_mt[0] * $royalty_rate[0]);
            
            $query = "update `#__suppliers` set `account_balance`=account_balance+" . floatval($total_royalty). " where `id`=". intval($party_id[0]);
            $db->setQuery($query);
            $db->query();
        }
        
        if(intval($party_id[1]) > 0)
        {
            $total_royalty = floatval($royalty_mt[1] * $royalty_rate[1]);
            
            $query = "update `#__suppliers` set `account_balance`=account_balance+" . floatval($total_royalty). " where `id`=". intval($party_id[1]);
            $db->setQuery($query);
            $db->query();
        }
        
        //$total_loading_amount = 0;
        //$total_loading_amount = $loading_amount + $waiverage_charges;
        
        $query = "update `#__customers` set `account_balance`=account_balance+" . floatval($grand_total_amount) . "+" . floatval($loading_amount + $waiverage_charges)  . " where id=" . $customer_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::adjust_customer_account($customer_id);
        
        if($royalty_no[0])
        {
            $query = "update `#__royalty_booklet_items` set `sales_invoice_id`=" . intval($sales_id) . ",`used`=". USED ." where `rb_no`=" . $royalty_no[0];
            $db->setQuery($query);
            $db->query();
        }
        if($royalty_no[1])
        {
            $query = "update `#__royalty_booklet_items` set `sales_invoice_id`=" . intval($sales_id) . ",`used`=". USED ." where `rb_no`=" . $royalty_no[1];
            $db->setQuery($query);
            $db->query();
        }
                
        $query = "select `customer_name` from `#__customers` where id=" . $customer_id;
        $db->setQuery($query);
        $customer_name = $db->loadResult();
        Functions::log_activity("Sales invoice for customer " . $customer_name . " has been saved.", "SI", $sales_id);
        return $sales_id;
    }
    
    function update_sales_invoice()
    {
        $db = JFactory::getDbO();
        date_default_timezone_set('Asia/Kolkata');
        
        $sales_id = intval(JRequest::getVar('sales_id')); 
        
        $query = "select state_id, gst_registration_type from `#__customers` where id=" . intval(JRequest::getVar("customer_id"));
        $db->setQuery($query);
        $customer_details = $db->loadObject();  
        
        $self_gst_state_code = Functions::get_setting("self_gst_state_code");
        $customers_gst_state_code = Functions::get_gst_state_code($customer_details->state_id);

        $bill_date = date("Y-m-d", strtotime(JRequest::getVar("bill_date"))); 
        $time = date("h:i:s A"); 
        $challan_no = intval(JRequest::getVar('challan_no')); 
        $bill_type = intval(JRequest::getVar('bill_type')); 
        $royalty_id = JRequest::getVar('royalty_id'); 
        $customer_id = JRequest::getVar('customer_id');   
        $order_id = intval(JRequest::getVar('order_id_si')); 
        
        $vehicle_id = intval(JRequest::getVar('vehicle_id')); 
        $starting_km = floatval(JRequest::getVar('starting_km')); 
        
        $transporter_id = intval(JRequest::getVar('transporter_id'));
        $driver_name = JRequest::getVar('driver_name');
        $driver_no = JRequest::getVar('driver_no');
        $driver_license_no = JRequest::getVar('driver_license_no');
        $add_cash = floatval(JRequest::getVar('add_cash'));
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        $liter = floatval(JRequest::getVar('liter'));
        $diesel_rate = floatval(JRequest::getVar('diesel_rate'));
        $diesel_total_amount = floatval(JRequest::getVar('diesel_total_amount'));
        
        $vehicle_id = intval(JRequest::getVar('vehicle_id')); 
        $starting_km = floatval(JRequest::getVar('starting_km')); 
        $vehicle_rate_per_mt = floatval(JRequest::getVar('vehicle_rate'));
        
        $transporter_id = intval(JRequest::getVar('transporter_id'));
        $driver_name = JRequest::getVar('driver_name');
        $driver_no = JRequest::getVar('driver_no');
        $driver_license_no = JRequest::getVar('driver_license_no');
        $add_cash = floatval(JRequest::getVar('add_cash'));
        $supplier_id = intval(JRequest::getVar('supplier_id'));
        $liter = floatval(JRequest::getVar('liter'));
        $diesel_rate = floatval(JRequest::getVar('diesel_rate'));
        $diesel_total_amount = floatval(JRequest::getVar('diesel_total_amount'));
        $transportation_paid_amount = floatval($add_cash + $diesel_total_amount);
        // items
        
        $product_item_type = JRequest::getVar('product_item_type');
        $product_items_id = JRequest::getVar('product_item_id'); 
        $product_mt = JRequest::getVar('product_mt');
        $product_rate = JRequest::getVar('product_rate');  
        
        //$gst_percent = JRequest::getVar("gst_percent");
        if($bill_type == BILL)
        {
            $gst_percent = GST_PERCENT_5; 
            $total_gst_amount = floatval(JRequest::getVar("total_gst_amount"));
        }
        else{
             $gst_percent = GST_PERCENT_0;
             $total_gst_amount = 0;
        }
        $product_note = JRequest::getVar('product_note');
        $product_total_amount = JRequest::getVar('product_total_amount');
        
        $mixing_item_type = JRequest::getVar('mixing_item_type');
        $mixing_items_id = JRequest::getVar('mixing_items_id');
        $mixing_mt = JRequest::getVar('mixing_mt');
        $mixing_rate = JRequest::getVar('mixing_rate');
        $mixing_note = JRequest::getVar('mixing_note');
        
        $gross_amount = floatval(JRequest::getVar("gross_amount"));
        $total_gst_amount = floatval(JRequest::getVar("total_gst_amount"));
        $total_amount = floatval(JRequest::getVar("total_amount"));
        $total_weight = floatval(JRequest::getVar("total_weight"));
         // end items
        $loaded_weight = floatval(JRequest::getVar('loaded_weight'));
        $empty_weight = floatval(JRequest::getVar('empty_weight'));
        $net_weight = floatval(JRequest::getVar('net_weight'));
        
        $vehicle_rate = $vehicle_rate_per_mt * $net_weight;
        
        $loading_type = intval(JRequest::getVar('loading'));
        $loading_vehicle_type = intval(JRequest::getVar('loading_vehicle_type'));
        $loading_transporter_id = intval(JRequest::getVar('loading_transporter_id'));
        $loading_amount = floatval(JRequest::getVar('loading_amount'));
        
        $royalty_type = JRequest::getVar('royalty');
        $royalty_mt = JRequest::getVar('royalty_mt');
        $royalty_no = JRequest::getVar('royalty_no');
        $royalty_rate = JRequest::getVar('royalty_rate');
        $party_id = JRequest::getVar('party_id');
    
        $waiverage_charges = JRequest::getVar('waiverage_charges');
        $remarks = JRequest::getVar('remarks');
        $grand_total_amount = $total_amount;
        
        
        $query = "select customer_id,total_amount,loading_amount,waiverage_charges from `#__sales_invoice` where id=" . $sales_id;
        $db->setQuery($query);
        $previous_total_amount = $db->loadObject();
        
        $query = "select p.id purchase_id, pi.id purchase_item_id from `#__purchase` p inner join `#__purchase_items` pi on p.id=pi.id where p.sales_invoice_id=" . $sales_id;
        $db->setQuery($query);
        $purchase_ids = $db->loadObject();
        $purchase_id = $purchase_ids->purchase_id;
        $purchase_item_id = $purchase_ids->purchase_item_id;
        
       // $query = "select billed_quantity from #__sales_orders where id=".$order_id;
//        $db->setQuery($query);
//        $previous_billed_qty = floatval($db->loadResult()); 
        
        $query = "SELECT * FROM `#__sales_invoice` where id =" . $sales_id;
        $db->setQuery($query);
        $previous_details = $db->loadObject();
        
        $query = "update `#__sales_orders` set `billed_quantity` = billed_quantity-" . floatval($previous_details->total_weight)." where id=".$order_id;
        $db->setQuery($query);
        $db->query();
                        
        
        //$previous_transporter_id= intval($previous_details->transporter_id);
//        $previous_loader_id = intval($previous_details->loading_transporter_id);
//        $previous_vehicle_rate = floatval($previous_details->vehicle_rate);
//        $previous_transportation_amount_paid = floatval($previous_details->transportation_amount_paid);
//           
//        $pending_transportation_cost =  $previous_vehicle_rate - $previous_transportation_amount_paid;
//        $previous_loading_amount = floatval($previous_details->loading_amount);
//        $previous_loading_amount_paid = floatval($previous_details->loading_amount_paid);
//        $pending_loading_amount = $previous_loading_amount - $previous_loading_amount_paid;
        
        
        $query = "select * from `#__sales_invoice` where id=" . $sales_id;
        $db->setQuery($query);
        $sales_invoice_details = $db->loadObject();
        

        //$query = "select `account_balance` from `#__transporters` where id=" .$previous_transporter_id;
//        $db->setQuery($query);
//        $previous_transporter_balance = intval($db->loadResult());
//        
//        $query = "select `account_balance` from `#__transporters` where id=" .$previous_loader_id;
//        $db->setQuery($query);
//        $previous_loader_balance =  intval($db->loadResult());
//        
//        
//        if($previous_transporter_id == $previous_loader_id)
//        {
//             $query = "select * from `#__transporter_payment_items` where transporter_id=".$previous_transporter_id." and invoice_id= ".$sales_id;
//             $db->setQuery($query);
//             $transporter_payments_items_details = $db->loadObjectList();
//             
//             foreach($transporter_payments_items_details as $transporter_payments_items_detail)
//             {
//                  $query = "update `#__transporter_payments` set amount_adjusted=amount_adjusted-".floatval($transporter_payments_items_detail->amount).", status=".NOT_ADJUSTED." where id=".$transporter_payments_items_detail->transporter_payment_id;      
//                  $db->setQuery($query);
//                  $db->query();  
//             }
//        }
//        else
//        {
//            $query = "select * from `#__transporter_payment_items` where transporter_id=".$previous_transporter_id." and invoice_id= ".$sales_id;
//            $db->setQuery($query);
//            $transporter_payments_items_details = $db->loadObjectList();
//            
//            $query = "select * from `#__transporter_payment_items` where transporter_id=".$previous_loader_id." and invoice_id= ".$sales_id;
//            $db->setQuery($query);
//            $loader_payments_items_details = $db->loadObjectList();
//            
//            foreach($transporter_payments_items_details as $transporter_payments_items_detail)
//            {
//                  $query = "update `#__transporter_payments` set amount_adjusted=amount_adjusted-".floatval($transporter_payments_items_detail->amount).", status=".NOT_ADJUSTED." where id=".$transporter_payments_items_detail->transporter_payment_id;      
//                  $db->setQuery($query);
//                  $db->query();  
//            }
//             
//            foreach($loader_payments_items_details as $loader_payments_items_detail)
//            {
//                  $query = "update `#__transporter_payments` set amount_adjusted=amount_adjusted-".floatval($loader_payments_items_detail->amount).", status=".NOT_ADJUSTED." where id=".$loader_payments_items_detail->transporter_payment_id;      
//                  $db->setQuery($query);
//                  $db->query();  
//            }
//        }
//        if($pending_transportation_cost > 0)
//        {
//              $query = "update `#__transporters` set `account_balance`=account_balance-".$pending_transportation_cost." where id=".$previous_transporter_id;                      
//              $db->setQuery($query);
//              $db->query();
//        }
//        if($pending_loading_amount > 0)
//        {
//              $query = "update `#__transporters` set `account_balance`=account_balance-".$pending_loading_amount." where id=".$previous_loader_id;        
//              $db->setQuery($query);
//              $db->query();
//        } 
//        if($previous_transporter_id == $previous_loader_id)
//        {
//            $query = "select sum(amount) from `#__transporter_payment_items` where invoice_id=".$sales_id." and transporter_id=".$previous_transporter_id;
//            $db->setQuery($query);
//            $transporter_loader_payment = floatval($db->loadResult());

//            $query = "update `#__transporters` set `account_balance`=account_balance-".$transporter_loader_payment." where id=".$previous_transporter_id;       
//            $db->setQuery($query);
//            $db->query();    
//        }
//        else{
//            $query = "select sum(amount) from `#__transporter_payment_items` where invoice_id=".$sales_id." and transporter_id=".$previous_transporter_id;
//            $db->setQuery($query);
//            $transporter_payments = floatval($db->loadResult());

//            $query = "update `#__transporters` set `account_balance`=account_balance-".$transporter_payments." where id=".$previous_transporter_id;       
//            $db->setQuery($query);
//            $db->query();

//            $query = "select sum(amount) from `#__transporter_payment_items` where invoice_id=".$sales_id." and transporter_id=".$previous_loader_id;
//            $db->setQuery($query);
//            $loader_payments = floatval($db->loadResult()); 

//            $query = "update `#__transporters` set `account_balance`=account_balance-".$loader_payments." where id=".$previous_loader_id;                                                                                                                     
//            $db->setQuery($query);
//            $db->query(); 
//        }  
//        if($previous_transporter_id != $previous_loader_id)
//        {
//             $query = "DELETE FROM `#__transporter_payment_items` WHERE transporter_id=".$previous_transporter_id." and invoice_id= ".$sales_id;
//        }
//        else{
//            $query = "DELETE FROM `#__transporter_payment_items` WHERE transporter_id=".$previous_transporter_id." and invoice_id= ".$sales_id;
//             $db->setQuery($query);
//             $db->query();   
//        
//            $query = "DELETE FROM `#__transporter_payment_items` WHERE transporter_id=".$previous_loader_id." and invoice_id= ".$sales_id;
//             $db->setQuery($query);
//             $db->query();   
//        }
        //if($previous_transporter_id != 0)
//        {
//            $query = "DELETE FROM `#__transporter_payment_items` WHERE transporter_id=".$previous_transporter_id." and invoice_id= ".$sales_id;
//        }
//        else{
//            $query = "DELETE FROM `#__transporter_payment_items` WHERE transporter_id=".$previous_loader_id." and invoice_id= ".$sales_id;
//        }
        
        //echo $query;exit;
        //$db->setQuery($query);
//        $db->query();   
        
        
        $sales_invoice = new stdClass();
        $sales_invoice->id = $sales_id;
        
        $sales_invoice->date = $bill_date;
        $sales_invoice->time = $time;
        $sales_invoice->bill_challan_no = $challan_no;
        $sales_invoice->bill_type = $bill_type;
        $sales_invoice->royalty_id = $royalty_id;
        $sales_invoice->customer_id = $customer_id;
        
        $sales_invoice->total_weight = $total_weight;
        $sales_invoice->gross_amount = $gross_amount;
        $sales_invoice->gst_amount = $total_gst_amount;
        $sales_invoice->total_amount = $total_amount + $loading_amount + $waiverage_charges;
        
        $sales_invoice->vehicle_id = $vehicle_id;
        $sales_invoice->starting_km = $starting_km;
        $sales_invoice->vehicle_rate_per_mt = $vehicle_rate;
        $sales_invoice->transportation_amount_paid = $transportation_paid_amount;
        
        $sales_invoice->transporter_id = $transporter_id;
        $sales_invoice->driver_name = $driver_name;
        $sales_invoice->driver_no = $driver_no;
        
        $sales_invoice->add_cash = $add_cash;
        $sales_invoice->liter = $liter;
        $sales_invoice->diesel_rate = $diesel_rate;
        $sales_invoice->diesel_total_amount = $diesel_total_amount;
        $sales_invoice->loaded_weight = $loaded_weight;
        $sales_invoice->empty_weight = $empty_weight;
        $sales_invoice->net_weight = $net_weight;
        
        $sales_invoice->vehicle_rate = $vehicle_rate;
        
        $sales_invoice->loading_type = $loading_type;
        $sales_invoice->loading_vehicle_type = $loading_vehicle_type;
        $sales_invoice->loading_transporter_id = $loading_transporter_id;
        $sales_invoice->loading_amount = $loading_amount;
        $sales_invoice->loading_amount_paid = 0;
        
        $sales_invoice->royalty_type = $royalty_type[0];
        $sales_invoice->royalty_mt = $royalty_mt[0];
        $sales_invoice->royalty_no = $royalty_no[0];
        $sales_invoice->royalty_rate = $royalty_rate[0];
        $sales_invoice->party_id = $party_id[0];
        
        $sales_invoice->royalty_type1 = $royalty_type[1];
        $sales_invoice->royalty_mt1 = $royalty_mt[1];
        $sales_invoice->royalty_no1 = $royalty_no[1];
        $sales_invoice->royalty_rate1 = $royalty_rate[1];
        $sales_invoice->party_id1 = $party_id[1];
        
        $sales_invoice->waiverage_charges = $waiverage_charges;
        $sales_invoice->remarks = $remarks;
        
        $previous_total_royalty1 = floatval($previous_details->royalty_mt * $previous_details->royalty_rate);
        $previous_total_royalty2 = floatval($previous_details->royalty_mt1 * $previous_details->royalty_rate1);

        $db->updateObject("#__sales_invoice", $sales_invoice, "id");  
        
        if($diesel_total_amount > 0 && $sales_id > 0)
        {
            $purchase_entry = new stdClass();
            $purchase_entry->id = $purchase_id;
            $purchase_entry->supplier_id = $supplier_id;
            $purchase_entry->vehicle_id = $vehicle_id;
            $purchase_entry->gross_amount = $diesel_total_amount;
            $purchase_entry->total_amount = $diesel_total_amount;
            $db->updateObject("#__purchase",$purchase_entry, "id");
            
            $purchase_items_entry = new stdClass();
            $purchase_items_entry->id = $purchase_item_id;
            $purchase_items_entry->purchase_id = $purchase_id;
            $purchase_items_entry->product_mt = $liter;
            $purchase_items_entry->product_rate = $diesel_rate;
            $purchase_items_entry->gross_amount = $diesel_total_amount;
            $purchase_items_entry->total_amount = $diesel_total_amount;
            $db->updateObject("#__purchase_items",$purchase_items_entry, "id");
        }
        
        $query = "select * from `#__transporter_bills` where sales_invoice_id=" . $sales_id;
        $db->setquery($query);
        $transporter_bill_details = $db->loadObjectList();
        
        foreach($transporter_bill_details as $transporter_bill_detail)
        {
            $transporter_bill = new stdClass();
            $transporter_bill->id = intval($transporter_bill_detail->id);
            
            if($transporter_bill_details->transporter_type == TRANSPORTER)
            {
                $transporter_bill->transporter_id = $transporter_id; 
                $transporter_bill->transporter_type = TRANSPORTER;  
                $transporter_bill->amount = $vehicle_rate;   
                $transporter_bill->cash_paid_to_driver = $add_cash;
                $transporter_bill->diesel_amount = $diesel_total_amount;
            }
            else if($transporter_bill_details->transporter_type == LOADER)
            {
                $transporter_bill->transporter_id = $loading_transporter_id;   
                $transporter_bill->transporter_type = LOADER;    
                $transporter_bill->amount = $loading_amount; 
                $transporter_bill->cash_paid_to_driver = 0;
                $transporter_bill->diesel_amount = 0;  
            }
            
            $db->updateObject("#__transporter_bills", $transporter_bill,"id");
        }
        
        $query = "select billed_quantity from #__sales_orders where id=".$order_id;
        $db->setQuery($query);
        $previous_billed_qty = floatval($db->loadResult());
        
       if($order_id != 0)
        {
          $sales_order = new stdClass();  
          $sales_order->id = $order_id;
          $sales_order->billed_quantity = $previous_billed_qty + $total_weight;
          $db->updateObject("#__sales_orders",$sales_order,"id");     
        }            
        $query = "delete from `#__sales_invoice_items` where `sales_invoice_id`=" . $sales_id;
        $db->setQuery($query);
        $db->query();
                   
        $total_mixing_weight = 0;
        if($sales_id > 0)
        {
            if(count($mixing_items_id) > 0)
            {
                for($x = 0; $x < count($mixing_items_id);$x++)
                {
                    if(intval($mixing_items_id[$x]) > 0)
                    {
                        $total_mixing_weight += floatval($mixing_mt[$x]);
                    }
                }    
            }
            
            if(count($product_items_id) > 0)
            {
                for($i = 0; $i < count($product_items_id);$i++)
                {
                    if(intval($product_items_id[$i]) > 0)
                    {
                        $product_items = new stdClass();
                    
                        $product_items->sales_invoice_id = intval($sales_id);
                        $product_items->item_type = PRODUCT;
                        
                        $product_items->product_id = intval($product_items_id[$i]);
                        $product_items->quantity = floatval($product_mt[$i]);
                        
                        $product_items->actual_weight = floatval($product_mt[$i]);
                        if($i == 0)
                        {                        
                            $product_items->mixing_weight = $total_mixing_weight;
                            $total_weight = floatval($product_mt[$i]) + $total_mixing_weight;
                            $gross_amount = floatval($product_rate[$i]) * $total_weight;
                            $total_mixing_weight = 0;
                        }
                        else 
                        {
                            $total_weight = floatval($product_mt[$i]);
                            $gross_amount = floatval($product_total_amount[$i]);
                        }
                        
                        $product_items->total_weight = $total_weight;
                        
                        $product_items->product_rate = floatval($product_rate[$i]);
                        $gst_amount = floatval($product_rate[$i] * $total_weight * $gst_percent)/100 ;  
                        $product_items->gst_amount = $gst_amount;
                        
                        $product_items->gross_amount = floatval($gross_amount);
                        $product_items->gst_percent = $gst_percent; 
                        
                        $total_amount = $gross_amount + $gst_amount;
                        
                        $product_items->total_amount = floatval($total_amount);
                        $product_items->product_note = $product_note[$i];
                        
                        if($customer_details->gst_registration_type == CSD)
                        {
                            $product_items->cgst_percent = floatval(0); 
                            $product_items->cgst_amount = floatval(0);
                            $product_items->sgst_percent = floatval(0); 
                            $product_items->sgst_amount = floatval(0);
                            $product_items->igst_percent = floatval(0);
                            $product_items->igst_amount = floatval(0);
                        }
                        else
                        {
                            if($self_gst_state_code == $customers_gst_state_code)
                            {
                                $product_items->cgst_percent = floatval($gst_percent / 2); 
                                $product_items->cgst_amount = floatval($gst_amount / 2);
                                $product_items->sgst_percent = floatval($gst_percent / 2); 
                                $product_items->sgst_amount = floatval($gst_amount / 2);
                            }
                            else
                            {
                                $product_items->igst_percent = floatval($gst_percent);
                                $product_items->igst_amount = floatval($gst_amount);
                            }
                        }
                        
                        $db->insertObject("#__sales_invoice_items", $product_items, ""); 
                    }
                }    
            } 

            
            if(count($mixing_items_id) > 0)
            {
                for($x = 0; $x < count($mixing_items_id);$x++)
                {
                    if(intval($mixing_items_id[$x]) > 0)
                    {
                        $mixing_items = new stdClass();
                    
                        $mixing_items->sales_invoice_id = intval($sales_id);
                        $mixing_items->item_type = MIXING;
                        
                        $mixing_items->product_id = intval($mixing_items_id[$x]);
                        $mixing_items->quantity = floatval($mixing_mt[$x]);
                        $mixing_items->actual_weight = floatval($mixing_mt[$x]);
                        $mixing_items->mixing_weight = 0;
                        $mixing_items->total_weight = floatval($mixing_mt[$x]);
                        $mixing_items->product_rate = floatval($mixing_rate[$x]);
                        $mixing_items->gross_amount = floatval($mixing_total_amount[$x]);
                        $mixing_items->total_amount = floatval($mixing_total_amount[$x]);
                        $mixing_items->product_note = $mixing_note[$x];
                        
                        $db->insertObject("#__sales_invoice_items", $mixing_items, ""); 
                    }
                }    
            }
        }
        $query = "select `self_rent_id` from `#__vehicles` where id=" . $vehicle_id;
        $db->setQuery($query);
        $self_rent_id = $db->loadResult();
        
        $query="select account_balance from `#__transporters` where id =".$transporter_id;
        $db->setQuery($query);
        $transporter_account_balance = floatval($db->loadResult());
        $loading_transporter_account_balance = 0;
        $loading_transporter_account_balance_fetched = 0;
        if($loading_transporter_id != $transporter_id)
        {
            $query="select account_balance from `#__transporters` where id =".$loading_transporter_id;
            $db->setQuery($query);
            $loading_transporter_account_balance = floatval($db->loadResult());
            $loading_transporter_account_balance_fetched = 1;
        }
        
        if($self_rent_id == RENT)
        {
            //if($transporter_account_balance < 0)
//            {
//                if($transporter_id == $loading_transporter_id)
//                    $total_transport_amount = floatval($loading_amount) + floatval($vehicle_rate) - (floatval($add_cash) + floatval($diesel_total_amount));                    
//                else
//                    $total_transport_amount = floatval($vehicle_rate) - (floatval($add_cash) + floatval($diesel_total_amount));                                                
//                
//                if(abs($transporter_account_balance) >= $total_transport_amount)
//                    Functions::adjust_transporter_account($transporter_id, $total_transport_amount);
//                else
//                    Functions::adjust_transporter_account($transporter_id, abs($transporter_account_balance));
//            }
//            else
//            {
                $transport_amount = floatval($add_cash) + floatval($diesel_total_amount);  
                if($transporter_id == $loading_transporter_id)
                {
                   $total_transport_amount = floatval($vehicle_rate) + floatval($loading_amount) - floatval($transport_amount); 
                }
                else
                {
                   $total_transport_amount = floatval($vehicle_rate) - floatval($transport_amount); 
                }
                $query = "update `#__transporters` set `account_balance`=account_balance+" . $total_transport_amount . " where `id`=". $transporter_id;
                $db->setQuery($query);
                $db->query();    
                
            //}
        }
        
        if($loading_type == RENT)
        {
            //if($loading_transporter_account_balance < 0)
//            {
//                if($transporter_id != $loading_transporter_id)
//                {
//                    if(abs($loading_transporter_account_balance) >= $loading_amount)
//                        Functions::adjust_transporter_account($loading_transporter_id, $loading_amount);
//                    else
//                        Functions::adjust_transporter_account($loading_transporter_id, abs($loading_transporter_account_balance));        
//                }
//            }   
//            else if($loading_transporter_account_balance >= 0 && ($transporter_id != $loading_transporter_id))
//            {
                if($transporter_id != $loading_transporter_id)
                {
                    $query = "update `#__transporters` set `account_balance`=account_balance+" . $loading_amount .  " where `id`=". $loading_transporter_id;
                    $db->setQuery($query);
                    $db->query();     
                }
                
            //}
        }
        $current_total_royalty1 = floatval($royalty_mt[0] * $royalty_rate[0]);
        $current_total_royalty2 = floatval($royalty_mt[1] * $royalty_rate[1]);

        if(intval($party_id[0]) > 0)
        {
            $query = "update `#__suppliers` set `account_balance`=account_balance+" . floatval($current_total_royalty1 - $previous_total_royalty1). " where `id`=". intval($party_id[0]);
            $db->setQuery($query);
            $db->query();
        }
        if(intval($party_id[1]) > 0)
        {
            $query = "update `#__suppliers` set `account_balance`=account_balance+" . floatval($current_total_royalty2 - $previous_total_royalty2). " where `id`=". intval($party_id[1]);
            $db->setQuery($query);
            $db->query();
        }                
        
        $new_total_amount = floatval($grand_total_amount + $loading_amount + $waiverage_charges );
        $query = "update `#__customers` set `account_balance`=account_balance+" . floatval($new_total_amount) . "-" . floatval($previous_total_amount->total_amount) .  " where id=" . intval($previous_total_amount->customer_id);
        $db->setQuery($query);
        $db->query();
        
        Functions::adjust_customer_account($customer_id);
        
        $query = "update `#__royalty_booklet_items` set `sales_invoice_id`=0,`used`=0 where `sales_invoice_id`=" . $sales_id;
        $db->setQuery($query);
        $db->query();
        
        if($royalty_no[0])
        {
            $query = "update `#__royalty_booklet_items` set `sales_invoice_id`=" . intval($sales_id) . ",`used`=". USED ." where `rb_no`=" . $royalty_no[0];
            $db->setQuery($query);
            $db->query();
        }
        if($royalty_no[1])
        {
            $query = "update `#__royalty_booklet_items` set `sales_invoice_id`=" . intval($sales_id) . ",`used`=". USED ." where `rb_no`=" . $royalty_no[1];
            $db->setQuery($query);
            $db->query();
        } 
        
        $query = "select `customer_name` from `#__customers` where id=" . $customer_id;
        $db->setQuery($query);
        $customer_name = $db->loadResult();
        
        Functions::log_activity("Sales invoice for customer " . $customer_name . " has been updated.", "SI", $sales_id);
        
        return $sales_id;    
    }
    
    function delete_sales_invoice()
    {
        $db = JFactory::getDbO();
        $sales_id = intval(JRequest::getVar('sales_id'));  
        $order_id = intval(JRequest::getVar("order_id")); 
        $this->order_id = $order_id;
        
        $query = "select * from `#__sales_invoice` where id=".$sales_id;
        $db->setQuery($query);
        $sales_invoice_details = $db->loadObject();

        $query = "select billed_quantity from #__sales_orders where id=".$order_id;
        $db->setQuery($query);
        $previous_billed_qty = floatval($db->loadResult()); 
        
        $query = "SELECT `total_weight` FROM `#__sales_invoice` where id =" . $sales_id;
        $db->setQuery($query);
        $total_weight = floatval($db->loadResult());
        
        $query = "update `#__sales_orders` set `billed_quantity`=billed_quantity-" . $total_weight." where id=".$order_id;
        $db->setQuery($query);
        $db->query();
        
        $customer_id = intval($sales_invoice_details->customer_id);    
        $party_id = intval($sales_invoice_details->party_id);    
        $party_id1 = intval($sales_invoice_details->party_id1);    
//        
//        $transporter_id= intval($sales_invoice_details->transporter_id);
//        $loader_id = intval($sales_invoice_details->loading_transporter_id);
//        $vehicle_rate = floatval($sales_invoice_details->vehicle_rate);
//        $transportation_amount_paid = floatval($sales_invoice_details->transportation_amount_paid);
//           
//        $pending_transportation_cost =  $vehicle_rate - $transportation_amount_paid;
//        $loading_amount = floatval($sales_invoice_details->loading_amount);
//        $loading_amount_paid = floatval($sales_invoice_details->loading_amount_paid);
//        $pending_loading_amount = $loading_amount - $loading_amount_paid;
        
        
        //$query = "select `account_balance` from `#__transporters` where id=" .$transporter_id;
//        $db->setQuery($query);
//        $previous_transporter_balance = intval($db->loadResult());
//        
//        
//        $query = "select `account_balance` from `#__transporters` where id=" .$loader_id;
//        $db->setQuery($query);
//        $previous_loader_balance =  intval($db->loadResult()); 
        
        
        //if($transporter_id == $loader_id)
//        {
//             $query = "select * from `#__transporter_payment_items` where transporter_id=".$transporter_id." and invoice_id= ".$sales_id;
//             $db->setQuery($query);
//             $transporter_payments_items_details = $db->loadObjectList();
//             
//             foreach($transporter_payments_items_details as $transporter_payments_items_detail)
//             {
//                  $query = "update `#__transporter_payments` set amount_adjusted=amount_adjusted-".floatval($transporter_payments_items_detail->amount).", status=".NOT_ADJUSTED." where id=".$transporter_payments_items_detail->transporter_payment_id;      
//                  $db->setQuery($query);
//                  $db->query();  
//             }
//        }
//        else
//        {
//            $query = "select * from `#__transporter_payment_items` where transporter_id=".$transporter_id." and invoice_id= ".$sales_id;
//            $db->setQuery($query);
//            $transporter_payments_items_details = $db->loadObjectList();
//            
//            $query = "select * from `#__transporter_payment_items` where transporter_id=".$loader_id." and invoice_id= ".$sales_id;
//            $db->setQuery($query);
//            $loader_payments_items_details = $db->loadObjectList();
//            
//            foreach($transporter_payments_items_details as $transporter_payments_items_detail)
//            {
//                $query = "update `#__transporter_payments` set amount_adjusted=amount_adjusted-".floatval($transporter_payments_items_detail->amount).", status=".NOT_ADJUSTED." where id=".intval($transporter_payments_items_detail->transporter_payment_id);      
//                $db->setQuery($query);
//                $db->query();  
//            }

//            foreach($loader_payments_items_details as $loader_payments_items_detail)
//            {
//                $query = "update `#__transporter_payments` set amount_adjusted=amount_adjusted-".floatval($loader_payments_items_detail->amount).", status=".NOT_ADJUSTED." where id=".intval($loader_payments_items_detail->transporter_payment_id);      
//                $db->setQuery($query);
//                $db->query();  
//            }
//        }
//        
//        if($pending_transportation_cost > 0)
//        {
//            $query = "update `#__transporters` set `account_balance`=account_balance-".$pending_transportation_cost." where id=".$transporter_id;                      
//            $db->setQuery($query);
//            $db->query();
//        }
//        
//        if($pending_loading_amount > 0)
//        {
//            $query = "update `#__transporters` set `account_balance`=account_balance-".$pending_loading_amount." where id=".$loader_id;        
//            $db->setQuery($query);
//            $db->query();
//        }
//      
//        if($transporter_id == $loader_id)
//        {
//            $query = "select sum(amount) from `#__transporter_payment_items` where invoice_id=".$sales_id." and transporter_id=".$transporter_id;
//            $db->setQuery($query);
//            $transporter_loader_payment = floatval($db->loadResult());

//            $query = "update `#__transporters` set `account_balance`=account_balance-".$transporter_loader_payment." where id=".$transporter_id;       
            // echo $query;
//            $db->setQuery($query);
//            $db->query();  
//        }
//        
//        else
//        {
//            $query = "select sum(amount) from `#__transporter_payment_items` where invoice_id=".$sales_id." and transporter_id=".$transporter_id;
//            $db->setQuery($query);
//            $transporter_payments = floatval($db->loadResult());
//            
//            $query = "update `#__transporters` set `account_balance`=account_balance-".$transporter_payments." where id=".$transporter_id;       
//            $db->setQuery($query);
//            $db->query();
//            
//            $query = "select sum(amount) from `#__transporter_payment_items` where invoice_id=".$sales_id." and transporter_id=".$loader_id;
//            $db->setQuery($query);
//            $loader_payments = floatval($db->loadResult()); 
//            
//            $query = "update `#__transporters` set `account_balance`=account_balance-".$loader_payments." where id=".$loader_id;                                                                                                                     
//            $db->setQuery($query);
//            $db->query(); 
//        } 
//        
//        if($previous_transporter_id != $previous_loader_id)
//        {
//             $query = "DELETE FROM `#__transporter_payment_items` WHERE transporter_id=".$previous_transporter_id." and invoice_id= ".$sales_id;
//        }
//        
//        else
//        {
//            $query = "DELETE FROM `#__transporter_payment_items` WHERE transporter_id=".$previous_transporter_id." and invoice_id= ".$sales_id;
//             $db->setQuery($query);
//             $db->query();   
//        
//            $query = "DELETE FROM `#__transporter_payment_items` WHERE transporter_id=".$previous_loader_id." and invoice_id= ".$sales_id;
//             $db->setQuery($query);
//             $db->query();   
//        }
        
        
        if(intval($sales_invoice_details->royalty_type) == PURCHASE)
        {
            if(intval($party_id) > 0)
            {
                $total_royalty = floatval($sales_invoice_details->royalty_mt * $sales_invoice_details->royalty_rate);
                
                $query = "update `#__suppliers` set `account_balance`=account_balance-" . floatval($total_royalty). " where `id`=". intval($party_id);
                $db->setQuery($query);
                $db->query();
            }    
        }
        
        if(intval($sales_invoice_details->royalty_type1) == PURCHASE)
        {
             if(intval($party_id1) > 0)
             {
                $total_royalty = floatval($sales_invoice_details->royalty_mt1 * $sales_invoice_details->royalty_rate1);
                
                $query = "update `#__suppliers` set `account_balance`=account_balance-" . floatval($total_royalty). " where `id`=". intval($party_id1);
                $db->setQuery($query);
                $db->query();
             }    
        }
        
        $query = "update `#__customers` set `account_balance`=account_balance-" . floatval($sales_invoice_details->total_amount) ." where id=" . $customer_id;                                                                                                                     
        $db->setQuery($query);
        $db->query();
        
        Functions::adjust_customer_account($customer_id);
        
        $query = "delete from `#__sales_invoice_items` where `sales_invoice_id`=" . $sales_id;
        $db->setQuery($query);
        $db->query(); 
        
        $query = "select pi.id purchase_items_id from `#__purchase` p inner join `#__purchase_items` pi on p.id=pi.purchase_id where p.sales_invoice_id=".$sales_id;
        $db->setQuery($query);
        $purchase_item_id = $db->loadResult();
        
        $query = "delete from `#__purchase_items` where id=" . $purchase_item_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__purchase` where sales_invoice_id=" . $sales_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__transporter_bills` where sales_invoice_id=" . $sales_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__sales_invoice` where `id`=" . $sales_id;
        $db->setQuery($query);
        $db->query();   
        
        //if($transporter_id == $loader_id)
//        {
//            Functions::adjust_transporter_account($transporter_id, abs($previous_transporter_balance)); 
//        }
//        else
//        {
//            if($previous_transporter_balance < 0)
//            {
//                Functions::adjust_transporter_account($transporter_id, abs($previous_transporter_balance)); 
//            }
//            
//            if($previous_loader_balance < 0)
//            {
//                Functions::adjust_transporter_account($transporter_id, abs($previous_loader_balance));        
//            }
//        }
        Functions::log_activity("Sales invoice has been deleted.", "SI", $sales_id);
        return "Sales invoice deleted successfully.";
     }      
      
     function save_royalty_sales()
     {
        $db = JFactory::getDbo();
        
        $customer_id = intval(JRequest::getVar("customer_id"));      
        $date = date("Y-m-d", strtotime(JRequest::getVar("date")));     
        $royalty_booklet_id = intval(JRequest::getVar("booklet_id"));     
        $from_booklet_no = intval(JRequest::getVar("from_booklet_no"));     
        $to_booklet_no = intval(JRequest::getVar("to_booklet_no"));     
        $total_pages = intval(JRequest::getVar("total_pages"));     
        $amount = floatval(JRequest::getVar("amount"));     
        $comments = JRequest::getVar("comments");
        $pages = explode(",", JRequest::getVar("all_booklet_no_id"));
        
        //print_r($pages); exit;
        
        $royalty_sales = new stdClass();
        
        $royalty_sales->customer_id = $customer_id;     
        $royalty_sales->date = $date;     
        $royalty_sales->royalty_booklet_id = $royalty_booklet_id; 
        $royalty_sales->from_booklet_no = $from_booklet_no; 
        $royalty_sales->to_booklet_no = $to_booklet_no; 
        $royalty_sales->total_pages = $total_pages; 
        $royalty_sales->amount = $amount; 
        $royalty_sales->comments = $comments;
        
        if($db->insertObject("#__royalty_sales", $royalty_sales))
        {
            foreach($pages as $page)
            {
                $query = "update `#__royalty_booklet_items` set used=". SALE ." where id=".intval($page);
                $db->setQuery($query);
                $db->query();
            }
            return("Booklet sale");
        }
     } 
          
    function get_royalty_mt()
    {
        $db = JFactory::getDbO(); 
        $royalty_no = intval(JRequest::getVar("royalty_no"));
        
        $query = "select count(id) from `#__sales_invoice` where `royalty_no`=" . $royalty_no;
        $db->setQuery($query);
        $count = intval($db->loadResult());
        
        if($count > 0)
        {
            echo "This royalty number already used.";
            return; 
        }
        else
        {
            $query = "select count(id) from `#__royalty_booklet_items` where `rb_no`=" . $royalty_no;
            $db->setQuery($query);
            $is_rb_no_exists = intval($db->loadResult());
            
            if($is_rb_no_exists > 0)
            {
                $query = "select * from `#__royalty_booklet_items` where `rb_no`=" . $royalty_no;
                $db->setQuery($query);
                $booklet_item = $db->loadObject();
                
                $booklet_id = intval($booklet_item->booklet_id);
                $rb_no = $booklet_item->rb_no;
                $used = intval($booklet_item->used);
                
                if($booklet_id > 0 && $used == 0) 
                {
                    $query = "select `quantity` from `#__royalty_booklets` where `id`=" . $booklet_id;
                    $db->setQuery($query);
                    $quantity = floatval($db->loadResult());
                    
                    echo $quantity;
                }
                else
                {
                    echo "This royalty number is sold.";
                    return;
                }
            }
            else
            {
                 echo "This royalty number is not available.";
                 return;    
            }
        }
    }
    
    function get_royalty_no()
    {
        $db = JFactory::getDbO();
         
        $royalty_id = intval(JRequest::getVar("royalty_id"));
        $query = "select * from `#__royalty_booklet_items` where booklet_id='" . $royalty_id . "' and used=0 ";
        $db->setQuery($query);
        $booklet_items = $db->loadObjectList();
         
        echo json_encode($booklet_items);
    }
    
    function save_sales_order()
    {
        $db = JFactory::getDbO();
        date_default_timezone_set('Asia/Kolkata');
        
         
        $query = "select state_id, gst_registration_type from `#__customers` where id=" . intval(JRequest::getVar("customer_id"));
        $db->setQuery($query);
        $customer_details = $db->loadObject();  
        
        $self_gst_state_code = Functions::get_setting("self_gst_state_code");
        $customers_gst_state_code = Functions::get_gst_state_code($customer_details->state_id);
        
        $bill_date = date("Y-m-d", strtotime(JRequest::getVar("bill_date"))); 
        $time = date("h:i:s A");
        $customer_id = JRequest::getVar('customer_id'); 
        $challan_no = intval(JRequest::getVar('challan_no')); 
        $bill_type = intval(JRequest::getVar('bill_type')); 
        $royalty_id = intval(JRequest::getVar('royalty_id')); 
        $royalty_rate = floatval(JRequest::getVar('royalty_rate'));
        $transportation_rate = floatval(JRequest::getVar('transportation_rate'));
         
        // items
        
        $product_item_type = JRequest::getVar('product_item_type');
        $product_items_id = JRequest::getVar('product_item_id'); 
        $product_mt = JRequest::getVar('product_mt');
        $product_rate = JRequest::getVar('product_rate');
        
        $gst_percent = GST_PERCENT_5;
        $gst_amount = JRequest::getVar("gst_amount");
        
        $product_note = JRequest::getVar('product_note');
        $product_total_amount = JRequest::getVar('product_total_amount');
        
        $mixing_item_type = JRequest::getVar('mixing_item_type');
        $mixing_items_id = JRequest::getVar('mixing_item_id');
        $mixing_mt = JRequest::getVar('mixing_mt');
        $mixing_rate = JRequest::getVar('mixing_rate');
        $mixing_note = JRequest::getVar('mixing_note');
        
        
        
        $gross_amount = floatval(JRequest::getVar("gross_amount"));
        $total_gst_amount = floatval(JRequest::getVar("total_gst_amount"));
        $total_amount = floatval(JRequest::getVar("total_amount"));
        $total_weight = floatval(JRequest::getVar("total_weight"));
         // end items
        
        $query = "select max(bill_no) from `#__sales_invoice` where `bill_type`=" . $bill_type . "";
        $db->setQuery($query);
        $bill_no = intval($db->loadResult()) + 1;
        
        $sales_order = new stdClass();
        
        $sales_order->order_date = $bill_date;
        $sales_order->bill_no = $bill_no;
        $sales_order->bill_challan_no = $challan_no;
        $sales_order->bill_type = $bill_type;
        $sales_order->customer_id = $customer_id;
        $sales_order->royalty_id = $royalty_id;
        $sales_order->royalty_rate = $royalty_rate;
        $sales_order->total_weight = $total_weight;
        $sales_order->gross_amount = $gross_amount;
        $sales_order->gst_amount = $total_gst_amount;
        $sales_order->total_amount = $total_amount;
        $sales_order->transportation_rate = $transportation_rate;
        $sales_order->creation_date =  date("Y:m:d h:i:sa");
        
        $db->insertObject("#__sales_orders", $sales_order, "");
        $order_id = intval($db->insertid());
        
        $total_mixing_weight = 0;
        if($order_id > 0)
        {
            if(count($mixing_items_id) > 0)
            {
                for($x = 0; $x < count($mixing_items_id);$x++)
                {
                    if(intval($mixing_items_id[$x]) > 0)
                    {
                        $total_mixing_weight += floatval($mixing_mt[$x]);
                    }
                }    
            }
            
            if(count($product_items_id) > 0)
            {
                for($i = 0; $i < count($product_items_id);$i++)
                {
                    if(intval($product_items_id[$i]) > 0)
                    {
                        $product_items = new stdClass();
                    
                        $product_items->sales_order_id = intval($order_id);
                        $product_items->item_type = PRODUCT;
                        
                        $product_items->product_id = intval($product_items_id[$i]);
                        $product_items->quantity = floatval($product_mt[$i]);
                        
                        $product_items->actual_weight = floatval($product_mt[$i]);
                        if($i == 0)
                        {                        
                            $product_items->mixing_weight = $total_mixing_weight;
                            $total_weight = floatval($product_mt[$i]) + $total_mixing_weight;
                            $gross_amount = floatval($product_rate[$i]) * $total_weight;
                            $total_mixing_weight = 0;
                        }
                        else 
                        {
                            $total_weight = floatval($product_mt[$i]);
                            $gross_amount = floatval($product_total_amount[$i]);
                        }
                        
                        $product_items->total_weight = $total_weight;
                        
                        $product_items->product_rate = floatval($product_rate[$i]);
                        
                        $gst_amount = (floatval($product_rate[$i]) * $total_weight * 0.05) ;
                        $product_items->gst_amount = $gst_amount;
                        
                        $product_items->gross_amount = floatval($gross_amount);
                        $product_items->gst_percent = $gst_percent; 
                        
                        $total_amount = $gross_amount + $gst_amount;
                        
                        $product_items->total_amount = floatval($total_amount);
                        $product_items->product_note = $product_note[$i];
                        
                        if($customer_details->gst_registration_type == CSD)
                        {
                            $product_items->cgst_percent = floatval(0); 
                            $product_items->cgst_amount = floatval(0);
                            $product_items->sgst_percent = floatval(0); 
                            $product_items->sgst_amount = floatval(0);
                            $product_items->igst_percent = floatval(0);
                            $product_items->igst_amount = floatval(0);
                        }
                        else
                        {
                            if($self_gst_state_code == $customers_gst_state_code)
                            {
                                $product_items->cgst_percent = floatval($gst_percent / 2); 
                                $product_items->cgst_amount = floatval($gst_amount / 2);
                                $product_items->sgst_percent = floatval($gst_percent / 2); 
                                $product_items->sgst_amount = floatval($gst_amount / 2);
                            }
                            else
                            {
                                $product_items->igst_percent = floatval($gst_percent);
                                $product_items->igst_amount = floatval($gst_amount);
                            }
                        }
                        
                        $db->insertObject("#__sales_order_items", $product_items, ""); 
                    }
                }    
            }
            
            if(count($mixing_items_id) > 0)
            {
                for($x = 0; $x < count($mixing_items_id);$x++)
                {
                    if(intval($mixing_items_id[$x]) > 0)
                    {
                        $mixing_items = new stdClass();
                    
                        $mixing_items->sales_order_id = intval($order_id);
                        $mixing_items->item_type = MIXING;
                        
                        $mixing_items->product_id = intval($mixing_items_id[$x]);
                        $mixing_items->quantity = floatval($mixing_mt[$x]);
                        $mixing_items->actual_weight = floatval($mixing_mt[$x]);
                        $mixing_items->mixing_weight = 0;
                        $mixing_items->total_weight = floatval($mixing_mt[$x]);
                        $mixing_items->product_rate = floatval($mixing_rate[$x]);
                        $mixing_items->gross_amount = floatval($mixing_total_amount[$x]);
                        $mixing_items->total_amount = floatval($mixing_total_amount[$x]);
                        $mixing_items->product_note = $mixing_note[$x];
                        
                        $db->insertObject("#__sales_order_items", $mixing_items, ""); 
                    }
                }    
            }
        }
        return $order_id;
    }
    
     
    function update_sales_order()
    {
        $db = JFactory::getDbO();
        date_default_timezone_set('Asia/Kolkata');
        
        $order_id = intval(JRequest::getVar('sales_id')); 
        
        $query = "select state_id, gst_registration_type from `#__customers` where id=" . intval(JRequest::getVar("customer_id"));
        $db->setQuery($query);
        $customer_details = $db->loadObject();  
        
        $self_gst_state_code = Functions::get_setting("self_gst_state_code");
        $customers_gst_state_code = Functions::get_gst_state_code($customer_details->state_id);

        $bill_date = date("Y-m-d", strtotime(JRequest::getVar("bill_date"))); 
        $time = date("h:i:s A"); 
        $challan_no = intval(JRequest::getVar('challan_no')); 
        $bill_type = intval(JRequest::getVar('bill_type')); 
        $customer_id = JRequest::getVar('customer_id');
        
        $royalty_id = intval(JRequest::getVar('royalty_id')); 
        $royalty_rate = floatval(JRequest::getVar('royalty_rate'));
        // items
        
        $product_item_type = JRequest::getVar('product_item_type');
        $product_items_id = JRequest::getVar('product_item_id'); 
        $product_mt = JRequest::getVar('product_mt');
        $product_rate = JRequest::getVar('product_rate');
        $transportation_rate = JRequest::getVar('transportation_rate');
        
        $gst_percent = GST_PERCENT_5;
        $gst_amount = JRequest::getVar("gst_amount");
        
        $product_note = JRequest::getVar('product_note');
        $product_total_amount = JRequest::getVar('product_total_amount');
        
        $mixing_item_type = JRequest::getVar('mixing_item_type');
        $mixing_items_id = JRequest::getVar('mixing_items_id');
        $mixing_mt = JRequest::getVar('mixing_mt');
        $mixing_rate = JRequest::getVar('mixing_rate');
        $mixing_note = JRequest::getVar('mixing_note');
        $mixing_total_amount = JRequest::getVar('mixing_total_amount');
        
        $gross_amount = floatval(JRequest::getVar("gross_amount"));
        $total_gst_amount = floatval(JRequest::getVar("total_gst_amount"));
        $total_amount = floatval(JRequest::getVar("total_amount"));
        $total_weight = floatval(JRequest::getVar("total_weight"));
         // end items
        
        $sales_order = new stdClass();
        $sales_order->id = $order_id;
        
        $sales_order->order_date = $bill_date;
        $sales_order->bill_challan_no = $challan_no;
        $sales_order->bill_type = $bill_type;
        $sales_order->customer_id = $customer_id;
        $sales_order->royalty_id = $royalty_id;
        $sales_order->royalty_rate = $royalty_rate;
        $sales_order->total_weight = $total_weight;
        $sales_order->gross_amount = $gross_amount;
        $sales_order->gst_amount = $total_gst_amount;
        $sales_order->total_amount = $total_amount;
        $sales_order->transportation_rate = $transportation_rate;
        
        $query = "SELECT * FROM `#__sales_orders` where id =" . $order_id;
        $db->setQuery($query);
        $previous_details = $db->loadObject();

        $db->updateObject("#__sales_orders", $sales_order, "id");
        
        $query = "delete from `#__sales_order_items` where `sales_order_id`=" . $order_id;
        $db->setQuery($query);
        $db->query();
                   
        $total_mixing_weight = 0;
        if($order_id > 0)
        {
            if(count($mixing_items_id) > 0)
            {
                for($x = 0; $x < count($mixing_items_id);$x++)
                {
                    if(intval($mixing_items_id[$x]) > 0)
                    {
                        $total_mixing_weight += floatval($mixing_mt[$x]);
                    }
                }    
            }
            
           if(count($product_items_id) > 0)
            {
                for($i = 0; $i < count($product_items_id);$i++)
                {
                    if(intval($product_items_id[$i]) > 0)
                    {
                        $product_items = new stdClass();
                    
                        $product_items->sales_order_id = intval($order_id);
                        $product_items->item_type = PRODUCT;
                        
                        $product_items->product_id = intval($product_items_id[$i]);
                        $product_items->quantity = floatval($product_mt[$i]);
                        
                        $product_items->actual_weight = floatval($product_mt[$i]);
                        if($i == 0)
                        {                        
                            $product_items->mixing_weight = $total_mixing_weight;
                            $total_weight = floatval($product_mt[$i]) + $total_mixing_weight;
                            $gross_amount = floatval($product_rate[$i]) * $total_weight;
                            $total_mixing_weight = 0;
                        }
                        else 
                        {
                            $total_weight = floatval($product_mt[$i]);
                            $gross_amount = floatval($product_total_amount[$i]);
                        }
                        
                        $product_items->total_weight = $total_weight;
                        
                        $product_items->product_rate = floatval($product_rate[$i]);
                        
                        $gst_amount = (floatval($product_rate[$i]) * $total_weight * 0.05) ;
                        $product_items->gst_amount = $gst_amount;
                        
                        $product_items->gross_amount = floatval($gross_amount);
                        $product_items->gst_percent = $gst_percent; 
                        
                        $total_amount = $gross_amount + $gst_amount;
                        
                        $product_items->total_amount = floatval($total_amount);
                        $product_items->product_note = $product_note[$i];
                        
                        if($customer_details->gst_registration_type == CSD)
                        {
                            $product_items->cgst_percent = floatval(0); 
                            $product_items->cgst_amount = floatval(0);
                            $product_items->sgst_percent = floatval(0); 
                            $product_items->sgst_amount = floatval(0);
                            $product_items->igst_percent = floatval(0);
                            $product_items->igst_amount = floatval(0);
                        }
                        else
                        {
                            if($self_gst_state_code == $customers_gst_state_code)
                            {
                                $product_items->cgst_percent = floatval($gst_percent / 2); 
                                $product_items->cgst_amount = floatval($gst_amount / 2);
                                $product_items->sgst_percent = floatval($gst_percent / 2); 
                                $product_items->sgst_amount = floatval($gst_amount / 2);
                            }
                            else
                            {
                                $product_items->igst_percent = floatval($gst_percent);
                                $product_items->igst_amount = floatval($gst_amount);
                            }
                        }
                        
                        $db->insertObject("#__sales_order_items", $product_items, ""); 
                    }
                }    
            }
            
            if(count($mixing_items_id) > 0)
            {
                for($x = 0; $x < count($mixing_items_id);$x++)
                {
                    if(intval($mixing_items_id[$x]) > 0)
                    {
                        $mixing_items = new stdClass();
                    
                        $mixing_items->sales_order_id = intval($order_id);
                        $mixing_items->item_type = MIXING;
                        
                        $mixing_items->product_id = intval($mixing_items_id[$x]);
                        $mixing_items->quantity = floatval($mixing_mt[$x]);
                        $mixing_items->actual_weight = floatval($mixing_mt[$x]);
                        $mixing_items->mixing_weight = 0;
                        $mixing_items->total_weight = floatval($mixing_mt[$x]);
                        $mixing_items->product_rate = floatval($mixing_rate[$x]);
                        $mixing_items->gross_amount = floatval($mixing_total_amount[$x]);
                        $mixing_items->total_amount = floatval($mixing_total_amount[$x]);
                        $mixing_items->product_note = $mixing_note[$x];
                        
                        $db->insertObject("#__sales_order_items", $mixing_items, ""); 
                    }
                }    
            }
        }
   
        return $order_id;    
    }
    
    function delete_sales_order()
    {
        $db = JFactory::getDbO();
        $sales_id = intval(JRequest::getVar('sales_id'));

        $query = "delete from `#__sales_orders` where `id`=" . $sales_id;
        $db->setQuery($query);
        $db->query(); 
                                                
        $query = "delete from `#__sales_order_items` where `sales_order_id`=" . $sales_id;
        $db->setQuery($query);
        $db->query();
    }
       
    function delete_sales_order_from_pending_view()
    {
        $db = JFactory::getDbO();
        $sales_id = intval(JRequest::getVar('sales_id'));

        $query = "delete from `#__sales_orders` where `id`=" . $sales_id;
        $db->setQuery($query);
        $db->query(); 
                                                
        $query = "delete from `#__sales_order_items` where `sales_order_id`=" . $sales_id;
        $db->setQuery($query);
        $db->query();
    }  
}
?>