<?php
    defined('_JEXEC') or die('Restricted access'); 
?>
<style>
    .pagination span, a {
        padding: 3px;
    }
</style>
<script>
    j(function(){
        j("#employee_id, #location_id, #account_status").chosen();        
    });
    
    function show_employees()
    {
        if(j("#employee_id").val() == 0 && j("#location_id").val() == 0 && j("#account_status").val() == 0)
        {
            alert("Select filters."); return false;
        }
        else
        {
            go("index.php?option=com_hr&view=employee_management&employee_id=" + j("#employee_id").val() + "&location_id=" + j("#location_id").val() + "&account_status=" + j("#account_status").val());
        }
    }
    
    function activate_account(employee_id)
    {
        if(confirm("Do you really want to activate account of this employee?"))
        {
            go("index.php?option=com_hr&task=activate_account&employee_id=" + employee_id);
        }        
    }
    
    function deactivate_account(employee_id)
    {
        if(confirm("Do you really want to deactivate account of this employee?"))
        {
            go("index.php?option=com_hr&task=deactivate_account&employee_id=" + employee_id);
        }        
    }
</script>
<h1>Employee Management</h1>
<input type="button" value="New Employee" onclick="go('index.php?option=com_hr&view=employee_registration');">
<br />
<table>
    <tr>
        <td>Employees : </td>
        <td>
            <select id="employee_id" style="width:180px;">
                <option value="0"></option>
                <?
                    if(count($this->employee_names) > 0)
                    {       
                        foreach($this->employee_names as $employee)
                        {   
                            ?><option value="<? echo $employee["id"]; ?>" <? echo ($employee["id"] == $this->employee_id ? "selected='selected'" : ""); ?> ><? echo $employee["employee_name"]; ?></option><? 
                        }
                    }
                ?>
            </select>
        </td>
        <td>Location : </td>
        <td>
            <select id="location_id" style="width:180px;">
                <option value="0"></option>
                <?
                    foreach($this->locations as $location)
                    {
                        ?><option value="<? echo $location["id"]; ?>" <? echo ($location["id"] == $this->location_id ? "selected='selected'" : ""); ?> ><? echo $location["location_name"]; ?></option><?
                    } 
                ?>
            </select>
        </td>
        <td>Account Status : </td>
        <td>
            <select id="account_status" style="width:180px;">
                <option value="0"></option>
                <option value="<? echo AC_ACTIVE; ?>" <? echo ($this->account_status == AC_ACTIVE ? "selected='selected'" : ""); ?> >Active</option>
                <option value="<? echo AC_CLOSED; ?>" <? echo ($this->account_status == AC_CLOSED ? "selected='selected'" : ""); ?> >Closed</option>
            </select>
        </td>
        <td>
            <input type="button" value="Refresh" onclick="show_employees(); return false;">
            <input type="button" value="Clear" onclick="go('index.php?option=com_hr&view=employee_management');">
        </td>
    </tr>
</table>
<table width="80%">
    <tr align="center">
        <td>
            <?         
                if($this->total > 100)
                {
                    echo "<br />";
                    echo $this->pagination->getPagesLinks();
                    echo "<br /><br />";
                }
                else
                {
                    echo "<br />";
                }
            ?>
        </td>
    </tr>
</table>
<?
    if(count($this->employees) > 0)
    {
        ?><a href="#" onclick="popup_print('<h1>Employees</h1><br />' + j('#employees').html()); return false;"><img src="custom/graphics/icons/blank.gif" class="print"></a><br /><br /><?
    }
?>
<div id="employees">
    <table class="clean centreheadings floatheader" width="80%">
        <!--<thead>-->
            <tr>
                <th>S.No.</th>
                <th>Emp. Code</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Location</th>
                <th>DOJ</th>
                <th>Gross Salary</th>
                <th>Mobile No.</th>
                <th>Address</th>
                <th>Account Status</th>
                <th class="noprint">Actions</th>
            </tr>
        <!--</thead>-->
        <?
            if(count($this->employees) > 0)
            {
                $x = $this->limitstart;
                foreach($this->employees as $employee)
                {
                    ?>
                    <tr>
                        <td align="center"><? echo ++$x; ?></td>
                        <td align="center"><? echo $employee->id; ?></td>
                        <td><a href="index.php?option=com_hr&view=employee_account&employee_id=<? echo $employee->id; ?>"><? echo $employee->employee_name; ?></a></td>
                        <td><? echo $employee->designation; ?></td>
                        <td><? echo $employee->location_name; ?></td>
                        <td align="center"><? echo date("d-M-Y", strtotime($employee->doj)); ?></td>
                        <td align="right"><? echo round_2dp($employee->gross_salary); ?></td>
                        <td><? echo $employee->mobile_no; ?></td>
                        <td><? echo $employee->address; ?></td>
                        <td><? echo ($employee->account_status == AC_ACTIVE ? "Active" : "Closed"); ?></td>
                        <td align="center" class="noprint">
                            <input type="button" value="Actions" data-dropdown="#action-dropdown<? echo $employee->id; ?>"/>
                            <div id="action-dropdown<?php echo $employee->id ;?>" class="dropdown dropdown-tip dropdown-anchor-left" align="left">
                                <ul class="dropdown-menu">
                                    <?
                                        if($employee->account_status == AC_ACTIVE)
                                        {
                                            ?>
                                            <li><a href="index.php?option=com_hr&view=employee_registration&m=e&employee_id=<? echo $employee->id; ?>"><img src="custom/graphics/icons/blank.gif" class="edit" title="Edit">&nbsp;Edit Profile</a></li>
                                            <li class="dropdown-divider"></li>
                                            <li><a href="#" onclick="deactivate_account(<? echo $employee->id; ?>); return false;"><img src="custom/graphics/icons/deactivate.png" title="Deactivate Account">&nbsp;Deactivate Account</a></li>
                                            <?
                                        }
                                        else
                                        {
                                            ?><li><a href="#" onclick="activate_account(<? echo $employee->id; ?>); return false;"><img src="custom/graphics/icons/activate.png" title="Activate Account">&nbsp;Activate Account</a></li><?
                                        }
                                    ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?
                }
            }
            else
            {
                ?>
                <tr>
                    <td colspan="11" align="center">No employees to display.</td>
                </tr>
                <?
            }
        ?>
    </table>
</div>
<table width="80%">
    <tr align="center">
        <td>
            <?
                if($this->total > 100)
                {
                    echo "<br />";
                    echo $this->pagination->getPagesLinks();
                }
            ?>
        </td>
    </tr>
</table>