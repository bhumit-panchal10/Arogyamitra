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
            <div class="medicine_list d-none">Beneficiaries Report</div>
            <form method="GET" action="{{ route('report.beneficiariesReport') }}" class="mb-5">
                <div class="row align-items-end g-3">

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
                        <a href="{{ route('report.beneficiariesReport') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>

            </form>
            @if ($searched && $report->isNotEmpty())
                <a href="{{ route('report.beneficiaries.export', request()->query()) }}" class="btn btn-success">
                    Export Excel
                </a>
            @endif


            <table id="medicineTable" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                        <th class="text-center">#</th>
                        <th class="text-center">Prant</th>
                        <th class="text-center">Vibhag</th>
                        <th class="text-center">Jilla</th>
                        <th class="text-center">Taluka</th>
                        <th class="text-center">Gramjuth</th>
                        <th class="text-center">Gram</th>
                        <th class="text-center">Beneficiary</th>
                        <th class="text-center">Arogyamitra</th>
                        <th class="text-center">Arogyamitra Mobile</th>
                        <th class="text-center">App User</th>
                        <th class="text-center">App User Mobile</th>
                        <th class="text-center">Stockiest</th>
                        <th class="text-center">Stockiest Mobile</th>
                    </tr>
                </thead>

                <tbody>
                    @if (!$searched)
                        <tr>
                            <td colspan="14" class="text-center text-muted">
                                Please select From Date & To Date and click Search
                            </td>
                        </tr>
                    @elseif ($report->isEmpty())
                        <tr>
                            <td colspan="14" class="text-center text-danger">
                                No data found for selected date range
                            </td>
                        </tr>
                    @else
                        @foreach ($report as $index => $row)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $row->Prant }}</td>
                                <td class="text-center">{{ $row->Vibhag }}</td>
                                <td class="text-center">{{ $row->Jilla }}</td>
                                <td class="text-center">{{ $row->Taluka }}</td>
                                <td class="text-center">{{ $row->Gramjuth }}</td>
                                <td class="text-center">{{ $row->Gram }}</td>
                                <td class="text-center">{{ $row->total_beneficiary }}</td>
                                <td class="text-center">{{ $row->arogyamitraName }}</td>
                                <td class="text-center">{{ $row->mobile_no }}</td>
                                <td class="text-center">{{ $row->AppUser }}</td>
                                <td class="text-center">{{ $row->AppUserMobile }}</td>
                                <td class="text-center">{{ $row->StockiestUser }}</td>
                                <td class="text-center">{{ $row->StockiestMobile }}</td>
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
