<?php
    defined('_JEXEC') or die('Restricted access');
    $months = array("0"=>"","1"=>"January","2"=>"February","3"=>"March","4"=>"April","5"=>"May","6"=>"June","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
?>
<script>
    j(function(){
        j("#month, #year").chosen();
    });
    
    function delete_salary()
    {   
        if(j("#month").val() == 0 || j("#year").val() == 0)
        {
            alert("Please select month and year");
            return false;
        }
        else
        {
            if(confirm("Are you sure you want to delete salary for the selected month and year?"))
            {
                go("index.php?option=com_hr&task=delete_salary&month=" + j("#month").val() + "&year=" + j("#year").val());
            }
            else
            {
                return false;
            }
        }
    }
</script>
<h1>Delete Salary</h1>
<table>
    <tr>
        <td>Month : </td>
        <td>
            <select id="month" style="width:90px;">
                <?
                    foreach($months as $key=>$month)
                    {
                        ?>
                        <option value="<? echo $key; ?>" <? echo ($key == $this->month ? "selected='selected'" : ""); ?> ><? echo $month; ?></option>
                        <?
                    }
                ?>
            </select>
        </td>
        <td>Year :</td>
        <td>
            <select id="year" style="width:90px;">
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
        <td>
            <input type="button" value="Delete" onclick="delete_salary();">
            <input type="button" value="Clear" onclick="go('index.php?option=com_hr&view=delete_salary');">
        </td>
    </tr>
</table>