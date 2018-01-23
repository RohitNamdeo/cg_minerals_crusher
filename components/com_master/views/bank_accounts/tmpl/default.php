<?php
    defined('_JEXEC') or die; 
?>
<script>
    j(function(){   
        j( "#bank_accounts" ).dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            modal: true,
            buttons: 
            {
                "Submit": function() 
                {    
                    if(j("#account_name").val() == "")
                    {
                        alert("Please fill account name.");
                        return false;
                    }
                    
                    if(j("#bank_name").val() == "")
                    {
                        alert("Please fill bank name.");
                        return false;
                    }
                    
                    if(j("#branch").val() == "")
                    {
                        alert("Please fill branch.");
                        return false;
                    }
                    
                    if(j("#account_no").val() == "")
                    {
                        alert("Please fill account no.");
                        return false;
                    }
                    
                    if(j("#account_type").val() == 0)
                    {
                        alert("Please select account tyoe.");
                        return false;
                    }
                    
                    if(j("#ifsc_code").val() == "")
                    {
                        alert("Please fill IFSC code.");
                        return false;
                    }
                    if(j("#ifsc_code").val().length != 11)
                    {
                        alert("Please fill correct 11 digit IFSC code.");
                        return false;
                    }
                    
                    j('#bank_accounts').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", true).addClass("ui-state-disabled");
                    
                    if(j("#mode").val() == "e")
                    {
                       j.get("index.php?option=com_master&task=update_bank_account&tmpl=xml&" + j("#bankAccountForm").serialize() + "&bank_account_id=" + j("#bank_account_id").val(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#bank_accounts').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#bank_accounts").dialog( "close" );
                                go(window.location);
                            } 
                       }); 
                    }
                    else
                    {
                        j.get("index.php?option=com_master&task=save_bank_account&tmpl=xml&" + j("#bankAccountForm").serialize(), function(data){
                            if(data != "")
                            {
                                alert(data);
                                j('#bank_accounts').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
                            }
                            else
                            {
                                j("#bank_accounts").dialog( "close" );
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
    });
    
    j(document).on("keypress","#opening_balance",function(e){
        prevent_char(e.which,e);
    });
    
    j(document).on("keypress","#account_no",function(e){
        numeric(e);
    }); 
    j(document).on("keypress","#ifsc_code",function(e){
        alpha_numeric(e);
        
    });
    j(document).on("keypress","#account_name,#bank_name,#branch,#account_no,#ifsc_code,#opening_balance", function(e) {
        if (e.which === 32 && !this.value.length)
            e.preventDefault();
    });
    
    j(document).on("keydown","#bank_name,#branch,#account_name",function (e) {
        if ( e.ctrlKey || e.altKey) 
        {
            e.preventDefault();
        } 
        else
        {
            var key = e.keyCode;
            if (!((key == 8) ||(key == 9) ||(key == 32)||(key == 13) || (key >= 65 && key <= 90) )) 
            {
                e.preventDefault();
            }
        }  
    });
    

    function add_bank_account()
    {   
        j("#mode").val("");
        j("#bank_account_id").val("");
        j("#account_name").val("");
        j("#bank_name").val("");
        j("#branch").val("");
        j("#account_no").val("");
        j("#account_type").val(0);
        j("#ifsc_code").val("");
        j("#opening_balance").val("");
              
        
        j("#bank_accounts").dialog("open");
        j("#bank_accounts").dialog({"title":"Add Bank Account"});
        j("#account_type").chosen({allow_single_deselect: true});
        j('#bank_accounts').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
 
    function edit_bank_account(bank_account_id)
    {   
        j("#bank_account_id").val(bank_account_id);
        j("#mode").val("e");
        
        j.get("index.php?option=com_master&task=bank_account_details&tmpl=xml&bank_account_id=" + bank_account_id, function(data){
            bank_account_details = j.parseJSON(data);
            
            j("#account_name").val(bank_account_details.account_name);  
            j("#bank_name").val(bank_account_details.bank_name);  
            j("#branch").val(bank_account_details.branch);  
            j("#account_no").val(bank_account_details.account_no);  
            j("#account_type").val(bank_account_details.account_type);  
            j("#ifsc_code").val(bank_account_details.ifsc_code);  
            j("#opening_balance").val(bank_account_details.opening_balance);  
        });
                
        j("#bank_accounts").dialog("open");
        j("#bank_accounts").dialog({"title":"Edit Bank Account"});
        //j("#account_type").chosen({allow_single_deselect: true});
        j('#bank_accounts').parent().find('.ui-dialog-buttonpane').find('button:contains(\'Submit\')').attr("disabled", false).removeClass("ui-state-disabled");
    }
    
    function change_bank_account_status(bank_account_id, account_status)
    {
        if(confirm("Are you sure?"))
        {
            j.get("index.php?option=com_master&task=change_bank_account_status&tmpl=xml&bank_account_id=" + bank_account_id + "&s=" + account_status, function(data){
                if(data == "ok")
                {
                    go(window.location);
                }
                else
                {
                    alert("Some error occurred!!!\nPlease Try Again.");
                }
            });
        }
        else
        {
            return false;
        }
    }
</script>
<h1>Bank Accounts</h1>
<input type="button" value="Add Bank Account" onclick="add_bank_account();">
<br /><br />
<div id="bank_accountslist">
    <table class="clean">
        <tr>
            <th width="20">S.No.</th>
            <th>Account Name</th>
            <th>Bank Name</th>
            <th>Branch</th>
            <th>Account No.</th>
            <th>Account Type</th>
            <th>IFSC Code</th>
            <th>Opening Balance</th>
            <th>Balance</th>
            <th>Uncleared Balance</th>
            <th>Status</th>
            <?
                if(is_admin())
                {
                    ?><th>Action</th><?
                }
            ?>
        </tr>
        <?  
            if(count($this->bank_accounts) > 0)
            {
                $x = 0;
                foreach($this->bank_accounts as $account)
                {
                    ?>
                    <tr style="cursor:pointer;">
                        <td align="center"><? echo ++$x; ?></td>
                        <td onclick="go('index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $account->id; ?>');"><? echo $account->account_name; ?></td>
                        <td onclick="go('index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $account->id; ?>');"><? echo $account->bank_name; ?></td>
                        <td onclick="go('index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $account->id; ?>');"><? echo $account->branch; ?></td>
                        <td onclick="go('index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $account->id; ?>');"><? echo $account->account_no; ?></td>
                        <td onclick="go('index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $account->id; ?>');">
                            <?
                                switch($account->account_type)
                                {
                                    case SAVINGS :
                                        echo "Savings";
                                        break;
                                    case CURRENT :
                                        echo "Current";
                                        break;
                                    case OD :
                                        echo "OD";
                                        break;
                                    case CC :
                                        echo "CC";
                                        break;
                                    default :
                                        echo "";
                                        break;
                                }
                            ?>
                        </td>
                        <td onclick="go('index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $account->id; ?>');"><? echo $account->ifsc_code; ?></td>
                        <td onclick="go('index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $account->id; ?>');"><? echo round_2dp($account->opening_balance); ?></td>
                        <td onclick="go('index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $account->id; ?>');"><? echo round_2dp($account->balance); ?></td>
                        <td onclick="go('index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $account->id; ?>');"><? echo round_2dp($account->uncleared_balance); ?></td>
                        <td onclick="go('index.php?option=com_amittrading&view=bank_account&bank_account_id=<? echo $account->id; ?>');">
                            <?
                                if($account->account_status == AC_ACTIVE)
                                { echo "Active"; }
                                else if($account->account_status == AC_CLOSED)
                                { echo "Closed"; }
                            ?>
                        </td>
                        <?
                            if(is_admin())
                            {
                                ?>    
                                <td align="center">
                                    <?
                                        if($account->account_status == AC_ACTIVE)
                                        {
                                            ?>
                                            <a onclick="edit_bank_account(<? echo $account->id; ?>); return false;"><img src="custom/graphics/icons/blank.gif" title="Edit" class="edit"></a>
                                            <a onclick="change_bank_account_status(<? echo $account->id; ?>, <? echo AC_CLOSED; ?>); return false;"><img src="custom/graphics/icons/16x16/login_inactive.png" title="Close A/c"></a>
                                            <?
                                        }
                                        else
                                        {
                                            ?>
                                            <a onclick="change_bank_account_status(<? echo $account->id; ?>, <? echo AC_ACTIVE; ?>); return false;"><img src="custom/graphics/icons/16x16/login_active.png" title="Re-Open A/c"></a>
                                            <?
                                        }
                                    ?>
                                </td>
                                <?
                            }
                        ?> 
                    </tr>
                    <?
                }
            }
        ?>
             
    </table>
</div>
<br />
<input type="hidden" name="mode" id="mode" value="" /> 
<input type="hidden" name="bank_account_id" id="bank_account_id" value="" /> 

<div style="display: none;" id="bank_accounts">
    <form method="post" id="bankAccountForm">
        <table class="">
            <tr>
                <td>Account Name :</td>
                <td><input type="text" id="account_name" name="account_name" />
            </tr>
            <tr>
                <td>Bank Name :</td>
                <td><input type="text" id="bank_name" name="bank_name" />
            </tr>
            <tr>
                <td>Branch :</td>
                <td><input type="text" id="branch" name="branch" />
            </tr>
            <tr>
                <td>Account No. :</td>
                <td><input type="text" id="account_no" name="account_no" />
            </tr>
            <tr>
                <td>Account Type. :</td> 
                <td>
                    <select id="account_type" name="account_type" style="width:166px;">
                        <option value="0"></option>
                        <option value="<? echo SAVINGS; ?>">Savings</option>
                        <option value="<? echo CURRENT; ?>">Current</option>
                        <option value="<? echo OD; ?>">OD</option>
                        <option value="<? echo CC; ?>">CC</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>IFSC Code. :</td>
                <td><input type="text" id="ifsc_code" name="ifsc_code" />
            </tr>
            <tr>
                <td>Opening Balance :</td>
                <td><input type="text" id="opening_balance" name="opening_balance" />
            </tr>
        </table>
    </form>
</div>