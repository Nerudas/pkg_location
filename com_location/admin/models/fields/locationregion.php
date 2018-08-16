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
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Factory;

FormHelper::loadFieldClass('list');

class JFormFieldLocationRegion extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'locationRegion';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since  1.0.0
	 */
	protected function getOptions()
	{
		$app = Factory::getApplication();

		// Get regions
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('r.id', 'r.parent_id', 'r.name', 'r.level'))
			->from($db->quoteName('#__location_regions', 'r'))
			->where($db->quoteName('r.alias') . ' <>' . $db->quote('root'));

		$query->order($db->escape('r.lft') . ' ' . $db->escape('asc'));

		$db->setQuery($query);
		$regions = $db->loadObjectList();

		// Prepare options
		$check     = false;
		$component = $app->input->get('option', 'com_location');
		$view      = $app->input->get('view', 'region');
		$id        = $app->input->getInt('id', 0);

		if ($app->isAdmin() && $component == 'com_location' && $view == 'region')
		{
			$check = true;
		}
		$options = parent::getOptions();
		foreach ($regions as $i => $region)
		{
			$option        = new stdClass();
			$option->value = $region->id;
			$option->text  = $region->name;
			if (empty($option->text))
			{
				$option->text = $region->alias;
			}

			if ($region->level > 1)
			{
				$option->text = str_repeat('- ', ($region->level - 1)) . $option->text;
			}

			if ($check && $id !== 0 && ($region->id == $id || $region->parent_id == $id))
			{
				$option->disable = true;
			}

			if ($id == 0 && $region->id = 1)
			{
				$option->selected = true;
			}

			$options[] = $option;
		}

		return $options;
	}
}
