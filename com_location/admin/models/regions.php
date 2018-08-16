<?php
/**
 * @package    Location Component
 * @version    1.0.1
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

class LocationModelRegions extends ListModel
{
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
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
		$this->setState('filter.access', $access);

		$tags = $this->getUserStateFromRequest($this->context . '.filter.tags', 'filter_tags', '');
		$this->setState('filter.tags', $tags);

		// List state information.
		$ordering  = empty($ordering) ? 'r.lft' : $ordering;
		$direction = empty($direction) ? 'asc' : $direction;
		parent::populateState($ordering, $direction);
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
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . serialize($this->getState('filter.tag'));

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

		// Join over the asset groups.
		$query->select('ag.title AS access_level')
			->join('LEFT', '#__viewlevels AS ag ON ag.id = r.access');

		// Filter by access level.
		$access = $this->getState('filter.access');
		if (is_numeric($access))
		{
			$query->where('r.access = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('r.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(r.state = 0 OR r.state = 1)');
		}

		// Filter by a single or group of tags.
		$tags = $this->getState('filter.tags');
		if (is_array($tags))
		{
			$tags = ArrayHelper::toInteger($tags);
			$tags = implode(',', $tags);
			if (!empty($tags))
			{
				$query->join('LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
					. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('r.id')
					. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_location.region'))
					->where($db->quoteName('tagmap.tag_id') . ' IN (' . $tags . ')');
			}
		}

		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('id = ' . (int) substr($search, 3));
			}
			else
			{
				$cols = array('r.name', 'r.alias', 'r.abbreviation');
				$sql  = array();
				foreach ($cols as $col)
				{
					$sql[] = $db->quoteName($col) . ' LIKE '
						. $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				}
				$query->where('(' . implode(' OR ', $sql) . ')');

			}
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
					$item->icon : 'media/com_location/images/no-icon.png';
				$item->icon = Uri::root(true) . '/' . $icon;
			}
		}

		return $items;
	}
}