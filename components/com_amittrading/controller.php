<?
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class AmittradingController extends JControllerLegacy
{
    function display($cachable = false, $urlparams = Array())
    {
        parent::display();
    }
    
    function get_items()
    {
        $model = $this->getModel("amittrading");
        $model->get_items();
    }
    
    function get_items_for_sales_order()
    {
        $model = $this->getModel("amittrading");
        $model->get_items_for_sales_order();
    }
    
    function get_items_with_stock_details()
    {
        $model = $this->getModel("amittrading");
        $model->get_items_with_stock_details();
    }
    
    function get_locationwise_items_with_stock()
    {
        $model = $this->getModel("amittrading");
        $model->get_locationwise_items_with_stock();
    }
    
    function get_items_in_stock()
    {
        $model = $this->getModel("amittrading3");
        $model->get_items_in_stock();
    }
    
    function save_purchase_order()
    {
        $model = $this->getModel("amittrading");
        $msg = $model->save_purchase_order();
        //$this->setRedirect("index.php?option=com_amittrading&view=pending_purchase_orders", $msg);
        $this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
    }
    
    function update_purchase_order()
    {
        $model = $this->getModel("amittrading");
        $msg = $model->update_purchase_order();
        //$this->setRedirect("index.php?option=com_amittrading&view=pending_purchase_orders", $msg);
        $this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
    }
    
    function cancel_order_items()
    {
        $model = $this->getModel("amittrading");
        $model->cancel_order_items();
    }
    
    function delete_purchase_order()
    {
        $model = $this->getModel("amittrading");
        $msg = $model->delete_purchase_order();
        //$this->setRedirect("index.php?option=com_amittrading&view=pending_purchase_orders", $msg);
        $this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
    }
    
    function save_purchase_invoice()
    {
        $model = $this->getModel("amittrading");
        $msg = $model->save_purchase_invoice();
        
        $return = JRequest::getVar("r");
        $url = ($return!="" ? base64_decode($return) : "index.php");
        $this->setRedirect($url,$msg);
        //$this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
    }
    
    function update_purchase_invoice()
    {
        $model = $this->getModel("amittrading");
        $msg = $model->update_purchase_invoice();
        
        $return = JRequest::getVar("r");
        $url = ($return!="" ? base64_decode($return) : "index.php");
        $this->setRedirect($url,$msg);
        //$this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
    }
    
    function delete_purchase_invoice()
    {
        $model = $this->getModel("amittrading");
        $msg = $model->delete_purchase_invoice();
        
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        
        /*if($supplier_id)
        { $this->setRedirect("index.php?option=com_amittrading&view=supplier_account&supplier_id=" . $supplier_id, $msg); }
        else
        {
            $this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
            //$this->setRedirect("index.php?option=com_amittrading&view=purchase_history", $msg);
        }*/
        
        $this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
    }
    
    function save_purchase_return()
    {
        $model = $this->getModel("amittrading");
        $msg = $model->save_purchase_return();
        //$this->setRedirect("index.php?option=com_amittrading&view=purchase_return", $msg);
        $this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
    }
    
    function update_purchase_return()
    {
        $model = $this->getModel("amittrading");
        $msg = $model->update_purchase_return();
        //$this->setRedirect("index.php?option=com_amittrading&view=purchase_return_history", $msg);
        $this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
    }
    
    function save_sales_order()
    {
        $model = $this->getModel("amittrading1");
        $sales_id = $model->save_sales_order();
        //$this->setRedirect("index.php?option=com_amittrading&view=sales_order_history");
        $this->setRedirect("index.php?option=com_amittrading&view=pending_sales_orders");
    }
    
    function save_sales_invoice()
    {
        $model = $this->getModel("amittrading1");
        $sales_id = $model->save_sales_invoice();
        $this->setRedirect("index.php?option=com_amittrading&view=sales_invoice_print&tmpl=print&invoice_id=" . $sales_id);
        //$this->setRedirect("index.php?option=com_amittrading&view=sales_invoice",$msg);
    }
    
    function update_sales_invoice()
    {
        $model = $this->getModel("amittrading1");
        $sales_id = $model->update_sales_invoice();
        $this->setRedirect("index.php?option=com_amittrading&view=sales_invoice_print&tmpl=print&invoice_id=" . $sales_id);
        //$this->setRedirect("index.php?option=com_amittrading&view=sales_invoice_history",$msg);
    }
    
    function delete_sales_invoice()
    {
        $model = $this->getModel("amittrading1");
        $msg = $model->delete_sales_invoice(); 
        $customer_id = intval(JRequest::getVar("customer_id"));
        
        if($customer_id)
        { $this->setRedirect("index.php?option=com_amittrading&view=customer_account&customer_id=" . $customer_id, $msg); }
        else
        {
            //$this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
            $this->setRedirect("index.php?option=com_amittrading&view=sales_invoice_history", $msg);
        }
        //$this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
    }
    function save_royalty_sales()
    {
        $model = $this->getModel("amittrading1");
        $msg = $model->save_royalty_sales();
        $this->setRedirect("index.php?option=com_amittrading&view=royalty_sales_history", $msg);        
    }
    
    function get_royalty_mt()
    {
        $model = $this->getModel("amittrading1");
        $msg = $model->get_royalty_mt();    
    }
    
    function get_royalty_no()
    {
        $model = $this->getModel("amittrading1");
        $msg = $model->get_royalty_no();    
    }
    /*function total_pages()
    {
        $model = $this->getModel("amittrading1");
        $msg = $model->total_pages();    
    }*/
    
    function get_bill_no()
    {
        $model = $this->getModel("amittrading");
        $msg = $model->get_bill_no();   
    }
    
    /*function get_product_name()
    {
        $model = $this->getModel("amittrading1");
        $msg = $model->get_product_name(); 
    }*/
    
    
    function calculate_due_amount()
    {
        $model = $this->getModel("amittrading2");
        $model->calculate_due_amount();
    }
    
    function save_customer_payment()
    {
        $model = $this->getModel("amittrading2");
        //$msg = $model->save_customer_payment();
        $payment_id = $model->save_customer_payment();
        
       /* $return = JRequest::getVar("r");
        $url = ($return!="" ? base64_decode($return) : "index.php");
        $this->setRedirect($url,$msg);*/
        
        $this->setRedirect("index.php?option=com_amittrading&view=customer_payment_print&tmpl=print&payment_id=" . $payment_id);
    }
    
    function update_customer_payment()
    {
        $model = $this->getModel("amittrading2");
        $payment_id = $model->update_customer_payment();
        
        $this->setRedirect("index.php?option=com_amittrading&view=customer_payment_print&tmpl=print&payment_id=" . $payment_id);
    }
    
    function delete_customer_account()
    {
        $model = $this->getModel("amittrading2");
        $msg = $model->delete_customer_account();
        $this->setRedirect("index.php?option=com_master&view=manage_customers", $msg);
    }
    
    function get_bills()
    {
        $model = $this->getModel("amittrading2");
        $model->get_bills();
    }
    
    function delete_customer_payment()
    {
        $model = $this->getModel("amittrading2");
        $msg = $model->delete_customer_payment();
        $customer_id = intval(JRequest::getVar("customer_id"));
        $this->setRedirect("index.php?option=com_amittrading&view=customer_account&customer_id=" . $customer_id, $msg);
    }
    
    function delete_customer_cheque_payment()
    {
        $model = $this->getModel("amittrading4");
        $model->delete_customer_cheque_payment();
    }
    
    function save_supplier_payment()
    {
        $model = $this->getModel("amittrading2");
        $msg = $model->save_supplier_payment();
        
        $return = JRequest::getVar("r");
        $url = ($return!="" ? base64_decode($return) : "index.php");
        $this->setRedirect($url,$msg);
    }
    
    function update_supplier_payment()
    {
        $model = $this->getModel("amittrading2");
        $msg = $model->update_supplier_payment();
        
        $return = JRequest::getVar("r");
        $url = ($return!="" ? base64_decode($return) : "index.php");
        $this->setRedirect($url,$msg);
    }
    
    function delete_supplier_payment()
    {
        $model = $this->getModel("amittrading2");
        $msg = $model->delete_supplier_payment();
        $supplier_id = intval(JRequest::getVar("supplier_id"));
        $this->setRedirect("index.php?option=com_amittrading&view=supplier_account&supplier_id=" . $supplier_id, $msg);
    }
    
    function delete_supplier_account()
    {
        $model = $this->getModel("amittrading2");
        $msg = $model->delete_supplier_account();
        $this->setRedirect("index.php?option=com_master&view=manage_suppliers", $msg);
    }
    
    function save_stock_transfer_item()
    {
        $model = $this->getModel("amittrading3");
        $model->save_stock_transfer_item();
    }
    
    function delete_stock_transfer_item()
    {
        $model = $this->getModel("amittrading3");
        $model->delete_stock_transfer_item();
    }
    
    function save_stock_transfer()
    {
        $model = $this->getModel("amittrading3");
        //$msg = $model->save_stock_transfer();
        //$this->setRedirect("index.php?option=com_amittrading&view=stock_transfer", $msg);
        
        $stock_transfer_id = $model->save_stock_transfer();
        $this->setRedirect("index.php?option=com_amittrading&view=stock_transfer_print&tmpl=print&st_id=" . $stock_transfer_id);
    } 
    
    function update_stock_transfer()
    {
        $model = $this->getModel("amittrading3");
        //$msg = $model->update_stock_transfer();
        //$this->setRedirect("index.php?option=com_amittrading&view=stock_transfer_history", $msg);
        
        $stock_transfer_id = $model->update_stock_transfer();
        $this->setRedirect("index.php?option=com_amittrading&view=stock_transfer_print&tmpl=print&st_id=" . $stock_transfer_id);
    }
    
    function change_transfer_status()
    {
        $model = $this->getModel("amittrading3");
        $model->change_transfer_status();
    }
    
    function delete_stock_transfer()
    {
        $model = $this->getModel("amittrading3");
        $msg = $model->delete_stock_transfer();
        //$this->setRedirect("index.php?option=com_amittrading&view=stock_transfer_history", $msg);
        $this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
    }
    
    function save_cash_expense_entry()
    {
        $model = $this->getModel("amittrading4");
        $msg = $model->save_cash_expense_entry();
        $this->setRedirect("index.php?option=com_amittrading&view=cash_expense_entry", $msg);
    }
    
    function save_transporter_payment()
    {
        $model = $this->getModel("amittrading2");
        $msg = $model->save_transporter_payment();
        
        $return = JRequest::getVar("r");
        $url = ($return!="" ? base64_decode($return) : "index.php");
        $this->setRedirect($url,$msg);
    }
    
    function update_transporter_payment()
    {
        $model = $this->getModel("amittrading2");
        $msg = $model->update_transporter_payment();
        
        $return = JRequest::getVar("r");
        $url = ($return!="" ? base64_decode($return) : "index.php");
        $this->setRedirect($url,$msg);
    }
    
    function delete_transporter_payment()
    {
        $model = $this->getModel("amittrading2");
        $msg = $model->delete_transporter_payment();
        $transporter_id = intval(JRequest::getVar("transporter_id"));
        $this->setRedirect("index.php?option=com_amittrading&view=transporter_account&transporter_id=" . $transporter_id, $msg);
    }
    
    function delete_transporter_account()
    {
        $model = $this->getModel("amittrading2");
        $msg = $model->delete_transporter_account();
        $this->setRedirect("index.php?option=com_master&view=manage_transporters", $msg);
    }
    
    function save_cash_transaction()
    {
        $model = $this->getModel("amittrading4");
        $msg = $model->save_cash_transaction();
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $this->setRedirect("index.php?option=com_amittrading&view=bank_account&bank_account_id=" . $bank_account_id, $msg);
    }
    
    function cash_transaction_details()
    {
        $model = $this->getModel("amittrading4");
        $model->cash_transaction_details();
    }
    
    function edit_cash_transaction()
    {
        $model = $this->getModel("amittrading4");
        $msg = $model->edit_cash_transaction();
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $this->setRedirect("index.php?option=com_amittrading&view=bank_account&bank_account_id=" . $bank_account_id, $msg);
    }
    
    function edit_fund_transfer()
    {
        $model = $this->getModel("amittrading4");
        $msg = $model->edit_fund_transfer();
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $this->setRedirect("index.php?option=com_amittrading&view=bank_account&bank_account_id=" . $bank_account_id, $msg);
    }
    
    
    function delete_cash_transaction()
    {
        $model = $this->getModel("amittrading4");
        $msg = $model->delete_cash_transaction();
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $this->setRedirect("index.php?option=com_amittrading&view=bank_account&bank_account_id=" . $bank_account_id, $msg);
    }
    function delete_fund_transfer()
    {
        $model = $this->getModel("amittrading4");
        $msg = $model->delete_fund_transfer();
        $bank_account_id = intval(JRequest::getVar("bank_account_id"));
        $this->setRedirect("index.php?option=com_amittrading&view=bank_account&bank_account_id=" . $bank_account_id, $msg);          
    }
    
    function clear_customer_cheque()
    {
        $model = $this->getModel("amittrading4");
        $msg = $model->clear_customer_cheque();
        $this->setRedirect("index.php?option=com_amittrading&view=bank_reconcilliation", $msg);
    }
    
    function clear_cheque()
    {
        $model = $this->getModel("amittrading4");
        $model->clear_cheque();
    }
    
    function get_pending_bills()
    {
        $model = $this->getModel("amittrading1");
        $model->get_pending_bills();
    }
    
    function send_payment_reminder_to_customers()
    {
        $model = $this->getModel("amittrading4");
        $model->send_payment_reminder_to_customers();
    }
    
    function update_item_stock()
    {
        $model = $this->getModel("amittrading4");
        $model->update_item_stock();
    }
    
    function update_customer_account_balance()
    {
        $model = $this->getModel("amittrading4");
        $model->update_customer_account_balance();
    }
    
    function update_supplier_account_balance()
    {
        $model = $this->getModel("amittrading4");
        $model->update_supplier_account_balance();
    }
    
    function save_production()
    {
        $model = $this->getModel('amittrading');
        $model->save_production();    
    }
    function production_details()
    {
        $model = $this->getModel('amittrading');
        $model->production_details();    
    }
    function update_production()
    {
        $model = $this->getModel('amittrading');
        $model->update_production();    
    }
    function delete_production()
    {
        $model = $this->getModel('amittrading');
        $model->delete_production();
        $url = "index.php?option=com_amittrading&view=production_history";
        $this->setRedirect($url);    
    }
    
    function save_purchase_entry()
    {
        
        $model = $this->getModel('amittrading');   
        $msg = $model->save_purchase_entry();
        $url = "index.php?option=com_amittrading&view=purchase_entry";
        $this->setRedirect($url,$msg);       
    }
    function update_purchase()
    {
        $model = $this->getModel('amittrading');
        $msg = $model->update_purchase();
        $url = "index.php?option=com_amittrading&view=purchase_history";
        $this->setRedirect($url,$msg);         
    }
    function delete_purchase()
    {
        $model = $this->getModel('amittrading');
        $model->delete_purchase();
        $url = "index.php?option=com_amittrading&view=purchase_history";
        $this->setRedirect($url);        
    }
    
    function fetch_unit_gst()
    {
        $model = $this->getModel('amittrading');
        $model->fetch_unit_gst(); 
    }
    function delete_sales_order()
    {
        $model = $this->getModel("amittrading1");
        $model->delete_sales_order();
        
        $customer_id = intval(JRequest::getVar("customer_id"));
        $this->setRedirect("index.php?option=com_amittrading&view=sales_order_history");
        
       // if($customer_id)
//        { $this->setRedirect("index.php?option=com_amittrading&view=customer_account&customer_id=" . $customer_id, $msg); }
//        else
//        {
            //$this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
            //$this->setRedirect("index.php?option=com_amittrading&view=sales_order_history", $msg);
//        }
        //$this->setRedirect("index.php?option=com_hr&view=dashboard", $msg);
    }
    
    function delete_sales_order_from_pending_view()
    {
        $model = $this->getModel("amittrading1");
        $msg = $model->delete_sales_order();
        $this->setRedirect("index.php?option=com_amittrading&view=pending_sales_orders", $msg);
    }
    
    function update_sales_order()
    {
        $model = $this->getModel("amittrading1");
        //$sales_id = $model->update_sales_order();
        $model->update_sales_order();
        $this->setRedirect("index.php?option=com_amittrading&view=sales_order_history");
    }
}
?>