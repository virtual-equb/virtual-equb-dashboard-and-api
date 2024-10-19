        @if (Auth::user()->role == 'admin' ||
                Auth::user()->role == 'general_manager' ||
                Auth::user()->role == 'operation_manager' ||
                Auth::user()->role == 'customer_service' ||
                Auth::user()->role == 'it')
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <form role="form" method="post" class="form-horizontal form-group"
                            action="{{ route('registerEqubType') }}" enctype="multipart/form-data" id="addEqubType">
                            {{ csrf_field() }}
                            <div class="modal-header">
                                <h4 class="modal-title">Add Equb Type </h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="col-sm-12">
                                    <div class="form-group required">
                                        <label class="control-label">Equb</label>
                                        <select class="custom-select form-control" id="main_equb_id" name="main_equb_id">
                                            <option selected value="">Choose Equb</option>
                                            @if(isset($mainEqubs) && count($mainEqubs) > 0)
                                                @foreach($mainEqubs as $equb)
                                                    <option value="{{ $equb->id }}">{{ $equb->name }}</option>
                                                @endforeach
                                            @else
                                                <option disabled>No Equbs Available</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label">Type</label>
                                        <select class="custom-select form-control" id="type" name="type">
                                            <option selected value="">Choose Type</option>
                                            <option value="Automatic">Automatic</option>
                                            <option value="Manual">Manual</option>
                                        </select>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label">Name</label>
                                        <input type="text" class="form-control" id="name"
                                            name="name"placeholder="Name" required>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label">Round</label>
                                        <input type="number" class="form-control" id="round"
                                            name="round"placeholder="Round" min="1" required>
                                    </div>
                                    <div class="form-group required">
                                        <label class="control-label">Rote</label>
                                        <select class="custom-select form-control" id="rote" name="rote">
                                            <option selected value="">Choose Rote</option>
                                            <option value="Daily" id="daily_rote">Daily</option>
                                            <option value="Weekly" id="weekly_rote">Weekly</option>
                                            {{-- <option value="Biweekly" id="biweekly_rote">Biweekly</option> --}}
                                            <option value="Monthly" id="monthly_rote">Monthly</option>
                                        </select>
                                    </div>
                                    <div id="start_date_div" class="form-group d-none">
                                        <label for="start_date" class="control-label">Start Date</label>
                                        <input type="text" class="form-control" id="start_date" name="start_date"
                                            placeholder="Start Date" autocomplete="off">
                                    </div>
                                    <div id="quota_div" class="form-group d-none">
                                        <label class="control-label">Quota</label>
                                        <input type="number" class="form-control" id="quota" name="quota"
                                            placeholder="Quota" min="1" required>
                                    </div>
                                    <div id="end_date_div" class="form-group d-none">
                                        <label for="end_date" class="control-label">End Date</label>
                                        <input type="text" class="form-control" id="end_date" name="end_date"
                                            placeholder="End Date" autocomplete="off" readonly>
                                    </div>
                                    <div id="lottery_date_div" class="form-group d-none">
                                        <label for="lottery_date" class="control-label">Lottery Date</label>
                                        <input type="text" class="form-control" id="lottery_date" name="lottery_date"
                                            placeholder="Lottery Date" autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Icon</label>
                                        <input type="file" class="form-control" name="icon"
                                            accept="image/jpeg, image/png">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Remark</label>
                                        <textarea class="form-control" id="remark" name="remark" placeholder="remark"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Terms and Conditions</label>
                                        <textarea class="form-control textareaa" id="terms" name="terms" placeholder="terms"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary"
                                    onclick="addEqubTypeValidation()">Save</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            <div class="modal fade" id="drawModal" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <form role="form" method="post" class="form-horizontal form-group"
                            action="{{ route('drawAutoWinners') }}" enctype="multipart/form-data" id="addEqubType">
                            {{ csrf_field() }}
                            <div class="modal-header">
                                <h4 class="modal-title">Automatic Draw </h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                {{-- <div class="col-sm-12">
                                    <label class="control-label">Draw winners for automatic equbs?</label>
                                </div> --}}
                                <div class="form-group required">
                                    <label class="control-label">Equb Type</label>
                                    <select class="form-control select2" id="equbTypeId" name="equbTypeId"
                                        placeholder="Equb Type">
                                        <option value="all">All
                                        </option>
                                        @foreach ($equbTypes as $equbType)
                                            <option data-info="{{ $equbType->type }}"
                                                data-startdate="{{ $equbType->start_date }}"
                                                data-enddate="{{ $equbType->end_date }}"
                                                data-rote="{{ $equbType->rote }}" data-quota="{{ $equbType->quota }}"
                                                value="{{ $equbType->id }}">
                                                {{ $equbType->name }} round {{ $equbType->round }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary"
                                    onclick="drawAutoWinners()">Draw</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        @endif
