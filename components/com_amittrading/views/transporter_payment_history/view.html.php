<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewTransporter_payment_history extends JViewLegacy
{
    public function display($tpl = null)
    {
        // just a payment history without any action, edit delete can be done from transporter's account
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "transporter_payment_history"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Transporter Payment History");
        
        $d = JRequest::getVar("d");
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        
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
        
        if($transporter_id != 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(p.transporter_id=" . $transporter_id . ")";
        }
        
        if($from_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(p.payment_date='" . date("Y-m-d", strtotime($from_date)) . "')";
        }
        
        $query = "select p.*, p.id payment_id, t.transporter from `#__transporter_payments` p inner join `#__transporters` t on p.transporter_id=t.id " . ($condition != "" ? " where " . $condition : "") . " order by p.payment_date asc, p.id";
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
        
        $query = "select id, transporter from `#__transporters` order by transporter";
        $db->setQuery($query);
        $transporters = $db->loadObjectList();                
        $this->transporters = $transporters;
       
        $this->payments = $payments;
        $this->transporter_id = $transporter_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        parent::display($tpl);
    } 
}
?>