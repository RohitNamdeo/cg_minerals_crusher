<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewCash_expense_history extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * view to show list of cash expense entry
        * cash enpense entry can be via form ( no type mentioned in list) or "transporter payment" made in transporter payment
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "cash_expense_history"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Cash Expense History");
        
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        
        if($from_date == "" || $to_date == "")
        {
            $from_date = date("Y-m-01");
            $to_date = date("Y-m-d");
        }
        
        $condition = "";
        
        if($from_date != "" && $to_date != "")
        {
            $condition .= ($condition != "" ? " and " : "") . "(ce.expense_date between '" . date("Y-m-d", strtotime($from_date)) . "' and '" . date("Y-m-d", strtotime($to_date)) . "')";
        }
        
        $query = "select ce.*,eh.expense_head from `#__cash_expenses` ce inner join `#__expense_head` eh on ce.expense_head_id=eh.id " . ($condition != "" ? " where " . $condition : "") . " order by ce.expense_date asc, id";
        $db->setQuery($query);
        $expenses = $db->loadObjectList();       
        
        $limit = 100;
        $total = count($expenses);
        $limit = JRequest::getVar('limit',$limit, '', 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
        $db->setQuery( $query, $limitstart, $limit );
        $expenses = $db->loadObjectlist();
        
        $this->pagination = $pagination;
        $this->total = $total;
        $this->limit = $limit;        
        $this->limitstart = $limitstart;
       
        $this->expenses = $expenses;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        parent::display($tpl);
    } 
}
?>