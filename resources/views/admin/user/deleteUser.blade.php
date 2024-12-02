@can('delete user')
      <div class="modal modal-danger fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
          aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Delete user </h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <form action="" method="post" id="deleteUser">
                          @csrf
                          @method('DELETE')
                          <input id="user_id" name="id" hidden value="">
                          <h5 class="text-center">Are you sure you want to delete this user?</h5>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                  </div>
                  </form>
              </div>
          </div>
      </div>
  @endcan
