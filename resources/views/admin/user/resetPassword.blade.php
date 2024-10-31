{{-- @if (Auth::user()->role == 'admin' ||
        Auth::user()->role == 'general_manager' ||
        Auth::user()->role == 'operation_manager' ||
        Auth::user()->role == 'it') --}}
    <div class="modal fade" id="resetPasswordModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group"
                    action="{{ url('user/resetPassword') }}" id="resetPassword">
                    {{ csrf_field() }}
                    <input type="hidden" id='u_id' name="u_id" value="">

                    <div class="modal-header">
                        <h4 class="modal-title">Reset Password</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group">
                                Reset password for this user?
                                {{-- <label class="control-label">Reset password for this user?</label> --}}
                                {{-- <input type="password" class="form-control" id="reset_password" name="reset_password"placeholder="password" required> --}}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mr -2">Reset Password</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{{-- @endif --}}
