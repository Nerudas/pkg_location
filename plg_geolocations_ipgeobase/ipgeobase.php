<?php
/**
 * @package    Geolocations - IPGeoBase Plugin
 * @version    1.1.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Registry\Registry;

jimport('joomla.filesystem.file');

class plgGeolocationsIPGeoBase extends CMSPlugin
{
	/**
	 *  Visitor Region
	 *
	 * @var    bool| Registry
	 * @since  1.0.0
	 */
	protected $_geoData = null;

	/**
	 * Method to get geoDate
	 *
	 * @return bool| Registry
	 *
	 * @since 1.0.0
	 */
	public function getGeoData()
	{
		if ($this->_geoData == null)
		{
			try
			{
				$ip = $_SERVER['REMOTE_ADDR'];
				if (!empty($_SERVER['HTTP_CLIENT_IP']))
				{
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				}
				if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
				{
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				}

				$api = @simplexml_load_file('http://ipgeobase.ru:7020/geo?ip=' . $ip);
				if (empty($api->ip))
				{
					$this->_geoData = false;

					return $this->_geoData;
				}

				$apiData = new Registry($api->ip);

				$this->_geoData = new Registry(array(
					'country'   => $apiData->get('country', '-'),
					'district'  => $apiData->get('district', '-'),
					'region'    => $apiData->get('region', '-'),
					'city'      => $apiData->get('city', '-'),
					'latitude'  => $apiData->get('lat', 0),
					'longitude' => $apiData->get('lng', 0),
				));
			}
			catch (Exception $e)
			{
				$this->_geoData = false;
			}
		}

		return $this->_geoData;
	}
}