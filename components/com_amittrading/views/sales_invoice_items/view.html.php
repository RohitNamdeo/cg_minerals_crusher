<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewsales_invoice_items extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Sales Items");
        
        $sales_id = intval(JRequest::getVar('sales_id'));
        //echo $sales_id; exit;
        $this->sales_id = $sales_id;
        
        $query = "select si.*,c.customer_name,t.transporter_name,t1.transporter_name loading_transporter_name,v.vehicle_type vehicle_status,vt.vehicle_type,cu.customer_name,cu.customer_address,cu.contact_no,cu.other_contact_numbers,v.vehicle_number,v.owner_name,v.owner_number,r.royalty_name from #__sales_invoice si inner join #__customers c on si.customer_id=c.id inner join #__vehicles v on si.vehicle_id=v.id left join #__transporters t on si.transporter_id=t.id left join #__transporters t1 on si.loading_transporter_id=t1.id inner join  #__vehicles_type vt on si.loading_vehicle_type=vt.id inner join #__customers cu on si.customer_id=cu.id inner join #__royalty r on si.royalty_id=r.id where si.id=". $sales_id;
        $db->setQuery($query);
        $sales_invoice = $db->loadObject();
      
        $this->sales_invoice = $sales_invoice; 
        
        $query = "select sii.*,p.product_name product_name from  #__sales_invoice si inner join  #__sales_invoice_items sii on si.id=sii.sales_invoice_id inner join #__products p on sii.product_id=p.id where sii.sales_invoice_id=" . $sales_id;
        $db->setQuery($query);
        $product_sales_items = $db->loadObjectList(); 
        $this->product_sales_items = $product_sales_items;  
        
        $query = "select * from `#__suppliers` order by supplier_name";
        $db->setQuery($query);
        $suppliers = $db->loadObjectList("id");
        $this->suppliers = $suppliers;
        
        parent::display($tpl);
    } 
}
?>