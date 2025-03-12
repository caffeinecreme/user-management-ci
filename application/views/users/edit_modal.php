<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-light">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="edit_user_id" name="user_id">
                    <div class="mb-3">
                        <label for="edit_firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="edit_firstname" name="firstname" required>
                        <div class="invalid-feedback">First name is required</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lastname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="edit_lastname" name="lastname" required>
                        <div class="invalid-feedback">Last name is required</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                        <div class="invalid-feedback">Valid email is required</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                        <div class="invalid-feedback">Phone number is required</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                        <div class="invalid-feedback">Username is required</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateUserBtn">Update User</button>
            </div>
        </div>
    </div>
</div>