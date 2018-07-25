<?php
/**
 * @package    Location Component
 * @version    1.0.0
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
}