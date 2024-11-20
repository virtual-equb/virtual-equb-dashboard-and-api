{{-- @if (Auth::user()->role == 'member') --}}
<table id="equb-list-table" class="table table-bordered table-striped">      
    <thead>
        <tr>
            <th></th>
            <th>No</th>
            <th>Equb Type</th>
            <th>Amount in Birr</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Total Payment</th>
            <th>Remaining Payment</th>
            <th>Expected Total</th>
            <th>Remaining Date</th>
            <th>Lottery Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($equbs as $key => $equb)  {{-- Use paginated $equbs --}}
            <tr id="tre{{ $equb['id'] }}">
                <?php
                $totalPpayment = App\Models\Payment::where('equb_id', $equb['id'])->where('status', 'paid')->sum('amount');
                $totalEqubAmount = App\Models\Equb::where('status', 'Active')->where('id', $equb['id'])->value('total_amount');
                $remainingPayment = $totalEqubAmount - $totalPpayment;
                $lotteryDate = explode(',', App\Models\Equb::where('status', 'Active')->where('id', $equb['id'])->value('lottery_date'))[0];
                $currentDate = new DateTime();
                $lotteryDateTime = new DateTime($lotteryDate);
                $interval = $lotteryDateTime > $currentDate ? $lotteryDateTime->diff($currentDate)->days : ($lotteryDateTime == $currentDate ? 0 : 'passed');
                ?>
                <td class="details-control_lottery" id="{{ $equb['id'] }}"></td>
                <td>{{ $key + 1 + ($equbs->currentPage() - 1) * $equbs->perPage() }}</td>
                <td>
                    <a href="javascript:void(0);" onclick="openPaymentTab({{ $equb }})">{{ $equb->equbType->name }} round {{ $equb->equbType->round }}</a>
                </td> 
                <td>{{ number_format($equb->amount) }}</td>
                <td>{{ (new DateTime($equb['start_date']))->format("M-j-Y") }}</td>
                <td>{{ (new DateTime($equb['end_date']))->format("M-j-Y") }}</td>
                <td>{{ number_format($totalPpayment) }}</td>
                <td>{{ number_format($remainingPayment) }}</td>
                <td>{{ number_format($equb->total_amount) }}</td>
                <td>
                    @foreach(explode(',', $equb->lottery_date) as $lottery_date)
                        {{ (new DateTime($lottery_date))->format("M-j-Y") }}<br>
                    @endforeach
                </td>   
                <td>{{ $interval }}</td>
                <td>{{ $equb->status }}</td>                           
            </tr>
            <tr class="child-row" style="display:none;">
                <td colspan="12">
                    {{-- Nested table or additional details can go here --}}
                    <div class="nested-table">
                        <h5>Additional Details for Equb ID: {{ $equb['id'] }}</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Detail 1</th>
                                    <th>Detail 2</th>
                                    <th>Detail 3</th>
                                    {{-- Add more detail headers as needed --}}
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Populate nested details here --}}
                                @foreach($equb->payments as $payment) {{-- Assuming payments relation exists --}}
                                    <tr>
                                        <td>{{ $payment->detail1 }}</td>
                                        <td>{{ $payment->detail2 }}</td>
                                        <td>{{ $payment->detail3 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Pagination Links --}}
<div class="mt-4">
    {{ $equbs->links() }}  {{-- Include pagination links --}}
</div>

{{-- Modal for Equb Status Change --}}
<div class="modal modal-danger fade" id="aaaa" tabindex="-1" role="dialog" aria-labelledby="Delete" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <p class="modal-title" id="exampleModalLabel">Update Equb Type Status</p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="bbbb">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <p class="text-center">Are you sure you want to update status?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-danger">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function equbStatusChange(item){
        $('#aaaa').modal('show');
        $('#bbbb').attr('action', "{{ url('member/equbStatus-update') }}" + '/' + item.id);   
    }

    $(function () {
        if ($.fn.DataTable.isDataTable('#equb-list-table')) {
            $('#equb-list-table').DataTable().destroy();
        }
        var table = $("#equb-list-table").DataTable({
            responsive: false,
            lengthChange: false,
            searching: false,
            autoWidth: false,
            bSort: false,
            bDestroy: true,
            language: { 
                search: "",
                searchPlaceholder: "Search",
            }
        });

        $('#equb-list-table tbody').on('click', 'td.details-control_lottery', function() {
            var tr = $(this).closest('tr').next('.child-row');
            if (tr.is(':visible')) {
                tr.hide();
            } else {
                tr.show();
            }
        });
    });
</script>
