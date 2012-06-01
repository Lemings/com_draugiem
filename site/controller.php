<?php
/**
 * Joomla! 1.5 component myApi
 *
 * @version $Id: controller.php 2010-05-01 08:43:14 svn $
 * @author Thomas Welton
 * @package Joomla
 * @subpackage myApi
 * @license GNU/GPL
 *
 * myApi - Combining the power of the Facebook platform with the ease and simplicity of Joomla.
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * myApi Component Controller
 */
class DraugiemController extends JController {

	function showRegisterWindow($responseArr){

		$forgotPass	= JRoute::_( 'index.php?option=com_users&view=reset' );
		$forgotUser	= JRoute::_( 'index.php?option=com_users&view=remind' );
		$formToken	= JHTML::_( 'form.token' );

		ob_start();
	 		include(JPATH_SITE.DS.'components'.DS.'com_draugiem'.DS.'views'.DS.'link'.DS.'tmpl'.DS.'default.php');
			$html = ob_get_contents();
		ob_end_clean();
		echo JHTML::stylesheet('styles.css','components/com_draugiem/assets/',array());
		echo $html;
		$mainframe =& JFactory::getApplication();
		$mainframe->close();
	}
	//This task logs in a user if account ir already linked with draugiem.lv
	function draugiemlogin($uid) {
		//$user = JFactory::getUser();
		//$return 	= base64_decode(JRequest::getVar('return',''));

		$return = '';

		if($uid){
			$mainframe = JFactory::getApplication();
			$options['return'] = $return;
			$options['uid'] = $uid;
			$error = $mainframe->login($uid,$options);

			if(!is_object($error)){
				$return = ($return == '') ? JURI::base() : $return;
				self::jsredirect($return, JText::_( 'COM_DRAUGIEM_LOGGED_IN_DRAUGIEM' ));
			}
		}else{
			self::jsredirect($return, JText::_( 'COM_DRAUGIEM_LOGIN_ERROR' )." - ".$error->getMessage());
		}

	}

	//Joomla user login task, after inputing existing user name and password
	function login(){

		JRequest::checkToken( 'post' ) or die( 'Invalid Token' );
		$mainframe =& JFactory::getApplication();
		$options 				= array();
		$credentials 			= array();
		$return 				= base64_decode(JRequest::getVar('return','','post'));
		$options['remember']	= JRequest::getBool('remember', false);
		$options['return'] 		= $return;
		$options['uid'] 		= JRequest::getInt('uid',0);
		$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');
		$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);

		$error = $mainframe->login($credentials, $options);

		if(JError::isError($error)){
			JError::raiseWarning( 100, $error->getMessage() );
		}else{
			self::newLink($options['uid'], JURI::base());
		}

	}

	//Checks if user is liked with Joomla user
	function isLinked(){
		$mainframe =& JFactory::getApplication();
		JRequest::checkToken( 'get' ) or die( 'Invalid Token' );
		$dr_auth_status = JRequest::getString('dr_auth_status','');
		$dr_auth_code = JRequest::getString('dr_auth_code', '');
		if ( $dr_auth_status == 'ok' && $dr_auth_code != '' ) {// Lietotājs ir autorizēts draugiem.lv portalā
			$draugiemParams = JComponentHelper::getParams('com_draugiem');
			$app_key = $draugiemParams->get('app_key');
			$url = 'http://api.draugiem.lv/php/';
			$url .= "?action=authorize&app=".$app_key;
			$url .= "&code=".$dr_auth_code;

			$response = @file_get_contents($url);
			$responseArr = unserialize($response);

			$db 	= JFactory::getDBO();

			//$responseArr['uid']=24794;
			$query 	= "SELECT userId FROM ".$db->nameQuote('#__draugiem_users')." WHERE drId =".$db->quote($responseArr['uid']);

			$db->setQuery($query);
			$db->query();
			$result = $db->loadObject();
			if (is_object($result))
				$user = JFactory::getUser($result->userId);
			if (isset($user) && $user->id > 0) {// Reāls lietotājs
				self::draugiemlogin($user->id);
			}
			else
				self::showRegisterWindow($responseArr); // Parāda reģistrācijas formu
		} else {
			JFactory::getApplication()->enqueueMessage( JText::_('COM_DRAUGIEM_AUTH_FAILED') );
			// Pievieno rindai paziņojumu
			if (count($msgQueue = $mainframe->getMessageQueue())) {
				$session = JFactory::getSession();
				$session->set('application.queue', $msgQueue);
			}
			$return='';
			$return = ($return == '') ? JURI::base() : $return;
			echo "<script>window.opener.parent.location.href='$return';if(window.opener!=window){ window.close();}</script>\n";
			$mainframe->close();
		}

	}
	// Links draugiem.lv user with existing user
	function newLink($uid, $return){

		if($uid != ''){
			$user	= JFactory::getUser();

			$db		= JFactory::getDBO();
			$query	= "INSERT INTO ".$db->nameQuote('#__draugiem_users')." (id,drId,userId,params) VALUES(0,".$db->quote($uid).",".$db->quote($user->id).",'')";
			$db->setQuery($query);
			$db->query();

			/*try{
			  $query = "UPDATE #__comprofiler SET #__comprofiler.avatar ='".$avatar."' user_id ='".$user->id."'";
			  $db->setQuery($query);
			  $db->query();
			}catch(Exception $e){}*/
			self::jsredirect($return, JText::_('COM_DRAUGIEM_LINK_COMPLETE'));
		}else{
			self::jsredirect($return, JText::_('COM_DRAUGIEM_NO_UID_FOUND'));
		}
	}

	//Creates Joomla user after form is submited.
	function newUser()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	// If registration is disabled - Redirect to login page.
		if(JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0) {
			self::jsredirect(JRoute::_('index.php?option=com_users&view=login', false));
			return false;
		}

		$mainframe =& JFactory::getApplication();

		//$return = base64_decode(JRequest::getVar('return',''));
		$return = JURI::base();
		// Get required system objects
		$user 		= clone(JFactory::getUser());
		$pathway 	=& $mainframe->getPathway();
		$config		=& JFactory::getConfig();
		$authorize	=& JFactory::getACL();
		$document   =& JFactory::getDocument();

		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		// Initialize new usertype setting
		$newUsertype = $usersConfig->get( 'new_usertype' );
		if (!$newUsertype) {
			$newUsertype = 'Registered';		}

		$userName = JRequest::getString('username', '', 'POST');
		$email = JRequest::getString('email', '', 'POST');
		$uid = JRequest::getString('uid', 0, 'POST');

		$db = JFactory::getDBO();
		$uniqueUsername = false;
		$i = 0;
		while(!$uniqueUsername){
			$tryUsername = $userName;
			if($i >=1){
				$tryUsername = $tryUsername.$i;		}

			$query = "SELECT COUNT(".$db->nameQuote('id').") FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('username')." = ".$db->quote($tryUsername);
			$db->setQuery($query);
			$count = $db->loadResult();
			if($count ==0){
				$uniqueUsername = true;
				$userName = $tryUsername;		}
				$i++;
		}

    	$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    	$randomPassword ="";
    	for ($p = 0; $p < 8; $p++) {
    	    $randomPassword .= $characters[mt_rand(0, strlen($characters))]; 	}


		$newUser['name'] = JRequest::getString('name','');
		$newUser['username'] = $userName;
		$newUser['password'] = $newUser['password2'] = $randomPassword;
		$newUser['email'] = $email;

		// Bind the post array to the user object
		if (!$user->bind( $newUser, 'usertype' )) {
			$message = $user->getError();
			self::jsredirect($return,$message);
		}

		// Set some initial user values
		$user->set('id',0);

		jimport('joomla.application.component.helper');

		// Default to Registered.
		$defaultUserGroup = $usersConfig->get('new_usertype',2);
		$user->set('usertype'		, 'deprecated');
		$user->set('groups'		, array($defaultUserGroup));

		$date = JFactory::getDate();
		$user->set('registerDate', $date->toMySQL());

		// If user activation is turned on, we need to set the activation information
		$useractivation = $usersConfig->get( 'useractivation' );

		// If there was an error with registration, set the message and display form
		if ( $result = !$user->save() ){
			$message = $user->getError();
			self::jsredirect($return,$message);
		}else {
			$db = JFactory::getDBO();

			$query	= "INSERT INTO ".$db->nameQuote('#__draugiem_users')." (id,drId,userId,params) VALUES(0,".$db->quote($uid).",".$db->quote($user->id).",'')";
			$db->setQuery($query);
			$db->query();



			try{
				//Sync Community Builder
				$sql_sync = "INSERT IGNORE INTO #__comprofiler(id,user_id) SELECT id,id FROM #__users WHERE #__users.id =".$db->Quote($user->id);
				$db->setQuery($sql_sync);
				$db->query();
			}catch(Exception $e){}

			// Send registration confirmation mail
			$cleanPassword = preg_replace('/[\x00-\x1F\x7F]/', '', $randomPassword); //Disallow control chars in the email
			self::_sendMail($user, $cleanPassword);

			$message = JText::_( 'COM_DRAUGIEM_LOGGED_IN_DRAUGIEM' );
			$options['return'] = $return;
			$options['uid'] = $user->id;
			$options['silent'] = true;
			$error = $mainframe->login($user->id,$options);

			$dispatcher =& JDispatcher::getInstance();
			$dispatcher->trigger('onAfterStoreUser', array($user->getProperties(), true, $result,''));

			self::jsredirect($return,$message);

		}
	}

	function _sendMail(&$user, $password){
		$mainframe =& JFactory::getApplication();
		$db		=& JFactory::getDBO();

		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');

		$usersConfig 	= &JComponentHelper::getParams( 'com_users' );
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$useractivation = $usersConfig->get( 'useractivation' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();

		$subject 	= sprintf ( JText::_( 'COM_DRAUGIEM_ACCOUNT_DETAILS_FOR' ), $name, $sitename);
		$subject 	= html_entity_decode($subject, ENT_QUOTES);

		$html = sprintf ( JText::_( 'COM_DRAUGIEM_EMAIL_REGISTERED_BODY' ), $sitename, $username, $password);
		$html = html_entity_decode($message2, ENT_QUOTES);


		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Send email to user
		if ( ! $mailfrom  || ! $fromname) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;		}

		$mailer =& JFactory::getMailer();
		$mailer->addReplyTo(array($mailfrom,$fromname));
		$mailer->addRecipient($email);
		$mailer->setSubject($subject);
		$mailer->setBody($html);
		$mailer->isHTML(true);
		$mailer->AltBody = strip_tags($html);
		$send = $mailer->Send();


		// Send notification to all administrators
		$subject2 = sprintf ( JText::_( 'COM_DRAUGIEM_ACCOUNT_DETAILS_FOR' ), $name, $sitename);
		$subject2 = html_entity_decode($subject2, ENT_QUOTES);

		// get superadministrators id
		foreach ( $rows as $row ) {
			if ($row->sendEmail) {
				$message2 = sprintf ( JText::_( 'COM_DRAUGIEM_SEND_MSG_ADMIN' ), $row->name, $sitename, $name, $email, $username);
				$message2 = html_entity_decode($message2, ENT_QUOTES);
				JUtility::sendMail($mailfrom, $fromname, $row->email, $subject2, $message2);
			}
		}
	}

	static function jsredirect($return, $message) {
		$mainframe = JFactory::getApplication();

		$mainframe->enqueueMessage($message);
		// No JApplication->redirect
		// Persist messages if they exist.
		if (count($msgQueue = $mainframe->getMessageQueue())) {
			$session = JFactory::getSession();
			$session->set('application.queue', $msgQueue);
		}
		echo "<script>parent.document.location.href='$return';</script>\n";
		$mainframe->close();
	}



}
?>
