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

use Joomla\CMS\Language\Text;

?>
<div class="body" style="font-family: sans-serif; font-size: 18px;">
	<table style="border-collapse: collapse; border-spacing: 0; width: 100%; margin: 15px 0; border: 1px solid #ddd;">
		<?php $i = 0;
		foreach ($displayData as $key => $value): ?>
			<tr <?php echo ($i % 2) ? '' : 'style="background: #fafafa;"'; ?>>
				<td style="padding: 8px 8px; border-bottom: 1px solid #ddd; text-align: left; vertical-align: top;">
					<?php if ($key != 'adminLink')
					{
						echo Text::_('COM_LOCATION_GEOLOCATION_' . $key);
					} ?>
				</td>
				<td style="padding: 8px 8px; border-bottom: 1px solid #ddd; text-align: left; vertical-align: top;">
					<?php if ($key == 'adminLink')
					{
						echo '<a href="' . $value . '">' . Text::_('COM_LOCATION_GEOLOCATION_ADMINLINK') . '</a>';
					}
					else
					{
						echo $value;
					} ?>
				</td>
			</tr>
			<?php $i++;
		endforeach; ?>
	</table>
</div>