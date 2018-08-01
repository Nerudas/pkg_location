<?php
/**
 * @package    Location - Regions Module
 * @version    1.0.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;

// Load Language
$language = Factory::getLanguage();
$language->load('com_location', JPATH_SITE, $language->getTag(), false);

// Initialize model
BaseDatabaseModel::addIncludePath(JPATH_ROOT . '/components/com_location/models', 'LocationModel');
$model = BaseDatabaseModel::getInstance('Regions', 'LocationModel', array('ignore_request' => true));
if ((!Factory::getUser()->authorise('core.edit.state', 'com_location')) &&
	(!Factory::getUser()->authorise('core.edit', 'com_location')))
{
	$model->setState('filter.published', 1);
}
else
{
	$model->setState('filter.published', array(0, 1));
}

$current = $model->getVisitorRegion();
$new     = ($app->input->cookie->get('region_new', false));

require ModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));