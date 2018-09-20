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

use Joomla\CMS\Form\Form;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

JLoader::register('LocationModelGeolocation', JPATH_ADMINISTRATOR . '/components/com_location/models/geolocation.php');

class LocationModelGeolocationForm extends LocationModelGeolocation
{
	/**
	 * Method to save the form data.
	 *
	 * @param   array $data The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since  1.0.0
	 */
	public function save($data)
	{
		$pk    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
		$isNew = ($pk == 0);

		if ($id = parent::save($data))
		{
			if ($isNew)
			{
				$language = Factory::getLanguage();
				$language->load('com_location', JPATH_SITE, $language->getTag(), true);

				$subject = Text::_('COM_LOCATION_NEW_GEOLOCATION_MAIL_SUBJECT');

				$layoutData              = $data;
				$layoutData['adminLink'] = Uri::root() . 'administrator/index.php?option=com_location&task=geolocation.edit&id=' . $id;
				$body                    = LayoutHelper::render('components.com_location.mail.newgeolocation', $layoutData);

				$siteConfig      = Factory::getConfig();
				$componentConfig = ComponentHelper::getParams('com_location');

				$sender = array($siteConfig->get('mailfrom'), $siteConfig->get('sitename'));

				$recipient = explode(',', $componentConfig->get('admin_email', $siteConfig->get('mailfrom')));

				$mail = Factory::getMailer();
				$mail->setSubject($subject);
				$mail->setSender($sender);
				$mail->addRecipient($recipient);
				$mail->setBody($body);
				$mail->isHtml(true);
				$mail->Encoding = 'base64';
				$mail->send();
			}

			return $id;
		}

		return false;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   string          $name    The name of the form.
	 * @param   string          $source  The form source. Can be XML string if file flag is set to false.
	 * @param   array           $options Optional array of options for the form creation.
	 * @param   boolean         $clear   Optional argument to force load a new form.
	 * @param   string| boolean $xpath   An optional xpath to search for the fields.
	 *
	 * @return  Form|boolean  Form object on success, false on error.
	 *
	 * @see     Form
	 *
	 * @since   1.0.0
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = ArrayHelper::getValue((array) $options, 'control', false);

		// Create a signature hash. But make sure, that loading the data does not create a new instance
		$sigoptions = $options;

		if (isset($sigoptions['load_data']))
		{
			unset($sigoptions['load_data']);
		}

		$hash = md5($source . serialize($sigoptions));

		// Check if we can use a previously loaded form.
		if (!$clear && isset($this->_forms[$hash]))
		{
			return $this->_forms[$hash];
		}

		// Get the form.
		Form::addFormPath(JPATH_SITE . '/components/com_location/models/forms');
		Form::addFieldPath(JPATH_SITE . '/components/com_location/models/fields');

		try
		{
			$form = Form::getInstance($name, $source, $options, false, $xpath);

			if (isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = $this->loadFormData();
			}
			else
			{
				$data = array();
			}

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);

			// Load the data into the form after the plugins have operated.
			$form->bind($data);
		}
		catch (\Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Store the form for later.
		$this->_forms[$hash] = $form;

		return $form;
	}
}