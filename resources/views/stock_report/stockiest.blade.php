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
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            {!! Form::open(['route' => 'report.stockiest', 'method' => 'POST', 'class' => 'row row-cols-lg-auto g-3 align-items-center', 'autocomplete' => 'off']) !!}
            @csrf
            <div class="d-flex align-items-center ms-2">
                {{ Form::select('stockiest_id', $stockiest, $selStockiest, ['class' => 'form-select', 'id' => 'stockiest', 'data-control' => 'select2', 'placeholder' => 'Select Stockiest']) }}
                {!! Form::submit('Submit', ['id' => 'submit', 'class' => 'btn btn-primary ms-2']) !!}
                    <a href="{{ route('report.stockiest') }}" class="btn btn-light me-2 ms-2">Reset</a>
                {!! Form::close() !!}
            </div>

        </div>
    </div>
    <div class="card-body pt-0 table-responsive">
        <div class="medicine_list d-none"> Medicine Stock List</div>
            <table id="kt_customers_table" class="table align-middle table-row-dashed fs-6 gy-5 w-10px pe-2">
                <thead>
                    <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                        <th class="text-center">{{ trans('messages.dashboard.fields.serial_no') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.medicine') }}</th>
                        <th class="text-center">{{ trans('messages.stock.fields.current') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.quantity_type') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.action') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @forelse($medicines as $medicine)
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td class="text-center">{{ $medicine->name }}</td>
                            <td class="text-center">{{ $medicine->qty }}</td>
                            <td class="text-center">{{ $medicine->qty_type }}</td>
                            <td class="text-center">
                               <a href="{{ route('report.stockiest-show', [$medicine->id, $selStockiest]) }}" class="btn btn-info"><i class="fa fa-eye"></i></a>
                            </td>
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
        // Initialize DataTables with search input functionality
        $('#kt_customers_table').DataTable({
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
            $('#kt_customers_table').DataTable().search($(this).val()).draw();
        });
    });

</script>
@endsection