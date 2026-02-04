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

        <div class="card-body pt-0 table-responsive">
            <div class="medicine_list d-none">Stockiest Report</div>
            <form method="GET" action="{{ route('report.stockiestReport') }}" class="mb-5">
                <div class="row align-items-end g-3">

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Stockiest</label>
                        <select name="stockiest_id" class="form-control">
                            <option value="">All Stockiest</option>
                            @foreach ($stockiests as $stockiest)
                                <option value="{{ $stockiest->id }}"
                                    {{ request('stockiest_id') == $stockiest->id ? 'selected' : '' }}>
                                    {{ $stockiest->name }} ({{ $stockiest->mobile_no }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Search
                        </button>
                        <a href="{{ route('report.stockiestReport') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>

            </form>

            @if ($dispatchData->isNotEmpty())
                <a href="{{ route('report.stockiest.export', request()->query()) }}" class="btn btn-success">
                    Export Excel
                </a>
            @endif

            <table id="medicineTable" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                        <th class="text-center">{{ trans('messages.medicine.fields.serial_no') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.medicine') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.quantity') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.prant') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.vibhag') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.jilla') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.stockiest_user') }}</th>
                        <th class="text-center">{{ trans('messages.medicine.fields.stockiest_mobile') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @if ($dispatchData->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                Please search to view stockiest report
                            </td>
                        </tr>
                    @else
                        @foreach ($medicines as $index => $medicine)
                            @php
                                $row = $dispatchData->firstWhere('medicine_id', $medicine['medicine_id']);
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $medicine['name'] }}</td>
                                <td class="text-center">{{ $row->total_dispatch ?? '-' }}</td>
                                <td class="text-center">{{ $row->prant_name ?? '-' }}</td>
                                <td class="text-center">{{ $row->vibhag_name ?? '-' }}</td>
                                <td class="text-center">{{ $row->jilla_name ?? '-' }}</td>
                                <td class="text-center">{{ $row->stockiest_name ?? '-' }}</td>
                                <td class="text-center">{{ $row->mobile_no ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>



            </table>
        </div>
    </div>
@endsection

@section('javascript')
@endsection
