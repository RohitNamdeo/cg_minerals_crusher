<?php
    defined('_JEXEC') or die; 
    $total_account_balance = 0;
?>
<style>
    #footer_div {
        position: fixed;
        left: 80px;
        bottom : 1px;
        height: 40px;
        background-color: white;
        padding: 6px;
        border: 1px solid lightgray;
        text-align: center; -webkit-border-radius: 6px; -moz-border-radius: 6px; border-radius: 6px;
    }
</style>
<script>
    j(function(){         
        j( "#transporters" ).dialog({
            autoOpen: false,
            height: 150,
            width: 390,
            modal: true,
            buttons: 
            {
                "Submit": function() 
                {    
                    if(j("#transporter").val() == "")
                    {
                        alert("Please fill transporter.");
                        return false;
                    }
                    
                    j('#transporters').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_transporter&tmpl=xml&" + j("#transporterForm").serialize() + "&transporter_id=" + j("#transporter_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#transporters').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#transporters").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_transporter&tmpl=xml&" + j("#transporterForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#transporters').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#transporters").dialog( "close" );
                                go(window.location); 
                            }
                        });
                    }
                },
                Cancel: function() {
                     j( this ).dialog( "close" );
                }
            },
        });
        
        j("#transporterList").find("tr.transporter").first().addClass("clickedRow").click();   

    });
        
    var isTableActive = false;

    // every time that there's a click, detect if it was on the table or outside of it
    j(document).on("click", function(e) {
        isTableActive = j.contains(document.getElementById("transporterList"), e.target);
    });

    j(document).on("click", ".transporter", function(){
        j(this).closest("table").find(".clickedRow").removeClass('clickedRow');
        j(this).addClass('clickedRow');
    });

    j(document).on("keydown", function(e) {
        switch(e.keyCode) {
            case 38: // up
                if (j("#transporterList tr.clickedRow").prev().length) {
                    j("#transporterList tr.clickedRow").removeClass("clickedRow").prev().addClass("clickedRow");
                }
                break;
            case 40: // down
                if (j("#transporterList tr.clickedRow").next().length) {
                    j("#transporterList tr.clickedRow").removeClass("clickedRow").next().addClass("clickedRow");
                }
                break;
            case 13: // enter
                var transporter_id = j("#transporterList tr.clickedRow").attr("transporter_id");
                show_transporter_account(transporter_id);
                break;
        }
        
        //j("#transporterList tbody tr.clickedRow")[0].scrollIntoView();
        if (j("#transporterList tbody tr.clickedRow").offset().top < window.scrollY)
        {
            j("html, body").scrollTop(j("#transporterList tbody tr.clickedRow").offset().top);
        }else if (j("#transporterList tbody tr.clickedRow").offset().top + j("#transporterList tbody tr.clickedRow").height() > window.scrollY + (window.innerHeight || document.documentElement.clientHeight) - 50)
        {
            j("html, body").scrollTop(j("#transporterList tbody tr.clickedRow").offset().top + j("#transporterList tbody tr.clickedRow").height() - (window.innerHeight || document.documentElement.clientHeight) + 50);
        }
         
        //var scrolltop_value =  parseFloat(j("#transporterList tbody tr.clickedRow").offset().top.toFixed(2)) + parseFloat(j("#transporterList tbody").scrollTop().toFixed(2)) - parseFloat(j("#transporterList tbody").offset().top.toFixed(2));
        //j(window).scrollTop(scrolltop_value.toFixed(2));

        //if (isTableActive)
        if (e.keyCode != 70 && e.keyCode != 127 && e.keyCode != 17)
        {
            e.preventDefault();
            return false;
        }
    }); 

    function add_transporter()
    {   
        j("#mode").val("");
        j("#transporter_id").val("");
        j("#transporter").val("");
        
        j("#transporters").dialog("open");
        j("#transporters").dialog({"title":"Add Transporter"});
        j('#transporters').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_transporter(transporter_id)
    {   
        j("#transporter_id").val(transporter_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=transporter_details&tmpl=xml&transporter_id=" + transporter_id, function(data){
            transporter_details = j.parseJSON(data);
            
            j("#transporter").val(transporter_details.transporter);  
        });
                
        j("#transporters").dialog("open");
        j("#transporters").dialog({"title":"Edit Transporter"});
        j('#transporters').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
     
    function delete_transporter(transporter_id)
    {
         if(confirm("Are You Sure?"))
         {
            go("index.php?option=com_master&task=delete_transporter&transporter_id=" + transporter_id);
         }
         else
         {
            return false;
         }
    }
    
    function show_transporter_account(transporter_id)
    {
        window.open('index.php?option=com_amittrading&view=transporter_account&transporter_id=' + transporter_id, "transporter_account" + transporter_id, "height=" + screen.height + ", width=" + screen.width).focus();
    }
</script>
<h1>Transporters</h1>
<input type="button" value="Add Transporter" onclick="add_transporter();">
<br /><br />
<div id="transporterslist">
    <table id="transporterList" class="clean">
        <tr>
            <th width="20">S.No.</th>
            <th><a href="index.php?option=com_master&view=manage_transporters&so=<? echo base64_encode("transporter"); ?>" <? echo ($this->sort_order == "transporter" ? "style='color:green;'" : ""); ?>>Transporter</a></th>
            <th><a href="index.php?option=com_master&view=manage_transporters&so=<? echo base64_encode("account_balance"); ?>" <? echo ($this->sort_order == "account_balance" ? "style='color:green;'" : ""); ?>>Account Balance</a></th>
            <?
                /*if(is_admin())
                {
                    ?><th>Action</th><?
                }*/
            ?>
        </tr>
        <?  
            if(count($this->transporters) > 0)
            {
                $x = 0;
                foreach($this->transporters as $transporter)
                {
                    $total_account_balance += $transporter->account_balance;
                    ?>
                    <tr style="cursor:pointer;" ondblclick="show_transporter_account(<? echo $transporter->id;?>);" transporter_id="<? echo $transporter->id;?>" class="transporter">
                        <td align="center"><? echo ++$x; ?></td>
                        <td><a href="index.php?option=com_amittrading&view=transporter_account&transporter_id=<? echo $transporter->id; ?>"><? echo $transporter->transporter; ?></a></td>
                        <td align="right"><? echo round_2dp($transporter->account_balance); ?></td>
                        <?
                            /*if(is_admin())
                            {
                                ?>    
                                <td align="center">
                                    <a onclick="edit_transporter(<? echo $transporter->id; ?>)"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                    <img src="custom/graphics/icons/blank.gif" id="delete" title="Delete" onclick="delete_transporter(<? echo $transporter->id; ?>);" class="delete">
                                </td>
                                <?
                            }*/
                        ?> 
                    </tr>
                    <?
                }
            }
        ?>
             
    </table>
</div>
<div id="footer_div">
    <b>Total Outstanding Amount : <? echo round_2dp($total_account_balance); ?>/-</b>
</div>

<input type="hidden" name="mode" id="mode" value="" /> 
<input type="hidden" name="transporter_id" id="transporter_id" value="" /> 

<div style="display: none;" id="transporters">
    <form method="post" id="transporterForm">
        <table class="">
            <tr>
                <td>Transporter :</td>
                <td><input type="text" id="transporter" name="transporter" size="27" />
            </tr>
        </table>
    </form>
</div>