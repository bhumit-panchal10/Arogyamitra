@extends('layouts.app')

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="card mb-5 mb-xl-8">
        <div class="py-2">
            <div class="card-body pt-0 pb-5">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed gy-5" id="kt_table_users_login_session">
                        <tbody class="fs-6 fw-bold text-gray-600">
                            <tr>
                                <td><b class="text-black">{{ trans('messages.medicine.fields.medicine') }}</b></td>
                                <td>{{ $medicine->name }}</td>
                            </tr>
                            <tr>
                                <td><b class="text-black">{{ trans('messages.medicine.fields.quantity') }}</b></td>
                                <td>{{ $medicine->qty }}</td>
                            </tr>
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
                            <tr>
                                <td><b class="text-black">{{ trans('messages.quantity_type')}}</b></td>
                                <td>{{ $qtyType }}</td>
                            </tr>
                            <tr>
                                <td><b class="text-black">{{ trans('messages.medicine.fields.status')}}</b></td>
                                <td>{{ $medicine->status == 1 ? 'Active' : 'Inactive' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer border-0 d-flex justify-content-center pt-0">
            <a class="btn btn-xs btn-primary" href="{{ route('medicines.index') }}">{{ trans('messages.medicine.fields.back') }}</a>
        </div>
    </div>
</div>
@endsection