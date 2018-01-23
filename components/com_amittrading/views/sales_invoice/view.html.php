<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewSales_invoice extends JViewLegacy
{
    public function display($tpl = null)
    {
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if(!Functions::has_permissions("amittrading", "sales_invoice"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        } 
             
         $db = JFactory::getDbo();
         //$order_id = intval(JRequest::getVar("order_id"));
         $c = JRequest::getVar("c");
         $mode = JRequest::getVar("m");
         
        if($mode == "e")
        { 
            $document = JFactory::getDocument()->setTitle("Edit Sales Invoice"); 
        }
        else
        { 
            $document = JFactory::getDocument()->setTitle("Sales Invoice");
        }
        
        $query = "select `value_numeric` from `#__settings` where `key`='cash_sale_customer_id'";
        $db->setQuery($query);
        $cash_sale_customer_id = intval($db->loadResult());
        $this->cash_sale_customer_id = $cash_sale_customer_id;
        
        // $query = "select customer_name, customer_address, contact_no from `#__customers` where id=" . $cash_sale_customer_id;
         //echo $query;exit;
//        $db->setQuery($query);
//        $this->cash_sale_customer = $db->loadObject();      
         
        $query = "select * from `#__products` order by product_name";        
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $this->products = $products; 
        
        $query = "select * from `#__vehicles_type` ";        
        $db->setQuery($query);
        $vehicles_type = $db->loadObjectList();
        $this->vehicles_type = $vehicles_type;
        
        //$query = "select * from `#__vehicles` order by id";   
        $query = "select v.*,t.transporter_name,t.id transporterid from `#__vehicles` v inner join `#__transporters` t on t.id=v.transporter_id";                                                                                                                                        
        $db->setQuery($query);
        $vehicles = $db->loadObjectList();
        $this->vehicles = $vehicles;
        
        $query = "select * from `#__transporters` order by transporter_name"; 
        $db->setQuery($query);
        $transporters = $db->loadObjectList();
        $this->transporters = $transporters;
        
        $query = "select * from `#__customers` order by customer_name ";        
        $db->setQuery($query);
        $customers = $db->loadObjectList();
        $this->customers = $customers;
        
        $customers_array = "";
        if(count($customers) > 0)
        {
            foreach($customers as $customer)
            {                                                                                                                    
                $customers_array .= ( $customers_array == "" ? "" : ",") . "{ value: \"" . $customer->id . "\", label:\"" . $customer->customer_name . "\", desc: \"" . $customer->customer_name. "\"}";
            }
        }
        $this->customers_array = $customers_array; 
        
        $query = "select * from `#__suppliers` order by supplier_name";        
        $db->setQuery($query);
        $suppliers = $db->loadObjectList();
        $this->suppliers = $suppliers;
        
        $query = "select * from `#__royalty` order by royalty_name";        
        $db->setQuery($query);
        $royalty_list = $db->loadObjectList();
        $this->royalty_list = $royalty_list;
        
        $suppliers_array = "";
        if(is_array($suppliers))
        {
            foreach($suppliers as $supplier)
            {
                $suppliers_array .= ( $suppliers_array == "" ? "" : ",") . "{ value: \"" . $supplier->id . "\", label:\"" . $supplier->supplier_name . "\", desc: \"" . $supplier->supplier_name . "\"}";
            }
        }
        $this->suppliers_array = $suppliers_array; 
        
        if($mode == "e")
        {
            $sales_id = intval(JRequest::getVar("sales_id"));  
            $this->sales_id = $sales_id;
            $this->order_id = intval(JRequest::getVar("order_id")); 
            
           /* $return = JRequest::getVar("r");
            if($return == "")
            {
                $return = base64_encode("index.php?option=com_amittrading&view=sales_invoice_history");
            }
            $this->return = $return;  */
            
            //$query = "select * from `#__sales_invoice_items` where sales_invoice_id=".$sales_id;
           // $query = "select distinct si.order_id order_id,sii.* from `#__sales_invoice_items` sii inner join `#__sales_invoice` si inner join `#__sales_orders` so where si.order_id = so.id and si.id = sii.sales_invoice_id and si.id=".$sales_id." and so.id=".$this->order_id;     
//            $db->setQuery($query);
//            $sales_invoice_items_details = $db->loadObjectList();
//            
//            foreach($sales_invoice_items_details as $sales_invoice_item)
//            {
//                $query = "update `#__sales_orders` set billed_quantity=billed_quantity-".$sales_invoice_item->quantity." where id=".$sales_invoice_item->order_id; 
//                $db->setQuery($query);
//                $db->query();
//            }  
            $query = "select billed_quantity,total_weight from `#__sales_orders` where id=".intval($this->order_id);
            $db->setQuery($query);
            $billed_quant = $db->loadObject();
            
            $query = "select total_weight from `#__sales_invoice` where id=" . $sales_id;
            $db->setQuery($query);
            $total_weight = floatval($db->loadResult());
            if(is_object($billed_quant))
            {
                $this->billed_quantity = floatval($billed_quant->billed_quantity) - $total_weight;
                $this->quantity = floatval($billed_quant->total_weight);
            }
            else
            {
                $this->billed_quantity = 0;
                $this->quantity = 0;
            }
            //$query = "select si.*,c.customer_name,t.transporter_name,vt.vehicle_type,cu.customer_name,cu.customer_address,cu.contact_no,cu.other_contact_numbers,v.vehicle_number,v.owner_name,v.owner_number from #__sales_invoice si inner join #__customers c on si.customer_id=c.id inner join #__vehicles v on si.vehicle_id=v.id left join #__transporters t on si.transporter_id=t.id inner join #__vehicles_type vt on si.loading_vehicle_type=vt.id inner join #__customers cu on si.customer_id=cu.id where si.id=". $sales_id;
            $query= "select si.*,c.customer_name,t.transporter_name,vt.vehicle_type,cu.customer_name,cu.customer_address,cu.contact_no,cu.other_contact_numbers,v.vehicle_number,v.owner_number from #__sales_invoice si inner join #__customers c on si.customer_id=c.id inner join #__vehicles v on si.vehicle_id=v.id left join #__transporters t on si.transporter_id=t.id inner join #__vehicles_type vt on si.loading_vehicle_type=vt.id inner join #__customers cu on si.customer_id=cu.id where si.id=". $sales_id;
            $db->setQuery($query);
            $sales = $db->loadObject();
            //print_r($sales);exit;
            
            $this->sales = $sales;
            
            $query = "select * from `#__suppliers`";
            $db->setQuery($query);
            $suppliers = $db->loadObjectList();
            $this->suppliers = $suppliers;
            
            $query = "select sii.*,p.product_name product_name from #__sales_invoice si inner join  #__sales_invoice_items sii on si.id=sii.sales_invoice_id inner join #__products p on sii.product_id=p.id where sii.sales_invoice_id=" . $sales_id . " and sii.item_type=". PRODUCT;
            $db->setQuery($query);
            $sales_product_items = $db->loadObjectList();
             
            $this->sales_product_items = $sales_product_items;
            
            $query = "select sii.*,p.product_name product_name from #__sales_invoice si inner join  #__sales_invoice_items sii on si.id=sii.sales_invoice_id inner join #__products p on sii.product_id=p.id where sii.sales_invoice_id=" . $sales_id . " and sii.item_type=".MIXING;
            $db->setQuery($query);
            $sales_mixing_items = $db->loadObjectList(); 
            
            $this->sales_mixing_items = $sales_mixing_items;
            parent::display("edit");
        }
        else
        {
            $this->show_default_row = YES;
            $item_ids = JRequest::getVar("item_ids");
            $this->customer_id = intval(JRequest::getVar("customer_id"));
            $this->order_id = intval(JRequest::getVar("order_id"));
                        
            $query = "select so.*,c.customer_name,c.customer_address,c.contact_no,c.other_contact_numbers,r.royalty_name from `#__customers` c inner join `#__sales_orders` so inner join `#__royalty` r on so.royalty_id=r.id  where c.id = so.customer_id and c.id=".$this->customer_id." and so.id=".$this->order_id;
            //$query = "select so.*,soi.*,c.customer_name,c.customer_address,c.contact_no,c.other_contact_no from `#__customers` c inner join `#__sales_orders` so inner join `#__sales_order_items` soi where soi.customer_id=c.id and where c.id = so.customer_id and c.id=".$this->customer_id." and so.id=".$this->order_id;
            $db->setQuery($query);
            $order_details = $db->loadObject();
            $this->order_details = $order_details;
            
            $sales_items = array();
            
            $query = "select billed_quantity,total_weight from `#__sales_orders` where id=".intval($this->order_id);
            $db->setQuery($query);
            $billed_quant = $db->loadObject();
            if(is_object($billed_quant))
            {
                
                $this->billed_quantity = floatval($billed_quant->billed_quantity);
                $this->quantity = floatval($billed_quant->total_weight);
            }
            else
            {
                $this->billed_quantity = 0;
                $this->quantity = 0;
            }
            
            if(count($item_ids) > 0)
            {     
                $return = "index.php?option=com_amittrading&view=pending_sales_orders";
                $this->show_default_row = NO;
                foreach($item_ids as $key => $item_id)
                {
                    $query = "select p.product_name,soi.* from `#__sales_order_items` soi inner join `#__products` p on p.id=soi.product_id where soi.id=" . $item_id;
                    $db->setQuery($query);
                    $sales_items[$key] = $db->loadObject();
                }
            }
            else
            { 
                $return = "index.php?option=com_amittrading&view=sales_invoice"; 
            }
            
            $query = "select * from `#__products` order by product_name";
            $db->setQuery($query);
            $items = $db->loadObjectList();
           
            $this->items = $items;
            $this->sales_items = $sales_items;
            //print_r($this->sales_items);exit;
            $this->return = $return;
            
            parent::display($tpl);
        }
        
    } 
}
?>