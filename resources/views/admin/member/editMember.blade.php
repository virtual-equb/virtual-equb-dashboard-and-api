<div class="modal fade" id="editMemberModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="post" class="form-horizontal form-group" action="" id="updateMember" enctype="multipart/form-data">
                {{ csrf_field() }}
                @method('put')
                <input type="hidden" id='m_id' name="m_id" value="">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Member</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="profilePicPreview" src="path/to/default/profile/pic.png" alt="Profile Picture" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        <div class="form-group mt-2">
                            <label for="profile_picture" class="control-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group required">
                            <label class="control-label">Full Name</label>
                            <input type="text" class="form-control" id="update_full_name" name="full_name" placeholder="Full Name" required>
                        </div>
                        <div class="form-group required">
                            <label class="control-label">Phone</label>
                            <input type="text" class="form-control" id="update_phone" name="phone" placeholder="251911121314" required>
                        </div>
                        <div class="form-group" id="addEmail">
                            <label class="control-label">Email</label>
                            <input type="text" class="form-control" id="update_email" name="email" placeholder="Email">
                        </div>
                        <div class="form-group required">
                            <label class="control-label">Gender</label>
                            <select class="form-control" id="update_gender" name="gender">
                                <option value="Female">Female</option>
                                <option value="Male">Male</option>
                            </select>
                        </div>
                        <hr>
                        <label class="control-label">Address</label>
                        <div class="col-12 row">
                            <div class="form-group required col-6" id="addCity">
                                <label class="control-label">City</label>
                                <select class="form-control select2" name="update_city" id="update_city" required>
                                    <option value="">City</option>
                                    <option value="Abomsa">Abomsa</option>
                                    <option value="Adama">Adama</option>
                                    <option value="Addis Ababa">Addis Ababa</option>
                                    <!-- Add other cities as needed -->
                                </select>
                            </div>
                            <div class="form-group col-6" id="addSubcity">
                                <label class="control-label">Sub-City</label>
                                <input type="text" class="form-control" id="update_subcity" name="update_subcity" placeholder="Subcity">
                            </div>
                        </div>
                        <div class="col-12 row">
                            <div class="form-group col-6" id="addWoreda">
                                <label class="control-label">Woreda</label>
                                <input type="text" class="form-control" id="update_woreda" name="update_woreda" placeholder="Woreda">
                            </div>
                            <div class="form-group col-6" id="addHousenumber">
                                <label class="control-label">House Number</label>
                                <input type="text" class="form-control" id="update_housenumber" name="update_housenumber" placeholder="House Number">
                            </div>
                            <div class="form-group required col-12" id="addLocation">
                                <label class="control-label">Specific Location</label>
                                <input type="text" class="form-control" id="update_location" name="update_location" placeholder="Location" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary mr-2" id="updateSubmitBtn">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>