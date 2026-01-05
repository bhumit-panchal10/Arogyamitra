@extends('layouts.app')

@section('head')
<link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                    </svg>
                </span>
                <input type="text" id="searchInput" class="form-control form-control-solid w-250px ps-14" placeholder="Search" />
            </div>
        </div>
        @if(Auth::user()->role != '1')
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                <a href="#" class="btn btn-xs btn-success print"><i class="la la-print"></i>{{ trans('messages.medicine.fields.print') }}</a>
            </div>
        </div>
        @endif
    </div>

    <div class="card-body pt-0 table-responsive">
        <form id="storeStock" action="{{ route('medicine-stock.store') }}" method="post">
            @csrf
            <table id="medicineStockTable" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                        <th class="text-center">{{ trans('messages.medicine.fields.serial_no') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.medicine') }}</th>
                        <th class="text-center">{{ trans('messages.stock.fields.current') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.quantity_type') }}</th>
                        @if(Auth::user()->role == '1')
                        <th class="text-center">{{ trans('messages.medicine.fields.quantity') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @forelse($medicines as $medicine)
                    <tr>
                        <td class="text-center">{{$loop->iteration}}</td>
                        <td class="text-center">{{ $medicine->name }}</td>
                        <td class="text-center">{{ $medicine->qty }}</td>
                        <td class="text-center">{{ $medicine->qty_type }}</td>
                        @if(Auth::user()->role == '1')
                            <td class="text-center">
                                <input type="text" class="form-control" name="medicine[{{$medicine->id}}]" maxlength="5" onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                            </td>
                        @endif
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
        </form>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ url('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTables with search input functionality
        $('#medicineStockTable').DataTable({
            paginate: true,
            pageLength: 50,
            searching: true,
            order: [],
            columnDefs: [{
                targets: 'no-search',
                searchable: false,
            }],
        });

        // Apply search to DataTables
        $('#searchInput').on('keyup', function() {
            $('#medicineStockTable').DataTable().search($(this).val()).draw();
        });
    });


    $('#stockUpdate').click(function(){
        $('#storeStock').submit();
    });
</script>
@endsection