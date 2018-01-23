<?
    defined('_JEXEC') or die;
?>
<style>
    #royalty_items {
        padding: 20px;
    }
    
    tr.border_bottom td {
      border-bottom:1px solid black;
    }
</style>

<script >

    /*j(function(){
        
        j("#sale").click(function(){
            var booklet_id = j("#hidden").val();
            //show_items(booklet_id);
        });

    });*/ 
    
    
    /*j(document).on("click", "#select_all", function(){
        
        var select_all = document.getElementById("select_all"); 
        var checkboxes = document.getElementsByClassName("checkbox"); 

        select_all.addEventListener("change", function(e){
            for (i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = select_all.checked;
            }
        });

        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].addEventListener('change', function(e){ 
                if(this.checked == false){
                    select_all.checked = false;
                }
                if(document.querySelectorAll('.checkbox:checked').length == checkboxes.length){
                    select_all.checked = true;
                    
                }
            });
        }
          
    });*/
    
    j(document).on("change", "#select_all", function(){
        if(j(this).is(":checked"))
        {
            j(".checkbox").attr("checked", true);
            
        }
        else
        {
            j(".checkbox").attr("checked", false);
        }
    });
    
    j(document).on("change", ".checkbox", function(){
        if(j(".checkbox:checked").length == j(".checkbox").length)
        {
            j("#check_all").attr("checked", true);
        }
        else
        {
            j("#check_all").attr("checked", false);
        }
    });
    
    /*function show_items(booklet_id)
    {
        alert();
    }*/
    
    function validateForm()
    {
        
        if(j(".checkbox:checked").length)
        {
            j("#royalty_sale").submit();
        }
        else
        {
            alert("At least 1 item should be selected for royalty sale.");
            return false;
        }
    }
    
        
</script>
 
<div style="width:100%;" id="royalty_items">
    <h1>Royalty Booklet Numbers</h1>
    <form id="royalty_sale" action="index.php?option=com_amittrading&view=royalty_sales" method="post">
        <table class="clean">
            <tr>
                <th><input type="checkbox" id="select_all"></th>
                <th>#</th>
                <th>Number</th>
                <th>Used in sales invoice</th>
                <th>Date</th>
                <th>Customer</th>
            </tr>
            <?
                $x = 0;
                $rn = $this->royalty_numbers;
                $tsp = $this->total_sale_pages;
                foreach($this->pages as $key => $number)
                {
                    //print_r($number) ;
                    if(array_key_exists($key,$rn) || array_key_exists($key,$tsp))
                    {
                    ?>
                        <tr>
                            <td>
                                <?
                                    if($number->used == 0)
                                    {
                                        ?>
                                            <input type="checkbox" name="checkbox[]" class="checkbox" value="<? echo $number->id;?>">
                                        <?
                                    }
                                ?>
                            </td>
                            
                            <td><? echo ++$x;?></td>
                            <td><? echo $number->rb_no; ?></td>
                            <td><? echo (array_key_exists($key,$rn) ? ($rn[$key]->bill_no > 0 ? "CG " . $rn[$key]->bill_no : "") : "Sold"); ?></td>
                            <td><? echo (array_key_exists($key,$rn) ? ($rn[$key]->date != null ? date("d-M-Y", strtotime($rn[$key]->date)) : "") : "") . (array_key_exists($key,$tsp) ? ($tsp[$key]->sale_date != null ? date("d-M-Y", strtotime($tsp[$key]->sale_date)) : "") : ""); ?></td>
                            <td><? echo (array_key_exists($key,$rn) ? ($rn[$key]->customer_name != "" ? $rn[$key]->customer_name : "") : "") . (array_key_exists($key,$tsp) ? ($tsp[$key]->sale_customer_name != "" ? $tsp[$key]->sale_customer_name : "") : ""); ?></td>
                            <input type="hidden" name="hidden" id="hidden" value="<? echo $number->booklet_id; ?>"/>
                        </tr>
                    <?
                    }
                    else
                    {
                    ?>
                        <tr>
                            <td>
                                <?
                                    if($number->used == 0)
                                    {
                                        ?>
                                            <input type="checkbox" name="checkbox[]" class="checkbox" value="<? echo $number->id;?>">
                                        <?
                                    }
                                ?>
                            </td>
                            
                            <td><? echo ++$x;?></td>
                            <td><? echo $number->rb_no; ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <input type="hidden" name="hidden" id="hidden" value="<? echo $number->booklet_id; ?>"/>
                        </tr>
                    <?   
                    }
                }
            ?>
            <tr>
                <td colspan="6">
                    <input type="button" name="sale" id="sale" value="Sale" onclick="validateForm(); return false;" style="float:right; "/>
                </td>
            </tr> 
        </table>
    </form>    
    
    
    
</div>    