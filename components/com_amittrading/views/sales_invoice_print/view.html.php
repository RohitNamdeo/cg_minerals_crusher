<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewSales_invoice_print extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * view to print sales invoice
        * 2 separate defaults for bill & quotation
        * godown slip is prepared for both
        * G (Ganj) location is ommitted in godown slip
        * this slip shows the item quantity location-wise
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        $document = JFactory::getDocument()->setTitle("Sales Invoice Print");
        
        $sales_id = intval(JRequest::getVar("invoice_id")); 
        
        //$query = "select s.*, cu.customer_name, cu.customer_address, cu.contact_no, cu.account_balance, cc.customer_category, c.city from `#__sales_invoice` s inner join `#__customers` cu on s.customer_id=cu.id inner join `#__customer_categories` cc on cu.customer_category_id=cc.id inner join `#__cities` c on cu.city_id=c.id where s.id=" . $sales_id;
        $query = "select s.*, cu.customer_name, cu.customer_address, cu.contact_no, cu.account_balance, c.city,v.vehicle_number from `#__sales_invoice` s inner join `#__customers` cu on s.customer_id=cu.id inner join `#__cities` c on cu.city_id=c.id inner join `#__vehicles` v on s.vehicle_id=v.id where s.id=" . $sales_id;
        $db->setQuery($query);
        $sales = $db->loadObject();
        $this->sales = $sales;
        
        $query = "select `value_numeric` from `#__settings` where `key`='cash_sale_customer_id'";
        $db->setQuery($query);
        $cash_sale_customer_id = intval($db->loadResult());
        $this->cash_sale_customer_id = $cash_sale_customer_id;
        
        $query = "select * from `#__royalty` order by royalty_name";
        $db->setQuery($query);
        $royalty_list = $db->loadObjectList("id");
        $this->royalty_list = $royalty_list;
        
        $query = "select * from `#__transporters` order by transporter_name";
        $db->setQuery($query);
        $transporter_list = $db->loadObjectList("id");
        $this->transporter_list = $transporter_list;
        
        if($sales->customer_id == $cash_sale_customer_id)
        { $document = JFactory::getDocument()->setTitle("Cash Invoice Print"); }
        
        $query = "select si.*,p.product_name,p.hsn_code from `#__sales_invoice_items` si inner join `#__products` p on si.product_id=p.id where si.item_type=" . PRODUCT . " and si.sales_invoice_id=" . $sales_id;
        $db->setQuery($query);
        $sales_items = $db->loadObjectList();
        
       // if($sales->bill_type == BILL)
//        {
//            foreach($sales_items as $item)
//            {
//                $total_qty += floatval($item->quantity);
//                if($item->item_type == PRODUCT)
//                {
//                    $rate = $item->product_rate;
//                }
//            }
//            
//            foreach($sales_items as $key => $item)
//            {
//                if($item->item_type == MIXING)
//                {
//                    unset($sales_items[$key]);
//                }
//            } 
//            
//            foreach($sales_items as $key => $item)
//            {
//                $item->quantity = $total_qty;
//                $item->product_rate = $rate;
//            }
//        } 
                
        $this->sales_items = $sales_items;
        
        $query = "select `value_string` from `#__settings` where `key`='mobile_no'";
        $db->setQuery($query);
        $mobile_no = $db->loadResult();
        
        $mobile_no = explode(",", $mobile_no);
        $this->mobile_no = implode("<br />", $mobile_no);
        
        $day_munshi_mobile_no = Functions::get_setting("day_munshi_mobile_no");
        $night_munshi_mobile_no = Functions::get_setting("night_munshi_mobile_no");
        
        $this->day_munshi_mobile_no = $day_munshi_mobile_no;
        $this->night_munshi_mobile_no = $night_munshi_mobile_no;
        
        $query = "select `value_string` from `#__settings` where `key`='gst_no'";
        $db->setQuery($query);
        $this->gst_no = $db->loadResult();
        
        $query = "select `value_string` from `#__settings` where `key`='invoice_footer'";
        $db->setQuery($query);
        $this->invoice_footer = $db->loadResult();
        
        if($sales->bill_type == BILL)
        {
            parent::display("bill");
        }
        else
        {
            parent::display("challan");
        }
    } 
}
?>