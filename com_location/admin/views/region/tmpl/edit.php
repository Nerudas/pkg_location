<?php
/**
 * @package    Location Component
 * @version    1.0.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$doc = Factory::getDocument();

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::stylesheet('media/com_location/css/admin-region.min.css', array('version' => 'auto'));
HTMLHelper::_('script', '//api-maps.yandex.ru/2.1/?lang=ru-RU', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', 'media/com_location/js/admin-region.min.js', array('version' => 'auto'));

$doc->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "region.cancel" || document.formvalidator.isValid(document.getElementById("item-form")))
		{
			Joomla.submitform(task, document.getElementById("item-form"));
		}
	};
');
?>
<form action="<?php echo Route::_('index.php?option=com_location&view=region&id=' . $this->item->id); ?>"
	  method="post" name="adminForm" id="item-form" class="form-validate map-container" enctype="multipart/form-data">
	<div class="map-block">
		<div class="map-header form-inline form-inline-header">
			<?php echo $this->form->renderFieldset('header'); ?>
		</div>
		<div id="map">
			<?php echo HTMLHelper::image('media/com_location/images/target.png', '', array('class' => 'target')); ?>
		</div>
	</div>
	<div class="map-sidebar">
		<fieldset class="form-vertical">
			<?php echo $this->form->renderFieldset('global'); ?>
		</fieldset>
	</div>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="return" value="<?php echo $app->input->getCmd('return'); ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>