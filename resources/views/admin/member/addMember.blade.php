@can('create member')
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('registerMember') }}" enctype="multipart/form-data" id="addMember">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h4 class="modal-title">Member Registration</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group required" id="addFullName">
                            <label for="full_name" class="control-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Name" autocomplete="off">
                        </div>

                        <div class="form-group required" id="addPhone">
                            <label class="control-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="+251911121314" required>
                        </div>

                        <div class="form-group" id="addEmail">
                            <label class="control-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" placeholder="Email">
                        </div>

                        <div class="form-group required" id="addGender">
                            <label class="control-label">Gender</label>
                            <select class="form-control select2" name="gender" id="gender" required>
                                <option value="">Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <hr>

                        <label class="control-label">Address</label>
                        <div class="col-12 row">
                            <div class="form-group required col-6" id="addCity">
                                <label class="control-label">City</label>
                                <select class="form-control select2" name="city" required id="select-city1">
                                    <option value="">Select City</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-6" id="addSubcity1" style="display:none;">
                                <label class="control-label">Sub-City</label>
                                <select class="form-control select2" id="subcity1" name="subcity" required>
                                    <option value="">Select Sub-City</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 row">
                            <div class="form-group col-6" id="addWoreda">
                                <label class="control-label">Woreda</label>
                                <input type="text" class="form-control" id="woreda" name="woreda" placeholder="Woreda">
                            </div>
                            <div class="form-group col-6" id="addHousenumber">
                                <label class="control-label">House Number</label>
                                <input type="text" class="form-control" id="housenumber" name="housenumber" placeholder="House Number">
                            </div>
                            <div class="form-group required col-12" id="addLocation">
                                <label class="control-label">Specific Location</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="Location" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
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
        $('#subcity1').empty().append('<option value="">Select Sub-City</option>');
        $('#addSubcity1').hide();

        if (cityId) {
            $.ajax({
                url: '/subcities/city/' + encodeURIComponent(cityId),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) {
                        $.each(data, function(index, subcity) {
                            $('#subcity1').append('<option value="' + subcity.id + '">' + subcity.name + '</option>');
                        });
                        $('#addSubcity1').show();
                    } else {
                        $('#addSubcity1').hide();
                    }
                },
                error: function() {
                    alert('Failed to retrieve sub-cities.');
                    $('#addSubcity1').hide();
                }
            });
        } else {
            $('#addSubcity1').hide();
        }
    });
});
</script>
@endcan