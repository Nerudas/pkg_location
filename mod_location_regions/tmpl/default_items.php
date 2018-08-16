<?php
/**
 * @package    Location - Regions Module
 * @version    1.0.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

defined('_JEXEC') or die;

?>
<div>
	<?php foreach ($items as $item): ?>
		<div>
			<a data-location-set-region="<?php echo $item->id; ?>">
				<?php echo ($item->level > 1) ? str_repeat('- ', ($item->level - 1)) . $item->name : $item->name; ?>
			</a>
		</div>
	<?php endforeach; ?>
</div>