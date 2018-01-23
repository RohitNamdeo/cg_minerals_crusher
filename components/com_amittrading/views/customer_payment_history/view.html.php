<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewCustomer_payment_history extends JViewLegacy
{
    public function display($tpl = null)
    {
        // just a payment history without any action, edit delete can be done from customer's account
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "customer_payment_history"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Customer Payment History");
        
        $d = JRequest::getVar("d");
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        $customer_id = intval(JRequest::getVar("customer_id"));
        
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
        
        $condition = "(p.payment_type=" . CUSTOMER_PAYMENT . ")";
        
        if($customer_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(p.party_id=" . $customer_id . ")";
        }
        
        if($from_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(p.payment_date='" . date("Y-m-d", strtotime($from_date)) . "')";
        }
        
        $query = "select p.*, p.id payment_id, c.customer_name, b.bank_name cheque_bank, ba.account_name, ba.bank_name from `#__payments` p inner join `#__customers` c on p.party_id=c.id left join `#__banks` b on p.bank_id=b.id left join `#__bank_accounts` ba on p.bank_account_id=ba.id " . ($condition != "" ? " where " . $condition : "") . " order by p.payment_date asc, p.id";
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
        
        $query = "select c.id, c.customer_name from `#__customers` c where account_status=" . AC_ACTIVE . " order by c.customer_name";
        $db->setQuery($query);
        $customers = $db->loadObjectList();                
        $this->customers = $customers;
       
        $this->payments = $payments;
        $this->customer_id = $customer_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        parent::display($tpl);
    } 
}
?>