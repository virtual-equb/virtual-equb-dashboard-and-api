@can('create city')
<div class="modal fade" id="addCityModal" tabindex="-1" role="dialog" aria-labelledby="addCityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('cities.store') }}" id="addCityForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title" id="addCityModalLabel">Add City</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group required">
                            <label for="city_name" class="control-label">City Name</label>
                            <input type="text" class="form-control" id="city_name" name="name" placeholder="City Name" required>
                        </div>
                        <div class="form-group required">
                            <label for="active" class="control-label">Status</label>
                            <select class="form-control" id="active" name="active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add City</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan