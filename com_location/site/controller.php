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

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class LocationController extends BaseController
{
	/**
	 * Method to set region
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setRegion()
	{
		$app = Factory::getApplication();

		$id = $app->input->get('region_id', 0);

		if (!empty($id))
		{
			$model = $this->getModel('Regions');
			if ($region = $model->getRegion($id))
			{
				$success = true;
				$message = Text::sprintf('COM_LOCATION_REGION_SET_SUCCESS', $region->name);

				// Set region cookie
				$name   = 'region';
				$value  = $region->id;
				$expire = Factory::getDate('now +1 year')->toUnix();
				$path   = rtrim(Uri::root(true), '/') . '/';

				$check_name   = 'region_check';
				$check_value  = true;
				$check_expire = Factory::getDate('now +' . Factory::getConfig()->get('lifetime') . ' minute')->toUnix();

				$app->input->cookie->set($name, $value, $expire, $path);
				$app->input->cookie->set($check_name, $check_value, $check_expire, $path);
			}
			else
			{
				$region  = false;
				$success = false;
				$message = Text::_('COM_LOCATION_ERROR_REGION_NOT_FOUND');
				foreach ($model->getErrors() as $error)
				{
					$message .= '<br/>' . Text::_($error);
				}
			}
		}
		else
		{
			$region  = false;
			$success = false;
			$message = Text::_('COM_LOCATION_ERROR_REGION_NOT_FOUND');
		}

		echo new JsonResponse($region, $message, !$success);

		$app->close();
	}
}