<?php
/**
 * @package    Location - Regions Module
 * @version    1.1.2
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('script', 'media/com_location/js/regions.min.js', array('version' => 'auto'));
HTMLHelper::_('script', 'media/mod_location_regions/js/ajax.min.js', array('version' => 'auto'));
HTMLHelper::_('script', 'media/mod_location_regions/js/default.min.js', array('version' => 'auto'));

//echo '<pre>', var_dump($new), '</pre>';
//echo '<pre>', print_r($current, true), '</pre>';

if ($new)
{
	echo Text::sprintf('MOD_LOCATION_REGIONS_NEW', $current->name);
}
?>

<div data-mod-location-regions="<?php echo $module->id; ?>">
	<h2><?php echo Text::sprintf('MOD_LOCATION_REGIONS_CURRENT', $current->name); ?></h2>
	<h3><?php echo Text::_('MOD_LOCATION_REGIONS_SELECT'); ?></h3>
	<div class="success"></div>
	<div class="loading">L</div>
	<div class="error"></div>
	<div class="items"></div>
	<div>
		<a class="refresh">R</a>
	</div>
</div>
