<div class="modal fade" id="addEqubModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" method="post" class="form-horizontal form-group nn"
                action="{{ route('registerEqub') }}" enctype="multipart/form-data" id="addEqub">
                {{ csrf_field() }}

                <div class="modal-header">
<<<<<<< HEAD
                    <h4 class="modal-title">Add Equb</h4>
=======
<<<<<<< HEAD
                    <h4 class="modal-title">Add Equb</h4>
=======
                    <h4 class="modal-title">Add Equb sami</h4>
>>>>>>> ca9d679eede169b91f99c58c076e2efb6fadc2cb
>>>>>>> f57ab65126e22772e68049102f72b64d0da86517
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
                                <option value="Manual">Manual</option>
                            </select>
                        </div>
                        <div class="form-group required">
                            <label class="control-label">Equb Type</label>
                            <select class="form-control select2" id="equb_type_id" name="equb_type_id" placeholder="Equb Type">
                                <option value="">choose...</option>
                                @foreach ($equbTypes as $equbType)
                                    <option data-info="{{ $equbType->type }}"
                                        data-startdate="{{ $equbType->start_date }}"
                                        data-enddate="{{ $equbType->end_date }}" 
                                        data-rote="{{ $equbType->rote }}" 
                                        data-quota="{{ $equbType->quota }}"
<<<<<<< HEAD
                                        data-amount="{{ $equbType->amount }}" 
                                        data-expected-total="{{ $equbType->expected_total }}" 
=======
                                        data-amount="{{ $equbType->amount }}" <!-- Add amount data -->
                                        data-expected-total="{{ $equbType->expected_total }}" <!-- Add expected total data -->
>>>>>>> ca9d679eede169b91f99c58c076e2efb6fadc2cb
                                        value="{{ $equbType->id }}">
                                        {{ $equbType->name }} round {{ $equbType->round }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group required">
                            <label class="control-label">Start Date</label>
                            <input type="text" onchange="getExpectedTotal()" class="form-control" id="start_date"
                                name="start_date" placeholder="Start date" autocomplete="off">
                        </div>
                        <div id="timeline_div" class="form-group required">
                            <label class="control-label">Timeline</label>
                            <select class="form-control select2" id="timeline" name="timeline" placeholder="Timeline">
                                <option value="">Choose Timeline</option>
                                <option value="105" data-info="105">105 days</option>
                                <option value="210" data-info="210">210 days</option>
                                <option value="315" data-info="315">315 days</option>
                                <option value="420" data-info="420">420 days</option>
                                <option value="350" data-info="350">50 Weeks</option>
                                <option value="700" data-info="700">100 Weeks</option>
                                <option value="1050" data-info="1050">150 Weeks</option>
                                <option value="365" data-info="365">12 Months</option>
                                <option value="730" data-info="730">24 Months</option>
                                <option value="1095" data-info="1095">36 Months</option>
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
                            <input type="number" value="0" onkeyup="getExpectedTotal()" class="form-control"
                                id="amount_per_day" name="amount" placeholder="Amount" required readonly>
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
    const equbTypes = @json($equbTypes); // Pass the PHP variable to JavaScript

    function filterEqubTypes() {
        const selectedType = document.getElementById('type').value;
        const equbTypeSelect = document.getElementById('equb_type_id');

        // Clear previous options
        equbTypeSelect.innerHTML = '<option value="">choose...</option>';

        // Filter equbTypes based on the selected type
        const filteredTypes = equbTypes.filter(equbType => equbType.type === selectedType);

        // Populate the dropdown with filtered options
        filteredTypes.forEach(equbType => {
            const option = document.createElement('option');
            option.value = equbType.id;
            option.setAttribute('data-info', equbType.type);
            option.setAttribute('data-startdate', equbType.start_date);
            option.setAttribute('data-enddate', equbType.end_date);
            option.setAttribute('data-rote', equbType.rote);
            option.setAttribute('data-quota', equbType.quota);
            option.setAttribute('data-amount', equbType.amount); // Add amount data
            option.setAttribute('data-expected-total', equbType.expected_total); // Add expected total data
            option.textContent = `${equbType.name} round ${equbType.round}`;
            equbTypeSelect.appendChild(option);
        });
    }

    document.getElementById('type').addEventListener('change', function() {
        filterEqubTypes();

        // Reset fields when type changes
        document.getElementById('amount_per_day').value = '';
        document.getElementById('total_amount').value = '';
        document.getElementById('start_date').readOnly = false; // Reset readOnly state
    });

    document.getElementById('equb_type_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        // Check if the selected type is Automatic
        if (document.getElementById('type').value === 'Automatic') {
            const amount = selectedOption.getAttribute('data-amount');
            const expectedTotal = selectedOption.getAttribute('data-expected-total');
            const startDate = selectedOption.getAttribute('data-startdate');

            // Set the input fields
            document.getElementById('amount_per_day').value = amount;
            document.getElementById('total_amount').value = expectedTotal;
            document.getElementById('start_date').value = startDate; // Set start date
            document.getElementById('start_date').readOnly = true; // Make start date read-only

            // Make amount and total_amount fields read-only
            document.getElementById('amount_per_day').readOnly = true;
            document.getElementById('total_amount').readOnly = true;
        } else {
            // If not Automatic, clear the values and make fields editable
            document.getElementById('amount_per_day').value = '';
            document.getElementById('total_amount').value = '';
            document.getElementById('start_date').readOnly = false; // Make start date editable
            document.getElementById('amount_per_day').readOnly = false; // Make amount editable
            document.getElementById('total_amount').readOnly = false; // Make total_amount editable
        }
    });
</script>