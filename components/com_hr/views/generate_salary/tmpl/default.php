<?php
    defined('_JEXEC') or die('Restricted access');
?>
<script>
    j(function(){
        j("#month, #year").chosen();
    });
    
    j(document).on("change", "#month, #year", function(){
        j("#msg").html("");
    });
    
    function generate_salary()
    {
        if(j("#month").val() == 0 && j("#year").val() == 0)
        {
            alert("Select month and year."); return false;
        } 
        else
        {
            go("index.php?option=com_hr&task=generate_salary&month=" + j("#month").val() + "&year=" + j("#year").val());
        }
    }
</script>
<h1>Generate Salary</h1>
<table>
    <tr>
        <td>Month : </td>
        <td>
            <select id="month" style="width:100px;">
                <?
                    $months = array("0"=>"","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
                    foreach($months as $key=>$month)
                    {
                        ?>
                        <option value="<? echo $key; ?>" <? echo ($key == $this->month ? "selected='selected'" : ""); ?> ><? echo $month; ?></option>
                        <?
                    }
                ?>
            </select>
        </td>
        <td>Year : </td>
        <td>
            <select id="year" style="width:80px;">
                <option value="0"></option>
                <?
                    for($y=date("Y");$y>=2015;$y--)
                    {
                        ?>
                        <option value="<? echo $y; ?>" <? echo ($y == $this->year ? "selected='selected'" : ""); ?> ><? echo $y; ?></option>
                        <?
                    }
                ?>
            </select>
        </td>
        <td valign="bottom"><input type="button" value="Generate" onclick="generate_salary();"></td>
        <td valign="bottom"><input type="button" value="Clear" onclick="go('index.php?option=com_hr&view=generate_salary');"></td>
    </tr>
</table>
<br />
<?
    if($this->salary_generated)
    {
        echo "<div id='msg'>Salary has been generated for selected month and year.</div>";
    }
    else
    {
        echo "<div id='msg'>Click on generate button to generate salary.</div>";
    }
?>