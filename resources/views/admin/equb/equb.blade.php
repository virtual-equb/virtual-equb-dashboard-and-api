@can('view equb')
              <form role="form" method="post" class="form-horizontal form-group" action="{{ route('registerEqub') }}"
                  enctype="multipart/form-data" id="addEqub">
                  {{ csrf_field() }}
                  <div class="row">
                      <div class="col">
                          <div class="form-group">
                              <label class="control-label">Member</label>
                              <select class="form-control select2" id="equb_type_id" name="equb_type_id"
                                  placeholder="Equb Type"required>
                                  <option value="">choose...</option>
                                  @foreach ($members as $member)
                                      <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                  @endforeach
                              </select>

                          </div>
                      </div>
                      <div class="col">
                          <div class="">
                              <button type="submit" class="btn btn-primary">Go</button>
                          </div>
                      </div>
                  </div>
              </form>
@endcan
