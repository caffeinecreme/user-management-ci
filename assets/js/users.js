let userTable;

$(document).ready(function () {
	initDataTable();
	setupEventListeners();
});

function initDataTable() {
	userTable = $("#userTable").DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: BASE_URL + "users/get_users",
			type: "GET",
			dataSrc: function (json) {
				return json.data;
			},
		},
		columns: [
			{ data: "id" },
			{ data: "name" },
			{ data: "email" },
			{ data: "phone" },
			{ data: "username" },
			{
				data: "actions",
				orderable: false,
				searchable: false,
			},
		],
		order: [[0, "asc"]],
		responsive: true,
		language: {
			processing:
				'<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
			emptyTable: "No users found",
			zeroRecords: "No matching users found",
			info: "Showing _START_ to _END_ of _TOTAL_ users",
			infoEmpty: "Showing 0 to 0 of 0 users",
			infoFiltered: "(filtered from _MAX_ total users)",
		},
		pageLength: 10,
		lengthMenu: [
			[5, 10, 25, 50, -1],
			[5, 10, 25, 50, "All"],
		],
	});
}

function showSpinner() {
	if ($("#loading-spinner").length === 0) {
		const spinner = `
            <div id="loading-spinner" class="position-fixed d-flex justify-content-center align-items-center" style="top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.7); z-index: 9999;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
		$("body").append(spinner);
	}
}

function hideSpinner() {
	$("#loading-spinner").fadeOut("fast", function () {
		$(this).remove();
	});
}
