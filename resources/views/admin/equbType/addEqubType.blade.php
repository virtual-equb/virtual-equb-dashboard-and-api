@can('draw equb_type_winner')
<div class="modal fade" id="drawModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form role="form" method="post" class="form-horizontal" action="{{ route('drawAutoWinners') }}" enctype="multipart/form-data" id="drawEqubType">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h4 class="modal-title"> Draw</h4>
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
                                        value="{{ $equbType->id }}">
                                    {{ $equbType->name }} round {{ $equbType->round }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" onclick="drawAutoWinners()">Draw</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<script>
    document.getElementById('draw_type').addEventListener('change', function() {
        var selectedType = this.value; // Get the selected draw type
        var equbTypeSelect = document.getElementById('equbTypeId'); // Get the "Equb Type" select element
        var form = document.getElementById('drawEqubType'); // Get the form element

        // Clear existing options in the "Equb Type" dropdown
        equbTypeSelect.innerHTML = '<option selected value="">Choose Equb Type</option>';

        // Get the equb types from the original options (passed from Laravel to JS)
        var equbTypes = @json($equbTypes); // Pass the PHP array to JavaScript

        // Loop through the equb types and filter them based on the selected draw type
        equbTypes.forEach(function(equbType) {
            // Check if the current equbType matches the selected draw type
            if (equbType.type === selectedType) {
                // Create a new option element for the matching equb type
                var option = document.createElement('option');
                option.value = equbType.id; // Set the value to the equbType ID
                option.setAttribute('data-info', equbType.type);
                option.setAttribute('data-startdate', equbType.start_date);
                option.setAttribute('data-enddate', equbType.end_date);
                option.setAttribute('data-rote', equbType.rote);
                option.setAttribute('data-quota', equbType.quota);
                
                // Set the text content to display the name and round information
                option.textContent = equbType.name + ' round ' + equbType.round;
                
                // Append the newly created option to the "Equb Type" select element
                equbTypeSelect.appendChild(option);
            }
        });

        // Change the form action based on the selected draw type
        if (selectedType === 'Seasonal') {
            form.action = "{{ route('drawAutoSeasonal') }}"; 
        } else {
            form.action = "{{ route('drawAutoWinners') }}";
        }
    });
</script>