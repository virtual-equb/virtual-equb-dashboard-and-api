        <section class="content-header">
            <h1>
                <small>Add User</small><br>
            </h1>
        </section>
        <form role="form" method="post" class="form-horizontal form-group"
            action="{{ route('createUser') }}" enctype="multipart/form-data"
            id="addUserForm">
            {{ csrf_field() }}
            <div class="card-body">
                <div class="form-group row">
                    <label for="name" class="control-label col-md-2 ">Full Name  <i class="fa fa-asterisk text-danger" style="font-size: 8px"></i></label>
                    <div class="form-group required col-md-10">
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Full Name" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="control-label col-md-2 ">Email  <i class="fa fa-asterisk text-danger" style="font-size: 8px"></i></label>
                    <div class="form-group required col-md-10">
                        <input type="email" class="form-control" id="email"
                            name="email" placeholder="Email" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="phone" class="control-label col-md-2"> Phone  <i class="fa fa-asterisk text-danger" style="font-size: 8px"></i></label>
                    <div class="form-group required col-md-10">
                        <input type="text" class="form-control" id="phone_number"
                            name="phone_number" placeholder="+251...." required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="phone" class="control-label col-md-2 "> Gender  <i class="fa fa-asterisk text-danger" style="font-size: 8px"></i></label>
                    <div class="form-group required col-md-10">

                        <select class="custom-select form-control" id="gender"
                        name="gender" required>
                        <option selected value="">Choose...</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>

                    </div>
                </div>

                <div class="form-group row">
                    {{-- {{ $roles }} --}}
                    <label for="phone" class="control-label col-md-2 "> Role  <i class="fa fa-asterisk text-danger" style="font-size: 8px"></i></label>
                    <div class="form-group required col-md-10">

                        <select class="custom-select form-control" id="role" multiple
                        name="role[]">
                        
                        <option selected value="">Choose...</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                            {{-- <option value="equb_collector">Equb Collector</option>
                            <option value="general_manager">General Manager</option>
                            <option value="operation_manager">Operation Manager</option>
                            <option value="marketing_manager">Marketing Manager</option>
                            <option value="assistant">Assistant</option>
                            <option value="customer_service">Customer Service</option>
                            <option value="finance">Finance</option>
                            <option value="it">IT</option> --}}
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="control-label col-md-2 ">Password  <i class="fa fa-asterisk text-danger" style="font-size: 8px"></i></label>
                    <div class="form-group required col-md-10">
                        <input type="password" class="form-control" id="email"
                            name="password" placeholder="Password" required>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mr-5">
                <button type="submit" id="submit" class="btn btn-outline-primary mr-3">Submit</button>
                <button type="reset" class="btn btn-warning text-white">Clear</button>
            </div>
        </form>
