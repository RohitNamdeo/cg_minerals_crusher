<?php
jimport( 'joomla.application.component.view');

class MasterViewPurchase_entry extends JViewLegacy
{
    function display($tpl = null)
    {
        /*
        * view for add/edit of purchase
        * salary is defined as gross salary, no salary structure as other HR s/w
        * Employee is on machine attendance
        * Non-machine attendance does not exists
        */
        
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
                
        $document = JFactory::getDocument();
        $document->setTitle("Purchase  Registration");
        
            $mode = JRequest::getVar("mode");
            $query ="select * from `#__suppliers`";
            $db->setQuery($query);
            $suppliers = $db->loadObjectList();
            $this->suppliers = $suppliers;
            
            $query ="select * from `#__products`";
            $db->setQuery($query);
            $products = $db->loadObjectList();
            $this->products = $products;
            
            
            if($mode == 'e')
            {
                $purchase_id = JRequest::getVar("purchase_id"); 
               $query = "select u.unit,u.id as unit_id,p.product_name,s.id as supplier_id,p.id as product_id,s.supplier_name,pc.date,pc.supplier_challan_no,pc.challan_no,pc.vehicle_no,pc.quantity,pc.rate,pc.gross_amount,pc.gst_percent,pc.gst_amount,pc.payable_amount,pc.total_amount,pc.loading_charges,pc.royalty,pc.waiverage_charges,pc.remarks,pc.creation_date from `jos_purchase` as pc inner join `jos_suppliers` as s inner join `jos_products` as p inner join `jos_units` as u where pc.supplier_id=s.id and pc.unit_id=u.id and pc.product_id=p.id and pc.id=".$purchase_id;
        $db->setQuery($query);
        $purchase_edit = $db->loadObject();
        $this->purchase_edit = $purchase_edit;
        $this->purchase_id = $purchase_id;
        
                
                parent::display("edit");
            }
        else
            {
                parent::display($tpl);
            } 
            
            
             
	}
}
?>
