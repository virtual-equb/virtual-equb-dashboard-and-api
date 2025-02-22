@can('create city')
<div class="modal fade" id="editCityModal" tabindex="-1" role="dialog" aria-labelledby="editCityModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCityModalLabel">Edit City</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form role="form" method="post" class="form-horizontal form-group" action="" id="editCityForm">
                        {{ csrf_field() }}
                        <input type="hidden" id="editCityId">
                        <div class="form-group required">
                            <label for="editCityName" class="control-label">City Name</label>
                            <input type="text" class="form-control" id="editCityName" required>
                        </div>
                        <div class="form-group required">
                            <label for="editCityStatus" class="control-label">Status</label>
                            <select class="form-control" id="editCityStatus" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEditCity">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    @endcan