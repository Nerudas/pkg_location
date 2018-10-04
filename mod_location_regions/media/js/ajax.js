/*
 * @package    Location - Regions Module
 * @version    1.1.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

function modLocationRegionsGetRegions(block) {
	(function ($) {
		var loading = block.find('.loading'),
			error = block.find('.error'),
			success = block.find('.success'),
			items = block.find('.items'),
			module_id = $(block).data('mod-location-regions');

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: '/index.php?option=com_ajax&module=location_regions&format=json',
			data: {module_id: module_id},
			beforeSend: function () {
				loading.show();
				items.hide();
				items.html('');
				error.hide();
				error.html('');
				success.hide();
				success.html('');
			},
			success: function (response) {
				if (response.success) {
					items.html(response.data);
					items.show();

					$(items).find('[data-location-set-region]').on('click', function () {
						$.ajax({
							type: 'POST',
							dataType: 'json',
							url: '/index.php?option=com_location&task=setRegion',
							cache: false,
							data: {region_id: $(this).data('location-set-region')},
							beforeSend: function () {
								loading.show();
								error.hide();
								error.html('');
								success.hide();
								success.html('');
							},
							complete: function () {
								loading.hide();
							},
							success: function (response) {
								if (response.success) {
									console.log(response);
									success.html(response.message);
									success.show();
									setTimeout(function () {
										window.location.reload()
									}, 1000)
								}
								else {
									error.html(response.message);
									error.show();
								}
							},
							error: function (response) {
								error.html(response.status + ': ' + response.statusText);
								error.show();
							}
						});
					});
				}
				else {
					error.html(response.message);
					error.show();
				}
			},
			complete: function () {
				loading.hide();
			},
			error: function (response) {
				error.html(response.status + ': ' + response.statusText);
				error.show();
			}
		});
	})(jQuery);
}