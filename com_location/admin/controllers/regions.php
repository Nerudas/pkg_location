<?php
/**
 * @package    Location Component
 * @version    1.1.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;

class LocationControllerRegions extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_LOCATION_REGIONS';

	/**
	 *
	 * Proxy for getModel.
	 *
	 * @param   string $name   The model name. Optional.
	 * @param   string $prefix The class prefix. Optional.
	 * @param   array  $config The array of possible config values. Optional.
	 *
	 * @return  JModelLegacy
	 *
	 * @since  1.0.0
	 */
	public function getModel($name = 'Region', $prefix = 'LocationModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Method to set the default for region.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function setDefault()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$pks = $this->input->post->get('cid', array(), 'array');

		try
		{
			if (empty($pks))
			{
				throw new Exception(Text::_('COM_LOCATION_ERROR_NO_REGIONS_SELECTED'));
			}

			$pks = ArrayHelper::toInteger($pks);

			// Pop off the first element.
			$id    = array_shift($pks);
			$model = $this->getModel();
			$model->setDefault($id);
			$this->setMessage(Text::_('COM_LOCATION_REGION_DEFAULT_SET'));
		}
		catch (Exception $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		$this->setRedirect('index.php?option=com_location&view=regions');
	}

	/**
	 * Method to set set show_all to one or more records.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function setShowAll()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$ids = $this->input->get('cid', array(), 'array');

		if (empty($ids))
		{
			JError::raiseWarning(500, Text::_('COM_LOCATION_ERROR_NO_REGIONS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Change the state of the records.
			if (!$model->setShowAll($ids))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				$this->setMessage(Text::plural('COM_LOCATION_REGIONS_N_ITEMS_SET_SHOW_ALL', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_location&view=regions');
	}

	/**
	 * Method to unset show_all to one or more records.
	 *
	 * @return  void
	 *
	 * @since   1.0.7
	 */
	public function unsetShowAll()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$ids = $this->input->get('cid', array(), 'array');

		if (empty($ids))
		{
			JError::raiseWarning(500, Text::_('COM_LOCATION_ERROR_NO_REGIONS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Change the state of the records.
			if (!$model->unsetShowAll($ids))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				$this->setMessage(Text::plural('COM_LOCATION_REGIONS_N_ITEMS_UNSET_SHOW_ALL', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_location&view=regions');
	}
}