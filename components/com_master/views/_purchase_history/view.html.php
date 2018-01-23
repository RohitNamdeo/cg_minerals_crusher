<?php
defined('_JEXEC') or die( 'Restricted access' );

class MasterViewPurchase_history extends JViewLegacy
{
    public function display($tpl = null)
    {
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("master", "purchase_history"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_master&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Purchase History");
        
        $d = JRequest::getVar("d");
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        
       echo $supplier_id;
        
        if($d != "")
        {
            if($d == 'p')
            {
                $from_date = date("Y-m-d", strtotime("-1 day", strtotime($from_date)));
            }
            else if($d == 'n')
            {
                $from_date = date("Y-m-d", strtotime("+1 day", strtotime($from_date)));
            }
        }
        else
        {
            if($from_date != "")
            { $from_date = date("Y-m-d", strtotime($from_date)); }
        }
        
        if($from_date == "")
        {
            $from_date = date("Y-m-d");
        }
        
        $condition = "";
        
        if($supplier_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(p.supplier_id=" . $supplier_id . ")";
        }
        
        if($from_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(p.bill_date='" . date("Y-m-d", strtotime($from_date)) . "')";
        }
        
        
       
        
        $query = "select pc.*,pc.id purchase_id,u.unit,p.product_name,s.supplier_name from `jos_purchase` pc inner join `jos_suppliers` s pc.supplier_id=s.id inner join `jos_products` p inner join pc.product_id=p.id `jos_units`u pc.unit_id=u.id";
        $db->setQuery($query);
        $purchase_history = $db->loadObjectList();
        
        $this->purchase_history = $purchase_history;
        
        parent::display($tpl);
    } 
}
?>