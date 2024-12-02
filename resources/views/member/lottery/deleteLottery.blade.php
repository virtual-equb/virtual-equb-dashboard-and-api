@can('delete lottery_winner')
    
                <div class="modal modal-danger fade" id="openDeleteLotteryModal" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <p class="modal-title" id="exampleModalLabel">Delete Lottery History </p>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                            <form action="" method="post" id="deleteLottery">
                                @csrf
                                @method('DELETE')
                                 <input id="payment_id" name="id" hidden value="">
                                <p class="text-center">Are you sure you want to delete this lottery history?</p>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                
                            </div>
                            </form>
                        </div>
                    </div>
              </div>
            @endcan