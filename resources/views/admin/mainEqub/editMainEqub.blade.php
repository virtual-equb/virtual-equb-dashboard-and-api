<div class="modal fade" id="editMainEqubModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="post" action="{{ route('mainEqubs.update', 'equb') }}" enctype="multipart/form-data" id="editMainEqubForm">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <div class="modal-header">
                    <h4 class="modal-title">Edit Main Equb</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_equb_id" name="equb_id">

                    <div class="form-group required">
                        <label for="edit_name" class="control-label">Main Equb Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" placeholder="Main Equb Name" required>
                    </div>

                    <div class="form-group required">
                        <label for="edit_created_by" class="control-label">Created By</label>
                        <input type="text" class="form-control" id="edit_created_by" name="created_by" placeholder="Your Name" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_image" class="control-label">Image</label>
                        <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label for="edit_remark" class="control-label">Remark</label>
                        <textarea class="form-control" id="edit_remark" name="remark" placeholder="Additional Information"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>