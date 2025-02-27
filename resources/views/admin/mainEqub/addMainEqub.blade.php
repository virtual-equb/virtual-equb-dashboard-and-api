@can('create main_equb')
<div class="modal fade" id="addMainEqubModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="post" action="{{ route('storeMainEqub') }}" enctype="multipart/form-data" id="addMainEqub" name="addMainEqub">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h4 class="modal-title">Add Main Equb</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group required">
                            <label for="name" class="control-label">Main Equb Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Main Equb Name" required>
                        </div>
                        <div class="form-group">
                            <label for="image" class="control-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>

                        <div class="form-group required">
                            <label for="remark" class="control-label">Remark</label>
                            <textarea class="form-control" id="remark" name="remark" placeholder="Additional Information" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="active" class="control-label">Status*</label>
                            <select class="form-control" id="active" name="active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary" id="submitBtn">Save</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endcan
