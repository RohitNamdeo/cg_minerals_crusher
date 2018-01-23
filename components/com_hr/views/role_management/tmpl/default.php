<?php
    defined('_JEXEC') or die('Restricted access');
?>
<h1>Role Management</h1><br />
<div>
    <table class="clean centreheadings floatheader" width="400">
        <tr>
            <th width="20">#</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?  
            if(count($this->designations) > 0)
            {
                $x = 0;
                foreach($this->designations as $designation)
                {
                    ?>
                        <tr>
                            <td align="center"><? echo ++$x; ?></td>
                            <td><? echo $designation->designation_name; ?></td>
                            <td align="center">
                                <input type="button" value="Assign Permissions" onclick="go('index.php?option=com_hr&view=permission_assignment&designation_id=<? echo $designation->id; ?>')">
                            </td>
                        </tr>
                    <?
                }
            }
        ?>
    </table>
</div>