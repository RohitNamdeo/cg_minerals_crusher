<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class AmittradingModelAmittrading3 extends JModelItem
{
    function get_items_in_stock()
    {
        // function to get list of items of particular category with their stock in selected location
        $db = JFactory::getDBO();
        
        $location_id = intval(JRequest::getVar("location_id"));
        $category_id = intval(JRequest::getVar("category_id"));
        $item_id = intval(JRequest::getVar("item_id"));
        $pack = floatval(JRequest::getVar("pack"));
        
        $query = "select id, item_name, last_purchase_rate, gst_percent, piece_per_pack, sale_price1, sale_price2 from `#__items` where category_id=" . $category_id . " order by item_name";
        $db->setQuery($query);
        $items = $db->loadObjectList();
        
        $query = "select stock, item_id from `#__inventory_items` where location_id=" . $location_id . " and stock>0";
        $db->setQuery($query);
        $inventory_data = $db->loadObjectList("item_id"); 
        
        $html = "";
        
        $html .= "<select class='item_id' name='item_id[]' style='width:140px;'>";
        $html .= "<option value='0' style='text-align:left;'></option>";
        
        if(count($items) > 0)
        {
            foreach($items as $item)
            {
                if($item->id == $item_id)
                {
                    if(isset($inventory_data[$item->id]))
                    { $inventory_data[$item->id]->stock += $pack; }
                    else
                    {
                        $inventory_data[$item->id] = new stdClass();
                        $inventory_data[$item->id]->stock = $pack;
                    }
                }
                
                if(floatval(@$inventory_data[$item->id]->stock) > 0)
                { $html .= "<option value='" . $item->id . "' last_purchase_rate='" . $item->last_purchase_rate . "' piece_per_pack='" . $item->piece_per_pack . "' sale_price1='" . $item->sale_price1 . "' sale_price2='" . $item->sale_price2 . "' stock='" . (isset($inventory_data[$item->id]) ? floatval($inventory_data[$item->id]->stock) : 0) . "' gst_percent='" . floatval($item->gst_percent) . "' style='text-align:left;'>" . $item->item_name . "</option>"; }
            }
        }
        
        $html .= "</select>";
        
        echo $html;
    }
    
    function save_stock_transfer_item()
    {
        // function to insert a new item in stock transfer list 
        // row html is created to prepend it to the existing table
        
        $db = JFactory::getDbo();
        
        $category_id = intval(JRequest::getVar("category_id"));
        $item_id = intval(JRequest::getVar("item_id"));
        $pack = floatval(JRequest::getVar("pack"));
        
        $stock_transfer = new stdClass();
        
        $stock_transfer->category_id = $category_id;
        $stock_transfer->item_id = $item_id;
        $stock_transfer->pack = $pack;
        
        if($db->insertObject("#__stock_transfer_list", $stock_transfer, ""))
        {
            $stock_transfer_list_id = intval($db->insertid());
            
            $query = "select concat(c.category_name, '-', i.item_name) item_to_transfer, c.category_name, i.item_name from `#__category_list` c inner join `#__items` i on c.id=i.category_id where i.id=" . $item_id;
            $db->setQuery($query);
            $item_details = $db->loadObject();
            
            Functions::log_activity("Item '" . $item_details->item_to_transfer . "' has been added to stock transfer list.", "STL", $stock_transfer_list_id);
            
            $data = "<tr id='item_" . $stock_transfer_list_id . "'>";
            $data .= "<td align='center'><input type='checkbox' class='transfer_checkbox' name='stl_ids[]' value='" . $stock_transfer_list_id . "'></td>";
            $data .= "<td>" . $item_details->category_name . "</td>";
            $data .= "<td>" . $item_details->item_name . "</td>";
            $data .= "<td align='right'>" . $pack . "<input type='hidden' id='item_name" . $stock_transfer_list_id . "' value='" . $item_details->item_to_transfer . "'></td>";
            $data .= "<td align='center'><a href='#' onclick='delete_stock_transfer_item(" . $stock_transfer_list_id . "); return false;'><img src='custom/graphics/icons/blank.gif' class='delete' title='Remove'></a></td>";
            $data .= "</tr>";
            
            $response = array("success"=>true, "data"=>$data);
        }
        else
        {
            $response = array("success"=>false, "data"=>"");
        }
        
        echo json_encode($response);
    }
    
    function delete_stock_transfer_item()
    {
        // function to delete an item from stock transfer list 
        $db = JFactory::getDbo();
        
        $stock_transfer_list_id = intval(JRequest::getVar("list_id"));
        
        $query = "select concat(c.category_name, '-', i.item_name) item_to_delete from `#__stock_transfer_list` stl inner join `#__category_list` c on stl.category_id=c.id inner join `#__items` i on stl.item_id=i.id where stl.id=" . $stock_transfer_list_id;
        $db->setQuery($query);
        $item_to_delete = $db->loadResult();
        
        $query = "delete from `#__stock_transfer_list` where id=" . $stock_transfer_list_id;
        $db->setQuery($query);
        if($db->query())
        {
            Functions::log_activity("Item '" . $item_to_delete . "' has been deleted from stock transfer list.", "STL", $stock_transfer_list_id);
            echo "ok";
        }
    }
    
    function save_stock_transfer()
    {
        /*
        * stock of item is decreased for from location and increased for to location
        * if the stock transfer is created via stock transfer list then those items are deleted from stock transfer list
        */
        
        $db = JFactory::getDbo();
        
        $location_from_id = intval(JRequest::getVar("location_from_id"));
        $location_to_id = intval(JRequest::getVar("location_to_id"));
        $st_date = date("Y-m-d", strtotime(JRequest::getVar("st_date")));
        $issued_by = JRequest::getVar("issued_by");
        $transferred_by = JRequest::getVar("transferred_by");
        $received_by = JRequest::getVar("received_by");
        $pending_stock_transfer = JRequest::getVar("pending_stock_transfer");
        //$total_amount = floatval(JRequest::getVar("total_amount"));
        $remarks = JRequest::getVar("remarks");
        
        $stl_ids = JRequest::getVar("stl_ids");
        $category_id = JRequest::getVar("category_id");
        $item_id = JRequest::getVar("item_id");
        $description = JRequest::getVar("description");
        $pack = JRequest::getVar("pack");
        $quantity = JRequest::getVar("quantity");
        //$unit_rate = JRequest::getVar("unit_rate");
        //$item_amount = JRequest::getVar("item_amount");
        
        $stock_transfer = new stdClass();
        
        $stock_transfer->st_date = $st_date;
        $stock_transfer->location_from_id = $location_from_id;
        $stock_transfer->location_to_id = $location_to_id;
        $stock_transfer->issued_by = $issued_by;
        $stock_transfer->transferred_by = $transferred_by;
        $stock_transfer->received_by = $received_by;
        //$stock_transfer->total_amount = $total_amount;
        $stock_transfer->status = $pending_stock_transfer;
        $stock_transfer->remarks = $remarks;
        $stock_transfer->creation_date = date("Y-m-d H:i:s");
        
        $db->insertobject("#__stock_transfer", $stock_transfer, "");
        $stock_transfer_id = intval($db->insertid());
        
        for($i=0;$i<count($category_id);$i++)
        {
            $stock_transfer_item = new stdClass();
        
            $stock_transfer_item->stock_transfer_id = $stock_transfer_id;
            $stock_transfer_item->category_id = $category_id[$i];
            $stock_transfer_item->item_id = $item_id[$i];
            $stock_transfer_item->description = $description[$i];
            $stock_transfer_item->pack = $pack[$i];
            $stock_transfer_item->quantity = $quantity[$i];
            //$stock_transfer_item->unit_rate = $unit_rate[$i];
            //$stock_transfer_item->amount = $item_amount[$i];
            
            $db->insertObject("#__stock_transfer_items", $stock_transfer_item, "");
            
            $query = "select count(id) from `#__inventory_items` where item_id=" . intval($item_id[$i]) . " and location_id=" . $location_from_id;
            $db->setQuery($query);
            if(intval($db->loadResult()) > 0)
            {
                $query = "update `#__inventory_items` set `stock`=stock-" . floatval($pack[$i]) . " where item_id=" . intval($item_id[$i]) . " and location_id=" . $location_from_id;
                $db->setQuery($query);
                $db->query();
            }
            else
            {
                $stock = floatval($pack[$i]) * -1;
                $query = "insert into `#__inventory_items` (`item_id`,`location_id`,`stock`) values(" . intval($item_id[$i]) . "," . $location_from_id . "," . $stock . ")";
                $db->setQuery($query);
                $db->query();
            }
            
            $query = "select count(id) from `#__inventory_items` where item_id=" . intval($item_id[$i]) . " and location_id=" . $location_to_id;
            $db->setQuery($query);
            if(intval($db->loadResult()) > 0)
            {
                $query = "update `#__inventory_items` set `stock`=stock+" . floatval($pack[$i]) . " where item_id=" . intval($item_id[$i]) . " and location_id=" . $location_to_id;
                $db->setQuery($query);
                $db->query();
            }
            else
            {
                $stock = floatval($pack[$i]);
                $query = "insert into `#__inventory_items` (`item_id`,`location_id`,`stock`) values(" . $item_id[$i] . "," . $location_to_id . "," . $stock . ")";
                $db->setQuery($query);
                $db->query();
            }
        }
        
        if(count($stl_ids) > 0)
        {
            $this->update_stock_transfer_list($stl_ids, $stock_transfer_id);
        }
        
        $query = "select `location_name` from `#__inventory_locations` where id=" . $location_from_id;
        $db->setQuery($query);
        $location_from_name = $db->loadResult();
        
        $query = "select `location_name` from `#__inventory_locations` where id=" . $location_to_id;
        $db->setQuery($query);
        $location_to_name = $db->loadResult();
        
        Functions::log_activity("Stock transfer from " . $location_from_name . " to " . $location_to_name . " has been added.", "ST", $stock_transfer_id);
        //return "Stock transfer saved successfully.";   
        return $stock_transfer_id;
    }
    
    function update_stock_transfer_list($stl_ids, $stock_transfer_id)
    {
        $db = JFactory::getDbo();
        
        $condition = "";
                
        foreach($stl_ids as $stl_id)
        {
            if($stl_id > 0)
            { $condition .= ($condition != "" ? " or " : "") . "(id=" . $stl_id . ")"; }
        }
        
        if($condition != "")
        {
            $condition = "(" . $condition . ")";
            
            $query = "delete from `#__stock_transfer_list` where " . $condition;
            $db->setQuery($query);
            $db->query();
            
            Functions::log_activity("Stock transfer list has been updated after stock transfer voucher creation.", "ST-STL", $stock_transfer_id);
        }
        
        return;
    }
    
    function update_stock_transfer()
    {
        // stock of original items and location is reverted, rest is same as add
        // no role of stock transfer list
        
        $db = JFactory::getDbo();
        
        $stock_transfer_id = intval(JRequest::getVar("transfer_id"));
        
        $location_from_id = intval(JRequest::getVar("location_from_id"));
        $location_to_id = intval(JRequest::getVar("location_to_id"));
        $st_date = date("Y-m-d", strtotime(JRequest::getVar("st_date")));
        $issued_by = JRequest::getVar("issued_by");
        $transferred_by = JRequest::getVar("transferred_by");
        $received_by = JRequest::getVar("received_by");
        $pending_stock_transfer = JRequest::getVar("pending_stock_transfer");        
        //$total_amount = floatval(JRequest::getVar("total_amount"));
        $remarks = JRequest::getVar("remarks");
        
        $category_id = JRequest::getVar("category_id");
        $item_id = JRequest::getVar("item_id");
        $description = JRequest::getVar("description");
        $pack = JRequest::getVar("pack");
        $quantity = JRequest::getVar("quantity");
        //$unit_rate = JRequest::getVar("unit_rate");
        //$item_amount = JRequest::getVar("item_amount");
        
        $original_location_from_id = intval(JRequest::getVar("original_location_from_id"));
        $original_location_to_id = intval(JRequest::getVar("original_location_to_id"));
        
        $stock_transfer = new stdClass();
        
        $stock_transfer->id = $stock_transfer_id;
        $stock_transfer->st_date = $st_date;
        $stock_transfer->location_from_id = $location_from_id;
        $stock_transfer->location_to_id = $location_to_id;
        $stock_transfer->issued_by = $issued_by;
        $stock_transfer->transferred_by = $transferred_by;
        $stock_transfer->received_by = $received_by;
        $stock_transfer->status = $pending_stock_transfer;
        //$stock_transfer->total_amount = $total_amount;
        $stock_transfer->remarks = $remarks;
        
        $db->updateobject("#__stock_transfer", $stock_transfer, "id");
        
        $query = "select item_id, pack, quantity from `#__stock_transfer_items` where stock_transfer_id=" . $stock_transfer_id;
        $db->setQuery($query);
        $original_items = $db->loadObjectList();
        
        $query = "delete from `#__stock_transfer_items` where stock_transfer_id=" . $stock_transfer_id;
        $db->setQuery($query);
        $db->query();
        
        for($i=0;$i<count($category_id);$i++)
        {
            $stock_transfer_item = new stdClass();
        
            $stock_transfer_item->stock_transfer_id = $stock_transfer_id;
            $stock_transfer_item->category_id = $category_id[$i];
            $stock_transfer_item->item_id = $item_id[$i];
            $stock_transfer_item->description = $description[$i];
            $stock_transfer_item->pack = $pack[$i];
            $stock_transfer_item->quantity = $quantity[$i];
            //$stock_transfer_item->unit_rate = $unit_rate[$i];
            //$stock_transfer_item->amount = $item_amount[$i];
            
            $db->insertObject("#__stock_transfer_items", $stock_transfer_item, "");
            
            $query = "select count(id) from `#__inventory_items` where item_id=" . intval($item_id[$i]) . " and location_id=" . $location_from_id;
            $db->setQuery($query);
            if(intval($db->loadResult()) > 0)
            {
                $query = "update `#__inventory_items` set `stock`=stock-" . floatval($pack[$i]) . " where item_id=" . intval($item_id[$i]) . " and location_id=" . $location_from_id;
                $db->setQuery($query);
                $db->query();
            }
            else
            {
                $stock = floatval($pack[$i]) * -1;
                $query = "insert into `#__inventory_items` (`item_id`,`location_id`,`stock`) values(" . $item_id[$i] . "," . $location_from_id . "," . $stock . ")";
                $db->setQuery($query);
                $db->query();
            }
            
            $query = "select count(id) from `#__inventory_items` where item_id=" . intval($item_id[$i]) . " and location_id=" . $location_to_id;
            $db->setQuery($query);
            if(intval($db->loadResult()) > 0)
            {
                $query = "update `#__inventory_items` set `stock`=stock+" . floatval($pack[$i]) . " where item_id=" . intval($item_id[$i]) . " and location_id=" . $location_to_id;
                $db->setQuery($query);
                $db->query();
            }
            else
            {
                $stock = floatval($pack[$i]);
                $query = "insert into `#__inventory_items` (`item_id`,`location_id`,`stock`) values(" . $item_id[$i] . "," . $location_to_id . "," . $stock . ")";
                $db->setQuery($query);
                $db->query();
            }
        }
        
        foreach($original_items as $item)
        {
            $query = "update `#__inventory_items` set `stock`=stock+" . floatval($item->pack) . " where item_id=" . intval($item->item_id) . " and location_id=" . $original_location_from_id;
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__inventory_items` set `stock`=stock-" . floatval($item->pack) . " where item_id=" . intval($item->item_id) . " and location_id=" . $original_location_to_id;
            $db->setQuery($query);
            $db->query();
        }
        
        $query = "select `location_name` from `#__inventory_locations` where id=" . $location_from_id;
        $db->setQuery($query);
        $location_from_name = $db->loadResult();
        
        $query = "select `location_name` from `#__inventory_locations` where id=" . $location_to_id;
        $db->setQuery($query);
        $location_to_name = $db->loadResult();
        
        Functions::log_activity("Stock transfer from " . $location_from_name . " to " . $location_to_name . " has been updated.", "ST", $stock_transfer_id);
        //return "Stock transfer updated successfully.";   
        return $stock_transfer_id;
    }
    
    function change_transfer_status()
    {
        $db = JFactory::getDbo();

        $stock_transfer_id = intval(JRequest::getVar("transfer_id"));
        $status = intval(JRequest::getVar("status"));
        
        $query = "update `#__stock_transfer` set `status`=" . $status . " where id= " . $stock_transfer_id;
        $db->setQuery($query);
        $db->query();
        
        if($status == 1)
        {
            echo "Pending";
        }
        else if($status == 2)
        {
            echo "Transferred";
        }
    }
    
    function delete_stock_transfer()
    {
        // stock of items and location is reverted
        // no role of stock transfer list
        
        $db = JFactory::getDbo();
        
        $stock_transfer_id = intval(JRequest::getVar("transfer_id"));
        
        $query = "select st.location_from_id, st.location_to_id, l.location_name location_from_name, loc.location_name location_to_name from `#__stock_transfer` st inner join `#__inventory_locations` l on st.location_from_id=l.id inner join `#__inventory_locations` loc on st.location_to_id=loc.id where st.id=" . $stock_transfer_id;
        $db->setQuery($query);
        $stock_transfer = $db->loadObject();
        
        $query = "select item_id, pack, quantity from `#__stock_transfer_items` where stock_transfer_id=" . $stock_transfer_id;
        $db->setQuery($query);
        $original_items = $db->loadObjectList();
        
        $query = "delete from `#__stock_transfer` where id=" . $stock_transfer_id;
        $db->setQuery($query);
        $db->query();
        
        $query = "delete from `#__stock_transfer_items` where stock_transfer_id=" . $stock_transfer_id;
        $db->setQuery($query);
        $db->query();
        
        foreach($original_items as $item)
        {
            $query = "update `#__inventory_items` set `stock`=stock+" . floatval($item->pack) . " where item_id=" . intval($item->item_id) . " and location_id=" . intval($stock_transfer->location_from_id);
            $db->setQuery($query);
            $db->query();
            
            $query = "update `#__inventory_items` set `stock`=stock-" . floatval($item->pack) . " where item_id=" . intval($item->item_id) . " and location_id=" . intval($stock_transfer->location_to_id);
            $db->setQuery($query);
            $db->query();
        }
        
        Functions::log_activity("Stock transfer from " . $stock_transfer->location_from_name . " to " . $stock_transfer->location_to_name . " has been deleted.", "ST", $stock_transfer_id);
        return "Stock transfer deleted successfully.";   
    } 
}
?>