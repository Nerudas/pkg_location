<?php
/**
 * @package    System - Location Geolocation Plugin
 * @version    1.1.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;

class plgSystemLocation_Geolocation extends CMSPlugin
{
	/**
	 * Set region cookie
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onAfterInitialise()
	{
		$app = Factory::getApplication();
		if ($app->isSite())
		{
			BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_location/models', 'LocationModel');
			$model = BaseDatabaseModel::getInstance('Regions', 'LocationModel', array('ignore_request' => false));

			$name   = 'region';
			$value  = $app->input->cookie->get($name, -1);
			$expire = Factory::getDate('now +1 year')->toUnix();
			$path   = rtrim(Uri::root(true), '/') . '/';

			$check_name   = 'region_check';
			$check_value  = $app->input->cookie->get($check_name, false);
			$check_expire = Factory::getDate('now +' . Factory::getConfig()->get('lifetime') . ' minute')->toUnix();

			// Set new region
			if (empty($value) || $value == -1 || $value == 'undefined' || (!$check_value && !$model->getRegion($value)))
			{
				$value = $model->getVisitorRegion()->id;
				$app->input->cookie->set('region_new', true, Factory::getDate('now +10 second')->toUnix(), $path);
			}

			// Set region cookie
			$check_value = true;
			$app->input->cookie->set($check_name, $check_value, $check_expire, $path);
			$app->input->cookie->set($name, $value, $expire, $path);
		}
	}
}
