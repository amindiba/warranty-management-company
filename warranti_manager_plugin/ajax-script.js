jQuery(document).ready(function ($) {
	$('form').on('submit', function (e) {
		e.preventDefault(); // جلوگیری از ارسال پیش‌فرض فرم

		var serialNumber = $('#serial_number').val();

		$.ajax({
			url: ajax_object.ajax_url,
			type: 'POST',
			data: {
				action: 'warranty_search',
				serial_number: serialNumber
			},
			success: function (response) {
				if (response.success) {
					showPopup(response.data);
				} else {
					showPopup(response.message);
				}
			},
			error: function () {
				alert('خطا در ارسال درخواست. لطفا دوباره امتحان کنید.');
			}
		});
	});

	function showPopup(content) {
		$('body').append('<div id="warrantyPopup" class="popup"><div class="popup-content"><span class="close" onclick="closePopup()">&times;</span><div class="popup-body">' + content + '</div></div></div>');
		$('#warrantyPopup').show();

		// بستن پاپ آپ با دکمه Esc
		$(document).on('keydown', function (event) {
			if (event.key === 'Escape') {
				closePopup();
			}
		});
	}

	window.closePopup = function () {
		$('#warrantyPopup').remove();
	};
});
