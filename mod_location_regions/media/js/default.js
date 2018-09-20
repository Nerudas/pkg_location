/*
 * @package    Location - Regions Module
 * @version    1.1.0
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

(function ($) {
	$(document).ready(function () {
		$('[data-mod-location-regions]').each(function () {
			var block = $(this);
			modLocationRegionsGetRegions(block);
			$(block).find('a.refresh').on('click', function () {
				modLocationRegionsGetRegions(block);
			});
		});
	});
})(jQuery);