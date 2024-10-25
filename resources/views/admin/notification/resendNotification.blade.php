{{-- @if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'it') --}}
    <div class="modal fade" id="resendNotificationModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group" action=""
                    id="resendNotification">
                    {{ csrf_field() }}
                    @method('put')
                    <input type="hidden" id='notification_id' name="notification_id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Resend Notification</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="form-group">
                                <label class="control-label">Send WIth</label>
                                <select class="form-control select2"id="update_method" name="update_method"
                                    placeholder="Equb Type">
                                    <option value="both">Both</option>
                                    <option value="sms">SMS</option>
                                    <option value="notification">Push Notification</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Equb Type</label>
                                <select class="form-control select2"id="update_equb_type" name="update_equb_type"
                                    placeholder="Equb Type">
                                    <option value="all">All</option>
                                    @foreach ($equbTypes as $equbType)
                                        <option data-info="{{ $equbType->type }}" value="{{ $equbType->id }}">
                                            {{ $equbType->name }} round {{ $equbType->round }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="form-group required col-12">
                                    <label class="control-label">Title</label>
                                    <input type="text" class="form-control" id="update_title" name="update_title"
                                        placeholder="Enter title" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>Body</label>
                                    <textarea rows="7" id="update_body" name="update_body" class="form-control" placeholder="Enter body"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mr -2">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editPendingNotificationModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group" action=""
                    id="editPendingNotification">
                    {{ csrf_field() }}
                    @method('put')
                    <input type="hidden" id='notification_id_pending' name="notification_id_pending" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Resend Notification</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="form-group">
                                <label class="control-label">Send WIth</label>
                                <select class="form-control select2"id="update_method_pending" name="update_method_pending"
                                    placeholder="Equb Type">
                                    <option value="both">Both</option>
                                    <option value="sms">SMS</option>
                                    <option value="notification">Push Notification</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Equb Type</label>
                                <select class="form-control select2"id="update_equb_type_pending" name="update_equb_type_pending"
                                    placeholder="Equb Type">
                                    <option value="all">All</option>
                                    @foreach ($equbTypes as $equbType)
                                        <option data-info="{{ $equbType->type }}" value="{{ $equbType->id }}">
                                            {{ $equbType->name }} round {{ $equbType->round }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="form-group required col-12">
                                    <label class="control-label">Title</label>
                                    <input type="text" class="form-control" id="update_title_pending" name="update_title_pending"
                                        placeholder="Enter title" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>Body</label>
                                    <textarea rows="7" id="update_body_pending" name="update_body_pending" class="form-control" placeholder="Enter body"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mr -2">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{{-- @endif --}}
