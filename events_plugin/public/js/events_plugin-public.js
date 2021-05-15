(function ($) {
	'use strict';

	$(function () {
		$(document).on('click', '.events__loadmore', function () {

			let button = $(this),
				data = {
					'action': 'loadmore',
					'query': ajax_params.posts,
					'page': ajax_params.current_page
				};

			$.ajax({
				url: ajax_params.ajaxurl,
				data: data,
				type: 'POST',
				success: function (data) {
					if (data) {
						$('#events__wrapper').append(data);
						ajax_params.current_page++;

						if (ajax_params.current_page == ajax_params.max_page) {
							button.remove();
						}
					} else {
						button.remove();
					}
				}
			});
		});

		$('.filters__item a').on('click', function (e) {
			e.preventDefault();
			$('.filters__item').removeClass('active');
			$(this).addClass('active');

			let data = {
				'action': 'filter',
				'type_evenement': $(this).data('slug'),
			};

			$.ajax({
				url: ajax_params.ajaxurl,
				data: data,
				type: 'POST',
				success: function (data) {
					$('#events__wrapper').html(data);
					ajax_params.current_page = 1;
				}
			});
		});
	});

})(jQuery);
