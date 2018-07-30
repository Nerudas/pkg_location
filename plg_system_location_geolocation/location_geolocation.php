<?php
/**
 * @package    System - Location Geolocation Plugin
 * @version    1.0.0
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
			$name   = 'region';
			$value  = $app->input->cookie->get($name, -1);
			$expire = Factory::getDate('now +1 year')->toUnix();
			$path   = rtrim(Uri::root(true), '/') . '/';

			if (empty($value) || $value == -1 || $value == 'undefined')
			{
				BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_location/models', 'LocationModel');
				$model = BaseDatabaseModel::getInstance('Regions', 'LocationModel', array('ignore_request' => true));
				$value = $model->getVisitorRegion()->id;

				$app->input->cookie->set('new_region', $value, Factory::getDate('now +10 second')->toUnix(), $path);
			}

			$app->input->cookie->set($name, $value, $expire, $path);
		}
	}
}
