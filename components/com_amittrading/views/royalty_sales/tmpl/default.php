<?php
    defined('_JEXEC') or die;
    
?>
<style >
    tr > td
    {
        text-align: center;
    }
</style>

<script>
    j(function(){
        j("#date").datepicker({"dateFormat" : "dd-M-yy", changeMonth: true, changeYear: true});
        
        <? 
            if($this->booklet_id == 0)
            {
                ?>
                    j("#submit").hide();    
                    j("#reset").hide();    
                <?
            }
        ?>
    }); 
    
    
    /*function get_royalt_no(booklet_id)
    {
        if( (!isNaN(booklet_id) && booklet_id!= "") )
        {
            j.get("index.php?option=com_amittrading&task=get_royalty_no&tmpl=xml&royalty_id=" + booklet_id, function(data){
                window.booklets = j.parseJSON(data);
                var html = "<option value=0></option>";
                j.each(booklets, function(i, data){
                    html += "<option value=" + data.rb_no + ">" + data.rb_no + "</option>";
                });
                j("#from").html(html);
                j("#to").html(html);
            });
        }   
    }*/
    
    function form_reset()
    {
        go("index.php?option=com_amittrading&view=royalty_sales");
    }
    
    function validateForm()
    {
        
        if(j("#customer_id").val() == 0)
        {
            alert("Select customer name.");
            return false;
        }
        
        j("#royalty_sales").submit();
    } 
   
    
</script>
<h1>Royalty Sales </h1>
<br />                                                                                                                                                                        
<div id="royalty_sales_form">
    <form id="royalty_sales" method="post" action="index.php?option=com_amittrading&task=save_royalty_sales">
        <input type="hidden" name="all_booklet_no_id" id="all_booklet_no_id" value="<? echo $this->pages; ?>">
        <table class="clean">
            <tr>
                <td> Customer Name :</td>
                <td>
                    <select name="customer_id" id="customer_id" style="width:200px;">
                        <option value="0"></option>
                        <?
                            if(count($this->customers) > 0 )
                            {
                                foreach($this->customers as $customer)
                                {
                                    ?>
                                        <option value="<? echo $customer->id ;?>"><? echo $customer->customer_name ;?></option>
                                    <?    
                                }
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td> Date :</td>
                <td><input type="text" id="date" name="date" value="<? echo date("d-M-Y"); ?>" readonly="readonly" style="width:200px;"/></td>
            </tr>
            <tr>
                <td> Royalty Booklets :</td>
                <td>
                    <? echo ($this->booklet_name != "" ? $this->booklet_name : ""); ?>
                    <input type="hidden" name="booklet_id" value="<? echo ($this->booklet_id != "" ? $this->booklet_id : ""); ?>">
                </td>
            </tr>
            <tr>
                <td> Booklet Pages : </td>
                <td>
                    From
                     <input type="text" name="from_booklet_no" style="width: 72px;" value="<? echo ($this->min != "" ? $this->min : "");?>" readonly="readonly">
                    
                    To
                    <input type="text" name="to_booklet_no" style="width: 72px;" value="<? echo ($this->max != "" ? $this->max : "");?>" readonly="readonly">
                </td>
            </tr>
            <tr>
                <td>Total Pages :</td>
                <td><input type="text" name="total_pages" id="total_pages" style="width:200px;" value="<? echo ($this->total_pages != "" ? $this->total_pages : "");?>" readonly="readonly"/></td>
            </tr>
            <tr>
                <td>Amount :</td>
                <? $total_amount = floatval($this->total_pages) * (floatval($this->quantity) * floatval($this->rate)); ?>
                <td><input type="text" name="amount" id="amount" style="width:200px;" value=" <? echo ($total_amount > 0 ? $total_amount : "") ;?>" readonly="readonly"/>  </td>
            </tr>
            <tr>
                <td>Comments : </td>
                <td><input type="text" name="comments" id="comments" style="width:200px;"/></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="submit" id="submit" value="Submit" onclick="return validateForm();">
                    <input type="reset" name="reset" id="reset" value="Reset" onclick="form_reset();">
                </td>
            </tr>
            
        </table>
    </form>
</div>