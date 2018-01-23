<?php
jimport( 'joomla.application.component.view');

class AmittradingViewPurchase_entry extends JViewLegacy
{
    function display($tpl = null)
    {
        
        $db = JFactory::getDBO();
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
                
        $document = JFactory::getDocument();
        $document->setTitle("Purchase  Registration");
        
        $mode = JRequest::getVar("m");
        
        $query ="select * from `#__suppliers`";
        $db->setQuery($query);
        $suppliers = $db->loadObjectList();
        $this->suppliers = $suppliers;

        $query ="select p.*,u.id unit_id,u.unit unit_name from `#__products` p inner join `#__units` u on p.unit_id=u.id";
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $this->products = $products;
        
        
        /*$query = "select * from `#__products` order by product_name";        
        $db->setQuery($query);
        $products = $db->loadObjectList();
        $this->products = $products;*/
        //print_r($products); 
        
        $query = "SELECT * FROM `jos_vehicles`";
        $db->setQuery($query);
        $vehicles = $db->loadObjectlist();
        $this->vehicles = $vehicles;

        if($mode == 'e')
        {
            
            $document = JFactory::getDocument();
            $document->setTitle("Edit Purchase Entry");
            
            $purchase_id = JRequest::getVar("purchase_id"); 
            
            $query = "select pu.*,s.supplier_name,v.vehicle_number from `#__purchase` pu inner join `#__suppliers` s on pu.supplier_id=s.id inner join `#__vehicles` v on pu.vehicle_id=v.id where pu.id=" . $purchase_id;
            $db->setQuery($query);
            $purchase = $db->loadObject();
            $this->purchase = $purchase;
            $this->purchase_id = $purchase_id;
            
            $query = "select pi.*,p.product_name,u.unit from `#__purchase` pr inner join `#__purchase_items` pi on pi.purchase_id=pr.id inner join `#__products` p on pi.item_id=p.id inner join `#__units` u on pi.unit_id=u.id where pi.purchase_id=" . $purchase_id ;
            $db->setQuery($query);
            $purchase_items = $db->loadObjectList();
            $this->purchase_items = $purchase_items;
        
            parent::display("edit");
        }
        else
        {
            $this->supplier_id = intval(JRequest::getVar("supplier_id"));
            parent::display($tpl);
        } 
	}
}
?>
