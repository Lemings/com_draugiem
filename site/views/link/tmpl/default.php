<?php defined('_JEXEC') or die('Restricted access');
$doc = JFactory::getDocument();
$doc->addStyleSheet( 'components/com_draugiem/assets/styles.css' );
?>

<div class="draugiem-wrapper">
	<div class="info">
		<?php echo JText::_('COM_DRAUGIEM_LOGIN_DR_USR_INFO')?><img src="<? echo $responseArr['users'][$responseArr['uid']]['imgi'];?>" />
			<?php
				echo $responseArr['users'][$responseArr['uid']]['name'].' '.$responseArr['users'][$responseArr['uid']]['surname']?>
	</div>
	<div class="clear" style="clear:both;"></div>
	<div class="link">
		<h2><?php echo JText::_('COM_DRAUGIEM_LOGIN_TITLE'); ?></h2>
		<span class="tip"><?php echo JText::_('COM_DRAUGIEM_LINK_TIP'); ?></span>
		<form action="index.php?option=com_draugiem&task=login" method="post">
		<ul>
			<li>
				<label for="username"><span><?php echo JText::_('COM_DRAUGIEM_USERNAME'); ?></span></label>
				<input name="username" id="username" type="text" class="inputbox" alt="username" size="10" />
			</li>
			<li>
				<label for="passwd"><span><?php echo JText::_('COM_DRAUGIEM_PASSWORD'); ?></span></label>
				<input type="password" id="passwd" name="passwd" class="inputbox" size="10" alt="password" />
			</li>
			<li>
				<button class="button" type="submit"><?php echo JText::_('COM_DRAUGIEM_LOGIN'); ?></button>
			</li>
			<li>
				<a href="<?php echo $forgotPass; ?>" onclick="parent.document.location.href='<?php echo $forgotPass;?>';" > <?php echo JText::_('COM_DRAUGIEM_FORGOT_PASS'); ?></a>
			</li>
			<li>
				<a href="<?php echo $forgotUser; ?>" onclick="parent.document.location.href='<?php echo $forgotUser;?>';" ><?php echo JText::_('COM_DRAUGIEM_FORGOT_USERNAME'); ?></a>
			</li>
		</ul>
		<input type="hidden" name="option" value="com_draugiem" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="uid" value="<?php echo $responseArr['uid']?>" />
		<input type="hidden" name="return" value="<?php echo JRequest::getVar('return',JURI::base(),'get'); ?>" />
		<input type="hidden" name="<?php echo JSession::getFormToken(); ?>" value="1" />
		</form>
	</div>
	<div class="register">
		<h2><?php echo JText::_('COM_DRAUGIEM_NEWUSER_TITLE'); ?></h2>
		<span class="tip"><?php echo JText::_('COM_DRAUGIEM_REGISTER_TIP'); ?></span>
		<form action="index.php?option=com_draugiem&task=newUser" method="post" >
		<ul>
			<li>
				<label for="username"><span><?php echo JText::_('COM_DRAUGIEM_USERNAME'); ?></span></label>
				<input name="username" id="username" type="text" class="inputbox" alt="username" size="10" />
			</li>
			<li>
				<label for="email"><span><?php echo JText::_('COM_DRAUGIEM_EMAIL'); ?></span></label>
				<input type="email" id="email" name="email" class="inputbox" size="10" alt="password" />
			</li>
			<li>
				<a href="<?php echo $forgotPass; ?>" onclick="parent.document.location.href='<?php echo $forgotPass;?>';"><?php echo JText::_('COM_DRAUGIEM_FORGOT_PASS'); ?></a>
			</li>
			<li>
				<a href="<?php echo $forgotUser; ?>" onclick="parent.document.location.href='<?php echo $forgotUser;?>';"><?php echo JText::_('COM_DRAUGIEM_FORGOT_USERNAME'); ?></a>
			</li>
			<li>
				<button class="button" type="submit"><?php echo JText::_('COM_DRAUGIEM_REGISTER'); ?></button>
			</li>
		</ul>
			<input type="hidden" name="task" value="newUser" />
			<input type="hidden" name="id" value="0" />
			<input type="hidden" name="uid" value="<?php echo $responseArr['uid']?>" />
			<input type="hidden" name="name" value="<?php echo $responseArr['users'][$responseArr['uid']]['name'].' '.$responseArr['users'][$responseArr['uid']]['surname']?>" />
			<input type="hidden" name="return" value="<?php echo JRequest::getVar('return',JURI::base(),'get'); ?>" />
			<input type="hidden" name="<?php echo JSession::getFormToken(); ?>" value="1" />
		</form>
	</div>
	<div class="clear" style="clear:both;"></div>
</div>
