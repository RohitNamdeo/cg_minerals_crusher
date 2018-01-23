<?
    defined('_JEXEC') or die('Restricted access');
?>
<script>
function disable_user(employee_user_id)
{
    if(confirm("Are you sure?"))
    {
        j.get("index.php?option=com_hr&task=disable_user&tmpl=xml&employee_u_id=" + employee_user_id, function(data){
            if(data == "ok")
            {
                alert("User disabled successfully.");
                go(window.location);
            }
        });
    }
}

function enable_user(employee_user_id)
{
    if(confirm("Are you sure?"))
    {
        j.get("index.php?option=com_hr&task=enable_user&tmpl=xml&employee_u_id=" + employee_user_id, function(data){
            if(data != "")
            {
                alert(data);
                go(window.location);
            }
            else
            {
                alert("Error Occured! Please try again.");
            }
        });
    }
}

function apply_filter()
{
    go("index.php?option=com_hr&view=user_management&user_status=" + j("#user_status").val());
}
</script>
<h1>Users</h1>
<button onclick="go('index.php?option=com_hr&view=user_registration');">New User</button>
<br /><br />
<table>
    <tr>
        <td>User Status</td>
        <td>
            <select id="user_status">
                <option value="0" selected="selected">All</option>
                <option value="<? echo U_ACTIVE;?>" <? echo ($this->user_status == U_ACTIVE ? "selected='selected'" : "");?>>Active</option>
                <option value="<? echo U_DISABLED;?>" <? echo ($this->user_status == U_DISABLED ? "selected='selected'" : "");?>>Disabled</option>                                   
            </select>
        </td>
        <td>
            <input type="button" value="Apply" onclick="apply_filter();">
        </td>
        <td>
            <input type="button" value="Clear" onclick="go('index.php?option=com_hr&view=user_management');">
        </td>
    </tr>
</table><br />
<table class="clean" width="700" id="user_list">
    <thead>
      <tr>
  	    <th width="20">#</th>
        <th>Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Role</th>
        <th>User Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
        <?php
            $x=0;
            foreach($this->employees as $employee)
            {
            ?>
              <tr>
  	            <td align="center"><? $x++; echo $x; ?></td>
                <td>
                    <a href="index.php?option=com_hr&view=user_registration&m=e&e=<? echo $employee->id; ?>" alt="View Profile" title="View Profile">
                        <? echo $employee->first_name . " " . $employee->last_name; ?>
                    </a>
                </td>
                <td><? echo $employee->username; ?></td>
                <td align="center"><? echo $employee->email; ?></td>
                <td align="center"><? echo $employee->mobile_number; ?></td>
                <td><? echo $employee->designation_name; ?></td>
                <td><? echo ($employee->user_status == U_ACTIVE ? "Active" : "Disabled"); ?></td>
                <td align="center">
                    <input type="button" value="Actions" data-dropdown="#employee-dropdown<? echo $employee->id; ?>"/>
                    <div id="employee-dropdown<?php echo $employee->id ;?>" class="dropdown dropdown-tip dropdown-anchor-left" align="left">
                        <ul class="dropdown-menu">
                            <li><a href="index.php?option=com_hr&view=user_registration&m=e&e=<? echo $employee->id; ?>"><img src="custom/graphics/icons/blank.gif" class="edit" border="0" title="Edit">&nbsp;Edit User</a></li>
                            <?
                                if($employee->user_status == U_ACTIVE)
                                {
                                    ?>
                                        <li class="dropdown-divider"></li>
                                        <li><a href="#" onclick="disable_user(<? echo $employee->user_id;?>); return false;"><img src="custom/graphics/icons/blank.gif" class="delete" border="0" title="Disable User">&nbsp;Disable User</a></li>
                                    <?
                                }
                                else if($employee->user_status == U_DISABLED)
                                {
                                    ?>
                                        <li class="dropdown-divider"></li>
                                        <li><a href="#" onclick="enable_user(<? echo $employee->user_id;?>); return false;"><img src="custom/graphics/icons/16x16/tick.png" border="0" title="Enable User">&nbsp;Enable User</a></li>
                                    <?
                                }
                            ?>    
                        </ul>
                    </div>
                </td>
              </tr>
            <?
            }
        ?>
    </tbody>
</table>