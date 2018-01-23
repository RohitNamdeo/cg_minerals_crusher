<?php defined('_JEXEC') or die('Restricted access'); ?>
<h1>User Groups</h1>
<table class="clean" width="400">
<thead>
   <tr>
      <th>
         Group Name
      </th>
      <th>
         Class
      </th>
   </tr>
</thead>
<tbody>
<?
$current_class = "";
foreach($this->usergroups as $usergroup)
{
	if ($current_class != $usergroup->class_name)
	{
		?>
			<tr>
				<td colspan="2">
					<b><? echo $usergroup->class_name; ?></b>
				</td>
			</tr>
		<?
		$current_class = $usergroup->class_name;
	}
?>
   <tr>
      <td>
         <a href="index.php?option=com_twolevelmenu&view=permitassignment&g=<? echo $usergroup->id; ?>"><? echo $usergroup->designation_name; ?></a>
      </td>
      <td>
         <? echo $usergroup->class_name; ?>
      </td>
   </tr>
<?
}
?>
<tbody>
</table>
<small>(click a group name to assign permits)</small>