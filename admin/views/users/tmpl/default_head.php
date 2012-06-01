<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width="5">
		<?php echo JHtml::_('grid.sort',  'Id', 'id'); ?>
	</th>
	<th width="20">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>
	<th>
		<?php echo JText::_('COM_DRAUGIEM_USERNAME'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_DRAUGIEM_NAME'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_DRAUGIEM_DRID'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_DRAUGIEM_UID'); ?>
	</th>
</tr>
