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
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::stylesheet('media/com_location/css/admin-regions.css', array('version' => 'auto'));

$app       = Factory::getApplication();
$doc       = Factory::getDocument();
$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$saveOrder = ($listOrder == 'r.lft' && strtolower($listDirn) == 'asc');
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_location&task=regions.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'regionsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}
$columns = 8;

?>

<form action="<?php echo Route::_('index.php?option=com_location&view=regions'); ?>" method="post" name="adminForm"
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
			<table id="regionsList" class="table table-striped">
				<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', '', 'r.lft', $listDirn,
							$listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="1%" class="center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th width="2%" style="min-width:100px" class="center">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'r.state', $listDirn, $listOrder); ?>
					</th>
					<th style="min-width:100px" class="nowrap">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_LOCATION_REGION_NAME', 'r.name', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('searchtools.sort', 'COM_LOCATION_REGION_DEFAULT', 'r.default', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('searchtools.sort', 'COM_LOCATION_REGION_SHOW_ALL', 'r.show_all', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'r.access', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone center">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'r.id', $listDirn, $listOrder); ?>
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
					$orderkey = array_search($item->id, $this->ordering[$item->parent_id]);
					$canEdit = $user->authorise('core.edit', '#__location_regions.' . $item->id);
					$canChange = $user->authorise('core.edit.state', '#__location_regions' . $item->id);
					$item->parents = '';

					// Get the parents of item for sorting
					if ($item->level > 1)
					{
						$parentsStr       = '';
						$_currentParentId = $item->parent_id;
						$parentsStr       = ' ' . $_currentParentId;
						for ($i2 = 0; $i2 < $item->level; $i2++)
						{
							foreach ($this->ordering as $k => $v)
							{
								$v = implode('-', $v);
								$v = '-' . $v . '-';
								if (strpos($v, '-' . $_currentParentId . '-') !== false)
								{
									$parentsStr       .= ' ' . $k;
									$_currentParentId = $k;
									break;
								}
							}
						}
					}
					else
					{
						$parentsStr = '';
					}
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->parent_id; ?>"
						item-id="<?php echo $item->id ?>" parents="<?php echo $parentsStr ?>"
						level="<?php echo $item->level ?>">
						<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::_('tooltipText', 'JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler<?php echo $iconClass ?>"><span
										class="icon-menu"></span></span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display:none" name="order[]" size="5"
									   value="<?php echo $orderkey + 1; ?>"/>
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<a class="btn btn-micro hasTooltip" title="<?php echo Text::_('JACTION_EDIT'); ?>"
								   href="<?php echo Route::_('index.php?option=com_location&task=region.edit&id=' . $item->id); ?>">
									<span class="icon-apply icon-white"></span>
								</a>
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'regions.', $canChange, 'cb'); ?>
								<?php
								// Create dropdown items and render the dropdown list.
								if ($canChange)
								{
									HTMLHelper::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'regions');
									echo HTMLHelper::_('actionsdropdown.render', $this->escape($item->name));
								}
								?>
							</div>
						</td>
						<td>
							<div>
								<?php echo LayoutHelper::render('joomla.html.treeprefix', array('level' => $item->level)); ?>
								<?php if ($canEdit) : ?>
									<a class="hasTooltip" title="<?php echo Text::_('JACTION_EDIT'); ?>"
									   href="<?php echo Route::_('index.php?option=com_location&task=region.edit&id=' . $item->id); ?>">
										<?php echo $this->escape($item->name); ?>
									</a>
								<?php else : ?>
									<?php echo $this->escape($item->name); ?>
								<?php endif; ?>
								<span class="small break-word">
									<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
								</span>
							</div>
						</td>
						<td class="center">
							<?php echo HTMLHelper::_('jgrid.isdefault', $item->default != '0', $i, 'regions.', $canChange && $item->default != '1'); ?>
						</td>
						<td class="center">
							<?php if ($item->show_all): ?>
								<a class="hasTooltip btn btn-micro jgrid "
								   title="<?php echo Text::_('COM_LOCATION_REGION_UNSET_SHOW_ALL'); ?>"
								   onclick="return listItemTask('cb<?php echo $item->id; ?>','regions.unsetShowAll')"><span
											class="icon-eye text-success" aria-hidden="true"></span></a>
							<?php else: ?>
								<a class="btn btn-micro hasTooltip"
								   title="<?php echo Text::_('COM_LOCATION_REGION_SET_SHOW_ALL'); ?>"
								   onclick="return listItemTask('cb<?php echo $item->id; ?>','regions.setShowAll')"><span
											class="icon-eye-2 muted" aria-hidden="true"></span></a>
							<?php endif; ?>
						</td>
						<td class="small hidden-phone">
							<?php echo $this->escape($item->access_level); ?>
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