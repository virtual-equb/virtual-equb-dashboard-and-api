@if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'customer_service' ||
        Auth::user()->role == 'it')
    <div class="modal fade" id="lotteryDateCheckModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group nn"
                    action="{{ route('registerEqub') }}" enctype="multipart/form-data" id="addEqub">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h4 class="modal-title">Check Lottery Date</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <input type="hidden" id='member_id' name="member_id" value="">
                            <div class="form-row">
                                <div class="form-group required col-md-9">
                                    <label for="lottery_date" class="control-label">Lottery Date</label>
                                    <input type="text" class="form-control" id="lottery_date_check"
                                        name="lottery_date" placeholder="Lottery Date" autocomplete="off">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="check" class="control-label">&nbsp;</label>
                                    <input type="button" class="form-control btn btn-primary" value="Check"
                                        onclick="lotteryDateCheck()">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endif
