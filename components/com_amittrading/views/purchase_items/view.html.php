<?
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewpurchase_items extends JViewLegacy
{
    public function display($tpl = null)
    {
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Purchase Items");
        
        $purchase_id = intval(JRequest::getVar('purchase_id'));
        $this->purchase_id = $purchase_id;
        
        $query = "select pu.*,s.supplier_name,v.vehicle_number from `#__purchase` pu inner join `#__suppliers` s on pu.supplier_id=s.id inner join `#__vehicles` v on pu.vehicle_id=v.id where pu.id=" . $purchase_id;
        $db->setQuery($query);
        $purchase = $db->loadObject();
        $this->purchase = $purchase;
           
        $query = "select pi.*,p.product_name,u.unit from `#__purchase` pr inner join `#__purchase_items` pi on pi.purchase_id=pr.id inner join `#__products` p on pi.item_id=p.id inner join `#__units` u on pi.unit_id=u.id where pi.purchase_id=" . $purchase_id ;
        $db->setQuery($query);
        $purchase_items = $db->loadObjectList();
        $this->purchase_items = $purchase_items;
        
        parent::display($tpl);
    } 
}
?>