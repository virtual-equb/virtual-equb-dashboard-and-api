<!-- Edit Main Equb Modal -->
@can('update main_equb')
<div class="modal fade" id="editMainEqubModal" tabindex="-1" role="dialog" aria-labelledby="editMainEqubModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMainEqubModalLabel">Edit Main Equb</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editMainEqubForm" enctype="multipart/form-data"> <!-- Added enctype for file upload -->
                    <input type="hidden" id="editMainEqubId">
                    
                    <div class="form-group required">
                        <label for="editMainEqubName" class="control-label">Main Equb Name</label>
                        <input type="text" class="form-control" id="editMainEqubName" required>
                    </div>
                    
                    <div class="form-group required">
                        <label for="editMainEqubStatus" class="control-label">Status</label>
                        <select class="form-control" id="editMainEqubStatus" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="currentImage" class="control-label">Current Image:</label>
                        <div style="background-color: rgb(76, 175, 80); width: 60px; text-align: center;">
                            <img id="currentImage" src="{{ asset('storage/' . $equb->image) }}" alt="{{ $equb->name }}" 
                                style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; display: {{ $equb->image ? 'block' : 'none' }};">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="newImage" class="control-label">Upload New Image:</label>
                        <input type="file" class="form-control" id="newImage" name="new_image" accept="image/*"> <!-- File input for new image -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEditMainEqub">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endcan