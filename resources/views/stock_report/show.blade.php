@extends('layouts.app')

@section('head')
<link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
        {!! Form::open(['route' => ['report.show', [$id]], 'method' => 'GET', 'id' => 'formId', 'class' => 'row row-cols-lg-auto g-3 align-items-center', 'autocomplete' => 'off']) !!}
            @csrf
            @foreach($medicines as $medicine)
                <h1 class="text-start fw-bolder text-uppercase gs-0">{{ $medicine->name }}</h1>
                @break
            @endforeach
            <div class="d-flex align-items-center ms-2">
                <input type="text" class="form-control" style="width: 230px;" id="date_range" placeholder="Select Date" name="date_range" onclick="getdate()" value="{{ request()->date_range }}" />
                {!! Form::submit('Submit', ['id' => 'submit', 'class' => 'btn btn-primary ms-2']) !!}
                <a href="{{ route('report.show', [$id]) }}" class="btn btn-light ms-2">Reset</a>
                {!! Form::close() !!}
            </div>

        </div>
    </div>
    <div class="card-body pt-0 table-responsive">

            <table id="kt_customers_table" class="table align-middle table-row-dashed fs-6 gy-5 w-10px pe-2">
                <thead>
                    <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                        <th class="text-center">{{ trans('messages.dashboard.fields.serial_no') }}</th>
                        <th class="text-center">{{ trans('messages.stock.fields.date') }}</th>
                        <th class="text-center">{{ trans('messages.stock.fields.type') }}</th>
                        <th class="text-center">{{ trans('messages.stock.fields.qty') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.quantity_type') }}</th>
                        <th class="text-center">{{ trans('messages.stock.fields.delivered') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @forelse($medicines as $medicine)
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="text-center">{{ $medicine->track_date ? date('d-m-Y', strtotime($medicine->track_date)) : '' }}</td>

                            <td class="text-center">{{ $medicine->mode }}</td>
                                <td class="text-center">{{ $medicine->medicine_qty }}</td>
                                <td class="text-center">{{ $medicine->qty_type }}</td>
                                <td class="text-center">@if ($medicine->mode == 'C') {{ $medicine->uname }}  @else - @endif</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">
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

        $(document).on('click', '.checkbox', function() {
            var selectedCount = $('.checkbox').filter(':checked').length;
            console.log(selectedCount);
            if (selectedCount > 0) {
                $('.showCount').text(selectedCount + " Selected");
                $('.showCount').removeClass('d-none');
                $('#selectAllCheckbox').prop("checked", false);
            } else {
                $('.showCount').addClass('d-none');
                $('#selectAllCheckbox').prop("checked", false);
            }

            var totalCheckboxes = parseFloat($('input:checkbox').length) - 1;
            console.log(totalCheckboxes,selectedCount);
            if (totalCheckboxes == selectedCount) {
                $('#selectAllCheckbox').prop("checked", true);
            }

            if ($('.checkbox:checked').length > 0) {
                $('button[type=button]').removeClass('d-none');
            } else {
                $('button[type=button]').addClass('d-none');
            }
        });

        $('#selectAllCheckbox').click(function() {
            var checkboxes = $('.checkbox');
            if ($(this).is(':checked') == true) {
                $('button[type=button]').removeClass('d-none');
                $('.showCount').removeClass('d-none');
                checkboxes.prop('checked', $(this).is(':checked'));
            } else {
                $('.showCount').addClass('d-none');
                $('button[type=button]').addClass('d-none');
            }
            var selectedCount = checkboxes.filter(':checked').length;
            $('.showCount').text(selectedCount + " Selected");
        });

        $('#kt_customers_table').DataTable({
            paginate: true,
            searching: true,
            pageLength: 25,
            order: [],
            columnDefs: [{
                'targets': [0],
                'orderable': false,
                //targets: 'no-search',
                searchable: false,
            }],
        });

        // search to DataTables
        $('#searchInput').on('keyup', function() {
            $('#kt_customers_table').DataTable().search($(this).val()).draw();
        });

            //
    });

    function updateStatusChange(modeC, modeR, date, name, qty, qty_type) {
        Swal.fire({
            title: 'Medicine Quantity Information',
            confirmButtonText: 'Ok',
            confirmButtonColor: 'green',
            reverseButtons: false,
            html: '<div class="text-center">' +
                    '<table style="text-align: center; margin: auto; width: 300px;">'+
                        '<tr><td style="padding-bottom: 10px;">Date : </td><td style="padding-bottom: 10px;">' + date + '</td></tr>' +
                        '<tr><td style="padding-bottom: 10px;">Comsume : </td><td style="padding-bottom: 10px;">' + modeC + '</td></tr>' +
                        '<tr><td style="padding-bottom: 10px;">Received : </td><td style="padding-bottom: 10px;">' + modeR + '</td></tr>' +
                        '<tr><td style="padding-bottom: 10px;">Quantity : </td><td style="padding-bottom: 10px;">' + qty + '</td></tr>' +
                        '<tr><td style="padding-bottom: 10px;">Quantity Type : </td><td style="padding-bottom: 10px;">' + qty_type + '</td></tr>' +
                        '<tr><td style="padding-bottom: 10px;">Delivered To : </td><td style="padding-bottom: 10px;">' + name + '</td></tr>' +
                    '<table>' +
                   '<div>',
        });
    }

    function hide() {
        const checkbox = document.getElementById('status');
        checkbox.classList.toggle('d-none');
    }

    function getdate() {
        $("#filterType").val("date_filter");
    }
    $("#date_range").flatpickr({
        mode: "range",
        defaultDate: ["{{ $start_date }}", "{{ $end_date }}"],
        dateFormat: "d-m-Y"
    });


</script>
@endsection