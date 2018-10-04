<?php
/**
 * @package    Field Types - Location Plugin
 * @version    1.1.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

FormHelper::loadFieldClass('list');

class JFormFieldRegions extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'regions';

	/**
	 * Options array
	 *
	 * @var   array
	 *
	 * @since  1.0.0
	 */
	protected $_options = null;

	/**
	 * Current region id
	 *
	 * @var   int
	 *
	 * @since  1.0.0
	 */
	protected $current = null;

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement $element   The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed            $value     The form field value to validate.
	 * @param   string           $group     The field name group control value. This acts as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 *
	 * @since   1.0.0
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		$app = Factory::getApplication();

		// Initialize model
		BaseDatabaseModel::addIncludePath(JPATH_ROOT . '/components/com_location/models', 'LocationModel');
		$model = BaseDatabaseModel::getInstance('Regions', 'LocationModel', array('ignore_request' => true));

		$user = Factory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_location')) &&
			(!$user->authorise('core.edit', 'com_location')))
		{
			$model->setState('filter.published', 1);
		}
		else
		{
			$model->setState('filter.published', array(0, 1));
		}

		$current       = $model->getVisitorRegion();
		$this->current = $model->getVisitorRegion()->id;

		if ($app->isSite() && empty($this->value))
		{
			$this->value = ($this->multiple) ? array($current) : $current;
		}

		return $return;
	}


	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since  1.0.0
	 */
	protected function getOptions()
	{
		if (!is_array($this->_options))
		{
			// Get regions
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array('r.id', 'r.parent_id', 'r.name', 'r.level', 'r.state', 'r.access'))
				->from($db->quoteName('#__location_regions', 'r'))
				->where($db->quoteName('r.alias') . ' <>' . $db->quote('root'))
				->where($db->quoteName('r.state') . ' != ' . $db->quote(-2));

			$query->order($db->escape('r.lft') . ' ' . $db->escape('asc'));

			$db->setQuery($query);
			$regions = $db->loadObjectList('id');

			// Add all option
			$all            = new stdClass();
			$all->id        = '*';
			$all->parent_id = -1;
			$all->name      = Text::_('JGLOBAL_FIELD_REGIONS_ALL');
			$all->access    = 'admin';
			$all->state     = 1;
			$all->level     = 1;
			array_unshift($regions, $all);

			// Filters
			$user      = Factory::getUser();
			$published = ((!$user->authorise('core.manage', 'com_location')));
			$access    = (!$user->authorise('core.admin')) ? $user->getAuthorisedViewLevels() : false;

			$options = parent::getOptions();
			foreach ($regions as $i => $region)
			{
				$option           = new stdClass();
				$option->value    = $region->id;
				$option->selected = ($option->value == $this->value);

				$option->text = $region->name;
				if (!$published || !$access)
				{
					$option->text = '[' . $region->id . '] ' . $option->text;
				}

				if ($region->level > 1)
				{
					$option->text = str_repeat('- ', ($region->level - 1)) . $option->text;
				}

				$option->disable = false;

				// Filter by published
				if ($published && $region->state != 1)
				{
					$option->disable = true;
				}

				// Filter by access
				if ($access && !in_array($region->access, $access))
				{
					$option->disable = true;
				}

				$options[] = $option;
			}

			$this->_options = $options;
		}

		return $this->_options;
	}
}
