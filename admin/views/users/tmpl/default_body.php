<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.$item->uid);?>"><?php echo $item->username; ?></a>
		</td>
		<td>
			<?php echo $item->name; ?></a>
		</td>
		<td>
			<?php echo $item->drId; ?></a>
		</td>
		<td>
			<?php echo $item->uid; ?></a>
		</td>
	</tr>
<?php endforeach; ?>
