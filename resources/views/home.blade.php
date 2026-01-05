@extends('layouts.app')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <!--begin::Row-->
    <div class="row gy-5 g-xl-8">
        <!--begin::Col-->
        <div class="col-xl-16">
            <!--begin::Tables Widget 9-->
            <div class="card card-xl-stretch mb-5 mb-xl-8">
                <!--begin::Header-->
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">{{ trans('messages.dashboard.title') }}</span>
                    </h3>

                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body pt-0 table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                                <th class="text-center">{{ trans('messages.dashboard.fields.serial_no') }}</th>
                                @if (Auth::user()->role == 1 || Auth::user()->role == 4)
                                <th class="text-center">{{ trans('messages.dashboard.fields.user_name') }}</th>
                                @elseif (Auth::user()->role == 5)
                                <th class="text-center">{{ trans('messages.medicine_request.user_name') }}</th>
                                @endif
                                @if (Auth::user()->role !== 4)
                                <th class="text-center">{{ trans('messages.dashboard.fields.vibhag') }}</th>
                                @endif
                                <th class="text-center">{{ trans('messages.dashboard.fields.jilla') }}</th>
                                <th class="text-center">{{ trans('messages.dashboard.fields.gram') }}</th>
                                <th class="text-center">{{ trans('messages.dashboard.fields.medicine') }}</th>
                                <th class="text-center">{{ trans('messages.dashboard.fields.request_quantity') }}</th>
                                <th class="text-center">{{ trans('messages.dashboard.fields.request_quantity_type') }}</th>
                                <th class="text-center">{{ trans('messages.dashboard.fields.request') }}</th>
                                <th class="text-center">{{ trans('messages.dashboard.fields.status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @forelse ($medicineRequest as $medicine)
                            <tr>

                                <td class="text-center">{{$loop->iteration}}</td>
                                <td class="text-center">{{ $medicine->arogyamitra_name}}</td>
                                @if (Auth::user()->role !== 4)
                                <td class="text-center">{{ $medicine->vibhag_name }}</td>
                                @endif
                                <td class="text-center">{{ $medicine->jilla_name }}</td>
                                <td class="text-center">{{ $medicine->gram_name }}</td>
                                <td class="text-center">{{ $medicine->medicine_name }}</td>
                                @if (Auth::user()->role == 5)
                                <td class="text-center">{{ $medicine->qty }}</td>
                                @elseif (Auth::user()->role == 4)
                                <td class="text-center">{{ $medicine->qty }}</td>
                                @else
                                <td class="text-center">{{ $medicine->qty }}</td>
                                @endif
                                @if($medicine->qty_type=='નંગ' || $medicine->qty_type=='ગ્રામ')
                                <td class="text-center">Packet</td>
                                @else
                                <td class="text-center">Bottle</td>
                                @endif
                                <td class="text-center">{{ $medicine->created_at ? $medicine->created_at->format('d-m-Y') : date('d-m-Y') }}</td>
                                <td class="text-center">
                                    @if($medicine->status == '2')
                                    <div class="badge badge-light-success fw-bolder">Accepted</div>
                                    @elseif($medicine->status == '0')
                                    <div class="badge badge-light-danger fw-bolder">Rejected</div>
                                    @else
                                    <div class="badge badge-light-primary fw-bolder">Pending</div>
                                    @endif
                                </td>
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
                    @if(count($medicineRequest)>0)
                    <a href="{{route('medicineRequest.index')}}" title="{{ trans('messages.dashboard.view_all') }}" data-bs-custom-class="tooltip-dark" data-toggle="tooltip" class="btn btn-primary">{{ trans('messages.dashboard.view_all') }}</a>
                    @endif
                </div>
                <!--begin::Body-->
            </div>
            <!--end::Tables Widget 9-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
</div>
@endsection
@section('javascript')
<script src="{{ url('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/user-management/users/list/table.js')}}"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTables with search input functionality
        $('#homeTable').DataTable({
            paginate: true,
            searching: true,
            pageLength: 25,
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
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
@endsection