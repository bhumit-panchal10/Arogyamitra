@extends('layouts.app')

@section('head')
<link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
<style>
    .col-2 {
        width: 25%;
    }
    .col-md-6 {
        width: 16%;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="card-body md-5">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                {!! Form::open(['route' => 'order.medicines.index', 'method' => 'POST', 'class' => 'row row-cols-lg-auto g-3 align-items-center', 'autocomplete' => 'off']) !!}
                @csrf

                @if (Auth::user()->role == 1)
                <div class="col-2">
                    <label class="form-label fs-6 fw-bolder text-dark">Prant</label>
                    {{ Form::select('prant_id', $prant, $selPrant, ['class' => 'form-select', 'id' => 'prant', 'data-control' => 'select2', 'onchange' => 'getVibhagList(this.value)', 'placeholder' => 'Select Prant']) }}
                </div>
                @endif

                @if (Auth::user()->role == 5 || Auth::user()->role == 1)
                <div class="col-2">
                    <label class="form-label fs-6 fw-bolder text-dark">Vibhag</label>
                    {{ Form::select('vibhag_id', $vibhag, $selVibhag, ['class' => 'form-select', 'id' => 'vibhag', 'data-control' => 'select2', 'onchange' => 'getJillaList(this.value)', 'placeholder' => 'Select Vibhag']) }}
                </div>
                @endif
                <div class="col-2">
                    <label class="form-label fs-6 fw-bolder text-dark">Jilla</label>
                    {{ Form::select('jilla_id', $jilla, $selJilla, ['class' => 'form-select', 'id' => 'jilla', 'data-control' => 'select2', 'onchange' => 'getTalukaList(this.value)', 'placeholder' => 'Select Jilla']) }}
                </div>
                <div class="col-md-8 fv-row mt-3" style="width: 23%;">
                    <label class="fs-6 fw-bolder text-dark mb-2">Date</label>
                    <input type="text" class="form-control" id="date_range" placeholder="Select Date" onclick="getdate()" name="date_range" value="{{ request()->date_range }}" />
                </div>

                <div class=" col-2md-5 aqua mt-5">
                    <div style="display: flex;">
                        <input type="hidden" id="selected_date" name="selected_date">
                        {!! Form::submit('Submit', ['id' => 'submit', 'class' => 'btn btn-primary me-2 mt-6']) !!}
                        <a href="" class="btn btn-light me-2 mt-6">Reset</a>
                        <button type="button" id="exportButton" class="btn btn-info me-2 mt-6" onclick="exportCSV('medicineTable')">Export</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                @if($medicinesReq->count() > 0)
                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                    </svg>
                </span>
                <div class="col-6">
                    <input type="text" id="searchInput" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search" />
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body pt-0 table-responsive">
        <div class="medicine_list d-none"> Medicine List</div>
        <table id="medicineTable" class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                    <th class="text-center">{{ trans('messages.medicine.fields.serial_no') }}</th>
                    <th class="text-center">{{ trans('messages.medicine.fields.medicine') }}</th>
                    <th class="text-center">{{ trans('messages.medicine_request.request_quantity') }}</th>
                    <th class="text-center">{{ trans('messages.medicine_request.request_quantity_type') }}</th>
                    <th class="text-center">{{ trans('messages.medicine_request.quantity') }}</th>
                    <th class="text-center">{{ trans('messages.medicine_request.quantity_type') }}</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @forelse($medicinesReq as $medicine)
                <tr>
                    <td class="text-center">{{$loop->iteration}}</td>
                    <td class="text-center">{{ $medicine->name }}</td>
                    <td class="text-center">{{ $medicine->request_medicine}}</td>
                    <td class="text-center">
                        @php
                        if($medicine->qty_type == 'નંગ' || $medicine->qty_type == 'ગ્રામ') {
                            $qtyType = 'Packet';
                        } else {
                            $qtyType = 'Bottle';
                        }
                        @endphp
                        {{ $qtyType }}
                    </td>
                    <td class="text-center">{{ $medicine->qty }}</td>
                    @if($medicine->qty_type=='નંગ')
                    @php
                    $qtyType = 'Pcs(નંગ)'
                    @endphp
                    @elseif($medicine->qty_type=='ગ્રામ')
                    @php
                    $qtyType = 'Grm(ગ્રામ)'
                    @endphp
                    @else
                    @php
                    $qtyType = 'Ml(મી.લી.)'
                    @endphp
                    @endif
                    <td class="text-center">{{ $qtyType }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" style="text-align: center;">
                        No record found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ url('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        var prantName = $('#prant').find(':selected').text();
        var vibhagName = $('#vibhag').find(':selected').text();
        var jillaName = $('#jilla').find(':selected').text();
        var vibhag = $('#vibhag');
        var jilla = $('#jilla');
        var vibhagVal = $('#vibhag').val();
        var jillaVal = $('#jilla').val();

        // Check if prantName is not 'Select Prant'
        if (prantName !== 'Select Prant') {

            // Add a new placeholder option
            vibhag.prepend("<option value=''>Select Vibhag of" + prantName + "</option>");

            // Remove the second option
            vibhag.find('option:eq(1)').remove();
        }

        if (prantName !== 'Select Prant' && vibhagVal == '') {

            // Add a new placeholder option
            vibhag.prepend("<option value=''>Select Vibhag " + prantName + "</option>");

            // Remove the second option
            vibhag.find('option:eq(1)').remove();
        }

        if (prantName !== 'Select Prant' && vibhagVal !== '') {

            // Add a new placeholder option
            jilla.prepend("<option value=''>Select Jilla of " + vibhagName + "</option>");

            // Remove the second option
            jilla.find('option:eq(1)').remove();

            $('#prant').on('change', function() {

                // Clear all existing options except the default option at position 0
                jilla.find('option:not(:first)').remove();

                // Add a default option
                jilla.prepend("<option value='' selected>Select Jilla</option>");

                //remove second option
                jilla.find('option:eq(1)').remove();
            });
        }

    });

    function getVibhagList(id) {
        $('#prant').on('change', function() {
            $('#vibhag').val('');
        });
        $("#filterType").val("prant")
        if (id) {
            $.ajax({
                url: "{{ url('vibhag-list') }}",
                type: 'GET',
                data: {
                    prantId: id
                },
                dataType: "JSON",
                success: function(response) {
                    var html = '';
                    html += "<option value=''> Select Vibhag of " +  $('#prant').find(':selected').text() + "</option>";
                    if (response) {
                        $.each(response, function(key, val) {
                            html += "<option value='" + val.id + "'>" + val.name + "</option>";
                        });
                        $('#vibhag').html(html);
                    } else {
                        $('#vibhag').html('');
                    }
                }
            });
        } else {
            console.log('dsaf');
            html = "<option value=''> Select Vibhag</option>";
            $('#vibhag').html(html);
        }
    }

    function getJillaList(id) {
        $("#filterType").val("vibhag");
        if (id) {
            $.ajax({
                url: "{{ url('jilla-list') }}",
                type: 'GET',
                data: {
                    vibhagId: id
                },
                dataType: "JSON",
                success: function(response) {
                    var html = '';
                    if (response) {
                        html += "<option value=''> Select Jilla of " + $('#vibhag').find(':selected').text() + "</option>";
                        $.each(response, function(key, val) {
                            html += "<option value='" + val.id + "'>" + val.name + "</option>";
                        });
                        $('#jilla').html(html);
                    } else {
                        $('#jilla').html('');
                    }
                }
            });
        } else {
            html = "<option value=''> Select Jilla </option>";
            $('#jilla').html(html);
        }
    }

    function getTalukaList(id) {
        $("#filterType").val("jilla");
        if (id) {
            $.ajax({
                url: "{{ url('taluka-list') }}",
                type: 'GET',
                data: {
                    jillaId: id
                },
                dataType: "JSON",
                success: function(response) {
                    var html = '';
                    if (response) {
                        html += "<option value=''> Select Taluka of " + $('#jilla').find(':selected').text() + "</option>";
                        $.each(response, function(key, val) {
                            html += "<option value='" + val.id + "'>" + val.name + "</option>";
                        });
                        $('#taluka').html(html);
                    } else {
                        $('#taluka').html('');
                    }
                }
            });
        } else {
            html = "<option value=''> Select Taluka </option>";
            $('#taluka').html(html);
        }
    }

    function getdate() {
        $("#filterType").val("date_filter");
    }

    $(document).ready(function() {
        // Initialize DataTables with search input functionality
        const table = $('#medicineTable').DataTable({
            paginate: true,
            pageLength: 25,
            searching: true,
            order: [],
            columnDefs: [{
                'targets': [0],
                'orderable': false,
                //targets: 'no-search',
                searchable: false,
            }],
        });

        // Apply search to DataTables
        $('#searchInput').on('keyup', function() {
            $('#medicineTable').DataTable().search($(this).val()).draw();
        });

    });

    $("#date_range").flatpickr({
        mode: "range",
        defaultDate: ["{{ $start_date }}", "{{ $end_date }}"],
        dateFormat: "d-m-Y"
    });

    function exportCSV(medicineTable) {
        var table = document.getElementById(medicineTable);
        var rows = table.querySelectorAll('tbody tr');
        var csvContent = "data:text/csv;charset=utf-8,";

        var headers = Array.from(table.querySelectorAll('thead th')).map(header => header.textContent.trim());
        csvContent += headers.join(',') + '\n';

        rows.forEach(row => {
            var rowData = Array.from(row.children).map(cell => cell.textContent.trim());
            csvContent += rowData.join(',') + '\n';
        });

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "Medicine-Order-Report.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection