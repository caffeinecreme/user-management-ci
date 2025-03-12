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

function setupEventListeners() {
	$("#saveUserBtn").on("click", function () {
		submitAddUserForm();
	});

	$(document).on("click", ".edit-user", function () {
		const userId = $(this).data("id");
		loadUserForEditing(userId);
	});

	$("#updateUserBtn").on("click", function () {
		submitUpdateUserForm();
	});

	$(document).on("click", ".delete-user", function () {
		const userId = $(this).data("id");
		const userName = $(this).data("name");
		showDeleteConfirmation(userId, userName);
	});

	$("#confirmDeleteBtn").on("click", function () {
		const userId = $("#delete_user_id").val();
		deleteUser(userId);
	});

	$("#addUserModal, #editUserModal").on("hidden.bs.modal", function () {
		resetFormValidation($(this).find("form"));
	});
}

function submitAddUserForm() {
	const form = $("#addUserForm");

	if (!validateForm(form)) {
		return;
	}

	$("#saveUserBtn").html(
		'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
	);
	$("#saveUserBtn").prop("disabled", true);

	const formData = {
		firstname: $("#firstname").val(),
		lastname: $("#lastname").val(),
		email: $("#email").val(),
		phone: $("#phone").val(),
		username: $("#username").val(),
		password: $("#password").val(),
	};

	$.ajax({
		url: BASE_URL + "users/add_user",
		type: "POST",
		data: formData,
		dataType: "json",
		success: function (response) {
			$("#saveUserBtn").html("Save User");
			$("#saveUserBtn").prop("disabled", false);

			if (response.status === "success") {
				$("#addUserModal").modal("hide");
				form[0].reset();

				userTable.ajax.reload();

				showAlert("success", response.message);
			} else {
				showAlert("danger", response.message);
			}
		},
		error: function (xhr, status, error) {
			$("#saveUserBtn").html("Save User");
			$("#saveUserBtn").prop("disabled", false);

			showAlert(
				"danger",
				"An error occurred while saving the user. Please try again."
			);
			console.error(xhr.responseText);
		},
	});
}

function validateForm(form) {
	let isValid = true;

	form.find("input[required]").each(function () {
		const input = $(this);

		if (input.val().trim() === "") {
			markInvalid(input);
			isValid = false;
		} else {
			markValid(input);

			if (input.attr("type") === "email" && !validateEmail(input.val())) {
				markInvalid(input);
				isValid = false;
			}

			if (input.attr("id") === "password" && input.val().length < 6) {
				markInvalid(input);
				isValid = false;
			}
		}
	});

	return isValid;
}

function validateEmail(email) {
	const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	return re.test(email);
}

function markInvalid(input) {
	input.addClass("is-invalid").removeClass("is-valid");
}

function markValid(input) {
	input.addClass("is-valid").removeClass("is-invalid");
}

function resetFormValidation(form) {
	form.find("input, select, textarea").removeClass("is-invalid is-valid");
	form[0].reset();
}

function showAlert(type, message) {
	const alertId = "alert-" + Date.now();
	const alertHTML = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

	$(".alert-container").append(alertHTML);

	setTimeout(function () {
		$("#" + alertId).fadeOut("slow", function () {
			$(this).remove();
		});
	}, 5000);
}

function loadUserForEditing(userId) {
	showSpinner();

	$.ajax({
		url: BASE_URL + "users/get_user/" + userId,
		type: "GET",
		dataType: "json",
		success: function (response) {
			hideSpinner();

			if (response.status === "success") {
				const user = response.user;

				$("#edit_user_id").val(user.id);
				$("#edit_firstname").val(user.name.firstname);
				$("#edit_lastname").val(user.name.lastname);
				$("#edit_email").val(user.email);
				$("#edit_phone").val(user.phone);
				$("#edit_username").val(user.username);

				$("#editUserModal").modal("show");
			} else {
				showAlert("danger", response.message);
			}
		},
		error: function (xhr, status, error) {
			hideSpinner();

			showAlert(
				"danger",
				"An error occurred while loading user data. Please try again."
			);
			console.error(xhr.responseText);
		},
	});
}

function submitUpdateUserForm() {
	const form = $("#editUserForm");

	if (!validateForm(form)) {
		return;
	}

	$("#updateUserBtn").html(
		'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...'
	);
	$("#updateUserBtn").prop("disabled", true);

	const formData = form.serialize();

	$.ajax({
		url: BASE_URL + "users/update_user",
		type: "POST",
		data: formData,
		dataType: "json",
		success: function (response) {
			$("#updateUserBtn").html("Update User");
			$("#updateUserBtn").prop("disabled", false);

			if (response.status === "success") {
				$("#editUserModal").modal("hide");

				userTable.ajax.reload();

				showAlert("success", response.message);
			} else {
				showAlert("danger", response.message);
			}
		},
		error: function (xhr, status, error) {
			$("#updateUserBtn").html("Update User");
			$("#updateUserBtn").prop("disabled", false);

			showAlert(
				"danger",
				"An error occurred while updating the user. Please try again."
			);
			console.error(xhr.responseText);
		},
	});
}

function showDeleteConfirmation(userId, userName) {
	$("#delete_user_id").val(userId);
	$("#delete_user_name").text(userName);
	$("#deleteUserModal").modal("show");
}

function deleteUser(userId) {
	$("#confirmDeleteBtn").html(
		'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...'
	);
	$("#confirmDeleteBtn").prop("disabled", true);

	$.ajax({
		url: BASE_URL + "users/delete_user/" + userId,
		type: "POST",
		dataType: "json",
		success: function (response) {
			$("#confirmDeleteBtn").html("Delete");
			$("#confirmDeleteBtn").prop("disabled", false);

			$("#deleteUserModal").modal("hide");

			if (response.status === "success") {
				userTable.ajax.reload();

				showAlert("success", response.message);
			} else {
				showAlert("danger", response.message);
			}
		},
		error: function (xhr, status, error) {
			$("#confirmDeleteBtn").html("Delete");
			$("#confirmDeleteBtn").prop("disabled", false);

			$("#deleteUserModal").modal("hide");

			showAlert(
				"danger",
				"An error occurred while deleting the user. Please try again."
			);
			console.error(xhr.responseText);
		},
	});
}
