<div class="modal fade" id="editMainEqubModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" id="editMainEqubForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_equb_id" name="equb_id">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Main Equb</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group required">
                            <label for="edit_name" class="control-label">Main Equb Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" placeholder="Main Equb Name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_image" class="control-label">Image</label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                            <img id="currentImage" src="" alt="Current Image" class="img-thumbnail" style="max-width: 100px; max-height: 100px; display: none;">
                        </div>

                        <div class="form-group">
                            <label for="edit_remark" class="control-label">Remark</label>
                            <textarea class="form-control" id="edit_remark" name="remark" placeholder="Additional Information"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="edit_active" class="control-label">Active:</label>
                            <select class="form-control" id="edit_active" name="active">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveChanges">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>