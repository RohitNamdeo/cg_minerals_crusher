<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewSupplier_account extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * this is supplier account
        * there are 3 tabs
        * 1st tab is purchases & payments -> purchases_and_payments view
        * 2nd tab is for supplier details, edit and delete account options are provided -> in same view
        * 3rd tab id account statement -> supplier_account_statement view
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Supplier Account");
        
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        $this->supplier_id = $supplier_id;
        
        $query = "select s.*, c.city city_name,st.name state_name from `#__suppliers` s inner join `#__cities` c on s.city_id=c.id left join `#__states` st on s.state_id=st.id where s.id=" . $supplier_id;
        $db->setQuery($query);
        $supplier = $db->loadObject();                
        $this->supplier = $supplier;
        
        
        $query = "select * from `#__cities` order by city";
        $db->setQuery($query);
        $cities = $db->loadObjectList();
        $this->cities = $cities;
        
        parent::display($tpl);
    } 
}
?>