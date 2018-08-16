/*
 * @package    Location Component
 * @version    1.0.1
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

(function ($) {
	$(document).ready(function () {
		var mapContainer = $('.map-container'),
			coordinates = [$('#jform_latitude').val(), $('#jform_longitude').val()];
		if (mapContainer.length > 0) {
			$(mapContainer).outerHeight($(window).outerHeight() - $(mapContainer).offset().top - 10);
			$('#map').outerHeight($(mapContainer).outerHeight());
		}

		var title = $('#jform_country').val();
		if ($('#jform_city').val() != '' && $('#jform_city').val() != '-') {
			title = $('#jform_city').val();
		}


		ymaps.ready(function () {
			// Map object
			var map = new ymaps.Map('map', {
				center: coordinates,
				zoom: 10,
				controls: ['zoomControl']
			});
			map.behaviors.disable('drag');
			map.behaviors.disable('scrollZoom');

			map.geoObjects.add(new ymaps.Placemark(coordinates, {
				iconCaption: title
			}, {
				preset: 'islands#redStarIcon',
			}))
		});
	});
})(jQuery);