                   @if (Auth::user()->role == 'admin' ||
                           Auth::user()->role == 'general_manager' ||
                           Auth::user()->role == 'operation_manager' ||
                           Auth::user()->role == 'customer_service' ||
                           Auth::user()->role == 'it')
                       <div class="modal modal-danger fade" id="deleteEqubModal" tabindex="-1" role="dialog"
                           aria-labelledby="Delete" aria-hidden="true">
                           <div class="modal-dialog" role="document">
                               <div class="modal-content">
                                   <div class="modal-header">
                                       <p class="modal-title" id="exampleModalLabel">Delete equb</p>
                                       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                           <span aria-hidden="true">&times;</span>
                                       </button>
                                   </div>
                                   <div class="modal-body">
                                       <form action="" method="post" id="deleteEqub">
                                           @csrf
                                           @method('DELETE')
                                           <input id="equb_id" name="id" hidden value="">
                                           <p class="text-center">Are you sure you want to delete this equb?</p>
                                   </div>
                                   <div class="modal-footer">
                                       <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                       <button type="button" class="btn btn-secondary"
                                           data-dismiss="modal">Cancel</button>

                                   </div>
                                   </form>
                               </div>
                           </div>
                       </div>
                   @endif
