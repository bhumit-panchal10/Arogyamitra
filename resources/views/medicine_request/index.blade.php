@extends('layouts.app')

@section('head')
    <link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <style>
        .nav-tabs .nav-link.active {
            background-color: #da7643;
            color: #fff;
            font-weight: 600;
        }

        /* Hover effect */
        .nav-tabs .nav-link {
            transition: all 0.25s ease;
            border-radius: 6px 6px 0 0;
        }

        .nav-tabs .nav-link:hover {
            background-color: #da7643;
            /* light blue */
            color: #fff;
            /* primary blue */
        }

        /* Keep active tab strong */
        /* .nav-tabs .nav-link.active {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            background-color: #0d6efd;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            color: #fff;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            font-weight: 600;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } */
    </style>
    <div class="card">

        @include('medicine_request.orderTab')

        <div class="row">
            @if (request()->status == 2)
                <div class="col-md-12  text-end">
                    <a href="{{ route('medicineRequest.export', request('status', 1)) }}" class="btn btn-success btn-sm mr-3">
                        Export Excel
                    </a>

                </div>
            @endif
        </div>


        <div class="card-header border-0 pt-6">
            @if (request()->status == 2 || request()->status == 3)
                <div class="card-title">
                    {!! Form::open([
                        'route' => 'medicineRequest.index',
                        'method' => 'POST',
                        'class' => 'row row-cols-lg-auto g-3 align-items-center',
                        'autocomplete' => 'off',
                    ]) !!}
                    @csrf
                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path
                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                fill="black" />
                        </svg>
                    </span>
                    <input type="hidden" name="status" value="{{ request()->status }}">
                    <input type="text" id="searchInput" data-kt-user-table-filter="search"
                        class="form-control form-control-solid w-250px ps-14" placeholder="Search" />
                    @if (request()->status == 3)
                        <div class="col-12">
                            <input type="date" name="fromdate" id="fromdate"
                                class="form-control form-control-solid w-250px ps-14" placeholder="date"
                                value="{{ $fromdate ?? '' }}" />
                        </div>
                        To
                        <div class="col-12">
                            <input type="date" id="Todate" name="Todate"
                                class="form-control form-control-solid w-250px ps-14" placeholder="date"
                                value="{{ $todate ?? '' }}" />
                        </div>
                        <input type="submit" name="search" class="btn btn-secondary" style="margin-right: 7px">
                        <a href="{{ url('medicineRequest?status=3') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    @endif

                    {{-- <div class="col-12">
                        <select id="Stockiest" name="Stockiest" class="form-control w-150px" data-kt-select2="true">
                            <option value="">Select Stockiest</option>
                            @foreach ($stockiestuser as $stockiest)
                                <option value="{{ $stockiest->id }}">{{ $stockiest->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div> --}}

                    {{-- <div class="col-12">
                        {!! Form::submit('Submit', ['class' => 'btn btn-primary me-2']) !!}
                        <a href="" class="btn btn-light">Reset</a>
                        <span class="me-2 d-none showCount"></span>
                        <button type="button" class="btn btn-info d-none" onclick="updateStatusChange()">Change
                            status</button>
                        {!! Form::close() !!}
                    </div> --}}
                    {!! Form::close() !!}
                </div>
            @endif

        </div>


        <div class="row">
            <div class="col-md-3">

                @if (request()->status == 1)
                    <button class="btn btn-xs btn-success" id="bulkAcceptBtn">
                        Accept Request
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body pt-0 table-responsive">


            <table id="kt_customers_table" class="table align-middle table-row-dashed fs-6 gy-5 w-10px pe-2">
                <thead>
                    <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                        {{-- @if (request()->status == 1)
                            <th class="text-center">
                                <input type="checkbox" id="selectAll">
                            </th>
                        @endif --}}
                        <!-- @if ($totalMedicinePending > 0 || Auth::user()->role != '5')
    <th class="text-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3 {{ $dNone }}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <input class="form-check-input" type="checkbox" data-kt-check="true" id="selectAllCheckbox" data-kt-check-target="#kt_customers_table .form-check-input" value="" />
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </th>
@elseif (Auth::user()->role == '5')
    <th class="text-center">{{ trans('messages.dashboard.fields.serial_no') }}</th>
    @endif -->
                        <th class="text-center">{{ trans('messages.dashboard.fields.serial_no') }}</th>

                        <th class="text-center">{{ trans('messages.medicine_request.user_name') }}</th>
                        @if (Auth::user()->role == 5)
                            <th class="text-center">{{ trans('messages.medicine_request.vibhag') }}</th>
                        @endif
                        <th class="text-center">{{ trans('messages.medicine_request.jilla') }}</th>
                        <th class="text-center">{{ trans('messages.medicine_request.medicine_name') }}</th>
                        {{-- @if (Auth::user()->role == 1)
                            <th class="text-center">{{ trans('messages.medicine_request.current_quantity') }}</th>
                        @endif --}}
                        <th class="text-center">{{ trans('messages.medicine_request.quantity') }}</th>
                        <th class="text-center">{{ trans('messages.medicine_request.request') }}</th>
                        <th class="text-center">{{ trans('messages.medicine_request.status') }}</th>
                        @if (request()->status == 2)
                            <th class="text-center">{{ trans('messages.medicine_request.delivered_quantity') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @forelse($medicineRequest as $medicine)
                        <tr>

                            {{-- @if (request()->status == 1)
                                <td class="text-center">
                                    <input type="checkbox" class="rowCheckbox" data-medicine="{{ $medicine['id'] }}"
                                        data-user="{{ $medicine['arogyamitra_id'] }}">
                                </td>
                            @endif --}}
                            @php
                                $mids = \App\Models\MedicineRequest::select(DB::raw('GROUP_CONCAT(id) AS mrId'))
                                    ->where([
                                        'arogyamitra_id' => $medicine['arogyamitra_id'],
                                        'status' => '1',
                                        'medicine_id' => $medicine['id'],
                                    ])
                                    ->groupBy('medicine_id', 'app_user_id')
                                    ->first();

                            @endphp
                            {{-- @if ($medicine['status'] == '1' && (Auth::user()->role == '1' || Auth::user()->role == '4' || Auth::user()->role != '5'))
                                <td class="text-center">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input checkbox" type="checkbox"
                                            value="{{ $mids['mrId'] ? $mids['mrId'] : '' }}" />
                                        <input type="hidden" name="app_user_id[]" class="app_user_id"
                                            value="{{ $medicine['arogyamitra_id'] }}">
                                    </div>
                                </td>
                            @elseif (Auth::user()->role == '5')
                                <td class="text-center">{{ $loop->iteration }}</td>
                            @endif --}}
                            <td class="text-center">{{ $loop->iteration }}</td>

                            <td class="text-center">
                                {{ $medicine['vibhag_name'] }}
                            </td>
                            @if (Auth::user()->role == 5)
                                <td class="text-center">{{ $medicine['vibhag_name'] }}</td>
                            @endif

                            <td class="text-center">{{ $medicine['jilla_name'] }}</td>
                            <td class="text-center">{{ $medicine['medicine_name'] }}</td>

                            {{-- @if (Auth::user()->role == 1)
                                @php
                                    $stock = \App\Models\MedicineStock::select('qty')
                                        ->where([
                                            'arogyamitra_id' => Auth::user()->id,
                                            'medicine_id' => $medicine['id'],
                                        ])
                                        ->first();
                                @endphp

                                <td class="text-center">
                                    {{ $stock ? $stock->qty : 0 }}
                                </td>
                            @endif --}}


                            @if (request()->status == 3)
                                <td class="text-center">
                                    {{ $medicine['deliverd_qty'] }}
                                </td>
                            @elseif(request()->status == 1 || request()->status == 0)
                                <td class="text-center">
                                    {{ $medicine['total_request'] }}
                                </td>
                            @else
                                <td class="text-center">
                                    {{ $medicine['medicinereq_delivered_quantity'] }}
                                </td>
                            @endif
                            @if (request()->status == 3)
                                <td class="text-center">
                                    {{ $medicine['deliverd_date'] ? date('d-m-Y', strtotime($medicine['deliverd_date'])) : date('d-m-Y') }}
                                </td>
                            @else
                                <td class="text-center">
                                    {{ $medicine['created_at'] ? date('d-m-Y', strtotime($medicine['created_at'])) : date('d-m-Y') }}
                                </td>
                            @endif
                            @if (Auth::user()->role == '1')
                                <td class="text-center">
                                    @if ($medicine['status'] == 1)
                                        <div class="badge badge fw-bolder">
                                            {{-- <a href="javascript:void(0);" type="submit"
                                                class="btn btn-xs btn-success acceptStock" data-toggle="tooltip"
                                                data-bs-custom-class="tooltip-dark" title="Accept"
                                                data-medicine="{{ $medicine['id'] }}"
                                                data-arogyamitra_id="{{ $medicine['arogyamitra_id'] }}"><i
                                                    class="fa fa-check"></i></a> --}}

                                            <input type="number" class="form-control deliveredQty"
                                                data-id="{{ $medicine['mrId'] }}" placeholder="Qty">


                                            {!! Form::open([
                                                'style' => 'display: inline-block;',
                                                'method' => 'POST',
                                                'onsubmit' => "return confirm('Are you sure you want to reject this request?');",
                                                'route' => ['medicineRequest.updateRequestStatus', $medicine['arogyamitra_id']],
                                            ]) !!}
                                            {!! Form::hidden('arogyamitra_id', $medicine['arogyamitra_id']) !!}
                                            {!! Form::hidden('medicine_req_id', $medicine['mrId']) !!}
                                            {!! Form::hidden('status', '0') !!}
                                            {!! Form::button('<i class="fa fa-ban"></i>', [
                                                'type' => 'submit',
                                                'class' => 'btn btn-xs btn-danger',
                                                'data-toggle' => 'tooltip',
                                                'data-bs-custom-class' => 'tooltip-dark',
                                                'title' => 'Reject',
                                            ]) !!}
                                            {!! Form::close() !!}
                                        </div>
                                    @elseif($medicine['status'] == 2)
                                        <div class="badge badge-light-success fw-bolder">Accepted</div>
                                    @elseif($medicine['status'] == 0)
                                        <div class="badge badge-light-danger fw-bolder">Rejected</div>
                                    @elseif($medicine['status'] == 3)
                                        <div class="badge badge-light-success fw-bolder">Deliverd</div>
                                    @endif
                                </td>
                            @else
                                @if ($medicine['status'] == '1')
                                    <td>
                                        <div class="badge badge-light-primary fw-bolder">Pending</div>
                                    </td>
                                @elseif($medicine['status'] == '2')
                                    <td>
                                        <div class="badge badge-light-success fw-bolder">Accepted</div>
                                    </td>
                                @elseif($medicine['status'] == 3)
                                    <div class="badge badge-light-success fw-bolder">Deliverd</div>
                                @else
                                    <td>
                                        <div class="badge badge-light-danger fw-bolder">Rejected</div>
                                    </td>
                                @endif
                            @endif
                            @if (request()->status == 2)
                                <td class="text-center">

                                    {!! Form::open([
                                        'route' => 'medicineRequest.delivered_flag_update',
                                        'method' => 'POST',
                                    ]) !!}
                                    @csrf

                                    <input type="hidden" name="medicine_request_id" value="{{ $medicine['mrId'] }}">
                                    <input type="hidden" name="arogyamitra_id"
                                        value="{{ $medicine['arogyamitra_id'] }}">

                                    <button type="submit" class="btn btn-primary btn-sm mt-2">
                                        Delivered
                                    </button>

                                    {!! Form::close() !!}
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" style="text-align: center;">
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
                console.log(totalCheckboxes, selectedCount);
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
            var status = "{{ request()->status }}";
            if (status == '0' || status == '2') {
                $('#selectAllCheckbox').addClass('d-none');
            }

            // search to DataTables
            $('#searchInput').on('keyup', function() {
                $('#kt_customers_table').DataTable().search($(this).val()).draw();
            });
        });

        function updateStatusChange() {
            var selectedIds = [];
            var appUserIds = $('.app_user_id').val();
            $(':checkbox:checked').each(function(i) {
                selectedIds.push($(this).val());
            });
            Swal.fire({
                title: 'Confirmation',
                text: "Are you sure you want to change the status of medicine request?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Accept',
                cancelButtonText: 'Cancel',
                confirmButtonColor: 'green',
                denyButtonText: 'Reject',
                showCloseButton: true,
                showDenyButton: true,
                reverseButtons: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Stock provide to stockiest',
                        input: 'text',
                        inputPlaceholder: 'Enter your quantity.',
                        showCancelButton: true,
                        confirmButtonText: 'Submit',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: "{{ url('change-status') }}",
                                type: 'GET',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    ids: selectedIds,
                                    status: 2,
                                    appIds: appUserIds,
                                    qty: result.value,
                                    type: "Multiple"
                                },
                                dataType: "JSON",
                                success: function(response) {
                                    if (response.status == 1 || response.status == 2) {
                                        Swal.fire({
                                            title: 'Success',
                                            text: response.message,
                                            icon: 'success'
                                        });
                                        location.reload();
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'Something went wrong!',
                                            icon: 'error'
                                        });
                                        location.reload();
                                    }
                                }
                            });
                        } else {
                            Swal.fire("Please enter your quantity.", "", "error");
                        }
                    });
                } else if (result.isDenied) {
                    $.ajax({
                        url: "{{ url('change-status') }}",
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            ids: selectedIds,
                            status: 0,
                            appIds: appUserIds,
                            type: "Multiple"
                        },
                        dataType: "JSON",
                        success: function(response) {
                            if (response.status == 1 || response.status == 2) {
                                Swal.fire({
                                    title: 'Success',
                                    text: response.message,
                                    icon: 'success'
                                });
                                location.reload();
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Something went wrong!',
                                    icon: 'error'
                                });
                                location.reload();
                            }
                        }
                    });
                }
            });
        }

        function hide() {
            const checkbox = document.getElementById('status');
            checkbox.classList.toggle('d-none');
        }

        $(document).on('click', '.acceptStock', function() {
            var medicine = $(this).data("medicine");
            var user_id = $(this).data("arogyamitra_id");

            Swal.fire({
                title: 'Stock provide to stockiest',
                input: 'text',
                inputPlaceholder: 'Enter your quantity.',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.value) {
                    if (/^[0-9()]*$/.test(result.value)) {
                        $.ajax({
                            url: "{{ url('accept-stock') }}",
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                medicine_id: medicine,
                                status: 2,
                                arogyamitra_id: user_id,
                                qty: result.value
                            },
                            dataType: "JSON",
                            success: function(response) {
                                if (response.status == 1) {
                                    Swal.fire({
                                        title: 'Success',
                                        text: response.message,
                                        icon: 'success'
                                    });
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                }
                            }
                        });
                    } else {
                        Swal.fire("Please enter only numeric value.", "", "error");
                    }
                } else if (result.dismiss == 'cancel') {} else {
                    Swal.fire("Please enter your quantity.", "", "error");
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {

            /* ===============================
               SELECT ALL CHECKBOX
            =============================== */
            $('#selectAll').on('change', function() {
                $('.rowCheckbox').prop('checked', $(this).prop('checked'));
                toggleBulkButton();
            });

            /* ===============================
               SINGLE CHECKBOX CHANGE
            =============================== */
            $(document).on('change', '.rowCheckbox', function() {
                toggleBulkButton();

                // auto uncheck selectAll if any unchecked
                if (!$(this).prop('checked')) {
                    $('#selectAll').prop('checked', false);
                }
            });

            /* ===============================
               SHOW / HIDE BULK BUTTON
            =============================== */
            function toggleBulkButton() {
                let checkedCount = $('.rowCheckbox:checked').length;

                if (checkedCount > 0) {
                    $('#bulkAcceptBtn').removeClass('d-none');
                } else {
                    $('#bulkAcceptBtn').addClass('d-none');
                }
            }


            // $('#bulkAcceptBtn').on('click', function() {

            //     let requests = [];

            //     $('.rowCheckbox:checked').each(function() {
            //         requests.push({
            //             medicine_id: $(this).data('medicine'),
            //             arogyamitra_id: $(this).data('user')
            //         });
            //     });

            //     if (requests.length === 0) {
            //         alert('Please select at least one request');
            //         return;
            //     }

            //     if (!confirm('Are you sure you want to accept selected requests?')) {
            //         return;
            //     }

            //     $.ajax({
            //         url: "{{ route('medicineRequest.delivered_flag_update') }}",
            //         type: "POST",
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             requests: requests
            //         },
            //         success: function(response) {
            //             alert(response.message);
            //             location.reload();
            //         },
            //         error: function() {
            //             alert('Something went wrong. Please try again.');
            //         }
            //     });
            // });

            $('#bulkAcceptBtn').on('click', function() {

                let requests = [];

                $('.deliveredQty').each(function() {

                    let qty = $(this).val();
                    let requestId = $(this).data('id');

                    if (qty !== '' && qty > 0) {
                        requests.push({
                            medicine_request_id: requestId,
                            delivered_quantity: qty
                        });
                    }
                });

                /* ===============================
                   NO QTY ENTERED
                =============================== */
                if (requests.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Quantity Required',
                        text: 'Please enter quantity in at least one row',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                /* ===============================
                   CONFIRMATION
                =============================== */
                Swal.fire({
                    title: 'Confirm Delivery',
                    text: 'Are you sure you want to deliver selected medicines?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Deliver',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#28a745'
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    /* ===============================
                       AJAX CALL
                    =============================== */
                    $.ajax({
                        url: "{{ route('medicineRequest.deliver') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            requests: requests
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message ??
                                    'Medicine delivered successfully'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message ??
                                    'Something went wrong'
                            });
                        }
                    });

                });
            });



        });
    </script>
@endsection
