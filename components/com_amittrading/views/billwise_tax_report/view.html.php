<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewBillwise_tax_report extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * it shows the list of sales invoices with type bill as tax is applied in those sales invoice only
        * no tax is applied on quotations
        * it shows the invoice amount bifurcation as what amount is calculated on 5% VAT & sale and 14.5% VAT & sale
        * how much amount was taxfree
        * this tax variation comes becomes each item as different vat tax% (item master)
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        if (!Functions::has_permissions("amittrading", "billwise_tax_report"))
        {
            JFactory::getApplication()->redirect("index.php?option=com_amittrading&view=not_found");
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Billwise Tax Report");
        
        $from_date = JRequest::getVar("from_date");
        $to_date = JRequest::getVar("to_date");
        $customer_id = intval(JRequest::getVar("c_id"));
        
        if($from_date == "" || $to_date == "")
        {
            $from_date = date("Y-m-01");
            $to_date = date("Y-m-d");
        }
        else
        {
            $from_date = date("Y-m-d", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));
        }
        
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->customer_id = $customer_id;

        $condition = "(s.bill_date between '" . $from_date . "' and '" . $to_date . "')";
        
        if($customer_id > 0)
        {
            $condition .= ($condition != "" ? " and " : "") . "(s.customer_id=" . $customer_id . ")";
        }
        
        $query = "select s.bill_date, s.bill_no, cu.customer_name, si.pack, si.quantity, si.unit_rate, si.amount, i.vat_percent, cu.customer_address, c.city from `#__sales_invoice` s inner join `#__sales_invoice_items` si on s.id=si.sales_id inner join `#__customers` cu on s.customer_id=cu.id inner join `#__items` i on si.item_id=i.id inner join `#__cities` c on cu.city_id=c.id where s.invoice_type=" . BILL . " and " . $condition . " order by s.id, s.bill_date asc";
        $db->setQuery($query);
        $bills = $db->loadObjectList();       
        $this->bills = $bills;
        
        $query = "select id, customer_name from `#__customers` order by customer_name";
        $db->setQuery($query);
        $this->customers = $db->loadObjectList();
        
        parent::display($tpl);
    } 
}
?>