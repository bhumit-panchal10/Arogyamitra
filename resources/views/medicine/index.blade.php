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
        <table id="medicineTable" class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                    <th class="text-center">{{ trans('messages.medicine.fields.serial_no') }}</th>
                    <th class="text-center">{{ trans('messages.medicine.fields.medicine') }}</th>
                    <th class="text-center">{{ trans('messages.medicine.fields.quantity') }}</th>
                    <th class="text-center">{{ trans('messages.medicine.fields.quantity_type') }}</th>
                    @if(Auth::user()->role == '1')
                    <th class="text-center">{{ trans('messages.medicine.fields.action') }}</th>
                    @endif

                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @forelse($medicines as $medicine)
                <tr>
                    <td class="text-center">{{$loop->iteration}}</td>
                    <td class="text-center">{{ $medicine->name }}</td>
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
                        @if(Auth::user()->role == '1')
                        <td class="text-center">
                        {!! Form::open(['route' => ['medicines.updateStatus', $medicine->id], 'method' => 'PUT', 'style' => 'display: inline-block;']) !!}
                        {!! Form::hidden('status', $medicine->status == 1 ? 0 : 1) !!}
                        {!! Form::button($medicine->status == 1 ? '<i class="fas fa-regular fa-lock-open"></i>' : '<i class="fas fa-regular fa-lock"></i>', ['type' => 'submit', 'class' => 'btn btn-success','data-toggle'=> 'tooltip', 'data-bs-custom-class'=>"tooltip-dark", $medicine->status == '1' ? 'title= Active' : 'title=Inactive', 'onclick' => "return confirm('Are you sure you want to toggle the status?')"]) !!}
                        {!! Form::close() !!}

                        @if(Auth::user()->role == '4' || Auth::user()->role == '5')
                        {{ $medicine->status ==1 ? 'Active' : 'Inactive'}}
                        @endif
                        <a class="btn btn-primary" data-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Show" href="{{ route('medicines.show', $medicine->id) }}"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-info" data-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Edit" href="{{ route('medicines.edit', $medicine->id) }}"><i class="fa fa-edit"></i></a>
                        {!! Form::open(['route' => ['medicines.destroy', $medicine->id], 'method' => 'DELETE', 'style' => 'display: inline-block;']) !!}
                        {!! Form::button('<i class="fa fa-trash"></i>', ['data-toggle'=>"tooltip", 'data-bs-custom-class'=>"tooltip-dark", 'title'=>"Delete",'type' => 'submit', 'class' => 'btn btn-danger', 'onclick' => "return confirm('Are you sure you want to delete this medicine?')"]) !!}
                        {!! Form::close() !!}
                    </td>
                    @endif
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
        // Initialize DataTables with search input functionality
        $('#medicineTable').DataTable({
            paginate: true,
            pageLength: 25,
            searching: true,
            order: [],
            columnDefs: [{
                targets: 'no-search',
                searchable: false,
            }],
        });

        // Apply search to DataTables
        $('#searchInput').on('keyup', function() {
            $('#medicineTable').DataTable().search($(this).val()).draw();
        });
    });

    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $('.print').click(function() {
        window.focus();
        window.print();
        location.reload(true);
        return false;
    });

    function updateStatus() {
        var selectedIds = [];
        $(':checkbox:checked').each(function (i) {
            selectedIds.push($(this).val());
        });

        Swal.fire({
            title: 'Confirmation',
            text: "Are you sure you want to change the status of medicine request?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Active',
            cancelButtonText: 'Cancel',
            confirmButtonColor: 'green',
            denyButtonText: 'Deactive',
            showCloseButton: true,
            showDenyButton: true,
            reverseButtons: false,
        }).then((result) => {
            console.log(result);

            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('change-medicine-status') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        ids: selectedIds,
                        status: 1,
                        type: 'multiple',
                    },
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: 'Success',
                                text: response.messages,
                                icon: 'success'
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.messages, //'Something went wrong!'
                                icon: 'error'
                            });
                        }
                    }
                });
            } else if (result.isDenied) {
                $.ajax({
                    url: "{{ url('change-medicine-status') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        ids: selectedIds,
                        status: 0,
                    },
                    dataType: "JSON",
                    success: function(response) {
                        console.log(response.status)
                        if (response.status) {
                            Swal.fire({
                                title: 'Success',
                                text: response.messages,
                                icon: 'success'
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.messages, //'Something went wrong!'
                                icon: 'error'
                            });
                        }
                    }
                });
            }
        });
    }
    function multiDelete() {
        var selectedIds = [];
        $(':checkbox:checked').each(function(i) {
            selectedIds.push($(this).val());
        });

        Swal.fire({
            text: "Are you sure you want to delete selected medicine?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'No, cancel',
            confirmButtonColor: 'red',
            showCloseButton: true,
            reverseButtons: false,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('delete-medicine') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        ids: selectedIds,
                        status: 1,
                    },
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: 'Success',
                                text: response.messages,
                                icon: 'success',
                                reverseButtons: false,

                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.messages,
                                icon: 'error'
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    }
                });
            }
        });
    }
</script>
@endsection