@can('create equb_type')
<div class="modal fade" id="addEqubModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="post" class="form-horizontal form-group nn"
                action="{{ route('registerEqub') }}" enctype="multipart/form-data" id="addEqub">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h4 class="modal-title">Add Equb Samisams</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12">
                        <input type="hidden" id='member_id' name="member_id" value="">
                        
                        <div class="form-group required">
                            <label class="control-label">Type</label>
                            <select class="custom-select form-control" id="type" name="type" required>
                                <option selected value="">Choose Type</option>
                                <option value="Automatic">Automatic</option>
                                <option value="Seasonal">Automatic Seasonal</option>
                                <option value="Manual">Manual</option>
                            </select>
                        </div>
                        
                        <div class="form-group required">
                            <label class="control-label">Equb Type</label>
                            <select class="form-control select2" id="equb_type_id" name="equb_type_id" onchange="updateAmountAndTotal()">
                                <option value="">Choose...</option>
                                @foreach ($equbTypes as $equbType)
                                    <option data-amount="{{ $equbType->amount }}" 
                                            data-expected-total="{{ $equbType->expected_total }}" 
                                            data-expected-members="{{ $equbType->expected_members }}"
                                            value="{{ $equbType->id }}">
                                        {{ $equbType->name }} round {{ $equbType->round }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group required">
                            <label class="control-label">Start Date</label>
                            <input type="text" class="form-control" id="start_date"
                                name="start_date" placeholder="Start date" autocomplete="off">
                        </div>
                        
                        <div id="timeline_div" class="form-group required">
                            <label class="control-label">Timeline</label>
                            <select class="form-control select2" id="timeline" name="timeline" onchange="updateAmountAndTotal()">
                                <option value="">Choose Timeline</option>
                                <option value="105">105 days</option>
                                <option value="210">210 days</option>
                                <option value="315">315 days</option>
                                <option value="420">420 days</option>
                                <option value="350">50 Weeks</option>
                                <option value="700">100 Weeks</option>
                                <option value="1050">150 Weeks</option>
                                <option value="365">12 Months</option>
                                <option value="730">24 Months</option>
                                <option value="1095">36 Months</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label">End Date</label>
                            <input type="text" class="form-control disabled" id="end_date"
                                name="end_date" placeholder="End date" readonly>
                        </div>

                        <div id="equb_lottery_date_div" class="form-row">
                            <div class="form-group required col-md-9">
                                <label for="lottery_date" class="control-label">Lottery Date</label>
                                <input type="text" class="form-control" id="lottery_date" name="lottery_date"
                                    placeholder="Lottery Date" autocomplete="off">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="check" class="control-label">&nbsp;</label>
                                <input type="button" class="form-control btn btn-primary" value="Check"
                                    onclick="validateForm()">
                            </div>
                        </div>

                        <div class="form-group required">
                            <label class="control-label">Amount</label>
                            <input type="number" value="0" class="form-control" id="amount_per_day" name="amount" placeholder="Amount" required>
                        </div>
                        
                        <div class="form-group required">
                            <label class="control-label">Expected Total</label>
                            <input type="number" class="form-control" id="total_amount"
                                name="total_amount" placeholder="Total equb amount" readonly min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="addEqubBtn">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateAmountAndTotal() {
        const equbTypeSelect = document.getElementById('equb_type_id');
        const selectedOption = equbTypeSelect.options[equbTypeSelect.selectedIndex];

        if (selectedOption.value) {
            const amountFromDB = parseFloat(selectedOption.getAttribute('data-amount')) || 0;
            const expectedTotalFromDB = parseFloat(selectedOption.getAttribute('data-expected-total')) || 0;
            const expectedMembers = parseFloat(selectedOption.getAttribute('data-expected-members')) || 0;

            // Calculate Expected Total based on expected_total and expected_members
            const expectedTotal = expectedTotalFromDB * expectedMembers;

            // Update the input fields
            document.getElementById('total_amount').value = expectedTotal; // Set Expected Total from DB calculation
            document.getElementById('amount_per_day').value = amountFromDB; // Update Amount field
        } else {
            // Reset values if no option is selected
            document.getElementById('total_amount').value = '';
            document.getElementById('amount_per_day').value = 0;
        }
    }

    document.getElementById('equb_type_id').addEventListener('change', updateAmountAndTotal);
    document.getElementById('type').addEventListener('change', function() {
        const selectedType = this.value;
        const equbTypeSelect = document.getElementById('equb_type_id');

        // Clear existing options
        equbTypeSelect.innerHTML = '<option value="">Choose...</option>';

        // Populate equb_type_id based on selected type
        @foreach ($equbTypes as $equbType)
            if (selectedType === "{{ $equbType->type }}") {
                const option = document.createElement('option');
                option.value = "{{ $equbType->id }}";
                option.textContent = "{{ $equbType->name }} round {{ $equbType->round }}";
                option.setAttribute('data-amount', "{{ $equbType->amount }}");
                option.setAttribute('data-expected-total', "{{ $equbType->expected_total }}");
                option.setAttribute('data-expected-members', "{{ $equbType->expected_members }}");
                equbTypeSelect.appendChild(option);
            }
        @endforeach
    });
</script>
@endcan