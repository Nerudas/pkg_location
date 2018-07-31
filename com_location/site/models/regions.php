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

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

class LocationModelRegions extends ListModel
{
	/**
	 *  Visitor Region
	 *
	 * @var    object
	 * @since  1.0.0
	 */
	protected $_visitorRegion = null;

	/**
	 *  Geolocation Region
	 *
	 * @var    object
	 * @since  1.0.0
	 */
	protected $_geolocationRegion = null;

	/**
	 *  Default Region
	 *
	 * @var    object
	 * @since  1.0.0
	 */
	protected $_defaultRegion = null;

	/**
	 * Regions array
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $_regions = array();

	/**
	 * Regions
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $_profileRegions = array();

	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @since  1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'r.id',
				'name', 'r.name',
				'abbreviation', 'r.abbreviation',
				'parent_id', 'r.parent_id',
				'lft', 'r.lft',
				'rgt', 'r.rgt',
				'level', 'r.level',
				'path', 'r.path',
				'alias', 'r.alias',
				'icon', 'r.icon',
				'default', 'r.default',
				'show_all', 'r.show_all',
				'state', 'r.state',
				'access', 'r.access',
				'latitude', 'r.latitude',
				'longitude', 'r.longitude',
				'zoom', 'r.zoom',
				'items_tags', 'r.items_tags',
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string $ordering  An optional ordering field.
	 * @param   string $direction An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$user = Factory::getUser();

		// Published state
		if ((!$user->authorise('core.manage', 'com_companies')))
		{
			// Limit to published for people who can't edit or edit.state.
			$this->setState('filter.published', 1);
		}
		else
		{
			$this->setState('filter.published', array(0, 1));
		}

		// List state information.
		$ordering  = empty($ordering) ? 'r.lft' : $ordering;
		$direction = empty($direction) ? 'asc' : $direction;
		parent::populateState($ordering, $direction);

		// Set limit & limitstart for query.
		$this->setState('list.limit', 0);
		$this->setState('list.start', 0);

		// Set ordering for query.
		$ordering  = empty($ordering) ? 'r.lft' : $ordering;
		$direction = empty($direction) ? 'asc' : $direction;
		$this->setState('list.ordering', $ordering);
		$this->setState('list.direction', $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string $id A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since  1.0.0
	 */
	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . serialize($this->getState('filter.item_id'));
		$id .= ':' . $this->getState('filter.item_id.include');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since  1.0.0
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select('r.*')
			->from($db->quoteName('#__location_regions', 'r'))
			->where($db->quoteName('r.alias') . ' <> ' . $db->quote('root'));

		// Filter by access level
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('r.access IN (' . $groups . ')');
		}

		// Filter by published state.
		$published = $this->getState('filter.published');
		if (!empty($published))
		{
			if (is_numeric($published))
			{
				$query->where('r.state = ' . (int) $published);
			}
			elseif (is_array($published))
			{
				$query->where('r.state IN (' . implode(',', $published) . ')');
			}
		}

		// Filter by a single or group of items.
		$itemId = $this->getState('filter.item_id');
		if (is_numeric($itemId))
		{
			$type = $this->getState('filter.item_id.include', true) ? '= ' : '<> ';
			$query->where('r.id ' . $type . (int) $itemId);
		}
		elseif (is_array($itemId))
		{
			$itemId = ArrayHelper::toInteger($itemId);
			$itemId = implode(',', $itemId);
			$type   = $this->getState('filter.item_id.include', true) ? 'IN' : 'NOT IN';
			$query->where('r.id ' . $type . ' (' . $itemId . ')');
		}

		// Add the list ordering clause.
		$ordering  = $this->state->get('list.ordering', 'r.lft');
		$direction = $this->state->get('list.direction', 'asc');
		$query->order($db->escape($ordering) . ' ' . $db->escape($direction));

		return $query;
	}

	/**
	 * Gets an array of objects from the results of database query.
	 *
	 * @param   string  $query      The query.
	 * @param   integer $limitstart Offset.
	 * @param   integer $limit      The number of records.
	 *
	 * @return  object[]  An array of results.
	 *
	 * @since 1.0.0
	 * @throws  \RuntimeException
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$this->getDbo()->setQuery($query, $limitstart, $limit);

		return $this->getDbo()->loadObjectList('id');
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	public function getItems()
	{
		$items = parent::getItems();
		if (!empty($items))
		{
			foreach ($items as &$item)
			{
				// Get Tags
				$item->tags = new TagsHelper;
				$item->tags->getItemTags('com_location.region', $item->id);

				// Icons
				$icon       = (!empty($item->icon) && JFile::exists(JPATH_ROOT . '/' . $item->icon)) ?
					$item->icon : 'media/com_location/images/no-icon.jpg';
				$item->icon = Uri::root(true) . '/' . $icon;

				$this->_regions[$item->id] = $item;
			}
		}

		return $items;
	}

	/**
	 * Method to get visitor region
	 *
	 * @return object
	 *
	 * @since 1.0.0
	 */
	public function getVisitorRegion()
	{
		if (!is_object($this->_visitorRegion))
		{
			$app  = Factory::getApplication();
			$user = Factory::getUser();
			$id   = $app->input->cookie->get('region', -1);
			if (empty($id) || $id == -1 || $id == 'undefined' ||
				(!$app->input->cookie->get($check_name, false) && !$this->getRegion($id)))
			{
				if (!$user->guest)
				{
					$this->_visitorRegion = $this->getProfileRegion($user->id);

					return $this->_visitorRegion;
				}

				$this->_visitorRegion = $this->getGeoLocationRegion();

				return $this->_visitorRegion;
			}

			$this->_visitorRegion = $this->getRegion($id);
		}

		return $this->_visitorRegion;
	}

	/**
	 * Method to get a single region record.
	 *
	 * @param   integer $pk The id of profile
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	public function getProfileRegion($pk)
	{
		$user = Factory::getUser();
		$pk   = (!empty($pk)) ? $pk : $user->id;
		if (!isset($this->_profileRegions[$pk]))
		{
			try
			{
				$db    = Factory::getDbo();
				$query = $db->getQuery(true)
					->select('p.region')
					->from($db->quoteName('#__profiles', 'p'))
					->join('INNER', '#__location_regions AS r ON r.id = p.region')
					->where('p.id =' . (int) $pk);

				// Filter by access level
				if (!$user->authorise('core.admin'))
				{
					$groups = implode(',', $user->getAuthorisedViewLevels());
					$query->where('r.access IN (' . $groups . ')');
				}

				// Filter by published state.
				$published = $this->getState('filter.published');
				if (!empty($published))
				{
					if (is_numeric($published))
					{
						$query->where('r.state = ' . (int) $published);
					}
					elseif (is_array($published))
					{
						$query->where('r.state IN (' . implode(',', $published) . ')');
					}
				}

				$db->setQuery($query);
				$id = $db->loadResult();

				if (!empty($id))
				{
					$this->_profileRegions[$pk] = $this->getRegion($id);
				}
				else
				{
					$this->_profileRegions[$pk] = $this->getDefaultRegion();
				}

			}
			catch (Exception $e)
			{
				$this->setError($e);
				$this->_profileRegions[$pk] = false;
			}
		}

		return $this->_profileRegions[$pk];
	}

	/**
	 * Method to get a single region record.
	 *
	 * @param   integer $pk The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	public function getRegion($pk)
	{
		if (!isset($this->_regions[$pk]))
		{
			try
			{
				$db    = Factory::getDbo();
				$query = $db->getQuery(true)
					->select('r.*')
					->from($db->quoteName('#__location_regions', 'r'))
					->where('r.id =' . (int) $pk);

				// Filter by access level
				$user = Factory::getUser();
				if (!$user->authorise('core.admin'))
				{
					$groups = implode(',', $user->getAuthorisedViewLevels());
					$query->where('r.access IN (' . $groups . ')');
				}

				// Filter by published state.
				$published = $this->getState('filter.published');
				if (!empty($published))
				{
					if (is_numeric($published))
					{
						$query->where('r.state = ' . (int) $published);
					}
					elseif (is_array($published))
					{
						$query->where('r.state IN (' . implode(',', $published) . ')');
					}
				}

				$db->setQuery($query);
				$data = $db->loadObject();

				if (empty($data))
				{
					$this->_regions[$pk] = false;

					return $this->_regions[$pk];
				}

				// Get Tags
				$data->tags = new TagsHelper;
				$data->tags->getItemTags('com_location.region', $data->id);

				// Icons
				$icon       = (!empty($data->icon) && JFile::exists(JPATH_ROOT . '/' . $data->icon)) ?
					$data->icon : 'media/com_location/images/no-icon.jpg';
				$data->icon = Uri::root(true) . '/' . $icon;

				$this->_regions[$pk] = $data;

			}
			catch (Exception $e)
			{
				$this->setError($e);
				$this->_regions[$pk] = false;
			}
		}

		return $this->_regions[$pk];
	}

	/**
	 * Method to get geolocation region
	 *
	 * @return object
	 *
	 * @since 1.0.0
	 */
	public function getGeoLocationRegion()
	{
		if (!is_object($this->_geolocationRegion))
		{
			$this->_geolocationRegion = $this->getDefaultRegion();
		}

		return $this->_geolocationRegion;
	}

	/**
	 * Method to get default region
	 *
	 * @return object
	 *
	 * @since 1.0.0
	 */
	public function getDefaultRegion()
	{
		if (!is_object($this->_defaultRegion))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('id')
				->from($db->quoteName('#__location_regions'))
				->where($db->quoteName('default') . ' = 1');
			$db->setQuery($query);

			$id = $db->loadResult();
			if (empty($id))
			{
				$id = -1;
			}

			$this->_defaultRegion = $this->getRegion($id);
		}

		return $this->_defaultRegion;
	}
}