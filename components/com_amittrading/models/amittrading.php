<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class AmittradingModelAmittrading extends JModelItem
{
    // first 4 functions are used in ajax calls for purchase, sales, stock transfer etc
    
    function get_items()
    {
        // function to get list of items for particular category
        $db = JFactory::getDBO();
        
        $category_id = intval(JRequest::getVar("category_id"));
        
        $query = "select id, item_name, last_purchase_rate, piece_per_pack, sale_price1, sale_price2,gst_percent from `#__items` where category_id=" . $category_id . " order by item_name";
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        $query = "select sum(stock) stock, item_id from `#__inventory_items` group by item_id";
        $db->setQuery($query);
        $inventory_data = $db->loadObjectList("item_id");
        
        $html = "";
        
        $html .= "<select class='item_id' name='item_id[]' style='width:140px;'>";
        $html .= "<option value='0' style='text-align:left;'></option>";
        
        if(count($items) > 0)
        {
            foreach($items as $item)
            {
                $html .= "<option value='" . $item->id . "' last_purchase_rate='" . $item->last_purchase_rate . "' piece_per_pack='" . $item->piece_per_pack . "' sale_price1='" . $item->sale_price1 . "' sale_price2='" . $item->sale_price2 . "' gst_percent='" . $item->gst_percent . "' current_stock='" . (isset($inventory_data[$item->id]) ? floatval($inventory_data[$item->id]->stock) : 0) . "' style='text-align:left;'>" . $item->item_name . "</option>";
            }
        }
        
        $html .= "</select>";
        
        echo $html;
    }
    
    function get_items_for_sales_order()
    {
        // function to get list of items for particular category but here stock is important
        $db = JFactory::getDBO();
        
        $category_id = intval(JRequest::getVar("category_id"));
        
        $query = "select id, item_name, last_purchase_rate, piece_per_pack, sale_price1, sale_price2 from `#__items` where category_id=" . $category_id . " order by item_name";
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        $query = "select sum(stock) stock, item_id from `#__inventory_items` group by item_id";
        $db->setQuery($query);
        $inventory_data = $db->loadObjectList("item_id");
        
        $query = "select sum(pack) unbilled_stock, item_id from `#__sales_order_items` where status=" . UNBILLED . " group by item_id";
        $db->setQuery($query);
        $unbilled_sales_order_items = $db->loadObjectList("item_id");
        
        $html = "";
        
        $html .= "<select class='item_id' name='item_id[]' style='width:140px;'>";
        $html .= "<option value='0' style='text-align:left;'></option>";
        
        if(count($items) > 0)
        {
            foreach($items as $item)
            {
                $current_stock = 0;
                $current_stock += (isset($inventory_data[$item->id]) ? floatval($inventory_data[$item->id]->stock) : 0);
                $current_stock -= (isset($unbilled_sales_order_items[$item->id]) ? floatval($unbilled_sales_order_items[$item->id]->unbilled_stock) : 0);
                
                $html .= "<option value='" . $item->id . "' last_purchase_rate='" . $item->last_purchase_rate . "' piece_per_pack='" . $item->piece_per_pack . "' sale_price1='" . $item->sale_price1 . "' sale_price2='" . $item->sale_price2 . "' current_stock='" . $current_stock . "' style='text-align:left;'>" . $item->item_name . "</option>";
            }
        }
        
        $html .= "</select>";
        
        echo $html;
    }
    
    function get_items_with_stock_details()
    {
        // function to get list of items for particular category & location and here stock is important
        $db = JFactory::getDBO();
        
        $category_id = intval(JRequest::getVar("category_id"));
        $location_id = intval(JRequest::getVar("location_id"));
        
        $query = "select i.id, i.item_name, i.gst_percent, i.last_purchase_rate, i.piece_per_pack, i.sale_price1, i.sale_price2, ii.stock from `#__items` i left join `#__inventory_items` ii on (i.id=ii.item_id and ii.location_id=" . $location_id . ") where i.category_id=" . $category_id . " order by i.item_name";
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        $html = "";
        
        $html .= "<select class='item_id' name='item_id[]' style='width:140px;'>";
        $html .= "<option value='0' style='text-align:left;'></option>";
        
        if(count($items) > 0)
        {
            foreach($items as $item)
            {   
                $html .= "<option value='" . $item->id . "' last_purchase_rate='" . $item->last_purchase_rate . "' piece_per_pack='" . $item->piece_per_pack . "' sale_price1='" . $item->sale_price1 . "' sale_price2='" . $item->sale_price2 . "' stock='" . floatval($item->stock) . "' gst_percent='" . floatval($item->gst_percent) . "' style='text-align:left;'>" . $item->item_name . "</option>";
            }
        }
        
        $html .= "</select>";
        
        echo $html;
    }
    
    function get_locationwise_items_with_stock()
    {
        $db = JFactory::getDBO();
        
        $location_id = intval(JRequest::getVar("location_id"));
        
        $query = "select i.id, i.item_name, i.category_id, i.gst_percent, ii.stock from `#__items` i left join `#__inventory_items` ii on (i.id=ii.item_id and ii.location_id=" . $location_id . ") order by i.item_name";
        $db->setQuery($query);
        $itemlist = $db->loadObjectList();
        
        $items = array();
        if(count($itemlist) > 0)
        {
            $category_id = 0;
            foreach($itemlist as $i)
            {
                if($category_id != 0 && $category_id != $i->category_id)
                {
                    $items[$category_id] .= "</select>";
                }
                if(!isset($items[$i->category_id]))
                {
                    $category_id = $i->category_id;
                    $items[$i->category_id] = "<select class='item_id' name='item_id[]' style='width:140px;'>";
                    $items[$i->category_id] .= "<option value='0' style='text-align:left;' ></option>";
                }
                $items[$i->category_id] .= "<option value='" . $i->id . "' stock='" . floatval($i->stock) . "' gst_percent='" . floatval($i->gst_percent) . "' style='text-align:left;' >" . $i->item_name . "</option>";
            }
            
            $items[$category_id] .= "</select>";
        }
        
        echo json_encode($items);
    }
    
    function save_purchase_entry()
    {  
        
        $db = JFactory::getDBO();
      
        /* GST :: Start */
        $query = "select state_id, gst_registration_type from `#__suppliers` where id=" . intval(JRequest::getVar('supplier_id'));
        $db->setQuery($query);
        $supplier_details = $db->loadObject();
        
        $self_gst_state_code = Functions::get_setting("self_gst_state_code");
        $suppliers_gst_state_code = Functions::get_gst_state_code($supplier_details->state_id); 
        
                 
        $purchase_date = date("Y-m-d", strtotime(JRequest::getVar('purchase_date')));
        $supplier_id = intval(JRequest::getVar('supplier_id'));
        
        $bill_type = intval(JRequest::getVar('bill_type'));
        $bill_no = intval(JRequest::getVar('bill_no'));  
        $supplier_challan_no = intval(JRequest::getVar('supplier_challan_no'));  
        $challan_no = intval(JRequest::getVar('challan_no')); 
        $vehicle_id = intval(JRequest::getVar('vehicle_id'));
        $loading_charges = floatval(JRequest::getVar('loading_charges'));
        $waiverage_charges = floatval(JRequest::getVar('waiverage_charges'));
        
        $item_id = JRequest::getVar('item_id'); 
        $unit_id = JRequest::getVar('unit_id'); 
        $product_mt = JRequest::getVar('product_mt'); 
        $product_rate = JRequest::getVar('product_rate');
        $gst_percent = JRequest::getVar('gst_percent');
        $gst_amount = JRequest::getVar('gst_amount');
        
        $product_gross_amount = JRequest::getVar('product_gross_amount');
        $product_total_amount = JRequest::getVar('product_total_amount'); 
        
        $total_amount = floatval(JRequest::getVar('total_amount'));         
        $total_gst_amount = JRequest::getVar('total_gst_amount'); 
        $total_gross_amount = JRequest::getVar('total_gross_amount'); 
        $product_grand_total = floatval(JRequest::getVar('product_grand_total')); 
       
        $item_note = JRequest::getVar('item_note');  
        $remarks = JRequest::getVar('remarks');  
        $creation_date = date("Y-m-d h:i:s");
        
        $purchase = new stdClass();
    
        $purchase->bill_date = $purchase_date;
        $purchase->bill_no = $bill_no;
        $purchase->supplier_id = $supplier_id;
        $purchase->bill_type = $bill_type;
        $purchase->supplier_challan_no = $supplier_challan_no;
        $purchase->challan_no = $challan_no;
        $purchase->vehicle_id = $vehicle_id;
        $purchase->gross_amount = $total_gross_amount;
        $purchase->gst_amount = $total_gst_amount;
        $purchase->total_amount = $product_grand_total + $loading_charges + $waiverage_charges;
        $purchase->loading_charges = $loading_charges;
        $purchase->waiverage_charges = $waiverage_charges;
        $purchase->remarks = $remarks;
        $purchase->creation_date = $creation_date;
        
        $db->insertObject("#__purchase", $purchase, "");
        
        $inserted_id = intval($db->insertid());
       
        if($inserted_id > 0)
        {
            if(count($item_id) > 0)
            {
                for($i = 0; $i < count($item_id);$i++)
                {
                    if(intval($item_id[$i]) > 0)
                    {
                        $purchase_items = new stdClass();
                         
                        $purchase_items->purchase_id = intval($inserted_id);
                        $purchase_items->item_id = intval($item_id[$i]);
                        $purchase_items->unit_id = intval($unit_id[$i]);
                        $purchase_items->product_mt = floatval($product_mt[$i]);
                        $purchase_items->product_rate = floatval($product_rate[$i]);
                        $purchase_items->gross_amount = floatval($product_gross_amount[$i]);
                        $purchase_items->gst_percent = $gst_percent[$i];
                        $purchase_items->gst_amount = $gst_amount[$i];
                        $purchase_items->total_amount = floatval($product_total_amount[$i]);
                        $purchase_items->note = $item_note[$i];
                       
                        if($supplier_details->gst_registration_type == CSD)
                        {
                            $purchase_items->cgst_percent = floatval(0); 
                            $purchase_items->cgst_amount = floatval(0);
                            $purchase_items->sgst_percent = floatval(0); 
                            $purchase_items->sgst_amount = floatval(0);
                            $purchase_items->igst_percent = floatval(0);
                            $purchase_items->igst_amount = floatval(0);
                        }
                        else
                        {
                            if($self_gst_state_code == $customers_gst_state_code)
                            {
                                $purchase_items->cgst_percent = floatval($gst_percent[$i] / 2); 
                                $purchase_items->cgst_amount = floatval($gst_amount[$i] / 2);
                                $purchase_items->sgst_percent = floatval($gst_percent[$i] / 2); 
                                $purchase_items->sgst_amount = floatval($gst_amount[$i] / 2);
                            }
                            else
                            {
                                $purchase_items->igst_percent = floatval($gst_percent[$i]);
                                $purchase_items->igst_amount = floatval($gst_amount[$i]);
                            }
                        }
                        $db->insertObject("#__purchase_items", $purchase_items, "");
                    }    
                }
            }
        } 
           
        $extra_charges = floatval($loading_charges + $waiverage_charges);
        $query = "update `#__suppliers` set `account_balance`=account_balance+" . floatval($total_amount + $extra_charges) . " where id=" . $supplier_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::adjust_supplier_account($supplier_id);
        
        $query = "select `supplier_name` from `#__suppliers` where id=" . $supplier_id;
        $db->setQuery($query);
        $supplier_name = $db->loadResult(); 
        
        Functions::log_activity("Purchase invoice for supplier " . $supplier_name . " has been saved.", "PI", $purchase_id);
        return("Purchase invoice saved successfully...");     
           
    }
            
    function update_purchase()
    {
        $db = JFactory::getDbO();    
        
        $query = "select state_id, gst_registration_type from `#__suppliers` where id=" . intval(JRequest::getVar('supplier_id'));
        $db->setQuery($query);
        $supplier_details = $db->loadObject();
        
        $self_gst_state_code = Functions::get_setting("self_gst_state_code");
        $suppliers_gst_state_code = Functions::get_gst_state_code($supplier_details->state_id);

        $purchase_id = intval(JRequest::getVar('purchase_id'));
        
        $purchase_date = date("Y-m-d", strtotime(JRequest::getVar('purchase_date')));
        $supplier_id = intval(JRequest::getVar('supplier_id'));  
        $bill_type = intval(JRequest::getVar('bill_type'));
        $bill_no = intval(JRequest::getVar('bill_no'));   
        $supplier_challan_no = intval(JRequest::getVar('supplier_challan_no'));  
        $challan_no = intval(JRequest::getVar('challan_no')); 
        $vehicle_id = intval(JRequest::getVar('vehicle_id'));
        $loading_charges = floatval(JRequest::getVar('loading_charges'));
        $waiverage_charges = floatval(JRequest::getVar('waiverage_charges'));
         
        $item_id = JRequest::getVar('item_id'); 
        $unit_id = JRequest::getVar('unit_id'); 
        $product_mt = JRequest::getVar('product_mt'); 
        $product_rate = JRequest::getVar('product_rate');
        $gst_percent = JRequest::getVar('gst_percent');
        $gst_amount = JRequest::getVar('gst_amount');
        
        $product_gross_amount = JRequest::getVar('product_gross_amount');
        $product_total_amount = JRequest::getVar('product_total_amount'); 
        
        $total_amount = floatval(JRequest::getVar('total_amount'));         
        $total_gst_amount = JRequest::getVar('total_gst_amount'); 
        $total_gross_amount = JRequest::getVar('total_gross_amount'); 
        $product_grand_total = floatval(JRequest::getVar('product_grand_total')); 
       
       
        $item_note = JRequest::getVar('item_note');  
        $remarks = JRequest::getVar('remarks');  
        $creation_date = date("Y-m-d h:i:s");  
       
        $query = "select supplier_id, total_amount from `#__purchase` where id=" . $purchase_id;
        $db->setQuery($query);
        $previous_total_amount = $db->loadObject();
        
        //echo (floatval($total_amount . " " .$loading_charges . "" .$waiverage_charges)); exit;
        $purchase = new stdClass();
        
        $purchase->id = $purchase_id;
        
        $purchase->bill_date = $purchase_date;
        $purchase->bill_no = $bill_no;
        $purchase->supplier_id = $supplier_id;
        $purchase->bill_type = $bill_type;
        $purchase->supplier_challan_no = $supplier_challan_no;
        $purchase->challan_no = $challan_no;
        $purchase->vehicle_id = $vehicle_id;
        $purchase->gross_amount = $total_gross_amount;
        $purchase->gst_amount = $total_gst_amount;
        $purchase->total_amount = $product_grand_total + $loading_charges + $waiverage_charges;
        $purchase->loading_charges = $loading_charges;
        $purchase->waiverage_charges = $waiverage_charges;
        $purchase->remarks = $remarks;
        $purchase->creation_date = $creation_date;
        
        $db->updateObject("#__purchase", $purchase, "id");
       
        $query = "delete from `#__purchase_items` where `purchase_id`=" . $purchase_id;
        $db->setQuery($query);
        $db->query();
        
        
        if(count($item_id) > 0)
        {
            for($i = 0; $i < count($item_id);$i++)
            {
                if(intval($item_id[$i]) > 0)
                {
                    $purchase_items = new stdClass();
                     
                    $purchase_items->purchase_id = $purchase_id;
                    
                    $purchase_items->item_id = intval($item_id[$i]);
                    $purchase_items->unit_id = intval($unit_id[$i]);
                    $purchase_items->product_mt = floatval($product_mt[$i]);
                    $purchase_items->product_rate = floatval($product_rate[$i]);
                    $purchase_items->gross_amount = floatval($product_gross_amount[$i]);
                    $purchase_items->gst_percent = $gst_percent[$i];
                    $purchase_items->gst_amount = $gst_amount[$i];
                    $purchase_items->total_amount = floatval($product_total_amount[$i]);
                    $purchase_items->note = $item_note[$i];
                   
                    if($supplier_details->gst_registration_type == CSD)
                    {
                        $purchase_items->cgst_percent = floatval(0); 
                        $purchase_items->cgst_amount = floatval(0);
                        $purchase_items->sgst_percent = floatval(0); 
                        $purchase_items->sgst_amount = floatval(0);
                        $purchase_items->igst_percent = floatval(0);
                        $purchase_items->igst_amount = floatval(0);
                    }
                    else
                    {
                        if($self_gst_state_code == $customers_gst_state_code)
                        {
                            $purchase_items->cgst_percent = floatval($gst_percent[$i] / 2); 
                            $purchase_items->cgst_amount = floatval($gst_amount[$i] / 2);
                            $purchase_items->sgst_percent = floatval($gst_percent[$i] / 2); 
                            $purchase_items->sgst_amount = floatval($gst_amount[$i] / 2);
                        }
                        else
                        {
                            $purchase_items->igst_percent = floatval($gst_percent[$i]);
                            $purchase_items->igst_amount = floatval($gst_amount[$i]);
                        }
                    }
                    $db->insertObject("#__purchase_items", $purchase_items, "");
                }    
            }
        }
         
        $extra_charges = floatval($loading_charges + $waiverage_charges);
       
        $query = "update `#__suppliers` set `account_balance`=account_balance+" . floatval($total_amount + $extra_charges) . "-" . $previous_total_amount->total_amount . " where id=" . $previous_total_amount->supplier_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::adjust_supplier_account($supplier_id);
        
        $query = "select `supplier_name` from `#__suppliers` where id=" . $supplier_id;
        $db->setQuery($query);
        $supplier_name = $db->loadResult();
        
        Functions::log_activity("Purchase invoice for supplier " . $supplier_name . " has been updated.", "PI", $purchase_id);
        
        return "Purchase invoice updated successfully.";
    }
    
    function delete_purchase()
    {
        $db = JFactory::getDbO();
        $purchase_id = intval(JRequest::getVar("purchase_id"));
        
        
        $query = "select supplier_id, total_amount from `#__purchase` where id=" . $purchase_id;
        $db->setQuery($query);
        $purchase = $db->loadObject();
        
        $query = "delete from `#__purchase` where `id`=" . $purchase_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__purchase_items` where `purchase_id`=" . $purchase_id;
        $db->setQuery($query);
        $db->query();
         
        $query = "update `#__suppliers` set `account_balance`=account_balance-" . floatval($purchase->total_amount) . " where id=" . intval($purchase->supplier_id);
        $db->setQuery($query);
        $db->query();             
    }
    
    function save_production()
    {
        $db = JFactory::getDBO();
         
       // $production_date = JRequest::getVar('production_date');
        
        $production_date = date("Y-m-d", strtotime(JRequest::getVar('production_date')));
        
        $product_id = JRequest::getVar('product_id');
        $total_production = JRequest::getVar('total_production');
        $comment = JRequest::getVar('comment');
        
        $query = "insert into `#__daily_production_entry`(`production_date`,`product_id`,`total_production`,`comment`) values('" . $production_date . "','" . $product_id . "','" . $total_production . "','" . $comment . "')";
        $db->setQuery($query);
        $db->query();
        
       /* $query = "select count(*) from `#__daily_production_entry` where vehicle_number='" . $vehicle_number . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Vehicle Number already exists.";
        }
        else
        {
            $query = "insert into `#__vehicles`(`vehicle_number`,`vehicle_type`,`owner_name`,`owner_address`,`owner_number`) values('" . $vehicle_number . "','" . $vehicle_type . "','" . $owner_name . "','" . $owner_address . "','" . $owner_number . "')";
            $db->setQuery($query);
            $db->query();
        }*/        
    }
    function production_details()
    {
        $db = JFactory::getDbO();
        
        $production_id = intval(JRequest::getVar("production_id"));
        $query = "select * from `#__daily_production_entry` where id=" . $production_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());        
    }
    function update_production()
    {
        $db = JFactory::getDBO();
        
        $production_id = JRequest::getVar('production_id');
        $production_date = JRequest::getVar('production_date');
        $product_id = JRequest::getVar('product_id');
        $total_production = JRequest::getVar('total_production');
        $comment = JRequest::getVar('comment');
        
        $query = "update `#__daily_production_entry` set `production_date`='" . $production_date . "', `product_id`='" . $product_id . "',`total_production`='" . $total_production . "',`comment`='" . $comment . "' where `id`=" . $production_id;
        $db->setQuery($query);
        $db->query();
        
        /*$query = "select count(*) from `#__daily_production_entry` where product='" . $vehicle_number . "' and id<>" . $vehicle_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Vehicle already exists.";
        }
        else
        {   
            $query = "update `#__vehicles` set `vehicle_number`='" . $vehicle_number . "', `vehicle_type`='" . $vehicle_type . "',`owner_name`='" . $woner_name . "',`owner_address`='" . $owner_address . "',`owner_number`='" . $owner_number . "' where `id`=" . $vehicle_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Vehicle " . $vehicle_number .  " has been updated.");
        } */   
    }
    function delete_production()
    {
        $db = JFactory::getDbO();
        $production_id = intval(JRequest::getVar("production_id"));
        
        $query = "delete from `#__daily_production_entry` where `id`=" . $production_id;
        $db->setQuery($query);
        $db->query();        
    }
    
    function fetch_unit_gst()
    {
        $db = JFactory::getDbO();
        $product_id = intval(JRequest::getVar("product_id")); 
         
        $query = "SELECT unit,u.id,gst_percent FROM `#__products` p inner join `#__units` u on p.unit_id = u.id WHERE p.id=". $product_id;
        //$query = "SELECT u.unit FROM `#__products` p inner join `#__units` u on p.unit_id = u.id WHERE p.id=". $product_id;
        $db->setQuery($query);
        
        echo json_encode($db->loadAssoc());
    }
    function cancel_order_items()
    {
        // item in order is cancelled
        // single function for both purchase & sales order
         
        $db = JFactory::getDBO();
        
        $order_id = intval(JRequest::getVar("order_id"));
        $order_item_id = intval(JRequest::getVar("order_item_id"));
        $type = JRequest::getVar("type");
        //echo $type;exit;
        
        if($type == "p")
        {
            $query = "update `#__purchase_order_items` set `status`=" . CANCELLED . " where id=" . $order_item_id;
            $query1= "select i.item_name from `#__purchase_order_items` p inner join `#__items` i on p.item_id=i.id where p.id=" . $order_item_id;
        }
        else if($type == "s")
        {
           // echo "aa";exit;
            //$query = "update `#__sales_order_items` set `status`=" . CANCELLED . " where id=" . $order_item_id;
            $query = "delete from `#__sales_order_items` where id=" . $order_item_id;       
            //$query1= "select i.item_name from `#__sales_order_items` s inner join `#__items` i on s.item_id=i.id where s.id=" . $order_item_id;
            $query1 = "select p.product_name from `#__sales_order_items` s inner join `#__products` p on s.product_id=p.id where s.id=" . $order_item_id;
            //echo $query1;
            
            //$query2 = "select "
        }
        
        $db->setQuery($query);
        if($db->query())
        {
            $db->setQuery($query1);
            $item_name = $db->loadResult();
            
            Functions::log_activity("Item " . $item_name . " of " . ($type == "p" ? "purchase" : "sales") . " order no. " . $order_id . " has been cancelled.", ($type == "p" ? "PO" : "SO"), $order_id);
            echo "ok";
        }
    }
    
    function get_bill_no()
    {
        $db = JFactory::getDbO(); 
        $bill_no = intval(JRequest::getVar("bill_no"));
        $supplier_challan_no = intval(JRequest::getVar("supplier_challan_no"));
        $challan_no = intval(JRequest::getVar("challan_no"));
        
        $purchase_id = intval(JRequest::getVar("purchase_id"));
         
        $query = "select count(id) count,id from `#__purchase` where `bill_no`=" . $bill_no ;
        $db->setQuery($query);
        $match = $db->loadObject();
        
        if($purchase_id != 0 )
        {
            if($match->count > 0 && $match->id <> $purchase_id )
            {
                echo "bill_no";
            }
            else
            {
                $query = "select count(id) count,id from `#__purchase` where `supplier_challan_no`=" . $supplier_challan_no ;
                $db->setQuery($query);
                $match = $db->loadObject();
                
                if($match->count > 0 && $match->id <> $purchase_id )
                {
                    echo "supplier_challan_no";
                }
                else
                {
                    $query = "select count(id) count,id from `#__purchase` where `challan_no`=" . $challan_no ;
                    $db->setQuery($query);
                    $match = $db->loadObject();
                    
                    if($match->count > 0 && $match->id <> $purchase_id )
                    {
                        echo "challan_no";
                    }
                    else
                    {
                        echo "false";
                    }    
                }
                
              
            }
        }
        else
        {
            $query = "select count(*) from `#__purchase` where `bill_no`=" . $bill_no ;
            $db->setQuery($query);
            $count = $db->loadResult(); 
            
            if($count > 0)
            {
                echo "bill_no"; 
            }
            else
            {  
                $query = "select count(*) from `#__purchase` where `supplier_challan_no`=" . $supplier_challan_no ;
                $db->setQuery($query);
                $count = $db->loadResult();
                
                if($count > 0)
                {
                    echo "supplier_challan_no";    
                }
                else
                {
                    $query = "select count(*) from `#__purchase` where `challan_no`=" . $challan_no ;
                    $db->setQuery($query);
                    $count = $db->loadResult();
                    
                    if($count > 0)
                    {
                        echo "challan_no";            
                    }
                    else
                    {
                        echo "false";
                    }     
                }    
            }
        }
    }
    
    
    // backup
    
    /*function get_bill_no()
    {
        $db = JFactory::getDbO(); 
        $bill_no = intval(JRequest::getVar("bill_no"));
        $supplier_challan_no = intval(JRequest::getVar("supplier_challan_no"));
        $challan_no = intval(JRequest::getVar("challan_no"));
        
        $purchase_id = intval(JRequest::getVar("purchase_id"));
         
        $query = "select count(id) count,id from `#__purchase` where `bill_no`=" . $bill_no ;
        $db->setQuery($query);
        $match = $db->loadObject();
        
        if($purchase_id != 0 )
        {
            if($match->count > 0 && $match->id <> $purchase_id )
            {
                echo "true";
            }
            else
            {
                echo "false";
            }
        }
        else
        {
            $query = "select count(*) from `#__purchase` where `bill_no`=" . $bill_no ;
            $db->setQuery($query);
            $count = $db->loadResult(); 
            
            if($count > 0)
            {
                echo "bill_no"; 
            }
            else
            {  
                $query = "select count(*) from `#__purchase` where `supplier_challan_no`=" . $supplier_challan_no ;
                $db->setQuery($query);
                $count = $db->loadResult();
                
                if($count > 0)
                {
                    echo "supplier_challan_no";    
                }
                else
                {
                    $query = "select count(*) from `#__purchase` where `challan_no`=" . $challan_no ;
                    $db->setQuery($query);
                    $count = $db->loadResult();
                    
                    if($count > 0)
                    {
                        echo "challan_no";            
                    }
                    else
                    {
                        echo "false";
                    }     
                }    
            }
        }
    } */
    
    //end backup
}
?>