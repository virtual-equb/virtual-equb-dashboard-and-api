@if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'customer_service' ||
        Auth::user()->role == 'it')
    <div class="modal fade" id="editEqubTypeModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group" action="" id="updateEqubType"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" id='did' name="did" value="">

                    <div class="modal-header">
                        <h4 class="modal-title">Edit Equb Type</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group required">
                                <label class="control-label">Equb</label>
                                <select class="custom-select form-control" id="update_main_equb" name="update_main_equb">
                                    <option selected value="">Choose Equb</option>
                                    @if(isset($mainEqubs) && count($mainEqubs) > 0)
                                        @foreach($mainEqubs as $key => $equb)
                                            <option value="{{ $equb->id }}">{{ $equb->name }}</option>
                                        @endforeach
                                    @else
                                        <option disabled>No Equbs Available</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Equb Type</label>
                                <select class="custom-select form-control" id="update_type" name="update_type">
                                    <option selected value="">Choose Type</option>
                                    <option value="Automatic">Automatic</option>
                                    <option value="Manual">Manual</option>
                                </select>
                            </div>
                            <div class="form-group required">
                                <label label class="control-label">Name</label>
                                <input type="text" class="form-control" id="update_name"
                                    name="update_name"placeholder="Name" required>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Round</label>
                                <input type="number" class="form-control" id="update_round"
                                    name="update_round"placeholder="Round" min="1" required>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Rote</label>
                                <select class="custom-select form-control" id="update_rote" name="update_rote">
                                    <option selected value="">Choose Rote</option>
                                    <option value="Daily">Daily</option>
                                    <option value="Weekly">Weekly</option>
                                    {{-- <option value="Biweekly">Biweekly</option> --}}
                                    <option value="Monthly">Monthly</option>
                                </select>
                            </div>
                            <div id="update_start_date_div" class="form-group d-none">
                                <label for="update_start_date" class="control-label">Start Date</label>
                                <input type="text" class="form-control" id="update_start_date" name="start_date"
                                    placeholder="Start Date" autocomplete="off">
                            </div>
                            <div id="update_quota_div" class="form-group d-none">
                                <label class="control-label">Quota</label>
                                <input type="number" class="form-control" id="update_quota" name="quota"
                                    placeholder="Quota" min="1" required>
                            </div>
                            <div id="update_end_date_div" class="form-group d-none">
                                <label for="update_end_date" class="control-label">End Date</label>
                                <input type="text" class="form-control" id="update_end_date" name="end_date"
                                    placeholder="End Date" autocomplete="off" readonly>
                            </div>
                            <div id="update_lottery_date_div" class="form-group d-none">
                                <label for="update_lottery_date" class="control-label">Lottery Date</label>
                                <input type="text" class="form-control" id="update_lottery_date"
                                    name="update_lottery_date" placeholder="Lottery Date" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Icon</label>
                                <input type="file" class="form-control" name="icon_update"
                                    accept="image/jpeg, image/png">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Remark</label>
                                <textarea class="form-control" id="update_remark" name="update_remark" placeholder="remark"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Terms and Conditions</label>
                                <textarea class="form-control textareaa" id="update_terms" name="update_terms" placeholder="terms"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mr -2"
                            onclick="editEqubTypeValidation()">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal-->
    <div class="modal fade" id="modaloff6" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group" action=""
                    id="viewUserForm" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="modal-header">
                        <h4 class="modal-title">Icon</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-sm-12 border">
                                <img width="250px" height="200px" src="" id="viewImage" alt="">
                                {{-- <img width="250px" height="200px" src="storage/profile_picture/29/1663753785.png" id="viewImage" alt=""> --}}
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
    <!-- End Modal-->
@endif
