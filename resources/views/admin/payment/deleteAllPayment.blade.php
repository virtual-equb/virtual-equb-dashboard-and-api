@can('delete payment')
     <div class="modal modal-danger fade" id="deleteAllPaymentModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
         aria-hidden="true">
         <div class="modal-dialog" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <p class="modal-title" id="">Delete all payment </p>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <form action="" method="post" id="deleteAllPayment">
                         @csrf
                         @method('DELETE')
                         <input id="member_id" name="member_id" hidden value="">
                         <input id="equb_id" name="equb_id" hidden value="">
                         <p class="text-center">Are you sure you want to delete all payment?</p>
                 </div>
                 <div class="modal-footer">
                     <button type="submit" class="btn btn-sm btn-danger">Delete </button>
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                 </div>
                 </form>
             </div>
         </div>
     </div>
 @endcan
