    @if (Auth::user()->role == 'equb_collector')
     <form role="form" method="post" class="form-horizontal form-group"
             action="{{ route('registerEqub') }}"
            enctype="multipart/form-data" id="addEqub">
            {{ csrf_field() }}
             <!--<div class="row">-->             
            <div class="row">
            <!-- text input -->
              <div class="col">
              <div class="form-group"> 
                    <label class="control-label">Member</label>
                    <select class="form-control select2" style="width: 400px;"id="equb_type_id" name="equb_type_id" placeholder="Equb Type"required>
                        <option value="">choose...</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                        @endforeach
                    </select>
              </div>
              </div>
              <div class="col">
              <div class="form-group"> 
                    <label class="control-label">Equb type</label>
                    <select class="form-control select2" style="width: 400px;"id="equb_type_id" name="equb_type_id" placeholder="Equb Type"required>
                        <option value="">choose...</option>
                        @foreach ($equbTypes as $equbType)
                            <option value="{{ $equbType->id }}">{{ $equbType->name }}</option>
                        @endforeach
                    </select>
              </div>
            </div>
              <div class="col" style="padding: 30px;">
                <div class="">
                         <button type="submit" class="btn btn-primary">Go</button>
                </div>
               </div> 
          </div>
      </form>
    @endif  