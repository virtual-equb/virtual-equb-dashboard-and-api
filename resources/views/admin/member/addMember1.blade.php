@can('update member')
<div class="modal fade" id="editMemberModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="post" class="form-horizontal" action="" id="updateMember" enctype="multipart/form-data">
                {{ csrf_field() }}
                @method('put')
                <input type="hidden" id="m_id" name="m_id" value="">
                
                <div class="modal-header">
                    <h4 class="modal-title">Edit Member</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="profile-picture-container">
                                <img id="profilePicPreview" src="{{ asset('storage/profile_pictures/default.png') }}" alt="Profile Picture" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" onclick="document.getElementById('profile_picture').click();">
                                <input type="file" class="form-control mt-2" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(event)" style="display: none;">
                            </div>
                        </div>

                        <div class="form-group required">
                            <label class="control-label" for="update_full_name">Full Name</label>
                            <input type="text" class="form-control" id="update_full_name" name="full_name" placeholder="Full Name" required>
                        </div>

                        <div class="form-group required">
                            <label class="control-label" for="update_phone">Phone</label>
                            <input type="text" class="form-control" id="update_phone" name="phone" placeholder="251911121314" required>
                        </div>

                        <div class="form-group" id="addEmail">
                            <label class="control-label" for="update_email">Email</label>
                            <input type="text" class="form-control" id="update_email" name="email" placeholder="Email">
                        </div>

                        <div class="form-group required">
                            <label class="control-label" for="update_gender">Gender</label>
                            <select class="form-control" id="update_gender" name="gender" required>
                                <option value="Female">Female</option>
                                <option value="Male">Male</option>
                            </select>
                        </div>
                        
                        <hr>

                        <label class="control-label">Address</label>
                        <div class="col-12 row">
                            <div class="form-group required col-6">
                                <label class="control-label" for="select-city">City</label>
                                <select class="form-control select2" id="select-city" name="city" required>
                                    <option value="">Select City</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-6" id="addSubcity" style="display:none;">
                                <label class="control-label" for="subcity">Sub-City</label>
                                <select class="form-control select2" id="subcity" name="subcity" required>
                                    <option value="">Select Sub-City</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 row">
                            <div class="form-group col-6">
                                <label class="control-label" for="update_woreda">Woreda</label>
                                <input type="text" class="form-control" id="update_woreda" name="update_woreda" placeholder="Woreda">
                            </div>
                            <div class="form-group col-6">
                                <label class="control-label" for="update_housenumber">House Number</label>
                                <input type="text" class="form-control" id="update_housenumber" name="update_housenumber" placeholder="House Number">
                            </div>
                            <div class="form-group required col-12">
                                <label class="control-label" for="update_location">Specific Location</label>
                                <input type="text" class="form-control" id="update_location" name="update_location" placeholder="Location" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="updateSubmitBtn">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include jQuery from CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#select-city').change(function() {
        var cityId = $(this).val();
        $('#subcity').empty().append('<option value="">Select Sub-City</option>');
        $('#addSubcity').hide();

        if (cityId) {
            $.ajax({
                url: '/subcities/city/' + encodeURIComponent(cityId),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) {
                        $.each(data, function(index, subcity) {
                            $('#subcity').append('<option value="' + subcity.id + '">' + subcity.name + '</option>');
                        });
                        $('#addSubcity').show();
                    } else {
                        $('#addSubcity').hide();
                    }
                },
                error: function() {
                    alert('Failed to retrieve sub-cities.');
                    $('#addSubcity').hide();
                }
            });
        } else {
            $('#addSubcity').hide();
        }
    });
});

function previewImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('profilePicPreview').src = e.target.result;
    }
    reader.readAsDataURL(file);
}
</script>
@endcan