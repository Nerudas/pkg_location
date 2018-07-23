<?php
/**
 * @package    Location Component
 * @version    1.0.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::stylesheet('media/com_location/css/admin-geolocations.min.css', array('version' => 'auto'));

$app       = Factory::getApplication();
$doc       = Factory::getDocument();
$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$columns = 9;

?>

<form action="<?php echo Route::_('index.php?option=com_location&view=geolocations'); ?>" method="post" name="adminForm"
	  id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php echo LayoutHelper::render('joomla.searchtools.default',
			array('view' => $this, 'options' => array('filtersHidden' => false))); ?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table id="itemList" class="table table-striped">
				<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th width="2%" style="min-width:100px" class="center">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'g.state', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_LOCATION_GEOLOCATION_COUNTRY', 'g.country', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_LOCATION_GEOLOCATION_DISTRICT', 'g.district', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_LOCATION_GEOLOCATION_REGION', 'g.region', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_LOCATION_GEOLOCATION_CITY', 'g.city', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_CREATED_DATE', 'g.created', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone center">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'g.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="<?php echo $columns; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>

				<?php foreach ($this->items as $i => $item) :
					$canEdit = $user->authorise('core.edit', '#__location_geolocations.' . $item->id);
					$canChange = $user->authorise('core.edit.state', '#__location_geolocations' . $item->id);
					?>
					<tr>
						<td class="center">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<a class="btn btn-micro hasTooltip" title="<?php echo Text::_('JACTION_EDIT'); ?>"
								   href="<?php echo Route::_('index.php?option=com_location&task=geolocation.edit&id=' . $item->id); ?>">
									<span class="icon-apply icon-white"></span>
								</a>
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'geolocations.', $canChange, 'cb', $item->created); ?>
								<?php
								if ($canChange)
								{
									HTMLHelper::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'geolocations');
									echo HTMLHelper::_('actionsdropdown.render', $this->escape($item->title));
								}
								?>
							</div>
						</td>
						<td>
							<?php echo $this->escape($item->country); ?>
						</td>
						<td>
							<?php echo $this->escape($item->district); ?>
						</td>
						<td>
							<?php echo $this->escape($item->region); ?>
						</td>
						<td>
							<?php echo $this->escape($item->city); ?>
						</td>

						<td class="nowrap small hidden-phone">
							<?php echo $item->created > 0 ? HTMLHelper::_('date', $item->created,
								Text::_('DATE_FORMAT_LC2')) : '-' ?>
						</td>
						<td class="hidden-phone center">
							<?php echo $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
