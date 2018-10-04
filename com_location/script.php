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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class com_locationInstallerScript
{
	/**
	 * Runs right after any installation action is preformed on the component.
	 *
	 * @return bool
	 *
	 * @since  1.0.0
	 */
	function postflight()
	{
		$path = '/components/com_location';
		$this->fixTables($path);
		$this->tagsIntegration();
		$this->createImageFolders();
		$this->createRootRegion();
		$this->moveLayouts($path);

		return true;
	}

	/**
	 * Move layouts folder
	 *
	 * @param string $path path to files
	 *
	 * @since 1.0.0
	 */
	protected function moveLayouts($path)
	{
		$component = JPATH_ADMINISTRATOR . $path . '/layouts';
		$layouts   = JPATH_ROOT . '/layouts' . $path;
		if (!JFolder::exists(JPATH_ROOT . '/layouts/components'))
		{
			JFolder::create(JPATH_ROOT . '/layouts/components');
		}
		if (JFolder::exists($layouts))
		{
			JFolder::delete($layouts);
		}
		JFolder::move($component, $layouts);
	}

	/**
	 * Create or image folders
	 *
	 * @since  1.0.0
	 */
	protected function createImageFolders()
	{
		$folders = array(
			'images/location',
			'images/location/regions',
		);


		foreach ($folders as $path)
		{
			$folder = JPATH_ROOT . '/' . $path;
			if (!JFolder::exists($folder))
			{
				JFolder::create($folder);
				JFile::write($folder . '/index.html', '<!DOCTYPE html><title></title>');
			}
		}
	}

	/**
	 * Create root region
	 *
	 * @since  1.0.0
	 */
	protected function createRootRegion()
	{
		$db = Factory::getDbo();
		// Category
		$query = $db->getQuery(true)
			->select('id')
			->from($db->quoteName('#__location_regions'))
			->where($db->quoteName('id') . ' = ' . $db->quote(-1));
		$db->setQuery($query);
		$current_id = $db->loadResult();

		$root            = new stdClass();
		$root->id        = -1;
		$root->parent_id = 0;
		$root->lft       = 0;
		$root->rgt       = 1;
		$root->level     = 0;
		$root->path      = '';
		$root->alias     = 'root';
		$root->access    = 1;
		$root->state     = 1;

		(!empty($current_id)) ? $db->updateObject('#__location_regions', $root, array('id'))
			: $db->insertObject('#__location_regions', $root);
	}


	/**
	 * Create or update tags integration
	 *
	 * @since  1.0.0
	 */
	protected function tagsIntegration()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('type_id')
			->from($db->quoteName('#__content_types'))
			->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_location.region'));
		$db->setQuery($query);
		$current_id = $db->loadResult();

		$region                                               = new stdClass();
		$region->type_id                                      = (!empty($current_id)) ? $current_id : '';
		$region->type_title                                   = 'Location Region';
		$region->type_alias                                   = 'com_location.region';
		$region->table                                        = new stdClass();
		$region->table->special                               = new stdClass();
		$region->table->special->dbtable                      = '#__location_regions';
		$region->table->special->key                          = 'id';
		$region->table->special->type                         = 'Regions';
		$region->table->special->prefix                       = 'LocationTable';
		$region->table->special->config                       = 'array()';
		$region->table->common                                = new stdClass();
		$region->table->common->dbtable                       = '#__ucm_content';
		$region->table->common->key                           = 'ucm_id';
		$region->table->common->type                          = 'Corecontent';
		$region->table->common->prefix                        = 'JTable';
		$region->table->common->config                        = 'array()';
		$region->table                                        = json_encode($region->table);
		$region->rules                                        = '';
		$region->field_mappings                               = new stdClass();
		$region->field_mappings->common                       = new stdClass();
		$region->field_mappings->common->core_content_item_id = 'id';
		$region->field_mappings->common->core_title           = 'name';
		$region->field_mappings->common->core_state           = 'state';
		$region->field_mappings->common->core_alias           = 'id';
		$region->field_mappings->common->core_created_time    = 'null';
		$region->field_mappings->common->core_modified_time   = 'null';
		$region->field_mappings->common->core_body            = 'null';
		$region->field_mappings->common->core_hits            = 'null';
		$region->field_mappings->common->core_publish_up      = 'null';
		$region->field_mappings->common->core_publish_down    = 'null';
		$region->field_mappings->common->core_access          = 'access';
		$region->field_mappings->common->core_params          = 'attribs';
		$region->field_mappings->common->core_featured        = 'null';
		$region->field_mappings->common->core_metadata        = 'null';
		$region->field_mappings->common->core_language        = 'null';
		$region->field_mappings->common->core_images          = 'null';
		$region->field_mappings->common->core_urls            = 'null';
		$region->field_mappings->common->core_version         = 'null';
		$region->field_mappings->common->core_ordering        = 'ordering';
		$region->field_mappings->common->core_metakey         = 'null';
		$region->field_mappings->common->core_metadesc        = 'null';
		$region->field_mappings->common->core_catid           = 'null';
		$region->field_mappings->common->core_xreference      = 'null';
		$region->field_mappings->common->asset_id             = 'null';
		$region->field_mappings->special                      = new stdClass();
		$region->field_mappings                               = json_encode($region->field_mappings);
		$region->router                                       = 'null';
		$region->content_history_options                      = '';

		(!empty($current_id)) ? $db->updateObject('#__content_types', $region, array('type_id'))
			: $db->insertObject('#__content_types', $region);
	}

	/**
	 *
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance $adapter The object responsible for running this script
	 *
	 * @since  1.0.0
	 */
	public function uninstall(JAdapterInstance $adapter)
	{
		$db = Factory::getDbo();
		// Remove content_type
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__content_types'))
			->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_location.region'));
		$db->setQuery($query)->execute();

		// Remove tag_map
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__contentitem_tag_map'))
			->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_location.region'));
		$db->setQuery($query)->execute();

		// Remove ucm_content
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__ucm_content'))
			->where($db->quoteName('core_type_alias') . ' = ' . $db->quote('com_location.region'));
		$db->setQuery($query)->execute();

		// Remove images
		JFolder::delete(JPATH_ROOT . '/images/location');

		// Remove layouts
		JFolder::delete(JPATH_ROOT . '/layouts/components/com_location');
	}


	/**
	 * Method to fix tables
	 *
	 * @param string $path path to component directory
	 *
	 * @since  1.0.0
	 */
	protected function fixTables($path)
	{
		$file = JPATH_ADMINISTRATOR . $path . '/sql/install.mysql.utf8.sql';
		if (!empty($file))
		{
			$sql = JFile::read($file);

			if (!empty($sql))
			{
				$db      = Factory::getDbo();
				$queries = $db->splitSql($sql);
				foreach ($queries as $query)
				{
					$db->setQuery($db->convertUtf8mb4QueryToUtf8($query));
					try
					{
						$db->execute();
					}
					catch (JDataBaseExceptionExecuting $e)
					{
						JLog::add(Text::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $e->getMessage()),
							JLog::WARNING, 'jerror');
					}
				}
			}
		}
	}
}