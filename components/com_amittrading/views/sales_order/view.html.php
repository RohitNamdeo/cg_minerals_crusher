<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewSales_order extends JViewLegacy
{
    public function display($tpl = null)
    {
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if(!Functions::has_permissions("amittrading", "sales_order"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        } 
             
         $db = JFactory::getDbo();
         $c = JRequest::getVar("c");
         $mode = JRequest::getVar("m");
         
        if($mode == "e")
        { 
            $document = JFactory::getDocument()->setTitle("Edit Sales Order"); 
        }
        else
        { 
            $document = JFactory::getDocument()->setTitle("Sales Order");
        }
         
        $query = "select * from `#__products` order by product_name";        
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $this->products = $products; 
        
       // $query = "select * from `#__vehicles_type` ";        
//        $db->setQuery($query);
//        $vehicles_type = $db->loadObjectList();
//        $this->vehicles_type = $vehicles_type;
        
        //$query = "select * from `#__vehicles` order by id";        
//        $db->setQuery($query);
//        $vehicles = $db->loadObjectList();
//        $this->vehicles = $vehicles;
//        
//        $query = "select * from `#__transporters` order by transporter_name";        
//        $db->setQuery($query);
//        $transporters = $db->loadObjectList();
//        $this->transporters = $transporters;
//        
        $query = "select * from `#__customers` order by customer_name ";        
        $db->setQuery($query);
        $customers = $db->loadObjectList();
        $this->customers = $customers;
        
        $query = "select * from `#__royalty` order by royalty_name ";        
        $db->setQuery($query);
        $royalties = $db->loadObjectList();
        $this->royalties = $royalties;
        
        $customers_array = "";
        if(count($customers) > 0)
        {
            foreach($customers as $customer)
            {                                                                                                                    
                $customers_array .= ( $customers_array == "" ? "" : ",") . "{ value: \"" . $customer->id . "\", label:\"" . $customer->customer_name . "\", desc: \"" . $customer->customer_name. "\"}";
            }
        }
        $this->customers_array = $customers_array; 
        
        //$query = "select * from `#__suppliers` order by supplier_name";        
//        $db->setQuery($query);
//        $suppliers = $db->loadObjectList();
//        $this->suppliers = $suppliers;
        
        //$query = "select * from `#__royalty` order by royalty_name";        
//        $db->setQuery($query);
//        $royalty_list = $db->loadObjectList();
//        $this->royalty_list = $royalty_list;
        
        $suppliers_array = "";
       // if(is_array($suppliers))
//        {
//            foreach($suppliers as $supplier)
//            {
//                $suppliers_array .= ( $suppliers_array == "" ? "" : ",") . "{ value: \"" . $supplier->id . "\", label:\"" . $supplier->supplier_name . "\", desc: \"" . $supplier->supplier_name . "\"}";
//            }
//        }
//        $this->suppliers_array = $suppliers_array; 
        
        if($mode == "e")
        {
            $sales_id = intval(JRequest::getVar("sales_id"));  
            $this->sales_id = $sales_id;
            
           /* $return = JRequest::getVar("r");
            if($return == "")
            {
                $return = base64_encode("index.php?option=com_amittrading&view=sales_invoice_history");
            }
            $this->return = $return;  */
            
            $query = "select so.*,c.customer_name,cu.customer_name,cu.customer_address,cu.contact_no,cu.other_contact_numbers from `#__sales_orders` so inner join `#__customers` c on so.customer_id=c.id inner join `#__customers` cu on so.customer_id=cu.id where so.id=". $sales_id;
            $db->setQuery($query);
            $sales = $db->loadObject();
            
            $this->sales_order = $sales;
            
            $query = "select soi.*,p.product_name from #__sales_orders so inner join  #__sales_order_items soi on so.id=soi.sales_order_id inner join #__products p on soi.product_id=p.id where soi.sales_order_id=" . $sales_id . " and soi.item_type=". PRODUCT;
            //echo $query;
            $db->setQuery($query);
            $sales_product_items = $db->loadObjectList();
             
            $this->sales_product_items = $sales_product_items;
            
            $query = "select soi.*,p.product_name from #__sales_orders so inner join  #__sales_order_items soi on so.id=soi.sales_order_id inner join #__products p on soi.product_id=p.id where soi.sales_order_id=" . $sales_id . " and soi.item_type=".MIXING;
            $db->setQuery($query);
            $sales_mixing_items = $db->loadObjectList(); 
            
            $this->sales_mixing_items = $sales_mixing_items;
            
            parent::display("edit");
        }
        else
        {
            parent::display($tpl);          
        }
        
    } 
}
?>