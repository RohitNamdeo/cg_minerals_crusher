<?php
defined('_JEXEC') or die( 'Restricted access' );

class AmittradingViewTransports_and_payments extends JViewLegacy
{
    public function display($tpl = null)
    {
        /*
        * this view can be viewed from transporter account
        * It shows the list of all the purchase invoices and payments for that transporter
        * payments can be edited/deleted only if the payment type is credit
        * unpaid bills total can be viewed by clicking on the checkbox provided
        * unpaid bills are red and paid are black
        * on clicking on payment, all the purchase invoices that were adjusted in that payment are highlighted by different color depending on whether the it was fully or partly adjusted
        */
        
        if(Functions::ifNotLoginRedirect("index.php"))
        {
            return;
        }
        
        $db = JFactory::getDbo();
        
        $document = JFactory::getDocument()->setTitle("Transports and Payments");
        
        $from_date = JRequest::getVar('from_date');
        $to_date = JRequest::getVar('to_date');
        
        if($from_date != "" && $to_date != "")
        { 
            $from_date = date("Y-m-d", strtotime($from_date));
            $to_date = date("Y-m-d", strtotime($to_date));
        }
        else
        {
            $from_date = date("Y-m-d", strtotime("-1 months"));
            $to_date = date("Y-m-d");
        }
        
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $condition1 = "(date between '" . $from_date . "' and '" . $to_date . "')";
        
        $transporter_id = intval(JRequest::getVar("transporter_id")); 
        
        //$queryA = "select a.transporter_id transporter_id, a.vehicle_rate vehicle_rate, a.add_cash, a.diesel_total_amount, '' loading_amount, '' loading_amount_paid,'' loading_transporter_id, a.transportation_amount_paid transportation_amount_paid, a.id id, a.bill_no bill_no, a.amount_paid amount_paid, a.date date, a.remarks remarks from #__sales_invoice a where transporter_id=".$transporter_id." and ".$condition1; 
//        $queryB = "select '' transporter_id, '' vehicle_rate, '' add_cash, '' diesel_total_amount, b.loading_amount loading_amount, b.loading_amount_paid loading_amount_paid, b.loading_transporter_id loading_transporter_id, '' transportation_amount_paid, b.id id, b.bill_no bill_no, b.amount_paid amount_paid, b.date date, b.remarks remarks from #__sales_invoice b where loading_transporter_id=".$transporter_id." and ".$condition1; 
        
        
       // $query = "(" . $queryA. ") UNION ( " .$queryB. " ) order by id, bill_no";
        $query = "select * from `#__transporter_bills` where transporter_id=".$transporter_id;
        $db->setQuery($query);
        $transporter_bills = $db->loadObjectList();
        $this->bills = $transporter_bills;       
        
        //$invoicewise_total_amount_paid = array(); 
//        foreach($transporter_details as $transporter_detail)
//        {
//            if($transporter_detail->transportation_amount_paid != "")
//            {
//                $invoicewise_total_amount_paid[$transporter_detail->id] = $transporter_detail->transportation_amount_paid ;            
//            }
//        }
//        
//        if($invoicewise_total_amount_paid != "")
//        {
//            foreach($transporter_details as $transporter_detail)
//            {
//                $invoicewise_total_amount = $invoicewise_total_amount_paid[$transporter_detail->id];
//                $amount_to_be_paid = ($transporter_detail->vehicle_rate != "" ? $transporter_detail->vehicle_rate : $transporter_detail->loading_amount);
//                $transportation_amount_paid = $transporter_detail->transportation_amount_paid;
//                if($amount_to_be_paid !='' && $amount_to_be_paid > 0)
//                {
//                    if($invoicewise_total_amount >= $amount_to_be_paid)
//                    {
//                        
//                        $transporter_detail->transportation_amount_paid = $amount_to_be_paid; 
//                        $invoicewise_total_amount_paid[$transporter_detail->id] = $invoicewise_total_amount - $amount_to_be_paid;            
//                    }
//                    else
//                    {
//                        $transporter_detail->transportation_amount_paid = $invoicewise_total_amount; 
//                        $invoicewise_total_amount_paid[$transporter_detail->id] = 0;                                    
//                    }
//                }
//            }
//        }
        
        //$query = "select p.*, p.id payment_id from `#__transporter_payments` p where p.transporter_id=" . $transporter_id ." order by p.payment_date asc, p.id";
        
        $query = "select * from `#__transporter_payments` where transporter_id=".$transporter_id;
        $db->setQuery($query);
        $payments = $db->loadObjectList();        
        
        //$this->bills = $transporter_details;
        $this->transporter_id = $transporter_id;  
        $this->payments = $payments;
        
        parent::display($tpl);
    } 
}
?>