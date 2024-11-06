@can('create equb_type')
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
                            <!-- Form fields -->
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
                            <!-- Additional form fields omitted for brevity -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" onclick="addEqubTypeValidation()">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

@can('draw equb_type_winner')
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
                        <div class="form-group required">
                            <label class="control-label">Equb Type</label>
                            <select class="form-control select2" id="equbTypeId" name="equbTypeId" placeholder="Equb Type">
                                <option value="all">All</option>
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
                        <button type="submit" class="btn btn-primary" onclick="drawAutoWinners()">Draw</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan
