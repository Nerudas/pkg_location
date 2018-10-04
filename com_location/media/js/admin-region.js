/*
 * @package    Location Component
 * @version    1.1.2
 * @author     Nerudas  - nerudas.ru
 * @copyright  Copyright (c) 2013 - 2018 Nerudas. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://nerudas.ru
 */

(function ($) {
	$(document).ready(function () {
		var mapContainer = $('.map-container'),
			coordinates = [$('#jform_latitude').val(), $('#jform_longitude').val()],
			zoom = $('#jform_zoom').val();
		if (mapContainer.length > 0) {
			$(mapContainer).outerHeight($(window).outerHeight() - $(mapContainer).offset().top - 10);
			$('#map').outerHeight($(mapContainer).outerHeight() - $('.map-container .map-header').outerHeight());
		}

		ymaps.ready(function () {
			var map = new ymaps.Map('map', {
				center: coordinates,
				zoom: zoom,
				controls: ['zoomControl', 'fullscreenControl', 'searchControl', 'geolocationControl']
			});

			var searchControl = map.controls.get('searchControl');
			searchControl.options.set('noPlacemark', true);

			// On change map bounds
			map.events.add('boundschange', function (event) {
				//  Change zoom
				if (event.get('newZoom') != event.get('oldZoom')) {
					$('#jform_zoom').val(event.get('newZoom'));
				}

				//  Change center
				if (event.get('newCenter') != event.get('oldCenter')) {
					$('#jform_latitude').val(event.get('newCenter')[0].toFixed(6));
					$('#jform_longitude').val(event.get('newCenter')[1].toFixed(6));
				}
			});
		});
	});
})(jQuery);