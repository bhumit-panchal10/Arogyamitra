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
                <input type="text" id="prantSearchInput" class="form-control form-control-solid w-250px ps-14" placeholder="Search" />
            </div>
        </div>
        <div class="card-toolbar">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bolder fs-3 mb-1">Active Log</span>
            </h3>
        </div>
    </div>
    <div class="card-body pt-0 table-responsive">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
            <thead>
                <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                    <th class="text-center">S.NO</th>
                    <th class="text-center">URL</th>
                    <th class="text-center">Method</th>
                    <th class="text-center">IP_Address</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Browser</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Date</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @forelse($activity as $active)
                <tr>
                    <!-- <td class="text-center"></td> -->
                    <td class="text-center">{{$loop->iteration}}</td>
                    <td class="text-center">{{ $active->request_url }}</td>
                    <td class="text-center">
                        @if($active->method == 'GET')
                        <div class="badge badge-light-success fw-bolder">{{ $active->method }}</div>
                        @elseif($active->method == 'POST')
                        <div class="badge badge-light-primary fw-bolder">{{ $active->method }}</div>
                        @else
                        <div class="badge badge-light-danger fw-bolder">{{ $active->method }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $active->ip_address }}</td>
                    <td class="text-center">{{ $active->request_para }}</td>
                    <td class="text-center">{{ $active->user_agent }}</td>
                    <td class="text-center">
                        @php
                        $userTypes = [
                        1 => 'Backend',
                        2 => 'App User',
                        3 => 'Arogyamitra',
                        4 => 'Vibhag',
                        5 => 'Prant',
                        ];
                        @endphp
                        {{ $userTypes[$active->user_id] ?? 'Unknown' }}
                    </td>
                    <td class="text-center">{{ $active->created_at }}</td>
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
        $('#kt_customers_table').DataTable({
            paginate: true,
            searching: true,
            pageLength: 10,
            order: [],
            columnDefs: [{
                targets: [0],
                orderable: false,
                //targets: 'no-search',
                searchable: false,
            }],
        });

        $('#prantSearchInput').on('keyup', function() {
            $('#kt_customers_table').DataTable().search($(this).val()).draw();
        });
    });
</script>
@endsection