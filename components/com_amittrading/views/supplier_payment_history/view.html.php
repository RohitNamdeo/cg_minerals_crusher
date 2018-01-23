<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewSupplier_payment_history extends JViewLegacy
{
    public function display($tpl = null)
    {
        // just a payment history without any action, edit delete can be done from supplier's account
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "supplier_payment_history"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Supplier Payment History");
        
        $d = JRequest::getVar("d");
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        
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
        
        $condition = "(p.payment_type=" . SUPPLIER_PAYMENT . ")";
        
        if($supplier_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(p.party_id=" . $supplier_id . ")";
        }
        
        if($from_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(p.payment_date='" . date("Y-m-d", strtotime($from_date)) . "')";
        }
        
        $query = "select p.*, p.id payment_id, s.supplier_name, ba.account_name, ba.bank_name from `#__payments` p inner join `#__suppliers` s on p.party_id=s.id left join `#__bank_accounts` ba on p.bank_account_id=ba.id " . ($condition != "" ? " where " . $condition : "") . " order by p.payment_date asc, p.id";
        $db->setQuery($query);
        $payments = $db->loadObjectList();       
        
        $limit = 100;
        $total = count($payments);
        $limit = JRequest::getVar('limit',$limit, '', 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $db->setQuery( $query, $limitstart, $limit );
        $payments = $db->loadObjectlist();
        
        $this->pagination = $pagination;
        $this->total = $total;
        $this->limit = $limit;        
        $this->limitstart = $limitstart;
        
        $query = "select s.id, s.supplier_name from `#__suppliers` s order by s.supplier_name";
        $db->setQuery($query);
        $suppliers = $db->loadObjectList();                
        $this->suppliers = $suppliers;
       
        $this->payments = $payments;
        $this->supplier_id = $supplier_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        parent::display($tpl);
    } 
}
?>