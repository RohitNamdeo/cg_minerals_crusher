<?php
    defined ("_JEXEC") or die("Restricted Access");
?>
<script>
    j(function(){
        j("#refresh, #clear").addClass("ui-button ui-widget ui-state-default ui-corner-all");
    });
    
    function get_salary_statement()
    {
        if(j("#salary_st_from_month").val() == 0 || j("#salary_st_from_year").val() == 0 || j("#salary_st_to_month").val() == 0 || j("#salary_st_to_year").val() == 0)
        {
            alert("Please select both from and to month,year.");
            return false;
        }
        
        j.get("index.php?option=com_hr&view=employee_salary_statement&tmpl=xml&employee_id=<? echo $this->employee_id; ?>&from_month=" + j("#salary_st_from_month").val() + "&from_year=" + j("#salary_st_from_year").val() + "&to_month=" + j("#salary_st_to_month").val() + "&to_year=" + j("#salary_st_to_year").val(), function(data){
            if(data != "")
            {
                j("#salary_statement").html(j(data).filter("#salary_statement").html());
                j("#refresh, #clear").addClass("ui-button ui-widget ui-state-default ui-corner-all");
            }
        });
    }
        
    function clear_records()
    {
        j.get("index.php?option=com_hr&view=employee_salary_statement&tmpl=xml&employee_id=<? echo $this->employee_id; ?>", function(data){
            if(data != "")
            {
                j("#salary_statement").html(j(data).filter("#salary_statement").html());
                j("#refresh, #clear").addClass("ui-button ui-widget ui-state-default ui-corner-all");
            }
        });
    }
</script>
<div id="salary_statement">
    <table>
        <tr>
            <td align='right'>From : </td>
            <td align='right'>
                <select id="salary_st_from_month" style="width:90px;">
                    <?
                        $months = array("0"=>"","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
                        foreach($months as $key=>$month)
                        {
                            ?>
                            <option value="<? echo $key; ?>" <? echo ($key == $this->from_month ? "selected='selected'" : ""); ?> ><? echo $month; ?></option>
                            <?
                        }
                    ?>
                </select>
            </td>
            <td align='right'>
                <select id="salary_st_from_year" style="width:60px;">
                    <option value="0"></option>
                    <?
                        for($y=date("Y");$y>=2015;$y--)
                        {
                            ?>
                            <option value="<? echo $y; ?>" <? echo ($y == $this->from_year ? "selected='selected'" : ""); ?> ><? echo $y; ?></option>
                            <?
                        }
                    ?>
                </select>
            </td>
            <td align='right'>To : </td>
            <td align='right'>
                <select id="salary_st_to_month" style="width:90px;">
                    <?
                        $months = array("0"=>"","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
                        foreach($months as $key=>$month)
                        {
                            ?>
                            <option value="<? echo $key; ?>" <? echo ($key == $this->to_month ? "selected='selected'" : ""); ?> ><? echo $month; ?></option>
                            <?
                        }
                    ?>
                </select>
            </td>
            <td align='right'>
                <select id="salary_st_to_year" style="width:60px;">
                    <option value="0"></option>
                    <?
                        for($y=date("Y");$y>=2015;$y--)
                        {
                            ?>
                            <option value="<? echo $y; ?>" <? echo ($y == $this->to_year ? "selected='selected'" : ""); ?> ><? echo $y; ?></option>
                            <?
                        }
                    ?>
                </select>
            </td>
            <td align='right'>
                <input type="button" value="Refresh" id="refresh" onclick="get_salary_statement();">
                <input type="button" value="Clear" id="clear" onclick="clear_records();">
            </td>
        </tr>
    </table>
    <br />
    <?
        $balance = $this->opening_balance;
        if(count($this->salary_statement) > 0)
        {
            $x = 0;
            $total_debit = 0;
            $total_credit = 0;
            ?>
            <div align="right"><b>Opening Balance : </b><? echo abs($this->opening_balance) . ($this->opening_balance < 0 ? "Cr" : "Dr"); ?></div>
            <br />
            <table class="clean floatheader centreheadings spread">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Particulars</th>
                    <th>Instrument</th>
                    <th>Instrument No.</th>
                    <th>Instrument Bank</th>
                    <th>Month</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Balance</th>
                </tr>
                <?
                    foreach($this->salary_statement as $statement)
                    {
                        ?>
                        <tr>
                            <td align="center"><? echo ++$x; ?></td>
                            <td align="center">
                                <?
                                    if($statement->is_last_day)
                                    {
                                        echo ($statement->date != "0000-00-00" ? date("t-M-Y", strtotime($statement->date)) : "");
                                    }
                                    else
                                    {
                                        echo ($statement->date != "0000-00-00" ? date("d-M-Y", strtotime($statement->date)) : "");
                                    }
                                ?>
                            </td>
                            <td><? echo $statement->particulars; ?></td>
                            <td align="center"><? echo $statement->instrument; ?></td>
                            <td><? echo $statement->instrument_no; ?></td>
                            <td><? echo $statement->instrument_bank; ?></td>
                            <td><? echo $statement->statement_month; ?></td>
                            <td align="right">
                                <? 
                                    $balance += floatval($statement->debit);
                                    $total_debit += floatval($statement->debit);
                                    echo $statement->debit;
                                ?>
                            </td>
                            <td align="right">
                                <? 
                                    $balance -= floatval($statement->credit);
                                    $total_credit += floatval($statement->credit);
                                    echo $statement->credit;
                                ?>
                            </td>
                            <td align="right"><? echo round_2dp(abs($balance)) . ($balance < 0 ? "Cr" : "Dr"); ?></td>
                        </tr>    
                        <?
                    }    
                ?>
                <tfoot>
                    <tr>
                        <td colspan="7" align="right"><b>Total :</b></td>
                        <td align="right"><b><? echo round_2dp($total_debit); ?></b></td>
                        <td align="right"><b><? echo round_2dp($total_credit); ?></b></td>
                        <td align="right"><b><? echo round_2dp(abs($balance)) . ($balance < 0 ? "Cr" : "Dr"); ?></b></td>
                    </tr>
                </tfoot>
            </table>
            <br />
            <div align="right"><b>Closing Balance : </b><? echo round_2dp(abs($balance)) . ($balance < 0 ? "Cr" : "Dr"); ?></div>
            <?
        }
        else
        {
            echo "No records found!!";
        }
    ?>    
</div>