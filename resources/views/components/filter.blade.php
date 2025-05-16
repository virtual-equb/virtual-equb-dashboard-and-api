<div class="p-4 mb-3" style="background: #eaf0ec; border-radius: 20px;">
    <form id="filterForm" method="GET" action="{{ $action ?? '' }}">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <span class="mr-2" style="font-size: 1.5rem; color: #2e343b;">
                    <!-- Filter Icon SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 5a1 1 0 0 1 1-1h16a1 1 0 0 1 .8 1.6l-5.6 7.47V19a1 1 0 0 1-1.45.89l-4-2A1 1 0 0 1 9 17v-4.93L3.2 6.6A1 1 0 0 1 3 5zm2.618 1l5.382 7.18V17.38l2 1V13.18L18.382 6z"/>
                    </svg>
                </span>
                <span class="mb-0 h5 font-weight-bold" style="color: #2d3e50;">Filters</span>
            </div>
        </div>
        <div class="m-0 form-row align-items-end">
            <div class="float-right col-6" id="member_table_filter">
                <select class="form-control" id="equbSearchText" name="equb_type_id"
                    placeholder="Equb Type">
                    <option value="">All Equb Type</option>
                    @foreach ($equbTypes as $equbType)
                        <option data-info="{{ $equbType->type }}"
                            value="{{ $equbType->id }}">
                            {{ $equbType->name }} round {{ $equbType->round }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="float-right col-6" id="member_table_filter">
                <select class="form-control"id="statusSearchText"
                    name="member_status" placeholder="Status">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Deactive">Deactive</option>
                </select>
            </div>
        </div>
    </form>
</div> 