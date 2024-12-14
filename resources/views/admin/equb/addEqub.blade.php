@can('create equb_type')
<div class="modal fade" id="addEqubModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="post" class="form-horizontal" action="{{ route('registerEqub') }}" enctype="multipart/form-data" id="addEqub">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h4 class="modal-title">Add sami</h4>
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
                            <select class="form-control select2" id="equb_type_id" name="equb_type_id">
                                <option value="">choose...</option>
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
                        
                        <div class="form-group required">
                            <label class="control-label">Start Date</label>
                            <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Start date" autocomplete="off">
                        </div>
                        
                        <div id="timeline_div" class="form-group required">
                            <label class="control-label">Timeline</label>
                            <select class="form-control select2" id="timeline" name="timeline">
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
                            <input type="text" class="form-control" id="end_date" name="end_date" placeholder="End date" readonly>
                        </div>

                        <div id="equb_lottery_date_div" class="form-row">
                            <div class="form-group required col-md-9">
                                <label for="lottery_date" class="control-label">Lottery Date</label>
                                <input type="text" class="form-control" id="lottery_date" name="lottery_date" placeholder="Lottery Date" autocomplete="off">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="check" class="control-label">&nbsp;</label>
                                <input type="button" class="form-control btn btn-primary" value="Check" onclick="validateForm()">
                            </div>
                        </div>

                        <div class="form-group required">
                            <label class="control-label">Amount</label>
                            <input type="number" value="0" onkeyup="getExpectedTotal()" class="form-control" id="amount_per_day" name="amount" placeholder="Amount" required readonly>
                        </div>
                        
                        <div class="form-group required" id="expected_total_div">
                            <label class="control-label">Expected Total</label>
                            <input type="number" class="form-control" id="total_amount" name="total_amount" placeholder="Total equb amount" readonly min="1">
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
    const equbTypes = @json($equbTypes); // Pass the PHP variable to JavaScript

    function filterEqubTypes() {
        const selectedType = document.getElementById('type').value;
        const equbTypeSelect = document.getElementById('equb_type_id');

        // Clear previous options
        equbTypeSelect.innerHTML = '<option value="">choose...</option>';

        // Filter equbTypes based on the selected type
        const filteredTypes = equbTypes.filter(equbType => {
            return (selectedType === "Automatic" && equbType.type === "Automatic") ||
                   (selectedType === "Seasonal" && equbType.type === "Seasonal") ||
                   (selectedType === "Manual" && equbType.type === "Manual");
        });

        // Populate the dropdown with filtered options
        filteredTypes.forEach(equbType => {
            const option = document.createElement('option');
            option.value = equbType.id;
            option.setAttribute('data-info', equbType.type);
            option.setAttribute('data-startdate', equbType.start_date);
            option.setAttribute('data-enddate', equbType.end_date);
            option.setAttribute('data-rote', equbType.rote);
            option.setAttribute('data-quota', equbType.quota);
            option.setAttribute('data-amount', equbType.amount);
            option.setAttribute('data-expected-total', equbType.expected_total);
            option.textContent = `${equbType.name} round ${equbType.round}`;
            equbTypeSelect.appendChild(option);
        });
    }

    document.getElementById('type').addEventListener('change', function() {
        filterEqubTypes();

        // Reset fields when type changes
        document.getElementById('amount_per_day').value = '';
        document.getElementById('total_amount').value = '';
        document.getElementById('start_date').value = ''; // Clear start date
        document.getElementById('start_date').readOnly = false; // Reset readOnly state
        document.getElementById('end_date').value = ''; // Clear end date

        // Hide or show expected total based on type
        const expectedTotalDiv = document.getElementById('expected_total_div');
        expectedTotalDiv.style.display = (this.value === 'Automatic' || this.value === 'Seasonal') ? 'none' : 'block';
    });

    document.getElementById('equb_type_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        // Populate fields based on selected Equb Type
        const amount = selectedOption.getAttribute('data-amount');
        const expectedTotal = selectedOption.getAttribute('data-expected-total');
        const startDate = selectedOption.getAttribute('data-startdate');
        const endDate = selectedOption.getAttribute('data-enddate');

        document.getElementById('amount_per_day').value = amount;
        document.getElementById('total_amount').value = expectedTotal;
        document.getElementById('start_date').value = formatDate(startDate); // Format and set start date
        document.getElementById('end_date').value = formatDate(endDate); // Format and set end date
        document.getElementById('start_date').readOnly = true; // Make start date read-only
    });

    function formatDate(dateString) {
        const date = new Date(dateString);
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Get month and pad with leading zero
        const day = String(date.getDate()).padStart(2, '0'); // Get day and pad with leading zero
        const year = date.getFullYear(); // Get year
        return `${month}/${day}/${year}`; // Return formatted date
    }

    document.getElementById('timeline').addEventListener('change', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        const startDateValue = new Date(startDateInput.value);
        const timelineValue = parseInt(this.value);

        if (startDateInput.value && !isNaN(timelineValue)) {
            const endDate = new Date(startDateValue);
            endDate.setDate(endDate.getDate() + timelineValue); // Add timeline days to start date
            endDateInput.value = formatDate(endDate.toISOString().split('T')[0]); // Format the date as MM/DD/YYYY
        }
    });
</script>
@endcan