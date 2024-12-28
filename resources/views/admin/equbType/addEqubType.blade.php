@can('draw equb_type_winner')
<div class="modal fade" id="drawModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form role="form" method="post" class="form-horizontal" action="{{ route('drawAutoWinners') }}" enctype="multipart/form-data" id="drawEqubType">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h4 class="modal-title">Draw</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group required">
                        <label class="control-label">Draw Type</label>
                        <select class="custom-select form-control" id="draw_type" name="draw_type" required>
                            <option selected value="">Choose Draw Type</option>
                            <option value="Automatic">Automatic</option>
                            <option value="Seasonal">Seasonal</option>
                        </select>
                    </div>
                    <div class="form-group required">
                        <label class="control-label">Equb Type</label>
                        <select class="form-control select2" id="equbTypeId" name="equbTypeId" required>
                            <option selected value="">Choose Equb Type</option>
                            @foreach ($equbTypes as $equbType)
                                <option data-info="{{ $equbType->type }}"
                                        data-startdate="{{ $equbType->start_date }}"
                                        data-enddate="{{ $equbType->end_date }}"
                                        data-rote="{{ $equbType->rote }}" 
                                        data-quota="{{ $equbType->quota }}"
                                        data-amount="{{ $equbType->amount }}" 
                                        data-expected-total="{{ $equbType->expected_total }}" 
                                        value="{{ $equbType->id }}">
                                    {{ $equbType->name }} round {{ $equbType->round }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                  

                   
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Draw</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form role="form" method="post" class="form-horizontal" action="{{ route('registerEqubType') }}" enctype="multipart/form-data" id="addEqubType">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h4 class="modal-title">Add Equb Type</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group required">
                            <label class="control-label">Equb</label>
                            <select class="custom-select form-control" id="main_equb_id" name="main_equb_id" required>
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
                            <select class="custom-select form-control" id="type" name="type" required>
                                <option selected value="">Choose Type</option>
                                <option value="Automatic">Automatic</option>
                                <option value="Manual">Manual</option>
                                <option value="Seasonal">Seasonal</option>
                            </select>
                        </div>
                        <div class="form-group required">
                            <label class="control-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                        </div>
                        <div class="form-group required">
                            <label class="control-label">Round</label>
                            <input type="number" class="form-control" id="round" name="round" placeholder="Round" min="1" required>
                        </div>
                        <div class="form-group required">
                            <label class="control-label">Rote</label>
                            <select class="custom-select form-control" id="rote" name="rote" required>
                                <option selected value="">Choose Rote</option>
                                <option value="Daily">Daily</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                            </select>
                        </div>
                        <div id="start_date_div" class="form-group d-none">
                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="date" id="" name="start_date" placeholder="Start Date" autocomplete="off">
                        </div>
                        <div id="quota_div" class="form-group d-none">
                            <label class="control-label">Quota</label>
                            <input type="number" class="form-control" id="quota" name="quota" placeholder="Quota" min="1" required>
                        </div>
                        <div id="end_date_div" class="form-group d-none">
                            <label for="end_date" class="control-label">End Date</label>
                            <input type="text" class="form-control" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                        </div>
                        <div id="amount_div" class="form-group d-none">
                        <label for="amount" class="control-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount" required>
                    </div>
                    <div id="expected_members_div" class="form-group d-none">
                        <label for="expected_members" class="control-label">Expected Members</label>
                        <input type="number" class="form-control" id="expected_members" name="expected_members" placeholder="Expected Members" readonly>
                    </div>
                    <div id="total_amount_div" class="form-group d-none">
                        <label for="total_amount" class="control-label">Total Amount</label>
                        <input type="number" class="form-control" id="total_amount" name="total_amount" placeholder="Total Amount" readonly>
                    </div>
                        <div id="lottery_date_div" class="form-group d-none">
                            <label for="lottery_date" class="control-label">Lottery Date</label>
                            <input type="text" class="form-control" id="lottery_date" name="lottery_date" placeholder="Lottery Date" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Icon</label>
                            <input type="file" class="form-control" name="icon" accept="image/jpeg, image/png">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Remark</label>
                            <textarea class="form-control" id="remark" name="remark" placeholder="Remark"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Terms and Conditions</label>
                            <textarea class="form-control" id="terms" name="terms" placeholder="Terms"></textarea>
                        </div>
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

<script>
    document.getElementById('draw_type').addEventListener('change', function() {
        var selectedType = this.value; 
        var equbTypeSelect = document.getElementById('equbTypeId'); 
        var amountDiv = document.getElementById('amount_div'); 
        var expectedMembersDiv = document.getElementById('expected_members_div');
        var totalAmountDiv = document.getElementById('total_amount_div');
        var form = document.getElementById('drawEqubType'); 

        // Clear existing options in the "Equb Type" dropdown
        equbTypeSelect.innerHTML = '<option selected value="">Choose Equb Type</option>';

        var equbTypes = @json($equbTypes); 

        equbTypes.forEach(function(equbType) {
            if (equbType.type === selectedType) {
                var option = document.createElement('option');
                option.value = equbType.id; 
                option.setAttribute('data-info', equbType.type);
                option.setAttribute('data-startdate', equbType.start_date);
                option.setAttribute('data-enddate', equbType.end_date);
                option.setAttribute('data-rote', equbType.rote);
                option.setAttribute('data-quota', equbType.quota);
                option.setAttribute('data-amount', equbType.amount); 
                option.setAttribute('data-expected-total', equbType.expected_total);

                option.textContent = equbType.name + ' round ' + equbType.round;
                equbTypeSelect.appendChild(option);
            }
        });

        form.action = selectedType === 'Seasonal' ? "{{ route('drawAutoSeasonal') }}" : "{{ route('drawAutoWinners') }}";

        if (selectedType === 'Automatic') {
            amountDiv.classList.remove('d-none'); // Show amount field
            expectedMembersDiv.classList.remove('d-none'); // Show expected members field
            totalAmountDiv.classList.remove('d-none'); // Show total amount field
        } else {
            amountDiv.classList.add('d-none'); // Hide amount field
            expectedMembersDiv.classList.add('d-none'); // Hide expected members field
            totalAmountDiv.classList.add('d-none'); // Hide total amount field
        }
    });

    document.getElementById('equbTypeId').addEventListener('change', function() {
        var selectedEqubType = this.selectedOptions[0];
        var amountInput = document.getElementById('amount');
        var expectedMembersInput = document.getElementById('expected_members');
        var totalAmountInput = document.getElementById('total_amount');

        if (selectedEqubType && document.getElementById('draw_type').value === 'Automatic') {
            amountInput.value = selectedEqubType.getAttribute('data-amount'); 
            expectedMembersInput.value = 100; // Set fixed expected members to 100
            totalAmountInput.value = selectedEqubType.getAttribute('data-expected-total'); 
        }
    });
</script>