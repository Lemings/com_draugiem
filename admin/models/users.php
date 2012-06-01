<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * HelloWorldList Model
 */
class DraugiemModelUsers extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// Select some fields
		$query->select('dr.id AS id, dr.drId as drId,dr.params as params,u.id AS uid, u.name AS name, u.username AS username');
		// From the hello table
		$query->from('#__draugiem_users AS dr');
		$query->join('INNER', '`#__users` AS u ON u.id = dr.userId');
		return $query;
	}
}
