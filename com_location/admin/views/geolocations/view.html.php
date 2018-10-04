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

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class LocationViewGeolocations extends HtmlView
{
	/**
	 * An array of geolocations
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected $geolocations;

	/**
	 * The pagination object
	 *
	 * @var  JPagination
	 *
	 * @since  1.0.0
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var  object
	 *
	 * @since  1.0.0
	 */
	protected $state;

	/**
	 * Form object for search filters
	 *
	 * @var  JForm
	 *
	 * @since  1.0.0
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	public $activeFilters;

	/**
	 * Display the view
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return mixed A string if successful, otherwise an Error object.
	 *
	 * @throws Exception
	 *
	 * @since  1.0.0
	 */
	public function display($tpl = null)
	{
		LocationHelper::addSubmenu('geolocations');

		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->state         = $this->get('State');

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		return parent::display($tpl);
	}


	/**
	 * Add the extension title and toolbar.
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	protected function addToolbar()
	{
		$user  = Factory::getUser();
		$canDo = LocationHelper::getActions('com_location', 'geolocations');

		JToolBarHelper::title(Text::_('COM_LOCATION') . ': ' . Text::_('COM_LOCATION_GEOLOCATIONS'), 'location');

		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('geolocation.edit');
		}


		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('geolocations.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('geolocations.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'geolocations.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('geolocations.trash');
		}

		if ($user->authorise('core.admin', 'com_location') || $user->authorise('core.options', 'com_location'))
		{
			JToolbarHelper::preferences('com_location');
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since  1.0.0
	 */
	protected function getSortFields()
	{
		return [
			'g.state'    => Text::_('JSTATUS'),
			'g.id'       => Text::_('JGRID_HEADING_ID'),
			'g.country'  => Text::_('COM_LOCATION_GEOLOCATION_COUNTRY'),
			'g.district' => Text::_('COM_LOCATION_GEOLOCATION_DISTRICT'),
			'g.region'   => Text::_('COM_LOCATION_GEOLOCATION_REGION'),
			'g.city'     => Text::_('COM_LOCATION_GEOLOCATION_CITY'),
			'g.created'  => Text::_('JGLOBAL_CREATED_DATE'),
		];
	}
}