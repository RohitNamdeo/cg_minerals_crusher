<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

class MasterModelMaster extends JModelItem
{
    // City
    
    function save_city()
    {
        $db = JFactory::getDBO();
        
        $city_name = ucwords(addslashes(JRequest::getVar('city_name')));
        $state_id = intval(JRequest::getVar("state_id"));
        
        $query = "select count(*) from `#__cities` where city='" . $city_name . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "City already exists.";
        }
        else
        {                      
            $query = "insert into `#__cities`(`city`,`state_id`) values('" . $city_name . "'," . $state_id . ")";
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("City " . $city_name .  " has been added.");
        }
    }   
    
    function city_details()
    {
        $db = JFactory::getDbO();
        
        $city_id = intval(JRequest::getVar("city_id"));

        $query = "select * from `#__cities` where id=" . $city_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());   
    }
    
    function update_city()
    {
        $db = JFactory::getDBO();
        
        $city_name = ucwords(addslashes(JRequest::getVar('city_name')));
        $city_id = intval(JRequest::getVar('city_id'));
        $state_id = intval(JRequest::getVar("state_id"));
        
        $query = "select count(*) from `#__cities` where city='" . $city_name . "' and id<>" . $city_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "City already exists.";
        }
        else
        {                      
            $query = "update `#__cities` set `city`='" . $city_name . "',`state_id`=" . $state_id . " where `id`=" . $city_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("City " . $city_name .  " has been updated.");
        }
    }
    
    function delete_city()
    {   
        if(!is_admin())
        {
            return false;
        }
        
        $db = JFactory::getDBO();
        $city_id = intval(JRequest::getVar('city_id'));
        
        $count = 0;
        
        $query = "select count(id) from `#__customers` where city_id=" . $city_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__suppliers` where city_id=" . $city_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            return "Unable to delete city. It has dependencies.";
        }

        $query = "select city from `#__cities` where id=" . $city_id;
        $db->setQuery($query);
        $city_name = $db->loadResult();
        
        $query = "delete from `#__cities` where `id`=" . $city_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("City " . $city_name . " has been deleted."); 
        return "City deleted successfully.";
    }
    
    // vehicle
    function save_vehicle()
    {
        $db = JFactory::getDBO();
         
        $vehicle_number = strtoupper(JRequest::getVar('vehicle_number'));
        $vehicle_type = JRequest::getVar('vehicle_type');
        $transporter_id = intval(JRequest::getVar('owner_name'));
        $self_rent_id = intval(JRequest::getVar('self_rent'));
        $owner_address = JRequest::getVar('owner_address');
        $owner_number = JRequest::getVar('owner_number');
        $other_contact_numbers = JRequest::getVar('other_contact_numbers');
        
        $query = "select count(*) from `#__vehicles` where vehicle_number='" . $vehicle_number . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Vehicle Number already exists.";
        }
        else
        {
            $query = "insert into `#__vehicles`(`vehicle_number`,`vehicle_type`,`transporter_id`,`self_rent_id`,`owner_address`,`owner_number`,`other_contact_numbers`) values('" . $vehicle_number . "','" . $vehicle_type . "','" . $transporter_id . "',". $self_rent_id .",'" . $owner_address . "','" . $owner_number . "','" . $other_contact_numbers . "')";
            $db->setQuery($query);
            $db->query();
        }
    }
    
    function update_vehicle()
    {
        $db = JFactory::getDBO();
        
        $vehicle_number = strtoupper(JRequest::getVar('vehicle_number'));
        $vehicle_type = JRequest::getVar('vehicle_type');
        $transporter_id = JRequest::getVar('owner_name');
        $self_rent_id = intval(JRequest::getVar('self_rent'));
        $owner_address = JRequest::getVar('owner_address');
        $owner_number = JRequest::getVar('owner_number');
        $other_contact_numbers = JRequest::getVar('other_contact_numbers');
        
        $vehicle_id = intval(JRequest::getVar('vehicle_id'));
        
        $query = "select count(*) from `#__vehicles` where vehicle_number='" . $vehicle_number . "' and id<>" . $vehicle_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Vehicle already exists.";
        }
        else
        {   
            $query = "update `#__vehicles` set `vehicle_number`='" . $vehicle_number . "', `vehicle_type`='" . $vehicle_type . "',`transporter_id`='" . $transporter_id . "',`self_rent_id`=". $self_rent_id .",`owner_address`='" . $owner_address . "',`owner_number`='" . $owner_number . "',`other_contact_numbers`='" . $other_contact_numbers . "' where `id`=" . $vehicle_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Vehicle " . $vehicle_number .  " has been updated.");
        }    
    }
    
    function vehicle_details()
    {
        $db = JFactory::getDbO();
        
        $vehicle_id = intval(JRequest::getVar("vehicle_id"));
        $query =  "select v.*,t.transporter_name from `#__vehicles` v inner join `#__transporters` t on t.id=v.transporter_id where v.id=".$vehicle_id;
        //echo $query;exit;
        //$query = "select * from `#__vehicles` where id=" . $vehicle_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());    
    }
    
    function delete_vehicle()
    {
        $db = JFactory::getDbO();
        $vehicle_id = intval(JRequest::getVar("vehicle_id"));
        $query = "delete from `#__vehicles` where `id`=" . $vehicle_id;
        $db->setQuery($query);
        $db->query();
    }
    
    function save_vehicle_type()
    {
        $db = JFactory::getDBO();
         
        $vehicle_type = strtoupper(JRequest::getVar('vehicle_type'));
        
        $query = "select count(*) from `#__vehicles_type` where vehicle_type='" . $vehicle_type . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Vehicle Type Already Exists.";
        }
        else
        {
            $query = "insert into `#__vehicles_type`(`vehicle_type`) values('" . $vehicle_type . "')";
            $db->setQuery($query);
            $db->query();
        }    
    }
    function vehicle_type_details()
    {
        $db = JFactory::getDbO();
        
        $vehicle_type_id = intval(JRequest::getVar("vehicle_type_id"));
        $query = "select * from `#__vehicles_type` where id=" . $vehicle_type_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());    
    }
    function update_vehicle_type()
    {
        $db = JFactory::getDBO();
        
        $vehicle_type = strtoupper(JRequest::getVar('vehicle_type'));
        $vehicle_type_id = intval(JRequest::getVar('vehicle_type_id'));
        //$location_name = strtoupper(addslashes(JRequest::getVar('location_name')));
        
        $query = "select count(*) from `#__vehicles_type` where vehicle_type='" . $vehicle_type . "' and id<>" . $vehicle_type_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Vehicle Type already exists.";
        }
        else
        {   
            $query = "update `#__vehicles_type` set `vehicle_type`='" . $vehicle_type . "' where `id`=" . $vehicle_type_id;
            $db->setQuery($query);
            $db->query();

            //Functions::log_activity("Vehicle " . $vehicle_type .  " has been updated.");
        }    
    }
    function delete_vehicle_type()
    {
        $db = JFactory::getDbO();
        $vehicle_type_id = intval(JRequest::getVar("vehicle_type_id"));
        $query = "delete from `#__vehicles_type` where `id`=" . $vehicle_type_id;
        $db->setQuery($query);
        $db->query();        
    }
    
    function save_royalty()
    {
        $db = JFactory::getDBO();
         
        $royalty_name = strtoupper(JRequest::getVar('royalty_name'));
        
        $query = "select count(*) from `#__royalty` where royalty_name='" . $royalty_name . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Royalty Name Already Exists.";
        }
        else
        {
            $query = "insert into `#__royalty`(`royalty_name`) values('" . $royalty_name . "')";
            $db->setQuery($query);
            $db->query(); 
        }      
    }
    function royalty_details()
    {
        $db = JFactory::getDbO();
        
        $royalty_id = intval(JRequest::getVar("royalty_id"));
        $query = "select * from `#__royalty` where id=" . $royalty_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());    
    }
    function update_royalty()
    {
         $db = JFactory::getDBO();
        
        $royalty_name = strtoupper(JRequest::getVar('royalty_name'));
        $royalty_id = intval(JRequest::getVar('royalty_id'));
        
        $query = "select count(*) from `#__royalty` where royalty_name='" . $royalty_name . "' and id<>" . $royalty_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Royalty Name already exists.";
        }
        else
        {   
            $query = "update `#__royalty` set `royalty_name`='" . $royalty_name . "' where `id`=" . $royalty_id;
            $db->setQuery($query);
            $db->query();
        }     
    }
    function delete_royalty()
    {
        $db = JFactory::getDbO();
        $royalty_id = intval(JRequest::getVar("royalty_id"));
        $query = "delete from `#__royalty` where `id`=" . $royalty_id;
        $db->setQuery($query);
        $db->query();    
    } 
    
    function save_expense_head()
    {
        $db = JFactory::getDBO();
         
        $expense_head = strtoupper(JRequest::getVar('expense_head'));
        
        $query = "select count(*) from `#__expense_head` where expense_head='" . $expense_head . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Expense Head already exists.";
        }
        else
        {
            
            $query = "insert into `#__expense_head`(`expense_head`) values('" . $expense_head . "')";
            $db->setQuery($query);
            $db->query();
        }        
    }
    function details_expense_head()
    {
        $db = JFactory::getDbO();
        
        $expense_head_id = intval(JRequest::getVar("expense_head_id"));
        $query = "select * from `#__expense_head` where id=" . $expense_head_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());    
    }
    function update_expense_head()
    {
        $db = JFactory::getDBO();
        
        $expense_head = strtoupper(JRequest::getVar('expense_head')) ;
        $expense_head_id = intval(JRequest::getVar('expense_head_id'));
        //$location_name = strtoupper(addslashes(JRequest::getVar('location_name')));
        
        $query = "select count(*) from `#__expense_head` where expense_head='" . $expense_head . "' and id<>" . $expense_head_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Expense Head already exists.";
        }
        else
        {   
            $query = "update `#__expense_head` set `expense_head`='" . $expense_head . "' where `id`=" . $expense_head_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Vehicle " . $vehicle_type .  " has been updated.");
        }    
    }
    function delete_expense_head()
    {
        $db = JFactory::getDbO();
        $expense_head_id = intval(JRequest::getVar("expense_head_id"));
        $query = "delete from `#__expense_head` where `id`=" . $expense_head_id;
        $db->setQuery($query);
        $db->query();    
    }
    
    function save_product()
    {
        $db = JFactory::getDBO();
         
        $product_name = strtoupper(JRequest::getVar('product_name'));
        $unit_id = intval(JRequest::getVar('unit_name'));
        $gst_percent = floatval(JRequest::getVar('gst_percent'));  
        $hsn_code = intval(JRequest::getVar('hsn_code')); 
        
        $query = "select count(*) from `#__products` where product_name='" . $product_name . "'";
        $db->setQuery($query);
        $count = intval($db->loadResult());
    
        if($count > 0 )
        {
            echo "Product name already exists.";
        }
        else
        {
            $query = "insert into `#__products`(`product_name`,`unit_id`,`gst_percent`,`hsn_code`) values('" . $product_name . "','" . $unit_id . "','" . $gst_percent . "','" . $hsn_code . "')";
            $db->setQuery($query);
            $db->query();
            Functions::log_activity("Product " . $product_name .  " has been updated.");
            
            echo "ok";    
        } 
    }
    
    function product_details()
    {
        $db = JFactory::getDbO();
        
        $product_id = intval(JRequest::getVar("product_id"));
        $query = "select * from `#__products` where id=" . $product_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());    
    }
    function update_product()
    {
        $db = JFactory::getDBO();
        
        $product_name = strtoupper(JRequest::getVar('product_name')) ;
        $product_id = intval(JRequest::getVar('product_id'));
        $unit_id = intval(JRequest::getVar('unit_name'));
        $gst_percent = floatval(JRequest::getVar('gst_percent'));  
        $hsn_code = JRequest::getVar('hsn_code');  
        //$location_name = strtoupper(addslashes(JRequest::getVar('location_name')));
        //echo $product_id; exit;
        
        $query = "select count(*) from `#__products` where product_name='" . $product_name . "'and id<>" . $product_id;
        $db->setQuery($query);
        $count = intval($db->loadResult());
        
        if($count > 0)
        {
            echo "Product name already exists.";
        }
        else
        {   
            $query = "update `#__products` set `product_name`='" . $product_name . "', `unit_id`=".$unit_id.", `gst_percent`=".$gst_percent.",`hsn_code`='" . $hsn_code . "' where `id`=" . $product_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Product " . $product_name .  " has been updated.");
            
            echo "ok";
        }     
    }
    function delete_product()
    {
        $db = JFactory::getDBO();
        $product_id = intval(JRequest::getVar("product_id"));
        $query = "delete from `#__products` where `id`=" . $product_id;
        $db->setQuery($query);
        $db->query();     
    }
    
    function save_notepad()
    {
        $db = JFactory::getDBO();
        $notepad = JRequest::getVar('notepad');
        $due_date = JRequest::getVar('due_date');
        
        $query = "select count(*) from `#__notepad` where notepad='". $notepad ."'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Notepad already exist.";    
        }
        else
        {
            $query = "insert into `#__notepad`(`notepad`,`due_date`) values('" . $notepad . "','" . $due_date ."')";
            $db->setQuery($query);
            $db->query();    
        }   
    }
    function notepad_details()
    {
        $db = JFactory::getDbO();
        
        $notepad_id = intval(JRequest::getVar("notepad_id"));
        $query = "select * from `#__notepad` where id=" . $notepad_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());    
    }
    function update_notepad()
    {
        $db = JFactory::getDBO();
        
        $notepad = JRequest::getVar('notepad') ;
        $due_date = JRequest::getVar('due_date') ;
        $notepad_id = intval(JRequest::getVar('notepad_id'));
        
        $query = "select count(*) from `#__notepad` where notepad='" . $notepad . "' and id<>" . $notepad_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Notepad already exists.";
        }
        else
        {   
            $query = "update `#__notepad` set `notepad`='" . $notepad . "',`due_date`='" . $due_date . "' where `id`=" . $notepad_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Notepad " . $notepad .  " has been updated.");
        }    
    }
    function delete_notepad()
    {
        $db = JFactory::getDBO();
        $notepad_id = intval(JRequest::getVar("notepad_id"));
        $query = "delete from `#__notepad` where `id`=" . $notepad_id;
        $db->setQuery($query);
        $db->query();        
    }
    
    function save_royalty_booklet()
    {
        $db = JFactory::getDBO();
        
        
        $entry_date = date("Y-m-d");
        $booklet_name = strtoupper(JRequest::getVar('booklet_name'));
        $rb_no_from =intval(JRequest::getVar('rb_no_from')) ;
        $rb_no_to =intval(JRequest::getVar('rb_no_to')) ;
        $royalty_type =intval(JRequest::getVar('royalty_type')) ;
        $purchase_date = date("Y-m-d", strtotime(JRequest::getVar("purchase_date")));
        if($royalty_type == 1)
        {
            $purchase_date = null;      
        }
        $supplier_id =intval(JRequest::getVar('supplier_id')) ;
        $quantity =floatval(JRequest::getVar('quantity')) ;
        $rate =floatval(JRequest::getVar('rate')) ;
        $total_pages = intval(JRequest::getVar('total_pages')) ;
        
        $creation_date = date("Y-m-d h:i:s");
        
        $query = "select count(id) from `#__royalty_booklets` where `booklet_name`='" . $booklet_name . "' ";
        $db->setQuery($query);
        $royalty_booklet_name = $db->loadResult();
        
        if($royalty_booklet_name > 0)
        {
            echo "Royalty name " . $booklet_name . " already exists.";         
        }
        else
        {

            $query = "select `rb_no` from `#__royalty_booklet_items` where `rb_no` between " . $rb_no_from . " and " . $rb_no_to . " ";
            $db->setQuery($query);
            $count = $db->loadResult(); 

            if($count > 0)
            {
                echo "Royalty number " . $count . " already exists.";
            }
            else
            {
               
                $query = "insert into `#__royalty_booklets`(`entry_date`,`booklet_name`,`rb_no_from`,`rb_no_to`,`royalty_type`,purchase_date,`supplier_id`,`quantity`,`rate`,`total_amount`,`total_pages`) values('" . $entry_date . "','" . $booklet_name . "','" . $rb_no_from ."','" . $rb_no_to . "','" . $royalty_type . "','" . $purchase_date . "','" . $supplier_id . "','" . $quantity . "','" . $rate . "','". floatval($quantity * $rate) ."','" . $total_pages . "')";
                $db->setQuery($query);
                $db->query();
                
                $royalty_id = $db->insertid();
                 
                foreach (range($rb_no_from, $rb_no_to) as $rb_no) 
                {
                    $query = "insert into `#__royalty_booklet_items`(`booklet_id`,`rb_no`) values('" . $royalty_id . "','" . $rb_no ."')";
                    $db->setQuery($query);
                    $db->query();   
                }
                if($royalty_type == PURCHASE)
                {
                     
                    $total_amount = $quantity * $rate;
                    
                    $royalty_purchase = new stdClass();
                    
                    $royalty_purchase->bill_date = $purchase_date;
                    $royalty_purchase->supplier_id = $supplier_id;
                    $royalty_purchase->royalty_purchase_id = $royalty_id;
                    $royalty_purchase->gross_amount = $total_amount;
                    $royalty_purchase->total_amount = $total_amount;
                    $royalty_purchase->remarks = "Royalty Purchase";
                    $royalty_purchase->creation_date = $creation_date;
                
                    $db->insertObject("#__purchase", $royalty_purchase, "");
                    
                    $query = "update `#__suppliers` set `account_balance`=account_balance+" . floatval($total_amount) . " where id=" . intval($supplier_id);
                    $db->setQuery($query);
                    $db->query(); 
                    
                    //Functions::adjust_supplier_account($supplier_id);   
                }
            }    
        }
    }
    
    function royalty_booklet_details()
    {
        $db = JFactory::getDbO();
        
        $royalty_booklet_id = intval(JRequest::getVar("royalty_booklet_id"));
        $query = "select * from `#__royalty_booklets` where id=" . $royalty_booklet_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());          
    }
    
    
    function update_royalty_booklet()
    {
        $db = JFactory::getDBO();
        $entry_date = date("Y-m-d");
        $booklet_name = strtoupper(JRequest::getVar('booklet_name'));
        $rb_no_from = intval(JRequest::getVar('rb_no_from'));
        $rb_no_to = intval(JRequest::getVar('rb_no_to'));
        $royalty_type =intval(JRequest::getVar('royalty_type')) ;
        $purchase_date = date("Y-m-d", strtotime(JRequest::getVar("purchase_date")));
        if($royalty_type == 1)
        {
            $purchase_date = null;      
        }
        
        $supplier_id =intval(JRequest::getVar('supplier_id')) ;
        $quantity =floatval(JRequest::getVar('quantity')) ;
        $rate =floatval(JRequest::getVar('rate')) ;
        $total_pages = intval(JRequest::getVar('total_pages')) ;
        
        $royalty_booklet_id = intval(JRequest::getVar('royalty_booklet_id'));
        
        $query = "select count(*) from `#__royalty_booklets` where booklet_name='" . $booklet_name . "' and id<>" . $royalty_booklet_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Royalty name " . $booklet_name . " already exists. ";    
        }
        else
        {
            
            $new_total_amount = $quantity * $rate ;
            
            $query = "select supplier_id,quantity,rate from `#__royalty_booklets` where id=".$royalty_booklet_id;
            $db->setQuery($query);
            $previous_royalty_details = $db->loadObject();
            $previous_total_amount = intval($previous_royalty_details->quantity) * floatval($previous_royalty_details->rate)  ;
            
            $query = "update `#__royalty_booklets` set `entry_date`='" . $entry_date . "',`booklet_name`='" . $booklet_name . "',`rb_no_from`='" . $rb_no_from . "',`rb_no_to`='". $rb_no_to ."',`royalty_type`='" . $royalty_type . "',`purchase_date`='" . $purchase_date ."',`supplier_id`='" . $supplier_id . "',`quantity`=". $quantity .",`rate`='". $rate ."',`total_amount`='" . $new_total_amount . "',`total_pages`='". $total_pages . "' where `id`=" . $royalty_booklet_id;
            $db->setQuery($query);
            $db->query(); 
            
            if($royalty_type == 3)
            {
                
                $query = "select * from `#__purchase` where royalty_purchase_id=".$royalty_booklet_id;
                $db->setQuery($query);
                $royalty_details = $db->loadObject();
                
                $id = intval($royalty_details->id);
                
                $royalty_purchase = new stdClass();
                    
                $royalty_purchase->id = $id;
                $royalty_purchase->bill_date = $purchase_date;
                $royalty_purchase->supplier_id = $supplier_id;
                $royalty_purchase->royalty_purchase_id = $royalty_booklet_id;
                $royalty_purchase->gross_amount = $new_total_amount;
                $royalty_purchase->total_amount = $new_total_amount;
                if($new_total_amount > floatval($royalty_details->amount_paid))
                {
                    $royalty_purchase->status = UNPAID;
                    $db->updateObject("#__purchase", $royalty_purchase, "id");    
                }
            
                $db->updateObject("#__purchase", $royalty_purchase, "id");
                
                $query = "update `#__suppliers` set `account_balance`=account_balance-" . floatval($previous_total_amount) . " where id=" . intval($royalty_details->supplier_id);
                $db->setQuery($query);
                $db->query();
                
                $query = "update `#__suppliers` set `account_balance`=account_balance+" . floatval($new_total_amount) . " where id=" . intval($supplier_id);
                $db->setQuery($query);
                $db->query();
           }        
        }
    }
    
    
    // start backup code
    
    /*function update_royalty_booklet()
    {
        $db = JFactory::getDBO();
        $entry_date = date("Y-m-d");
        $booklet_name = strtoupper(JRequest::getVar('booklet_name'));
        $rb_no_from = intval(JRequest::getVar('rb_no_from'));
        $rb_no_to = intval(JRequest::getVar('rb_no_to'));
        $royalty_type =intval(JRequest::getVar('royalty_type')) ;
        $purchase_date = date("Y-m-d", strtotime(JRequest::getVar("purchase_date")));
        if($royalty_type == 1)
        {
            $purchase_date = null;      
        }
        
        $supplier_id =intval(JRequest::getVar('supplier_id')) ;
        $quantity =floatval(JRequest::getVar('quantity')) ;
        $rate =floatval(JRequest::getVar('rate')) ;
        $total_pages = intval(JRequest::getVar('total_pages')) ;
        
        $royalty_booklet_id = intval(JRequest::getVar('royalty_booklet_id'));
        
        $query = "select count(*) from `#__royalty_booklets` where booklet_name='" . $booklet_name . "' and id<>" . $royalty_booklet_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Royalty name " . $booklet_name . " already exists. ";    
        }
        else
        {
            
            $new_total_amount = $quantity * $rate ;
            
            $query = "select supplier_id,quantity,rate from `#__royalty_booklets` where id=".$royalty_booklet_id;
            $db->setQuery($query);
            $previous_royalty_details = $db->loadObject();
            $previous_total_amount = intval($previous_royalty_details->quantity) * floatval($previous_royalty_details->rate)  ;
            
            $query = "update `#__royalty_booklets` set `entry_date`='" . $entry_date . "',`booklet_name`='" . $booklet_name . "',`rb_no_from`='" . $rb_no_from . "',`rb_no_to`='". $rb_no_to ."',`royalty_type`='" . $royalty_type . "',`purchase_date`='" . $purchase_date ."',`supplier_id`='" . $supplier_id . "',`quantity`=". $quantity .",`rate`='". $rate ."',`total_amount`='" . $new_total_amount . "',`total_pages`='". $total_pages . "' where `id`=" . $royalty_booklet_id;
            $db->setQuery($query);
            $db->query(); 
            
            if($royalty_type == 3)
            {
                
                $query = "select * from `#__purchase` where royalty_purchase_id=".$royalty_booklet_id;
                $db->setQuery($query);
                $royalty_details = $db->loadObject();
                
                $id = intval($royalty_details->id);
                
                $royalty_purchase = new stdClass();
                    
                $royalty_purchase->id = $id;
                $royalty_purchase->bill_date = $purchase_date;
                $royalty_purchase->supplier_id = $supplier_id;
                $royalty_purchase->royalty_purchase_id = $royalty_booklet_id;
                $royalty_purchase->gross_amount = $new_total_amount;
                $royalty_purchase->total_amount = $new_total_amount;
                if($new_total_amount > floatval($royalty_details->amount_paid))
                {
                    $royalty_purchase->status = UNPAID;
                    $db->updateObject("#__purchase", $royalty_purchase, "id");    
                }
            
                $db->updateObject("#__purchase", $royalty_purchase, "id");
                
                $query = "update `#__suppliers` set `account_balance`=account_balance-" . floatval($previous_total_amount) . " where id=" . intval($royalty_details->supplier_id);
                $db->setQuery($query);
                $db->query();
                
                $query = "update `#__suppliers` set `account_balance`=account_balance+" . floatval($new_total_amount) . " where id=" . intval($supplier_id);
                $db->setQuery($query);
                $db->query();
           }        
        }
    } */   
    
    // end backup code
    
    function delete_royalty_booklet()
    {
        $db = JFactory::getDBO();
        $royalty_booklet_id = intval(JRequest::getVar("royalty_booklet_id"));
        
        //$query = "select p.*,rb.id royalty_id,rb.royalty_type from `#__purchase` p inner join `#__royalty_booklets` rb on p.royalty_purchase_id=rb.id where royalty_purchase_id =" . $royalty_booklet_id;
        $query = "select rb.*,p.id purchase_id,p.status from `#__royalty_booklets` rb inner join `#__purchase` p on rb.id=p.royalty_purchase_id where rb.id =" . $royalty_booklet_id;
        $db->setQuery($query);
        $previous_royalty_purchase = $db->loadObject();
        
        $royalty_type = intval($previous_royalty_purchase->royalty_type);
        $total_amount = floatval($previous_royalty_purchase->total_amount);
        $supplier_id = intval($previous_royalty_purchase->supplier_id);
        $purchase_id = intval($previous_royalty_purchase->purchase_id);
        $status = intval($previous_royalty_purchase->status);
        
        //print_r($status); exit;
        
        if($royalty_type == PURCHASE)
        {
            if($status == PAID)
            {
                $query = "delete from `#__royalty_booklets` where `id`=" . $royalty_booklet_id;
                $db->setQuery($query);
                $db->query();
                
                $query = "delete from `#__royalty_booklet_items` where `booklet_id`=" . $royalty_booklet_id;
                $db->setQuery($query);
                $db->query();
                
                $query = "delete from `#__purchase` where `id`=" . $purchase_id;
                $db->setQuery($query);
                $db->query();        
            }
            else
            { 
                $query = "update `#__suppliers` set `account_balance`=account_balance-" . floatval($total_amount) . " where id=" . intval($supplier_id);
                $db->setQuery($query);
                $db->query();
                
                //Functions::adjust_supplier_account($supplier_id);
                
                $query = "delete from `#__royalty_booklets` where `id`=" . $royalty_booklet_id;
                $db->setQuery($query);
                $db->query();
                
                $query = "delete from `#__royalty_booklet_items` where `booklet_id`=" . $royalty_booklet_id;
                $db->setQuery($query);
                $db->query();
                
                $query = "delete from `#__purchase` where `id`=" . $purchase_id;
                $db->setQuery($query);
                $db->query();         
            }
        }
        else
        {
            $query = "delete from `#__royalty_booklets` where `id`=" . $royalty_booklet_id;
            $db->setQuery($query);
            $db->query();  
        
            $query = "delete from `#__royalty_booklet_items` where `booklet_id`=" . $royalty_booklet_id;
            $db->setQuery($query);
            $db->query();    
        } 
    }
    
    function save_location()
    {
        $db = JFactory::getDBO();
        
        $location_name = strtoupper(addslashes(JRequest::getVar('location_name')));
        
        $query = "select count(*) from `#__inventory_locations` where location_name='" . $location_name . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Location already exists.";
        }
        else
        {                      
            $query = "insert into `#__inventory_locations`(`location_name`) values('" . $location_name . "')";
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Location " . $location_name .  " has been added.");
        }
    }   
    
    function location_details()
    {
        $db = JFactory::getDbO();
        
        $location_id = intval(JRequest::getVar("location_id"));

        $query = "select * from `#__inventory_locations` where id=" . $location_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());   
    }
    
    function update_location()
    {
        $db = JFactory::getDBO();
        
        $location_name = strtoupper(addslashes(JRequest::getVar('location_name')));
        $location_id = intval(JRequest::getVar('location_id'));
        
        $query = "select count(*) from `#__inventory_locations` where location_name='" . $location_name . "' and id<>" . $location_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Location already exists.";
        }
        else
        {                      
            $query = "update `#__inventory_locations` set `location_name`='" . $location_name . "' where `id`=" . $location_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Location " . $location_name .  " has been updated.");
        }
    }
    
    function delete_location()
    {   
        $db = JFactory::getDBO();
        $location_id = intval(JRequest::getVar('location_id'));
        
       // $count = 0;
        
        /*$query = "select count(*) from `#__hr_employees` where location_id=" . $location_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(*) from `#__inventory_items` where location_id=" . $location_id;
        $db->setQuery($query);
        $count += intval($db->loadResult()); */
        
        /*$query = "select count(id) from `#__purchase_orders` where location_id=" . $location_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_invoice` where location_id=" . $location_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_returns` where location_id=" . $location_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_invoice` where location_id=" . $location_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_returns` where location_id=" . $location_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__stock_transfer` where location_from_id=" . $location_id . " or location_to_id=" . $location_id;
        $db->setQuery($query);
        $count += intval($db->loadResult()); 
        
        if($count > 0)
        {
            return "Location cannot be deleted. It has dependencies.";
        }

        $query = "select location_name from `#__inventory_locations` where id=" . $location_id;
        $db->setQuery($query);
        $location_name = $db->loadResult();*/
        
        $query = "delete from `#__inventory_locations` where `id`=" . $location_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Location " . $location_name . " has been deleted."); 
        return "Location deleted successfully.";
    }
    
     // Location
    
    function save_route()
    {
        $db = JFactory::getDBO();
        
        $route_name = strtoupper(addslashes(JRequest::getVar('route_name')));
        
        $query = "select count(*) from `#__routes` where route_name='" . $route_name . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Route already exists.";
        }
        else
        {                      
            $query = "insert into `#__routes`(`route_name`) values('" . $route_name . "')";
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Route " . $route_name .  " has been added.");
        }
    }   
    
    function route_details()
    {
        $db = JFactory::getDbO();
        
        $route_id = intval(JRequest::getVar("route_id"));

        $query = "select * from `#__routes` where id=" . $route_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());   
    }
    
    function update_route()
    {
        $db = JFactory::getDBO();
        
        $route_name = strtoupper(addslashes(JRequest::getVar('route_name')));
        $route_id = intval(JRequest::getVar('route_id'));
        
        $query = "select count(*) from `#__routes` where route_name='" . $route_name . "' and id<>" . $route_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Route already exists.";
        }
        else
        {                      
            $query = "update `#__routes` set `route_name`='" . $route_name . "' where `id`=" . $route_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Route " . $route_name .  " has been updated.");
        }
    }
    
    function delete_route()
    {   
        $db = JFactory::getDBO();
        $route_id = intval(JRequest::getVar('route_id'));
        
        $count = 0;
        
        $query = "select count(*) from `#__customers` where route_id=" . $route_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            return "Route cannot be deleted. It has dependencies.";
        }

        $query = "select route_name from `#__routes` where id=" . $route_id;
        $db->setQuery($query);
        $route_name = $db->loadResult();
        
        $query = "delete from `#__routes` where `id`=" . $route_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Route " . $route_name . " has been deleted."); 
        return "Route deleted successfully.";
    }
    
     function save_salesman()
    {
        $db = JFactory::getDBO();
        
        $salesman_name = strtoupper(addslashes(JRequest::getVar('salesman_name')));
        
        $query = "select count(*) from `#__salesmans` where salesman_name='" . $salesman_name . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Salesman already exists.";
        }
        else
        {                      
            $query = "insert into `#__salesmans`(`salesman_name`) values('" . $salesman_name . "')";
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Salesman " . $salesman_name .  " has been added.");
        }
    }   
    
    function salesman_details()
    {
        $db = JFactory::getDbO();
        
        $salesman_id = intval(JRequest::getVar("sm_id"));

        $query = "select * from `#__salesmans` where id=" . $salesman_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());   
    }
    
    function update_salesman()
    {
        $db = JFactory::getDBO();
        
        $salesman_name = strtoupper(addslashes(JRequest::getVar('salesman_name')));
        $salesman_id = intval(JRequest::getVar('sm_id'));
        
        $query = "select count(*) from `#__salesmans` where salesman_name='" . $salesman_name . "' and id<>" . $salesman_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Salesman already exists.";
        }
        else
        {                      
            $query = "update `#__salesmans` set `salesman_name`='" . $salesman_name . "' where `id`=" . $salesman_id;
            $db->setQuery($query); 
            $db->query();

            Functions::log_activity("Route " . $salesman_name .  " has been updated.");
        }
    }
    
    function delete_salesman()
    {   
        $db = JFactory::getDBO();
        $salesman_id = intval(JRequest::getVar('sm_id'));
        
        $count = 0;
        
        $query = "select count(id) from `#__sales_order` where salesman_id=" . $salesman_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_invoice` where salesman_id=" . $salesman_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            return "Salesman cannot be deleted. It has dependencies.";
        }

        $query = "select salesman_name from `#__salesmans` where id=" . $salesman_id;
        $db->setQuery($query);
        $salesman_name = $db->loadResult();
        
        $query = "delete from `#__salesmans` where `id`=" . $salesman_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Salesman " . $salesman_name . " has been deleted."); 
        return "Salesman deleted successfully.";
    }
    
    // Category
    
    function create_category()
    {
        $db = JFactory::getDBO();
        $category_name = strtoupper(addslashes(JRequest::getVar("category_name")));
        
        $query = "select count(*) from `#__category_list` where `category_name`='" . $category_name . "'";
        $db->setQuery($query);
        $count = intval($db->loadResult());
        
        if($count > 0)
        {
            echo "Category already exists.";
        }
        else
        {
            $query = "insert into `#__category_list` (`category_name`) values('" . $category_name . "')";
            $db->setQuery($query);
            $db->query();
            
            Functions::log_activity("Category " . $category_name . " has been added.");
        }
    }
    
    function category_details()
    {
        $db = JFactory::getDBO();
        $category_id = intval(JRequest::getVar("category_id"));
        
        $query = "select `category_name` from `#__category_list` where id=" . $category_id;
        $db->setQuery($query);
        $category_name = $db->loadResult();
        echo json_encode($category_name);
    }
    
    function update_category()
    {
        $db = JFactory::getDBO();
        $category_id = intval(JRequest::getVar("category_id"));
        $category_name = strtoupper(addslashes(JRequest::getVar("category_name")));
        
        $query = "select count(*) from `#__category_list` where category_name='" . $category_name . "' and id<>" . $category_id;
        $db->setQuery($query);
        $count = intval($db->loadResult());
        
        if($count > 0)
        {
            echo "Category already exists.";
        }
        else
        {
            $query = "update `#__category_list` set `category_name`='" . $category_name . "' where id=" . $category_id;                                                
            $db->setQuery($query);
            $db->query();
            
            Functions::log_activity("Category " . $category_name . " has been updated.");
            echo "ok";
        }
    }
    
    function delete_category()
    {
        $db = JFactory::getDBO();
        $category_id = intval(JRequest::getVar("category_id"));
        $count = 0;
        
        $query = "select count(*) from `#__items` where category_id=" . $category_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_order_items` where category_id=" . $category_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_invoice_items` where category_id=" . $category_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_return_items` where category_id=" . $category_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_order_items` where category_id=" . $category_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_invoice_items` where category_id=" . $category_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_return_items` where category_id=" . $category_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__stock_transfer_items` where category_id=" . $category_id;
        $db->setQuery($query);
        $count += intval($db->loadResult()); 
        
        if($count > 0)
        {
            return "Category cannot be deleted. It has dependencies.";
        }
        else
        {
            $query = "select `category_name` from `#__category_list` where id=" . $category_id;
            $db->setQuery($query);
            $category_name = $db->loadResult();
            
            $query = "delete from `#__category_list` where id=" . $category_id;
            $db->setQuery($query);
            $db->query();
            
            Functions::log_activity("Category " . $category_name . " has been deleted."); 

            return "Category deleted successfully.";
        }
    }
    
    // Items 
    
    function create_item()
    {
        $db = JFactory::getDBO();
        
        $item_name = strtoupper(addslashes(JRequest::getVar("item_name")));
        $item_category_name = strtoupper(addslashes(JRequest::getVar("item_category_name")));
        $item_category_id = intval(JRequest::getVar("item_category_id"));
        $hsn_code = JRequest::getVar("hsn_code");
        $gst_percent = floatval(JRequest::getVar("gst_percent"));
        $last_purchase_rate = floatval(JRequest::getVar("last_purchase_rate"));
        $sale_price1 = floatval(JRequest::getVar("sale_price1"));
        $sale_price2 = floatval(JRequest::getVar("sale_price2"));
        $piece_per_pack = intval(JRequest::getVar("piece_per_pack"));
        
        $location_ids = JRequest::getVar("location_ids");
        $opening_stocks = JRequest::getVar("opening_stocks");
        
        $query = "select count(*) from `#__items` where `item_name`='" . $item_name . "' and category_id=" . $item_category_id;
        $db->setQuery($query);
        $count = intval($db->loadResult());
        
        if($count > 0)
        {
            echo "Item name already exists under " . $item_category_name . ".";
        }
        else
        {
            $query = "insert into `#__items` (`item_name`, `category_id`,`hsn_code`,`gst_percent`, `last_purchase_rate`, `sale_price1`, `sale_price2`, `piece_per_pack`) values('" . $item_name . "', " . $item_category_id . ",'" . $hsn_code . "'," . $gst_percent . ", " . $last_purchase_rate . ", " . $sale_price1 . ", " . $sale_price2 . ", " . $piece_per_pack . ")";
            $db->setQuery($query);
            $db->query();
            
            $item_id = intval($db->insertid());
            
            for($i=0;$i<count($location_ids);$i++)
            {
                //if(floatval($opening_stocks[$i]) > 0)
                {
                    $query = "insert into `#__inventory_items` (`item_id`,`location_id`,`opening_stock`,`stock`) values(" . $item_id . "," . intval($location_ids[$i]) . "," . floatval($opening_stocks[$i]) . "," . floatval($opening_stocks[$i]) . ")";
                    $db->setQuery($query);
                    $db->query();
                }
            }
            
            Functions::log_activity("Item " . $item_name . " under category " . $item_category_name . " has been added.");
        }
    }
    
    function item_details()
    {
        $db = JFactory::getDBO();
        $item_id = intval(JRequest::getVar("item_id"));
        
        $query = "select * from `#__items` where id=" . $item_id;
        $db->setQuery($query);
        $item = $db->loadObject();
        echo json_encode($item);
    }
    
    function get_locationwise_items_opening_balance()
    {
        $db = JFactory::getDbo();
        $item_id = intval(JRequest::getVar("item_id"));
        
        $db->setQuery("select * from `#__inventory_items` where item_id=" . $item_id);
        $details = $db->loadObjectList();
        
        echo json_encode($details);
    }
    
    function update_item()
    {
        // stock is updated by calculating the difference in old opening stock and current opening stock
        $db = JFactory::getDBO();
        
        $item_id = intval(JRequest::getVar("item_id"));
        $item_name = strtoupper(addslashes(JRequest::getVar("item_name")));
        $item_category_name = strtoupper(addslashes(JRequest::getVar("item_category_name")));
        $item_category_id = intval(JRequest::getVar("item_category_id"));
        $hsn_code = JRequest::getVar("hsn_code");
        $gst_percent = floatval(JRequest::getVar("gst_percent"));
        $piece_per_pack = intval(JRequest::getVar("piece_per_pack"));
        $sale_price1 = floatval(JRequest::getVar("sale_price1"));
        $sale_price2 = floatval(JRequest::getVar("sale_price2"));
        //$last_purchase_rate = floatval(JRequest::getVar("last_purchase_rate"));
        
        $location_ids = JRequest::getVar("location_ids");
        $opening_stocks = JRequest::getVar("opening_stocks");
        
        $query = "select count(*) from `#__items` where item_name='" . $item_name . "' and category_id=" . $item_category_id . " and id<>" . $item_id;
        $db->setQuery($query);
        $count = intval($db->loadResult());
        
        if($count > 0)
        {
            echo "Item name already exists under " . $item_category_name . ".";
        }
        else
        {
            $query = "update `#__items` set `item_name`='" . $item_name . "',`hsn_code`='" . $hsn_code . "',`gst_percent`=" . $gst_percent . ", `piece_per_pack`=" . $piece_per_pack . ", `sale_price1`=" . $sale_price1 . ", `sale_price2`=" . $sale_price2 . " where id=" . $item_id;                                                
            $db->setQuery($query);
            $db->query();
            
            for($i=0;$i<count($location_ids);$i++)
            {
                //if(floatval($opening_stocks[$i]) > 0)
                {
                    $query = "select count(*) from `#__inventory_items` where item_id=" . $item_id . " and location_id=" . intval($location_ids[$i]);
                    $db->setQuery($query);
                    if(intval($db->loadResult()) == 0)
                    {
                        $query = "insert into `#__inventory_items` (`item_id`,`location_id`,`opening_stock`,`stock`) values(" . $item_id . "," . intval($location_ids[$i]) . "," . floatval($opening_stocks[$i]) . "," . floatval($opening_stocks[$i]) . ")";
                        $db->setQuery($query);
                        $db->query();
                    }
                    else
                    {
                        $stock_column = "";
                        
                        $query = "select `opening_stock` from `#__inventory_items` where item_id=" . $item_id . " and location_id=" . intval($location_ids[$i]);
                        $db->setQuery($query);
                        $old_opening_stock = floatval($db->loadResult());
                        
                        $difference_in_opening_stock = floatval($opening_stocks[$i]) - $old_opening_stock;
                        if($difference_in_opening_stock != 0)
                        {
                            $stock_column = ", `stock`=stock+" . $difference_in_opening_stock;
                        }
                        
                        $query = "update `#__inventory_items` set `opening_stock`=" . floatval($opening_stocks[$i]) . ($stock_column != "" ? $stock_column : "") . " where item_id=" . $item_id . " and location_id=" . intval($location_ids[$i]);
                        $db->setQuery($query);
                        $db->query();
                    }
                }
            }
            
            Functions::log_activity("Item " . $item_name . " under category " . $item_category_name . " has been updated.");
            echo "ok";
        }
    }
    
    function delete_item()
    {
        $db = JFactory::getDBO();
        $item_id = intval(JRequest::getVar("item_id"));
        $count = 0;
        
        $query = "select count(id) from `#__inventory_items` where item_id=" . $item_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_order_items` where item_id=" . $item_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_invoice_items` where item_id=" . $item_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_return_items` where item_id=" . $item_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_order_items` where item_id=" . $item_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_invoice_items` where item_id=" . $item_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_return_items` where item_id=" . $item_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__stock_transfer_items` where item_id=" . $item_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            echo "Item cannot be deleted. It has dependencies.";
            exit;
        }
        else
        {
            $query = "select `item_name` from `#__items` where id=" . $item_id;
            $db->setQuery($query);
            $item_name = $db->loadResult();
            
            $query = "delete from `#__items` where id=" . $item_id;
            $db->setQuery($query);
            $db->query();
            
            $query = "delete from `#__inventory_items` where item_id=" . $item_id;
            $db->setQuery($query);
            $db->query();
            
            Functions::log_activity("Item " . $item_name . " has been deleted."); 

            echo "Item deleted successfully.";
        }
    }
    
    // Supplier
    
    function create_supplier()
    {
        $db = JFactory::getDbo();
        
        $supplier_name = strtoupper(JRequest::getVar("supplier_name"));
        $supplier_address = JRequest::getVar("supplier_address");
        $city_id = intval(JRequest::getVar("city_id"));
        $state_id = intval(JRequest::getVar("state_id"));
        $gstin = intval(JRequest::getVar("gstin"));
        $gst_registration_type = intval(JRequest::getVar("gst_registration_type"));
        $contact_no = JRequest::getVar("contact_no");
        $other_contact_numbers = JRequest::getVar("other_contact_numbers");
        $opening_balance = floatval(JRequest::getVar("opening_balance"));
        $comment = JRequest::getVar("comment");
        
        $query = "select count(supplier_name) from `#__suppliers` where supplier_name='" . $supplier_name. "'";
        $db->setQuery($query);
        $count = intval($db->loadResult()); 
       
        if($count > 0)
        {
            echo "Supplier already exists.";
        }
        else
        {
            $query = "select count(supplier_name) from `#__suppliers` where supplier_name='" . $supplier_name. "' ";
            $db->setQuery($query);
            $count = intval($db->loadResult());
            
            $supplier = new stdClass();
        
            $supplier->supplier_name = $supplier_name;
            $supplier->supplier_address = $supplier_address;
            $supplier->city_id = $city_id;
            $supplier->state_id = $state_id;
            $supplier->gstin = $gstin;
            $supplier->gst_registration_type = $gst_registration_type;
            $supplier->contact_no = $contact_no;
            $supplier->other_contact_numbers = $other_contact_numbers;
            $supplier->opening_balance = $opening_balance;
            $supplier->account_balance = $opening_balance;
            $supplier->comment = $comment;
            
            if($db->insertObject('#__suppliers',$supplier,''))
            {
                Functions::log_activity("Supplier " . $supplier_name . " has been added.");
                echo "ok";
            }
            else
            {
                echo "Failed to save supplier.";
            }            
        }
    }
    
    function supplier_details()
    {
        $db = JFactory::getDbo();
        
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        
        $query = "select s.*,st.name state_name,st.gst_state_code from `#__suppliers` s left join `#__states` st on s.state_id=st.id where s.id=" .$supplier_id;
        $db->setQuery($query);
        $supplier = $db->loadObject();
        
        echo json_encode($supplier);
    }
    
    function update_supplier()
    {
        $db = JFactory::getDbo();
        
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        $supplier_name = strtoupper(JRequest::getVar("supplier_name"));
        $supplier_address = JRequest::getVar("supplier_address");
        $city_id = intval(JRequest::getVar("city_id"));
        $state_id = intval(JRequest::getVar("state_id"));
        $gstin = intval(JRequest::getVar("gstin"));
        $gst_registration_type = intval(JRequest::getVar("gst_registration_type"));
        $contact_no = JRequest::getVar("contact_no");
        $other_contact_numbers = JRequest::getVar("other_contact_numbers");
        $opening_balance = floatval(JRequest::getVar("opening_balance"));
        $comment = JRequest::getVar("comment");
        
        $query = "select count(supplier_name) from `#__suppliers` where supplier_name='" . $supplier_name . "' and id<>" .$supplier_id;
        $db->setQuery($query);
        $count = intval($db->loadResult()); 
       
        if($count > 0)
        {
            echo "Supplier already exists.";
        }
        else
        {
            $query = "select `opening_balance`, `account_balance` from `#__suppliers` where id=" . $supplier_id;
            $db->setQuery($query);
            $previous_balance_details = $db->loadObject();
            
            $difference_in_opening_balance = $opening_balance - floatval($previous_balance_details->opening_balance);
            
            $supplier = new stdClass();
        
            $supplier->id = $supplier_id;
            $supplier->supplier_name = $supplier_name;
            $supplier->supplier_address = $supplier_address;
            $supplier->city_id = $city_id;
            $supplier->state_id = $state_id;
            $supplier->gstin = $gstin;
            $supplier->gst_registration_type = $gst_registration_type;
            $supplier->contact_no = $contact_no;
            $supplier->other_contact_numbers = $other_contact_numbers;
            $supplier->opening_balance = $opening_balance;
            
            if($difference_in_opening_balance != 0)
            {
                $supplier->account_balance = floatval($previous_balance_details->account_balance) + $difference_in_opening_balance;
            }
            
            $supplier->comment = $comment;
            
            if($db->updateObject('#__suppliers',$supplier,'id'))
            {
                Functions::log_activity("Supplier " . $supplier_name . " has been updated."); 
                echo "ok";
            }
            else
            {
                echo "Failed to update supplier.";
            }            
        }
    }
    
    function delete_supplier()
    {
        return false;
        $db = JFactory::getDbo();
        
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        
        $count = 0;
        
        $query = "select count(id) from `#__payments` where party_id=" . $supplier_id . " and payment_type=" . SUPPLIER_PAYMENT;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__payment_items` where party_id=" . $supplier_id . " and payment_type=" . SUPPLIER_PAYMENT; 
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_orders` where supplier_id=" . $supplier_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_invoice` where supplier_id=" . $supplier_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_returns` where supplier_id=" . $supplier_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            echo "Unable to delete supplier. It has dependencies.";
            exit;
        }
        
        $query = "select `supplier_name` from `#__suppliers` where id=" .$supplier_id;
        $db->setQuery($query);
        $supplier_name = $db->loadResult();
        
        $query = "delete from `#__suppliers` where id=" .$supplier_id;
        $db->setQuery($query);
        if($db->query())
        {
            Functions::log_activity("Supplier " . $supplier_name . " has been deleted."); 
            echo "ok";
        }
        else
        {
            echo "Failed to delete supplier.";
        }
    }
    
    // Transporter
    
    function create_transporter()
    {
        $db = JFactory::getDbo();
        
        $transporter_name = strtoupper(JRequest::getVar("transporter_name"));
        $transporter_address = JRequest::getVar("transporter_address");
        $city_id = intval(JRequest::getVar("city_id"));
        $state_id = intval(JRequest::getVar("state_id"));
        $gstin = intval(JRequest::getVar("gstin"));
        $gst_registration_type = intval(JRequest::getVar("gst_registration_type"));
        $contact_no = JRequest::getVar("contact_no");
        $other_contact_numbers = JRequest::getVar("other_contact_numbers");
        $opening_balance = floatval(JRequest::getVar("opening_balance"));
        $comment = JRequest::getVar("comment");
        
        $query = "select count(transporter_name) from `#__transporters` where transporter_name='" . $transporter_name . "' " ;
        //echo $query;exit;
        $db->setQuery($query);
        $count = intval($db->loadResult()); 
       
        if($count > 0)
        {
            echo "Transporter already exists.";
        }
        else
        {
            $transporter = new stdClass();
        
            $transporter->transporter_name = $transporter_name;
            $transporter->transporter_address = $transporter_address;
            $transporter->city_id = $city_id;
            $transporter->state_id = $state_id;
            $transporter->gstin = $gstin;
            $transporter->gst_registration_type = $gst_registration_type;
            $transporter->contact_no = $contact_no;
            $transporter->other_contact_numbers = $other_contact_numbers;
            $transporter->opening_balance = $opening_balance;
            $transporter->account_balance = $opening_balance;
            $transporter->comment = $comment;
            
            if($db->insertObject('#__transporters',$transporter,''))
            {
                Functions::log_activity("Transporter " . $transporter_name . " has been added.");
                echo "ok";
            }
            else
            {
                echo "Failed to save transporter.";
            }            
        }      
    }
    function transporter_details()
    {
        $db = JFactory::getDbo();
        
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        
        $query = "select t.*,st.name state_name,st.gst_state_code from `#__transporters` t left join `#__states` st on t.state_id=st.id where t.id=" .$transporter_id;
        $db->setQuery($query);
        $transporter = $db->loadObject();
        
        echo json_encode($transporter);        
    }
    function update_transporter()
    {
        $db = JFactory::getDbo();
        
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        $transporter_name = strtoupper(JRequest::getVar("transporter_name"));
        $transporter_address = JRequest::getVar("transporter_address");
        $city_id = intval(JRequest::getVar("city_id"));
        $state_id = intval(JRequest::getVar("state_id"));
        $gstin = intval(JRequest::getVar("gstin"));
        $gst_registration_type = intval(JRequest::getVar("gst_registration_type"));
        $contact_no = JRequest::getVar("contact_no");
        $other_contact_numbers = JRequest::getVar("other_contact_numbers");
        $opening_balance = floatval(JRequest::getVar("opening_balance"));
        $comment = JRequest::getVar("comment");
        
        $query = "select count(transporter_name) from `#__transporters` where transporter_name='" . $transporter_name . "' and id<>" .$transporter_id;
        $db->setQuery($query);
        $count = intval($db->loadResult()); 
       
        if($count > 0)
        {
            echo "Transporter for already exists.";
        }
        else
        {
            $query = "select `opening_balance`, `account_balance` from `#__transporters` where id=" . $transporter_id;
            $db->setQuery($query);
            $previous_balance_details = $db->loadObject();
            
            $difference_in_opening_balance = $opening_balance - floatval($previous_balance_details->opening_balance);
            
            $transporter = new stdClass();
        
            $transporter->id = $transporter_id;
            $transporter->transporter_name = $transporter_name;
            $transporter->transporter_address = $transporter_address;
            $transporter->city_id = $city_id;
            $transporter->state_id = $state_id;
            $transporter->gstin = $gstin;
            $transporter->gst_registration_type = $gst_registration_type;
            $transporter->contact_no = $contact_no;
            $transporter->other_contact_numbers = $other_contact_numbers;
            $transporter->opening_balance = $opening_balance;
            
            if($difference_in_opening_balance != 0)
            {
                $transporter->account_balance = floatval($previous_balance_details->account_balance) + $difference_in_opening_balance;
            }
            
            $transporter->comment = $comment;
            
            if($db->updateObject('#__transporters',$transporter,'id'))
            {
                Functions::log_activity("Transporter " . $transporter_name . " has been updated."); 
                echo "ok";
            }
            else
            {
                echo "Failed to update transporter.";
            }            
        }    
    }
    
    function delete_transporter()
    {
        /*return false;
        $db = JFactory::getDbo();
        
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        
        $count = 0;
        
        $query = "select count(id) from `#__payments` where party_id=" . $transporter_id . " and payment_type=" . TRANSPORTER_PAYMENT;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__payment_items` where party_id=" . $transporter_id . " and payment_type=" . TRANSPORTER_PAYMENT; 
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_orders` where supplier_id=" . $transporter_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_invoice` where supplier_id=" . $transporter_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__purchase_returns` where supplier_id=" . $transporter_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            echo "Unable to delete supplier. It has dependencies.";
            exit;
        }
        
        $query = "select `supplier_name` from `#__suppliers` where id=" .$transporter_id;
        $db->setQuery($query);
        $supplier_name = $db->loadResult();
        
        $query = "delete from `#__suppliers` where id=" .$transporter_id;
        $db->setQuery($query);
        if($db->query())
        {
            Functions::log_activity("Transporter " . $transporter_id . " has been deleted."); 
            echo "ok";
        }
        else
        {
            echo "Failed to delete transporter.";
        } */
        
        if(!is_admin())
        {
            return false;
        }
        
        $db = JFactory::getDBO();
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        
        $count = 0;
        
        $query = "select count(id) from `#__purchase_invoice` where transporter_id=" . $transporter_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            return "Unable to delete transporters. It has dependencies.";
        }

        $query = "select transporter_name from `#__transporters` where id=" . $transporter_id;
        $db->setQuery($query);
        $transporter = $db->loadResult();
        
        $query = "delete from `#__transporters` where `id`=" . $transporter_id;
        $db->setQuery($query);
        
        if($db->query())
        {
            Functions::log_activity("Transporter " . $transporter_name . " has been deleted."); 
            echo "ok";
        }
        else
        {
            echo "Failed to delete transporter.";
        } 
    } 
    
    // Customer
    
    function create_customer()
    {
        $db = JFactory::getDbo();
        
        $customer_name = strtoupper(JRequest::getVar("customer_name"));
        $customer_address = JRequest::getVar("customer_address");
        $city_id = intval(JRequest::getVar("city_id"));
        $state_id = intval(JRequest::getVar("state_id"));
        $route_id = intval(JRequest::getVar("route_id"));
        $gstin = intval(JRequest::getVar("gstin"));
        $gst_registration_type = intval(JRequest::getVar("gst_registration_type"));
        $customer_category_id = intval(JRequest::getVar("customer_category_id"));
        $customer_segment_id = intval(JRequest::getVar("customer_segment_id"));
        $contact_no = JRequest::getVar("contact_no");
        $other_contact_numbers = JRequest::getVar("other_contact_numbers");
        $opening_balance = floatval(JRequest::getVar("opening_balance"));
        $comment = JRequest::getVar("comment");
        
        $query = "select count(customer_name) from `#__customers` where customer_name='" . $customer_name. "'";
        $db->setQuery($query);
        $count = intval($db->loadResult()); 
       
        if($count > 0)
        {
            echo "Customer already exists.";
        }
        else
        {
            $customer = new stdClass();
        
            $customer->customer_name = $customer_name;
            $customer->customer_address = $customer_address;
            $customer->city_id = $city_id;
            $customer->state_id = $state_id;
            $customer->route_id = $route_id;
            $customer->gstin = $gstin;
            $customer->gst_registration_type = $gst_registration_type;
            $customer->contact_no = $contact_no;
            $customer->other_contact_numbers = $other_contact_numbers;
            $customer->opening_balance = $opening_balance;
            $customer->account_balance = $opening_balance;
            $customer->customer_category_id = $customer_category_id;
            $customer->customer_segment_id = $customer_segment_id;
            $customer->comment = $comment;
            $customer->account_status = AC_ACTIVE;
            
            if($db->insertObject('#__customers',$customer,''))
            {
                Functions::log_activity("Customer " . $customer_name . " has been added.");
                echo "ok"; 
            }
            else
            {
                echo "Failed to save customer.";
            }            
        }
    }
    
    function customer_details()
    {
        $db = JFactory::getDbo();
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        
        $query = "select c.*,st.name state_name,st.gst_state_code from `#__customers` c left join `#__states` st on c.state_id=st.id where c.id=" .$customer_id;
        $db->setQuery($query);
        $customer = $db->loadObject();
        
        echo json_encode($customer);
    }
    
    function update_customer()
    {
        $db = JFactory::getDbo();
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        $customer_name = strtoupper(JRequest::getVar("customer_name"));
        $customer_address = JRequest::getVar("customer_address");
        $city_id = intval(JRequest::getVar("city_id"));
        $state_id = intval(JRequest::getVar("state_id"));
        $route_id = intval(JRequest::getVar("route_id"));
        $gstin = intval(JRequest::getVar("gstin"));
        $gst_registration_type = intval(JRequest::getVar("gst_registration_type"));
        $customer_category_id = intval(JRequest::getVar("customer_category_id"));
        $customer_segment_id = intval(JRequest::getVar("customer_segment_id"));
        $contact_no = JRequest::getVar("contact_no");
        $other_contact_numbers = JRequest::getVar("other_contact_numbers");
        $opening_balance = floatval(JRequest::getVar("opening_balance"));
        $comment = JRequest::getVar("comment");
        
        $query = "select count(customer_name) from `#__customers` where customer_name='" . $customer_name . "' and id<>" .$customer_id;
        $db->setQuery($query);
        $count = intval($db->loadResult()); 
       
        if($count > 0)
        {
            echo "Customer already exists.";
        }
        else
        {
            $query = "select `opening_balance`, `account_balance` from `#__customers` where id=" . $customer_id;
            $db->setQuery($query);
            $previous_balance_details = $db->loadObject();
            
            $difference_in_opening_balance = $opening_balance - floatval($previous_balance_details->opening_balance);
            
            $customer = new stdClass();
        
            $customer->id = $customer_id;
            $customer->customer_name = $customer_name;
            $customer->customer_address = $customer_address;
            $customer->city_id = $city_id;
            $customer->state_id = $state_id;
            $customer->route_id = $route_id;
            $customer->gstin = $gstin;
            $customer->gst_registration_type = $gst_registration_type;
            $customer->contact_no = $contact_no;
            $customer->other_contact_numbers = $other_contact_numbers;
            $customer->opening_balance = $opening_balance;
            
            if($difference_in_opening_balance != 0)
            {
                $customer->account_balance = floatval($previous_balance_details->account_balance) + $difference_in_opening_balance;
            }
            
            $customer->customer_category_id = $customer_category_id;
            $customer->customer_segment_id = $customer_segment_id;
            $customer->comment = $comment;
            
            if($db->updateObject('#__customers',$customer,'id'))
            {
                Functions::log_activity("Customer " . $customer_name . " has been updated.");
                echo "ok"; 
            }
            else
            {
                echo "Failed to update customer.";
            }            
        }
    }
    
    function change_customer_account_status()
    {
        $db = JFactory::getDbo();
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        $status = intval(JRequest::getVar("status"));
        
        $query = "update `#__customers` set `account_status`=" . $status . " where id=" . $customer_id;
        $db->setQuery($query);
        if($db->query())
        {
            $query = "select `customer_name` from `#__customers` where id=" . $customer_id;
            $db->setquery($query);
            $customer_name = $db->loadResult();
            
            Functions::log_activity("Customer " . $customer_name . "'s account has been " . ($status == AC_ACTIVE ? "activated" : "deactivated") . ".");
            echo "ok";
        }
    }
    
    function update_customers_category()
    {
        // set customer category of multiple customers
        $db = JFactory::getDbo();
        
        $customer_category_id = intval(JRequest::getVar("customer_category_id"));
        $customer_ids = JRequest::getVar("customer_ids");
        
        foreach($customer_ids as $customer_id)
        {
            $query = "update `#__customers` set `customer_category_id`=" . $customer_category_id . " where id=" . $customer_id;
            $db->setQuery($query);
            $db->query();
        }
        
        Functions::log_activity("Customer category bulk change.");
        echo "ok";
    }
    
    function send_sms_to_customers()
    {
        // send custom message to customers of mentioned customer category
        $db = JFactory::getDbo();
        
        $customer_category_id = intval(JRequest::getVar("customerCategoryID"));
        $message = JRequest::getVar("message");
        
        $query = "select cu.contact_no from `#__customers` cu where cu.customer_category_id=" . $customer_category_id . " and cu.account_status=" . AC_ACTIVE;
        $db->setQuery($query);
        $customers = $db->loadObjectList();
                                           
        $org_name = "AMIT TRADING COMPANY";
        $sent_sms_count = 0;
        $sms = $message . "\n" . $org_name;
        
        if(count($customers) > 0)
        {
            foreach($customers as $customer)
            {
                $mobile_no = $customer->contact_no;
                if(strlen($mobile_no) == 10)
                {
                    $query = "select `value_numeric` from `#__settings` where `key`='sms_balance'";
                    $db->setQuery($query);
                    $sms_balance = intval($db->loadResult());
                    
                    if($sms_balance > 0)
                    {
                        Functions::send_sms($mobile_no,$sms);
                        $sent_sms_count++;
                    }
                }
            }
        }
        
        if($sent_sms_count > 0)
        { Functions::log_activity("Bulk SMS (total " . $sent_sms_count . ") has been sent to customers."); }

        echo "ok";
    }
    
    function save_collection_remarks()
    {
        // Function called from collection report($x) and customers($customer_id) view
        $db = JFactory::getDbo();
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        $collection_remarks = ucfirst(JRequest::getVar("collection_remarks"));
        $x = intval(JRequest::getVar("x"));
        
        $query = "update `#__customers` set collection_remarks='" . $collection_remarks . "' where id=" . $customer_id;
        $db->setQuery($query);
        if($db->query())
        {
            //$data = "<span id='text_mode" . ($x > 0 ? $x : $customer_id) . "'><a href='#' onclick='edit_collection_remarks(" . ($x > 0 ? $x : $customer_id) . "); return false;'><img src='custom/graphics/icons/blank.gif' class='edit' title='Edit'></a>&nbsp;&nbsp;" . substr($collection_remarks, 0, 20) . "</span>";
//            $data .= "<span id='edit_mode" . ($x > 0 ? $x : $customer_id) . "' class='edit_textboxes' style='display:none;'><input type='text' id='collection_remarks" . ($x > 0 ? $x : $customer_id) . "' value='" . $collection_remarks . "' style='width:90px;'><br /><a href='#' class='link1' onclick='save_collection_remarks(" . $customer_id . ", " . $x . "); return false;'><img src='custom/graphics/icons/16x16/tick.png' title='Save'></a><a href='#' class='link2' onclick='cancel_collection_remarks_edit(" . ($x > 0 ? $x : $customer_id) . "); return false;'><img src='custom/graphics/icons/cancel.png' title='Cancel'></a></span>";
//            
            $response = array("success"=>true, "data"=>$data);
        }
        else
        {
            $response = array("success"=>false, "data"=>"");
        }
        
        echo json_encode($response);
    }
    
    function delete_customer()
    {
        return false;
        $db = JFactory::getDbo();
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        
        $count = 0;
        
        $query = "select count(id) from `#__payments` where party_id=" . $customer_id . " and payment_type=" . CUSTOMER_PAYMENT;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__payment_items` where party_id=" . $customer_id . " and payment_type=" . CUSTOMER_PAYMENT; 
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_orders` where customer_id=" . $customer_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_invoice` where customer_id=" . $customer_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__sales_returns` where customer_id=" . $customer_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            echo "Unable to delete customer. It has dependencies.";
            exit;
        }
        
        $query = "select `customer_name` from `#__customers` where id=" .$customer_id;
        $db->setQuery($query);
        $customer_name = $db->loadResult();
        
        $query = "delete from `#__customers` where id=" .$customer_id;
        $db->setQuery($query);
        if($db->query())
        {
            Functions::log_activity("Customer " . $customer_name . " has been deleted."); 
            echo "ok";
        }
        else
        {
            echo "Failed to delete customer.";
        }
    }
    
    // Bank
    
    function save_bank()
    {
        $db = JFactory::getDBO();
        
        $bank_name = addslashes(JRequest::getVar('bank_name'));
        
        $query = "select count(*) from `#__banks` where bank_name='" . $bank_name . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Bank already exists.";
        }
        else
        {        
            $query = "insert into `#__banks` (`bank_name`) values('". $bank_name . "')";
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("New Bank " . $bank_name .  " has been added.");
            echo "";
        }    
    }
    
    function bank_details()
    {
        $db = JFactory::getDbO();
        
        $bank_id = intval(JRequest::getVar("bank_id"));

        $query = "select * from #__banks where id=". $bank_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());
    }
    
    function update_bank()
    {
        $db = JFactory::getDbo();
        
        $bank_id = intval(JRequest::getVar("bank_id"));
        $bank_name = addslashes(JRequest::getVar('bank_name'));
        
        $query = "select count(*) from `#__banks` where bank_name='" . $bank_name . "' and id<>" . $bank_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Bank already exists.";
        }
        else
        {        
            $query = "update `#__banks` set `bank_name`='". $bank_name ."' where id=" .$bank_id;
            $db->setQuery($query);
            $db->query();
            
            Functions::log_activity("Bank " . $bank_name .  " has been updated.");
            echo "";
        }
    }
    
    function delete_bank()
    {
        if(!is_admin())
        {
            return false;
        }
        
        $db = JFactory::getDBO();
        $bank_id = intval(JRequest::getVar('bank_id'));
        
        $count = 0;
        
        $query = "select count(id) from `#__payments` where bank_id=" . $bank_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        $query = "select count(id) from `#__hr_salary_payment_items` where instrument_bank=" . $bank_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            return "Unable to delete bank. It has dependencies.";
        }

        $query = "select `bank_name` from `#__banks` where `id`=" . $bank_id;
        $db->setQuery($query);
        $bank_name = $db->loadResult();
        
        $query = "delete from `#__banks` where `id`=" . $bank_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Bank " . $bank_name . " has been deleted.");
        return "Bank deleted successfully.";
    }
    
    // Customer category
    
    function save_customer_category()
    {
        $db = JFactory::getDBO();
        
        $customer_category = ucwords(addslashes(JRequest::getVar('customer_category')));
        
        $query = "select count(*) from `#__customer_categories` where customer_category='" . $customer_category . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Customer category already exists.";
        }
        else
        {                      
            $query = "insert into `#__customer_categories`(`customer_category`) values('" . $customer_category . "')";
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Customer category " . $customer_category .  " has been added.");
        }
    }   
    
    function customer_category_details()
    {
        $db = JFactory::getDbO();
        
        $customer_category_id = intval(JRequest::getVar("customer_category_id"));

        $query = "select * from `#__customer_categories` where id=" . $customer_category_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());   
    }
    
    function update_customer_category()
    {
        $db = JFactory::getDBO();
        
        $customer_category = ucwords(addslashes(JRequest::getVar('customer_category')));
        $customer_category_id = intval(JRequest::getVar("customer_category_id"));
        
        $query = "select count(*) from `#__customer_categories` where customer_category='" . $customer_category . "' and id<>" . $customer_category_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Customer category already exists.";
        }
        else
        {                      
            $query = "update `#__customer_categories` set `customer_category`='" . $customer_category . "' where `id`=" . $customer_category_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Customer category " . $customer_category .  " has been updated.");
        }
    }
    
    function delete_customer_category()
    {   
        if(!is_admin())
        {
            return false;
        }
        
        $db = JFactory::getDBO();
        $customer_category_id = intval(JRequest::getVar("customer_category_id"));
        
        $count = 0;
        
        $query = "select count(id) from `#__customers` where customer_category_id=" . $customer_category_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            return "Unable to delete customer category. It has dependencies.";
        }

        $query = "select customer_category from `#__customer_categories` where id=" . $customer_category_id;
        $db->setQuery($query);
        $customer_category = $db->loadResult();
        
        $query = "delete from `#__customer_categories` where `id`=" . $customer_category_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Customer category " . $customer_category . " has been deleted."); 
        return "Customer category deleted successfully.";
    }
    
    // Transporter
    
   /* function save_transporter()
    {
        $db = JFactory::getDBO();
        
        $transporter = ucwords(addslashes(JRequest::getVar('transporter')));
        
        $query = "select count(*) from `#__transporters` where transporter='" . $transporter . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Transporter already exists.";
        }
        else
        {                      
            $query = "insert into `#__transporters`(`transporter`) values('" . $transporter . "')";
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Transporter " . $transporter .  " has been added.");
        }
    }   
    
    function transporter_details()
    {
        $db = JFactory::getDbO();
        
        $transporter_id = intval(JRequest::getVar("transporter_id"));

        $query = "select * from `#__transporters` where id=" . $transporter_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());   
    }
    
    function update_transporter()
    {
        $db = JFactory::getDBO();
        
        $transporter = ucwords(addslashes(JRequest::getVar('transporter')));
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        
        $query = "select count(*) from `#__transporters` where transporter='" . $transporter . "' and id<>" . $transporter_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Transporter already exists.";
        }
        else
        {                      
            $query = "update `#__transporters` set `transporter`='" . $transporter . "' where `id`=" . $transporter_id;
            $db->setQuery($query);

            if($db->query())
            {
                Functions::log_activity("Transporter " . $transporter .  " has been updated.");
                echo "ok"; 
            }
            else
            {
                echo "Failed to update transporter.";
            }
        }
    }
    
    function delete_transporter()
    {   
        if(!is_admin())
        {
            return false;
        }
        
        $db = JFactory::getDBO();
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        
        $count = 0;
        
        $query = "select count(id) from `#__purchase_invoice` where transporter_id=" . $transporter_id;
        $db->setQuery($query);
        $count += intval($db->loadResult());
        
        if($count > 0)
        {
            return "Unable to delete transporters. It has dependencies.";
        }

        $query = "select transporter from `#__transporters` where id=" . $transporter_id;
        $db->setQuery($query);
        $transporter = $db->loadResult();
        
        $query = "delete from `#__transporters` where `id`=" . $transporter_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Transporter " . $transporter . " has been deleted."); 
        return "Transporter deleted successfully.";
    }  */
    
    // Bank account
    
    function save_bank_account()
    {
        $db = JFactory::getDBO();
        
        $account_name = ucwords(addslashes(JRequest::getVar('account_name')));
        $bank_name = ucwords(addslashes(JRequest::getVar('bank_name')));
        $branch = JRequest::getVar('branch');
        $account_no = JRequest::getVar('account_no');
        $account_type = intval(JRequest::getVar('account_type'));
        $ifsc_code = JRequest::getVar('ifsc_code');
        $opening_balance = floatval(JRequest::getVar('opening_balance'));
        
        $query = "select count(*) from `#__bank_accounts` where account_name='" . $account_name . "' and bank_name='" . $bank_name . "' and account_no='" . $account_no . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Bank account already exists.";
        }
        else
        {                      
            $query = "insert into `#__bank_accounts`(`account_name`, `bank_name`, `branch`, `account_no`, `account_type`, `ifsc_code`, `opening_balance`, `balance`, `account_status`) values('" . $account_name . "', '" . $bank_name . "', '" . $branch . "', '" . $account_no . "', " . $account_type . ", '" . $ifsc_code . "', " . $opening_balance . ", " . $opening_balance . ", " . AC_ACTIVE . ")";
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Bank Account " . $account_name .  " in " . $bank_name . "  has been added.");
        }
    }   
    
    function bank_account_details()
    {
        $db = JFactory::getDbO();
        
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));

        $query = "select * from `#__bank_accounts` where id=" . $bank_account_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());   
    }
    
    function update_bank_account()
    {
        $db = JFactory::getDBO();
        
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        
        $account_name = ucwords(addslashes(JRequest::getVar('account_name')));
        $bank_name = ucwords(addslashes(JRequest::getVar('bank_name')));
        $branch = JRequest::getVar('branch');
        $account_no = JRequest::getVar('account_no');
        $account_type = intval(JRequest::getVar('account_type'));
        $ifsc_code = JRequest::getVar('ifsc_code');
        $opening_balance = floatval(JRequest::getVar('opening_balance'));
        
        $query = "select count(*) from `#__bank_accounts` where account_name='" . $account_name . "' and bank_name='" . $bank_name . "' and account_no='" . $account_no . "' and id<>" . $bank_account_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Bank account already exists.";
        }
        else
        {                      
            $query = "update `#__bank_accounts` set `account_name`='" . $account_name . "', `bank_name`='" . $bank_name . "', `branch`='" . $branch . "', `account_no`='" . $account_no . "', `account_type`=" . $account_type . ", `ifsc_code`='" . $ifsc_code . "', `opening_balance`=" . $opening_balance . " where `id`=" . $bank_account_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Bank Account " . $account_name .  " in " . $bank_name . "  has been updated.");
        }
    }
    
    function change_bank_account_status()
    {
        $db = JFactory::getDbO();
        
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $account_status = intval(JRequest::getVar("s"));
        
        $query = "select concat(account_name, '(', account_no, ')') account_name, bank_name from `#__bank_accounts` where id=" . $bank_account_id;
        $db->setQuery($query);
        $account = $db->loadObject();

        $query = "update `#__bank_accounts` set `account_status`=" . $account_status . " where id=" . $bank_account_id;
        $db->setQuery($query);
        $db->query();

        Functions::log_activity("Bank Account " . $account->account_name .  " in " . $account->bank_name . "  has been " . ($account_status == AC_ACTIVE ? "re-opened" : "closed") . ".");
        echo "ok";
    }
    
    // Notepad
    
    function save_note()
    {
        $db = JFactory::getDBO();
        
        $note = ucfirst(addslashes(JRequest::getVar('note')));
        $date_of_note = date("Y-m-d", strtotime(JRequest::getVar("date_of_note")));
        $note_type = intval(JRequest::getVar("note_type"));
        
        $query = "insert into `#__notes`(`date_of_note`, `note`, `note_type`) values('" . $date_of_note . "', '" . $note . "', " . $note_type . ")";
        $db->setQuery($query);
        $db->query();
        
        $note_id = intval($db->insertid());

        Functions::log_activity("Note " . substr($note, 0, 20) .  "... dated " . $date_of_note . " has been added.", "", $note_id);
    }
    
    function delete_note()
    {   
        if(!is_admin())
        {
            return false;
        }
        
        $db = JFactory::getDBO();
        $note_id = intval(JRequest::getVar("note_id"));
        
        $query = "select note from `#__notes` where id=" . $note_id;
        $db->setQuery($query);
        $note = $db->loadResult();
        
        $query = "update `#__notes` set deleted=" . YES . " where `id`=" . $note_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Note " . substr($note, 0, 20) . "... has been marked as deleted.", "", $note_id); 
        return "Note marked as deleted successfully.";
    }
    
    function merge_items()
    {
        // stock of items to merge is calculated for from location and added to to-location for the $merge_to_item_id
        // stock of items to merge is set to 0 for from location
        
        $db = JFactory::getDBO();
        $items_to_merge = base64_decode(JRequest::getVar("items"));
        $items_to_merge = explode(",",$items_to_merge);
        
        $merge_to_item_id = intval(JRequest::getVar("merge_to_item"));
        $location_from = intval(JRequest::getVar("location_from"));
        $location_to = intval(JRequest::getVar("location_to"));
        
        $item_stock = 0;
        foreach($items_to_merge as $item_id)
        {
            $query = "select stock from `#__inventory_items` where item_id=" . intval($item_id) . " and location_id=" . $location_from . "";
            $db->setQuery($query);
            $item_stock += floatval($db->loadResult());
        }
        
        $query = "select count(id) from `#__inventory_items` where item_id=" . $merge_to_item_id . " and location_id=" . $location_to;
        $db->setQuery($query);
        $check_availability = intval($db->loadResult());
        
        if($check_availability == 0)
        {
            $query = "insert into `#__inventory_items`(`item_id`,`location_id`,`opening_stock`,`stock`) values(" . $merge_to_item_id . "," . $location_to . "," . $item_stock . "," . $item_stock . ")";
            $db->setQuery($query);
            $db->query();
        }
        else
        {
            $query = "update `#__inventory_items` set stock=stock + " . $item_stock . " where item_id=" . $merge_to_item_id . " and location_id=" . $location_to;
            $db->setQuery($query);
            $db->query();
        }
        
        foreach($items_to_merge as $item_id)
        {
            $query = "update `#__inventory_items` set stock=0 where item_id=" . intval($item_id) . " and location_id=" . $location_from . "";
            $db->setQuery($query);
            $db->query();
        }
        
        echo "ok";
    }
    
    // Customer segment
    
    function save_customer_segment()
    {
        $db = JFactory::getDBO();
        
        $customer_segment = ucfirst(JRequest::getVar('customer_segment'));
        
        $query = "select count(*) from `#__customer_segments` where customer_segment='" . $customer_segment . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Customer Segment already exists.";
        }
        else
        {                      
            $query = "insert into `#__customer_segments`(`customer_segment`) values('" . $customer_segment . "')";
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Customer Segment " . $customer_segment .  " has been added.");
        }
    }   
    
    function customer_segment_details()
    {
        $db = JFactory::getDbO();
        
        $customer_segment_id = intval(JRequest::getVar("customer_segment_id"));

        $query = "select * from `#__customer_segments` where id=" . $customer_segment_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());   
    }
    
    function update_customer_segment()
    {
        $db = JFactory::getDBO();
        
        $customer_segment = ucfirst(JRequest::getVar('customer_segment'));
        $customer_segment_id = intval(JRequest::getVar('customer_segment_id'));
        
        $query = "select count(*) from `#__customer_segments` where customer_segment='" . $customer_segment . "' and id<>" . $customer_segment_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Customer Segment already exists.";
        }
        else
        {                      
            $query = "update `#__customer_segments` set `customer_segment`='" . $customer_segment . "' where `id`=" . $customer_segment_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Customer Segment " . $customer_segment .  " has been updated.");
        }
    }
    
    function delete_customer_segment()
    {   
        $db = JFactory::getDBO();
        $customer_segment_id = intval(JRequest::getVar('customer_segment_id'));
        
        $query = "select count(id) from `#__customers` where customer_segment_id=" . $customer_segment_id;
        $db->setQuery($query);
        $count = intval($db->loadResult()); 
        
        if($count > 0)
        {
            return "Customer Segment cannot be deleted. It has dependencies.";
        }

        $query = "select customer_segment from `#__customer_segments` where id=" . $customer_segment_id;
        $db->setQuery($query);
        $customer_segment = $db->loadResult();
        
        $query = "delete from `#__customer_segments` where `id`=" . $customer_segment_id;
        $db->setQuery($query);
        $db->query();
        
        Functions::log_activity("Customer Segment " . $customer_segment . " has been deleted."); 
        return "Customer Segment deleted successfully.";
    }
    // end customer segments
    
    function state_details()
    {
        $db = JFactory::getDBO();

        $city_id = intval(JRequest::getVar("city_id"));

        $query = "select s.name state_name,s.id state_id,s.gst_state_code from #__cities c inner join #__states s on c.state_id=s.id where c.id=" . $city_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());  
    }
    
    
    //Units
     function save_unit()
    {
        $db = JFactory::getDBO();
        
        $unit_name = (JRequest::getVar('unit_name'));
        
        $query = "select count(*) from `#__units` where unit='" . $unit_name . "'";
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Unit already exists.";
        }
        else           
        {                      
            $query = "insert into `#__units`(`unit`) values ('" . $unit_name . "')";
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Unit " . $unit_name .  " has been added.");
        }
    }   
 
    
    function update_unit()
    {
        $db = JFactory::getDBO();
        
        $unit_name = (JRequest::getVar('unit_name'));
        $unit_id = intval(JRequest::getVar('unit_id'));
        
        $query = "select count(*) from `#__units` where unit='" . $unit_name . "' and id<>" . $unit_id;
        $db->setQuery($query);
        $count = $db->loadResult();
        
        if($count > 0)
        {
            echo "Unit already exists.";
        }
        else
        {                      
            $query = "update `#__units` set `unit`='" . $unit_name . "' where `id`=" . $unit_id;
            $db->setQuery($query);
            $db->query();

            Functions::log_activity("Unit " . $unit_name .  " has been updated.");
        }
    }
    
    
    
     function unit_details()
    {
        $db = JFactory::getDbO();
        
        $unit_id = intval(JRequest::getVar("unit_id"));

        $query = "select * from `#__units` where id=" . $unit_id;
        $db->setQuery($query);
        echo json_encode($db->loadAssoc());   
    }
    
    function delete_unit()
    {
        $db = JFactory::getDbO();
        $unit_id = intval(JRequest::getVar("unit_id"));
        $query = "delete from `#__units` where `id`=" . $unit_id;
        $db->setQuery($query);
        $db->query();
    }
}
?>