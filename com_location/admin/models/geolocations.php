<?php
/**
 * @package    Location Component
 * @version    1.1.2
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class LocationModelGeolocations extends ListModel
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
				'id', 'g.id',
				'region_id', 'g.region_id',
				'state', 'g.state',
				'country', 'g.country',
				'district', 'g.district',
				'region', 'g.region',
				'city', 'g.city',
				'latitude', 'g.latitude',
				'longitude', 'g.longitude',
				'created', 'g.created',
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

		$country = $this->getUserStateFromRequest($this->context . '.filter.country', 'filter_country');
		$this->setState('filter.country', $country);

		$district = $this->getUserStateFromRequest($this->context . '.filter.district', 'filter_district');
		$this->setState('filter.district', $district);

		$region = $this->getUserStateFromRequest($this->context . '.filter.region', 'filter_region');
		$this->setState('filter.region', $region);

		$city = $this->getUserStateFromRequest($this->context . '.filter.city', 'filter_city');
		$this->setState('filter.city', $city);

		$region_id = $this->getUserStateFromRequest($this->context . '.filter.region_id', 'filter_region_id');
		$this->setState('filter.region_id', $region_id);

		// List state information.
		$ordering  = empty($ordering) ? 'g.created' : $ordering;
		$direction = empty($direction) ? 'desc' : $direction;
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
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.country');
		$id .= ':' . $this->getState('filter.district');
		$id .= ':' . $this->getState('filter.region');
		$id .= ':' . $this->getState('filter.city');
		$id .= ':' . $this->getState('filter.region_id');

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
			->select('g.*')
			->from($db->quoteName('#__location_geolocations', 'g'));

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('g.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(g.state = 0 OR g.state = 1)');
		}

		// Filter by country
		$country = $this->getState('filter.country');
		if (!empty($country))
		{
			$query->where($db->quoteName('g.country') . ' = ' . $db->quote($country));
		}

		// Join over the asset groups.
		$query->select('r.name AS associated_region')
			->join('LEFT', '#__location_regions AS r ON r.id = g.region_id');


		// Filter by district
		$district = $this->getState('filter.district');
		if (!empty($district))
		{
			$query->where($db->quoteName('g.district') . ' = ' . $db->quote($district));
		}

		// Filter by region
		$region = $this->getState('filter.region');
		if (!empty($region))
		{
			$query->where($db->quoteName('g.region') . ' = ' . $db->quote($region));
		}

		// Filter by city
		$city = $this->getState('filter.city');
		if (!empty($city))
		{
			$query->where($db->quoteName('g.city') . ' = ' . $db->quote($city));
		}

		// Filter by region_id state
		$region_id = $this->getState('filter.region_id');
		if (is_numeric($region_id))
		{
			$query->where('g.region_id = ' . (int) $region_id);
		}

		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('g.id = ' . (int) substr($search, 3));
			}
			else
			{
				$cols = array('g.country', 'g.district', 'g.region', 'g.city');
				$sql  = array();
				foreach ($cols as $col)
				{
					$sql[] = $db->quoteName($col) . ' LIKE '
						. $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				}
				$query->where('(' . implode(' OR ', $sql) . ')');
			}
		}

		// Group by
		$query->group(array('g.id'));

		// Add the list ordering clause.
		$ordering  = $this->state->get('list.ordering', 'g.created');
		$direction = $this->state->get('list.direction', 'desc');
		$query->order($db->escape($ordering) . ' ' . $db->escape($direction));

		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItems()
	{
		if ($items = parent::getItems())
		{
			foreach ($items as &$item)
			{
				$item->title =
					$item->country . ' / ' .
					$item->district . ' / ' .
					$item->region . ' / ' .
					$item->city;
			}
		}


		return $items;
	}
}