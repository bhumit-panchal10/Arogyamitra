@extends('layouts.app')

@section('head')
<link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            {!! Form::open(['route' => 'report.backend', 'method' => 'GET', 'class' => 'row row-cols-lg-auto g-3 align-items-center', 'autocomplete' => 'off']) !!}
            @csrf
            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                </svg>
            </span>
            <input type="text" id="searchInput" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search" />
        </div>
    </div>
    <div class="card-body pt-0 table-responsive">
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
                                <a href="{{ route('report.show', $medicine->id) }}" class="btn btn-info"><i class="fa fa-eye"></i></a>
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
    });

</script>
@endsection