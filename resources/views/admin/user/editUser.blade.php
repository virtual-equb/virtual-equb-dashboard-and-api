  {{-- @if (Auth::user()->role == 'admin' ||
          Auth::user()->role == 'general_manager' ||
          Auth::user()->role == 'operation_manager' ||
          Auth::user()->role == 'it') --}}
     <section class="content-header">
         <h1>
             <small>Update User</small><br>
         </h1>
     </section>
     <form role="form" method="post" class="form-horizontal form-group" action="{{ url('user/update/' . $user['id']) }}"
         enctype="multipart/form-data" id="editUserForm">
         {{ csrf_field() }}
         @method('put')
         <div class="card-body">
             <input type="hidden" id='user_id' name="user_id" value="{{ $user['id'] }}">
             <div class="form-group row">
                 <label for="name" class="col-md-2 control-label">Full Name <i class="fa fa-asterisk text-danger"
                         style="font-size: 8px"></i></label>
                 <div class="form-group col-md-10">
                     <input type="text" class="form-control" id="name" name="name"
                         value="{{ $user['name'] }}">
                 </div>
             </div>
             <div class="form-group row">
                 <label for="email" class="control-label col-md-2">Email <i class="fa fa-asterisk text-danger"
                         style="font-size: 8px"></i></label>
                 <div class=" form-group col-md-10">
                     <input type="email" class="form-control" id="email" name="email"
                         value="{{ $user['email'] }}">
                 </div>
             </div>
             <div class="form-group row">
                 <label for="phone" class="control-label col-md-2"> Phone <i class="fa fa-asterisk text-danger"
                         style="font-size: 8px"></i></label>
                 <div class="form-group col-md-10">
                     <input type="text" class="form-control" id="phone" name="phone_number"
                         value="{{ $user['phone_number'] }}">
                 </div>
             </div>
             <div class="form-group row">
                 <label for="phone" class="col-md-2 control-label"> Gender <i class="fa fa-asterisk text-danger"
                         style="font-size: 8px"></i></label>
                 <div class="form-group col-md-10">
                     <select class="custom-select form-control" id="gender" name="gender">

                         @if ($user['gender'] == 'Male')
                             <option value="Male" selected>Male</option>
                         @else
                             <option value="Male">Male</option>
                         @endif

                         @if ($user['gender'] == 'Female')
                             <option value="Female" selected>Female</option>
                         @else
                             <option value="Female">Female</option>
                         @endif


                     </select>

                 </div>
             </div>
             <div class="form-group row">
                 <label for="phone" class="col-md-2 control-label"> Role <i class="fa fa-asterisk text-danger"
                         style="font-size: 8px"></i></label>
                 <div class=" form-group col-md-10">

                     <select class="custom-select form-control" id="editRole" multiple name="role[]">
                        @foreach($roles as $role)
                            {{-- <option 
                                value="{{ $role->name }}"
                                {{ in_array($role, $userRoles) ? 'selected' : '' }}
                            >
                                {{ $role->name }}
                            </option> --}}
                            <option 
                                value="{{ $role->name }}"
                                {{ in_array($role->name, $userRoles) ? 'selected' : '' }}
                            >
                                {{ $role->name }}
                            </option>
                        @endforeach
                         {{-- @if ($user['role'] == 'admin')
                             <option value="admin" selected>Admin</option>
                         @else
                             <option value="admin">Admin</option>
                         @endif --}}

                     </select>
                 </div>
             </div>
         </div>
         <div class="d-flex justify-content-end mr-5">
             <button type="submit" onclick="edit()" class="btn btn-outline-primary mr-3">Submit</button>
             <button type="reset" class="btn btn-warning text-white">Clear</button>
         </div>
     </form>
 {{-- @endif --}}
