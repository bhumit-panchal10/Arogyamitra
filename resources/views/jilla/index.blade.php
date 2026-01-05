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
                <input type="text" id="searchInput" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search" />
            </div>
        </div>
        <div class="card-toolbar">
            <span class="me-2 d-none showCount fw-bolder me-3"></span>
            <button type="button" class="btn btn-info me-2 d-none" onclick="updateStatusChange()">Change status</button>
            <button type="button" class="btn btn-danger me-2 d-none" onclick="multiDelete()">Delete Selected</button>
        </div>
    </div>

    <div class="card-body pt-0 table-responsive">
        <table id="kt_customers_table" class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                    <th class="text-center">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3 dNone">
                            <input class="form-check-input" type="checkbox" data-kt-check="true" id="selectAllCheckbox" data-kt-check-target="#kt_customers_table .form-check-input" value="" />
                        </div>
                    </th>
                    <th class="text-center">{{ trans('messages.jilla.fields.serial_no') }}</th>
                    <th class="text-center">{{ trans('messages.jilla.fields.name') }}</th>
                    <th class="text-center">{{ trans('messages.jilla.fields.vibhag') }}</th>
                    <th class="text-center">{{ trans('messages.jilla.fields.action') }}</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @forelse ($district as $districts)
                <tr>
                    <td class="text-center">
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input name="id[]" class="form-check-input checkbox selectAllCheckbox" type="checkbox" value="{{ $districts->id }}" />
                        </div>
                    </td>
                    <td class="text-center">{{$loop->iteration}}</td>
                    <td class="text-center">{{ $districts->name }}</td>
                    <td class="text-center">{{ $districts->vibhag->name  }}</td>
                    <td class="text-center">
                        <span class="btn btn-info" style="width: 60px;" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark"    data-bs-placement="top" title="{{ $districts->concat_values }}"><i class="fa fa-info"></i></span>
                        <a href="javascript:void(0);" data-toggle="tooltip" data-bs-custom-class="tooltip-dark" {{ $districts->status == '1' ? 'title= Active' : 'title=Inactive' }} onclick="changeJillaStatus('{{$districts->status}}','{{$districts->id}}')" class="btn btn-xs btn-success">
                            @if($districts->status == '1')
                            <i class="fas fa-regular fa-lock-open fa-5x" aria-hidden="true"></i>
                            @else
                            <i class="fas fa-regular fa-lock fa-5x" aria-hidden="true"></i>
                            @endif
                        </a>
                        <a href="{{ route('jilla.show', [$districts->id]) }}" data-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Show" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>
                        <a href="{{ route('jilla.edit', [$districts->id]) }}" data-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Edit" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                        {!! Form::open(array(
                        'style' => 'display: inline-block;',
                        'method' => 'DELETE',
                        'onsubmit' => "return confirm('Are you sure you want to delete this jilla?');",
                        'route' => ['jilla.destroy', $districts->id])) !!}
                        {!! Form::button('<i class="fa fa-trash"></i>', array('type'=>'submit', 'data-bs-custom-class'=>"tooltip-dark", 'data-toggle'=> 'tooltip','title'=>'Delete','class' => 'btn btn-xs btn-danger')) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" style="text-align: center;">No record found.</td>
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
            if (selectedCount > 0) {
                $('.showCount').text(selectedCount + " Selected");
                $('.showCount').removeClass('d-none');
                $('#selectAllCheckbox').prop("checked", false);
            } else {
                $('.showCount').addClass('d-none');
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
                targets: [0],
                orderable: false,
                //targets: 'no-search',
                searchable: false,
            }],
        });

        // Apply search to DataTables
        $('#searchInput').on('keyup', function() {
            $('#kt_customers_table').DataTable().search($(this).val()).draw();
        });
    });

    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    function changeJillaStatus(status, id) {
        if (confirm('Are you sure you want to change your status')) {
            $.ajax({
                url: "{{ url('change-jilla-status') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    jilla_id: id,
                    jilla_status: status,
                },
                dataType: "JSON",
                success: function(response) {
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            });
        }
    }

    function updateStatusChange() {
        var selectedIds = [];
        $(':checkbox:checked').each(function(i) {
            selectedIds.push($(this).val());
        });

        Swal.fire({
            title: 'Confirmation',
            text: "Are you sure you want to change the status of jilla request?",
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
                    url: "{{ url('change-jilla-status') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        ids: selectedIds,
                        status: 1,
                        type: "Multiple"
                    },
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status == 1) {
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
                                text: 'Something went wrong!',
                                icon: 'error'
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    }
                });
            } else if (result.isDenied) {
                $.ajax({
                    url: "{{ url('change-jilla-status') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        ids: selectedIds,
                        status: 0,
                        type: "Multiple"
                    },
                    dataType: "JSON",
                    success: function(response) {
                        console.log(response.status)
                        if (response.status == 0) {
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
                                text: 'Something went wrong!',
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

    function multiDelete() {
        var selectedIds = [];
        $(':checkbox:checked').each(function(i) {
            selectedIds.push($(this).val());
        });

        Swal.fire({
            text: "Are you sure you want to delete selected jilla?",
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
                    url: "{{ url('delete-jilla') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        ids: selectedIds,
                        status: 1,
                        type: "Multiple"
                    },
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status == 1) {
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