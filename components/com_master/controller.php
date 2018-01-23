<?
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class MasterController extends JControllerLegacy
{
	function display($cachable = false, $urlparams = Array())
	{
		parent::display();
	}
    
    function save_city()
    {
        $model = $this->getModel('master');
        $model->save_city();
    }
        
    function city_details()
    {
        $model = $this->getModel('master');
        $model->city_details();
    }
    
    function update_city()
    {
        $model = $this->getModel('master');
        $model->update_city();    
    }
    
    function delete_city()
    {
        $model = $this->getModel('master');
        $msg = $model->delete_city();  
        $url = "index.php?option=com_master&view=manage_cities";
        $this->setRedirect($url,$msg);  
    }
    //////////////////////////////
    function save_vehicle()
    {
        $model = $this->getModel('master');
        $model->save_vehicle();
    }
    
    function vehicle_details()
    {
        $model = $this->getModel('master');
        $model->vehicle_details();                
    }
    
    function update_vehicle()
    {
        $model = $this->getModel('master');
        $model->update_vehicle();    
    }
    
    function delete_vehicle()
    {
        $model = $this->getModel('master');
        $model->delete_vehicle();
        $url = "index.php?option=com_master&view=manage_vehicles";
        $this->setRedirect($url);    
    }
    
    function save_vehicle_type()
    {
        $model = $this->getModel('master');
        $model->save_vehicle_type();    
    }
    function vehicle_type_details()
    {
        $model = $this->getModel('master');
        $model->vehicle_type_details();    
    }
    function update_vehicle_type()
    {
        $model = $this->getModel('master');
        $model->update_vehicle_type();        
    }
    function delete_vehicle_type()
    {
        $model = $this->getModel('master');
        $model->delete_vehicle_type();
        $url = "index.php?option=com_master&view=manage_vehicles_type";
        $this->setRedirect($url);    
    }
    
    function save_royalty()
    {
        $model = $this->getModel('master');
        $model->save_royalty();    
    }
    function royalty_details()
    {
        $model = $this->getModel('master');
        $model->royalty_details();     
    }
    function update_royalty()
    {
         $model = $this->getModel('master');
        $model->update_royalty();    
    }
    function delete_royalty()
    {
        $model = $this->getModel('master');
        $model->delete_royalty();
        $url = "index.php?option=com_master&view=manage_royalty";
        $this->setRedirect($url);     
    } 
    
    function save_expense_head()
    {
        $model = $this->getModel('master');
        $model->save_expense_head();    
    }
    function details_expense_head()
    {
        $model = $this->getModel('master');
        $model->details_expense_head();    
    }
    function update_expense_head()
    {
        $model = $this->getModel('master');
        $model->update_expense_head();    
    }
    function delete_expense_head()
    {
        $model = $this->getModel('master');
        $model->delete_expense_head();
        $url = "index.php?option=com_master&view=expense_heads";
        $this->setRedirect($url);    
    }
    
    function save_product()
    {
        $model = $this->getModel('master');
        $model->save_product();
    }
    function product_details()
    {
        $model = $this->getModel('master');
        $model->product_details();    
    }
    function update_product()
    {
        $model = $this->getModel('master');
        $model->update_product();
    }
    function delete_product()
    {
        $model = $this->getModel('master');
        $model->delete_product();
        $url = "index.php?option=com_master&view=manage_products";
        $this->setRedirect($url);    
    }
    
    function save_notepad()
    {
        $model = $this->getModel('master');
        $model->save_notepad();           
    }
    function notepad_details()
    {
        $model = $this->getModel('master');
        $model->notepad_details();    
    }
    function update_notepad()
    {
        $model = $this->getModel('master');
        $model->update_notepad();    
    }
    function delete_notepad()
    {
        $model = $this->getModel('master');
        $model->delete_notepad();
        $url = "index.php?option=com_master&view=notepad";
        $this->setRedirect($url);    
    }
    
    function save_royalty_booklet()
    {
        $model = $this->getModel('master');
        $model->save_royalty_booklet();          
    }
    function royalty_booklet_details()
    {
        $model = $this->getModel('master');
        $model->royalty_booklet_details();           
    }
    function update_royalty_booklet()
    {
        $model = $this->getModel('master');
        $model->update_royalty_booklet();    
    }
    function delete_royalty_booklet()
    {
        $model = $this->getModel('master');
        $model->delete_royalty_booklet();
        $url = "index.php?option=com_master&view=royalty_booklets";
        $this->setRedirect($url);    
    }
    
    //////////////////////////////
    function save_location()
    {
        $model = $this->getModel('master');
        $model->save_location();
    }
    
    function location_details()
    {
        $model = $this->getModel('master');
        $model->location_details();
    }
    
    function update_location()
    {
        $model = $this->getModel('master');
        $model->update_location();    
    }
    
    function delete_location()
    {
        $model = $this->getModel('master');
        $msg = $model->delete_location();  
        $url = "index.php?option=com_master&view=manage_locations";
        $this->setRedirect($url,$msg);  
    }
    
    
    function save_route()
    {
        $model = $this->getModel('master');
        $model->save_route();
    }
    
    function route_details()
    {
        $model = $this->getModel('master');
        $model->route_details();
    }
    
    function update_route()
    {
        $model = $this->getModel('master');
        $model->update_route();    
    }
    
    function delete_route()
    {
        $model = $this->getModel('master');
        $msg = $model->delete_route();  
        $url = "index.php?option=com_master&view=manage_routes";
        $this->setRedirect($url,$msg);  
    }
    
    function save_salesman()
    {
        $model = $this->getModel('master');
        $model->save_salesman();
    }
    
    function salesman_details()
    {
        $model = $this->getModel('master');
        $model->salesman_details();
    }
    
    function update_salesman()
    {
        $model = $this->getModel('master');
        $model->update_salesman();    
    }
    
    function delete_salesman()
    {
        $model = $this->getModel('master');
        $msg = $model->delete_route();  
        $url = "index.php?option=com_master&view=manage_salesmans";
        $this->setRedirect($url,$msg);  
    }
    
    function create_category()
    {
        $model = $this->getModel("master");
        $model->create_category();
    }
    
    function category_details()
    {
        $model = $this->getModel("master");
        $model->category_details();
    }

    function update_category()
    {
        $model = $this->getModel("master");
        $model->update_category();
    }

    function delete_category()
    {
        $model = $this->getModel("master");
        $msg = $model->delete_category();
        $link = "index.php?option=com_master&view=manage_categories" ;
        $this->setRedirect($link, $msg);
    }
    
    function create_item()
    {
        $model = $this->getModel("master");
        $model->create_item();
    }
    
    function item_details()
    {
        $model = $this->getModel("master");
        $model->item_details();
    }
    
    function get_locationwise_items_opening_balance()
    {
        $model = $this->getModel("master");
        $model->get_locationwise_items_opening_balance();
    }

    function update_item()
    {
        $model = $this->getModel("master");
        $model->update_item();
    }

    function delete_item()
    {
        $model = $this->getModel("master");
        $model->delete_item();
    }
    
    function create_supplier()
    {
        $model = $this->getModel("master");
        $model->create_supplier();
    }
    
    function supplier_details()
    {
        $model = $this->getModel("master");
        $model->supplier_details();
    }
    
    function update_supplier()
    {
        $model = $this->getModel("master");
        $model->update_supplier();
    }
    
    function delete_supplier()
    {
        $model = $this->getModel("master");
        $model->delete_supplier();
    }
    
    function create_transporter()
    {
        $model = $this->getModel("master");
        $model->create_transporter();    
    }
    function transporter_details()
    {
        $model = $this->getModel("master");
        $model->transporter_details();    
    }
    function update_transporter()
    {
        $model = $this->getModel("master");
        $model->update_transporter();    
    }
    function delete_transporter()
    {
        $model = $this->getModel("master");
        $model->delete_transporter();
    } 
    
    // Customer
    
    function create_customer()
    {
        $model = $this->getModel("master");
        $model->create_customer();
    }
    
    function customer_details()
    {
        $model = $this->getModel("master");
        $model->customer_details();
    }
    
    function update_customer()
    {
        $model = $this->getModel("master");
        $model->update_customer();
    }
    
    function update_customers_category()
    {
        $model = $this->getModel("master");
        $model->update_customers_category();
    }
    
    function send_sms_to_customers()
    {
        $model = $this->getModel("master");
        $model->send_sms_to_customers();
    }
    
    function save_collection_remarks()
    {
        $model = $this->getModel('master');
        $model->save_collection_remarks();    
    }    
    
    function delete_customer()
    {
        $model = $this->getModel("master");
        $model->delete_customer();
    }
    
    function save_bank()
    {
        $model = $this->getModel('master');
        $model->save_bank();     
    }
    
    function bank_details()
    {
        $model = $this->getModel('master');
        $model->bank_details();     
    }
    
    function update_bank()
    {
        $model=$this->getModel('master');
        $model->update_bank();    
    }
    
    function delete_bank()
    {
        $model=$this->getModel('master');
        $msg = $model->delete_bank();
        $link = "index.php?option=com_master&view=manage_banks" ;
        $this->setRedirect($link, $msg);    
    }
    
    function save_customer_category()
    {
        $model = $this->getModel('master');
        $model->save_customer_category();
    }
    
    function customer_category_details()
    {
        $model = $this->getModel('master');
        $model->customer_category_details();
    }
    
    function update_customer_category()
    {
        $model = $this->getModel('master');
        $model->update_customer_category();    
    }
    
    function change_customer_account_status()
    {
        $model = $this->getModel('master');
        $model->change_customer_account_status();    
    }
    
    function delete_customer_category()
    {
        $model = $this->getModel('master');
        $msg = $model->delete_customer_category();  
        $url = "index.php?option=com_master&view=manage_customer_categories";
        $this->setRedirect($url,$msg);  
    }
    
   /* function save_transporter()
    {
        $model = $this->getModel('master');
        $model->save_transporter();
    }
    
    function transporter_details()
    {
        $model = $this->getModel('master');
        $model->transporter_details();
    }
    
    function update_transporter()
    {
        $model = $this->getModel('master');
        $model->update_transporter();    
    }
    
    function delete_transporter()
    {
        $model = $this->getModel('master');
        $msg = $model->delete_transporter();  
        $url = "index.php?option=com_master&view=manage_transporters";
        $this->setRedirect($url,$msg);  
    }  */
    
    function save_bank_account()
    {
        $model = $this->getModel('master');
        $model->save_bank_account();
    }
    
    function bank_account_details()
    {
        $model = $this->getModel('master');
        $model->bank_account_details();
    }
    
    function update_bank_account()
    {
        $model = $this->getModel('master');
        $model->update_bank_account();    
    }
    
    function change_bank_account_status()
    {
        $model = $this->getModel('master');
        $model->change_bank_account_status();    
    }
    
    function save_note()
    {
        $model = $this->getModel('master');
        $model->save_note();
    }
    
    function delete_note()
    {
        $model = $this->getModel('master');
        $msg = $model->delete_note();  
        $url = "index.php?option=com_master&view=manage_notes";
        $this->setRedirect($url,$msg);  
    }
    
    function merge_items()
    {
        $model = $this->getModel("master");
        $model->merge_items();
    }
    
    //customer_segments
    function save_customer_segment()
    {
        $model = $this->getModel('master');
        $model->save_customer_segment();
    }
    
    function customer_segment_details()
    {
        $model = $this->getModel('master');
        $model->customer_segment_details();
    }
    
    function update_customer_segment()
    {
        $model = $this->getModel('master');
        $model->update_customer_segment();    
    }
    
    function delete_customer_segment()
    {
        $model = $this->getModel('master');
        $msg = $model->delete_customer_segment();  
        $url = "index.php?option=com_master&view=manage_customer_segments";
        $this->setRedirect($url,$msg);  
    }
    
    function state_details()
    {
        $model = $this->getModel('master');
        $model->state_details();
    }
    
    
    function save_unit()
    {
        $model = $this->getModel('master');
        $model->save_unit();
    }
    
    function update_unit()
    {
        $model = $this->getModel('master');
        $model->update_unit();    
    }
    
     function unit_details()
    {
        $model = $this->getModel('master');
        $model->unit_details();
    }
    
    function delete_unit()
    {
        $model = $this->getModel('master');
        $model->delete_unit(); 
        $url = "index.php?option=com_master&view=manage_units";
        $this->setRedirect($url);    
    } 
}
?>