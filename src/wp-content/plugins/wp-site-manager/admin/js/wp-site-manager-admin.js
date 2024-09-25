(function ($) {
	'use strict';
	$(document).ready(function ($) {
		if ($('#wp-site-manager-table').length) {
			$.ajax({
				url: wp_site_manager_data.ajax_url,
				type: 'post',
				data: {
					action: 'wp_site_manager_fetch_data'
				},
				success: function (response) {
					if (response.success) {
						var table = '<table class="cell-border hover">';
						table += `<thead>
							<tr>
								<th class="col-name">Name</th>
								<th class="col-slug">Slug</th>
								<th class="col-version">Version</th>
								<th class="col-size">Size</th>
								<th class="col-type">Type</th>
								<th class="col-author">Home</th>
								<th class="col-updated">Uploaded</th>
								<th class="col-action">Action</th>
							</tr>
						</thead>`;
						table += '<tbody>';

						$.each(response.data, function (index, item) {
							table += `<tr>
								<td class="col-name">${item.name}</td>
								<td class="col-slug">${item.slug}</td>
								<td class="col-version">${item.latest}</td>
								<td class="col-size">${item.size}</td>
								<td class="col-type">${item.type}</td>
								<td class="col-author"><a href="${item.homepage}" target="_blank"><span class="dashicons dashicons-admin-site"></span></a></td>
								<td class="col-updated">${item.last_updated.substring(0, 10)}</td>
								<td class="col-action">${item.buttons}</a></td>
							</tr>`;
						});
						table += '</tbody></table>';
						$('#wp-site-manager-table').html(table);
						new DataTable('#wp-site-manager-table table', {
							lengthMenu: [
								[20, 30, 40, -1],
								[20, 30, 40, 'All']
							],
						});

					} else {
						console.log('Error:', response.data);
					}
				},
				error: function (xhr, status, error) {
					console.log('AJAX Error:', status, error);
				}
			});
		};

		$(document).on('click tab', '#wp-site-manager-table td.col-action a.button', function (e) {
			e.preventDefault();

			var button = $(this);
			var cell = button.closest('td');
			var text = cell.html();
			cell.html('<span class="spinner is-active"></span>');

			var url = button.prop('href');
			var queryString = url.split('?')[1];
			if (!queryString) return false;

			var params = {};
			var keyValuePairs = queryString.split('&');

			keyValuePairs.forEach(function (keyValuePair) {
				var keyValue = keyValuePair.split('=');
				var key = decodeURIComponent(keyValue[0]);
				var value = decodeURIComponent(keyValue[1] || '');
				params[key] = value;
			});

			params['action'] = 'wp_site_manager_' + params['action'];

			$.ajax({
				url: wp_site_manager_data.ajax_url,
				type: 'post',
				data: params,
				success: function (response) {
					var jsonData = {};
					if (typeof response === 'string') {
						var startIndex = response.indexOf('{"success"');
						if (startIndex !== -1) {
							var jsonSubstring = response.substring(startIndex);
							jsonData = JSON.parse(jsonSubstring);
						}
					} else if (typeof response === 'object') {
						jsonData = response;
					}
					if (jsonData.hasOwnProperty('success') && jsonData.success) {
						cell.html(jsonData.data.buttons);
					} else {
						button.html(text);
					}
				},
				error: function (xhr, status, error) {
					console.log('AJAX Error:', status, error);
					cell.html(text);
				}
			});

			return false;
		});
	});
})(jQuery);
