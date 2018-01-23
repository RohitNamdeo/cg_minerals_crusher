<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewItemwise_sales_stats extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * For selected item and for a selected date range
        * the data is shown day-wise
        * it includes purchase, sales, their return and stock transfers
        * how the stock changed with every voucher is shown as account statement (credit & debit) 
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "itemwise_sales_stats"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Item wise Sales Stats");
        
        $item_id = intval(JRequest::getVar("item_id"));
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        
        if($from_date == "" || $to_date == "")
        {
            $from_date = date("Y-m-d");
            $to_date = date("Y-m-d");
        }
        
        $opening_data = new stdClass();
        $locationwise_item_sales_stats = array();
        
        if($item_id > 0 && $from_date != "" && $to_date != "")
        {
            $purchase_condition = "";
            $purchase_return_condition = "";
            $sales_condition = "";
            $sales_return_condition = "";
            $stock_condition = "";
            
            $purchase_condition .= ($purchase_condition != "" ? " and " : "") . "(pi.item_id=" . $item_id . " and p.bill_date between '" . date("Y-m-d", strtotime($from_date)) . "' and '" . date("Y-m-d", strtotime($to_date)) . "')";
            $purchase_return_condition .= ($purchase_return_condition != "" ? " and " : "") . "(pri.item_id=" . $item_id . " and pr.challan_date between '" . date("Y-m-d", strtotime($from_date)) . "' and '" . date("Y-m-d", strtotime($to_date)) . "')";
            $sales_condition .= ($sales_condition != "" ? " and " : "") . "(si.item_id=" . $item_id . " and s.bill_date between '" . date("Y-m-d", strtotime($from_date)) . "' and '" . date("Y-m-d", strtotime($to_date)) . "')";
            $sales_return_condition .= ($sales_return_condition != "" ? " and " : "") . "(sri.item_id=" . $item_id . " and sr.bill_date between '" . date("Y-m-d", strtotime($from_date)) . "' and '" . date("Y-m-d", strtotime($to_date)) . "')";
            $stock_condition .= ($stock_condition != "" ? " and " : "") . "(sti.item_id=" . $item_id . " and st.st_date between '" . date("Y-m-d", strtotime($from_date)) . "' and '" . date("Y-m-d", strtotime($to_date)) . "')";
            
            $query = "select ii.opening_stock, ii.location_id from `#__inventory_items` ii where ii.item_id=" . $item_id;
            $db->setQuery($query);
            $opening_data->opening = $db->loadObjectList("location_id");
            
            $query = "select sum(pi.pack) opening_credit ,pi.location_id from `#__purchase_invoice_items` pi inner join `#__purchase_invoice` p on pi.purchase_id=p.id where p.bill_date < '" . date("Y-m-d", strtotime($from_date)) . "' and pi.item_id=" . $item_id . " group by pi.location_id";
            $db->setQuery($query);
            $opening_data->purchase_opening = $db->loadObjectList("location_id");
            
            $query = "select sum(pri.pack) opening_debit ,pri.location_id from `#__purchase_returns` pr inner join `#__purchase_return_items` pri on pri.return_id=pr.id where pr.challan_date < '" . date("Y-m-d", strtotime($from_date)) . "' and pri.item_id=" . $item_id . " group by pri.location_id";
            $db->setQuery($query);
            $opening_data->purchase_return_opening = $db->loadObjectList("location_id");
            
            $query = "select sum(si.pack) opening_debit ,si.location_id from `#__sales_invoice_items` si inner join `#__sales_invoice` s on si.sales_id=s.id where s.bill_date < '" . date("Y-m-d", strtotime($from_date)) . "' and si.item_id=" . $item_id . " group by si.location_id";
            $db->setQuery($query);
            $opening_data->sale_opening = $db->loadObjectList("location_id");
            
            $query = "select sum(sri.pack) opening_credit ,sri.location_id from `#__sales_return_items` sri inner join `#__sales_returns` sr on sri.return_id=sr.id where sr.bill_date < '" . date("Y-m-d", strtotime($from_date)) . "' and sri.item_id=" . $item_id . " group by sri.location_id";
            $db->setQuery($query);
            $opening_data->sale_return_opening = $db->loadObjectList("location_id");
            
            $query = "select sum(sti.pack) opening_debit, st.location_from_id from `#__stock_transfer` st inner join `#__stock_transfer_items` sti on sti.stock_transfer_id=st.id where st.st_date < '" . date("Y-m-d", strtotime($from_date)) . "' and sti.item_id=" . $item_id . " group by st.location_from_id";
            $db->setQuery($query);
            $opening_data->stock_transfer_opening_debit = $db->loadObjectList("location_from_id");

            $query = "select sum(sti.pack) opening_credit, st.location_to_id from `#__stock_transfer` st inner join `#__stock_transfer_items` sti on sti.stock_transfer_id=st.id where st.st_date < '" . date("Y-m-d", strtotime($from_date)) . "' and sti.item_id=" . $item_id . " group by st.location_to_id";
            $db->setQuery($query);
            $opening_data->stock_transfer_opening_credit = $db->loadObjectList("location_to_id");
            
            $query1 = "select 'CREDIT' item_type, pi.location_id, '0' location_from_id, '0' location_to_id, pi.pack quantity, p.bill_date date, concat(s.supplier_name ,'(', p.bill_no, ')') particular from `#__purchase_invoice_items` pi inner join `#__purchase_invoice` p on pi.purchase_id=p.id inner join `#__suppliers` s on p.supplier_id=s.id where " . $purchase_condition;
            $query2 = "select 'DEBIT' item_type, pri.location_id, '0' location_from_id, '0' location_to_id, pri.pack quantity, pr.challan_date date, concat(s.supplier_name ,'(', pr.challan_no, ')') particular from `#__purchase_return_items` pri inner join `#__purchase_returns` pr on pri.return_id=pr.id inner join `#__suppliers` s on pr.supplier_id=s.id where " . $purchase_return_condition;
            $query3 = "select 'DEBIT' item_type, si.location_id, '0' location_from_id, '0' location_to_id, si.pack quantity, s.bill_date date, concat(c.customer_name ,'(', s.bill_no, ')') particular from `#__sales_invoice_items` si inner join `#__sales_invoice` s on si.sales_id=s.id inner join `#__customers` c on s.customer_id=c.id where " . $sales_condition;
            $query4 = "select 'CREDIT' item_type, sri.location_id, '0' location_from_id, '0' location_to_id, sri.pack quantity, sr.bill_date date, concat(c.customer_name ,'(', sr.bill_no, ')') particular from `#__sales_return_items` sri inner join `#__sales_returns` sr on sri.return_id=sr.id inner join `#__customers` c on sr.customer_id=c.id where " . $sales_return_condition;
            $query5 = "select 'ST' item_type, '0' location_id, st.location_from_id, st.location_to_id, sti.pack quantity, st.st_date date, concat('Stock Transfer', '(', st.id, ')') particular from `#__stock_transfer` st inner join `#__stock_transfer_items` sti on sti.stock_transfer_id=st.id where " . $stock_condition;
            
            $query = $query1 . " union " . $query2 . " union " . $query3 . " union " . $query4 . " union " . $query5 . " order by date";
            $db->setQuery($query);
            $locationwise_item_sales_stats = $db->loadObjectList();
        }
        
        $query = "select id, location_name from `#__inventory_locations` order by location_name";
        $db->setQuery($query);
        $locations = $db->loadObjectList("id");
        
        $query = "select i.id, i.item_name, i.category_id, c.category_name from `#__items` i inner join `#__category_list` c on i.category_id=c.id order by c.category_name, i.item_name";
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        $this->locationwise_item_sales_stats = $locationwise_item_sales_stats;
        $this->opening_data = $opening_data;
        $this->locations = $locations;
        $this->items = $items;
        $this->item_id = $item_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        
        parent::display($tpl);
    } 
}
?>