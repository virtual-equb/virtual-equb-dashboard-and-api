@can('delete payment')
    <div class="modal modal-danger fade" id="deletePaymentModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" id="exampleModalLabel">Delete payment </p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="deletePayment">
                        @csrf
                        @method('DELETE')
                        <input id="payment_id" name="id" hidden value="">
                        <p class="text-center">Are you sure you want to delete this payment?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-danger">Delete </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="approvePaymentModal" tabindex="-1" role="dialog"
        aria-labelledby="Approve" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" id="exampleModalLabel">Delete payment </p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="approvePayment">
                        @csrf
                        @method('PUT')
                        <input id="payment_id" name="id" hidden value="">
                        <p class="text-center">Are you sure you want to approve this payment?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success">Approve </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal modal-danger fade" id="rejectPaymentModal" tabindex="-1" role="dialog" aria-labelledby="Reject"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" id="exampleModalLabel">Delete payment </p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="rejectPayment">
                        @csrf
                        @method('PUT')
                        <input id="payment_id" name="id" hidden value="">
                        <p class="text-center">Are you sure you want to reject this payment?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-danger">Reject </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Show Payment modal --}}
    <div id="paymentProofModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <form id="viewUserForm" class="form-horizontal" action="" method="post">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Payment Proof</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-sm-12 border">
                                <img class="col-12" src="" id="viewImage" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan
