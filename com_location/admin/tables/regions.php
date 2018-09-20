<?php
/**
 * @package    Location Component
 * @version    1.1.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Table\Nested;
use Joomla\CMS\Table\Observer\Tags;

class LocationTableRegions extends Nested
{
	/**
	 * Cache for the root ID
	 *
	 * @var    integer
	 * @since  1.0.0
	 */
	protected static $root_id = -1;

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver &$db Database connector object
	 *
	 * @since  1.0.0
	 */
	function __construct(&$db)
	{
		parent::__construct('#__location_regions', 'id', $db);

		// Set the alias since the column is called state
		$this->setColumnAlias('published', 'state');

		Tags::createObserver($this, array('typeAlias' => 'com_location.region'));
	}


	/**
	 * Gets the ID of the root item in the tree
	 *
	 * @return  mixed  The primary id of the root row, or false if not found and the internal error is set.
	 *
	 * @since   11.1
	 */
	public function getRootId()
	{
		return -1;
	}

	/**
	 * Method to set the location of a node in the tree object.  This method does not
	 * save the new location to the database, but will set it in the object so
	 * that when the node is stored it will be stored in the new location.
	 *
	 * @param   integer $referenceId The primary key of the node to reference new location by.
	 * @param   string  $position    Location type string.
	 *
	 * @return void
	 *
	 * @note    Since 12.1 this method returns void and throws an \InvalidArgumentException when an invalid position is passed.
	 * @see     Nested::$_validLocations
	 * @since   1.0.0
	 */
	public function setLocation($referenceId, $position = 'after')
	{
		$referenceId = ($referenceId == -1) ? 0 : $referenceId;

		return parent::setLocation($referenceId, $position);
	}
}