<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class DraugiemViewUsers extends JView
{
	/**
	 * HelloWorlds view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		//$canDo = HelloWorldHelper::getActions();
		JToolBarHelper::title(JText::_('COM_DRAUGIEM_USERS_TITLE'), 'draugiem');

		/*
		 * /**
	 * Writes a custom option and task button for the button bar.
	 *
	 * @param	string	$task		The task to perform (picked up by the switch($task) blocks.
	 * @param	string	$icon		The image to display.
	 * @param	string	$iconOver	The image to display when moused over.
	 * @param	string	$alt		The alt text for the icon image.
	 * @param	bool	$listSelect	True if required to check that a standard list item is checked.
	 * @since	1.0
	 */
	//public static function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
		JToolBarHelper::preferences('com_draugiem');

	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_DRAUGIEM_USERS_TITLE'));
	}
}
