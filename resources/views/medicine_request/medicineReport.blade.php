@extends('layouts.app')

@section('head')
    <link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <style>
        .card-title {
            font-weight: 600;
            font-size: 18px;
        }

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
    </style>

    <div class="card">
        @include('medicine_request.orderTab')

        {{-- Card Header --}}
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                {{ $title ?? 'Medicine Request Report' }}
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body pt-0 table-responsive">
            <table id="kt_customers_table" class="table table-bordered table-striped align-middle">
                <thead>
                    <tr class="fw-bolder text-uppercase text-center">
                        <th style="width: 80px;">SR NO</th>
                        <th>Medicine Name</th>
                        <th style="width: 200px;">Total Requested Quantity</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($medicineRequest as $medicine)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $medicine->medicine_name }}</td>
                            <td>{{ $medicine->total_request }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">
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
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 25,
                order: []
            });
        });
    </script>
@endsection
